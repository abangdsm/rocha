<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Device Details') }}: {{ $device->device_id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="font-semibold">Device ID:</div>
                        <div>{{ $device->device_id }}</div>

                        <div class="font-semibold">Phone Number:</div>
                        <div>{{ $device->phone_number ?? '-' }}</div>

                        <div class="font-semibold">Status:</div>
                        <div>
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $device->status == 'connected' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                {{ ucfirst($device->status) }}
                            </span>
                        </div>

                        <div class="font-semibold">Last Connected:</div>
                        <div>{{ $device->last_connected_at ? $device->last_connected_at->format('Y-m-d H:i:s') : '-' }}</div>

                        <div class="font-semibold">Created At:</div>
                        <div>{{ $device->created_at->format('Y-m-d H:i:s') }}</div>

                        <div class="font-semibold">Updated At:</div>
                        <div>{{ $device->updated_at->format('Y-m-d H:i:s') }}</div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('devices.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            ← Back to Devices
                        </a>
                        <button onclick="refreshStatus()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Refresh Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function refreshStatus() {
            fetch('{{ route("devices.refresh-status", $device) }}')
                .then(response => response.json())
                .then(data => {
                    if(data.connected) {
                        alert('✅ Device is connected!');
                        location.reload();
                    } else {
                        alert('❌ Device is disconnected. Please check Node.js terminal for QR code.');
                    }
                })
                .catch(error => {
                    alert('Error checking status. Make sure Node.js is running.');
                });
        }
    </script>
</x-app-layout>