<?php

namespace Tests\Feature;

use App\Models\Thread;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class TrendingThreadsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Redis::del('trending_threads');
    }

    /**
     * @test
     * @return void
     */
    public function it_increments_a_threads_score_each_time_it_is_read()
    {
        $this->assertCount(0,Redis::zrevrange('trending_threads', 0, -1));
        $thread = create(Thread::class);

        $this->call('GET',$thread->path());

        $trending = Redis::zrevrange('trending_threads', 0, -1);

        $this->assertCount(1, $trending);
    }

}