var pickupGroupCode = function (handleName) {
    if (handleName == "lngInChargeUserCode") {
        var groupCode = $('input[name="lngInChargeGroupCode"]').val();
        var mswUserGroupCode = $($('.msw-user')[0].contentWindow.document).find('.dammy-input-code');
        // 担当部署コードが入力されていればKWG担当者MSWにセットする
        if (groupCode) {
            mswUserGroupCode.val(groupCode);
        } else {
            // 担当部署コードが空ならmsw-userの部署コード欄をクリア
            mswUserGroupCode.val('');
        }
    }
}

var pickupGroupCodeToNull = function (handleName) {
    var mswUserGroupCode = $($('.msw-user')[0].contentWindow.document).find('.dammy-input-code');
    mswUserGroupCode.val('');
}