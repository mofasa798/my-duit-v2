<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\TransactionService;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $service,
        private CategoryService $categoryService
    ) {}

    /**
     * Display a paginated list of the user's transactions.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'type', 'category_id', 'date_from', 'date_to']);

        $transactions = $this->service->getForUser(
            user:       $request->user(),
            search:     $filters['search'] ?? null,
            type:       $filters['type'] ?? null,
            categoryId: isset($filters['category_id']) ? (int) $filters['category_id'] : null,
            dateFrom:   $filters['date_from'] ?? null,
            dateTo:     $filters['date_to'] ?? null,
        );

        $categories = $this->categoryService->getForUser($request->user());

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'categories'   => $categories,
            'filters'      => $filters,
        ]);
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create(Request $request): Response
    {
        $categories = $this->categoryService->getForUser($request->user());

        return Inertia::render('Transactions/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a new transaction.
     */
    public function store(StoreTransactionRequest $request)
    {
        $this->service->create($request->user(), $request->validated());

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a transaction (must belong to authenticated user).
     */
    public function edit(Request $request, Transaction $transaction): Response
    {
        Gate::authorize('view', $transaction);

        $categories = $this->categoryService->getForUser($request->user());

        return Inertia::render('Transactions/Edit', [
            'transaction' => $transaction->load('category'),
            'categories'  => $categories,
        ]);
    }

    /**
     * Update the given transaction.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        Gate::authorize('update', $transaction);

        $this->service->update($transaction, $request->validated());

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Delete the given transaction.
     */
    public function destroy(Request $request, Transaction $transaction)
    {
        Gate::authorize('delete', $transaction);

        $this->service->delete($transaction);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
