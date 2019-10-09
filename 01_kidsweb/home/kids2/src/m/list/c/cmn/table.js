$(document).ready(function () {
    // var row = $("#result_tbl thead tr");
    // var columnNum = row.find('td').length;
    // var widthArry = [];
    // var theadwidth = $("#result_tbl thead").width();
    // for (var i = 1; i <= columnNum; i++) {
    //     var width = $("#result_tbl thead tr td:nth-child(" + i + ")").width();
    //     var tdwidth = $("#result_tbl tbody tr:nth-child(1) td:nth-child(" + i + ")").width();
    //     widthArry.push(width);
    // }
    // $("#result_tbl").css('display', 'block');
    // $("#result_tbl").css('overflow-y', 'scroll');
    // $("#result_tbl").css('padding-top', '0px');
    // $("#result_tbl").css('width', '912px');
    // $("#result_tbl").css('height', '422px');
    // $("#result_tbl").css('table-layout', 'fixed');
    // $("#result_tbl tbody").css('display', 'block');
    // $("#result_tbl thead").css('display', 'block');
    // $("#result_tbl thead").css('position', 'sticky');
    // $("#result_tbl thead").css('top', '0');
    // $("#result_tbl thead").css('z-index', '2');

    // if ($('input[name="strTableName"]').val() == "m_StockItem") {
    //     $("#result_tbl thead").width($("#result_tbl tbody").width() + (5 * columnNum) + 100);
    //     $("#result_tbl tbody").width($("#result_tbl tbody").width() + (5 * columnNum) + 100);
    //     for (var i = 1; i <= columnNum; i++) {
    //         $("#result_tbl thead tr td:nth-child(" + i + ")").width(widthArry[i - 1] + 20);
    //         $("#result_tbl tbody tr:nth-child(1) td:nth-child(" + i + ")").width(widthArry[i - 1] + 20);
    //     }
    // } else {
    //     $("#result_tbl thead").width($("#result_tbl tbody").width());
    //     $("#result_tbl tbody").width($("#result_tbl tbody").width());
    //     for (var i = 1; i <= columnNum; i++) {
    //         $("#result_tbl thead tr td:nth-child(" + i + ")").width(widthArry[i - 1]);
    //         $("#result_tbl tbody tr:nth-child(1) td:nth-child(" + i + ")").width(widthArry[i - 1]);
    //     }
    // }
    if ($('input[name="strTableName"]').val() == "m_Company") {
        $("#result thead tr td:nth-child(2)").find('div').text('会社コード');
        $("#result thead tr td:nth-child(3)").find('div').text('国コード');
        $("#result thead tr td:nth-child(4)").find('div').text('組織コード');
        $("#result thead tr td:nth-child(5)").find('div').text('組織表記');
        $("#result thead tr td:nth-child(6)").find('div').text('会社名称');
        $("#result thead tr td:nth-child(7)").find('div').text('表示会社許可');
        $("#result thead tr td:nth-child(8)").find('div').text('表示会社コード');
        $("#result thead tr td:nth-child(9)").find('div').text('表示会社名称');
        $("#result thead tr td:nth-child(10)").find('div').text('省略名称');
        $("#result thead tr td:nth-child(11)").find('div').text('郵便番号');
        $("#result thead tr td:nth-child(12)").find('div').text('住所1 / 都道府県');
        $("#result thead tr td:nth-child(13)").find('div').text('住所2 / 市、区、郡');
        $("#result thead tr td:nth-child(14)").find('div').text('住所3 / 町、番地');
        $("#result thead tr td:nth-child(15)").find('div').text('住所4 / ビル等、建物名');
        $("#result thead tr td:nth-child(16)").find('div').text('電話番号1');
        $("#result thead tr td:nth-child(17)").find('div').text('電話番号2');
        $("#result thead tr td:nth-child(18)").find('div').text('ファックス番号1');
        $("#result thead tr td:nth-child(19)").find('div').text('ファックス番号2');
        $("#result thead tr td:nth-child(20)").find('div').text('識別コード');
        $("#result thead tr td:nth-child(21)").find('div').text('締め日コード');
        $("#result thead tr td:nth-child(22)").find('div').text('会社属性');
    }
});