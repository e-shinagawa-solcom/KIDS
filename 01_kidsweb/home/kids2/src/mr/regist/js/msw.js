var pickupGroupCode = function(handleName){
    if( handleName == "KuwagataUserCode" ) {
        var groupCode = $('input[name="KuwagataGroupCode"]').val();
        var mswUserGroupCode = $($('.msw-inchargeuser')[0].contentWindow.document).find('.dammy-input-code');
        // KWG担当部署コードが入力されていればKWG担当者MSWにセットする
        if( groupCode ){
            mswUserGroupCode.val(groupCode);
        } else {
            // KWG担当部署コードが空ならmsw-userの部署コード欄をクリア
            mswUserGroupCode.val('');
        }
    }
}
