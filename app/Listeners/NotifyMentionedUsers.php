<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;
use App\Models\User;
use App\Notifications\YouWereMentioned;

class NotifyMentionedUsers
{
    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewReply  $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
        preg_match_all('/(?<=@)[^\s\.@]+/', $event->reply->body, $matches);

        $names = $matches[0];

        foreach ($names as $name){
            $user = User::whereName($name)->first();

            if ($user)
            {
                $user->notify(new YouWereMentioned($event->reply));
            }
        }
    }
}
