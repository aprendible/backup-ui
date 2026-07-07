<?php

namespace Aprendible\BackupUi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Aprendible\BackupUi\BackupUi
 */
class BackupUi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Aprendible\BackupUi\BackupUi::class;
    }
}
