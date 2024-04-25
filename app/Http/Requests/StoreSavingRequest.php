<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class StoreSavingRequest extends FormRequest
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
            'save_goal' => 'required|integer',
            'save_end_date' => 'nullable|date|after_or_equal:today',
            //'save_fixed_amount' => 'nullable|integer',
            'schedule_id' => 'required|integer|in:4'
        ];
    }

    public function messages()
    {
        return [
            'schedule_id.in' => 'Schedule id can only be 4'
        ];
    }
}
