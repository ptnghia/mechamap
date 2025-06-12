<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'admin')->get();
        $faqCategories = FaqCategory::all();

        if ($users->count() === 0) {
            return;
        }

        $admin = $users->first();

        // Tạo FAQs về cơ khí và tự động hóa
        $faqsData = [
            // PLC Programming
            [
                'question' => 'PLC là gì và tại sao nó quan trọng trong tự động hóa?',
                'answer' => 'PLC (Programmable Logic Controller) là thiết bị điều khiển có thể lập trình được, thay thế các relay truyền thống trong hệ thống điều khiển. PLC quan trọng vì:

• Tính linh hoạt cao: Có thể thay đổi logic điều khiển bằng phần mềm
• Độ tin cậy cao: Thiết kế để hoạt động trong môi trường công nghiệp khắc nghiệt
• Khả năng mở rộng: Dễ dàng thêm I/O modules
• Chẩn đoán lỗi: Có khả năng tự chẩn đoán và báo lỗi
• Tiết kiệm chi phí: Giảm thời gian commissioning và maintenance',
                'category' => 'PLC Programming',
            ],
            [
                'question' => 'Sự khác biệt giữa Ladder Logic và Function Block Diagram?',
                'answer' => 'Ladder Logic (LAD) và Function Block Diagram (FBD) là hai ngôn ngữ lập trình PLC phổ biến:

**Ladder Logic:**
• Dựa trên sơ đồ relay truyền thống
• Dễ hiểu cho kỹ sư điện
• Phù hợp cho logic Boolean đơn giản
• Debugging dễ dàng

**Function Block Diagram:**
• Dựa trên các function blocks
• Phù hợp cho xử lý analog và toán học phức tạp
• Tái sử dụng code hiệu quả
• Cấu trúc rõ ràng cho hệ thống lớn

Chọn ngôn ngữ tùy thuộc vào ứng dụng và sở thích của programmer.',
                'category' => 'PLC Programming',
            ],
            [
                'question' => 'Làm thế nào để tối ưu hóa scan time của PLC?',
                'answer' => 'Để tối ưu hóa scan time của PLC, áp dụng các kỹ thuật sau:

**Tối ưu code:**
• Sử dụng local variables thay vì global khi có thể
• Tránh nested loops sâu
• Sử dụng interrupts cho các task real-time
• Optimize instruction sequence

**Cấu trúc chương trình:**
• Chia nhỏ program thành các FC/FB
• Sử dụng conditional execution
• Prioritize critical functions
• Minimize data movement

**Hardware optimization:**
• Chọn CPU có tốc độ xử lý phù hợp
• Sử dụng local I/O modules
• Optimize communication protocols
• Regular maintenance và updates',
                'category' => 'PLC Programming',
            ],

            // Robot Programming
            [
                'question' => 'Các loại robot công nghiệp phổ biến và ứng dụng?',
                'answer' => 'Các loại robot công nghiệp phổ biến:

**1. Articulated Robot (6-DOF):**
• Ứng dụng: Welding, painting, assembly
• Ưu điểm: Flexibility cao, workspace rộng
• Nhược điểm: Phức tạp, giá cao

**2. SCARA Robot (4-DOF):**
• Ứng dụng: Pick & place, assembly
• Ưu điểm: Tốc độ cao, precision tốt
• Nhược điểm: Workspace hạn chế

**3. Delta Robot (3-4 DOF):**
• Ứng dụng: High-speed packaging, sorting
• Ưu điểm: Tốc độ cực cao, lightweight
• Nhược điểm: Payload thấp

**4. Cartesian Robot:**
• Ứng dụng: CNC loading, 3D printing
• Ưu điểm: Precision cao, programming đơn giản
• Nhược điểm: Kích thước lớn',
                'category' => 'Robotics',
            ],
            [
                'question' => 'Path planning và trajectory optimization cho robot?',
                'answer' => 'Path planning và trajectory optimization là key factors cho hiệu suất robot:

**Path Planning Methods:**
• Point-to-point: Đơn giản, nhanh
• Linear interpolation: Đường thẳng
• Circular interpolation: Đường cong
• Spline interpolation: Smooth motion

**Trajectory Optimization:**
• Minimize cycle time
• Smooth acceleration/deceleration
• Avoid obstacles và singularities
• Optimize joint movements

**Implementation Steps:**
1. Define waypoints
2. Choose interpolation method
3. Set velocity và acceleration limits
4. Simulate và verify
5. Optimize parameters
6. Test với actual robot

**Tools:**
• RobotStudio (ABB)
• KUKA.Sim
• RoboGuide (Fanuc)
• MoveIt (ROS)',
                'category' => 'Robotics',
            ],

            // CNC Machining
            [
                'question' => 'G-code cơ bản cho lập trình CNC?',
                'answer' => 'G-code là ngôn ngữ lập trình chuẩn cho máy CNC:

**G-codes chính:**
• G00: Rapid positioning
• G01: Linear interpolation
• G02: Clockwise circular interpolation
• G03: Counter-clockwise circular interpolation
• G28: Return to home position
• G90: Absolute programming
• G91: Incremental programming

**M-codes quan trọng:**
• M03: Spindle on (clockwise)
• M04: Spindle on (counter-clockwise)
• M05: Spindle stop
• M06: Tool change
• M08: Coolant on
• M09: Coolant off
• M30: Program end

**Example Program:**
```
N10 G90 G00 X0 Y0 Z10
N20 M03 S1000
N30 G01 Z-2 F100
N40 G01 X50 Y50 F500
N50 G00 Z10
N60 M05
N70 M30
```',
                'category' => 'CNC Machining',
            ],
            [
                'question' => 'Cách chọn cutting parameters cho CNC machining?',
                'answer' => 'Cutting parameters ảnh hưởng trực tiếp đến chất lượng và hiệu suất gia công:

**Spindle Speed (RPM):**
• Formula: N = (V × 1000) / (π × D)
• V: Cutting speed (m/min)
• D: Tool diameter (mm)

**Feed Rate:**
• Feed per tooth: fz (mm/tooth)
• Feed per revolution: f = fz × Z
• Feed rate: F = f × N (mm/min)

**Depth of Cut:**
• Axial depth: ap (mm)
• Radial depth: ae (mm)
• Rule: ap × ae ≤ Tool capability

**Material Considerations:**
• Steel: V = 100-200 m/min
• Aluminum: V = 300-800 m/min
• Stainless steel: V = 80-150 m/min
• Titanium: V = 50-120 m/min

**Optimization Tips:**
• Start conservative, then optimize
• Monitor tool wear
• Use coolant effectively
• Consider workpiece stability',
                'category' => 'CNC Machining',
            ],

            // Sensor Technology
            [
                'question' => 'Các loại sensor phổ biến trong automation?',
                'answer' => 'Sensors là "mắt và tai" của hệ thống automation:

**1. Proximity Sensors:**
• Inductive: Detect metal objects
• Capacitive: Detect any material
• Ultrasonic: Distance measurement
• Photoelectric: Light-based detection

**2. Pressure Sensors:**
• Gauge pressure: Relative to atmosphere
• Absolute pressure: Relative to vacuum
• Differential pressure: Between two points
• Applications: Hydraulic, pneumatic systems

**3. Temperature Sensors:**
• Thermocouple: Wide range, robust
• RTD (PT100): High accuracy
• Thermistor: Sensitive, limited range
• Infrared: Non-contact measurement

**4. Flow Sensors:**
• Magnetic: For conductive liquids
• Ultrasonic: Non-invasive
• Turbine: Mechanical, accurate
• Differential pressure: Orifice plates

**5. Level Sensors:**
• Float switch: Simple, reliable
• Ultrasonic: Non-contact
• Radar: Harsh environments
• Pressure transmitter: Hydrostatic',
                'category' => 'Sensors',
            ],

            // Safety
            [
                'question' => 'Tiêu chuẩn an toàn ISO 13849 trong automation?',
                'answer' => 'ISO 13849 quy định về safety của machinery control systems:

**Performance Levels (PL):**
• PLa: Very low risk
• PLb: Low risk
• PLc: Medium risk
• PLd: High risk
• PLe: Very high risk

**Categories:**
• Category B: Basic safety
• Category 1: Well-tried components
• Category 2: Self-monitoring
• Category 3: Single fault tolerance
• Category 4: Single fault tolerance + detection

**Safety Functions:**
• Emergency stop
• Safety guards
• Two-hand control
• Light curtains
• Safety mats

**Implementation Steps:**
1. Risk assessment
2. Determine required PL
3. Design safety system
4. Calculate achieved PL
5. Validate và verify
6. Documentation

**Safety Devices:**
• Safety relays
• Safety PLCs
• Safety light curtains
• Safety laser scanners',
                'category' => 'Safety',
            ],

            // Maintenance
            [
                'question' => 'Predictive maintenance với IoT sensors?',
                'answer' => 'Predictive maintenance sử dụng IoT để dự đoán failures:

**Monitoring Parameters:**
• Vibration: Bearing wear, misalignment
• Temperature: Overheating, friction
• Current signature: Motor condition
• Oil analysis: Contamination, wear particles
• Ultrasonic: Leak detection, bearing condition

**IoT Architecture:**
• Sensors → Edge gateway → Cloud platform
• Real-time monitoring
• Data analytics và ML
• Alert systems
• Mobile dashboards

**Benefits:**
• 30-50% reduction in maintenance costs
• 70-90% reduction in breakdowns
• 20-25% increase in equipment availability
• Optimized spare parts inventory

**Implementation:**
1. Identify critical equipment
2. Install appropriate sensors
3. Set up data collection
4. Develop baseline conditions
5. Train ML models
6. Deploy alert systems
7. Continuous improvement

**Technologies:**
• Wireless sensor networks
• Edge computing
• Machine learning
• Digital twins',
                'category' => 'Maintenance',
            ],
        ];

        foreach ($faqsData as $faqData) {
            $category = $faqCategories->where('name', $faqData['category'])->first();

            Faq::create([
                'category_id' => $category ? $category->id : $faqCategories->first()?->id,
                'question' => $faqData['question'],
                'answer' => $faqData['answer'],
                'is_active' => true,
                'order' => 0,
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);
        }
    }
}
