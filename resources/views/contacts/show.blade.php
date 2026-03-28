<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contact Details') }}: {{ $contact->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div><strong>Name:</strong> {{ $contact->name }}</div>
                        <div><strong>Phone:</strong> {{ $contact->phone }}</div>
                        <div><strong>Email:</strong> {{ $contact->email ?? '-' }}</div>
                        <div><strong>Address:</strong> {{ $contact->address ?? '-' }}</div>
                        <div class="md:col-span-2"><strong>Notes:</strong> {{ $contact->notes ?? '-' }}</div>
                        <div><strong>Groups:</strong> 
                            @foreach($contact->groups as $group)
                                <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded mr-1">{{ $group->name }}</span>
                            @endforeach
                        </div>
                        <div><strong>Tags:</strong>
                            @foreach($contact->tags as $tag)
                                <span class="inline-block text-white px-2 py-1 rounded mr-1" style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                        <div><strong>Created At:</strong> {{ $contact->created_at->format('Y-m-d H:i:s') }}</div>
                        <div><strong>Updated At:</strong> {{ $contact->updated_at->format('Y-m-d H:i:s') }}</div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('contacts.edit', $contact) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white px-4 py-2 rounded">Edit</a>
                        <a href="{{ route('contacts.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>