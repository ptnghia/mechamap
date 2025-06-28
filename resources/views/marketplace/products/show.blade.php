@extends('layouts.unified')

@section('title', $product->name . ' - Marketplace')

@section('content')
<div class="min-vh-100 bg-light">
    <!-- Breadcrumb & Page Title -->
    <div class="bg-white border-bottom">
        <div class="container-fluid py-4">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <i class="bi bi-house me-2"></i>
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('marketplace.index') }}" class="text-decoration-none">Marketplace</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('marketplace.products.index') }}" class="text-decoration-none">Products</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($product->name, 30) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Product Details -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Images -->
            <div>
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    @if($product->featured_image)
                        <img src="{{ $product->featured_image }}" alt="{{ $product->name }}" class="w-full h-96 object-cover rounded-lg">
                    @else
                        <div class="w-full h-96 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="bi bi-image text-gray-400 text-6xl"></i>
                        </div>
                    @endif

                    <!-- Additional Images -->
                    @if($product->images && count($product->images) > 1)
                        <div class="grid grid-cols-4 gap-2 mt-4">
                            @foreach(array_slice($product->images, 1, 4) as $image)
                                <img src="{{ $image }}" alt="{{ $product->name }}" class="w-full h-20 object-cover rounded border cursor-pointer hover:opacity-75">
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div>
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <!-- Product Title -->
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>

                    <!-- Seller Info -->
                    <div class="flex items-center mb-4">
                        <span class="text-sm text-gray-600">Sold by</span>
                        <a href="{{ route('marketplace.sellers.show', $product->seller->store_slug) }}" class="ml-2 text-blue-600 hover:text-blue-800 font-medium">
                            {{ $product->seller->business_name ?? $product->seller->user->name }}
                        </a>
                        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $product->seller->verification_status === 'verified' ? 'green' : 'gray' }}-100 text-{{ $product->seller->verification_status === 'verified' ? 'green' : 'gray' }}-800">
                            {{ ucfirst($product->seller->verification_status) }}
                        </span>
                    </div>

                    <!-- Rating -->
                    @if($product->rating_average > 0)
                        <div class="flex items-center mb-4">
                            <div class="flex text-yellow-400 mr-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $product->rating_average)
                                        <i class="bi bi-star-fill"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-sm text-gray-600">({{ $product->rating_count }} reviews)</span>
                        </div>
                    @endif

                    <!-- Price -->
                    <div class="mb-6">
                        @if($product->is_on_sale && $product->sale_price)
                            <div class="flex items-center">
                                <span class="text-3xl font-bold text-red-600">${{ number_format($product->sale_price, 2) }}</span>
                                <span class="text-lg text-gray-500 line-through ml-3">${{ number_format($product->price, 2) }}</span>
                                <span class="ml-3 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}% OFF
                                </span>
                            </div>
                        @else
                            <span class="text-3xl font-bold text-blue-600">${{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>

                    <!-- Stock Status -->
                    <div class="mb-6">
                        @if($product->in_stock)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="bi bi-check-circle mr-1"></i>
                                In Stock
                            </span>
                            @if($product->stock_quantity <= 5)
                                <span class="ml-2 text-sm text-orange-600">Only {{ $product->stock_quantity }} left!</span>
                            @endif
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="bi bi-x-circle mr-1"></i>
                                Out of Stock
                            </span>
                        @endif
                    </div>

                    <!-- Product Type & Category -->
                    <div class="mb-6">
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($product->product_type) }}
                            </span>
                            @if($product->category)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $product->category->name }}
                                </span>
                            @endif
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ ucfirst($product->seller_type) }}
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        @if($product->in_stock)
                            <button class="w-full bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 font-medium">
                                <i class="bi bi-cart-plus mr-2"></i>
                                Add to Cart
                            </button>
                        @else
                            <button class="w-full bg-gray-300 text-gray-500 py-3 px-6 rounded-md cursor-not-allowed font-medium" disabled>
                                Out of Stock
                            </button>
                        @endif

                        <button class="w-full bg-gray-100 text-gray-700 py-3 px-6 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 font-medium">
                            <i class="bi bi-heart mr-2"></i>
                            Add to Wishlist
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Product Description</h2>
                <div class="prose max-w-none">
                    {!! nl2br(e($product->description)) !!}
                </div>

                <!-- Technical Specifications -->
                @if($product->technical_specs)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Technical Specifications</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($product->technical_specs as $key => $value)
                                <div class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                    <span class="text-gray-600">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mt-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Related Products</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow duration-200">
                            <div class="relative">
                                @if($relatedProduct->featured_image)
                                    <img src="{{ $relatedProduct->featured_image }}" class="w-full h-48 object-cover rounded-t-lg" alt="{{ $relatedProduct->name }}">
                                @else
                                    <div class="w-full h-48 bg-gray-100 rounded-t-lg flex items-center justify-center">
                                        <i class="bi bi-image text-gray-400 text-4xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-2">
                                    <a href="{{ route('marketplace.products.show', $relatedProduct->slug) }}" class="hover:text-blue-600">
                                        {{ Str::limit($relatedProduct->name, 50) }}
                                    </a>
                                </h3>
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-bold text-blue-600">${{ number_format($relatedProduct->price, 2) }}</span>
                                    <button class="bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
