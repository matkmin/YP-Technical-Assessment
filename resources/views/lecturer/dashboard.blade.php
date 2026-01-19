<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lecturer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Welcome, {{ auth()->user()->name }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Manage Subjects -->
                        <a href="{{ route('lecturer.subjects.index') }}"
                            class="block p-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                            <h4 class="font-bold text-blue-700 dark:text-blue-300">Manage Subjects</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Create and edit subjects.</p>
                        </a>

                        <!-- Manage Classes -->
                        <a href="{{ route('lecturer.classes.index') }}"
                            class="block p-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/40 transition">
                            <h4 class="font-bold text-green-700 dark:text-green-300">Manage Classes</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Assign students to classes.</p>
                        </a>

                        <!-- Manage Exams -->
                        <a href="{{ route('lecturer.exams.index') }}"
                            class="block p-6 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/40 transition">
                            <h4 class="font-bold text-purple-700 dark:text-purple-300">Manage Exams</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Create exams and questions.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>