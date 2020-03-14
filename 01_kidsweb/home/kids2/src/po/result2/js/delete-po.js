//
// delete-po.js
//
jQuery(function($){

    // events
    $('#btnClose').on('click', function(){
        window.opener.location.reload();
        window.close();
        //alert("閉じるボタンクリック");
    });
    $('#btnDelete').on('click', function(){
        $('#deleteForm').submit();
        //alert("削除ボタンクリック");
    });
});