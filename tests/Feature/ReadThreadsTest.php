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

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var Collection|Model|mixed
     */
    private $thread;

    public function setUp(): void
    {
        parent::setUp();
        $this->thread = Thread::factory()->create();
    }

    /**
     * @test
     * @return void
     */
    function a_user_can_view_all_threads() {
        $this->get('/threads')->assertSee($this->thread->title);
    }

    /**
     * @test
     * @return void
     * @throws Exception
     */
    function a_user_can_read_a_single_thread() {
        $this->get($this->thread->path())->assertSee($this->thread->title);
    }

    /**
     * @test
     * @throws Exception
     */
    function a_user_can_read_replies_that_are_associated_with_a_thread() {
       $reply = Reply::factory()->create(['thread_id' => $this->thread->id]);

       $this->get($this->thread->path())->assertSee($reply->body);
    }

    /**
     * @test
     */
    function a_user_can_filter_threads_according_to_a_channel() {
        $channel = Channel::factory()->create();
        $threadInChannel = create(Thread::class,['channel_id' => $channel->id]);
        $threadNotInChannel = create(Thread::class);

        $this->get('/threads/'.$channel->slug)->assertSee($threadInChannel->title)->assertDontSee($threadNotInChannel->title);
    }

    /**
     * @test
     * @throws Exception
     */
    function a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(create(User::class,['name' => 'JohnDoe']));

        $threadByJohn = create(Thread::class,['user_id' => auth()->id()]);
        $threadNotByJohn = create(Thread::class);

        $this->get('threads?by=JohnDoe')->assertSee($threadByJohn->title)->assertDontSee($threadNotByJohn->title);
    }

}
