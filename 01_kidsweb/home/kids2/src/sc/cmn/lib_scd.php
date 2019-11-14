<?
// ----------------------------------------------------------------------------
/**
*       売上管理  納品書検索関連関数群
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
*         ・納品書検索結果関連の関数
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



/**
* 納品書検索の検索項目から一致する最新の売上データを取得するSQL文の作成関数
*
*	納品書検索の検索項目から SQL文を作成する
*
*	@param  Array 	$arySearchColumn 		検索対象カラム名の配列
*	@param  Array 	$arySearchDataColumn 	検索内容の配列
*	@param  Object	$objDB       			DBオブジェクト
*	@param	String	$strSlipCode			納品伝票コード	空白指定時:検索結果出力	納品伝票コード指定時:管理用、同じ納品書ＮＯの一覧取得
*	@param	Integer	$lngSlipNo				納品伝票番号	0:検索結果出力	納品伝票番号指定時:管理用、同じ納品伝票コードとする時の対象外納品伝票番号
*	@return Array 	$strSQL 検索用SQL文 OR Boolean FALSE
*	@access public
*/
function fncGetSearchSlipSQL ( $arySearchColumn, $arySearchDataColumn, $objDB, $strSlipCode, $lngSlipNo, $strSessionID)
{
	// -----------------------------
	//  検索条件の動的設定
	// -----------------------------
	// 明細条件追加済みフラグ
	$detailFlag = FALSE;

	// 同じ納品伝票コードのデータを取得する場合
	if ( $strSlipCode )
	{
		// 同じ納品伝票コードに対して指定の納品伝票番号のデータは除外する
		if ( $lngSlipNo )
		{
			$aryQuery[] = " WHERE s.bytInvalidFlag = FALSE AND s.strSlipCode = '" . $strSlipCode . "'";
		}
		else
		{
			fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../sc/search2/index.php?strSessionID=".$strSessionID, $objDB );
		}
	}
	// 管理モードでの同じ納品伝票コードに対する検索モード以外の場合は検索条件を追加する
	else
	{
		// 絶対条件 無効フラグが設定されておらず、最新売上のみ
		$aryQuery[] = " WHERE s.bytInvalidFlag = FALSE AND s.lngRevisionNo >= 0";

		// 検索チェックボックスがONの項目のみ検索条件に追加
		for ( $i = 0; $i < count($arySearchColumn); $i++ )
		{
			$strSearchColumnName = $arySearchColumn[$i];
			
			// ----------------------------------------------
			//   納品書マスタ（ヘッダ部）の検索条件
			// ----------------------------------------------
			// 顧客（売上先）
			if ( $strSearchColumnName == "lngCustomerCompanyCode" )
			{
				if ( $arySearchDataColumn["lngCustomerCompanyCode"] )
				{
					$aryQuery[] = " AND cust_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngCustomerCompanyCode"] . "'";
				}
			}

			// 課税区分（消費税区分）
			if ( $strSearchColumnName == "lngTaxClassCode" )
			{
				if ( $arySearchDataColumn["lngTaxClassCode"] )
				{
					$aryQuery[] = " AND s.lngTaxClassCode = '" . $arySearchDataColumn["lngTaxClassCode"] . "'";
				}
			}

			// 納品伝票コード（納品書NO）
			if ( $strSearchColumnName == "strSlipCode" )
			{
				if ( $arySearchDataColumn["strSlipCode"] )
				{
					// カンマ区切りの入力値をOR条件に展開
					$arySCValue = explode(",",$arySearchDataColumn["strSlipCode"]);
					foreach($arySCValue as $strSCValue){
						$arySCOr[] = "UPPER(s.strSlipCode) LIKE UPPER('%" . $strSCValue . "%')";
					}
					$aryQuery[] = " AND (";
					$aryQuery[] = implode(" OR ", $arySCOr);
					$aryQuery[] = ") ";
				}
			}

			// 納品日
			if ( $strSearchColumnName == "dtmDeliveryDate" )
			{
				if ( $arySearchDataColumn["dtmDeliveryDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmDeliveryDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND s.dtmDeliveryDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmDeliveryDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmDeliveryDateTo"] . " 23:59:59";
					$aryQuery[] = " AND s.dtmDeliveryDate <= '" . $dtmSearchDate . "'";
				}
			}

			// 納品先
			if ( $strSearchColumnName == "lngDeliveryPlaceCode" )
			{
				if ( $arySearchDataColumn["lngDeliveryPlaceCode"] )
				{
					//会社マスタと紐づけた値と比較
					$aryQuery[] = " AND delv_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'";
				}
//				if ( $arySearchDataColumn["strDeliveryPlaceName"] )
//				{
//					$aryQuery[] = " AND UPPER(s.strDeliveryPlaceName) LIKE UPPER('%" . $arySearchDataColumn["strDeliveryPlaceName"] . "%')";
//				}
			}

			// 起票者
			if ( $strSearchColumnName == "lngInsertUserCode" )
			{
				if ( $arySearchDataColumn["lngInsertUserCode"] )
				{
					$aryQuery[] = " AND insert_u.struserdisplaycode ~* '" . $arySearchDataColumn["lngInsertUserCode"] . "'";
				}
//				if ( $arySearchDataColumn["strInsertUserName"] )
//				{
//					$aryQuery[] = " AND UPPER(s.strInsertUserName) LIKE UPPER('%" . $arySearchDataColumn["strInsertUserName"] . "%')";
//				}
			}

			// ----------------------------------------------
			//   納品伝票明細テーブル（明細部）の検索条件
			// ----------------------------------------------
			// 注文書NO.
			if ( $strSearchColumnName == "strCustomerSalesCode" )
			{
				if ( $arySearchDataColumn["strCustomerSalesCode"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						unset( $aryDetailTargetQuery );
						$aryDetailTargetQuery[] = " where";
						
						$aryDetailWhereQuery[] = "AND ";
					}

					// カンマ区切りの入力値をOR条件に展開
					$aryCSCValue = explode(",",$arySearchDataColumn["strCustomerSalesCode"]);
					foreach($aryCSCValue as $strCSCValue){
						$aryCSCOr[] = "UPPER(sd1.strCustomerSalesCode) LIKE UPPER('%" . $strCSCValue . "%')";
					}
					$aryDetailWhereQuery[] = " (";
					$aryDetailWhereQuery[] = implode(" OR ", $aryCSCOr);
					$aryDetailWhereQuery[] = ") ";

					$detailFlag = TRUE;
				}
			}
		
			// 顧客品番
			if ( $strSearchColumnName == "strGoodsCode" )
			{
				if ( $arySearchDataColumn["strGoodsCode"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						unset( $aryDetailTargetQuery );
						$aryDetailTargetQuery[] = " where";
						
						$aryDetailWhereQuery[] = "AND ";
					}

					// カンマ区切りの入力値をOR条件に展開
					$aryGCValue = explode(",",$arySearchDataColumn["strGoodsCode"]);
					foreach($aryGCValue as $strGCValue){
						$aryGCOr[] = "UPPER(sd1.strGoodsCode) LIKE UPPER('%" . $strGCValue . "%')";
					}
					$aryDetailWhereQuery[] = " (";
					$aryDetailWhereQuery[] = implode(" OR ", $aryGCOr);
					$aryDetailWhereQuery[] = ") ";

					$detailFlag = TRUE;
				}
			}

			// 売上区分
			if ( $strSearchColumnName == "lngSalesClassCode" )
			{
				if ( $arySearchDataColumn["lngSalesClassCode"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
					$aryDetailWhereQuery[] = "sd1.lngSalesClassCode = " . $arySearchDataColumn["lngSalesClassCode"] . " ";
					$detailFlag = TRUE;
				}
			}
		}
	}



	// ---------------------------------
	//   SQL文の作成
	// ---------------------------------
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT distinct s.lngSlipNo as lngSlipNo";	//納品伝票番号
	$aryOutQuery[] = "	,s.lngSalesNo as lngSalesNo";			    //売上番号
	$aryOutQuery[] = "	,s.lngRevisionNo as lngRevisionNo";			//リビジョン番号
	$aryOutQuery[] = "	,s.dtmInsertDate as dtmInsertDate";			//作成日
	// 顧客
	$arySelectQuery[] = ", cust_c.strcompanydisplaycode as strCustomerDisplayCode";
	$arySelectQuery[] = ", cust_c.strcompanydisplayname as strCustomerDisplayName";
	// 顧客の国
	$arySelectQuery[] = ", cust_c.lngCountryCode as lngcountrycode";
	// 請求書番号
	$arySelectQuery[] = ", sa.lngInvoiceNo as lnginvoiceno";
	// 課税区分
	$arySelectQuery[] = ", s.strTaxClassName as strTaxClassName";
	// 納品伝票コード（納品書NO）
	$arySelectQuery[] = ", s.strSlipCode as strSlipCode";
	// 納品日
	$arySelectQuery[] = ", to_char( s.dtmDeliveryDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmDeliveryDate";
	// 納品先
	$arySelectQuery[] = " , delv_c.strcompanydisplaycode as strdeliveryplacecode";
	$arySelectQuery[] = " , s.strDeliveryPlaceName as strDeliveryPlaceName";
	// 起票者
	$arySelectQuery[] = ", insert_u.struserdisplaycode as strInsertUserCode";
	$arySelectQuery[] = ", s.strInsertUserName as strInsertUserName";
	// 備考
	$arySelectQuery[] = ", s.strNote as strNote";
	// 合計金額
	$arySelectQuery[] = ", To_char( s.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
	//// 売上Ｎｏ
	$arySelectQuery[] = ", sa.strSalesCode as strSalesCode";
	// 売上状態コード
	$arySelectQuery[] = ", sa.lngSalesStatusCode as lngSalesStatusCode";
	$arySelectQuery[] = ", ss.strSalesStatusName as strSalesStatusName";
	// 通貨単位
	$arySelectQuery[] = ", s.lngMonetaryUnitCode";
	$arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";

	// select句 クエリー連結
	$aryOutQuery[] = implode("\n", $arySelectQuery);

	// From句 の生成
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM m_Slip s";
//	if ( !$strSlipCode )
//	{
		 $aryFromQuery[] = "INNER JOIN (SELECT lngSlipNo, MAX(lngRevisionNo) AS lngRevisionNo from m_slip group by lngSlipNo) max_rev "
		 . "on max_rev.lngSlipNo = s.lngslipno and max_rev.lngRevisionNo = s.lngrevisionno";

//    }
	$aryFromQuery[] = " INNER JOIN m_Sales sa ON s.lngSalesNo = sa.lngSalesNo AND s.lngRevisionNo = sa.lngRevisionNo";
	$aryFromQuery[] = " LEFT JOIN m_SalesStatus ss ON sa.lngSalesStatusCode = ss.lngSalesStatusCode";
	$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON s.lngCustomerCode = cust_c.lngCompanyCode";
	$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	$aryFromQuery[] = " LEFT JOIN m_User insert_u ON s.lngInsertUserCode = insert_u.lngusercode";
	$aryFromQuery[] = " LEFT JOIN m_Company delv_c ON s.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
	// From句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryFromQuery);

	// 明細検索用テーブル結合条件
	$aryDetailFrom = array();
	$aryDetailFrom[] = ", (SELECT distinct on ( sd1.lngSlipNo ) sd1.lngSlipNo ";
	$aryDetailFrom[] = "	,sd1.lngSlipDetailNo";				// 納品伝票明細番号
	$aryDetailFrom[] = "	,sd1.lngSortKey as lngRecordNo";	// 明細行NO
	$aryDetailFrom[] = "	,sd1.strCustomerSalesCode";			// 注文書NO
	$aryDetailFrom[] = "	,sd1.strGoodsCode";					// 顧客品番
	$aryDetailFrom[] = "	,sd1.strProductName";				// 品名
	$aryDetailFrom[] = "	,sd1.strSalesClassName";	// 売上区分
	$aryDetailFrom[] = "	,sd1.curProductPrice";		// 単価
	$aryDetailFrom[] = "	,sd1.lngQuantity";	        // 入数
	$aryDetailFrom[] = "	,sd1.lngProductQuantity";	// 数量
	$aryDetailFrom[] = "	,sd1.strProductUnitName";	// 単位
	$aryDetailFrom[] = "	,sd1.curSubTotalPrice";		// 税抜金額
	$aryDetailFrom[] = "	,sd1.strNote";				// 明細備考
	$aryDetailFrom[] = "	FROM t_SlipDetail sd1 ";
	// where句（明細行） クエリー連結
	$strDetailQuery = implode("\n", $aryDetailFrom) . "\n";
	// 明細行の条件が存在する場合
	if ( $detailFlag )
	{
		$strDetailQuery .= implode("\n", $aryDetailTargetQuery) . "\n";
	}
	$aryDetailWhereQuery[] = ") as sd";
	$strDetailQuery .= implode("\n", $aryDetailWhereQuery) . "\n";
	
	// Where句 クエリー連結
	$aryOutQuery[] = $strDetailQuery;
	$aryOutQuery[] = implode("\n", $aryQuery);

	// 明細行用の条件連結
	$aryOutQuery[] = " AND sd.lngSlipNo = s.lngSlipNo";


	/////////////////////////////////////////////////////////////
	//// 最新売上（リビジョン番号が最大、リバイズ番号が最大、     ////
	//// かつリビジョン番号負の値で無効フラグがFALSEの           ////
	//// 同じ納品伝票コードを持つデータが無い売上データ          ////
	/////////////////////////////////////////////////////////////
	// 納品伝票コードが指定されていない場合は検索条件を設定する
	if ( !$strSlipCode )
	{
		// 管理モードの場合は削除データも検索対象とするため以下の条件は対象外
		if ( !$arySearchDataColumn["Admin"] )
		{
//			$aryOutQuery[] = " AND 0 <= ( "
//				. "SELECT MIN( s2.lngRevisionNo ) FROM m_Slip s2 WHERE s2.bytInvalidFlag = false AND s2.strSlipCode = s.strSlipCode )";
			$aryOutQuery[] = " AND not exists (SELECT lngslipno from m_slip s1 where s1.lngslipno=s.lngslipno and s1.lngRevisionNo < 0 and s1.bytInvalidFlag = false)";
		}
	}

	// 同じ納品伝票コードのデータを取得する場合
	if ($strSlipCode)
	{
		$aryOutQuery[] = " ORDER BY dtmInsertDate DESC";
	}
	else
	{
		// ソート条件設定
		$aryOutQuery[] = " ORDER BY lngSlipNo DESC";		
	}
	return implode("\n", $aryOutQuery);
}



