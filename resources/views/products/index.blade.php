<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>商品一覧画面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .actions button {
            margin-right: 5px;
        }

        /* ログアウトボタンを画面右下に配置 */
        .logout-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .logout-button:hover {
            background-color: #d32f2f;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>
<body>
    <h1>商品一覧画面</h1>

    <!-- ログアウトボタン -->
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="logout-button">ログアウト</button>
    </form>

<!-- 検索フォーム -->
<form action="{{ route('search') }}" method="get" id="search_form">
<form id="search_form" onsubmit="event.preventDefault(); nameSearch();">

    <!-- キーワード検索 -->
    <input type="text" id="name_search" name="keyword" placeholder="検索キーワード" value="{{ request()->keyword }}">
    
    
    
    <!-- 価格範囲 -->
    <label for="price_min">価格 (下限)</label>
    <input type="number" id="price_min" name="price_min" placeholder="最低価格" value="{{ request()->price_min }}">
    
    <label for="price_max">価格 (上限)</label>
    <input type="number" id="price_max" name="price_max" placeholder="最高価格" value="{{ request()->price_max }}">
    
    <!-- 在庫数範囲 -->
    <label for="stock_min">在庫数 (下限)</label>
    <input type="number" id="stock_min" name="stock_min" placeholder="最低在庫数" value="{{ request()->stock_min }}">
    
    <label for="stock_max">在庫数 (上限)</label>
    <input type="number" id="stock_max" name="stock_max" placeholder="最高在庫数" value="{{ request()->stock_max }}">
    
    <!-- 会社名検索 -->
    <select name="company" id="company_search">
        <option value="">全ての会社</option>
        @foreach($companies as $company)
            <option value="{{ $company->company_name }}" 
                {{ request()->company == $company->company_name ? 'selected' : '' }}>
                {{ $company->company_name }}
            </option>
        @endforeach
    </select>
    
</form>


<table>
    <thead>
        <tr>
            <th data-sort="id" data-order="desc">ID</th>
            <th>商品画像</th>
            <th data-sort="product_name" data-order="asc">商品名</th>
            <th data-sort="price" data-order="asc">価格</th>
            <th data-sort="stock" data-order="asc">在庫数</th>
            <th data-sort="company_name" data-order="asc">メーカー名</th>
            <th><a href="{{ route('show.create') }}">新規登録</a></th>
        </tr>
    </thead>
    <tbody id="content">
        @foreach($products as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td><img src="{{ asset($product->img_path) }}" width="100px" height="100px" alt="商品画像です"></td>
            <td>{{ $product->product_name }}</td>
            <td>¥{{ $product->price }}</td>
            <td>{{ $product->stock }}</td>
            <td>{{ $product->company->company_name }}</td>
            <td class="actions">
            <a href="{{ route('show', $product->id) }}">詳細</a>
            <button type="button" class="delete-button" data-id="{{ $product->id }}">削除</button>
            <button type="button" class="purchase-button" data-id="{{ $product->id }}" data-price="{{ $product->price }}">購入</button>
            </td>

        </tr>
        @endforeach
    </tbody>
</table>


    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
