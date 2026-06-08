<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


#[Fillable([
     'order_id', 'variant_id', 'product_name', 'variant_size',
        'variant_color', 'variant_sku', 'quantity', 'unit_price', 'subtotal'
])]

class OrderItem extends Model
{
       
 
    
 
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
 
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
