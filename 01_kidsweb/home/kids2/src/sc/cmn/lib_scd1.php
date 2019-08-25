<?
/** 
*	納品書　詳細、削除、無効化関数群
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.01
*
*	処理概要
*	検索結果関連の関数
*
*	修正履歴
*
*	2004.03.17	詳細表示時の単価部分の表示方式を小数点以下４桁に変更
*	2004.03.30	詳細表示時の表示順を明細行番号順から表示用ソートキー順に変更
*
*/

/**
* 指定された納品伝票番号から納品書ヘッダ情報を取得するＳＱＬ文を作成
*
*	指定納品伝票番号のヘッダ情報の取得用ＳＱＬ文作成関数
*
*	@param  Integer 	$lngSlipNo 			取得する納品伝票番号
*	@return strQuery 	$strQuery 検索用SQL文
*	@access public
*/
function fncGetSlipHeadNoToInfoSQL ( $lngSlipNo )
{
	// 納品伝票番号、リビジョン番号
	$aryQuery[] = "SELECT distinct on (s.lngSlipNo) s.lngSlipNo as lngSlipNo, s.lngRevisionNo as lngRevisionNo";
	// 納品書No
	$aryQuery[] = ", s.strSlipCode as strSlipCode";
	// 顧客
	$aryQuery[] = ", s.strCustomerCode as strCustomerCode";	//顧客コード
	$aryQuery[] = ", s.strCustomerName as strCustomerName";	//顧客名
	// 納品日
	$aryQuery[] = ", to_char( s.dtmDeliveryDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmDeliveryDate";
	// 納品場所名
	$aryQuery[] = ", s.strDeliveryPlaceName as strDeliveryPlaceName";
	// 納品場所担当者名
	$aryQuery[] = ", s.strDeliveryPlaceUserName as strDeliveryPlaceUserName";
	// 課税区分
	$aryQuery[] = ", s.strTaxClassName as strTaxClassName";
	// 通貨記号。ヘッダ部の合計金額、明細部の単価と税抜価格に付与される
	$aryQuery[] = ", s.strMonetaryUnitSign as strMonetaryUnitSign";
	// 合計金額
	$aryQuery[] = ", To_char( s.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
	// 通貨（この項目だけマスタを紐づけて取得）
	$aryQuery[] = ", mu.strMonetaryUnitName as strMonetaryUnitName";
	// 備考
	$aryQuery[] = ", s.strNote as strNote";
	// 入力日
	$aryQuery[] = ", to_char( s.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmInsertDate";
	// 入力者＝起票者
	$aryQuery[] = ", s.strInsertUserCode as strInsertUserCode";	//入力者コード
	$aryQuery[] = ", s.strInsertUserName as strInsertUserName";	//入力者名
	// 印刷回数
	$aryQuery[] = ", s.lngPrintCount as lngPrintCount";

	// FROM句
	$aryQuery[] = " FROM m_Slip s ";
	$aryQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";

	// WHERE句
	$aryQuery[] = " WHERE s.lngSlipNo = " . $lngSlipNo . "";

	$strQuery = implode( "\n", $aryQuery );

//fncDebug("lib_scs1.txt", $strQuery, __FILE__, __LINE__);
//fncDebug("lib_scs1-1.txt", $arySalesResult, __FILE__, __LINE__);

	return $strQuery;
}



