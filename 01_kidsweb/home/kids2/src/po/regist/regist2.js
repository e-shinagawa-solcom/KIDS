//
// regist2.js
//
jQuery(function($){
    $("#DetailTableBody").sortable();
    $("#DetailTableBody").on('sortstop', function(){
        changeRowNum();
    });

    function changeRowNum(){
        $('#DetailTableBody').find('[name="rownum"]').each(function(idx){
            $(this).html(idx + 1);
        });
    }
    function getCheckedRows(){
        var selected = getSelectedRows();
        var cnt = $(selected).length;
        if(cnt === 0) {
            // console.log("�ʤˤ⤷�ʤ�");
            return false;
        }
        if(cnt > 1) {
            alert("��ư�оݤ�1�ԤΤ����򤷤Ƥ�������");
            return false;
        }
        return true;
    }
    function getSelectedRows(){
        //return $('#DetailTableBody tr.selected');
        return $('#DetailTableBody tr');
    }
    function executeSort(mode){
        var row = $('#DetailTableBody').children('.selected');
        switch(mode) {
            case 0:
                $('#DetailTableBody tr:first').before($(row));
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
                $('#DetailTableBody').append($(row));
                break;
            default:
                break;
        }
        changeRowNum();
    }
    function validationCheck(){
        var result = true;
        var selectedRows = getSelectedRows();
        if(!selectedRows.length){
            alert("ȯ������򤪤��ʤ����ٹԤ����򤵤�Ƥ��ޤ���");
            return false;
        }
        var expirationDate = $('input[name="dtmExpirationDate"]').val();
        result = validateDate(expirationDate);
//        result = validateDelivery(selectedRows);
        result = validatePayCondition();

        return result;
    }
    function validateDate(d){
        if(!d){
            // ȯ��ͭ����������̤���Ϥξ��
            alert("ȯ��ͭ�������������ꤵ��Ƥ��ޤ���");
            return false;
        }
        if(!d.match(/^\d{4}\/\d{1,2}\/\d{1,2}$/g)){
            // ȯ��ͭ��������������ɽ����^\d{4}\/\d{1,2}\/\d{1,2}$�פ˰��פ��ʤ����
            alert("ȯ��ͭ���������ν񼰤˸�꤬����ޤ���");
            return false;
        }
        if(!isDate(d)){
            // ȯ��ͭ����������¸�ߤ��ʤ�����(2/31��)�ξ��
            alert("ȯ��ͭ����������¸�ߤ��ʤ����դ����ꤵ��ޤ�����");
            return false;
        }

        return true;
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
    function validateDelivery(rows){
        var result = true;
        var messages = [];
        $.each(rows, function(i, row){
            // console.log($(row)[0]);
            var deliveryMethod = $(row).find('select[name="lngdeliverymethodcode"]').val();
            if(deliveryMethod === '0') {
                messages.push((i + 1) + "���ܤα�����ˡ�����ꤵ��Ƥ��ޤ���");
                result = false;
            }
        });

        if(!result) {
            alert(messages.join("\n"));
        }
        return result;
    }
    function validatePayCondition(){
        var result = true;
        var payConditionOrign = $('input[name="lngPayConditionCodeOrign"]').val();
        var payCondition = $('select[name="lngPayConditionCode"]').val();
        if(payConditionOrign === payConditionOrign && payCondition === "1"){
            result = confirm("��ʧ����ˡ��T/T��侩���ޤ�����������L/C���ѹ����ޤ���?(�ѹ�����᤻�ޤ���)");
        }

        return result;
    }
    function getUpdateDetail(){
        var result = [];
        $.each(getSelectedRows(), function(i, tr){
            var param = {
                lngSortKey:               $(tr).find('td[name="rownum"]').text(),
                lngPurchaseOrderDetailNo: $(tr).find('.detailPurchaseorderDetailNo').text(),
                lngDeliveryMethodCode:    $(tr).find('option:selected').val(),
                strDeliveryMethodName:    $(tr).find('option:selected').text(),
            };
            result.push(param);
        });

        return result;
    }

    // events
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
    $('body').on('click', '#DetailTableBody tr', function(e){
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
    $(document).on('click', '#btnClose', function(){
        window.open('about:blank','_self').close();
    });
    $('#FixEntryBtn').on('click', function(){
        // console.log('������Ͽ�ܥ��󥯥�å�');
        if(!validationCheck()) {
            console.log("�Х�ǡ�����󥨥顼�Τ��������³��ߡ�")
            return false;
        }

        $.ajax({
            type: 'POST',
            url: 'renew.php',
            scriptCharset: 'EUC-JP',
            data: {
                strSessionID:        $('input[name="strSessionID"]').val(),
                strMode:             'renew',
                lngPurchaseOrderNo:  $('input[name="lngPurchaseOrderNo"]').val(),
                lngRevisionNo:       $('input[name="lngRevisionNo"]').val(),
                dtmExpirationDate:   $('input[name="dtmExpirationDate"]').val(),
                lngPayConditionCode: $('select[name="lngPayConditionCode"]').children('option:selected').val(),
                strPayConditionName: $('select[name="lngPayConditionCode"]').children('option:selected').text(),
                lngLocationCode:     $('input[name="lngLocationCode"]').val(),
                strLocationName:     $('input[name="strLocationName"]').val(),
                strNote:             $('input[name="strNote"]').val(),
                strOrderCode:        $('input[name="strOrderCode"]').val(),
                aryDetail:           getUpdateDetail(),
            }
        }).done(function(data){
            console.log("done");
            // console.log(data);
            document.write(data);
        }).fail(function(error){
            console.log("fail");
            console.log(error);
        });
    });
});