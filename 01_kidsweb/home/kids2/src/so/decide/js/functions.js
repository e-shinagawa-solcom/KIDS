(function () {
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
                    var decide_body = '<tr id="' + detail_id + '" rownum="' + rowNum + '" onclick="rowSelect(this);">'
                        + '<td style="width: 100px;">' + row.strreceivecode + '</td>'
                        + '<td style="width: 50px;">' + row.lngreceivedetailno + '</td>'
                        + '<td style="width: 100px;"><input type="text" class="form-control form-control-sm txt-kids" style="width:90px;" value="' + row.strcustomerreceivecode + '"></td>'
                        + '<td style="width: 250px;">[' + convertNull(row.strproductcode) + '] ' + convertNull(row.strproductname.substring(1, 28)) + '</td>'
                        + '<td style="width: 100px;">' + convertNull(row.strgoodscode) + '</td>'
                        + '<td style="width: 70px;">' + convertNull(row.dtmdeliverydate) + '</td>'
                        + '<td style="width: 70px;">[' + convertNull(row.lngsalesclasscode) + '] ' + convertNull(row.strsalesclassname) + '</td>'
                        + '<td style="width: 100px;">' + money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, row.curproductprice) + '</td>'
                        + '<td style="width: 100px;">' + select + '</td>' //ñ��
                        + '<td style="width: 70px;">' + lngunitquantity + '</td>'
                        + '<td style="width: 100px;text-align: center;"><img class="button" src="/img/type01/so/product_off_ja_bt.gif" onclick="showProductInfo(this)" strproductcode="' + row.strproductcode + '"></td>'
                        + '<td style="width: 70px;">' + lngproductquantity + '</td>'
                        + '<td style="width: 100px;">' +  money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, row.cursubtotalprice) + '</td>'
                        + '<td style="width: 250px;"><input type="text" class="form-control form-control-sm txt-kids" style="width:240px;" value="' + convertNull(row.strdetailnote) + '"></td>'
                        + '<td style="display:none">[' + convertNull(row.strcompanydisplaycode) + '] ' + convertNull(row.strcompanydisplayname) + '</td>'
                        + '<td style="display:none">' + row.lngreceiveno + '</td>'
                        + '<td style="display:none">' + row.lngrevisionno + '</td>'
                        + '<td style="display:none">' + convertNullToZero(row.lngcartonquantity) + '</td>'
                        + '<td style="display:none">' + convertNullToZero(row.lngproductquantity) + '</td>'
                        + '</tr>';
                    $("#table_decide_body").append(decide_body);
                }

            })
            .fail(function () {
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
        url = '/so/decide/search_init.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        strReceiveCode = 'strReceiveCode=' + $(this).attr('code');
        // �̥�����ɥ���ɽ��
        window.open(url + '?' + sessionID + '&' + strReceiveCode, '_blank', 'width=730, height=570, resizable=yes, scrollbars=yes, menubar=no');
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

    $('img.regist').on('click', function () {
        var params = new Array();
        var len = 0;
        $("#table_decide_body tr").each(function (i, e) {
            len += 1;
            // �����ֹ�
            var strReceiveCode = $(this).find('td').first().text();
            // ���������ֹ�
            var lngReceiveDetailNo = $(this).find('td:nth-child(2)').text();
            // �ܵҼ����ֹ�
            var strCustomerReceiveCode = $(this).find('td:nth-child(3)').find('input:text').val();
            if (strCustomerReceiveCode == "") {
                alert(len + "���ܤθܵҼ����ֹ椬���Ϥ���Ƥ��ޤ���");
                exit;
            }
            var lngproductquantity = $(this).find('td:nth-child(12)').text();
            if (lngproductquantity.indexOf('.') != -1) {
                alert(len + "���ܤθ��Ѹ����׻����ο��̤������ǳ���ڤ�ޤ���");
                exit;
            }

            var strDetailNote = $(this).find('td:nth-child(14)').find('input:text').val();
            var strProductUnitName = $(this).find('td:nth-child(9)').find('select option:selected').text();            
            var lngProductUnitCode = $(this).find('td:nth-child(9)').find('select option:selected').val();

            params[len - 1] = {
                "strReceiveCode": strReceiveCode,
                "lngReceiveDetailNo": lngReceiveDetailNo,
                "strCustomerReceiveCode": strCustomerReceiveCode,
                "strProductCode": $(this).find('td:nth-child(4)').text(),
                "strGoodsCode": $(this).find('td:nth-child(5)').text(),
                "dtmDeliveryDate": $(this).find('td:nth-child(6)').text(),
                "lngSalesClassCode": $(this).find('td:nth-child(7)').text(),
                "curProductPrice": $(this).find('td:nth-child(8)').text(),
                "strProductUnitName": strProductUnitName,
                "lngUnitQuantity": $(this).find('td:nth-child(10)').text(),
                "lngProductQuantity": $(this).find('td:nth-child(12)').text(),
                "curSubtotalPrice": $(this).find('td:nth-child(13)').text(),
                "strDetailNote": strDetailNote,
                "strCompanyDisplayCode": $(this).find('td:nth-child(15)').text(),
                "lngReceiveNo": $(this).find('td:nth-child(16)').text(),
                "lngRevisionNo": $(this).find('td:nth-child(17)').text(),
                "lngProductUnitCode": lngProductUnitCode
            };
        });

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
                var w = window.open();
                w.document.open();
                w.document.write(response);
                w.document.close();
            })
            .fail(function (response) {
                alert("fail");
                // Ajax�ꥯ�����Ȥ�����
            });
    });

})();

function showProductInfo(objID) {
    // url = '/p/result/index2.php';
    url = '/p/regist/renew.php';
    sessionID = 'strSessionID=' + $.cookie('strSessionID');
    strProductCode = 'strProductCode=' + objID.getAttribute('strproductcode');
    // �̥�����ɥ���ɽ��
    window.open(url + '?' + sessionID + '&' + strProductCode, '_blank', 'height=510, width=600, resizable=yes, scrollbars=yes, menubar=no');
    // var w = window.open(url + '?' + sessionID + '&' + lngReceiveNo, 'display-detail', 'width=1000, height=600, resizable=yes, scrollbars=yes, menubar=no');
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
    var lngcartonquantity = children[17].innerHTML;
    // ���ʿ��̤μ���
    var lngproductquantity = children[18].innerHTML;

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

function money_format(lngmonetaryunitcode, strmonetaryunitsign, price)
{
    if (lngmonetaryunitcode == 1) {
        return '\xA5' + " " + price;
    } else {
        return $strmonetaryunitsign + " " + $price;
    }
}
