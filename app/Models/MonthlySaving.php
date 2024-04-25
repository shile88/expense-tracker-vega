<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlySaving extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function relatedSaving(): BelongsTo
    {
        return $this->belongsTo(Saving::class, 'saving_id');
    }
}
