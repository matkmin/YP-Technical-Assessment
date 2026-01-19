<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Available Exams</h3>

                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                            role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($exams as $exam)
                                            @php
                                                $attempt = $exam->attempts->first();
                                                $status = 'Not Started';
                                                if ($attempt) {
                                                    if ($attempt->completed_at) {
                                                        $status = 'Completed';
                                                    } else {
                                                        $status = 'In Progress';
                                                    }
                                                }
                                            @endphp

                                            <div
                                                class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 bg-gray-50 dark:bg-gray-700/50 hover:shadow-lg transition">
                                                <h4 class="font-bold text-lg mb-2">{{ $exam->title }}</h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Subject: {{ $exam->subject->name }}
                                                </p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Duration:
                                                    {{ $exam->duration_minutes }} mins</p>

                                                <div class="flex justify-between items-center mt-4">
                                                    <span
                                                        class="px-2 py-1 text-xs font-semibold rounded-full 
                                                            {{ $status == 'Completed' ? 'bg-green-100 text-green-800' :
                            ($status == 'In Progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                                        {{ $status }}
                                                    </span>

                                                    <a href="{{ route('student.exams.show', $exam->id) }}"
                                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                        {{ $status == 'Completed' ? 'View Results' : ($status == 'In Progress' ? 'Continue' : 'Start Exam') }}
                                                    </a>
                                                </div>
                                            </div>
                        @empty
                            <div class="col-span-3 text-center text-gray-500 py-8">
                                No exams available at the moment.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>