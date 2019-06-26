$(document).ready(function () {
    // datepicker�о�����
    var ymElements = [
        $("#objectYm"),
        $("#openYm"),
        $("#shipYm")
    ];

    // datepicker������
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
                    month = datestr.substr(4, 2);
                    $(this).datepicker('option', 'defaultDate', new Date(year, month - 1, 1));
                    $(this).datepicker('setDate', new Date(year, month - 1, 1));
                }
                inst.dpDiv.addClass('datepicker-month-year');
            }
        });
    });
    
    // datepicker�о�����
    var dateElements = [
        $("#startDate"),
        $("#endDate")

    ];
    // datepicker������
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