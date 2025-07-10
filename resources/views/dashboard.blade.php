<x-app-layout>
    <div class="py-4">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <x-category-tabs>
                    No categories found.
                </x-category-tabs>


            </div>
            <div class="mt-8 text-gray-900 dark:text-gray-100">


                @forelse ($posts as $post)
                    <x-post-item :post="$post" />
                @empty
                    <div class="text-center text-gray-500 dark:text-gray-400 py-60">
                        No posts Found.
                    </div>
                @endforelse


            </div>
            {{ $posts->onEachSide(1)->links() }}
        </div>
    </div>
</x-app-layout>
