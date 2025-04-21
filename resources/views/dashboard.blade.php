<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto text-white">
                    <h1 class="text-base font-semibold text-white-900">Polls</h1>
                </div>
                <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                    <a href="{{ route('polls.add') }}" wire:navigate class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add Poll</a>
                </div>
            </div>
            @session('poll-added')
                <flux:callout variant="success" icon="check-circle" heading="{{ session('poll-added') }}" class="mt-2" />
            @endsession
            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <div class="overflow-hidden shadow sm:rounded-lg">
                            <livewire:poll.poll-list />
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
