<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'price',
        'company_id',
        'stock',
        'comment',
        'img_path',
    ];

    // 会社とのリレーション
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // 商品登録処理
    public static function createProduct($request, $img_path)
    {
        return Product::create([
            'product_name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'comment' => $request->comment,
            'company_id' => $request->company_id,
            'img_path' => $img_path,
        ]);
    }

    // 商品更新処理
    public static function updateProduct($product, $request, $img_path)
    {
        $product->update([
            'product_name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'comment' => $request->comment,
            'img_path' => $img_path,
        ]);
    }

    // 商品削除処理
    public static function deleteProduct($id)
    {
        return Product::destroy($id);
    }
}
