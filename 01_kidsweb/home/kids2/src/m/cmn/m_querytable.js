

//////////////////////////////////////////////////////////////////
///// [修正][追加]時に使用する変数の定義 /////
if( typeof(parent.g_aryArgs) != 'undefined' )
{

	// 処理モード値取得
	var g_strMode = parent.g_aryArgs[0][4];

}



	// デバッグ
	//g_strMode = 'add';





/*

//***************************************************************************
// マスターデータ編集後、「戻る」ボタンで戻る場合に、編集データを
// 「g_aryEditData」に格納しておくための関数
//***************************************************************************
function fncGetEditData()
{

	// 編集フラグ値が「0」の場合
	if( parent.g_EditCnt == 0 )
	{
		for( i = 0; i < window.PS.elements.length; i++ )
		{
			if( window.PS.elements[i].id.substring( 0 , 3 ) == 'Txt' )
			{
				parent.g_aryEditData[i] = window.PS.elements[i].value;
			}
		}

		// 編集フラグ値の変更
		parent.g_EditCnt = 1;

	}

	//alert( parent.g_aryEditData );
	return false;

}

//***************************************************************************
// マスターデータ編集後、「戻る」ボタンで戻ってきた場合に、保持しておいた
// 上記データ「g_aryEditData」を展開するための関数
//***************************************************************************
function fncApplyEditData()
{

	// 編集フラグ値が「1」の場合
	if( parent.g_EditCnt == 1 )
	{
		for( i = 0; i < window.PS.elements.length; i++ )
		{
			if( window.PS.elements[i].id.substring( 0 , 3 ) == 'Txt' )
			{
				window.PS.elements[i].value = parent.g_aryEditData[i];
			}
		}

	// 編集フラグ値の初期化
	parent.g_EditCnt = 0;

	}

	return false;

}

*/




//////////////////////////////////////////////////////////////////
// クエリー取得用関数
function fncGetAction(strAction)
{

	if( g_strMode == 'fix' ||
		g_strMode == 'add' )
	{
		window.PS.action = strAction;
		window.PS.submit();
	}

	// 確認
	else if( g_strMode == '2' ||
			 g_strMode == '1' ||
			 g_strMode == '3' )
	{
		window.PS.action = strAction;
		window.PS.submit();
	}

}





/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 修正用テーブルセット(日本語)
var fixTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="parent.window.close();"><img onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'confirm.php\');"><img onmouseover="MasterRegJOn(this);" onmouseout="MasterRegJOff(this);" src="/img/type01/cmn/seg/regist_off_ja_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';
// 修正用テーブルセット(英語)
var fixTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="parent.window.close();"><img onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'confirm.php\');"><img onmouseover="MasterRegEOn(this);" onmouseout="MasterRegEOff(this);" src="/img/type01/cmn/seg/regist_off_en_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 追加用テーブルセット(日本語)
var addTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="parent.window.close();"><img onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'confirm.php\');"><img onmouseover="MasterRegJOn(this);" onmouseout="MasterRegJOff(this);" src="/img/type01/cmn/seg/regist_off_ja_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';
// 追加用テーブルセット(英語)
var addTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="parent.window.close();"><img onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'confirm.php\');"><img onmouseover="MasterRegEOn(this);" onmouseout="MasterRegEOff(this);" src="/img/type01/cmn/seg/regist_off_en_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';




