<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Test the gallery filtering logic
 */
class TestGalleryFilter extends Command
{
    protected $signature = 'test:gallery-filter';
    protected $description = 'Test the gallery filtering logic';

    public function handle()
    {
        $this->info('=== GALLERY FILTERING TEST ===');

        // Total media count
        $totalMedia = Media::count();
        $this->info("Total media items: {$totalMedia}");

        // Count standalone gallery uploads
        $standaloneMedia = Media::whereNull('thread_id')
            ->whereNull('mediable_type')
            ->whereNull('mediable_id')
            ->count();
        $this->info("Standalone gallery uploads: {$standaloneMedia}");

        // Count media from public threads
        $publicThreadMedia = Media::whereHas('thread', function ($query) {
            $query->where('moderation_status', 'approved')
                ->where('is_spam', false)
                ->whereNull('hidden_at')
                ->whereNull('archived_at');
        })->count();
        $this->info("Media from public threads: {$publicThreadMedia}");

        // Count media from hidden/private threads
        $hiddenThreadMedia = Media::whereHas('thread', function ($query) {
            $query->where(function ($subQuery) {
                $subQuery->where('moderation_status', '!=', 'approved')
                    ->orWhere('is_spam', true)
                    ->orWhereNotNull('hidden_at')
                    ->orWhereNotNull('archived_at');
            });
        })->count();
        $this->info("Media from hidden/private threads: {$hiddenThreadMedia}");

        // Count showcase media
        $showcaseMedia = Media::where('mediable_type', 'App\\Models\\Showcase')->count();
        $this->info("Total showcase media: {$showcaseMedia}");

        // Count approved showcase media
        $approvedShowcaseMedia = Media::where('mediable_type', 'App\\Models\\Showcase')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('showcases')
                    ->whereColumn('showcases.id', 'media.mediable_id')
                    ->where('showcases.status', 'approved');
            })->count();
        $this->info("Media from approved showcases: {$approvedShowcaseMedia}");

        // Count media visible in gallery (our filtered query)
        $galleryVisibleMedia = Media::where(function ($query) {
            // Standalone gallery uploads
            $query->where(function ($subQuery) {
                $subQuery->whereNull('thread_id')
                    ->whereNull('mediable_type')
                    ->whereNull('mediable_id');
            });

            // Media from public threads
            $query->orWhereHas('thread', function ($threadQuery) {
                $threadQuery->where('moderation_status', 'approved')
                    ->where('is_spam', false)
                    ->whereNull('hidden_at')
                    ->whereNull('archived_at');
            });

            // Media from approved showcases
            $query->orWhere(function ($showcaseQuery) {
                $showcaseQuery->where('mediable_type', 'App\\Models\\Showcase')
                    ->whereExists(function ($existsQuery) {
                        $existsQuery->select(DB::raw(1))
                            ->from('showcases')
                            ->whereColumn('showcases.id', 'media.mediable_id')
                            ->where('showcases.status', 'approved');
                    });
            });
        })->count();
        $this->info("Media visible in gallery: {$galleryVisibleMedia}");

        $this->newLine();

        // Verification
        $expected = $standaloneMedia + $publicThreadMedia + $approvedShowcaseMedia;
        if ($expected === $galleryVisibleMedia) {
            $this->info('✅ VERIFICATION PASSED: Gallery filtering is working correctly!');
        } else {
            $this->error('❌ VERIFICATION FAILED: Gallery filtering has issues.');
            $this->error("Expected: {$expected}, Got: {$galleryVisibleMedia}");
        }

        // Show some examples
        $this->newLine();
        $this->info('=== SAMPLE FILTERED MEDIA ===');

        $sampleMedia = Media::with(['user', 'thread'])
            ->where(function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereNull('thread_id')
                        ->whereNull('mediable_type')
                        ->whereNull('mediable_id');
                });
                $query->orWhereHas('thread', function ($threadQuery) {
                    $threadQuery->where('moderation_status', 'approved')
                        ->where('is_spam', false)
                        ->whereNull('hidden_at')
                        ->whereNull('archived_at');
                });
                $query->orWhere(function ($showcaseQuery) {
                    $showcaseQuery->where('mediable_type', 'App\\Models\\Showcase')
                        ->whereExists(function ($existsQuery) {
                            $existsQuery->select(DB::raw(1))
                                ->from('showcases')
                                ->whereColumn('showcases.id', 'media.mediable_id')
                                ->where('showcases.status', 'approved');
                        });
                });
            })
            ->take(5)
            ->get();

        foreach ($sampleMedia as $media) {
            if ($media->thread) {
                $this->line("✓ {$media->file_name} - Thread: {$media->thread->title}");
            } elseif ($media->mediable_type === 'App\\Models\\Showcase') {
                $this->line("✓ {$media->file_name} - Showcase media");
            } else {
                $this->line("✓ {$media->file_name} - Standalone gallery upload");
            }
        }

        // Show some hidden examples
        $this->newLine();
        $this->info('=== SAMPLE HIDDEN MEDIA ===');

        $hiddenMedia = Media::whereHas('thread', function ($query) {
            $query->whereNotNull('hidden_at');
        })->take(3)->get();

        foreach ($hiddenMedia as $media) {
            $this->line("✗ {$media->file_name} - Hidden thread (filtered out)");
        }

        return 0;
    }
}
