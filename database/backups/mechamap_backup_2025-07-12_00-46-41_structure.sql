-- MechaMap Database Structure Backup
-- Generated: 2025-07-12_00-46-41
-- Database: mechamap_backend

SET FOREIGN_KEY_CHECKS = 0;

-- Table: achievements
DROP TABLE IF EXISTS `achievements`;
CREATE TABLE `achievements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(191) NOT NULL,
  `type` enum('milestone','badge','streak','special') NOT NULL,
  `criteria` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`criteria`)),
  `icon` varchar(191) DEFAULT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#3B82F6',
  `points` int(11) NOT NULL DEFAULT 0,
  `rarity` enum('common','uncommon','rare','epic','legendary') NOT NULL DEFAULT 'common',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `achievements_key_unique` (`key`),
  KEY `achievements_category_is_active_index` (`category`,`is_active`),
  KEY `achievements_type_rarity_index` (`type`,`rarity`),
  KEY `achievements_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: alerts
DROP TABLE IF EXISTS `alerts`;
CREATE TABLE `alerts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(191) NOT NULL,
  `content` text NOT NULL,
  `type` varchar(191) NOT NULL DEFAULT 'info',
  `read_at` timestamp NULL DEFAULT NULL,
  `alertable_type` varchar(191) DEFAULT NULL,
  `alertable_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alerts_user_id_read_at_index` (`user_id`,`read_at`),
  KEY `alerts_alertable_type_alertable_id_index` (`alertable_type`,`alertable_id`),
  KEY `alerts_type_created_at_index` (`type`,`created_at`),
  CONSTRAINT `alerts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: analytics_reports
DROP TABLE IF EXISTS `analytics_reports`;
CREATE TABLE `analytics_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: audit_logs
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_type` varchar(191) NOT NULL DEFAULT 'user',
  `action` varchar(191) NOT NULL,
  `resource` varchar(191) NOT NULL,
  `resource_id` bigint(20) unsigned DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `risk_level` enum('low','medium','high','critical') NOT NULL DEFAULT 'low',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `method` varchar(10) DEFAULT NULL,
  `session_id` varchar(191) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `audit_logs_action_created_at_index` (`action`,`created_at`),
  KEY `audit_logs_resource_resource_id_index` (`resource`,`resource_id`),
  KEY `audit_logs_risk_level_created_at_index` (`risk_level`,`created_at`),
  KEY `audit_logs_ip_address_created_at_index` (`ip_address`,`created_at`),
  KEY `audit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: b2b_quotes
DROP TABLE IF EXISTS `b2b_quotes`;
CREATE TABLE `b2b_quotes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `buyer_id` bigint(20) unsigned NOT NULL,
  `seller_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `quantity` int(11) NOT NULL,
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifications`)),
  `delivery_requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`delivery_requirements`)),
  `budget_range` varchar(191) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `status` enum('pending','quoted','accepted','rejected','completed') NOT NULL DEFAULT 'pending',
  `priority` enum('low','normal','high') NOT NULL DEFAULT 'normal',
  `quoted_amount` decimal(15,2) DEFAULT NULL,
  `final_amount` decimal(15,2) DEFAULT NULL,
  `seller_notes` text DEFAULT NULL,
  `quoted_at` timestamp NULL DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `b2b_quotes_product_id_foreign` (`product_id`),
  KEY `b2b_quotes_buyer_id_status_index` (`buyer_id`,`status`),
  KEY `b2b_quotes_seller_id_status_index` (`seller_id`,`status`),
  KEY `b2b_quotes_deadline_index` (`deadline`),
  CONSTRAINT `b2b_quotes_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `b2b_quotes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `b2b_quotes_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: bookmarks
DROP TABLE IF EXISTS `bookmarks`;
CREATE TABLE `bookmarks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `bookmarkable_id` bigint(20) unsigned NOT NULL,
  `bookmarkable_type` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bookmarks_user_id_bookmarkable_id_bookmarkable_type_unique` (`user_id`,`bookmarkable_id`,`bookmarkable_type`),
  KEY `bookmarks_bookmarkable_type_bookmarkable_id_index` (`bookmarkable_type`,`bookmarkable_id`),
  KEY `bookmarks_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `bookmarks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: cache
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: cache_locks
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: cad_files
DROP TABLE IF EXISTS `cad_files`;
CREATE TABLE `cad_files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` uuid NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `file_number` varchar(191) NOT NULL,
  `version` varchar(10) NOT NULL DEFAULT '1.0',
  `created_by` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `file_path` varchar(191) NOT NULL,
  `original_filename` varchar(191) NOT NULL,
  `file_extension` varchar(191) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `mime_type` varchar(191) NOT NULL,
  `checksum` varchar(191) DEFAULT NULL,
  `cad_software` varchar(191) DEFAULT NULL,
  `software_version` varchar(191) DEFAULT NULL,
  `compatible_software` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`compatible_software`)),
  `model_type` varchar(191) NOT NULL,
  `geometry_type` varchar(191) DEFAULT NULL,
  `units` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`units`)),
  `bounding_box` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`bounding_box`)),
  `volume` decimal(15,6) DEFAULT NULL,
  `surface_area` decimal(15,6) DEFAULT NULL,
  `mass` decimal(15,6) DEFAULT NULL,
  `material_type` varchar(191) DEFAULT NULL,
  `material_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`material_properties`)),
  `manufacturing_methods` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`manufacturing_methods`)),
  `manufacturing_constraints` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`manufacturing_constraints`)),
  `design_intent` varchar(191) DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`parameters`)),
  `configurations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configurations`)),
  `technical_drawing_id` bigint(20) unsigned DEFAULT NULL,
  `related_files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`related_files`)),
  `thumbnail_path` varchar(191) DEFAULT NULL,
  `design_standards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`design_standards`)),
  `tolerance_standards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tolerance_standards`)),
  `quality_requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`quality_requirements`)),
  `version_number` int(11) NOT NULL DEFAULT 1,
  `parent_file_id` bigint(20) unsigned DEFAULT NULL,
  `version_notes` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `visibility` enum('public','private','company_only') NOT NULL DEFAULT 'private',
  `license_type` enum('free','commercial','educational','open_source') NOT NULL DEFAULT 'free',
  `price` decimal(10,2) DEFAULT NULL,
  `usage_rights` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`usage_rights`)),
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`keywords`)),
  `industry_category` varchar(191) DEFAULT NULL,
  `application_area` varchar(191) DEFAULT NULL,
  `complexity_level` varchar(191) DEFAULT NULL,
  `download_count` int(11) NOT NULL DEFAULT 0,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `like_count` int(11) NOT NULL DEFAULT 0,
  `rating_average` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `processing_status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `processing_log` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`processing_log`)),
  `processed_at` timestamp NULL DEFAULT NULL,
  `status` enum('draft','pending','approved','rejected','archived') NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `virus_scanned` tinyint(1) NOT NULL DEFAULT 0,
  `virus_scan_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cad_files_uuid_unique` (`uuid`),
  UNIQUE KEY `cad_files_file_number_unique` (`file_number`),
  KEY `cad_files_technical_drawing_id_foreign` (`technical_drawing_id`),
  KEY `cad_files_parent_file_id_foreign` (`parent_file_id`),
  KEY `cad_files_approved_by_foreign` (`approved_by`),
  KEY `cad_files_created_by_status_index` (`created_by`,`status`),
  KEY `cad_files_company_id_visibility_index` (`company_id`,`visibility`),
  KEY `cad_files_file_number_version_index` (`file_number`,`version`),
  KEY `cad_files_cad_software_model_type_index` (`cad_software`,`model_type`),
  KEY `cad_files_industry_category_application_area_index` (`industry_category`,`application_area`),
  KEY `cad_files_is_featured_is_active_index` (`is_featured`,`is_active`),
  KEY `cad_files_processing_status_virus_scanned_index` (`processing_status`,`virus_scanned`),
  KEY `cad_files_created_at_status_index` (`created_at`,`status`),
  CONSTRAINT `cad_files_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cad_files_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `marketplace_sellers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cad_files_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cad_files_parent_file_id_foreign` FOREIGN KEY (`parent_file_id`) REFERENCES `cad_files` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cad_files_technical_drawing_id_foreign` FOREIGN KEY (`technical_drawing_id`) REFERENCES `technical_drawings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: cart_items
DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE `cart_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` uuid NOT NULL,
  `shopping_cart_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(12,2) NOT NULL,
  `sale_price` decimal(12,2) DEFAULT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `product_name` varchar(191) NOT NULL,
  `product_sku` varchar(191) DEFAULT NULL,
  `product_image` varchar(191) DEFAULT NULL,
  `product_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`product_options`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cart_product` (`shopping_cart_id`,`product_id`),
  UNIQUE KEY `cart_items_uuid_unique` (`uuid`),
  KEY `cart_items_shopping_cart_id_product_id_index` (`shopping_cart_id`,`product_id`),
  KEY `cart_items_product_id_index` (`product_id`),
  CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `marketplace_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_shopping_cart_id_foreign` FOREIGN KEY (`shopping_cart_id`) REFERENCES `shopping_carts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: categories
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

-- Table: centralized_payments
DROP TABLE IF EXISTS `centralized_payments`;
CREATE TABLE `centralized_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_reference` varchar(191) NOT NULL COMMENT 'Mã tham chiếu thanh toán',
  `order_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `customer_email` varchar(191) NOT NULL,
  `payment_method` enum('stripe','sepay') NOT NULL,
  `gateway_transaction_id` varchar(191) DEFAULT NULL,
  `gateway_payment_intent_id` varchar(191) DEFAULT NULL,
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `gross_amount` decimal(12,2) NOT NULL COMMENT 'Tổng tiền khách hàng trả',
  `gateway_fee` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Phí payment gateway',
  `net_received` decimal(12,2) NOT NULL COMMENT 'Tiền thực nhận vào Admin account',
  `status` enum('pending','processing','completed','failed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `centralized_payments_payment_reference_unique` (`payment_reference`),
  KEY `centralized_payments_order_id_status_index` (`order_id`,`status`),
  KEY `centralized_payments_customer_id_status_index` (`customer_id`,`status`),
  KEY `centralized_payments_payment_method_status_index` (`payment_method`,`status`),
  KEY `centralized_payments_gateway_transaction_id_index` (`gateway_transaction_id`),
  KEY `centralized_payments_paid_at_status_index` (`paid_at`,`status`),
  CONSTRAINT `centralized_payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `centralized_payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `marketplace_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: comment_dislikes
DROP TABLE IF EXISTS `comment_dislikes`;
CREATE TABLE `comment_dislikes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `comment_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `comment_dislikes_user_id_comment_id_unique` (`user_id`,`comment_id`),
  KEY `comment_dislikes_comment_id_created_at_index` (`comment_id`,`created_at`),
  CONSTRAINT `comment_dislikes_comment_id_foreign` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comment_dislikes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: comment_likes
DROP TABLE IF EXISTS `comment_likes`;
CREATE TABLE `comment_likes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `comment_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `comment_likes_user_id_comment_id_unique` (`user_id`,`comment_id`),
  KEY `comment_likes_comment_id_created_at_index` (`comment_id`,`created_at`),
  CONSTRAINT `comment_likes_comment_id_foreign` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comment_likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=833 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: comments
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
  KEY `comments_thread_created_idx` (`thread_id`,`created_at`),
  KEY `comments_user_thread_created_idx` (`user_id`,`thread_id`,`created_at`),
  FULLTEXT KEY `comments_content_search` (`content`),
  CONSTRAINT `comments_edited_by_foreign` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `comments_thread_id_foreign` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=360 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: commission_settings
DROP TABLE IF EXISTS `commission_settings`;
CREATE TABLE `commission_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `seller_role` enum('manufacturer','supplier','brand','verified_partner') NOT NULL,
  `product_type` enum('digital','new_product','used_product','service') DEFAULT NULL,
  `commission_rate` decimal(5,2) NOT NULL COMMENT 'Tỷ lệ hoa hồng (%)',
  `fixed_fee` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Phí cố định (VNĐ)',
  `min_commission` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Hoa hồng tối thiểu (VNĐ)',
  `max_commission` decimal(10,2) DEFAULT NULL COMMENT 'Hoa hồng tối đa (VNĐ)',
  `min_order_value` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Giá trị đơn hàng tối thiểu',
  `special_conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Điều kiện đặc biệt' CHECK (json_valid(`special_conditions`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `description` varchar(191) DEFAULT NULL,
  `effective_from` timestamp NOT NULL DEFAULT '2025-07-11 04:21:36',
  `effective_until` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `commission_settings_created_by_foreign` (`created_by`),
  KEY `commission_settings_updated_by_foreign` (`updated_by`),
  KEY `commission_settings_seller_role_product_type_is_active_index` (`seller_role`,`product_type`,`is_active`),
  KEY `commission_settings_effective_from_effective_until_index` (`effective_from`,`effective_until`),
  CONSTRAINT `commission_settings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commission_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: commissions
DROP TABLE IF EXISTS `commissions`;
CREATE TABLE `commissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `seller_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `transaction_type` varchar(50) NOT NULL,
  `gross_amount` decimal(15,2) NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL,
  `commission_amount` decimal(15,2) NOT NULL,
  `seller_earnings` decimal(15,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'VND',
  `status` enum('pending','calculated','paid') NOT NULL DEFAULT 'pending',
  `period` varchar(7) NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `calculated_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `commissions_order_id_foreign` (`order_id`),
  KEY `commissions_seller_id_status_index` (`seller_id`,`status`),
  KEY `commissions_period_status_index` (`period`,`status`),
  KEY `commissions_transaction_type_index` (`transaction_type`),
  CONSTRAINT `commissions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `commissions_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: content_blocks
DROP TABLE IF EXISTS `content_blocks`;
CREATE TABLE `content_blocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `content` longtext NOT NULL,
  `block_type` enum('engineering_formula','material_properties','standard_table','calculation_example','cad_snippet','code_block','diagram_embed','reference_link') NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `content_format` varchar(50) NOT NULL DEFAULT 'html',
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `engineering_domain` enum('mechanical_design','manufacturing','materials','thermodynamics','fluid_mechanics','controls','fea_analysis','cad_cam') DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `reference_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_blocks_slug_unique` (`slug`),
  KEY `content_blocks_created_by_foreign` (`created_by`),
  KEY `blk_type_public_approved_idx` (`block_type`,`is_public`,`is_approved`),
  KEY `blk_domain_date_idx` (`engineering_domain`,`created_at`),
  FULLTEXT KEY `content_blocks_title_content_fulltext` (`title`,`content`),
  CONSTRAINT `content_blocks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: content_categories
