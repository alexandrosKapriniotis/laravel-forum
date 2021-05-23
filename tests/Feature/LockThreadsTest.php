<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LockThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function non_administrators_may_not_lock_threads()
    {
        $this->signIn();

        $thread = create(Thread::class,['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store',$thread))->assertStatus(403);

        $this->assertFalse(!! $thread->fresh()->locked);
    }

    /** @test */
    public function administrators_can_lock_threads()
    {
        $this->signIn(User::factory()->administrator()->create());

        $thread = create(Thread::class,['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store',$thread));

        $this->assertTrue(! ! $thread->fresh()->locked,'Failed asserting that the thread was locked');
    }

    /** @test */
    public function administrators_can_unlock_threads()
    {
        $this->signIn(User::factory()->administrator()->create());

        $thread = create(Thread::class,['user_id' => auth()->id(),'locked'  => true]);

        $this->delete(route('locked-threads.destroy',$thread));

        $this->assertFalse($thread->fresh()->locked,'Failed asserting that the thread was unlocked');
    }

    /** @test */
    public function once_locked_a_thread_may_not_receive_new_replies()
    {
        $this->signIn();

        $user   = create(User::class);
        $thread = create(Thread::class);

        $thread->lock();

        $this->post($thread->path().'/replies',[
            'body'      => 'FooBar',
            'user_id'   =>  $user->id
        ])->assertStatus(422);
    }
}
