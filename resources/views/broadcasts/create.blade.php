<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Broadcast') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('broadcasts.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Broadcast Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full border border-gray-300 rounded-lg p-2">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Device *</label>
                            <select name="device_id" required class="w-full border border-gray-300 rounded-lg p-2">
                                <option value="">Select Device</option>
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                                        {{ $device->device_id }} ({{ $device->phone_number ?? 'No number' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('device_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                            <textarea name="message" rows="5" required
                                      class="w-full border border-gray-300 rounded-lg p-2">{{ old('message') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">You can use variables: {name}, {phone}</p>
                            @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Send To *</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="send_type" value="all" class="mr-2" {{ old('send_type') == 'all' ? 'checked' : '' }}>
                                    All Contacts
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="send_type" value="groups" class="mr-2" id="send_to_groups">
                                    Specific Groups
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="send_type" value="tags" class="mr-2" id="send_to_tags">
                                    Specific Tags
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="send_type" value="selected" class="mr-2" id="send_to_selected">
                                    Selected Contacts
                                </label>
                            </div>
                        </div>

                        <div id="groups_select" class="mb-4 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select Groups</label>
                            <select name="groups[]" multiple class="w-full border border-gray-300 rounded-lg p-2">
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }} ({{ $group->contacts->count() }} contacts)</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                        </div>

                        <div id="tags_select" class="mb-4 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select Tags</label>
                            <select name="tags[]" multiple class="w-full border border-gray-300 rounded-lg p-2">
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }} ({{ $tag->contacts->count() }} contacts)</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                        </div>

                        <div id="contacts_select" class="mb-4 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select Contacts</label>
                            <select name="contacts[]" multiple class="w-full border border-gray-300 rounded-lg p-2">
                                @foreach($contacts as $contact)
                                    <option value="{{ $contact->id }}">{{ $contact->name }} ({{ $contact->phone }})</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Schedule (Optional)</label>
                            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}"
                                   class="w-full border border-gray-300 rounded-lg p-2">
                            <p class="text-xs text-gray-500 mt-1">Leave empty to send immediately</p>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                                Create Broadcast
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

    <script>
        const sendTypeRadios = document.querySelectorAll('input[name="send_type"]');
        const groupsSelect = document.getElementById('groups_select');
        const tagsSelect = document.getElementById('tags_select');
        const contactsSelect = document.getElementById('contacts_select');

        sendTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                groupsSelect.classList.add('hidden');
                tagsSelect.classList.add('hidden');
                contactsSelect.classList.add('hidden');
                
                if (this.value === 'groups') {
                    groupsSelect.classList.remove('hidden');
                } else if (this.value === 'tags') {
                    tagsSelect.classList.remove('hidden');
                } else if (this.value === 'selected') {
                    contactsSelect.classList.remove('hidden');
                }
            });
        });
        
        // Trigger on page load
        const checkedRadio = document.querySelector('input[name="send_type"]:checked');
        if (checkedRadio) {
            const event = new Event('change');
            checkedRadio.dispatchEvent(event);
        }
    </script>
</x-app-layout>