<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manage Exam: ') . $exam->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="flex gap-6">
                <!-- Exam Info -->
                <div class="w-1/3 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4">Exam Details</h3>
                        <p><strong>Subject:</strong> {{ $exam->subject->name }}</p>
                        <p><strong>Duration:</strong> {{ $exam->duration_minutes }} min</p>
                        <p><strong>Status:</strong>
                            @if($exam->is_active)
                                <span class="text-green-600 font-bold">Active</span>
                            @else
                                <span class="text-red-600 font-bold">Inactive</span>
                            @endif
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('lecturer.exams.edit', $exam->id) }}"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">Edit
                                Details</a>
                        </div>
                    </div>
                </div>

                <!-- Assigned Classes -->
                <div class="w-2/3 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4">Assigned Classes</h3>

                        <div class="mb-4">
                            <form action="{{ route('lecturer.exams.classes.store', $exam->id) }}" method="POST"
                                class="flex gap-4">
                                @csrf
                                <select name="class_id"
                                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full">
                                    <option value="">Select Class to Assign</option>
                                    @foreach($allClasses as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500">Assign</button>
                            </form>
                        </div>

                        <ul class="list-disc pl-5">
                            @forelse($exam->classes as $class)
                                <li class="mb-2 flex justify-between items-center">
                                    <span>{{ $class->name }}</span>
                                    <form action="{{ route('lecturer.exams.classes.destroy', [$exam->id, $class->id]) }}"
                                        method="POST" onsubmit="return confirm('Unassign class?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 text-sm">Remove</button>
                                    </form>
                                </li>
                            @empty
                                <li class="text-gray-500">No classes assigned yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Questions ({{ $exam->questions->count() }})</h3>
                        <a href="{{ route('lecturer.exams.questions.create', $exam->id) }}"
                            class="px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-500 focus:outline-none focus:border-purple-700 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Add Question
                        </a>
                    </div>

                    <div class="space-y-4">
                        @forelse($exam->questions as $index => $question)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <div class="flex justify-between">
                                    <div class="font-semibold text-lg">Q{{ $index + 1 }}. {{ $question->question_text }}
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('lecturer.questions.edit', $question->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form action="{{ route('lecturer.questions.destroy', $question->id) }}"
                                            method="POST" class="inline" onsubmit="return confirm('Delete question?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Type: {{ ucfirst(str_replace('_', ' ', $question->type)) }} | Points:
                                    {{ $question->points }}
                                </div>
                                @if($question->type == 'multiple_choice')
                                    <div class="mt-2 pl-4 border-l-2 border-gray-300 dark:border-gray-600">
                                        <ul class="list-disc pl-5">
                                            @foreach($question->options as $option)
                                                <li class="{{ $option->is_correct ? 'text-green-600 font-bold' : '' }}">
                                                    {{ $option->option_text }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500 text-center">No questions added yet.</p>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>