
(function(){

    // �ޥ�����������
    var searchMaster = {
                    url: '/mold/lib/queryMasterData.php?strSessionID=' + $.cookie('strSessionID'),
                    type: 'post',
                    dataType: 'json'
                };

    
    // ������-ɽ����ҥ����� ���٥����Ͽ
    $('input[name="lngCustomerCode"]').on({
        'change': function(){
            // ɽ��̾�����
            selectCustomerName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե���������ɽ�����̾�˹�碌��
            $(this).next('input').focus();
        }
    });

    // Ǽ�ʹ���-ɽ����ҥ����� ���٥����Ͽ
    $('input[name="lngLocationCode"]').on({
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
    // ������-ɽ����ҥ����ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ������-ɽ����ҥ����ɤ���ɽ��̾�����
    var selectCustomerName = function(invoker){
        console.log("������-ɽ����ҥ�����->ɽ��̾ change");

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
            console.log("������-ɽ����ҥ�����->ɽ��̾ done");
            // ������-ɽ��̾���ͤ򥻥å�
            $('input[name="strCustomerName"]').val(response[0].companydisplayname);
        })
        .fail(function(response){
            console.log("������-ɽ����ҥ�����->ɽ��̾ fail");
            console.log(response.responseText);
            // ������-ɽ��̾���ͤ�ꥻ�å�
            $('input[name="strCustomerName"]').val('');
        });
    };

    // --------------------------------------------------------------------------
    // ����-ɽ����ҥ����ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ����-ɽ����ҥ����ɤ���ɽ��̾�����
    var selectFactoryName =  function(invoker){
        console.log("����-ɽ����ҥ�����->ɽ��̾ change");

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
            $('input[name="strLocationName"]').val(response[0].companydisplayname);
        })
        .fail(function(response){
            console.log("����-ɽ����ҥ�����->ɽ��̾ fail");
            console.log(response.responseText);
            // ������-ɽ��̾���ͤ�ꥻ�å�
            $('input[name="strLocationName"]').val('');
        });
    };

})();
