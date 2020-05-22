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
    // $aryQuery[] = "  distinct ";
    $aryQuery[] = "  r.lngReceiveNo as lngReceiveNo";
    $aryQuery[] = "  , r.lngReceiveNo as lngpkno";
    $aryQuery[] = "  , r.lngRevisionNo as lngRevisionNo";
    $aryQuery[] = "  , rd.lngReceiveDetailNo";
    $aryQuery[] = "  , rd.lngReceiveDetailNo as lngdetailno";
    $aryQuery[] = "  , rd.strProductCode as strProductCode";
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
    $aryQuery[] = "  , rd.lngEstimateNo";
    $aryQuery[] = "  , rd.estimateRevisionNo";
    $aryQuery[] = "  , to_char(rd.lngProductQuantity, '9,999,999,990') as lngProductQuantity";
    $aryQuery[] = "  , rd.curSubTotalPrice";
    $aryQuery[] = "  , rd.strNote as strDetailNote";
    $aryQuery[] = "  , to_char(r.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS') as dtmInsertDate";
    $aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
    $aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
    $aryQuery[] = "  , r.strCustomerReceiveCode as strCustomerReceiveCode";
    $aryQuery[] = "  , r.strReceiveCode";
    $aryQuery[] = "  , r.strReceiveCode as strCode";
    $aryQuery[] = "  , cust_c.strCompanyDisplayCode as strcompanydisplaycode";
    $aryQuery[] = "  , cust_c.strCompanyDisplayName as strcompanydisplayname";
    $aryQuery[] = "  , to_char(rd.dtmDeliveryDate, 'YYYY/MM/DD') as dtmDeliveryDate";
    $aryQuery[] = "  , r.lngReceiveStatusCode as lngReceiveStatusCode";
    $aryQuery[] = "  , rs.strReceiveStatusName as strReceiveStatusName";
    $aryQuery[] = "  , mu.strMonetaryUnitSign as strMonetaryUnitSign";
    $aryQuery[] = "  , mu.lngmonetaryunitcode as lngmonetaryunitcode ";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  m_Receive r ";
    $aryQuery[] = "  inner join ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      max(lngrevisionno) lngrevisionno";
    $aryQuery[] = "      , lngReceiveNo ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_Receive ";
    $aryQuery[] = "    group by";
    $aryQuery[] = "      lngReceiveNo";
    $aryQuery[] = "  ) r1 ";
    $aryQuery[] = "    on r.lngrevisionno = r1.lngrevisionno ";
    $aryQuery[] = "    and r.lngReceiveNo = r1.lngReceiveNo ";
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
    $aryQuery[] = "        , rd1.lngEstimateNo";
    $aryQuery[] = "        , p.strProductCode || '_' || p.strReviseCode as strProductCode";
    $aryQuery[] = "        , p.lngRevisionNo as estimateRevisionNo";
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
    $aryQuery[] = "        , rd1.curProductPrice";
    $aryQuery[] = "        , mp.lngProductUnitCode";
    $aryQuery[] = "        , mp.strproductunitname";
    $aryQuery[] = "        , rd1.lngProductQuantity";
    $aryQuery[] = "        , rd1.curSubTotalPrice";
    $aryQuery[] = "        , rd1.strNote ";
    $aryQuery[] = "      FROM";
    $aryQuery[] = "        t_ReceiveDetail rd1 ";
    $aryQuery[] = "        LEFT JOIN (";
    $aryQuery[] = "            select p1.*  from m_product p1 ";
    $aryQuery[] = "        	inner join (select max(p2.lngRevisionNo) lngRevisionNo, p2.strproductcode, p2.strrevisecode from m_Product p2 where p2.bytinvalidflag = false ";
    $aryQuery[] = "        	and not exists (select strproductcode from m_product p3 where p3.lngRevisionNo < 0 and p3.strproductcode = p2.strproductcode) group by p2.strProductCode, p2.strrevisecode) p4";
    $aryQuery[] = "            on p1.lngRevisionNo = p4.lngRevisionNo and p1.strproductcode = p4.strproductcode  and p1.strrevisecode = p4.strrevisecode ";
    $aryQuery[] = "          ) p ";
    $aryQuery[] = "          ON rd1.strProductCode = p.strProductCode ";
    $aryQuery[] = "          and rd1.strrevisecode = p.strrevisecode ";
    $aryQuery[] = "        left join m_group mg ";
    $aryQuery[] = "          on p.lnginchargegroupcode = mg.lnggroupcode ";
    $aryQuery[] = "        left join m_user mu ";
    $aryQuery[] = "          on p.lnginchargeusercode = mu.lngusercode ";
    $aryQuery[] = "        left join m_salesclass ms ";
    $aryQuery[] = "          on ms.lngsalesclasscode = rd1.lngsalesclasscode ";
    $aryQuery[] = "        left join m_productunit mp ";
    $aryQuery[] = "          on mp.lngproductunitcode = rd1.lngproductunitcode ";

    // 製品コード
    if (array_key_exists("strProductCode", $searchColumns) &&
        array_key_exists("strProductCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $strProductCodeArray = explode(",", $searchValue["strProductCode"]);
        $aryQuery[] = " (";
        $count = 0;
        foreach ($strProductCodeArray as $strProductCode) {
            $count += 1;
            if ($count != 1) {
                $aryQuery[] = " OR ";
            }
            if (strpos($strProductCode, '-') !== false) {
                $aryQuery[] = "(rd1.strProductCode" .
                " between '" . explode("-", $strProductCode)[0] . "'" .
                " AND " . "'" . explode("-", $strProductCode)[1] . "')";
            } else {
                if (strpos($strProductCode, '_') !== false) {
                    $aryQuery[] = "rd1.strProductCode = '" . explode("_", $strProductCode)[0] . "'";
                    $aryQuery[] = " AND rd1.strrevisecode = '" . explode("_", $strProductCode)[1] . "'";
                } else {
                    $aryQuery[] = "rd1.strProductCode = '" . $strProductCode . "'";
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
        // $aryQuery[] = "UPPER(p.strproductname) like UPPER('%" . pg_escape_string($searchValue["strProductName"]) . "%')";
        $aryQuery[] = "sf_translate_case(p.strproductname) like '%' || sf_translate_case('".pg_escape_string($searchValue["strProductName"])."') || '%'";

    }
    // 製品名称(英語)
    if (array_key_exists("strProductEnglishName", $searchColumns) &&
        array_key_exists("strProductEnglishName", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        // $aryQuery[] = "UPPER(p.strproductenglishname) like UPPER('%" . pg_escape_string($searchValue["strProductEnglishName"]) . "%')";
        $aryQuery[] = "sf_translate_case(p.strproductenglishname) like '%' || sf_translate_case('".pg_escape_string($searchValue["strProductEnglishName"])."') || '%'";
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
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "rd1.dtmdeliverydate" .
            " >= '" . $from["dtmDeliveryDate"] . "'";
    }
    // 納期_to
    if (array_key_exists("dtmDeliveryDate", $searchColumns) &&
        array_key_exists("dtmDeliveryDate", $to) && $to["dtmDeliveryDate"] != '') {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "rd1.dtmdeliverydate" .
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
        $aryQuery[] = "  AND not exists ( ";
        $aryQuery[] = "    select";
        $aryQuery[] = "      lngReceiveNo ";
        $aryQuery[] = "    FROM";
        $aryQuery[] = "      m_Receive r ";
        $aryQuery[] = "    where";
        $aryQuery[] = "      r.lngRevisionNo < 0 ";
        $aryQuery[] = "      and r1.lngReceiveNo = r.lngReceiveNo";
        $aryQuery[] = "  ) ";
    } else {
        $aryQuery[] = " AND r.bytInvalidFlag = FALSE ";
        $aryQuery[] = " AND r.lngRevisionNo >= 0";
    }
    $aryQuery[] = " ORDER BY r.dtmInsertDate DESC";

    // クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);

    return $strQuery;
}

function fncGetReceivesByStrReceiveCodeSQL($strReceiveCode, $lngReceiveDetailNo, $lngRevisionNo)
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
    $aryQuery[] = "        	inner join (select max(lngRevisionNo) lngRevisionNo, strproductcode, strrevisecode from m_Product group by strProductCode, strrevisecode) p2";
    $aryQuery[] = "            on p1.lngRevisionNo = p2.lngRevisionNo and p1.strproductcode = p2.strproductcode and p1.strrevisecode = p2.strrevisecode";
    $aryQuery[] = "          ) p ";
    $aryQuery[] = "          ON rd1.strProductCode = p.strProductCode ";
    $aryQuery[] = "          AND rd1.strrevisecode = p.strrevisecode ";
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
    $aryQuery[] = " AND r.strReceiveCode = '" . $strReceiveCode . "'";
    $aryQuery[] = " AND rd.lngReceiveDetailNo = '" . $lngReceiveDetailNo . "'";
    $aryQuery[] = " AND r.bytInvalidFlag = FALSE ";
    $aryQuery[] = " AND r.lngRevisionNo <> " . $lngRevisionNo . "";

    $aryQuery[] = "ORDER BY";
    $aryQuery[] = " r.strReceiveCode, rd.lngReceiveDetailNo, r.lngRevisionNo DESC";

    // クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);

    return $strQuery;
}

/**
 * 受注コードによりデータの状態を確認する
 *
 * @param [type] $strstrreceivecode
 * @param [type] $objDB
 * @return void [true:削除済・無効データ　false：有効データ]
 */
function fncCheckData($strstrreceivecode, $objDB)
{
    $result = false;
    unset($aryQuery);
    $aryQuery[] = "SELECT";
    $aryQuery[] = " min(lngrevisionno) lngrevisionno, bytInvalidFlag, strreceivecode ";
    $aryQuery[] = "FROM m_receive ";
    $aryQuery[] = "WHERE strreceivecode='" . $strstrreceivecode . "' ";
    $aryQuery[] = "group by strreceivecode, bytInvalidFlag";

    // クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum) {
        $resultObj = $objDB->fetchArray($lngResultID, 0);
    }

    $objDB->freeResult($lngResultID);

    if ($resultObj["lngrevisionno"] < 0 || $resultObj["bytInvalidFlag"]) {
        $result = true;
    }
    return $result;
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
function fncSetHeaderDataToTr($doc, $trBody, $bgcolor, $aryTableHeaderName, $displayColumns, $record, $toUTF8Flag)
{
    // 指定されたテーブル項目のセルを作成する
    foreach ($aryTableHeaderName as $key => $value) {
        // 表示対象のカラムの場合
        if (array_key_exists($key, $displayColumns)) {
            // 項目別に表示テキストを設定
            switch ($key) {
                // 登録日
                case "dtminsertdate":
                    $td = $doc->createElement("td", $record["dtminsertdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [入力者表示コード] 入力者表示名
                case "lnginputusercode":
                    $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];                        
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 顧客受注番号
                case "strcustomerreceivecode":
                    $td = $doc->createElement("td", $record["strcustomerreceivecode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 受注ＮＯ.
                case "strreceivecode":
                    $textContent = $record["strreceivecode"];
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // リビジョン番号
                case "lngrevisionno":
                    $td = $doc->createElement("td", $record["lngrevisionno"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 製品コード
                case "strproductcode":
                    $td = $doc->createElement("td", $record["strproductcode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 製品マスタ.製品コード(日本語)
                case "strproductname":
                    $textContent = $record["strproductname"];
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 製品マスタ.製品名称(英語)
                case "strproductenglishname":
                    $textContent = $record["strproductenglishname"];
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [営業部署表示コード] 営業部署表示名
                case "lnginchargegroupcode":
                    if ($record["strgroupdisplaycode"] != '') {
                        $textContent = "[" . $record["strgroupdisplaycode"] . "]" . " " . $record["strgroupdisplayname"];
                    } else {
                        $textContent = "    ";
                    }
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [開発担当者表示コード] 開発担当者表示名
                case "lnginchargeusercode":
                    if ($record["struserdisplaycode"] != '') {
                        $textContent = "[" . $record["struserdisplaycode"] . "]" . " " . $record["struserdisplayname"];
                    } else {
                        $textContent = "    ";
                    }
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 売上区分
                case "lngsalesclasscode":                    
                    if ($record["lngsalesclasscode"] != '') {
                        $textContent = "[" . $record["lngsalesclasscode"] . "]" . " " . $record["strsalesclassname"];
                    } else {
                        $textContent = "    ";
                    }
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 顧客品番
                case "strgoodscode":
                    $td = $doc->createElement("td", $record["strgoodscode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [顧客表示コード] 顧客表示名
                case "lngcustomercompanycode":
                    if ($record["strcustomerdisplaycode"] != '') {
                        $textContent = "[" . $record["strcustomerdisplaycode"] . "]" . " " . $record["strcustomerdisplayname"];
                    } else {
                        $textContent = "    ";
                    }
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 納期
                case "dtmdeliverydate":                
                    $textContent = $record["dtmdeliverydate"];
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 状態
                case "lngreceivestatuscode":
                    $textContent = $record["strreceivestatusname"];
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 明細行番号
                case "lngrecordno":
                    $td = $doc->createElement("td", $record["lngreceivedetailno"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 単価
                case "curproductprice":
                    $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curproductprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 単位
                case "lngproductunitcode":
                    $textContent = $record["lngproductunitname"];
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 数量
                case "lngproductquantity":
                    $textContent = $record["lngproductquantity"];
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 税抜金額
                case "cursubtotalprice":
                    $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["cursubtotalprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 明細備考
                case "strdetailnote":
                    $textContent = $record["strdetailnote"];
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
            }
        }
    }
}
