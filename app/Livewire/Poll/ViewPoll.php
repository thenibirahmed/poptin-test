<?php

namespace App\Livewire\Poll;

use App\Models\Poll;
use Livewire\Component;

class ViewPoll extends Component
{
    public $poll;

    public function mount()
    {
        $this->poll = Poll::with(['pollOptions', 'pollVotes'])->find($this->poll);
    }

    public function render()
    {
        return view('livewire.poll.view-poll');
    }
}
