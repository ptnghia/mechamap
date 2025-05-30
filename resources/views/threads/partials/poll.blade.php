@if($thread->hasPoll())
    @php
        $poll = $thread->poll()->first();
        $options = $poll->options;
        $hasVoted = Auth::check() && $poll->hasVoted(Auth::user());
        $canViewResults = $poll->allow_view_without_vote || $hasVoted;
        $isClosed = $poll->isClosed();
    @endphp

    <div class="card mb-4 poll-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $poll->question }}</h5>
            @if($isClosed)
                <span class="badge bg-secondary">Closed</span>
            @elseif($poll->close_at)
                <span class="badge bg-info">Closes {{ $poll->close_at->diffForHumans() }}</span>
            @endif
        </div>
        <div class="card-body">
            @if(!$hasVoted && !$isClosed && Auth::check())
                <form action="{{ route('polls.vote', $poll) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        @foreach($options as $option)
                            <div class="form-check mb-2">
                                <input class="form-check-input" 
                                    type="{{ $poll->max_options > 1 ? 'checkbox' : 'radio' }}" 
                                    name="options[]" 
                                    id="option-{{ $option->id }}" 
                                    value="{{ $option->id }}">
                                <label class="form-check-label" for="option-{{ $option->id }}">
                                    {{ $option->text }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-primary">Vote</button>
                    
                    @if($canViewResults)
                        <button type="button" class="btn btn-outline-secondary view-results-btn">View Results</button>
                    @endif
                </form>
            @else
                <div class="poll-results">
                    @php
                        $totalVotes = $poll->votes()->count();
                    @endphp
                    
                    @foreach($options as $option)
                        @php
                            $voteCount = $option->votes()->count();
                            $percentage = $totalVotes > 0 ? round(($voteCount / $totalVotes) * 100) : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $option->text }}</span>
                                <span>{{ $voteCount }} {{ Str::plural('vote', $voteCount) }} ({{ $percentage }}%)</span>
                            </div>
                            <div class="progress" style="height: 24px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%;" 
                                    aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $percentage }}%
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <strong>Total votes:</strong> {{ $totalVotes }}
                        </div>
                        
                        @if($hasVoted && !$isClosed && $poll->allow_change_vote)
                            <form action="{{ route('polls.vote', $poll) }}" method="POST">
                                @csrf
                                <button type="button" class="btn btn-sm btn-outline-primary change-vote-btn">Change Vote</button>
                            </form>
                        @endif
                    </div>
                </div>
                
                @if($hasVoted && !$isClosed && $poll->allow_change_vote)
                    <div class="poll-voting-form d-none">
                        <form action="{{ route('polls.vote', $poll) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                @foreach($options as $option)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" 
                                            type="{{ $poll->max_options > 1 ? 'checkbox' : 'radio' }}" 
                                            name="options[]" 
                                            id="option-change-{{ $option->id }}" 
                                            value="{{ $option->id }}">
                                        <label class="form-check-label" for="option-change-{{ $option->id }}">
                                            {{ $option->text }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Update Vote</button>
                                <button type="button" class="btn btn-outline-secondary cancel-change-vote-btn">Cancel</button>
                            </div>
                        </form>
                    </div>
                @endif
            @endif
        </div>
        <div class="card-footer text-muted">
            @if($poll->show_votes_publicly && $poll->votes()->count() > 0)
                <div class="voters-list">
                    <small>
                        <strong>Voters:</strong>
                        @php
                            $voters = $poll->votes()->with('user')->get()->pluck('user')->unique('id');
                        @endphp
                        {{ $voters->implode('name', ', ') }}
                    </small>
                </div>
            @endif
        </div>
    </div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // View results button
        const viewResultsBtn = document.querySelector('.view-results-btn');
        if (viewResultsBtn) {
            viewResultsBtn.addEventListener('click', function() {
                const form = this.closest('form');
                const pollCard = this.closest('.poll-card');
                
                // Create a temporary div to hold results
                const tempResults = document.createElement('div');
                tempResults.className = 'poll-results temp-results';
                tempResults.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading results...</p>
                    </div>
                `;
                
                // Hide form and show temp results
                form.style.display = 'none';
                form.insertAdjacentElement('afterend', tempResults);
                
                // Simulate loading (in a real app, you'd fetch results via AJAX)
                setTimeout(() => {
                    // Remove temp results and show real results via page refresh
                    window.location.reload();
                }, 1000);
            });
        }
        
        // Change vote button
        const changeVoteBtn = document.querySelector('.change-vote-btn');
        if (changeVoteBtn) {
            changeVoteBtn.addEventListener('click', function() {
                const resultsDiv = document.querySelector('.poll-results');
                const votingForm = document.querySelector('.poll-voting-form');
                
                resultsDiv.classList.add('d-none');
                votingForm.classList.remove('d-none');
            });
        }
        
        // Cancel change vote button
        const cancelChangeVoteBtn = document.querySelector('.cancel-change-vote-btn');
        if (cancelChangeVoteBtn) {
            cancelChangeVoteBtn.addEventListener('click', function() {
                const resultsDiv = document.querySelector('.poll-results');
                const votingForm = document.querySelector('.poll-voting-form');
                
                resultsDiv.classList.remove('d-none');
                votingForm.classList.add('d-none');
            });
        }
        
        // Limit checkbox selection based on max_options
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="options[]"]');
        if (checkboxes.length > 0) {
            const maxOptions = {{ $poll->max_options ?? 1 }};
            
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checkedBoxes = document.querySelectorAll('input[type="checkbox"][name="options[]"]:checked');
                    
                    if (checkedBoxes.length > maxOptions) {
                        this.checked = false;
                        alert(`You can only select up to ${maxOptions} options.`);
                    }
                });
            });
        }
    });
</script>
@endpush

<style>
.poll-card .progress-bar {
    background-color: #3366CC;
    transition: width 1s ease-in-out;
}

.poll-card .card-header {
    background-color: rgba(51, 102, 204, 0.1);
}

.poll-card .form-check-input:checked {
    background-color: #3366CC;
    border-color: #3366CC;
}

.dark .poll-card .progress-bar {
    background-color: #4377d6;
}

.dark .poll-card .card-header {
    background-color: rgba(67, 119, 214, 0.2);
}
</style>
