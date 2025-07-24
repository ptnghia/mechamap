<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LangCheckCommand extends Command
{
    protected $signature = 'lang:check';
    protected $description = 'Check translation keys';

    public function handle()
    {
        $this->info('Checking translation keys...');
        // Implementation here
    }
}
