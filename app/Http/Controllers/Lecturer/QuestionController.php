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

    public function store(StoreQuestionRequest $req, Exam $exam): RedirectResponse
    {
        $question = $exam->questions()->create(
            $req->only(['question_text', 'type', 'points'])
        );

        if ($req->type === 'multiple_choice') {
            $options = $req->options;
            $correctIndex = $req->input('correct_option');

            foreach ($options as $key => &$option) {
                $option['is_correct'] = ($key == $correctIndex);
            }
            
            $question->options()->createMany($options);
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

    public function update(UpdateQuestionRequest $req, Question $question): RedirectResponse
    {
        $question->update(
            $req->only(['question_text', 'type', 'points'])
        );

        $question->options()->delete();

        if ($req->type === 'multiple_choice' && $req->filled('options')) {
            $options = $req->options;
            $correctIndex = $req->input('correct_option');

            foreach ($options as $key => &$option) {
                $option['is_correct'] = ($key == $correctIndex);
            }

            $question->options()->createMany($options);
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
