<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use App\Rules\Recaptcha;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\TestResponse;
use Mockery;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(function($request){
            if ($request['response'] == 'invalid') {
                return Http::response(['success' => false]);
            }
            return Http::response(['success' => true]);
        });
    }

    use RefreshDatabase;


    /**
     * @test
     */
    function guest_may_not_create_threads()
    {
        $this->withExceptionHandling();
        $this->get('/threads/create')->assertRedirect('login');
        $this->post('/threads/')->assertRedirect('login');
    }

    /** @test */
    function an_authenticated_user_can_create_new_forum_threads()
    {
        $response = $this->publishThread(['title' => 'some title','body' => 'some body']);

        $this->followingRedirects()->get($response->headers->get('location'))
            ->assertSee('some title')
            ->assertSee('some body');
    }

    /** @test */
    function a_thread_requires_a_title(){
        $this->publishThread(['title' => null])->assertSessionHasErrors('title');
    }

    /** @test */
    function a_thread_requires_a_description(){
        $this->publishThread(['body' => null])->assertSessionHasErrors('body');
    }

    /** @test */
    function a_thread_requires_a_valid_channel(){
        Channel::factory()->count(2)->create();

        $this->publishThread(['channel_id' => null])->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 9999])->assertSessionHasErrors('channel_id');
    }

    /**
     * @param array $overrides
     * @return TestResponse
     */
    public function publishThread($overrides = []): TestResponse
    {
        $this->signIn();

        $thread = make(Thread::class,$overrides);

        return $this->post('/threads',$thread->toArray() + ['g-recaptcha-response' => 'token']);
    }

    /**
     * @test
     */
    function unauthorised_users_may_not_delete_threads(){

        $this->withExceptionHandling();

        $thread = create(Thread::class);

        $this->delete($thread->path())->assertRedirect('/login');

        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);
    }

    /**
     * @test
     */
    function authorised_users_can_delete_threads(){

        $this->signIn();

        $thread = create(Thread::class,['user_id' => auth()->id()]);
        $reply  = create(Reply::class,['thread_id' => $thread->id]);
        $response = $this->json('DELETE', $thread->path());

        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads',['id' => $thread->id]);
        $this->assertDatabaseMissing('replies',['id' => $reply->id]);

        $this->assertEquals(0,Activity::count());
    }

    /**
     * @test
     */
    function a_thread_requires_a_unique_slug()
    {
        $this->signIn();

        $thread = create(Thread::class,['title' => 'Foo Title']);

        $this->assertEquals('foo-title', $thread->fresh()->slug);

        $this->post(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token']);

        $this->assertTrue(Thread::whereSlug('foo-title-2')->exists());

        $this->post(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token']);

        $this->assertTrue(Thread::whereSlug('foo-title-3')->exists());
    }

    /**
     * @test
     */
    function a_thread_requires_a_recaptcha_verification()
    {
        $this->withExceptionHandling();

        $this->followingRedirects()->publishThread(['g-recaptcha-response' => 'invalid'])
            ->assertSessionHasErrors('g-recaptcha-response');
    }
}
