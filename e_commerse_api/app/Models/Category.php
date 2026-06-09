<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'parent_id', 'name', 'slug', 'description', 'image_url', 'sort_order', 'is_active'
])]



class Category extends Model
{
  
   protected $table = 'category';
 
    // Vetë-referenca (sub-kategori)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
 
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
 
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
