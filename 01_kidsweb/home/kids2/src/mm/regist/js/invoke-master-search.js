
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
    $('input[name="ProductCode"]').on({
        'change': function(){
            
            revisecode = $('input[name="ReviseCode"]').val();
            if (revisecode != "") {
                // ����̾�κ���
                selectProductByCode($(this), revisecode);
                // �ⷿ�ꥹ�Ⱥ���
                selectMoldSelectionListByReviseCode($(this), revisecode);
            } else {
                // ����̾�κ���
                selectProductName($(this));
            }
        }
    });
    

    // �إå����� ���ʥ����� ���٥����Ͽ
    $('input[name="ReviseCode"]').on({
        'change': function () {
            var revisecode = $(this).val();
            var productcode = $('input[name="ProductCode"]');
            if (productcode.val() != "") {
                // ����̾�κ���
                selectProductByCode(productcode, revisecode);
                // �ⷿ�ꥹ�Ⱥ���
                selectMoldSelectionListByReviseCode(productcode, revisecode);
            }
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
    // ���ʥ����ɡ����Υ����ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ���ʥ����ɡ����Υ����ɤ�������̾�Τ򸡺�
    var selectProductByCode = function (invoker, revisecode) {
        console.log("���ʥ�����->����̾�� change");
        // �������
        var condition = {
            data: {
                QueryName: 'selectProductByCode',
                Conditions: {
                    ProductCode: invoker.val(),
                    ReviseCode: revisecode
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log(response);
                console.log(response.length);
                console.log("���ʥ�����->����̾�� done");
                // �إå�����/�ܺ٥��֤����ʥ����ɵڤ�����̾�Τ��ͤ򥻥å�
                $('input[name="ProductCode"]').val(invoker.val());
                $('input[name="ReviseCode"]').val(response[0].revisecode);
                $('input[name="ProductName"]').val(response[0].productname);

                // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
                $('input[name="ProductCode"]').trigger('blur');
                $('input[name="ReviseCode"]').trigger('blur');
                $('input[name="ProductName"]').trigger('blur');
            })
            .fail(function (response) {
                console.log("���ʥ�����->����̾�� fail");
                console.log(response.responseText);
                // �إå�����/�ܺ٥��֤����ʥ����ɵڤ�����̾�Τ��ͤ�ꥻ�å�
                $('input[name="ProductCode"]').val('');
                $('input[name="ReviseCode"]').val('');
                $('input[name="ProductName"]').val('');

                // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
                $('input[name="ProductCode"]').trigger('blur');
                $('input[name="ReviseCode"]').trigger('blur');
                $('input[name="ProductName"]').trigger('blur');
            });
    };
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
            if (response.length == 1) {
                $('input[name="ReviseCode"]').val(response[0].revisecode);
                $('input[name="ProductName"]').val(response[0].productname);
                revisecode = response[0].revisecode;
                // �ⷿ�ꥹ�Ⱥ���
                selectMoldSelectionListByReviseCode(invoker, revisecode);
            }

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

    // ���ʥ����ɤ���ⷿ�ꥹ�Ȥ����
    var selectMoldSelectionListByReviseCode = function(invoker, revisecode){
        console.log("���ʥ�����->�ⷿ�ꥹ�� change");

        // �������
        var condition = {
            data: {
                QueryName: 'selectMoldSelectionListByReviseCode',
                Conditions: {
                    ProductCode: $(invoker).val(),
                    ReviseCode: revisecode
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
            if ($(invoker).attr('name')=="SourceFactory") {
                $('input[name="SourceFactoryName"] + img').css('visibility', 'hidden');
            }
        })
        .fail(function(response){
            console.log("����-ɽ����ҥ�����->ɽ��̾ fail");
            console.log(response.responseText);
            var listlength = $('.mold-selection__choosen-list').find('option').length;
            if ($(invoker).attr('name')=="SourceFactory") {
                if (listlength > 0) {
                    $('input[name="SourceFactoryName"] + img').css('visibility', 'visible');
                } else {
                    $('input[name="SourceFactoryName"] + img').css('visibility', 'hidden');
                }
            }
            // ����-�����ɡ�ɽ��̾���ͤ�ꥻ�åȤ�����������˥ե�������
            $(targetCssSelector).val('');
            $(targetCodeCssSelector).val('').focus();
        });
    };

})();
