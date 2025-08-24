<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ConversationType;

class ConversationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conversationTypes = [
            [
                'name' => 'Thảo luận kỹ thuật',
                'slug' => 'technical-discussion',
                'description' => 'Nhóm thảo luận các chủ đề kỹ thuật chuyên sâu về cơ khí, thiết kế, sản xuất',
                'max_members' => 50,
                'requires_approval' => true,
                'created_by_roles' => ['member', 'senior_member', 'verified_partner'],
                'can_join_roles' => ['member', 'senior_member', 'verified_partner', 'student'],
                'is_active' => true,
            ],
            [
                'name' => 'Hợp tác dự án',
                'slug' => 'project-collaboration',
                'description' => 'Nhóm làm việc cho các dự án cụ thể, chia sẻ tài liệu và phối hợp công việc',
                'max_members' => 20,
                'requires_approval' => true,
                'created_by_roles' => ['senior_member', 'verified_partner', 'manufacturer', 'supplier'],
                'can_join_roles' => ['member', 'senior_member', 'verified_partner', 'manufacturer', 'supplier'],
                'is_active' => true,
            ],
            [
                'name' => 'Nhóm học tập',
                'slug' => 'study-group',
                'description' => 'Nhóm học tập và chia sẻ kiến thức cho sinh viên và người mới bắt đầu',
                'max_members' => 30,
                'requires_approval' => false,
                'created_by_roles' => ['student', 'member', 'senior_member'],
                'can_join_roles' => ['student', 'member', 'senior_member'],
                'is_active' => true,
            ],
            [
                'name' => 'Mạng lưới ngành',
                'slug' => 'industry-network',
                'description' => 'Mạng lưới kết nối trong ngành cơ khí, chia sẻ cơ hội và thông tin thị trường',
                'max_members' => 100,
                'requires_approval' => true,
                'created_by_roles' => ['verified_partner', 'manufacturer', 'supplier', 'brand'],
                'can_join_roles' => ['verified_partner', 'manufacturer', 'supplier', 'brand', 'senior_member'],
                'is_active' => true,
            ],
            [
                'name' => 'Hỗ trợ kỹ thuật',
                'slug' => 'technical-support',
                'description' => 'Nhóm hỗ trợ giải đáp thắc mắc kỹ thuật và troubleshooting',
                'max_members' => 40,
                'requires_approval' => false,
                'created_by_roles' => ['member', 'senior_member', 'verified_partner'],
                'can_join_roles' => ['student', 'member', 'senior_member', 'verified_partner'],
                'is_active' => true,
            ],
            [
                'name' => 'Nghiên cứu & Phát triển',
                'slug' => 'research-development',
                'description' => 'Nhóm nghiên cứu và phát triển công nghệ mới trong lĩnh vực cơ khí',
                'max_members' => 25,
                'requires_approval' => true,
                'created_by_roles' => ['senior_member', 'verified_partner', 'manufacturer'],
                'can_join_roles' => ['senior_member', 'verified_partner', 'manufacturer', 'supplier'],
                'is_active' => true,
            ],
        ];

        foreach ($conversationTypes as $type) {
            ConversationType::create($type);
        }

        $this->command->info('✅ Created ' . count($conversationTypes) . ' conversation types');
    }
}
