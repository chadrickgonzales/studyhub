<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-gray-800">Room Categories</h2>
                        <button @click="$dispatch('open-modal', 'create-category')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                            Create Category
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($categories as $category)
                            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                                <div class="flex items-center mb-4">
                                    @if($category->icon)
                                        <i class="{{ $category->icon }} text-2xl text-gray-600 mr-3"></i>
                                    @endif
                                    <h3 class="text-xl font-semibold text-gray-800">{{ $category->name }}</h3>
                                </div>
                                <p class="text-gray-600 mb-4">{{ $category->description }}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">{{ $category->study_rooms_count }} rooms</span>
                                    <div class="flex space-x-2">
                                        <button @click="$dispatch('open-modal', 'edit-category-{{ $category->id }}')" class="text-blue-500 hover:text-blue-600">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('room-categories.destroy', $category) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-600" onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Modal -->
                            <x-modal name="edit-category-{{ $category->id }}">
                                <form action="{{ route('room-categories.update', $category) }}" method="POST" class="p-6">
                                    @csrf
                                    @method('PUT')
                                    <h2 class="text-lg font-medium text-gray-900 mb-4">Edit Category</h2>
                                    <div class="mb-4">
                                        <x-input-label for="name" value="Name" />
                                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ $category->name }}" required />
                                    </div>
                                    <div class="mb-4">
                                        <x-input-label for="description" value="Description" />
                                        <x-textarea id="description" name="description" class="mt-1 block w-full">{{ $category->description }}</x-textarea>
                                    </div>
                                    <div class="mb-4">
                                        <x-input-label for="icon" value="Icon Class" />
                                        <x-text-input id="icon" name="icon" type="text" class="mt-1 block w-full" value="{{ $category->icon }}" />
                                    </div>
                                    <div class="mt-6 flex justify-end">
                                        <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                        <x-primary-button class="ml-3">Update Category</x-primary-button>
                                    </div>
                                </form>
                            </x-modal>
                        @endforeach
                    </div>

                    <!-- Create Modal -->
                    <x-modal name="create-category">
                        <form action="{{ route('room-categories.store') }}" method="POST" class="p-6">
                            @csrf
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Create Category</h2>
                            <div class="mb-4">
                                <x-input-label for="name" value="Name" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                            </div>
                            <div class="mb-4">
                                <x-input-label for="description" value="Description" />
                                <x-textarea id="description" name="description" class="mt-1 block w-full"></x-textarea>
                            </div>
                            <div class="mb-4">
                                <x-input-label for="icon" value="Icon Class" />
                                <x-text-input id="icon" name="icon" type="text" class="mt-1 block w-full" />
                            </div>
                            <div class="mt-6 flex justify-end">
                                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                <x-primary-button class="ml-3">Create Category</x-primary-button>
                            </div>
                        </form>
                    </x-modal>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 