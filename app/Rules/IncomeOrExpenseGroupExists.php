<?php

namespace App\Rules;

use App\Models\ExpenseGroup;
use App\Models\IncomeGroup;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IncomeOrExpenseGroupExists implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isIncomeGroup = IncomeGroup::where('name', $value)->exists() || ExpenseGroup::where('name', $value)->exists();

        if(!$isIncomeGroup)
            $fail('The selected group name does not exist in database.');
    }
}
