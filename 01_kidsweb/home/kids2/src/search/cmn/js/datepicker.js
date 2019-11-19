// datepicker�����ǻ���
(function () {

    // datepicker�о�����
    var dateElements = [
        $('input[name="ActionDate"]')
        , $('input[name="DtmAppropriationDate"]')
        , $('input[name="Created"]')
        , $('input[name="Updated"]')
    ];

    // datepicker������
    $.each(dateElements, function () {
        this.datepicker({
            buttonImage: "/img/type01/date/open_off_on_bt.gif",
            buttonImageOnly: true,           // �����Ȥ���ɽ��
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
})();
