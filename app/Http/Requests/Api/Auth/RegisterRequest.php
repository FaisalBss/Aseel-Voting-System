<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|regex:/^[A-Za-z\s]+$/|string|max:255',
            'mobile_number' => 'required|string|regex:/^[0-9+]+$/|max:15|unique:users,mobile_number',
            'username' => 'required|alpha_num|string|max:50|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}
