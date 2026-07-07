<?php

namespace Aprendible\BackupUi\Http\Controllers\Concerns;

use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestinationFactory;
use Spatie\Backup\Config\Config;

trait FindsBackups
{
    private function findBackupOrFail(string $filename): Backup
    {
        $config = app(Config::class);
        $destinations = BackupDestinationFactory::createFromArray($config);

        foreach ($destinations as $destination) {
            foreach ($destination->backups() as $backup) {
                if (basename($backup->path()) === $filename) {
                    return $backup;
                }
            }
        }

        abort(404, 'Backup not found.');
    }
}
