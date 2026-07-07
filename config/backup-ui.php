<?php

return [

    'gate' => null,

    'schedule' => [
        'backup' => true,
        'clean' => true,
    ],

    'routes' => [
        'prefix' => 'backup',
        'middleware' => ['web'],
    ],

];
