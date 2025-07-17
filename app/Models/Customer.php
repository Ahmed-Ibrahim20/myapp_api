<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'phone',
        'alternate_phone',
        'country',
        'address',
        'note',
        'user_add_id',
        'user_id',
    ];

    // علاقة مع المستخدم الذي أضاف العميل
    public function userAdd()
    {
        return $this->belongsTo(User::class, 'user_add_id');
    }

    // علاقة مع المستخدم المرتبط بالعميل
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
