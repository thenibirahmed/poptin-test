<x-layouts.app :title="__('Poll Analytics')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto text-white">
                    <h1 class="text-base font-semibold text-white-900">Poll Analytics</h1>
                </div>
            </div>
            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <div class="overflow-hidden shadow sm:rounded-lg">
                            <livewire:poll.poll-analytics :poll="$poll" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
