(function () {
    // テーブル各セルの幅をリセットする
    resetTableWidth($("#tableB_chkbox_head"), $("#tableB_chkbox"), $("#tableB_head"), $("#tableB"));
    // テーブル行クリックイベントの設定
    selectRow('hasChkbox', $("#tableB_chkbox"), $("#tableB"), $("#allChecked"));
    // 対象チェックボックスチェック状態の設定
    scanAllCheckbox($("#tableB_chkbox"), $("#allChecked"));
    // チェックボックスクリックイベントの設定
    setCheckBoxClickEvent($('input[name="edit"]'), $("#tableB"), $("#tableB_chkbox"), $("#allChecked"));
    // 対象チェックボックスクリックイベントの設定
    setAllCheckClickEvent($("#allChecked"), $("#tableB"), $("#tableB_chkbox"));

    $("#allChecked").prop('checked', true);

    $("select[name='lngMonetaryRateCode'] option:not(:selected)").prop('disabled', true);

    $("#tableB_chkbox").find("tbody tr").css("background-color", "#bbbbbb");
    $("#tableB").find("tbody tr").css("background-color", "#bbbbbb");


})();