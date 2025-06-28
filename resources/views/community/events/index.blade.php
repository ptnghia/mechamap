@extends('layouts.app')

@section('title', 'Events & Webinars')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-calendar-days text-primary me-2"></i>
                        Events & Webinars
                    </h1>
                    <p class="text-muted mb-0">Discover upcoming events, workshops, and networking opportunities</p>
                </div>
                <div class="d-flex gap-2">
                    @auth
                    <a href="{{ route('events.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-1"></i>
                        Create Event
                    </a>
                    @endauth
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-download me-1"></i>
                            Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('events.export', ['format' => 'csv']) }}">
                                <i class="fa-solid fa-file-csv me-2"></i>CSV Format
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('events.export', ['format' => 'json']) }}">
                                <i class="fa-solid fa-file-code me-2"></i>JSON Format
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('events.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search Events</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Search by title, description, or location...">
                        </div>
                        <div class="col-md-3">
                            <label for="type" class="form-label">Event Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All Types</option>
                                @foreach($eventTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                                <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-calendar-check text-primary mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">{{ $paginatedEvents->where('status', 'upcoming')->count() }}</h5>
                    <p class="card-text text-muted">Upcoming Events</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-users text-success mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">{{ $paginatedEvents->sum('attendees') }}</h5>
                    <p class="card-text text-muted">Total Attendees</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-video text-info mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">{{ $paginatedEvents->where('type', 'webinar')->count() }}</h5>
                    <p class="card-text text-muted">Webinars</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-handshake text-warning mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">{{ $paginatedEvents->where('type', 'networking')->count() }}</h5>
                    <p class="card-text text-muted">Networking</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="row">
        @forelse($paginatedEvents as $event)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 event-card">
                @if(isset($event['image']))
                <img src="{{ $event['image'] }}" class="card-img-top" alt="{{ $event['title'] }}" style="height: 200px; object-fit: cover;">
                @endif
                
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="badge bg-{{ $event['type'] == 'webinar' ? 'info' : ($event['type'] == 'conference' ? 'primary' : 'success') }}">
                        {{ $eventTypes[$event['type']] ?? ucfirst($event['type']) }}
                    </span>
                    <span class="badge bg-{{ $event['status'] == 'upcoming' ? 'success' : ($event['status'] == 'ongoing' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($event['status']) }}
                    </span>
                </div>
                
                <div class="card-body">
                    <h6 class="card-title">{{ $event['title'] }}</h6>
                    <p class="card-text text-muted small">{{ Str::limit($event['description'], 100) }}</p>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="fa-solid fa-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($event['date'])->format('M d, Y') }}
                            </small>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="fa-solid fa-clock me-1"></i>
                                {{ $event['time'] }}
                            </small>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="fa-solid fa-location-dot me-1"></i>
                                {{ $event['location'] }}
                            </small>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">Attendees</small>
                            <div class="fw-medium">{{ $event['attendees'] }}/{{ $event['max_attendees'] }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Price</small>
                            <div class="fw-medium">
                                @if($event['price'] == 0)
                                    <span class="text-success">Free</span>
                                @else
                                    {{ number_format($event['price']) }} {{ $event['currency'] }}
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="progress mb-2" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ ($event['attendees'] / $event['max_attendees']) * 100 }}%">
                        </div>
                    </div>
                    <small class="text-muted">
                        {{ round(($event['attendees'] / $event['max_attendees']) * 100) }}% filled
                    </small>
                </div>
                
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">by {{ $event['organizer'] }}</small>
                        <a href="{{ route('events.show', $event['id']) }}" class="btn btn-sm btn-outline-primary">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fa-solid fa-calendar-xmark text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">No events found</h4>
                <p class="text-muted">Try adjusting your search criteria or check back later for new events</p>
                <a href="{{ route('events.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-refresh me-1"></i>
                    Reset Filters
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Upcoming Events Sidebar -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fa-solid fa-clock me-2"></i>
                        Quick Upcoming Events
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($paginatedEvents->where('status', 'upcoming')->take(3) as $event)
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded text-center p-2" style="width: 50px;">
                                        <div style="font-size: 0.8rem;">{{ \Carbon\Carbon::parse($event['date'])->format('M') }}</div>
                                        <div class="fw-bold">{{ \Carbon\Carbon::parse($event['date'])->format('d') }}</div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ Str::limit($event['title'], 30) }}</h6>
                                    <small class="text-muted">{{ $event['location'] }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.event-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.event-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
@endsection