DROP TABLE IF EXISTS `content_categories`;
CREATE TABLE `content_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `icon_url` varchar(191) DEFAULT NULL,
  `color_code` varchar(7) DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `category_type` enum('engineering_discipline','content_type','skill_level','industry_sector','software_category') NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `show_in_menu` tinyint(1) NOT NULL DEFAULT 1,
  `content_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_categories_slug_unique` (`slug`),
  KEY `cat_parent_sort_idx` (`parent_id`,`sort_order`),
  KEY `cat_type_active_idx` (`category_type`,`is_active`),
  KEY `cat_menu_sort_idx` (`show_in_menu`,`sort_order`),
  CONSTRAINT `content_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `content_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: content_revisions
DROP TABLE IF EXISTS `content_revisions`;
CREATE TABLE `content_revisions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `revisionable_type` varchar(191) NOT NULL,
  `revisionable_id` bigint(20) unsigned NOT NULL,
  `revision_number` int(11) NOT NULL,
  `content_snapshot` longtext NOT NULL,
  `metadata_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata_snapshot`)),
  `change_summary` varchar(191) DEFAULT NULL,
  `change_type` enum('technical_correction','content_update','formatting_fix','standard_update','formula_revision','procedure_update') NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `editor_notes` varchar(191) DEFAULT NULL,
  `is_major_revision` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `content_revisions_revisionable_type_revisionable_id_index` (`revisionable_type`,`revisionable_id`),
  KEY `rev_morphs_rev_idx` (`revisionable_type`,`revisionable_id`,`revision_number`),
  KEY `rev_creator_date_idx` (`created_by`,`created_at`),
  CONSTRAINT `content_revisions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: content_templates
DROP TABLE IF EXISTS `content_templates`;
CREATE TABLE `content_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `template_content` longtext NOT NULL,
  `template_type` enum('calculation_guide','cad_tutorial','fea_procedure','manufacturing_process','safety_protocol','design_standard','material_spec','troubleshooting') NOT NULL,
  `template_variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`template_variables`)),
  `required_skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`required_skills`)),
  `difficulty_level` enum('beginner','intermediate','advanced','expert') NOT NULL,
  `industry_sector` varchar(191) DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `usage_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_templates_slug_unique` (`slug`),
  KEY `content_templates_created_by_foreign` (`created_by`),
  KEY `content_templates_updated_by_foreign` (`updated_by`),
  KEY `tmpl_type_active_idx` (`template_type`,`is_active`),
  KEY `tmpl_diff_sector_idx` (`difficulty_level`,`industry_sector`),
  KEY `tmpl_featured_date_idx` (`is_featured`,`created_at`),
  FULLTEXT KEY `content_templates_name_description_template_content_fulltext` (`name`,`description`,`template_content`),
  CONSTRAINT `content_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `content_templates_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: conversation_participants
DROP TABLE IF EXISTS `conversation_participants`;
CREATE TABLE `conversation_participants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `last_read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `conversation_participants_conversation_id_user_id_unique` (`conversation_id`,`user_id`),
  KEY `conversation_participants_user_id_last_read_at_index` (`user_id`,`last_read_at`),
  CONSTRAINT `conversation_participants_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversation_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: conversations
DROP TABLE IF EXISTS `conversations`;
CREATE TABLE `conversations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conversations_updated_at_index` (`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: countries
DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `name_local` varchar(191) DEFAULT NULL,
  `code` varchar(2) NOT NULL,
  `code_alpha3` varchar(3) NOT NULL,
  `phone_code` varchar(10) DEFAULT NULL,
  `currency_code` varchar(3) DEFAULT NULL,
  `currency_symbol` varchar(10) DEFAULT NULL,
  `continent` varchar(191) DEFAULT NULL,
  `timezone` varchar(191) DEFAULT NULL,
  `timezones` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`timezones`)),
  `language_code` varchar(5) NOT NULL DEFAULT 'en',
  `languages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`languages`)),
  `measurement_system` enum('metric','imperial','mixed') NOT NULL DEFAULT 'metric',
  `standard_organizations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`standard_organizations`)),
  `common_cad_software` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`common_cad_software`)),
  `flag_emoji` varchar(10) DEFAULT NULL,
  `flag_icon` varchar(191) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `allow_user_registration` tinyint(1) NOT NULL DEFAULT 1,
  `mechanical_specialties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`mechanical_specialties`)),
  `industrial_sectors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`industrial_sectors`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `countries_code_unique` (`code`),
  UNIQUE KEY `countries_code_alpha3_unique` (`code_alpha3`),
  KEY `countries_is_active_sort_order_index` (`is_active`,`sort_order`),
  KEY `countries_continent_is_active_index` (`continent`,`is_active`),
  KEY `countries_allow_user_registration_index` (`allow_user_registration`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: documentation_categories
DROP TABLE IF EXISTS `documentation_categories`;
CREATE TABLE `documentation_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `color_code` varchar(7) NOT NULL DEFAULT '#007bff',
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `allowed_roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allowed_roles`)),
  `document_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `documentation_categories_slug_unique` (`slug`),
  KEY `documentation_categories_parent_id_sort_order_index` (`parent_id`,`sort_order`),
  KEY `documentation_categories_is_active_is_public_index` (`is_active`,`is_public`),
  KEY `documentation_categories_slug_index` (`slug`),
  CONSTRAINT `documentation_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `documentation_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: documentation_comments
DROP TABLE IF EXISTS `documentation_comments`;
CREATE TABLE `documentation_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentation_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `is_staff_response` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documentation_comments_documentation_id_status_created_at_index` (`documentation_id`,`status`,`created_at`),
  KEY `documentation_comments_parent_id_created_at_index` (`parent_id`,`created_at`),
  KEY `documentation_comments_user_id_index` (`user_id`),
  CONSTRAINT `documentation_comments_documentation_id_foreign` FOREIGN KEY (`documentation_id`) REFERENCES `documentations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documentation_comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `documentation_comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documentation_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: documentation_downloads
DROP TABLE IF EXISTS `documentation_downloads`;
CREATE TABLE `documentation_downloads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentation_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `file_name` varchar(191) NOT NULL,
  `file_path` varchar(191) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documentation_downloads_documentation_id_created_at_index` (`documentation_id`,`created_at`),
  KEY `documentation_downloads_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `documentation_downloads_documentation_id_foreign` FOREIGN KEY (`documentation_id`) REFERENCES `documentations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documentation_downloads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: documentation_ratings
DROP TABLE IF EXISTS `documentation_ratings`;
CREATE TABLE `documentation_ratings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentation_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL,
  `comment` text DEFAULT NULL,
  `is_helpful` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `documentation_ratings_documentation_id_user_id_unique` (`documentation_id`,`user_id`),
  KEY `documentation_ratings_documentation_id_rating_index` (`documentation_id`,`rating`),
  KEY `documentation_ratings_user_id_index` (`user_id`),
  CONSTRAINT `documentation_ratings_documentation_id_foreign` FOREIGN KEY (`documentation_id`) REFERENCES `documentations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documentation_ratings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: documentation_versions
DROP TABLE IF EXISTS `documentation_versions`;
CREATE TABLE `documentation_versions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentation_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `version_number` varchar(191) NOT NULL,
  `content` longtext NOT NULL,
  `change_summary` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `is_major_version` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documentation_versions_user_id_foreign` (`user_id`),
  KEY `documentation_versions_documentation_id_created_at_index` (`documentation_id`,`created_at`),
  KEY `documentation_versions_version_number_index` (`version_number`),
  CONSTRAINT `documentation_versions_documentation_id_foreign` FOREIGN KEY (`documentation_id`) REFERENCES `documentations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documentation_versions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: documentation_views
DROP TABLE IF EXISTS `documentation_views`;
CREATE TABLE `documentation_views` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentation_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(191) DEFAULT NULL,
  `referrer` varchar(191) DEFAULT NULL,
  `time_spent` int(11) DEFAULT NULL,
  `scroll_percentage` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documentation_views_documentation_id_created_at_index` (`documentation_id`,`created_at`),
  KEY `documentation_views_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `documentation_views_ip_address_index` (`ip_address`),
  CONSTRAINT `documentation_views_documentation_id_foreign` FOREIGN KEY (`documentation_id`) REFERENCES `documentations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documentation_views_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: documentations
DROP TABLE IF EXISTS `documentations`;
CREATE TABLE `documentations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `content` longtext NOT NULL,
  `excerpt` text DEFAULT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  `reviewer_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('draft','review','published','archived') NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `allowed_roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allowed_roles`)),
  `content_type` enum('guide','api','tutorial','reference','faq') NOT NULL DEFAULT 'guide',
  `difficulty_level` enum('beginner','intermediate','advanced','expert') NOT NULL DEFAULT 'beginner',
  `estimated_read_time` int(11) DEFAULT NULL,
  `meta_title` varchar(191) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `featured_image` varchar(191) DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `rating_average` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `download_count` int(11) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `related_docs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`related_docs`)),
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `downloadable_files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`downloadable_files`)),
  `published_at` timestamp NULL DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `documentations_slug_unique` (`slug`),
  KEY `documentations_author_id_foreign` (`author_id`),
  KEY `documentations_reviewer_id_foreign` (`reviewer_id`),
  KEY `documentations_category_id_status_sort_order_index` (`category_id`,`status`,`sort_order`),
  KEY `documentations_is_public_status_published_at_index` (`is_public`,`status`,`published_at`),
  KEY `documentations_content_type_difficulty_level_index` (`content_type`,`difficulty_level`),
  KEY `documentations_is_featured_published_at_index` (`is_featured`,`published_at`),
  KEY `documentations_slug_index` (`slug`),
  FULLTEXT KEY `documentations_title_content_excerpt_fulltext` (`title`,`content`,`excerpt`),
  CONSTRAINT `documentations_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
  CONSTRAINT `documentations_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `documentation_categories` (`id`),
  CONSTRAINT `documentations_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: download_tokens
DROP TABLE IF EXISTS `download_tokens`;
CREATE TABLE `download_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(64) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `product_purchase_id` bigint(20) unsigned NOT NULL,
  `protected_file_id` bigint(20) unsigned NOT NULL,
  `expires_at` timestamp NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(191) DEFAULT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `used_at` timestamp NULL DEFAULT NULL,
  `download_attempts` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `download_tokens_token_unique` (`token`),
  KEY `download_tokens_protected_file_id_foreign` (`protected_file_id`),
  KEY `download_tokens_token_expires_at_index` (`token`,`expires_at`),
  KEY `download_tokens_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `download_tokens_product_purchase_id_is_used_index` (`product_purchase_id`,`is_used`),
  CONSTRAINT `download_tokens_product_purchase_id_foreign` FOREIGN KEY (`product_purchase_id`) REFERENCES `product_purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `download_tokens_protected_file_id_foreign` FOREIGN KEY (`protected_file_id`) REFERENCES `protected_files` (`id`) ON DELETE CASCADE,
  CONSTRAINT `download_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: educational_resources
DROP TABLE IF EXISTS `educational_resources`;
CREATE TABLE `educational_resources` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `content` longtext DEFAULT NULL,
  `category` varchar(191) NOT NULL,
  `type` varchar(191) NOT NULL,
  `difficulty_level` enum('beginner','intermediate','advanced','expert') NOT NULL DEFAULT 'beginner',
  `file_path` varchar(191) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `file_type` varchar(191) DEFAULT NULL,
  `thumbnail_path` varchar(191) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `language` varchar(10) NOT NULL DEFAULT 'en',
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `user_id` bigint(20) unsigned NOT NULL,
  `university_id` bigint(20) unsigned DEFAULT NULL,
  `course_code` varchar(191) DEFAULT NULL,
  `academic_year` varchar(191) DEFAULT NULL,
  `semester` varchar(191) DEFAULT NULL,
  `prerequisites` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`prerequisites`)),
  `learning_objectives` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`learning_objectives`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `view_count` bigint(20) NOT NULL DEFAULT 0,
  `download_count` bigint(20) NOT NULL DEFAULT 0,
  `rating_average` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: engineering_standards
DROP TABLE IF EXISTS `engineering_standards`;
CREATE TABLE `engineering_standards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` uuid NOT NULL,
  `title` varchar(191) NOT NULL,
  `standard_number` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `organization` varchar(191) NOT NULL,
  `category` varchar(191) NOT NULL,
  `version` varchar(191) DEFAULT NULL,
  `publication_date` date DEFAULT NULL,
  `revision_date` date DEFAULT NULL,
  `next_review_date` date DEFAULT NULL,
  `status` enum('current','superseded','withdrawn','under_review') NOT NULL DEFAULT 'current',
  `scope` text DEFAULT NULL,
  `applicable_industries` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`applicable_industries`)),
  `applicable_processes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`applicable_processes`)),
  `applicable_materials` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`applicable_materials`)),
  `product_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`product_types`)),
  `key_requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`key_requirements`)),
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifications`)),
  `test_methods` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`test_methods`)),
  `acceptance_criteria` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`acceptance_criteria`)),
  `measurement_methods` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`measurement_methods`)),
  `compliance_level` enum('mandatory','recommended','optional') NOT NULL DEFAULT 'recommended',
  `regulatory_requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`regulatory_requirements`)),
  `certification_bodies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`certification_bodies`)),
  `compliance_costs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`compliance_costs`)),
  `supersedes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`supersedes`)),
  `superseded_by` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`superseded_by`)),
  `related_standards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`related_standards`)),
  `referenced_standards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`referenced_standards`)),
  `implementation_guidelines` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`implementation_guidelines`)),
  `common_interpretations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`common_interpretations`)),
  `implementation_challenges` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`implementation_challenges`)),
  `best_practices` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`best_practices`)),
  `document_path` varchar(191) DEFAULT NULL,
  `summary_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`summary_documents`)),
  `training_materials` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`training_materials`)),
  `case_studies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`case_studies`)),
  `geographic_scope` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`geographic_scope`)),
  `national_adoptions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`national_adoptions`)),
  `regional_variations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`regional_variations`)),
  `business_impact` text DEFAULT NULL,
  `affected_stakeholders` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`affected_stakeholders`)),
  `implementation_timeline` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`implementation_timeline`)),
  `recent_changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recent_changes`)),
  `planned_changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`planned_changes`)),
  `change_rationale` text DEFAULT NULL,
  `training_providers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`training_providers`)),
  `consulting_services` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`consulting_services`)),
  `software_tools` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`software_tools`)),
  `useful_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`useful_links`)),
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`keywords`)),
  `created_by_user` varchar(191) DEFAULT NULL,
  `verified_by` varchar(191) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `download_count` int(11) NOT NULL DEFAULT 0,
  `reference_count` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `admin_status` enum('draft','pending','approved') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `engineering_standards_uuid_unique` (`uuid`),
  UNIQUE KEY `engineering_standards_standard_number_unique` (`standard_number`),
  KEY `engineering_standards_organization_category_index` (`organization`,`category`),
  KEY `engineering_standards_status_is_active_index` (`status`,`is_active`),
  KEY `engineering_standards_standard_number_version_index` (`standard_number`,`version`),
  KEY `engineering_standards_publication_date_revision_date_index` (`publication_date`,`revision_date`),
  KEY `engineering_standards_is_featured_admin_status_index` (`is_featured`,`admin_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: faq_categories
