<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function create(\App\Models\Exam $exam)
    {
        return view('lecturer.questions.create', compact('exam'));
    }

    public function store(\Illuminate\Http\Request $request, \App\Models\Exam $exam)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:multiple_choice,text',
            'points' => 'required|integer|min:1',
            'options' => 'array|required_if:type,multiple_choice',
            'options.*.option_text' => 'required_with:options|string',
            'options.*.is_correct' => 'boolean',
        ]);

        $question = $exam->questions()->create([
            'question_text' => $validated['question_text'],
            'type' => $validated['type'],
            'points' => $validated['points'],
        ]);

        if ($validated['type'] === 'multiple_choice' && isset($validated['options'])) {
            foreach ($validated['options'] as $optionData) {
                // Ensure is_correct is boolean
                $isCorrect = isset($optionData['is_correct']) && $optionData['is_correct'];
                $question->options()->create([
                    'option_text' => $optionData['option_text'],
                    'is_correct' => $isCorrect,
                ]);
            }
        }

        return redirect()->route('lecturer.exams.show', $exam->id)->with('success', 'Question added successfully.');
    }

    public function edit(\App\Models\Question $question)
    {
        $question->load('options');
        return view('lecturer.questions.edit', compact('question'));
    }

    public function update(\Illuminate\Http\Request $request, \App\Models\Question $question)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:multiple_choice,text',
            'points' => 'required|integer|min:1',
            'options' => 'array|nullable',
            'options.*.id' => 'nullable|exists:options,id',
            'options.*.option_text' => 'required_with:options|string',
            'options.*.is_correct' => 'boolean',
        ]);

        $question->update([
            'question_text' => $validated['question_text'],
            'type' => $validated['type'],
            'points' => $validated['points'],
        ]);

        // Handle Options Update/Create logic (Simplified for this complexity: Delete all and recreate? Or sync)
        // Recreating is easiest for MVP unless ID tracking is strict. 
        // But tracking ID is better.
        // I'll delete old options and create new ones for simplicity to handle "removing options".
        // Or if the view allows dynamic add/remove, sync is hard.

        // Simple approach: Delete all options and recreate if type is multiple_choice
        if ($validated['type'] === 'multiple_choice') {
            $question->options()->delete(); // Remove old
            if (isset($request->options)) {
                foreach ($request->options as $optionData) {
                    $isCorrect = isset($optionData['is_correct']);
                    $question->options()->create([
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $isCorrect,
                    ]);
                }
            }
        } else {
            $question->options()->delete();
        }

        return redirect()->route('lecturer.exams.show', $question->exam_id)->with('success', 'Question updated successfully.');
    }

    public function destroy(\App\Models\Question $question)
    {
        $examId = $question->exam_id;
        $question->delete();
        return redirect()->route('lecturer.exams.show', $examId)->with('success', 'Question deleted successfully.');
    }
}
