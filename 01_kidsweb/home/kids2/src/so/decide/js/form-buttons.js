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

                var decideId = "";
                window.opener.$('#table_decide_body tr').each(function (i, e) {
                    console.log($(this).find('#lngreceiveno').text());
                    decideId += $(this).find('#lngreceiveno').text();
                });
                console.log(decideId);
                $('input[name="lngReceiveNo"]').val(decideId);

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
                            window.opener.$('input[name="strProductCode"]').val(response.strProductCode);                            
                            window.opener.$('input[name="strProductName"]').val(response.strProductName);
                            window.opener.$('input[name="lngProductNo"]').val(response.lngProductNo);
                            window.opener.$('input[name="lngProductRevisionNo"]').val(response.lngProductRevisionNo);
                            window.opener.$('input[name="strReviseCode"]').val(response.strReviseCode);
                            window.opener.$('input[name="strGoodsCode"]').val(response.strGoodsCode);
                            window.opener.$('input[name="lngInChargeGroupCode"]').val(response.lngInChargeGroupCode);
                            window.opener.$('input[name="strInChargeGroupName"]').val(response.strInChargeGroupName);
                            window.opener.$('input[name="lngInChargeUserCode"]').val(response.lngInChargeUserCode);
                            window.opener.$('input[name="strInChargeUserName"]').val(response.strInChargeUserName);
                            window.opener.$('input[name="lngDevelopUserCode"]').val(response.lngDevelopUserCode);
                            window.opener.$('input[name="strDevelopUserName"]').val(response.strDevelopUserName);

                            if (response.strGoodsCode != "") {
                                window.opener.$('input[name="strGoodsCode"]').attr('readonly',true);
                            } else {
                                window.opener.$('input[name="strGoodsCode"]').attr('readonly',false);
                            }

                            window.opener.$('#table_decide_body tr').remove();
                            window.opener.$('#table_decide_no tr').remove();
                        }

                        window.opener.$('input[name="allSel"]').prop('checked', false);
                        window.opener.$('#tbl_detail tbody tr').remove();
                        window.opener.$('#tbl_detail_chkbox tbody tr').remove();

                        var data = response.result;
                        var tblchkbox = window.opener.$("#tbl_detail_chkbox");
                        window.opener.$('#tbl_detail_chkbox tbody').append(response.tblA_chkbox_result);
                        window.opener.$('#tbl_detail tbody').append(response.tblA_detail_result);
                        window.opener.$('input[name="strProductName"]').val(response.strProductName);
                        window.opener.$('input[name="strProductCode"]').val(response.strProductCode);
                        window.opener.$('#tbl_detail_chkbox tbody tr td:nth-child(1)').width(window.opener.$('#tbl_detail_chkbox_head tr th:nth-child(1)').width());

                        
                        var thwidthArry = [];
                        var tdwidthArry = [];
                        var columnNum = window.opener.$('#tbl_detail_head tr th').length;
                        for (var i = 1; i <= columnNum; i++) {
                            // console.log(window.opener.$(".table-decide-description").eq(3).find('thead tr th:nth-child(' + i + ')').css('width'));
                            // console.log(window.opener.$(".table-decide-description").eq(0).find('thead tr th:nth-child(' + i + ')').css('width'));
                            // console.log(window.opener.$('#tbl_detail_head tr th:nth-child(' + i + ')').css('width'));

                            var thwidth = Number(window.opener.$(".table-decide-description").eq(3).find('thead tr th:nth-child(' + i + ')').css('width').replace('px', ''));
                            var tdwidth = window.opener.$('#tbl_detail tbody tr td:nth-child(' + i + ')').width();
                            thwidthArry.push(thwidth + 1);
                            tdwidthArry.push(tdwidth + 1);
                        }
