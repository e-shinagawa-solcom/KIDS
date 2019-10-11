(function() {
    var mswBox = $('.msw-box');
    var userCode = mswBox.find('.input-code');
    var groupCode = mswBox.find('.dammy-input-code');
    var userName = mswBox.find('.input-name');
    var btnSearch = mswBox.find('.search-btn img');

    // TabKeyの制御
    mswBox.on(
        'keydown', 'input.input-code', function(e){
            // Tab + shiftでmswの最後の要素にフォーカスを戻す
            if(e.keyCode == 9 && e.shiftKey){
                mswBox.find('img.apply').focus();
                return false;
            }
        }
    );
    mswBox.on(
        'keydown', 'img.apply', function(e){
            // Tabのみでmswの最初の要素にフォーカスを戻す
            if(e.keyCode == 9 && !e.shiftKey){
                mswBox.find('input.input-code').focus();
                return false;
            }
        }
    );

    // 検索結果ダブルクリックで適用する
    $(".result-select").on("dblclick",  function(){
        mswBox.find('img.apply').trigger('click');
        mswBox.find('img.msw-box__header__close-btn').trigger('click');
    });

    // 検索ボタン押下時の処理
    btnSearch.on({
        'click': function() {
            selectusers();
        },
        // EnterKey
        'keypress': function(e) {
            if(e.which == 13){
                selectusers();
            }
        }
    });
    var selectusers = function() {
        $('select').find('option').remove();
        // 部署コードが入力されていなければ
        if( !$('.dammy-input-code').val() ){

        }
        switch (isEmpty(userCode.val()) + isEmpty(userName.val())) {
            // どちらも未入力
            case '00':
                var condition = {
                    data: {
                        QueryName: 'selectInChargeUsers',
                        Conditions: {
                            groupCode: groupCode.val()
                        }
                    }
                };
                break;
            // 製品名称のみ入力
            case '01':
                var condition = {
                    data: {
                        QueryName: 'selectInChargeUserByInChargeUserName',
                        Conditions: {
                            groupCode: groupCode.val(),
                            userName: userName.val()
                        }
                    }
                };
                break;
            // 製品コードのみ入力
            case '10':
                var condition = {
                    data: {
                        QueryName: 'selectInChargeUserByInChargeUserCode',
                        Conditions: {
                            groupCode: groupCode.val(),
                            userCode: userCode.val()
                        }
                    }
                };
                break;
            // どちらも入力
            case '11':
                var condition = {
                    data: {
                        QueryName: 'selectInChargeUserByCodeAndName',
                        Conditions: {
                            groupcode: groupCode.val(),
                            userCode: userCode.val(),
                            userName: userName.val()
                        }
                    }
                };
                break;
            default:
                break;
        }
        // マスター検索実行
        queryMasterData(condition, setResult, setNodata);
    };

    // 真偽値の文字列表現を取得
    function isEmpty(val) {
        if (val) {
            return '1';
        } else {
            return '0';
        }
    }

    // 検索結果をselectのoption要素にセット
    function setResult(response) {
        // 検索件数をカウンターにセット
        $('.result-count .counter').val(response.length);
        $.each(response, function() {
            $('.result-select').append(
                $('<option>')
                .attr({
                    code: this.usercode,
                    name: this.username
                })
                .html(this.usercode + '&nbsp;&nbsp;&nbsp;' + this.username)
            );
        });
    }

    // 検索結果0件の時optionにNoDataをセット
    function setNodata(response){
        console.log(response.responseText);
        // 検索件数リセット
        $('.result-count .counter').val('');
        $('.result-select').append(
            $('<option>')
                .attr('disabled','disabled')
                .html('(No&nbsp;&nbsp;Data)')
        );
    }
})();
