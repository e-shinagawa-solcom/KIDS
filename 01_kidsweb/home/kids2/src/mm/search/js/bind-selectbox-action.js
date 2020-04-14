
(function(){
    // 金型リスト
    var moldList = $('.mold-selection__list');
    // 選択中の金型リスト
    var moldChoosenList = $('.mold-selection__choosen-list');

    // 追加ボタン(→)
    $('.list-add').on({
        'click': function(){
            // セレクトボックス間の移動
            selectBoxMoveTo(moldList, moldChoosenList);
            // 選択中の金型リストのソート
            selectBoxCommand(moldChoosenList, 'sort');
            
            moldList.focus();
        }
    });

    // 削除ボタン(←)
    $('.list-del').on({
        'click': function(){
            // セレクトボックス間の移動
            selectBoxMoveTo(moldChoosenList, moldList);
            // 金型リストのソート
            selectBoxCommand(moldList, 'sort');

            moldChoosenList.focus();
        }
    });

    // UPボタン
    $('.list-up').on({
        'click': function(){
            selectBoxCommand(moldChoosenList, 'up');
        }
    });

    // DOWNボタン
    $('.list-down').on({
        'click': function(){
            selectBoxCommand(moldChoosenList, 'down');
        }
    });

    // SELECT ALLボタン
    $('.mold-selection tr > td:nth-of-type(even) > img.list-sort').on({
        'click': function(){
            $(this).parent().prev().find('select').find('option').prop('selected', true);
        }
    });

})();
