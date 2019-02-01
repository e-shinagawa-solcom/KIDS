
(function(){

    // �ޥ�����������
    var searchMaster = {
                    url: '/mold/lib/queryMasterData.php?strSessionID=' + $.cookie('strSessionID'),
                    type: 'post',
                    dataType: 'json'
                };

    // ���������궦��
    var updateQuery = {
                    url: '/mold/lib/execUpdateQuery.php?strSessionID=' + $.cookie('strSessionID'),
                    type: 'post',
                    dataType: 'json'
                };

    // --------------------------------------------------------------------------
    // ���٥����Ͽ
    // --------------------------------------------------------------------------
    // �إå����� ���ʥ����� ���٥����Ͽ
    $('input[name="ProductCode"]').on({
        'change': function(){
            // ����̾�κ���
            selectProductName($(this));
            // �ܵ����ֺ���
            selectGoodsCode($(this));
            // �ⷿ�ꥹ�Ⱥ���
            selectMoldSelectionList($(this));
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

    // --------------------------------------------------------------------------
    // ���ʥ����ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ���ʥ����ɤ�������̾�Τ򸡺�
    var selectProductName = function(invoker){
        console.log("���ʥ�����->����̾�� change");

        // �������
        var condition = {
            data: {
                QueryName: 'selectProductName',
                Conditions: {
                    ProductCode: invoker.val()
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("���ʥ�����->����̾�� done");
            // �إå�����/�ܺ٥��֤����ʥ����ɵڤ�����̾�Τ��ͤ򥻥å�
            $('input[name="ProductCode"]').val(invoker.val());
            $('input[name="ProductName"]').val(response[0].productname);

            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $('input[name="ProductCode"]').trigger('blur');
            $('input[name="ProductName"]').trigger('blur');
        })
        .fail(function(response){
            console.log("���ʥ�����->����̾�� fail");
            console.log(response.responseText);
            // �إå�����/�ܺ٥��֤����ʥ����ɵڤ�����̾�Τ��ͤ�ꥻ�å�
            $('input[name="ProductCode"]').val('');
            $('input[name="ProductName"]').val('');

            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $('input[name="ProductCode"]').trigger('blur');
            $('input[name="ProductName"]').trigger('blur');
        });
    };

    // ���ʥ����ɤ���ܵ����֤����
    var selectGoodsCode = function(invoker){
        console.log("���ʥ�����->�ܵ����� change");

        // �������
        var condition = {
            data: {
                QueryName: 'selectGoodsCode',
                Conditions: {
                    ProductCode: $(invoker).val()
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("���ʥ�����->�ܵ����� done");

            var goodscode = response[0].goodscode;
            // �ܵ����֤����ꤵ��Ƥ�����
            if (goodscode) {
                // �إå�����/�ܺ٥��֤θܵ����֤��ͤ򥻥å�
                $('input[name="GoodsCode"]').val(goodscode);
                // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
                $('input[name="GoodsCode"]').trigger('blur');
            }
            else {
                var newgoodscode = window.prompt('�ܵ����֤����Ϥ��Ƥ���������(Ⱦ�ѱѿ��Τ�)', '');

                if (newgoodscode) {
                    if (newgoodscode.match(/[^A-Za-z0-9]+/)) {
                        window.alert('�ܵ����֤�Ⱦ�ѱѿ������Ϥ��Ƥ���������');
                    }
                    else {
                        // �������
                        var condition = {
                            data: JSON.stringify({
                                QueryName: 'updateGoodsCode',
                                Conditions: {
                                    ProductCode: $(invoker).val(),
                                    GoodsCode: newgoodscode
                                }
                            })
                        };

                        // �ꥯ����������
                        $.ajax($.extend({}, updateQuery, condition))
                        .done(function(response) {
                            window.alert('�ܵ����֤򹹿����ޤ�����');
                            $(invoker).change();
                        })
                        .fail(function(response) {
                            window.alert(response.responseText);
                        });
                    }
                }
            }
        })
        .fail(function(response){
            console.log("���ʥ�����->�ܵ����� fail");
            console.log(response.responseText);
            // �إå�����/�ܺ٥��֤θܵ����֤��ͤ�ꥻ�å�
            $('input[name="GoodsCode"]').val('');
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $('input[name="GoodsCode"]').trigger('blur');
        });
    };

    // ���ʥ����ɤ���ⷿ�ꥹ�Ȥ����
    var selectMoldSelectionList = function(invoker){
        console.log("���ʥ�����->�ⷿ�ꥹ�� change");

        // �������
        var condition = {
            data: {
                QueryName: 'selectMoldSelectionListForModify',
                Conditions: {
                    ProductCode: $(invoker).val(),
                    MoldReportId: $.cookie('MoldReportId')
                }
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
            moldChoosenList.find('option').remove();

            // �������ʬ����
            $.each(response, function(index, row){
                // OPTION���Ǻ���
                moldList.append(
                    $('<option>')
                        .val(row.moldno)
                        .attr('displaycode', row.companydisplaycode)
                        .html(row.moldno + ' : ' + '[' + row.companydisplaycode + ']' + ' '+ row.companydisplayname)
                );
            });

            // ���������ѥ��٥�ȥ��å�
            moldList.trigger('load-completed');
        })
        .fail(function(response){
            console.log("���ʥ�����->�ⷿ�ꥹ�� fail");
            console.log(response.responseText);

            // �ⷿ���쥯�ȥܥå����μ���
            var moldList = $('.mold-selection__list');
            var moldChoosenList = $('.mold-selection__choosen-list');

            // ��¸OPTION���Ǥκ��
            moldList.find('option').remove();
            moldChoosenList.find('option').remove();
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
            $('input[name="KuwagataUserName"]').val('');
        });
    };

    // --------------------------------------------------------------------------
    // ����-ɽ����ҥ����ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ����-ɽ����ҥ����ɤ���ɽ��̾�����
    var selectFactoryName =  function(invoker){
        console.log("����-ɽ����ҥ�����->ɽ��̾ change value=" + $(invoker).val());
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

})();
