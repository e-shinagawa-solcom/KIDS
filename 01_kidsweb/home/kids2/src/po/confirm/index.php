<?php

// ----------------------------------------------------------------------------
/**
*       発注管理  登録確認画面
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
	require(SRC_ROOT."po/cmn/lib_po.php");
	//2007.07.23 matsuki update start
	require(SRC_ROOT."po/cmn/lib_pop.php");
	//2007.07.23 matsuki update start
	require(SRC_ROOT."po/cmn/lib_pos1.php");
	require(SRC_ROOT."po/cmn/column.php");
	
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
		$aryData["strDetailHidden"] = fncDetailHidden( $_POST["aryPoDitail"] ,"insert" , $objDB );
	}
	

	// 言語の設定
	if ( isset($aryData["lngLanguageCode"]) and  $aryData["lngLanguageCode"] == 0 )
	{
		$aryTytle = $aryTableTytleEng;
	}
	else
	{
		$aryTytle = $aryTableTytle;
	}
	
	// カラム名の設定
	$aryHeadColumnNames = fncSetPurchaseTabelName ( $aryTableViewHead, $aryTytle );
	$aryDetailColumnNames = fncSetPurchaseTabelName( $aryTableViewDetail, $aryTytle );

	for ( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
	{
	
		$_POST["aryPoDitail"][$i]["lngrecordno"] = $i + 1;
		
		// 仕入科目
		$_POST["aryPoDitail"][$i]["strStockSubjectName"] = fncGetMasterValue( "m_stocksubject", "lngstocksubjectcode", "strstocksubjectname",  $_POST["aryPoDitail"][$i]["strStockSubjectCode"], '', $objDB );
		// 仕入部品 
		$_POST["aryPoDitail"][$i]["strStockItemName"] = fncGetMasterValue( "m_stockitem", "lngstockitemcode", "strstockitemname", $_POST["aryPoDitail"][$i]["strStockItemCode"], "lngstocksubjectcode = ".$_POST["aryPoDitail"][$i]["strStockSubjectCode"], $objDB );
		
		// 顧客品番
		$_POST["aryPoDitail"][$i]["strGoodsName"] = fncGetMasterValue( "m_product", "strproductcode", "strGoodsCode", $_POST["aryPoDitail"][$i]["strProductCode"].":str", "bytinvalidflag = false", $objDB );
		
		// 運搬方法
		$_POST["aryPoDitail"][$i]["strCarrierName"] = fncGetMasterValue( "m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", $_POST["aryPoDitail"][$i]["lngCarrierCode"],'', $objDB );
		// 単位
		$_POST["aryPoDitail"][$i]["strProductUnitName"] = fncGetMasterValue("m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["aryPoDitail"][$i]["lngProductUnitCode"], '', $objDB );

		// 明細行備考の特殊文字変換
		$_POST["aryPoDitail"][$i]["strDetailNote"] = fncHTMLSpecialChars( $_POST["aryPoDitail"][$i]["strDetailNote"] );
		
		//2004/03/17 watanabe update start
		$strProductName = "";
		if( $strProductName = fncGetMasterValue( "m_product", "strproductcode", "strproductname",  $_POST["aryPoDitail"][$i]["strProductCode"] . ":str",'',$objDB ) )
		{
			$_POST["aryPoDitail"][$i]["strproductname"] = $strProductName;
		}
		// watanabe end
		
		// 2004/03/11 number_format watanabe
		$_POST["aryPoDitail"][$i]["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
		$_POST["aryPoDitail"][$i]["curproductprice_DIS"] = ($_POST["aryPoDitail"][$i]["curProductPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curProductPrice"] ,4 ) : "";
		$_POST["aryPoDitail"][$i]["lnggoodsquantity_DIS"] = ($_POST["aryPoDitail"][$i]["lngGoodsQuantity"] != "") ? number_format( $_POST["aryPoDitail"][$i]["lngGoodsQuantity"] ) : "";
		$_POST["aryPoDitail"][$i]["curtotalprice_DIS"] = ($_POST["aryPoDitail"][$i]["curTotalPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curTotalPrice"] ,2 ) : "";
		// watanabe update end
		
		// 2004/03/19 watanabe update コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）
		$_POST["aryPoDitail"][$i]["strproductcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strProductCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strProductCode"]."]" : "";
		$_POST["aryPoDitail"][$i]["strstockitemcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strStockItemCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strStockItemCode"]."]" : "";
		$_POST["aryPoDitail"][$i]["strstocksubjectcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strStockSubjectCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strStockSubjectCode"]."]" : "";
		
		
		
		// テンプレート読み込み
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "po/result/parts_detail2.tmpl" );
		
		// テンプレート生成
		$objTemplate->replace( $aryDetailColumnNames );
		$objTemplate->replace( $_POST["aryPoDitail"][$i] );
		$objTemplate->complete();
		
		// HTML出力
		$aryDetailTable[] = $objTemplate->strTemplate;
	}
	// exit();
	
	
	$aryData["strDetailTable"] = implode ("\n", $aryDetailTable );
	
	
	$aryData["strMode"] = "regist";
	$aryData["strProcMode"] = $_POST["strProcMode"];



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

/*
	// 部門
	$aryData["strInChargeGroupName"] = fncGetMasterValue( "m_group", "strgroupdisplaycode", "strgroupdisplayname",  $aryData['lngInChargeGroupCode'] . ":str",'',$objDB);
	// 担当者
	$aryData["strInChargeUserName"] = fncGetMasterValue( "m_user", "struserdisplaycode" ,"struserdisplayname" , $aryData["lngInChargeUserCode"] . ":str",'',$objDB);
*/



	// 登録日
	$aryData["dtminsertdate"] = date( 'Y/m/d', time());
	// 入力者 
	$aryData["lngInputUserCode"] = $UserDisplayCode;
	$aryData["strInputUserName"] = $UserDisplayName;
	// 通貨
	$aryData["strMonetaryUnitName"] = ($aryData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryData["lngMonetaryUnitCode"];
	$aryData["strMonetaryUnitName"] = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $aryData["strMonetaryUnitName"] . ":str", '', $objDB );
	// 支払条件
	$strPayConditionName = fncGetMasterValue( "m_paycondition", "lngpayconditioncode", "strpayconditionname", $aryData["lngPayConditionCode"], '', $objDB);
	$aryData["strPayConditionName"] = ( $strPayConditionName == "−" ) ? "" : $strPayConditionName;
	
	// 納品場所
	$aryData["strLocationName"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "strcompanydisplayname", $aryData["lngLocationCode"].":str", '', $objDB);

	// レートコード
	$strMonetaryrateName = fncGetMasterValue( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $aryData["lngMonetaryRateCode"], '', $objDB);
	$aryData["strMonetaryrateName"] = ( $strMonetaryrateName == "−" ) ? "" : $strMonetaryrateName;

	// 状態
	$aryData["strAction"] = "/po/regist/index2.php?strSessionID=".$aryData["strSessionID"];

	//ヘッダ備考の特殊文字変換
	$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );
	
	// number_format 2004/03/11 watanabe
	$aryData["curConversionRate_DIS"] = number_format( $aryData["curConversionRate"],6 );	// 換算レート
	$aryData["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
	$aryData["curAllTotalPrice_DIS"] = number_format( $aryData["curAllTotalPrice"],2 );		// 合計金額
	// watanabe update end
	
	// 2004/03/19 watanabe update コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）
	$aryData["lngInputUserCode_DISCODE"] = ( $aryData["lngInputUserCode"] != "" ) ? "[".$aryData["lngInputUserCode"]."]" : "";
	$aryData["lngCustomerCode_DISCODE"] = ( $aryData["lngCustomerCode"] != "" ) ? "[".$aryData["lngCustomerCode"]."]" : "";
	//$aryData["lngInChargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
	//$aryData["lngInChargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";
	$aryData["lngLocationCode_DISCODE"] = ( $aryData["lngLocationCode"] != "" ) ? "[".$aryData["lngLocationCode"]."]" : "";
	// watanabe update end
	

	// ワークフロー順序
	if ( $aryData["lngWorkflowOrderCode"] != "" and $aryData["lngWorkflowOrderCode"] != 0 )
	{
		$aryData["strWorkflowOrderName"] = fncGetMasterValue( "m_workfloworder", "lngworkflowordercode", "strworkflowordername", $aryData["lngWorkflowOrderCode"] . ":str", 'lngWorkflowOrderNo = 1', $objDB);
// 2004.03.24 suzukaze update start
		$aryData["strWorkflowMessage_visibility"] = "block;";
// 2004.03.24 suzukaze update end
	}
	else
	{
		$aryData["strWorkflowOrderName"] = "承認なし";
// 2004.03.24 suzukaze update start
		$aryData["strWorkflowMessage_visibility"] = "none;";
// 2004.03.24 suzukaze update end
	}
	
	
//2007.07.23 matsuki update start
		$aryData = fncPayConditionCodeMatch($aryData ,$aryHeadColumnNames, $_POST["aryPoDitail"] , $objDB);
//2007.07.23 matsuki update end


	$objDB->close();
	
	

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	//var_dump($aryData);
	
	$objTemplate->getTemplate( "po/confirm/parts.tmpl" );

	// テンプレート生成
	
	$objTemplate->replace( $aryHeadColumnNames );
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML出力 明細行は_%strDetailTable%_で受け渡し
	echo $objTemplate->strTemplate;
	


	return true;

	

?>