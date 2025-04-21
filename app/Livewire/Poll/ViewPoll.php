<?php

namespace App\Livewire\Poll;

use App\Models\Poll;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Computed;

class ViewPoll extends Component
{
    public $poll;

    public $selectedOption;

    public function mount()
    {
        $this->poll = Poll::with(['pollOptions', 'pollVotes'])->find($this->poll);
        $this->selectedOption = $this->getUsersVote?->id;
    }

    #[Computed(persist: true)]
    public function getUsersVote()
    {
        $userId = Auth::id();
        $ip = request()->ip();

        return $this->poll->getUsersVote($userId, $ip);
    }

    public function vote()
    {
        if($this->getUsersVote) {
            $this->addError('selectedOption', 'You have already voted.');
            return;
        }

        $this->validate([
            'selectedOption' => 'required|exists:poll_options,id',
        ]);

        $http = Http::acceptJson();

        if (Auth::check()) {
            $user = Auth::user();

            $token = $user->tokens()->first()?->plainTextToken ?? $user->createToken('poll-token')->plainTextToken;

            $http = $http->withToken($token);
        }

        $response = $http->post(url("/api/polls/{$this->poll->id}/vote"), [
            'poll_option_id' => $this->selectedOption,
        ]);
        
        if ($response->successful()) {
            $this->dispatch('$refresh');
        } else {
            $this->addError('selectedOption', $response->json('message', 'An error occurred while submitting your vote.'));
        }
    }


    public function render()
    {
        return view('livewire.poll.view-poll');
    }
}
