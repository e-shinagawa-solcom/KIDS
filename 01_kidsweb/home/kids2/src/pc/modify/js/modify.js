(function () {
    // テーブル各セルの幅をリセットする
    resetTableWidth($("#tableB_chkbox_head"), $("#tableB_chkbox"), $("#tableB_head"), $("#tableB"));
    // テーブル行クリックイベントの設定
    selectRow('hasChkbox', $("#tableB_chkbox"), $("#tableB"), $("#allChecked"), 1);
    // 対象チェックボックスチェック状態の設定
    scanAllCheckbox($("#tableB_chkbox"), $("#allChecked"));
    // 税抜金額の合計の計算
    totalPriceCalculation($("#tableB_chkbox"), $("#tableB"));
    // チェックボックスクリックイベントの設定
    setCheckBoxClickEvent($('input[name="edit"]'), $("#tableB"), $("#tableB_chkbox"), $("#allChecked"), 1);
    // 対象チェックボックスクリックイベントの設定
    setAllCheckClickEvent($("#allChecked"), $("#tableB"), $("#tableB_chkbox"));

    $("select[name='lngMonetaryRateCode'] option:not(:selected)").prop('disabled', true);
    
    $('#cancel').on('click', function () {
        window.close();
    });

})();