@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm rounded-3">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                            </div>
                            <h3 class="mb-4">{{ __('Thank You for Your Subscription!') }}</h3>
                            <p class="lead mb-4">{{ __('Your account has been successfully upgraded.') }}</p>
                            <p class="mb-5">{{ __('You now have access to all premium features included in your subscription plan.') }}</p>
                            
                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('Go to Dashboard') }}</a>
                                <a href="{{ route('profile.show', Auth::user()->username) }}" class="btn btn-outline-primary">{{ __('View Your Profile') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
