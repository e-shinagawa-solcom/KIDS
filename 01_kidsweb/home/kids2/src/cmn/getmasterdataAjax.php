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
	
	//PHP標準のJSON変換メソッドはエラーになるので外部のライブラリ(恐らくエンコードの問題)
	require_once 'JSON.php';

	// GET情報の取得
	$lngProcessID  = $_GET["lngProcessID"];  // 実行するSQLのID
	$strFormValue  = $_GET["strFormValue"];   // フィールド

	//JSONクラスインスタンス化
	$s = new Services_JSON();

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

	// データ取得
	$masterData = fncGetMasterData( $strQuery );
	mb_convert_variables('UTF-8' , 'EUC-JP' , $masterData );
	echo $s->encodeUnsafe($masterData);



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
	
	$result = pg_fetch_all($result);

	//既存の処理への修正量を考慮して、取得したデータのキーを既存の処理に合わせる
	//第一フィールドはID、それ以降はNAME
	for($i=0;$i<count($result);$i++){
		$key = array_keys($result[$i]);
		for($i2=0;$i2<count($key);$i2++){
			if($i2==0){
				$result[$i]["id"] = $result[$i][$key[$i2]];
			} else {
				$result[$i]["name".$i2] = $result[$i][$key[$i2]];
			}
		}
	}

	return $result;

}
?>
