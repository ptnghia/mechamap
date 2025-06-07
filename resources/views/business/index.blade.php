@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

<div class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm rounded-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3>{{ __('Expand Your Reach with Business Services') }}</h3>
                                <p class="lead">{{ __('Promote your business, connect with potential clients, and grow
                                    your network with our premium business services.') }}</p>
                                <a href="{{ route('business.services') }}" class="btn btn-primary">{{ __('Explore
                                    Services') }}</a>
                            </div>
                            <div class="col-md-4 text-center">
                                <img src="{{ placeholder_image(300, 200, 'Business Growth') }}" alt="Business Growth"
                                    class="img-fluid rounded">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm rounded-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up fs-1 text-primary mb-3"></i>
                        <h4>{{ __('Increase Visibility') }}</h4>
                        <p>{{ __('Get your business in front of thousands of potential customers with premium listings
                            and sponsored content.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm rounded-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-people fs-1 text-primary mb-3"></i>
                        <h4>{{ __('Build Connections') }}</h4>
                        <p>{{ __('Connect with industry professionals, potential clients, and partners to expand your
                            network.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm rounded-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-bar-chart fs-1 text-primary mb-3"></i>
                        <h4>{{ __('Track Performance') }}</h4>
                        <p>{{ __('Access detailed analytics to measure the impact of your business presence and optimize
                            your strategy.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Success Stories') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="https://ui-avatars.com/api/?name=Coteccons&size=64&background=0d6efd&color=fff&rounded=true"
                                            alt="Coteccons Logo" class="rounded-circle">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5>{{ __('Coteccons Group') }}</h5>
                                        <p class="mb-1">{{ __('Nền tảng MechaMap đã giúp chúng tôi kết nối với nhiều đối
                                            tác thiết kế và tư vấn chất lượng cao. Dự án Landmark 81 đã được hỗ trợ
                                            tuyệt vời từ cộng đồng chuyên gia trên đây.') }}</p>
                                        <small class="text-muted">{{ __('Nguyễn Bá Dương, Chủ tịch HĐQT') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="https://ui-avatars.com/api/?name=VinGroup&size=64&background=dc3545&color=fff&rounded=true"
                                            alt="VinGroup Logo" class="rounded-circle">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5>{{ __('Tập đoàn Vingroup') }}</h5>
                                        <p class="mb-1">{{ __('Thông qua MechaMap, chúng tôi đã tìm được những kiến trúc
                                            sư và kỹ sư tài năng cho các dự án VinCity. Cộng đồng chuyên nghiệp và năng
                                            động của nền tảng này thật sự ấn tượng.') }}</p>
                                        <small class="text-muted">{{ __('Phạm Nhật Vượng, Chủ tịch Tập đoàn') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection