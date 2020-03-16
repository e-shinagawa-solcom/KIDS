
(function(){

    $('a').on('keydown', function (e) {
        e.stopPropagation();
        if (e.which == 13) {
            $(this).find('img').click();
        }
    });
    
    // マスタ検索共通
    var searchMaster = {
        url: '/cmn/querydata.php?strSessionID=' + $.cookie('strSessionID'),
        type: 'post',
                    dataType: 'json'
                };

    // --------------------------------------------------------------------------
    // イベント登録
    // --------------------------------------------------------------------------
    // 登録者-表示ユーザコード イベント登録
    $('input[name="payfCode"]').on({
        'change': function(){
            // 表示名を索引
            selectPayfByCode($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスをユーザ名に合わせる
            $('input[name="payfCode"]').focus();
        }
    });

    // --------------------------------------------------------------------------
    // 支払先情報-支払先CDによるデータ索引
    // --------------------------------------------------------------------------
    // 支払先情報-支払先CDから支払先正式名称を索引
    var selectPayfByCode = function(invoker){
        console.log("支払先情報-支払先CD->支払先正式名称 change");
        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectPayfByCode',
                Conditions: {
                    payfCode: invoker.val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("支払先情報-支払先CD->支払先正式名称 done");
            // 支払先情報-支払先正式名称に値をセット
            $('input[name="payfName"]').val(response[0].payfdisplayname);
        })
        .fail(function(response){
            console.log("支払先情報-支払先CD->支払先正式名称 fail");
            console.log(response.responseText);
            // 支払先情報-支払先正式名称の値をリセット
            $(invoker).val('');
            $('input[name="payfName"]').val('');
        });
    };
})();
