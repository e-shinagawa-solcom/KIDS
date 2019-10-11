(function() {
    var mswBox = $('.msw-box');
    var locationCode = mswBox.find('.input-code');
    var locationName = mswBox.find('.input-name');
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
        // クリック
        'click': function() {
            selectlocations();
        },
        // EnterKey
        'keypress': function(e) {
            if(e.which == 13){
                selectlocations();
            }
        }
    });
    var selectlocations = function() {
        $('select').find('option').remove();
        switch (isEmpty(locationCode.val()) + isEmpty(locationName.val())) {
            // どちらも未入力
            case '00':
                var condition = {
                    data: {
                        QueryName: 'selectLocations'
                    }
                };
                break;
            // 製品名称のみ入力
            case '01':
                var condition = {
                    data: {
                        QueryName: 'selectLocationByLocationName',
                        Conditions: {
                            locationName: locationName.val()
                        }
                    }
                };
                break;
            // 製品コードのみ入力
            case '10':
                var condition = {
                    data: {
                        QueryName: 'selectLocationByLocationCode',
                        Conditions: {
                            locationCode: locationCode.val()
                        }
                    }
                };
                break;
            // どちらも入力
            case '11':
                var condition = {
                    data: {
                        QueryName: 'selectLocationByCodeAndName',
                        Conditions: {
                            locationCode: locationCode.val(),
                            locationName: locationName.val()
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
                    code: this.locationdisplaycode,
                    name: this.locationdisplayname
                })
                .html(this.locationdisplaycode + '&nbsp;&nbsp;&nbsp;' + this.locationdisplayname)
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
