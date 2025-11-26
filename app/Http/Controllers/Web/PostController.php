<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\PostStoreRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
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
        $categories = Category::DontParent()->get();

        return view('posts.create', [
            'categories' => $categories,
        ]);
    }

    public function store(PostStoreRequest $request): RedirectResponse
    {
        $userId = Auth::user()->id;

        Post::query()->create([
            'content' => $request->get('content'),
            'user_id' => $userId,
            'category_id' => $request->get('category_id'),
        ]);

        return redirect()->route('posts.index')->with('success', 'Пост успешно создан!');
    }


    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $comments = $post->comments()->orderByDesc('created_at')->paginate(3);
        $categories = Category::DontParent()->get();

        return view('posts.edit', compact('post', 'comments', 'categories'));
    }

    public function update(PostStoreRequest $postStoreRequest, Post $post)
    {
        // Безопасная проверка на админа
        $isAdmin = Auth::user()->status === 'admin';
        if ($post->user_id !== Auth::id() && !$isAdmin) {
            abort(403);
        }

        $post->update($postStoreRequest->all());

        return redirect()->back()->with('success', 'Пост успешно обновлен!');
    }

    public function destroy(Post $post)
    {
        /** @var User $user */
        $user = Auth::user();
        $isAdmin = $user->role->name === 'admin';
        if ($post->user_id !== Auth::id() && !$isAdmin) {
            abort(403);
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Пост успешно удален!');
    }
}
