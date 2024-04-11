<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
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
            'amount' => 'required|integer',
            'schedule_id' => 'required|integer|exists:schedules,id',
            'end_date' => 'nullable|date|after_or_equal:today',
            'transaction_start' => 'required|integer|in:1,2,3,4,5,6,7'
        ];
    }
}
