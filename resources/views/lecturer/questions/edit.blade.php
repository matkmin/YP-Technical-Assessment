<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Question') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100" x-data="{ type: '{{ $question->type }}' }">
                    <form action="{{ route('lecturer.questions.update', $question->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="question_text"
                                class="block font-medium text-sm text-gray-700 dark:text-gray-300">Question Text</label>
                            <textarea name="question_text" id="question_text" rows="3"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full"
                                required>{{ old('question_text', $question->question_text) }}</textarea>
                        </div>

                        <div class="flex gap-4 mb-4">
                            <div class="w-1/2">
                                <label for="points"
                                    class="block font-medium text-sm text-gray-700 dark:text-gray-300">Points</label>
                                <input type="number" name="points" id="points"
                                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full"
                                    value="{{ old('points', $question->points) }}" min="1" required>
                            </div>

                            <div class="w-1/2">
                                <label for="type"
                                    class="block font-medium text-sm text-gray-700 dark:text-gray-300">Type</label>
                                <select name="type" id="type" x-model="type"
                                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full"
                                    required>
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="text">Text Answer</option>
                                </select>
                            </div>
                        </div>

                        <!-- Options Section -->
                        <div x-show="type === 'multiple_choice'"
                            class="mt-6 border-t pt-4 border-gray-200 dark:border-gray-700">
                            <h4 class="font-bold mb-2">Options</h4>
                            @if($question->type === 'multiple_choice')
                                @foreach($question->options as $i => $option)
                                    <div class="flex items-center gap-2 mb-2">
                                        <input type="radio" name="correct_option" value="{{ $i }}"
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" {{ $option->is_correct ? 'checked' : '' }}
                                            onclick="document.querySelectorAll('.is-correct-hidden').forEach(el => el.value = 0); document.getElementById('is_correct_{{ $i }}').value = 1;">
                                        <input type="hidden" name="options[{{ $i }}][is_correct]" id="is_correct_{{ $i }}"
                                            class="is-correct-hidden" value="{{ $option->is_correct ? 1 : 0 }}">

                                        <input type="text" name="options[{{ $i }}][option_text]"
                                            value="{{ $option->option_text }}"
                                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full"
                                            required>
                                    </div>
                                @endforeach
                                <!-- Add simple implementation for ensuring 4 slots if less -->
                                @for($i = count($question->options); $i < 4; $i++)
                                    <div class="flex items-center gap-2 mb-2">
                                        <input type="radio" name="correct_option" value="{{ $i }}"
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                                            onclick="document.querySelectorAll('.is-correct-hidden').forEach(el => el.value = 0); document.getElementById('is_correct_{{ $i }}').value = 1;">
                                        <input type="hidden" name="options[{{ $i }}][is_correct]" id="is_correct_{{ $i }}"
                                            class="is-correct-hidden" value="0">

                                        <input type="text" name="options[{{ $i }}][option_text]"
                                            placeholder="Option {{ $i + 1 }}"
                                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full">
                                    </div>
                                @endfor
                            @else
                                <!-- If switching from Text to MC, show empty slots -->
                                @for($i = 0; $i < 4; $i++)
                                    <div class="flex items-center gap-2 mb-2">
                                        <input type="radio" name="correct_option" value="{{ $i }}"
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" {{ $i == 0 ? 'checked' : '' }}
                                            onclick="document.querySelectorAll('.is-correct-hidden').forEach(el => el.value = 0); document.getElementById('is_correct_{{ $i }}').value = 1;">
                                        <input type="hidden" name="options[{{ $i }}][is_correct]" id="is_correct_{{ $i }}"
                                            class="is-correct-hidden" value="{{ $i == 0 ? 1 : 0 }}">

                                        <input type="text" name="options[{{ $i }}][option_text]"
                                            placeholder="Option {{ $i + 1 }}"
                                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full">
                                    </div>
                                @endfor
                            @endif
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Update Question
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>