<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\PollStatus;

class UpdatePollRequest extends FormRequest
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
        // $allowedStatuses = ['draft', 'scheduled', 'active'];
        return [
            'title' => 'sometimes|required|string|max:500',
            'description' => 'sometimes|nullable|string|max:3000',
            'start_time' => ['sometimes', 'required', 'date_format:' . $dateTimeFormat],
            'end_time' => ['sometimes', 'required', 'date_format:' . $dateTimeFormat, 'after:start_time'],
            'status' => ['sometimes', 'required', Rule::enum(PollStatus::class)->except([PollStatus::Closed])],
        ];
    }
}
