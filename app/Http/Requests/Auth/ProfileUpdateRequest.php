<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'email' => ['string', 'email', 'max:255', 'unique:users'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,', 'max:200048'],
            'current_dictionary' => [Rule::exists('dictionaries', 'id')->where(function (Builder &$query) {
                //dd($this->current_dictionary, Auth::user()->id);
                $query->where('user_id', Auth::user()->id);
                $query->where('id', (int)$this->current_dictionary);
            }),],
        ];
    }

}
