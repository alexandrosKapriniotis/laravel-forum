<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations as DatabaseMigration;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DB::statement('PRAGMA foreign_keys=on');
    }

    use RefreshDatabase;
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
