<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Transaction;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

class ReportService
{
    /**
     * Generate a new financial report for a user within a date range.
     */
    public function generate(Authenticatable $user, string $startDate, string $endDate): Report
    {
        $transactions = Transaction::with('category')
            ->where('user_id', '=', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalIncome = 0;
        $totalExpense = 0;

        foreach ($transactions as $tx) {
            if ($tx->category && $tx->category->type === 'income') {
                $totalIncome += $tx->amount;
            } else {
                $totalExpense += $tx->amount;
            }
        }

        $balance = $totalIncome - $totalExpense;

        return Report::create([
            'user_id' => $user->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $balance,
        ]);
    }

    /**
     * Get all reports for a specific user.
     */
    public function getForUser(Authenticatable $user): Collection
    {
        return Report::where('user_id', '=', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Delete a report.
     */
    public function delete(Report $report): void
    {
        $report->delete();
    }
}
