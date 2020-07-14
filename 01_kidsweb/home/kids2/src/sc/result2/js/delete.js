//
// delete.js
//
jQuery(function ($) {

    // events
    $('#CloseBt').on('click', function () {
        window.close();
        //親ウィンドウをリロードする
        openerReload();
    });
    $('#DeleteBt').on('click', function () {

        console.log(location)
        $('#deleteForm').attr('action', location);
        $('#deleteForm').submit();
    });
});