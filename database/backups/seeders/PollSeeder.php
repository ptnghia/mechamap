<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PollSeeder extends Seeder
{
    /**
     * Seed polls với câu hỏi chuyên ngành cơ khí
     * Tạo polls thực tế cho community discussions
     */
    public function run(): void
    {
        $this->command->info('📊 Bắt đầu seed polls với nội dung chuyên ngành...');

        // Lấy dữ liệu cần thiết
        $threads = Thread::all();
        $users = User::all();

        if ($threads->isEmpty() || $users->isEmpty()) {
            $this->command->error('❌ Cần có threads và users trước khi seed polls!');
            return;
        }

        // Tạo polls cho một số threads
        $this->createPolls($threads, $users);

        $this->command->info('✅ Hoàn thành seed polls!');
    }

    private function createPolls($threads, $users): void
    {
        $pollData = $this->getPollQuestions();

        // Chọn random 8-12 threads để tạo polls
        $selectedThreads = $threads->random(min(10, $threads->count()));

        foreach ($selectedThreads as $index => $thread) {
            if ($index >= count($pollData)) break;

            $pollInfo = $pollData[$index];

            // Tạo poll
            $poll = Poll::create([
                'thread_id' => $thread->id,
                'question' => $pollInfo['question'],
                'max_options' => $pollInfo['max_options'],
                'allow_change_vote' => $pollInfo['allow_change_vote'],
                'show_votes_publicly' => $pollInfo['show_votes_publicly'],
                'allow_view_without_vote' => $pollInfo['allow_view_without_vote'],
                'close_at' => $pollInfo['close_at'] ? now()->addDays($pollInfo['close_at']) : null,
                'created_at' => $thread->created_at->addHours(rand(1, 24)),
                'updated_at' => now(),
            ]);

            // Tạo poll options
            foreach ($pollInfo['options'] as $optionText) {
                PollOption::create([
                    'poll_id' => $poll->id,
                    'text' => $optionText,
                ]);
            }

            // Tạo votes từ users
            $this->createPollVotes($poll, $users);

            $this->command->line("   📊 Tạo poll: {$poll->question}");
        }
    }

    private function createPollVotes($poll, $users): void
    {
        $options = $poll->options;
        if ($options->isEmpty()) return;

        // 30-70% users vote trong poll này
        $votePercentage = rand(30, 70);
        $voterCount = ceil($users->count() * $votePercentage / 100);

        $voters = $users->random($voterCount);

        foreach ($voters as $voter) {
            // Random chọn option(s) dựa vào max_options
            if ($poll->max_options == 1) {
                $selectedOptions = [$options->random()];
            } else {
                $selectedOptions = $options->random(min($poll->max_options, $options->count()));
                if (!is_array($selectedOptions) && !$selectedOptions instanceof \Illuminate\Support\Collection) {
                    $selectedOptions = [$selectedOptions];
                }
            }

            foreach ($selectedOptions as $option) {
                PollVote::create([
                    'poll_id' => $poll->id,
                    'poll_option_id' => $option->id,
                    'user_id' => $voter->id,
                    'created_at' => $poll->created_at->addDays(rand(0, 7)),
                    'updated_at' => now(),
                ]);
            }
        }

        // Vote counts sẽ được tính dynamic từ poll_votes table
    }

    private function getPollQuestions(): array
    {
        return [
            [
                'question' => 'Phần mềm CAD nào bạn sử dụng chính trong công việc?',
                'options' => [
                    'SolidWorks',
                    'AutoCAD',
                    'Inventor',
                    'Fusion 360',
                    'CATIA',
                    'Creo/Pro-E',
                    'Khác'
                ],
                'max_options' => 2,
                'allow_change_vote' => true,
                'show_votes_publicly' => true,
                'allow_view_without_vote' => true,
                'close_at' => null
            ],
            [
                'question' => 'Thách thức lớn nhất khi thiết kế sản phẩm mới?',
                'options' => [
                    'Yêu cầu kỹ thuật phức tạp',
                    'Hạn chế về ngân sách',
                    'Thời gian giao hàng gấp',
                    'Thiếu kinh nghiệm với công nghệ mới',
                    'Khó khăn trong manufacturing',
                    'Compliance với standards'
                ],
                'max_options' => 1,
                'allow_change_vote' => true,
                'show_votes_publicly' => true,
                'allow_view_without_vote' => false,
                'close_at' => 30
            ],
            [
                'question' => 'Phương pháp FEA analysis nào bạn thường dùng?',
                'options' => [
                    'Static Structural',
                    'Modal Analysis',
                    'Thermal Analysis',
                    'CFD',
                    'Fatigue Analysis',
                    'Không sử dụng FEA'
                ],
                'max_options' => 3,
                'allow_change_vote' => true,
                'show_votes_publicly' => true,
                'allow_view_without_vote' => true,
                'close_at' => null
            ],
            [
                'question' => 'Vật liệu nào bạn làm việc nhiều nhất?',
                'options' => [
                    'Steel (Carbon/Alloy)',
                    'Stainless Steel',
                    'Aluminum Alloys',
                    'Plastics/Polymers',
                    'Composites',
                    'Cast Iron',
                    'Titanium'
                ],
                'max_options' => 2,
                'allow_change_vote' => true,
                'show_votes_publicly' => true,
                'allow_view_without_vote' => true,
                'close_at' => null
            ],
            [
                'question' => 'Quy trình manufacturing nào bạn có kinh nghiệm?',
                'options' => [
                    'CNC Machining',
                    'Welding/Fabrication',
                    '3D Printing',
                    'Casting',
                    'Injection Molding',
                    'Sheet Metal',
                    'Assembly'
                ],
                'max_options' => 4,
                'allow_change_vote' => true,
                'show_votes_publicly' => true,
                'allow_view_without_vote' => true,
                'close_at' => null
            ],
            [
                'question' => 'Industry 4.0 technology nào quan trọng nhất?',
                'options' => [
                    'IoT Sensors',
                    'AI/Machine Learning',
                    'Digital Twin',
                    'Robotics/Automation',
                    'Additive Manufacturing',
                    'Augmented Reality',
                    'Blockchain'
                ],
                'max_options' => 1,
                'allow_change_vote' => false,
                'show_votes_publicly' => true,
                'allow_view_without_vote' => false,
                'close_at' => 14
            ],
            [
                'question' => 'Ngành công nghiệp nào bạn đang làm việc?',
                'options' => [
                    'Automotive',
                    'Aerospace',
                    'Manufacturing/Industrial',
                    'Energy/Oil & Gas',
                    'Construction',
                    'Electronics',
                    'Medical Devices',
                    'Consumer Products'
                ],
                'max_options' => 1,
                'allow_change_vote' => true,
                'show_votes_publicly' => true,
                'allow_view_without_vote' => true,
                'close_at' => null
            ],
            [
                'question' => 'Certification nào quan trọng nhất cho career?',
                'options' => [
                    'Professional Engineer (PE)',
                    'SolidWorks Certification',
                    'AutoCAD Certification',
                    'PMP (Project Management)',
                    'Six Sigma',
                    'ISO 9001 Lead Auditor',
                    'Không cần certification'
                ],
                'max_options' => 2,
                'allow_change_vote' => true,
                'show_votes_publicly' => true,
                'allow_view_without_vote' => true,
                'close_at' => null
            ],
            [
                'question' => 'Xu hướng công nghệ nào sẽ thay đổi ngành cơ khí?',
                'options' => [
                    'Electric Vehicles',
                    'Renewable Energy',
                    'Advanced Materials',
                    'Automation/Robotics',
                    'Sustainable Manufacturing',
                    'Space Technology',
                    'Biomedical Engineering'
                ],
                'max_options' => 1,
                'allow_change_vote' => true,
                'show_votes_publicly' => true,
                'allow_view_without_vote' => false,
                'close_at' => 21
            ],
            [
                'question' => 'Skill nào cần improve nhất trong 2024?',
                'options' => [
                    'Programming/Automation',
                    'Advanced CAD/CAM',
                    'Data Analysis',
                    'Project Management',
                    'Sustainability/Green Tech',
                    'AI/Machine Learning',
                    'Communication Skills'
                ],
                'max_options' => 2,
                'allow_change_vote' => true,
                'show_votes_publicly' => true,
                'allow_view_without_vote' => true,
                'close_at' => null
            ]
        ];
    }
}
