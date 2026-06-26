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

        $totals = Transaction::query()
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $user->id)
            ->whereBetween('transactions.date', [$dateFrom, $dateTo])
            ->selectRaw('categories.type, SUM(transactions.amount) as total')
            ->groupBy('categories.type')
            ->pluck('total', 'type');

        $totalIncome = (float) ($totals['income'] ?? 0);
        $totalExpense = (float) ($totals['expense'] ?? 0);
        $balance = $totalIncome - $totalExpense;

        $categoryBreakdown = Transaction::query()
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $user->id)
            ->whereBetween('transactions.date', [$dateFrom, $dateTo])
            ->selectRaw('categories.name, categories.type, SUM(transactions.amount) as amount')
            ->groupBy('categories.id', 'categories.name', 'categories.type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->name => [
                    'amount' => (float) $item->amount,
                    'type' => $item->type,
                ]];
            });

        $trendsQuery = Transaction::query()
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $user->id)
            ->whereBetween('transactions.date', [$dateFrom, $dateTo])
            ->selectRaw('transactions.date, categories.type, SUM(transactions.amount) as amount')
            ->groupBy('transactions.date', 'categories.type')
            ->get();

        $trends = collect();
        foreach ($trendsQuery as $item) {
            $date = $item->date instanceof Carbon ? $item->date->toDateString() : (string) $item->date;
            if (!$trends->has($date)) {
                $trends->put($date, ['income' => 0, 'expense' => 0]);
            }
            $data = $trends->get($date);
            if ($item->type === 'income') {
                $data['income'] = (float) $item->amount;
            } else {
                $data['expense'] = (float) $item->amount;
            }
            $trends->put($date, $data);
        }
        $trends = $trends->sortKeys();

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
