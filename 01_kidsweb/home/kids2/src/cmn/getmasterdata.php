<?
	// ---------------------------------------------------------------
	// プログラム名: getMasterData
	//
	// 概要:   マスターデータを取得
	//
	// 引数:
	//         $strFormValue1 : フォームの値1
	//         $strFormValue2 : フォームの値2
	//			・・・フォーム値が存在するだけ全て取得
	//
	//         $lngProcessID  : 実行するSQLのID
	//
	// 戻り値:
	//         $strMasterData : マスターデータ(***,***\n)
	// ---------------------------------------------------------------

	// 設定読み込み
	include ( "conf.inc" );
	require (LIB_FILE);

	// GET情報の取得
	$lngProcessID  = $_GET["lngProcessID"];  // 実行するSQLのID
	$strFormValue  = $_GET["strFormValue"];   // フィールド


	//////////////////////////////////////////////////////////////
	// メインルーチン
	//////////////////////////////////////////////////////////////
	for ( $i = 0; $i < count ( $strFormValue ); $i++ )
	{
		$strFormValue[$i] = mb_convert_encoding( $strFormValue[$i], "EUC-JP", "SJIS,EUC" );
	}

	//{
	//	echo ",引数無し";
	//	exit;
	//}
	// 外部ファイルよりクエリ取得、生成
	if ( !$strQuery = file_get_contents ( LIB_ROOT . "sql/$lngProcessID.sql" ) )
	{
		echo ",ファイルオープンに失敗しました。";
		exit;
	}
	// コメント(//タイプ)の削除
	$strQuery = preg_replace ( "/\/\/.+?\n/", "", $strQuery );
	// 2つのスペース、改行、タブをスペース1つに変換
	$strQuery = preg_replace ( "/(\s{2}|\n|\t)/", " ", $strQuery );
	// コメント(/**/タイプ)の削除
	$strQuery = preg_replace ( "/\/\*.+?\*\//m", "", $strQuery );

	// 取得変数の置き換え
	for ( $i = 0; $i < count ( $strFormValue ); $i++ )
	{
		$strQuery = preg_replace ( "/_%strFormValue$i%_/", $strFormValue[$i], $strQuery );
	}

	// 置き換えられなかった変数の WHERE 句、修正
	//$strQuery = preg_replace ( "/(AND|WHERE) [\w\._\(\)]+? (=||LIKE) [\w\(\)]*('%??%??'| )\)?/", "", $strQuery );
	$strQuery = preg_replace ( "/(AND|WHERE) [\w\._]+? ([<>]?=||LIKE) ('%??%??'| )/", "", $strQuery );

	// \ の処理(DB 問い合わせのため \ を \\ にする)
	$strQuery = preg_replace ( "/\\\\/", "\\\\\\\\", $strQuery );

	$fp = fopen ( SQLLOG_ROOT . "$lngProcessID.txt", "w" );
	fwrite ( $fp, $strQuery . "\n" );
	
	// データ文字列取得(***,***\n)
	if ( $strMasterData = fncGetMasterData( $strQuery ) )
	{
		// マスターデータ出力
		echo $strMasterData;
	}

	fwrite ( $fp, $strQuery . "\n" . $strMasterData );
	fclose ( $fp );


// ---------------------------------------------------------------
// 関数名: fncGetMasterData
//
// 概要:   マスターデータ取得クエリを実行、データ取得しCSV形式で生成
//
// 引数:
//         $strQuery      : クエリ
//
// 戻り値:
//         $strMasterData : マスターデータ(***,***\n)
// ---------------------------------------------------------------
function fncGetMasterData( $strQuery )
{
	// フィールド名設定
	$strMasterHeader = "id";

	// DB接続
	$objDB = new clsDB();
	if ( !$objDB->open( "", "", "", "" ) ) {
	    echo ",DB接続に失敗しました。\n";
	    exit;
	}

	// マスタ取得クエリ実行
	if ( !$result = $objDB->execute( $strQuery ) )
	{
		echo "id\tname1\nマスターデータの結果取得に失敗しました。\n";
	    exit;
	}

	if ( $lngResultRows = pg_Num_Rows( $result ) )
	{

		$aryFieldNum = $objDB->getFieldsCount( $result );

		// カラム生成(,name1,name2・・・)
		for ( $i = 1; $i < $aryFieldNum; $i++ )
		{
			$strMasterHeader .= "\tname$i";
		}

		// 結果を成形
		for ( $i = 0; $i < $lngResultRows; $i++ )
		{
			$array = $objDB->fetchArray ( $result, $i );

			// 第１フィールドはIDとする
			$strMasterData .= "$array[0]";

			// 第２フィールド以降はNAMEとする
			for ( $j = 1; $j < $aryFieldNum; $j++ )
			{
				$array[$j] = preg_replace ( "/\s+?$/", "", $array[$j] );// 空白削除
				$strMasterData   .= "\t$array[$j]";
			}

			$strMasterData .= "\n";
		}
	}
	else
	{
		$strMasterData .= "";
	}
	return $strMasterHeader . "\n" . $strMasterData;

}
?>
