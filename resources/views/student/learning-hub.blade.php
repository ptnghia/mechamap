@extends('layouts.app')

@section('title', __('student.learning_hub'))

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('student.learning_hub') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('nav.home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('student.learning_hub') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title">{{ __('student.welcome_message') }}</h5>
                            <p class="card-text">{{ __('student.hub_description') }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <img src="{{ asset('images/student-learning.svg') }}" alt="Learning" class="img-fluid" style="max-height: 120px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">{{ __('student.courses_enrolled') }}</p>
                            <h4 class="mb-0">{{ $stats['courses_enrolled'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="bx bx-book-open font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">{{ __('student.completed_lessons') }}</p>
                            <h4 class="mb-0">{{ $stats['completed_lessons'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="bx bx-check-circle font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">{{ __('student.study_hours') }}</p>
                            <h4 class="mb-0">{{ $stats['study_hours'] ?? 0 }}h</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="bx bx-time font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">{{ __('student.achievements') }}</p>
                            <h4 class="mb-0">{{ $stats['achievements'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="bx bx-trophy font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Educational Resources -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('student.educational_resources') }}</h5>
                        <a href="{{ route('student.resources.index') }}" class="btn btn-sm btn-primary">
                            {{ __('common.buttons.view_all') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Resource Categories -->
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <i class="bx bx-book font-size-24 text-primary mb-2"></i>
                                    <h6>{{ __('student.textbooks') }}</h6>
                                    <p class="text-muted small">{{ $resourceCounts['textbooks'] ?? 0 }} {{ __('student.resources') }}</p>
                                    <a href="{{ route('student.resources.category', 'textbooks') }}" class="btn btn-sm btn-outline-primary">
                                        {{ __('common.buttons.explore') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <i class="bx bx-video font-size-24 text-success mb-2"></i>
                                    <h6>{{ __('student.video_tutorials') }}</h6>
                                    <p class="text-muted small">{{ $resourceCounts['videos'] ?? 0 }} {{ __('student.resources') }}</p>
                                    <a href="{{ route('student.resources.category', 'videos') }}" class="btn btn-sm btn-outline-success">
                                        {{ __('common.buttons.explore') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <i class="bx bx-file font-size-24 text-warning mb-2"></i>
                                    <h6>{{ __('student.research_papers') }}</h6>
                                    <p class="text-muted small">{{ $resourceCounts['papers'] ?? 0 }} {{ __('student.resources') }}</p>
                                    <a href="{{ route('student.resources.category', 'papers') }}" class="btn btn-sm btn-outline-warning">
                                        {{ __('common.buttons.explore') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <i class="bx bx-wrench font-size-24 text-info mb-2"></i>
                                    <h6>{{ __('student.tools_software') }}</h6>
                                    <p class="text-muted small">{{ $resourceCounts['tools'] ?? 0 }} {{ __('student.resources') }}</p>
                                    <a href="{{ route('student.resources.category', 'tools') }}" class="btn btn-sm btn-outline-info">
                                        {{ __('common.buttons.explore') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Resources -->
                    <div class="mt-4">
                        <h6>{{ __('student.recent_resources') }}</h6>
                        <div class="list-group">
                            @forelse($recentResources ?? [] as $resource)
                            <a href="{{ route('student.resources.show', $resource->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $resource->title }}</h6>
                                    <small>{{ $resource->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ Str::limit($resource->description, 100) }}</p>
                                <small class="text-muted">{{ $resource->category }} • {{ $resource->downloads }} {{ __('student.downloads') }}</small>
                            </a>
                            @empty
                            <div class="text-center py-4">
                                <i class="bx bx-book-open font-size-48 text-muted"></i>
                                <p class="text-muted mt-2">{{ __('student.no_recent_resources') }}</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Learning Progress -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('student.learning_progress') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>{{ __('student.overall_progress') }}</span>
                            <span>{{ $progress['overall'] ?? 0 }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $progress['overall'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>{{ __('student.current_course') }}</span>
                            <span>{{ $progress['current_course'] ?? 0 }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress['current_course'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <a href="{{ route('student.learning-path') }}" class="btn btn-primary btn-sm w-100">
                        {{ __('student.view_learning_path') }}
                    </a>
                </div>
            </div>

            <!-- Study Groups -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('student.study_groups') }}</h5>
                        <a href="{{ route('student.study-groups.create') }}" class="btn btn-sm btn-outline-primary">
                            {{ __('common.buttons.create') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($studyGroups ?? [] as $group)
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-sm me-3">
                            <div class="avatar-title rounded-circle bg-primary">
                                {{ substr($group->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <h6 class="mb-1">{{ $group->name }}</h6>
                            <p class="text-muted small mb-0">{{ $group->members_count }} {{ __('student.members') }}</p>
                        </div>
                        <a href="{{ route('student.study-groups.show', $group->id) }}" class="btn btn-sm btn-outline-primary">
                            {{ __('common.buttons.join') }}
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-3">
                        <i class="bx bx-group font-size-36 text-muted"></i>
                        <p class="text-muted mt-2">{{ __('student.no_study_groups') }}</p>
                        <a href="{{ route('student.study-groups.index') }}" class="btn btn-sm btn-primary">
                            {{ __('student.find_groups') }}
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('student.quick_actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.projects.create') }}" class="btn btn-outline-primary">
                            <i class="bx bx-plus me-2"></i>{{ __('student.new_project') }}
                        </a>
                        <a href="{{ route('student.assignments.index') }}" class="btn btn-outline-success">
                            <i class="bx bx-task me-2"></i>{{ __('student.assignments') }}
                        </a>
                        <a href="{{ route('student.calendar') }}" class="btn btn-outline-info">
                            <i class="bx bx-calendar me-2"></i>{{ __('student.study_calendar') }}
                        </a>
                        <a href="{{ route('student.help') }}" class="btn btn-outline-warning">
                            <i class="bx bx-help-circle me-2"></i>{{ __('student.get_help') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
