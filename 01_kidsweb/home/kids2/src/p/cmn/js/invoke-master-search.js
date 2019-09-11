
(function () {

    // マスタ検索共通
    var searchMaster = {
        url: '/mold/lib/queryMasterData.php?strSessionID=' + $.cookie('strSessionID'),
        type: 'post',
        dataType: 'json'
    };

    // 顧客-表示会社コード イベント登録
    $('input[name="lngCustomerCompanyCode"]').on({
        'change': function () {
            // 表示名を索引
            selectCustomerName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを生産工場名に合わせる
            $('input[name="strCustomerCompanyName"]').focus();
        }
    });

    // 顧客担当者-表示ユーザーコード イベント登録
    $('input[name="lngCustomerUserCode"]').on({
        'change': function () {
            // 表示名を索引
            selectUserName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを生産工場名に合わせる
            $('input[name="strCustomerUserName"]').focus();
        }
    });

    // 入力者-表示ユーザーコード イベント登録
    $('input[name="lngInputUserCode"]').on({
        'change': function () {
            // 表示名を索引
            selectUserName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを生産工場名に合わせる
            $('input[name="strInputUserName"]').focus();
        }
    });

    // 起票者-表示ユーザーコード イベント登録
    $('input[name="lngInsertUserCode"]').on({
        'change': function () {
            // 表示名を索引
            selectUserName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを生産工場名に合わせる
            $('input[name="strInsertUserName"]').focus();
        }
    });

    // 開発担当者-表示ユーザーコード イベント登録
    $('input[name="lngDevelopUsercode"]').on({
        'change': function () {
            // 表示名を索引
            selectUserName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを生産工場名に合わせる
            $('input[name="strDevelopUserName"]').focus();
        }
    });

    // 営業部署-表示ユーザーコード イベント登録
    $('input[name="lngInChargeGroupCode"]').on({
        'change': function () {
            // 表示名を索引
            selectGroupName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを生産工場名に合わせる
            $('input[name="strInChargeGroupName"]').focus();
        }
    });

    // 担当者-表示ユーザーコード イベント登録
    $('input[name="lngInChargeUserCode"]').on({
        'change': function () {
            // 表示名を索引
            selectUserName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを生産工場名に合わせる
            $('input[name="strInChargeUserName"]').focus();
        }
    });

    // 生産工場-表示会社コード イベント登録
    $('input[name="lngFactoryCode"]').on({
        'change': function () {
            // 表示名を索引
            selectFactoryName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを生産工場名に合わせる
            $('input[name="strFactoryName"]').focus();
        }
    });

    // アッセンブリ工場-表示会社コード イベント登録
    $('input[name="lngAssemblyFactoryCode"]').on({
        'change': function () {
            // 表示名を索引
            selectFactoryName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを生産工場名に合わせる
            $('input[name="strAssemblyFactoryName"]').focus();
        }
    });

    // 納品場所-表示会社コード イベント登録
    $('input[name="lngDeliveryPlaceCode"]').on({
        'change': function () {
            // 表示名を索引
            selectFactoryName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを生産工場名に合わせる
            $('input[name="strDeliveryPlaceName"]').focus();
        }
    });

    // --------------------------------------------------------------------------
    // 工場-表示会社コードによるデータ索引
    // --------------------------------------------------------------------------
    // 工場-表示会社コードから表示名を索引
    var selectFactoryName = function (invoker) {
        console.log("工場-表示会社コード->表示名 change value=" + $(invoker).val());
        // 索引結果のセット先CSSセレクタの作成
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // 索引結果0件の時のコード欄のCSSセレクタの作成
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';
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
            .done(function (response) {
                console.log("工場-表示会社コード->表示名 done");
                // 工場-表示名に値をセット
                $(targetCssSelector).val(response[0].companydisplayname);
            })
            .fail(function (response) {
                console.log("工場-表示会社コード->表示名 fail");
                console.log(response.responseText);
                // 工場-コード、表示名の値をリセットし、コード欄にフォーカス
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };

    // --------------------------------------------------------------------------
    // 顧客-表示会社コードによるデータ索引
    // --------------------------------------------------------------------------
    // 顧客-表示会社コードから表示名を索引
    var selectCustomerName = function (invoker) {
        console.log("顧客-表示会社コード->表示名 change");
        // 索引結果のセット先CSSセレクタの作成
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // 索引結果0件の時のコード欄のCSSセレクタの作成
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';
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
            .done(function (response) {
                console.log("工場-表示会社コード->表示名 done");
                // 工場-表示名に値をセット
                $(targetCssSelector).val(response[0].companydisplayname);
            })
            .fail(function (response) {
                console.log("工場-表示会社コード->表示名 fail");
                console.log(response.responseText);
                // 工場-コード、表示名の値をリセットし、コード欄にフォーカス
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };
    // --------------------------------------------------------------------------
    // 顧客担当者-表示ユーザコードによるデータ索引
    // --------------------------------------------------------------------------
    // 顧客担当者-表示ユーザコードから表示名を索引
    var selectUserName = function (invoker) {
        console.log("担当者-表示ユーザコード->表示名 change");// 索引結果のセット先CSSセレクタの作成
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // 索引結果0件の時のコード欄のCSSセレクタの作成
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';
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
            .done(function (response) {
                console.log("担当者-表示ユーザコード->表示名 done");
                // 担当者-表示名に値をセット
                $(targetCssSelector).val(response[0].userdisplayname);
            })
            .fail(function (response) {
                console.log("担当者-表示ユーザコード->表示名 fail");
                console.log(response.responseText);
                // 担当者-表示名の値をリセット
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };

    

    // --------------------------------------------------------------------------
    // 担当グループ-表示グループコードによるデータ索引
    // --------------------------------------------------------------------------
    // 担当グループ-表示グループコードから表示名を索引
    var selectGroupName = function(invoker){
        console.log("担当グループ-表示グループコード->表示名 change");
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // 索引結果0件の時のコード欄のCSSセレクタの作成
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';

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
            $(targetCssSelector).val(response[0].groupdisplayname);
        })
        .fail(function(response){
            console.log("担当グループ-表示グループコード->表示名 fail");
            console.log(response.responseText);
            $(targetCssSelector).val('');
            $(targetCodeCssSelector).val('').focus();
        });
    };
})();
