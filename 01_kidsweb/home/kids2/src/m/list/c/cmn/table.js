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
    $("#result_tbl").css('width', '900px');
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

        $("#result_tbl thead").width($("#result_tbl tbody").width() + (5 * columnNum));
        $("#result_tbl tbody").width($("#result_tbl tbody").width() + (5 * columnNum));
        for (var i = 1; i <= columnNum; i++) {
            $("#result_tbl thead tr td:nth-child(" + i + ")").width(widthArry[i - 1]);
            $("#result_tbl tbody tr:nth-child(1) td:nth-child(" + i + ")").width(widthArry[i - 1]);
        }
    }
});