
// datepicker�����ǻ���
(function(){

    // datepicker�о�����
    var dateElements = [
            $('input[name="ActionDate"]')
    ];

    // datepicker������
    $.each(dateElements, function(){
        this.datepicker({
                showButtonPanel: true,
                dateFormat: "yy/mm/dd",
                onClose: function(){
                    this.focus();
                }
            }).attr({
                maxlength: "10"
        });
    });
})();
