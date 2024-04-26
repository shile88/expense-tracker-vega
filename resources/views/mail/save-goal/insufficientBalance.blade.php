<x-mail::message>
# Insufficient funds in account 

Your account id: {{ $accountId }} did not have enough funds for monthly saving id: {{ $monthlySavingId }}. Difference in amount {{ $difference }}
is distributed in remaining {{ $numberOfRemainingSaving }} monthly saving and amount of {{ $amountToAdd }} is add to every saving.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
