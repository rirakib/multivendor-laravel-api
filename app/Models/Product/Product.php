<?php

namespace App\Models\Product;

use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use Searchable;

    protected $appends = ['thumbnail_image', 'image_path'];

    protected $casts = [
        'price' => 'float',
        'discount_price' => 'float',
        'stock_quantity' => 'integer'
    ];

    function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    function attributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id');
    }

    public function thumbnailImage()
    {
        return $this->belongsTo(ProductImage::class, 'thumbnail_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function getThumbnailImageAttribute()
    {
        return $this->thumbnailImage?->image ?? null;
    }


    public function getImagePathAttribute()
    {

        return url('assets/images/');
    }


    protected static function booted()
    {
        static::saved(function ($product) {
            Cache::forget("product_details_{$product->slug}");
        });

        static::deleted(function ($product) {
            Cache::forget("product_details_{$product->slug}");
        });
    }
}