/**
* 指定された売上番号から売上明細情報を取得するＳＱＬ文を作成
*
*	指定売上番号の明細情報の取得用ＳＱＬ文作成関数
*
*	@param  Integer 	$lngSalesNo 			取得する売上番号
*	@return strQuery 	$strQuery 検索用SQL文
*	@access public
*/
function fncGetSlipDetailNoToInfoSQL ( $lngSlipNo )
{
	// ソートキー
	$aryQuery[] = "SELECT distinct on (sd.lngSortKey) sd.lngSortKey as lngRecordNo, ";
	// 納品伝票番号、リビジョン番号
	$aryQuery[] = "sd.lngSlipNo as lngSlipNo, sd.lngRevisionNo as lngRevisionNo";
	// 顧客受注番号
	$aryQuery[] = ", sd.strCustomerSalesCode as strCustomerSalesCode";
	// 売上区分
	$aryQuery[] = ", sd.lngSalesClassCode as lngSalesClassCode";	//売上区分コード
	$aryQuery[] = ", sd.strSalesClassName as strSalesClassName";	//売上区分名
	// 顧客品番
	$aryQuery[] = ", sd.strGoodsCode as strGoodsCode";
	// 製品コード・名称
	$aryQuery[] = ", sd.strProductCode as strProductCode";	//製品コード
	$aryQuery[] = ", sd.strProductName as strProductName";	//製品名
	// 名称（英語）
	$aryQuery[] = ", sd.strProductEnglishName as strProductEnglishName";	//製品名（英語）
	// 単価
	$aryQuery[] = ", To_char( sd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
	// 数量
	$aryQuery[] = ", To_char( sd.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
	// 単位
	$aryQuery[] = ", sd.strProductUnitName as strProductUnitName";
	// 税抜金額
	$aryQuery[] = ", To_char( sd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
	// 明細備考
	$aryQuery[] = ", sd.strNote as strDetailNote";

	// FROM句
	$aryQuery[] = " FROM t_SlipDetail sd";

	$aryQuery[] = " WHERE sd.lngSlipNo = " . $lngSlipNo . "";

	$aryQuery[] = " ORDER BY sd.lngSortKey ASC ";

	$strQuery = implode( "\n", $aryQuery );
//fncDebug("lib_scs1-1.txt", $strQuery, __FILE__, __LINE__);

	return $strQuery;
}


/**
* ヘッダ部データ加工
*
*	SQLで取得したヘッダ部の値を表示用に加工する
*	※SQL取得結果のキー名はすべて小文字になることに注意
*
*	@param  Array 	$aryResult 				ヘッダ行の検索結果が格納された配列
*	@access public
*/
function fncSetSlipHeadTableData ( $aryResult )
{
	// 納品伝票番号
	$aryNewResult["lngSlipNo"] = $aryResult["lngslipno"];
	// リビジョン番号
	$aryNewResult["lngRevisionNo"] = $aryResult["lngrevisionno"];
	// 納品書No
	$aryNewResult["strSlipCode"] = $aryResult["strslipcode"];

	// 顧客
	if ( $aryResult["strcustomercode"] )
	{
		$aryNewResult["strCustomer"] = "[" . $aryResult["strcustomercode"] ."]";
	}
	else
	{
		$aryNewResult["strCustomer"] = "      ";
	}
	$aryNewResult["strCustomer"] .= " " . $aryResult["strcustomername"];

	// 納品日
	$aryNewResult["dtmDeliveryDate"] = $aryResult["dtmdeliverydate"];
	// 納品場所名
	$aryNewResult["strDeliveryPlaceName"] = $aryResult["strdeliveryplacename"];
	// 納品場所担当者名
	$aryNewResult["strDeliveryPlaceUserName"] = $aryResult["strdeliveryplaceusername"];
	// 課税区分
	$aryNewResult["strTaxClassName"] = $aryResult["strtaxclassname"];

	// 通貨記号。ヘッダ部の合計金額、明細部の単価と税抜価格に付与される
	$aryNewResult["strMonetaryUnitSign"] = $aryResult["strmonetaryunitsign"];
	// 合計金額
	$aryNewResult["curTotalPrice"] = $aryNewResult["strMonetaryUnitSign"] . " ";
	if ( !$aryResult["curtotalprice"] )
	{
		$aryNewResult["curTotalPrice"] .= "0.00";
	}
	else
	{
		$aryNewResult["curTotalPrice"] .= $aryResult["curtotalprice"];
	}

	// 通貨
	$aryNewResult["strMonetaryUnitName"] = $aryResult["strmonetaryunitname"];

	// 備考
	$aryNewResult["strNote"] = nl2br($aryResult["strnote"]);

	// 入力日
	$aryNewResult["dtmInsertDate"] = $aryResult["dtminsertdate"];

	// 入力者
	if ( $aryResult["strinsertusercode"] )
	{
		$aryNewResult["strInsertUser"] = "[" . $aryResult["strinsertusercode"] ."]";
	}
	else
	{
		$aryNewResult["strInsertUser"] = "      ";
	}
	$aryNewResult["strInsertUser"] .= " " . $aryResult["strinsertusername"];

	// 起票者＝入力者
	$aryNewResult["strDrafter"] = $aryNewResult["strInsertUser"];

	// 印刷回数
	$aryNewResult["lngPrintCount"] = $aryResult["lngprintcount"];

	return $aryNewResult;
}



