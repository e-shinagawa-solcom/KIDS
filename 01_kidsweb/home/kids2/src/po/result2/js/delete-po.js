//
// delete-po.js
//
jQuery(function($){

    // events
    $('img#btnClose').on('click', function(){
        window.open('about:blank','_self').close();
        //alert("�Ĥ���ܥ��󥯥�å�");
    });
    $('img#btnDelete').on('click', function(){
        $('#deleteForm').submit();
        //alert("����ܥ��󥯥�å�");
    });
});