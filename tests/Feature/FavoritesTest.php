<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    /**
     * @test
     */
    public function a_guest_can_not_favorite_anything()
    {
        $this->withExceptionHandling()->post('replies/1/favorites')->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function an_authenticated_user_can_favorite_a_reply()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $this->post('replies/' . $reply->id . '/favorites');

        $this->assertCount(1,$reply->favorites);
    }

    /**
     * @test
     */
    public function an_authenticated_user_can_unfavorite_a_reply()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $reply->favorite();

        $this->assertCount(1,$reply->favorites);

        $this->delete('replies/'. $reply->id. '/favorites');

        $this->assertCount(0, $reply->fresh()->favorites);
    }

    /**
     * @test
     */
    public function an_authenticated_user_may_only_favorite_a_reply_once()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $this->post('replies/' . $reply->id . '/favorites');
        $this->post('replies/' . $reply->id . '/favorites');

        $this->assertCount(1,$reply->favorites);
    }

}
