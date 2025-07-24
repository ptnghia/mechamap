# Blade Localization Audit Report

**Directory:** components
**Generated:** 2025-07-20 03:32:10
**Files processed:** 55

## 📝 Hardcoded Texts Found (237)

- `>✓ Verified</small>`
- `>⏳ Pending Verification</small>`
- `DOMContentLoaded`
- `Tìm kiếm cuộc trò chuyện...`
- `>Đang tải...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Chat -->
                <div class=`
- `>Chọn một cuộc trò chuyện để bắt đầu</p>
                        </div>
                    </div>
                    <div class=`
- `Nhập tin nhắn...`
- `></i>
                    Tin nhắn mới
                </h5>
                <button type=`
- `>Người nhận:</label>
                        <input type=`
- `Tìm kiếm thành viên...`
- `>Tin nhắn đầu tiên:</label>
                        <textarea class=`
- `>Hủy</button>
                <button type=`
- `></i>
                    Gửi tin nhắn
                </button>
            </div>
        </div>
    </div>
</div>

@push(`
- `Stainless Steel`
- `>
                    © {{ date(`
- `Bản quyền thuộc về Công ty Cổ phần Công nghệ MechaMap.`
- `Chuyển chế độ sáng/tối`
- `>Chuyển chế độ sáng/tối</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
// Dark mode toggle functionality
document.addEventListener(`
- `Dark Mode`
- `Light Mode`
- `Facebook`
- `Twitter`
- `Instagram`
- `>
                                    🔍 {{ __(`
- `)
                            </div>
                        </li>

                        <!-- 3. Dự án - Direct Link -->
                        <li class=`
- `Thêm`
- `></i>
                                    Quản trị
                                </a>
                                <ul class=`
- `></i>
                                    Nhà cung cấp
                                </a>
                                <ul class=`
- `></i>
                                    Thương hiệu
                                </a>
                                <ul class=`
- `); // Thêm sticky-top và hiệu ứng đổ bóng
        } else {
        header.classList.remove(`
- `></i>THỬ TÌM KIẾM NÂNG CAO
                        </a>
                    </p>
                </div>
            `;
            return;
        }

        let html =`
- `></i>Thảo luận</h6>`
- `></i>${thread.author.name} •
                                        <i class=`
- `></i>${thread.stats.comments} • ${thread.created_at}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html +=`
- `></i>${showcase.author.name} •
                                        <span class=`
- `></i>${showcase.stats.views} • ⭐${showcase.stats.rating}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html +=`
- `></i>Sản phẩm</h6>`
- `></i>${product.stats.views} • ${product.created_at}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html +=`
- `></i>Thành viên</h6>`
- `>${user.role}</span>
                                    ${user.business_name ? `• ${user.business_name}` :`
- `>Tìm thấy ${totalResults} kết quả</small>
                    <a href=`
- `></i>Tìm kiếm nâng cao
                    </a>
                </div>
            </div>
        `;

        searchResultsContent.innerHTML = html;
    }

    // Legacy function for backward compatibility
    function displaySearchResults(data) {
        // Clear previous results
        searchResultsContent.innerHTML =`
- `></i>THỬ TÌM KIẾM NÂNG CAO
                        </a>
                    </p>
                </div>
            `;
            return;
        }

        // Build results HTML - Exact structure from old search
        let resultsHTML =`
- `>Chủ đề</div>
                    <div class=`
- `>
                    Xem tất cả kết quả
                </a>
            </div>
        `;

        // Update content
        searchResultsContent.innerHTML = resultsHTML;
    }



    // Cart toggle - Use Bootstrap dropdown events instead of click to avoid conflicts
    // Always initialize if cart element exists (regardless of isMarketplace flag)
    const cartToggle = document.getElementById(`
- `Chế độ sáng`
- `Chế độ tối`
- `>Mới</span></div>`
- `Đã đánh dấu tất cả thông báo là đã đọc`
- `🔍 Checking for duplicate`
- `;
                    console.log(`✅ Hidden duplicate`
- `✅ No duplicate`
- `Toggle mobile navigation`
- `Search elements not found on this page`
- `View all results`
- `Mini cart not available for this user`
- `Failed to remove item`
- `Close`
- `Search`
- `Enter`
- `Remove`
- `Thông báo`
- `>thông báo chưa đọc</span>
            </span>
        </button>

        <!-- Dropdown Menu -->
        <div class=`
- `>Thông báo</h6>
                <div class=`
- `Đánh dấu tất cả là đã đọc`
- `Xóa tất cả`
- `>Đang tải...</span>
                </div>
                <div class=`
- `>Đang tải thông báo...</div>
            </div>

            <!-- Notifications List -->
            <div class=`
- `>Không có thông báo nào</p>
            </div>

            <!-- Footer -->
            <div class=`
- `></i>
                    Xem tất cả thông báo
                </a>
            </div>
        </div>
    @else
        <!-- Guest State -->
        <button type=`
- `Đăng nhập để xem thông báo`
- `Không thể tải thông báo`
- `Có lỗi xảy ra khi tải thông báo`
- `>Mới</span>`
- `Xóa thông báo`
- `Có lỗi xảy ra`
- `Có lỗi xảy ra khi xóa thông báo`
- `=> null
])

@php
use App\Services\ShowcaseImageService;

// Định nghĩa kích thước hình ảnh
$sizes = [`
- `;

// Lấy featured image metadata
$imageMeta = ShowcaseImageService::getFeaturedImageMeta($showcase);

// Xác định link URL
$finalLinkUrl = $linkUrl ?? ($showcase->showcase_url ?? route(`
- `)
<!-- Hiển thị hình ảnh thực -->
@if($showLink)
<a href=`
- `@endif>
    @if($showLink)
</a>
@endif
@else
<!-- Hiển thị placeholder khi không có hình ảnh -->
@if($showLink)
<a href=`
- `Viết nội dung...`
- `Đậm`
- `Nghiêng`
- `Gạch chân`
- `Danh sách`
- `Danh sách số`
- `Liên kết`
- `Chèn hình ảnh`
- `Hoàn tác`
- `Làm lại`
- `>Chèn hình ảnh</h5>
                    <button type=`
- `>Tải lên hình ảnh</label>
                        <input type=`
- `>Hỗ trợ: JPG, PNG, GIF. Tối đa 5MB mỗi file.</div>
                    </div>
                    <div class=`
- `>Hoặc nhập URL hình ảnh</label>
                        <input type=`
- `>Hủy</button>
                    <button type=`
- `>Chèn</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Link Modal --}}
    <div class=`
- `>Thêm liên kết</h5>
                    <button type=`
- `>Văn bản hiển thị</label>
                        <input type=`
- `Nhập văn bản...`
- `Viết bình luận của bạn...`
- `Mở trong tab mới`
- `IntersectionObserver`
- `=> true])

@if($showSidebar)
@php
// Cache dữ liệu để tối ưu performance
$communityStats = Cache::remember(`
- `// Có thể tính toán thực tế
    ];
});

