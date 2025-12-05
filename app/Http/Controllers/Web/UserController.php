<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        $posts = $user->posts()->latest()->get();
        return view('users.show', compact('user', 'posts'));
    }

    public function profile()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('users.profile', compact('user'));
    }


    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Убедимся что папка существует с правильным путем
        $avatarsPath = 'public\avatars';
        if (!Storage::exists($avatarsPath)) {
            Storage::makeDirectory($avatarsPath);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $updateData = [
            'username' => $request->username,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // ИСПРАВЛЕННАЯ обработка аватарки для Windows
        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');

            // Удаляем старую аватарку
            if ($user->avatar) {
                $oldPath = 'public\avatars\\' . $user->avatar;
                if (Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }
            }

            // Генерируем имя файла
            $avatarName = 'avatar_' . $user->id . '_' . time() . '.' . $avatarFile->getClientOriginalExtension();

            // Сохраняем файл с правильным путем для Windows
            try {
                // Способ 1: Используем storeAs с правильным путем
                $path = $avatarFile->storeAs($avatarsPath, $avatarName);

                // Способ 2: Альтернативно - сохраняем напрямую
                $destinationPath = storage_path('app\\public\\avatars\\' . $avatarName);
                $avatarFile->move(storage_path('app\\public\\avatars'), $avatarName);

                Log::info('Avatar saved', [
                    'method' => 'direct_move',
                    'filename' => $avatarName,
                    'destination' => $destinationPath,
                    'file_exists' => file_exists($destinationPath) ? 'YES' : 'NO'
                ]);

                if (file_exists($destinationPath)) {
                    $updateData['avatar'] = $avatarName;
                } else {
                    Log::error('File not found after move: ' . $destinationPath);
                    return back()->with('error', 'Файл не сохранился');
                }
            } catch (\Exception $e) {
                Log::error('Error saving avatar: ' . $e->getMessage());
                return back()->with('error', 'Ошибка сохранения: ' . $e->getMessage());
            }
        }

        User::where('id', $user->id)->update($updateData);

        return redirect()->route('profile')->with('success', 'Профиль успешно обновлен!');
    }

    public function removeAvatar()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->avatar) {
            Storage::delete('public/avatars/' . $user->avatar);
            User::where('id', $user->id)->update(['avatar' => null]);
            Log::info('Avatar removed for user: ' . $user->id);
        }

        return redirect()->route('profile')->with('success', 'Аватарка успешно удалена!');
    }
}
