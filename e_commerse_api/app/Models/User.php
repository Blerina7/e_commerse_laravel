<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;


#[Fillable([
    'name', 
    'last_name', 
    'email', 
    'password', 
    'birth_date', 
    'verification_code', 
    'is_verified', 
    'role'
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasApiTokens, Notifiable;
    

   
    public function orders() :HasMany
    {
        return $this->hasMany(Order::class); //1 perdorues mund te kete shume porosi
    }

    public function address():HasMany{
        return $this->hasMany(Address::class); //1 perdorues mund te kete shume adresa
    }
   
    public function cartItem():HasMany{
        return $this->hasMany(CartItem::class); //1 perdorues mudn te ket shume gjera ne karte
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean', 
            'birth_date' => 'date',
        ];
    }
    

}