$trendingForums = Cache::remember(`
- `>
                        {{ $thread->user->name }} • {{ $thread->forum->name ??`
- `Nơi hội tụ tri thức cơ khí`
- `></i>
                                            <span>Kết nối với 64+ kỹ sư chuyên nghiệp</span>
                                        </div>
                                        <div class=`
- `></i>
                                            <span>Tham gia 118+ thảo luận kỹ thuật</span>
                                        </div>
                                        <div class=`
- `></i>
                                            <span>Chia sẻ kinh nghiệm CAD/CAM/CNC</span>
                                        </div>
                                        <div class=`
- `></i>
                                            <span>Học hỏi từ chuyên gia hàng đầu</span>
                                        </div>
                                    </div>

                                    <!-- Trust Indicators -->
                                    <div class=`
- `>Được tin tưởng bởi:</p>
                                        <div class=`
- `>Kỹ sư CAD</span>
                                            <span class=`
- `>Chuyên gia CNC</span>
                                            <span class=`
- `>Nhà sản xuất</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Section -->
                        <div class=`
- `>
                                            Hoặc đăng nhập với
                                        </span>
                                    </div>

                                    <div class=`
- `></i>
                                        Bảo mật SSL 256-bit
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Community Highlights Section -->
<div class=`
- `>Tham gia cộng đồng kỹ thuật hàng đầu Việt Nam</h4>
                    <p class=`
- `>Khám phá những thảo luận nổi bật và kết nối với các chuyên gia</p>
                </div>

                <div class=`
- `>Xu hướng nổi bật</h5>
                                <p class=`
- `>Mastercam, Siemens PLC, Robot công nghiệp</p>
                            </div>
                        </div>
                    </div>

                    <div class=`
- `>Mạng lưới chuyên gia</h5>
                                <p class=`
- `>64+ kỹ sư từ các công ty hàng đầu</p>
                            </div>
                        </div>
                    </div>

                    <div class=`
- `>Kho tri thức</h5>
                                <p class=`
- `>
                <h6>Kéo thả ảnh vào đây</h6>
                <p>hoặc <span class=`
- `>chọn từ máy tính</span></p>
            </div>
            <div class=`
- `>
                    Tối đa {{ $maxFiles }} ảnh • {{ $maxSize }}MB mỗi ảnh • JPG, PNG, GIF, WebP
                </small>
            </div>
        </div>
        <input type=`
- `);
        handleFiles(e.dataTransfer.files);
    }
    
    function handleFiles(files) {
        const fileArray = Array.from(files);
        
        // Validate file count
        if (selectedFiles.length + fileArray.length > maxFiles) {
            showError(`Chỉ được chọn tối đa ${maxFiles} ảnh`);
            return;
        }
        
        fileArray.forEach(file => {
            // Validate file type
            if (!file.type.startsWith(`
- `không phải là ảnh`);
                return;
            }
            
            // Validate file size
            if (file.size > maxSize) {
                showError(`File`
- `quá lớn (tối đa {{ $maxSize }}MB)`);
                return;
            }
            
            selectedFiles.push(file);
            createPreview(file, selectedFiles.length - 1);
        });
        
        updateFileInput();
    }
    
    function createPreview(file, index) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const previewItem = document.createElement(`
- `Bytes`
- `🇻🇳`
- `🇺🇸`
- `Đăng ký tài khoản MechaMap`
- `Tiếp tục`
- `Quay lại`
- `Bước $i`
- `></i>
                        <strong>Có lỗi xảy ra:</strong>
                        <ul class=`
- `Đang lưu tự động...`
- `Wizard steps`
- `)
<!-- Sidebar chuyên dụng cho trang tạo threads -->
@include(`
- `=> auth()->user()])
@else
<!-- Sidebar thông thường -->
<div class=`
- `>
    <!-- Thông tin về cộng đồng -->
    <div class=`
- `Cộng đồng chia sẻ thông tin về kiến trúc, xây dựng, giao
                thông, quy hoạch đô thị và nhiều chủ đề khác.`
- `) }}
                </a>
            </div>
        </div>
    </div>

    <!-- Các chủ đề mới/nổi bật -->
    <div class=`
- `) }}</a>
            </div>
        </div>
    </div>

    <!-- Các diễn đàn hàng đầu -->
    <div class=`
- `) }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Những người đóng góp hàng đầu -->
    <div class=`
- `) }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Các cộng đồng được đề xuất -->
    <div class=`
- `>
            @php
            // Lấy các diễn đàn phổ biến nhất (có nhiều threads nhất)
            $relatedForums = \App\Models\Forum::with([`
- `, null) // Chỉ lấy forums chính, không phải sub-forums
            ->orderBy(`
- `> @php
                    // Lấy ảnh đại diện của forum từ media relationship
                    $forumImage = $forum->media->first();
                    if ($forumImage) {
                        // Nếu file_path là URL đầy đủ thì dùng trực tiếp
                        if (filter_var($forumImage->file_path, FILTER_VALIDATE_URL)) {
                            $imageUrl = $forumImage->file_path;
                        } elseif (strpos($forumImage->file_path,`
- `) === 0) {
                            // Nếu file_path bắt đầu bằng /images/ thì dùng asset() trực tiếp
                            $imageUrl = asset($forumImage->file_path);
                        } else {
                            // Loại bỏ slash đầu để tránh double slash
                            $cleanPath = ltrim($forumImage->file_path,`
- `. $cleanPath);
                        }
                    } else {
                        // Fallback về avatar generator nội bộ nếu không có ảnh
                        $forumInitials = strtoupper(substr($forum->name, 0, 2));
                        $imageUrl = route(`
- `>
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= ($product->rating_average ?? 0))
                                ★
                            @else
                                ☆
                            @endif
                        @endfor
                    </div>
                    <small class=`
- `) }}₫</h5>
                            <small class=`
- `) }}₫
                            </small>
                        @else
                            <h5 class=`
- `) }}₫</h5>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class=`
- `XMLHttpRequest`
- `Icon`
- `></i>
                                Trạng thái xác thực
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>

                <!-- Shopping Cart (if can buy) -->
                @if($canBuy && $isVerified && Route::has(`
- `>Tạo mới</span>
                    </a>
                    <ul class=`
- `></i>
                                Thêm sản phẩm
                            </a>
                        </li>
                        @endif
                        @if(Route::has(`
- `></i>
                                Tạo bài viết
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- Notifications -->
                <li class=`
- `>Thông báo kinh doanh</h6></li>
                        @forelse($user->business_notifications()->limit(5)->get() as $notification)
                        <li>
                            <a class=`
- `>Không có thông báo mới</span></li>
                        @endforelse
                        <li><hr class=`
- `>Xem tất cả</a></li>
                    </ul>
                </li>

                <!-- User Profile Dropdown -->
                <li class=`
- `Đã xác thực`
- `Chờ xác thực`
- `></i>
                    <strong>Tài khoản kinh doanh:</strong> {{ $user->role_display_name }}
                    @if($isVerified)
                        - Đã xác thực
                    @else
                        - Chờ xác thực
                    @endif
                    @if($canSell && $isVerified)
                        <span class=`
- `></i>
                            Hoa hồng: {{ config(`
- `Toggle navigation`
- `>
        <!-- Column 1: Khám Phá & Mua Sắm -->
        <div class=`
- `) }}</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Column 2: Theo Mục Đích Sử Dụng -->
        <div class=`
- `>--</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Column 3: Nhà Cung Cấp & Đối Tác -->
        <div class=`
- `) }}</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Column 4: Tài Khoản & Hỗ Trợ -->
        <div class=`
- `>Quản trị</span>
                    </a>
                    <ul class=`
- `>Thông báo</h6></li>
                        <li><a class=`
- `></i>
                            Có 5 user mới đăng ký
                        </a></li>
                        <li><a class=`
- `></i>
                            2 báo cáo cần xử lý
                        </a></li>
                        <li><hr class=`
- `></i>
                    <strong>Chế độ quản trị:</strong> {{ $user->role_display_name }}
                    <span class=`
- `></i>
                        Đăng nhập lúc: {{ $user->last_login_at?->format(`
- `>
            {{-- Column 1: Tạo Nội Dung Mới --}}
            <div class=`
- `) }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Column 2: Tìm Kiếm & Khám Phá --}}
            <div class=`
- `) }}</small>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Column 3: Công Cụ & Tiện Ích --}}
            <div class=`
- `) }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Column 4: Cộng Đồng & Hỗ Trợ --}}
            <div class=`
- `Trang chủ MechaMap`
- `Diễn đàn cộng đồng (chỉ xem)`
- `Showcase sản phẩm (chỉ xem)`
- `Marketplace (chỉ xem)`
- `></i>Tiếng Việt
                        </a></li>
                        <li><a class=`
- `></i>
                    Bạn đang xem với quyền khách. 
                    <strong>Đăng ký</strong> để tham gia thảo luận và sử dụng đầy đủ tính năng.
                </small>
            </div>
            <div class=`
- `></i>
                    Đăng ký ngay
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Guest Menu Specific Styles */
.guest-notice {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.navbar-brand .brand-text {
    font-weight: 600;
    color: var(--bs-primary);
}

.nav-link.active {
    color: var(--bs-primary) !important;
    font-weight: 500;
}

.nav-link:hover {
    color: var(--bs-primary);
    transition: color 0.3s ease;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .guest-notice .col-md-4 {
        text-align: center !important;
        margin-top: 10px;
    }
    
    .navbar-nav .nav-item {
        text-align: center;
    }
    
    .navbar-nav .ms-2 {
        margin-left: 0 !important;
        margin-top: 0.5rem;
    }
}
</style>

<script>
// Guest Menu JavaScript
document.addEventListener(`
- `></i>
                                Tạo bài viết
                            </a>
                        </li>
                        @endif
                        @if(Route::has(`
- `></i>
                                Tạo showcase
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- Search -->
                <li class=`
- `>Thông báo</h6></li>
                        @forelse($user->notifications()->limit(5)->get() as $notification)
                        <li>
                            <a class=`
- `></i>
                    <strong>Tài khoản Guest:</strong> Một số tính năng bị hạn chế. 
                    <a href=`
- `>
                        Nâng cấp tài khoản
                    </a>
                </small>
            </div>
            <div class=`
- `>
    <!-- Hướng dẫn viết bài -->
    <div class=`
- `) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quy tắc cộng đồng -->
    <div class=`
- `) }}
                </a>
            </div>
        </div>
    </div>

    <!-- Các danh mục phổ biến -->
    <div class=`
- `> @php
                // Cache các forum phổ biến trong 1 giờ để tối ưu hiệu suất
                $popularForums = Cache::remember(`
- `, false) // Chỉ lấy forum công khai
                ->orderBy(`
- `) }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Hỗ trợ và trợ giúp -->
    <div class=`
- `) }}
                </a>
            </div>
        </div>
    </div>

    <!-- Thống kê cá nhân (nếu đã đăng nhập) -->
    @auth
    <div class=`
- `)
    @param array $fileTypes - Các loại file được phép upload (default: [`
- `])
    @param string|int $maxSize - Dung lượng tối đa cho mỗi file (default:`
- `)
    @param bool $multiple - Cho phép upload nhiều file (default: false)
    @param string|null $accept - MIME types được chấp nhận (auto-generate nếu null)
    @param bool $required - Trường bắt buộc (default: false)
    @param string|null $label - Label cho input (default: auto-generate)
    @param string|null $helpText - Text hướng dẫn (default: auto-generate)
    @param int $maxFiles - Số file tối đa khi multiple=true (default: 10)
    @param bool $showProgress - Hiển thị progress bar (default: true)
    @param bool $showPreview - Hiển thị preview file (default: true)
    @param bool $dragDrop - Cho phép drag & drop (default: true)
    @param string|null $id - ID của component (auto-generate nếu null)
--}}

@props([`
- `=> null
])

@php
    // Generate unique ID nếu không được cung cấp
    $componentId = $id ??`
- `. uniqid();
    
    // Generate accept attribute từ fileTypes nếu không được cung cấp
    if (!$accept) {
        $mimeTypes = [];
        foreach ($fileTypes as $type) {
            switch (strtolower($type)) {
                case`
- `;
                    break;
                default:
                    // Cho các file extension khác (CAD files, etc.)
                    $mimeTypes[] =`
- `, array_unique($mimeTypes));
    }
    
    // Parse maxSize thành bytes
    $maxSizeBytes = $maxSize;
    if (is_string($maxSize)) {
        $maxSize = strtoupper($maxSize);
        if (str_contains($maxSize,`
- `, $maxSize) * 1024 * 1024 * 1024;
        } else {
            $maxSizeBytes = (int) $maxSize;
        }
    }
    
    // Generate label nếu không được cung cấp
    if (!$label) {
        $label = $multiple ? __(`
- `;
        }
    }
    
    // Generate help text nếu không được cung cấp
    if (!$helpText) {
        $typesList = implode(`
- `></div>
</div>

<!-- Include CSS và JavaScript -->
@once
    @push(`
- `=> false])

@php
    use App\Services\MenuService;
    
    // Lấy menu component phù hợp cho user hiện tại
    $menuComponent = MenuService::getMenuComponent(auth()->user());
    $menuConfig = MenuService::getMenuConfiguration(auth()->user());
@endphp

<header class=`
- `></i>
                        Tìm kiếm
                    </h5>
                    <button type=`
- `Nhập từ khóa tìm kiếm...`
- `>Tìm trong:</label>
                                <div class=`
- `>Bài viết</label>
                                </div>
                                <div class=`
- `>Sản phẩm</label>
                                </div>
                                <div class=`
- `>Người dùng</label>
                                </div>
                            </div>
                            <div class=`
- `>Sắp xếp theo:</label>
                                <select class=`
- `>Độ liên quan</option>
                                    <option value=`
- `>Mới nhất</option>
                                    <option value=`
- `>Cũ nhất</option>
                                    <option value=`
- `>Phổ biến</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Quick Search Results -->
                    <div id=`
- `>
                        <h6>Kết quả nhanh:</h6>
                        <div class=`
- `>Đóng</button>
                    <button type=`
- `>Tìm kiếm</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile Search Modal -->
    <div class=`
- `>Tìm kiếm</h5>
                    <button type=`
- `Tìm kiếm...`
- `>Lỗi tìm kiếm</div>`
- `>Không tìm thấy kết quả</div>`
- `></i>
                Lỗi tải menu. Vui lòng tải lại trang.
                <button onclick=`
- `Nhập nội dung của bạn...`
- `>Đang tải...</span>
            </div>
            <span class=`
- `>Đang khởi tạo editor...</span>
        </div>
    </div>
</div>

{{-- Push TinyMCE scripts to the end of the page --}}
@push(`

## 🔑 Existing Translation Keys (484)

- `badges.complete`
- `nav.messages`
- `content.new_message`
- `content.minimize`
- `content.list`
- `content.chat`
- `ui.common.marketplace.advanced_search`
- `ui.common.marketplace.close`
- `ui.common.marketplace.keywords`
- `ui.common.marketplace.search_descriptions`
- `ui.common.marketplace.use_quotes_help`
- `ui.common.marketplace.category`
- `ui.common.marketplace.all_categories`
- `ui.common.marketplace.product_type`
- `ui.common.marketplace.all_types`
- `ui.common.marketplace.physical_products`
- `ui.common.marketplace.digital_products`
- `ui.common.marketplace.services`
- `ui.common.marketplace.seller_type`
- `ui.common.marketplace.all_sellers`
- `ui.common.marketplace.suppliers`
- `ui.common.marketplace.manufacturers`
- `ui.common.marketplace.brands`
- `ui.common.marketplace.price_range_usd`
- `ui.common.marketplace.min_price`
- `ui.common.marketplace.max_price`
- `ui.common.marketplace.material`
- `ui.common.marketplace.any_material`
- `ui.common.marketplace.steel`
- `ui.common.marketplace.aluminum`
- `ui.common.marketplace.stainless_steel`
- `ui.common.marketplace.titanium`
- `ui.common.marketplace.file_format`
- `ui.common.marketplace.any_format`
- `ui.common.marketplace.minimum_rating`
- `ui.common.marketplace.any_rating`
- `ui.common.marketplace.4_plus_stars`
- `ui.common.marketplace.3_plus_stars`
- `ui.common.marketplace.2_plus_stars`
- `ui.common.marketplace.availability`
- `ui.common.marketplace.in_stock_only`
- `ui.common.marketplace.featured_only`
- `ui.common.marketplace.on_sale`
- `ui.common.marketplace.sort_results_by`
- `ui.common.marketplace.relevance`
- `ui.common.marketplace.latest`
- `ui.common.marketplace.price_low_to_high`
- `ui.common.marketplace.price_high_to_low`
- `ui.common.marketplace.highest_rated`
- `ui.common.marketplace.most_popular`
- `ui.common.marketplace.name_a_z`
- `ui.common.marketplace.search_products`
- `ui.common.marketplace.clear_all`
- `ui.common.marketplace.filters_applied`
- `forum.search.placeholder`
- `ui.actions.search`
- `search.all_content`
- `search.search_in_thread`
- `search.search_in_forum`
- `nav.marketplace`
- `search.advanced`
- `ui.common.community`
- `ui.common.showcase`
- `ui.common.marketplace`
- `ui.common.add`
- `ui.common.technical_resources`
- `ui.common.technical_database`
- `ui.common.materials_database`
- `ui.common.engineering_standards`
- `ui.common.manufacturing_processes`
- `ui.common.design_resources`
- `ui.common.cad_library`
- `ui.common.technical_drawings`
- `ui.common.tools_calculators`
- `ui.common.material_cost_calculator`
- `ui.common.process_selector`
- `ui.common.standards_compliance`
- `ui.common.knowledge`
- `ui.common.learning_resources`
- `ui.common.knowledge_base`
- `ui.common.tutorials_guides`
- `ui.common.technical_documentation`
- `ui.common.industry_updates`
- `ui.common.industry_news`
- `ui.common.whats_new`
- `ui.common.industry_reports`
- `ui.common.admin_dashboard`
- `ui.common.user_management`
- `ui.common.forum_management`
- `ui.common.marketplace_management`
- `ui.common.dashboard`
- `ui.common.my_products`
- `ui.common.orders`
- `ui.common.reports`
- `ui.common.market_insights`
- `ui.common.marketplace_analytics`
- `ui.common.promotion_opportunities`
- `ui.common.more`
- `ui.common.search_discovery`
- `ui.common.advanced_search`
- `ui.common.photo_gallery`
- `ui.common.browse_by_tags`
- `ui.common.help_support`
- `ui.common.faq`
- `ui.common.help_center`
- `ui.common.contact_support`
- `ui.common.about_mechamap`
- `ui.common.about_us`
- `ui.common.terms_of_service`
- `ui.common.privacy_policy`
- `marketplace.cart.shopping_cart`
- `marketplace.cart.cart_empty`
- `marketplace.cart.add_products`
- `ui.common.supplier_dashboard`
- `ui.common.product_management`
- `ui.common.my_orders`
- `ui.common.manufacturer_dashboard`
- `ui.common.design_management`
- `ui.common.download_orders`
- `ui.common.brand_dashboard`
- `ui.common.market_analysis`
- `nav.user.profile`
- `ui.common.messages`
- `ui.common.notifications`
- `ui.common.saved`
- `ui.common.my_showcase`
- `nav.user.settings`
- `ui.common.my_business`
- `ui.common.verification_status`
- `ui.common.my_subscription`
- `auth.logout`
- `auth.register.title`
- `forum.search.recent_searches`
- `forum.search.no_recent_searches`
- `forum.search.popular_searches`
- `forum.search.cad_files`
- `forum.search.iso_standards`
- `forum.search.forum`
- `forum.search.threads`
- `showcase.project_showcase`
- `showcase.discover_engineering_projects`
- `showcase.total_projects`
- `showcase.downloads`
- `showcase.avg_rating`
- `showcase.total_views`
- `showcase.create_project`
- `showcase.popular_categories`
- `showcase.projects`
- `showcase.featured_projects`
- `content.view_all`
- `showcase.popular_software`
- `showcase.top_contributors`
- `showcase.views`
- `content.mechamap_community`
- `content.professional_network`
- `content.technical_discussions`
- `content.engineers`
- `content.weekly_activity`
- `content.growth_rate`
- `content.join_professional_network`
- `content.weekly_trends`
- `content.points`
- `content.discussions`
- `content.featured_discussions`
- `content.top_engineers`
- `content.leaderboard`
- `content.recently`
- `content.recommendations_for_you`
- `content.by`
- `content.in`
- `content.active_forums`
- `content.new_this_month`
- `content.high_activity`
- `content.medium_activity`
- `content.low_activity`
- `nav.user.dashboard`
- `nav.user.my_threads`
- `nav.user.my_comments`
- `nav.user.bookmarks`
- `nav.user.activity`
- `nav.user.following`
- `nav.user.ratings`
- `messages.quick_stats`
- `ui.common.threads`
- `ui.common.comments`
- `ui.common.following`
- `ui.common.points`
- `messages.upgrade_account`
- `messages.upgrade_to_member_desc`
- `messages.upgrade_now`
- `content.mechamap`
- `content.engineering_community`
- `forums.threads.title`
- `content.active_today`
- `content.this_week`
- `content.this_month`
- `content.quick_actions`
- `forums.actions.create_thread`
- `content.share_project`
- `content.my_profile`
- `language.vietnamese`
- `language.english`
- `language.select_language`
- `language.auto_detect`
- `ui.common.close`
- `common.loading`
- `language.switched_successfully`
- `language.switch_failed`
- `language.auto_detected`
- `auth.register.step1_label`
- `auth.register.step2_label`
- `auth.register.security_note`
- `auth.register.already_have_account`
- `auth.register.login_now`
- `auth.register.auto_saving`
- `forum.threads`
- `user.members`
- `content.active_since`
- `content.join_community`
- `content.business_development`
- `content.featured_topics`
- `content.no_featured_topics`
- `content.view_more`
- `content.popular_forums`
- `content.no_forums`
- `content.active_members`
- `content.contributions`
- `content.no_active_members`
- `content.related_communities`
- `content.topics`
- `ui.common.marketplace.in_stock`
- `ui.common.marketplace.out_of_stock`
- `ui.common.marketplace_actions.by`
- `ui.common.marketplace_actions.add_to_wishlist`
- `ui.common.marketplace_actions.add_to_cart`
- `ui.common.marketplace_actions.added_to_wishlist`
- `ui.common.marketplace_actions.added_to_cart`
- `forum.actions.unfollow_thread`
- `forum.actions.follow_thread`
- `thread.following`
- `thread.follow`
- `forum.actions.login_to_follow`
- `forum.actions.following`
- `forum.actions.follow`
- `forum.actions.error_occurred`
- `forum.actions.request_error`
- `nav.auth.login`
- `auth.login.welcome_back`
- `auth.login.email_or_username`
- `ui.common.password`
- `auth.login.remember`
- `ui.common.forgot_password`
- `auth.login.or_login_with`
- `auth.login.login_with_google`
- `auth.login.login_with_facebook`
- `auth.login.dont_have_account`
- `auth.register.create_business_account`
- `auth.password.forgot_description`
- `ui.common.email`
- `auth.password.send_reset_link`
- `auth.login.back_to_login`
- `messages.forgot_password`
- `content.processing`
- `content.error_occurred`
- `nav.home`
- `nav.forums`
- `nav.showcases`
- `nav.business.partner_dashboard`
- `nav.business.manufacturer_dashboard`
- `nav.business.supplier_dashboard`
- `nav.business.brand_dashboard`
- `nav.business.my_products`
- `nav.business.orders`
- `nav.business.analytics`
- `nav.business.market_insights`
- `nav.business.advertising`
- `nav.business.business_profile`
- `nav.user.account_settings`
- `ui.community.quick_access`
- `forum.threads.title`
- `ui.community.forum_home_desc`
- `ui.common.popular_topics`
- `ui.community.popular_discussions_desc`
- `ui.community.browse_categories`
- `ui.community.explore_topics_desc`
- `ui.community.discover`
- `ui.common.recent_discussions`
- `ui.community.recent_discussions_desc`
- `ui.common.trending`
- `ui.community.trending_desc`
- `ui.common.most_viewed`
- `ui.community.most_viewed_desc`
- `ui.common.hot_topics`
- `ui.community.hot_topics_desc`
- `ui.community.tools_connect`
- `ui.search.advanced_search`
- `ui.search.advanced_search_desc`
- `ui.common.member_directory`
- `ui.community.member_directory_desc`
- `ui.common.events_webinars`
- `ui.community.events_webinars_desc`
- `ui.common.coming_soon`
- `ui.common.job_board`
- `ui.community.job_board_desc`
- `marketplace.discover_shopping`
- `marketplace.products.all`
- `marketplace.products.all_desc`
- `marketplace.products.featured`
- `marketplace.products.featured_desc`
- `marketplace.products.newest`
- `marketplace.products.newest_desc`
- `marketplace.products.discounts`
- `marketplace.products.discounts_desc`
- `marketplace.search.advanced`
- `marketplace.search.advanced_desc`
- `marketplace.by_purpose`
- `marketplace.products.digital`
- `marketplace.products.digital_desc`
- `marketplace.products.new`
- `marketplace.products.new_desc`
- `marketplace.products.used`
- `marketplace.products.used_desc`
- `marketplace.suppliers_partners`
- `marketplace.suppliers.all`
- `marketplace.suppliers.all_desc`
- `marketplace.suppliers.verified`
- `marketplace.suppliers.verified_desc`
- `marketplace.suppliers.top_sellers`
- `marketplace.suppliers.top_sellers_desc`
- `marketplace.company_profiles`
- `marketplace.company_profiles_desc`
- `marketplace.account_support`
- `marketplace.cart.title`
- `marketplace.cart.desc`
- `marketplace.my_orders`
- `marketplace.my_orders_desc`
- `marketplace.wishlist`
- `marketplace.wishlist_desc`
- `marketplace.seller_dashboard`
- `marketplace.seller_dashboard_desc`
- `auth.login.title`
- `marketplace.login_desc`
- `marketplace.register_desc`
- `marketplace.help_support`
- `marketplace.help_support_desc`
- `nav.admin.dashboard`
- `nav.admin.users`
- `nav.admin.content`
- `nav.admin.marketplace`
- `nav.admin.settings`
- `nav.admin.profile`
- `add_menu.create_content.title`
- `add_menu.create_content.new_thread`
- `add_menu.create_content.new_thread_desc`
- `add_menu.create_content.new_showcase`
- `add_menu.create_content.new_showcase_desc`
- `add_menu.create_content.upload_photo`
- `add_menu.create_content.upload_photo_desc`
- `add_menu.status.coming_soon`
- `add_menu.create_content.add_product`
- `add_menu.create_content.add_product_desc`
- `add_menu.create_content.become_seller`
- `add_menu.create_content.become_seller_desc`
- `add_menu.create_content.create_document`
- `add_menu.create_content.create_document_desc`
- `add_menu.discovery.title`
- `add_menu.discovery.advanced_search`
- `add_menu.discovery.advanced_search_desc`
- `add_menu.discovery.browse_tags`
- `add_menu.discovery.browse_tags_desc`
- `add_menu.discovery.community_stats`
- `add_menu.discovery.community_stats_desc`
- `add_menu.discovery.tech_trends`
- `add_menu.discovery.tech_trends_desc`
- `add_menu.discovery.recommendations`
- `add_menu.discovery.recommendations_desc`
- `add_menu.tools.title`
- `add_menu.tools.calculator`
- `add_menu.tools.calculator_desc`
- `add_menu.tools.unit_converter`
- `add_menu.tools.unit_converter_desc`
- `add_menu.tools.material_lookup`
- `add_menu.tools.material_lookup_desc`
- `add_menu.tools.design_tools`
- `add_menu.tools.design_tools_desc`
- `add_menu.tools.mobile_app`
- `add_menu.tools.mobile_app_desc`
- `add_menu.status.beta`
- `add_menu.tools.api_integration`
- `add_menu.tools.api_integration_desc`
- `add_menu.status.new`
- `add_menu.community.title`
- `add_menu.community.find_experts`
- `add_menu.community.find_experts_desc`
- `add_menu.community.business_connect`
- `add_menu.community.business_connect_desc`
- `add_menu.community.mentorship`
- `add_menu.community.mentorship_desc`
- `add_menu.community.job_opportunities`
- `add_menu.community.job_opportunities_desc`
- `add_menu.community.professional_groups`
- `add_menu.community.professional_groups_desc`
- `add_menu.community.events`
- `add_menu.community.events_desc`
- `add_menu.support.title`
- `add_menu.support.faq`
- `add_menu.support.faq_desc`
- `add_menu.support.contact`
- `add_menu.support.contact_desc`
- `add_menu.support.about`
- `add_menu.support.about_desc`
- `add_menu.footer.quick_tip`
- `add_menu.footer.keyboard_shortcut`
- `add_menu.footer.dark_mode`
- `auth.login`
- `auth.register`
- `nav.docs`
- `sidebar.writing_tips`
- `sidebar.clear_title`
- `sidebar.clear_title_desc`
- `sidebar.detailed_content`
- `sidebar.detailed_content_desc`
- `sidebar.use_images`
- `sidebar.use_images_desc`
- `sidebar.choose_right_category`
- `sidebar.choose_right_category_desc`
- `sidebar.community_rules`
- `sidebar.respect_opinions`
- `sidebar.no_spam`
- `sidebar.appropriate_language`
- `sidebar.no_personal_info`
- `sidebar.verify_info`
- `sidebar.read_full_rules`
- `sidebar.popular_categories`
- `sidebar.posts`
- `sidebar.no_categories`
- `sidebar.need_support`
- `sidebar.support_description`
- `sidebar.detailed_guide`
- `sidebar.contact_support`
- `sidebar.your_activity`
- `sidebar.posts_count`
- `sidebar.comments_count`
- `sidebar.recent_post`
- `forms.upload.attach_files`
- `forms.upload.attach_file`
- `forms.upload.optional`
- `forms.upload.drag_drop_here`
- `forms.upload.or`
- `forms.upload.select_from_computer`
- `forms.upload.select_files`
- `forms.upload.files_selected`
- `forms.upload.uploading`
- `marketplace.engineering_marketplace`
- `marketplace.buy_sell_engineering_products`
- `marketplace.total_products`
- `marketplace.total_sales`
- `marketplace.avg_price_vnd`
- `marketplace.active_sellers`
- `marketplace.list_product`
- `marketplace.join_marketplace`
- `marketplace.product_categories`
- `marketplace.products.title`
- `marketplace.hot_products`
- `marketplace.top_sellers`
- `marketplace.sales.title`
- `marketplace.payment_methods`
- `marketplace.international_cards`
- `marketplace.vietnam_banking`
- `marketplace.secure_payment_guarantee`
- `ui.common.home`
- `marketplace.categories.title`
- `marketplace.suppliers.title`
- `ui.common.company_profiles`
- `marketplace.rfq.title`
- `marketplace.bulk_orders`
- `marketplace.downloads`
- `ui.common.my_account`
- `ui.common.my_profile`
- `ui.common.account_settings`
- `ui.common.logout`
- `ui.common.login`
- `ui.common.register`
- `content.showcase_item`

## 💡 Recommendations (237)

### Text: `>✓ Verified</small>`
- **Suggested key:** `ui.components._verifiedsmall`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>⏳ Pending Verification</small>`
- **Suggested key:** `ui.components._pending_verificationsmall`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `DOMContentLoaded`
- **Suggested key:** `ui.components.domcontentloaded`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Tìm kiếm cuộc trò chuyện...`
- **Suggested key:** `ui.components.tm_kim_cuc_tr_chuyn`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Đang tải...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Chat -->
                <div class=`
- **Suggested key:** `ui.components.ang_tispan_div_div_div_div_act`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Chọn một cuộc trò chuyện để bắt đầu</p>
                        </div>
                    </div>
                    <div class=`
- **Suggested key:** `ui.components.chn_mt_cuc_tr_chuyn_bt_up_div_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Nhập tin nhắn...`
- **Suggested key:** `ui.components.nhp_tin_nhn`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                    Tin nhắn mới
                </h5>
                <button type=`
- **Suggested key:** `ui.components.i_tin_nhn_mi_h5_button_type`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Người nhận:</label>
                        <input type=`
- **Suggested key:** `ui.components.ngi_nhnlabel_input_type`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Tìm kiếm thành viên...`
- **Suggested key:** `ui.components.tm_kim_thnh_vin`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Tin nhắn đầu tiên:</label>
                        <textarea class=`
- **Suggested key:** `ui.components.tin_nhn_u_tinlabel_textarea_cl`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Hủy</button>
                <button type=`
- **Suggested key:** `ui.components.hybutton_button_type`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                    Gửi tin nhắn
                </button>
            </div>
        </div>
    </div>
</div>

@push(`
- **Suggested key:** `ui.components.i_gi_tin_nhn_button_div_div_di`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Stainless Steel`
- **Suggested key:** `ui.components.stainless_steel`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>
                    © {{ date(`
- **Suggested key:** `ui.components._date`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Bản quyền thuộc về Công ty Cổ phần Công nghệ MechaMap.`
- **Suggested key:** `ui.components.bn_quyn_thuc_v_cng_ty_c_phn_cn`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Chuyển chế độ sáng/tối`
- **Suggested key:** `ui.components.chuyn_ch_sngti`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Chuyển chế độ sáng/tối</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
// Dark mode toggle functionality
document.addEventListener(`
- **Suggested key:** `ui.components.chuyn_ch_sngtispan_button_div_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Dark Mode`
- **Suggested key:** `ui.components.dark_mode`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Light Mode`
- **Suggested key:** `ui.components.light_mode`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Facebook`
- **Suggested key:** `ui.components.facebook`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Twitter`
- **Suggested key:** `ui.components.twitter`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Instagram`
- **Suggested key:** `ui.components.instagram`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>
                                    🔍 {{ __(`
- **Suggested key:** `ui.components._`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `)
                            </div>
                        </li>

                        <!-- 3. Dự án - Direct Link -->
                        <li class=`
- **Suggested key:** `ui.components._div_li_3_d_n_direct_link_li_c`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Thêm`
- **Suggested key:** `ui.components.thm`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                                    Quản trị
                                </a>
                                <ul class=`
- **Suggested key:** `ui.components.i_qun_tr_a_ul_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                                    Nhà cung cấp
                                </a>
                                <ul class=`
- **Suggested key:** `ui.components.i_nh_cung_cp_a_ul_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                                    Thương hiệu
                                </a>
                                <ul class=`
- **Suggested key:** `ui.components.i_thng_hiu_a_ul_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `); // Thêm sticky-top và hiệu ứng đổ bóng
        } else {
        header.classList.remove(`
- **Suggested key:** `ui.components._thm_stickytop_v_hiu_ng_bng_el`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>THỬ TÌM KIẾM NÂNG CAO
                        </a>
                    </p>
                </div>
            `;
            return;
        }

        let html =`
- **Suggested key:** `ui.components.ith_tm_kim_nng_cao_a_p_div_ret`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>Thảo luận</h6>`
- **Suggested key:** `ui.components.itho_lunh6`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>${thread.author.name} •
                                        <i class=`
- **Suggested key:** `ui.components.ithreadauthorname_i_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>${thread.stats.comments} • ${thread.created_at}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html +=`
- **Suggested key:** `ui.components.ithreadstatscomments_threadcre`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>${showcase.author.name} •
                                        <span class=`
- **Suggested key:** `ui.components.ishowcaseauthorname_span_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>${showcase.stats.views} • ⭐${showcase.stats.rating}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html +=`
- **Suggested key:** `ui.components.ishowcasestatsviews_showcasest`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>Sản phẩm</h6>`
- **Suggested key:** `ui.components.isn_phmh6`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>${product.stats.views} • ${product.created_at}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html +=`
- **Suggested key:** `ui.components.iproductstatsviews_productcrea`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>Thành viên</h6>`
- **Suggested key:** `ui.components.ithnh_vinh6`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>${user.role}</span>
                                    ${user.business_name ? `• ${user.business_name}` :`
- **Suggested key:** `ui.components.userrolespan_userbusinessname_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Tìm thấy ${totalResults} kết quả</small>
                    <a href=`
- **Suggested key:** `ui.components.tm_thy_totalresults_kt_qusmall`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>Tìm kiếm nâng cao
                    </a>
                </div>
            </div>
        `;

        searchResultsContent.innerHTML = html;
    }

    // Legacy function for backward compatibility
    function displaySearchResults(data) {
        // Clear previous results
        searchResultsContent.innerHTML =`
- **Suggested key:** `ui.components.itm_kim_nng_cao_a_div_div_sear`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>THỬ TÌM KIẾM NÂNG CAO
                        </a>
                    </p>
                </div>
            `;
            return;
        }

        // Build results HTML - Exact structure from old search
        let resultsHTML =`
- **Suggested key:** `ui.components.ith_tm_kim_nng_cao_a_p_div_ret`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Chủ đề</div>
                    <div class=`
- **Suggested key:** `ui.components.ch_div_div_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>
                    Xem tất cả kết quả
                </a>
            </div>
        `;

        // Update content
        searchResultsContent.innerHTML = resultsHTML;
    }



    // Cart toggle - Use Bootstrap dropdown events instead of click to avoid conflicts
    // Always initialize if cart element exists (regardless of isMarketplace flag)
    const cartToggle = document.getElementById(`
- **Suggested key:** `ui.components._xem_tt_c_kt_qu_a_div_update_c`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Chế độ sáng`
- **Suggested key:** `ui.components.ch_sng`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Chế độ tối`
- **Suggested key:** `ui.components.ch_ti`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Mới</span></div>`
- **Suggested key:** `ui.components.mispandiv`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Đã đánh dấu tất cả thông báo là đã đọc`
- **Suggested key:** `ui.components._nh_du_tt_c_thng_bo_l_c`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `🔍 Checking for duplicate`
- **Suggested key:** `ui.components._checking_for_duplicate`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `;
                    console.log(`✅ Hidden duplicate`
- **Suggested key:** `ui.components._consolelog_hidden_duplicate`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `✅ No duplicate`
- **Suggested key:** `ui.components._no_duplicate`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Toggle mobile navigation`
- **Suggested key:** `ui.components.toggle_mobile_navigation`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Search elements not found on this page`
- **Suggested key:** `ui.components.search_elements_not_found_on_t`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `View all results`
- **Suggested key:** `ui.components.view_all_results`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Mini cart not available for this user`
- **Suggested key:** `ui.components.mini_cart_not_available_for_th`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Failed to remove item`
- **Suggested key:** `ui.components.failed_to_remove_item`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Close`
- **Suggested key:** `ui.components.close`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Search`
- **Suggested key:** `ui.components.search`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Enter`
- **Suggested key:** `ui.components.enter`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Remove`
- **Suggested key:** `ui.components.remove`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Thông báo`
- **Suggested key:** `ui.components.thng_bo`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>thông báo chưa đọc</span>
            </span>
        </button>

        <!-- Dropdown Menu -->
        <div class=`
- **Suggested key:** `ui.components.thng_bo_cha_cspan_span_button_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Thông báo</h6>
                <div class=`
- **Suggested key:** `ui.components.thng_boh6_div_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Đánh dấu tất cả là đã đọc`
- **Suggested key:** `ui.components.nh_du_tt_c_l_c`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Xóa tất cả`
- **Suggested key:** `ui.components.xa_tt_c`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Đang tải...</span>
                </div>
                <div class=`
- **Suggested key:** `ui.components.ang_tispan_div_div_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Đang tải thông báo...</div>
            </div>

            <!-- Notifications List -->
            <div class=`
- **Suggested key:** `ui.components.ang_ti_thng_bodiv_div_notifica`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Không có thông báo nào</p>
            </div>

            <!-- Footer -->
            <div class=`
- **Suggested key:** `ui.components.khng_c_thng_bo_nop_div_footer_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                    Xem tất cả thông báo
                </a>
            </div>
        </div>
    @else
        <!-- Guest State -->
        <button type=`
- **Suggested key:** `ui.components.i_xem_tt_c_thng_bo_a_div_div_e`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Đăng nhập để xem thông báo`
- **Suggested key:** `ui.components.ng_nhp_xem_thng_bo`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Không thể tải thông báo`
- **Suggested key:** `ui.components.khng_th_ti_thng_bo`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Có lỗi xảy ra khi tải thông báo`
- **Suggested key:** `ui.components.c_li_xy_ra_khi_ti_thng_bo`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Mới</span>`
- **Suggested key:** `ui.components.mispan`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Xóa thông báo`
- **Suggested key:** `ui.components.xa_thng_bo`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Có lỗi xảy ra`
- **Suggested key:** `ui.components.c_li_xy_ra`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Có lỗi xảy ra khi xóa thông báo`
- **Suggested key:** `ui.components.c_li_xy_ra_khi_xa_thng_bo`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `=> null
])

@php
use App\Services\ShowcaseImageService;

// Định nghĩa kích thước hình ảnh
$sizes = [`
- **Suggested key:** `ui.components._null_php_use_appservicesshowc`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `;

// Lấy featured image metadata
$imageMeta = ShowcaseImageService::getFeaturedImageMeta($showcase);

// Xác định link URL
$finalLinkUrl = $linkUrl ?? ($showcase->showcase_url ?? route(`
- **Suggested key:** `ui.components._ly_featured_image_metadata_im`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `)
<!-- Hiển thị hình ảnh thực -->
@if($showLink)
<a href=`
- **Suggested key:** `ui.components._hin_th_hnh_nh_thc_ifshowlink_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `@endif>
    @if($showLink)
</a>
@endif
@else
<!-- Hiển thị placeholder khi không có hình ảnh -->
@if($showLink)
<a href=`
- **Suggested key:** `ui.components.endif_ifshowlink_a_endif_else_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Viết nội dung...`
- **Suggested key:** `ui.components.vit_ni_dung`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Đậm`
- **Suggested key:** `ui.components.m`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Nghiêng`
- **Suggested key:** `ui.components.nghing`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Gạch chân`
- **Suggested key:** `ui.components.gch_chn`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Danh sách`
- **Suggested key:** `ui.components.danh_sch`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Danh sách số`
- **Suggested key:** `ui.components.danh_sch_s`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Liên kết`
- **Suggested key:** `ui.components.lin_kt`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Chèn hình ảnh`
- **Suggested key:** `ui.components.chn_hnh_nh`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Hoàn tác`
- **Suggested key:** `ui.components.hon_tc`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Làm lại`
- **Suggested key:** `ui.components.lm_li`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Chèn hình ảnh</h5>
                    <button type=`
- **Suggested key:** `ui.components.chn_hnh_nhh5_button_type`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Tải lên hình ảnh</label>
                        <input type=`
- **Suggested key:** `ui.components.ti_ln_hnh_nhlabel_input_type`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Hỗ trợ: JPG, PNG, GIF. Tối đa 5MB mỗi file.</div>
                    </div>
                    <div class=`
- **Suggested key:** `ui.components.h_tr_jpg_png_gif_ti_a_5mb_mi_f`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Hoặc nhập URL hình ảnh</label>
                        <input type=`
- **Suggested key:** `ui.components.hoc_nhp_url_hnh_nhlabel_input_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Hủy</button>
                    <button type=`
- **Suggested key:** `ui.components.hybutton_button_type`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Chèn</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Link Modal --}}
    <div class=`
- **Suggested key:** `ui.components.chnbutton_div_div_div_div_endi`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Thêm liên kết</h5>
                    <button type=`
- **Suggested key:** `ui.components.thm_lin_kth5_button_type`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Văn bản hiển thị</label>
                        <input type=`
- **Suggested key:** `ui.components.vn_bn_hin_thlabel_input_type`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Nhập văn bản...`
- **Suggested key:** `ui.components.nhp_vn_bn`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Viết bình luận của bạn...`
- **Suggested key:** `ui.components.vit_bnh_lun_ca_bn`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Mở trong tab mới`
- **Suggested key:** `ui.components.m_trong_tab_mi`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `IntersectionObserver`
- **Suggested key:** `ui.components.intersectionobserver`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `=> true])

@if($showSidebar)
@php
// Cache dữ liệu để tối ưu performance
$communityStats = Cache::remember(`
- **Suggested key:** `ui.components._true_ifshowsidebar_php_cache_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `// Có thể tính toán thực tế
    ];
});

$trendingForums = Cache::remember(`
- **Suggested key:** `ui.components._c_th_tnh_ton_thc_t_trendingfo`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>
                        {{ $thread->user->name }} • {{ $thread->forum->name ??`
- **Suggested key:** `ui.components._threadusername_threadforumnam`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Nơi hội tụ tri thức cơ khí`
- **Suggested key:** `ui.components.ni_hi_t_tri_thc_c_kh`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                                            <span>Kết nối với 64+ kỹ sư chuyên nghiệp</span>
                                        </div>
                                        <div class=`
- **Suggested key:** `ui.components.i_spankt_ni_vi_64_k_s_chuyn_ng`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                                            <span>Tham gia 118+ thảo luận kỹ thuật</span>
                                        </div>
                                        <div class=`
- **Suggested key:** `ui.components.i_spantham_gia_118_tho_lun_k_t`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                                            <span>Chia sẻ kinh nghiệm CAD/CAM/CNC</span>
                                        </div>
                                        <div class=`
- **Suggested key:** `ui.components.i_spanchia_s_kinh_nghim_cadcam`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                                            <span>Học hỏi từ chuyên gia hàng đầu</span>
                                        </div>
                                    </div>

                                    <!-- Trust Indicators -->
                                    <div class=`
- **Suggested key:** `ui.components.i_spanhc_hi_t_chuyn_gia_hng_us`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Được tin tưởng bởi:</p>
                                        <div class=`
- **Suggested key:** `ui.components.c_tin_tng_bip_div_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Kỹ sư CAD</span>
                                            <span class=`
- **Suggested key:** `ui.components.k_s_cadspan_span_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Chuyên gia CNC</span>
                                            <span class=`
- **Suggested key:** `ui.components.chuyn_gia_cncspan_span_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Nhà sản xuất</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Section -->
                        <div class=`
- **Suggested key:** `ui.components.nh_sn_xutspan_div_div_div_div_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>
                                            Hoặc đăng nhập với
                                        </span>
                                    </div>

                                    <div class=`
- **Suggested key:** `ui.components._hoc_ng_nhp_vi_span_div_div_cl`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                                        Bảo mật SSL 256-bit
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Community Highlights Section -->
<div class=`
- **Suggested key:** `ui.components.i_bo_mt_ssl_256bit_small_div_d`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Tham gia cộng đồng kỹ thuật hàng đầu Việt Nam</h4>
                    <p class=`
- **Suggested key:** `ui.components.tham_gia_cng_ng_k_thut_hng_u_v`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Khám phá những thảo luận nổi bật và kết nối với các chuyên gia</p>
                </div>

                <div class=`
- **Suggested key:** `ui.components.khm_ph_nhng_tho_lun_ni_bt_v_kt`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Xu hướng nổi bật</h5>
                                <p class=`
- **Suggested key:** `ui.components.xu_hng_ni_bth5_p_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Mastercam, Siemens PLC, Robot công nghiệp</p>
                            </div>
                        </div>
                    </div>

                    <div class=`
- **Suggested key:** `ui.components.mastercam_siemens_plc_robot_cn`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Mạng lưới chuyên gia</h5>
                                <p class=`
- **Suggested key:** `ui.components.mng_li_chuyn_giah5_p_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>64+ kỹ sư từ các công ty hàng đầu</p>
                            </div>
                        </div>
                    </div>

                    <div class=`
- **Suggested key:** `ui.components.64_k_s_t_cc_cng_ty_hng_up_div_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Kho tri thức</h5>
                                <p class=`
- **Suggested key:** `ui.components.kho_tri_thch5_p_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>
                <h6>Kéo thả ảnh vào đây</h6>
                <p>hoặc <span class=`
- **Suggested key:** `ui.components._h6ko_th_nh_vo_yh6_phoc_span_c`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>chọn từ máy tính</span></p>
            </div>
            <div class=`
- **Suggested key:** `ui.components.chn_t_my_tnhspanp_div_div_clas`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>
                    Tối đa {{ $maxFiles }} ảnh • {{ $maxSize }}MB mỗi ảnh • JPG, PNG, GIF, WebP
                </small>
            </div>
        </div>
        <input type=`
- **Suggested key:** `ui.components._ti_a_maxfiles_nh_maxsize_mb_m`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `);
        handleFiles(e.dataTransfer.files);
    }
    
    function handleFiles(files) {
        const fileArray = Array.from(files);
        
        // Validate file count
        if (selectedFiles.length + fileArray.length > maxFiles) {
            showError(`Chỉ được chọn tối đa ${maxFiles} ảnh`);
            return;
        }
        
        fileArray.forEach(file => {
            // Validate file type
            if (!file.type.startsWith(`
- **Suggested key:** `ui.components._handlefilesedatatransferfiles`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `không phải là ảnh`);
                return;
            }
            
            // Validate file size
            if (file.size > maxSize) {
                showError(`File`
- **Suggested key:** `ui.components.khng_phi_l_nh_return_validate_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `quá lớn (tối đa {{ $maxSize }}MB)`);
                return;
            }
            
            selectedFiles.push(file);
            createPreview(file, selectedFiles.length - 1);
        });
        
        updateFileInput();
    }
    
    function createPreview(file, index) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const previewItem = document.createElement(`
- **Suggested key:** `ui.components.qu_ln_ti_a_maxsize_mb_return_s`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Bytes`
- **Suggested key:** `ui.components.bytes`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `🇻🇳`
- **Suggested key:** `ui.components.`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `🇺🇸`
- **Suggested key:** `ui.components.`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Đăng ký tài khoản MechaMap`
- **Suggested key:** `ui.components.ng_k_ti_khon_mechamap`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Tiếp tục`
- **Suggested key:** `ui.components.tip_tc`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Quay lại`
- **Suggested key:** `ui.components.quay_li`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Bước $i`
- **Suggested key:** `ui.components.bc_i`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                        <strong>Có lỗi xảy ra:</strong>
                        <ul class=`
- **Suggested key:** `ui.components.i_strongc_li_xy_rastrong_ul_cl`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Đang lưu tự động...`
- **Suggested key:** `ui.components.ang_lu_t_ng`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Wizard steps`
- **Suggested key:** `ui.components.wizard_steps`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `)
<!-- Sidebar chuyên dụng cho trang tạo threads -->
@include(`
- **Suggested key:** `ui.components._sidebar_chuyn_dng_cho_trang_t`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `=> auth()->user()])
@else
<!-- Sidebar thông thường -->
<div class=`
- **Suggested key:** `ui.components._authuser_else_sidebar_thng_th`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>
    <!-- Thông tin về cộng đồng -->
    <div class=`
- **Suggested key:** `ui.components._thng_tin_v_cng_ng_div_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Cộng đồng chia sẻ thông tin về kiến trúc, xây dựng, giao
                thông, quy hoạch đô thị và nhiều chủ đề khác.`
- **Suggested key:** `ui.components.cng_ng_chia_s_thng_tin_v_kin_t`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) }}
                </a>
            </div>
        </div>
    </div>

    <!-- Các chủ đề mới/nổi bật -->
    <div class=`
- **Suggested key:** `ui.components._a_div_div_div_cc_ch_mini_bt_d`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) }}</a>
            </div>
        </div>
    </div>

    <!-- Các diễn đàn hàng đầu -->
    <div class=`
- **Suggested key:** `ui.components._a_div_div_div_cc_din_n_hng_u_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Những người đóng góp hàng đầu -->
    <div class=`
- **Suggested key:** `ui.components._p_div_endforelse_div_div_div_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Các cộng đồng được đề xuất -->
    <div class=`
- **Suggested key:** `ui.components._p_div_endforelse_div_div_div_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>
            @php
            // Lấy các diễn đàn phổ biến nhất (có nhiều threads nhất)
            $relatedForums = \App\Models\Forum::with([`
- **Suggested key:** `ui.components._php_ly_cc_din_n_ph_bin_nht_c_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `, null) // Chỉ lấy forums chính, không phải sub-forums
            ->orderBy(`
- **Suggested key:** `ui.components._null_ch_ly_forums_chnh_khng_p`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `> @php
                    // Lấy ảnh đại diện của forum từ media relationship
                    $forumImage = $forum->media->first();
                    if ($forumImage) {
                        // Nếu file_path là URL đầy đủ thì dùng trực tiếp
                        if (filter_var($forumImage->file_path, FILTER_VALIDATE_URL)) {
                            $imageUrl = $forumImage->file_path;
                        } elseif (strpos($forumImage->file_path,`
- **Suggested key:** `ui.components._php_ly_nh_i_din_ca_forum_t_me`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) === 0) {
                            // Nếu file_path bắt đầu bằng /images/ thì dùng asset() trực tiếp
                            $imageUrl = asset($forumImage->file_path);
                        } else {
                            // Loại bỏ slash đầu để tránh double slash
                            $cleanPath = ltrim($forumImage->file_path,`
- **Suggested key:** `ui.components._0_nu_filepath_bt_u_bng_images`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `. $cleanPath);
                        }
                    } else {
                        // Fallback về avatar generator nội bộ nếu không có ảnh
                        $forumInitials = strtoupper(substr($forum->name, 0, 2));
                        $imageUrl = route(`
- **Suggested key:** `ui.components._cleanpath_else_fallback_v_ava`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= ($product->rating_average ?? 0))
                                ★
                            @else
                                ☆
                            @endif
                        @endfor
                    </div>
                    <small class=`
- **Suggested key:** `ui.components._fori_1_i_5_i_ifi_productratin`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) }}₫</h5>
                            <small class=`
- **Suggested key:** `ui.components._h5_small_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) }}₫
                            </small>
                        @else
                            <h5 class=`
- **Suggested key:** `ui.components._small_else_h5_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) }}₫</h5>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class=`
- **Suggested key:** `ui.components._h5_endif_div_quick_actions_di`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `XMLHttpRequest`
- **Suggested key:** `ui.components.xmlhttprequest`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Icon`
- **Suggested key:** `ui.components.icon`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                                Trạng thái xác thực
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>

                <!-- Shopping Cart (if can buy) -->
                @if($canBuy && $isVerified && Route::has(`
- **Suggested key:** `ui.components.i_trng_thi_xc_thc_a_li_endif_u`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Tạo mới</span>
                    </a>
                    <ul class=`
- **Suggested key:** `ui.components.to_mispan_a_ul_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                                Thêm sản phẩm
                            </a>
                        </li>
                        @endif
                        @if(Route::has(`
- **Suggested key:** `ui.components.i_thm_sn_phm_a_li_endif_ifrout`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                                Tạo bài viết
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- Notifications -->
                <li class=`
- **Suggested key:** `ui.components.i_to_bi_vit_a_li_endif_ul_li_e`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Thông báo kinh doanh</h6></li>
                        @forelse($user->business_notifications()->limit(5)->get() as $notification)
                        <li>
                            <a class=`
- **Suggested key:** `ui.components.thng_bo_kinh_doanhh6li_forelse`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Không có thông báo mới</span></li>
                        @endforelse
                        <li><hr class=`
- **Suggested key:** `ui.components.khng_c_thng_bo_mispanli_endfor`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Xem tất cả</a></li>
                    </ul>
                </li>

                <!-- User Profile Dropdown -->
                <li class=`
- **Suggested key:** `ui.components.xem_tt_cali_ul_li_user_profile`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Đã xác thực`
- **Suggested key:** `ui.components._xc_thc`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Chờ xác thực`
- **Suggested key:** `ui.components.ch_xc_thc`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                    <strong>Tài khoản kinh doanh:</strong> {{ $user->role_display_name }}
                    @if($isVerified)
                        - Đã xác thực
                    @else
                        - Chờ xác thực
                    @endif
                    @if($canSell && $isVerified)
                        <span class=`
- **Suggested key:** `ui.components.i_strongti_khon_kinh_doanhstro`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                            Hoa hồng: {{ config(`
- **Suggested key:** `ui.components.i_hoa_hng_config`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Toggle navigation`
- **Suggested key:** `ui.components.toggle_navigation`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>
        <!-- Column 1: Khám Phá & Mua Sắm -->
        <div class=`
- **Suggested key:** `ui.components._column_1_khm_ph_mua_sm_div_cl`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) }}</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Column 2: Theo Mục Đích Sử Dụng -->
        <div class=`
- **Suggested key:** `ui.components._small_div_a_li_ul_div_div_col`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>--</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Column 3: Nhà Cung Cấp & Đối Tác -->
        <div class=`
