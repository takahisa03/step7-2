<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function purchase(Request $request)
{
    $request->validate([
        'product_id' => 'required|integer',
        'quantity' => 'required|integer|min:1'
    ]);

    DB::beginTransaction();

    try {
        $product = Product::find($request->input('product_id'));
        if (!$product) {
            return response()->json(['error' => '商品が見つかりませんでした'], 404);
        }

        if ($product->stock < $request->input('quantity')) {
            return response()->json(['error' => '在庫が不足しています'], 400);
        }

        // 在庫数を減らす処理を実行
        Sale::purchaseProduct($product, $request->input('quantity'));

        // 在庫数をログに出力して確認
        \Log::info('購入後の在庫数: ' . $product->stock);

        DB::commit();

        // 購入成功後に減少後の在庫数を返す
        return response()->json(['success' => '購入が成功しました', 'stock' => $product->stock]);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('購入処理中にエラーが発生しました: ' . $e->getMessage());
        return response()->json(['error' => '購入処理中にエラーが発生しました'], 500);
    }
}

    
}