/**
* 指定した納品伝票番号のデータに対応する「明細行」を取得するSQL文の作成関数
*
*	納品伝票番号から明細を取得するSQL文を作成する
*
*	@param  String 	$lngSlipNo 			    対象納品伝票番号
*	@param  Array 	$aryData 				POSTデータの配列
*	@param  Object	$objDB       			DBオブジェクト
*	@return Array 	$strSQL 検索用SQL文 OR Boolean FALSE
*	@access public
*/
function fncGetSlipToProductSQL ( $lngSlipNo, $lngRevisionNo, $aryData, $objDB )
{
	// ----------------------
	//   SQL文の作成
	// ----------------------
	$aryOutQuery = array();
	//明細行NO
	$aryOutQuery[] = "SELECT sd.lngSortKey as lngRecordNo";
	//納品伝票番号
	$aryOutQuery[] = "	,sd.lngSlipNo as lngSlipNo";
	//リビジョン番号	
	$aryOutQuery[] = "	,sd.lngRevisionNo as lngRevisionNo";
	// 注文書NO.
	$aryOutQuery[] = ", sd.strCustomerSalesCode as strCustomerSalesCode";
	// 顧客品番
	$aryOutQuery[] = ", sd.strGoodsCode as strGoodsCode";
	// 品名
	$aryOutQuery[] = ", sd.strProductName as strProductName";
	// 売上区分
	$aryOutQuery[] = ", sd.lngSalesClassCode as lngSalesClassCode";
	$aryOutQuery[] = ", sd.strSalesClassName as strSalesClassName";
	// 単価
	$aryOutQuery[] = ", To_char( sd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
	// 入数
	$aryOutQuery[] = ", To_char( sd.lngQuantity, '9,999,999,990' )  as lngQuantity";
	// 数量
	$aryOutQuery[] = ", To_char( sd.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
	// 単位
	$aryOutQuery[] = ", sd.strProductUnitName as strProductUnitName";
	// 税抜金額
	$aryOutQuery[] = ", To_char( sd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
	// 明細備考
	$aryOutQuery[] = ", sd.strNote as strDetailNote";
	// 受注ステータスコード
	$aryOutQuery[] = ", re.lngReceiveStatusCode as lngReceiveStatusCode";

	// From句
	$aryOutQuery[] = " FROM t_SlipDetail sd";
	$aryOutQuery[] = "  LEFT JOIN ( ";
	$aryOutQuery[] = "    select";
	$aryOutQuery[] = "      r1.* ";
	$aryOutQuery[] = "    from";
	$aryOutQuery[] = "      m_Receive r1 ";
	$aryOutQuery[] = "      inner join ( ";
	$aryOutQuery[] = "        select";
	$aryOutQuery[] = "          max(lngRevisionNo) lngRevisionNo";
	$aryOutQuery[] = "          , strreceivecode ";
	$aryOutQuery[] = "        from";
	$aryOutQuery[] = "          m_Receive ";
	$aryOutQuery[] = "        group by";
	$aryOutQuery[] = "          strreceivecode";
	$aryOutQuery[] = "      ) r2 ";
	$aryOutQuery[] = "        on r1.lngrevisionno = r2.lngRevisionNo ";
	$aryOutQuery[] = "        and r1.strreceivecode = r2.strreceivecode";
	$aryOutQuery[] = "  ) re ";
	$aryOutQuery[] = "    ON sd.lngReceiveNo = re.lngReceiveNo ";

	// Where句
	$aryOutQuery[] = " WHERE sd.lngSlipNo = " . $lngSlipNo . " AND sd.lngRevisionNo = " . $lngRevisionNo . "";	// 対象納品伝票番号の指定

	// OrderBy句
	$aryOutQuery[] = " ORDER BY sd.lngSortKey ASC";

	return implode("\n", $aryOutQuery);
}


