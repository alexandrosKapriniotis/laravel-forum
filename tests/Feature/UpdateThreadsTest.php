<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateThreadsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    use RefreshDatabase;


    /** @test */
    function a_thread_requires_a_title_and_a_body()
    {
        $thread = create(Thread::class,['user_id' => auth()->id()]);

        $this->patch($thread->path(),[
            'title' => 'title has changed'
        ])->assertSessionHasErrors('body');

        $this->patch($thread->path(),[
            'body' => 'body has changed'
        ])->assertSessionHasErrors('title');
    }

    /** @test */
    function unauthorised_users_may_not_update_threads()
    {
        $thread = create(Thread::class,['user_id' => create(User::class)->id]);

        $this->patch($thread->path(),[
            'title' => 'title has changed',
            'body'  => 'body has changed'
        ])->assertStatus(403);
    }

    /** @test */
    function a_thread_can_be_updated_by_its_creator()
    {
        $thread = create(Thread::class,['user_id' => auth()->id()]);

        $this->patch($thread->path(),[
            'title' => 'title has changed',
            'body'  => 'body has changed'
        ]);

        $thread = $thread->fresh();

        $this->assertEquals('title has changed',$thread->title);

        $this->assertEquals('body has changed',$thread->body);
    }
}
