<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - StudyHub</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/alpinejs" defer></script>
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
                        <a href="{{ route('dashboard') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('study-rooms.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Study Rooms
                        </a>
                        <a href="{{ route('profile.edit') }}" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
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
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <!-- Profile Information -->
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Profile Information</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Update your profile information and manage your account settings.
                    </p>
                </div>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="shadow sm:rounded-md sm:overflow-hidden">
                        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                            <!-- Profile Picture -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Profile Picture
                                </label>
                                <div class="mt-1 flex items-center">
                                    <span class="inline-block h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                                        @if(auth()->user()->profile_picture)
                                            <img src="{{ Storage::url(auth()->user()->profile_picture) }}" alt="Profile picture" class="h-full w-full object-cover">
                                        @else
                                            <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        @endif
                                    </span>
                                    <input type="file" name="profile_picture" class="ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" name="name" id="name" value="{{ auth()->user()->name }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" id="email" value="{{ auth()->user()->email }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>

                            <!-- Bio -->
                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                                <div class="mt-1">
                                    <textarea id="bio" name="bio" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md">{{ auth()->user()->bio }}</textarea>
                                </div>
                            </div>

                            <!-- Education Information -->
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="education_level" class="block text-sm font-medium text-gray-700">Education Level</label>
                                    <select id="education_level" name="education_level" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select level</option>
                                        <option value="high_school" {{ auth()->user()->education_level === 'high_school' ? 'selected' : '' }}>High School</option>
                                        <option value="undergraduate" {{ auth()->user()->education_level === 'undergraduate' ? 'selected' : '' }}>Undergraduate</option>
                                        <option value="graduate" {{ auth()->user()->education_level === 'graduate' ? 'selected' : '' }}>Graduate</option>
                                        <option value="phd" {{ auth()->user()->education_level === 'phd' ? 'selected' : '' }}>PhD</option>
                                    </select>
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="institution" class="block text-sm font-medium text-gray-700">Institution</label>
                                    <input type="text" name="institution" id="institution" value="{{ auth()->user()->institution }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6">
                                    <label for="major" class="block text-sm font-medium text-gray-700">Major/Field of Study</label>
                                    <input type="text" name="major" id="major" value="{{ auth()->user()->major }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>

                            <!-- Interests -->
                            <div x-data="{ newInterest: '' }">
                                <label class="block text-sm font-medium text-gray-700">Interests</label>
                                <div class="mt-1">
                                    <div class="flex gap-2 flex-wrap mb-2">
                                        @foreach(auth()->user()->interests as $interest)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ $interest->interest }}
                                                <form action="{{ route('profile.interests.destroy', $interest) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="ml-1 text-indigo-600 hover:text-indigo-900">
                                                        &times;
                                                    </button>
                                                </form>
                                            </span>
                                        @endforeach
                                    </div>
                                    <div class="flex">
                                        <input type="text" x-model="newInterest" placeholder="Add new interest" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <form action="{{ route('profile.interests.store') }}" method="POST" class="ml-2">
                                            @csrf
                                            <input type="hidden" name="interest" x-bind:value="newInterest">
                                            <button type="submit" x-on:click="newInterest = ''" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Add
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Activity History -->
        <div class="mt-8">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Activity History</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Your recent activities and contributions.
                        </p>
                    </div>
                </div>

                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="bg-white shadow sm:rounded-md">
                        <ul role="list" class="divide-y divide-gray-200">
                            @foreach(auth()->user()->activities()->latest()->take(10)->get() as $activity)
                                <li class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-indigo-600 truncate">
                                            {{ $activity->type }}
                                        </p>
                                        <div class="ml-2 flex-shrink-0 flex">
                                            <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-2 sm:flex sm:justify-between">
                                        <div class="sm:flex">
                                            <p class="text-sm text-gray-500">
                                                {{ $activity->description }}
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="mt-8">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Notifications</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Your recent notifications and updates.
                        </p>
                    </div>
                </div>

                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="bg-white shadow sm:rounded-md">
                        <ul role="list" class="divide-y divide-gray-200">
                            @foreach(auth()->user()->notifications()->latest()->take(10)->get() as $notification)
                                <li class="px-4 py-4 sm:px-6 {{ is_null($notification->read_at) ? 'bg-indigo-50' : '' }}">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-indigo-600 truncate">
                                            {{ $notification->title }}
                                        </p>
                                        <div class="ml-2 flex-shrink-0 flex">
                                            <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ is_null($notification->read_at) ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-2 sm:flex sm:justify-between">
                                        <div class="sm:flex">
                                            <p class="text-sm text-gray-500">
                                                {{ $notification->message }}
                                            </p>
                                        </div>
                                        @if(is_null($notification->read_at))
                                            <form action="{{ route('profile.notifications.mark-as-read', $notification) }}" method="POST" class="mt-2 sm:mt-0">
                                                @csrf
                                                <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-900">Mark as read</button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 