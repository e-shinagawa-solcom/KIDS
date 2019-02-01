<?php

// ----------------------------------------------------------------------------
/**
*       仕入管理  登録確認画面
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
*	  ・税率取得処理修正（期間内の税率取得できない場合、最新期間の税率を取得する 20130531）
*
*/
// ----------------------------------------------------------------------------



	// 読み込み
	include('conf.inc');
	require (LIB_FILE);
// 2004.12.28 suzukaze update start
	require(LIB_ROOT."libcalc.php");
// 2004.12.28 suzukaze update end
	require(SRC_ROOT."pc/cmn/lib_pc.php");
	require(SRC_ROOT."pc/cmn/lib_pcs1.php");
	require(SRC_ROOT."pc/cmn/lib_pcs.php");
	require(SRC_ROOT."pc/cmn/column.php");
	
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
	
	// 明細行を除く
	for( $i = 0; $i < count( $_POST ); $i++ )
	{
		list( $strKeys, $strValues ) = each ( $_POST );
		if($strKeys != "aryPoDitail")
		{
			$aryData[$strKeys] = $strValues;
		}
	}
	
	//ヘッダ備考の特殊文字変換
	$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );

// 2004.12.28 suzukaze update start
	// 明細行用に消費税コードを取得する
	// 消費税コード
	// 計上日よりその時の税率をもとめる
	$strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
		. "FROM m_tax "
		. "WHERE dtmapplystartdate <= '" . $aryData["dtmOrderAppDate"] . "' "
		. "AND dtmapplyenddate >= '" . $aryData["dtmOrderAppDate"] . "' "
		. "GROUP BY lngtaxcode, curtax "
		. "ORDER BY 3 ";

	// 税率などの取得クエリーの実行
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum == 1 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngTaxCode = $objResult->lngtaxcode;
		$curTax = $objResult->curtax;
	}
	else
	{
		$strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
				. "FROM m_tax "
				. "WHERE dtmapplystartdate=(SELECT MAX(dtmapplystartdate) FROM m_tax) "
				. "GROUP BY lngtaxcode, curtax ";
		// 税率などの取得クエリーの実行
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if ( $lngResultNum == 1 )
		{
			$objResult = $objDB->fetchObject( $lngResultID, 0 );
			$lngTaxCode = $objResult->lngtaxcode;
			$curTax = $objResult->curtax;
		}
		else
		{
			fncOutputError ( 9051, DEF_ERROR, "消費税情報の取得に失敗しました。", TRUE, "", $objDB );
		}
	}
	$objDB->freeResult( $lngResultID );

	$lngCalcCode = DEF_CALC_KIRISUTE;

	// 仕入時の通貨単位コードより処理対象桁数を設定
	if ( $aryData["lngMonetaryUnitCode"] == "\\" or $aryData["lngMonetaryUnitCode"] == "\\\\" )
	{
		$lngDigitNumber = 0;		// 日本円の場合は０桁
	}
	else
	{
		$lngDigitNumber = 2;		// 日本円以外の場合は小数点以下２桁
	}


	// 明細行処理 ===========================================================================================
	// 明細行のhidden生成
	if( is_array( $_POST["aryPoDitail"] ) )
	{
		// hidden値生成の際に税額異常の調査を行う
		for ( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
		{
			// 内税の際に製品価格 ＝ 税抜金額 ＋ 税額 にならない場合は税額の再計算を行う
			if ( $_POST["aryPoDitail"][$i]["lngTaxClassCode"] == DEF_TAXCLASS_UCHIZEI ) 
			{
				// 製品価格 = 製品単価 × 数量
				$curProductTotalPrice = $_POST["aryPoDitail"][$i]["curProductPrice"] * $_POST["aryPoDitail"][$i]["lngGoodsQuantity"];

				// 製品価格 が 税抜金額 ＋ 税額 になっていない場合
				if ( $curProductTotalPrice != ( $_POST["aryPoDitail"][$i]["curTaxPrice"] + $_POST["aryPoDitail"][$i]["curTotalPrice"] ) )
				{
					// 内税税額 ＝ 税抜金額 × 税率
					$curTaxPrice = $_POST["aryPoDitail"][$i]["curTotalPrice"] * $curTax;
					// 端数処理を行う
					$curTaxPrice = fncCalcDigit( $curTaxPrice, $lngCalcCode, $lngDigitNumber );

					$_POST["aryPoDitail"][$i]["curTaxPrice"] = $curTaxPrice;
				}
			}
		}

		$aryData["strDetailHidden"] = fncDetailHidden_pc( $_POST["aryPoDitail"] ,"insert" , $objDB );
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
	$aryDetailColumnNames = fncSetStockTabelName( $aryTableViewDetail, $aryTytle );
	



	$lngAllPrice = 0;

	for ( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
	{
	
		$_POST["aryPoDitail"][$i]["lngrecordno"] = $i + 1;
		
		// 仕入科目
		if ( $_POST["aryPoDitail"][$i]["strStockSubjectCode"] != "" )
		{
			$_POST["aryPoDitail"][$i]["strStockSubjectName"] 
				= fncGetMasterValue( "m_stocksubject", "lngstocksubjectcode", "strstocksubjectname", 
					$_POST["aryPoDitail"][$i]["strStockSubjectCode"], '', $objDB );
		}
		
		// 仕入部品 
		if ( $_POST["aryPoDitail"][$i]["strStockItemCode"] != "" )
		{
			$_POST["aryPoDitail"][$i]["strStockItemName"] 
				= fncGetMasterValue( "m_stockitem", "lngstockitemcode", "strstockitemname", 
					$_POST["aryPoDitail"][$i]["strStockItemCode"], "lngstocksubjectcode = ".$_POST["aryPoDitail"][$i]["strStockSubjectCode"], $objDB );
		}
		
		// 運搬方法
		//echo "carriercode : ".$_POST["aryPoDitail"][$i]["lngCarrierCode"]."<br>";
		//exit();
		if ( $_POST["aryPoDitail"][$i]["lngCarrierCode"] != "" )
		{
			$_POST["aryPoDitail"][$i]["strCarrierName"] 
				= fncGetMasterValue( "m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", 
					$_POST["aryPoDitail"][$i]["lngCarrierCode"],'', $objDB );
		}
		
		// 顧客品番
		if ( $_POST["aryPoDitail"][$i]["strProductCode"] != "" )
		{
			$_POST["aryPoDitail"][$i]["strGoodsName"] 
				= fncGetMasterValue( "m_product", "strproductcode", "strGoodsCode", 
					$_POST["aryPoDitail"][$i]["strProductCode"].":str", "bytinvalidflag = false", $objDB );
		}
		
		// 単位
		if ( $_POST["aryPoDitail"][$i]["lngProductUnitCode"] != "" )
		{
			$_POST["aryPoDitail"][$i]["strProductUnitName"] 
				= fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", 
					$_POST["aryPoDitail"][$i]["lngProductUnitCode"], '', $objDB );
		}
		
		// 税区分
		if ( $_POST["aryPoDitail"][$i]["lngTaxClassCode"] != "" )
		{
			$_POST["aryPoDitail"][$i]["strTaxClassName"] 
				= fncGetMasterValue( "m_taxclass", "lngtaxclasscode", "strtaxclassname", $_POST["aryPoDitail"][$i]["lngTaxClassCode"], '', $objDB );
		}


		// 税率
		if ( $_POST["aryPoDitail"][$i]["lngTaxCode"] != "" )
		{
			$_POST["aryPoDitail"][$i]["curTax"] = $curTax;
		}


		// 明細行備考の特殊文字変換
		$_POST["aryPoDitail"][$i]["strDetailNote"] = fncHTMLSpecialChars( $_POST["aryPoDitail"][$i]["strDetailNote"] );
		

		$strProductName = "";

		if( $strProductName = fncGetMasterValue( "m_product", "strproductcode", "strproductname",  $_POST["aryPoDitail"][$i]["strProductCode"] . ":str",'',$objDB ) )
		{
			$_POST["aryPoDitail"][$i]["strproductname"] = $strProductName;
		}

		
		// 内税の際に製品価格 ＝ 税抜金額 ＋ 税額 にならない場合は税額の再計算を行う
		if ( $_POST["aryPoDitail"][$i]["lngTaxClassCode"] == DEF_TAXCLASS_UCHIZEI ) 
		{
			// 製品価格 = 製品単価 × 数量
			$curProductTotalPrice = $_POST["aryPoDitail"][$i]["curProductPrice"] * $_POST["aryPoDitail"][$i]["lngGoodsQuantity"];

			// 製品価格 が 税抜金額 ＋ 税額 になっていない場合
			if ( $curProductTotalPrice != ( $_POST["aryPoDitail"][$i]["curTaxPrice"] + $_POST["aryPoDitail"][$i]["curTotalPrice"] ) )
			{
				// 内税税額 ＝ 税抜金額 × 税率
				$curTaxPrice = $_POST["aryPoDitail"][$i]["curTotalPrice"] * $curTax;
				// 端数処理を行う
				$curTaxPrice = fncCalcDigit( $curTaxPrice, $lngCalcCode, $lngDigitNumber );

				$_POST["aryPoDitail"][$i]["curTaxPrice"] = $curTaxPrice;
			}
		}



		// 合計金額の取得
		$lngAllPrice += $_POST["aryPoDitail"][$i]["curTotalPrice"];



		// number_format
		$_POST["aryPoDitail"][$i]["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
		$_POST["aryPoDitail"][$i]["curproductprice_DIS"] =  ($_POST["aryPoDitail"][$i]["curProductPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curProductPrice"] ,4 ) : "";
		$_POST["aryPoDitail"][$i]["lnggoodsquantity_DIS"] =  ($_POST["aryPoDitail"][$i]["lngGoodsQuantity"] != "") ? number_format( $_POST["aryPoDitail"][$i]["lngGoodsQuantity"] ) : "";
		$_POST["aryPoDitail"][$i]["curtaxprice_DIS"] =  ($_POST["aryPoDitail"][$i]["curTaxPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curTaxPrice"] ,2 ) : "";
		$_POST["aryPoDitail"][$i]["curTotalPrice_DIS"] = ($_POST["aryPoDitail"][$i]["curTotalPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curTotalPrice"] ,2 ) : "";

		// コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）
		$_POST["aryPoDitail"][$i]["strproductcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strProductCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strProductCode"]."]" : "";
		$_POST["aryPoDitail"][$i]["strstockitemcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strStockItemCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strStockItemCode"]."]" : "";
		$_POST["aryPoDitail"][$i]["strstocksubjectcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strStockSubjectCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strStockSubjectCode"]."]" : "";






		// テンプレート読み込み
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "pc/result/parts_detail2.tmpl" );
		
		
		// テンプレート生成
		$objTemplate->replace( $aryDetailColumnNames );
		$objTemplate->replace( $_POST["aryPoDitail"][$i] );
		$objTemplate->complete();
		
		// HTML出力
		$aryDetailTable[] = $objTemplate->strTemplate;
	}




	$aryData["strDetailTable"] = implode ("\n", $aryDetailTable );
	
	$aryData["strProcMode"] = "regist";

	$aryData["strMode"] = "regist";
	
	// 登録日
	$aryData["dtminsertdate"] = date( 'Y/m/d', time());
	// 状態
	$aryData["strAction"] = "/pc/regist/index2.php?strSessionID=".$aryData["strSessionID"];
	// 入力者
	$aryData["lngInputUserCode"] = $objAuth->UserID;
	$aryData["strInputUserName"] = $objAuth->UserDisplayName;



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



	// 通貨
	$aryData["strMonetaryUnitName"]   = ($aryData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryData["lngMonetaryUnitCode"];
	$aryData["strMonetaryUnitName"]   = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $aryData["strMonetaryUnitName"] . ":str", '', $objDB );
	// レートコード
	$strMonetaryrateName = fncGetMasterValue( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $aryData["lngMonetaryRateCode"], '', $objDB);
	$aryData["strMonetaryrateName"]   = ( $strMonetaryrateName == "−" ) ? "" : $strMonetaryrateName;
	// 支払条件
	$strPayConditionName = fncGetMasterValue( "m_paycondition", "lngpayconditioncode", "strpayconditionname", $aryData["lngPayConditionCode"], '', $objDB );
	$aryData["strPayConditionName"]   = ( $strPayConditionName == "−" ) ? "" : $strPayConditionName;
	$aryData["curConversionRate_DIS"] = number_format( $aryData["curConversionRate"],6 );	// 換算レート
	$aryData["strMonetaryrate"]       = $aryData["lngMonetaryUnitCode"];


	//$aryData["curAllTotalPrice_DIS"]  = number_format( $aryData["curAllTotalPrice"],2 );		// 合計金額
	$aryData["curAllTotalPrice_DIS"] = number_format( $lngAllPrice, 2 );		// 合計金額



	// コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）
	$aryData["lngInputUserCode_DISCODE"] = ( $aryData["lngInputUserCode"] != "" ) ? "[".$aryData["lngInputUserCode"]."]" : "";
	$aryData["lngCustomerCode_DISCODE"]  = ( $aryData["lngCustomerCode"] != "" ) ? "[".$aryData["lngCustomerCode"]."]" : "";
	//$aryData["lngInchargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
	//$aryData["lngInchargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";
	$aryData["lngLocationCode_DISCODE"]  = ( $aryData["lngLocationCode"] != "" ) ? "[".$aryData["lngLocationCode"]."]" : "";








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

	// ワークフローメッセージ表示・非表示設定
	$aryData["strWorkflowMessage_visibility"] = 'none';





	$objDB->close();

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "pc/confirm/parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;


	return true;

?>