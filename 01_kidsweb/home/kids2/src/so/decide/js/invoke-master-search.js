
(function () {
    $('select[name="lngSalesDivisionCode"]').val(0);
    // �ޥ�����������
    var searchMaster = {
        url: '/mold/lib/queryMasterData.php?strSessionID=' + $.cookie('strSessionID'),
        type: 'post',
        dataType: 'json'
    };

    // �ܵ�-ɽ����ҥ����� ���٥����Ͽ
    $('input[name="lngCustomerCode"]').on({
        'change': function () {        
            // ɽ��̾�����
            selectCustomerName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե�����������������̾�˹�碌��
            $('input[name="strCustomerName"]').focus();
        }
    });

        // --------------------------------------------------------------------------
    // �ܵ�-ɽ����ҥ����ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // �ܵ�-ɽ����ҥ����ɤ���ɽ��̾�����
    var selectCustomerName = function (invoker) {
        console.log("�ܵ�-ɽ����ҥ�����->ɽ��̾ change");
        // ������̤Υ��å���CSS���쥯���κ���
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // �������0��λ��Υ��������CSS���쥯���κ���
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';
        // �������
        var condition = {
            data: {
                QueryName: 'selectCustomerNameForSo',
                Conditions: {
                    CompanyDisplayName: $(invoker).val()
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log(response);
                console.log("����-ɽ����ҥ�����->ɽ��̾ done");
                // ����-ɽ��̾���ͤ򥻥å�
                $(targetCssSelector).val(response[0].customerdisplayname);
            })
            .fail(function (response) {
                console.log("����-ɽ����ҥ�����->ɽ��̾ fail");
                console.log(response.responseText);
                // ����-�����ɡ�ɽ��̾���ͤ�ꥻ�åȤ�����������˥ե�������
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };

    // �ܵ�-ɽ����ҥ����� ���٥����Ͽ
    $('select[name="lngSalesDivisionCode"]').on({
        'change': function () {
            var val = $('select[name="lngSalesDivisionCode"] option:selected').val();
            $.ajax({
                url: "/cmn/getdropdowndata.php",
                type: 'post',
                data: {
                    'lngProcessID': "cnSalesClassCode",
                    'strFormValue': val
                }
            })
                .done(function (response) {
                    var data = JSON.parse(response);
                    $('select[name="lngSalesClassCode"] option').remove();
                    $('select[name="lngSalesClassCode"]').append("<option value=''></option>");
                    $('select[name="lngSalesClassCode"]').append(data.pulldown);
                })
                .fail(function (response) {
                    alert("fail");
                })
        }
    });    
})();