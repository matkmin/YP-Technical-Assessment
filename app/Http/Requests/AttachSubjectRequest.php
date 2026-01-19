<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => 'required|exists:subjects,id',
        ];
    }
}
