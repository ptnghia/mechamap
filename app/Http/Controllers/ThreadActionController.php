<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\ThreadBookmark;
use App\Models\ThreadFollow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadActionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Thêm bookmark cho thread
     */
    public function addBookmark(Thread $thread): RedirectResponse
    {
        $user = Auth::user();

        // Kiểm tra xem đã bookmark chưa
        $exists = ThreadBookmark::where('user_id', $user->id)
            ->where('thread_id', $thread->id)
            ->exists();

        if (!$exists) {
            ThreadBookmark::create([
                'user_id' => $user->id,
                'thread_id' => $thread->id,
            ]);

            session()->flash('success', 'Đã thêm thread vào bookmark!');
        } else {
            session()->flash('info', 'Thread này đã có trong bookmark của bạn.');
        }

        return back();
    }

    /**
     * Xóa bookmark cho thread
     */
    public function removeBookmark(Thread $thread): RedirectResponse
    {
        $user = Auth::user();

        $deleted = ThreadBookmark::where('user_id', $user->id)
            ->where('thread_id', $thread->id)
            ->delete();

        if ($deleted) {
            session()->flash('success', 'Đã xóa thread khỏi bookmark!');
        } else {
            session()->flash('info', 'Thread này không có trong bookmark của bạn.');
        }

        return back();
    }

    /**
     * Theo dõi thread
     */
    public function addFollow(Thread $thread): RedirectResponse
    {
        $user = Auth::user();

        // Kiểm tra xem đã theo dõi chưa
        $exists = ThreadFollow::where('user_id', $user->id)
            ->where('thread_id', $thread->id)
            ->exists();

        if (!$exists) {
            ThreadFollow::create([
                'user_id' => $user->id,
                'thread_id' => $thread->id,
            ]);

            session()->flash('success', 'Đã theo dõi thread này!');
        } else {
            session()->flash('info', 'Bạn đã theo dõi thread này rồi.');
        }

        return back();
    }

    /**
     * Bỏ theo dõi thread
     */
    public function removeFollow(Thread $thread): RedirectResponse
    {
        $user = Auth::user();

        $deleted = ThreadFollow::where('user_id', $user->id)
            ->where('thread_id', $thread->id)
            ->delete();

        if ($deleted) {
            session()->flash('success', 'Đã bỏ theo dõi thread này!');
        } else {
            session()->flash('info', 'Bạn chưa theo dõi thread này.');
        }

        return back();
    }
}
