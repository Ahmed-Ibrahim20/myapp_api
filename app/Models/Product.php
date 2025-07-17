<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'slug',
        'description',
        'price',
        'compare_price',
        'cost_price',
        'weight',
        'dimensions',
        'images',
        'user_add_id',
        'supplier_id',
        'category_id',
        'brand_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'float',
        'dimensions' => 'array',
        'images' => 'array',
    ];

    // علاقة مع المستخدم الذي أضاف المنتج
    public function userAdd()
    {
        return $this->belongsTo(User::class, 'user_add_id');
    }

    // علاقة مع المورد
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // علاقة مع القسم
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // علاقة مع البراند
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // علاقة مع صور المنتج
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
