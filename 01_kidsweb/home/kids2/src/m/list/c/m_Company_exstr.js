<!--
/************************* [ 会社マスタ ] *************************/



//////////////////////////////////////////////////////////////////
////////// [修正][追加]時・オブジェクトのオンロード処理関数 //////////
function fncEditObjectOnload( lngLangCode )
{
	// オブジェクトの自動レイアウト
	fncInitLayoutObjectModule( objColumn , objInput  , 60 , 216 );


	// 「会社属性」専用レイアウト
	Column19.style.padding = '50 0 0 8';
	Column19.style.height = '109';


	// [追加]
	if( g_strMode == 'add' )
	{
		// オブジェクトのID変換
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2' , 'Input3' , 'Input4' ,
										'Input5' , 'Input6' , 'Input7' , 'Input8' , 'Input9' ,
										'Input10' , 'Input11' , 'Input12' , 'Input13' , 'Input14' ,
										'Input15' , 'Input16' , 'Input17' , 'Input18' , 'Input19' ) ,

								 Array( 'TxtDis04L' , 'TxtSlt20' , 'TxtSlt20' , 'CheckBox14' , 'Txt40L' ,
										'CheckBox14' , 'Txt40L' , 'Txt40L' , 'Txt08L' , 'Txt40L' ,
										'Txt40L' , 'Txt40L' , 'Txt40L' , 'Txt40L' , 'Txt40L' ,
										'Txt40L' , 'Txt40L' , 'Txt02L' , 'TxtSlt20' , 'TxtSlt20' ) );
	}
	// [修正]
	else if( g_strMode == 'fix' )
	{
		// オブジェクトのID変換
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2' , 'Input3' , 'Input4' ,
										'Input5' , 'Input6' , 'Input7' , 'Input8' , 'Input9' ,
										'Input10' , 'Input11' , 'Input12' , 'Input13' , 'Input14' ,
										'Input15' , 'Input16' , 'Input17' , 'Input18' , 'Input19' ) ,

								 Array( 'TxtDis04L' , 'TxtSlt20' , 'TxtSlt20' , 'CheckBox14' , 'Txt40L' ,
										'CheckBox14' , 'Txt40L' , 'Txt40L' , 'Txt08L' , 'Txt40L' ,
										'Txt40L' , 'Txt40L' , 'Txt40L' , 'Txt40L' , 'Txt40L' ,
										'Txt40L' , 'Txt40L' , 'Txt02L' , 'TxtSlt20' , 'TxtSlt20' ) );
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
var headerAJ = '<img src="' + h_companyJ + '" width="949" height="30" border="0" alt="会社マスタ">';
var headerAE = '<img src="' + h_companyE + '" width="949" height="30" border="0" alt="COMPANY MASTER">';








//////////////////////////////////////////////////////////////////
////////// 日本語・英語切替モジュール //////////
function ChgEtoJ( g_lngCode )
{

	// クエリーテーブルＴＯＰ座標
	if( typeof(QueryTable) != 'undefined' )
	{
		QueryTable.style.top = 800;
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
		Column0.innerText	= 'Company code';
		Column1.innerText	= 'Country code';
		Column2.innerText	= 'Organization code';
		Column3.innerText	= 'Organization front';
		Column4.innerText	= 'Company name';
		Column5.innerText	= 'Company permission';
		Column6.innerText	= 'Company display code';
		Column7.innerText	= 'Company display name';
		Column8.innerText	= 'Postal code';
		Column9.innerText	= 'Address 1';
		Column10.innerText	= 'Address 2';
		Column11.innerText	= 'Address 3';
		Column12.innerText	= 'Address 4';
		Column13.innerText	= 'Tel 1';
		Column14.innerText	= 'Tel 2';
		Column15.innerText	= 'Fax 1';
		Column16.innerText	= 'Fax 2';
		Column17.innerText	= 'Distinct code';
		Column18.innerText	= 'Paystyle code';
		Column19.innerText	= 'Company attribute';

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
		Column0.innerText	= '会社コード';
		Column1.innerText	= '国コード';
		Column2.innerText	= '組織コード';
		Column3.innerText	= '組織表記';
		Column4.innerText	= '会社名称';
		Column5.innerText	= '表示会社許可';
		Column6.innerText	= '表示会社コード';
		Column7.innerText	= '表示会社名称';
		Column8.innerText	= '郵便番号';
		Column9.innerText	= '住所1 / 都道府県';
		Column10.innerText	= '住所2 / 市、区、郡';
		Column11.innerText	= '住所3 / 町、番地';
		Column12.innerText	= '住所4 / ビル等、建物名';
		Column13.innerText	= '電話番号1';
		Column14.innerText	= '電話番号2';
		Column15.innerText	= 'ファックス番号1';
		Column16.innerText	= 'ファックス番号2';
		Column17.innerText	= '識別コード';
		Column18.innerText	= '締め日コード';
		Column19.innerText	= '会社属性';

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