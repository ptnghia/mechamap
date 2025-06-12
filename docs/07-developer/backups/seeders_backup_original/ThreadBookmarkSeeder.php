<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Thread;
use App\Models\User;
use App\Models\ThreadBookmark;
use Faker\Factory as Faker;

class ThreadBookmarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Lấy threads và users
        $threads = Thread::limit(100)->get();
        $users = User::all();

        if ($threads->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Không có threads hoặc users để tạo bookmarks. Hãy chạy ThreadSeeder và UserSeeder trước.');
            return;
        }

        $this->command->info('Tạo thread bookmarks...');

        // Định nghĩa bookmark folders phổ biến
        $bookmarkFolders = [
            'Mechanical Design',
            'AutoCAD Tutorials',
            'SolidWorks Tips',
            'Manufacturing Process',
            'Material Selection',
            'Project Ideas',
            'Reference Docs',
            'Troubleshooting',
            'Career Advice',
            'Industry News',
            'Standards & Codes',
            'Software Tools',
            null, // Uncategorized
        ];

        // Mẫu notes cho bookmarks
        $bookmarkNotes = [
            'Cần review lại khi làm project tương tự',
            'Great resource cho material properties',
            'Bookmark để reference cho team members',
            'Tutorial rất detailed, sẽ follow step-by-step',
            'Good examples cho portfolio project',
            'Cần apply techniques này vào current project',
            'Reference cho design calculations',
            'Useful for troubleshooting common issues',
            'Bookmark để share với students',
            'Implementation guide rất clear',
            'Best practices cho manufacturing process',
            'Comprehensive documentation, very helpful',
        ];

        $bookmarksCreated = 0;
        $targetBookmarks = min(200, $users->count() * 3); // Max 3 bookmarks per user average

        foreach ($users as $user) {
            if ($bookmarksCreated >= $targetBookmarks) break;

            // Random number of bookmarks per user (0-8)
            $userBookmarksCount = $faker->numberBetween(0, 8);
            $userBookmarked = collect();

            for ($i = 0; $i < $userBookmarksCount && $bookmarksCreated < $targetBookmarks; $i++) {
                // Select thread that user hasn't bookmarked yet
                $availableThreads = $threads->whereNotIn('id', $userBookmarked->pluck('id'));

                if ($availableThreads->isEmpty()) break;

                $thread = $availableThreads->random();

                // Check if bookmark already exists (extra safety)
                $existingBookmark = ThreadBookmark::where('thread_id', $thread->id)
                    ->where('user_id', $user->id)
                    ->exists();

                if ($existingBookmark) {
                    continue; // Skip this iteration
                }

                $userBookmarked->push($thread);

                // Random folder assignment (60% organized, 40% uncategorized)
                $folder = $faker->boolean(60) ? $faker->randomElement($bookmarkFolders) : null;

                // 50% chance to add notes
                $notes = null;
                if ($faker->boolean(50)) {
                    $notes = $faker->randomElement($bookmarkNotes);

                    // Sometimes add more specific notes
                    if ($faker->boolean(30)) {
                        $specificNotes = [
                            'Page số ' . $faker->numberBetween(1, 20) . ' có diagram rất hữu ích.',
                            'Method trong section 3 cần apply cho current design.',
                            'Formula tính toán ở cuối thread rất accurate.',
                            'CAD files attached rất useful cho reference.',
                            'Discussion trong comments section có insights hay.',
                            'Author response rate cao, có thể contact để ask questions.',
                        ];
                        $notes .= ' ' . $faker->randomElement($specificNotes);
                    }
                }

                try {
                    ThreadBookmark::create([
                        'thread_id' => $thread->id,
                        'user_id' => $user->id,
                        'folder' => $folder,
                        'notes' => $notes,
                        'created_at' => $faker->dateTimeBetween($thread->created_at, 'now'),
                    ]);

                    $bookmarksCreated++;
                } catch (\Illuminate\Database\QueryException $e) {
                    // Handle duplicate entry gracefully
                    if ($e->errorInfo[1] == 1062) { // Duplicate entry error code
                        $this->command->warn("Duplicate bookmark skipped for thread {$thread->id} and user {$user->id}");
                        continue;
                    }
                    throw $e; // Re-throw if it's a different error
                }
            }
        }

        $this->command->info("✅ Đã tạo {$bookmarksCreated} thread bookmarks");

        // Show bookmark statistics
        $totalFoldersUsed = ThreadBookmark::whereNotNull('folder')->distinct('folder')->count();
        $uncategorizedCount = ThreadBookmark::whereNull('folder')->count();
        $withNotesCount = ThreadBookmark::whereNotNull('notes')->count();

        $this->command->info("📊 Thống kê bookmarks:");
        $this->command->info("   - Folders được sử dụng: {$totalFoldersUsed}");
        $this->command->info("   - Uncategorized: {$uncategorizedCount}");
        $this->command->info("   - Có notes: {$withNotesCount}");

        // Update bookmark counts for threads
        $this->command->info('Đang update bookmark counts...');

        $threadsWithBookmarks = Thread::whereHas('bookmarks')->withCount('bookmarks')->get();
        foreach ($threadsWithBookmarks as $thread) {
            $thread->update(['bookmark_count' => $thread->bookmarks_count]);
        }

        $this->command->info("✅ Đã update bookmark counts cho {$threadsWithBookmarks->count()} threads");
    }
}
