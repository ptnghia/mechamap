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
