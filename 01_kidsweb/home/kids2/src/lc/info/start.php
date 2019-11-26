<?php

// ----------------------------------------------------------------------------
/**
*       LC管理  LC情報開始
*       initLcInfoを実行させるだけの空の画面です。
*/
// ----------------------------------------------------------------------------

	// 読み込み
	include('conf.inc');
	//共通ファイル読み込み
	require SRC_ROOT . "lc/lcModel/lcModelCommon.php";
	require (LIB_FILE);


	$objDB		= new clsDB();
	$objAuth	= new clsAuth();

	$objDB->open("", "", "", "");


	//-------------------------------------------------------------------------
	// ■ パラメータ取得
	//-------------------------------------------------------------------------
	$aryData = $_REQUEST;
	// 文字列チェック
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	// クッキーにセッションIDをセット
	setcookie("strSessionID",$aryData["strSessionID"]);

	//ユーザーID取得(半角スペースがあるため)
	$user_id = trim($objAuth->UserID);
	
	$objDB->close();
	
	//HTMLへの引き渡しデータ
	$aryData["session_id"] = $aryData["strSessionID"];

	echo fncGetReplacedHtmlWithBase("lc/base_lc.html", "lc/info/start.tmpl", $aryData ,$objAuth );

	return true;
?>