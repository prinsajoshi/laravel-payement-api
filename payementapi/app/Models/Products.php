<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Categories;


class Products extends Model
{
    use HasFactory;
    protected $fillable=['title','description','category_id','price','stock'];
    protected $hidden=['created_at','updated_at'];
    public function category(){
        return $this->belongsTo(Categories::class,'category_id');
    }

    public function carts(){
        return $this->hasMany(Carts::class);
    }

    public function order_product(){
        return $this->hasMany(Products::class);
    }

}
