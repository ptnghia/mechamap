<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\File;

class QueueWorkerManager extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'queue:manage 
                            {action : Action to perform (start|stop|restart|status|generate-supervisor)}
                            {--worker= : Specific worker to manage}
                            {--force : Force action without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Manage queue workers for production environment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $worker = $this->option('worker');

        switch ($action) {
            case 'start':
                return $this->startWorkers($worker);
            case 'stop':
                return $this->stopWorkers($worker);
            case 'restart':
                return $this->restartWorkers($worker);
            case 'status':
                return $this->showStatus($worker);
            case 'generate-supervisor':
                return $this->generateSupervisorConfig();
            default:
                $this->error("Unknown action: {$action}");
                return 1;
        }
    }

    /**
     * Start queue workers
     */
    private function startWorkers($specificWorker = null)
    {
        $workers = $this->getWorkerConfigs($specificWorker);

        foreach ($workers as $name => $config) {
            $this->info("Starting worker: {$name}");
            
            for ($i = 0; $i < $config['processes']; $i++) {
                $command = $this->buildWorkerCommand($name, $config, $i);
                $this->info("Command: {$command}");
                
                // Start worker in background
                if (PHP_OS_FAMILY === 'Windows') {
                    popen("start /B {$command}", 'r');
                } else {
                    exec("{$command} > /dev/null 2>&1 &");
                }
            }
        }

        $this->info('Queue workers started successfully!');
        return 0;
    }

    /**
     * Stop queue workers
     */
    private function stopWorkers($specificWorker = null)
    {
        if (!$this->option('force') && !$this->confirm('Are you sure you want to stop queue workers?')) {
            return 1;
        }

        $this->info('Stopping queue workers...');

        if (PHP_OS_FAMILY === 'Windows') {
            // Windows: Kill php processes running queue:work
            exec('taskkill /F /IM php.exe /FI "WINDOWTITLE eq *queue:work*"');
        } else {
            // Linux: Kill processes
            exec('pkill -f "queue:work"');
        }

        $this->info('Queue workers stopped!');
        return 0;
    }

    /**
     * Restart queue workers
     */
    private function restartWorkers($specificWorker = null)
    {
        $this->stopWorkers($specificWorker);
        sleep(2); // Wait for processes to stop
        return $this->startWorkers($specificWorker);
    }

    /**
     * Show worker status
     */
    private function showStatus($specificWorker = null)
    {
        $this->info('Queue Worker Status:');
        $this->line('');

        if (PHP_OS_FAMILY === 'Windows') {
            $output = shell_exec('tasklist /FI "IMAGENAME eq php.exe" /FO CSV');
            $processes = str_getcsv($output, "\n");
            $queueProcesses = array_filter($processes, function($process) {
                return strpos($process, 'queue:work') !== false;
            });
        } else {
            $output = shell_exec('ps aux | grep "queue:work" | grep -v grep');
            $queueProcesses = $output ? explode("\n", trim($output)) : [];
        }

        if (empty($queueProcesses)) {
            $this->warn('No queue workers are currently running.');
        } else {
            $this->info('Running queue workers:');
            foreach ($queueProcesses as $process) {
                $this->line($process);
            }
        }

        return 0;
    }

    /**
     * Generate supervisor configuration
     */
    private function generateSupervisorConfig()
    {
        $workers = config('queue-workers.workers');
        $supervisorConfig = config('queue-workers.supervisor');

        if (!$supervisorConfig['enabled']) {
            $this->warn('Supervisor is not enabled in configuration.');
            return 1;
        }

        $configContent = '';

        foreach ($workers as $name => $config) {
            $configContent .= $this->generateWorkerSupervisorConfig($name, $config);
            $configContent .= "\n\n";
        }

        // Add group configuration
        $configContent .= "[group:mechamap-queue-workers]\n";
        $configContent .= "programs=" . implode(',', array_map(function($name) {
            return "mechamap-queue-{$name}";
        }, array_keys($workers))) . "\n";
        $configContent .= "priority=999\n";

        $configPath = $supervisorConfig['config_path'] . 'mechamap-queue-workers.conf';
        
        if (File::put($configPath, $configContent)) {
            $this->info("Supervisor configuration generated: {$configPath}");
            $this->info('Run the following commands to apply:');
            $this->line('sudo supervisorctl reread');
            $this->line('sudo supervisorctl update');
            $this->line('sudo supervisorctl start mechamap-queue-workers:*');
        } else {
            $this->error('Failed to write supervisor configuration file.');
            return 1;
        }

        return 0;
    }

    /**
     * Generate supervisor config for a single worker
     */
    private function generateWorkerSupervisorConfig($name, $config)
    {
        $supervisorConfig = config('queue-workers.supervisor');
        $logPath = $supervisorConfig['log_path'];
        
        // Ensure log directory exists
        if (!File::exists($logPath)) {
            File::makeDirectory($logPath, 0755, true);
        }

        $command = $this->buildWorkerCommand($name, $config);

        return "[program:mechamap-queue-{$name}]
command={$command}
process_name=%(program_name)s_%(process_num)02d
numprocs={$config['processes']}
directory=" . base_path() . "
autostart=true
autorestart=true
startsecs=1
startretries=3
user={$supervisorConfig['user']}
redirect_stderr=true
stdout_logfile={$logPath}mechamap-queue-{$name}.log
stdout_logfile_maxbytes=100MB
stdout_logfile_backups=2";
    }

    /**
     * Build worker command
     */
    private function buildWorkerCommand($name, $config, $processIndex = 0)
    {
        $phpPath = PHP_BINARY;
        $artisanPath = base_path('artisan');
        
        return "{$phpPath} {$artisanPath} queue:work {$config['connection']} " .
               "--queue={$config['queue']} " .
               "--timeout={$config['timeout']} " .
               "--sleep={$config['sleep']} " .
               "--tries={$config['tries']} " .
               "--memory={$config['memory']} " .
               "--name=mechamap-{$name}-{$processIndex}";
    }

    /**
     * Get worker configurations
     */
    private function getWorkerConfigs($specificWorker = null)
    {
        $workers = config('queue-workers.workers');

        if ($specificWorker) {
            if (!isset($workers[$specificWorker])) {
                $this->error("Worker '{$specificWorker}' not found in configuration.");
                return [];
            }
            return [$specificWorker => $workers[$specificWorker]];
        }

        return $workers;
    }
}
