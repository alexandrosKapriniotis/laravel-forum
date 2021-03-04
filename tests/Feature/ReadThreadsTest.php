<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
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

}
