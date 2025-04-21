<div>
    <h1 class="mb-1 font-medium border-b pb-2">Question: 
        {{ $poll?->question }}
    </h1>
    <div class="mt-6">
        <flux:radio.group wire:model="selectedOption" label="Select your poll" :disabled="$this->getUsersVote">
            @if ($poll?->pollOptions)
                @foreach ($poll->pollOptions as $pollOption)
                    <flux:radio :value="$pollOption->id" :label="$pollOption->option . ' ('.$pollOption->votes?->count().')'" class="mt-3" />
                @endforeach
            @endif
        </flux:radio.group>
    </div>
    <div class="mt-10">
        @if ($this->getUsersVote)
            <div class="text-sm text-gray-500">
                You have voted for option: <span class="font-bold">{{ $this->getUsersVote?->pollOption?->option }}</span>
            </div>
        @else
            <flux:button wire:click.prevent="vote" size="sm">
                Submit Vote
            </flux:button>
        @endif
    </div>
</div>
