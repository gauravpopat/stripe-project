<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = ['product_id','name','description','currency','stripe_id'];
    
    public function prices()
    {
        return $this->belongsToMany(Price::class)->withTimestamps();
    }
}
