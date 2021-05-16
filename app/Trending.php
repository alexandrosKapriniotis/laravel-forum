<?php


namespace App;

use Illuminate\Support\Facades\Redis;

class Trending
{
    /**
     * @return array
     */
    public function get(): array
    {
        return array_map('json_decode',Redis::zrevrange($this->cacheKey(), 0, 4));
    }

    /**
     * @return string
     */
    public function cacheKey(): string
    {
        return 'trending_threads';
    }

    /**
     * @param $thread
     */
    public function push($thread)
    {
        Redis::zincrby('trending_threads', 1, json_encode([
            'title' => $thread->title,
            'path'  => $thread->path()
        ]));
    }

    public function reset()
    {
        Redis::del($this->cacheKey());
    }
}
