<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'product_id',
    ];

    protected $casts = [
        'type' => 'integer',
    ];

    // علاقة مع المنتج
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // علاقة مع القيم (VariantValue)


    public function values()
{
    return $this->hasMany(VariantValue::class, 'variant_id');
}

}
