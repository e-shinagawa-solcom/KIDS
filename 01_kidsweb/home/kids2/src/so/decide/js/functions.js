(function () {
    var lockId = "lockId";

    // �����å��ܥå������ڤ��ؤ������ΥХ����
    $('input[type="checkbox"][name="allSel"]').on({
        'click': function () {
            var status = this.checked;
            $('input[type="checkbox"]')
                .each(function () {
                    this.checked = status;
                });
        }
    });


    // ����ܥ���Υ��٥��
    $('img.decide').on('click', function () {
        // ���򤷤��ԤΥ�ӥ�����ֹ���������
        var strId = "";
        $('input[type="checkbox"]')
            .each(function () {
                if (this.checked) {
                    if ($(this).attr('name') != "allSel") {
                        if (strId == "") {
                            strId = $(this).attr('id');
                        } else {
                            strId = strId + "," + $(this).attr('id');
                        }
                    }
                }
            });

        // �Ԥ����򤵤�Ƥʤ����
        if (strId == "") {
            alert("������ꤹ�����٥ǡ��������ꤵ��Ƥ��ޤ���");
            return;
        }

        $.ajax({
            url: '/so/decide/decide.php',
            type: 'POST',
            data: {
                'strSessionID': $.cookie('strSessionID'),
                'strId': strId
            }
        })
            .done(function (data) {
                console.log("�����ǡ�����" + data);
                // Ajax�ꥯ�����Ȥ�����
                var data = JSON.parse(data);
                //��¸�ǡ����κ��
                $("#table_decide_no").empty();
                $("#table_decide_body").empty();

                for (var i = 0; i < data.receiveDetail.length; i++) {
                    var rowNum = i + 1;
                    var id = "decide_no_" + rowNum;
                    var decide_no = '<tr id="' + id + '" rownum="' + rowNum + '"><td style="height: 20px;width: 20px;">' + rowNum + '</td></tr>';
                    $("#table_decide_no").append(decide_no);
                    //�ԥǡ���
                    var row = data.receiveDetail[i];
                    var detail_id = "decide_detail_" + rowNum;
                    var select = '<select style="width:90px;" onchange="resetData(this)">';
                    for (var j = 0; j < data.productUnit.length; j++) {
                        var productunit = data.productUnit[j];
                        if (productunit.lngproductunitcode == row.lngproductunitcode) {
                            select += '<option value="' + productunit.lngproductunitcode + '" selected>' + productunit.strproductunitname + '</option>';
                        } else {
                            select += '<option value="' + productunit.lngproductunitcode + '">' + productunit.strproductunitname + '</option>';
                        }
                    }
                    select += '</select>';
                    // ���������̷׻�
                    var lngunitquantity = 1;
                    var lngproductquantity = convertNullToZero(row.lngproductquantity) / lngunitquantity;
                    if (productunit.lngproductunitcode == 2) {
                        lngunitquantity = convertNullToZero(row.lngcartonquantity);
                        lngproductquantity = convertNullToZero(row.lngproductquantity) / lngunitquantity;
                    }
                    // var decide_body = '<tr id="' + detail_id + '" rownum="' + rowNum + '" onclick="rowSelect(this);">'
                    var decide_body = '<tr id="' + detail_id + '" rownum="' + rowNum + '" >'
                        + '<td id="lngreceivedetailno">' + row.lngreceivedetailno + '</td>' // ���ٹ��ֹ�
                        + '<td id="strcustomerreceivecode"><input type="text" class="form-control form-control-sm txt-kids" style="width:90px;" value="' + convertNull(row.strcustomerreceivecode) + '"></td>' //�ܵҼ����ֹ�
                        + '<td id="strcompanydisplaycode" style="white-space: nowrap;">[' + convertNull(row.strcompanydisplaycode) + '] ' + convertNull(row.strcompanydisplayname) + '</td>' //�ܵ�
                        + '<td id="strgoodscode">' + convertNull(row.strgoodscode) + '</td>'//�ܵ�����
                        + '<td id="dtmdeliverydate">' + convertNull(row.dtmdeliverydate) + '</td>'//Ǽ��
                        + '<td id="lngsalesdivisioncode" style="white-space: nowrap;" >[' + convertNull(row.lngsalesdivisioncode) + '] ' + convertNull(row.strsalesdivisionname) + '</td>'//���ʬ��
                        + '<td id="lngsalesclasscode" style="white-space: nowrap;" >[' + convertNull(row.lngsalesclasscode) + '] ' + convertNull(row.strsalesclassname) + '</td>'//����ʬ
                        + '<td id="curproductprice">' + money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, row.curproductprice) + '</td>'//ñ��
                        + '<td id="lngproductunitcode">' + select + '</td>' //ñ��
                        + '<td id="lngunitquantity">' + lngunitquantity + '</td>'//����
                        + '<td style="text-align: center;"><img class="button" src="/img/type01/so/product_off_ja_bt.gif" onclick="showProductInfo(this)" lngproductno="' + row.lngproductno + '" lngrevisionno="' + row.lngproductrevisionno + '"></td>'//����
                        + '<td>' + lngproductquantity + '</td>'//����
                        + '<td id="cursubtotalprice" style="white-space: nowrap;">' + money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, row.cursubtotalprice) + '</td>'//����
                        + '<td id="strdetailnote"><input type="text" class="form-control form-control-sm txt-kids" style="width:240px;" value="' + convertNull(row.strdetailnote) + '"></td>'//��������
                        + '<td id="strproductcode" style="display:none">' + row.strproductcode + '</td>'//���ʥ�����
                        + '<td id="strproductname" style="display:none">[' + convertNull(row.strproductcode) + '] ' + convertNull(row.strproductname) + '</td>'//���ʥ�����
                        + '<td id="lngreceiveno" style="display:none">' + row.lngreceiveno + '</td>'//�����ֹ�
                        + '<td id="strreceivecode" style="display:none">' + row.strreceivecode + '</td>'//��������
                        + '<td id="lngrevisionno" style="display:none">' + row.lngrevisionno + '</td>'//��ӥ�����ֹ�
                        + '<td id="lngcartonquantity" style="display:none">' + convertNullToZero(row.lngcartonquantity) + '</td>'//�����ȥ�����
                        + '<td id="lngproductquantity" style="display:none">' + convertNullToZero(row.lngproductquantity) + '</td>'//����
                        + '<td id="strrevisecode" style="display:none">' + row.strrevisecode + '</td>'//���ʥ�ӥ�����ֹ�
                        + '</tr>';
                    $("#table_decide_body").append(decide_body);
                }

                var row = $(".table-decide-description tbody tr");
                var columnNum = row.find('td').length;
                var thwidthArry = [];
                var tdwidthArry = [];
                for (var i = 1; i <= 14; i++) {
                    var width = $(".table-decide-description thead tr th:nth-child(" + i + ")").width();
                    thwidthArry.push(width);
                    var tdwidth = $(".table-decide-description tbody tr td:nth-child(" + i + ")").width();
                    tdwidthArry.push(tdwidth);
                }
                for (var i = 1; i <= 14; i++) {
                    if (thwidthArry[i - 1] > tdwidthArry[i - 1]) {
                        $(".table-decide-description thead tr th:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                    } else {
                        $(".table-decide-description thead tr th:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
                    }
                }
                for (var i = 1; i <= 14; i++) {
                    if (thwidthArry[i - 1] > tdwidthArry[i - 1]) {
                        $(".table-decide-description tbody tr td:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                    } else {
                        $(".table-decide-description tbody tr td:nth-child(" + i + ")").width(tdwidthArry[i - 1]);

                    }
                }
                var rows = $('.table-decide-description tbody tr');
                var lastSelectedRow;
                /* Create 'click' event handler for rows */
                rows.on('click', function (e) {
                    /* Get current row */
                    var row = $(this);

                    /* Check if 'Ctrl', 'cmd' or 'Shift' keyboard key was pressed
                     * 'Ctrl' => is represented by 'e.ctrlKey' or 'e.metaKey'
                     * 'Shift' => is represented by 'e.shiftKey' */
                    if (e.ctrlKey || e.metaKey) {
                        /* If pressed highlight the other row that was clicked */
                        $(".table-decide-description tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
                        
                    } else if (e.shiftKey) {
                        /* If pressed highlight the other row that was clicked */
                        var indexes = [lastSelectedRow.index(), row.index()];
                        indexes.sort(function (a, b) {
                            return a - b;
                        });
                        for (var i = indexes[0]; i <= indexes[1]; i++) {
                            $(".table-decide-description tbody tr:nth-child(" + (i + 1) + ")").css("background-color", "#bbbbbb");
                        }
                    } else {
                        /* Otherwise just highlight one row and clean others */
                        $(".table-decide-description tbody tr").css("background-color", "#ffffff");
                        $(".table-decide-description tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
                        lastSelectedRow = row;
                    }

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

            })
            .fail(function (data) {
                console.log("������̡�" + data);
                alert("fail");
                // Ajax�ꥯ�����Ȥ�����
            });
    });

    // ������ܥ���Υ��٥��
    $('img.alldelete').on('click', function () {
        $("#table_decide_no").empty();
        $("#table_decide_body").empty();
    });

    // ����ܥ���Υ��٥��
    $('img.delete').on('click', function () {
        var rownum = 0;
        $("#table_decide_no tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).remove();
            } else {
                rownum += 1;
                $(this).attr('id', 'decide_no_' + rownum);
                $(this).attr('rownum', rownum);
                $(this).find('td').first().text(rownum);
            }


        });

        rownum = 0;
        $("#table_decide_body tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).remove();
            } else {
                rownum += 1;
                $(this).attr('id', 'decide_detail_' + rownum);
                $(this).attr('rownum', rownum);
            }
        });
    });

    // ��������ѹ��ܥ���Υ��٥��
    $('img.search').on('click', function () {

        // ��������̵������
        lockScreen(lockId);

        url = '/so/decide/search_init.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        param = 'strproductcode=' + $('input[name="strProductCode"]').val();
        // �̥�����ɥ���ɽ��
        window.open(url + '?' + sessionID + '&' + param, '_blank', 'width=730, height=570, resizable=yes, scrollbars=yes, menubar=no');
    });

    // �Ԥ��ľ�˰�ư����ܥ���
    $('img.rowup').click(function () {
        var len = $("#table_decide_body").children().length;
        for (var i = 1; i <= len; i++) {
            var row = $("#decide_detail_" + i);
            var backgroud = row.css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                for (var j = i - 1; j >= 1; j--) {
                    var row_prev = $("#decide_detail_" + j);
                    var row_prev_backgroud = row_prev.css("background-color");
                    if (row_prev_backgroud == 'rgb(255, 255, 255)') {
                        row.insertBefore(row_prev);
                        break;
                    }
                }
            }
        }

        var len = $("#table_decide_no").children().length;
        for (var i = 1; i <= len; i++) {
            var row = $("#decide_no_" + i);
            var backgroud = row.css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                for (var j = i - 1; j >= 1; j--) {
                    var row_prev = $("#decide_no_" + j);
                    var row_prev_backgroud = row_prev.css("background-color");
                    if (row_prev_backgroud == 'rgb(255, 255, 255)') {
                        row.insertBefore(row_prev);
                        break;
                    }
                }
            }
        }

        resetRowid();

    });

    // �Ԥ��Ĳ��˰�ư����ܥ���
    $('img.rowdown').click(function () {
        var len = $("#table_decide_body").children().length;
        for (var i = len; i >= 1; i--) {
            var row = $("#decide_detail_" + i);
            var backgroud = row.css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                for (var j = i + 1; j <= len; j++) {
                    var row_prev = $("#decide_detail_" + j);
                    var row_prev_backgroud = row_prev.css("background-color");
                    if (row_prev_backgroud == 'rgb(255, 255, 255)') {
                        row.insertAfter(row_prev);
                        break;
                    }
                }
            }
        }


        var len = $("#table_decide_no").children().length;
        for (var i = len; i >= 1; i--) {
            var row = $("#decide_no_" + i);
            var backgroud = row.css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                for (var j = i + 1; j <= len; j++) {
                    var row_prev = $("#decide_no_" + j);
                    var row_prev_backgroud = row_prev.css("background-color");
                    if (row_prev_backgroud == 'rgb(255, 255, 255)') {
                        row.insertAfter(row_prev);
                        break;
                    }
                }
            }
        }

        resetRowid();

    });

    // �Ԥ���־�˰�ư����
    $('img.rowtop').click(function () {
        $("#table_decide_body tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).insertBefore($("#decide_detail_1"));
            }
        });

        $("#table_decide_no tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).insertBefore($("#decide_no_1"));
            }
        });

        resetRowid();

    });

    // �Ԥ���ֲ��˰�ư����
    $('img.rowbottom').click(function () {
        var lasttr = $("#table_decide_body").find('tr').last();
        $("#table_decide_body tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).insertAfter(lasttr);
            }
        });

        lasttr = $("#table_decide_no").find('tr').last();
        $("#table_decide_no tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).insertAfter(lasttr);
            }
        });

        resetRowid();
    });

    // ������Ͽ���٥��
    $('img.decideRegist').on('click', function () {
        
        var params = new Array();
        var len = 0;
        $("#table_decide_body tr").each(function (i, e) {
            len += 1;
            // // �����ֹ�
            var strReceiveCode = $(this).find('#strreceivecode').text();
            // ���������ֹ�
            var lngReceiveDetailNo = $(this).find('#lngreceivedetailno').text();
            // �ܵҼ����ֹ�
            var strCustomerReceiveCode = $(this).find('#strcustomerreceivecode').find('input:text').val();
            if (strCustomerReceiveCode == "") {
                alert(len + "���ܤθܵҼ����ֹ椬���Ϥ���Ƥ��ޤ���");
                exit;
            }
            var lngproductquantity = $(this).find('#lngproductquantity').text();
            if (lngproductquantity.indexOf('.') != -1) {
                alert(len + "���ܤθ��Ѹ����׻����ο��̤������ǳ���ڤ�ޤ���");
                exit;
            }

            var strDetailNote = $(this).find('#strdetailnote').find('input:text').val();
            var strProductUnitName = $(this).find('#lngproductunitcode').find('select option:selected').text();
            var lngProductUnitCode = $(this).find('#lngproductunitcode').find('select option:selected').val();

            params[len - 1] = {
                "strReceiveCode": strReceiveCode,
                "lngReceiveDetailNo": lngReceiveDetailNo,
                "strCustomerReceiveCode": strCustomerReceiveCode,
                "strProductCode": $(this).find('#strproductname').text(),
                "strGoodsCode": $(this).find('#strgoodscode').text(),
                "dtmDeliveryDate": $(this).find('#dtmdeliverydate').text(),
                "lngSalesClassCode": $(this).find('#lngsalesclasscode').text(),
                "curProductPrice": $(this).find('#curproductprice').text(),
                "strProductUnitName": strProductUnitName,
                "lngUnitQuantity": $(this).find('#lngunitquantity').text(),
                "lngProductQuantity": $(this).find('#lngproductquantity').text(),
                "curSubtotalPrice": $(this).find('#cursubtotalprice').text(),
                "strDetailNote": strDetailNote,
                "strCompanyDisplayCode": $(this).find('#strcompanydisplaycode').text(),
                "lngReceiveNo": $(this).find('#lngreceiveno').text(),
                "lngRevisionNo": $(this).find('#lngrevisionno').text(),
                "strReviseCode": $(this).find('#strrevisecode').text(),
                "strProductCode_product": $(this).find('#strproductcode').text(),
                "lngProductUnitCode": lngProductUnitCode
            };
        });
        if (len == 0) {
            alert("������ꤹ�����ٹԤ����򤵤�Ƥ��ޤ���");
            exit;
        }
        // ��������̵������
        // lockScreen("lockId");

        // �ꥯ����������
        $.ajax({
            url: '/so/decide/decide_confirm.php',
            type: 'post',
            // dataType: 'json',
            type: 'POST',
            data: {
                'strSessionID': $.cookie('strSessionID'),
                'detailData': params
            }
        })
            .done(function (response) {
                var w = window.open("", 'Decide Confirm', "width=1011px, height=600px, scrollbars=yes, resizable=yes");
                w.document.open();
                w.document.write(response);
                w.document.close();
            })
            .fail(function (response) {
                console.log("������̡�" + JSON.stringify(response));
                alert("fail");

                // ��������ͭ���ˤ���
                unlockScreen("lockId");
            });
    });

})();

