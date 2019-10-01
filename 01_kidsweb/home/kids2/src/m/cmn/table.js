$(document).ready(function () {
    $("#ResultIframe", parent.document).css("width", "100%");
    $("#ResultIframe", parent.document).css("height", "100%");
    var row = $("#result_tbl thead tr:nth-child(2)");
    var columnNum = row.find('td').length;
    var widthArry = [];
    var theadwidth = $("#result_tbl thead").width();
    for (var i = 1; i <= columnNum; i++) {
        var width = $("#result_tbl thead tr:nth-child(2) td:nth-child(" + i + ")").width();
        var text = $("#result_tbl thead tr:nth-child(2) td:nth-child(" + i + ")").text();
        var tdwidth = $("#result_tbl tbody tr:nth-child(2) td:nth-child(" + i + ")").width();
        widthArry.push(width);
    }
    $("#result_tbl").css('display', 'block');
    $("#result_tbl").css('overflow-y', 'scroll');
    $("#result_tbl").css('padding-top', '0px');
    // $("#result_tbl").css('width', '990px');
    $("#result_tbl").css('width', '99%');
    $("#result_tbl").css('height', '97%');
    // $("#result_tbl").css('height', '640px');
    $("#result_tbl").css('table-layout', 'fixed');
    $("#result_tbl tbody").css('display', 'block');
    $("#result_tbl thead").css('display', 'block');
    $("#result_tbl thead").css('position', 'sticky');
    $("#result_tbl thead").css('top', '0');
    $("#result_tbl thead").css('z-index', '2');
    if ($('input[name="strTableName"]').val() == "m_Company") {
        $("#result_tbl thead").width($("#result_tbl tbody").width() + (10 * columnNum) + 2450);
        $("#result_tbl tbody").width($("#result_tbl tbody").width() + (10 * columnNum) + 2450);
    } else {
        $("#result_tbl thead").width($("#result_tbl tbody").width() + (2 * columnNum));
        $("#result_tbl tbody").width($("#result_tbl tbody").width() + (2 * columnNum));

    }
    $("#result_tbl tbody tr:nth-child(1) th").width(widthArry[0] + 10);
    $("#result_tbl thead tr:nth-child(2) td:nth-child(1)").width(widthArry[0] + 10);
    for (var i = 2; i <= columnNum; i++) {
        $("#result_tbl thead tr:nth-child(2) td:nth-child(" + i + ")").width(widthArry[i - 1]+10);
        $("#result_tbl tbody tr:nth-child(1) td:nth-child(" + i + ")").width(widthArry[i - 1]+10);
    }
});