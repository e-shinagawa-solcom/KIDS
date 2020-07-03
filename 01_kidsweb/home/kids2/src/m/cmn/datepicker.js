
// datepickerの要素指定
(function () {

    var url = location.href;
    console.log(url);
    var dateElements = [];
    if (url.indexOf('lngActionCode=2') >= 0) {
        dateElements = [
            $('input[name="dtmapplyenddate"]')
        ];
    } else {
        dateElements = [
            $('input[name="dtmapplystartdate"]')
            , $('input[name="dtmapplyenddate"]')
        ];
    }
    // datepickerの設定
    $.each(dateElements, function () {
        this.datepicker({
            buttonImage: "/img/type01/date/open_off_on_bt.gif",
            buttonImageOnly: true,           // 画像として表示
            buttonText: "D",
            showOn: "button",
            showButtonPanel: true,
            dateFormat: "yy/mm/dd",
            onClose: function () {
                this.focus();
            }
        }).attr({
            maxlength: "10"
        });
    });

    // 開始日時フォーカスを取ったときの処理
    $('input[name="dtmapplystartdate"], input[name="dtmapplyenddate"]').on('blur', function () {
        blurDate($(this));
    });
})();


