@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm rounded-3">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Welcome') }}</h5>
                            <p class="card-text">{{ __("You're logged in!") }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
