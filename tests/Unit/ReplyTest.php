<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Reply;
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
}
