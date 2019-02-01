
(function(){
    // 金型リスト
    var moldList = $('.mold-selection__list');
    // 選択中の金型リスト
    var moldChoosenList = $('.mold-selection__choosen-list');
    // 保管元工場
    var sourceFactory = $('input[name="SourceFactory"]');
    // 金型説明テーブル
    var tableMoldDescription = $('.table-description');

    // 追加ボタン(→)
    $('.list-add').on({
        'click': function(){
            // セレクトボックス間の移動
            selectBoxMoveTo(moldList, moldChoosenList);
            // 現在の保管元(最新の移動先)が混在していないかチェック
            checkUniqueSourceFactory(moldChoosenList.find('option'));
            // 保管元工場項目への入力補助
            propSourceFactory(moldChoosenList);
            // 選択中の金型リストのソート
            selectBoxCommand(moldChoosenList, 'sort');
            // 金型説明入力欄の作成
            createFormMoldDescription(moldChoosenList.find('option'));
        }
    });

    // 削除ボタン(←)
    $('.list-del').on({
        'click': function(){
            // セレクトボックス間の移動
            selectBoxMoveTo(moldChoosenList, moldList);
            // 保管元工場項目への入力補助
            propSourceFactory(moldChoosenList);
            // 金型リストのソート
            selectBoxCommand(moldList, 'sort');
            // 金型説明入力欄の作成
            createFormMoldDescription(moldChoosenList.find('option'));
        }
    });

    // UPボタン
    $('.list-up').on({
        'click': function(){
            selectBoxCommand(moldChoosenList, 'up');
            // 保管元工場項目への入力補助
            propSourceFactory(moldChoosenList);
            // 金型説明入力欄の作成
            createFormMoldDescription(moldChoosenList.find('option'));
        }
    });

    // DOWNボタン
    $('.list-down').on({
        'click': function(){
            selectBoxCommand(moldChoosenList, 'down');
            // 保管元工場項目への入力補助
            propSourceFactory(moldChoosenList);
            // 金型説明入力欄の作成
            createFormMoldDescription(moldChoosenList.find('option'));
        }
    });

    // SELECT ALLボタン
    $('.mold-selection tr > td:nth-of-type(even) > img.list-sort').on({
        'click': function(){
            $(this).parent().prev().find('select').find('option').prop('selected', true);
        }
    });

    // 保管元工場項目への入力補助
    var propSourceFactory = function(selectBox){
        sourceFactory.val(selectBox.find('option').first().attr('displaycode'));
        sourceFactory.trigger('change');
    };

    // 金型説明入力欄の作成
    var createFormMoldDescription = function(options){
        // データ部リセット
        tableMoldDescription.find('tbody').empty();
        // OPTION要素数分走査
        options.each(function(index){
            var nameMoldNo = 'MoldNo' + (index + 1);
            var nameMoldDescription = 'MoldDescription' + (index + 1);

            var row = $('<tr>').attr('moldno', $(this).val());
            var colNo = $('<td>').append(index + 1);
            var colMoldNo = $('<td>').append(
                                $('<input>')
                                    .attr('name', nameMoldNo)
                                    .attr('readonly', "")
                                    .val($(this).val())
                            );
            var colDescription = $('<td>').append($('<input>').attr('name', nameMoldDescription));

            row.append(colNo);
            row.append(colMoldNo);
            row.append(colDescription);

            tableMoldDescription.append(row);
        });
    };

    // 現在の保管元(最新の移動先)が混在していないかチェック
    var checkUniqueSourceFactory = function(options){
        // OPTION要素が存在しない場合
        if (options.length <= 0) {return;}

        // 除外する表示会社コードリスト
        var excludeDisplayCompanyCode = new Array();

        // 表示会社コードリストの抽出
        options.each(function(index){
            excludeDisplayCompanyCode.push($(this).attr('displaycode'));
        });

        // 値が重複している要素を削除
        excludeDisplayCompanyCode = $.unique(excludeDisplayCompanyCode);

        // ユニークな表示会社コードが1つの場合
        if (excludeDisplayCompanyCode.length === 1){return;}

        // 先頭の表示会社コード(優先)を取得
        var ignoreCode = excludeDisplayCompanyCode[0];
        options.each(function(i, option){
            // 除外対象のOPTIONをSELECTEDに切り替える
            $.each(excludeDisplayCompanyCode, function(j, value){
                // 先頭要素(除外対象外)の場合
                if ($(option).attr('displaycode') === ignoreCode){
                    $(option).prop('selected', false);
                }
                // それ以外(除外対象)
                else if ($(option).attr('displaycode') === value){
                    $(option).prop('selected', true);
                }
            });
        });
        // メッセージ出力
        alert('保管元工場が異なる金型を同時に登録することはできません。');
        // 除外対象のOPTION要素を戻す
        $('.list-del').trigger('click');
    };
})();
