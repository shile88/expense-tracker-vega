<x-mail::message>
# Total expense budget reached

Your last expense with amount {{ $expense }} exceeded total budget set: {{ $accountBudget }} for period {{ $expenseBudgetStartDate }} - {{ $expenseBudgetEndDate }}.

Please check your account.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