/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [CONFIRM]修正用テーブルセット(日本語)
var confirmfixTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>修正しますか？</td></tr><tr><td bgcolor="#f1f1f1"><a href="javascript:fncGetAction(\'edit.php\');"><img onmouseover="GrayBackJOn( this );" onmouseout="GrayBackJOff( this );" src="/img/type01/cmn/querybt/back_gray_off_ja_bt.gif" width="72" height="20" border="0" alt="BACK"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'action.php\');"><img onmouseover="MasterRegJOn(this);" onmouseout="MasterRegJOff(this);" src="/img/type01/cmn/seg/regist_off_ja_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';
// [CONFIRM]修正用テーブルセット(英語)
var confirmfixTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Does it fix?</td></tr><tr><td bgcolor="#f1f1f1"><a href="javascript:fncGetAction(\'edit.php\');"><img onmouseover="GrayBackEOn( this );" onmouseout="GrayBackEOff( this );" src="/img/type01/cmn/querybt/back_gray_off_en_bt.gif" width="72" height="20" border="0" alt="BACK"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'action.php\');"><img onmouseover="MasterRegEOn(this);" onmouseout="MasterRegEOff(this);" src="/img/type01/cmn/seg/regist_off_en_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [CONFIRM]追加用テーブルセット(日本語)
var confirmaddTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>追加しますか？</td></tr><tr><td bgcolor="#f1f1f1"><a href="javascript:fncGetAction(\'edit.php\');"><img onmouseover="GrayBackJOn( this );" onmouseout="GrayBackJOff( this );" src="/img/type01/cmn/querybt/back_gray_off_ja_bt.gif" width="72" height="20" border="0" alt="BACK"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'action.php\');"><img onmouseover="MasterRegJOn(this);" onmouseout="MasterRegJOff(this);" src="/img/type01/cmn/seg/regist_off_ja_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';
// [CONFIRM]追加用テーブルセット(英語)
var confirmaddTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Does it addition?</td></tr><tr><td bgcolor="#f1f1f1"><a href="javascript:fncGetAction(\'edit.php\');"><img onmouseover="GrayBackEOn( this );" onmouseout="GrayBackEOff( this );" src="/img/type01/cmn/querybt/back_gray_off_en_bt.gif" width="72" height="20" border="0" alt="BACK"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'action.php\');"><img onmouseover="MasterRegEOn(this);" onmouseout="MasterRegEOff(this);" src="/img/type01/cmn/seg/regist_off_en_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [CONFIRM]削除用テーブルセット(日本語)
var confirmdeleteTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>削除しますか？</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="parent.window.close();"><img onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'action.php\');"><img onmouseover="DeleteJOn( this );" onmouseout="DeleteJOff( this );" src="/img/type01/cmn/seg/delete_off_ja_bt.gif" width="72" height="20" border="0" alt="DELETE"><a></td></tr></table>';
// [CONFIRM]削除用テーブルセット(英語)
var confirmdeleteTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Does it delete?</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="parent.window.close();"><img onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'action.php\');"><img onmouseover="DeleteEOn( this );" onmouseout="DeleteEOff( this );" src="/img/type01/cmn/seg/delete_off_en_bt.gif" width="72" height="20" border="0" alt="DELETE"><a></td></tr></table>';






//////////////////////////////////////////////////////////////////
// クエリーボタンテーブル書き出しモジュール
function fncTitleOutput( lngCode )
{


	switch( g_strMode )
	{

		// [修正]
		case 'fix':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'FIX';
				objQuery.innerHTML = fixTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '修正';
				objQuery.innerHTML = fixTableJ;
			}

			break;


		// [追加]
		case 'add':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'ADDITION';
				objQuery.innerHTML = addTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '追加';
				objQuery.innerHTML = addTableJ;
			}

			break;


		// [修正確認]
		case '2':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'FIX';
				objQuery.innerHTML = confirmfixTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '修正確認';
				objQuery.innerHTML = confirmfixTableJ;
			}

			break;


		// [追加確認]
		case '1':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'ADDITION';
				objQuery.innerHTML = confirmaddTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '追加確認';
				objQuery.innerHTML = confirmaddTableJ;
			}

			break;


		// [削除確認]
		case '3':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'DELETE';
				objQuery.innerHTML = confirmdeleteTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '削除確認';
				objQuery.innerHTML = confirmdeleteTableJ;
			}

			break;


		default:
			break;

	}


	return false;
}






//////////////////////////////////////////////////////////////////
// [デバッグ]HTMLソースの表示関数
function fncShowHTML( strID )
{
	// 指定のタグ(ID)内のテキストレンジを取得する
	var doc1 = document.body.createTextRange();

	doc1.moveToElementText(strID);

	alert(doc1.htmlText);
}