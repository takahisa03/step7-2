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

        $product = Product::find($request->input('product_id'));
        logger($request->input('product_id')); 
        if (!$product) {
            return response()->json(['error' => '商品が見つかりませんでした'], 404);
        }

        if ($product->stock < $request->input('quantity')) {
            return response()->json(['error' => '在庫が不足しています'], 400);
        }

        $product->stock -= $request->input('quantity');
        $product->save();

        return response()->json(['success' => '購入が成功しました']);
    }
    
}
