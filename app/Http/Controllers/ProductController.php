<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Company;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Exception;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('company')->get();
        $companies = Company::all();
        
        return view('products.index', compact('products', 'companies'));
    }

    public function showCreate()
    {
        $companies = Company::all(); // 会社情報を取得
        return view('products.create', compact('companies')); // ビューに渡す
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('products.show', compact('product'));
    }

    public function showEdit($id)
    {
        $product = Product::findOrFail($id);
        $companies = Company::all();
        return view('products.edit', compact('product', 'companies'));
    }

    public function create(ProductRequest $request)
    {
        DB::beginTransaction();
        try {
            $img_path = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $image->storeAs('public/images', $imageName);
                $img_path = 'storage/images/' . $imageName;
            }

            // モデルで商品登録処理を呼び出す
            Product::createProduct($request, $img_path);
            DB::commit();
            return redirect()->route('products.index')->with('success', '商品が登録されました。');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('show.create')->with('error', '商品登録に失敗しました。');
        }
    }

    public function update(ProductRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);
            $img_path = $product->img_path; // デフォルトは既存の画像パス

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $image->storeAs('public/images', $imageName);
                $img_path = 'storage/images/' . $imageName;

                if (File::exists(public_path($product->img_path))) {
                    File::delete(public_path($product->img_path)); // 古い画像を削除
                }
            }

            // モデルで商品更新処理を呼び出す
            Product::updateProduct($product, $request, $img_path);
            DB::commit();
            return redirect()->route('products.index')->with('success', '商品情報を更新しました。');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('show.edit', $id)->with('error', '商品更新に失敗しました。');
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            // モデルで商品削除処理を呼び出す
            Product::deleteProduct($id);
            DB::commit();
            return response()->json(['success' => '商品が削除されました']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => '削除に失敗しました'], 500);
        }
    }

    public function search(Request $request)
    {
        $query = Product::query()->with('company');

        // 検索条件を適用
        if ($request->name_search) {
            $query->where('product_name', 'LIKE', '%' . $request->name_search . '%');
        }

        if ($request->company_search) {
            $query->whereHas('company', function ($q) use ($request) {
                $q->where('company_name', $request->company_search);
            });
        }

        // 価格や在庫数などの条件も追加
        if ($request->price_min) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->price_max) {
            $query->where('price', '<=', $request->price_max);
        }
        if ($request->stock_min) {
            $query->where('stock', '>=', $request->stock_min);
        }
        if ($request->stock_max) {
            $query->where('stock', '<=', $request->stock_max);
        }

        // 並び替え処理
        if ($request->sort_column && $request->sort_order) {
            if ($request->sort_column === 'company_name') {
                $query->join('companies', 'products.company_id', '=', 'companies.id')
                      ->orderBy('companies.company_name', $request->sort_order);
            } else {
                $query->orderBy($request->sort_column, $request->sort_order);
            }
        } else {
            $query->orderBy('id', 'asc');
        }

        $products = $query->select('products.*')->get();
        return response()->json($products);
    }
}
