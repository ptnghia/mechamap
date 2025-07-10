-- MechaMap Database Backup
-- Created: 2025-07-10 10:13:48
-- Database: mechamap_backend
-- Laravel Version: 11.44.7
-- PHP Version: 8.2.12
-- 
-- IMPORTANT: This backup was created before notification system changes
-- Use this backup to restore if any issues occur during implementation
-- 

SET FOREIGN_KEY_CHECKS=0;

-- Structure for table `categories`
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `icon` varchar(500) DEFAULT NULL COMMENT 'URL hoặc class name của icon cho danh mục (material-symbols, ionicons, etc.)',
  `avatar_url` varchar(191) DEFAULT NULL COMMENT 'URL của avatar/logo cho danh mục',
  `avatar_media_id` bigint(20) unsigned DEFAULT NULL COMMENT 'ID của media avatar trong bảng media',
  `banner_url` varchar(191) DEFAULT NULL COMMENT 'URL banner cho danh mục',
  `banner_media_id` bigint(20) unsigned DEFAULT NULL COMMENT 'ID của media banner trong bảng media',
  `color_code` varchar(7) DEFAULT NULL COMMENT 'Mã màu hex cho danh mục (#FF5722 cho Manufacturing, #2196F3 cho CAD/CAM)',
  `meta_description` text DEFAULT NULL COMMENT 'Mô tả SEO cho danh mục',
  `meta_keywords` text DEFAULT NULL COMMENT 'Keywords SEO cho danh mục kỹ thuật',
  `is_technical` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Danh mục kỹ thuật yêu cầu expertise hay thảo luận chung',
  `expertise_level` enum('beginner','intermediate','advanced','expert') DEFAULT NULL COMMENT 'Cấp độ chuyên môn được khuyến nghị cho danh mục',
  `requires_verification` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Yêu cầu verification từ expert để post trong danh mục này',
  `allowed_file_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Các loại file được phép upload: ["dwg","step","iges","pdf","doc","jpg"]' CHECK (json_valid(`allowed_file_types`)),
  `thread_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Số lượng thread trong danh mục (cached)',
  `post_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Tổng số bài post trong danh mục (cached)',
  `last_activity_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian hoạt động cuối cùng trong danh mục',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Danh mục có đang hoạt động không',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Thứ tự sắp xếp danh mục (thay thế cho order)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_hierarchy_order_index` (`parent_id`,`order`),
  KEY `categories_active_sort_index` (`is_active`,`sort_order`),
  KEY `categories_technical_level_index` (`is_technical`,`expertise_level`),
  KEY `categories_active_hierarchy_index` (`parent_id`,`is_active`,`sort_order`),
  KEY `categories_activity_stats_index` (`thread_count`,`last_activity_at`),
  KEY `categories_search_index` (`is_active`,`name`),
  KEY `categories_order_index` (`order`),
  KEY `categories_avatar_media_id_foreign` (`avatar_media_id`),
  KEY `categories_banner_media_id_foreign` (`banner_media_id`),
  FULLTEXT KEY `categories_fulltext_search` (`name`,`description`,`meta_keywords`),
  CONSTRAINT `categories_avatar_media_id_foreign` FOREIGN KEY (`avatar_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  CONSTRAINT `categories_banner_media_id_foreign` FOREIGN KEY (`banner_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data for table `categories`
INSERT INTO `categories` VALUES ('1', 'Thiết kế Cơ khí', 'thiet-ke-co-khi', 'Thảo luận về thiết kế sản phẩm cơ khí, nguyên lý hoạt động, và phương pháp tính toán thiết kế', NULL, '0', 'https://api.iconify.design/material-symbols:engineering.svg', NULL, NULL, NULL, NULL, '#1976D2', 'Cộng đồng thiết kế cơ khí - Chia sẻ kinh nghiệm thiết kế, tính toán và phân tích kỹ thuật', 'thiết kế cơ khí, mechanical design, CAD, kỹ thuật cơ khí, tính toán thiết kế', '1', 'intermediate', '0', '\"[\\\"dwg\\\",\\\"step\\\",\\\"iges\\\",\\\"pdf\\\",\\\"doc\\\",\\\"jpg\\\",\\\"png\\\"]\"', '0', '0', NULL, '1', '1', '2025-06-25 17:57:14', '2025-06-25 17:57:14');
INSERT INTO `categories` VALUES ('2', 'CAD/CAM Software', 'cad-cam-software', 'Thảo luận về AutoCAD, SolidWorks, CATIA, Fusion 360, và các phần mềm CAD/CAM khác', '1', '0', 'https://api.iconify.design/material-symbols:topic.svg', NULL, NULL, NULL, NULL, '#2196F3', 'Thảo luận về CAD/CAM Software - Thảo luận về AutoCAD, SolidWorks, CATIA, Fusion 360, và các phần mềm CAD/CAM khác', 'CAD/CAM Software, thiết kế cơ khí, mechanical design, CAD, kỹ thuật cơ khí, tính toán thiết kế', '1', 'beginner', '0', '\"[\\\"dwg\\\",\\\"step\\\",\\\"iges\\\",\\\"ipt\\\",\\\"sldprt\\\",\\\"f3d\\\"]\"', '0', '0', NULL, '1', '0', '2025-06-25 17:57:14', '2025-06-25 17:57:14');
INSERT INTO `categories` VALUES ('3', 'Phân tích FEA/CFD', 'phan-tich-fea-cfd', 'Finite Element Analysis, Computational Fluid Dynamics với ANSYS, Abaqus, SolidWorks Simulation', '1', '0', 'https://api.iconify.design/material-symbols:topic.svg', NULL, NULL, NULL, NULL, '#3F51B5', 'Thảo luận về Phân tích FEA/CFD - Finite Element Analysis, Computational Fluid Dynamics với ANSYS, Abaqus, SolidWorks Simulation', 'Phân tích FEA/CFD, thiết kế cơ khí, mechanical design, CAD, kỹ thuật cơ khí, tính toán thiết kế', '1', 'advanced', '1', '\"[\\\"anf\\\",\\\"inp\\\",\\\"cae\\\",\\\"pdf\\\",\\\"xlsx\\\"]\"', '0', '0', NULL, '1', '0', '2025-06-25 17:57:14', '2025-06-25 17:57:14');
INSERT INTO `categories` VALUES ('4', 'Thiết kế máy móc', 'thiet-ke-may-moc', 'Thiết kế hệ thống cơ khí, máy móc công nghiệp, thiết bị tự động', '1', '0', 'https://api.iconify.design/material-symbols:topic.svg', NULL, NULL, NULL, NULL, '#4CAF50', 'Thảo luận về Thiết kế máy móc - Thiết kế hệ thống cơ khí, máy móc công nghiệp, thiết bị tự động', 'Thiết kế máy móc, thiết kế cơ khí, mechanical design, CAD, kỹ thuật cơ khí, tính toán thiết kế', '1', 'intermediate', '0', '\"[\\\"dwg\\\",\\\"step\\\",\\\"iges\\\",\\\"pdf\\\",\\\"doc\\\",\\\"jpg\\\",\\\"png\\\"]\"', '0', '0', NULL, '1', '0', '2025-06-25 17:57:14', '2025-06-25 17:57:14');
INSERT INTO `categories` VALUES ('5', 'Công nghệ Chế tạo', 'cong-nghe-che-tao', 'Các phương pháp gia công, công nghệ sản xuất, quy trình chế tạo trong công nghiệp', NULL, '0', 'https://api.iconify.design/material-symbols:precision-manufacturing.svg', NULL, NULL, NULL, NULL, '#FF5722', 'Cộng đồng công nghệ chế tạo - Chia sẻ kinh nghiệm gia công, CNC, công nghệ sản xuất', 'công nghệ chế tạo, CNC machining, gia công cơ khí, manufacturing technology', '1', 'intermediate', '0', '\"[\\\"nc\\\",\\\"tap\\\",\\\"pdf\\\",\\\"doc\\\",\\\"jpg\\\",\\\"mp4\\\"]\"', '0', '0', NULL, '1', '2', '2025-06-25 17:57:14', '2025-06-25 17:57:14');
INSERT INTO `categories` VALUES ('6', 'CNC Machining', 'cnc-machining', 'Gia công CNC, lập trình G-code, CAM, setup máy và tối ưu thông số gia công', '5', '0', 'https://api.iconify.design/material-symbols:topic.svg', NULL, NULL, NULL, NULL, '#FF7043', 'Thảo luận về CNC Machining - Gia công CNC, lập trình G-code, CAM, setup máy và tối ưu thông số gia công', 'CNC Machining, công nghệ chế tạo, CNC machining, gia công cơ khí, manufacturing technology', '1', 'intermediate', '0', '\"[\\\"nc\\\",\\\"tap\\\",\\\"mpf\\\",\\\"cnc\\\",\\\"pdf\\\"]\"', '0', '0', NULL, '1', '0', '2025-06-25 17:57:14', '2025-06-25 17:57:14');
INSERT INTO `categories` VALUES ('7', 'Gia công truyền thống', 'gia-cong-truyen-thong', 'Gia công trên máy tiện, máy phay, máy bào, máy mài và các phương pháp gia công thủ công', '5', '0', 'https://api.iconify.design/material-symbols:topic.svg', NULL, NULL, NULL, NULL, '#FF8A65', 'Thảo luận về Gia công truyền thống - Gia công trên máy tiện, máy phay, máy bào, máy mài và các phương pháp gia công thủ công', 'Gia công truyền thống, công nghệ chế tạo, CNC machining, gia công cơ khí, manufacturing technology', '1', 'beginner', '0', '\"[\\\"nc\\\",\\\"tap\\\",\\\"pdf\\\",\\\"doc\\\",\\\"jpg\\\",\\\"mp4\\\"]\"', '0', '0', NULL, '1', '0', '2025-06-25 17:57:14', '2025-06-25 17:57:14');
INSERT INTO `categories` VALUES ('8', 'In 3D & Additive Manufacturing', 'in-3d-additive', 'Công nghệ in 3D, SLA, SLS, FDM, và các phương pháp sản xuất cộng dồn', '5', '0', 'https://api.iconify.design/material-symbols:topic.svg', NULL, NULL, NULL, NULL, '#FF9800', 'Thảo luận về In 3D & Additive Manufacturing - Công nghệ in 3D, SLA, SLS, FDM, và các phương pháp sản xuất cộng dồn', 'In 3D & Additive Manufacturing, công nghệ chế tạo, CNC machining, gia công cơ khí, manufacturing technology', '1', 'intermediate', '0', '\"[\\\"stl\\\",\\\"obj\\\",\\\"3mf\\\",\\\"gcode\\\",\\\"pdf\\\"]\"', '0', '0', NULL, '1', '0', '2025-06-25 17:57:14', '2025-06-25 17:57:14');
INSERT INTO `categories` VALUES ('9', 'Vật liệu Kỹ thuật', 'vat-lieu-ky-thuat', 'Thảo luận về tính chất, ứng dụng và lựa chọn các loại vật liệu kỹ thuật', NULL, '0', 'https://api.iconify.design/material-symbols:science.svg', NULL, NULL, NULL, NULL, '#9C27B0', 'Cộng đồng vật liệu kỹ thuật - Nghiên cứu tính chất vật liệu, lựa chọn vật liệu cho thiết kế', 'vật liệu kỹ thuật, engineering materials, kim loại, composite, polymer', '1', 'advanced', '1', '\"[\\\"pdf\\\",\\\"xlsx\\\",\\\"doc\\\",\\\"jpg\\\",\\\"png\\\"]\"', '0', '0', NULL, '1', '3', '2025-06-25 17:57:14', '2025-06-25 17:57:14');
INSERT INTO `categories` VALUES ('10', 'Kim loại & Hợp kim', 'kim-loai-hop-kim', 'Thép, nhôm, đồng, titan và các hợp kim kỹ thuật, xử lý nhiệt', '9', '0', 'https://api.iconify.design/material-symbols:topic.svg', NULL, NULL, NULL, NULL, '#673AB7', 'Thảo luận về Kim loại & Hợp kim - Thép, nhôm, đồng, titan và các hợp kim kỹ thuật, xử lý nhiệt', 'Kim loại & Hợp kim, vật liệu kỹ thuật, engineering materials, kim loại, composite, polymer', '1', 'intermediate', '0', '\"[\\\"pdf\\\",\\\"xlsx\\\",\\\"doc\\\",\\\"jpg\\\",\\\"png\\\"]\"', '0', '0', NULL, '1', '0', '2025-06-25 17:57:14', '2025-06-25 17:57:14');
INSERT INTO `categories` VALUES ('11', 'Polymer & Composite', 'polymer-composite', 'Vật liệu polyme, composite, sợi carbon, sợi thủy tinh', '9', '0', 'https://api.iconify.design/material-symbols:topic.svg', NULL, NULL, NULL, NULL, '#9C27B0', 'Thảo luận về Polymer & Composite - Vật liệu polyme, composite, sợi carbon, sợi thủy tinh', 'Polymer & Composite, vật liệu kỹ thuật, engineering materials, kim loại, composite, polymer', '1', 'advanced', '0', '\"[\\\"pdf\\\",\\\"xlsx\\\",\\\"doc\\\",\\\"jpg\\\",\\\"png\\\"]\"', '0', '0', NULL, '1', '0', '2025-06-25 17:57:14', '2025-06-25 17:57:14');
INSERT INTO `categories` VALUES ('12', 'Vật liệu Smart', 'vat-lieu-smart', 'Vật liệu thông minh, shape memory alloy, piezoelectric materials', '9', '0', 'https://api.iconify.design/material-symbols:topic.svg', NULL, NULL, NULL, NULL, '#E91E63', 'Thảo luận về Vật liệu Smart - Vật liệu thông minh, shape memory alloy, piezoelectric materials', 'Vật liệu Smart, vật liệu kỹ thuật, engineering materials, kim loại, composite, polymer', '1', 'expert', '1', '\"[\\\"pdf\\\",\\\"xlsx\\\",\\\"doc\\\",\\\"jpg\\\",\\\"png\\\"]\"', '0', '0', NULL, '1', '0', '2025-06-25 17:57:14', '2025-06-25 17:57:14');
INSERT INTO `categories` VALUES ('13', 'Tự động hóa & Robotics', 'tu-dong-hoa-robotics', 'Hệ thống tự động hóa, robot công nghiệp, IoT và điều khiển thông minh', NULL, '0', 'https://api.iconify.design/material-symbols:smart-toy-outline.svg', NULL, NULL, NULL, NULL, '#607D8B', 'Cộng đồng tự động hóa & robotics - PLC, HMI, robot công nghiệp, IoT', 'tự động hóa, robotics, PLC, HMI, industrial automation, IoT', '1', 'advanced', '0', '\"[\\\"pdf\\\",\\\"doc\\\",\\\"zip\\\",\\\"jpg\\\",\\\"mp4\\\"]\"', '0', '0', NULL, '1', '4', '2025-06-25 17:57:14', '2025-06-25 17:57:14');
INSERT INTO `categories` VALUES ('14', 'PLC & HMI', 'plc-hmi', 'Lập trình PLC, thiết kế HMI, SCADA, Siemens, Allen-Bradley, Mitsubishi', '13', '0', 'https://api.iconify.design/material-symbols:topic.svg', NULL, NULL, NULL, NULL, '#546E7A', 'Thảo luận về PLC & HMI - Lập trình PLC, thiết kế HMI, SCADA, Siemens, Allen-Bradley, Mitsubishi', 'PLC & HMI, tự động hóa, robotics, PLC, HMI, industrial automation, IoT', '1', 'intermediate', '0', '\"[\\\"pdf\\\",\\\"doc\\\",\\\"zip\\\",\\\"jpg\\\",\\\"mp4\\\"]\"', '0', '0', NULL, '1', '0', '2025-06-25 17:57:14', '2025-06-25 17:57:14');
INSERT INTO `categories` VALUES ('15', 'Robot công nghiệp', 'robot-cong-nghiep', 'Robot 6 trục, lập trình robot, ứng dụng robot trong sản xuất', '13', '0', 'https://api.iconify.design/material-symbols:topic.svg', NULL, NULL, NULL, NULL, '#78909C', 'Thảo luận về Robot công nghiệp - Robot 6 trục, lập trình robot, ứng dụng robot trong sản xuất', 'Robot công nghiệp, tự động hóa, robotics, PLC, HMI, industrial automation, IoT', '1', 'advanced', '0', '\"[\\\"pdf\\\",\\\"doc\\\",\\\"zip\\\",\\\"jpg\\\",\\\"mp4\\\"]\"', '0', '0', NULL, '1', '0', '2025-06-25 17:57:14', '2025-06-25 17:57:14');
INSERT INTO `categories` VALUES ('16', 'Sensors & Actuators', 'sensors-actuators', 'Cảm biến công nghiệp, actuator, servo motor, stepper motor', '13', '0', 'https://api.iconify.design/material-symbols:topic.svg', NULL, NULL, NULL, NULL, '#90A4AE', 'Thảo luận về Sensors & Actuators - Cảm biến công nghiệp, actuator, servo motor, stepper motor', 'Sensors & Actuators, tự động hóa, robotics, PLC, HMI, industrial automation, IoT', '1', 'intermediate', '0', '\"[\\\"pdf\\\",\\\"doc\\\",\\\"zip\\\",\\\"jpg\\\",\\\"mp4\\\"]\"', '0', '0', NULL, '1', '0', '2025-06-25 17:57:14', '2025-06-25 17:57:14');

-- Structure for table `comments`
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `content` text NOT NULL,
  `has_media` tinyint(1) NOT NULL DEFAULT 0,
  `has_code_snippet` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Comment có chứa code/formula/technical calculation không',
  `has_formula` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Comment có chứa công thức toán học/kỹ thuật không',
  `formula_content` text DEFAULT NULL COMMENT 'Nội dung công thức (LaTeX format cho MathJax rendering)',
  `like_count` int(11) NOT NULL DEFAULT 0,
  `dislikes_count` int(11) NOT NULL DEFAULT 0,
  `helpful_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Số lượt đánh giá "hữu ích" cho câu trả lời kỹ thuật',
  `expert_endorsements` int(11) NOT NULL DEFAULT 0 COMMENT 'Số lượng expert ủng hộ câu trả lời này',
  `quality_score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `technical_accuracy_score` decimal(3,2) NOT NULL DEFAULT 0.00 COMMENT 'Điểm độ chính xác kỹ thuật (0.00 - 5.00) do expert đánh giá',
  `verification_status` enum('unverified','pending','verified','disputed') NOT NULL DEFAULT 'unverified' COMMENT 'Trạng thái xác minh: chưa xác minh, chờ xác minh, đã xác minh, có tranh cãi',
  `verified_by` bigint(20) unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian được verify',
  `technical_tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Tags kỹ thuật: ["calculation","design","material","manufacturing"]' CHECK (json_valid(`technical_tags`)),
  `answer_type` enum('general','calculation','reference','experience','tutorial') DEFAULT NULL COMMENT 'Loại câu trả lời: tổng quát, tính toán, tham khảo, kinh nghiệm, hướng dẫn',
  `is_flagged` tinyint(1) NOT NULL DEFAULT 0,
  `is_spam` tinyint(1) NOT NULL DEFAULT 0,
  `is_solution` tinyint(1) NOT NULL DEFAULT 0,
  `reports_count` int(11) NOT NULL DEFAULT 0,
  `edited_at` timestamp NULL DEFAULT NULL,
  `edit_count` int(11) NOT NULL DEFAULT 0,
  `edited_by` bigint(20) unsigned DEFAULT NULL,
  `edit_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_verified_by_foreign` (`verified_by`),
  KEY `comments_edited_by_foreign` (`edited_by`),
  KEY `comments_thread_hierarchy` (`thread_id`,`parent_id`),
  KEY `comments_thread_timeline` (`thread_id`,`created_at`),
  KEY `comments_reply_timeline` (`parent_id`,`created_at`),
  KEY `comments_user_activity` (`user_id`,`created_at`),
  KEY `comments_moderation_flagged` (`is_flagged`),
  KEY `comments_moderation_spam` (`is_spam`),
  KEY `comments_solution_tracking` (`is_solution`),
  KEY `comments_quality_ranking` (`quality_score`),
  KEY `comments_edit_history` (`edited_at`),
  KEY `comments_expert_verification` (`verification_status`,`technical_accuracy_score`),
  KEY `comments_technical_content` (`has_code_snippet`,`has_formula`),
  KEY `comments_helpfulness_ranking` (`helpful_count`,`expert_endorsements`),
  KEY `comments_answer_classification` (`answer_type`,`created_at`),
  KEY `comments_verified_solutions` (`thread_id`,`verification_status`,`is_solution`),
  KEY `comments_quality_search` (`answer_type`,`technical_accuracy_score`,`helpful_count`),
  KEY `comments_like_count_created_at_index` (`like_count`,`created_at`),
  FULLTEXT KEY `comments_content_search` (`content`),
  CONSTRAINT `comments_edited_by_foreign` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `comments_thread_id_foreign` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=360 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data for table `comments`
INSERT INTO `comments` VALUES ('1', '1', '27', NULL, 'Cảm ơn bạn đã chia sẻ! Mình cũng gặp vấn đề tương tự với large assemblies. Tip về SpeedPak rất hữu ích, đã thử và thấy performance cải thiện đáng kể.

Thêm một tip nữa: sử dụng **Lightweight mode** cho các components không cần edit thường xuyên. Có thể tiết kiệm đến 60% memory usage.', '0', '0', '0', NULL, '1', '2', '5', '2', '3.23', '4.10', 'pending', NULL, NULL, '\"[\\\"solidworks\\\",\\\"performance\\\",\\\"assembly\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-15 05:50:50', '2025-05-15 05:50:50', NULL);
INSERT INTO `comments` VALUES ('2', '1', '15', NULL, 'Về vấn đề **Graphics Settings**, mình khuyên nên:

1. **Image Quality**: Giảm xuống Medium cho assemblies > 500 parts
2. **RealView**: Chỉ bật khi cần render presentation
3. **Anti-aliasing**: Tắt hoàn toàn khi modeling

Với setup này, máy i7-8700K + GTX 1060 của mình handle được assembly 2000+ parts khá mượt.', '0', '0', '0', NULL, '21', '2', '0', '3', '4.74', '3.73', 'unverified', '15', NULL, '\"[\\\"solidworks\\\",\\\"graphics\\\",\\\"optimization\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '0', NULL, NULL, '2025-06-25 11:56:10', '2025-06-25 11:56:10', NULL);
INSERT INTO `comments` VALUES ('3', '1', '7', '2', 'Thông tin rất hữu ích. Có thể bạn elaborate thêm về phần implementation không?', '0', '1', '0', NULL, '5', '1', '0', '0', '4.23', '4.50', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-30 00:10:10', '2025-06-30 00:10:10', NULL);
INSERT INTO `comments` VALUES ('4', '2', '10', NULL, 'Cảm ơn bạn đã chia sẻ! Mình cũng gặp vấn đề tương tự với large assemblies. Tip về SpeedPak rất hữu ích, đã thử và thấy performance cải thiện đáng kể.

Thêm một tip nữa: sử dụng **Lightweight mode** cho các components không cần edit thường xuyên. Có thể tiết kiệm đến 60% memory usage.', '0', '0', '0', NULL, '13', '3', '1', '0', '4.88', '4.91', 'unverified', NULL, NULL, '\"[\\\"solidworks\\\",\\\"performance\\\",\\\"assembly\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-29 08:20:06', '2025-06-29 08:20:06', NULL);
INSERT INTO `comments` VALUES ('5', '2', '11', NULL, 'Về vấn đề **Graphics Settings**, mình khuyên nên:

1. **Image Quality**: Giảm xuống Medium cho assemblies > 500 parts
2. **RealView**: Chỉ bật khi cần render presentation
3. **Anti-aliasing**: Tắt hoàn toàn khi modeling

Với setup này, máy i7-8700K + GTX 1060 của mình handle được assembly 2000+ parts khá mượt.', '0', '0', '0', NULL, '2', '1', '10', '3', '4.22', '3.94', 'unverified', NULL, '2025-06-15 17:57:51', '\"[\\\"solidworks\\\",\\\"graphics\\\",\\\"optimization\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '2', NULL, NULL, '2025-05-31 09:49:44', '2025-05-31 09:49:44', NULL);
INSERT INTO `comments` VALUES ('6', '2', '23', '5', 'Mình đã thử method này và thấy kết quả khá tốt. Recommend!', '0', '0', '0', NULL, '7', '1', '8', '0', '3.26', '4.33', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-15 19:24:08', '2025-06-15 19:24:08', NULL);
INSERT INTO `comments` VALUES ('7', '3', '14', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '18', '2', '10', '3', '3.48', '3.84', 'unverified', '2', '2025-06-23 17:57:51', '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-25 09:50:46', '2025-06-25 09:50:46', NULL);
INSERT INTO `comments` VALUES ('8', '4', '26', NULL, 'Cảm ơn bạn đã chia sẻ! Mình cũng gặp vấn đề tương tự với large assemblies. Tip về SpeedPak rất hữu ích, đã thử và thấy performance cải thiện đáng kể.

Thêm một tip nữa: sử dụng **Lightweight mode** cho các components không cần edit thường xuyên. Có thể tiết kiệm đến 60% memory usage.', '0', '0', '0', NULL, '24', '5', '13', '1', '3.02', '4.51', 'unverified', '18', NULL, '\"[\\\"solidworks\\\",\\\"performance\\\",\\\"assembly\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-29 11:29:41', '2025-06-29 11:29:41', NULL);
INSERT INTO `comments` VALUES ('9', '4', '32', NULL, 'Về vấn đề **Graphics Settings**, mình khuyên nên:

1. **Image Quality**: Giảm xuống Medium cho assemblies > 500 parts
2. **RealView**: Chỉ bật khi cần render presentation
3. **Anti-aliasing**: Tắt hoàn toàn khi modeling

Với setup này, máy i7-8700K + GTX 1060 của mình handle được assembly 2000+ parts khá mượt.', '0', '0', '0', NULL, '17', '4', '0', '3', '3.90', '3.93', 'unverified', NULL, NULL, '\"[\\\"solidworks\\\",\\\"graphics\\\",\\\"optimization\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '2', NULL, NULL, '2025-06-29 01:21:56', '2025-06-29 01:21:56', NULL);
INSERT INTO `comments` VALUES ('10', '4', '4', NULL, 'Bổ sung thêm về **Hardware Optimization**:

- **RAM**: 32GB là sweet spot, 64GB chỉ cần thiết cho assemblies > 5000 parts
- **Storage**: NVMe SSD cho OS + SolidWorks, SATA SSD cho project files
- **Graphics**: Quadro RTX 4000 trở lên cho professional work

Đặc biệt chú ý **Page File** settings - nên set manual 1.5x RAM size.', '0', '0', '0', NULL, '20', '0', '15', '3', '4.31', '4.21', 'unverified', NULL, NULL, '\"[\\\"hardware\\\",\\\"performance\\\",\\\"system\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-29 12:38:36', '2025-06-29 12:38:36', NULL);
INSERT INTO `comments` VALUES ('11', '4', '14', '10', 'Interesting approach! Có case study nào cụ thể không?', '0', '1', '0', NULL, '10', '2', '8', '1', '4.36', '3.20', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-30 02:18:33', '2025-06-30 02:18:33', NULL);
INSERT INTO `comments` VALUES ('12', '5', '15', NULL, 'Excellent tips! Về **Mesh Quality**, mình thường check:

1. **Aspect Ratio** < 3:1 cho structural analysis
2. **Skewness** < 0.7 (tốt nhất < 0.5)
3. **Jacobian** > 0.6

Với ANSYS Mechanical, **Patch Conforming Method** cho geometry phức tạp, **Patch Independent** cho simple parts.', '0', '0', '0', NULL, '7', '4', '4', '2', '4.96', '4.49', 'unverified', NULL, NULL, '\"[\\\"fea\\\",\\\"meshing\\\",\\\"ansys\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '2', NULL, NULL, '2025-06-28 19:12:25', '2025-06-28 19:12:25', NULL);
INSERT INTO `comments` VALUES ('13', '5', '18', NULL, 'Về **Boundary Conditions**, một số lưu ý:

- **Fixed Support**: Chỉ dùng khi thực sự cần thiết
- **Remote Displacement**: Tốt hơn cho bolt connections
- **Contact**: Bonded vs Frictional vs Frictionless

**Von Mises Stress** công thức:
σ_vm = √[(σ₁-σ₂)² + (σ₂-σ₃)² + (σ₃-σ₁)²]/√2', '0', '0', '1', '\\sigma_{vm} = \\sqrt{\\frac{(\\sigma_1-\\sigma_2)^2 + (\\sigma_2-\\sigma_3)^2 + (\\sigma_3-\\sigma_1)^2}{2}}', '23', '1', '2', '0', '4.38', '3.65', 'unverified', '34', NULL, '\"[\\\"stress-analysis\\\",\\\"boundary-conditions\\\",\\\"theory\\\"]\"', 'calculation', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-28 21:57:42', '2025-06-28 21:57:42', NULL);
INSERT INTO `comments` VALUES ('14', '5', '32', '12', 'Interesting approach! Có case study nào cụ thể không?', '0', '0', '0', NULL, '9', '1', '3', '0', '3.07', '3.58', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-29 23:59:41', '2025-06-29 23:59:41', NULL);
INSERT INTO `comments` VALUES ('15', '6', '12', NULL, 'Excellent tips! Về **Mesh Quality**, mình thường check:

1. **Aspect Ratio** < 3:1 cho structural analysis
2. **Skewness** < 0.7 (tốt nhất < 0.5)
3. **Jacobian** > 0.6

Với ANSYS Mechanical, **Patch Conforming Method** cho geometry phức tạp, **Patch Independent** cho simple parts.', '0', '0', '0', NULL, '22', '2', '11', '1', '3.14', '4.20', 'unverified', NULL, NULL, '\"[\\\"fea\\\",\\\"meshing\\\",\\\"ansys\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '1', NULL, NULL, '2025-06-10 13:20:28', '2025-06-10 13:20:28', NULL);
INSERT INTO `comments` VALUES ('16', '6', '33', NULL, 'Về **Boundary Conditions**, một số lưu ý:

- **Fixed Support**: Chỉ dùng khi thực sự cần thiết
- **Remote Displacement**: Tốt hơn cho bolt connections
- **Contact**: Bonded vs Frictional vs Frictionless

**Von Mises Stress** công thức:
σ_vm = √[(σ₁-σ₂)² + (σ₂-σ₃)² + (σ₃-σ₁)²]/√2', '0', '0', '1', '\\sigma_{vm} = \\sqrt{\\frac{(\\sigma_1-\\sigma_2)^2 + (\\sigma_2-\\sigma_3)^2 + (\\sigma_3-\\sigma_1)^2}{2}}', '22', '5', '1', '3', '3.64', '3.61', 'unverified', NULL, NULL, '\"[\\\"stress-analysis\\\",\\\"boundary-conditions\\\",\\\"theory\\\"]\"', 'calculation', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-11 22:49:12', '2025-06-11 22:49:12', NULL);
INSERT INTO `comments` VALUES ('17', '6', '32', '15', 'Upvoted! Đây chính xác là thông tin mình cần.', '0', '0', '0', NULL, '8', '2', '2', '0', '3.97', '3.41', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-20 02:01:19', '2025-06-20 02:01:19', NULL);
INSERT INTO `comments` VALUES ('18', '7', '25', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '15', '5', '11', '0', '4.12', '4.18', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-06 00:58:34', '2025-06-06 00:58:34', NULL);
INSERT INTO `comments` VALUES ('19', '7', '11', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '2', '4', '10', '0', '4.79', '4.43', 'pending', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-05-12 12:14:28', '2025-05-12 12:14:28', NULL);
INSERT INTO `comments` VALUES ('20', '7', '5', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '4', '2', '5', '3', '4.31', '3.50', 'verified', '25', NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-12 17:58:52', '2025-05-12 17:58:52', NULL);
INSERT INTO `comments` VALUES ('21', '8', '25', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '12', '1', '15', '2', '3.22', '4.65', 'verified', NULL, '2025-06-24 17:57:51', '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-27 07:54:22', '2025-06-27 07:54:22', NULL);
INSERT INTO `comments` VALUES ('22', '9', '6', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '1', '1', '6', '3', '4.84', '4.82', 'unverified', '32', NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-24 14:32:54', '2025-06-24 14:32:54', NULL);
INSERT INTO `comments` VALUES ('23', '9', '9', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '12', '3', '1', '0', '4.95', '4.18', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-17 21:46:30', '2025-06-17 21:46:30', NULL);
INSERT INTO `comments` VALUES ('24', '9', '23', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '8', '0', '4', '3', '4.59', '5.00', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-26 22:44:51', '2025-06-26 22:44:51', NULL);
INSERT INTO `comments` VALUES ('25', '9', '23', '23', 'Cảm ơn bạn đã chia sẻ! Mình sẽ thử approach này.', '0', '1', '0', NULL, '10', '1', '1', '0', '4.12', '3.37', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-27 19:35:18', '2025-06-27 19:35:18', NULL);
INSERT INTO `comments` VALUES ('26', '10', '15', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '7', '1', '0', '1', '4.94', '4.20', 'pending', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-07-01 08:44:05', '2025-07-01 08:44:05', NULL);
INSERT INTO `comments` VALUES ('27', '10', '12', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '17', '1', '3', '2', '3.75', '4.63', 'unverified', '3', NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-30 12:47:01', '2025-06-30 12:47:01', NULL);
INSERT INTO `comments` VALUES ('28', '10', '5', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '3', '4', '0', '2', '4.05', '4.71', 'verified', '12', '2025-06-15 17:57:51', '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-29 18:08:08', '2025-06-29 18:08:08', NULL);
INSERT INTO `comments` VALUES ('29', '10', '22', '26', 'Interesting approach! Có case study nào cụ thể không?', '0', '0', '0', NULL, '8', '1', '2', '0', '3.04', '3.44', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-30 09:07:37', '2025-06-30 09:07:37', NULL);
INSERT INTO `comments` VALUES ('30', '10', '8', '27', 'Agree với points này. Đặc biệt là phần về cost-benefit analysis.', '0', '0', '0', NULL, '2', '0', '2', '1', '4.48', '3.28', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-01 16:26:16', '2025-07-01 16:26:16', NULL);
INSERT INTO `comments` VALUES ('31', '10', '9', '28', 'Perfect timing! Mình đang research về topic này.', '0', '0', '0', NULL, '4', '2', '2', '0', '2.59', '3.77', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-01 07:17:14', '2025-07-01 07:17:14', NULL);
INSERT INTO `comments` VALUES ('32', '11', '31', NULL, 'Cảm ơn bạn đã chia sẻ! Mình cũng gặp vấn đề tương tự với large assemblies. Tip về SpeedPak rất hữu ích, đã thử và thấy performance cải thiện đáng kể.

Thêm một tip nữa: sử dụng **Lightweight mode** cho các components không cần edit thường xuyên. Có thể tiết kiệm đến 60% memory usage.', '0', '0', '0', NULL, '25', '1', '6', '3', '3.17', '4.99', 'unverified', '6', NULL, '\"[\\\"solidworks\\\",\\\"performance\\\",\\\"assembly\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-07-01 02:05:01', '2025-07-01 02:05:01', NULL);
INSERT INTO `comments` VALUES ('33', '11', '14', NULL, 'Về vấn đề **Graphics Settings**, mình khuyên nên:

1. **Image Quality**: Giảm xuống Medium cho assemblies > 500 parts
2. **RealView**: Chỉ bật khi cần render presentation
3. **Anti-aliasing**: Tắt hoàn toàn khi modeling

Với setup này, máy i7-8700K + GTX 1060 của mình handle được assembly 2000+ parts khá mượt.', '0', '0', '0', NULL, '24', '2', '1', '0', '4.27', '4.85', 'verified', NULL, NULL, '\"[\\\"solidworks\\\",\\\"graphics\\\",\\\"optimization\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '2', NULL, NULL, '2025-06-30 05:27:21', '2025-06-30 05:27:21', NULL);
INSERT INTO `comments` VALUES ('34', '11', '32', NULL, 'Bổ sung thêm về **Hardware Optimization**:

- **RAM**: 32GB là sweet spot, 64GB chỉ cần thiết cho assemblies > 5000 parts
- **Storage**: NVMe SSD cho OS + SolidWorks, SATA SSD cho project files
- **Graphics**: Quadro RTX 4000 trở lên cho professional work

Đặc biệt chú ý **Page File** settings - nên set manual 1.5x RAM size.', '0', '0', '0', NULL, '10', '3', '1', '2', '4.95', '4.22', 'verified', NULL, NULL, '\"[\\\"hardware\\\",\\\"performance\\\",\\\"system\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-29 21:38:46', '2025-06-29 21:38:46', NULL);
INSERT INTO `comments` VALUES ('35', '11', '2', '33', 'Bổ sung thêm: cần chú ý về safety requirements khi implement.', '0', '0', '0', NULL, '5', '2', '2', '1', '3.60', '3.16', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-01 04:51:24', '2025-07-01 04:51:24', NULL);
INSERT INTO `comments` VALUES ('36', '12', '28', NULL, 'Cảm ơn bạn đã chia sẻ! Mình cũng gặp vấn đề tương tự với large assemblies. Tip về SpeedPak rất hữu ích, đã thử và thấy performance cải thiện đáng kể.

Thêm một tip nữa: sử dụng **Lightweight mode** cho các components không cần edit thường xuyên. Có thể tiết kiệm đến 60% memory usage.', '0', '0', '0', NULL, '4', '4', '14', '1', '4.41', '4.70', 'unverified', NULL, '2025-06-19 17:57:51', '\"[\\\"solidworks\\\",\\\"performance\\\",\\\"assembly\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-28 19:27:11', '2025-06-28 19:27:11', NULL);
INSERT INTO `comments` VALUES ('37', '12', '25', NULL, 'Về vấn đề **Graphics Settings**, mình khuyên nên:

1. **Image Quality**: Giảm xuống Medium cho assemblies > 500 parts
2. **RealView**: Chỉ bật khi cần render presentation
3. **Anti-aliasing**: Tắt hoàn toàn khi modeling

Với setup này, máy i7-8700K + GTX 1060 của mình handle được assembly 2000+ parts khá mượt.', '0', '0', '0', NULL, '12', '5', '4', '1', '4.11', '4.30', 'pending', NULL, NULL, '\"[\\\"solidworks\\\",\\\"graphics\\\",\\\"optimization\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '0', NULL, NULL, '2025-07-01 17:05:56', '2025-07-01 17:05:56', NULL);
INSERT INTO `comments` VALUES ('38', '12', '17', NULL, 'Bổ sung thêm về **Hardware Optimization**:

- **RAM**: 32GB là sweet spot, 64GB chỉ cần thiết cho assemblies > 5000 parts
- **Storage**: NVMe SSD cho OS + SolidWorks, SATA SSD cho project files
- **Graphics**: Quadro RTX 4000 trở lên cho professional work

Đặc biệt chú ý **Page File** settings - nên set manual 1.5x RAM size.', '0', '0', '0', NULL, '17', '0', '12', '2', '3.12', '4.96', 'unverified', NULL, NULL, '\"[\\\"hardware\\\",\\\"performance\\\",\\\"system\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-27 17:41:49', '2025-06-27 17:41:49', NULL);
INSERT INTO `comments` VALUES ('39', '12', '27', '37', 'Bổ sung thêm: cần chú ý về safety requirements khi implement.', '0', '1', '0', NULL, '7', '0', '8', '0', '3.21', '3.67', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-01 16:24:24', '2025-07-01 16:24:24', NULL);
INSERT INTO `comments` VALUES ('40', '13', '17', NULL, 'Cảm ơn bạn đã chia sẻ! Mình cũng gặp vấn đề tương tự với large assemblies. Tip về SpeedPak rất hữu ích, đã thử và thấy performance cải thiện đáng kể.

Thêm một tip nữa: sử dụng **Lightweight mode** cho các components không cần edit thường xuyên. Có thể tiết kiệm đến 60% memory usage.', '0', '0', '0', NULL, '17', '4', '5', '0', '4.80', '4.20', 'verified', NULL, NULL, '\"[\\\"solidworks\\\",\\\"performance\\\",\\\"assembly\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-09 03:11:54', '2025-05-09 03:11:54', NULL);
INSERT INTO `comments` VALUES ('41', '13', '4', NULL, 'Về vấn đề **Graphics Settings**, mình khuyên nên:

1. **Image Quality**: Giảm xuống Medium cho assemblies > 500 parts
2. **RealView**: Chỉ bật khi cần render presentation
3. **Anti-aliasing**: Tắt hoàn toàn khi modeling

Với setup này, máy i7-8700K + GTX 1060 của mình handle được assembly 2000+ parts khá mượt.', '0', '0', '0', NULL, '6', '4', '11', '2', '4.41', '4.86', 'unverified', NULL, NULL, '\"[\\\"solidworks\\\",\\\"graphics\\\",\\\"optimization\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '1', NULL, NULL, '2025-06-30 12:04:41', '2025-06-30 12:04:41', NULL);
INSERT INTO `comments` VALUES ('42', '13', '2', NULL, 'Bổ sung thêm về **Hardware Optimization**:

- **RAM**: 32GB là sweet spot, 64GB chỉ cần thiết cho assemblies > 5000 parts
- **Storage**: NVMe SSD cho OS + SolidWorks, SATA SSD cho project files
- **Graphics**: Quadro RTX 4000 trở lên cho professional work

Đặc biệt chú ý **Page File** settings - nên set manual 1.5x RAM size.', '0', '0', '0', NULL, '17', '4', '10', '1', '3.94', '4.08', 'verified', NULL, NULL, '\"[\\\"hardware\\\",\\\"performance\\\",\\\"system\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-09 04:05:55', '2025-05-09 04:05:55', NULL);
INSERT INTO `comments` VALUES ('43', '13', '32', '41', 'Thông tin rất hữu ích. Có thể bạn elaborate thêm về phần implementation không?', '0', '0', '0', NULL, '0', '2', '0', '0', '4.35', '3.79', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-14 21:50:03', '2025-05-14 21:50:03', NULL);
INSERT INTO `comments` VALUES ('44', '14', '25', NULL, 'Cảm ơn bạn đã chia sẻ! Mình cũng gặp vấn đề tương tự với large assemblies. Tip về SpeedPak rất hữu ích, đã thử và thấy performance cải thiện đáng kể.

Thêm một tip nữa: sử dụng **Lightweight mode** cho các components không cần edit thường xuyên. Có thể tiết kiệm đến 60% memory usage.', '0', '0', '0', NULL, '6', '1', '3', '3', '3.27', '4.72', 'unverified', NULL, '2025-06-17 17:57:51', '\"[\\\"solidworks\\\",\\\"performance\\\",\\\"assembly\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-16 09:27:31', '2025-06-16 09:27:31', NULL);
INSERT INTO `comments` VALUES ('45', '14', '31', NULL, 'Về vấn đề **Graphics Settings**, mình khuyên nên:

1. **Image Quality**: Giảm xuống Medium cho assemblies > 500 parts
2. **RealView**: Chỉ bật khi cần render presentation
3. **Anti-aliasing**: Tắt hoàn toàn khi modeling

Với setup này, máy i7-8700K + GTX 1060 của mình handle được assembly 2000+ parts khá mượt.', '0', '0', '0', NULL, '21', '3', '3', '3', '3.25', '3.60', 'pending', '11', NULL, '\"[\\\"solidworks\\\",\\\"graphics\\\",\\\"optimization\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '2', NULL, NULL, '2025-06-27 01:46:22', '2025-06-27 01:46:22', NULL);
INSERT INTO `comments` VALUES ('46', '14', '4', NULL, 'Bổ sung thêm về **Hardware Optimization**:

- **RAM**: 32GB là sweet spot, 64GB chỉ cần thiết cho assemblies > 5000 parts
- **Storage**: NVMe SSD cho OS + SolidWorks, SATA SSD cho project files
- **Graphics**: Quadro RTX 4000 trở lên cho professional work

Đặc biệt chú ý **Page File** settings - nên set manual 1.5x RAM size.', '0', '0', '0', NULL, '1', '5', '5', '3', '4.98', '4.91', 'verified', NULL, NULL, '\"[\\\"hardware\\\",\\\"performance\\\",\\\"system\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-09 18:15:27', '2025-06-09 18:15:27', NULL);
INSERT INTO `comments` VALUES ('47', '15', '9', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '2', '5', '3', '3', '4.30', '4.66', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-25 02:05:18', '2025-06-25 02:05:18', NULL);
INSERT INTO `comments` VALUES ('48', '15', '15', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '22', '3', '4', '3', '3.98', '4.04', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-07-01 14:39:47', '2025-07-01 14:39:47', NULL);
INSERT INTO `comments` VALUES ('49', '15', '12', '47', 'Agree với points này. Đặc biệt là phần về cost-benefit analysis.', '0', '0', '0', NULL, '0', '2', '5', '1', '3.87', '4.40', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-27 01:22:52', '2025-06-27 01:22:52', NULL);
INSERT INTO `comments` VALUES ('50', '16', '13', NULL, 'Cảm ơn bạn đã chia sẻ! Mình cũng gặp vấn đề tương tự với large assemblies. Tip về SpeedPak rất hữu ích, đã thử và thấy performance cải thiện đáng kể.

Thêm một tip nữa: sử dụng **Lightweight mode** cho các components không cần edit thường xuyên. Có thể tiết kiệm đến 60% memory usage.', '0', '0', '0', NULL, '3', '5', '9', '2', '4.78', '4.95', 'unverified', NULL, NULL, '\"[\\\"solidworks\\\",\\\"performance\\\",\\\"assembly\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-05-23 16:08:35', '2025-05-23 16:08:35', NULL);
INSERT INTO `comments` VALUES ('51', '16', '21', NULL, 'Về vấn đề **Graphics Settings**, mình khuyên nên:

1. **Image Quality**: Giảm xuống Medium cho assemblies > 500 parts
2. **RealView**: Chỉ bật khi cần render presentation
3. **Anti-aliasing**: Tắt hoàn toàn khi modeling

Với setup này, máy i7-8700K + GTX 1060 của mình handle được assembly 2000+ parts khá mượt.', '0', '0', '0', NULL, '10', '0', '6', '2', '3.84', '3.64', 'pending', '3', NULL, '\"[\\\"solidworks\\\",\\\"graphics\\\",\\\"optimization\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '1', NULL, NULL, '2025-05-22 21:11:54', '2025-05-22 21:11:54', NULL);
INSERT INTO `comments` VALUES ('52', '16', '4', NULL, 'Bổ sung thêm về **Hardware Optimization**:

- **RAM**: 32GB là sweet spot, 64GB chỉ cần thiết cho assemblies > 5000 parts
- **Storage**: NVMe SSD cho OS + SolidWorks, SATA SSD cho project files
- **Graphics**: Quadro RTX 4000 trở lên cho professional work

Đặc biệt chú ý **Page File** settings - nên set manual 1.5x RAM size.', '0', '0', '0', NULL, '5', '2', '8', '1', '3.15', '4.32', 'verified', '15', NULL, '\"[\\\"hardware\\\",\\\"performance\\\",\\\"system\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-14 07:55:13', '2025-06-14 07:55:13', NULL);
INSERT INTO `comments` VALUES ('53', '17', '24', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '6', '5', '0', '1', '3.25', '4.30', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-27 21:08:04', '2025-06-27 21:08:04', NULL);
INSERT INTO `comments` VALUES ('54', '17', '32', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '20', '4', '2', '2', '3.10', '4.09', 'unverified', '17', '2025-06-21 17:57:51', '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-30 09:16:18', '2025-06-30 09:16:18', NULL);
INSERT INTO `comments` VALUES ('55', '17', '24', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '0', '4', '13', '0', '4.49', '4.66', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-25 15:59:35', '2025-06-25 15:59:35', NULL);
INSERT INTO `comments` VALUES ('56', '17', '18', '53', 'Mình đã thử method này và thấy kết quả khá tốt. Recommend!', '0', '0', '0', NULL, '8', '2', '7', '1', '3.10', '3.03', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-22 05:20:48', '2025-06-22 05:20:48', NULL);
INSERT INTO `comments` VALUES ('57', '17', '32', '54', 'Agree với points này. Đặc biệt là phần về cost-benefit analysis.', '0', '1', '0', NULL, '3', '2', '6', '0', '3.94', '4.20', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-26 05:18:29', '2025-06-26 05:18:29', NULL);
INSERT INTO `comments` VALUES ('58', '18', '17', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '15', '3', '9', '1', '4.58', '4.07', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-19 00:15:57', '2025-06-19 00:15:57', NULL);
INSERT INTO `comments` VALUES ('59', '18', '2', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '6', '5', '8', '2', '4.67', '3.53', 'pending', '1', '2025-06-20 17:57:51', '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-31 18:34:57', '2025-05-31 18:34:57', NULL);
INSERT INTO `comments` VALUES ('60', '18', '19', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '20', '5', '3', '0', '3.58', '3.55', 'unverified', '26', '2025-06-21 17:57:51', '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-15 19:22:32', '2025-05-15 19:22:32', NULL);
INSERT INTO `comments` VALUES ('61', '19', '23', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '23', '5', '3', '1', '4.49', '4.82', 'unverified', NULL, '2025-06-18 17:57:51', '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-01 23:43:47', '2025-07-01 23:43:47', NULL);
INSERT INTO `comments` VALUES ('62', '19', '15', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '0', '1', '14', '1', '4.57', '3.65', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-30 08:33:02', '2025-06-30 08:33:02', NULL);
INSERT INTO `comments` VALUES ('63', '19', '12', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '11', '0', '4', '1', '4.18', '4.56', 'unverified', NULL, '2025-06-17 17:57:51', '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-07-01 20:02:00', '2025-07-01 20:02:00', NULL);
INSERT INTO `comments` VALUES ('64', '20', '24', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '20', '3', '6', '1', '4.26', '3.55', 'verified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-21 13:59:03', '2025-06-21 13:59:03', NULL);
INSERT INTO `comments` VALUES ('65', '20', '17', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '21', '2', '4', '3', '3.81', '4.47', 'verified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-20 23:22:54', '2025-06-20 23:22:54', NULL);
INSERT INTO `comments` VALUES ('66', '20', '3', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '20', '5', '6', '0', '3.07', '4.16', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-05 09:10:01', '2025-06-05 09:10:01', NULL);
INSERT INTO `comments` VALUES ('67', '20', '23', '66', 'Thông tin rất hữu ích. Có thể bạn elaborate thêm về phần implementation không?', '0', '0', '0', NULL, '5', '0', '6', '0', '2.85', '3.42', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-14 00:36:22', '2025-06-14 00:36:22', NULL);
INSERT INTO `comments` VALUES ('68', '21', '2', NULL, 'Excellent tips! Về **Mesh Quality**, mình thường check:

1. **Aspect Ratio** < 3:1 cho structural analysis
2. **Skewness** < 0.7 (tốt nhất < 0.5)
3. **Jacobian** > 0.6

Với ANSYS Mechanical, **Patch Conforming Method** cho geometry phức tạp, **Patch Independent** cho simple parts.', '0', '0', '0', NULL, '19', '0', '1', '0', '4.46', '4.23', 'pending', NULL, NULL, '\"[\\\"fea\\\",\\\"meshing\\\",\\\"ansys\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '2', NULL, NULL, '2025-06-21 02:25:23', '2025-06-21 02:25:23', NULL);
INSERT INTO `comments` VALUES ('69', '21', '8', NULL, 'Về **Boundary Conditions**, một số lưu ý:

- **Fixed Support**: Chỉ dùng khi thực sự cần thiết
- **Remote Displacement**: Tốt hơn cho bolt connections
- **Contact**: Bonded vs Frictional vs Frictionless

**Von Mises Stress** công thức:
σ_vm = √[(σ₁-σ₂)² + (σ₂-σ₃)² + (σ₃-σ₁)²]/√2', '0', '0', '1', '\\sigma_{vm} = \\sqrt{\\frac{(\\sigma_1-\\sigma_2)^2 + (\\sigma_2-\\sigma_3)^2 + (\\sigma_3-\\sigma_1)^2}{2}}', '8', '5', '1', '0', '4.99', '3.55', 'pending', NULL, NULL, '\"[\\\"stress-analysis\\\",\\\"boundary-conditions\\\",\\\"theory\\\"]\"', 'calculation', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-05-31 18:16:00', '2025-05-31 18:16:00', NULL);
INSERT INTO `comments` VALUES ('70', '22', '25', NULL, 'Excellent tips! Về **Mesh Quality**, mình thường check:

1. **Aspect Ratio** < 3:1 cho structural analysis
2. **Skewness** < 0.7 (tốt nhất < 0.5)
3. **Jacobian** > 0.6

Với ANSYS Mechanical, **Patch Conforming Method** cho geometry phức tạp, **Patch Independent** cho simple parts.', '0', '0', '0', NULL, '15', '5', '13', '0', '3.24', '3.58', 'verified', '17', NULL, '\"[\\\"fea\\\",\\\"meshing\\\",\\\"ansys\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '0', NULL, NULL, '2025-06-22 19:02:45', '2025-06-22 19:02:45', NULL);
INSERT INTO `comments` VALUES ('71', '22', '9', NULL, 'Về **Boundary Conditions**, một số lưu ý:

- **Fixed Support**: Chỉ dùng khi thực sự cần thiết
- **Remote Displacement**: Tốt hơn cho bolt connections
- **Contact**: Bonded vs Frictional vs Frictionless

**Von Mises Stress** công thức:
σ_vm = √[(σ₁-σ₂)² + (σ₂-σ₃)² + (σ₃-σ₁)²]/√2', '0', '0', '1', '\\sigma_{vm} = \\sqrt{\\frac{(\\sigma_1-\\sigma_2)^2 + (\\sigma_2-\\sigma_3)^2 + (\\sigma_3-\\sigma_1)^2}{2}}', '8', '4', '1', '2', '4.29', '4.65', 'unverified', NULL, NULL, '\"[\\\"stress-analysis\\\",\\\"boundary-conditions\\\",\\\"theory\\\"]\"', 'calculation', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-04-07 14:23:36', '2025-04-07 14:23:36', NULL);
INSERT INTO `comments` VALUES ('72', '23', '13', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '12', '3', '1', '2', '3.01', '3.73', 'verified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-25 14:30:13', '2025-06-25 14:30:13', NULL);
INSERT INTO `comments` VALUES ('73', '23', '17', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '4', '2', '9', '0', '4.37', '4.69', 'unverified', NULL, '2025-06-16 17:57:51', '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-28 21:01:11', '2025-06-28 21:01:11', NULL);
INSERT INTO `comments` VALUES ('74', '23', '32', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '1', '4', '12', '2', '3.87', '4.06', 'verified', '29', NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-26 11:49:45', '2025-06-26 11:49:45', NULL);
INSERT INTO `comments` VALUES ('75', '23', '33', '74', 'Interesting approach! Có case study nào cụ thể không?', '0', '0', '0', NULL, '1', '2', '6', '0', '4.50', '4.04', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-24 08:12:38', '2025-06-24 08:12:38', NULL);
INSERT INTO `comments` VALUES ('76', '24', '16', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '15', '4', '2', '3', '3.07', '4.24', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-18 15:55:31', '2025-06-18 15:55:31', NULL);
INSERT INTO `comments` VALUES ('77', '24', '16', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '18', '3', '13', '0', '4.25', '4.76', 'verified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-05 01:18:44', '2025-06-05 01:18:44', NULL);
INSERT INTO `comments` VALUES ('78', '24', '13', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '18', '3', '1', '2', '4.40', '4.53', 'disputed', '2', NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-13 01:17:18', '2025-06-13 01:17:18', NULL);
INSERT INTO `comments` VALUES ('79', '25', '11', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '1', '1', '3', '1', '4.22', '4.25', 'verified', NULL, '2025-06-21 17:57:51', '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-04-28 09:16:45', '2025-04-28 09:16:45', NULL);
INSERT INTO `comments` VALUES ('80', '25', '24', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '13', '4', '9', '2', '3.60', '4.87', 'unverified', NULL, '2025-06-21 17:57:51', '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-02 05:27:26', '2025-06-02 05:27:26', NULL);
INSERT INTO `comments` VALUES ('81', '26', '7', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '9', '2', '14', '1', '3.70', '4.70', 'disputed', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-25 17:52:56', '2025-06-25 17:52:56', NULL);
INSERT INTO `comments` VALUES ('82', '26', '26', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '25', '4', '8', '1', '3.52', '3.85', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-28 15:54:47', '2025-06-28 15:54:47', NULL);
INSERT INTO `comments` VALUES ('83', '26', '14', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '0', '5', '11', '0', '4.73', '4.13', 'unverified', '10', NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-30 21:18:04', '2025-06-30 21:18:04', NULL);
INSERT INTO `comments` VALUES ('84', '27', '17', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '3', '5', '8', '3', '3.42', '4.56', 'pending', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-29 01:47:13', '2025-06-29 01:47:13', NULL);
INSERT INTO `comments` VALUES ('85', '27', '26', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '5', '3', '6', '1', '4.35', '4.07', 'pending', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-07-01 16:53:57', '2025-07-01 16:53:57', NULL);
INSERT INTO `comments` VALUES ('86', '27', '23', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '22', '4', '2', '0', '4.51', '4.09', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-26 16:47:03', '2025-06-26 16:47:03', NULL);
INSERT INTO `comments` VALUES ('87', '28', '20', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '10', '1', '10', '0', '3.99', '4.49', 'unverified', '29', NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-05-31 18:49:28', '2025-05-31 18:49:28', NULL);
INSERT INTO `comments` VALUES ('88', '28', '1', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '8', '0', '0', '3', '4.47', '4.28', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-21 15:35:25', '2025-06-21 15:35:25', NULL);
INSERT INTO `comments` VALUES ('89', '28', '3', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '7', '2', '4', '3', '3.79', '4.79', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-05-10 09:36:10', '2025-05-10 09:36:10', NULL);
INSERT INTO `comments` VALUES ('90', '28', '20', '88', 'Cảm ơn bạn đã chia sẻ! Mình sẽ thử approach này.', '0', '0', '0', NULL, '9', '0', '1', '1', '4.31', '3.81', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-10 18:00:17', '2025-06-10 18:00:17', NULL);
INSERT INTO `comments` VALUES ('91', '29', '3', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '20', '5', '9', '3', '3.93', '4.24', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-25 04:03:36', '2025-06-25 04:03:36', NULL);
INSERT INTO `comments` VALUES ('92', '29', '12', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '17', '1', '11', '0', '3.73', '4.53', 'pending', NULL, '2025-06-22 17:57:52', '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-29 18:15:19', '2025-06-29 18:15:19', NULL);
INSERT INTO `comments` VALUES ('93', '29', '9', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '13', '0', '8', '1', '4.68', '4.62', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-27 07:29:10', '2025-06-27 07:29:10', NULL);
INSERT INTO `comments` VALUES ('94', '29', '6', '92', 'Thông tin rất hữu ích. Có thể bạn elaborate thêm về phần implementation không?', '0', '1', '0', NULL, '2', '1', '8', '1', '3.82', '4.47', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-27 11:34:10', '2025-06-27 11:34:10', NULL);
INSERT INTO `comments` VALUES ('95', '30', '24', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '8', '3', '9', '0', '4.97', '4.70', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-14 00:50:23', '2025-06-14 00:50:23', NULL);
INSERT INTO `comments` VALUES ('96', '30', '13', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '14', '2', '13', '1', '3.05', '3.62', 'unverified', NULL, '2025-06-21 17:57:52', '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-04-11 22:34:30', '2025-04-11 22:34:30', NULL);
INSERT INTO `comments` VALUES ('97', '31', '11', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '7', '4', '8', '1', '4.92', '3.75', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-30 14:07:19', '2025-06-30 14:07:19', NULL);
INSERT INTO `comments` VALUES ('98', '32', '14', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '23', '4', '14', '3', '4.76', '4.79', 'pending', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-30 13:02:37', '2025-06-30 13:02:37', NULL);
INSERT INTO `comments` VALUES ('99', '32', '27', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '10', '1', '13', '2', '3.01', '4.16', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-08 11:40:12', '2025-06-08 11:40:12', NULL);
INSERT INTO `comments` VALUES ('100', '32', '18', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '20', '5', '10', '3', '4.54', '4.94', 'pending', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-06 12:16:31', '2025-06-06 12:16:31', NULL);
INSERT INTO `comments` VALUES ('101', '33', '5', NULL, 'Kinh nghiệm hay! Về **Dynamic Mill**, mình thường set:

```
Stock to leave: 0.2mm cho roughing
Min toolpath radius: 65% tool diameter
Optimal load: 15-20% cho aluminum
Max stepdown: 3x tool diameter
```

Với aluminum 6061, speeds/feeds này work rất tốt:
- 12mm end mill: 8000 RPM, 2000 mm/min
- Coolant: Flood hoặc mist đều OK', '0', '1', '0', NULL, '6', '5', '13', '3', '3.19', '4.35', 'unverified', NULL, NULL, '\"[\\\"cnc\\\",\\\"mastercam\\\",\\\"aluminum\\\"]\"', 'calculation', '0', '0', '1', '0', NULL, '1', NULL, NULL, '2025-06-18 06:39:05', '2025-06-18 06:39:05', NULL);
INSERT INTO `comments` VALUES ('102', '34', '4', NULL, 'Thông tin về **heat treatment** rất chi tiết! Bổ sung về **quenching media:**

**Water quenching:**
- Cooling rate: ~600°C/s
- Risk: High distortion, cracking
- Use: Simple geometry, low carbon steel

**Oil quenching:**
- Cooling rate: ~150°C/s  
- Better: Less distortion
- Use: Complex parts, medium carbon

**Polymer quenching:**
- Cooling rate: Variable (100-400°C/s)
- Advantage: Controllable cooling curve
- Cost: Higher than oil/water', '0', '0', '0', NULL, '0', '5', '5', '2', '4.50', '3.66', 'unverified', NULL, NULL, '\"[\\\"heat-treatment\\\",\\\"quenching\\\",\\\"steel\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-15 01:30:53', '2025-06-15 01:30:53', NULL);
INSERT INTO `comments` VALUES ('103', '34', '35', NULL, 'Về **7075-T6 aluminum**, thêm thông tin machining:

**Cutting parameters:**
```
Speed: 300-500 m/min
Feed: 0.15-0.25 mm/tooth
Depth: 2-8 mm
Coolant: Flood recommended
```

**Tool selection:**
- Uncoated carbide preferred
- Sharp cutting edges essential
- Positive rake angle: 15-20°
- Helix angle: 45° minimum

**Surface finish:** Ra 0.8-1.6 μm achievable with proper setup', '0', '1', '0', NULL, '17', '2', '11', '3', '4.02', '3.88', 'unverified', NULL, NULL, '\"[\\\"aluminum\\\",\\\"machining\\\",\\\"aerospace\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '1', NULL, NULL, '2025-04-27 04:03:47', '2025-04-27 04:03:47', NULL);
INSERT INTO `comments` VALUES ('104', '34', '22', '102', 'Thông tin rất hữu ích. Có thể bạn elaborate thêm về phần implementation không?', '0', '0', '0', NULL, '2', '0', '7', '0', '3.24', '3.42', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-20 20:57:04', '2025-06-20 20:57:04', NULL);
INSERT INTO `comments` VALUES ('105', '34', '30', '103', 'Có alternative solution nào khác không? Current approach hơi complex.', '0', '1', '0', NULL, '8', '2', '4', '0', '3.23', '3.38', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-08 03:20:41', '2025-06-08 03:20:41', NULL);
INSERT INTO `comments` VALUES ('106', '35', '9', NULL, 'Kinh nghiệm hay! Về **Dynamic Mill**, mình thường set:

```
Stock to leave: 0.2mm cho roughing
Min toolpath radius: 65% tool diameter
Optimal load: 15-20% cho aluminum
Max stepdown: 3x tool diameter
```

Với aluminum 6061, speeds/feeds này work rất tốt:
- 12mm end mill: 8000 RPM, 2000 mm/min
- Coolant: Flood hoặc mist đều OK', '0', '1', '0', NULL, '10', '2', '14', '1', '4.42', '3.52', 'unverified', NULL, NULL, '\"[\\\"cnc\\\",\\\"mastercam\\\",\\\"aluminum\\\"]\"', 'calculation', '0', '0', '1', '0', NULL, '2', NULL, NULL, '2025-05-19 12:09:57', '2025-05-19 12:09:57', NULL);
INSERT INTO `comments` VALUES ('107', '35', '24', NULL, 'Về **Tool Selection**, có thể tham khảo bảng này:

**Aluminum 6061:**
- Roughing: Uncoated carbide, 3 flutes
- Finishing: Polished carbide, 2 flutes
- Coating: Tránh TiN (dễ stick)

**Steel 1045:**
- Roughing: TiAlN coated, 4 flutes
- Finishing: TiN coated, 2-3 flutes
- Coolant: Bắt buộc phải có

Chipload formula: **Feed = RPM × Flutes × Chipload**', '0', '0', '1', 'Feed = RPM \\times Flutes \\times Chipload', '17', '4', '13', '1', '3.85', '3.65', 'unverified', NULL, NULL, '\"[\\\"tooling\\\",\\\"feeds-speeds\\\",\\\"materials\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-28 18:47:20', '2025-06-28 18:47:20', NULL);
INSERT INTO `comments` VALUES ('108', '35', '26', '107', 'Thông tin rất hữu ích. Có thể bạn elaborate thêm về phần implementation không?', '0', '0', '0', NULL, '2', '0', '3', '1', '2.58', '4.06', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-11 14:04:33', '2025-06-11 14:04:33', NULL);
INSERT INTO `comments` VALUES ('109', '36', '24', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '10', '1', '2', '3', '4.77', '4.49', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-26 03:59:03', '2025-06-26 03:59:03', NULL);
INSERT INTO `comments` VALUES ('110', '36', '33', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '1', '4', '5', '3', '4.15', '4.23', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-12 20:18:27', '2025-06-12 20:18:27', NULL);
INSERT INTO `comments` VALUES ('111', '36', '10', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '18', '5', '6', '3', '3.03', '4.84', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-25 17:33:13', '2025-06-25 17:33:13', NULL);
INSERT INTO `comments` VALUES ('112', '37', '18', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '1', '0', '3', '1', '4.32', '4.65', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-01 18:03:55', '2025-07-01 18:03:55', NULL);
INSERT INTO `comments` VALUES ('113', '37', '5', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '5', '4', '7', '0', '3.11', '4.03', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-15 01:32:11', '2025-06-15 01:32:11', NULL);
INSERT INTO `comments` VALUES ('114', '37', '2', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '16', '4', '3', '1', '4.51', '3.57', 'pending', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-05-26 03:30:02', '2025-05-26 03:30:02', NULL);
INSERT INTO `comments` VALUES ('115', '37', '5', '113', 'Interesting approach! Có case study nào cụ thể không?', '0', '1', '0', NULL, '7', '1', '0', '1', '3.92', '4.10', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-25 05:51:59', '2025-06-25 05:51:59', NULL);
INSERT INTO `comments` VALUES ('116', '38', '31', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '11', '3', '7', '3', '4.30', '3.80', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-27 16:09:51', '2025-06-27 16:09:51', NULL);
INSERT INTO `comments` VALUES ('117', '38', '8', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '24', '2', '1', '2', '3.73', '3.89', 'unverified', NULL, '2025-06-24 17:57:52', '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-26 15:27:38', '2025-06-26 15:27:38', NULL);
INSERT INTO `comments` VALUES ('118', '38', '23', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '20', '1', '8', '1', '4.58', '5.00', 'unverified', NULL, '2025-06-19 17:57:52', '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-05-30 03:14:09', '2025-05-30 03:14:09', NULL);
INSERT INTO `comments` VALUES ('119', '38', '19', '118', 'Cảm ơn bạn đã chia sẻ! Mình sẽ thử approach này.', '0', '1', '0', NULL, '3', '2', '0', '0', '2.87', '3.38', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-03 14:41:52', '2025-06-03 14:41:52', NULL);
INSERT INTO `comments` VALUES ('120', '39', '31', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '12', '2', '10', '0', '3.77', '4.56', 'unverified', NULL, '2025-06-16 17:57:52', '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-20 14:07:30', '2025-06-20 14:07:30', NULL);
INSERT INTO `comments` VALUES ('121', '39', '33', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '24', '0', '14', '1', '4.87', '4.16', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-01 06:54:33', '2025-07-01 06:54:33', NULL);
INSERT INTO `comments` VALUES ('122', '39', '35', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '19', '4', '15', '2', '4.19', '3.60', 'unverified', '1', '2025-06-15 17:57:52', '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-10 18:06:02', '2025-06-10 18:06:02', NULL);
INSERT INTO `comments` VALUES ('123', '39', '31', '120', 'Perfect timing! Mình đang research về topic này.', '0', '1', '0', NULL, '7', '1', '6', '0', '2.82', '3.76', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-26 21:06:08', '2025-06-26 21:06:08', NULL);
INSERT INTO `comments` VALUES ('124', '40', '22', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '6', '4', '1', '0', '4.39', '4.79', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-12 17:46:51', '2025-06-12 17:46:51', NULL);
INSERT INTO `comments` VALUES ('125', '40', '9', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '1', '3', '11', '2', '4.55', '3.87', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-11 17:31:37', '2025-05-11 17:31:37', NULL);
INSERT INTO `comments` VALUES ('126', '40', '1', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '6', '4', '5', '0', '4.43', '4.37', 'unverified', '34', NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-05-15 23:18:30', '2025-05-15 23:18:30', NULL);
INSERT INTO `comments` VALUES ('127', '41', '33', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '19', '2', '12', '0', '4.87', '3.57', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-29 16:22:05', '2025-06-29 16:22:05', NULL);
INSERT INTO `comments` VALUES ('128', '41', '17', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '0', '0', '10', '2', '3.54', '4.03', 'pending', NULL, '2025-06-23 17:57:52', '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-07-02 03:21:42', '2025-07-02 03:21:42', NULL);
INSERT INTO `comments` VALUES ('129', '41', '6', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '10', '2', '14', '0', '4.89', '4.19', 'verified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-07-01 09:19:02', '2025-07-01 09:19:02', NULL);
INSERT INTO `comments` VALUES ('130', '41', '1', '127', 'Agree với points này. Đặc biệt là phần về cost-benefit analysis.', '0', '0', '0', NULL, '10', '0', '4', '1', '2.60', '4.25', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-25 03:19:36', '2025-06-25 03:19:36', NULL);
INSERT INTO `comments` VALUES ('131', '42', '2', NULL, 'Cảm ơn bạn đã chia sẻ! Mình cũng gặp vấn đề tương tự với large assemblies. Tip về SpeedPak rất hữu ích, đã thử và thấy performance cải thiện đáng kể.

Thêm một tip nữa: sử dụng **Lightweight mode** cho các components không cần edit thường xuyên. Có thể tiết kiệm đến 60% memory usage.', '0', '0', '0', NULL, '3', '1', '15', '1', '4.63', '4.94', 'verified', NULL, NULL, '\"[\\\"solidworks\\\",\\\"performance\\\",\\\"assembly\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-24 00:26:52', '2025-06-24 00:26:52', NULL);
INSERT INTO `comments` VALUES ('132', '42', '26', '131', 'Bổ sung thêm: cần chú ý về safety requirements khi implement.', '0', '1', '0', NULL, '7', '2', '5', '0', '3.17', '4.41', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-29 23:35:55', '2025-05-29 23:35:55', NULL);
INSERT INTO `comments` VALUES ('133', '43', '22', NULL, 'Cảm ơn bạn đã chia sẻ! Mình cũng gặp vấn đề tương tự với large assemblies. Tip về SpeedPak rất hữu ích, đã thử và thấy performance cải thiện đáng kể.

Thêm một tip nữa: sử dụng **Lightweight mode** cho các components không cần edit thường xuyên. Có thể tiết kiệm đến 60% memory usage.', '0', '0', '0', NULL, '22', '3', '8', '3', '3.31', '4.38', 'unverified', NULL, '2025-06-21 17:57:52', '\"[\\\"solidworks\\\",\\\"performance\\\",\\\"assembly\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-30 11:27:41', '2025-06-30 11:27:41', NULL);
INSERT INTO `comments` VALUES ('134', '43', '26', NULL, 'Về vấn đề **Graphics Settings**, mình khuyên nên:

1. **Image Quality**: Giảm xuống Medium cho assemblies > 500 parts
2. **RealView**: Chỉ bật khi cần render presentation
3. **Anti-aliasing**: Tắt hoàn toàn khi modeling

Với setup này, máy i7-8700K + GTX 1060 của mình handle được assembly 2000+ parts khá mượt.', '0', '0', '0', NULL, '17', '1', '7', '2', '3.37', '4.23', 'verified', NULL, NULL, '\"[\\\"solidworks\\\",\\\"graphics\\\",\\\"optimization\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '0', NULL, NULL, '2025-06-26 09:24:14', '2025-06-26 09:24:14', NULL);
INSERT INTO `comments` VALUES ('135', '43', '15', '134', 'Có alternative solution nào khác không? Current approach hơi complex.', '0', '1', '0', NULL, '8', '2', '8', '1', '4.12', '3.57', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-14 19:15:11', '2025-06-14 19:15:11', NULL);
INSERT INTO `comments` VALUES ('136', '44', '19', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '14', '1', '5', '2', '3.98', '4.54', 'unverified', '1', NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-27 17:24:53', '2025-06-27 17:24:53', NULL);
INSERT INTO `comments` VALUES ('137', '44', '19', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '9', '5', '3', '0', '3.65', '4.99', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-22 08:11:36', '2025-06-22 08:11:36', NULL);
INSERT INTO `comments` VALUES ('138', '44', '6', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '1', '0', '12', '2', '3.92', '4.43', 'unverified', '19', NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-07-02 01:59:24', '2025-07-02 01:59:24', NULL);
INSERT INTO `comments` VALUES ('139', '44', '31', '137', 'Thanks for the detailed explanation. Saved cho reference sau này.', '0', '0', '0', NULL, '0', '0', '2', '0', '3.89', '3.54', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-21 21:29:23', '2025-06-21 21:29:23', NULL);
INSERT INTO `comments` VALUES ('140', '44', '8', '138', 'Cảm ơn bạn đã chia sẻ! Mình sẽ thử approach này.', '0', '1', '0', NULL, '10', '2', '7', '0', '3.58', '4.36', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-21 18:10:04', '2025-06-21 18:10:04', NULL);
INSERT INTO `comments` VALUES ('141', '45', '20', NULL, 'Cảm ơn bạn đã chia sẻ! Mình cũng gặp vấn đề tương tự với large assemblies. Tip về SpeedPak rất hữu ích, đã thử và thấy performance cải thiện đáng kể.

Thêm một tip nữa: sử dụng **Lightweight mode** cho các components không cần edit thường xuyên. Có thể tiết kiệm đến 60% memory usage.', '0', '0', '0', NULL, '22', '2', '11', '1', '3.91', '4.08', 'pending', NULL, NULL, '\"[\\\"solidworks\\\",\\\"performance\\\",\\\"assembly\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-22 22:34:08', '2025-06-22 22:34:08', NULL);
INSERT INTO `comments` VALUES ('142', '45', '19', NULL, 'Về vấn đề **Graphics Settings**, mình khuyên nên:

1. **Image Quality**: Giảm xuống Medium cho assemblies > 500 parts
2. **RealView**: Chỉ bật khi cần render presentation
3. **Anti-aliasing**: Tắt hoàn toàn khi modeling

Với setup này, máy i7-8700K + GTX 1060 của mình handle được assembly 2000+ parts khá mượt.', '0', '0', '0', NULL, '14', '4', '14', '3', '4.91', '3.73', 'unverified', NULL, NULL, '\"[\\\"solidworks\\\",\\\"graphics\\\",\\\"optimization\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '2', NULL, NULL, '2025-06-19 13:39:52', '2025-06-19 13:39:52', NULL);
INSERT INTO `comments` VALUES ('143', '45', '7', NULL, 'Bổ sung thêm về **Hardware Optimization**:

- **RAM**: 32GB là sweet spot, 64GB chỉ cần thiết cho assemblies > 5000 parts
- **Storage**: NVMe SSD cho OS + SolidWorks, SATA SSD cho project files
- **Graphics**: Quadro RTX 4000 trở lên cho professional work

Đặc biệt chú ý **Page File** settings - nên set manual 1.5x RAM size.', '0', '0', '0', NULL, '6', '0', '8', '1', '3.49', '4.44', 'unverified', NULL, NULL, '\"[\\\"hardware\\\",\\\"performance\\\",\\\"system\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-20 23:50:24', '2025-06-20 23:50:24', NULL);
INSERT INTO `comments` VALUES ('144', '45', '6', '142', 'Perfect timing! Mình đang research về topic này.', '0', '1', '0', NULL, '3', '2', '7', '0', '2.94', '3.30', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-30 03:18:28', '2025-06-30 03:18:28', NULL);
INSERT INTO `comments` VALUES ('145', '46', '23', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '23', '4', '1', '3', '4.40', '3.50', 'verified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-05-20 04:11:38', '2025-05-20 04:11:38', NULL);
INSERT INTO `comments` VALUES ('146', '46', '30', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '17', '3', '7', '0', '4.41', '4.20', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-10 05:17:00', '2025-06-10 05:17:00', NULL);
INSERT INTO `comments` VALUES ('147', '46', '20', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '10', '2', '14', '3', '3.99', '4.46', 'unverified', '29', '2025-06-21 17:57:52', '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-05-18 14:26:29', '2025-05-18 14:26:29', NULL);
INSERT INTO `comments` VALUES ('148', '47', '1', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '22', '4', '7', '1', '3.16', '4.70', 'pending', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-26 18:37:28', '2025-06-26 18:37:28', NULL);
INSERT INTO `comments` VALUES ('149', '47', '1', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '21', '3', '9', '3', '4.09', '4.83', 'unverified', '10', NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-19 14:43:13', '2025-06-19 14:43:13', NULL);
INSERT INTO `comments` VALUES ('150', '47', '8', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '1', '2', '1', '1', '3.62', '4.10', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-20 15:48:36', '2025-06-20 15:48:36', NULL);
INSERT INTO `comments` VALUES ('151', '48', '28', NULL, 'Kinh nghiệm hay! Về **Dynamic Mill**, mình thường set:

```
Stock to leave: 0.2mm cho roughing
Min toolpath radius: 65% tool diameter
Optimal load: 15-20% cho aluminum
Max stepdown: 3x tool diameter
```

Với aluminum 6061, speeds/feeds này work rất tốt:
- 12mm end mill: 8000 RPM, 2000 mm/min
- Coolant: Flood hoặc mist đều OK', '0', '1', '0', NULL, '8', '0', '0', '1', '4.30', '3.55', 'pending', NULL, NULL, '\"[\\\"cnc\\\",\\\"mastercam\\\",\\\"aluminum\\\"]\"', 'calculation', '0', '0', '1', '0', NULL, '0', NULL, NULL, '2025-04-25 03:38:51', '2025-04-25 03:38:51', NULL);
INSERT INTO `comments` VALUES ('152', '48', '17', NULL, 'Về **Tool Selection**, có thể tham khảo bảng này:

**Aluminum 6061:**
- Roughing: Uncoated carbide, 3 flutes
- Finishing: Polished carbide, 2 flutes
- Coating: Tránh TiN (dễ stick)

**Steel 1045:**
- Roughing: TiAlN coated, 4 flutes
- Finishing: TiN coated, 2-3 flutes
- Coolant: Bắt buộc phải có

Chipload formula: **Feed = RPM × Flutes × Chipload**', '0', '0', '1', 'Feed = RPM \\times Flutes \\times Chipload', '1', '2', '15', '2', '4.45', '4.45', 'pending', NULL, NULL, '\"[\\\"tooling\\\",\\\"feeds-speeds\\\",\\\"materials\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-04-26 03:57:14', '2025-04-26 03:57:14', NULL);
INSERT INTO `comments` VALUES ('153', '48', '14', '151', 'Thông tin rất hữu ích. Có thể bạn elaborate thêm về phần implementation không?', '0', '0', '0', NULL, '10', '2', '8', '1', '3.26', '3.59', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-06 10:03:00', '2025-06-06 10:03:00', NULL);
INSERT INTO `comments` VALUES ('154', '49', '4', NULL, 'Thông tin về **heat treatment** rất chi tiết! Bổ sung về **quenching media:**

**Water quenching:**
- Cooling rate: ~600°C/s
- Risk: High distortion, cracking
- Use: Simple geometry, low carbon steel

**Oil quenching:**
- Cooling rate: ~150°C/s  
- Better: Less distortion
- Use: Complex parts, medium carbon

**Polymer quenching:**
- Cooling rate: Variable (100-400°C/s)
- Advantage: Controllable cooling curve
- Cost: Higher than oil/water', '0', '0', '0', NULL, '12', '4', '14', '2', '4.42', '4.13', 'unverified', NULL, NULL, '\"[\\\"heat-treatment\\\",\\\"quenching\\\",\\\"steel\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-29 15:12:59', '2025-06-29 15:12:59', NULL);
INSERT INTO `comments` VALUES ('155', '49', '13', NULL, 'Về **7075-T6 aluminum**, thêm thông tin machining:

**Cutting parameters:**
```
Speed: 300-500 m/min
Feed: 0.15-0.25 mm/tooth
Depth: 2-8 mm
Coolant: Flood recommended
```

**Tool selection:**
- Uncoated carbide preferred
- Sharp cutting edges essential
- Positive rake angle: 15-20°
- Helix angle: 45° minimum

**Surface finish:** Ra 0.8-1.6 μm achievable with proper setup', '0', '1', '0', NULL, '21', '4', '3', '3', '4.14', '3.68', 'unverified', NULL, NULL, '\"[\\\"aluminum\\\",\\\"machining\\\",\\\"aerospace\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '0', NULL, NULL, '2025-06-28 21:58:15', '2025-06-28 21:58:15', NULL);
INSERT INTO `comments` VALUES ('156', '49', '14', '154', 'Agree với points này. Đặc biệt là phần về cost-benefit analysis.', '0', '0', '0', NULL, '9', '0', '8', '0', '4.37', '3.74', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-28 12:21:25', '2025-06-28 12:21:25', NULL);
INSERT INTO `comments` VALUES ('157', '50', '6', NULL, 'Kinh nghiệm hay! Về **Dynamic Mill**, mình thường set:

```
Stock to leave: 0.2mm cho roughing
Min toolpath radius: 65% tool diameter
Optimal load: 15-20% cho aluminum
Max stepdown: 3x tool diameter
```

Với aluminum 6061, speeds/feeds này work rất tốt:
- 12mm end mill: 8000 RPM, 2000 mm/min
- Coolant: Flood hoặc mist đều OK', '0', '1', '0', NULL, '18', '5', '14', '2', '3.12', '4.00', 'unverified', '23', NULL, '\"[\\\"cnc\\\",\\\"mastercam\\\",\\\"aluminum\\\"]\"', 'calculation', '0', '0', '1', '0', NULL, '0', NULL, NULL, '2025-06-02 05:27:51', '2025-06-02 05:27:51', NULL);
INSERT INTO `comments` VALUES ('158', '51', '14', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '1', '3', '12', '1', '3.21', '4.86', 'pending', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-24 14:18:39', '2025-06-24 14:18:39', NULL);
INSERT INTO `comments` VALUES ('159', '51', '7', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '15', '2', '9', '2', '3.40', '3.77', 'pending', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-02 09:18:42', '2025-06-02 09:18:42', NULL);
INSERT INTO `comments` VALUES ('160', '51', '4', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '6', '3', '15', '0', '4.38', '4.08', 'unverified', '9', NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-18 14:27:20', '2025-06-18 14:27:20', NULL);
INSERT INTO `comments` VALUES ('161', '52', '21', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '8', '2', '9', '3', '3.83', '3.62', 'disputed', NULL, '2025-06-16 17:57:52', '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-05-25 05:19:17', '2025-05-25 05:19:17', NULL);
INSERT INTO `comments` VALUES ('162', '52', '30', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '3', '5', '8', '3', '3.44', '3.75', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-03 03:32:07', '2025-06-03 03:32:07', NULL);
INSERT INTO `comments` VALUES ('163', '52', '26', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '18', '4', '10', '1', '4.03', '3.64', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-03 11:22:27', '2025-06-03 11:22:27', NULL);
INSERT INTO `comments` VALUES ('164', '52', '7', '161', 'Bổ sung thêm: cần chú ý về safety requirements khi implement.', '0', '1', '0', NULL, '9', '2', '0', '0', '4.28', '4.19', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-18 09:47:49', '2025-06-18 09:47:49', NULL);
INSERT INTO `comments` VALUES ('165', '53', '14', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '12', '4', '5', '0', '3.84', '4.00', 'verified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-21 04:04:56', '2025-06-21 04:04:56', NULL);
INSERT INTO `comments` VALUES ('166', '53', '2', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '2', '3', '15', '2', '3.44', '4.22', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-05-18 18:17:06', '2025-05-18 18:17:06', NULL);
INSERT INTO `comments` VALUES ('167', '53', '9', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '11', '2', '5', '3', '4.56', '3.84', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-25 01:32:39', '2025-06-25 01:32:39', NULL);
INSERT INTO `comments` VALUES ('168', '54', '15', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '21', '4', '13', '0', '4.07', '4.49', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-07-01 23:58:06', '2025-07-01 23:58:06', NULL);
INSERT INTO `comments` VALUES ('169', '54', '21', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '19', '2', '11', '0', '4.26', '3.50', 'disputed', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-07-01 23:31:11', '2025-07-01 23:31:11', NULL);
INSERT INTO `comments` VALUES ('170', '54', '28', '168', 'Có alternative solution nào khác không? Current approach hơi complex.', '0', '1', '0', NULL, '2', '1', '6', '1', '3.52', '3.14', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-01 23:58:33', '2025-07-01 23:58:33', NULL);
INSERT INTO `comments` VALUES ('171', '55', '27', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '15', '4', '1', '1', '3.32', '4.35', 'unverified', '21', NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-30 02:25:43', '2025-06-30 02:25:43', NULL);
INSERT INTO `comments` VALUES ('172', '55', '1', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '17', '4', '3', '2', '3.18', '4.95', 'disputed', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-27 07:10:56', '2025-06-27 07:10:56', NULL);
INSERT INTO `comments` VALUES ('173', '55', '34', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '6', '0', '1', '1', '4.19', '3.94', 'unverified', '14', NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-22 19:30:16', '2025-06-22 19:30:16', NULL);
INSERT INTO `comments` VALUES ('174', '55', '6', '173', 'Perfect timing! Mình đang research về topic này.', '0', '0', '0', NULL, '7', '0', '6', '0', '3.89', '3.50', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-29 13:18:58', '2025-06-29 13:18:58', NULL);
INSERT INTO `comments` VALUES ('175', '56', '30', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '8', '3', '4', '3', '4.97', '3.81', 'unverified', '15', NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-18 00:27:43', '2025-06-18 00:27:43', NULL);
INSERT INTO `comments` VALUES ('176', '56', '26', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '3', '5', '5', '0', '3.15', '3.83', 'pending', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-25 05:58:03', '2025-05-25 05:58:03', NULL);
INSERT INTO `comments` VALUES ('177', '56', '21', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '18', '5', '4', '0', '3.54', '4.63', 'unverified', '33', NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-23 21:10:49', '2025-06-23 21:10:49', NULL);
INSERT INTO `comments` VALUES ('178', '57', '20', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '16', '0', '8', '0', '3.46', '4.72', 'verified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-27 22:23:45', '2025-06-27 22:23:45', NULL);
INSERT INTO `comments` VALUES ('179', '57', '29', '178', 'Có alternative solution nào khác không? Current approach hơi complex.', '0', '1', '0', NULL, '5', '2', '5', '1', '3.98', '3.43', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-27 06:03:26', '2025-06-27 06:03:26', NULL);
INSERT INTO `comments` VALUES ('180', '58', '12', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '18', '4', '9', '1', '4.42', '4.13', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-18 01:11:37', '2025-06-18 01:11:37', NULL);
INSERT INTO `comments` VALUES ('181', '58', '13', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '16', '0', '13', '2', '4.26', '3.88', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-23 19:45:13', '2025-06-23 19:45:13', NULL);
INSERT INTO `comments` VALUES ('182', '59', '18', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '1', '0', '2', '0', '3.66', '3.93', 'unverified', NULL, '2025-06-23 17:57:52', '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-30 03:58:20', '2025-06-30 03:58:20', NULL);
INSERT INTO `comments` VALUES ('183', '59', '22', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '20', '2', '11', '1', '3.23', '3.53', 'pending', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-10 08:04:28', '2025-06-10 08:04:28', NULL);
INSERT INTO `comments` VALUES ('184', '60', '18', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '3', '4', '2', '3', '4.14', '3.94', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-02 04:15:11', '2025-07-02 04:15:11', NULL);
INSERT INTO `comments` VALUES ('185', '60', '25', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '19', '2', '7', '1', '4.52', '3.82', 'unverified', NULL, '2025-06-17 17:57:52', '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-01 19:51:51', '2025-07-01 19:51:51', NULL);
INSERT INTO `comments` VALUES ('186', '60', '10', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '11', '1', '9', '2', '3.42', '4.91', 'verified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-02 05:38:38', '2025-07-02 05:38:38', NULL);
INSERT INTO `comments` VALUES ('187', '60', '7', '185', 'Interesting approach! Có case study nào cụ thể không?', '0', '0', '0', NULL, '10', '2', '6', '0', '3.59', '4.15', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-01 21:31:42', '2025-07-01 21:31:42', NULL);
INSERT INTO `comments` VALUES ('188', '61', '2', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '13', '0', '8', '2', '4.88', '4.48', 'verified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-04-17 13:54:09', '2025-04-17 13:54:09', NULL);
INSERT INTO `comments` VALUES ('189', '61', '2', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '1', '0', '8', '3', '3.72', '4.16', 'pending', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-05-01 17:39:20', '2025-05-01 17:39:20', NULL);
INSERT INTO `comments` VALUES ('190', '61', '11', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '19', '2', '1', '2', '4.28', '4.69', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-04-22 16:46:56', '2025-04-22 16:46:56', NULL);
INSERT INTO `comments` VALUES ('191', '61', '28', '189', 'Thanks for the detailed explanation. Saved cho reference sau này.', '0', '1', '0', NULL, '7', '1', '8', '0', '4.31', '4.29', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-04 10:16:47', '2025-06-04 10:16:47', NULL);
INSERT INTO `comments` VALUES ('192', '61', '31', '190', 'Có alternative solution nào khác không? Current approach hơi complex.', '0', '1', '0', NULL, '4', '1', '1', '0', '3.81', '3.24', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-21 05:35:49', '2025-06-21 05:35:49', NULL);
INSERT INTO `comments` VALUES ('193', '62', '4', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '11', '0', '10', '1', '3.26', '4.21', 'unverified', '15', NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-07-02 05:51:48', '2025-07-02 05:51:48', NULL);
INSERT INTO `comments` VALUES ('194', '62', '34', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '20', '2', '14', '1', '4.70', '4.29', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-07-02 00:44:12', '2025-07-02 00:44:12', NULL);
INSERT INTO `comments` VALUES ('195', '62', '19', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '21', '2', '2', '1', '3.59', '3.64', 'unverified', NULL, '2025-06-22 17:57:52', '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-07-01 23:06:59', '2025-07-01 23:06:59', NULL);
INSERT INTO `comments` VALUES ('196', '62', '31', '194', 'Mình đã thử method này và thấy kết quả khá tốt. Recommend!', '0', '1', '0', NULL, '7', '2', '4', '1', '3.54', '3.03', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-02 05:52:06', '2025-07-02 05:52:06', NULL);
INSERT INTO `comments` VALUES ('197', '63', '22', NULL, 'Thông tin về **heat treatment** rất chi tiết! Bổ sung về **quenching media:**

**Water quenching:**
- Cooling rate: ~600°C/s
- Risk: High distortion, cracking
- Use: Simple geometry, low carbon steel

**Oil quenching:**
- Cooling rate: ~150°C/s  
- Better: Less distortion
- Use: Complex parts, medium carbon

**Polymer quenching:**
- Cooling rate: Variable (100-400°C/s)
- Advantage: Controllable cooling curve
- Cost: Higher than oil/water', '0', '0', '0', NULL, '24', '4', '5', '2', '4.05', '4.97', 'disputed', NULL, NULL, '\"[\\\"heat-treatment\\\",\\\"quenching\\\",\\\"steel\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-28 22:07:43', '2025-06-28 22:07:43', NULL);
INSERT INTO `comments` VALUES ('198', '64', '16', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '17', '0', '14', '3', '3.50', '3.62', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-20 19:53:46', '2025-06-20 19:53:46', NULL);
INSERT INTO `comments` VALUES ('199', '64', '31', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '9', '1', '3', '1', '3.68', '4.53', 'unverified', '11', NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-22 17:57:03', '2025-06-22 17:57:03', NULL);
INSERT INTO `comments` VALUES ('200', '64', '16', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '10', '2', '3', '1', '3.33', '4.29', 'verified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-27 00:30:13', '2025-06-27 00:30:13', NULL);
INSERT INTO `comments` VALUES ('201', '65', '17', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '2', '5', '1', '1', '3.52', '4.44', 'pending', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-04-20 07:14:15', '2025-04-20 07:14:15', NULL);
INSERT INTO `comments` VALUES ('202', '65', '16', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '10', '5', '7', '2', '3.48', '4.38', 'verified', '14', NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-05-06 12:31:19', '2025-05-06 12:31:19', NULL);
INSERT INTO `comments` VALUES ('203', '65', '4', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '25', '5', '10', '3', '4.05', '4.05', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-22 03:52:09', '2025-06-22 03:52:09', NULL);
INSERT INTO `comments` VALUES ('204', '65', '28', '201', 'Perfect timing! Mình đang research về topic này.', '0', '1', '0', NULL, '6', '1', '4', '0', '3.94', '3.72', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-04-30 16:23:48', '2025-04-30 16:23:48', NULL);
INSERT INTO `comments` VALUES ('205', '66', '10', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '1', '2', '10', '3', '3.75', '4.81', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-28 16:16:33', '2025-06-28 16:16:33', NULL);
INSERT INTO `comments` VALUES ('206', '67', '18', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '9', '3', '15', '0', '4.63', '3.81', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-04-26 03:31:15', '2025-04-26 03:31:15', NULL);
INSERT INTO `comments` VALUES ('207', '67', '22', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '14', '4', '14', '0', '3.98', '3.91', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-27 13:59:11', '2025-06-27 13:59:11', NULL);
INSERT INTO `comments` VALUES ('208', '67', '5', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '3', '0', '7', '1', '3.03', '3.74', 'pending', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-05-31 19:43:08', '2025-05-31 19:43:08', NULL);
INSERT INTO `comments` VALUES ('209', '67', '22', '206', 'Thanks for the detailed explanation. Saved cho reference sau này.', '0', '0', '0', NULL, '5', '2', '2', '0', '4.02', '3.95', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-15 15:08:54', '2025-06-15 15:08:54', NULL);
INSERT INTO `comments` VALUES ('210', '67', '23', '207', 'Cảm ơn bạn đã chia sẻ! Mình sẽ thử approach này.', '0', '0', '0', NULL, '10', '1', '8', '1', '4.41', '3.96', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-27 02:51:44', '2025-05-27 02:51:44', NULL);
INSERT INTO `comments` VALUES ('211', '68', '22', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '5', '4', '0', '0', '4.41', '4.67', 'pending', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-26 20:13:16', '2025-06-26 20:13:16', NULL);
INSERT INTO `comments` VALUES ('212', '68', '10', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '8', '1', '11', '2', '3.86', '3.81', 'pending', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-30 18:46:09', '2025-06-30 18:46:09', NULL);
INSERT INTO `comments` VALUES ('213', '68', '14', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '15', '3', '6', '0', '3.13', '3.86', 'verified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-30 20:54:09', '2025-06-30 20:54:09', NULL);
INSERT INTO `comments` VALUES ('214', '68', '17', '213', 'Agree với points này. Đặc biệt là phần về cost-benefit analysis.', '0', '1', '0', NULL, '0', '1', '6', '0', '4.27', '3.41', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-01 21:27:47', '2025-07-01 21:27:47', NULL);
INSERT INTO `comments` VALUES ('215', '69', '16', NULL, 'Thông tin về **heat treatment** rất chi tiết! Bổ sung về **quenching media:**

**Water quenching:**
- Cooling rate: ~600°C/s
- Risk: High distortion, cracking
- Use: Simple geometry, low carbon steel

**Oil quenching:**
- Cooling rate: ~150°C/s  
- Better: Less distortion
- Use: Complex parts, medium carbon

**Polymer quenching:**
- Cooling rate: Variable (100-400°C/s)
- Advantage: Controllable cooling curve
- Cost: Higher than oil/water', '0', '0', '0', NULL, '23', '5', '11', '1', '3.12', '3.95', 'unverified', NULL, NULL, '\"[\\\"heat-treatment\\\",\\\"quenching\\\",\\\"steel\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-30 07:02:52', '2025-06-30 07:02:52', NULL);
INSERT INTO `comments` VALUES ('216', '69', '5', NULL, 'Về **7075-T6 aluminum**, thêm thông tin machining:

**Cutting parameters:**
```
Speed: 300-500 m/min
Feed: 0.15-0.25 mm/tooth
Depth: 2-8 mm
Coolant: Flood recommended
```

**Tool selection:**
- Uncoated carbide preferred
- Sharp cutting edges essential
- Positive rake angle: 15-20°
- Helix angle: 45° minimum

**Surface finish:** Ra 0.8-1.6 μm achievable with proper setup', '0', '1', '0', NULL, '10', '2', '0', '2', '3.61', '4.53', 'verified', NULL, NULL, '\"[\\\"aluminum\\\",\\\"machining\\\",\\\"aerospace\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '2', NULL, NULL, '2025-06-24 09:12:56', '2025-06-24 09:12:56', NULL);
INSERT INTO `comments` VALUES ('217', '70', '6', NULL, 'Thông tin về **heat treatment** rất chi tiết! Bổ sung về **quenching media:**

**Water quenching:**
- Cooling rate: ~600°C/s
- Risk: High distortion, cracking
- Use: Simple geometry, low carbon steel

**Oil quenching:**
- Cooling rate: ~150°C/s  
- Better: Less distortion
- Use: Complex parts, medium carbon

**Polymer quenching:**
- Cooling rate: Variable (100-400°C/s)
- Advantage: Controllable cooling curve
- Cost: Higher than oil/water', '0', '0', '0', NULL, '4', '4', '4', '1', '4.07', '4.68', 'unverified', NULL, NULL, '\"[\\\"heat-treatment\\\",\\\"quenching\\\",\\\"steel\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-10 09:42:51', '2025-06-10 09:42:51', NULL);
INSERT INTO `comments` VALUES ('218', '70', '12', NULL, 'Về **7075-T6 aluminum**, thêm thông tin machining:

**Cutting parameters:**
```
Speed: 300-500 m/min
Feed: 0.15-0.25 mm/tooth
Depth: 2-8 mm
Coolant: Flood recommended
```

**Tool selection:**
- Uncoated carbide preferred
- Sharp cutting edges essential
- Positive rake angle: 15-20°
- Helix angle: 45° minimum

**Surface finish:** Ra 0.8-1.6 μm achievable with proper setup', '0', '1', '0', NULL, '2', '1', '3', '3', '3.02', '3.94', 'pending', '4', '2025-06-16 17:57:52', '\"[\\\"aluminum\\\",\\\"machining\\\",\\\"aerospace\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '2', NULL, NULL, '2025-06-29 09:51:45', '2025-06-29 09:51:45', NULL);
INSERT INTO `comments` VALUES ('219', '71', '18', NULL, 'Thông tin về **heat treatment** rất chi tiết! Bổ sung về **quenching media:**

**Water quenching:**
- Cooling rate: ~600°C/s
- Risk: High distortion, cracking
- Use: Simple geometry, low carbon steel

**Oil quenching:**
- Cooling rate: ~150°C/s  
- Better: Less distortion
- Use: Complex parts, medium carbon

**Polymer quenching:**
- Cooling rate: Variable (100-400°C/s)
- Advantage: Controllable cooling curve
- Cost: Higher than oil/water', '0', '0', '0', NULL, '14', '3', '4', '2', '4.12', '4.92', 'unverified', NULL, '2025-06-22 17:57:52', '\"[\\\"heat-treatment\\\",\\\"quenching\\\",\\\"steel\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-07-02 00:18:01', '2025-07-02 00:18:01', NULL);
INSERT INTO `comments` VALUES ('220', '71', '6', NULL, 'Về **7075-T6 aluminum**, thêm thông tin machining:

**Cutting parameters:**
```
Speed: 300-500 m/min
Feed: 0.15-0.25 mm/tooth
Depth: 2-8 mm
Coolant: Flood recommended
```

**Tool selection:**
- Uncoated carbide preferred
- Sharp cutting edges essential
- Positive rake angle: 15-20°
- Helix angle: 45° minimum

**Surface finish:** Ra 0.8-1.6 μm achievable with proper setup', '0', '1', '0', NULL, '3', '1', '8', '2', '3.52', '4.48', 'pending', NULL, NULL, '\"[\\\"aluminum\\\",\\\"machining\\\",\\\"aerospace\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '2', NULL, NULL, '2025-07-02 04:38:21', '2025-07-02 04:38:21', NULL);
INSERT INTO `comments` VALUES ('221', '72', '6', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '16', '0', '14', '2', '4.39', '3.61', 'verified', '31', NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-20 20:53:24', '2025-06-20 20:53:24', NULL);
INSERT INTO `comments` VALUES ('222', '72', '16', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '8', '4', '10', '2', '3.53', '3.74', 'pending', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-07-01 05:40:57', '2025-07-01 05:40:57', NULL);
INSERT INTO `comments` VALUES ('223', '72', '5', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '15', '1', '4', '1', '4.26', '3.70', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-23 03:54:51', '2025-06-23 03:54:51', NULL);
INSERT INTO `comments` VALUES ('224', '73', '35', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '13', '4', '0', '3', '4.17', '4.80', 'unverified', '18', NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-05-29 14:31:30', '2025-05-29 14:31:30', NULL);
INSERT INTO `comments` VALUES ('225', '73', '25', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '18', '0', '0', '1', '3.38', '4.85', 'unverified', NULL, '2025-06-15 17:57:52', '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-28 06:26:37', '2025-06-28 06:26:37', NULL);
INSERT INTO `comments` VALUES ('226', '74', '29', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '6', '0', '0', '3', '4.87', '3.57', 'unverified', NULL, '2025-06-21 17:57:52', '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-07-02 01:17:10', '2025-07-02 01:17:10', NULL);
INSERT INTO `comments` VALUES ('227', '74', '30', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '0', '1', '10', '3', '3.04', '4.29', 'verified', '24', NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-30 06:02:36', '2025-06-30 06:02:36', NULL);
INSERT INTO `comments` VALUES ('228', '74', '9', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '15', '1', '2', '0', '3.71', '4.50', 'verified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-07-01 22:02:20', '2025-07-01 22:02:20', NULL);
INSERT INTO `comments` VALUES ('229', '74', '10', '227', 'Agree với points này. Đặc biệt là phần về cost-benefit analysis.', '0', '1', '0', NULL, '0', '2', '5', '0', '4.31', '3.04', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-30 08:13:09', '2025-06-30 08:13:09', NULL);
INSERT INTO `comments` VALUES ('230', '75', '21', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '2', '4', '14', '2', '4.52', '4.44', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-27 12:38:09', '2025-06-27 12:38:09', NULL);
INSERT INTO `comments` VALUES ('231', '75', '17', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '24', '2', '1', '0', '4.62', '3.84', 'unverified', NULL, '2025-06-22 17:57:52', '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-04-07 20:37:39', '2025-04-07 20:37:39', NULL);
INSERT INTO `comments` VALUES ('232', '75', '30', '230', 'Có alternative solution nào khác không? Current approach hơi complex.', '0', '1', '0', NULL, '6', '0', '8', '0', '4.31', '3.54', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-07 16:44:50', '2025-06-07 16:44:50', NULL);
INSERT INTO `comments` VALUES ('233', '76', '34', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '22', '5', '1', '3', '4.14', '4.85', 'unverified', NULL, '2025-06-19 17:57:52', '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-29 11:30:40', '2025-06-29 11:30:40', NULL);
INSERT INTO `comments` VALUES ('234', '76', '13', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '2', '0', '6', '0', '4.58', '3.98', 'unverified', NULL, '2025-06-22 17:57:52', '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-07-02 04:39:57', '2025-07-02 04:39:57', NULL);
INSERT INTO `comments` VALUES ('235', '76', '21', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '1', '0', '1', '3', '4.51', '3.80', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-30 03:24:55', '2025-06-30 03:24:55', NULL);
INSERT INTO `comments` VALUES ('236', '76', '1', '233', 'Mình đã thử method này và thấy kết quả khá tốt. Recommend!', '0', '0', '0', NULL, '4', '0', '3', '1', '4.12', '3.32', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-30 23:48:20', '2025-06-30 23:48:20', NULL);
INSERT INTO `comments` VALUES ('237', '76', '8', '235', 'Thông tin rất hữu ích. Có thể bạn elaborate thêm về phần implementation không?', '0', '1', '0', NULL, '5', '1', '0', '1', '2.63', '3.76', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-01 03:39:29', '2025-07-01 03:39:29', NULL);
INSERT INTO `comments` VALUES ('238', '77', '26', NULL, 'Thông tin về **heat treatment** rất chi tiết! Bổ sung về **quenching media:**

**Water quenching:**
- Cooling rate: ~600°C/s
- Risk: High distortion, cracking
- Use: Simple geometry, low carbon steel

**Oil quenching:**
- Cooling rate: ~150°C/s  
- Better: Less distortion
- Use: Complex parts, medium carbon

**Polymer quenching:**
- Cooling rate: Variable (100-400°C/s)
- Advantage: Controllable cooling curve
- Cost: Higher than oil/water', '0', '0', '0', NULL, '10', '2', '12', '3', '4.59', '3.89', 'unverified', NULL, NULL, '\"[\\\"heat-treatment\\\",\\\"quenching\\\",\\\"steel\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-04 00:34:14', '2025-06-04 00:34:14', NULL);
INSERT INTO `comments` VALUES ('239', '77', '8', NULL, 'Về **7075-T6 aluminum**, thêm thông tin machining:

**Cutting parameters:**
```
Speed: 300-500 m/min
Feed: 0.15-0.25 mm/tooth
Depth: 2-8 mm
Coolant: Flood recommended
```

**Tool selection:**
- Uncoated carbide preferred
- Sharp cutting edges essential
- Positive rake angle: 15-20°
- Helix angle: 45° minimum

**Surface finish:** Ra 0.8-1.6 μm achievable with proper setup', '0', '1', '0', NULL, '11', '2', '3', '0', '3.73', '4.77', 'unverified', '34', NULL, '\"[\\\"aluminum\\\",\\\"machining\\\",\\\"aerospace\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '2', NULL, NULL, '2025-06-12 05:57:15', '2025-06-12 05:57:15', NULL);
INSERT INTO `comments` VALUES ('240', '77', '6', '238', 'Bổ sung thêm: cần chú ý về safety requirements khi implement.', '0', '0', '0', NULL, '10', '2', '5', '0', '2.78', '3.55', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-28 13:01:09', '2025-05-28 13:01:09', NULL);
INSERT INTO `comments` VALUES ('241', '78', '20', NULL, 'Thông tin về **heat treatment** rất chi tiết! Bổ sung về **quenching media:**

**Water quenching:**
- Cooling rate: ~600°C/s
- Risk: High distortion, cracking
- Use: Simple geometry, low carbon steel

**Oil quenching:**
- Cooling rate: ~150°C/s  
- Better: Less distortion
- Use: Complex parts, medium carbon

**Polymer quenching:**
- Cooling rate: Variable (100-400°C/s)
- Advantage: Controllable cooling curve
- Cost: Higher than oil/water', '0', '0', '0', NULL, '9', '2', '14', '0', '4.54', '4.78', 'verified', NULL, NULL, '\"[\\\"heat-treatment\\\",\\\"quenching\\\",\\\"steel\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-07-02 02:15:12', '2025-07-02 02:15:12', NULL);
INSERT INTO `comments` VALUES ('242', '79', '23', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '13', '1', '0', '1', '3.40', '4.88', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-03 13:11:32', '2025-06-03 13:11:32', NULL);
INSERT INTO `comments` VALUES ('243', '79', '31', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '2', '2', '13', '2', '3.52', '4.33', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-30 01:34:06', '2025-06-30 01:34:06', NULL);
INSERT INTO `comments` VALUES ('244', '79', '26', '242', 'Có alternative solution nào khác không? Current approach hơi complex.', '0', '1', '0', NULL, '0', '1', '3', '0', '3.43', '3.76', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-01 01:38:15', '2025-06-01 01:38:15', NULL);
INSERT INTO `comments` VALUES ('245', '79', '8', '243', 'Bổ sung thêm: cần chú ý về safety requirements khi implement.', '0', '1', '0', NULL, '5', '0', '8', '0', '2.71', '3.05', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-28 03:34:37', '2025-05-28 03:34:37', NULL);
INSERT INTO `comments` VALUES ('246', '80', '4', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '5', '2', '13', '0', '4.75', '3.50', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-27 14:22:19', '2025-06-27 14:22:19', NULL);
INSERT INTO `comments` VALUES ('247', '80', '10', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '18', '4', '3', '0', '4.67', '3.90', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-26 19:29:44', '2025-06-26 19:29:44', NULL);
INSERT INTO `comments` VALUES ('248', '80', '3', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '18', '4', '1', '3', '4.00', '3.52', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-30 16:53:28', '2025-06-30 16:53:28', NULL);
INSERT INTO `comments` VALUES ('249', '80', '26', '248', 'Agree với points này. Đặc biệt là phần về cost-benefit analysis.', '0', '0', '0', NULL, '6', '0', '5', '1', '3.94', '3.76', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-11 13:47:25', '2025-06-11 13:47:25', NULL);
INSERT INTO `comments` VALUES ('250', '81', '19', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '25', '4', '0', '2', '3.83', '3.77', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-05-17 15:15:39', '2025-05-17 15:15:39', NULL);
INSERT INTO `comments` VALUES ('251', '82', '18', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '15', '4', '15', '2', '3.54', '4.05', 'verified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-05-09 05:01:52', '2025-05-09 05:01:52', NULL);
INSERT INTO `comments` VALUES ('252', '82', '32', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '1', '0', '15', '2', '3.27', '4.26', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-04-19 01:28:26', '2025-04-19 01:28:26', NULL);
INSERT INTO `comments` VALUES ('253', '82', '10', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '8', '3', '7', '3', '3.04', '3.93', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-24 03:24:13', '2025-06-24 03:24:13', NULL);
INSERT INTO `comments` VALUES ('254', '82', '33', '252', 'Mình đã thử method này và thấy kết quả khá tốt. Recommend!', '0', '0', '0', NULL, '5', '2', '8', '0', '3.19', '3.91', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-06 03:52:56', '2025-05-06 03:52:56', NULL);
INSERT INTO `comments` VALUES ('255', '82', '22', '253', 'Upvoted! Đây chính xác là thông tin mình cần.', '0', '0', '0', NULL, '7', '1', '2', '0', '3.17', '4.42', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-17 09:30:42', '2025-06-17 09:30:42', NULL);
INSERT INTO `comments` VALUES ('256', '83', '9', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '23', '1', '3', '3', '4.29', '4.85', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-24 01:04:52', '2025-06-24 01:04:52', NULL);
INSERT INTO `comments` VALUES ('257', '83', '9', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '14', '4', '4', '1', '4.67', '4.48', 'pending', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-03 06:05:07', '2025-06-03 06:05:07', NULL);
INSERT INTO `comments` VALUES ('258', '83', '11', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '2', '0', '7', '3', '4.12', '4.28', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-05-22 08:38:20', '2025-05-22 08:38:20', NULL);
INSERT INTO `comments` VALUES ('259', '84', '5', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '11', '2', '10', '2', '3.00', '4.08', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-28 13:21:02', '2025-06-28 13:21:02', NULL);
INSERT INTO `comments` VALUES ('260', '84', '22', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '9', '5', '12', '3', '4.10', '3.99', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-19 13:54:24', '2025-06-19 13:54:24', NULL);
INSERT INTO `comments` VALUES ('261', '84', '20', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '5', '2', '14', '3', '4.69', '3.60', 'unverified', NULL, '2025-06-16 17:57:52', '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-14 12:05:41', '2025-06-14 12:05:41', NULL);
INSERT INTO `comments` VALUES ('262', '84', '10', '261', 'Agree với points này. Đặc biệt là phần về cost-benefit analysis.', '0', '1', '0', NULL, '5', '1', '3', '1', '4.01', '3.28', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-21 03:18:46', '2025-06-21 03:18:46', NULL);
INSERT INTO `comments` VALUES ('263', '85', '6', NULL, 'Thông tin về **heat treatment** rất chi tiết! Bổ sung về **quenching media:**

**Water quenching:**
- Cooling rate: ~600°C/s
- Risk: High distortion, cracking
- Use: Simple geometry, low carbon steel

**Oil quenching:**
- Cooling rate: ~150°C/s  
- Better: Less distortion
- Use: Complex parts, medium carbon

**Polymer quenching:**
- Cooling rate: Variable (100-400°C/s)
- Advantage: Controllable cooling curve
- Cost: Higher than oil/water', '0', '0', '0', NULL, '9', '3', '0', '2', '4.69', '3.79', 'verified', '13', '2025-06-21 17:57:52', '\"[\\\"heat-treatment\\\",\\\"quenching\\\",\\\"steel\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-14 20:57:00', '2025-06-14 20:57:00', NULL);
INSERT INTO `comments` VALUES ('264', '85', '14', NULL, 'Về **7075-T6 aluminum**, thêm thông tin machining:

**Cutting parameters:**
```
Speed: 300-500 m/min
Feed: 0.15-0.25 mm/tooth
Depth: 2-8 mm
Coolant: Flood recommended
```

**Tool selection:**
- Uncoated carbide preferred
- Sharp cutting edges essential
- Positive rake angle: 15-20°
- Helix angle: 45° minimum

**Surface finish:** Ra 0.8-1.6 μm achievable with proper setup', '0', '1', '0', NULL, '9', '0', '10', '1', '3.49', '4.29', 'disputed', NULL, NULL, '\"[\\\"aluminum\\\",\\\"machining\\\",\\\"aerospace\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '0', NULL, NULL, '2025-05-20 01:41:47', '2025-05-20 01:41:47', NULL);
INSERT INTO `comments` VALUES ('265', '85', '17', '264', 'Interesting approach! Có case study nào cụ thể không?', '0', '1', '0', NULL, '3', '1', '3', '0', '3.76', '3.64', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-28 11:23:32', '2025-06-28 11:23:32', NULL);
INSERT INTO `comments` VALUES ('266', '86', '10', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '6', '5', '7', '3', '3.72', '4.24', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-25 20:09:40', '2025-06-25 20:09:40', NULL);
INSERT INTO `comments` VALUES ('267', '86', '1', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '5', '4', '12', '3', '3.91', '4.59', 'pending', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-12 20:17:24', '2025-06-12 20:17:24', NULL);
INSERT INTO `comments` VALUES ('268', '87', '14', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '1', '1', '8', '0', '3.69', '4.72', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-02 00:27:12', '2025-07-02 00:27:12', NULL);
INSERT INTO `comments` VALUES ('269', '87', '15', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '21', '5', '9', '3', '4.59', '3.85', 'unverified', NULL, '2025-06-17 17:57:53', '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-15 06:58:34', '2025-06-15 06:58:34', NULL);
INSERT INTO `comments` VALUES ('270', '87', '32', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '18', '4', '6', '2', '4.16', '4.01', 'unverified', '24', NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-18 12:32:22', '2025-06-18 12:32:22', NULL);
INSERT INTO `comments` VALUES ('271', '88', '22', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '7', '2', '13', '3', '4.06', '4.78', 'pending', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-30 10:09:27', '2025-06-30 10:09:27', NULL);
INSERT INTO `comments` VALUES ('272', '88', '15', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '16', '0', '9', '2', '3.70', '4.84', 'unverified', '25', NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-27 06:01:52', '2025-06-27 06:01:52', NULL);
INSERT INTO `comments` VALUES ('273', '88', '31', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '18', '3', '13', '3', '3.93', '3.58', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-28 06:19:27', '2025-06-28 06:19:27', NULL);
INSERT INTO `comments` VALUES ('274', '89', '25', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '0', '4', '8', '1', '4.28', '3.97', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-07-01 14:59:55', '2025-07-01 14:59:55', NULL);
INSERT INTO `comments` VALUES ('275', '89', '4', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '14', '0', '7', '3', '3.92', '3.92', 'pending', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-30 05:07:14', '2025-06-30 05:07:14', NULL);
INSERT INTO `comments` VALUES ('276', '89', '23', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '11', '5', '0', '2', '3.24', '4.78', 'unverified', '33', NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-30 13:07:55', '2025-06-30 13:07:55', NULL);
INSERT INTO `comments` VALUES ('277', '89', '25', '275', 'Mình đã thử method này và thấy kết quả khá tốt. Recommend!', '0', '1', '0', NULL, '0', '0', '3', '0', '3.61', '3.31', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-27 00:46:10', '2025-06-27 00:46:10', NULL);
INSERT INTO `comments` VALUES ('278', '90', '35', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '0', '3', '13', '3', '3.06', '4.28', 'disputed', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-07 23:29:16', '2025-05-07 23:29:16', NULL);
INSERT INTO `comments` VALUES ('279', '90', '10', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '4', '0', '3', '3', '3.36', '4.70', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-07-01 17:48:19', '2025-07-01 17:48:19', NULL);
INSERT INTO `comments` VALUES ('280', '90', '9', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '13', '0', '0', '3', '3.20', '4.34', 'pending', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-04-18 18:04:18', '2025-04-18 18:04:18', NULL);
INSERT INTO `comments` VALUES ('281', '91', '19', NULL, 'Thông tin về **heat treatment** rất chi tiết! Bổ sung về **quenching media:**

**Water quenching:**
- Cooling rate: ~600°C/s
- Risk: High distortion, cracking
- Use: Simple geometry, low carbon steel

**Oil quenching:**
- Cooling rate: ~150°C/s  
- Better: Less distortion
- Use: Complex parts, medium carbon

**Polymer quenching:**
- Cooling rate: Variable (100-400°C/s)
- Advantage: Controllable cooling curve
- Cost: Higher than oil/water', '0', '0', '0', NULL, '5', '5', '9', '3', '4.98', '3.53', 'pending', NULL, NULL, '\"[\\\"heat-treatment\\\",\\\"quenching\\\",\\\"steel\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-26 05:13:17', '2025-06-26 05:13:17', NULL);
INSERT INTO `comments` VALUES ('282', '91', '3', NULL, 'Về **7075-T6 aluminum**, thêm thông tin machining:

**Cutting parameters:**
```
Speed: 300-500 m/min
Feed: 0.15-0.25 mm/tooth
Depth: 2-8 mm
Coolant: Flood recommended
```

**Tool selection:**
- Uncoated carbide preferred
- Sharp cutting edges essential
- Positive rake angle: 15-20°
- Helix angle: 45° minimum

**Surface finish:** Ra 0.8-1.6 μm achievable with proper setup', '0', '1', '0', NULL, '21', '3', '3', '2', '4.98', '4.32', 'unverified', '20', NULL, '\"[\\\"aluminum\\\",\\\"machining\\\",\\\"aerospace\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '2', NULL, NULL, '2025-06-27 20:06:52', '2025-06-27 20:06:52', NULL);
INSERT INTO `comments` VALUES ('283', '92', '21', NULL, 'Thông tin về **heat treatment** rất chi tiết! Bổ sung về **quenching media:**

**Water quenching:**
- Cooling rate: ~600°C/s
- Risk: High distortion, cracking
- Use: Simple geometry, low carbon steel

**Oil quenching:**
- Cooling rate: ~150°C/s  
- Better: Less distortion
- Use: Complex parts, medium carbon

**Polymer quenching:**
- Cooling rate: Variable (100-400°C/s)
- Advantage: Controllable cooling curve
- Cost: Higher than oil/water', '0', '0', '0', NULL, '24', '3', '7', '3', '3.80', '3.77', 'unverified', NULL, NULL, '\"[\\\"heat-treatment\\\",\\\"quenching\\\",\\\"steel\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-07-02 00:36:27', '2025-07-02 00:36:27', NULL);
INSERT INTO `comments` VALUES ('284', '92', '21', NULL, 'Về **7075-T6 aluminum**, thêm thông tin machining:

**Cutting parameters:**
```
Speed: 300-500 m/min
Feed: 0.15-0.25 mm/tooth
Depth: 2-8 mm
Coolant: Flood recommended
```

**Tool selection:**
- Uncoated carbide preferred
- Sharp cutting edges essential
- Positive rake angle: 15-20°
- Helix angle: 45° minimum

**Surface finish:** Ra 0.8-1.6 μm achievable with proper setup', '0', '1', '0', NULL, '2', '5', '14', '0', '4.21', '4.49', 'disputed', NULL, NULL, '\"[\\\"aluminum\\\",\\\"machining\\\",\\\"aerospace\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '1', NULL, NULL, '2025-07-01 15:57:53', '2025-07-01 15:57:53', NULL);
INSERT INTO `comments` VALUES ('285', '92', '30', '284', 'Có alternative solution nào khác không? Current approach hơi complex.', '0', '0', '0', NULL, '1', '2', '4', '0', '4.22', '3.37', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-30 17:18:56', '2025-06-30 17:18:56', NULL);
INSERT INTO `comments` VALUES ('286', '93', '33', NULL, 'Code ladder rất clear! Thêm một số tips cho **S7-1200**:

```ladder
// Memory optimization
Network 1: Pulse Generator
+--[CLK]--+--[TON]--+--( )--+
|  M0.0   |   T1    |  M0.1 |
+--------+  PT:500ms +-------+

// Edge detection
Network 2: Rising Edge
+--[P]---+--( )--+
|  I0.0  |  M1.0 |
+--------+-------+
```

**Addressing tips:**
- I0.0-I0.7: Digital inputs
- Q0.0-Q0.7: Digital outputs  
- M0.0-M255.7: Memory bits', '0', '1', '0', NULL, '3', '5', '1', '3', '3.17', '4.43', 'verified', '8', NULL, '\"[\\\"plc\\\",\\\"ladder\\\",\\\"siemens\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '0', NULL, NULL, '2025-06-23 07:16:30', '2025-06-23 07:16:30', NULL);
INSERT INTO `comments` VALUES ('287', '94', '2', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '23', '5', '1', '0', '4.52', '4.24', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-07-01 16:15:06', '2025-07-01 16:15:06', NULL);
INSERT INTO `comments` VALUES ('288', '94', '28', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '18', '5', '2', '3', '3.23', '3.85', 'unverified', '10', NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-07-01 21:03:46', '2025-07-01 21:03:46', NULL);
INSERT INTO `comments` VALUES ('289', '94', '27', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '11', '4', '7', '0', '4.20', '4.57', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-30 03:51:49', '2025-06-30 03:51:49', NULL);
INSERT INTO `comments` VALUES ('290', '94', '30', '288', 'Upvoted! Đây chính xác là thông tin mình cần.', '0', '0', '0', NULL, '1', '1', '1', '1', '3.80', '4.16', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-23 09:55:16', '2025-06-23 09:55:16', NULL);
INSERT INTO `comments` VALUES ('291', '94', '34', '289', 'Mình đã thử method này và thấy kết quả khá tốt. Recommend!', '0', '0', '0', NULL, '8', '1', '3', '0', '2.66', '4.43', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-29 15:25:06', '2025-06-29 15:25:06', NULL);
INSERT INTO `comments` VALUES ('292', '95', '11', NULL, 'Great case study! Về **ABB RAPID programming**, một số tips:

```rapid
! Optimized move sequence
MoveJ pHome, v1000, fine, tool0;
MoveL Offs(pTarget, 0, 0, 50), v500, z10, tool0;
MoveL pTarget, v100, fine, tool0;

! Error handling
IF DOutput(doGripper) = 0 THEN
    TPWrite \"Gripper failed!\";
    Stop;
ENDIF
```

**Cycle time optimization:**
- Sử dụng **zone data** thay vì fine
- **Concurrent I/O** operations
- **Path blending** cho smooth motion', '0', '1', '0', NULL, '14', '4', '12', '0', '4.37', '3.90', 'verified', NULL, NULL, '\"[\\\"abb\\\",\\\"rapid\\\",\\\"programming\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '1', NULL, NULL, '2025-06-20 16:28:06', '2025-06-20 16:28:06', NULL);
INSERT INTO `comments` VALUES ('293', '96', '28', NULL, 'Great case study! Về **ABB RAPID programming**, một số tips:

```rapid
! Optimized move sequence
MoveJ pHome, v1000, fine, tool0;
MoveL Offs(pTarget, 0, 0, 50), v500, z10, tool0;
MoveL pTarget, v100, fine, tool0;

! Error handling
IF DOutput(doGripper) = 0 THEN
    TPWrite \"Gripper failed!\";
    Stop;
ENDIF
```

**Cycle time optimization:**
- Sử dụng **zone data** thay vì fine
- **Concurrent I/O** operations
- **Path blending** cho smooth motion', '0', '1', '0', NULL, '22', '4', '12', '2', '4.82', '3.84', 'unverified', NULL, NULL, '\"[\\\"abb\\\",\\\"rapid\\\",\\\"programming\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '0', NULL, NULL, '2025-06-29 10:51:43', '2025-06-29 10:51:43', NULL);
INSERT INTO `comments` VALUES ('294', '96', '4', NULL, '**ROI calculation** rất impressive! Thêm một số factors khác:

**Hidden costs:**
- Training: $5,000-10,000
- Maintenance: $8,000/year
- Spare parts inventory: $15,000
- Safety upgrades: $20,000

**Additional benefits:**
- Reduced insurance costs
- Improved workplace safety
- 24/7 operation capability
- Consistent quality

**Payback formula:** 
Payback = Initial Investment / (Annual Savings - Annual Costs)', '0', '0', '1', 'Payback = \\frac{Initial\\ Investment}{Annual\\ Savings - Annual\\ Costs}', '24', '5', '2', '0', '4.65', '4.24', 'unverified', NULL, NULL, '\"[\\\"roi\\\",\\\"economics\\\",\\\"business-case\\\"]\"', 'calculation', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-30 05:06:52', '2025-06-30 05:06:52', NULL);
INSERT INTO `comments` VALUES ('295', '96', '15', '293', 'Upvoted! Đây chính xác là thông tin mình cần.', '0', '1', '0', NULL, '2', '2', '6', '1', '2.98', '3.90', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-30 14:55:44', '2025-06-30 14:55:44', NULL);
INSERT INTO `comments` VALUES ('296', '97', '8', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '9', '0', '11', '2', '4.93', '4.61', 'pending', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-04-16 20:22:47', '2025-04-16 20:22:47', NULL);
INSERT INTO `comments` VALUES ('297', '97', '14', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '10', '3', '15', '1', '4.13', '3.91', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-12 19:27:52', '2025-06-12 19:27:52', NULL);
INSERT INTO `comments` VALUES ('298', '97', '23', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '17', '1', '4', '3', '4.31', '4.61', 'verified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-09 12:55:53', '2025-06-09 12:55:53', NULL);
INSERT INTO `comments` VALUES ('299', '98', '6', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '25', '3', '1', '3', '4.37', '3.75', 'verified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-27 11:34:21', '2025-05-27 11:34:21', NULL);
INSERT INTO `comments` VALUES ('300', '98', '25', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '4', '1', '8', '2', '3.74', '4.52', 'verified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-08 16:54:40', '2025-06-08 16:54:40', NULL);
INSERT INTO `comments` VALUES ('301', '98', '34', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '22', '4', '13', '1', '4.83', '4.97', 'unverified', '22', NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-05-31 00:40:40', '2025-05-31 00:40:40', NULL);
INSERT INTO `comments` VALUES ('302', '98', '28', '300', 'Agree với points này. Đặc biệt là phần về cost-benefit analysis.', '0', '1', '0', NULL, '4', '1', '3', '1', '3.13', '4.07', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-03 06:32:03', '2025-05-03 06:32:03', NULL);
INSERT INTO `comments` VALUES ('303', '99', '1', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '8', '2', '7', '3', '3.31', '4.51', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-18 22:48:03', '2025-05-18 22:48:03', NULL);
INSERT INTO `comments` VALUES ('304', '99', '15', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '10', '1', '10', '2', '4.99', '4.71', 'pending', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-27 17:56:37', '2025-06-27 17:56:37', NULL);
INSERT INTO `comments` VALUES ('305', '99', '25', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '18', '5', '13', '2', '4.63', '4.19', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-08 18:47:18', '2025-06-08 18:47:18', NULL);
INSERT INTO `comments` VALUES ('306', '99', '15', '303', 'Có alternative solution nào khác không? Current approach hơi complex.', '0', '1', '0', NULL, '8', '0', '6', '1', '4.05', '4.12', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-07 07:49:58', '2025-05-07 07:49:58', NULL);
INSERT INTO `comments` VALUES ('307', '100', '12', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '8', '1', '7', '3', '4.95', '3.79', 'verified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-22 02:51:08', '2025-06-22 02:51:08', NULL);
INSERT INTO `comments` VALUES ('308', '100', '25', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '10', '0', '6', '2', '3.20', '4.96', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-07-01 05:34:14', '2025-07-01 05:34:14', NULL);
INSERT INTO `comments` VALUES ('309', '100', '15', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '17', '4', '14', '0', '3.25', '4.62', 'pending', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-18 22:56:54', '2025-06-18 22:56:54', NULL);
INSERT INTO `comments` VALUES ('310', '101', '29', NULL, 'Code ladder rất clear! Thêm một số tips cho **S7-1200**:

```ladder
// Memory optimization
Network 1: Pulse Generator
+--[CLK]--+--[TON]--+--( )--+
|  M0.0   |   T1    |  M0.1 |
+--------+  PT:500ms +-------+

// Edge detection
Network 2: Rising Edge
+--[P]---+--( )--+
|  I0.0  |  M1.0 |
+--------+-------+
```

**Addressing tips:**
- I0.0-I0.7: Digital inputs
- Q0.0-Q0.7: Digital outputs  
- M0.0-M255.7: Memory bits', '0', '1', '0', NULL, '8', '0', '0', '0', '4.42', '3.73', 'verified', NULL, NULL, '\"[\\\"plc\\\",\\\"ladder\\\",\\\"siemens\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '1', NULL, NULL, '2025-06-02 18:26:11', '2025-06-02 18:26:11', NULL);
INSERT INTO `comments` VALUES ('311', '101', '17', NULL, 'Về **TIA Portal**, một số shortcuts hữu ích:

- **Ctrl+1**: Switch to Portal view
- **Ctrl+2**: Switch to Project view
- **F7**: Start simulation
- **F8**: Download to PLC

**Best practices:**
1. Luôn comment networks
2. Sử dụng symbolic addressing
3. Organize code bằng FCs và FBs
4. Regular backup projects', '0', '0', '0', NULL, '12', '2', '11', '0', '3.76', '3.89', 'unverified', NULL, NULL, '\"[\\\"tia-portal\\\",\\\"programming\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-05-18 15:42:13', '2025-05-18 15:42:13', NULL);
INSERT INTO `comments` VALUES ('312', '101', '15', '310', 'Bổ sung thêm: cần chú ý về safety requirements khi implement.', '0', '0', '0', NULL, '7', '0', '8', '0', '3.71', '3.82', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-07 20:32:15', '2025-05-07 20:32:15', NULL);
INSERT INTO `comments` VALUES ('313', '102', '22', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '23', '0', '14', '0', '3.38', '4.51', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-14 19:49:39', '2025-06-14 19:49:39', NULL);
INSERT INTO `comments` VALUES ('314', '103', '2', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '1', '1', '7', '3', '3.91', '4.35', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-29 23:01:42', '2025-06-29 23:01:42', NULL);
INSERT INTO `comments` VALUES ('315', '103', '21', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '6', '2', '2', '2', '3.18', '4.81', 'unverified', NULL, '2025-06-15 17:57:53', '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-24 06:36:44', '2025-06-24 06:36:44', NULL);
INSERT INTO `comments` VALUES ('316', '103', '1', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '25', '4', '10', '1', '4.55', '4.93', 'verified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-18 09:55:44', '2025-06-18 09:55:44', NULL);
INSERT INTO `comments` VALUES ('317', '103', '22', '316', 'Bổ sung thêm: cần chú ý về safety requirements khi implement.', '0', '0', '0', NULL, '10', '2', '7', '1', '4.40', '3.14', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-20 01:52:15', '2025-06-20 01:52:15', NULL);
INSERT INTO `comments` VALUES ('318', '104', '8', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '23', '5', '7', '3', '4.66', '3.55', 'unverified', NULL, '2025-06-24 17:57:53', '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-12 10:36:40', '2025-06-12 10:36:40', NULL);
INSERT INTO `comments` VALUES ('319', '104', '4', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '8', '2', '6', '0', '4.90', '3.81', 'verified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-10 20:19:59', '2025-06-10 20:19:59', NULL);
INSERT INTO `comments` VALUES ('320', '105', '3', NULL, 'Code ladder rất clear! Thêm một số tips cho **S7-1200**:

```ladder
// Memory optimization
Network 1: Pulse Generator
+--[CLK]--+--[TON]--+--( )--+
|  M0.0   |   T1    |  M0.1 |
+--------+  PT:500ms +-------+

// Edge detection
Network 2: Rising Edge
+--[P]---+--( )--+
|  I0.0  |  M1.0 |
+--------+-------+
```

**Addressing tips:**
- I0.0-I0.7: Digital inputs
- Q0.0-Q0.7: Digital outputs  
- M0.0-M255.7: Memory bits', '0', '1', '0', NULL, '14', '1', '13', '0', '4.09', '4.38', 'unverified', NULL, NULL, '\"[\\\"plc\\\",\\\"ladder\\\",\\\"siemens\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '2', NULL, NULL, '2025-06-24 00:50:44', '2025-06-24 00:50:44', NULL);
INSERT INTO `comments` VALUES ('321', '105', '1', NULL, 'Về **TIA Portal**, một số shortcuts hữu ích:

- **Ctrl+1**: Switch to Portal view
- **Ctrl+2**: Switch to Project view
- **F7**: Start simulation
- **F8**: Download to PLC

**Best practices:**
1. Luôn comment networks
2. Sử dụng symbolic addressing
3. Organize code bằng FCs và FBs
4. Regular backup projects', '0', '0', '0', NULL, '16', '1', '6', '0', '4.54', '4.87', 'pending', NULL, NULL, '\"[\\\"tia-portal\\\",\\\"programming\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-20 16:09:01', '2025-06-20 16:09:01', NULL);
INSERT INTO `comments` VALUES ('322', '106', '14', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '23', '3', '14', '0', '3.55', '4.53', 'verified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-07-01 10:15:13', '2025-07-01 10:15:13', NULL);
INSERT INTO `comments` VALUES ('323', '106', '24', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '24', '5', '7', '3', '3.17', '4.82', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-01 06:28:14', '2025-06-01 06:28:14', NULL);
INSERT INTO `comments` VALUES ('324', '106', '32', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '23', '3', '12', '1', '3.91', '4.06', 'unverified', '21', NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-01 16:14:59', '2025-05-01 16:14:59', NULL);
INSERT INTO `comments` VALUES ('325', '106', '3', '322', 'Cảm ơn bạn đã chia sẻ! Mình sẽ thử approach này.', '0', '0', '0', NULL, '5', '1', '1', '1', '3.97', '4.05', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-23 23:00:23', '2025-05-23 23:00:23', NULL);
INSERT INTO `comments` VALUES ('326', '106', '23', '324', 'Có alternative solution nào khác không? Current approach hơi complex.', '0', '0', '0', NULL, '5', '2', '5', '1', '3.48', '3.77', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-10 21:52:20', '2025-06-10 21:52:20', NULL);
INSERT INTO `comments` VALUES ('327', '107', '7', NULL, 'Great case study! Về **ABB RAPID programming**, một số tips:

```rapid
! Optimized move sequence
MoveJ pHome, v1000, fine, tool0;
MoveL Offs(pTarget, 0, 0, 50), v500, z10, tool0;
MoveL pTarget, v100, fine, tool0;

! Error handling
IF DOutput(doGripper) = 0 THEN
    TPWrite \"Gripper failed!\";
    Stop;
ENDIF
```

**Cycle time optimization:**
- Sử dụng **zone data** thay vì fine
- **Concurrent I/O** operations
- **Path blending** cho smooth motion', '0', '1', '0', NULL, '13', '4', '15', '3', '3.18', '4.92', 'pending', '26', NULL, '\"[\\\"abb\\\",\\\"rapid\\\",\\\"programming\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '2', NULL, NULL, '2025-07-01 19:29:33', '2025-07-01 19:29:33', NULL);
INSERT INTO `comments` VALUES ('328', '108', '4', NULL, 'Great case study! Về **ABB RAPID programming**, một số tips:

```rapid
! Optimized move sequence
MoveJ pHome, v1000, fine, tool0;
MoveL Offs(pTarget, 0, 0, 50), v500, z10, tool0;
MoveL pTarget, v100, fine, tool0;

! Error handling
IF DOutput(doGripper) = 0 THEN
    TPWrite \"Gripper failed!\";
    Stop;
ENDIF
```

**Cycle time optimization:**
- Sử dụng **zone data** thay vì fine
- **Concurrent I/O** operations
- **Path blending** cho smooth motion', '0', '1', '0', NULL, '23', '3', '15', '2', '4.40', '4.60', 'verified', NULL, '2025-06-23 17:57:53', '\"[\\\"abb\\\",\\\"rapid\\\",\\\"programming\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '1', NULL, NULL, '2025-06-14 17:20:54', '2025-06-14 17:20:54', NULL);
INSERT INTO `comments` VALUES ('329', '108', '28', '328', 'Bổ sung thêm: cần chú ý về safety requirements khi implement.', '0', '1', '0', NULL, '6', '0', '3', '0', '4.07', '3.06', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-24 12:07:22', '2025-06-24 12:07:22', NULL);
INSERT INTO `comments` VALUES ('330', '109', '26', NULL, 'Great case study! Về **ABB RAPID programming**, một số tips:

```rapid
! Optimized move sequence
MoveJ pHome, v1000, fine, tool0;
MoveL Offs(pTarget, 0, 0, 50), v500, z10, tool0;
MoveL pTarget, v100, fine, tool0;

! Error handling
IF DOutput(doGripper) = 0 THEN
    TPWrite \"Gripper failed!\";
    Stop;
ENDIF
```

**Cycle time optimization:**
- Sử dụng **zone data** thay vì fine
- **Concurrent I/O** operations
- **Path blending** cho smooth motion', '0', '1', '0', NULL, '11', '3', '8', '2', '4.54', '4.06', 'unverified', NULL, '2025-06-23 17:57:53', '\"[\\\"abb\\\",\\\"rapid\\\",\\\"programming\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '0', NULL, NULL, '2025-06-16 21:02:17', '2025-06-16 21:02:17', NULL);
INSERT INTO `comments` VALUES ('331', '109', '25', NULL, '**ROI calculation** rất impressive! Thêm một số factors khác:

**Hidden costs:**
- Training: $5,000-10,000
- Maintenance: $8,000/year
- Spare parts inventory: $15,000
- Safety upgrades: $20,000

**Additional benefits:**
- Reduced insurance costs
- Improved workplace safety
- 24/7 operation capability
- Consistent quality

**Payback formula:** 
Payback = Initial Investment / (Annual Savings - Annual Costs)', '0', '0', '1', 'Payback = \\frac{Initial\\ Investment}{Annual\\ Savings - Annual\\ Costs}', '4', '2', '11', '0', '4.55', '4.24', 'unverified', NULL, NULL, '\"[\\\"roi\\\",\\\"economics\\\",\\\"business-case\\\"]\"', 'calculation', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-28 06:47:50', '2025-06-28 06:47:50', NULL);
INSERT INTO `comments` VALUES ('332', '109', '1', '330', 'Agree với points này. Đặc biệt là phần về cost-benefit analysis.', '0', '0', '0', NULL, '4', '1', '1', '1', '3.84', '4.36', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-12 23:39:02', '2025-06-12 23:39:02', NULL);
INSERT INTO `comments` VALUES ('333', '110', '25', NULL, 'Great case study! Về **ABB RAPID programming**, một số tips:

```rapid
! Optimized move sequence
MoveJ pHome, v1000, fine, tool0;
MoveL Offs(pTarget, 0, 0, 50), v500, z10, tool0;
MoveL pTarget, v100, fine, tool0;

! Error handling
IF DOutput(doGripper) = 0 THEN
    TPWrite \"Gripper failed!\";
    Stop;
ENDIF
```

**Cycle time optimization:**
- Sử dụng **zone data** thay vì fine
- **Concurrent I/O** operations
- **Path blending** cho smooth motion', '0', '1', '0', NULL, '6', '3', '4', '1', '4.44', '4.67', 'unverified', NULL, NULL, '\"[\\\"abb\\\",\\\"rapid\\\",\\\"programming\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '2', NULL, NULL, '2025-05-31 11:43:56', '2025-05-31 11:43:56', NULL);
INSERT INTO `comments` VALUES ('334', '110', '23', NULL, '**ROI calculation** rất impressive! Thêm một số factors khác:

**Hidden costs:**
- Training: $5,000-10,000
- Maintenance: $8,000/year
- Spare parts inventory: $15,000
- Safety upgrades: $20,000

**Additional benefits:**
- Reduced insurance costs
- Improved workplace safety
- 24/7 operation capability
- Consistent quality

**Payback formula:** 
Payback = Initial Investment / (Annual Savings - Annual Costs)', '0', '0', '1', 'Payback = \\frac{Initial\\ Investment}{Annual\\ Savings - Annual\\ Costs}', '23', '4', '15', '3', '3.01', '3.68', 'verified', NULL, NULL, '\"[\\\"roi\\\",\\\"economics\\\",\\\"business-case\\\"]\"', 'calculation', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-08 02:04:06', '2025-05-08 02:04:06', NULL);
INSERT INTO `comments` VALUES ('335', '110', '32', '333', 'Agree với points này. Đặc biệt là phần về cost-benefit analysis.', '0', '1', '0', NULL, '9', '0', '3', '0', '2.89', '3.67', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-29 18:33:17', '2025-06-29 18:33:17', NULL);
INSERT INTO `comments` VALUES ('336', '111', '7', NULL, 'Great case study! Về **ABB RAPID programming**, một số tips:

```rapid
! Optimized move sequence
MoveJ pHome, v1000, fine, tool0;
MoveL Offs(pTarget, 0, 0, 50), v500, z10, tool0;
MoveL pTarget, v100, fine, tool0;

! Error handling
IF DOutput(doGripper) = 0 THEN
    TPWrite \"Gripper failed!\";
    Stop;
ENDIF
```

**Cycle time optimization:**
- Sử dụng **zone data** thay vì fine
- **Concurrent I/O** operations
- **Path blending** cho smooth motion', '0', '1', '0', NULL, '25', '4', '0', '0', '4.20', '4.83', 'unverified', NULL, NULL, '\"[\\\"abb\\\",\\\"rapid\\\",\\\"programming\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '0', NULL, NULL, '2025-06-15 21:09:24', '2025-06-15 21:09:24', NULL);
INSERT INTO `comments` VALUES ('337', '111', '23', NULL, '**ROI calculation** rất impressive! Thêm một số factors khác:

**Hidden costs:**
- Training: $5,000-10,000
- Maintenance: $8,000/year
- Spare parts inventory: $15,000
- Safety upgrades: $20,000

**Additional benefits:**
- Reduced insurance costs
- Improved workplace safety
- 24/7 operation capability
- Consistent quality

**Payback formula:** 
Payback = Initial Investment / (Annual Savings - Annual Costs)', '0', '0', '1', 'Payback = \\frac{Initial\\ Investment}{Annual\\ Savings - Annual\\ Costs}', '5', '0', '4', '0', '3.70', '4.54', 'unverified', '4', NULL, '\"[\\\"roi\\\",\\\"economics\\\",\\\"business-case\\\"]\"', 'calculation', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-26 12:38:23', '2025-06-26 12:38:23', NULL);
INSERT INTO `comments` VALUES ('338', '111', '8', '337', 'Bổ sung thêm: cần chú ý về safety requirements khi implement.', '0', '0', '0', NULL, '0', '1', '5', '1', '4.38', '3.82', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-19 10:38:20', '2025-06-19 10:38:20', NULL);
INSERT INTO `comments` VALUES ('339', '112', '28', NULL, 'Great case study! Về **ABB RAPID programming**, một số tips:

```rapid
! Optimized move sequence
MoveJ pHome, v1000, fine, tool0;
MoveL Offs(pTarget, 0, 0, 50), v500, z10, tool0;
MoveL pTarget, v100, fine, tool0;

! Error handling
IF DOutput(doGripper) = 0 THEN
    TPWrite \"Gripper failed!\";
    Stop;
ENDIF
```

**Cycle time optimization:**
- Sử dụng **zone data** thay vì fine
- **Concurrent I/O** operations
- **Path blending** cho smooth motion', '0', '1', '0', NULL, '7', '5', '8', '1', '4.76', '4.65', 'verified', NULL, NULL, '\"[\\\"abb\\\",\\\"rapid\\\",\\\"programming\\\"]\"', 'tutorial', '0', '0', '1', '0', NULL, '0', NULL, NULL, '2025-06-22 10:41:20', '2025-06-22 10:41:20', NULL);
INSERT INTO `comments` VALUES ('340', '112', '23', NULL, '**ROI calculation** rất impressive! Thêm một số factors khác:

**Hidden costs:**
- Training: $5,000-10,000
- Maintenance: $8,000/year
- Spare parts inventory: $15,000
- Safety upgrades: $20,000

**Additional benefits:**
- Reduced insurance costs
- Improved workplace safety
- 24/7 operation capability
- Consistent quality

**Payback formula:** 
Payback = Initial Investment / (Annual Savings - Annual Costs)', '0', '0', '1', 'Payback = \\frac{Initial\\ Investment}{Annual\\ Savings - Annual\\ Costs}', '15', '2', '5', '2', '4.78', '3.60', 'verified', NULL, NULL, '\"[\\\"roi\\\",\\\"economics\\\",\\\"business-case\\\"]\"', 'calculation', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-17 10:43:56', '2025-06-17 10:43:56', NULL);
INSERT INTO `comments` VALUES ('341', '113', '32', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '19', '5', '12', '0', '3.32', '3.68', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-14 01:10:01', '2025-05-14 01:10:01', NULL);
INSERT INTO `comments` VALUES ('342', '113', '30', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '12', '0', '11', '3', '3.69', '4.71', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-02 17:31:18', '2025-06-02 17:31:18', NULL);
INSERT INTO `comments` VALUES ('343', '113', '20', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '8', '4', '5', '0', '3.03', '4.69', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-19 18:40:17', '2025-06-19 18:40:17', NULL);
INSERT INTO `comments` VALUES ('344', '113', '27', '341', 'Interesting approach! Có case study nào cụ thể không?', '0', '1', '0', NULL, '8', '0', '4', '1', '3.28', '3.05', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-25 19:57:10', '2025-06-25 19:57:10', NULL);
INSERT INTO `comments` VALUES ('345', '113', '28', '342', 'Upvoted! Đây chính xác là thông tin mình cần.', '0', '0', '0', NULL, '1', '0', '3', '1', '2.80', '4.46', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-05-04 16:51:55', '2025-05-04 16:51:55', NULL);
INSERT INTO `comments` VALUES ('346', '114', '15', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '6', '4', '3', '0', '3.64', '3.73', 'pending', NULL, '2025-06-21 17:57:53', '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-22 04:25:15', '2025-06-22 04:25:15', NULL);
INSERT INTO `comments` VALUES ('347', '114', '12', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '2', '4', '8', '2', '3.74', '4.70', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-26 18:11:08', '2025-06-26 18:11:08', NULL);
INSERT INTO `comments` VALUES ('348', '114', '3', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '18', '2', '12', '0', '4.99', '4.28', 'verified', NULL, '2025-06-24 17:57:53', '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-26 11:13:01', '2025-06-26 11:13:01', NULL);
INSERT INTO `comments` VALUES ('349', '114', '30', '348', 'Thanks for the detailed explanation. Saved cho reference sau này.', '0', '1', '0', NULL, '0', '0', '1', '0', '2.68', '4.47', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-19 10:05:55', '2025-06-19 10:05:55', NULL);
INSERT INTO `comments` VALUES ('350', '115', '21', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '12', '3', '5', '0', '4.83', '3.68', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-26 18:22:54', '2025-06-26 18:22:54', NULL);
INSERT INTO `comments` VALUES ('351', '115', '7', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '6', '4', '9', '2', '3.08', '3.65', 'pending', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-25 20:11:03', '2025-06-25 20:11:03', NULL);
INSERT INTO `comments` VALUES ('352', '116', '13', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '21', '1', '9', '2', '4.32', '4.46', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-07 05:16:27', '2025-06-07 05:16:27', NULL);
INSERT INTO `comments` VALUES ('353', '116', '8', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '8', '1', '3', '1', '3.81', '4.60', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-04-24 21:56:32', '2025-04-24 21:56:32', NULL);
INSERT INTO `comments` VALUES ('354', '116', '29', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '8', '2', '12', '1', '3.51', '3.79', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-05-15 19:21:16', '2025-05-15 19:21:16', NULL);
INSERT INTO `comments` VALUES ('355', '117', '29', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '21', '3', '1', '0', '4.77', '3.86', 'pending', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '2', NULL, NULL, '2025-06-14 00:16:54', '2025-06-14 00:16:54', NULL);
INSERT INTO `comments` VALUES ('356', '118', '23', NULL, 'Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?', '0', '0', '0', NULL, '25', '1', '11', '2', '3.61', '3.63', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"general\\\"]\"', 'general', '0', '0', '0', '0', NULL, '1', NULL, NULL, '2025-06-30 17:11:51', '2025-06-30 17:11:51', NULL);
INSERT INTO `comments` VALUES ('357', '118', '14', NULL, 'Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:

1. Cần kiểm tra compatibility với hệ thống hiện tại
2. Training cho operators là rất quan trọng
3. Maintenance schedule phải được tuân thủ nghiêm ngặt

Overall, đây là approach đáng thử!', '0', '0', '0', NULL, '14', '1', '10', '0', '4.23', '4.60', 'unverified', NULL, NULL, '\"[\\\"implementation\\\",\\\"best-practices\\\"]\"', 'experience', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-25 02:05:04', '2025-06-25 02:05:04', NULL);
INSERT INTO `comments` VALUES ('358', '118', '32', NULL, 'Excellent explanation! Mình có thêm một số resources hữu ích:

- Standards: ISO 9001, ASME Y14.5
- Software: Miễn phí alternatives
- Training: Online courses và certifications
- Community: Forums và professional groups

Ai cần thêm thông tin có thể PM mình!', '0', '0', '0', NULL, '20', '1', '3', '2', '4.56', '4.50', 'unverified', NULL, NULL, '\"[\\\"resources\\\",\\\"standards\\\",\\\"learning\\\"]\"', 'reference', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-14 10:14:44', '2025-06-14 10:14:44', NULL);
INSERT INTO `comments` VALUES ('359', '118', '29', '357', 'Interesting approach! Có case study nào cụ thể không?', '0', '0', '0', NULL, '10', '1', '0', '1', '3.26', '3.48', 'unverified', NULL, NULL, '\"[\\\"discussion\\\",\\\"follow-up\\\"]\"', 'general', '0', '0', '0', '0', NULL, '0', NULL, NULL, '2025-06-23 23:49:00', '2025-06-23 23:49:00', NULL);

-- Structure for table `forums`
DROP TABLE IF EXISTS `forums`;
CREATE TABLE `forums` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `avatar_url` varchar(191) DEFAULT NULL COMMENT 'URL của avatar/logo cho forum',
  `avatar_media_id` bigint(20) unsigned DEFAULT NULL COMMENT 'ID của media avatar trong bảng media',
  `banner_url` varchar(191) DEFAULT NULL COMMENT 'URL banner cho forum',
  `banner_media_id` bigint(20) unsigned DEFAULT NULL COMMENT 'ID của media banner trong bảng media',
  `gallery_media_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array các ID media cho gallery của forum' CHECK (json_valid(`gallery_media_ids`)),
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `region_id` bigint(20) unsigned DEFAULT NULL,
  `allowed_countries` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allowed_countries`)),
  `primary_languages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`primary_languages`)),
  `scope` enum('global','regional','country','local') NOT NULL DEFAULT 'global',
  `category_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Danh mục mà forum thuộc về',
  `order` int(11) NOT NULL DEFAULT 0,
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  `thread_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Số lượng threads trong forum',
  `post_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Tổng số posts trong forum',
  `last_activity_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian hoạt động cuối cùng',
  `last_thread_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Thread mới nhất',
  `last_post_user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'User post cuối cùng',
  `requires_approval` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Yêu cầu phê duyệt trước khi post',
  `allowed_thread_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Loại thread được phép: ["discussion","question","tutorial"]' CHECK (json_valid(`allowed_thread_types`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `regional_standards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`regional_standards`)),
  `local_regulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`local_regulations`)),
  PRIMARY KEY (`id`),
  UNIQUE KEY `forums_slug_unique` (`slug`),
  KEY `forums_parent_id_order_index` (`parent_id`,`order`),
  KEY `forums_name_index` (`name`),
  KEY `forums_category_display` (`category_id`,`order`,`is_private`),
  KEY `forums_activity_stats` (`last_activity_at`,`thread_count`),
  KEY `forums_last_thread_id_foreign` (`last_thread_id`),
  KEY `forums_last_post_user_id_foreign` (`last_post_user_id`),
  KEY `forums_region_id_is_private_index` (`region_id`,`is_private`),
  KEY `forums_scope_is_private_index` (`scope`,`is_private`),
  KEY `forums_avatar_media_id_foreign` (`avatar_media_id`),
  KEY `forums_banner_media_id_foreign` (`banner_media_id`),
  CONSTRAINT `forums_avatar_media_id_foreign` FOREIGN KEY (`avatar_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  CONSTRAINT `forums_banner_media_id_foreign` FOREIGN KEY (`banner_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  CONSTRAINT `forums_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forums_last_post_user_id_foreign` FOREIGN KEY (`last_post_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `forums_last_thread_id_foreign` FOREIGN KEY (`last_thread_id`) REFERENCES `threads` (`id`) ON DELETE SET NULL,
  CONSTRAINT `forums_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `forums` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forums_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data for table `forums`
INSERT INTO `forums` VALUES ('1', 'CAD/CAM Software', 'cadcam-software-1', 'Thảo luận về phần mềm thiết kế CAD/CAM: SolidWorks, AutoCAD, Inventor, Fusion 360, Mastercam', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '1', '1', '0', '4', '11', '2025-06-24 17:57:51', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('2', 'Phân tích FEA/CFD', 'phan-tich-feacfd-1', 'Finite Element Analysis và Computational Fluid Dynamics: ANSYS, Abaqus, COMSOL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '1', '2', '0', '2', '6', '2025-06-21 17:57:51', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('3', 'Thiết kế máy móc', 'thiet-ke-may-moc-1', 'Thiết kế và tính toán máy móc công nghiệp, cơ cấu, truyền động', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '1', '3', '0', '2', '4', '2025-06-25 17:57:51', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('4', 'Bản vẽ kỹ thuật', 'ban-ve-ky-thuat-1', 'Quy chuẩn bản vẽ, ký hiệu, dung sai, tiêu chuẩn ISO/TCVN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '1', '4', '0', '2', '10', '2025-06-23 17:57:51', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('5', 'SolidWorks', 'solidworks-2', 'Thảo luận về SolidWorks: modeling, assembly, simulation, tips & tricks', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '2', '1', '0', '2', '8', '2025-06-23 05:27:51', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('6', 'AutoCAD', 'autocad-2', 'AutoCAD 2D/3D, AutoCAD Mechanical, bản vẽ kỹ thuật', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '2', '2', '0', '4', '13', '2025-06-23 17:57:51', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('7', 'Inventor', 'inventor-2', 'Autodesk Inventor, parametric modeling, iLogic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '2', '3', '0', '2', '8', '2025-06-25 17:57:51', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('8', 'Fusion 360', 'fusion-360-2', 'Fusion 360 CAD/CAM, cloud-based design, generative design', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '2', '4', '0', '2', '7', '2025-06-19 18:33:51', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('9', 'ANSYS', 'ansys-3', 'ANSYS Workbench, Mechanical, Fluent, CFX simulation', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '3', '1', '0', '2', '4', '2025-06-23 17:57:51', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('10', 'ABAQUS', 'abaqus-3', 'ABAQUS/CAE, nonlinear analysis, advanced materials', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '3', '2', '0', '2', '7', '2025-06-18 17:57:51', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('11', 'COMSOL', 'comsol-3', 'COMSOL Multiphysics, coupled physics simulation', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '3', '3', '0', '2', '5', '2025-06-21 17:57:51', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('12', 'Thảo luận chung - Thiết kế máy móc', 'thao-luan-chung-thiet-ke-may-moc-4', 'Thảo luận chung về Thiết kế máy móc', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '4', '1', '0', '2', '7', '2025-06-22 17:57:51', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('13', 'Hỏi đáp - Thiết kế máy móc', 'hoi-dap-thiet-ke-may-moc-4', 'Hỏi đáp và giải đáp thắc mắc về Thiết kế máy móc', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '4', '2', '0', '2', '6', '2025-06-10 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('14', 'Kinh nghiệm - Thiết kế máy móc', 'kinh-nghiem-thiet-ke-may-moc-4', 'Chia sẻ kinh nghiệm và best practices về Thiết kế máy móc', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '4', '3', '0', '2', '4', '2025-06-22 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('15', 'CNC Machining', 'cnc-machining-5', 'Gia công CNC: lập trình, vận hành máy phay, máy tiện CNC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '5', '1', '0', '3', '8', '2025-06-19 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('16', 'Gia công truyền thống', 'gia-cong-truyen-thong-5', 'Tiện, phay, bào, mài và các phương pháp gia công cơ khí truyền thống', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '5', '2', '0', '2', '7', '2025-06-21 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('17', 'In 3D & Additive Manufacturing', 'in-3d-additive-manufacturing-5', 'Công nghệ in 3D, SLA, SLS, FDM và ứng dụng trong sản xuất', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '5', '3', '0', '2', '8', '2025-06-21 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('18', 'Đồ gá & Fixture', 'do-ga-fixture-5', 'Thiết kế và chế tạo đồ gá, fixture, jig cho gia công', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '5', '4', '0', '2', '7', '2025-06-24 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('19', 'Mastercam', 'mastercam-6', 'Lập trình CNC với Mastercam: 2D, 3D, 4-5 axis, post processor', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '6', '1', '0', '4', '14', '2025-06-25 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('20', 'G-Code Programming', 'g-code-programming-6', 'Lập trình G-code thủ công, macro, custom cycles', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '6', '2', '0', '2', '6', '2025-06-19 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('21', 'CNC Setup & Operation', 'cnc-setup-operation-6', 'Setup máy CNC, work holding, tool management', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '6', '3', '0', '3', '7', '2025-06-22 14:03:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('22', 'Thảo luận chung - Gia công truyền thống', 'thao-luan-chung-gia-cong-truyen-thong-7', 'Thảo luận chung về Gia công truyền thống', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '7', '1', '0', '2', '7', '2025-06-23 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('23', 'Hỏi đáp - Gia công truyền thống', 'hoi-dap-gia-cong-truyen-thong-7', 'Hỏi đáp và giải đáp thắc mắc về Gia công truyền thống', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '7', '2', '0', '2', '6', '2025-06-18 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('24', 'Kinh nghiệm - Gia công truyền thống', 'kinh-nghiem-gia-cong-truyen-thong-7', 'Chia sẻ kinh nghiệm và best practices về Gia công truyền thống', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '7', '3', '0', '2', '7', '2025-06-24 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('25', 'Thảo luận chung - In 3D & Additive Manufacturing', 'thao-luan-chung-in-3d-additive-manufacturing-8', 'Thảo luận chung về In 3D & Additive Manufacturing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '8', '1', '0', '2', '4', '2025-06-19 13:53:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('26', 'Hỏi đáp - In 3D & Additive Manufacturing', 'hoi-dap-in-3d-additive-manufacturing-8', 'Hỏi đáp và giải đáp thắc mắc về In 3D & Additive Manufacturing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '8', '2', '0', '2', '6', '2025-06-23 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('27', 'Kinh nghiệm - In 3D & Additive Manufacturing', 'kinh-nghiem-in-3d-additive-manufacturing-8', 'Chia sẻ kinh nghiệm và best practices về In 3D & Additive Manufacturing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '8', '3', '0', '2', '9', '2025-06-24 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('28', 'Kim loại & Hợp kim', 'kim-loai-hop-kim-9', 'Thép, nhôm, đồng, titan và các hợp kim công nghiệp', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '9', '1', '0', '2', '4', '2025-06-19 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('29', 'Polymer & Composite', 'polymer-composite-9', 'Nhựa kỹ thuật, composite, vật liệu tổng hợp', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '9', '2', '0', '2', '5', '2025-06-21 23:04:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('30', 'Xử lý nhiệt', 'xu-ly-nhiet-9', 'Nhiệt luyện, tôi, ram, ủ và các phương pháp xử lý nhiệt', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '9', '3', '0', '2', '9', '2025-06-23 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('31', 'Vật liệu Smart', 'vat-lieu-smart-9', 'Vật liệu thông minh, hợp kim nhớ hình, vật liệu nano', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '9', '4', '0', '2', '4', '2025-06-20 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('32', 'Kim loại & Hợp kim', 'kim-loai-hop-kim-10', 'Thép, nhôm, đồng, titan và các hợp kim công nghiệp', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '10', '1', '0', '2', '5', '2025-06-24 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('33', 'Polymer & Composite', 'polymer-composite-10', 'Nhựa kỹ thuật, composite, vật liệu tổng hợp', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '10', '2', '0', '2', '6', '2025-06-24 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('34', 'Xử lý nhiệt', 'xu-ly-nhiet-10', 'Nhiệt luyện, tôi, ram, ủ và các phương pháp xử lý nhiệt', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '10', '3', '0', '2', '8', '2025-06-22 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('35', 'Vật liệu Smart', 'vat-lieu-smart-10', 'Vật liệu thông minh, hợp kim nhớ hình, vật liệu nano', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '10', '4', '0', '2', '4', '2025-06-24 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('36', 'Thảo luận chung - Polymer & Composite', 'thao-luan-chung-polymer-composite-11', 'Thảo luận chung về Polymer & Composite', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '11', '1', '0', '2', '8', '2025-06-26 16:30:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('37', 'Hỏi đáp - Polymer & Composite', 'hoi-dap-polymer-composite-11', 'Hỏi đáp và giải đáp thắc mắc về Polymer & Composite', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '11', '2', '0', '2', '6', '2025-06-25 08:43:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('38', 'Kinh nghiệm - Polymer & Composite', 'kinh-nghiem-polymer-composite-11', 'Chia sẻ kinh nghiệm và best practices về Polymer & Composite', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '11', '3', '0', '2', '7', '2025-06-23 17:57:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('39', 'Kim loại & Hợp kim', 'kim-loai-hop-kim-12', 'Thép, nhôm, đồng, titan và các hợp kim công nghiệp', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '12', '1', '0', '2', '5', '2025-06-26 01:55:52', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('40', 'Polymer & Composite', 'polymer-composite-12', 'Nhựa kỹ thuật, composite, vật liệu tổng hợp', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '12', '2', '0', '2', '6', '2025-06-22 17:57:53', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('41', 'Xử lý nhiệt', 'xu-ly-nhiet-12', 'Nhiệt luyện, tôi, ram, ủ và các phương pháp xử lý nhiệt', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '12', '3', '0', '2', '7', '2025-06-23 08:25:53', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('42', 'Vật liệu Smart', 'vat-lieu-smart-12', 'Vật liệu thông minh, hợp kim nhớ hình, vật liệu nano', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '12', '4', '0', '2', '5', '2025-06-14 17:57:53', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('43', 'PLC & HMI', 'plc-hmi-13', 'Lập trình PLC: Siemens, Allen-Bradley, Mitsubishi, Schneider', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '13', '1', '0', '2', '6', '2025-06-19 17:57:53', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('44', 'Robot công nghiệp', 'robot-cong-nghiep-13', 'Robot ABB, KUKA, Fanuc, Yaskawa và ứng dụng trong sản xuất', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '13', '2', '0', '2', '4', '2025-06-13 17:57:53', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('45', 'Sensors & Actuators', 'sensors-actuators-13', 'Cảm biến, động cơ servo, stepper, van điện từ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '13', '3', '0', '2', '7', '2025-06-25 17:57:53', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('46', 'Industry 4.0 & IoT', 'industry-40-iot-13', 'Công nghiệp 4.0, Internet of Things, Smart Factory', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '13', '4', '0', '2', '7', '2025-06-23 17:57:53', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('47', 'Siemens PLC', 'siemens-plc-14', 'Siemens S7-1200, S7-1500, TIA Portal, WinCC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '14', '1', '0', '2', '4', '2025-06-08 17:57:53', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('48', 'Allen-Bradley', 'allen-bradley-14', 'ControlLogix, CompactLogix, RSLogix, FactoryTalk', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '14', '2', '0', '2', '6', '2025-06-22 17:57:53', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('49', 'Mitsubishi PLC', 'mitsubishi-plc-14', 'FX series, Q series, GX Works, GOT HMI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '14', '3', '0', '2', '7', '2025-06-16 06:07:53', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('50', 'ABB Robotics', 'abb-robotics-15', 'ABB robot programming, RobotStudio, RAPID language', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '15', '1', '0', '2', '3', '2025-06-13 17:57:53', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('51', 'KUKA Robotics', 'kuka-robotics-15', 'KUKA robot, KRL programming, WorkVisual', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '15', '2', '0', '2', '6', '2025-06-26 16:37:53', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('52', 'Fanuc Robotics', 'fanuc-robotics-15', 'Fanuc robot, KAREL, Roboguide simulation', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '15', '3', '0', '2', '5', '2025-06-24 17:57:53', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('53', 'Thảo luận chung - Sensors & Actuators', 'thao-luan-chung-sensors-actuators-16', 'Thảo luận chung về Sensors & Actuators', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '16', '1', '0', '2', '9', '2025-06-22 17:57:53', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('54', 'Hỏi đáp - Sensors & Actuators', 'hoi-dap-sensors-actuators-16', 'Hỏi đáp và giải đáp thắc mắc về Sensors & Actuators', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '16', '2', '0', '2', '5', '2025-06-22 17:57:53', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);
INSERT INTO `forums` VALUES ('55', 'Kinh nghiệm - Sensors & Actuators', 'kinh-nghiem-sensors-actuators-16', 'Chia sẻ kinh nghiệm và best practices về Sensors & Actuators', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'global', '16', '3', '0', '2', '5', '2025-06-19 17:57:53', NULL, NULL, '0', NULL, '2025-06-25 17:57:34', '2025-06-25 18:19:49', NULL, NULL);

-- Structure for table `marketplace_products`
DROP TABLE IF EXISTS `marketplace_products`;
CREATE TABLE `marketplace_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` uuid NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `short_description` text DEFAULT NULL,
  `sku` varchar(191) NOT NULL,
  `seller_id` bigint(20) unsigned NOT NULL,
  `product_category_id` bigint(20) unsigned DEFAULT NULL,
  `product_type` enum('digital','new_product','used_product') NOT NULL DEFAULT 'new_product',
  `seller_type` enum('supplier','manufacturer','brand') NOT NULL DEFAULT 'supplier',
  `industry_category` varchar(191) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `sale_price` decimal(12,2) DEFAULT NULL,
  `is_on_sale` tinyint(1) NOT NULL DEFAULT 0,
  `sale_starts_at` timestamp NULL DEFAULT NULL,
  `sale_ends_at` timestamp NULL DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `manage_stock` tinyint(1) NOT NULL DEFAULT 1,
  `in_stock` tinyint(1) NOT NULL DEFAULT 1,
  `low_stock_threshold` int(11) NOT NULL DEFAULT 5,
  `technical_specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`technical_specs`)),
  `mechanical_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`mechanical_properties`)),
  `material` varchar(191) DEFAULT NULL,
  `manufacturing_process` varchar(191) DEFAULT NULL,
  `standards_compliance` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`standards_compliance`)),
  `file_formats` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`file_formats`)),
  `software_compatibility` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`software_compatibility`)),
  `file_size_mb` decimal(8,2) DEFAULT NULL,
  `download_limit` int(11) DEFAULT NULL,
  `digital_files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`digital_files`)),
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `featured_image` varchar(191) DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `meta_title` varchar(191) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `status` enum('draft','pending','approved','rejected','suspended') NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `like_count` int(11) NOT NULL DEFAULT 0,
  `download_count` int(11) NOT NULL DEFAULT 0,
  `purchase_count` int(11) NOT NULL DEFAULT 0,
  `rating_average` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `featured_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marketplace_products_uuid_unique` (`uuid`),
  UNIQUE KEY `marketplace_products_slug_unique` (`slug`),
  UNIQUE KEY `marketplace_products_sku_unique` (`sku`),
  KEY `marketplace_products_approved_by_foreign` (`approved_by`),
  KEY `marketplace_products_seller_id_status_index` (`seller_id`,`status`),
  KEY `marketplace_products_product_category_id_status_index` (`product_category_id`,`status`),
  KEY `marketplace_products_product_type_seller_type_index` (`product_type`,`seller_type`),
  KEY `marketplace_products_is_featured_is_active_index` (`is_featured`,`is_active`),
  KEY `marketplace_products_created_at_status_index` (`created_at`,`status`),
  KEY `marketplace_products_slug_index` (`slug`),
  KEY `marketplace_products_sku_index` (`sku`),
  CONSTRAINT `marketplace_products_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `marketplace_products_product_category_id_foreign` FOREIGN KEY (`product_category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `marketplace_products_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data for table `marketplace_products`
INSERT INTO `marketplace_products` VALUES ('1', '88897055-45b0-4741-b9a8-ed9d5de471e2', 'Precision Ball Bearing Set - Hydraulic Systems Vietnam Supply Co.', 'precision-ball-bearing-set-hydraulic-systems-vietnam-supply-co-5787', 'Professional grade Precision Ball Bearing Set designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality precision ball bearing set for professional mechanical engineering applications.', 'PHY54984', '5', '19', 'new_product', 'supplier', NULL, '106.00', '86.92', '1', NULL, NULL, '32', '1', '1', '5', '\"{\\\"Material\\\":\\\"Stainless Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"59 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Steel', 'Casting', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/Mechanical-Engineering.jpg', NULL, 'Precision Ball Bearing Set - Professional Quality', 'Buy high-quality precision ball bearing set from verified sellers. Fast shipping and professional support.', '\"[\\\"precision ball bearing set\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '348', '41', '0', '100', '4.30', '28', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 10:38:32', NULL);
INSERT INTO `marketplace_products` VALUES ('2', '255ca040-1e58-4f65-8f8d-50e2a973522d', 'Precision Ball Bearing Set - Mitsubishi Electric Vietnam Brand', 'precision-ball-bearing-set-mitsubishi-electric-vietnam-brand-5831', 'Professional grade Precision Ball Bearing Set designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality precision ball bearing set for professional mechanical engineering applications.', 'PHY79927', '12', '8', 'new_product', 'brand', NULL, '121.00', NULL, '0', NULL, NULL, '37', '1', '1', '5', '\"{\\\"Material\\\":\\\"Aluminum\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"55 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Aluminum', 'Welding', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/DesignEngineer.jpg', NULL, 'Precision Ball Bearing Set - Professional Quality', 'Buy high-quality precision ball bearing set from verified sellers. Fast shipping and professional support.', '\"[\\\"precision ball bearing set\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '1', '1', '2025-06-25 18:19:39', NULL, NULL, '714', '26', '0', '5', '5.00', '47', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 10:54:41', NULL);
INSERT INTO `marketplace_products` VALUES ('3', '1dc94297-2fe8-46f7-867d-bec9930573ac', 'Precision Ball Bearing Set - Brand Test User Business', 'precision-ball-bearing-set-brand-test-user-business-4538', 'Professional grade Precision Ball Bearing Set designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality precision ball bearing set for professional mechanical engineering applications.', 'PHY26990', '15', '13', 'new_product', 'brand', NULL, '91.00', NULL, '0', NULL, NULL, '64', '1', '1', '5', '\"{\\\"Material\\\":\\\"Stainless Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"59 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Titanium', 'CNC Machining', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/Mechanical-Engineering-MS-Professionals-Hero-1600x900_0.jpg', NULL, 'Precision Ball Bearing Set - Professional Quality', 'Buy high-quality precision ball bearing set from verified sellers. Fast shipping and professional support.', '\"[\\\"precision ball bearing set\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '652', '50', '0', '94', '3.90', '48', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('4', 'e3463ca3-8b13-49ae-8526-b69ff8ca682b', 'Industrial Gear Assembly - Hanoi Industrial Manufacturing Manufacturing', 'industrial-gear-assembly-hanoi-industrial-manufacturing-manufacturing-7203', 'Professional grade Industrial Gear Assembly designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality industrial gear assembly for professional mechanical engineering applications.', 'PHY24311', '8', '3', 'new_product', 'manufacturer', NULL, '222.00', '173.16', '1', NULL, NULL, '8', '1', '1', '5', '\"{\\\"Material\\\":\\\"Aluminum\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"50 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Stainless Steel', 'Forging', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/PFxP5HX8oNsLtufFRMumpc.jpg', NULL, 'Industrial Gear Assembly - Professional Quality', 'Buy high-quality industrial gear assembly from verified sellers. Fast shipping and professional support.', '\"[\\\"industrial gear assembly\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '739', '38', '0', '53', '3.90', '60', '0', '2025-06-25 18:19:39', '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('5', '84cc754e-dac6-4ef8-a54b-12b355777d0d', 'Industrial Gear Assembly - Mitsubishi Electric Vietnam Brand', 'industrial-gear-assembly-mitsubishi-electric-vietnam-brand-8956', 'Professional grade Industrial Gear Assembly designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality industrial gear assembly for professional mechanical engineering applications.', 'PHY65179', '12', '12', 'new_product', 'brand', NULL, '231.00', NULL, '0', NULL, NULL, '80', '1', '1', '5', '\"{\\\"Material\\\":\\\"Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"48 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Titanium', 'Welding', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/depositphotos_73832701-Mechanical-design-office-.jpg', NULL, 'Industrial Gear Assembly - Professional Quality', 'Buy high-quality industrial gear assembly from verified sellers. Fast shipping and professional support.', '\"[\\\"industrial gear assembly\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '826', '40', '0', '83', '3.70', '42', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('6', '768a13b1-35f0-4d56-8a6a-6a9caa462e76', 'Industrial Gear Assembly - Mekong Delta Engineering Works Manufacturing', 'industrial-gear-assembly-mekong-delta-engineering-works-manufacturing-8671', 'Professional grade Industrial Gear Assembly designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality industrial gear assembly for professional mechanical engineering applications.', 'PHY50443', '24', '6', 'new_product', 'manufacturer', NULL, '136.00', NULL, '0', NULL, NULL, '90', '1', '1', '5', '\"{\\\"Material\\\":\\\"Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"51 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Stainless Steel', 'Welding', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/engineering_mechanical_3042380_cropped.jpg', NULL, 'Industrial Gear Assembly - Professional Quality', 'Buy high-quality industrial gear assembly from verified sellers. Fast shipping and professional support.', '\"[\\\"industrial gear assembly\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '557', '40', '0', '30', '5.00', '52', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('7', '5383bf41-8a51-465d-9b59-ac26120b4ef3', 'Hydraulic Cylinder Kit - Vật Liệu Cơ Khí Hà Nội Supply Co.', 'hydraulic-cylinder-kit-vat-lieu-co-khi-ha-noi-supply-co-4353', 'Professional grade Hydraulic Cylinder Kit designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality hydraulic cylinder kit for professional mechanical engineering applications.', 'PHY93670', '2', '13', 'new_product', 'supplier', NULL, '736.00', NULL, '0', NULL, NULL, '33', '1', '1', '5', '\"{\\\"Material\\\":\\\"Titanium\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"45 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Titanium', 'Welding', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/mechanical-design-vs-mechanical-engineer2.jpg.webp', NULL, 'Hydraulic Cylinder Kit - Professional Quality', 'Buy high-quality hydraulic cylinder kit from verified sellers. Fast shipping and professional support.', '\"[\\\"hydraulic cylinder kit\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '656', '23', '0', '58', '4.70', '98', '0', '2025-06-25 18:19:39', '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('8', '144bc0b0-ea25-4b5c-9421-42a7298488fd', 'Hydraulic Cylinder Kit - Supplier Test User Business', 'hydraulic-cylinder-kit-supplier-test-user-business-5591', 'Professional grade Hydraulic Cylinder Kit designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality hydraulic cylinder kit for professional mechanical engineering applications.', 'PHY15624', '13', '2', 'new_product', 'supplier', NULL, '281.00', '219.18', '1', NULL, NULL, '13', '1', '1', '5', '\"{\\\"Material\\\":\\\"Aluminum\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"56 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Steel', 'Forging', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/mj_11208_2.jpg', NULL, 'Hydraulic Cylinder Kit - Professional Quality', 'Buy high-quality hydraulic cylinder kit from verified sellers. Fast shipping and professional support.', '\"[\\\"hydraulic cylinder kit\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '1', '1', '2025-06-25 18:19:39', NULL, NULL, '444', '27', '0', '44', '4.10', '99', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('9', 'd7c66375-8628-4e99-8f97-de0cae69bbbb', 'Hydraulic Cylinder Kit - Industrial Tools Vietnam Supply Co.', 'hydraulic-cylinder-kit-industrial-tools-vietnam-supply-co-5074', 'Professional grade Hydraulic Cylinder Kit designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality hydraulic cylinder kit for professional mechanical engineering applications.', 'PHY30029', '18', '11', 'new_product', 'supplier', NULL, '364.00', '291.20', '1', NULL, NULL, '70', '1', '1', '5', '\"{\\\"Material\\\":\\\"Titanium\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"50 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Steel', 'CNC Machining', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/mj_11226_4.jpg', NULL, 'Hydraulic Cylinder Kit - Professional Quality', 'Buy high-quality hydraulic cylinder kit from verified sellers. Fast shipping and professional support.', '\"[\\\"hydraulic cylinder kit\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '197', '3', '0', '91', '4.50', '88', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('10', '722403c2-a495-4c1f-87cb-915ae2146c27', 'Hydraulic Cylinder Kit - Hanoi Industrial Manufacturing Manufacturing', 'hydraulic-cylinder-kit-hanoi-industrial-manufacturing-manufacturing-7064', 'Professional grade Hydraulic Cylinder Kit designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality hydraulic cylinder kit for professional mechanical engineering applications.', 'PHY54867', '23', '17', 'new_product', 'manufacturer', NULL, '550.00', NULL, '0', NULL, NULL, '60', '1', '1', '5', '\"{\\\"Material\\\":\\\"Stainless Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"44 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Stainless Steel', 'Welding', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/1567174641278.jpg', NULL, 'Hydraulic Cylinder Kit - Professional Quality', 'Buy high-quality hydraulic cylinder kit from verified sellers. Fast shipping and professional support.', '\"[\\\"hydraulic cylinder kit\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '180', '44', '0', '90', '4.20', '56', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('11', '2576c0f6-6361-49a5-ab00-3778de3ff817', 'Pneumatic Valve System - Industrial Tools Vietnam Supply Co.', 'pneumatic-valve-system-industrial-tools-vietnam-supply-co-2390', 'Professional grade Pneumatic Valve System designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality pneumatic valve system for professional mechanical engineering applications.', 'PHY86312', '3', '3', 'new_product', 'supplier', NULL, '477.00', NULL, '0', NULL, NULL, '58', '1', '1', '5', '\"{\\\"Material\\\":\\\"Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"50 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Titanium', 'Forging', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/threads/Mechanical-Engineer-1-1024x536.webp', NULL, 'Pneumatic Valve System - Professional Quality', 'Buy high-quality pneumatic valve system from verified sellers. Fast shipping and professional support.', '\"[\\\"pneumatic valve system\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '70', '10', '0', '78', '3.60', '58', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('12', '4a31a3ef-e200-4187-b68d-bf0af746a9b4', 'Pneumatic Valve System - Nhà máy Cơ khí Đông Á Manufacturing', 'pneumatic-valve-system-nha-may-co-khi-dong-a-manufacturing-5048', 'Professional grade Pneumatic Valve System designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality pneumatic valve system for professional mechanical engineering applications.', 'PHY33439', '6', '15', 'new_product', 'manufacturer', NULL, '589.00', NULL, '0', NULL, NULL, '82', '1', '1', '5', '\"{\\\"Material\\\":\\\"Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"50 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Steel', 'CNC Machining', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/threads/Mechanical-Engineering-thumbnail.jpg', NULL, 'Pneumatic Valve System - Professional Quality', 'Buy high-quality pneumatic valve system from verified sellers. Fast shipping and professional support.', '\"[\\\"pneumatic valve system\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '939', '49', '0', '3', '3.50', '100', '0', '2025-06-25 18:19:39', '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('13', '1ef7737f-b2ee-4a2c-9f56-45591f0cac30', 'Pneumatic Valve System - Vật Liệu Cơ Khí Hà Nội Supply Co.', 'pneumatic-valve-system-vat-lieu-co-khi-ha-noi-supply-co-7306', 'Professional grade Pneumatic Valve System designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality pneumatic valve system for professional mechanical engineering applications.', 'PHY84183', '17', '19', 'new_product', 'supplier', NULL, '203.00', NULL, '0', NULL, NULL, '78', '1', '1', '5', '\"{\\\"Material\\\":\\\"Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"41 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Steel', 'Forging', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/threads/Mechanical_components.png', NULL, 'Pneumatic Valve System - Professional Quality', 'Buy high-quality pneumatic valve system from verified sellers. Fast shipping and professional support.', '\"[\\\"pneumatic valve system\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '670', '24', '0', '12', '3.50', '80', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('14', '6f80a8d1-54c2-44b8-bcb8-187b46930c11', 'Motor Coupling Set - Vật Liệu Cơ Khí Hà Nội Supply Co.', 'motor-coupling-set-vat-lieu-co-khi-ha-noi-supply-co-4120', 'Professional grade Motor Coupling Set designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality motor coupling set for professional mechanical engineering applications.', 'PHY43525', '2', '19', 'new_product', 'supplier', NULL, '200.00', NULL, '0', NULL, NULL, '64', '1', '1', '5', '\"{\\\"Material\\\":\\\"Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"42 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Aluminum', 'CNC Machining', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/threads/Professional Engineer.webp', NULL, 'Motor Coupling Set - Professional Quality', 'Buy high-quality motor coupling set from verified sellers. Fast shipping and professional support.', '\"[\\\"motor coupling set\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '675', '50', '0', '95', '4.30', '67', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('15', '80d73127-7ad2-4b34-a83c-dbbca5908ff5', 'Motor Coupling Set - Vietnam Precision Manufacturing Manufacturing', 'motor-coupling-set-vietnam-precision-manufacturing-manufacturing-8933', 'Professional grade Motor Coupling Set designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality motor coupling set for professional mechanical engineering applications.', 'PHY78619', '7', '1', 'new_product', 'manufacturer', NULL, '208.00', '156.00', '1', NULL, NULL, '46', '1', '1', '5', '\"{\\\"Material\\\":\\\"Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"55 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Aluminum', 'Casting', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/threads/compressed_2151589656.jpg', NULL, 'Motor Coupling Set - Professional Quality', 'Buy high-quality motor coupling set from verified sellers. Fast shipping and professional support.', '\"[\\\"motor coupling set\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '905', '15', '0', '65', '5.00', '21', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('16', 'fceb1ab7-0b5c-4f27-b91c-7aa848b43dd9', 'Motor Coupling Set - Supplier Test User Business', 'motor-coupling-set-supplier-test-user-business-3911', 'Professional grade Motor Coupling Set designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality motor coupling set for professional mechanical engineering applications.', 'PHY82097', '13', '11', 'new_product', 'supplier', NULL, '244.00', NULL, '0', NULL, NULL, '36', '1', '1', '5', '\"{\\\"Material\\\":\\\"Aluminum\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"59 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Steel', 'Casting', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/threads/images.jpg', NULL, 'Motor Coupling Set - Professional Quality', 'Buy high-quality motor coupling set from verified sellers. Fast shipping and professional support.', '\"[\\\"motor coupling set\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '48', '22', '0', '42', '3.50', '56', '0', '2025-06-25 18:19:39', '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('17', 'd6fe0fec-0471-43f8-a2da-742d0b57fb4a', 'CAD Model - Gear Assembly - Bearing & Fastener Supply Co. Supply Co.', 'cad-model-gear-assembly-bearing-fastener-supply-co-supply-co-7898', 'High-quality digital CAD Model - Gear Assembly created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality cad model - gear assembly for professional mechanical engineering applications.', 'DIG83705', '4', '6', 'digital', 'supplier', NULL, '30.00', NULL, '0', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"PDF\\\",\\\"CAD Software\\\":\\\"Fusion 360\\\",\\\"File Size\\\":\\\"7 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"14 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '33.00', '5', NULL, NULL, 'images/threads/male-asian-engineer-professional-having-discussion-standing-by-machine-factory-two-asian-coworker-brainstorm-explaining-solves-process-curcuit-mother-board-machine.webp', NULL, 'CAD Model - Gear Assembly - Professional Quality', 'Buy high-quality cad model - gear assembly from verified sellers. Fast shipping and professional support.', '\"[\\\"cad model - gear assembly\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '712', '11', '63', '18', '4.90', '21', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('18', 'd40ce1f6-d296-493b-b30c-68ce97096ecd', 'CAD Model - Gear Assembly - Brand Test User Business', 'cad-model-gear-assembly-brand-test-user-business-3269', 'High-quality digital CAD Model - Gear Assembly created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality cad model - gear assembly for professional mechanical engineering applications.', 'DIG38130', '15', '4', 'digital', 'brand', NULL, '68.00', NULL, '0', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"STEP\\\",\\\"CAD Software\\\":\\\"AutoCAD\\\",\\\"File Size\\\":\\\"19 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"9 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '17.00', '5', NULL, NULL, 'images/threads/male-worker-factory.webp', NULL, 'CAD Model - Gear Assembly - Professional Quality', 'Buy high-quality cad model - gear assembly from verified sellers. Fast shipping and professional support.', '\"[\\\"cad model - gear assembly\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '202', '32', '73', '60', '3.90', '10', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('19', 'c2d923ac-01b0-43ad-b4b5-be7879014d10', 'CAD Model - Gear Assembly - Bearing & Fastener Supply Co. Supply Co.', 'cad-model-gear-assembly-bearing-fastener-supply-co-supply-co-8376', 'High-quality digital CAD Model - Gear Assembly created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality cad model - gear assembly for professional mechanical engineering applications.', 'DIG36841', '19', '11', 'digital', 'supplier', NULL, '72.00', NULL, '0', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"DWG\\\",\\\"CAD Software\\\":\\\"AutoCAD\\\",\\\"File Size\\\":\\\"12 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"16 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '46.00', '5', NULL, NULL, 'images/threads/man-woman-engineering-computer-mechanical.jpg', NULL, 'CAD Model - Gear Assembly - Professional Quality', 'Buy high-quality cad model - gear assembly from verified sellers. Fast shipping and professional support.', '\"[\\\"cad model - gear assembly\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '261', '24', '101', '94', '3.50', '79', '0', '2025-06-25 18:19:39', '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('20', 'e432ec71-d4de-4443-b150-931655ff194d', 'CAD Model - Gear Assembly - Mitsubishi Electric Vietnam Brand', 'cad-model-gear-assembly-mitsubishi-electric-vietnam-brand-3551', 'High-quality digital CAD Model - Gear Assembly created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality cad model - gear assembly for professional mechanical engineering applications.', 'DIG52778', '27', '10', 'digital', 'brand', NULL, '87.00', NULL, '0', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"PDF\\\",\\\"CAD Software\\\":\\\"Fusion 360\\\",\\\"File Size\\\":\\\"36 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"15 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '48.00', '5', NULL, NULL, 'images/threads/mechanical-engineering-la-gi-7.webp', NULL, 'CAD Model - Gear Assembly - Professional Quality', 'Buy high-quality cad model - gear assembly from verified sellers. Fast shipping and professional support.', '\"[\\\"cad model - gear assembly\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '335', '28', '24', '29', '4.10', '85', '0', '2025-06-25 18:19:39', '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('21', 'a00e1ed6-ca93-4e74-a22f-4b51e2ef32ad', 'Technical Drawing - Bearing Housing - Industrial Tools Vietnam Supply Co.', 'technical-drawing-bearing-housing-industrial-tools-vietnam-supply-co-9419', 'High-quality digital Technical Drawing - Bearing Housing created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality technical drawing - bearing housing for professional mechanical engineering applications.', 'DIG65594', '18', '4', 'digital', 'supplier', NULL, '76.00', NULL, '0', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"IGES\\\",\\\"CAD Software\\\":\\\"Fusion 360\\\",\\\"File Size\\\":\\\"31 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"13 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '1.00', '5', NULL, NULL, 'images/threads/mechanical-mini-projects-cover-pic.webp', NULL, 'Technical Drawing - Bearing Housing - Professional Quality', 'Buy high-quality technical drawing - bearing housing from verified sellers. Fast shipping and professional support.', '\"[\\\"technical drawing - bearing housing\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '59', '6', '116', '41', '4.60', '81', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('22', '0fe65b9b-c358-4f07-9635-d741c9d0c72a', 'Technical Drawing - Bearing Housing - Vietnam Precision Manufacturing Manufacturing', 'technical-drawing-bearing-housing-vietnam-precision-manufacturing-manufacturing-6122', 'High-quality digital Technical Drawing - Bearing Housing created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality technical drawing - bearing housing for professional mechanical engineering applications.', 'DIG67676', '22', '14', 'digital', 'manufacturer', NULL, '50.00', NULL, '0', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"PDF\\\",\\\"CAD Software\\\":\\\"Fusion 360\\\",\\\"File Size\\\":\\\"16 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"17 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '7.00', '5', NULL, NULL, 'images/threads/mechanical-update_0.jpg', NULL, 'Technical Drawing - Bearing Housing - Professional Quality', 'Buy high-quality technical drawing - bearing housing from verified sellers. Fast shipping and professional support.', '\"[\\\"technical drawing - bearing housing\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '1', '1', '2025-06-25 18:19:39', NULL, NULL, '331', '47', '22', '3', '4.50', '62', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 12:55:24', NULL);
INSERT INTO `marketplace_products` VALUES ('23', '058f60dd-891d-4f3d-b290-cc94a94c8834', 'Technical Drawing - Bearing Housing - MechaTech Solutions Vietnam Brand', 'technical-drawing-bearing-housing-mechatech-solutions-vietnam-brand-3600', 'High-quality digital Technical Drawing - Bearing Housing created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality technical drawing - bearing housing for professional mechanical engineering applications.', 'DIG75852', '25', '9', 'digital', 'brand', NULL, '54.00', NULL, '0', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"PDF\\\",\\\"CAD Software\\\":\\\"SolidWorks\\\",\\\"File Size\\\":\\\"25 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"19 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '41.00', '5', NULL, NULL, 'images/threads/mj_11351_4.jpg', NULL, 'Technical Drawing - Bearing Housing - Professional Quality', 'Buy high-quality technical drawing - bearing housing from verified sellers. Fast shipping and professional support.', '\"[\\\"technical drawing - bearing housing\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '1', '1', '2025-06-25 18:19:39', NULL, NULL, '442', '31', '132', '57', '5.00', '46', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('24', 'cb5e9655-fcfc-4bd4-b1a6-b13b804855ba', 'SolidWorks Model - Engine Block - Hanoi Industrial Manufacturing Manufacturing', 'solidworks-model-engine-block-hanoi-industrial-manufacturing-manufacturing-5442', 'High-quality digital SolidWorks Model - Engine Block created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality solidworks model - engine block for professional mechanical engineering applications.', 'DIG28247', '8', '8', 'digital', 'manufacturer', NULL, '55.00', NULL, '0', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"DWG\\\",\\\"CAD Software\\\":\\\"Fusion 360\\\",\\\"File Size\\\":\\\"6 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"19 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '24.00', '5', NULL, NULL, 'images/threads/program-mech-eng.jpg', NULL, 'SolidWorks Model - Engine Block - Professional Quality', 'Buy high-quality solidworks model - engine block from verified sellers. Fast shipping and professional support.', '\"[\\\"solidworks model - engine block\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '345', '24', '193', '21', '3.60', '56', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('25', 'cd8c0e26-9a1a-492c-98e3-a3e58fd21877', 'SolidWorks Model - Engine Block - Công ty TNHH Thép Việt Nam Supply Co.', 'solidworks-model-engine-block-cong-ty-tnhh-thep-viet-nam-supply-co-4492', 'High-quality digital SolidWorks Model - Engine Block created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality solidworks model - engine block for professional mechanical engineering applications.', 'DIG60397', '16', '5', 'digital', 'supplier', NULL, '140.00', '113.40', '1', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"PDF\\\",\\\"CAD Software\\\":\\\"Fusion 360\\\",\\\"File Size\\\":\\\"5 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"19 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '48.00', '5', NULL, NULL, 'images/threads/success-story-schuetz-industrie-anlagenmechanikerin-2128x1330-c.jpg.webp', NULL, 'SolidWorks Model - Engine Block - Professional Quality', 'Buy high-quality solidworks model - engine block from verified sellers. Fast shipping and professional support.', '\"[\\\"solidworks model - engine block\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '88', '9', '45', '58', '3.60', '81', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('26', 'b3b0c223-8265-4650-a533-8d28fe50c0a6', 'SolidWorks Model - Engine Block - Bearing & Fastener Supply Co. Supply Co.', 'solidworks-model-engine-block-bearing-fastener-supply-co-supply-co-3825', 'High-quality digital SolidWorks Model - Engine Block created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality solidworks model - engine block for professional mechanical engineering applications.', 'DIG74511', '19', '6', 'digital', 'supplier', NULL, '183.00', NULL, '0', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"STEP\\\",\\\"CAD Software\\\":\\\"AutoCAD\\\",\\\"File Size\\\":\\\"27 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"9 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '20.00', '5', NULL, NULL, 'images/threads/ImageForArticle_20492_16236782958233468.webp', NULL, 'SolidWorks Model - Engine Block - Professional Quality', 'Buy high-quality solidworks model - engine block from verified sellers. Fast shipping and professional support.', '\"[\\\"solidworks model - engine block\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '979', '15', '103', '6', '3.80', '7', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('27', 'fec75f22-19fb-448d-8074-a7baa14a732d', 'AutoCAD Drawing - Mechanical Frame - Industrial Tools Vietnam Supply Co.', 'autocad-drawing-mechanical-frame-industrial-tools-vietnam-supply-co-7456', 'High-quality digital AutoCAD Drawing - Mechanical Frame created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality autocad drawing - mechanical frame for professional mechanical engineering applications.', 'DIG55904', '3', '8', 'digital', 'supplier', NULL, '71.00', NULL, '0', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"STEP\\\",\\\"CAD Software\\\":\\\"Fusion 360\\\",\\\"File Size\\\":\\\"22 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"6 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '21.00', '5', NULL, NULL, 'images/demo/showcase-1.jpg', NULL, 'AutoCAD Drawing - Mechanical Frame - Professional Quality', 'Buy high-quality autocad drawing - mechanical frame from verified sellers. Fast shipping and professional support.', '\"[\\\"autocad drawing - mechanical frame\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '542', '31', '16', '1', '4.10', '99', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('28', '20a89dcf-39b7-4365-bdeb-74163b2f5f6b', 'AutoCAD Drawing - Mechanical Frame - Brand Test User Business', 'autocad-drawing-mechanical-frame-brand-test-user-business-9943', 'High-quality digital AutoCAD Drawing - Mechanical Frame created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality autocad drawing - mechanical frame for professional mechanical engineering applications.', 'DIG89933', '15', '6', 'digital', 'brand', NULL, '33.00', NULL, '0', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"IGES\\\",\\\"CAD Software\\\":\\\"Fusion 360\\\",\\\"File Size\\\":\\\"12 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"11 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '41.00', '5', NULL, NULL, 'images/demo/showcase-2.jpg', NULL, 'AutoCAD Drawing - Mechanical Frame - Professional Quality', 'Buy high-quality autocad drawing - mechanical frame from verified sellers. Fast shipping and professional support.', '\"[\\\"autocad drawing - mechanical frame\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '926', '36', '183', '87', '4.90', '99', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('29', 'e421b195-99ba-49d1-b49e-6938ef73745a', 'AutoCAD Drawing - Mechanical Frame - Vật Liệu Cơ Khí Hà Nội Supply Co.', 'autocad-drawing-mechanical-frame-vat-lieu-co-khi-ha-noi-supply-co-2060', 'High-quality digital AutoCAD Drawing - Mechanical Frame created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality autocad drawing - mechanical frame for professional mechanical engineering applications.', 'DIG42373', '17', '16', 'digital', 'supplier', NULL, '89.00', '66.75', '1', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"DWG\\\",\\\"CAD Software\\\":\\\"AutoCAD\\\",\\\"File Size\\\":\\\"25 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"18 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '13.00', '5', NULL, NULL, 'images/demo/showcase-3.jpg', NULL, 'AutoCAD Drawing - Mechanical Frame - Professional Quality', 'Buy high-quality autocad drawing - mechanical frame from verified sellers. Fast shipping and professional support.', '\"[\\\"autocad drawing - mechanical frame\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '809', '32', '168', '56', '3.90', '91', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('30', '4f6d60ab-bbb8-4b53-8036-eb17e88adb75', 'AutoCAD Drawing - Mechanical Frame - MechaTech Solutions Vietnam Brand', 'autocad-drawing-mechanical-frame-mechatech-solutions-vietnam-brand-7842', 'High-quality digital AutoCAD Drawing - Mechanical Frame created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality autocad drawing - mechanical frame for professional mechanical engineering applications.', 'DIG79158', '25', '9', 'digital', 'brand', NULL, '48.00', '40.80', '1', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"IGES\\\",\\\"CAD Software\\\":\\\"AutoCAD\\\",\\\"File Size\\\":\\\"21 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"11 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '21.00', '5', NULL, NULL, 'images/demo/showcase-4.jpg', NULL, 'AutoCAD Drawing - Mechanical Frame - Professional Quality', 'Buy high-quality autocad drawing - mechanical frame from verified sellers. Fast shipping and professional support.', '\"[\\\"autocad drawing - mechanical frame\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '570', '24', '20', '26', '4.00', '68', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('31', 'ef9bebb5-ac50-481d-b44a-3500bf95d869', 'FEA Analysis Report - Stress Testing - Vietnam Precision Manufacturing Manufacturing', 'fea-analysis-report-stress-testing-vietnam-precision-manufacturing-manufacturing-2923', 'High-quality digital FEA Analysis Report - Stress Testing created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality fea analysis report - stress testing for professional mechanical engineering applications.', 'DIG40356', '7', '12', 'digital', 'manufacturer', NULL, '270.00', '213.30', '1', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"DWG\\\",\\\"CAD Software\\\":\\\"Fusion 360\\\",\\\"File Size\\\":\\\"17 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"5 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '28.00', '5', NULL, NULL, 'images/demo/showcase-5.jpg', NULL, 'FEA Analysis Report - Stress Testing - Professional Quality', 'Buy high-quality fea analysis report - stress testing from verified sellers. Fast shipping and professional support.', '\"[\\\"fea analysis report - stress testing\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '455', '30', '175', '44', '3.90', '31', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('32', '40df21b4-5d45-4b2b-9fdc-31dd12883a7a', 'FEA Analysis Report - Stress Testing - Hydraulic Systems Vietnam Supply Co.', 'fea-analysis-report-stress-testing-hydraulic-systems-vietnam-supply-co-8849', 'High-quality digital FEA Analysis Report - Stress Testing created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality fea analysis report - stress testing for professional mechanical engineering applications.', 'DIG46805', '20', '14', 'digital', 'supplier', NULL, '170.00', NULL, '0', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"IGES\\\",\\\"CAD Software\\\":\\\"Fusion 360\\\",\\\"File Size\\\":\\\"42 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"12 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '11.00', '5', NULL, NULL, 'images/demo/gallery-1.jpg', NULL, 'FEA Analysis Report - Stress Testing - Professional Quality', 'Buy high-quality fea analysis report - stress testing from verified sellers. Fast shipping and professional support.', '\"[\\\"fea analysis report - stress testing\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '771', '4', '191', '65', '3.80', '41', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('33', 'a459c0e1-abf5-4ef0-abff-4b2f8cbbacb5', 'FEA Analysis Report - Stress Testing - Hanoi Industrial Manufacturing Manufacturing', 'fea-analysis-report-stress-testing-hanoi-industrial-manufacturing-manufacturing-4392', 'High-quality digital FEA Analysis Report - Stress Testing created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality fea analysis report - stress testing for professional mechanical engineering applications.', 'DIG45952', '23', '10', 'digital', 'manufacturer', NULL, '186.00', NULL, '0', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"DWG\\\",\\\"CAD Software\\\":\\\"SolidWorks\\\",\\\"File Size\\\":\\\"15 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"8 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '11.00', '5', NULL, NULL, 'images/demo/gallery-2.jpg', NULL, 'FEA Analysis Report - Stress Testing - Professional Quality', 'Buy high-quality fea analysis report - stress testing from verified sellers. Fast shipping and professional support.', '\"[\\\"fea analysis report - stress testing\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '1', '1', '2025-06-25 18:19:39', NULL, NULL, '277', '6', '26', '86', '4.90', '17', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('34', '437f8ff0-916b-4093-9f3a-a05c5edec5d3', 'FEA Analysis Report - Stress Testing - Mekong Delta Engineering Works Manufacturing', 'fea-analysis-report-stress-testing-mekong-delta-engineering-works-manufacturing-9857', 'High-quality digital FEA Analysis Report - Stress Testing created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.', 'High-quality fea analysis report - stress testing for professional mechanical engineering applications.', 'DIG31197', '24', '8', 'digital', 'manufacturer', NULL, '172.00', NULL, '0', NULL, NULL, '999', '0', '1', '0', '\"{\\\"File Format\\\":\\\"IGES\\\",\\\"CAD Software\\\":\\\"AutoCAD\\\",\\\"File Size\\\":\\\"2 MB\\\",\\\"Drawing Scale\\\":\\\"1:1\\\",\\\"Units\\\":\\\"Metric (mm)\\\",\\\"Layers\\\":\\\"15 layers\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '\"[\\\"SolidWorks\\\",\\\"AutoCAD\\\",\\\"Fusion 360\\\"]\"', '43.00', '5', NULL, NULL, 'images/demo/gallery-3.jpg', NULL, 'FEA Analysis Report - Stress Testing - Professional Quality', 'Buy high-quality fea analysis report - stress testing from verified sellers. Fast shipping and professional support.', '\"[\\\"fea analysis report - stress testing\\\",\\\"mechanical engineering\\\",\\\"digital\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '944', '36', '56', '62', '4.90', '6', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('35', 'c37ff66a-1e24-48bc-9a64-0763269a2120', 'Aluminum Sheet 6061-T6 - Hydraulic Systems Vietnam Supply Co.', 'aluminum-sheet-6061-t6-hydraulic-systems-vietnam-supply-co-2180', 'Professional grade Aluminum Sheet 6061-T6 designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality aluminum sheet 6061-t6 for professional mechanical engineering applications.', 'PHY81677', '5', '11', 'new_product', 'supplier', NULL, '199.00', NULL, '0', NULL, NULL, '79', '1', '1', '5', '\"{\\\"Material\\\":\\\"Aluminum\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"50 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Stainless Steel', 'Welding', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/demo/gallery-4.jpg', NULL, 'Aluminum Sheet 6061-T6 - Professional Quality', 'Buy high-quality aluminum sheet 6061-t6 from verified sellers. Fast shipping and professional support.', '\"[\\\"aluminum sheet 6061-t6\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '261', '39', '0', '58', '4.60', '5', '0', '2025-06-25 18:19:39', '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('36', '1c0673dc-b92e-48de-a1a3-09569195bc92', 'Aluminum Sheet 6061-T6 - MechaTech Solutions Vietnam Brand', 'aluminum-sheet-6061-t6-mechatech-solutions-vietnam-brand-4221', 'Professional grade Aluminum Sheet 6061-T6 designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality aluminum sheet 6061-t6 for professional mechanical engineering applications.', 'PHY61948', '10', '10', 'new_product', 'brand', NULL, '58.00', NULL, '0', NULL, NULL, '52', '1', '1', '5', '\"{\\\"Material\\\":\\\"Titanium\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"53 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Steel', 'Casting', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/demo/gallery-5.jpg', NULL, 'Aluminum Sheet 6061-T6 - Professional Quality', 'Buy high-quality aluminum sheet 6061-t6 from verified sellers. Fast shipping and professional support.', '\"[\\\"aluminum sheet 6061-t6\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '866', '33', '0', '31', '4.80', '34', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('37', '45785ee6-d29d-4638-8556-4c1173168dd0', 'Aluminum Sheet 6061-T6 - Siemens Vietnam Representative Brand', 'aluminum-sheet-6061-t6-siemens-vietnam-representative-brand-6709', 'Professional grade Aluminum Sheet 6061-T6 designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality aluminum sheet 6061-t6 for professional mechanical engineering applications.', 'PHY70573', '11', '2', 'new_product', 'brand', NULL, '110.00', '97.90', '1', NULL, NULL, '89', '1', '1', '5', '\"{\\\"Material\\\":\\\"Aluminum\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"59 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Stainless Steel', 'Welding', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/demo/thread-1.jpg', NULL, 'Aluminum Sheet 6061-T6 - Professional Quality', 'Buy high-quality aluminum sheet 6061-t6 from verified sellers. Fast shipping and professional support.', '\"[\\\"aluminum sheet 6061-t6\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '354', '49', '0', '68', '3.70', '45', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('38', '0e758f63-1b2d-4dd6-8411-ef9884ac8da9', 'Aluminum Sheet 6061-T6 - Siemens Vietnam Representative Brand', 'aluminum-sheet-6061-t6-siemens-vietnam-representative-brand-3140', 'Professional grade Aluminum Sheet 6061-T6 designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality aluminum sheet 6061-t6 for professional mechanical engineering applications.', 'PHY37338', '26', '12', 'new_product', 'brand', NULL, '149.00', NULL, '0', NULL, NULL, '27', '1', '1', '5', '\"{\\\"Material\\\":\\\"Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"41 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Steel', 'Forging', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/demo/thread-2.jpg', NULL, 'Aluminum Sheet 6061-T6 - Professional Quality', 'Buy high-quality aluminum sheet 6061-t6 from verified sellers. Fast shipping and professional support.', '\"[\\\"aluminum sheet 6061-t6\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '291', '8', '0', '18', '3.70', '91', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('39', 'e9e8cfa2-e1e8-45cb-9a00-bf4d5c9c6159', 'Stainless Steel Rod 316L - Brand Test User Business', 'stainless-steel-rod-316l-brand-test-user-business-1121', 'Professional grade Stainless Steel Rod 316L designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality stainless steel rod 316l for professional mechanical engineering applications.', 'PHY76128', '15', '8', 'new_product', 'brand', NULL, '67.00', NULL, '0', NULL, NULL, '92', '1', '1', '5', '\"{\\\"Material\\\":\\\"Titanium\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"59 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Steel', 'Forging', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/demo/thread-3.jpg', NULL, 'Stainless Steel Rod 316L - Professional Quality', 'Buy high-quality stainless steel rod 316l from verified sellers. Fast shipping and professional support.', '\"[\\\"stainless steel rod 316l\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '276', '2', '0', '74', '4.80', '50', '0', '2025-06-25 18:19:39', '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('40', 'f7adf135-3eef-48f4-bc74-1e3d87ba5b22', 'Stainless Steel Rod 316L - Vietnam Precision Manufacturing Manufacturing', 'stainless-steel-rod-316l-vietnam-precision-manufacturing-manufacturing-7415', 'Professional grade Stainless Steel Rod 316L designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality stainless steel rod 316l for professional mechanical engineering applications.', 'PHY54149', '22', '20', 'new_product', 'manufacturer', NULL, '71.00', NULL, '0', NULL, NULL, '61', '1', '1', '5', '\"{\\\"Material\\\":\\\"Titanium\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"51 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Titanium', 'Casting', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/demo/thread-4.jpg', NULL, 'Stainless Steel Rod 316L - Professional Quality', 'Buy high-quality stainless steel rod 316l from verified sellers. Fast shipping and professional support.', '\"[\\\"stainless steel rod 316l\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '756', '22', '0', '8', '4.50', '11', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('41', 'd0679d59-e046-4b31-92b7-331233bdd547', 'Stainless Steel Rod 316L - Hanoi Industrial Manufacturing Manufacturing', 'stainless-steel-rod-316l-hanoi-industrial-manufacturing-manufacturing-1102', 'Professional grade Stainless Steel Rod 316L designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality stainless steel rod 316l for professional mechanical engineering applications.', 'PHY43134', '23', '17', 'new_product', 'manufacturer', NULL, '152.00', '109.44', '1', NULL, NULL, '52', '1', '1', '5', '\"{\\\"Material\\\":\\\"Aluminum\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"59 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Titanium', 'Forging', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/demo/thread-5.jpg', NULL, 'Stainless Steel Rod 316L - Professional Quality', 'Buy high-quality stainless steel rod 316l from verified sellers. Fast shipping and professional support.', '\"[\\\"stainless steel rod 316l\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '538', '38', '0', '61', '3.50', '12', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('42', '98cf6946-7e46-42fd-b114-aa1e3db899d2', 'Carbon Fiber Plate - Siemens Vietnam Representative Brand', 'carbon-fiber-plate-siemens-vietnam-representative-brand-6768', 'Professional grade Carbon Fiber Plate designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality carbon fiber plate for professional mechanical engineering applications.', 'PHY39338', '11', '15', 'new_product', 'brand', NULL, '476.00', '414.12', '1', NULL, NULL, '37', '1', '1', '5', '\"{\\\"Material\\\":\\\"Stainless Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"40 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Stainless Steel', 'Welding', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/categories/automation.png', NULL, 'Carbon Fiber Plate - Professional Quality', 'Buy high-quality carbon fiber plate from verified sellers. Fast shipping and professional support.', '\"[\\\"carbon fiber plate\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '825', '20', '0', '22', '4.30', '53', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('43', '782e8649-bcb8-4269-b67a-50fcbda3890a', 'Carbon Fiber Plate - Công ty TNHH Thép Việt Nam Supply Co.', 'carbon-fiber-plate-cong-ty-tnhh-thep-viet-nam-supply-co-7732', 'Professional grade Carbon Fiber Plate designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality carbon fiber plate for professional mechanical engineering applications.', 'PHY76966', '16', '20', 'new_product', 'supplier', NULL, '436.00', NULL, '0', NULL, NULL, '78', '1', '1', '5', '\"{\\\"Material\\\":\\\"Aluminum\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"44 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Titanium', 'Casting', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/categories/brakes.png', NULL, 'Carbon Fiber Plate - Professional Quality', 'Buy high-quality carbon fiber plate from verified sellers. Fast shipping and professional support.', '\"[\\\"carbon fiber plate\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '541', '28', '0', '21', '4.70', '37', '0', '2025-06-25 18:19:39', '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('44', '954ecf11-78d0-4db3-8ba0-a87e04710065', 'Carbon Fiber Plate - Hydraulic Systems Vietnam Supply Co.', 'carbon-fiber-plate-hydraulic-systems-vietnam-supply-co-8669', 'Professional grade Carbon Fiber Plate designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality carbon fiber plate for professional mechanical engineering applications.', 'PHY19896', '20', '12', 'new_product', 'supplier', NULL, '319.00', NULL, '0', NULL, NULL, '16', '1', '1', '5', '\"{\\\"Material\\\":\\\"Aluminum\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"55 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Titanium', 'Welding', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/categories/control.png', NULL, 'Carbon Fiber Plate - Professional Quality', 'Buy high-quality carbon fiber plate from verified sellers. Fast shipping and professional support.', '\"[\\\"carbon fiber plate\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '415', '8', '0', '49', '3.80', '58', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('45', '6fa5ce06-8149-4a04-8f1c-9a68d00de820', 'Titanium Alloy Bar - Siemens Vietnam Representative Brand', 'titanium-alloy-bar-siemens-vietnam-representative-brand-7396', 'Professional grade Titanium Alloy Bar designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality titanium alloy bar for professional mechanical engineering applications.', 'PHY53236', '11', '6', 'new_product', 'brand', NULL, '457.00', NULL, '0', NULL, NULL, '15', '1', '1', '5', '\"{\\\"Material\\\":\\\"Titanium\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"43 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Stainless Steel', 'Forging', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/categories/drill.png', NULL, 'Titanium Alloy Bar - Professional Quality', 'Buy high-quality titanium alloy bar from verified sellers. Fast shipping and professional support.', '\"[\\\"titanium alloy bar\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '793', '10', '0', '46', '4.30', '62', '0', '2025-06-25 18:19:39', '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('46', '343a4235-d076-4701-bc2c-3b8fcb15f47e', 'Titanium Alloy Bar - Nhà máy Cơ khí Đông Á Manufacturing', 'titanium-alloy-bar-nha-may-co-khi-dong-a-manufacturing-6280', 'Professional grade Titanium Alloy Bar designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality titanium alloy bar for professional mechanical engineering applications.', 'PHY45213', '21', '15', 'new_product', 'manufacturer', NULL, '690.00', NULL, '0', NULL, NULL, '45', '1', '1', '5', '\"{\\\"Material\\\":\\\"Aluminum\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"49 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Titanium', 'Forging', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/categories/engineering.png', NULL, 'Titanium Alloy Bar - Professional Quality', 'Buy high-quality titanium alloy bar from verified sellers. Fast shipping and professional support.', '\"[\\\"titanium alloy bar\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '853', '39', '0', '1', '4.20', '40', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('47', '7f8b5bcf-8675-4cf5-8889-9e2a5e2af388', 'Titanium Alloy Bar - Vietnam Precision Manufacturing Manufacturing', 'titanium-alloy-bar-vietnam-precision-manufacturing-manufacturing-8252', 'Professional grade Titanium Alloy Bar designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality titanium alloy bar for professional mechanical engineering applications.', 'PHY33551', '22', '8', 'new_product', 'manufacturer', NULL, '593.00', NULL, '0', NULL, NULL, '29', '1', '1', '5', '\"{\\\"Material\\\":\\\"Titanium\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"60 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Titanium', 'Forging', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/categories/mechanic.png', NULL, 'Titanium Alloy Bar - Professional Quality', 'Buy high-quality titanium alloy bar from verified sellers. Fast shipping and professional support.', '\"[\\\"titanium alloy bar\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '600', '2', '0', '50', '4.10', '23', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 10:54:51', NULL);
INSERT INTO `marketplace_products` VALUES ('48', 'a88ea039-65c1-451e-b939-1f8a1b188709', 'Titanium Alloy Bar - Siemens Vietnam Representative Brand', 'titanium-alloy-bar-siemens-vietnam-representative-brand-8523', 'Professional grade Titanium Alloy Bar designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality titanium alloy bar for professional mechanical engineering applications.', 'PHY45358', '26', '8', 'new_product', 'brand', NULL, '797.00', '701.36', '1', NULL, NULL, '61', '1', '1', '5', '\"{\\\"Material\\\":\\\"Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"57 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Titanium', 'Forging', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/categories/robot.png', NULL, 'Titanium Alloy Bar - Professional Quality', 'Buy high-quality titanium alloy bar from verified sellers. Fast shipping and professional support.', '\"[\\\"titanium alloy bar\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '322', '44', '0', '93', '4.40', '77', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('49', '99c83243-a4ec-4f91-87d6-a9217b40ff2c', 'Digital Caliper Set - Mitsubishi Electric Vietnam Brand', 'digital-caliper-set-mitsubishi-electric-vietnam-brand-3393', 'Professional grade Digital Caliper Set designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality digital caliper set for professional mechanical engineering applications.', 'PHY71618', '12', '16', 'new_product', 'brand', NULL, '217.00', NULL, '0', NULL, NULL, '6', '1', '1', '5', '\"{\\\"Material\\\":\\\"Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"42 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Stainless Steel', 'Welding', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/categories/robotic-arm.png', NULL, 'Digital Caliper Set - Professional Quality', 'Buy high-quality digital caliper set from verified sellers. Fast shipping and professional support.', '\"[\\\"digital caliper set\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '764', '16', '0', '62', '3.80', '32', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('50', 'e7202651-54fd-4dbe-8974-90732e713d90', 'Digital Caliper Set - Manufacturer Test User Business', 'digital-caliper-set-manufacturer-test-user-business-4933', 'Professional grade Digital Caliper Set designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality digital caliper set for professional mechanical engineering applications.', 'PHY38378', '14', '11', 'new_product', 'manufacturer', NULL, '269.00', NULL, '0', NULL, NULL, '84', '1', '1', '5', '\"{\\\"Material\\\":\\\"Titanium\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"45 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Aluminum', 'Welding', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/categories/timing.png', NULL, 'Digital Caliper Set - Professional Quality', 'Buy high-quality digital caliper set from verified sellers. Fast shipping and professional support.', '\"[\\\"digital caliper set\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '804', '31', '0', '45', '3.90', '26', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('51', 'f2d183b1-7ddc-45c8-b13d-3f87cf54ba01', 'Digital Caliper Set - Mekong Delta Engineering Works Manufacturing', 'digital-caliper-set-mekong-delta-engineering-works-manufacturing-9830', 'Professional grade Digital Caliper Set designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality digital caliper set for professional mechanical engineering applications.', 'PHY37662', '24', '12', 'new_product', 'manufacturer', NULL, '94.00', NULL, '0', NULL, NULL, '45', '1', '1', '5', '\"{\\\"Material\\\":\\\"Stainless Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"55 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Stainless Steel', 'Casting', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcases/Mechanical-Engineering.jpg', NULL, 'Digital Caliper Set - Professional Quality', 'Buy high-quality digital caliper set from verified sellers. Fast shipping and professional support.', '\"[\\\"digital caliper set\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '101', '48', '0', '40', '3.50', '67', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('52', '93ccd200-3e69-4207-ac78-7d74652607c1', 'Precision Micrometer - Siemens Vietnam Representative Brand', 'precision-micrometer-siemens-vietnam-representative-brand-5061', 'Professional grade Precision Micrometer designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality precision micrometer for professional mechanical engineering applications.', 'PHY54095', '11', '5', 'new_product', 'brand', NULL, '236.00', NULL, '0', NULL, NULL, '56', '1', '1', '5', '\"{\\\"Material\\\":\\\"Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"51 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Stainless Steel', 'Welding', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcases/DesignEngineer.jpg', NULL, 'Precision Micrometer - Professional Quality', 'Buy high-quality precision micrometer from verified sellers. Fast shipping and professional support.', '\"[\\\"precision micrometer\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:39', NULL, NULL, '330', '26', '0', '81', '3.50', '45', '0', NULL, '2025-06-25 18:19:39', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('53', 'e74c0113-5596-4083-8ec3-ca3ade22cc7b', 'Precision Micrometer - Vật Liệu Cơ Khí Hà Nội Supply Co.', 'precision-micrometer-vat-lieu-co-khi-ha-noi-supply-co-4418', 'Professional grade Precision Micrometer designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality precision micrometer for professional mechanical engineering applications.', 'PHY15042', '17', '9', 'new_product', 'supplier', NULL, '271.00', NULL, '0', NULL, NULL, '53', '1', '1', '5', '\"{\\\"Material\\\":\\\"Titanium\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"49 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Stainless Steel', 'Forging', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcases/Mechanical-Engineering-MS-Professionals-Hero-1600x900_0.jpg', NULL, 'Precision Micrometer - Professional Quality', 'Buy high-quality precision micrometer from verified sellers. Fast shipping and professional support.', '\"[\\\"precision micrometer\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:40', NULL, NULL, '920', '36', '0', '95', '5.00', '90', '0', NULL, '2025-06-25 18:19:40', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('54', '3b9d4bdd-83a2-4586-a7a7-dd53b14ca4b6', 'Precision Micrometer - Siemens Vietnam Representative Brand', 'precision-micrometer-siemens-vietnam-representative-brand-5912', 'Professional grade Precision Micrometer designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality precision micrometer for professional mechanical engineering applications.', 'PHY36591', '26', '6', 'new_product', 'brand', NULL, '360.00', NULL, '0', NULL, NULL, '13', '1', '1', '5', '\"{\\\"Material\\\":\\\"Titanium\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"53 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Aluminum', 'Forging', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcases/PFxP5HX8oNsLtufFRMumpc.jpg', NULL, 'Precision Micrometer - Professional Quality', 'Buy high-quality precision micrometer from verified sellers. Fast shipping and professional support.', '\"[\\\"precision micrometer\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:40', NULL, NULL, '117', '9', '0', '45', '5.00', '50', '0', NULL, '2025-06-25 18:19:40', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('55', 'b1cf59b1-d594-4d7c-a7bd-afbfdf749e1e', 'Torque Wrench Kit - Industrial Tools Vietnam Supply Co.', 'torque-wrench-kit-industrial-tools-vietnam-supply-co-3335', 'Professional grade Torque Wrench Kit designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality torque wrench kit for professional mechanical engineering applications.', 'PHY59049', '3', '18', 'new_product', 'supplier', NULL, '361.00', '252.70', '1', NULL, NULL, '29', '1', '1', '5', '\"{\\\"Material\\\":\\\"Titanium\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"60 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Titanium', 'Forging', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcases/depositphotos_73832701-Mechanical-design-office-.jpg', NULL, 'Torque Wrench Kit - Professional Quality', 'Buy high-quality torque wrench kit from verified sellers. Fast shipping and professional support.', '\"[\\\"torque wrench kit\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:40', NULL, NULL, '652', '43', '0', '62', '4.30', '11', '0', NULL, '2025-06-25 18:19:40', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('56', '7b20c6a2-fb06-4329-b7e5-40c048caa247', 'Torque Wrench Kit - Hydraulic Systems Vietnam Supply Co.', 'torque-wrench-kit-hydraulic-systems-vietnam-supply-co-5769', 'Professional grade Torque Wrench Kit designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality torque wrench kit for professional mechanical engineering applications.', 'PHY38707', '5', '20', 'new_product', 'supplier', NULL, '403.00', NULL, '0', NULL, NULL, '79', '1', '1', '5', '\"{\\\"Material\\\":\\\"Aluminum\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"56 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Stainless Steel', 'CNC Machining', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcases/engineering_mechanical_3042380_cropped.jpg', NULL, 'Torque Wrench Kit - Professional Quality', 'Buy high-quality torque wrench kit from verified sellers. Fast shipping and professional support.', '\"[\\\"torque wrench kit\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:40', NULL, NULL, '464', '33', '0', '41', '4.10', '86', '0', NULL, '2025-06-25 18:19:40', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('57', '9e432d67-d69d-4839-a306-d90a07b1c757', 'Surface Roughness Tester - Vật Liệu Cơ Khí Hà Nội Supply Co.', 'surface-roughness-tester-vat-lieu-co-khi-ha-noi-supply-co-5200', 'Professional grade Surface Roughness Tester designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality surface roughness tester for professional mechanical engineering applications.', 'PHY57783', '17', '4', 'new_product', 'supplier', NULL, '1827.00', NULL, '0', NULL, NULL, '81', '1', '1', '5', '\"{\\\"Material\\\":\\\"Stainless Steel\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"44 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Titanium', 'Welding', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcases/mechanical-design-vs-mechanical-engineer2.jpg.webp', NULL, 'Surface Roughness Tester - Professional Quality', 'Buy high-quality surface roughness tester from verified sellers. Fast shipping and professional support.', '\"[\\\"surface roughness tester\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:40', NULL, NULL, '96', '27', '0', '92', '3.50', '54', '0', NULL, '2025-06-25 18:19:40', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('58', '7b7ed558-2bb6-4ec8-bc00-eb90934244a4', 'Surface Roughness Tester - Vietnam Precision Manufacturing Manufacturing', 'surface-roughness-tester-vietnam-precision-manufacturing-manufacturing-2981', 'Professional grade Surface Roughness Tester designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.', 'High-quality surface roughness tester for professional mechanical engineering applications.', 'PHY87135', '22', '15', 'new_product', 'manufacturer', NULL, '614.00', NULL, '0', NULL, NULL, '44', '1', '1', '5', '\"{\\\"Material\\\":\\\"Aluminum\\\",\\\"Tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"Surface Finish\\\":\\\"Ra 0.8\\\\u03bcm\\\",\\\"Hardness\\\":\\\"54 HRC\\\",\\\"Operating Temperature\\\":\\\"-20\\\\u00b0C to +150\\\\u00b0C\\\",\\\"Certification\\\":\\\"ISO 9001:2015\\\"}\"', NULL, 'Aluminum', 'CNC Machining', '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcases/mj_11208_2.jpg', NULL, 'Surface Roughness Tester - Professional Quality', 'Buy high-quality surface roughness tester from verified sellers. Fast shipping and professional support.', '\"[\\\"surface roughness tester\\\",\\\"mechanical engineering\\\",\\\"physical\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:40', NULL, NULL, '928', '11', '0', '70', '3.60', '29', '0', NULL, '2025-06-25 18:19:40', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('59', 'e9dbd695-8c54-4631-9f86-830b6a8a6cd2', 'CNC Machining Service - Bearing & Fastener Supply Co. Supply Co.', 'cnc-machining-service-bearing-fastener-supply-co-supply-co-5623', 'Professional CNC Machining Service provided by certified mechanical engineers with extensive industry experience. We use state-of-the-art equipment and follow international quality standards. Custom solutions available for specific requirements.', 'High-quality cnc machining service for professional mechanical engineering applications.', 'SER45740', '4', '5', 'new_product', 'supplier', NULL, '151.00', NULL, '0', NULL, NULL, '51', '1', '1', '5', '\"{\\\"Lead Time\\\":\\\"11 days\\\",\\\"Minimum Order\\\":\\\"1 piece\\\",\\\"Precision\\\":\\\"\\\\u00b10.05mm\\\",\\\"Quality Standard\\\":\\\"ISO 9001:2015\\\",\\\"Material Options\\\":\\\"Multiple available\\\",\\\"Delivery\\\":\\\"Worldwide shipping\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcases/mj_11226_4.jpg', NULL, 'CNC Machining Service - Professional Quality', 'Buy high-quality cnc machining service from verified sellers. Fast shipping and professional support.', '\"[\\\"cnc machining service\\\",\\\"mechanical engineering\\\",\\\"service\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:40', NULL, NULL, '112', '10', '0', '86', '4.50', '66', '0', '2025-06-25 18:19:40', '2025-06-25 18:19:40', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('60', '7b2d5a0c-7778-4ae9-97f2-0382fb71a88c', 'CNC Machining Service - Siemens Vietnam Representative Brand', 'cnc-machining-service-siemens-vietnam-representative-brand-5871', 'Professional CNC Machining Service provided by certified mechanical engineers with extensive industry experience. We use state-of-the-art equipment and follow international quality standards. Custom solutions available for specific requirements.', 'High-quality cnc machining service for professional mechanical engineering applications.', 'SER55687', '11', '14', 'new_product', 'brand', NULL, '962.00', NULL, '0', NULL, NULL, '61', '1', '1', '5', '\"{\\\"Lead Time\\\":\\\"6 days\\\",\\\"Minimum Order\\\":\\\"1 piece\\\",\\\"Precision\\\":\\\"\\\\u00b10.05mm\\\",\\\"Quality Standard\\\":\\\"ISO 9001:2015\\\",\\\"Material Options\\\":\\\"Multiple available\\\",\\\"Delivery\\\":\\\"Worldwide shipping\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcases/1567174641278.jpg', NULL, 'CNC Machining Service - Professional Quality', 'Buy high-quality cnc machining service from verified sellers. Fast shipping and professional support.', '\"[\\\"cnc machining service\\\",\\\"mechanical engineering\\\",\\\"service\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:40', NULL, NULL, '776', '0', '0', '27', '3.80', '99', '0', NULL, '2025-06-25 18:19:40', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('61', '5d970590-fdba-45c3-b3d4-12973348f826', 'CNC Machining Service - Brand Test User Business', 'cnc-machining-service-brand-test-user-business-8178', 'Professional CNC Machining Service provided by certified mechanical engineers with extensive industry experience. We use state-of-the-art equipment and follow international quality standards. Custom solutions available for specific requirements.', 'High-quality cnc machining service for professional mechanical engineering applications.', 'SER88391', '15', '12', 'new_product', 'brand', NULL, '999.00', NULL, '0', NULL, NULL, '35', '1', '1', '5', '\"{\\\"Lead Time\\\":\\\"6 days\\\",\\\"Minimum Order\\\":\\\"1 piece\\\",\\\"Precision\\\":\\\"\\\\u00b10.05mm\\\",\\\"Quality Standard\\\":\\\"ISO 9001:2015\\\",\\\"Material Options\\\":\\\"Multiple available\\\",\\\"Delivery\\\":\\\"Worldwide shipping\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcases/demo-3.jpg', NULL, 'CNC Machining Service - Professional Quality', 'Buy high-quality cnc machining service from verified sellers. Fast shipping and professional support.', '\"[\\\"cnc machining service\\\",\\\"mechanical engineering\\\",\\\"service\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '1', '1', '2025-06-25 18:19:40', NULL, NULL, '382', '43', '0', '11', '3.80', '80', '0', NULL, '2025-06-25 18:19:40', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('62', '6b52636e-6344-4eb4-9ace-a2a7771b5ac6', 'CNC Machining Service - Mitsubishi Electric Vietnam Brand', 'cnc-machining-service-mitsubishi-electric-vietnam-brand-6908', 'Professional CNC Machining Service provided by certified mechanical engineers with extensive industry experience. We use state-of-the-art equipment and follow international quality standards. Custom solutions available for specific requirements.', 'High-quality cnc machining service for professional mechanical engineering applications.', 'SER19035', '27', '6', 'new_product', 'brand', NULL, '652.00', NULL, '0', NULL, NULL, '41', '1', '1', '5', '\"{\\\"Lead Time\\\":\\\"11 days\\\",\\\"Minimum Order\\\":\\\"1 piece\\\",\\\"Precision\\\":\\\"\\\\u00b10.05mm\\\",\\\"Quality Standard\\\":\\\"ISO 9001:2015\\\",\\\"Material Options\\\":\\\"Multiple available\\\",\\\"Delivery\\\":\\\"Worldwide shipping\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcases/demo-4.jpg', NULL, 'CNC Machining Service - Professional Quality', 'Buy high-quality cnc machining service from verified sellers. Fast shipping and professional support.', '\"[\\\"cnc machining service\\\",\\\"mechanical engineering\\\",\\\"service\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '1', '1', '2025-06-25 18:19:40', NULL, NULL, '565', '13', '0', '73', '4.30', '30', '0', NULL, '2025-06-25 18:19:40', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('63', '8d7c10c8-8be1-4a6c-a7b7-87db0704b9bb', '3D Printing Service - Công ty TNHH Thép Việt Nam Supply Co.', '3d-printing-service-cong-ty-tnhh-thep-viet-nam-supply-co-4338', 'Professional 3D Printing Service provided by certified mechanical engineers with extensive industry experience. We use state-of-the-art equipment and follow international quality standards. Custom solutions available for specific requirements.', 'High-quality 3d printing service for professional mechanical engineering applications.', 'SER93170', '1', '2', 'new_product', 'supplier', NULL, '279.00', NULL, '0', NULL, NULL, '74', '1', '1', '5', '\"{\\\"Lead Time\\\":\\\"9 days\\\",\\\"Minimum Order\\\":\\\"1 piece\\\",\\\"Precision\\\":\\\"\\\\u00b10.05mm\\\",\\\"Quality Standard\\\":\\\"ISO 9001:2015\\\",\\\"Material Options\\\":\\\"Multiple available\\\",\\\"Delivery\\\":\\\"Worldwide shipping\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcases/demo-5.jpg', NULL, '3D Printing Service - Professional Quality', 'Buy high-quality 3d printing service from verified sellers. Fast shipping and professional support.', '\"[\\\"3d printing service\\\",\\\"mechanical engineering\\\",\\\"service\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:40', NULL, NULL, '328', '44', '0', '74', '4.60', '53', '0', NULL, '2025-06-25 18:19:40', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('64', '2a3fee87-9e2d-41a0-88ec-507c27848e5f', '3D Printing Service - Bearing & Fastener Supply Co. Supply Co.', '3d-printing-service-bearing-fastener-supply-co-supply-co-2672', 'Professional 3D Printing Service provided by certified mechanical engineers with extensive industry experience. We use state-of-the-art equipment and follow international quality standards. Custom solutions available for specific requirements.', 'High-quality 3d printing service for professional mechanical engineering applications.', 'SER17193', '4', '5', 'new_product', 'supplier', NULL, '183.00', NULL, '0', NULL, NULL, '69', '1', '1', '5', '\"{\\\"Lead Time\\\":\\\"3 days\\\",\\\"Minimum Order\\\":\\\"1 piece\\\",\\\"Precision\\\":\\\"\\\\u00b10.05mm\\\",\\\"Quality Standard\\\":\\\"ISO 9001:2015\\\",\\\"Material Options\\\":\\\"Multiple available\\\",\\\"Delivery\\\":\\\"Worldwide shipping\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/Mechanical-Engineering.jpg', NULL, '3D Printing Service - Professional Quality', 'Buy high-quality 3d printing service from verified sellers. Fast shipping and professional support.', '\"[\\\"3d printing service\\\",\\\"mechanical engineering\\\",\\\"service\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '1', '1', '2025-06-25 18:19:40', NULL, NULL, '428', '47', '0', '55', '3.60', '85', '0', NULL, '2025-06-25 18:19:40', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('65', '98342743-868c-4020-a0a8-c9f7269945c5', '3D Printing Service - Manufacturer Test User Business', '3d-printing-service-manufacturer-test-user-business-6223', 'Professional 3D Printing Service provided by certified mechanical engineers with extensive industry experience. We use state-of-the-art equipment and follow international quality standards. Custom solutions available for specific requirements.', 'High-quality 3d printing service for professional mechanical engineering applications.', 'SER37635', '14', '12', 'new_product', 'manufacturer', NULL, '97.00', NULL, '0', NULL, NULL, '8', '1', '1', '5', '\"{\\\"Lead Time\\\":\\\"6 days\\\",\\\"Minimum Order\\\":\\\"1 piece\\\",\\\"Precision\\\":\\\"\\\\u00b10.05mm\\\",\\\"Quality Standard\\\":\\\"ISO 9001:2015\\\",\\\"Material Options\\\":\\\"Multiple available\\\",\\\"Delivery\\\":\\\"Worldwide shipping\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/DesignEngineer.jpg', NULL, '3D Printing Service - Professional Quality', 'Buy high-quality 3d printing service from verified sellers. Fast shipping and professional support.', '\"[\\\"3d printing service\\\",\\\"mechanical engineering\\\",\\\"service\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:40', NULL, NULL, '21', '47', '0', '18', '4.40', '26', '0', NULL, '2025-06-25 18:19:40', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('66', 'e83d99a9-79b7-4b48-8bf1-964de5beabaf', '3D Printing Service - Mekong Delta Engineering Works Manufacturing', '3d-printing-service-mekong-delta-engineering-works-manufacturing-1832', 'Professional 3D Printing Service provided by certified mechanical engineers with extensive industry experience. We use state-of-the-art equipment and follow international quality standards. Custom solutions available for specific requirements.', 'High-quality 3d printing service for professional mechanical engineering applications.', 'SER68305', '24', '12', 'new_product', 'manufacturer', NULL, '341.00', NULL, '0', NULL, NULL, '92', '1', '1', '5', '\"{\\\"Lead Time\\\":\\\"4 days\\\",\\\"Minimum Order\\\":\\\"1 piece\\\",\\\"Precision\\\":\\\"\\\\u00b10.05mm\\\",\\\"Quality Standard\\\":\\\"ISO 9001:2015\\\",\\\"Material Options\\\":\\\"Multiple available\\\",\\\"Delivery\\\":\\\"Worldwide shipping\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/Mechanical-Engineering-MS-Professionals-Hero-1600x900_0.jpg', NULL, '3D Printing Service - Professional Quality', 'Buy high-quality 3d printing service from verified sellers. Fast shipping and professional support.', '\"[\\\"3d printing service\\\",\\\"mechanical engineering\\\",\\\"service\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '1', '1', '2025-06-25 18:19:40', NULL, NULL, '901', '34', '0', '15', '3.90', '89', '0', '2025-06-25 18:19:40', '2025-06-25 18:19:40', '2025-07-04 09:43:53', NULL);
INSERT INTO `marketplace_products` VALUES ('67', '7bf1095a-f735-4527-b443-1f97701d79cc', 'Engineering Consultation - Industrial Tools Vietnam Supply Co.', 'engineering-consultation-industrial-tools-vietnam-supply-co-6042', 'Professional Engineering Consultation provided by certified mechanical engineers with extensive industry experience. We use state-of-the-art equipment and follow international quality standards. Custom solutions available for specific requirements.', 'High-quality engineering consultation for professional mechanical engineering applications.', 'SER15218', '3', '18', 'new_product', 'supplier', NULL, '349.00', NULL, '0', NULL, NULL, '30', '1', '1', '5', '\"{\\\"Lead Time\\\":\\\"4 days\\\",\\\"Minimum Order\\\":\\\"1 piece\\\",\\\"Precision\\\":\\\"\\\\u00b10.05mm\\\",\\\"Quality Standard\\\":\\\"ISO 9001:2015\\\",\\\"Material Options\\\":\\\"Multiple available\\\",\\\"Delivery\\\":\\\"Worldwide shipping\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/PFxP5HX8oNsLtufFRMumpc.jpg', NULL, 'Engineering Consultation - Professional Quality', 'Buy high-quality engineering consultation from verified sellers. Fast shipping and professional support.', '\"[\\\"engineering consultation\\\",\\\"mechanical engineering\\\",\\\"service\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:40', NULL, NULL, '333', '40', '0', '20', '4.90', '87', '0', NULL, '2025-06-25 18:19:40', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('68', '4ebbaa8d-71c1-4c94-a67d-bcdefbd628d4', 'Engineering Consultation - MechaTech Solutions Vietnam Brand', 'engineering-consultation-mechatech-solutions-vietnam-brand-4958', 'Professional Engineering Consultation provided by certified mechanical engineers with extensive industry experience. We use state-of-the-art equipment and follow international quality standards. Custom solutions available for specific requirements.', 'High-quality engineering consultation for professional mechanical engineering applications.', 'SER71037', '25', '3', 'new_product', 'brand', NULL, '675.00', NULL, '0', NULL, NULL, '24', '1', '1', '5', '\"{\\\"Lead Time\\\":\\\"11 days\\\",\\\"Minimum Order\\\":\\\"1 piece\\\",\\\"Precision\\\":\\\"\\\\u00b10.05mm\\\",\\\"Quality Standard\\\":\\\"ISO 9001:2015\\\",\\\"Material Options\\\":\\\"Multiple available\\\",\\\"Delivery\\\":\\\"Worldwide shipping\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/depositphotos_73832701-Mechanical-design-office-.jpg', NULL, 'Engineering Consultation - Professional Quality', 'Buy high-quality engineering consultation from verified sellers. Fast shipping and professional support.', '\"[\\\"engineering consultation\\\",\\\"mechanical engineering\\\",\\\"service\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:40', NULL, NULL, '835', '32', '0', '67', '3.50', '28', '0', '2025-06-25 18:19:40', '2025-06-25 18:19:40', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('69', '8c9abb99-0cb0-4f49-a2ba-f5b1e981ff29', 'Quality Inspection Service - Công ty TNHH Thép Việt Nam Supply Co.', 'quality-inspection-service-cong-ty-tnhh-thep-viet-nam-supply-co-7478', 'Professional Quality Inspection Service provided by certified mechanical engineers with extensive industry experience. We use state-of-the-art equipment and follow international quality standards. Custom solutions available for specific requirements.', 'High-quality quality inspection service for professional mechanical engineering applications.', 'SER15947', '16', '1', 'new_product', 'supplier', NULL, '295.00', NULL, '0', NULL, NULL, '17', '1', '1', '5', '\"{\\\"Lead Time\\\":\\\"5 days\\\",\\\"Minimum Order\\\":\\\"1 piece\\\",\\\"Precision\\\":\\\"\\\\u00b10.05mm\\\",\\\"Quality Standard\\\":\\\"ISO 9001:2015\\\",\\\"Material Options\\\":\\\"Multiple available\\\",\\\"Delivery\\\":\\\"Worldwide shipping\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/engineering_mechanical_3042380_cropped.jpg', NULL, 'Quality Inspection Service - Professional Quality', 'Buy high-quality quality inspection service from verified sellers. Fast shipping and professional support.', '\"[\\\"quality inspection service\\\",\\\"mechanical engineering\\\",\\\"service\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:40', NULL, NULL, '678', '27', '0', '36', '3.80', '78', '0', '2025-06-25 18:19:40', '2025-06-25 18:19:40', '2025-07-04 07:40:48', NULL);
INSERT INTO `marketplace_products` VALUES ('70', '9a72f859-80cb-45e7-966f-3fc33a070b5d', 'Quality Inspection Service - Industrial Tools Vietnam Supply Co.', 'quality-inspection-service-industrial-tools-vietnam-supply-co-4631', 'Professional Quality Inspection Service provided by certified mechanical engineers with extensive industry experience. We use state-of-the-art equipment and follow international quality standards. Custom solutions available for specific requirements.', 'High-quality quality inspection service for professional mechanical engineering applications.', 'SER52423', '18', '13', 'new_product', 'supplier', NULL, '231.00', NULL, '0', NULL, NULL, '53', '1', '1', '5', '\"{\\\"Lead Time\\\":\\\"12 days\\\",\\\"Minimum Order\\\":\\\"1 piece\\\",\\\"Precision\\\":\\\"\\\\u00b10.05mm\\\",\\\"Quality Standard\\\":\\\"ISO 9001:2015\\\",\\\"Material Options\\\":\\\"Multiple available\\\",\\\"Delivery\\\":\\\"Worldwide shipping\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/mechanical-design-vs-mechanical-engineer2.jpg.webp', NULL, 'Quality Inspection Service - Professional Quality', 'Buy high-quality quality inspection service from verified sellers. Fast shipping and professional support.', '\"[\\\"quality inspection service\\\",\\\"mechanical engineering\\\",\\\"service\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '0', '1', '2025-06-25 18:19:40', NULL, NULL, '427', '24', '0', '54', '5.00', '45', '0', '2025-06-25 18:19:40', '2025-06-25 18:19:40', '2025-07-09 13:05:20', NULL);
INSERT INTO `marketplace_products` VALUES ('71', '38caf38a-f7ab-45bb-8b2f-4676fec3cb48', 'Quality Inspection Service - Mekong Delta Engineering Works Manufacturing', 'quality-inspection-service-mekong-delta-engineering-works-manufacturing-6480', 'Professional Quality Inspection Service provided by certified mechanical engineers with extensive industry experience. We use state-of-the-art equipment and follow international quality standards. Custom solutions available for specific requirements.', 'High-quality quality inspection service for professional mechanical engineering applications.', 'SER13182', '24', '14', 'new_product', 'manufacturer', NULL, '202.00', '167.66', '1', NULL, NULL, '6', '1', '1', '5', '\"{\\\"Lead Time\\\":\\\"5 days\\\",\\\"Minimum Order\\\":\\\"1 piece\\\",\\\"Precision\\\":\\\"\\\\u00b10.05mm\\\",\\\"Quality Standard\\\":\\\"ISO 9001:2015\\\",\\\"Material Options\\\":\\\"Multiple available\\\",\\\"Delivery\\\":\\\"Worldwide shipping\\\"}\"', NULL, NULL, NULL, '\"[\\\"ISO 9001:2015\\\",\\\"ANSI\\\\\\/ASME\\\",\\\"DIN Standards\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, 'images/showcase/mj_11208_2.jpg', NULL, 'Quality Inspection Service - Professional Quality', 'Buy high-quality quality inspection service from verified sellers. Fast shipping and professional support.', '\"[\\\"quality inspection service\\\",\\\"mechanical engineering\\\",\\\"service\\\",\\\"professional\\\",\\\"quality\\\"]\"', 'approved', '1', '1', '2025-06-25 18:19:40', NULL, NULL, '542', '11', '0', '48', '4.40', '51', '0', NULL, '2025-06-25 18:19:40', '2025-07-04 09:20:39', NULL);
INSERT INTO `marketplace_products` VALUES ('72', '06cacb1e-b83e-426c-b0f9-e53331119973', 'Bộ bản vẽ CAD - Hộp giảm tốc bánh răng', 'bo-ban-ve-cad-hop-giam-toc-banh-rang', 'Bộ bản vẽ CAD hoàn chỉnh cho hộp giảm tốc bánh răng, tỷ số truyền 1:10. Bao gồm file SolidWorks, AutoCAD và PDF.', 'Bản vẽ CAD hộp giảm tốc bánh răng tỷ số 1:10', 'MP-9R5KX8PL', '40', '10', 'digital', 'supplier', NULL, '150000.00', NULL, '0', NULL, NULL, '999', '0', '1', '5', NULL, NULL, NULL, NULL, NULL, '[\"dwg\",\"sldprt\",\"pdf\"]', '[\"SolidWorks 2020+\",\"AutoCAD 2019+\"]', NULL, NULL, '[\"gearbox_assembly.sldasm\",\"gearbox_parts.zip\",\"technical_drawing.pdf\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '1', '1', '2025-07-09 15:08:02', '1', NULL, '92', '0', '0', '19', '3.60', '5', '0', NULL, '2025-07-09 15:08:02', '2025-07-09 15:08:02', NULL);
INSERT INTO `marketplace_products` VALUES ('73', 'f818ef58-2680-4fdd-8690-e67e6f440780', 'Thư viện linh kiện chuẩn ISO - Ốc vít và bu lông', 'thu-vien-linh-kien-chuan-iso-oc-vit-va-bu-long', 'Thư viện CAD đầy đủ các loại ốc vít, bu lông theo tiêu chuẩn ISO. Hơn 500 model 3D với kích thước chuẩn.', 'Thư viện CAD ốc vít bu lông chuẩn ISO', 'MP-54LQTI05', '38', '9', 'digital', 'supplier', NULL, '200000.00', NULL, '0', NULL, NULL, '999', '0', '1', '5', NULL, NULL, NULL, NULL, NULL, '[\"sldprt\",\"step\",\"iges\"]', '[\"SolidWorks\",\"Inventor\",\"Fusion 360\"]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '1', '1', '2025-07-09 15:08:02', '1', NULL, '145', '0', '0', '47', '3.90', '8', '0', NULL, '2025-07-09 15:08:02', '2025-07-09 15:08:02', NULL);
INSERT INTO `marketplace_products` VALUES ('74', '5a61b132-3604-4d1f-8031-2b9aa4f593d1', 'Catalog sản phẩm bearing SKF - File CAD', 'catalog-san-pham-bearing-skf-file-cad', 'Catalog đầy đủ các loại bearing SKF với file CAD 3D. Bao gồm thông số kỹ thuật và hướng dẫn lắp đặt.', 'Catalog bearing SKF với file CAD 3D', 'MP-KB10BUEL', '39', '18', 'digital', 'supplier', NULL, '300000.00', NULL, '0', NULL, NULL, '999', '0', '1', '5', NULL, NULL, NULL, NULL, NULL, '[\"step\",\"iges\",\"pdf\"]', '[\"Universal CAD format\"]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '0', '1', '2025-07-09 15:08:02', '1', NULL, '52', '0', '0', '40', '3.50', '20', '0', NULL, '2025-07-09 15:08:02', '2025-07-09 15:08:02', NULL);
INSERT INTO `marketplace_products` VALUES ('75', 'ef90a4e0-4367-4c08-8d60-c2fc901eaf07', 'Bản vẽ kỹ thuật máy phay CNC 3 trục', 'ban-ve-ky-thuat-may-phay-cnc-3-truc', 'Bộ bản vẽ kỹ thuật hoàn chỉnh máy phay CNC 3 trục. Bao gồm assembly, part drawings và bill of materials.', 'Bản vẽ kỹ thuật máy phay CNC 3 trục', 'MP-1GQDKD1G', '43', '19', 'digital', 'manufacturer', NULL, '2000000.00', NULL, '0', NULL, NULL, '999', '0', '1', '5', NULL, NULL, NULL, NULL, NULL, '[\"dwg\",\"sldasm\",\"pdf\",\"xlsx\"]', '[\"SolidWorks 2021+\",\"AutoCAD 2020+\"]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '0', '1', '2025-07-09 15:08:02', '1', NULL, '382', '0', '0', '17', '4.10', '8', '0', NULL, '2025-07-09 15:08:02', '2025-07-09 15:08:02', NULL);
INSERT INTO `marketplace_products` VALUES ('76', '3daca66e-30f3-477f-9c56-1b2104e035d7', 'Bearing SKF 6205-2RS1 - Vòng bi bịt kín', 'bearing-skf-6205-2rs1-vong-bi-bit-kin', 'Vòng bi SKF 6205-2RS1 chính hãng, bịt kín 2 phía. Kích thước: 25x52x15mm. Tải trọng động: 14kN.', 'Vòng bi SKF 6205-2RS1 chính hãng', 'MP-BKBPJGGP', '51', '13', 'new_product', 'supplier', NULL, '85000.00', NULL, '0', NULL, NULL, '50', '1', '1', '5', '{\"inner_diameter\":\"25mm\",\"outer_diameter\":\"52mm\",\"width\":\"15mm\",\"dynamic_load\":\"14kN\",\"static_load\":\"6.8kN\"}', NULL, 'Thép chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '0', '1', '2025-07-09 15:08:02', '1', NULL, '459', '0', '0', '26', '3.80', '11', '0', NULL, '2025-07-09 15:08:02', '2025-07-09 15:08:02', NULL);
INSERT INTO `marketplace_products` VALUES ('77', 'b14f1111-7112-4ed9-8881-b1b4bc68f31e', 'Motor servo Mitsubishi HC-KFS43 - 400W', 'motor-servo-mitsubishi-hc-kfs43-400w', 'Motor servo Mitsubishi HC-KFS43, công suất 400W, tốc độ 3000rpm. Bao gồm encoder tuyệt đối 17-bit.', 'Motor servo Mitsubishi HC-KFS43 400W', 'MP-3MA7LKBL', '56', '4', 'new_product', 'supplier', NULL, '8500000.00', NULL, '0', NULL, NULL, '5', '1', '1', '5', '{\"power\":\"400W\",\"speed\":\"3000rpm\",\"torque\":\"1.27Nm\",\"encoder\":\"17-bit absolute\",\"voltage\":\"200V\"}', NULL, 'Hợp kim nhôm', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '0', '1', '2025-07-09 15:08:02', '1', NULL, '318', '0', '0', '7', '4.90', '9', '0', NULL, '2025-07-09 15:08:02', '2025-07-09 15:08:02', NULL);
INSERT INTO `marketplace_products` VALUES ('78', 'ecb6620d-5a56-4b29-a7f7-a511c79dba14', 'Thép tấm SS304 - 2mm x 1000mm x 2000mm', 'thep-tam-ss304-2mm-x-1000mm-x-2000mm', 'Thép không gỉ SS304 tấm phẳng, độ dày 2mm, kích thước 1000x2000mm. Bề mặt 2B finish.', 'Thép tấm SS304 2mm x 1000x2000mm', 'MP-HZDV0AW8', '40', '20', 'new_product', 'supplier', NULL, '1200000.00', NULL, '0', NULL, NULL, '20', '1', '1', '5', '{\"thickness\":\"2mm\",\"width\":\"1000mm\",\"length\":\"2000mm\",\"surface\":\"2B finish\",\"grade\":\"SS304\"}', NULL, 'Thép không gỉ SS304', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '0', '1', '2025-07-09 15:08:02', '1', NULL, '103', '0', '0', '38', '4.40', '11', '0', NULL, '2025-07-09 15:08:02', '2025-07-09 15:08:02', NULL);

-- Structure for table `notifications`
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(191) NOT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `priority` enum('low','normal','high') NOT NULL DEFAULT 'normal',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_is_read_index` (`user_id`,`is_read`),
  KEY `notifications_type_created_at_index` (`type`,`created_at`),
  KEY `notifications_type_index` (`type`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data for table `notifications`
INSERT INTO `notifications` VALUES ('1', '54', 'business_verified', 'Doanh nghiệp được xác minh', 'Công ty TNHH Cơ Khí Việt Nam đã hoàn tất quy trình xác minh.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/users\"}', 'normal', '0', NULL, '2025-07-10 08:20:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('2', '54', 'marketplace_activity', 'Đơn hàng mới trên Marketplace', 'Có 5 đơn hàng mới cần xử lý trong hệ thống marketplace.', '{\"action_url\":\"#\"}', 'normal', '0', NULL, '2025-07-10 07:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('3', '54', 'commission_paid', 'Hoa hồng đã được thanh toán', 'Đã thanh toán 2,500,000₫ hoa hồng cho 12 sellers trong tháng này.', '{\"action_url\":\"#\"}', 'normal', '1', '2025-07-10 06:50:22', '2025-07-10 05:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('4', '54', 'system_announcement', 'Hệ thống MechaMap đã được cập nhật', 'Phiên bản mới với nhiều tính năng cải tiến đã được triển khai thành công.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\"}', 'high', '0', NULL, '2025-07-10 06:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('5', '54', 'user_registered', 'Người dùng mới đăng ký', 'Có 3 người dùng mới đăng ký tài khoản trong 24h qua.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/users\"}', 'normal', '1', NULL, '2025-07-09 14:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('6', '54', 'forum_activity', 'Hoạt động diễn đàn tăng cao', 'Có 15 bài đăng mới và 45 bình luận trong ngày hôm nay.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/threads\"}', 'normal', '1', '2025-07-10 03:50:22', '2025-07-10 03:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('7', '54', 'system_announcement', 'Bảo trì hệ thống hoàn tất', 'Việc bảo trì hệ thống đã hoàn tất. Tất cả tính năng đã hoạt động bình thường.', '[]', 'normal', '1', '2025-07-08 08:50:22', '2025-07-07 08:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('8', '54', 'forum_activity', 'Thống kê tuần qua', 'Tuần qua có 150 bài đăng mới, 420 bình luận và 25 thành viên mới.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/statistics\"}', 'normal', '1', '2025-07-05 08:50:22', '2025-07-03 08:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('9', '65', 'business_verified', 'Doanh nghiệp được xác minh', 'Công ty TNHH Cơ Khí Việt Nam đã hoàn tất quy trình xác minh.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/users\"}', 'normal', '0', NULL, '2025-07-10 08:20:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('10', '65', 'marketplace_activity', 'Đơn hàng mới trên Marketplace', 'Có 5 đơn hàng mới cần xử lý trong hệ thống marketplace.', '{\"action_url\":\"#\"}', 'normal', '0', NULL, '2025-07-10 07:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('11', '65', 'commission_paid', 'Hoa hồng đã được thanh toán', 'Đã thanh toán 2,500,000₫ hoa hồng cho 12 sellers trong tháng này.', '{\"action_url\":\"#\"}', 'normal', '1', '2025-07-10 06:50:22', '2025-07-10 05:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('12', '65', 'system_announcement', 'Hệ thống MechaMap đã được cập nhật', 'Phiên bản mới với nhiều tính năng cải tiến đã được triển khai thành công.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\"}', 'high', '0', NULL, '2025-07-10 06:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('13', '65', 'user_registered', 'Người dùng mới đăng ký', 'Có 3 người dùng mới đăng ký tài khoản trong 24h qua.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/users\"}', 'normal', '0', '2025-07-09 22:50:22', '2025-07-09 10:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('14', '65', 'forum_activity', 'Hoạt động diễn đàn tăng cao', 'Có 15 bài đăng mới và 45 bình luận trong ngày hôm nay.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/threads\"}', 'normal', '1', NULL, '2025-07-09 20:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('15', '65', 'system_announcement', 'Bảo trì hệ thống hoàn tất', 'Việc bảo trì hệ thống đã hoàn tất. Tất cả tính năng đã hoạt động bình thường.', '[]', 'normal', '1', '2025-07-08 08:50:22', '2025-07-07 08:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('16', '65', 'forum_activity', 'Thống kê tuần qua', 'Tuần qua có 150 bài đăng mới, 420 bình luận và 25 thành viên mới.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/statistics\"}', 'normal', '1', '2025-07-05 08:50:22', '2025-07-03 08:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('17', '28', 'forum_activity', 'Bình luận mới trong thread của bạn', 'Có 2 bình luận mới trong thread \"Hướng dẫn thiết kế CAD cơ bản\".', '{\"action_url\":\"\\/threads\\/123\"}', 'normal', '0', NULL, '2025-07-10 08:35:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('18', '28', 'marketplace_activity', 'Sản phẩm được phê duyệt', 'Sản phẩm \"Thiết kế CAD Engine V1.0\" đã được phê duyệt và đăng lên marketplace.', '{\"action_url\":\"\\/marketplace\\/products\\/1\"}', 'normal', '1', NULL, '2025-07-09 21:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('19', '28', 'system_announcement', 'Hệ thống MechaMap đã được cập nhật', 'Phiên bản mới với nhiều tính năng cải tiến đã được triển khai thành công.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\"}', 'high', '0', NULL, '2025-07-10 06:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('20', '28', 'user_registered', 'Người dùng mới đăng ký', 'Có 3 người dùng mới đăng ký tài khoản trong 24h qua.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/users\"}', 'normal', '1', '2025-07-09 22:50:22', '2025-07-10 01:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('21', '28', 'forum_activity', 'Hoạt động diễn đàn tăng cao', 'Có 15 bài đăng mới và 45 bình luận trong ngày hôm nay.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/threads\"}', 'normal', '0', NULL, '2025-07-10 00:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('22', '28', 'system_announcement', 'Bảo trì hệ thống hoàn tất', 'Việc bảo trì hệ thống đã hoàn tất. Tất cả tính năng đã hoạt động bình thường.', '[]', 'normal', '1', '2025-07-08 08:50:22', '2025-07-07 08:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('23', '28', 'forum_activity', 'Thống kê tuần qua', 'Tuần qua có 150 bài đăng mới, 420 bình luận và 25 thành viên mới.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/statistics\"}', 'normal', '1', '2025-07-05 08:50:22', '2025-07-03 08:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('24', '29', 'forum_activity', 'Bình luận mới trong thread của bạn', 'Có 2 bình luận mới trong thread \"Hướng dẫn thiết kế CAD cơ bản\".', '{\"action_url\":\"\\/threads\\/123\"}', 'normal', '0', NULL, '2025-07-10 08:35:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('25', '29', 'marketplace_activity', 'Sản phẩm được phê duyệt', 'Sản phẩm \"Thiết kế CAD Engine V1.0\" đã được phê duyệt và đăng lên marketplace.', '{\"action_url\":\"\\/marketplace\\/products\\/1\"}', 'normal', '0', NULL, '2025-07-09 20:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('26', '29', 'system_announcement', 'Hệ thống MechaMap đã được cập nhật', 'Phiên bản mới với nhiều tính năng cải tiến đã được triển khai thành công.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\"}', 'high', '0', NULL, '2025-07-10 06:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('27', '29', 'user_registered', 'Người dùng mới đăng ký', 'Có 3 người dùng mới đăng ký tài khoản trong 24h qua.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/users\"}', 'normal', '0', '2025-07-10 04:50:22', '2025-07-10 00:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('28', '29', 'forum_activity', 'Hoạt động diễn đàn tăng cao', 'Có 15 bài đăng mới và 45 bình luận trong ngày hôm nay.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/threads\"}', 'normal', '0', '2025-07-10 01:50:22', '2025-07-10 05:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('29', '29', 'system_announcement', 'Bảo trì hệ thống hoàn tất', 'Việc bảo trì hệ thống đã hoàn tất. Tất cả tính năng đã hoạt động bình thường.', '[]', 'normal', '1', '2025-07-08 08:50:22', '2025-07-07 08:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('30', '29', 'forum_activity', 'Thống kê tuần qua', 'Tuần qua có 150 bài đăng mới, 420 bình luận và 25 thành viên mới.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/statistics\"}', 'normal', '1', '2025-07-05 08:50:22', '2025-07-03 08:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('31', '30', 'forum_activity', 'Bình luận mới trong thread của bạn', 'Có 2 bình luận mới trong thread \"Hướng dẫn thiết kế CAD cơ bản\".', '{\"action_url\":\"\\/threads\\/123\"}', 'normal', '0', NULL, '2025-07-10 08:35:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('32', '30', 'marketplace_activity', 'Sản phẩm được phê duyệt', 'Sản phẩm \"Thiết kế CAD Engine V1.0\" đã được phê duyệt và đăng lên marketplace.', '{\"action_url\":\"\\/marketplace\\/products\\/1\"}', 'normal', '0', NULL, '2025-07-10 00:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('33', '30', 'system_announcement', 'Hệ thống MechaMap đã được cập nhật', 'Phiên bản mới với nhiều tính năng cải tiến đã được triển khai thành công.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\"}', 'high', '0', NULL, '2025-07-10 06:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('34', '30', 'user_registered', 'Người dùng mới đăng ký', 'Có 3 người dùng mới đăng ký tài khoản trong 24h qua.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/users\"}', 'normal', '1', '2025-07-10 05:50:22', '2025-07-09 09:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('35', '30', 'forum_activity', 'Hoạt động diễn đàn tăng cao', 'Có 15 bài đăng mới và 45 bình luận trong ngày hôm nay.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/threads\"}', 'normal', '0', '2025-07-10 01:50:22', '2025-07-10 05:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('36', '30', 'system_announcement', 'Bảo trì hệ thống hoàn tất', 'Việc bảo trì hệ thống đã hoàn tất. Tất cả tính năng đã hoạt động bình thường.', '[]', 'normal', '1', '2025-07-08 08:50:22', '2025-07-07 08:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('37', '30', 'forum_activity', 'Thống kê tuần qua', 'Tuần qua có 150 bài đăng mới, 420 bình luận và 25 thành viên mới.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/statistics\"}', 'normal', '1', '2025-07-05 08:50:22', '2025-07-03 08:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('38', '60', 'forum_activity', 'Bình luận mới trong thread của bạn', 'Có 2 bình luận mới trong thread \"Hướng dẫn thiết kế CAD cơ bản\".', '{\"action_url\":\"\\/threads\\/123\"}', 'normal', '0', NULL, '2025-07-10 08:35:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('39', '60', 'marketplace_activity', 'Sản phẩm được phê duyệt', 'Sản phẩm \"Thiết kế CAD Engine V1.0\" đã được phê duyệt và đăng lên marketplace.', '{\"action_url\":\"\\/marketplace\\/products\\/1\"}', 'normal', '1', '2025-07-10 07:50:22', '2025-07-10 04:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('40', '60', 'system_announcement', 'Hệ thống MechaMap đã được cập nhật', 'Phiên bản mới với nhiều tính năng cải tiến đã được triển khai thành công.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\"}', 'high', '0', NULL, '2025-07-10 06:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('41', '60', 'user_registered', 'Người dùng mới đăng ký', 'Có 3 người dùng mới đăng ký tài khoản trong 24h qua.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/users\"}', 'normal', '0', '2025-07-10 07:50:22', '2025-07-09 10:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('42', '60', 'forum_activity', 'Hoạt động diễn đàn tăng cao', 'Có 15 bài đăng mới và 45 bình luận trong ngày hôm nay.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/threads\"}', 'normal', '0', NULL, '2025-07-10 05:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('43', '60', 'system_announcement', 'Bảo trì hệ thống hoàn tất', 'Việc bảo trì hệ thống đã hoàn tất. Tất cả tính năng đã hoạt động bình thường.', '[]', 'normal', '1', '2025-07-08 08:50:22', '2025-07-07 08:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('44', '60', 'forum_activity', 'Thống kê tuần qua', 'Tuần qua có 150 bài đăng mới, 420 bình luận và 25 thành viên mới.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/statistics\"}', 'normal', '1', '2025-07-05 08:50:22', '2025-07-03 08:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('45', '41', 'forum_activity', 'Bình luận mới trong thread của bạn', 'Có 2 bình luận mới trong thread \"Hướng dẫn thiết kế CAD cơ bản\".', '{\"action_url\":\"\\/threads\\/123\"}', 'normal', '0', NULL, '2025-07-10 08:35:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('46', '41', 'marketplace_activity', 'Sản phẩm được phê duyệt', 'Sản phẩm \"Thiết kế CAD Engine V1.0\" đã được phê duyệt và đăng lên marketplace.', '{\"action_url\":\"\\/marketplace\\/products\\/1\"}', 'normal', '1', '2025-07-10 02:50:22', '2025-07-09 22:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('47', '41', 'business_verified', 'Tài khoản doanh nghiệp đã xác thực', 'Chúc mừng! Tài khoản doanh nghiệp của bạn đã được xác thực thành công.', '{\"action_url\":\"\\/business\\/dashboard\"}', 'high', '0', NULL, '2025-07-10 06:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('48', '41', 'order_update', 'Đơn hàng mới', 'Bạn có đơn hàng mới #12345 cần xử lý.', '{\"action_url\":\"\\/orders\\/12345\"}', 'normal', '0', NULL, '2025-07-10 08:20:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('49', '41', 'system_announcement', 'Hệ thống MechaMap đã được cập nhật', 'Phiên bản mới với nhiều tính năng cải tiến đã được triển khai thành công.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\"}', 'high', '0', NULL, '2025-07-10 06:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('50', '41', 'user_registered', 'Người dùng mới đăng ký', 'Có 3 người dùng mới đăng ký tài khoản trong 24h qua.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/users\"}', 'normal', '1', '2025-07-10 06:50:22', '2025-07-09 10:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('51', '41', 'forum_activity', 'Hoạt động diễn đàn tăng cao', 'Có 15 bài đăng mới và 45 bình luận trong ngày hôm nay.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/threads\"}', 'normal', '0', '2025-07-10 03:50:22', '2025-07-10 00:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('52', '41', 'system_announcement', 'Bảo trì hệ thống hoàn tất', 'Việc bảo trì hệ thống đã hoàn tất. Tất cả tính năng đã hoạt động bình thường.', '[]', 'normal', '1', '2025-07-08 08:50:22', '2025-07-07 08:50:22', '2025-07-10 08:50:22');
INSERT INTO `notifications` VALUES ('53', '41', 'forum_activity', 'Thống kê tuần qua', 'Tuần qua có 150 bài đăng mới, 420 bình luận và 25 thành viên mới.', '{\"action_url\":\"https:\\/\\/mechamap.test\\/admin\\/statistics\"}', 'normal', '1', '2025-07-05 08:50:22', '2025-07-03 08:50:22', '2025-07-10 08:50:22');

-- Structure for table `orders`
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(191) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `processing_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL,
  `payment_status` enum('pending','processing','completed','failed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `payment_method` enum('stripe','vnpay','bank_transfer') DEFAULT NULL,
  `payment_intent_id` varchar(191) DEFAULT NULL,
  `transaction_id` varchar(191) DEFAULT NULL,
  `status` enum('pending','confirmed','processing','completed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`billing_address`)),
  `invoice_number` varchar(191) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `notes` text DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_user_id_status_index` (`user_id`,`status`),
  KEY `orders_payment_status_created_at_index` (`payment_status`,`created_at`),
  KEY `orders_order_number_index` (`order_number`),
  KEY `orders_payment_intent_id_index` (`payment_intent_id`),
  KEY `orders_transaction_id_index` (`transaction_id`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data for table `orders`
INSERT INTO `orders` VALUES ('1', 'MECHA-685BDB3503564', '1', '1700000.00', '170000.00', '0.00', '0.00', '1870000.00', 'failed', 'stripe', NULL, NULL, 'confirmed', '{\"name\":\"Nguy\\u1ec5n V\\u0103n A\",\"phone\":\"0901234567\",\"address\":\"123 \\u0110\\u01b0\\u1eddng ABC, Ph\\u01b0\\u1eddng XYZ\",\"city\":\"TP. H\\u1ed3 Ch\\u00ed Minh\",\"district\":\"Qu\\u1eadn 1\",\"postal_code\":\"70000\"}', NULL, NULL, 'Liên hệ trước 30 phút', NULL, NULL, NULL, '2025-06-13 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('2', 'MECHA-685BDB3504B87', '1', '510000.00', '51000.00', '0.00', '0.00', '561000.00', 'completed', 'vnpay', NULL, NULL, 'completed', '{\"name\":\"Nguy\\u1ec5n V\\u0103n A\",\"phone\":\"0901234567\",\"address\":\"123 \\u0110\\u01b0\\u1eddng ABC, Ph\\u01b0\\u1eddng XYZ\",\"city\":\"TP. H\\u1ed3 Ch\\u00ed Minh\",\"district\":\"Qu\\u1eadn 1\",\"postal_code\":\"70000\"}', NULL, NULL, 'Giao hàng trong giờ hành chính', NULL, NULL, NULL, '2025-06-22 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('3', 'MECHA-685BDB3505753', '2', '1700000.00', '170000.00', '0.00', '0.00', '1870000.00', 'completed', 'vnpay', NULL, NULL, 'confirmed', '{\"name\":\"Tr\\u1ea7n Th\\u1ecb B\",\"phone\":\"0987654321\",\"address\":\"456 \\u0110\\u01b0\\u1eddng DEF, Ph\\u01b0\\u1eddng UVW\",\"city\":\"H\\u00e0 N\\u1ed9i\",\"district\":\"Qu\\u1eadn Ba \\u0110\\u00ecnh\",\"postal_code\":\"10000\"}', NULL, NULL, 'Liên hệ trước 30 phút', NULL, NULL, NULL, '2025-06-17 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('4', 'MECHA-685BDB3506255', '2', '500000.00', '50000.00', '0.00', '0.00', '550000.00', 'completed', 'stripe', NULL, NULL, 'completed', '{\"name\":\"Tr\\u1ea7n Th\\u1ecb B\",\"phone\":\"0987654321\",\"address\":\"456 \\u0110\\u01b0\\u1eddng DEF, Ph\\u01b0\\u1eddng UVW\",\"city\":\"H\\u00e0 N\\u1ed9i\",\"district\":\"Qu\\u1eadn Ba \\u0110\\u00ecnh\",\"postal_code\":\"10000\"}', NULL, NULL, 'Giao hàng trong giờ hành chính', NULL, NULL, NULL, '2025-06-09 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('5', 'MECHA-685BDB3506FD2', '5', '439998.00', '43999.80', '0.00', '0.00', '483997.80', 'completed', 'stripe', NULL, NULL, 'processing', '{\"name\":\"Nguy\\u1ec5n V\\u0103n A\",\"phone\":\"0901234567\",\"address\":\"123 \\u0110\\u01b0\\u1eddng ABC, Ph\\u01b0\\u1eddng XYZ\",\"city\":\"TP. H\\u1ed3 Ch\\u00ed Minh\",\"district\":\"Qu\\u1eadn 1\",\"postal_code\":\"70000\"}', NULL, NULL, 'Kiểm tra hàng trước khi thanh toán', NULL, NULL, NULL, '2025-05-26 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('6', 'MECHA-685BDB3507E27', '5', '950000.00', '95000.00', '0.00', '0.00', '1045000.00', 'failed', 'stripe', NULL, NULL, 'completed', '{\"name\":\"Tr\\u1ea7n Th\\u1ecb B\",\"phone\":\"0987654321\",\"address\":\"456 \\u0110\\u01b0\\u1eddng DEF, Ph\\u01b0\\u1eddng UVW\",\"city\":\"H\\u00e0 N\\u1ed9i\",\"district\":\"Qu\\u1eadn Ba \\u0110\\u00ecnh\",\"postal_code\":\"10000\"}', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-21 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('7', 'MECHA-685BDB3508B87', '7', '2210000.00', '221000.00', '0.00', '0.00', '2431000.00', 'completed', 'vnpay', NULL, NULL, 'cancelled', '{\"name\":\"Tr\\u1ea7n Th\\u1ecb B\",\"phone\":\"0987654321\",\"address\":\"456 \\u0110\\u01b0\\u1eddng DEF, Ph\\u01b0\\u1eddng UVW\",\"city\":\"H\\u00e0 N\\u1ed9i\",\"district\":\"Qu\\u1eadn Ba \\u0110\\u00ecnh\",\"postal_code\":\"10000\"}', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-17 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('8', 'MECHA-685BDB3509B1B', '7', '500000.00', '50000.00', '0.00', '0.00', '550000.00', 'refunded', 'bank_transfer', NULL, NULL, 'completed', '{\"name\":\"Tr\\u1ea7n Th\\u1ecb B\",\"phone\":\"0987654321\",\"address\":\"456 \\u0110\\u01b0\\u1eddng DEF, Ph\\u01b0\\u1eddng UVW\",\"city\":\"H\\u00e0 N\\u1ed9i\",\"district\":\"Qu\\u1eadn Ba \\u0110\\u00ecnh\",\"postal_code\":\"10000\"}', NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-27 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('9', 'MECHA-685BDB350A810', '16', '1880000.00', '188000.00', '0.00', '0.00', '2068000.00', 'refunded', 'vnpay', NULL, NULL, 'pending', '{\"name\":\"Nguy\\u1ec5n V\\u0103n A\",\"phone\":\"0901234567\",\"address\":\"123 \\u0110\\u01b0\\u1eddng ABC, Ph\\u01b0\\u1eddng XYZ\",\"city\":\"TP. H\\u1ed3 Ch\\u00ed Minh\",\"district\":\"Qu\\u1eadn 1\",\"postal_code\":\"70000\"}', NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-28 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('10', 'MECHA-685BDB350B906', '16', '1700000.00', '170000.00', '0.00', '0.00', '1870000.00', 'completed', 'bank_transfer', NULL, NULL, 'completed', '{\"name\":\"Tr\\u1ea7n Th\\u1ecb B\",\"phone\":\"0987654321\",\"address\":\"456 \\u0110\\u01b0\\u1eddng DEF, Ph\\u01b0\\u1eddng UVW\",\"city\":\"H\\u00e0 N\\u1ed9i\",\"district\":\"Qu\\u1eadn Ba \\u0110\\u00ecnh\",\"postal_code\":\"10000\"}', NULL, NULL, 'Giao hàng trong giờ hành chính', NULL, NULL, NULL, '2025-06-06 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('11', 'MECHA-685BDB350C523', '18', '792500.00', '79250.00', '0.00', '0.00', '871750.00', 'completed', 'vnpay', NULL, NULL, 'completed', '{\"name\":\"Nguy\\u1ec5n V\\u0103n A\",\"phone\":\"0901234567\",\"address\":\"123 \\u0110\\u01b0\\u1eddng ABC, Ph\\u01b0\\u1eddng XYZ\",\"city\":\"TP. H\\u1ed3 Ch\\u00ed Minh\",\"district\":\"Qu\\u1eadn 1\",\"postal_code\":\"70000\"}', NULL, NULL, 'Giao hàng trong giờ hành chính', NULL, NULL, NULL, '2025-06-17 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('12', 'MECHA-685BDB350D384', '21', '950000.00', '95000.00', '0.00', '0.00', '1045000.00', 'completed', 'bank_transfer', NULL, NULL, 'processing', '{\"name\":\"Nguy\\u1ec5n V\\u0103n A\",\"phone\":\"0901234567\",\"address\":\"123 \\u0110\\u01b0\\u1eddng ABC, Ph\\u01b0\\u1eddng XYZ\",\"city\":\"TP. H\\u1ed3 Ch\\u00ed Minh\",\"district\":\"Qu\\u1eadn 1\",\"postal_code\":\"70000\"}', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-15 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('13', 'MECHA-685BDB350DED5', '21', '292500.00', '29250.00', '0.00', '0.00', '321750.00', 'completed', 'vnpay', NULL, NULL, 'confirmed', '{\"name\":\"Tr\\u1ea7n Th\\u1ecb B\",\"phone\":\"0987654321\",\"address\":\"456 \\u0110\\u01b0\\u1eddng DEF, Ph\\u01b0\\u1eddng UVW\",\"city\":\"H\\u00e0 N\\u1ed9i\",\"district\":\"Qu\\u1eadn Ba \\u0110\\u00ecnh\",\"postal_code\":\"10000\"}', NULL, NULL, 'Giao hàng trong giờ hành chính', NULL, NULL, NULL, '2025-06-21 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('14', 'MECHA-685BDB350ECA0', '22', '392484.00', '39248.40', '0.00', '0.00', '431732.40', 'failed', 'bank_transfer', NULL, NULL, 'completed', '{\"name\":\"Tr\\u1ea7n Th\\u1ecb B\",\"phone\":\"0987654321\",\"address\":\"456 \\u0110\\u01b0\\u1eddng DEF, Ph\\u01b0\\u1eddng UVW\",\"city\":\"H\\u00e0 N\\u1ed9i\",\"district\":\"Qu\\u1eadn Ba \\u0110\\u00ecnh\",\"postal_code\":\"10000\"}', NULL, NULL, 'Kiểm tra hàng trước khi thanh toán', NULL, NULL, NULL, '2025-06-06 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('15', 'MECHA-685BDB350FF8B', '22', '4612500.00', '461250.00', '0.00', '0.00', '5073750.00', 'completed', 'stripe', NULL, NULL, 'completed', '{\"name\":\"Tr\\u1ea7n Th\\u1ecb B\",\"phone\":\"0987654321\",\"address\":\"456 \\u0110\\u01b0\\u1eddng DEF, Ph\\u01b0\\u1eddng UVW\",\"city\":\"H\\u00e0 N\\u1ed9i\",\"district\":\"Qu\\u1eadn Ba \\u0110\\u00ecnh\",\"postal_code\":\"10000\"}', NULL, NULL, 'Kiểm tra hàng trước khi thanh toán', NULL, NULL, NULL, '2025-05-28 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('16', 'MECHA-685BDB351100F', '23', '1020000.00', '102000.00', '0.00', '0.00', '1122000.00', 'completed', 'stripe', NULL, NULL, 'completed', '{\"name\":\"Nguy\\u1ec5n V\\u0103n A\",\"phone\":\"0901234567\",\"address\":\"123 \\u0110\\u01b0\\u1eddng ABC, Ph\\u01b0\\u1eddng XYZ\",\"city\":\"TP. H\\u1ed3 Ch\\u00ed Minh\",\"district\":\"Qu\\u1eadn 1\",\"postal_code\":\"70000\"}', NULL, NULL, 'Giao hàng trong giờ hành chính', NULL, NULL, NULL, '2025-06-05 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('17', 'MECHA-685BDB3511D75', '27', '2295000.00', '229500.00', '0.00', '0.00', '2524500.00', 'completed', 'vnpay', NULL, NULL, 'confirmed', '{\"name\":\"Nguy\\u1ec5n V\\u0103n A\",\"phone\":\"0901234567\",\"address\":\"123 \\u0110\\u01b0\\u1eddng ABC, Ph\\u01b0\\u1eddng XYZ\",\"city\":\"TP. H\\u1ed3 Ch\\u00ed Minh\",\"district\":\"Qu\\u1eadn 1\",\"postal_code\":\"70000\"}', NULL, NULL, 'Giao hàng trong giờ hành chính', NULL, NULL, NULL, '2025-06-14 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('18', 'MECHA-685BDB3512E9D', '27', '279984.00', '27998.40', '0.00', '0.00', '307982.40', 'completed', 'stripe', NULL, NULL, 'completed', '{\"name\":\"Nguy\\u1ec5n V\\u0103n A\",\"phone\":\"0901234567\",\"address\":\"123 \\u0110\\u01b0\\u1eddng ABC, Ph\\u01b0\\u1eddng XYZ\",\"city\":\"TP. H\\u1ed3 Ch\\u00ed Minh\",\"district\":\"Qu\\u1eadn 1\",\"postal_code\":\"70000\"}', NULL, NULL, 'Kiểm tra hàng trước khi thanh toán', NULL, NULL, NULL, '2025-06-19 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('19', 'MECHA-685BDB3513C58', '31', '680000.00', '68000.00', '0.00', '0.00', '748000.00', 'completed', 'vnpay', NULL, NULL, 'completed', '{\"name\":\"Nguy\\u1ec5n V\\u0103n A\",\"phone\":\"0901234567\",\"address\":\"123 \\u0110\\u01b0\\u1eddng ABC, Ph\\u01b0\\u1eddng XYZ\",\"city\":\"TP. H\\u1ed3 Ch\\u00ed Minh\",\"district\":\"Qu\\u1eadn 1\",\"postal_code\":\"70000\"}', NULL, NULL, 'Kiểm tra hàng trước khi thanh toán', NULL, NULL, NULL, '2025-06-17 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('20', 'MECHA-685BDB35146C4', '31', '2050005.00', '205000.50', '0.00', '0.00', '2255005.50', 'pending', 'bank_transfer', NULL, NULL, 'completed', '{\"name\":\"Nguy\\u1ec5n V\\u0103n A\",\"phone\":\"0901234567\",\"address\":\"123 \\u0110\\u01b0\\u1eddng ABC, Ph\\u01b0\\u1eddng XYZ\",\"city\":\"TP. H\\u1ed3 Ch\\u00ed Minh\",\"district\":\"Qu\\u1eadn 1\",\"postal_code\":\"70000\"}', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-04 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('21', 'MECHA-685BDB35151E4', '34', '849996.00', '84999.60', '0.00', '0.00', '934995.60', 'completed', 'stripe', NULL, NULL, 'completed', '{\"name\":\"Nguy\\u1ec5n V\\u0103n A\",\"phone\":\"0901234567\",\"address\":\"123 \\u0110\\u01b0\\u1eddng ABC, Ph\\u01b0\\u1eddng XYZ\",\"city\":\"TP. H\\u1ed3 Ch\\u00ed Minh\",\"district\":\"Qu\\u1eadn 1\",\"postal_code\":\"70000\"}', NULL, NULL, 'Liên hệ trước 30 phút', NULL, NULL, NULL, '2025-05-31 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('22', 'MECHA-685BDB351643F', '34', '180000.00', '18000.00', '0.00', '0.00', '198000.00', 'completed', 'vnpay', NULL, NULL, 'cancelled', '{\"name\":\"Tr\\u1ea7n Th\\u1ecb B\",\"phone\":\"0987654321\",\"address\":\"456 \\u0110\\u01b0\\u1eddng DEF, Ph\\u01b0\\u1eddng UVW\",\"city\":\"H\\u00e0 N\\u1ed9i\",\"district\":\"Qu\\u1eadn Ba \\u0110\\u00ecnh\",\"postal_code\":\"10000\"}', NULL, NULL, 'Kiểm tra hàng trước khi thanh toán', NULL, NULL, NULL, '2025-06-11 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('23', 'MECHA-685BDB3517085', '34', '250005.00', '25000.50', '0.00', '0.00', '275005.50', 'refunded', 'bank_transfer', NULL, NULL, 'processing', '{\"name\":\"Tr\\u1ea7n Th\\u1ecb B\",\"phone\":\"0987654321\",\"address\":\"456 \\u0110\\u01b0\\u1eddng DEF, Ph\\u01b0\\u1eddng UVW\",\"city\":\"H\\u00e0 N\\u1ed9i\",\"district\":\"Qu\\u1eadn Ba \\u0110\\u00ecnh\",\"postal_code\":\"10000\"}', NULL, NULL, 'Giao hàng trong giờ hành chính', NULL, NULL, NULL, '2025-06-05 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('24', 'MECHA-685BDB3517DC5', '35', '439998.00', '43999.80', '0.00', '0.00', '483997.80', 'failed', 'stripe', NULL, NULL, 'completed', '{\"name\":\"Tr\\u1ea7n Th\\u1ecb B\",\"phone\":\"0987654321\",\"address\":\"456 \\u0110\\u01b0\\u1eddng DEF, Ph\\u01b0\\u1eddng UVW\",\"city\":\"H\\u00e0 N\\u1ed9i\",\"district\":\"Qu\\u1eadn Ba \\u0110\\u00ecnh\",\"postal_code\":\"10000\"}', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-05 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('25', 'MECHA-685BDB3518BA4', '35', '2500000.00', '250000.00', '0.00', '0.00', '2750000.00', 'completed', 'stripe', NULL, NULL, 'completed', '{\"name\":\"Tr\\u1ea7n Th\\u1ecb B\",\"phone\":\"0987654321\",\"address\":\"456 \\u0110\\u01b0\\u1eddng DEF, Ph\\u01b0\\u1eddng UVW\",\"city\":\"H\\u00e0 N\\u1ed9i\",\"district\":\"Qu\\u1eadn Ba \\u0110\\u00ecnh\",\"postal_code\":\"10000\"}', NULL, NULL, 'Liên hệ trước 30 phút', NULL, NULL, NULL, '2025-06-22 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('26', 'MECHA-685BDB3519922', '35', '1275000.00', '127500.00', '0.00', '0.00', '1402500.00', 'completed', 'bank_transfer', NULL, NULL, 'pending', '{\"name\":\"Nguy\\u1ec5n V\\u0103n A\",\"phone\":\"0901234567\",\"address\":\"123 \\u0110\\u01b0\\u1eddng ABC, Ph\\u01b0\\u1eddng XYZ\",\"city\":\"TP. H\\u1ed3 Ch\\u00ed Minh\",\"district\":\"Qu\\u1eadn 1\",\"postal_code\":\"70000\"}', NULL, NULL, 'Kiểm tra hàng trước khi thanh toán', NULL, NULL, NULL, '2025-06-19 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('27', 'MECHA-685BDB351A687', '54', '950000.00', '95000.00', '0.00', '0.00', '1045000.00', 'completed', 'stripe', NULL, NULL, 'processing', '{\"name\":\"Tr\\u1ea7n Th\\u1ecb B\",\"phone\":\"0987654321\",\"address\":\"456 \\u0110\\u01b0\\u1eddng DEF, Ph\\u01b0\\u1eddng UVW\",\"city\":\"H\\u00e0 N\\u1ed9i\",\"district\":\"Qu\\u1eadn Ba \\u0110\\u00ecnh\",\"postal_code\":\"10000\"}', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-07 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('28', 'MECHA-685BDB351BB75', '54', '500000.00', '50000.00', '0.00', '0.00', '550000.00', 'completed', 'bank_transfer', NULL, NULL, 'confirmed', '{\"name\":\"Tr\\u1ea7n Th\\u1ecb B\",\"phone\":\"0987654321\",\"address\":\"456 \\u0110\\u01b0\\u1eddng DEF, Ph\\u01b0\\u1eddng UVW\",\"city\":\"H\\u00e0 N\\u1ed9i\",\"district\":\"Qu\\u1eadn Ba \\u0110\\u00ecnh\",\"postal_code\":\"10000\"}', NULL, NULL, 'Liên hệ trước 30 phút', NULL, NULL, NULL, '2025-06-09 18:19:17', '2025-06-25 18:19:17');
INSERT INTO `orders` VALUES ('29', 'ORD-685BDC1D81F56', '1', '379.00', '37.90', '0.00', '0.00', '416.90', 'cancelled', 'bank_transfer', NULL, NULL, 'pending', '\"{\\\"name\\\":\\\"Nguy\\\\u1ec5n Qu\\\\u1ea3n Tr\\\\u1ecb\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 95\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-09 18:23:09', '2025-06-25 18:23:09');
INSERT INTO `orders` VALUES ('30', 'ORD-685BDC4529FDA', '1', '423.00', '42.30', '0.00', '0.00', '465.30', 'pending', 'bank_transfer', NULL, NULL, 'pending', '\"{\\\"name\\\":\\\"Nguy\\\\u1ec5n Qu\\\\u1ea3n Tr\\\\u1ecb\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 68\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-29 18:23:49', '2025-06-20 18:23:49');
INSERT INTO `orders` VALUES ('31', 'ORD-685BDC5F712CA', '1', '161.00', '16.10', '0.00', '0.00', '177.10', 'failed', 'stripe', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"Nguy\\\\u1ec5n Qu\\\\u1ea3n Tr\\\\u1ecb\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 99\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-30 18:24:15', '2025-06-21 18:24:15');
INSERT INTO `orders` VALUES ('32', 'ORD-685BDC5F73078', '2', '279.00', '27.90', '0.00', '0.00', '306.90', 'processing', 'stripe', NULL, NULL, 'pending', '\"{\\\"name\\\":\\\"Tr\\\\u1ea7n H\\\\u1ec7 Th\\\\u1ed1ng\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 75\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-28 18:24:15', '2025-06-21 18:24:15');
INSERT INTO `orders` VALUES ('33', 'ORD-685BDC5F74A67', '3', '285.00', '28.50', '0.00', '0.00', '313.50', 'completed', 'vnpay', NULL, NULL, 'confirmed', '\"{\\\"name\\\":\\\"L\\\\u00ea Ki\\\\u1ec3m Duy\\\\u1ec7t\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 84\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-27 18:24:15', '2025-06-22 18:24:15');
INSERT INTO `orders` VALUES ('34', 'ORD-685BDC85871A7', '1', '299.00', '29.90', '0.00', '0.00', '328.90', 'processing', 'bank_transfer', NULL, NULL, 'processing', '\"{\\\"name\\\":\\\"Nguy\\\\u1ec5n Qu\\\\u1ea3n Tr\\\\u1ecb\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 31\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-16 18:24:53', '2025-06-21 18:24:53');
INSERT INTO `orders` VALUES ('35', 'ORD-685BDC8588CFB', '2', '279.00', '27.90', '0.00', '0.00', '306.90', 'processing', 'vnpay', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"Tr\\\\u1ea7n H\\\\u1ec7 Th\\\\u1ed1ng\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 60\\\",\\\"city\\\":\\\"\\\\u0110\\\\u00e0 N\\\\u1eb5ng\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-18 18:24:53', '2025-06-20 18:24:53');
INSERT INTO `orders` VALUES ('36', 'ORD-685BDC85896B0', '3', '259.00', '25.90', '0.00', '0.00', '284.90', 'failed', 'stripe', NULL, NULL, 'pending', '\"{\\\"name\\\":\\\"L\\\\u00ea Ki\\\\u1ec3m Duy\\\\u1ec7t\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 67\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-07 18:24:53', '2025-06-25 18:24:53');
INSERT INTO `orders` VALUES ('37', 'ORD-685BDC858ADD7', '4', '291.00', '29.10', '0.00', '0.00', '320.10', 'completed', 'stripe', NULL, NULL, 'cancelled', '\"{\\\"name\\\":\\\"Ph\\\\u1ea1m N\\\\u1ed9i Dung\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 56\\\",\\\"city\\\":\\\"\\\\u0110\\\\u00e0 N\\\\u1eb5ng\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-30 18:24:53', '2025-06-25 18:24:53');
INSERT INTO `orders` VALUES ('38', 'ORD-685BDC858C6D8', '5', '141.00', '14.10', '0.00', '0.00', '155.10', 'completed', 'vnpay', NULL, NULL, 'processing', '\"{\\\"name\\\":\\\"Ng\\\\u00f4 Qu\\\\u1ea3n L\\\\u00fd\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 56\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-22 18:24:53', '2025-06-24 18:24:53');
INSERT INTO `orders` VALUES ('39', 'ORD-685BDC858D88C', '6', '163.00', '16.30', '0.00', '0.00', '179.30', 'processing', 'bank_transfer', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"Sarah Wilson\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 100\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-30 18:24:53', '2025-06-25 18:24:53');
INSERT INTO `orders` VALUES ('40', 'ORD-685BDC858E137', '7', '253.00', '25.30', '0.00', '0.00', '278.30', 'completed', 'bank_transfer', NULL, NULL, 'pending', '\"{\\\"name\\\":\\\"Ho\\\\u00e0ng C\\\\u01a1 Kh\\\\u00ed\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 7\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-11 18:24:53', '2025-06-20 18:24:53');
INSERT INTO `orders` VALUES ('41', 'ORD-685BDC858F38A', '8', '247.00', '24.70', '0.00', '0.00', '271.70', 'completed', 'bank_transfer', NULL, NULL, 'pending', '\"{\\\"name\\\":\\\"\\\\u0110\\\\u1ed7 T\\\\u1ef1 \\\\u0110\\\\u1ed9ng\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 91\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-11 18:24:53', '2025-06-25 18:24:53');
INSERT INTO `orders` VALUES ('42', 'ORD-685BDC8590C36', '9', '452.00', '45.20', '0.00', '0.00', '497.20', 'pending', 'vnpay', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"V\\\\u0169 CAD Master\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 99\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-23 18:24:53', '2025-06-24 18:24:53');
INSERT INTO `orders` VALUES ('43', 'ORD-685BDC8591E31', '10', '67.00', '6.70', '0.00', '0.00', '73.70', 'completed', 'vnpay', NULL, NULL, 'confirmed', '\"{\\\"name\\\":\\\"L\\\\u00fd V\\\\u1eadt Li\\\\u1ec7u\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 54\\\",\\\"city\\\":\\\"\\\\u0110\\\\u00e0 N\\\\u1eb5ng\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-01 18:24:53', '2025-06-21 18:24:53');
INSERT INTO `orders` VALUES ('44', 'ORD-685BDC8592F84', '11', '427.00', '42.70', '0.00', '0.00', '469.70', 'pending', 'vnpay', NULL, NULL, 'pending', '\"{\\\"name\\\":\\\"Michael Chen\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 49\\\",\\\"city\\\":\\\"\\\\u0110\\\\u00e0 N\\\\u1eb5ng\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-30 18:24:53', '2025-06-25 18:24:53');
INSERT INTO `orders` VALUES ('45', 'ORD-685BDC85942B3', '12', '243.00', '24.30', '0.00', '0.00', '267.30', 'cancelled', 'stripe', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"Tr\\\\u01b0\\\\u01a1ng Thi\\\\u1ebft K\\\\u1ebf\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 21\\\",\\\"city\\\":\\\"\\\\u0110\\\\u00e0 N\\\\u1eb5ng\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-10 18:24:53', '2025-06-20 18:24:53');
INSERT INTO `orders` VALUES ('46', 'ORD-685BDC8594C56', '13', '218.00', '21.80', '0.00', '0.00', '239.80', 'pending', 'vnpay', NULL, NULL, 'cancelled', '\"{\\\"name\\\":\\\"Nguy\\\\u1ec5n H\\\\u1ecdc Vi\\\\u00ean\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 30\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-11 18:24:53', '2025-06-24 18:24:53');
INSERT INTO `orders` VALUES ('47', 'ORD-685BDC85953D5', '14', '236.00', '23.60', '0.00', '0.00', '259.60', 'cancelled', 'vnpay', NULL, NULL, 'cancelled', '\"{\\\"name\\\":\\\"Tr\\\\u1ea7n Fresher\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 42\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-10 18:24:53', '2025-06-23 18:24:53');
INSERT INTO `orders` VALUES ('48', 'ORD-685BDC8595F52', '15', '389.00', '38.90', '0.00', '0.00', '427.90', 'cancelled', 'bank_transfer', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"L\\\\u00ea K\\\\u1ef9 Thu\\\\u1eadt\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 97\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-29 18:24:53', '2025-06-20 18:24:53');
INSERT INTO `orders` VALUES ('49', 'ORD-685BDD0F0CFBF', '1', '460.00', '46.00', '0.00', '0.00', '506.00', 'failed', 'stripe', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"Nguy\\\\u1ec5n Qu\\\\u1ea3n Tr\\\\u1ecb\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 59\\\",\\\"city\\\":\\\"\\\\u0110\\\\u00e0 N\\\\u1eb5ng\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-29 18:27:11', '2025-06-23 18:27:11');
INSERT INTO `orders` VALUES ('50', 'ORD-685BDD0F0FB25', '2', '325.00', '32.50', '0.00', '0.00', '357.50', 'completed', 'bank_transfer', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"Tr\\\\u1ea7n H\\\\u1ec7 Th\\\\u1ed1ng\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 74\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-09 18:27:11', '2025-06-22 18:27:11');
INSERT INTO `orders` VALUES ('51', 'ORD-685BDD0F116DA', '3', '300.00', '30.00', '0.00', '0.00', '330.00', 'completed', 'stripe', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"L\\\\u00ea Ki\\\\u1ec3m Duy\\\\u1ec7t\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 80\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-27 18:27:11', '2025-06-23 18:27:11');
INSERT INTO `orders` VALUES ('52', 'ORD-685BDD0F13332', '4', '279.00', '27.90', '0.00', '0.00', '306.90', 'pending', 'bank_transfer', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"Ph\\\\u1ea1m N\\\\u1ed9i Dung\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 3\\\",\\\"city\\\":\\\"\\\\u0110\\\\u00e0 N\\\\u1eb5ng\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-12 18:27:11', '2025-06-23 18:27:11');
INSERT INTO `orders` VALUES ('53', 'ORD-685BDD0F145C4', '5', '234.00', '23.40', '0.00', '0.00', '257.40', 'failed', 'vnpay', NULL, NULL, 'confirmed', '\"{\\\"name\\\":\\\"Ng\\\\u00f4 Qu\\\\u1ea3n L\\\\u00fd\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 9\\\",\\\"city\\\":\\\"\\\\u0110\\\\u00e0 N\\\\u1eb5ng\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-11 18:27:11', '2025-06-22 18:27:11');
INSERT INTO `orders` VALUES ('54', 'ORD-685BDD0F156A4', '6', '138.00', '13.80', '0.00', '0.00', '151.80', 'pending', 'bank_transfer', NULL, NULL, 'cancelled', '\"{\\\"name\\\":\\\"Sarah Wilson\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 78\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-22 18:27:11', '2025-06-21 18:27:11');
INSERT INTO `orders` VALUES ('55', 'ORD-685BDD0F15E84', '7', '356.00', '35.60', '0.00', '0.00', '391.60', 'processing', 'bank_transfer', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"Ho\\\\u00e0ng C\\\\u01a1 Kh\\\\u00ed\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 90\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-20 18:27:11', '2025-06-25 18:27:11');
INSERT INTO `orders` VALUES ('56', 'ORD-685BDD0F16697', '8', '170.00', '17.00', '0.00', '0.00', '187.00', 'pending', 'vnpay', NULL, NULL, 'pending', '\"{\\\"name\\\":\\\"\\\\u0110\\\\u1ed7 T\\\\u1ef1 \\\\u0110\\\\u1ed9ng\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 4\\\",\\\"city\\\":\\\"\\\\u0110\\\\u00e0 N\\\\u1eb5ng\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-28 18:27:11', '2025-06-20 18:27:11');
INSERT INTO `orders` VALUES ('57', 'ORD-685BDD0F1788B', '9', '187.00', '18.70', '0.00', '0.00', '205.70', 'processing', 'bank_transfer', NULL, NULL, 'processing', '\"{\\\"name\\\":\\\"V\\\\u0169 CAD Master\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 38\\\",\\\"city\\\":\\\"\\\\u0110\\\\u00e0 N\\\\u1eb5ng\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-23 18:27:11', '2025-06-20 18:27:11');
INSERT INTO `orders` VALUES ('58', 'ORD-685BDD0F18572', '10', '342.00', '34.20', '0.00', '0.00', '376.20', 'completed', 'stripe', NULL, NULL, 'confirmed', '\"{\\\"name\\\":\\\"L\\\\u00fd V\\\\u1eadt Li\\\\u1ec7u\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 93\\\",\\\"city\\\":\\\"\\\\u0110\\\\u00e0 N\\\\u1eb5ng\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-07 18:27:11', '2025-06-22 18:27:11');
INSERT INTO `orders` VALUES ('59', 'ORD-685BDD0F1985A', '11', '268.00', '26.80', '0.00', '0.00', '294.80', 'pending', 'bank_transfer', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"Michael Chen\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 46\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-06 18:27:11', '2025-06-20 18:27:11');
INSERT INTO `orders` VALUES ('60', 'ORD-685BDD0F1A6B4', '12', '242.00', '24.20', '0.00', '0.00', '266.20', 'completed', 'stripe', NULL, NULL, 'processing', '\"{\\\"name\\\":\\\"Tr\\\\u01b0\\\\u01a1ng Thi\\\\u1ebft K\\\\u1ebf\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 93\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-27 18:27:11', '2025-06-21 18:27:11');
INSERT INTO `orders` VALUES ('61', 'ORD-685BDD0F1C0A6', '13', '72.00', '7.20', '0.00', '0.00', '79.20', 'cancelled', 'bank_transfer', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"Nguy\\\\u1ec5n H\\\\u1ecdc Vi\\\\u00ean\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 63\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-14 18:27:11', '2025-06-22 18:27:11');
INSERT INTO `orders` VALUES ('62', 'ORD-685BDD0F1CD2A', '14', '354.00', '35.40', '0.00', '0.00', '389.40', 'pending', 'bank_transfer', NULL, NULL, 'cancelled', '\"{\\\"name\\\":\\\"Tr\\\\u1ea7n Fresher\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 100\\\",\\\"city\\\":\\\"\\\\u0110\\\\u00e0 N\\\\u1eb5ng\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-04 18:27:11', '2025-06-23 18:27:11');
INSERT INTO `orders` VALUES ('63', 'ORD-685BDD0F1DDBF', '15', '386.00', '38.60', '0.00', '0.00', '424.60', 'processing', 'stripe', NULL, NULL, 'processing', '\"{\\\"name\\\":\\\"L\\\\u00ea K\\\\u1ef9 Thu\\\\u1eadt\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 36\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-28 18:27:11', '2025-06-23 18:27:11');
INSERT INTO `orders` VALUES ('64', 'ORD-685BDD38B9149', '1', '479.00', '47.90', '0.00', '0.00', '526.90', 'cancelled', 'bank_transfer', NULL, NULL, 'confirmed', '\"{\\\"name\\\":\\\"Nguy\\\\u1ec5n Qu\\\\u1ea3n Tr\\\\u1ecb\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 81\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-06 18:27:52', '2025-06-22 18:27:52');
INSERT INTO `orders` VALUES ('65', 'ORD-685BDD38BAC70', '2', '340.00', '34.00', '0.00', '0.00', '374.00', 'pending', 'vnpay', NULL, NULL, 'pending', '\"{\\\"name\\\":\\\"Tr\\\\u1ea7n H\\\\u1ec7 Th\\\\u1ed1ng\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 25\\\",\\\"city\\\":\\\"\\\\u0110\\\\u00e0 N\\\\u1eb5ng\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-06 18:27:52', '2025-06-25 18:27:52');
INSERT INTO `orders` VALUES ('66', 'ORD-685BDD38BC3A3', '3', '369.00', '36.90', '0.00', '0.00', '405.90', 'failed', 'vnpay', NULL, NULL, 'pending', '\"{\\\"name\\\":\\\"L\\\\u00ea Ki\\\\u1ec3m Duy\\\\u1ec7t\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 40\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-07 18:27:52', '2025-06-22 18:27:52');
INSERT INTO `orders` VALUES ('67', 'ORD-685BDD38BD4BC', '4', '315.00', '31.50', '0.00', '0.00', '346.50', 'completed', 'bank_transfer', NULL, NULL, 'confirmed', '\"{\\\"name\\\":\\\"Ph\\\\u1ea1m N\\\\u1ed9i Dung\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 33\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-30 18:27:52', '2025-06-23 18:27:52');
INSERT INTO `orders` VALUES ('68', 'ORD-685BDD38BF4A9', '5', '466.00', '46.60', '0.00', '0.00', '512.60', 'failed', 'bank_transfer', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"Ng\\\\u00f4 Qu\\\\u1ea3n L\\\\u00fd\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 33\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-10 18:27:52', '2025-06-22 18:27:52');
INSERT INTO `orders` VALUES ('69', 'ORD-685BDD38C0533', '6', '256.00', '25.60', '0.00', '0.00', '281.60', 'cancelled', 'bank_transfer', NULL, NULL, 'pending', '\"{\\\"name\\\":\\\"Sarah Wilson\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 11\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-23 18:27:52', '2025-06-20 18:27:52');
INSERT INTO `orders` VALUES ('70', 'ORD-685BDD38C0F7D', '7', '172.00', '17.20', '0.00', '0.00', '189.20', 'completed', 'bank_transfer', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"Ho\\\\u00e0ng C\\\\u01a1 Kh\\\\u00ed\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 71\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-11 18:27:52', '2025-06-24 18:27:52');
INSERT INTO `orders` VALUES ('71', 'ORD-685BDD38C1FD0', '8', '452.00', '45.20', '0.00', '0.00', '497.20', 'pending', 'vnpay', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"\\\\u0110\\\\u1ed7 T\\\\u1ef1 \\\\u0110\\\\u1ed9ng\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 90\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-10 18:27:52', '2025-06-23 18:27:52');
INSERT INTO `orders` VALUES ('72', 'ORD-685BDD38C32D7', '9', '370.00', '37.00', '0.00', '0.00', '407.00', 'completed', 'bank_transfer', NULL, NULL, 'confirmed', '\"{\\\"name\\\":\\\"V\\\\u0169 CAD Master\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 44\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-28 18:27:52', '2025-06-25 18:27:52');
INSERT INTO `orders` VALUES ('73', 'ORD-685BDD38C409C', '10', '478.00', '47.80', '0.00', '0.00', '525.80', 'completed', 'stripe', NULL, NULL, 'processing', '\"{\\\"name\\\":\\\"L\\\\u00fd V\\\\u1eadt Li\\\\u1ec7u\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 18\\\",\\\"city\\\":\\\"\\\\u0110\\\\u00e0 N\\\\u1eb5ng\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-10 18:27:52', '2025-06-25 18:27:52');
INSERT INTO `orders` VALUES ('74', 'ORD-685BDD38C5A9C', '11', '438.00', '43.80', '0.00', '0.00', '481.80', 'pending', 'stripe', NULL, NULL, 'cancelled', '\"{\\\"name\\\":\\\"Michael Chen\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 51\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-10 18:27:52', '2025-06-21 18:27:52');
INSERT INTO `orders` VALUES ('75', 'ORD-685BDD38C6E0F', '12', '449.00', '44.90', '0.00', '0.00', '493.90', 'pending', 'vnpay', NULL, NULL, 'processing', '\"{\\\"name\\\":\\\"Tr\\\\u01b0\\\\u01a1ng Thi\\\\u1ebft K\\\\u1ebf\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 5\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-15 18:27:52', '2025-06-25 18:27:52');
INSERT INTO `orders` VALUES ('76', 'ORD-685BDD38C74EE', '13', '285.00', '28.50', '0.00', '0.00', '313.50', 'pending', 'bank_transfer', NULL, NULL, 'processing', '\"{\\\"name\\\":\\\"Nguy\\\\u1ec5n H\\\\u1ecdc Vi\\\\u00ean\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 87\\\",\\\"city\\\":\\\"H\\\\u00e0 N\\\\u1ed9i\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-29 18:27:52', '2025-06-22 18:27:52');
INSERT INTO `orders` VALUES ('77', 'ORD-685BDD38C7F1E', '14', '155.00', '15.50', '0.00', '0.00', '170.50', 'cancelled', 'stripe', NULL, NULL, 'completed', '\"{\\\"name\\\":\\\"Tr\\\\u1ea7n Fresher\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 41\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-05-27 18:27:52', '2025-06-24 18:27:52');
INSERT INTO `orders` VALUES ('78', 'ORD-685BDD38C8E62', '15', '329.00', '32.90', '0.00', '0.00', '361.90', 'processing', 'stripe', NULL, NULL, 'pending', '\"{\\\"name\\\":\\\"L\\\\u00ea K\\\\u1ef9 Thu\\\\u1eadt\\\",\\\"address\\\":\\\"\\\\u0110\\\\u1ecba ch\\\\u1ec9 thanh to\\\\u00e1n 51\\\",\\\"city\\\":\\\"TP.HCM\\\",\\\"country\\\":\\\"Vietnam\\\"}\"', NULL, NULL, 'Technical product order for mechanical engineering', NULL, NULL, NULL, '2025-06-18 18:27:52', '2025-06-23 18:27:52');

-- Structure for table `permissions`
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL COMMENT 'Tên permission (vd: users.create)',
  `display_name` varchar(191) NOT NULL COMMENT 'Tên hiển thị (vd: Tạo người dùng)',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết permission',
  `category` varchar(191) NOT NULL COMMENT 'Nhóm permission (vd: user_management)',
  `module` varchar(191) NOT NULL COMMENT 'Module chính (vd: users, forums, marketplace)',
  `action` varchar(191) NOT NULL COMMENT 'Hành động (vd: create, read, update, delete)',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Thông tin bổ sung (conditions, restrictions)' CHECK (json_valid(`metadata`)),
  `is_system` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Permission hệ thống không thể xóa',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Trạng thái hoạt động',
  `parent_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Permission cha (hierarchy)',
  `dependencies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Permissions phụ thuộc' CHECK (json_valid(`dependencies`)),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`),
  KEY `permissions_category_module_index` (`category`,`module`),
  KEY `permissions_is_active_is_system_index` (`is_active`,`is_system`),
  KEY `permissions_parent_id_index` (`parent_id`),
  KEY `permissions_created_by_foreign` (`created_by`),
  KEY `permissions_updated_by_foreign` (`updated_by`),
  CONSTRAINT `permissions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `permissions_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `permissions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `permissions_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data for table `permissions`
INSERT INTO `permissions` VALUES ('1', 'manage-system', 'Quản lý hệ thống', 'Quyền quản lý toàn bộ hệ thống, cấu hình server và infrastructure', 'system', 'manage', 'system', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('2', 'manage-infrastructure', 'Quản lý hạ tầng', 'Quyền Quản lý hạ tầng', 'system', 'manage', 'infrastructure', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('3', 'manage-database', 'Quản lý cơ sở dữ liệu', 'Quyền Quản lý cơ sở dữ liệu', 'system', 'manage', 'database', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('4', 'manage-security', 'Quản lý bảo mật', 'Quyền Quản lý bảo mật', 'system', 'manage', 'security', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('5', 'access-super-admin', 'Truy cập Super Admin', 'Quyền truy cập cao nhất, có thể thực hiện mọi hành động trong hệ thống', 'system', 'access_super', 'admin', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('6', 'view-system-logs', 'Xem logs hệ thống', 'Quyền Xem logs hệ thống', 'system', 'view_system', 'logs', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('7', 'manage-backups', 'Quản lý backup', 'Quyền Quản lý backup', 'system', 'manage', 'backups', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('8', 'view-users', 'Xem người dùng', 'Quyền xem danh sách và thông tin người dùng', 'users', 'view', 'users', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('9', 'create-users', 'Tạo người dùng', 'Quyền Tạo người dùng', 'users', 'create', 'users', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('10', 'update-users', 'Cập nhật người dùng', 'Quyền Cập nhật người dùng', 'users', 'update', 'users', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('11', 'delete-users', 'Xóa người dùng', 'Quyền Xóa người dùng', 'users', 'delete', 'users', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('12', 'ban-users', 'Cấm người dùng', 'Quyền Cấm người dùng', 'users', 'ban', 'users', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('13', 'manage-user-roles', 'Quản lý vai trò người dùng', 'Quyền gán và thay đổi vai trò của người dùng', 'users', 'manage_user', 'roles', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('14', 'verify-business-accounts', 'Xác thực tài khoản doanh nghiệp', 'Quyền Xác thực tài khoản doanh nghiệp', 'users', 'verify_business', 'accounts', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('15', 'manage-subscriptions', 'Quản lý gói đăng ký', 'Quyền Quản lý gói đăng ký', 'users', 'manage', 'subscriptions', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('16', 'manage-content', 'Quản lý nội dung', 'Quyền Quản lý nội dung', 'content', 'manage', 'content', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('17', 'moderate-content', 'Kiểm duyệt nội dung', 'Quyền kiểm duyệt, phê duyệt hoặc từ chối nội dung do người dùng tạo', 'content', 'moderate', 'content', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('18', 'approve-content', 'Phê duyệt nội dung', 'Quyền Phê duyệt nội dung', 'content', 'approve', 'content', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('19', 'delete-content', 'Xóa nội dung', 'Quyền Xóa nội dung', 'content', 'delete', 'content', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('20', 'manage-categories', 'Quản lý danh mục', 'Quyền Quản lý danh mục', 'content', 'manage', 'categories', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('21', 'manage-forums', 'Quản lý diễn đàn', 'Quyền Quản lý diễn đàn', 'content', 'manage', 'forums', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('22', 'pin-threads', 'Ghim bài viết', 'Quyền Ghim bài viết', 'content', 'pin', 'threads', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('23', 'lock-threads', 'Khóa bài viết', 'Quyền Khóa bài viết', 'content', 'lock', 'threads', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('24', 'feature-content', 'Nổi bật nội dung', 'Quyền Nổi bật nội dung', 'content', 'feature', 'content', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('25', 'manage-marketplace', 'Quản lý marketplace', 'Quyền quản lý toàn bộ marketplace, sản phẩm và giao dịch', 'marketplace', 'manage', 'marketplace', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('26', 'approve-products', 'Phê duyệt sản phẩm', 'Quyền Phê duyệt sản phẩm', 'marketplace', 'approve', 'products', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('27', 'manage-orders', 'Quản lý đơn hàng', 'Quyền Quản lý đơn hàng', 'marketplace', 'manage', 'orders', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('28', 'manage-payments', 'Quản lý thanh toán', 'Quyền Quản lý thanh toán', 'marketplace', 'manage', 'payments', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('29', 'view-marketplace-analytics', 'Xem phân tích marketplace', 'Quyền Xem phân tích marketplace', 'marketplace', 'view_marketplace', 'analytics', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('30', 'manage-seller-accounts', 'Quản lý tài khoản bán hàng', 'Quyền Quản lý tài khoản bán hàng', 'marketplace', 'manage_seller', 'accounts', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('31', 'handle-disputes', 'Xử lý tranh chấp', 'Quyền Xử lý tranh chấp', 'marketplace', 'handle', 'disputes', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('32', 'manage-commissions', 'Quản lý hoa hồng', 'Quyền Quản lý hoa hồng', 'marketplace', 'manage', 'commissions', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('33', 'manage-community', 'Quản lý cộng đồng', 'Quyền Quản lý cộng đồng', 'community', 'manage', 'community', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('34', 'moderate-discussions', 'Kiểm duyệt thảo luận', 'Quyền Kiểm duyệt thảo luận', 'community', 'moderate', 'discussions', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('35', 'manage-events', 'Quản lý sự kiện', 'Quyền Quản lý sự kiện', 'community', 'manage', 'events', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('36', 'send-announcements', 'Gửi thông báo', 'Quyền Gửi thông báo', 'community', 'send', 'announcements', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('37', 'manage-user-groups', 'Quản lý nhóm người dùng', 'Quyền Quản lý nhóm người dùng', 'community', 'manage_user', 'groups', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('38', 'view-analytics', 'Xem phân tích', 'Quyền Xem phân tích', 'analytics', 'view', 'analytics', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('39', 'view-reports', 'Xem báo cáo', 'Quyền Xem báo cáo', 'analytics', 'view', 'reports', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('40', 'export-data', 'Xuất dữ liệu', 'Quyền Xuất dữ liệu', 'analytics', 'export', 'data', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('41', 'manage-reports', 'Quản lý báo cáo', 'Quyền Quản lý báo cáo', 'analytics', 'manage', 'reports', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('42', 'access-admin-panel', 'Truy cập panel admin', 'Quyền truy cập vào khu vực quản trị admin', 'admin_access', 'access_admin', 'panel', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('43', 'access-system-admin', 'Truy cập quản trị hệ thống', 'Quyền Truy cập quản trị hệ thống', 'admin_access', 'access_system', 'admin', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('44', 'access-content-admin', 'Truy cập quản trị nội dung', 'Quyền Truy cập quản trị nội dung', 'admin_access', 'access_content', 'admin', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('45', 'access-marketplace-admin', 'Truy cập quản trị marketplace', 'Quyền Truy cập quản trị marketplace', 'admin_access', 'access_marketplace', 'admin', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('46', 'access-community-admin', 'Truy cập quản trị cộng đồng', 'Quyền Truy cập quản trị cộng đồng', 'admin_access', 'access_community', 'admin', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('47', 'view-content', 'Xem nội dung', 'Quyền Xem nội dung', 'basic', 'view', 'content', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('48', 'create-threads', 'Tạo bài viết', 'Quyền Tạo bài viết', 'basic', 'create', 'threads', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('49', 'create-comments', 'Tạo bình luận', 'Quyền Tạo bình luận', 'basic', 'create', 'comments', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('50', 'upload-files', 'Tải lên tệp', 'Quyền Tải lên tệp', 'basic', 'upload', 'files', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('51', 'send-messages', 'Gửi tin nhắn', 'Quyền Gửi tin nhắn', 'basic', 'send', 'messages', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('52', 'create-polls', 'Tạo bình chọn', 'Quyền Tạo bình chọn', 'basic', 'create', 'polls', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('53', 'rate-products', 'Đánh giá sản phẩm', 'Quyền Đánh giá sản phẩm', 'basic', 'rate', 'products', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('54', 'write-reviews', 'Viết đánh giá', 'Quyền Viết đánh giá', 'basic', 'write', 'reviews', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('55', 'sell-products', 'Bán sản phẩm', 'Quyền Bán sản phẩm', 'business', 'sell', 'products', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('56', 'manage-own-products', 'Quản lý sản phẩm của mình', 'Quyền Quản lý sản phẩm của mình', 'business', 'manage_own', 'products', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('57', 'view-sales-analytics', 'Xem phân tích bán hàng', 'Quyền Xem phân tích bán hàng', 'business', 'view_sales', 'analytics', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('58', 'manage-business-profile', 'Quản lý hồ sơ doanh nghiệp', 'Quyền Quản lý hồ sơ doanh nghiệp', 'business', 'manage_business', 'profile', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('59', 'access-seller-dashboard', 'Truy cập dashboard bán hàng', 'Quyền Truy cập dashboard bán hàng', 'business', 'access_seller', 'dashboard', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('60', 'upload-technical-files', 'Tải lên tệp kỹ thuật', 'Quyền Tải lên tệp kỹ thuật', 'business', 'upload_technical', 'files', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('61', 'manage-cad-files', 'Quản lý tệp CAD', 'Quyền Quản lý tệp CAD', 'business', 'manage_cad', 'files', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('62', 'access-b2b-features', 'Truy cập tính năng B2B', 'Quyền Truy cập tính năng B2B', 'business', 'access_b2b', 'features', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('63', 'manage-faqs', 'Quản lý FAQ', 'Quyền quản lý câu hỏi thường gặp', 'content', 'faqs', 'manage', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('64', 'view-faqs', 'Xem FAQ', 'Quyền xem danh sách FAQ', 'content', 'faqs', 'view', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('65', 'manage-knowledge-base', 'Quản lý Knowledge Base', 'Quyền quản lý cơ sở tri thức', 'content', 'knowledge', 'manage', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('66', 'manage-cad-library', 'Quản lý thư viện CAD', 'Quyền quản lý thư viện tệp CAD', 'business', 'cad', 'manage', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('67', 'manage-showcases', 'Quản lý Showcase', 'Quyền quản lý showcase sản phẩm', 'content', 'showcases', 'manage', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');
INSERT INTO `permissions` VALUES ('68', 'manage-roles-permissions', 'Quản lý Roles & Permissions', 'Quyền quản lý vai trò và phân quyền hệ thống', 'system', 'roles', 'manage', NULL, '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:30', '2025-07-01 21:38:30');

-- Structure for table `roles`
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL COMMENT 'Tên role (vd: super_admin)',
  `display_name` varchar(191) NOT NULL COMMENT 'Tên hiển thị (vd: Super Admin)',
  `description` text DEFAULT NULL COMMENT 'Mô tả vai trò',
  `role_group` enum('system_management','community_management','community_members','business_partners') NOT NULL COMMENT 'Nhóm vai trò chính',
  `hierarchy_level` int(11) NOT NULL DEFAULT 10 COMMENT 'Cấp độ phân quyền (1=cao nhất)',
  `default_permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Permissions mặc định cho role' CHECK (json_valid(`default_permissions`)),
  `restricted_permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Permissions bị cấm' CHECK (json_valid(`restricted_permissions`)),
  `color` varchar(20) NOT NULL DEFAULT 'primary' COMMENT 'Màu badge hiển thị',
  `icon` varchar(50) NOT NULL DEFAULT 'fas fa-user' COMMENT 'Icon hiển thị',
  `is_visible` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Hiển thị trong danh sách',
  `is_system` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Role hệ thống không thể xóa',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Trạng thái hoạt động',
  `can_be_assigned` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Có thể gán cho user',
  `max_users` int(11) DEFAULT NULL COMMENT 'Giới hạn số user (null = không giới hạn)',
  `business_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Quy tắc kinh doanh đặc biệt' CHECK (json_valid(`business_rules`)),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`),
  KEY `roles_role_group_hierarchy_level_index` (`role_group`,`hierarchy_level`),
  KEY `roles_is_active_is_visible_index` (`is_active`,`is_visible`),
  KEY `roles_hierarchy_level_index` (`hierarchy_level`),
  KEY `roles_created_by_foreign` (`created_by`),
  KEY `roles_updated_by_foreign` (`updated_by`),
  CONSTRAINT `roles_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `roles_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data for table `roles`
INSERT INTO `roles` VALUES ('1', 'super_admin', 'Super Admin', 'Quyền cao nhất trong hệ thống, có thể thực hiện mọi hành động', 'system_management', '1', '[\"*\"]', NULL, 'danger', 'fas fa-crown', '1', '1', '1', '1', '2', NULL, '1', NULL, '2025-07-01 21:38:40', '2025-07-01 21:38:40');
INSERT INTO `roles` VALUES ('2', 'system_admin', 'System Admin', 'Quản trị hệ thống và infrastructure, quản lý người dùng', 'system_management', '2', NULL, NULL, 'warning', 'fas fa-cogs', '1', '1', '1', '1', '5', NULL, '1', NULL, '2025-07-01 21:38:40', '2025-07-01 21:38:40');
INSERT INTO `roles` VALUES ('3', 'content_admin', 'Content Admin', 'Quản trị nội dung, trang và tri thức', 'system_management', '3', NULL, NULL, 'info', 'fas fa-edit', '1', '1', '1', '1', '10', NULL, '1', NULL, '2025-07-01 21:38:40', '2025-07-01 21:38:40');
INSERT INTO `roles` VALUES ('4', 'content_moderator', 'Content Moderator', 'Kiểm duyệt nội dung, quản lý diễn đàn và bài viết', 'community_management', '4', NULL, NULL, 'primary', 'fas fa-shield-alt', '1', '1', '1', '1', '20', NULL, '1', NULL, '2025-07-01 21:38:40', '2025-07-01 21:38:40');
INSERT INTO `roles` VALUES ('5', 'marketplace_moderator', 'Marketplace Moderator', 'Kiểm duyệt marketplace, quản lý sản phẩm và giao dịch', 'community_management', '5', NULL, NULL, 'success', 'fas fa-store', '1', '1', '1', '1', '15', NULL, '1', NULL, '2025-07-01 21:38:40', '2025-07-01 21:38:40');
INSERT INTO `roles` VALUES ('6', 'community_moderator', 'Community Moderator', 'Quản lý cộng đồng, sự kiện và hoạt động người dùng', 'community_management', '6', NULL, NULL, 'dark', 'fas fa-users-cog', '1', '1', '1', '1', '25', NULL, '1', NULL, '2025-07-01 21:38:40', '2025-07-01 21:38:40');
INSERT INTO `roles` VALUES ('7', 'senior_member', 'Thành viên cấp cao', 'Thành viên có kinh nghiệm, có thêm quyền trong cộng đồng', 'community_members', '7', NULL, NULL, 'info', 'fas fa-star', '1', '1', '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:40', '2025-07-01 21:38:40');
INSERT INTO `roles` VALUES ('8', 'member', 'Thành viên', 'Thành viên thường của cộng đồng', 'community_members', '8', NULL, NULL, 'primary', 'fas fa-user', '1', '1', '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:40', '2025-07-01 21:38:40');
INSERT INTO `roles` VALUES ('9', 'guest', 'Khách', 'Khách tham quan, quyền hạn hạn chế', 'community_members', '10', NULL, NULL, 'secondary', 'fas fa-eye', '1', '1', '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:40', '2025-07-01 21:38:40');
INSERT INTO `roles` VALUES ('10', 'student', 'Sinh viên', 'Sinh viên đang học tập, có quyền hạn cơ bản', 'community_members', '9', NULL, NULL, 'light', 'fas fa-graduation-cap', '1', '1', '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:40', '2025-07-01 21:38:40');
INSERT INTO `roles` VALUES ('11', 'manufacturer', 'Nhà sản xuất', 'Nhà sản xuất có thể bán sản phẩm và tệp kỹ thuật', 'business_partners', '12', NULL, NULL, 'dark', 'fas fa-industry', '1', '1', '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:40', '2025-07-01 21:38:40');
INSERT INTO `roles` VALUES ('12', 'supplier', 'Nhà cung cấp', 'Nhà cung cấp có thể mua bán sản phẩm', 'business_partners', '13', NULL, NULL, 'success', 'fas fa-truck', '1', '1', '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:40', '2025-07-01 21:38:40');
INSERT INTO `roles` VALUES ('13', 'brand', 'Nhãn hàng', 'Nhãn hàng có quyền xem và quảng cáo', 'business_partners', '14', NULL, NULL, 'purple', 'fas fa-tags', '1', '1', '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:40', '2025-07-01 21:38:40');
INSERT INTO `roles` VALUES ('14', 'verified_partner', 'Đối tác xác thực', 'Đối tác kinh doanh đã được xác thực, có quyền ưu tiên', 'business_partners', '11', NULL, NULL, 'warning', 'fas fa-certificate', '1', '1', '1', '1', NULL, NULL, '1', NULL, '2025-07-01 21:38:40', '2025-07-01 21:38:40');

-- Structure for table `showcases`
DROP TABLE IF EXISTS `showcases`;
CREATE TABLE `showcases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `showcaseable_type` varchar(191) NOT NULL,
  `showcaseable_id` bigint(20) unsigned NOT NULL,
  `title` varchar(191) NOT NULL COMMENT 'Tiêu đề dự án kỹ thuật',
  `slug` varchar(191) NOT NULL COMMENT 'URL-friendly identifier cho project',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết dự án, phương pháp, kết quả',
  `project_type` enum('design','analysis','manufacturing','prototype','assembly','testing','research','optimization','simulation') DEFAULT NULL COMMENT 'Loại dự án kỹ thuật',
  `software_used` varchar(191) DEFAULT NULL COMMENT 'Phần mềm sử dụng: SolidWorks, AutoCAD, ANSYS, CATIA, Fusion360',
  `materials` varchar(191) DEFAULT NULL COMMENT 'Vật liệu sử dụng: Steel, Aluminum, Composite, Plastic, etc.',
  `manufacturing_process` varchar(191) DEFAULT NULL COMMENT 'Quy trình sản xuất: CNC, 3D Printing, Casting, Welding, Machining',
  `technical_specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Thông số kỹ thuật: {"dimensions":"100x50x20mm","tolerance":"±0.01","weight":"2.5kg"}' CHECK (json_valid(`technical_specs`)),
  `category` enum('design','analysis','manufacturing','prototype','assembly','testing','research','innovation','optimization','education') NOT NULL DEFAULT 'design' COMMENT 'Danh mục dự án',
  `complexity_level` enum('beginner','intermediate','advanced','expert') NOT NULL DEFAULT 'intermediate' COMMENT 'Mức độ phức tạp kỹ thuật',
  `industry_application` enum('automotive','aerospace','manufacturing','energy','construction','marine','electronics','medical','general') DEFAULT NULL COMMENT 'Ứng dụng ngành công nghiệp',
  `has_tutorial` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Project có kèm hướng dẫn step-by-step không',
  `has_calculations` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Project có kèm tính toán kỹ thuật không',
  `has_cad_files` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Project có file CAD đính kèm không',
  `learning_objectives` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Mục tiêu học tập: ["FEA analysis","Design optimization","Manufacturing process"]' CHECK (json_valid(`learning_objectives`)),
  `cover_image` varchar(191) DEFAULT NULL COMMENT 'Ảnh đại diện chính của project',
  `image_gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Gallery ảnh process và kết quả' CHECK (json_valid(`image_gallery`)),
  `file_attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Files đính kèm: CAD, drawings, calculations, reports' CHECK (json_valid(`file_attachments`)),
  `status` enum('draft','pending','approved','rejected','featured','archived') NOT NULL DEFAULT 'draft' COMMENT 'Trạng thái review và publication',
  `is_public` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Project có public access không',
  `allow_downloads` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Cho phép download files không',
  `allow_comments` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Cho phép comment và discussion không',
  `view_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Số lượt xem project',
  `like_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Số lượt like từ community',
  `download_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Số lượt download files',
  `share_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Số lượt chia sẻ project',
  `rating_average` decimal(3,2) NOT NULL DEFAULT 0.00 COMMENT 'Đánh giá trung bình (0.00 - 5.00)',
  `rating_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Số lượng đánh giá',
  `technical_quality_score` decimal(3,2) NOT NULL DEFAULT 0.00 COMMENT 'Điểm chất lượng kỹ thuật do expert đánh giá',
  `display_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Thứ tự hiển thị trong category',
  `featured_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian được featured',
  `approved_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian được approve',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `showcases_unique_user_object` (`user_id`,`showcaseable_id`,`showcaseable_type`),
  UNIQUE KEY `showcases_slug_unique` (`slug`),
  KEY `showcases_showcaseable_type_showcaseable_id_index` (`showcaseable_type`,`showcaseable_id`),
  KEY `showcases_approved_by_foreign` (`approved_by`),
  KEY `showcases_status_timeline` (`status`,`created_at`),
  KEY `showcases_user_projects` (`user_id`,`status`),
  KEY `showcases_classification` (`category`,`complexity_level`),
  KEY `showcases_public_featured` (`is_public`,`status`,`featured_at`),
  KEY `showcases_featured_quality` (`status`,`featured_at`,`rating_average`),
  KEY `showcases_technical_tools` (`project_type`,`software_used`),
  KEY `showcases_industry_category` (`category`,`industry_application`),
  KEY `showcases_learning_level` (`complexity_level`,`has_tutorial`),
  KEY `showcases_quality_ranking` (`rating_average`,`rating_count`),
  KEY `showcases_popularity` (`view_count`,`like_count`),
  KEY `showcases_expert_approved` (`technical_quality_score`,`approved_at`),
  KEY `showcases_downloadable_content` (`has_cad_files`,`allow_downloads`),
  KEY `showcases_educational_content` (`has_tutorial`,`has_calculations`),
  KEY `showcases_advanced_filter` (`category`,`project_type`,`complexity_level`,`status`),
  KEY `showcases_professional_search` (`industry_application`,`software_used`,`is_public`),
  FULLTEXT KEY `showcases_content_search` (`title`,`description`),
  CONSTRAINT `showcases_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `showcases_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data for table `showcases`
INSERT INTO `showcases` VALUES ('1', '6', 'App\\Models\\Thread', '52', 'Thiết kế và Phân tích Cầu Trục 5 Tấn', 'thiet-ke-va-phan-tich-cau-truc-5-tan-6-322', 'Dự án thiết kế hoàn chỉnh cầu trục 5 tấn cho nhà máy sản xuất.

**Scope công việc:**
- Thiết kế 3D với SolidWorks
- Phân tích FEA với ANSYS
- Tính toán kết cấu thép
- Bản vẽ chế tạo chi tiết

**Kết quả đạt được:**
- Giảm 15% trọng lượng so với thiết kế cũ
- Tăng 20% độ an toàn
- Tiết kiệm 30% chi phí vật liệu', 'design', '[\"SolidWorks\",\"ANSYS Mechanical\",\"AutoCAD\"]', '[\"Steel S355\",\"Steel S235\",\"Bearing SKF\"]', '[\"Welding\",\"Machining\",\"Assembly\"]', '{\"capacity\":\"5000 kg\",\"span\":\"12 m\",\"lifting_height\":\"8 m\",\"safety_factor\":\"2.5\"}', 'design', 'advanced', 'manufacturing', '1', '1', '1', '[\"Structural Analysis\",\"Steel Design\",\"FEA Simulation\"]', '/images/showcase/Mechanical-Engineering-MS-Professionals-Hero-1600x900_0.jpg', '[\"\\/images\\/showcase\\/Mechanical-Engineering-MS-Professionals-Hero-1600x900_0.jpg\"]', '[\"crane_assembly.SLDASM\",\"stress_analysis.pdf\",\"calculations.xlsx\"]', 'featured', '1', '1', '1', '521', '0', '19', '24', '4.03', '20', '4.96', '0', '2025-06-17 18:06:44', '2025-06-19 18:06:44', '1', '2025-06-16 18:06:44', '2025-06-19 18:06:44');
INSERT INTO `showcases` VALUES ('2', '9', 'App\\Models\\Thread', '30', 'Tối ưu hóa Toolpath CNC cho Aluminum Aerospace', 'toi-uu-hoa-toolpath-cnc-cho-aluminum-aerospace-9-623', 'Nghiên cứu tối ưu hóa toolpath CNC cho gia công chi tiết aluminum hàng không.

**Phương pháp:**
- Sử dụng Mastercam 2023
- Adaptive milling strategies
- High-speed machining parameters
- Surface finish optimization

**Kết quả:**
- Giảm 40% thời gian gia công
- Cải thiện Ra từ 1.6 xuống 0.8 μm
- Tăng tool life 60%', 'manufacturing', '[\"Mastercam\",\"Vericut\",\"SolidWorks\"]', '[\"Aluminum 7075-T6\",\"Aluminum 6061-T6\"]', '[\"CNC Milling\",\"High-Speed Machining\"]', '{\"surface_finish\":\"Ra 0.8 \\u03bcm\",\"tolerance\":\"\\u00b10.02 mm\",\"material_removal_rate\":\"120 cm\\u00b3\\/min\"}', 'manufacturing', 'intermediate', 'aerospace', '1', '1', '1', '[\"CNC Programming\",\"Toolpath Optimization\",\"Surface Finish\"]', '/images/showcase/PFxP5HX8oNsLtufFRMumpc.jpg', '[\"\\/images\\/showcase\\/PFxP5HX8oNsLtufFRMumpc.jpg\"]', '[\"toolpath.mcx\",\"speeds_feeds.pdf\",\"results.xlsx\"]', 'featured', '1', '0', '1', '96', '0', '22', '39', '4.99', '12', '4.30', '1', '2025-06-20 18:06:44', '2025-06-19 18:06:44', '1', '2025-05-28 18:06:44', '2025-06-24 18:06:44');
INSERT INTO `showcases` VALUES ('3', '1', 'App\\Models\\Thread', '1', 'Dự án Test Swiper 3', 'du-an-test-swiper-3-1', 'Mô tả dự án test swiper số 3 để kiểm tra tính năng slide.', NULL, NULL, NULL, NULL, NULL, 'design', 'intermediate', NULL, '0', '0', '0', NULL, '/images/showcases/demo-3.jpg', NULL, NULL, 'featured', '1', '0', '1', '0', '0', '0', '0', '0.00', '0', '0.00', '0', NULL, NULL, NULL, '2025-07-03 22:18:51', '2025-07-04 00:23:21');
INSERT INTO `showcases` VALUES ('4', '2', 'App\\Models\\Thread', '2', 'Dự án Test Swiper 4', 'du-an-test-swiper-4-2', 'Mô tả dự án test swiper số 4 để kiểm tra tính năng slide.', NULL, NULL, NULL, NULL, NULL, 'analysis', 'intermediate', NULL, '0', '0', '0', NULL, '/images/showcases/demo-4.jpg', NULL, NULL, 'featured', '1', '0', '1', '0', '0', '0', '0', '0.00', '0', '0.00', '0', NULL, NULL, NULL, '2025-07-03 22:18:51', '2025-07-04 00:23:21');
INSERT INTO `showcases` VALUES ('5', '3', 'App\\Models\\Thread', '3', 'Dự án Test Swiper 5', 'du-an-test-swiper-5-3', 'Mô tả dự án test swiper số 5 để kiểm tra tính năng slide.', NULL, NULL, NULL, NULL, NULL, 'manufacturing', 'intermediate', NULL, '0', '0', '0', NULL, '/images/showcases/demo-5.jpg', NULL, NULL, 'featured', '1', '0', '1', '0', '0', '0', '0', '0.00', '0', '0.00', '0', NULL, NULL, NULL, '2025-07-03 22:18:51', '2025-07-04 00:23:21');

-- Structure for table `threads`
DROP TABLE IF EXISTS `threads`;
CREATE TABLE `threads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `content` text NOT NULL,
  `featured_image` varchar(191) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `search_keywords` text DEFAULT NULL,
  `read_time` int(11) DEFAULT NULL,
  `status` varchar(191) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `forum_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `is_sticky` tinyint(1) NOT NULL DEFAULT 0,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_solved` tinyint(1) NOT NULL DEFAULT 0,
  `solution_comment_id` bigint(20) unsigned DEFAULT NULL,
  `solved_at` timestamp NULL DEFAULT NULL,
  `solved_by` bigint(20) unsigned DEFAULT NULL,
  `quality_score` int(11) NOT NULL DEFAULT 0,
  `average_rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `ratings_count` int(11) NOT NULL DEFAULT 0,
  `thread_type` enum('discussion','question','tutorial','showcase','news','poll') NOT NULL DEFAULT 'discussion',
  `technical_difficulty` enum('beginner','intermediate','advanced','expert') DEFAULT NULL COMMENT 'Cấp độ kỹ thuật của chủ đề (beginner=sinh viên, expert=kỹ sư senior)',
  `project_type` enum('design','manufacturing','analysis','troubleshooting','maintenance','research','tutorial','case_study') DEFAULT NULL COMMENT 'Loại dự án/vấn đề: thiết kế, sản xuất, phân tích, xử lý sự cố',
  `software_used` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Phần mềm sử dụng: ["AutoCAD","SolidWorks","ANSYS","CATIA","Fusion360"]' CHECK (json_valid(`software_used`)),
  `industry_sector` enum('automotive','aerospace','manufacturing','energy','construction','marine','electronics','general') DEFAULT NULL COMMENT 'Lĩnh vực công nghiệp: ô tô, hàng không, sản xuất, năng lượng',
  `technical_specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Thông số kỹ thuật: {"material":"Steel","tolerance":"±0.01","pressure":"10MPa"}' CHECK (json_valid(`technical_specs`)),
  `requires_calculations` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Thread yêu cầu tính toán kỹ thuật (FEA, stress analysis, thermal)',
  `has_drawings` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Thread có kèm bản vẽ kỹ thuật (DWG, PDF, STEP)',
  `urgency_level` enum('low','normal','high','critical') NOT NULL DEFAULT 'normal' COMMENT 'Mức độ khẩn cấp: low=học tập, critical=sự cố sản xuất',
  `standards_compliance` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Tiêu chuẩn áp dụng: ["ASME","ISO","ASTM","JIS","DIN"]' CHECK (json_valid(`standards_compliance`)),
  `requires_pe_review` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Yêu cầu review từ Professional Engineer (PE license)',
  `has_cad_files` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Thread có file CAD đính kèm',
  `attachment_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Số lượng file đính kèm',
  `view_count` int(11) NOT NULL DEFAULT 0,
  `likes` int(11) NOT NULL DEFAULT 0,
  `bookmarks` int(11) NOT NULL DEFAULT 0,
  `shares` int(11) NOT NULL DEFAULT 0,
  `replies` int(11) NOT NULL DEFAULT 0,
  `last_comment_by` bigint(20) unsigned DEFAULT NULL,
  `bump_count` int(11) NOT NULL DEFAULT 0,
  `dislikes_count` int(11) NOT NULL DEFAULT 0,
  `bookmark_count` int(11) NOT NULL DEFAULT 0,
  `follow_count` int(11) NOT NULL DEFAULT 0,
  `share_count` int(11) NOT NULL DEFAULT 0,
  `cached_comments_count` int(11) NOT NULL DEFAULT 0,
  `cached_participants_count` int(11) NOT NULL DEFAULT 0,
  `attachment_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachment_types`)),
  `has_calculations` tinyint(1) NOT NULL DEFAULT 0,
  `has_3d_models` tinyint(1) NOT NULL DEFAULT 0,
  `expert_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verified_by` bigint(20) unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `technical_accuracy_score` decimal(3,2) NOT NULL DEFAULT 0.00,
  `technical_keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`technical_keywords`)),
  `related_standards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`related_standards`)),
  `flagged_at` timestamp NULL DEFAULT NULL,
  `last_comment_at` timestamp NULL DEFAULT NULL,
  `last_bump_at` timestamp NULL DEFAULT NULL,
  `moderation_status` enum('pending','approved','rejected','flagged') NOT NULL DEFAULT 'approved',
  `is_spam` tinyint(1) NOT NULL DEFAULT 0,
  `hidden_at` timestamp NULL DEFAULT NULL,
  `archived_at` timestamp NULL DEFAULT NULL,
  `moderated_by` bigint(20) unsigned DEFAULT NULL,
  `moderation_notes` text DEFAULT NULL,
  `moderated_at` timestamp NULL DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `bumped_at` timestamp NULL DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `threads_slug_unique` (`slug`),
  KEY `threads_forum_id_foreign` (`forum_id`),
  KEY `threads_solved_by_foreign` (`solved_by`),
  KEY `threads_category_id_status_created_at_index` (`category_id`,`status`,`created_at`),
  KEY `threads_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `threads_is_sticky_is_featured_created_at_index` (`is_sticky`,`is_featured`,`created_at`),
  KEY `threads_is_solved_solved_at_index` (`is_solved`,`solved_at`),
  KEY `threads_technical_classification` (`technical_difficulty`,`project_type`),
  KEY `threads_industry_professional` (`industry_sector`,`requires_pe_review`),
  KEY `threads_software_index` (`software_used`(768)),
  KEY `threads_urgency_timeline` (`urgency_level`,`created_at`),
  KEY `threads_technical_features` (`has_cad_files`,`requires_calculations`),
  KEY `threads_activity_popularity` (`last_activity_at`,`view_count`),
  KEY `threads_category_activity` (`category_id`,`is_sticky`,`last_activity_at`),
  KEY `threads_technical_timeline` (`project_type`,`technical_difficulty`,`created_at`),
  KEY `threads_moderated_by_foreign` (`moderated_by`),
  KEY `threads_last_comment_by_foreign` (`last_comment_by`),
  KEY `threads_verified_by_foreign` (`verified_by`),
  KEY `threads_moderation_status_index` (`moderation_status`),
  KEY `threads_is_spam_index` (`is_spam`),
  KEY `threads_hidden_at_index` (`hidden_at`),
  KEY `threads_archived_at_index` (`archived_at`),
  KEY `threads_solution_comment_id_foreign` (`solution_comment_id`),
  FULLTEXT KEY `threads_content_search` (`title`,`content`),
  FULLTEXT KEY `threads_title_content_fulltext` (`title`,`content`),
  CONSTRAINT `threads_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `threads_forum_id_foreign` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`id`) ON DELETE CASCADE,
  CONSTRAINT `threads_last_comment_by_foreign` FOREIGN KEY (`last_comment_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `threads_moderated_by_foreign` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `threads_solution_comment_id_foreign` FOREIGN KEY (`solution_comment_id`) REFERENCES `comments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `threads_solved_by_foreign` FOREIGN KEY (`solved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `threads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `threads_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data for table `threads`
INSERT INTO `threads` VALUES ('1', 'Cách tối ưu file SolidWorks để chạy nhanh hơn - 6 tips quan trọng', 'cach-toi-uu-file-solidworks-de-chay-nhanh-hon-6-tips-quan-trong-1-147', '
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
        ', '/images/threads/compressed_2151589656.jpg', '
# 6 Cách Tối Ưu File SolidWorks Để Chạy Nhanh Hơn

Khi làm việc với các file SolidWorks lớn, hiệu suất có thể bị ảnh hưởng đáng kể. Dưới đây là 6 tips quan trọ...', '\"[\\\"solidworks\\\",\\\"cad\\\",\\\"3d modeling\\\"]\"', '2', 'published', '34', '1', '1', '0', '0', '1', '0', NULL, NULL, NULL, '8', '4.70', '19', 'tutorial', 'intermediate', 'design', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'low', '[\"ISO\",\"ASME\"]', '1', '1', '0', '85', '27', '10', '1', '3', '7', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[\\\"solidworks\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-12 17:57:51', NULL, '4', '2025-04-25 18:44:59', '2025-07-09 11:12:24', NULL);
INSERT INTO `threads` VALUES ('2', 'Lỗi \"Sketch is open, self-intersecting\" trong SolidWorks - Cách khắc phục', 'loi-sketch-is-open-self-intersecting-trong-solidworks-cach-khac-phuc-1-881', '
# Khắc Phục Lỗi \'Sketch is open, self-intersecting\' Trong SolidWorks

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
        ', '/images/threads/success-story-schuetz-industrie-anlagenmechanikerin-2128x1330-c.jpg.webp', '
# Khắc Phục Lỗi \'Sketch is open, self-intersecting\' Trong SolidWorks

Đây là một trong những lỗi phổ biến nhất khi sử dụng **Revolved Boss/Base** feature. Hãy...', '\"[\\\"solidworks\\\",\\\"cad\\\",\\\"3d modeling\\\"]\"', '2', 'published', '29', '1', '1', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.00', '24', 'tutorial', 'intermediate', 'design', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'low', '[\"ISO\",\"ASME\"]', '0', '1', '1', '324', '36', '15', '0', '3', '23', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[\\\"solidworks\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-10 20:38:51', NULL, '3', '2025-05-05 01:47:51', '2025-05-05 01:47:51', NULL);
INSERT INTO `threads` VALUES ('3', 'So sánh IGES vs STEP - Format file CAD nào tốt hơn?', 'so-sanh-iges-vs-step-format-file-cad-nao-tot-hon-1-825', '
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
        ', '/images/threads/mechanical-mini-projects-cover-pic.webp', '
# So Sánh IGES vs STEP - Format File CAD Nào Tốt Hơn?

Khi trao đổi file CAD giữa các phần mềm khác nhau, việc chọn format phù hợp rất quan trọng. Hãy cùng so...', '\"[]\"', '3', 'published', '16', '1', '1', '1', '1', '1', '0', NULL, NULL, NULL, '9', '4.10', '19', 'question', 'intermediate', 'design', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'high', '[\"ISO\",\"ASME\"]', '1', '1', '3', '127', '37', '10', '1', '1', '14', '0', '0', '3', '2', '0', '1', '2', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '0', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-21 17:57:51', NULL, '2', '2025-06-11 17:31:27', '2025-07-05 11:14:50', NULL);
INSERT INTO `threads` VALUES ('4', 'SolidWorks Material Library - Hướng dẫn sử dụng chi tiết', 'solidworks-material-library-huong-dan-su-dung-chi-tiet-1-880', '
# SolidWorks Material Library - Hướng Dẫn Sử Dụng Chi Tiết

Material Library là tính năng mạnh mẽ của SolidWorks giúp quản lý và áp dụng vật liệu cho models.

## Truy Cập Material Library

### Cách 1: Feature Manager
```
1. Right-click trên part name
2. Chọn \'Edit Material\'
3. Material dialog sẽ mở
```

### Cách 2: Material Tab
```
1. Mở ConfigurationManager
2. Click tab \'Material\'
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
- **Poisson\'s Ratio**
- **Tensile Strength** (N/m²)
- **Thermal Properties**

## Tạo Custom Material

### Bước 1: Copy Existing Material
```
1. Right-click material tương tự
2. Chọn \'Copy\'
3. Paste vào Custom Materials
```

### Bước 2: Edit Properties
```
- Name: Thép CT3 Việt Nam
- Density: 7850 kg/m³
- Elastic Modulus: 2.1e11 N/m²
- Poisson\'s Ratio: 0.28
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
\' VBA example
Set swMaterial = swModel.GetMaterialPropertyName2(\"Default\")
```

Ai đã tạo custom materials chưa? Share materials hay ho nhé!
        ', '/images/threads/Mechanical_components.png', '
# SolidWorks Material Library - Hướng Dẫn Sử Dụng Chi Tiết

Material Library là tính năng mạnh mẽ của SolidWorks giúp quản lý và áp dụng vật liệu cho models....', '\"[\\\"solidworks\\\",\\\"cad\\\",\\\"3d modeling\\\"]\"', '3', 'published', '14', '1', '1', '0', '0', '0', '0', NULL, NULL, NULL, '7', '3.50', '18', 'tutorial', 'intermediate', 'design', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'high', '[\"ISO\",\"ASME\"]', '1', '1', '2', '391', '10', '18', '6', '4', '14', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[\\\"solidworks\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-24 17:57:51', NULL, '2', '2025-06-28 06:12:31', '2025-07-09 11:12:57', NULL);
INSERT INTO `threads` VALUES ('5', '10 cách thiết kế CAD model thân thiện với FEA', '10-cach-thiet-ke-cad-model-than-thien-voi-fea-2-276', '
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
        ', '/images/threads/Mechanical-Engineer-1-1024x536.webp', '
# 10 Cách Thiết Kế CAD Model Thân Thiện Với FEA

Finite Element Analysis (FEA) là bước quan trọng trong thiết kế. Tuy nhiên, không phải model CAD nào cũng phù...', '\"[]\"', '1', 'published', '22', '2', '1', '0', '0', '1', '0', NULL, NULL, NULL, '9', '3.60', '23', 'tutorial', 'intermediate', 'analysis', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'critical', '[\"ISO\",\"ASME\"]', '0', '0', '3', '141', '10', '12', '9', '3', '32', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"ANSYS\\\",\\\"PDF\\\",\\\"CSV\\\",\\\"STEP\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[\\\"fea\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-08 12:49:51', NULL, '1', '2025-06-28 10:06:36', '2025-06-28 15:12:53', NULL);
INSERT INTO `threads` VALUES ('6', 'ANSYS vs ABAQUS vs COMSOL - So sánh phần mềm FEA', 'ansys-vs-abaqus-vs-comsol-so-sanh-phan-mem-fea-2-847', '
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
        ', '/images/threads/program-mech-eng.jpg', '
# ANSYS vs ABAQUS vs COMSOL - So Sánh Phần Mềm FEA

Chọn phần mềm FEA phù hợp là quyết định quan trọng cho dự án simulation.

## ANSYS Workbench

### Ưu Điểm:...', '\"[\\\"ansys\\\",\\\"fea\\\",\\\"simulation\\\"]\"', '1', 'published', '14', '2', '1', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.50', '21', 'discussion', 'intermediate', 'analysis', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'high', '[\"ISO\",\"ASME\"]', '0', '0', '1', '314', '2', '20', '1', '3', '32', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"ANSYS\\\",\\\"PDF\\\",\\\"CSV\\\",\\\"STEP\\\"]\"', '0', '1', '1', NULL, NULL, '0.00', '\"[\\\"ansys\\\",\\\"fea\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-21 17:57:51', NULL, '3', '2025-06-08 03:30:13', '2025-06-08 03:30:13', NULL);
INSERT INTO `threads` VALUES ('7', 'Thảo luận về Thiết kế máy móc - Chia sẻ kinh nghiệm', 'thao-luan-ve-thiet-ke-may-moc-chia-se-kinh-nghiem-3-267', '
# Chào Mừng Đến Với Forum Thiết kế máy móc

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Thiết kế máy móc**.

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
        ', '/images/threads/male-asian-engineer-professional-having-discussion-standing-by-machine-factory-two-asian-coworker-brainstorm-explaining-solves-process-curcuit-mother-board-machine.webp', '
# Chào Mừng Đến Với Forum Thiết kế máy móc

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Thiết kế máy móc**.

## Mục Đích Forum...', '\"[]\"', '2', 'published', '5', '3', '1', '0', '0', '0', '0', NULL, NULL, NULL, '8', '3.80', '14', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'high', '[\"ISO\",\"ASME\"]', '1', '0', '1', '359', '29', '0', '7', '3', '5', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-18 17:57:51', NULL, '5', '2025-04-12 17:36:50', '2025-04-12 17:36:50', NULL);
INSERT INTO `threads` VALUES ('8', 'Hỏi đáp kỹ thuật về Thiết kế máy móc', 'hoi-dap-ky-thuat-ve-thiet-ke-may-moc-3-464', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Thiết kế máy móc

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Thiết kế máy móc.

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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Thiết kế máy móc

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Thiết kế máy móc.

## Cách Đặt Câu H...', '\"[]\"', '2', 'published', '19', '3', '1', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.50', '20', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'critical', '[\"ISO\",\"ASME\"]', '0', '0', '0', '14', '8', '5', '9', '1', '25', '0', '0', '0', '0', '0', '1', '2', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-25 17:57:51', NULL, '3', '2025-06-17 02:14:26', '2025-06-25 16:16:35', NULL);
INSERT INTO `threads` VALUES ('9', 'Thảo luận về Bản vẽ kỹ thuật - Chia sẻ kinh nghiệm', 'thao-luan-ve-ban-ve-ky-thuat-chia-se-kinh-nghiem-4-843', '
# Chào Mừng Đến Với Forum Bản vẽ kỹ thuật

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Bản vẽ kỹ thuật**.

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
        ', '/images/threads/mechanical-engineering-la-gi-7.webp', '
# Chào Mừng Đến Với Forum Bản vẽ kỹ thuật

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Bản vẽ kỹ thuật**.

## Mục Đích Forum
-...', '\"[]\"', '2', 'published', '11', '4', '1', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.70', '17', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'low', '[\"ISO\",\"ASME\"]', '0', '1', '2', '223', '28', '6', '7', '4', '23', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-23 17:57:51', NULL, '1', '2025-06-15 12:20:56', '2025-06-15 12:20:56', NULL);
INSERT INTO `threads` VALUES ('10', 'Hỏi đáp kỹ thuật về Bản vẽ kỹ thuật', 'hoi-dap-ky-thuat-ve-ban-ve-ky-thuat-4-740', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Bản vẽ kỹ thuật

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Bản vẽ kỹ thuật.

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
        ', '/images/threads/program-mech-eng.jpg', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Bản vẽ kỹ thuật

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Bản vẽ kỹ thuật.

## Cách Đặt Câu Hỏi...', '\"[]\"', '2', 'published', '4', '4', '1', '0', '0', '0', '0', NULL, NULL, NULL, '9', '3.80', '18', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'low', '[\"ISO\",\"ASME\"]', '1', '1', '3', '65', '50', '4', '0', '6', '9', '0', '0', '0', '0', '0', '6', '7', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-21 13:40:51', NULL, '4', '2025-06-29 07:48:04', '2025-07-09 02:29:45', NULL);
INSERT INTO `threads` VALUES ('11', 'Thảo luận về SolidWorks - Chia sẻ kinh nghiệm', 'thao-luan-ve-solidworks-chia-se-kinh-nghiem-5-112', '
# Chào Mừng Đến Với Forum SolidWorks

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **SolidWorks**.

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
        ', '/images/threads/male-worker-factory.webp', '
# Chào Mừng Đến Với Forum SolidWorks

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **SolidWorks**.

## Mục Đích Forum
- Chia sẻ k...', '\"[\\\"solidworks\\\",\\\"cad\\\",\\\"3d modeling\\\"]\"', '1', 'published', '11', '5', '2', '0', '0', '0', '0', NULL, NULL, NULL, '7', '5.00', '18', 'discussion', 'intermediate', 'design', '\"[\\\"SolidWorks\\\"]\"', 'manufacturing', '\"null\"', '0', '0', 'low', '[\"ISO\",\"ASME\"]', '0', '1', '3', '500', '36', '2', '2', '4', '2', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[\\\"solidworks\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-23 05:27:51', NULL, '3', '2025-06-26 21:59:51', '2025-06-26 21:59:51', NULL);
INSERT INTO `threads` VALUES ('12', 'Hỏi đáp kỹ thuật về SolidWorks', 'hoi-dap-ky-thuat-ve-solidworks-5-463', '
# Q&A - Hỏi Đáp Kỹ Thuật Về SolidWorks

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến SolidWorks.

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
        ', '/images/threads/male-worker-factory.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về SolidWorks

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến SolidWorks.

## Cách Đặt Câu Hỏi Hiệu Quả...', '\"[\\\"solidworks\\\",\\\"cad\\\",\\\"3d modeling\\\"]\"', '2', 'published', '4', '5', '2', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.10', '14', 'question', 'intermediate', 'design', '\"[\\\"SolidWorks\\\"]\"', 'manufacturing', '\"null\"', '0', '1', 'low', '[\"ISO\",\"ASME\"]', '1', '0', '1', '72', '49', '16', '1', '4', '27', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[\\\"solidworks\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-13 10:49:51', NULL, '1', '2025-06-24 18:09:33', '2025-06-24 18:09:33', NULL);
INSERT INTO `threads` VALUES ('13', 'Cách tối ưu file SolidWorks để chạy nhanh hơn - 6 tips quan trọng', 'cach-toi-uu-file-solidworks-de-chay-nhanh-hon-6-tips-quan-trong-6-421', '
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
        ', '/images/threads/mechanical-update_0.jpg', '
# 6 Cách Tối Ưu File SolidWorks Để Chạy Nhanh Hơn

Khi làm việc với các file SolidWorks lớn, hiệu suất có thể bị ảnh hưởng đáng kể. Dưới đây là 6 tips quan trọ...', '\"[\\\"solidworks\\\",\\\"cad\\\",\\\"3d modeling\\\"]\"', '2', 'published', '2', '6', '2', '0', '0', '1', '0', NULL, NULL, NULL, '9', '3.80', '23', 'tutorial', 'intermediate', 'design', '\"[\\\"AutoCAD\\\"]\"', 'manufacturing', '\"null\"', '0', '1', 'low', '[\"ISO\",\"ASME\"]', '1', '0', '1', '394', '11', '15', '4', '4', '32', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '0', '1', '1', NULL, NULL, '0.00', '\"[\\\"solidworks\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-23 17:57:51', NULL, '2', '2025-04-05 09:55:52', '2025-04-05 09:55:52', NULL);
INSERT INTO `threads` VALUES ('14', 'Lỗi \"Sketch is open, self-intersecting\" trong SolidWorks - Cách khắc phục', 'loi-sketch-is-open-self-intersecting-trong-solidworks-cach-khac-phuc-6-584', '
# Khắc Phục Lỗi \'Sketch is open, self-intersecting\' Trong SolidWorks

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
        ', '/images/threads/images.jpg', '
# Khắc Phục Lỗi \'Sketch is open, self-intersecting\' Trong SolidWorks

Đây là một trong những lỗi phổ biến nhất khi sử dụng **Revolved Boss/Base** feature. Hãy...', '\"[\\\"solidworks\\\",\\\"cad\\\",\\\"3d modeling\\\"]\"', '2', 'published', '26', '6', '2', '0', '0', '0', '0', NULL, NULL, NULL, '9', '5.00', '25', 'tutorial', 'intermediate', 'design', '\"[\\\"AutoCAD\\\"]\"', 'manufacturing', '\"null\"', '1', '0', 'normal', '[\"ISO\",\"ASME\"]', '1', '0', '0', '454', '21', '3', '4', '3', '4', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '1', '0', '1', NULL, NULL, '0.00', '\"[\\\"solidworks\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-20 17:57:51', NULL, '1', '2025-05-19 09:56:31', '2025-06-30 16:01:40', NULL);
INSERT INTO `threads` VALUES ('15', 'So sánh IGES vs STEP - Format file CAD nào tốt hơn?', 'so-sanh-iges-vs-step-format-file-cad-nao-tot-hon-6-728', '
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
        ', '/images/threads/images.jpg', '
# So Sánh IGES vs STEP - Format File CAD Nào Tốt Hơn?

Khi trao đổi file CAD giữa các phần mềm khác nhau, việc chọn format phù hợp rất quan trọng. Hãy cùng so...', '\"[]\"', '3', 'published', '5', '6', '2', '0', '0', '1', '0', NULL, NULL, NULL, '9', '4.40', '22', 'question', 'intermediate', 'design', '\"[\\\"AutoCAD\\\"]\"', 'manufacturing', '\"null\"', '1', '1', 'normal', '[\"ISO\",\"ASME\"]', '0', '0', '1', '275', '14', '10', '9', '3', '12', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-05 00:16:51', NULL, '5', '2025-06-17 06:01:01', '2025-06-20 18:10:29', NULL);
INSERT INTO `threads` VALUES ('16', 'SolidWorks Material Library - Hướng dẫn sử dụng chi tiết', 'solidworks-material-library-huong-dan-su-dung-chi-tiet-6-239', '
# SolidWorks Material Library - Hướng Dẫn Sử Dụng Chi Tiết

Material Library là tính năng mạnh mẽ của SolidWorks giúp quản lý và áp dụng vật liệu cho models.

## Truy Cập Material Library

### Cách 1: Feature Manager
```
1. Right-click trên part name
2. Chọn \'Edit Material\'
3. Material dialog sẽ mở
```

### Cách 2: Material Tab
```
1. Mở ConfigurationManager
2. Click tab \'Material\'
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
- **Poisson\'s Ratio**
- **Tensile Strength** (N/m²)
- **Thermal Properties**

## Tạo Custom Material

### Bước 1: Copy Existing Material
```
1. Right-click material tương tự
2. Chọn \'Copy\'
3. Paste vào Custom Materials
```

### Bước 2: Edit Properties
```
- Name: Thép CT3 Việt Nam
- Density: 7850 kg/m³
- Elastic Modulus: 2.1e11 N/m²
- Poisson\'s Ratio: 0.28
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
\' VBA example
Set swMaterial = swModel.GetMaterialPropertyName2(\"Default\")
```

Ai đã tạo custom materials chưa? Share materials hay ho nhé!
        ', '/images/threads/images.jpg', '
# SolidWorks Material Library - Hướng Dẫn Sử Dụng Chi Tiết

Material Library là tính năng mạnh mẽ của SolidWorks giúp quản lý và áp dụng vật liệu cho models....', '\"[\\\"solidworks\\\",\\\"cad\\\",\\\"3d modeling\\\"]\"', '3', 'published', '12', '6', '2', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.20', '20', 'tutorial', 'intermediate', 'design', '\"[\\\"AutoCAD\\\"]\"', 'manufacturing', '\"null\"', '1', '1', 'low', '[\"ISO\",\"ASME\"]', '1', '1', '2', '71', '15', '14', '5', '3', '4', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"STEP\\\",\\\"IGES\\\",\\\"DWG\\\",\\\"PDF\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[\\\"solidworks\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-15 17:57:51', NULL, '3', '2025-05-12 15:22:41', '2025-05-14 07:01:03', NULL);
INSERT INTO `threads` VALUES ('17', 'Thảo luận về Inventor - Chia sẻ kinh nghiệm', 'thao-luan-ve-inventor-chia-se-kinh-nghiem-7-186', '
# Chào Mừng Đến Với Forum Inventor

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Inventor**.

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
        ', '/images/threads/mechanical-engineering-la-gi-7.webp', '
# Chào Mừng Đến Với Forum Inventor

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Inventor**.

## Mục Đích Forum
- Chia sẻ kiến...', '\"[]\"', '1', 'published', '24', '7', '2', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.20', '14', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'low', '[\"ISO\",\"ASME\"]', '0', '0', '2', '358', '7', '3', '9', '5', '32', '0', '0', '0', '0', '0', '5', '6', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-15 05:58:51', NULL, '5', '2025-06-22 01:27:27', '2025-06-29 20:51:32', NULL);
INSERT INTO `threads` VALUES ('18', 'Hỏi đáp kỹ thuật về Inventor', 'hoi-dap-ky-thuat-ve-inventor-7-657', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Inventor

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Inventor.

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
        ', '/images/threads/success-story-schuetz-industrie-anlagenmechanikerin-2128x1330-c.jpg.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Inventor

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Inventor.

## Cách Đặt Câu Hỏi Hiệu Quả

###...', '\"[]\"', '2', 'published', '33', '7', '2', '0', '0', '0', '0', NULL, NULL, NULL, '8', '3.90', '24', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'high', '[\"ISO\",\"ASME\"]', '0', '1', '1', '250', '30', '2', '4', '3', '19', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-25 17:57:51', NULL, '1', '2025-05-15 09:50:46', '2025-05-19 07:04:23', NULL);
INSERT INTO `threads` VALUES ('19', 'Thảo luận về Fusion 360 - Chia sẻ kinh nghiệm', 'thao-luan-ve-fusion-360-chia-se-kinh-nghiem-8-812', '
# Chào Mừng Đến Với Forum Fusion 360

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Fusion 360**.

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
        ', '/images/threads/male-worker-factory.webp', '
# Chào Mừng Đến Với Forum Fusion 360

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Fusion 360**.

## Mục Đích Forum
- Chia sẻ k...', '\"[]\"', '1', 'published', '22', '8', '2', '0', '0', '0', '0', NULL, NULL, NULL, '9', '3.50', '21', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'high', '[\"ISO\",\"ASME\"]', '0', '0', '0', '50', '8', '9', '2', '3', '12', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-11 17:57:51', NULL, '1', '2025-06-29 13:26:42', '2025-06-29 13:26:42', NULL);
INSERT INTO `threads` VALUES ('20', 'Hỏi đáp kỹ thuật về Fusion 360', 'hoi-dap-ky-thuat-ve-fusion-360-8-873', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Fusion 360

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Fusion 360.

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
        ', '/images/threads/program-mech-eng.jpg', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Fusion 360

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Fusion 360.

## Cách Đặt Câu Hỏi Hiệu Quả...', '\"[]\"', '2', 'published', '27', '8', '2', '0', '0', '0', '0', NULL, NULL, NULL, '8', '3.90', '21', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'high', '[\"ISO\",\"ASME\"]', '1', '1', '1', '240', '50', '2', '6', '4', '23', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-19 18:33:51', NULL, '3', '2025-05-22 20:15:50', '2025-05-22 20:15:50', NULL);
INSERT INTO `threads` VALUES ('21', 'Thảo luận về ANSYS - Chia sẻ kinh nghiệm', 'thao-luan-ve-ansys-chia-se-kinh-nghiem-9-434', '
# Chào Mừng Đến Với Forum ANSYS

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **ANSYS**.

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
        ', '/images/threads/male-worker-factory.webp', '
# Chào Mừng Đến Với Forum ANSYS

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **ANSYS**.

## Mục Đích Forum
- Chia sẻ kiến thức c...', '\"[\\\"ansys\\\",\\\"fea\\\",\\\"simulation\\\"]\"', '1', 'published', '30', '9', '3', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.30', '8', 'discussion', 'intermediate', 'analysis', '\"[\\\"ANSYS\\\"]\"', 'manufacturing', '\"null\"', '0', '1', 'normal', '[\"ISO\",\"ASME\"]', '0', '1', '0', '173', '19', '5', '2', '2', '8', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"ANSYS\\\",\\\"PDF\\\",\\\"CSV\\\",\\\"STEP\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[\\\"ansys\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-23 17:57:51', NULL, '4', '2025-05-24 15:26:55', '2025-05-24 15:26:55', NULL);
INSERT INTO `threads` VALUES ('22', 'Hỏi đáp kỹ thuật về ANSYS', 'hoi-dap-ky-thuat-ve-ansys-9-448', '
# Q&A - Hỏi Đáp Kỹ Thuật Về ANSYS

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến ANSYS.

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
        ', '/images/threads/Mechanical_components.png', '
# Q&A - Hỏi Đáp Kỹ Thuật Về ANSYS

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến ANSYS.

## Cách Đặt Câu Hỏi Hiệu Quả

### 1. Ti...', '\"[\\\"ansys\\\",\\\"fea\\\",\\\"simulation\\\"]\"', '2', 'published', '9', '9', '3', '0', '0', '0', '0', NULL, NULL, NULL, '7', '4.00', '10', 'question', 'intermediate', 'analysis', '\"[\\\"ANSYS\\\"]\"', 'manufacturing', '\"null\"', '1', '1', 'normal', '[\"ISO\",\"ASME\"]', '1', '0', '2', '346', '6', '1', '6', '2', '9', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"ANSYS\\\",\\\"PDF\\\",\\\"CSV\\\",\\\"STEP\\\"]\"', '1', '0', '1', NULL, NULL, '0.00', '\"[\\\"ansys\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-17 17:57:51', NULL, '3', '2025-04-05 05:55:08', '2025-04-05 05:55:08', NULL);
INSERT INTO `threads` VALUES ('23', 'Thảo luận về ABAQUS - Chia sẻ kinh nghiệm', 'thao-luan-ve-abaqus-chia-se-kinh-nghiem-10-845', '
# Chào Mừng Đến Với Forum ABAQUS

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **ABAQUS**.

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
        ', '/images/threads/man-woman-engineering-computer-mechanical.jpg', '
# Chào Mừng Đến Với Forum ABAQUS

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **ABAQUS**.

## Mục Đích Forum
- Chia sẻ kiến thức...', '\"[]\"', '1', 'published', '25', '10', '3', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.70', '16', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'critical', '[\"ISO\",\"ASME\"]', '0', '0', '1', '245', '41', '6', '6', '4', '33', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-18 17:57:51', NULL, '5', '2025-06-20 21:15:25', '2025-06-20 21:15:25', NULL);
INSERT INTO `threads` VALUES ('24', 'Hỏi đáp kỹ thuật về ABAQUS', 'hoi-dap-ky-thuat-ve-abaqus-10-172', '
# Q&A - Hỏi Đáp Kỹ Thuật Về ABAQUS

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến ABAQUS.

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
        ', '/images/threads/success-story-schuetz-industrie-anlagenmechanikerin-2128x1330-c.jpg.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về ABAQUS

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến ABAQUS.

## Cách Đặt Câu Hỏi Hiệu Quả

### 1....', '\"[]\"', '2', 'published', '32', '10', '3', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.30', '5', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'high', '[\"ISO\",\"ASME\"]', '0', '1', '0', '246', '37', '13', '1', '3', '13', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-07 17:57:51', NULL, '4', '2025-06-04 10:34:54', '2025-06-04 10:34:54', NULL);
INSERT INTO `threads` VALUES ('25', 'Thảo luận về COMSOL - Chia sẻ kinh nghiệm', 'thao-luan-ve-comsol-chia-se-kinh-nghiem-11-189', '
# Chào Mừng Đến Với Forum COMSOL

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **COMSOL**.

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
        ', '/images/threads/male-worker-factory.webp', '
# Chào Mừng Đến Với Forum COMSOL

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **COMSOL**.

## Mục Đích Forum
- Chia sẻ kiến thức...', '\"[]\"', '1', 'published', '5', '11', '3', '0', '0', '0', '0', NULL, NULL, NULL, '9', '3.60', '21', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'low', '[\"ISO\",\"ASME\"]', '1', '0', '1', '447', '33', '12', '7', '2', '24', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-17 17:57:51', NULL, '2', '2025-04-18 15:13:52', '2025-05-28 21:04:00', NULL);
INSERT INTO `threads` VALUES ('26', 'Hỏi đáp kỹ thuật về COMSOL', 'hoi-dap-ky-thuat-ve-comsol-11-856', '
# Q&A - Hỏi Đáp Kỹ Thuật Về COMSOL

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến COMSOL.

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
        ', '/images/threads/mechanical-update_0.jpg', '
# Q&A - Hỏi Đáp Kỹ Thuật Về COMSOL

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến COMSOL.

## Cách Đặt Câu Hỏi Hiệu Quả

### 1....', '\"[]\"', '2', 'published', '18', '11', '3', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.40', '17', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'low', '[\"ISO\",\"ASME\"]', '0', '0', '0', '392', '47', '17', '7', '3', '14', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-21 17:57:51', NULL, '4', '2025-06-03 10:59:28', '2025-06-22 18:47:37', NULL);
INSERT INTO `threads` VALUES ('27', 'Thảo luận về Thảo luận chung - Thiết kế máy móc - Chia sẻ kinh nghiệm', 'thao-luan-ve-thao-luan-chung-thiet-ke-may-moc-chia-se-kinh-nghiem-12-419', '
# Chào Mừng Đến Với Forum Thảo luận chung - Thiết kế máy móc

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Thảo luận chung - Thiết kế máy móc**.

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
        ', '/images/threads/Mechanical-Engineer-1-1024x536.webp', '
# Chào Mừng Đến Với Forum Thảo luận chung - Thiết kế máy móc

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Thảo luận chung - Th...', '\"[]\"', '2', 'published', '14', '12', '4', '0', '0', '0', '0', NULL, NULL, NULL, '8', '3.50', '18', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'normal', '[\"ISO\",\"ASME\"]', '1', '1', '0', '171', '41', '4', '5', '3', '23', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:51', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-22 17:57:51', NULL, '2', '2025-06-25 14:14:59', '2025-06-28 19:47:36', NULL);
INSERT INTO `threads` VALUES ('28', 'Hỏi đáp kỹ thuật về Thảo luận chung - Thiết kế máy móc', 'hoi-dap-ky-thuat-ve-thao-luan-chung-thiet-ke-may-moc-12-332', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Thảo luận chung - Thiết kế máy móc

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Thảo luận chung - Thiết kế máy móc.

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
        ', '/images/threads/images.jpg', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Thảo luận chung - Thiết kế máy móc

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Thảo luận chung -...', '\"[]\"', '2', 'published', '5', '12', '4', '0', '0', '0', '0', NULL, NULL, NULL, '7', '4.10', '13', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'critical', '[\"ISO\",\"ASME\"]', '1', '1', '3', '276', '14', '8', '3', '4', '20', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-21 17:57:51', NULL, '3', '2025-05-06 09:19:27', '2025-05-06 09:19:27', NULL);
INSERT INTO `threads` VALUES ('29', 'Thảo luận về Hỏi đáp - Thiết kế máy móc - Chia sẻ kinh nghiệm', 'thao-luan-ve-hoi-dap-thiet-ke-may-moc-chia-se-kinh-nghiem-13-913', '
# Chào Mừng Đến Với Forum Hỏi đáp - Thiết kế máy móc

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Hỏi đáp - Thiết kế máy móc**.

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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# Chào Mừng Đến Với Forum Hỏi đáp - Thiết kế máy móc

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Hỏi đáp - Thiết kế máy móc**...', '\"[]\"', '2', 'published', '2', '13', '4', '0', '0', '0', '0', NULL, NULL, NULL, '7', '4.40', '7', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'critical', '[\"ISO\",\"ASME\"]', '1', '1', '2', '371', '28', '17', '8', '4', '6', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-08 04:05:52', NULL, '3', '2025-06-23 18:13:16', '2025-06-23 18:13:16', NULL);
INSERT INTO `threads` VALUES ('30', 'Hỏi đáp kỹ thuật về Hỏi đáp - Thiết kế máy móc', 'hoi-dap-ky-thuat-ve-hoi-dap-thiet-ke-may-moc-13-524', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Hỏi đáp - Thiết kế máy móc

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Hỏi đáp - Thiết kế máy móc.

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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Hỏi đáp - Thiết kế máy móc

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Hỏi đáp - Thiết kế máy móc...', '\"[]\"', '2', 'published', '34', '13', '4', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.80', '22', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'critical', '[\"ISO\",\"ASME\"]', '1', '0', '3', '15', '14', '11', '6', '2', '13', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-10 17:57:52', NULL, '1', '2025-02-26 04:15:05', '2025-02-26 04:15:05', NULL);
INSERT INTO `threads` VALUES ('31', 'Thảo luận về Kinh nghiệm - Thiết kế máy móc - Chia sẻ kinh nghiệm', 'thao-luan-ve-kinh-nghiem-thiet-ke-may-moc-chia-se-kinh-nghiem-14-253', '
# Chào Mừng Đến Với Forum Kinh nghiệm - Thiết kế máy móc

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Kinh nghiệm - Thiết kế máy móc**.

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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# Chào Mừng Đến Với Forum Kinh nghiệm - Thiết kế máy móc

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Kinh nghiệm - Thiết kế m...', '\"[]\"', '2', 'published', '5', '14', '4', '0', '0', '0', '0', NULL, NULL, NULL, '9', '3.70', '21', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'critical', '[\"ISO\",\"ASME\"]', '0', '1', '0', '484', '45', '20', '8', '1', '11', '0', '0', '0', '0', '0', '1', '2', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-16 17:57:52', NULL, '1', '2025-05-29 23:03:44', '2025-05-29 23:03:44', NULL);
INSERT INTO `threads` VALUES ('32', 'Hỏi đáp kỹ thuật về Kinh nghiệm - Thiết kế máy móc', 'hoi-dap-ky-thuat-ve-kinh-nghiem-thiet-ke-may-moc-14-723', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Kinh nghiệm - Thiết kế máy móc

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Kinh nghiệm - Thiết kế máy móc.

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
        ', '/images/threads/Mechanical_components.png', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Kinh nghiệm - Thiết kế máy móc

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Kinh nghiệm - Thiết kế...', '\"[]\"', '2', 'published', '20', '14', '4', '0', '0', '0', '0', NULL, NULL, NULL, '8', '3.60', '25', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'low', '[\"ISO\",\"ASME\"]', '1', '1', '3', '245', '8', '7', '8', '3', '18', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-22 17:57:52', NULL, '4', '2025-05-09 00:33:49', '2025-05-09 00:33:49', NULL);
INSERT INTO `threads` VALUES ('33', 'Lập trình CNC 3 trục với Mastercam - Kinh nghiệm từ thực tế', 'lap-trinh-cnc-3-truc-voi-mastercam-kinh-nghiem-tu-thuc-te-15-288', '
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
        ', '/images/threads/mechanical-mini-projects-cover-pic.webp', '
# Lập Trình CNC 3 Trục Với Mastercam - Kinh Nghiệm Thực Tế

Sau 10 năm làm việc với CNC, tôi muốn chia sẻ những kinh nghiệm thực tế trong lập trình Mastercam....', '\"[\\\"cnc\\\",\\\"machining\\\",\\\"manufacturing\\\",\\\"mastercam\\\",\\\"cam\\\",\\\"toolpath\\\"]\"', '3', 'published', '25', '15', '5', '0', '0', '1', '0', NULL, NULL, NULL, '9', '4.20', '22', 'discussion', 'intermediate', 'manufacturing', '\"null\"', 'manufacturing', '\"{\\\"tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"surface_finish\\\":\\\"Ra 1.6\\\",\\\"material_removal_rate\\\":\\\"50 cm\\\\u00b3\\\\\\/min\\\"}\"', '0', '1', 'low', '[\"ISO 2768\",\"ASME Y14.5\"]', '0', '0', '3', '208', '46', '19', '10', '1', '5', '0', '0', '0', '0', '0', '1', '2', '\"[\\\"NC\\\",\\\"MCX\\\",\\\"PDF\\\",\\\"STEP\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[\\\"mastercam\\\",\\\"cnc\\\"]\"', '\"[\\\"ISO 2768-1\\\",\\\"ASME Y14.5\\\",\\\"DIN 6930\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-19 17:57:52', NULL, '3', '2025-06-05 20:12:18', '2025-06-05 20:12:18', NULL);
INSERT INTO `threads` VALUES ('34', 'Chọn dao phay phù hợp cho từng loại vật liệu - Bảng tra cứu', 'chon-dao-phay-phu-hop-cho-tung-loai-vat-lieu-bang-tra-cuu-15-446', '
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
        ', '/images/threads/Professional Engineer.webp', '
# Chọn Dao Phay Phù Hợp Cho Từng Loại Vật Liệu

Việc chọn dao phay đúng là yếu tố quyết định chất lượng gia công và tuổi thọ dao.

## Bảng Tra Cứu Nhanh

### A...', '\"[]\"', '3', 'published', '9', '15', '5', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.70', '20', 'discussion', 'intermediate', 'manufacturing', '\"null\"', 'manufacturing', '\"{\\\"tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"surface_finish\\\":\\\"Ra 1.6\\\",\\\"material_removal_rate\\\":\\\"50 cm\\\\u00b3\\\\\\/min\\\"}\"', '0', '0', 'low', '[\"ISO 2768\",\"ASME Y14.5\"]', '0', '1', '3', '339', '34', '15', '3', '4', '30', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"NC\\\",\\\"MCX\\\",\\\"PDF\\\",\\\"STEP\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 2768-1\\\",\\\"ASME Y14.5\\\",\\\"DIN 6930\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-11 05:18:52', NULL, '2', '2025-04-03 16:37:54', '2025-05-29 09:13:28', NULL);
INSERT INTO `threads` VALUES ('35', 'Post Processor là gì? Cách cài đặt và sử dụng trong Mastercam', 'post-processor-la-gi-cach-cai-dat-va-su-dung-trong-mastercam-15-667', '
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
        ', '/images/threads/success-story-schuetz-industrie-anlagenmechanikerin-2128x1330-c.jpg.webp', '
# Post Processor Là Gì? Cách Cài Đặt Và Sử Dụng Trong Mastercam

Post Processor (PP) là cầu nối quan trọng giữa CAM software và máy CNC.

## Post Processor Là...', '\"[\\\"mastercam\\\",\\\"cam\\\",\\\"toolpath\\\"]\"', '3', 'published', '33', '15', '5', '0', '0', '1', '0', NULL, NULL, NULL, '8', '3.60', '22', 'question', 'intermediate', 'manufacturing', '\"null\"', 'manufacturing', '\"{\\\"tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"surface_finish\\\":\\\"Ra 1.6\\\",\\\"material_removal_rate\\\":\\\"50 cm\\\\u00b3\\\\\\/min\\\"}\"', '0', '1', 'critical', '[\"ISO 2768\",\"ASME Y14.5\"]', '0', '1', '3', '499', '6', '20', '2', '3', '26', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"NC\\\",\\\"MCX\\\",\\\"PDF\\\",\\\"STEP\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[\\\"mastercam\\\"]\"', '\"[\\\"ISO 2768-1\\\",\\\"ASME Y14.5\\\",\\\"DIN 6930\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-19 13:30:52', NULL, '2', '2025-03-30 20:20:56', '2025-04-12 19:36:37', NULL);
INSERT INTO `threads` VALUES ('36', 'Thảo luận về Gia công truyền thống - Chia sẻ kinh nghiệm', 'thao-luan-ve-gia-cong-truyen-thong-chia-se-kinh-nghiem-16-320', '
# Chào Mừng Đến Với Forum Gia công truyền thống

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Gia công truyền thống**.

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
        ', '/images/threads/mechanical-mini-projects-cover-pic.webp', '
# Chào Mừng Đến Với Forum Gia công truyền thống

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Gia công truyền thống**.

## Mục...', '\"[]\"', '2', 'published', '7', '16', '5', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.50', '7', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'high', '[\"ISO\",\"ASME\"]', '0', '1', '1', '136', '29', '9', '3', '3', '10', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-14 17:57:52', NULL, '4', '2025-05-20 23:03:07', '2025-05-30 03:55:10', NULL);
INSERT INTO `threads` VALUES ('37', 'Hỏi đáp kỹ thuật về Gia công truyền thống', 'hoi-dap-ky-thuat-ve-gia-cong-truyen-thong-16-746', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Gia công truyền thống

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Gia công truyền thống.

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
        ', '/images/threads/compressed_2151589656.jpg', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Gia công truyền thống

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Gia công truyền thống.

## Cách...', '\"[]\"', '2', 'published', '5', '16', '5', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.10', '24', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'high', '[\"ISO\",\"ASME\"]', '1', '1', '1', '416', '17', '8', '0', '4', '5', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-21 17:57:52', NULL, '3', '2025-05-10 12:30:13', '2025-05-11 23:03:11', NULL);
INSERT INTO `threads` VALUES ('38', 'Thảo luận về In 3D & Additive Manufacturing - Chia sẻ kinh nghiệm', 'thao-luan-ve-in-3d-additive-manufacturing-chia-se-kinh-nghiem-17-853', '
# Chào Mừng Đến Với Forum In 3D & Additive Manufacturing

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **In 3D & Additive Manufacturing**.

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
        ', '/images/threads/Mechanical-Engineering-thumbnail.jpg', '
# Chào Mừng Đến Với Forum In 3D & Additive Manufacturing

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **In 3D & Additive Manufac...', '\"[]\"', '2', 'published', '28', '17', '5', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.90', '25', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'critical', '[\"ISO\",\"ASME\"]', '1', '0', '0', '282', '24', '0', '1', '4', '19', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-21 17:57:52', NULL, '3', '2025-05-12 13:54:01', '2025-05-24 21:52:56', NULL);
INSERT INTO `threads` VALUES ('39', 'Hỏi đáp kỹ thuật về In 3D & Additive Manufacturing', 'hoi-dap-ky-thuat-ve-in-3d-additive-manufacturing-17-124', '
# Q&A - Hỏi Đáp Kỹ Thuật Về In 3D & Additive Manufacturing

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến In 3D & Additive Manufacturing.

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
        ', '/images/threads/male-asian-engineer-professional-having-discussion-standing-by-machine-factory-two-asian-coworker-brainstorm-explaining-solves-process-curcuit-mother-board-machine.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về In 3D & Additive Manufacturing

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến In 3D & Additive Manuf...', '\"[]\"', '2', 'published', '25', '17', '5', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.20', '8', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'high', '[\"ISO\",\"ASME\"]', '1', '0', '1', '475', '19', '3', '4', '4', '31', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-18 17:57:52', NULL, '5', '2025-06-08 02:29:33', '2025-07-08 13:49:06', NULL);
INSERT INTO `threads` VALUES ('40', 'Thảo luận về Đồ gá & Fixture - Chia sẻ kinh nghiệm', 'thao-luan-ve-do-ga-fixture-chia-se-kinh-nghiem-18-239', '
# Chào Mừng Đến Với Forum Đồ gá & Fixture

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Đồ gá & Fixture**.

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
        ', '/images/threads/mj_11351_4.jpg', '
# Chào Mừng Đến Với Forum Đồ gá & Fixture

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Đồ gá & Fixture**.

## Mục Đích Forum
-...', '\"[]\"', '1', 'published', '9', '18', '5', '0', '0', '0', '0', NULL, NULL, NULL, '9', '3.80', '11', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'low', '[\"ISO\",\"ASME\"]', '1', '1', '2', '477', '15', '12', '3', '3', '1', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-14 17:57:52', NULL, '3', '2025-05-03 03:54:10', '2025-06-05 10:33:46', NULL);
INSERT INTO `threads` VALUES ('41', 'Hỏi đáp kỹ thuật về Đồ gá & Fixture', 'hoi-dap-ky-thuat-ve-do-ga-fixture-18-190', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Đồ gá & Fixture

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Đồ gá & Fixture.

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
        ', '/images/threads/Mechanical_components.png', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Đồ gá & Fixture

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Đồ gá & Fixture.

## Cách Đặt Câu Hỏi...', '\"[]\"', '2', 'published', '22', '18', '5', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.70', '16', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'high', '[\"ISO\",\"ASME\"]', '0', '0', '2', '353', '43', '8', '5', '4', '1', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-24 17:57:52', NULL, '1', '2025-06-16 11:46:12', '2025-07-08 14:00:21', NULL);
INSERT INTO `threads` VALUES ('42', 'Cách tối ưu file SolidWorks để chạy nhanh hơn - 6 tips quan trọng', 'cach-toi-uu-file-solidworks-de-chay-nhanh-hon-6-tips-quan-trong-19-203', '
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
        ', '/images/threads/Mechanical-Engineering-thumbnail.jpg', '
# 6 Cách Tối Ưu File SolidWorks Để Chạy Nhanh Hơn

Khi làm việc với các file SolidWorks lớn, hiệu suất có thể bị ảnh hưởng đáng kể. Dưới đây là 6 tips quan trọ...', '\"[\\\"solidworks\\\",\\\"cad\\\",\\\"3d modeling\\\"]\"', '2', 'published', '11', '19', '6', '0', '0', '1', '0', NULL, NULL, NULL, '9', '5.00', '17', 'tutorial', 'intermediate', 'tutorial', '\"[\\\"Mastercam\\\"]\"', 'manufacturing', '\"null\"', '0', '1', 'normal', '[\"ISO\",\"ASME\"]', '1', '1', '2', '353', '20', '3', '0', '2', '26', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"NC\\\",\\\"MCX\\\",\\\"PDF\\\",\\\"STEP\\\"]\"', '1', '0', '1', NULL, NULL, '0.00', '\"[\\\"solidworks\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-06 03:49:52', NULL, '3', '2025-05-24 20:40:41', '2025-06-11 03:54:33', NULL);
INSERT INTO `threads` VALUES ('43', 'Lỗi \"Sketch is open, self-intersecting\" trong SolidWorks - Cách khắc phục', 'loi-sketch-is-open-self-intersecting-trong-solidworks-cach-khac-phuc-19-989', '
# Khắc Phục Lỗi \'Sketch is open, self-intersecting\' Trong SolidWorks

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
        ', '/images/threads/male-worker-factory.webp', '
# Khắc Phục Lỗi \'Sketch is open, self-intersecting\' Trong SolidWorks

Đây là một trong những lỗi phổ biến nhất khi sử dụng **Revolved Boss/Base** feature. Hãy...', '\"[\\\"solidworks\\\",\\\"cad\\\",\\\"3d modeling\\\"]\"', '2', 'published', '13', '19', '6', '0', '0', '0', '0', NULL, NULL, NULL, '7', '4.70', '12', 'tutorial', 'intermediate', 'tutorial', '\"[\\\"Mastercam\\\"]\"', 'manufacturing', '\"null\"', '1', '0', 'low', '[\"ISO\",\"ASME\"]', '0', '0', '0', '88', '24', '0', '9', '3', '15', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"NC\\\",\\\"MCX\\\",\\\"PDF\\\",\\\"STEP\\\"]\"', '1', '0', '1', NULL, NULL, '0.00', '\"[\\\"solidworks\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-14 18:29:52', NULL, '5', '2025-06-12 12:37:51', '2025-06-16 02:30:23', NULL);
INSERT INTO `threads` VALUES ('44', 'So sánh IGES vs STEP - Format file CAD nào tốt hơn?', 'so-sanh-iges-vs-step-format-file-cad-nao-tot-hon-19-324', '
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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# So Sánh IGES vs STEP - Format File CAD Nào Tốt Hơn?

Khi trao đổi file CAD giữa các phần mềm khác nhau, việc chọn format phù hợp rất quan trọng. Hãy cùng so...', '\"[]\"', '3', 'published', '27', '19', '6', '0', '1', '1', '0', NULL, NULL, NULL, '10', '4.90', '13', 'question', 'intermediate', 'tutorial', '\"[\\\"Mastercam\\\"]\"', 'manufacturing', '\"null\"', '1', '1', 'high', '[\"ISO\",\"ASME\"]', '1', '1', '1', '495', '14', '5', '0', '5', '8', '0', '0', '0', '0', '0', '5', '6', '\"[\\\"NC\\\",\\\"MCX\\\",\\\"PDF\\\",\\\"STEP\\\"]\"', '1', '0', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-24 17:56:52', NULL, '5', '2025-06-18 19:25:14', '2025-06-26 03:06:31', NULL);
INSERT INTO `threads` VALUES ('45', 'SolidWorks Material Library - Hướng dẫn sử dụng chi tiết', 'solidworks-material-library-huong-dan-su-dung-chi-tiet-19-318', '
# SolidWorks Material Library - Hướng Dẫn Sử Dụng Chi Tiết

Material Library là tính năng mạnh mẽ của SolidWorks giúp quản lý và áp dụng vật liệu cho models.

## Truy Cập Material Library

### Cách 1: Feature Manager
```
1. Right-click trên part name
2. Chọn \'Edit Material\'
3. Material dialog sẽ mở
```

### Cách 2: Material Tab
```
1. Mở ConfigurationManager
2. Click tab \'Material\'
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
- **Poisson\'s Ratio**
- **Tensile Strength** (N/m²)
- **Thermal Properties**

## Tạo Custom Material

### Bước 1: Copy Existing Material
```
1. Right-click material tương tự
2. Chọn \'Copy\'
3. Paste vào Custom Materials
```

### Bước 2: Edit Properties
```
- Name: Thép CT3 Việt Nam
- Density: 7850 kg/m³
- Elastic Modulus: 2.1e11 N/m²
- Poisson\'s Ratio: 0.28
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
\' VBA example
Set swMaterial = swModel.GetMaterialPropertyName2(\"Default\")
```

Ai đã tạo custom materials chưa? Share materials hay ho nhé!
        ', '/images/threads/male-worker-factory.webp', '
# SolidWorks Material Library - Hướng Dẫn Sử Dụng Chi Tiết

Material Library là tính năng mạnh mẽ của SolidWorks giúp quản lý và áp dụng vật liệu cho models....', '\"[\\\"solidworks\\\",\\\"cad\\\",\\\"3d modeling\\\"]\"', '3', 'published', '8', '19', '6', '0', '0', '0', '0', NULL, NULL, NULL, '8', '5.00', '25', 'tutorial', 'intermediate', 'tutorial', '\"[\\\"Mastercam\\\"]\"', 'manufacturing', '\"null\"', '1', '1', 'normal', '[\"ISO\",\"ASME\"]', '1', '1', '2', '276', '17', '14', '3', '4', '6', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"NC\\\",\\\"MCX\\\",\\\"PDF\\\",\\\"STEP\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[\\\"solidworks\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-25 17:57:52', NULL, '2', '2025-06-18 16:20:06', '2025-06-22 01:22:51', NULL);
INSERT INTO `threads` VALUES ('46', 'Thảo luận về G-Code Programming - Chia sẻ kinh nghiệm', 'thao-luan-ve-g-code-programming-chia-se-kinh-nghiem-20-362', '
# Chào Mừng Đến Với Forum G-Code Programming

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **G-Code Programming**.

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
        ', '/images/threads/mechanical-mini-projects-cover-pic.webp', '
# Chào Mừng Đến Với Forum G-Code Programming

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **G-Code Programming**.

## Mục Đích F...', '\"[]\"', '1', 'published', '29', '20', '6', '0', '0', '0', '0', NULL, NULL, NULL, '7', '3.70', '11', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'low', '[\"ISO\",\"ASME\"]', '0', '1', '3', '271', '28', '16', '9', '3', '20', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-14 17:57:52', NULL, '3', '2025-05-17 19:16:34', '2025-06-24 09:17:44', NULL);
INSERT INTO `threads` VALUES ('47', 'Hỏi đáp kỹ thuật về G-Code Programming', 'hoi-dap-ky-thuat-ve-g-code-programming-20-141', '
# Q&A - Hỏi Đáp Kỹ Thuật Về G-Code Programming

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến G-Code Programming.

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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về G-Code Programming

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến G-Code Programming.

## Cách Đặt C...', '\"[]\"', '2', 'published', '33', '20', '6', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.40', '5', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'critical', '[\"ISO\",\"ASME\"]', '1', '0', '1', '281', '36', '0', '5', '3', '8', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-19 17:57:52', NULL, '4', '2025-06-15 20:44:06', '2025-06-15 20:44:06', NULL);
INSERT INTO `threads` VALUES ('48', 'Lập trình CNC 3 trục với Mastercam - Kinh nghiệm từ thực tế', 'lap-trinh-cnc-3-truc-voi-mastercam-kinh-nghiem-tu-thuc-te-21-392', '
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
        ', '/images/threads/Professional Engineer.webp', '
# Lập Trình CNC 3 Trục Với Mastercam - Kinh Nghiệm Thực Tế

Sau 10 năm làm việc với CNC, tôi muốn chia sẻ những kinh nghiệm thực tế trong lập trình Mastercam....', '\"[\\\"cnc\\\",\\\"machining\\\",\\\"manufacturing\\\",\\\"mastercam\\\",\\\"cam\\\",\\\"toolpath\\\"]\"', '3', 'published', '5', '21', '6', '0', '0', '1', '0', NULL, NULL, NULL, '9', '3.70', '21', 'discussion', 'intermediate', 'manufacturing', '\"null\"', 'manufacturing', '\"{\\\"tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"surface_finish\\\":\\\"Ra 1.6\\\",\\\"material_removal_rate\\\":\\\"50 cm\\\\u00b3\\\\\\/min\\\"}\"', '1', '0', 'high', '[\"ISO 2768\",\"ASME Y14.5\"]', '0', '1', '3', '229', '26', '11', '2', '3', '14', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"NC\\\",\\\"MCX\\\",\\\"PDF\\\",\\\"STEP\\\"]\"', '0', '1', '0', NULL, NULL, '0.00', '\"[\\\"mastercam\\\",\\\"cnc\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-22 14:03:52', NULL, '1', '2025-04-05 01:59:28', '2025-04-05 01:59:28', NULL);
INSERT INTO `threads` VALUES ('49', 'Chọn dao phay phù hợp cho từng loại vật liệu - Bảng tra cứu', 'chon-dao-phay-phu-hop-cho-tung-loai-vat-lieu-bang-tra-cuu-21-635', '
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
        ', '/images/threads/Mechanical-Engineering-thumbnail.jpg', '
# Chọn Dao Phay Phù Hợp Cho Từng Loại Vật Liệu

Việc chọn dao phay đúng là yếu tố quyết định chất lượng gia công và tuổi thọ dao.

## Bảng Tra Cứu Nhanh

### A...', '\"[]\"', '3', 'published', '35', '21', '6', '0', '0', '0', '0', NULL, NULL, NULL, '7', '4.70', '21', 'discussion', 'intermediate', 'manufacturing', '\"null\"', 'manufacturing', '\"{\\\"tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"surface_finish\\\":\\\"Ra 1.6\\\",\\\"material_removal_rate\\\":\\\"50 cm\\\\u00b3\\\\\\/min\\\"}\"', '1', '1', 'critical', '[\"ISO 2768\",\"ASME Y14.5\"]', '0', '1', '0', '488', '30', '1', '2', '3', '14', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"NC\\\",\\\"MCX\\\",\\\"PDF\\\",\\\"STEP\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-12 17:57:52', NULL, '5', '2025-06-14 16:09:02', '2025-06-14 16:09:02', NULL);
INSERT INTO `threads` VALUES ('50', 'Post Processor là gì? Cách cài đặt và sử dụng trong Mastercam', 'post-processor-la-gi-cach-cai-dat-va-su-dung-trong-mastercam-21-992', '
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
        ', '/images/threads/Mechanical-Engineer-1-1024x536.webp', '
# Post Processor Là Gì? Cách Cài Đặt Và Sử Dụng Trong Mastercam

Post Processor (PP) là cầu nối quan trọng giữa CAM software và máy CNC.

## Post Processor Là...', '\"[\\\"mastercam\\\",\\\"cam\\\",\\\"toolpath\\\"]\"', '3', 'published', '29', '21', '6', '0', '0', '1', '0', NULL, NULL, NULL, '9', '4.10', '6', 'question', 'intermediate', 'manufacturing', '\"null\"', 'manufacturing', '\"{\\\"tolerance\\\":\\\"\\\\u00b10.01mm\\\",\\\"surface_finish\\\":\\\"Ra 1.6\\\",\\\"material_removal_rate\\\":\\\"50 cm\\\\u00b3\\\\\\/min\\\"}\"', '0', '0', 'critical', '[\"ISO 2768\",\"ASME Y14.5\"]', '1', '0', '0', '177', '31', '2', '9', '1', '6', '0', '0', '0', '0', '0', '1', '2', '\"[\\\"NC\\\",\\\"MCX\\\",\\\"PDF\\\",\\\"STEP\\\"]\"', '0', '0', '1', NULL, NULL, '0.00', '\"[\\\"mastercam\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-14 17:57:52', NULL, '5', '2025-03-01 07:17:18', '2025-06-28 12:51:29', NULL);
INSERT INTO `threads` VALUES ('51', 'Thảo luận về Thảo luận chung - Gia công truyền thống - Chia sẻ kinh nghiệm', 'thao-luan-ve-thao-luan-chung-gia-cong-truyen-thong-chia-se-kinh-nghiem-22-508', '
# Chào Mừng Đến Với Forum Thảo luận chung - Gia công truyền thống

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Thảo luận chung - Gia công truyền thống**.

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
        ', '/images/threads/mechanical-engineering-la-gi-7.webp', '
# Chào Mừng Đến Với Forum Thảo luận chung - Gia công truyền thống

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Thảo luận chung...', '\"[]\"', '2', 'published', '27', '22', '7', '0', '0', '0', '0', NULL, NULL, NULL, '7', '4.90', '25', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'high', '[\"ISO\",\"ASME\"]', '1', '1', '3', '61', '8', '4', '9', '3', '4', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-20 17:57:52', NULL, '5', '2025-05-16 06:06:38', '2025-06-23 14:20:43', NULL);
INSERT INTO `threads` VALUES ('52', 'Hỏi đáp kỹ thuật về Thảo luận chung - Gia công truyền thống', 'hoi-dap-ky-thuat-ve-thao-luan-chung-gia-cong-truyen-thong-22-942', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Thảo luận chung - Gia công truyền thống

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Thảo luận chung - Gia công truyền thống.

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
        ', '/images/threads/male-asian-engineer-professional-having-discussion-standing-by-machine-factory-two-asian-coworker-brainstorm-explaining-solves-process-curcuit-mother-board-machine.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Thảo luận chung - Gia công truyền thống

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Thảo luận chu...', '\"[]\"', '2', 'published', '12', '22', '7', '0', '0', '0', '0', NULL, NULL, NULL, '8', '3.50', '23', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'critical', '[\"ISO\",\"ASME\"]', '1', '1', '2', '285', '44', '18', '4', '4', '7', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-23 17:57:52', NULL, '2', '2025-05-19 06:36:03', '2025-07-03 16:26:47', NULL);
INSERT INTO `threads` VALUES ('53', 'Thảo luận về Hỏi đáp - Gia công truyền thống - Chia sẻ kinh nghiệm', 'thao-luan-ve-hoi-dap-gia-cong-truyen-thong-chia-se-kinh-nghiem-23-894', '
# Chào Mừng Đến Với Forum Hỏi đáp - Gia công truyền thống

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Hỏi đáp - Gia công truyền thống**.

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
        ', '/images/threads/mechanical-update_0.jpg', '
# Chào Mừng Đến Với Forum Hỏi đáp - Gia công truyền thống

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Hỏi đáp - Gia công truy...', '\"[]\"', '2', 'published', '9', '23', '7', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.20', '13', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'normal', '[\"ISO\",\"ASME\"]', '1', '1', '1', '13', '40', '15', '8', '3', '9', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-13 17:57:52', NULL, '3', '2025-05-03 04:38:53', '2025-06-30 04:15:02', NULL);
INSERT INTO `threads` VALUES ('54', 'Hỏi đáp kỹ thuật về Hỏi đáp - Gia công truyền thống', 'hoi-dap-ky-thuat-ve-hoi-dap-gia-cong-truyen-thong-23-288', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Hỏi đáp - Gia công truyền thống

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Hỏi đáp - Gia công truyền thống.

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
        ', '/images/threads/program-mech-eng.jpg', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Hỏi đáp - Gia công truyền thống

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Hỏi đáp - Gia công tr...', '\"[]\"', '2', 'published', '22', '23', '7', '0', '0', '0', '0', NULL, NULL, NULL, '7', '4.30', '8', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'critical', '[\"ISO\",\"ASME\"]', '1', '1', '1', '394', '50', '14', '6', '3', '28', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-18 17:57:52', NULL, '1', '2025-07-01 14:11:18', '2025-07-09 01:53:53', NULL);
INSERT INTO `threads` VALUES ('55', 'Thảo luận về Kinh nghiệm - Gia công truyền thống - Chia sẻ kinh nghiệm', 'thao-luan-ve-kinh-nghiem-gia-cong-truyen-thong-chia-se-kinh-nghiem-24-338', '
# Chào Mừng Đến Với Forum Kinh nghiệm - Gia công truyền thống

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Kinh nghiệm - Gia công truyền thống**.

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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# Chào Mừng Đến Với Forum Kinh nghiệm - Gia công truyền thống

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Kinh nghiệm - Gia c...', '\"[]\"', '2', 'published', '6', '24', '7', '0', '0', '0', '0', NULL, NULL, NULL, '7', '4.70', '21', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'normal', '[\"ISO\",\"ASME\"]', '1', '1', '1', '22', '37', '18', '0', '4', '6', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-22 17:57:52', NULL, '4', '2025-06-20 17:47:12', '2025-06-29 12:02:47', NULL);
INSERT INTO `threads` VALUES ('56', 'Hỏi đáp kỹ thuật về Kinh nghiệm - Gia công truyền thống', 'hoi-dap-ky-thuat-ve-kinh-nghiem-gia-cong-truyen-thong-24-469', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Kinh nghiệm - Gia công truyền thống

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Kinh nghiệm - Gia công truyền thống.

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
        ', '/images/threads/Mechanical_components.png', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Kinh nghiệm - Gia công truyền thống

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Kinh nghiệm - Gia...', '\"[]\"', '2', 'published', '15', '24', '7', '0', '0', '0', '0', NULL, NULL, NULL, '10', '4.40', '5', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'high', '[\"ISO\",\"ASME\"]', '1', '1', '3', '217', '46', '11', '6', '3', '21', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-24 17:57:52', NULL, '5', '2025-05-23 01:21:47', '2025-05-23 01:21:47', NULL);
INSERT INTO `threads` VALUES ('57', 'Thảo luận về Thảo luận chung - In 3D & Additive Manufacturing - Chia sẻ kinh nghiệm', 'thao-luan-ve-thao-luan-chung-in-3d-additive-manufacturing-chia-se-kinh-nghiem-25-400', '
# Chào Mừng Đến Với Forum Thảo luận chung - In 3D & Additive Manufacturing

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Thảo luận chung - In 3D & Additive Manufacturing**.

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
        ', '/images/threads/Professional Engineer.webp', '
# Chào Mừng Đến Với Forum Thảo luận chung - In 3D & Additive Manufacturing

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Thảo l...', '\"[]\"', '2', 'published', '2', '25', '8', '0', '0', '0', '0', NULL, NULL, NULL, '9', '5.00', '11', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'low', '[\"ISO\",\"ASME\"]', '0', '0', '0', '242', '11', '2', '4', '2', '29', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-19 13:53:52', NULL, '1', '2025-06-20 17:31:26', '2025-06-20 17:31:26', NULL);
INSERT INTO `threads` VALUES ('58', 'Hỏi đáp kỹ thuật về Thảo luận chung - In 3D & Additive Manufacturing', 'hoi-dap-ky-thuat-ve-thao-luan-chung-in-3d-additive-manufacturing-25-934', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Thảo luận chung - In 3D & Additive Manufacturing

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Thảo luận chung - In 3D & Additive Manufacturing.

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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Thảo luận chung - In 3D & Additive Manufacturing

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Thảo...', '\"[]\"', '2', 'published', '21', '25', '8', '0', '0', '0', '0', NULL, NULL, NULL, '8', '3.50', '7', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'normal', '[\"ISO\",\"ASME\"]', '1', '0', '1', '363', '26', '13', '6', '2', '13', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-12 17:57:52', NULL, '3', '2025-06-13 15:35:37', '2025-06-13 15:35:37', NULL);
INSERT INTO `threads` VALUES ('59', 'Thảo luận về Hỏi đáp - In 3D & Additive Manufacturing - Chia sẻ kinh nghiệm', 'thao-luan-ve-hoi-dap-in-3d-additive-manufacturing-chia-se-kinh-nghiem-26-331', '
# Chào Mừng Đến Với Forum Hỏi đáp - In 3D & Additive Manufacturing

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Hỏi đáp - In 3D & Additive Manufacturing**.

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
        ', '/images/threads/Mechanical_components.png', '
# Chào Mừng Đến Với Forum Hỏi đáp - In 3D & Additive Manufacturing

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Hỏi đáp - In 3...', '\"[]\"', '2', 'published', '35', '26', '8', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.70', '10', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'high', '[\"ISO\",\"ASME\"]', '1', '1', '2', '400', '14', '20', '7', '2', '22', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-19 17:57:52', NULL, '2', '2025-05-04 05:26:24', '2025-06-30 13:09:32', NULL);
INSERT INTO `threads` VALUES ('60', 'Hỏi đáp kỹ thuật về Hỏi đáp - In 3D & Additive Manufacturing', 'hoi-dap-ky-thuat-ve-hoi-dap-in-3d-additive-manufacturing-26-599', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Hỏi đáp - In 3D & Additive Manufacturing

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Hỏi đáp - In 3D & Additive Manufacturing.

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
        ', '/images/threads/images.jpg', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Hỏi đáp - In 3D & Additive Manufacturing

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Hỏi đáp - In...', '\"[]\"', '2', 'published', '7', '26', '8', '0', '0', '0', '0', NULL, NULL, NULL, '10', '3.80', '15', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'high', '[\"ISO\",\"ASME\"]', '1', '0', '1', '61', '9', '19', '10', '4', '7', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-23 17:57:52', NULL, '2', '2025-07-01 23:05:46', '2025-07-01 23:05:46', NULL);
INSERT INTO `threads` VALUES ('61', 'Thảo luận về Kinh nghiệm - In 3D & Additive Manufacturing - Chia sẻ kinh nghiệm', 'thao-luan-ve-kinh-nghiem-in-3d-additive-manufacturing-chia-se-kinh-nghiem-27-904', '
# Chào Mừng Đến Với Forum Kinh nghiệm - In 3D & Additive Manufacturing

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Kinh nghiệm - In 3D & Additive Manufacturing**.

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
        ', '/images/threads/Mechanical_components.png', '
# Chào Mừng Đến Với Forum Kinh nghiệm - In 3D & Additive Manufacturing

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Kinh nghiệ...', '\"[]\"', '2', 'published', '32', '27', '8', '0', '0', '0', '0', NULL, NULL, NULL, '9', '3.80', '17', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'high', '[\"ISO\",\"ASME\"]', '0', '1', '2', '461', '29', '18', '1', '5', '31', '0', '0', '0', '0', '0', '5', '6', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-18 05:33:52', NULL, '4', '2025-04-14 00:17:16', '2025-07-02 02:21:31', NULL);
INSERT INTO `threads` VALUES ('62', 'Hỏi đáp kỹ thuật về Kinh nghiệm - In 3D & Additive Manufacturing', 'hoi-dap-ky-thuat-ve-kinh-nghiem-in-3d-additive-manufacturing-27-967', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Kinh nghiệm - In 3D & Additive Manufacturing

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Kinh nghiệm - In 3D & Additive Manufacturing.

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
        ', '/images/threads/mechanical-mini-projects-cover-pic.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Kinh nghiệm - In 3D & Additive Manufacturing

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Kinh ngh...', '\"[]\"', '2', 'published', '22', '27', '8', '1', '0', '0', '0', NULL, NULL, NULL, '8', '3.50', '23', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'normal', '[\"ISO\",\"ASME\"]', '1', '1', '2', '260', '7', '2', '8', '4', '31', '0', '0', '1', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-24 17:57:52', NULL, '2', '2025-07-01 23:41:11', '2025-07-09 09:31:13', NULL);
INSERT INTO `threads` VALUES ('63', 'Xử lý nhiệt thép carbon - Quy trình và thông số chuẩn', 'xu-ly-nhiet-thep-carbon-quy-trinh-va-thong-so-chuan-28-746', '
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
        ', '/images/threads/male-asian-engineer-professional-having-discussion-standing-by-machine-factory-two-asian-coworker-brainstorm-explaining-solves-process-curcuit-mother-board-machine.webp', '
# Xử Lý Nhiệt Thép Carbon - Quy Trình Và Thông Số Chuẩn

Heat treatment là quá trình quan trọng để cải thiện tính chất cơ học của thép.

## Các Phương Pháp Xử...', '\"[]\"', '3', 'published', '18', '28', '9', '0', '0', '1', '0', NULL, NULL, NULL, '7', '3.80', '6', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'critical', '[\"ISO\",\"ASME\"]', '1', '0', '2', '246', '1', '12', '9', '1', '22', '0', '0', '0', '0', '0', '1', '2', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-13 17:57:52', NULL, '4', '2025-06-26 02:45:34', '2025-07-05 11:16:41', NULL);
INSERT INTO `threads` VALUES ('64', 'Hợp kim nhôm trong ngành hàng không - Tính chất và ứng dụng', 'hop-kim-nhom-trong-nganh-hang-khong-tinh-chat-va-ung-dung-28-766', '
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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# Hợp Kim Nhôm Trong Ngành Hàng Không - Tính Chất Và Ứng Dụng

Aluminum alloys là vật liệu chủ đạo trong ngành aerospace nhờ tỷ lệ strength-to-weight tuyệt vời...', '\"[]\"', '2', 'published', '15', '28', '9', '0', '0', '0', '0', NULL, NULL, NULL, '10', '4.30', '21', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'normal', '[\"ISO\",\"ASME\"]', '1', '0', '0', '113', '17', '7', '4', '3', '16', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-19 17:57:52', NULL, '1', '2025-06-19 20:28:11', '2025-06-26 17:27:08', NULL);
INSERT INTO `threads` VALUES ('65', 'Thảo luận về Polymer & Composite - Chia sẻ kinh nghiệm', 'thao-luan-ve-polymer-composite-chia-se-kinh-nghiem-29-345', '
# Chào Mừng Đến Với Forum Polymer & Composite

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Polymer & Composite**.

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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# Chào Mừng Đến Với Forum Polymer & Composite

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Polymer & Composite**.

## Mục Đích...', '\"[]\"', '1', 'published', '3', '29', '9', '0', '0', '0', '0', NULL, NULL, NULL, '7', '3.80', '22', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'critical', '[\"ISO\",\"ASME\"]', '1', '1', '3', '498', '49', '17', '6', '4', '28', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-21 23:04:52', NULL, '2', '2025-03-27 12:49:59', '2025-03-27 12:49:59', NULL);
INSERT INTO `threads` VALUES ('66', 'Hỏi đáp kỹ thuật về Polymer & Composite', 'hoi-dap-ky-thuat-ve-polymer-composite-29-827', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Polymer & Composite

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Polymer & Composite.

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
        ', '/images/threads/Mechanical_components.png', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Polymer & Composite

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Polymer & Composite.

## Cách Đặt...', '\"[]\"', '2', 'published', '14', '29', '9', '0', '0', '0', '0', NULL, NULL, NULL, '8', '5.00', '17', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'normal', '[\"ISO\",\"ASME\"]', '1', '0', '3', '248', '37', '14', '3', '1', '10', '0', '0', '0', '0', '0', '1', '2', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-05-31 17:57:52', NULL, '5', '2025-06-07 06:41:47', '2025-06-07 06:41:47', NULL);
INSERT INTO `threads` VALUES ('67', 'Thảo luận về Xử lý nhiệt - Chia sẻ kinh nghiệm', 'thao-luan-ve-xu-ly-nhiet-chia-se-kinh-nghiem-30-141', '
# Chào Mừng Đến Với Forum Xử lý nhiệt

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Xử lý nhiệt**.

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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# Chào Mừng Đến Với Forum Xử lý nhiệt

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Xử lý nhiệt**.

## Mục Đích Forum
- Chia sẻ...', '\"[]\"', '2', 'published', '17', '30', '9', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.80', '7', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'high', '[\"ISO\",\"ASME\"]', '0', '0', '1', '277', '11', '10', '6', '5', '23', '0', '0', '0', '0', '0', '5', '6', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-18 21:15:52', NULL, '4', '2025-03-13 09:09:53', '2025-06-29 14:16:22', NULL);
INSERT INTO `threads` VALUES ('68', 'Hỏi đáp kỹ thuật về Xử lý nhiệt', 'hoi-dap-ky-thuat-ve-xu-ly-nhiet-30-441', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Xử lý nhiệt

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Xử lý nhiệt.

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
        ', '/images/threads/images.jpg', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Xử lý nhiệt

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Xử lý nhiệt.

## Cách Đặt Câu Hỏi Hiệu Qu...', '\"[]\"', '2', 'published', '23', '30', '9', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.10', '16', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'high', '[\"ISO\",\"ASME\"]', '0', '0', '0', '170', '45', '8', '6', '4', '17', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-23 17:57:52', NULL, '5', '2025-06-26 21:32:11', '2025-06-26 21:32:11', NULL);
INSERT INTO `threads` VALUES ('69', 'Thảo luận về Vật liệu Smart - Chia sẻ kinh nghiệm', 'thao-luan-ve-vat-lieu-smart-chia-se-kinh-nghiem-31-875', '
# Chào Mừng Đến Với Forum Vật liệu Smart

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Vật liệu Smart**.

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
        ', '/images/threads/Mechanical_components.png', '
# Chào Mừng Đến Với Forum Vật liệu Smart

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Vật liệu Smart**.

## Mục Đích Forum
- C...', '\"[]\"', '2', 'published', '25', '31', '9', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.20', '10', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'high', '[\"ISO\",\"ASME\"]', '0', '1', '3', '131', '12', '4', '2', '2', '5', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-20 17:57:52', NULL, '1', '2025-06-24 02:43:19', '2025-06-24 02:43:19', NULL);
INSERT INTO `threads` VALUES ('70', 'Hỏi đáp kỹ thuật về Vật liệu Smart', 'hoi-dap-ky-thuat-ve-vat-lieu-smart-31-910', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Vật liệu Smart

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Vật liệu Smart.

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
        ', '/images/threads/Professional Engineer.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Vật liệu Smart

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Vật liệu Smart.

## Cách Đặt Câu Hỏi H...', '\"[]\"', '2', 'published', '35', '31', '9', '0', '0', '0', '0', NULL, NULL, NULL, '8', '3.80', '22', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'high', '[\"ISO\",\"ASME\"]', '0', '1', '0', '169', '3', '14', '6', '2', '12', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-13 17:57:52', NULL, '4', '2025-05-31 09:51:54', '2025-06-18 17:16:45', NULL);
INSERT INTO `threads` VALUES ('71', 'Xử lý nhiệt thép carbon - Quy trình và thông số chuẩn', 'xu-ly-nhiet-thep-carbon-quy-trinh-va-thong-so-chuan-32-261', '
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
        ', '/images/threads/Mechanical-Engineering-thumbnail.jpg', '
# Xử Lý Nhiệt Thép Carbon - Quy Trình Và Thông Số Chuẩn

Heat treatment là quá trình quan trọng để cải thiện tính chất cơ học của thép.

## Các Phương Pháp Xử...', '\"[]\"', '3', 'published', '31', '32', '10', '0', '0', '1', '0', NULL, NULL, NULL, '9', '4.90', '15', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'normal', '[\"ISO\",\"ASME\"]', '1', '1', '0', '100', '9', '20', '1', '2', '6', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-20 17:57:52', NULL, '1', '2025-07-01 14:06:13', '2025-07-09 05:18:18', NULL);
INSERT INTO `threads` VALUES ('72', 'Hợp kim nhôm trong ngành hàng không - Tính chất và ứng dụng', 'hop-kim-nhom-trong-nganh-hang-khong-tinh-chat-va-ung-dung-32-103', '
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
        ', '/images/threads/male-worker-factory.webp', '
# Hợp Kim Nhôm Trong Ngành Hàng Không - Tính Chất Và Ứng Dụng

Aluminum alloys là vật liệu chủ đạo trong ngành aerospace nhờ tỷ lệ strength-to-weight tuyệt vời...', '\"[]\"', '2', 'published', '14', '32', '10', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.00', '9', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'critical', '[\"ISO\",\"ASME\"]', '0', '0', '1', '306', '19', '7', '1', '3', '5', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-24 17:57:52', NULL, '4', '2025-06-15 23:06:36', '2025-07-01 06:23:33', NULL);
INSERT INTO `threads` VALUES ('73', 'Thảo luận về Polymer & Composite - Chia sẻ kinh nghiệm', 'thao-luan-ve-polymer-composite-chia-se-kinh-nghiem-33-150', '
# Chào Mừng Đến Với Forum Polymer & Composite

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Polymer & Composite**.

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
        ', '/images/threads/Mechanical-Engineer-1-1024x536.webp', '
# Chào Mừng Đến Với Forum Polymer & Composite

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Polymer & Composite**.

## Mục Đích...', '\"[]\"', '1', 'published', '17', '33', '10', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.70', '13', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'high', '[\"ISO\",\"ASME\"]', '1', '1', '1', '296', '14', '15', '4', '2', '25', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-24 17:57:52', NULL, '5', '2025-05-26 21:12:20', '2025-05-26 21:12:20', NULL);
INSERT INTO `threads` VALUES ('74', 'Hỏi đáp kỹ thuật về Polymer & Composite', 'hoi-dap-ky-thuat-ve-polymer-composite-33-913', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Polymer & Composite

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Polymer & Composite.

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
        ', '/images/threads/male-worker-factory.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Polymer & Composite

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Polymer & Composite.

## Cách Đặt...', '\"[]\"', '2', 'published', '12', '33', '10', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.90', '9', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'low', '[\"ISO\",\"ASME\"]', '1', '0', '1', '87', '41', '12', '10', '4', '10', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-24 17:57:52', NULL, '5', '2025-06-29 14:00:37', '2025-07-03 14:43:20', NULL);
INSERT INTO `threads` VALUES ('75', 'Thảo luận về Xử lý nhiệt - Chia sẻ kinh nghiệm', 'thao-luan-ve-xu-ly-nhiet-chia-se-kinh-nghiem-34-436', '
# Chào Mừng Đến Với Forum Xử lý nhiệt

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Xử lý nhiệt**.

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
        ', '/images/threads/male-asian-engineer-professional-having-discussion-standing-by-machine-factory-two-asian-coworker-brainstorm-explaining-solves-process-curcuit-mother-board-machine.webp', '
# Chào Mừng Đến Với Forum Xử lý nhiệt

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Xử lý nhiệt**.

## Mục Đích Forum
- Chia sẻ...', '\"[]\"', '2', 'published', '28', '34', '10', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.40', '19', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'normal', '[\"ISO\",\"ASME\"]', '1', '1', '3', '200', '16', '8', '5', '3', '30', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-22 17:57:52', NULL, '2', '2025-03-06 22:45:10', '2025-03-06 22:45:10', NULL);
INSERT INTO `threads` VALUES ('76', 'Hỏi đáp kỹ thuật về Xử lý nhiệt', 'hoi-dap-ky-thuat-ve-xu-ly-nhiet-34-838', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Xử lý nhiệt

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Xử lý nhiệt.

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
        ', '/images/threads/mechanical-update_0.jpg', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Xử lý nhiệt

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Xử lý nhiệt.

## Cách Đặt Câu Hỏi Hiệu Qu...', '\"[]\"', '2', 'published', '5', '34', '10', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.40', '10', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'critical', '[\"ISO\",\"ASME\"]', '1', '1', '2', '79', '34', '4', '9', '5', '8', '0', '0', '0', '0', '0', '5', '6', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-21 04:54:52', NULL, '5', '2025-06-27 22:29:11', '2025-06-27 22:29:11', NULL);
INSERT INTO `threads` VALUES ('77', 'Thảo luận về Vật liệu Smart - Chia sẻ kinh nghiệm', 'thao-luan-ve-vat-lieu-smart-chia-se-kinh-nghiem-35-103', '
# Chào Mừng Đến Với Forum Vật liệu Smart

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Vật liệu Smart**.

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
        ', '/images/threads/Mechanical_components.png', '
# Chào Mừng Đến Với Forum Vật liệu Smart

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Vật liệu Smart**.

## Mục Đích Forum
- C...', '\"[]\"', '2', 'published', '12', '35', '10', '0', '0', '0', '0', NULL, NULL, NULL, '10', '4.90', '25', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'low', '[\"ISO\",\"ASME\"]', '0', '0', '0', '110', '44', '4', '8', '3', '6', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-24 17:57:52', NULL, '5', '2025-05-16 20:43:53', '2025-06-07 11:25:34', NULL);
INSERT INTO `threads` VALUES ('78', 'Hỏi đáp kỹ thuật về Vật liệu Smart', 'hoi-dap-ky-thuat-ve-vat-lieu-smart-35-162', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Vật liệu Smart

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Vật liệu Smart.

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
        ', '/images/threads/images.jpg', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Vật liệu Smart

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Vật liệu Smart.

## Cách Đặt Câu Hỏi H...', '\"[]\"', '2', 'published', '10', '35', '10', '0', '0', '0', '0', NULL, NULL, NULL, '8', '3.80', '19', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'low', '[\"ISO\",\"ASME\"]', '1', '1', '1', '350', '41', '14', '9', '1', '20', '0', '0', '0', '0', '0', '1', '2', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-12 17:57:52', NULL, '4', '2025-07-01 14:41:47', '2025-07-01 14:41:47', NULL);
INSERT INTO `threads` VALUES ('79', 'Thảo luận về Thảo luận chung - Polymer & Composite - Chia sẻ kinh nghiệm', 'thao-luan-ve-thao-luan-chung-polymer-composite-chia-se-kinh-nghiem-36-741', '
# Chào Mừng Đến Với Forum Thảo luận chung - Polymer & Composite

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Thảo luận chung - Polymer & Composite**.

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
        ', '/images/threads/mechanical-mini-projects-cover-pic.webp', '
# Chào Mừng Đến Với Forum Thảo luận chung - Polymer & Composite

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Thảo luận chung -...', '\"[]\"', '2', 'published', '34', '36', '11', '0', '0', '0', '0', NULL, NULL, NULL, '7', '3.90', '8', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'normal', '[\"ISO\",\"ASME\"]', '1', '1', '0', '211', '8', '11', '6', '4', '8', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-26 16:30:52', NULL, '1', '2025-05-21 15:12:38', '2025-05-21 15:12:38', NULL);
INSERT INTO `threads` VALUES ('80', 'Hỏi đáp kỹ thuật về Thảo luận chung - Polymer & Composite', 'hoi-dap-ky-thuat-ve-thao-luan-chung-polymer-composite-36-703', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Thảo luận chung - Polymer & Composite

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Thảo luận chung - Polymer & Composite.

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
        ', '/images/threads/male-worker-factory.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Thảo luận chung - Polymer & Composite

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Thảo luận chung...', '\"[]\"', '2', 'published', '7', '36', '11', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.60', '20', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'normal', '[\"ISO\",\"ASME\"]', '1', '0', '0', '250', '43', '19', '8', '4', '26', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-18 17:57:52', NULL, '2', '2025-05-07 20:18:29', '2025-05-07 20:18:29', NULL);
INSERT INTO `threads` VALUES ('81', 'Thảo luận về Hỏi đáp - Polymer & Composite - Chia sẻ kinh nghiệm', 'thao-luan-ve-hoi-dap-polymer-composite-chia-se-kinh-nghiem-37-936', '
# Chào Mừng Đến Với Forum Hỏi đáp - Polymer & Composite

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Hỏi đáp - Polymer & Composite**.

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
        ', '/images/threads/Mechanical_components.png', '
# Chào Mừng Đến Với Forum Hỏi đáp - Polymer & Composite

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Hỏi đáp - Polymer & Compo...', '\"[]\"', '2', 'published', '35', '37', '11', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.10', '7', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'normal', '[\"ISO\",\"ASME\"]', '0', '0', '0', '480', '44', '8', '4', '1', '19', '0', '0', '0', '0', '0', '1', '2', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-24 17:57:52', NULL, '1', '2025-05-09 18:07:41', '2025-05-09 18:07:41', NULL);
INSERT INTO `threads` VALUES ('82', 'Hỏi đáp kỹ thuật về Hỏi đáp - Polymer & Composite', 'hoi-dap-ky-thuat-ve-hoi-dap-polymer-composite-37-105', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Hỏi đáp - Polymer & Composite

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Hỏi đáp - Polymer & Composite.

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
        ', '/images/threads/images.jpg', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Hỏi đáp - Polymer & Composite

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Hỏi đáp - Polymer & Com...', '\"[]\"', '2', 'published', '18', '37', '11', '0', '0', '0', '0', NULL, NULL, NULL, '10', '3.80', '24', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'high', '[\"ISO\",\"ASME\"]', '1', '1', '2', '242', '22', '19', '1', '5', '22', '0', '0', '0', '0', '0', '5', '6', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-25 08:43:52', NULL, '1', '2025-04-05 02:02:50', '2025-05-09 08:59:07', NULL);
INSERT INTO `threads` VALUES ('83', 'Thảo luận về Kinh nghiệm - Polymer & Composite - Chia sẻ kinh nghiệm', 'thao-luan-ve-kinh-nghiem-polymer-composite-chia-se-kinh-nghiem-38-797', '
# Chào Mừng Đến Với Forum Kinh nghiệm - Polymer & Composite

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Kinh nghiệm - Polymer & Composite**.

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
        ', '/images/threads/program-mech-eng.jpg', '
# Chào Mừng Đến Với Forum Kinh nghiệm - Polymer & Composite

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Kinh nghiệm - Polymer...', '\"[]\"', '2', 'published', '24', '38', '11', '0', '0', '0', '0', NULL, NULL, NULL, '8', '3.80', '13', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'normal', '[\"ISO\",\"ASME\"]', '1', '1', '3', '500', '38', '1', '9', '3', '11', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-15 17:57:52', NULL, '4', '2025-05-21 18:44:10', '2025-05-23 17:50:48', NULL);
INSERT INTO `threads` VALUES ('84', 'Hỏi đáp kỹ thuật về Kinh nghiệm - Polymer & Composite', 'hoi-dap-ky-thuat-ve-kinh-nghiem-polymer-composite-38-612', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Kinh nghiệm - Polymer & Composite

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Kinh nghiệm - Polymer & Composite.

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
        ', '/images/threads/mj_11351_4.jpg', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Kinh nghiệm - Polymer & Composite

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Kinh nghiệm - Polym...', '\"[]\"', '2', 'published', '30', '38', '11', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.20', '6', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'high', '[\"ISO\",\"ASME\"]', '1', '0', '1', '71', '48', '17', '3', '4', '10', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:52', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-23 17:57:52', NULL, '1', '2025-05-24 20:58:47', '2025-05-24 20:58:47', NULL);
INSERT INTO `threads` VALUES ('85', 'Xử lý nhiệt thép carbon - Quy trình và thông số chuẩn', 'xu-ly-nhiet-thep-carbon-quy-trinh-va-thong-so-chuan-39-216', '
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
        ', '/images/threads/male-asian-engineer-professional-having-discussion-standing-by-machine-factory-two-asian-coworker-brainstorm-explaining-solves-process-curcuit-mother-board-machine.webp', '
# Xử Lý Nhiệt Thép Carbon - Quy Trình Và Thông Số Chuẩn

Heat treatment là quá trình quan trọng để cải thiện tính chất cơ học của thép.

## Các Phương Pháp Xử...', '\"[]\"', '3', 'published', '3', '39', '12', '0', '0', '1', '0', NULL, NULL, NULL, '8', '4.30', '9', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'normal', '[\"ISO\",\"ASME\"]', '0', '1', '2', '312', '14', '16', '1', '3', '17', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-26 01:55:52', NULL, '3', '2025-04-29 10:39:57', '2025-04-29 10:39:57', NULL);
INSERT INTO `threads` VALUES ('86', 'Hợp kim nhôm trong ngành hàng không - Tính chất và ứng dụng', 'hop-kim-nhom-trong-nganh-hang-khong-tinh-chat-va-ung-dung-39-578', '
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
        ', '/images/threads/Professional Engineer.webp', '
# Hợp Kim Nhôm Trong Ngành Hàng Không - Tính Chất Và Ứng Dụng

Aluminum alloys là vật liệu chủ đạo trong ngành aerospace nhờ tỷ lệ strength-to-weight tuyệt vời...', '\"[]\"', '2', 'published', '19', '39', '12', '0', '0', '0', '0', NULL, NULL, NULL, '7', '3.90', '9', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'high', '[\"ISO\",\"ASME\"]', '0', '1', '1', '28', '37', '16', '1', '2', '1', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-09 17:57:53', NULL, '2', '2025-05-28 23:33:06', '2025-06-24 22:16:29', NULL);
INSERT INTO `threads` VALUES ('87', 'Thảo luận về Polymer & Composite - Chia sẻ kinh nghiệm', 'thao-luan-ve-polymer-composite-chia-se-kinh-nghiem-40-909', '
# Chào Mừng Đến Với Forum Polymer & Composite

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Polymer & Composite**.

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
        ', '/images/threads/success-story-schuetz-industrie-anlagenmechanikerin-2128x1330-c.jpg.webp', '
# Chào Mừng Đến Với Forum Polymer & Composite

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Polymer & Composite**.

## Mục Đích...', '\"[]\"', '1', 'published', '28', '40', '12', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.20', '9', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'high', '[\"ISO\",\"ASME\"]', '1', '0', '0', '143', '15', '14', '3', '3', '32', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-22 17:57:53', NULL, '1', '2025-06-10 19:26:52', '2025-06-10 19:26:52', NULL);
INSERT INTO `threads` VALUES ('88', 'Hỏi đáp kỹ thuật về Polymer & Composite', 'hoi-dap-ky-thuat-ve-polymer-composite-40-115', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Polymer & Composite

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Polymer & Composite.

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
        ', '/images/threads/Mechanical_components.png', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Polymer & Composite

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Polymer & Composite.

## Cách Đặt...', '\"[]\"', '2', 'published', '10', '40', '12', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.10', '18', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'critical', '[\"ISO\",\"ASME\"]', '1', '0', '1', '326', '36', '7', '5', '3', '31', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-20 17:57:53', NULL, '3', '2025-06-26 01:15:21', '2025-06-26 01:15:21', NULL);
INSERT INTO `threads` VALUES ('89', 'Thảo luận về Xử lý nhiệt - Chia sẻ kinh nghiệm', 'thao-luan-ve-xu-ly-nhiet-chia-se-kinh-nghiem-41-557', '
# Chào Mừng Đến Với Forum Xử lý nhiệt

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Xử lý nhiệt**.

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
        ', '/images/threads/mechanical-update_0.jpg', '
# Chào Mừng Đến Với Forum Xử lý nhiệt

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Xử lý nhiệt**.

## Mục Đích Forum
- Chia sẻ...', '\"[]\"', '2', 'published', '25', '41', '12', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.00', '19', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'critical', '[\"ISO\",\"ASME\"]', '0', '1', '2', '391', '8', '14', '6', '4', '25', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-23 08:25:53', NULL, '3', '2025-06-26 18:38:16', '2025-06-26 18:38:16', NULL);
INSERT INTO `threads` VALUES ('90', 'Hỏi đáp kỹ thuật về Xử lý nhiệt', 'hoi-dap-ky-thuat-ve-xu-ly-nhiet-41-439', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Xử lý nhiệt

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Xử lý nhiệt.

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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Xử lý nhiệt

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Xử lý nhiệt.

## Cách Đặt Câu Hỏi Hiệu Qu...', '\"[]\"', '2', 'published', '1', '41', '12', '0', '0', '0', '0', NULL, NULL, NULL, '9', '3.70', '11', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'normal', '[\"ISO\",\"ASME\"]', '0', '1', '1', '149', '15', '20', '1', '3', '9', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-22 17:57:53', NULL, '4', '2025-04-10 23:36:02', '2025-07-09 12:59:40', NULL);
INSERT INTO `threads` VALUES ('91', 'Thảo luận về Vật liệu Smart - Chia sẻ kinh nghiệm', 'thao-luan-ve-vat-lieu-smart-chia-se-kinh-nghiem-42-970', '
# Chào Mừng Đến Với Forum Vật liệu Smart

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Vật liệu Smart**.

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
        ', '/images/threads/success-story-schuetz-industrie-anlagenmechanikerin-2128x1330-c.jpg.webp', '
# Chào Mừng Đến Với Forum Vật liệu Smart

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Vật liệu Smart**.

## Mục Đích Forum
- C...', '\"[]\"', '2', 'published', '31', '42', '12', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.60', '14', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'high', '[\"ISO\",\"ASME\"]', '0', '1', '0', '65', '18', '20', '5', '2', '3', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-14 17:57:53', NULL, '2', '2025-06-22 14:51:48', '2025-06-22 14:51:48', NULL);
INSERT INTO `threads` VALUES ('92', 'Hỏi đáp kỹ thuật về Vật liệu Smart', 'hoi-dap-ky-thuat-ve-vat-lieu-smart-42-580', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Vật liệu Smart

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Vật liệu Smart.

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
        ', '/images/threads/Mechanical_components.png', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Vật liệu Smart

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Vật liệu Smart.

## Cách Đặt Câu Hỏi H...', '\"[]\"', '2', 'published', '6', '42', '12', '0', '0', '0', '0', NULL, NULL, NULL, '7', '4.20', '16', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'normal', '[\"ISO\",\"ASME\"]', '0', '1', '0', '219', '14', '8', '2', '3', '30', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-11 17:57:53', NULL, '4', '2025-06-29 21:58:32', '2025-06-29 21:58:32', NULL);
INSERT INTO `threads` VALUES ('93', 'Lập trình PLC Siemens S7-1200 cho người mới bắt đầu', 'lap-trinh-plc-siemens-s7-1200-cho-nguoi-moi-bat-dau-43-160', '
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
        ', '/images/threads/Mechanical-Engineer-1-1024x536.webp', '
# Lập Trình PLC Siemens S7-1200 Cho Người Mới

PLC (Programmable Logic Controller) là trái tim của hệ thống tự động hóa. Hướng dẫn này sẽ giúp bạn bắt đầu với...', '\"[\\\"plc\\\",\\\"automation\\\",\\\"control\\\"]\"', '1', 'published', '12', '43', '13', '0', '0', '1', '0', NULL, NULL, NULL, '9', '3.70', '19', 'discussion', 'beginner', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'critical', '[\"ISO\",\"ASME\"]', '1', '1', '2', '24', '12', '13', '2', '1', '33', '0', '0', '0', '0', '0', '1', '2', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '0', NULL, NULL, '0.00', '\"[\\\"plc\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-19 17:57:53', NULL, '5', '2025-06-18 18:29:20', '2025-06-18 18:29:20', NULL);
INSERT INTO `threads` VALUES ('94', 'Thiết kế HMI hiệu quả - Best practices và tips', 'thiet-ke-hmi-hieu-qua-best-practices-va-tips-43-560', '
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
        ', '/images/threads/Mechanical-Engineer-1-1024x536.webp', '
# Thiết Kế HMI Hiệu Quả - Best Practices Và Tips

Human Machine Interface (HMI) là giao diện quan trọng giữa operator và máy móc.

## Nguyên Tắc Thiết Kế

###...', '\"[]\"', '2', 'published', '28', '43', '13', '0', '0', '0', '0', NULL, NULL, NULL, '8', '5.00', '19', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'critical', '[\"ISO\",\"ASME\"]', '1', '1', '0', '368', '40', '8', '3', '5', '34', '0', '0', '0', '0', '0', '5', '6', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[\\\"hmi\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-16 21:54:53', NULL, '3', '2025-06-19 14:02:59', '2025-06-19 14:02:59', NULL);
INSERT INTO `threads` VALUES ('95', 'Tích hợp robot ABB vào dây chuyền sản xuất - Case study', 'tich-hop-robot-abb-vao-day-chuyen-san-xuat-case-study-44-244', '
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
        ', '/images/threads/Professional Engineer.webp', '
# Tích Hợp Robot ABB Vào Dây Chuyền Sản Xuất - Case Study

Dự án tích hợp robot ABB IRB 1600 vào dây chuyền welding tại nhà máy ô tô.

## Thông Tin Dự Án

###...', '\"[\\\"robot\\\",\\\"robotics\\\",\\\"automation\\\"]\"', '3', 'published', '15', '44', '13', '0', '0', '1', '0', NULL, NULL, NULL, '7', '3.90', '24', 'discussion', 'intermediate', 'manufacturing', '\"null\"', 'manufacturing', '\"{\\\"payload\\\":\\\"6 kg\\\",\\\"reach\\\":\\\"1450 mm\\\",\\\"repeatability\\\":\\\"\\\\u00b10.1 mm\\\",\\\"speed\\\":\\\"2.3 m\\\\\\/s\\\"}\"', '1', '0', 'high', '[\"ISO 10218\",\"IEC 61508\"]', '0', '1', '1', '264', '26', '6', '7', '1', '11', '0', '0', '0', '0', '0', '1', '2', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '0', NULL, NULL, '0.00', '\"[\\\"robot\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-13 17:57:53', NULL, '2', '2025-06-11 01:15:07', '2025-06-11 01:15:07', NULL);
INSERT INTO `threads` VALUES ('96', 'So sánh robot KUKA vs Fanuc vs ABB - Ưu nhược điểm', 'so-sanh-robot-kuka-vs-fanuc-vs-abb-uu-nhuoc-diem-44-149', '
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
        ', '/images/threads/Professional Engineer.webp', '
# So Sánh Robot KUKA vs Fanuc vs ABB - Ưu Nhược Điểm

Chọn robot phù hợp cho ứng dụng cụ thể cần hiểu rõ đặc điểm từng hãng.

## ABB Robotics

### Ưu Điểm:
- *...', '\"[\\\"robot\\\",\\\"robotics\\\",\\\"automation\\\"]\"', '3', 'published', '4', '44', '13', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.40', '22', 'discussion', 'intermediate', 'manufacturing', '\"null\"', 'manufacturing', '\"{\\\"payload\\\":\\\"6 kg\\\",\\\"reach\\\":\\\"1450 mm\\\",\\\"repeatability\\\":\\\"\\\\u00b10.1 mm\\\",\\\"speed\\\":\\\"2.3 m\\\\\\/s\\\"}\"', '0', '0', 'high', '[\"ISO 10218\",\"IEC 61508\"]', '0', '0', '3', '260', '49', '11', '5', '3', '15', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[\\\"robot\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-07 17:57:53', NULL, '1', '2025-06-27 07:53:08', '2025-06-27 07:53:08', NULL);
INSERT INTO `threads` VALUES ('97', 'Thảo luận về Sensors & Actuators - Chia sẻ kinh nghiệm', 'thao-luan-ve-sensors-actuators-chia-se-kinh-nghiem-45-731', '
# Chào Mừng Đến Với Forum Sensors & Actuators

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Sensors & Actuators**.

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
        ', '/images/threads/Mechanical-Engineer-1-1024x536.webp', '
# Chào Mừng Đến Với Forum Sensors & Actuators

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Sensors & Actuators**.

## Mục Đích...', '\"[]\"', '1', 'published', '32', '45', '13', '0', '0', '0', '0', NULL, NULL, NULL, '8', '3.90', '22', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'critical', '[\"ISO\",\"ASME\"]', '0', '0', '0', '151', '12', '2', '9', '3', '23', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-25 17:57:53', NULL, '5', '2025-03-07 17:27:18', '2025-03-07 17:27:18', NULL);
INSERT INTO `threads` VALUES ('98', 'Hỏi đáp kỹ thuật về Sensors & Actuators', 'hoi-dap-ky-thuat-ve-sensors-actuators-45-200', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Sensors & Actuators

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Sensors & Actuators.

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
        ', '/images/threads/male-worker-factory.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Sensors & Actuators

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Sensors & Actuators.

## Cách Đặt...', '\"[]\"', '2', 'published', '28', '45', '13', '0', '0', '0', '0', NULL, NULL, NULL, '9', '5.00', '24', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'critical', '[\"ISO\",\"ASME\"]', '1', '0', '0', '189', '22', '13', '3', '4', '28', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-24 17:57:53', NULL, '2', '2025-03-28 22:30:27', '2025-05-06 01:13:51', NULL);
INSERT INTO `threads` VALUES ('99', 'Thảo luận về Industry 4.0 & IoT - Chia sẻ kinh nghiệm', 'thao-luan-ve-industry-40-iot-chia-se-kinh-nghiem-46-301', '
# Chào Mừng Đến Với Forum Industry 4.0 & IoT

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Industry 4.0 & IoT**.

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
        ', '/images/threads/mechanical-update_0.jpg', '
# Chào Mừng Đến Với Forum Industry 4.0 & IoT

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Industry 4.0 & IoT**.

## Mục Đích F...', '\"[]\"', '1', 'published', '15', '46', '13', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.30', '16', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '1', 'low', '[\"ISO\",\"ASME\"]', '0', '1', '0', '124', '48', '7', '3', '4', '15', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-19 09:08:53', NULL, '1', '2025-05-02 08:28:25', '2025-05-02 08:28:25', NULL);
INSERT INTO `threads` VALUES ('100', 'Hỏi đáp kỹ thuật về Industry 4.0 & IoT', 'hoi-dap-ky-thuat-ve-industry-40-iot-46-670', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Industry 4.0 & IoT

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Industry 4.0 & IoT.

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
        ', '/images/threads/compressed_2151589656.jpg', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Industry 4.0 & IoT

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Industry 4.0 & IoT.

## Cách Đặt C...', '\"[]\"', '2', 'published', '11', '46', '13', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.70', '8', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'critical', '[\"ISO\",\"ASME\"]', '1', '1', '3', '309', '4', '15', '9', '3', '15', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-23 17:57:53', NULL, '2', '2025-06-14 04:54:24', '2025-06-14 04:54:24', NULL);
INSERT INTO `threads` VALUES ('101', 'Lập trình PLC Siemens S7-1200 cho người mới bắt đầu', 'lap-trinh-plc-siemens-s7-1200-cho-nguoi-moi-bat-dau-47-945', '
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
        ', '/images/threads/images.jpg', '
# Lập Trình PLC Siemens S7-1200 Cho Người Mới

PLC (Programmable Logic Controller) là trái tim của hệ thống tự động hóa. Hướng dẫn này sẽ giúp bạn bắt đầu với...', '\"[\\\"plc\\\",\\\"automation\\\",\\\"control\\\"]\"', '1', 'published', '3', '47', '14', '0', '0', '1', '0', NULL, NULL, NULL, '8', '3.70', '9', 'discussion', 'beginner', 'tutorial', '\"[\\\"TIA Portal\\\"]\"', 'manufacturing', '\"null\"', '1', '0', 'normal', '[\"ISO\",\"ASME\"]', '0', '1', '2', '399', '37', '8', '3', '3', '15', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[\\\"plc\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-04 10:07:53', NULL, '4', '2025-04-22 09:06:06', '2025-04-22 09:06:06', NULL);
INSERT INTO `threads` VALUES ('102', 'Thiết kế HMI hiệu quả - Best practices và tips', 'thiet-ke-hmi-hieu-qua-best-practices-va-tips-47-660', '
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
        ', '/images/threads/Professional Engineer.webp', '
# Thiết Kế HMI Hiệu Quả - Best Practices Và Tips

Human Machine Interface (HMI) là giao diện quan trọng giữa operator và máy móc.

## Nguyên Tắc Thiết Kế

###...', '\"[]\"', '2', 'published', '9', '47', '14', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.70', '17', 'discussion', 'intermediate', 'tutorial', '\"[\\\"TIA Portal\\\"]\"', 'manufacturing', '\"null\"', '1', '1', 'critical', '[\"ISO\",\"ASME\"]', '0', '0', '3', '329', '2', '13', '5', '1', '22', '0', '0', '0', '0', '0', '1', '2', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '1', NULL, NULL, '0.00', '\"[\\\"hmi\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-08 17:57:53', NULL, '1', '2025-06-13 21:43:24', '2025-06-13 21:43:24', NULL);
INSERT INTO `threads` VALUES ('103', 'Thảo luận về Allen-Bradley - Chia sẻ kinh nghiệm', 'thao-luan-ve-allen-bradley-chia-se-kinh-nghiem-48-933', '
# Chào Mừng Đến Với Forum Allen-Bradley

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Allen-Bradley**.

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
        ', '/images/threads/Mechanical_components.png', '
# Chào Mừng Đến Với Forum Allen-Bradley

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Allen-Bradley**.

## Mục Đích Forum
- Chi...', '\"[]\"', '1', 'published', '28', '48', '14', '0', '0', '0', '0', NULL, NULL, NULL, '8', '3.70', '8', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'normal', '[\"ISO\",\"ASME\"]', '1', '0', '3', '70', '2', '14', '5', '4', '22', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-22 17:57:53', NULL, '5', '2025-06-14 12:22:55', '2025-06-14 12:22:55', NULL);
INSERT INTO `threads` VALUES ('104', 'Hỏi đáp kỹ thuật về Allen-Bradley', 'hoi-dap-ky-thuat-ve-allen-bradley-48-427', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Allen-Bradley

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Allen-Bradley.

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
        ', '/images/threads/Professional Engineer.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Allen-Bradley

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Allen-Bradley.

## Cách Đặt Câu Hỏi Hiệ...', '\"[]\"', '2', 'published', '33', '48', '14', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.40', '17', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'high', '[\"ISO\",\"ASME\"]', '1', '1', '2', '456', '40', '3', '9', '2', '4', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-04 17:57:53', NULL, '5', '2025-04-06 08:03:47', '2025-04-06 08:03:47', NULL);
INSERT INTO `threads` VALUES ('105', 'Lập trình PLC Siemens S7-1200 cho người mới bắt đầu', 'lap-trinh-plc-siemens-s7-1200-cho-nguoi-moi-bat-dau-49-277', '
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
        ', '/images/threads/male-worker-factory.webp', '
# Lập Trình PLC Siemens S7-1200 Cho Người Mới

PLC (Programmable Logic Controller) là trái tim của hệ thống tự động hóa. Hướng dẫn này sẽ giúp bạn bắt đầu với...', '\"[\\\"plc\\\",\\\"automation\\\",\\\"control\\\"]\"', '1', 'published', '3', '49', '14', '0', '0', '1', '0', NULL, NULL, NULL, '9', '4.80', '15', 'discussion', 'beginner', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'high', '[\"ISO\",\"ASME\"]', '0', '1', '2', '251', '23', '0', '4', '2', '1', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '0', NULL, NULL, '0.00', '\"[\\\"plc\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-09 17:57:53', NULL, '5', '2025-06-04 15:06:35', '2025-06-13 14:04:17', NULL);
INSERT INTO `threads` VALUES ('106', 'Thiết kế HMI hiệu quả - Best practices và tips', 'thiet-ke-hmi-hieu-qua-best-practices-va-tips-49-586', '
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
        ', '/images/threads/Professional Engineer.webp', '
# Thiết Kế HMI Hiệu Quả - Best Practices Và Tips

Human Machine Interface (HMI) là giao diện quan trọng giữa operator và máy móc.

## Nguyên Tắc Thiết Kế

###...', '\"[]\"', '2', 'published', '18', '49', '14', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.90', '11', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'low', '[\"ISO\",\"ASME\"]', '0', '1', '0', '81', '1', '19', '9', '5', '23', '0', '0', '0', '0', '0', '5', '6', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '0', NULL, NULL, '0.00', '\"[\\\"hmi\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-16 06:07:53', NULL, '1', '2025-04-07 02:28:46', '2025-06-14 20:43:41', NULL);
INSERT INTO `threads` VALUES ('107', 'Tích hợp robot ABB vào dây chuyền sản xuất - Case study', 'tich-hop-robot-abb-vao-day-chuyen-san-xuat-case-study-50-363', '
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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# Tích Hợp Robot ABB Vào Dây Chuyền Sản Xuất - Case Study

Dự án tích hợp robot ABB IRB 1600 vào dây chuyền welding tại nhà máy ô tô.

## Thông Tin Dự Án

###...', '\"[\\\"robot\\\",\\\"robotics\\\",\\\"automation\\\"]\"', '3', 'published', '25', '50', '15', '0', '0', '1', '0', NULL, NULL, NULL, '7', '3.70', '14', 'discussion', 'intermediate', 'manufacturing', '\"[\\\"RobotStudio\\\"]\"', 'manufacturing', '\"{\\\"payload\\\":\\\"6 kg\\\",\\\"reach\\\":\\\"1450 mm\\\",\\\"repeatability\\\":\\\"\\\\u00b10.1 mm\\\",\\\"speed\\\":\\\"2.3 m\\\\\\/s\\\"}\"', '0', '1', 'low', '[\"ISO 10218\",\"IEC 61508\"]', '0', '1', '2', '455', '45', '10', '1', '1', '7', '0', '0', '0', '0', '0', '1', '2', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '1', NULL, NULL, '0.00', '\"[\\\"robot\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-13 17:57:53', NULL, '1', '2025-06-30 12:22:43', '2025-07-03 23:01:11', NULL);
INSERT INTO `threads` VALUES ('108', 'So sánh robot KUKA vs Fanuc vs ABB - Ưu nhược điểm', 'so-sanh-robot-kuka-vs-fanuc-vs-abb-uu-nhuoc-diem-50-317', '
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
        ', '/images/threads/Mechanical-Engineering-thumbnail.jpg', '
# So Sánh Robot KUKA vs Fanuc vs ABB - Ưu Nhược Điểm

Chọn robot phù hợp cho ứng dụng cụ thể cần hiểu rõ đặc điểm từng hãng.

## ABB Robotics

### Ưu Điểm:
- *...', '\"[\\\"robot\\\",\\\"robotics\\\",\\\"automation\\\"]\"', '3', 'published', '1', '50', '15', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.80', '11', 'discussion', 'intermediate', 'manufacturing', '\"[\\\"RobotStudio\\\"]\"', 'manufacturing', '\"{\\\"payload\\\":\\\"6 kg\\\",\\\"reach\\\":\\\"1450 mm\\\",\\\"repeatability\\\":\\\"\\\\u00b10.1 mm\\\",\\\"speed\\\":\\\"2.3 m\\\\\\/s\\\"}\"', '1', '0', 'high', '[\"ISO 10218\",\"IEC 61508\"]', '0', '1', '1', '67', '7', '18', '5', '2', '28', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[\\\"robot\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-12 15:45:53', NULL, '2', '2025-05-02 08:45:08', '2025-05-02 08:45:08', NULL);
INSERT INTO `threads` VALUES ('109', 'Tích hợp robot ABB vào dây chuyền sản xuất - Case study', 'tich-hop-robot-abb-vao-day-chuyen-san-xuat-case-study-51-865', '
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
        ', '/images/threads/Professional Engineer.webp', '
# Tích Hợp Robot ABB Vào Dây Chuyền Sản Xuất - Case Study

Dự án tích hợp robot ABB IRB 1600 vào dây chuyền welding tại nhà máy ô tô.

## Thông Tin Dự Án

###...', '\"[\\\"robot\\\",\\\"robotics\\\",\\\"automation\\\"]\"', '3', 'published', '16', '51', '15', '0', '0', '1', '0', NULL, NULL, NULL, '8', '4.60', '12', 'discussion', 'intermediate', 'manufacturing', '\"null\"', 'manufacturing', '\"{\\\"payload\\\":\\\"6 kg\\\",\\\"reach\\\":\\\"1450 mm\\\",\\\"repeatability\\\":\\\"\\\\u00b10.1 mm\\\",\\\"speed\\\":\\\"2.3 m\\\\\\/s\\\"}\"', '1', '1', 'low', '[\"ISO 10218\",\"IEC 61508\"]', '1', '0', '0', '218', '42', '9', '9', '3', '1', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '1', NULL, NULL, '0.00', '\"[\\\"robot\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-13 05:00:53', NULL, '1', '2025-05-25 05:44:19', '2025-05-25 05:44:19', NULL);
INSERT INTO `threads` VALUES ('110', 'So sánh robot KUKA vs Fanuc vs ABB - Ưu nhược điểm', 'so-sanh-robot-kuka-vs-fanuc-vs-abb-uu-nhuoc-diem-51-374', '
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
        ', '/images/threads/male-worker-factory.webp', '
# So Sánh Robot KUKA vs Fanuc vs ABB - Ưu Nhược Điểm

Chọn robot phù hợp cho ứng dụng cụ thể cần hiểu rõ đặc điểm từng hãng.

## ABB Robotics

### Ưu Điểm:
- *...', '\"[\\\"robot\\\",\\\"robotics\\\",\\\"automation\\\"]\"', '3', 'published', '13', '51', '15', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.90', '21', 'discussion', 'intermediate', 'manufacturing', '\"null\"', 'manufacturing', '\"{\\\"payload\\\":\\\"6 kg\\\",\\\"reach\\\":\\\"1450 mm\\\",\\\"repeatability\\\":\\\"\\\\u00b10.1 mm\\\",\\\"speed\\\":\\\"2.3 m\\\\\\/s\\\"}\"', '1', '1', 'normal', '[\"ISO 10218\",\"IEC 61508\"]', '0', '1', '3', '23', '16', '16', '6', '3', '32', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '1', NULL, NULL, '0.00', '\"[\\\"robot\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-26 16:37:53', NULL, '1', '2025-04-30 21:36:09', '2025-04-30 21:36:09', NULL);
INSERT INTO `threads` VALUES ('111', 'Tích hợp robot ABB vào dây chuyền sản xuất - Case study', 'tich-hop-robot-abb-vao-day-chuyen-san-xuat-case-study-52-300', '
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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# Tích Hợp Robot ABB Vào Dây Chuyền Sản Xuất - Case Study

Dự án tích hợp robot ABB IRB 1600 vào dây chuyền welding tại nhà máy ô tô.

## Thông Tin Dự Án

###...', '\"[\\\"robot\\\",\\\"robotics\\\",\\\"automation\\\"]\"', '3', 'published', '25', '52', '15', '0', '0', '1', '0', NULL, NULL, NULL, '8', '3.60', '8', 'discussion', 'intermediate', 'manufacturing', '\"null\"', 'manufacturing', '\"{\\\"payload\\\":\\\"6 kg\\\",\\\"reach\\\":\\\"1450 mm\\\",\\\"repeatability\\\":\\\"\\\\u00b10.1 mm\\\",\\\"speed\\\":\\\"2.3 m\\\\\\/s\\\"}\"', '1', '0', 'low', '[\"ISO 10218\",\"IEC 61508\"]', '1', '0', '0', '330', '16', '4', '10', '3', '8', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '1', '1', NULL, NULL, '0.00', '\"[\\\"robot\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-18 17:57:53', NULL, '5', '2025-06-14 02:26:03', '2025-06-14 02:26:03', NULL);
INSERT INTO `threads` VALUES ('112', 'So sánh robot KUKA vs Fanuc vs ABB - Ưu nhược điểm', 'so-sanh-robot-kuka-vs-fanuc-vs-abb-uu-nhuoc-diem-52-931', '
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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# So Sánh Robot KUKA vs Fanuc vs ABB - Ưu Nhược Điểm

Chọn robot phù hợp cho ứng dụng cụ thể cần hiểu rõ đặc điểm từng hãng.

## ABB Robotics

### Ưu Điểm:
- *...', '\"[\\\"robot\\\",\\\"robotics\\\",\\\"automation\\\"]\"', '3', 'published', '13', '52', '15', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.40', '25', 'discussion', 'intermediate', 'manufacturing', '\"null\"', 'manufacturing', '\"{\\\"payload\\\":\\\"6 kg\\\",\\\"reach\\\":\\\"1450 mm\\\",\\\"repeatability\\\":\\\"\\\\u00b10.1 mm\\\",\\\"speed\\\":\\\"2.3 m\\\\\\/s\\\"}\"', '0', '0', 'high', '[\"ISO 10218\",\"IEC 61508\"]', '0', '0', '1', '385', '20', '7', '4', '2', '23', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '1', NULL, NULL, '0.00', '\"[\\\"robot\\\"]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-24 17:57:53', NULL, '4', '2025-06-11 16:26:21', '2025-06-11 16:26:21', NULL);
INSERT INTO `threads` VALUES ('113', 'Thảo luận về Thảo luận chung - Sensors & Actuators - Chia sẻ kinh nghiệm', 'thao-luan-ve-thao-luan-chung-sensors-actuators-chia-se-kinh-nghiem-53-597', '
# Chào Mừng Đến Với Forum Thảo luận chung - Sensors & Actuators

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Thảo luận chung - Sensors & Actuators**.

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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# Chào Mừng Đến Với Forum Thảo luận chung - Sensors & Actuators

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Thảo luận chung -...', '\"[]\"', '2', 'published', '28', '53', '16', '0', '0', '0', '0', NULL, NULL, NULL, '8', '4.10', '6', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'normal', '[\"ISO\",\"ASME\"]', '0', '0', '2', '306', '27', '18', '10', '5', '28', '0', '0', '0', '0', '0', '5', '6', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-22 17:57:53', NULL, '2', '2025-05-02 22:05:25', '2025-05-02 22:05:25', NULL);
INSERT INTO `threads` VALUES ('114', 'Hỏi đáp kỹ thuật về Thảo luận chung - Sensors & Actuators', 'hoi-dap-ky-thuat-ve-thao-luan-chung-sensors-actuators-53-368', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Thảo luận chung - Sensors & Actuators

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Thảo luận chung - Sensors & Actuators.

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
        ', '/images/threads/Professional Engineer.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Thảo luận chung - Sensors & Actuators

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Thảo luận chung...', '\"[]\"', '2', 'published', '12', '53', '16', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.50', '17', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'normal', '[\"ISO\",\"ASME\"]', '1', '0', '2', '295', '15', '16', '7', '4', '30', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '1', '0', '1', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-16 17:57:53', NULL, '1', '2025-05-27 08:34:52', '2025-05-27 08:34:52', NULL);
INSERT INTO `threads` VALUES ('115', 'Thảo luận về Hỏi đáp - Sensors & Actuators - Chia sẻ kinh nghiệm', 'thao-luan-ve-hoi-dap-sensors-actuators-chia-se-kinh-nghiem-54-762', '
# Chào Mừng Đến Với Forum Hỏi đáp - Sensors & Actuators

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Hỏi đáp - Sensors & Actuators**.

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
        ', '/images/threads/mj_11351_4.jpg', '
# Chào Mừng Đến Với Forum Hỏi đáp - Sensors & Actuators

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Hỏi đáp - Sensors & Actua...', '\"[]\"', '2', 'published', '29', '54', '16', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.20', '10', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'low', '[\"ISO\",\"ASME\"]', '1', '0', '1', '461', '4', '17', '5', '2', '7', '0', '0', '0', '0', '0', '2', '3', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-22 17:57:53', NULL, '5', '2025-05-16 07:39:32', '2025-05-16 07:39:32', NULL);
INSERT INTO `threads` VALUES ('116', 'Hỏi đáp kỹ thuật về Hỏi đáp - Sensors & Actuators', 'hoi-dap-ky-thuat-ve-hoi-dap-sensors-actuators-54-128', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Hỏi đáp - Sensors & Actuators

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Hỏi đáp - Sensors & Actuators.

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
        ', '/images/threads/Professional Engineer.webp', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Hỏi đáp - Sensors & Actuators

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Hỏi đáp - Sensors & Act...', '\"[]\"', '2', 'published', '6', '54', '16', '0', '0', '0', '0', NULL, NULL, NULL, '9', '4.00', '23', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '0', 'high', '[\"ISO\",\"ASME\"]', '0', '0', '0', '108', '40', '10', '6', '3', '29', '0', '0', '0', '0', '0', '3', '4', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '1', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-17 17:57:53', NULL, '5', '2025-04-16 01:05:21', '2025-06-21 13:58:23', NULL);
INSERT INTO `threads` VALUES ('117', 'Thảo luận về Kinh nghiệm - Sensors & Actuators - Chia sẻ kinh nghiệm', 'thao-luan-ve-kinh-nghiem-sensors-actuators-chia-se-kinh-nghiem-55-589', '
# Chào Mừng Đến Với Forum Kinh nghiệm - Sensors & Actuators

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Kinh nghiệm - Sensors & Actuators**.

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
        ', '/images/threads/ImageForArticle_20492_16236782958233468.webp', '
# Chào Mừng Đến Với Forum Kinh nghiệm - Sensors & Actuators

Đây là nơi chúng ta cùng nhau thảo luận, chia sẻ kinh nghiệm và học hỏi về **Kinh nghiệm - Sensors...', '\"[]\"', '2', 'published', '2', '55', '16', '0', '0', '0', '0', NULL, NULL, NULL, '10', '4.90', '9', 'discussion', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '0', '1', 'normal', '[\"ISO\",\"ASME\"]', '1', '1', '1', '433', '41', '4', '2', '1', '29', '0', '0', '0', '0', '0', '1', '2', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-12 17:57:53', NULL, '4', '2025-05-31 06:29:13', '2025-06-24 10:29:12', NULL);
INSERT INTO `threads` VALUES ('118', 'Hỏi đáp kỹ thuật về Kinh nghiệm - Sensors & Actuators', 'hoi-dap-ky-thuat-ve-kinh-nghiem-sensors-actuators-55-705', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Kinh nghiệm - Sensors & Actuators

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Kinh nghiệm - Sensors & Actuators.

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
        ', '/images/threads/Mechanical-Engineering-thumbnail.jpg', '
# Q&A - Hỏi Đáp Kỹ Thuật Về Kinh nghiệm - Sensors & Actuators

Thread này dành cho việc **hỏi đáp nhanh** các vấn đề kỹ thuật liên quan đến Kinh nghiệm - Senso...', '\"[]\"', '2', 'published', '21', '55', '16', '0', '0', '0', '0', NULL, NULL, NULL, '7', '4.80', '22', 'question', 'intermediate', 'tutorial', '\"null\"', 'manufacturing', '\"null\"', '1', '0', 'critical', '[\"ISO\",\"ASME\"]', '1', '0', '0', '79', '20', '19', '7', '4', '29', '0', '0', '0', '0', '0', '4', '5', '\"[\\\"PDF\\\",\\\"DOC\\\"]\"', '0', '0', '0', NULL, NULL, '0.00', '\"[]\"', '\"[\\\"ISO 9001\\\",\\\"ASME\\\"]\"', NULL, '2025-06-25 17:57:53', NULL, 'approved', '0', NULL, NULL, NULL, NULL, NULL, '2025-06-19 17:57:53', NULL, '5', '2025-06-12 07:58:20', '2025-06-12 07:58:20', NULL);

-- Structure for table `user_devices`
DROP TABLE IF EXISTS `user_devices`;
CREATE TABLE `user_devices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `device_fingerprint` varchar(191) NOT NULL,
  `device_name` varchar(191) DEFAULT NULL,
  `device_type` varchar(191) DEFAULT NULL,
  `browser` varchar(191) DEFAULT NULL,
  `browser_version` varchar(191) DEFAULT NULL,
  `platform` varchar(191) DEFAULT NULL,
  `platform_version` varchar(191) DEFAULT NULL,
  `user_agent` varchar(191) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `country` varchar(191) DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `is_trusted` tinyint(1) NOT NULL DEFAULT 0,
  `first_seen_at` timestamp NULL DEFAULT NULL,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `trusted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Structure for table `users`
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `company_name` varchar(191) DEFAULT NULL COMMENT 'Tên công ty (cho supplier, manufacturer, brand)',
  `business_license` varchar(191) DEFAULT NULL COMMENT 'Số giấy phép kinh doanh',
  `tax_code` varchar(191) DEFAULT NULL COMMENT 'Mã số thuế',
  `username` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `role` varchar(50) DEFAULT 'member',
  `role_group` enum('system_management','community_management','community_members','business_partners') DEFAULT NULL COMMENT 'Nhóm phân quyền chính',
  `role_permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Cache permissions cho role' CHECK (json_valid(`role_permissions`)),
  `role_updated_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian cập nhật role gần nhất',
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `status` varchar(191) NOT NULL DEFAULT 'Registered',
  `avatar` varchar(191) DEFAULT NULL,
  `about_me` text DEFAULT NULL,
  `business_description` text DEFAULT NULL COMMENT 'Mô tả doanh nghiệp',
  `business_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Danh mục kinh doanh chính' CHECK (json_valid(`business_categories`)),
  `business_phone` varchar(191) DEFAULT NULL COMMENT 'Số điện thoại doanh nghiệp',
  `business_email` varchar(191) DEFAULT NULL COMMENT 'Email doanh nghiệp',
  `business_address` text DEFAULT NULL COMMENT 'Địa chỉ doanh nghiệp',
  `is_verified_business` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Đã xác thực doanh nghiệp',
  `business_verified_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian xác thực doanh nghiệp',
  `verified_by` bigint(20) unsigned DEFAULT NULL,
  `subscription_level` enum('free','basic','premium','enterprise') NOT NULL DEFAULT 'free' COMMENT 'Gói dịch vụ',
  `business_rating` decimal(3,2) NOT NULL DEFAULT 0.00 COMMENT 'Đánh giá doanh nghiệp (0-5.0)',
  `total_reviews` int(11) NOT NULL DEFAULT 0 COMMENT 'Tổng số đánh giá',
  `website` varchar(191) DEFAULT NULL,
  `location` varchar(191) DEFAULT NULL,
  `country_id` bigint(20) unsigned DEFAULT NULL,
  `region_id` bigint(20) unsigned DEFAULT NULL,
  `work_locations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`work_locations`)),
  `expertise_regions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`expertise_regions`)),
  `signature` text DEFAULT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `reaction_score` int(11) NOT NULL DEFAULT 0,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `last_activity` text DEFAULT NULL,
  `setup_progress` tinyint(4) NOT NULL DEFAULT 0,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `banned_at` timestamp NULL DEFAULT NULL,
  `banned_reason` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_name_index` (`name`),
  KEY `users_username_index` (`username`),
  KEY `users_status_role_index` (`status`,`role`),
  KEY `users_last_seen_at_index` (`last_seen_at`),
  KEY `users_region_id_foreign` (`region_id`),
  KEY `users_country_id_region_id_index` (`country_id`,`region_id`),
  KEY `users_country_id_is_active_index` (`country_id`,`is_active`),
  KEY `users_verified_by_foreign` (`verified_by`),
  KEY `users_role_index` (`role`),
  KEY `users_email_performance_idx` (`email`),
  KEY `users_username_performance_idx` (`username`),
  KEY `users_role_performance_idx` (`role`),
  KEY `users_created_at_performance_idx` (`created_at`),
  CONSTRAINT `users_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data for table `users`
INSERT INTO `users` VALUES ('1', 'Nguyễn Quản Trị', NULL, NULL, NULL, 'admin', 'admin@mechamap.vn', 'super_admin', 'system_management', '[\"manage-system\",\"manage-infrastructure\",\"manage-database\",\"manage-security\",\"access-super-admin\",\"view-system-logs\",\"manage-backups\",\"view-users\",\"create-users\",\"update-users\",\"delete-users\",\"ban-users\",\"manage-user-roles\",\"verify-business-accounts\",\"manage-subscriptions\",\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-marketplace\",\"approve-products\",\"manage-orders\",\"manage-payments\",\"view-marketplace-analytics\",\"manage-seller-accounts\",\"handle-disputes\",\"manage-commissions\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\",\"access-admin-panel\",\"access-system-admin\",\"access-content-admin\",\"access-marketplace-admin\",\"access-community-admin\",\"view-content\",\"create-threads\",\"create-comments\",\"upload-files\",\"send-messages\",\"create-polls\",\"rate-products\",\"write-reviews\",\"sell-products\",\"manage-own-products\",\"view-sales-analytics\",\"manage-business-profile\",\"access-seller-dashboard\",\"upload-technical-files\",\"manage-cad-files\",\"access-b2b-features\",\"manage-faqs\",\"view-faqs\",\"manage-knowledge-base\",\"manage-cad-library\",\"manage-showcases\",\"manage-roles-permissions\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-1.jpg', 'Quản trị viên hệ thống MechaMap - Chuyên gia về cơ khí và tự động hóa với 15+ năm kinh nghiệm trong lĩnh vực.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', 'https://mechamap.vn', 'TP. Hồ Chí Minh, Việt Nam', NULL, NULL, NULL, NULL, 'Building the future of Vietnamese mechanical engineering community 🔧', '10000', '500', '2025-07-09 13:44:58', 'admin.marketplace.products.index', '100', '2025-06-25 17:57:15', '1', NULL, '$2y$12$9howXg1mIIopEyE6VFt.q.5kxo0SPzBkfTpNBs7OEHmm8Hv64M02e', 'RUEUx6qfxAaTpUiyPDX5tBxsDWh0QKIdm2IovMFCuEFzepv7fNEgSlJ6EN1d', NULL, NULL, '2025-01-10 07:16:19', '2025-07-09 13:44:58');
INSERT INTO `users` VALUES ('2', 'Trần Hệ Thống', NULL, NULL, NULL, 'sysadmin', 'sysadmin@mechamap.vn', 'super_admin', 'system_management', '[\"manage-system\",\"manage-infrastructure\",\"manage-database\",\"manage-security\",\"access-super-admin\",\"view-system-logs\",\"manage-backups\",\"view-users\",\"create-users\",\"update-users\",\"delete-users\",\"ban-users\",\"manage-user-roles\",\"verify-business-accounts\",\"manage-subscriptions\",\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-marketplace\",\"approve-products\",\"manage-orders\",\"manage-payments\",\"view-marketplace-analytics\",\"manage-seller-accounts\",\"handle-disputes\",\"manage-commissions\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\",\"access-admin-panel\",\"access-system-admin\",\"access-content-admin\",\"access-marketplace-admin\",\"access-community-admin\",\"view-content\",\"create-threads\",\"create-comments\",\"upload-files\",\"send-messages\",\"create-polls\",\"rate-products\",\"write-reviews\",\"sell-products\",\"manage-own-products\",\"view-sales-analytics\",\"manage-business-profile\",\"access-seller-dashboard\",\"upload-technical-files\",\"manage-cad-files\",\"access-b2b-features\",\"manage-faqs\",\"view-faqs\",\"manage-knowledge-base\",\"manage-cad-library\",\"manage-showcases\",\"manage-roles-permissions\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-2.jpg', 'System Administrator - Chuyên trách bảo trì và phát triển hệ thống kỹ thuật.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Hà Nội, Việt Nam', NULL, NULL, NULL, NULL, 'System reliability & performance optimization specialist', '8500', '420', '2025-06-25 06:30:05', NULL, '100', '2025-06-25 17:57:15', '1', NULL, '$2y$12$GrGBMiLUVjBVVzTBljLxV.pxavyLiD7bjt/F/TQRH2cFbnxA8dZsG', NULL, NULL, NULL, '2025-03-02 17:10:35', '2025-03-02 17:10:35');
INSERT INTO `users` VALUES ('3', 'Lê Kiểm Duyệt', NULL, NULL, NULL, 'moderator', 'moderator@mechamap.vn', 'content_moderator', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-3.jpg', 'Moderator chuyên nghiệp - Kỹ sư Cơ khí chuyên ngành CAD/CAM với 10 năm kinh nghiệm. Đảm bảo chất lượng nội dung và hỗ trợ cộng đồng.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', 'https://cadcam-expert.com', 'Đà Nẵng, Việt Nam', NULL, NULL, NULL, NULL, 'Quality content for better community 📝', '5000', '380', '2025-06-20 13:42:38', 'admin.dashboard', '100', '2025-06-25 17:57:15', '1', NULL, '$2y$12$gtLTTpAEHPLcuI6blkfYAeN/0xeKaqd0cDginGwYUKLQI4Qtl2XPi', NULL, NULL, NULL, '2025-02-23 22:12:03', '2025-07-04 01:24:56');
INSERT INTO `users` VALUES ('4', 'Phạm Nội Dung', NULL, NULL, NULL, 'content_mod', 'content@mechamap.vn', 'content_moderator', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-4.jpg', 'Content Moderator - Chuyên gia về vật liệu kỹ thuật và quy trình sản xuất. Hỗ trợ review và kiểm duyệt nội dung chuyên môn.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Hải Phòng, Việt Nam', NULL, NULL, NULL, NULL, 'Technical accuracy & community guidelines', '4200', '310', '2025-06-11 14:52:34', NULL, '100', '2025-06-25 17:57:15', '1', NULL, '$2y$12$toYcmCrbDP7s5DGSoI9Nye7GpSEDLr7EybB6A/wn94x89gS5Nwt3e', NULL, NULL, NULL, '2025-05-26 14:41:03', '2025-05-26 14:41:03');
INSERT INTO `users` VALUES ('5', 'Ngô Quản Lý', NULL, NULL, NULL, 'ngo_manager', 'manager@mechamap.vn', 'content_moderator', 'community_management', '[\"moderate-content\",\"approve-content\",\"delete-content\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"view-reports\",\"manage-reports\",\"view-content\",\"create-threads\",\"create-comments\",\"upload-files\",\"send-messages\"]', '2025-06-28 09:21:18', NULL, 'active', '/images/users/avatar-5.jpg', 'Community Manager - Chuyên gia quản lý cộng đồng với background về Mechanical Engineering. Đảm bảo môi trường thảo luận tích cực và chuyên nghiệp.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', 'https://community-expert.vn', 'Hà Nội, Việt Nam', NULL, NULL, NULL, NULL, 'Building stronger engineering community 🤝', '4800', '350', '2025-03-25 12:08:19', NULL, '100', '2025-06-25 17:57:15', '1', NULL, '$2y$12$Qbmqg5sVEX.CBf1SW1IjPuQPcY2v23PpMmx2fz4gYWW6KJQai2Ble', NULL, NULL, NULL, '2025-03-06 20:42:07', '2025-03-06 20:42:07');
INSERT INTO `users` VALUES ('6', 'Sarah Wilson', NULL, NULL, NULL, 'sarah_moderator', 'sarah.wilson@mechamap.vn', 'content_moderator', 'community_management', '[\"moderate-content\",\"approve-content\",\"delete-content\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"view-reports\",\"manage-reports\",\"view-content\",\"create-threads\",\"create-comments\",\"upload-files\",\"send-messages\"]', '2025-06-28 09:21:18', NULL, 'active', '/images/users/avatar-6.jpg', 'International Moderator - Mechanical Engineer from Australia working in Vietnam. Helps bridge international engineering standards and practices.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', 'https://intl-engineering.com', 'TP. Hồ Chí Minh, Việt Nam', NULL, NULL, NULL, NULL, 'Global engineering perspectives 🌏', '3900', '280', '2025-03-02 12:25:30', 'admin.dashboard', '100', '2025-06-25 17:57:16', '1', NULL, '$2y$12$5VHuQZEUx77B4Zg0PvzCYOc7dkZOtb5E6fweIbkAAOhLfFNGtc.8a', NULL, NULL, NULL, '2025-02-01 14:10:23', '2025-02-01 14:10:23');
INSERT INTO `users` VALUES ('7', 'Hoàng Cơ Khí', NULL, NULL, NULL, 'hoang_engineer', 'hoang.engineer@gmail.com', 'senior_member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-7.jpg', 'Senior Mechanical Engineer tại Samsung Vietnam. Chuyên gia thiết kế sản phẩm điện tử với 8+ năm kinh nghiệm. Passionate về automation và Industry 4.0.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', 'https://linkedin.com/in/hoang-engineer', 'Bắc Ninh, Việt Nam', NULL, NULL, NULL, NULL, 'Innovation through precision engineering ⚙️', '3500', '240', '2025-07-04 01:42:35', 'marketplace.cart.data', '95', '2025-06-25 17:57:16', '1', NULL, '$2y$12$a0P4xAJOw6lrbroOsVsY6er0/iKRLJiZWc/CiiH22CsuUYyolPthK', NULL, NULL, NULL, '2025-02-02 01:44:32', '2025-07-04 01:42:35');
INSERT INTO `users` VALUES ('8', 'Đỗ Tự Động', NULL, NULL, NULL, 'do_automation', 'do.automation@company.vn', 'senior_member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-8.jpg', 'Automation Engineer chuyên PLC/SCADA. Lead Engineer tại một công ty OEM hàng đầu. Có kinh nghiệm triển khai hơn 50 dự án tự động hóa.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', 'https://automation-blog.vn', 'Bình Dương, Việt Nam', NULL, NULL, NULL, NULL, 'Automate everything! 🤖', '3200', '195', '2025-06-04 17:19:33', NULL, '90', '2025-06-25 17:57:16', '1', NULL, '$2y$12$DdN6dYqAvgQNN1gCZm1c2OtlmzYm7BdUtnawWlYiBKa.1NNuCxNba', NULL, NULL, NULL, '2025-03-30 23:09:29', '2025-03-30 23:09:29');
INSERT INTO `users` VALUES ('9', 'Vũ CAD Master', NULL, NULL, NULL, 'vu_cadmaster', 'vu.cad@designfirm.vn', 'senior_member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-9.jpg', 'Senior CAD/CAM Specialist. Master trong SolidWorks, Fusion 360, và CNC programming. Trainer chính thức của SolidWorks Vietnam.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', 'https://cadtraining.vn', 'TP. Hồ Chí Minh, Việt Nam', NULL, NULL, NULL, NULL, 'Design with precision, manufacture with excellence 📐', '4100', '320', '2025-07-09 13:48:14', 'marketplace.cart.data', '100', '2025-06-25 17:57:16', '1', NULL, '$2y$12$DbefuA5hNcOj/ssMPoT5..mEhZDReKeA9nMiSgUH./qIxaQzCRzxu', NULL, NULL, NULL, '2025-01-28 10:02:52', '2025-07-09 13:48:14');
INSERT INTO `users` VALUES ('10', 'Lý Vật Liệu', NULL, NULL, NULL, 'ly_materials', 'ly.materials@research.edu.vn', 'senior_member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-10.jpg', 'Materials Engineer & Researcher tại ĐH Bách Khoa TP.HCM. PhD in Materials Science. Chuyên gia về composite và smart materials.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', 'https://materials-research.edu.vn', 'TP. Hồ Chí Minh, Việt Nam', NULL, NULL, NULL, NULL, 'Advanced materials for future technology 🧪', '2800', '180', '2025-06-15 05:44:58', NULL, '85', '2025-06-25 17:57:16', '1', NULL, '$2y$12$UHmH9Viz.8sYEE0UCvu8SeEBUph/wY421Iy9jyGgIG3ZqFnJ7ZQM2', NULL, NULL, NULL, '2025-06-13 03:41:47', '2025-06-13 03:41:47');
INSERT INTO `users` VALUES ('11', 'Michael Chen', NULL, NULL, NULL, 'michael_robotics', 'michael.chen@robotics.vn', 'senior_member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-1.jpg', 'Robotics Engineer - Chuyên gia về robot công nghiệp và AI. Lead Engineer tại công ty robotics hàng đầu Việt Nam.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', 'https://robotics-vietnam.com', 'Bình Dương, Việt Nam', NULL, NULL, NULL, NULL, 'Robots are the future of manufacturing 🤖', '3800', '290', '2025-05-29 12:33:20', NULL, '95', '2025-06-25 17:57:16', '1', NULL, '$2y$12$dePlr7Cu5hjQXZFm0/HDq.sP4f48H1sDzpDv.PtwJLnRFT09Ika4S', NULL, NULL, NULL, '2025-04-05 18:29:51', '2025-04-05 18:29:51');
INSERT INTO `users` VALUES ('12', 'Trương Thiết Kế', NULL, NULL, NULL, 'truong_design', 'truong.design@consultant.vn', 'senior_member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-2.jpg', 'Senior Design Consultant - 12 năm kinh nghiệm thiết kế sản phẩm công nghiệp. Chuyên gia về Design for Manufacturing (DFM).', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', 'https://design-consultant.vn', 'Hà Nội, Việt Nam', NULL, NULL, NULL, NULL, 'Good design is good business 💡', '3300', '220', '2025-05-25 16:26:25', NULL, '90', '2025-06-25 17:57:17', '1', NULL, '$2y$12$e7oqEe4CpkT/42G7qzp8duB5N/ZUzh3PbJeXnD.lqYITISD1ihvxu', NULL, NULL, NULL, '2025-04-13 03:11:23', '2025-04-13 03:11:23');
INSERT INTO `users` VALUES ('13', 'Nguyễn Học Viên', NULL, NULL, NULL, 'nguyen_student', 'nguyen.student@university.edu.vn', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-3.jpg', 'Sinh viên năm 4 ngành Cơ khí tại ĐH Bách Khoa Hà Nội. Đang tìm hiểu về thiết kế máy và automation. Mong muốn học hỏi từ các anh chị trong ngành.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Hà Nội, Việt Nam', NULL, NULL, NULL, NULL, 'Learning every day to become better engineer 📚', '150', '25', '2025-07-09 13:51:10', 'marketplace.cart.data', '60', '2025-06-25 17:57:17', '1', NULL, '$2y$12$6hnjeu6TtM1eMmF6Bp9/HebvIAhPbBOrprBpTXwTZFc8Bl7juEaXK', NULL, NULL, NULL, '2025-03-26 21:58:32', '2025-07-09 13:51:10');
INSERT INTO `users` VALUES ('14', 'Trần Fresher', NULL, NULL, NULL, 'tran_fresher', 'tran.fresher@gmail.com', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-4.jpg', 'Fresh Graduate Engineer vừa ra trường. Đang tìm việc trong lĩnh vực thiết kế cơ khí. Có kinh nghiệm thực tập tại công ty sản xuất auto parts.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Đồng Nai, Việt Nam', NULL, NULL, NULL, NULL, 'Ready to contribute to engineering world! 🚀', '80', '12', '2025-07-04 02:02:16', 'marketplace.cart.data', '45', '2025-06-25 17:57:17', '1', NULL, '$2y$12$iO98txk1C2BzSjHots4yo.88eJVB47Nkk/l6YQYUBCpcbtK12WpLS', NULL, NULL, NULL, '2025-05-11 08:11:40', '2025-07-04 02:02:16');
INSERT INTO `users` VALUES ('15', 'Lê Kỹ Thuật', NULL, NULL, NULL, 'le_technician', 'le.technician@workshop.vn', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-5.jpg', 'Kỹ thuật viên CNC với 3 năm kinh nghiệm. Chuyên gia công chi tiết chính xác và setup máy. Muốn nâng cao kiến thức về programming và troubleshooting.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Bình Dương, Việt Nam', NULL, NULL, NULL, NULL, 'Precision machining specialist 🔩', '320', '45', '2025-04-09 12:08:08', NULL, '70', '2025-06-25 17:57:17', '1', NULL, '$2y$12$dTdAAT7kJBy3bCEZyTStAeiZHbzMzCeU6vXYCyxXGDPEfZ9T6sZ6m', NULL, NULL, NULL, '2025-03-25 00:24:17', '2025-03-25 00:24:17');
INSERT INTO `users` VALUES ('16', 'Phạm Tìm Hiểu', NULL, NULL, NULL, 'pham_curious', 'pham.curious@engineer.com', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-6.jpg', 'Junior Engineer yêu thích tìm tòi công nghệ mới. Đang học thêm về IoT và Smart Manufacturing. Thích chia sẻ và học hỏi kinh nghiệm từ cộng đồng.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Cần Thơ, Việt Nam', NULL, NULL, NULL, NULL, 'Curiosity drives innovation 💡', '180', '28', '2025-05-06 13:24:05', NULL, '55', '2025-06-25 17:57:17', '1', NULL, '$2y$12$ar.N6zfMgaRe7O70LViQ9u.DFRDyzKlgbwgDUvKGULOgrhJEZ2K8G', NULL, NULL, NULL, '2025-04-16 21:46:27', '2025-04-16 21:46:27');
INSERT INTO `users` VALUES ('17', 'Bùi Thực Tập', NULL, NULL, NULL, 'bui_intern', 'bui.intern@company.vn', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-7.jpg', 'Intern Engineer tại công ty sản xuất thiết bị y tế. Đang học về quality control và lean manufacturing. Mong muốn phát triển career trong ngành cơ khí chính xác.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'TP. Hồ Chí Minh, Việt Nam', NULL, NULL, NULL, NULL, 'Every expert was once a beginner 🌱', '90', '15', '2025-03-19 23:51:10', NULL, '40', '2025-06-25 17:57:18', '1', NULL, '$2y$12$YH1bD1TrHsrFnLKIcINIjO4JNaAV6g1aCmCC.8qtcaAp4BXAwR8l6', NULL, NULL, NULL, '2025-02-19 19:23:46', '2025-02-19 19:23:46');
INSERT INTO `users` VALUES ('18', 'Nguyễn Công Nghệ', NULL, NULL, NULL, 'nguyen_tech', 'nguyen.tech@factory.vn', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-8.jpg', 'Kỹ thuật viên bảo trì máy móc tại nhà máy dệt may. Chuyên về troubleshooting và preventive maintenance. Muốn học thêm về predictive maintenance.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Nam Định, Việt Nam', NULL, NULL, NULL, NULL, 'Keep machines running smoothly ⚙️', '220', '35', '2025-03-16 22:38:04', NULL, '65', '2025-06-25 17:57:18', '1', NULL, '$2y$12$rV5gmM9/jys8VBncKMvP7etTpIGIK0pw3dCQFh73NRCECJKL4j/tO', NULL, NULL, NULL, '2025-02-17 07:32:39', '2025-02-17 07:32:39');
INSERT INTO `users` VALUES ('19', 'Emma Johnson', NULL, NULL, NULL, 'emma_engineer', 'emma.johnson@international.com', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-9.jpg', 'Mechanical Engineer from UK working in Vietnam. Interested in sustainable manufacturing and green technology solutions.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Đà Nẵng, Việt Nam', NULL, NULL, NULL, NULL, 'Engineering for a sustainable future 🌱', '280', '42', '2025-06-07 11:12:04', NULL, '75', '2025-06-25 17:57:18', '1', NULL, '$2y$12$7BCaDqHZdlUki1e0hunoWu3bXDnnYK6Z5zgSQM1U.Qdh86/dQMiBm', NULL, NULL, NULL, '2025-04-30 22:07:18', '2025-04-30 22:07:18');
INSERT INTO `users` VALUES ('20', 'Đặng Sản Xuất', NULL, NULL, NULL, 'dang_production', 'dang.production@manufacturing.vn', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-10.jpg', 'Production Engineer tại công ty sản xuất linh kiện điện tử. Chuyên về process optimization và lean manufacturing.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Bắc Giang, Việt Nam', NULL, NULL, NULL, NULL, 'Optimize processes, maximize efficiency 📈', '350', '58', '2025-05-20 08:29:49', NULL, '80', '2025-06-25 17:57:18', '1', NULL, '$2y$12$LY0WpFPHIYB1ruKO9FbbpO7LnPVBNzrtr2oe3VYQrZoEHFihuU9Cm', NULL, NULL, NULL, '2025-05-03 21:50:20', '2025-05-03 21:50:20');
INSERT INTO `users` VALUES ('21', 'Võ Chất Lượng', NULL, NULL, NULL, 'vo_quality', 'vo.quality@qc.vn', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-1.jpg', 'Quality Control Engineer với 4 năm kinh nghiệm. Chuyên về ISO standards và quality management systems.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Hải Dương, Việt Nam', NULL, NULL, NULL, NULL, 'Quality is not an act, it is a habit ✅', '420', '68', '2025-06-24 01:07:36', NULL, '85', '2025-06-25 17:57:18', '1', NULL, '$2y$12$K/OkyZj7ANSJ3jfBiPEHzuSKXGzsrnDknqAgJXmmyYdAVX2Y5EBlq', NULL, NULL, NULL, '2025-04-24 03:59:37', '2025-04-24 03:59:37');
INSERT INTO `users` VALUES ('22', 'Lê Nghiên Cứu', NULL, NULL, NULL, 'le_research', 'le.research@university.edu.vn', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-2.jpg', 'Nghiên cứu sinh ngành Cơ khí tại ĐH Bách Khoa Hà Nội. Đang nghiên cứu về additive manufacturing và 3D printing.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Hà Nội, Việt Nam', NULL, NULL, NULL, NULL, 'Research today, innovate tomorrow 🔬', '190', '28', '2025-06-24 08:12:47', NULL, '70', '2025-06-25 17:57:18', '1', NULL, '$2y$12$kdDxceRzk0s6vy558rfNfOTb3/6DrY1LgMJD7RFDUyHUGU1np72y2', NULL, NULL, NULL, '2025-06-02 09:21:42', '2025-06-02 09:21:42');
INSERT INTO `users` VALUES ('23', 'James Smith', NULL, NULL, NULL, 'james_expat', 'james.smith@expat.com', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-3.jpg', 'Expat Engineer from Canada working in Vietnam manufacturing sector. Passionate about technology transfer and knowledge sharing.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Vũng Tàu, Việt Nam', NULL, NULL, NULL, NULL, 'Bridging global engineering practices 🌍', '310', '48', '2025-06-18 08:40:52', NULL, '78', '2025-06-25 17:57:19', '1', NULL, '$2y$12$YXn6Obh.7bO9JH7SNWAeI.oZ58R5.bQRa9wH6TmCu6wip89tMKoqO', NULL, NULL, NULL, '2025-05-02 07:26:55', '2025-05-02 07:26:55');
INSERT INTO `users` VALUES ('24', 'Hoàng Năng Lượng', NULL, NULL, NULL, 'hoang_energy', 'hoang.energy@renewable.vn', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-4.jpg', 'Kỹ sư năng lượng tái tạo. Chuyên về thiết kế và lắp đặt hệ thống solar và wind power. Quan tâm đến sustainable engineering.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Ninh Thuận, Việt Nam', NULL, NULL, NULL, NULL, 'Clean energy for a better future ☀️', '260', '38', '2025-05-28 02:49:32', NULL, '72', '2025-06-25 17:57:19', '1', NULL, '$2y$12$5kZhbup2OPPiH1HT9gBTq.VaR5Pei0dF7nBIQQzk0eGgjVtUf8u.K', NULL, NULL, NULL, '2025-05-14 07:34:41', '2025-05-14 07:34:41');
INSERT INTO `users` VALUES ('25', 'Phan Ô Tô', NULL, NULL, NULL, 'phan_automotive', 'phan.automotive@car.vn', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-5.jpg', 'Automotive Engineer tại công ty lắp ráp ô tô. Chuyên về engine systems và vehicle dynamics. Đam mê xe điện và autonomous vehicles.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Quảng Nam, Việt Nam', NULL, NULL, NULL, NULL, 'Driving the future of mobility 🚗', '380', '55', '2025-05-29 17:02:02', NULL, '82', '2025-06-25 17:57:19', '1', NULL, '$2y$12$7PygKMqZak2B0L56Rugp3u6rWQIyUSvGzz9Fj3tJKfpCZXT8tASTi', NULL, NULL, NULL, '2025-05-22 01:52:29', '2025-05-22 01:52:29');
INSERT INTO `users` VALUES ('26', 'Ngô Hàng Không', NULL, NULL, NULL, 'ngo_aerospace', 'ngo.aerospace@aviation.vn', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-6.jpg', 'Aerospace Engineer làm việc tại ngành hàng không Việt Nam. Chuyên về aircraft maintenance và aviation safety systems.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Hà Nội, Việt Nam', NULL, NULL, NULL, NULL, 'Safety first in aviation ✈️', '290', '44', '2025-06-01 06:41:58', NULL, '76', '2025-06-25 17:57:19', '1', NULL, '$2y$12$t7nd8s/ewZ/93TV0jAOxC.zBcY5N4koHo18i.sqBshYGDVlz81Hr6', NULL, NULL, NULL, '2025-05-09 12:52:09', '2025-05-09 12:52:09');
INSERT INTO `users` VALUES ('27', 'Trần Hàng Hải', NULL, NULL, NULL, 'tran_marine', 'tran.marine@shipyard.vn', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-7.jpg', 'Marine Engineer tại xưởng đóng tàu. Chuyên về ship design và marine propulsion systems. Quan tâm đến green shipping technology.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Hải Phòng, Việt Nam', NULL, NULL, NULL, NULL, 'Engineering the seas 🚢', '240', '36', '2025-06-30 16:00:51', NULL, '68', '2025-06-25 17:57:19', '1', NULL, '$2y$12$O6IE0Rj1YT3CT7BUlQ7hqObGSCg6CtnhtJmwlABmn6cWGJ4LzKzVe', NULL, NULL, NULL, '2025-05-12 01:53:23', '2025-05-12 01:53:23');
INSERT INTO `users` VALUES ('28', 'Demo Guest', NULL, NULL, NULL, 'demo_guest', 'guest@mechamap.vn', 'guest', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-8.jpg', 'Tài khoản demo cho khách tham quan hệ thống MechaMap.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Vietnam', NULL, NULL, NULL, NULL, 'Welcome to MechaMap Community!', '0', '0', '2025-06-08 09:12:12', NULL, '10', '2025-06-25 17:57:20', '0', NULL, '$2y$12$S90.9uQqN3bnqOWMY9WRFu.c9MbDsBD5rOeYcnN.rtABJC0ox4QZC', NULL, NULL, NULL, '2025-01-04 16:28:10', '2025-01-04 16:28:10');
INSERT INTO `users` VALUES ('29', 'Khách Tham Quan', NULL, NULL, NULL, 'visitor_user', 'visitor@example.com', 'guest', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-9.jpg', 'Khách tham quan quan tâm đến ngành cơ khí. Đang tìm hiểu về cộng đồng kỹ sư Việt Nam.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Hà Nội, Việt Nam', NULL, NULL, NULL, NULL, 'Exploring the engineering world 👀', '5', '1', '2025-05-22 10:06:58', NULL, '15', '2025-06-25 17:57:20', '0', NULL, '$2y$12$wTdwDLNVl.M2H0THmfU6/e5fzwfrMH3C4ExzAkvEC7yS9nibrO1SS', NULL, NULL, NULL, '2025-02-02 16:25:16', '2025-02-02 16:25:16');
INSERT INTO `users` VALUES ('30', 'Observer User', NULL, NULL, NULL, 'observer_guest', 'observer@guest.com', 'guest', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:07', NULL, 'active', '/images/users/avatar-10.jpg', 'Người quan sát muốn tìm hiểu về xu hướng công nghệ cơ khí hiện đại.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'TP. Hồ Chí Minh, Việt Nam', NULL, NULL, NULL, NULL, 'Learning by observing 📖', '2', '0', '2025-04-26 13:09:59', NULL, '12', '2025-06-25 17:57:20', '0', NULL, '$2y$12$FBEqE.g/JFrC3mGwdkynCOWpbpcd..wIcpBKiIR7ZYv5JCNVl8i1C', NULL, NULL, NULL, '2025-04-24 08:21:55', '2025-04-24 08:21:55');
INSERT INTO `users` VALUES ('31', 'Nguyễn Kỹ sư CAD', NULL, NULL, NULL, 'cad_engineer', 'cad.engineer@mechamap.com', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatar-1.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-06-16 01:31:43', NULL, '0', '2025-06-25 17:57:23', '1', NULL, '$2y$12$anlkb55QoaADZuTUdJSAQeUj90XtULfDnW029a.MEyZavLxGEQkFK', NULL, NULL, NULL, '2025-06-10 18:17:00', '2025-06-10 18:17:00');
INSERT INTO `users` VALUES ('32', 'Trần CNC Master', NULL, NULL, NULL, 'cnc_master', 'cnc.master@mechamap.com', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatar-2.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-01-22 14:51:47', NULL, '0', '2025-06-25 17:57:23', '1', NULL, '$2y$12$QELqYVpD0f11vZUCxmQNZ.lZZqQs9IZa5irjTtlenoxDMd.ugAhim', NULL, NULL, NULL, '2025-01-05 23:08:07', '2025-01-05 23:08:07');
INSERT INTO `users` VALUES ('33', 'Lê Robot Expert', NULL, NULL, NULL, 'robot_expert', 'robot.expert@mechamap.com', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatar-3.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-04-18 19:34:40', NULL, '0', '2025-06-25 17:57:23', '1', NULL, '$2y$12$3KxNuK.7ozkTWNoqql5q5esFieKtyuexvPiZ7RoQlmiTp6DBlvbHa', NULL, NULL, NULL, '2025-02-15 02:45:56', '2025-02-15 02:45:56');
INSERT INTO `users` VALUES ('34', 'Phạm Materials Pro', NULL, NULL, NULL, 'materials_pro', 'materials.pro@mechamap.com', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatar-4.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-01-11 14:24:38', NULL, '0', '2025-06-25 17:57:23', '1', NULL, '$2y$12$REXM.a1PvN91KkZ61yiZ7OM5cxyLOwHdUeZxbuNCzwBEIEiWZkEwq', NULL, NULL, NULL, '2025-01-02 12:17:51', '2025-01-02 12:17:51');
INSERT INTO `users` VALUES ('35', 'Võ Design Guru', NULL, NULL, NULL, 'design_guru', 'design.guru@mechamap.com', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatar-5.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-05-08 08:30:55', NULL, '0', '2025-06-25 17:57:23', '1', NULL, '$2y$12$RqTIlXFgXVwlXUq8hyo5lOjdpUNaJ3muUA2GDpns7l6DATwoLoxgK', NULL, NULL, NULL, '2025-04-19 20:30:12', '2025-04-19 20:30:12');
INSERT INTO `users` VALUES ('36', 'Công ty TNHH Thép Việt Nam', 'Công ty TNHH Thép Việt Nam', '0123456789', '0123456789-001', 'thep_vietnam', 'contact@thepvietnam.com', 'supplier', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'active', '/images/avatars/supplier1.jpg', NULL, 'Chuyên cung cấp thép xây dựng, thép công nghiệp, vật liệu kim loại chất lượng cao cho các dự án cơ khí.', '[\"steel\",\"metal\",\"construction_materials\"]', '+84-28-1234-5678', 'sales@thepvietnam.com', '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM', '1', '2025-06-25 18:05:36', NULL, 'premium', '4.50', '127', NULL, 'TP. Hồ Chí Minh, Việt Nam', NULL, NULL, NULL, NULL, NULL, '2500', '0', '2025-05-17 06:00:20', NULL, '0', '2025-06-25 18:05:36', '1', NULL, '$2y$12$11kKiMiGNQIF4WQprVRym.9jEje5JnXat1nv1K0uy60WfNpXQKASC', NULL, NULL, NULL, '2025-02-27 10:36:14', '2025-02-27 10:36:14');
INSERT INTO `users` VALUES ('37', 'Vật Liệu Cơ Khí Hà Nội', 'Công ty Cổ phần Vật Liệu Cơ Khí Hà Nội', '0987654321', '0987654321-002', 'vlck_hanoi', 'info@vlckhanoi.vn', 'supplier', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'active', '/images/avatars/supplier2.jpg', NULL, 'Nhà phân phối ủy quyền các thiết bị cơ khí, dụng cụ công nghiệp, phụ tùng máy móc.', '[\"tools\",\"equipment\",\"spare_parts\"]', '+84-24-9876-5432', 'sales@vlckhanoi.vn', '456 Phố Huế, Hai Bà Trưng, Hà Nội', '1', '2025-06-25 18:05:36', NULL, 'basic', '4.20', '89', NULL, 'Hà Nội, Việt Nam', NULL, NULL, NULL, NULL, NULL, '1800', '0', '2025-03-29 09:15:34', NULL, '0', '2025-06-25 18:05:36', '1', NULL, '$2y$12$ozPX2ORT0LVnhWXlc6wTCO5DijV4QxgiBOVAWHetD5OdK36vaYUky', NULL, NULL, NULL, '2025-03-28 20:16:49', '2025-03-28 20:16:49');
INSERT INTO `users` VALUES ('38', 'Industrial Tools Vietnam', 'Công ty TNHH Industrial Tools Vietnam', '1234567890', '1234567890-003', 'industrial_tools_vn', 'contact@industrialtools.vn', 'supplier', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'active', '/images/avatars/supplier3.jpg', NULL, 'Nhà cung cấp dụng cụ công nghiệp chuyên nghiệp, thiết bị đo lường, máy móc chính xác từ các thương hiệu hàng đầu thế giới.', '[\"precision_tools\",\"measuring_equipment\",\"industrial_machinery\"]', '+84-28-2345-6789', 'sales@industrialtools.vn', '789 Đường Lê Văn Việt, Quận 9, TP.HCM', '1', '2025-06-25 18:05:36', NULL, 'enterprise', '4.70', '156', NULL, 'TP. Hồ Chí Minh, Việt Nam', NULL, NULL, NULL, NULL, NULL, '3200', '0', '2025-06-11 01:55:05', NULL, '0', '2025-06-25 18:05:36', '1', NULL, '$2y$12$N4M9a7qNRfeZ/3Pl7CThLun5i8uH3QsCSXlA8yY5gzzGwLy/yoUrm', NULL, NULL, NULL, '2025-06-08 13:02:44', '2025-06-08 13:02:44');
INSERT INTO `users` VALUES ('39', 'Bearing & Fastener Supply Co.', 'Công ty Cổ phần Bearing & Fastener Supply', '2345678901', '2345678901-004', 'bearing_fastener', 'info@bearingfastener.com.vn', 'supplier', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'active', '/images/avatars/supplier4.jpg', NULL, 'Chuyên cung cấp ổ bi, bu lông, ốc vít, và các loại fastener chất lượng cao cho ngành cơ khí, ô tô, và hàng không.', '[\"bearings\",\"fasteners\",\"automotive_parts\"]', '+84-24-3456-7890', 'sales@bearingfastener.com.vn', '321 Phố Minh Khai, Hai Bà Trưng, Hà Nội', '1', '2025-06-25 18:05:36', NULL, 'premium', '4.30', '98', NULL, 'Hà Nội, Việt Nam', NULL, NULL, NULL, NULL, NULL, '2100', '0', '2025-05-14 08:56:31', NULL, '0', '2025-06-25 18:05:36', '1', NULL, '$2y$12$3vrOQoAq5XwPWbSHMzClw.7YvFzWqsNhkLfdgTZDm/V.KjbvQpzpK', NULL, NULL, NULL, '2025-04-23 11:22:30', '2025-04-23 11:22:30');
INSERT INTO `users` VALUES ('40', 'Hydraulic Systems Vietnam', 'Công ty TNHH Hydraulic Systems Vietnam', '3456789012', '3456789012-005', 'hydraulic_vn', 'contact@hydraulicvn.com', 'supplier', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'active', '/images/avatars/supplier5.jpg', NULL, 'Nhà cung cấp hệ thống thủy lực, khí nén, và các thiết bị tự động hóa cho ngành công nghiệp nặng.', '[\"hydraulic_systems\",\"pneumatic_systems\",\"automation\"]', '+84-251-456-7890', 'sales@hydraulicvn.com', 'KCN Biên Hòa 1, Đồng Nai', '1', '2025-06-25 18:05:36', NULL, 'basic', '4.10', '73', NULL, 'Đồng Nai, Việt Nam', NULL, NULL, NULL, NULL, NULL, '1900', '0', '2025-06-29 03:21:13', NULL, '0', '2025-06-25 18:05:36', '1', NULL, '$2y$12$5Szpz.xirusPJG8x5UMH6emv.hZbFFL8BPUwYOjp1Sf9wDLqRRn9C', NULL, NULL, NULL, '2025-05-23 02:15:22', '2025-05-23 02:15:22');
INSERT INTO `users` VALUES ('41', 'Nhà máy Cơ khí Đông Á', 'Công ty Cổ phần Cơ khí Đông Á', '1122334455', '1122334455-003', 'dongamech', 'contact@dongamech.com', 'manufacturer', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'active', '/images/avatars/manufacturer1.jpg', NULL, 'Chuyên sản xuất linh kiện cơ khí chính xác, gia công CNC, đúc kim loại cho ngành ô tô và điện tử.', '[\"cnc_machining\",\"precision_parts\",\"automotive\"]', '+84-251-234-5678', 'production@dongamech.com', 'KCN Đồng Nai, Biên Hòa, Đồng Nai', '1', '2025-06-25 18:05:37', NULL, 'enterprise', '4.80', '203', NULL, 'Đồng Nai, Việt Nam', NULL, NULL, NULL, NULL, NULL, '4200', '0', '2025-05-25 02:50:44', NULL, '0', '2025-06-25 18:05:37', '1', NULL, '$2y$12$.0fgI62nUZM3A9ni/L0GIOFvHqxaJcppV4X9y3JLxlXxWYyjat5Oq', NULL, NULL, NULL, '2025-03-02 10:17:02', '2025-07-04 01:24:56');
INSERT INTO `users` VALUES ('42', 'Vietnam Precision Manufacturing', 'Công ty TNHH Vietnam Precision Manufacturing', '4455667788', '4455667788-006', 'vn_precision', 'info@vnprecision.com', 'manufacturer', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'active', '/images/avatars/manufacturer2.jpg', NULL, 'Nhà sản xuất linh kiện chính xác cho ngành hàng không, y tế và năng lượng. Chứng nhận ISO 9001:2015 và AS9100.', '[\"aerospace_parts\",\"medical_devices\",\"precision_manufacturing\"]', '+84-28-5678-9012', 'manufacturing@vnprecision.com', 'KCN Tân Thuận, Quận 7, TP.HCM', '1', '2025-06-25 18:05:37', NULL, 'enterprise', '4.90', '167', NULL, 'TP. Hồ Chí Minh, Việt Nam', NULL, NULL, NULL, NULL, NULL, '4800', '0', '2025-05-21 03:21:18', NULL, '0', '2025-06-25 18:05:37', '1', NULL, '$2y$12$cAb4NlIWMMnKIUrBdk1ZYeFqeypGkxyL3j8UmS.aVIETtsoZQCpj6', NULL, NULL, NULL, '2025-05-03 21:19:49', '2025-05-03 21:19:49');
INSERT INTO `users` VALUES ('43', 'Hanoi Industrial Manufacturing', 'Công ty Cổ phần Hanoi Industrial Manufacturing', '5566778899', '5566778899-007', 'hanoi_industrial', 'contact@hanoiindustrial.vn', 'manufacturer', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'active', '/images/avatars/manufacturer3.jpg', NULL, 'Sản xuất máy móc công nghiệp, thiết bị tự động hóa, và hệ thống băng tải cho các nhà máy sản xuất.', '[\"industrial_machinery\",\"automation_equipment\",\"conveyor_systems\"]', '+84-24-6789-0123', 'production@hanoiindustrial.vn', 'KCN Thăng Long, Hà Nội', '1', '2025-06-25 18:05:37', NULL, 'premium', '4.60', '134', NULL, 'Hà Nội, Việt Nam', NULL, NULL, NULL, NULL, NULL, '3900', '0', '2025-05-13 13:35:23', NULL, '0', '2025-06-25 18:05:37', '1', NULL, '$2y$12$otRdKXId/5zuNiQvkZ4.qO.cobFBglt8tWR455t4AQ8Qr7SFJKVyq', NULL, NULL, NULL, '2025-04-05 22:24:32', '2025-04-05 22:24:32');
INSERT INTO `users` VALUES ('44', 'Mekong Delta Engineering Works', 'Công ty TNHH Mekong Delta Engineering Works', '6677889900', '6677889900-008', 'mekong_engineering', 'info@mekongworks.vn', 'manufacturer', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'active', '/images/avatars/manufacturer4.jpg', NULL, 'Chuyên sản xuất thiết bị nông nghiệp, máy móc chế biến thực phẩm và hệ thống tưới tiêu tự động.', '[\"agricultural_machinery\",\"food_processing\",\"irrigation_systems\"]', '+84-292-789-0123', 'manufacturing@mekongworks.vn', 'KCN Trà Nóc, Cần Thơ', '1', '2025-06-25 18:05:37', NULL, 'basic', '4.40', '89', NULL, 'Cần Thơ, Việt Nam', NULL, NULL, NULL, NULL, NULL, '3100', '0', '2025-06-28 12:10:50', NULL, '0', '2025-06-25 18:05:37', '1', NULL, '$2y$12$T9xwezpuswlmNHUAKqNWEO2Sa5W36aDAFf34OsISy23UyTb4BFlki', NULL, NULL, NULL, '2025-06-23 00:41:36', '2025-06-23 00:41:36');
INSERT INTO `users` VALUES ('45', 'MechaTech Solutions Vietnam', 'MechaTech Solutions Vietnam Co., Ltd', '9988776655', '9988776655-005', 'mechatech_vn', 'vietnam@mechatech.global', 'brand', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'active', '/images/avatars/brand1.jpg', NULL, 'Đại diện chính thức thương hiệu MechaTech tại Việt Nam. Chuyên phân phối thiết bị đo lường, kiểm tra chất lượng.', '[\"measurement_tools\",\"quality_control\",\"testing_equipment\"]', '+84-28-3456-7890', 'sales@mechatech.vn', 'Tầng 15, Tòa nhà Bitexco, Quận 1, TP.HCM', '1', '2025-06-25 18:05:37', NULL, 'enterprise', '4.90', '312', NULL, 'TP. Hồ Chí Minh, Việt Nam', NULL, NULL, NULL, NULL, NULL, '5500', '0', '2025-06-26 12:12:30', NULL, '0', '2025-06-25 18:05:37', '1', NULL, '$2y$12$1yfSzd0Eoa2bgo0A/ypv2Ozc9YHZ6aIjpzjq9lHkr6Yl19qBoax5q', NULL, NULL, NULL, '2025-04-30 03:29:32', '2025-04-30 03:29:32');
INSERT INTO `users` VALUES ('46', 'Siemens Vietnam Representative', 'Siemens Vietnam Co., Ltd', '1357924680', '1357924680-009', 'siemens_vietnam', 'vietnam@siemens.com', 'brand', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'active', '/images/avatars/brand2.jpg', NULL, 'Đại diện chính thức Siemens tại Việt Nam. Cung cấp giải pháp tự động hóa công nghiệp, điều khiển và năng lượng.', '[\"automation\",\"industrial_control\",\"energy_solutions\"]', '+84-24-1357-9246', 'contact@siemens.vn', 'Tầng 12, Lotte Center, Cầu Giấy, Hà Nội', '1', '2025-06-25 18:05:37', NULL, 'enterprise', '4.95', '487', NULL, 'Hà Nội, Việt Nam', NULL, NULL, NULL, NULL, NULL, '7200', '0', '2025-06-30 11:15:34', NULL, '0', '2025-06-25 18:05:37', '1', NULL, '$2y$12$I5WlsMq4hZ0coMPgnQsoJ.6yFypo240wp/HArluKKfoPliyKOcWOm', NULL, NULL, NULL, '2025-06-21 23:02:00', '2025-06-21 23:02:00');
INSERT INTO `users` VALUES ('47', 'Mitsubishi Electric Vietnam', 'Mitsubishi Electric Vietnam Co., Ltd', '2468135790', '2468135790-010', 'mitsubishi_vn', 'vietnam@mitsubishielectric.com', 'brand', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'active', '/images/avatars/brand3.jpg', NULL, 'Thương hiệu hàng đầu về thiết bị điện công nghiệp, hệ thống tự động hóa và giải pháp Factory Automation.', '[\"factory_automation\",\"electrical_equipment\",\"servo_systems\"]', '+84-28-2468-1357', 'sales@mitsubishielectric.vn', 'Tầng 8, Saigon Trade Center, Quận 1, TP.HCM', '1', '2025-06-25 18:05:38', NULL, 'enterprise', '4.80', '356', NULL, 'TP. Hồ Chí Minh, Việt Nam', NULL, NULL, NULL, NULL, NULL, '6800', '0', '2025-05-07 05:20:18', NULL, '0', '2025-06-25 18:05:38', '1', NULL, '$2y$12$7zTZmNj0Jsu11Zf58hzFfeta1UiZLP4dJ3L0gjIfoH93qRRKcdhj.', NULL, NULL, NULL, '2025-03-17 15:59:53', '2025-03-17 15:59:53');
INSERT INTO `users` VALUES ('48', 'Schneider Electric Vietnam', 'Schneider Electric Vietnam Co., Ltd', '3691470258', '3691470258-011', 'schneider_vn', 'vietnam@schneider-electric.com', 'brand', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'active', '/images/avatars/brand4.jpg', NULL, 'Chuyên gia về quản lý năng lượng và tự động hóa. Cung cấp giải pháp toàn diện cho công nghiệp và tòa nhà thông minh.', '[\"energy_management\",\"building_automation\",\"power_distribution\"]', '+84-24-3691-4702', 'contact@schneider-electric.vn', 'Tầng 10, Vincom Center, Ba Đình, Hà Nội', '1', '2025-06-25 18:05:38', NULL, 'enterprise', '4.70', '289', NULL, 'Hà Nội, Việt Nam', NULL, NULL, NULL, NULL, NULL, '6200', '0', '2025-03-12 18:17:50', NULL, '0', '2025-06-25 18:05:38', '1', NULL, '$2y$12$GUQvAvGmHYdYlFHKPNsf2eAh/MaZ0hxbeuSRsEd5TyJ.YU0edxWN6', NULL, NULL, NULL, '2025-02-21 14:45:37', '2025-02-21 14:45:37');
INSERT INTO `users` VALUES ('49', 'Ms. Aurore Bayer', NULL, NULL, NULL, 'hal.kerluke', 'dlubowitz@example.org', 'supplier', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatars/admin.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-06-12 17:51:37', NULL, '0', '2025-06-25 18:06:06', '1', NULL, '$2y$12$YkZn9/4Ve4juadfyTosGCui4XoGw2WyMmj6s8eenkIoajocoqf7FS', 'xEHg5mfC3B', NULL, NULL, '2025-06-06 00:48:33', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('50', 'Florine Von', NULL, NULL, NULL, 'bahringer.eliseo', 'tromp.kamille@example.com', 'supplier', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatars/avatar-1.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-06-18 05:44:03', NULL, '0', '2025-06-25 18:06:06', '1', NULL, '$2y$12$YkZn9/4Ve4juadfyTosGCui4XoGw2WyMmj6s8eenkIoajocoqf7FS', 'u30emjSgtc', NULL, NULL, '2025-06-14 15:57:21', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('51', 'Kaylie Morissette', NULL, NULL, NULL, 'lveum', 'batz.rogelio@example.com', 'supplier', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatars/avatar-10.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-06-23 13:40:48', NULL, '0', '2025-06-25 18:06:06', '1', NULL, '$2y$12$YkZn9/4Ve4juadfyTosGCui4XoGw2WyMmj6s8eenkIoajocoqf7FS', 'ATmVNpd3A4', NULL, NULL, '2025-02-03 20:14:18', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('52', 'Joannie Maggio', NULL, NULL, NULL, 'spencer.harmony', 'xanderson@example.org', 'supplier', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatars/avatar-2.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-05-30 19:15:38', NULL, '0', '2025-06-25 18:06:06', '1', NULL, '$2y$12$YkZn9/4Ve4juadfyTosGCui4XoGw2WyMmj6s8eenkIoajocoqf7FS', 't1Ccroty8s', NULL, NULL, '2025-01-11 05:39:53', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('53', 'Noel Steuber', NULL, NULL, NULL, 'wkuphal', 'gorczany.gayle@example.net', 'supplier', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatars/avatar-3.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-01-28 09:59:19', NULL, '0', '2025-06-25 18:06:06', '1', NULL, '$2y$12$YkZn9/4Ve4juadfyTosGCui4XoGw2WyMmj6s8eenkIoajocoqf7FS', 'VYC7kBccWJ', NULL, NULL, '2025-01-05 06:17:49', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('54', 'Admin User', NULL, NULL, NULL, 'admin_test', 'admin@mechamap.test', 'admin', 'system_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"access-admin-panel\",\"access-system-admin\",\"access-content-admin\",\"access-marketplace-admin\",\"access-community-admin\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatars/avatar-4.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-06-25 10:40:46', 'admin.documentation.analytics', '0', '2025-06-25 18:19:07', '1', NULL, '$2y$12$II7XcsW1y7eIJBEWMHqkNOA25j8e3/ufJyP7OKqmDLdhZYMAESqGG', NULL, NULL, NULL, '2025-06-01 08:04:49', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('55', 'Moderator Test User', NULL, NULL, NULL, 'moderator_test', 'moderator@mechamap.test', 'content_moderator', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatars/avatar-5.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-03-16 04:26:09', 'admin.logout', '0', '2025-06-25 18:19:07', '1', NULL, '$2y$12$26rwSNJjvCu/MiTCKQP7bO6qroGdzlDPUj43xvV.LA7hZyXrzcQJ.', NULL, NULL, NULL, '2025-03-13 03:11:36', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('56', 'Supplier Test User', NULL, NULL, NULL, 'supplier_test', 'supplier@mechamap.test', 'supplier', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatars/avatar-6.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-04-14 07:34:45', NULL, '0', '2025-06-25 18:19:07', '1', NULL, '$2y$12$uiWX7wSxHI6vHJgRurfVcOx1c1bOeU2BFzn3PxvcaiLRfKxgD/EVy', NULL, NULL, NULL, '2025-03-10 17:12:19', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('57', 'Manufacturer Test User', NULL, NULL, NULL, 'manufacturer_test', 'manufacturer@mechamap.test', 'manufacturer', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatars/avatar-7.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-02-27 05:18:09', NULL, '0', '2025-06-25 18:19:08', '1', NULL, '$2y$12$6DkhcxcMJ3rPHpBHXJ.oIO29UQYWdcod/7YXrxGmAFf8zlBRXFsAC', NULL, NULL, NULL, '2025-02-03 22:24:44', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('58', 'Brand Test User', NULL, NULL, NULL, 'brand_test', 'brand@mechamap.test', 'brand', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatars/avatar-8.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-06-20 19:44:10', NULL, '0', '2025-06-25 18:19:08', '1', NULL, '$2y$12$dKe7aS1JULSL/NycRaBuzeyKdgaD.dAPN0oTDPhnfP4AdBSHfLfvK', NULL, NULL, NULL, '2025-04-29 08:08:52', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('59', 'Member Test User', NULL, NULL, NULL, 'member_test', 'member@mechamap.test', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatars/avatar-9.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-06-08 05:17:36', NULL, '0', '2025-06-25 18:19:08', '1', NULL, '$2y$12$YKWA6azwqnfWqbl0HYdFWe2M62DnvZAr1hf6w3JiS9GUe7Y04jpZm', NULL, NULL, NULL, '2025-05-05 11:56:29', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('60', 'Guest Test User', NULL, NULL, NULL, 'guest_test', 'guest@mechamap.test', 'guest', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatars/default.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-05-12 02:52:05', NULL, '0', '2025-06-25 18:19:08', '1', NULL, '$2y$12$kYmPHYQY72mBYwOhqlUAm.n4IgVB1s9oAG8pzIqBdZd/OlvV8DMlG', NULL, NULL, NULL, '2025-04-13 09:57:53', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('61', 'Test User', NULL, NULL, NULL, 'testuser', 'test@test.com', 'member', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'Registered', '/images/users/avatars/moderator.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-06-22 13:55:33', 'marketplace.index', '0', '2025-06-26 05:28:02', '1', NULL, '$2y$12$CdfMMZMamE8ML/Ap0j8P1Owfc6Pcew0DCcw9fBtviqSN9o3orYu/W', 'wOdvuIb8BYwkTYlnsJmilCI5N7Jzat70u3vXFEXsUCCpleEabMWjzFITO5Gy', NULL, NULL, '2025-05-16 12:51:04', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('62', 'Nguyễn Quản Trị', NULL, NULL, NULL, 'superadmin', 'superadmin@mechamap.vn', 'super_admin', 'system_management', '[\"manage-system\",\"manage-infrastructure\",\"manage-database\",\"manage-security\",\"access-super-admin\",\"view-system-logs\",\"manage-backups\",\"view-users\",\"create-users\",\"update-users\",\"delete-users\",\"ban-users\",\"manage-user-roles\",\"verify-business-accounts\",\"manage-subscriptions\",\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-marketplace\",\"approve-products\",\"manage-orders\",\"manage-payments\",\"view-marketplace-analytics\",\"manage-seller-accounts\",\"handle-disputes\",\"manage-commissions\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\",\"access-admin-panel\",\"access-system-admin\",\"access-content-admin\",\"access-marketplace-admin\",\"access-community-admin\",\"view-content\",\"create-threads\",\"create-comments\",\"upload-files\",\"send-messages\",\"create-polls\",\"rate-products\",\"write-reviews\",\"sell-products\",\"manage-own-products\",\"view-sales-analytics\",\"manage-business-profile\",\"access-seller-dashboard\",\"upload-technical-files\",\"manage-cad-files\",\"access-b2b-features\",\"manage-faqs\",\"view-faqs\",\"manage-knowledge-base\",\"manage-cad-library\",\"manage-showcases\",\"manage-roles-permissions\"]', '2025-07-02 09:31:08', NULL, 'active', '/images/avatars/super_admin.jpg', 'Super Administrator MechaMap - Founder & CEO với 20+ năm kinh nghiệm trong ngành cơ khí và công nghệ. Chịu trách nhiệm chiến lược phát triển toàn diện.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', 'https://mechamap.vn', 'TP. Hồ Chí Minh, Việt Nam', NULL, NULL, NULL, NULL, 'Building the future of Vietnamese mechanical engineering community �', '15000', '800', '2025-07-04 01:35:52', 'marketplace.index', '100', '2025-06-26 12:28:32', '1', NULL, '$2y$12$SSUorwICOK.F3CjU.Jpi9.ZiHP9QnDMcX4Ee3CKNon41T.yhU4YsS', NULL, NULL, NULL, '2025-01-08 11:41:50', '2025-07-04 01:35:52');
INSERT INTO `users` VALUES ('63', 'Lê Database Master', NULL, NULL, NULL, 'dbadmin', 'database@mechamap.vn', 'system_admin', 'system_management', '[\"manage-system\",\"manage-infrastructure\",\"manage-database\",\"manage-security\",\"access-super-admin\",\"view-system-logs\",\"manage-backups\",\"view-users\",\"create-users\",\"update-users\",\"delete-users\",\"ban-users\",\"manage-user-roles\",\"verify-business-accounts\",\"manage-subscriptions\",\"access-admin-panel\",\"access-system-admin\",\"access-content-admin\",\"access-marketplace-admin\",\"access-community-admin\"]', '2025-07-02 09:31:08', NULL, 'active', '/images/avatars/system_admin2.jpg', 'Database Administrator & Data Architect - Chuyên gia về database optimization, backup/recovery và data security. 10+ năm kinh nghiệm với enterprise systems.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Đà Nẵng, Việt Nam', NULL, NULL, NULL, NULL, 'Data integrity & system performance guardian 🛡️', '10500', '520', '2025-06-10 15:40:51', 'admin.dashboard', '100', '2025-06-26 12:28:33', '1', NULL, '$2y$12$Ym8daWISfTrmpuA42l0.peWVUg9Y1RsOjSeS.rcbDf5.RbDqnqHne', NULL, NULL, NULL, '2025-03-14 02:38:59', '2025-07-04 01:24:55');
INSERT INTO `users` VALUES ('64', 'Võ Forum Expert', NULL, NULL, NULL, 'forumadmin', 'forum@mechamap.vn', 'content_admin', 'community_management', '[\"manage-content\",\"moderate-content\",\"approve-content\",\"delete-content\",\"manage-categories\",\"manage-forums\",\"pin-threads\",\"lock-threads\",\"feature-content\",\"manage-community\",\"moderate-discussions\",\"manage-events\",\"send-announcements\",\"manage-user-groups\",\"view-analytics\",\"view-reports\",\"export-data\",\"manage-reports\"]', '2025-07-02 09:31:08', NULL, 'active', '/images/avatars/content_admin2.jpg', 'Forum Administrator - Chuyên gia quản lý diễn đàn kỹ thuật và community engagement. Background về Mechanical Engineering và Community Management.', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Hà Nội, Việt Nam', NULL, NULL, NULL, NULL, 'Fostering technical discussions & knowledge sharing 💬', '8800', '420', '2025-05-09 03:41:16', NULL, '100', '2025-06-26 12:28:33', '1', NULL, '$2y$12$FyfJ.GKlXRhNfzJYS31oNuDiH1hKK2jvr/PBCqtOJNjngOFBPm3i6', NULL, NULL, NULL, '2025-03-14 18:06:53', '2025-07-04 01:24:55');
INSERT INTO `users` VALUES ('65', 'Admin Documentation', NULL, NULL, NULL, 'cruickshank.pauline', 'admin.docs@mechamap.test', 'admin', NULL, NULL, NULL, NULL, 'Registered', '/images/users/avatars/super_admin.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-07-02 01:20:35', NULL, '0', '2025-07-01 08:35:44', '1', NULL, '$2y$12$mzRYHEkamp5MXrs9fuIpF.TCgeghsyHeWxx3NH34pEKknNV1ewxbi', 'RQQVs7aEhx', NULL, NULL, '2025-05-22 17:16:41', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('66', 'Test Multiple Roles User', NULL, NULL, NULL, 'test_multi_roles', 'test.multi.roles@mechamap.test', 'content_moderator', 'community_management', NULL, NULL, NULL, 'active', '/images/users/avatars/admin.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-04-04 15:45:07', NULL, '0', '2025-07-01 21:55:50', '1', NULL, '$2y$12$29bPoXjTCvoUc31hSny4rOcSoiW9FbImPP9utAyc8dZSARyq5RaHC', NULL, NULL, NULL, '2025-01-30 00:14:56', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('67', 'Business Multi-Role User', 'Test Manufacturing Co.', NULL, NULL, 'business_multi', 'business.multi@mechamap.test', 'manufacturer', 'business_partners', NULL, NULL, NULL, 'active', '/images/users/avatars/avatar-1.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-04-10 23:00:47', NULL, '0', '2025-07-01 21:55:51', '1', NULL, '$2y$12$C72qBXqVtRbD8e7NvFnVBu6sc.3dwBRcwzJJGvNkBh40s8NHkLZhi', NULL, NULL, NULL, '2025-01-04 14:56:14', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('68', 'Content Admin Test', NULL, NULL, NULL, 'content.admin.test', 'content.admin@mechamap.test', 'content_admin', 'system_management', NULL, NULL, NULL, 'Registered', '/images/users/avatars/avatar-10.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-01-27 16:59:58', NULL, '0', '2025-07-02 09:52:29', '1', NULL, '$2y$12$DsBSAxdarxiy/XTNbzMWJedEQBB1dXRIQzybEQhsb.K.qEtKw64AK', NULL, NULL, NULL, '2025-01-05 21:00:10', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('69', 'Test Moderator', NULL, NULL, NULL, 'test.moderator', 'test.moderator.unique@mechamap.test', 'content_moderator', 'community_management', NULL, NULL, NULL, 'active', '/images/users/avatars/avatar-2.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-07-02 15:45:30', 'admin.dashboard', '0', '2025-07-02 10:26:27', '1', NULL, '$2y$12$NlrLGoCN.FPvI51RJeSt1.e3CMHSmgTANnI6elKMtyERsZ8fcf./i', NULL, NULL, NULL, '2025-04-09 15:41:08', '2025-07-03 14:17:32');
INSERT INTO `users` VALUES ('70', 'Nguyễn Marketplace Mod', NULL, NULL, NULL, 'marketplace_mod_test', 'marketplace.mod@mechamap.test', 'marketplace_moderator', NULL, NULL, NULL, NULL, 'active', '/images/avatars/moderator1.jpg', 'Marketplace Moderator - Test user for marketplace moderation features', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'TP. Hồ Chí Minh, Việt Nam', NULL, NULL, NULL, NULL, 'Testing marketplace moderation 🛒', '500', '50', '2025-07-04 01:27:09', NULL, '100', '2025-07-04 01:27:09', '1', NULL, '$2y$12$wwBwplZYmC7kOdfGAVYxUupd8809XjVTYtuVJnT0hMcekAh2jj9TW', NULL, NULL, NULL, '2025-07-04 01:27:09', '2025-07-04 01:27:09');
INSERT INTO `users` VALUES ('71', 'Trần Community Mod', NULL, NULL, NULL, 'community_mod_test', 'community.mod@mechamap.test', 'community_moderator', NULL, NULL, NULL, NULL, 'active', '/images/avatars/moderator2.jpg', 'Community Moderator - Test user for community moderation features', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, 'Hà Nội, Việt Nam', NULL, NULL, NULL, NULL, 'Testing community moderation 👥', '600', '60', '2025-07-04 01:27:10', NULL, '100', '2025-07-04 01:27:10', '1', NULL, '$2y$12$U0Ej9NJl3kMA86GINfrBsO8Ra9KYpkxSjy6P8em6lAVz9hQr8x9fq', NULL, NULL, NULL, '2025-07-04 01:27:10', '2025-07-04 01:27:10');
INSERT INTO `users` VALUES ('72', 'Lê Verified Partner', NULL, NULL, NULL, 'verified_partner_test', 'verified.partner@mechamap.test', 'verified_partner', NULL, NULL, NULL, NULL, 'active', '/images/avatars/partner1.jpg', 'Verified Partner - Test user for verified business partner features', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', 'https://verifiedpartner.test', 'Đà Nẵng, Việt Nam', NULL, NULL, NULL, NULL, 'Testing verified partner features 🤝', '1000', '100', '2025-07-04 05:06:06', 'community.index', '100', '2025-07-04 01:27:10', '1', NULL, '$2y$12$rZ4Lv6EYYdY0hAuGRQo9M.hqQekNTAcds41V9Rw7YeIiXTcydODtO', NULL, NULL, NULL, '2025-07-04 01:27:10', '2025-07-04 05:06:06');
INSERT INTO `users` VALUES ('73', 'Test Member', NULL, NULL, NULL, 'test_member', 'member@test.com', 'member', NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 'free', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '2025-07-04 12:55:24', 'marketplace.download.file', '0', '2025-07-04 10:11:49', '1', NULL, '$2y$12$vcAHkddb8e180T9/UO3qjuEvi8WhPRkXUMqpIWUf2f21VPRbcbbV.', NULL, NULL, NULL, '2025-07-04 10:11:49', '2025-07-04 12:55:24');

SET FOREIGN_KEY_CHECKS=1;

-- Backup completed at 2025-07-10 10:13:48