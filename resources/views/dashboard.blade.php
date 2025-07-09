<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">


                    <ul
                        class="flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400 justify-center">
                        <li class="me-2">
                            <a href="#" class="inline-block px-4 py-3 text-white bg-blue-600 rounded-lg active"
                                aria-current="page">
                                tab1
                            </a>
                        </li>
                        <li class="me-2">
                            <a href="#" class="inline-block px-4 py-3 text-white bg-blue-600 rounded-lg active"
                                aria-current="page">
                                tab2
                            </a>
                        </li>
                        <li class="me-2">
                            <a href="#" class="inline-block px-4 py-3 text-white bg-blue-600 rounded-lg active"
                                aria-current="page">
                                tab3
                            </a>
                        </li>
                        {{-- @foreach ($categories as $category)
                            <li class="me-2">
                                <a href="#" class="inline-block px-4 py-3 text-white bg-blue-600 rounded-lg active" aria-current="page">
                                    {{ $categ->name }}
                                </a>
                            </li>
                        @endforeach --}}


                    </ul>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
