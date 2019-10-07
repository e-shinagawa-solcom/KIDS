(function () {
    // 詳細ボタンのイベント
    $('img.history.button').on('click', function () {
        var id = $(this).attr('id');
        var revisionNos = $(this).attr('revisionnos').split(",");
        var maxdetailno = $(this).attr('maxdetailno');
        var revisionno = $(this).attr('revisionno');

        for (var i = revisionNos.length - 1; i >= 0; i--) {
            var row = $('tr[id="' + id + '_' + revisionNos[i] + '"]');
            var detailnos = row.attr('detailnos').split(",");
            for (var j = 0; j < detailnos.length; j++) {
                var subrow = $('tr[id="' + id + '_' + revisionNos[i] + '_' + detailnos[j] + '"]');
                var display = subrow.css('display');
                if (display == 'none') {
                    subrow.css("display", "");
                } else {
                    subrow.css("display", "none");
                }
                subrow.insertAfter($('tr[id="' + id + "_" + revisionno + "_" + maxdetailno + '" ]'));
            }

            var display = row.css('display');
                if (display == 'none') {
                    row.css("display", "");
                } else {
                    row.css("display", "none");
                }
            row.insertAfter($('tr[id="' + id + "_" + revisionno + "_" + maxdetailno + '" ]'));
            
        }
    });
})();