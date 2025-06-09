@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Test Thread Actions Integration</h4>
                    <p class="mb-0 text-muted">Testing thread bookmark and follow functionality</p>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('info'))
                    <div class="alert alert-info">
                        {{ session('info') }}
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                    @if($threads->count() > 0)
                    <h5 class="mb-3">Test với các threads hiện có:</h5>

                    <div class="list-group">
                        @foreach($threads as $thread)
                        @include('partials.thread-item', [
                        'thread' => $thread,
                        'variant' => 'default',
                        'showBookmark' => true,
                        'showFollow' => true,
                        'showContentPreview' => true,
                        'showUserInfo' => true
                        ])
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <h6>Thông tin debug:</h6>
                        <ul class="list-unstyled small text-muted">
                            <li><strong>Authenticated:</strong> {{ Auth::check() ? 'Yes (' . Auth::user()->name . ')' :
                                'No' }}</li>
                            <li><strong>Total threads:</strong> {{ $threads->count() }}</li>
                            <li><strong>JavaScript loaded:</strong> thread-actions-simple.js</li>
                            <li><strong>Forms use:</strong> POST/DELETE với CSRF tokens</li>
                        </ul>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        Không có threads để test. Hãy tạo một số threads trước.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection