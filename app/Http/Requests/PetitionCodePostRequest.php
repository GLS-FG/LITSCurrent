<?php

namespace App\Http\Requests;

use App\Models\PetitionCode;
use Illuminate\Foundation\Http\FormRequest;

class PetitionCodePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', PetitionCode::class);
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:3',
            'description' => 'required|string|max:500',
            'application_assumptions' => 'required|string|max:500',
        ];
    }
}
