<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
use HasFactory;

protected $fillable = [
'name',
'slug',
'description',
'logo',
'user_add_id',
];

// علاقة مع المستخدم الذي أضاف البراند
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