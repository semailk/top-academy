@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Все посты</h1>
    <a href="{{ route('posts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
        Создать пост
    </a>
</div>

<div id="data-box" class="space-y-6">
    {{ $posts->links() }}
    @foreach($posts as $post)
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-start mb-4">
            <!-- Левая часть с аватаром и информацией -->
            <div class="flex items-center space-x-4">
                <img src="{{ $post->user->getAvatarUrl() }}"
                     alt="{{ $post->user->username }}"
                     class="w-12 h-12 rounded-full object-cover">
                <div>
                    <h2 class="text-xl font-semibold">
                        {{ $post->user->username }}
                    </h2>
                    <p class="text-gray-500 text-sm">
                        {{ $post->created_at->format('d.m.Y H:i') }}
                    </p>
                </div>
            </div>

            <!-- Правая часть с кнопками (если доступно) -->

            <div class="flex space-x-2">
                <a href="{{ route('posts.edit', $post) }}" class="text-blue-600 hover:text-blue-800">Редактировать</a>
                @if(Auth::id() === $post->user_id || Auth::user()->isAdmin())
                <form method="POST" action="{{ route('posts.destroy', $post) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Удалить пост?')">Удалить</button>
                </form>
                @endif
            </div>
        </div>
        <p class="text-gray-700 whitespace-pre-line">{{ $post->content }}</p>
    </div>
@endforeach
</div>
    <script>
        $(document).ready(function (){
            $.ajax({ type: "GET",
                url: "{{ route('api.posts.index') }}",
                headers: {
                    Authorization: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWUsImlhdCI6MTUxNjIzOTAyMn0.KMUFsIDTnFmyG3nMiGM6H9FNFUROf3wh7SmqJp-QV30'
                },
                data: {
                  page: 1
                },
                success : function(response)
                {
                   // $.each(response.data, function (i, item){
                   //    $('#data-box').append(
                   //        '<div style="border: solid 2px black"> <div>ID:' + item.id +'</div>'+
                   //        '<div>CONTENT:' + item.content +'</div></div>'
                   //    )
                   // });
                }
            });
        });
    </script>
@endsection
