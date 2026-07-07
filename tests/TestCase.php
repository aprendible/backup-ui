<?php

namespace Aprendible\BackupUi\Tests;

use Aprendible\BackupUi\BackupUiServiceProvider;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Backup\BackupServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            BackupUiServiceProvider::class,
            BackupServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        config()->set('database.default', 'testing');

        $backupDiskPath = sys_get_temp_dir().'/backup-ui-testing';

        if (! is_dir($backupDiskPath)) {
            mkdir($backupDiskPath, 0777, true);
        }

        config()->set('filesystems.disks.backup-test', [
            'driver' => 'local',
            'root' => $backupDiskPath,
        ]);

        config()->set('backup.backup.name', 'backup-ui-test');
        config()->set('backup.backup.source.files.include', []);
        config()->set('backup.backup.source.databases', []);
        config()->set('backup.backup.destination.disks', ['backup-test']);
        config()->set('backup.backup.temporary_directory', $backupDiskPath.'/temp');
        config()->set('backup.monitor_backups', []);
        config()->set('backup.notifications.notifications', []);
    }

    protected function authorizeBackupAccess(): void
    {
        $user = new class(['name' => 'Test']) extends User
        {
            protected $table = 'users';

            protected $fillable = ['name'];
        };

        $this->actingAs($user);

        Gate::define('backup-access', fn () => true);
    }
}
