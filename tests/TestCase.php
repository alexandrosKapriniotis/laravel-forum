<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations as DatabaseMigration;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigration;

    /**
     * @param null $user
     * @return $this
     */
    protected function signIn($user = null): TestCase
    {
        $user = $user ?: create(User::class);

        $this->actingAs($user);

        return $this;
    }
}
