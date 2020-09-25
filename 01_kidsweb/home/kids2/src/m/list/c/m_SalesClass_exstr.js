<!--
/************************* [ 売上区分マスタ ] *************************/



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
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1', 'Input2', 'Input3' ) ,
								 Array( 'TxtDis03L' , 'Txt40L', '', '' ) );
	}
	// [修正]
	else if( g_strMode == 'fix' )
	{
		// オブジェクトのID変換
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1', 'Input2', 'Input3' ) ,
								 Array( 'TxtDis03L' , 'Txt40L', '', '' ) );
	}


	// [lngLanguageCode]値の初期化
	var g_lngCode = '';

	// [lngLanguageCode]値の取得
	g_lngCode = lngLangCode;


	// オブジェクトの日本語・英語切替
	ChgEtoJ( g_lngCode );


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
////////// 追加ボタンの表示 //////////
if( typeof(window.top.MasterAddBt) != 'undefined' )
{
	window.top.MasterAddBt.style.visibility = 'visible';
}








//////////////////////////////////////////////////////////////////
////////// ヘッダーイメージの生成 //////////
var headerAJ = '<img src="' + h_salesclassJ + '" width="949" height="30" border="0" alt="売上区分マスタ">';
var headerAE = '<img src="' + h_salesclassE + '" width="949" height="30" border="0" alt="SALES CLASS MASTER">';











//////////////////////////////////////////////////////////////////
////////// 日本語・英語切替モジュール //////////
function ChgEtoJ( g_lngCode )
{

	// クエリーテーブルＴＯＰ座標
	if( typeof(QueryTable) != 'undefined' )
	{
		QueryTable.style.top = 400;
	}


	// 英語
	if ( g_lngCode == 0 )
	{


		// ヘッダーイメージ書き出し
		if( typeof(window.top.SegAHeader) != 'undefined' )
		{
			window.top.SegAHeader.innerHTML = headerAE;
		}


		// クエリーボタンテーブル書き出し
		if( typeof(fncTitleOutput) != 'undefined' )
		{
			fncTitleOutput( 0 );
		}


		// 追加ボタンイメージ書き出し
		if( typeof(window.top.MasterAddBt) != 'undefined' )
		{
			window.top.MasterAddBt.innerHTML = maddbtE1;
		}



		// カラム名
		if( typeof(Column0) != 'undefined' )
		{
			Column0.innerText	= 'Sales class code';
		}

		if( typeof(Column1) != 'undefined' )
		{
			Column1.innerText	= 'Sales class name';
		}

		// 処理名
		if( typeof(FixColumn) != 'undefined' )
		{
			FixColumn.innerText		= 'Fix';
		}

		if( typeof(DeleteColumn) != 'undefined' )
		{
			DeleteColumn.innerText 	= 'Delete';
		}

	}

	// 日本語
	else if ( g_lngCode == 1 )
	{


		// ヘッダーイメージ書き出し
		if( typeof(window.top.SegAHeader) != 'undefined' )
		{
			window.top.SegAHeader.innerHTML = headerAJ;
		}


		// クエリーボタンテーブル書き出し
		if( typeof(fncTitleOutput) != 'undefined' )
		{
			fncTitleOutput( 1 );
		}


		// 追加ボタンイメージ書き出し
		if( typeof(window.top.MasterAddBt) != 'undefined' )
		{
			window.top.MasterAddBt.innerHTML = maddbtJ1;
		}



		// カラム名
		if( typeof(Column0) != 'undefined' )
		{
			Column0.innerText	= '売上区分コード';
		}

		if( typeof(Column1) != 'undefined' )
		{
			Column1.innerText	= '売上区分名称';
		}

		if( typeof(Column2) != 'undefined' )
		{
			Column2.innerText	= '納品書備考出力フラグ';
		}

		if( typeof(Column3) != 'undefined' )
		{
			Column3.innerText	= '明細統一フラグ';
		}

		if( typeof(Column4) != 'undefined' )
		{
			Column4.innerText	= '表示フラグ';
		}

		// 処理名
		if( typeof(FixColumn) != 'undefined' )
		{
			FixColumn.innerText		= '修正';
		}

		if( typeof(DeleteColumn) != 'undefined' )
		{
			DeleteColumn.innerText	= '削除';
		}

	}

	return false;

}


//-->