<?php

// ----------------------------------------------------------------------------
/**
*       商品管理  類似商品リスト
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
*         ・登録確認画面で、類似商品リストを表示
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
	
	$aryData["strSessionID"]    = $_GET["strSessionID"];
	//echo "session :".$aryData["strSessionID"];

	
	// 文字列チェック
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );
	
	
	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	
	
	//print_r($aryData);
	$objDB->close();
	
	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "/p/confirm_ifrm/parts.tmpl" );
	
	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;
	
	
	return true;
	
?>

