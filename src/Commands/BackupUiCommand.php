<?php

namespace Aprendible\BackupUi\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupUiCommand extends Command
{
    public $signature = 'backup-ui:install';

    public $description = 'Install and configure the backup UI package';

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║     Backup UI — Setup Wizard             ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->newLine();

        $this->publishConfigs();

        $backupName = $this->ask('Application/backup name', config('app.name'));
        $databases = $this->getDatabases();
        $disks = $this->getDestinationDisks();
        $keepAllDays = $this->ask('Keep all backups for how many days', '7');
        $email = $this->ask('Notification email address (optional)', '');
        $backupTime = $this->ask('Schedule backup daily at (24h)', '01:00');
        $cleanTime = $this->ask('Schedule cleanup daily at (24h)', '00:30');

        $this->newLine();
        $this->info('Configuring backup settings...');

        $this->patchBackupConfig(
            name: $backupName,
            databases: $databases,
            disks: $disks,
            keepAllDays: (int) $keepAllDays,
            email: $email,
        );

        if (! $this->diskExists($disks[0] ?? 'local')) {
            $this->createBackupDisk($disks[0]);
        }

        $this->newLine();
        $this->info('✓ Backup UI installed successfully!');
        $this->warn('  Visit '.route('backup.index', absolute: false).' to manage backups.');

        return self::SUCCESS;
    }

    private function publishConfigs(): void
    {
        $this->comment('Publishing configuration files...');

        $this->call('vendor:publish', [
            '--provider' => 'Spatie\Backup\BackupServiceProvider',
            '--tag' => 'config',
        ]);

        $this->call('vendor:publish', [
            '--provider' => 'Aprendible\BackupUi\BackupUiServiceProvider',
            '--tag' => 'backup-ui-config',
        ]);
    }

    private function getDatabases(): array
    {
        $connections = array_keys(config('database.connections', []));

        if (empty($connections)) {
            return [config('database.default')];
        }

        return (array) $this->choice(
            question: 'Which databases should be backed up?',
            choices: $connections,
            default: 0,
            multiple: true,
        );
    }

    private function getDestinationDisks(): array
    {
        $disks = array_keys(config('filesystems.disks', []));

        if (empty($disks)) {
            return ['local'];
        }

        return (array) $this->choice(
            question: 'Which storage disks should backups be stored on?',
            choices: $disks,
            default: array_search('local', $disks) ?: 0,
            multiple: true,
        );
    }

    private function diskExists(string $disk): bool
    {
        return array_key_exists($disk, config('filesystems.disks', []));
    }

    private function createBackupDisk(string $diskName): void
    {
        if (! $this->confirm("The disk '{$diskName}' doesn't exist. Create it at storage/app/{$diskName}?", true)) {
            return;
        }

        $path = config_path('filesystems.php');
        $contents = File::get($path);

        $diskConfig = <<<PHP

            '{$diskName}' => [
                'driver' => 'local',
                'root' => storage_path('app/{$diskName}'),
                'throw' => false,
                'report' => false,
            ],
PHP;

        config()->set("filesystems.disks.{$diskName}", [
            'driver' => 'local',
            'root' => storage_path("app/{$diskName}"),
            'throw' => false,
            'report' => false,
        ]);

        $search = "'disks' => [";
        $pos = strpos($contents, $search);

        if ($pos === false) {
            $this->warn('Could not modify config/filesystems.php. Add the disk manually.');

            return;
        }

        $contents = substr_replace($contents, $search.$diskConfig, $pos, strlen($search));
        File::put($path, $contents);

        $this->info("Disk '{$diskName}' added to config/filesystems.php.");
    }

    private function patchBackupConfig(
        string $name,
        array $databases,
        array $disks,
        int $keepAllDays,
        string $email,
    ): void {
        $path = config_path('backup.php');

        if (! file_exists($path)) {
            $this->warn('config/backup.php not found. Skipping backup configuration.');

            return;
        }

        $contents = File::get($path);

        $replacements = [
            ["'name' => env('APP_NAME', 'laravel-backup'),", "        'name' => '{$name}',"],

            [
                "'databases' => [\n                env('DB_CONNECTION', 'mysql'),\n            ],",
                "'databases' => [\n".collect($databases)->map(fn ($db) => "                '{$db}',")->implode("\n")."\n            ],",
            ],

            [
                "'disks' => [\n                'local',\n            ],",
                "'disks' => [\n".collect($disks)->map(fn ($disk) => "                '{$disk}',")->implode("\n")."\n            ],",
            ],
        ];

        if ($email) {
            $replacements[] = ["'to' => 'your@example.com',", "            'to' => '{$email}',"];
        }

        $contents = preg_replace(
            '/\'keep_all_backups_for_days\'\s*=>\s*\d+/',
            "'keep_all_backups_for_days' => {$keepAllDays}",
            $contents,
            1,
        );

        // Disable encryption by default
        $contents = preg_replace(
            "/'encryption'\s*=>\s*'default'/",
            "'encryption' => env('BACKUP_ENCRYPTION', 'none')",
            $contents,
            1,
        );

        foreach ($replacements as [$search, $replace]) {
            $pos = strpos($contents, $search);
            if ($pos !== false) {
                $contents = substr_replace($contents, $replace, $pos, strlen($search));
            }
        }

        File::put($path, $contents);

        $this->info('config/backup.php updated.');
    }
}
