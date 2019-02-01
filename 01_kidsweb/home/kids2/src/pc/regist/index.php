<?php

// ----------------------------------------------------------------------------
/**
*       仕入管理  登録画面
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
	require( LIB_DEBUGFILE );
	require( LIB_ROOT."libcalc.php" );
	require( SRC_ROOT."po/cmn/lib_po.php" );
	require( SRC_ROOT."pc/cmn/lib_pc.php" );
	require( SRC_ROOT."pc/cmn/lib_pcp.php" );


	//-------------------------------------------------------------------------
	// ■ オブジェクト生成
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();   // DBオブジェクト
	$objAuth = new clsAuth(); // 認証処理オブジェクト


	//-------------------------------------------------------------------------
	// ■ パラメータ取得
	//-------------------------------------------------------------------------
	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];   // セッションID
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"]; // 言語コード

	$dtmNowDate = date( 'Y/m/d', time() );  // 現在日付



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


	// 700 仕入管理
	if( !fncCheckAuthority( DEF_FUNCTION_PC0, $objAuth ) )
	{
	    fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
	}

	// 701 仕入管理（ 仕入登録）
	if( fncCheckAuthority( DEF_FUNCTION_PC1, $objAuth ) )
	{
		$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
	}


	// 710 仕入管理（行追加・行削除）
	if( !fncCheckAuthority( DEF_FUNCTION_PC10, $objAuth ) )
	{
		$aryData["adddelrowview"] = 'hidden';
	}




	//-------------------------------------------------------------------------
	// ■ 入力チェック
	//-------------------------------------------------------------------------
	if( $_POST["strProcMode"] == "check" && $_POST["strMode"] == "regist")
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


		//-------------------------------------------------
		// ユーザの権限ごとに登録できる金額をチェック
		//-------------------------------------------------
		$aryData["strOrderCode"] = trim( $aryData["strOrderCode"] );

		if( $aryData["strOrderCode"] == "" || $aryData["strOrderCode"] == "null" )
		{
			// 日本円に変換
			$lngPrice = $aryData["curAllTotalPrice"] * $aryData["curConversionRate"];


			// 708 仕入管理（受注NOを指定しない登録が可能かどうか）
			if ( !fncCheckAuthority( DEF_FUNCTION_PC8, $objAuth ) )
			{
				$aryDetailErrorMessage[] = fncOutputError( 710, "", "", FALSE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			else
			{
				// 設定金額が上限以上でその登録可能な権限を持っていないなら
				if ( $lngPrice > DEF_MONEY_NO_CODE and !fncCheckAuthority( DEF_FUNCTION_PC9, $objAuth ) )
				{
					$aryDetailErrorMessage[] = fncOutputError( 704, "", "", FALSE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}


/*			if( $lngUserGroup == 5 )					// ユーザ
			{
				$aryDetailErrorMessage[] = fncOutputError ( 710, "", "", FALSE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			elseif( $lngUserGroup == 4 )				// マネージャー
			{
				// 5万まで登録可能
				if( $lngPrice > DEF_MONEY_MANAGER )
				{
					//$strDetailErrorMessage .= "この権限では".DEF_MONEY_MANAGER."円以上のものを登録することができません";
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}
			else										// ディレクター
			{
				// 20万まで登録可能
				if( $lngPrice > DEF_MONEY_DIRECTOR )
				{
					// $strDetailErrorMessage .= "この権限では".DEF_MONEY_DIRECTOR."円以上のものを登録することができません";
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}

			}
*/
		}


		//-----------------------------------------------------------
		// ヘッダー項目チェック
		//-----------------------------------------------------------
		list ( $aryData, $bytErrorFlag ) = fncCheckData_pc( $aryData,"header", $objDB );

		$errorCount = ( $bytErrorFlag != "") ? 1 : 0;

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



		//-----------------------------------------------------------
		// 明細行のチェック
		//-----------------------------------------------------------
		if(  count( $_POST["aryPoDitail"] ) > 0 )
		{
			for( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
			{
				list ( $aryDetailCheck[], $bytErrorFlag2[] ) = fncCheckData_pc( $_POST["aryPoDitail"][$i], "detail", $objDB );
			}

			for( $i = 0; $i < count( $bytErrorFlag2 ); $i++ )
			{
				// 明細行のエラー関数
				if( $bytErrorFlag2[$i] == "true")
				{
					$aryDetailErrorMessage[] = fncDetailError( $bytErrorFlag2 );
				}
			}

			$errorCount_detail = ( is_array( $aryDetailErrorMessage ) ) ? 1 : 0;

		}
		else
		{
			if( !is_array( $_POST["aryPoDitail"] ))
			{
				$aryDetailErrorMessage[] = fncOutputError ( 9001, "", "", FALSE, "", $objDB );
			}

			// ↓不明？？？
			$errorCount_detail = 1;
		}

		//-------------------------------------------------
		// 仕入の金額と数量が発注の金額と数量を上回っていないかチェック
		// 仕入れの情報を取得
		//-------------------------------------------------
		if( strcmp( $_POST["lngOrderNo"],"") != 0 && is_array( $_POST["aryPoDitail"] ) )
		{
			$aryData["lngOrderNo"] = $_POST["lngOrderNo"];

			if ( $_POST["strOrderCode"] == "" )
			{
				$strQuery = "SELECT strOrderCode FROM m_Order WHERE lngOrderNo = " . $aryData["lngOrderNo"];

				if ( isset( $lngResultID ) )
				{
					$objDB->freeResult( $lngResultID );
				}
				list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
				if ( $lngResultNum )
				{
					$objResult = $objDB->fetchObject( $lngResultID, 0 );
					$strOrderCode = $objResult->strordercode;
				}
				else
				{
					// 発注コード取得失敗
					fncOutputError ( 9051, DEF_ERROR, "発注コード取得失敗", TRUE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}

				$objDB->freeResult( $lngResultID );
			}
			else
			{
				$strOrderCode = $_POST["strOrderCode"];
			}

			$strQuery = "SELECT lngMonetaryUnitCode FROM m_Order WHERE lngOrderNo = " . $aryData["lngOrderNo"];

			if ( isset( $lngResultID ) )
			{
				$objDB->freeResult( $lngResultID );
			}

			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if ( $lngResultNum )
			{
				$objResult = $objDB->fetchObject( $lngResultID, 0 );
				$lngOrderMonetaryUnitCode = $objResult->lngmonetaryunitcode;
			}
			else
			{
				// 通貨レート取得失敗
				fncOutputError ( 9051, DEF_ERROR, "通貨レートコード取得失敗", TRUE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}

			$objDB->freeResult( $lngResultID );



			//-------------------------------------------------------
			// DB -> SELECT : t_orderdetail
			//-------------------------------------------------------
			$aryQuery   = array();
			$aryQuery[] = "SELECT ";
			$aryQuery[] = "lngorderdetailno, ";								// 発注明細番号
			$aryQuery[] = "lngrevisionno, ";								// リビジョン番号
			$aryQuery[] = "strproductcode, ";								// 製品コード
			$aryQuery[] = "lngstocksubjectcode, ";							// 仕入科目コード
			$aryQuery[] = "lngstockitemcode, ";								// 仕入部品コード
			$aryQuery[] = "dtmdeliverydate, ";								// 納品日
			$aryQuery[] = "lngdeliverymethodcode as lngCarrierCode, ";		// 運搬方法コード
			$aryQuery[] = "lngconversionclasscode, ";						// 換算区分コード / 1：単位計上/ 2：荷姿単位計上
			$aryQuery[] = "curproductprice, ";								// 製品価格
			$aryQuery[] = "lngproductquantity, ";							// 製品数量
			$aryQuery[] = "lngproductunitcode, ";							// 製品単位コード
			$aryQuery[] = "lngtaxclasscode, ";								// 消費税区分コード
			$aryQuery[] = "lngtaxcode, ";									// 消費税コード
			$aryQuery[] = "curtaxprice, ";									// 消費税金額
			$aryQuery[] = "cursubtotalprice, ";								// 小計金額
			$aryQuery[] = "strnote ";										// 備考
			$aryQuery[] = "FROM t_orderdetail ";
			$aryQuery[] = "WHERE ";
			$aryQuery[] = "lngorderno = ".$aryData["lngOrderNo"];
			$aryQuery[] = " ORDER BY lngSortKey";

			$strQuery = "";
			$strQuery = implode("\n", $aryQuery );


			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if ( $lngResultNum )
			{
				for ( $i = 0; $i < $lngResultNum; $i++ )
				{
					$aryOrderDetail[] = $objDB->fetchArray( $lngResultID, $i );
				}
			}
			else
			{
				// 明細行が存在しない場合異常データ
				fncOutputError ( 9051, DEF_ERROR, "明細行取得失敗", TRUE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			$objDB->freeResult( $lngResultID );

			// 通貨の値をコードに変更
			if ( $_POST["lngMonetaryUnitCode"] != "" )
			{
				 $_POST["lngMonetaryUnitCode"] = ( $_POST["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $_POST["lngMonetaryUnitCode"];
				$lngStockMonetaryUnitCode = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $_POST["lngMonetaryUnitCode"] . ":str", '', $objDB );
			}
			else
			{
				// 発注コード取得失敗
				fncOutputError ( 9061, DEF_ERROR, "通貨単位の取得失敗", TRUE, "pc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			// 仕入時の端数処理は切捨て
			$lngCalcCode = DEF_CALC_KIRISUTE;

			//---------------------------------------------
			// 仕入残以上に仕入を設定していないかどうかのチェック関数呼び出し
			//---------------------------------------------
			$lngResult = fncGetStatusStockRemains ( $aryData["lngOrderNo"], $_POST["aryPoDitail"], $lngOrderMonetaryUnitCode, $lngStockMonetaryUnitCode, "", $lngCalcCode, $objDB );

			switch( $lngResult )
			{
				// 発注情報の取得失敗
				case 0:
					fncOutputError ( 9061, DEF_ERROR, "指定された発注は削除されました。", TRUE, "pc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
					break;

				// 発注明細、仕入明細情報の取得失敗
				case 1:
					fncOutputError ( 9061, DEF_ERROR, "発注、仕入情報の取得に失敗しました。", TRUE, "pc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
					break;

				// 仕入残の以上の仕入が設定されている
				case 99:
					fncOutputError ( 705, DEF_ERROR, "", TRUE, "pc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
					break;

				// 設定されている仕入情報は仕入残内
				case 50:
					break;
			}
		}

		// 関数 fncChangeDisplayNameで表示用データに変換(HEADER）
		$aryData = fncChangeData3( $aryData , $objDB );


		// 明細行をhidden値に変換する
		if( is_array( $_POST["aryPoDitail"] ) )
		{
			$aryData["strDetailHidden"]      = fncDetailHidden_pc( $_POST["aryPoDitail"] ,"insert", $objDB);
			$aryData["MonetaryUnitDisabled"] = "disabled";
		}


		// プルダウンメニューの生成
		// 通貨
		$aryData["lngMonetaryUnitCode"]   = fncPulldownMenu( 0, $aryData["lngMonetaryUnitCode"], '', $objDB );
		// レートタイプ
		$aryData["lngMonetaryRateCode"]   = fncPulldownMenu( 1, $aryData["lngMonetaryRateCode"], '', $objDB );
		// 支払条件
		$aryData["lngPayConditionCode"]   = fncPulldownMenu( 2, $aryData["lngPayConditionCode"], '', $objDB );
		// 仕入科目
		$aryData["strStockSubjectCode"]   = fncPulldownMenu( 3, $aryData["strStockSubjectCode"], '', $objDB );
		// 運搬方法
		$aryData["lngCarrierCode"]        = fncPulldownMenu( 6, 0, '', $objDB );
		// 製品単位
		$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
		// 荷姿単位
		$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );



		// ワークフロー表示・非表示設定
		$aryData["visibleWF"] = "hidden";
		// 承認ルート
		// *v2 ワークフローなし* $aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"] );



		$aryData["lngOrderStatusCode_Display"] = ( strcmp( $aryData["lngOrderStatusCode"], "" ) != 0 ) ? fncGetMasterValue("m_orderstatus", "lngorderstatuscode", "strorderstatusname", $aryData["lngOrderStatusCode"],'', $objDB ) : "";



		//ヘッダ備考の特殊文字変換
		$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );


		//-----------------------------------------------------------
		// 入力エラー
		//-----------------------------------------------------------
		if( is_array( $aryDetailErrorMessage ) || $errorCount != 0)
		{
			if(is_array( $aryDetailErrorMessage ) )
			{
				$aryData["strErrorMessage"] = implode( " : ", $aryDetailErrorMessage);
			}


			$aryData["strProcMode"]           = "check";
			$aryData["lngOrderCode"]          = $_GET["strOrderCode"];
			$aryData["OrderSubmit"]           = "fncOrderSubmit();";
			$aryData["strOrderCode_Editable"] = "";
			$aryData["strSessionID"]          = $aryData["strSessionID"];
			$aryData["lngCalcCode"]           = DEF_CALC_KIRISUTE;
			$aryData["strPageCondition"]      = "regist";
			$aryData["lngRegistConfirm"]      = 0;

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

			echo fncGetReplacedHtml( "pc/regist/parts.tmpl", $aryData ,$objAuth);

			return true;
		}

		//-----------------------------------------------------------
		// 確認画面表示
		//-----------------------------------------------------------
		if( $errorCount == 0  )
		{
			$aryData["strBodyOnload"]         = "";
			$aryData["strMode"]               = "regist";
			$aryData["strProcMode"]           = "check";
			$aryData["strurl"]                = "/pc/confirm/index.php?strSessionID=".$aryData["strSessionID"];
			$aryData["strActionURL"]          = "index.php";
			$aryData["strOrderCode_Editable"] = "";
			$aryData["OrderSubmit"]           = "fncOrderSubmit();";
			$aryData["lngCalcCode"]           = DEF_CALC_KIRISUTE;
			$aryData["strPageCondition"]      = "regist";
			$aryData["lngRegistConfirm"]      = 1;


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

			echo fncGetReplacedHtml( "/pc/regist/parts.tmpl", $aryData, $objAuth );
			return true;
		}
	}





	//-------------------------------------------------------------------------
	// ■ 発注データの引き当て
	//-------------------------------------------------------------------------
	if( strcmp( $_POST["strOrderCode"],"") != 0 && $_POST["strMode"] == "onchange" )
	{

		$aryData["strOrderCode"] = $_POST["strOrderCode"];


		$aryQuery = array();
		$aryQuery[] = "SELECT ";
		$aryQuery[] = "lngOrderNo, ";									// オーダ番号
		$aryQuery[] = "lngRevisionNo, ";								// リビジョン番号
		$aryQuery[] = "( select min(lngrevisionno) from m_order where strordercode = '" . $aryData["strOrderCode"] . "' "
			. "and bytinvalidflag = false ) as lngRevisionMin, ";
		$aryQuery[] = "strOrderCode, ";									// 発注コード
		$aryQuery[] = "strrevisecode as strReviseCode, ";				// リバイズコード
		$aryQuery[] = "lngcustomercompanycode as lngCustomerCode, ";	// 会社コード（仕入先）
		//$aryQuery[] = "lnggroupcode as lngInChargeGroupCode, ";			// 部門
		//$aryQuery[] = "lngusercode as lngInChargeUserCode, ";			// 担当者
		$aryQuery[] = "lngorderstatuscode as lngOrderStatusCode, ";		// 発注状態コード
		$aryQuery[] = "lngMonetaryUnitCode, ";							// 通貨単位コード
		$aryQuery[] = "lngMonetaryrateCode, ";							// 通貨レートコード
		$aryQuery[] = "curConversionRate, ";							// 換算レート
		$aryQuery[] = "lngPayconditionCode, ";							// 支払条件コード
		$aryQuery[] = "curTotalPrice, ";								// 合計金額
		$aryQuery[] = "lngdeliveryplacecode as lngLocationCode, ";		// 納品場所コード / 会社コード
		$aryQuery[] = "To_char( dtmexpirationdate, 'YYYY/mm/dd' ) as dtmexdate, ";	// 製品到着日;
		$aryQuery[] = "strNote ";										// 備考
		$aryQuery[] = "FROM m_order ";
		$aryQuery[] = "WHERE ";
		$aryQuery[] = "strordercode = '".$aryData["strOrderCode"]."' AND ";	// オーダコード
		$aryQuery[] = "bytinvalidflag = false AND ";					// 無効フラグ

		$aryQuery[] = "lngrevisionno = ";
		$aryQuery[] = "( select max(lngrevisionno) from m_order where strordercode = '".$aryData["strOrderCode"]."') AND ";
		$aryQuery[] = "0 <= ( SELECT MIN( o2.lngrevisionno ) FROM m_order o2 WHERE o2.bytinvalidflag = false AND o2.strordercode = '".$aryData["strOrderCode"]."')";


		$strQuery = implode("\n", $aryQuery );

		if ( isset( $lngResultID ) )
		{
			$objDB->freeResult( $lngResultID );
		}

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if ( $lngResultNum == 1 )
		{
			$objResult = $objDB->fetchObject( $lngResultID, 0 );
			$aryOrderResult["lngorderno"] 			= $objResult->lngorderno;			// オーダ番号
			$aryOrderResult["lngrevisionno"] 		= $objResult->lngrevisionno;		// リビジョン番号
			$aryOrderResult["lngrevisionmin"] 		= $objResult->lngrevisionmin;		// 最小リビジョン番号？？？
			$aryOrderResult["strordercode"] 		= $objResult->strordercode;			// 発注コード
			$aryOrderResult["strrevisecode"] 		= $objResult->strrevisecode;		// リバイズコード
			$aryOrderResult["lngcustomercode"] 		= $objResult->lngcustomercode;		// 仕入先コード
			//$aryOrderResult["lnginchargegroupcode"] = $objResult->lnginchargegroupcode;	// 部門コード
			//$aryOrderResult["lnginchargeusercode"] 	= $objResult->lnginchargeusercode;	// 担当者コード
			$aryOrderResult["lngorderstatuscode"] 	= $objResult->lngorderstatuscode;	// 発注状態コード
			$aryOrderResult["lngmonetaryunitcode"] 	= $objResult->lngmonetaryunitcode;	// 通貨単位コード
			$aryOrderResult["lngmonetaryratecode"] 	= $objResult->lngmonetaryratecode;	// 通貨レートコード
			$aryOrderResult["curconversionrate"] 	= $objResult->curconversionrate;	// 換算レート
			$aryOrderResult["lngpayconditioncode"] 	= $objResult->lngpayconditioncode;	// 支払条件コード
			$aryOrderResult["curtotalprice"] 		= $objResult->curtotalprice;		// 合計金額
			$aryOrderResult["lnglocationcode"] 		= $objResult->lnglocationcode;		// 納品場所コード / 会社コード
			$aryOrderResult["dtmexpirationdate"] 	= $objResult->dtmexdate;			// 製品到着日
			$aryOrderResult["strnote"] 				= $objResult->strnote;				// 備考
		}
		// 指定された発注が存在しない場合
		else
		{
			fncOutputError ( 503, DEF_WARNING, "", TRUE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
		}

		$objDB->freeResult( $lngResultID );




		//-----------------------------------------------------------
		// 発注状態のチェック
		//-----------------------------------------------------------
		// 申請中
		if( $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_APPLICATE )
		{
			fncOutputError( 504, DEF_WARNING, "", TRUE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
		}

		// 納品済み
		if( $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_END )
		{
			fncOutputError( 507, DEF_WARNING, "", TRUE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
		}

		// 否認・申請取り消し
		if( $aryOrderResult["lngorderstatuscode"] == "" || $aryOrderResult["lngorderstatuscode"] == "null" )
		{
			fncOutputError( 508, DEF_WARNING, "", TRUE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
		}




		// 明細行
		$aryQuery = array();
		$aryQuery[] = "SELECT ";
		$aryQuery[] = "lngorderdetailno, ";								// 発注明細番号
		$aryQuery[] = "lngrevisionno, ";								// リビジョン番号
		$aryQuery[] = "strproductcode, ";								// 製品コード
		$aryQuery[] = "lngstocksubjectcode, ";							// 仕入科目コード
		$aryQuery[] = "lngstockitemcode, ";								// 仕入部品コード
		$aryQuery[] = "To_char( dtmdeliverydate, 'YYYY/mm/dd' ) as dtmdeliverydate, ";	// 納品日
		$aryQuery[] = "lngdeliverymethodcode as lngCarrierCode, ";		// 運搬方法コード
		$aryQuery[] = "lngconversionclasscode, ";						// 換算区分コード / 1：単位計上/ 2：荷姿単位計上
		$aryQuery[] = "curproductprice, ";								// 製品価格
		$aryQuery[] = "lngproductquantity, ";							// 製品数量
		$aryQuery[] = "lngproductunitcode, ";							// 製品単位コード
		$aryQuery[] = "lngtaxclasscode, ";								// 消費税区分コード
		$aryQuery[] = "lngtaxcode, ";									// 消費税コード
		$aryQuery[] = "curtaxprice, ";									// 消費税金額
		$aryQuery[] = "cursubtotalprice, ";								// 小計金額
		$aryQuery[] = "strnote, ";										// 備考
		$aryQuery[] = "strmoldno as strSerialNo ";						// シリアル
		$aryQuery[] = "FROM t_orderdetail ";
		$aryQuery[] = "WHERE ";
		$aryQuery[] = "lngorderno = ".$aryOrderResult["lngorderno"];
		$aryQuery[] = " ORDER BY lngSortKey";

		$strQuery = "";
		$strQuery = implode("\n", $aryQuery );



		list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if( $lngResultNum )
		{
			for( $i = 0; $i < $lngResultNum; $i++ )
			{
				$aryOrderDetail[] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		else
		{
			fncOutputError( 503, DEF_WARNING, "", TRUE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
		}

		$objDB->freeResult( $lngResultID );



		// 受注残を求める際の端数処理の算出方法を取得する
		//  ------> 第一段階では 受注、売上==>切捨て処理とする
		$lngCalcMode = DEF_CALC_KIRISUTE;


		// 計上日が設定されている場合は、入力された計上日を引き継ぐ
		if ( $_POST["dtmOrderAppDate"] != "" )
		{
			$dtmOrderAppDate = $_POST["dtmOrderAppDate"];
		}
		else
		// 計上日は今日の日付を適用する
		{
			$dtmOrderAppDate = date('Y/m/d',time());
		}


		///////////////////////////////////////////
		//////// 発注から仕入残数を求める /////////
		///////////////////////////////////////////
		$aryRemainsDetail = fncGetStockRemains ( $aryOrderResult["lngorderno"], "", $lngCalcMode, $objDB );

		if ( $aryRemainsDetail == 1 )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
		}
		// aryRemainsDetail に配列が含まれていなくても同じ処理を行うように修正
		else
		{
			///////////////////////////////////////////////////////////////
			//////// 仕入残数の計上単位等を発注時の単位にあわせる /////////
			///////////////////////////////////////////////////////////////
			$aryRemainsDetail_New = fncSetConversionStockRemains ( $aryRemainsDetail, $aryOrderDetail, 
				$aryOrderResult["lngmonetaryunitcode"], $lngCalcMode, $dtmOrderAppDate, $objDB );
			if ( $aryRemainsDetail_New == 1 )
			{
				fncOutputError ( 9051, DEF_ERROR, "", TRUE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}

			// aryDataにデータを設定する
			$aryData = fncChangeData2( $aryOrderResult , $objDB );

			// 明細行をhidden値に変換する
			$aryData["strDetailHidden"] = fncDetailHidden_pc( $aryRemainsDetail_New, "", $objDB);
		}

		// 計上日をaryDataに設定
		$aryData["dtmOrderAppDate"] = $dtmOrderAppDate;

		$lngMonetaryRateCode = ( $aryOrderResult["lngmonetaryratecode"] != "\\" ) ? 1 : 0;

		$lngMonetaryUnitCode = fncGetMasterValue( "m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitsign", $aryOrderResult["lngmonetaryunitcode"], "", $objDB );

		// レートタイプ
//------ 通貨が日本以外ならレートタイプを「TTM」にする
//通貨が日本以外ならレートタイプを「社内」にする
		if( $aryData["lngmonetaryunitcode"] != 1)
		{
//			$aryData["lngmonetaryratecode"] = fncPulldownMenu( 1, 1, '', $objDB );
			$aryData["lngmonetaryratecode"] = fncPulldownMenu( 1, 2, '', $objDB );
		}
		else
		{
			$aryData["lngmonetaryratecode"] = fncPulldownMenu( 1, $lngMonetaryUnitCode, '', $objDB );
		}


		// プルダウンメニューの生成
		// 通貨
		$aryData["lngmonetaryunitcode"] 		= fncPulldownMenu( 0, $lngMonetaryUnitCode, '', $objDB );
		// 支払条件
		$aryData["lngpayconditioncode"]			= fncPulldownMenu( 2, $aryOrderResult["lngpayconditioncode"], '', $objDB );
		// 仕入科目
		$aryData["strStockSubjectCode"]			= fncPulldownMenu( 3, $aryOrderResult["strstocksubjectcode"], '', $objDB );
		// 運搬方法
		$aryData["lngCarrierCode"]				= fncPulldownMenu( 6, 0, '', $objDB );
		// 製品単位
		$aryData["lngProductUnitCode_gs"]		= fncPulldownMenu( 7, 0, '', $objDB );
		// 荷姿単位
		$aryData["lngProductUnitCode_ps"]		= fncPulldownMenu( 8, 0, '', $objDB );


		//---------------------------------------------------------------
		// 仕入状態の取得(表示用)
		//---------------------------------------------------------------
		if ( $aryOrderResult["lngorderstatuscode"] != "" )
		{
			$aryData["lngOrderStatusCode_Display"] = fncGetMasterValue( "m_stockstatus", "lngstockstatuscode", "strstockstatusname", $aryOrderResult["lngorderstatuscode"],'', $objDB );
		}



		// ワークフロー表示・非表示設定
		$aryData["visibleWF"] = "hidden";
		// 承認ルートの取得
		$lngWorkflowOrderCode = fncGetMasterValue( "m_workflow", "strworkflowkeycode", "lngworkflowordercode", $aryOrderResult["lngorderno"].":str", '', $objDB );
		// 承認ルート
		// *v2 ワークフローなし* $aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $lngWorkflowOrderCode );





		//-----------------------------------------------------------
		// *v2*
		//-----------------------------------------------------------
/*
		// 仕入先コード
		$aryData["lngCustomerCode"] = $aryOrderResult["lngcustomercode"];

		// 仕入先名称
		$aryData["strCustomerName"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "strcompanydisplayname", $aryOrderResult["lngcustomercode"],'', $objDB );

		// 納品先コード
		$aryData["lngLocationCode"] = $aryOrderResult["lnglocationcode"];

		// 納品先名称
		$aryData["strLocationName"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "strcompanydisplayname", $aryOrderResult["lnglocationcode"],'', $objDB );

		// 製品到着日
		$aryData["dtmExpirationDate"] = $aryOrderResult["dtmexpirationdate"];
*/




		$aryData["strnote"] = fncHTMLSpecialChars( $aryData["strnote"] );

		$aryData["strMode"]               = "regist";
		$aryData["strProcMode"]           = "check";
		$aryData["MonetaryUnitDisabled"]  = " disabled";
		$aryData["lngOrderCode"]          = $_GET["strOrderCode"];
		$aryData["strOrderCode_Editable"] = "";
		$aryData["OrderSubmit"]           = "fncOrderSubmit();";
		$aryData["strSessionID"]          = $_POST["strSessionID"];
		$aryData["lngCalcCode"]           = DEF_CALC_KIRISUTE;
		$aryData["strPageCondition"]      = "regist";
		$aryData["lngRegistConfirm"]      = 0;


		// 710 仕入管理（行追加・行削除）
		if( !fncCheckAuthority( DEF_FUNCTION_PC10, $objAuth ) )
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

		echo fncGetReplacedHtml( "pc/regist/parts.tmpl", $aryData ,$objAuth);

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
	// 仕入科目
	$aryData["strStockSubjectCode"]   = fncPulldownMenu( 3, 0, '', $objDB );
	// 運搬方法
	$aryData["lngCarrierCode"]        = fncPulldownMenu( 6, 0, '', $objDB );
	// 製品単位
	$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
	// 荷姿単位
	$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );



	// ワークフロー表示・非表示設定
	$aryData["visibleWF"] = "hidden";
	// 承認ルート
	// *v2 ワークフローなし* $aryData["lngWorkflowOrderCode"]  = fncWorkFlow( $lngUserCode , $objDB , "" );



	$aryData["strMode"]               = "regist";
	$aryData["strProcMode"]           = "check";
	$aryData["curConversionRate"]     = "1.000000";
	$aryData["strSessionID"]          = $aryData["strSessionID"];
	$aryData["strOrderCode_Editable"] = "";
	$aryData["OrderSubmit"]           = "fncOrderSubmit();";
	$aryData["dtmOrderAppDate"]       = $dtmNowDate;
	$aryData["lngCalcCode"]           = DEF_CALC_KIRISUTE;
	$aryData["strPageCondition"]      = "regist";
	$aryData["lngFunctionCode"]       = DEF_FUNCTION_PC1;
	$aryData["lngRegistConfirm"]      = 0;


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

	echo fncGetReplacedHtml( "pc/regist/parts.tmpl", $aryData ,$objAuth);

	return true;


?>