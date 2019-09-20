
(function () {
    // �ե�����
    var workForm = $('form');
    // ���顼�������󥯥饹̾
    var classNameErrorIcon = 'error-icon';
    // ���顼��������꥽����URL
    var urlErrorIcon = '/img/type01/cmn/seg/seg_error_mark.gif';
    // ���ꥢ�ܥ���
    var btnClear = $('img.clear');
    // ��Ͽ�ܥ���
    var btnRegist = $('img.regist');
    // ȯ����������ܥ���
    var btnGetPoInfo = $('img.getpoinfo');


    // �ե����ॵ�֥ߥå��޻�
    $('document').on('submit', 'form', function (e) {
        e.preventDefault();
        return false;
    });

    btnGetPoInfo.on('click', function () {
        // ȯ��NO.�μ���
        var strOrderCode = $('input[name="strOrderCode"]').val();
        var strReviseCode = $('input[name="strReviseCode"]').val();
        var dtmStockAppDate = $('input[name="dtmStockAppDate"]').val();
        if (strOrderCode != "" && /^[a-zA-Z0-9]+$/.test(strOrderCode)) {
            // �ꥯ����������
            $.ajax({
                url: '/pc/regist/getPoInfo.php',
                type: 'post',
                data: {
                    'strSessionID': $.cookie('strSessionID'),
                    'strOrderCode': strOrderCode,
                    'strReviseCode': strReviseCode,
                    'dtmStockAppDate': dtmStockAppDate
                }
            })
                .done(function (response) {
                    var data = JSON.parse(response);
                    if (data.orderdetail.length == 0) {
                        alert("��������ȯ���ǡ���������ޤ���");
                        exit;
                    }

                    $("#tbl_order_detail").empty();

                    for (var i = 0; i < data.orderdetail.length; i++) {
                        var row = data.orderdetail[i];
                        // ȯ�����ơ�������Ǽ�ʺѤξ�硢���顼��Ф�
                        if (row.lngorderstatuscode == 4) {
                            alert("���ꤵ�줿ȯ���ֹ�ϡ�Ǽ�ʺѤߡפǤ���");
                            exit;
                        }
                    }

                    for (var i = 0; i < data.orderdetail.length; i++) {
                        var rowNum = i + 1;
                        var row = data.orderdetail[i];
                        // �̲�ñ�̥�����
                        var lngmonetaryunitcode = row.lngmonetaryunitcode;
                        // �̲ߥ졼�ȥ�����
                        var lngmonetaryratecode = row.lngmonetaryratecode;
                        $('select[name="lngMonetaryUnitCode"]').val(lngmonetaryunitcode);
                        $('select[name="lngMonetaryRateCode"]').val(lngmonetaryratecode);
                        $('input[name="lngOrderStatusCode"]').val(row.strorderstatusname);
                        $('input[name="curConversionRate"]').val(row.curconversionrate);
                        $('select[name="lngPayConditionCode"]').val(row.lngpayconditioncode);
                        $('input[name="lngCustomerCode"]').val(row.strcompanydisplaycode);
                        $('input[name="strCustomerName]').val(row.strcompanydisplayname);
                        $('input[name="strReviseCode"]').val(row.strrevisecode);
                        $('input[name="lngLocationCode"]').val(row.lnglocationcode);
                        $('input[name="strLocationName"]').val(row.strlocationame);
                        $('input[name="dtmExpirationDate"]').val(row.dtmexpirationdate);
                        $('input[name="lngOrderNo"]').val(row.lngorderno);
                        
                        // �񥳡��ɤμ���
                        var lngcountrycode = row.lngcountrycode;
                        var curtax = 0;
                        var lngtaxclasscode = 0;
                        // �񥳡��ɡ�81���ܸ�ξ�硢�ֳ��ǡפȤʤ롢����ʳ��ξ�硢�����
                        if (lngcountrycode == 81) {
                            if (data.tax == null) {
                                alert("�����Ǿ���μ����˼��Ԥ��ޤ�����");
                                exit;
                            } else {
                                curtax = data.tax.curtax;
                            }
                            lngtaxclasscode = 2;
                        } else {
                            curtax = 0;
                            lngtaxclasscode = 1;
                        }

                        var curtaxprice = 0;
                        // ���������
                        if (lngtaxclasscode == 1) {
                            curtaxprice = 0;
                            //��2:����
                        } else if (lngtaxclasscode == 2) {
                            curtaxprice = Math.floor(row.cursubtotalprice * (1 + curtax));
                            // 3:����
                        } else {
                            curtaxprice = row.cursubtotalprice - Math.floor((row.cursubtotalprice / (1 + curtax)) * curtax);
                        }

                        var select = '<select style="width:90px;" onchange="resetTaxPrice(this)">';
                        for (var j = 0; j < data.taxclass.length; j++) {
                            var taxclassRow = data.taxclass[j];
                            if (taxclassRow.lngtaxclasscode == lngtaxclasscode) {
                                select += '<option value="' + taxclassRow.lngtaxclasscode + '" selected>' + taxclassRow.strtaxclassname + '</option>'
                            } else {
                                select += '<option value="' + taxclassRow.lngtaxclasscode + '">' + taxclassRow.strtaxclassname + '</option>'

                            }
                        }
                        select += '</select>';
                        var detail_body = '<tr>'
                            + '<td class="col1">' + rowNum + '</td>'
                            + '<td class="col2"><input type="checkbox" style="width:10px;"></td>'
                            + '<td class="col3">[' + convertNull(row.strproductcode) + '] ' + convertNull(row.strproductname).substring(1, 28) + '</td>'
                            + '<td class="col4">[' + convertNull(row.lngstocksubjectcode) + '] ' + convertNull(row.strstocksubjectname) + '</td>'
                            + '<td class="col5">[' + convertNull(row.lngstockitemcode) + '] ' + convertNull(row.strstockitemname) + '</td>'
                            + '<td class="col6">' + money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, row.curproductprice) + '</td>'
                            + '<td class="col7">' + row.strmonetaryunitname + '</td>'
                            + '<td class="col8">' + convertNumber(row.lngproductquantity, 0) + '</td>'
                            + '<td class="col9">' + money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, row.cursubtotalprice) + '</td>'
                            // �����Ƕ�ʬ
                            + '<td class="col10">' + select + '</td>'
                            // ������Ψ
                            + '<td class="col11">' + curtax + '</td>'
                            // �����ǳ�
                            + '<td class="col12">' + money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, curtaxprice) + '</td>'
                            + '<td class="col13">' + row.dtmdeliverydate + '</td>'
                            + '<td class="col14">' + row.strnote + '</td>'
                            + '<td style="display:none">' + row.cursubtotalprice + '</td>'
                            + '<td style="display:none">' + data.tax.curtax + '</td>'
                            + '<td style="display:none">' + row.lngmonetaryunitcode + '</td>'
                            + '<td style="display:none">' + row.strmonetaryunitsign + '</td>'
                            + '<td style="display:none">' + curtaxprice + '</td>'
                            + '<td style="display:none">' + row.lngorderno + '</td>'
                            + '<td style="display:none">' + row.lngorderdetailno + '</td>'
                            + '<td style="display:none">' + data.tax.lngtaxcode + '</td>'
                            + '</tr>';
                        $("#tbl_order_detail").append(detail_body);
                    }

                })
                .fail(function (response) {
                    alert(response);
                    alert("fail");
                })
        }

    });


    // �̲��ѹ����٥��
    $('select[name="lngMonetaryUnitCode"]').on('change', function () {
        // �ꥯ����������
        $.ajax({
            url: '/pc/regist/getMonetaryRate.php',
            type: 'post',
            data: {
                'strSessionID': $.cookie('strSessionID'),
                'lngMonetaryUnitCode': $(this).val(),
                'lngMonetaryRateCode': $('select[name="lngMonetaryRateCode"]').val(),
                'dtmStockAppDate': $('input[name="dtmStockAppDate"]').val()
            }
        })
            .done(function (response) {
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
                'lngMonetaryUnitCode': $('select[name="lngMonetaryUnitCode"]').val(),
                'lngMonetaryRateCode': $(this).val(),
                'dtmStockAppDate': $('input[name="dtmStockAppDate"]').val()
            }
        })
            .done(function (response) {
                var data = JSON.parse(response);
                $('input[name="curConversionRate"]').val(data.curconversionrate);
            })
            .fail(function (response) {
                alert("fail");
            })
    });

    // ��Ͽ�ܥ��󲡲����ν���
    btnRegist.on('click', function () {
        
        if (workForm.valid()) {

            var dtmExpirationDate = $('input[name="dtmExpirationDate"]').val();

            var detaildata = new Array();
            var len = 0;
            $("#tbl_order_detail tr").each(function (i, e) {
                // ���ٹ��ֹ�
                var strReceiveCode = $(this).find('td:nth-child(1)').text();
                // ȯ�����ٹ��ֹ�
                // ���������ֹ�
                var chkbox = $(this).find('td:nth-child(2)').find('input:checkbox');
                if (chkbox.prop("checked")) {
                    len += 1;
                    // ȯ���ֹ�                
                    var lngOrderNo = $(this).find('td:nth-child(20)').text();
                    // ȯ�������ֹ�                
                    var lngOrderDetailNo = $(this).find('td:nth-child(21)').text();
                    // ���������ֹ�
                    var lngStockDetailNo = len;
                    // �����Ƕ�ʬ
                    var lngTaxClassCode = $(this).find('td:nth-child(10)').find('select').val();
                    var strTaxClassName = $(this).find('td:nth-child(10)').find('select option:selected').text();
                    // ����Ψ
                    var curTax = $(this).find('td:nth-child(11)').text();
                    // �����ǳ�                
                    var curTaxPrice = $(this).find('td:nth-child(19)').text();
                    // Ǽ��
                    var dtmDeliveryDate = $(this).find('td:nth-child(13)').text();
                    // �����ǥ�����                
                    var lngTaxCode = $(this).find('td:nth-child(22)').text();

                    // Ǽ�����إå��������Ϥ���������������Ʊ��Ǥʤ��Ԥ�¸�ߤ������
                    if (dtmDeliveryDate.substring(1, 7) != dtmExpirationDate.substring(1, 7)) {
                        alert("ȯ���������Ǽ����Ǽ�����Ȱ��פ��ޤ���ȯ���ǡ����������Ƥ���������");
                        exit;
                    }

                    detaildata[len - 1] = {
                        "lngOrderNo": lngOrderNo,
                        "lngOrderDetailNo": lngOrderDetailNo,
                        "lngStockDetailNo": lngStockDetailNo,
                        "lngTaxClassCode": lngTaxClassCode,
                        "strTaxClassName": strTaxClassName,
                        "curTax": curTax,
                        "curTaxPrice": curTaxPrice,
                        "lngTaxCode": lngTaxCode
                    };
                }
            });

            if (len == 0) {
                exit;
            }
            var formData = workForm.serializeArray();
            formData.push({name:"detailData", value:JSON.stringify(detaildata)});
            formData.push({name:"strSessionID", value:$.cookie('strSessionID')});
            formData.push({name:"strMonetaryRateName", value:$('select[name="lngMonetaryRateCode"] option:selected').text()});
            formData.push({name:"strMonetaryUnitName", value:$('select[name="lngMonetaryUnitCode"] option:selected').text()});
            formData.push({name:"strPayConditionName", value:$('select[name="lngPayConditionCode"] option:selected').text()});

            var actionUrl = workForm.attr('action');
            // �ꥯ����������
            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData
            })
                .done(function (response) {
                    var w = window.open();
                    w.document.open();
                    w.document.write(response);
                    w.document.close();
                    w.onunload = function () {
                        window.opener.location.reload();
                    }
                })
                .fail(function (response) {
                    alert("fail");
                    // Ajax�ꥯ�����Ȥ�����
                });
        }
        else {
            // �Х�ǡ������Υ��å�
            workForm.find(':submit').click();
        }
    });


})();

