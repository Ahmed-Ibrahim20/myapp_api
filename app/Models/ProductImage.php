<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_add_id',
        'product_id',
        'url',
        'alt_text',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // علاقة مع المستخدم الذي أضاف الصورة
    public function userAdd()
    {
        return $this->belongsTo(User::class, 'user_add_id');
    }

    // علاقة مع المنتج
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
