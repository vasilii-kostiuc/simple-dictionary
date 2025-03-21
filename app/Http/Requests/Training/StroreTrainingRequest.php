<?php

namespace App\Http\Requests\Training;

use App\Enums\TrainingType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StroreTrainingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'training_type' => ['required', 'integer', Rule::enum(TrainingType::class)],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
        ];
    }
}
