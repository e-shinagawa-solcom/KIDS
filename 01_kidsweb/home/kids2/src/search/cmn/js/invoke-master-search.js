
(function () {

    // �ޥ�����������
    var searchMaster = {
        url: '/mold/lib/queryMasterData.php?strSessionID=' + $.cookie('strSessionID'),
        type: 'post',
        dataType: 'json'
    };

    // �ܵ�-ɽ����ҥ����� ���٥����Ͽ
    $('input[name="lngCustomerCompanyCode"]').on({
        'change': function () {
            // ɽ��̾�����
            selectCustomerName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե�����������������̾�˹�碌��
            $('input[name="strCustomerCompanyName"]').focus();
        }
    });

    // ������-ɽ����ҥ����� ���٥����Ͽ
    $('input[name="lngCustomerCode"]').on({
        'change': function () {
            var iscustomer = $(this).attr('iscustomer');
            if (iscustomer == "yes") {
                selectCustomerName($(this));
            } else {
                // ɽ��̾�����
                selectSupplierName($(this));
            }
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե�����������������̾�˹�碌��
            $('input[name="strCustomerName"]').focus();
        }
    });

    // �ܵ�ô����-ɽ���桼���������� ���٥����Ͽ
    $('input[name="lngCustomerUserCode"]').on({
        'change': function () {
            // ɽ��̾�����
            selectUserName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե�����������������̾�˹�碌��
            $('input[name="strCustomerUserName"]').focus();
        }
    });

    // ���ϼ�-ɽ���桼���������� ���٥����Ͽ
    $('input[name="lngInputUserCode"]').on({
        'change': function () {
            // ɽ��̾�����
            selectInputUserName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե�����������������̾�˹�碌��
            $('input[name="strInputUserName"]').focus();
        }
    });

    // ��ɼ��-ɽ���桼���������� ���٥����Ͽ
    $('input[name="lngInsertUserCode"]').on({
        'change': function () {
            // ɽ��̾�����
            selectInputUserName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե�����������������̾�˹�碌��
            $('input[name="strInsertUserName"]').focus();
        }
    });

    // ��ȯô����-ɽ���桼���������� ���٥����Ͽ
    $('input[name="lngDevelopUserCode"]').on({
        'change': function () {
            // ɽ��̾�����
            selectInputUserName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե�����������������̾�˹�碌��
            $('input[name="strDevelopUserName"]').focus();
        }
    });

    // �Ķ�����-ɽ���桼���������� ���٥����Ͽ
    $('input[name="lngInChargeGroupCode"]').on({
        'change': function () {
            // ɽ��̾�����
            selectGroupName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե�����������������̾�˹�碌��
            $('input[name="strInChargeGroupName"]').focus();
        }
    });

    // ô����-ɽ���桼���������� ���٥����Ͽ
    $('input[name="lngInChargeUserCode"]').on({
        'change': function () {
            // ɽ��̾�����
            selectInChargeUserName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե�����������������̾�˹�碌��
            $('input[name="strInChargeUserName"]').focus();
        }
    });

    // ��������-ɽ����ҥ����� ���٥����Ͽ
    $('input[name="lngFactoryCode"]').on({
        'change': function () {
            // ɽ��̾�����
            selectFactoryName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե�����������������̾�˹�碌��
            $('input[name="strFactoryName"]').focus();
        }
    });

    // ���å���֥깩��-ɽ����ҥ����� ���٥����Ͽ
    $('input[name="lngAssemblyFactoryCode"]').on({
        'change': function () {
            // ɽ��̾�����
            selectFactoryName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե�����������������̾�˹�碌��
            $('input[name="strAssemblyFactoryName"]').focus();
        }
    });

    // Ǽ�ʾ��-ɽ����ҥ����� ���٥����Ͽ
    $('input[name="lngDeliveryPlaceCode"]').on({
        'change': function () {
            // ɽ��̾�����
            selectLocationName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե�����������������̾�˹�碌��
            $('input[name="strDeliveryPlaceName"]').focus();
        }
    });

    // Ǽ�ʾ��-ɽ����ҥ����� ���٥����Ͽ
    $('input[name="lngLocationCode"]').on({
        'change': function () {
            // ɽ��̾�����
            selectLocationName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե�����������������̾�˹�碌��
            $('input[name="strLocationName"]').focus();
        }
    });

    // --------------------------------------------------------------------------
    // Ǽ�ʾ��-ɽ����ҥ����ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // Ǽ�ʾ��-ɽ����ҥ����ɤ���ɽ��̾�����
    var selectLocationName = function (invoker) {
        console.log("����-ɽ����ҥ�����->ɽ��̾ change value=" + $(invoker).val());
        // ������̤Υ��å���CSS���쥯���κ���
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // �������0��λ��Υ��������CSS���쥯���κ���
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';
        // �������
        var condition = {
            data: {
                QueryName: 'selectLocationName',
                Conditions: {
                    CompanyDisplayName: $(invoker).val()
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("����-ɽ����ҥ�����->ɽ��̾ done");
                // ����-ɽ��̾���ͤ򥻥å�
                $(targetCssSelector).val(response[0].locationdisplayname);
            })
            .fail(function (response) {
                console.log("����-ɽ����ҥ�����->ɽ��̾ fail");
                console.log(response.responseText);
                // ����-�����ɡ�ɽ��̾���ͤ�ꥻ�åȤ�����������˥ե�������
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };

    // --------------------------------------------------------------------------
    // ����-ɽ����ҥ����ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ����-ɽ����ҥ����ɤ���ɽ��̾�����
    var selectFactoryName = function (invoker) {
        console.log("����-ɽ����ҥ�����->ɽ��̾ change value=" + $(invoker).val());
        // ������̤Υ��å���CSS���쥯���κ���
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // �������0��λ��Υ��������CSS���쥯���κ���
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';
        // �������
        var condition = {
            data: {
                QueryName: 'selectFactoryName',
                Conditions: {
                    CompanyDisplayName: $(invoker).val()
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("����-ɽ����ҥ�����->ɽ��̾ done");
                // ����-ɽ��̾���ͤ򥻥å�
                $(targetCssSelector).val(response[0].companydisplayname);
            })
            .fail(function (response) {
                console.log("����-ɽ����ҥ�����->ɽ��̾ fail");
                console.log(response.responseText);
                // ����-�����ɡ�ɽ��̾���ͤ�ꥻ�åȤ�����������˥ե�������
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };

    // --------------------------------------------------------------------------
    // ������-ɽ����ҥ����ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ������-ɽ����ҥ����ɤ���ɽ��̾�����
    var selectSupplierName = function (invoker) {
        console.log("������-ɽ����ҥ�����->ɽ��̾ change");
        // ������̤Υ��å���CSS���쥯���κ���
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // �������0��λ��Υ��������CSS���쥯���κ���
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';
        // �������
        var condition = {
            data: {
                QueryName: 'selectSupplierName',
                Conditions: {
                    CompanyDisplayName: $(invoker).val()
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("����-ɽ����ҥ�����->ɽ��̾ done");
                // ����-ɽ��̾���ͤ򥻥å�
                $(targetCssSelector).val(response[0].supplierdisplayname);
            })
            .fail(function (response) {
                console.log("����-ɽ����ҥ�����->ɽ��̾ fail");
                console.log(response.responseText);
                // ����-�����ɡ�ɽ��̾���ͤ�ꥻ�åȤ�����������˥ե�������
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };

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
                QueryName: 'selectCustomerName',
                Conditions: {
                    CompanyDisplayName: $(invoker).val()
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("����-ɽ����ҥ�����->ɽ��̾ done");
                // ����-ɽ��̾���ͤ򥻥å�
                $(targetCssSelector).val(response[0].companydisplayname);
            })
            .fail(function (response) {
                console.log("����-ɽ����ҥ�����->ɽ��̾ fail");
                console.log(response.responseText);
                // ����-�����ɡ�ɽ��̾���ͤ�ꥻ�åȤ�����������˥ե�������
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };
    // --------------------------------------------------------------------------
    // �ܵ�ô����-ɽ���桼�������ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // �ܵ�ô����-ɽ���桼�������ɤ���ɽ��̾�����
    var selectUserName = function (invoker) {
        console.log("ô����-ɽ���桼��������->ɽ��̾ change");// ������̤Υ��å���CSS���쥯���κ���
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // �������0��λ��Υ��������CSS���쥯���κ���
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';
        // �������
        var condition = {
            data: {
                QueryName: 'selectUserName',
                Conditions: {
                    UserDisplayName: $(invoker).val()
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("ô����-ɽ���桼��������->ɽ��̾ done");
                // ô����-ɽ��̾���ͤ򥻥å�
                $(targetCssSelector).val(response[0].userdisplayname);
            })
            .fail(function (response) {
                console.log("ô����-ɽ���桼��������->ɽ��̾ fail");
                console.log(response.responseText);
                // ô����-ɽ��̾���ͤ�ꥻ�å�
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };
    // --------------------------------------------------------------------------
    // ���ϼ�-ɽ���桼�������ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ���ϼ�-ɽ���桼�������ɤ���ɽ��̾�����
    var selectInputUserName = function (invoker) {
        console.log("ô����-ɽ���桼��������->ɽ��̾ change");// ������̤Υ��å���CSS���쥯���κ���
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // �������0��λ��Υ��������CSS���쥯���κ���
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';
        // �������
        var condition = {
            data: {
                QueryName: 'selectInputUserName',
                Conditions: {
                    UserDisplayName: $(invoker).val()
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("ô����-ɽ���桼��������->ɽ��̾ done");
                // ô����-ɽ��̾���ͤ򥻥å�
                $(targetCssSelector).val(response[0].userdisplayname);
            })
            .fail(function (response) {
                console.log("ô����-ɽ���桼��������->ɽ��̾ fail");
                console.log(response.responseText);
                // ô����-ɽ��̾���ͤ�ꥻ�å�
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };
    // --------------------------------------------------------------------------
    // ��ȯô����-ɽ���桼�������ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ��ȯô����-ɽ���桼�������ɤ���ɽ��̾�����
    var selectDevelopUserName = function (invoker) {
        console.log("ô����-ɽ���桼��������->ɽ��̾ change");// ������̤Υ��å���CSS���쥯���κ���
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // �������0��λ��Υ��������CSS���쥯���κ���
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';
        // �������
        var condition = {
            data: {
                QueryName: 'selectDevelopUserName',
                Conditions: {
                    UserDisplayName: $(invoker).val()
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("ô����-ɽ���桼��������->ɽ��̾ done");
                // ô����-ɽ��̾���ͤ򥻥å�
                $(targetCssSelector).val(response[0].userdisplayname);
            })
            .fail(function (response) {
                console.log("ô����-ɽ���桼��������->ɽ��̾ fail");
                console.log(response.responseText);
                // ô����-ɽ��̾���ͤ�ꥻ�å�
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };
    
    // --------------------------------------------------------------------------
    // �Ķ�ô����-ɽ���桼�������ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // �Ķ�ô����-ɽ���桼�������ɤ���ɽ��̾�����
    var selectInChargeUserName = function (invoker) {
        console.log("ô����-ɽ���桼��������->ɽ��̾ change");// ������̤Υ��å���CSS���쥯���κ���
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // �������0��λ��Υ��������CSS���쥯���κ���
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';
        // �������
        var condition = {
            data: {
                QueryName: 'selectInChargeUserName',
                Conditions: {
                    UserDisplayName: $(invoker).val()
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("�Ķ�ô����-ɽ���桼��������->ɽ��̾ done");
                // ô����-ɽ��̾���ͤ򥻥å�
                $(targetCssSelector).val(response[0].userdisplayname);
            })
            .fail(function (response) {
                console.log("�Ķ�ô����-ɽ���桼��������->ɽ��̾ fail");
                console.log(response.responseText);
                // ô����-ɽ��̾���ͤ�ꥻ�å�
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };

    

    // --------------------------------------------------------------------------
    // ô�����롼��-ɽ�����롼�ץ����ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ô�����롼��-ɽ�����롼�ץ����ɤ���ɽ��̾�����
    var selectGroupName = function(invoker){
        console.log("ô�����롼��-ɽ�����롼�ץ�����->ɽ��̾ change");
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // �������0��λ��Υ��������CSS���쥯���κ���
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';

        // �������
        var condition = {
            data: {
                QueryName: 'selectGroupName',
                Conditions: {
                    GroupDisplayName: $(invoker).val()
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("ô�����롼��-ɽ�����롼�ץ�����->ɽ��̾ done");
            $(targetCssSelector).val(response[0].groupdisplayname);
        })
        .fail(function(response){
            console.log("ô�����롼��-ɽ�����롼�ץ�����->ɽ��̾ fail");
            console.log(response.responseText);
            $(targetCssSelector).val('');
            $(targetCodeCssSelector).val('').focus();
        });
    };
})();
