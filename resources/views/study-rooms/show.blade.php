<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $studyRoom->name }} - StudyHub</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100" x-data="{ scheduleModal: false, resourceModal: false }">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-2xl font-bold text-indigo-600">StudyHub</h1>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('dashboard') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('study-rooms.index') }}" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Study Rooms
                        </a>
                        <a href="{{ route('profile.edit') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Profile
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Room Header -->
        <div class="px-4 py-6 sm:px-0">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $studyRoom->name }}</h1>
                    <p class="mt-1 text-sm text-gray-600">{{ $studyRoom->description }}</p>
                </div>
                <div class="flex space-x-4">
                    <button type="button" 
                            @click="scheduleModal = true"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        Schedule Session
                    </button>
                    <button type="button"
                            @click="resourceModal = true"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Share Resources
                    </button>
                </div>
            </div>
        </div>

        <!-- Room Categories -->
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-4">
                @foreach($studyRoom->categories as $category)
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                        @if($category->icon)
                            <i class="{{ $category->icon }} mr-1"></i>
                        @endif
                        {{ $category->name }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Announcements Section -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Announcements</h2>
                @can('create', [App\Models\RoomAnnouncement::class, $studyRoom])
                    <button @click="$dispatch('open-modal', 'create-announcement')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                        New Announcement
                    </button>
                @endcan
            </div>

            <div class="space-y-4">
                @forelse($studyRoom->announcements()->with('user')->active()->orderByDesc('is_pinned')->latest()->get() as $announcement)
                    <div class="border rounded-lg p-4 {{ $announcement->is_pinned ? 'bg-yellow-50' : 'bg-white' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold">{{ $announcement->title }}</h3>
                                <p class="text-sm text-gray-500">
                                    By {{ $announcement->user->name }} • {{ $announcement->created_at->diffForHumans() }}
                                    @if($announcement->expires_at)
                                        • Expires {{ $announcement->expires_at->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                            @can('update', [$announcement, $studyRoom])
                                <div class="flex space-x-2">
                                    <button @click="$dispatch('open-modal', 'edit-announcement-{{ $announcement->id }}')" class="text-blue-500 hover:text-blue-600">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('study-rooms.announcements.destroy', [$studyRoom, $announcement]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-600" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </div>
                        <div class="mt-2">
                            <p class="text-gray-700">{{ $announcement->content }}</p>
                        </div>
                    </div>

                    <!-- Edit Announcement Modal -->
                    <x-modal name="edit-announcement-{{ $announcement->id }}">
                        <form action="{{ route('study-rooms.announcements.update', [$studyRoom, $announcement]) }}" method="POST" class="p-6">
                            @csrf
                            @method('PUT')
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Edit Announcement</h2>
                            <div class="mb-4">
                                <x-input-label for="title" value="Title" />
                                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ $announcement->title }}" required />
                            </div>
                            <div class="mb-4">
                                <x-input-label for="content" value="Content" />
                                <x-textarea id="content" name="content" class="mt-1 block w-full" required>{{ $announcement->content }}</x-textarea>
                            </div>
                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_pinned" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" value="1" {{ $announcement->is_pinned ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">Pin this announcement</span>
                                </label>
                            </div>
                            <div class="mb-4">
                                <x-input-label for="expires_at" value="Expiration Date (optional)" />
                                <x-text-input id="expires_at" name="expires_at" type="datetime-local" class="mt-1 block w-full" value="{{ $announcement->expires_at?->format('Y-m-d\TH:i') }}" />
                            </div>
                            <div class="mt-6 flex justify-end">
                                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                <x-primary-button class="ml-3">Update Announcement</x-primary-button>
                            </div>
                        </form>
                    </x-modal>
                @empty
                    <p class="text-gray-500 text-center py-4">No announcements yet.</p>
                @endforelse
            </div>

            <!-- Create Announcement Modal -->
            <x-modal name="create-announcement">
                <form action="{{ route('study-rooms.announcements.store', $studyRoom) }}" method="POST" class="p-6">
                    @csrf
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Create Announcement</h2>
                    <div class="mb-4">
                        <x-input-label for="title" value="Title" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="content" value="Content" />
                        <x-textarea id="content" name="content" class="mt-1 block w-full" required></x-textarea>
                    </div>
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_pinned" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" value="1">
                            <span class="ml-2 text-sm text-gray-600">Pin this announcement</span>
                        </label>
                    </div>
                    <div class="mb-4">
                        <x-input-label for="expires_at" value="Expiration Date (optional)" />
                        <x-text-input id="expires_at" name="expires_at" type="datetime-local" class="mt-1 block w-full" />
                    </div>
                    <div class="mt-6 flex justify-end">
                        <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                        <x-primary-button class="ml-3">Create Announcement</x-primary-button>
                    </div>
                </form>
            </x-modal>
        </div>

        <!-- Main Grid -->
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Chat Section -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg p-6" x-data="{ 
                    replyTo: null, 
                    replyToName: '', 
                    replyToContent: '',
                    typingMessage: '',
                    typingTimeout: null,
                    updateTypingStatus() {
                        clearTimeout(this.typingTimeout);
                        fetch('{{ route('study-rooms.typing.update', $studyRoom) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        });
                        this.typingTimeout = setTimeout(() => this.getTypers(), 1000);
                    },
                    async getTypers() {
                        try {
                            const response = await fetch('{{ route('study-rooms.typing.get', $studyRoom) }}');
                            const data = await response.json();
                            this.typingMessage = data.message;
                        } catch (error) {
                            console.error('Error getting typers:', error);
                        }
                    }
                }">
                    <div class="flex flex-col h-[500px]">
                        <!-- Messages -->
                        <div class="flex-1 overflow-y-auto mb-4 space-y-4" id="messages">
                            @foreach($studyRoom->messages()->with(['user', 'attachments', 'parent.user'])->latest()->get() as $message)
                                <div class="flex items-start gap-3 {{ $message->user_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                                    <img src="{{ $message->user->profile_photo_url }}" alt="{{ $message->user->name }}" class="w-8 h-8 rounded-full">
                                    <div class="flex-1 {{ $message->user_id === auth()->id() ? 'items-end' : '' }}">
                                        <!-- Reply Reference -->
                                        @if($message->parent)
                                            <div class="text-sm text-gray-500 mb-1">
                                                Replying to {{ $message->parent->user->name }}:
                                                <span class="italic">{{ Str::limit($message->parent->content, 50) }}</span>
                                            </div>
                                        @endif

                                        <div class="bg-gray-100 rounded-lg p-3 {{ $message->user_id === auth()->id() ? 'bg-blue-100' : '' }}">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <span class="text-sm font-medium">{{ $message->user->name }}</span>
                                                    <p class="mt-1">{{ $message->content }}</p>
                                                </div>
                                                @if($message->user_id === auth()->id())
                                                    <div class="flex items-center gap-1">
                                                        <button 
                                                            x-data
                                                            @click="$dispatch('open-modal', 'edit-message-{{ $message->id }}')"
                                                            class="text-xs text-gray-500 hover:text-gray-700"
                                                        >
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                        <form action="{{ route('study-rooms.messages.destroy', [$studyRoom, $message]) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-xs text-gray-500 hover:text-gray-700" onclick="return confirm('Are you sure you want to delete this message?')">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-xs mt-1 opacity-75">{{ $message->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Message Modal -->
                                <div
                                    x-data="{ show: false }"
                                    x-show="show"
                                    x-on:open-modal.window="if ($event.detail === 'edit-message-{{ $message->id }}') show = true"
                                    x-on:close-modal.window="show = false"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="fixed inset-0 z-50 overflow-y-auto"
                                    style="display: none;"
                                >
                                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                                            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                        </div>

                                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                            <form action="{{ route('study-rooms.messages.update', [$studyRoom, $message]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                    <div class="mb-4">
                                                        <label for="content" class="block text-sm font-medium text-gray-700">Edit Message</label>
                                                        <textarea
                                                            name="content"
                                                            id="content"
                                                            rows="3"
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                            required
                                                        >{{ $message->content }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                    <button
                                                        type="submit"
                                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                                                    >
                                                        Update Message
                                                    </button>
                                                    <button
                                                        type="button"
                                                        @click="show = false"
                                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                                    >
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Message Input -->
                        <form action="{{ route('study-rooms.messages.store', $studyRoom) }}" method="POST" class="mt-4">
                            @csrf
                            <div class="flex space-x-3">
                                <input type="text" 
                                       name="content" 
                                       class="flex-1 min-w-0 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                       placeholder="Type your message..."
                                       required>
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Send
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Members List -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Members</h3>
                        <div class="mt-4 space-y-4">
                            @forelse($studyRoom->members as $member)
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100">
                                        <span class="text-sm font-medium leading-none text-indigo-600">
                                            {{ substr($member->name, 0, 1) }}
                                        </span>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $member->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $member->pivot->role }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No members yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Resources -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Resources</h3>
                        <div class="mt-4 space-y-4">
                            @forelse($resources as $resource)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $resource->name }}</p>
                                            <p class="text-xs text-gray-500">
                                                {{ $resource->file_type }} • 
                                                {{ number_format($resource->file_size / 1024, 2) }} KB
                                                @if($resource->description)
                                                    • {{ Str::limit($resource->description, 50) }}
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-400">Uploaded by {{ $resource->uploader->name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('study-rooms.resources.download', [$studyRoom, $resource]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            Download
                                        </a>
                                        @if($resource->uploaded_by === auth()->id())
                                            <form action="{{ route('study-rooms.resources.destroy', [$studyRoom, $resource]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No resources shared yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Upcoming Sessions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Upcoming Sessions</h3>
                        <div class="mt-4 space-y-4">
                            @forelse($studyRoom->sessions()->where('scheduled_at', '>', now())->orderBy('scheduled_at')->get() as $session)
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $session->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $session->scheduled_at->format('M d, Y H:i') }}</p>
                                        @if($session->meeting_link)
                                            <a href="{{ $session->meeting_link }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm">Join Meeting</a>
                                        @endif
                                    </div>
                                    @if($session->created_by === auth()->id())
                                        <form action="{{ route('study-rooms.sessions.destroy', [$studyRoom, $session]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Cancel</button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No upcoming sessions scheduled.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Session Modal -->
    <div x-show="scheduleModal" 
         class="fixed z-10 inset-0 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="scheduleModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="scheduleModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Schedule Study Session
                        </h3>
                        <div class="mt-4">
                            <form action="{{ route('study-rooms.sessions.store', $studyRoom) }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                        <input type="text" name="title" id="title" required
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                        <textarea name="description" id="description" rows="3"
                                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                    </div>
                                    <div>
                                        <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Date & Time</label>
                                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" required
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="duration" class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                                        <input type="number" name="duration" id="duration" required min="15" max="480"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="meeting_link" class="block text-sm font-medium text-gray-700">Meeting Link (optional)</label>
                                        <input type="url" name="meeting_link" id="meeting_link"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                    <button type="submit"
                                            class="inline-flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                        Schedule
                                    </button>
                                    <button type="button"
                                            @click="scheduleModal = false"
                                            class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Resource Modal -->
    <div x-show="resourceModal" 
         class="fixed z-10 inset-0 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="resourceModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="resourceModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Share Resource
                        </h3>
                        <div class="mt-4">
                            <form action="{{ route('study-rooms.resources.store', $studyRoom) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Resource Name</label>
                                        <input type="text" name="name" id="name" required
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                        <textarea name="description" id="description" rows="3"
                                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                    </div>
                                    <div>
                                        <label for="file" class="block text-sm font-medium text-gray-700">File</label>
                                        <input type="file" name="file" id="file" required
                                               class="mt-1 block w-full text-sm text-gray-500
                                                      file:mr-4 file:py-2 file:px-4
                                                      file:rounded-md file:border-0
                                                      file:text-sm file:font-medium
                                                      file:bg-indigo-50 file:text-indigo-700
                                                      hover:file:bg-indigo-100">
                                        <p class="mt-1 text-sm text-gray-500">Max file size: 10MB</p>
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                    <button type="submit"
                                            class="inline-flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                        Upload
                                    </button>
                                    <button type="button"
                                            @click="resourceModal = false"
                                            class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 