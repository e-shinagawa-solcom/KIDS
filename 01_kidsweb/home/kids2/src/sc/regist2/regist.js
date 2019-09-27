//
// regist.js
//

// ------------------------------------------------------------------
//   Enter�����������٥��
// ------------------------------------------------------------------
window.document.onkeydown=fncEnterKeyDown;

function fncEnterKeyDown( e )
{
    // Enter���������������ɲ�
	if( window.event.keyCode == 13)
	{
        if (document.activeElement.id == "BaseBack"){
            $("#AddBt").trigger('click');
        }
	}
}

// ------------------------------------------------------------------
//   �������򥨥ꥢ���������
// ------------------------------------------------------------------
var lastSelectedRow;
function RowClick(currenttr, lock) {
    if (window.event.ctrlKey) {
        toggleRow(currenttr);
    }

    if (window.event.button === 0) {
        if (!window.event.ctrlKey && !window.event.shiftKey) {
            clearAllSelected();
            toggleRow(currenttr);
        }

        if (window.event.shiftKey) {
            selectRowsBetweenIndexes([lastSelectedRow.rowIndex, currenttr.rowIndex])
        }
    }
}

function toggleRow(row) {
    row.className = row.className == 'selected' ? '' : 'selected';
    // TODO:Ʊ���ԤΥ����å��ܥå�����ON/OFF���ڤ��ؤ���
    var checked = $(row).find('input[name="edit"]').prop('checked');
    $(row).find('input[name="edit"]').prop('checked', !checked);
    
    lastSelectedRow = row;
}

function selectRowsBetweenIndexes(indexes) {
    var trs = document.getElementById("DetailTable").tBodies[0].getElementsByTagName("tr");
    indexes.sort(function(a, b) {
        return a - b;
    });

    for (var i = indexes[0]; i <= indexes[1]; i++) {
        trs[i-1].className = 'selected';
        var checked = $(trs[i-1]).find('input[name="edit"]').prop('checked');
        $(trs[i-1]).find('input[name="edit"]').prop('checked', !checked);
    }
}

function clearAllSelected() {
    var trs = document.getElementById("DetailTable").tBodies[0].getElementsByTagName("tr");
    for (var i = 0; i < trs.length; i++) {
        trs[i].className = '';
    }
}
// ------------------------------------------------------------------

// �㸡��������ϲ��̤���ƤФ��ؿ����Σ���
// ����������ϲ��̤����Ϥ��줿�ͤ�����
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
                strcompanydisplaycode: search_condition.strCompanyDisplayCode,
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

// �㸡��������ϲ��̤���ƤФ��ؿ����Σ���
// ���ٸ���
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
        // jQueryUI��tablesorter�ǥ���������
        $('#DetailTable').tablesorter({
            headers: {
                'not-sortable': { sorter: false },
            }            
        });

    }).fail(function(error){
        console.log("fail:search-detail");
        console.log(error);
    });
    
}

// �㸡��������ϲ��̤���ƤФ��ؿ����Σ���
// �������٤򤹤٤ƥ��ꥢ
function ClearAllEditDetail() {
    // ������ܥ��󥯥�å����ư�ǵ�ư
    $("#AllDeleteBt").trigger('click');
}

