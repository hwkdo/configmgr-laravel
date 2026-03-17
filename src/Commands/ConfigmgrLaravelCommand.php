<?php

namespace Hwkdo\ConfigmgrLaravel\Commands;

use Illuminate\Console\Command;

class ConfigmgrLaravelCommand extends Command
{
    public $signature = 'configmgr-laravel';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
