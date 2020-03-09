<?php

// ----------------------------------------------------------------------------
/**
*       売上管理  登録
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
*         ・登録処理
*         ・エラーチェック
*         ・登録処理完了後、登録完了画面へ
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
	require( LIB_ROOT."libcalc.php");
	require( SRC_ROOT."po/cmn/lib_po.php" );
	require( SRC_ROOT."pc/cmn/lib_pc.php" );
	require( SRC_ROOT."sc/cmn/lib_scp.php" );
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

	$lngInputUserCode = $objAuth->UserCode;

	// 600 売上管理
	if( !fncCheckAuthority( DEF_FUNCTION_SC0, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 601 売上管理（売上登録）
	if( fncCheckAuthority( DEF_FUNCTION_SCO1, $objAuth ) )
	{
		$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
	}


	// 明細行を除く
	for( $i = 0; $i < count( $_POST ); $i++ )
	{
		list( $strKeys, $strValues ) = each ( $_POST );

		if( $strKeys != "aryPoDitail")
		{
			$aryData[$strKeys] = $strValues;
		}
	}


	// displayCodeをcodeに変換する
	// fncChangeDataで新しい配列を作る
	$aryNewData = fncChangeData( $aryData, $objDB );


	// 明細行の配列を整備
	for($i=0; $i<count($_POST[aryPoDitail]); $i++ )
	{
		while( list( $strKeys, $strValues ) = each( $_POST[aryPoDitail][$i] ) )
		{
			$aryNewData["aryPoDitail"][$i][$strKeys] = ( $strValues == "" ) ? "null" : $strValues ;
		}
	}


	//-------------------------------------------------------------------------
	// ■ トランザクション開始
	//-------------------------------------------------------------------------
	$objDB->transactionBegin();



	//-------------------------------------------------------------------------
	// ■ DB -> SELECT : m_productprice
	//-------------------------------------------------------------------------
	// 同じ「仕入科目」「仕入部品」の場合に同じ単価があるか
	// m_productPriceに同じ値があるか？在った場合は配列に行番号を記憶！！
	for( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
		$aryNewData["lngMonetaryUnitCode"] = ( $aryNewData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryNewData["lngMonetaryUnitCode"];

		$lngmonetaryunitcode = "";
		$lngmonetaryunitcode = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryNewData["lngMonetaryUnitCode"] . ":str", '', $objDB );
		$strProductCode = "";
		$strProductCode = fncGetMasterValue( "m_product", "strproductcode", "lngproductno", $aryNewData["aryPoDitail"][$i]["strProductCode"]. ":str", '', $objDB );

		$arySelect = array();
		$arySelect[] = "SELECT ";
		$arySelect[] = "lngproductpricecode ";
		$arySelect[] = "FROM ";
		$arySelect[] = "m_productprice ";
		$arySelect[] = "WHERE ";
		$arySelect[] = "lngproductno = $strProductCode AND ";
		$arySelect[] = "lngsalesclasscode = ".$aryNewData["aryPoDitail"][$i]["lngSalesClassCode"]." AND ";
		$arySelect[] = "lngmonetaryunitcode = $lngmonetaryunitcode AND ";
		$arySelect[] = "curproductprice = ".$aryNewData["aryPoDitail"][$i]["curProductPrice"];

		$strSelect = implode("\n", $arySelect );

		if ( $lngResultID = $objDB->execute( $strSelect ) )
		{
			// 同一製品価格が見つからない場合、もしくは単位計上が製品単位計上の場合のみ行番号を記憶する
			if( pg_num_rows( $lngResultID ) == 0 and $aryNewData["aryPoDitail"][$i]["lngConversionClassCode"] == "gs" )
			{
				$aryM_ProductPrice[] = $i;		//行番号を記憶
			}
		}
		$objDB->freeResult( $lngResultID );

	}



	//-------------------------------------------------------------------------
	// ■ m_Sales のシーケンス番号を取得
	//-------------------------------------------------------------------------
	$sequence_m_sales = fncGetSequence( 'm_sales.lngSalesNo', $objDB );


	//-------------------------------------------------------------------------
	// ■ DB -> SELECT : m_Receive
	//-------------------------------------------------------------------------
	// 受注番号
	$strReceiveCode = $aryNewData["strReceiveCode"];

	if( $strReceiveCode != "null" )
	{
		$aryQuery = array();
		$aryQuery[] = "SELECT "; 
		$aryQuery[] = "r.lngReceiveNo, ";										// 1:受注番号
		$aryQuery[] = "r.lngReceiveStatusCode as lngSalesStatusCode ";			// 9:受注状態コード
		$aryQuery[] = "FROM m_Receive r ";
		$aryQuery[] = "WHERE r.strReceiveCode = '". $strReceiveCode . "' ";
		$aryQuery[] = "AND r.bytInvalidFlag = FALSE ";
		$aryQuery[] = "AND r.lngRevisionNo >= 0 ";
		$aryQuery[] = "AND r.lngRevisionNo = ( ";
		$aryQuery[] = "SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode  ";
		$aryQuery[] = "AND r2.strReviseCode = ( ";
		$aryQuery[] = "SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode ) ) ";
		$aryQuery[] = "AND 0 <= ( ";
		$aryQuery[] = "SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode ) ";

		$strQuery = "";
		$strQuery = implode( "\n", $aryQuery );

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
		if ( $lngResultNum == 1 )
		{
			$aryReceiveResult = $objDB->fetchArray( $lngResultID, 0 );
		}
		// 指定された発注が存在しない場合
		else
		{
			fncOutputError ( 403, DEF_ERROR, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		$lngReceiveCode = $aryReceiveResult["lngreceiveno"];
	}
	else
	{
		$lngReceiveCode = "null";
	}



	// 通貨をコードに変換
	$aryNewData["lngMonetaryUnitCode"] = ( $aryNewData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryNewData["lngMonetaryUnitCode"];
	$lngmonetaryunitcode = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryNewData["lngMonetaryUnitCode"] . ":str", '', $objDB );

	// 入力者コードを取得
	$lngUserCode = $objAuth->UserCode;

	// 伝票番号
	$strSlipCode = ( $aryNewData["strSlipCode"] != "null" ) ? "'".$aryNewData["strSlipCode"]."'" : "null";
	// 備考
	$strNote = ( $aryNewData["strNote"] != "null" ) ? "'".$aryNewData["strNote"]."'" : "null";



	// 顧客コードを取得
	$aryNewData["lngCustomerCode"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryNewData["lngCustomerCode"] . ":str", '', $objDB );

	//var_dump( $aryNewData["lngCustomerCode"] ); exit();


	//-------------------------------------------------------------------------
	// ■ 処理モードが「修正」の場合
	//-------------------------------------------------------------------------
	// リビジョン番号 // 売上コード
	if( $aryNewData["strProcMode"] == "renew" )
	{
		//-----------------------------------------------------------
		// 最新リバイズデータが「申請中」になっていないかどうか確認
		//-----------------------------------------------------------
		$strCheckQuery = "SELECT lngSalesNo, lngSalesStatusCode FROM m_Sales s WHERE s.strSalesCode = '" . $aryNewData["strSalesCode"] . "'";
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



		//-------------------------------------------------------------------------
		// 状態コードが「 null / "" 」の場合、「0」を再設定
		//-------------------------------------------------------------------------
		$lngSalesStatusCode = fncCheckNullStatus( $lngSalesStatusCode );

		//-------------------------------------------------------------------------
		// 状態コードが「0」の場合、「1」を再設定
		//-------------------------------------------------------------------------
		$lngSalesStatusCode = fncCheckZeroStatus( $lngSalesStatusCode );




		$strsalsecode = $aryNewData["strSalesCode"];

		// 修正の場合同じ仕入に対してリビジョン番号の最大値を取得する
		// リビジョン番号を現在の最大値をとるように修正する
		// その際にSELECT FOR UPDATEを使用して、同じ仕入に対してロック状態にする
		$strLockQuery = "SELECT lngRevisionNo FROM m_Sales WHERE strSalesCode = '" . $strsalsecode . "' FOR UPDATE";

		// ロッククエリーの実行
		list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );

		$lngMaxRevision = 0;

		if ( $lngLockResultNum )
		{
			for ( $i = 0; $i < $lngLockResultNum; $i++ )
			{
				$objRevision = $objDB->fetchObject( $lngLockResultID, $i );

				if ( $lngMaxRevision < $objRevision->lngrevisionno )
				{
					$lngMaxRevision = $objRevision->lngrevisionno;
				}
			}

			// リビジョン番号をインクリメント
			$lngrevisionno = $lngMaxRevision + 1;
		}
		else
		{
			$lngrevisionno = $lngMaxRevision;
		}

		// 結果IDを解放
		$objDB->freeResult( $lngLockResultID );
	}
	//-------------------------------------------------------------------------
	// ■ 処理モードが「登録」の場合
	//-------------------------------------------------------------------------
	else
	{
		// リビジョン番号を初期化
		$lngrevisionno = 0;

		// 売上番号の取得
		$strsalsecode = fncGetDateSequence( date( 'Y', strtotime( $aryNewData["dtmOrderAppDate"] ) ), date( 'm',strtotime( $aryNewData["dtmOrderAppDate"] ) ), "m_sales.lngSalesNo", $objDB );

		// 売上状態コードの取得
		$lngSalesStatusCode = ( $aryNewData["lngWorkflowOrderCode"] == 0 ) ? DEF_SALES_ORDER : DEF_SALES_APPLICATE;
	}

	//-------------------------------------------------------------------------
	// ■ DB -> INSERT : m_sales
	//-------------------------------------------------------------------------
	$aryQuery = array();
	$aryQuery[] = "INSERT INTO m_sales ( ";
	$aryQuery[] = "lngsalesno, ";											// 1:売上番号
	$aryQuery[] = "lngrevisionno, ";										// 2:リビジョン番号
	$aryQuery[] = "strsalescode, ";											// 3:売上コード(yymmxxx 年月連番で構成された7桁の番号)
	$aryQuery[] = "lngreceiveno, ";											// 4:受注番号
	$aryQuery[] = "dtmappropriationdate, ";									// 5:計上日
	$aryQuery[] = "lngcustomercompanycode, ";								// 6:顧客
	//$aryQuery[] = "lnggroupcode, ";											// 7:部門
	//$aryQuery[] = "lngusercode, ";											// 8:担当者
	$aryQuery[] = "lngsalesstatuscode, ";									// 9:売上状態コード
	$aryQuery[] = "lngmonetaryunitcode, ";									// 10:通貨単位コード
	$aryQuery[] = "lngmonetaryratecode, ";									// 11:通貨レートコード
	$aryQuery[] = "curconversionrate, ";									// 12:換算レート
	$aryQuery[] = "strslipcode, ";											// 13:伝票コード 
	$aryQuery[] = "curtotalprice, ";										// 14:合計金額
	$aryQuery[] = "strnote, ";												// 15:備考
	$aryQuery[] = "lnginputusercode, ";										// 16:入力者コード
	$aryQuery[] = "bytinvalidflag, ";										// 17:無効フラグ
	$aryQuery[] = "dtminsertdate";											// 18:登録日
	$aryQuery[] = " ) values ( ";
	$aryQuery[] = "$sequence_m_sales,";										// 1:売上番号
	$aryQuery[] = "$lngrevisionno, ";										// 2:リビジョン番号
	$aryQuery[] = "'$strsalsecode', ";										// 3:売上コード
	$aryQuery[] = "null, ";													// 4:受注番号
	//$aryQuery[] = "$lngReceiveCode, ";										// 4:受注番号
	$aryQuery[] = "'".$aryNewData["dtmOrderAppDate"]."',";					// 5:計上日
	$aryQuery[] = $aryNewData["lngCustomerCode"].", ";						// 6:顧客
	//$aryQuery[] = $aryNewData["lngInChargeGroupCode"].", ";					// 7:部門
	//$aryQuery[] = $aryNewData["lngInChargeUserCode"].", ";					// 8:担当者

	$aryQuery[] = $lngSalesStatusCode . ", ";								// 9:売上状態コード
/*
	if( $strReceiveCode == "null" )// 9:売上状態コード
	{
		$aryQuery[] = DEF_SALES_END . ", ";
	}
	else
	{
		$aryQuery[] = $lngSalesStatusCode . ", ";
	}
*/

	$aryQuery[] = "$lngmonetaryunitcode, ";									// 10:通貨単位コード
	$aryQuery[] = $aryNewData["lngMonetaryRateCode"].", ";					// 11:通貨レートコード
	$aryQuery[] = "'".$aryNewData["curConversionRate"]."', ";				// 12:換算レート
	$aryQuery[] = "$strSlipCode, ";											// 13:伝票コード
	$aryQuery[] = "'".$aryNewData["curAllTotalPrice"]."', ";				// 14:合計金額
	$aryQuery[] = "$strNote, ";												// 15:備考
	$aryQuery[] = "$lngUserCode, ";											// 16:入力者コード
	$aryQuery[] = "false, ";												// 17:無効フラグ
	$aryQuery[] = "'". fncGetDateTimeString() ."'";													// 18:登録日
	$aryQuery[] = ")";

	$strQuery = "";
	$strQuery = implode( "\n", $aryQuery );




	if( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}

	$objDB->freeResult( $lngResultID );


