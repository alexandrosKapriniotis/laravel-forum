<?php

namespace Tests\Unit;

use App\Models\Channel;
use App\Models\Thread;
use Tests\TestCase;

class ChannelTest extends TestCase
{
    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function a_channel_consists_of_threads()
    {
        $channel = create(Channel::class);
        $thread = create(Thread::class,['channel_id' => $channel->id]);


        $this->assertTrue($channel->threads->contains($thread));
    }
}
