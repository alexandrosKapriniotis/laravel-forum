<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilesTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_user_has_a_profile()
    {
        $user = create(User::class);

        $this->withoutExceptionHandling()->get("/profiles/$user->name")->assertSee($user->name);
    }

    /**
     * @test
     * @throws Exception
     */
    public function profiles_display_all_threads_created_cy_the_associated_user()
    {
        $this->signIn();

        $thread = create(Thread::class,['user_id' => auth()->id()]);

        $this->withoutExceptionHandling()->get("/profiles/".auth()->user()->name)
             ->assertSee($thread->title)
             ->assertSee($thread->body);
    }

}
