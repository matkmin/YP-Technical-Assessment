<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function show(\App\Models\Exam $exam)
    {
        // 1. Check Access
        $user = auth()->user();
        if (!$user->classes()->whereHas('exams', fn($q) => $q->where('exams.id', $exam->id))->exists()) {
            abort(403, 'You are not assigned to this exam.');
        }

        // 2. Check Attempt
        $attempt = $exam->attempts()->where('user_id', $user->id)->first();

        // 3. Logic
        if (!$attempt) {
            // Not started -> Show Start Page
            return view('student.exams.start', compact('exam'));
        }

        if ($attempt->completed_at) {
            // Completed -> Show Result
            $attempt->load('answers.question.options');
            return view('student.exams.result', compact('exam', 'attempt'));
        }

        // In Progress -> Show Questions (Resume)
        // Check time remaining
        $startTime = $attempt->started_at;
        $endTime = $startTime->copy()->addMinutes($exam->duration_minutes);

        if (now()->greaterThan($endTime)) {
            // Time expired, auto-submit (mark completed)
            $attempt->update(['completed_at' => now()]);
            // Redirect to result with message/score
            return redirect()->route('student.exams.show', $exam->id)->with('info', 'Time expired.');
        }

        $remainingSeconds = (int) now()->diffInSeconds($endTime, false);
        $exam->load(['questions.options']);

        return view('student.exams.show', compact('exam', 'attempt', 'remainingSeconds'));
    }

    public function start(\App\Models\Exam $exam)
    {
        $user = auth()->user();

        // Double check access
        if (!$user->classes()->whereHas('exams', fn($q) => $q->where('exams.id', $exam->id))->exists()) {
            abort(403, 'Unauthorized.');
        }

        // Check if already attempted
        if ($exam->attempts()->where('user_id', $user->id)->exists()) {
            return redirect()->route('student.exams.show', $exam->id);
        }

        $exam->attempts()->create([
            'user_id' => $user->id,
            'started_at' => now(),
        ]);

        return redirect()->route('student.exams.show', $exam->id);
    }

    public function submit(\Illuminate\Http\Request $request, \App\Models\Exam $exam)
    {
        $user = auth()->user();
        $attempt = $exam->attempts()->where('user_id', $user->id)->whereNull('completed_at')->firstOrFail();

        // Validate time (backend check)
        $startTime = $attempt->started_at;
        // Allow 1 minute buffer for network latency
        $maxEndTime = $startTime->copy()->addMinutes($exam->duration_minutes)->addMinutes(1);

        if (now()->greaterThan($maxEndTime)) {
            $attempt->update(['completed_at' => $startTime->addMinutes($exam->duration_minutes)]); // Set to max time
            return redirect()->route('student.exams.show', $exam->id)->with('error', 'Submission rejected. Time limit exceeded.');
        }

        // Save Answers
        // Request: answers[question_id] = option_id (for MC) or text (for Text)
        // Simple mapping: answers[question_id] => value

        $answers = $request->input('answers', []);
        $score = 0;
        $totalPoints = 0; // Only calculate for MCQs or auto-gradable.

        foreach ($answers as $questionId => $answerValue) {
            $question = $exam->questions()->find($questionId);
            if (!$question)
                continue;

            if ($question->type === 'multiple_choice') {
                $option = $question->options()->find($answerValue); // answerValue is option_id
                $isCorrect = $option && $option->is_correct;
                if ($isCorrect) {
                    $score += $question->points;
                }

                $attempt->answers()->create([
                    'question_id' => $question->id,
                    'option_id' => $answerValue,
                ]);
            } else {
                // Text answer
                $attempt->answers()->create([
                    'question_id' => $question->id,
                    'answer_text' => $answerValue,
                ]);
                // Text answers need manual grading usually, score remains 0 for this question for now.
            }
        }

        $attempt->update([
            'completed_at' => now(),
            'score' => $score,
        ]);

        return redirect()->route('student.exams.show', $exam->id)->with('success', 'Exam submitted successfully.');
    }
}
