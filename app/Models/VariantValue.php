<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'image_name',
        'color_name',
        'variant_id',
        'product_id',
    ];

    // علاقة مع الـ Variant
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // علاقة مع المنتج
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
