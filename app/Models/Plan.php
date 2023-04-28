<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'amount',
        'currency',
        'interval',
        'interval_unit',
        'description',
        'stripe_id'
    ];
    
    public function prices()
    {
        return $this->belongsToMany(Price::class)->withTimestamps();
    }
}
