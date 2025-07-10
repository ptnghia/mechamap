{{-- Permission Matrix Component --}}
<div class="card">
    <div class="card-header">
        <h4 class="card-title">
            <i class="fas fa-shield-alt me-2"></i>
            Ma Trận Phân Quyền Marketplace
        </h4>
        <p class="card-title-desc">Quyền mua/bán theo từng loại người dùng và sản phẩm</p>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="20%">Loại Người Dùng</th>
                        <th width="25%">Quyền Mua</th>
                        <th width="25%">Quyền Bán</th>
                        <th width="30%">Mô Tả</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Guest/Member --}}
                    <tr>
                        <td>
                            <span class="badge bg-info">Cá nhân</span>
                            <br><small class="text-muted">Guest/Member</small>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                <i class="fas fa-download me-1"></i>
                                Sản phẩm kỹ thuật số
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                <i class="fas fa-upload me-1"></i>
                                Sản phẩm kỹ thuật số
                            </span>
                        </td>
                        <td>
                            <small>Chỉ được mua/bán file thiết kế CAD, hình ảnh kỹ thuật, tài liệu technical</small>
                        </td>
                    </tr>

                    {{-- Supplier --}}
                    <tr>
                        <td>
                            <span class="badge bg-primary">Nhà cung cấp</span>
                            <br><small class="text-muted">Supplier</small>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                <i class="fas fa-download me-1"></i>
                                Sản phẩm kỹ thuật số
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                <i class="fas fa-upload me-1"></i>
                                Sản phẩm kỹ thuật số
                            </span>
                            <br>
                            <span class="badge bg-success mt-1">
                                <i class="fas fa-box me-1"></i>
                                Sản phẩm mới
                            </span>
                        </td>
                        <td>
                            <small>Có thể bán thiết bị, linh kiện, vật liệu mới</small>
                        </td>
                    </tr>

                    {{-- Manufacturer --}}
                    <tr>
                        <td>
                            <span class="badge bg-warning">Nhà sản xuất</span>
                            <br><small class="text-muted">Manufacturer</small>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                <i class="fas fa-download me-1"></i>
                                Sản phẩm kỹ thuật số
                            </span>
                            <br>
                            <span class="badge bg-success mt-1">
                                <i class="fas fa-box me-1"></i>
                                Sản phẩm mới
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                <i class="fas fa-upload me-1"></i>
                                Sản phẩm kỹ thuật số
                            </span>
                        </td>
                        <td>
                            <small>Có thể mua nguyên liệu để sản xuất, chỉ bán file kỹ thuật</small>
                        </td>
                    </tr>

                    {{-- Brand --}}
                    <tr>
                        <td>
                            <span class="badge bg-secondary">Thương hiệu</span>
                            <br><small class="text-muted">Brand</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">
                                <i class="fas fa-times me-1"></i>
                                Không được phép
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-danger">
                                <i class="fas fa-times me-1"></i>
                                Không được phép
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                <i class="fas fa-eye me-1"></i>
                                Chỉ xem
                            </span>
                            <br>
                            <span class="badge bg-info mt-1">
                                <i class="fas fa-phone me-1"></i>
                                Liên hệ
                            </span>
                            <br><small>Chỉ được xem sản phẩm và liên hệ với người bán</small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Legend --}}
        <div class="row mt-3">
            <div class="col-md-12">
                <h6>Chú thích loại sản phẩm:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-download text-primary me-1"></i>
                        <strong>Sản phẩm kỹ thuật số:</strong> File thiết kế CAD, hình ảnh kỹ thuật, tài liệu technical
                    </span>
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-box text-success me-1"></i>
                        <strong>Sản phẩm mới:</strong> Thiết bị, linh kiện, vật liệu mới
                    </span>
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-recycle text-warning me-1"></i>
                        <strong>Sản phẩm cũ:</strong> Thiết bị, linh kiện đã qua sử dụng
                    </span>
                </div>
            </div>
        </div>

        {{-- Implementation Notes --}}
        <div class="alert alert-info mt-3">
            <h6><i class="fas fa-info-circle me-2"></i>Lưu ý triển khai:</h6>
            <ul class="mb-0">
                <li>Middleware <code>marketplace.permission</code> kiểm tra quyền trước khi thực hiện hành động</li>
                <li>Service <code>MarketplacePermissionService</code> cung cấp các method helper</li>
                <li>UI sẽ ẩn/hiện các nút action dựa trên quyền của user</li>
                <li>API endpoints được bảo vệ bởi middleware permission</li>
            </ul>
        </div>
    </div>
</div>
