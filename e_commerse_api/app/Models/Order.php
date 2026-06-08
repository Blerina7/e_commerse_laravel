<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'user_id', 
    'address_id',
    'order_number',
    'status',
    'subtotal', 
    'shipping_cost', 
    'discount_amount', 
    'total_amount',
    'coupon_code', 
    'notes', 
    'shipped_at', 
    'delivered_at'
])]
 
class Order extends Model
{
   public function user():BelongsTo{
    return $this->belongsTo(User::class); //1 order i perket vetem 1 perdoruesi
   }

   public function address():BelongsTo{
    return $this->belongsTo(Address::class); // 1 order i perket vetem 1 adresee
   }
   
   public function payment():HasOne{
    return $this->hasOne(Payment::class);
   }
}
