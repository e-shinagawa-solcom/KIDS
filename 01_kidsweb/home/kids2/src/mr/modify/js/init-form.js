(function(){
    // changeイベントを発生させる要素リスト
    var list_onchange = [
          $('.regist-tab-header input[name="ProductCode"]')
        , $('input[name="CustomerCode"]')
        , $('input[name="KuwagataGroupCode"]')
        , $('input[name="KuwagataUserCode"]')
        , $('input[name="DestinationFactory"]')
    ];

    // 初期値設定対象のSELECT要素リスト
    var list_select = [
          $('select[name="ReportCategory"]')
        , $('select[name="RequestCategory"]')
        , $('select[name="TransferMethod"]')
        , $('select[name="InstructionCategory"]')
        , $('select[name="FinalKeep"]')
    ];

    // 金型セレクトボックスの取得
    var moldList = $('.mold-selection__list');
    var moldChoosenList = $('.mold-selection__choosen-list');

    // 初期化に使用する金型情報リスト
    var list_initMoldRecord = $('.init-mold-info__record');

    // 追加ボタン
    var btnAdd = $('.mold-selection__backimage-add-del .list-add');
    // 金型説明テーブル
    var tableMoldDescription = $('.table-description');

    // onchangeイベントキック
    $.each(list_onchange, function(){
        this.change();
    });

    // SELECT要素の初期値設定 & onchangeイベントキック
    $.each(list_select, function(){
        var init_value = this.attr('init-value');
        this.find('option[value="' + init_value + '"]').prop('selected', true);
        this.change();
    });

    // 金型リスト読込完了時に「選択済みの金型リスト」を初期化する
    moldList.on('load-completed', function(){
        var options = moldList.find('option');

        // 金型NO数分走査
        list_initMoldRecord.each(function(i, row){
            var cur = $(row).attr('moldno');
            // OPTION要素数分走査
            options.each(function(j, option) {
                var target = $(option).val();
                // 金型リストに含まれている場合
                if (cur === target) {
                    // 選択状態にする
                    $(this).prop('selected', true);
                    // ループから抜ける
                    return false;
                }
            });
        });

        // 追加ボタンのクリック(金型説明テーブルの作成)
        btnAdd.click();

        $('input[name="SourceFactory"]').val($('input[name="init_SourceFactory"]').val());
        $('input[name="SourceFactory"]').change();
    });

    // 金型説明の作成完了時に金型説明を設定する
    tableMoldDescription.on('create-completed', function(){
        var trs = $(this).find('tbody > tr');

        // 金型説明テーブルのレコード件数分走査
        trs.each(function(i, table_row){
            var cur_table_moldno = $(table_row).attr('moldno');
            // 初期化対象となる金型説明の件数分走査
            list_initMoldRecord.each(function(j, init_row){
                var cur_init_moldno = $(init_row).attr('moldno')
                // 金型説明テーブル内に初期化対象の金型が存在する場合
                if (cur_table_moldno === cur_init_moldno){
                    // 金型説明を初期化
                    $(table_row).find('input[name^="MoldDescription"]').val($(init_row).attr('desc'));
                    // ループから抜ける
                    return false;
                };
            });
        });
    });
})();
