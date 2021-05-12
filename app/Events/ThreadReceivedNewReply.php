<?php

namespace App\Events;

use App\Models\Reply;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThreadReceivedNewReply
{
    use Dispatchable, SerializesModels;

    public Reply $reply;

    /**
     * ThreadReceivedNewReply constructor.
     * @param Reply $reply
     */
    public function __construct(Reply $reply)
    {
        $this->reply = $reply;
    }

}
