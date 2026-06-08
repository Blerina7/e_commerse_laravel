<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([ 
    'product_id',
    'sku',
    'size', 
    'color', 
    'color_hex',
    'stock_quantity', 
    'price_override', 
    'is_available'
])]

class ProductVariant extends Model
{
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
 
    public function orderItems():HasMany
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }
 
    public function cartItems():HasMany
    {
        return $this->hasMany(CartItem::class, 'variant_id');
    }
 
    // Çmimi final i variantit
    public function getFinalPriceAttribute(): float
    {
        return $this->price_override ?? $this->product->current_price;
    }
 
    public function isInStock(): bool
    {
        return $this->is_available && $this->stock_quantity > 0;
    }
 
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('stock_quantity', '>', 0);
    }
}

