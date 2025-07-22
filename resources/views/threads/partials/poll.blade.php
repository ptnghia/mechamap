@if($thread->hasPoll())
    @php
        $poll = $thread->poll()->first();
        $options = $poll->options;
        $hasVoted = Auth::check() && $poll->hasVoted(Auth::user());
        $canViewResults = $poll->allow_view_without_vote || $hasVoted;
        $isClosed = $poll->isClosed();
    @endphp

    <div class="mb-4 poll-card">
        <div class="poll-card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 title_page_sub"><i class="fa-solid fa-square-poll-vertical me-1"></i> {{ $poll->question }}</h5>
            @if($isClosed)
                <span class="badge bg-secondary">{{ __('forum.poll.closed') }}</span>
            @elseif($poll->close_at)
                <span class="badge bg-info">{{ __('forum.poll.closes_at', ['time' => $poll->close_at->diffForHumans()]) }}</span>
            @endif
        </div>
        <div class="poll-card-body">
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
                    <button type="submit" class="btn btn-primary">{{ __('forum.poll.vote') }}</button>

                    @if($canViewResults)
                        <button type="button" class="btn btn-outline-secondary view-results-btn">{{ __('forum.poll.view_results') }}</button>
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
                            <div class="d-flex justify-content-between mb-1 poll-results_item">
                                <span>{{ $option->text }}</span>
                                <span>{{ $voteCount }} {{ trans_choice('forum.poll.votes', $voteCount) }} ({{ $percentage }}%)</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%;"
                                    aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $percentage }}%
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <strong>{{ __('forum.poll.total_votes') }}:</strong> {{ $totalVotes }}
                        </div>

                        @if($hasVoted && !$isClosed && $poll->allow_change_vote)
                            <form action="{{ route('polls.vote', $poll) }}" method="POST">
                                @csrf
                                <button type="button" class="btn btn-sm btn-outline-primary change-vote-btn">{{ __('forum.poll.change_vote') }}</button>
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
                                <button type="submit" class="btn btn-primary">{{ __('forum.poll.update_vote') }}</button>
                                <button type="button" class="btn btn-outline-secondary cancel-change-vote-btn">{{ __('common.cancel') }}</button>
                            </div>
                        </form>
                    </div>
                @endif
            @endif
        </div>
        <div class="poll-card-footer text-muted">
            @if($poll->show_votes_publicly && $poll->votes()->count() > 0)
                <div class="voters-list">
                    <small>
                        <strong>{{ __('forum.poll.voters') }}:</strong>
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
                            <span class="visually-hidden">{{ __('ui.common.loading') }}</span>
                        </div>
                        <p class="mt-2">{{ __('forum.poll.loading_results') }}</p>
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
                        alert('{{ __("forum.poll.max_options_exceeded", ["max" => ""]) }}'.replace('', maxOptions));
                    }
                });
            });
        }
    });
</script>
@endpush

