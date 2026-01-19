<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $exam->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 text-center">
                    <h3 class="text-2xl font-bold mb-4">Exam Instructions</h3>

                    <div class="text-left max-w-2xl mx-auto space-y-4 mb-8">
                        <p><strong>Subject:</strong> {{ $exam->subject->name }}</p>
                        <p><strong>Duration:</strong> {{ $exam->duration_minutes }} minutes</p>
                        <p><strong>Total Questions:</strong> {{ $exam->questions()->count() }}</p>
                        <div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 p-4">
                            <p class="font-bold">Important:</p>
                            <ul class="list-disc pl-5">
                                <li>Once you start, the timer will not stop even if you close the window.</li>
                                <li>Ensure you have a stable internet connection.</li>
                                <li>The exam will auto-submit when the time is up.</li>
                            </ul>
                        </div>
                    </div>

                    <form action="{{ route('student.exams.start', $exam->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-8 py-3 bg-green-600 hover:bg-green-500 text-white font-bold rounded-lg text-lg shadow-lg transform transition hover:scale-105"
                            onclick="return confirm('Are you ready to start the exam?');">
                            Start Exam Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>