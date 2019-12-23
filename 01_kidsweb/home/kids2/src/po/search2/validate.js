//
// validation.js
//
jQuery(function($){

   function changeAdmin(mode){
        var targets = [
            $('input[name="IsDisplay_strProductCode"]'),
            $('input[name="IsDisplay_lngInChargeGroupCode"]'),
            $('input[name="IsDisplay_lngInChargeUserCode"]'),
            $('input[name="IsDisplay_strNote"]'),
        ];

        if(mode){
            $.each(targets, function(){
                $(this).prop('checked', false);
                $(this).attr('disabled', 'disabled');
            });
            // console.log("checked");
        } else {
            $.each(targets, function(){
                $(this).attr('disabled', false);
            });
            $('input[name="IsDisplay_lngRecordNo"]').prop('checked', true);
            // console.log("unchecked");
        }
    }

    // events
    $('input[name="IsDisplay_btnAdmin"]').on('change', function(){
        changeAdmin($(this).prop('checked'));
    });
});
