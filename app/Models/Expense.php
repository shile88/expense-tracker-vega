<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['expense_group_id', 'amount', 'schedule_id', 'expense_date'];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function expenseGroup()
    {
        return $this->belongsTo(ExpenseGroup::class);
    }

}
