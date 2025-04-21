<div>
    <table class="min-w-full divide-y divide-white-300">
        <thead class="bg-zinc-700">
            <tr>
                <th scope="col" class="text-white py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white-900 sm:pl-6">Name</th>
                <th scope="col" class="px-3 text-white py-3.5 text-left text-sm font-semibold text-white-900">Question</th>
                <th scope="col" class="px-3 text-white py-3.5 text-left text-sm font-semibold text-white-900">Votes</th>
                <th scope="col" class="relative text-white py-3.5 pl-3 pr-4 sm:pr-6">
                    <span class="sr-only">Edit</span>
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white-200 bg-zinc-700 text-white">
            @forelse ($polls as $poll)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-white-900 sm:pl-6">{{ $poll->name }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-white-500">{{ $poll->question }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-white-500">{{ $poll->poll_votes_count }}</td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <a href="{{ route('poll.view', ['poll' => $poll->id]) }}" class="text-white hover:underline">View</a> |
                        <a wire:click.prevent='deletePoll({{ $poll->id }})' wire:confirm='Are you sure you want to delete this poll?' href="#" class="text-red-500 hover:underline">Delete</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-sm text-white-500">No polls available</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
