<?php

use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->authorizeBackupAccess();
    $this->disk = Storage::disk('backup-test');
    $this->disk->put('backup-ui-test/test-2026-07-07-12-00-00.zip', 'fake-zip-content');
    $this->disk->put('backup-ui-test/test-2026-07-06-12-00-00.zip', 'another-fake-zip');
});

afterEach(function () {
    $this->disk->deleteDirectory('backup-ui-test');
});

it('lists backups on the index page', function () {
    $response = $this->get(route('backup.index'));

    $response->assertSuccessful();
    $response->assertSee('backup-ui-test');
    $response->assertSee('test-2026-07-07-12-00-00.zip');
    $response->assertSee('test-2026-07-06-12-00-00.zip');
});

it('shows the run and clean buttons on the index page', function () {
    $response = $this->get(route('backup.index'));

    $response->assertSuccessful();
    $response->assertSee('Run Full Backup');
    $response->assertSee('Clean Old Backups');
});

it('shows an empty state when no backups exist', function () {
    $this->disk->deleteDirectory('backup-ui-test');

    $response = $this->get(route('backup.index'));

    $response->assertSuccessful();
    $response->assertSee('No backups yet');
});

it('deletes a backup', function () {
    expect($this->disk->exists('backup-ui-test/test-2026-07-07-12-00-00.zip'))->toBeTrue();

    $this->delete(route('backup.destroy', 'test-2026-07-07-12-00-00.zip'))
        ->assertRedirect();

    expect($this->disk->exists('backup-ui-test/test-2026-07-07-12-00-00.zip'))->toBeFalse();
});

it('returns 404 when deleting a non-existent backup', function () {
    $this->delete(route('backup.destroy', 'nonexistent.zip'))->assertNotFound();
});
