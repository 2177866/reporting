# Использование для проекта, где применяется автоинкрементные ключи

По умолчанию в качестве ключей  используются UUID. Для работы с автоинкрементными ключами потребуется внести правки описанные ниже.

## Содержание
[< Вернуться к описанию пакета](../README_RU.md#шаг-2-публикация-конфигурации-и-миграций)

1. [Настройка](#настройка)
   - [Подготовка базы данных](#подготовка-базы-данных)
   - [Подготовка модели](#подготовка-модели)
   - [Регистрация кастомной модели](#регистрация-кастомной-модели)

## Настройка
### Подготовка базы данных
После установки пакета измените файл миграции `database/migrations/2024_11_30_000000_create_reports_table.php`, для использования автоинкремента.

Вместо
```php
$table->uuid('id')->primary();
$table->uuidMorphs('reportable');
$table->foreignUuid('user_id')->nullable()->constrained()->cascadeOnDelete();
```
используйте
```php
$table->id('id');
$table->morphs('reportable');
$table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
```

У вас должно получиться приблизительно так:
```php
Schema::create('reports', function (Blueprint $table) {

    $table->id('id');
    $table->morphs('reportable');
    $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();

    $table->string('reason');
    $table->json('meta')->nullable();
    // ... ваши дополнительные поля

    $table->softDeletes();
    $table->timestamps();
});
```

После внесения изменений выполните команду `php artisan migrate`

## Подготовка модели

Создайте кастомную модель для жалоб, она должна наследоваться от Alyakin\Reporting\Models\Report.
И принудительно используйте автоинкремент.

Пример кастомной модели:
```php
namespace App\Models;

class CustomReport extends Alyakin\Reporting\Models\Report
{
    protected $fillable = [
        'reason',
        'meta',
        'user_id',
        // ... ваши дополнительные поля
    ];

    protected $keyType = 'int';
    public $incrementing = true;

    protected function initializeHasUuids(): void {}
}
```

## Регистрация кастомной модели
Чтобы ваша кастомная модель использовалась, требуется указать ее в файле конфигурации пакета `config/reporting.php`.

```php
return [
    'user_model' => App\Models\User::class,

    // Модель наследованная от Alyakin\Reporting\Models\Report
    'report_model' => App\Models\CustomReport::class,

    'soft_delete_days' => 30,
];
```
