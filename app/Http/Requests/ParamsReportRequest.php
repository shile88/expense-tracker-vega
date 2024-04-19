<?php

namespace App\Http\Requests;

use App\Rules\IncomeOrExpenseGroupExists;
use Illuminate\Foundation\Http\FormRequest;

class ParamsReportRequest extends FormRequest
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
            'type' => 'nullable|string|in:income,expense',
            'group' => ['nullable', 'string', new IncomeOrExpenseGroupExists()],
            'schedule' => 'nullable|string|exists:schedules,type',
            'end_date' => 'nullable|date|date_format:Y-m-d'
        ];
    }
}
