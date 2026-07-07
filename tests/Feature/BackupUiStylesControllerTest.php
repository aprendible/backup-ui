<?php

it('serves the compiled stylesheet', function () {
    $response = $this->get(route('backup.styles'));

    $response->assertSuccessful();
    expect($response->headers->get('Content-Type'))->toContain('text/css');
});

it('serves the stylesheet without requiring authorization', function () {
    // No gate defined and no authenticated user: styles must still load
    // so the UI is never rendered unstyled behind a 403.
    $this->get(route('backup.styles'))->assertSuccessful();
});
