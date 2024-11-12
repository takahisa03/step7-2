<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品新規登録画面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            width: 400px;
            margin: auto;
        }
        div {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        input[type="file"] {
            display: block;
        }
        button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h1>商品新規登録画面</h1>
    <form action="{{ route('create') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="name">商品名<span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" >
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="price">価格<span class="text-danger">*</span></label>
            <input type="number" id="price" name="price" >
            @error('price')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="stock">在庫数<span class="text-danger">*</span></label>
            <input type="number" id="stock" name="stock" >
            @error('stock')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="company_id">会社<span class="text-danger">*</span></label>
            <select class="p-2 w-100 form-select" name="company_id">
                <option value="" selected disabled>選択してください</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                @endforeach
            </select>
            @error('company_id')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="comment">コメント</label>
            <textarea id="comment" name="comment"></textarea>
            @error('comment')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="image">商品画像</label>
            <input type="file" id="image" name="image">
            @error('image')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <button type="submit">新規登録</button>
            <a href="{{ route('products.index') }}"><button type="button">戻る</button></a>
        </div>
    </form>
</body>
</html>
