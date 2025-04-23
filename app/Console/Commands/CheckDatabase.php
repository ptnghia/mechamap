<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Category;
use App\Models\Forum;
use App\Models\Thread;
use App\Models\Post;

class CheckDatabase extends Command
{
    protected $signature = 'db:check';
    protected $description = 'Check database tables and counts';

    public function handle()
    {
        $this->info('Database Check');
        $this->info('-------------');
        
        $this->info('Users: ' . User::count());
        $this->info('Categories: ' . Category::count());
        $this->info('Forums: ' . Forum::count());
        $this->info('Threads: ' . Thread::count());
        $this->info('Posts: ' . Post::count());
        
        return Command::SUCCESS;
    }
}
