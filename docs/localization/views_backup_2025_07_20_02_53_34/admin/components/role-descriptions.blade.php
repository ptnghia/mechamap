{{-- 📚 MechaMap Roles & Permissions Guide --}}
<div class="card shadow-sm mt-4">
    <div class="card-header bg-light">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-book me-2"></i>
            Hướng Dẫn Roles & Permissions
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            {{-- System Management Roles --}}
            <div class="col-md-6 mb-4">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-cogs me-2"></i>
                    System Management
                </h6>
                
                <div class="mb-3">
                    <h6 class="text-danger mb-2">
                        <i class="fas fa-crown me-1"></i>
                        Super Admin
                    </h6>
                    <p class="small text-muted mb-1">
                        <strong>Vai trò:</strong> Quản trị tối cao hệ thống
                    </p>
                    <p class="small text-muted">
                        <strong>Quyền hạn:</strong> Toàn quyền truy cập tất cả tính năng, quản lý system settings, 
                        user management, security configurations
                    </p>
                </div>

                <div class="mb-3">
                    <h6 class="text-warning mb-2">
                        <i class="fas fa-user-shield me-1"></i>
                        System Admin
                    </h6>
                    <p class="small text-muted mb-1">
                        <strong>Vai trò:</strong> Quản trị hệ thống cấp cao
                    </p>
                    <p class="small text-muted">
                        <strong>Quyền hạn:</strong> Quản lý users, system monitoring, backup/restore, 
                        performance optimization
                    </p>
                </div>

                <div class="mb-3">
                    <h6 class="text-success mb-2">
                        <i class="fas fa-edit me-1"></i>
                        Content Admin
                    </h6>
                    <p class="small text-muted mb-1">
                        <strong>Vai trò:</strong> Quản trị nội dung
                    </p>
                    <p class="small text-muted">
                        <strong>Quyền hạn:</strong> Quản lý forums, pages, knowledge base, content moderation, 
                        SEO management
                    </p>
                </div>
            </div>

            {{-- Community Management Roles --}}
            <div class="col-md-6 mb-4">
                <h6 class="text-info mb-3">
                    <i class="fas fa-users me-2"></i>
                    Community Management
                </h6>
                
                <div class="mb-3">
                    <h6 class="text-info mb-2">
                        <i class="fas fa-user-edit me-1"></i>
                        Content Moderator
                    </h6>
                    <p class="small text-muted mb-1">
                        <strong>Vai trò:</strong> Kiểm duyệt nội dung
                    </p>
                    <p class="small text-muted">
                        <strong>Quyền hạn:</strong> Moderate posts/comments, approve content, 
                        manage categories, basic forum management
                    </p>
                </div>

                <div class="mb-3">
                    <h6 class="text-purple mb-2">
                        <i class="fas fa-store me-1"></i>
                        Marketplace Moderator
                    </h6>
                    <p class="small text-muted mb-1">
                        <strong>Vai trò:</strong> Quản lý thị trường
                    </p>
                    <p class="small text-muted">
                        <strong>Quyền hạn:</strong> Manage products, orders, sellers, marketplace analytics, 
                        transaction monitoring
                    </p>
                </div>

                <div class="mb-3">
                    <h6 class="text-secondary mb-2">
                        <i class="fas fa-user-friends me-1"></i>
                        Community Moderator
                    </h6>
                    <p class="small text-muted mb-1">
                        <strong>Vai trò:</strong> Quản lý cộng đồng
                    </p>
                    <p class="small text-muted">
                        <strong>Quyền hạn:</strong> User management, community events, 
                        member engagement, basic moderation
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Community Members --}}
            <div class="col-md-6 mb-4">
                <h6 class="text-success mb-3">
                    <i class="fas fa-user-circle me-2"></i>
                    Community Members
                </h6>
                
                <div class="mb-3">
                    <h6 class="text-success mb-2">
                        <i class="fas fa-star me-1"></i>
                        Thành viên cấp cao
                    </h6>
                    <p class="small text-muted mb-1">
                        <strong>Vai trò:</strong> Thành viên tích cực
                    </p>
                    <p class="small text-muted">
                        <strong>Quyền hạn:</strong> Advanced posting, file uploads, 
                        private messaging, forum access
                    </p>
                </div>

                <div class="mb-3">
                    <h6 class="text-primary mb-2">
                        <i class="fas fa-user me-1"></i>
                        Thành viên
                    </h6>
                    <p class="small text-muted mb-1">
                        <strong>Vai trò:</strong> Thành viên thường
                    </p>
                    <p class="small text-muted">
                        <strong>Quyền hạn:</strong> Basic posting, commenting, 
                        profile management, limited forum access
                    </p>
                </div>

                <div class="mb-3">
                    <h6 class="text-info mb-2">
                        <i class="fas fa-graduation-cap me-1"></i>
                        Sinh viên
                    </h6>
                    <p class="small text-muted mb-1">
                        <strong>Vai trò:</strong> Sinh viên/học viên
                    </p>
                    <p class="small text-muted">
                        <strong>Quyền hạn:</strong> Educational content access, 
                        student forums, learning resources
                    </p>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-2">
                        <i class="fas fa-eye me-1"></i>
                        Khách
                    </h6>
                    <p class="small text-muted mb-1">
                        <strong>Vai trò:</strong> Khách tham quan
                    </p>
                    <p class="small text-muted">
                        <strong>Quyền hạn:</strong> View public content, 
                        limited access, no posting
                    </p>
                </div>
            </div>

            {{-- Business Partners --}}
            <div class="col-md-6 mb-4">
                <h6 class="text-warning mb-3">
                    <i class="fas fa-handshake me-2"></i>
                    Business Partners
                </h6>
                
                <div class="mb-3">
                    <h6 class="text-warning mb-2">
                        <i class="fas fa-certificate me-1"></i>
                        Đối tác xác thực
                    </h6>
                    <p class="small text-muted mb-1">
                        <strong>Vai trò:</strong> Đối tác được xác thực
                    </p>
                    <p class="small text-muted">
                        <strong>Quyền hạn:</strong> Verified business features, 
                        premium marketplace access, partnership benefits
                    </p>
                </div>

                <div class="mb-3">
                    <h6 class="text-danger mb-2">
                        <i class="fas fa-industry me-1"></i>
                        Nhà sản xuất
                    </h6>
                    <p class="small text-muted mb-1">
                        <strong>Vai trò:</strong> Nhà sản xuất thiết bị
                    </p>
                    <p class="small text-muted">
                        <strong>Quyền hạn:</strong> Product listings, technical documentation, 
                        manufacturer tools, B2B features
                    </p>
                </div>

                <div class="mb-3">
                    <h6 class="text-info mb-2">
                        <i class="fas fa-truck me-1"></i>
                        Nhà cung cấp
                    </h6>
                    <p class="small text-muted mb-1">
                        <strong>Vai trò:</strong> Nhà cung cấp dịch vụ
                    </p>
                    <p class="small text-muted">
                        <strong>Quyền hạn:</strong> Service listings, supplier dashboard, 
                        order management, customer communication
                    </p>
                </div>

                <div class="mb-3">
                    <h6 class="text-purple mb-2">
                        <i class="fas fa-tag me-1"></i>
                        Nhãn hàng
                    </h6>
                    <p class="small text-muted mb-1">
                        <strong>Vai trò:</strong> Đại diện thương hiệu
                    </p>
                    <p class="small text-muted">
                        <strong>Quyền hạn:</strong> Brand promotion, marketing tools, 
                        brand showcase, promotional content
                    </p>
                </div>
            </div>
        </div>

        {{-- Important Notes --}}
        <div class="alert alert-info mt-3">
            <h6 class="alert-heading">
                <i class="fas fa-lightbulb me-2"></i>
                Lưu ý quan trọng:
            </h6>
            <ul class="mb-0 small">
                <li><strong>Hybrid System:</strong> Có thể kết hợp Multiple Roles + Custom Permissions</li>
                <li><strong>Security:</strong> Chỉ Admin/Moderator mới có quyền truy cập admin panel</li>
                <li><strong>Hierarchy:</strong> Roles có thứ bậc, role cao hơn có thể quản lý role thấp hơn</li>
                <li><strong>Audit Trail:</strong> Tất cả thay đổi permissions đều được ghi log</li>
            </ul>
        </div>
    </div>
</div>

<style>
.text-purple {
    color: #6f42c1 !important;
}
</style>
