<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionService
{
    /**
     * Get paginated transactions for a user with optional search and filters.
     */
    public function getForUser(
        Authenticatable $user,
        ?string $search = null,
        ?string $type = null,
        ?int $categoryId = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = Transaction::query()
            ->with('category')
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where('description', 'like', "%{$search}%");
        }

        if ($type && in_array($type, ['income', 'expense'])) {
            $query->whereHas('category', fn ($q) => $q->where('type', $type));
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($dateFrom) {
            $query->whereDate('date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('date', '<=', $dateTo);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Create a new transaction for the given user.
     */
    public function create(Authenticatable $user, array $data): Transaction
    {
        return Transaction::create([
            'user_id'     => $user->id,
            'category_id' => $data['category_id'],
            'amount'      => $data['amount'],
            'description' => $data['description'] ?? null,
            'date'        => $data['date'],
        ]);
    }

    /**
     * Update an existing transaction.
     */
    public function update(Transaction $transaction, array $data): Transaction
    {
        $transaction->update([
            'category_id' => $data['category_id'],
            'amount'      => $data['amount'],
            'description' => $data['description'] ?? null,
            'date'        => $data['date'],
        ]);

        return $transaction->fresh('category');
    }

    /**
     * Delete a transaction.
     */
    public function delete(Transaction $transaction): void
    {
        Transaction::destroy($transaction->getKey());
    }
}
