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

// User-specific private channels for notifications
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Conversation channels for chat messages
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    // Check if user is participant in this conversation
    return \App\Models\Conversation::where('id', $conversationId)
        ->whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->exists();
});

// Admin dashboard channels
Broadcast::channel('admin.dashboard', function ($user) {
    return $user->hasRole(['Admin', 'Moderator']);
});

Broadcast::channel('admin.activity', function ($user) {
    return $user->hasRole(['Admin', 'Moderator']);
});

// Role-specific channels
Broadcast::channel('dashboard.moderator', function ($user) {
    return $user->hasRole('Moderator');
});

Broadcast::channel('dashboard.supplier', function ($user) {
    return $user->hasRole('Supplier');
});

Broadcast::channel('dashboard.manufacturer', function ($user) {
    return $user->hasRole('Manufacturer');
});

// Marketplace channels
Broadcast::channel('marketplace.orders.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('marketplace.seller.{sellerId}', function ($user, $sellerId) {
    // Check if user is the seller
    $seller = \App\Models\MarketplaceSeller::find($sellerId);
    return $seller && $seller->user_id === $user->id;
});

// Forum channels
Broadcast::channel('forum.{forumId}', function ($user, $forumId) {
    // Check if user can access this forum
    $forum = \App\Models\Forum::find($forumId);
    return $forum && $forum->isAccessibleBy($user);
});

Broadcast::channel('thread.{threadId}', function ($user, $threadId) {
    // Check if user can access this thread
    $thread = \App\Models\Thread::find($threadId);
    return $thread && $thread->isAccessibleBy($user);
});

// Notification channels
Broadcast::channel('notifications.role.{role}', function ($user, $role) {
    return $user->hasRole($role);
});

// Activity channels
Broadcast::channel('activity.social.{userId}', function ($user, $userId) {
    // Check if user is following this user or is the user themselves
    return (int) $user->id === (int) $userId || $user->isFollowing($userId);
});

// System monitoring channels (admin only)
Broadcast::channel('system.monitoring', function ($user) {
    return $user->hasRole('Admin');
});

Broadcast::channel('system.performance', function ($user) {
    return $user->hasRole('Admin');
});

// Public channels (no authorization needed)
// These are defined as public channels in the events themselves
