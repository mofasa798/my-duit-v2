<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

    /**
     * Display the dashboard with spreadsheet-like transactions.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Get ALL transactions for the grid (or we could paginate, but let's send all for client-side AG Grid features)
        $transactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $categories = Category::query()->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereNull('user_id');
        })->orderBy('type')->orderBy('name')->get();

        return Inertia::render('Dashboard', [
            'transactions' => $transactions,
            'categories'   => $categories,
        ]);
    }

    /**
     * Handle inline updates from AG Grid.
     */
    public function updateInline(Request $request, Transaction $transaction)
    {
        abort_unless($transaction->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'amount'      => ['required', 'numeric', 'min:0'],
            'date'        => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $this->transactionService->update($transaction, $validated);

        return back()->with('success', 'Transaksi diperbarui.');
    }
}
