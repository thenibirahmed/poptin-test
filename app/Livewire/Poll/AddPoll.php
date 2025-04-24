<?php

namespace App\Livewire\Poll;

use App\Models\Poll;
use Livewire\Component;
use App\Services\PollService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AddPoll extends Component
{
    public $poll;
    
    public $pollOptions = ['Option 1'];

    protected PollService $pollService;

    public function boot(PollService $pollService)
    {
        $this->pollService = $pollService;
    }

    public function mount()
    {
        if ($this->poll) {
            $poll = Poll::find($this->poll);

            $this->authorize('update', $poll);

            $this->pollOptions = $poll->pollOptions->pluck('option')->toArray();
            $this->poll = $poll->only(['id', 'name', 'question']);
        } else {
            $this->poll = Poll::CREATE_SKELETON;
        }
    }

    public function addPollOption()
    {
        $this->pollOptions[] = '';
    }

    public function removePollOption($index)
    {
        unset($this->pollOptions[$index]);
        $this->pollOptions = array_values($this->pollOptions);
    }

    public function savePoll()
    {
        $validated = $this->validate();

        if ($this->isEditing()) {
            $poll = Poll::find($this->poll['id']);
            $this->pollService->updatePoll($poll, $validated['poll'], $this->pollOptions);
            $sessionMessage = 'Poll updated successfully.';
        } else {
            $validated['poll']['user_id'] = Auth::id();
            $validated['poll']['uuid'] = (string) Str::uuid();

            $this->pollService->createPoll($validated['poll'], $this->pollOptions);
            $sessionMessage = 'Poll created successfully.';
        }

        session()->flash('poll-added', $sessionMessage);
        return $this->redirect(route('dashboard'), true);
    }

    public function isEditing()
    {
        return isset($this->poll['id']);
    }

    public function rules()
    {
        return [
            'poll.name' => 'required|string|max:255',
            'poll.question' => 'required|string|max:255',
            'pollOptions' => 'required|array|min:1',
            'pollOptions.*' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'poll.name.required' => 'The poll name is required.',
            'poll.question.required' => 'The poll question is required.',
            'pollOptions.*.required' => 'Each poll option is required.',
        ];
    }

    public function render()
    {
        return view('livewire.poll.add-poll');
    }
}
