<?php

// ----------------------------------------------------------------------------
/**
*       LC管理  LC情報画面
*/
// ----------------------------------------------------------------------------

	//-------------------------------------------------------------------------
	// ■ ライブラリファイル読込
	//-------------------------------------------------------------------------
	// 読み込み
	include('conf.inc');
	//共通ファイル読み込み
	require_once '../lcModel/lcModelCommon.php';
	require_once '../lcModel/db_common.php';
	require_once '../lcModel/kidscore_common.php';
	require_once '../lcModel/lcinfo.php';
	require (LIB_FILE);
	
	//PHP標準のJSON変換メソッドはエラーになるので外部のライブラリ(恐らくエンコードの問題)
	require_once '../lcModel/JSON.php';

	//-------------------------------------------------------------------------
	// ■ オブジェクト生成
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	//LC用DB接続インスタンス生成
    $db			= new lcConnect();

	//-------------------------------------------------------------------------
	// ■ DBオープン
	//-------------------------------------------------------------------------
	$objDB->open("", "", "", "");

	//-------------------------------------------------------------------------
	// ■ パラメータ取得
	//-------------------------------------------------------------------------
	$aryData = $_POST;

	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];   // セッションID
	$aryData["reSearchFlg"]    = $_REQUEST["reSearchFlg"];   // 再検索フラグ

	setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	//ユーザーID取得(半角スペースがあるため)
	$usrId = trim($objAuth->UserID);

	//経理サブシステムDB接続
	$lcModel		= new lcModel();

	//ログイン状況の最大管理番号の取得
	$maxLgno = $lcModel->getMaxLoginStateNum();

	// ログアウト時刻の取得
	$acloginstate = $lcModel->getAcLoginstateBylgno($maxLgno);
	$lgoutymd = $acloginstate->lgoutymd;
	
	$objDB->close();

	//結果配列
	$result = array();
	$result["strSessionID"] = $aryData["strSessionID"];
	$result["lgoutymd"] = $lgoutymd;

	//JSONクラスインスタンス化
	$s = new Services_JSON();
	//結果出力
	mb_convert_variables('UTF-8' , 'EUC-JP' , $result );
	echo $s->encodeUnsafe($result);
?>