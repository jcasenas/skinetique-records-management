<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryMethod extends Model
{
    protected $table = 'delivery_methods';

    protected $fillable = ['type', 'courier_name'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Returns a readable label e.g. "Shipping — JRS" or "Pickup"
    public function getLabelAttribute(): string
    {
        if ($this->courier_name) {
            return ucfirst($this->type) . ' — ' . $this->courier_name;
        }
        return ucfirst($this->type);
    }
}