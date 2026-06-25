<table>
    <thead>
        <tr>
            <th colspan="5">Financial Report: {{ $report->start_date }} to {{ $report->end_date }}</th>
        </tr>
        <tr>
            <th>Total Income:</th>
            <th>{{ $report->total_income }}</th>
            <th>Total Expense:</th>
            <th>{{ $report->total_expense }}</th>
            <th>Balance: {{ $report->balance }}</th>
        </tr>
        <tr>
            <th></th>
        </tr>
        <tr>
            <th>Date</th>
            <th>Category</th>
            <th>Type</th>
            <th>Description</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
    @foreach($transactions as $transaction)
        <tr>
            <td>{{ $transaction->date }}</td>
            <td>{{ $transaction->category ? $transaction->category->name : 'Uncategorized' }}</td>
            <td>{{ $transaction->category ? ucfirst($transaction->category->type) : '-' }}</td>
            <td>{{ $transaction->description }}</td>
            <td>{{ $transaction->amount }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
