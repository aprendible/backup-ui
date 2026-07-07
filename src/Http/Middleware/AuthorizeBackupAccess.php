<?php

namespace Aprendible\BackupUi\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeBackupAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Gate::has('backup-access')) {
            Gate::authorize('backup-access');
        } else {
            abort(403, 'Backup access not configured. Define a "backup-access" gate.');
        }

        return $next($request);
    }
}
