<?php

// ----------------------------------------------------------------------------
/**
*       売上管理  登録画面
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
*         ・初期登録画面を表示
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
	include( 'conf.inc' );
	require( LIB_FILE );
	require( LIB_ROOT."libcalc.php" );
	require( SRC_ROOT."po/cmn/lib_po.php" );
	//require( SRC_ROOT."pc/cmn/lib_pc.php" );
	require( SRC_ROOT."sc/cmn/lib_sc.php" );
	require( SRC_ROOT."sc/cmn/lib_scp.php" );
	require( LIB_DEBUGFILE );



	//-------------------------------------------------------------------------
	// ■ オブジェクト生成
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();   // DBオブジェクト
	$objAuth = new clsAuth(); // 認証処理オブジェクト


	//-------------------------------------------------------------------------
	// ■ パラメータ取得
	//-------------------------------------------------------------------------
	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];   // セッションID
	$aryData["lngLanguageCode"] = 1; // 言語コード

	$strGetDataMode = $_POST["strGetDataMode"]; // データ引き当てモード
	$strProcMode    = $_POST["strProcMode"];    // 処理モード
	$dtmNowDate     = date( 'Y/m/d', time() );  // 現在日付


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

	$lngUserCode  = $objAuth->UserCode;
	$lngUserGroup = $objAuth->AuthorityGroupCode;


	// 600 売上管理
	if( !fncCheckAuthority( DEF_FUNCTION_SC0, $objAuth ) )
	{
	        fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
	}

	// 601 売上管理（売上登録）
	if( fncCheckAuthority( DEF_FUNCTION_SC1, $objAuth ) )
	{
		$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
	}


	// 610 売上管理（行追加・行削除）
	if( !fncCheckAuthority( DEF_FUNCTION_SC10, $objAuth ) )
	{
		$aryData["adddelrowview"] = 'hidden';
	}




	//-------------------------------------------------------------------------
	// ■ 入力チェック
	//-------------------------------------------------------------------------
	if( $strProcMode == "check")
	{
		// 直登録フラグの取得
		$lngDirectRegistFlag = $_POST["lngDirectRegistFlag"];


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
		list( $aryData, $bytErrorFlag ) = fncCheckData_sc( $aryData, "header", $objDB );

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

		// エラーカウントの取得
		$errorCount = ( $bytErrorFlag != "" ) ? 1 : 0;


		//-----------------------------------------------------------
		// 明細行のチェック
		//-----------------------------------------------------------
		if( count( $_POST["aryPoDitail"] ) > 0 )
		{
			for( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
			{
				list( $aryDetailCheck[], $bytErrorFlag2[] ) = fncCheckData_sc( $_POST["aryPoDitail"][$i], "detail", $objDB );
			}
		}

		for( $i=0; $i < count( $bytErrorFlag2 ); $i++ )
		{
			// 明細行のエラー関数
			if( $bytErrorFlag2[$i] == "true" )
			{
				$aryDetailErrorMessage[] = fncDetailError( $bytErrorFlag2 );
			}
		}

		//-----------------------------------------------------------
		// 受注No.が存在しない場合
		//-----------------------------------------------------------
		if( $aryData["strReceiveCode"] == "" )
		{
			// 日本円に変換
			$lngPrice = $aryData["curAllTotalPrice"] * $aryData["curConversionRate"];

			// 608 売上管理(受注NOを指定しない登録が可能かどうか)
			if( !fncCheckAuthority( DEF_FUNCTION_SC8, $objAuth ) )
			{
				$aryDetailErrorMessage[] = fncOutputError( 607, "", "", FALSE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			else
			{
				// 設定金額が上限以上でその登録可能な権限を持っていないなら
				if( $lngPrice > DEF_MONEY_NO_CODE and !fncCheckAuthority( DEF_FUNCTION_SC9, $objAuth ) )
				{
					$aryDetailErrorMessage[] = fncOutputError( 704, "", "", FALSE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}

/* *v2*
			if( $lngUserGroup == 5 )					// ユーザ
			{
				$aryDetailErrorMessage[] = fncOutputError ( 9060, "", "", FALSE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			elseif( $lngUserGroup == 4 )				// マネージャー
			{
				// 5万まで登録可能
				if( $lngPrice > DEF_MONEY_MANAGER )
				{
					//$strDetailErrorMessage .= "この権限では".DEF_MONEY_MANAGER."円以上のものを登録することができません";
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}
			else										// ディレクター
			{
				// 20万まで登録可能
				if( $lngPrice > DEF_MONEY_DIRECTOR )
				{
					// $strDetailErrorMessage .= "この権限では".DEF_MONEY_DIRECTOR."円以上のものを登録することができません";
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}
*/
		}


		if( !is_array( $_POST["aryPoDitail"] ))
		{
			$aryDetailErrorMessage[] = fncOutputError ( 9001, "", "", FALSE, "", $objDB );
		}


		//-----------------------------------------------------------
		// 受注No.が存在する場合
		//-----------------------------------------------------------
		if( strcmp( $_POST["strReceiveCode"], "") != 0 )
		{
			//---------------------------------------------
			// 売上の金額と数量が、受注の金額と数量を上回っていないかチェック
			//---------------------------------------------
			$aryData["lngReceiveNo"] = $_POST["lngReceiveNo"];

			if( $_POST["strReceiveCode"] == "" )
			{
				//-----------------------------------------
				// DB -> SELECT : m_Receive
				//-----------------------------------------
				$strQuery = "SELECT strReceiveCode FROM m_Receive WHERE lngReceiveNo = " . $aryData["lngReceiveNo"];

				if ( isset( $lngResultID ) )
				{
					$objDB->freeResult( $lngResultID );
				}
				list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
				if ( $lngResultNum )
				{
					$objResult = $objDB->fetchObject( $lngResultID, 0 );
					$strReceiveCode = $objResult->strreceivecode;
				}
				else
				{
					// 発注コード取得失敗
					fncOutputError ( 9051, DEF_ERROR, "受注コード取得失敗", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}

				// 結果IDを解放
				$objDB->freeResult( $lngResultID );
			}
			else
			{
				$strReceiveCode = $_POST["strReceiveCode"];
			}

			//-------------------------------------------------------
			// DB -> SELECT : m_Receive
			//-------------------------------------------------------
			$strQuery = "SELECT lngMonetaryUnitCode FROM m_Receive WHERE lngReceiveNo = " . $aryData["lngReceiveNo"];

			if( isset( $lngResultID ) )
			{
				$objDB->freeResult( $lngResultID );
			}

			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if( $lngResultNum )
			{
				$objResult = $objDB->fetchObject( $lngResultID, 0 );
				$lngReceiveMonetaryUnitCode = $objResult->lngmonetaryunitcode;
			}
			else
			{
				// 通貨レート取得失敗
				fncOutputError ( 9051, DEF_ERROR, "通貨レート取得失敗", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}

			// 結果IDを解放
			$objDB->freeResult( $lngResultID );


			//-------------------------------------------------------
			// DB -> SELECT : t_receivedetail
			//-------------------------------------------------------
			$aryQuery   = array();
			$aryQuery[] = "SELECT ";
			$aryQuery[] = "lngreceiveno, ";									// 1:受注番号
			$aryQuery[] = "lngreceivedetailno as lngOrderDetailNo, ";		// 2:受注明細番号
			$aryQuery[] = "lngrevisionno, ";								// 3:リビジョン番号
			$aryQuery[] = "strproductcode, ";								// 4:製品コード
			$aryQuery[] = "lngsalesclasscode, ";							// 5:売上区分コード
			$aryQuery[] = "dtmdeliverydate, ";								// 6:納品日
			$aryQuery[] = "lngconversionclasscode, ";						// 7:換算区分コード / 1：単位計上/ 2：荷姿単位計上
			$aryQuery[] = "curproductprice, ";								// 8:製品価格
			$aryQuery[] = "lngproductquantity, ";							// 9:製品数量
			$aryQuery[] = "lngproductunitcode, ";							// 10:製品単位コード
			$aryQuery[] = "lngtaxclasscode, ";								// 11:消費税区分コード
			$aryQuery[] = "lngtaxcode, ";									// 12:消費税コード
			$aryQuery[] = "curtaxprice, ";									// 13:消費税金額
			$aryQuery[] = "cursubtotalprice, ";								// 14:小計金額
			$aryQuery[] = "strnote as strdetailnote ";						// 15:備考 
			$aryQuery[] = "FROM t_receivedetail ";
			$aryQuery[] = "WHERE ";
			$aryQuery[] = "lngreceiveno = ".$aryData["lngReceiveNo"];
			$aryQuery[] = " ORDER BY lngSortKey ASC";

			$strQuery = "";
			$strQuery = implode("\n", $aryQuery );

			list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if( $lngResultNum )
			{
				for ( $i = 0; $i < $lngResultNum; $i++ )
				{
					$aryReceiveDetail[] = $objDB->fetchArray( $lngResultID, $i );
				}
			}
			else
			{
				// 明細行が存在しない場合異常データ
				fncOutputError ( 9051, DEF_ERROR, "明細行取得失敗", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}

			// 結果IDを解放
			$objDB->freeResult( $lngResultID );

			// 通貨の値をコードに変更
			if( $_POST["lngMonetaryUnitCode"] != "" )
			{
				$_POST["lngMonetaryUnitCode"] = ( $_POST["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $_POST["lngMonetaryUnitCode"];

				$lngSalesMonetaryUnitCode = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $_POST["lngMonetaryUnitCode"] . ":str", '', $objDB );
			}
			else
			{
				// 発注コード取得失敗
				fncOutputError ( 9061, DEF_ERROR, "通貨単位の取得失敗", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			// 仕入時の端数処理は切捨て
			$lngCalcCode = DEF_CALC_KIRISUTE;

			//---------------------------------------------
			// 受注残以上に売上を設定していないかどうかのチェック
			//---------------------------------------------
			$lngResult = fncGetStatusSalesRemains( $aryData["lngReceiveNo"], $_POST["aryPoDitail"], $lngReceiveMonetaryUnitCode, $lngSalesMonetaryUnitCode, "", $lngCalcCode, $objDB );

			switch( $lngResult )
			{
				// 受注情報の取得失敗
				case 0:
					fncOutputError ( 9061, DEF_ERROR, "指定された受注は削除されました。", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
					break;

				// 受注明細、売上明細情報の取得失敗
				case 1:
					fncOutputError ( 9061, DEF_ERROR, "受注、売上情報の取得に失敗しました。", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
					break;

				// 受注残の以上の仕入が設定されている
				case 99:
					fncOutputError ( 604, DEF_ERROR, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
					break;

				// 設定されている仕入情報は仕入残内
				case 50:
					break;
			}
		}

		// 明細行が存在しない場合
		if( !is_array( $_POST["aryPoDitail"] ))
		{
			$aryDetailErrorMessage[] = fncOutputError ( 9001, "", "", FALSE, "", $objDB );
		}


		//ヘッダ備考の特殊文字変換
		$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );


		//-----------------------------------------------------------
		// 入力エラー
		//-----------------------------------------------------------
		if( is_array( $aryDetailErrorMessage ) || $errorCount != 0 )
		{
			$aryData = fncChangeData3( $aryData, $objDB );

			// プルダウンメニューの生成
			// 通貨
			$aryData["lngMonetaryUnitCode"] 		= fncPulldownMenu( 0, $aryData["lngMonetaryUnitCode"], '', $objDB );
			// レートタイプ
			$aryData["lngMonetaryRateCode"]			= fncPulldownMenu( 1, $aryData["lngMonetaryRateCode"], '', $objDB );
			// 支払条件
			$aryData["lngPayConditionCode"]			= fncPulldownMenu( 2, $aryData["lngPayConditionCode"], '', $objDB );
			// 製品単位
			$aryData["lngProductUnitCode_gs"]		= fncPulldownMenu( 7, 0, '', $objDB );
			// 荷姿単位
			$aryData["lngProductUnitCode_ps"]		= fncPulldownMenu( 8, 0, '', $objDB );
			// 売上区分
			$aryData["lngSalesClassCode"]			= fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );


			// ワークフロー表示・非表示設定
			$aryData["visibleWF"] = "hidden";
			// 承認ルート
			// *v2 ワークフローなし* $aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"] );


			// 明細行をhidden値に変換する
			if(is_array( $_POST["aryPoDitail"] ) ) 
			{
				$aryData["strDetailHidden"] = fncDetailHidden_sc( $_POST["aryPoDitail"] ,"insert", $objDB );
			}
			if( is_array( $aryDetailErrorMessage ) )
			{
				$aryData["strErrorMessage"] = implode(" : ", $aryDetailErrorMessage );
			}

//fncDebug( 'lib_sc.txt', $aryData["strDetailHidden"], __FILE__, __LINE__);

			$aryData["strGetDataMode"]          = "none";
			$aryData["strProcMode"]             = "check";
			$aryData["ReceiveSubmit"]           = 'fncOrderSubmit( document.HSO.strCustomerReceiveCode.value );';
			$aryData["ReceiveSubmit2"]          = 'fncOrderSubmit2( document.HSO.strProductCodeOpen.value );';
			$aryData["strCustomerReceiveDis"]   = '';
			$aryData["strProductCodeOpenDis"]   = '';
			//$aryData["strReceiveCode_Editable"] = "";
			$aryData["lngCalcCode"]             = DEF_CALC_KIRISUTE;
			$aryData["strPageCondition"]        = "regist";


			// CRCリスト
			$aryData["crcflag"] = '0';
			$aryData["crcview"] = 'hidden';


			if( is_array( $_POST["aryPoDitail"] ) )
			{
				$aryData["MonetaryUnitDisabled"] = " disabled";
				$aryData["lngRegistConfirm"]     = 0;
			}

			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード


			// 直登録フラグ
			$aryData["lngDirectRegistFlag"] = $lngDirectRegistFlag;


			// 権限グループコードが「マネージャー」以下の場合
			if( $lngUserGroup >= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				$aryData["lngAuthorityGroupFlag"] = 0;			// 権限グループコードフラグ
				$aryData["blnContentEditable"]    = 'false';	// オブジェクト操作可否
				$aryData["blnBtnEditable"]        = 'return;';	// ボタン操作可否
			}
			else
			{
				$aryData["lngAuthorityGroupFlag"] = 1;			// 権限グループコードフラグ
				$aryData["blnContentEditable"]    = 'true';		// オブジェクト操作可否
				$aryData["blnBtnEditable"]        = '';			// ボタン操作可否
			}



			$objDB->close();
			$objDB->freeResult( $lngResultID );


			echo fncGetReplacedHtml( "sc/regist/parts.tmpl", $aryData ,$objAuth);

			return true;
		}
		//-----------------------------------------------------------
		// 確認画面表示
		//-----------------------------------------------------------
		if( $errorCount == 0 )
		{
			// ワークフロー表示・非表示設定
			$aryData["visibleWF"] = "hidden";

			// プルダウンメニューの生成
			// 通貨
			$aryData["lngMonetaryUnitCode"]   = fncPulldownMenu( 0, $aryData["lngMonetaryUnitCode"], '', $objDB );
			// レートタイプ
			$aryData["lngMonetaryRateCode"]   = fncPulldownMenu( 1, $aryData["lngMonetaryRateCode"], '', $objDB );
			// 支払条件
			$aryData["lngPayConditionCode"]   = fncPulldownMenu( 2, $aryData["lngPayConditionCode"], '', $objDB );
			// 製品単位
			$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
			// 荷姿単位
			$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );
			// 売上区分
			$aryData["lngSalesClassCode"]     = fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );
			// 承認ルート
			$aryData["lngWorkflowOrderCode"]  = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"] );

			// 明細行をhidden値に変換する
			$aryData["strDetailHidden"] = fncDetailHidden_sc( $_POST["aryPoDitail"] ,"insert" ,$objDB );

			$aryData["strBodyOnload"]           = "";
			$aryData["strGetDataMode"]          = "none";
			$aryData["strProcMode"]             = "check";

			$aryData["ReceiveSubmit"]           = 'fncOrderSubmit( document.HSO.strCustomerReceiveCode.value );';
			$aryData["ReceiveSubmit2"]          = 'fncOrderSubmit2( document.HSO.strProductCodeOpen.value );';
			$aryData["strCustomerReceiveDis"]   = '';
			$aryData["strProductCodeOpenDis"]   = '';

			$aryData["MonetaryUnitDisabled"]    = " disabled";
			//$aryData["strReceiveCode_Editable"] = "";
			$aryData["lngCalcCode"]             = DEF_CALC_KIRISUTE;
			$aryData["strPageCondition"]        = "regist";
			$aryData["lngRegistConfirm"]        = 1;


			// CRCリスト
			$aryData["crcflag"] = '0';
			$aryData["crcview"] = 'hidden';


			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード


			// 直登録フラグ
			$aryData["lngDirectRegistFlag"] = $lngDirectRegistFlag;


			// 権限グループコードが「マネージャー」以下の場合
			if( $lngUserGroup >= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				$aryData["lngAuthorityGroupFlag"] = 0;			// 権限グループコードフラグ
				$aryData["blnContentEditable"]    = 'false';	// オブジェクト操作可否
				$aryData["blnBtnEditable"]        = 'return;';	// ボタン操作可否
			}
			else
			{
				$aryData["lngAuthorityGroupFlag"] = 1;			// 権限グループコードフラグ
				$aryData["blnContentEditable"]    = 'true';		// オブジェクト操作可否
				$aryData["blnBtnEditable"]        = '';			// ボタン操作可否
			}


			$objDB->close();


			$aryData["strurl"]       = "/sc/confirm/index.php?strSessionID=".$aryData["strSessionID"];
			$aryData["strActionURL"] = "index.php";


			// テンプレート読み込み
			$objDB->freeResult( $lngResultID );
			echo fncGetReplacedHtml( "/sc/regist/parts.tmpl", $aryData, $objAuth );
			return true;
		}
	}





	//-------------------------------------------------------------------------
	// ■ 受注データの引き当て
	//-------------------------------------------------------------------------
	if( $strProcMode == "onchange" )
	{
		//-----------------------------------------------------------
		// 顧客受注番号から引き当て
		//-----------------------------------------------------------
		if( $strGetDataMode == "customer" )
		{
			$strCustomerReceiveCode = $_POST["strCustomerReceiveCode"];

			//---------------------------------------------
			// DB -> SELECT : m_Receive
			//---------------------------------------------
			$aryQuery   = array();
			$aryQuery[] = "SELECT distinct";
			$aryQuery[] = "	r.lngReceiveNo";
			$aryQuery[] = "	,r.lngRevisionNo";
			$aryQuery[] = "	,r.strCustomerReceiveCode";
			$aryQuery[] = "	,r.strReceiveCode";
			$aryQuery[] = "	,r.strReviseCode";
			$aryQuery[] = "	,r.lngCustomerCompanyCode as lngCustomerCode";
			$aryQuery[] = "	,r.lngReceiveStatusCode as lngSalesStatusCode";
			$aryQuery[] = "	,r.lngMonetaryUnitCode";
			$aryQuery[] = "	,r.lngMonetaryRateCode";
			$aryQuery[] = "	,r.curConversionRate";
			$aryQuery[] = "	,r.strNote";
			$aryQuery[] = "	,r.lngreceivestatuscode";
			$aryQuery[] = "FROM";
			$aryQuery[] = "	m_Receive r ";
			$aryQuery[] = "		LEFT JOIN t_receivedetail tr";
			$aryQuery[] = " 	ON r.lngreceiveno = tr.lngreceiveno";
			$aryQuery[] = "WHERE r.strCustomerReceiveCode = '". $strCustomerReceiveCode . "'";
			$aryQuery[] = " AND ( r.lngreceivestatuscode = 2 or r.lngreceivestatuscode = 3 )";
			$aryQuery[] = " AND ( r.strcustomerreceivecode is not null and r.strcustomerreceivecode != '' )";
			$aryQuery[] = "	AND r.bytInvalidFlag = FALSE";
			$aryQuery[] = "	AND r.lngRevisionNo >= 0";
			$aryQuery[] = "	AND r.lngRevisionNo = (";
			$aryQuery[] = "		SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode";
			$aryQuery[] = "		AND r2.strReviseCode = ( ";
			$aryQuery[] = "		SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode )";
			$aryQuery[] = "	)";
			$aryQuery[] = "	AND 0 <= (";
			$aryQuery[] = "		SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode";
			$aryQuery[] = "	)";
			$aryQuery[] = "ORDER BY r.lngReceiveNo";

			$strQuery = implode( "\n", $aryQuery );

//fncDebug('lib_scp.txt', $strQuery, __FILE__, __LINE__);
		}

		//-----------------------------------------------------------
		// 製品コードから引き当て
		//-----------------------------------------------------------
		if( $strGetDataMode == "product" )
		{
			$strProductCodeOpen = $_POST["strProductCodeOpen"];

			// 同一の顧客受注番号の存在チェック (個数を求める)
			$aryQuery   = array();
			$aryQuery[] = "SELECT DISTINCT";
			$aryQuery[] = "	mr.strcustomerreceivecode";
			$aryQuery[] = "FROM";
			$aryQuery[] = " m_receive mr";
			$aryQuery[] = "		LEFT JOIN t_receivedetail tr";
			$aryQuery[] = " 	ON tr.lngreceiveno = mr.lngreceiveno";
			$aryQuery[] = "WHERE";
			$aryQuery[] = " tr.strproductcode = '" . $strProductCodeOpen . "'";
			$aryQuery[] = " AND mr.bytinvalidflag = false";
			$aryQuery[] = " AND ( mr.lngreceivestatuscode = 2 or mr.lngreceivestatuscode = 3 )";
			$aryQuery[] = " AND ( mr.strcustomerreceivecode is not null and mr.strcustomerreceivecode != '' )";
			$aryQuery[] = " AND mr.lngrevisionno >= 0";
			$aryQuery[] = " AND mr.lngrevisionno = (";
			$aryQuery[] = "  SELECT";
			$aryQuery[] = "   max( mr2.lngrevisionno )";
			$aryQuery[] = "  FROM";
			$aryQuery[] = "   m_receive mr2";
			$aryQuery[] = "  WHERE";
			$aryQuery[] = "   mr2.strReceiveCode = mr.strReceiveCode";
			$aryQuery[] = "   AND mr2.strrevisecode = (";
			$aryQuery[] = "    SELECT";
			$aryQuery[] = "     max( mr3.strrevisecode )";
			$aryQuery[] = "    FROM";
			$aryQuery[] = "     m_receive mr3";
			$aryQuery[] = "    WHERE";
			$aryQuery[] = "     mr3.strReceiveCode = mr2.strReceiveCode";
			$aryQuery[] = "   )";
			$aryQuery[] = " )";
			$aryQuery[] = " AND 0 <= (";
			$aryQuery[] = "  SELECT";
			$aryQuery[] = "   min( mr4.lngrevisionno )";
			$aryQuery[] = "  FROM";
			$aryQuery[] = "   m_receive mr4";
			$aryQuery[] = "  WHERE";
			$aryQuery[] = "   mr4.bytinvalidflag = false";
			$aryQuery[] = "   AND mr4.strReceiveCode = mr.strReceiveCode";
			$aryQuery[] = " )";

			$strQuery = "";
			$strQuery = implode( "\n", $aryQuery );

			// クエリ実行
			list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
			
			if( $lngResultNum )
			{
			//	for( $i = 0; $i < $lngResultNum; $i++ )
			//	{
			//		$objResult[$i] = $objDB->fetchArray( $lngResultID, $i );
			//	}
				//$strCRC = $objResult->strcustomerreceivecode;

				// カウント取得
				$lngCount = (int)$lngResultNum;
			}
			else
			{
				// カウント取得失敗
				fncOutputError( 416, DEF_WARNING, "", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}

//var_dump( $lngCount ); exit();

			//---------------------------------------------
			// 顧客受注番号が１つのみの場合
			//---------------------------------------------
			if( $lngCount == 1 )
			{
				// 顧客受注番号の取得
				$aryQuery   = array();
				$aryQuery[] = "SELECT DISTINCT";
				$aryQuery[] = " mr.strcustomerreceivecode";
				$aryQuery[] = "FROM";
				$aryQuery[] = " m_receive mr";
				$aryQuery[] = "		LEFT JOIN t_receivedetail tr";
				$aryQuery[] = " 	ON tr.lngreceiveno = mr.lngreceiveno";
				$aryQuery[] = "WHERE";
				$aryQuery[] = " tr.strproductcode = '" . $strProductCodeOpen . "'";
				$aryQuery[] = " AND mr.bytinvalidflag = false";
				$aryQuery[] = " AND ( mr.lngreceivestatuscode = 2 or mr.lngreceivestatuscode = 3 )";
				$aryQuery[] = " AND ( mr.strcustomerreceivecode is not null and mr.strcustomerreceivecode != '' )";
				$aryQuery[] = " AND mr.lngrevisionno >= 0";
				$aryQuery[] = " AND mr.lngrevisionno = (";
				$aryQuery[] = "  SELECT";
				$aryQuery[] = "   max( mr2.lngrevisionno )";
				$aryQuery[] = "  FROM";
				$aryQuery[] = "   m_receive mr2";
				$aryQuery[] = "  WHERE";
				$aryQuery[] = "   mr2.strReceiveCode = mr.strReceiveCode";
				$aryQuery[] = "   AND mr2.strrevisecode = (";
				$aryQuery[] = "    SELECT";
				$aryQuery[] = "     max( mr3.strrevisecode )";
				$aryQuery[] = "    FROM";
				$aryQuery[] = "     m_receive mr3";
				$aryQuery[] = "    WHERE";
				$aryQuery[] = "     mr3.strReceiveCode = mr2.strReceiveCode";
				$aryQuery[] = "   )";
				$aryQuery[] = " )";
				$aryQuery[] = " AND 0 <= (";
				$aryQuery[] = "  SELECT";
				$aryQuery[] = "   min( mr4.lngrevisionno )";
				$aryQuery[] = "  FROM";
				$aryQuery[] = "   m_receive mr4";
				$aryQuery[] = "  WHERE";
				$aryQuery[] = "   mr4.bytinvalidflag = false";
				$aryQuery[] = "   AND mr4.strReceiveCode = mr.strReceiveCode";
				$aryQuery[] = " )";

				$strQuery = "";
				$strQuery = implode( "\n", $aryQuery );
//	fncDebug('lib_scp.txt', $strQuery, __FILE__, __LINE__);

				// クエリ実行
				list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

				if( $lngResultNum )
				{
					$objResult = $objDB->fetchObject( $lngResultID, 0 );
					$strCRC = $objResult->strcustomerreceivecode;
				}
				else
				{
					// 顧客受注番号取得失敗
					fncOutputError( 412, DEF_WARNING, "", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}

				// 顧客受注番号取得
				$strCustomerReceiveCode = $strCRC;



				//---------------------------------------------
				// DB -> SELECT : m_Receive
				//---------------------------------------------
				$aryQuery   = array();
				$aryQuery[] = "SELECT distinct";
				$aryQuery[] = "	r.lngReceiveNo";
				$aryQuery[] = "	,r.lngRevisionNo";
				$aryQuery[] = "	,r.strCustomerReceiveCode";
				$aryQuery[] = "	,r.strReceiveCode";
				$aryQuery[] = "	,r.strReviseCode";
				$aryQuery[] = "	,r.lngCustomerCompanyCode as lngCustomerCode";
				$aryQuery[] = "	,r.lngReceiveStatusCode as lngSalesStatusCode";
				$aryQuery[] = "	,r.lngMonetaryUnitCode";
				$aryQuery[] = "	,r.lngMonetaryRateCode";
				$aryQuery[] = "	,r.curConversionRate";
				$aryQuery[] = "	,r.strNote";
				$aryQuery[] = "FROM";
				$aryQuery[] = "	m_Receive r ";
				$aryQuery[] = "		LEFT JOIN t_receivedetail tr";
				$aryQuery[] = " 	ON r.lngreceiveno = tr.lngreceiveno";
				$aryQuery[] = "WHERE r.strCustomerReceiveCode = '". $strCustomerReceiveCode . "'";
				$aryQuery[] = " AND ( r.lngreceivestatuscode = 2 or r.lngreceivestatuscode = 3 )";
				$aryQuery[] = " AND ( r.strcustomerreceivecode is not null and r.strcustomerreceivecode != '' )";
				$aryQuery[] = "	AND r.bytInvalidFlag = FALSE";
				$aryQuery[] = "	AND r.lngRevisionNo >= 0";
				$aryQuery[] = "	AND r.lngRevisionNo = (";
				$aryQuery[] = "		SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode";
				$aryQuery[] = "		AND r2.strReviseCode = ( ";
				$aryQuery[] = "		SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode )";
				$aryQuery[] = "	)";
				$aryQuery[] = "	AND 0 <= (";
				$aryQuery[] = "		SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode";
				$aryQuery[] = "	)";
				$aryQuery[] = "ORDER BY r.lngReceiveNo";

/*
				$aryQuery   = array();
				$aryQuery[] = "SELECT ";
				$aryQuery[] = "r.lngReceiveNo, ";										// 1:受注番号
				$aryQuery[] = "r.lngRevisionNo, ";										// 2:リビジョン番号
				$aryQuery[] = "r.strCustomerReceiveCode, ";								// 顧客受注番号
				$aryQuery[] = "r.strReceiveCode, ";										// 3:受注コード
				$aryQuery[] = "r.strReviseCode, ";										// 4:リバイズコード
				$aryQuery[] = "r.lngCustomerCompanyCode as lngCustomerCode, ";			// 6:顧客
				$aryQuery[] = "r.lngReceiveStatusCode as lngSalesStatusCode, ";			// 9:受注状態コード
				$aryQuery[] = "r.lngMonetaryUnitCode, ";								// 10:通貨単位コード
				$aryQuery[] = "r.lngMonetaryRateCode, ";								// 11:通貨レートコード
				$aryQuery[] = "r.curConversionRate, ";									// 12:換算レート
				$aryQuery[] = "r.strNote ";												// 14:備考
				$aryQuery[] = "FROM m_Receive r ";
				$aryQuery[] = "WHERE r.strCustomerReceiveCode = '". $strCustomerReceiveCode . "' ";
				$aryQuery[] = "AND r.bytInvalidFlag = FALSE ";
				$aryQuery[] = "AND r.lngRevisionNo >= 0 ";
				$aryQuery[] = "AND r.lngRevisionNo = ( ";
				$aryQuery[] = "SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode  ";
				$aryQuery[] = "AND r2.strReviseCode = ( ";
				$aryQuery[] = "SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode ) ) ";
				$aryQuery[] = "AND 0 <= ( ";
				$aryQuery[] = "SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode ) ";
				$aryQuery[] = "ORDER BY r.lngReceiveNo";
*/

				$strQuery = "";
				$strQuery = implode( "\n", $aryQuery );
			}
			//---------------------------------------------
			// 顧客受注番号が複数存在する場合
			//---------------------------------------------
			else if( $lngCount > 1 )
			{
				// 顧客受注番号の取得
				$aryQuery   = array();
				$aryQuery[] = "SELECT";
				$aryQuery[] = " mr.strcustomerreceivecode,";
				$aryQuery[] = " to_char( tr.dtmdeliverydate, 'YYYY/mm/dd' ) as dtmdeliverydate,";
				$aryQuery[] = " tr.lngreceivedetailno";
				$aryQuery[] = "FROM";
				$aryQuery[] = " m_receive mr";
				$aryQuery[] = "LEFT JOIN";
				$aryQuery[] = " t_receivedetail tr";
				$aryQuery[] = " ON mr.lngreceiveno = tr.lngreceiveno";
				$aryQuery[] = "WHERE";
				$aryQuery[] = "mr.lngreceiveno in ( select tr1.lngreceiveno from t_receivedetail tr1 where tr1.strproductcode = '" . $strProductCodeOpen . "' )";
				//$aryQuery[] = " tr.strproductcode = '" . $strProductCodeOpen . "'";
				$aryQuery[] = " AND mr.bytinvalidflag = false";
				$aryQuery[] = " AND ( mr.lngreceivestatuscode = 2 or mr.lngreceivestatuscode = 3 )";
				$aryQuery[] = " AND ( mr.strcustomerreceivecode is not null and mr.strcustomerreceivecode != '' )";
				$aryQuery[] = " AND mr.lngrevisionno >= 0";
				$aryQuery[] = " AND mr.lngrevisionno = (";
				$aryQuery[] = "  SELECT";
				$aryQuery[] = "   max( mr2.lngrevisionno )";
				$aryQuery[] = "  FROM";
				$aryQuery[] = "   m_receive mr2";
				$aryQuery[] = "  WHERE";
				$aryQuery[] = "   mr2.strReceiveCode = mr.strReceiveCode";
				$aryQuery[] = "   AND mr2.strrevisecode = (";
				$aryQuery[] = "    SELECT";
				$aryQuery[] = "     max( mr3.strrevisecode )";
				$aryQuery[] = "    FROM";
				$aryQuery[] = "     m_receive mr3";
				$aryQuery[] = "    WHERE";
				$aryQuery[] = "     mr3.strReceiveCode = mr2.strReceiveCode";
				$aryQuery[] = "   )";
				$aryQuery[] = " )";
				$aryQuery[] = " AND 0 <= (";
				$aryQuery[] = "  SELECT";
				$aryQuery[] = "   min( mr4.lngrevisionno )";
				$aryQuery[] = "  FROM";
				$aryQuery[] = "   m_receive mr4";
				$aryQuery[] = "  WHERE";
				$aryQuery[] = "   mr4.bytinvalidflag = false";
				$aryQuery[] = "   AND mr4.strReceiveCode = mr.strReceiveCode";
				$aryQuery[] = " )";
				$aryQuery[] = "ORDER BY";
				$aryQuery[] = " dtmdeliverydate";

				$strQuery = "";
				$strQuery = implode( "\n", $aryQuery );

				// クエリ実行
				list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

				// データの取得
				for( $i = 0; $i < $lngResultNum; $i++ )
				{
					$aryCRCBase[$i] = $objDB->fetchArray( $lngResultID, $i );
				}


				// 参照データのカウント取得
				$lngDBCnt = count( $aryCRCBase );

				// データ比較用カウンタの初期化
				$lngCreateCnt = 0;


				// データの比較
				for( $i = 0; $i < $lngDBCnt; $i++ )
				{

					$aryCRC[$lngCreateCnt] = $aryCRCBase[$i];

					$blnReplaceFlag = false;
					for( $j = 0; $j < count( $aryCRC ); $j++ )
					{
						if( $aryCRC[$j]["strcustomerreceivecode"] == $aryCRCBase[$i]["strcustomerreceivecode"] )
						{
							// 確認対象の配列が最後では無い（以前の配列に置き換える場合）は置き換えフラグをTrueにする
							if( $j != $lngCreateCnt )
							{
								$blnReplaceFlag = true;
							}

							if( !is_array( $aryCRC[$j]["dtmdeliverydate"] ) )
							{
								$aryCRC[$j]["dtmdeliverydate"] = array();
							}
							else
							{
								$lngCreateCnt--;
							}

							$aryCRC[$j]["dtmdeliverydate"][] = $aryCRCBase[$i]["lngreceivedetailno"] . ". " . $aryCRCBase[$i]["dtmdeliverydate"];

							break;
						}
						
					}


					$lngCreateCnt++;
				}


				// 不要な要素を取り除く
				if( $blnReplaceFlag )
				{
					array_pop( $aryCRC );
				}

//fncDebug( 'index.txt', $aryCRCBase, __FILE__, __LINE__);


				// 納期が複数存在するデータの文字列結合
				for( $i = 0; $i < count( $aryCRC ); $i++ )
				{
					if( is_array( $aryCRC[$i]["dtmdeliverydate"] ) )
					{
						$aryCRC[$i]["dtmdeliverydate"] = implode( "<br>", $aryCRC[$i]["dtmdeliverydate"] );
					}
				}

				// デバッグ
				//fncDebug( 'sc_debug.txt', $aryCRC, __FILE__, __LINE__);


				// 重複している顧客受注番号を表記
				$lngLangCode = $aryData["lngLanguageCode"];
				$aryCRCList  = array();

				$aryCRCList[] = '<table id="CRCTable" width="905" cellpadding="4" cellspacing="1" border="0">';

				if( $lngLangCode == 0 )
				{
					$aryCRCList[] = '<tr id="CRCHeader"><td id="excrctext" colspan="3" align="center">Two or more customer order numbers exist. Please select it from the following.</td></tr>';
					$aryCRCList[] = '<tr id="CRCColumn"><td>&nbsp;</td><td id="excrc" align="center">Customer order No.</td><td id="exdeli" align="center">Delivery date</td></tr>';
				}
				else
				{
					$aryCRCList[] = '<tr id="CRCHeader"><td id="excrctext" colspan="3" align="center">顧客受注番号が複数存在しています。下記より選択してください。</td></tr>';
					$aryCRCList[] = '<tr id="CRCColumn"><td>&nbsp;</td><td id="excrc" align="center">顧客受注番号</td><td id="exdeli" align="center">納期</td></tr>';
				}


				$crc = 1;

				for( $i = 0; $i < count( $aryCRC ); $i++ )
				{
					$aryCRCList[] = '<tr id="CRCData">';
					$aryCRCList[] = '<td align="right">' . $crc . '</td>';
					$aryCRCList[] = '<td><a href="#" onclick="fncOrderSubmit( \'' . $aryCRC[$i]["strcustomerreceivecode"] . '\' );">' . $aryCRC[$i]["strcustomerreceivecode"] . '</a></td>';
					$aryCRCList[] = '<td>' . $aryCRC[$i]["dtmdeliverydate"] . '</td>';
					$aryCRCList[] = '</tr>';

					$crc = $crc + 1;
				}

				$aryCRCList[] = '</table>';

				$strCRCList = "";
				$strCRCList = implode( "\n", $aryCRCList );





				// プルダウンメニューの生成
				// 通貨
				$aryData["lngMonetaryUnitCode"]   = fncPulldownMenu( 0, "\\", '', $objDB );
				// レートタイプ
				$aryData["lngMonetaryRateCode"]   = fncPulldownMenu( 1, 0, '', $objDB );
				// 支払条件
				$aryData["lngPayConditionCode"]   = fncPulldownMenu( 2, 0, '', $objDB );
				// 売上区分
				$aryData["lngSalesClassCode"]     = fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );
				// 製品単位
				$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
				// 荷姿単位
				$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );



				// ワークフロー表示・非表示設定
				$aryData["visibleWF"] = "hidden";
				// 承認ルート
				// *v2 ワークフローなし* $aryData["lngWorkflowOrderCode"]  = fncWorkFlow( $lngUserCode , $objDB , "" );



				$aryData["strGetDataMode"]        = "none";
				$aryData["strProcMode"]           = "check";
				$aryData["curConversionRate"]     = "1.000000";
				$aryData["strSessionID"]          = $aryData[ "strSessionID" ];
				$aryData["ReceiveSubmit"]         = 'fncOrderSubmit( document.HSO.strCustomerReceiveCode.value );';
				$aryData["ReceiveSubmit2"]        = 'fncOrderSubmit2( document.HSO.strProductCodeOpen.value );';
				$aryData["strProductCodeOpen"]    = $strProductCodeOpen;
				$aryData["strCustomerReceiveDis"] = '';
				$aryData["strProductCodeOpenDis"] = '';

				$aryData["lngCalcCode"]           = DEF_CALC_KIRISUTE;
				$aryData["strPageCondition"]      = "regist";
				$aryData["dtmOrderAppDate"]       = $dtmNowDate;
				$aryData["lngRegistConfirm"]      = 0;


				// CRCリスト
				$aryData["crcflag"] = '1';
				$aryData["crcview"] = 'visible';
				$aryData["crclist"] = $strCRCList;


				// 610 売上管理（行追加・行削除）
				if( !fncCheckAuthority( DEF_FUNCTION_SC10, $objAuth ) )
				{
					$aryData["adddelrowview"] = 'hidden';
				}

				$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード


				// 直登録フラグ
				$aryData["lngDirectRegistFlag"] = 1;


				// 権限グループコードが「マネージャー」以下の場合
				if( $lngUserGroup >= DEF_DIRECT_REGIST_AUTHORITY_CODE )
				{
					$aryData["lngAuthorityGroupFlag"] = 0;			// 権限グループコードフラグ
					$aryData["blnContentEditable"]    = 'false';	// オブジェクト操作可否
					$aryData["blnBtnEditable"]        = 'return;';	// ボタン操作可否
				}
				else
				{
					$aryData["lngAuthorityGroupFlag"] = 1;			// 権限グループコードフラグ
					$aryData["blnContentEditable"]    = 'true';		// オブジェクト操作可否
					$aryData["blnBtnEditable"]        = '';			// ボタン操作可否
				}


				$objDB->close();
				$objDB->freeResult( $lngResultID );

				echo fncGetReplacedHtml( "sc/regist/parts.tmpl", $aryData ,$objAuth );

				return true;
			}
			//---------------------------------------------
			// 受注データが存在しない場合
			//---------------------------------------------
			else
			{
				fncOutputError( 403, DEF_WARNING, "(".$strCustomerReceiveCode.")", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}
		}



		//---------------------------------------------------------------------
		// 顧客受注番号から引き当て → データの存在及び、受注状態のチェック
		//---------------------------------------------------------------------
		// 結果IDを解放
		if( isset( $lngResultID ) ) $objDB->freeResult( $lngResultID );

		// クエリ実行
		list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		$aryReceive = array();

		// 指定された受注が存在する場合
		if( $lngResultNum )
		{
			for( $i = 0; $i < $lngResultNum; $i++ )
			{
				// データを取得
				$aryReceive[$i] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		// 指定された受注状態が「申請中・納品済み」か、またはデータが存在しない場合
		else
		{
			//---------------------------------------------
			// DB -> SELECT : m_Receive
			//---------------------------------------------
			$aryQuery   = array();
			$aryQuery[] = "SELECT distinct";
			$aryQuery[] = "	r.lngReceiveNo";
			$aryQuery[] = "	,r.lngRevisionNo";
			$aryQuery[] = "	,r.strCustomerReceiveCode";
			$aryQuery[] = "	,r.strReceiveCode";
			$aryQuery[] = "	,r.strReviseCode";
			$aryQuery[] = "	,r.lngCustomerCompanyCode as lngCustomerCode";
			$aryQuery[] = "	,r.lngReceiveStatusCode as lngSalesStatusCode";
			$aryQuery[] = "	,r.lngMonetaryUnitCode";
			$aryQuery[] = "	,r.lngMonetaryRateCode";
			$aryQuery[] = "	,r.curConversionRate";
			$aryQuery[] = "	,r.strNote";
			$aryQuery[] = "	,r.lngreceivestatuscode";
			$aryQuery[] = "FROM";
			$aryQuery[] = "	m_Receive r ";
			$aryQuery[] = "		LEFT JOIN t_receivedetail tr";
			$aryQuery[] = " 	ON r.lngreceiveno = tr.lngreceiveno";
			$aryQuery[] = "WHERE r.strCustomerReceiveCode = '". $strCustomerReceiveCode . "'";
			//$aryQuery[] = " AND ( r.lngreceivestatuscode = 2 or r.lngreceivestatuscode = 3 )";
			$aryQuery[] = " AND ( r.strcustomerreceivecode is not null and r.strcustomerreceivecode != '' )";
			$aryQuery[] = "	AND r.bytInvalidFlag = FALSE";
			$aryQuery[] = "	AND r.lngRevisionNo >= 0";
			$aryQuery[] = "	AND r.lngRevisionNo = (";
			$aryQuery[] = "		SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode";
			$aryQuery[] = "		AND r2.strReviseCode = ( ";
			$aryQuery[] = "		SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode )";
			$aryQuery[] = "	)";
			$aryQuery[] = "	AND 0 <= (";
			$aryQuery[] = "		SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode";
			$aryQuery[] = "	)";
			$aryQuery[] = "ORDER BY r.lngReceiveNo";

			$strQuery = implode( "\n", $aryQuery );

			// クエリ実行
			list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			// 指定された受注が存在する場合
			if( $lngResultNum )
			{
				// 申請中・納品済
				fncOutputError( 417, DEF_WARNING, "(".$strCustomerReceiveCode.")", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}
			// 該当する受注データが存在しない場合
			else
			{
				fncOutputError( 403, DEF_WARNING, "(".$strCustomerReceiveCode.")", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}
		}

		// 結果IDを解放
		$objDB->freeResult( $lngResultID );


		// デバッグ
//fncDebug( 'lib_scp.txt', $aryReceive, __FILE__, __LINE__);


		//-----------------------------------------------------------
		// 受注状態のチェック・特殊文字変換
		//-----------------------------------------------------------
		for( $i = 0; $i < count($aryReceive); $i++ )
		{
			// 仮受注の場合
			if( $aryReceive[$i]["lngsalesstatuscode"] == DEF_RECEIVE_PREORDER )
			{
				fncOutputError( 410, DEF_WARNING, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			// 申請中の場合
			if( $aryReceive[$i]["lngsalesstatuscode"] == DEF_RECEIVE_APPLICATE )
			{
				fncOutputError( 406, DEF_WARNING, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			// 納品済の場合
			if( $aryReceive[$i]["lngsalesstatuscode"] == DEF_RECEIVE_END )
			{
				//fncOutputError( 415, DEF_WARNING, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			// 否認・申請取り消しの場合
			if( $aryReceive[$i]["lngsalesstatuscode"] == "" || $aryReceive[$i]["lngsalesstatuscode"] == "null" )
			{
				fncOutputError( 414, DEF_WARNING, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			// 備考欄の特殊文字変換
			$aryReceive[$i]["strnote"] =  fncHTMLSpecialChars( $aryReceive[$i]["strnote"] );
		}



		//-----------------------------------------------------------
		// DB -> SELECT : t_receivedetail
		//-----------------------------------------------------------
		$aryReceiveDetail = array();
		// 明細行の取得
		for( $i = 0; $i < count($aryReceive); $i++ )
		{
			$aryQuery   = array();
			$aryQuery[] = "SELECT ";
			$aryQuery[] = "	lngreceiveno";
			$aryQuery[] = "	,lngreceivedetailno as lngorderdetailno";
			$aryQuery[] = "	,lngreceivedetailno as lngreceivedetailno";
			$aryQuery[] = "	,lngrevisionno";
			$aryQuery[] = "	,strproductcode";
			$aryQuery[] = "	,lngsalesclasscode";
			$aryQuery[] = "	,To_char( dtmdeliverydate,'YYYY/mm/dd') as dtmdeliverydate";
			$aryQuery[] = "	,lngconversionclasscode";
			$aryQuery[] = "	,curproductprice";
			$aryQuery[] = "	,lngproductquantity";
			$aryQuery[] = "	,lngproductunitcode";
			$aryQuery[] = "	,lngtaxclasscode";
			$aryQuery[] = "	,lngtaxcode";
			$aryQuery[] = "	,curtaxprice";
			$aryQuery[] = "	,cursubtotalprice";
			$aryQuery[] = "	,strnote as strdetailnote";
			$aryQuery[] = "FROM";
			$aryQuery[] = "	t_receivedetail";
			$aryQuery[] = "WHERE";
			$aryQuery[] = "	lngreceiveno = " . $aryReceive[$i]["lngreceiveno"];
			$aryQuery[] = "ORDER BY lngSortKey ASC";

			$strQuery = "";
			$strQuery = implode( "\n", $aryQuery );

//fncDebug( 'lib_scp.txt', $strQuery, __FILE__, __LINE__);
			// クエリ実行
			list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if( $lngResultNum )
			{
				$aryBuff = array();
				for( $lngTR = 0; $lngTR < $lngResultNum; $lngTR++ )
				{
					// データを取得
					$aryBuff[$lngTR] = $objDB->fetchArray( $lngResultID, $lngTR );
				
					// 納品日項目を表示方式を変更する
					$aryBuff[$lngTR]["dtmdeliverydate"] = str_replace( "-", "/", $aryBuff[$lngTR]["dtmdeliverydate"] );

					// 売上明細番号のインクリメント
					$aryBuff[$lngTR]["lngorderdetailno"] = $lngTR + 1;
				}
				
				$aryReceiveDetail[$i] = $aryBuff;
			}
			else
			{
				fncOutputError( 403, DEF_WARNING, "(".$strCustomerReceiveCode.")", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			// 結果IDを解放
			$objDB->freeResult( $lngResultID );
		}


		// 受注残を求める際の端数処理の算出方法を取得する
		//  ------> 第一段階では 受注、売上==>切捨て処理とする
		$lngCalcMode = DEF_CALC_KIRISUTE;

		// 計上日が設定されている場合は、入力された計上日を引き継ぐ
		if( $_POST["dtmOrderAppDate"] != "" )
		{
			$dtmOrderAppDate = $_POST["dtmOrderAppDate"];
		}
		else
		// 計上日は今日の日付を適用する
		{
			$dtmOrderAppDate = $dtmNowDate;
		}



		//-----------------------------------------------------------
		// 受注から受注残数を求める
		//-----------------------------------------------------------
		$aryRemainsDetail = array();
		for( $i = 0; $i < count($aryReceive); $i++ )
		{
			$aryRemainsDetail[$i] = fncGetSalesRemains( $aryReceive[$i]["lngreceiveno"], "", $lngCalcMode, $objDB );

		}

//fncDebug( 'lib_scp.txt', $aryRemainsDetail, __FILE__, __LINE__);

		// 参照用カウントの初期化
		$lngPlusCnt = 0;
		for( $i = 0; $i < count( $aryRemainsDetail ); $i++ )
		{
			if( $aryRemainsDetail[$i] == 1 )
			{
				fncOutputError( 9051, DEF_ERROR, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}
			else
			{
				//---------------------------------------------
				// 受注残数の計上単位等を受注時の単位にあわせる
				//---------------------------------------------
//fncDebug( 'lib_scp.txt', $aryRemainsDetail[$i], __FILE__, __LINE__);
				$aryRemainsDetail_New = fncSetConversionSalesRemains( $aryRemainsDetail[$i], $aryReceiveDetail[$i], $aryReceive[$i]["lngmonetaryunitcode"], $lngCalcMode, $dtmOrderAppDate, $objDB );
//fncDebug( 'lib_scp.txt', $aryRemainsDetail_New, __FILE__, __LINE__);

				if( $aryRemainsDetail_New == 1 )
				{
					fncOutputError( 9051, DEF_ERROR, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
				}

				// 明細行をhidden値に変換する
				$strBuff .= "\n" . fncDetailHidden_sc( $aryRemainsDetail_New, "detail", $objDB, $lngPlusCnt );

//fncDebug( 'lib_scp.txt', $lngPlusCnt, __FILE__, __LINE__);
			}
		}

		//-----------------------------------------------------------
		// データの移行
		//-----------------------------------------------------------
		$aryData = fncChangeData2( $aryReceive[0] , $objDB );
		$aryData["strDetailHidden"] = $strBuff;



		// 計上日をaryDataに設定
		$aryData["dtmOrderAppDate"] = $dtmOrderAppDate;

		// レートタイプ
		// 通貨が日本以外ならレートタイプを「TTM」にする
		if( $aryData["lngmonetaryunitcode"] != 1 )
		{
			$aryData["lngmonetaryratecode"] = fncPulldownMenu( 1, 2, '', $objDB );
//			$aryData["lngmonetaryratecode"] = fncPulldownMenu( 1, 1, '', $objDB );
		}
		else
		{
			$aryData["lngmonetaryratecode"] = fncPulldownMenu( 1, $aryData["lngmonetaryratecode"], '', $objDB );
		}

		// プルダウンメニューの生成
		// 通貨
		$lngMonetaryUnit                  = fncGetMasterValue( "m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitsign", $aryData["lngmonetaryunitcode"],'', $objDB );
		$aryData["lngmonetaryunitcode"]   = fncPulldownMenu( 0, $lngMonetaryUnit, '', $objDB );
		// 状態
		$strSalesStatus = fncGetMasterValue( "m_salesstatus", "lngsalesstatuscode", "strsalesstatusname", $aryData["lngsalesstatuscode"],'', $objDB );
		$aryData["strsalsestatus_dis"]    = $strSalesStatus;
		// 製品単位
		$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
		// 荷姿単位
		$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );
		// 売上区分
		$aryData["lngSalesClassCode"]     = fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );



		//---------------------------------------------------------------
		// 売上状態の取得
		//---------------------------------------------------------------
		if( strcmp( $aryData["lngsalesstatuscode"], "" ) != 0 )
		{
			$strSalseStatus = fncGetMasterValue( "m_salesstatus", "lngsalesstatuscode", "strsalesstatusname", $aryData["lngsalesstatuscode"],'', $objDB );
			$aryData["lngSalseStatusCode_Display"] = $strSalseStatus;
			$aryData["lngSalseStatusCode"]         = $aryData["lngsalesstatuscode"];
		}



		// 承認ルートの取得
		$lngWorkflowOrderCode = fncGetMasterValue( "m_workflow", "strworkflowkeycode", "lngworkflowordercode", $aryData["lngreceiveno"].":str", '', $objDB );
		// 承認ルート
		// *v2 ワークフローなし* $aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $lngWorkflowOrderCode );


		// ワークフロー表示・非表示設定
		$aryData["visibleWF"] = "hidden";


		// 計上日が設定されている場合は、入力された計上日を引き継ぐ
		if( $_POST["dtmOrderAppDate"] != "" )
		{
			$aryData["dtmOrderAppDate"] = $_POST["dtmOrderAppDate"];
		}
		else
		// 計上日は今日の日付を適用する
		{
			$aryData["dtmOrderAppDate"] = $dtmNowDate;
		}


		// 製品コードを再設定
		// 製品コードから引き当ての場合
		if( $strGetDataMode == "product" )
		{
			$aryData["strProductCodeOpen"] = $strProductCodeOpen;
		}
		// 顧客受注番号から引き当ての場合
		else
		{
			$aryData["strProductCodeOpen"] = $aryReceiveDetail[0]["strproductcode"];
		}


		$aryData["strGetDataMode"]          = "none";
		$aryData["strProcMode"]             = "check";
		$aryData["ReceiveSubmit"]           = 'fncOrderSubmit( document.HSO.strCustomerReceiveCode.value );';
		$aryData["ReceiveSubmit2"]          = 'fncOrderSubmit2( document.HSO.strProductCodeOpen.value );';
		$aryData["strCustomerReceiveDis"]   = '';
		$aryData["strProductCodeOpenDis"]   = '';
		$aryData["MonetaryUnitDisabled"]    = " disabled";
		$aryData["strSessionID"]            = $_POST["strSessionID"];
		$aryData["lngRegistConfirm"]        = 0;
		$aryData["lngCalcCode"]             = DEF_CALC_KIRISUTE;
		$aryData["strPageCondition"]        = "regist";


		$aryData["crcflag"] = '0';
		$aryData["crcview"] = 'hidden';


		// 610 売上管理（行追加・行削除）
		if( !fncCheckAuthority( DEF_FUNCTION_SC10, $objAuth ) )
		{
			$aryData["adddelrowview"] = 'hidden';
		}


		$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード


		// 直登録フラグ
		$aryData["lngDirectRegistFlag"] = 0;


		// 権限グループコードが「マネージャー」以下の場合
		if( $lngUserGroup >= DEF_DIRECT_REGIST_AUTHORITY_CODE )
		{
			$aryData["lngAuthorityGroupFlag"] = 0;			// 権限グループコードフラグ
			$aryData["blnContentEditable"]    = 'false';	// オブジェクト操作可否
			$aryData["blnBtnEditable"]        = 'return;';	// ボタン操作可否
		}
		else
		{
			$aryData["lngAuthorityGroupFlag"] = 1;			// 権限グループコードフラグ
			$aryData["blnContentEditable"]    = 'true';		// オブジェクト操作可否
			$aryData["blnBtnEditable"]        = '';			// ボタン操作可否
		}


		$objDB->close();
		$objDB->freeResult( $lngResultID );

		//require( LIB_DEBUGFILE );
		//fncDebug( 'lib_sc.txt', $aryData, __FILE__, __LINE__);



		echo fncGetReplacedHtml( "sc/regist/parts.tmpl", $aryData ,$objAuth );

		return true;
	}






	//-------------------------------------------------------------------------
	// ■ 初期表示 -> 初期値設定
	//-------------------------------------------------------------------------
	// プルダウンメニューの生成
	// 通貨
	$aryData["lngMonetaryUnitCode"]   = fncPulldownMenu( 0, "\\", '', $objDB );
	// レートタイプ
	$aryData["lngMonetaryRateCode"]   = fncPulldownMenu( 1, 0, '', $objDB );
	// 支払条件
	$aryData["lngPayConditionCode"]   = fncPulldownMenu( 2, 0, '', $objDB );
	// 売上区分
	$aryData["lngSalesClassCode"]     = fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );
	// 製品単位
	$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
	// 荷姿単位
	$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );



	// ワークフロー表示・非表示設定
	$aryData["visibleWF"] = "hidden";
	// 承認ルート
	// *v2 ワークフローなし* $aryData["lngWorkflowOrderCode"]  = fncWorkFlow( $lngUserCode , $objDB , "" );



	$aryData["strGetDataMode"]              = "none";
	$aryData["strProcMode"]                 = "check";
	$aryData["curConversionRate"]           = "1.000000";
	$aryData["strSessionID"]                = $aryData[ "strSessionID" ];
	$aryData["ReceiveSubmit"]               = 'fncOrderSubmit( document.HSO.strCustomerReceiveCode.value );';
	$aryData["ReceiveSubmit2"]              = 'fncOrderSubmit2( document.HSO.strProductCodeOpen.value );';
	$aryData["strCustomerReceiveDis"]       = '';
	$aryData["strProductCodeOpenDis"]       = '';
	//$aryData["strReceiveCode_Editable"]     = '';

	$aryData["lngCalcCode"]                 = DEF_CALC_KIRISUTE;
	$aryData["strPageCondition"]            = "regist";
	$aryData["dtmOrderAppDate"]             = $dtmNowDate;
	$aryData["lngRegistConfirm"]            = 0;


	// CRCリスト
	$aryData["crcflag"] = '0';
	$aryData["crcview"] = 'hidden';


	$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード


	// 直登録フラグ
	$aryData["lngDirectRegistFlag"] = 1;



	// 権限グループコードが「マネージャー」以下の場合
	if( $lngUserGroup >= DEF_DIRECT_REGIST_AUTHORITY_CODE )
	{
		$aryData["lngAuthorityGroupFlag"] = 0;			// 権限グループコードフラグ
		$aryData["blnContentEditable"]    = 'false';	// オブジェクト操作可否
		$aryData["blnBtnEditable"]        = 'return;';	// ボタン操作可否
	}
	else
	{
		$aryData["lngAuthorityGroupFlag"] = 1;			// 権限グループコードフラグ
		$aryData["blnContentEditable"]    = 'true';		// オブジェクト操作可否
		$aryData["blnBtnEditable"]        = '';			// ボタン操作可否
	}



	$objDB->close();
	$objDB->freeResult( $lngResultID );


	echo fncGetReplacedHtml( "sc/regist/parts.tmpl", $aryData ,$objAuth);

	return true;

?>
