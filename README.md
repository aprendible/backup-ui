# Backup UI for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/aprendible/backup-ui.svg?style=flat-square)](https://packagist.org/packages/aprendible/backup-ui)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/aprendible/backup-ui/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/aprendible/backup-ui/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/aprendible/backup-ui.svg?style=flat-square)](https://packagist.org/packages/aprendible/backup-ui)

A clean, self-contained admin UI on top of [`spatie/laravel-backup`](https://github.com/spatie/laravel-backup). List your backups per disk, run full / database-only / files-only backups, download or delete archives, clean up old backups, and review your backup configuration — all from the browser.

## Requirements

- PHP `^8.4`
- Laravel `^11.0`, `^12.0` or `^13.0`
- [`spatie/laravel-backup`](https://github.com/spatie/laravel-backup) `^10.3` (pulled in automatically as a dependency)

## Installation

Follow the steps below to get the UI running end to end.

### 1. Install the package with Composer

```bash
composer require aprendible/backup-ui
```

The service provider is registered automatically via package discovery — no manual `config/app.php` changes are required.

### 2. Run the interactive installer

```bash
php artisan backup-ui:install
```

The wizard will:

1. Publish the `spatie/laravel-backup` config (`config/backup.php`) and the package config (`config/backup-ui.php`).
2. Ask for your **application/backup name**, the **databases** to back up, and the **destination disks**.
3. Ask how many days to **keep all backups**, an optional **notification email**, and the **daily times** for the backup and cleanup schedules.
4. Patch `config/backup.php` with your answers and, if a chosen disk does not exist, offer to create it in `config/filesystems.php`.

> Prefer to configure things by hand? Skip the wizard and publish the config on its own:
>
> ```bash
> php artisan vendor:publish --tag="backup-ui-config"
> ```

This is the contents of the published `config/backup-ui.php` file:

```php
return [

    // The Gate used to authorize access to the UI. Defaults to "backup-access".
    'gate' => null,

    // Whether the package registers the daily backup / cleanup scheduled tasks.
    'schedule' => [
        'backup' => true,
        'clean' => true,
    ],

    // Where the UI is mounted and which middleware guards it.
    'routes' => [
        'prefix' => 'backup',
        'middleware' => ['web'],
    ],

];
```

### 3. Define the authorization gate

The UI is protected by a [Gate](https://laravel.com/docs/authorization#gates) named `backup-access` (see [Authorization](#authorization) below). **Until you define it, every request returns `403`.** Add it to your `AppServiceProvider::boot()`:

```php
use Illuminate\Support\Facades\Gate;

Gate::define('backup-access', fn ($user) => in_array($user->email, [
    'admin@example.com',
]));
```

### 4. Make sure the scheduler runs

The package registers a daily `backup:run` and `backup:clean`. For those to fire, your app's scheduler must be running. In local development:

```bash
php artisan schedule:work
```

In production, add the single Laravel cron entry (`* * * * * php artisan schedule:run`) if you have not already.

### 5. Visit the UI

Log in as a user the gate allows and open **`/backup`** (or your configured prefix). You're done — the stylesheet is compiled and self-hosted, so there is no front-end build step in your application.

### (Optional) Customize the views

```bash
php artisan vendor:publish --tag="backup-ui-views"
```

## Authorization

The UI is protected by a [Gate](https://laravel.com/docs/authorization#gates). By default it looks for a gate named `backup-access` (override the name via the `gate` config key). Define it in your `AppServiceProvider`:

```php
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::define('backup-access', function ($user) {
        return in_array($user->email, [
            'admin@example.com',
        ]);
    });
}
```

If no matching gate is defined, the UI returns `403` for every request.

## Usage

Once installed, visit `/backup` (or your configured prefix) while authenticated as a user the gate allows. From there you can:

- View every backup grouped by destination disk, with reachability and used storage.
- Run a full, database-only, or files-only backup.
- Download or delete individual backups.
- Clean up old backups using your configured strategy.
- Review the resolved backup, schedule, cleanup and notification settings.

### Scheduling

When `schedule.backup` is enabled the package registers a daily `backup:run` at `01:00`, and when `schedule.clean` is enabled a daily `backup:clean` at `00:30`. Make sure your application runs the Laravel scheduler:

```bash
php artisan schedule:work
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Jorge García](https://github.com/aprendible)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
