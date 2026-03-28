<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add WhatsApp Device') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('devices.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="device_id" class="block text-gray-700 text-sm font-bold mb-2">Device ID *</label>
                            <input type="text" name="device_id" id="device_id" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('device_id') border-red-500 @enderror" 
                                required placeholder="e.g., toko-1, toko-2, my-store"
                                value="{{ old('device_id') }}">
                            <p class="text-sm text-gray-500 mt-1">Unique identifier for this WhatsApp device (letters, numbers, and hyphens only)</p>
                            @error('device_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="phone_number" class="block text-gray-700 text-sm font-bold mb-2">Phone Number (Optional)</label>
                            <input type="text" name="phone_number" id="phone_number" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                placeholder="e.g., 628123456789 (international format)"
                                value="{{ old('phone_number') }}">
                            <p class="text-sm text-gray-500 mt-1">Will be auto-filled after connection</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Add Device
                            </button>
                            <a href="{{ route('devices.index') }}" class="text-gray-500 hover:text-gray-700">Cancel</a>
                        </div>
                    </form>

                    <div class="mt-8 p-4 bg-blue-50 rounded border border-blue-200">
                        <h3 class="font-bold text-blue-800 mb-2">📱 How to connect:</h3>
                        <ol class="list-decimal list-inside space-y-1 text-sm text-blue-700">
                            <li>Add device with a unique ID (e.g., "toko-1")</li>
                            <li>After adding, <strong>QR code will appear in the terminal where Node.js is running</strong></li>
                            <li>Open WhatsApp on your phone</li>
                            <li>Go to <strong>Settings → Linked Devices → Link a Device</strong></li>
                            <li>Scan the QR code from the terminal</li>
                            <li>Once connected, status will change to "connected"</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>