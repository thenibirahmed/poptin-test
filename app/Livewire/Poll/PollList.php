<?php

namespace App\Livewire\Poll;

use App\Models\Poll;
use Livewire\Component;
use Livewire\WithPagination;

class PollList extends Component
{
    use WithPagination;

    public function deletePoll($pollId)
    {
        $this->authorize('delete', $this->poll);

        Poll::where('id', $pollId)->delete();
    }
    
    public function render()
    {
        return view('livewire.poll.poll-list', [
            'polls' => Poll::where('user_id', auth()->id())->withCount('pollVotes')->paginate(10),
        ]);
    }
}
