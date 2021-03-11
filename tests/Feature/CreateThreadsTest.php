<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Thread;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    /**
     * @test
     */
    function guest_may_not_create_threads()
    {
        $this->withExceptionHandling();
        $this->get('/threads/create')->assertRedirect('login');
        $this->post('/threads/')->assertRedirect('login');
    }

    /** @test */
    function an_authenticated_user_can_create_new_forum_threads()
    {
        $this->signIn();
        $thread = make(Thread::class);

        $this->followingRedirects();

        $this->post('/threads', $thread->toArray())->assertSee($thread->body);
    }

    /** @test */
    function a_thread_requires_a_title(){
        $this->publishThread(['title' => null])->assertSessionHasErrors('title');
    }

    /** @test */
    function a_thread_requires_a_description(){
        $this->publishThread(['body' => null])->assertSessionHasErrors('body');
    }

    /** @test */
    function a_thread_requires_a_valid_channel(){
        Channel::factory()->count(2)->create();

        $this->publishThread(['channel_id' => null])->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 9999])->assertSessionHasErrors('channel_id');
    }

    /**
     * @param array $overrides
     * @return TestResponse
     */
    public function publishThread($overrides = []): TestResponse
    {
        $this->signIn();
        $thread = make(Thread::class,$overrides);

        return $this->post('/threads',$thread->toArray());
    }
}
