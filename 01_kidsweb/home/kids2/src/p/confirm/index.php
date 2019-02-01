<?php

// ----------------------------------------------------------------------------
/**
*       商品管理  登録確認画面
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
*         ・登録確認画面を表示
*         ・エラーチェック
*         ・登録ボタン押下後、登録処理へ
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



	// 読み込み
	include('conf.inc');
	require (LIB_FILE);
	require(SRC_ROOT."p/cmn/lib_p3.php");
	require (LIB_DEBUGFILE);

	
	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	
	$objDB->open("", "", "", "");
	
	
	$aryData["strSessionID"]    = $_GET["strSessionID"];
	
	$aryData = $_POST;




	// 文字列チェック
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );
	
	
	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	
	$lngInChargeGroupCode = fncGetMasterValue("m_group", "strgroupdisplaycode", "lnggroupcode", $aryData["lngInChargeGroupCode"] . ":str",'bytGroupDisplayFlag=true',$objDB);

	// 製品名（英語）の類似検索
//	$lngInChargeGroupCode = fncGetMasterValue(m_group, strgroupdisplaycode, lnggroupcode,  $aryData['lngInChargeGroupCode'] . ":str",'',$objDB);
	$strOptionValue = fncNearName ( $aryData["strProductEnglishName"], $lngInChargeGroupCode , "", $objDB );
	$aryData["strOptionValue"] = $strOptionValue;

	// カテゴリー名称
	// /kids21/tmp/p/confirm/parts.tmpl 

	$aryData["lngCategoryCode_DIS"] = fncGetMasterValue( "m_Category", "lngCategoryCode", "strCategoryName", $aryData["lngCategoryCode"], '', $objDB);

	// 荷姿単価
	$aryData["lngPackingUnitCode_DIS"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngPackingUnitCode"], '', $objDB);
	
	
	// 製品単位
	$aryData["lngProductUnitCode_DIS"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngProductUnitCode"], '', $objDB);
	
	// 商品形態
	$aryData["lngProductFormCode_DIS"] = fncGetMasterValue( "m_productform", "lngproductformcode", "strproductformname", $aryData["lngProductFormCode"], '', $objDB);
	
	
	// 対象年齢
	$aryData["lngTargetAgeCode_DIS"] = fncGetMasterValue( "m_targetage", "lngTargetAgeCode", "strTargetAgeName", $aryData["lngTargetAgeCode"], '', $objDB);
	
	// 証紙
	$aryData["lngCertificateClassCode_DIS"] = fncGetMasterValue( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", $aryData["lngCertificateClassCode"], '', $objDB);
	
	// 版権元
	$aryData["lngCopyrightCode_DIS"] = fncGetMasterValue( "m_copyright", "lngcopyrightcode", "strcopyrightname", $aryData["lngCopyrightCode"], '', $objDB);
	
	// 生産予定数
	$aryData["lngProductionUnitCode_DIS"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngProductionUnitCode"], '', $objDB);
	
	// 初回納品数
	$aryData["lngFirstDeliveryUnitCode_DIS"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngFirstDeliveryUnitCode"], '', $objDB);
	
	// 企画進行状況
	$aryData["lngGoodsPlanProgressCode_DIS"] = fncGetMasterValue( "m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname" ,$aryData["lngGoodsPlanProgressCode"], '',  $objDB );

	// アッセンブル
	$aryData["strAssemblyFactoryName"]		= addslashes( $aryData["strAssemblyFactoryName"] );


	// --------------------------
	// クワガタ社内サーバーでは、POSTされたデータのダブルクォートに￥マークが付いてしまうため、これを削除する
	// confirm/index.php と regist/renew.php にて対応　2006/10/08 K.Saito
	$aryData["strSpecificationDetails"] = StripSlashes($aryData["strSpecificationDetails"]);
	// --------------------------

	// http:// 又は https:// のホストが含まれている場合、削除する
	$aryData["strSpecificationDetails"] = preg_replace("/(http:\/\/?[^\/]+)|(https:\/\/?[^\/]+)/i", "" , $aryData["strSpecificationDetails"]);


	// 仕様詳細の特殊文字処理
	//$aryData["strSpecificationDetails"] = fncHTMLSpecialChars( $aryData["strSpecificationDetails"] );
	// 仕様詳細表示用
	$aryData["strSpecificationDetails_DIS"] = nl2br( $aryData["strSpecificationDetails"] );

	// 仕様詳細HIDDEN用（HIDDENに埋め込むために余分なタグなどを取り除く）
	if( strcmp( $aryData["strSpecificationDetails"], "") != 0 )
	{
		$aryData["strSpecificationDetails"] = stripslashes($aryData["strSpecificationDetails"]);
		$aryData["strSpecificationDetails"] = htmlspecialchars($aryData["strSpecificationDetails"], ENT_COMPAT | ENT_HTML401, "ISO-8859-1");
	}

//fncDebug("pdetail2.txt", $aryData["strSpecificationDetails_DIS"], __FILE__, __LINE__ );


/**
	仕様詳細画像ファイルHIDDEN生成
*/
if( $aryData["uploadimages"] )
{
	for( $i = 0; $i < count($aryData["uploadimages"]); $i ++ )
	{
		$aryUploadImagesHidden[]	= "<input type=\"hidden\" name=\"uploadimages[]\" value=\"" .$aryData["uploadimages"][$i]. "\" />\n";
	}

	// 再取得用に設定
	$aryData["re_uploadimages"]	= implode( "\n", $aryUploadImagesHidden );
	$aryData["re_editordir"]	= "<input type=\"hidden\" name=\"strTempImageDir\" value=\"" .$aryData["strTempImageDir"]. "\" />\n";
}
else
{
	$aryData["re_uploadimages"]	= "";
	$aryData["re_editordir"]	= "";
}



	$aryData["strMonetaryrate"] = DEF_EN_MARK; //通貨マーク

	// 2004/03/19 watanabe update コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）
	$aryData["strCustomerUserCode_DISCODE"] = ( $aryData["strCustomerUserCode"] != "" ) ? "[".$aryData["strCustomerUserCode"]."]" : "";
	$aryData["lngInChargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
	$aryData["lngInChargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";
	$aryData["lngCompanyCode_DISCODE"] = ( $aryData["lngCompanyCode"] != "" ) ? "[".$aryData["lngCompanyCode"]."]" : "";
	$aryData["lngFactoryCode_DISCODE"] = ( $aryData["lngFactoryCode"] != "" ) ? "[".$aryData["lngFactoryCode"]."]" : "";
	$aryData["lngAssemblyFactoryCode_DISCODE"] = ( $aryData["lngAssemblyFactoryCode"] != "" ) ? "[".$aryData["lngAssemblyFactoryCode"]."]" : "";
	$aryData["lngDeliveryPlaceCode_DISCODE"] = ( $aryData["lngDeliveryPlaceCode"] != "" ) ? "[".$aryData["lngDeliveryPlaceCode"]."]" : "";
	// watanabe update end



	// *v2* ワークフロー順序
	if( $aryData["lngWorkflowOrderCode"] != "" and $aryData["lngWorkflowOrderCode"] != 0 )
	{
		$aryData["strWorkflowOrderName"] = fncGetMasterValue( "m_workfloworder", "lngworkflowordercode", "strworkflowordername", $aryData["lngWorkflowOrderCode"] . ":str", 'lngWorkflowOrderNo = 1', $objDB );

		$aryData["strWorkflowMessage_visibility"] = "block;";

	}
	else
	{
		$aryData["strWorkflowOrderName"] = "承認なし";

		$aryData["strWorkflowMessage_visibility"] = "none;";
	}


	$aryData["strActionURL"] = "/p/regist/index3.php?strSessionID=".$aryData["strSessionID"];

	//print_r($aryData);
	
	$objDB->close();
	
	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "p/confirm/parts.tmpl" );
	
	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();


// debug file出力
//fncDebug("pdetail.txt", $objTemplate->strTemplate, __FILE__, __LINE__ );


	// HTML出力
	echo $objTemplate->strTemplate;


	return true;
	
?>

