(function () {

    $('input[type="checkbox"][name="IsDisplay_strStockCode"]').prop('checked', true);

    // 製品名称、仕入科目、仕入部品、顧客品番、運搬方法、単価、単位、数量、税抜金額、明細備考
    var chkboxElements = [
        $('input[type="checkbox"][name="IsDisplay_lngRecordNo"]')
        , $('input[type="checkbox"][name="IsDisplay_strProductName"]')
        , $('input[type="checkbox"][name="IsDisplay_lngInChargeGroupCode"]')
        , $('input[type="checkbox"][name="IsDisplay_lngInChargeUserCode"]')
        , $('input[type="checkbox"][name="IsDisplay_lngStockSubjectCode"]')
        , $('input[type="checkbox"][name="IsDisplay_lngStockItemCode"]')
        , $('input[type="checkbox"][name="IsDisplay_strMoldNo"]')
        , $('input[type="checkbox"][name="IsDisplay_strGoodsCode"]')
        , $('input[type="checkbox"][name="IsDisplay_lngDeliveryMethodCode"]')
        , $('input[type="checkbox"][name="IsDisplay_dtmDeliveryDate"]')
        , $('input[type="checkbox"][name="IsDisplay_curProductPrice"]')
        , $('input[type="checkbox"][name="IsDisplay_lngProductUnitCode"]')
        , $('input[type="checkbox"][name="IsDisplay_lngProductQuantity"]')
        , $('input[type="checkbox"][name="IsDisplay_curSubTotalPrice"]')
        , $('input[type="checkbox"][name="IsDisplay_lngTaxClassCode"]')
        , $('input[type="checkbox"][name="IsDisplay_curTax"]')
        , $('input[type="checkbox"][name="IsDisplay_curTaxPrice"]')
        , $('input[type="checkbox"][name="IsDisplay_strDetailNote"]')
    ];

    // 管理者モードチェックボックスをクリックする時
    $('input[name="Option_admin"]').on('click', function () {
        // チェック済みの場合
        if (this.checked) {
            // チェックボックスの設定
            $.each(chkboxElements, function () {
                this.prop('checked', false);
                this.prop('disabled', true);
            });
            $('input[type="checkbox"][name="IsDisplay_btnInvalid"]').prop('checked', true);
            $('input[type="checkbox"][name="IsDisplay_btnInvalid"]').prop('disabled', false);
        }
        // 未チェックの場合
        else {
            // チェックボックスの設定
            $.each(chkboxElements, function () {
                this.prop('disabled', false);
            });
            $('input[name="IsDisplay_lngRecordNo"]').prop('checked', true);
            $('input[type="checkbox"][name="IsDisplay_btnInvalid"]').prop('checked', false);
            $('input[type="checkbox"][name="IsDisplay_btnInvalid"]').prop('disabled', true);

        }
    });
})();
