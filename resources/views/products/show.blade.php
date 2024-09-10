<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品情報詳細画面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .product-detail {
            border: 1px solid #ddd;
            padding: 20px;
            width: 50%;
            margin: 0 auto;
        }
        .product-detail div {
            margin-bottom: 10px;
        }
        .product-detail label {
            display: inline-block;
            width: 100px;
        }
        .actions button {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <h1>商品情報詳細画面</h1>
    <div class="product-detail">
        <div>
            <label><strong>ID</strong></label>
            <span>{{ $product->id }}</span>
        </div>
        <div>
            <label><strong>商品画像</strong></label>
            <!-- 画像の表示 -->
            <img src="{{ asset($product->img_path) }}" alt="商品画像" width="200px" height="200px">
        </div>
        <div>
            <label><strong>商品名</strong></label>
            <span>{{ $product->product_name }}</span>
        </div>
        <div>
            <label><strong>メーカー</strong></label>
            <span>{{ $product->company_id }}</span>
        </div>
        <div>
            <label><strong>価格</strong></label>
            <span>¥{{ $product->price }}</span>
        </div>
        <div>
            <label><strong>在庫数</strong></label>
            <span>{{ $product->stock }}</span>
        </div>
        <div>
            <label><strong>コメント</strong></label>
            <textarea readonly>{{ $product->comment }}</textarea>
        </div>
        <div class="actions">
        <a href="{{ route('show.edit', $product->id) }}"><button>編集</button></a>
            <a href="{{ route('products.index') }}"><button>戻る</button></a>
        </div>
    </div>
</body>
</html>
