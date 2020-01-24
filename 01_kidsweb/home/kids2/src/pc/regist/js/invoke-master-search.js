
(function(){

    // マスタ検索共通
    var searchMaster = {
                    url: '/mold/lib/queryMasterData.php?strSessionID=' + $.cookie('strSessionID'),
                    type: 'post',
                    dataType: 'json'
                };

    
    // 仕入先-表示会社コード イベント登録
    $('input[name="lngCustomerCode"]').on({
        'change': function(){
            // 表示名を索引
            selectCustomerName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを表示会社名に合わせる
            $(this).next('input').focus();
        }
    });

    // 納品工場-表示会社コード イベント登録
    $('input[name="lngLocationCode"]').on({
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
    // 仕入先-表示会社コードによるデータ索引
    // --------------------------------------------------------------------------
    // 仕入先-表示会社コードから表示名を索引
    var selectCustomerName = function(invoker){
        console.log("仕入先-表示会社コード->表示名 change");

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
            console.log("仕入先-表示会社コード->表示名 done");
            // 仕入先-表示名に値をセット
            $('input[name="strCustomerName"]').val(response[0].companydisplayname);
        })
        .fail(function(response){
            console.log("仕入先-表示会社コード->表示名 fail");
            console.log(response.responseText);
            // 仕入先-表示名の値をリセット
            $('input[name="strCustomerName"]').val('');
        });
    };

    // --------------------------------------------------------------------------
    // 工場-表示会社コードによるデータ索引
    // --------------------------------------------------------------------------
    // 工場-表示会社コードから表示名を索引
    var selectFactoryName =  function(invoker){
        console.log("工場-表示会社コード->表示名 change");

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
            $('input[name="strLocationName"]').val(response[0].companydisplayname);
        })
        .fail(function(response){
            console.log("工場-表示会社コード->表示名 fail");
            console.log(response.responseText);
            // 仕入先-表示名の値をリセット
            $('input[name="strLocationName"]').val('');
        });
    };

})();
