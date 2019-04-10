<?php

// ----------------------------------------------------------------------------
/**
*       受注管理  修正画面
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



	//-------------------------------------------------------------------------
	// ■ ライブラリファイル読込
	//-------------------------------------------------------------------------
	include('conf.inc');
	require (LIB_FILE);
	require(SRC_ROOT."po/cmn/lib_po.php");
	require(SRC_ROOT."pc/cmn/lib_pc.php");
	require(SRC_ROOT."so/cmn/lib_so.php");
	require(SRC_ROOT."so/cmn/lib_sos1.php");
	require(SRC_ROOT."so/cmn/column.php");
	require( LIB_DEBUGFILE );

	require_once(SRC_ROOT.'/mold/lib/UtilProduct.class.php'); // 製品マスタユーティリティ
	//-------------------------------------------------------------------------
	// ■ オブジェクト生成
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();
	$objAuth = new clsAuth();


	//-------------------------------------------------------------------------
	// ■ パラメータ取得
	//-------------------------------------------------------------------------
	$aryData["lngReceiveNo"]    = $_REQUEST["lngReceiveNo"];
	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	$strProcMode = $_POST["strProcMode"]; // 処理モード


	//var_dump( $aryData["lngReceiveNo"] ); exit();

	//-------------------------------------------------------------------------
	// ■ DBオープン
	//-------------------------------------------------------------------------
	$objDB->open( "", "", "", "" );


	//-------------------------------------------------------------------------
	// ■ 入力文字列値・セッション・権限チェック
	//-------------------------------------------------------------------------
	// 文字列チェック
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	$lngUserCode = $objAuth->UserCode;


	// 400 受注管理
	if( !fncCheckAuthority( DEF_FUNCTION_SO0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 401 受注管理（受注修正）
	if( !fncCheckAuthority( DEF_FUNCTION_SO5, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}



	// 408 受注管理（商品マスタダイレクト修正）
	if( !fncCheckAuthority( DEF_FUNCTION_SO8, $objAuth ) )
	{
		$aryData["popenview"] = 'hidden';
	}

	//-------------------------------------------------------------------------
	// ■「製品」にログインユーザーが属しているかチェック
	//-------------------------------------------------------------------------
	$strFncFlag = "SO";
	$blnCheck = fncCheckInChargeProduct( $aryData["lngReceiveNo"], $lngUserCode, $strFncFlag, $objDB );

	// ユーザーが対象製品に属していない場合
	if( !$blnCheck )
	{
		fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
	}



	//-------------------------------------------------------------------------
	// ■「受注データ」の有効性チェックを行う
	//-------------------------------------------------------------------------
	include "../statuscheck.php";

	if( !fncSoDataStatusCheck( $aryData["lngReceiveNo"], $objDB) )
	{
		return false;
	}


	//-------------------------------------------------------------------------
	// ■ 入力チェック
	//-------------------------------------------------------------------------
	if( $strProcMode == "check" )
	{
		// 明細行を除く
		for( $i = 0; $i < count( $_POST ); $i++ )
		{
			list( $strKeys, $strValues ) = each ( $_POST );
			if($strKeys != "aryPoDitail")
			{
				$aryData[$strKeys] = $strValues;
			}
		}

		//-----------------------------------------------------------
		// ヘッダー項目チェック
		//-----------------------------------------------------------
		// 発注有効期限日をチェックしないためのフラグ
		$aryData["renew"] = "true";


		//-----------------------------------------------------------
		// DB -> SELECT : m_Receive
		//-----------------------------------------------------------
		// 受注NoがDisabledなので値が渡らないため、受注Noを取得する
		$strReceiveCodeQuery = "SELECT distinct strReceiveCode FROM m_Receive WHERE lngReceiveNo = " . $aryData["lngReceiveNo"];

		list ( $lngReceiveResultID, $lngReceiveResultNum ) = fncQuery( $strReceiveCodeQuery, $objDB );

		if( $lngReceiveResultNum == 1 )
		{
			$objReceiveResult = $objDB->fetchObject( $lngReceiveResultID, 0 );
			$strReceiveCode   = $objReceiveResult->strreceivecode;
		}
		else
		{
			fncOutputError ( 403, DEF_ERROR, "", TRUE, "", $objDB );
		}

		// 結果IDを解放
		$objDB->freeResult( $lngReceiveResultID );


		// 受注番号の取得
		$aryData["strReceiveCode"] = $strReceiveCode;

		list ( $aryData, $bytErrorFlag ) = fncCheckData_so( $aryData,"header", $objDB );

		$errorCount = ( $bytErrorFlag == "TRUE" ) ? 1 : 0;


		//-----------------------------------------------------------
		// 明細行のチェック
		//-----------------------------------------------------------
		$aryQueryResult2 = $_POST["aryPoDitail"];

		for( $i = 0; $i < count( $aryQueryResult2 ); $i++ )
		{
			list ( $aryDetailCheck[], $bytErrorFlag2[] ) = fncCheckData_so( $_POST["aryPoDitail"][$i], "detail", $objDB );
		}

		// 商品コードに付随する商品が存在するか
		for( $i=0; $i < count( $_POST["aryPoDitail"] ); $i++ )
		{
			// 製品コード
			$strProductCode = fncGetMasterValue( "m_product", "strproductcode", "strproductcode",  $_POST["aryPoDitail"][$i]["strProductCode"] . ":str",'',$objDB );

			// 製品コードが索引できなかった場合
			if( !$strProductCode )
			{
				$aryDetailErrorMessage[] = fncOutputError( 303, "", "", FALSE, "", $objDB );
			}
			// 製品コードが索引できた場合
			else
			{
				$utilProduct = UtilProduct::getInstance();
				$product = $utilProduct->selectProductByProductCode($strProductCode);

				// 顧客受注番号が設定されており尚且つ顧客品番が設定されていない場合
				// 0を設定するとfalseになるので注意
				if($_REQUEST["strCustomerReceiveCode"] && !$product["strgoodscode"])
				{
					$aryDetailErrorMessage[] = fncOutputError( 303, "", ": 顧客品番未設定の商品。", FALSE, "", $objDB );
				}
			}
		}

		// 明細行のエラー関数
		$strDetailErrorMessage = fncDetailError( $bytErrorFlag2 );

		if( $strDetailErrorMessage != "" )
		{
			$aryDetailErrorMessage = $strDetailErrorMessage;
		}

		if( !is_array( $_POST["aryPoDitail"] ) )
		{
			$aryDetailErrorMessage[] = fncOutputError ( 9001, "", "", FALSE, "", $objDB );
		}


		//-------------------------------------------------
		// 最新リバイズデータが「申請中」になっていないかどうか確認
		//-------------------------------------------------
		$strCheckQuery = "SELECT lngReceiveStatusCode FROM m_Receive r WHERE r.strReceiveCode = '" . $aryData["strReceiveCode"] . "'";
		$strCheckQuery .= " AND r.bytInvalidFlag = FALSE ";
		$strCheckQuery .= " AND r.lngRevisionNo = ( "
			. "SELECT MAX( r1.lngRevisionNo ) FROM m_Receive r1 WHERE r1.strReceiveCode = r.strReceiveCode )\n";
		$strCheckQuery .= " AND r.strReviseCode = ( "
			. "SELECT MAX( r2.strReviseCode ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode )\n";

		// チェッククエリーの実行
		list( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

		if ( $lngCheckResultNum == 1 )
		{
			$objResult            = $objDB->fetchObject( $lngCheckResultID, 0 );
			$lngReceiveStatusCode = $objResult->lngreceivestatuscode;

			if( $lngOrderStatusCode == DEF_ORDER_APPLICATE )
			{
				fncOutputError( 409, DEF_WARNING, "", TRUE, "../so/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
		}

		// 結果IDを解放
		$objDB->freeResult( $lngCheckResultID );


		//-----------------------------------------------------------
		// 入力エラー
		//-----------------------------------------------------------
		if( $errorCount != 0  || is_array( $aryDetailErrorMessage ) )
		{
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



			if( is_array( $aryDetailErrorMessage ) )
			{
				$aryData["strErrorMessage"] = implode( " : ", $aryDetailErrorMessage);
			}

			if( is_array( $_POST["aryPoDitail"]) )
			{
				// 明細行をhidden値に変換する
				$aryData["strDetailHidden"] = fncDetailHidden_so( $_POST["aryPoDitail"] ,"insert", $objDB);
			}

			// エラーで戻った場合
			// 関数 fncChangeDisplayNameで表示用データに変換(HEADER）
			$aryData = fncChangeData3( $aryData , $objDB );
			$aryData["strMonetaryUnitCodeDis"] = ( is_array( $_POST["aryPoDitail"] ) ) ? "disabled" : "";

			// 備考
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );

			// プルダウンメニューの生成
			// 通貨
			$aryData["lngMonetaryUnitCode"] 		= fncPulldownMenu( 0, $aryData["lngMonetaryUnitCode"], '', $objDB );
			// レートタイプ
			$aryData["lngMonetaryRateCode"]			= fncPulldownMenu( 1, $aryData["lngMonetaryRateCode"], '', $objDB );
			// 製品単位
			$aryData["lngProductUnitCode_gs"]		= fncPulldownMenu( 7, 0, '', $objDB );
			// 荷姿単位
			$aryData["lngProductUnitCode_ps"]		= fncPulldownMenu( 8, 0, '', $objDB );
			// 売上区分
			$aryData["lngSalesClassCode"]			= fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );



			$aryData["lngRegistConfirm"]           = 0;




			//-------------------------------------------------------------------------
			// 状態コードが「 null / "" 」の場合、「0」を再設定
			//-------------------------------------------------------------------------
			$aryData["lngReceiveStatusCode"] = fncCheckNullStatus( $aryData["lngReceiveStatusCode"] );

			//---------------------------------------------
			// 受注状態(表示用)の取得
			//---------------------------------------------
			$aryData["strReceiveStatusCodeDisplay"] = fncGetMasterValue( "m_receivestatus", "lngreceivestatuscode", "strreceivestatusname", $aryData["lngReceiveStatusCode"], '', $objDB );



			$aryData["lngOrderCode"]                = $_GET["strOrderCode"];

			//$aryData["OrderSubmit"] = "fncOrderSubmit();";



			$aryData["RENEW"] = TRUE;

			$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;

			$aryData["strPageCondition"] = "renew";


			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード

			$objDB->close();

			// 結果IDを解放
			$objDB->freeResult( $lngResultID );

			echo fncGetReplacedHtml( "so/regist/parts.tmpl", $aryData ,$objAuth);
			return true;
		}
		//-----------------------------------------------------------
		// 確認画面表示
		//-----------------------------------------------------------
		else
		{
			$aryData["strBodyOnload"] = "";

			// 明細行をhidden値に変換する
			$aryData["strProcMode"] = "renew"; // モード（次の動作）check→insert
			$aryData["RENEW"]       = TRUE;

			// 備考
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );
			$aryData["strDetailHidden"] = fncDetailHidden_so( $_POST["aryPoDitail"] ,"insert", $objDB );

			// submit関数
			$aryData["lngRegistConfirm"] = 0;

			// 言語の設定
			if( isset($aryData["lngLanguageCode"]) and  $aryData["lngLanguageCode"] == 0 )
			{
				$aryTytle = $aryTableTytleEng;
			}
			else
			{
				$aryTytle = $aryTableTytle2;
			}

			// カラム名の設定
			$aryHeadColumnNames   = fncSetReceiveTabelName ( $aryTableViewHead, $aryTytle );
			$aryDetailColumnNames = fncSetReceiveTabelName ( $aryTableViewDetail, $aryTytle );

			for( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
			{
				$_POST["aryPoDitail"][$i]["lngrecordno"] = $i + 1;
				$_POST["aryPoDitail"][$i]["lngsalesclasscode_DIS"] = fncGetMasterValue( "m_salesclass", "lngsalesclasscode", "strsalesclassname", $_POST["aryPoDitail"][$i]["lngSalesClassCode"], '', $objDB );
				$_POST["aryPoDitail"][$i]["lngproductunitcode_DIS"] = fncGetMasterValue( "m_productunit", "lngproductunitcode", "strproductunitname", $_POST["aryPoDitail"][$i]["lngProductUnitCode"], '', $objDB );
				$_POST["aryPoDitail"][$i]["strproductcode_DIS"] = fncGetMasterValue( "m_product", "strproductcode", "strproductname", $_POST["aryPoDitail"][$i]["strProductCode"].":str", '', $objDB );

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
			$aryData["strProcMode"] = "renew";

			// 部門
			$aryData["strInChargeGroupName"] = fncGetMasterValue( "m_group", "strgroupdisplaycode", "strgroupdisplayname",  $_POST['lngInChargeGroupCode'] . ":str",'',$objDB);
			// 担当者
			$aryData["strInChargeUserName"] = fncGetMasterValue( "m_user", "struserdisplaycode" ,"struserdisplayname" , $_POST["lngInChargeUserCode"] . ":str",'',$objDB);
			// 登録日
			$aryData["dtminsertdate"] = $_POST["dtmInsertDate"];
			// 入力者
			$UserDisplayName = "";
			$UserDisplayCode = "";
			$aryData["lngInputUserCode"] = $objAuth->UserID;
			$aryData["strInputUserCode"] = $objAuth->UserDisplayName;


			$_POST["strMonetaryUnitName"] = ( $aryData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryData["lngMonetaryUnitCode"];

			$aryData["strMonetaryUnitName"] = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $_POST["strMonetaryUnitName"] . ":str", '', $objDB );


			//---------------------------------------------
			// 受注状態の取得
			//---------------------------------------------
			//$aryData["strReceiveStatusCodeDisplay"] = fncGetMasterValue( "m_receivestatus", "lngreceivestatuscode", "strreceivestatusname", $_POST["lngReceiveStatusCode"].":str", '', $objDB );


			// レートコード
			$strMonetaryrateName = fncGetMasterValue( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $aryData["lngMonetaryRateCode"], '', $objDB);
			$aryData["strMonetaryrateName"] = ( $strMonetaryrateName == "−" ) ? "" : $strMonetaryrateName;

			$aryData["lngRegistConfirm"] = 0;
			$aryData["curConversionRate_DIS"] = number_format( $aryData["curConversionRate"],6 );	// 換算レート
			$aryData["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
			$aryData["curAllTotalPrice_DIS"] = number_format( $aryData["curAllTotalPrice"],2 );		// 合計金額

			// コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）
			$aryData["lngInputUserCode_DISCODE"] = ( $aryData["lngInputUserCode"] != "" ) ? "[".$aryData["lngInputUserCode"]."]" : "";
			$aryData["lngCustomerCode_DISCODE"] = ( $aryData["lngCustomerCode"] != "" ) ? "[".$aryData["lngCustomerCode"]."]" : "";
			//$aryData["lngInChargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
			//$aryData["lngInChargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";


			//---------------------------------------------
			// 承認ルート
			//---------------------------------------------
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


			$aryData["RENEW"] = TRUE;

			$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

			$aryData["strActionURL"] = "/so/regist/index2.php?strSessionID=".$aryData["strSessionID"];



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


			// テンプレート読み込み
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "so/confirm/parts.tmpl" );

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



	//-------------------------------------------------------------------------
	// ■ 初期表示 -> 初期値設定
	//-------------------------------------------------------------------------
	$aryQuery = array();
	$aryQuery[] = "SELECT  ";
	$aryQuery[] = "lngreceiveno, ";												// 1:受注番号
	$aryQuery[] = "lngrevisionno, ";											// 2:リビジョン番号
	$aryQuery[] = "strreceivecode, ";											// 3:受注コード
	$aryQuery[] = "strrevisecode, ";											// 4:リバイズコード

	$aryQuery[] = "To_char( dtmAppropriationDate, 'YYYY/mm/dd') as dtmOrderAppDate, ";		// 5:計上日

	$aryQuery[] = "lngcustomercompanycode as lngCustomerCode, ";				// 6:会社コード
	//$aryQuery[] = "lnggroupcode as lngInChargeGroupCode, ";						// 7:グループコード
	//$aryQuery[] = "lngusercode as lngInChargeUserCode, ";						// 8:ユーザーコード
	$aryQuery[] = "lngreceivestatuscode as lngReceiveStatusCode, ";				// 9:受注状態コード
	$aryQuery[] = "lngmonetaryunitcode as MonetaryUnitCode, ";					// 10:通貨単位コード
	$aryQuery[] = "lngmonetaryratecode as lngMonetaryRateCode, ";				// 11:通貨レートコード
	$aryQuery[] = "curconversionrate, ";										// 12:換算レート
	$aryQuery[] = "curtotalprice, ";											// 13:合計金額
	$aryQuery[] = "strnote, ";													// 14:備考
	$aryQuery[] = "lnginputusercode, ";											// 15:入力者コード
	$aryQuery[] = "bytinvalidflag, ";											// 16:無効フラグ
	$aryQuery[] = "strcustomerreceivecode ";									// 顧客受注番号
	$aryQuery[] = "FROM ";
	$aryQuery[] = "m_receive ";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "lngreceiveno = " . $aryData["lngReceiveNo"];

	$strQuery = implode( "\n", $aryQuery );

	// クエリー実行
	$objDB->freeResult( $lngResultID );

	if( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}

	$aryData2 = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );


	//var_dump( $aryData2["strcustomerreceivecode"] ); exit();



	//-------------------------------------------------------------------------
	// 受注状態のチェック
	//-------------------------------------------------------------------------
	// 申請中の場合
	if( $aryData2["lngReceiveStatusCode"] == DEF_ORDER_APPLICATE )
	{
		fncOutputError( 404, DEF_WARNING, "", TRUE, "", $objDB );
	}


	//-------------------------------------------------------------------------
	// 状態コードが「 null / "" 」の場合、「0」を再設定
	//-------------------------------------------------------------------------
	$aryData2["lngReceiveStatusCode"] = fncCheckNullStatus( $aryData2["lngReceiveStatusCode"] );


//var_dump( $aryData2["lngReceiveStatusCode"] ); exit();

	$aryData2 = fncChangeData2( $aryData2, $objDB );
	$aryData2["strnote"] = fncHTMLSpecialChars( $aryData2["strnote"] );


	$aryData = array_merge( $aryData, $aryData2 );




	// プルダウンメニューの生成
	// 通貨
	$lngMonetaryUnitCode = fncGetMasterValue("m_monetaryunit", "lngmonetaryunitcode","strmonetaryunitsign", $aryData2["monetaryunitcode"], '', $objDB );
	$aryData["lngMonetaryUnitCode"] 		= fncPulldownMenu( 0, $lngMonetaryUnitCode, '', $objDB );
	// レートタイプ
	$aryData["lngmonetaryratecode"]			= fncPulldownMenu( 1, $aryData2["lngmonetaryratecode"], '', $objDB );
	// 製品単位
	$aryData["lngProductUnitCode_gs"]		= fncPulldownMenu( 7, 0, '', $objDB );
	// 荷姿単位
	$aryData["lngProductUnitCode_ps"]		= fncPulldownMenu( 8, 0, '', $objDB );
	// 売上区分
	$aryData["lngSalesClassCode"]			= fncPulldownMenu( 10, $aryData2["lngsalesclasscode"], '', $objDB );
	// 備考



	// 明細行
	$aryQuery = array();
	$aryQuery[] = "SELECT ";
	$aryQuery[] = "lngreceiveno, ";													// 1:受注番号

	$aryQuery[] = "lngreceivedetailno as lngorderdetailno, ";						// 2:受注明細番号 JavaScript名称の関連でlngOrderDetailNoを使用

	$aryQuery[] = "lngrevisionno, ";												// 3:リビジョン番号
	$aryQuery[] = "strproductcode, ";												// 4:製品コード
	$aryQuery[] = "lngsalesclasscode, ";											// 5:売上区分コード
	$aryQuery[] = "To_char( dtmdeliverydate, 'YYYY/mm/dd' ) as dtmdeliverydate, ";	// 6:納品日
	$aryQuery[] = "lngconversionclasscode, ";										// 7:換算区分コード
	$aryQuery[] = "curproductprice, ";												// 8:製品価格
	$aryQuery[] = "lngproductquantity, ";											// 9:製品数量
	$aryQuery[] = "lngproductunitcode, ";											// 10:製品単位コード
	$aryQuery[] = "cursubtotalprice, ";												// 14:小計金額
	$aryQuery[] = "strnote ";														// 15:備考
	$aryQuery[] = "FROM t_receivedetail";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "lngreceiveno = " . $aryData["lngReceiveNo"];
	$aryQuery[] = " ORDER BY lngSortKey ASC";


	$strQuery = "";
	$strQuery = implode("\n", $aryQuery );

	$objDB->freeResult( $lngResultID );

	if( !$lngResultID = $objDB->execute( $strQuery ) )
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
			$aryQueryResult[] = pg_fetch_array( $lngResultID, $i, PGSQL_ASSOC );
		}
	}



	// 明細行をhidden値に変換する
	$aryData["strDetailHidden"] = fncDetailHidden_so( $aryQueryResult ,"", $objDB);
	$aryData["strProcMode"] = "check";
	$aryData["strSessionID"] = $aryData["strSessionID"];


	$aryData["strActionUrl"] = "renew.php";			// formのaction

	$aryData["curConversionRate"] = "1.000000";



	//---------------------------------------------------------------
	// 受注状態の取得
	//---------------------------------------------------------------
	$aryData["strReceiveStatusCodeDisplay"] = fncGetMasterValue( "m_receivestatus", "lngreceivestatuscode", "strreceivestatusname", $aryData["lngReceiveStatusCode"], '', $objDB );


	// レートタイプ
	$aryData["strMonetaryUnitCodeDis"] = ( is_array( $aryQueryResult ) ) ? "disabled" : "";
	$aryData["RENEW"] = TRUE;


	$aryData["strReceiveCodeDis"] = "disabled";

	$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;

	$aryData["strPageCondition"] = "renew";



	// 承認ルートの取得
	$lngWorkflowOrderCode = fncGetMasterValue( "m_workflow", "strworkflowkeycode", "lngworkflowordercode", $aryData["lngReceiveNo"].":str", '', $objDB );


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
		$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $lngWorkflowOrderCode );
	}




	// 顧客受注番号の取得
	$aryData["strCustomerReceiveCode"] = $aryData["strcustomerreceivecode"];

	$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード

	$objDB->close();

	$objDB->freeResult( $lngResultID );

	// ヘルプ対応
	$aryData["lngFunctionCode"] = DEF_FUNCTION_SO5;

	echo fncGetReplacedHtml( "/so/regist/parts.tmpl", $aryData ,$objAuth );

	return true;

?>
