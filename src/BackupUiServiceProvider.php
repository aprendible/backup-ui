<?php

namespace Aprendible\BackupUi;

use Aprendible\BackupUi\Commands\BackupUiCommand;
use Aprendible\BackupUi\Http\Controllers\BackupController;
use Aprendible\BackupUi\Http\Controllers\CleanBackupsController;
use Aprendible\BackupUi\Http\Controllers\DownloadBackupController;
use Aprendible\BackupUi\Http\Controllers\RunBackupController;
use Aprendible\BackupUi\Http\Controllers\SettingsController;
use Aprendible\BackupUi\Http\Middleware\AuthorizeBackupAccess;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BackupUiServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('backup-ui')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_backup_ui_table')
            ->hasCommand(BackupUiCommand::class);
    }

    public function bootingPackage(): void
    {
        $this->registerRoutes();

        $this->registerSchedule();
    }

    protected function registerRoutes(): void
    {
        $prefix = config('backup-ui.routes.prefix', 'backup');
        $middleware = config('backup-ui.routes.middleware', ['web']);

        Route::middleware([...$middleware, AuthorizeBackupAccess::class])
            ->prefix($prefix)
            ->name('backup.')
            ->group(function (): void {
                Route::get('/', [BackupController::class, 'index'])->name('index');
                Route::get('/settings', SettingsController::class)->name('settings');
                Route::post('/run', RunBackupController::class)->name('run');
                Route::post('/clean', CleanBackupsController::class)->name('clean');
                Route::get('/{filename}/download', DownloadBackupController::class)->name('download');
                Route::delete('/{filename}', [BackupController::class, 'destroy'])->name('destroy');
            });
    }

    protected function registerSchedule(): void
    {
        if (! config('backup-ui.schedule.backup', true)) {
            return;
        }

        $schedule = Schedule::command('backup:run', [
            '--filename' => 'backup-'.now()->format('Y-m-d-H-i-s').'.zip',
        ])->daily()->at('01:00');

        if (config('backup-ui.schedule.clean', true)) {
            Schedule::command('backup:clean')->daily()->at('00:30');
        }
    }
}
