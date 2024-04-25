<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['balance', 'expense_end_date', 'expense_budget', 'user_id', 'type'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function incomeGroups(): HasMany
    {
        return $this->hasMany(IncomeGroup::class);
    }

    public function expenseGroups(): HasMany
    {
        return $this->hasMany(ExpenseGroup::class);
    }

    public function savings(): HasMany
    {
        return $this->hasMany(Saving::class);
    }
}
