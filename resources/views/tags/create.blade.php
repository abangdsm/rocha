<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Tag') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('tags.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tag Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full border border-gray-300 rounded-lg p-2">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <div class="flex gap-2 items-center">
                                <input type="color" name="color" value="{{ old('color', '#3B82F6') }}"
                                       class="w-16 h-10 border border-gray-300 rounded cursor-pointer">
                                <input type="text" name="color_text" id="color_text" value="{{ old('color', '#3B82F6') }}"
                                       class="flex-1 border border-gray-300 rounded-lg p-2" placeholder="#3B82F6">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Choose a color for the tag</p>
                            @error('color') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                                Save Tag
                            </button>
                            <a href="{{ route('tags.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sync color picker with text input
        const colorPicker = document.querySelector('input[type="color"]');
        const colorText = document.getElementById('color_text');
        
        colorPicker.addEventListener('change', () => {
            colorText.value = colorPicker.value;
        });
        
        colorText.addEventListener('input', () => {
            colorPicker.value = colorText.value;
        });
    </script>
</x-app-layout>