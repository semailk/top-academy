<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\PostStoreRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->paginate(10);
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    // public  function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function store(PostStoreRequest $request)
    {
        // Принудительно получаем числовой ID
        $userId = Auth::user()->id; // Используем прямое обращение к модели

        Post::create([
            'content' => $request->get('content'),
            'user_id' => $userId, // Гарантированно число
        ]);

        return redirect()->route('posts.index')->with('success', 'Пост успешно создан!');
    }


    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        // Безопасная проверка на админа
//        $isAdmin = Auth::user()->status === 'admin';
//        if ($post->user_id !== Auth::id() && !$isAdmin) {
//            abort(403);
//        }
        $comments = $post->comments()->paginate(3);
        return view('posts.edit', compact('post', 'comments'));
    }

    public function update(PostStoreRequest $postStoreRequest, Post $post)
    {
        // Безопасная проверка на админа
        $isAdmin = Auth::user()->status === 'admin';
        if ($post->user_id !== Auth::id() && !$isAdmin) {
            abort(403);
        }

        $postStoreRequest->validate([
            'content' => 'required|string|max:1000',
        ]);

        $post->update($postStoreRequest->all());

        return redirect()->route('posts.index')->with('success', 'Пост успешно обновлен!');
    }

    public function destroy(Post $post)
    {
        // Безопасная проверка на админа
        $isAdmin = Auth::user()->status === 'admin';
        if ($post->user_id !== Auth::id() && !$isAdmin) {
            abort(403);
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Пост успешно удален!');
    }
}
