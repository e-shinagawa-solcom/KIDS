
(function(){
    // フォーム
    var workForm = $('form[name="RegistMoldReport"]');
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
    // 金型セレクトボックス
    var moldList = $('.mold-selection__list');
    var moldChoosenList = $('.mold-selection__choosen-list');
    // 追加ボタン
    var btnDel = $('.mold-selection__backimage-add-del .list-del');

    // フォームサブミット抑止
    $('document').on('submit', 'form', function(e){
        e.preventDefault();
        return false;
    });

    // 閉じた際の処理
    $(window).on('beforeunload', function(){
        $(window.opener.opener.document).find('form').submit();
    });

    // クリアボタン
    btnClear.on('click', function(){
        // 選択した金型を戻す
        moldChoosenList.find('option').prop('selected', true);
        btnDel.click();
        // テキスト入力箇所をリセット
        workForm.find('input:not(input[name="ProductCode"], ' +
                    'input[name="ProductName"], ' +
                    'input[name="GoodsCode"]' +
                    '), textarea').val('');
        // ヘッダタブのSELECT要素は先頭OPTION要素にリセット
        workForm.find('.regist-tab-header select').each(function(index){
            $(this).val($(this).find('option').first().val());
        });
        // 検証処理のキック
        validate(this);
    });

    // 登録ボタン押下時の処理
    btnRegist.on('click', function(){
            clickRegist(this);
    });

    // 登録ボタンクリック時に呼び出すfunction
    var clickRegist = function(invoker) {
        // フォーム検証
        if(validate(invoker)){
            // サブミット処理
            submitMoldReport(invoker);
        }
    }

    // 検証処理のキック
    var validate = function(invoker) {
        // 検証結果
        var result = false;

        // 両タブ内容を透明にして一旦隠す
        divHeader.css('opacity', 0.0);
        divDetail.css('opacity', 0.0);

        // ヘッダタブ検証
        tabHeader.click();
        // ヘッダタブの検証結果がOKの場合
        if(workForm.valid()){
            // 詳細タブ検証
            tabDetail.click();
            // 詳細タブ検証結果がOKの場合
            if(workForm.valid()){
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

    // 金型帳票サブミット
    var submitMoldReport = function(invoker) {
        // クリックイベント無効可
        $(invoker).off('click');

        var formData = workForm.serializeArray();

        // 金型帳票IDの追加
        formData.push({
            name: 'MoldReportId',
            value: $.cookie('MoldReportId')
        });

        // リビジョンの追加
        formData.push({
            name: 'Revision',
            value: $.cookie('Revision')
        });

        // バージョンの追加
        formData.push({
            name: 'Version',
            value: $.cookie('Version')
        });

        // デバッグ出力
        $.each(formData, function(index, data){
            console.log(data.name + ' : ' + data.value);
        });

        // リクエスト送信
        $.ajax({
            url: '/mold/validation/MoldReport/modify.php?strSessionID=' + $.cookie('strSessionID'),
            type: 'post',
            dataType: 'json',
            data: formData
        })
        .done(function(response){
            console.log('金型帳票登録-検証 done');

            // 検証OKの場合
            if (response.resultHash)
            {
                console.log('金型帳票登録-検証結果 OK');

                // 確認画面URL
                var confirmUrl = '/mr/modify/confirm/mr_confirm.php';
                // パラメータ
                var sessionId = 'strSessionID=' + $.cookie('strSessionID');
                var resultHash = 'resultHash=' + response.resultHash;
                var moldReportId='MoldReportId=' + $.cookie('MoldReportId');
                var revision='Revision=' + $.cookie('Revision');
                var version='Version=' + $.cookie('Version');

                var url  = confirmUrl + '?' +
                        sessionId + '&' +
                        resultHash + '&' +
                        moldReportId + '&' +
                        revision + '&' +
                        version;

                // 確認画面用iframe作成
                $dialogContent = $('<iframe>')
                                    .attr("class", "regist-confirm")
                                    .attr("src", url)
                                    .attr("frameborder", 0)
                                    .attr("style", "width: 300px; height: 600px;");

                // ダイアログ設定(jQuery UI)
                $dialogContent.dialog({
                    autoOpen: true,
                    closeOnEscape: true,
                    modal: true,
                    resizable: true,
                    draggable: true,
                    position: {
                        at: "left top"
                    },
                    hight: 500,
                    width: "auto",
                    // 閉じる際にiframeを破棄する
                    close: function(event, ui){
                        try {
                            // 確認画面以外で閉じた場合は窓を閉じる
                            if (location.origin != this.contentWindow.location.origin ||
                                !/\/mr\/modify\/confirm\/mr_confirm.php/.test(this.contentWindow.location.href)){
                                window.close();
                            }
                            // ダイアログ/iframe破棄
                            $(this).dialog('destroy');
                            $(event.target).remove();
                        }
                        // エラーの場合はリロード
                        catch (e){
                            location.reload();
                        }

                        // focusできなくなるバグ対応
                        $($('input[name="ProductCode"]')[1]).focus();

                        // 登録ボタン押下時の復活
                        btnRegist.on('click', function(){
                                clickRegist(this);
                        });
                    }
                });

                // jQuery UIで自動的に設定されるスタイルを削除
                $dialogContent.removeAttr("style");
                // ダイアログをセンタリングする
                var divDialog = $('body > .ui-dialog');
                divDialog.css("top", ( $(window).height() - divDialog.height() ) / 2 + $(window).scrollTop() + "px")
                         .css("left", ( $(window).width() - divDialog.width() ) / 2 + $(window).scrollLeft() + "px");
            }
            // 検証NGの場合
            else {
                console.log('金型帳票登録-検証結果 NG');
                console.log(response);

                // 両タブ内容を透明にして一旦隠す
                divHeader.css('opacity', 0.0);
                divDetail.css('opacity', 0.0);

                // alertで表示させるメッセージ群
                var alertMessages = '';

                // エラーメッセージのフィードバック
                $.each(response, function(name, msgError){
                    var element = $('[name="' + name + '"]');

                    // name属性が一致する場合
                    if (1 <= element.length){
                        // ヘッダDIVの子要素の場合
                        if(1 <= element.parents('div.regist-tab-header').length){
                            // 表示タブがheaderでない場合
                            if(tabs.prop('displayTab') != 'header'){
                                tabHeader.click();
                            }
                        }
                        // 詳細DIVの子要素の場合
                        else if(1 <= element.parents('div.regist-tab-detail').length){
                            // 表示タブがdetailでない場合
                            if(tabs.prop('displayTab') != 'detail'){
                                tabDetail.click();
                            }
                        }

                        invalidImg = $('<img>')
                                        .attr('class', classNameErrorIcon)
                                        .attr('src', urlErrorIcon)
                                        // CSS設定(表示位置)
                                        .css({
                                            position: 'absolute',
                                            top: $(element).position().top,
                                            left: $(element).position().left - 20,
                                        })
                                        // ツールチップ表示
                                        .tooltipster({
                                            trigger: 'hover',
                                            onlyone: false,
                                            position: 'top',
                                            content: msgError
                                        });

                        // エラーアイコンが存在しない場合
                        if ($(element).prev('img.' + classNameErrorIcon).length <= 0){
                            // エラーアイコンを表示
                            $(element).before(invalidImg);
                        }
                        // エラーアイコンが存在する場合
                        else {
                            // 既存のエラーアイコンのツールチップテキストを更新
                            $(element).prev('img.' + classNameErrorIcon)
                                        .tooltipster('content', msgError);
                        }
                    }
                    // それ以外のエラーメッセージはalertで表示する。
                    else {
                        alertMessages += msgError + "\r\n";
                    }
                });

                // 両タブの透明化を解除
                divHeader.css('opacity', '');
                divDetail.css('opacity', '');

                // alertメッセージが設定されている場合
                if (alertMessages){
                    alert(alertMessages);
                }

                // 登録ボタン押下時の復活
                btnRegist.on('click', function(){
                        clickRegist(this);
                });
            }
        })
        .fail(function(response){
            console.log('金型帳票登録-検証 fail');
            console.log(response.responseText);

            alert(
                "リクエストの処理中にエラーが発生しました。" + "\r\n" +
                "このエラーが解消されない場合はシステム担当者にご連絡下さい。"
            );

            // 登録ボタン押下時の復活
            btnRegist.on('click', function(){
                    clickRegist(this);
            });
        });
    };
})();
