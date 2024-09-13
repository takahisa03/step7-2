<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        // 基本ルールの定義
        $rules = [
            'name' => 'required|max:25',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',  // 画像のバリデーション
            'company_id' => 'required',  // 会社選択のバリデーション
            'comment' => 'nullable|max:255', // 追加
        ];

      

        return $rules;
    }

    /**
     * カスタムエラーメッセージ
     *
     * @return array<string, mixed>
     */
    
    public function messages()
    {
        return [
            'name.required' => '商品名を入力して下さい。',
            'name.max' => '商品名は25文字以下で入力して下さい。',
            'price.required' => '商品価格を入力して下さい。',
            'price.numeric' => '商品価格は数字で入力して下さい。',
            'stock.required' => '在庫数を入力して下さい。',
            'stock.numeric' => '在庫数は数字で入力して下さい。',
            'image.image' => 'アップロードできるファイルは画像のみです。',
            'image.mimes' => '画像はjpeg, png, jpg, gif形式でアップロードしてください。',
            'image.max' => '画像のサイズは2MB以下にしてください。',
            'company_id.required' => '会社を選択して下さい。',
        ];
    }
}
