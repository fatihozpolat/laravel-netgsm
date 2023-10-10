<?php

namespace Fatihozpolat\Netgsm\Commands;

use Illuminate\Console\Command;

class NetgsmCommand extends Command
{
    public $signature = 'laravel-netgsm';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
