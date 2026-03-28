<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tag Details') }}: {{ $tag->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div><strong>Name:</strong> {{ $tag->name }}</div>
                            <div>
                                <strong>Color:</strong>
                                <span class="inline-block w-6 h-6 rounded-full align-middle" style="background-color: {{ $tag->color }}"></span>
                                {{ $tag->color }}
                            </div>
                            <div><strong>Contacts Count:</strong> {{ $tag->contacts->count() }}</div>
                            <div><strong>Created At:</strong> {{ $tag->created_at->format('Y-m-d H:i:s') }}</div>
                        </div>
                    </div>

                    <h3 class="font-bold text-lg mb-2">Contacts with this tag:</h3>
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-2">Name</th>
                                    <th class="border border-gray-300 px-4 py-2">Phone</th>
                                    <th class="border border-gray-300 px-4 py-2">Email</th>
                                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tag->contacts as $contact)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $contact->name }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $contact->phone }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $contact->email ?? '-' }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <a href="{{ route('contacts.show', $contact) }}" class="text-blue-500 hover:text-blue-700">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="border border-gray-300 px-4 py-2 text-center text-gray-500">
                                        No contacts with this tag.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('tags.edit', $tag) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white px-4 py-2 rounded">Edit</a>
                        <a href="{{ route('tags.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>