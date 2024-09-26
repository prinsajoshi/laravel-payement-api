<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Products;


class Categories extends Model
{
    use HasFactory;
    protected $fillable=['category_name'];
    protected $hidden=['created_at','updated_at'];

    public function products(){
        return $this->hasMany(Products::class,'category_id');
    }

}
