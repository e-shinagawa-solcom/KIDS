(function () {
    $('input[type="checkbox"][name="IsDisplay_strSalesCode"]').prop('checked', true);

    var chkboxElements = [
        $('input[type="checkbox"][name="IsDisplay_lngRecordNo"]')
        , $('input[type="checkbox"][name="IsDisplay_lngInChargeGroupCode"]')
        , $('input[type="checkbox"][name="IsDisplay_lngInChargeUserCode"]')
        , $('input[type="checkbox"][name="IsDisplay_strProductName"]')
        , $('input[type="checkbox"][name="IsDisplay_strProductEnglishName"]')
        , $('input[type="checkbox"][name="IsDisplay_lngSalesClassCode"]')
        , $('input[type="checkbox"][name="IsDisplay_strGoodsCode"]')
        , $('input[type="checkbox"][name="IsDisplay_dtmDeliveryDate"]')
        , $('input[type="checkbox"][name="IsDisplay_strNote"]')
        , $('input[type="checkbox"][name="IsDisplay_curProductPrice"]')
        , $('input[type="checkbox"][name="IsDisplay_lngProductUnitCode"]')
        , $('input[type="checkbox"][name="IsDisplay_lngProductQuantity"]')
        , $('input[type="checkbox"][name="IsDisplay_curSubTotalPrice"]')
        , $('input[type="checkbox"][name="IsDisplay_lngTaxClassCode"]')
        , $('input[type="checkbox"][name="IsDisplay_curTax"]')
        , $('input[type="checkbox"][name="IsDisplay_curTaxPrice"]')
        , $('input[type="checkbox"][name="IsDisplay_strDetailNote"]')
    ];

    // �����ԥ⡼�ɥ����å��ܥå����򥯥�å������
    $('input[name="Option_admin"]').on('click', function () {
        // �����å��Ѥߤξ��
        if (this.checked) {
            // �����å��ܥå���������
            $.each(chkboxElements, function () {
                this.prop('checked', false);
                this.prop('disabled', true);
            });


        }
        // ̤�����å��ξ��
        else {
            // �����å��ܥå���������
            $.each(chkboxElements, function () {
                this.prop('disabled', false);
            });
            $('input[name="IsDisplay_lngRecordNo"]').prop('checked', true);
        }
    });
})();
