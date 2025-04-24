<?php

namespace App\Livewire\Poll;

use App\Models\Poll;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\PollService;

class PollAnalytics extends Component
{
    use WithPagination;

    public $poll;

    protected PollService $pollService;

    public function boot(PollService $pollService)
    {
        $this->pollService = $pollService;
    }

    public function mount($poll)
    {
        $this->poll = Poll::with(['pollVotes.pollOption', 'pollOptions.votes'])->find($poll);

        $this->authorize('view', $this->poll);

        if (!$this->poll) {
            abort(404);
        }
    }

    public function getWinnerNames()
    {
        return $this->pollService->getFormattedWinners($this->poll);
    }

    public function render()
    {
        return view('livewire.poll.poll-analytics', [
            'pollVotes' => $this->poll->pollVotes()->with(['pollOption', 'user'])->paginate(20),
        ]);
    }
}
