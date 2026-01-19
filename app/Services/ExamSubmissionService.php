<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamAttempt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExamSubmissionService
{
    /**
     * Handle the exam submission process.
     *
     * @param Exam $exam
     * @param ExamAttempt $attempt
     * @param array $answers
     * @return array
     * @throws \Exception
     */
    public function submit(Exam $exam, ExamAttempt $attempt, array $answers): array
    {
        // 1. Validate Time
        $this->validateTime($exam, $attempt);

        // 2. Process submission in a transaction
        return DB::transaction(function () use ($exam, $attempt, $answers) {
            $score = 0;

            foreach ($answers as $questionId => $answerValue) {
                $question = $exam->questions()->find($questionId);

                if (!$question) {
                    continue;
                }

                if ($question->type === 'multiple_choice') {
                    $option = $question->options()->find($answerValue);
                    $isCorrect = $option && $option->is_correct;

                    if ($isCorrect) {
                        $score += $question->points;
                    }

                    $attempt->answers()->create([
                        'question_id' => $question->id,
                        'option_id' => $answerValue,
                    ]);
                } else {
                    // Text Answer
                    $attempt->answers()->create([
                        'question_id' => $question->id,
                        'answer_text' => $answerValue,
                    ]);
                }
            }

            // 3. Mark as completed
            $attempt->update([
                'completed_at' => now(),
                'score' => $score,
            ]);

            return [
                'success' => true,
                'message' => 'Exam submitted successfully.',
                'score' => $score // Optional return
            ];
        });
    }

    /**
     * Validate if the submission is within the allowed time.
     *
     * @param Exam $exam
     * @param ExamAttempt $attempt
     * @throws \Exception
     */
    protected function validateTime(Exam $exam, ExamAttempt $attempt): void
    {
        $startTime = $attempt->started_at;
        // Allow 1 minute buffer for network latency
        $maxEndTime = $startTime->copy()->addMinutes($exam->duration_minutes)->addMinutes(1);

        if (now()->greaterThan($maxEndTime)) {
            // Auto-close the attempt with max duration
            $attempt->update(['completed_at' => $startTime->addMinutes($exam->duration_minutes)]);

            throw new \Exception('Submission rejected. Time limit exceeded.');
        }
    }
}
