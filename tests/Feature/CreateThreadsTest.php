<?php

namespace Tests\Feature;

use App\Models\Thread;
use Illuminate\Auth\AuthenticationException;
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
        $this->post('/threads')->assertRedirect('login');
    }

    /** @test */
    function an_authenticated_user_can_create_new_forum_threads()
    {
        $this->signIn();
        $thread = create(Thread::class);

        $this->followingRedirects();

        $this->post('/threads', $thread->toArray())->assertSee($thread->body);
    }
}
