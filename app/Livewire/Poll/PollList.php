<?php

namespace App\Livewire\Poll;

use App\Models\Poll;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\PollService;

class PollList extends Component
{
    use WithPagination;

    protected PollService $pollService;

    public function boot(PollService $pollService)
    {
        $this->pollService = $pollService;
    }

    public function deletePoll($pollId)
    {
        $poll = Poll::findOrFail($pollId);
        $this->authorize('delete', $poll);
        $this->pollService->deletePoll($pollId);
    }

    public function render()
    {
        return view('livewire.poll.poll-list', [
            'polls' => $this->pollService->getPollsForUser(),
        ]);
    }
}
