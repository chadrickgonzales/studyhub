<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Study Rooms') }}
            </h2>
            <a href="{{ route('study-rooms.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Create Room
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filter -->
            <div class="mb-8">
                <form action="{{ route('study-rooms.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Search by room name...">
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" name="subject" id="subject" value="{{ request('subject') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Filter by subject...">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-gray-100 px-4 py-2 rounded-md hover:bg-gray-200">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <!-- Study Rooms Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($studyRooms as $room)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        @if($room->banner_image)
                            <img src="{{ Storage::url($room->banner_image) }}" alt="{{ $room->name }}" class="w-full h-32 object-cover">
                        @else
                            <div class="w-full h-32 bg-gray-100 flex items-center justify-center">
                                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        @endif
                        
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900">{{ $room->name }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ $room->subject }}</p>
                            
                            @if($room->description)
                                <p class="mt-2 text-sm text-gray-600">{{ Str::limit($room->description, 100) }}</p>
                            @endif

                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    {{ $room->members_count }} members
                                </div>

                                @if($room->members->contains(auth()->id()))
                                    <a href="{{ route('study-rooms.show', $room) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        Enter Room
                                    </a>
                                @else
                                    <form action="{{ route('study-rooms.join', ['studyRoom' => $room->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200">
                                            Join Room
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3">
                        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                            <p class="text-gray-500">No study rooms found. Why not create one?</p>
                            <a href="{{ route('study-rooms.create') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Create Study Room
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $studyRooms->links() }}
            </div>
        </div>
    </div>
</x-app-layout> 