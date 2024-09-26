<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class shipping_address extends Model
{
    use HasFactory;
    protected $fillable=['user_id','order_id','shipping_address','city','state','zip_code','country'];

    public function order() {
        return $this->belongsTo(Orders::class, 'order_id');
    }
    public function customer() {
        return $this->belongsTo(Customer::class, 'user_id');
    }
    
    
}
