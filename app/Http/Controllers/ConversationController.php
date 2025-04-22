<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ConversationController extends Controller
{
    /**
     * Display a listing of the user's conversations.
     */
    public function index(): View
    {
        $user = Auth::user();
        $conversations = $user->conversations()
            ->with(['participants', 'lastMessage'])
            ->latest('updated_at')
            ->paginate(20);
        
        return view('conversations.index', compact('conversations'));
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
        $conversation->markAsRead(Auth::id());
        
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
        ]);
        
        $user = Auth::user();
        $recipient = User::findOrFail($request->recipient_id);
        
        // Check if a conversation already exists between these users
        $existingConversation = $user->conversations()
            ->whereHas('participants', function ($query) use ($recipient) {
                $query->where('user_id', $recipient->id);
            })
            ->first();
        
        if ($existingConversation) {
            // Add message to existing conversation
            $existingConversation->messages()->create([
                'user_id' => $user->id,
                'content' => $request->message,
            ]);
            
            $existingConversation->touch();
            
            return redirect()->route('conversations.show', $existingConversation)
                ->with('success', 'Message sent successfully.');
        }
        
        // Create a new conversation
        $conversation = Conversation::create([
            'title' => null, // For direct messages, title can be null
        ]);
        
        // Add participants
        $conversation->participants()->createMany([
            ['user_id' => $user->id],
            ['user_id' => $recipient->id],
        ]);
        
        // Add the first message
        $conversation->messages()->create([
            'user_id' => $user->id,
            'content' => $request->message,
        ]);
        
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
        
        // Check if the user is a participant in the conversation
        if (!$conversation->participants()->where('user_id', Auth::id())->exists()) {
            abort(403);
        }
        
        // Create the message
        $conversation->messages()->create([
            'user_id' => Auth::id(),
            'content' => $request->message,
        ]);
        
        // Update conversation timestamp
        $conversation->touch();
        
        return back()->with('success', 'Message sent successfully.');
    }
}
