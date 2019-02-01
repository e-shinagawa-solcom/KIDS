<?php

// ----------------------------------------------------------------------------
/**
*       受注管理  登録確認画面
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
	require(SRC_ROOT."so/cmn/lib_so.php");
	require(SRC_ROOT."so/cmn/lib_sos1.php");
	require(SRC_ROOT."so/cmn/column.php");
	
	$objDB          = new clsDB();
	$objAuth        = new clsAuth();
	
	$objDB->open("", "", "", "");
	
	$aryData["strSessionID"]    = $_GET["strSessionID"];
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];


	// 文字列チェック
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $_POST["strSessionID"], $objAuth, $objDB );
	$UserDisplayName = $objAuth->UserDisplayName;
	$UserDisplayCode = $objAuth->UserID;



	// 明細行を除く
	for( $i = 0; $i < count( $_POST ); $i++ )
	{
		list( $strKeys, $strValues ) = each ( $_POST );
		if($strKeys != "aryPoDitail")
		{
			$aryData[$strKeys] = $strValues;
		}
	}



	// 明細行処理 ===========================================================================================
	// 明細行のhidden生成
	if( is_array( $_POST["aryPoDitail"] ) )
	{
		$aryData["strDetailHidden"] = fncDetailHidden_so( $_POST["aryPoDitail"] ,"insert" , $objDB );
	}
	
	
	// 言語の設定
	if ( isset($aryData["lngLanguageCode"]) and  $aryData["lngLanguageCode"] == 0 )
	{
		$aryTytle = $aryTableTytleEng;
	}
	else
	{
		$aryTytle = $aryTableTytle2;
	}

	// カラム名の設定
	$aryHeadColumnNames = fncSetReceiveTabelName ( $aryTableViewHead, $aryTytle );
	$aryDetailColumnNames = fncSetReceiveTabelName ( $aryTableViewDetail2, $aryTytle );

	for( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
	{
		$_POST["aryPoDitail"][$i]["lngrecordno"] = $i + 1;

		$_POST["aryPoDitail"][$i]["strproductcode_DIS"] = fncGetMasterValue( "m_product", "strproductcode", "strproductname", $_POST["aryPoDitail"][$i]["strProductCode"].":str", '', $objDB );

		$_POST["aryPoDitail"][$i]["lngsalesclasscode_DIS"] = fncGetMasterValue( "m_salesclass", "lngsalesclasscode", "strsalesclassname", $_POST["aryPoDitail"][$i]["lngSalesClassCode"], '', $objDB );

		$_POST["aryPoDitail"][$i]["lngproductunitcode_DIS"] = fncGetMasterValue( "m_productunit", "lngproductunitcode", "strproductunitname", $_POST["aryPoDitail"][$i]["lngProductUnitCode"], '', $objDB );

		// 明細行備考の特殊文字変換
		$_POST["aryPoDitail"][$i]["strDetailNote"] = fncHTMLSpecialChars( $_POST["aryPoDitail"][$i]["strDetailNote"] );

		$_POST["aryPoDitail"][$i]["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];

		$_POST["aryPoDitail"][$i]["curproductprice_DIS"] = ($_POST["aryPoDitail"][$i]["curProductPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curProductPrice"] ,4 ) : "";

		$_POST["aryPoDitail"][$i]["lnggoodsquantity_DIS"] = ($_POST["aryPoDitail"][$i]["lngGoodsQuantity"] != "") ? number_format( $_POST["aryPoDitail"][$i]["lngGoodsQuantity"] ) : "";

		$_POST["aryPoDitail"][$i]["curtotalprice_DIS"] = ($_POST["aryPoDitail"][$i]["curTotalPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curTotalPrice"] ,2 ) : "";


		// コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）
		$_POST["aryPoDitail"][$i]["strproductcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strProductCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strProductCode"]."]" : "";
		$_POST["aryPoDitail"][$i]["lngsalesclasscode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["lngSalesClassCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["lngSalesClassCode"]."]" : "";



		// テンプレート読み込み
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "so/result/parts_detail2.tmpl" );

		// テンプレート生成
		$objTemplate->replace( $aryDetailColumnNames );
		$objTemplate->replace( $_POST["aryPoDitail"][$i] );
		$objTemplate->complete();

		// HTML出力
		$aryDetailTable[] = $objTemplate->strTemplate;
	}



	$aryData["strDetailTable"] = implode ("\n", $aryDetailTable );
	
	$aryData["strMode"] = "regist";
	$aryData["strProcMode"] = "regist";



	//-------------------------------------------------------------------------
	// *v2* 部門・担当者の取得
	//-------------------------------------------------------------------------
	$strProductCode       = $_POST["aryPoDitail"][0]["strProductCode"];

	$lngInChargeGroupCode = fncGetMasterValue( "m_product", "strproductcode", "lnginchargegroupcode", $strProductCode . ":str", '', $objDB );
	$strInChargeGroupCode = fncGetMasterValue( "m_group", "lnggroupcode", "strgroupdisplaycode", $lngInChargeGroupCode . '', '', $objDB );
	$strInChargeGroupName = fncGetMasterValue( "m_group", "strgroupdisplaycode", "strgroupdisplayname",  $strInChargeGroupCode . ":str",'',$objDB );

	$lngInChargeUserCode  = fncGetMasterValue( "m_product", "strproductcode", "lnginchargeusercode", $strProductCode . ":str", '', $objDB );
	$strInChargeUserCode  = fncGetMasterValue( "m_user", "lngusercode", "struserdisplaycode", $lngInChargeUserCode . '', '', $objDB );
	$strInChargeUserName  = fncGetMasterValue( "m_user", "struserdisplaycode" ,"struserdisplayname" , $strInChargeUserCode . ":str",'',$objDB );

	// 部門コード・名称
	$aryData["strInChargeGroup"] = "[" . $strInChargeGroupCode . "] " . $strInChargeGroupName;
	// 担当者コード・名称
	$aryData["strInChargeUser"]  = "[" . $strInChargeUserCode . "] " . $strInChargeUserName;
	//-------------------------------------------------------------------------



	// 登録日
	$aryData["dtminsertdate"] = date( 'Y/m/d', time() );
	// 入力者
	$aryData["lngInputUserCode"] = $UserDisplayCode;
	$aryData["strInputUserCode"] = $UserDisplayName;
	// 通貨
	$aryData["strMonetaryUnitName"] = ( $aryData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryData["lngMonetaryUnitCode"];


	$aryData["strMonetaryUnitName"] = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $aryData["strMonetaryUnitName"] . ":str", '', $objDB );

	// レートコード
	$strMonetaryrateName = fncGetMasterValue( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $aryData["lngMonetaryRateCode"], '', $objDB);
	$aryData["strMonetaryrateName"] = ( $strMonetaryrateName == "−" ) ? "" : $strMonetaryrateName;

	// 状態
	$aryData["strActionURL"] = "/so/regist/index2.php?strSessionID=".$aryData["strSessionID"];


	// 備考
	$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );



	$aryData["curConversionRate_DIS"] = number_format( $aryData["curConversionRate"],6 );	// 換算レート
	$aryData["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
	$aryData["curAllTotalPrice_DIS"] = number_format( $aryData["curAllTotalPrice"],2 );		// 合計金額




	// コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）
/*
	$aryData["lngInputUserCode_DISCODE"] = ( $aryData["lngInputUserCode"] != "" ) ? "[".$aryData["lngInputUserCode"]."]" : "";
	$aryData["lngCustomerCode_DISCODE"] = ( $aryData["lngCustomerCode"] != "" ) ? "[".$aryData["lngCustomerCode"]."]" : "";
	$aryData["lngInChargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
	$aryData["lngInChargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";
*/

	$aryData["lngInputUserCode_DISCODE"] = ( $aryData["lngInputUserCode"] != "" ) ? "[".$aryData["lngInputUserCode"]."]" : "";
	$aryData["lngCustomerCode_DISCODE"] = ( $aryData["lngCustomerCode"] != "" ) ? "[".$aryData["lngCustomerCode"]."]" : "";
	//$aryData["lngInChargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
	//$aryData["lngInChargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";
	$aryData["lngLocationCode_DISCODE"] = ( $aryData["lngLocationCode"] != "" ) ? "[".$aryData["lngLocationCode"]."]" : "";




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



/*
	// ファイルアウト
	$fp = fopen( "/home/kids2/kids/log/so_debug.txt", "a" );
	foreach( $aryData as $key => $value )
	{
		fwrite( $fp, "[" . $key . "] = " . $value . "\n" );
	}
	fclose( $fp );
*/


	//var_dump( $aryData["lngWorkflowOrderCode"] );

	$objDB->close();


	// 顧客受注番号の取得
	$aryData["strCustomerReceiveCode"] = trim( $aryData["strCustomerReceiveCode"] );
	$aryData["strCustomerReceiveCode"] = ( $aryData["strCustomerReceiveCode"] == "null" ) ? "" : $aryData["strCustomerReceiveCode"];

	// 仮受注の場合、ワークフローメッセージ欄を非表示
	if( $aryData["strCustomerReceiveCode"] == "null" || $aryData["strCustomerReceiveCode"] == "" )
	{
		$aryData["strWorkflowMessage_visibility"] = "none;";
	}





	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "so/confirm/parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryHeadColumnNames );
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;


	return true;

?>
