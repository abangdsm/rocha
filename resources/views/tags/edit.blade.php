<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Tag') }}: {{ $tag->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('tags.update', $tag) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tag Name *</label>
                            <input type="text" name="name" value="{{ old('name', $tag->name) }}" required
                                   class="w-full border border-gray-300 rounded-lg p-2">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <div class="flex gap-2 items-center">
                                <input type="color" name="color" value="{{ old('color', $tag->color) }}"
                                       class="w-16 h-10 border border-gray-300 rounded cursor-pointer">
                                <input type="text" name="color_text" id="color_text" value="{{ old('color', $tag->color) }}"
                                       class="flex-1 border border-gray-300 rounded-lg p-2">
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                                Update Tag
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