/*
	////////////////////////////////////
	//// 明細行番号が不明な行の対処 ////
	////////////////////////////////////
	$lngMaxDetailNo = 0;

	if ( $lngReceiveCode != "null" )
	{
		// 指定されている受注での最大値を求める
		$strQuery = "SELECT MAX(lngReceiveDetailNo) as maxDetailNo FROM t_ReceiveDetail WHERE lngReceiveNo = " . $lngReceiveCode;
		// 検索クエリーの実行
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if ( $lngResultNum == 1 )
		{
			$objResult = $objDB->fetchObject( $lngResultID, 0 );
			$lngMaxDetailNo = $objResult->maxdetailno;
		}
		else
		{
			fncOutputError ( 9051, DEF_ERROR, "情報の取得に失敗しました。", TRUE, "", $objDB );
		}
		$objDB->freeResult( $lngResultID );
	}
	else
	// 受注Noを指定しない仕入の場合
	{
		// 明細行の中で指定されている最大値を求める
		for ( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
		{
			if ( $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "null" 
				and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "" 
				and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "undefined" 
				and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] > $lngMaxDetailNo )
			{
				$lngMaxDetailNo = $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"];
			}
		}
	}
*/

	// 確認画面より税率のコードが渡されない場合はその時の計上日より再度取得しなおす
	// 明細行用に消費税コードを取得する
	// 消費税コード
	// 計上日よりその時の税率をもとめる
	$strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
		. "FROM m_tax "
		. "WHERE dtmapplystartdate <= '" . $aryNewData["dtmOrderAppDate"] . "' "
		. "AND dtmapplyenddate >= '" . $aryNewData["dtmOrderAppDate"] . "' "
		. "GROUP BY lngtaxcode, curtax "
		. "ORDER BY 3 ";
