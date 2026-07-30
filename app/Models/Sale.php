<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'vehicle_id',
        'payment_method_id',
        'invoice_number',
        'transaction_type',
        'notes',
        'sale_date',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'status',
        'payment_status',
    ];

    protected $casts = [
        'sale_date'    => 'date',
        'subtotal'     => 'decimal:2',
        'discount'     => 'decimal:2',
        'tax'          => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function cashTransactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class, 'referenceable_id')
                    ->where('referenceable_type', self::class);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Grand total after discount and tax.
     */
    public function getGrandTotalAttribute(): float
    {
        return (float) ($this->subtotal - $this->discount + $this->tax);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeByDateRange($query, string $from, string $to)
    {
        return $query->whereBetween('sale_date', [$from, $to]);
    }
}
