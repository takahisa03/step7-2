$(document).ready(function() {
    var sortColumn = 'id';
    var sortOrder = 'asc';

    // ソート用クリックイベント
    $('th[data-sort]').on('click', function() {
        sortColumn = $(this).data('sort') || sortColumn;
        sortOrder = $(this).data('order') === 'asc' ? 'desc' : 'asc';
        $(this).data('order', sortOrder);
        nameSearch(); // ソート後に検索処理
    });

    // 検索処理
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
            cache: false, // キャッシュを無効化
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
                    $row.append($('<td>').text(companyName));
                    $row.append($('<td>', { class: 'actions' }).append(
                        $('<a>', { href: `/show/${product.id}` }).text('詳細'),
                        $('<button>', { class: 'delete-button', 'data-id': product.id }).text('削除'),
                        $('<button>', { class: 'purchase-button', 'data-id': product.id, 'data-price': product.price }).text('購入')
                    ));
                    $('#content').append($row);
                });
                // 購入ボタンイベントを再設定
                attachPurchaseButtonEvent();
            }
        });
    }

    // 購入ボタンのクリックイベント設定
    function attachPurchaseButtonEvent() {
        $(document).off('click', '.purchase-button').on('click', '.purchase-button', function() {
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
                    if (response.success) {
                        alert(response.success);
                        // 在庫数をレスポンスの在庫数で更新
                        $(`#row-${productId} td:nth-child(5)`).text(response.stock);
                    } else {
                        window.location.reload(); // リロードで最新情報を取得
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON ? xhr.responseJSON.error : 'エラーが発生しました');
                }
            });
        });
    }

    // 削除ボタンのクリックイベント
    $(document).on('click', '.delete-button', function() {
        const productId = $(this).data('id');
        if (!confirm('この商品を削除しますか？')) return;

        $.ajax({
            url: `/delete/${productId}`,
            type: 'POST',
            data: {
                _method: 'DELETE',
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert(response.success);
                // 商品が削除された後にテーブルを再描画
                nameSearch();  // 再度検索処理を呼び出し
            },
            error: function(xhr) {
                alert(xhr.responseJSON.error || '削除に失敗しました');
            }
        });
    });

    // 入力フィールドやドロップダウンでの検索
    $('#name_search, #price_min, #price_max, #stock_min, #stock_max').on('input', nameSearch);
    $('#company_search').on('change', nameSearch);

    // 初回の購入ボタンイベント設定と在庫データ取得
    attachPurchaseButtonEvent();
    nameSearch();

    // CSRFトークンの設定
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});
