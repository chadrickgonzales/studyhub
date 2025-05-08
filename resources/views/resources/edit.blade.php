<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Resource') }} - {{ $resource->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('resources.update', [$studyRoom, $resource]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" id="title" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ old('title', $resource->title) }}" required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="4" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>{{ old('description', $resource->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                            <select name="type" id="type" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                                <option value="">Select a type</option>
                                <option value="link" {{ old('type', $resource->type) == 'link' ? 'selected' : '' }}>Link</option>
                                <option value="file" {{ old('type', $resource->type) == 'file' ? 'selected' : '' }}>File</option>
                                <option value="document" {{ old('type', $resource->type) == 'document' ? 'selected' : '' }}>Document</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="url-field" class="mb-4" style="display: none;">
                            <label for="url" class="block text-sm font-medium text-gray-700">URL</label>
                            <input type="url" name="url" id="url" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ old('url', $resource->url) }}">
                            @error('url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="file-field" class="mb-4" style="display: none;">
                            <label for="file" class="block text-sm font-medium text-gray-700">File</label>
                            @if($resource->file_path)
                                <div class="mb-2">
                                    <p class="text-sm text-gray-500">Current file: {{ basename($resource->file_path) }}</p>
                                </div>
                            @endif
                            <input type="file" name="file" id="file" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300">
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('resources.show', [$studyRoom, $resource]) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Resource
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('type').addEventListener('change', function() {
            const urlField = document.getElementById('url-field');
            const fileField = document.getElementById('file-field');
            
            if (this.value === 'link') {
                urlField.style.display = 'block';
                fileField.style.display = 'none';
            } else if (this.value === 'file') {
                urlField.style.display = 'none';
                fileField.style.display = 'block';
            } else {
                urlField.style.display = 'none';
                fileField.style.display = 'none';
            }
        });

        // Trigger change event on page load to set initial state
        document.getElementById('type').dispatchEvent(new Event('change'));
    </script>
    @endpush
</x-app-layout> 