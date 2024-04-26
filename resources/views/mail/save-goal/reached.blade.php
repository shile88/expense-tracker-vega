<x-mail::message>
# Save Goal Reached

For your account with id: {{ $accountId }} you reached set save goal of {{ $amount }}.

You account balance has been updated with given amount.

New account balance: {{ $balance }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
