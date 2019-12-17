(function() {
    var mswBox = $('.msw-box');
    var productCode = mswBox.find('.input-code');
    var reviseCode = mswBox.find('.revise-code');
    var productName = mswBox.find('.input-name');
    var btnSearch = mswBox.find('.search-btn img');

    // TabKeyのフォーカス制御
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
            selectProducts();
        },
        // EnterKey
        'keypress': function(e) {
            if(e.which == 13){
                selectProducts();
            }
        }
    });
    var selectProducts = function() {
        $('select').find('option').remove();
        switch (isEmpty(productCode.val()) + isEmpty(productName.val()) + isEmpty(reviseCode.val())) {
            // どちらも未入力
            case '000':
                var condition = {
                    data: {
                        QueryName: 'selectProducts'
                    }
                };
                break;
            // 製品名称のみ入力
            case '010':
                var condition = {
                    data: {
                        QueryName: 'selectProductByProductName',
                        Conditions: {
                            ProductName: productName.val()
                        }
                    }
                };
                break;
            // 製品コードのみ入力
            case '100':
                var condition = {
                    data: {
                        QueryName: 'selectProductByProductCode',
                        Conditions: {
                            ProductCode: productCode.val()
                        }
                    }
                };
                break;
            // 再販コードのみ入力
            case '001':
                    var condition = {
                        data: {
                            QueryName: 'selectProductByReviseCode',
                            Conditions: {
                                ReviseCode: reviseCode.val()
                            }
                        }
                    };
                    break;
            // 製品コード、再販コードのみ入力
            case '101':
                    var condition = {
                        data: {
                            QueryName: 'selectProductByCode',
                            Conditions: {
                                ProductCode: productCode.val(),
                                ReviseCode: reviseCode.val()
                            }
                        }
                    };
                    break;
            
            // 製品名称、再販コードのみ入力
            case '011':
                    var condition = {
                        data: {
                            QueryName: 'selectProductByNameAndReviseCode',
                            Conditions: {
                                ReviseCode: reviseCode.val(),
                                ProductName: productName.val()
                            }
                        }
                    };
                    break;
            // どちらも入力
            case '110':
                var condition = {
                    data: {
                        QueryName: 'selectProductByProductCodeAndName',
                        Conditions: {
                            ProductCode: productCode.val(),
                            ProductName: productName.val()
                        }
                    }
                };
                break;
            // どちらも入力
            case '111':
                var condition = {
                    data: {
                        QueryName: 'selectProductByCodeAndName',
                        Conditions: {
                            ProductCode: productCode.val(),
                            ReviseCode: reviseCode.val(),
                            ProductName: productName.val()
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
                    code: this.productcode + "_" + this.revisecode,
                    name: this.productname
                })
                .html(this.productcode + "_" + this.revisecode + '&nbsp;&nbsp;&nbsp;' + this.productname)
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
