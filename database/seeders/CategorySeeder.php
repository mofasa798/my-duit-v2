<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $incomes = ['Salary', 'Bonus', 'Investment'];
        foreach ($incomes as $income) {
            \App\Models\Category::create(['name' => $income, 'type' => 'income', 'user_id' => null]);
        }

        $expenses = ['Food', 'Transport', 'Utilities', 'Shopping', 'Entertainment'];
        foreach ($expenses as $expense) {
            \App\Models\Category::create(['name' => $expense, 'type' => 'expense', 'user_id' => null]);
        }
    }
}
