<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIncomeGroupRequest extends FormRequest
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
        $incomeGroup = $this->route('income_group');
      
        return [
            'name' => [
                'required',
                'string',
                'alpha',
                Rule::unique('income_groups', 'name')->ignore($incomeGroup->id),
            ],
        ];
    }
}
