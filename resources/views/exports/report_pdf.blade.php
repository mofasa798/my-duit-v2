<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 5px;
        }
        h3 {
            text-align: center;
            font-weight: normal;
            margin-top: 0;
            color: #666;
        }
        .summary {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .summary th, .summary td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .summary th {
            background-color: #f9f9f9;
        }
        .transactions {
            width: 100%;
            border-collapse: collapse;
        }
        .transactions th, .transactions td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .transactions th {
            background-color: #f2f2f2;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .text-green {
            color: green;
        }
        .text-red {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Financial Report</h1>
    <h3>{{ \Carbon\Carbon::parse($report->start_date)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($report->end_date)->format('M d, Y') }}</h3>

    <table class="summary">
        <tr>
            <th>Total Income</th>
            <td class="text-right text-green">{{ number_format($report->total_income, 2) }}</td>
            <th>Total Expense</th>
            <td class="text-right text-red">{{ number_format($report->total_expense, 2) }}</td>
            <th>Balance</th>
            <td class="text-right"><strong>{{ number_format($report->balance, 2) }}</strong></td>
        </tr>
    </table>

    <table class="transactions">
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Type</th>
                <th>Description</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
            <tr>
                <td>{{ \Carbon\Carbon::parse($tx->date)->format('M d, Y') }}</td>
                <td>{{ $tx->category ? $tx->category->name : '-' }}</td>
                <td>{{ $tx->category ? ucfirst($tx->category->type) : '-' }}</td>
                <td>{{ $tx->description }}</td>
                <td class="text-right {{ $tx->category && $tx->category->type === 'expense' ? 'text-red' : 'text-green' }}">
                    {{ number_format($tx->amount, 2) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;">No transactions found in this period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
