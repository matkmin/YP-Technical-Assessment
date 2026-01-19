<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $exam->title }}
            </h2>
            <div class="text-xl font-bold text-red-600 dark:text-red-400" id="timer">
                Time Remaining: <span id="time-display">00:00</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('student.exams.submit', $exam->id) }}" method="POST" id="exam-form">
                @csrf
                <div class="space-y-6">
                    @foreach($exam->questions as $index => $question)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="mb-4">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                    Q{{ $index + 1 }}. {{ $question->question_text }}
                                    <span class="text-sm font-normal text-gray-500">({{ $question->points }} points)</span>
                                </h4>
                            </div>

                            @if($question->type === 'multiple_choice')
                                <div class="space-y-2">
                                    @foreach($question->options as $option)
                                        <label
                                            class="flex items-center space-x-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}"
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                            <span class="text-gray-700 dark:text-gray-300">{{ $option->option_text }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div>
                                    <textarea name="answers[{{ $question->id }}]" rows="4"
                                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        placeholder="Type your answer here..."></textarea>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-lg shadow-lg"
                            onclick="return confirm('Submit exam? You cannot change answers after submitting.');">
                            Submit Exam
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let remainingSeconds = {{ $remainingSeconds }};
            const display = document.getElementById('time-display');

            function updateTimer() {
                if (remainingSeconds <= 0) {
                    clearInterval(timerInterval);
                    display.innerText = "00:00";
                    alert('Time is up! Submitting your exam automatically.');
                    document.getElementById('exam-form').submit();
                    return;
                }

                const minutes = Math.floor(remainingSeconds / 60);
                const seconds = Math.floor(remainingSeconds % 60);

                display.innerText = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                // Visual warning
                if (remainingSeconds < 300) { // 5 mins
                    display.parentElement.classList.add('animate-pulse');
                }

                remainingSeconds--;
            }

            const timerInterval = setInterval(updateTimer, 1000);
            updateTimer(); // Initial call
        });
    </script>
</x-app-layout>