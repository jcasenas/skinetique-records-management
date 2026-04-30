<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturn extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'order_id', 'product_id', 'employee_id',
        'quantity', 'reason', 'refund_amount', 'return_date',
    ];

    protected function casts(): array
    {
        return [
            'return_date'   => 'date',
            'refund_amount' => 'decimal:2',
            'quantity'      => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}