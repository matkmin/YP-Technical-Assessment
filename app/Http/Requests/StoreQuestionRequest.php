<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // later you can restrict lecturer ownership
    }

    public function rules(): array
    {
        return [
            'question_text' => 'required|string',
            'type' => 'required|in:multiple_choice,text',
            'points' => 'required|integer|min:1',

            'options' => 'required_if:type,multiple_choice|array',
            'options.*.option_text' => 'required_with:options|string',
            'options.*.is_correct' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('options')) {
            $this->merge([
                'options' => collect($this->options)->map(fn ($option) => [
                    'option_text' => $option['option_text'] ?? null,
                    'is_correct' => isset($option['is_correct']),
                ])->toArray(),
            ]);
        }
    }
}
