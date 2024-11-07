<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Company;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $companies = Company::all();
        
        return view('products.index')->with([
            'products' => $products,
            'companies' => $companies,
        ]);
    }

    public function showCreate(){
        //companyテーブル
        $companies = Company::all();

        return view('products.create')->with([
            'companies' => $companies,
        ]);
    }
    
    
    


    //show.bladeの表示をしてid情報を数字毎に表示させる
    public function show($id){
        $product = Product::find($id);

        return view('products.show')->with([
            'product' => $product,
        ]);
    }
    //編集画面移行
    public function showEdit($id) {
        $product = Product::find($id);
        $companies = Company::all(); // すべてのメーカー情報を取得
        return view('products.edit')->with([
            'product' => $product,
            'companies' => $companies, // ここで$companiesをビューに渡す
        ]);
    }

    // 編集画面の表示
    public function edit($id)
    {
        $product = Product::findOrFail($id); // IDで商品を取得、見つからなければ404エラー
        $companies = Company::all(); // すべてのメーカーを取得

        return view('products.edit', compact('product', 'companies')); // 編集画面に商品データを渡す
    }


   //新規登録機能
   public function create(ProductRequest $request){
       DB::beginTransaction();
       try {
           $product = new Product();
           
           if ($request->hasFile('image')) {
               $image = $request->file('image');
               $imageName = $image->getClientOriginalName();
               $image->storeAs('public/images', $imageName);
               $img_path = 'storage/images/' . $imageName;
               $product->img_path = $img_path;
            }
            
            $product->createProduct($request, $img_path ?? null); // 画像がない場合はnull
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect(route('show.create'))->with('error', '商品登録に失敗しました。');
        }
        
        return redirect()->route('products.index');
    }
    
    
    
    // 更新処理
    
    public function update(ProductRequest $request, $id) {
         

        DB::beginTransaction();
        try {
            
            // 商品をIDで検索
            $product = Product::findOrFail($id);
    
            // 画像がアップロードされている場合の処理
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $image->storeAs('public/images', $imageName);
                $img_path = 'storage/images/' . $imageName;
                
                // 古い画像が存在する場合は削除
                if (File::exists(public_path($product->img_path))) {
                    File::delete(public_path($product->img_path));
                }
                
                // 新しい画像パスを保存
                $product->img_path = $img_path;
               

                
            }

            $product->comment = $request->input('comment');
    
            // 商品情報の更新処理
            $product->update($request->validated());
            DB::commit();
    
            return redirect()->route('products.index')->with('success', '商品情報を更新しました。');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect(route('show.edit', $id))->with('error', '商品更新に失敗しました。');
        }
    }
    
    
    


     //1.ProductRequestでルールに沿った情報を入れてるかチェック　
     //1.$requestは入力した変更後のデータ $idは「$id番目」のデータを取得するときに使う引数
     
     //2.トランザクション実行の宣言を書く
     //3.try and catchを書く（テンプレ）
     //4.(tryの中)登録するための正規の処理
     //4.(catchの中)データベースでエラーが出て処理ができなかった時の処理
     //5.ルーティングの「->name()」を参照してビューにリダイレクトする処理

     //削除処理
     public function delete($id)
{
    DB::beginTransaction();
    try {
        $product = Product::findOrFail($id);
        $product->delete();
        DB::commit();
        return response()->json(['success' => '商品が削除されました']);
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json(['error' => '削除に失敗しました'], 500);
    }
}

     
    
    

    public function search(Request $request) {
        $query = Product::query()->with('company'); // companyと連結
    
        // 商品名検索
        if ($request->name_search) {
            $query->where('product_name', 'LIKE', '%' . $request->name_search . '%');
        }
    
        // 会社名検索
        if ($request->company_search) {
            $query->whereHas('company', function($q) use ($request) {
                $q->where('company_name', $request->company_search);
            });
        }
    
        // 価格・在庫数フィルタ
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
    
        // ソート処理
        if ($request->sort_column && $request->sort_order) {
            if ($request->sort_column === 'company_name') {
                $query->join('companies', 'products.company_id', '=', 'companies.id')
                      ->orderBy('companies.company_name', $request->sort_order);
            } else {
                $query->orderBy($request->sort_column, $request->sort_order);
            }
        } else {
            // 初期ソート: IDの昇順
            $query->orderBy('id', 'asc');
        }
    
        $products = $query->select('products.*')->get(); // join使用時にproducts.*を明示
    
        return response()->json($products);
    }
    
     
     

 
    public function searchStock(Request $request)
    {
        $stockMin = $request->get('stock_min', 0); // 最小在庫数
        $stockMax = $request->get('stock_max', null); // 最大在庫数が指定されていない場合はnull

        // 在庫数でフィルタリング
        $query = Product::query();
        
        if ($stockMin !== null) {
            $query->where('stock', '>=', $stockMin);
        }
        
        if ($stockMax !== null) {
            $query->where('stock', '<=', $stockMax);
        }

        $products = $query->get();

        return response()->json($products); // 結果をJSONで返す

        \Log::info('searchStock endpoint accessed');
    \Log::info('Request parameters:', $request->all());
    
    // 検索ロジック
    }
     

    
}

    

    
       
          
    



     
     



//もし検索キーワードで検索したら検索キーワードのみ表示 company_name実行でcompany_nameのみ表示
//$request->keywordがnullじゃない場合は検索キーワードは実行する
//もしcompanyがnull

