//
// delete.js
//
jQuery(function ($) {

    // events
    $('#btnClose').on('click', function () {
        window.opener.location.reload();
        window.close();
    });
    $('#btnDelete').on('click', function () {
        $('#deleteForm').submit();
        //alert("削除ボタンクリック");
    });
});