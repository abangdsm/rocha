<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Send WhatsApp Message') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form id="sendForm">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Select Device</label>
                            <select id="device_id" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                @foreach(Auth::user()->devices as $device)
                                    <option value="{{ $device->device_id }}">{{ $device->device_id }} ({{ $device->status }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">To (Phone Number)</label>
                            <input type="text" id="to" class="shadow border rounded w-full py-2 px-3 text-gray-700" placeholder="628123456789" required>
                            <p class="text-sm text-gray-500">Format: 628xxxxxxxxx (tanpa + atau 0)</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Message</label>
                            <textarea id="message" rows="4" class="shadow border rounded w-full py-2 px-3 text-gray-700" placeholder="Type your message here..." required></textarea>
                        </div>

                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md">
                            Send Message
                        </button>
                    </form>

                    <div id="result" class="mt-4 hidden">
                        <div id="resultMessage"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('sendForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const deviceId = document.getElementById('device_id').value;
            const to = document.getElementById('to').value;
            const message = document.getElementById('message').value;
            
            const resultDiv = document.getElementById('result');
            const resultMessage = document.getElementById('resultMessage');
            
            resultDiv.classList.remove('hidden');
            resultMessage.innerHTML = '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">Sending...</div>';
            
            try {
                const response = await fetch('/api/whatsapp/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        accountId: deviceId,
                        to: to,
                        message: message
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    resultMessage.innerHTML = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">✅ Message sent successfully!</div>';
                    document.getElementById('to').value = '';
                    document.getElementById('message').value = '';
                } else {
                    resultMessage.innerHTML = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">❌ Error: ' + (data.error || 'Unknown error') + '</div>';
                }
            } catch (error) {
                resultMessage.innerHTML = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">❌ Error: ' + error.message + '</div>';
            }
        });
    </script>
</x-app-layout>