<div class="px-5">
    <div>
        <h1 class="mb-1 font-medium border-b pb-2 text-white">Question: 
            {{ $poll?->question }}
        </h1>
        <div class="mt-6 text-white">
            @if ($poll?->pollOptions)
                @foreach ($poll->pollOptions as $pollOption)
                    <div>
                        - {{ $pollOption->option . ' ('.$pollOption->votes?->count().')' }}
                    </div>
                @endforeach
            @endif
        </div>
        <div class="mt-6 text-white">
            Winner/s: {{ $this->getWinnerNames() }}
        </div>
        <div class="mt-6 text-white">
            <table class="min-w-full divide-y divide-white-300">
                <thead class="bg-zinc-700">
                    <tr>
                        <th scope="col" class="text-white py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white-900 sm:pl-6">User</th>
                        <th scope="col" class="px-3 text-white py-3.5 text-left text-sm font-semibold text-white-900">IP Address</th>
                        <th scope="col" class="px-3 text-white py-3.5 text-left text-sm font-semibold text-white-900">Vote</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white-200 bg-zinc-700 text-white">
                    @forelse ($pollVotes as $vote)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-white-900 sm:pl-6">{{ $vote?->user?->name ?: 'Guest' }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-white-500">{{ $vote->ip_address }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-white-500">{{ $vote->pollOption?->option }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-sm text-white-500">No votes available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-5">
                {{ $pollVotes->links() }}
            </div>
        </div>
    </div>    
</div>
