(function () {
    // �ե����ॵ�֥ߥå��޻�
    $('document').on('submit', 'form', function (e) {
        e.preventDefault();
        return false;
    });

    // ToDo  msw.js�����ƤϤۤ�Ʊ���ʤΤ�1�ĤˤޤȤ᤿����
    var apply = function (handleName, docMsw) {
        var code = $('input[name=' + handleName + ']');
        code.val(docMsw.find('select.result-select').find('option:selected').attr('code'));
        // msw����ɽ��
        invokeMswClose(docMsw);
        // �ܵҥ����ɥ����󥸥��٥�ȥ��å�
        code.trigger('change');
        // �ܵҤ��ѹ����줿���
        if (handleName == 'lngCustomerCode') {
            customerChangeReset();
        }
    };

    // �ܵҤ��ѹ����줿�������ٴ�Ϣ��ꥻ�åȤ���
    var customerChangeReset = function () {
        // �������ٰ�������
        $('#btnAllDelete').trigger('click');
        // ��������ĳ�(�����ǹ���)
        $('input[name="curlastmonthbalance"]').val(0).change();
        // ������
        $('input[name="curtaxprice"]').val(0).change();
        // ���������
        $('input[name="curthismonthamount"]').val(0).change();
        // ������׳�
        $('input[name="notaxcurthismonthamount"]').val(0).change();
        // �������η׻�
        btnbilling.trigger('click');
    }

    // �Ĥ���ܥ�������θƤӽФ�
    var invokeMswClose = function (msw) {
        msw.find('.msw-box__header__close-btn').trigger('click');
    };

    // // M�ܥ��󲡲����� (�ܵ��ѹ�)
    // $('img.msw-inv-button').on({
    //     'click': function() {
    //         var msg = '�����оݤ����٤򤹤٤ƥ��ꥢ���ޤ���\n������Ǥ�����';
    //         var cc = isEmpty($('input[name="lngCustomerCode"]').val());
    //         var cn = isEmpty($('input[name="strCustomerName"]').val());
    //         var $tableA_rows = $('#tableA tbody tr');
    //         var $tableA_rows_length = $tableA_rows.length;

    //         var warn = ($tableA_rows_length > 0) ? true : false;

    //         if(warn && window.confirm(msg) === false ) {
    //             return;
    //         }

    //         var mswName = $(this).attr('invokeMSWName');
    //         var ifmMsw = $('iframe.' + mswName);
    //         var docMsw = $(ifmMsw.get(0).contentWindow.document);

    //         // iframe�Υݥ������,����������
    //         // iframe��ɽ���ΰ��ɽ��ʪ(msw-box)�Υ������˹�碌��
    //         var mswBox = docMsw.find('.msw-box');
    //         var ifmHeight = mswBox.outerHeight(true);
    //         if(typeof mswBox.offset() !== 'undefined' ) {
    //             var ifmHeight = mswBox.offset().top + mswBox.outerHeight(true);
    //         }
    //         var ifmWidth = mswBox.offset().top + mswBox.outerWidth(true);
    //         var pos = setPosition(this, docMsw);
    //         ifmMsw.css({
    //             'position': 'absolute',
    //             'top': pos.top,
    //             'left': pos.left,
    //             'height': ifmHeight,
    //             'width': ifmWidth,
    //             'z-index': '9999'
    //         });

    //         var handleName = $(this).prev().prev().attr('name');
    //         ifmMsw.get(0).handler = handleName;
    //         docMsw.off('click', 'img.apply');
    //         docMsw.off('keydown', 'img.apply');
    //         docMsw.on('click', 'img.apply', function() {
    //                 apply(handleName, docMsw);
    //             }
    //         );
    //         docMsw.on('keydown', 'img.apply', function(e){
    //                 if(e.which == 13){
    //                     apply(handleName, docMsw);
    //                 }
    //             }
    //         );

    //         // MSWɽ��ľ���˼¹Ԥ�����������
    //         var mswBrfore = $(this).attr('msw-before');
    //         if(mswBrfore){
    //             eval(mswBrfore + '(handleName);');
    //         }

    //         // msw��ɽ��
    //         invokeMswClose(docMsw);

    //         // �إå���������
    //         var headerWidth = docMsw.find('.msw-box__header').width();
    //         var btnCloseWidth = docMsw.find('.msw-box__header__close-btn').width();
    //         var btnCloseHeight = docMsw.find('.msw-box__header__close-btn').height();
    //         var headerbar = docMsw.find('.msw-box__header__bar');
    //         headerbar.css({
    //             'height': btnCloseHeight,
    //             'width': headerWidth - btnCloseWidth,
    //             'background-color': '#5495c8',
    //             'line-height': btnCloseHeight + 'px',
    //             'color': 'white',
    //             'font-size': '12px',
    //             'font-weight': 'bold',
    //             'text-indent': '1em'
    //         });

    //         // msw��κǽ��input�˥ե�������
    //         docMsw.find('input[tabindex="1"]').focus();
    //     }
    // });

    // // msw��position����
    // var setPosition = function(btn, docMsw) {
    //     // �ܥ���οƤΥ饤��
    //     var line = $(btn).parents('[class*="regist-line"]');
    //     var lineOffset = line.offset();

    //     var mswBox = docMsw.find('.msw-box');
    //     var mswBoxHeight = mswBox.outerHeight(true);
    //     var mswBoxWidth = mswBox.outerWidth(true);
    //     // msw�������
    //     var position = {top: line.position().top + line.height(), left: line.position().left};

    //     // msw��ɽ�������̤˼��ޤ�ʤ����
    //     if(lineOffset.top + line.height() + mswBoxHeight > $(document).height() && $(document).height() > mswBoxHeight){
    //         // ���̤ι⤵�˼��ޤ�ʤ��⤵ʬ�����
    //         position.top -= $('[class^="form-box--"], [class="form-box"]').offset().top + position.top + line.height() + mswBoxHeight - $(document).height();
    //     }

    //     // msw���������̤˼��ޤ�ʤ����
    //     position.left -= Math.min(position.left, (position.left + mswBoxWidth > $(document).width() && $(document).width() > mswBoxWidth)?
    //     Math.abs(position.left + mswBoxWidth - $(document).width()) : 0);

    //     return position;
    // }

    // ������̥��֥륯��å���Ŭ�Ѥ���
    $(".result-select").on("dblclick", function () {
        mswBox.find('img.apply').trigger('click');
        mswBox.find('img.msw-box__header__close-btn').trigger('click');
    });

    // �������ѹ��ν���
    $('input[name="ActionDate"]').on("change", function () {
        selectClosedDay();
    });

    // �ܵҥ������ѹ��ν���
    $('input[name="lngCustomerCode"]').on("change", function () {
        var msg = '�����оݤ����٤򤹤٤ƥ��ꥢ���ޤ���\n������Ǥ�����';
        var $tableA_rows = $('#tableA tbody tr');
        var $tableA_rows_length = $tableA_rows.length;

        var warn = ($tableA_rows_length > 0) ? true : false;

        if (warn && window.confirm(msg) === false) {
            return;
        }

        $tableA_rows.remove();

        selectClosedDay();
    });

    // ���������������
    var isCloseDay;
    var selectClosedDay = function () {

        var customerCode = $('input[name="lngCustomerCode"]');
        var customerName = $('input[name="strCustomerName"]');
        var billingDate = $('input[name="ActionDate"]');
        // ��������̤����
        if (isEmpty(billingDate.val()) == '0') {
            console.log('none ������');
            return;
        }

        switch (isEmpty(customerCode.val()) + isEmpty(customerName.val())) {
            // �ɤ����̤����
            case '00':
                return;
                break;

            // �����줫�����������Ϥ���Ƥ���
            case '01':
            case '10':
            case '11':
                var condition = {
                    data: {
                        QueryName: 'selectClosedDayByCodeAndName',
                        Conditions: {
                            customerCode: customerCode.val(),
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

    // ������̤��鼫/��򻻽Ф����å�
    function setResult(response) {

        console.log(response);
        if (isEmpty(response[0].lngclosedday) == 0) {
            alert('�������μ����˼��Ԥ��ޤ�����');
            return false;
        }

        // ��/������
        var [start, end] = getClosedDay(response[0].lngclosedday);
        // ��/�ꥻ�å�
        var billingStart = $('input[name="dtmchargeternstart"]');
        var billingEnd = $('input[name="dtmchargeternend"]');
        var billingMonth = $('#invoiceMonth');

        billingStart.val(start).change();
        billingEnd.val(end).change();
        // �����å�
        billingMonth.val(end.split('/')[1]).change();
        return true;


    }

    // �������0��λ�option��NoData�򥻥å�
    function setNodata(response) {
        console.log('0��');
        console.log(response.responseText);
    }

    // ���������鼫��򻻽Ф���
    function getClosedDay(close) {
        var billingDate = $('input[name="ActionDate"]');
        var billingStart = $('input[name="dtmchargeternstart"]');
        var billingEnd = $('input[name="dtmchargeternend"]');
        var dateLength = splitDate(billingDate.val());

        // ��������̤����
        if (isEmpty(billingDate.val()) == '0') {
            return [false, false];
        }

        // �������η�������������
        if (dateLength == false) {
            return [false, false];
        }

        // ������
        var yyyy = parseInt(dateLength[1]);
        var mm = parseInt(dateLength[2]);
        var dd = parseInt(dateLength[3]);
        // ������ (close)
        close = parseInt(close);
        isCloseDay = close;
        if (close === 0) {
            // ����������б�
            var date1 = new Date(yyyy, mm, dd);
            date1.setMonth(date1.getMonth() - 2);
            date1.setDate(1);
            var start = date1.getFullYear() + '/' + (date1.getMonth() + 1) + '/' + date1.getDate();
            var date2 = new Date(date1.getFullYear(), date1.getMonth() + 1, 0);
            var end = date2.getFullYear() + '/' + (date2.getMonth() + 1) + '/' + date2.getDate();
        }
        else {
            // �� ������������ <= �������ξ�硢���������������������ʳ��ξ��ϡ�������������            
            var date1 = new Date(yyyy, mm, dd);
            if (dd <= close) {
                date1.setMonth(date1.getMonth() - 1);
                var end = date1.getFullYear() + '/' + date1.getMonth() + '/' + close;
            } else {
                var end = date1.getFullYear() + '/' + date1.getMonth() + '/' + close;
            }

            var date2 = new Date(date1.getFullYear(), date1.getMonth(), close);
            date2.setMonth(date2.getMonth() - 1);
            date2.setDate(date2.getDate() + 1);
            var start = date2.getFullYear() + '/' + date2.getMonth() + '/' + date2.getDate();
        }
        // �ֵ�
        return [start, end];
    }

    // �����������դ�����å�������������С�/�פ�ʬ��
    function splitDate(str) {

        // ���եե����ޥå� yyyy/mm(m)/dd(d)����
        var regDate = /(\d{4})\/(\d{1,2})\/(\d{1,2})/;

        // yyyy/mm/dd������
        if (!(regDate.test(str))) {
            return false;
        }

        // ����ʸ����λ���ʬ��
        var regResult = regDate.exec(str);
        var yyyy = regResult[1];
        var mm = regResult[2];
        var dd = regResult[3];
        var di = new Date(yyyy, mm - 1, dd);
        // ���դ�ͭ���������å�
        if (di.getFullYear() == yyyy && di.getMonth() == mm - 1 && di.getDate() == dd) {
            return regResult;
        }

        return false;
    };

    // �����Υ��쥯��BOX
    function setMonthSelectBox() {
        var today = new Date();
        var mm = today.getMonth() + 1;
        //��
        var month = "<select>";
        for (var i = 1; i <= 12; i++) {
            if (i == mm) {
                month += '<option value=\"' + i + '\" selected >' + i + '</option>';
            } else {
                month += '<option value=\"' + i + '\" >' + i + '</option>';
            }
        }
        month += '</select>';
        $('#invoiceMonth').html(month + "��");
    };
    setMonthSelectBox();

    // Ǽ�ʽ����ٸ����ܥ��󲡲�����
    $('img.search-condition').on({
        'click': function () {
            // validation���å�
            var form = $('form[name="Invoice"]');
            // From/To�����Ϥ���Ƥ���������å�
            let $dtmFrom = $('input[name="From_dtmDeliveryDate"]').val();
            let $dtmTo = $('input[name="To_dtmDeliveryDate"]').val();
            if ($dtmFrom && $dtmTo) {
                let d1 = new Date($dtmFrom);
                let d2 = new Date($dtmTo);
                if (d1 > d2) {
                    alert('Ǽ������TO�ˤ�Ǽ������FROM�ˤ��������Ǥ�');
                    return false;
                }
            }

            // ���ꥢ�����å� (�ܵҥ����ɤޤ����̲ߤޤ��ϲ��Ƕ�ʬ������ͤȰۤʤ���)
            let bReset = false;
            let changeCode = false;
            // �Ƹܵҥ�����
            var parentCustomerCode = window.opener.$('input[name="lngCustomerCode"]').val();
            var parentCustomerName = window.opener.$('input[name="strCustomerName"]').val();
            // �ܵҥ�����
            let customerCode = $('input[name="lngCustomerCode"]').val();
            let customerName = $('input[name="strCustomerName"]').val();


            if (parentCustomerCode != customerCode) {
                bReset = true;
                changeCode = true;
            }

            let $tableB = window.opener.$('#tableB');
            // �ơ��֥�B <tbody>, <tr>
            let $tableB_tbody = $('tbody', $tableB);
            let $tableB_row = $('tr', $tableB_tbody);
            if ($tableB_row.length > 0) {
                for (var i = 0, rowlen = $tableB_row.length; i < rowlen; i++) {
                    for (var j = 0, collen = $tableB_row[i].cells.length; j < collen; j++) {
                        if ($tableB_row[i].cells[j].className == 'taxclass') {
                            // ���Ƕ�ʬ
                            var parentTaxCode = $tableB_row[i].cells[j].innerText.replace(/[^0-9]/g, '');
                        }
                    }
                }

                let taxClassCode = $('select[name="lngTaxClassCode"]').val();
                if (taxClassCode != parentTaxCode) {
                    bReset = true;
                }
            }

            if (form.valid() == false) {
                return;
            }

            // �����ͤ����
            // �ܵ�
            var params = {
                mode: 'ajax',
                strSessionID: $('input[name="strSessionID"]').val(),
                QueryName: 'selectClosedDayByCodeAndName',
                conditions: {
                    customerCode: customerCode,
                    customerName: customerName,
                    strSlipCode: $('input[name="strSlipCode"]').val(),
                    deliveryFrom: $('input[name="From_dtmDeliveryDate"]').val(),
                    deliveryTo: $('input[name="To_dtmDeliveryDate"]').val(),
                    deliveryPlaceCode: $('input[name="lngDeliveryPlaceCode"]').val(),
                    deliveryPlaceName: $('input[name="strDeliveryPlaceName"]').val(),
                    moneyClassCode: $('select[name="lngMoneyClassCode"]').val(),
                    taxClassCode: $('select[name="lngTaxClassCode"]').val(),
                    inChargeUserCode: $('input[name="lngInChargeUserCode"]').val(),
                    inChargeUserName: $('input[name="strInChargeUserName"]').val(),
                    inputUserCode: $('input[name="lngInputUserCode"]').val(),
                    inputUserName: $('input[name="lngInputUserName"]').val()
                }
            }

            // ��������
            var search = {
                url: '/inv/regist/condition.php?strSessionID=' + $.cookie('strSessionID'),
                type: 'post',
                dataType: 'json',
                data: params,
            };

            $.ajax(search)
                .done(function (response) {
                    console.log(response);
                    // �ƥ�����ɥ���¸�ߥ����å�
                    if (!window.opener || window.opener.closed) {
                        // �ƥ�����ɥ���¸�ߤ��ʤ����
                        window.alert('�ᥤ�󥦥���ɥ�����������ޤ���');
                    }
                    else {
                        if (response.Message) {
                            alert(response.Message);
                            return;
                        }
                        var msg = '���򤵤줿���٤����ƥ��ꥢ���ޤ�����������Ǥ�����';
                        var $tableA_rows = window.opener.$('#tableA tbody tr');
                        var $tableA_rows_length = $tableA_rows.length;
                        var warn = ($tableA_rows_length > 0) ? true : false;
                        if (warn && window.confirm(msg) === false) {
                            return;
                        }

                        // �ܵҥ������ѹ�
                        if (changeCode == true) {
                            window.opener.$('input[name="lngCustomerCode"]').val(customerCode).change();
                            window.opener.$('input[name="strCustomerName"]').val(customerName);
                        }
                        // TABLE����
                        window.opener.$.createTable(response);

                        // �ơ��֥�����
                        if (bReset == true) {
                            // �������ٰ�������
                            window.opener.$('#btnAllDelete').trigger('click');
                            // ��������ĳ�(�����ǹ���)
                            window.opener.$('input[name="curlastmonthbalance"]').val(0).change();
                            // ������
                            window.opener.$('input[name="curtaxprice"]').val(0).change();
                            // ���������
                            window.opener.$('input[name="curthismonthamount"]').val(0).change();
                            // ������׳�
                            window.opener.$('input[name="notaxcurthismonthamount"]').val(0).change();
                        }

                        // �̲��ѹ�
                        var lngMoneyClassCode = $('select[name="lngMoneyClassCode"] option:selected').val();
                        if (lngMoneyClassCode == '1') {
                            window.opener.$('span.moneyclass').text("\xA5");
                        } else {
                            window.opener.$('span.moneyclass').text($('select[name="lngMoneyClassCode"] option:selected').text());

                        }


                        window.close();
                    }
                })
                .fail(function (response) {
                    console.log(response);
                    alert('�����˼��Ԥ��ޤ�����\n�����ѹ����Ʋ�������');
                    return;
                });
            return false;
        }
    });

    // PREVIEW�ܥ��󲡲����� (preview)
    $('img.preview-button').on({
        'click': function () {
            // validation���å�
            if ($('form[name="Invoice"]').valid() == false) {
                return;
            }
            // // ��۷׻�
            // billingAmount();
            // �ץ�ӥ塼���̸ƤӽФ� (�ٱ䤵���ʤ���INPUT�����Ǥ��ʤ�)
            var prev = setTimeout(previewDrow, 800);
        }
    });


    var previewDrow = function () {
        tableB = $('#tableB');
        tableB_tbody = $('tbody', $tableB);
        tableB_row = $('tbody tr', $tableB);

        // Ǽ�ʽ��ֹ��������롣slipcode
        var slipCodeList = [];
        // Ǽ�������Ǽ����
        var deliveryDate = [];
        // �ǽ�β��Ƕ�ʬ��������롣
        var taxclass = false;
        // �ǽ����Ψ��������롣
        var tax = false;
        // ��Ψ��Ʊ����������å�����ե饰
        var isSameTax = true;

        for (var i = 0, rowlen = tableB_row.length; i < rowlen; i++) {
            for (var j = 0, collen = tableB_row[i].cells.length; j < collen; j++) {
                if (!tableB_row[i].cells[j].innerText) continue;

                if (tableB_row[i].cells[j].className.substr(0,'slipcode'.length) == 'slipcode') {
                    // Ǽ�ʽ�No
                    slipCodeList.push(tableB_row[i].cells[j].innerText);
                }
                if (tableB_row[i].cells[j].className.substr(0,'tax right'.length) == 'tax right' && !tax) {
                    // ������
                    tax = tableB_row[i].cells[j].innerText;
                    console.log('�����ǥ��å�');
                }
                if (tableB_row[i].cells[j].className.substr(0,'taxclass'.length) == 'taxclass' && !taxclass) {
                    // ��ȴ�����
                    taxclass = tableB_row[i].cells[j].innerText;
                }
                if (tableB_row[i].cells[j].className.substr(0,'deliverydate'.length) == 'deliverydate') {
                    // Ǽ����
                    deliveryDate.push(tableB_row[i].cells[j].innerText);
                }
                if (tableB_row[i].cells[j].className.substr(0,'tax right'.length) == 'tax right' && tax) {
                    console.log('������Ψ�����å�');
                    if (tableB_row[i].cells[j].innerText != tax) {
                        console.log('������NG');
                        isSameTax = false;
                    }
                }
            }
        }

        // ���顼�����å�������ʤ���г�ǧ����ɽ��

        // �ץ�ӥ塼�Х�ǡ����������å�
        // �������ٰ������ꥢ�����٤�1�Ԥ�¸�ߤ��ʤ����
        if (slipCodeList.length === 0) {
            alert('�������٤����򤵤�Ƥ��ޤ���');
            return false;
        }
        // �������ٰ������ꥢ�����򤵤줿Ǽ�ʽ�ξ�����Ψ�����٤�Ʊ��ǤϤʤ����
        if (isSameTax == false) {
            alert('������Ψ�ΰۤʤ�Ǽ�ʽ�����������٤˺��ߤǤ��ޤ���');
            return false;
        }
        // ���������ּ��פ���������դ���ꤹ���硢
        let activeDate = new Date($('input[name="ActionDate"]').val());
        // 2�����ƥ����դȸܵҤ����������顢��������������롣
        let systemDate = new Date();
        if (isCloseDay != 0) {
            if (systemDate.getDate() > isCloseDay) {
                var ternstart = new Date(systemDate.getFullYear(), systemDate.getMonth(), isCloseDay + 1);
            } else {
                systemDate.setMonth(systemDate.getMonth() - 1);
                var ternstart = new Date(systemDate.getFullYear(), systemDate.getMonth(), isCloseDay);
            }
        } else {
            var ternstart = new Date(systemDate.getFullYear(), systemDate.getMonth(), 1);
        }
        if (activeDate < ternstart) {
            alert('���ѤߤΤ��ᡢ���ꤵ�줿��������̵���Ǥ�');
            return false;
        }

        // Ǽ�������ּ��פ�1����������ֻ�פޤǤδ��ְʳ��ʤ����
        let start = new Date($('input[name="dtmchargeternstart"]').val());
        start.setMonth(start.getMonth() - 1);
        let end = new Date($('input[name="dtmchargeternend"]').val());
        let isDeliveryDate = true;

        $.each(deliveryDate, function (i, v) {
            let deliDate = new Date(v);

            if (deliDate < start || deliDate > end) {
                isDeliveryDate = false;
            }
        });

        if (isDeliveryDate == false) {
            alert('�������٤ˤϡ����Ϥ��줿������Ӥ��������Ǽ�ʤ��줿���٤Τ߻��ꤷ�Ƥ�������');
            return false;
        }

        var strMode = $('input[name="strMode"]').val();

        // ��¸�ե�������
        var delold = document.getElementsByName('slipCodeList');
        if( delold.length > 0 )
        {
            for( var i = 0; i < delold.length; i++ ){
                delold[i].parentNode.removeChild(delold[i]);
            }
        }
        delold = document.getElementsByName('mode');
        if( delold.length > 0 )
        {
            for( var i = 0; i < delold.length; i++ ){
                delold[i].parentNode.removeChild(delold[i]);
            }
        }
        delold = document.getElementsByName('taxclass');
        if( delold.length > 0 )
        {
            for( var i = 0; i < delold.length; i++ ){
                delold[i].parentNode.removeChild(delold[i]);
            }
        }
        delold = document.getElementsByName('tax');
        if( delold.length > 0 )
        {
            for( var i = 0; i < delold.length; i++ ){
                delold[i].parentNode.removeChild(delold[i]);
            }
        }
        // �ե������ɲ�
        var ele1 = document.createElement('input');
        // �ǡ���������
        ele1.setAttribute('type', 'hidden');
        ele1.setAttribute('name', 'slipCodeList');
        ele1.setAttribute('value', slipCodeList);
        // ���Ǥ��ɲ�
        document.Invoice.appendChild(ele1);
        // �ե������ɲ�
        var ele2 = document.createElement('input');
        // �ǡ���������
        ele2.setAttribute('type', 'hidden');
        ele2.setAttribute('name', 'mode');
        ele2.setAttribute('value', 'prev');
        // ���Ǥ��ɲ�
        document.Invoice.appendChild(ele2);
        // �ե������ɲ�
        var ele3 = document.createElement('input');
        // �ǡ���������
        ele3.setAttribute('type', 'hidden');
        ele3.setAttribute('name', 'taxclass');
        ele3.setAttribute('value', taxclass);
        // ���Ǥ��ɲ�
        document.Invoice.appendChild(ele3);
        // �ե������ɲ�
        var ele4 = document.createElement('input');
        // �ǡ���������
        ele4.setAttribute('type', 'hidden');
        ele4.setAttribute('name', 'tax');
        ele4.setAttribute('value', tax);
        // ���Ǥ��ɲ�
        document.Invoice.appendChild(ele4);

        var invForm = $('form[name="Invoice"]');

        if (invForm.valid()) {

            var windowName = 'registPreview';
            if (strMode == 'renewPrev') {
                // ����
                url = '/inv/regist/renew.php?strSessionID=' + $('input[name="strSessionID"]').val();
            } else {
                // ��Ͽ
                url = '/inv/regist/index.php?strSessionID=' + $('input[name="strSessionID"]').val();
            }

            // �ե���������
            var windowPrev = open('about:blank', windowName, 'scrollbars=yes, resizable=yes');
            invForm.attr('action', url);
            invForm.attr('method', 'post');
            invForm.attr('target', windowName);


            // ���֥ߥå�
            invForm.submit();

            return false;

        }
        else {
            // �Х�ǡ������Υ��å�
            invForm.find(':submit').click();
        }

        return true;

    }

    // ��Ͽ���� (insert)
    var insertCheck = function () {

        tableB = $('#tableB');
        tableB_tbody = $('tbody', $tableB);
        tableB_row = $('tbody tr', $tableB);

        // Ǽ�ʽ��ֹ��������롣slipcode
        var slipCodeList = [];
        // �ǽ�β��Ƕ�ʬ��������롣
        var taxclass = false;
        // �ǽ����Ψ��������롣
        var tax = false;

        for (var i = 0, rowlen = tableB_row.length; i < rowlen; i++) {
            for (var j = 0, collen = tableB_row[i].cells.length; j < collen; j++) {
                if (!tableB_row[i].cells[j].innerText) continue;

                if (tableB_row[i].cells[j].className == 'slipcode') {
                    // Ǽ�ʽ�No
                    console.log(tableB_row[i].cells[j].className);
                    console.log(tableB_row[i].cells[j].innerText);
                    slipCodeList.push(tableB_row[i].cells[j].innerText);
                }
                if (tableB_row[i].cells[j].className == 'tax right' && !tax) {
                    // ������
                    console.log(tableB_row[i].cells[j].className);
                    console.log(tableB_row[i].cells[j].innerText);
                    tax = tableB_row[i].cells[j].innerText;
                }
                if (tableB_row[i].cells[j].className == 'taxclass' && !taxclass) {
                    // ��ȴ�����
                    console.log(tableB_row[i].cells[j].className);
                    console.log(tableB_row[i].cells[j].innerText);
                    taxclass = tableB_row[i].cells[j].innerText;
                }
            }
        }

        // ���顼�����å�������ʤ���г�ǧ����ɽ��

        // �ե������ɲ�
        var ele1 = document.createElement('input');
        // �ǡ���������
        ele1.setAttribute('type', 'hidden');
        ele1.setAttribute('name', 'slipCodeList');
        ele1.setAttribute('value', slipCodeList);
        // ���Ǥ��ɲ�
        document.Invoice.appendChild(ele1);
        // �ե������ɲ�
        var ele2 = document.createElement('input');
        // �ǡ���������
        ele2.setAttribute('type', 'hidden');
        ele2.setAttribute('name', 'mode');
        ele2.setAttribute('value', 'prev');
        // ���Ǥ��ɲ�
        document.Invoice.appendChild(ele2);
        // �ե������ɲ�
        var ele3 = document.createElement('input');
        // �ǡ���������
        ele3.setAttribute('type', 'hidden');
        ele3.setAttribute('name', 'taxclass');
        ele3.setAttribute('value', taxclass);
        // ���Ǥ��ɲ�
        document.Invoice.appendChild(ele3);
        // �ե������ɲ�
        var ele4 = document.createElement('input');
        // �ǡ���������
        ele4.setAttribute('type', 'hidden');
        ele4.setAttribute('name', 'tax');
        ele4.setAttribute('value', tax);
        // ���Ǥ��ɲ�
        document.Invoice.appendChild(ele4);

        var invForm = $('form[name="Invoice"]');
        if (invForm.valid()) {

            var windowName = 'registPreview';
            url = '/inv/regist/index.php?strSessionID=' + $.cookie('strSessionID');
            // �ե���������
            var windowPrev = open('about:blank', windowName, 'scrollbars=yes, resizable=yes');
            invForm.attr('action', url);
            invForm.attr('method', 'post');
            invForm.attr('target', windowName);
            // ���֥ߥå�
            invForm.submit();
            return false;
        }
        else {
            // �Х�ǡ������Υ��å�
            form.find(':submit').click();
        }
        return true;
    };


    function convertNumber(str) {
        if (str != "" && str != undefined && str != "null") {
            return Number(str).toLocaleString(undefined, {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        } else if (str == "0") {
            return str;
        } else {
            return "";
        }
    }
})();
