<?php

namespace Aprendible\BackupUi\Http\Controllers;

use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupUiStylesController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        $path = __DIR__.'/../../../resources/dist/backup-ui.css';

        return response()
            ->file($path, [
                'Content-Type' => 'text/css',
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
    }
}
