<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Broadcasts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-4">
                        <a href="{{ route('broadcasts.create') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition">
                            + New Broadcast
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-2">Name</th>
                                    <th class="border border-gray-300 px-4 py-2">Device</th>
                                    <th class="border border-gray-300 px-4 py-2">Status</th>
                                    <th class="border border-gray-300 px-4 py-2">Progress</th>
                                    <th class="border border-gray-300 px-4 py-2">Scheduled</th>
                                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($broadcasts as $broadcast)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $broadcast->name }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $broadcast->device->device_id ?? '-' }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <span class="px-2 py-1 rounded text-xs font-semibold
                                            @if($broadcast->status == 'completed') bg-green-200 text-green-800
                                            @elseif($broadcast->status == 'processing') bg-blue-200 text-blue-800
                                            @elseif($broadcast->status == 'pending') bg-yellow-200 text-yellow-800
                                            @elseif($broadcast->status == 'scheduled') bg-purple-200 text-purple-800
                                            @else bg-red-200 text-red-800
                                            @endif">
                                            {{ ucfirst($broadcast->status) }}
                                        </span>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        @if($broadcast->total_contacts > 0)
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($broadcast->sent_count + $broadcast->failed_count) / $broadcast->total_contacts * 100 }}%"></div>
                                            </div>
                                            <span class="text-xs">{{ $broadcast->sent_count + $broadcast->failed_count }}/{{ $broadcast->total_contacts }}</span>
                                        @else
                                            0/0
                                        @endif
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        {{ $broadcast->scheduled_at ? $broadcast->scheduled_at->format('Y-m-d H:i') : '-' }}
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <a href="{{ route('broadcasts.show', $broadcast) }}" class="text-blue-500 hover:text-blue-700 mr-2">View</a>
                                        @if($broadcast->status == 'pending' || $broadcast->status == 'scheduled')
                                            <a href="{{ route('broadcasts.edit', $broadcast) }}" class="text-yellow-500 hover:text-yellow-700 mr-2">Edit</a>
                                            <form action="{{ route('broadcasts.cancel', $broadcast) }}" method="POST" class="inline">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Cancel this broadcast?')">Cancel</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('broadcasts.destroy', $broadcast) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 ml-2" onclick="return confirm('Delete this broadcast?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="border border-gray-300 px-4 py-2 text-center text-gray-500">
                                        No broadcasts yet. Click "New Broadcast" to get started.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $broadcasts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>