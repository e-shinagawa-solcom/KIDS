//
// regist.js
//

// ����������ϲ��̤����Ϥ��줿�ͤ�����ʸ���������ϲ��̤��ƤӽФ�����
function SetSearchConditionWindowValue(search_condition) {
    // �ܵ�
    $('input[name="lngCustomerCode"]').val(search_condition.strCompanyDisplayCode);
    $('input[name="strCustomerName"]').val(search_condition.strCompanyDisplayName);

    // �ܵҤ�ɳ�Ť��񥳡��ɤˤ�äƾ����Ƕ�ʬ�Υץ��������ѹ�����
    if(search_condition.strCompanyDisplayCode != ""){
        $.ajax({
            type: 'POST',
            url: 'index.php',
            data: {
                strMode : "get-lngcountrycode",
                strSessionID: $('input[name="strSessionID"]').val(),
                strCompanyDisplayCode: search_condition.strCompanyDisplayCode,
            },
            async: true,
        }).done(function(data){
            console.log("done:get-lngcountrycode");
            if (data == "81"){
                // 81���ֳ��ǡפ������¾�ι��ܤ������ǽ��
                $("select[name='lngTaxClassCode'] option:not(:selected)").prop('disabled', false);
                $("select[name='lngTaxClassCode']").val("2");
    
            }else{
                // 81�ʳ���������ǡ׸���
                $("select[name='lngTaxClassCode']").val("1");
                $("select[name='lngTaxClassCode'] option:not(:selected)").prop('disabled', true);
            }
        }).fail(function(error){
            console.log("fail:get-lngcountrycode");
            console.log(error);
        });
    }else{
        // �ܵҥ����ɤ����ʤ������
        $("select[name='lngTaxClassCode'] option:not(:selected)").prop('disabled', false);
    }
}

// ���ٸ����ʸ���������ϲ��̤��ƤӽФ�����
function SearchReceiveDetail(search_condition) {
 
    // ��ʬ�񤭴����Τ���ajax��POST
    $.ajax({
        type: 'POST',
        url: 'index.php',
        data: {
            strMode : "search-detail",
            strSessionID: $('input[name="strSessionID"]').val(),
            condition: search_condition,
        },
        async: true,
    }).done(function(data){
        console.log("done:search-detail");
        // ������̤�ơ��֥�˥��å�
        $('#DetailTableBody').html(data);

    }).fail(function(error){
        console.log("fail:search-detail");
        console.log(error);
    });
    
}