DROP TABLE IF EXISTS `faq_categories`;
CREATE TABLE `faq_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `icon_url` varchar(191) DEFAULT NULL,
  `engineering_domain` enum('mechanical_design','manufacturing','materials','cad_software','analysis_simulation','career_guidance','general') NOT NULL DEFAULT 'general',
  `faq_count` int(11) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `faq_categories_slug_unique` (`slug`),
  KEY `faq_categories_engineering_domain_is_active_order_index` (`engineering_domain`,`is_active`,`order`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: faqs
DROP TABLE IF EXISTS `faqs`;
CREATE TABLE `faqs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(191) NOT NULL,
  `answer` text NOT NULL,
  `faq_type` enum('software_usage','calculation_method','design_standard','material_property','manufacturing_process','career_advice','general_engineering') NOT NULL DEFAULT 'general_engineering',
  `related_topics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`related_topics`)),
  `applicable_standards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`applicable_standards`)),
  `code_example` text DEFAULT NULL,
  `difficulty_level` varchar(191) NOT NULL DEFAULT 'beginner',
  `category_id` bigint(20) unsigned NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `helpful_votes` int(11) NOT NULL DEFAULT 0,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `last_updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faqs_reviewed_by_foreign` (`reviewed_by`),
  KEY `faqs_category_id_faq_type_is_active_index` (`category_id`,`faq_type`,`is_active`),
  KEY `faqs_difficulty_level_helpful_votes_index` (`difficulty_level`,`helpful_votes`),
  KEY `faqs_created_by_created_at_index` (`created_by`,`created_at`),
  FULLTEXT KEY `faqs_question_answer_fulltext` (`question`,`answer`),
  CONSTRAINT `faqs_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `faq_categories` (`id`),
  CONSTRAINT `faqs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `faqs_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: followers
DROP TABLE IF EXISTS `followers`;
CREATE TABLE `followers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `follower_id` bigint(20) unsigned NOT NULL,
  `following_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `followers_follower_id_following_id_unique` (`follower_id`,`following_id`),
  KEY `followers_following_id_created_at_index` (`following_id`,`created_at`),
  KEY `followers_follower_id_created_at_index` (`follower_id`,`created_at`),
  CONSTRAINT `followers_follower_id_foreign` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `followers_following_id_foreign` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: forums
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

-- Table: job_batches
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: jobs
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: knowledge_articles
DROP TABLE IF EXISTS `knowledge_articles`;
CREATE TABLE `knowledge_articles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `article_type` enum('tutorial','best_practice','case_study','troubleshooting','standard_procedure','design_guide','calculation_method','software_guide') NOT NULL,
  `engineering_field` enum('mechanical_design','manufacturing_engineering','materials_engineering','automotive_engineering','aerospace_engineering','industrial_engineering','quality_engineering','maintenance_engineering') DEFAULT NULL,
  `prerequisites` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`prerequisites`)),
  `learning_outcomes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`learning_outcomes`)),
  `difficulty_level` enum('beginner','intermediate','advanced','expert') NOT NULL,
  `estimated_read_time` int(11) DEFAULT NULL,
  `featured_image` varchar(191) DEFAULT NULL,
  `technical_specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`technical_specs`)),
  `software_requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`software_requirements`)),
  `author_id` bigint(20) unsigned NOT NULL,
  `reviewer_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('draft','review','published','archived') NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `rating_average` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `requires_pe_license` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `knowledge_articles_slug_unique` (`slug`),
  KEY `knowledge_articles_reviewer_id_foreign` (`reviewer_id`),
  KEY `art_type_status_pub_idx` (`article_type`,`status`,`published_at`),
  KEY `art_field_diff_idx` (`engineering_field`,`difficulty_level`),
  KEY `art_featured_rating_idx` (`is_featured`,`rating_average`),
  KEY `art_author_date_idx` (`author_id`,`created_at`),
  KEY `knowledge_articles_category_id_index` (`category_id`),
  FULLTEXT KEY `knowledge_articles_title_excerpt_content_fulltext` (`title`,`excerpt`,`content`),
  CONSTRAINT `knowledge_articles_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
  CONSTRAINT `knowledge_articles_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `knowledge_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `knowledge_articles_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: knowledge_bookmarks
DROP TABLE IF EXISTS `knowledge_bookmarks`;
CREATE TABLE `knowledge_bookmarks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `bookmarkable_type` varchar(191) NOT NULL,
  `bookmarkable_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `knowledge_bookmarks_user_id_foreign` (`user_id`),
  CONSTRAINT `knowledge_bookmarks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: knowledge_categories
DROP TABLE IF EXISTS `knowledge_categories`;
CREATE TABLE `knowledge_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#007bff',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `knowledge_categories_slug_unique` (`slug`),
  KEY `knowledge_categories_is_active_sort_order_index` (`is_active`,`sort_order`),
  KEY `knowledge_categories_parent_id_index` (`parent_id`),
  CONSTRAINT `knowledge_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `knowledge_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: knowledge_documents
DROP TABLE IF EXISTS `knowledge_documents`;
CREATE TABLE `knowledge_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(191) NOT NULL,
  `file_type` varchar(191) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `original_filename` varchar(191) NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `download_count` int(11) NOT NULL DEFAULT 0,
  `rating_average` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `knowledge_documents_slug_unique` (`slug`),
  KEY `knowledge_documents_author_id_foreign` (`author_id`),
  KEY `knowledge_documents_status_published_at_index` (`status`,`published_at`),
  KEY `knowledge_documents_category_id_status_index` (`category_id`,`status`),
  KEY `knowledge_documents_file_type_index` (`file_type`),
  KEY `knowledge_documents_is_featured_index` (`is_featured`),
  FULLTEXT KEY `knowledge_documents_title_description_fulltext` (`title`,`description`),
  CONSTRAINT `knowledge_documents_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `knowledge_documents_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `knowledge_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: knowledge_tags
