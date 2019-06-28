
(function(){

    // �ޥ�����������
    var searchMaster = {
        url: '/cmn/querydata.php?strSessionID=' + $.cookie('strSessionID'),
        type: 'post',
                    dataType: 'json'
                };

    // --------------------------------------------------------------------------
    // ���٥����Ͽ
    // --------------------------------------------------------------------------
    // ��Ͽ��-ɽ���桼�������� ���٥����Ͽ
    $('input[name="payfCode"]').on({
        'change': function(){
            // ɽ��̾�����
            selectPayfByCode($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե���������桼��̾�˹�碌��
            $('input[name="payfCode"]').focus();
        }
    });

    // --------------------------------------------------------------------------
    // ��ʧ�����-��ʧ��CD�ˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ��ʧ�����-��ʧ��CD�����ʧ������̾�Τ����
    var selectPayfByCode = function(invoker){
        console.log("��ʧ�����-��ʧ��CD->��ʧ������̾�� change");
        // �������
        var condition = {
            data: {
                QueryName: 'selectPayfByCode',
                Conditions: {
                    payfCode: invoker.val()
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("��ʧ�����-��ʧ��CD->��ʧ������̾�� done");
            // ��ʧ�����-��ʧ������̾�Τ��ͤ򥻥å�
            $('input[name="payfName"]').val(response[0].payfdisplayname);
        })
        .fail(function(response){
            console.log("��ʧ�����-��ʧ��CD->��ʧ������̾�� fail");
            console.log(response.responseText);
            // ��ʧ�����-��ʧ������̾�Τ��ͤ�ꥻ�å�
            $(invoker).val('');
            $('input[name="payfName"]').val('');
        });
    };
})();