jQuery(function($){
    $("#EditTableBody").sortable();
    $("#EditTableBody").on('sortstop', function(){
        changeRowNum();
    });
    // $('input[name="dtmExpirationDate"]').datepicker();

    // ------------------------------------------
    //   functions
    // ------------------------------------------
    function checkPresentRow(tr){
        var orderNo = $(tr).children('td.detailOrderCode').text();
        var orderDetailNo = $(tr).children('td.detailOrderDetailNo').text();
        var result = false;
        $.each($('#EditTableBody tr'), function(i, tr){
            var od = $(tr).children('.detailOrderCode').text();
            var odd = $(tr).children('td.detailOrderDetailNo').text();
            if(orderNo === od && orderDetailNo === odd){
                result = true;
            }
        });
        return result;
    }
    
    //�������ٰ������ꥢ�����򤷤����٤��ɲ�
    function setEdit(tr){

        //DEBUG:��ö��ʣ�����å�����
        //if(checkPresentRow(tr)){
        //   return false;
        //}

        var editTable = $('#EditTable');
        var tbody = $('#EditTableBody');
        var i = $(tbody).find('tr').length;
        
        var editTr = $('<tr></tr>');
        var td = $('<td></td>').text(i + 1);
        
        // No.
        $(td).attr('name', 'rownum');
        $(editTr).append($(td));
        // �ܵҼ����ֹ�
        td = $(tr).find($('td.detailCustomerReceiveCode')).clone();
        $(editTr).append($(td));
        // �����ֹ�
        td = $(tr).find($('td.detailReceiveCode')).clone();
        $(editTr).append($(td));
        // �ܵ�����
        td = $(tr).find($('td.detailGoodsCode')).clone();
        $(editTr).append($(td));
        // ���ʥ�����
        td = $(tr).find($('td.detailProductCode')).clone();
        $(editTr).append($(td));
        // ����̾
        td = $(tr).find($('td.detailProductName')).clone();
        $(editTr).append($(td));
        // ����̾�ʱѸ��
        td = $(tr).find($('td.detailProductEnglishName')).clone();
        $(editTr).append($(td));
        // �Ķ�����
        td = $(tr).find($('td.detailSalesDeptName')).clone();
        $(editTr).append($(td));
        // ����ʬ
        td = $(tr).find($('td.detailSalesClassName')).clone();
        $(editTr).append($(td));
        // Ǽ��
        td = $(tr).find($('td.detailDeliveryDate')).clone();
        $(editTr).append($(td));
        // ����
        td = $(tr).find($('td.detailUnitQuantity')).clone();
        $(editTr).append($(td));
        // ñ��
        td = $(tr).find($('td.detailProductPrice')).clone();
        $(editTr).append($(td));
        // ñ��
        td = $(tr).find($('td.detailProductUnitName')).clone();
        $(editTr).append($(td));
        // ����
        td = $(tr).find($('td.detailProductQuantity')).clone();
        $(editTr).append($(td));
        // ��ȴ���
        td = $(tr).find($('td.detailSubTotalPrice')).clone();
        $(editTr).append($(td));
        //�����ֹ��������Ͽ�ѡ�
        td = $(tr).find($('td.detailReceiveNo')).clone();
        $(editTr).append($(td));
        //���������ֹ��������Ͽ�ѡ�
        td = $(tr).find($('td.detailReceiveDetailNo')).clone();
        $(editTr).append($(td));
        //��ӥ�����ֹ��������Ͽ�ѡ�
        td = $(tr).find($('td.detailRevisionNo')).clone();
        $(editTr).append($(td));
        //���Υ����ɡ�������Ͽ�ѡ�
        td = $(tr).find($('td.detailReviseCode')).clone();
        $(editTr).append($(td));
        //����ʬ�����ɡ�������Ͽ�ѡ�
        td = $(tr).find($('td.detailSalesClassCode')).clone();
        $(editTr).append($(td));
        //����ñ�̥����ɡ�������Ͽ�ѡ�
        td = $(tr).find($('td.detailProductUnitCode')).clone();
        $(editTr).append($(td));
        //���͡�������Ͽ�ѡ�
        td = $(tr).find($('td.detailNote')).clone();
        $(editTr).append($(td));
        //�̲�ñ�̥����ɡ�������Ͽ�ѡ�
        td = $(tr).find($('td.detailMonetaryUnitCode')).clone();
        $(editTr).append($(td));
        //�̲ߥ졼�ȥ����ɡ�������Ͽ�ѡ�
        td = $(tr).find($('td.detailMonetaryRateCode')).clone();
        $(editTr).append($(td));
        //�̲�ñ�̵����������Ͽ�ѡ�
        td = $(tr).find($('td.detailMonetaryUnitSign')).clone();
        $(editTr).append($(td));
        
        // �������٥ơ��֥�����٤��ɲ�
        $(tbody).append($(editTr));
        $(editTable).append($(tbody));
    }

    
    // ��׶�ۡ������ǳۤι���
    function updateAmount(){

        // ��ȴ��ۤ������������˳�Ǽ
        var aryPrice = [];
        $("#EditTableBody tr").each(function() {
            //����ޤ�Ϥ����Ƥ�����ͤ��Ѵ�
            var price = Number($(this).find($('td.detailSubTotalPrice')).html().split(',').join(''));
            aryPrice.push(price);
        });

        // ----------------
        // ��׶�ۤλ���
        // ----------------
        var totalAmount = 0;
        aryPrice.forEach(function(price) {
            totalAmount += price;
        });

        // ----------------
        // �����ǳۤλ���
        // ----------------
        // �����Ƕ�ʬ�����
        var taxClassCode = $('select[name="lngTaxClassCode"]').children('option:selected').val();

        // ������Ψ�����
        var taxRate = Number($('select[name="lngTaxRate"]').children('option:selected').text());

        console.log(taxRate);

        // �����ǳۤη׻�
        var taxAmount = 0;
        if (taxClassCode == "1")
        {
            // 1:�����
            taxAmount = 0;
        }
        else if (taxClassCode == "2")
        {
            // 2:����
            taxAmount = Math.floor(totalAmount * taxRate);
        }
        else if (taxClassCode == "3")
        {
            // 3:����
            aryPrice.forEach(function(price) {
                taxAmount += Math.floor( (price / (1+taxRate)) * taxRate );
            });
        }

        // ------------------
        // �ե�������ͤ�����
        // ------------------
        $('input[name="strTotalAmount"]').val(totalAmount);
        $('input[name="strTaxAmount"]').val(taxAmount);
    }

    function getCheckedRows(){
        var selected = getSelectedRows();
        var cnt = $(selected).length;
        if(cnt === 0) {
            console.log("�ʤˤ⤷�ʤ�");
            return false;
        }
        if(cnt > 1) {
            //console.log("�ʤˤ⤷�ʤ�����������ǽ������ɲäˤʤ뤫�⤷��ʤ���");
            alert("��ư�оݤ�1�ԤΤ����򤷤Ƥ�������");
            return false;
        }
        return true;
    }
    function getSelectedRows(){
        return $('#EditTableBody tr.selected');
    }
    function executeSort(mode){
        var row = $('#EditTableBody').children('.selected');
        switch(mode) {
            case 0:
                $('#EditTableBody tr:first').before($(row));
                break;
            case 1:
                var rowPreview = $(row).prev('tr');
                if(row.prev.length) {
                    row.insertBefore(rowPreview);
                    var td = rowPreview.children('td[name="rownum"]')
                }
                break;
            case 2:
                var rowNext = $(row).next('tr');
                if(rowNext.length) {
                    row.insertAfter(rowNext);
                    var td = rowNext.children('td[name="rownum"]')
                }
                break;
            case 3:
                $('#EditTableBody').append($(row));
                break;
            default:
                break;
        }
        changeRowNum();
    }
    function changeRowNum(){
        $('#EditTableBody').find('[name="rownum"]').each(function(idx){
            $(this).html(idx + 1);
        });
    }
    function validationCheck2(){
        var result = true;
        if(!getSelectedRows().length){
            // ȯ�����ٹԤ�1������򤵤�Ƥ��ʤ����
            alert("ȯ����ꤹ�����ٹԤ����򤵤�Ƥ��ޤ���");
            result = false;
        }
        var expirationDate = $('input[name="dtmExpirationDate"]').val();
        if(!expirationDate){
            // ȯ��ͭ����������̤���Ϥξ��
            alert("ȯ��ͭ�������������ꤵ��Ƥ��ޤ���");
            result = false;
        }
        if(!expirationDate.match(/^\d{4}\/\d{1,2}\/\d{1,2}$/g)){
            // ȯ��ͭ��������������ɽ����^\d{4}\/\d{1,2}\/\d{1,2}$�פ˰��פ��ʤ����
            alert("ȯ��ͭ���������ν񼰤˸�꤬����ޤ���");
            result = false;
        }
        if(!isDate(expirationDate)){
            // ȯ��ͭ����������¸�ߤ��ʤ�����(2/31��)�ξ��
            alert("ȯ��ͭ����������¸�ߤ��ʤ����դ����ꤵ��ޤ�����");
            result = false;
        }
        var countryCode = $('input[name="lngCountryCode"]').val();
        if(countryCode !== '81'){
            var selected = $('select[name="optPayCondition"]').children('option:selected').val();
            if(selected === '0'){
                // �������m_company.lngcountrycode����81(����)�װʳ����Ļ�ʧ��郎̤����ξ��
                alert('�����褬�����ξ�硢��ʧ��������ꤷ�Ƥ���������');
                result = false;
            }
        }
        var locationCode = $('input[name="lngLocationCode"]').val();
        if(!locationCode){
            // Ǽ�ʾ�꤬̤���Ϥξ��
            alert('Ǽ�ʾ�꤬���ꤵ��Ƥ��ޤ���');
            result = false;
        }
        var details = getSelectedRows();
        var message = [];
        $.each(details, function(i, tr){
            var selected = $(tr).find('option:selected').val();
            if(selected === "0"){
                var row = $(tr).children('td[name="rownum"]').text();
                message.push(row + '���ܤα�����ˡ�����ꤵ��Ƥ��ޤ���');
            }
        });
        if(message.length){
            // ������ˡ��1��Ǥ�̤����ξ��
            alert(message.join('\n'));
            result = false;
        }

        return result;
    }
    function isDate(d){
        if(d == "") { return false; }
        if(!d.match(/^\d{4}\/\d{1,2}\/\d{1,2}$/)) { return false; }
    
        var date = new Date(d);
        if(date.getFullYear() != d.split("/")[0]
            || date.getMonth() != d.split("/")[1] - 1
            || date.getDate() != d.split("/")[2]
        ){
            return false;
        }
        return true;
    }

    // �إå������եå������ϥ��ꥢ��POST�ѥǡ�������
    function getUpdateHeader(){
        
        var result = {
            //��ɼ��
            lnginsertusercode:         $('input[name="lngInsertUserCode"]').val(),
            strinsertusername:         $('input[name="strInsertUserName"]').val(),
            //�ܵ�
            strcompanydisplaycode:     $('input[name="lngCustomerCode"]').val(),
            strcompanydisplayname:     $('input[name="strCustomerName"]').val(),
            //�ܵ�ô����
            strcustomerresponder:      $('input[name="strCustomerResponder"]').val(),
            //Ǽ����
            dtmdeliverydate:           $('input[name="dtmDeliveryDate"]').val(),
            //Ǽ����
            lngdeliveryplacecode:      $('input[name="lngDeliveryPlaceCode"]').val(),
            strdeliveryplacename:      $('input[name="strDeliveryPlaceName"]').val(),
            //Ǽ����ô����
            strdeliverydestresponder:  $('input[name="strDeliveryDestResponder"]').val(),
            //����
            strnote:                   $('input[name="strNote"]').val(),
            //�����Ƕ�ʬ
            lngtaxclasscode:           $('select[name="lngTaxClassCode"]').children('option:selected').val(),
            strtaxclassname:           $('select[name="lngTaxClassCode"]').children('option:selected').text(),
            //������Ψ
            lngtaxrate:                $('select[name="lngTaxRate"]').children('option:selected').text(),
            //�����ǳ�
            strtaxamount:              $('input[name="strTaxAmount"]').val(),
            //��ʧ����
            dtmpaymentduedate:         $('input[name="dtmPaymentDueDate"]').val(),
            //��ʧ��ˡ
            lngpaymentmethodcode:      $('select[name="lngPaymentMethodCode"]').children('option:selected').val(),
            //��׶��
            curtotalprice:             $('input[name="strTotalAmount"]').val(),
        };

        return result;
    }

    // �������ٰ������ꥢ��POST�ѥǡ�������
    function getUpdateDetail(){
        var result = [];
        $.each($('#EditTableBody tr'), function(i, tr){
            var param ={
                //�ܵ�ȯ���ֹ�
                strcustomerreceivecode: $(tr).children('.detailCustomerReceiveCode').text(),
                //�����ֹ�
                strreceivecode: $(tr).children('.detailReceiveCode').text(),
                //�ܵ�����
                strgoodscode: $(tr).children('.detailGoodsCode').text(),
                //���ʥ�����
                strproductcode: $(tr).children('.detailProductCode').text(),
                //����̾
                strproductname: $(tr).children('.detailProductName').text(),
                //����̾�ʱѸ��
                strproductenglishname: $(tr).children('.detailProductEnglishName').text(),
                //�Ķ�����
                strsalesdeptname: $(tr).children('.detailSalesDeptName').text(),
                //����ʬ
                strsalesclassname: $(tr).children('.detailSalesClassName').text(),
                //Ǽ��
                dtmdeliverydate: $(tr).children('.detailDeliveryDate').text(),
                //����
                lngunitquantity: $(tr).children('.detailUnitQuantity').text(),
                //ñ��
                curproductprice: $(tr).children('.detailProductPrice').text(),
                //ñ��
                strproductunitname: $(tr).children('.detailProductUnitName').text(),
                //����
                lngproductquantity: $(tr).children('.detailProductQuantity').text(),
                //��ȴ���
                cursubtotalprice: $(tr).children('.detailSubTotalPrice').text(),
                //�����ֹ��������Ͽ�ѡ�
                lngreceiveno: $(tr).children('.detailReceiveNo').text(),
                //���������ֹ��������Ͽ�ѡ�
                lngreceivedetailno: $(tr).children('.detailReceiveDetailNo').text(),
                //��ӥ�����ֹ��������Ͽ�ѡ�
                lngrevisionno: $(tr).children('.detailRevisionNo').text(),
                //���Υ����ɡ�������Ͽ�ѡ�
                strrevisecode: $(tr).children('.detailReviseCode').text(),
                //����ʬ�����ɡ�������Ͽ�ѡ�
                lngsalesclasscode: $(tr).children('.detailSalesClassCode').text(),
                //����ñ�̥����ɡ�������Ͽ�ѡ�
                lngproductunitcode: $(tr).children('.detailProductUnitCode').text(),
                //���͡�������Ͽ�ѡ�
                strnote: $(tr).children('.detailNote').text(),
                //�̲�ñ�̥����ɡ�������Ͽ�ѡ�
                lngmonetaryunitcode: $(tr).children('.detailMonetaryUnitCode').text(),
                //�̲ߥ졼�ȥ����ɡ�������Ͽ�ѡ�
                lngmonetaryratecode: $(tr).children('.detailMonetaryRateCode').text(),
                //�̲�ñ�̵����������Ͽ�ѡ�
                strmonetaryunitsign: $(tr).children('.detailMonetaryUnitSign').text(),
            };
            result.push(param);
        });
        return result;
    }


    // �̥�����ɥ��򳫤���POST����
    function post_open(url, data, target, features) {

        window.open('', target, features);
       
        // �ե������ưŪ������
        var html = '<form id="temp_form" style="display:none;">';
        for(var x in data) {
          if(data[x] == undefined || data[x] == null) {
            continue;
          }
          var _val = data[x].replace(/'/g, "\'");
          html += "<input type='hidden' name='" + x + "' value='" + _val + "' >";
        }
        html += '</form>';
        $("body").append(html);
       
        $('#temp_form').attr("action",url);
        $('#temp_form').attr("target",target);
        $('#temp_form').attr("method","POST");
        $('#temp_form').submit();
       
        // �ե��������
        $('#temp_form').remove();
    }
    
    // ------------------------------------------
    //   events
    // ------------------------------------------
    $("select[name='lngTaxClassCode']").on('change', function(){
        updateAmount();
    });

    $('select[name="lngTaxRate"]').on('change', function(){
        updateAmount();
    });

    $('input[name="dtmDeliveryDate"]').on('change', function(){
        //TODO:change���٥�Ȥ򽦤��ʤ��Τ��������衣��ľ���������Ϥ�dateTimePicker���ؤ����ۤ�������
        
        //������Ψ����������ѹ�
        $.ajax({
            type: 'POST',
            url: 'index.php',
            data: {
                strMode : "change-deliverydate",
                strSessionID: $('input[name="strSessionID"]').val(),
                dtmDeliveryDate: $(this).val(),
            },
            async: true,
        }).done(function(data){
            console.log("done:change-deliverydate");
            //TODO:������Ψ��������ܹ���
            console.log(data);
            
            //��ۤι���
            updateAmount();

        }).fail(function(error){
            console.log("fail:change-deliverydate");
            console.log(error);
        });

    });

    $('#DetailTableBodyAllCheck').on('change', function(){
        $('input[name="edit"]').prop('checked', this.checked);
    });
    $('#SearchBt').on('click', function(){
        
        var url = "/sc/regist2/condition.php" + "?strSessionID=" + $('input[name="strSessionID"]').val();
        var data = {
            strSessionID: $('input[name="strSessionID"]').val(),
            param1: 'test'
          };
        
        var features = "width=710,height=460,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no";
        post_open(url, data, "conditionWin", features);

    });
    
    // �ɲåܥ���
    $('#AddBt').on('click', function(){
        
        var cb = $('#DetailTableBody').find('input[name="edit"]');
        var checked = false;
        var trArray = [];
        $.each(cb, function(i, v){
            if($(v).prop('checked')){
                checked = true;
                trArray.push($(v).parent().parent());
            }
        });
        if(!checked){
            //alert("���ٹԤ����򤵤�Ƥ��ޤ���");
            return false;
        }
        
        // ���������ɲ�
        $.each($(trArray), function(i, v){
            setEdit($(v));
        });

        // ��׶�ۡ������ǳۤι���
        updateAmount();
    });


    $('body').on('click', '#EditTableBody tr', function(e){
        var tds = $(e.currentTarget).children('td');
        var checked = $(tds).hasClass('selected');
        if(checked){
            $(tds).removeClass('selected');
            $(this).removeClass('selected');
        } else {
            $(tds).addClass('selected');
            $(this).addClass('selected');
        }
    });
    $('#selectup').on('click', function(){
        var selected = getCheckedRows();
        if(!selected){ return false; }
        executeSort(0);
    });
    $('#selectup1').on('click', function(){
        var selected = getCheckedRows();
        if(!selected){ return false; }
        executeSort(1);
    });
    $('#selectdown1').on('click', function(){
        var selected = getCheckedRows();
        if(!selected){ return false; }
        executeSort(2);
    });
    $('#selectdown').on('click', function(){
        var selected = getCheckedRows();
        if(!selected){ return false; }
        executeSort(3);
    });
    $("#DeleteBt").on('click', function(){
        var selected = getSelectedRows();
        if(!selected.length) { return false; }
        $(selected).remove();
        changeRowNum();
        updateAmount();
    });
    $('#AllDeleteBt').on('click', function(){
        $('#EditTableBody').empty();
        updateAmount();
    });


    // �ץ�ӥ塼�ܥ��󲡲�
    $('#PreviewBt').on('click', function(){
       
        var target = "previewWin";
        var features = "width=800,height=800,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no";

        // ��˶��Υ�����ɥ��򳫤��Ƥ���
        var emptyWin = window.open('', target, features);

        // POST�ǡ�������
        var data = {
            strMode :      "display-preview",
            strSessionID:  $('input[name="strSessionID"]').val(),
            aryHeader:     getUpdateHeader(),
            aryDetail:     getUpdateDetail(),
        };

        $.ajax({
            type: 'POST',
            url: 'preview.php',
            data: data,
            async: true,
        }).done(function(data){
            console.log("done");
            
            var url = "/sc/regist2/preview.php" + "?strSessionID=" + $('input[name="strSessionID"]').val();
            var previewWin = window.open('' , target , features );
            previewWin.document.write(data);
            previewWin.document.close();
            
            //���ɤ߹��ߤʤ��ǥ��ɥ쥹�С���URL�Τ��ѹ�
            previewWin.history.pushState(null,null,url);
            
        }).fail(function(error){
            console.log("fail");
            console.log(error);
            emptyWin.close();
        });

    });
});