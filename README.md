
# Laravel Complaint & Note Manager

[![Packagist Version](https://img.shields.io/packagist/v/alyakin/reporting)](https://packagist.org/packages/alyakin/reporting)
[![Downloads](https://img.shields.io/packagist/dt/alyakin/reporting)](https://packagist.org/packages/alyakin/reporting)
![Laravel 9+](https://img.shields.io/badge/Laravel-10%2B-orange)
![PHP 8+](https://img.shields.io/badge/PHP-8%2B-blue)
[![MIT License](https://img.shields.io/badge/license-MIT-green)](LICENCE)

[![PHPUnit](https://github.com/2177866/reporting/actions/workflows/phpunit.yml/badge.svg)](https://github.com/2177866/reporting/actions/workflows/phpunit.yml)
[![Laravel Pint](https://github.com/2177866/reporting/actions/workflows/pint.yml/badge.svg)](https://github.com/2177866/reporting/actions/workflows/pint.yml)
[![Larastan](https://github.com/2177866/reporting/actions/workflows/larastan.yml/badge.svg)](https://github.com/2177866/reporting/actions/workflows/larastan.yml)
![Larastan Level](https://img.shields.io/badge/Larastan-level%209-blueviolet)

**Laravel Complaint & Note Manager** is a package for managing complaints, notes, and similar entities in Laravel projects. It uses polymorphic relationships for integration with any models.

Ideal for social networks, e-commerce, and content moderation projects that require collecting and managing user complaints, reports, or note ([use cases](#use-cases)).

### Features:
- **Polymorphic Relationships**: Easy integration with different models.
- **Metadata**: Ability to save additional data.
- **Automatic Deletion**: Manage outdated records.
- **Customization**: Configure through configuration files and migrations.

## Table of Contents

1. [Laravel Complaint & Note Manager](#laravel-complaint--note-manager)
2. [Installation](#installation)
   - [Requirements ](#requirements)
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
6. [Use cases](#use-cases)
7. [Testing](#testing)
8. [Want to Contribute?](#want-to-contribute)
9. [License](#license)


## Installation

### Requirements

- PHP ^8.0
- Laravel ^9.0, ^10.0, ^11.0

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

## Use Cases

Here are five different examples of how this package can be applied across various domains:

1. **Social Networks:** Users can report posts or comments that violate guidelines...
2. **E-commerce Platforms:** Customers can flag products or sellers...
3. **Content Management Systems (CMS):** Readers can report offensive or incorrect content...
4. **Customer Support Systems:** Users can submit complaints linked to their accounts or tickets...
5. **Educational Platforms:** Students can report problems with course materials or instructors...

## Testing

This package includes a test suite to ensure functionality works as expected. To run the tests:

```bash
composer test
```

### PHPUnit

The package uses PHPUnit for feature and unit tests. You can run PHPUnit tests specifically with:

```bash
./vendor/bin/phpunit
```

### Static Analysis

We use Larastan (PHPStan for Laravel) for static code analysis (with level 9):

```bash
./vendor/bin/phpstan analyse
```

### Code Style

Laravel Pint is used for code style enforcement:

```bash
./vendor/bin/pint
```

## Want to Contribute?

This package is open for community contributions!

You can:
- Explore the [open issues](https://github.com/2177866/reporting/issues) to see what's planned
- Pick a task labeled [`good first issue`](https://github.com/2177866/reporting/issues?q=is%3Aissue+is%3Aopen+label%3A%22good+first+issue%22) or [`help wanted`](https://github.com/2177866/reporting/issues?q=is%3Aissue+is%3Aopen+label%3A%22help+wanted%22)
- Suggest a new feature or improvement by opening an issue
- Fork the repository and submit a Pull Request

### Contribution Requirements

When contributing to this package, please ensure:

1. **Code Style**: All code must follow our style guidelines. Run Laravel Pint before submitting:
   ```bash
   ./vendor/bin/pint
   ```

2. **Static Analysis**: Code must pass Larastan level 9 analysis:
   ```bash
   ./vendor/bin/phpstan analyse
   ```

3. **Test Coverage**: All new features or bug fixes must include tests.

4. **Documentation**: Update the README.md and other documentation to reflect any changes in functionality.

5. **Feature Branches**: Create a feature branch for your changes and submit a pull request against the main branch.

### Current Roadmap Highlights

- Add support for Laravel-style events (e.g. `ReportCreated`, `ReportDeleted`)
- Artisan command to purge old reports (`reporting:purge`)

We welcome contributions, feedback, and ideas! 😊

## License

This package is distributed under the [MIT License](https://opensource.org/licenses/MIT).
