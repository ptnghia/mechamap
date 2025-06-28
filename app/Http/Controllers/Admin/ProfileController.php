<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang profile admin
     */
    public function index(): View
    {
        $user = Auth::user();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Hồ sơ', 'url' => route('admin.profile.index')]
        ];

        return view('admin.profile.index', compact('user', 'breadcrumbs'));
    }

    /**
     * Cập nhật thông tin profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'about_me' => ['nullable', 'string', 'max:1000'],
            'website' => ['nullable', 'url', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'signature' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Cập nhật thông tin cơ bản
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->about_me = $request->about_me;
        $user->website = $request->website;
        $user->location = $request->location;
        $user->signature = $request->signature;

        // Xử lý upload avatar
        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu có
            if ($user->avatar && !str_contains($user->avatar, 'ui-avatars.com')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
            }

            // Upload avatar mới
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/' . $avatarPath;
        }

        $user->save();

        return back()->with('success', 'Thông tin hồ sơ đã được cập nhật thành công.');
    }

    /**
     * Hiển thị form đổi mật khẩu
     */
    public function showChangePasswordForm(): View
    {
        $user = Auth::user();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Hồ sơ', 'url' => route('admin.profile.index')],
            ['title' => 'Đổi mật khẩu', 'url' => route('admin.profile.password')]
        ];

        return view('admin.profile.password', compact('user', 'breadcrumbs'));
    }

    /**
     * Xử lý đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Mật khẩu hiện tại không chính xác.');
                }
            }],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Cập nhật mật khẩu
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Mật khẩu đã được thay đổi thành công.');
    }
}
