<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Contact') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('contacts.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                       class="w-full border border-gray-300 rounded-lg p-2 @error('name') border-red-500 @enderror">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" required
                                       placeholder="628123456789"
                                       class="w-full border border-gray-300 rounded-lg p-2 @error('phone') border-red-500 @enderror">
                                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                <p class="text-xs text-gray-500">Format: 628xxxxxxxxx (tanpa + atau 0)</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       class="w-full border border-gray-300 rounded-lg p-2">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea name="address" rows="2" class="w-full border border-gray-300 rounded-lg p-2">{{ old('address') }}</textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg p-2">{{ old('notes') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Groups</label>
                                <select name="groups[]" multiple class="w-full border border-gray-300 rounded-lg p-2">
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500">Hold Ctrl/Cmd to select multiple</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                                <select name="tags[]" multiple class="w-full border border-gray-300 rounded-lg p-2">
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500">Hold Ctrl/Cmd to select multiple</p>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                                Save Contact
                            </button>
                            <a href="{{ route('contacts.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>