DROP TABLE IF EXISTS `knowledge_tags`;
CREATE TABLE `knowledge_tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#6c757d',
  `usage_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `knowledge_tags_slug_unique` (`slug`),
  KEY `knowledge_tags_usage_count_index` (`usage_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: knowledge_videos
DROP TABLE IF EXISTS `knowledge_videos`;
CREATE TABLE `knowledge_videos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `video_url` varchar(191) NOT NULL,
  `video_type` varchar(191) NOT NULL DEFAULT 'youtube',
  `thumbnail` varchar(191) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  `difficulty_level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `views_count` int(11) NOT NULL DEFAULT 0,
  `rating_average` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `knowledge_videos_slug_unique` (`slug`),
  KEY `knowledge_videos_author_id_foreign` (`author_id`),
  KEY `knowledge_videos_status_published_at_index` (`status`,`published_at`),
  KEY `knowledge_videos_category_id_status_index` (`category_id`,`status`),
  KEY `knowledge_videos_is_featured_index` (`is_featured`),
  FULLTEXT KEY `knowledge_videos_title_description_fulltext` (`title`,`description`),
  CONSTRAINT `knowledge_videos_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `knowledge_videos_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `knowledge_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: manufacturing_processes
DROP TABLE IF EXISTS `manufacturing_processes`;
CREATE TABLE `manufacturing_processes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` uuid NOT NULL,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(191) NOT NULL,
  `subcategory` varchar(191) DEFAULT NULL,
  `process_type` varchar(191) NOT NULL,
  `alternative_names` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`alternative_names`)),
  `materials_compatible` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`materials_compatible`)),
  `material_limitations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`material_limitations`)),
  `dimensional_capabilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dimensional_capabilities`)),
  `surface_finish_range` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`surface_finish_range`)),
  `tolerance_capabilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tolerance_capabilities`)),
  `required_equipment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`required_equipment`)),
  `tooling_requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tooling_requirements`)),
  `setup_requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`setup_requirements`)),
  `operating_parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`operating_parameters`)),
  `parameter_ranges` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`parameter_ranges`)),
  `optimization_guidelines` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`optimization_guidelines`)),
  `geometric_capabilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`geometric_capabilities`)),
  `geometric_limitations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`geometric_limitations`)),
  `min_feature_size` decimal(8,4) DEFAULT NULL,
  `max_part_size` decimal(12,2) DEFAULT NULL,
  `complexity_rating` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`complexity_rating`)),
  `quality_standards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`quality_standards`)),
  `inspection_methods` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`inspection_methods`)),
  `typical_defects` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`typical_defects`)),
  `prevention_methods` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`prevention_methods`)),
  `setup_cost` decimal(10,2) DEFAULT NULL,
  `unit_cost_factor` decimal(8,4) DEFAULT NULL,
  `minimum_quantity` int(11) DEFAULT NULL,
  `cost_drivers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cost_drivers`)),
  `setup_time_hours` decimal(8,2) DEFAULT NULL,
  `cycle_time_factor` decimal(8,4) DEFAULT NULL,
  `production_rate_factors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`production_rate_factors`)),
  `lead_time_days` int(11) DEFAULT NULL,
  `environmental_impact` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`environmental_impact`)),
  `safety_requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`safety_requirements`)),
  `waste_products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`waste_products`)),
  `requires_special_handling` tinyint(1) NOT NULL DEFAULT 0,
  `typical_applications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`typical_applications`)),
  `industries` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`industries`)),
  `part_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`part_types`)),
  `prerequisite_processes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`prerequisite_processes`)),
  `subsequent_processes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`subsequent_processes`)),
  `alternative_processes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`alternative_processes`)),
  `process_sheet_path` varchar(191) DEFAULT NULL,
  `reference_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`reference_documents`)),
  `case_studies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`case_studies`)),
  `video_tutorials` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`video_tutorials`)),
  `service_providers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`service_providers`)),
  `equipment_suppliers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`equipment_suppliers`)),
  `geographic_availability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`geographic_availability`)),
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`keywords`)),
  `created_by_user` varchar(191) DEFAULT NULL,
  `verified_by` varchar(191) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `status` enum('draft','pending','approved','deprecated') NOT NULL DEFAULT 'draft',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `usage_count` int(11) NOT NULL DEFAULT 0,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `manufacturing_processes_uuid_unique` (`uuid`),
  UNIQUE KEY `manufacturing_processes_code_unique` (`code`),
  KEY `manufacturing_processes_category_subcategory_index` (`category`,`subcategory`),
  KEY `manufacturing_processes_process_type_status_index` (`process_type`,`status`),
  KEY `manufacturing_processes_is_active_is_featured_index` (`is_active`,`is_featured`),
  KEY `manufacturing_processes_name_code_index` (`name`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: marketplace_cart_items
DROP TABLE IF EXISTS `marketplace_cart_items`;
CREATE TABLE `marketplace_cart_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` uuid NOT NULL,
  `shopping_cart_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(12,2) NOT NULL,
  `sale_price` decimal(12,2) DEFAULT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `product_name` varchar(191) NOT NULL,
  `product_sku` varchar(191) DEFAULT NULL,
  `product_image` varchar(191) DEFAULT NULL,
  `product_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`product_options`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cart_product` (`shopping_cart_id`,`product_id`),
  UNIQUE KEY `marketplace_cart_items_uuid_unique` (`uuid`),
  KEY `marketplace_cart_items_shopping_cart_id_product_id_index` (`shopping_cart_id`,`product_id`),
  KEY `marketplace_cart_items_product_id_index` (`product_id`),
  CONSTRAINT `marketplace_cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `marketplace_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_cart_items_shopping_cart_id_foreign` FOREIGN KEY (`shopping_cart_id`) REFERENCES `marketplace_shopping_carts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: marketplace_download_history
DROP TABLE IF EXISTS `marketplace_download_history`;
CREATE TABLE `marketplace_download_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` uuid NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `order_item_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `file_name` varchar(191) NOT NULL,
  `file_path` varchar(191) NOT NULL,
  `original_filename` varchar(191) NOT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `mime_type` varchar(191) DEFAULT NULL,
  `downloaded_at` timestamp NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(191) DEFAULT NULL,
  `download_method` varchar(191) NOT NULL DEFAULT 'direct',
  `download_token` varchar(191) DEFAULT NULL,
  `is_valid_download` tinyint(1) NOT NULL DEFAULT 1,
  `validation_status` varchar(191) NOT NULL DEFAULT 'success',
  `validation_notes` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marketplace_download_history_uuid_unique` (`uuid`),
  KEY `marketplace_download_history_order_item_id_foreign` (`order_item_id`),
  KEY `marketplace_download_history_user_id_downloaded_at_index` (`user_id`,`downloaded_at`),
  KEY `marketplace_download_history_order_id_downloaded_at_index` (`order_id`,`downloaded_at`),
  KEY `marketplace_download_history_product_id_downloaded_at_index` (`product_id`,`downloaded_at`),
  KEY `marketplace_download_history_ip_address_downloaded_at_index` (`ip_address`,`downloaded_at`),
  KEY `marketplace_download_history_download_token_index` (`download_token`),
  CONSTRAINT `marketplace_download_history_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `marketplace_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_download_history_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `marketplace_order_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_download_history_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `marketplace_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_download_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: marketplace_order_items
DROP TABLE IF EXISTS `marketplace_order_items`;
CREATE TABLE `marketplace_order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `seller_id` bigint(20) unsigned NOT NULL,
  `product_name` varchar(191) NOT NULL,
  `product_sku` varchar(191) NOT NULL,
  `product_description` text DEFAULT NULL,
  `product_specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`product_specifications`)),
  `unit_price` decimal(12,2) NOT NULL,
  `sale_price` decimal(12,2) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL,
  `download_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`download_links`)),
  `download_count` int(11) NOT NULL DEFAULT 0,
  `download_limit` int(11) DEFAULT NULL,
  `download_expires_at` timestamp NULL DEFAULT NULL,
  `fulfillment_status` enum('pending','processing','ready_to_ship','shipped','delivered','downloaded','completed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `commission_rate` decimal(5,2) NOT NULL,
  `commission_amount` decimal(12,2) NOT NULL,
  `payout_request_id` bigint(20) unsigned DEFAULT NULL,
  `included_in_payout` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Đã bao gồm trong payout chưa',
  `payout_included_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian include vào payout',
  `admin_commission` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Hoa hồng admin nhận được',
  `gateway_fee_share` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Phần phí gateway seller chịu',
  `seller_earnings` decimal(12,2) NOT NULL,
  `tracking_number` varchar(191) DEFAULT NULL,
  `seller_notes` text DEFAULT NULL,
  `customer_notes` text DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `marketplace_order_items_order_id_seller_id_index` (`order_id`,`seller_id`),
  KEY `marketplace_order_items_product_id_fulfillment_status_index` (`product_id`,`fulfillment_status`),
  KEY `marketplace_order_items_seller_id_fulfillment_status_index` (`seller_id`,`fulfillment_status`),
  KEY `marketplace_order_items_payout_request_id_index` (`payout_request_id`),
  KEY `marketplace_order_items_included_in_payout_seller_id_index` (`included_in_payout`,`seller_id`),
  CONSTRAINT `marketplace_order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `marketplace_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_order_items_payout_request_id_foreign` FOREIGN KEY (`payout_request_id`) REFERENCES `seller_payout_requests` (`id`) ON DELETE SET NULL,
  CONSTRAINT `marketplace_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `marketplace_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_order_items_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `marketplace_sellers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=229 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: marketplace_orders
DROP TABLE IF EXISTS `marketplace_orders`;
CREATE TABLE `marketplace_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` uuid NOT NULL,
  `order_number` varchar(191) NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `customer_email` varchar(191) NOT NULL,
  `customer_phone` varchar(191) DEFAULT NULL,
  `order_type` enum('product_purchase','service_booking','digital_download') NOT NULL DEFAULT 'product_purchase',
  `subtotal` decimal(12,2) NOT NULL,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shipping_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'VND',
  `status` enum('pending','confirmed','processing','shipped','delivered','completed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','processing','paid','failed','refunded','partially_refunded') NOT NULL DEFAULT 'pending',
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`shipping_address`)),
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`billing_address`)),
  `shipping_method` varchar(191) DEFAULT NULL,
  `tracking_number` varchar(191) DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(191) DEFAULT NULL,
  `payment_gateway_id` varchar(191) DEFAULT NULL,
  `payment_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_details`)),
  `centralized_payment_id` bigint(20) unsigned DEFAULT NULL,
  `requires_admin_review` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Đơn hàng cần admin review',
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian admin review',
  `seller_paid` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Đã trả tiền cho seller chưa',
  `seller_paid_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian trả tiền cho seller',
  `paid_at` timestamp NULL DEFAULT NULL,
  `company_name` varchar(191) DEFAULT NULL,
  `tax_code` varchar(191) DEFAULT NULL,
  `requires_invoice` tinyint(1) NOT NULL DEFAULT 0,
  `invoice_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`invoice_details`)),
  `customer_notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `processing_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marketplace_orders_uuid_unique` (`uuid`),
  UNIQUE KEY `marketplace_orders_order_number_unique` (`order_number`),
  KEY `marketplace_orders_customer_id_status_index` (`customer_id`,`status`),
  KEY `marketplace_orders_status_payment_status_index` (`status`,`payment_status`),
  KEY `marketplace_orders_order_number_index` (`order_number`),
  KEY `marketplace_orders_created_at_status_index` (`created_at`,`status`),
  KEY `marketplace_orders_payment_method_payment_status_index` (`payment_method`,`payment_status`),
  KEY `marketplace_orders_reviewed_by_foreign` (`reviewed_by`),
  KEY `marketplace_orders_centralized_payment_id_index` (`centralized_payment_id`),
  KEY `marketplace_orders_requires_admin_review_status_index` (`requires_admin_review`,`status`),
  KEY `marketplace_orders_seller_paid_created_at_index` (`seller_paid`,`created_at`),
  CONSTRAINT `marketplace_orders_centralized_payment_id_foreign` FOREIGN KEY (`centralized_payment_id`) REFERENCES `centralized_payments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `marketplace_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_orders_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: marketplace_products
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
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: marketplace_sellers
DROP TABLE IF EXISTS `marketplace_sellers`;
CREATE TABLE `marketplace_sellers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` uuid NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `seller_type` enum('supplier','manufacturer','brand') NOT NULL DEFAULT 'supplier',
  `business_type` enum('individual','company','corporation') NOT NULL DEFAULT 'company',
  `business_name` varchar(191) NOT NULL,
  `business_registration_number` varchar(191) DEFAULT NULL,
  `tax_identification_number` varchar(191) DEFAULT NULL,
  `business_description` text DEFAULT NULL,
  `contact_person_name` varchar(191) NOT NULL,
  `contact_email` varchar(191) NOT NULL,
  `contact_phone` varchar(191) NOT NULL,
  `business_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`business_address`)),
  `website_url` varchar(191) DEFAULT NULL,
  `industry_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`industry_categories`)),
  `specializations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specializations`)),
  `certifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`certifications`)),
  `capabilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`capabilities`)),
  `verification_status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` bigint(20) unsigned DEFAULT NULL,
  `verification_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`verification_documents`)),
  `verification_notes` text DEFAULT NULL,
  `rating_average` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `total_sales` int(11) NOT NULL DEFAULT 0,
  `total_revenue` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_products` int(11) NOT NULL DEFAULT 0,
  `active_products` int(11) NOT NULL DEFAULT 0,
  `commission_rate` decimal(5,2) NOT NULL DEFAULT 5.00,
  `bank_information` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Thông tin ngân hàng để nhận tiền' CHECK (json_valid(`bank_information`)),
  `payout_frequency` enum('weekly','biweekly','monthly') NOT NULL DEFAULT 'monthly' COMMENT 'Tần suất nhận tiền',
  `minimum_payout_amount` decimal(12,2) NOT NULL DEFAULT 100000.00 COMMENT 'Số tiền tối thiểu để payout (VNĐ)',
  `pending_earnings` decimal(12,2) NOT NULL DEFAULT 0.00,
  `available_earnings` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_earnings` decimal(15,2) NOT NULL DEFAULT 0.00,
  `pending_payout` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Số tiền chờ thanh toán',
  `total_commission_paid` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Tổng hoa hồng đã trả',
  `last_payout_at` timestamp NULL DEFAULT NULL COMMENT 'Lần payout cuối cùng',
  `payment_methods` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_methods`)),
  `auto_approve_orders` tinyint(1) NOT NULL DEFAULT 0,
  `processing_time_days` int(11) NOT NULL DEFAULT 3,
  `shipping_methods` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`shipping_methods`)),
  `return_policy` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`return_policy`)),
  `terms_conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`terms_conditions`)),
  `status` enum('active','inactive','suspended','banned') NOT NULL DEFAULT 'active',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `last_active_at` timestamp NULL DEFAULT NULL,
  `suspended_at` timestamp NULL DEFAULT NULL,
  `suspension_reason` text DEFAULT NULL,
  `store_name` varchar(191) DEFAULT NULL,
  `store_slug` varchar(191) DEFAULT NULL,
  `store_description` text DEFAULT NULL,
  `store_logo` varchar(191) DEFAULT NULL,
  `store_banner` varchar(191) DEFAULT NULL,
  `store_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`store_settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marketplace_sellers_uuid_unique` (`uuid`),
  UNIQUE KEY `marketplace_sellers_store_slug_unique` (`store_slug`),
  KEY `marketplace_sellers_verified_by_foreign` (`verified_by`),
  KEY `marketplace_sellers_user_id_index` (`user_id`),
  KEY `marketplace_sellers_seller_type_status_index` (`seller_type`,`status`),
  KEY `marketplace_sellers_verification_status_index` (`verification_status`),
  KEY `marketplace_sellers_is_featured_status_index` (`is_featured`,`status`),
  KEY `marketplace_sellers_store_slug_index` (`store_slug`),
  KEY `marketplace_sellers_rating_average_rating_count_index` (`rating_average`,`rating_count`),
  CONSTRAINT `marketplace_sellers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_sellers_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: marketplace_shopping_carts
DROP TABLE IF EXISTS `marketplace_shopping_carts`;
CREATE TABLE `marketplace_shopping_carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` uuid NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `session_id` varchar(191) DEFAULT NULL,
  `status` enum('active','abandoned','converted') NOT NULL DEFAULT 'active',
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shipping_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `expires_at` timestamp NULL DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marketplace_shopping_carts_uuid_unique` (`uuid`),
  KEY `marketplace_shopping_carts_user_id_status_index` (`user_id`,`status`),
  KEY `marketplace_shopping_carts_session_id_status_index` (`session_id`,`status`),
  KEY `marketplace_shopping_carts_expires_at_index` (`expires_at`),
  KEY `marketplace_shopping_carts_session_id_index` (`session_id`),
  CONSTRAINT `marketplace_shopping_carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: marketplace_wishlists
DROP TABLE IF EXISTS `marketplace_wishlists`;
CREATE TABLE `marketplace_wishlists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `notify_when_available` tinyint(1) NOT NULL DEFAULT 1,
  `notify_price_drops` tinyint(1) NOT NULL DEFAULT 1,
  `target_price` decimal(12,2) DEFAULT NULL,
  `last_notified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marketplace_wishlists_user_id_product_id_unique` (`user_id`,`product_id`),
  KEY `marketplace_wishlists_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `marketplace_wishlists_product_id_notify_when_available_index` (`product_id`,`notify_when_available`),
  KEY `marketplace_wishlists_notify_price_drops_index` (`notify_price_drops`),
  CONSTRAINT `marketplace_wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `marketplace_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: materials
DROP TABLE IF EXISTS `materials`;
CREATE TABLE `materials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` uuid NOT NULL,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(191) NOT NULL,
  `subcategory` varchar(191) DEFAULT NULL,
  `material_type` varchar(191) NOT NULL,
  `grade` varchar(191) DEFAULT NULL,
  `alternative_designations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`alternative_designations`)),
  `density` decimal(8,4) DEFAULT NULL,
  `melting_point` decimal(8,2) DEFAULT NULL,
  `thermal_conductivity` decimal(8,4) DEFAULT NULL,
  `thermal_expansion` decimal(12,8) DEFAULT NULL,
  `specific_heat` decimal(8,4) DEFAULT NULL,
  `electrical_resistivity` decimal(12,8) DEFAULT NULL,
  `youngs_modulus` decimal(12,2) DEFAULT NULL,
  `shear_modulus` decimal(12,2) DEFAULT NULL,
  `bulk_modulus` decimal(12,2) DEFAULT NULL,
  `poissons_ratio` decimal(6,4) DEFAULT NULL,
  `yield_strength` decimal(12,2) DEFAULT NULL,
  `tensile_strength` decimal(12,2) DEFAULT NULL,
  `compressive_strength` decimal(12,2) DEFAULT NULL,
  `fatigue_strength` decimal(12,2) DEFAULT NULL,
  `hardness_hb` decimal(8,2) DEFAULT NULL,
  `hardness_hrc` decimal(6,2) DEFAULT NULL,
  `impact_energy` decimal(8,2) DEFAULT NULL,
  `elongation` decimal(6,2) DEFAULT NULL,
  `chemical_composition` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`chemical_composition`)),
  `impurities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`impurities`)),
  `machinability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`machinability`)),
  `weldability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`weldability`)),
  `formability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`formability`)),
  `heat_treatment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`heat_treatment`)),
  `standards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`standards`)),
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifications`)),
  `certifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`certifications`)),
  `typical_applications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`typical_applications`)),
  `industries` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`industries`)),
  `manufacturing_processes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`manufacturing_processes`)),
  `suppliers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`suppliers`)),
  `forms_available` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`forms_available`)),
  `cost_per_kg` decimal(10,4) DEFAULT NULL,
  `availability` varchar(191) DEFAULT NULL,
  `environmental_impact` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`environmental_impact`)),
  `safety_considerations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`safety_considerations`)),
  `recycling_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recycling_info`)),
  `hazardous` tinyint(1) NOT NULL DEFAULT 0,
  `datasheet_path` varchar(191) DEFAULT NULL,
  `reference_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`reference_documents`)),
  `test_reports` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`test_reports`)),
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`keywords`)),
  `created_by_user` varchar(191) DEFAULT NULL,
  `verified_by` varchar(191) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `status` enum('draft','pending','approved','deprecated') NOT NULL DEFAULT 'draft',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `usage_count` int(11) NOT NULL DEFAULT 0,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `materials_uuid_unique` (`uuid`),
  UNIQUE KEY `materials_code_unique` (`code`),
  KEY `materials_category_subcategory_index` (`category`,`subcategory`),
  KEY `materials_material_type_grade_index` (`material_type`,`grade`),
  KEY `materials_status_is_active_index` (`status`,`is_active`),
  KEY `materials_is_featured_category_index` (`is_featured`,`category`),
  KEY `materials_name_code_index` (`name`,`code`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: media
DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `mediable_type` varchar(191) NOT NULL,
  `mediable_id` bigint(20) unsigned NOT NULL,
  `file_name` varchar(191) NOT NULL,
  `file_path` varchar(191) NOT NULL,
  `disk` varchar(191) NOT NULL DEFAULT 'public',
  `mime_type` varchar(191) NOT NULL,
  `file_size` bigint(20) unsigned NOT NULL,
  `file_extension` varchar(10) NOT NULL,
  `file_category` enum('cad_drawing','cad_model','technical_doc','image','simulation','other') NOT NULL DEFAULT 'other',
  `cad_metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cad_metadata`)),
  `cad_software` varchar(191) DEFAULT NULL,
  `cad_version` varchar(191) DEFAULT NULL,
  `drawing_scale` decimal(8,4) DEFAULT NULL,
  `units` varchar(191) DEFAULT NULL,
  `dimensions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dimensions`)),
  `standard_compliance` varchar(191) DEFAULT NULL,
  `revision_number` varchar(191) DEFAULT NULL,
  `drawing_date` date DEFAULT NULL,
  `material_specification` varchar(191) DEFAULT NULL,
  `technical_notes` text DEFAULT NULL,
  `processing_status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `conversion_formats` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`conversion_formats`)),
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `virus_scanned` tinyint(1) NOT NULL DEFAULT 0,
  `scanned_at` timestamp NULL DEFAULT NULL,
  `contains_sensitive_data` tinyint(1) NOT NULL DEFAULT 0,
  `download_count` int(11) NOT NULL DEFAULT 0,
  `thumbnail_path` varchar(191) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `exif_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`exif_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_mediable_type_mediable_id_index` (`mediable_type`,`mediable_id`),
  KEY `media_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `media_file_category_is_public_index` (`file_category`,`is_public`),
  KEY `media_mime_type_index` (`mime_type`),
  KEY `media_cad_category` (`file_category`,`cad_software`),
  KEY `media_standards` (`standard_compliance`),
  KEY `media_processing` (`processing_status`,`created_at`),
  KEY `media_access` (`is_approved`,`is_public`),
  KEY `media_type_classification` (`file_extension`,`file_category`),
  KEY `media_popularity` (`download_count`),
  KEY `media_user_category` (`user_id`,`file_category`,`created_at`),
  KEY `media_attachment_type` (`mediable_type`,`file_category`),
  KEY `media_cad_compatibility` (`cad_software`,`cad_version`),
  KEY `media_security_status` (`virus_scanned`,`is_approved`),
  FULLTEXT KEY `media_content_search` (`file_name`,`technical_notes`),
  CONSTRAINT `media_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: messages
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_conversation_id_created_at_index` (`conversation_id`,`created_at`),
  KEY `messages_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: messaging
DROP TABLE IF EXISTS `messaging`;
CREATE TABLE `messaging` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: notification_ab_test_events
DROP TABLE IF EXISTS `notification_ab_test_events`;
CREATE TABLE `notification_ab_test_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `participant_id` bigint(20) unsigned NOT NULL,
  `notification_id` bigint(20) unsigned DEFAULT NULL,
  `event_type` varchar(191) NOT NULL,
  `event_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_data`)),
  `occurred_at` timestamp NOT NULL,
  `session_id` varchar(191) DEFAULT NULL,
  `user_agent` varchar(191) DEFAULT NULL,
  `ip_address` varchar(191) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_ab_test_events_notification_id_foreign` (`notification_id`),
  KEY `notification_ab_test_events_participant_id_event_type_index` (`participant_id`,`event_type`),
  KEY `notification_ab_test_events_event_type_occurred_at_index` (`event_type`,`occurred_at`),
  KEY `notification_ab_test_events_occurred_at_index` (`occurred_at`),
  CONSTRAINT `notification_ab_test_events_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE SET NULL,
  CONSTRAINT `notification_ab_test_events_participant_id_foreign` FOREIGN KEY (`participant_id`) REFERENCES `notification_ab_test_participants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: notification_ab_test_participants
DROP TABLE IF EXISTS `notification_ab_test_participants`;
CREATE TABLE `notification_ab_test_participants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ab_test_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `variant` varchar(191) NOT NULL,
  `assigned_at` timestamp NOT NULL,
  `first_notification_sent_at` timestamp NULL DEFAULT NULL,
  `last_notification_sent_at` timestamp NULL DEFAULT NULL,
  `total_notifications_sent` int(11) NOT NULL DEFAULT 0,
  `total_notifications_opened` int(11) NOT NULL DEFAULT 0,
  `total_notifications_clicked` int(11) NOT NULL DEFAULT 0,
  `total_notifications_dismissed` int(11) NOT NULL DEFAULT 0,
  `total_actions_taken` int(11) NOT NULL DEFAULT 0,
  `engagement_score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `conversion_achieved` tinyint(1) NOT NULL DEFAULT 0,
  `conversion_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `opted_out_at` timestamp NULL DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_ab_test_participants_ab_test_id_user_id_unique` (`ab_test_id`,`user_id`),
  KEY `notification_ab_test_participants_ab_test_id_variant_index` (`ab_test_id`,`variant`),
  KEY `notification_ab_test_participants_user_id_assigned_at_index` (`user_id`,`assigned_at`),
  CONSTRAINT `notification_ab_test_participants_ab_test_id_foreign` FOREIGN KEY (`ab_test_id`) REFERENCES `notification_ab_tests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notification_ab_test_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: notification_ab_tests
