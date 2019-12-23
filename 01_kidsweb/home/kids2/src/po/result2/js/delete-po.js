//
// delete-po.js
//
jQuery(function($){

    // events
    $('img#btnClose').on('click', function(){
        window.open('about:blank','_self').close();
        //alert("閉じるボタンクリック");
    });
    $('img#btnDelete').on('click', function(){
        $('#deleteForm').submit();
        //alert("削除ボタンクリック");
    });
});