<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function create(Exam $exam): View
    {
        return view('lecturer.questions.create', compact('exam'));
    }

    public function store(StoreQuestionRequest $request, Exam $exam): RedirectResponse
    {
        $question = $exam->questions()->create(
            $request->only(['question_text', 'type', 'points'])
        );

        if ($request->type === 'multiple_choice') {
            $question->options()->createMany($request->options);
        }

        return redirect()
            ->route('lecturer.exams.show', $exam->id)
            ->with('success', 'Question added successfully.');
    }

    public function edit(Question $question): View
    {
        $question->load('options');

        return view('lecturer.questions.edit', compact('question'));
    }

    public function update(UpdateQuestionRequest $request, Question $question): RedirectResponse
    {
        $question->update(
            $request->only(['question_text', 'type', 'points'])
        );
        
        $question->options()->delete();

        if ($request->type === 'multiple_choice' && $request->filled('options')) {
            $question->options()->createMany($request->options);
        }

        return redirect()
            ->route('lecturer.exams.show', $question->exam_id)
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(Question $question): RedirectResponse
    {
        $examId = $question->exam_id;

        $question->delete();

        return redirect()
            ->route('lecturer.exams.show', $examId)
            ->with('success', 'Question deleted successfully.');
    }
}
