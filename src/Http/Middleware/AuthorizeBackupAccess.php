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
        $gate = config('backup-ui.gate') ?: 'backup-access';

        if (Gate::has($gate)) {
            Gate::authorize($gate);
        } else {
            abort(403, "Backup access not configured. Define a \"{$gate}\" gate.");
        }

        return $next($request);
    }
}
