<?php

namespace App\Console\Commands;

use App\Models\Showcase;
use Illuminate\Console\Command;

class NormalizeSoftwareUsed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'showcase:normalize-software';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize software_used field to JSON array format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting software_used normalization...');
        
        $showcases = Showcase::all();
        $updated = 0;
        $skipped = 0;
        
        foreach ($showcases as $showcase) {
            $originalSoftware = $showcase->software_used;
            
            if (empty($originalSoftware)) {
                $this->line("Showcase {$showcase->id}: Skipping empty software_used");
                $skipped++;
                continue;
            }
            
            // Check if already valid JSON array
            $decoded = json_decode($originalSoftware, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->line("Showcase {$showcase->id}: Already valid JSON array");
                $skipped++;
                continue;
            }
            
            // Normalize to array
            $normalizedSoftware = $this->normalizeSoftwareString($originalSoftware);
            
            if (!empty($normalizedSoftware)) {
                $showcase->software_used = json_encode($normalizedSoftware);
                $showcase->save();
                
                $this->info("Showcase {$showcase->id}: Updated");
                $this->line("  From: {$originalSoftware}");
                $this->line("  To: " . json_encode($normalizedSoftware));
                $updated++;
            } else {
                $this->warn("Showcase {$showcase->id}: Could not normalize '{$originalSoftware}'");
                $skipped++;
            }
        }
        
        $this->info("\nNormalization completed!");
        $this->info("Updated: {$updated} showcases");
        $this->info("Skipped: {$skipped} showcases");
        
        return 0;
    }
    
    /**
     * Normalize software string to array
     */
    private function normalizeSoftwareString(string $software): array
    {
        // Remove extra whitespace
        $software = trim($software);
        
        // Split by common delimiters
        $delimiters = [',', ';', '|', ' and ', ' & ', ' + '];
        
        $items = [$software];
        
        foreach ($delimiters as $delimiter) {
            $newItems = [];
            foreach ($items as $item) {
                $split = explode($delimiter, $item);
                $newItems = array_merge($newItems, $split);
            }
            $items = $newItems;
        }
        
        // Clean up each item
        $normalized = [];
        foreach ($items as $item) {
            $item = trim($item);
            if (!empty($item)) {
                // Capitalize first letter of each word
                $item = ucwords(strtolower($item));
                
                // Fix common software names
                $item = $this->fixCommonSoftwareNames($item);
                
                if (!in_array($item, $normalized)) {
                    $normalized[] = $item;
                }
            }
        }
        
        return $normalized;
    }
    
    /**
     * Fix common software name variations
     */
    private function fixCommonSoftwareNames(string $name): string
    {
        $corrections = [
            'solidworks' => 'SolidWorks',
            'autocad' => 'AutoCAD',
            'ansys' => 'ANSYS',
            'ansys mechanical' => 'ANSYS Mechanical',
            'ansys fluent' => 'ANSYS Fluent',
            'catia' => 'CATIA',
            'fusion360' => 'Fusion 360',
            'fusion 360' => 'Fusion 360',
            'mastercam' => 'Mastercam',
            'vericut' => 'Vericut',
            'matlab' => 'MATLAB',
            'fluidsim' => 'FluidSIM',
            'nx' => 'NX',
            'creo' => 'Creo',
            'inventor' => 'Inventor',
            'rhino' => 'Rhino',
            'keyshot' => 'KeyShot',
        ];
        
        $lowerName = strtolower($name);
        
        return $corrections[$lowerName] ?? $name;
    }
}
