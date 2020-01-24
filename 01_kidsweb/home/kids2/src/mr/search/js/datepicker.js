
// datepickerの要素指定
(function(){

    // datepicker対象要素
    var dateElements = [
          $('input[name$="RequestDate"]:not([type])')
        , $('input[name$="ActionRequestDate"]:not([type])')
        , $('input[name$="ReturnSchedule"]:not([type])')
        , $('input[name$="Created"]:not([type])')
        , $('input[name$="Updated"]:not([type])')
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
