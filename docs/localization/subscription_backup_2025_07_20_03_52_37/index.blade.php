@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm rounded-3">
                        <div class="card-body">
                            <h3 class="text-center mb-4">{{ __('Choose Your Plan') }}</h3>
                            <p class="text-center mb-5">{{ __('Upgrade your account to unlock premium features and enhance your experience.') }}</p>
                            
                            <div class="row">
                                @foreach($plans as $plan)
                                    <div class="col-md-4 mb-4">
                                        <div class="card h-100 shadow-sm {{ $currentSubscription && $currentSubscription->plan_id === $plan['id'] ? 'border-primary' : '' }}">
                                            <div class="card-header {{ $currentSubscription && $currentSubscription->plan_id === $plan['id'] ? 'bg-primary text-white' : 'bg-light' }} text-center py-3">
                                                <h5 class="mb-0">{{ $plan['name'] }}</h5>
                                                @if($currentSubscription && $currentSubscription->plan_id === $plan['id'])
                                                    <span class="badge bg-light text-primary mt-2">{{ __('Current Plan') }}</span>
                                                @endif
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <div class="text-center mb-4">
                                                    <span class="display-6">${{ number_format($plan['price'], 2) }}</span>
                                                    <span class="text-muted">/ {{ $plan['duration'] }}</span>
                                                </div>
                                                <ul class="list-group list-group-flush mb-4 flex-grow-1">
                                                    @foreach($plan['features'] as $feature)
                                                        <li class="list-group-item border-0">
                                                            <i class="fas fa-check-circle-fill text-success me-2"></i> {{ $feature }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                
                                                @if($currentSubscription && $currentSubscription->plan_id === $plan['id'])
                                                    <div class="text-center mt-auto">
                                                        <p class="text-muted mb-2">{{ __('Expires') }}: {{ $currentSubscription->expires_at->format('M d, Y') }}</p>
                                                        <a href="{{ route('subscription.cancel') }}" class="btn btn-outline-danger">{{ __('Cancel Subscription') }}</a>
                                                    </div>
                                                @else
                                                    <form action="{{ route('subscription.store') }}" method="POST" class="mt-auto">
                                                        @csrf
                                                        <input type="hidden" name="plan_id" value="{{ $plan['id'] }}">
                                                        <button type="submit" class="btn btn-primary w-100">
                                                            @if($currentSubscription)
                                                                {{ __('Switch Plan') }}
                                                            @else
                                                                {{ __('Subscribe Now') }}
                                                            @endif
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm rounded-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Subscription Benefits') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-shield-alt-check fs-1 text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5>{{ __('Ad-Free Experience') }}</h5>
                                            <p>{{ __('Enjoy browsing without any advertisements or distractions.') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-comment-square-dots fs-1 text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5>{{ __('Unlimited Messages') }}</h5>
                                            <p>{{ __('Send unlimited private messages to other users.') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="person-badge fs-1 text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5>{{ __('Premium Badge') }}</h5>
                                            <p>{{ __('Get a special badge that shows your premium status.') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="headset fs-1 text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5>{{ __('Priority Support') }}</h5>
                                            <p>{{ __('Get faster responses from our support team.') }}</p>
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