fncDebug( 'pc_regist_index2.txt', $strQuery, __FILE__, __LINE__);
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
fncDebug( 'pc_regist_index2.txt', $strQuery, __FILE__, __LINE__);
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



	for( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
/*
		// 仕入明細番号
		// 明細行番号がない場合（仕入で追加された明細行の場合）
		if ( $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "null" 
			or $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "" 
			or $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "undefined" )
		{
			$lngMaxDetailNo++;
			$aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] = $lngMaxDetailNo;
		}
*/

		// 備考
		$strDetailNote = ( $aryNewData["aryPoDitail"][$i]["strDetailNote"] == "null" )  ? "null" : "'".$aryNewData["aryPoDitail"][$i]["strDetailNote"]."'";

		// 換算区分コード
		$lngConversionClassCode = ( $aryNewData["aryPoDitail"][$i]["lngConversionClassCode"] == "gs" ) ? 1 : 2 ;


		// 税額がもしNULLならば、税区分、税抜金額より判断し、再計算する
		$lngCalcCode = DEF_CALC_KIRISUTE;

		// 売上時の通貨単位コードより処理対象桁数を設定
		if ( $lngmonetaryunitcode == DEF_MONETARY_YEN )
		{
			$lngDigitNumber = 0; // 日本円の場合は０桁
		}
		else
		{
			$lngDigitNumber = 2; // 日本円以外の場合は小数点以下２桁
		}

		// 税額
		if ( $aryNewData["aryPoDitail"][$i]["curTaxPrice"] == "null" )
		{
			// 税区分が非課税以外の場合
			if ( $aryNewData["aryPoDitail"][$i]["lngTaxClassCode"] != DEF_TAXCLASS_HIKAZEI )
			{
				$curTaxPrice = $aryNewData["aryPoDitail"][$i]["curTotalPrice"] * $curTax;
				// 端数処理を行う
				$curTaxPrice = fncCalcDigit( $curTaxPrice, $lngCalcCode, $lngDigitNumber );
			}
			else
			{
				$curTaxPrice = 0;
			}
		}
		else
		{
			$curTaxPrice = $aryNewData["aryPoDitail"][$i]["curTaxPrice"];
		}

		// 消費税コード
		// 非課税の場合はNULL値を設定
		if ( $aryNewData["aryPoDitail"][$i]["lngTaxClassCode"] == DEF_TAXCLASS_HIKAZEI )
		{
			$lngTaxCode_Detail = "null";
		}
		// 非課税以外の場合は共通の税コードを設定
		else
		{
			$lngTaxCode_Detail = $lngTaxCode;
		}

		// ソート番号
		$lngSortKey = $i + 1;

		//-----------------------------------------------------------
		// DB -> INSERT : t_salesdetail
		//-----------------------------------------------------------
		$aryQuery	= array();
		$aryQuery[] = "INSERT INTO t_salesdetail ( ";
		$aryQuery[] = "lngreceiveno,";													// 受注番号
		$aryQuery[] = "lngreceivedetailno,";											// 明細行番号

		$aryQuery[] = "lngsalesno, ";													// 1:売上番号
		$aryQuery[] = "lngsalesdetailno, ";												// 2:売上明細番号
		$aryQuery[] = "lngrevisionno, ";												// 3:リビジョン番号
		$aryQuery[] = "strproductcode, ";												// 4:製品コード
		$aryQuery[] = "lngsalesclasscode, ";											// 5:売上区分コード
		$aryQuery[] = "dtmdeliverydate, ";												// 6:納品日
		$aryQuery[] = "lngconversionclasscode, ";										// 7:換算区分コード
		$aryQuery[] = "curproductprice, ";												// 8:製品価格
		$aryQuery[] = "lngproductquantity, ";											// 9:製品数量
		$aryQuery[] = "lngproductunitcode, ";											// 10:製品単位コード
		$aryQuery[] = "lngtaxclasscode, ";												// 11:消費税区分コード
		$aryQuery[] = "lngtaxcode, ";													// 12:消費税コード
		$aryQuery[] = "curtaxprice, ";													// 13:消費税金額
		$aryQuery[] = "cursubtotalprice, ";												// 14:小計金額
		$aryQuery[] = "strnote, ";														// 15:備考
		$aryQuery[] = "lngSortKey ";													// 16:表示用ソートキー

		$aryQuery[] = " ) values ( ";

		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngReceiveNo"] . ",";				// 受注番号
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngReceiveDetailNo"] . ",";		// 明細行番号


		$aryQuery[] = "$sequence_m_sales, ";											// 1:売上番号
		$aryQuery[] = $i + 1 . ", ";													// 2:売上明細番号 行ごとの明細発注は持っている
		//$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] . ", ";		// 2:売上明細番号 行ごとの明細発注は持っている
		$aryQuery[] = "$lngrevisionno, ";												// 3:リビジョン番号
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["strProductCode"]."', ";		// 4:製品コード
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngSalesClassCode"].", ";			// 5:売上区分コード

		if ( $aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"] == "" or $aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"] == "null" )
		{
			$aryQuery[] = $aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"] . ", ";
		}
		else
		{
			$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"]."', ";		// 6:納品日
		}

		$aryQuery[] = "$lngConversionClassCode, ";										// 7:換算区分コード
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["curProductPrice"]."', ";		// 8:製品価格
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngGoodsQuantity"].", ";			// 9:製品数量
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngProductUnitCode"].", ";		// 10:製品単位コード
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngTaxClassCode"].", ";			// 11:消費税区分コード

		$aryQuery[] = "$lngTaxCode_Detail, ";											// 12:消費税コード

		$aryQuery[] = $curTaxPrice . ", ";			// 13:消費税金額

		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["curTotalPrice"]."', ";		// 14:小計金額
		$aryQuery[] = $strDetailNote. ", ";												// 15:備考
		$aryQuery[] = $lngSortKey. " ";													// 16:表示用ソートキー
		$aryQuery[] = " )";

		$strQuery = "";
		$strQuery = implode( "\n", $aryQuery );


		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			$objDB->close();
		}
		$objDB->freeResult( $lngResultID );

	}


	//require( LIB_DEBUGFILE );
	//fncDebug( 'lib_sc.txt', $aryNewData["aryPoDitail"], __FILE__, __LINE__);
	//exit();


	$lngCalcCode = DEF_CALC_KIRISUTE;		// 仕入時の端数処理は切捨て


	////////////////////////////////////////////////////
	//// この売上の登録により受注に対しての状態設定 ////
	////////////////////////////////////////////////////
	$lngReceiveNoBuff = null;

	for( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
		$lngReceiveNo = $aryNewData["aryPoDitail"][$i]["lngReceiveNo"];

		if( $lngReceiveNoBuff == $lngReceiveNo )
		{
			continue;
		}
		else
		{
			$lngReceiveNoBuff = null;
		}

		if( $lngReceiveNo != "" and $lngReceiveNo != "null" )
		{
			$lngResult = fncSalesSetStatus( $lngReceiveNo, $lngCalcCode, $objDB );

			if( $lngResult == 1 )
			{
				fncOutputError( 403, DEF_ERROR, "", TRUE, "", $objDB );
			}
			else if( $lngResult == 2 )
			{
				fncOutputError( 9061, DEF_ERROR, "", TRUE, "", $objDB );
			}
		}

		$lngReceiveNoBuff = $lngReceiveNo;
	}


	//-------------------------------------------------------------------------
	// ■ DB -> INSERT : m_productprice
	//-------------------------------------------------------------------------
	// m_productPrice 同じ値が入っていない場合
	if( count($aryM_ProductPrice) != 0)
	{
		for( $i = 0; $i < count( $aryM_ProductPrice ); $i++ )
		{
			// m_orderのシーケンスを取得
			$sequence_m_productprice = fncGetSequence( 'm_ProductPrice.lngProductPriceCode', $objDB );

			list ( $strKeys, $strValues ) = each( $aryM_ProductPrice );
			$curProductPrice = sprintf("%d", $aryNewData["aryPoDitail"][$strValues]["curProductPrice"]);

			$aryQuery = array();
			$aryQuery[] = "INSERT INTO m_productprice (";
			$aryQuery[] = "lngproductpricecode, ";												// 製品価格コード 
			$aryQuery[] = "lngproductno,";														// 製品番号
			$aryQuery[] = "lngsalesclasscode, ";												// 売上区分コード
			$aryQuery[] = "lngmonetaryunitcode,";												// 通貨単位コード
			$aryQuery[] = "curproductprice ";													// 製品価格 
			$aryQuery[] = ") VALUES (";
			$aryQuery[] = "$sequence_m_productprice, ";
			$aryQuery[] = "'".$aryNewData["aryPoDitail"][$strValues]["strProductCode"]."', ";
			$aryQuery[] = $aryNewData["aryPoDitail"][$strValues]["lngSalesClassCode"].",";
			$aryQuery[] = "$lngmonetaryunitcode,";
			$aryQuery[] = "'".$curProductPrice."'";
			$aryQuery[] = ")";

			$strQuery = "";
			$strQuery = implode("\n", $aryQuery );

			// echo "<br>$strQuery<br>";

			if ( !$lngResultID = $objDB->execute( $strQuery ) )
			{
				fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			}
			$objDB->freeResult( $lngResultID );
		}
	}




