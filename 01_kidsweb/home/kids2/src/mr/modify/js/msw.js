var pickupGroupCode = function(handleName){
    if( handleName == "KuwagataUserCode" ) {
        var groupCode = $('input[name="KuwagataGroupCode"]').val();
        var mswUserGroupCode = $($('.msw-inchargeuser')[0].contentWindow.document).find('.dammy-input-code');
        // KWGô�����𥳡��ɤ����Ϥ���Ƥ����KWGô����MSW�˥��åȤ���
        if( groupCode ){
            mswUserGroupCode.val(groupCode);
        } else {
            // KWGô�����𥳡��ɤ����ʤ�msw-user�����𥳡�����򥯥ꥢ
            mswUserGroupCode.val('');
        }
    }
}
