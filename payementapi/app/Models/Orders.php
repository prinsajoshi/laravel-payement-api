<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stripe\Discount;

class Orders extends Model
{
    use HasFactory;
    protected $fillable=['user_id','total_amount','status','order_number'];
    protected $hidden=['created_at','updated_at'];

    public function user(){
        return $this->belongsTo(Customer::class,'user_id');
    }
    public function shippingAddress() {
        return $this->hasOne(shipping_address::class, 'order_id');
    }
    public function discount() {
        return $this->belongsTo(Discount::class, 'discount_token');
    }

    public function order_products(){
        return $this->hasMany(order_product::class,'order_id');
    }

    

}
