<?php

// ----------------------------------------------------------------------------
/**
*       発注管理  登録
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
*         ・登録処理
*         ・エラーチェック
*         ・登録処理完了後、登録完了画面へ
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------

	// 読み込み
	include('conf.inc');
	require (LIB_FILE);
	require (SRC_ROOT."sc/cmn/lib_scr.php");
/*
	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	
	$aryData["strSessionID"] = $_GET["strSessionID"];
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
	$aryData["aryPurchaseOrderNo"] = $_GET["aryPurchaseOrderNo"];

	$objDB->open("", "", "", "");
	
	// 文字列チェック
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );



	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	
	$lngInputUserCode = $objAuth->UserCode;
	
	// 権限確認
	if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	// 500	発注管理
	if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	
	// 501 発注管理（発注登録）
	if ( !fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	
	// 508 発注管理（商品マスタダイレクト修正）
	if( !fncCheckAuthority( DEF_FUNCTION_PO8, $objAuth ) )
	{
		$aryData["popenview"] = 'hidden';
	}

	// 発注書データ取得
	$aryPurchaseOrderNo = explode(",", $aryData["aryPurchaseOrderNo"]);
	for($i = 0; $i < count($aryPurchaseOrderNo); $i++){
		$arr = explode("-", $aryPurchaseOrderNo[$i]);
		$aryKey[$i]["purchaseorderno"] = $arr[0];
		$aryKey[$i]["revisionno"] = $arr[1];
	}
	$aryPurcharseOrder = fncGetPurchaseOrder($aryKey, $objDB);
	if(!$aryPurcharseOrder){
		fncOutputError ( 9051, DEF_ERROR, "発注書の取得に失敗しました。", TRUE, "", $objDB );
		return FALSE;
	}
	
	$strHtml = fncCreatePurchaseOrderHtml($aryPurcharseOrder);
	$aryData["aryPurchaseOrder"] = $strHtml;


	
	$aryData["strBodyOnload"] = "";
	
	$objDB->close();

	// 成功時戻り先のアドレス指定
	$aryData["strAction"] = "/sc/regist2/index.php?strSessionID=";
*/

	echo fncGetReplacedHtmlWithBase("base_mold_noframes.html", "sc/regist2/condition.tmpl", $aryData ,$objAuth );

	// テンプレート読み込み
	//$objTemplate = new clsTemplate();
	
	// テンプレートに反映する文字列
	//$aryData["lngPONo"] = "$strOrderCode - $strReviseCode";

	//header("Content-type: text/plain; charset=EUC-JP");
	//$objTemplate->getTemplate( "sc/finish2/parts.tmpl" );
	

	


	// テンプレート生成
	//$objTemplate->replace( $aryData );
	//$objTemplate->complete();

	// HTML出力
	//echo $objTemplate->strTemplate;
			
	return true;
?>