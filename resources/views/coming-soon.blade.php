@extends('layouts.app')

@section('title', $title . ' - ' . config('app.name'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/coming-soon.css') }}">
@endpush

@section('content')
<div class="mechanical-bg min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full">
        <div class="glass-card p-8 lg:p-12 text-center">
            <!-- Animated Icon -->
            <div class="mx-auto h-32 w-32 flex items-center justify-center rounded-full mechanical-icon floating-animation mb-8">
                <svg class="h-16 w-16 text-white gear-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>

            <!-- Title & Message -->
            <div class="mb-8">
                <h1 class="text-4xl lg:text-5xl font-bold text-white mb-4">
                    {{ $title }}
                </h1>
                <p class="text-xl lg:text-2xl text-white/90 mb-4">
                    {{ $message }}
                </p>
                <p class="text-white/70 text-lg">
                    🔧 Chúng tôi đang xây dựng tính năng mới dành cho cộng đồng kỹ sư cơ khí
                </p>
            </div>

            <!-- Progress Section -->
            <div class="mb-10">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-white/80 font-medium">Tiến độ phát triển</span>
                    <span class="text-white font-bold">75%</span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-4 mb-2">
                    <div class="progress-bar h-4 rounded-full" style="width: 75%"></div>
                </div>
                <p class="text-white/60 text-sm">🚀 Dự kiến hoàn thành trong tháng này</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-10">
                <a href="{{ route('home') }}"
                   class="btn-mechanical inline-flex items-center px-8 py-4 text-lg font-medium rounded-xl">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Về Trang Chủ
                </a>

                <a href="{{ route('marketplace.index') }}"
                   class="inline-flex items-center px-8 py-4 text-lg font-medium rounded-xl bg-white/20 text-white hover:bg-white/30 transition-all">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h2M9 7h6m-6 4h6m-6 4h6"/>
                    </svg>
                    Marketplace
                </a>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="glass-card p-6">
                    <div class="feature-icon h-12 w-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-lg mb-2">Công nghệ tiên tiến</h3>
                    <p class="text-white/80 text-sm">Ứng dụng công nghệ AI và IoT trong thiết kế cơ khí</p>
                </div>

                <div class="glass-card p-6">
                    <div class="feature-icon h-12 w-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-lg mb-2">Cộng đồng chuyên nghiệp</h3>
                    <p class="text-white/80 text-sm">Kết nối với 10,000+ kỹ sư cơ khí hàng đầu Việt Nam</p>
                </div>

                <div class="glass-card p-6">
                    <div class="feature-icon h-12 w-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-lg mb-2">Hiệu suất cao</h3>
                    <p class="text-white/80 text-sm">Tối ưu hóa quy trình thiết kế và sản xuất</p>
                </div>
            </div>

            <!-- Newsletter Signup -->
            <div class="glass-card p-8 mb-8">
                <h3 class="text-2xl font-bold text-white mb-3">
                    🔔 Nhận thông báo khi tính năng ra mắt
                </h3>
                <p class="text-white/80 mb-6">
                    Đăng ký để trở thành người đầu tiên trải nghiệm tính năng mới!
                </p>
                <div class="max-w-md mx-auto flex gap-2">
                    <input
                        type="email"
                        placeholder="email@example.com"
                        class="newsletter-input flex-1 px-4 py-3 rounded-xl bg-white/20 text-white placeholder-white/60 border border-white/30 focus:outline-none focus:border-white transition-all"
                    >
                    <button class="btn-mechanical px-6 py-3 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Social Links -->
            <div class="text-center">
                <p class="text-white/70 mb-4">Theo dõi tiến độ phát triển:</p>
                <div class="flex justify-center space-x-6">
                    <a href="#" class="social-icon text-white/60 hover:text-white transition-all transform">
                        <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-icon text-white/60 hover:text-white transition-all transform">
                        <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-icon text-white/60 hover:text-white transition-all transform">
                        <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
