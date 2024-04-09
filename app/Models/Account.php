<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['balance', 'expense_end_date', 'expense_budget', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
