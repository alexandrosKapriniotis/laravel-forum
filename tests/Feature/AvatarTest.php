<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @return void
     */
    public function only_members_can_add_avatars()
    {
        $this->json('POST','api/users/1/avatar')
              ->assertStatus(401);
    }

    /**
     * @test
     * @return void
     */
    public function a_valid_avatar_must_be_provided()
    {
        $this->signIn();

        $this->json('POST','api/users/'.auth()->id().'/avatar',[
            'avatar' => 'not-an-image'
        ])->assertStatus(422);
    }

    /**
     * @test
     * @return void
     */
    public function a_user_may_add_an_avatar_to_their_profile()
    {
        $this->signIn();

        Storage::fake('public');

        $this->json('POST','api/users/'.auth()->id().'/avatar',[
            'avatar' => $file = UploadedFile::fake()->image('avatar.jpg')
        ]);

        $this->assertEquals('/storage/avatars/'. $file->hashName(),auth()->user()->avatar_path);

        //Storage::disk('public')->assertExists('/storage/avatars/'. $file->hashName());
    }
}
