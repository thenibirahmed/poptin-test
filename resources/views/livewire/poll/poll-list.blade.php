<div>
    <table class="min-w-full divide-y divide-white-300">
        <thead class="bg-zinc-700">
            <tr>
                <th scope="col" class="text-white py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white-900 sm:pl-6">Name</th>
                <th scope="col" class="px-3 text-white py-3.5 text-left text-sm font-semibold text-white-900">Question</th>
                <th scope="col" class="px-3 text-white py-3.5 text-left text-sm font-semibold text-white-900">Votes</th>
                <th scope="col" class="relative text-white py-3.5 pl-3 pr-4 sm:pr-6">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white-200 bg-zinc-700 text-white">
            @forelse ($polls as $poll)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-white-900 sm:pl-6">{{ $poll->name }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-white-500">{{ $poll->question }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-white-500">{{ $poll->poll_votes_count }}</td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6"
                        x-data="{
                            text: '{{ route('poll.view', ['poll' => $poll->id]) }}',
                            copied: false,
                            copy() {
                                const el = document.createElement('textarea');
                                el.value = this.text;
                                document.body.appendChild(el);
                                el.select();
                                document.execCommand('copy');
                                document.body.removeChild(el);
                                this.copied = true;
                                setTimeout(() => {
                                    this.copied = false;
                                }, 2000);
                            }
                        }"
                    >
                        <flux:button x-text="copied ? 'Copied ✅' : 'Copy Link'" @click.prevent="copy" href="#" size="xs" variant="primary">Copy Link</flux:button>
                        <flux:button href="{{ route('poll.view', ['poll' => $poll->id]) }}" size="xs" variant="primary">View</flux:button>
                        <flux:button wire:click.prevent='deletePoll({{ $poll->id }})' wire:confirm='Are you sure you want to delete this poll?' href="#" size="xs" variant="danger">Delete</flux:button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-sm text-white-500">No polls available</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">
        {{ $polls->links() }}
    </div>
</div>
