<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable=['username','password','email'];
    protected $hidden=['created_at','updated_at'];

    public function carts(){
        return $this->hasMany(Carts::class);
    }
    public function shippingAddresses() {
        return $this->hasMany(shipping_address::class, 'user_id');
    }
    
}
