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
                        <i class="mdi mdi-view-dashboard"></i>
                        <span data-key="t-dashboard">Bảng Điều Khiển</span>
                    </a>
                </li>

                <li class="menu-title" data-key="t-content">Quản Lý Nội Dung</li>

                <!-- Forum Management -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-forum"></i>
                        <span data-key="t-forum-management">Quản Lý Diễn Đàn</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.categories.index') }}" data-key="t-categories" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">Danh Mục</a></li>
                        <li><a href="{{ route('admin.forums.index') }}" data-key="t-forums" class="{{ request()->routeIs('admin.forums.*') ? 'active' : '' }}">Diễn Đàn</a></li>
                        <li><a href="{{ route('admin.threads.index') }}" data-key="t-threads" class="{{ request()->routeIs('admin.threads.*') ? 'active' : '' }}">Chủ Đề</a></li>
                        <li><a href="{{ route('admin.comments.index') }}" data-key="t-comments" class="{{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">Bình Luận</a></li>
                    </ul>
                </li>

                <!-- Content Management -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-file-document-multiple"></i>
                        <span data-key="t-content-management">Nội Dung</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.pages.index') }}" data-key="t-pages" class="{{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">Trang Web</a></li>
                        <li><a href="{{ route('admin.faqs.index') }}" data-key="t-faqs" class="{{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">Câu Hỏi Thường Gặp</a></li>
                        <li><a href="{{ route('admin.knowledge.index') }}" data-key="t-knowledge-base" class="{{ request()->routeIs('admin.knowledge.*') ? 'active' : '' }}">Cơ Sở Tri Thức</a></li>
                        <li><a href="{{ route('admin.media.index') }}" data-key="t-media" class="{{ request()->routeIs('admin.media.*') ? 'active' : '' }}">Thư Viện Media</a></li>
                    </ul>
                </li>

                <!-- Showcase -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-image-multiple"></i>
                        <span data-key="t-showcase">Trưng Bày Sản Phẩm</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.showcases.index') }}" data-key="t-all-showcases" class="{{ request()->routeIs('admin.showcases.index') ? 'active' : '' }}">Tất Cả Sản Phẩm</a></li>
                        <li><a href="{{ route('admin.showcases.pending') }}" data-key="t-pending-approval" class="{{ request()->routeIs('admin.showcases.pending') ? 'active' : '' }}">Chờ Duyệt</a></li>
                        <li><a href="{{ route('admin.showcases.featured') }}" data-key="t-featured" class="{{ request()->routeIs('admin.showcases.featured') ? 'active' : '' }}">Nổi Bật</a></li>
                    </ul>
                </li>

                <li class="menu-title" data-key="t-marketplace">Thị Trường Cơ Khí</li>

                <!-- Marketplace -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-shopping"></i>
                        <span data-key="t-marketplace">Thị Trường</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.products.index') }}" data-key="t-products" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">Sản Phẩm</a></li>
                        <li><a href="{{ route('admin.orders.index') }}" data-key="t-orders" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">Đơn Hàng</a></li>
                        <li><a href="{{ route('admin.payments.index') }}" data-key="t-payments" class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">Thanh Toán</a></li>
                        <li><a href="{{ route('admin.sellers.index') }}" data-key="t-sellers" class="{{ request()->routeIs('admin.sellers.*') ? 'active' : '' }}">Nhà Cung Cấp</a></li>
                    </ul>
                </li>

                <li class="menu-title" data-key="t-users">Quản Lý Người Dùng</li>

                <!-- User Management -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-account-group"></i>
                        <span data-key="t-user-management">Người Dùng</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.users.index') }}" data-key="t-all-users" class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}">Tất Cả Người Dùng</a></li>
                        <li><a href="{{ route('admin.users.admins') }}" data-key="t-admins" class="{{ request()->routeIs('admin.users.admins*') ? 'active' : '' }}">Quản Trị Viên</a></li>
                        <li><a href="{{ route('admin.users.members') }}" data-key="t-members" class="{{ request()->routeIs('admin.users.members*') ? 'active' : '' }}">Thành Viên</a></li>
                        <li><a href="{{ route('admin.roles.index') }}" data-key="t-roles" class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">Vai Trò & Quyền Hạn</a></li>
                    </ul>
                </li>

                <!-- Moderation -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-shield-check"></i>
                        <span data-key="t-moderation">Kiểm Duyệt</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.moderation.dashboard') }}" data-key="t-mod-dashboard" class="{{ request()->routeIs('admin.moderation.dashboard') ? 'active' : '' }}">Bảng Điều Khiển</a></li>
                        <li><a href="{{ route('admin.moderation.threads') }}" data-key="t-mod-threads" class="{{ request()->routeIs('admin.moderation.threads') ? 'active' : '' }}">Chủ Đề</a></li>
                        <li><a href="{{ route('admin.moderation.comments') }}" data-key="t-mod-comments" class="{{ request()->routeIs('admin.moderation.comments') ? 'active' : '' }}">Bình Luận</a></li>
                        <li><a href="{{ route('admin.reports.index') }}" data-key="t-reports" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">Báo Cáo Vi Phạm</a></li>
                    </ul>
                </li>

                <li class="menu-title" data-key="t-communication">Giao Tiếp & Thông Báo</li>

                <!-- Messages & Alerts -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-message-text"></i>
                        <span data-key="t-communication">Giao Tiếp</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.chat.index') }}" data-key="t-chat" class="{{ request()->routeIs('admin.chat.*') ? 'active' : '' }}">Chat Trực Tiếp</a></li>
                        <li><a href="{{ route('admin.messages.index') }}" data-key="t-messages" class="{{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">Cấu Hình Tin Nhắn</a></li>
                        <li><a href="{{ route('admin.alerts.index') }}" data-key="t-alerts" class="{{ request()->routeIs('admin.alerts.*') ? 'active' : '' }}">Thông Báo</a></li>
                    </ul>
                </li>

                <li class="menu-title" data-key="t-marketplace">Thị Trường Cơ Khí</li>

                <!-- Marketplace -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-store"></i>
                        <span data-key="t-marketplace">Thị Trường</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.marketplace.products.index') }}" data-key="t-products" class="{{ request()->routeIs('admin.marketplace.products.*') ? 'active' : '' }}">Sản Phẩm</a></li>
                        <li><a href="{{ route('admin.marketplace.orders.index') }}" data-key="t-orders" class="{{ request()->routeIs('admin.marketplace.orders.*') ? 'active' : '' }}">Đơn Hàng</a></li>
                        <li><a href="{{ route('admin.payments.index') }}" data-key="t-payments" class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">Thanh Toán</a></li>
                        <li><a href="{{ route('admin.marketplace.sellers.index') }}" data-key="t-sellers" class="{{ request()->routeIs('admin.marketplace.sellers.*') ? 'active' : '' }}">Nhà Cung Cấp</a></li>
                    </ul>
                </li>

                <li class="menu-title" data-key="t-technical">Quản Lý Kỹ Thuật</li>

                <!-- Technical Management -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-cogs"></i>
                        <span data-key="t-technical-mgmt">Kỹ Thuật</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.technical.drawings.index') }}" data-key="t-drawings" class="{{ request()->routeIs('admin.technical.drawings.*') ? 'active' : '' }}">Bản Vẽ Kỹ Thuật</a></li>
                        <li><a href="{{ route('admin.technical.cad-files.index') }}" data-key="t-cad-files" class="{{ request()->routeIs('admin.technical.cad-files.*') ? 'active' : '' }}">File CAD</a></li>
                        <li><a href="{{ route('admin.technical.materials.index') }}" data-key="t-materials" class="{{ request()->routeIs('admin.technical.materials.*') ? 'active' : '' }}">Vật Liệu</a></li>
                        <li><a href="{{ route('admin.technical.processes.index') }}" data-key="t-processes" class="{{ request()->routeIs('admin.technical.processes.*') ? 'active' : '' }}">Quy Trình Sản Xuất</a></li>
                        <li><a href="{{ route('admin.technical.standards.index') }}" data-key="t-standards" class="{{ request()->routeIs('admin.technical.standards.*') ? 'active' : '' }}">Tiêu Chuẩn Kỹ Thuật</a></li>
                    </ul>
                </li>

                <li class="menu-title" data-key="t-analytics">Phân Tích & Báo Cáo</li>

                <!-- Statistics -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-chart-line"></i>
                        <span data-key="t-statistics">Thống Kê</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.statistics.index') }}" data-key="t-overview" class="{{ request()->routeIs('admin.statistics.index') ? 'active' : '' }}">Tổng Quan</a></li>
                        <li><a href="{{ route('admin.statistics.users') }}" data-key="t-users-stats" class="{{ request()->routeIs('admin.statistics.users') ? 'active' : '' }}">Người Dùng</a></li>
                        <li><a href="{{ route('admin.statistics.content') }}" data-key="t-content-stats" class="{{ request()->routeIs('admin.statistics.content') ? 'active' : '' }}">Nội Dung</a></li>
                        <li><a href="{{ route('admin.statistics.interactions') }}" data-key="t-interactions" class="{{ request()->routeIs('admin.statistics.interactions') ? 'active' : '' }}">Tương Tác</a></li>
                    </ul>
                </li>

                <!-- Advanced Analytics -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-chart-box"></i>
                        <span data-key="t-analytics">Phân Tích Nâng Cao</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.analytics.index') }}" data-key="t-analytics-overview" class="{{ request()->routeIs('admin.analytics.index') ? 'active' : '' }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.analytics.revenue') }}" data-key="t-revenue" class="{{ request()->routeIs('admin.analytics.revenue') ? 'active' : '' }}">Doanh Thu</a></li>
                        <li><a href="{{ route('admin.analytics.users') }}" data-key="t-user-analytics" class="{{ request()->routeIs('admin.analytics.users') ? 'active' : '' }}">Người Dùng</a></li>
                        <li><a href="{{ route('admin.analytics.marketplace') }}" data-key="t-marketplace-analytics" class="{{ request()->routeIs('admin.analytics.marketplace') ? 'active' : '' }}">Marketplace</a></li>
                        <li><a href="{{ route('admin.analytics.technical') }}" data-key="t-technical-analytics" class="{{ request()->routeIs('admin.analytics.technical') ? 'active' : '' }}">Kỹ Thuật</a></li>
                        <li><a href="{{ route('admin.analytics.content') }}" data-key="t-content-analytics" class="{{ request()->routeIs('admin.analytics.content') ? 'active' : '' }}">Nội Dung</a></li>
                    </ul>
                </li>

                <!-- Analytics -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-google-analytics"></i>
                        <span data-key="t-analytics">Phân Tích</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.analytics.overview') }}" data-key="t-analytics-overview" class="{{ request()->routeIs('admin.analytics.overview') ? 'active' : '' }}">Tổng Quan</a></li>
                        <li><a href="{{ route('admin.analytics.marketplace') }}" data-key="t-marketplace-analytics" class="{{ request()->routeIs('admin.analytics.marketplace') ? 'active' : '' }}">Thị Trường</a></li>
                        <li><a href="{{ route('admin.analytics.users') }}" data-key="t-user-analytics" class="{{ request()->routeIs('admin.analytics.users') ? 'active' : '' }}">Người Dùng</a></li>
                        <li><a href="{{ route('admin.analytics.revenue') }}" data-key="t-revenue" class="{{ request()->routeIs('admin.analytics.revenue') ? 'active' : '' }}">Doanh Thu</a></li>
                    </ul>
                </li>

                <li class="menu-title" data-key="t-system">Hệ Thống & Công Cụ</li>

                <!-- SEO & Search -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-magnify"></i>
                        <span data-key="t-seo-search">SEO & Tìm Kiếm</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.seo.index') }}" data-key="t-seo" class="{{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">Quản Lý SEO</a></li>
                        <li><a href="{{ route('admin.search.index') }}" data-key="t-search" class="{{ request()->routeIs('admin.search.*') ? 'active' : '' }}">Cấu Hình Tìm Kiếm</a></li>
                    </ul>
                </li>

                <!-- Performance & Security -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-speedometer"></i>
                        <span data-key="t-performance">Hiệu Suất & Bảo Mật</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.performance.index') }}" data-key="t-performance-dashboard" class="{{ request()->routeIs('admin.performance.index') ? 'active' : '' }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.performance.cache') }}" data-key="t-cache-management" class="{{ request()->routeIs('admin.performance.cache') ? 'active' : '' }}">Quản Lý Cache</a></li>
                        <li><a href="{{ route('admin.performance.database') }}" data-key="t-database-optimization" class="{{ request()->routeIs('admin.performance.database') ? 'active' : '' }}">Tối Ưu Database</a></li>
                        <li><a href="{{ route('admin.performance.security') }}" data-key="t-security-monitoring" class="{{ request()->routeIs('admin.performance.security') ? 'active' : '' }}">Giám Sát Bảo Mật</a></li>
                    </ul>
                </li>

                <!-- System Settings -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-cog"></i>
                        <span data-key="t-system-settings">Cài Đặt Hệ Thống</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.settings.general') }}" data-key="t-general" class="{{ request()->routeIs('admin.settings.general') ? 'active' : '' }}">Cài Đặt Chung</a></li>
                        <li><a href="{{ route('admin.settings.email') }}" data-key="t-email" class="{{ request()->routeIs('admin.settings.email') ? 'active' : '' }}">Cài Đặt Email</a></li>
                        <li><a href="{{ route('admin.settings.security') }}" data-key="t-security" class="{{ request()->routeIs('admin.settings.security') ? 'active' : '' }}">Bảo Mật</a></li>
                        <li><a href="{{ route('admin.settings.social') }}" data-key="t-social" class="{{ request()->routeIs('admin.settings.social') ? 'active' : '' }}">Mạng Xã Hội</a></li>
                        <li><a href="{{ route('admin.settings.payment') }}" data-key="t-payment" class="{{ request()->routeIs('admin.settings.payment') ? 'active' : '' }}">Thanh Toán</a></li>
                    </ul>
                </li>

                <!-- Location Management -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-map-marker"></i>
                        <span data-key="t-location">Địa Điểm</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.countries.index') }}" data-key="t-countries" class="{{ request()->routeIs('admin.countries.*') ? 'active' : '' }}">Quốc Gia</a></li>
                        <li><a href="{{ route('admin.regions.index') }}" data-key="t-regions" class="{{ request()->routeIs('admin.regions.*') ? 'active' : '' }}">Khu Vực</a></li>
                    </ul>
                </li>

                <li class="menu-title" data-key="t-account">Tài Khoản</li>

                <!-- Profile -->
                <li>
                    <a href="{{ route('admin.profile.index') }}" class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                        <i class="mdi mdi-account-circle"></i>
                        <span data-key="t-profile">Hồ Sơ Cá Nhân</span>
                    </a>
                </li>

                <!-- View Website -->
                <li>
                    <a href="{{ route('home') }}" target="_blank">
                        <i class="mdi mdi-open-in-new"></i>
                        <span data-key="t-view-website">Xem Website</span>
                    </a>
                </li>

            </ul>

            <div class="card sidebar-alert border-0 text-center mx-4 mb-0 mt-5">
                <div class="card-body">
                    <img src="{{ asset('assets/images/giftbox.png') }}" alt="">
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
