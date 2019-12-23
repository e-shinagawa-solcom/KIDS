
(function () {

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
        'change': function () {
            revisecode = $('input[name="ReviseCode"]').val();
            if (revisecode != "") {
                // ����̾�κ���
                selectProductByCode($(this), revisecode);
                // �ܵ����ֺ���
                selectGoodsCode($(this), revisecode);
                // ������(�ܵ�)-ɽ����ҥ����ɺ���
                selectCustomerByProductCode($(this), revisecode);
                // ô�����롼��-ɽ�����롼�ץ����ɺ���
                selectGroupByProductCode($(this), revisecode);
                // ô����-ɽ���桼�������ɺ���
                selectUserByProductCode($(this), revisecode);
                // �ⷿ�ꥹ�Ⱥ���
                selectMoldSelectionListByReviseCode($(this), revisecode);
            } else {
                // ����̾�κ���
                selectProductName($(this));
            }
            console.log(revisecode);
            if (revisecode != "") {
                console.log("���Υ����ɡ�" + revisecode);
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
                // �ܵ����ֺ���
                selectGoodsCode(productcode, revisecode);
                // ������(�ܵ�)-ɽ����ҥ����ɺ���
                selectCustomerByProductCode(productcode, revisecode);
                // ô�����롼��-ɽ�����롼�ץ����ɺ���
                selectGroupByProductCode(productcode, revisecode);
                // ô����-ɽ���桼�������ɺ���
                selectUserByProductCode(productcode, revisecode);
                // �ⷿ�ꥹ�Ⱥ���
                selectMoldSelectionListByReviseCode(productcode, revisecode);
            }
        }
    });
    // ������(�ܵ�)-ɽ����ҥ����� ���٥����Ͽ
    $('input[name="CustomerCode"]').on({
        'change': function () {
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
        'change': function () {
            // ɽ��̾�����
            selectGroupName($(this));
            // ���롼�ץ����ɤ����ξ��桼�����͡��������
            if (!$('input[name="KuwagataGroupCode"]').val()) {
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
        'change': function () {
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
        'change': function () {
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
    var selectProductName = function (invoker) {
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
            .done(function (response) {
                console.log(response);
                console.log(response.length);
                console.log("���ʥ�����->����̾�� done");
                // �إå�����/�ܺ٥��֤����ʥ����ɵڤ�����̾�Τ��ͤ򥻥å�
                $('input[name="ProductCode"]').val(invoker.val());
                if (response.length == 1) {
                    $('input[name="ReviseCode"]').val(response[0].revisecode);
                    $('input[name="ProductName"]').val(response[0].productname);
                    revisecode = response[0].revisecode;
                    // �ܵ����ֺ���
                    selectGoodsCode(invoker, revisecode);
                    // ������(�ܵ�)-ɽ����ҥ����ɺ���
                    selectCustomerByProductCode(invoker, revisecode);
                    // ô�����롼��-ɽ�����롼�ץ����ɺ���
                    selectGroupByProductCode(invoker, revisecode);
                    // ô����-ɽ���桼�������ɺ���
                    selectUserByProductCode(invoker, revisecode);
                    // �ⷿ�ꥹ�Ⱥ���
                    selectMoldSelectionListByReviseCode(invoker, revisecode);
                }

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

    // ���ʥ����ɤ���ܵ����֤����
    var selectGoodsCode = function (invoker, revisecode) {
        console.log("���ʥ�����->�ܵ����� change");

        // �������
        var condition = {
            data: {
                QueryName: 'selectGoodsCode',
                Conditions: {
                    ProductCode: $(invoker).val(),
                    ReviseCode: revisecode
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
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
                    // ���ϥ�������ɽ��
                    var newgoodscode = window.prompt('�ܵ����֤����Ϥ��Ƥ���������(Ⱦ�ѱѿ��Τ�)', '');

                    // ����󥻥벡�������å�
                    if (!newgoodscode) {
                        // ��å���������
                        window.alert('���ʥ����ɤ�ɳ�դ��ܵ����֤�ɬ�ܹ��ܤǤ���');
                        return;
                    }

                    // ���ϥ����å�
                    if (!newgoodscode.match(/^[A-Za-z0-9]{1,10}$/)) {
                        window.alert('�ܵ����֤�Ⱦ�ѱѿ�����10ʸ����������Ϥ��Ƥ���������');
                        $(invoker).change();
                        return;
                    }

                    // �������
                    var condition = {
                        data: JSON.stringify({
                            QueryName: 'updateGoodsCode',
                            Conditions: {
                                ProductCode: $(invoker).val(),
                                ReviseCode: revisecode,
                                GoodsCode: newgoodscode
                            }
                        })
                    };

                    // �ꥯ����������
                    $.ajax($.extend({}, updateQuery, condition))
                        .done(function (response) {
                            window.alert('�ܵ����֤򹹿����ޤ�����');
                            $(invoker).change();
                        })
                        .fail(function (response) {
                            window.alert('�ܵ����֤ι����˼��Ԥ��ޤ�����');
                            $(invoker).change();
                        });
                }
            })
            .fail(function (response) {
                console.log("���ʥ�����->�ܵ����� fail");
                console.log(response.responseText);
                // �إå�����/�ܺ٥��֤θܵ����֤��ͤ�ꥻ�å�
                $('input[name="GoodsCode"]').val('');
                // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
                $('input[name="GoodsCode"]').trigger('blur');
            });
    };

    // ���ʥ����ɤ�����ȼ�(�ܵ�)-ɽ����ҥ����ɤ����
    var selectCustomerByProductCode = function (invoker, revisecode) {
        console.log("���ʥ�����->������(�ܵ�)-ɽ����ҥ����� change");

        // �������
        var condition = {
            data: {
                QueryName: 'selectCustomerByProductCode',
                Conditions: {
                    ProductCode: $(invoker).val(),
                    ReviseCode: revisecode
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("���ʥ�����->������(�ܵ�)-ɽ����ҥ����� done");
                // ������(�ܵ�)���ͤ򥻥å�
                $('input[name="CustomerCode"]').val(response[0].companydisplaycode);
                // ������(�ܵ�)-ɽ��̾�κ������å�
                $('input[name="CustomerCode"]').trigger('change');
            })
            .fail(function (response) {
                console.log("���ʥ�����->������(�ܵ�)-ɽ����ҥ����� fail");
                console.log(response.responseText);
                // ������(�ܵ�)��ꥻ�å�
                $('input[name="CustomerCode"]').val('');
                // ������(�ܵ�)-ɽ��̾�κ������å�
                $('input[name="CustomerCode"]').trigger('change');
            });
    };

    // ���ʥ����ɤ���ô�����롼��-ɽ�����롼�ץ����ɤ����
    var selectGroupByProductCode = function (invoker, revisecode) {
        console.log("���ʥ�����->ô�����롼��-ɽ�����롼�ץ����� change");

        // �������
        var condition = {
            data: {
                QueryName: 'selectGroupByProductCode',
                Conditions: {
                    ProductCode: $(invoker).val(),
                    ReviseCode: revisecode
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("���ʥ�����->ô�����롼��-ɽ�����롼�ץ����� done");
                // ô�����롼�פ��ͤ򥻥å�
                $('input[name="KuwagataGroupCode"]').val(response[0].groupdisplaycode);
                // ô�����롼��-ɽ��̾�κ������å�
                $('input[name="KuwagataGroupCode"]').trigger('change');
            })
            .fail(function (response) {
                console.log("���ʥ�����->ô�����롼��-ɽ�����롼�ץ����� fail");
                console.log(response.responseText);
                // ô�����롼�פ�ꥻ�å�
                $('input[name="KuwagataGroupCode"]').val('');
                // ô�����롼��-ɽ��̾�κ������å�
                $('input[name="KuwagataGroupCode"]').trigger('change');
            });
    };

    // ���ʥ����ɤ���ô����-ɽ���桼�������ɤ����
    var selectUserByProductCode = function (invoker, revisecode) {
        console.log("���ʥ�����->ô����-ɽ���桼�������� change");

        // �������
        var condition = {
            data: {
                QueryName: 'selectUserByProductCode',
                Conditions: {
                    ProductCode: $(invoker).val(),
                    ReviseCode: revisecode
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("���ʥ�����->ô����-ɽ���桼�������� done");
                // ô���Ԥ��ͤ򥻥å�
                $('input[name="KuwagataUserCode"]').val(response[0].userdisplaycode);
                // ô����-ɽ��̾�κ������å�
                $('input[name="KuwagataUserCode"]').trigger('change');
            })
            .fail(function (response) {
                console.log("���ʥ�����->ô����-ɽ���桼�������� fail");
                console.log(response.responseText);
                // ô���Ԥ�ꥻ�å�
                $('input[name="KuwagataUserCode"]').val('');
                // ô����-ɽ��̾�κ������å�
                $('input[name="KuwagataUserCode"]').trigger('change');
            });
    };

    // ���ʥ����ɤ���ⷿ�ꥹ�Ȥ����
    var selectMoldSelectionListByReviseCode = function (invoker, revisecode) {
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
            .done(function (response) {
                console.log("���ʥ�����->�ⷿ�ꥹ�� done");

                // �ⷿ���쥯�ȥܥå����μ���
                var moldList = $('.mold-selection__list');
                var moldChoosenList = $('.mold-selection__choosen-list');

                // ��¸OPTION���Ǥκ��
                moldList.find('option').remove();
                moldChoosenList.find('option').remove();

                // �������ʬ����
                $.each(response, function (index, row) {
                    // OPTION���Ǻ���
                    moldList.append(
                        $('<option>')
                            .val(row.moldno)
                            .attr('displaycode', row.companydisplaycode)
                            .html(row.moldno + ' : ' + '[' + row.companydisplaycode + ']' + ' ' + row.companydisplayname)
                    );
                });
            })
            .fail(function (response) {
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
    var selectCustomerName = function (invoker) {
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
            .done(function (response) {
                console.log("������(�ܵ�)-ɽ����ҥ�����->ɽ��̾ done");
                // ������(�ܵ�)-ɽ��̾���ͤ򥻥å�
                $('input[name="CustomerName"]').val(response[0].companydisplayname);
            })
            .fail(function (response) {
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
    var selectGroupName = function (invoker) {
        console.log("ô�����롼��-ɽ�����롼�ץ�����->ɽ��̾ change");
        // ɽ���ե饰���¤μ���
        var displayFlagLimit = $(invoker).attr('displayFlagLimit');
        if (displayFlagLimit == '0') {
            displayFlagLimit0 = true;
            displayFlagLimit1 = false;
        } else {
            displayFlagLimit0 = true;
            displayFlagLimit1 = true;
        } 

        // �������
        var condition = {
            data: {
                QueryName: 'selectGroupName',
                Conditions: {
                    GroupDisplayName: $(invoker).val(),
                    displayFlagLimit0: displayFlagLimit0,
                    displayFlagLimit1: displayFlagLimit1
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("ô�����롼��-ɽ�����롼�ץ�����->ɽ��̾ done");
                // ������(�ܵ�)-ɽ��̾���ͤ򥻥å�
                $('input[name="KuwagataGroupName"]').val(response[0].groupdisplayname);
            })
            .fail(function (response) {
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
    var selectUserName = function (invoker) {
        console.log("ô����-ɽ���桼��������->ɽ��̾ change");
        // ɽ���ե饰���¤μ���
        var displayFlagLimit = $(invoker).attr('displayFlagLimit');
        if (displayFlagLimit == '0') {
            displayFlagLimit0 = true;
            displayFlagLimit1 = false;
        } else {
            displayFlagLimit0 = true;
            displayFlagLimit1 = true;
        } 

        // �������
        var condition = {
            data: {
                QueryName: 'selectInChargeUserName',
                Conditions: {
                    UserDisplayName: $(invoker).val(),
                    displayFlagLimit0: displayFlagLimit0,
                    displayFlagLimit1: displayFlagLimit1
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("ô����-ɽ���桼��������->ɽ��̾ done");
                // ô����-ɽ��̾���ͤ򥻥å�
                $('input[name="KuwagataUserName"]').val(response[0].userdisplayname);
            })
            .fail(function (response) {
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
    var selectFactoryName = function (invoker) {
        console.log("����-ɽ����ҥ�����->ɽ��̾ change");
        // ������̤Υ��å���CSS���쥯���κ���
        var targetCssSelector = 'input[name="' + $(invoker).attr('name') + 'Name"]';
        // �������0��λ��Υ��������CSS���쥯���κ���
        var targetCodeCssSelector = 'input[name="' + $(invoker).attr('name') + '"]';

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
                if ($(invoker).attr('name')=="SourceFactory") {
                    $('input[name="SourceFactoryName"] + img').css('visibility', 'hidden');
                }
            })
            .fail(function (response) {
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
