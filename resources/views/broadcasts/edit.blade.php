<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Broadcast') }}: {{ $broadcast->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('broadcasts.update', $broadcast) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Broadcast Name *</label>
                            <input type="text" name="name" value="{{ old('name', $broadcast->name) }}" required
                                   class="w-full border border-gray-300 rounded-lg p-2">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Device *</label>
                            <select name="device_id" required class="w-full border border-gray-300 rounded-lg p-2">
                                <option value="">Select Device</option>
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}" {{ old('device_id', $broadcast->device_id) == $device->id ? 'selected' : '' }}>
                                        {{ $device->device_id }} ({{ $device->phone_number ?? 'No number' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('device_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                            <textarea name="message" rows="5" required
                                      class="w-full border border-gray-300 rounded-lg p-2">{{ old('message', $broadcast->message) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">You can use variables: {name}, {phone}</p>
                            @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Schedule (Optional)</label>
                            <input type="datetime-local" name="scheduled_at" 
                                   value="{{ old('scheduled_at', $broadcast->scheduled_at ? $broadcast->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full border border-gray-300 rounded-lg p-2">
                            <p class="text-xs text-gray-500 mt-1">Leave empty to send immediately</p>
                        </div>

                        <div class="bg-yellow-50 p-4 rounded mb-4">
                            <p class="text-sm text-yellow-800">⚠️ Note: You cannot change the target contacts after creation. This broadcast is set to send to {{ $broadcast->total_contacts }} contacts.</p>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                                Update Broadcast
                            </button>
                            <a href="{{ route('broadcasts.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>