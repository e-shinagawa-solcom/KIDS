(function(){
    // 金型ステータスの選択状態を初期化する
    $status = $('select[init-value]')
    // 金型ステータスのvalueと一致
    $status.find('option[value="' + $status.attr("init-value") + '"]').prop('selected', true);
    $status.change();
})();
