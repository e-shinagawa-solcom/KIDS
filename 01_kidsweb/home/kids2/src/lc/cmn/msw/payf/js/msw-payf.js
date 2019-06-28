(function() {
    var mswBox = $('.msw-box');
    var payfCode = mswBox.find('.input-code');
    var payfName = mswBox.find('.input-name');
    var btnSearch = mswBox.find('.search-btn img');

    // TabKey������
    mswBox.on(
        'keydown', 'input.input-code', function(e){
            // Tab + shift��msw�κǸ�����Ǥ˥ե����������᤹
            if(e.keyCode == 9 && e.shiftKey){
                mswBox.find('img.apply').focus();
                return false;
            }
        }
    );
    mswBox.on(
        'keydown', 'img.apply', function(e){
            // Tab�Τߤ�msw�κǽ�����Ǥ˥ե����������᤹
            if(e.keyCode == 9 && !e.shiftKey){
                mswBox.find('input.input-code').focus();
                return false;
            }
        }
    );

    // ������̥��֥륯��å���Ŭ�Ѥ���
    $(".result-select").on("dblclick",  function(){
        mswBox.find('img.apply').trigger('click');
        mswBox.find('img.msw-box__header__close-btn').trigger('click');
    });
    
    // �����ܥ��󲡲����ν���
    btnSearch.on({
        // ����å�
        'click': function() {
            selectpayfs();
        },
        // EnterKey
        'keypress': function(e) {
            if(e.which == 13){
                selectpayfs();
            }
        }
    });
    var selectpayfs = function() {
        $('select').find('option').remove();
        switch (isEmpty(payfCode.val()) + isEmpty(payfName.val())) {
            // �ɤ����̤����
            case '00':
                var condition = {
                    data: {
                        QueryName: 'selectPayfs'
                    }
                };
                break;
            // ��ʧ��̾�ΤΤ�����
            case '01':
                var condition = {
                    data: {
                        QueryName: 'selectPayfByPayfName',
                        Conditions: {
                            payfName: payfName.val()
                        }
                    }
                };
                break;
            // ��ʧ�襳���ɤΤ�����
            case '10':
                var condition = {
                    data: {
                        QueryName: 'selectPayfByCode',
                        Conditions: {
                            payfCode: payfCode.val()
                        }
                    }
                };
                break;
            // �ɤ��������
            case '11':
                var condition = {
                    data: {
                        QueryName: 'selectPayfByCodeAndName',
                        Conditions: {
                            payfCode: payfCode.val(),
                            payfName: payfName.val()
                        }
                    }
                };
                break;
            default:
                break;
        }
        // �ޥ����������¹�
        queryMasterData(condition, setResult, setNodata);
    };

    // �����ͤ�ʸ����ɽ�������
    function isEmpty(val) {
        if (val) {
            return '1';
        } else {
            return '0';
        }
    }

    // ������̤�select��option���Ǥ˥��å�
    function setResult(response) {
        // ��������򥫥��󥿡��˥��å�
        $('.result-count .counter').val(response.length);
        $.each(response, function() {
            $('.result-select').append(
                $('<option>')
                .attr({
                    code: this.payfdisplaycode,
                    name: this.payfdisplayname
                })
                .html(this.payfdisplaycode + '&nbsp;&nbsp;&nbsp;' + this.payfdisplayname)
            );
        });
    }

    // �������0��λ�option��NoData�򥻥å�
    function setNodata(response){
        console.log(response.responseText);
        // ��������ꥻ�å�
        $('.result-count .counter').val('');
        $('.result-select').append(
            $('<option>')
                .attr('disabled','disabled')
                .html('(No&nbsp;&nbsp;Data)')
        );
    }

    function queryMasterData(condition, procDone, procFail) {

        // �ޥ�����������
        var searchMaster = {
                        url: '/cmn/querydata.php?strSessionID=' + $.cookie('strSessionID'),
                        type: 'post',
                        dataType: 'json'
                    };
    
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            procDone(response);
        })
        .fail(function(response){
            alert(searchMaster);
            procFail(response);
        });
    }
})();
