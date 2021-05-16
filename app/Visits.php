<?php


namespace App;


use App\Models\Thread;
use Illuminate\Support\Facades\Redis;

class Visits
{
    protected Thread $thread;

    /**
     * Visits constructor.
     */
    public function __construct($thread)
    {
        $this->thread = $thread;
    }

    /**
     * @return $this
     */
    public function record(): Visits
    {
        Redis::incr($this->cacheKey());

        return $this;
    }

    /**
     * @return $this
     */
    public function reset(): Visits
    {
        Redis::del($this->cacheKey());

        return $this;
    }

    public function count()
    {
        return Redis::get($this->cacheKey()) ?? 0;
    }

    protected function cacheKey()
    {
        return "threads.{$this->thread->id}.visits";
    }
}
