<?php

namespace App\Http\Requests\Match;

use App\Domain\Match\Enums\MatchType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateMatchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'language_from_id' => ['required', 'integer', Rule::exists('languages', 'id')],
            'language_to_id' => ['required', 'integer', Rule::exists('languages', 'id')],
            'dictionary_id' => ['nullable', 'integer', Rule::exists('dictionaries', 'id')],
            'match_type' => ['required', Rule::enum(MatchType::class)],
            'match_type_params' => ['required', 'array'],
            'match_type_params.duration' => ['required_if:match_type,time', 'integer', 'min:60', 'max:3600'],
            'match_type_params.steps' => ['required_if:match_type,steps', 'integer', 'min:1', 'max:100'],
            'participants' => ['required', 'array', 'min:2', 'max:10'],
            'participants.*.type' => ['required', 'in:user,guest'],
            'participants.*.id' => ['required'],
            'participants.*.name' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'participants.min' => 'Match requires at least 2 participants',
            'participants.max' => 'Match can have maximum 10 participants',
        ];
    }
}
