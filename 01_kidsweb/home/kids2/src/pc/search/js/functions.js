(function () {
    // ����̾�Ρ��������ܡ��������ʡ��ܵ����֡�������ˡ��ñ����ñ�̡����̡���ȴ��ۡ���������
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

    // �����ԥ⡼�ɥ����å��ܥå����򥯥�å������
    $('input[name="Option_admin"]').on('click', function () {
        // �����å��Ѥߤξ��
        if (this.checked) {
            // �����å��ܥå���������
            $.each(chkboxElements, function () {
                this.prop('checked', false);
                this.prop('disabled', true);
            });
            $('input[type="checkbox"][name="IsDisplay_btnInvalid"]').prop('checked', true);
            $('input[type="checkbox"][name="IsDisplay_btnInvalid"]').prop('disabled', false);
        }
        // ̤�����å��ξ��
        else {
            // �����å��ܥå���������
            $.each(chkboxElements, function () {
                this.prop('disabled', false);
            });
            $('input[name="IsDisplay_lngRecordNo"]').prop('checked', true);
            $('input[type="checkbox"][name="IsDisplay_btnInvalid"]').prop('checked', false);
            $('input[type="checkbox"][name="IsDisplay_btnInvalid"]').prop('disabled', true);

        }
    });
})();
