<?php

use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification;
use Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays;

use function Pest\Laravel\get;

beforeEach(function () {
    $this->authorizeBackupAccess();
});

test('settings page shows monitored backups', function () {
    config()->set('backup.monitor_backups', [
        [
            'name' => 'monitored-app',
            'disks' => ['backup-test'],
            'health_checks' => [
                MaximumAgeInDays::class => 1,
            ],
        ],
    ]);

    get(route('backup.settings'))
        ->assertOk()
        ->assertSee('monitored-app')
        ->assertSee('backup-test')
        ->assertSee('MaximumAgeInDays');
});

test('settings page is displayed with backup config', function () {

    config()->set('backup.backup.name', 'test-app');
    config()->set('backup.backup.source.files.include', [base_path()]);
    config()->set('backup.backup.source.databases', ['mysql']);
    config()->set('backup.backup.destination.disks', ['backup-test']);

    get(route('backup.settings'))
        ->assertOk()
        ->assertSee('test-app')
        ->assertSee('mysql')
        ->assertSee('backup-test');
});

test('settings page shows notification config', function () {

    config()->set('backup.notifications.mail.to', 'admin@example.com');
    config()->set('backup.notifications.notifications', [
        BackupHasFailedNotification::class => ['mail'],
    ]);

    get(route('backup.settings'))
        ->assertOk()
        ->assertSee('admin@example.com')
        ->assertSee('BackupHasFailedNotification');
});

test('settings page shows cleanup retention', function () {

    config()->set('backup.cleanup.default_strategy.keep_all_backups_for_days', 14);

    get(route('backup.settings'))
        ->assertOk()
        ->assertSee('14');
});

test('settings page shows schedule config', function () {

    get(route('backup.settings'))
        ->assertOk()
        ->assertSee('daily')
        ->assertSee('01:00')
        ->assertSee('00:30');
});

test('settings page shows package config', function () {

    get(route('backup.settings'))
        ->assertOk()
        ->assertSee('/backup')
        ->assertSee('web');
});
