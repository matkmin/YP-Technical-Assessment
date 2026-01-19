<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Exam Results') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-center text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-2">{{ $exam->title }}</h3>
                    <div class="text-4xl font-extrabold text-blue-600 dark:text-blue-400 my-4">
                        Score: {{ $attempt->score }} / {{ $exam->questions->sum('points') }}
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Submitted on {{ $attempt->completed_at->format('d M Y, h:i A') }}
                    </p>
                    <a href="{{ route('student.dashboard') }}" class="mt-6 inline-block text-indigo-600 hover:text-indigo-800 font-bold">Back to Dashboard</a>
                </div>
            </div>

            <div class="space-y-6">
                @foreach($exam->questions as $index => $question)
                    @php
                        $userAnswer = $attempt->answers->where('question_id', $question->id)->first();
                        $isCorrect = false;
                        if ($question->type === 'multiple_choice' && $userAnswer) {
                             $selectedOption = $question->options->where('id', $userAnswer->option_id)->first();
                             $isCorrect = $selectedOption && $selectedOption->is_correct;
                        }
                    @endphp

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 {{ $isCorrect ? 'border-green-500' : ($question->type === 'text' ? 'border-gray-500' : 'border-red-500') }}">
                        <div class="mb-2">
                             <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                Q{{ $index + 1 }}. {{ $question->question_text }}
                                <span class="text-sm font-normal text-gray-500">({{ $question->points }} points)</span>
                            </h4>
                        </div>

                        @if($question->type === 'multiple_choice')
                             <div class="space-y-2">
                                @foreach($question->options as $option)
                                    @php
                                        $isSelected = $userAnswer && $userAnswer->option_id == $option->id;
                                        $style = '';
                                        if ($option->is_correct) {
                                            $style = 'bg-green-100 dark:bg-green-900/30 border-green-500';
                                        } elseif ($isSelected && !$option->is_correct) {
                                            $style = 'bg-red-100 dark:bg-red-900/30 border-red-500';
                                        } else {
                                            $style = 'border-gray-200 dark:border-gray-700 opacity-50';
                                        }
                                    @endphp
                                    <div class="p-3 rounded-lg border {{ $style }} flex justify-between">
                                        <span>{{ $option->option_text }}</span>
                                        @if($isSelected)
                                            <span class="text-xs font-bold uppercase">(Your Answer)</span>
                                        @endif
                                        @if($option->is_correct)
                                            <span class="text-xs font-bold text-green-600 uppercase">Correct</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="mt-2">
                                <p class="font-bold text-gray-700 dark:text-gray-300">Your Answer:</p>
                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600">
                                    {{ $userAnswer->answer_text ?? 'No answer provided.' }}
                                </div>
                                <p class="text-xs text-gray-500 mt-2">* Text answers require manual grading by lecturer.</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
