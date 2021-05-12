<?php

namespace Tests\Unit;

use App\Inspections\Spam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpamTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    function it_checks_for_invalid_keywords()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply here'));

        $this->expectException('Exception');

        $spam->detect('Yahoo Customer Support');
    }

    /**
     * @test
     */
    function it_checks_for_any_key_being_held_down()
    {
        $spam = new Spam();

        $this->expectException('Exception');

        $spam->detect('Hello world aaaaaaaaa');

    }
}
