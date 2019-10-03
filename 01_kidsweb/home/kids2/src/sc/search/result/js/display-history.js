(function () {
    // 詳細ボタンのイベント
    $('img.history.button').on('click', function () {
        var strSalesCode = $(this).attr('id');
        var revisionNos = $(this).attr('revisionnos').split(",");
        alert(revisionNos);
        for (var i = revisionNos.length - 1; i >= 0; i--) {
            var row = $('tr[id="' + strSalesCode + '_' + revisionNos[i] + '"]');
            var display = row.css('display');
            if (display == 'none') {
                row.css("display", "");
            } else {
                row.css("display", "none");
            }
            row.insertAfter($('tr[id="' + strSalesCode + '"]'));
        }
    });
})();