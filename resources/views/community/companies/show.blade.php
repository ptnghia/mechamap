@extends('layouts.app')

@section('title', $company->business_name . ' - ' . __('companies.company_profile'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/community/companies.css') }}">
@endpush

@section('full-width-content')
<!-- Company Profile Content -->
<div class="container mb-5 mt-4">
    <!-- Company Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2">
                    @if($company->store_logo)
                        <img src="{{ asset('storage/' . $company->store_logo) }}"
                                alt="{{ $company->business_name }}"
                                class="img-fluid rounded-circle"
                                style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 100px; height: 100px; font-size: 2rem;">
                            {{ substr($company->business_name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="col-md-8">
                    <h1 class="h3 mb-2">{{ $company->business_name }}</h1>
                    <p class="text-muted mb-2">{{ $company->business_description }}</p>
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-success me-2">
                            <i class="fas fa-check-circle"></i> {{ $company->verification_status_label }}
                        </span>
                        <span class="badge bg-info me-2">{{ $company->seller_type_label }}</span>
                        <span class="badge bg-secondary">{{ $company->business_type }}</span>
                    </div>
                    @if($company->rating_average > 0)
                        <div class="d-flex align-items-center">
                            <div class="text-warning me-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $company->rating_average)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-muted">{{ number_format($company->rating_average, 1) }} ({{ $company->rating_count }} {{ __('companies.reviews') }})</span>
                        </div>
                    @endif
                </div>
                <div class="col-md-2 text-end">
                    <div class="btn-group-vertical" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm mb-2" onclick="toggleFavorite({{ $company->id }})">
                            <i class="fas fa-heart"></i> Yêu thích
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm mb-2" onclick="shareCompany({{ $company->id }})">
                            <i class="fas fa-share"></i> Chia sẻ
                        </button>
                        <a href="{{ route('companies.products', $company) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Xem sản phẩm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-primary">{{ $stats['total_products'] }}</h4>
                    <p class="text-muted mb-0">Sản phẩm</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-success">{{ $stats['total_orders'] }}</h4>
                    <p class="text-muted mb-0">Đơn hàng</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-info">{{ $stats['response_rate'] }}%</h4>
                    <p class="text-muted mb-0">Tỷ lệ phản hồi</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-warning">{{ $stats['on_time_delivery'] }}%</h4>
                    <p class="text-muted mb-0">Giao hàng đúng hẹn</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Information -->
    <div class="row">
        <div class="col-md-8">
            <!-- About Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Giới thiệu doanh nghiệp</h5>
                </div>
                <div class="card-body">
                    <p>{{ $company->business_description ?: 'Chưa có mô tả về doanh nghiệp.' }}</p>

                    @if($company->specializations)
                        <h6>Chuyên môn:</h6>
                        <div class="mb-3">
                            @foreach($company->specializations as $spec)
                                <span class="badge bg-light text-dark me-1">{{ $spec }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if($company->certifications)
                        <h6>Chứng nhận:</h6>
                        <div class="mb-3">
                            @foreach($company->certifications as $cert)
                                <span class="badge bg-success me-1">{{ $cert }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Products -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sản phẩm mới nhất</h5>
                    <a href="{{ route('companies.products', $company) }}" class="btn btn-sm btn-outline-primary">
                        Xem tất cả
                    </a>
                </div>
                <div class="card-body">
                    @if($company->products && $company->products->count() > 0)
                        <div class="row">
                            @foreach($company->products->take(6) as $product)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        @if($product->featured_image)
                                            <img src="{{ asset($product->featured_image) }}"
                                                    class="card-img-top"
                                                    style="height: 150px; object-fit: cover;"
                                                    alt="{{ $product->name }}">
                                        @endif
                                        <div class="card-body p-2">
                                            <h6 class="card-title small">{{ Str::limit($product->name, 50) }}</h6>
                                            <p class="card-text small text-muted">{{ Str::limit($product->short_description, 80) }}</p>
                                            @if($product->price)
                                                <p class="text-primary fw-bold mb-0">{{ number_format($product->price) }} VNĐ</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Chưa có sản phẩm nào.</p>
                    @endif
                </div>
            </div>

            <!-- Reviews Section -->
            @if($reviews && $reviews->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Đánh giá từ khách hàng</h5>
                    </div>
                    <div class="card-body">
                        @foreach($reviews as $review)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <strong>{{ $review->user->name ?? 'Khách hàng' }}</strong>
                                    <div class="text-warning ms-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <p class="mb-1">{{ $review->content }}</p>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="col-md-4">
            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin liên hệ</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Người liên hệ:</strong><br>
                        {{ $company->contact_person_name }}
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong><br>
                        <a href="mailto:{{ $company->contact_email }}">{{ $company->contact_email }}</a>
                    </div>
                    <div class="mb-3">
                        <strong>Điện thoại:</strong><br>
                        <a href="tel:{{ $company->contact_phone }}">{{ $company->contact_phone }}</a>
                    </div>
                    @if($company->website_url)
                        <div class="mb-3">
                            <strong>Website:</strong><br>
                            <a href="{{ $company->website_url }}" target="_blank">{{ $company->website_url }}</a>
                        </div>
                    @endif
                    @if($company->business_address)
                        <div class="mb-3">
                            <strong>Địa chỉ:</strong><br>
                            @if(is_array($company->business_address))
                                {{ implode(', ', array_filter($company->business_address)) }}
                            @else
                                {{ $company->business_address }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Related Companies -->
            @if($relatedCompanies && $relatedCompanies->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Doanh nghiệp liên quan</h5>
                    </div>
                    <div class="card-body">
                        @foreach($relatedCompanies as $related)
                            <div class="d-flex align-items-center mb-3">
                                @if($related->store_logo)
                                    <img src="{{ asset('storage/' . $related->store_logo) }}"
                                            alt="{{ $related->business_name }}"
                                            class="rounded me-3"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                        {{ substr($related->business_name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <a href="{{ route('companies.show', $related) }}" class="text-decoration-none">
                                        <strong>{{ Str::limit($related->business_name, 30) }}</strong>
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $related->seller_type_label }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="favoriteToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Thông báo</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <!-- Message will be inserted here -->
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleFavorite(companyId) {
    fetch(`/companies/${companyId}/favorite`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        showToast(data.message);
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Có lỗi xảy ra. Vui lòng thử lại.');
    });
}

function shareCompany(companyId) {
    const url = window.location.href;
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: url
        });
    } else {
        navigator.clipboard.writeText(url).then(() => {
            showToast('Đã sao chép liên kết vào clipboard!');
        });
    }
}

function showToast(message) {
    const toastElement = document.getElementById('favoriteToast');
    const toastBody = toastElement.querySelector('.toast-body');
    toastBody.textContent = message;

    const toast = new bootstrap.Toast(toastElement);
    toast.show();
}
</script>
@endpush
