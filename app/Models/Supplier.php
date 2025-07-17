<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'phone',
        'alternate_phone',
        'total',
        'country',
        'address',
        'note',
        'user_add_id',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    // علاقة مع المستخدم الذي أضاف المورد
    public function userAdd()
    {
        return $this->belongsTo(User::class, 'user_add_id');
    }

    // علاقة مع المنتجات
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
