<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Services\TransactionService;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
        private CategoryService $categoryService
    ) {}

    /**
     * Display the dashboard with spreadsheet-like transactions.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Get limited transactions for the grid to act as eager/lazy loading
        $transactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        $categories = $this->categoryService->getForUser($user);

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
        Gate::authorize('update', $transaction);

        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'amount'      => ['required', 'numeric', 'min:0'],
            'date'        => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        if (isset($validated['description'])) {
            $validated['description'] = strip_tags($validated['description']);
        }

        $this->transactionService->update($transaction, $validated);

        return back()->with('success', 'Transaksi diperbarui.');
    }
}
