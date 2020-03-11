<?php

// ----------------------------------------------------------------------------
/**
*       発注管理  メニュー画面
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       処理概要
*         ・メニュー画面を表示
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



	// 読み込み
	include('conf.inc');
	require (LIB_FILE);
	
	
	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	$objDB->open("", "", "", "");

	$aryData["strSessionID"] = $_POST["strSessionID"];

	
	// 文字列チェック
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );
	
	// セッション確認
	$objAuth = fncIsSession( $_POST["strSessionID"], $objAuth, $objDB );

	// 500	発注管理
	if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
	        fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 502 発注管理（発注検索）
	if ( fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )	
	{
		$aryData["Regist_visibility"] = 'style="visibility: visible"';
	} else {
		$aryData["Regist_visibility"] = 'style="visibility: hidden"';
	}
	
	// 510 発注管理（発注書検索）
	if ( fncCheckAuthority( DEF_FUNCTION_PO10, $objAuth ) )		
	{
		$aryData["Search_visibility"] = 'style="visibility: visible"';
	} else {
		$aryData["Search_visibility"] = 'style="visibility: hidden"';
	}
	// ヘルプ対応
	$aryData["lngFunctionCode"] = DEF_FUNCTION_PO0;

	echo fncGetReplacedHtmlWithBase("base_mold.html", "po/parts.tmpl", $aryData ,$objAuth );

	$objDB->close();
	return true;
?>