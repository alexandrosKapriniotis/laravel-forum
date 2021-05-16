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
    use RefreshDatabase;

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

    /**
     * @test
     * @throws Exception
     */
    function a_user_can_filter_threads_by_popularity()
    {
        $threadWithOneReply = Thread::factory()
            ->hasReplies(1)
            ->create();

        $threadWithThreeReplies = Thread::factory()
            ->hasReplies(3)
            ->create();

        $threadWithZeroReplies = Thread::factory()->create();

        $this->get('threads?popular=1')
            ->assertSeeInOrder([
                $threadWithThreeReplies->title,
                $threadWithOneReply->title,
                $threadWithZeroReplies->title
            ]);
    }

    /**
     * @test
     * @throws Exception
     */
    function a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        $thread = create(Thread::class);
        create(Reply::class,['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();

        $this->assertCount(1,$response['data']);
    }

    /**
     * @test
     * @throws Exception
     */
    public function a_user_can_request_all_replies_for_a_given_thread()
    {
        $thread = create(Thread::class);
        create(Reply::class,['thread_id' => $thread->id],2);

        $response = $this->getJson($thread->path() . '/replies')->json();

        $this->assertCount(2,$response['data']);
        $this->assertEquals(2,$response['total']);
    }

    /**
     * @test
     */
    public function we_record_a_new_visit_each_time_the_thread_is_read()
    {
        $thread = create(Thread::class);

        $this->assertSame(0, $thread->visits);

        $this->call('GET',$thread->path());

        $this->assertEquals(1, $thread->fresh()->visits);
    }

}
