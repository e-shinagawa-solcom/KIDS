<?
// ----------------------------------------------------------------------------
/**
 *       売上管理  検索関連関数群
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
 * 検索項目から一致する最新の売上データを取得するSQL文の作成関数
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
function fncGetMaxSalesSQL($displayColumns, $searchColumns, $from, $to, $searchValue, $optionColumns)
{
    // 明細検索条件数
    $detailConditionCount = 0;

    // クエリの組立て
    $aryQuery = array();
    $aryQuery[] = "SELECT distinct";
    $aryQuery[] = "  s.lngSalesNo as lngSalesNo";
    $aryQuery[] = "  , s.lngSalesNo as lngpkno";
    $aryQuery[] = "  , s.lngRevisionNo as lngRevisionNo";
    $aryQuery[] = "  , to_char(s.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS') as dtmInsertDate";
    $aryQuery[] = "  , to_char(s.dtmappropriationdate, 'YYYY/MM/DD') as dtmappropriationdate";
    $aryQuery[] = "  , s.strSalesCode as strSalesCode";
    $aryQuery[] = "  , sd.strCustomerReceiveCode as strCustomerReceiveCode";
    $aryQuery[] = "  , s.strSlipCode as strSlipCode";
    $aryQuery[] = "  , s.lngInputUserCode as lngInputUserCode";
    $aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
    $aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
    $aryQuery[] = "  , s.lngCustomerCompanyCode";
    $aryQuery[] = "  , cust_c.strCompanyDisplayCode as strcompanydisplaycode";
    $aryQuery[] = "  , cust_c.strCompanyDisplayName as strcompanydisplayname";
    $aryQuery[] = "  , s.lngSalesStatusCode as lngSalesStatusCode";
    $aryQuery[] = "  , ss.strSalesStatusName as strSalesStatusName";
    $aryQuery[] = "  , s.strNote as strNote";
    $aryQuery[] = "  , To_char(s.curTotalPrice, '9,999,999,990.99') as curTotalPrice";
    $aryQuery[] = "  , mu.strMonetaryUnitSign as strMonetaryUnitSign";
    $aryQuery[] = "  , s.lngMonetaryUnitCode as lngMonetaryUnitCode ";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  m_Sales s ";
    $aryQuery[] = "  inner join ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      max(lngrevisionno) lngrevisionno";
    $aryQuery[] = "      , strSalesCode ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_Sales ";
    $aryQuery[] = "    group by";
    $aryQuery[] = "      strSalesCode";
    $aryQuery[] = "  ) s1 ";
    $aryQuery[] = "    on s.lngrevisionno = s1.lngrevisionno ";
    $aryQuery[] = "    and s.strSalesCode = s1.strSalesCode ";
    $aryQuery[] = "  LEFT JOIN m_User input_u ";
    $aryQuery[] = "    ON s.lngInputUserCode = input_u.lngUserCode ";
    $aryQuery[] = "  LEFT JOIN m_Company cust_c ";
    $aryQuery[] = "    ON s.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
    $aryQuery[] = "  LEFT JOIN m_SalesStatus ss ";
    $aryQuery[] = "    USING (lngSalesStatusCode) ";
    $aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
    $aryQuery[] = "    ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
    $aryQuery[] = "  , ( ";
    $aryQuery[] = "      SELECT distinct";
    $aryQuery[] = "          on (sd1.lngSalesNo) sd1.lngSalesNo";
    $aryQuery[] = "        , sd1.lngSalesDetailNo";
    $aryQuery[] = "        , sd1.lngRevisionNo";
    $aryQuery[] = "        , p.strProductCode";
    $aryQuery[] = "        , mg.strGroupDisplayCode";
    $aryQuery[] = "        , mg.strGroupDisplayName";
    $aryQuery[] = "        , mu.struserdisplaycode";
    $aryQuery[] = "        , mu.struserdisplayname";
    $aryQuery[] = "        , p.strProductName";
    $aryQuery[] = "        , p.strProductEnglishName";
    $aryQuery[] = "        , sd1.lngSalesClassCode";
    $aryQuery[] = "        , ms.strSalesClassName";
    $aryQuery[] = "        , p.strGoodsCode";
    $aryQuery[] = "        , sd1.curProductPrice";
    $aryQuery[] = "        , sd1.lngProductUnitCode";
    $aryQuery[] = "        , mp.strproductunitname";
    $aryQuery[] = "        , sd1.lngProductQuantity";
    $aryQuery[] = "        , sd1.curSubTotalPrice";
    $aryQuery[] = "        , sd1.lngTaxClassCode";
    $aryQuery[] = "        , mtc.strtaxclassname";
    $aryQuery[] = "        , mt.curtax";
    $aryQuery[] = "        , sd1.curtaxprice";
    $aryQuery[] = "        , sd1.strNote ";
    $aryQuery[] = "        , r.strCustomerReceiveCode ";
    $aryQuery[] = "      FROM";
    $aryQuery[] = "        t_SalesDetail sd1 ";
    $aryQuery[] = "        LEFT JOIN (";
    $aryQuery[] = "            select p1.*  from m_product p1 ";
    $aryQuery[] = "        	inner join (select max(lngRevisionNo) lngRevisionNo, strproductcode, strrevisecode from m_Product group by strProductCode, strrevisecode) p2";
    $aryQuery[] = "            on p1.lngRevisionNo = p2.lngRevisionNo and p1.strproductcode = p2.strproductcode and p1.strrevisecode = p2.strrevisecode";
    $aryQuery[] = "          ) p ";
    $aryQuery[] = "          ON sd1.strProductCode = p.strProductCode ";
    $aryQuery[] = "          AND sd1.strrevisecode = p.strrevisecode ";
    $aryQuery[] = "        left join m_group mg ";
    $aryQuery[] = "          on p.lnginchargegroupcode = mg.lnggroupcode ";
    $aryQuery[] = "        left join m_user mu ";
    $aryQuery[] = "          on p.lnginchargeusercode = mu.lngusercode ";
    $aryQuery[] = "        left join m_tax mt ";
    $aryQuery[] = "          on mt.lngtaxcode = sd1.lngtaxcode ";
    $aryQuery[] = "        left join m_taxclass mtc ";
    $aryQuery[] = "          on mtc.lngtaxclasscode = sd1.lngtaxclasscode ";
    $aryQuery[] = "        left join m_salesclass ms ";
    $aryQuery[] = "          on ms.lngsalesclasscode = sd1.lngsalesclasscode";
    $aryQuery[] = "        left join m_productunit mp ";
    $aryQuery[] = "          on mp.lngproductunitcode = sd1.lngproductunitcode ";
    $aryQuery[] = "        left join m_Receive r ";
    $aryQuery[] = "          on sd1.lngreceiveno = r.lngreceiveno ";
    $aryQuery[] = "          and sd1.lngreceiverevisionno = r.lngrevisionno ";

    // 製品コード
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
                $aryQuery[] = "(p.strProductCode" .
                " between '" . explode("-", $strProductCode)[0] . "'" .
                " AND " . "'" . explode("-", $strProductCode)[1] . "')";
            } else {
                if (strpos($strProductCode, '_') !== false) {
                    $aryQuery[] = "p.strProductCode = '" . explode("_", $strProductCode)[0] . "'";
                    $aryQuery[] = " AND p.strrevisecode = '" . explode("_", $strProductCode)[1] . "'";
                } else {
                    $aryQuery[] = "p.strProductCode = '" . $strProductCode . "'";
                }
            }

        }
        $aryQuery[] = ")";
    }

    // 製品名称
    if (array_key_exists("strProductName", $searchColumns) &&
        array_key_exists("strProductName", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "UPPER(p.strproductname) like UPPER('%" . pg_escape_string($searchValue["strProductName"]) . "%')";
        $aryQuery[] = " OR ";
        $aryQuery[] = "UPPER(p.strproductenglishname) like UPPER('%" . pg_escape_string($searchValue["strProductName"]) . "%')";

    }

    // 顧客品番
    if (array_key_exists("strGoodsCode", $searchColumns) &&
        array_key_exists("strGoodsCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "p.strgoodscode = '" . pg_escape_string($searchValue["strGoodsCode"]) . "'";
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

    // 売上区分
    if (array_key_exists("lngSalesClassCode", $searchColumns) &&
        array_key_exists("lngSalesClassCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "rd1.lngSalesClassCode = '" . pg_escape_string($searchValue["lngSalesClassCode"]) . "'";
    }

    $aryQuery[] = "    ) as sd ";
    $aryQuery[] = "WHERE";
    $aryQuery[] = " sd.lngSalesNo = s.lngSalesNo ";

    // 登録日
    if (array_key_exists("dtmInsertDate", $searchColumns) &&
        array_key_exists("dtmInsertDate", $from) &&
        array_key_exists("dtmInsertDate", $to)) {
        if ($from["dtmInsertDate"] != '') {
            $aryQuery[] = "AND s.dtmInsertDate" .
                " >= '" . $from["dtmInsertDate"] . " 00:00:00'";
        }

        if ($to["dtmInsertDate"] != '') {
            $aryQuery[] = "AND s.dtmInsertDate" .
                " <= '" . $to["dtmInsertDate"] . " 00:00:00'";
        }
    }

    // 請求日
    if (array_key_exists("dtmAppropriationDate", $searchColumns) &&
        array_key_exists("dtmAppropriationDate", $from) &&
        array_key_exists("dtmAppropriationDate", $to)) {
        if ($from["dtmAppropriationDate"] != '') {
            $aryQuery[] = "AND s.dtmAppropriationDate" .
                " >= '" . $from["dtmAppropriationDate"] . " 00:00:00'";
        }
        if ($to["dtmAppropriationDate"] != '') {
            $aryQuery[] = "AND s.dtmAppropriationDate" .
                " <= " . "'" . $to["dtmAppropriationDate"] . " 23:59:59.99999'";
        }
    }
    // 売上No.
    if (array_key_exists("strSalesCode", $searchColumns) &&
        array_key_exists("strSalesCode", $searchValue)) {

        $detailConditionCount += 1;
        $aryQuery[] = " AND (";
        $strSalesCodeArray = explode(",", $searchValue["strSalesCode"]);
        $count = 0;
        foreach ($strSalesCodeArray as $strSalesCode) {
            $count += 1;
            if ($count != 1) {
                $aryQuery[] = " OR ";
            }
            if (strpos($strSalesCode, '-') !== false) {
                $aryQuery[] = "(s.strSalesCode" .
                " between '" . explode("-", $strSalesCode)[0] . "'" .
                " AND " . "'" . explode("-", $strSalesCode)[1] . "')";
            } else {
                $aryQuery[] = "s.strSalesCode = '" . $strSalesCode . "'";
            }

        }
        $aryQuery[] = ")";
    }
    // 顧客受注番号
    if (array_key_exists("strCustomerReceiveCode", $searchColumns) &&
        array_key_exists("strCustomerReceiveCode", $searchValue)) {

        $detailConditionCount += 1;
        $aryQuery[] = " AND (";
        $strCustomerReceiveCodeArray = explode(",", $searchValue["strCustomerReceiveCode"]);
        $count = 0;
        foreach ($strCustomerReceiveCodeArray as $strCustomerReceiveCode) {
            $count += 1;
            if ($count != 1) {
                $aryQuery[] = " OR ";
            }
            if (strpos($strCustomerReceiveCode, '-') !== false) {
                $aryQuery[] = "(s.strCustomerReceiveCode" .
                " between '" . explode("-", $strCustomerReceiveCode)[0] . "'" .
                " AND " . "'" . explode("-", $strCustomerReceiveCode)[1] . "')";
            } else {
                $aryQuery[] = "s.strCustomerReceiveCode = '" . $strCustomerReceiveCode . "'";
            }

        }
        $aryQuery[] = ")";
    }

    // 納品書NO.
    if (array_key_exists("strSlipCode", $searchColumns) &&
        array_key_exists("strSlipCode", $searchValue)) {

        $detailConditionCount += 1;
        $aryQuery[] = " AND (";
        $strSlipCodeArray = explode(",", $searchValue["strSlipCode"]);
        $count = 0;
        foreach ($strSlipCodeArray as $strSlipCode) {
            $count += 1;
            if ($count != 1) {
                $aryQuery[] = " OR ";
            }
            if (strpos($strSalesCode, '-') !== false) {
                $aryQuery[] = "(s.strSlipCode" .
                " between '" . explode("-", $strSlipCode)[0] . "'" .
                " AND " . "'" . explode("-", $strSlipCode)[1] . "')";
            } else {
                $aryQuery[] = "s.strSlipCode = '" . $strSlipCode . "'";
            }
        }
        $aryQuery[] = ")";
    }

    // 入力者
    if (array_key_exists("lngInputUserCode", $searchColumns) &&
        array_key_exists("lngInputUserCode", $searchValue)) {
        $aryQuery[] = " AND input_u.strUserDisplayCode = '" . $searchValue["lngInputUserCode"] . "'";
    }

    // 顧客
    if (array_key_exists("lngCustomerCompanyCode", $searchColumns) &&
        array_key_exists("lngCustomerCompanyCode", $searchValue)) {
        $aryQuery[] = " AND cust_c.strCompanyDisplayCode = '" . $searchValue["lngCustomerCompanyCode"] . "'";
    }

    // 状態
    if (array_key_exists("lngSalesStatusCode", $searchColumns) &&
        array_key_exists("lngSalesStatusCode", $searchValue)) {
        if (is_array($searchValue["lngSalesStatusCode"])) {
            $searchStatus = implode(",", $searchValue["lngSalesStatusCode"]);
            $aryQuery[] = " AND s.lngSalesStatusCode in (" . $searchStatus . ")";
        }
    }

    if (!array_key_exists("admin", $optionColumns)) {
        $aryQuery[] = "  AND not exists ( ";
        $aryQuery[] = "    select";
        $aryQuery[] = "      s1.strSalesCode ";
        $aryQuery[] = "    from";
        $aryQuery[] = "      ( ";
        $aryQuery[] = "        SELECT";
        $aryQuery[] = "          min(lngRevisionNo) lngRevisionNo";
        $aryQuery[] = "          , strSalesCode ";
        $aryQuery[] = "        FROM";
        $aryQuery[] = "          m_sales ";
        $aryQuery[] = "        group by";
        $aryQuery[] = "          strSalesCode";
        $aryQuery[] = "      ) as s1 ";
        $aryQuery[] = "    where";
        $aryQuery[] = "      s1.strSalesCode = s.strSalesCode";
        $aryQuery[] = "      AND s1.lngRevisionNo < 0";
        $aryQuery[] = "  ) ";
    } else {
        $aryQuery[] = "  AND s.bytInvalidFlag = FALSE ";
        $aryQuery[] = "  AND s.lngRevisionNo >= 0 ";
    }

    // クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);

    return $strQuery;
}

