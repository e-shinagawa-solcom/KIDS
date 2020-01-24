//
// regist.js
//

// ------------------------------------------------------------------
//   Enter�����������٥��
// ------------------------------------------------------------------
window.document.onkeydown = fncEnterKeyDown;

function fncEnterKeyDown(e) {
    // Enter���������������ɲ�
    if (window.event.keyCode == 13) {
        if (document.activeElement.id == "BaseBack") {
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
    var trs = document.getElementById("tbl_detail").tBodies[0].getElementsByTagName("tr");
    indexes.sort(function (a, b) {
        return a - b;
    });

    for (var i = indexes[0]; i <= indexes[1]; i++) {
        trs[i - 1].className = 'selected';
        var checked = $(trs[i - 1]).find('input[name="edit"]').prop('checked');
        $(trs[i - 1]).find('input[name="edit"]').prop('checked', !checked);
    }
}

function clearAllSelected() {
    var trs = document.getElementById("tbl_detail").tBodies[0].getElementsByTagName("tr");
    for (var i = 0; i < trs.length; i++) {
        trs[i].className = '';
    }
}
// ------------------------------------------------------------------

// ------------------------------------------------------------------
//
//  ����������ϲ��̤���ƤФ��ؿ��ʸƤӽФ����� condition.js �򻲾ȡ�
//
// ------------------------------------------------------------------
// ����������ϲ��̤����Ϥ��줿�ͤ�����
function SetSearchConditionWindowValue(search_condition) {
    // POST��
    var postTarget = $('input[name="ajaxPostTarget"]').val();

    // �ܵ�
    $('input[name="lngCustomerCode"]').val(search_condition.strCompanyDisplayCode);
    $('input[name="strCustomerName"]').val(search_condition.strCompanyDisplayName);

    // �ܵҤ�ɳ�Ť��񥳡��ɤˤ�äƾ����Ƕ�ʬ�Υץ��������ѹ�����
    if (search_condition.strCompanyDisplayCode != "") {
        $.ajax({
            type: 'POST',
            url: postTarget,
            data: {
                strMode: "get-lngcountrycode",
                strSessionID: $('input[name="strSessionID"]').val(),
                strcompanydisplaycode: search_condition.strCompanyDisplayCode,
            },
            async: true,
        }).done(function (data) {
            console.log(data);
            console.log("done:get-lngcountrycode");
            if (data == "81") {
                // 81���ֳ��ǡפ������¾�ι��ܤ������ǽ��
                $("select[name='lngTaxClassCode'] option:not(:selected)").prop('disabled', false);
                $("select[name='lngTaxClassCode']").val("2");
                $('select[name="lngTaxRate"]').prop("selectedIndex", 1);

                $("input[name='dtmPaymentLimit']").prop('readonly', true)
                $("input[name='dtmPaymentLimit']").val("");
                $("select[name='lngPaymentMethodCode']").val("0");
                $("select[name='lngPaymentMethodCode'] option:not(:selected)").prop('disabled', true);

            } else {
                // 81�ʳ���������ǡ׸���
                $("select[name='lngTaxClassCode']").val("1");
                $("select[name='lngTaxClassCode'] option:not(:selected)").prop('disabled', true);
                $("select[name='lngPaymentMethodCode']").val("1");
                $("select[name='lngPaymentMethodCode'] option[value=0]").prop('disabled', true);

                $('select[name="lngTaxRate"]').val('');
                $("select[name='lngTaxRate'] option[value=0]").prop('disabled', true);

            }
        }).fail(function (error) {
            console.log("fail:get-lngcountrycode");
            console.log(error);
        });
    } else {
        // �ܵҥ����ɤ����ʤ������
        $("select[name='lngTaxClassCode'] option:not(:selected)").prop('disabled', false);
    }
}

// $('input[name="lngCustomerCode"]').addEventListener("click",function(e){ 
// 	e.preventDefault();
// });
// // �ܵҥ������ѹ��ν���
// $('input[name="lngCustomerCode"]').on("change", function () {
//     var msg = 'Ǽ���оݤ����٤򤹤٤ƥ��ꥢ���ޤ���\n������Ǥ�����';
//     console.log(msg);
//     var $tableA_rows = $('#tbl_detail tbody tr');
//     var $tableA_rows_length = $tableA_rows.length;

//     var warn = ($tableA_rows_length > 0) ? true : false;

//     if (warn && window.confirm(msg) === false) {
//         return;
//     }

//     $tableA_rows.remove();

//     $('#tbl_detail tbody tr');
// });

// ���ٸ���
function SearchReceiveDetail(search_condition) {
    // POST��
    var postTarget = $('input[name="ajaxPostTarget"]').val();

    // ��ʬ�񤭴����Τ���ajax��POST
    $.ajax({
        type: 'POST',
        url: postTarget,
        data: {
            strMode: "search-detail",
            strSessionID: $('input[name="strSessionID"]').val(),
            condition: search_condition,
        },
        async: true,
    }).done(function (data) {
        console.log("done:search-detail");
        console.log(data);
        // ������̤�ơ��֥�˥��å�

        var data = JSON.parse(data);

        $('#tbl_detail_chkbox tr').remove();
        $('#tbl_detail tr').remove();
        $('#tbl_edit_no_body tr').remove();
        $('#tbl_edit_detail_body tr').remove();

        $('#tbl_detail_chkbox').append(data.chkbox_body);
        $('#tbl_detail').append(data.detail_body);

        $('#tbl_detail_chkbox tbody tr td:nth-child(1)').width($('#tbl_detail_chkbox_head tr th:nth-child(1)').width());
        $('#tbl_detail_chkbox_head tr th:nth-child(1)').width($('#tbl_detail_chkbox_head tr th:nth-child(1)').width());

        resetTableAWidth();

        $("#DetailTable").trigger("update");
        // jQueryUI��tablesorter�ǥ���������
        $('#DetailTable').tablesorter({
            headers: {
                0: { sorter: false }
            }
        });

        $('input[name="allSel"]').on('change', function () {
            $('input[name="edit"]').prop('checked', this.checked);
            if (this.checked) {
                // $("#DetailTableBody tr").css('background-color','#87cefa');
                // $("#tbl_detail_chkbox tbody tr").css('background-color','#87cefa');
            } else {
                $("#DetailTableBody tr").css('background-color', '#ffffff');
                $("#tbl_detail_chkbox tbody tr").css('background-color', '#ffffff');
            }
        });

        $('input[name="strMonetaryUnitName"]').val(data.strmonetaryunitname);
        $('input[name="lngMonetaryUnitCode"]').val(data.lngmonetaryunitcode);

        setTableAEvent();

        // �̲��ѹ����٥��
        $('input[name="lngMonetaryUnitCode"]').on('change', function () {
            // �ꥯ����������
            $.ajax({
                url: '/pc/regist/getMonetaryRate.php',
                type: 'post',
                data: {
                    'strSessionID': $.cookie('strSessionID'),
                    'lngMonetaryUnitCode': $(this).val(),
                    'lngMonetaryRateCode': $('select[name="lngMonetaryRateCode"]').val(),
                    'dtmStockAppDate': $('input[name="dtmDeliveryDate"]').val()
                }
            })
                .done(function (response) {
                    console.log(response);
                    var data = JSON.parse(response);
                    $('input[name="curConversionRate"]').val(data.curconversionrate);
                })
                .fail(function (response) {
                    alert(response);
                    alert("fail");
                })
        });

        // �̲ߥ졼���ѹ����٥��
        $('select[name="lngMonetaryRateCode"]').on('change', function () {
            // �ꥯ����������
            $.ajax({
                url: '/pc/regist/getMonetaryRate.php',
                type: 'post',
                data: {
                    'strSessionID': $.cookie('strSessionID'),
                    'lngMonetaryUnitCode': $('input[name="lngMonetaryUnitCode"]').val(),
                    'lngMonetaryRateCode': $(this).val(),
                    'dtmStockAppDate': $('input[name="dtmDeliveryDate"]').val()
                }
            })
                .done(function (response) {
                    console.log(response);
                    var data = JSON.parse(response);
                    $('input[name="curConversionRate"]').val(data.curconversionrate);
                })
                .fail(function (response) {
                    alert("fail");
                })
        });

    }).fail(function (error) {
        console.log("fail:search-detail");
        console.log(error);
    });
}

function setCheckBoxEvent() {
    $('input[name="edit"]').on('click', function () {
        var rowindex = $(this).parent().parent().index();
        console.log(rowindex);
        setRowBackGroundColor(rowindex, this.checked);
        console.log(this.checked);
        $(this).prop('checked', this.checked);
    });

}
function resetTableAWidth() {
    var thwidthArry = [];
    var tdwidthArry = [];
    var columnNum = $('#tbl_detail_head tr th').length;
    console.log(columnNum);
    for (var i = 1; i <= columnNum; i++) {
        var thwidth = $('#tbl_detail_head tr th:nth-child(' + i + ')').width();
        var tdwidth = $('#tbl_detail tbody tr td:nth-child(' + i + ')').width();
        thwidthArry.push(thwidth + 2);
        tdwidthArry.push(tdwidth + 2);
    }
    console.log(thwidthArry);
    console.log(tdwidthArry);


    for (var i = 1; i <= columnNum; i++) {
        if (thwidthArry[i - 1] > tdwidthArry[i - 1]) {
            $("#tbl_detail_head tr th:nth-child(" + i + ")").width(thwidthArry[i - 1]);
            $("#tbl_detail tbody tr td:nth-child(" + i + ")").width(thwidthArry[i - 1]);
        } else {
            $("#tbl_detail_head tr th:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
            $("#tbl_detail tbody tr td:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
        }
    }
}

function resetTableBWidth() {
    var thwidthArry = [];
    var tdwidthArry = [];
    var columnNum = $('#tbl_edit_detail_head thead tr th').length;
    console.log(columnNum);
    for (var i = 1; i <= columnNum; i++) {
        var thwidth = $('#tbl_edit_detail_head thead tr th:nth-child(' + i + ')').width();
        var tdwidth = $('#tbl_edit_detail_body tbody tr td:nth-child(' + i + ')').width();
        thwidthArry.push(thwidth + 1);
        tdwidthArry.push(tdwidth + 1);
    }

    for (var i = 1; i <= columnNum; i++) {
        if (thwidthArry[i - 1] > tdwidthArry[i - 1]) {
            $("#tbl_edit_detail_head thead tr th:nth-child(" + i + ")").width(thwidthArry[i - 1]);
            $("#tbl_edit_detail_body tbody tr td:nth-child(" + i + ")").width(thwidthArry[i - 1]);
        } else {
            $("#tbl_edit_detail_head thead tr th:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
            $("#tbl_edit_detail_body tbody tr td:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
        }
    }
}
function setTableAEvent() {
    $("#DetailTableBody tr, #tbl_detail_chkbox tbody tr").on('click', function () {
        var rowindex = $(this).index();
        console.log(rowindex);
        var checked = $("#tbl_detail_chkbox tbody tr:nth-child(" + (rowindex + 1) + ")").find('td').find('input[name="edit"]').prop('checked');
        console.log(checked);
        setRowBackGroundColor(rowindex, checked);
    });
}

function setRowBackGroundColor(rowindex, chkBoxStatus) {
    if (!chkBoxStatus) {
        $("#tbl_detail tbody tr:nth-child(" + (rowindex + 1) + ")").css('background-color', '#87cefa');
        $("#tbl_detail_chkbox tbody tr:nth-child(" + (rowindex + 1) + ")").css('background-color', '#87cefa');
        $("#tbl_detail_chkbox tbody tr:nth-child(" + (rowindex + 1) + ")").find('td').find('input[name="edit"]').prop('checked', true);
    } else {
        $("#tbl_detail tbody tr:nth-child(" + (rowindex + 1) + ")").css('background-color', '#ffffff');
        $("#tbl_detail_chkbox tbody tr:nth-child(" + (rowindex + 1) + ")").css('background-color', '#ffffff');
        $("#tbl_detail_chkbox tbody tr:nth-child(" + (rowindex + 1) + ")").find('td').find('input[name="edit"]').prop('checked', false);
    }

}
// �������٤򤹤٤ƥ��ꥢ
function ClearAllEditDetail() {
    // ������ܥ��󥯥�å����ư�ǵ�ư
    $("#AllDeleteBt").trigger('click');
}
// ------------------------------------------------------------------

// ------------------------------------------
//   HTML�������������ν������
// ------------------------------------------
jQuery(function ($) {

    // ��ʧ���¤�����
    var now = new Date();
    now.setMonth(now.getMonth() + 1);
    $('input[name="dtmPaymentLimit"]').val(now.getFullYear() + "/" + ("00" + (now.getMonth() + 1)).slice(-2) + "/" + ("00" + now.getDate()).slice(-2));
    // ������Ψ������
    var taxClassCode = $('select[name="lngTaxClassCode"]').children('option:selected').val();
    if (taxClassCode == 1) {
        $('select[name="lngTaxRate"]').val('');
    } else {
        $('select[name="lngTaxRate"]').prop("selectedIndex", 1);
    }


    if ($('input[name="lngSlipNo"]').val().length > 0) {
        window.opener.$('input[name="locked"]').val("1");
    }
    // // ����������������
    // $("#EditTableBody").sortable();
    // $("#EditTableBody").on('sortstop', function () {
    //     changeRowNum();
    // });

    // ����̾�إå������ڤ��ؤ�
    if ($('input[name="lngSlipNo"]').val()) {
        $('#SegAHeader').text('����Ǽ�ʽ�˽���');
    } else {
        $('#SegAHeader').text('����Ǽ�ʽ����Ͽ');
    }

    // datepicker�о�����
    var dateElements = [
        // Ǽ����
        $('input[name="dtmDeliveryDate"]'),
        // ��ʧ����
        $('input[name="dtmPaymentLimit"]'),
    ];
    // datepicker������
    $.each(dateElements, function () {
        this.datepicker({
            showButtonPanel: true,
            dateFormat: "yy/mm/dd",
            onClose: function () {
                this.focus();
            }
        }).attr({
            maxlength: "10"
        });
    });

    // ��׶�ۡ������ǳۤι���
    updateAmount();

    // ------------------------------------------
    //   functions
    // ------------------------------------------
    // �ɲåХ�ǡ����������å�
    function validateAdd(tr) {
        //�������򥨥ꥢ��Ǽ��
        var detailDeliveryDate = new Date($(tr).children('td.detailDeliveryDate').text());

        //�إå����եå�����Ǽ��������ӡ�Ʊ��ʳ���������
        var headerDeliveryDate = new Date($('input[name="dtmDeliveryDate"]').val());
        var sameMonth = (headerDeliveryDate.getYear() == detailDeliveryDate.getYear())
            && (headerDeliveryDate.getMonth() == detailDeliveryDate.getMonth());
        if (!sameMonth) {
            alert("����������Ǽ����Ǽ�����Ȱ��פ��ޤ��󡣼���ǡ����������Ƥ���������");
            return false;
        }

        //�������٤���Ƭ�Ԥ�����Ф���Ǽ������ӡ�Ʊ��ʳ���������
        var firstTr = $("#EditTableBody tr").eq(0);
        if (0 < firstTr.length) {
            var firstRowDate = new Date($(firstTr).children('td.detailDeliveryDate').text());
            var sameMonthDetail = (firstRowDate.getYear() == detailDeliveryDate.getYear())
                && (firstRowDate.getMonth() == detailDeliveryDate.getMonth());
            if (!sameMonthDetail) {
                alert("�������٤�Ǽ�ʷ�ۤʤ����٤�����Ǥ��ޤ���");
                return false;
            }
        }

        // //��ʣ�������٤��ɲä�ػߡʽ�ʣȽ�ꡧ�������٤Υ�����
        // var existsSameKey = false;
        // var isSame = false;
        // var rn1 = $(tr).children('td.detailReceiveNo').text();
        // var dn1 = $(tr).children('td.detailReceiveDetailNo').text();
        // var rev1 = $(tr).children('td.detailReceiveRevisionNo').text();

        // $("#EditTableBody tr").each(function () {
        //     var rn2 = $(this).children('td.detailReceiveNo').text();
        //     var dn2 = $(this).children('td.detailReceiveDetailNo').text();
        //     var rev2 = $(this).children('td.detailReceiveRevisionNo').text();

        //     if ((rn1 == rn2) && (dn1 == dn2) && (rev1 == rev2)) {
        //         isSame = true;
        //     } else {
        //         existsSameKey = false;
        //     }
        //     var isSame = (rn1 == rn2) && (dn1 == dn2) && (rev1 == rev2);
        //     existsSameKey = existsSameKey || isSame;
        // });

        // if (existsSameKey) {
        //     alert("��ʣ�������٤�����Ǥ��ޤ���");
        //     return false;
        // }

        return true;
    }


    // ��׶�ۡ������ǳۤι���
    function updateAmount() {

        // ��ȴ��ۤ������������˳�Ǽ
        var aryPrice = [];
        $("#EditTableBody tr").each(function () {
            //����ޤ�Ϥ����Ƥ�����ͤ��Ѵ�
            var price = Number($(this).find($('td.detailSubTotalPrice')).html().split(',').join(''));
            aryPrice.push(price);
        });

        // ----------------
        // ��׶�ۤλ���
        // ----------------
        var totalAmount = 0;
        aryPrice.forEach(function (price) {
            totalAmount += price;
        });

        // ----------------
        // �����ǳۤλ���
        // ----------------
        // �����Ƕ�ʬ�����
        var taxClassCode = $('select[name="lngTaxClassCode"]').children('option:selected').val();

        // ������Ψ�����
        var taxRate = Number($('select[name="lngTaxRate"]').children('option:selected').text().replace("%", "")) * 0.01;

        console.log(taxRate);

        // �����ǳۤη׻�
        var taxAmount = 0;
        if (taxClassCode == "1") {
            // 1:�����
            taxAmount = 0;
        }
        else if (taxClassCode == "2") {
            // 2:����
            taxAmount = Math.floor(totalAmount * taxRate);
        }
        else if (taxClassCode == "3") {
            // 3:����
            aryPrice.forEach(function (price) {
                taxAmount += Math.floor((price / (1 + taxRate)) * taxRate);
            });
        }

        // ------------------
        // �ե�������ͤ�����
        // ------------------
        $('input[name="strTotalAmount"]').val(convertNumber(totalAmount, 4));
        $('input[name="strTaxAmount"]').val(convertNumber(taxAmount, 4));
    }

    function getCheckedRows() {
        var selected = getSelectedRows();
        var cnt = $(selected).length;
        if (cnt === 0) {
            console.log("�ʤˤ⤷�ʤ�");
            return false;
        }
        if (cnt > 1) {
            //console.log("�ʤˤ⤷�ʤ�����������ǽ������ɲäˤʤ뤫�⤷��ʤ���");
            alert("��ư�оݤ�1�ԤΤ����򤷤Ƥ�������");
            return false;
        }
        return true;
    }
    function getSelectedRows() {
        return $('#EditTableBody tr.selected');
    }
    function executeSort(mode) {
        var row = $('#EditTableBody').children('.selected');
        switch (mode) {
            case 0:
                $('#EditTableBody tr:first').before($(row));
                break;
            case 1:
                var rowPreview = $(row).prev('tr');
                if (row.prev.length) {
                    row.insertBefore(rowPreview);
                    var td = rowPreview.children('td[name="rownum"]')
                }
                break;
            case 2:
                var rowNext = $(row).next('tr');
                if (rowNext.length) {
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
    function changeRowNum() {
        $('#EditTableBody').find('[name="rownum"]').each(function (idx) {
            $(this).html(idx + 1);
        });
    }

    function isDate(d) {
        if (d == "") { return false; }
        if (!d.match(/^\d{4}\/\d{1,2}\/\d{1,2}$/)) { return false; }

        var date = new Date(d);
        if (date.getFullYear() != d.split("/")[0]
            || date.getMonth() != d.split("/")[1] - 1
            || date.getDate() != d.split("/")[2]
        ) {
            return false;
        }
        return true;
    }

    // �إå������եå������ϥ��ꥢ��POST�ѥǡ�������
    function getUpdateHeader() {

        var result = {
            //��ɼ��
            strdrafteruserdisplaycode: $('input[name="lngInsertUserCode"]').val(),
            strdrafteruserdisplayname: $('input[name="strInsertUserName"]').val(),
            //�ܵ�
            strcompanydisplaycode: $('input[name="lngCustomerCode"]').val(),
            strcompanydisplayname: $('input[name="strCustomerName"]').val(),
            //�ܵ�ô����
            strcustomerusername: $('input[name="strCustomerUserName"]').val(),
            //Ǽ����
            dtmdeliverydate: $('input[name="dtmDeliveryDate"]').val(),
            //Ǽ����
            strdeliveryplacecompanydisplaycode: $('input[name="lngDeliveryPlaceCode"]').val(),
            strdeliveryplacename: $('input[name="strDeliveryPlaceName"]').val(),
            //Ǽ����ô����
            strdeliveryplaceusername: $('input[name="strDeliveryPlaceUserName"]').val(),
            //����
            strnote: $('input[name="strNote"]').val(),
            //�����Ƕ�ʬ
            lngtaxclasscode: $('select[name="lngTaxClassCode"]').children('option:selected').val(),
            strtaxclassname: $('select[name="lngTaxClassCode"]').children('option:selected').text(),
            //������Ψ
            lngtaxcode: $('select[name="lngTaxRate"]').children('option:selected').val(),
            curtax: $('select[name="lngTaxRate"]').children('option:selected').text().replace("%", "") * 0.01,
            //�����ǳ�
            strtaxamount: Number(($('input[name="strTaxAmount"]').val()).split(',').join('')),
            //��ʧ����
            dtmpaymentlimit: $('input[name="dtmPaymentLimit"]').val(),
            //��ʧ��ˡ
            lngpaymentmethodcode: $('select[name="lngPaymentMethodCode"]').children('option:selected').val(),
            //��ʧ��ˡ
            strpaymentmethodname: $('select[name="lngPaymentMethodCode"]').children('option:selected').text(),
            //��׶��
            curtotalprice: Number(($('input[name="strTotalAmount"]').val()).split(',').join('')),
        };

        return result;
    }

    // �������ٰ������ꥢ��POST�ѥǡ�������
    function getUpdateDetail() {
        var result = [];

        $.each($('#EditTableBody tr'), function (i, tr) {

            var param = {
                //No.�ʹ��ֹ��
                rownumber: $(tr).children('[name="rownum"]').text(),
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
        for (var x in data) {
            if (data[x] == undefined || data[x] == null) {
                continue;
            }
            var _val = data[x].replace(/'/g, "\'");
            html += "<input type='hidden' name='" + x + "' value='" + _val + "' >";
        }
        html += '</form>';
        $("body").append(html);

        $('#temp_form').attr("action", url);
        $('#temp_form').attr("target", target);
        $('#temp_form').attr("method", "POST");
        $('#temp_form').submit();

        // �ե��������
        $('#temp_form').remove();
    }

    // --------------------------------------------------------------------------------
    //   ���շ׻��إ�Ѵؿ�
    // --------------------------------------------------------------------------------
    // n���������ǯ�������������
    function getAddMonthDate(year, month, day, add) {
        var addMonth = month + add;
        var endDate = getEndOfMonth(year, addMonth);//addʬ��ä�����κǽ��������

        //�������Ϥ��줿���դ�n�����κǽ�������礭��������դ򼡷�ǽ����˹�碌��
        //5/31��6/30�Τ褦�˱�������̵������ɬ��
        if (day > endDate) {
            day = endDate;
        } else {
            day = day - 1;
        }

        var addMonthDate = new Date(year, addMonth - 1, day);
        return addMonthDate;
    }
    //����η����������
    //�������0���ܡả��������ˤʤ�
    function getEndOfMonth(year, month) {
        var endDate = new Date(year, month, 0);
        return endDate.getDate();
    }

    // ���������Ȥ˷��٤�׻�����
    function getMonthlyBasedOnClosedDay(targetDate, closedDay) {
        var targetYear = targetDate.getFullYear();
        var targetMonth = targetDate.getMonth() + 1;
        var targetDay = targetDate.getDate();

        if (targetDay > closedDay) {
            // �о��� > ������ �ʤ������
            return getAddMonthDate(targetYear, targetMonth, 1, +1);
        } else {
            // �о��� <= ������ �ʤ�������
            return new Date(targetYear, targetMonth, 1);
        }
    }
    // --------------------------------------------------------------------------------

    // ------------------------------------------
    //    �Х�ǡ�������Ϣ
    // ------------------------------------------
    // �������٥��ꥢ�����٤�1�԰ʾ�¸�ߤ���ʤ� true
    function existsEditDetail() {
        return $("#EditTableBody tr").length > 0;
    }

    // Ǽ�����η���ѤߤǤ���ʤ� true
    function isClosedMonthOfDeliveryDate(deliveryDate, closedDay) {
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
    function withinOneMonthBeforeAndAfter(targetDate) {
        // �����ƥ�����
        var nowDate = new Date();
        var nowYear = nowDate.getFullYear();
        var nowMonth = nowDate.getMonth() + 1;
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
    function existsInDifferentDetailDeliveryMonthly(deliveryDate, closedDay) {

        // Ǽ�����η���
        var deliveryMonthly = getMonthlyBasedOnClosedDay(deliveryDate, closedDay);

        // �����٤�Ǽ���η��٤��������
        var aryDetailDeliveryMonthly = [];
        $("#EditTableBody tr").each(function () {
            // ���٤�Ǽ�������
            var arr = $(this).children('td.detailDeliveryDate').text().split('/');
            var detailDeliveryDate = new Date(arr[0], arr[1] - 1, arr[2]);

            // Ǽ���η���
            var detailDeliveryMonthly = getMonthlyBasedOnClosedDay(detailDeliveryDate, closedDay);
            // ������ɲ�
            aryDetailDeliveryMonthly.push(detailDeliveryMonthly);
        });

        // Ǽ���η��٤�Ǽ�����η��٤Ȱ��פ��ʤ����٤����ĤǤ⤢�ä��饨�顼
        var indifferentDetailExists = aryDetailDeliveryMonthly.some(function (element) {
            return (element.getTime() != deliveryMonthly.getTime());
        });

        return indifferentDetailExists;
    }

    // �������٥��ꥢ�γ����٤�����ʬ��������������å��������å�OK�ʤ�true��NG�ʤ�false
    function checkEditDetailsAreSameSalesClass() {
        // �������٥��ꥢ�ˤ��뤹�٤Ƥ����٤���������ե饰������ʬ�����ɤ��������
        var aryDetailUnifiedFlg = [];
        var aryDetailSalesClassCode = [];
        $("#EditTableBody tr").each(function () {
            // ��������ե饰���������������ɲ�
            aryDetailUnifiedFlg.push($(this).children('td.detailUnifiedFlg').text());
            // ����ʬ�����ɤ��������������ɲ�
            aryDetailSalesClassCode.push($(this).children('td.detailSalesClassCode').text());
        });

        // �������Ԥ�����ʬ�ޥ�������������ե饰��false -> OK�Ȥ��ƥ����å���λ�������Ǥʤ��ʤ飲����
        var allDetailUnifiedFlgIsFalse = aryDetailUnifiedFlg.every(function (element) {
            return (element.toUpperCase() == 'F');
        });
        if (allDetailUnifiedFlgIsFalse) {
            return true;
        }

        // ��������ʬ�ޥ�������������ե饰��true�Ǥ������٤η�� != �������ٰ������ꥢ�����ٹԿ� ���������ʤ� NG�Ȥ��ƥ����å���λ�������Ǥʤ��ʤ飳����
        var aryDetailUnifiedFlgIsTrue = aryDetailUnifiedFlg.filter(function (element) {
            return (element.toUpperCase() == 'T');
        });
        if (aryDetailUnifiedFlgIsTrue.length != $("#EditTableBody tr").length) {
            return false;
        }

        // �����������ٰ������ꥢ�����٤�����ʬ�����ɤ����٤�Ʊ���� -> OK�Ȥ��ƥ����å���λ�������Ǥʤ��ʤ� NG�Ȥ��ƥ����å���λ
        var allDetailSalesClassCodeHasSameValue = aryDetailSalesClassCode.every(function (element) {
            return (element == aryDetailSalesClassCode[0]);
        });

        return allDetailSalesClassCodeHasSameValue;
    }

    // �ץ�ӥ塼���Х�ǡ����������å�
    function varidateBeforePreview(closedDay) {
        // �������٥��ꥢ�����٤���Ԥ�ʤ�
        if (!existsEditDetail()) {
            alert("�������٤����򤵤�Ƥ��ޤ���");
            return false;
        }

        // �إå����եå�����Ǽ���������
        var deliveryDate = new Date($('input[name="dtmDeliveryDate"]').val());

        // Ǽ�����η���ѤߤǤ���
        if (isClosedMonthOfDeliveryDate(deliveryDate, closedDay)) {
            alert("���ѤߤΤ��ᡢ���ꤵ�줿Ǽ������̵���Ǥ�");
            return false;
        }

        // Ǽ�����������ƥ����դ������������ˤʤ�
        if (!withinOneMonthBeforeAndAfter(deliveryDate)) {
            alert("Ǽ�����Ϻ��������1����δ֤���ꤷ�Ƥ�������");
            return false;
        }

        // �������ٰ������ꥢ�����٤ˡ��إå�����Ǽ�����η��٤�Ʊ���٤ǤϤʤ�Ǽ�������٤�¸�ߤ���
        if (existsInDifferentDetailDeliveryMonthly(deliveryDate, closedDay)) {
            alert("�������٤ˤϡ����Ϥ��줿Ǽ�����Ȱۤʤ���Ǽ�ʤ��줿���٤����Ǥ��ޤ���");
            return false;
        }

        // �������ٰ������ꥢ�����ٳƹԤ�����ʬ�����������å�
        if (!checkEditDetailsAreSameSalesClass()) {
            alert("����ʬ�κ��ߤ��Ǥ��ʤ����٤����򤵤�Ƥ��ޤ�");
            return false;
        }

        // �Х�ǡ����������
        return true;
    }

    // �ץ�ӥ塼���̤�ɽ������ʥХ�ǡ�����󤢤��
    function displayPreview() {

        // �ץ�ӥ塼���̤Υ�����ɥ�°�������
        var target = "previewWin";
        var features = "width=900,height=800,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no";

        // ��˶��Υ�����ɥ��򳫤��Ƥ���
        var emptyWin = window.open('', target, features);

        // POST�ǡ�������
        var data = {
            strMode: "display-preview",
            strSessionID: $('input[name="strSessionID"]').val(),
            lngRenewTargetSlipNo: $('input[name="lngSlipNo"]').val(),
            lngRenewTargetRevisionNo: $('input[name="lngRevisionNo"]').val(),
            strRenewTargetSlipCode: $('input[name="strSlipCode"]').val(),
            lngRenewTargetSalesNo: $('input[name="lngSalesNo"]').val(),
            strRenewTargetSalesCode: $('input[name="strSalesCode"]').val(),
            aryHeader: getUpdateHeader(),
            aryDetail: getUpdateDetail(),
        };

        $.ajax({
            type: 'POST',
            url: 'preview.php',
            data: data,
            async: true,
        }).done(function (data) {
            console.log("done");
            console.log(data);

            var url = "/sc/regist2/preview.php" + "?strSessionID=" + $('input[name="strSessionID"]').val();
            var previewWin = window.open('', target, features);
            previewWin.document.open();
            previewWin.document.write(data);
            previewWin.document.close();

            //���ɤ߹��ߤʤ��ǥ��ɥ쥹�С���URL�Τ��ѹ�
            emptyWin.history.pushState(null, null, url);

        }).fail(function (error) {
            console.log("fail");
            console.log(error);
            emptyWin.close();
        });
    }

    // ------------------------------------------
    //   events
    // ------------------------------------------
    $("select[name='lngTaxClassCode']").on('change', function () {
        // �����Ƕ�ʬ�����
        var taxClassCode = $('select[name="lngTaxClassCode"]').children('option:selected').val();
        if (taxClassCode == 1) {
            // $('select[name="lngTaxRate"]').append('<option value=""></option>');
            $('select[name="lngTaxRate"]').prop("selectedIndex", 0);
        } else {
            $('select[name="lngTaxRate"]').prop("selectedIndex", 1);
        }

        updateAmount();


    });

    $('select[name="lngTaxRate"]').on('change', function () {
        updateAmount();
    });

    $('input[name="dtmDeliveryDate"]').on('change', function () {

        // POST��
        var postTarget = $('input[name="ajaxPostTarget"]').val();

        //������Ψ����������ѹ�
        $.ajax({
            type: 'POST',
            url: postTarget,
            data: {
                strMode: "change-deliverydate",
                strSessionID: $('input[name="strSessionID"]').val(),
                dtmDeliveryDate: $(this).val(),
            },
            async: true,
        }).done(function (data) {
            console.log("done:change-deliverydate");
            console.log(data);

            //������Ψ��������ܹ���
            $('select[name="lngTaxRate"] > option').remove();
            $('select[name="lngTaxRate"]').append(data);

            //��ۤι���
            updateAmount();

        }).fail(function (error) {
            console.log("fail:change-deliverydate");
            console.log(error);
        });

    });


    // $('input[id="DetailTableBodyAllCheck"').on({
    //     'click': function () {
    //         alert("test");
    //         var status = this.checked;
    //         $('input[type="checkbox"][name="edit"]')
    //             .each(function () {
    //                 this.checked = status;
    //             });
    //     }
    // });


    // ����������ϥܥ��󲡲�
    $('img.search').on('click', function () {

        //�������ٰ������ꥢ��1���ܤ�����ʬ�����ɤ��������
        var firstRowSalesClassCode = "";
        var firstTr = $("#EditTableBody tr").eq(0);
        if (0 < firstTr.length) {
            firstRowSalesClassCode = $(firstTr).children('.detailSalesClassCode').text();
        }

        // Ǽ�ʽ����ٸ���������ϲ��̤�����ǳ���
        var url = "/sc/regist2/condition.php" + "?strSessionID=" + $('input[name="strSessionID"]').val();
        var data = {
            strSessionID: $('input[name="strSessionID"]').val(),
            //�ܵҥ����ɡ�ɽ���Ѳ�ҥ����ɡ�
            strcompanydisplaycode: $('input[name="lngCustomerCode"]').val(),
            //�������ٰ������ꥢ��1���ܤ�����ʬ������
            lngsalesclasscode: firstRowSalesClassCode,
        };

        var features = "width=710,height=460,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no";
        post_open(url, data, "conditionWin", features);
    });

    // �ɲåܥ���
    $('img.add').on('click', function () {

        var trArray = [];

        // ����Ԥ��ɲ�
        $("#tbl_detail_chkbox tbody tr").each(function (index, tr) {
            if ($(tr).find('input[name="edit"]').prop('checked') == true) {
                trArray.push(tr);
            }
        });

        if (trArray.length < 1) {
            // alert("���ٹԤ����򤵤�Ƥ��ޤ���");
            return false;
        }

        // DBG:��������ȥ������о�
        // �ɲåХ�ǡ����������å�
        var invalid = false;
        $.each($(trArray), function (i, v) {
            if (!invalid) {
                invalid = !validateAdd($(v));
            }
        });
        if (invalid) {
            return false;
        }

        // �����ɲ�        
        $('#tbl_detail_chkbox tbody tr').each(function (i, e) {
            var rownum = i + 1;
            var chkbox = $(this).find('input[type="checkbox"]');
            if (chkbox.prop("checked")) {
                var rn1 = $('#tbl_detail tbody tr:nth-child(' + rownum + ')').find('td.detailReceiveNo').text();
                var dn1 = $('#tbl_detail tbody tr:nth-child(' + rownum + ')').find('td.detailReceiveDetailNo').text();
                var rev1 = $('#tbl_detail tbody tr:nth-child(' + rownum + ')').find('td.detailReceiveRevisionNo').text();

                console.log(rn1);
                console.log(dn1);
                console.log(rev1);
                var addObj = true;
                $('#tbl_edit_detail_body tbody tr').each(function (i, e) {
                    var rn2 = $(this).find('td.detailReceiveNo').text();
                    var dn2 = $(this).find('td.detailReceiveDetailNo').text();
                    var rev2 = $(this).find('td.detailReceiveRevisionNo').text();

                    if ((rn1 == rn2) && (dn1 == dn2) && (rev1 == rev2)) {
                        addObj = false;
                        return false;
                    }
                });
                console.log(addObj);
                if (addObj) {
                    // tableB���ɲ�
                    $("#tbl_edit_detail_body tbody").append('<tr>' + $('#tbl_detail tbody tr:nth-child(' + rownum + ')').html() + '</tr>');
                    var no = $("#tbl_edit_no_body tbody").find('tr').length + 1;
                    $("#tbl_edit_no_body tbody").append('<tr><td>' + no + '</td></tr>');

                    var lasttr = $("#tbl_edit_detail_body").find('tr').last();
                    lasttr.find('td:nth-child(1)').css('display', 'none');
                }

            }
        });

        $('#tbl_edit_no_body tbody tr td').width($('#tbl_edit_no_head thead tr th').width());

        resetTableBWidth();

        // tableB�Υꥻ�å�
        for (var i = $('#tbl_detail_chkbox tbody tr').length; i > 0; i--) {
            var row = $('#tbl_detail_chkbox tbody tr:nth-child(' + i + ')');
            var chkbox = row.find('input[type="checkbox"]');
            if (chkbox.prop("checked")) {
                row.remove();
                $('#tbl_detail tbody tr:nth-child(' + i + ')').remove();
            }
        }

        $('#tbl_detail tbody tr').each(function (i, e) {
            $(this).find('td:nth-child(1)').text(i + 1);
        });

        selectRow($("#tbl_edit_no_body"), $("#tbl_edit_detail_body"));

        // ��׶�ۡ������ǳۤι���
        updateAmount();
    });


    // ��ID�κ�����
    function resetTableBRowid() {
        var rownum = 0;
        $("#tbl_edit_no_body tbody tr").each(function (i, e) {
            rownum += 1;
            $(this).find('td').first().text(rownum);
        });
    }



    // ��ID�κ�����
    function resetTableARowid() {
        var rownum = 0;
        $("#tbl_detail tbody tr").each(function (i, e) {
            rownum += 1;
            $(this).find('td').first().text(rownum);
        });
    }

    function selectRow(objA, objB) {
        var rows = objA.find('tbody tr');
        var rows = objB.find('tbody tr');
        var lastSelectedRow;
        /* Create 'click' event handler for rows */
        objA.find('tbody tr').on('click', function (e) {
            lastSelectedRow = trClickEvent($(this), lastSelectedRow, e, objA, objB);
        });


        /* Create 'click' event handler for rows */
        objB.find('tbody tr').on('click', function (e) {
            lastSelectedRow = trClickEvent($(this), lastSelectedRow, e, objA, objB);
        });

        /* This 'event' is used just to avoid that the table text 
         * gets selected (just for styling). 
         * For example, when pressing 'Shift' keyboard key and clicking 
         * (without this 'event') the text of the 'table' will be selected.
         * You can remove it if you want, I just tested this in 
         * Chrome v30.0.1599.69 */
        $(document).bind('selectstart dragstart', function (e) {
            e.preventDefault(); return false;
        });
    }

    function trClickEvent(row, lastSelectedRow, e, objA, objB) {

        /* Check if 'Ctrl', 'cmd' or 'Shift' keyboard key was pressed
         * 'Ctrl' => is represented by 'e.ctrlKey' or 'e.metaKey'
         * 'Shift' => is represented by 'e.shiftKey' */
        if (e.ctrlKey || e.metaKey) {
            /* If pressed highlight the other row that was clicked */
            objA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#87cefa");
            objB.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#87cefa");

        } else if (e.shiftKey) {
            /* If pressed highlight the other row that was clicked */
            var indexes = [lastSelectedRow.index(), row.index()];
            indexes.sort(function (a, b) {
                return a - b;
            });
            for (var i = indexes[0]; i <= indexes[1]; i++) {
                objA.find("tbody tr:nth-child(" + (i + 1) + ")").css("background-color", "#87cefa");
                objB.find("tbody tr:nth-child(" + (i + 1) + ")").css("background-color", "#87cefa");
            }
        } else {
            /* Otherwise just highlight one row and clean others */
            objA.find("tbody tr").css("background-color", "#ffffff");
            objA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#87cefa");
            objB.find("tbody tr").css("background-color", "#ffffff");
            objB.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#87cefa");
            lastSelectedRow = row;
        }

        return lastSelectedRow;
    }

    // $('body').on('click', '#EditTableBody tr', function (e) {
    //     var tds = $(e.currentTarget).children('td');
    //     var checked = $(tds).hasClass('selected');
    //     if (checked) {
    //         $(tds).removeClass('selected');
    //         $(this).removeClass('selected');
    //     } else {
    //         $(tds).addClass('selected');
    //         $(this).addClass('selected');
    //     }
    // });


    // ������ܥ���Υ��٥��
    $('img.alldelete').on('click', function () {

        $("#tbl_edit_detail_body tbody tr").each(function (i, e) {
            removeTableBToTableA($(this));
        });

        $("#tbl_edit_no_body tbody").empty();

        resetTableARowid();
        resetTableAWidth();

        $('input[type="checkbox"][name="allSel"]').prop("checked", false);

        setCheckBoxEvent();

        setTableAEvent();
    });

    // ����ܥ���Υ��٥��
    $('img.delete').on('click', function () {
        $("#tbl_edit_no_body tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).remove();
            }
        });
        $("#tbl_edit_detail_body tbody tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                removeTableBToTableA($(this));
            }
        });

        resetTableARowid();
        resetTableBRowid();
        resetTableAWidth();

        $('input[type="checkbox"][name="allSel"]').prop("checked", false);

        setCheckBoxEvent();

        setTableAEvent();
    });

    function removeTableBToTableA(tableBRow) {
        var trhtml = tableBRow.html();
        var detailnoB = tableBRow.find('.detailReceiveNo').text();
        console.log(detailnoB);
        var rownum = 0;
        $("#tbl_detail tbody tr").each(function (i, e) {
            var detailnoA = $(this).find('.detailReceiveNo').text();
            if (detailnoA > detailnoB) {
                rownum = i + 1;
                return false;
            }
        });
        if (rownum == 0) {
            $('#tbl_detail tbody').append('<tr>' + trhtml + '</tr>');
            $('#tbl_detail_chkbox tbody').append('<tr><td style="text-align:center;"><input type="checkbox" name="edit" style="width: 10px;"></td></tr>');
            rownum = $("#tbl_detail tbody tr").length;
        } else {
            $('#tbl_detail tbody tr:nth-child(' + rownum + ')').before('<tr>' + trhtml + '</tr>');
            $('#tbl_detail_chkbox tbody tr:nth-child(' + rownum + ')').before('<tr><td style="text-align:center;"><input type="checkbox" name="edit" style="width: 10px;"></td></tr>');
        }

        $('#tbl_detail tbody tr:nth-child(' + (rownum) + ') td:nth-child(1)').width($("#tbl_detail_head thead tr th:nth-child(1)").width());
        console.log($('#tbl_detail_chkbox tbody tr:nth-child(' + (rownum) + ') td:nth-child(1)').width());
        console.log($('#tbl_detail_chkbox_head thead tr th:nth-child(1').width());

        $('#tbl_detail_chkbox tbody tr:nth-child(' + (rownum) + ') td:nth-child(1)').width($("#tbl_detail_chkbox_head tr th:nth-child(1)").width());
        $('#tbl_detail tbody tr:nth-child(' + (rownum) + ')').find('td:nth-child(1)').css('display', '');

        tableBRow.remove();
    }



    $('#selectup').on('click', function () {
        var selected = getCheckedRows();
        if (!selected) { return false; }
        executeSort(0);
    });
    $('#selectup1').on('click', function () {
        var selected = getCheckedRows();
        if (!selected) { return false; }
        executeSort(1);
    });
    $('#selectdown1').on('click', function () {
        var selected = getCheckedRows();
        if (!selected) { return false; }
        executeSort(2);
    });
    $('#selectdown').on('click', function () {
        var selected = getCheckedRows();
        if (!selected) { return false; }
        executeSort(3);
    });
    $("#DeleteBt").on('click', function () {
        var selected = getSelectedRows();
        if (!selected.length) { return false; }
        $(selected).remove();
        changeRowNum();
        updateAmount();
    });
    $('#AllDeleteBt').on('click', function () {
        $('#EditTableBody').empty();
        updateAmount();
    });

    $('#DateBtB').on('click', function () {
        $('input[name="dtmDeliveryDate"]').focus();
    });
    $('#DateBtC').on('click', function () {
        $('input[name="dtmPaymentLimit"]').focus();
    });

    // �ץ�ӥ塼�ܥ��󲡲�
    $('img.preview').on('click', function () {
        // Ǽ����
        var lngDeliveryPlaceCode = $('input[name="lngDeliveryPlaceCode"]').val();
        if (lngDeliveryPlaceCode.length == 0) {
            alert("Ǽ��������ꤷ�Ƥ���������");
            return;
        }
        // POST��
        var postTarget = $('input[name="ajaxPostTarget"]').val();

        // POST�ǡ�������
        var data = {
            strMode: "get-closedday",
            strSessionID: $('input[name="strSessionID"]').val(),
            strcompanydisplaycode: $('input[name="lngCustomerCode"]').val(),
        };

        // �ץ�ӥ塼���ΥХ�ǡ������ˡ��������פ�ɬ�פʤΤ�ajax�Ǽ�������
        $.ajax({
            type: 'POST',
            url: postTarget,
            data: data,
            async: true,
        }).done(function (data) {
            console.log("done:get-closedday");
            console.log(data);

            // ������
            var closedDay = data;

            // �ܵҥ����ɤ��б������������������Ǥ��ʤ��Ȥ��⤽��Х�ǡ������Ǥ��ʤ�
            if (!closedDay) {
                alert("�ܵҥ����ɤ��б������������������Ǥ��ޤ���");
                return false;
            }

            if (closedDay < 0) {
                alert("�ܵҥ����ɤ��б�����������������ͤǤ���");
                return false;
            }

            // DBG:��������ȥ������о�
            // �ץ�ӥ塼����ɽ�����ΥХ�ǡ����������å�
            if (!varidateBeforePreview(closedDay)) {
                return false;
            }

            // �ץ�ӥ塼����ɽ��
            displayPreview();

        }).fail(function (error) {
            console.log("fail:get-closedday");
            console.log(error);
        });

    });

    function convertNumber(str, fracctiondigits) {
        console.log(str);
        if ((str != "" && str != undefined && str != "null") || str == 0) {
            console.log("null�ʳ��ξ�硧" + str);
            return Number(str).toLocaleString(undefined, {
                minimumFractionDigits: fracctiondigits,
                maximumFractionDigits: fracctiondigits
            });
        } else {
            console.log("null�ξ�硧" + str);
            return "";
        }
    }

});