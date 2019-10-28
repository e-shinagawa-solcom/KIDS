(function() {
    // �ե����ॵ�֥ߥå��޻�
    $('document').on('submit', 'form', function(e){
        e.preventDefault();
        return false;
    });

    // ToDo  msw.js�����ƤϤۤ�Ʊ���ʤΤ�1�ĤˤޤȤ᤿����
    var apply = function(handleName, docMsw){
        var code = $('input[name=' + handleName + ']');
        code.val(docMsw.find('select.result-select').find('option:selected').attr('code'));
        // msw����ɽ��
        invokeMswClose(docMsw);
        // �ܵҥ����ɥ����󥸥��٥�ȥ��å�
        code.trigger('change');
        // �ܵҤ��ѹ����줿���
        if(handleName == 'lngCustomerCode')
        {
            customerChangeReset();
        }
    };

    // �ܵҤ��ѹ����줿�������ٴ�Ϣ��ꥻ�åȤ���
    var customerChangeReset = function(){
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
    var invokeMswClose = function(msw){
        msw.find('.msw-box__header__close-btn').trigger('click');
    };

    // M�ܥ��󲡲����� (�ܵ��ѹ�)
    $('img.msw-inv-button').on({
        'click': function() {
            var msg = '�����оݤ����٤򤹤٤ƥ��ꥢ���ޤ���\n������Ǥ�����';
            var cc = isEmpty($('input[name="lngCustomerCode"]').val());
            var cn = isEmpty($('input[name="strCustomerName"]').val());
            var warn = (cc == 1 || cn == 1) ? true : false;

            if(warn && window.confirm(msg) === false ) {
                return;
            }

            var mswName = $(this).attr('invokeMSWName');
            var ifmMsw = $('iframe.' + mswName);
            var docMsw = $(ifmMsw.get(0).contentWindow.document);

            // iframe�Υݥ������,����������
            // iframe��ɽ���ΰ��ɽ��ʪ(msw-box)�Υ������˹�碌��
            var mswBox = docMsw.find('.msw-box');
            var ifmHeight = mswBox.outerHeight(true);
            if(typeof mswBox.offset() !== 'undefined' ) {
                var ifmHeight = mswBox.offset().top + mswBox.outerHeight(true);
            }
            var ifmWidth = mswBox.offset().top + mswBox.outerWidth(true);
            var pos = setPosition(this, docMsw);
            ifmMsw.css({
                'position': 'absolute',
                'top': pos.top,
                'left': pos.left,
                'height': ifmHeight,
                'width': ifmWidth,
                'z-index': '9999'
            });

            var handleName = $(this).prev().prev().attr('name');
            ifmMsw.get(0).handler = handleName;
            docMsw.off('click', 'img.apply');
            docMsw.off('keydown', 'img.apply');
            docMsw.on('click', 'img.apply', function() {
                    apply(handleName, docMsw);
                }
            );
            docMsw.on('keydown', 'img.apply', function(e){
                    if(e.which == 13){
                        apply(handleName, docMsw);
                    }
                }
            );

            // MSWɽ��ľ���˼¹Ԥ�����������
            var mswBrfore = $(this).attr('msw-before');
            if(mswBrfore){
                eval(mswBrfore + '(handleName);');
            }

            // msw��ɽ��
            invokeMswClose(docMsw);

            // �إå���������
            var headerWidth = docMsw.find('.msw-box__header').width();
            var btnCloseWidth = docMsw.find('.msw-box__header__close-btn').width();
            var btnCloseHeight = docMsw.find('.msw-box__header__close-btn').height();
            var headerbar = docMsw.find('.msw-box__header__bar');
            headerbar.css({
                'height': btnCloseHeight,
                'width': headerWidth - btnCloseWidth,
                'background-color': '#5495c8',
                'line-height': btnCloseHeight + 'px',
                'color': 'white',
                'font-size': '12px',
                'font-weight': 'bold',
                'text-indent': '1em'
            });

            // msw��κǽ��input�˥ե�������
            docMsw.find('input[tabindex="1"]').focus();
        }
    });

    // msw��position����
    var setPosition = function(btn, docMsw) {
        // �ܥ���οƤΥ饤��
        var line = $(btn).parents('[class*="regist-line"]');
        var lineOffset = line.offset();

        var mswBox = docMsw.find('.msw-box');
        var mswBoxHeight = mswBox.outerHeight(true);
        var mswBoxWidth = mswBox.outerWidth(true);
        // msw�������
        var position = {top: line.position().top + line.height(), left: line.position().left};

        // msw��ɽ�������̤˼��ޤ�ʤ����
        if(lineOffset.top + line.height() + mswBoxHeight > $(document).height() && $(document).height() > mswBoxHeight){
            // ���̤ι⤵�˼��ޤ�ʤ��⤵ʬ�����
            position.top -= $('[class^="form-box--"], [class="form-box"]').offset().top + position.top + line.height() + mswBoxHeight - $(document).height();
        }

        // msw���������̤˼��ޤ�ʤ����
        position.left -= Math.min(position.left, (position.left + mswBoxWidth > $(document).width() && $(document).width() > mswBoxWidth)?
        Math.abs(position.left + mswBoxWidth - $(document).width()) : 0);

        return position;
    }

    // ������̥��֥륯��å���Ŭ�Ѥ���
    $(".result-select").on("dblclick",  function(){
        mswBox.find('img.apply').trigger('click');
        mswBox.find('img.msw-box__header__close-btn').trigger('click');
    });

    $('.TxtStyle05L.billing-date.hasDatepicker').on("change", function(){
            selectClosedDay();
    });
    // �������ܥ��󲡲����ν���
    var btnbilling = $('.billing-button').find('img');
    btnbilling.on({
        // ����å�
        'click': function() {
            selectClosedDay();
        },
        // EnterKey
        'keypress': function(e) {
           if(e.which == 13){
               selectClosedDay();
            }
        }
    });

    // ���������������
    var isCloseDay;
    var selectClosedDay = function() {
        var customerCode = $('input[name="lngCustomerCode"]');
        var customerName = $('input[name="strCustomerName"]');
        var billingDate  = $('input[name="ActionDate"]');

        // ��������̤����
        if(isEmpty(billingDate.val()) == '0') {
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
//        console.log(response);
        if (isEmpty(response[0].lngclosedday) == 0) {
            alert('�������μ����˼��Ԥ��ޤ�����');
            return false;
        }

        // ��/������
        var [start , end ] = getClosedDay(response[0].lngclosedday);
        // ��/�ꥻ�å�
        var billingStart = $('input[name="dtmchargeternstart"]');
        var billingEnd   = $('input[name="dtmchargeternend"]');
        billingStart.val(start).change();
        billingEnd.val(end).change();
        // �����å�
        var billingMonth = $('input[name="From_strInvoiceMonth"]');
        billingMonth.val(end.split('/')[1]).change();
        return true;


    }

    // �������0��λ�option��NoData�򥻥å�
    function setNodata(response){
        console.log('0��');
        console.log(response.responseText);
    }

    // ���������鼫��򻻽Ф���
    function getClosedDay(close) {
        var billingDate  = $('input[name="ActionDate"]');
        var billingStart = $('input[name="dtmchargeternstart"]');
        var billingEnd   = $('input[name="dtmchargeternend"]');
        var dateLength   = splitDate(billingDate.val());

        // ��������̤����
        if(isEmpty(billingDate.val()) == '0') {
            return [false, false];
        }

        // �������η�������������
        if(dateLength == false) {
            return [false, false];
        }

        // ������
        var yyyy = parseInt(dateLength[1]);
        var mm   = parseInt(dateLength[2]);
        var dd   = parseInt(dateLength[3]);
        // ������ (close)
        close = parseInt(close);
        isCloseDay = close;
        if( close === 0 )
        {
	        // ����������б�
	        var startDate = new Date(yyyy, mm - 1, 1);
	        var start = startDate.getFullYear() + '/' + (startDate.getMonth()+1) + '/' + startDate.getDate();
	        var endDate = new Date(yyyy, mm, 1);
	        endDate.setDate(endDate.getDate() - 1);
	        var end = endDate.getFullYear() + '/' + (endDate.getMonth()+1) + '/' + endDate.getDate();
        }
    	else
    	{
            // �� ������������ <= �������ξ�硢�����������������ʳ��ξ��ϡ�����������
            if (dd <= close) {
                var date1 = new Date(yyyy, mm - 1, close);
                var end = yyyy + '/' + mm + '/' + close;
            } else {
                var date1 = new Date(yyyy, mm - 1, close);
                date1.setMonth(date1.getMonth() + 1);
                var end = date1.getFullYear() + '/' + (date1.getMonth()+1) + '/' + date1.getDate();
            }

            // �������1������������
            var startSplit = splitDate(end);
            var syyyy = parseInt(startSplit[1]);
            var smm   = parseInt(startSplit[2]);
            var sdd   = parseInt(startSplit[3]);
            var date2 = new Date(syyyy, smm - 1, sdd);
            // �줫����
            date2.setMonth(date2.getMonth() - 1);
            // ����
            date2.setDate(date2.getDate() + 1);
            var start = date2.getFullYear() + '/' + (date2.getMonth()+1) + '/' + date2.getDate();
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
        var mm   = regResult[2];
        var dd   = regResult[3];
        var di   = new Date(yyyy, mm - 1, dd);
        // ���դ�ͭ���������å�
        if (di.getFullYear() == yyyy && di.getMonth() == mm - 1 && di.getDate() == dd) {
            return regResult;
        }

        return false;
    };

    // �����Υ��쥯��BOX
    function setMonthSelectBox() {
        var today = new Date();
        var mm = today.getMonth()+1;
        //��
        var month = "<select>";
        for (var i=1; i<=12; i++ ) {
            if( i == mm) {
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
            'click': function() {
                // validation���å�
                var form = $('form[name="Invoice"]');
                // From/To�����Ϥ���Ƥ���������å�
                let $dtmFrom = $('input[name="From_dtmDeliveryDate"]').val();
                let $dtmTo   = $('input[name="To_dtmDeliveryDate"]').val();
                if($dtmFrom && $dtmTo){
                    let d1 = new Date($dtmFrom);
                    let d2 = new Date($dtmTo);
                    if(d1 > d2){
                        alert('Ǽ������TO�ˤ�Ǽ������FROM�ˤ��������Ǥ�');
                        return false;
                    }
                }

                // ���ꥢ�����å� (�ܵҥ����ɤޤ����̲ߤޤ��ϲ��Ƕ�ʬ������ͤȰۤʤ���)
                let bReset     = false;
                let changeCode = false;
                // �Ƹܵҥ�����
                var parentCustomerCode = window.opener.$('input[name="lngCustomerCode"]').val();
                var parentCustomerName = window.opener.$('input[name="strCustomerName"]').val();
                // �ܵҥ�����
                let customerCode = $('input[name="lngCustomerCode"]').val();
                let customerName = $('input[name="strCustomerName"]').val();

                let $tableB = window.opener.$('#tableB');
                // �ơ��֥�B <tbody>, <tr>
                let $tableB_tbody = $('tbody', $tableB);
                let $tableB_row = $('tr', $tableB_tbody);
                if($tableB_row.length > 0){
                    for (var i = 0, rowlen = $tableB_row.length; i < rowlen; i++) {
                        for (var j = 0, collen = $tableB_row[i].cells.length; j < collen; j++) {
                            if($tableB_row[i].cells[j].className == 'taxclass') {
                                // ���Ƕ�ʬ
                                var parentTaxCode  = $tableB_row[i].cells[j].innerText.replace(/[^0-9]/g, '');
                            }
                        }
                    }

                    if(parentCustomerCode != customerCode)
                    {
                        bReset     = true;
                        changeCode = true;
                    }
                    let taxClassCode = $('select[name="lngTaxClassCode"]').val();
                    if(taxClassCode != parentTaxCode){
                        bReset = true;
                    }
                }

                var msg = '���򤵤줿���٤����ƥ��ꥢ���ޤ�����������Ǥ�����';
                if(bReset == true && window.confirm(msg) === false ) {
                    return;
                }

                if(form.valid() == false){
                    return;
                }

                // �����ͤ����
                // �ܵ�
                var params = {
                        mode: 'ajax',
                        strSessionID: $('input[name="strSessionID"]').val(),
                        QueryName: 'selectClosedDayByCodeAndName',
                        conditions: {
                            customerCode:        customerCode,
                            customerName:        customerName,
                            strSlipCode:         $('input[name="strSlipCode"]').val(),
                            deliveryFrom:        $('input[name="From_dtmDeliveryDate"]').val(),
                            deliveryTo:          $('input[name="To_dtmDeliveryDate"]').val(),
                            deliveryPlaceCode:   $('input[name="lngDeliveryPlaceCode"]').val(),
                            deliveryPlaceName:   $('input[name="strDeliveryPlaceName"]').val(),
                            moneyClassCode:      $('select[name="lngMoneyClassCode"]').val(),
                            taxClassCode:        $('select[name="lngTaxClassCode"]').val(),
                            inChargeUserCode:    $('input[name="lngInChargeUserCode"]').val(),
                            inChargeUserName:    $('input[name="strInChargeUserName"]').val(),
                            inputUserCode:       $('input[name="lngInputUserCode"]').val(),
                            inputUserName:       $('input[name="lngInputUserName"]').val()
                        }
                    }

                // ��������
                var search = {
                                url: '/inv/regist/condition.php?strSessionID=' + $.cookie('strSessionID'),
                                type: 'post',
                                dataType: 'json',
                                data:params,
                            };

                $.ajax( search )
                .done(function(response){
                    console.log(response);
                    // �ƥ�����ɥ���¸�ߥ����å�
                    if (!window.opener || window.opener.closed)
                    {
                        // �ƥ�����ɥ���¸�ߤ��ʤ����
                        window.alert('�ᥤ�󥦥���ɥ�����������ޤ���');
                    }
                    else
                    {
                    	if(response.Message)
                    	{
                    		alert(response.Message);
                    		return;
                    	}
                        // TABLE����
                        window.opener.$.createTable(response);

                        // �ơ��֥�����
                        if(bReset == true) {
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

                        // �ܵҥ������ѹ�
                        if(changeCode == true) {
                            window.opener.$('input[name="lngCustomerCode"]').val(customerCode);
                            window.opener.$('input[name="strCustomerName"]').val(customerName);
                        }
                        window.close();
                    }
                })
                .fail(function(response){
                    console.log(response);
                    alert('�����˼��Ԥ��ޤ�����\n�����ѹ����Ʋ�������');
                    return;
                });
                return false;
            }
    });

    // ��۷׻�
    function billingAmount() {

        // �������ٰ�������
        tableB = $('#tableB');
        tableB_tbody = $('tbody', $tableB);
        tableB_row = $('tbody tr', $tableB);

        // �������ٰ������ꥢ��1���ܤξ�����Ψ���������
        let tax = false;
        for (var i = 0, rowlen = tableB_row.length; i < rowlen; i++) {
            if (tax !== false) continue;
            for (var j = 0, collen = tableB_row[i].cells.length; j < collen; j++) {
                if (tax !== false || !tableB_row[i].cells[j]) continue;
                if(tableB_row[i].cells[j].className == 'tax right') {
                    // ������Ψ
                    console.log(tableB_row[i].cells[j].innerText);
                    strtax = tableB_row[i].cells[j].innerText.replace(/[^0-9]/g, '');
                    tax = Number(strtax)/100;
                }
            }
        }

        // ��������ĳ�
        // Ǽ�������ּ��װ����Ǥ������٤���ȴ��ۤι��+���ι�פ��Ф��Ʋ��Ƕ�ʬ�˱����Ʒ׻����줿������
        let lastMonthBalance = 0;
        let curLastMonthBalance = 0;
        // ���������
        // Ǽ�������ּ��װʹߤǤ������٤���ȴ��ۤι��
        let thisMonthAmount = 0;
        // ������
        // ��������ۤ��Ф��Ʋ��Ƕ�ʬ�˱����Ʒ׻�
        let taxPrice = 0;
        // ������׳�
        // ��������ĳ� + ��������� + ������"
        let noTaxMonthAmount  = 0;

        // �ּ��סֻ�פ�׻�����
        selectClosedDay();

        var chargetern = function(){
            // �ּ��׼���
            let chargeternstart = $('input[name="dtmchargeternstart"]').val();
            let cs = isEmpty(chargeternstart);
            // �ֻ�׼���
            let chargeternend = $('input[name="dtmchargeternend"]').val();
            let ce = isEmpty(chargeternend);

            if (cs == 0 || ce == 0) return false;

            startStamp = new Date(chargeternstart);
            endStamp   = new Date(chargeternend);

            for (var i = 0, rowlen = tableB_row.length; i < rowlen; i++) {
                let deliverydate = false;
                let price   = false;
                let data = false;

                for (var j = 0, collen = tableB_row[i].cells.length; j < collen; j++) {
                    if (!tableB_row[i].cells[j].innerText) continue;
                    if(tableB_row[i].cells[j].className == 'deliverydate') {
                        // Ǽ����
                        deliverydate = tableB_row[i].cells[j].innerText;
                    }
                    if(tableB_row[i].cells[j].className == 'price right') {
                        // ��ȴ��
                        price = tableB_row[i].cells[j].innerText;
                    }
                }

                if(!deliverydate || !price) continue;
                date = splitDate(deliverydate);
                deliverydateStamp = new Date(deliverydate);

                if(deliverydateStamp <= startStamp) {
                    // ��������ĳ�
                    lastMonthBalance += Number(price);
                } else {
                    // ���������
                    thisMonthAmount  += Number(price);
                }
            }

            // ��������ĳ�(�����ǹ���)
            curLastMonthBalance = lastMonthBalance+(lastMonthBalance * (tax*100))/100;
            // �����Ƿ׻�
            // ��������ۤ��Ф��Ʋ��Ƕ�ʬ�˱����Ʒ׻�
            taxPrice  = (thisMonthAmount*(tax*100))/100;
            // ������׳�
            // ��������ĳ� + ��������� + ������
            noTaxMonthAmount  = curLastMonthBalance + thisMonthAmount + taxPrice;
            // ��̤��˱�
            $('input[name="curlastmonthbalance"]').val(Math.round(curLastMonthBalance)).change();
            $('input[name="curthismonthamount"]').val(thisMonthAmount).change();
            $('input[name="curtaxprice"]').val(Math.round(taxPrice)).change();
            $('input[name="notaxcurthismonthamount"]').val(Math.round(noTaxMonthAmount)).change();
        };
        var result = setTimeout(chargetern, 500);

    }

    // PREVIEW�ܥ��󲡲����� (preview)
    $('img.preview-button').on({
        'click': function() {
            // validation���å�
            if($('form[name="Invoice"]').valid()==false)
            {
                return;
            }
            // ��۷׻�
            billingAmount();
            // �ץ�ӥ塼���̸ƤӽФ� (�ٱ䤵���ʤ���INPUT�����Ǥ��ʤ�)
            var prev = setTimeout(previewDrow, 800);
        }
    });


    var previewDrow = function(){
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
                if(!tableB_row[i].cells[j].innerText) continue;

                if(tableB_row[i].cells[j].className == 'slipcode') {
                    // Ǽ�ʽ�No
                    slipCodeList.push(tableB_row[i].cells[j].innerText);
                }
                if(tableB_row[i].cells[j].className == 'tax right' && !tax) {
                    // ������
                    tax = tableB_row[i].cells[j].innerText;
                       console.log('�����ǥ��å�');
                }
                if(tableB_row[i].cells[j].className == 'taxclass' && !taxclass) {
                    // ��ȴ�����
                    taxclass = tableB_row[i].cells[j].innerText;
                }
                if(tableB_row[i].cells[j].className == 'deliverydate') {
                    // Ǽ����
                    deliveryDate.push(tableB_row[i].cells[j].innerText);
                }
                   if(tableB_row[i].cells[j].className == 'tax right' && tax) {
                       console.log('������Ψ�����å�');
                       if(tableB_row[i].cells[j].innerText != tax){
                           console.log('������NG');
                           isSameTax = false;
                       }
                   }
           }
        }

        // ���顼�����å�������ʤ���г�ǧ����ɽ��

        // �ץ�ӥ塼�Х�ǡ����������å�
        // �������ٰ������ꥢ�����٤�1�Ԥ�¸�ߤ��ʤ����
        if(slipCodeList.length === 0)
        {
            alert('�������٤����򤵤�Ƥ��ޤ���');
            return false;
        }
        // �������ٰ������ꥢ�����򤵤줿Ǽ�ʽ�ξ�����Ψ�����٤�Ʊ��ǤϤʤ����
        if(isSameTax == false)
        {
            alert('������Ψ�ΰۤʤ�Ǽ�ʽ�����������٤˺��ߤǤ��ޤ���');
            return false;
        }
        // Ǽ�����η���Ѥߤξ��
        // 1�������ȸܵҤ����������顢����������������롣
        // �ֻ�׼���
        let ternend = $('input[name="dtmchargeternend"]').val();
        let ternEndDate = new Date(ternend);
        // 2�����ƥ����դȸܵҤ����������顢��������������롣
        let systemDate = new Date();
        // �� ������������ <= �������ξ�硢�����������������ʳ��ξ��ϡ�����������
        let sysEndDate = new Date(systemDate.getFullYear(), systemDate.getMonth(), isCloseDay);
        if (systemDate.getDate() > isCloseDay) {
            sysEndDate.setMonth(sysEndDate.getMonth() + 1);
        }
        // �����(ǯ�٤����θ����)
        let d1 = new Date(ternEndDate.getFullYear(), ternEndDate.getMonth());
        let d2 = new Date(sysEndDate.getFullYear(), sysEndDate.getMonth());
        if(d1 < d2){
            alert('���ѤߤΤ��ᡢ���ꤵ�줿Ǽ������̵���Ǥ�');
            return false;
        }

        // Ǽ�����������ƥ����դǻ��Ф���������������1�������ˤʤ����
        // �إå����������ǤϤʤ�Ǽ�������٤�¸�ߤ������
        let start = new Date(sysEndDate.getTime());
        start.setMonth(start.getMonth() - 1);
        let end   = new Date(sysEndDate.getTime());
        end.setMonth(end.getMonth() + 1);
        let isDeliveryDate = true;

        // �����
        let invoicemonth = $('option:selected').val();
        let isSameMonth = true;

        $.each(deliveryDate, function(i, v) {
            let deliDate = new Date(v);

            if(deliDate < start || deliDate > end) {
                isDeliveryDate = false;
            }
            if(invoicemonth != deliDate.getMonth()+1){
                isSameMonth = false;
            }
        });

        if(isDeliveryDate == false) {
            alert('Ǽ�����������٤�����1����δ֤���ꤷ�Ƥ�������');
            return false;
        }
        if(isSameMonth == false) {
            alert('�������٤ˤϡ����Ϥ��줿Ǽ�����Ȱۤʤ���Ǽ�ʤ��줿���٤����Ǥ��ޤ���');
            return false;
        }

        var strMode = $('input[name="strMode"]').val();

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

        if(invForm.valid()){

            var windowName = 'registPreview';
            if(strMode == 'renewPrev') {
                // ����
                url = '/inv/regist/renew.php?strSessionID=' + $.cookie('strSessionID');
            }else{
                // ��Ͽ
                url = '/inv/regist/index.php?strSessionID=' + $.cookie('strSessionID');
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
    var insertCheck = function() {

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
                    if(!tableB_row[i].cells[j].innerText) continue;

                    if(tableB_row[i].cells[j].className == 'slipcode') {
                        // Ǽ�ʽ�No
                        console.log(tableB_row[i].cells[j].className);
                        console.log(tableB_row[i].cells[j].innerText);
                        slipCodeList.push(tableB_row[i].cells[j].innerText);
                    }
                    if(tableB_row[i].cells[j].className == 'tax right' && !tax) {
                        // ������
                        console.log(tableB_row[i].cells[j].className);
                        console.log(tableB_row[i].cells[j].innerText);
                        tax = tableB_row[i].cells[j].innerText;
                    }
                    if(tableB_row[i].cells[j].className == 'taxclass' && !taxclass) {
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
            if(invForm.valid()){

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

})();