DROP TABLE IF EXISTS `notification_ab_tests`;
CREATE TABLE `notification_ab_tests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `test_type` enum('title','message','timing','priority','template','frequency') NOT NULL,
  `notification_type` varchar(191) NOT NULL,
  `status` enum('draft','active','paused','concluded','cancelled') NOT NULL DEFAULT 'draft',
  `variants` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`variants`)),
  `traffic_split` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`traffic_split`)),
  `target_metrics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`target_metrics`)),
  `segmentation_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`segmentation_rules`)),
  `start_date` timestamp NOT NULL,
  `end_date` timestamp NOT NULL,
  `min_sample_size` int(11) NOT NULL DEFAULT 100,
  `confidence_level` decimal(5,2) NOT NULL DEFAULT 95.00,
  `statistical_significance` decimal(5,4) NOT NULL DEFAULT 0.0500,
  `auto_conclude` tinyint(1) NOT NULL DEFAULT 1,
  `results` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`results`)),
  `winner_variant` varchar(191) DEFAULT NULL,
  `statistical_confidence` decimal(5,2) DEFAULT NULL,
  `effect_size` decimal(5,4) DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `concluded_at` timestamp NULL DEFAULT NULL,
  `conclusion_reason` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_ab_tests_created_by_foreign` (`created_by`),
  KEY `notification_ab_tests_notification_type_status_index` (`notification_type`,`status`),
  KEY `notification_ab_tests_status_start_date_end_date_index` (`status`,`start_date`,`end_date`),
  CONSTRAINT `notification_ab_tests_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: notifications
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
  KEY `notifications_user_type_created_idx` (`user_id`,`type`,`created_at`),
  KEY `notifications_user_read_created_idx` (`user_id`,`is_read`,`created_at`),
  KEY `notifications_type_read_created_idx` (`type`,`is_read`,`created_at`),
  KEY `notifications_user_priority_created_idx` (`user_id`,`priority`,`created_at`),
  KEY `notifications_type_priority_idx` (`type`,`priority`),
  KEY `notifications_created_priority_idx` (`created_at`,`priority`),
  KEY `notifications_user_type_idx` (`user_id`,`type`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=883 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: order_items
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `technical_product_id` bigint(20) unsigned NOT NULL,
  `seller_id` bigint(20) unsigned NOT NULL,
  `product_title` varchar(191) NOT NULL,
  `product_description` text DEFAULT NULL,
  `product_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`product_snapshot`)),
  `quantity` int(10) unsigned NOT NULL DEFAULT 1,
  `unit_price` decimal(12,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `seller_earnings` decimal(12,2) NOT NULL,
  `platform_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `license_type` enum('single','commercial','extended') NOT NULL DEFAULT 'single',
  `license_terms` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`license_terms`)),
  `license_expires_at` timestamp NULL DEFAULT NULL,
  `download_count` int(10) unsigned NOT NULL DEFAULT 0,
  `download_limit` int(10) unsigned DEFAULT NULL,
  `first_downloaded_at` timestamp NULL DEFAULT NULL,
  `last_downloaded_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','active','expired','revoked') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_technical_product_id_index` (`order_id`,`technical_product_id`),
  KEY `order_items_seller_id_created_at_index` (`seller_id`,`created_at`),
  KEY `order_items_technical_product_id_status_index` (`technical_product_id`,`status`),
  KEY `order_items_license_expires_at_index` (`license_expires_at`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_technical_product_id_foreign` FOREIGN KEY (`technical_product_id`) REFERENCES `technical_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: orders
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

-- Table: page_categories
DROP TABLE IF EXISTS `page_categories`;
CREATE TABLE `page_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `icon_url` varchar(191) DEFAULT NULL,
  `color_code` varchar(7) DEFAULT NULL,
  `category_type` enum('engineering_guides','company_info','technical_standards','software_tutorials','career_resources','industry_news') NOT NULL DEFAULT 'engineering_guides',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `show_in_menu` tinyint(1) NOT NULL DEFAULT 1,
  `page_count` int(11) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_categories_slug_unique` (`slug`),
  KEY `page_categories_is_active_show_in_menu_order_index` (`is_active`,`show_in_menu`,`order`),
  KEY `page_categories_category_type_is_active_index` (`category_type`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: page_seos
DROP TABLE IF EXISTS `page_seos`;
CREATE TABLE `page_seos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `route_name` varchar(191) DEFAULT NULL,
  `url_pattern` varchar(191) DEFAULT NULL,
  `title` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `og_title` varchar(191) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(191) DEFAULT NULL,
  `twitter_title` varchar(191) DEFAULT NULL,
  `twitter_description` text DEFAULT NULL,
  `twitter_image` varchar(191) DEFAULT NULL,
  `canonical_url` varchar(191) DEFAULT NULL,
  `no_index` tinyint(1) NOT NULL DEFAULT 0,
  `extra_meta` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `page_seos_route_name_index` (`route_name`),
  KEY `page_seos_url_pattern_index` (`url_pattern`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: pages
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `content` longtext NOT NULL,
  `excerpt` text DEFAULT NULL,
  `page_type` enum('about_us','engineering_guide','software_tutorial','standards_reference','career_guide','company_policy','technical_documentation','industry_insight') NOT NULL DEFAULT 'engineering_guide',
  `technical_specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`technical_specs`)),
  `prerequisites` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`prerequisites`)),
  `difficulty_level` enum('beginner','intermediate','advanced','expert') DEFAULT NULL,
  `estimated_read_time` int(11) DEFAULT NULL,
  `featured_image` varchar(191) DEFAULT NULL,
  `related_software` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`related_software`)),
  `engineering_standards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`engineering_standards`)),
  `category_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `rating_average` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `requires_login` tinyint(1) NOT NULL DEFAULT 0,
  `is_premium` tinyint(1) NOT NULL DEFAULT 0,
  `meta_title` varchar(191) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `author_id` bigint(20) unsigned DEFAULT NULL,
  `reviewer_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`),
  KEY `pages_user_id_foreign` (`user_id`),
  KEY `pages_reviewer_id_foreign` (`reviewer_id`),
  KEY `pages_page_type_status_published_at_index` (`page_type`,`status`,`published_at`),
  KEY `pages_category_id_is_featured_published_at_index` (`category_id`,`is_featured`,`published_at`),
  KEY `pages_difficulty_level_rating_average_index` (`difficulty_level`,`rating_average`),
  KEY `pages_author_id_created_at_index` (`author_id`,`created_at`),
  FULLTEXT KEY `pages_title_content_excerpt_fulltext` (`title`,`content`,`excerpt`),
  CONSTRAINT `pages_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
  CONSTRAINT `pages_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `page_categories` (`id`),
  CONSTRAINT `pages_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`),
  CONSTRAINT `pages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: password_reset_tokens
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: payment_audit_logs
DROP TABLE IF EXISTS `payment_audit_logs`;
CREATE TABLE `payment_audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_type` varchar(191) NOT NULL COMMENT 'Loại sự kiện: payment, payout, refund, etc.',
  `entity_type` varchar(191) NOT NULL COMMENT 'Loại entity: order, payment, payout, etc.',
  `entity_id` bigint(20) unsigned NOT NULL COMMENT 'ID của entity',
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(191) DEFAULT NULL,
  `user_agent` varchar(191) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Giá trị cũ' CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Giá trị mới' CHECK (json_valid(`new_values`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Thông tin bổ sung' CHECK (json_valid(`metadata`)),
  `amount_impact` decimal(12,2) DEFAULT NULL COMMENT 'Tác động tài chính',
  `currency` varchar(3) NOT NULL DEFAULT 'VND',
  `description` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_audit_logs_entity_type_entity_id_index` (`entity_type`,`entity_id`),
  KEY `payment_audit_logs_event_type_created_at_index` (`event_type`,`created_at`),
  KEY `payment_audit_logs_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `payment_audit_logs_admin_id_created_at_index` (`admin_id`,`created_at`),
  CONSTRAINT `payment_audit_logs_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: payment_disputes
DROP TABLE IF EXISTS `payment_disputes`;
CREATE TABLE `payment_disputes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` uuid NOT NULL,
  `dispute_reference` varchar(191) NOT NULL,
  `centralized_payment_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `customer_email` varchar(191) NOT NULL,
  `dispute_type` enum('chargeback','payment_not_received','unauthorized','duplicate','product_not_received','product_defective','service_issue','billing_error','other') NOT NULL,
  `status` enum('pending','investigating','evidence_required','escalated','resolved','lost','withdrawn','expired') NOT NULL DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `disputed_amount` decimal(15,2) NOT NULL,
  `refund_amount` decimal(15,2) DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'VND',
  `gateway_dispute_id` varchar(191) DEFAULT NULL,
  `gateway_reason_code` varchar(191) DEFAULT NULL,
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `customer_reason` text NOT NULL,
  `customer_description` text DEFAULT NULL,
  `customer_evidence` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`customer_evidence`)),
  `merchant_response` text DEFAULT NULL,
  `merchant_evidence` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`merchant_evidence`)),
  `merchant_response_deadline` timestamp NULL DEFAULT NULL,
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `internal_notes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`internal_notes`)),
  `resolution_summary` text DEFAULT NULL,
  `resolution_type` enum('full_refund','partial_refund','no_refund','replacement','store_credit','other') DEFAULT NULL,
  `dispute_date` timestamp NOT NULL,
  `gateway_deadline` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_disputes_uuid_unique` (`uuid`),
  UNIQUE KEY `payment_disputes_dispute_reference_unique` (`dispute_reference`),
  KEY `payment_disputes_centralized_payment_id_foreign` (`centralized_payment_id`),
  KEY `payment_disputes_order_id_foreign` (`order_id`),
  KEY `payment_disputes_status_priority_index` (`status`,`priority`),
  KEY `payment_disputes_dispute_type_status_index` (`dispute_type`,`status`),
  KEY `payment_disputes_customer_id_status_index` (`customer_id`,`status`),
  KEY `payment_disputes_assigned_to_status_index` (`assigned_to`,`status`),
  KEY `payment_disputes_dispute_date_status_index` (`dispute_date`,`status`),
  KEY `payment_disputes_gateway_dispute_id_index` (`gateway_dispute_id`),
  CONSTRAINT `payment_disputes_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_disputes_centralized_payment_id_foreign` FOREIGN KEY (`centralized_payment_id`) REFERENCES `centralized_payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_disputes_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_disputes_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `marketplace_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: payment_refunds
DROP TABLE IF EXISTS `payment_refunds`;
CREATE TABLE `payment_refunds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` uuid NOT NULL,
  `refund_reference` varchar(191) NOT NULL,
  `centralized_payment_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `dispute_id` bigint(20) unsigned DEFAULT NULL,
  `refund_type` enum('full','partial','shipping','tax','item','goodwill','chargeback','error') NOT NULL,
  `reason` enum('customer_request','product_defective','wrong_item','not_delivered','damaged_shipping','duplicate_payment','billing_error','fraud_prevention','dispute_resolution','goodwill','admin_error','other') NOT NULL,
  `status` enum('pending','approved','processing','completed','failed','cancelled','rejected') NOT NULL DEFAULT 'pending',
  `original_amount` decimal(15,2) NOT NULL,
  `refund_amount` decimal(15,2) NOT NULL,
  `gateway_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `net_refund` decimal(15,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'VND',
  `payment_method` varchar(191) NOT NULL,
  `gateway_refund_id` varchar(191) DEFAULT NULL,
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `gateway_error` text DEFAULT NULL,
  `customer_reason` text DEFAULT NULL,
  `admin_reason` text DEFAULT NULL,
  `refund_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`refund_items`)),
  `requested_by` bigint(20) unsigned DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `internal_notes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`internal_notes`)),
  `seller_adjustments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`seller_adjustments`)),
  `adjust_seller_earnings` tinyint(1) NOT NULL DEFAULT 1,
  `seller_deduction` decimal(15,2) NOT NULL DEFAULT 0.00,
  `requested_at` timestamp NOT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `customer_notified` tinyint(1) NOT NULL DEFAULT 0,
  `seller_notified` tinyint(1) NOT NULL DEFAULT 0,
  `customer_notified_at` timestamp NULL DEFAULT NULL,
  `seller_notified_at` timestamp NULL DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_refunds_uuid_unique` (`uuid`),
  UNIQUE KEY `payment_refunds_refund_reference_unique` (`refund_reference`),
  KEY `payment_refunds_centralized_payment_id_foreign` (`centralized_payment_id`),
  KEY `payment_refunds_order_id_foreign` (`order_id`),
  KEY `payment_refunds_dispute_id_foreign` (`dispute_id`),
  KEY `payment_refunds_requested_by_foreign` (`requested_by`),
  KEY `payment_refunds_approved_by_foreign` (`approved_by`),
  KEY `payment_refunds_processed_by_foreign` (`processed_by`),
  KEY `payment_refunds_status_refund_type_index` (`status`,`refund_type`),
  KEY `payment_refunds_customer_id_status_index` (`customer_id`,`status`),
  KEY `payment_refunds_payment_method_status_index` (`payment_method`,`status`),
  KEY `payment_refunds_requested_at_status_index` (`requested_at`,`status`),
  KEY `payment_refunds_gateway_refund_id_index` (`gateway_refund_id`),
  CONSTRAINT `payment_refunds_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_refunds_centralized_payment_id_foreign` FOREIGN KEY (`centralized_payment_id`) REFERENCES `centralized_payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_refunds_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_refunds_dispute_id_foreign` FOREIGN KEY (`dispute_id`) REFERENCES `payment_disputes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_refunds_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `marketplace_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_refunds_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_refunds_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: payment_system_settings
DROP TABLE IF EXISTS `payment_system_settings`;
CREATE TABLE `payment_system_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) NOT NULL COMMENT 'Khóa cấu hình',
  `value` text DEFAULT NULL COMMENT 'Giá trị cấu hình',
  `type` varchar(191) NOT NULL DEFAULT 'string' COMMENT 'Kiểu dữ liệu: string, number, boolean, json',
  `description` text DEFAULT NULL COMMENT 'Mô tả cấu hình',
  `group` varchar(191) NOT NULL DEFAULT 'general' COMMENT 'Nhóm cấu hình',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_system` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Cấu hình hệ thống không được xóa',
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_system_settings_key_unique` (`key`),
  KEY `payment_system_settings_updated_by_foreign` (`updated_by`),
  KEY `payment_system_settings_group_sort_order_index` (`group`,`sort_order`),
  KEY `payment_system_settings_is_active_group_index` (`is_active`,`group`),
  CONSTRAINT `payment_system_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: payment_transactions
DROP TABLE IF EXISTS `payment_transactions`;
CREATE TABLE `payment_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(191) NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `payment_method` enum('stripe','vnpay','bank_transfer') NOT NULL,
  `gateway_transaction_id` varchar(191) DEFAULT NULL,
  `payment_intent_id` varchar(191) DEFAULT NULL,
  `charge_id` varchar(191) DEFAULT NULL,
  `type` enum('payment','refund','chargeback','fee') NOT NULL,
  `status` enum('pending','processing','completed','failed','cancelled') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'VND',
  `fee_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(12,2) NOT NULL,
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `failure_reason` text DEFAULT NULL,
  `receipt_url` varchar(191) DEFAULT NULL,
  `refund_transaction_id` bigint(20) unsigned DEFAULT NULL,
  `refunded_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_transactions_transaction_id_unique` (`transaction_id`),
  KEY `payment_transactions_refund_transaction_id_foreign` (`refund_transaction_id`),
  KEY `payment_transactions_order_id_type_index` (`order_id`,`type`),
  KEY `payment_transactions_user_id_status_index` (`user_id`,`status`),
  KEY `payment_transactions_payment_method_status_index` (`payment_method`,`status`),
  KEY `payment_transactions_gateway_transaction_id_index` (`gateway_transaction_id`),
  KEY `payment_transactions_payment_intent_id_index` (`payment_intent_id`),
  KEY `payment_transactions_processed_at_index` (`processed_at`),
  CONSTRAINT `payment_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_transactions_refund_transaction_id_foreign` FOREIGN KEY (`refund_transaction_id`) REFERENCES `payment_transactions` (`id`),
  CONSTRAINT `payment_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: permissions
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