/**
 * 納品書コードによりデータの状態を確認する
 *
 * @param [type] $strstrslipcode
 * @param [type] $objDB
 * @return void [0:削除済データ　1：確定対象データ]
 */
function fncCheckData($strslipcode, $objDB)
{
    $result = 0;
    unset($aryQuery);
    $aryQuery[] = "SELECT";
    $aryQuery[] = " min(lngrevisionno) lngrevisionno, bytInvalidFlag, strslipcode ";
    $aryQuery[] = "FROM m_slip ";
    $aryQuery[] = "WHERE strslipcode='" . $strslipcode . "'";
    $aryQuery[] = "group by strslipcode, bytInvalidFlag";
    // クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum) {
        $resultObj = $objDB->fetchArray($lngResultID, 0);
    }

    $objDB->freeResult($lngResultID);

    if ($resultObj["lngrevisionno"] < 0) {
        $result = 1;
    }
    return $result;
}

/**
 * 明細データの取得
 *
 * @param [type] $lngSlipNo
 * @param [type] $lngRevisionNo
 * @param [type] $objDB
 * @return void
 */
function fncGetDetailData($lngSlipNo, $lngRevisionNo, $objDB)
{
    $detailData = array();
    unset($aryQuery);
	$aryQuery[] = "select";
	$aryQuery[] = "  sd.lngSlipDetailNo";
	$aryQuery[] = "  , sd.strCustomerSalesCode";
	$aryQuery[] = "  , sd.strGoodsCode";
	$aryQuery[] = "  , sd.strProductCode";
	$aryQuery[] = "  , sd.strProductName";
	$aryQuery[] = "  , sd.strSalesClassName";
	$aryQuery[] = "  , to_char(sd.curProductPrice, '9,999,999,990.99') as curProductPrice";
	$aryQuery[] = "  , to_char(sd.lngQuantity, '9,999,999,990.99') as lngQuantity";
	$aryQuery[] = "  , to_char(sd.lngProductQuantity, '9,999,999,990.99') as lngProductQuantity";
	$aryQuery[] = "  , sd.strProductUnitName";
	$aryQuery[] = "  , to_char(sd.curSubTotalPrice, '9,999,999,990.99') as curSubTotalPrice";
	$aryQuery[] = "  , sd.strNote ";
	$aryQuery[] = "from";
	$aryQuery[] = "  t_slipdetail sd ";
	$aryQuery[] = "where";
	$aryQuery[] = "  sd.lngslipno = " . $lngSlipNo;
    $aryQuery[] = "  AND sd.lngrevisionno = " . $lngRevisionNo;
    $aryQuery[] = "ORDER BY";
    $aryQuery[] = "  sd.lngSlipDetailNo ASC";
    // クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    // 検索件数がありの場合
    if ($lngResultNum > 0) {
        // 指定数以内であれば通常処理
        for ($i = 0; $i < $lngResultNum; $i++) {
            $detailData = pg_fetch_all($lngResultID);
        }
    }
    $objDB->freeResult($lngResultID);

    return $detailData;
}


