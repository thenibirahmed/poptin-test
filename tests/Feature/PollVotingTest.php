<?php

namespace Tests\Feature;

use App\Livewire\Poll\AddPoll;
use App\Livewire\Poll\PollList;
use Tests\TestCase;
use App\Models\Poll;
use App\Models\User;
use Livewire\Livewire;
use App\Models\PollVote;
use App\Models\PollOption;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PollVotingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_can_vote_and_cookie_is_set()
    {
        $poll = Poll::factory()->has(PollOption::factory()->count(3))->create();
        $option = $poll->pollOptions()->first();

        $response = $this->postJson(route('polls.vote', $poll), [
            'poll_option_id' => $option->id,
            'ip_address' => '127.0.0.1'
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Vote submitted successfully.'])
            ->assertCookie(Poll::POLL_COOKIE_KEY);

        $this->assertDatabaseHas('poll_votes', [
            'poll_option_id' => $option->id,
            'user_id' => null,
        ]);
    }

    public function test_authenticated_user_can_vote_and_vote_is_stored()
    {
        $user = User::factory()->create();
        $poll = Poll::factory()->has(PollOption::factory()->count(2))->create();
        $option = $poll->pollOptions()->first();

        $response = $this->actingAs($user, 'sanctum')->postJson(route('polls.vote', $poll), [
            'poll_option_id' => $option->id,
            'ip_address' => '192.168.1.2'
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Vote submitted successfully.']);

        $this->assertDatabaseHas('poll_votes', [
            'poll_option_id' => $option->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_vote_is_updated_if_user_already_voted()
    {
        $user = User::factory()->create();
        $poll = Poll::factory()->has(PollOption::factory()->count(2))->create();
        $option1 = $poll->pollOptions[0];
        $option2 = $poll->pollOptions[1];

        PollVote::create([
            'poll_option_id' => $option1->id,
            'user_id' => $user->id,
            'voter_identity' => Str::uuid(),
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson(route('polls.vote', $poll), [
            'poll_option_id' => $option2->id,
            'ip_address' => '127.0.0.1'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('poll_votes', [
            'poll_option_id' => $option2->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_multiple_guests_with_different_voter_ids_can_vote()
    {
        $poll = Poll::factory()->has(PollOption::factory()->count(2))->create();
        $option = $poll->pollOptions[0];

        $response1 = $this->withCookie(Poll::POLL_COOKIE_KEY, json_encode([
            'voter_identity' => (string) Str::uuid(),
            'poll_votes' => []
        ]))->postJson(route('polls.vote', $poll), [
            'poll_option_id' => $option->id,
            'ip_address' => '1.1.1.1'
        ]);

        $response2 = $this->withCookie(Poll::POLL_COOKIE_KEY, json_encode([
            'voter_identity' => (string) Str::uuid(),
            'poll_votes' => []
        ]))->postJson(route('polls.vote', $poll), [
            'poll_option_id' => $option->id,
            'ip_address' => '2.2.2.2'
        ]);

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        $this->assertEquals(2, PollVote::count());
    }

    public function test_user_can_create_poll()
    {
        $user = User::factory()->create();

        $name = 'New Poll';
        $question = 'What is your favorite color?';
        $options = [
            'Red',
            'Blue',
            'Green'
        ];

        Livewire::actingAs($user)
            ->test(AddPoll::class)
            ->set('poll.name', $name)
            ->set('poll.question', $question)
            ->set('pollOptions', $options)
            ->call('savePoll')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('polls', [
            'name' => $name,
            'question' => $question,
            'user_id' => $user->id,
        ]);

        $pollId = Poll::where('name', $name)->first()->id;

        $optionTexts = PollOption::where('poll_id', $pollId)->pluck('option')->toArray();
        $this->assertEqualsCanonicalizing($options, $optionTexts);
    }

    public function test_user_can_delete_own_poll()
    {
        $user = User::factory()->create();
        $poll = Poll::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(PollList::class)
            ->call('deletePoll', $poll->id);

        $this->assertDatabaseMissing('polls', [
            'id' => $poll->id
        ]);
    }

    public function test_user_cannot_delete_others_poll()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $poll = Poll::factory()->for($user1)->create();

        Livewire::actingAs($user2)
            ->test(PollList::class)
            ->call('deletePoll', $poll->id)
            ->assertForbidden();

        $this->assertDatabaseHas('polls', ['id' => $poll->id]);
    }

    public function test_admin_can_delete_all_polls()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $user = User::factory()->create();
        $poll = Poll::factory()->for($user)->create();

        Livewire::actingAs($admin)
            ->test(PollList::class)
            ->call('deletePoll', $poll->id);

        $this->assertDatabaseMissing('polls', ['id' => $poll->id]);
    }

    public function test_user_can_edit_own_poll()
    {
        $user = User::factory()->create();
        $poll = Poll::factory()->for($user)->hasPollOptions(3)->create();

        $newName = 'Updated Poll Name';
        $newQuestion = 'Updated Question?';
        $newOptions = ['Updated 1', 'Updated 2'];

        Livewire::actingAs($user)
            ->test(AddPoll::class, ['poll' => $poll->id])
            ->set('poll.name', $newName)
            ->set('poll.question', $newQuestion)
            ->set('pollOptions', $newOptions)
            ->call('savePoll')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('polls', [
            'id' => $poll->id,
            'name' => $newName,
            'question' => $newQuestion,
        ]);

        $this->assertEqualsCanonicalizing(
            $newOptions,
            PollOption::where('poll_id', $poll->id)->pluck('option')->toArray()
        );
    }

    public function test_user_cannot_edit_others_poll()
    {
        $pollOwner = User::factory()->create();
        $otherUser = User::factory()->create();
        $poll = Poll::factory()->for($pollOwner)->hasPollOptions(2)->create();

        Livewire::actingAs($otherUser)
            ->test(AddPoll::class, ['poll' => $poll->id])
            ->assertForbidden();
    }

    public function test_admin_can_edit_any_poll()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $poll = Poll::factory()->for($user)->hasPollOptions(2)->create();

        $updatedName = 'Admin Updated Name';

        Livewire::actingAs($admin)
            ->test(AddPoll::class, ['poll' => $poll->id])
            ->set('poll.name', $updatedName)
            ->call('savePoll')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('polls', [
            'id' => $poll->id,
            'name' => $updatedName,
        ]);
    }

}
