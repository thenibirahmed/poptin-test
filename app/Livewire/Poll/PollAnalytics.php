<?php

namespace App\Livewire\Poll;

use App\Models\Poll;
use Livewire\Component;
use Livewire\WithPagination;

class PollAnalytics extends Component
{
    use WithPagination;

    public $poll;

    public function mount($poll)
    {
        $this->poll = Poll::with(['pollVotes.pollOption', 'pollOptions.votes'])->find($poll);

        $this->authorize('view', $this->poll);

        if (!$this->poll) {
            abort(404);
        }
    }

    public function getPollWinners()
    {
        $maxVotes = $this->poll->pollOptions
            ->map(fn ($option) => $option->votes->count())
            ->max();

        return $this->poll->pollOptions->filter(fn ($option) => $option->votes->count() === $maxVotes);
    }

    public function getWinnerNames()
    {
        return $this->getPollWinners()
            ->pluck('option') 
            ->implode(', ');
    }

    public function render()
    {
        return view('livewire.poll.poll-analytics', [
            'pollVotes' => $this->poll->pollVotes()->with(['pollOption', 'user'])->paginate(20),
        ]);
    }
}
