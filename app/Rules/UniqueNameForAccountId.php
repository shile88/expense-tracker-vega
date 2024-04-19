<?php

namespace App\Rules;

use App\Models\IncomeGroup;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueNameForAccountId implements ValidationRule
{
    public function __construct(protected $account)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $checkName = IncomeGroup::where('account_id', $this->account->id)
            ->where('name', $value)
            ->exists();

        if ($checkName) {
            $fail('The :attribute has already been taken for this account.');
        }
    }
}
