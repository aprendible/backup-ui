<?php

namespace Aprendible\BackupUi\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Spatie\Backup\Config\Config;

class SettingsController extends Controller
{
    public function __invoke(): View
    {
        $backupConfig = app(Config::class);

        $schedule = [
            'backup' => [
                'enabled' => config('backup-ui.schedule.backup', true),
                'frequency' => 'daily',
                'time' => '01:00',
            ],
            'clean' => [
                'enabled' => config('backup-ui.schedule.clean', true),
                'frequency' => 'daily',
                'time' => '00:30',
            ],
        ];

        return view('backup-ui::settings.index', [
            'backupConfig' => $backupConfig,
            'backupUiConfig' => config('backup-ui'),
            'schedule' => $schedule,
            'monitoredBackups' => $backupConfig->monitoredBackups,
            'backupSource' => $backupConfig->backup->source,
            'destination' => $backupConfig->backup->destination,
            'cleanup' => $backupConfig->cleanup,
            'notifications' => $backupConfig->notifications,
        ]);
    }
}
