@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm rounded-3">
                        <div class="card-body">
                            <h3 class="text-center mb-4">{{ __('business.choose_service_title') }}</h3>
                            <p class="text-center mb-5">{{ __('business.choose_service_subtitle') }}</p>

                            <div class="row">
                                @foreach($services as $service)
                                    <div class="col-md-6 col-lg-3 mb-4">
                                        <div class="card h-100 shadow-sm">
                                            <div class="card-header bg-primary text-white text-center py-3">
                                                <h5 class="mb-0">{{ $service['name'] }}</h5>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <div class="text-center mb-4">
                                                    <span class="display-6">${{ number_format($service['price'], 2) }}</span>
                                                    <span class="text-muted">/ {{ $service['duration'] }}</span>
                                                </div>
                                                <p class="card-text flex-grow-1">{{ $service['description'] }}</p>
                                                <a href="{{ route('subscription.index') }}" class="btn btn-outline-primary mt-auto">{{ __('ui.get_started') }}</a>
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
                            <h5 class="card-title mb-0">{{ __('business.faq_title') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="faqAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            {{ __('business.faq_premium_listings') }}
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            {{ __('Premium listings appear at the top of search results and category pages, giving your business maximum visibility. They also include enhanced visual elements to make your listing stand out from the competition.') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            {{ __('business.faq_cancel_subscription') }}
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            {{ __('Yes, you can cancel your subscription at any time. Your services will continue until the end of your current billing period.') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            {{ __('business.faq_analytics') }}
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            {{ __('Our analytics dashboard provides detailed insights into views, clicks, inquiries, and engagement metrics. You\'ll be able to track performance over time and see which aspects of your business presence are generating the most interest.') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingFour">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                            {{ __('business.faq_custom_packages') }}
                                        </button>
                                    </h2>
                                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            {{ __('Yes, we offer custom enterprise packages for larger businesses with specific needs. Please contact our sales team to discuss your requirements and get a tailored solution.') }}
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
