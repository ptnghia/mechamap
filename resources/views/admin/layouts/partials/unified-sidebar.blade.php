{{--
    MechaMap Unified Admin Sidebar
    Consolidation of all sidebar features with enhanced security and UX
--}}

@php
    $user = auth()->user();
    $currentRoute = request()->route()->getName();
@endphp

<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Quản Trị MechaMap</li>

                <!-- Dashboard -->
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span data-key="t-dashboard">Bảng Điều Khiển</span>
                    </a>
                </li>

                @adminCanAny(['moderate-content', 'approve-content', 'manage-categories', 'manage-forums'])
                <li class="menu-title" data-key="t-content">Quản Lý Nội Dung</li>

                <!-- Forum Management -->
                @adminCanAny(['manage-categories', 'manage-forums', 'moderate-content', 'approve-content'])
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-comments"></i>
                        <span data-key="t-forum-management">Quản Lý Diễn Đàn</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @adminCan('manage-categories')
                        <li><a href="{{ route('admin.categories.index') }}" data-key="t-categories" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="fas fa-folder"></i> Danh Mục
                        </a></li>
                        @endadminCan
                        @adminCan('manage-forums')
                        <li><a href="{{ route('admin.forums.index') }}" data-key="t-forums" class="{{ request()->routeIs('admin.forums.*') ? 'active' : '' }}">
                            <i class="fas fa-comments"></i> Diễn Đàn
                        </a></li>
                        @endadminCan
                        @adminCan('moderate-content')
                        <li><a href="{{ route('admin.threads.index') }}" data-key="t-threads" class="{{ request()->routeIs('admin.threads.*') ? 'active' : '' }}">
                            <i class="fas fa-list"></i> Chủ Đề
                            {{-- @php $pendingThreads = \App\Models\Thread::where('status', 'pending')->count(); @endphp
                            @if($pendingThreads > 0)
                                <span class="badge rounded-pill bg-warning float-end">{{ $pendingThreads }}</span>
                            @endif --}}
                        </a></li>
                        @endadminCan
                        @adminCan('moderate-content')
                        <li><a href="{{ route('admin.comments.index') }}" data-key="t-comments" class="{{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                            <i class="fas fa-comment"></i> Bình Luận
                            {{-- @php $pendingComments = \App\Models\Comment::where('status', 'pending')->count(); @endphp
                            @if($pendingComments > 0)
                                <span class="badge rounded-pill bg-warning float-end">{{ $pendingComments }}</span>
                            @endif --}}
                        </a></li>
                        @endadminCan
                    </ul>
                </li>
                @endadminCanAny

                <!-- Knowledge Base & Documentation -->
                @adminCanAny(['moderate-content', 'approve-content'])
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-graduation-cap"></i>
                        <span data-key="t-knowledge">📄 Trang & Tri Thức</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <!-- Knowledge Base -->
                        <li><a href="{{ route('admin.knowledge.index') }}" data-key="t-knowledge-base" class="{{ request()->routeIs('admin.knowledge.*') ? 'active' : '' }}">
                            <i class="fas fa-brain"></i> Cơ Sở Tri Thức
                        </a></li>

                        <!-- Pages & Content -->
                        @isAdmin
                        <li><a href="{{ route('admin.pages.index') }}" data-key="t-pages" class="{{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                            <i class="fas fa-file"></i> Trang Web
                        </a></li>
                        <li><a href="{{ route('admin.page-categories.index') }}" data-key="t-page-categories" class="{{ request()->routeIs('admin.page-categories.*') ? 'active' : '' }}">
                            <i class="fas fa-folder-open"></i> Danh Mục Trang
                        </a></li>
                        <li><a href="{{ route('admin.faqs.index') }}" data-key="t-faqs" class="{{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
                            <i class="fas fa-question"></i> Câu Hỏi Thường Gặp
                        </a></li>
                        <li><a href="{{ route('admin.faq-categories.index') }}" data-key="t-faq-categories" class="{{ request()->routeIs('admin.faq-categories.*') ? 'active' : '' }}">
                            <i class="fas fa-tags"></i> Danh Mục FAQ
                        </a></li>
                        @endisAdmin

                        <!-- Documentation (Admin only) -->
                        @isAdmin
                        <li><a href="{{ route('admin.documentation.index') }}" data-key="t-docs-overview" class="{{ request()->routeIs('admin.documentation.index') ? 'active' : '' }}">
                            <i class="fas fa-chart-pie"></i> Tài Liệu Hệ Thống
                        </a></li>
                        <li><a href="{{ route('admin.documentation.user-guides') }}" data-key="t-docs-user-guides" class="{{ request()->routeIs('admin.documentation.user-guides') ? 'active' : '' }}">
                            <i class="fas fa-question-circle"></i> Hướng Dẫn Sử Dụng
                        </a></li>
                        @endisAdmin

                        <!-- Media Library -->
                        @isAdmin
                        <li><a href="{{ route('admin.media.index') }}" data-key="t-media" class="{{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
                            <i class="fas fa-images"></i> Thư Viện Media
                        </a></li>
                        @endisAdmin
                    </ul>
                </li>
                @endadminCanAny

                <!-- Showcase -->
                @adminCan('view_showcases')
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-eye"></i>
                        <span data-key="t-showcase">Trưng Bày Sản Phẩm</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.showcases.index') }}" data-key="t-all-showcases" class="{{ request()->routeIs('admin.showcases.index') ? 'active' : '' }}">
                            <i class="fas fa-list"></i> Tất Cả Sản Phẩm
                        </a></li>
                        <li><a href="{{ route('admin.showcases.pending') }}" data-key="t-pending-approval" class="{{ request()->routeIs('admin.showcases.pending') ? 'active' : '' }}">
                            <i class="fas fa-clock"></i> Chờ Duyệt
                            @php $pendingShowcases = \App\Models\Showcase::where('status', 'pending')->count(); @endphp
                            @if($pendingShowcases > 0)
                                <span class="badge rounded-pill bg-warning float-end">{{ $pendingShowcases }}</span>
                            @endif
                        </a></li>
                        <li><a href="{{ route('admin.showcases.featured') }}" data-key="t-featured" class="{{ request()->routeIs('admin.showcases.featured') ? 'active' : '' }}">
                            <i class="fas fa-star"></i> Nổi Bật
                        </a></li>
                    </ul>
                </li>
                @endadminCan
                @endadminCanAny

                @adminCanAny(['view_products', 'view_orders', 'view_payments', 'manage_sellers'])
                <li class="menu-title" data-key="t-marketplace">Thị Trường Cơ Khí</li>

                <!-- Marketplace -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-shopping-cart"></i>
                        <span data-key="t-marketplace">Thị Trường</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @adminCan('view_products')
                        <li><a href="{{ route('admin.marketplace.products.index') }}" data-key="t-products" class="{{ request()->routeIs('admin.marketplace.products*') ? 'active' : '' }}">
                            <i class="fas fa-box"></i> Sản Phẩm
                        </a></li>
                        @endadminCan
                        @adminCan('view_orders')
                        <li><a href="{{ route('admin.marketplace.orders.index') }}" data-key="t-orders" class="{{ request()->routeIs('admin.marketplace.orders*') ? 'active' : '' }}">
                            <i class="fas fa-receipt"></i> Đơn Hàng
                            @php $newOrders = \App\Models\Order::where('status', 'pending')->count(); @endphp
                            @if($newOrders > 0)
                                <span class="badge rounded-pill bg-success float-end">{{ $newOrders }}</span>
                            @endif
                        </a></li>
                        @endadminCan
                        @adminCan('view_payments')
                        <li><a href="{{ route('admin.payments.index') }}" data-key="t-payments" class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                            <i class="fas fa-credit-card"></i> Thanh Toán
                        </a></li>
                        @endadminCan
                        @adminCan('manage_sellers')
                        <li><a href="{{ route('admin.marketplace.sellers.index') }}" data-key="t-sellers" class="{{ request()->routeIs('admin.marketplace.sellers*') ? 'active' : '' }}">
                            <i class="fas fa-store"></i> Nhà Cung Cấp
                        </a></li>
                        @endadminCan
                        @adminCan('view_products')
                        <li><a href="{{ route('admin.marketplace.categories.index') }}" data-key="t-marketplace-categories" class="{{ request()->routeIs('admin.marketplace.categories*') ? 'active' : '' }}">
                            <i class="fas fa-tags"></i> Danh Mục Sản Phẩm
                        </a></li>
                        @endadminCan
                        @adminCan('view_payments')
                        <li><a href="{{ route('admin.marketplace.transactions.index') }}" data-key="t-transactions" class="{{ request()->routeIs('admin.marketplace.transactions*') ? 'active' : '' }}">
                            <i class="fas fa-exchange-alt"></i> Giao Dịch
                        </a></li>
                        @endadminCan
                    </ul>
                </li>
                @endadminCanAny

                @adminCanAny(['view_users', 'manage_admins', 'manage_roles'])
                <li class="menu-title" data-key="t-users">Quản Lý Người Dùng</li>

                <!-- User Management -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-users"></i>
                        <span data-key="t-user-management">Người Dùng</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @adminCan('view_users')
                        <li><a href="{{ route('admin.users.index') }}" data-key="t-all-users" class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                            <i class="fas fa-list"></i> Tất Cả Người Dùng
                        </a></li>
                        @endadminCan
                        @adminCan('manage_admins')
                        <li><a href="{{ route('admin.users.admins') }}" data-key="t-admins" class="{{ request()->routeIs('admin.users.admins*') ? 'active' : '' }}">
                            <i class="fas fa-user-shield"></i> Quản Trị Viên
                        </a></li>
                        @endadminCan
                        @adminCan('view_users')
                        <li><a href="{{ route('admin.users.members') }}" data-key="t-members" class="{{ request()->routeIs('admin.users.members*') ? 'active' : '' }}">
                            <i class="fas fa-user"></i> Thành Viên
                        </a></li>
                        @endadminCan
                        @adminCan('manage_roles')
                        <li><a href="{{ route('admin.roles.index') }}" data-key="t-roles" class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                            <i class="fas fa-key"></i> Vai Trò & Quyền Hạn
                        </a></li>
                        @endadminCan
                    </ul>
                </li>
                @endadminCanAny

                <!-- Moderation -->
                @adminCanAny(['moderate-content', 'approve-content', 'view-reports', 'manage-reports'])
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-shield-alt"></i>
                        <span data-key="t-moderation">Kiểm Duyệt</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.moderation.dashboard') }}" data-key="t-mod-dashboard" class="{{ request()->routeIs('admin.moderation.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i> Bảng Điều Khiển
                        </a></li>
                        @adminCan('moderate-content')
                        <li><a href="{{ route('admin.moderation.threads') }}" data-key="t-mod-threads" class="{{ request()->routeIs('admin.moderation.threads') ? 'active' : '' }}">
                            <i class="fas fa-list"></i> Chủ Đề
                        </a></li>
                        @endadminCan
                        @adminCan('moderate-content')
                        <li><a href="{{ route('admin.moderation.comments') }}" data-key="t-mod-comments" class="{{ request()->routeIs('admin.moderation.comments') ? 'active' : '' }}">
                            <i class="fas fa-comment"></i> Bình Luận
                        </a></li>
                        @endadminCan
                        @adminCan('view-reports')
                        <li><a href="{{ route('admin.moderation.reports') }}" data-key="t-reports" class="{{ request()->routeIs('admin.moderation.reports*') ? 'active' : '' }}">
                            <i class="fas fa-flag"></i> Báo Cáo Vi Phạm
                            @php $pendingReports = \App\Models\Report::where('status', 'pending')->count(); @endphp
                            @if($pendingReports > 0)
                                <span class="badge rounded-pill bg-danger float-end">{{ $pendingReports }}</span>
                            @endif
                        </a></li>
                        @endadminCan
                    </ul>
                </li>
                @endadminCanAny

                @adminCanAny(['view_messages', 'send_messages'])
                <li class="menu-title" data-key="t-communication">Giao Tiếp & Thông Báo</li>

                <!-- Messages & Alerts -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-envelope"></i>
                        <span data-key="t-communication">Giao Tiếp</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @adminCan('view_messages')
                        <li><a href="{{ route('admin.chat.index') }}" data-key="t-chat" class="{{ request()->routeIs('admin.chat.*') ? 'active' : '' }}">
                            <i class="fas fa-comments"></i> Chat Trực Tiếp
                            {{-- @php $unreadMessages = auth()->user()->unreadMessages()->count(); @endphp
                            @if($unreadMessages > 0)
                                <span class="badge rounded-pill bg-info float-end">{{ $unreadMessages }}</span>
                            @endif --}}
                        </a></li>
                        @endadminCan
                        @isAdmin
                        <li><a href="{{ route('admin.messages.index') }}" data-key="t-messages" class="{{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i> Cấu Hình Tin Nhắn
                        </a></li>
                        <li><a href="{{ route('admin.alerts.index') }}" data-key="t-alerts" class="{{ request()->routeIs('admin.alerts.*') ? 'active' : '' }}">
                            <i class="fas fa-bell"></i> Cấu Hình Thông Báo
                        </a></li>
                        <li><a href="{{ route('admin.notifications.index') }}" data-key="t-notifications" class="{{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                            <i class="fas fa-bell-slash"></i> Quản Lý Thông Báo
                        </a></li>
                        @endisAdmin
                    </ul>
                </li>
                @endadminCanAny

                @adminCanAny(['view_cad_files', 'view_materials', 'view_standards'])
                <li class="menu-title" data-key="t-technical">Quản Lý Kỹ Thuật</li>

                <!-- Technical Management -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-cogs"></i>
                        <span data-key="t-technical-mgmt">Kỹ Thuật</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @adminCan('view_cad_files')
                        <li><a href="{{ route('admin.technical.drawings.index') }}" data-key="t-drawings" class="{{ request()->routeIs('admin.technical.drawings.*') ? 'active' : '' }}">
                            <i class="fas fa-drafting-compass"></i> Bản Vẽ Kỹ Thuật
                        </a></li>
                        <li><a href="{{ route('admin.technical.cad-files.index') }}" data-key="t-cad-files" class="{{ request()->routeIs('admin.technical.cad-files.*') ? 'active' : '' }}">
                            <i class="fas fa-file-code"></i> File CAD
                        </a></li>
                        @endadminCan
                        @adminCan('view_materials')
                        <li><a href="{{ route('admin.technical.materials.index') }}" data-key="t-materials" class="{{ request()->routeIs('admin.technical.materials.*') ? 'active' : '' }}">
                            <i class="fas fa-cubes"></i> Vật Liệu
                        </a></li>
                        <li><a href="{{ route('admin.technical.processes.index') }}" data-key="t-processes" class="{{ request()->routeIs('admin.technical.processes.*') ? 'active' : '' }}">
                            <i class="fas fa-industry"></i> Quy Trình Sản Xuất
                        </a></li>
                        @endadminCan
                        @adminCan('view_standards')
                        <li><a href="{{ route('admin.technical.standards.index') }}" data-key="t-standards" class="{{ request()->routeIs('admin.technical.standards.*') ? 'active' : '' }}">
                            <i class="fas fa-certificate"></i> Tiêu Chuẩn Kỹ Thuật
                        </a></li>
                        @endadminCan
                    </ul>
                </li>
                @endadminCanAny

                @adminCanAny(['view_statistics', 'view_analytics'])
                <li class="menu-title" data-key="t-analytics">Phân Tích & Báo Cáo</li>

                <!-- Statistics -->
                @adminCan('view_statistics')
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-chart-line"></i>
                        <span data-key="t-statistics">Thống Kê</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.statistics.index') }}" data-key="t-overview" class="{{ request()->routeIs('admin.statistics.index') ? 'active' : '' }}">
                            <i class="fas fa-chart-pie"></i> Tổng Quan
                        </a></li>
                        <li><a href="{{ route('admin.statistics.users') }}" data-key="t-users-stats" class="{{ request()->routeIs('admin.statistics.users') ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Người Dùng
                        </a></li>
                        <li><a href="{{ route('admin.statistics.content') }}" data-key="t-content-stats" class="{{ request()->routeIs('admin.statistics.content') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i> Nội Dung
                        </a></li>
                        <li><a href="{{ route('admin.statistics.interactions') }}" data-key="t-interactions" class="{{ request()->routeIs('admin.statistics.interactions') ? 'active' : '' }}">
                            <i class="fas fa-handshake"></i> Tương Tác
                        </a></li>
                    </ul>
                </li>
                @endadminCan

                <!-- Advanced Analytics -->
                @adminCan('view_analytics')
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-chart-bar"></i>
                        <span data-key="t-analytics">Phân Tích Nâng Cao</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.analytics.index') }}" data-key="t-analytics-overview" class="{{ request()->routeIs('admin.analytics.index') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a></li>
                        <li><a href="{{ route('admin.analytics.realtime') }}" data-key="t-realtime-analytics" class="{{ request()->routeIs('admin.analytics.realtime*') ? 'active' : '' }}">
                            <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i> Real-time Analytics
                        </a></li>
                        <li><a href="{{ route('admin.analytics.kpi.index') }}" data-key="t-kpi-builder" class="{{ request()->routeIs('admin.analytics.kpi.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-area"></i> KPI Builder
                        </a></li>
                        <li><a href="{{ route('admin.analytics.business.dashboard') }}" data-key="t-business-analytics" class="{{ request()->routeIs('admin.analytics.business.*') ? 'active' : '' }}">
                            <i class="fas fa-briefcase"></i> Business Analytics
                        </a></li>
                        <li><a href="{{ route('admin.analytics.revenue') }}" data-key="t-revenue" class="{{ request()->routeIs('admin.analytics.revenue') ? 'active' : '' }}">
                            <i class="fas fa-dollar-sign"></i> Doanh Thu
                        </a></li>
                        <li><a href="{{ route('admin.analytics.users') }}" data-key="t-user-analytics" class="{{ request()->routeIs('admin.analytics.users') ? 'active' : '' }}">
                            <i class="fas fa-user-chart"></i> Người Dùng
                        </a></li>
                        <li><a href="{{ route('admin.analytics.marketplace') }}" data-key="t-marketplace-analytics" class="{{ request()->routeIs('admin.analytics.marketplace') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart"></i> Marketplace
                        </a></li>
                        <li><a href="{{ route('admin.analytics.technical') }}" data-key="t-technical-analytics" class="{{ request()->routeIs('admin.analytics.technical') ? 'active' : '' }}">
                            <i class="fas fa-cogs"></i> Kỹ Thuật
                        </a></li>
                        <li><a href="{{ route('admin.analytics.content') }}" data-key="t-content-analytics" class="{{ request()->routeIs('admin.analytics.content') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i> Nội Dung
                        </a></li>
                    </ul>
                </li>
                @endadminCan
                @endadminCanAny

                @adminCanAny(['manage_seo', 'manage_performance', 'view_settings', 'manage_locations'])
                <li class="menu-title" data-key="t-system">Hệ Thống & Công Cụ</li>

                <!-- SEO & Search -->
                @adminCan('manage_seo')
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-search"></i>
                        <span data-key="t-seo-search">SEO & Tìm Kiếm</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.seo.index') }}" data-key="t-seo" class="{{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                            <i class="fas fa-search-plus"></i> Quản Lý SEO
                        </a></li>
                        <li><a href="{{ route('admin.search.index') }}" data-key="t-search" class="{{ request()->routeIs('admin.search.*') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i> Cấu Hình Tìm Kiếm
                        </a></li>
                        <li><a href="{{ route('admin.page-seo.index') }}" data-key="t-page-seo" class="{{ request()->routeIs('admin.page-seo.*') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i> SEO Trang Web
                        </a></li>
                    </ul>
                </li>
                @endadminCan

                <!-- Performance & Security -->
                @adminCan('manage_performance')
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-tachometer-alt"></i>
                        <span data-key="t-performance">Hiệu Suất & Bảo Mật</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.performance.index') }}" data-key="t-performance-dashboard" class="{{ request()->routeIs('admin.performance.index') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a></li>
                        <li><a href="{{ route('admin.performance.cache') }}" data-key="t-cache-management" class="{{ request()->routeIs('admin.performance.cache') ? 'active' : '' }}">
                            <i class="fas fa-memory"></i> Quản Lý Cache
                        </a></li>
                        <li><a href="{{ route('admin.performance.database') }}" data-key="t-database-optimization" class="{{ request()->routeIs('admin.performance.database') ? 'active' : '' }}">
                            <i class="fas fa-database"></i> Tối Ưu Database
                        </a></li>
                        <li><a href="{{ route('admin.performance.security') }}" data-key="t-security-monitoring" class="{{ request()->routeIs('admin.performance.security') ? 'active' : '' }}">
                            <i class="fas fa-shield-alt"></i> Giám Sát Bảo Mật
                        </a></li>
                    </ul>
                </li>
                @endadminCan

                <!-- System Settings -->
                @adminCan('view_settings')
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-cog"></i>
                        <span data-key="t-system-settings">Cài Đặt Hệ Thống</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.settings.general') }}" data-key="t-general" class="{{ request()->routeIs('admin.settings.general') ? 'active' : '' }}">
                            <i class="fas fa-cogs"></i> Cài Đặt Chung
                        </a></li>
                        <li><a href="{{ route('admin.settings.email') }}" data-key="t-email" class="{{ request()->routeIs('admin.settings.email') ? 'active' : '' }}">
                            <i class="fas fa-envelope"></i> Cài Đặt Email
                        </a></li>
                        <li><a href="{{ route('admin.settings.security') }}" data-key="t-security" class="{{ request()->routeIs('admin.settings.security') ? 'active' : '' }}">
                            <i class="fas fa-lock"></i> Bảo Mật
                        </a></li>
                        <li><a href="{{ route('admin.settings.social') }}" data-key="t-social" class="{{ request()->routeIs('admin.settings.social') ? 'active' : '' }}">
                            <i class="fas fa-share-alt"></i> Mạng Xã Hội
                        </a></li>
                        <li><a href="{{ route('admin.settings.payment') }}" data-key="t-payment" class="{{ request()->routeIs('admin.settings.payment') ? 'active' : '' }}">
                            <i class="fas fa-credit-card"></i> Thanh Toán
                        </a></li>
                    </ul>
                </li>
                @endadminCan

                <!-- Location Management -->
                @adminCan('manage_locations')
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-map-marker-alt"></i>
                        <span data-key="t-location">Địa Điểm</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.countries.index') }}" data-key="t-countries" class="{{ request()->routeIs('admin.countries.*') ? 'active' : '' }}">
                            <i class="fas fa-flag"></i> Quốc Gia
                        </a></li>
                        <li><a href="{{ route('admin.regions.index') }}" data-key="t-regions" class="{{ request()->routeIs('admin.regions.*') ? 'active' : '' }}">
                            <i class="fas fa-globe"></i> Khu Vực
                        </a></li>
                    </ul>
                </li>
                @endadminCan
                @endadminCanAny

                <li class="menu-title" data-key="t-account">Tài Khoản</li>

                <!-- Profile -->
                <li>
                    <a href="{{ route('admin.profile.index') }}" class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                        <i class="fas fa-user-circle"></i>
                        <span data-key="t-profile">Hồ Sơ Cá Nhân</span>
                    </a>
                </li>

                <!-- View Website -->
                <li>
                    <a href="{{ url('/') }}" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        <span data-key="t-view-website">Xem Website</span>
                    </a>
                </li>

            </ul>

            <!-- Promotional Card -->
            <div class="card sidebar-alert border-0 text-center mx-4 mb-0 mt-5">
                <div class="card-body">
                    <img src="{{ asset('assets/images/giftbox.png') }}" alt="Upgrade">
                    <div class="mt-4">
                        <h5 class="alertcard-title font-size-16">Truy Cập Không Giới Hạn</h5>
                        <p class="font-size-13">Nâng cấp gói từ dùng thử miễn phí lên 'Gói Doanh Nghiệp'.</p>
                        <a href="#!" class="btn btn-primary mt-2">Nâng Cấp Ngay</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
