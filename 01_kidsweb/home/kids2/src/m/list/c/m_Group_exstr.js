/************************* [ グループマスタ ] *************************/




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
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2' , 'Input3' , 'Input4' , 'Input5' , 'Input6' ) ,

								 Array( 'TxtDis05L' , 'TxtSlt38' , 'Txt25L' , 'CheckBox14' , 'Txt02L' , 'Txt25L' , 'Txt08L' ) );
	}
	// [修正]
	else if( g_strMode == 'fix' )
	{
		// オブジェクトのID変換
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2' , 'Input3' , 'Input4' , 'Input5' , 'Input6' ) ,

								 Array( 'TxtDis05L' , 'TxtSlt38' , 'Txt25L' , 'CheckBox14' , 'Txt02L' , 'Txt25L' , 'Txt08L' ) );
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
////////// ヘッダーイメージの生成 //////////
var headerAJ = '<img src="' + h_groupJ + '" width="949" height="30" border="0" alt="グループマスタ">';
var headerAE = '<img src="' + h_groupE + '" width="949" height="30" border="0" alt="GROUP MASTER">';








//////////////////////////////////////////////////////////////////
////////// 日本語・英語切替モジュール //////////
function ChgEtoJ( g_lngCode )
{

	// クエリーテーブルＴＯＰ座標
	if( typeof(QueryTable) != 'undefined' )
	{
		QueryTable.style.top = 300;
	}


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
		Column0.innerText	= 'Group code';
		Column1.innerText	= 'Company code';
		Column2.innerText	= 'Group name';
		Column3.innerText	= 'Group permission';
		Column4.innerText	= 'Group display code';
		Column5.innerText	= 'Group display name';
		Column6.innerText	= 'Group display color';


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
		Column0.innerText	= 'グループコード';
		Column1.innerText	= '会社コード';
		Column2.innerText	= 'グループ名称';
		Column3.innerText	= '表示グループ許可';
		Column4.innerText	= '表示グループコード';
		Column5.innerText	= '表示グループ名称';
		Column6.innerText	= '表示グループカラー';


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