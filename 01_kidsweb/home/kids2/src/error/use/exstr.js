<!--


function fncChgEtoJ( lngCode )
{

	if ( lngCode == 0 )
	{

		// 処理用テーブル書き出し
		fncProcessingOutputModule( 'use' , 0 );

		strComments.innerHTML = 'Selected data is used in the following item.';
		Column0.innerHTML     = 'Control name';
		Column1.innerHTML     = 'No.';

	}

	else if ( lngCode == 1 )
	{

		// 処理用テーブル書き出し
		fncProcessingOutputModule( 'use' , 1 );

		strComments.innerHTML = '選択されたデータは下記の項目にて使用されています。';
		Column0.innerHTML     = '管理名称';
		Column1.innerHTML     = 'ＮＯ.';

	}

	return false;

}


//-->