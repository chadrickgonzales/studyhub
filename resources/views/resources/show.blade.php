<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $resource->title }}
            </h2>
            <div class="flex space-x-2">
                @can('update', $resource)
                    <a href="{{ route('resources.edit', [$studyRoom, $resource]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </a>
                @endcan
                @can('delete', $resource)
                    <form action="{{ route('resources.destroy', [$studyRoom, $resource]) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this resource?')">
                            Delete
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-8">
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <span class="mr-4">
                                <i class="fas fa-user"></i> {{ $resource->user->name }}
                            </span>
                            <span>
                                <i class="fas fa-clock"></i> {{ $resource->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <div class="prose max-w-none">
                            <p class="text-gray-700">{{ $resource->description }}</p>
                        </div>

                        @if($resource->type === 'link')
                            <div class="mt-4">
                                <a href="{{ $resource->url }}" target="_blank" class="text-blue-500 hover:text-blue-700">
                                    <i class="fas fa-external-link-alt"></i> Open Link
                                </a>
                            </div>
                        @elseif($resource->type === 'file')
                            <div class="mt-4">
                                <a href="{{ Storage::url($resource->file_path) }}" class="text-blue-500 hover:text-blue-700" download>
                                    <i class="fas fa-download"></i> Download File
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-gray-200 pt-8">
                        <h3 class="text-lg font-semibold mb-4">Ratings & Reviews</h3>
                        
                        <div class="mb-6">
                            <div class="flex items-center mb-4">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $resource->ratings->avg('rating') ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                    <span class="ml-2 text-sm text-gray-500">
                                        ({{ $resource->ratings->count() }} ratings)
                                    </span>
                                </div>
                            </div>

                            <form action="{{ route('resources.rate', [$studyRoom, $resource]) }}" method="POST" class="mb-4">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Your Rating</label>
                                    <div class="flex items-center mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" class="rating-star text-2xl text-gray-300 hover:text-yellow-400" data-rating="{{ $i }}">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        @endfor
                                        <input type="hidden" name="rating" id="rating" value="{{ old('rating') }}">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="comment" class="block text-sm font-medium text-gray-700">Your Review</label>
                                    <textarea name="comment" id="comment" rows="3" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('comment') }}</textarea>
                                </div>

                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Submit Review
                                </button>
                            </form>
                        </div>

                        <div class="space-y-4">
                            @foreach($resource->ratings as $rating)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-center mb-2">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $rating->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="ml-2 text-sm text-gray-500">
                                            by {{ $rating->user->name }} • {{ $rating->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    @if($rating->comment)
                                        <p class="text-gray-700">{{ $rating->comment }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-8 mt-8">
                        <h3 class="text-lg font-semibold mb-4">Comments</h3>

                        <form action="{{ route('resources.comment', [$studyRoom, $resource]) }}" method="POST" class="mb-6">
                            @csrf
                            <div class="mb-4">
                                <textarea name="content" rows="3" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Write a comment..." required></textarea>
                            </div>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Post Comment
                            </button>
                        </form>

                        <div class="space-y-4">
                            @foreach($resource->comments->whereNull('parent_id') as $comment)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-center mb-2">
                                        <span class="font-medium">{{ $comment->user->name }}</span>
                                        <span class="ml-2 text-sm text-gray-500">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-gray-700">{{ $comment->content }}</p>

                                    @if($comment->replies->count() > 0)
                                        <div class="mt-4 ml-8 space-y-4">
                                            @foreach($comment->replies as $reply)
                                                <div class="bg-white p-3 rounded-lg">
                                                    <div class="flex items-center mb-2">
                                                        <span class="font-medium">{{ $reply->user->name }}</span>
                                                        <span class="ml-2 text-sm text-gray-500">
                                                            {{ $reply->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                    <p class="text-gray-700">{{ $reply->content }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <button class="mt-2 text-sm text-blue-500 hover:text-blue-700 reply-button" data-comment-id="{{ $comment->id }}">
                                        Reply
                                    </button>

                                    <form action="{{ route('resources.comment', [$studyRoom, $resource]) }}" method="POST" class="reply-form mt-2 hidden" data-comment-id="{{ $comment->id }}">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                        <textarea name="content" rows="2" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Write a reply..." required></textarea>
                                        <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                            Post Reply
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Rating stars
        document.querySelectorAll('.rating-star').forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                document.getElementById('rating').value = rating;
                
                document.querySelectorAll('.rating-star').forEach(s => {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-300');
                });

                for (let i = 1; i <= rating; i++) {
                    document.querySelector(`.rating-star[data-rating="${i}"]`).classList.remove('text-gray-300');
                    document.querySelector(`.rating-star[data-rating="${i}"]`).classList.add('text-yellow-400');
                }
            });
        });

        // Reply forms
        document.querySelectorAll('.reply-button').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                const form = document.querySelector(`.reply-form[data-comment-id="${commentId}"]`);
                form.classList.toggle('hidden');
            });
        });
    </script>
    @endpush
</x-app-layout> 