<?php

namespace App\Livewire\Poll;

use App\Models\Poll;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Computed;
use App\Services\PollService;

class ViewPoll extends Component
{
    public $poll;

    public $selectedOption;

    protected PollService $pollService;

    public function boot(PollService $pollService)
    {
        $this->pollService = $pollService;
    }

    public function getListeners()
    {
        return [
            "echo:vote-casted.{$this->poll->id},VoteCasted" => '$refresh',
        ];
    }

    public function mount()
    {
        $this->poll = $this->pollService->getPollByUuid($this->poll);

        if (!$this->poll) {
            abort(404);
        }

        $this->selectedOption = $this->getUsersVote?->pollOption->id;
    }

    #[Computed]
    public function getUsersVote()
    {
        return $this->pollService->getUsersVote($this->poll, Auth::id());
    }

    public function vote()
    {
        $this->validate();

        try {
            $cookieValue = $this->pollService->makeVoteApiCall($this->poll, $this->selectedOption);
    
            if ($cookieValue) {
                Cookie::queue(Poll::POLL_COOKIE_KEY, $cookieValue, 60 * 24 * 30);
            }
    
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            $this->addError('selectedOption', $e->getMessage());
        }
    }

    public function rules()
    {
        return [
            'selectedOption' => 'required|exists:poll_options,id',
        ];
    }

    public function messages()
    {
        return [
            'selectedOption.required' => 'Please select an option to vote.',
            'selectedOption.exists' => 'The selected option is invalid.',
        ];
    }

    public function render()
    {
        return view('livewire.poll.view-poll');
    }
}
