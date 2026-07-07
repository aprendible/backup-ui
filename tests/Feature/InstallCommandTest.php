<?php

use Illuminate\Support\Facades\File;

/**
 * Writes the spatie backup config stub to config_path so the install
 * command can find and patch it — in testbench the config is loaded
 * from the package directly and never published to config_path().
 */
function writeSpatieConfigStub(): void
{
    $configPath = config_path('backup.php');

    $stub = <<<'PHP'
<?php

use Spatie\Backup\Notifications\Notifiable;
use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification;
use Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification;
use Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification;
use Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification;
use Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy;
use Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays;
use Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes;

return [

    'backup' => [
        'name' => env('APP_NAME', 'laravel-backup'),
        'source' => [
            'files' => [
                'include' => [
                    base_path(),
                ],
                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                ],
                'follow_links' => false,
                'ignore_unreadable_directories' => false,
                'relative_path' => null,
            ],
            'databases' => [
                env('DB_CONNECTION', 'mysql'),
            ],
        ],
        'database_dump_compressor' => null,
        'database_dump_file_timestamp_format' => null,
        'database_dump_filename_base' => 'database',
        'database_dump_file_extension' => '',
        'destination' => [
            'compression_method' => ZipArchive::CM_DEFAULT,
            'compression_level' => 9,
            'filename_prefix' => '',
            'disks' => [
                'local',
            ],
            'continue_on_failure' => false,
        ],
        'temporary_directory' => storage_path('app/backup-temp'),
        'password' => env('BACKUP_ARCHIVE_PASSWORD'),
        'encryption' => 'default',
        'verify_backup' => false,
        'tries' => 1,
        'retry_delay' => 0,
    ],

    'notifications' => [
        'notifications' => [
            BackupHasFailedNotification::class => ['mail'],
        ],
        'notifiable' => Notifiable::class,
        'mail' => [
            'to' => 'your@example.com',
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'name' => env('MAIL_FROM_NAME', 'Example'),
            ],
        ],
        'slack' => [
            'webhook_url' => '',
            'channel' => null,
            'username' => null,
            'icon' => null,
        ],
        'discord' => [
            'webhook_url' => '',
            'username' => '',
            'avatar_url' => '',
        ],
        'webhook' => [
            'url' => '',
        ],
    ],

    'log_channel' => null,

    'monitor_backups' => [],

    'cleanup' => [
        'strategy' => DefaultStrategy::class,
        'default_strategy' => [
            'keep_all_backups_for_days' => 7,
            'keep_daily_backups_for_days' => 16,
            'keep_weekly_backups_for_weeks' => 8,
            'keep_monthly_backups_for_months' => 4,
            'keep_yearly_backups_for_years' => 2,
            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],
        'tries' => 1,
        'retry_delay' => 0,
    ],

];
PHP;

    File::put($configPath, $stub);
}

function writeBackupUiConfigStub(): void
{
    $configPath = config_path('backup-ui.php');

    $stub = <<<'PHP'
<?php

return [

    'gate' => null,

    'schedule' => [
        'backup' => true,
        'clean' => true,
    ],

    'routes' => [
        'prefix' => 'backup',
        'middleware' => ['web'],
    ],

];
PHP;

    File::put($configPath, $stub);
}

function writeFilesystemsStub(): void
{
    $configPath = config_path('filesystems.php');

    $stub = <<<'PHP'
<?php

return [

    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],
    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
PHP;

    File::put($configPath, $stub);
}

function cleanupTempFiles(): void
{
    $files = [
        config_path('backup.php'),
        config_path('backup-ui.php'),
        config_path('filesystems.php'),
        dirname(config_path('filesystems.php')).'/filesystems.php.bak',
    ];

    foreach ($files as $file) {
        if (file_exists($file)) {
            File::delete($file);
        }
    }
}

beforeEach(function () {
    cleanupTempFiles();
    writeSpatieConfigStub();
    writeBackupUiConfigStub();
    writeFilesystemsStub();
});

afterEach(function () {
    cleanupTempFiles();
});

test('the install command is registered', function () {
    $commands = Artisan::all();

    expect($commands)->toHaveKey('backup-ui:install');
});

