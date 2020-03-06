//
// delete.js
//
jQuery(function ($) {

    // events
    $('img#btnClose').on('click', function () {
        window.opener.location.reload();
        window.close();
    });
    $('img#btnDelete').on('click', function () {
        $('#deleteForm').submit();
        //alert("削除ボタンクリック");
    });
});