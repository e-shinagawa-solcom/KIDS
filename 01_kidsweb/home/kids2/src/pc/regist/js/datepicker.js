
// datepickerの要素指定
(function () {

    // datepicker対象要素
    var dateElements = [
        $('input[name="dtmStockAppDate"]')
        , $('input[name="dtmExpirationDate"]')
    ];

    // datepickerの設定
    $.each(dateElements, function () {
        this.datepicker({
            buttonImage: "/img/type01/date/open_off_on_bt.gif",
            buttonImageOnly: true,           // 画像として表示
            showOn: "both",
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
    $('input[name="dtmStockAppDate"], input[name="dtmExpirationDate"]').on('blur', function () {        
        blurDate($(this));
    });
})();


