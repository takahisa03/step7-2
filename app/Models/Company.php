<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    // productsテーブルとの関連付け
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
