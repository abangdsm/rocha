<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contact Groups') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-4">
                        <a href="{{ route('groups.create') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition">
                            + Add New Group
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-2">Name</th>
                                    <th class="border border-gray-300 px-4 py-2">Description</th>
                                    <th class="border border-gray-300 px-4 py-2">Contacts Count</th>
                                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groups as $group)
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2">{{ $group->name }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $group->description ?? '-' }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $group->contacts->count() }}</td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <a href="{{ route('groups.show', $group) }}" class="text-blue-500 hover:text-blue-700 mr-2">View</a>
                                            <a href="{{ route('groups.edit', $group) }}" class="text-yellow-500 hover:text-yellow-700 mr-2">Edit</a>
                                            <form action="{{ route('groups.destroy', $group) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Delete this group?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="border border-gray-300 px-4 py-2 text-center text-gray-500">
                                            No groups yet. Click "Add New Group" to get started.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $groups->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>