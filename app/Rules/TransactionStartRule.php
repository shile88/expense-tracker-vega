<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TransactionStartRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $scheduleId = request()->input('schedule_id');
        $allowedValues = null;
    
        switch ($scheduleId) {
            case 1:
                $allowedValues = ['empty'];
                break;
            case 2:
                $allowedValues = [1, 2, 3, 4, 5, 6, 7];
                break;
            case 3:
                $allowedValues = [1, 2, 3, 4, 5, 6, 7];
                break;
            case 4:
                $allowedValues = [0, 1];
                break;
            default:
                return;
        }
    
        $allowedValuesString = implode(', ', $allowedValues);
        if (!in_array($value, $allowedValues)) {
            $fail(":attribute must be $allowedValuesString for the given schedule_id.");
        }
    }
}
