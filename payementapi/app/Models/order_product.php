<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_product extends Model
{
    use HasFactory;
    protected $fillable=['order_id','product_id','quantity'];

    public function orders(){
        return $this->belongsTo(Orders::class,'order_id');
    }
  
    public function products(){
        return $this->belongsTo(Products::class,'product_id');
    }
}
