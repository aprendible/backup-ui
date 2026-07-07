<?php

namespace Aprendible\BackupUi\Http\Controllers;

use Aprendible\BackupUi\Http\Controllers\Concerns\FindsBackups;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\BackupDestination\BackupDestinationFactory;
use Spatie\Backup\Config\Config;

class BackupController extends Controller
{
    use FindsBackups;

    public function index(): View
    {
        $config = app(Config::class);
        $destinations = BackupDestinationFactory::createFromArray($config);

        $statuses = $destinations->map(fn (BackupDestination $destination) => [
            'disk' => $destination->diskName(),
            'name' => $destination->backupName(),
            'reachable' => $destination->isReachable(),
            'used_storage' => $destination->usedStorage(),
            'backups' => $destination->backups()->map(fn (Backup $backup) => [
                'path' => $backup->path(),
                'filename' => basename($backup->path()),
                'date' => $backup->date(),
                'size' => $backup->sizeInBytes(),
            ]),
        ]);

        return view('backup-ui::backups.index', [
            'statuses' => $statuses,
        ]);
    }

    public function destroy(string $filename): RedirectResponse
    {
        $this->findBackupOrFail($filename)->delete();

        return redirect()->back()->with('success', __('Backup deleted.'));
    }
}
