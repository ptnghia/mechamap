<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ConversationController extends Controller
{
    /**
     * Display a listing of the user's conversations.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $filter = $request->input('filter');

        $query = Conversation::whereHas('participants', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });

        // Apply filters
        if ($filter === 'unread') {
            $query->whereHas('messages', function ($q) use ($user) {
                $q->whereHas('conversation.participants', function ($q2) use ($user) {
                    $q2->where('user_id', $user->id)
                        ->whereColumn('messages.created_at', '>', DB::raw('COALESCE(conversation_participants.last_read_at, "1970-01-01")'));
                })->where('user_id', '!=', $user->id);
            });
        } elseif ($filter === 'started') {
            $query->whereHas('messages', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->whereRaw('messages.id IN (SELECT MIN(id) FROM messages GROUP BY conversation_id)');
            });
        }

        $conversations = $query->with(['participants.user', 'messages' => function ($q) {
            $q->latest()->limit(1);
        }])
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('conversations.index', compact('conversations', 'filter'));
    }

    /**
     * Display the specified conversation.
     */
    public function show(Conversation $conversation): View
    {
        // Check if the user is a participant in the conversation
        if (!$conversation->participants()->where('user_id', Auth::id())->exists()) {
            abort(403);
        }

        $messages = $conversation->messages()
            ->with('user')
            ->latest()
            ->paginate(50);

        // Mark unread messages as read
        $participant = $conversation->participants()->where('user_id', Auth::id())->first();
        if ($participant) {
            $participant->update(['last_read_at' => now()]);
        }

        return view('conversations.show', compact('conversation', 'messages'));
    }

    /**
     * Store a newly created conversation in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
            'title' => 'nullable|string|max:255',
            'allow_invite' => 'nullable|boolean',
            'lock_conversation' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $recipient = User::findOrFail($request->recipient_id);

        // Check if a conversation already exists between these users
        $existingConversation = Conversation::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->whereHas('participants', function ($query) use ($recipient) {
                $query->where('user_id', $recipient->id);
            })
            ->first();

        if ($existingConversation) {
            // Add message to existing conversation
            $message = $existingConversation->messages()->create([
                'user_id' => $user->id,
                'content' => $request->message,
            ]);

            $existingConversation->touch();

            // Create alert for recipient
            $this->createMessageAlert($recipient, $user, $existingConversation, $message);

            return redirect()->route('conversations.show', $existingConversation)
                ->with('success', 'Message sent successfully.');
        }

        // Create a new conversation
        $conversation = Conversation::create([
            'title' => $request->title,
        ]);

        // Add participants
        $conversation->participants()->createMany([
            [
                'user_id' => $user->id,
                'last_read_at' => now(),
            ],
            [
                'user_id' => $recipient->id,
            ],
        ]);

        // Add the first message
        $message = $conversation->messages()->create([
            'user_id' => $user->id,
            'content' => $request->message,
        ]);

        // Create alert for recipient
        $this->createMessageAlert($recipient, $user, $conversation, $message);

        return redirect()->route('conversations.show', $conversation)
            ->with('success', 'Conversation started successfully.');
    }

    /**
     * Store a new message in the specified conversation.
     */
    public function storeMessage(Request $request, Conversation $conversation): RedirectResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        // Check if the user is a participant in the conversation
        if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
            abort(403);
        }

        // Create the message
        $message = $conversation->messages()->create([
            'user_id' => $user->id,
            'content' => $request->message,
        ]);

        // Update conversation timestamp
        $conversation->touch();

        // Mark as read for the sender
        $participant = $conversation->participants()->where('user_id', $user->id)->first();
        if ($participant) {
            $participant->update(['last_read_at' => now()]);
        }

        // Create alerts for other participants
        $otherParticipants = $conversation->participants()
            ->where('user_id', '!=', $user->id)
            ->with('user')
            ->get();

        foreach ($otherParticipants as $participant) {
            $this->createMessageAlert($participant->user, $user, $conversation, $message);
        }

        return back()->with('success', 'Message sent successfully.');
    }

    /**
     * Create an alert for a new message.
     */
    private function createMessageAlert(User $recipient, User $sender, Conversation $conversation, Message $message): void
    {
        $title = $conversation->title
            ? __('New message in :conversation', ['conversation' => $conversation->title])
            : __('New message from :sender', ['sender' => $sender->name]);

        $content = __(':sender sent you a message: :preview', [
            'sender' => $sender->name,
            'preview' => Str::limit($message->content, 100)
        ]);

        Alert::create([
            'user_id' => $recipient->id,
            'title' => $title,
            'content' => $content,
            'type' => 'info',
            'alertable_id' => $conversation->id,
            'alertable_type' => Conversation::class,
        ]);
    }
}
