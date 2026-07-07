<?php

use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->authorizeBackupAccess();
});

it('runs cleanup successfully', function () {
    Event::fake();

    $this->from('/backup')
        ->post(route('backup.clean'))
        ->assertSessionHasNoErrors()
        ->assertRedirect('/backup');
});

it('requires authorization for cleanup', function () {
    $this->from('/backup')
        ->post(route('backup.clean'))
        ->assertRedirect();
});
