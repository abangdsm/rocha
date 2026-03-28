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

                    <!-- MODAL QR CODE -->
                    <div id="qrModal"
                        class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
                        <div class="bg-white rounded-lg p-6 max-w-md w-full">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold">Scan QR Code</h3>
                                <button onclick="closeQRModal()"
                                    class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                            </div>
                            <div class="flex justify-center">
                                <img id="qrCodeImg" src="" alt="QR Code" class="w-64 h-64">
                            </div>
                            <p class="text-center text-gray-600 mt-4">Scan dengan WhatsApp untuk menghubungkan device
                            </p>
                            <p class="text-center text-sm text-gray-500 mt-2">Device ID: <span id="qrDeviceId"></span>
                            </p>
                        </div>
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
                                    <tr id="device-{{ $device->device_id }}">
                                        <td class="border border-gray-300 px-4 py-2 device-id">{{ $device->device_id }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2 phone-number">
                                            {{ $device->phone_number ?? '-' }}</td>
                                        <td class="border border-gray-300 px-4 py-2 status-badge">
                                            <span
                                                class="px-2 py-1 rounded text-xs font-semibold {{ $device->status == 'connected' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                                {{ ucfirst($device->status) }}
                                            </span>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2 last-connected">
                                            {{ $device->last_connected_at ? $device->last_connected_at->diffForHumans() : '-' }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <a href="{{ route('devices.show', $device) }}"
                                                class="text-blue-500 hover:text-blue-700 mr-2">View</a>
                                            <button
                                                onclick="refreshStatus({{ $device->id }}, '{{ $device->device_id }}')"
                                                class="text-green-500 hover:text-green-700 mr-2">Refresh
                                            </button>
                                            <button
                                                onclick="disconnectDevice('{{ $device->device_id }}', {{ $device->id }})"
                                                class="text-orange-500 hover:text-orange-700 mr-2">
                                                Disconnect
                                            </button>
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

    <!-- Socket.io Script -->
    <script src="https://cdn.socket.io/4.5.0/socket.io.min.js"></script>
    <script>
        // Variable
        let qrCheckInterval = null;

        // Connect ke Socket.io
        const socket = io('http://localhost:3000');

        socket.on('connect', () => {
            console.log('✅ Connected to socket.io server');
        });

        // Listen untuk device-status event
        socket.on('device-status', (data) => {
            console.log('📡 Device status update:', data);

            // Cari baris tabel berdasarkan device ID
            const row = document.querySelector(`#device-${data.deviceId}`);

            if (row) {
                const statusBadge = row.querySelector('.status-badge span');
                const phoneCell = row.querySelector('.phone-number');
                const lastConnectedCell = row.querySelector('.last-connected');

                if (data.status === 'connected') {
                    // Update status
                    if (statusBadge) {
                        statusBadge.className =
                            'px-2 py-1 rounded text-xs font-semibold bg-green-200 text-green-800';
                        statusBadge.innerText = 'Connected';
                    }
                    // Update nomor HP
                    if (phoneCell && data.phoneNumber) {
                        phoneCell.innerText = data.phoneNumber;
                    }
                    // Update last connected
                    if (lastConnectedCell) {
                        lastConnectedCell.innerText = 'Just now';
                    }

                    // Tutup modal QR
                    closeQRModal();

                    // Tampilkan notifikasi
                    showNotification('✅ Device ' + data.deviceId + ' connected!', 'success');

                } else if (data.status === 'disconnected') {
                    // Update status
                    if (statusBadge) {
                        statusBadge.className = 'px-2 py-1 rounded text-xs font-semibold bg-red-200 text-red-800';
                        statusBadge.innerText = 'Disconnected';
                    }
                    showNotification('❌ Device ' + data.deviceId + ' disconnected', 'error');
                }
            }
        });

        // Function show notification
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded shadow-lg z-50 transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } text-white font-semibold`;
            notification.innerText = message;
            document.body.appendChild(notification);

            // Animasi fade out
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // QR Modal functions
        function showQRModal(deviceId, qrString) {
            document.getElementById('qrDeviceId').innerText = deviceId;
            document.getElementById('qrCodeImg').src =
                `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(qrString)}`;
            document.getElementById('qrModal').classList.remove('hidden');
            document.getElementById('qrModal').classList.add('flex');

            if (qrCheckInterval) {
                clearInterval(qrCheckInterval);
                qrCheckInterval = null;
            }
        }

        function closeQRModal() {
            document.getElementById('qrModal').classList.add('hidden');
            document.getElementById('qrModal').classList.remove('flex');
        }

        function checkQR(deviceId) {
            if (qrCheckInterval) clearInterval(qrCheckInterval);

            qrCheckInterval = setInterval(async () => {
                try {
                    const response = await fetch(`/api/whatsapp/get-qr/${deviceId}`);
                    const data = await response.json();

                    if (data.qr) {
                        showQRModal(deviceId, data.qr);
                        if (qrCheckInterval) clearInterval(qrCheckInterval);
                    }
                } catch (e) {
                    console.error('Error fetching QR:', e);
                }
            }, 2000);
        }

        // Refresh status function
        function refreshStatus(deviceId, deviceName) {
            fetch(`/devices/${deviceId}/refresh-status`)
                .then(response => response.json())
                .then(data => {
                    if (data.connected) {
                        showNotification('✅ Device ' + deviceName + ' is connected!', 'success');
                        // Reload halaman setelah 1 detik agar data terbaru
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification('❌ Device ' + deviceName + ' is disconnected', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error checking status', 'error');
                });
        }

        // Disconected Device
        function disconnectDevice(deviceId, deviceDbId) {
        if (confirm('Are you sure you want to disconnect ' + deviceId + '?')) {
            fetch(`/devices/${deviceDbId}/disconnect`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Device ' + deviceId + ' disconnected', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Failed to disconnect', 'error');
                }
            });
        }
}

        // Saat halaman load
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('new_device_id'))
                checkQR('{{ session('new_device_id') }}');
            @endif
        });
    </script>
</x-app-layout>
