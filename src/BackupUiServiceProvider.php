<?php

namespace Aprendible\BackupUi;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Aprendible\BackupUi\Commands\BackupUiCommand;

class BackupUiServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('backup-ui')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_backup_ui_table')
            ->hasCommand(BackupUiCommand::class);
    }
}
