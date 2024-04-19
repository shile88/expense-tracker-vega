<!DOCTYPE html>
<html>
    <style>
        /* Example CSS styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #007bff;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 10px;
        }
    </style>

    <div class="container">
        <h1>Financial report</h1>
        <p>Here is your financial report for the previous period:</p>
        <ul>
            <li>Account ID: {{ $accountId }}</li>
            <li>Type: {{ $type }}</li>
            <li>Total Income: {{ $totalIncome }}</li>
            <li>Total Expense: {{ $totalExpense }}</li>
            <li>Net Income: {{ $netIncome }}</li>
        </ul>
        <p>Your current balance on this account is: {{ $balance }}</p>
        <p>Thanks,<br>{{ config('app.name') }}</p>
    </div>
</html>