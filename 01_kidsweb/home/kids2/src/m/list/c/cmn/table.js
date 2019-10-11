$(document).ready(function () {
    var row = $("#result_tbl thead tr");
    var columnNum = row.find('td').length;
    var widthArry = [];
    var theadwidth = $("#result_tbl thead").width();
    for (var i = 1; i <= columnNum; i++) {
        var width = $("#result_tbl thead tr td:nth-child(" + i + ")").width();
        var tdwidth = $("#result_tbl tbody tr:nth-child(1) td:nth-child(" + i + ")").width();
        widthArry.push(width);
    }
    $("#result_tbl").css('display', 'block');
    $("#result_tbl").css('overflow-y', 'scroll');
    $("#result_tbl").css('padding-top', '0px');
    $("#result_tbl").css('width', '912px');
    $("#result_tbl").css('height', '422px');
    $("#result_tbl").css('table-layout', 'fixed');
    $("#result_tbl tbody").css('display', 'block');
    $("#result_tbl thead").css('display', 'block');
    $("#result_tbl thead").css('position', 'sticky');
    $("#result_tbl thead").css('top', '0');
    $("#result_tbl thead").css('z-index', '2');

    if ($('input[name="strTableName"]').val() == "m_StockItem") {
        $("#result_tbl thead").width($("#result_tbl tbody").width() + (5 * columnNum) + 100);
        $("#result_tbl tbody").width($("#result_tbl tbody").width() + (5 * columnNum) + 100);
        for (var i = 1; i <= columnNum; i++) {
            $("#result_tbl thead tr td:nth-child(" + i + ")").width(widthArry[i - 1] + 20);
            $("#result_tbl tbody tr:nth-child(1) td:nth-child(" + i + ")").width(widthArry[i - 1] + 20);
        }
    } else {
        $("#result_tbl thead").width($("#result_tbl tbody").width());
        $("#result_tbl tbody").width($("#result_tbl tbody").width());
        for (var i = 1; i <= columnNum; i++) {
            $("#result_tbl thead tr td:nth-child(" + i + ")").width(widthArry[i - 1]);
            $("#result_tbl tbody tr:nth-child(1) td:nth-child(" + i + ")").width(widthArry[i - 1]);
        }
    }
    // if ($('input[name="strTableName"]').val() == "m_Company") {
    //     $("#result thead tr td:nth-child(2)").find('div').text('��ҥ�����');
    //     $("#result thead tr td:nth-child(3)").find('div').text('�񥳡���');
    //     $("#result thead tr td:nth-child(4)").find('div').text('�ȿ�������');
    //     $("#result thead tr td:nth-child(5)").find('div').text('�ȿ�ɽ��');
    //     $("#result thead tr td:nth-child(6)").find('div').text('���̾��');
    //     $("#result thead tr td:nth-child(7)").find('div').text('ɽ����ҵ���');
    //     $("#result thead tr td:nth-child(8)").find('div').text('ɽ����ҥ�����');
    //     $("#result thead tr td:nth-child(9)").find('div').text('ɽ�����̾��');
    //     $("#result thead tr td:nth-child(10)").find('div').text('��ά̾��');
    //     $("#result thead tr td:nth-child(11)").find('div').text('͹���ֹ�');
    //     $("#result thead tr td:nth-child(12)").find('div').text('����1 / ��ƻ�ܸ�');
    //     $("#result thead tr td:nth-child(13)").find('div').text('����2 / �ԡ��衢��');
    //     $("#result thead tr td:nth-child(14)").find('div').text('����3 / Į������');
    //     $("#result thead tr td:nth-child(15)").find('div').text('����4 / �ӥ�������ʪ̾');
    //     $("#result thead tr td:nth-child(16)").find('div').text('�����ֹ�1');
    //     $("#result thead tr td:nth-child(17)").find('div').text('�����ֹ�2');
    //     $("#result thead tr td:nth-child(18)").find('div').text('�ե��å����ֹ�1');
    //     $("#result thead tr td:nth-child(19)").find('div').text('�ե��å����ֹ�2');
    //     $("#result thead tr td:nth-child(20)").find('div').text('���̥�����');
    //     $("#result thead tr td:nth-child(21)").find('div').text('������������');
    //     $("#result thead tr td:nth-child(22)").find('div').text('���°��');
    // }
});