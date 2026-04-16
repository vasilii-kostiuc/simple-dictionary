<?php

namespace App\Http\Requests\Match;

use Illuminate\Foundation\Http\FormRequest;

class SubmitMatchAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Allow both authenticated users and guests
        return true;
    }

    public function rules(): array
    {
        return [
            'attempt_data' => ['required', 'array'],
            'attempt_number' => ['required', 'integer', 'min:1'],
            'participant_type' => ['required', 'in:user,guest'],
            'participant_id' => ['required'],
        ];
    }
}
