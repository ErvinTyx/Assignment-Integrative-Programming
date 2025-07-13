<x-app-layout>
    <div class="py-4">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <h1 class="text-5xl font-bold mb-4 text-gray-900 dark:text-white mt-4 ml-4">
                    {{ $post->title }}
                </h1>
                {{-- User Avatar --}}
                <div class="flex gap-4 ml-4 mb-4">
                    <x-user-avatar :user="$post->user" />

                    <div>
                        <div
                            class="flex gap-2 text-sm text-gray-500 dark:text-gray-400 text-xl font-semibold text-gray-900 dark:text-white">
                            <a class="hover:underline" href="{{ route('profile.show',$post->user) }}">{{ $post->user->name }}</a>
                            &middot;
                            <a href="" class="text-emerald-500 hover:underline">
                                Follow
                            </a>
                        </div>
                        <div class="flex gap-2 text-gray-500 dark:text-gray-400">
                            {{ $post->readTime() }}
                            &middot;
                            {{ $post->created_at->format('M d, Y') }}

                        </div>
                    </div>



                </div>
                {{-- Clap Section --}}
                <x-clap-button :post="$post" />

                {{-- Post Section --}}
                <div class="mt-8">
                    <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="w-full" />
                    <div class="mt-4 text-gray-900 dark:text-gray-100 mx-4">
                        {{ $post->content }}
                    </div>

                </div>
                <div class="mt-8 mb-8 ml-4">
                    <span class="px-4 py-2 rounded-xl bg-gray-300 text-gray-500 dark:text-gray-800 text-sm">
                        {{ $post->category->name }}

                    </span>
                </div>
                <x-clap-button />

            </div>
        </div>

    </div>
</x-app-layout>
