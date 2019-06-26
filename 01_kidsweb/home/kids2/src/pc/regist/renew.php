<?php

// ----------------------------------------------------------------------------
/**
*       仕入管理  修正画面
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
	include( 'conf.inc' );
	require( LIB_FILE );
	require( LIB_ROOT."libcalc.php" );
	require( SRC_ROOT."po/cmn/lib_po.php" );
	require( SRC_ROOT."pc/cmn/lib_pc.php" );
	require( SRC_ROOT."pc/cmn/lib_pcp.php" );
	require( SRC_ROOT."pc/cmn/lib_pcs1.php" );
	require( SRC_ROOT."pc/cmn/column.php" );


	//-------------------------------------------------------------------------
	// ■ オブジェクト生成
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();
	$objAuth = new clsAuth();


	//-------------------------------------------------------------------------
	// ■ パラメータ取得
	//-------------------------------------------------------------------------
	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];
	// $aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];



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

	// 700 仕入管理
	if( !fncCheckAuthority( DEF_FUNCTION_PC0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 705 仕入管理（ 仕入修正）
	if( fncCheckAuthority( DEF_FUNCTION_PC5, $objAuth ) )
	{
		$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
	}



	// 710 仕入管理（行追加・行削除）
	if( !fncCheckAuthority( DEF_FUNCTION_PC10, $objAuth ) )
	{
		$aryData["adddelrowview"] = 'hidden';
	}



	//-------------------------------------------------------------------------
	// ■ 仕入番号取得
	//-------------------------------------------------------------------------
	$lngstockno = $_REQUEST["lngStockNo"];



	//-------------------------------------------------------------------------
	// ■ 入力チェック
	//-------------------------------------------------------------------------
	if( $_POST["strMode"] == "check")
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
		$aryData["renew"] = "true"; // 発注有効期限日をチェックしないためのフラグ

		list ( $aryData, $bytErrorFlag ) = fncCheckData_pc( $aryData,"header", $objDB );

		$errorCount = ( $bytErrorFlag == "TRUE" ) ? 1 : 0;


		//-----------------------------------------------------------
		// 明細行のチェック
		//-----------------------------------------------------------
		$aryQueryResult2 = $_POST["aryPoDitail"];
		for( $i = 0; $i < count( $aryQueryResult2 ); $i++ )
		{
			list ( $aryDetailCheck[], $bytErrorFlag2[] ) = fncCheckData_pc( $_POST["aryPoDitail"][$i], "detail", $objDB );
		}

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



		// 明細行のエラー関数
		for( $i = 0; $i < count( $bytErrorFlag2 ); $i++ )
		{
			if( $bytErrorFlag2[$i] == "true")
			{
				$aryDetailErrorMessage[] = fncDetailError( $bytErrorFlag2 );
			}
		}

		if( !is_array( $_POST["aryPoDitail"] ))
		{
			$aryDetailErrorMessage[] = fncOutputError ( 9001, "", "", FALSE, "", $objDB );
		}


		$_POST["lngOrderNo"] = trim( $_POST["lngOrderNo"] );

		// ユーザに応じての金額制限
		if( $_POST["lngOrderNo"] == "" )
		{
			$lngUserGroup = $objAuth->AuthorityGroupCode;

			// 日本円に変換
			$lngPrice = $aryData["curAllTotalPrice"] * $aryData["curConversionRate"];

			// 708 仕入管理（発注NOを指定しない登録が可能かどうか）
			if ( !fncCheckAuthority( DEF_FUNCTION_PC8, $objAuth ) )
			{
				$aryDetailErrorMessage[] = fncOutputError ( 710, "", "", FALSE, "pc/regist/renew.php?lngStockNo=" . $lngstockno . "&strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			else
			{
				// 709 設定金額が上限以上でその登録可能な権限を持っていないなら
				if ( $lngPrice > DEF_MONEY_NO_CODE and !fncCheckAuthority( DEF_FUNCTION_PC9, $objAuth ) )
				{
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "pc/regist/renew.php?lngStockNo=" . $lngstockno . "&strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}

/*
			if( $lngUserGroup == 5 )					// ユーザ
			{
				$aryDetailErrorMessage[] = fncOutputError ( 710, "", "", FALSE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			elseif( $lngUserGroup == 4 )				// マネージャー
			{
				// 5万まで登録可能
				if( $lngPrice > DEF_MONEY_MANAGER )
				{
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}
			else										// ディレクター
			{
				// 20万まで登録可能
				if( $lngPrice > DEF_MONEY_DIRECTOR )
				{
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}

			}
*/
		}


		if( strcmp( $_POST["strOrderCode"],"") != 0 )
		{
			$aryData["lngOrderNo"] = $_POST["lngOrderNo"];

			if ( $_POST["strOrderCode"] == "" )
			{
				//-----------------------------------------
				// DB -> SELECT : m_Order
				//-----------------------------------------
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

			//-------------------------------------------------------
			// DB -> SELECT : m_Order
			//-------------------------------------------------------
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
			$aryQuery[] = " ORDER BY lngSortKey ASC";

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
				$lngStockMonetaryUnitCode = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $_POST["lngMonetaryUnitCode"] . ":str", '', $objDB );
			}
			else
			{
				// 発注コード取得失敗
				fncOutputError ( 9061, DEF_ERROR, "通貨単位の取得失敗", TRUE, "pc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			$aryDetailResult = $_POST["aryPoDitail"];

			$lngCalcCode = DEF_CALC_KIRISUTE;

			/////////////////////////////////////////////////////////////////////////
			///// 発注残以上に仕入を設定していないかどうかのチェック関数呼び出し ////
			/////////////////////////////////////////////////////////////////////////
			$lngResult = fncGetStatusStockRemains ( $aryData["lngOrderNo"], $aryDetailResult, $lngOrderMonetaryUnitCode, $lngStockMonetaryUnitCode, $lngstockno, $lngCalcCode, $objDB );

			switch ( $lngResult )
			{
				// 発注情報の取得失敗
				case 0:
					$aryDetailErrorMessage[] = fncOutputError ( 9061, DEF_ERROR, "指定された発注は削除されました", FALSE, "" . $_POST["strSessionID"], $objDB );
					break;

				// 発注明細、仕入明細情報の取得失敗
				case 1:
					$aryDetailErrorMessage[] = fncOutputError ( 9061, DEF_ERROR, "発注、仕入情報の取得に失敗しました", FALSE, "", $objDB );
					break;

				// 発注残以上の仕入が設定されている
				case 99:
					$aryDetailErrorMessage[] = fncOutputError ( 705, DEF_ERROR, "", FALSE, "", $objDB );
					break;

				// 設定されている仕入情報は発注残内
				case 50:
					break;
			}
		}




		//-----------------------------------------------------------
		// 最新データが「申請中」になっていないかどうか確認
		//-----------------------------------------------------------
		$strCheckQuery = "SELECT lngStockNo, lngStockStatusCode FROM m_Stock s WHERE s.strStockCode = '" . $aryData["strStockCode"] . "'";
		$strCheckQuery .= " AND s.bytInvalidFlag = FALSE ";
		$strCheckQuery .= " AND s.lngRevisionNo = ( " . "SELECT MAX( s1.lngRevisionNo ) FROM m_Stock s1 WHERE s1.strStockCode = s.strStockCode )\n";

		// チェッククエリーの実行
		list( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

		if( $lngCheckResultNum == 1 )
		{
			$objResult          = $objDB->fetchObject( $lngCheckResultID, 0 );
			$lngStockStatusCode = $objResult->lngstockstatuscode;

			//---------------------------------------------
			// 仕入状態のチェック
			//---------------------------------------------
			// 申請中の場合
			if( $lngStockStatusCode == DEF_STOCK_APPLICATE )
			{
				fncOutputError( 711, DEF_WARNING, "", TRUE, "../sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			// 締め済の場合
			if( $lngStockStatusCode == DEF_STOCK_CLOSED )
			{
				fncOutputError( 711, DEF_WARNING, "", TRUE, "", $objDB );
			}
		}

		// 結果IDを解放
		$objDB->freeResult( $lngCheckResultID );




		//-----------------------------------------------------------
		// 入力エラー
		//-----------------------------------------------------------
		if( is_array( $aryDetailErrorMessage ) || $errorCount == 1 )
		{
			// 関数 fncChangeDisplayNameで表示用データに変換(HEADER）
			$aryData = fncChangeData3( $aryData , $objDB );

			if( is_array( $aryDetailErrorMessage ) )
			{
				$aryData["strErrorMessage"] = implode(" : ", $aryDetailErrorMessage);
			}

			// 明細行をhidden値に変換する
			if( is_array( $_POST["aryPoDitail"] ))
			{
				$aryData["strDetailHidden"] = fncDetailHidden_pc( $_POST["aryPoDitail"] ,"insert", $objDB);
				$aryData["MonetaryUnitDisabled"] = "disabled";
			}

			//ヘッダ備考の特殊文字変換
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );




			//-------------------------------------------------------------------------
			// 状態コードが「 null / "" 」の場合、「0」を再設定
			//-------------------------------------------------------------------------
			$lngStockStatusCode = fncCheckNullStatus( $lngStockStatusCode );


			//---------------------------------------------------------------
			// 仕入状態の取得
			//---------------------------------------------------------------
			$aryData["lngOrderStatusCode_Display"] = fncGetMasterValue( "m_stockstatus", "lngstockstatuscode", "strstockstatusname", $lngStockStatusCode, '', $objDB );

			$aryData["lngStockStatusCode"] = $lngStockStatusCode;




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
			// *v2 ワークフローなし* $aryData["lngWorkflowOrderCode"]  = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"] );



			$aryData["strMode"]               = "check";
			$aryData["strOrderCode_Editable"] = "contenteditable=\"false\"";
			$aryData["OrderSubmit"]           = "";
			$aryData["lngOrderCode"]          = $_GET["strOrderCode"];
			$aryData["lngCalcCode"]           = DEF_CALC_KIRISUTE;
			$aryData["RENEW"]                 = TRUE;
			$aryData["strPageCondition"]      = "renew";

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


			echo fncGetReplacedHtml( "pc/regist/parts.tmpl", $aryData ,$objAuth);

			return true;

		}

		//-----------------------------------------------------------
		// 確認画面表示
		//-----------------------------------------------------------
		else
		{
			// 言語の設定
			if( isset($aryData["lngLanguageCode"]) and  $aryData["lngLanguageCode"] == 0 )
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
				// 最新の税率情報を取得する 20130531 add
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
				$lngDigitNumber = 0; // 日本円の場合は０桁
			}
			else
			{
				$lngDigitNumber = 2; // 日本円以外の場合は小数点以下２桁
			}

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

			// 明細行をhidden値に変換する
			$aryData["strDetailHidden"] = fncDetailHidden_pc( $_POST["aryPoDitail"] ,"insert", $objDB);


			// カラム名の設定
			$aryHeadColumnNames = fncSetStockTabelName( $aryTableViewHead, $aryTytle );
			// カラム名の設定
			$aryDetailColumnNames = fncSetStockTabelName( $aryTableViewDetail, $aryTytle );


			for ( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
			{
				$_POST["aryPoDitail"][$i]["lngrecordno"] = $i + 1;

				// 仕入科目
				$_POST["aryPoDitail"][$i]["strStockSubjectName"] = fncGetMasterValue( "m_stocksubject", "lngstocksubjectcode", "strstocksubjectname",  $_POST["aryPoDitail"][$i]["strStockSubjectCode"], '', $objDB );

				// 仕入部品 
				$_POST["aryPoDitail"][$i]["strStockItemName"] = fncGetMasterValue( "m_stockitem", "lngstockitemcode", "strstockitemname", $_POST["aryPoDitail"][$i]["strStockItemCode"], "lngstocksubjectcode = ".$_POST["aryPoDitail"][$i]["strStockSubjectCode"], $objDB );

				// 運搬方法
				$_POST["aryPoDitail"][$i]["strCarrierName"] = fncGetMasterValue( "m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", $_POST["aryPoDitail"][$i]["lngCarrierCode"],'', $objDB );

				// 顧客品番
				$_POST["aryPoDitail"][$i]["strGoodsName"] = fncGetMasterValue( "m_product", "strproductcode", "strGoodsCode", $_POST["aryPoDitail"][$i]["strProductCode"].":str", "bytinvalidflag = false", $objDB );

				// 単位
				$_POST["aryPoDitail"][$i]["strProductUnitName"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["aryPoDitail"][$i]["lngProductUnitCode"], '', $objDB );

				// 税区分
				$_POST["aryPoDitail"][$i]["strTaxClassName"] = fncGetMasterValue( "m_taxclass", "lngtaxclasscode", "strtaxclassname", $_POST["aryPoDitail"][$i]["lngTaxClassCode"], '', $objDB );


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


				// number_format
				$_POST["aryPoDitail"][$i]["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
				$_POST["aryPoDitail"][$i]["curproductprice_DIS"] = ($_POST["aryPoDitail"][$i]["curProductPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curProductPrice"] ,4 ) : "";
				$_POST["aryPoDitail"][$i]["lnggoodsquantity_DIS"] = ($_POST["aryPoDitail"][$i]["lngGoodsQuantity"] != "") ? number_format( $_POST["aryPoDitail"][$i]["lngGoodsQuantity"] ) : "";
				$_POST["aryPoDitail"][$i]["curtaxprice_DIS"] = ($_POST["aryPoDitail"][$i]["curTaxPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curTaxPrice"] ,2 ) : "";
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


			// 登録日
			$aryData["dtminsertdate"] = date( 'Y/m/d', time());
			// 入力者
			$aryData["lngInputUserCode"] = $objAuth->UserID;
			$aryData["strInputUserName"] = $objAuth->UserDisplayName;

			// 部門
			$aryData["strInChargeGroupName"] = fncGetMasterValue( "m_group", "strgroupdisplaycode", "strgroupdisplayname",  $_POST['lngInChargeGroupCode'] . ":str",'',$objDB);
			// 担当者
			$aryData["strInChargeUserName"] = fncGetMasterValue( "m_user", "struserdisplaycode" ,"struserdisplayname" , $_POST["lngInChargeUserCode"] . ":str",'',$objDB);
			// 通貨
			$_POST["strMonetaryUnitName"] = ($_POST["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $_POST["lngMonetaryUnitCode"];
			$aryData["strMonetaryUnitName"] = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $_POST["strMonetaryUnitName"] . ":str", '', $objDB );
			// レートコード
			$strMonetaryrateName = fncGetMasterValue( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $_POST["lngMonetaryRateCode"], '', $objDB);
			$aryData["strMonetaryrateName"] = ( $strMonetaryrateName == "−" ) ? "" : $strMonetaryrateName;

			// 支払条件
			$strPayConditionName = fncGetMasterValue( "m_paycondition", "lngpayconditioncode", "strpayconditionname", $_POST["lngPayConditionCode"], '', $objDB );
			$aryData["strPayConditionName"] = ( $strPayConditionName == "−" ) ? "" : $strPayConditionName;

			//ヘッダ備考の特殊文字変換
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );





			//---------------------------------------------------------------
			// 仕入状態の取得
			//---------------------------------------------------------------
/*
			if( $lngStockStatusCode != "" )
			{
				$aryData["lngOrderStatusCode_Display"] = fncGetMasterValue( "m_stockstatus", "lngstockstatuscode", "strstockstatusname", $lngStockStatusCode, '', $objDB );

				$aryData["lngStockStatusCode"] = $lngStockStatusCode;
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



			// number_format
			$aryData["curConversionRate_DIS"] = number_format( $aryData["curConversionRate"],6 );	// 換算レート
			$aryData["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
			$aryData["curAllTotalPrice_DIS"] = number_format( $aryData["curAllTotalPrice"],2 );		// 合計金額


			// コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）
			$aryData["lngInputUserCode_DISCODE"] = ( $aryData["lngInputUserCode"] != "" ) ? "[".$aryData["lngInputUserCode"]."]" : "";
			$aryData["lngCustomerCode_DISCODE"] = ( $aryData["lngCustomerCode"] != "" ) ? "[".$aryData["lngCustomerCode"]."]" : "";
			//$aryData["lngInchargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
			//$aryData["lngInchargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";
			$aryData["lngLocationCode_DISCODE"] = ( $aryData["lngLocationCode"] != "" ) ? "[".$aryData["lngLocationCode"]."]" : "";



			$aryData["RENEW"]            = TRUE;
			$aryData["strMode"]          = "regist";
			$aryData["strProcMode"]      = "renew";
			$aryData["lngLanguageCode"]  = $_COOKIE["lngLanguageCode"];
			$aryData["lngCalcCode"]      = DEF_CALC_KIRISUTE;
			$aryData["lngRegistConfirm"] = 0;


			$aryData["strAction"]    = "/pc/regist/index2.php?strSessionID=".$aryData["strSessionID"];
			$aryData["strActionURL"] = "/pc/regist/index2.php?strSessionID=".$aryData["strSessionID"];




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
			$objTemplate->getTemplate( "pc/confirm/parts.tmpl" );

			// テンプレート生成
			$objTemplate->replace( $aryHeadColumnNames );
			$objTemplate->replace( $aryData );
			$objTemplate->complete();


			$objDB->close();


			// HTML出力
			echo $objTemplate->strTemplate;
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
	$aryQuery   = array();
	$aryQuery[] = "SELECT ";
	$aryQuery[] = "o.strrevisecode as strReviseCode, ";					// 2:リバイス番号
	$aryQuery[] = "o.strordercode as strOrderCode, ";					// 1:仕入番号
	$aryQuery[] = "s.strstockcode as lngStockCode, ";					// 3:仕入コード 
	$aryQuery[] = "s.lngorderno, ";
	$aryQuery[] = "To_char( s.dtmAppropriationDate, 'YYYY/mm/dd' ) as dtmOrderAppDate,";	// 4:計上日
	$aryQuery[] = "s.lngcustomercompanycode, ";							// 6:仕入先
	//$aryQuery[] = "s.lnggroupcode as lngInChargeGroupCode, ";			// 7:部門
	//$aryQuery[] = "s.lngusercode as lngInChargeUserCode, ";				// 8:担当者
	$aryQuery[] = "s.lngstockstatuscode, ";								// 9:仕入状態コード
	$aryQuery[] = "s.lngmonetaryunitcode, ";							// 10:通貨単位コード
	$aryQuery[] = "s.lngmonetaryratecode, ";							// 11:通貨レートコード
	$aryQuery[] = "s.curconversionrate, ";								// 12:換算レート
	$aryQuery[] = "s.lngpayconditioncode, ";							// 13:支払い条件
	$aryQuery[] = "s.strslipcode, ";									// 14:伝票コード
	$aryQuery[] = "s.lngdeliveryplacecode, ";							// 15:納品場所
	$aryQuery[] = "s.curtotalprice, ";									// 16:合計金額 
	$aryQuery[] = "To_char( s.dtmexpirationdate, 'YYYY/mm/dd') as dtmexpirationdate,";// 17:発注有効期限日
	$aryQuery[] = "s.strnote, ";											// 18:無効フラグ
	$aryQuery[] = "o.lngorderstatuscode, ";								// 状態
	$aryQuery[] = "s.lngrevisionno ";									// リビジョン番号
	$aryQuery[] = "FROM m_stock s LEFT JOIN m_order o ON o.lngorderno = s.lngorderno ";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "s.lngstockno = $lngstockno AND ";					// オーダコード
	$aryQuery[] = "s.bytinvalidflag = false";							// 無効フラグ

	$strQuery = implode("\n", $aryQuery );


	// クエリー実行
	$objDB->freeResult( $lngResultID );

	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );

	}


	$aryData2 = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );



	// 発注番号の取得
	$lngorderno = $aryData2["lngorderno"];

	if( $lngorderno != "" )
	{
		$aryData["strReceiveCode"] = fncGetMasterValue( "m_order", "lngorderno", "strordercode", $lngorderno , '',$objDB );
		$aryData["strReviseCode"]  = fncGetMasterValue( "m_order", "lngorderno", "strrevisecode", $lngorderno , '',$objDB );
	}

	// 関数 fncChangeDisplayNameで表示用データに変換(HEADER）
	$aryData2 = fncChangeData2( $aryData2 , $objDB );






	// プルダウンメニューの生成
	// 通貨
	$lngMonetaryUnitCode = fncGetMasterValue("m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitsign", $aryData2["lngmonetaryunitcode"], "", $objDB );


	$aryData["lngmonetaryunitcode"] 		= fncPulldownMenu( 0, $lngMonetaryUnitCode, '', $objDB );

	// レートタイプ
	$aryData["lngmonetaryratecode"]			= fncPulldownMenu( 1, $aryData2["lngmonetaryratecode"], '', $objDB );
	// 支払条件
	$aryData["lngpayconditioncode"]			= fncPulldownMenu( 2, $aryData2["lngpayconditioncode"], '', $objDB );


	// データのマージ
	$aryData = array_merge( $aryData2, $aryData);

	//ヘッダ備考の特殊文字変換
	$aryData["strnote"] = fncHTMLSpecialChars( $aryData["strnote"] );


	// 明細行
	$aryQuery = array();
	$aryQuery[] = "SELECT ";
	$aryQuery[] = "lngstockno, ";									// 仕入番号
	$aryQuery[] = "lngstockdetailno as lngOrderDetailNo, ";			// 仕入明細番号
	$aryQuery[] = "lngrevisionno, ";								// リビジョン番号
	$aryQuery[] = "strproductcode, ";								// 製品コード
	$aryQuery[] = "lngstocksubjectcode, ";							// 仕入科目コード
	$aryQuery[] = "lngstockitemcode, ";								// 仕入部品コード
	$aryQuery[] = "To_char(dtmdeliverydate, 'YYYY/mm/dd') as dtmdeliverydate, ";// 納品日
	$aryQuery[] = "lngdeliverymethodcode as lngcarriercode, ";			// 運搬方法
	$aryQuery[] = "lngdeliverymethodcode as lngdeliverymethodcode, ";	// 運搬方法
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
	$aryQuery[] = "FROM t_stockdetail ";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "lngstockno = $lngstockno";
	$aryQuery[] = " ORDER BY lngSortKey ASC";

	$strQuery = "";
	$strQuery = implode("\n", $aryQuery );


	$objDB->freeResult( $lngResultID );

	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}


	if( $lngResultNum = pg_num_rows( $lngResultID ) )
	{
		for( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryQueryResult[] = pg_fetch_array( $lngResultID, $i, PGSQL_ASSOC );
		}
	}


	$aryData["lngStockNo"] = $lngstockno;



	// 明細行をhidden値に変換する
	$aryData["strDetailHidden"] = fncDetailHidden_pc( $aryQueryResult ,"", $objDB);


	// 仕入科目
	$strStockSubjectCode = ( $aryQueryResult["strStockSubjectCode"] != "" ) ? $aryQueryResult["strStockSubjectCode"] : 0;

	$aryData["strStockSubjectCode"]   = fncPulldownMenu( 3, $strStockSubjectCode, '', $objDB );
	// 運搬方法
	$aryData["lngCarrierCode"]        = fncPulldownMenu( 6, 0, '', $objDB );
	// 製品単位
	$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
	// 荷姿単位
	$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );

	$aryData["MonetaryUnitDisabled"]  = " disabled";






	//-------------------------------------------------------------------------
	// 仕入状態のチェック
	//-------------------------------------------------------------------------
	// 申請中の場合
	if( $aryData["lngstockstatuscode"] == DEF_STOCK_APPLICATE )
	{
		fncOutputError( 712, DEF_WARNING, "", TRUE, "", $objDB );
	}
	// 締め済の場合
	if( $aryData["lngstockstatuscode"] == DEF_STOCK_CLOSED )
	{
		fncOutputError( 9062, DEF_WARNING, "", TRUE, "", $objDB );
	}


	//-------------------------------------------------------------------------
	// 状態コードが「 null / "" 」の場合、「0」を再設定
	//-------------------------------------------------------------------------
	$aryData["lngstockstatuscode"] = fncCheckNullStatus( $aryData["lngstockstatuscode"] );


	//---------------------------------------------------------------
	// 仕入状態の取得
	//---------------------------------------------------------------
	$aryData["lngOrderStatusCode_Display"] = fncGetMasterValue( "m_stockstatus", "lngstockstatuscode", "strstockstatusname", $aryData["lngstockstatuscode"], '', $objDB );




	// ワークフロー表示・非表示設定
	$aryData["visibleWF"] = "hidden";
	// 承認ルートの取得
	if( $lngorderno != "" )
	{
		$lngWorkflowOrderCode = fncGetMasterValue( "m_workflow", "strworkflowkeycode", "lngworkflowordercode", $lngorderno.":str", '', $objDB );
	}
	// *v2 ワークフローなし* $aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $lngWorkflowOrderCode );




	// 仕入先コード
	$aryData["lngCustomerCode"] = fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $aryData2["lngcustomercompanycode"], '', $objDB );

	// 仕入先名称
	$aryData["strCustomerName"] = fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplayname", $aryData2["lngcustomercompanycode"], '', $objDB );

	// 納品先コード
	$aryData["lngLocationCode"] = fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $aryData2["lngdeliveryplacecode"], '', $objDB );

	// 納品先名称
	$aryData["strLocationName"] = fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplayname", $aryData2["lngdeliveryplacecode"], '', $objDB );




	$aryData["RENEW"]                 = TRUE;
	$aryData["strMode"]               = "check";
	$aryData["strSessionID"]          = $aryData["strSessionID"];
	$aryData["strOrderCode_Editable"] = 'contenteditable="false"';
	$aryData["lngCalcCode"]           = DEF_CALC_KIRISUTE;
	$aryData["strPageCondition"]      = "renew";
	$aryData["lngFunctionCode"]       = DEF_FUNCTION_PC5;


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

	echo fncGetReplacedHtml( "pc/regist/parts.tmpl", $aryData ,$objAuth);

	return true;


?>