<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



use App\Models\Brand;


#[Fillable([
    'name',
        'slug',
        'description',
        'base_price',
        'sale_price',
        'gender',
        'is_active',
        'is_featured',
        'category_id',
        'brand_id'
])]

class Product extends Model
{
   use hasFactory;
  
   public function brand(): BelongsTo 
   {
    return $this->belongsTo(Brand::class, 'brand_id');}  //1 produkt i perket 1 brandi. po 1 brand ka shume produkte


}
