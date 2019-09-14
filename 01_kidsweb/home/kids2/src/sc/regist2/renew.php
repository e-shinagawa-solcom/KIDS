<?php

// ----------------------------------------------------------------------------
/**
*       発注管理  修正画面
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
*         ・修正時登録画面を表示
*         ・入力エラーチェック
*         ・登録ボタン押下後、登録確認画面へ
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



	// 読み込み
	include('conf.inc');
	require(LIB_FILE);
	require(SRC_ROOT."po/cmn/lib_po.php");
	require(SRC_ROOT."po/cmn/lib_pop.php");
	require(SRC_ROOT."po/cmn/lib_pos1.php");
	require(SRC_ROOT."po/cmn/column.php");


	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	
	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];
	$aryData["lngOrderNo"]      = $_REQUEST["lngOrderNo"];
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
	

	$objDB->open("", "", "", "");
	
	// 文字列チェック
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );


	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	
	$lngUserCode = $objAuth->UserCode;
	
	
	// 500	発注管理
	if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	
	
	// 505 発注管理（発注修正）
	if ( !fncCheckAuthority( DEF_FUNCTION_PO5, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}



	// 508 発注管理（商品マスタダイレクト修正）
	if( !fncCheckAuthority( DEF_FUNCTION_PO8, $objAuth ) )
	{
		$aryData["popenview"] = 'hidden';
	}




	//-------------------------------------------------------------------------
	// ■「製品」にログインユーザーが属しているかチェック
	//-------------------------------------------------------------------------
	$strFncFlag = "PO";
	$blnCheck = fncCheckInChargeProduct( $aryData["lngOrderNo"], $lngUserCode, $strFncFlag, $objDB );

	// ユーザーが対象製品に属していない場合
	if( !$blnCheck )
	{
		fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
	}




// 2005.07.14 K.Saito update start
	//
	// 「発注データ」の有効性チェックを行う
	//
	include "../statuscheck.php";
	if( !fncPoDataStatusCheck( $aryData["lngOrderNo"], $objDB) )
	{
		return false;
	}
// 2005.07.14 K.Saito update end


	// check
	if( $_POST["strMode"] == "check" || $_POST["strMode"] == "renew" )
	{
		// 明細行を除く
		for( $i = 0; $i < count( $_POST ); $i++ )
		{
			list( $strKeys, $strValues ) = each( $_POST );

			if( $strKeys != "aryPoDitail")
			{
				$aryData[$strKeys] = $strValues;
			}
		}
	
		// headerの項目チェック
		list ( $aryData, $bytErrorFlag ) = fncCheckData_po( $aryData,"header", $objDB );
		$errorCount = ( $bytErrorFlag != "") ? 1 : 0;
		
		// 2004/03/15 watanabe update start
		// 商品コードに付随する商品が存在するか
		for( $i=0; $i < count( $_POST["aryPoDitail"] ); $i++ )
		{
			// 製品コード００００の製品対応
			$strProductCode = fncGetMasterValue( "m_product", "strproductcode", "strproductcode",  $_POST["aryPoDitail"][$i]["strProductCode"] . ":str",'',$objDB );
			if( !$strProductCode )
			{
				$aryDetailErrorMessage[] = fncOutputError ( 303, "", "", FALSE, "", $objDB );
			}
		}
		// watanabe end
		
		
		// 明細行のチェック
		if( is_array( $_POST["aryPoDitail"] ))
		{
			for( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
			{
				list ( $aryDetailCheck[], $bytErrorFlag2[] ) = fncCheckData_po( $_POST["aryPoDitail"][$i], "detail", $objDB );
			}
		}


		$strDetailErrorMessage = fncDetailError( $bytErrorFlag2 );
			
		if( $strDetailErrorMessage != "")
		{
			$aryDetailErrorMessage[] =  $strDetailErrorMessage;
		}
		
		
		// 明細行のエラー関数
		if( !is_array( $_POST["aryPoDitail"]))
		{
			$aryDetailErrorMessage[] = fncOutputError ( 9001, "", "", FALSE, "", $objDB );
		}

		// 明細行のデータに対して、製品コードが違うデータが存在しないかどうかのチェック
		$bytCheck = fncCheckOrderDetailProductCode ( $_POST["aryPoDitail"], $objDB );
		if ( $bytCheck == 99 )
		{
			$aryDetailErrorMessage[] = fncOutputError( 506, "", "", FALSE, "", $objDB );
		}

		// 確認画面を表示する際に最新リバイズの発注が「申請中」になっていないかどうかの確認を行う
		$strCheckQuery = "SELECT lngOrderStatusCode FROM m_Order o WHERE o.strOrderCode = '" . $aryData["strOrderCode"] . "'";
		$strCheckQuery .= " AND o.bytInvalidFlag = FALSE ";
		$strCheckQuery .= " AND o.lngRevisionNo = ( "
			. "SELECT MAX( o1.lngRevisionNo ) FROM m_Order o1 WHERE o1.strOrderCode = o.strOrderCode )\n";
		$strCheckQuery .= " AND o.strReviseCode = ( "
			. "SELECT MAX( o2.strReviseCode ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode )\n";

		// チェッククエリーの実行
		list ( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

		if ( $lngCheckResultNum == 1 )
		{
			$objResult          = $objDB->fetchObject( $lngCheckResultID, 0 );
			$lngOrderStatusCode = $objResult->lngorderstatuscode;

			if ( $lngOrderStatusCode == DEF_ORDER_APPLICATE )
			{
				fncOutputError ( 505, DEF_WARNING, "", TRUE, "../po/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
		}

		$objDB->freeResult( $lngCheckResultID );




		
		// この辺不明？？？
		
		$aryData = fncChangeData3($aryData, $objDB);


		// 通貨をdisableにする
		if( is_array( $_POST["aryPoDitail"] ))
		{
			$aryData["strMonetaryDisable"] = "disabled";
			// 明細行をhidden値に変換する
			$aryData["strDetailHidden"] = fncDetailHidden( $_POST["aryPoDitail"], "insert" ,$objDB );
		}
			
		//ヘッダ備考の特殊文字変換
		$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );
		



		// エラーで戻った場合 ==================================
		if( $errorCount != 0 || is_array( $aryDetailErrorMessage ))
		{

			if( is_array( $aryDetailErrorMessage ) )
			{
				$aryData["strErrorMessage"] = implode(" : ", $aryDetailErrorMessage );
			}



			// 権限グループコードの取得
			$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

			// 承認ルートの生成
			// 「マネージャー」以上の場合
			if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				$aryData["lngWorkflowOrderCode"] = '<option value="0">承認なし</option>';
			}
			else
			{
				$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , '');
			}



			// プルダウンメニューの生成
			// 通貨
			$aryData["lngMonetaryUnitCode"] 		= fncPulldownMenu( 0, $aryData["lngMonetaryUnitCode"], '', $objDB );
			// レートタイプ
			$aryData["lngMonetaryRateCode"]			= fncPulldownMenu( 1, $aryData["lngMonetaryRateCode"], '', $objDB );
			// 支払条件
			$aryData["lngPayConditionCode"]			= fncPulldownMenu( 2, $aryData["lngPayConditionCode"], '', $objDB );
			// 仕入科目
			$aryData["strStockSubjectCode"]			= fncPulldownMenu( 3, $aryData["strStockSubjectCode"], '', $objDB );

			// 運搬方法
			$aryData["lngCarrierCode"]				= fncPulldownMenu( 6, $aryData["lngCarrierCode"], '', $objDB );
			// 製品単位
			$aryData["lngProductUnitCode_gs"]		= fncPulldownMenu( 7, $aryData["lngProductUnitCode"], '', $objDB );
			// 荷姿単位
			$aryData["lngProductUnitCode_ps"]		= fncPulldownMenu( 8, $aryData["lngPackingUnitCode"], '', $objDB );




			//-------------------------------------------------------------------------
			// 状態コードが「 null / "" 」の場合、「0」を再設定
			//-------------------------------------------------------------------------
			$aryData["lngOrderStatusCode"] = fncCheckNullStatus( $aryData["lngOrderStatusCode"] );

			//-------------------------------------------------------------------------
			// 発注状態(表示用)の取得
			//-------------------------------------------------------------------------
			$aryData["lngOrderStatusCode_Display"]	= ( $aryData["lngorderstatuscode"] != "" ) ? fncGetMasterValue("m_orderstatus", "lngorderstatuscode", "strorderstatusname", $aryData["lngOrderStatusCode"],'', $objDB ) : "" ;



			if( is_array( $_POST["aryPoDitail"] ) )
			{
				$aryData["MonetaryUnitDisabled"] = " disabled";
				//$aryData["PayConditionDisabled"] = " disabled";
			}
			
			$aryData["strMode"] = "check";
			$aryData["RENEW"] = TRUE;
			
// 2004.04.08 suzukaze update start
			$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;
// 2004.04.08 suzukaze update end

// 2004.04.19 suzukaze update start
			$aryData["strPageCondition"] = "renew";
// 2004.04.19 suzukaze update end

			// submit関数
			$aryData["lngRegistConfirm"] = 0;

			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード

			echo fncGetReplacedHtml( "po/regist/parts.tmpl", $aryData ,$objAuth );
			
			$objDB->close();

			return true;
			
		}
		else
		{
			$aryData["strProcMode"] = "renew";
			$aryData["RENEW"] = TRUE;
			
			$aryData["strBodyOnload"] = "";
			
			// submit関数
			$aryData["lngRegistConfirm"] = 0;

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
			$aryData["strProcMode"] = "renew";
			// 部門
			$aryData["strInChargeGroupName"] = fncGetMasterValue( "m_group", "strgroupdisplaycode", "strgroupdisplayname",  $_POST['lngInChargeGroupCode'] . ":str",'',$objDB);
			// 担当者
			$aryData["strInChargeUserName"] = fncGetMasterValue( "m_user", "struserdisplaycode" ,"struserdisplayname" , $_POST["lngInChargeUserCode"] . ":str",'',$objDB);
			// 登録日
			$aryData["dtminsertdate"] = date( 'Y/m/d', time());
			// 入力者
			$aryData["lngInputUserCode"] = $objAuth->UserID;
			$aryData["strInputUserName"] = $objAuth->UserDisplayName;
			
			// 通貨
			$_POST["strMonetaryUnitName"] = ($_POST["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $_POST["lngMonetaryUnitCode"];
			$aryData["strMonetaryUnitName"] = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $_POST["strMonetaryUnitName"] . ":str", '', $objDB );
			// 支払条件
			if ( $_POST["lngPayConditionCode"] != "" )
			{
				$strPayConditionName = fncGetMasterValue( "m_paycondition", "lngpayconditioncode", "strpayconditionname", $_POST["lngPayConditionCode"], '', $objDB);
				$aryData["strPayConditionName"] = ( $strPayConditionName == "−" ) ? "" : $strPayConditionName;
			}

			// 納品場所
			$aryData["strLocationName"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "strcompanydisplayname", $_POST["lngLocationCode"].":str", '', $objDB);

			// レートコード
			$strMonetaryrateName = fncGetMasterValue( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $_POST["lngMonetaryRateCode"], '', $objDB);
			$aryData["strMonetaryrateName"] = ( $strMonetaryrateName == "−" ) ? "" : $strMonetaryrateName;

			// 状態
			$aryData["strAction"] = "/po/regist/index2.php?strSessionID=".$aryData["strSessionID"];
			
			// number_format 2004/03/11 watanabe
			$aryData["curConversionRate_DIS"] = number_format( $aryData["curConversionRate"],6 );	// 換算レート
			$aryData["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
			$aryData["curAllTotalPrice_DIS"] = number_format( $aryData["curAllTotalPrice"],2 );		// 合計金額
			// watanabe update end
			
			// 2004/03/19 watanabe update コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）
			$aryData["lngInputUserCode_DISCODE"] = ( $_POST["lngInputUserCode"] != "" ) ? "[".$_POST["lngInputUserCode"]."]" : "";
			$aryData["lngCustomerCode_DISCODE"] = ( $_POST["lngCustomerCode"] != "" ) ? "[".$_POST["lngCustomerCode"]."]" : "";
			//$aryData["lngInChargeGroupCode_DISCODE"] = ( $_POST["lngInChargeGroupCode"] != "" ) ? "[".$_POST["lngInChargeGroupCode"]."]" : "";
			//$aryData["lngInChargeUserCode_DISCODE"] = ( $_POST["lngInChargeUserCode"] != "" ) ? "[".$_POST["lngInChargeUserCode"]."]" : "";
			$aryData["lngLocationCode_DISCODE"] = ( $_POST["lngLocationCode"] != "" ) ? "[".$_POST["lngLocationCode"]."]" : "";
			// watanabe update end
			
			// ワークフロー順序
			if ( $_POST["lngWorkflowOrderCode"] != "" and $_POST["lngWorkflowOrderCode"] != 0 )
			{
				$aryData["strWorkflowOrderName"] = fncGetMasterValue( "m_workfloworder", "lngworkflowordercode", "strworkflowordername", $_POST["lngWorkflowOrderCode"] . ":str", 'lngWorkflowOrderNo = 1', $objDB);

				$aryData["strWorkflowMessage_visibility"] = "block;";
			}
			else
			{
				$aryData["strWorkflowOrderName"] = "承認なし";

				$aryData["strWorkflowMessage_visibility"] = "none;";
			}
			

			$aryData["lngRegistConfirm"] = 0;

			$aryData["RENEW"] = TRUE;

			$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

			$aryData["strActionURL"] = "/po/regist/index2.php?strSessionID=".$aryData["strSessionID"];

// 2004.04.08 suzukaze update start
			$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;
// 2004.04.08 suzukaze update end



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


			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード
			
			//2007.07.23 matsuki update start				
			$aryData = fncPayConditionCodeMatch($aryData ,$aryHeadColumnNames, $_POST["aryPoDitail"] , $objDB);
			//2007.07.23 matsuki update end

			// テンプレート読み込み
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "po/confirm/parts.tmpl" );
			$aryData["yokuwakaran"] = $yokuwakaran;
			

			// テンプレート生成
			$objTemplate->replace( $aryHeadColumnNames );
			$objTemplate->replace( $aryData );
			$objTemplate->complete();

			
			// HTML出力
			echo $objTemplate->strTemplate;
			
			
			$objDB->close();
			
			return true;
		}

	}





	// 権限グループコード(ユーザー以下)チェック
	$blnAG = fncCheckUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

	// 「ユーザー」以下の場合
	if( $blnAG )
	{
		// 承認ルート存在チェック
		$blnWF = fncCheckWorkFlowRoot( $lngUserCode, $aryData["strSessionID"], $objDB );

		// 承認ルートが存在しない場合
		if( !$blnWF )
		{
			fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
		}
	}





	// 最初のページ
	$aryQuery   = array();
	$aryQuery[] = "SELECT ";
	$aryQuery[] = "lngrevisionno, ";								// リビジョン番号
	$aryQuery[] = "strordercode, ";									// 発注コード
	$aryQuery[] = "strrevisecode as strReviseCode, ";				// リバイズコード
	$aryQuery[] = "To_char( dtmAppropriationDate, 'YYYY/mm/dd') as dtmOrderAppDate,";	// 計上日
	$aryQuery[] = "lngcustomercompanycode as lngCustomerCode, ";	// 会社コード（仕入先）
	//$aryQuery[] = "lnggroupcode as lngInChargeGroupCode, ";		// 部門
	//$aryQuery[] = "lngusercode as lngInChargeUserCode, ";			// 担当者
	$aryQuery[] = "lngorderstatuscode, ";							// 発注状態コード
	$aryQuery[] = "lngmonetaryunitcode, ";							// 通貨単位コード
	$aryQuery[] = "lngmonetaryratecode, ";							// 通貨レートコード
	$aryQuery[] = "curconversionrate, ";							// 換算レート
	$aryQuery[] = "lngpayconditioncode, ";							// 支払条件コード
	$aryQuery[] = "curtotalprice, ";								// 合計金額
	$aryQuery[] = "lngdeliveryplacecode as lngLocationCode, ";		// 納品場所コード / 会社コード
	$aryQuery[] = "To_char( dtmexpirationdate, 'YYYY/mm/dd') as dtmexpirationdate, ";		// 発注有効期限日
	$aryQuery[] = "strNote ";										// 備考
	$aryQuery[] = "FROM m_order ";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "lngorderno = ". $aryData["lngOrderNo"];


	$strQuery = implode("\n", $aryQuery );


	// クエリー実行
	$objDB->freeResult( $lngResultID );

	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}

	$aryQueryResult = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );



	//-------------------------------------------------------------------------
	// 発注状態のチェック
	//-------------------------------------------------------------------------
	// 申請中の場合
	if( $aryQueryResult["lngorderstatuscode"] == DEF_ORDER_APPLICATE )
	{
		fncOutputError( 505, DEF_WARNING, "", TRUE, "", $objDB );
	}


	//-------------------------------------------------------------------------
	// 状態コードが「 null / "" 」の場合、「0」を再設定
	//-------------------------------------------------------------------------
	$aryQueryResult["lngorderstatuscode"] = fncCheckNullStatus( $aryQueryResult["lngorderstatuscode"] );




	$aryQuery   = array();
	$aryQuery[] = "SELECT ";
	$aryQuery[] = "lngorderdetailno, ";								// 発注明細番号
	$aryQuery[] = "lngrevisionno, ";								// リビジョン番号
	$aryQuery[] = "strproductcode, ";								// 製品コード
	$aryQuery[] = "lngstocksubjectcode, ";							// 仕入科目コード
	$aryQuery[] = "lngstockitemcode, ";								// 仕入部品コード
	$aryQuery[] = "To_char( dtmdeliverydate, 'YYYY/mm/dd' ) as dtmdeliverydate,";	// 納品日
	$aryQuery[] = "lngdeliverymethodcode, ";						// 運搬方法コード
	$aryQuery[] = "lngconversionclasscode, ";						// 換算区分コード / 1：単位計上/ 2：荷姿単位計上
	$aryQuery[] = "curproductprice, ";								// 製品価格
	$aryQuery[] = "lngproductquantity, ";							// 製品数量
	$aryQuery[] = "lngproductunitcode, ";							// 製品単位コード
	$aryQuery[] = "lngtaxclasscode, ";								// 消費税区分コード
	$aryQuery[] = "lngtaxcode, ";									// 消費税コード
	$aryQuery[] = "curtaxprice, ";									// 消費税金額
	$aryQuery[] = "cursubtotalprice, ";								// 小計金額
	$aryQuery[] = "strnote, ";										// 備考
	$aryQuery[] = "strmoldno as strserialno ";						// シリアル番号
	$aryQuery[] = "FROM t_orderdetail ";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "lngorderno = ". $aryData["lngOrderNo"];
	$aryQuery[] = " ORDER BY lngSortKey ASC";
	$strQuery = "";
	$strQuery = implode("\n", $aryQuery );




	$objDB->freeResult( $lngResultID );

	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}
	
	if( !$lngResultNum = pg_num_rows( $lngResultID ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}
	else
	{
		for( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryQueryResult2[] = pg_fetch_array( $lngResultID, $i, PGSQL_ASSOC );
		}
	}



	$lngWorkflowOrderCode = fncGetMasterValue( "m_workflow", "strworkflowkeycode", "lngworkflowordercode", $aryData["lngOrderNo"] . ":str", '', $objDB );


	// 関数 fncChangeDisplayNameで表示用データに変換(HEADER）
	$aryNewResult = fncChangeData2( $aryQueryResult , $objDB );


	// 明細行をhidden値に変換する
	$aryNewResult["strDetailHidden"] = fncDetailHidden( $aryQueryResult2 ,"", $objDB );



	// プルダウンメニューの生成
	// 通貨
	$lngMonetaryUnitCode = fncGetMasterValue("m_monetaryunit", "lngmonetaryunitcode","strmonetaryunitsign", $aryQueryResult["lngmonetaryunitcode"], '', $objDB );

	$aryNewResult["lngmonetaryunitcode"] 		= fncPulldownMenu( 0, $lngMonetaryUnitCode, '', $objDB );

	// レートタイプ
	$aryNewResult["lngmonetaryratecode"]		= fncPulldownMenu( 1, $aryQueryResult["lngmonetaryratecode"], '', $objDB );
	// 支払条件
	$aryNewResult["lngpayconditioncode"]		= fncPulldownMenu( 2, $aryQueryResult["lngpayconditioncode"], '', $objDB );
	// 仕入科目
	$aryNewResult["strStockSubjectCode"]		= fncPulldownMenu( 3, $aryQueryResult["strStockSubjectCode"], '', $objDB );
	// 運搬方法
	$aryNewResult["lngCarrierCode"]				= fncPulldownMenu( 6, $aryQueryResult["lngCarrierCode"], '', $objDB );
	// 製品単位
	$aryNewResult["lngProductUnitCode_gs"]		= fncPulldownMenu( 7, $aryQueryResult["lngProductUnitCode"], '', $objDB );
	// 荷姿単位
	$aryNewResult["lngProductUnitCode_ps"]		= fncPulldownMenu( 8, $aryQueryResult["lngPackingUnitCode"], '', $objDB );


	//-------------------------------------------------------------------------
	// 発注状態(表示用)の取得
	//-------------------------------------------------------------------------
	$aryNewResult["lngOrderStatusCode_Display"] = fncGetMasterValue( "m_orderstatus", "lngorderstatuscode", "strorderstatusname", $aryNewResult["lngorderstatuscode"],'', $objDB );



	$aryNewResult["strMode"] = "check";									// モード（次の動作）check→renew
	$aryNewResult["strSessionID"] = $aryData["strSessionID"];			// セッション
	$aryNewResult["strActionUrl"] = "renew.php"; 						// formのaction
	$aryNewResult["lngOrderNo"] = $aryData["lngOrderNo"];				// オーダ番号
	
	if( is_array( $aryQueryResult2 ) )
	{
		$aryNewResult["MonetaryUnitDisabled"] = " disabled";
	}



	// 権限グループコードの取得
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

	// 承認ルートの生成
	// 「マネージャー」以上の場合
	if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
	{
		$aryNewResult["lngWorkflowOrderCode"] = '<option value="0">承認なし</option>';
	}
	else
	{
		$aryNewResult["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $lngWorkflowOrderCode );
	}


	//ヘッダ備考の特殊文字変換
	$aryNewResult["strnote"] = fncHTMLSpecialChars( $aryNewResult["strnote"] );

// 2004.04.08 suzukaze update start
	$aryNewResult["lngCalcCode"] = DEF_CALC_KIRISUTE;
// 2004.04.08 suzukaze update end

// 2004.04.19 suzukaze update start
	$aryData["strPageCondition"] = "renew";
// 2004.04.19 suzukaze update end


	$aryNewResult["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード


	$objDB->close();
	$objDB->freeResult( $lngResultID );
	
	$aryNewResult["RENEW"] = TRUE;

	// ヘルプ対応
	$aryNewResult["lngFunctionCode"] = DEF_FUNCTION_PO5;
	
	echo fncGetReplacedHtml( "po/regist/parts.tmpl", $aryNewResult ,$objAuth );
	
	return true;
	
?>