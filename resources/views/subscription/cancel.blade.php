@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm rounded-3">
                        <div class="card-body p-5">
                            <h3 class="mb-4 text-center">{{ __('subscription.sorry_to_see_you_go') }}</h3>
                            <p class="lead text-center mb-5">{{ __('subscription.confirm_cancel_question') }}</p>

                            <div class="alert alert-info mb-4">
                                <h5 class="alert-heading">{{ __('subscription.what_happens_when_cancel') }}</h5>
                                <ul class="mb-0">
                                    <li>{{ __('subscription.continue_access_until_end') }}</li>
                                    <li>{{ __('subscription.no_auto_renewal') }}</li>
                                    <li>{{ __('subscription.can_resubscribe_anytime') }}</li>
                                </ul>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('subscription.current_subscription_details') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>{{ __('subscription.plan') }}:</strong> {{ Auth::user()->subscription->getPlanDetails()['name'] ?? 'Unknown' }}</p>
                                            <p class="mb-1"><strong>{{ __('subscription.status') }}:</strong> {{ ucfirst(Auth::user()->subscription->status) }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>{{ __('subscription.billing_period_ends') }}:</strong> {{ Auth::user()->subscription->expires_at->format('M d, Y') }}</p>
                                            <p class="mb-1"><strong>{{ __('subscription.price') }}:</strong> ${{ number_format(Auth::user()->subscription->getPlanDetails()['price'] ?? 0, 2) }}/{{ __('subscription.month') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5>{{ __('subscription.tell_us_why_cancelling') }}</h5>
                                <form id="cancellation-form">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cancellation_reason" id="reason1" value="too_expensive">
                                            <label class="form-check-label" for="reason1">
                                                {{ __('subscription.too_expensive') }}
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cancellation_reason" id="reason2" value="not_using">
                                            <label class="form-check-label" for="reason2">
                                                {{ __('subscription.not_using_premium_features') }}
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cancellation_reason" id="reason3" value="missing_features">
                                            <label class="form-check-label" for="reason3">
                                                {{ __('subscription.missing_features_needed') }}
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cancellation_reason" id="reason4" value="switching">
                                            <label class="form-check-label" for="reason4">
                                                {{ __('subscription.switching_to_another_service') }}
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cancellation_reason" id="reason5" value="other">
                                            <label class="form-check-label" for="reason5">
                                                {{ __('subscription.other_reason') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="feedback" class="form-label">{{ __('subscription.additional_feedback_optional') }}</label>
                                        <textarea class="form-control" id="feedback" rows="3"></textarea>
                                    </div>
                                </form>
                            </div>

                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ route('subscription.index') }}" class="btn btn-outline-secondary">{{ __('subscription.keep_my_subscription') }}</a>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmCancelModal">
                                    {{ __('subscription.cancel_subscription') }}
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
                    <h5 class="modal-title" id="confirmCancelModalLabel">{{ __('subscription.confirm_cancellation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('subscription.absolutely_sure_cancel') }}</p>
                    <p>{{ __('subscription.lose_access_premium_features') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('subscription.no_keep_subscription') }}</button>
                    <form action="{{ route('subscription.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ Auth::user()->subscription->plan_id }}">
                        <input type="hidden" name="cancel" value="true">
                        <button type="submit" class="btn btn-danger">{{ __('subscription.yes_cancel_subscription') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
