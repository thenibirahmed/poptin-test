<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
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

    public function test_authenticated_user_can_vote()
    {
        $user = User::factory()->create();
        $poll = Poll::factory()->has(PollOption::factory()->count(3))->create();
        $option = $poll->pollOptions()->first();

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/polls/{$poll->id}/vote", [
            'poll_option_id' => $option->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('poll_votes', [
            'poll_option_id' => $option->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_cannot_vote_twice()
    {
        $user = User::factory()->create();
        $poll = Poll::factory()->has(PollOption::factory()->count(2))->create();
        $option = $poll->pollOptions()->first();

        $this->actingAs($user, 'sanctum')->postJson("/api/polls/{$poll->id}/vote", [
            'poll_option_id' => $option->id,
        ])->assertStatus(200);

        $this->actingAs($user, 'sanctum')->postJson("/api/polls/{$poll->id}/vote", [
            'poll_option_id' => $option->id,
        ])->assertStatus(403);
    }

    public function test_guest_can_vote_by_ip()
    {
        $poll = Poll::factory()->has(PollOption::factory()->count(2))->create();
        $option = $poll->pollOptions()->first();

        $this->postJson("/api/polls/{$poll->id}/vote", [
            'poll_option_id' => $option->id,
        ])->assertStatus(200);

        $this->assertDatabaseHas('poll_votes', [
            'poll_option_id' => $option->id,
            'user_id' => null,
        ]);
    }

    public function test_guest_cannot_vote_twice_from_same_ip()
    {
        $poll = Poll::factory()->has(PollOption::factory()->count(2))->create();
        $option = $poll->pollOptions()->first();

        $this->postJson("/api/polls/{$poll->id}/vote", [
            'poll_option_id' => $option->id,
        ])->assertStatus(200);

        $this->postJson("/api/polls/{$poll->id}/vote", [
            'poll_option_id' => $option->id,
        ])->assertStatus(403);
    }

    public function test_guest_vote_is_updated_when_user_logs_in()
    {
        $poll = Poll::factory()->has(PollOption::factory()->count(2))->create();
        $option = $poll->pollOptions()->first();

        $this->postJson("/api/polls/{$poll->id}/vote", [
            'poll_option_id' => $option->id,
        ])->assertStatus(200);

        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')->postJson("/api/polls/{$poll->id}/vote", [
            'poll_option_id' => $option->id,
        ])->assertStatus(403);

        $this->assertDatabaseHas('poll_votes', [
            'poll_option_id' => $option->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(1, PollVote::count());
    }
}
