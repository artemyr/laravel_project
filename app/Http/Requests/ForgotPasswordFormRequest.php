<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guest();
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }
}
