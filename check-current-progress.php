<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

class ProgressChecker
{
    private $translationFiles = [];
    private $totalCalls = 0;
    private $successfulCalls = 0;
    private $problematicFiles = [];

    public function __construct()
    {
        $this->loadTranslations();
    }

    private function loadTranslations()
    {
        $viPath = resource_path('lang/vi');
        $files = glob($viPath . '/*.php');

        foreach ($files as $file) {
            $basename = basename($file, '.php');
            $this->translationFiles[$basename] = include $file;
        }
    }

    public function analyzeProgress()
    {
        echo "🔍 CHECKING TRANSLATION PROGRESS\n";
        echo "=================================\n\n";

        $viewsPath = resource_path('views');
        $bladeFiles = $this->getBladeFiles($viewsPath);

        echo "📊 Found " . count($bladeFiles) . " blade files\n\n";

        $fileResults = [];

        foreach ($bladeFiles as $file) {
            $result = $this->analyzeFile($file);
            if ($result['total'] > 0) {
                $fileResults[] = $result;
                $this->totalCalls += $result['total'];
                $this->successfulCalls += $result['successful'];

                if ($result['success_rate'] < 100) {
                    $this->problematicFiles[] = $result;
                }
            }
        }

        // Sort problematic files by number of issues (descending)
        usort($this->problematicFiles, function($a, $b) {
            return $b['problematic'] - $a['problematic'];
        });

        $this->printSummary();
        $this->printTopProblematic();
    }

    private function getBladeFiles($directory)
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php' && strpos($file->getFilename(), '.blade.') !== false) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    private function analyzeFile($filePath)
    {
        $content = file_get_contents($filePath);
        $relativePath = str_replace(resource_path('views') . DIRECTORY_SEPARATOR, '', $filePath);
        $relativePath = str_replace('\\', '/', $relativePath);

        preg_match_all('/{{\s*__\([\'"]([^\'"]+)[\'"]\)\s*}}/', $content, $matches);

        $total = count($matches[1]);
        $successful = 0;
        $problematic = 0;

        foreach ($matches[1] as $key) {
            if ($this->resolveTranslationKey($key)) {
                $successful++;
            } else {
                $problematic++;
            }
        }

        $successRate = $total > 0 ? round(($successful / $total) * 100, 1) : 100;

        return [
            'file' => $relativePath,
            'total' => $total,
            'successful' => $successful,
            'problematic' => $problematic,
            'success_rate' => $successRate
        ];
    }

    private function resolveTranslationKey($key)
    {
        if (strpos($key, '.') === false) {
            return false;
        }

        $parts = explode('.', $key);
        $namespace = array_shift($parts);

        if (!isset($this->translationFiles[$namespace])) {
            return false;
        }

        $current = $this->translationFiles[$namespace];
        foreach ($parts as $part) {
            if (!is_array($current) || !isset($current[$part])) {
                return false;
            }
            $current = $current[$part];
        }

        return is_string($current);
    }

    private function printSummary()
    {
        $overallSuccessRate = $this->totalCalls > 0 ? round(($this->successfulCalls / $this->totalCalls) * 100, 1) : 100;

        echo "📈 OVERALL PROGRESS SUMMARY\n";
        echo "===========================\n";
        echo "Total translation calls: {$this->totalCalls}\n";
        echo "Successful calls: {$this->successfulCalls}\n";
        echo "Problematic calls: " . ($this->totalCalls - $this->successfulCalls) . "\n";
        echo "Overall success rate: {$overallSuccessRate}%\n\n";

        if ($overallSuccessRate >= 95) {
            echo "🎉 EXCELLENT PROGRESS! Almost at 100%!\n\n";
        } elseif ($overallSuccessRate >= 90) {
            echo "🚀 GREAT PROGRESS! Getting close to the goal!\n\n";
        } else {
            echo "📊 Good progress, keep going!\n\n";
        }
    }

    private function printTopProblematic()
    {
        echo "🎯 TOP PROBLEMATIC FILES (Need immediate attention)\n";
        echo "====================================================\n";

        if (empty($this->problematicFiles)) {
            echo "🎉 NO PROBLEMATIC FILES! ALL FILES ARE 100% SUCCESSFUL!\n";
            return;
        }

        $topFiles = array_slice($this->problematicFiles, 0, 10);

        foreach ($topFiles as $index => $result) {
            $status = $result['success_rate'] == 0 ? "❌" : "⚠️";
            echo sprintf(
                "%s %d. %s\n   Success: %d%% (%d/%d) - %d issues\n\n",
                $status,
                $index + 1,
                $result['file'],
                $result['success_rate'],
                $result['successful'],
                $result['total'],
                $result['problematic']
            );
        }

        echo "💡 NEXT ACTIONS:\n";
        echo "================\n";
        echo "1. Focus on files with most issues (top of the list)\n";
        echo "2. Files with 0% success rate need namespace conversion\n";
        echo "3. Files with partial success need missing key additions\n";
    }
}

$checker = new ProgressChecker();
$checker->analyzeProgress();
