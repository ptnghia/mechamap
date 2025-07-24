@extends('layouts.app')

@section('title', 'My Threads')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1>My Threads - Simple Test</h1>
            
            <div class="card">
                <div class="card-body">
                    <h5>Test Page</h5>
                    <p>This is a simple test page to check if the basic structure works.</p>
                    
                    @if(isset($threads) && $threads->count() > 0)
                        <h6>Threads Found: {{ $threads->count() }}</h6>
                        <ul>
                            @foreach($threads as $thread)
                                <li>{{ $thread->title ?? 'No title' }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>No threads found or threads variable not set.</p>
                    @endif
                    
                    @if(isset($stats))
                        <h6>Stats:</h6>
                        <ul>
                            @foreach($stats as $key => $value)
                                <li>{{ $key }}: {{ $value }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>Stats not available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
