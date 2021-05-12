<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscribeToThreadsTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_user_can_subscribe_to_threads ()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $thread = create(Thread::class);

        $this->post($thread->path(). '/subscriptions');

        $this->assertCount(1, $thread->fresh()->subscriptions);
    }

    /**
     * @test
     */
    public function a_user_can_unsubscribe_from_threads ()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $thread = create(Thread::class);

        $thread->subscribe();

        $this->delete($thread->path().'/subscriptions');

        $this->assertCount(0, $thread->subscriptions);
    }
}
