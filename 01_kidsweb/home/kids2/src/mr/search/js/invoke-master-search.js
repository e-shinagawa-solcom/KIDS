
(function(){

    // マスタ検索共通
    var searchMaster = {
                    url: '/mold/lib/queryMasterData.php?strSessionID=' + $.cookie('strSessionID'),
                    type: 'post',
                    dataType: 'json'
                };

    // --------------------------------------------------------------------------
    // イベント登録
    // --------------------------------------------------------------------------
    // ヘッダタブ 製品コード イベント登録
    $('input.mold-product-code').on({
        'change': function(){
            var revisecode = $('input[name="ReviseCode"]').val();
            // 金型リスト索引
            selectMoldSelectionList($(this), revisecode);
        }
    });
    // ヘッダタブ 再販コード イベント登録
    $('input[name="ReviseCode"]').on({
        'change': function () {
            var revisecode = $(this).val();
            var productcode = $('input.mold-product-code');
            if (productcode.val() != "") {
                // 金型リスト索引
                selectMoldSelectionList(productcode, revisecode);
            }
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
    // 登録者-表示ユーザコード イベント登録
    $('input[name="CreateBy"]').on({
        'change': function(){
            // 表示名を索引
            selectCreateUserName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスをユーザ名に合わせる
            $('input[name="CreateByName"]').focus();
        }
    });
    // 更新者-表示ユーザコード イベント登録
    $('input[name="UpdateBy"]').on({
        'change': function(){
            // 表示名を索引
            selectUpdateUserName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスをユーザ名に合わせる
            $('input[name="UpdateByName"]').focus();
        }
    });
    
    // 製品コードから金型リストを索引
    var selectMoldSelectionList = function (invoker, revisecode) {
        console.log("製品コード->金型リスト change");
        var queryname = 'selectMoldByProductcode';
        var conditions = {
            ProductCode: $(invoker).val()
        };
        if (revisecode != "") {
            queryname = 'selectMoldByCode';
            conditions = {
                ProductCode: $(invoker).val(),
                ReviseCode: revisecode
            };
        }
        // 検索条件
        var condition = {
            data: {
                QueryName: queryname,
                Conditions: conditions
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

            // 索引件数分走査
            $.each(response, function(index, row){
                // OPTION要素作成
                moldList.append(
                    $('<option>')
                        .val(row.moldno)
                        .html(row.moldno)
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
            $(invoker).val('');
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
            $(invoker).val('');
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
            $(invoker).val('');
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

    // --------------------------------------------------------------------------
    // 登録者-表示ユーザコードによるデータ索引
    // --------------------------------------------------------------------------
    // 登録者-表示ユーザコードから表示名を索引
    var selectCreateUserName = function(invoker){
        console.log("登録者-表示ユーザコード->表示名 change");

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
            console.log("登録者-表示ユーザコード->表示名 done");
            // 登録者-表示名に値をセット
            $('input[name="CreateByName"]').val(response[0].userdisplayname);
        })
        .fail(function(response){
            console.log("登録者-表示ユーザコード->表示名 fail");
            console.log(response.responseText);
            // 登録者-表示名の値をリセット
            $(invoker).val('');
            $('input[name="CreateByName"]').val('');
        });
    };

    // --------------------------------------------------------------------------
    // 更新者-表示ユーザコードによるデータ索引
    // --------------------------------------------------------------------------
    // 更新者-表示ユーザコードから表示名を索引
    var selectUpdateUserName = function(invoker){
        console.log("更新者-表示ユーザコード->表示名 change");

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
            console.log("更新者-表示ユーザコード->表示名 done");
            // 登録者-表示名に値をセット
            $('input[name="UpdateByName"]').val(response[0].userdisplayname);
        })
        .fail(function(response){
            console.log("更新者-表示ユーザコード->表示名 fail");
            console.log(response.responseText);
            // 登録者-表示名の値をリセット
            $(invoker).val('');
            $('input[name="UpdateByName"]').val('');
        });
    };
})();
