<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'quantity', 'price', 'total'
    ];

    // 購入処理を行うメソッド
    public static function purchaseProduct($product, $quantity)
    {
        // 在庫を減らす
        $product->decrement('stock', $quantity);

        // 購入情報を記録
        return Sale::create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $product->price,
            'total' => $product->price * $quantity
        ]);
    }
}
