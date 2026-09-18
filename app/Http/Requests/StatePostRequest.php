<?php

namespace App\Http\Requests;

use App\Models\State;
use Illuminate\Foundation\Http\FormRequest;

class StatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', State::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'short_name' => 'required|string|max:50',
            'country_id' => 'required|integer|exists:countries,id',
        ];
    }
}
