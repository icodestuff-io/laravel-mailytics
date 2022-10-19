<?php

namespace Icodestuff\Mailytics\Commands;

use Illuminate\Console\Command;

class MailyticsCommand extends Command
{
    public $signature = 'laravel-mailytics';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
