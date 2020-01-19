<?
/** 
*	納品書　詳細、削除関数群
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.01
*
*	処理概要
*	　納品書検索結果からの詳細表示と削除に関する処理
*
*	修正履歴
*
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
function fncGetSlipHeadNoToInfoSQL ( $lngSlipNo, $lngRevisionNo )
{
	// 納品伝票番号、リビジョン番号
	$aryQuery[] = "SELECT distinct on (s.lngSlipNo) s.lngSlipNo as lngslipno, s.lngRevisionNo as lngrevisionno";
	// 納品書No
	$aryQuery[] = ", s.strSlipCode as strslipcode";
	// 顧客
	$aryQuery[] = ", c.strcompanydisplaycode as strcustomercode";	//顧客コード
	$aryQuery[] = ", s.strCustomerName as strcustomername";	//顧客名
	// 納品日
	$aryQuery[] = ", to_char( s.dtmDeliveryDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmdeliverydate";
	// 納品場所名
	$aryQuery[] = ", s.strDeliveryPlaceName as strdeliveryplacename";
	// 納品場所担当者名
	$aryQuery[] = ", s.strDeliveryPlaceUserName as strdeliveryplaceusername";
	// 課税区分
	$aryQuery[] = ", s.strTaxClassName as strtaxclassname";
	// 通貨記号。ヘッダ部の合計金額、明細部の単価と税抜価格に付与される
	$aryQuery[] = ", s.strMonetaryUnitSign as strmonetaryunitsign";
	// 合計金額
	$aryQuery[] = ", To_char( s.curTotalPrice, '9,999,999,990.99' ) as curtotalprice";
	// 通貨（この項目だけマスタを紐づけて取得）
	$aryQuery[] = ", mu.strMonetaryUnitName as strmonetaryunitname";
	// 備考
	$aryQuery[] = ", s.strNote as strnote";
	// 入力日
	$aryQuery[] = ", to_char( s.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS' ) as dtminsertdate";
	// 入力者＝起票者
	$aryQuery[] = ", u.struserdisplaycode as strinsertusercode";	//入力者コード
	$aryQuery[] = ", s.strInsertUserName as strinsertusername";	//入力者名
	// 起票者
	$aryQuery[] = ", u2.struserdisplaycode as strusercode";	//入力者コード
	$aryQuery[] = ", s.strusername as strusername";	//入力者名
	// 印刷回数
	$aryQuery[] = ", s.lngPrintCount as lngprintcount";
	// 売上番号
	$aryQuery[] = ", s.lngSalesNo as lngsalesno";

	// FROM句
	$aryQuery[] = " FROM m_Slip s ";
	$aryQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	$aryQuery[] = " LEFT JOIN m_company c ON s.lngCustomerCode = c.lngcompanycode";
	$aryQuery[] = " LEFT JOIN m_user u ON s.lngInsertUserCode = u.lngusercode";
	$aryQuery[] = " LEFT JOIN m_user u2 ON s.lngUserCode = u2.lngusercode";

	// WHERE句
	$aryQuery[] = " WHERE s.lngSlipNo = " . $lngSlipNo . "";
	$aryQuery[] = " AND s.lngRevisionNo = " . $lngRevisionNo . "";

	$strQuery = implode( "\n", $aryQuery );

	return $strQuery;
}

/**
* 指定された納品伝票番号から納品伝票明細情報を取得するＳＱＬ文を作成
*
*	指定納品伝票番号の明細情報の取得用ＳＱＬ文作成関数
*
*	@param  Integer 	$lngSalesNo キーとなる納品伝票番号
*	@return strQuery 	$strQuery 	検索用SQL文
*	@access public
*/
function fncGetSlipDetailNoToInfoSQL ( $lngSlipNo)
{
	// ソートキー
	$aryQuery[] = "SELECT distinct on (sd.lngSortKey) sd.lngSortKey as lngrecordno, ";
	// 納品伝票番号、リビジョン番号
	$aryQuery[] = "sd.lngSlipNo as lngslipno, sd.lngRevisionNo as lngrevisionno";
	// 顧客受注番号
	$aryQuery[] = ", sd.strCustomerSalesCode as strcustomersalescode";
	// 売上区分
	$aryQuery[] = ", sd.lngSalesClassCode as lngsalesclasscode";	//売上区分コード
	$aryQuery[] = ", sd.strSalesClassName as strsalesclassname";	//売上区分名
	// 顧客品番
	$aryQuery[] = ", sd.strGoodsCode as strgoodscode";
	// 製品コード・名称
	$aryQuery[] = ", sd.strProductCode as strproductcode";	//製品コード
	$aryQuery[] = ", sd.strProductName as strproductname";	//製品名
	// 名称（英語）
	$aryQuery[] = ", sd.strProductEnglishName as strproductenglishname";	//製品名（英語）
	// 単価
	$aryQuery[] = ", To_char( sd.curProductPrice, '9,999,999,990.9999' )  as curproductprice";
	// 数量
	$aryQuery[] = ", To_char( sd.lngProductQuantity, '9,999,999,990' )  as lngproductquantity";
	// 単位
	$aryQuery[] = ", sd.strProductUnitName as strproductunitname";
	// 税抜金額
	$aryQuery[] = ", To_char( sd.curSubTotalPrice, '9,999,999,990.99' )  as cursubtotalprice";
	// 明細備考
	$aryQuery[] = ", sd.strNote as strDetailNote";
	// 受注番号
	$aryQuery[] = ", sd.lngReceiveNo as lngreceiveno";
	// 受注明細番号
	$aryQuery[] = ", sd.lngReceiveDetailNo as lngreceivedetailno";
	// 受注リビジョン番号
	$aryQuery[] = ", sd.lngReceiveRevisionNo as lngreceiverevisionno";

	// FROM句
	$aryQuery[] = " FROM t_SlipDetail sd";

	$aryQuery[] = " WHERE sd.lngSlipNo = " . $lngSlipNo . "";
	$aryQuery[] = " AND sd.lngRevisionNo = (SELECT MAX( s.lngRevisionNo ) FROM m_slip s WHERE s.lngSlipNo = sd.lngSlipNo)";
	
	$aryQuery[] = " ORDER BY sd.lngSortKey ASC ";

	$strQuery = implode( "\n", $aryQuery );
	
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


	// 起票者
	if ( $aryResult["strusercode"] )
	{
		$aryNewResult["strDrafter"] = "[" . $aryResult["strusercode"] ."]";
	}
	else
	{
		$aryNewResult["strDrafter"] = "      ";
	}
	$aryNewResult["strDrafter"] .= " " . $aryResult["strusername"];

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


function fncJapaneseInvoiceExists($lngCustomerCode, $lngSalesNo, $objDB)
{
	// 顧客の国コード取得
	$strCompanyQuery = "SELECT lngcountrycode FROM m_Company WHERE strcompanydisplaycode = '" . $lngCustomerCode . "'";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strCompanyQuery, $objDB );
	if ( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngCountryCode = $objResult->lngcountrycode;
	}
	else
	{
		// 国コード取得失敗⇒DBエラー
		fncOutputError ( 9501, DEF_FATAL, "削除前チェック処理に伴う国コード取得失敗", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// 請求書明細番号取得
	$strSalesQuery = "SELECT lnginvoiceno FROM m_Sales WHERE lngSalesNo = " . $lngSalesNo . " FOR UPDATE";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strSalesQuery, $objDB );
	if ( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngInvoiceNo = $objResult->lnginvoiceno;
	}
	else
	{
		// 請求書番号取得失敗→チェック失敗⇒DBエラー
		fncOutputError ( 9501, DEF_FATAL, "削除前チェック処理に伴う請求書番号取得失敗", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// 顧客の国が日本で、かつ納品書ヘッダに紐づく請求書明細が存在する
	return ($lngCountryCode == 81) && ($lngInvoiceNo != null);

}

function fncReceiveStatusIsClosed($lngSlipNo, $objDB)
{
	// 納品伝票明細データの取得
	$strQuery = fncGetSlipDetailNoToInfoSQL ( $lngSlipNo, $lngRevisionNo );
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		// 納品伝票番号に紐づく納品伝票明細が見つからない⇒DBエラー
		fncOutputError ( 9501, DEF_FATAL, "削除前チェック処理に伴う納品伝票番号取得失敗", TRUE, "", $objDB );
	}

	// 納品伝票明細に紐づく受注のステータスが「締め済」かどうか
	for ( $i = 0; $i < count($aryDetailResult); $i++)
	{
		// 受注番号
		$lngReceiveNo = $aryDetailResult[$i]["lngreceiveno"];

		// 受注マスタより受注状態コードを取得
		$strReceiveCodeQuery = "SELECT lngreceivestatuscode FROM m_Receive WHERE lngReceiveNo = " . $lngReceiveNo . " FOR UPDATE";
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strReceiveCodeQuery, $objDB );
		if ( $lngResultNum )
		{
			$objResult = $objDB->fetchObject( $lngResultID, 0 );
			$lngReceiveStatusCode = $objResult->lngreceivestatuscode;
		}
		else
		{
			// 受注状態コード取得失敗⇒DBエラー
			fncOutputError ( 9051, DEF_FATAL, "削除前チェック処理に伴う受注状態コード取得失敗", TRUE, "", $objDB );
		}
		$objDB->freeResult( $lngResultID );

		if ($lngReceiveStatusCode == DEF_RECEIVE_CLOSED){
			// 受注状態コードが「締め済」の明細が1件以上存在
			return true;
		}
	}

	// 受注状態コードが「締め済」の明細は1件も無い
	return false;
}

/**
 * 売上データの削除
 * 
 *	@param  Long 		$lngSalesNo 売上番号
 *	@param  Object		$objDB		DBオブジェクト
 *	@param  Object		$objAuth	権限オブジェクト
 *	@return Boolean 	true		実行成功
 *						false		実行失敗 情報取得失敗
 */
function fncDeleteSales($lngSalesNo, $objDB, $objAuth)
{
	// 売上番号をキーに売上コードを取得
	$strSalesCodeQuery = "SELECT strsalescode FROM m_Sales WHERE lngSalesNo = " . $lngSalesNo;
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strSalesCodeQuery, $objDB );
	if ( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$strSalesCode = $objResult->strsalescode;
	}
	else
	{
		// 売上コード取得失敗
		return false;
	}
	$objDB->freeResult( $lngResultID );
	
	// 売上マスタのシーケンスを取得
	$sequence_m_sales = fncGetSequence( 'm_Sales.lngSalesNo', $objDB );

	/*
	// 最小リビジョン番号の取得
	$strRevisionGetQuery = "SELECT MIN(lngRevisionNo) as minrevision FROM m_Sales WHERE strSalesCode = '" . $strSalesCode . "'";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strRevisionGetQuery, $objDB );
	if ( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngMinRevisionNo = $objResult->minrevision;
		if ( $lngMinRevisionNo > 0 )
		{
			$lngMinRevisionNo = 0;
		}
	}
	else
	{
		$lngMinRevisionNo = 0;
	}
	$objDB->freeResult( $lngResultID );
	$lngMinRevisionNo--;
	*/
	// リビジョン番号は-1固定（仕様書に準ずる）
	$lngMinRevisionNo = -1;

	// 売上マスタにリビジョン番号が -1 のレコードを追加
	$aryQuery[] = "INSERT INTO m_sales (";
	$aryQuery[] = " lngSalesNo,";				// 1:売上番号
	$aryQuery[] = " lngRevisionNo, ";			// 2:リビジョン番号
	$aryQuery[] = " strSalesCode, ";    		// 3:売上コード
	$aryQuery[] = " lngInputUserCode, ";		// 4:入力者コード
	$aryQuery[] = " bytInvalidFlag, "; 			// 5:無効フラグ
	$aryQuery[] = " dtmInsertDate";				// 6:登録日
	$aryQuery[] = ") values (";
	$aryQuery[] = $sequence_m_sales . ", ";		// 1:売上番号
	$aryQuery[] = $lngMinRevisionNo . ", ";		// 2:リビジョン番号
	$aryQuery[] = "'" . $strSalesCode . "', ";	// 3:売上コード．
	$aryQuery[] = $objAuth->UserCode . ", ";	// 4:入力者コード
	$aryQuery[] = "false, ";					// 5:無効フラグ
	$aryQuery[] = "now()";						// 6:登録日
	$aryQuery[] = ")";

	unset($strQuery);
	$strQuery = implode("\n", $aryQuery );

	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		// レコード追加失敗
		return false;
	}
	$objDB->freeResult( $lngResultID );

	// 処理成功
	return true;
}

