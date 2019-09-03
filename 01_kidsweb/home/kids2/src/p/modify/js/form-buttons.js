
(function () {
    // フォーム
    var workForm = $('form');
    // タブDIV
    var tabs = $('.tabs');
    // ヘッダタブ
    var tabHeader = $('.tabs__header');
    // 詳細タブ
    var tabDetail = $('.tabs__detail');
    // ヘッダDIV
    var divHeader = $('div.regist-tab-header');
    // 詳細DIV
    var divDetail = $('div.regist-tab-detail');
    // エラーアイコンクラス名
    var classNameErrorIcon = 'error-icon';
    // エラーアイコンリソースURL
    var urlErrorIcon = '/img/type01/cmn/seg/seg_error_mark.gif';
    // クリアボタン
    var btnClear = $('.form-buttons__clear');
    // 登録ボタン
    var btnRegist = $('.form-buttons__regist');

    // フォームサブミット抑止
    $('document').on('submit', 'form', function (e) {
        e.preventDefault();
        return false;
    });

    // 閉じた際の処理
    $(window).on('beforeunload', function () {
        $(window.opener.opener.document).find('form').submit();
    });

    // クリアボタン
    btnClear.on('click', function () {
        window.location.reload();
    });

    // 登録ボタン押下時の処理
    btnRegist.on('click', function () {
        clickRegist(this);
    });

    // 登録ボタンクリック時に呼び出すfunction
    var clickRegist = function (invoker) {

        // フォーム検証
        if (validate(invoker)) {
            // サブミット処理
            submitProduct(invoker);
        }
    }

    // 検証処理のキック
    var validate = function (invoker) {
        // 検証結果
        var result = false;

        // 両タブ内容を透明にして一旦隠す
        divHeader.css('opacity', 0.0);
        divDetail.css('opacity', 0.0);

        // ヘッダタブ検証
        tabHeader.click();
        
        // ヘッダタブの検証結果がOKの場合
        if (workForm.valid()) {
            // 詳細タブ検証
            tabDetail.click();
            // 詳細タブ検証結果がOKの場合
            if (workForm.valid()) {
                // 検証結果OK
                result = true;
            }
            // 詳細タブ検証結果がNGの場合
            else {
                // サブミット(検証結果の表示)
                workForm.find(':submit').click();
            }
        }
        // ヘッダタブの検証結果がNGの場合
        else {
            // 詳細タブ検証
            tabDetail.click();
            // サブミット(検証結果の表示)
            workForm.find(':submit').click();
            // ヘッダタブに切り替え
            tabHeader.click();            
        }
        // 両タブの透明化を解除
        divHeader.css('opacity', '');
        divDetail.css('opacity', '');

        return result;
    };

    // 製品サブミット
    var submitProduct = function (invoker) {
        // クリックイベント無効可
        // $(invoker).off('click');

        var formData = workForm.serializeArray();
        formData.push({ name: "strSessionID", value: $.cookie('strSessionID') });
        // リクエスト送信
        $.ajax({
            url: '/p/modify/modify_confirm.php',
            type: 'POST',
            data: formData
        })
            .done(function (response) {
                var w = window.open();
                w.document.open();
                w.document.write(response);
                w.document.close();
                w.onunload = function () {
                    window.opener.location.reload();
                }
            })
            .fail(function (response) {
                alert("fail");
                alert(response);

            });
    };

    $("img.edit").on('click', function () {
        var display = $('#EditFrame').css('display');
        if (display == "block") {
            $("#EditFrame").css("display", "none");
        } else {
            $("#EditFrame").css("display", "block");
        }
        
        window.editWin.fncEditParentToHtmltext();
    });
})();
