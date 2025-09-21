<?php

namespace App\Models\Vendor;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{

    function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    function products()
    {
        return $this->hasMany(Product::class, 'product_id');
    }
}