/*
	//-------------------------------------------------------------------------
	// ■ 承認処理
	//
	//   承認ルート
	//     ・0 : 承認ルートなし
	//-------------------------------------------------------------------------
	$lngWorkflowOrderCode = $aryNewData["lngWorkflowOrderCode"];	// 承認ルート

	$strWFName   = "売上 [No:" . $strsalsecode . "]";
	$lngSequence = $sequence_m_sales;
	$strDefFnc   = DEF_FUNCTION_SC1;

	$strProductCode       = $aryNewData["aryPoDitail"][0]["strProductCode"];
	$lngApplicantUserCode = fncGetMasterValue( "m_product", "strproductcode", "lnginchargeusercode", $strProductCode . ":str", '', $objDB );


	// 承認ルートが選択された場合
	if( $lngWorkflowOrderCode != 0 )
	{
		//---------------------------------------------------------------
		// DB -> INSERT : m_workflow
		//---------------------------------------------------------------
		// m_workflow のシーケンスを取得
		$lngworkflowcode = fncGetSequence( 'm_Workflow.lngworkflowcode', $objDB );
		$strworkflowname = $strWFName;

		$aryQuery   = array();
		$aryQuery[] = "INSERT INTO m_workflow (";
		$aryQuery[] = "lngworkflowcode, ";							// 1  : ワークフローコード
		$aryQuery[] = "lngworkflowordercode, ";						// 2  : ワークフロー順序コード
		$aryQuery[] = "strworkflowname, ";							// 3  : ワークフロー名称
		$aryQuery[] = "lngfunctioncode, ";							// 4  : 機能コード
		$aryQuery[] = "strworkflowkeycode, ";						// 5  : ワークフローキーコード 
		$aryQuery[] = "dtmstartdate, ";								// 6  : 案件発生日
		$aryQuery[] = "dtmenddate, ";								// 7  : 案件終了日
		$aryQuery[] = "lngapplicantusercode, ";						// 8  : 案件申請者コード
		$aryQuery[] = "lnginputusercode, ";							// 9  : 案件入力者コード
		$aryQuery[] = "bytinvalidflag, ";							// 10 : 無効フラグ
		$aryQuery[] = "strnote";									// 11 : 備考

		$aryQuery[] = " ) values (";
		$aryQuery[] = "$lngworkflowcode, ";							// 1  : ワークフローコード
		$aryQuery[] = ( $lngWorkflowOrderCode != "" ) ? $lngWorkflowOrderCode . ", " : "null, ";	// 2  : ワークフロー順序コード
		$aryQuery[] = "'$strworkflowname', ";						// 3  : ワークフロー名称
		$aryQuery[] = $strDefFnc . ", ";								// 4  : 機能コード
		$aryQuery[] = $lngSequence . ", ";							// 5  : ワークフローキーコード 
		$aryQuery[] = "now(), ";									// 6  : 案件発生日
		$aryQuery[] = "null, ";										// 7  : 案件終了日
		$aryQuery[] = $lngApplicantUserCode . ", ";					// 8  : 案件申請者コード
		$aryQuery[] = "$lngUserCode, ";								// 9  : 案件入力者コード
		$aryQuery[] = "false, ";									// 10 : 無効フラグ
		$aryQuery[] = "null";										// 11 : 備考
		$aryQuery[] = " )";

		$strQuery = "";
		$strQuery = implode( "\n", $aryQuery );


		// クエリ実行
		$lngResultID = $objDB->execute( $strQuery );


		// クエリ実行失敗の場合
		if( !$lngResultID )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
		}

		// 結果IDを解放
		$objDB->freeResult( $lngResultID );
		//---------------------------------------------------------------



		// 有効期限日の取得
		$lngLimitDate = fncGetMasterValue( "m_workfloworder" ,"lngworkflowordercode", "lnglimitdays", $lngWorkflowOrderCode ,"lngworkfloworderno = 1", $objDB );

		//echo "期限日：$lngLimitDate<br>";



		//---------------------------------------------------------------
		// DB -> INSERT : t_workflow
		//---------------------------------------------------------------
		$aryQuery   = array();
		$aryQuery[] = "INSERT INTO t_workflow (";
		$aryQuery[] = "lngworkflowcode, ";								// ワークフローコード
		$aryQuery[] = "lngworkflowsubcode, ";							// ワークフローサブコード
		$aryQuery[] = "lngworkfloworderno, ";							// ワークフロー順序番号
		$aryQuery[] = "lngworkflowstatuscode, ";						// ワークフロー状態コード
		$aryQuery[] = "strnote, ";										// 備考
		$aryQuery[] = "dtminsertdate, ";								// 登録日
		$aryQuery[] = "dtmlimitdate ";									// 期限日

		$aryQuery[] = ") values (";
		$aryQuery[] = "$lngworkflowcode, ";								// ワークフローコード
		$aryQuery[] = DEF_T_WORKFLOW_SUBCODE.", ";						// ワークフローサブコード
		$aryQuery[] = DEF_T_WORKFLOW_ORDERNO.", ";						// ワークフロー順序番号
		$aryQuery[] = DEF_T_WORKFLOW_STATUS.", ";						// ワークフロー状態コード
		$aryQuery[] = "'" . $aryNewData["strWorkflowMessage"] . "',";	// 11:備考
		$aryQuery[] = "now(), ";										// 登録日
		$aryQuery[] = "now() + (interval '$lngLimitDate day' )";		// 期限日
		$aryQuery[] = ")";

		$strQuery = "";
		$strQuery = implode("\n", $aryQuery );


		// クエリ実行
		$lngResultID = $objDB->execute( $strQuery );


		// クエリ実行失敗の場合
		if( !$lngResultID )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
		}

		// 結果IDを解放
		$objDB->freeResult( $lngResultID );
		//---------------------------------------------------------------



		//---------------------------------------------------------------
		// DB -> SELECT : m_workfloworder, m_user, m_authoritygroup
		//---------------------------------------------------------------
		// 承認者にメールを送る
		$arySelect = array();
		$arySelect[] = "SELECT u.strmailaddress, ";									// メールアドレス
		$arySelect[] = "u.bytMailTransmitFlag, ";									// メール配信許可フラグ
		$arySelect[] = "w.strworkflowordername, ";									// ワークフロー名
		$arySelect[] = "u.struserdisplayname ";										// 承認者
		$arySelect[] = "FROM m_workfloworder w, m_user u, m_authoritygroup a ";
		$arySelect[]= "WHERE w.lngworkflowordercode = ";
		$arySelect[] = $lngWorkflowOrderCode." AND ";
		$arySelect[] = "u.lngusercode = w.lnginchargecode AND ";
		$arySelect[] = "u.lngauthoritygroupcode = a.lngauthoritygroupcode ";
		$arySelect[] = "ORDER BY a.lngauthoritylevel DESC";

		$strSelect = "";
		$strSelect = implode("\n", $arySelect );

		// echo "$strSelect";


		// クエリ実行
		$lngResultID = $objDB->execute( $strSelect );


		// クエリ実行成功の場合
		if( $lngResultID )
		{
			$aryResult[] = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );
		}

		// 結果IDを解放
		$objDB->freeResult( $lngResultID );
		//---------------------------------------------------------------



		//---------------------------------------------------------------
		// DB -> SELECT : m_User
		//---------------------------------------------------------------
		// 入力者メールアドレスの取得
		$strUserMailQuery = "SELECT bytMailTransmitFlag, strMailAddress FROM m_User WHERE lngUserCode = " . $objAuth->UserCode;

		list( $lngUserMailResultID, $lngUserMailResultNum ) = fncQuery( $strUserMailQuery, $objDB );

		// クエリ実行成功の場合
		if( $lngUserMailResultNum == 1 )
		{
			$objResult = $objDB->fetchObject( $lngUserMailResultID, 0 );
			$bytInputUserMailTransmitFlag = $objResult->bytmailtransmitflag;
			$strInputUserMailAddress      = $objResult->strmailaddress;
		}
		// クエリ実行失敗の場合
		else
		{
			fncOutputError( 9051, DEF_ERROR, "データが異常です", TRUE, "po/regist/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}

		// 結果IDを解放
		$objDB->freeResult( $lngUserMailResultID );
		//---------------------------------------------------------------



		//---------------------------------------------------------------
		// メール送信
		//---------------------------------------------------------------
		// メール文面に必要なデータを配列 $aryMailData に格納
		//$aryMailData["strmailaddress"] = $aryResult[0]["strmailaddress"];	// 承認者メールアドレス

		// メール配信許可フラグが TRUE に設定されていない場合かつ、
		// 入力者（申請者）のメールアドレスが設定されていない場合は、メール送信しない
		if( $aryResult[0]["bytmailtransmitflag"] == "t" and $aryMailData["strmailaddress"] != "" and $strInputUserMailAddress != "" )
		{
			$aryMailData                       = array();
			//$strMailAddress                    = $aryResult[0]["strmailaddress"];			// 承認者メールアドレス
			$aryMailData["strmailaddress"]     = $aryResult[0]["strmailaddress"];			// 承認者メールアドレス
			$aryMailData["strWorkflowName"]    = $strworkflowname;							// 案件名
			//$aryMailData["strUserDisplayName"] = $aryResult[0]["struserdisplayname"];		// 承認依頼者
			$aryMailData["strUserDisplayName"] = $objAuth->UserDisplayName;					// 入力者（申請者）表示名
			$aryMailData["strURL"]             = LOGIN_URL;									// URL

			// 確認画面上のメッセージをメール内の備考欄として送信
			$aryMailData["strNote"] = $aryNewData["strWorkflowMessage"];


			// メールメッセージ取得
			list( $strSubject, $strTemplate ) = fncGetMailMessage( 807, $aryMailData, $objDB );

			// 管理者メールアドレス取得
			$strAdminMailAddress = fncGetCommonFunction( "adminmailaddress", "m_adminfunction", $objDB );

			// メール送信
			mail( $strMailAddress, $strSubject, $strTemplate, "From: $strInputUserMailAddress\nReturn-Path: " . $strAdminMailAddress . "\n" );
		}

		// 帳票出力表示切替
		$aryData["PreviewVisible"] = "hidden";
		//---------------------------------------------------------------
	}



	//-------------------------------------------------------------------------
	// ■ 即承認の場合
	//-------------------------------------------------------------------------
	// プレビューボタンを表示する
	else
	{
		// 帳票出力対応
		// 権限を持ってない場合はプレビューボタンを表示しない
		if( fncCheckAuthority( DEF_FUNCTION_LO2, $objAuth ) )
		{
			$aryData["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ORDER . "&strReportKeyCode=" . $lngSequence . "&bytCopyFlag=TRUE";

			// 帳票出力表示切替
			$aryData["PreviewVisible"] = "visible";
		}
		else
		{
			// 帳票出力表示切替
			$aryData["PreviewVisible"] = "hidden";
		}
	}
*/



	//-------------------------------------------------------------------------
	// ■ トランザクション完了
	//-------------------------------------------------------------------------
	$objDB->transactionCommit();



	//-------------------------------------------------------------------------
	// ■ 出力
	//-------------------------------------------------------------------------
	// テンプレートに反映する文字列
	$aryData["lngPONo"] = $strsalsecode;

	// 成功時戻り先のアドレス指定
	$aryData["strAction"] = "/sc/regist/index.php?strSessionID=";

	$objDB->close();

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "sc/finish/parts.tmpl" );
	header("Content-type: text/plain; charset=UTF-8");

	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	return true;

?>
