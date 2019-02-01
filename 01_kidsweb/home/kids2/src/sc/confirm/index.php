<?php

// ----------------------------------------------------------------------------
/**
*       売上管理  登録確認画面
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
*       2013.05.31　　　　税率取得処理修正（期間内の税率取得できない場合、最新期間の税率を取得する ）
*
*/
// ----------------------------------------------------------------------------


	// 読み込み
	include('conf.inc');
	require (LIB_FILE);
// 2004.12.28 suzukaze update start
	require(LIB_ROOT."libcalc.php");
// 2004.12.28 suzukaze update end
	require(SRC_ROOT."sc/cmn/lib_sc.php");
	require(SRC_ROOT."sc/cmn/lib_scs1.php");
	require(SRC_ROOT."sc/cmn/lib_scs.php");
	require(SRC_ROOT."sc/cmn/column.php");
	
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
	
/*
while (list ($strKeys, $strValues ) = each( $aryData ))
{
	echo "$strKeys ++++ $strValues &nbsp;&nbsp;&nbsp;";
}
exit();
*/



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
		$objResult  = $objDB->fetchObject( $lngResultID, 0 );
		$lngTaxCode = $objResult->lngtaxcode;
		$curTax     = $objResult->curtax;
	}
	else
	{
                // 最新の税率情報を取得する
		$strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
			. "FROM m_tax "
			. "WHERE dtmapplystartdate=(SELECT MAX(dtmapplystartdate) FROM m_tax) "
			. "GROUP BY lngtaxcode, curtax ";


		// 税率などの取得クエリーの実行
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if ( $lngResultNum == 1 )
		{
			$objResult  = $objDB->fetchObject( $lngResultID, 0 );
			$lngTaxCode = $objResult->lngtaxcode;
			$curTax     = $objResult->curtax;
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

		$aryData["strDetailHidden"] = fncDetailHidden_sc( $_POST["aryPoDitail"] ,"insert" , $objDB );
	}
	
	// echo htmlspecialchars( $aryData["strDetailHidden"] );

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
	$aryDetailColumnNames = fncSetSalesTabelName( $aryTableViewDetail, $aryTytle );
	



	$lngAllPrice = 0;

	for ( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
	{
		//-------------------------------------------------------------------------
		// *v2* 部門・担当者の取得
		//-------------------------------------------------------------------------
		$aryQuery   = array();
		$aryQuery[] = "SELECT DISTINCT";
		$aryQuery[] = "	mg.strgroupdisplaycode";
		$aryQuery[] = "	,mg.strgroupdisplayname";
		$aryQuery[] = "	,mu.struserdisplaycode";
		$aryQuery[] = "	,mu.struserdisplayname";
		$aryQuery[] = "FROM";
		$aryQuery[] = "	m_group mg";
		$aryQuery[] = "	,m_user mu";
		$aryQuery[] = "WHERE";
		$aryQuery[] = "	mg.lnggroupcode =";
		$aryQuery[] = "	(";
		$aryQuery[] = "		SELECT mp1.lnginchargegroupcode";
		$aryQuery[] = "		FROM m_product mp1";
		$aryQuery[] = "		WHERE mp1.strproductcode = '" . $_POST["aryPoDitail"][$i]["strProductCode"] . "'";
		$aryQuery[] = "	)";
		$aryQuery[] = "	AND mu.lngusercode =";
		$aryQuery[] = "	(";
		$aryQuery[] = "		SELECT mp2.lnginchargeusercode";
		$aryQuery[] = "		FROM m_product mp2";
		$aryQuery[] = "		WHERE mp2.strproductcode = '" . $_POST["aryPoDitail"][$i]["strProductCode"] . "'";
		$aryQuery[] = "	)";

		$strQuery = "";
		$strQuery = implode( "\n", $aryQuery );


		// クエリー実行
		list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if( $lngResultNum )
		{
			$objResult = $objDB->fetchObject( $lngResultID, 0 );

			// 部門コード・名称
			$_POST["aryPoDitail"][$i]["strInChargeGroup"] = "[" . $objResult->strgroupdisplaycode . "] " . $objResult->strgroupdisplayname;
			// 担当者コード・名称
			$_POST["aryPoDitail"][$i]["strInChargeUser"]  = "[" . $objResult->struserdisplaycode . "] " . $objResult->struserdisplayname;
		}
		else
		{
			fncOutputError( 9051, DEF_ERROR, "", TRUE, "", $objDB );
		}
		//-------------------------------------------------------------------------



		$_POST["aryPoDitail"][$i]["lngrecordno"] = $i + 1;

		// 売上区分
		$_POST["aryPoDitail"][$i]["strSalesClassName"] = fncGetMasterValue( "m_salesclass", "lngsalesclasscode", "strsalesclassname", $_POST["aryPoDitail"][$i]["lngSalesClassCode"] ,'', $objDB );
		
		// 単位
		$_POST["aryPoDitail"][$i]["strProductUnitName"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["aryPoDitail"][$i]["lngProductUnitCode"], '', $objDB );
		
		// 税区分
		$_POST["aryPoDitail"][$i]["strTaxClassName"] = fncGetMasterValue( "m_taxclass", "lngtaxclasscode", "strtaxclassname", $_POST["aryPoDitail"][$i]["lngTaxClassCode"], '', $objDB );

		// 税率
		$_POST["aryPoDitail"][$i]["strTaxName"] = ( $_POST["aryPoDitail"][$i]["lngTaxCode"] != "" ) ?  fncGetMasterValue( "m_tax", "lngtaxcode", "curtax", $_POST["aryPoDitail"][$i]["lngTaxCode"], '', $objDB ) : "";
		
		// 顧客品番
		$_POST["aryPoDitail"][$i]["strGoodsName"] = fncGetMasterValue( "m_product", "strproductcode", "strGoodsCode", $_POST["aryPoDitail"][$i]["strProductCode"].":str", "bytinvalidflag = false", $objDB );

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



		$_POST["aryPoDitail"][$i]["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
		$_POST["aryPoDitail"][$i]["curproductprice_DIS"] = ($_POST["aryPoDitail"][$i]["curProductPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curProductPrice"] ,4 ) : "";
		$_POST["aryPoDitail"][$i]["lnggoodsquantity_DIS"] = ($_POST["aryPoDitail"][$i]["lngGoodsQuantity"] != "") ? number_format( $_POST["aryPoDitail"][$i]["lngGoodsQuantity"] ) : "";
		$_POST["aryPoDitail"][$i]["curtotalprice_DIS"] = ($_POST["aryPoDitail"][$i]["curTotalPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curTotalPrice"] ,2 ) : "";
		$_POST["aryPoDitail"][$i]["curtaxprice_DIS"] = ($_POST["aryPoDitail"][$i]["curTaxPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curTaxPrice"] ,2 ) : "";


		// コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）
		$_POST["aryPoDitail"][$i]["strproductcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strProductCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strProductCode"]."]" : "";
		$_POST["aryPoDitail"][$i]["lngsalesclasscode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["lngSalesClassCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["lngSalesClassCode"]."]" : "";


		//var_dump( $_POST["aryPoDitail"] ); exit();

		// テンプレート読み込み
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/result/parts_detail2.tmpl" );
		
		// テンプレート生成
		$objTemplate->replace( $aryDetailColumnNames );
		$objTemplate->replace( $_POST["aryPoDitail"][$i] );
		$objTemplate->complete();


		// HTML出力
		$aryDetailTable[] = $objTemplate->strTemplate;
	}







//var_dump( $_POST["aryPoDitail"] );exit();


	$aryData["strDetailTable"] = implode ("\n", $aryDetailTable );


	$aryData["strMode"] = "regist";

	// 登録日
	$aryData["dtminsertdate"] = date( 'Y/m/d', time());
	// 状態
	$aryData["strAction"] = "/sc/regist/index2.php?strSessionID=".$aryData["strSessionID"];
	// 入力者
	$aryData["lngInputUserCode"] = $objAuth->UserID;
	$aryData["strInputUserName"] = $objAuth->UserDisplayName;




	// 通貨
	$aryData["strMonetaryUnitName"] = ($aryData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryData["lngMonetaryUnitCode"];
	$aryData["strMonetaryUnitName"] = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $aryData["strMonetaryUnitName"] . ":str", '', $objDB );
	// レートコード
	$strMonetaryrateName = fncGetMasterValue( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $aryData["lngMonetaryRateCode"], '', $objDB);
	$aryData["strMonetaryrateName"] = ( $strMonetaryrateName == "−" ) ? "" : $strMonetaryrateName; 

	//ヘッダ備考の特殊文字変換
	$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );


	$aryData["curConversionRate_DIS"] = number_format( $aryData["curConversionRate"],6 );	// 換算レート
	$aryData["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];

	//$aryData["curAllTotalPrice_DIS"] = number_format( $aryData["curAllTotalPrice"],2 );		// 合計金額
	$aryData["curAllTotalPrice_DIS"] = number_format( $lngAllPrice, 2 );		// 合計金額

	$aryData["lngInputUserCode_DISCODE"] = ( $aryData["lngInputUserCode"] != "" ) ? "[".$aryData["lngInputUserCode"]."]" : "";
	$aryData["lngcustomercode_DISCODE"] = ( $aryData["lngCustomerCode"] != "" ) ? "[".$aryData["lngCustomerCode"]."]" : "";
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

	// ワークフローメッセージ表示・非表示設定
	$aryData["strWorkflowMessage_visibility"] = 'none';


	$objDB->freeResult( $lngResultID );
	$objDB->close();

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "sc/confirm/parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;


	return true;

?>