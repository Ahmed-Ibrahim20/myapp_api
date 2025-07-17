<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_add_id',
        'product_id',
        'title',
        'description',
        'discount_type',
        'value',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    // علاقة مع المستخدم الذي أضاف العرض
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
