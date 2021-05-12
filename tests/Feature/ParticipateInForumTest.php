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
        $this->withExceptionHandling()
            ->post('/threads/some-channel/1/replies', [])
            ->assertRedirect('/login');
    }

    /**
     * @test
     * @return void
     */
    function an_authenticated_user_may_participate_in_forum_threads()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $thread = create(Thread::class);
        $reply = make(Reply::class);

        $this->post($thread->path().'/replies',$reply->toArray());

        $this->assertDatabaseHas('replies',['body' => $reply->body]);

        $this->assertEquals(1,$thread->fresh()->replies_count);
    }

    /**
     * @test
     */
    function a_reply_requires_a_body(){
        $this->signIn();
        $thread = Thread::factory()->create();
        $reply = make(Reply::class,['body' => null]);

        $this->json('POST',$thread->path().'/replies',$reply->toArray())->assertStatus(422);
    }

    /**
     * @test
     */
    function unauthorised_users_cannot_delete_replies()
    {
        $this->withExceptionHandling();

        $reply = create(Reply::class);

        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('login');
    }

    /**
     * @test
     */
    function authorised_users_can_delete_replies()
    {
        $this->signIn();

        $reply = create(Reply::class,['user_id' => auth()->id()]);

        $this->delete("/replies/{$reply->id}")->assertStatus(302);

        $this->assertDatabaseMissing('replies',['id' => $reply->id]);
        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }

    /**
     * @test
     */
    function authorised_users_can_update_replies()
    {
        $this->signIn();

        $reply = create(Reply::class,['user_id' => auth()->id()]);

        $updatedReply = 'you\'ve been changed, fool.';

        $this->patch("/replies/{$reply->id}",['body' => $updatedReply]);

        $this->assertDatabaseHas('replies',['id' => $reply->id,'body' => $updatedReply]);
    }

    /**
     * @test
     */
    function unauthorised_users_cannot_update_replies()
    {
        $reply = create(Reply::class);

        $this->patch("/replies/{$reply->id}")
            ->assertRedirect('login');

        $this->signIn()->patch("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    /**
     * @test
     */
    function replies_that_contain_spam_may_not_be_created()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create(Thread::class);

        $reply  = make(Reply::class,[
           'body' => 'Yahoo Customer Support'
        ]);

        $this->json('POST', $thread->path() . '/replies', $reply->toArray())->assertStatus(422);
    }

    /** @test */
    function users_may_only_reply_a_maximum_of_once_per_minute()
    {
        $this->signIn();

        $thread = create('App\Models\Thread');
        $reply  = make('App\Models\Reply');

         $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(201);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(429);
    }
}
