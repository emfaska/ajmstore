<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'income_date',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'income_date' => 'date',
    ];

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeByDateRange($query, string $from, string $to)
    {
        return $query->whereBetween('income_date', [$from, $to]);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('title', 'like', "%{$search}%");
    }
}
