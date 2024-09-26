<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class discount extends Model
{
    use HasFactory;
    protected $fillable=["discount_token","discount_percentage"];

    public function orders(){
        return $this->hasMany(Orders::class,"discount_token");
    }

    
}
