

// 処理モード値取得
var g_strMode = parent.g_aryArgs[0][4];

// [lngLanguageCode]値取得
// var g_lngCode = parent.lngLanguageCode.value;
var g_lngCode = 1;







// クエリーボタンテーブル書き出しモジュール
function fncTitleOutput( lngCode )
{

	// 削除確認用テーブルセット(日本語)
	var deleteTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>削除しますか？</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();window.returnValue=true;window.close();"><img onmouseover="DeleteJOn( this );" onmouseout="DeleteJOff( this );" src="/img/type01/cmn/seg/delete_off_ja_bt.gif" width="72" height="20" border="0" alt="DELETE"><a></td></tr></table>';
	// 削除確認用テーブルセット(英語)
	var deleteTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Does it delete?</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();window.returnValue=true;window.close();"><img onmouseover="DeleteEOn( this );" onmouseout="DeleteEOff( this );" src="/img/type01/cmn/seg/delete_off_en_bt.gif" width="72" height="20" border="0" alt="DELETE"><a></td></tr></table>';

	// 詳細確認用テーブルセット(日本語)
	var detailTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="alert("test");window.close();"><img onclick="window.close();" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a></td></tr></table>';
	// 詳細確認用テーブルセット(英語)
	var detailTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a></td></tr></table>';


	if( g_strMode == 'detail' )
	{
		if( lngCode == 0 )
		{
			ControlTitle.innerText = 'DETAIL';
			objQuery.innerHTML = detailTableE;
		}
		else if( lngCode == 1 )
		{
			ControlTitle.innerText = '詳細確認';
			objQuery.innerHTML = detailTableJ;
		}
	}
	else if( g_strMode == 'delete' )
	{
		if( lngCode == 0 )
		{
			ControlTitle.innerText = 'DELETE';
			objQuery.innerHTML = deleteTableE;
		}
		else if( lngCode == 1 )
		{
			ControlTitle.innerText = '削除確認';
			objQuery.innerHTML = deleteTableJ;
		}
	}

	return false;
}





// 日本語英語切替
function fncChgEtoJ( strMode )
{

	// 英語切替
	if( g_lngCode == 0 )
	{

		// クエリーボタンテーブル書き出し
		//fncTitleOutput( 0 );

		// 処理用テーブル書き出し
		fncProcessingOutputModule( strMode , 0 );

		bytInvalidFlag.innerText        = 'Login permission';
		lngUserCode.innerText           = 'User code';
		strUserID.innerText             = 'User ID';
		bytMailTransmitFlag.innerText   = 'Email permission';
		strMailAddress.innerText        = 'Email';
		bytUserDisplayFlag.innerText    = 'User permission';
		strUserDisplayCode.innerText    = 'Display user code';
		strUserDisplayName.innerText    = 'Display user name';
		strUserFullName.innerText       = 'User full name';
		lngCompanyCode.innerText        = 'Company';
		aryGroup.innerText              = 'Group';
		strAuthorityGroupName.innerText = 'Authority group';
		strAccessIPAddress.innerText    = 'Access IP Address';
		strUserImageFileName.innerHTML  = 'User image';
		strNote.innerText               = 'Remark';

		if( typeof(strPasswd) != 'undefined' )
		{
			strPasswd.innerHTML = 'Password';
		}

		if( typeof(strMyPageInfo) != 'undefined' )
		{
			strMyPageInfo.innerHTML = 'My page info';
		}

	}

	// 日本語切替
	else if( g_lngCode == 1 )
	{

		// クエリーボタンテーブル書き出し
		//fncTitleOutput( 1 );

		// 処理用テーブル書き出し
		fncProcessingOutputModule( strMode , 1 );

		bytInvalidFlag.innerText        = 'ログイン許可';
		lngUserCode.innerText           = 'ユーザーコード';
		strUserID.innerText             = 'ユーザーID';
		bytMailTransmitFlag.innerText   = 'メール配信許可';
		strMailAddress.innerText        = 'メールアドレス';
		bytUserDisplayFlag.innerText    = 'ユーザー表示';
		strUserDisplayCode.innerText    = '表示ユーザーコード';
		strUserDisplayName.innerText    = '表示ユーザー名';
		strUserFullName.innerText       = 'フルネーム';
		lngCompanyCode.innerText        = '会社';
		aryGroup.innerText              = 'グループ';
		strAuthorityGroupName.innerText = '権限グループ';
		strAccessIPAddress.innerText    = 'アクセスIPアドレス';
		strUserImageFileName.innerHTML  = 'ユーザーイメージ';
		strNote.innerText               = '備考';

		if( typeof(strPasswd) != 'undefined' )
		{
			strPasswd.innerHTML = 'パスワード';
		}

		if( typeof(strMyPageInfo) != 'undefined' )
		{
			strMyPageInfo.innerHTML = 'マイページ情報';
		}

	}

	return false;
}