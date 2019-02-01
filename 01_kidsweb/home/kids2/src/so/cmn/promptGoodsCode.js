function promptGoodsCode(){
    var elmProductCode = document.getElementById("ProductCode");
    var elmCustomerReceiveCode = document.getElementById("CustomerReceiveCode");
    var elmGoodsCode = document.getElementById("GoodsCode");
    var elmSessionID = document.getElementById("SessionID");

    // 顧客品番が未設定の場合
    if (elmProductCode.value && !elmGoodsCode.value) {
        // 入力ダイアログの表示
        var newgoodscode = window.prompt('顧客品番を入力してください。(半角英数のみ)', '');

        // キャンセル押下チェック
        if (!newgoodscode)
        {
            // メッセージ出力
            window.alert('製品コードに紐付く顧客品番は必須項目です。(仮受注の場合を除く)');
            return;
        }

        // 入力チェック
        if (!newgoodscode.match(/^[A-Za-z0-9]{1,10}$/)) {
            window.alert('顧客品番は半角英数かつ10文字以内で入力してください。');
            elmProductCode.fireEvent('onchange');
            return;
        }

        // 顧客品番の更新
        execUpdateGoodsCode(newgoodscode);
    }
}