/**
 * 納品書データの削除
 * 
 *	@param  String 		$strSlipCode	納品伝票コード
 *	@param  Object		$objDB			DBオブジェクト
 *	@param  Object		$objAuth		権限オブジェクト
 *	@return Boolean 	true			実行成功
 *						false			実行失敗 情報取得失敗
 */
function fncDeleteSlip($lngSlipNo, $objDB, $objAuth)
{
	// 納品書マスタのシーケンスを取得
	//$sequence_m_slip = fncGetSequence( 'm_Slip.lngSlipNo', $objDB );

	/*
	// 最小リビジョン番号の取得
	$strRevisionGetQuery = "SELECT MIN(lngRevisionNo) as minrevision FROM m_Slip WHERE strSlipCode = '" . $strSlipCode . "'";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strRevisionGetQuery, $objDB );
	if ( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngMinRevisionNo = $objResult->minrevision;
		if ( $lngMinRevisionNo > 0 )
		{
			$lngMinRevisionNo = 0;
		}
	}
	else
	{
		$lngMinRevisionNo = 0;
	}
	$objDB->freeResult( $lngResultID );
	// 基本ここで -1 になる
	$lngMinRevisionNo--;
	*/

	// リビジョン番号は-1固定（仕様書に準ずる）
	$lngMinRevisionNo = -1;

	// 納品書マスタにリビジョン番号が -1 のレコードを追加
	$aryQuery[] = "INSERT INTO m_slip (";
	$aryQuery[] = " lngSlipNo,";					// 1:納品伝票番号
	$aryQuery[] = " lngRevisionNo, ";				// 2:リビジョン番号
	$aryQuery[] = " lnginsertusercode, ";			// 4:入力者コード
	$aryQuery[] = " bytInvalidFlag, "; 				// 5:無効フラグ
	$aryQuery[] = " dtmInsertDate";					// 6:登録日
	$aryQuery[] = ") values (";
	$aryQuery[] = $lngSlipNo . ", ";			// 1:納品伝票番号
	$aryQuery[] = "-1, ";			// 2:リビジョン番号
	$aryQuery[] = "'" . $objAuth->UserCode . "', ";	// 4:入力者コード
	$aryQuery[] = "false, ";						// 5:無効フラグ
	$aryQuery[] = "now()";							// 6:登録日
	$aryQuery[] = ")";

	unset($strQuery);
	$strQuery = implode("\n", $aryQuery );

	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		// レコード追加失敗
		return false;
	}
	$objDB->freeResult( $lngResultID );

	// 処理成功
	return true;
}

