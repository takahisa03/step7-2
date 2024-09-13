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
    public function showEdit($id){
        $product = Product::find($id);
        return view('products.edit')->with([
            'product' => $product,
        ]);

    }

    // 編集画面の表示
    public function edit($id)
    {
        $product = Product::findOrFail($id); // IDで商品を取得、見つからなければ404エラー
        return view('products.edit', compact('product')); // 編集画面に商品データを渡す
    }


   //新規登録機能
   public function create(ProductRequest $request)
   {
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
                dd($image);
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
     public function delete($id){
         //トランザクション実行
         DB::beginTransaction();
         try {
             $product = Product::find($id);
             $product->deleteProduct($product, $id);
             DB::commit();

       } catch (Exception $e) {
        return redirect()->route('products.index');
       }

       return redirect()->route('products.index');

     }

    //検索機能
    public function search(Request $request)
    {
        // クエリビルダの初期化
        $query = Product::query();
    
        // キーワードで検索
        if ($request->filled('keyword')) {
            $query->where('product_name', 'LIKE', "%{$request->keyword}%");
        }
    
        // 会社名で検索
        if ($request->filled('company')) {
            $query->join('companies', 'products.company_id', '=', 'companies.id')
                  ->where('companies.company_name', $request->company);
        }
    
        // クエリを実行して結果を取得
        $products = $query->get();
    
        // 全ての会社情報を取得
        $companies = Company::all();
    
        return view('products.index')->with([
            'companies' => $companies,
            'products' => $products,
        ]);
    }
    
    

    
       
          
    
}


     
     



//もし検索キーワードで検索したら検索キーワードのみ表示 company_name実行でcompany_nameのみ表示
//$request->keywordがnullじゃない場合は検索キーワードは実行する
//もしcompanyがnull







