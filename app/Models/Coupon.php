<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_value',
        'discount_type',
        'usage_limit',
        'start_date',
        'end_date',
        'description',
        'user_add_id',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'discount_type' => 'integer',
        'usage_limit' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // علاقة مع المستخدم الذي أضاف الكوبون
    public function userAdd()
    {
        return $this->belongsTo(User::class, 'user_add_id');
    }
}
