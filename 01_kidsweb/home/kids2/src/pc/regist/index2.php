<?php

// ----------------------------------------------------------------------------
/**
*       仕入管理  登録
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  Kuwagata
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
*	  ・税率取得処理修正（期間内の税率取得できない場合、最新期間の税率を取得する 20130531）
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
	require( SRC_ROOT."po/cmn/lib_pop.php" );



	//-------------------------------------------------------------------------
	// ■ オブジェクト生成
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();
	$objAuth = new clsAuth();


	//-------------------------------------------------------------------------
	// ■ パラメータ取得
	//-------------------------------------------------------------------------
	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];



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

	// 権限確認
	if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
	        fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	// 700 仕入管理
	if ( !fncCheckAuthority( DEF_FUNCTION_PC0, $objAuth ) )
	{
	        fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}


	// 明細行を除く
	for( $i = 0; $i < count( $_POST ); $i++ )
	{
		list( $strKeys, $strValues ) = each ( $_POST );
		if($strKeys != "aryPoDitail")
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
	// ■ m_Sales のシーケンス番号を取得
	//-------------------------------------------------------------------------
	$sequence_m_stock = fncGetSequence( 'm_stock.lngStockNo', $objDB );



	// 通貨をコードに変換
	$aryNewData["lngMonetaryUnitCode"] = ( $aryNewData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryNewData["lngMonetaryUnitCode"];

	$lngmonetaryunitcode = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryNewData["lngMonetaryUnitCode"] . ":str", '', $objDB );

	// 入力者コードを取得
	$lngUserCode = $objAuth->UserCode;


	// オーダ番号
	if( $aryNewData["strOrderCode"] != "null" )
	{
		$lngOrderCode = $aryData["lngOrderNo"];
	}
	else
	{
		$lngOrderCode = "null";
	}


	// 伝票番号
	$strSlipCode = ( $aryNewData["strSlipCode"] != "null" ) ? "'".$aryNewData["strSlipCode"]."'" : "null";



	// 仕入コード
	if( $aryNewData["strProcMode"] == "regist" )
	{
		$strstockcode = fncGetDateSequence( date('Y',strtotime( $aryNewData["dtmOrderAppDate"] ) ), date('m',strtotime( $aryNewData["dtmOrderAppDate"] ) ), "m_stock.strStockCode", $objDB );
	}
	else
	{
		$strstockcode = $aryNewData["lngStockCode"];
	}

	$strstockcode2 = ( $strstockcode != "" ) ? "'" . $strstockcode . "'" : "null";



	//-------------------------------------------------------------------------
	// ■ 処理モードが「登録」の場合
	//-------------------------------------------------------------------------
	//if( $aryNewData["strProcMode"] == "regist" && $aryNewData["lngRevisionNo"] == "null" )
	if( $aryNewData["strProcMode"] == "regist" )
	{
		$lngrevisionno = 0;

		// 仕入状態コードの取得
		$lngStockStatusCode = ( $aryNewData["lngWorkflowOrderCode"] == 0 ) ? DEF_STOCK_ORDER : DEF_STOCK_APPLICATE;
	}
	//-------------------------------------------------------------------------
	// ■ 処理モードが「修正」の場合
	//-------------------------------------------------------------------------
	else
	{
		//-----------------------------------------------------------
		// 最新リバイズデータが「申請中」になっていないかどうか確認
		//-----------------------------------------------------------
		$strCheckQuery = "SELECT lngStockNo, lngStockStatusCode FROM m_Stock s WHERE s.strStockCode = '" . $strstockcode . "'";
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
				fncOutputError( 711, DEF_WARNING, "", TRUE, "../pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			// 締め済の場合
			if( $lngSalesStatusCode == DEF_STOCK_CLOSED )
			{
				fncOutputError( 711, DEF_WARNING, "", TRUE, "", $objDB );
			}
		}

		// 結果IDを解放
		$objDB->freeResult( $lngCheckResultID );




		//-------------------------------------------------------------------------
		// 状態コードが「 null / "" 」の場合、「0」を再設定
		//-------------------------------------------------------------------------
		$lngStockStatusCode = fncCheckNullStatus( $lngStockStatusCode );

		//-------------------------------------------------------------------------
		// 状態コードが「0」の場合、「1」を再設定
		//-------------------------------------------------------------------------
		$lngStockStatusCode = fncCheckZeroStatus( $lngStockStatusCode );




		// 修正の場合同じ仕入に対してリビジョン番号の最大値を取得する
		//リビジョン番号を現在の最大値をとるように修正する　その際にSELECT FOR UPDATEを使用して、同じ仕入に対してロック状態にする
		$strLockQuery = "SELECT lngRevisionNo FROM m_Stock WHERE strStockCode = " . $strstockcode2 . " FOR UPDATE";

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

			$lngrevisionno = $lngMaxRevision + 1;
		}
		else
		{
			$lngrevisionno = $lngMaxRevision;
		}

		$objDB->freeResult( $lngLockResultID );
	}




	// 仕入先コードを取得
	$aryNewData["lngCustomerCode"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryNewData["lngCustomerCode"] . ":str", '', $objDB );

	// 納品場所コードを取得
	$aryNewData["lngLocationCode"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryNewData["lngLocationCode"] . ":str", '', $objDB );



	//-------------------------------------------------------------------------
	// ■ DB -> INSERT : m_stock
	//-------------------------------------------------------------------------
	$aryQuery = array();
	$aryQuery[] = "INSERT INTO m_stock( ";
	$aryQuery[] = "lngstockno, ";														// 1:仕入番号
	$aryQuery[] = "lngrevisionno, ";													// 2:リビジョン番号
	$aryQuery[] = "strstockcode, ";														// 3:仕入コード / yymmxxx 年月連番で構成された7桁の番号
	$aryQuery[] = "lngorderno, ";														// 4:発注番号 
	$aryQuery[] = "dtmappropriationdate, ";												// 5:仕入日
	$aryQuery[] = "lngcustomercompanycode, ";											// 6:仕入先コード 
	//$aryQuery[] = "lnggroupcode, ";														// 7:部門コード
	//$aryQuery[] = "lngusercode, ";														// 8:担当者コード 
	$aryQuery[] = "lngstockstatuscode, ";												// 9:仕入状態コード
	$aryQuery[] = "lngmonetaryunitcode, ";												// 10:通貨単位コード
	$aryQuery[] = "lngmonetaryratecode, ";												// 11:通貨レートコード
	$aryQuery[] = "curconversionrate, ";												// 12:適用レート 
	$aryQuery[] = "lngpayconditioncode, ";												// 13:支払い条件
	$aryQuery[] = "strslipcode, ";														// 14:伝票コード 
	$aryQuery[] = "curtotalprice, ";													// 15:合計金額
	$aryQuery[] = "lngdeliveryplacecode, ";												// 16:納品場所
	$aryQuery[] = "dtmexpirationdate, ";												// 17:製品到着日
	$aryQuery[] = "strnote, ";															// 18:備考
	$aryQuery[] = "lnginputusercode, ";													// 19:入力者コード 
	$aryQuery[] = "bytinvalidflag, ";													// 20:無効フラグ 
	$aryQuery[] = "dtminsertdate ";														// 21:登録日
	$aryQuery[] = " ) VALUES ( ";
	$aryQuery[] = "$sequence_m_stock, ";												// 1:仕入番号
	$aryQuery[] = "$lngrevisionno,";													// 2:リビジョン番号
	$aryQuery[] = "$strstockcode2, ";													// 3:仕入コード 
	$aryQuery[] = "$lngOrderCode, ";													// 4:発注番号
	$aryQuery[] = "'".$aryNewData["dtmOrderAppDate"]."', ";								// 5:計上日

	if ( $aryNewData["lngCustomerCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngCustomerCode"] . ", ";									// 6:仕入先コード
	}
	else
	{
		$aryQuery[] = "null, ";
	}
/*
	if ( $aryNewData["lngInChargeGroupCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngInChargeGroupCode"].", ";								// 7:部門コード
	}
	else
	{
		$aryQuery[] = "null, ";
	}
	if ( $aryNewData["lngInChargeUserCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngInChargeUserCode"].", ";								// 8:担当者コード 
	}
	else
	{
		$aryQuery[] = "null, ";
	}
*/

	$aryQuery[] = $lngStockStatusCode . ", ";												// 9:仕入状態コード
