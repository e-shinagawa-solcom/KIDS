$(document).ready(function () {
    var row = $(".table-description tbody tr");
    var columnNum = row.find('td').length;
    var widthArry = [];
    for (var i = 1; i <= 14; i++) {
        var width = $(".table-description tbody tr td:nth-child(" + i + ")").width();
        widthArry.push(width);
    }
    for (var i = 1; i <= 14; i++) {
        $(".table-description thead tr th:nth-child(" + i + ")").width(widthArry[i-1]+1);
    }
});