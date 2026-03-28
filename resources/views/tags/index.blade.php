<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contact Tags') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-4">
                        <a href="{{ route('tags.create') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition">
                            + Add New Tag
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
                                    <th class="border border-gray-300 px-4 py-2">Color</th>
                                    <th class="border border-gray-300 px-4 py-2">Contacts Count</th>
                                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                                 </tr>
                            </thead>
                            <tbody>
                                @forelse($tags as $tag)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $tag->name }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <span class="inline-block w-6 h-6 rounded-full" style="background-color: {{ $tag->color }}"></span>
                                        {{ $tag->color }}
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $tag->contacts->count() }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <a href="{{ route('tags.show', $tag) }}" class="text-blue-500 hover:text-blue-700 mr-2">View</a>
                                        <a href="{{ route('tags.edit', $tag) }}" class="text-yellow-500 hover:text-yellow-700 mr-2">Edit</a>
                                        <form action="{{ route('tags.destroy', $tag) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Delete this tag?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="border border-gray-300 px-4 py-2 text-center text-gray-500">
                                        No tags yet. Click "Add New Tag" to get started.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $tags->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>