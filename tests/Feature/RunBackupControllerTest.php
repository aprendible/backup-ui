<?php

use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->authorizeBackupAccess();
});

it('runs a full backup', function () {
    Event::fake();

    $this->from('/backup')
        ->post(route('backup.run'), ['type' => 'full'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/backup');
});

it('runs a database-only backup', function () {
    Event::fake();

    $this->from('/backup')
        ->post(route('backup.run'), ['type' => 'db'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/backup');
});

it('runs a files-only backup', function () {
    Event::fake();

    $this->from('/backup')
        ->post(route('backup.run'), ['type' => 'files'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/backup');
});

it('runs a backup targeting a specific disk', function () {
    Event::fake();

    $this->from('/backup')
        ->post(route('backup.run'), ['type' => 'full', 'disk' => 'backup-test'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/backup');
});

it('validates the backup type', function () {
    $this->from('/backup')
        ->post(route('backup.run'), ['type' => 'invalid'])
        ->assertSessionHasErrors('type');
});

it('validates the disk is a string', function () {
    $this->from('/backup')
        ->post(route('backup.run'), ['disk' => ['invalid']])
        ->assertSessionHasErrors('disk');
});
