$(document).ready(function() {
    // 初期ソート条件の設定（未定義エラーの防止）
    var sortColumn = 'id';
    var sortOrder = 'asc'; // 初期は昇順に設定

    // ソート用クリックイベント
    $('th[data-sort]').on('click', function() {
        sortColumn = $(this).data('sort') || sortColumn;
        sortOrder = $(this).data('order') === 'asc' ? 'desc' : 'asc';
        $(this).data('order', sortOrder);
        nameSearch();
    });

    function nameSearch() {
        var dataVal = {
            'name_search': $('#name_search').val(),
            'company_search': $('#company_search').val(),
            'price_min': $('#price_min').val(),
            'price_max': $('#price_max').val(),
            'stock_min': $('#stock_min').val(),
            'stock_max': $('#stock_max').val(),
            'sort_column': sortColumn,
            'sort_order': sortOrder
        };

        $.ajax({
            type: 'GET',
            url: '/search',
            data: dataVal,
            dataType: 'json',
            success: function(data) {
                $('#content').empty();
                data.forEach(function(product) {
                    var companyName = product.company_name || (product.company ? product.company.company_name : '不明');

                    var $row = $('<tr>', { id: `row-${product.id}`, 'data-id': product.id });
                    $row.append($('<td>').text(product.id));
                    $row.append($('<td>').append($('<img>', { src: product.img_path, width: 100, height: 100, alt: '商品画像です' })));
                    $row.append($('<td>').text(product.product_name));
                    $row.append($('<td>').text(`¥${product.price}`));
                    $row.append($('<td>').text(product.stock));
                    $row.append($('<td>').text(companyName)); // 会社名の表示を修正
                    $row.append($('<td>', { class: 'actions' }).append(
                        $('<a>', { href: `/show/${product.id}` }).text('詳細'),
                        $('<button>', { class: 'delete-button', 'data-id': product.id }).text('削除'),
                        $('<button>', { class: 'purchase-button', 'data-id': product.id, 'data-price': product.price }).text('購入')
                    ));
                    $('#content').append($row);
                });
            }
        });
    }


    $(document).ready(function() {
        // 削除ボタンのクリックイベント
        $(document).on('click', '.delete-button', function() {
            const productId = $(this).data('id');
            if (!confirm('この商品を削除しますか？')) return;
    
            $.ajax({
                url: `/delete/${productId}`,  // ルートに応じて修正が必要か確認
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert(response.success);
                    $(`#row-${productId}`).remove();  // 成功時に行を削除
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.error || '削除に失敗しました');
                }
            });
        });
    
       
    });
    

    // 購入ボタンのイベントリスナーを設定
    $(document).on('click', '.purchase-button', function() {
        const productId = $(this).data('id');
        const quantity = 1;

        $.ajax({
            url: '/api/purchase',
            type: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert(response.success);
                // 購入成功後、在庫数を更新
                $(`#row-${productId} td:nth-child(5)`).text(function(i, stock) {
                    return stock - quantity;
                });
            },
            error: function(xhr) {
                alert(xhr.responseJSON.error);
            }
        });
    });

    // 入力フィールドやドロップダウンでの検索
    $('#name_search, #price_min, #price_max, #stock_min, #stock_max').on('input', nameSearch);
    $('#company_search').on('change', nameSearch);
}); // ここで $(document).ready を閉じます
