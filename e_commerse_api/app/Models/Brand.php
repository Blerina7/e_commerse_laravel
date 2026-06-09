<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;

use App\Models\Product;

#[Fillable([
   'name', 'slug', 'logo_url', 'description', 'is_active'
])]



class Brand extends Model
{
   
    protected $table = 'brand';
    public function products(): HasMany {
    return $this->hasMany(Product::class);} //1 brand ka shume produkte
    
 
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