/**
 * ヘッダー部データの生成
 *
 * @param [type] $doc
 * @param [type] $trBody
 * @param [type] $bgcolor
 * @param [type] $aryTableHeaderName
 * @param [type] $record
 * @param [type] $toUTF8Flag
 * @return void
 */
function fncSetHeaderDataToTr($doc, $trBody, $bgcolor, $rowspan, $aryTableHeaderName, $record, $toUTF8Flag)
{
	// TODO 要リファクタリング
    // 指定されたテーブル項目のセルを作成する
    foreach ($aryTableHeaderName as $key => $value) {
        // 項目別に表示テキストを設定
        switch ($key) {
            // 顧客
            case "lngCustomerCode":
                if ($record["strcustomerdisplaycode"] != '') {
                    $textContent = "[" . $record["strcustomerdisplaycode"] . "]" . " " . $record["strcustomerdisplayname"];
                } else {
                    $textContent .= "     ";
				}
				if ($toUTF8Flag) {
					$textContent = toUTF8($textContent);
				}
                $td = $doc->createElement("td", $textContent);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // 課税区分
			case "lngTaxClassCode":
				$textContent = $record["strtaxclassname"];
				if ($toUTF8Flag) {
					$textContent = toUTF8($textContent);
				}
                $td = $doc->createElement("td", $textContent);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // 納品書NO.
            case "strSlipCode":
                $td = $doc->createElement("td", $record["strslipcode"]);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // 納品日
            case "dtmDeliveryDate":
                $td = $doc->createElement("td", str_replace("-", "/", substr($record["dtmdeliverydate"], 0, 19)));
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // 納品先
            case "lngDeliveryPlaceCode":
                if ($record["strdeliveryplacecode"] != '') {
                    $textContent = "[" . $record["strdeliveryplacecode"] . "]" . " " . $record["strdeliveryplacename"];
                } else {
                    $textContent = "     ";
				}
				if ($toUTF8Flag) {
					$textContent = toUTF8($textContent);
				}
                $td = $doc->createElement("td", $textContent);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // 起票者
            case "lngInsertUserCode":
                if ($record["strinsertusercode"] != '') {
                    $textContent = "[" . $record["strinsertusercode"] . "]" . " " . $record["strinsertusername"];
                } else {
                    $textContent .= "     ";
				}				
				if ($toUTF8Flag) {
					$textContent = toUTF8($textContent);
				}
                $td = $doc->createElement("td", $textContent);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // 備考
			case "strNote":
				$textContent = $record["strnote"];
				if ($toUTF8Flag) {
					$textContent = toUTF8($textContent);
				}
                $td = $doc->createElement("td", $textContent);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // 合計金額
            case "curTotalPrice":
                $textContent = toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curtotalprice"]);
                $td = $doc->createElement("td", $textContent);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
        }
	}
	
    return $trBody;
}

