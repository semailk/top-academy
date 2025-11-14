<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Project</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <!-- Header -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Левая часть - меню -->
                <div class="flex space-x-4">
                    <a href="{{ route('posts.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded transition">Главная</a>
                    <a href="{{ route('posts.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded transition">Посты</a>
                    @if(auth()->check() && auth()->user()->isAdmin())
                    <a href="{{ route('users.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded transition">Пользователи</a>
                    @endif
                    <a href="{{ route('about') }}" class="hover:bg-blue-700 px-3 py-2 rounded transition">О нас</a>
                </div>

                <!-- Правая часть - информация пользователя -->
                @auth
                <div class="flex items-center space-x-4">
                    <!-- Аватарка в шапке -->
                    <img src="{{ Auth::user()->getAvatarUrl() }}"
                        alt="{{ Auth::user()->username }}"
                        class="w-8 h-8 rounded-full object-cover">
                    <!-- Имя пользователя -->
                    <span class="text-blue-100">Добро пожаловать, {{ Auth::user()->username }}!</span>

                    <!-- Кнопка Профиль -->
                    <a href="{{ route('profile') }}"
                        class="bg-blue-500 hover:bg-blue-400 px-4 py-2 rounded transition">
                        Профиль
                    </a>

                    <!-- Кнопка Выйти -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-400 px-4 py-2 rounded transition">
                            Выйти
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4">
        <!-- Блок успешных сообщений -->
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <!-- Блок ошибок -->
        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        <!-- Блок ошибок валидации -->
        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
        @yield('content')
    </main>

</body>

</html>
