<x-mail::message>
# Budget notification

    Your expense with id: {{$expense->id}} and amount:{{$expense->amount}} made total amount of expenses: {{$expenseSum}}.

    Your budget for expense group:{{$expenseGroup}} was {{ $budgetCap }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