/**
* 詳細部データ加工
*
*	SQLで取得した詳細部の値を表示用に加工する
*	※SQL取得結果のキー名はすべて小文字になることに注意
*
*	@param  Array 	$aryDetailResult 	明細行の検索結果が格納された配列（１データ分）
*	@param  Array 	$aryHeadResult 		ヘッダ行の検索結果が格納された配列（参照用）
*	@access public
*/
function fncSetSlipDetailTableData ( $aryDetailResult, $aryHeadResult )
{

	// ソートキー
	$aryNewDetailResult["lngRecordNo"] = $aryDetailResult["lngrecordno"];
	// 納品伝票番号
	$aryNewDetailResult["lngSlipNo"] = $aryDetailResult["lngslipno"];
	// リビジョン番号
	$aryNewDetailResult["lngRevisionNo"] = $aryDetailResult["lngrevisionno"];
	// 顧客受注番号
	$aryNewDetailResult["strCustomerSalesCode"] = $aryDetailResult["strcustomersalescode"];
	// 売上区分
	if ( $aryDetailResult["lngsalesclasscode"] )
	{
		$aryNewDetailResult["lngSalesClassCode"] = "[" . $aryDetailResult["lngsalesclasscode"] ."]";
	}
	else
	{
		$aryNewDetailResult["lngSalesClassCode"] = "      ";
	}
	$aryNewDetailResult["lngSalesClassCode"] .= " " . $aryDetailResult["strsalesclassname"];

	// 顧客品番
	$aryNewDetailResult["strGoodsCode"] = $aryDetailResult["strgoodscode"];
	
	// 製品コード・名称
	if ( $aryDetailResult["strproductcode"] )
	{
		$aryNewDetailResult["strProductCode"] = "[" . $aryDetailResult["strproductcode"] ."]";
	}
	else
	{
		$aryNewDetailResult["strProductCode"] = "      ";
	}
	$aryNewDetailResult["strProductCode"] .= " " . $aryDetailResult["strproductname"];
	
	// 名称（英語）
	$aryNewDetailResult["strProductEnglishName"] = $aryDetailResult["strproductenglishname"];

	// 単価
	$aryNewDetailResult["curProductPrice"] = $aryHeadResult["strMonetaryUnitSign"] . " ";
	if ( !$aryDetailResult["curproductprice"] )
	{
		$aryNewDetailResult["curProductPrice"] .= "0.00";
	}
	else
	{
		$aryNewDetailResult["curProductPrice"] .= $aryDetailResult["curproductprice"];
	}

	// 数量
	$aryNewDetailResult["lngProductQuantity"] = $aryDetailResult["lngproductquantity"];
	// 単位
	$aryNewDetailResult["strProductUnitName"] = $aryDetailResult["strproductunitname"];

	// 税抜金額
	$aryNewDetailResult["curSubTotalPrice"] = $aryHeadResult["strMonetaryUnitSign"] . " ";
	if ( !$aryDetailResult["cursubtotalprice"] )
	{
		$aryNewDetailResult["curSubTotalPrice"] .= "0.00";
	}
	else
	{
		$aryNewDetailResult["curSubTotalPrice"] .= $aryDetailResult["cursubtotalprice"];
	}

	// 明細備考
	$aryNewDetailResult["strDetailNote"] = nl2br($aryDetailResult["strdetailnote"]);

	return $aryNewDetailResult;
}


