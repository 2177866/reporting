
# Laravel Complaint & Note Manager

[![Packagist Version](https://img.shields.io/packagist/v/alyakin/reporting)](https://packagist.org/packages/alyakin/reporting)
[![Downloads](https://img.shields.io/packagist/dt/alyakin/reporting)](https://packagist.org/packages/alyakin/reporting)
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
6. [Want to Contribute?](#-want-to-contribute)
7. [License](#license)


## Installation

### Step 1: Install via Composer

```bash
composer require alyakin/reporting
```

### Step 2: Publish Configuration and Migrations
```bash
php artisan vendor:publish --provider="Alyakin\Reporting\ReportingServiceProvider"
php artisan migrate
```

Ensure that your database settings are correct before running migrations.


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
$user = $request->user();

$report = $post->addReport([
    'reason' => 'Спам',
    'meta' => ['severity' => 'низкий'],
], $user->id);
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

## 🙌 Want to Contribute?

This package is open for community contributions!

You can:
- Explore the [open issues](https://github.com/2177866/reporting/issues) to see what's planned
- Pick a task labeled [`good first issue`](https://github.com/2177866/reporting/issues?q=is%3Aissue+is%3Aopen+label%3A%22good+first+issue%22) or [`help wanted`](https://github.com/2177866/reporting/issues?q=is%3Aissue+is%3Aopen+label%3A%22help+wanted%22)
- Suggest a new feature or improvement by opening an issue
- Fork the repository and submit a Pull Request

### 🗺️ Current Roadmap Highlights

- Add support for Laravel-style events (e.g. `ReportCreated`, `ReportDeleted`)
- Artisan command to purge old reports (`reporting:purge`)

We welcome contributions, feedback, and ideas! 😊

## License

This package is distributed under the [MIT License](https://opensource.org/licenses/MIT).
