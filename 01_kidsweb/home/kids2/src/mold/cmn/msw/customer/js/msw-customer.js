(function() {
    var mswBox = $('.msw-box');
    var customerCode = mswBox.find('.input-code');
    var customerName = mswBox.find('.input-name');
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
            selectcustomers();
        },
        // EnterKey
        'keypress': function(e) {
            if(e.which == 13){
                selectcustomers();
            }
        }
    });
    var selectcustomers = function() {
        $('select').find('option').remove();
        switch (isEmpty(customerCode.val()) + isEmpty(customerName.val())) {
            // �ɤ����̤����
            case '00':
                var condition = {
                    data: {
                        QueryName: 'selectCustomers'
                    }
                };
                break;
            // ����̾�ΤΤ�����
            case '01':
                var condition = {
                    data: {
                        QueryName: 'selectCustomerByCustomerName',
                        Conditions: {
                            customerName: customerName.val()
                        }
                    }
                };
                break;
            // ���ʥ����ɤΤ�����
            case '10':
                var condition = {
                    data: {
                        QueryName: 'selectCustomerByCustomerCode',
                        Conditions: {
                            customerCode: customerCode.val()
                        }
                    }
                };
                break;
            // �ɤ��������
            case '11':
                var condition = {
                    data: {
                        QueryName: 'selectCustomerByCodeAndName',
                        Conditions: {
                            customerCode: customerCode.val(),
                            customerName: customerName.val()
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
                    code: this.customerdisplaycode,
                    name: this.customerdisplayname
                })
                .html(this.customerdisplaycode + '&nbsp;&nbsp;&nbsp;' + this.customerdisplayname)
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
})();
