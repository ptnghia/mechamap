<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Thread;
use App\Models\Forum;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ThreadSeeder extends Seeder
{
    /**
     * Seed threads với nội dung chuyên ngành cơ khí
     * Sử dụng nội dung thực tế từ web search và hình ảnh có sẵn
     */
    public function run(): void
    {
        $this->command->info('💬 Bắt đầu seed threads với nội dung chuyên ngành...');

        // Lấy dữ liệu cần thiết
        $forums = Forum::all();
        $users = User::all();
        $tags = Tag::all();
        $threadImages = Media::where('file_path', 'like', '%/threads/%')->get();

        if ($forums->isEmpty()) {
            $this->command->error('❌ Không có forums! Chạy ForumSeeder trước.');
            return;
        }

        // Tạo threads theo từng forum
        foreach ($forums as $forum) {
            $this->createThreadsForForum($forum, $users, $tags, $threadImages);
        }

        $this->command->info('✅ Hoàn thành seed threads!');
    }

    private function createThreadsForForum(Forum $forum, $users, $tags, $threadImages): void
    {
        // Lấy thread data dựa vào tên forum
        $threadData = $this->getThreadDataForForum($forum);

        foreach ($threadData as $threadInfo) {
            // Random user và image
            $author = $users->random();
            $image = $threadImages->random();

            // Tạo thread với cấu trúc đúng
            $thread = Thread::create([
                'title' => $threadInfo['title'],
                'slug' => Str::slug($threadInfo['title']) . '-' . $forum->id . '-' . rand(100, 999),
                'content' => $threadInfo['content'],
                'featured_image' => $image->file_path,
                'meta_description' => Str::limit(strip_tags($threadInfo['content']), 160),
                'search_keywords' => json_encode($this->extractKeywords($threadInfo['title'])),
                'read_time' => $this->calculateReadTime($threadInfo['content']),
                'status' => 'published',
                'user_id' => $author->id,
                'forum_id' => $forum->id,
                'category_id' => $forum->category_id,
                'is_sticky' => $threadInfo['pinned'] ?? false,
                'is_locked' => false,
                'is_featured' => $threadInfo['featured'] ?? false,
                'is_solved' => false,
                'quality_score' => rand(70, 95) / 10, // 7.0 - 9.5
                'average_rating' => rand(35, 50) / 10, // 3.5 - 5.0
                'ratings_count' => rand(5, 25),
                'thread_type' => $this->getThreadType($threadInfo['title']),
                'technical_difficulty' => $this->getTechnicalDifficulty($threadInfo['title']),
                'project_type' => $this->getProjectType($forum->name),
                'software_used' => json_encode($this->getSoftwareUsed($forum->name)),
                'industry_sector' => 'manufacturing',
                'technical_specs' => json_encode($this->getTechnicalSpecs($forum->name)),
                'requires_calculations' => rand(0, 1),
                'has_drawings' => rand(0, 1),
                'urgency_level' => $this->getUrgencyLevel(),
                'standards_compliance' => json_encode($this->getStandardsCompliance($forum->name)),
                'requires_pe_review' => rand(0, 1),
                'has_cad_files' => rand(0, 1),
                'attachment_count' => rand(0, 3),
                'view_count' => rand(10, 500),
                'likes' => rand(0, 50),
                'bookmarks' => rand(0, 20),
                'shares' => rand(0, 10),
                'replies' => 0, // Sẽ update sau khi tạo comments
                'attachment_types' => json_encode($this->getAttachmentTypes($forum->name)),
                'has_calculations' => rand(0, 1),
                'has_3d_models' => rand(0, 1),
                'expert_verified' => rand(0, 1),
                'technical_keywords' => json_encode($this->getTechnicalKeywords($threadInfo['title'])),
                'related_standards' => json_encode($this->getRelatedStandards($forum->name)),
                'moderation_status' => 'approved',
                'is_spam' => false,
                'last_activity_at' => now()->subDays(rand(0, 5)),
                'priority' => rand(1, 5),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);

            // Attach random tags
            $randomTags = $tags->random(rand(2, 4));
            $thread->tags()->attach($randomTags->pluck('id'));

            $this->command->line("   📝 Tạo thread: {$thread->title}");
        }
    }

    private function getThreadDataForForum(Forum $forum): array
    {
        $forumName = strtolower($forum->name);

        // CAD/CAM Software Forum
        if (str_contains($forumName, 'cad') || str_contains($forumName, 'cam')) {
            return [
                [
                    'title' => 'Cách tối ưu file SolidWorks để chạy nhanh hơn - 6 tips quan trọng',
                    'content' => $this->getSolidWorksOptimizationContent(),
                    'featured' => true,
                ],
                [
                    'title' => 'Lỗi "Sketch is open, self-intersecting" trong SolidWorks - Cách khắc phục',
                    'content' => $this->getSolidWorksErrorContent(),
                    'featured' => false,
                ],
                [
                    'title' => 'So sánh IGES vs STEP - Format file CAD nào tốt hơn?',
                    'content' => $this->getFileFormatComparisonContent(),
                    'featured' => true,
                ],
                [
                    'title' => 'SolidWorks Material Library - Hướng dẫn sử dụng chi tiết',
                    'content' => $this->getMaterialLibraryContent(),
                    'featured' => false,
                ],
            ];
        }

        // CNC Machining Forum
        if (str_contains($forumName, 'cnc')) {
            return [
                [
                    'title' => 'Lập trình CNC 3 trục với Mastercam - Kinh nghiệm từ thực tế',
                    'content' => $this->getCNCProgrammingContent(),
                    'featured' => true,
                ],
                [
                    'title' => 'Chọn dao phay phù hợp cho từng loại vật liệu - Bảng tra cứu',
                    'content' => $this->getToolSelectionContent(),
                    'featured' => false,
                ],
                [
                    'title' => 'Post Processor là gì? Cách cài đặt và sử dụng trong Mastercam',
                    'content' => $this->getPostProcessorContent(),
                    'featured' => true,
                ],
            ];
        }

        // FEA/CFD Forum
        if (str_contains($forumName, 'fea') || str_contains($forumName, 'cfd') || str_contains($forumName, 'phân tích')) {
            return [
                [
                    'title' => '10 cách thiết kế CAD model thân thiện với FEA',
                    'content' => $this->getFEAFriendlyContent(),
                    'featured' => true,
                ],
                [
                    'title' => 'ANSYS vs ABAQUS vs COMSOL - So sánh phần mềm FEA',
                    'content' => $this->getFEASoftwareComparisonContent(),
                    'featured' => false,
                ],
            ];
        }

        // PLC & HMI Forum
        if (str_contains($forumName, 'plc') || str_contains($forumName, 'hmi')) {
            return [
                [
                    'title' => 'Lập trình PLC Siemens S7-1200 cho người mới bắt đầu',
                    'content' => $this->getPLCProgrammingContent(),
                    'featured' => true,
                ],
                [
                    'title' => 'Thiết kế HMI hiệu quả - Best practices và tips',
                    'content' => $this->getHMIDesignContent(),
                    'featured' => false,
                ],
            ];
        }

        // Robot công nghiệp Forum
        if (str_contains($forumName, 'robot')) {
            return [
                [
                    'title' => 'Tích hợp robot ABB vào dây chuyền sản xuất - Case study',
                    'content' => $this->getRobotIntegrationContent(),
                    'featured' => true,
                ],
                [
                    'title' => 'So sánh robot KUKA vs Fanuc vs ABB - Ưu nhược điểm',
                    'content' => $this->getRobotComparisonContent(),
                    'featured' => false,
                ],
            ];
        }

        // Kim loại & Hợp kim Forum
        if (str_contains($forumName, 'kim loại') || str_contains($forumName, 'hợp kim')) {
            return [
                [
                    'title' => 'Xử lý nhiệt thép carbon - Quy trình và thông số chuẩn',
                    'content' => $this->getHeatTreatmentContent(),
                    'featured' => true,
                ],
                [
                    'title' => 'Hợp kim nhôm trong ngành hàng không - Tính chất và ứng dụng',
                    'content' => $this->getAluminumAlloyContent(),
                    'featured' => false,
                ],
            ];
        }

        // Default threads cho forums khác
        return [
            [
                'title' => "Thảo luận về {$forum->name} - Chia sẻ kinh nghiệm",
                'content' => $this->getDefaultContent($forum->name),
                'featured' => false,
            ],
            [
                'title' => "Hỏi đáp kỹ thuật về {$forum->name}",
                'content' => $this->getQAContent($forum->name),
                'featured' => false,
            ],
        ];
    }

    private function getSolidWorksOptimizationContent(): string
    {
        return "
# 6 Cách Tối Ưu File SolidWorks Để Chạy Nhanh Hơn

Khi làm việc với các file SolidWorks lớn, hiệu suất có thể bị ảnh hưởng đáng kể. Dưới đây là 6 tips quan trọng để tối ưu file của bạn:

## 1. Giảm Kích Thước File DWG
- Sử dụng lệnh **PURGE** để xóa các layer, block không sử dụng
- Xóa các object ẩn và geometry không cần thiết
- Compress file định kỳ

## 2. Tối Ưu Feature Tree
- Sắp xếp lại thứ tự features hợp lý
- Suppress các features không cần thiết trong quá trình thiết kế
- Sử dụng **Configurations** thay vì tạo nhiều file riêng biệt

## 3. Quản Lý Assemblies Hiệu Quả
- Sử dụng **Lightweight mode** cho các components lớn
- **SpeedPak** cho assemblies phức tạp
- Chia nhỏ assembly thành các sub-assemblies

## 4. Cấu Hình Graphics Settings
- Giảm **Image Quality** trong View Settings
- Tắt **RealView Graphics** khi không cần thiết
- Sử dụng **Large Assembly Mode**

## 5. Hardware Optimization
- RAM tối thiểu 16GB, khuyến nghị 32GB+
- Graphics card chuyên dụng (Quadro/FirePro)
- SSD thay vì HDD

## 6. Maintenance Định Kỳ
- Chạy **SolidWorks Rx** để kiểm tra hệ thống
- Update driver graphics card thường xuyên
- Backup và archive các file cũ

**Kết quả:** Áp dụng các tips này có thể cải thiện hiệu suất lên đến 50-70%, đặc biệt với các assemblies lớn.

*Bạn đã thử tip nào chưa? Chia sẻ kinh nghiệm của bạn nhé!*
        ";
    }

    private function getSolidWorksErrorContent(): string
    {
        return "
# Khắc Phục Lỗi 'Sketch is open, self-intersecting' Trong SolidWorks

Đây là một trong những lỗi phổ biến nhất khi sử dụng **Revolved Boss/Base** feature. Hãy cùng tìm hiểu nguyên nhân và cách khắc phục.

## Nguyên Nhân Gây Lỗi

### 1. Sketch Không Đóng Kín
- Các đường line không connect với nhau
- Có gaps nhỏ giữa các segments
- Endpoints không trùng nhau

### 2. Self-Intersecting Geometry
- Sketch tự cắt chính nó
- Có các loops phức tạp
- Centerline cắt qua sketch profile

## Cách Khắc Phục

### Bước 1: Kiểm Tra Sketch
```
1. Edit sketch
2. Tools > Sketch Tools > Check Sketch for Feature
3. Xem các lỗi được highlight
```

### Bước 2: Sửa Geometry
- **Trim/Extend** các đường line để đóng kín
- Sử dụng **Coincident** constraint cho endpoints
- Xóa các đường line thừa

### Bước 3: Kiểm Tra Centerline
- Centerline phải nằm ngoài sketch profile
- Không được cắt qua closed profile
- Sử dụng **Construction Line** nếu cần

### Bước 4: Validate Sketch
```
Tools > Sketch Tools > Repair Sketch
```

## Tips Phòng Tránh
1. **Snap to Grid** khi vẽ sketch
2. Sử dụng **Automatic Relations**
3. Kiểm tra sketch trước khi revolve
4. Vẽ từ centerline ra ngoài

## Video Hướng Dẫn
*[Link video demo sẽ được update]*

**Lưu ý:** Nếu vẫn gặp lỗi, hãy thử **Convert Entities** từ existing geometry thay vì vẽ từ đầu.

Ai đã gặp lỗi này chưa? Share cách giải quyết của bạn!
        ";
    }

    private function getCNCProgrammingContent(): string
    {
        return "
# Lập Trình CNC 3 Trục Với Mastercam - Kinh Nghiệm Thực Tế

Sau 10 năm làm việc với CNC, tôi muốn chia sẻ những kinh nghiệm thực tế trong lập trình Mastercam.

## Workflow Chuẩn

### 1. Chuẩn Bị File CAD
- Import file STEP/IGES vào Mastercam
- Kiểm tra geometry integrity
- Set up **WCS** (Work Coordinate System)
- Định nghĩa **Stock** material

### 2. Lựa Chọn Toolpath Strategy

#### Roughing Operations:
- **Dynamic Mill** cho material removal nhanh
- **Pocket** cho các cavity sâu
- **Contour** cho profile roughing

#### Finishing Operations:
- **Contour** cho walls và profiles
- **Surface High Speed** cho 3D surfaces
- **Pencil Mill** cho corners nhỏ

### 3. Tool Selection Best Practices

```
Material: Aluminum 6061
- Roughing: End mill 12mm, 3 flutes
- Finishing: End mill 6mm, 2 flutes
- Speeds: 8000-12000 RPM
- Feeds: 1500-2500 mm/min
```

```
Material: Steel 1045
- Roughing: End mill 10mm, 4 flutes
- Finishing: End mill 4mm, 2 flutes
- Speeds: 3000-5000 RPM
- Feeds: 800-1200 mm/min
```

## Tips Tối Ưu Toolpath

### 1. Climb Milling
- Luôn sử dụng climb milling khi có thể
- Giảm burr và cải thiện surface finish
- Tăng tool life

### 2. Stepdown/Stepover
- Roughing: 60-80% tool diameter
- Finishing: 10-20% tool diameter
- Adjust theo material hardness

### 3. Lead In/Out
- Sử dụng **Arc** lead in/out
- Tránh plunge cuts trực tiếp
- **Ramp** entry cho deep cuts

## Post Processor Setup

### Fanuc Controls:
```gcode
G90 G54 G17 G49 G40 G80
M06 T01 (12MM END MILL)
G43 H01 Z100.
S8000 M03
G00 X0. Y0.
Z5.
```

### Siemens 840D:
```gcode
G90 G54 G17 G40 G49
T1 D1 (12MM END MILL)
G43 Z100.
S8000 M3
G0 X0. Y0.
Z5.
```

## Kinh Nghiệm Thực Tế

### 1. Simulation Trước Khi Chạy
- **Verify** toolpath trong Mastercam
- Check for **gouges** và **collisions**
- Estimate **cycle time**

### 2. Prove Out Strategy
- Chạy **single block** lần đầu
- **Feed override** 50% cho roughing
- Monitor **spindle load** và **vibration**

### 3. Troubleshooting Common Issues

**Chatter:**
- Giảm spindle speed
- Tăng feed rate
- Shorter tool length

**Poor Surface Finish:**
- Check tool sharpness
- Adjust feeds/speeds
- Coolant flow

**Tool Breakage:**
- Reduce chipload
- Better work holding
- Proper tool selection

## Kết Luận
Mastercam là công cụ mạnh mẽ nhưng cần kinh nghiệm để sử dụng hiệu quả. Key success factors:
1. **Understand your material**
2. **Choose right tools**
3. **Optimize toolpaths**
4. **Simulate everything**

Các bạn có kinh nghiệm gì với Mastercam? Share nhé!
        ";
    }

    private function getDefaultContent(string $forumName): string
    {
        return "
# Chào Mừng Đến Với Forum {$forumName}

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **{$forumName}**.

## Mục Đích Forum
- Chia sẻ kiến thức chuyên môn
- Giải đáp thắc mắc kỹ thuật
- Cập nhật xu hướng công nghệ mới
- Kết nối cộng đồng kỹ sư Việt Nam

## Quy Tắc Tham Gia
1. **Tôn trọng** ý kiến của mọi người
2. **Chia sẻ** kiến thức một cách chân thành
3. **Tìm kiếm** trước khi đặt câu hỏi
4. **Sử dụng** tiếng Việt có dấu

## Chủ Đề Thảo Luận
- Kinh nghiệm thực tế từ công việc
- Tips & tricks hữu ích
- Troubleshooting các vấn đề kỹ thuật
- Review công cụ, phần mềm mới

Hãy bắt đầu chia sẻ và thảo luận nhé! 🚀
        ";
    }

    private function getQAContent(string $forumName): string
    {
        return "
# Q&A - Hỏi Đáp Kỹ Thuật Về {$forumName}

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến {$forumName}.

## Cách Đặt Câu Hỏi Hiệu Quả

### 1. Tiêu Đề Rõ Ràng
❌ \"Help me!\"
✅ \"Lỗi G-code khi lập trình CNC Fanuc\"

### 2. Mô Tả Chi Tiết
- **Vấn đề gặp phải**
- **Các bước đã thử**
- **Kết quả mong muốn**
- **Screenshots/code** nếu có

### 3. Thông Tin Môi Trường
- Phần mềm và version
- Hardware specifications
- Operating system

## Ví Dụ Câu Hỏi Tốt

**Tiêu đề:** Mastercam 2023 - Toolpath bị lỗi khi post processor

**Nội dung:**
```
Chào mọi người,

Mình đang gặp vấn đề khi post toolpath từ Mastercam 2023:
- Software: Mastercam 2023
- Post: Fanuc_18i.pst
- Lỗi: \"Invalid G-code at line 125\"

Đã thử:
1. Regenerate toolpath
2. Check geometry
3. Verify post processor

Ai đã gặp tương tự chưa? Cảm ơn!
```

## Guidelines Trả Lời
- **Cụ thể** và **chi tiết**
- **Test** solution trước khi share
- **Explain why** không chỉ how
- **Follow up** để confirm

Hãy cùng nhau xây dựng cộng đồng hỗ trợ mạnh mẽ! 💪
        ";
    }

    private function getFileFormatComparisonContent(): string
    {
        return "
# So Sánh IGES vs STEP - Format File CAD Nào Tốt Hơn?

Khi trao đổi file CAD giữa các phần mềm khác nhau, việc chọn format phù hợp rất quan trọng. Hãy cùng so sánh IGES và STEP.

## IGES (Initial Graphics Exchange Specification)

### Ưu Điểm:
- **Tương thích rộng** - Hầu hết phần mềm CAD đều hỗ trợ
- **File size nhỏ** hơn STEP
- **Nhanh** khi import/export
- **Lịch sử lâu đời** - Stable và reliable

### Nhược Điểm:
- **Mất thông tin** feature history
- **Không hỗ trợ** assembly structure tốt
- **Chất lượng surface** có thể bị giảm
- **Không có metadata** chi tiết

## STEP (Standard for Exchange of Product Data)

### Ưu Điểm:
- **Bảo toàn geometry** tốt hơn
- **Hỗ trợ assembly** structure
- **Metadata phong phú** (materials, properties)
- **Chuẩn ISO** - Tương lai của CAD exchange

### Nhược Điểm:
- **File size lớn** hơn IGES
- **Chậm hơn** khi xử lý
- **Một số phần mềm cũ** chưa hỗ trợ đầy đủ

## Khuyến Nghị Sử Dụng

### Dùng IGES Khi:
- ✅ File đơn giản, chỉ cần geometry
- ✅ Tương thích với phần mềm cũ
- ✅ Cần file size nhỏ
- ✅ Export cho machining (CAM)

### Dùng STEP Khi:
- ✅ Assembly phức tạp
- ✅ Cần bảo toàn chất lượng cao
- ✅ Trao đổi với khách hàng/đối tác
- ✅ Lưu trữ lâu dài

## Tips Thực Tế

### Export Settings:
```
IGES:
- Version: 214
- Units: mm
- Precision: 0.01mm

STEP:
- Version: AP214
- Units: mm
- Include: Colors, Materials
```

### Troubleshooting:
- **Geometry bị lỗi**: Thử giảm precision
- **File quá lớn**: Sử dụng IGES thay vì STEP
- **Mất màu sắc**: Check export settings

## Kết Luận
- **STEP** cho projects quan trọng, cần chất lượng cao
- **IGES** cho workflow nhanh, file đơn giản
- **Luôn backup** file native trước khi export

Các bạn thường dùng format nào? Chia sẻ kinh nghiệm nhé!
        ";
    }

    private function getMaterialLibraryContent(): string
    {
        return "
# SolidWorks Material Library - Hướng Dẫn Sử Dụng Chi Tiết

Material Library là tính năng mạnh mẽ của SolidWorks giúp quản lý và áp dụng vật liệu cho models.

## Truy Cập Material Library

### Cách 1: Feature Manager
```
1. Right-click trên part name
2. Chọn 'Edit Material'
3. Material dialog sẽ mở
```

### Cách 2: Material Tab
```
1. Mở ConfigurationManager
2. Click tab 'Material'
3. Browse materials có sẵn
```

## Cấu Trúc Material Library

### Built-in Categories:
- **Steel** - Các loại thép công nghiệp
- **Aluminum Alloys** - Hợp kim nhôm
- **Plastics** - Nhựa kỹ thuật
- **Composites** - Vật liệu composite
- **Other Metals** - Kim loại khác

### Properties Included:
- **Density** (kg/m³)
- **Elastic Modulus** (N/m²)
- **Poisson's Ratio**
- **Tensile Strength** (N/m²)
- **Thermal Properties**

## Tạo Custom Material

### Bước 1: Copy Existing Material
```
1. Right-click material tương tự
2. Chọn 'Copy'
3. Paste vào Custom Materials
```

### Bước 2: Edit Properties
```
- Name: Thép CT3 Việt Nam
- Density: 7850 kg/m³
- Elastic Modulus: 2.1e11 N/m²
- Poisson's Ratio: 0.28
- Tensile Strength: 370e6 N/m²
```

### Bước 3: Save Material
```
File > Save As > Material Database (.sldmat)
```

## Material Database Management

### Backup Materials:
```
Location: C:\\ProgramData\\SOLIDWORKS\\SOLIDWORKS 2023\\lang\\english\\sldmaterials\\
Files: *.sldmat
```

### Share Materials:
```
1. Export: File > Save As > .sldmat
2. Import: Tools > Options > File Locations > Material Databases
3. Add path to shared folder
```

## Simulation Integration

### For FEA Analysis:
- **Verify** material properties
- **Check** temperature dependency
- **Validate** stress-strain curves

### For Motion Study:
- **Density** affects inertia
- **Friction** coefficients important
- **Damping** properties

## Best Practices

### 1. Organization:
- **Tạo folders** theo dự án
- **Naming convention** rõ ràng
- **Document** material sources

### 2. Validation:
- **Cross-check** với material datasheets
- **Test** với simple geometry
- **Verify** simulation results

### 3. Maintenance:
- **Regular backup** material databases
- **Update** properties khi có data mới
- **Clean up** unused materials

## Common Issues

### Material Not Showing:
```
Solution:
1. Check file path in Options
2. Verify .sldmat file integrity
3. Restart SolidWorks
```

### Properties Not Updating:
```
Solution:
1. Rebuild model (Ctrl+B)
2. Update mass properties
3. Check material assignment
```

## Advanced Tips

### Custom Appearance:
- **Link** material với appearance
- **Create** realistic renderings
- **Match** real-world colors

### API Integration:
```vb
' VBA example
Set swMaterial = swModel.GetMaterialPropertyName2(\"Default\")
```

Ai đã tạo custom materials chưa? Share materials hay ho nhé!
        ";
    }

    private function getToolSelectionContent(): string
    {
        return "
# Chọn Dao Phay Phù Hợp Cho Từng Loại Vật Liệu

Việc chọn dao phay đúng là yếu tố quyết định chất lượng gia công và tuổi thọ dao.

## Bảng Tra Cứu Nhanh

### Aluminum 6061:
```
Roughing: End mill 3-4 flutes, uncoated
Finishing: End mill 2 flutes, polished
Speed: 8000-15000 RPM
Feed: 1500-3000 mm/min
Coolant: Flood coolant hoặc air blast
```

### Steel 1045:
```
Roughing: End mill 4 flutes, TiN coated
Finishing: End mill 2-3 flutes, TiAlN coated
Speed: 3000-6000 RPM
Feed: 800-1500 mm/min
Coolant: Flood coolant bắt buộc
```

### Stainless Steel 304:
```
Roughing: End mill 3 flutes, sharp edge
Finishing: End mill 2 flutes, positive rake
Speed: 2000-4000 RPM
Feed: 600-1200 mm/min
Coolant: High pressure coolant
```

### Titanium Ti-6Al-4V:
```
Roughing: End mill 3 flutes, very sharp
Finishing: End mill 2 flutes, polished
Speed: 1500-3000 RPM
Feed: 400-800 mm/min
Coolant: High volume flood
```

## Chi Tiết Theo Vật Liệu

### 1. Aluminum Alloys

#### Đặc Điểm:
- **Soft** và **gummy**
- **Chip evacuation** quan trọng
- **Built-up edge** dễ xảy ra

#### Tool Selection:
- **2-3 flutes** cho chip clearance
- **Sharp cutting edges**
- **Polished flutes** chống stick
- **Large helix angle** (45°+)

#### Recommended Brands:
- Harvey Tool (USA)
- Onsrud (USA)
- Kyocera (Japan)

### 2. Carbon Steel

#### Đặc Điểm:
- **Work hardening** nhanh
- **Heat generation** cao
- **Chip control** cần thiết

#### Tool Selection:
- **4 flutes** cho surface finish
- **TiN/TiAlN coating**
- **Variable helix** chống chatter
- **Chip breaker** geometry

### 3. Stainless Steel

#### Đặc Điểm:
- **Work hardening** rất nhanh
- **Gummy** và **stringy chips**
- **Heat resistant**

#### Tool Selection:
- **Sharp edges** bắt buộc
- **Positive rake angle**
- **Uncoated carbide** hoặc **PVD coating**
- **Constant feed** để tránh work hardening

## Coating Selection Guide

### Uncoated Carbide:
- ✅ Aluminum, Copper
- ✅ Plastics, Composites
- ❌ Steel, Stainless

### TiN (Titanium Nitride):
- ✅ General purpose steel
- ✅ Cast iron
- ⚠️ Aluminum (có thể stick)

### TiAlN (Titanium Aluminum Nitride):
- ✅ High-speed steel machining
- ✅ Stainless steel
- ✅ High-temp applications

### Diamond (PCD):
- ✅ Aluminum (high volume)
- ✅ Composites
- ❌ Ferrous metals

## Geometry Considerations

### Helix Angle:
- **30°**: General purpose
- **45°**: Aluminum, soft materials
- **60°**: Finishing operations

### End Mill Types:
- **Square End**: General milling
- **Ball End**: 3D contouring
- **Corner Radius**: Strength + finish
- **Tapered**: Deep cavities

## Troubleshooting Guide

### Poor Surface Finish:
- ✅ Increase speed
- ✅ Decrease feed per tooth
- ✅ Check tool sharpness
- ✅ Improve rigidity

### Tool Breakage:
- ✅ Reduce chipload
- ✅ Check work holding
- ✅ Verify speeds/feeds
- ✅ Improve coolant flow

### Built-up Edge:
- ✅ Increase cutting speed
- ✅ Use sharper tools
- ✅ Better coolant
- ✅ Reduce feed rate

## Cost Optimization

### High-Volume Production:
- **PCD tools** cho aluminum
- **Ceramic inserts** cho cast iron
- **Indexable** end mills

### Prototype/Low-Volume:
- **Solid carbide** end mills
- **General purpose** coatings
- **Standard geometries**

Các bạn có kinh nghiệm gì về chọn dao? Share tips nhé!
        ";
    }

    private function getPostProcessorContent(): string
    {
        return "
# Post Processor Là Gì? Cách Cài Đặt Và Sử Dụng Trong Mastercam

Post Processor (PP) là cầu nối quan trọng giữa CAM software và máy CNC.

## Post Processor Là Gì?

### Định Nghĩa:
Post Processor là **chương trình dịch** toolpath từ Mastercam thành **G-code** mà máy CNC hiểu được.

### Chức Năng:
- **Translate** toolpath coordinates
- **Generate** G-code commands
- **Format** theo syntax của controller
- **Add** machine-specific functions

## Tại Sao Cần Post Processor?

### Vấn Đề:
- Mỗi **CNC controller** có syntax khác nhau
- **Mastercam** tạo universal toolpath
- Cần **dịch** sang ngôn ngữ máy cụ thể

### Ví Dụ Khác Biệt:

#### Fanuc:
```gcode
G90 G54 G17 G49 G40 G80
M06 T01
G43 H01 Z100.
S1000 M03
```

#### Siemens 840D:
```gcode
G90 G54 G17 G40 G49
T1 D1
G43 Z100.
S1000 M3
```

#### Heidenhain:
```gcode
BEGIN PGM TEST MM
TOOL CALL 1 Z S1000
G43 Z100
```

## Cài Đặt Post Processor

### Bước 1: Download Post
```
1. Mastercam website > Support > Posts
2. Tìm theo machine/controller
3. Download .pst file
```

### Bước 2: Install Post
```
1. Copy .pst file vào folder:
   C:\\Users\\Public\\Documents\\shared mcam2023\\mill\\Posts\\
2. Restart Mastercam
```

### Bước 3: Verify Installation
```
1. Machine Definition Manager
2. Check post trong danh sách
3. Test với simple toolpath
```

## Cấu Hình Post Processor

### Machine Definition:
```
- Machine name: HAAS VF2
- Post processor: haas_vf2.pst
- Control type: Fanuc
- Work envelope: X30 Y16 Z20
```

### Post Settings:
```
- Output units: MM
- Sequence numbers: Yes
- Tool change position: G28
- Coolant codes: M08/M09
```

## Customization Post

### Common Modifications:

#### 1. Tool Change Position:
```
# Default
G28 G91 Z0.

# Custom
G53 G00 Z-10. (Safe Z)
G53 G00 X-15. Y-10. (Tool change position)
```

#### 2. Spindle Start Delay:
```
S1000 M03
G04 P2. (2 second delay)
```

#### 3. Custom M-Codes:
```
M100 (Pallet clamp)
M101 (Pallet unclamp)
M110 (Part probe)
```

## Testing Post Processor

### Verification Steps:
```
1. Create simple 2D contour
2. Generate toolpath
3. Post process
4. Check G-code output
5. Simulate in machine simulator
```

### Common Issues:

#### Wrong Tool Numbers:
```
Problem: T99 instead of T01
Solution: Check tool numbering in post
```

#### Missing Coolant:
```
Problem: No M08/M09
Solution: Enable coolant in post settings
```

#### Incorrect Coordinates:
```
Problem: Wrong work offset
Solution: Verify WCS setup
```

## Advanced Post Features

### Macro Programming:
```gcode
#100 = 10. (X position)
#101 = 20. (Y position)
G01 X#100 Y#101 F500
```

### Subroutines:
```gcode
M98 P1000 (Call subroutine)
...
O1000 (Subroutine start)
G01 X10. Y10. F500
M99 (Return)
```

### Parametric Programming:
```gcode
#1 = 5. (Number of holes)
WHILE [#1 GT 0] DO1
  G81 X[#1*10] Y0 Z-5. R2. F100
  #1 = #1 - 1
END1
```

## Best Practices

### 1. Documentation:
- **Document** all post modifications
- **Version control** custom posts
- **Test** thoroughly before production

### 2. Backup:
- **Backup** original posts
- **Save** machine-specific versions
- **Archive** working configurations

### 3. Validation:
- **Simulate** before running
- **Dry run** first parts
- **Monitor** machine behavior

## Troubleshooting

### Post Not Found:
```
1. Check file path
2. Verify .pst extension
3. Restart Mastercam
4. Check permissions
```

### G-code Errors:
```
1. Compare with working program
2. Check post settings
3. Verify machine definition
4. Contact post developer
```

## Kết Luận

Post Processor là **link quan trọng** trong CNC workflow. Hiểu và configure đúng sẽ:
- ✅ **Tăng hiệu quả** programming
- ✅ **Giảm lỗi** gia công
- ✅ **Tối ưu** machine performance

Ai đã custom post processor chưa? Chia sẻ kinh nghiệm nhé!
        ";
    }

    private function getFEAFriendlyContent(): string
    {
        return "
# 10 Cách Thiết Kế CAD Model Thân Thiện Với FEA

Finite Element Analysis (FEA) là bước quan trọng trong thiết kế. Tuy nhiên, không phải model CAD nào cũng phù hợp cho FEA.

## 1. Geometry Simplification
- **Loại bỏ** các features không ảnh hưởng đến kết quả
- **Defeaturing** các chamfers, fillets nhỏ
- **Suppress** các holes, threads không cần thiết

## 2. Mesh-Friendly Geometry
- **Tránh** sharp corners (R < 0.1mm)
- **Sử dụng** fillets phù hợp (R ≥ 0.5mm)
- **Symmetric** geometry khi có thể

## 3. Aspect Ratio Control
- **Tránh** thin walls (t < 0.1mm)
- **Length/thickness ratio** < 100:1
- **Uniform** thickness distribution

## 4. Material Properties
- **Định nghĩa** đúng material properties
- **Isotropic** vs **Anisotropic** materials
- **Temperature dependent** properties

## 5. Boundary Conditions
- **Realistic** constraints và loads
- **Avoid** over-constraining
- **Distributed** loads thay vì point loads

*Tiếp tục đọc để biết thêm 5 tips còn lại...*
        ";
    }

    private function getFEASoftwareComparisonContent(): string
    {
        return "
# ANSYS vs ABAQUS vs COMSOL - So Sánh Phần Mềm FEA

Chọn phần mềm FEA phù hợp là quyết định quan trọng cho dự án simulation.

## ANSYS Workbench

### Ưu Điểm:
- **User-friendly** interface
- **Integrated** CAD tools
- **Strong** structural analysis
- **Good** documentation

### Nhược Điểm:
- **Expensive** licensing
- **Resource** intensive
- **Limited** customization

### Best For:
- Structural analysis
- Thermal analysis
- Beginner users
- Industry standard

## ABAQUS

### Ưu Điểm:
- **Powerful** nonlinear solver
- **Advanced** material models
- **Excellent** contact analysis
- **Customizable** via scripting

### Nhược Điểm:
- **Steep** learning curve
- **Complex** interface
- **Expensive**

### Best For:
- Nonlinear analysis
- Advanced materials
- Research applications
- Expert users

## COMSOL Multiphysics

### Ưu Điểm:
- **Multiphysics** coupling
- **Flexible** physics setup
- **Good** meshing tools
- **Parametric** studies

### Nhược Điểm:
- **Very expensive**
- **Steep** learning curve
- **Resource** heavy

### Best For:
- Coupled physics
- Heat transfer + fluid flow
- Electromagnetic analysis
- Research & development

Các bạn đã dùng phần mềm nào? Chia sẻ kinh nghiệm nhé!
        ";
    }

    private function getHMIDesignContent(): string
    {
        return "
# Thiết Kế HMI Hiệu Quả - Best Practices Và Tips

Human Machine Interface (HMI) là giao diện quan trọng giữa operator và máy móc.

## Nguyên Tắc Thiết Kế

### 1. Simplicity
- **Ít** là **nhiều**
- **Tránh** clutter
- **Focus** vào thông tin quan trọng

### 2. Consistency
- **Unified** color scheme
- **Standard** button sizes
- **Consistent** navigation

### 3. Visibility
- **High contrast** colors
- **Readable** fonts (min 12pt)
- **Clear** status indicators

## Layout Best Practices

### Screen Organization:
```
Header: Title, Time, Alarms
Main Area: Process graphics
Footer: Navigation, Status
```

### Color Coding:
- **Red**: Alarms, Emergency stop
- **Yellow**: Warnings, Attention
- **Green**: Normal operation, OK
- **Blue**: Information, Manual mode
- **Gray**: Inactive, Disabled

## Navigation Design

### Menu Structure:
```
Main Menu
├── Production
│   ├── Auto Mode
│   ├── Manual Mode
│   └── Recipe Management
├── Maintenance
│   ├── Diagnostics
│   ├── Calibration
│   └── Service Menu
└── Settings
    ├── User Management
    ├── Network Config
    └── Backup/Restore
```

### Button Design:
- **Minimum** 40x40 pixels
- **Clear** labels
- **Visual** feedback on press
- **Disabled** state visible

## Alarm Management

### Alarm Priorities:
1. **Critical**: Process shutdown
2. **High**: Immediate attention
3. **Medium**: Action required
4. **Low**: Information only

### Alarm Display:
```
[TIMESTAMP] [PRIORITY] [MESSAGE] [ACK]
12:34:56    CRITICAL   Motor 1 Fault  [ACK]
12:35:12    HIGH       Temp High      [ACK]
```

## Data Visualization

### Trends:
- **Real-time** data plots
- **Historical** data access
- **Zoom** and **pan** capabilities
- **Export** functionality

### Gauges:
- **Analog** for continuous values
- **Digital** for precise readings
- **Color bands** for ranges
- **Min/Max** indicators

Ai đã thiết kế HMI chưa? Share screenshots nhé!
        ";
    }

    private function getRobotIntegrationContent(): string
    {
        return "
# Tích Hợp Robot ABB Vào Dây Chuyền Sản Xuất - Case Study

Dự án tích hợp robot ABB IRB 1600 vào dây chuyền welding tại nhà máy ô tô.

## Thông Tin Dự Án

### Yêu Cầu:
- **Welding** 24 điểm hàn/sản phẩm
- **Cycle time**: < 45 giây
- **Precision**: ±0.1mm
- **Uptime**: > 95%

### Equipment:
- **Robot**: ABB IRB 1600-6/1.45
- **Controller**: IRC5 Compact
- **Welding**: Fronius TPS 320i
- **Vision**: Cognex In-Sight 7000
- **Safety**: ABB SafeMove

## Giai Đoạn Thiết Kế

### 1. Layout Planning
```
Station Layout:
- Robot reach: 1450mm
- Part fixture: 800x600mm
- Safety fence: 2000x2000mm
- Operator access: Front side
```

### 2. Kinematics Analysis
- **Joint limits** check
- **Singularity** avoidance
- **Collision** detection
- **Cycle time** optimization

### 3. Tool Design
```
Welding Gun Specifications:
- Weight: 2.5kg
- Reach: 150mm
- Cable management: Dress pack
- Quick change: Manual
```

## Programming Strategy

### RAPID Code Structure:
```rapid
MODULE MainModule
  PROC main()
    ! Initialize
    InitializeStation;

    ! Main loop
    WHILE TRUE DO
      WaitForPart;
      PickupPart;
      WeldSequence;
      PlacePart;
    ENDWHILE
  ENDPROC
ENDMODULE
```

### Welding Sequence:
```rapid
PROC WeldSequence()
  ! Move to start position
  MoveJ pWeldStart, v100, fine, tWeldGun;

  ! Start welding
  SetDO doWeldStart, 1;

  ! Weld path
  MoveL pWeld1, v50, z1, tWeldGun;
  MoveL pWeld2, v50, z1, tWeldGun;

  ! Stop welding
  SetDO doWeldStart, 0;
ENDPROC
```

## Integration Challenges

### 1. Timing Synchronization
**Problem**: Robot và conveyor không sync
**Solution**:
```rapid
! Wait for conveyor signal
WaitDI diConveyorReady, 1;
! Start robot motion
MoveJ pPickup, v200, fine, tool0;
```

### 2. Vision System Integration
**Problem**: Part position variation
**Solution**:
```rapid
! Get vision data
GetVisionOffset nXOffset, nYOffset, nRotOffset;
! Apply offset
pPickupActual := Offs(pPickupNominal, nXOffset, nYOffset, 0);
```

### 3. Safety Implementation
**Problem**: Operator access during operation
**Solution**:
- **Light curtains** at entry points
- **SafeMove** reduced speed zones
- **Emergency stops** accessible

## Performance Results

### Before Automation:
- **Cycle time**: 120 seconds
- **Quality**: 85% first pass
- **Operator**: 2 người
- **Downtime**: 15%

### After Robot Integration:
- **Cycle time**: 42 seconds ✅
- **Quality**: 98% first pass ✅
- **Operator**: 1 người ✅
- **Downtime**: 3% ✅

## Lessons Learned

### 1. Planning Phase:
- **Simulation** trước khi install
- **Mock-up** testing quan trọng
- **Operator training** từ sớm

### 2. Programming:
- **Modular** code structure
- **Error handling** comprehensive
- **Documentation** chi tiết

### 3. Maintenance:
- **Preventive** maintenance schedule
- **Spare parts** inventory
- **Remote monitoring** setup

## ROI Analysis

### Investment:
- Robot system: $80,000
- Integration: $30,000
- Training: $10,000
- **Total**: $120,000

### Savings/Year:
- Labor cost: $60,000
- Quality improvement: $25,000
- Productivity gain: $40,000
- **Total**: $125,000

**Payback period**: 11.5 tháng ✅

## Recommendations

### For Similar Projects:
1. **Start** với simulation
2. **Involve** operators từ đầu
3. **Plan** cho maintenance
4. **Document** everything
5. **Train** thoroughly

Ai đã làm robot integration? Share kinh nghiệm nhé!
        ";
    }

    private function getRobotComparisonContent(): string
    {
        return "
# So Sánh Robot KUKA vs Fanuc vs ABB - Ưu Nhược Điểm

Chọn robot phù hợp cho ứng dụng cụ thể cần hiểu rõ đặc điểm từng hãng.

## ABB Robotics

### Ưu Điểm:
- **IRC5 controller** mạnh mẽ
- **RobotStudio** simulation tốt
- **RAPID** programming dễ học
- **Service** network rộng

### Nhược Điểm:
- **Giá** cao hơn competitors
- **Spare parts** đắt
- **Programming** phức tạp cho advanced features

### Best Applications:
- Automotive welding
- Material handling
- Painting applications
- General automation

## KUKA Robotics

### Ưu Điểm:
- **KRL** programming linh hoạt
- **Payload** cao
- **German** engineering quality
- **Automotive** heritage

### Nhược Điểm:
- **Learning curve** steep
- **Programming** phức tạp
- **Service** limited ở VN

### Best Applications:
- Heavy payload (>100kg)
- Automotive assembly
- Foundry applications
- Research projects

## Fanuc Robotics

### Ưu Điểm:
- **Reliability** cao nhất
- **Programming** đơn giản
- **Service** tốt
- **Price** competitive

### Nhược Điểm:
- **Interface** hơi cũ
- **Simulation** software basic
- **Customization** hạn chế

### Best Applications:
- CNC machine tending
- Pick and place
- Assembly operations
- High-volume production

## So Sánh Chi Tiết

### Programming:
```
ABB RAPID:
MoveJ pHome, v1000, fine, tool0;

KUKA KRL:
PTP HOME Vel=100% PDAT1 Tool[1]

Fanuc KAREL:
J P[1] 100% FINE
```

### Payload Comparison:
- **ABB**: 0.5kg - 800kg
- **KUKA**: 3kg - 1300kg
- **Fanuc**: 0.5kg - 2300kg

### Reach Comparison:
- **ABB**: 580mm - 3500mm
- **KUKA**: 635mm - 3900mm
- **Fanuc**: 522mm - 4700mm

## Market Share Vietnam:

### Industrial Segments:
1. **Fanuc**: 35% (CNC integration)
2. **ABB**: 30% (Automotive)
3. **KUKA**: 15% (Heavy industry)
4. **Others**: 20% (Yaskawa, Kawasaki)

### Price Comparison (6-axis, 6kg):
- **Fanuc**: $25,000 - $30,000
- **ABB**: $28,000 - $35,000
- **KUKA**: $30,000 - $38,000

## Selection Criteria

### Choose ABB If:
- ✅ Need good simulation
- ✅ Automotive applications
- ✅ Complex programming required
- ✅ Budget allows premium

### Choose Fanuc If:
- ✅ CNC machine tending
- ✅ Reliability critical
- ✅ Simple applications
- ✅ Cost-sensitive project

### Choose KUKA If:
- ✅ Heavy payload required
- ✅ Automotive assembly
- ✅ Research application
- ✅ German quality needed

## Support & Service

### ABB:
- **Local office**: TP.HCM, Hà Nội
- **Response time**: 24h
- **Training**: Regular courses
- **Spare parts**: 2-3 days

### Fanuc:
- **Local office**: TP.HCM, Hà Nội, Đà Nẵng
- **Response time**: 12h
- **Training**: Excellent
- **Spare parts**: 1-2 days

### KUKA:
- **Local office**: TP.HCM
- **Response time**: 48h
- **Training**: Limited
- **Spare parts**: 5-7 days

## Recommendations

### For Beginners:
**Fanuc** - Dễ học, reliable, support tốt

### For Automotive:
**ABB** - Industry standard, proven solutions

### For Heavy Duty:
**KUKA** - Payload cao, robust design

### For Budget Projects:
**Fanuc** - Best value for money

Các bạn đã dùng robot nào? Chia sẻ kinh nghiệm nhé!
        ";
    }

    private function getHeatTreatmentContent(): string
    {
        return "
# Xử Lý Nhiệt Thép Carbon - Quy Trình Và Thông Số Chuẩn

Heat treatment là quá trình quan trọng để cải thiện tính chất cơ học của thép.

## Các Phương Pháp Xử Lý Nhiệt

### 1. Annealing (Ủ)
**Mục đích**: Làm mềm, giảm stress, cải thiện machinability

**Quy trình**:
```
1. Heating: 750-850°C (trên A3)
2. Holding: 1-2 giờ
3. Cooling: Furnace cooling (chậm)
4. Result: Soft, machinable structure
```

### 2. Normalizing (Thường Hóa)
**Mục đích**: Đồng đều cấu trúc, cải thiện tính chất

**Quy trình**:
```
1. Heating: 850-900°C
2. Holding: 30-60 phút
3. Cooling: Air cooling
4. Result: Fine grain structure
```

### 3. Hardening (Tôi)
**Mục đích**: Tăng độ cứng, wear resistance

**Quy trình**:
```
1. Heating: 800-850°C (trên A3)
2. Holding: 15-30 phút
3. Cooling: Water/Oil quenching
4. Result: Hard, brittle martensite
```

### 4. Tempering (Ram)
**Mục đích**: Giảm brittleness, tăng toughness

**Quy trình**:
```
1. Heating: 150-650°C
2. Holding: 1-2 giờ
3. Cooling: Air cooling
4. Result: Balanced hardness/toughness
```

## Thông Số Cho Thép Carbon

### Low Carbon Steel (0.1-0.3% C):

#### Normalizing:
- **Temperature**: 870-920°C
- **Time**: 30-45 phút
- **Cooling**: Air
- **Result**: 150-200 HB

#### Case Hardening:
- **Process**: Carburizing
- **Temperature**: 900-950°C
- **Time**: 4-8 giờ
- **Case depth**: 0.5-1.5mm

### Medium Carbon Steel (0.3-0.6% C):

#### Hardening:
- **Temperature**: 820-870°C
- **Quenchant**: Oil
- **Hardness**: 50-60 HRC

#### Tempering:
- **150°C**: 58-60 HRC (tools)
- **300°C**: 45-50 HRC (springs)
- **500°C**: 30-35 HRC (gears)

### High Carbon Steel (0.6-1.0% C):

#### Hardening:
- **Temperature**: 780-820°C
- **Quenchant**: Water/Brine
- **Hardness**: 60-65 HRC

#### Tempering:
- **200°C**: 60-62 HRC (cutting tools)
- **400°C**: 40-45 HRC (chisels)
- **600°C**: 25-30 HRC (springs)

## Equipment Requirements

### Furnace Types:
- **Electric**: Precise control, clean
- **Gas**: Cost effective, large parts
- **Induction**: Fast heating, selective

### Quenching Media:
- **Water**: Fast cooling, risk of cracking
- **Oil**: Moderate cooling, less distortion
- **Polymer**: Controlled cooling rate
- **Air**: Slow cooling, minimal distortion

## Quality Control

### Testing Methods:

#### Hardness Testing:
```
- Rockwell C (HRC): Hardened parts
- Brinell (HB): Soft materials
- Vickers (HV): Thin sections
```

#### Microstructure:
```
- Optical microscopy
- Grain size measurement
- Phase identification
```

#### Mechanical Properties:
```
- Tensile strength
- Impact toughness
- Fatigue resistance
```

## Common Problems

### Cracking:
**Causes**:
- Quench too fast
- Sharp corners
- Contamination

**Solutions**:
- Slower quenchant
- Stress relief
- Clean surfaces

### Distortion:
**Causes**:
- Uneven heating
- Rapid cooling
- Residual stress

**Solutions**:
- Uniform heating
- Fixtures/jigs
- Pre-stress relief

### Soft Spots:
**Causes**:
- Insufficient temperature
- Poor circulation
- Scale formation

**Solutions**:
- Temperature verification
- Atmosphere control
- Surface preparation

## Safety Considerations

### PPE Required:
- **Heat resistant** gloves
- **Safety glasses**
- **Protective clothing**
- **Respiratory protection**

### Ventilation:
- **Exhaust systems** for fumes
- **Fresh air** supply
- **Gas detection** systems

## Cost Optimization

### Batch Processing:
- **Group** similar parts
- **Maximize** furnace capacity
- **Minimize** heat cycles

### Energy Efficiency:
- **Insulation** maintenance
- **Heat recovery** systems
- **Optimal** scheduling

Ai đã làm heat treatment chưa? Share kinh nghiệm nhé!
        ";
    }

    private function getAluminumAlloyContent(): string
    {
        return "
# Hợp Kim Nhôm Trong Ngành Hàng Không - Tính Chất Và Ứng Dụng

Aluminum alloys là vật liệu chủ đạo trong ngành aerospace nhờ tỷ lệ strength-to-weight tuyệt vời.

## Các Series Hợp Kim Nhôm

### 2xxx Series (Al-Cu):
**Đại diện**: 2024, 2014, 2219
**Đặc điểm**:
- **High strength** (up to 470 MPa)
- **Good** machinability
- **Poor** corrosion resistance
- **Heat treatable**

**Ứng dụng**:
- Aircraft structures
- Fuselage frames
- Wing spars
- Landing gear

### 6xxx Series (Al-Mg-Si):
**Đại diện**: 6061, 6082, 6063
**Đặc điểm**:
- **Medium strength** (up to 310 MPa)
- **Excellent** corrosion resistance
- **Good** weldability
- **Extrudable**

**Ứng dụng**:
- Aircraft panels
- Interior structures
- Non-critical components

### 7xxx Series (Al-Zn):
**Đại diện**: 7075, 7050, 7150
**Đặc điểm**:
- **Highest strength** (up to 570 MPa)
- **Excellent** fatigue resistance
- **Good** machinability
- **Premium** applications

**Ứng dụng**:
- Wing structures
- Fuselage frames
- Landing gear
- High-stress components

## Chi Tiết 7075-T6

### Composition:
```
Aluminum: 87.1-91.4%
Zinc: 5.1-6.1%
Magnesium: 2.1-2.9%
Copper: 1.2-2.0%
Chromium: 0.18-0.28%
```

### Mechanical Properties:
```
Tensile Strength: 572 MPa
Yield Strength: 503 MPa
Elongation: 11%
Hardness: 150 HB
Density: 2.81 g/cm³
```

### Heat Treatment:
```
Solution: 465-482°C, 1-2 hours
Quench: Water, <15 seconds
Age: 121°C, 24 hours (T6)
```

## Manufacturing Processes

### Machining:
**Cutting Parameters**:
```
Speed: 200-400 m/min
Feed: 0.1-0.3 mm/rev
Depth: 1-5 mm
Coolant: Flood recommended
```

**Tool Selection**:
- **Carbide** inserts
- **Sharp** cutting edges
- **Positive** rake angles
- **Polished** surfaces

### Welding:
**TIG Welding**:
```
Current: 80-150A AC
Electrode: 2% Thoriated
Filler: ER4043 or ER5356
Gas: Argon, 15-20 L/min
```

**Challenges**:
- **Hot cracking** susceptibility
- **Porosity** issues
- **Strength** reduction in HAZ

### Forming:
**Bend Radius**:
```
2024-T3: 2.5t minimum
6061-T6: 1.5t minimum
7075-T6: 4.0t minimum
```

## Corrosion Protection

### Anodizing:
**Type II** (Sulfuric Acid):
- **Thickness**: 5-25 μm
- **Colors**: Natural, Black, etc.
- **Corrosion** resistance improved

**Type III** (Hard Anodizing):
- **Thickness**: 25-100 μm
- **Hardness**: 300-500 HV
- **Wear** resistance excellent

### Chemical Conversion:
**Alodine/Chromate**:
- **Thin** coating (0.5-3 μm)
- **Paint** adhesion improved
- **Electrical** conductivity maintained

### Primers:
- **Zinc Chromate** (traditional)
- **Zinc Phosphate** (modern)
- **Epoxy** based systems

## Quality Standards

### Aerospace Standards:
- **AMS**: Aerospace Material Specifications
- **ASTM**: American Society for Testing
- **EN**: European Norms
- **JIS**: Japanese Industrial Standards

### Testing Requirements:
```
Tensile Testing: ASTM E8
Hardness: ASTM E18 (Rockwell)
Corrosion: ASTM B117 (Salt spray)
Fatigue: ASTM D7791
```

## Cost Considerations

### Material Costs (per kg):
```
6061-T6: $3-4
2024-T3: $5-7
7075-T6: $8-12
```

### Processing Costs:
- **Machining**: High (work hardening)
- **Welding**: Medium (skill required)
- **Forming**: Medium (springback)
- **Finishing**: Low-Medium

## Future Trends

### Advanced Alloys:
- **Al-Li** alloys (lighter)
- **Al-Sc** alloys (stronger)
- **MMCs** (Metal Matrix Composites)

### Manufacturing:
- **Additive** manufacturing
- **Friction** stir welding
- **Superplastic** forming

## Environmental Impact

### Recycling:
- **95%** energy savings vs primary
- **Infinite** recyclability
- **Closed loop** systems

### Sustainability:
- **Lightweight** = fuel savings
- **Corrosion** resistance = longevity
- **Recyclable** = circular economy

Ai đã làm việc với aluminum alloys? Share kinh nghiệm nhé!
        ";
    }

    private function getPLCProgrammingContent(): string
    {
        return "
# Lập Trình PLC Siemens S7-1200 Cho Người Mới

PLC (Programmable Logic Controller) là trái tim của hệ thống tự động hóa. Hướng dẫn này sẽ giúp bạn bắt đầu với Siemens S7-1200.

## Chuẩn Bị
### Hardware:
- CPU 1214C DC/DC/DC
- Digital Input Module (DI 16x24VDC)
- Digital Output Module (DO 16x24VDC)
- HMI KTP700 Basic

### Software:
- TIA Portal V17
- WinCC Runtime Advanced

## Bài 1: Blink LED
```ladder
Network 1: LED Blink
      +--[/]--+--( )--+
      |  M0.0  |  Q0.0 |
      +-------+-------+

      +--[ ]--+--( )--+
      |  M0.0  |  M0.1 |
      +-------+-------+

Network 2: Timer
      +--[ ]--+--[TON]--+--( )--+
      |  M0.1  |   T1   |  M0.0 |
      +-------+  PT:1s  +-------+
```

## Bài 2: Start/Stop Motor
```ladder
Network 1: Motor Control
      +--[ ]--+--[/]--+--( )--+
      | Start | Stop  | Motor |
      | I0.0  | I0.1  | Q0.0  |
      +-------+-------+-------+
      |              |
      +--[ ]--------+
      |  Q0.0       |
      +-------------+
```

## Tips Quan Trọng
1. **Comment** mọi networks
2. **Sử dụng** symbolic addressing
3. **Test** từng network riêng biệt
4. **Backup** project thường xuyên

Ai muốn học thêm về PLC? Comment nhé!
        ";
    }

    // Helper methods cho Thread attributes
    private function extractKeywords(string $title): array
    {
        $keywords = [];
        $title = strtolower($title);

        // Technical keywords mapping
        $keywordMap = [
            'solidworks' => ['solidworks', 'cad', '3d modeling'],
            'cnc' => ['cnc', 'machining', 'manufacturing'],
            'mastercam' => ['mastercam', 'cam', 'toolpath'],
            'ansys' => ['ansys', 'fea', 'simulation'],
            'plc' => ['plc', 'automation', 'control'],
            'robot' => ['robot', 'robotics', 'automation'],
            'steel' => ['steel', 'material', 'metallurgy'],
            'aluminum' => ['aluminum', 'alloy', 'aerospace'],
        ];

        foreach ($keywordMap as $key => $values) {
            if (str_contains($title, $key)) {
                $keywords = array_merge($keywords, $values);
            }
        }

        return array_unique($keywords);
    }

    private function calculateReadTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, ceil($wordCount / 200)); // 200 words per minute
    }

    private function getThreadType(string $title): string
    {
        $title = strtolower($title);

        if (str_contains($title, 'hỏi') || str_contains($title, '?')) {
            return 'question';
        } elseif (str_contains($title, 'so sánh') || str_contains($title, 'vs')) {
            return 'discussion'; // comparison không có trong enum
        } elseif (str_contains($title, 'hướng dẫn') || str_contains($title, 'cách')) {
            return 'tutorial';
        } elseif (str_contains($title, 'thảo luận')) {
            return 'discussion';
        } else {
            return 'discussion'; // thay vì article
        }
    }

    private function getProjectType(string $forumName): string
    {
        $forumName = strtolower($forumName);

        if (str_contains($forumName, 'cad') || str_contains($forumName, 'solidworks')) {
            return 'design';
        } elseif (str_contains($forumName, 'cnc') || str_contains($forumName, 'machining')) {
            return 'manufacturing';
        } elseif (str_contains($forumName, 'robot') || str_contains($forumName, 'automation')) {
            return 'manufacturing'; // automation không có trong enum
        } elseif (str_contains($forumName, 'material') || str_contains($forumName, 'steel')) {
            return 'research';
        } elseif (str_contains($forumName, 'ansys') || str_contains($forumName, 'fea')) {
            return 'analysis';
        } else {
            return 'tutorial'; // thay vì general
        }
    }

    private function getSoftwareUsed(string $forumName): ?array
    {
        $forumName = strtolower($forumName);

        if (str_contains($forumName, 'solidworks')) {
            return ['SolidWorks'];
        } elseif (str_contains($forumName, 'autocad')) {
            return ['AutoCAD'];
        } elseif (str_contains($forumName, 'mastercam')) {
            return ['Mastercam'];
        } elseif (str_contains($forumName, 'ansys')) {
            return ['ANSYS'];
        } elseif (str_contains($forumName, 'siemens')) {
            return ['TIA Portal'];
        } elseif (str_contains($forumName, 'abb')) {
            return ['RobotStudio'];
        } else {
            return null;
        }
    }

    private function getTechnicalSpecs(string $forumName): ?array
    {
        $forumName = strtolower($forumName);

        if (str_contains($forumName, 'steel') || str_contains($forumName, 'material')) {
            return [
                'material' => 'Steel AISI 1045',
                'yield_strength' => '370 MPa',
                'tensile_strength' => '625 MPa',
                'hardness' => '180 HB'
            ];
        } elseif (str_contains($forumName, 'cnc') || str_contains($forumName, 'machining')) {
            return [
                'tolerance' => '±0.01mm',
                'surface_finish' => 'Ra 1.6',
                'material_removal_rate' => '50 cm³/min'
            ];
        } elseif (str_contains($forumName, 'robot')) {
            return [
                'payload' => '6 kg',
                'reach' => '1450 mm',
                'repeatability' => '±0.1 mm',
                'speed' => '2.3 m/s'
            ];
        }

        return null;
    }

    private function getUrgencyLevel(): string
    {
        $levels = ['low', 'normal', 'high', 'critical'];
        return $levels[array_rand($levels)];
    }

    private function getStandardsCompliance(string $forumName): ?array
    {
        $forumName = strtolower($forumName);

        if (str_contains($forumName, 'material') || str_contains($forumName, 'steel')) {
            return ['ASTM', 'ISO', 'JIS'];
        } elseif (str_contains($forumName, 'machining') || str_contains($forumName, 'cnc')) {
            return ['ISO 2768', 'ASME Y14.5'];
        } elseif (str_contains($forumName, 'robot') || str_contains($forumName, 'automation')) {
            return ['ISO 10218', 'IEC 61508'];
        }

        return ['ISO', 'ASME'];
    }

    private function getAttachmentTypes(string $forumName): ?array
    {
        $forumName = strtolower($forumName);

        if (str_contains($forumName, 'cad') || str_contains($forumName, 'solidworks')) {
            return ['STEP', 'IGES', 'DWG', 'PDF'];
        } elseif (str_contains($forumName, 'cnc') || str_contains($forumName, 'mastercam')) {
            return ['NC', 'MCX', 'PDF', 'STEP'];
        } elseif (str_contains($forumName, 'ansys') || str_contains($forumName, 'fea')) {
            return ['ANSYS', 'PDF', 'CSV', 'STEP'];
        }

        return ['PDF', 'DOC'];
    }

    private function getTechnicalKeywords(string $title): array
    {
        $keywords = [];
        $title = strtolower($title);

        // Extract technical terms
        $technicalTerms = [
            'solidworks', 'autocad', 'mastercam', 'ansys', 'cnc', 'fea', 'cfd',
            'plc', 'hmi', 'robot', 'automation', 'steel', 'aluminum', 'machining',
            'welding', 'casting', 'forging', 'heat treatment', 'tolerance', 'gd&t'
        ];

        foreach ($technicalTerms as $term) {
            if (str_contains($title, $term)) {
                $keywords[] = $term;
            }
        }

        return $keywords;
    }

    private function getRelatedStandards(string $forumName): array
    {
        $forumName = strtolower($forumName);

        if (str_contains($forumName, 'material')) {
            return ['ASTM A36', 'ISO 898', 'JIS G3101'];
        } elseif (str_contains($forumName, 'machining')) {
            return ['ISO 2768-1', 'ASME Y14.5', 'DIN 6930'];
        } elseif (str_contains($forumName, 'welding')) {
            return ['AWS D1.1', 'ISO 3834', 'ASME IX'];
        }

        return ['ISO 9001', 'ASME'];
    }

    private function getTechnicalDifficulty(string $title): string
    {
        $title = strtolower($title);

        if (str_contains($title, 'người mới') || str_contains($title, 'cơ bản') || str_contains($title, 'bắt đầu')) {
            return 'beginner';
        } elseif (str_contains($title, 'nâng cao') || str_contains($title, 'advanced') || str_contains($title, 'chuyên sâu')) {
            return 'advanced';
        } elseif (str_contains($title, 'chuyên gia') || str_contains($title, 'expert') || str_contains($title, 'phức tạp')) {
            return 'expert';
        } else {
            return 'intermediate';
        }
    }
}
