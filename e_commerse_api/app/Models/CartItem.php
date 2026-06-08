<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
#[Fillable([
    'user_id', 'variant_id', 'quantity'
])]

class CartItem extends Model
{
   
 
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
 
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
 
    public function getSubtotalAttribute(): float
    {
        return $this->variant->final_price * $this->quantity;
    }
}
