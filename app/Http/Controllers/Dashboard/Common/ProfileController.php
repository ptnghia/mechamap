<?php

namespace App\Http\Controllers\Dashboard\Common;

use App\Http\Controllers\Dashboard\BaseController;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Profile Controller cho Dashboard
 *
 * Quản lý thông tin cá nhân của user trong dashboard
 */
class ProfileController extends BaseController
{
    /**
     * Hiển thị form chỉnh sửa profile
     */
    public function edit(Request $request)
    {
        $breadcrumb = $this->getBreadcrumb([
            ['name' => 'Profile', 'route' => 'dashboard.profile.edit']
        ]);

        return $this->dashboardResponse('dashboard.common.profile.edit', [
            'user' => $this->user,
            'breadcrumb' => $breadcrumb
        ]);
    }

    /**
     * Cập nhật thông tin profile
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'job_title' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'experience_years' => 'nullable|string|in:0-1,1-3,3-5,5-10,10+',
            'bio' => 'nullable|string|max:500',
            'skills' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only([
            'name', 'email', 'job_title', 'company', 'location',
            'experience_years', 'bio', 'skills', 'phone',
            'website', 'linkedin_url', 'github_url'
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($this->user->avatar && !str_contains($this->user->avatar, 'ui-avatars.com')) {
                $oldAvatarPath = str_replace('/storage/', '', $this->user->avatar);
                if (Storage::disk('public')->exists($oldAvatarPath)) {
                    Storage::disk('public')->delete($oldAvatarPath);
                }
            }

            // Upload new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = '/storage/' . $avatarPath;
        }

        $this->user->fill($data);

        if ($this->user->isDirty('email')) {
            $this->user->email_verified_at = null;
        }

        $this->user->save();

        return redirect()->route('dashboard.profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Cập nhật thông tin profile chi tiết
     */
    public function updateDetails(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $this->user->id,
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'profession' => 'nullable|string|max:100',
            'about_me' => 'nullable|string|max:1000',
            'signature' => 'nullable|string|max:500',
        ]);

        $data = $request->only([
            'name', 'email', 'username', 'phone', 'location',
            'bio', 'website', 'profession', 'about_me', 'signature'
        ]);

        // Reset email verification if email changed
        if ($this->user->email !== $request->email) {
            $data['email_verified_at'] = null;
        }

        $this->user->update($data);

        return redirect()->route('dashboard.profile.edit')
            ->with('success', 'Thông tin chi tiết đã được cập nhật thành công.');
    }

    /**
     * Cập nhật avatar
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Xóa avatar cũ nếu có
        if ($this->user->avatar && !str_contains($this->user->avatar, 'ui-avatars.com')) {
            $oldAvatarPath = str_replace('/storage/', '', $this->user->avatar);
            if (Storage::disk('public')->exists($oldAvatarPath)) {
                Storage::disk('public')->delete($oldAvatarPath);
            }
        }

        // Upload avatar mới
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $this->user->update([
            'avatar' => '/storage/' . $avatarPath
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Avatar đã được cập nhật thành công.',
                'avatar_url' => $this->user->avatar
            ]);
        }

        return redirect()->route('dashboard.profile.edit')
            ->with('success', 'Avatar đã được cập nhật thành công.');
    }

    /**
     * Xóa avatar
     */
    public function deleteAvatar(Request $request)
    {
        if ($this->user->avatar && !str_contains($this->user->avatar, 'ui-avatars.com')) {
            $oldAvatarPath = str_replace('/storage/', '', $this->user->avatar);
            if (Storage::disk('public')->exists($oldAvatarPath)) {
                Storage::disk('public')->delete($oldAvatarPath);
            }
        }

        $this->user->update(['avatar' => null]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Avatar đã được xóa thành công.'
            ]);
        }

        return redirect()->route('dashboard.profile.edit')
            ->with('success', 'Avatar đã được xóa thành công.');
    }

    /**
     * Cập nhật mật khẩu
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validateWithBag('updatePassword', [
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $this->user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('dashboard.profile.edit')
            ->with('status', 'password-updated');
    }

    /**
     * Hiển thị form xóa tài khoản
     */
    public function showDeleteForm()
    {
        $breadcrumb = $this->getBreadcrumb([
            ['name' => 'Profile', 'route' => 'dashboard.profile.edit'],
            ['name' => 'Delete Account', 'route' => null]
        ]);

        return $this->dashboardResponse('dashboard.common.profile.delete', [
            'breadcrumb' => $breadcrumb
        ]);
    }

    /**
     * Xóa tài khoản
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => 'required|current_password',
        ]);

        $user = $this->user;

        // Xóa avatar nếu có
        if ($user->avatar && !str_contains($user->avatar, 'ui-avatars.com')) {
            $avatarPath = str_replace('/storage/', '', $user->avatar);
            if (Storage::disk('public')->exists($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }
        }

        // Logout và xóa tài khoản
        auth()->logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Tài khoản đã được xóa thành công.');
    }

    /**
     * Hiển thị thống kê profile
     */
    public function stats()
    {
        $stats = [
            'threads_count' => $this->user->threads()->count(),
            'comments_count' => $this->user->comments()->count(),
            'likes_received' => $this->user->threads()->sum('likes_count') +
                              $this->user->comments()->sum('likes_count'),
            'bookmarks_count' => $this->user->bookmarks()->count(),
            'following_count' => $this->user->following()->count(),
            'followers_count' => $this->user->followers()->count(),
            'profile_views' => $this->user->profile_views ?? 0,
            'reputation_score' => $this->user->reputation_score ?? 0,
        ];

        // Marketplace stats nếu có quyền
        if ($this->user->hasAnyMarketplacePermission()) {
            $marketplaceStats = $this->getMarketplaceStats();
            $stats = array_merge($stats, $marketplaceStats);
        }

        $breadcrumb = $this->getBreadcrumb([
            ['name' => 'Profile', 'route' => 'dashboard.profile.edit'],
            ['name' => 'Statistics', 'route' => null]
        ]);

        return $this->dashboardResponse('dashboard.common.profile.stats', [
            'stats' => $stats,
            'breadcrumb' => $breadcrumb
        ]);
    }
}
