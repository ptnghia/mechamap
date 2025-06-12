<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Models\Thread;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $threads = Thread::all();

        if ($threads->count() === 0 || $users->count() === 0) {
            return;
        }

        // Nội dung posts về cơ khí và tự động hóa
        $mechanicalPosts = [
            [
                'content' => 'Tôi vừa hoàn thành việc lập trình PLC S7-1500 cho dây chuyền sản xuất bánh kẹo tự động. Hệ thống bao gồm 15 stations với các sensor proximity, load cell, và servo motor. Thời gian cycle time đạt 2.5 giây/sản phẩm.

Một số điểm quan trọng khi thiết kế:
- Sử dụng Profinet để kết nối các I/O distributed
- Tích hợp HMI Comfort Panel 15" cho operator
- Backup data qua MicroSD card
- Safety function với F-CPU

Ai có kinh nghiệm với Siemens TIA Portal không? Muốn trao đổi về optimization.',
            ],
            [
                'content' => '@cnc_master Cảm ơn anh đã chia sẻ! Tôi cũng đang gặp vấn đề tương tự với máy CNC Mazak.

Theo kinh nghiệm của tôi, lỗi này thường do:
1. Tool wear compensation chưa chính xác
2. Backlash của ball screw
3. Thermal expansion của spindle

Tôi đã fix bằng cách:
- Cài đặt lại tool offset table
- Check và adjust backlash compensation
- Sử dụng coolant circulation liên tục

Kết quả: độ chính xác từ ±0.05mm cải thiện xuống ±0.02mm. Hy vọng có ích cho anh!',
            ],
            [
                'content' => 'Update dự án robot hàn: Đã hoàn thành commissioning robot KUKA KR 16-2 cho dây chuyền hàn khung xe máy.

Thông số kỹ thuật:
- Payload: 16kg
- Reach: 1611mm
- Repeatability: ±0.05mm
- Welding gun: Binzel ABICOR
- Weld seam tracking: laser sensor

Challenges đã gặp:
❌ Path planning phức tạp do nhiều seam
❌ Collision detection với fixture
❌ Spatter cleaning tự động

Solutions applied:
✅ Offline programming với RobotStudio
✅ Dynamic collision avoidance
✅ Pneumatic cleaning station

ROI: 40% tăng productivity, giảm 60% defect rate.',
            ],
            [
                'content' => 'Vấn đề với hệ thống băng tải: Băng tải PVC bị trượt liên tục tại góc cong 90°. Load = 50kg/m, speed = 1.2m/s.

Đã thử:
- Tăng tension roller
- Thay belt PVC thành modular plastic
- Adjust drive sprocket

Vẫn không khắc phục được. Có ai gặp case này chưa? Cần advice urgently!

#ConveyorProblem #MaterialHandling #Help',
            ],
            [
                'content' => '@conveyor_specialist Tôi từng xử lý vấn đề tương tự ở nhà máy dệt.

Root cause: coefficient of friction không đủ tại curved section.

Solution đã áp dụng:
1. Lắp thêm guide rails bên trong curve
2. Giảm speed xuống 0.8m/s tại curve section
3. Sử dụng belt surface có texture
4. Thêm hold-down roller tại exit curve

Kết quả: zero slippage trong 6 tháng vận hành.

Chi phí: ~15 triệu VND cho modification. Worth it!',
            ],
            [
                'content' => 'Chia sẻ kinh nghiệm setup servo motor Mitsubishi HG-SR cho ứng dụng pick&place:

Parameter quan trọng:
- PA01 (Control mode): Position control
- PA04 (Position gain): 150 rad/s
- PA05 (Speed gain): 2000 Hz
- PA06 (Integral time): 10 ms
- PA13 (Acceleration time): 100 ms

Tuning tips:
🔧 Bắt đầu với gain thấp, tăng dần
🔧 Monitor oscillation bằng MR Configurator
🔧 Adjust theo actual load inertia
🔧 Enable vibration suppression nếu cần

Kết quả: positioning accuracy ±0.01mm, settling time <50ms.',
            ],
            [
                'content' => 'Dự án Industry 4.0: Triển khai IoT sensors cho predictive maintenance tại nhà máy thép.

Deployed sensors:
📊 Temperature: PT100 RTD
📊 Vibration: Accelerometer ADXL355
📊 Current: Hall effect CT
📊 Oil analysis: Particle counter

Data platform:
- Edge gateway: Advantech UNO-2484G
- Cloud: Microsoft Azure IoT Hub
- Analytics: Power BI + ML models
- Alerts: SMS + Email + Mobile app

ROI sau 1 năm:
💰 Giảm 45% unplanned downtime
💰 Tăng 25% OEE
💰 Tiết kiệm 30% maintenance cost

Lesson learned: Data quality là yếu tố quan trọng nhất!',
            ],
            [
                'content' => 'Cần tư vấn về selection motor cho application sau:
- Load: 500kg
- Speed: 2000 RPM
- Duty cycle: Continuous
- Environment: IP65
- Control: Variable frequency

Đang cân nhắc giữa:
1. ABB M3BP motor + ACS580 VFD
2. Siemens 1LE1 + G120C VFD
3. WEG W22 + CFW11 VFD

Budget: ~50 triệu VND. Ai có experience với 3 brands này?',
            ],
            [
                'content' => '@motor_specialist Tôi recommend option 2: Siemens 1LE1 + G120C.

Lý do:
✅ Reliability cao (MTBF > 40,000h)
✅ Efficiency IE3 standard
✅ G120C có built-in safety functions
✅ Support network: Profinet, Modbus
✅ Easy commissioning với STARTER software
✅ Local service network tốt ở VN

Đã deploy >100 sets trong 3 năm, failure rate <2%.

Alternative: nếu budget tight thì WEG cũng OK, nhưng spare parts hơi khó.',
            ],
            [
                'content' => 'CAD tip: Modeling complex cam mechanism trong SolidWorks.

Workflow tôi thường dùng:
1. Sketch cam profile theo motion requirement
2. Use Curve Driven Pattern for follower path
3. Apply Motion Study để verify kinematics
4. Check interference với Collision Detection
5. Generate manufacturing drawing với GD&T

Advanced features:
🎯 Equation-driven curves cho smooth motion
🎯 Design tables cho multiple cam sizes
🎯 Simulation với realistic materials
🎯 Stress analysis với FEA add-in

Output: DXF for CNC, STEP for CAM software.

Ai cần file template không? Share được!',
            ],
            [
                'content' => 'Hydraulic system troubleshooting: Máy ép 200 tấn bị drop pressure không rõ nguyên nhân.

Symptoms:
❌ Pressure drop từ 200 bar xuống 150 bar trong 30 giây
❌ Pump running continuously
❌ Oil temperature tăng lên 65°C
❌ Cycle time tăng từ 45s lên 70s

Checked:
✅ Main relief valve - OK
✅ Hydraulic oil level - OK
✅ Filter element - Replaced
✅ Seals cylinder - No visible leak

Next steps to check?',
            ],
            [
                'content' => '@hydraulic_expert Internal leakage! 90% chắc chắn.

Diagnostic procedure:
1. Pressure test từng circuit riêng biệt
2. Check pump flow rate với flow meter
3. Isolate cylinder và test holding pressure
4. Inspect directional valve spool

Most likely:
- Pump internal wear (vane/piston)
- Directional valve cross leakage
- Cylinder internal seal failure

Quick test: disconnect load, run pump no-load. Nếu pressure stable thì chắc chắn cylinder problem.

Temporary fix: reduce cycle speed, add cooling.',
            ],
            [
                'content' => 'Automation project showcase: Smart Warehouse với AGV navigation.

System overview:
🤖 6x AGV Omron LD-90
🤖 WMS integration với SAP
🤖 RFID tracking cho inventory
🤖 QR code navigation
🤖 Automatic charging stations

Technical highlights:
- Fleet manager software
- Traffic control system
- Safety laser scanners
- Wi-Fi 6 infrastructure
- Real-time dashboard

Performance:
📈 99.5% uptime
📈 150% throughput increase
📈 85% labor cost reduction
📈 ROI breakeven: 18 months

Challenges: Integration với legacy WMS, Wi-Fi dead zones.',
            ],
            [
                'content' => 'PLC programming best practices từ 10 năm kinh nghiệm:

Code structure:
📝 Main OB1: Chỉ call FC/FB
📝 Alarms: Riêng biệt trong FC_Alarms
📝 HMI: Data blocks structured
📝 Safety: Isolated F-blocks
📝 Comments: Tiếng Việt + English

Naming convention:
- I/O: M001_Motor_Start
- Internal: MW_Process_Step
- Timers: T_Conveyor_Delay
- Counters: C_Production_Count

Testing approach:
🧪 Simulation trước khi download
🧪 Step-by-step commissioning
🧪 Fault injection testing
🧪 Documentation update

Result: 90% reduction in commissioning time!',
            ],
            [
                'content' => 'Sensor selection cho harsh environment (foundry):

Requirements:
🌡️ Temperature: -20°C to +85°C
🌡️ Vibration: 5G acceleration
🌡️ EMI resistance: High
🌡️ IP rating: IP67 minimum
🌡️ MTBF: >50,000 hours

Tested sensors:
1. Sick DTM60-A1A03: ✅ Excellent performance
2. Pepperl+Fuchs NBN40: ✅ Good but pricey
3. Omron E2E-X14MD1: ❌ Failed after 3 months
4. Schneider XS630B1PAL2: ✅ Decent budget option

Winner: Sick DTM60 - zero failures in 2 years operation.

Investment: 15% higher cost, but saved 80% replacement time.',
            ]
        ];

        // Tạo posts cho mỗi thread
        foreach ($threads as $thread) {
            $numPosts = rand(2, 8); // Mỗi thread có 2-8 posts

            for ($i = 0; $i < $numPosts; $i++) {
                $postData = $mechanicalPosts[array_rand($mechanicalPosts)];
                $user = $users->random();

                Post::create([
                    'thread_id' => $thread->id,
                    'user_id' => $user->id,
                    'content' => $postData['content'],
                    'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                    'updated_at' => now()->subDays(rand(0, 15))->subHours(rand(0, 23)),
                ]);
            }
        }
    }
}
