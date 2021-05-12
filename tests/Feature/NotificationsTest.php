<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp() : void
    {
        parent::setUp();

        $this->signIn();
    }

    /**
     * @test
     * @return void
     */
    public function a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply()
    {
        $thread2 = create(Thread::class);
        $thread = create(Thread::class)->subscribe();

        $this->assertCount(0, auth()->user()->notifications);

        $reply  = Reply::create(
            [
                'user_id'   => auth()->user()->id,
                'body'      => 'Some reply here',
                'thread_id' => $thread->id
            ]
        );

        $thread->addReply($reply);

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $reply2  = Reply::create(
            [
                'user_id'   => create(User::class)->id,
                'body'      => 'Some reply here',
                'thread_id' => $thread->id
            ]
        );

        $thread->addReply($reply2);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /**
     * @test
     */
    public function a_user_can_mark_a_notification_as_read ()
    {
        $thread = create(Thread::class)->subscribe();
        $reply  = Reply::create(
            [
                'user_id'   => create(User::class)->id,
                'body'      => 'Some reply here',
                'thread_id' => $thread->id
            ]
        );

        $thread->addReply($reply);

        $this->assertCount(1, auth()->user()->unreadNotifications);

        $notificationId = auth()->user()->unreadNotifications->first()->id;

        $this->delete("profiles/".auth()->user()->name."/notifications/{$notificationId}");

        $this->assertCount(0, auth()->user()->fresh()->unreadNotifications);
    }

    /**
     * @test
     */
    public function a_user_can_fetch_his_unread_notifications()
    {
        $thread = create(Thread::class)->subscribe();
        $reply  = Reply::create(
            [
                'user_id'   => create(User::class)->id,
                'body'      => 'Some reply here',
                'thread_id' => $thread->id
            ]
        );

        $thread->addReply($reply);

        $this->assertCount(
            1,
            $this->getJson("/profiles/". auth()->user()->name. "/notifications/")->json());
    }
}
