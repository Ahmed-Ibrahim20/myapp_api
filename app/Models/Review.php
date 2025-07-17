<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_add_id',
        'product_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    // علاقة مع المستخدم الذي أضاف التقييم
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
