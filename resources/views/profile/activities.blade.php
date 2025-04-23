@extends('layouts.app')

@section('title', $user->name . ' - ' . __('Activities'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">{{ $user->name }} - {{ __('Activities') }}</h1>
                <a href="{{ route('profile.show', $user) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> {{ __('Back to Profile') }}
                </a>
            </div>
            
            @include('profile.partials.activity-section', ['user' => $user, 'activities' => $activities, 'showAll' => true])
        </div>
    </div>
</div>
@endsection
