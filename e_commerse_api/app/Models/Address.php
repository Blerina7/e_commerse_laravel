<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'full_name', 'phone', 'street',
        'city', 'state', 'zip_code', 'country', 'is_default'
])]
class Address extends Model
{
    public function user ():BelongsTo{
        return $this->belongsTo(User::class); //1 adrese specifike i perket 1 useri specifik
    }

    public function orders():HasMany{
        return $this->hasMany(Order::class); //1 adrese mund te kete shume porosi 
    }
}
