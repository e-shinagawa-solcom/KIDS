//
// delete.js
//
jQuery(function ($) {

    // events
    $('#btnClose').on('click', function () {
        window.close();
        //親ウィンドウをリロードする
        openerReload();
    });
    $('#btnDelete').on('click', function () {
        $('#deleteForm').attr('action', location);
        $('#deleteForm').submit();
    });
});