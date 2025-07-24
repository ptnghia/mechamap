@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm rounded-3">
                        <div class="card-body p-5">
                            <h3 class="mb-4 text-center">{{ __('We\'re Sorry to See You Go') }}</h3>
                            <p class="lead text-center mb-5">{{ __('Are you sure you want to cancel your subscription?') }}</p>
                            
                            <div class="alert alert-info mb-4">
                                <h5 class="alert-heading">{{ __('What Happens When You Cancel') }}</h5>
                                <ul class="mb-0">
                                    <li>{{ __('You will continue to have access to premium features until the end of your current billing period.') }}</li>
                                    <li>{{ __('Your subscription will not renew automatically.') }}</li>
                                    <li>{{ __('You can resubscribe at any time.') }}</li>
                                </ul>
                            </div>
                            
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('Current Subscription Details') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>{{ __('Plan') }}:</strong> {{ Auth::user()->subscription->getPlanDetails()['name'] ?? 'Unknown' }}</p>
                                            <p class="mb-1"><strong>{{ __('Status') }}:</strong> {{ ucfirst(Auth::user()->subscription->status) }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>{{ __('Billing Period Ends') }}:</strong> {{ Auth::user()->subscription->expires_at->format('M d, Y') }}</p>
                                            <p class="mb-1"><strong>{{ __('Price') }}:</strong> ${{ number_format(Auth::user()->subscription->getPlanDetails()['price'] ?? 0, 2) }}/{{ __('month') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h5>{{ __('Please tell us why you\'re cancelling') }}</h5>
                                <form id="cancellation-form">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cancellation_reason" id="reason1" value="too_expensive">
                                            <label class="form-check-label" for="reason1">
                                                {{ __('Too expensive') }}
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cancellation_reason" id="reason2" value="not_using">
                                            <label class="form-check-label" for="reason2">
                                                {{ __('Not using the premium features') }}
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cancellation_reason" id="reason3" value="missing_features">
                                            <label class="form-check-label" for="reason3">
                                                {{ __('Missing features I need') }}
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cancellation_reason" id="reason4" value="switching">
                                            <label class="form-check-label" for="reason4">
                                                {{ __('Switching to another service') }}
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cancellation_reason" id="reason5" value="other">
                                            <label class="form-check-label" for="reason5">
                                                {{ __('Other reason') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="feedback" class="form-label">{{ __('Additional feedback (optional)') }}</label>
                                        <textarea class="form-control" id="feedback" rows="3"></textarea>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ route('subscription.index') }}" class="btn btn-outline-secondary">{{ __('Keep My Subscription') }}</a>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmCancelModal">
                                    {{ __('Cancel Subscription') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmCancelModal" tabindex="-1" aria-labelledby="confirmCancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmCancelModalLabel">{{ __('Confirm Cancellation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Are you absolutely sure you want to cancel your subscription?') }}</p>
                    <p>{{ __('You will lose access to premium features at the end of your current billing period.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('No, Keep My Subscription') }}</button>
                    <form action="{{ route('subscription.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ Auth::user()->subscription->plan_id }}">
                        <input type="hidden" name="cancel" value="true">
                        <button type="submit" class="btn btn-danger">{{ __('Yes, Cancel My Subscription') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
