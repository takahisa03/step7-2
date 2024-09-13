<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    //companiesテーブルとの関連付け（リレーション）
    public function company(){
        return $this->belongsTo('App\Models\Company');
    }


    //これいらんかも、いや必要でした。
    protected $fillable = [
        'product_name',
        'price',
        'company_id',
        'stock',
        'comment',
    ];
    //登録処理
    // テーブルの中身の変更or登録の処理のみモデルに書く
    public function createProduct($request, $img_path){
        
        DB::table('products')->insert([
           'product_name' => $request->name,
           'price' => $request->price,
           'stock' => $request->stock,
           'comment' => $request->comment,
           'company_id' => $request->company_id,
           'img_path' => $img_path,


        ]);
    }

    //更新処理
    // テーブルの中身の変更or登録の処理のみモデルに書く
    public function updateProduct($product, $request, $img_path, $id) {
        $product->product_name = $request->name;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->comment = $request->comment;
        $product->img_path = $img_path;

    
        $product->save(); // データベースに保存
    }
    
    
    
    

    //削除処理
    public function deleteProduct($product, $id){

        $product->destroy($id);

    }
}