/**
 * 明細行データの生成
 *
 * @param [type] $doc
 * @param [type] $trBody
 * @param [type] $bgcolor
 * @param [type] $aryTableDetailHeaderName
 * @param [type] $displayColumns
 * @param [type] $detailData
 * @return void
 */
function fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $detailData, $headerData, $toUTF8Flag)
{
    // 指定されたテーブル項目のセルを作成する
    foreach ($aryTableDetailHeaderName as $key => $value) {
            // 項目別に表示テキストを設定
            switch ($key) {                
                // 明細行番号
                case "lngRecordNo":
                    $td = $doc->createElement("td", $detailData["lngslipdetailno"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 納品日
                case "dtmDeliveryDate":
                    if ($toUTF8Flag) {
                        $td = $doc->createElement("td", str_replace( "-", "/", toUTF8(substr( $detailData["dtmdeliverydate"], 0, 19 ))));
                    } else {
                        $td = $doc->createElement("td", str_replace( "-", "/", substr( $detailData["dtmdeliverydate"], 0, 19 )));
                       
                    }
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 注文書NO
                case "strCustomerSalesCode":
                    $td = $doc->createElement("td", $detailData["strcustomersalescode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
					break;				
                // 顧客品番
                case "strGoodsCode":
                    $td = $doc->createElement("td", $detailData["strgoodscode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;		
                // 品名
                case "strProductName":
					$textContent = "[" . $detailData["strproductcode"] . "]" . " " . $detailData["strproductname"];                    
					if ($toUTF8Flag) {
						$textContent = toUTF8($textContent);
					}
					$td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
					break;			
                // 売上区分
                case "strSalesClassName":
					$textContent = $detailData["strsalesclassname"];                    
					if ($toUTF8Flag) {
						$textContent = toUTF8($textContent);
					}
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 単価
                case "curProductPrice":
                    $textContent = toMoneyFormat($headerData["lngmonetaryunitcode"], $headerData["strmonetaryunitsign"], $detailData["curproductprice"]);
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 入数
                case "lngQuantity":
                    $textContent = $detailData["lngquantity"];                    
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 数量
                case "lngProductQuantity":
                    $textContent = $detailData["lngproductquantity"];                    
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 単位
                case "strProductUnitName":
                    $textContent = $detailData["strproductunitname"];                    
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 税抜金額
                case "curSubTotalPrice":
                    $textContent = toMoneyFormat($headerData["lngmonetaryunitcode"], $headerData["strmonetaryunitsign"], $detailData["cursubtotalprice"]);
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 明細備考
                case "strDetailNote":
                    $textContent = $detailData["strnote"];
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
            }

    }
    return $trBody;
}



function fncGetSlipsByStrSlipCodeSQL($strslipcode, $lngrevisionno)
{
	$aryQuery[] = "SELECT distinct";
	$aryQuery[] = "  s.lngSlipNo as lngSlipNo";
	$aryQuery[] = "  , s.lngSalesNo as lngSalesNo";
	$aryQuery[] = "  , s.lngRevisionNo as lngRevisionNo";
	$aryQuery[] = "  , s.dtmInsertDate as dtmInsertDate";
	$aryQuery[] = "  , cust_c.strcompanydisplaycode as strCustomerDisplayCode";
	$aryQuery[] = "  , cust_c.strcompanydisplayname as strCustomerDisplayName";
	$aryQuery[] = "  , cust_c.lngCountryCode as lngcountrycode";
	$aryQuery[] = "  , sa.lngInvoiceNo as lnginvoiceno";
	$aryQuery[] = "  , s.strTaxClassName as strTaxClassName";
	$aryQuery[] = "  , s.strSlipCode as strSlipCode";
	$aryQuery[] = "  , to_char(s.dtmDeliveryDate, 'YYYY/MM/DD HH:MI:SS') as dtmDeliveryDate";
	$aryQuery[] = "  , delv_c.strcompanydisplaycode as strdeliveryplacecode";
	$aryQuery[] = "  , s.strDeliveryPlaceName as strDeliveryPlaceName";
	$aryQuery[] = "  , insert_u.struserdisplaycode as strInsertUserCode";
	$aryQuery[] = "  , s.strInsertUserName as strInsertUserName";
	$aryQuery[] = "  , s.strNote as strNote";
	$aryQuery[] = "  , To_char(s.curTotalPrice, '9,999,999,990.99') as curTotalPrice";
	$aryQuery[] = "  , sa.strSalesCode as strSalesCode";
	$aryQuery[] = "  , sa.lngSalesStatusCode as lngSalesStatusCode";
	$aryQuery[] = "  , ss.strSalesStatusName as strSalesStatusName";
	$aryQuery[] = "  , s.lngMonetaryUnitCode ";
	$aryQuery[] = "  , mu.strMonetaryUnitSign as strMonetaryUnitSign ";
	$aryQuery[] = "FROM";
	$aryQuery[] = "  m_Slip s ";
	$aryQuery[] = "  INNER JOIN m_Sales sa ";
	$aryQuery[] = "    ON s.lngSalesNo = sa.lngSalesNo ";
	$aryQuery[] = "    AND s.lngRevisionNo = sa.lngRevisionNo ";
	$aryQuery[] = "  LEFT JOIN m_SalesStatus ss ";
	$aryQuery[] = "    ON sa.lngSalesStatusCode = ss.lngSalesStatusCode ";
	$aryQuery[] = "  LEFT JOIN m_Company cust_c ";
	$aryQuery[] = "    ON s.lngCustomerCode = cust_c.lngCompanyCode ";
	$aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
	$aryQuery[] = "    ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode ";
	$aryQuery[] = "  LEFT JOIN m_User insert_u ";
	$aryQuery[] = "    ON s.lngInsertUserCode = insert_u.lngusercode ";
	$aryQuery[] = "  LEFT JOIN m_Company delv_c ";
	$aryQuery[] = "    ON s.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
	$aryQuery[] = "WHERE";
	$aryQuery[] = "  s.bytInvalidFlag = FALSE ";
	$aryQuery[] = "  AND s.lngRevisionNo <>" .$lngrevisionno. "";
	$aryQuery[] = "  AND s.strslipcode = '". $strslipcode."'";
	$aryQuery[] = "ORDER BY";
	$aryQuery[] = "  s.lngrevisionno DESC";

    return implode("\n", $aryQuery);
}
?>