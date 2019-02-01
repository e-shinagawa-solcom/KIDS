<!--
/************************* [ ワークフロー順序マスタ ] *************************/



/*
//////////////////////////////////////////////////////////////////
////////// [修正][追加]時・オブジェクトのオンロード処理関数 //////////
function fncEditObjectOnload( lngLangCode )
{
	// オブジェクトの自動レイアウト
	fncInitLayoutObjectModule( objColumn , objInput  , 60 , 216 );


	// [追加]
	if( g_strMode == 'add' )
	{
		// オブジェクトのID変換
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2' , 'Input3' , 'Input4'  ) ,

								 Array( 'Txt40L' , 'Txt40L' , 'Txt40L' , 'Txt40L' , 'Txt40L' ) );
	}


	// [lngLanguageCode]値の初期化
	var g_lngCode = '';

	// [lngLanguageCode]値の取得
	g_lngCode = lngLangCode;


	// オブジェクトの日本語・英語切替
	ChgEtoJ( g_lngCode );


	return true;
}
*/






//////////////////////////////////////////////////////////////////////
////////// [追加]時・クエリーテーブルモジュール //////////
function fncQueryTable( lngCode )
{
	if( lngCode == 0 )
	{
		objQuery.innerHTML = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1">	<a href="#" onclick="window.close();"><img onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="javascript:window.form1.reset();"><img  onmouseover="fncGrayClearButton( \'onE\' , this );" onmouseout="fncGrayClearButton( \'offE\' , this );" src="/img/type01/cmn/querybt/grayclear_off_en_bt.gif" width="72" height="20" border="0" alt="CLEAR"><a>&nbsp;&nbsp;<a href="javascript:window.form1.submit();"><img onmouseover="MasterRegEOn(this);" onmouseout="MasterRegEOff(this);" src="/img/type01/cmn/seg/regist_off_en_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';
	}
	else if( lngCode == 1 )
	{
		objQuery.innerHTML = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="javascript:window.form1.reset();"><img  onmouseover="fncGrayClearButton( \'onJ\' , this );" onmouseout="fncGrayClearButton( \'offJ\' , this );" src="/img/type01/cmn/querybt/grayclear_off_ja_bt.gif" width="72" height="20" border="0" alt="CLEAR"><a>&nbsp;&nbsp;<a href="javascript:window.form1.submit();"><img onmouseover="MasterRegJOn(this);" onmouseout="MasterRegJOff(this);" src="/img/type01/cmn/seg/regist_off_ja_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';
	}
}





//////////////////////////////////////////////////////////////////////
////////// [追加]時・オブジェクトのオンロード処理関数 //////////
function fncEditObjectOnload( lngCode )
{

	// クエリーテーブルＴＯＰ座標
	if( typeof(QueryTable) != 'undefined' )
	{
		QueryTable.style.top = 400;
	}


	// [追加]
	if( lngCode == 0 )
	{
		ControlTitle.innerText	= 'ADDITION';

		// カラム名
		Column0.innerText	= 'Order name';
		Column1.innerText	= 'Select order group';
		Column2.innerText	= 'Select in charge';
		Column3.innerText	= 'Limit Date';
		Column4.innerText	= 'Order';
		Column5.innerText	= 'Add';

		fncQueryTable( lngCode );
	}

	else if( lngCode == 1 )
	{
		ControlTitle.innerText	= '追加';

		// カラム名
		Column0.innerText	= 'ワークフロー順序名称';
		Column1.innerText	= 'グループ選択';
		Column2.innerText	= '担当者選択';
		Column3.innerText	= '期限日';
		Column4.innerText	= '順序';
		Column5.innerText	= '追加';

		fncQueryTable( lngCode );
	}


	return true;
}









//////////////////////////////////////////////////////////////////
////////// [CONFIRM]時・オブジェクトのオンロード処理関数 //////////
function fncConfirmObjectOnload( strMode , lngLangCode )
{

	// 処理モードの初期化
	g_strMode = '';

	// 処理モードの取得
	g_strMode = strMode;


	// [lngLanguageCode]値の初期化
	var g_lngCode = '';

	// [lngLanguageCode]値の取得
	g_lngCode = lngLangCode;


	// オブジェクトの日本語・英語切替
	ChgEtoJ( g_lngCode );


	return true;
}









//////////////////////////////////////////////////////////////////
////////// ヘッダーイメージの生成 //////////
var headerAJ = '<img src="' + h_wfJ + '" width="949" height="30" border="0" alt="ワークフロー順序マスタ">';
var headerAE = '<img src="' + h_wfE + '" width="949" height="30" border="0" alt="WORK FLOW ORDER MASTER">';











//////////////////////////////////////////////////////////////////
////////// 日本語・英語切替モジュール //////////
function ChgEtoJ( g_lngCode )
{


	// 英語
	if ( g_lngCode == 0 )
	{

		// 追加ボタンイメージ書き出し
		if( typeof(MasterAddBt) != 'undefined' )
		{
			MasterAddBt.innerHTML = maddbtE1;
		}


		// クエリーボタンテーブル書き出し
		if( typeof(fncTitleOutput) != 'undefined' )
		{
			fncTitleOutput( 0 );
		}


		// カラム名
		Column0.innerText	= 'Order name';
		Column1.innerText	= 'Order group name';

		if( typeof(Column2) != 'undefined' )
		{
			Column2.innerText	= 'Order';
		}

		if( typeof(Column3) != 'undefined' )
		{
			Column3.innerText	= 'In charge name';
		}

		if( typeof(Column4) != 'undefined' )
		{
			Column4.innerText	= 'Limit date';
		}


		// 処理名
		if( typeof(DeleteColumn) != 'undefined' )
		{
			DeleteColumn.innerText 	= 'Delete';
		}

	}

	// 日本語
	else if ( g_lngCode == 1 )
	{

		// 追加ボタンイメージ書き出し
		if( typeof(MasterAddBt) != 'undefined' )
		{
			MasterAddBt.innerHTML = maddbtJ1;
		}


		// クエリーボタンテーブル書き出し
		if( typeof(fncTitleOutput) != 'undefined' )
		{
			fncTitleOutput( 1 );
		}


		// カラム名
		Column0.innerText	= '順序名称';
		Column1.innerText	= '順序グループ名称';

		if( typeof(Column2) != 'undefined' )
		{
			Column2.innerText	= '順序';
		}

		if( typeof(Column3) != 'undefined' )
		{
			Column3.innerText	= '担当者名';
		}

		if( typeof(Column4) != 'undefined' )
		{
			Column4.innerText	= '期限日';
		}


		// 処理名
		if( typeof(DeleteColumn) != 'undefined' )
		{
			DeleteColumn.innerText	= '削除';
		}

	}

	return false;

}


//-->