- **Suggested key:** `ui.components.span_a_li_ul_div_div_column_3_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) }}</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Column 4: Tài Khoản & Hỗ Trợ -->
        <div class=`
- **Suggested key:** `ui.components._small_div_a_li_ul_div_div_col`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Quản trị</span>
                    </a>
                    <ul class=`
- **Suggested key:** `ui.components.qun_trspan_a_ul_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Thông báo</h6></li>
                        <li><a class=`
- **Suggested key:** `ui.components.thng_boh6li_lia_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                            Có 5 user mới đăng ký
                        </a></li>
                        <li><a class=`
- **Suggested key:** `ui.components.i_c_5_user_mi_ng_k_ali_lia_cla`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                            2 báo cáo cần xử lý
                        </a></li>
                        <li><hr class=`
- **Suggested key:** `ui.components.i_2_bo_co_cn_x_l_ali_lihr_clas`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                    <strong>Chế độ quản trị:</strong> {{ $user->role_display_name }}
                    <span class=`
- **Suggested key:** `ui.components.i_strongch_qun_trstrong_userro`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                        Đăng nhập lúc: {{ $user->last_login_at?->format(`
- **Suggested key:** `ui.components.i_ng_nhp_lc_userlastloginatfor`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>
            {{-- Column 1: Tạo Nội Dung Mới --}}
            <div class=`
- **Suggested key:** `ui.components._column_1_to_ni_dung_mi_div_cl`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Column 2: Tìm Kiếm & Khám Phá --}}
            <div class=`
