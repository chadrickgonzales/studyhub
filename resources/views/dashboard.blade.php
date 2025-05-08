<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - StudyHub</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-2xl font-bold text-indigo-600">StudyHub</h1>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('dashboard') }}" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('study-rooms.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
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
        <!-- Welcome Section -->
        <div class="px-4 py-6 sm:px-0">
            <h2 class="text-2xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h2>
            <p class="mt-1 text-sm text-gray-600">Here's what's happening with your study groups.</p>
        </div>

        <!-- Quick Actions -->
        <div class="mt-4 px-4 sm:px-0">
            <div class="flex space-x-4">
                <a href="{{ route('study-rooms.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                    Create Study Room
                </a>
                <a href="{{ route('study-rooms.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Join Study Room
                </a>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <!-- My Study Rooms -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">My Study Rooms</h3>
                    <div class="mt-4 space-y-4">
                        @forelse($studyRooms as $room)
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $room->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $room->members_count }} members</p>
                                </div>
                                <a href="{{ route('study-rooms.show', $room) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Enter Room</a>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-sm text-gray-500">You haven't joined any study rooms yet.</p>
                                <a href="{{ route('study-rooms.index') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Browse Study Rooms
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Upcoming Sessions -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Upcoming Sessions</h3>
                    <div class="mt-4 space-y-4">
                        @forelse($upcomingSessions as $session)
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $session->studyRoom->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $session->scheduled_at->format('M d, Y H:i') }}</p>
                                </div>
                                <a href="{{ route('study-sessions.show', $session) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View</a>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No upcoming sessions scheduled.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Activity</h3>
                    <div class="mt-4 space-y-4">
                        @forelse($recentActivity as $activity)
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100">
                                        <span class="text-sm font-medium leading-none text-indigo-600">
                                            {{ substr($activity->user->name, 0, 1) }}
                                        </span>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm text-gray-500">
                                        {{ $activity->description }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No recent activity.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
