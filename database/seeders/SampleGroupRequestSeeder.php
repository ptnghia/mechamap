<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GroupRequest;
use App\Models\ConversationType;
use App\Models\User;
use App\Enums\GroupRequestStatus;

class SampleGroupRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get conversation types
        $technicalType = ConversationType::where('slug', 'technical-discussion')->first();
        $projectType = ConversationType::where('slug', 'project-collaboration')->first();
        $studyType = ConversationType::where('slug', 'study-group')->first();
        $industryType = ConversationType::where('slug', 'industry-network')->first();

        // Get sample users
        $users = User::whereIn('role', ['member', 'senior_member', 'verified_partner'])->take(5)->get();

        if ($users->count() < 3) {
            $this->command->warn('⚠️ Not enough users to create sample group requests');
            return;
        }

        $sampleRequests = [
            [
                'conversation_type_id' => $technicalType->id,
                'title' => 'Thảo luận về CAD/CAM hiện đại',
                'description' => 'Nhóm thảo luận về các phần mềm CAD/CAM hiện đại như SolidWorks, Fusion 360, Mastercam. Chia sẻ kinh nghiệm, tips & tricks, và giải đáp thắc mắc.',
                'justification' => 'Hiện tại chưa có nhóm chuyên biệt về CAD/CAM, trong khi đây là công cụ thiết yếu cho mọi kỹ sư cơ khí. Nhóm này sẽ giúp thành viên nâng cao kỹ năng thiết kế.',
                'expected_members' => 25,
                'creator_id' => $users[0]->id,
                'status' => GroupRequestStatus::PENDING,
                'requested_at' => now()->subDays(2),
            ],
            [
                'conversation_type_id' => $projectType->id,
                'title' => 'Dự án Robot tự động hóa',
                'description' => 'Nhóm hợp tác phát triển robot tự động hóa cho dây chuyền sản xuất. Bao gồm thiết kế cơ khí, lập trình điều khiển, và tích hợp hệ thống.',
                'justification' => 'Dự án này cần sự phối hợp của nhiều chuyên gia từ thiết kế cơ khí đến automation. Nhóm sẽ tạo môi trường hợp tác hiệu quả.',
                'expected_members' => 8,
                'creator_id' => $users[1]->id,
                'status' => GroupRequestStatus::APPROVED,
                'requested_at' => now()->subDays(5),
                'reviewed_at' => now()->subDays(3),
                'reviewed_by' => 1, // Super Admin
                'admin_notes' => 'Dự án có tính khả thi cao và phù hợp với mục tiêu của cộng đồng.',
            ],
            [
                'conversation_type_id' => $studyType->id,
                'title' => 'Học nhóm FEA/CFD',
                'description' => 'Nhóm học tập về phân tích phần tử hữu hạn (FEA) và động lực học chất lỏng (CFD). Thực hành với ANSYS, Abaqus, và các phần mềm mô phỏng.',
                'justification' => 'FEA/CFD là kỹ năng quan trọng nhưng khó học. Nhóm học tập sẽ giúp thành viên hỗ trợ lẫn nhau và chia sẻ tài liệu.',
                'expected_members' => 15,
                'creator_id' => $users[2]->id,
                'status' => GroupRequestStatus::NEEDS_REVISION,
                'requested_at' => now()->subDays(4),
                'reviewed_at' => now()->subDays(1),
                'reviewed_by' => 1,
                'admin_notes' => 'Cần bổ sung thêm thông tin về chương trình học và timeline cụ thể.',
            ],
            [
                'conversation_type_id' => $industryType->id,
                'title' => 'Mạng lưới nhà cung cấp Việt Nam',
                'description' => 'Kết nối các nhà cung cấp thiết bị cơ khí tại Việt Nam. Chia sẻ thông tin thị trường, cơ hội hợp tác, và xu hướng ngành.',
                'justification' => 'Việt Nam có nhiều nhà cung cấp thiết bị cơ khí chất lượng nhưng thiếu kết nối. Nhóm này sẽ tạo cầu nối hiệu quả.',
                'expected_members' => 50,
                'creator_id' => $users[3]->id,
                'status' => GroupRequestStatus::UNDER_REVIEW,
                'requested_at' => now()->subDays(1),
                'reviewed_at' => now(),
                'reviewed_by' => 1,
            ],
            [
                'conversation_type_id' => $technicalType->id,
                'title' => 'Thảo luận về Industry 4.0',
                'description' => 'Nhóm thảo luận về công nghệ Industry 4.0: IoT, AI trong sản xuất, smart factory, digital twin, và tự động hóa thông minh.',
                'justification' => 'Industry 4.0 đang thay đổi ngành cơ khí. Cần có nhóm chuyên biệt để thảo luận và cập nhật xu hướng mới.',
                'expected_members' => 30,
                'creator_id' => $users[4]->id,
                'status' => GroupRequestStatus::REJECTED,
                'requested_at' => now()->subDays(7),
                'reviewed_at' => now()->subDays(5),
                'reviewed_by' => 1,
                'rejection_reason' => 'Chủ đề quá rộng và chưa có kế hoạch cụ thể. Đề xuất chia thành các nhóm nhỏ hơn theo từng công nghệ.',
            ],
        ];

        foreach ($sampleRequests as $request) {
            GroupRequest::create($request);
        }

        $this->command->info('✅ Created ' . count($sampleRequests) . ' sample group requests');
    }
}