function showProductInfo(objID) {
    url = '/p/detail/index.php';
    sessionID = 'strSessionID=' + $.cookie('strSessionID');
    lngProductNo = 'lngProductNo=' + objID.getAttribute('lngproductno');
    lngRevisionNo = 'lngRevisionNo=' + objID.getAttribute('lngrevisionno');
    // �̥�����ɥ���ɽ��
    window.open(url + '?' + sessionID + '&' + lngProductNo + '&' + lngRevisionNo, '_blank', 'height=510, width=600, resizable=yes, scrollbars=yes, menubar=no');

}

// �����򥤥٥��
function rowSelect(objID) {
    var rownum = objID.getAttribute('rownum');
    var backgroud = $("#decide_detail_" + rownum).css("background-color");
    if (backgroud == "rgb(255, 255, 255)") {
        $("#decide_detail_" + rownum).css("background-color", "#bbbbbb");
        $("#decide_no_" + rownum).css("background-color", "#bbbbbb");
    } else {
        $("#decide_detail_" + rownum).css("background-color", "#ffffff");
        $("#decide_no_" + rownum).css("background-color", "#ffffff");
    }
}

// ��ID�κ�����
function resetRowid() {
    var rownum = 0;
    $("#table_decide_body tr").each(function (i, e) {
        rownum += 1;
        $(this).attr('id', 'decide_detail_' + rownum);
        $(this).attr('rownum', rownum);
    });

    rownum = 0;
    $("#table_decide_no tr").each(function (i, e) {
        rownum += 1;
        $(this).attr('id', 'decide_no_' + rownum);
        $(this).attr('rownum', rownum);
        $(this).find('td').first().text(rownum);
    });
}

