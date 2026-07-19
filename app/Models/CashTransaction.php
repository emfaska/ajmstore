<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CashTransaction extends Model
{
    protected $fillable = [
        'payment_method_id',
        'type',
        'amount',
        'description',
        'referenceable_type',
        'referenceable_id',
        'transaction_date',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'transaction_date' => 'date',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Polymorphic parent: Sale, Purchase, or any future model.
     */
    public function referenceable(): MorphTo
    {
        return $this->morphTo();
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeDebit($query)
    {
        return $query->where('type', 'debit');
    }

    public function scopeCredit($query)
    {
        return $query->where('type', 'credit');
    }

    public function scopeByDateRange($query, string $from, string $to)
    {
        return $query->whereBetween('transaction_date', [$from, $to]);
    }
}
