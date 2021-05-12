<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @return void
     */
    public function mentioned_users_in_a_reply_are_notified()
    {
        $this->withoutExceptionHandling();
        $john = create(User::class,['name' => 'JohnDoe']);

        $this->signIn($john);

        $janeDoe = create(User::class,['name' => 'JaneDoe']);

        $thread = create(Thread::class);

        $reply = make(Reply::class,[
            'body' => '@JaneDoe look at this'
        ]);

        $this->post($thread->path().'/replies',$reply->toArray());

        $this->assertCount(1, $janeDoe->notifications);
    }
}
