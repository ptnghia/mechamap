<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a poll for a thread.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Thread $thread)
    {
        // Authorize the user to add a poll to this thread
        $this->authorize('update', $thread);

        // Validate the request
        $request->validate([
            'question' => 'required|string|max:255',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:255',
            'max_options' => 'required|integer|min:1',
            'allow_change_vote' => 'boolean',
            'show_votes_publicly' => 'boolean',
            'allow_view_without_vote' => 'boolean',
            'close_after_days' => 'nullable|integer|min:1',
        ]);

        // Create the poll
        DB::transaction(function () use ($request, $thread) {
            $poll = Poll::create([
                'thread_id' => $thread->id,
                'question' => $request->question,
                'max_options' => $request->max_options,
                'allow_change_vote' => $request->has('allow_change_vote'),
                'show_votes_publicly' => $request->has('show_votes_publicly'),
                'allow_view_without_vote' => $request->has('allow_view_without_vote'),
                'close_at' => $request->close_after_days ? now()->addDays($request->close_after_days) : null,
            ]);

            // Create the poll options
            foreach ($request->options as $optionText) {
                if (!empty($optionText)) {
                    PollOption::create([
                        'poll_id' => $poll->id,
                        'text' => $optionText,
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Poll created successfully.');
    }

    /**
     * Vote on a poll.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function vote(Request $request, Poll $poll)
    {
        // Check if the poll is closed
        if ($poll->isClosed()) {
            return redirect()->back()->with('error', 'This poll is closed.');
        }

        // Validate the request
        $request->validate([
            'options' => 'required|array|min:1|max:' . $poll->max_options,
            'options.*' => 'required|exists:poll_options,id,poll_id,' . $poll->id,
        ]);

        $user = Auth::user();

        // Check if the user has already voted
        $hasVoted = $poll->hasVoted($user);

        // If the user has voted and changing votes is not allowed
        if ($hasVoted && !$poll->allow_change_vote) {
            return redirect()->back()->with('error', 'You cannot change your vote on this poll.');
        }

        // Delete existing votes if the user has voted before
        if ($hasVoted) {
            PollVote::where('poll_id', $poll->id)
                ->where('user_id', $user->id)
                ->delete();
        }

        // Create new votes
        foreach ($request->options as $optionId) {
            PollVote::create([
                'poll_id' => $poll->id,
                'poll_option_id' => $optionId,
                'user_id' => $user->id,
            ]);
        }

        return redirect()->back()->with('success', 'Your vote has been recorded.');
    }
}
