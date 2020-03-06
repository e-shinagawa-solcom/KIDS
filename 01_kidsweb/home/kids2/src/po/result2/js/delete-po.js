//
// delete-po.js
//
jQuery(function($){

    // events
    $('img#btnClose').on('click', function(){
        window.opener.location.reload();
        window.close();
        //alert("閉じるボタンクリック");
    });
    $('img#btnDelete').on('click', function(){
        $('#deleteForm').submit();
        //alert("削除ボタンクリック");
    });
});