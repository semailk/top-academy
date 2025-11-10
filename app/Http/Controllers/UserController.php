<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $posts = $user->posts()->latest()->get();
        return view('users.show', compact('user', 'posts'));
    }

    public function profile(): View
    {
        $user = Auth::user();
        return view('users.profile', compact('user'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated = $validator->validated();

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Получаем экземпляр модели через find()
            $userToUpdate = User::find($user->id);
            $oldAvatar = $userToUpdate->avatar;
            $path = null;
            if (isset($validated['avatar'])) {
                $path = $request->file('avatar')->store('avatars', 'public');
            }

            $user->username = $validated['username'];
            $user->email = $validated['email'];
            $path = 'storage/' . $path;
            if ($path) {
                $user->avatar = $path;
            }

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
                $user->password = $updateData['password'];
            }

            $user->save();

            DB::commit();

            if (File::exists($oldAvatar) && $path) {
                File::delete($oldAvatar);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            abort(400);
        }


        return redirect()->route('profile')->with('success', 'Профиль успешно обновлен!');
    }
}
