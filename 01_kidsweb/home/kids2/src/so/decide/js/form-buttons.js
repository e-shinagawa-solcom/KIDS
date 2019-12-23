(function () {
    // �ե�����
    var workForm = $('form');
    // ���ꥢ�ܥ���
    var btnClear = $('img.clear');
    // ��Ͽ�ܥ���
    var btnSearch = $('img.search');
    // �Ĥ���ܥ���
    var btnClose = $('img.close');

    // �ե����ॵ�֥ߥå��޻�
    $('document').on('submit', 'form', function (e) {
        e.preventDefault();
        return false;
    });

    // ���ꥢ�ܥ���
    btnClear.on('click', function () {
        // �ƥ��������ϲս��ꥻ�å�
        workForm.find('input[type="text"]').each(function (i, e) {
            $(this).val('');
        });
    });

    // �Ĥ���ܥ���
    btnClose.on('click', function () {
        //������ɥ����Ĥ���
        window.close();
        // �ƥ�����ɥ��Υ�å���������
        if (window.opener.$('#lockId').length) {
            window.opener.$('#lockId').remove();
        }
    });

    var parantExistanceFlag = true;

    // �����ܥ��󲡲����ν���
    btnSearch.on('click', function () {
        if (workForm.valid()) {
            //������ɥ����֥������Ȥ�¸�ߤ��Ƥ��ʤ����ٹ𤷤ƥե饰��false��
            if (!window.opener || !Object.keys(window.opener).length) {
                window.alert('�Ʋ��̤�¸�ߤ��ޤ���');
                parantExistanceFlag = false;
            }

            //�Ʋ��̤��ͤ�����
            if (parantExistanceFlag) {
                var strProductCode = $('input[name="strProductCode"]').val();
                var strProductName = $('input[name="strProductName"]').val();
                if (strProductCode.length == 0) {
                    alert("���ʥ����ɤ����ꤷ�Ƥ���������");
                    return;
                }

                // �����ʥ�����
                var parentStrProductCode = window.opener.$('input[name="strProductCode"]').val();
                var lngSalesDivisionCode = $('select[name="lngSalesDivisionCode"] option:selected').val();
                if (lngSalesDivisionCode === undefined) {
                    alert("���ʬ�ॳ���ɤ����ꤷ�Ƥ���������");
                    return;
                }
                var formData = workForm.serializeArray();
                // �ꥯ����������
                $.ajax({
                    url: '/so/decide/search_result.php?strSessionID=' + $.cookie('strSessionID'),
                    type: 'post',
                    async: false,
                    data: formData
                })
                    .done(function (response) {
                        console.log("�����ǡ�����" + response);
                        var response = JSON.parse(response);
                        if (response.count == 0) {
                            alert("��������������٤�¸�ߤ��ޤ���");
                            exit;
                        }

                        let changeCode = false;

                        if (parentStrProductCode != strProductCode) {
                            var msg = '���򤵤줿���٤����ƥ��ꥢ���ޤ�����������Ǥ�����';
                            var $tableA_rows = window.opener.$('#tbl_detail tbody tr');
                            var $tableA_rows_length = $tableA_rows.length;
                            var warn = ($tableA_rows_length > 0) ? true : false;
                            if (warn && window.confirm(msg) === false) {
                                exit;
                            }
                            window.opener.$('input[name="strProductCode"]').val(strProductCode);
                            window.opener.$('#table_decide_body tr').remove();
                            window.opener.$('#table_decide_no tr').remove();
                        }

                        window.opener.$('input[name="allSel"]').prop('checked', false);
                        window.opener.$('#tbl_detail tbody tr').remove();
                        window.opener.$('#tbl_detail_chkbox tbody tr').remove();

                        var data = response.result;
                        var tblchkbox = window.opener.$("#tbl_detail_chkbox");
                        var tbl = window.opener.$("#tbl_detail");
                        var tmp_id = "";
                        for (var i = 0; i < data.length; i++) {
                            var row = data[i];
                            var chkbox_id = row.lngreceiveno + "_" + row.lngreceivedetailno + "_" + row.lngrevisionno;
                            var strProductName = row.strproductname;
                            var chkboxstr = '<tr><td style="width: 30px;">'
                                + '<input id="' + chkbox_id + '" style="width: 10px;" type="checkbox">'
                                + '</td></tr>';
                            tblchkbox.append(chkboxstr);
                            var detailstr = '<tr>'
                                + '<td style="width: 25px;"></td>'
                                + '<td style="width: 49px;">' + row.lngreceivedetailno + '</td>'
                                + '<td style="width: 249px;">[' + convertNull(row.strcustomerdisplaycode) + '] ' + convertNull(row.strcustomerdisplayname) + '</td>'
                                + '<td style="width: 119px;">[' + convertNull(row.lngsalesdivisioncode) + '] ' + convertNull(row.strsalesdivisionname) + '</td>'
                                + '<td style="width: 119px;">[' + convertNull(row.lngsalesclasscode) + '] ' + convertNull(row.strsalesclassname) + '</td>'
                                + '<td style="width: 119px;">' + convertNull(row.dtmdeliverydate) + '</td>'
                                + '</tr>';
                            tbl.append(detailstr);
                        }

                        tbl.find('tr').each(function (i, e) {
                            $(this).find('td').first().html(i + 1);
                        });

                        window.opener.$('input[name="strProductName"]').val(strProductName);
                    })
                    .fail(function (response) {
                        console.log("������̡�" + JSON.stringify(response));
                        alert("fail");
                        // Ajax�ꥯ�����Ȥ�����
                    });
            }
            //������ɥ����Ĥ���
            window.close();

            // �ƥ�����ɥ��Υ�å���������
            if (window.opener.$('#lockId').length) {
                window.opener.$('#lockId').remove();
            }
        }
        else {
            // �Х�ǡ������Υ��å�
            workForm.find(':submit').click();
        }
    });
})();


$(window).on("beforeunload", function (e) {
    // �ƥ�����ɥ��Υ�å���������
    if (window.opener.$('#lockId').length) {
        window.opener.$('#lockId').remove();
    }
});
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
