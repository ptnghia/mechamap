<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\Showcase;
use Illuminate\Support\Facades\DB;

class BookmarkSeeder extends Seeder
{
    /**
     * Seed bookmarks với user bookmarks thực tế
     * Tạo personal bookmark collections
     */
    public function run(): void
    {
        $this->command->info('🔖 Bắt đầu seed bookmarks...');

        // Lấy dữ liệu cần thiết
        $users = User::all();
        $threads = Thread::all();
        $comments = Comment::all();
        $showcases = Showcase::all();

        if ($users->isEmpty()) {
            $this->command->error('❌ Cần có users trước khi seed bookmarks!');
            return;
        }

        // Tạo bookmarks cho users
        $this->createBookmarks($users, $threads, $comments, $showcases);

        $this->command->info('✅ Hoàn thành seed bookmarks!');
    }

    private function createBookmarks($users, $threads, $comments, $showcases): void
    {
        $bookmarks = [];

        foreach ($users as $user) {
            // Mỗi user có 5-20 bookmarks
            $bookmarkCount = rand(5, 20);

            for ($i = 0; $i < $bookmarkCount; $i++) {
                $bookmark = $this->generateRandomBookmark($user, $threads, $comments, $showcases);
                if ($bookmark) {
                    $bookmarks[] = $bookmark;
                }
            }
        }

        // Remove duplicates
        $bookmarks = $this->removeDuplicateBookmarks($bookmarks);

        // Batch insert
        if (!empty($bookmarks)) {
            $chunks = array_chunk($bookmarks, 500);
            foreach ($chunks as $chunk) {
                DB::table('bookmarks')->insert($chunk);
            }
        }

        $this->command->line("   🔖 Tạo " . count($bookmarks) . " bookmarks");
    }

    private function generateRandomBookmark($user, $threads, $comments, $showcases): ?array
    {
        // Distribution: 60% threads, 25% showcases, 15% comments
        $contentTypes = [
            'thread' => 60,
            'showcase' => 25,
            'comment' => 15
        ];

        $contentType = $this->getWeightedRandom($contentTypes);

        switch ($contentType) {
            case 'thread':
                if ($threads->isEmpty()) return null;
                $thread = $threads->random();
                return [
                    'user_id' => $user->id,
                    'bookmarkable_type' => 'App\\Models\\Thread',
                    'bookmarkable_id' => $thread->id,
                    'created_at' => now()->subDays(rand(0, 60)),
                    'updated_at' => now()->subDays(rand(0, 10)),
                ];

            case 'showcase':
                if ($showcases->isEmpty()) return null;
                $showcase = $showcases->random();
                return [
                    'user_id' => $user->id,
                    'bookmarkable_type' => 'App\\Models\\Showcase',
                    'bookmarkable_id' => $showcase->id,
                    'created_at' => now()->subDays(rand(0, 45)),
                    'updated_at' => now()->subDays(rand(0, 8)),
                ];

            case 'comment':
                if ($comments->isEmpty()) return null;
                $comment = $comments->random();
                return [
                    'user_id' => $user->id,
                    'bookmarkable_type' => 'App\\Models\\Comment',
                    'bookmarkable_id' => $comment->id,
                    'created_at' => now()->subDays(rand(0, 30)),
                    'updated_at' => now()->subDays(rand(0, 5)),
                ];

            default:
                return null;
        }
    }

    private function getBookmarkCollection($type): string
    {
        $collections = [
            'thread' => [
                'Technical References',
                'SolidWorks Tips',
                'CNC Programming',
                'FEA Analysis',
                'Manufacturing Processes',
                'Material Science',
                'Design Guidelines',
                'Troubleshooting',
                'Best Practices',
                'Learning Resources'
            ],
            'showcase' => [
                'Inspiration Projects',
                'Design Ideas',
                'Technical Examples',
                'Portfolio References',
                'Advanced Techniques',
                'Industry Projects',
                'CAD Models',
                'Analysis Examples',
                'Manufacturing Cases',
                'Innovation Ideas'
            ],
            'comment' => [
                'Useful Tips',
                'Expert Advice',
                'Quick Solutions',
                'Code Snippets',
                'Formulas',
                'References',
                'Calculations',
                'Troubleshooting Tips',
                'Best Practices',
                'Learning Notes'
            ]
        ];

        $typeCollections = $collections[$type] ?? $collections['thread'];
        return $typeCollections[array_rand($typeCollections)];
    }

    private function getBookmarkNotes($type, $title): ?string
    {
        // 40% chance có notes
        if (rand(0, 100) > 40) {
            return null;
        }

        $notes = [
            'thread' => [
                'Cần review lại khi có project tương tự',
                'Approach hay, có thể apply vào work',
                'Good reference cho future design',
                'Bookmark để học thêm về topic này',
                'Useful troubleshooting steps',
                'Remember this solution cho similar problems',
                'Excellent tutorial, cần practice',
                'Important safety considerations',
                'Cost-effective approach',
                'Industry standard method'
            ],
            'showcase' => [
                'Inspiring design approach',
                'Innovative solution, cần study thêm',
                'Good example cho portfolio',
                'Advanced techniques worth learning',
                'Professional quality work',
                'Interesting manufacturing process',
                'Creative problem solving',
                'Excellent documentation style',
                'Real-world application example',
                'Benchmark cho quality standards'
            ],
            'comment' => [
                'Quick tip, very useful',
                'Expert insight, valuable',
                'Smart solution approach',
                'Remember this formula',
                'Practical advice',
                'Time-saving tip',
                'Important safety note',
                'Cost optimization idea',
                'Efficiency improvement',
                'Best practice reminder'
            ]
        ];

        $typeNotes = $notes[$type] ?? $notes['thread'];
        return $typeNotes[array_rand($typeNotes)];
    }

    private function removeDuplicateBookmarks($bookmarks): array
    {
        $unique = [];
        $seen = [];

        foreach ($bookmarks as $bookmark) {
            $key = $bookmark['user_id'] . '-' . $bookmark['bookmarkable_type'] . '-' . $bookmark['bookmarkable_id'];

            if (!in_array($key, $seen)) {
                $unique[] = $bookmark;
                $seen[] = $key;
            }
        }

        return $unique;
    }

    private function getWeightedRandom($weights): string
    {
        $random = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $item => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $item;
            }
        }

        return array_key_first($weights);
    }
}
