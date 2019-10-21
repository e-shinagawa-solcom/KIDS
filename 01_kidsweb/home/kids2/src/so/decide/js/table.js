$(document).ready(function () {
    var row = $(".table-description tbody tr");
    var columnNum = row.find('td').length;
    var thwidthArry = [];
    var tdwidthArry = [];
    for (var i = 2; i <= 8; i++) {
        var width = $(".table-description thead tr th:nth-child(" + i + ")").width();
        thwidthArry.push(width);
        var tdwidth = $(".table-description tbody tr td:nth-child(" + i + ")").width();
        tdwidthArry.push(tdwidth);
    }
    for (var i = 2; i <= 8; i++) {
        if (thwidthArry[i - 2] > tdwidthArry[i - 2]) {
            $(".table-description thead tr th:nth-child(" + i + ")").width(thwidthArry[i - 2]);
        } else {
            $(".table-description thead tr th:nth-child(" + i + ")").width(tdwidthArry[i - 2]);
        }
    }
    for (var i = 2; i <= 8; i++) {
        if (thwidthArry[i - 2] > tdwidthArry[i - 2]) {
            $(".table-description tbody tr td:nth-child(" + i + ")").width(thwidthArry[i - 2]);
        } else {
            $(".table-description tbody tr td:nth-child(" + i + ")").width(tdwidthArry[i - 2]);

        }
    }
});