<?
// ----------------------------------------------------------------------------
/**
 *       受注管理  検索関連関数群
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
 * 検索項目から一致する最新の受注データを取得するSQL文の作成関数
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
function fncGetMaxReceiveSQL($displayColumns, $searchColumns, $from, $to, $searchValue, $optionColumns)
{
    // 明細検索条件数
    $detailConditionCount = 0;
    // クエリの組立て
    $aryQuery = array();
    $aryQuery[] = "SELECT";
    $aryQuery[] = "  distinct r.strReceiveCode ";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  m_Receive r ";    
    $aryQuery[] = "  inner join ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      max(lngrevisionno) lngrevisionno";
    $aryQuery[] = "      , strReceiveCode ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_Receive ";
    $aryQuery[] = "    group by";
    $aryQuery[] = "      strReceiveCode";
    $aryQuery[] = "  ) r1 ";
    $aryQuery[] = "    on r.lngrevisionno = r1.lngrevisionno ";
    $aryQuery[] = "    and r.strReceiveCode = r1.strReceiveCode ";
    $aryQuery[] = "  LEFT JOIN m_User input_u ";
    $aryQuery[] = "    ON r.lngInputUserCode = input_u.lngUserCode ";
    $aryQuery[] = "  LEFT JOIN m_Company cust_c ";
    $aryQuery[] = "    ON r.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
    $aryQuery[] = "  LEFT JOIN m_ReceiveStatus rs ";
    $aryQuery[] = "    USING (lngReceiveStatusCode) ";
    $aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
    $aryQuery[] = "    ON r.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
    $aryQuery[] = "  , ( ";
    $aryQuery[] = "      SELECT rd1.lngReceiveNo";
    $aryQuery[] = "        , rd1.lngReceiveDetailNo";
    $aryQuery[] = "        , rd1.lngRevisionNo";
    $aryQuery[] = "        , p.strProductCode";
    $aryQuery[] = "        , mg.strGroupDisplayCode";
    $aryQuery[] = "        , mg.strGroupDisplayName";
    $aryQuery[] = "        , mu.struserdisplaycode";
    $aryQuery[] = "        , mu.struserdisplayname";
    $aryQuery[] = "        , p.strProductName";
    $aryQuery[] = "        , p.strProductEnglishName";
    $aryQuery[] = "        , ms.lngSalesClassCode";
    $aryQuery[] = "        , ms.strsalesclassname";
    $aryQuery[] = "        , p.strGoodsCode";
    $aryQuery[] = "        , rd1.dtmDeliveryDate";
    $aryQuery[] = "        , to_char(rd1.curProductPrice, '9,999,999,990.99') as curProductPrice";
    $aryQuery[] = "        , mp.lngProductUnitCode";
    $aryQuery[] = "        , mp.strproductunitname";
    $aryQuery[] = "        , rd1.lngProductQuantity";
    $aryQuery[] = "        , to_char(rd1.curSubTotalPrice, '9,999,999,990.99') as curSubTotalPrice";
    $aryQuery[] = "        , rd1.strNote ";
    $aryQuery[] = "      FROM";
    $aryQuery[] = "        t_ReceiveDetail rd1 ";
    $aryQuery[] = "        LEFT JOIN (";
    $aryQuery[] = "            select p1.*  from m_product p1 ";
    $aryQuery[] = "        	inner join (select max(lngRevisionNo) lngRevisionNo, strproductcode from m_Product group by strProductCode) p2";
    $aryQuery[] = "            on p1.lngRevisionNo = p2.lngRevisionNo and p1.strproductcode = p2.strproductcode ";
    $aryQuery[] = "          ) p ";
    $aryQuery[] = "          ON rd1.strProductCode = p.strProductCode ";
    $aryQuery[] = "        left join m_group mg ";
    $aryQuery[] = "          on p.lnginchargegroupcode = mg.lnggroupcode ";
    $aryQuery[] = "        left join m_user mu ";
    $aryQuery[] = "          on p.lnginchargeusercode = mu.lngusercode ";
    $aryQuery[] = "        left join m_salesclass ms ";
    $aryQuery[] = "          on ms.lngsalesclasscode = rd1.lngsalesclasscode ";
    $aryQuery[] = "        left join m_productunit mp ";
    $aryQuery[] = "          on mp.lngproductunitcode = rd1.lngproductunitcode ";
    // 製品コード_from
    if (array_key_exists("strProductCode", $searchColumns) &&
        array_key_exists("strProductCode", $from) && $from["strProductCode"] != '') {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = " rd1.strProductCode" .
        " >= '" . pg_escape_string($from["strProductCode"]) . "'";
    }
    // 製品コード_to
    if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $to) && $to["strProductCode"] != '') {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = " rd1.strProductCode" .
    " <= " . "'" . pg_escape_string($to["strProductCode"]) . "'";
}
    // 製品名称
    if (array_key_exists("strProductName", $searchColumns) &&
        array_key_exists("strProductName", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "UPPER(p.strproductname) like UPPER('%" . pg_escape_string($searchValue["strProductName"]) . "%')";
    }
    // 製品名称(英語)
    if (array_key_exists("strProductEnglishName", $searchColumns) &&
        array_key_exists("strProductEnglishName", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "UPPER(p.strproductenglishname) like UPPER('%" . pg_escape_string($searchValue["strProductEnglishName"]) . "%')";
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

    // 納期_from
    if (array_key_exists("dtmDeliveryDate", $searchColumns) &&
        array_key_exists("dtmDeliveryDate", $from) && $from["dtmDeliveryDate"] != '') {
        $aryQuery[] = "AND rd1.dtmdeliverydate" .
            " >= '" . $from["dtmDeliveryDate"] . "'";
    }
    // 納期_to
    if (array_key_exists("dtmDeliveryDate", $searchColumns) &&
        array_key_exists("dtmDeliveryDate", $to) && $to["dtmDeliveryDate"] != '') {
        $aryQuery[] = "AND rd1.dtmdeliverydate" .
            " <= " . "'" . $to["dtmDeliveryDate"] . "'";
    }
    $aryQuery[] = "    ) as rd ";
    $aryQuery[] = "WHERE";
    $aryQuery[] = " rd.lngReceiveNo = r.lngReceiveNo ";
    $aryQuery[] = " AND rd.lngRevisionNo = r.lngRevisionNo ";
    // 登録日_from
    if (array_key_exists("dtmInsertDate", $searchColumns) &&
        array_key_exists("dtmInsertDate", $from) && $from["dtmInsertDate"] != '') {
        $aryQuery[] = "AND r.dtmInsertDate" .
            " >= '" . $from["dtmInsertDate"] . " 00:00:00'";
    }
    // 登録日_to
    if (array_key_exists("dtmInsertDate", $searchColumns) &&
        array_key_exists("dtmInsertDate", $to) && $to["dtmInsertDate"] != '') {
        $aryQuery[] = "AND r.dtmInsertDate" .
            " <= " . "'" . $to["dtmInsertDate"] . " 23:59:59.99999'";
    }

    // 顧客受注番号_from
    if (array_key_exists("strCustomerReceiveCode", $searchColumns) &&
        array_key_exists("strCustomerReceiveCode", $from) && $from["strCustomerReceiveCode"] != '') {
        $aryQuery[] = "AND r.strCustomerReceiveCode" .
            " >= '" . $from["strCustomerReceiveCode"] . "'";
    }

    // 顧客受注番号_to
    if (array_key_exists("strCustomerReceiveCode", $searchColumns) &&
        array_key_exists("strCustomerReceiveCode", $to) && $to["strCustomerReceiveCode"] != '') {
        $aryQuery[] = "AND r.strCustomerReceiveCode" .
            " <= " . "'" . $to["strCustomerReceiveCode"] . "'";
    }

    // 受注Ｎｏ_from
    if (array_key_exists("strReceiveCode", $searchColumns) &&
        array_key_exists("strReceiveCode", $from) && $from["strReceiveCode"] != '') {
        $fromstrReceiveCode = strpos($from["strReceiveCode"], "-") ? preg_replace(strrchr($from["strReceiveCode"], "-"), "", $from["strReceiveCode"]) : $from["strReceiveCode"];
        $aryQuery[] = "AND r.strReceiveCode" .
            " >= '" . $fromstrReceiveCode . "'";
    }

    // 受注Ｎｏ_to
    if (array_key_exists("strReceiveCode", $searchColumns) &&
        array_key_exists("strReceiveCode", $to) && $to["strReceiveCode"] != '') {
        $tostrReceiveCode = strpos($to["strReceiveCode"], "-") ? preg_replace(strrchr($to["strReceiveCode"], "-"), "", $to["strReceiveCode"]) : $to["strReceiveCode"];

        $aryQuery[] = "AND r.strReceiveCode" .
            " <= " . "'" . $tostrReceiveCode . "'";
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
    if (array_key_exists("lngReceiveStatusCode", $searchColumns) &&
        array_key_exists("lngReceiveStatusCode", $searchValue)) {
        if (is_array($searchValue["lngReceiveStatusCode"])) {
            $searchStatus = implode(",", $searchValue["lngReceiveStatusCode"]);
            $aryQuery[] = " AND r.lngReceiveStatusCode in (" . $searchStatus . ")";
        }
    }
    if (!array_key_exists("admin", $optionColumns)) {
        $aryQuery[] = "  AND r.strReceiveCode not in ( ";
        $aryQuery[] = "    select";
        $aryQuery[] = "      r1.strReceiveCode ";
        $aryQuery[] = "    from";
        $aryQuery[] = "      ( ";
        $aryQuery[] = "        SELECT";
        $aryQuery[] = "          min(lngRevisionNo) lngRevisionNo";
        $aryQuery[] = "          , strReceiveCode ";
        $aryQuery[] = "        FROM";
        $aryQuery[] = "          m_Receive ";
        $aryQuery[] = "        group by";
        $aryQuery[] = "          strReceiveCode";
        $aryQuery[] = "      ) as r1 ";
        $aryQuery[] = "    where";
        $aryQuery[] = "      r1.lngRevisionNo < 0";
        $aryQuery[] = "  ) ";
    } else {
        $aryQuery[] = " AND r.bytInvalidFlag = FALSE ";
        $aryQuery[] = " AND r.lngRevisionNo >= 0";
    }

    // クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);
    
    return $strQuery;
}

function fncGetReceivesByStrReceiveCodeSQL($subStrQuery)
{
    // 明細検索条件数
    $detailConditionCount = 0;
    // クエリの組立て
    $aryQuery = array();
    $aryQuery[] = "SELECT";
    $aryQuery[] = "  r.lngReceiveNo as lngReceiveNo";
    $aryQuery[] = "  , r.lngRevisionNo as lngRevisionNo";
    $aryQuery[] = "  , rd.lngReceiveDetailNo";
    $aryQuery[] = "  , rd.strProductCode";
    $aryQuery[] = "  , rd.strGroupDisplayCode";
    $aryQuery[] = "  , rd.strGroupDisplayName";
    $aryQuery[] = "  , rd.strUserDisplayCode";
    $aryQuery[] = "  , rd.strUserDisplayName";
    $aryQuery[] = "  , rd.strProductName";
    $aryQuery[] = "  , rd.strProductEnglishName";
    $aryQuery[] = "  , rd.lngSalesClassCode";
    $aryQuery[] = "  , rd.strsalesclassname";
    $aryQuery[] = "  , rd.strGoodsCode";
    $aryQuery[] = "  , rd.curProductPrice";
    $aryQuery[] = "  , rd.lngProductUnitCode";
    $aryQuery[] = "  , rd.strproductunitname";
    $aryQuery[] = "  , to_char(rd.lngProductQuantity, '9,999,999,990') as lngProductQuantity";
    $aryQuery[] = "  , rd.curSubTotalPrice";
    $aryQuery[] = "  , rd.strNote as strDetailNote";
    $aryQuery[] = "  , to_char(r.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS') as dtmInsertDate";
    $aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
    $aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
    $aryQuery[] = "  , r.strCustomerReceiveCode as strCustomerReceiveCode";
    $aryQuery[] = "  , r.strReceiveCode";
    $aryQuery[] = "  , cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
    $aryQuery[] = "  , cust_c.strCompanyDisplayName as strCustomerDisplayName";
    $aryQuery[] = "  , to_char(rd.dtmDeliveryDate, 'YYYY/MM/DD') as dtmDeliveryDate";
    $aryQuery[] = "  , r.lngReceiveStatusCode as lngReceiveStatusCode";
    $aryQuery[] = "  , rs.strReceiveStatusName as strReceiveStatusName";
    $aryQuery[] = "  , mu.strMonetaryUnitSign as strMonetaryUnitSign ";
    $aryQuery[] = "  , mu.lngmonetaryunitcode as lngmonetaryunitcode ";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  m_Receive r ";
    $aryQuery[] = "  LEFT JOIN m_User input_u ";
    $aryQuery[] = "    ON r.lngInputUserCode = input_u.lngUserCode ";
    $aryQuery[] = "  LEFT JOIN m_Company cust_c ";
    $aryQuery[] = "    ON r.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
    $aryQuery[] = "  LEFT JOIN m_ReceiveStatus rs ";
    $aryQuery[] = "    USING (lngReceiveStatusCode) ";
    $aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
    $aryQuery[] = "    ON r.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
    $aryQuery[] = "  , ( ";
    $aryQuery[] = "      SELECT rd1.lngReceiveNo";
    $aryQuery[] = "        , rd1.lngReceiveDetailNo";
    $aryQuery[] = "        , rd1.lngRevisionNo";
    $aryQuery[] = "        , p.strProductCode";
    $aryQuery[] = "        , mg.strGroupDisplayCode";
    $aryQuery[] = "        , mg.strGroupDisplayName";
    $aryQuery[] = "        , mu.struserdisplaycode";
    $aryQuery[] = "        , mu.struserdisplayname";
    $aryQuery[] = "        , p.strProductName";
    $aryQuery[] = "        , p.strProductEnglishName";
    $aryQuery[] = "        , ms.lngSalesClassCode";
    $aryQuery[] = "        , ms.strsalesclassname";
    $aryQuery[] = "        , p.strGoodsCode";
    $aryQuery[] = "        , rd1.dtmDeliveryDate";
    $aryQuery[] = "        , to_char(rd1.curProductPrice, '9,999,999,990.99') as curProductPrice";
    $aryQuery[] = "        , mp.lngProductUnitCode";
    $aryQuery[] = "        , mp.strproductunitname";
    $aryQuery[] = "        , rd1.lngProductQuantity";
    $aryQuery[] = "        , to_char(rd1.curSubTotalPrice, '9,999,999,990.99') as curSubTotalPrice";
    $aryQuery[] = "        , rd1.strNote ";
    $aryQuery[] = "      FROM";
    $aryQuery[] = "        t_ReceiveDetail rd1 ";
    $aryQuery[] = "        LEFT JOIN (";
    $aryQuery[] = "            select p1.*  from m_product p1 ";
    $aryQuery[] = "        	inner join (select max(lngRevisionNo) lngRevisionNo, strproductcode from m_Product group by strProductCode) p2";
    $aryQuery[] = "            on p1.lngRevisionNo = p2.lngRevisionNo and p1.strproductcode = p2.strproductcode ";
    $aryQuery[] = "          ) p ";
    $aryQuery[] = "          ON rd1.strProductCode = p.strProductCode ";
    $aryQuery[] = "        left join m_group mg ";
    $aryQuery[] = "          on p.lnginchargegroupcode = mg.lnggroupcode ";
    $aryQuery[] = "        left join m_user mu ";
    $aryQuery[] = "          on p.lnginchargeusercode = mu.lngusercode ";
    $aryQuery[] = "        left join m_salesclass ms ";
    $aryQuery[] = "          on ms.lngsalesclasscode = rd1.lngsalesclasscode ";
    $aryQuery[] = "        left join m_productunit mp ";
    $aryQuery[] = "          on mp.lngproductunitcode = rd1.lngproductunitcode ";
    $aryQuery[] = "    ) as rd ";
    $aryQuery[] = "WHERE";
    $aryQuery[] = " rd.lngReceiveNo = r.lngReceiveNo ";
    $aryQuery[] = " AND rd.lngRevisionNo = r.lngRevisionNo ";
    $aryQuery[] = " AND r.strReceiveCode in (" . $subStrQuery . ")";
    $aryQuery[] = " AND r.bytInvalidFlag = FALSE ";
    $aryQuery[] = " AND r.lngRevisionNo >= 0";

    $aryQuery[] = "ORDER BY";
    $aryQuery[] = " r.strReceiveCode, rd.lngReceiveDetailNo, r.lngRevisionNo DESC";

    // クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);

    return $strQuery;
}
