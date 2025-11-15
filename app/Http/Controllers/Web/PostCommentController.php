<?php

namespace App\Http\Controllers\Web;

use App\Models\PostComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostCommentController
{
    //TODO ДЗ УДАЛЕНИЕ КОММЕНТАРИЯ
    public function delete(PostComment $postComment)
    {
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'comment' => 'required|min:2|max:255',
            'user_id' => 'required|exists:users,id'
        ]);

        $newPostComment = new PostComment();
        $newPostComment->post_id = $validated['post_id'];
        $newPostComment->user_id = $validated['user_id'];
        $newPostComment->comment = $validated['comment'];
        $newPostComment->save();

        return redirect()->route('posts.edit', $newPostComment->post_id)->with('success', 'Коммент успешно добавлен!');
    }
}
