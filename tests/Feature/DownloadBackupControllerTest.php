<?php

use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->authorizeBackupAccess();
    $this->disk = Storage::disk('backup-test');
    $this->disk->put('backup-ui-test/test-backup-2026-07-07-12-00-00.zip', 'fake-zip-content');
});

afterEach(function () {
    $this->disk->deleteDirectory('backup-ui-test');
});

it('downloads an existing backup file', function () {
    $response = $this->get(route('backup.download', 'test-backup-2026-07-07-12-00-00.zip'));

    $response->assertSuccessful();
    $response->assertDownload('test-backup-2026-07-07-12-00-00.zip');
});

it('returns 404 when downloading a non-existent backup', function () {
    $this->get(route('backup.download', 'nonexistent.zip'))
        ->assertNotFound();
});
