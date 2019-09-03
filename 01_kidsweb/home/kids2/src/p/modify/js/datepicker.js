
// datepickerの要素指定
(function(){

    // datepicker対象要素
    var dateElements = [
            $('input[name="RequestDate"]')
        ,   $('input[name="ActionRequestDate"]')
        ,   $('input[name="ReturnSchedule"]')
    ];

    // datepickerの設定
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
