<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contacts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Action Buttons -->
                    <div class="mb-4 flex flex-wrap gap-2">
                        <a href="{{ route('contacts.create') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition">
                            + Add New Contact
                        </a>
                        
                        <button onclick="document.getElementById('importForm').classList.toggle('hidden')"
                                class="bg-green-500 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition">
                            Import Excel
                        </button>
                        
                        <a href="{{ route('contacts.export') }}" 
                           class="bg-yellow-500 hover:bg-yellow-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition">
                            Export Excel
                        </a>
                    </div>

                    <!-- Import Form (Hidden by default) -->
                    <div id="importForm" class="hidden mb-4 p-4 border border-gray-300 rounded-lg">
                        <form action="{{ route('contacts.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="flex gap-2 items-end">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Excel File</label>
                                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                                           class="w-full border border-gray-300 rounded-lg p-2">
                                    <p class="text-xs text-gray-500 mt-1">Format: Name, Phone, Email, Address, Notes</p>
                                </div>
                                <button type="submit" 
                                        class="bg-green-500 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg">
                                    Upload
                                </button>
                                <button type="button" onclick="document.getElementById('importForm').classList.add('hidden')"
                                        class="bg-gray-500 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Filter & Search -->
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-2">
                        <form action="{{ route('contacts.index') }}" method="GET" class="col-span-2 flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search by name, phone, email..."
                                   class="flex-1 border border-gray-300 rounded-lg p-2">
                            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white px-4 rounded-lg">Search</button>
                            @if(request('search') || request('group_id') || request('tag_id'))
                                <a href="{{ route('contacts.index') }}" class="bg-red-500 hover:bg-red-700 text-white px-4 rounded-lg">Clear</a>
                            @endif
                        </form>
                        
                        <div class="flex gap-2">
                            <select name="group_id" onchange="this.form.submit()" form="filterForm" class="border border-gray-300 rounded-lg p-2 flex-1">
                                <option value="">All Groups</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <select name="tag_id" onchange="this.form.submit()" form="filterForm" class="border border-gray-300 rounded-lg p-2 flex-1">
                                <option value="">All Tags</option>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" {{ request('tag_id') == $tag->id ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <form id="filterForm" action="{{ route('contacts.index') }}" method="GET" class="hidden"></form>
                    </div>

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Contacts Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-2">Name</th>
                                    <th class="border border-gray-300 px-4 py-2">Phone</th>
                                    <th class="border border-gray-300 px-4 py-2">Email</th>
                                    <th class="border border-gray-300 px-4 py-2">Groups</th>
                                    <th class="border border-gray-300 px-4 py-2">Tags</th>
                                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contacts as $contact)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $contact->name }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $contact->phone }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $contact->email ?? '-' }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        @foreach($contact->groups as $group)
                                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                                                {{ $group->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        @foreach($contact->tags as $tag)
                                            <span class="inline-block text-white text-xs px-2 py-1 rounded mr-1 mb-1" style="background-color: {{ $tag->color }}">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <a href="{{ route('contacts.show', $contact) }}" class="text-blue-500 hover:text-blue-700 mr-2">View</a>
                                        <a href="{{ route('contacts.edit', $contact) }}" class="text-yellow-500 hover:text-yellow-700 mr-2">Edit</a>
                                        <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Delete this contact?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="border border-gray-300 px-4 py-2 text-center text-gray-500">
                                        No contacts yet. Click "Add New Contact" to get started.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $contacts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>