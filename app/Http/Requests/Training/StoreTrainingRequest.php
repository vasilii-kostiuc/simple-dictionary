<?php

namespace App\Http\Requests\Training;

use App\Training\Enums\TrainingType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTrainingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'training_type_id' => ['required', 'integer', Rule::enum(TrainingType::class)],
            'dictionary_id' => ['required', 'integer', Rule::exists('dictionaries', 'id')],
        ];
    }
}
