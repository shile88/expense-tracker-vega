<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseGroupRequest extends FormRequest
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
        $expenseGroup = $this->route('expense_group');
      
        return [
            'name' => [
                'required',
                'string',
                'alpha',
                Rule::unique('expense_groups', 'name')->ignore($expenseGroup->id),
            ],
            'group_budget' => 'nullable|integer|min:10'
        ];
    }
}
