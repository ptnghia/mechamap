<?php

namespace Database\Seeders;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\User;
use App\Models\Thread;
use Illuminate\Database\Seeder;

class PollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $threads = Thread::all();

        if ($users->count() === 0 || $threads->count() === 0) {
            return;
        }

        // Tạo polls về chủ đề cơ khí và tự động hóa
        $pollsData = [
            [
                'question' => 'PLC brand nào bạn thích sử dụng nhất?',
                'options' => ['Siemens', 'Allen-Bradley', 'Mitsubishi', 'Omron', 'Schneider Electric'],
                'expires_at' => now()->addDays(30),
            ],
            [
                'question' => 'Loại robot công nghiệp nào phổ biến nhất tại Việt Nam?',
                'options' => ['ABB', 'KUKA', 'Fanuc', 'Kawasaki', 'Universal Robots'],
                'expires_at' => now()->addDays(45),
            ],
            [
                'question' => 'CAD software nào bạn sử dụng chủ yếu?',
                'options' => ['SolidWorks', 'AutoCAD', 'Inventor', 'Fusion 360', 'CATIA'],
                'expires_at' => now()->addDays(20),
            ],
            [
                'question' => 'Thách thức lớn nhất trong triển khai Industry 4.0?',
                'options' => ['Chi phí đầu tư', 'Thiếu nhân lực', 'Kết nối hệ thống cũ', 'Bảo mật dữ liệu', 'Thay đổi quy trình'],
                'expires_at' => now()->addDays(60),
            ],
            [
                'question' => 'Sensor loại nào được sử dụng nhiều nhất trong automation?',
                'options' => ['Proximity sensor', 'Photo sensor', 'Pressure sensor', 'Temperature sensor', 'Flow sensor'],
                'expires_at' => now()->addDays(25),
            ],
            [
                'question' => 'Ngôn ngữ lập trình PLC nào bạn thành thạo nhất?',
                'options' => ['Ladder Logic', 'Function Block', 'Structured Text', 'Sequential Function Chart', 'Instruction List'],
                'expires_at' => now()->addDays(35),
            ],
            [
                'question' => 'Loại motor nào phù hợp cho ứng dụng positioning chính xác?',
                'options' => ['Servo motor', 'Stepper motor', 'AC motor + encoder', 'Linear motor', 'Brushless DC motor'],
                'expires_at' => now()->addDays(40),
            ],
            [
                'question' => 'HMI size nào được sử dụng phổ biến trong công nghiệp?',
                'options' => ['7 inch', '10 inch', '15 inch', '21 inch', 'Tablet/Mobile'],
                'expires_at' => now()->addDays(30),
            ],
            [
                'question' => 'Communication protocol nào bạn ưu tiên cho Industrial network?',
                'options' => ['Profinet', 'EtherNet/IP', 'Modbus TCP', 'CC-Link IE', 'EtherCAT'],
                'expires_at' => now()->addDays(50),
            ],
            [
                'question' => 'Phần mềm SCADA nào có hiệu suất tốt nhất?',
                'options' => ['WinCC', 'FactoryTalk View', 'Wonderware', 'Citect', 'Ignition'],
                'expires_at' => now()->addDays(28),
            ],
        ];

        foreach ($pollsData as $index => $pollData) {
            // Chọn thread ngẫu nhiên
            $thread = $threads->random();

            // Tạo poll
            $poll = Poll::create([
                'thread_id' => $thread->id,
                'question' => $pollData['question'],
                'close_at' => $pollData['expires_at'],
                'max_options' => rand(0, 100) < 20 ? rand(2, 3) : 1, // 20% chance multiple choice
                'allow_change_vote' => rand(0, 100) < 70,
                'show_votes_publicly' => rand(0, 100) < 80,
                'allow_view_without_vote' => rand(0, 100) < 90,
                'created_at' => now()->subDays(rand(1, 10)),
            ]);

            // Tạo poll options
            foreach ($pollData['options'] as $optionText) {
                PollOption::create([
                    'poll_id' => $poll->id,
                    'text' => $optionText,
                ]);
            }

            // Tạo votes từ users
            $pollOptions = $poll->options;
            $votersCount = rand(5, min(25, $users->count()));
            $voters = $users->random($votersCount);

            foreach ($voters as $voter) {
                if ($poll->max_options > 1) {
                    // Multiple choice: vote cho 1-max_options
                    $numChoices = rand(1, min($poll->max_options, $pollOptions->count()));
                    $chosenOptions = $pollOptions->random($numChoices);

                    foreach ($chosenOptions as $option) {
                        PollVote::firstOrCreate([
                            'poll_id' => $poll->id,
                            'poll_option_id' => $option->id,
                            'user_id' => $voter->id,
                        ], [
                            'created_at' => now()->subDays(rand(0, 8)),
                        ]);
                    }
                } else {
                    // Single choice: vote cho 1 option
                    $chosenOption = $pollOptions->random();
                    PollVote::firstOrCreate([
                        'poll_id' => $poll->id,
                        'poll_option_id' => $chosenOption->id,
                        'user_id' => $voter->id,
                    ], [
                        'created_at' => now()->subDays(rand(0, 8)),
                    ]);
                }
            }

            // Note: Vote counts are calculated dynamically through relationships
        }
    }
}
