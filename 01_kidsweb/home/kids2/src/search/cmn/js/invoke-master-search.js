
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
    // ��Ͽ��-ɽ���桼�������� ���٥����Ͽ
    $('input[name="lngInputUserCode"]').on({
        'change': function(){
            // ɽ��̾�����
            selectCreateUserName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե���������桼��̾�˹�碌��
            $('input[name="lngInputUserName"]').focus();
        }
    });
    // ô�����롼��-ɽ�����롼�ץ����� ���٥����Ͽ
    $('input[name="lngInChargeGroupCode"]').on({
        'change': function(){
            // ɽ��̾�����
            selectGroupName($(this));
            // ���롼�ץ����ɤ����ξ��桼�����͡��������
            if( !$('input[name="lngInChargeGroupCode"]').val() ){
                $('input[name="lngInChargeUserCode"]').val('').change();
            }

            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե��������򥰥롼��̾�˹�碌��
            $('input[name="strInChargeGroupName"]').focus();
        }
    });
    // ô����-ɽ���桼�������� ���٥����Ͽ
    $('input[name="lngInChargeUserCode"]').on({
        'change': function(){
            // ɽ��̾�����
            selectUserName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե���������桼��̾�˹�碌��
            $('input[name="strInChargeUserName"]').focus();
        }
    });
    // ������(�ܵ�)-ɽ����ҥ����� ���٥����Ͽ
    $('input[name="lngCustomerCode"]').on({
        'change': function(){
            // ɽ��̾�����
            selectCustomerName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե��������������̾�˹�碌��
            $('input[name="strCustomerName"]').focus();
        }
    });
    // ��������- ���٥����Ͽ
    $('select[name="lngStockSubjectCode"]').on({
        'change': function(){
            var TargetPull = $('select[name="lngStockItemCode"]')[0];
            var options = TargetPull.options;
            if (TargetPull.hasChildNodes()) {
                while (TargetPull.childNodes.length > 0) {
                    TargetPull.removeChild(TargetPull.firstChild)
                }
            }
            var ItemCodeValue = $('input[name="lngStockItemCodeValue"]')[0].value.split(',,');
            var ItemCodeDisp = $('input[name="lngStockItemCodeDisp"]')[0].value.split(',,');
            var ChangePullValue = $('select[name="lngStockSubjectCode"]')[0].value;
            for (var i = 0; i < ItemCodeValue.length;i++){
                if (ChangePullValue == ItemCodeValue[i].slice(0,ChangePullValue.length)){
                    let op = document.createElement("option");
                    op.value = ItemCodeValue[i];
                    op.text = ItemCodeDisp[i];
                    TargetPull.appendChild(op);
                }
            }
        }
    });

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
            $('input[name="lngInputUserName"]').val(response[0].userdisplayname);
        })
        .fail(function(response){
            console.log("��Ͽ��-ɽ���桼��������->ɽ��̾ fail");
            console.log(response.responseText);
            // ��Ͽ��-ɽ��̾���ͤ�ꥻ�å�
            $(invoker).val('');
            $('input[name="lngInputUserName"]').val('');
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
            $('input[name="strInChargeGroupName"]').val(response[0].groupdisplayname);
        })
        .fail(function(response){
            console.log("ô�����롼��-ɽ�����롼�ץ�����->ɽ��̾ fail");
            console.log(response.responseText);
            // ������(�ܵ�)-ɽ��̾���ͤ�ꥻ�å�
            $(invoker).val('');
            $('input[name="strInChargeGroupName"]').val('');
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
            $('input[name="strInChargeUserName"]').val(response[0].userdisplayname);
        })
        .fail(function(response){
            console.log("ô����-ɽ���桼��������->ɽ��̾ fail");
            console.log(response.responseText);
            // ô����-ɽ��̾���ͤ�ꥻ�å�
            $(invoker).val('');
            $('input[name="strInChargeUserName"]').val('');
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
            $('input[name="strCustomerName"]').val(response[0].companydisplayname);
        })
        .fail(function(response){
            console.log("������(�ܵ�)-ɽ����ҥ�����->ɽ��̾ fail");
            console.log(response.responseText);
            // ������(�ܵ�)-ɽ��̾���ͤ�ꥻ�å�
            $(invoker).val('');
            $('input[name="strCustomerName"]').val('');
        });
    };
})();
