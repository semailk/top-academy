@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
    <h1 class="text-3xl font-bold mb-6">Мой профиль</h1>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Имя пользователя</label>
                <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <div>
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Новый пароль</label>
                <input type="password" name="password" id="password"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       minlength="6">
                <p class="text-gray-500 text-xs mt-1">Оставьте пустым, если не хотите менять</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Подтверждение пароля</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       minlength="6">
            </div>

            <div>
                <img src="{{ $user->avatar_url }}" width="50px" height="50px" alt="">
                <label for="avatar" class="block text-gray-700 text-sm font-bold mb-2">Аватар</label>
                <input type="file" name="avatar" id="avatar"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Обновить профиль
        </button>
    </form>
</div>
@endsection