- **Suggested key:** `ui.components._span_a_li_ul_div_div_column_2`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) }}</small>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Column 3: Công Cụ & Tiện Ích --}}
            <div class=`
- **Suggested key:** `ui.components._small_div_a_li_ul_div_div_col`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Column 4: Cộng Đồng & Hỗ Trợ --}}
            <div class=`
- **Suggested key:** `ui.components._span_a_li_ul_div_div_column_4`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Trang chủ MechaMap`
- **Suggested key:** `ui.components.trang_ch_mechamap`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Diễn đàn cộng đồng (chỉ xem)`
- **Suggested key:** `ui.components.din_n_cng_ng_ch_xem`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Showcase sản phẩm (chỉ xem)`
- **Suggested key:** `ui.components.showcase_sn_phm_ch_xem`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Marketplace (chỉ xem)`
- **Suggested key:** `ui.components.marketplace_ch_xem`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>Tiếng Việt
                        </a></li>
                        <li><a class=`
- **Suggested key:** `ui.components.iting_vit_ali_lia_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                    Bạn đang xem với quyền khách. 
                    <strong>Đăng ký</strong> để tham gia thảo luận và sử dụng đầy đủ tính năng.
                </small>
            </div>
            <div class=`
- **Suggested key:** `ui.components.i_bn_ang_xem_vi_quyn_khch_stro`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                    Đăng ký ngay
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Guest Menu Specific Styles */
.guest-notice {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.navbar-brand .brand-text {
    font-weight: 600;
    color: var(--bs-primary);
}

.nav-link.active {
    color: var(--bs-primary) !important;
    font-weight: 500;
}

.nav-link:hover {
    color: var(--bs-primary);
    transition: color 0.3s ease;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .guest-notice .col-md-4 {
        text-align: center !important;
        margin-top: 10px;
    }
    
    .navbar-nav .nav-item {
        text-align: center;
    }
    
    .navbar-nav .ms-2 {
        margin-left: 0 !important;
        margin-top: 0.5rem;
    }
}
</style>

<script>
// Guest Menu JavaScript
document.addEventListener(`
- **Suggested key:** `ui.components.i_ng_k_ngay_a_div_div_div_div_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                                Tạo bài viết
                            </a>
                        </li>
                        @endif
                        @if(Route::has(`
