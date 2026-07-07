<?php

namespace Aprendible\BackupUi\Http\Controllers;

use Aprendible\BackupUi\Http\Controllers\Concerns\FindsBackups;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadBackupController extends Controller
{
    use FindsBackups;

    public function __invoke(string $filename): StreamedResponse
    {
        $backup = $this->findBackupOrFail($filename);

        return response()->streamDownload(function () use ($backup) {
            echo stream_get_contents($backup->stream());
        }, $filename);
    }
}
