<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'admin')->get();
        $pageCategories = PageCategory::all();

        if ($users->count() === 0) {
            return;
        }

        $admin = $users->first();

        // Tạo các pages về cơ khí và tự động hóa
        $pagesData = [
            [
                'title' => 'Hướng Dẫn Lập Trình PLC Siemens',
                'slug' => 'huong-dan-lap-trinh-plc-siemens',
                'content' => '
<h2>Giới thiệu về PLC Siemens</h2>
<p>PLC (Programmable Logic Controller) Siemens là một trong những dòng PLC phổ biến nhất trong công nghiệp. Với TIA Portal (Totally Integrated Automation Portal), việc lập trình và cấu hình trở nên dễ dàng hơn bao giờ hết.</p>

<h3>Các dòng PLC Siemens phổ biến:</h3>
<ul>
<li><strong>S7-1200:</strong> Dành cho ứng dụng nhỏ và vừa</li>
<li><strong>S7-1500:</strong> Dành cho ứng dụng hiệu suất cao</li>
<li><strong>S7-300/400:</strong> Dòng cũ nhưng vẫn được sử dụng rộng rãi</li>
</ul>

<h3>Ngôn ngữ lập trình:</h3>
<ol>
<li><strong>LAD (Ladder Logic):</strong> Dễ hiểu, phù hợp người mới</li>
<li><strong>FBD (Function Block Diagram):</strong> Trực quan, logic rõ ràng</li>
<li><strong>STL (Statement List):</strong> Hiệu quả, phù hợp chuyên gia</li>
<li><strong>SCL (Structured Control Language):</strong> Giống Pascal, mạnh mẽ</li>
</ol>

<h3>Cấu trúc chương trình cơ bản:</h3>
<pre><code>
OB1 (Main Program)
├── FC1 (Function: Input Processing)
├── FB1 (Function Block: Motor Control)
├── FC2 (Function: Output Processing)
└── FC99 (Function: Alarm Handling)
</code></pre>

<p>Để bắt đầu, hãy tải TIA Portal và tạo project đầu tiên của bạn!</p>
                ',
                'excerpt' => 'Hướng dẫn chi tiết về lập trình PLC Siemens từ cơ bản đến nâng cao, sử dụng TIA Portal.',
                'is_published' => true,
                'meta_title' => 'Hướng Dẫn Lập Trình PLC Siemens - TIA Portal',
                'meta_description' => 'Học lập trình PLC Siemens S7-1200, S7-1500 với TIA Portal. Hướng dẫn từ cơ bản đến nâng cao.',
            ],
            [
                'title' => 'Robot Công Nghiệp - Tổng Quan và Ứng Dụng',
                'slug' => 'robot-cong-nghiep-tong-quan-ung-dung',
                'content' => '
<h2>Robot Công Nghiệp trong Kỷ Nguyên 4.0</h2>
<p>Robot công nghiệp đang cách mạng hóa ngành sản xuất với khả năng tự động hóa cao, độ chính xác tuyệt đối và hiệu suất vượt trội.</p>

<h3>Các loại robot phổ biến:</h3>

<h4>1. Robot Cánh Tay Khớp (Articulated Robot)</h4>
<ul>
<li>6 bậc tự do (6-DOF)</li>
<li>Linh hoạt, phạm vi hoạt động rộng</li>
<li>Ứng dụng: Hàn, sơn, lắp ráp</li>
<li>Thương hiệu: ABB, KUKA, Fanuc</li>
</ul>

<h4>2. Robot Scara</h4>
<ul>
<li>4 bậc tự do</li>
<li>Tốc độ cao, chính xác</li>
<li>Ứng dụng: Pick & place, assembly</li>
<li>Thương hiệu: Omron, Yamaha, Denso</li>
</ul>

<h4>3. Robot Delta</h4>
<ul>
<li>3-4 bậc tự do</li>
<li>Tốc độ cực cao</li>
<li>Ứng dụng: Packaging, food processing</li>
<li>Thương hiệu: ABB, Fanuc, Stäubli</li>
</ul>

<h3>Ứng dụng trong công nghiệp:</h3>
<table border="1" style="width:100%; border-collapse: collapse;">
<tr>
<th>Ngành</th>
<th>Ứng dụng</th>
<th>Robot phù hợp</th>
</tr>
<tr>
<td>Ô tô</td>
<td>Hàn, sơn, lắp ráp</td>
<td>Articulated robot</td>
</tr>
<tr>
<td>Điện tử</td>
<td>Pick & place, test</td>
<td>Scara, Delta</td>
</tr>
<tr>
<td>Thực phẩm</td>
<td>Packaging, palletizing</td>
<td>Delta, Cartesian</td>
</tr>
</table>

<h3>Lợi ích của Robot:</h3>
<ul>
<li>✅ Tăng năng suất 200-300%</li>
<li>✅ Giảm 90% lỗi sản phẩm</li>
<li>✅ Hoạt động 24/7</li>
<li>✅ Cải thiện an toàn lao động</li>
<li>✅ ROI trong 18-24 tháng</li>
</ul>
                ',
                'excerpt' => 'Tổng quan về robot công nghiệp, phân loại, ứng dụng và lợi ích trong sản xuất hiện đại.',
                'is_published' => true,
                'meta_title' => 'Robot Công Nghiệp - Tổng Quan và Ứng Dụng',
                'meta_description' => 'Tìm hiểu về robot công nghiệp, phân loại ABB, KUKA, Fanuc và ứng dụng trong các ngành.',
            ],
            [
                'title' => 'Hệ Thống Băng Tải - Thiết Kế và Tính Toán',
                'slug' => 'he-thong-bang-tai-thiet-ke-tinh-toan',
                'content' => '
<h2>Hệ Thống Băng Tải Trong Công Nghiệp</h2>
<p>Băng tải là xương sống của hệ thống logistics và sản xuất hiện đại, đảm bảo vận chuyển nguyên liệu và sản phẩm một cách hiệu quả.</p>

<h3>Phân loại băng tải:</h3>

<h4>1. Băng Tải Phẳng (Flat Belt Conveyor)</h4>
<ul>
<li>Material: PVC, PU, Rubber</li>
<li>Tải trọng: 50-500 kg/m</li>
<li>Tốc độ: 0.1-3 m/s</li>
<li>Ứng dụng: Electronics, food, packaging</li>
</ul>

<h4>2. Băng Tải Modular</h4>
<ul>
<li>Material: Plastic modules</li>
<li>Ưu điểm: Easy maintenance, hygienic</li>
<li>Ứng dụng: Food processing, pharmaceutical</li>
</ul>

<h4>3. Roller Conveyor</h4>
<ul>
<li>Loại: Gravity, powered roller</li>
<li>Tải trọng: 100-1000 kg/item</li>
<li>Ứng dụng: Warehouse, distribution</li>
</ul>

<h3>Tính toán thiết kế:</h3>

<h4>Công suất động cơ:</h4>
<p><strong>P = (Q × L × f + Q × H) / (367 × η)</strong></p>
<p>Trong đó:</p>
<ul>
<li>P: Công suất (kW)</li>
<li>Q: Lưu lượng vật liệu (tấn/h)</li>
<li>L: Chiều dài băng tải (m)</li>
<li>H: Độ cao nâng (m)</li>
<li>f: Hệ số ma sát</li>
<li>η: Hiệu suất truyền động</li>
</ul>

<h4>Tension calculation:</h4>
<p><strong>T = T₁ + T₂ + T₃</strong></p>
<ul>
<li>T₁: Tension to overcome friction</li>
<li>T₂: Tension to lift material</li>
<li>T₃: Tension for acceleration</li>
</ul>

<h3>Các thành phần chính:</h3>
<ol>
<li><strong>Drive unit:</strong> Motor + Gearbox + Drum</li>
<li><strong>Belt:</strong> PVC, PU, Rubber, Steel cord</li>
<li><strong>Support structure:</strong> Frame + Idlers</li>
<li><strong>Control system:</strong> VFD + PLC + Sensors</li>
</ol>

<h3>Bảo trì định kỳ:</h3>
<table border="1" style="width:100%; border-collapse: collapse;">
<tr>
<th>Thành phần</th>
<th>Chu kỳ</th>
<th>Công việc</th>
</tr>
<tr>
<td>Motor</td>
<td>3 tháng</td>
<td>Check bearing, vibration</td>
</tr>
<tr>
<td>Belt</td>
<td>1 tháng</td>
<td>Check wear, tracking</td>
</tr>
<tr>
<td>Roller</td>
<td>6 tháng</td>
<td>Lubrication, alignment</td>
</tr>
</table>
                ',
                'excerpt' => 'Hướng dẫn thiết kế và tính toán hệ thống băng tải công nghiệp, từ lý thuyết đến thực tế.',
                'is_published' => true,
                'meta_title' => 'Hệ Thống Băng Tải - Thiết Kế và Tính Toán',
                'meta_description' => 'Học cách thiết kế và tính toán hệ thống băng tải công nghiệp, công thức tính toán chi tiết.',
            ],
            [
                'title' => 'Industry 4.0 và IoT trong Sản Xuất',
                'slug' => 'industry-4-0-iot-trong-san-xuat',
                'content' => '
<h2>Industry 4.0 - Cuộc Cách Mạng Công Nghiệp Lần Thứ 4</h2>
<p>Industry 4.0 tích hợp IoT, AI, Big Data và Automation để tạo ra nhà máy thông minh với khả năng tự động hóa và tối ưu hóa toàn diện.</p>

<h3>Công nghệ cốt lõi:</h3>

<h4>1. Internet of Things (IoT)</h4>
<ul>
<li>Sensor networks</li>
<li>Real-time monitoring</li>
<li>Edge computing</li>
<li>Protocols: MQTT, CoAP, LoRaWAN</li>
</ul>

<h4>2. Artificial Intelligence (AI)</h4>
<ul>
<li>Machine Learning algorithms</li>
<li>Predictive maintenance</li>
<li>Quality control automation</li>
<li>Process optimization</li>
</ul>

<h4>3. Digital Twin</h4>
<ul>
<li>Virtual factory model</li>
<li>Real-time synchronization</li>
<li>Simulation và testing</li>
<li>Performance optimization</li>
</ul>

<h3>Kiến trúc hệ thống IoT:</h3>
<pre><code>
Cloud Layer (AWS, Azure, GCP)
├── Data Analytics Platform
├── Machine Learning Models
├── Dashboard & Visualization
└── API Gateway

Edge Layer (Industrial Gateway)
├── Protocol Conversion
├── Data Preprocessing
├── Local Analytics
└── Security Gateway

Device Layer (Sensors & Actuators)
├── Temperature Sensors
├── Vibration Sensors
├── Pressure Transmitters
├── Flow Meters
├── Level Sensors
└── Actuators (Valves, Motors)
</code></pre>

<h3>Implementation roadmap:</h3>

<h4>Phase 1: Connectivity (3-6 tháng)</h4>
<ul>
<li>Install sensors và gateways</li>
<li>Establish network infrastructure</li>
<li>Basic data collection</li>
<li>Simple dashboards</li>
</ul>

<h4>Phase 2: Analytics (6-12 tháng)</h4>
<ul>
<li>Data warehouse setup</li>
<li>Real-time analytics</li>
<li>KPI monitoring</li>
<li>Alert systems</li>
</ul>

<h4>Phase 3: Intelligence (12-18 tháng)</h4>
<ul>
<li>Machine learning models</li>
<li>Predictive analytics</li>
<li>Automated decision making</li>
<li>Process optimization</li>
</ul>

<h3>Lợi ích đạt được:</h3>
<table border="1" style="width:100%; border-collapse: collapse;">
<tr>
<th>Metric</th>
<th>Trước</th>
<th>Sau</th>
<th>Cải thiện</th>
</tr>
<tr>
<td>OEE</td>
<td>65%</td>
<td>85%</td>
<td>+31%</td>
</tr>
<tr>
<td>Downtime</td>
<td>15%</td>
<td>5%</td>
<td>-67%</td>
</tr>
<tr>
<td>Maintenance Cost</td>
<td>100%</td>
<td>70%</td>
<td>-30%</td>
</tr>
<tr>
<td>Defect Rate</td>
<td>2%</td>
<td>0.5%</td>
<td>-75%</td>
</tr>
</table>

<h3>Challenges và Solutions:</h3>

<h4>❌ Challenges:</h4>
<ul>
<li>High initial investment</li>
<li>Legacy system integration</li>
<li>Cybersecurity risks</li>
<li>Skill gap</li>
</ul>

<h4>✅ Solutions:</h4>
<ul>
<li>Phased implementation approach</li>
<li>Use industrial IoT gateways</li>
<li>Implement security frameworks</li>
<li>Training và certification programs</li>
</ul>
                ',
                'excerpt' => 'Tìm hiểu về Industry 4.0, IoT và cách triển khai nhà máy thông minh trong thời đại số.',
                'is_published' => true,
                'meta_title' => 'Industry 4.0 và IoT trong Sản Xuất',
                'meta_description' => 'Hướng dẫn triển khai Industry 4.0, IoT, AI trong sản xuất. Kiến trúc hệ thống và roadmap.',
            ],
            [
                'title' => 'An Toàn Lao Động trong Tự Động Hóa',
                'slug' => 'an-toan-lao-dong-trong-tu-dong-hoa',
                'content' => '
<h2>An Toàn Lao Động trong Môi Trường Tự Động Hóa</h2>
<p>An toàn lao động là ưu tiên hàng đầu trong thiết kế và vận hành hệ thống tự động hóa. Việc tuân thủ các tiêu chuẩn an toàn quốc tế là bắt buộc.</p>

<h3>Các tiêu chuẩn an toàn:</h3>

<h4>ISO 13849 - Safety of Machinery</h4>
<ul>
<li>Performance Level (PL): PLa đến PLe</li>
<li>Category: B, 1, 2, 3, 4</li>
<li>Mean Time to Dangerous Failure (MTTFd)</li>
<li>Diagnostic Coverage (DC)</li>
</ul>

<h4>IEC 62061 - Functional Safety</h4>
<ul>
<li>Safety Integrity Level (SIL): SIL1 đến SIL4</li>
<li>Probability of Failure per Hour (PFH)</li>
<li>Hardware Fault Tolerance (HFT)</li>
<li>Safe Failure Fraction (SFF)</li>
</ul>

<h3>Thiết bị an toàn:</h3>

<h4>1. Emergency Stop Systems</h4>
<ul>
<li>Mushroom head buttons (màu đỏ)</li>
<li>Pull-wire switches</li>
<li>Safety relay modules</li>
<li>Dual channel monitoring</li>
</ul>

<h4>2. Light Curtains</h4>
<ul>
<li>Type 2: SIL2/PLd</li>
<li>Type 4: SIL3/PLe</li>
<li>Detection capability: 14-40mm</li>
<li>Response time: <15ms</li>
</ul>

<h4>3. Safety Gates & Interlocks</h4>
<ul>
<li>Magnetic safety switches</li>
<li>Coded safety switches</li>
<li>RFID safety systems</li>
<li>Guard locking systems</li>
</ul>

<h4>4. Safety Mats & Bumpers</h4>
<ul>
<li>Pressure sensitive mats</li>
<li>Safety bumpers</li>
<li>Area scanners</li>
<li>Laser safety systems</li>
</ul>

<h3>Risk Assessment Process:</h3>

<h4>Step 1: Hazard Identification</h4>
<ul>
<li>Mechanical hazards</li>
<li>Electrical hazards</li>
<li>Thermal hazards</li>
<li>Chemical hazards</li>
<li>Ergonomic hazards</li>
</ul>

<h4>Step 2: Risk Estimation</h4>
<table border="1" style="width:100%; border-collapse: collapse;">
<tr>
<th>Severity (S)</th>
<th>Frequency (F)</th>
<th>Probability (P)</th>
<th>Risk Level</th>
</tr>
<tr>
<td>S1: Slight injury</td>
<td>F1: Rare exposure</td>
<td>P1: Very low</td>
<td>Low risk</td>
</tr>
<tr>
<td>S2: Serious injury</td>
<td>F2: Frequent exposure</td>
<td>P2: Possible</td>
<td>Medium risk</td>
</tr>
<tr>
<td>S3: Death</td>
<td>F3: Continuous exposure</td>
<td>P3: Probable</td>
<td>High risk</td>
</tr>
</table>

<h4>Step 3: Risk Reduction</h4>
<ol>
<li><strong>Inherent safe design:</strong> Eliminate hazards</li>
<li><strong>Safeguarding:</strong> Guards, safety devices</li>
<li><strong>Information:</strong> Warning signs, training</li>
</ol>

<h3>Safety PLC Implementation:</h3>

<h4>Siemens S7-1500F Configuration:</h4>
<pre><code>
Safety Program Structure:
├── F-DB (Safety Data Block)
├── F-FB (Safety Function Block)
├── F-FC (Safety Function)
└── Safety I/O Mapping

Example Safety Function:
IF Emergency_Stop = FALSE AND
   Light_Curtain = FALSE AND
   Safety_Gate = CLOSED THEN
   Enable_Motors := TRUE;
ELSE
   Enable_Motors := FALSE;
   Trigger_Safe_Stop();
END_IF;
</code></pre>

<h3>Training và Certification:</h3>

<h4>Operator Training:</h4>
<ul>
<li>Safety procedures</li>
<li>Emergency response</li>
<li>Personal protective equipment</li>
<li>Hazard recognition</li>
</ul>

<h4>Maintenance Training:</h4>
<ul>
<li>Lockout/Tagout procedures</li>
<li>Safety device testing</li>
<li>Electrical safety</li>
<li>Confined space entry</li>
</ul>

<h3>Incident Investigation:</h3>
<ol>
<li>Immediate response</li>
<li>Scene preservation</li>
<li>Data collection</li>
<li>Root cause analysis</li>
<li>Corrective actions</li>
<li>Prevention measures</li>
</ol>
                ',
                'excerpt' => 'Hướng dẫn về an toàn lao động trong môi trường tự động hóa, tiêu chuẩn ISO 13849, IEC 62061.',
                'is_published' => true,
                'meta_title' => 'An Toàn Lao Động trong Tự Động Hóa',
                'meta_description' => 'Tìm hiểu về an toàn lao động trong tự động hóa, tiêu chuẩn ISO 13849, IEC 62061, safety PLC.',
            ],
        ];

        foreach ($pagesData as $pageData) {
            $page = Page::create([
                'category_id' => $pageCategories->isNotEmpty() ? $pageCategories->random()->id : null,
                'user_id' => $admin->id,
                'title' => $pageData['title'],
                'slug' => $pageData['slug'],
                'content' => $pageData['content'],
                'excerpt' => $pageData['excerpt'],
                'status' => $pageData['is_published'] ? 'published' : 'draft',
                'meta_title' => $pageData['meta_title'],
                'meta_description' => $pageData['meta_description'],
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(0, 15)),
            ]);
        }
    }
}
