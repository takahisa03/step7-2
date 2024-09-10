<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
</head>
<body>
    <h1>商品一覧画面</h1>

    <!-- ログアウトボタン -->
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="logout-button">ログアウト</button>
    </form>
    

    <form action="{{ route('search') }}" method="get">
    <input type="text" name="keyword" placeholder="検索キーワード" value="{{ request()->keyword }}">
    <select name="company">
        <option value="">全ての会社</option>
        @foreach($companies as $company)
            <option value="{{ $company->company_name }}" 
                {{ request()->company == $company->company_name ? 'selected' : '' }}>
                {{ $company->company_name }}
            </option>
        @endforeach
    </select>
    <button type="submit">検索</button>
</form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>商品画像</th>
                <th>商品名</th>
                <th>価格</th>
                <th>在庫数</th>
                <th>メーカー名</th>
                <th><a href="{{ route('show.create') }}">新規登録</a></th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td><img src="{{ asset($product->img_path) }}" width="100px" height="100px" alt="商品画像です"></td>
                <td>{{ $product->product_name }}</td>
                <td>¥{{ $product->price }}</td>
                <td>{{ $product->stock }}</td>
                <td>{{ $product->company->company_name}}</td>
                <td class="actions">
                <a href="{{ route('show',$product->id) }}">詳細</a>
                <form method="post" action="{{ route('delete',$product->id) }}">

                    @csrf
                    <button type="submit">削除</button>
                    

                </form>
                
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
</html>
