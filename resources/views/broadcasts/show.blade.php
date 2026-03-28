<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Broadcast Details') }}: {{ $broadcast->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Broadcast Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div><strong>Name:</strong> {{ $broadcast->name }}</div>
                        <div><strong>Device:</strong> {{ $broadcast->device->device_id ?? '-' }}</div>
                        <div><strong>Status:</strong> 
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                @if($broadcast->status == 'completed') bg-green-200 text-green-800
                                @elseif($broadcast->status == 'processing') bg-blue-200 text-blue-800
                                @elseif($broadcast->status == 'pending') bg-yellow-200 text-yellow-800
                                @elseif($broadcast->status == 'scheduled') bg-purple-200 text-purple-800
                                @else bg-red-200 text-red-800
                                @endif">
                                {{ ucfirst($broadcast->status) }}
                            </span>
                        </div>
                        <div><strong>Progress:</strong> 
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $broadcast->total_contacts > 0 ? ($broadcast->sent_count + $broadcast->failed_count) / $broadcast->total_contacts * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm">{{ $broadcast->sent_count }} sent, {{ $broadcast->failed_count }} failed of {{ $broadcast->total_contacts }} contacts</span>
                        </div>
                        <div><strong>Scheduled At:</strong> {{ $broadcast->scheduled_at ? $broadcast->scheduled_at->format('Y-m-d H:i:s') : '-' }}</div>
                        <div><strong>Started At:</strong> {{ $broadcast->started_at ? $broadcast->started_at->format('Y-m-d H:i:s') : '-' }}</div>
                        <div><strong>Completed At:</strong> {{ $broadcast->completed_at ? $broadcast->completed_at->format('Y-m-d H:i:s') : '-' }}</div>
                        <div class="md:col-span-2"><strong>Message:</strong> 
                            <div class="bg-gray-100 p-3 rounded mt-1 whitespace-pre-wrap">{{ $broadcast->message }}</div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2 mb-6">
                        @if($broadcast->status == 'completed' && $broadcast->failed_count > 0)
                            <form action="{{ route('broadcasts.retry', $broadcast) }}" method="POST">
                                @csrf
                                @method('POST')
                                <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white px-4 py-2 rounded">
                                    Retry Failed ({{ $broadcast->failed_count }})
                                </button>
                            </form>
                        @endif
                        
                        @if($broadcast->status == 'pending' || $broadcast->status == 'scheduled')
                            <a href="{{ route('broadcasts.edit', $broadcast) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white px-4 py-2 rounded">Edit</a>
                            <form action="{{ route('broadcasts.cancel', $broadcast) }}" method="POST">
                                @csrf
                                @method('POST')
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded" onclick="return confirm('Cancel this broadcast?')">Cancel</button>
                            </form>
                        @endif
                        
                        <a href="{{ route('broadcasts.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">Back</a>
                    </div>

                    <!-- Contacts Table -->
                    <h3 class="font-bold text-lg mb-2">Contacts Status</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-2">Name</th>
                                    <th class="border border-gray-300 px-4 py-2">Phone</th>
                                    <th class="border border-gray-300 px-4 py-2">Status</th>
                                    <th class="border border-gray-300 px-4 py-2">Sent At</th>
                                    <th class="border border-gray-300 px-4 py-2">Error</th>
                                 </tr>
                            </thead>
                            <tbody>
                                @forelse($broadcast->broadcastContacts as $bc)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $bc->contact->name ?? '-' }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $bc->contact->phone ?? '-' }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <span class="px-2 py-1 rounded text-xs font-semibold
                                            @if($bc->status == 'sent') bg-green-200 text-green-800
                                            @elseif($bc->status == 'pending') bg-yellow-200 text-yellow-800
                                            @else bg-red-200 text-red-800
                                            @endif">
                                            {{ ucfirst($bc->status) }}
                                        </span>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $bc->sent_at ? $bc->sent_at->format('Y-m-d H:i:s') : '-' }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $bc->error ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="border border-gray-300 px-4 py-2 text-center text-gray-500">
                                        No contacts in this broadcast.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $broadcast->broadcastContacts()->paginate(20)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>