<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('WhatsApp Devices') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- TOMBOL ADD DEVICE -->
                    <div class="mb-4">
                        <a href="{{ route('devices.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300">
                            + Add New Device
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-2">Device ID</th>
                                    <th class="border border-gray-300 px-4 py-2">Phone Number</th>
                                    <th class="border border-gray-300 px-4 py-2">Status</th>
                                    <th class="border border-gray-300 px-4 py-2">Last Connected</th>
                                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($devices as $device)
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2">{{ $device->device_id }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $device->phone_number ?? '-' }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <span
                                                class="px-2 py-1 rounded text-xs font-semibold {{ $device->status == 'connected' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                                {{ ucfirst($device->status) }}
                                            </span>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            {{ $device->last_connected_at ? $device->last_connected_at->diffForHumans() : '-' }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <a href="{{ route('devices.show', $device) }}"
                                                class="text-blue-500 hover:text-blue-700 mr-2">View</a>
                                            <button
                                                onclick="refreshStatus('{{ $device->id }}', '{{ $device->device_id }}')"
                                                class="text-green-500 hover:text-green-700 mr-2">Refresh</button>
                                            <form action="{{ route('devices.destroy', $device) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700"
                                                    onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="border border-gray-300 px-4 py-2 text-center text-gray-500">
                                            No devices yet. Click "Add New Device" to connect WhatsApp.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 p-4 bg-yellow-50 rounded border border-yellow-200">
                        <h3 class="font-bold text-yellow-800 mb-2">⚠️ Important Notes:</h3>
                        <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
                            <li>Make sure Node.js bridge is running: <code class="bg-gray-200 px-1 rounded">cd wa-bridge
                                    && node index.js</code></li>
                            <li>After adding device, QR code will appear in the Node.js terminal</li>
                            <li>Open WhatsApp on phone → Settings → Linked Devices → Link a Device → Scan QR code</li>
                            <li>Status will automatically update when connected</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function refreshStatus(deviceId, deviceName) {
            fetch(`/devices/${deviceId}/refresh-status`)
                .then(response => response.json())
                .then(data => {
                    if (data.connected) {
                        alert('✅ Device ' + deviceName + ' is connected!');
                        location.reload();
                    } else {
                        alert('❌ Device ' + deviceName +
                            ' is disconnected.\n\nPlease scan QR code from Node.js terminal.');
                    }
                })
                .catch(error => {
                    alert('Error checking status. Make sure Node.js is running.');
                });
        }
    </script>
</x-app-layout>
