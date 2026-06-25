<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Report extends Model
{
    /**
     * Mass assignable attributes.
     */
    protected $fillable = ['user_id', 'start_date', 'end_date', 'total_income', 'total_expense', 'balance'];

    /**
     * The user that owns the report.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_income' => 'decimal:2',
            'total_expense' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }
}
