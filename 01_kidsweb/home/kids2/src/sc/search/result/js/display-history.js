(function () {
    // 詳細ボタンのイベント
    $('img.history.button').on('click', function () {
        var strProductCode = $(this).attr('id');
        var revisionNos = $(this).attr('revisionnos').split(",");
        for (var i = revisionNos.length - 1; i >= 0; i--) {
            var row = $('tr[id="' + strProductCode + '_' + revisionNos[i] + '"]');
            var display = row.css('display');
            if (display == 'none') {
                row.css("display", "");
            } else {
                row.css("display", "none");
            }
            row.insertAfter($('tr[id="' + strProductCode + '"]'));
        }
    });
})();