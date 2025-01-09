# Laravel Complaint & Note Manager

[![Packagist Version](https://img.shields.io/packagist/v/alyakin/reporting)](https://packagist.org/packages/alyakin/reporting)
[![Downloads](https://img.shields.io/packagist/dt/alyakin/reporting)](https://packagist.org/packages/alyakin/reporting)
![Laravel 10+](https://img.shields.io/badge/Laravel-10%2B-orange)
![PHP 8+](https://img.shields.io/badge/PHP-8%2B-blue)
[![MIT License](https://img.shields.io/badge/license-MIT-green)](https://opensource.org/licenses/MIT)

**Laravel Complaint & Note Manager** — пакет для управления репортами (жалобами, заметками и т.д.) в проектах на Laravel. Модуль использует полиморфные отношения, позволяя легко добавлять функциональность для любых моделей.

Отлично подходит для проектов, где требуется модерация контента или сбор жалоб от пользователей.

### Преимущества:
- **Полиморфные отношения**: Быстрая интеграция с разными моделями.
- **Метаданные**: Возможность сохранять дополнительные данные.
- **Автоматическое удаление**: Управление сроками хранения устаревших записей.
- **Кастомизация**: Настройки через конфигурационные файлы, наследование и модификация миграций.

---

## Оглавление

1. [Laravel Complaint & Note Manager](#laravel-complaint--note-manager)
2. [Установка](#установка)
   - [Шаг 1: Установка через Composer](#шаг-1-установка-через-composer)
   - [Шаг 2: Публикация конфигурации и миграций](#шаг-2-публикация-конфигурации-и-миграций)
3. [Конфигурация](#конфигурация)
4. [Использование](#использование)
   - [Добавление трейт Reportable](#шаг-1-добавление-трейт-reportable)
   - [Создание репорта](#шаг-2-создание-репорта)
   - [Получение репортов](#шаг-3-получение-репортов)
   - [Удаление репорта](#дополнительный-пример-удаление-репорта)
5. [Автоматическое удаление старых репортов](#автоматическое-удаление-старых-репортов)
   - [Настройка планировщика](#настройка-планировщика)
   - [Ручное удаление](#ручное-удаление)
6. [Кастомизация модели репортов](#кастомизация-модели-репортов)
7. [Лицензия](#лицензия)

---

## Установка

### Шаг 1: Установка через Composer

```bash
composer require alyakin/reporting
```

### Шаг 2: Публикация конфигурации и миграций
```bash
php artisan vendor:publish --provider="Alyakin\Reporting\ReportingServiceProvider"
```
После запуска команды у вас в проекте появится файл конфигурации и миграция БД.

Перед запуском миграций убедитесь, что настройки базы данных верны (особое внимние уделите типам ключей, которые вы используете в проекте, по умолчанию используются UUID, для использования автоинкремента [читайте инструкцую](examples/usingAutoincrement.md)).

После внесения изменений выполните `php artisan migrate`

---

## Конфигурация

После установки модуля файл конфигурации доступен в `config/reporting.php`. Если файла нет, выполните команду:

```bash
php artisan vendor:publish --provider="Alyakin\Reporting\ReportingServiceProvider" --tag=config
```

В конфигурации используются следующие ключи:
| Ключ | Назначение |  значение по умолчанию  |
|----------|----------|----------|
|report_model|Модель используемая в качестве Репортов(жалоб)| `\Alyakin\Reporting\Models\Report::class`
|soft_delete_days|Срок хранения удаленных записей в БД|`30`
|user_model|Модель для ассоциирования с пользователем|`App\Models\User::class`


Пример содержимого файла config/reporting.php:

```php
return [
    'user_model' => App\Models\User::class,
    'report_model' => App\Models\Report::class,
    'soft_delete_days' => 90,
];
```



---

## Использование

### Добавление трейт `Reportable`

Добавьте трейт `Reportable` к любой модели, которая должна поддерживать репорты:

```php
use Alyakin\Reporting\Traits\Reportable;

class Post extends Model
{
    use Reportable;

    // Логика вашей модели
}
```

### Создание репорта

Используйте связь `reports()` для создания репорта:

```php
$post = Post::find(1);

$report = $post->reports()->create([
    'reason' => 'Спам',
    'meta' => ['severity' => 'низкий'],
]);
```

### Получение репортов

Для получения всех репортов модели:

```php
$reports = $post->reports;
```

Для получения связанной модели из репорта:

```php
$post = $report->reportable;
```

### Удаление репорта

Вы можете удалить репорт через стандартные методы Eloquent:

```php
$report->delete();
```

Репорт будет удален мягко (soft delete), если в модели включен `SoftDeletes`.

---

## Автоматическое удаление старых репортов

Модуль автоматически удаляет старые репорты на основе значения `soft_delete_days` в файле конфигурации. По умолчанию репорты удаляются через 30 дней после мягкого удаления.

### Настройка планировщика

Для автоматической очистки старых репортов убедитесь, что команда планировщика (`scheduler`) включена. Добавьте следующую строку в метод `schedule` в файле `app/Console/Kernel.php`:

```php
$schedule->command('model:prune')->daily();
```

### Ручное удаление

Вы можете вручную запустить процесс очистки репортов с помощью команды:

```bash
php artisan model:prune
```



## Кастомизация модели репортов

Если вам нужно расширить стандартную модель Report, создайте модель в вашем проекте она должна наследоваться от Alyakin\Reporting\Models\Report.

```php
namespace App\Models;

use Alyakin\Reporting\Models\Report;

class CustomReport extends Report
{
    // Добавьте свои кастомные методы или поля
    protected $fillable = [
        'reason',
        'meta',
        'user_id',
        // ... ваши дополнительные поля
        'resolved_at',
        'conclusion',
    ];
    // Пример кастомной логики очистки мусора
    protected function pruning()
    {
        return static::where('meta->resolved', '<', now()->subDays(config('reporting.soft_delete_days')));
    }
}
```
После добавления кастомной модели обновите значение `report_model` в файле конфигурации `config/reporting.php`:
```php
    'report_model' => App\Models\CustomReport::class,
```

Если вам нужно хранить дополнительные поля "жалоб", например такие как статус рассмотрения, комментарий пользователя и т.п. вы можете использовать meta, либо добавить свои поля в таблицу через определение миграции и указания полей в массиве `$fillable` модели.

Как определить куда добавить ваше поле? Если вы планируете осуществлять поиск, либо сортировку по этому полю, то следует его добавить как отдельное поле. Если же это требуется просто хранить (не будет использоваться для сортировки или поиска), то смело добавляйте в `meta`.

После модификации модели вы можете использовать метод addReport, который добавляется в модель при помощи трейта `Reportable` он автоматически определяет текущего пользователя и указывает его в репорте.
```php
$post->addReport([
    'reason' => $request->input('reason'),
    'meta' => [
        'comment' => $request->input('comment'),
        'priority' => $post->reports()->count() <= 1 ? 'low' : 'medium'
    ],
    'resolved_at' => null,
    'conclusion' => 'Review pending',
])
```
## Лицензия

Этот пакет распространяется под лицензией [MIT](https://opensource.org/licenses/MIT). Вы можете использовать его, модифицировать и распространять в рамках условий данной лицензии.
