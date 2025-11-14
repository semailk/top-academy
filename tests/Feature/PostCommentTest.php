<?php


// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class PostCommentTest extends TestCase
{
    //TODO Написать тестирования сервиса (создания, обновления и удаления) комментариев.
    public function test_store()
    {
        $user = User::query()->first();
        $post = Post::query()->first();
        $hashText = Str::random(32);

        $response = $this->actingAs($user)->post('/comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'comment' => $hashText
        ]);
        $postComment = PostComment::query()
            ->where('comment', $hashText)
            ->orderByDesc('id')
            ->first();

        $response->assertStatus(302);
        $this->assertEquals($hashText, $postComment->comment);
        $this->assertEquals($post->id, $postComment->post_id);
        $this->assertEquals($user->id, $postComment->user_id);
    }
}
