<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePollRequest extends FormRequest
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

        $dateTimeFormat = 'Y/m/d/H:i';
        return [
            'title' => 'required|string|max:500',
            'description' => 'nullable|string|max:3000',
            'start_time' => 'required|date_format:' . $dateTimeFormat . '|after_or_equal:now',
            'end_time' => 'required|date_format:'. $dateTimeFormat . '|after:start_time',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:255',
        ];
    }
}
