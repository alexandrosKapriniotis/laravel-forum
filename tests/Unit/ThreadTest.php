<?php

namespace Tests\Unit;

use App\Models\Thread;
use App\Notifications\ThreadWasUpdated;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;
    protected $guarded = [];

    /**
     * @var Collection|Model|mixed
     */
    protected $thread;

    public function setUp(): void
    {
        parent::setUp();
        $this->thread = Thread::factory()->create();
    }

    /**
     * @test
     */
    function a_thread_has_a_path()
    {
        $thread = create(Thread::class);

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->slug}",$thread->path());
    }

    /**
     * @test
     * @return void
     */
    function a_thread_has_a_creator(){
        $this->assertInstanceOf('App\Models\User',$this->thread->creator);
    }

    /**
     * @test
     * @return void
     */
    function a_thread_has_replies(){
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection',$this->thread->replies);
    }

    /**
     * @test
     * @return void
     */
    function a_thread_can_add_a_reply(){
        $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1
        ]);

        $this->assertCount(1,$this->thread->replies);
    }

    /**
     * @test
     * @return void
     */
    function a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    {
        Notification::fake();

        $this->signIn()
             ->thread
             ->subscribe()
             ->addReply([
                'body'    => 'Foobar',
                'user_id' => 999
            ]);

        Notification::assertSentTo(auth()->user(),ThreadWasUpdated::class);
    }

    /**
     * @test
     */
    function a_thread_belongs_to_a_channel()
    {
        $thread = create(Thread::class);

        $this->assertInstanceOf('App\Models\Channel',$thread->channel);
    }

    /**
     * @test
     */
    function a_thread_can_be_subscribed_to()
    {
        $thread = create(Thread::class);

        $thread->subscribe($userId = 1);

        $this->assertEquals(1,$thread->subscriptions()->where('user_id',$userId)->count());
    }

    /**
     * @test
     */
    function a_thread_can_be_unsubscribed_from()
    {
        $thread = create(Thread::class);

        $thread->subscribe($userId = 1);

        $thread->unsubscribe($userId);

        $this->assertEquals(0,$thread->subscriptions()->count());
    }

    /**
     * @test
     */
    function it_knows_if_the_authenticated_user_is_subscribed_to_it()
    {
        $thread = create(Thread::class);

        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    /**
     * @test
     * @throws Exception
     */
    function a_thread_can_check_if_the_authenticated_user_has_read_all_replies()
    {
        $this->signIn();

        $thread = create(Thread::class);

        $this->assertTrue($thread->hasUpdatesFor(auth()->user()));

        $key = sprintf("users.%s.visits.%s",auth()->id(),$thread->id);

        cache()->forever($key,Carbon::now());

        $this->assertFalse($thread->hasUpdatesFor(auth()->user()));
    }

}
