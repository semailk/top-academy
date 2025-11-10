<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->latest()->get();
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

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Принудительно получаем числовой ID
        $userId = Auth::user()->id; // Используем прямое обращение к модели

        Post::create([
            'content' => $request->content,
            'user_id' => $userId, // Гарантированно число
        ]);

        return redirect()->route('posts.index')->with('success', 'Пост успешно создан!');
    }
    // public function store(Request $request)
    // {
    // $request->validate([
    // 'content' => 'required|string|max:1000',
    // ]);

    //         // Проверяем, что пользователь авторизован
    //     if (!Auth::check()) {
    //         return redirect()->route('login')->with('error', 'Пожалуйста, войдите в систему');
    //     }
    // // Проверяем, что user_id - число
    //     $userId = Auth::id();
    //     if (!is_numeric($userId)) {

    //         return back()->with('error', 'Ошибка авторизации');
    //     }

    //         Post::create([
    //             'content' => $request->content,
    //             'user_id' => Auth::id(),
    //         ]);

    //         return redirect()->route('posts.index')->with('success', 'Пост успешно создан!');
    //     }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $response = Gate::inspect('view', $post);

        if (!$response->allowed()) {
            abort(403);
        }

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        // Безопасная проверка на админа
        $isAdmin = Auth::user()->status === 'admin';
        if ($post->user_id !== Auth::id() && !$isAdmin) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $post->update($request->all());

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
