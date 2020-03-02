<?php

// ----------------------------------------------------------------------------
/**
*       売上管理  修正画面
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
*       2013.05.31　　　　税率取得処理修正（期間内の税率取得できない場合、最新期間の税率を取得する ）
*
*/
// ----------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// ■ ライブラリファイル読込
	//-------------------------------------------------------------------------
	include( 'conf.inc');
	require( LIB_FILE );
	require( LIB_ROOT."libcalc.php" );
	require( SRC_ROOT."po/cmn/lib_po.php" );
	require( SRC_ROOT."pc/cmn/lib_pc.php" );
	require( SRC_ROOT."sc/cmn/lib_sc.php" );
	require( SRC_ROOT."sc/cmn/lib_scs1.php" );
	require( SRC_ROOT."sc/cmn/lib_scp.php" );
	require( SRC_ROOT."sc/cmn/column.php" );
	require( LIB_DEBUGFILE );


	//-------------------------------------------------------------------------
	// ■ オブジェクト生成
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();
	$objAuth = new clsAuth();


	//-------------------------------------------------------------------------
	// ■ パラメータ取得
	//-------------------------------------------------------------------------
	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];
	$aryData["lngLanguageCode"] = 1;


	//-------------------------------------------------------------------------
	// ■ DBオープン
	//-------------------------------------------------------------------------
	$objDB->open("", "", "", "");


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
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 605 売上修正
	if( fncCheckAuthority( DEF_FUNCTION_SC5, $objAuth ) )
	{
		$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
	}


	// 610 売上管理（行追加・行削除）
	if( !fncCheckAuthority( DEF_FUNCTION_SC10, $objAuth ) )
	{
		$aryData["adddelrowview"] = 'hidden';
	}






	//-------------------------------------------------------------------------
	// ■ 売上番号取得
	//-------------------------------------------------------------------------
	$lngSalesNo = $_REQUEST["lngSalesNo"];




	//-------------------------------------------------------------------------
	// ■ 入力チェック
	//-------------------------------------------------------------------------
	if( $_POST["strProcMode"] == "check")
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


		//-----------------------------------------------------------
		// ヘッダー項目チェック
		//-----------------------------------------------------------
		$aryData["renew"] = "true"; // 発注有効期限日をチェックしないためのフラグ

		list( $aryData, $bytErrorFlag ) = fncCheckData_sc( $aryData,"header", $objDB );

		$errorCount = ( $bytErrorFlag == "TRUE" ) ? 1 : 0;


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
		for( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
		{
			list( $aryDetailCheck[], $bytErrorFlag2[] ) = fncCheckData_sc( $_POST["aryPoDitail"][$i], "detail", $objDB );
		}

		for( $i = 0; $i < count( $bytErrorFlag2 ); $i++ )
		{
			// 明細行のエラー関数
			if( $bytErrorFlag2[$i] == "true" )
			{
				$aryDetailErrorMessage[] = fncDetailError( $bytErrorFlag2 );
			}
		}


		// ユーザに応じての金額制限
		if( $_POST["lngReceiveNo"] == "")
		{
			$lngUserGroup = $objAuth->AuthorityGroupCode;

			// 日本円に変換
			$lngPrice = $aryData["curAllTotalPrice"] * $aryData["curConversionRate"];

			// 608 売上管理（受注NOを指定しない登録が可能かどうか）
			if ( !fncCheckAuthority( DEF_FUNCTION_SC8, $objAuth ) )
			{
				$aryDetailErrorMessage[] = fncOutputError ( 607, "", "", FALSE, "sc/regist/renew.php?lngSalesNo=" . $lngSalesNo . "&strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			else
			{
				// 609 設定金額が上限以上でその登録可能な権限を持っていないなら
				if ( $lngPrice > DEF_MONEY_NO_CODE and !fncCheckAuthority( DEF_FUNCTION_SC9, $objAuth ) )
				{
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "sc/regist/renew.php?lngSalesNo=" . $lngSalesNo . "&strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}

/*
			if( $lngUserGroup == 5 )					// ユーザ
			{
				$aryDetailErrorMessage[] = fncOutputError ( 9060, "", "", FALSE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			elseif( $lngUserGroup == 4 )				// マネージャー
			{
				// 5万まで登録可能
				if( $lngPrice > DEF_MONEY_MANAGER )
				{
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}
			else										// ディレクター
			{
				// 20万まで登録可能
				if( $lngPrice > DEF_MONEY_DIRECTOR )
				{
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}

			}
*/
		}

		if( !is_array( $_POST["aryPoDitail"] ))
		{
			$aryDetailErrorMessage[] = fncOutputError ( 9001, "", "", FALSE, "", $objDB );
		}

		if( strcmp( $_POST["strReceiveCode"],"") != 0 )
		{

			$aryData["lngReceiveNo"] = $_POST["lngReceiveNo"];

			if ( $_POST["strReceiveCode"] == "" )
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

			if ( isset( $lngResultID ) )
			{
				$objDB->freeResult( $lngResultID );
			}

			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if ( $lngResultNum )
			{
				$objResult = $objDB->fetchObject( $lngResultID, 0 );
				$lngReceiveMonetaryUnitCode = $objResult->lngmonetaryunitcode;
			}
			else
			{
				// 通貨レート取得失敗
				fncOutputError ( 9051, DEF_ERROR, "通貨レート取得失敗", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}

			$objDB->freeResult( $lngResultID );


			//-------------------------------------------------------
			// DB -> SELECT : t_receivedetail
			//-------------------------------------------------------
			$aryQuery   = array();
			$aryQuery[] = "SELECT ";
			$aryQuery[] = "lngreceivedetailno, ";							// 受注明細番号
			$aryQuery[] = "lngrevisionno, ";								// リビジョン番号
			$aryQuery[] = "strproductcode, ";								// 製品コード
			$aryQuery[] = "lngsalesclasscode, ";							// 売上区分コード
			$aryQuery[] = "dtmdeliverydate, ";								// 納品日
			$aryQuery[] = "lngconversionclasscode, ";						// 換算区分コード / 1：単位計上/ 2：荷姿単位計上
			$aryQuery[] = "curproductprice, ";								// 製品価格
			$aryQuery[] = "lngproductquantity, ";							// 製品数量
			$aryQuery[] = "lngproductunitcode, ";							// 製品単位コード
			$aryQuery[] = "lngtaxclasscode, ";								// 消費税区分コード
			$aryQuery[] = "lngtaxcode, ";									// 消費税コード
			$aryQuery[] = "curtaxprice, ";									// 消費税金額
			$aryQuery[] = "cursubtotalprice, ";								// 小計金額
			$aryQuery[] = "strnote ";										// 備考
			$aryQuery[] = "FROM t_receivedetail ";
			$aryQuery[] = "WHERE ";
			$aryQuery[] = "lngreceiveno = ".$aryData["lngReceiveNo"];
			$aryQuery[] = " ORDER BY lngSortKey ASC";						// 取得順はソートキー順

			$strQuery = "";
			$strQuery = implode("\n", $aryQuery );

			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if ( $lngResultNum )
			{
				for ( $i = 0; $i < $lngResultNum; $i++ )
				{
					$aryReceiveDetail[] = $objDB->fetchArray( $lngResultID, $i );
				}
			}
			else
			{
				// 明細行が存在しない場合異常データ
				fncOutputError( 9051, DEF_ERROR, "明細行取得失敗", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}

			$objDB->freeResult( $lngResultID );

			// 通貨の値をコードに変更
			if ( $_POST["lngMonetaryUnitCode"] != "" )
			{
				$_POST["lngMonetaryUnitCode"] = ( $_POST["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $_POST["lngMonetaryUnitCode"];
				$lngSalesMonetaryUnitCode = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $_POST["lngMonetaryUnitCode"] . ":str", '', $objDB );
			}
			else
			{
				// 通貨レート取得失敗
				fncOutputError ( 9061, DEF_ERROR, "通貨単位の取得失敗", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			// $_POST["aryPoDitail"] の情報を変更してしまわないために
			$aryDetailResult = $_POST["aryPoDitail"];


			$lngCalcCode = DEF_CALC_KIRISUTE;

			//---------------------------------------------
			// 受注残以上に売上を設定していないかどうかのチェック
			//---------------------------------------------
			$lngResult = fncGetStatusSalesRemains( $aryData["lngReceiveNo"], $aryDetailResult, $lngReceiveMonetaryUnitCode, $lngSalesMonetaryUnitCode, $lngSalesNo, $lngCalcCode, $objDB );
//var_dump( $_POST );exit;
			switch ( $lngResult )
			{
				// 受注情報の取得失敗
				case 0:
					$aryDetailErrorMessage[] = fncOutputError ( 9061, DEF_ERROR, "指定された受注は削除されました", FALSE, "" . $_POST["strSessionID"], $objDB );
					break;

				// 受注明細、売上明細情報の取得失敗
				case 1:
					$aryDetailErrorMessage[] = fncOutputError ( 9061, DEF_ERROR, "受注、売上情報の取得に失敗しました", FALSE, "", $objDB );
					break;

				// 売上残の以上の仕入が設定されている
				case 99:
					$aryDetailErrorMessage[] = fncOutputError ( 604, DEF_ERROR, "", FALSE, "", $objDB );
					break;

				// 設定されている売上情報は売上残内
				case 50:
					break;
			}
		}




		//-----------------------------------------------------------
		// 最新データが「申請中」になっていないかどうか確認
		//-----------------------------------------------------------
		$strCheckQuery = "SELECT lngSalesNo, lngSalesStatusCode FROM m_Sales s WHERE s.strSalesCode = '" . $aryData["strSalesCode"] . "'";
		$strCheckQuery .= " AND s.bytInvalidFlag = FALSE ";
		$strCheckQuery .= " AND s.lngRevisionNo = ( " . "SELECT MAX( s1.lngRevisionNo ) FROM m_Sales s1 WHERE s1.strSalesCode = s.strSalesCode )\n";

		// チェッククエリーの実行
		list( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

		if( $lngCheckResultNum == 1 )
		{
			$objResult          = $objDB->fetchObject( $lngCheckResultID, 0 );
			$lngSalesStatusCode = $objResult->lngsalesstatuscode;

			//---------------------------------------------
			// 売上状態のチェック
			//---------------------------------------------
			// 申請中の場合
			if( $lngSalesStatusCode == DEF_SALES_PREORDER )
			{
				fncOutputError( 606, DEF_WARNING, "", TRUE, "../sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			// 締め済の場合
			if( $lngSalesStatusCode == DEF_SALES_CLOSED )
			{
				fncOutputError( 606, DEF_WARNING, "", TRUE, "", $objDB );
			}
		}

		// 結果IDを解放
		$objDB->freeResult( $lngCheckResultID );



		//-----------------------------------------------------------
		// 入力エラー
		//-----------------------------------------------------------
		if( is_array( $aryDetailErrorMessage ) || $errorCount == 1 )
		{
			if( is_array( $aryDetailErrorMessage ))
			{
				$aryData["strErrorMessage"] = implode( " : ", $aryDetailErrorMessage );
			}

			$aryData = fncChangeData3( $aryData , $objDB );

			// ordernoが入力された場合は通貨をdisabledにする
			if( is_array( $_POST["aryPoDitail"] ))
			{
				$aryData["MonetaryUnitDisabled"] = " disabled";
				// 明細行をhidden値に変換する
				$aryData["strDetailHidden"] = fncDetailHidden_sc( $_POST["aryPoDitail"] ,"insert", $objDB);
			}

			//ヘッダ備考の特殊文字変換
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );

			// プルダウンメニューの生成
			// 通貨
			$aryData["lngMonetaryUnitCode"]   = fncPulldownMenu( 0, $aryData["lngMonetaryUnitCode"], '', $objDB );
			// レートタイプ
			$aryData["lngMonetaryRateCode"]   = fncPulldownMenu( 1, $aryData["lngMonetaryRateCode"], '', $objDB );
			// 支払条件
			$aryData["lngPayConditionCode"]   = fncPulldownMenu( 2, $aryData["lngPayConditionCode"], '', $objDB );
			// 運搬方法
			$aryData["lngCarrierCode"]        = fncPulldownMenu( 6, 0, '', $objDB );
			// 製品単位
			$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
			// 荷姿単位
			$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );
			// 売上区分
			$aryData["lngSalesClassCode"]     = fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );



			// ワークフロー表示・非表示設定
			$aryData["visibleWF"] = "hidden";
			// 承認ルート
			// *v2 ワークフローなし* $aryData["lngWorkflowOrderCode"]  = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"] );



			//-------------------------------------------------------------------------
			// 状態コードが「 null / "" 」の場合、「0」を再設定
			//-------------------------------------------------------------------------
			$lngSalesStatusCode = fncCheckNullStatus( $lngSalesStatusCode );


			//---------------------------------------------------------------
			// 売上状態の取得
			//---------------------------------------------------------------
			$strSalseStatus = fncGetMasterValue( "m_salesstatus", "lngsalesstatuscode", "strsalesstatusname", $lngSalesStatusCode,'', $objDB );
			$aryData["lngSalseStatusCode_Display"] = $strSalseStatus;
			$aryData["lngSalseStatusCode"]         = $lngSalesStatusCode;



			$aryData["strGetDataMode"]        = "none";
			$aryData["strProcMode"]           = "check";
			$aryData["lngOrderCode"]          = $_REQUEST["strOrderCode"];
			$aryData["ReceiveSubmit"]         = "";
			$aryData["ReceiveSubmit2"]        = "";
			$aryData["strCustomerReceiveDis"] = 'contenteditable="false"';
			$aryData["strProductCodeOpenDis"] = 'contenteditable="false"';
			//$aryData["strReceiveCode_Editable"] = 'contenteditable="false"';
			$aryData["lngCalcCode"]           = DEF_CALC_KIRISUTE;
			$aryData["RENEW"]                 = TRUE;
			$aryData["strPageCondition"]      = "renew";


			// CRCリスト
			$aryData["crcflag"] = '0';
			$aryData["crcview"] = 'hidden';


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

			echo fncGetReplacedHtml( "sc/regist/parts.tmpl", $aryData ,$objAuth);

			return true;
		}

		//-----------------------------------------------------------
		// 確認画面表示
		//-----------------------------------------------------------
		else
		{
			$aryData["strProcMode"] = "renew";
			$aryData["RENEW"] = TRUE;

			// 言語の設定
			if ( isset($aryData["lngLanguageCode"]) and  $aryData["lngLanguageCode"] == 0 )
			{
				$aryTytle = $aryTableTytleEng;
			}
			else
			{
				$aryTytle = $aryTableTytle;
			}

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

			// hidden値生成の際に税額異常の調査を行う
			for( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
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

			// 明細行をhidden値に変換する
			$aryData["strDetailHidden"] = fncDetailHidden_sc( $_POST["aryPoDitail"] ,"insert", $objDB);

			// カラム名の設定
			$aryHeadColumnNames = fncSetSalesTabelName( $aryTableViewHead, $aryTytle );
			// カラム名の設定
			$aryDetailColumnNames = fncSetSalesTabelName( $aryTableViewDetail, $aryTytle );

			for ( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
			{

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


				$_POST["aryPoDitail"][$i]["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
				$_POST["aryPoDitail"][$i]["curproductprice_DIS"] = ( $_POST["aryPoDitail"][$i]["curProductPrice"] != "" ) ? number_format( $_POST["aryPoDitail"][$i]["curProductPrice"] ,4 ) : "";
				$_POST["aryPoDitail"][$i]["lnggoodsquantity_DIS"] = ( $_POST["aryPoDitail"][$i]["lngGoodsQuantity"] != "" ) ? number_format( $_POST["aryPoDitail"][$i]["lngGoodsQuantity"] ) : "";
				$_POST["aryPoDitail"][$i]["curtotalprice_DIS"] = ( $_POST["aryPoDitail"][$i]["curTotalPrice"] != "" ) ? number_format( $_POST["aryPoDitail"][$i]["curTotalPrice"] ,2 ) : "";
				$_POST["aryPoDitail"][$i]["curtaxprice_DIS"] = ( $_POST["aryPoDitail"][$i]["curTaxPrice"] != "" ) ? number_format( $_POST["aryPoDitail"][$i]["curTaxPrice"] ,2 ) : "";

				// コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）
				$_POST["aryPoDitail"][$i]["strproductcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strProductCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strProductCode"]."]" : "";
				$_POST["aryPoDitail"][$i]["lngsalesclasscode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["lngSalesClassCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["lngSalesClassCode"]."]" : "";


				$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;



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


			$aryData["strDetailTable"] = implode ("\n", $aryDetailTable );

			$aryData["strMode"] = "regist";
			$aryData["strProcMode"] = "renew";

			// 登録日
			$aryData["dtminsertdate"] = date( 'Y/m/d', time());
			// 状態
			$aryData["strAction"] = "/sc/regist/index2.php?strSessionID=".$aryData["strSessionID"];

			// 入力者 
			$aryData["lngInputUserCode"] = $objAuth->UserID;
			$aryData["strInputUserName"] = $objAuth->UserDisplayName;

			// 部門
			$aryData["strInChargeGroupName"] = fncGetMasterValue( "m_group", "strgroupdisplaycode", "strgroupdisplayname",  $aryData['lngInChargeGroupCode'] . ":str",'',$objDB);
			// 担当者
			$aryData["strInChargeUserName"] = fncGetMasterValue( "m_user", "struserdisplaycode" ,"struserdisplayname" , $aryData["lngInChargeUserCode"] . ":str",'',$objDB);
			// 通貨
			$_POST["strMonetaryUnitName"] = ($aryData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryData["lngMonetaryUnitCode"];
			$aryData["strMonetaryUnitName"] = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $_POST["strMonetaryUnitName"] . ":str", '', $objDB );


			// レートコード
			$strMonetaryrateName = fncGetMasterValue( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $aryData["lngMonetaryRateCode"], '', $objDB);
			$aryData["strMonetaryrateName"] = ( $strMonetaryrateName == "−" ) ? "" : $strMonetaryrateName;



/*
			//---------------------------------------------------------------
			// 売上状態の取得
			//---------------------------------------------------------------
			if( strcmp( $aryData["lngsalesstatuscode"], "" ) != 0 )
			{
				$strSalseStatus = fncGetMasterValue( "m_salesstatus", "lngsalesstatuscode", "strsalesstatusname", $aryData["lngSalesStatusCode"],'', $objDB );
				$aryData["lngSalseStatusCode_Display"] = $strSalseStatus;
				$aryData["lngSalseStatusCode"]         = $aryData["lngSalesStatusCode"];
			}
*/

			// ワークフロー表示・非表示設定
			$aryData["visibleWF"] = "hidden";


			//---------------------------------------------
			// 承認ルート
			//---------------------------------------------
			if ( $_POST["lngWorkflowOrderCode"] != "" and $_POST["lngWorkflowOrderCode"] != 0 )
			{
				$aryData["strWorkflowOrderName"] = fncGetMasterValue( "m_workfloworder", "lngworkflowordercode", "strworkflowordername", $_POST["lngWorkflowOrderCode"] . ":str", 'lngWorkflowOrderNo = 1', $objDB );

				$aryData["strWorkflowMessage_visibility"] = "block;";
			}
			else
			{
				$aryData["strWorkflowOrderName"] = "承認なし";

				$aryData["strWorkflowMessage_visibility"] = "none;";
			}



			//ヘッダ備考の特殊文字変換
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );

			$aryData["lngRegistConfirm"] = 0;

			$aryData["RENEW"] = TRUE;

			$aryData["lngLanguageCode"] = 1;



			// 状態
			$aryData["strAction"] = "/sc/regist/index2.php?strSessionID=".$aryData["strSessionID"];

			$aryData["strActionURL"] = "/sc/regist/index2.php?strSessionID=".$aryData["strSessionID"];

			$aryData["curConversionRate_DIS"] = number_format( $aryData["curConversionRate"],6 );	// 換算レート
			$aryData["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
			$aryData["curAllTotalPrice_DIS"] = number_format( $aryData["curAllTotalPrice"],2 );		// 合計金額

			// コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）
			$aryData["lngInputUserCode_DISCODE"] = ( $aryData["lngInputUserCode"] != "" ) ? "[".$aryData["lngInputUserCode"]."]" : "";
			$aryData["lngcustomercode_DISCODE"] = ( $aryData["lngCustomerCode"] != "" ) ? "[".$aryData["lngCustomerCode"]."]" : "";
			//$aryData["lngInchargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
			//$aryData["lngInchargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";

			//$aryData["strReceiveCode_Editable"] = "contenteditable=\"false\"";

			$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;



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



			// CRCリスト
			$aryData["crcflag"] = '0';
			$aryData["crcview"] = 'hidden';



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



			// テンプレート読み込み
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "sc/confirm/parts.tmpl" );

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

	//-------------------------------------------------------------------------
	// ■ 初期表示 -> 初期値設定
	//-------------------------------------------------------------------------
	// 売上番号取得
	$lngSalesNo = $_REQUEST["lngSalesNo"];


	$aryQuery   = array();
	$aryQuery[] = "SELECT ";
	$aryQuery[] = "s.lngSalesNo, ";															// 1:売上番号
	$aryQuery[] = "s.lngRevisionNo, ";														// 2:リビジョン番号 
	$aryQuery[] = "s.strSalesCode, ";														// 3:売上コード
	$aryQuery[] = "tsd.lngReceiveNo, ";														// 4:受注番号 
	$aryQuery[] = "To_char( s.dtmAppropriationDate, 'YYYY/mm/dd') as dtmOrderAppDate, ";	// 5:計上日
	$aryQuery[] = "s.lngCustomerCompanyCode as lngCustomerCode, ";							// 6:顧客
	//$aryQuery[] = "s.lngGroupCode as lnginchargegroupcode, ";								// 7:部門
	//$aryQuery[] = "s.lngUserCode as lnginchargeusercode, ";									// 8:担当者
	$aryQuery[] = "s.lngSalesStatusCode, ";													// 9:売上状態コード 
	$aryQuery[] = "s.lngMonetaryUnitCode, ";												// 10:通貨単位コード
	$aryQuery[] = "s.lngMonetaryRateCode, ";												// 11:通貨レートコード
	$aryQuery[] = "s.curConversionRate, ";													// 12:換算レート
	$aryQuery[] = "s.strSlipCode, ";														// 13:伝票コード
	$aryQuery[] = "s.curTotalPrice, ";														// 14:合計金額
	$aryQuery[] = "s.strNote ";																// 15:備考
	$aryQuery[] = "FROM m_sales s ";
	$aryQuery[] = "left join t_salesdetail tsd on tsd.lngsalesno = s.lngsalesno";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "s.lngSalesNo = $lngSalesNo";

	$strQuery = implode("\n", $aryQuery );


	// クエリー実行
	$objDB->freeResult( $lngResultID );

	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}


	// データ取得
	$aryData2 = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );



/*
	//-------------------------------------------------------------------------
	// 受注状態のチェック
	//-------------------------------------------------------------------------
	// 申請中の場合
	if( $aryData["lngorderstatuscode"] == DEF_RECEIVE_APPLICATE )
	{
		fncOutputError( 404, DEF_WARNING, "", TRUE, "", $objDB );
	}
	// 納品済の場合
	if( $aryData["lngorderstatuscode"] == DEF_RECEIVE_END )
	{
		fncOutputError( 405, DEF_WARNING, "", TRUE, "", $objDB );
	}
*/


	// 受注番号取得
	$lngReceiveNo = $aryData2["lngreceiveno"];


	if( $lngReceiveNo != "" )
	{ 
		$aryData["strReceiveCode"] = fncGetMasterValue( "m_receive", "lngreceiveno", "strreceivecode", $lngReceiveNo , '',$objDB );
		$aryData["strReviseCode"]  = fncGetMasterValue( "m_receive", "lngreceiveno", "strrevisecode", $lngReceiveNo , '',$objDB );
	}


	// プルダウンメニューの生成
	// 通貨
	$lngMonetaryUnitCode = fncGetMasterValue( "m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitsign", $aryData2["lngmonetaryunitcode"], "", $objDB );
	$aryData["lngmonetaryunitcode"] = fncPulldownMenu( 0, $lngMonetaryUnitCode, '', $objDB );

	// レートタイプ
	$aryData["lngmonetaryratecode"] = fncPulldownMenu( 1, $aryData2["lngmonetaryratecode"], '', $objDB );
	// 支払条件
	$aryData["lngpayconditioncode"] = fncPulldownMenu( 2, $aryData2["lngpayconditioncode"], '', $objDB );

	//ヘッダ備考の特殊文字変換
	$aryData["strnote"] = fncHTMLSpecialChars( $aryData2["strnote"] );


	// データのマージ
	$aryData = array_merge( $aryData2, $aryData );

	// 関数 fncChangeDisplayNameで表示用データに変換(HEADER）
	$aryData = fncChangeData2( $aryData , $objDB );



	// 明細行
	$aryQuery   = array();
	$aryQuery[] = "SELECT ";
	$aryQuery[] = "lngSalesNo, ";									// 1:売上番号
	$aryQuery[] = "lngSalesDetailNo, ";								// 2:売上明細番号
	$aryQuery[] = "lngSalesDetailNo as lngOrderDetailNo, ";			// −−−明細行番号
	$aryQuery[] = "lngRevisionNo, ";								// 3:リビジョン番号
	$aryQuery[] = "strProductCode, ";								// 4:製品コード
	$aryQuery[] = "lngSalesClassCode, ";							// 5:売上区分コード
	$aryQuery[] = "To_char(dtmDeliveryDate, 'YYYY/mm/dd') as dtmDeliveryDate,";	// 6:納品日
	$aryQuery[] = "lngConversionClassCode, ";						// 7:換算区分コード  1：単位計上/ 2：荷姿単位計上
	$aryQuery[] = "curProductPrice, ";								// 8:製品価格
	$aryQuery[] = "lngProductQuantity as lngGoodsQuantity, ";		// 9:製品数量
	$aryQuery[] = "lngProductUnitCode, ";							// 10:製品単位コード
	$aryQuery[] = "lngTaxClassCode, ";								// 11:消費税区分コード
	$aryQuery[] = "lngTaxCode, ";									// 12:消費税コード
	$aryQuery[] = "curTaxPrice, ";									// 13:消費税金額
	$aryQuery[] = "curSubTotalPrice, ";								// 14:小計金額
	$aryQuery[] = "strnote as strDetailNote ";										// 15:備考

	$aryQuery[] = ",lngreceiveno";
	$aryQuery[] = ",lngreceivedetailno";

	$aryQuery[] = "FROM t_salesdetail ";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "lngSalesNo = $lngSalesNo";
	$aryQuery[] = " ORDER BY lngSortKey ASC";

	$strQuery = "";
	$strQuery = implode("\n", $aryQuery );



	$objDB->freeResult( $lngResultID );

	if( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError( 9051, DEF_ERROR, "", TRUE, "", $objDB );
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
	$aryData["strDetailHidden"] = fncDetailHidden_sc( $aryQueryResult ,"detail", $objDB );


	// プルダウンメニューの生成
	// 通貨
	$lngMonetaryUnit = fncGetMasterValue( "m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitsign", $aryData2["lngmonetaryunitcode"],'', $objDB );
	$aryData["lngmonetaryunitcode"] = fncPulldownMenu( 0, $lngMonetaryUnit, '', $objDB );





	//-------------------------------------------------------------------------
	// 売上状態のチェック
	//-------------------------------------------------------------------------
	// 申請中の場合
	if( $aryData2["lngsalesstatuscode"] == DEF_SALES_APPLICATE )
	{
		fncOutputError( 608, DEF_WARNING, "", TRUE, "", $objDB );
	}
	// 締め済の場合
	if( $aryData2["lngsalesstatuscode"] == DEF_SALES_CLOSED )
	{
		fncOutputError( 9062, DEF_WARNING, "", TRUE, "", $objDB );
	}




	//-------------------------------------------------------------------------
	// 状態コードが「 null / "" 」の場合、「0」を再設定
	//-------------------------------------------------------------------------
	$aryData2["lngsalesstatuscode"] = fncCheckNullStatus( $aryData2["lngsalesstatuscode"] );




	//---------------------------------------------------------------
	// 売上状態の取得
	//---------------------------------------------------------------
	$strSalseStatus = fncGetMasterValue( "m_salesstatus", "lngsalesstatuscode", "strsalesstatusname", $aryData2["lngsalesstatuscode"],'', $objDB );
	$aryData["lngSalseStatusCode_Display"] = $strSalseStatus;
	$aryData["lngSalseStatusCode"]         = $aryData2["lngsalesstatuscode"];





	// ワークフロー表示・非表示設定
	$aryData["visibleWF"] = "hidden";
	// 承認ルートの取得
	$lngWorkflowOrderCode = 0;
	//$lngWorkflowOrderCode = fncGetMasterValue( "m_workflow", "strworkflowkeycode", "lngworkflowordercode", $lngReceiveNo, '', $objDB );
	// *v2 ワークフローなし* $aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $lngWorkflowOrderCode );



	// 顧客受注番号の取得
	if( $lngReceiveNo )
	{
		$strCustomerReceiveCode = fncGetMasterValue( "m_receive", "lngreceiveno", "strcustomerreceivecode", $lngReceiveNo, '', $objDB );
		$aryData["strCustomerReceiveCode"] = $strCustomerReceiveCode;
	}



	// レートタイプ
//	$aryData["lngmonetaryratecode"]   = fncPulldownMenu( 1, $aryData["lngmonetaryratecode"], '', $objDB );
	// 支払条件
	$aryData["lngPayConditionCode"]   = fncPulldownMenu( 2, $aryData["lngPayConditionCode"], '', $objDB );
	// 製品単位
	$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
	// 荷姿単位
	$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );
	// 売上区分
	$aryData["lngSalesClassCode"]     = fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );

	$aryData["MonetaryUnitDisabled"]  = " disabled";


	$aryData["strGetDataMode"]        = "none";
	$aryData["strProcMode"]           = "check";
	$aryData["strSessionID"]          = $aryData["strSessionID"];
	$aryData["ReceiveSubmit"]         = "";
	$aryData["ReceiveSubmit2"]        = "";
	$aryData["strCustomerReceiveDis"] = 'contenteditable="false"';
	$aryData["strProductCodeOpenDis"] = 'contenteditable="false"';
	//$aryData["strReceiveCode_Editable"] = 'contenteditable="false"';
	$aryData["RENEW"]                 = TRUE;
	$aryData["lngCalcCode"]           = DEF_CALC_KIRISUTE;
	$aryData["strPageCondition"]      = "renew";
	$aryData["lngFunctionCode"]       = DEF_FUNCTION_SC5;



	// CRCリスト
	$aryData["crcflag"] = '0';
	$aryData["crcview"] = 'hidden';

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

	echo fncGetReplacedHtml( "sc/regist/parts.tmpl", $aryData ,$objAuth);

	return true;


?>