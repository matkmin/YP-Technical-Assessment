<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
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
        $this->validateTime($exam, $attempt);

        return DB::transaction(function () use ($exam, $attempt, $answers) {
            $score = 0;

            foreach ($answers as $questionId => $answerValue) {
                $question = $exam->questions()->find($questionId);

                if (!$question) {
                    continue;
                }

                $score += match ($question->type) {
                    'multiple_choice' => $this->handleMultipleChoice($question, $attempt, $answerValue),
                    'text' => $this->handleTextAnswer($question, $attempt, $answerValue),
                    default => 0,
                };
            }

            $attempt->update([
                'completed_at' => now(),
                'score' => $score,
            ]);

            return [
                'success' => true,
                'message' => 'Exam submitted successfully.',
                'score' => $score 
            ];
        });
    }

    /**
     * Handle Multiple Choice Answer.
     *
     * @param Question $question
     * @param ExamAttempt $attempt
     * @param mixed $answerValue
     * @return int Points earned
     */
    private function handleMultipleChoice(Question $question, ExamAttempt $attempt, mixed $answerValue): int
    {
        $option = $question->options()->find($answerValue);
        $isCorrect = $option && $option->is_correct;

        $attempt->answers()->create([
            'question_id' => $question->id,
            'option_id' => $answerValue,
        ]);

        return $isCorrect ? $question->points : 0;
    }

    /**
     * Handle Text Answer.
     *
     * @param Question $question
     * @param ExamAttempt $attempt
     * @param mixed $answerValue
     * @return int Points earned (usually 0 for manual grading)
     */
    private function handleTextAnswer(Question $question, ExamAttempt $attempt, mixed $answerValue): int
    {
        $attempt->answers()->create([
            'question_id' => $question->id,
            'answer_text' => $answerValue,
        ]);

        return 0;
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
        
        $maxEndTime = $startTime->copy()->addMinutes($exam->duration_minutes)->addMinutes(1);

        if (now()->greaterThan($maxEndTime)) {
            
            $attempt->update(['completed_at' => $startTime->addMinutes($exam->duration_minutes)]);

            throw new \Exception('Submission rejected. Time limit exceeded.');
        }
    }
}
