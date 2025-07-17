<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'tech_stack',
        'user_add_id',
    ];

    protected $casts = [
        'tech_stack' => 'array',
    ];

    // علاقة مع المستخدم الذي أضاف المتجر
    public function userAdd()
    {
        return $this->belongsTo(User::class, 'user_add_id');
    }
}
