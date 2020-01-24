$(document).ready(function () {
    // datepicker対象要素
    var ymElements = [
        $("#objectYm"),
        $("#openYm"),
        $("#shipYm")
    ];

    // datepickerの設定
    $.each(ymElements, function () {
        this.datepicker({
            dateFormat: 'yy/mm',
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            language: 'ja',

            onClose: function (dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).val($.datepicker.formatDate('yy/mm', new Date(year, month, 1)));
            },
            beforeShow: function (input, inst) {
                if ((datestr = $(this).val()).length > 0) {
                    year = datestr.substr(0, 4);
                    month = datestr.substr(5, 2);
                    $(this).datepicker('option', 'defaultDate', new Date(year, month - 1, 1));
                    $(this).datepicker('setDate', new Date(year, month - 1, 1));
                }
                inst.dpDiv.addClass('datepicker-month-year');
            }
        });
    });
    
    // datepicker対象要素
    var dateElements = [
        $("#startDate"),
        $("#endDate")

    ];
    // datepickerの設定
    $.each(dateElements, function(){
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