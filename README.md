
# Laravel Complaint & Note Manager

[![Packagist Version](https://img.shields.io/packagist/v/2177866/laravel-reporting)](https://packagist.org/packages/2177866/laravel-reporting)
[![Downloads](https://img.shields.io/packagist/dt/2177866/laravel-reporting)](https://packagist.org/packages/2177866/laravel-reporting)
![Laravel 10+](https://img.shields.io/badge/Laravel-10%2B-orange)
![PHP 8+](https://img.shields.io/badge/PHP-8%2B-blue)
[![MIT License](https://img.shields.io/badge/license-MIT-green)](https://opensource.org/licenses/MIT)

**Laravel Complaint & Note Manager** is a package for managing complaints, notes, and similar entities in Laravel projects. It uses polymorphic relationships for integration with any models.

Ideal for projects requiring content moderation or collecting user complaints.

### Features:
- **Polymorphic Relationships**: Easy integration with different models.
- **Metadata**: Ability to save additional data.
- **Automatic Deletion**: Manage outdated records.
- **Customization**: Configure through configuration files and migrations.

---

## Table of Contents

1. [Laravel Complaint & Note Manager](#laravel-complaint--note-manager)
2. [Installation](#installation)
   - [Step 1: Install via Composer](#step-1-install-via-composer)
   - [Step 2: Publish Configuration and Migrations](#step-2-publish-configuration-and-migrations)
3. [Configuration](#configuration)
   - [Basic Settings](#basic-settings)
   - [Customizing the Complaint Model](#customizing-the-complaint-model)
4. [Usage](#usage)
   - [Adding the Reportable Trait](#adding-the-reportable-trait)
   - [Creating a Complaint](#creating-a-complaint)
   - [Retrieving Complaints](#retrieving-complaints)
   - [Deleting a Complaint](#deleting-a-complaint)
5. [Automatic Deletion of Old Complaints](#automatic-deletion-of-old-complaints)
   - [Scheduler Configuration](#scheduler-configuration)
   - [Manual Deletion](#manual-deletion)
6. [License](#license)

---

## Installation

### Step 1: Install via Composer

```bash
composer require 2177866/laravel-reporting
```

### Step 2: Publish Configuration and Migrations
```bash
php artisan vendor:publish --provider="Alyakin\Reporting\ReportingServiceProvider"
php artisan migrate
```

Ensure that your database settings are correct before running migrations.

---

## Configuration

After installing the module, the configuration file is available at `config/reporting.php`. If the file is missing, run the following command:

```bash
php artisan vendor:publish --provider="Alyakin\Reporting\ReportingServiceProvider" --tag=config
```

### Basic Settings

Example configuration file content:

```php
return [
    'report_model' => \Alyakin\Reporting\Models\Report::class,
    'soft_delete_days' => 30,
];
```

### Customizing the Complaint Model

If you need to extend the default model, update the `report_model` parameter in the configuration:

```php
'report_model' => \App\Models\CustomReport::class,
```

The model must extend `Alyakin\Reporting\Models\Report`:

```php
namespace App\Models;

use Alyakin\Reporting\Models\Report;

class CustomReport extends Report
{
    // Your additional methods or fields
}
```

---

## Usage

### Adding the `Reportable` Trait

Add the `Reportable` trait to any model that should support complaints:

```php
use Alyakin\Reporting\Traits\Reportable;

class Post extends Model
{
    use Reportable;
}
```

### Creating a Complaint

Use the `reports()` relationship to create a complaint:

```php
$post = Post::find(1);

$report = $post->reports()->create([
    'reason' => 'Spam',
    'meta' => ['severity' => 'low'],
]);
```

### Retrieving Complaints

Retrieve all complaints for a model:

```php
$reports = $post->reports;
```

Retrieve the related model from a complaint:

```php
$post = $report->reportable;
```

### Deleting a Complaint

Delete a complaint using standard Eloquent methods:

```php
$report->delete();
```

---

## Automatic Deletion of Old Complaints

Old complaints are deleted based on the `soft_delete_days` parameter in the configuration (default is 30 days).

### Scheduler Configuration

Add the following line to `app/Console/Kernel.php`:

```php
$schedule->command('model:prune')->daily();
```

### Manual Deletion

Run the cleanup process manually:

```bash
php artisan model:prune
```

---

## License

This package is distributed under the [MIT License](https://opensource.org/licenses/MIT).
