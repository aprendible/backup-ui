<?php

namespace Aprendible\BackupUi\Commands;

use Illuminate\Console\Command;

class BackupUiCommand extends Command
{
    public $signature = 'backup-ui';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
