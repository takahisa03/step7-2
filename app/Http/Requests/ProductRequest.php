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
        return [
            
            'name' => 'required|max:25',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 画像バリデーション
            'company_id' => 'required', // 会社選択のバリデーションを追加

        ];
        // 新規登録時は画像を必須にする
    if ($this->isMethod('post')) {
        $rules['image'] = 'required|' . $rules['image'];
    }
    return $rules;

    }
    public function messages() {
        return [
            'name.required' => '商品名を入力して下さい。',
            'name.max' => '25文字以下で入力して下さい。',
            'price.required' => '商品価格を入力して下さい。',
            'price.numeric' => '数字で入力して下さい。',
            'stock.required' => '在庫集を入力して下さい。',
            'stock.numeric' => '数字で入力して下さい。',
            'image.image' => 'アップロードできるファイルは画像のみです。',
            'image.mimes' => '画像はjpeg, png, jpg, gif形式でアップロードしてください。',
            'image.max' => '画像のサイズは2MB以下にしてください。',
            'company_id.required' => '会社を選択して下さい。', // 会社選択のエラーメッセージ

        ];
    }
}