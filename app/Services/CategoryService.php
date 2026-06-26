<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Get categories available for a given user (user's categories + global ones).
     *
     * @param Authenticatable $user
     * @return Collection
     */
    public function getForUser(Authenticatable $user): Collection
    {
        return Category::query()->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereNull('user_id');
        })->orderBy('type')->orderBy('name')->get();
    }
}
