<?php

namespace App\Http\Requests\Match;

use App\Core\Language\Models\Language;
use App\Core\Match\Enums\MatchType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMatchLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->mergeIfMissing([
            'language_from_id' => Language::query()->where('code', 'en')->value('id'),
            'language_to_id' => Language::query()->where('code', 'ru')->value('id'),
        ]);
    }

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
            'participants_limit' => ['nullable', 'integer', 'min:2', 'max:10'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'participants_limit.min' => 'Match link requires at least 2 participants',
            'participants_limit.max' => 'Match link can have maximum 10 participants',
        ];
    }
}
