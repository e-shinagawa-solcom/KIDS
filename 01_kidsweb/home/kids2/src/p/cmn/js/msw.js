var pickupGroupCode = function (handleName) {
    if (handleName == "lngInChargeUserCode") {
        var groupCode = $('input[name="lngInChargeGroupCode"]').val();
        var mswUserGroupCode = $($('.msw-user')[0].contentWindow.document).find('.dammy-input-code');
        // ô�����𥳡��ɤ����Ϥ���Ƥ����KWGô����MSW�˥��åȤ���
        if (groupCode) {
            mswUserGroupCode.val(groupCode);
        } else {
            // ô�����𥳡��ɤ����ʤ�msw-user�����𥳡�����򥯥ꥢ
            mswUserGroupCode.val('');
        }
    }
}

var pickupGroupCodeToNull = function (handleName) {
    var mswUserGroupCode = $($('.msw-user')[0].contentWindow.document).find('.dammy-input-code');
    mswUserGroupCode.val('');
}