-- Table: personal_access_tokens
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: poll_options
DROP TABLE IF EXISTS `poll_options`;
CREATE TABLE `poll_options` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` bigint(20) unsigned NOT NULL,
  `text` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `poll_options_poll_id_index` (`poll_id`),
  CONSTRAINT `poll_options_poll_id_foreign` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: poll_votes
DROP TABLE IF EXISTS `poll_votes`;
CREATE TABLE `poll_votes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` bigint(20) unsigned NOT NULL,
  `poll_option_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `poll_votes_poll_id_poll_option_id_user_id_unique` (`poll_id`,`poll_option_id`,`user_id`),
  KEY `poll_votes_user_id_foreign` (`user_id`),
  KEY `poll_votes_poll_id_user_id_index` (`poll_id`,`user_id`),
  KEY `poll_votes_poll_option_id_index` (`poll_option_id`),
  CONSTRAINT `poll_votes_poll_id_foreign` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `poll_votes_poll_option_id_foreign` FOREIGN KEY (`poll_option_id`) REFERENCES `poll_options` (`id`) ON DELETE CASCADE,
  CONSTRAINT `poll_votes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: polls
DROP TABLE IF EXISTS `polls`;
CREATE TABLE `polls` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` bigint(20) unsigned NOT NULL,
  `question` varchar(191) NOT NULL,
  `max_options` int(11) NOT NULL DEFAULT 1,
  `allow_change_vote` tinyint(1) NOT NULL DEFAULT 1,
  `show_votes_publicly` tinyint(1) NOT NULL DEFAULT 0,
  `allow_view_without_vote` tinyint(1) NOT NULL DEFAULT 1,
  `close_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `polls_thread_id_index` (`thread_id`),
  KEY `polls_close_at_index` (`close_at`),
  CONSTRAINT `polls_thread_id_foreign` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: posts
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `thread_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_thread_id_created_at_index` (`thread_id`,`created_at`),
  KEY `posts_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `posts_thread_id_foreign` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: product_categories
DROP TABLE IF EXISTS `product_categories`;
CREATE TABLE `product_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `commission_rate` decimal(5,2) NOT NULL DEFAULT 10.00,
  `engineering_discipline` varchar(50) DEFAULT NULL,
  `required_software` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`required_software`)),
  `product_count` int(10) unsigned NOT NULL DEFAULT 0,
  `total_sales` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_categories_slug_unique` (`slug`),
  KEY `product_categories_parent_id_is_active_index` (`parent_id`,`is_active`),
  KEY `product_categories_engineering_discipline_index` (`engineering_discipline`),
  KEY `product_categories_is_active_sort_order_index` (`is_active`,`sort_order`),
  CONSTRAINT `product_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: product_price_history
DROP TABLE IF EXISTS `product_price_history`;
CREATE TABLE `product_price_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `old_price` decimal(12,2) NOT NULL,
  `new_price` decimal(12,2) NOT NULL,
  `old_sale_price` decimal(12,2) DEFAULT NULL,
  `new_sale_price` decimal(12,2) DEFAULT NULL,
  `price_change` decimal(12,2) NOT NULL,
  `price_change_percentage` decimal(5,2) NOT NULL,
  `change_type` enum('increase','decrease','no_change') NOT NULL DEFAULT 'no_change',
  `reason` varchar(191) DEFAULT NULL,
  `changed_by` bigint(20) unsigned DEFAULT NULL,
  `effective_date` timestamp NOT NULL DEFAULT '2025-07-10 11:52:48',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_price_history_changed_by_foreign` (`changed_by`),
  KEY `product_price_history_product_id_created_at_index` (`product_id`,`created_at`),
  KEY `product_price_history_change_type_created_at_index` (`change_type`,`created_at`),
  KEY `product_price_history_effective_date_index` (`effective_date`),
  CONSTRAINT `product_price_history_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_price_history_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `marketplace_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: product_purchases
DROP TABLE IF EXISTS `product_purchases`;
CREATE TABLE `product_purchases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `buyer_id` bigint(20) unsigned NOT NULL,
  `seller_id` bigint(20) unsigned NOT NULL,
  `purchase_token` varchar(64) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `platform_fee` decimal(10,2) NOT NULL,
  `seller_revenue` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_id` varchar(191) DEFAULT NULL,
  `payment_status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_gateway` varchar(50) DEFAULT NULL,
  `license_type` enum('single_use','commercial','educational','unlimited') NOT NULL DEFAULT 'single_use',
  `license_key` varchar(128) NOT NULL,
  `download_limit` int(11) NOT NULL DEFAULT 5,
  `download_count` int(11) NOT NULL DEFAULT 0,
  `expires_at` timestamp NULL DEFAULT NULL,
  `download_token` varchar(128) NOT NULL,
  `last_download_at` timestamp NULL DEFAULT NULL,
  `download_ip_addresses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`download_ip_addresses`)),
  `status` enum('active','expired','revoked','refunded') NOT NULL DEFAULT 'active',
  `refund_reason` text DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_active_purchase` (`product_id`,`buyer_id`,`status`),
  UNIQUE KEY `product_purchases_purchase_token_unique` (`purchase_token`),
  UNIQUE KEY `product_purchases_license_key_unique` (`license_key`),
  UNIQUE KEY `product_purchases_download_token_unique` (`download_token`),
  KEY `product_purchases_seller_id_foreign` (`seller_id`),
  KEY `product_purchases_buyer_id_status_index` (`buyer_id`,`status`),
  KEY `product_purchases_product_id_payment_status_index` (`product_id`,`payment_status`),
  KEY `product_purchases_purchase_token_index` (`purchase_token`),
  KEY `product_purchases_download_token_index` (`download_token`),
  CONSTRAINT `product_purchases_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_purchases_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `technical_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_purchases_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: product_reviews
DROP TABLE IF EXISTS `product_reviews`;
CREATE TABLE `product_reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `rating` int(11) NOT NULL COMMENT '1-5 stars',
  `title` varchar(191) DEFAULT NULL,
  `content` text NOT NULL,
  `is_verified_purchase` tinyint(1) NOT NULL DEFAULT 0,
  `helpful_count` int(11) NOT NULL DEFAULT 0,
  `not_helpful_count` int(11) NOT NULL DEFAULT 0,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_user_review_unique` (`product_id`,`user_id`),
  KEY `product_reviews_product_id_rating_index` (`product_id`,`rating`),
  KEY `product_reviews_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `product_reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `marketplace_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: product_watchers
DROP TABLE IF EXISTS `product_watchers`;
CREATE TABLE `product_watchers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `target_price` decimal(12,2) DEFAULT NULL,
  `notify_any_drop` tinyint(1) NOT NULL DEFAULT 1,
  `notify_stock_changes` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_notified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_watchers_user_id_product_id_unique` (`user_id`,`product_id`),
  KEY `product_watchers_product_id_is_active_index` (`product_id`,`is_active`),
  KEY `product_watchers_user_id_is_active_index` (`user_id`,`is_active`),
  KEY `product_watchers_target_price_index` (`target_price`),
  CONSTRAINT `product_watchers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `marketplace_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_watchers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: products
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `short_description` text DEFAULT NULL,
  `sku` varchar(191) DEFAULT NULL,
  `seller_id` bigint(20) unsigned NOT NULL,
  `product_category_id` bigint(20) unsigned NOT NULL,
  `product_type` enum('physical','digital','service','technical_file') NOT NULL,
  `seller_type` enum('supplier','manufacturer','brand') NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `products_seller_id_foreign` (`seller_id`),
  KEY `products_product_category_id_foreign` (`product_category_id`),
  CONSTRAINT `products_product_category_id_foreign` FOREIGN KEY (`product_category_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: profile_posts
DROP TABLE IF EXISTS `profile_posts`;
CREATE TABLE `profile_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `profile_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_posts_user_id_index` (`user_id`),
  KEY `profile_posts_profile_id_index` (`profile_id`),
  CONSTRAINT `profile_posts_profile_id_foreign` FOREIGN KEY (`profile_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `profile_posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: protected_files
DROP TABLE IF EXISTS `protected_files`;
CREATE TABLE `protected_files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `original_filename` varchar(191) NOT NULL,
  `encrypted_filename` varchar(191) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_hash` varchar(128) NOT NULL,
  `file_type` enum('cad_file','documentation','calculation','tutorial','sample') NOT NULL DEFAULT 'cad_file',
  `software_required` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `encryption_key` varchar(128) NOT NULL,
  `encryption_method` varchar(50) NOT NULL DEFAULT 'AES-256-CBC',
  `access_level` enum('preview','sample','full_access') NOT NULL DEFAULT 'full_access',
  `download_count` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `protected_files_product_id_file_type_index` (`product_id`,`file_type`),
  KEY `protected_files_encrypted_filename_index` (`encrypted_filename`),
  KEY `protected_files_access_level_is_active_index` (`access_level`,`is_active`),
  CONSTRAINT `protected_files_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `technical_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: reactions
DROP TABLE IF EXISTS `reactions`;
CREATE TABLE `reactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `reactable_id` bigint(20) unsigned NOT NULL,
  `reactable_type` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reactions_user_id_reactable_id_reactable_type_unique` (`user_id`,`reactable_id`,`reactable_type`),
  KEY `reactions_reactable_type_reactable_id_index` (`reactable_type`,`reactable_id`),
  KEY `reactions_user_id_type_index` (`user_id`,`type`),
  KEY `reactions_reactable_type_reactable_id_type_index` (`reactable_type`,`reactable_id`,`type`),
  CONSTRAINT `reactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: regions
DROP TABLE IF EXISTS `regions`;
CREATE TABLE `regions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `name_local` varchar(191) DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL,
  `type` enum('province','state','prefecture','region','city','zone') NOT NULL DEFAULT 'province',
  `timezone` varchar(191) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `industrial_zones` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`industrial_zones`)),
  `universities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`universities`)),
  `major_companies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`major_companies`)),
  `specialization_areas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specialization_areas`)),
  `forum_moderator_timezone` varchar(191) DEFAULT NULL,
  `local_standards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`local_standards`)),
  `common_materials` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`common_materials`)),
  `icon` varchar(191) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `forum_count` int(11) NOT NULL DEFAULT 0,
  `user_count` int(11) NOT NULL DEFAULT 0,
  `thread_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `regions_country_id_code_unique` (`country_id`,`code`),
  KEY `regions_country_id_is_active_sort_order_index` (`country_id`,`is_active`,`sort_order`),
  KEY `regions_type_is_active_index` (`type`,`is_active`),
  KEY `regions_is_featured_is_active_index` (`is_featured`,`is_active`),
  KEY `regions_latitude_longitude_index` (`latitude`,`longitude`),
  CONSTRAINT `regions_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: reports
DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `reportable_type` varchar(191) NOT NULL,
  `reportable_id` bigint(20) unsigned NOT NULL,
  `reason` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','resolved','dismissed') NOT NULL DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `resolved_by` bigint(20) unsigned DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolution_note` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reports_user_id_foreign` (`user_id`),
  KEY `reports_reportable_type_reportable_id_index` (`reportable_type`,`reportable_id`),
  KEY `reports_status_created_at_index` (`created_at`),
  KEY `reports_priority_status_index` (`priority`),
  KEY `reports_resolved_by_resolved_at_index` (`resolved_by`,`resolved_at`),
  KEY `reports_status_priority_created_at_index` (`status`,`priority`,`created_at`),
  CONSTRAINT `reports_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: role_has_permissions
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL COMMENT 'ID của role',
  `permission_id` bigint(20) unsigned NOT NULL COMMENT 'ID của permission',
  `is_granted` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Cấp phép (true) hay từ chối (false)',
  `conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Điều kiện áp dụng permission' CHECK (json_valid(`conditions`)),
  `restrictions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Giới hạn khi sử dụng permission' CHECK (json_valid(`restrictions`)),
  `granted_by` bigint(20) unsigned DEFAULT NULL COMMENT 'Người cấp phép',
  `granted_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian cấp phép',
  `grant_reason` text DEFAULT NULL COMMENT 'Lý do cấp phép',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permission_unique` (`role_id`,`permission_id`),
  KEY `role_has_permissions_role_id_index` (`role_id`),
  KEY `role_has_permissions_permission_id_index` (`permission_id`),
  KEY `role_has_permissions_role_id_is_granted_index` (`role_id`,`is_granted`),
  KEY `role_has_permissions_granted_by_foreign` (`granted_by`),
  CONSTRAINT `role_has_permissions_granted_by_foreign` FOREIGN KEY (`granted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=239 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: roles
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

-- Table: search_logs
DROP TABLE IF EXISTS `search_logs`;
CREATE TABLE `search_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `query` varchar(191) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(191) DEFAULT NULL,
  `user_agent` varchar(191) DEFAULT NULL,
  `results_count` int(11) NOT NULL DEFAULT 0,
  `response_time_ms` int(11) NOT NULL DEFAULT 0,
  `filters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`filters`)),
  `content_type` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `search_logs_query_created_at_index` (`query`,`created_at`),
  KEY `search_logs_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `search_logs_created_at_index` (`created_at`),
  KEY `search_logs_results_count_index` (`results_count`),
  CONSTRAINT `search_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: secure_downloads
DROP TABLE IF EXISTS `secure_downloads`;
CREATE TABLE `secure_downloads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint(20) unsigned NOT NULL,
  `protected_file_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `download_token` varchar(128) NOT NULL,
  `download_url` varchar(500) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `downloaded_at` timestamp NULL DEFAULT NULL,
  `download_ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `download_size` bigint(20) DEFAULT NULL,
  `download_duration_seconds` int(11) DEFAULT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `failure_reason` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `secure_downloads_download_token_unique` (`download_token`),
  KEY `secure_downloads_protected_file_id_foreign` (`protected_file_id`),
  KEY `secure_downloads_download_token_expires_at_index` (`download_token`,`expires_at`),
  KEY `secure_downloads_user_id_downloaded_at_index` (`user_id`,`downloaded_at`),
  KEY `secure_downloads_purchase_id_protected_file_id_index` (`purchase_id`,`protected_file_id`),
  KEY `secure_downloads_expires_at_is_completed_index` (`expires_at`,`is_completed`),
  CONSTRAINT `secure_downloads_protected_file_id_foreign` FOREIGN KEY (`protected_file_id`) REFERENCES `protected_files` (`id`) ON DELETE CASCADE,
  CONSTRAINT `secure_downloads_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `product_purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `secure_downloads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: seller_earnings
DROP TABLE IF EXISTS `seller_earnings`;
CREATE TABLE `seller_earnings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `seller_id` bigint(20) unsigned NOT NULL,
  `order_item_id` bigint(20) unsigned NOT NULL,
  `technical_product_id` bigint(20) unsigned NOT NULL,
  `gross_amount` decimal(12,2) NOT NULL,
  `platform_fee` decimal(12,2) NOT NULL,
  `payment_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(12,2) NOT NULL,
  `platform_fee_rate` decimal(5,4) NOT NULL,
  `payment_fee_rate` decimal(5,4) NOT NULL DEFAULT 0.0000,
  `payout_status` enum('pending','available','paid','failed') NOT NULL DEFAULT 'pending',
  `payout_id` bigint(20) unsigned DEFAULT NULL,
  `available_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seller_earnings_seller_id_foreign` (`seller_id`),
  KEY `seller_earnings_order_item_id_foreign` (`order_item_id`),
  KEY `seller_earnings_technical_product_id_foreign` (`technical_product_id`),
  CONSTRAINT `seller_earnings_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seller_earnings_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seller_earnings_technical_product_id_foreign` FOREIGN KEY (`technical_product_id`) REFERENCES `technical_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: seller_payout_items
DROP TABLE IF EXISTS `seller_payout_items`;
CREATE TABLE `seller_payout_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payout_request_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `order_item_id` bigint(20) unsigned NOT NULL,
  `centralized_payment_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `seller_id` bigint(20) unsigned NOT NULL,
  `item_price` decimal(12,2) NOT NULL COMMENT 'Giá sản phẩm',
  `quantity` int(11) NOT NULL COMMENT 'Số lượng',
  `item_total` decimal(12,2) NOT NULL COMMENT 'Tổng tiền item',
  `commission_rate` decimal(5,2) NOT NULL COMMENT 'Tỷ lệ hoa hồng (%)',
  `commission_amount` decimal(12,2) NOT NULL COMMENT 'Số tiền hoa hồng',
  `seller_earnings` decimal(12,2) NOT NULL COMMENT 'Tiền seller nhận được',
  `status` enum('pending','included','paid','disputed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seller_payout_items_order_item_id_foreign` (`order_item_id`),
  KEY `seller_payout_items_centralized_payment_id_foreign` (`centralized_payment_id`),
  KEY `seller_payout_items_product_id_foreign` (`product_id`),
  KEY `seller_payout_items_payout_request_id_seller_id_index` (`payout_request_id`,`seller_id`),
  KEY `seller_payout_items_order_id_seller_id_index` (`order_id`,`seller_id`),
  KEY `seller_payout_items_seller_id_status_index` (`seller_id`,`status`),
  CONSTRAINT `seller_payout_items_centralized_payment_id_foreign` FOREIGN KEY (`centralized_payment_id`) REFERENCES `centralized_payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seller_payout_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `marketplace_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seller_payout_items_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `marketplace_order_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seller_payout_items_payout_request_id_foreign` FOREIGN KEY (`payout_request_id`) REFERENCES `seller_payout_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seller_payout_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seller_payout_items_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: seller_payout_requests
DROP TABLE IF EXISTS `seller_payout_requests`;
CREATE TABLE `seller_payout_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payout_reference` varchar(191) NOT NULL COMMENT 'Mã tham chiếu payout',
  `seller_id` bigint(20) unsigned NOT NULL,
  `seller_account_id` bigint(20) unsigned NOT NULL,
  `total_sales` decimal(12,2) NOT NULL COMMENT 'Tổng doanh thu',
  `commission_amount` decimal(12,2) NOT NULL COMMENT 'Tổng hoa hồng',
  `net_payout` decimal(12,2) NOT NULL COMMENT 'Số tiền thực trả cho seller',
  `order_count` int(11) NOT NULL COMMENT 'Số đơn hàng',
  `period_from` date NOT NULL COMMENT 'Từ ngày',
  `period_to` date NOT NULL COMMENT 'Đến ngày',
  `bank_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Thông tin ngân hàng nhận tiền' CHECK (json_valid(`bank_details`)),
  `status` enum('pending','approved','processing','completed','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seller_payout_requests_payout_reference_unique` (`payout_reference`),
  KEY `seller_payout_requests_seller_account_id_foreign` (`seller_account_id`),
  KEY `seller_payout_requests_seller_id_status_index` (`seller_id`,`status`),
  KEY `seller_payout_requests_status_period_from_period_to_index` (`status`,`period_from`,`period_to`),
  KEY `seller_payout_requests_processed_by_status_index` (`processed_by`,`status`),
  CONSTRAINT `seller_payout_requests_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `seller_payout_requests_seller_account_id_foreign` FOREIGN KEY (`seller_account_id`) REFERENCES `marketplace_sellers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seller_payout_requests_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: seller_payouts
DROP TABLE IF EXISTS `seller_payouts`;
CREATE TABLE `seller_payouts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payout_id` varchar(191) NOT NULL,
  `seller_id` bigint(20) unsigned NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'VND',
  `earnings_count` int(10) unsigned NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `payout_method` enum('bank_transfer','stripe_transfer','paypal') NOT NULL,
  `payout_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payout_details`)),
  `status` enum('pending','processing','completed','failed','cancelled') NOT NULL,
  `failure_reason` text DEFAULT NULL,
  `transaction_reference` varchar(191) DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seller_payouts_payout_id_unique` (`payout_id`),
  KEY `seller_payouts_seller_id_status_index` (`seller_id`,`status`),
  KEY `seller_payouts_seller_id_period_start_period_end_index` (`seller_id`,`period_start`,`period_end`),
  KEY `seller_payouts_status_created_at_index` (`status`,`created_at`),
  KEY `seller_payouts_payout_id_index` (`payout_id`),
  CONSTRAINT `seller_payouts_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: seo_settings
DROP TABLE IF EXISTS `seo_settings`;
CREATE TABLE `seo_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) NOT NULL,
  `value` text DEFAULT NULL,
  `group` varchar(191) NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seo_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: settings
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) NOT NULL,
  `value` text DEFAULT NULL,
  `group` varchar(191) NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: shopping_carts
DROP TABLE IF EXISTS `shopping_carts`;
CREATE TABLE `shopping_carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `technical_product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT 1,
  `unit_price` decimal(12,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `license_type` enum('standard','extended','commercial') NOT NULL DEFAULT 'standard',
  `product_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`product_snapshot`)),
  `status` enum('active','saved_for_later','expired') NOT NULL DEFAULT 'active',
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shopping_carts_user_id_technical_product_id_license_type_unique` (`user_id`,`technical_product_id`,`license_type`),
  KEY `shopping_carts_technical_product_id_foreign` (`technical_product_id`),
  KEY `shopping_carts_user_id_status_index` (`user_id`,`status`),
  KEY `shopping_carts_user_id_technical_product_id_index` (`user_id`,`technical_product_id`),
  KEY `shopping_carts_expires_at_index` (`expires_at`),
  CONSTRAINT `shopping_carts_technical_product_id_foreign` FOREIGN KEY (`technical_product_id`) REFERENCES `technical_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shopping_carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: showcase_comments
