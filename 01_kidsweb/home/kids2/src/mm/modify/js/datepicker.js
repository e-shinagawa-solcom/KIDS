
// datepicker�����ǻ���
(function(){

    // datepicker�о�����
    var dateElements = [
            $('input[name="ActionDate"]')
    ];

    // datepicker������
    $.each(dateElements, function(){
        this.datepicker({
                showButtonPanel: true
            ,   dateFormat: "yy/mm/dd"
            }).attr({
                maxlength: "10"
        });
    });
})();
