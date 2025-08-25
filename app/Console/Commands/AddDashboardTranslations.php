<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AddDashboardTranslations extends Command
{
    protected $signature = 'translations:import-batch';
    protected $description = 'Disabled command - use translations:marketplace instead';

    public function handle(): int
    {
        $this->error('This command is disabled. Use translations:marketplace instead.');
        return 1;
    }
}
