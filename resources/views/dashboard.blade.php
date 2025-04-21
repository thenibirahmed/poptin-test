<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto text-white">
                    <h1 class="text-base font-semibold text-white-900">Polls</h1>
                </div>
                <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                    <a href="#" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add Poll</a>
                </div>
            </div>
            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <div class="overflow-hidden shadow ring-1 ring-black/5 sm:rounded-lg">
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
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-white-900 sm:pl-6">My New Poll</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-white-500">What is your name</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-white-500">5</td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            <a href="#" class="text-white hover:underline">View</a> |
                                            <a href="#" class="text-white hover:underline">Edit</a> |
                                            <a href="#" class="text-red-500 hover:underline">Delete</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
