<?php

namespace Aprendible\BackupUi\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Backup\Config\Config;
use Spatie\Backup\Tasks\Backup\BackupJobFactory;

class RunBackupController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['sometimes', 'string', 'in:full,db,files'],
            'disk' => ['sometimes', 'string', 'nullable'],
        ]);

        $config = app(Config::class);
        $backupJob = BackupJobFactory::createFromConfig($config);

        if (($validated['type'] ?? 'full') === 'db') {
            $backupJob->dontBackupFilesystem();
        } elseif (($validated['type'] ?? 'full') === 'files') {
            $backupJob->dontBackupDatabases();
        }

        if (! empty($validated['disk'])) {
            $backupJob->onlyBackupTo($validated['disk']);
        }

        try {
            $backupJob->run();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Backup failed: :message', ['message' => $e->getMessage()]));
        }

        return redirect()->back()->with('success', __('Backup completed successfully.'));
    }
}