DROP TABLE IF EXISTS `showcase_comments`;
CREATE TABLE `showcase_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `showcase_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `comment` text NOT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `like_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `showcase_comments_parent_id_foreign` (`parent_id`),
  KEY `showcase_comments_showcase_id_parent_id_index` (`showcase_id`,`parent_id`),
  KEY `showcase_comments_user_id_index` (`user_id`),
  KEY `showcase_comments_showcase_id_created_at_index` (`showcase_id`,`created_at`),
  KEY `showcase_comments_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `showcase_comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `showcase_comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `showcase_comments_showcase_id_foreign` FOREIGN KEY (`showcase_id`) REFERENCES `showcases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `showcase_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: showcase_follows
DROP TABLE IF EXISTS `showcase_follows`;
CREATE TABLE `showcase_follows` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `follower_id` bigint(20) unsigned NOT NULL,
  `following_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `showcase_follows_follower_id_following_id_unique` (`follower_id`,`following_id`),
  KEY `showcase_follows_follower_id_index` (`follower_id`),
  KEY `showcase_follows_following_id_index` (`following_id`),
  KEY `showcase_follows_follower_id_following_id_index` (`follower_id`,`following_id`),
  KEY `showcase_follows_following_id_created_at_index` (`following_id`,`created_at`),
  CONSTRAINT `showcase_follows_follower_id_foreign` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `showcase_follows_following_id_foreign` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: showcase_likes
DROP TABLE IF EXISTS `showcase_likes`;
CREATE TABLE `showcase_likes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `showcase_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `showcase_likes_showcase_id_user_id_unique` (`showcase_id`,`user_id`),
  KEY `showcase_likes_user_id_index` (`user_id`),
  CONSTRAINT `showcase_likes_showcase_id_foreign` FOREIGN KEY (`showcase_id`) REFERENCES `showcases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `showcase_likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: showcase_ratings
DROP TABLE IF EXISTS `showcase_ratings`;
CREATE TABLE `showcase_ratings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `showcase_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `technical_quality` tinyint(3) unsigned NOT NULL COMMENT 'Chất lượng kỹ thuật (1-5)',
  `innovation` tinyint(3) unsigned NOT NULL COMMENT 'Tính sáng tạo (1-5)',
  `usefulness` tinyint(3) unsigned NOT NULL COMMENT 'Tính hữu ích (1-5)',
  `documentation` tinyint(3) unsigned NOT NULL COMMENT 'Chất lượng tài liệu (1-5)',
  `overall_rating` decimal(3,2) NOT NULL COMMENT 'Đánh giá tổng thể (1.00-5.00)',
  `review` text DEFAULT NULL COMMENT 'Nhận xét chi tiết',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_showcase_rating` (`showcase_id`,`user_id`),
  KEY `showcase_ratings_showcase_id_overall_rating_index` (`showcase_id`,`overall_rating`),
  KEY `showcase_ratings_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `showcase_ratings_overall_rating_created_at_index` (`overall_rating`,`created_at`),
  CONSTRAINT `showcase_ratings_showcase_id_foreign` FOREIGN KEY (`showcase_id`) REFERENCES `showcases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `showcase_ratings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: showcases
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: social_accounts
DROP TABLE IF EXISTS `social_accounts`;
CREATE TABLE `social_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `provider` varchar(191) NOT NULL,
  `provider_id` varchar(191) NOT NULL,
  `provider_avatar` varchar(191) DEFAULT NULL,
  `provider_token` text DEFAULT NULL,
  `provider_refresh_token` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `social_accounts_provider_provider_id_unique` (`provider`,`provider_id`),
  KEY `social_accounts_user_id_foreign` (`user_id`),
  CONSTRAINT `social_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: social_interactions
DROP TABLE IF EXISTS `social_interactions`;
CREATE TABLE `social_interactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `interaction_type` varchar(191) NOT NULL COMMENT 'Loại tương tác: like, share, follow, bookmark, rate, endorse, mention',
  `interactable_type` varchar(191) NOT NULL,
  `interactable_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'Người thực hiện tương tác',
  `target_user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Người nhận tương tác (cho follow, mention, endorse)',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Metadata bổ sung: {"rating": 4.5, "platform": "linkedin", "expertise_area": "FEA"}' CHECK (json_valid(`metadata`)),
  `context` enum('thread','comment','showcase','user','general') NOT NULL DEFAULT 'general' COMMENT 'Ngữ cảnh tương tác: trong thread, comment, showcase, profile user',
  `rating_value` decimal(3,2) DEFAULT NULL COMMENT 'Giá trị đánh giá kỹ thuật (1.00-5.00 cho technical accuracy)',
  `interaction_note` text DEFAULT NULL COMMENT 'Ghi chú cho tương tác phức tạp (lý do endorse, feedback chi tiết)',
  `endorsement_type` enum('technical_skill','problem_solving','innovation','mentoring','leadership') DEFAULT NULL COMMENT 'Loại endorsement chuyên môn cho mechanical engineers',
  `expertise_areas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Lĩnh vực chuyên môn được endorse: ["CAD", "FEA", "Manufacturing", "Materials"]' CHECK (json_valid(`expertise_areas`)),
  `status` enum('active','hidden','deleted') NOT NULL DEFAULT 'active' COMMENT 'Trạng thái tương tác',
  `interaction_date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian thực hiện tương tác',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP address cho audit trail',
  `user_agent` varchar(500) DEFAULT NULL COMMENT 'User agent cho analytics',
  `referrer_url` varchar(500) DEFAULT NULL COMMENT 'URL nguồn của tương tác',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `social_interactions_unique_interaction` (`user_id`,`interactable_type`,`interactable_id`,`interaction_type`),
  KEY `social_interactions_interactable_type_interactable_id_index` (`interactable_type`,`interactable_id`),
  KEY `social_interactions_type_context_date` (`interaction_type`,`context`,`interaction_date`),
  KEY `social_interactions_user_activity` (`user_id`,`interaction_type`,`created_at`),
  KEY `social_interactions_morph_type` (`interactable_type`,`interactable_id`,`interaction_type`),
  KEY `social_interactions_target_tracking` (`target_user_id`,`interaction_type`),
  KEY `social_interactions_professional_rating` (`endorsement_type`,`rating_value`),
  KEY `social_interactions_endorsement_history` (`target_user_id`,`endorsement_type`,`interaction_date`),
  KEY `social_interactions_status_timeline` (`status`,`interaction_date`),
  KEY `social_interactions_active_timeline` (`interaction_type`,`status`,`created_at`),
  KEY `social_interactions_analytics` (`context`,`interaction_date`,`rating_value`),
  KEY `social_interactions_relationship` (`user_id`,`target_user_id`,`interaction_type`),
  CONSTRAINT `social_interactions_target_user_id_foreign` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `social_interactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: subscriptions
DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE `subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `plan_id` varchar(191) NOT NULL,
  `status` varchar(191) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `payment_method` varchar(191) DEFAULT NULL,
  `payment_id` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_user_id_foreign` (`user_id`),
  CONSTRAINT `subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: system
