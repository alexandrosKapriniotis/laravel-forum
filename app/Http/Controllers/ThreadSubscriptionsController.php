<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Request;

class ThreadSubscriptionsController extends Controller
{
    public function store($channelId, Thread $thread)
    {
        $thread->subscribe();
    }

    /**
     * Unsubscribe a user from a thread.
     *
     */
    public function destroy($channelId, Thread $thread)
    {
        $thread->unsubscribe();
    }
}