// ñ�̥��쥯�ȥܥå������ѹ����٥��
function resetData(objID) {
    var val = objID.value;
    var children = objID.parentNode.parentNode.children;
    // �����ȥ������μ���
    var lngcartonquantity = children[19].innerHTML;
    // ���ʿ��̤μ���
    var lngproductquantity = children[20].innerHTML;
    // ���������̷׻�
    var lngunitquantitynew = 1;
    var lngproductquantitynew = lngproductquantity / lngunitquantitynew;
    // ñ�̤�[c/t]�ξ�硢
    if (val == 2) {
        // ���� = �����ȥ�����
        lngunitquantitynew = lngcartonquantity;
        // ���� = ���ʿ���/�����ȥ�����
        lngproductquantitynew = lngproductquantity / lngunitquantitynew;
    }
    children[9].innerText = lngunitquantitynew;
    children[11].innerText = lngproductquantitynew;


}

/**
 * ʸ���Ѵ���null�ξ�硢""���Ѵ���
 * @param {} str 
 */
function convertNull(str) {
    if (str != "" && str != undefined && str != "null") {
        return str;
    } else {
        return "";
    }
}

/**
 * ʸ���Ѵ���null�ξ�硢"0"���Ѵ���
 * @param {} str 
 */
function convertNullToZero(str) {
    if (str != "" && str != undefined && str != "null") {
        return str;
    } else {
        return 0;
    }
}

function money_format(lngmonetaryunitcode, strmonetaryunitsign, price) {
    if (lngmonetaryunitcode == 1) {
        return '\xA5' + " " + price;
    } else {
        return strmonetaryunitsign + " " + price;
    }
}
/*
 * ��������̵���ˤ���
 */
function lockScreen(id) {

    /*
     * ���߲��̤�ʤ�����������DIV�������������
     */
    var divTag = $('<div />').attr("id", id);

    /*
     * �������������
     */
    divTag.css("z-index", "999")
        .css("position", "absolute")
        .css("top", "0px")
        .css("left", "0px")
        .css("right", "0px")
        .css("bottom", "0px")
        .css("background-color", "gray")
        .css("opacity", "0.8");

    /*
     * BODY�����˺�������DIV�������ɲ�
     */
    $('body').append(divTag);
}

/*
 * �������̵����������
 */
function unlockScreen(id) {
    /*
     * ���̤�ʤ�äƤ��륿����������
     */
    $("#" + id).remove();
}
