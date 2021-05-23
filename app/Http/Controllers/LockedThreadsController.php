<?php

namespace App\Http\Controllers;

use App\Models\Thread;

class LockedThreadsController extends Controller
{
    /**
     * @param Thread $thread
     * @return void
     */
    public function store(Thread $thread)
    {
        $thread->lock();
    }

    /**
     * @param Thread $thread
     */
    public function destroy(Thread $thread)
    {
        $thread->unlock();
    }
}