- **Suggested key:** `ui.components.i_to_bi_vit_a_li_endif_ifroute`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                                Tạo showcase
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- Search -->
                <li class=`
- **Suggested key:** `ui.components.i_to_showcase_a_li_endif_ul_li`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Thông báo</h6></li>
                        @forelse($user->notifications()->limit(5)->get() as $notification)
                        <li>
                            <a class=`
- **Suggested key:** `ui.components.thng_boh6li_forelseusernotific`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                    <strong>Tài khoản Guest:</strong> Một số tính năng bị hạn chế. 
                    <a href=`
- **Suggested key:** `ui.components.i_strongti_khon_gueststrong_mt`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>
                        Nâng cấp tài khoản
                    </a>
                </small>
            </div>
            <div class=`
- **Suggested key:** `ui.components._nng_cp_ti_khon_a_small_div_di`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>
    <!-- Hướng dẫn viết bài -->
    <div class=`
- **Suggested key:** `ui.components._hng_dn_vit_bi_div_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quy tắc cộng đồng -->
    <div class=`
- **Suggested key:** `ui.components._p_div_div_div_div_div_div_quy`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) }}
                </a>
            </div>
        </div>
    </div>

    <!-- Các danh mục phổ biến -->
    <div class=`
- **Suggested key:** `ui.components._a_div_div_div_cc_danh_mc_ph_b`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `> @php
                // Cache các forum phổ biến trong 1 giờ để tối ưu hiệu suất
                $popularForums = Cache::remember(`
