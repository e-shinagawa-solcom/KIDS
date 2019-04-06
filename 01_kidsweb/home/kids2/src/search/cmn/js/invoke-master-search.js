
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
    // 登録者-表示ユーザコード イベント登録
    $('input[name="lngInputUserCode"]').on({
        'change': function(){
            // 表示名を索引
            selectCreateUserName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスをユーザ名に合わせる
            $('input[name="lngInputUserName"]').focus();
        }
    });
    // 担当グループ-表示グループコード イベント登録
    $('input[name="lngInChargeGroupCode"]').on({
        'change': function(){
            // 表示名を索引
            selectGroupName($(this));
            // グループコードが空の場合ユーザーネームを初期化
            if( !$('input[name="lngInChargeGroupCode"]').val() ){
                $('input[name="lngInChargeUserCode"]').val('').change();
            }

            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスをグループ名に合わせる
            $('input[name="strInChargeGroupName"]').focus();
        }
    });
    // 担当者-表示ユーザコード イベント登録
    $('input[name="lngInChargeUserCode"]').on({
        'change': function(){
            // 表示名を索引
            selectUserName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスをユーザ名に合わせる
            $('input[name="strInChargeUserName"]').focus();
        }
    });
    // 事業部(顧客)-表示会社コード イベント登録
    $('input[name="lngCustomerCode"]').on({
        'change': function(){
            // 表示名を索引
            selectCustomerName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを事業部名に合わせる
            $('input[name="strCustomerName"]').focus();
        }
    });
    // 仕入科目- イベント登録
    $('select[name="lngStockSubjectCode"]').on({
        'change': function(){
            var TargetPull = $('select[name="lngStockItemCode"]')[0];
            var options = TargetPull.options;
            if (TargetPull.hasChildNodes()) {
                while (TargetPull.childNodes.length > 0) {
                    TargetPull.removeChild(TargetPull.firstChild)
                }
            }
            var ItemCodeValue = $('input[name="lngStockItemCodeValue"]')[0].value.split(',,');
            var ItemCodeDisp = $('input[name="lngStockItemCodeDisp"]')[0].value.split(',,');
            var ChangePullValue = $('select[name="lngStockSubjectCode"]')[0].value;
            for (var i = 0; i < ItemCodeValue.length;i++){
                if (ChangePullValue == ItemCodeValue[i].slice(0,ChangePullValue.length)){
                    let op = document.createElement("option");
                    op.value = ItemCodeValue[i];
                    op.text = ItemCodeDisp[i];
                    TargetPull.appendChild(op);
                }
            }
        }
    });

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
            $('input[name="lngInputUserName"]').val(response[0].userdisplayname);
        })
        .fail(function(response){
            console.log("登録者-表示ユーザコード->表示名 fail");
            console.log(response.responseText);
            // 登録者-表示名の値をリセット
            $(invoker).val('');
            $('input[name="lngInputUserName"]').val('');
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
            $('input[name="strInChargeGroupName"]').val(response[0].groupdisplayname);
        })
        .fail(function(response){
            console.log("担当グループ-表示グループコード->表示名 fail");
            console.log(response.responseText);
            // 事業部(顧客)-表示名の値をリセット
            $(invoker).val('');
            $('input[name="strInChargeGroupName"]').val('');
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
            $('input[name="strInChargeUserName"]').val(response[0].userdisplayname);
        })
        .fail(function(response){
            console.log("担当者-表示ユーザコード->表示名 fail");
            console.log(response.responseText);
            // 担当者-表示名の値をリセット
            $(invoker).val('');
            $('input[name="strInChargeUserName"]').val('');
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
            $('input[name="strCustomerName"]').val(response[0].companydisplayname);
        })
        .fail(function(response){
            console.log("事業部(顧客)-表示会社コード->表示名 fail");
            console.log(response.responseText);
            // 事業部(顧客)-表示名の値をリセット
            $(invoker).val('');
            $('input[name="strCustomerName"]').val('');
        });
    };
})();
