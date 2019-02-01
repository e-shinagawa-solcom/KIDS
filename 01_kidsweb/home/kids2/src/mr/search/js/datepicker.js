
// datepicker�����ǻ���
(function(){

    // datepicker�о�����
    var dateElements = [
          $('input[name$="RequestDate"]:not([type])')
        , $('input[name$="ActionRequestDate"]:not([type])')
        , $('input[name$="ReturnSchedule"]:not([type])')
        , $('input[name$="Created"]:not([type])')
        , $('input[name$="Updated"]:not([type])')
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