/**
* カラム名を格納する配列のキーに"CN"を付与する
*
*	@param  Array 	$aryColumnNames 		カラム名が格納された配列
*	@access public
*/
function fncAddColumnNameArrayKeyToCN ($aryColumnNames)
{
	$arrayKeys = array_keys($aryColumnNames);

	// 表示対象カラムの配列より結果の出力
	for ( $i = 0; $i < count($arrayKeys); $i++ )
	{
		$key = $arrayKeys[$i];
		$strNewColumnName = "CN" . $key;
		$aryNames[$strNewColumnName] = $aryColumnNames[$key];
	}

	return $aryNames;
}




/**
* 指定の売上データについて無効化することでどうなるかケースわけする
*
*	指定の売上データの状態を調査し、ケースわけする関数
*
*	@param  Array 		$arySalesData 	売上データ
*	@param  Object		$objDB			DBオブジェクト
*	@return Integer 	$lngCase		状態のケース
*										1: 対象売上データを無効化しても、最新の売上データが影響受けない
*										2: 対象売上データを無効化することで、最新の売上データが入れ替わる
*										3: 対象売上データが削除データで、売上が復活する
*										4: 対象売上データを無効化することで、最新の売上データになりうる売上データがない
*	@access public
*/
function fncGetInvalidCodeToMaster ( $arySalesData, $objDB )
{
	// 削除対象売上と同じ売上コードの最新の売上Noを調べる
	$strQuery = "SELECT s.lngSalesNo FROM m_Sales s WHERE s.strSalesCode = '" . $arySalesData["strsalescode"] . "' AND s.bytInvalidFlag = FALSE ";
	$strQuery .= " AND s.lngRevisionNo >= 0";
	$strQuery .= " AND s.lngRevisionNo = ( "
		. "SELECT MAX( s1.lngRevisionNo ) FROM m_Sales s1 WHERE s1.strSalesCode = s.strSalesCode )";

	// 検索クエリーの実行
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum == 1 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngNewSalesNo = $objResult->lngsalesno;
//fncDebug("lib_scs1.txt",$objResult, __FILE__, __LINE__);

	}
	else
	{
		$lngCase = 4;
	}
	$objDB->freeResult( $lngResultID );

	// 削除対象が最新かどうかのチェック
	if ( $lngCase != 4 )
	{
		if ( $lngNewSalesNo == $arySalesData["lngsalesno"] )
		{
			// 最新の場合
			// 削除対象売上以外でと同じ売上コードの最新の売上Noを調べる
			$strQuery = "SELECT s.lngSalesNo FROM m_Sales s WHERE s.strSalesCode = '" . $strSalesCode . "' AND s.bytInvalidFlag = FALSE ";
			$strQuery .= " AND s.lngSalesNo <> " . $arySalesData["lngsalesno"] . " AND s.lngRevisionNo >= 0";

			// 検索クエリーの実行
			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
			if ( $lngResultNum >= 1 )
			{
				$lngCase = 2;
			}
			else
			{
				$lngCase = 4;
			}
			$objDB->freeResult( $lngResultID );
		}
		// 対象売上が削除データかどうかの確認
		else if ( $arySalesData["lngrevisionno"] < 0 )
		{
			$lngCase = 3;
		}
		else
		{
			$lngCase = 1;
		}
	}

	return $lngCase;
}

