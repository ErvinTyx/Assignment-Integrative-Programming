<x-app-layout>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">

                <div class="flex">
                    <div class="flex-1 pr-8">
                        <h1 class="font-semibold text-5xl text-gray-800 dark:text-gray-200 leading-tight">
                            {{ $user->name }}'s Profile
                        </h1>

                        <div class="mt-4 text-gray-500 dark:text-gray-400">
                            @forelse ($posts as $post)
                                <x-post-item :post="$post" />
                            @empty
                                <div class="text-center text-gray-500 dark:text-gray-400 py-60">
                                    No posts Found.
                                </div>
                            @endforelse
                        </div>


                    </div>

                    <x-follow-ctr :user="$user" class="w-[320px] border-l px-8">
                        <x-user-avatar :user="$user" size="w-16 h-16" />
                        <h3>
                            {{ $user->name }}
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            <span x-text="followersCount"></span> Followers
                        </p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            {{ $user->bio ?? 'The person is too lazy to write bio' }}
                        </p>
                        @if (auth()->user() && auth()->user()->id !== $user->id)
                            <div class ="mt-4">
                                <button @click="follow()" class=" px-4 py-2 text-white rounded-full  transition-colors"
                                    x-text="following ? 'Unfollow' : 'Follow'"
                                    :class="following ? 'bg-red-600 hover:bg-orange-600' : 'bg-emerald-600 hover:bg-blue-600'">

                                </button>
                            </div>
                        @endif
                    </x-follow-ctr>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
