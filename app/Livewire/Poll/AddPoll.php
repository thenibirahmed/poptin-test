<?php

namespace App\Livewire\Poll;

use App\Models\Poll;
use App\Models\PollOption;
use Livewire\Component;

class AddPoll extends Component
{
    public $poll = Poll::CREATE_SKELETON;

    public $pollOptions = [
        'Option 1',
    ];

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
        $validatedPoll = $this->validate();

        $poll = Poll::create($validatedPoll['poll']);

        $pollOptionToInsert = [];

        $timeStamp = now();

        foreach ($this->pollOptions as $option) {
            $pollOptionToInsert[] = [
                'poll_id' => $poll->id,
                'option' => $option,
                'created_at' => $timeStamp,
                'updated_at' => $timeStamp,
            ];
        }

        if(!empty($pollOptionToInsert)) {
            PollOption::insert($pollOptionToInsert);
        }

        session()->flash('poll-added', 'Poll created successfully.');
        return redirect()->route('dashboard');
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
