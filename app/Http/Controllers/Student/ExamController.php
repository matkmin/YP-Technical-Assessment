<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ExamSubmissionService;
use App\Http\Requests\SubmitExamRequest;
use App\Models\Exam;

class ExamController extends Controller
{
    public function show(Exam $exam)
    {
        $user = auth()->user();
        if (!$user->classes()->whereHas('exams', fn($q) => $q->where('exams.id', $exam->id))->exists()) {
            abort(403, 'You are not assigned to this exam.');
        }

        $attempt = $exam->attempts()->where('user_id', $user->id)->first();

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
        $startTime = $attempt->started_at;
        $endTime = $startTime->copy()->addMinutes($exam->duration_minutes);

        if (now()->greaterThan($endTime)) {
            // Time expired, auto-submit (mark completed)
            $attempt->update(['completed_at' => now()]);

            return redirect()->route('student.exams.show', $exam->id)->with('info', 'Time expired.');
        }

        $remainingSeconds = (int) now()->diffInSeconds($endTime, false);
        $exam->load(['questions.options']);

        return view('student.exams.show', compact('exam', 'attempt', 'remainingSeconds'));
    }

    public function start(Exam $exam)
    {
        $user = auth()->user();

        if (!$user->classes()->whereHas('exams', fn($q) => $q->where('exams.id', $exam->id))->exists()) {
            abort(403, 'Unauthorized.');
        }

        if ($exam->attempts()->where('user_id', $user->id)->exists()) {
            return redirect()->route('student.exams.show', $exam->id);
        }

        $exam->attempts()->create([
            'user_id' => $user->id,
            'started_at' => now(),
        ]);

        return redirect()->route('student.exams.show', $exam->id);
    }

    public function submit(SubmitExamRequest $req, Exam $exam, ExamSubmissionService $submissionService)
    {
        $user = auth()->user();
        $attempt = $exam->attempts()->where('user_id', $user->id)->whereNull('completed_at')->firstOrFail();

        try {
            $submissionService->submit($exam, $attempt, $req->input('answers', []));
            return redirect()->route('student.exams.show', $exam->id)->with('success', 'Exam submitted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('student.exams.show', $exam->id)->with('error', $e->getMessage());
        }
    }
}
