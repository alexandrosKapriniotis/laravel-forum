<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RepliesController extends Controller
{
    /**
     * RepliesController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param $channelId
     * @param Thread $thread
     * @return RedirectResponse
     */
    public function store($channelId,Thread $thread): RedirectResponse
    {
        $thread->addReply([
            'body' => request()->input('body'),
            'user_id' => auth()->id()
        ]);

        return back();
    }
}
