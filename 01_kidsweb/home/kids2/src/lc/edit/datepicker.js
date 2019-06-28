$(document).ready(function () {
    // datepicker対象要素
    var ymElements = [
        $("#opendate")
    ];
    
    var ymdElements = [
        $("#bankreqdate"),
        $("#lcamopen"),
        $("#validmonth")
    ];

    // datepickerの設定
    $.each(ymElements, function () {
        this.datepicker({
            dateFormat: 'yymm',
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            language: 'ja',

            onClose: function (dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).val($.datepicker.formatDate('yymm', new Date(year, month, 1)));
            },
            beforeShow: function (input, inst) {
                if ((datestr = $(this).val()).length > 0) {
                    year = datestr.substr(0, 4);
                    month = datestr.substr(4, 2);
                    $(this).datepicker('option', 'defaultDate', new Date(year, month - 1, 1));
                    $(this).datepicker('setDate', new Date(year, month - 1, 1));
                }
                inst.dpDiv.addClass('datepicker-month-year');
            }
        });
    });

    // datepickerの設定
    $.each(ymdElements, function(){
        this.datepicker({
            showButtonPanel: true,
            dateFormat: "yy/mm/dd",
            beforeShow: function (input, inst) {
                inst.dpDiv.removeClass('datepicker-month-year');
            }
        }).attr({
            maxlength: "8"
        });
    });
    
});