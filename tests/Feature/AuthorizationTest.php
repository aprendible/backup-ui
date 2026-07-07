<?php

use Aprendible\BackupUi\Http\Middleware\AuthorizeBackupAccess;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

it('returns 403 when no backup-access gate is defined', function () {
    $this->get('/backup')->assertForbidden();
});

it('returns 403 when the gate denies access', function () {
    Gate::define('backup-access', fn () => false);

    $this->get('/backup')->assertForbidden();
});

it('returns 200 when the gate allows access and user is authenticated', function () {
    Gate::define('backup-access', fn () => true);

    $user = User::make()->forceFill(['name' => 'Test']);

    $this->actingAs($user)
        ->get('/backup')
        ->assertSuccessful();
});

it('returns 403 for unauthenticated requests even with a permissive gate', function () {
    Gate::define('backup-access', fn () => true);

    $this->get('/backup')->assertForbidden();
});

it('applies the middleware to all backup routes', function () {
    Gate::define('backup-access', fn () => true);

    $routes = collect(Route::getRoutes())->filter(
        fn ($route) => str_starts_with($route->uri(), 'backup')
            && $route->getName() !== 'backup.styles'
    );

    expect($routes)->not->toBeEmpty();

    foreach ($routes as $route) {
        expect($route->gatherMiddleware())->toContain(AuthorizeBackupAccess::class);
    }
});