jQuery(function($){
    $("#EditTableBody").sortable();
    $("#EditTableBody").on('sortstop', function(){
        changeRowNum();
    });

    // ------------------------------------------
    //   datepicker����
    // ------------------------------------------
    // datepicker�о�����
    var dateElements = [
        // Ǽ����
        $('input[name="dtmDeliveryDate"]'),
        // ��ʧ����
        $('input[name="dtmPaymentLimit"]'),
    ];
    // datepicker������
    $.each(dateElements, function(){
        this.datepicker({
                showButtonPanel: true,
                dateFormat: "yy/mm/dd",
                onClose: function(){
                    this.focus();
                }
            }).attr({
                maxlength: "10"
        });
    });

    // ------------------------------------------
    //   functions
    // ------------------------------------------
    // �ɲåХ�ǡ����������å�
    function validateAdd(tr){
        //�������򥨥ꥢ��Ǽ��
        var detailDeliveryDate = new Date($(tr).children('td.detailDeliveryDate').text());

        //�إå����եå�����Ǽ��������ӡ�Ʊ��ʳ���������
        var headerDeliveryDate = new Date($('input[name="dtmDeliveryDate"]').val());
        var sameMonth = (headerDeliveryDate.getYear() == detailDeliveryDate.getYear())
                        && (headerDeliveryDate.getMonth() == detailDeliveryDate.getMonth());
        if (!sameMonth){
            alert("����������Ǽ����Ǽ�����Ȱ��פ��ޤ��󡣼���ǡ����������Ƥ���������");
            return false;
        }

        //�������٤���Ƭ�Ԥ�����Ф���Ǽ������ӡ�Ʊ��ʳ���������
        var firstTr = $("#EditTableBody tr").eq(0);
        if (0 < firstTr.length){
            var firstRowDate = new Date($(firstTr).children('td.detailDeliveryDate').text());
            var sameMonthDetail = (firstRowDate.getYear() == detailDeliveryDate.getYear())
                                && (firstRowDate.getMonth() == detailDeliveryDate.getMonth());
            if (!sameMonthDetail){
                alert("�������٤�Ǽ�ʷ�ۤʤ����٤�����Ǥ��ޤ���");
                return false;
            }
        }

        //��ʣ�������٤��ɲä�ػߡʽ�ʣȽ�ꡧ�������٤Υ�����
        var existsSameKey = false;
        var rn1 = $(tr).children('td.detailReceiveNo').text();
        var dn1 = $(tr).children('td.detailReceiveDetailNo').text();
        var rev1 = $(tr).children('td.detailReceiveRevisionNo').text();
    
        $("#EditTableBody tr").each(function(){
            var rn2 = $(this).children('td.detailReceiveNo').text();
            var dn2 = $(this).children('td.detailReceiveDetailNo').text();
            var rev2 = $(this).children('td.detailReceiveRevisionNo').text();

            var isSame = (rn1 == rn2) && (dn1 == dn2) && (rev1 == rev2);
            existsSameKey = existsSameKey || isSame;
        });

        if (existsSameKey){
            alert("��ʣ�������٤�����Ǥ��ޤ���");
            return false;
        }

        return true;
    }
    
    //�������ٰ������ꥢ�����򤷤����٤��ɲ�
    function setEdit(tr){

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
        td = $(tr).find($('td.detailReceiveRevisionNo')).clone();
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
        //��������ե饰��������Ͽ�ѡ�
        td = $(tr).find($('td.detailUnifiedFlg')).clone();
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
            strdrafteruserdisplaycode:  $('input[name="lngInsertUserCode"]').val(),
            strdrafteruserdisplayname:  $('input[name="strInsertUserName"]').val(),
            //�ܵ�
            strcompanydisplaycode:     $('input[name="lngCustomerCode"]').val(),
            strcompanydisplayname:     $('input[name="strCustomerName"]').val(),
            //�ܵ�ô����
            strcustomerusername:       $('input[name="strCustomerUserName"]').val(),
            //Ǽ����
            dtmdeliverydate:           $('input[name="dtmDeliveryDate"]').val(),
            //Ǽ����
            strdeliveryplacecompanydisplaycode: $('input[name="lngDeliveryPlaceCode"]').val(),
            strdeliveryplacename:      $('input[name="strDeliveryPlaceName"]').val(),
            //Ǽ����ô����
            strdeliveryplaceusername:  $('input[name="strDeliveryPlaceUserName"]').val(),
            //����
            strnote:                   $('input[name="strNote"]').val(),
            //�����Ƕ�ʬ
            lngtaxclasscode:           $('select[name="lngTaxClassCode"]').children('option:selected').val(),
            strtaxclassname:           $('select[name="lngTaxClassCode"]').children('option:selected').text(),
            //������Ψ
            lngtaxcode:                $('select[name="lngTaxRate"]').children('option:selected').val(),
            lngtaxrate:                $('select[name="lngTaxRate"]').children('option:selected').text(),
            //�����ǳ�
            strtaxamount:              $('input[name="strTaxAmount"]').val(),
            //��ʧ����
            dtmpaymentlimit:           $('input[name="dtmPaymentLimit"]').val(),
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
                //No.�ʹ��ֹ��
                rownumber: $(tr).children('td[name="rownum"]').text(),
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
                //ñ����������޽���
                curproductprice: $(tr).children('.detailProductPrice').text().split(',').join(''),
                //ñ��
                strproductunitname: $(tr).children('.detailProductUnitName').text(),
                //���̡�������޽���
                lngproductquantity: $(tr).children('.detailProductQuantity').text().split(',').join(''),
                //��ȴ��ۡ�������޽���
                cursubtotalprice: $(tr).children('.detailSubTotalPrice').text().split(',').join(''),
                //�����ֹ��������Ͽ�ѡ�
                lngreceiveno: $(tr).children('.detailReceiveNo').text(),
                //���������ֹ��������Ͽ�ѡ�
                lngreceivedetailno: $(tr).children('.detailReceiveDetailNo').text(),
                //��ӥ�����ֹ��������Ͽ�ѡ�
                lngreceiverevisionno: $(tr).children('.detailReceiveRevisionNo').text(),
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
                //��������ե饰��������Ͽ�ѡ�
                bytdetailunifiedflg: $(tr).children('.detailUnifiedFlg').text(),
            };
            result.push(param);
        });
        return result;
    }

    // �̥�����ɥ��򳫤���POST����ʸ���������ϲ��̤򳫤��Ȥ��������ѡ�
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

    // --------------------------------------------------------------------------------
    //   ���շ׻��إ�Ѵؿ�
    // --------------------------------------------------------------------------------
    // n���������ǯ�������������
    function getAddMonthDate(year,month,day,add){
        var addMonth = month + add;
        var endDate = getEndOfMonth(year,addMonth);//addʬ��ä�����κǽ��������

        //�������Ϥ��줿���դ�n�����κǽ�������礭��������դ򼡷�ǽ����˹�碌��
        //5/31��6/30�Τ褦�˱�������̵������ɬ��
        if(day > endDate){
            day = endDate;
        }else{
            day = day - 1;
        }

        var addMonthDate = new Date(year,addMonth - 1,day);
        return addMonthDate;
    }
    //����η����������
    //�������0���ܡả��������ˤʤ�
    function getEndOfMonth(year,month){
        var endDate = new Date(year,month,0);
        return endDate.getDate();
    }

    // ���������Ȥ˷��٤�׻�����
    function getMonthlyBasedOnClosedDay(targetDate, closedDay)
    {
        var targetYear = targetDate.getFullYear();
        var targetMonth = targetDate.getMonth()+1;
        var targetDay = targetDate.getDate();

        if (targetDay > closedDay){
            // �о��� > ������ �ʤ������
            return getAddMonthDate(targetYear, targetMonth, 1, +1);
        }else{
            // �о��� <= ������ �ʤ�������
            return new Date(targetYear, targetMonth, 1);
        }
    }
    // --------------------------------------------------------------------------------

    // ------------------------------------------
    //    �Х�ǡ�������Ϣ
    // ------------------------------------------
    // �������٥��ꥢ�����٤�1�԰ʾ�¸�ߤ���ʤ� true
    function existsEditDetail(){
        return $("#EditTableBody tr").length > 0;
    }

    // Ǽ�����η���ѤߤǤ���ʤ� true
    function isClosedMonthOfDeliveryDate(deliveryDate, closedDay)
    {
        // �����ƥ�����
        var nowDate = new Date();
        // �ܵҤη���
        var customerMonthly = getMonthlyBasedOnClosedDay(nowDate, closedDay);
        // Ǽ�����η���
        var deliveryMonthly = getMonthlyBasedOnClosedDay(deliveryDate, closedDay);
        // Ǽ�����η��١�ܵҤη��� �ʤ顢Ǽ�����η�������
        var isClosed = (deliveryMonthly.getTime() < customerMonthly.getTime());

        return isClosed;
    }

    // �о����դ������ƥ����դ������������ʤ� true
    function withinOneMonthBeforeAndAfter(targetDate)
    {
        // �����ƥ�����
        var nowDate = new Date();
        var nowYear = nowDate.getFullYear();
        var nowMonth = nowDate.getMonth()+1;
        var nowDay = nowDate.getDate();

        // �Ҥȷ���
        var aMonthAgo = getAddMonthDate(nowYear, nowMonth, nowDay, -1);
        // �Ҥȷ��
        var aMonthLater = getAddMonthDate(nowYear, nowMonth, nowDay, +1);

        // �����������ʤ�true
        var valid = (aMonthAgo.getTime() <= targetDate.getTime()) &&
                    (aMonthLater.getTime() >= targetDate.getTime());

        return valid;
    }

    // �������ٰ������ꥢ�����٤ˡ��إå�����Ǽ�����η��٤�Ʊ���٤ǤϤʤ�Ǽ�������٤�¸�ߤ���ʤ� true
    function existsInDifferentDetailDeliveryMonthly(deliveryDate, closedDay){

        // Ǽ�����η���
        var deliveryMonthly = getMonthlyBasedOnClosedDay(deliveryDate, closedDay);

        // �����٤�Ǽ���η��٤��������
        var aryDetailDeliveryMonthly = [];
        $("#EditTableBody tr").each(function(){
            // ���٤�Ǽ�������
            var detailDeliveryDate = $(this).children('td.detailDeliveryDate').text();
            // Ǽ���η���
            var detailDeliveryMonthly = getMonthlyBasedOnClosedDay(detailDeliveryDate, closedDay);
            // ������ɲ�
            aryDetailDeliveryMonthly.push(detailDeliveryMonthly);
        });

        // Ǽ���η��٤�Ǽ�����η��٤Ȱ��פ��ʤ����٤����ĤǤ⤢�ä��饨�顼
        var indifferentDetailExists = aryDetailDeliveryMonthly.some(function(element){ 
            return (element.getTime() != deliveryMonthly.getTime());
        });

        return !indifferentDetailExists;
    }

    // �������٥��ꥢ�γ����٤�����ʬ��������������å��������å�OK�ʤ�true��NG�ʤ�false
    function checkEditDetailsAreSameSalesClass()
    {
        // �������٥��ꥢ�ˤ��뤹�٤Ƥ����٤���������ե饰������ʬ�����ɤ��������
        var aryDetailUnifiedFlg = [];
        var aryDetailSalesClassCode = [];
        $("#EditTableBody tr").each(function(){
            // ��������ե饰���������������ɲ�
            aryDetailUnifiedFlg .push($(this).children('td.detailUnifiedFlg').text());
            // ����ʬ�����ɤ��������������ɲ�
            aryDetailSalesClassCode .push($(this).children('td.detailSalesClassCode').text());
        });

        // �������Ԥ�����ʬ�ޥ�������������ե饰��false -> OK�Ȥ��ƥ����å���λ�������Ǥʤ��ʤ飲����
        var allDetailUnifiedFlgIsFalse = aryDetailUnifiedFlg.every(function(element){ 
            return (element == false);
        });
        if (allDetailUnifiedFlgIsFalse){
            return true;
        }
        
        // ��������ʬ�ޥ�������������ե饰��true�Ǥ������٤η�� != �������ٰ������ꥢ�����ٹԿ� ���������ʤ� NG�Ȥ��ƥ����å���λ�������Ǥʤ��ʤ飳����
        var aryDetailUnifiedFlgIsTrue = aryDetailUnifiedFlg.filter(function(element){
            return (element == true);
        });
        if (aryDetailUnifiedFlgIsTrue.length != $("#EditTableBody tr").length){
            return false;
        }

        // �����������ٰ������ꥢ�����٤�����ʬ�����ɤ����٤�Ʊ���� -> OK�Ȥ��ƥ����å���λ�������Ǥʤ��ʤ� NG�Ȥ��ƥ����å���λ
        var allDetailSalesClassCodeHasSameValue = aryDetailSalesClassCode.every(function(element){ 
            return (element == aryDetailSalesClassCode[0]);
        });

        return allDetailSalesClassCodeHasSameValue;
    }

    // �ץ�ӥ塼���Х�ǡ����������å�
    function varidateBeforePreview(closedDay)
    {
        // �������٥��ꥢ�����٤���Ԥ�ʤ�
        if (!existsEditDetail()){
            alert("�������٤����򤵤�Ƥ��ޤ���");
            return false;
        }

        // �إå����եå�����Ǽ���������
        var deliveryDate = new Date($('input[name="dtmDeliveryDate"]').val());

        // Ǽ�����η���ѤߤǤ���
        if (isClosedMonthOfDeliveryDate(deliveryDate, closedDay)){
            alert("���ѤߤΤ��ᡢ���ꤵ�줿Ǽ������̵���Ǥ�");
            return false;
        }

        // Ǽ�����������ƥ����դ������������ˤʤ�
        if (!withinOneMonthBeforeAndAfter(deliveryDate)){
            alert("Ǽ�����Ϻ��������1����δ֤���ꤷ�Ƥ�������");
            return false;
        }

        // �������ٰ������ꥢ�����٤ˡ��إå�����Ǽ�����η��٤�Ʊ���٤ǤϤʤ�Ǽ�������٤�¸�ߤ���
        if (existsInDifferentDetailDeliveryMonthly(deliverDate, closedDay)){
            alert("�������٤ˤϡ����Ϥ��줿Ǽ�����Ȱۤʤ���Ǽ�ʤ��줿���٤����Ǥ��ޤ���");
            return false;
        }

        // �������ٰ������ꥢ�����ٳƹԤ�����ʬ�����������å�
        if (!checkEditDetailsAreSameSalesClass()){
            alert("����ʬ�κ��ߤ��Ǥ��ʤ����٤����򤵤�Ƥ��ޤ�");
            return false;
        }

        // �Х�ǡ����������
        return true;
    }

    // �ץ�ӥ塼���̤�ɽ������ʥХ�ǡ�����󤢤��
    function displayPreview(){

        // �ץ�ӥ塼���̤Υ�����ɥ�°�������
        var target = "previewWin";
        var features = "width=900,height=800,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no";

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
            console.log(data);

            //������Ψ��������ܹ���
            $('select[name="lngTaxRate"] > option').remove();
            $('select[name="lngTaxRate"]').append(data);

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



    // ����������ϥܥ��󲡲�
    $('#SearchBt').on('click', function(){

        //�������ٰ������ꥢ��1���ܤ�����ʬ�����ɤ��������
        var firstRowSalesClassCode = "";
        var firstTr = $("#EditTableBody tr").eq(0);
        if (0 < firstTr.length){
            firstRowSalesClassCode = $(firstTr).children('.detailSalesClassCode').text();
        }
        
        // Ǽ�ʽ����ٸ���������ϲ��̤�����ǳ���
        var url = "/sc/regist2/condition.php" + "?strSessionID=" + $('input[name="strSessionID"]').val();
        var data = {
            strSessionID: $('input[name="strSessionID"]').val(),
            //�ܵҥ����ɡ�ɽ���Ѳ�ҥ����ɡ�
            strcompanydisplaycode:   $('input[name="lngCustomerCode"]').val(),
            //�������ٰ������ꥢ��1���ܤ�����ʬ������
            lngsalesclasscode:       firstRowSalesClassCode,
          };
        
        var features = "width=710,height=460,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no";
        post_open(url, data, "conditionWin", features);
    });
    
    // �ɲåܥ���
    $('#AddBt').on('click', function(){
        
        var trArray = [];

        // ����Ԥ��ɲ�
        $("#DetailTableBody tr").each(function(index, tr){
            if ($(tr).attr('class') == "selected" || 
                $(tr).find('input[name="edit"]').prop('checked') == true){
                trArray.push(tr);
            }
        });

        if(trArray.length < 1){
            //alert("���ٹԤ����򤵤�Ƥ��ޤ���");
            return false;
        }

        // �ɲåХ�ǡ����������å�
        var invalid = false;
        $.each($(trArray), function(i, v){
            if (!invalid){
                invalid = !validateAdd($(v));
            }
        });
        if (invalid)
        {
            return false;
        }

        // �����ɲ�
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

    $('#DateBtB').on('click', function(){
        $('input[name="dtmDeliveryDate"]').focus();
    });
    $('#DateBtC').on('click', function(){
        $('input[name="dtmPaymentLimit"]').focus();
    });

    // �ץ�ӥ塼�ܥ��󲡲�
    $('#PreviewBt').on('click', function(){

        // POST�ǡ�������
        var data = {
            strMode :      "get-closedday",
            strSessionID:  $('input[name="strSessionID"]').val(),
            strcompanydisplaycode:   $('input[name="lngCustomerCode"]').val(),
        };
        
        // �ץ�ӥ塼���ΥХ�ǡ������ˡ��������פ�ɬ�פʤΤ�ajax�Ǽ�������
        $.ajax({
            type: 'POST',
            url: 'index.php',
            data: data,
            async: true,
        }).done(function(data){
            console.log("done:get-closedday");
            console.log(data);

            // ������
            var closedDay = data;

            // �ܵҥ����ɤ��б������������������Ǥ��ʤ�
            if (!closedDay){
                alert("�������������Ǥ��ޤ���");
                return false;
            }
            
            if (closedDay < 0){
                alert("������������ͤǤ���");
                return false;
            }
            
            // �ץ�ӥ塼����ɽ�����ΥХ�ǡ����������å�
            if (!varidateBeforePreview(closedDay)){
                return false;
            }

            // �ץ�ӥ塼����ɽ��
            displayPreview();
            
        }).fail(function(error){
            console.log("fail:get-closedday");
            console.log(error);
        });

    });
});