/**
 * 受注明細のステータス更新
 * 
 *	@param  Long 		$lngSlipNo	納品伝票番号
 *	@param  Object		$objDB		DBオブジェクト
 *	@return Boolean 	true		実行成功
 *						false		実行失敗 情報取得失敗
 */
function fncUpdateReceiveStatus($lngSlipNo, $objDB)
{
	// 納品伝票明細データの取得
	$strQuery = fncGetSlipDetailNoToInfoSQL ( $lngSlipNo );
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		// 納品伝票番号に紐づく納品伝票明細が見つからない
		return false;
	}

	for ( $i = 0; $i < count($aryDetailResult); $i++)
	{
		// 受注番号
		$lngReceiveNo = $aryDetailResult[$i]["lngreceiveno"];

		// 受注マスタより受注コードを取得
		$strReceiveCodeQuery = "SELECT strreceivecode FROM m_Receive WHERE lngReceiveNo = " . $lngReceiveNo;
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strReceiveCodeQuery, $objDB );
		if ( $lngResultNum )
		{
			$objResult = $objDB->fetchObject( $lngResultID, 0 );
			$strReceiveCode = $objResult->strreceivecode;
		}
		else
		{
			// 受注コード取得失敗
			return false;
		}
		$objDB->freeResult( $lngResultID );

		// 受注マスタの更新対象レコード選択条件
		$strWhere = "WHERE ";
		$strWhere .= "strReceiveCode = '" . $strReceiveCode . "'";
		$strWhere .= " and lngRevisionNo = (SELECT MAX(lngRevisionNo) FROM m_Receive WHERE strReceiveCode = '" . $strReceiveCode . "')";

		// 更新対象レコードの行ロック（選択したレコードに対し現在のトランザクションを終了するまで他のトランザクションによるUPDATEを禁止する）
		$strLockQuery = "SELECT * FROM m_Receive ";
		$strLockQuery .= $strWhere;
		list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
		if (!$lngLockResultID){ return false; }
		$objDB->freeResult( $lngLockResultID );

		// 更新対象レコードの受注状態コードを「受注」に更新
		$strUpdateQuery = "UPDATE m_Receive ";
		$strUpdateQuery .= "SET lngReceiveStatusCode = " . DEF_RECEIVE_ORDER;
		$strUpdateQuery .= $strWhere;
		list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
		if (!$lngUpdateResultID){ return false; }
		$objDB->freeResult( $lngUpdateResultID );
	}

	// 処理成功
	return true;

}

?>