- **Suggested key:** `ui.components._php_cache_cc_forum_ph_bin_tro`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `, false) // Chỉ lấy forum công khai
                ->orderBy(`
- **Suggested key:** `ui.components._false_ch_ly_forum_cng_khai_or`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Hỗ trợ và trợ giúp -->
    <div class=`
- **Suggested key:** `ui.components._p_div_endforelse_div_div_div_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `) }}
                </a>
            </div>
        </div>
    </div>

    <!-- Thống kê cá nhân (nếu đã đăng nhập) -->
    @auth
    <div class=`
- **Suggested key:** `ui.components._a_div_div_div_thng_k_c_nhn_nu`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `)
    @param array $fileTypes - Các loại file được phép upload (default: [`
- **Suggested key:** `ui.components._param_array_filetypes_cc_loi_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `])
    @param string|int $maxSize - Dung lượng tối đa cho mỗi file (default:`
- **Suggested key:** `ui.components._param_stringint_maxsize_dung_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `)
    @param bool $multiple - Cho phép upload nhiều file (default: false)
    @param string|null $accept - MIME types được chấp nhận (auto-generate nếu null)
    @param bool $required - Trường bắt buộc (default: false)
    @param string|null $label - Label cho input (default: auto-generate)
    @param string|null $helpText - Text hướng dẫn (default: auto-generate)
    @param int $maxFiles - Số file tối đa khi multiple=true (default: 10)
    @param bool $showProgress - Hiển thị progress bar (default: true)
    @param bool $showPreview - Hiển thị preview file (default: true)
    @param bool $dragDrop - Cho phép drag & drop (default: true)
    @param string|null $id - ID của component (auto-generate nếu null)
--}}

@props([`
- **Suggested key:** `ui.components._param_bool_multiple_cho_php_u`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `=> null
])

@php
    // Generate unique ID nếu không được cung cấp
    $componentId = $id ??`
- **Suggested key:** `ui.components._null_php_generate_unique_id_n`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `. uniqid();
    
    // Generate accept attribute từ fileTypes nếu không được cung cấp
    if (!$accept) {
        $mimeTypes = [];
        foreach ($fileTypes as $type) {
            switch (strtolower($type)) {
                case`
- **Suggested key:** `ui.components._uniqid_generate_accept_attrib`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `;
                    break;
                default:
                    // Cho các file extension khác (CAD files, etc.)
                    $mimeTypes[] =`
- **Suggested key:** `ui.components._break_default_cho_cc_file_ext`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `, array_unique($mimeTypes));
    }
    
    // Parse maxSize thành bytes
    $maxSizeBytes = $maxSize;
    if (is_string($maxSize)) {
        $maxSize = strtoupper($maxSize);
        if (str_contains($maxSize,`
- **Suggested key:** `ui.components._arrayuniquemimetypes_parse_ma`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `, $maxSize) * 1024 * 1024 * 1024;
        } else {
            $maxSizeBytes = (int) $maxSize;
        }
    }
    
    // Generate label nếu không được cung cấp
    if (!$label) {
        $label = $multiple ? __(`
- **Suggested key:** `ui.components._maxsize_1024_1024_1024_else_m`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `;
        }
    }
    
    // Generate help text nếu không được cung cấp
    if (!$helpText) {
        $typesList = implode(`
- **Suggested key:** `ui.components._generate_help_text_nu_khng_c_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></div>
</div>

<!-- Include CSS và JavaScript -->
@once
    @push(`
- **Suggested key:** `ui.components.div_div_include_css_v_javascri`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `=> false])

@php
    use App\Services\MenuService;
    
    // Lấy menu component phù hợp cho user hiện tại
    $menuComponent = MenuService::getMenuComponent(auth()->user());
    $menuConfig = MenuService::getMenuConfiguration(auth()->user());
@endphp

<header class=`
- **Suggested key:** `ui.components._false_php_use_appservicesmenu`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                        Tìm kiếm
                    </h5>
                    <button type=`
- **Suggested key:** `ui.components.i_tm_kim_h5_button_type`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Nhập từ khóa tìm kiếm...`
- **Suggested key:** `ui.components.nhp_t_kha_tm_kim`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Tìm trong:</label>
                                <div class=`
- **Suggested key:** `ui.components.tm_tronglabel_div_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Bài viết</label>
                                </div>
                                <div class=`
- **Suggested key:** `ui.components.bi_vitlabel_div_div_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Sản phẩm</label>
                                </div>
                                <div class=`
- **Suggested key:** `ui.components.sn_phmlabel_div_div_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Người dùng</label>
                                </div>
                            </div>
                            <div class=`
- **Suggested key:** `ui.components.ngi_dnglabel_div_div_div_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Sắp xếp theo:</label>
                                <select class=`
- **Suggested key:** `ui.components.sp_xp_theolabel_select_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Độ liên quan</option>
                                    <option value=`
- **Suggested key:** `ui.components._lin_quanoption_option_value`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Mới nhất</option>
                                    <option value=`
- **Suggested key:** `ui.components.mi_nhtoption_option_value`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Cũ nhất</option>
                                    <option value=`
- **Suggested key:** `ui.components.c_nhtoption_option_value`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Phổ biến</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Quick Search Results -->
                    <div id=`
- **Suggested key:** `ui.components.ph_binoption_select_div_div_fo`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>
                        <h6>Kết quả nhanh:</h6>
                        <div class=`
- **Suggested key:** `ui.components._h6kt_qu_nhanhh6_div_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Đóng</button>
                    <button type=`
- **Suggested key:** `ui.components.ngbutton_button_type`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Tìm kiếm</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile Search Modal -->
    <div class=`
- **Suggested key:** `ui.components.tm_kimbutton_div_div_div_div_m`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Tìm kiếm</h5>
                    <button type=`
- **Suggested key:** `ui.components.tm_kimh5_button_type`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Tìm kiếm...`
- **Suggested key:** `ui.components.tm_kim`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Lỗi tìm kiếm</div>`
- **Suggested key:** `ui.components.li_tm_kimdiv`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Không tìm thấy kết quả</div>`
- **Suggested key:** `ui.components.khng_tm_thy_kt_qudiv`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `></i>
                Lỗi tải menu. Vui lòng tải lại trang.
                <button onclick=`
- **Suggested key:** `ui.components.i_li_ti_menu_vui_lng_ti_li_tra`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `Nhập nội dung của bạn...`
- **Suggested key:** `ui.components.nhp_ni_dung_ca_bn`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Đang tải...</span>
            </div>
            <span class=`
- **Suggested key:** `ui.components.ang_tispan_div_span_class`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

### Text: `>Đang khởi tạo editor...</span>
        </div>
    </div>
</div>

{{-- Push TinyMCE scripts to the end of the page --}}
@push(`
- **Suggested key:** `ui.components.ang_khi_to_editorspan_div_div_`
- **Helper function:** `t_ui()`
- **Blade directive:** `@ui()`

