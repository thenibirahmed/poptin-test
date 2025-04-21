<div>
    <h1 class="mb-1 font-medium border-b pb-2">Question: 
        {{ $poll?->question }}
    </h1>
    <div class="mt-6">
        <flux:radio.group wire:model="selectedOption" label="Select your poll">
            @if ($poll?->pollOptions)
                @foreach ($poll->pollOptions as $pollOption)
                    <flux:radio :value="$pollOption->id" :label="$pollOption->option" class="mt-3" />
                @endforeach
            @endif
        </flux:radio.group>
    </div>
    <div class="mt-10">
        <flux:button wire:click.prevent="vote" size="sm">
            Submit Vote
        </flux:button>
    </div>
</div>
