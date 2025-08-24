<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Private user channels for notifications
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Thread channels for real-time comments
Broadcast::channel('thread.{threadId}', function ($user, $threadId) {
    // All authenticated users can listen to thread updates
    return $user ? ['id' => $user->id, 'name' => $user->name] : false;
});

// Forum channels for real-time updates
Broadcast::channel('forum.{forumId}', function ($user, $forumId) {
    // All authenticated users can listen to forum updates
    return $user ? ['id' => $user->id, 'name' => $user->name] : false;
});

// Global notifications channel
Broadcast::channel('notifications', function ($user) {
    // All authenticated users can listen to global notifications
    return $user ? ['id' => $user->id, 'name' => $user->name] : false;
});

// Group conversation channels
Broadcast::channel('group.{groupId}', function ($user, $groupId) {
    // Check if user is a member of the group
    $member = \App\Models\GroupMember::where('conversation_id', $groupId)
        ->where('user_id', $user->id)
        ->where('is_active', true)
        ->first();

    return $member ? ['id' => $user->id, 'name' => $user->name] : false;
});

// Private conversation channels
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    // Check if user is a participant in the conversation
    $participant = \App\Models\ConversationParticipant::where('conversation_id', $conversationId)
        ->where('user_id', $user->id)
        ->first();

    return $participant ? ['id' => $user->id, 'name' => $user->name] : false;
});
