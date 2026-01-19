<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in Controller/Middleware usually, but can check exam access here too.
    }

    public function rules(): array
    {
        return [
            'answers' => 'array',
            'answers.*' => 'nullable', // Answers can be text or ID, validation of values happens in logic partially
        ];
    }
}
