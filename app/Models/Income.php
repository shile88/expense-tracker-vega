<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'amount', 'schedule_id', 'income_group_id'];

    public function incomeGroup()
    {
        return $this->belongsTo(IncomeGroup::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
