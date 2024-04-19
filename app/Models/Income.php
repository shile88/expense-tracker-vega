<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['amount', 'schedule_id', 'income_group_id', 'end_date', 'transaction_start'];

    public function incomeGroup(): BelongsTo
    {
        return $this->belongsTo(IncomeGroup::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function scopeSearch($query, $validatedRequest, $account)
    {
        if (empty($validatedRequest['type'])) {
            return $query->leftJoin('income_groups', 'incomes.income_group_id', '=', 'income_groups.id')
                ->where('income_groups.account_id', $account->id);
        }

        $query->leftJoin('income_groups', 'incomes.income_group_id', '=', 'income_groups.id')->where('income_groups.account_id', $account->id)
            ->when($validatedRequest['group'], function ($query) use ($validatedRequest) {
                $query->where('income_groups.name', $validatedRequest['group']);
            })
            ->when($validatedRequest['schedule'], function ($query) use ($validatedRequest) {
                $query->leftJoin('schedules', 'incomes.schedule_id', '=', 'schedules.id')->where('schedules.type', $validatedRequest['schedule']);
            })
            ->when($validatedRequest['end_date'], function ($query) use ($validatedRequest) {
                $query->where('end_date', '<=', $validatedRequest['end_date']);
            });

        return $query;
    }
}
