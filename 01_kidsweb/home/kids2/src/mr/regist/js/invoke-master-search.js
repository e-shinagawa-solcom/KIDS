
(function(){

    // マスタ検索共通
    var searchMaster = {
                    url: '/mold/lib/queryMasterData.php?strSessionID=' + $.cookie('strSessionID'),
                    type: 'post',
                    dataType: 'json'
                };

    // 更新クエリ共通
    var updateQuery = {
                    url: '/mold/lib/execUpdateQuery.php?strSessionID=' + $.cookie('strSessionID'),
                    type: 'post',
                    dataType: 'json'
                };

    // --------------------------------------------------------------------------
    // イベント登録
    // --------------------------------------------------------------------------
    // ヘッダタブ 製品コード イベント登録
    $('input[name="ProductCode"]').on({
        'change': function(){
            // 製品名称索引
            selectProductName($(this));
            // 顧客品番索引
            selectGoodsCode($(this));
            // 事業部(顧客)-表示会社コード索引
            selectCustomerByProductCode($(this));
            // 担当グループ-表示グループコード索引
            selectGroupByProductCode($(this));
            // 担当者-表示ユーザコード索引
            selectUserByProductCode($(this));
            // 金型リスト索引
            selectMoldSelectionList($(this));
        }
    });
    // 事業部(顧客)-表示会社コード イベント登録
    $('input[name="CustomerCode"]').on({
        'change': function(){
            // 表示名を索引
            selectCustomerName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを事業部名に合わせる
            $('input[name="CustomerName"]').focus();
        }
    });
    // 担当グループ-表示グループコード イベント登録
    $('input[name="KuwagataGroupCode"]').on({
        'change': function(){
            // 表示名を索引
            selectGroupName($(this));
            // グループコードが空の場合ユーザーネームを初期化
            if( !$('input[name="KuwagataGroupCode"]').val() ){
                $('input[name="KuwagataUserCode"]').val('').change();
            }

            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスをグループ名に合わせる
            $('input[name="KuwagataGroupName"]').focus();
        }
    });
    // 担当者-表示ユーザコード イベント登録
    $('input[name="KuwagataUserCode"]').on({
        'change': function(){
            // 表示名を索引
            selectUserName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスをユーザ名に合わせる
            $('input[name="KuwagataUserName"]').focus();
        }
    });
    // 保管元工場/移動先工場-表示会社コード イベント登録
    $('input[name="SourceFactory"], input[name="DestinationFactory"]').on({
        'change': function(){
            // 表示名を索引
            selectFactoryName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを表示会社名に合わせる
            $(this).next('input').focus();
        }
    });

    // --------------------------------------------------------------------------
    // 製品コードによるデータ索引
    // --------------------------------------------------------------------------
    // 製品コードから製品名称を検索
    var selectProductName = function(invoker){
        console.log("製品コード->製品名称 change");

        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectProductName',
                Conditions: {
                    ProductCode: invoker.val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("製品コード->製品名称 done");
            // ヘッダタブ/詳細タブの製品コード及び製品名称に値をセット
            $('input[name="ProductCode"]').val(invoker.val());
            $('input[name="ProductName"]').val(response[0].productname);

            // JQuery Validation Pluginで検知させる為イベントキック
            $('input[name="ProductCode"]').trigger('blur');
            $('input[name="ProductName"]').trigger('blur');
        })
        .fail(function(response){
            console.log("製品コード->製品名称 fail");
            console.log(response.responseText);
            // ヘッダタブ/詳細タブの製品コード及び製品名称の値をリセット
            $('input[name="ProductCode"]').val('');
            $('input[name="ProductName"]').val('');

            // JQuery Validation Pluginで検知させる為イベントキック
            $('input[name="ProductCode"]').trigger('blur');
            $('input[name="ProductName"]').trigger('blur');
        });
    };

    // 製品コードから顧客品番を索引
    var selectGoodsCode = function(invoker){
        console.log("製品コード->顧客品番 change");

        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectGoodsCode',
                Conditions: {
                    ProductCode: $(invoker).val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("製品コード->顧客品番 done");

            var goodscode = response[0].goodscode;
            // 顧客品番が設定されている場合
            if (goodscode) {
                // ヘッダタブ/詳細タブの顧客品番に値をセット
                $('input[name="GoodsCode"]').val(goodscode);
                // JQuery Validation Pluginで検知させる為イベントキック
                $('input[name="GoodsCode"]').trigger('blur');
            }
            else {
                // 入力ダイアログ表示
                var newgoodscode = window.prompt('顧客品番を入力してください。(半角英数のみ)', '');

                // キャンセル押下チェック
                if (!newgoodscode)
                {
                    // メッセージ出力
                    window.alert('製品コードに紐付く顧客品番は必須項目です。');
                    return;
                }

                // 入力チェック
                if (!newgoodscode.match(/^[A-Za-z0-9]{1,10}$/)) {
                    window.alert('顧客品番は半角英数かつ10文字以内で入力してください。');
                    $(invoker).change();
                    return;
                }

                // 更新条件
                var condition = {
                    data: JSON.stringify({
                        QueryName: 'updateGoodsCode',
                        Conditions: {
                            ProductCode: $(invoker).val(),
                            GoodsCode: newgoodscode
                        }
                    })
                };

                // リクエスト送信
                $.ajax($.extend({}, updateQuery, condition))
                .done(function(response) {
                    window.alert('顧客品番を更新しました。');
                    $(invoker).change();
                })
                .fail(function(response) {
                    window.alert('顧客品番の更新に失敗しました。');
                    $(invoker).change();
                });
            }
        })
        .fail(function(response){
            console.log("製品コード->顧客品番 fail");
            console.log(response.responseText);
            // ヘッダタブ/詳細タブの顧客品番の値をリセット
            $('input[name="GoodsCode"]').val('');
            // JQuery Validation Pluginで検知させる為イベントキック
            $('input[name="GoodsCode"]').trigger('blur');
        });
    };

    // 製品コードから事業者(顧客)-表示会社コードを索引
    var selectCustomerByProductCode = function(invoker){
        console.log("製品コード->事業部(顧客)-表示会社コード change");

        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectCustomerByProductCode',
                Conditions: {
                    ProductCode: $(invoker).val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("製品コード->事業部(顧客)-表示会社コード done");
            // 事業部(顧客)に値をセット
            $('input[name="CustomerCode"]').val(response[0].companydisplaycode);
            // 事業部(顧客)-表示名の索引キック
            $('input[name="CustomerCode"]').trigger('change');
        })
        .fail(function(response){
            console.log("製品コード->事業部(顧客)-表示会社コード fail");
            console.log(response.responseText);
            // 事業部(顧客)をリセット
            $('input[name="CustomerCode"]').val('');
            // 事業部(顧客)-表示名の索引キック
            $('input[name="CustomerCode"]').trigger('change');
        });
    };

    // 製品コードから担当グループ-表示グループコードを索引
    var selectGroupByProductCode = function(invoker){
        console.log("製品コード->担当グループ-表示グループコード change");

        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectGroupByProductCode',
                Conditions: {
                    ProductCode: $(invoker).val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("製品コード->担当グループ-表示グループコード done");
            // 担当グループに値をセット
            $('input[name="KuwagataGroupCode"]').val(response[0].groupdisplaycode);
            // 担当グループ-表示名の索引キック
            $('input[name="KuwagataGroupCode"]').trigger('change');
        })
        .fail(function(response){
            console.log("製品コード->担当グループ-表示グループコード fail");
            console.log(response.responseText);
            // 担当グループをリセット
            $('input[name="KuwagataGroupCode"]').val('');
            // 担当グループ-表示名の索引キック
            $('input[name="KuwagataGroupCode"]').trigger('change');
        });
    };

    // 製品コードから担当者-表示ユーザコードを索引
    var selectUserByProductCode = function(invoker){
        console.log("製品コード->担当者-表示ユーザコード change");

        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectUserByProductCode',
                Conditions: {
                    ProductCode: $(invoker).val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("製品コード->担当者-表示ユーザコード done");
            // 担当者に値をセット
            $('input[name="KuwagataUserCode"]').val(response[0].userdisplaycode);
            // 担当者-表示名の索引キック
            $('input[name="KuwagataUserCode"]').trigger('change');
        })
        .fail(function(response){
            console.log("製品コード->担当者-表示ユーザコード fail");
            console.log(response.responseText);
            // 担当者をリセット
            $('input[name="KuwagataUserCode"]').val('');
            // 担当者-表示名の索引キック
            $('input[name="KuwagataUserCode"]').trigger('change');
        });
    };

    // 製品コードから金型リストを索引
    var selectMoldSelectionList = function(invoker){
        console.log("製品コード->金型リスト change");

        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectMoldSelectionList',
                Conditions: {
                    ProductCode: $(invoker).val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("製品コード->金型リスト done");

            // 金型セレクトボックスの取得
            var moldList = $('.mold-selection__list');
            var moldChoosenList = $('.mold-selection__choosen-list');

            // 既存OPTION要素の削除
            moldList.find('option').remove();
            moldChoosenList.find('option').remove();

            // 索引件数分走査
            $.each(response, function(index, row){
                // OPTION要素作成
                moldList.append(
                    $('<option>')
                        .val(row.moldno)
                        .attr('displaycode', row.companydisplaycode)
                        .html(row.moldno + ' : ' + '[' + row.companydisplaycode + ']' + ' '+ row.companydisplayname)
                );
            });
        })
        .fail(function(response){
            console.log("製品コード->金型リスト fail");
            console.log(response.responseText);

            // 金型セレクトボックスの取得
            var moldList = $('.mold-selection__list');
            var moldChoosenList = $('.mold-selection__choosen-list');

            // 既存OPTION要素の削除
            moldList.find('option').remove();
            moldChoosenList.find('option').remove();
        });
    };

    // --------------------------------------------------------------------------
    // 事業部(顧客)-表示会社コードによるデータ索引
    // --------------------------------------------------------------------------
    // 事業部(顧客)-表示会社コードから表示名を索引
    var selectCustomerName = function(invoker){
        console.log("事業部(顧客)-表示会社コード->表示名 change");

        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectCustomerName',
                Conditions: {
                    CompanyDisplayName: $(invoker).val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("事業部(顧客)-表示会社コード->表示名 done");
            // 事業部(顧客)-表示名に値をセット
            $('input[name="CustomerName"]').val(response[0].companydisplayname);
        })
        .fail(function(response){
            console.log("事業部(顧客)-表示会社コード->表示名 fail");
            console.log(response.responseText);
            // 事業部(顧客)-表示名の値をリセット
            $('input[name="CustomerName"]').val('');
        });
    };

    // --------------------------------------------------------------------------
    // 担当グループ-表示グループコードによるデータ索引
    // --------------------------------------------------------------------------
    // 担当グループ-表示グループコードから表示名を索引
    var selectGroupName = function(invoker){
        console.log("担当グループ-表示グループコード->表示名 change");

        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectGroupName',
                Conditions: {
                    GroupDisplayName: $(invoker).val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("担当グループ-表示グループコード->表示名 done");
            // 事業部(顧客)-表示名に値をセット
            $('input[name="KuwagataGroupName"]').val(response[0].groupdisplayname);
        })
        .fail(function(response){
            console.log("担当グループ-表示グループコード->表示名 fail");
            console.log(response.responseText);
            // 事業部(顧客)-表示名の値をリセット
            $('input[name="KuwagataGroupName"]').val('');
        });
    };
    // --------------------------------------------------------------------------
    // 担当者-表示ユーザコードによるデータ索引
    // --------------------------------------------------------------------------
    // 担当者-表示ユーザコードから表示名を索引
    var selectUserName = function(invoker){
        console.log("担当者-表示ユーザコード->表示名 change");

        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectUserName',
                Conditions: {
                    UserDisplayName: $(invoker).val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("担当者-表示ユーザコード->表示名 done");
            // 担当者-表示名に値をセット
            $('input[name="KuwagataUserName"]').val(response[0].userdisplayname);
        })
        .fail(function(response){
            console.log("担当者-表示ユーザコード->表示名 fail");
            console.log(response.responseText);
            // 担当者-表示名の値をリセット
            $('input[name="KuwagataUserName"]').val('');
        });
    };

    // --------------------------------------------------------------------------
    // 工場-表示会社コードによるデータ索引
    // --------------------------------------------------------------------------
    // 工場-表示会社コードから表示名を索引
    var selectFactoryName =  function(invoker){
        console.log("工場-表示会社コード->表示名 change");
        // 索引結果のセット先CSSセレクタの作成
        var targetCssSelector = 'input[name="' + $(invoker).attr('name') + 'Name"]';
        // 索引結果0件の時のコード欄のCSSセレクタの作成
        var targetCodeCssSelector = 'input[name="' + $(invoker).attr('name') +'"]';

        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectFactoryName',
                Conditions: {
                    CompanyDisplayName: $(invoker).val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("工場-表示会社コード->表示名 done");
            // 工場-表示名に値をセット
            $(targetCssSelector).val(response[0].companydisplayname);
        })
        .fail(function(response){
            console.log("工場-表示会社コード->表示名 fail");
            console.log(response.responseText);
            // 工場-コード、表示名の値をリセットし、コード欄にフォーカス
            $(targetCssSelector).val('');
            $(targetCodeCssSelector).val('').focus();
        });
    };

})();