DROP TABLE IF EXISTS `system`;
CREATE TABLE `system` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: tags
DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `color_code` varchar(7) NOT NULL DEFAULT '#6366f1' COMMENT 'Hex color code cho tag display (#FF5722 cho manufacturing)',
  `tag_type` enum('general','software','material','process','industry') NOT NULL DEFAULT 'general' COMMENT 'Loại tag: chung, phần mềm, vật liệu, quy trình, ngành công nghiệp',
  `expertise_level` enum('beginner','intermediate','advanced','expert') NOT NULL DEFAULT 'beginner' COMMENT 'Cấp độ chuyên môn yêu cầu cho tag này',
  `usage_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Số lần tag được sử dụng (cached count)',
  `last_used_at` timestamp NULL DEFAULT NULL COMMENT 'Lần cuối tag được sử dụng',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Tag được highlight trong suggestions không',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Tag có đang được sử dụng không',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Thứ tự sắp xếp khi hiển thị tag',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_slug_unique` (`slug`),
  KEY `tags_name_search` (`name`),
  KEY `tags_slug_lookup` (`slug`),
  KEY `tags_technical_classification` (`tag_type`,`expertise_level`),
  KEY `tags_type_active` (`tag_type`,`is_active`),
  KEY `tags_popularity_ranking` (`usage_count`,`last_used_at`),
  KEY `tags_usage_stats` (`usage_count`),
  KEY `tags_active_featured` (`is_active`,`is_featured`),
  KEY `tags_featured_ordering` (`is_featured`,`sort_order`),
  KEY `tags_display_order` (`sort_order`,`name`),
  KEY `tags_advanced_filter` (`tag_type`,`expertise_level`,`is_active`),
  KEY `tags_expert_popularity` (`expertise_level`,`usage_count`,`last_used_at`),
  FULLTEXT KEY `tags_content_search` (`name`,`description`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: technical_drawings
DROP TABLE IF EXISTS `technical_drawings`;
CREATE TABLE `technical_drawings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` uuid NOT NULL,
  `title` varchar(191) NOT NULL,
  `drawing_number` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `revision` varchar(10) NOT NULL DEFAULT 'A',
  `created_by` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `file_path` varchar(191) NOT NULL,
  `file_name` varchar(191) NOT NULL,
  `file_type` varchar(191) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `mime_type` varchar(191) NOT NULL,
  `drawing_type` varchar(191) DEFAULT NULL,
  `scale` varchar(191) DEFAULT NULL,
  `units` varchar(191) NOT NULL DEFAULT 'mm',
  `dimensions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dimensions`)),
  `sheet_size` decimal(8,2) DEFAULT NULL,
  `project_name` varchar(191) DEFAULT NULL,
  `part_number` varchar(191) DEFAULT NULL,
  `material_specification` varchar(191) DEFAULT NULL,
  `tolerances` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tolerances`)),
  `surface_finish` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`surface_finish`)),
  `drawing_standards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`drawing_standards`)),
  `material_standards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`material_standards`)),
  `manufacturing_notes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`manufacturing_notes`)),
  `version_number` int(11) NOT NULL DEFAULT 1,
  `parent_drawing_id` bigint(20) unsigned DEFAULT NULL,
  `revision_notes` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `visibility` enum('public','private','company_only') NOT NULL DEFAULT 'private',
  `license_type` enum('free','commercial','educational') NOT NULL DEFAULT 'free',
  `price` decimal(10,2) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`keywords`)),
  `industry_category` varchar(191) DEFAULT NULL,
  `application_area` varchar(191) DEFAULT NULL,
  `download_count` int(11) NOT NULL DEFAULT 0,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `like_count` int(11) NOT NULL DEFAULT 0,
  `rating_average` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `status` enum('draft','pending','approved','rejected','archived') NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `technical_drawings_uuid_unique` (`uuid`),
  UNIQUE KEY `technical_drawings_drawing_number_unique` (`drawing_number`),
  KEY `technical_drawings_parent_drawing_id_foreign` (`parent_drawing_id`),
  KEY `technical_drawings_approved_by_foreign` (`approved_by`),
  KEY `technical_drawings_created_by_status_index` (`created_by`,`status`),
  KEY `technical_drawings_company_id_visibility_index` (`company_id`,`visibility`),
  KEY `technical_drawings_drawing_number_revision_index` (`drawing_number`,`revision`),
  KEY `technical_drawings_project_name_part_number_index` (`project_name`,`part_number`),
  KEY `technical_drawings_industry_category_application_area_index` (`industry_category`,`application_area`),
  KEY `technical_drawings_is_featured_is_active_index` (`is_featured`,`is_active`),
  KEY `technical_drawings_created_at_status_index` (`created_at`,`status`),
  CONSTRAINT `technical_drawings_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `technical_drawings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `marketplace_sellers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `technical_drawings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `technical_drawings_parent_drawing_id_foreign` FOREIGN KEY (`parent_drawing_id`) REFERENCES `technical_drawings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: technical_products
DROP TABLE IF EXISTS `technical_products`;
CREATE TABLE `technical_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `showcase_id` bigint(20) unsigned DEFAULT NULL,
  `seller_id` bigint(20) unsigned NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `short_description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `sale_price` decimal(10,2) GENERATED ALWAYS AS (`price` * (1 - `discount_percentage` / 100)) STORED,
  `category_id` bigint(20) unsigned NOT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `software_compatibility` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`software_compatibility`)),
  `file_formats` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`file_formats`)),
  `complexity_level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'intermediate',
  `industry_applications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`industry_applications`)),
  `preview_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`preview_images`)),
  `sample_files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sample_files`)),
  `protected_files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`protected_files`)),
  `documentation_files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`documentation_files`)),
  `view_count` int(10) unsigned NOT NULL DEFAULT 0,
  `download_count` int(10) unsigned NOT NULL DEFAULT 0,
  `sales_count` int(10) unsigned NOT NULL DEFAULT 0,
  `total_revenue` decimal(12,2) NOT NULL DEFAULT 0.00,
  `rating_average` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(10) unsigned NOT NULL DEFAULT 0,
  `status` enum('draft','pending','approved','rejected','suspended') NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_bestseller` tinyint(1) NOT NULL DEFAULT 0,
  `featured_until` timestamp NULL DEFAULT NULL,
  `meta_title` varchar(191) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `technical_products_slug_unique` (`slug`),
  KEY `technical_products_showcase_id_foreign` (`showcase_id`),
  KEY `technical_products_seller_id_status_index` (`seller_id`,`status`),
  KEY `technical_products_category_id_is_featured_index` (`category_id`,`is_featured`),
  KEY `technical_products_price_status_index` (`price`,`status`),
  KEY `technical_products_rating_average_rating_count_index` (`rating_average`,`rating_count`),
  FULLTEXT KEY `technical_products_title_description_keywords_fulltext` (`title`,`description`,`keywords`),
  CONSTRAINT `technical_products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`),
  CONSTRAINT `technical_products_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `technical_products_showcase_id_foreign` FOREIGN KEY (`showcase_id`) REFERENCES `showcases` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: thread_bookmarks
DROP TABLE IF EXISTS `thread_bookmarks`;
CREATE TABLE `thread_bookmarks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `thread_id` bigint(20) unsigned NOT NULL,
  `folder` varchar(191) DEFAULT NULL COMMENT 'Folder name for organizing bookmarks',
  `notes` text DEFAULT NULL COMMENT 'User notes for the bookmark',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `thread_bookmarks_user_id_thread_id_unique` (`user_id`,`thread_id`),
  KEY `thread_bookmarks_thread_id_created_at_index` (`thread_id`,`created_at`),
  CONSTRAINT `thread_bookmarks_thread_id_foreign` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `thread_bookmarks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: thread_follows
DROP TABLE IF EXISTS `thread_follows`;
CREATE TABLE `thread_follows` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `thread_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `thread_follows_user_id_thread_id_unique` (`user_id`,`thread_id`),
  KEY `thread_follows_thread_id_created_at_index` (`thread_id`,`created_at`),
  KEY `thread_follows_user_created_idx` (`user_id`,`created_at`),
  KEY `thread_follows_thread_created_idx` (`thread_id`,`created_at`),
  CONSTRAINT `thread_follows_thread_id_foreign` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `thread_follows_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=755 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: thread_likes
DROP TABLE IF EXISTS `thread_likes`;
CREATE TABLE `thread_likes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `thread_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `thread_likes_user_id_thread_id_unique` (`user_id`,`thread_id`),
  KEY `thread_likes_thread_id_created_at_index` (`thread_id`,`created_at`),
  CONSTRAINT `thread_likes_thread_id_foreign` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `thread_likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3625 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: thread_ratings
DROP TABLE IF EXISTS `thread_ratings`;
CREATE TABLE `thread_ratings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `thread_id` bigint(20) unsigned NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `thread_ratings_user_id_thread_id_unique` (`user_id`,`thread_id`),
  KEY `thread_ratings_thread_id_rating_index` (`thread_id`,`rating`),
  KEY `thread_ratings_rating_created_at_index` (`rating`,`created_at`),
  CONSTRAINT `thread_ratings_thread_id_foreign` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `thread_ratings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: thread_saves
DROP TABLE IF EXISTS `thread_saves`;
CREATE TABLE `thread_saves` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `thread_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `thread_saves_user_id_thread_id_unique` (`user_id`,`thread_id`),
  KEY `thread_saves_thread_id_foreign` (`thread_id`),
  KEY `thread_saves_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `thread_saves_thread_id_foreign` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `thread_saves_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=318 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: thread_tag
DROP TABLE IF EXISTS `thread_tag`;
CREATE TABLE `thread_tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `thread_tag_thread_id_tag_id_unique` (`thread_id`,`tag_id`),
  KEY `thread_tag_thread_id_index` (`thread_id`),
  KEY `thread_tag_tag_id_index` (`tag_id`),
  CONSTRAINT `thread_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE,
  CONSTRAINT `thread_tag_thread_id_foreign` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=355 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: threads
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
  KEY `threads_forum_created_idx` (`forum_id`,`created_at`),
  KEY `threads_user_forum_created_idx` (`user_id`,`forum_id`,`created_at`),
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

-- Table: typing_indicators
DROP TABLE IF EXISTS `typing_indicators`;
CREATE TABLE `typing_indicators` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `context_type` varchar(191) NOT NULL,
  `context_id` bigint(20) unsigned NOT NULL,
  `typing_type` varchar(191) NOT NULL DEFAULT 'comment',
  `started_at` timestamp NOT NULL,
  `last_activity_at` timestamp NOT NULL,
  `expires_at` timestamp NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `typing_unique_constraint` (`user_id`,`context_type`,`context_id`,`typing_type`),
  KEY `typing_indicators_context_type_context_id_expires_at_index` (`context_type`,`context_id`,`expires_at`),
  KEY `typing_indicators_user_id_last_activity_at_index` (`user_id`,`last_activity_at`),
  KEY `typing_indicators_expires_at_index` (`expires_at`),
  CONSTRAINT `typing_indicators_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: user_achievements
DROP TABLE IF EXISTS `user_achievements`;
CREATE TABLE `user_achievements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `achievement_id` bigint(20) unsigned NOT NULL,
  `unlocked_at` timestamp NOT NULL,
  `progress_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`progress_data`)),
  `current_progress` int(11) NOT NULL DEFAULT 0,
  `target_progress` int(11) NOT NULL DEFAULT 1,
  `is_notified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_achievements_user_id_achievement_id_unique` (`user_id`,`achievement_id`),
  KEY `user_achievements_user_id_unlocked_at_index` (`user_id`,`unlocked_at`),
  KEY `user_achievements_achievement_id_unlocked_at_index` (`achievement_id`,`unlocked_at`),
  KEY `user_achievements_is_notified_index` (`is_notified`),
  CONSTRAINT `user_achievements_achievement_id_foreign` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_achievements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: user_activities
DROP TABLE IF EXISTS `user_activities`;
CREATE TABLE `user_activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `activity_type` varchar(191) NOT NULL,
  `activity_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_activities_user_id_index` (`user_id`),
  CONSTRAINT `user_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=915 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: user_devices
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
  PRIMARY KEY (`id`),
  KEY `user_devices_user_trusted_seen_idx` (`user_id`,`is_trusted`,`last_seen_at`),
  KEY `user_devices_fingerprint_user_idx` (`device_fingerprint`,`user_id`),
  KEY `user_devices_ip_created_idx` (`ip_address`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: user_favorite_companies
DROP TABLE IF EXISTS `user_favorite_companies`;
CREATE TABLE `user_favorite_companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `marketplace_seller_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_favorite_companies_user_id_marketplace_seller_id_unique` (`user_id`,`marketplace_seller_id`),
  KEY `user_favorite_companies_marketplace_seller_id_foreign` (`marketplace_seller_id`),
  CONSTRAINT `user_favorite_companies_marketplace_seller_id_foreign` FOREIGN KEY (`marketplace_seller_id`) REFERENCES `marketplace_sellers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_favorite_companies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: user_follows
DROP TABLE IF EXISTS `user_follows`;
CREATE TABLE `user_follows` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `follower_id` bigint(20) unsigned NOT NULL,
  `following_id` bigint(20) unsigned NOT NULL,
  `followed_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_follows_follower_id_following_id_unique` (`follower_id`,`following_id`),
  KEY `user_follows_follower_id_followed_at_index` (`follower_id`,`followed_at`),
  KEY `user_follows_following_id_followed_at_index` (`following_id`,`followed_at`),
  CONSTRAINT `user_follows_follower_id_foreign` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_follows_following_id_foreign` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: user_has_roles
DROP TABLE IF EXISTS `user_has_roles`;
CREATE TABLE `user_has_roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'ID của user',
  `role_id` bigint(20) unsigned NOT NULL COMMENT 'ID của role',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Role chính của user',
  `assigned_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian gán role',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian hết hạn (null = vĩnh viễn)',
  `assigned_by` bigint(20) unsigned DEFAULT NULL COMMENT 'Người gán role',
  `assignment_reason` text DEFAULT NULL COMMENT 'Lý do gán role',
  `assignment_conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Điều kiện đặc biệt' CHECK (json_valid(`assignment_conditions`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Trạng thái hoạt động',
  `deactivated_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian vô hiệu hóa',
  `deactivated_by` bigint(20) unsigned DEFAULT NULL COMMENT 'Người vô hiệu hóa',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_role_unique` (`user_id`,`role_id`),
  KEY `user_has_roles_user_id_index` (`user_id`),
  KEY `user_has_roles_role_id_index` (`role_id`),
  KEY `user_has_roles_user_id_is_primary_index` (`user_id`,`is_primary`),
  KEY `user_has_roles_is_active_expires_at_index` (`is_active`,`expires_at`),
  KEY `user_has_roles_assigned_by_foreign` (`assigned_by`),
  KEY `user_has_roles_deactivated_by_foreign` (`deactivated_by`),
  CONSTRAINT `user_has_roles_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_has_roles_deactivated_by_foreign` FOREIGN KEY (`deactivated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_has_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: user_payment_methods
DROP TABLE IF EXISTS `user_payment_methods`;
CREATE TABLE `user_payment_methods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` enum('stripe_card','stripe_bank','vnpay','bank_account') NOT NULL,
  `gateway_payment_method_id` varchar(191) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `last_four` varchar(191) DEFAULT NULL,
  `brand` varchar(191) DEFAULT NULL,
  `exp_month` varchar(191) DEFAULT NULL,
  `exp_year` varchar(191) DEFAULT NULL,
  `bank_name` varchar(191) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `verified_at` timestamp NULL DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_payment_methods_user_id_is_active_index` (`user_id`,`is_active`),
  KEY `user_payment_methods_user_id_is_default_index` (`user_id`,`is_default`),
  KEY `user_payment_methods_gateway_payment_method_id_index` (`gateway_payment_method_id`),
  KEY `user_payment_methods_type_is_active_index` (`type`,`is_active`),
  CONSTRAINT `user_payment_methods_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: user_unread_counts
DROP TABLE IF EXISTS `user_unread_counts`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_unread_counts` AS select `notifications`.`user_id` AS `user_id`,count(0) AS `unread_count`,max(`notifications`.`created_at`) AS `latest_notification` from `notifications` where `notifications`.`is_read` = 0 group by `notifications`.`user_id`;

-- Table: user_verification_documents
DROP TABLE IF EXISTS `user_verification_documents`;
CREATE TABLE `user_verification_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `original_name` varchar(191) NOT NULL,
  `file_path` varchar(191) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `mime_type` varchar(191) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_verification_documents_reviewed_by_foreign` (`reviewed_by`),
  KEY `user_verification_documents_user_id_status_index` (`user_id`,`status`),
  KEY `user_verification_documents_document_type_index` (`document_type`),
  CONSTRAINT `user_verification_documents_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_verification_documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: user_visits
DROP TABLE IF EXISTS `user_visits`;
CREATE TABLE `user_visits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `visitable_id` bigint(20) unsigned NOT NULL,
  `visitable_type` varchar(191) NOT NULL,
  `last_visit_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_visits_user_id_visitable_id_visitable_type_unique` (`user_id`,`visitable_id`,`visitable_type`),
  CONSTRAINT `user_visits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: users
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
  `locale` varchar(5) NOT NULL DEFAULT 'vi',
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
  `notification_preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notification_preferences`)),
  `email_notifications_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `browser_notifications_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `marketing_emails_enabled` tinyint(1) NOT NULL DEFAULT 1,
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
  KEY `users_active_seen_idx` (`is_active`,`last_seen_at`),
  KEY `users_verified_active_idx` (`email_verified_at`,`is_active`),
  KEY `users_locale_index` (`locale`),
  KEY `users_role_active_idx` (`role`,`is_active`),
  KEY `users_locale_notifications_idx` (`locale`,`email_notifications_enabled`),
  KEY `users_last_login_role_idx` (`last_login_at`,`role`),
  CONSTRAINT `users_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

SET FOREIGN_KEY_CHECKS = 1;
