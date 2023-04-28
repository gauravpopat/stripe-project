<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'plan_id',
        'amount',
        'currency',
        'stripe_id',
        'product_id'
    ];
    
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
