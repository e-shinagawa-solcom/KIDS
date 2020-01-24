(function() {
    var mswBox = $('.msw-box');
    var groupCode = mswBox.find('.input-code');
    var groupName = mswBox.find('.input-name');
    var displayFlagLimit = mswBox.find('.display-flag-limit');
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
            selectgroups();
        },
        // EnterKey
        'keypress': function(e) {
            if(e.which == 13){
                selectgroups();
            }
        }
    });
    var selectgroups = function() {
        $('select').find('option').remove();
        if (displayFlagLimit.val() == '1') {
            displayFlagLimit0 = true;
            displayFlagLimit1 = true;
        } else {
            displayFlagLimit0 = true;
            displayFlagLimit1 = false;
        }
        switch (isEmpty(groupCode.val()) + isEmpty(groupName.val())) {
            // どちらも未入力
            case '00':
                var condition = {
                    data: {
                        QueryName: 'selectGroups',
                        Conditions: {
                            displayFlagLimit0: displayFlagLimit0,
                            displayFlagLimit1: displayFlagLimit1
                        }
                    }
                };
                break;
            // 製品名称のみ入力
            case '01':
                var condition = {
                    data: {
                        QueryName: 'selectGroupByGroupName',
                        Conditions: {
                            groupName: groupName.val(),
                            displayFlagLimit0: displayFlagLimit0,
                            displayFlagLimit1: displayFlagLimit1
                        }
                    }
                };
                break;
            // 製品コードのみ入力
            case '10':
                var condition = {
                    data: {
                        QueryName: 'selectGroupByGroupCode',
                        Conditions: {
                            groupCode: groupCode.val(),
                            displayFlagLimit0: displayFlagLimit0,
                            displayFlagLimit1: displayFlagLimit1
                        }
                    }
                };
                break;
            // どちらも入力
            case '11':
                var condition = {
                    data: {
                        QueryName: 'selectGroupByCodeAndName',
                        Conditions: {
                            groupCode: groupCode.val(),
                            groupName: groupName.val(),
                            displayFlagLimit0: displayFlagLimit0,
                            displayFlagLimit1: displayFlagLimit1
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
                    code: this.groupdisplaycode,
                    name: this.groupdisplayname
                })
                .html(this.groupdisplaycode + '&nbsp;&nbsp;&nbsp;' + this.groupdisplayname)
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