/*
	if ( $aryNewData["lngOrderStatusCode"] != "null" )
	{
		$aryQuery[] = $aryNewData["lngOrderStatusCode"].", ";								// 9:仕入状態コード
	}
	else
	{
		if( $lngOrderCode == "null" )
		{
			$aryQuery[] = DEF_STOCK_END.", ";
		}
		else
		{
			$aryQuery[] = "null, ";
		}
	}
*/

	if ( $lngmonetaryunitcode != "" )
	{
		$aryQuery[] = "$lngmonetaryunitcode, ";												// 10:通貨単位コード
	}
	else
	{
		$aryQuery[] = "null, ";
	}
	if ( $aryNewData["lngMonetaryRateCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngMonetaryRateCode"].", ";								// 11:通貨レートコード
	}
	else
	{
		$aryQuery[] = "null, ";
	}
	if ( $aryNewData["curConversionRate"] != "" )
	{
		$aryQuery[] = $aryNewData["curConversionRate"].", ";								// 12:適用レート 
	}
	else
	{
		$aryQuery[] = "null, ";
	}
	if ( $aryNewData["lngPayConditionCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngPayConditionCode"].", ";								// 13:支払い条件
	}
	else
	{
		$aryQuery[] = "null, ";
	}
	$aryQuery[] = "$strSlipCode, ";														// 14:伝票コード 
	$aryQuery[] = $aryNewData["curAllTotalPrice"].", ";									// 15:合計金額
	if ( $aryNewData["lngLocationCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngLocationCode"].", ";									// 16:納品場所
	}
	else
	{
		$aryQuery[] = "null, ";
	}

	if( $aryNewData["dtmExpirationDate"] != "" and $aryNewData["dtmExpirationDate"] != "null" )	// 17:製品到着日
	{
		$aryQuery[] = "'".$aryNewData["dtmExpirationDate"]."', ";
	}
	else
	{
		$aryQuery[] = "null, ";
	}

	// 仕入の状態についてはこの時はチェックを行わず、発注番号を指定しない仕入の場合のみ、「納品済」とする
	if( $aryNewData["strNote"] != "null" )
	{
		$aryQuery[] = "'".$aryNewData["strNote"]."', ";									// 18:備考
	}
	else
	{
		$aryQuery[] = "null, ";
	}

	$aryQuery[] = "$lngUserCode, ";														// 19:入力者コード
	$aryQuery[] = "false, ";															// 20:無効フラグ 
	$aryQuery[] = "now()";																// 21:登録日
	$aryQuery[] = " )";

	$strQuery = "";
	$strQuery = implode( "\n", $aryQuery );


	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}

	$objDB->freeResult( $lngResultID );



	// 確認画面より税率のコードが渡されない場合はその時の仕入日より再度取得しなおす
	// 明細行用に消費税コードを取得する
	// 消費税コード
	// 仕入日よりその時の税率をもとめる
	$strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
		. "FROM m_tax "
		. "WHERE dtmapplystartdate <= '" . $aryNewData["dtmOrderAppDate"] . "' "
		. "AND dtmapplyenddate >= '" . $aryNewData["dtmOrderAppDate"] . "' "
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
		// 最新期間の税率情報を取得する
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


	// 明細行番号が不明な行の対処
	$lngMaxDetailNo = 0;
	if ( $lngOrderCode != "null" )
	{
		// 指定されている発注での最大値を求める
		$strQuery = "SELECT MAX(lngOrderDetailNo) as maxDetailNo FROM t_OrderDetail WHERE lngOrderNo = " . $lngOrderCode;
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
	// 発注Noを指定しない仕入の場合
	{
		// 明細行の中で指定されている最大値を求める
		for ( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
		{
			if ( $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "null" and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "" 
				and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] > $lngMaxDetailNo )
			{
				$lngMaxDetailNo = $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"];
			}
		}
	}

	for( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
		// 仕入明細番号
		// 明細行番号がない場合（仕入で追加された明細行の場合）
		if ( $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "null" 
			or $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "" 
			or $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "undefined" )
		{
			$lngMaxDetailNo++;
			$aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] = $lngMaxDetailNo;
		}

		// 備考
		$strDetailNote = ( $aryNewData["aryPoDitail"][$i]["strDetailNote"] == "null" )  ? "null" : "'".$aryNewData["aryPoDitail"][$i]["strDetailNote"]."'";

		// 換算区分コード
		$lngConversionClassCode = ( $aryNewData["aryPoDitail"][$i]["lngConversionClassCode"] == "gs" ) ? 1 : 2 ;


		// 税額がもしNULLならば、税区分、税抜金額より判断し、再計算する
		$lngCalcCode = DEF_CALC_KIRISUTE;

		// 仕入時の通貨単位コードより処理対象桁数を設定
		if ( $lngmonetaryunitcode == DEF_MONETARY_YEN )
		{
			$lngDigitNumber = 0;		// 日本円の場合は０桁
		}
		else
		{
			$lngDigitNumber = 2;		// 日本円以外の場合は小数点以下２桁
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

		// 金型番号の設定内容を取得（設定がない場合で金型指定だった場合は新規に金型番号を取得）
		if( $aryNewData["aryPoDitail"][$i]["strSerialNo"] == "null" or $aryNewData["aryPoDitail"][$i]["strSerialNo"] == "" )
		{
			$strSerialNo = "";
			// 仕入科目が４３３（金型海外償却）、仕入部品が１（Injection Mold）の場合
			// 仕入科目が４３１（金型償却高）、　仕入部品が８（金型）の場合
			if ( ( $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"] == DEF_MOLD_STOCK_SUBJECT 
				and $aryNewData["aryPoDitail"][$i]["strStockItemCode"] == DEF_MOLD_STOCK_ITEM )
				or ( $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"] == DEF_MOLD_STOCK_SUBJECT_ADD 
				and $aryNewData["aryPoDitail"][$i]["strStockItemCode"] == DEF_MOLD_STOCK_ITEM_ADD ) )
			{
				$strSerialNo = fncGetMoldNo( $aryNewData["aryPoDitail"][$i]["strProductCode"], $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"], $aryNewData["aryPoDitail"][$i]["strStockItemCode"], $objDB );
			}
			else
			// 指定されている仕入科目、仕入部品が金型番号使用でない場合は金型番号箇所にはNULL指定
			{
				$strSerialNo = "";
			}
		}
		else
		{
			// 仕入科目が４３３（金型海外償却）、仕入部品が１（Injection Mold）の場合
			// 仕入科目が４３１（金型海外償却）、仕入部品が８（金型）の場合
			if ( ( $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"] == DEF_MOLD_STOCK_SUBJECT 
				and $aryNewData["aryPoDitail"][$i]["strStockItemCode"] == DEF_MOLD_STOCK_ITEM )
				or ( $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"] == DEF_MOLD_STOCK_SUBJECT_ADD 
				and $aryNewData["aryPoDitail"][$i]["strStockItemCode"] == DEF_MOLD_STOCK_ITEM_ADD ) )
			{
				// 指定された金型番号が更新元で指定されていた場合はそのままの金型番号を設定する
				$strSerialNo = "";
				$strSerialNo = $aryNewData["aryPoDitail"][$i]["strSerialNo"];
			}
			else
			{
				$strSerialNo = "";
			}
		}
		// SQL文対応
		$strSerialNo = ( $strSerialNo != "" ) ? "'$strSerialNo'" : "null";

		// ソート番号
		$lngSortKey = $i + 1;



		//-----------------------------------------------------------
		// DB -> INSERT : t_stockdetail
		//-----------------------------------------------------------
		$aryQuery	= array();
		$aryQuery[] = "INSERT INTO t_stockdetail ( ";
		$aryQuery[] = "lngstockno, ";													// 1:仕入番号
		$aryQuery[] = "lngstockdetailno, ";												// 2:仕入明細番号
		$aryQuery[] = "lngrevisionno, ";												// 3:リビジョン番号 
		$aryQuery[] = "strproductcode, ";												// 4:製品コード
		$aryQuery[] = "lngstocksubjectcode, ";											// 5:仕入科目コード
		$aryQuery[] = "lngstockitemcode, ";												// 6:仕入部品コード
		$aryQuery[] = "dtmdeliverydate, ";												// 7:納品日
		$aryQuery[] = "lngdeliverymethodcode, ";										// 運搬方法
		$aryQuery[] = "lngconversionclasscode, ";										// 8:換算区分コード / 1：単位計上/ 2：荷姿単位計上
		$aryQuery[] = "curproductprice, ";												// 9:製品価格
		$aryQuery[] = "lngproductquantity, ";											// 10:製品数量
		$aryQuery[] = "lngproductunitcode, ";											// 11:製品単位コード
		$aryQuery[] = "lngtaxclasscode, ";												// 12:消費税区分コード
		$aryQuery[] = "lngtaxcode, ";													// 13:消費税コード
		$aryQuery[] = "curtaxprice, ";													// 14:税額
		$aryQuery[] = "cursubtotalprice, ";												// 15:小計金額 / 税抜小計金額
		$aryQuery[] = "strnote, ";														// 16:備考
		$aryQuery[] = "strmoldno, ";													// 17:金型番号
		$aryQuery[] = "lngSortKey ";													// 18:表示用ソートキー
		$aryQuery[] = " ) VALUES ( ";
		$aryQuery[] = "$sequence_m_stock, ";											// 1:仕入番号
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] . ", ";		// 2:仕入明細番号 行ごとの明細発注は持っている
		$aryQuery[] = "$lngrevisionno, ";												// 3:リビジョン番号
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["strProductCode"]."', ";		// 4:製品コード
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"].", ";		// 5:仕入科目コード
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["strStockItemCode"].", ";			// 6:仕入部品コード
		if( $aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"] != "" and $aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"] != "null" )
																						// 7:納期
		{
			$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"]."', ";
		}
		else
		{
			$aryQuery[] = "null, ";
		}
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngCarrierCode"].", ";
		$aryQuery[] = "$lngConversionClassCode, ";										// 8:換算区分コード / 1：単位計上/ 2：荷姿単位計上
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["curProductPrice"]."', ";		// 9:製品価格
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngGoodsQuantity"].", ";			// 10:製品数量
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngProductUnitCode"].", ";		// 11:製品単位コード
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngTaxClassCode"].", ";			// 12:消費税区分コード
		$aryQuery[] = "$lngTaxCode_Detail, ";											// 13:消費税コード
		$aryQuery[] = "$curTaxPrice, ";													// 14:税額
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["curTotalPrice"]."', ";		// 15:小計金額 / 税抜小計金額
		$aryQuery[] = "$strDetailNote, ";												// 16:備考
		$aryQuery[] = $strSerialNo . ", ";												// 17:金型番号
		$aryQuery[] = $lngSortKey . " ";												// 18:表示用ソートキー
		$aryQuery[] = " )";

		$strQuery = "";
		$strQuery = implode( "\n", $aryQuery );


		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
		}

		$objDB->freeResult( $lngResultID );
	}


	// 仕入時の端数処理は切捨て
	$lngCalcCode = DEF_CALC_KIRISUTE;

	////////////////////////////////////////////////////////
	//// この仕入の登録により発注に対しての状態チェック ////
	////////////////////////////////////////////////////////
	if ( $lngOrderCode != "" and $lngOrderCode != "null" )
	{
		$lngResult = fncStockSetStatus ( $lngOrderCode, $lngCalcCode, $objDB );
		if ( $lngResult == 1 )
		{
			fncOutputError ( 707, DEF_ERROR, "", TRUE, "", $objDB );
		}
		else if ( $lngResult == 2 )
		{
			fncOutputError ( 9061, DEF_ERROR, "", TRUE, "", $objDB );
		}
	}


	if( !fncCheckSetProduct ( $aryNewData["aryPoDitail"], $lngmonetaryunitcode, $objDB ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}




/*
	//-------------------------------------------------------------------------
	// ■ 承認処理
	//
	//   承認ルート
	//     ・0 : 承認ルートなし
	//-------------------------------------------------------------------------
	$lngWorkflowOrderCode = $aryNewData["lngWorkflowOrderCode"];	// 承認ルート

	$strWFName   = "仕入 [No:" . $strstockcode . "]";
	$lngSequence = $sequence_m_stock;
	$strDefFnc   = DEF_FUNCTION_PC1;

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
		$aryQuery[] = $strDefFnc . ", ";							// 4  : 機能コード
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
	// 仕入れ番号を発行
	$aryData["lngPONo"] = $strstockcode;

	// 成功時戻り先のアドレス指定
	$aryData["strAction"] = "/pc/regist/index.php?strSessionID=";

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "pc/finish/parts.tmpl" );
	header("Content-type: text/plain; charset=EUC-JP");

	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	return true;

?>
