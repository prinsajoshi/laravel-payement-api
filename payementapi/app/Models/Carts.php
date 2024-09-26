<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carts extends Model
{
    use HasFactory;
    protected $fillable=['user_id','product_id','quantity'];
    protected $hidden=['created_at','updated_at'];

    public function customer(){
        return $this->belongsTo(Customer::class,'user_id');
    }

    public function products(){
        return $this->belongsTo(Products::class,'product_id');
    }
}
