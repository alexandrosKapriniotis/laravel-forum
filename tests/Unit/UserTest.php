<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_fetch_their_most_recent_reply()
    {
        $user = create('App\Models\User');

        $reply = create('App\Models\Reply', ['user_id' => $user->id]);

        $this->assertEquals($reply->id, $user->lastReply->id);
    }
}