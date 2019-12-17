
(function(){

    // �ޥ�����������
    var searchMaster = {
                    url: '/mold/lib/queryMasterData.php?strSessionID=' + $.cookie('strSessionID'),
                    type: 'post',
                    dataType: 'json'
                };

    // --------------------------------------------------------------------------
    // ���٥����Ͽ
    // --------------------------------------------------------------------------
    // �إå����� ���ʥ����� ���٥����Ͽ
    $('input.mold-product-code').on({
        'change': function(){
            var revisecode = $('input[name="ReviseCode"]').val();
            // �ⷿ�ꥹ�Ⱥ���
            selectMoldSelectionList($(this), revisecode);
        }
    });
    // �إå����� ���Υ����� ���٥����Ͽ
    $('input[name="ReviseCode"]').on({
        'change': function () {
            var revisecode = $(this).val();
            var productcode = $('input.mold-product-code');
            if (productcode.val() != "") {
                // �ⷿ�ꥹ�Ⱥ���
                selectMoldSelectionList(productcode, revisecode);
            }
        }
    });
    // ������(�ܵ�)-ɽ����ҥ����� ���٥����Ͽ
    $('input[name="CustomerCode"]').on({
        'change': function(){
            // ɽ��̾�����
            selectCustomerName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե��������������̾�˹�碌��
            $('input[name="CustomerName"]').focus();
        }
    });
    // ô�����롼��-ɽ�����롼�ץ����� ���٥����Ͽ
    $('input[name="KuwagataGroupCode"]').on({
        'change': function(){
            // ɽ��̾�����
            selectGroupName($(this));
            // ���롼�ץ����ɤ����ξ��桼�����͡��������
            if( !$('input[name="KuwagataGroupCode"]').val() ){
                $('input[name="KuwagataUserCode"]').val('').change();
            }

            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե��������򥰥롼��̾�˹�碌��
            $('input[name="KuwagataGroupName"]').focus();
        }
    });
    // ô����-ɽ���桼�������� ���٥����Ͽ
    $('input[name="KuwagataUserCode"]').on({
        'change': function(){
            // ɽ��̾�����
            selectUserName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե���������桼��̾�˹�碌��
            $('input[name="KuwagataUserName"]').focus();
        }
    });
    // �ݴɸ�����/��ư�蹩��-ɽ����ҥ����� ���٥����Ͽ
    $('input[name="SourceFactory"], input[name="DestinationFactory"]').on({
        'change': function(){
            // ɽ��̾�����
            selectFactoryName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե���������ɽ�����̾�˹�碌��
            $(this).next('input').focus();
        }
    });
    // ��Ͽ��-ɽ���桼�������� ���٥����Ͽ
    $('input[name="CreateBy"]').on({
        'change': function(){
            // ɽ��̾�����
            selectCreateUserName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե���������桼��̾�˹�碌��
            $('input[name="CreateByName"]').focus();
        }
    });
    // ������-ɽ���桼�������� ���٥����Ͽ
    $('input[name="UpdateBy"]').on({
        'change': function(){
            // ɽ��̾�����
            selectUpdateUserName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե���������桼��̾�˹�碌��
            $('input[name="UpdateByName"]').focus();
        }
    });
    
    // ���ʥ����ɤ���ⷿ�ꥹ�Ȥ����
    var selectMoldSelectionList = function (invoker, revisecode) {
        console.log("���ʥ�����->�ⷿ�ꥹ�� change");
        var queryname = 'selectMoldByProductcode';
        var conditions = {
            ProductCode: $(invoker).val()
        };
        if (revisecode != "") {
            queryname = 'selectMoldByCode';
            conditions = {
                ProductCode: $(invoker).val(),
                ReviseCode: revisecode
            };
        }
        // �������
        var condition = {
            data: {
                QueryName: queryname,
                Conditions: conditions
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("���ʥ�����->�ⷿ�ꥹ�� done");

            // �ⷿ���쥯�ȥܥå����μ���
            var moldList = $('.mold-selection__list');
            var moldChoosenList = $('.mold-selection__choosen-list');

            // ��¸OPTION���Ǥκ��
            moldList.find('option').remove();

            // �������ʬ����
            $.each(response, function(index, row){
                // OPTION���Ǻ���
                moldList.append(
                    $('<option>')
                        .val(row.moldno)
                        .html(row.moldno)
                );
            });
        })
        .fail(function(response){
            console.log("���ʥ�����->�ⷿ�ꥹ�� fail");
            console.log(response.responseText);

            // �ⷿ���쥯�ȥܥå����μ���
            var moldList = $('.mold-selection__list');
            var moldChoosenList = $('.mold-selection__choosen-list');

            // ��¸OPTION���Ǥκ��
            moldList.find('option').remove();
        });
    };
    // --------------------------------------------------------------------------
    // ������(�ܵ�)-ɽ����ҥ����ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ������(�ܵ�)-ɽ����ҥ����ɤ���ɽ��̾�����
    var selectCustomerName = function(invoker){
        console.log("������(�ܵ�)-ɽ����ҥ�����->ɽ��̾ change");

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
        .done(function(response){
            console.log("������(�ܵ�)-ɽ����ҥ�����->ɽ��̾ done");
            // ������(�ܵ�)-ɽ��̾���ͤ򥻥å�
            $('input[name="CustomerName"]').val(response[0].companydisplayname);
        })
        .fail(function(response){
            console.log("������(�ܵ�)-ɽ����ҥ�����->ɽ��̾ fail");
            console.log(response.responseText);
            // ������(�ܵ�)-ɽ��̾���ͤ�ꥻ�å�
            $(invoker).val('');
            $('input[name="CustomerName"]').val('');
        });
    };

    // --------------------------------------------------------------------------
    // ô�����롼��-ɽ�����롼�ץ����ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ô�����롼��-ɽ�����롼�ץ����ɤ���ɽ��̾�����
    var selectGroupName = function(invoker){
        console.log("ô�����롼��-ɽ�����롼�ץ�����->ɽ��̾ change");

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
            // ������(�ܵ�)-ɽ��̾���ͤ򥻥å�
            $('input[name="KuwagataGroupName"]').val(response[0].groupdisplayname);
        })
        .fail(function(response){
            console.log("ô�����롼��-ɽ�����롼�ץ�����->ɽ��̾ fail");
            console.log(response.responseText);
            // ������(�ܵ�)-ɽ��̾���ͤ�ꥻ�å�
            $(invoker).val('');
            $('input[name="KuwagataGroupName"]').val('');
        });
    };
    // --------------------------------------------------------------------------
    // ô����-ɽ���桼�������ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ô����-ɽ���桼�������ɤ���ɽ��̾�����
    var selectUserName = function(invoker){
        console.log("ô����-ɽ���桼��������->ɽ��̾ change");

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
        .done(function(response){
            console.log("ô����-ɽ���桼��������->ɽ��̾ done");
            // ô����-ɽ��̾���ͤ򥻥å�
            $('input[name="KuwagataUserName"]').val(response[0].userdisplayname);
        })
        .fail(function(response){
            console.log("ô����-ɽ���桼��������->ɽ��̾ fail");
            console.log(response.responseText);
            // ô����-ɽ��̾���ͤ�ꥻ�å�
            $(invoker).val('');
            $('input[name="KuwagataUserName"]').val('');
        });
    };

    // --------------------------------------------------------------------------
    // ����-ɽ����ҥ����ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ����-ɽ����ҥ����ɤ���ɽ��̾�����
    var selectFactoryName =  function(invoker){
        console.log("����-ɽ����ҥ�����->ɽ��̾ change");
        // ������̤Υ��å���CSS���쥯���κ���
        var targetCssSelector = 'input[name="' + $(invoker).attr('name') + 'Name"]';
        // �������0��λ��Υ��������CSS���쥯���κ���
        var targetCodeCssSelector = 'input[name="' + $(invoker).attr('name') +'"]';

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
        .done(function(response){
            console.log("����-ɽ����ҥ�����->ɽ��̾ done");
            // ����-ɽ��̾���ͤ򥻥å�
            $(targetCssSelector).val(response[0].companydisplayname);
        })
        .fail(function(response){
            console.log("����-ɽ����ҥ�����->ɽ��̾ fail");
            console.log(response.responseText);
            // ����-�����ɡ�ɽ��̾���ͤ�ꥻ�åȤ�����������˥ե�������
            $(targetCssSelector).val('');
            $(targetCodeCssSelector).val('').focus();
        });
    };

    // --------------------------------------------------------------------------
    // ��Ͽ��-ɽ���桼�������ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ��Ͽ��-ɽ���桼�������ɤ���ɽ��̾�����
    var selectCreateUserName = function(invoker){
        console.log("��Ͽ��-ɽ���桼��������->ɽ��̾ change");

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
        .done(function(response){
            console.log("��Ͽ��-ɽ���桼��������->ɽ��̾ done");
            // ��Ͽ��-ɽ��̾���ͤ򥻥å�
            $('input[name="CreateByName"]').val(response[0].userdisplayname);
        })
        .fail(function(response){
            console.log("��Ͽ��-ɽ���桼��������->ɽ��̾ fail");
            console.log(response.responseText);
            // ��Ͽ��-ɽ��̾���ͤ�ꥻ�å�
            $(invoker).val('');
            $('input[name="CreateByName"]').val('');
        });
    };

    // --------------------------------------------------------------------------
    // ������-ɽ���桼�������ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ������-ɽ���桼�������ɤ���ɽ��̾�����
    var selectUpdateUserName = function(invoker){
        console.log("������-ɽ���桼��������->ɽ��̾ change");

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
        .done(function(response){
            console.log("������-ɽ���桼��������->ɽ��̾ done");
            // ��Ͽ��-ɽ��̾���ͤ򥻥å�
            $('input[name="UpdateByName"]').val(response[0].userdisplayname);
        })
        .fail(function(response){
            console.log("������-ɽ���桼��������->ɽ��̾ fail");
            console.log(response.responseText);
            // ��Ͽ��-ɽ��̾���ͤ�ꥻ�å�
            $(invoker).val('');
            $('input[name="UpdateByName"]').val('');
        });
    };
})();
