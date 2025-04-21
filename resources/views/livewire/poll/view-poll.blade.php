<div>
    <h1 class="mb-1 font-medium border-b pb-2">Question: 
        {{ $poll->question }}
    </h1>
    <div class="mt-6">
        <h2 class="text-sm mb-5">Options</h2>
        @foreach ($poll->pollOptions as $pollOption)
            <flux:checkbox :label="$pollOption->option" class="mt-3" />
        @endforeach
    </div>
</div>
