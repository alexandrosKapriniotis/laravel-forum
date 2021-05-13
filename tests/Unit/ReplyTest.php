<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Reply;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    /**
     * @test
     */
    function it_has_an_owner(){
        $reply = Reply::factory()->create();

        $this->assertInstanceOf('App\Models\User',$reply->owner);
    }

    /** @test */
    function it_knows_if_it_was_just_published()
    {
        $reply = create('App\Models\Reply');

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }
}
