@extends('layouts.app')

@section('title', 'Danh sách thành viên')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Danh sách thành viên</h1>
                <div>
                    <span class="text-muted">{{ $users->total() }} thành viên</span>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('users.index') }}" method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Tên hoặc username...">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="role" class="form-label">Vai trò</label>
                            <select class="form-select" id="role" name="role">
                                <option value="">Tất cả vai trò</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="moderator" {{ request('role') == 'moderator' ? 'selected' : '' }}>Moderator</option>
                                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Thành viên</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="sort" class="form-label">Sắp xếp</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Theo tên</option>
                                <option value="posts" {{ request('sort') == 'posts' ? 'selected' : '' }}>Số bài viết</option>
                                <option value="threads" {{ request('sort') == 'threads' ? 'selected' : '' }}>Số chủ đề</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Lọc</button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Đặt lại</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Users List -->
            <div class="card">
                <div class="list-group list-group-flush">
                    @forelse($users as $user)
                        <div class="list-group-item p-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="rounded-circle" width="64" height="64">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="mb-1">
                                                <a href="{{ route('profile.show', $user->username) }}" class="text-decoration-none">{{ $user->name }}</a>
                                                @if($user->hasRole('admin'))
                                                    <span class="badge bg-danger ms-1">Admin</span>
                                                @elseif($user->hasRole('moderator'))
                                                    <span class="badge bg-success ms-1">Moderator</span>
                                                @endif
                                            </h5>
                                            <div class="text-muted small">
                                                <span>@{{ $user->username }}</span>
                                                <span class="mx-1">•</span>
                                                <span>Tham gia {{ $user->created_at->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            @auth
                                                @if(Auth::id() !== $user->id)
                                                    @if(Auth::user()->isFollowing($user))
                                                        <form action="{{ route('profile.unfollow', $user->username) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                                <i class="person-dash"></i> Bỏ theo dõi
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('profile.follow', $user->username) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-user-plus"></i> Theo dõi
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            @endauth
                                        </div>
                                    </div>
                                    
                                    @if($user->about_me)
                                        <p class="text-muted small mt-2 mb-2">{{ Str::limit($user->about_me, 150) }}</p>
                                    @endif
                                    
                                    <div class="d-flex mt-2">
                                        <div class="me-3">
                                            <i class="fas fa-comment-left-text"></i>
                                            <span>{{ $user->posts_count }} bài viết</span>
                                        </div>
                                        <div class="me-3">
                                            <i class="fas fa-file-alt"></i>
                                            <span>{{ $user->threads_count }} chủ đề</span>
                                        </div>
                                        @if($user->location)
                                            <div>
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span>{{ $user->location }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item p-4 text-center">
                            <p class="mb-0">Không tìm thấy thành viên nào phù hợp với tiêu chí tìm kiếm.</p>
                        </div>
                    @endforelse
                </div>
                
                <div class="card-footer">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mt-4 mt-lg-0">
            <!-- Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thống kê</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tổng số thành viên:</span>
                        <span class="fw-bold">{{ App\Models\User::count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Thành viên mới nhất:</span>
                        @php
                            $newestUser = App\Models\User::latest()->first();
                        @endphp
                        <span class="fw-bold">{{ $newestUser ? $newestUser->name : 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Thành viên online:</span>
                        <span class="fw-bold">{{ App\Models\User::where('last_active_at', '>=', now()->subMinutes(15))->count() }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Top Contributors -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Top đóng góp tháng này</h5>
                </div>
                <div class="list-group list-group-flush">
                    @php
                        $topContributors = App\Models\User::withCount(['threads' => function($query) {
                            $query->where('created_at', '>=', now()->subMonth());
                        }, 'comments' => function($query) {
                            $query->where('created_at', '>=', now()->subMonth());
                        }])
                        ->orderByRaw('threads_count + comments_count DESC')
                        ->take(5)
                        ->get();
                    @endphp
                    
                    @forelse($topContributors as $user)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="rounded-circle me-2" width="32" height="32">
                                <a href="{{ route('profile.show', $user->username) }}" class="text-decoration-none">{{ $user->name }}</a>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $user->threads_count + $user->comments_count }} bài viết</span>
                        </div>
                    @empty
                        <div class="list-group-item text-center">Không có dữ liệu</div>
                    @endforelse
                </div>
            </div>
            
            <!-- Staff Members -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Ban quản trị</h5>
                </div>
                <div class="list-group list-group-flush">
                    @php
                        $staffMembers = App\Models\User::role(['admin', 'moderator'])->take(5)->get();
                    @endphp
                    
                    @forelse($staffMembers as $user)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="rounded-circle me-2" width="32" height="32">
                                <div>
                                    <a href="{{ route('profile.show', $user->username) }}" class="text-decoration-none">{{ $user->name }}</a>
                                    <div class="small text-muted">
                                        @if($user->hasRole('admin'))
                                            <span class="text-danger">Admin</span>
                                        @elseif($user->hasRole('moderator'))
                                            <span class="text-success">Moderator</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center">Không có dữ liệu</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
