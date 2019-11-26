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
    $('input[name="lngCustomerCode"]').change();
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

                var lngCustomerCode = $('input[name="lngCustomerCode"]').val();
                if (lngCustomerCode.length == 0) {
                    alert("�ܵҥ����ɤ����ꤷ�Ƥ���������")
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
                        var data = response.result;                        
                        if (response.count == 0) {
                            alert("��������������٤�¸�ߤ��ޤ���");
                        }
                        
                        var tblchkbox = window.opener.$("#tbl_detail_chkbox");
                        var tbl = window.opener.$("#tbl_detail");
                        var tmp_id = "";
                        for (var i = 0; i < data.length; i++) {
                            var row = data[i];
                            var chkbox_id = row.lngreceiveno + "_" + row.lngreceivedetailno + "_" + row.lngrevisionno;
                            var isInserted = false;
                            tblchkbox.find('tr').each(function (i, e) {
                                tmp_id = $(this).find('td').first().find('input:checkbox').attr('id');
                                if (tmp_id == chkbox_id) {
                                    isInserted = true;
                                }
                            });

                            if (!isInserted) {
                                var chkboxstr = '<tr><td style="width: 30px;">'
                                    + '<input id="' + chkbox_id + '" style="width: 10px;" type="checkbox">'
                                    + '</td></tr>';
                                tblchkbox.append(chkboxstr);

                                var detailstr = '<tr>'
                                    + '<td style="width: 25px;"></td>'
                                    + '<td style="width: 100px;">' + row.strreceivecode + '</td>'
                                    + '<td style="width: 70px;">' + row.lngreceivedetailno + '</td>'
                                    + '<td style="width: 250px;" nowrap>[' + convertNull(row.strproductcode) + '] ' + convertNull(row.strproductname) + '</td>'
                                    + '<td>' + convertNull(row.dtmdeliverydate) + '</td>'
                                    + '<td style="width: 120px;">[' + convertNull(row.lngsalesclasscode) + '] ' + convertNull(row.strsalesclassname) + '</td>'
                                    + '<td style="width: 250px;">[' + convertNull(row.strcustomerdisplaycode) + '] ' + convertNull(row.strcustomerdisplayname) + '</td>'
                                    + '</tr>';

                                tbl.append(detailstr);
                            }
                        }

                        tbl.find('tr').each(function (i, e) {
                            $(this).find('td').first().html(i+1);
                        });
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

 
$(window).on("beforeunload", function(e) {
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
