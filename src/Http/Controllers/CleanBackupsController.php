<?php

namespace Aprendible\BackupUi\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Spatie\Backup\BackupDestination\BackupDestinationFactory;
use Spatie\Backup\Config\Config;
use Spatie\Backup\Tasks\Cleanup\CleanupJob;
use Spatie\Backup\Tasks\Cleanup\CleanupStrategy;

class CleanBackupsController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $config = app(Config::class);
        $destinations = BackupDestinationFactory::createFromArray($config);
        $strategy = app(CleanupStrategy::class);

        try {
            (new CleanupJob($destinations, $strategy))->run();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Cleanup failed: :message', ['message' => $e->getMessage()]));
        }

        return redirect()->back()->with('success', __('Cleanup completed successfully.'));
    }
}
