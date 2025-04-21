<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PollVotingTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

}
