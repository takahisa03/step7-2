<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品情報編集画面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .product-edit {
            border: 1px solid #ddd;
            padding: 20px;
            width: 50%;
            margin: 0 auto;
        }
        .product-edit div {
            margin-bottom: 10px;
        }
        .product-edit label {
            display: inline-block;
            width: 100px;
        }
        .actions button {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <h1>商品情報編集画面</h1>
    <form action="{{ route('products.update', $product->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    
    
    <div>
        <label><strong>ID</strong></label>
        <span>{{ $product->id }}</span>
    </div>
    <div>
        <label>商品名 <span style="color: red;">*</span></label>
        <input type="text" name="name" value="{{ $product->product_name }}">
    </div>
    @error('name')
        <div class="error">{{ $message }}</div>
    @enderror

    <div>
        <label>価格 <span style="color: red;">*</span></label>
        <input type="number" name="price" value="{{ $product->price }}">
    </div>
    @error('price')
        <div class="error">{{ $message }}</div>
    @enderror

    <div>
        <label>在庫数 <span style="color: red;">*</span></label>
        <input type="number" name="stock" value="{{ $product->stock }}">
    </div>
    @error('stock')
        <div class="error">{{ $message }}</div>
    @enderror

    <div>
        <label>コメント</label>
        <textarea name="comment">{{ $product->comment }}</textarea>
    </div>

    <!-- 現在の画像表示 -->
    <div>
        <label>現在の画像</label>
        @if($product->img_path)
            <img src="{{ asset($product->img_path) }}" alt="商品画像" style="max-width: 200px;">
        @else
            <p>画像はありません</p>
        @endif
    </div>

    <!-- 新しい画像アップロード -->
    <div>
        <label for="image">新しい商品画像</label>
        <input type="file" id="image" name="image">
        @error('image')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <div class="actions">
        <button type="submit">更新</button>
        <a href="{{ route('products.index') }}"><button type="button">戻る</button></a>
    </div>
</form>

</body>
</html>
</html>