// 2004.03.09 suzukaze update start
/**
* 指定の売上データの削除に関して、その売上データを削除することでの状態変更関数
*
*	売上の状態が「納品済」の場合、受注Noを指定していた場合、分納であった場合など
*	各状態ごとにその売上に関するデータの状態を変更する
*
*	@param  Array 		$arySalesData 	売上データ
*	@param  Object		$objDB			DBオブジェクト
*	@return Boolean 	0				実行成功
*						1				実行失敗 情報取得失敗
*	@access public
*/
function fncSalesDeleteSetStatus ( $arySalesData, $objDB )
{
/*	// 削除対象売上は、受注Noを指定しない売上である
	if ( $arySalesData["lngreceiveno"] == "" or $arySalesData["lngreceiveno"] == 0 )
	{
		return 0;
	}
*/
	// 受注Noを指定している売上の場合は、指定している最新の受注のデータを取得する
	$arySql = array();
	$arySql[] = "SELECT";
	$arySql[] = "	r.lngReceiveNo";	//	as lngReceiveNo";
	$arySql[] = "	,r.strReceiveCode";	//	as strReceiveCode";
	$arySql[] = "	,r.lngReceiveStatusCode";	//	as lngReceiveStatusCode";
	$arySql[] = "	,r.lngMonetaryUnitCode";	//	as lngMonetaryUnitCode";
	$arySql[] = "	,r.strcustomerreceivecode";
	$arySql[] = "FROM";
	$arySql[] = "	m_Receive r";
	$arySql[] = "WHERE";
//	$arySql[] = "	r.strReceiveCode = (";
//	$arySql[] = "	SELECT r1.strReceiveCode FROM m_Receive r1 WHERE r1.lngReceiveNo = " . $arySalesData["lngreceiveno"];
	$arySql[] = "	r.strReceiveCode in (";
	$arySql[] = "	SELECT r1.strReceiveCode FROM m_Receive r1 WHERE r1.lngReceiveNo IN (SELECT ts.lngreceiveno FROM t_Salesdetail ts WHERE ts.lngsalesno = " . $arySalesData["lngsalesno"];
	$arySql[] = "	)";
//	$arySql[] = "SELECT r1.strReceiveCode FROM m_Receive r1 WHERE r1.lngReceiveNo in (select ts.lngreceiveno from t_salesdetail ts where ts.lngsalesno = "  . $lngSalesNo .")";
	$arySql[] = "	)";
	$arySql[] = "	AND r.bytInvalidFlag = FALSE";
	$arySql[] = "	AND r.lngRevisionNo >= 0";
	$arySql[] = "	AND r.lngRevisionNo = (";
	$arySql[] = "		SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode";
	$arySql[] = "		AND r2.strReviseCode = (";
	$arySql[] = "		SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode )";
	$arySql[] = "	)";
	$arySql[] = "	AND 0 <= (";
	$arySql[] = "		SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode";
	$arySql[] = "	)";
	$strQuery = implode("\n", $arySql);

	// 検索クエリーの実行
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	if ( $lngResultNum )
	{
		for ( $a = 0; $a < $lngResultNum; $a++ )
		{
			$objResult1[$a]= $objDB->fetchArray( $lngResultID, $a );
//fncDebug("lib_scs1.txt", $objResult1, __FILE__, __LINE__);
		}
	$objDB->freeResult( $lngResultID );
	}else{
		// 受注Noは指定しているが現在有効な最新受注が存在しない場合はそのまま削除可能とする
			return 0;
		}
	for($k=0;$k<count($objResult1);$k++)
		{
//               削除対象売上と同じ受注Noを指定している最新売上を検索
			$arySql = array();
			$arySql[] = "SELECT distinct";
			$arySql[] = "	s.lngSalesNo as lngSalesNo";
			$arySql[] = "	,s.lngSalesStatusCode as lngSalesStatusCode";
			$arySql[] = "	,s.lngMonetaryUnitCode as lngMonetaryUnitCode";
//			$arySql[] = "	,r.lngreceiveno as lngreceiveno";
			$arySql[] = "FROM";
			$arySql[] = "	m_Sales s";
			$arySql[] = "	left join t_salesdetail tsd";
			$arySql[] = "		on s.lngsalesno = tsd.lngsalesno";
			$arySql[] = "	,m_Receive r";
			$arySql[] = "WHERE";
			$arySql[] = "	r.lngreceiveno = " . $objResult1[$k]["lngreceiveno"];
//			$arySql[] = "	AND r.lngReceiveNo = tsd.lngReceiveNo";
			$arySql[] = "	AND tsd.lngReceiveNo in (select re1.lngReceiveNo from m_Receive re1 where re1.strreceivecode = (";
			$arySql[] = "	select re2.strreceivecode from m_Receive re2 where re2.lngreceiveno = " . $objResult1[$k]["lngreceiveno"];
			$arySql[] = "	))";
			$arySql[] = "	AND s.bytInvalidFlag = FALSE";
			$arySql[] = "	AND s.lngRevisionNo >= 0";
			$arySql[] = "	AND s.lngRevisionNo = (";
			$arySql[] = "		SELECT MAX( s2.lngRevisionNo ) FROM m_Sales s2 WHERE s2.strSalesCode = s.strSalesCode )";
			$arySql[] = "		AND 0 <= (";
			$arySql[] = "		SELECT MIN( s3.lngRevisionNo ) FROM m_Sales s3 WHERE s3.bytInvalidFlag = false AND s3.strSalesCode = s.strSalesCode";
			$arySql[] = "		)";
			$arySql[] = "	AND s.lngsalesno <> '"  . $arySalesData["lngsalesno"] . "'";
			$strQuery = implode("\n", $arySql);			
			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
			if ( $lngResultNum )
			{
				// 削除対象以外の売上データが存在する場合
				for ( $i = 0; $i < $lngResultNum; $i++ )
				{
					$arySalesResult1[$i] = $objDB->fetchArray( $lngResultID, $i );
//fncDebug("lib_scs1-1.txt", $arySalesResult1, __FILE__, __LINE__);
			// 売上参照受注の状態の状態を「納品中」とする
					// 同じ受注NOを指定している売上の状態に対しても「納品中」とする
						// 更新対象売上データをロックする
					if($arySalesResult1[$i]["lngsalesstatuscode"] != 99){
							$strLockQuery = "SELECT lngSalesNo FROM m_Sales " 
									. "WHERE lngSalesNo = " . $arySalesResult1[$i]["lngsalesno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
							list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
							$objDB->freeResult( $lngLockResultID );
							// 「納品中」状態への更新処理
							$strUpdateQuery = "UPDATE m_Sales set lngSalesStatusCode = " . DEF_SALES_DELIVER 
									. " WHERE lngSalesNo = " . $arySalesResult1[$i]["lngsalesno"];
							list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
							$objDB->freeResult( $lngUpdateResultID );
					}	
				}
				// 更新対象受注データをロックする
				$strLockQuery = "SELECT lngReceiveNo FROM m_Receive WHERE lngReceiveNo = " . $objResult1[$k]["lngreceiveno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
				$objDB->freeResult( $lngLockResultID );
				// 「納品中」状態への更新処理
				$strUpdateQuery = "UPDATE m_Receive set lngReceiveStatusCode = " . DEF_RECEIVE_DELIVER . " WHERE lngReceiveNo = " . $objResult1[$k]["lngreceiveno"];
				list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
				$objDB->freeResult( $lngUpdateResultID );
				}
				else
				{
				// 削除対象以外の売上データが存在しない場合
				// 売上の参照元最新受注の状態を「受注」に戻す
				// 更新対象受注データをロックする
				$strLockQuery = "SELECT lngReceiveNo FROM m_Receive WHERE lngReceiveNo = "  . $objResult1[$k]["lngreceiveno"] .  " AND bytInvalidFlag = FALSE FOR UPDATE";
				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
				if ( !$lngLockResultNum )
				{
					fncOutputError ( 9051, DEF_ERROR, "DB処理エラー", TRUE, "", $objDB );
				}
				$objDB->freeResult( $lngLockResultID );
				// 「受注」状態への更新処理
				$strUpdateQuery = "UPDATE m_Receive set lngReceiveStatusCode = " . DEF_RECEIVE_ORDER . " WHERE lngReceiveNo = "  . $objResult1[$k]["lngreceiveno"];
				list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
				$objDB->freeResult( $lngUpdateResultID );
			}
		}
	return 0;
}
?>