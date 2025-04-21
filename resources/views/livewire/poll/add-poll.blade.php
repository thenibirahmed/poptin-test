<form class="p-2" wire:submit.prevent='savePoll'>
    <div>
        <flux:input wire:model='poll.name' label="Name" autofocus />
    </div>
    <div class="mt-5">
        <flux:input wire:model='poll.question' label="Question" />
    </div>
    <div class="mt-5">
        @forelse ($pollOptions as $pollOptionIndex => $pollOption)
            <div class="mt-2 flex items-end gap-2">
                <div class="flex-1">
                    <flux:input wire:model="pollOptions.{{ $pollOptionIndex }}" label="Option {{ $pollOptionIndex + 1 }}" class="w-full" />
                </div>
                @if (count($pollOptions) > 1)
                    <flux:button size="sm" icon="trash" wire:click="removePollOption({{ $pollOptionIndex }})" />
                @endif
            </div>
        @empty
            <div class="text-red-200 text-sm">No options available!</div>
        @endforelse

        <flux:button size="sm" class="mt-4" wire:click="addPollOption">
            Add Option
        </flux:button>
    </div>
    <div class="mt-5">
        <flux:button type="submit">Add Poll</flux:button>
    </div>
</form>