test('the install command runs successfully with valid answers', function () {
    config()->set('database.connections', ['testing' => ['driver' => 'testing', 'database' => ':memory:']]);
    config()->set('filesystems.disks', ['local' => ['driver' => 'local', 'root' => sys_get_temp_dir()]]);

    $this->artisan('backup-ui:install')
        ->expectsQuestion('Application/backup name', 'TestApp')
        ->expectsChoice('Which databases should be backed up?', 'testing', ['testing'])
        ->expectsChoice('Which storage disks should backups be stored on?', 'local', ['local'])
        ->expectsQuestion('Keep all backups for how many days', '14')
        ->expectsQuestion('Notification email address (optional)', 'admin@test.com')
        ->expectsQuestion('Schedule backup daily at (24h)', '02:00')
        ->expectsQuestion('Schedule cleanup daily at (24h)', '03:00')
        ->assertSuccessful();
});

test('install command updates backup config values', function () {
    config()->set('database.connections', ['testing' => ['driver' => 'testing', 'database' => ':memory:']]);
    config()->set('filesystems.disks', ['local' => ['driver' => 'local', 'root' => sys_get_temp_dir()]]);

    $this->artisan('backup-ui:install')
        ->expectsQuestion('Application/backup name', 'MyCustomApp')
        ->expectsChoice('Which databases should be backed up?', 'testing', ['testing'])
        ->expectsChoice('Which storage disks should backups be stored on?', 'local', ['local'])
        ->expectsQuestion('Keep all backups for how many days', '30')
        ->expectsQuestion('Notification email address (optional)', 'ops@example.com')
        ->expectsQuestion('Schedule backup daily at (24h)', '02:00')
        ->expectsQuestion('Schedule cleanup daily at (24h)', '03:00')
        ->assertSuccessful();

    $config = require config_path('backup.php');

    expect($config['backup']['name'])->toBe('MyCustomApp');
    expect($config['backup']['source']['databases'])->toBe(['testing']);
    expect($config['backup']['destination']['disks'])->toBe(['local']);
    expect($config['cleanup']['default_strategy']['keep_all_backups_for_days'])->toBe(30);
    expect($config['notifications']['mail']['to'])->toBe('ops@example.com');
});

test('install creates backup disk if it does not exist', function () {
    config()->set('database.connections', ['testing' => ['driver' => 'testing', 'database' => ':memory:']]);
    config()->set('filesystems.disks', ['local' => ['driver' => 'local', 'root' => sys_get_temp_dir()]]);

    $diskName = 'backups';

    $this->artisan('backup-ui:install')
        ->expectsQuestion('Application/backup name', 'Test')
        ->expectsChoice('Which databases should be backed up?', 'testing', ['testing'])
        ->expectsChoice('Which storage disks should backups be stored on?', $diskName, ['local'])
        ->expectsQuestion('Keep all backups for how many days', '7')
        ->expectsQuestion('Notification email address (optional)', '')
        ->expectsQuestion('Schedule backup daily at (24h)', '01:00')
        ->expectsQuestion('Schedule cleanup daily at (24h)', '00:30')
        ->expectsConfirmation("The disk '{$diskName}' doesn't exist. Create it at storage/app/{$diskName}?", 'yes')
        ->assertSuccessful();

    $disks = config('filesystems.disks');
    expect($disks)->toHaveKey($diskName);
    expect($disks[$diskName]['driver'])->toBe('local');
});

test('install command skips disk creation when user declines', function () {
    config()->set('database.connections', ['testing' => ['driver' => 'testing', 'database' => ':memory:']]);
    config()->set('filesystems.disks', ['local' => ['driver' => 'local', 'root' => sys_get_temp_dir()]]);

    $diskName = 'backups';

    $this->artisan('backup-ui:install')
        ->expectsQuestion('Application/backup name', 'Test')
        ->expectsChoice('Which databases should be backed up?', 'testing', ['testing'])
        ->expectsChoice('Which storage disks should backups be stored on?', $diskName, ['local'])
        ->expectsQuestion('Keep all backups for how many days', '7')
        ->expectsQuestion('Notification email address (optional)', '')
        ->expectsQuestion('Schedule backup daily at (24h)', '01:00')
        ->expectsQuestion('Schedule cleanup daily at (24h)', '00:30')
        ->expectsConfirmation("The disk '{$diskName}' doesn't exist. Create it at storage/app/{$diskName}?", 'no')
        ->assertSuccessful();

    $disks = config('filesystems.disks');
    expect($disks)->not->toHaveKey($diskName);
});
