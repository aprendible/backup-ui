# Backup UI for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/aprendible/backup-ui.svg?style=flat-square)](https://packagist.org/packages/aprendible/backup-ui)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/aprendible/backup-ui/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/aprendible/backup-ui/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/aprendible/backup-ui.svg?style=flat-square)](https://packagist.org/packages/aprendible/backup-ui)

A clean, self-contained admin UI on top of [`spatie/laravel-backup`](https://github.com/spatie/laravel-backup). List your backups per disk, run full / database-only / files-only backups, download or delete archives, clean up old backups, and review your backup configuration — all from the browser.

## Installation

Install the package via Composer:

```bash
composer require aprendible/backup-ui
```

Run the interactive installer. It publishes the config files, walks you through the backup name, databases, destination disks, retention and schedule, and patches `config/backup.php` for you:

```bash
php artisan backup-ui:install
```

Optionally, publish the config file on its own:

```bash
php artisan vendor:publish --tag="backup-ui-config"
```

This is the contents of the published config file:

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

Optionally, publish the views to customize them:

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
