<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Resources') }} - {{ $studyRoom->name }}
            </h2>
            @can('create', [App\Models\Resource::class, $studyRoom])
                <a href="{{ route('resources.create', $studyRoom) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add Resource
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($resources->isEmpty())
                        <p class="text-gray-500 text-center py-4">No resources available yet.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($resources as $resource)
                                <div class="bg-white rounded-lg shadow-md p-4">
                                    <h3 class="text-lg font-semibold mb-2">{{ $resource->title }}</h3>
                                    <p class="text-gray-600 mb-4">{{ Str::limit($resource->description, 100) }}</p>
                                    
                                    <div class="flex items-center text-sm text-gray-500 mb-4">
                                        <span class="mr-4">
                                            <i class="fas fa-user"></i> {{ $resource->user->name }}
                                        </span>
                                        <span>
                                            <i class="fas fa-clock"></i> {{ $resource->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $resource->ratings->avg('rating') ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                @endfor
                                                <span class="ml-2 text-sm text-gray-500">
                                                    ({{ $resource->ratings->count() }})
                                                </span>
                                            </div>
                                        </div>
                                        <a href="{{ route('resources.show', [$studyRoom, $resource]) }}" class="text-blue-500 hover:text-blue-700">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $resources->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 