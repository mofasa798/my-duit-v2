<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth()->toDateString());

        $transactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->get();

        $totalIncome = 0;
        $totalExpense = 0;

        foreach ($transactions as $tx) {
            if ($tx->category->type === 'income') {
                $totalIncome += $tx->amount;
            } else {
                $totalExpense += $tx->amount;
            }
        }
        $balance = $totalIncome - $totalExpense;

        $categoryBreakdown = $transactions->groupBy(function ($tx) {
            return $tx->category ? $tx->category->name : 'Uncategorized';
        })->map(function ($group) {
            $firstCategory = $group->first()->category;
            return [
                'amount' => $group->sum('amount'),
                'type' => $firstCategory ? $firstCategory->type : 'expense',
            ];
        });

        $trends = $transactions->groupBy('date')->map(function ($group) {
            return [
                'income' => $group->filter(fn($tx) => $tx->category && $tx->category->type === 'income')->sum('amount'),
                'expense' => $group->filter(fn($tx) => !$tx->category || $tx->category->type === 'expense')->sum('amount'),
            ];
        })->sortKeys();

        return Inertia::render('Analytics/Index', [
            'summary' => [
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'balance' => $balance,
            ],
            'categoryBreakdown' => $categoryBreakdown,
            'trends' => $trends,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ]
        ]);
    }
}
