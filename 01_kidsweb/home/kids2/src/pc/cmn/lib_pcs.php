<?
// ----------------------------------------------------------------------------
/**
 *       仕入管理  検索関連関数群
 *
 *
 *       処理概要
 *         ・検索結果関連の関数
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

/**
 * 検索項目から一致する最新の仕入データを取得するSQL文の作成関数
 *
 *    検索項目から SQL文を作成する
 *
 *    @param  Array     $displayColumns             表示対象カラム名の配列
 *    @param  Array     $searchColumns         検索対象カラム名の配列
 *    @param  Array     $from     検索内容(from)の配列
 *    @param  Array     $to       検索内容(to)の配列
 *    @param  Array     $searchValue  検索内容の配列
 *    @return Array     $strSQL 検索用SQL文
 *    @access public
 */
function fncGetMaxStockSQL($displayColumns, $searchColumns, $from, $to, $searchValue, $optionColumns)
{
	$detailConditionCount = 0;

// クエリの組立て
    $aryQuery = array();
    $aryQuery[] = "SELECT";
    $aryQuery[] = "     s.lngstockno";
    $aryQuery[] = "   , s.lngRevisionNo as lngRevisionNo";
    $aryQuery[] = "   , sd.strordercode";
    $aryQuery[] = "   , to_char(s.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS') as dtmInsertDate";
    $aryQuery[] = "   , to_char(s.dtmappropriationdate, 'YYYY/MM/DD') as dtmappropriationdate";
    $aryQuery[] = "   , to_char(s.dtmexpirationdate, 'YYYY/MM/DD') as dtmexpirationdate";
    $aryQuery[] = "   , input_u.strUserDisplayCode as strInputUserDisplayCode";
    $aryQuery[] = "   , input_u.strUserDisplayName as strInputUserDisplayName";
    $aryQuery[] = "   , s.strStockCode as strStockCode";
    $aryQuery[] = "   , s.strslipcode as strslipcode";
    $aryQuery[] = "   , cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
    $aryQuery[] = "   , cust_c.strCompanyDisplayName as strCustomerDisplayName";
    $aryQuery[] = "   , s.lngStockStatusCode as lngStockStatusCode";
    $aryQuery[] = "   , rs.strStockStatusName as strStockStatusName";
    $aryQuery[] = "   , s.lngpayconditioncode as lngpayconditioncode";
    $aryQuery[] = "   , mp.strpayconditionname as strpayconditionname";
    $aryQuery[] = "   , s.strNote as strNote";
    $aryQuery[] = "   , To_char(s.curTotalPrice, '9,999,999,990.99') as curTotalPrice";
    $aryQuery[] = "   , mu.strMonetaryUnitSign as strMonetaryUnitSign";
    $aryQuery[] = "   , mu.lngmonetaryunitcode as lngmonetaryunitcode ";
//    $aryQuery[] = " s.strStockCode";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  m_Stock s ";
    $aryQuery[] = "  inner join ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      max(lngrevisionno) lngrevisionno";
    $aryQuery[] = "      , strStockCode ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_Stock ";
    $aryQuery[] = "    group by";
    $aryQuery[] = "      strStockCode";
    $aryQuery[] = "  ) s1 ";
    $aryQuery[] = "    on s.lngrevisionno = s1.lngrevisionno ";
    $aryQuery[] = "    and s.strStockCode = s1.strStockCode ";
    $aryQuery[] = "  LEFT JOIN m_User input_u ";
    $aryQuery[] = "    ON s.lngInputUserCode = input_u.lngUserCode ";
    $aryQuery[] = "  LEFT JOIN m_Company cust_c ";
    $aryQuery[] = "    ON s.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
    $aryQuery[] = "  LEFT JOIN m_StockStatus rs ";
    $aryQuery[] = "    USING (lngStockStatusCode) ";
    $aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
    $aryQuery[] = "    ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
    $aryQuery[] = "  LEFT JOIN m_paycondition mp ";
    $aryQuery[] = "    ON s.lngpayconditioncode = mp.lngpayconditioncode";
    $aryQuery[] = "  , ( ";
    $aryQuery[] = "      SELECT distinct";
    $aryQuery[] = "          on (sd1.lngStockNo) sd1.lngStockNo";
    $aryQuery[] = "        , sd1.lngStockDetailNo";
    $aryQuery[] = "        , sd1.lngRevisionNo ";
    $aryQuery[] = "        , mp.strordercode";
    $aryQuery[] = "        , p.strProductCode";
    $aryQuery[] = "        , mg.strGroupDisplayCode";
    $aryQuery[] = "        , mg.strGroupDisplayName";
    $aryQuery[] = "        , mu.struserdisplaycode";
    $aryQuery[] = "        , mu.struserdisplayname";
    $aryQuery[] = "        , p.strProductName";
    $aryQuery[] = "        , p.strProductEnglishName";
    $aryQuery[] = "        , sd1.lngStockSubjectCode";
    $aryQuery[] = "        , ss.strStockSubjectName";
    $aryQuery[] = "        , sd1.lngStockItemCode";
    $aryQuery[] = "        , si.strStockItemName";
    $aryQuery[] = "        , sd1.strMoldNo";
    $aryQuery[] = "        , p.strGoodsCode";
    $aryQuery[] = "        , sd1.lngDeliveryMethodCode";
    $aryQuery[] = "        , dm.strDeliveryMethodName";
    $aryQuery[] = "        , sd1.curProductPrice";
    $aryQuery[] = "        , sd1.lngProductUnitCode";
    $aryQuery[] = "        , pu.strProductUnitName";
    $aryQuery[] = "        , sd1.lngProductQuantity";
    $aryQuery[] = "        , sd1.curSubTotalPrice";
    $aryQuery[] = "        , sd1.lngTaxClassCode";
    $aryQuery[] = "        , mtc.strTaxClassName";
    $aryQuery[] = "        , mt.curtax";
    $aryQuery[] = "        , sd1.curtaxprice";
    $aryQuery[] = "        , sd1.strNote ";
    $aryQuery[] = "      FROM";
    $aryQuery[] = "        t_StockDetail sd1 ";
    $aryQuery[] = "        LEFT JOIN (";
    $aryQuery[] = "            select p1.*  from m_product p1 ";
    $aryQuery[] = "        	inner join (select max(lngrevisionno) lngrevisionno, strproductcode from m_Product group by strProductCode) p2";
    $aryQuery[] = "            on p1.lngrevisionno = p2.lngrevisionno and p1.strproductcode = p2.strproductcode";
    $aryQuery[] = "          ) p ";
    $aryQuery[] = "          ON sd1.strProductCode = p.strProductCode ";
    $aryQuery[] = "        left join m_group mg ";
    $aryQuery[] = "          on p.lnginchargegroupcode = mg.lnggroupcode ";
    $aryQuery[] = "        left join m_user mu ";
    $aryQuery[] = "          on p.lnginchargeusercode = mu.lngusercode ";
    $aryQuery[] = "        left join m_tax mt ";
    $aryQuery[] = "          on mt.lngtaxcode = sd1.lngtaxcode ";
    $aryQuery[] = "        left join m_taxclass mtc ";
    $aryQuery[] = "          on mtc.lngtaxclasscode = sd1.lngtaxclasscode ";
    $aryQuery[] = "        LEFT JOIN m_Stocksubject ss ";
    $aryQuery[] = "          on ss.lngStocksubjectcode = sd1.lngStocksubjectcode ";
    $aryQuery[] = "        LEFT JOIN m_Stockitem si ";
    $aryQuery[] = "          on si.lngStocksubjectcode = sd1.lngStocksubjectcode ";
    $aryQuery[] = "          and si.lngStockitemcode = sd1.lngStockitemcode ";
    $aryQuery[] = "        LEFT JOIN m_deliverymethod dm ";
    $aryQuery[] = "          on dm.lngdeliverymethodcode = sd1.lngdeliverymethodcode ";
    $aryQuery[] = "        LEFT JOIN m_productunit pu ";
    $aryQuery[] = "          on pu.lngproductunitcode = sd1.lngproductunitcode";
    $aryQuery[] = "        LEFT JOIN m_Order o on sd1.lngOrderNo = o.lngOrderNo";
    $aryQuery[] = "          and o.lngrevisionno = sd1.lngorderrevisionno ";
    $aryQuery[] = "        LEFT JOIN t_purchaseorderdetail tpd ";
    $aryQuery[] = "          on tpd.lngorderno = sd1.lngorderno ";
    $aryQuery[] = "          and tpd.lngorderdetailno = sd1.lngorderdetailno ";
    $aryQuery[] = "          and tpd.lngorderrevisionno = sd1.lngorderrevisionno ";
    $aryQuery[] = "        LEFT JOIN m_purchaseorder mp ";
    $aryQuery[] = "          on mp.lngpurchaseorderno = tpd.lngpurchaseorderno ";
    $aryQuery[] = "          and mp.lngrevisionno = tpd.lngrevisionno ";

    // 発注書No_from
    if (array_key_exists("strOrderCode", $searchColumns) &&
        array_key_exists("strOrderCode", $from) && $from["strOrderCode"]!='') {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = " o.strOrderCode" .
            " >= '" . $from["strOrderCode"] . "'";
    }
    // 発注書No_to
    if (array_key_exists("strOrderCode", $searchColumns) &&
        array_key_exists("strOrderCode", $to) && $to["strOrderCode"]!='') {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = " o.strOrderCode" .
            " >= " . "'" . $to["strOrderCode"] . "'";
    }
    if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE (" : "AND (";
        $strProductCodeArray = explode(",", $searchValue["strProductCode"]);
        $count = 0;
        foreach ($strProductCodeArray as $strProductCode) {
            $count += 1;
            if ($count != 1) {
                $aryQuery[] = " OR ";
            }
            if (strpos($strProductCode, '-') !== false) {
                $aryQuery[] = "(sd1.strProductCode" .
                " between '" . explode("-", $strProductCode)[0] . "'" .
                " AND " . "'" . explode("-", $strProductCode)[1] . "')";
            } else {
                if (strpos($strProductCode, '_') !== false) {
                    $aryQuery[] = "sd1.strProductCode = '" . explode("_", $strProductCode)[0] . "'";
                    $aryQuery[] = " AND sd1.strrevisecode = '" . explode("_", $strProductCode)[1] . "'";
                } else {
                    $aryQuery[] = "sd1.strProductCode = '" . $strProductCode . "'";
                }
            }
        }
        $aryQuery[] = ")";
    }
    // 営業部署
    if (array_key_exists("lngInChargeGroupCode", $searchColumns) &&
        array_key_exists("lngInChargeGroupCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "mg.strGroupDisplayCode = '" . pg_escape_string($searchValue["lngInChargeGroupCode"]) . "'";
    }

    // 開発担当者
    if (array_key_exists("lngInChargeUserCode", $searchColumns) &&
        array_key_exists("lngInChargeUserCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "mu.strUserDisplayCode = '" . pg_escape_string($searchValue["lngInChargeUserCode"]) . "'";
    }

    // 製品名称
    if (array_key_exists("strProductName", $searchColumns) &&
        array_key_exists("strProductName", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "UPPER(p.strproductname) like UPPER('%" . pg_escape_string($searchValue["strProductName"]) . "%')";
    }

    // 仕入科目コード
    if (array_key_exists("lngStockSubjectCode", $searchColumns) &&
        array_key_exists("lngStockSubjectCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "sd1.lngStockSubjectCode = " . $searchValue["lngStockSubjectCode"] . "";
    }

    // 仕入部品コード
    if (array_key_exists("lngStockItemCode", $searchColumns) &&
        array_key_exists("lngStockItemCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";

        $aryQuery[] = "sd1.lngStockItemCode = " . explode("-", $searchValue["lngStockItemCode"])[1] . "";
    }
    // 顧客品番
    if (array_key_exists("strGoodsCode", $searchColumns) &&
        array_key_exists("strGoodsCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "p.strgoodscode = '" . pg_escape_string($searchValue["strGoodsCode"]) . "'";
    }
    // 運搬方法
    if (array_key_exists("lngDeliveryMethodCode", $searchColumns) &&
        array_key_exists("lngDeliveryMethodCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "sd1.lngDeliveryMethodCode = " . $searchValue["lngDeliveryMethodCode"] . "";
    }
    $aryQuery[] = "    ) as sd ";
    $aryQuery[] = "WHERE";
    $aryQuery[] = "  sd.lngStockNo = s.lngStockNo ";
    // 登録日_from
    if (array_key_exists("dtmInsertDate", $searchColumns) &&
        array_key_exists("dtmInsertDate", $from) && $from["dtmInsertDate"] != '') {
        $aryQuery[] = "AND s.dtmInsertDate" .
            " >= '" . $from["dtmInsertDate"] . " 00:00:00'";
    }
    // 登録日_to
    if (array_key_exists("dtmInsertDate", $searchColumns) &&
        array_key_exists("dtmInsertDate", $to) && $to["dtmInsertDate"] != '') {
        $aryQuery[] = "AND s.dtmInsertDate" .
            " <= " . "'" . $to["dtmInsertDate"] . " 23:59:59.99999'";
    }
    // 仕入日_from
    if (array_key_exists("dtmAppropriationDate", $searchColumns) &&
        array_key_exists("dtmAppropriationDate", $from) && $from["dtmAppropriationDate"] != '') {
        $aryQuery[] = "AND s.dtmAppropriationDate" .
            " >= '" . $from["dtmAppropriationDate"] . "'";
    }
    // 仕入日_to
    if (array_key_exists("dtmAppropriationDate", $searchColumns) &&
        array_key_exists("dtmAppropriationDate", $to) && $to["dtmAppropriationDate"] != '') {
        $aryQuery[] = "AND s.dtmAppropriationDate" .
            " >= " . "'" . $to["dtmAppropriationDate"] . "'";
    }
    // 製品到着日_from
    if (array_key_exists("dtmExpirationDate", $searchColumns) &&
        array_key_exists("dtmExpirationDate", $from) && $from["dtmExpirationDate"] != '') {
        $aryQuery[] = "AND s.dtmExpirationDate" .
            " >= '" . $from["dtmExpirationDate"] . "'";
    }
    // 製品到着日_to
    if (array_key_exists("dtmExpirationDate", $searchColumns) &&
        array_key_exists("dtmExpirationDate", $to) && $to["dtmExpirationDate"] != '') {
        $aryQuery[] = "AND s.dtmExpirationDate" .
            " <= " . "'" . $to["dtmExpirationDate"] . "'";
    }

    // 仕入Ｎｏ_from
    if (array_key_exists("strStockCode", $searchColumns) &&
        array_key_exists("strStockCode", $from) && $from["strStockCode"] != '') {
        $aryQuery[] = "AND s.strStockCode" .
            " >= '" . $from["strStockCode"] . "'";
    }
    // 仕入Ｎｏ_to
    if (array_key_exists("strStockCode", $searchColumns) &&
        array_key_exists("strStockCode", $to) && $to["strStockCode"] != '') {
        $aryQuery[] = "AND s.strStockCode" .
            " <= " . "'" . $to["strStockCode"] . "'";
    }
    // 納品書Ｎｏ
    if (array_key_exists("strSlipCode", $searchColumns) &&
        array_key_exists("strSlipCode", $searchValue)) {
        $aryQuery[] = " AND s.strSlipCode = '" . $searchValue["strSlipCode"] . "'";
    }

    // 入力者
    if (array_key_exists("lngInputUserCode", $searchColumns) &&
        array_key_exists("lngInputUserCode", $searchValue)) {
        $aryQuery[] = " AND input_u.strUserDisplayCode = '" . $searchValue["lngInputUserCode"] . "'";
    }

    // 仕入先
    if (array_key_exists("lngCustomerCode", $searchColumns) &&
        array_key_exists("lngCustomerCode", $searchValue)) {
        $aryQuery[] = " AND cust_c.strCompanyDisplayCode = '" . $searchValue["lngCustomerCode"] . "'";
    }

    // 状態
    if (array_key_exists("lngStockStatusCode", $searchColumns) &&
        array_key_exists("lngStockStatusCode", $searchValue)) {
        if (is_array($searchValue["lngStockStatusCode"])) {
            $searchStatus = implode(",", $searchValue["lngStockStatusCode"]);
            $aryQuery[] = " AND s.lngStockStatusCode in (" . $searchStatus . ")";
        }
    }

    // 支払条件
    if (array_key_exists("lngPayConditionCode", $searchColumns) &&
        array_key_exists("lngPayConditionCode", $searchValue)) {
        $aryQuery[] = " AND s.lngPayConditionCode = '" . $searchValue["lngPayConditionCode"] . "'";
    }
	if (!array_key_exists("admin", $optionColumns)) {
		$aryQuery[] = "  AND not exists ( ";
		$aryQuery[] = "    select";
		$aryQuery[] = "      s2.strStockCode ";
		$aryQuery[] = "    from";
		$aryQuery[] = "      ( ";
		$aryQuery[] = "        SELECT";
		$aryQuery[] = "          min(lngRevisionNo) lngRevisionNo";
		$aryQuery[] = "          , strStockCode ";
		$aryQuery[] = "        FROM";
		$aryQuery[] = "          m_Stock ";
		$aryQuery[] = "        group by";
		$aryQuery[] = "          strStockCode";
		$aryQuery[] = "      ) as s2 ";
		$aryQuery[] = "    where";
		$aryQuery[] = "      s2.strStockCode = s.strStockCode";
		$aryQuery[] = "      AND s2.lngRevisionNo < 0";
		$aryQuery[] = "  ) ";
	} else {
		$aryQuery[] = " AND s.bytInvalidFlag = FALSE ";
		$aryQuery[] = " AND s.lngRevisionNo >= 0";
	}
    $aryQuery[] = "ORDER BY";
    $aryQuery[] = " strStockCode, lngRevisionNo DESC";

    // クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);

    return $strQuery;
}

function fncGetStocksByStrStockCodeSQL($strStockCode, $lngRevisionNo)
{

	// クエリの組立て
    $aryQuery = array();
    $aryQuery[] = "SELECT";
    $aryQuery[] = "  s.lngStockNo as lngStockNo";
    $aryQuery[] = "  , s.lngRevisionNo as lngRevisionNo";
    $aryQuery[] = "  , sd.strordercode";
    $aryQuery[] = "  , to_char(s.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS') as dtmInsertDate";
    $aryQuery[] = "  , to_char(s.dtmappropriationdate, 'YYYY/MM/DD') as dtmappropriationdate";
    $aryQuery[] = "  , to_char(s.dtmexpirationdate, 'YYYY/MM/DD') as dtmexpirationdate";
    $aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
    $aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
    $aryQuery[] = "  , s.strStockCode as strStockCode";
    $aryQuery[] = "  , s.strslipcode as strslipcode";
    $aryQuery[] = "  , cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
    $aryQuery[] = "  , cust_c.strCompanyDisplayName as strCustomerDisplayName";
    $aryQuery[] = "  , s.lngStockStatusCode as lngStockStatusCode";
    $aryQuery[] = "  , rs.strStockStatusName as strStockStatusName";
    $aryQuery[] = "  , s.lngpayconditioncode as lngpayconditioncode";
    $aryQuery[] = "  , mp.strpayconditionname as strpayconditionname";
    $aryQuery[] = "  , s.strNote as strNote";
    $aryQuery[] = "  , To_char(s.curTotalPrice, '9,999,999,990.99') as curTotalPrice";
    $aryQuery[] = "  , mu.strMonetaryUnitSign as strMonetaryUnitSign";
    $aryQuery[] = "  , mu.lngmonetaryunitcode as lngmonetaryunitcode ";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  m_Stock s ";
    $aryQuery[] = "  LEFT JOIN m_User input_u ";
    $aryQuery[] = "    ON s.lngInputUserCode = input_u.lngUserCode ";
    $aryQuery[] = "  LEFT JOIN m_Company cust_c ";
    $aryQuery[] = "    ON s.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
    $aryQuery[] = "  LEFT JOIN m_StockStatus rs ";
    $aryQuery[] = "    USING (lngStockStatusCode) ";
    $aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
    $aryQuery[] = "    ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
    $aryQuery[] = "  LEFT JOIN m_paycondition mp ";
    $aryQuery[] = "    ON s.lngpayconditioncode = mp.lngpayconditioncode";
    $aryQuery[] = "  , ( ";
    $aryQuery[] = "      SELECT distinct";
    $aryQuery[] = "          on (sd1.lngStockNo) sd1.lngStockNo";
    $aryQuery[] = "        , sd1.lngStockDetailNo";
    $aryQuery[] = "        , sd1.lngRevisionNo ";
    $aryQuery[] = "        , o.strordercode";
    $aryQuery[] = "        , p.strProductCode";
    $aryQuery[] = "        , mg.strGroupDisplayCode";
    $aryQuery[] = "        , mg.strGroupDisplayName";
    $aryQuery[] = "        , mu.struserdisplaycode";
    $aryQuery[] = "        , mu.struserdisplayname";
    $aryQuery[] = "        , p.strProductName";
    $aryQuery[] = "        , p.strProductEnglishName";
    $aryQuery[] = "        , sd1.lngStockSubjectCode";
    $aryQuery[] = "        , ss.strStockSubjectName";
    $aryQuery[] = "        , sd1.lngStockItemCode";
    $aryQuery[] = "        , si.strStockItemName";
    $aryQuery[] = "        , sd1.strMoldNo";
    $aryQuery[] = "        , p.strGoodsCode";
    $aryQuery[] = "        , sd1.lngDeliveryMethodCode";
    $aryQuery[] = "        , dm.strDeliveryMethodName";
    $aryQuery[] = "        , sd1.curProductPrice";
    $aryQuery[] = "        , sd1.lngProductUnitCode";
    $aryQuery[] = "        , pu.strProductUnitName";
    $aryQuery[] = "        , sd1.lngProductQuantity";
    $aryQuery[] = "        , sd1.curSubTotalPrice";
    $aryQuery[] = "        , sd1.lngTaxClassCode";
    $aryQuery[] = "        , mtc.strTaxClassName";
    $aryQuery[] = "        , mt.curtax";
    $aryQuery[] = "        , sd1.curtaxprice";
    $aryQuery[] = "        , sd1.strNote ";
    $aryQuery[] = "      FROM";
    $aryQuery[] = "        t_StockDetail sd1 ";
    $aryQuery[] = "      inner join ( ";
    $aryQuery[] = "          select lngstockno, lngstockdetailno, max(lngrevisionno) as lngrevisionno ";
    $aryQuery[] = "          from t_StockDetail group by lngstockno, lngstockdetailno ";
    $aryQuery[] = "      ) sd_rev ";
    $aryQuery[] = "        on sd_rev.lngstockno = sd1.lngstockno ";
    $aryQuery[] = "        and sd_rev.lngstockdetailno = sd1.lngstockdetailno ";
    $aryQuery[] = "        and sd_rev.lngrevisionno = sd1.lngrevisionno ";
    $aryQuery[] = "      INNER JOIN t_purchaseorderdetail tp ";
    $aryQuery[] = "        on tp.lngorderno = sd1.lngorderno ";
    $aryQuery[] = "        and tp.lngorderdetailno = sd1.lngorderdetailno ";
    $aryQuery[] = "        and tp.lngorderrevisionno = sd1.lngorderrevisionno ";
    $aryQuery[] = "      INNER JOIN m_purchaseorder o  ";
    $aryQuery[] = "        on o.lngpurchaseorderno = tp.lngpurchaseorderno ";
    $aryQuery[] = "        and o.lngrevisionno = tp.lngrevisionno ";
    $aryQuery[] = "      INNER JOIN ( ";
    $aryQuery[] = "        select p1.*  from m_product p1  ";
    $aryQuery[] = "        inner join (select max(lngrevisionno) lngrevisionno, strproductcode from m_Product group by strProductCode) p2 ";
    $aryQuery[] = "        on p1.lngrevisionno = p2.lngrevisionno and p1.strproductcode = p2.strproductcode ";
    $aryQuery[] = "      ) p  ";
    $aryQuery[] = "        ON sd1.strProductCode = p.strProductCode  ";
    $aryQuery[] = "      INNER join m_group mg  ";
    $aryQuery[] = "        on p.lnginchargegroupcode = mg.lnggroupcode  ";
    $aryQuery[] = "      left join m_user mu ";
    $aryQuery[] = "        on p.lnginchargeusercode = mu.lngusercode ";
    $aryQuery[] = "      left join m_tax mt ";
    $aryQuery[] = "        on mt.lngtaxcode = sd1.lngtaxcode ";
    $aryQuery[] = "      left join m_taxclass mtc ";
    $aryQuery[] = "        on mtc.lngtaxclasscode = sd1.lngtaxclasscode ";
    $aryQuery[] = "      LEFT JOIN m_Stocksubject ss ";
    $aryQuery[] = "        on ss.lngStocksubjectcode = sd1.lngStocksubjectcode ";
    $aryQuery[] = "      LEFT JOIN m_Stockitem si ";
    $aryQuery[] = "        on si.lngStocksubjectcode = sd1.lngStocksubjectcode ";
    $aryQuery[] = "        and si.lngStockitemcode = sd1.lngStockitemcode ";
    $aryQuery[] = "      LEFT JOIN m_deliverymethod dm ";
    $aryQuery[] = "        on dm.lngdeliverymethodcode = sd1.lngdeliverymethodcode ";
    $aryQuery[] = "      LEFT JOIN m_productunit pu ";
    $aryQuery[] = "        on pu.lngproductunitcode = sd1.lngproductunitcode";
    $aryQuery[] = ") as sd ";
    $aryQuery[] = "WHERE";
    $aryQuery[] = "  sd.lngStockNo = s.lngStockNo ";
    $aryQuery[] = " AND s.strStockCode = '" . $strStockCode ."'";
    $aryQuery[] = " AND s.lngrevisionno <> " . $lngRevisionNo ." ";
    $aryQuery[] = " AND s.bytInvalidFlag = FALSE ";
    $aryQuery[] = "ORDER BY";
    $aryQuery[] = " strStockCode, lngRevisionNo DESC";

	// クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);

    return $strQuery;
}



/**
 * 明細データの取得
 *
 * @param [type] $lngSalesNo
 * @param [type] $lngRevisionNo
 * @param [type] $objDB
 * @return void
 */
function fncGetDetailData($lngStockNo, $lngRevisionNo, $objDB)
{
    $detailData = array();
    unset($aryQuery);
    $aryQuery[] = "SELECT sd.lngStockNo";
    $aryQuery[] = "  , sd.lngStockDetailNo";
    $aryQuery[] = "  , p.strProductCode";
    $aryQuery[] = "  , mg.strGroupDisplayCode";
    $aryQuery[] = "  , mg.strGroupDisplayName";
    $aryQuery[] = "  , mu.struserdisplaycode";
    $aryQuery[] = "  , mu.struserdisplayname";
    $aryQuery[] = "  , p.strProductName";
    $aryQuery[] = "  , p.strProductEnglishName";
    $aryQuery[] = "  , sd.lngStockSubjectCode";
    $aryQuery[] = "  , ss.strStockSubjectName";
    $aryQuery[] = "  , sd.lngStockItemCode";
    $aryQuery[] = "  , si.strStockItemName";
    $aryQuery[] = "  , sd.strMoldNo";
    $aryQuery[] = "  , p.strGoodsCode";
    $aryQuery[] = "  , sd.lngDeliveryMethodCode";
    $aryQuery[] = "  , dm.strDeliveryMethodName";
    $aryQuery[] = "  , sd.curProductPrice";
    $aryQuery[] = "  , sd.lngProductUnitCode";
    $aryQuery[] = "  , pu.strProductUnitName";
    $aryQuery[] = "  , sd.lngProductQuantity";
    $aryQuery[] = "  , sd.curSubTotalPrice";
    $aryQuery[] = "  , sd.lngTaxClassCode";
    $aryQuery[] = "  , mtc.strTaxClassName";
    $aryQuery[] = "  , mt.curtax";
    $aryQuery[] = "  , to_char(sd.curTaxPrice, '9,999,999,990.99') as curTaxPrice";
    $aryQuery[] = "  , sd.strNote as strdetailnote";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  t_StockDetail sd ";
    $aryQuery[] = "  LEFT JOIN ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      p1.* ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_product p1 ";
    $aryQuery[] = "      inner join ( ";
    $aryQuery[] = "        select";
    $aryQuery[] = "          max(lngrevisionno) lngrevisionno";
    $aryQuery[] = "          , strproductcode ";
    $aryQuery[] = "        from";
    $aryQuery[] = "          m_Product ";
    $aryQuery[] = "        group by";
    $aryQuery[] = "          strProductCode";
    $aryQuery[] = "      ) p2 ";
    $aryQuery[] = "        on p1.lngrevisionno = p2.lngrevisionno ";
    $aryQuery[] = "        and p1.strproductcode = p2.strproductcode";
    $aryQuery[] = "  ) p ";
    $aryQuery[] = "    ON sd.strProductCode = p.strProductCode ";
    $aryQuery[] = "  left join m_group mg ";
    $aryQuery[] = "    on p.lnginchargegroupcode = mg.lnggroupcode ";
    $aryQuery[] = "  left join m_user mu ";
    $aryQuery[] = "    on p.lnginchargeusercode = mu.lngusercode ";
    $aryQuery[] = "  left join m_tax mt ";
    $aryQuery[] = "    on mt.lngtaxcode = sd.lngtaxcode ";
    $aryQuery[] = "  left join m_taxclass mtc ";
    $aryQuery[] = "    on mtc.lngtaxclasscode = sd.lngtaxclasscode ";
    $aryQuery[] = "  LEFT JOIN m_Stocksubject ss ";
    $aryQuery[] = "    on ss.lngStocksubjectcode = sd.lngStocksubjectcode ";
    $aryQuery[] = "  LEFT JOIN m_Stockitem si ";
    $aryQuery[] = "    on si.lngStocksubjectcode = sd.lngStocksubjectcode ";
    $aryQuery[] = "    and si.lngStockitemcode = sd.lngStockitemcode ";
    $aryQuery[] = "  LEFT JOIN m_deliverymethod dm ";
    $aryQuery[] = "    on dm.lngdeliverymethodcode = sd.lngdeliverymethodcode ";
    $aryQuery[] = "  LEFT JOIN m_productunit pu ";
    $aryQuery[] = "    on pu.lngproductunitcode = sd.lngproductunitcode ";
    $aryQuery[] = "where";
    $aryQuery[] = "  sd.lngStockNo = " . $lngStockNo;
    $aryQuery[] = "  and sd.lngrevisionno = " . $lngRevisionNo;
    $aryQuery[] = "order by sd.lngStockDetailNo";

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
function fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $detailData)
{
    // 指定されたテーブル項目のセルを作成する
    foreach ($aryTableDetailHeaderName as $key => $value) {
        // 表示対象のカラムの場合
        if (array_key_exists($key, $displayColumns)) {
            // 項目別に表示テキストを設定
            switch ($key) {                
                // 明細行番号
                case "lngrecordno":
                    $td = $doc->createElement("td", $detailData["lngstockdetailno"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 製品コード
                case "strproductcode":
                    $td = $doc->createElement("td", $detailData["strproductcode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [営業部署表示コード] 営業部署表示名
                case "lnginchargegroupcode":
                    $textContent = "[" . $detailData["strgroupdisplaycode"] . "]" . " " . $detailData["strgroupdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [開発担当者表示コード] 開発担当者表示名
                case "lnginchargeusercode":
                    $textContent = "[" . $detailData["struserdisplaycode"] . "]" . " " . $detailData["struserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 製品マスタ.製品名称(日本語)
                case "strproductname":
                    $td = $doc->createElement("td", toUTF8($detailData["strproductname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 仕入科目
                case "lngstockitemcode":
                    $textContent = "[" . $detailData["lngstockitemcode"] . "]" . " " . $detailData["strstockitemname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 仕入部品
                case "lngstocksubjectcode":
                    $textContent = "[" . $detailData["lngstocksubjectcode"] . "]" . " " . $detailData["strstocksubjectname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // NO.
                case "strmoldno":
                    $td = $doc->createElement("td", $detailData["strmoldno"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 顧客品番
                case "strgoodscode":
                    $td = $doc->createElement("td", toUTF8($detailData["strgoodscode"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 運搬方法
                case "lngdeliverymethodcode":
                    $td = $doc->createElement("td", toUTF8($detailData["strdeliverymethodname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 単価
                case "curproductprice":
                    $td = $doc->createElement("td", toMoneyFormat($detailData["lngmonetaryunitcode"], $detailData["strmonetaryunitsign"], $detailData["curproductprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 単位
                case "lngproductunitcode":
                    $td = $doc->createElement("td", toUTF8($detailData["strproductunitname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 数量
                case "lngproductquantity":
                    $td = $doc->createElement("td", number_format($detailData["lngproductquantity"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 税抜金額
                case "cursubtotalprice":
                    $td = $doc->createElement("td", toMoneyFormat($detailData["lngmonetaryunitcode"], $detailData["strmonetaryunitsign"], $detailData["cursubtotalprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 税区分
                case "lngtaxclasscode":
                    $td = $doc->createElement("td", toUTF8($detailData["strtaxclassname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 税率
                case "curtax":
                    $td = $doc->createElement("td", $detailData["curtax"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 税額
                case "curtaxprice":
                    $td = $doc->createElement("td", toMoneyFormat($detailData["lngmonetaryunitcode"], $detailData["strmonetaryunitsign"], $detailData["curtaxprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 明細備考
                case "strdetailnote":
                    $td = $doc->createElement("td", toUTF8($detailData["strdetailnote"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
            }
        }
    }
    return $trBody;
}

/**
 * 仕入コードによりデータの状態を確認する
 *
 * @param [type] $strstockcode
 * @param [type] $objDB
 * @return void [0：未削除データ　1：削除済データ]
 */
function fncCheckData($strstockcode, $objDB) {
    $result = 0;
    unset($aryQuery);
    $aryQuery[] = "SELECT";
    $aryQuery[] = " max(lngrevisionno) lngrevisionno, bytInvalidFlag, strstockcode ";
    $aryQuery[] = "FROM m_stock ";
    $aryQuery[] = "WHERE strstockcode='" . $strstockcode . "' ";
    $aryQuery[] = "group by strstockcode, bytInvalidFlag";

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