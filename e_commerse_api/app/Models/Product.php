<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


use App\Models\Brand;


#[Fillable([
        'category_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'gender',
        'is_active',
        'is_featured'
])]

class Product extends Model
{
   use hasFactory;
  
  public function brand() : BelongsTo {
    return $this->belongsTo(Brand::class);
    }//1 produkt i perket 1 brandi. po 1 brand ka shume produkte
   
    public function category():BelongsTo{
     return $this->belongsTo(Category::class);
    }
     //1 produkt i perket 1 kategorie. 1 kategori ka shume produkte
    
 

  public function variants() :HasMany{
    return $this->hasMany(ProductVariant::class);
  }
   //1 produkt ka shume variante
    
 
    public function images():HasMany{
        return $this->hasMany(ProductImage::class)->orderBy('sort_order'); //1 produkt ka shume  imazhe
    }
 
    


}
