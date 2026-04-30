<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\OrderReturn;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'customer_id',
        'delivery_method_id',
        'order_date',
        'subtotal',
        'delivery_fee',
        'total',
        'payment_status',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'order_date'   => 'date',
            'subtotal'     => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'total'        => 'decimal:2',
            'archived_at'  => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function deliveryMethod(): BelongsTo
    {
        return $this->belongsTo(DeliveryMethod::class);
    }

    public function orderLines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getOrderLabelAttribute(): string
    {
        return 'O' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(OrderReturn::class, 'order_id');
    }
}