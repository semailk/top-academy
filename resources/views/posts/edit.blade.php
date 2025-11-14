@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold mb-6">Редактировать пост</h1>

        {{-- Форма редактирования поста --}}
        <form method="POST" action="{{ route('posts.update', $post) }}">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="content" class="block text-gray-700 text-sm font-bold mb-2">Текст поста</label>
                <textarea name="content" id="content" rows="6"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          required>{{ old('content', $post->content) }}</textarea>
            </div>

            <div class="flex space-x-4">
                @if(auth()->user()->isAdmin() || $post->user_id == auth()->user()->id)
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Обновить
                    </button>
                @endif
                <a href="{{ route('posts.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    Отмена
                </a>
            </div>
        </form>

        {{-- Лента комментариев --}}
        <div class="mt-10">
            <h2 class="text-xl font-semibold mb-4">Комментарии</h2>

            @forelse($comments as $comment)
                <div class="border border-gray-200 rounded-md p-3 mb-3 bg-gray-50">
                    <p class="text-gray-700">{{ $comment->comment }}</p>
                    <div class="text-sm text-gray-500 mt-2">
                        Автор: {{ $comment->user->username }} | {{ $comment->created_at->diffForHumans() }}
                    </div>
                </div>
            @empty
                <p class="text-gray-500">Комментариев пока нет.</p>
            @endforelse

            {{ $comments->links() }}
        </div>

        {{-- Форма добавления комментария --}}
        @auth
            <div class="mt-6">
                <form method="POST" action="{{ route('comments.store', $post) }}">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <textarea name="comment" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:outline-none"
                              placeholder="Написать комментарий..." required></textarea>
                    <button type="submit"
                            class="mt-3 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        Отправить
                    </button>
                </form>
            </div>
        @endauth

    </div>
@endsection
