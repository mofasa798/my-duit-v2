<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::query()->where('email', 'test@example.com')->first();
        if (!$user) return;

        $categories = \App\Models\Category::all();
        if ($categories->isEmpty()) return;

        for ($i = 0; $i < 20; $i++) {
            $category = $categories->random();
            \App\Models\Transaction::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'amount' => rand(10, 500),
                'description' => 'Sample ' . $category->name . ' transaction',
                'date' => now()->subDays(rand(0, 30)),
            ]);
        }
    }
}
