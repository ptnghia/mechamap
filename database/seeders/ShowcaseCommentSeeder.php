<?php

namespace Database\Seeders;

use App\Models\ShowcaseComment;
use App\Models\Showcase;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShowcaseCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🎯 Bắt đầu tạo Showcase Comments...\n";

        $showcases = Showcase::all();
        $users = User::all();

        if ($showcases->count() === 0) {
            echo "❌ Không có showcases để tạo comments\n";
            return;
        }

        if ($users->count() === 0) {
            echo "❌ Không có users để tạo comments\n";
            return;
        }

        // Comments mẫu về kỹ thuật cơ khí
        $mechanicalComments = [
            'Thiết kế rất chuyên nghiệp! Tôi thích cách bạn tối ưu hóa kết cấu.',
            'Excellent CAD work! Có thể share thêm về material selection không?',
            'Impressive engineering! Tolerance này có đáp ứng manufacturing requirements không?',
            'Great simulation results. Bạn đã verify với actual testing chưa?',
            'Perfect assembly design. Rất dễ maintain và service.',
            'Outstanding FEA analysis. Safety factor có phù hợp với application không?',
            'Brilliant automation solution! Cost effectiveness thế nào?',
            'Innovative approach! Đã consider environmental impact chưa?',
            'Solid mechanical design. Documentation rất chi tiết.',
            'Amazing precision engineering. Quality control process như thế nào?',
            'Thank you for sharing! Sẽ áp dụng vào project hiện tại.',
            'Bookmarked! Rất hữu ích cho R&D team.',
            'Could you elaborate on the manufacturing process?',
            'Perfect solution for automation challenges we face.',
            'Great teamwork! Project management approach như thế nào?',
        ];

        $replyComments = [
            'Cảm ơn feedback! Sẽ update documentation chi tiết hơn.',
            'Good point! Tôi sẽ add thêm material properties analysis.',
            'Exactly! Safety là priority số 1 trong design này.',
            'Sure! Sẽ share workflow trong upcoming tutorial.',
            'Thanks! Quality assurance process rất strict.',
            'Appreciate the comment! Environmental compliance đã được ensure.',
            'Good question! Cost analysis sẽ được include trong phase 2.',
        ];

        $createdCount = 0;

        // Tạo comments cho mỗi showcase
        foreach ($showcases as $showcase) {
            // Tạo 2-4 comments cho mỗi showcase
            $numComments = rand(2, 4);
            $usedUsers = [];

            for ($i = 0; $i < $numComments; $i++) {
                // Đảm bảo không trùng user trong cùng showcase
                $availableUsers = $users->filter(function ($user) use ($showcase, $usedUsers) {
                    return $user->id !== $showcase->user_id && !in_array($user->id, $usedUsers);
                });

                if ($availableUsers->count() === 0) {
                    break; // Không còn user nào available
                }

                $randomUser = $availableUsers->random();
                $usedUsers[] = $randomUser->id;

                try {
                    $comment = ShowcaseComment::create([
                        'showcase_id' => $showcase->id,
                        'user_id' => $randomUser->id,
                        'comment' => $mechanicalComments[array_rand($mechanicalComments)],
                        'parent_id' => null,
                        'like_count' => rand(0, 8),
                        'created_at' => now()->subDays(rand(1, 10))->subHours(rand(0, 23)),
                        'updated_at' => now()->subDays(rand(0, 5))->subHours(rand(0, 23)),
                    ]);

                    if ($comment) {
                        echo "✅ Comment #{$comment->id}: {$randomUser->name} commented on showcase #{$showcase->id}\n";
                        $createdCount++;
                    }
                } catch (\Exception $e) {
                    echo "⚠️ Không thể tạo comment cho showcase {$showcase->id}: " . $e->getMessage() . "\n";
                    continue;
                }
            }
        }

        // Tạo một số reply comments
        $topLevelComments = ShowcaseComment::whereNull('parent_id')->take(5)->get();

        foreach ($topLevelComments as $parentComment) {
            // Tạo reply từ chủ showcase
            $showcase = $parentComment->showcase;
            if ($showcase && $showcase->user_id !== $parentComment->user_id) {
                try {
                    $reply = ShowcaseComment::create([
                        'showcase_id' => $showcase->id,
                        'user_id' => $showcase->user_id,
                        'comment' => $replyComments[array_rand($replyComments)],
                        'parent_id' => $parentComment->id,
                        'like_count' => rand(0, 5),
                        'created_at' => $parentComment->created_at->addHours(rand(1, 48)),
                        'updated_at' => $parentComment->created_at->addHours(rand(1, 72)),
                    ]);

                    if ($reply) {
                        echo "✅ Reply #{$reply->id}: {$showcase->user->name} replied to comment #{$parentComment->id}\n";
                        $createdCount++;
                    }
                } catch (\Exception $e) {
                    echo "⚠️ Không thể tạo reply cho comment {$parentComment->id}: " . $e->getMessage() . "\n";
                    continue;
                }
            }
        }

        echo "🎉 Hoàn thành tạo {$createdCount} showcase comments!\n";
    }
}