/**
 * �ǳۺƷ׻�
 * @param {*} objID 
 */
function resetTaxPrice(objID) {

    var lngtaxclasscode = objID.value;
    var children = objID.parentNode.parentNode.children;
    // �����ȥ������μ���
    var cursubtotalprice = children[14].innerHTML;
    var curtax = children[15].innerHTML;
    var lngmonetaryunitcode = children[16].innerHTML;
    var strmonetaryunitsign = children[17].innerHTML;

    if (lngtaxclasscode == 1) {
        curtaxprice = 0;
        curtax = 0;
        //��2:����
    } else if (lngtaxclasscode == 2) {
        curtaxprice = Math.floor(cursubtotalprice * (1 + curtax));
        // 3:����
    } else {
        curtaxprice = cursubtotalprice - Math.floor((cursubtotalprice / (1 + curtax)) * curtax);
    }

    children[10].innerText = curtax;
    children[11].innerText = money_format(lngmonetaryunitcode, strmonetaryunitsign, String(curtaxprice));
    children[18].innerText = curtaxprice;
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
        return '\xA5' + " " + convertNumber(price, 4);
    } else {
        return strmonetaryunitsign + " " + convertNumber(price, 4);
    }
}

function convertNumber(str, fracctiondigits) {
    if (str != "" && str != undefined && str != "null") {
        return Number(str).toLocaleString(undefined, {
            minimumFractionDigits: fracctiondigits,
            maximumFractionDigits: fracctiondigits
        });
    } else {
        return "";
    }
}