// console.log(thwidthArry);
// console.log(tdwidthArry);
                        for (var i = 1; i <= columnNum; i++) {
                            if (thwidthArry[i - 1] > tdwidthArry[i - 1]) {
                                window.opener.$(".table-decide-description thead tr th:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                                window.opener.$(".table-decide-description tbody tr td:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                            } else {
                                window.opener.$(".table-decide-description thead tr th:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
                                window.opener.$(".table-decide-description tbody tr td:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
                            }
                        }
                        // exit;
                        resetTableADisplayStyle();
                        resetTableAWidth();
                        resetTableBWidth();
                        // exit;
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



function resetTableADisplayStyle() {
    window.opener.$(".table-decide-description").eq(2).find("tbody tr td:nth-child(1)").css('display', '');
    window.opener.$(".table-decide-description").eq(0).find("thead tr th:nth-child(3)").css('display', 'none');
    window.opener.$(".table-decide-description").eq(2).find("tbody tr td:nth-child(3)").css('display', 'none');
    window.opener.$(".table-decide-description").eq(0).find("thead tr th:nth-child(8)").css('display', 'none');
    window.opener.$(".table-decide-description").eq(2).find("tbody tr td:nth-child(8)").css('display', 'none');
    window.opener.$(".table-decide-description").eq(0).find("thead tr th:nth-child(9)").css('display', 'none');
    window.opener.$(".table-decide-description").eq(2).find("tbody tr td:nth-child(9)").css('display', 'none');
    window.opener.$(".table-decide-description").eq(0).find("thead tr th:nth-child(10)").css('display', 'none');
    window.opener.$(".table-decide-description").eq(2).find("tbody tr td:nth-child(10)").css('display', 'none');
    window.opener.$(".table-decide-description").eq(0).find("thead tr th:nth-child(11)").css('display', 'none');
    window.opener.$(".table-decide-description").eq(2).find("tbody tr td:nth-child(11)").css('display', 'none');
    window.opener.$(".table-decide-description").eq(0).find("thead tr th:nth-child(12)").css('display', 'none');
    window.opener.$(".table-decide-description").eq(2).find("tbody tr td:nth-child(12)").css('display', 'none');
    window.opener.$(".table-decide-description").eq(0).find("thead tr th:nth-child(13)").css('display', 'none');
    window.opener.$(".table-decide-description").eq(2).find("tbody tr td:nth-child(13)").css('display', 'none');
}

function resetTableAWidth() {
    var width = 0;
    var columnNum = window.opener.$(".table-decide-description").eq(0).find("thead tr th").length;
    for (var i = 1; i <= columnNum; i++) {
        if (window.opener.$(".table-decide-description").eq(0).find("thead tr th:nth-child(" + i + ")").css('display') == "none") {
            // $(".table-decide-description").eq(0).find("thead tr th:nth-child(" + i + ")").css('width', '');
            // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(" + i + ")").css('width', '');
        } else {
            width += window.opener.$(".table-decide-description").eq(0).find("thead tr th:nth-child(" + i + ")").width();
        }
    }
    window.opener.$(".table-decide-description").eq(0).width(width + 25);
    window.opener.$(".table-decide-description").eq(2).width(width + 25);
}


function resetTableBWidth() {
    var width = 0;
    var columnNum = window.opener.$(".table-decide-description").eq(3).find("thead tr th").length;
    for (var i = 1; i <= columnNum; i++) {
        if (window.opener.$(".table-decide-description").eq(3).find("thead tr th:nth-child(" + i + ")").css('display') == "none") {
            // $(".table-decide-description").eq(3).find("thead tr th:nth-child(" + i + ")").css('width', '');
            // $(".table-decide-description").eq(5).find("tbody tr td:nth-child(" + i + ")").css('width', '');
        } else {
            width += window.opener.$(".table-decide-description").eq(3).find("thead tr th:nth-child(" + i + ")").width();
        }
    }
    window.opener.$(".table-decide-description").eq(3).width(width + 50);
    window.opener.$(".table-decide-description").eq(5).width(width + 50);
}

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
