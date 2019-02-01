
/**
*
*	@charset	: euc-jp
*/



	// 空欄チェック、ファイルアップロード
	function fncCheckField()
	{
		if( document.exc_upload.excel_file.value.length == 0 )
		{
			alert( 'ファイルを指定してください。' );
			return false;
		}

		window.exc_upload.submit();
		return true;
	}

	// ダイアログ表示処理
	function fncFileUpload( lngValue, objFrm )
	{
		// ダイアログ表示フラグが無効の場合
		if( lngValue != "1" )
		{
			return false;
		}
		else
		{
			// 結果ダイアログ展開
			GoResult( objFrm, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES' );
		}

		return true;
	}
