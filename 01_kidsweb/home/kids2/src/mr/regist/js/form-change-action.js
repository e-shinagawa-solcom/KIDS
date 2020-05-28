// 返却予定日　初期状態では入力不要
$('input[name="ReturnSchedule"]')
.prop({ disabled: true })
.css({ opacity: 0.3 });
// 生産後の処理が保管工場に返却の場合、返却予定日欄を有効にする
$('select[name="FinalKeep"]').on({
    'change': function(){
        $('input[name="ReturnSchedule"]').trigger('blur');
        var chk = $(this).prop('selectedIndex'),
            rtnDay = $('input[name="ReturnSchedule"]');
        (chk == 2) ? rtnDay.prop('disabled', false).css('opacity', 1) : rtnDay.prop('disabled', true).css('opacity', 0.3).val('');
    }
});

// 帳票区分が廃棄版の場合、移動方法、生産後の処理、保管工場、移動工場欄を無効にする
$('select[name="ReportCategory"]').on({
    'change': function(){
        if ( $(this).prop('selectedIndex') == 0 | $(this).prop('selectedIndex') == 1 | $(this).prop('selectedIndex') == 2 ) {
            $('select[name="TransferMethod"]').prop('disabled', false).css('opacity', 1);
            $('select[name="FinalKeep"]').prop({'disabled': false, 'selectedIndex': 0}).css('opacity', 1);
            $('input[name="SourceFactory"]').css('opacity', 1);
            $('input[name="SourceFactoryName"]').css('opacity', 1);
            $('input[name="DestinationFactory"]').prop('disabled', false).css('opacity', 1);
            $('input[name="DestinationFactoryName"]').css('opacity', 1);
            $('input[name="DestinationFactoryName"]').next('span').css('display',  '');
            $('input[name="SourceFactoryName"]').next('span').css('display',  '');
            // 生産後の処理に応じて返却予定日の有効無効を設定
            $('select[name="FinalKeep"]').trigger('change');

        } else if ( $(this).prop('selectedIndex') == 3 ) {
            $('select[name="TransferMethod"]').prop('disabled', true).css('opacity', 0.3);
            $('select[name="FinalKeep"]').prop({'disabled': true, 'selectedIndex': 0}).css('opacity', 0.3);
            $('input[name="ReturnSchedule"]').prop('disabled', true).css('opacity', 0.3).val('');
            $('input[name="SourceFactory"]').css('opacity', 0.3);
            $('input[name="SourceFactoryName"]').css('opacity', 0.3);
            $('input[name="DestinationFactory"]').prop('disabled', true).css('opacity', 0.3);
            $('input[name="DestinationFactoryName"]').css('opacity', 0.3);
            $('input[name="DestinationFactoryName"]').next('span').css('display',  'none');
            $('input[name="SourceFactoryName"]').next('span').css('display',  'none');
            // 返却予定日のvalidationキック
            $('select[name="FinalKeep"]').trigger('change');
        }
    }
});

// 製品コード変更時保管工場と金型説明テーブルをリセット
$('input[name="ProductCode"]').on({
    'change': function(){
        // 保管工場リセット
        $('input[name="SourceFactory"]').val('').trigger('change');
        // 金型説明テーブル配下のTBODY要素の子要素を削除
        $('form[name="RegistMoldReport"]').find('table.table-description').find('tbody').children().remove();
    }
});
