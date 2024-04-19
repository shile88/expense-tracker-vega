<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['expense_group_id', 'amount', 'schedule_id', 'end_date', 'transaction_start'];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function expenseGroup(): BelongsTo
    {
        return $this->belongsTo(ExpenseGroup::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function scopeSearch($query, $validatedRequest, $account)
    {
        if (empty($validatedRequest['type'])) {
            return  $query->leftJoin('expense_groups', 'expenses.expense_group_id', '=', 'expense_groups.id')
                ->where('expense_groups.account_id', $account->id);
        }

        $query->leftJoin('expense_groups', 'expenses.expense_group_id', '=', 'expense_groups.id')->where('expense_groups.account_id', $account->id)
            ->when($validatedRequest['group'], function ($query) use ($validatedRequest) {
                $query->where('expense_groups.name', $validatedRequest['group']);
            })
            ->when($validatedRequest['schedule'], function ($query) use ($validatedRequest) {
                $query->leftJoin('schedules', 'expenses.schedule_id', '=', 'schedules.id')->where('schedules.type', $validatedRequest['schedule']);
            })
            ->when($validatedRequest['end_date'], function ($query) use ($validatedRequest) {
                $query->where('end_date', '<=', $validatedRequest['end_date']);
            });

        return $query;
    }
}
