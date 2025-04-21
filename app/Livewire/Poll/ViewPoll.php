<?php

namespace App\Livewire\Poll;

use App\Models\Poll;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ViewPoll extends Component
{
    public $poll;

    public $selectedOption;

    public function mount()
    {
        $this->poll = Poll::with(['pollOptions', 'pollVotes'])->find($this->poll);
    }

    public function vote()
    {
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

        dd($response->json());
        
        if ($response->successful()) {

        } else {

        }
    }


    public function render()
    {
        return view('livewire.poll.view-poll');
    }
}
