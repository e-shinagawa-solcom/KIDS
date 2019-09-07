<?php

// ----------------------------------------------------------------------------
/**
*       発注管理  登録
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
*
*/
// ----------------------------------------------------------------------------



	// 読み込み
	include('conf.inc');
	require (LIB_FILE);
	// require(SRC_ROOT."po/cmn/lib_po.php");
	// require(SRC_ROOT."po/cmn/lib_pop.php");
	require (SRC_ROOT."po/cmn/lib_por.php");

	//var_dump($_POST);
	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	
	$aryData["strSessionID"] = $_POST["strSessionID"];
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	$objDB->open("", "", "", "");
	
	// 文字列チェック
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );



	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	
	$lngInputUserCode = $objAuth->UserCode;
	
	// 権限確認
	if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	// 500	発注管理
	if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	
	// 501 発注管理（発注登録）
	if ( !fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	
	// 508 発注管理（商品マスタダイレクト修正）
	if( !fncCheckAuthority( DEF_FUNCTION_PO8, $objAuth ) )
	{
		$aryData["popenview"] = 'hidden';
	}

	// 更新データ取得
	$aryUpdate["lngorderno"]           = $_POST["lngOrderNo"];
	$aryUpdate["lngrevisionno"]        = $_POST["lngRevisionNo"];
	$aryUpdate["dtmexpirationdate"]    = $_POST["dtmExpirationDate"];
	$aryUpdate["lngpayconditioncode"]  = $_POST["lngPayConditionCode"];
	$aryUpdate["lngdeliveryplacecode"] = $_POST["lngLocationCode"];
	$aryUpdate["lngorderstatuscode"]   = 2;
	for($i = 0; $i < count($_POST["aryDetail"]); $i++){
		$aryUpdateDetail[$i]["lngorderdetailno"]       = $_POST["aryDetail"][$i]["lngOrderDetailNo"];
		$aryUpdateDetail[$i]["lngsortkey"]             = $_POST["aryDetail"][$i]["lngSortKey"];
		$aryUpdateDetail[$i]["lngdeliverymethodcode"]  = $_POST["aryDetail"][$i]["lngDeliveryMethodCode"];
		$aryUpdateDetail[$i]["lngproductunitcode"]     = $_POST["aryDetail"][$i]["lngProductUnitCode"];
		$aryUpdateDetail[$i]["lngorderno"]             = $_POST["aryDetail"][$i]["lngOrderNo"];
		$aryUpdateDetail[$i]["lngrevisionno"]          = $_POST["aryDetail"][$i]["lngRevisionNo"];
		$aryUpdateDetail[$i]["lngstocksubjectcode"]    = $_POST["aryDetail"][$i]["lngStockSubjectCode"];
		$aryUpdateDetail[$i]["lngstockitemcode"]       = $_POST["aryDetail"][$i]["lngStockItemCode"];
		$aryUpdateDetail[$i]["lngmonetaryunitcode"]    = $_POST["aryDetail"][$i]["lngMonetaryUnitCode"];
		$aryUpdateDetail[$i]["lngcustomercompanycode"] = $_POST["aryDetail"][$i]["lngCustomerCompanyCode"];
	}
	
	$objDB->transactionBegin();
	// 発注マスタ更新
	if(!fncUpdateOrder($aryUpdate, $aryUpdateDetail, $objDB)) { return false; }
	// 発注明細更新
	if(!fncUpdateOrderDetail($aryUpdate, $aryUpdateDetail, $objDB)) { return false; }
	// 更新後発注マスタ/発注明細取得
	
	// $aryOrder = fncGetOrder($aryUpdate["lngorderno"], $objDB)[0];
	// $aryDetail = fncGetOrderDetail($aryUpdate["lngorderno"], $objDB);
	// 発注書マスタ更新
	if(!fncUpdatePurchaseOrder($aryUpdate, $aryUpdateDetail, $objAuth, $objDB)){ return false; }

	// TODO:あとでコミットに変更する
	$objDB->transactionRollback();
	echo fncGetReplacedHtml( "po/regist/parts2.tmpl", $aryData ,$objAuth);
	




	// 明細行を除く
	for( $i = 0; $i < count( $_POST ); $i++ )
	{
		list( $strKeys, $strValues ) = each ( $_POST );
		if($strKeys != "aryPoDitail")
		{
			$aryData[$strKeys] = $strValues;
		}
	}
	
	//var_dump( $aryData );
	//exit();

	// displayCodeをcodeに変換する
	// fncChangeDataで新しい配列を作る
	$aryNewData = fncChangeData( $aryData, $objDB );
	
	
	// 明細行の配列を整備
	for($i=0; $i<count($_POST[aryPoDitail]); $i++ )
	{
		while( list( $strKeys, $strValues ) = each( $_POST[aryPoDitail][$i] ) )
		{
			if( $strKeys == "strProductCode")
			{
				$aryNewData["aryPoDitail"][$i][$strKeys] = $strValues; //fncDispalayToCode
			}
			else
			{
				$aryNewData["aryPoDitail"][$i][$strKeys] = ( $strValues == "" ) ? "null" : $strValues ;
			}
		}
	}


	// ワークフローメッセージが空欄の場合
	$aryNewData["strWorkflowMessage"] = ( $aryNewData["strWorkflowMessage"] == "null" ) ? "" : $aryNewData["strWorkflowMessage"];



	// 修正処理の場合、以下をチェック
	if ( $aryNewData["strProcMode"] == "renew")
	{
		// 確認画面を表示する際に最新リバイズの発注が「申請中」になっていないかどうかの確認を行う
		$strCheckQuery = "SELECT lngOrderNo, lngOrderStatusCode FROM m_Order o WHERE o.strOrderCode = '" . $aryNewData["strOrderCode"] . "'";
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
			$lngOrderNo         = $objResult->lngorderno;
			$lngorderstatuscode = $objResult->lngorderstatuscode;

			if ( $lngorderstatuscode == DEF_ORDER_APPLICATE )
			{
				fncOutputError ( 505, DEF_WARNING, "", TRUE, "../po/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
		}

		$objDB->freeResult( $lngCheckResultID );


		//-------------------------------------------------------------------------
		// 再承認のため、ステータスを「申請中」に変更
		//-------------------------------------------------------------------------
		if( $aryNewData["lngWorkflowOrderCode"] == 0 )
		{
			$lngorderstatuscode = DEF_ORDER_ORDER;

			// 仕入チェック
			$arySql = array();
			$arySql[] = "select count(*) as count";
			$arySql[] = "from";
			$arySql[] = "	m_stock ms";
			//--		left join t_stockdetail tsd on tsd.lngstockno = ms.lngstockno
			$arySql[] = "where";
			$arySql[] = "ms.lngorderno in ";
			$arySql[] = "(";
			$arySql[] = "	select mo1.lngorderno";
			$arySql[] = "	from";
			$arySql[] = "		m_order mo1";
			$arySql[] = "	where";
			$arySql[] = "		mo1.strordercode = '" . $aryNewData["strOrderCode"] . "'";
			$arySql[] = ")";
			$arySql[] = "and ms.bytinvalidflag = false";
			$arySql[] = "AND ms.lngRevisionNo = (";
			$arySql[] = "	SELECT MAX( s1.lngRevisionNo ) FROM m_stock s1 WHERE s1.bytInvalidFlag = false and s1.strStockCode = ms.strStockCode)";
			$arySql[] = "	AND 0 <= (";
			$arySql[] = "		SELECT MIN( s2.lngRevisionNo ) FROM m_stock s2 WHERE s2.bytInvalidFlag = false and s2.strStockCode = ms.strStockCode )";

			$strQuery = implode("\n", $arySql);
			// ＤＢ問い合わせ
			list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if ( $lngResultNum == 1 )
			{
				$objResult	= $objDB->fetchObject( $lngResultID, 0 );
				// 一個以上、仕入データがあれば、「納品中」とする
				if( 1 <= (int)$objResult->count)
				{
					$lngorderstatuscode = DEF_ORDER_DELIVER;
				}
			}
		}

		// 申請中
		else
		{
			$lngorderstatuscode = DEF_ORDER_APPLICATE;
		}


		//-------------------------------------------------------------------------
		// 状態コードが「 null / "" 」の場合、「0」を再設定
		//-------------------------------------------------------------------------
		$lngorderstatuscode = fncCheckNullStatus( $lngorderstatuscode );

		//-------------------------------------------------------------------------
		// 状態コードが「0」の場合、「1」を再設定
		//-------------------------------------------------------------------------
		$lngorderstatuscode = fncCheckZeroStatus( $lngorderstatuscode );
	}


	// m_orderのシーケンスを取得
	$sequence_m_order = fncGetSequence( 'm_Order.lngOrderNo', $objDB );

	//トランザクション開始
	$objDB->transactionBegin();
	// echo "トランザクション実行<br>";
	
	//リバイズコードの取得
	// 修正画面からの場合

	if ( $aryNewData["strProcMode"] == "renew")
	{
		$strOrderCode = $aryNewData["strOrderCode"];

		// 修正の場合同じ発注に対してリビジョン番号、リバイズコードの最大値を取得する
		/////   リビジョン番号、リバイズコードを現在の最大値をとるように修正する　その際にSELECT FOR UPDATEを使用して、同じ発注に対してロック状態にする

		$strLockQuery = "SELECT lngRevisionNo, strReviseCode FROM m_Order WHERE strOrderCode = '" . $strOrderCode . "' FOR UPDATE";

		// ロッククエリーの実行
		list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );

		$lngMaxRevision = 0;
		$strMaxRevise   = 0;
		if ( $lngLockResultNum )
		{
			for ( $i = 0; $i < $lngLockResultNum; $i++ )
			{
				$objResult = $objDB->fetchObject( $lngLockResultID, $i );
				if ( $lngMaxRevision < $objResult->lngrevisionno )
				{
					$lngMaxRevision = $objResult->lngrevisionno;
				}
				if ( $strMaxRevise < intval($objResult->strrevisecode) )
				{
					$strMaxRevise = intval($objResult->strrevisecode);
				}
			}
		}
		$objDB->freeResult( $lngLockResultID );
		$lngRevisionNo = $lngMaxRevision + 1;
		$strReviseCode = sprintf("%02d", $strMaxRevise + 1);
	}
	else
	{
		// 登録の場合
		$strReviseCode = "00";
		$lngRevisionNo = 0;
		$strOrderCode = fncGetDateSequence( date('Y', strtotime( $aryNewData["dtmOrderAppDate"] ) ), date('m',strtotime( $aryNewData["dtmOrderAppDate"] ) ), "m_order.strOrderCode", $objDB );

		//「０」の場合は発注：その他は申請中
		$lngorderstatuscode = ( $aryNewData["lngWorkflowOrderCode"] == 0) ? DEF_ORDER_ORDER : DEF_ORDER_APPLICATE;
	}


	// m_order HEADER情報のインサート
	$aryNewData["lngMonetaryUnitCode"] = ( $aryNewData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryNewData["lngMonetaryUnitCode"];
	$lngmonetaryunitcode = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryNewData["lngMonetaryUnitCode"] . ":str", '', $objDB );
	
	//echo "lngWorkflowOrderCode : ".$aryNewData["lngWorkflowOrderCode"]."<br>";


	$strNote = ( $aryNewData["strNote"] == "null" ) ? "null" : "'".$aryNewData["strNote"]."'";
	$lngMonetaryRateCode = ( $aryNewData["lngMonetaryRateCode"] == "null" ) ? "null" : "'".$aryNewData["lngMonetaryRateCode"]."'";
	$curConversionRate = ( $aryNewData["curConversionRate"] == "null" ) ? "null" : "'".$aryNewData["curConversionRate"]."'";




	// 仕入先コードを取得
	$aryNewData["lngCustomerCode"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryNewData["lngCustomerCode"] . ":str", '', $objDB );

	// 納品場所コードを取得
	$aryNewData["lngLocationCode"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryNewData["lngLocationCode"] . ":str", '', $objDB );



	$aryQuery = array();
	$aryQuery[] = "INSERT INTO m_order (";
	$aryQuery[] = "lngorderno, ";													// 1:発注番号
	$aryQuery[] = "lngrevisionno, ";												// 2:リビジョン番号
	$aryQuery[] = "strordercode, ";													// 3:発注コード．
	$aryQuery[] = "strrevisecode, ";												// 4:リバイスコード
	$aryQuery[] = "dtmappropriationdate, ";											// 5:計上日
	$aryQuery[] = "lngcustomercompanycode, ";										// 6:会社コード(仕入先)
	//$aryQuery[] = "lnggroupcode, ";													// 7:グループコード（部門）
	//$aryQuery[] = "lngusercode, ";													// 8:ユーザコード（担当者）
	$aryQuery[] = "lngorderstatuscode, ";											// 9:発注状態
	$aryQuery[] = "lngmonetaryunitcode, ";											// 10:通貨単位コード
	$aryQuery[] = "lngmonetaryratecode, ";											// 11:通貨レートコード
	$aryQuery[] = "curconversionrate, ";											// 12:換算レート
	$aryQuery[] = "lngpayconditioncode, ";											// 13:支払条件
	$aryQuery[] = "curtotalprice, ";												// 14:合計金額
	$aryQuery[] = "lngdeliveryplacecode, ";											// 15:納品場所コード
	$aryQuery[] = "dtmexpirationdate, ";											// 16:発注有効期限日
	$aryQuery[] = "strnote, ";														// 17:備考
	$aryQuery[] = "lnginputusercode, ";												// 18:入力者コード
	$aryQuery[] = "bytinvalidflag, ";												// 19:無効フラグ
	$aryQuery[] = "dtminsertdate ";													// 20:登録日
	$aryQuery[] = ") values (";
	$aryQuery[] = "$sequence_m_order, ";											// 1:発注番号
	$aryQuery[] = "$lngRevisionNo, ";			 									// 2:リビジョン番号
	$aryQuery[] = "'".$strOrderCode."', ";											// 3:発注コード．
	$aryQuery[] = "'$strReviseCode',";												// 4:リバイスコード
	$aryQuery[] = "'".$aryNewData["dtmOrderAppDate"]."', ";							// 5:計上日
	if ( $aryNewData["lngCustomerCode"] != "" )
	{
		$aryQuery[] =  $aryNewData["lngCustomerCode"] . ", ";						// 6:会社コード(仕入先)
	}
	else
	{
		$aryQuery[] = "null, ";														// 6:会社コード(仕入先)
	}
	/*
	if ( $aryNewData["lngInChargeGroupCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngInChargeGroupCode"].", ";						// 7:グループコード（部門）
	}
	else
	{
		$aryQuery[] = "null, ";														// 7:グループコード（部門）
	}
	if ( $aryNewData["lngInChargeUserCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngInChargeUserCode"].",";						// 8:ユーザコード（担当者）
	}
	else
	{
		$aryQuery[] = "null, ";														// 8:ユーザコード（担当者）
	}
	*/
	if ( $lngorderstatuscode != "" )
	{
		$aryQuery[] = "$lngorderstatuscode, ";										// 9:発注状態
	}
	else
	{
		$aryQuery[] = "null, ";														// 9:発注状態
	}
	if ( $lngmonetaryunitcode != "" )
	{
		$aryQuery[] = "$lngmonetaryunitcode, ";										// 10:通貨単位コード
	}
	else
	{
		$aryQuery[] = "null, ";														// 10:通貨単位コード
	}
	if ( $lngMonetaryRateCode != "" )
	{
		$aryQuery[] = "$lngMonetaryRateCode, ";										// 11:通貨レートコード
	}
	else
	{
		$aryQuery[] = "null, ";														// 11:通貨レートコード
	}
	if ( $curConversionRate != "" )
	{
		$aryQuery[] = "$curConversionRate, ";										// 12:換算レート
	}
	else
	{
		$aryQuery[] = "null, ";														// 12:換算レート
	}
	if ( $aryNewData["lngPayConditionCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngPayConditionCode"] . ", ";					// 13:支払条件
	}
	else
	{
		$aryQuery[] = "null, ";														// 13:支払条件
	}
	$aryQuery[] = "'".$aryNewData["curAllTotalPrice"]."', ";						// 14:合計金額
	if ( $aryNewData["lngLocationCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngLocationCode"] . ", ";						// 15:納品場所コード
	}
	else
	{
		$aryQuery[] = "null, ";														// 15:納品場所コード
	}
	$aryQuery[] = "'".$aryNewData["dtmExpirationDate"]."', ";						// 16:発注有効期限日
	$aryQuery[] = "$strNote, ";														// 17:備考
	$aryQuery[] = "$lngInputUserCode, ";											// 18:入力者コード
	$aryQuery[] = "false, ";														// 19:無効フラグ
	$aryQuery[] = "now()";															// 20:登録日
	$aryQuery[] = ")";
	
	
	$strQuery = "";
	$strQuery = implode("\n", $aryQuery );
	// echo "$strQuery<br>";
	
	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
	}
	$objDB->freeResult( $lngResultID );
	
	// 2004.03.29 suzukaze update start
	////////////////////////////////////
	//// 明細行番号が不明な行の対処 ////
	////////////////////////////////////
	$lngMaxDetailNo = 0;
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
	// 仕入での明細行追加に対応するために重なりのない番号を使用する
	if ( $aryNewData["strProcMode"] == "renew" )
	{
		$lngMaxDetailNo = $lngMaxDetailNo + 100;
	}
	// 2004.03.29 suzukaze update end

	// t_orderDetail HEADER情報のインサート
	for( $i = 0 ; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
		// 9:概算区分コード
		$lngConversionClassCode = ( $aryNewData["aryPoDitail"][$i]["lngConversionClassCode"] == "gs" ) ? 1 : 2;
		$strDetailNote = ( $aryNewData["aryPoDitail"][$i]["strDetailNote"] == "null" ) ? "null" : "'".$aryNewData["aryPoDitail"][$i]["strDetailNote"]."'";

	// 2004.03.25 suzukaze update start
	// 2004.05.31 suzukaze update start
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
				// 修正で明細行番号が存在する場合に、修正元の同じ明細行番号の仕入科目、仕入部品、金型番号を取得
				if ( $aryNewData["strProcMode"] == "renew" 
					and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "" 
					and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "undefined" 
					and is_int($aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"]) )
				{
					$strMoldQuery = "SELECT lngStockSubjectCode, lngStockItemCode, strMoldNo FROM t_OrderDetail "
						. "WHERE lngOrderNo = " . $lngOrderNo 
						. " AND lngOrderDetailNo = " . $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"];
					// チェッククエリーの実行
					list ( $lngMoldResultID, $lngMoldResultNum ) = fncQuery( $strMoldQuery, $objDB );

					if ( $lngMoldResultNum == 1 )
					{
						$objResult = $objDB->fetchObject( $lngMoldResultID, 0 );
						$lngStockSubjectCode = $objResult->lngstocksubjectcode;
						$lngStockItemCode = $objResult->lngstockitemcode;
						$strMoldNo = $objResult->strmoldno;
					}
					$objDB->freeResult( $lngMoldResultID );
					// 同じ明細行番号のデータに金型番号がふられていない場合は新たに番号を取得する
					if ( $strMoldNo != "" )
					{
						$aryNewData["aryPoDitail"][$i]["strSerialNo"] = $strMoldNo;
					}
					else
					{
						$strSerialNo = fncGetMoldNo( $aryNewData["aryPoDitail"][$i]["strProductCode"], $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"], $aryNewData["aryPoDitail"][$i]["strStockItemCode"], $objDB );
					}
				}
				else
				// 明細行番号が不明==>修正により新たに明細に追加された場合は新たに金型番号を取得する
				{
					$strSerialNo = fncGetMoldNo( $aryNewData["aryPoDitail"][$i]["strProductCode"], $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"], $aryNewData["aryPoDitail"][$i]["strStockItemCode"], $objDB );
				}
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
			// 仕入科目が４３１（金型償却高）、　仕入部品が８（金型）の場合
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
		// 2004.03.25 suzukaze update end
		// 2004.05.31 suzukaze update end

		// 2004.03.29 suzukaze update start
		// 仕入明細番号
		// 明細行番号がない場合（仕入で追加された明細行の場合）
		if ( $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "null" 
			or $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "" 
			or $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "undefined" )
		{
			$lngMaxDetailNo++;
			$aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] = $lngMaxDetailNo;
		}

		// ソート番号
		$lngSortKey = $i + 1;
		// 2004.03.29 suzukaze update end

		$aryQuery	= array();
		$aryQuery[] = "INSERT INTO t_orderdetail (";
		$aryQuery[] = "lngorderno, ";												// 1:発注番号
		$aryQuery[] = "lngorderdetailno, ";											// 2:発注明細番号
		$aryQuery[] = "lngrevisionno, ";											// 3:リビジョン番号
		$aryQuery[] = "strproductcode, ";											// 4:製品コード
		$aryQuery[] = "lngstocksubjectcode, ";										// 5:仕入科目コード
		$aryQuery[] = "lngstockitemcode, ";											// 6:仕入部品コード
		$aryQuery[] = "dtmdeliverydate, ";											// 7:納品日
		$aryQuery[] = "lngdeliverymethodcode, ";									// 8:運搬方法コード
		$aryQuery[] = "lngconversionclasscode, ";									// 9:換算区分コード / 1：単位計上/ 2：荷姿単位計上
		$aryQuery[] = "curproductprice, ";											// 10:製品価格
		$aryQuery[] = "lngproductquantity, ";										// 11:製品数量
		$aryQuery[] = "lngproductunitcode, ";										// 12:製品単位コード
		$aryQuery[] = "lngtaxclasscode, ";											// 13:消費税区分コード
		$aryQuery[] = "lngtaxcode, ";												// 14:消費税コード
		$aryQuery[] = "curtaxprice, ";												// 15:消費税金額
		$aryQuery[] = "cursubtotalprice, ";											// 16:小計金額
		$aryQuery[] = "strnote, ";													// 17:備考
		$aryQuery[] = "strmoldno, ";												// 18:金型番号
		$aryQuery[] = "lngSortKey ";												// 19:表示用ソートキー
		$aryQuery[] = ") values (";
		$aryQuery[] = "$sequence_m_order, ";										// 1:発注番号
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] . ",";		// 2:発注明細番号
		$aryQuery[] = "$lngRevisionNo,";											// 3:リビジョン番号
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["strProductCode"]."', ";	// 4:製品コード
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"].", ";	// 5:仕入科目コード
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["strStockItemCode"].", ";		// 6:仕入部品コード
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"]."', ";	// 7:納品日
		if ( $aryNewData["aryPoDitail"][$i]["lngCarrierCode"] != "" )
		{
			$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngCarrierCode"].", ";	// 8:運搬方法コード
		}
		else
		{
			$aryQuery[] = "null, ";													// 8:運搬方法コード
		}
		if ( $lngConversionClassCode != "" )
		{
			$aryQuery[] = "$lngConversionClassCode, ";								// 9:換算区分コード
		}
		else
		{
			$aryQuery[] = "null, ";													// 9:換算区分コード
		}
		if ( $aryNewData["aryPoDitail"][$i]["curProductPrice"] != "" )
		{
			$aryQuery[] = $aryNewData["aryPoDitail"][$i]["curProductPrice"].", ";	// 10:価格
		}
		else
		{
			$aryQuery[] = "null, ";
		}
		if ( $aryNewData["aryPoDitail"][$i]["lngGoodsQuantity"] != "" )
		{
			$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngGoodsQuantity"].", ";		// 11:数量
		}
		else
		{
			$aryQuery[] = "null, ";
		}
		if ( $aryNewData["aryPoDitail"][$i]["lngProductUnitCode"] != "" )
		{
			$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngProductUnitCode"].", ";	// 12:単位コード
		}
		else
		{
			$aryQuery[] = "null, ";
		}
		$aryQuery[] = "null,";															// 13:消費税区分コード
		$aryQuery[] = "null,";															// 14:消費税コード
		$aryQuery[] = "null,";															// 15:消費税金額
		if ( $aryNewData["aryPoDitail"][$i]["curTotalPrice"] != "" )
		{
			$aryQuery[] = $aryNewData["aryPoDitail"][$i]["curTotalPrice"].", ";			// 16:税抜き金額
		}
		else
		{
			$aryQuery[] = "null, ";
		}
		$aryQuery[] = "$strDetailNote, ";												// 17:備考
		$aryQuery[] = $strSerialNo . ", ";												// 18:金型番号
		$aryQuery[] = $lngSortKey . " ";												// 19:表示用ソートキー
		$aryQuery[] = ")";
		
		$strQuery = "";
		$strQuery = implode( $aryQuery );
		
		//echo "<br><br>$strQuery<br>";

		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
		}
		$objDB->freeResult( $lngResultID );
		
	}

	// 2004.04.01 suzukaze update start
	if ( !fncCheckSetProduct ( $aryNewData["aryPoDitail"], $lngmonetaryunitcode, $objDB ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}
	// 2004.04.01 suzukaze update end



	$strProductCode       = $aryNewData["aryPoDitail"][0]["strProductCode"];
	$lngApplicantUserCode = fncGetMasterValue( "m_product", "strproductcode", "lnginchargeusercode", $strProductCode . ":str", '', $objDB );


	// 承認ルートが選択された場合「承認ルートなし」→「0」
	if($aryNewData["lngWorkflowOrderCode"] != 0 )
	{

		// m_workflowのシーケンスを取得
		$lngworkflowcode = fncGetSequence( 'm_Workflow.lngworkflowcode', $objDB );
		$strworkflowname = "発注 [No:$strOrderCode-$strReviseCode"."]";

		$aryQuery = array();
		$aryQuery[] = "INSERT INTO m_workflow (";
		$aryQuery[] = "lngworkflowcode, ";							// 1:ワークフローコード
		$aryQuery[] = "lngworkflowordercode, ";						// 2:ワークフロー順序コード
		$aryQuery[] = "strworkflowname, ";							// 3:ワークフロー名称
		$aryQuery[] = "lngfunctioncode, ";							// 4:機能コード
		$aryQuery[] = "strworkflowkeycode, ";						// 5:ワークフローキーコード 
		$aryQuery[] = "dtmstartdate, ";								// 6:案件発生日
		$aryQuery[] = "dtmenddate, ";								// 7:案件終了日
		$aryQuery[] = "lngapplicantusercode, ";						// 8:案件申請者コード
		$aryQuery[] = "lnginputusercode, ";							// 9:案件入力者コード
		$aryQuery[] = "bytinvalidflag, ";							// 10:無効フラグ
		$aryQuery[] = "strnote";									// 11:備考
		$aryQuery[] = " ) values (";
		$aryQuery[] = "$lngworkflowcode, ";							// 1:ワークフローコード
		if ( $aryNewData["lngWorkflowOrderCode"] != "" )
		{
			$aryQuery[] = $aryNewData["lngWorkflowOrderCode"].", ";		// 2:ワークフロー順序コード
		}
		else
		{
			$aryQuery[] = "null, ";
		}
		$aryQuery[] = "'$strworkflowname', ";						// 3:ワークフロー名称
		$aryQuery[] = DEF_FUNCTION_PO1.", ";						// 4:機能コード
		$aryQuery[] = "$sequence_m_order, ";						// 5:ワークフローキーコード 
		$aryQuery[] = "now(), ";									// 6:案件発生日
		$aryQuery[] = "null, ";										// 7:案件終了日
		$aryQuery[] = $lngApplicantUserCode . ", ";					// 8:案件申請者コード
		$aryQuery[] = "$lngInputUserCode, ";						// 9:案件入力者コード
		$aryQuery[] = "false, ";									// 10:無効フラグ
		$aryQuery[] = "null";										// 11:備考
		$aryQuery[] = " )";
		
		$strQuery = "";
		$strQuery = implode("\n", $aryQuery );
		
		// echo "$strQuery<br>";
		
		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
		}
		$objDB->freeResult( $lngResultID );
		
		
		$lngLimitDate = fncGetMasterValue( "m_workfloworder" ,"lngworkflowordercode", "lnglimitdays", $aryNewData["lngWorkflowOrderCode"] ,"lngworkfloworderno = 1", $objDB);
		
		
		//$lngLimitDate  = mktime ( date("H"), date("i"), date("s"), date("m"),  date("d")+$lngLimitDate,  date("Y"));
		//$lngLimitDate = date( 'Y-m-d H:i:s', $lngLimitDate );
		
		// echo "期限日：$lngLimitDate<br>";
		
		$aryQuery = array();
		$aryQuery[] = "INSERT INTO t_workflow (";
		$aryQuery[] = "lngworkflowcode, ";							// ワークフローコード
		$aryQuery[] = "lngworkflowsubcode, ";						// ワークフローサブコード
		$aryQuery[] = "lngworkfloworderno, ";						// ワークフロー順序番号
		$aryQuery[] = "lngworkflowstatuscode, ";					// ワークフロー状態コード
		$aryQuery[] = "strnote, ";									// 備考
		$aryQuery[] = "dtminsertdate, ";							// 登録日
		$aryQuery[] = "dtmlimitdate ";								// 期限日
		$aryQuery[] = ") values (";
		$aryQuery[] = "$lngworkflowcode, ";							// ワークフローコード
		$aryQuery[] = DEF_T_WORKFLOW_SUBCODE.", ";					// ワークフローサブコード
		$aryQuery[] = DEF_T_WORKFLOW_ORDERNO.", ";					// ワークフロー順序番号
		$aryQuery[] = DEF_T_WORKFLOW_STATUS.", ";					// ワークフロー状態コード
		// 2004.03.24 suzukaze update start
		$aryQuery[] = "'" . $aryNewData["strWorkflowMessage"] . "',";	// 11:備考
		// 2004.03.24 suzukaze update end
		$aryQuery[] = "now(), ";									// 登録日
		$aryQuery[] = "now() + (interval '$lngLimitDate day' )";	// 期限日
		$aryQuery[] = ")";
		
		$strQuery = "";
		$strQuery = implode("\n", $aryQuery );
		
		// echo "$strQuery<br>";

		
		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
		}
		$objDB->freeResult( $lngResultID );
		
		
		// 承認者にメールを送る
		$arySelect = array();
		$arySelect[] = "SELECT u.strmailaddress, ";									// メールアドレス
		$arySelect[] = "u.bytMailTransmitFlag, ";									// メール配信許可フラグ
		$arySelect[] = "w.strworkflowordername, ";									// ワークフロー名
		$arySelect[] = "u.struserdisplayname ";										// 承認者
		$arySelect[] = "FROM m_workfloworder w, m_user u, m_authoritygroup a ";
		$arySelect[]= "WHERE w.lngworkflowordercode = ";
		$arySelect[] = $aryNewData["lngWorkflowOrderCode"]." AND ";
		$arySelect[] = "u.lngusercode = w.lnginchargecode AND ";
		$arySelect[] = "u.lngauthoritygroupcode = a.lngauthoritygroupcode ";
		$arySelect[] = "ORDER BY a.lngauthoritylevel DESC";

		$strSelect = "";
		$strSelect = implode("\n", $arySelect );
		
		// echo "$strSelect";
		
		if ( $lngResultID = $objDB->execute( $strSelect ) )
		{
			$aryResult[] = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );
		}
		$objDB->freeResult( $lngResultID );
		
		// メール文面に必要なデータを配列$aryMailDataに格納
		$aryMailData["strmailaddress"] = $aryResult[0]["strmailaddress"];				// 承認者メールアドレス

		// 2004.03.23 suzukaze update start
		// 入力者メールアドレスの取得
		$strUserMailQuery = "SELECT bytMailTransmitFlag, strMailAddress FROM m_User WHERE lngUserCode = " . $objAuth->UserCode;

		list ( $lngUserMailResultID, $lngUserMailResultNum ) = fncQuery( $strUserMailQuery, $objDB );
		if ( $lngUserMailResultNum == 1 )
		{
			$objResult = $objDB->fetchObject( $lngUserMailResultID, 0 );
			$bytInputUserMailTransmitFlag 	= $objResult->bytmailtransmitflag;
			$strInputUserMailAddress 		= $objResult->strmailaddress;
		}
		else
		{
			fncOutputError( 9051, DEF_ERROR, "データが異常です", TRUE, "po/regist/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngUserMailResultID );

		// メール配信許可フラグが　TRUE　に設定されていない場合、入力者（申請者）のメールアドレスが設定されていない場合は、メール送信しない
		if ( $aryResult[0]["bytmailtransmitflag"] == "t" and $aryMailData["strmailaddress"] != "" 
			and $strInputUserMailAddress != "" )
		{
			$strMailAddress = $aryResult[0]["strmailaddress"];								// 承認者メールアドレス
			$aryMailData["strWorkflowName"] = $strworkflowname;								// 案件名
			//			$aryMailData["strUserDisplayName"] = $aryResult[0]["struserdisplayname"];		// 承認依頼者
			$aryMailData["strUserDisplayName"] = $objAuth->UserDisplayName;					// 入力者（申請者）表示名
			$aryMailData["strURL"] = LOGIN_URL;												// URL
			// 2004.03.24 suzukaze update start
			// 確認画面上のメッセージをメール内の備考欄として送信
			$aryMailData["strNote"] = $aryNewData["strWorkflowMessage"];
			// 2004.03.24 suzukaze update end

			// メールメッセージ取得関数
			list ( $strSubject, $strTemplate ) = fncGetMailMessage( 807, $aryMailData, $objDB );

			// 管理者メールアドレス取得関数
			$strAdminMailAddress = fncGetCommonFunction( "adminmailaddress", "m_adminfunction", $objDB );

			// メール送信
			fncSendMail( $strMailAddress, $strSubject, $strTemplate, "From: $strInputUserMailAddress\nReturn-Path: " . $strAdminMailAddress . "\n" );
		}
		// 2004.03.23 suzukaze update end

		// 帳票出力表示切替
		$aryData["PreviewVisible"] = "hidden";

	}
	// 即承認の場合プレビューボタンを表示する
	else
	{
		// 帳票出力対応
		// 権限を持ってない場合はプレビューボタンを表示しない
		if ( fncCheckAuthority( DEF_FUNCTION_LO2, $objAuth ) && $lngorderstatuscode != DEF_ORDER_APPLICATE )
		{
			$aryData["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ORDER . "&strReportKeyCode=" . $sequence_m_order . "&bytCopyFlag=TRUE";
			// 帳票出力表示切替
			$aryData["PreviewVisible"] = "visible";
		}
		else
		{
			// 帳票出力表示切替
			$aryData["PreviewVisible"] = "hidden";
		}
	}

	// トランザクション完了
	$objDB->transactionCommit();


	
	$aryData["strBodyOnload"] = "";
	
	$objDB->close();


	// 成功時戻り先のアドレス指定
	$aryData["strAction"] = "/po/regist/index.php?strSessionID=";

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	
	// テンプレートに反映する文字列
	$aryData["lngPONo"] = "$strOrderCode - $strReviseCode";

	header("Content-type: text/plain; charset=EUC-JP");
	$objTemplate->getTemplate( "po/finish/parts.tmpl" );
	
	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;
			
	return true;
?>