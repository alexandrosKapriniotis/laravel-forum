<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Auth\AuthenticationException;
use Tests\TestCase;

class ParticipateInForumTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    /**
     * @test
     */
    function unauthenticated_users_may_not_add_replies(){
        $this->expectException(AuthenticationException::class);

        $thread = Thread::find(1);
        dd($thread);
        $this->post($thread->path(),[]);
    }

    /**
     * @test
     * @return void
     */
    function an_authenticated_user_may_participate_in_forum_threads(){
        $this->be(User::factory()->create());

        $thread = Thread::factory()->create();

        $reply = Reply::factory()->make();
        $this->post($thread->path().'/replies',$reply->toArray());

        $this->get($thread->path())->assertSee($reply->body);
    }
}
