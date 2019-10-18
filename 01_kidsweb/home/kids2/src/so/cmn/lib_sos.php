<?
// ----------------------------------------------------------------------------
/**
 *       �������  ������Ϣ�ؿ���
 *
 *
 *       ��������
 *         ��������̴�Ϣ�δؿ�
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------

/**
 * �������ܤ�����פ���ǿ��μ���ǡ������������SQLʸ�κ����ؿ�
 *
 *    �������ܤ��� SQLʸ���������
 *
 *    @param  Array     $displayColumns             ɽ���оݥ����̾������
 *    @param  Array     $searchColumns         �����оݥ����̾������
 *    @param  Array     $from     ��������(from)������
 *    @param  Array     $to       ��������(to)������
 *    @param  Array     $searchValue  �������Ƥ�����
 *    @return Array     $strSQL ������SQLʸ
 *    @access public
 */
function fncGetMaxReceiveSQL($displayColumns, $searchColumns, $from, $to, $searchValue, $optionColumns)
{
    // ���ٸ�������
    $detailConditionCount = 0;
    // ���������Ω��
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
    // ���ʥ�����_from
    if (array_key_exists("strProductCode", $searchColumns) &&
        array_key_exists("strProductCode", $from) && $from["strProductCode"] != '') {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = " rd1.strProductCode" .
        " >= '" . pg_escape_string($from["strProductCode"]) . "'";
    }
    // ���ʥ�����_to
    if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $to) && $to["strProductCode"] != '') {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = " rd1.strProductCode" .
    " <= " . "'" . pg_escape_string($to["strProductCode"]) . "'";
}
    // ����̾��
    if (array_key_exists("strProductName", $searchColumns) &&
        array_key_exists("strProductName", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "UPPER(p.strproductname) like UPPER('%" . pg_escape_string($searchValue["strProductName"]) . "%')";
    }
    // ����̾��(�Ѹ�)
    if (array_key_exists("strProductEnglishName", $searchColumns) &&
        array_key_exists("strProductEnglishName", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "UPPER(p.strproductenglishname) like UPPER('%" . pg_escape_string($searchValue["strProductEnglishName"]) . "%')";
    }

    // �ܵ�����
    if (array_key_exists("strGoodsCode", $searchColumns) &&
        array_key_exists("strGoodsCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "p.strgoodscode = '" . pg_escape_string($searchValue["strGoodsCode"]) . "'";
    }

    // �Ķ�����
    if (array_key_exists("lngInChargeGroupCode", $searchColumns) &&
        array_key_exists("lngInChargeGroupCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "mg.strGroupDisplayCode = '" . pg_escape_string($searchValue["lngInChargeGroupCode"]) . "'";
    }

    // ��ȯô����
    if (array_key_exists("lngInChargeUserCode", $searchColumns) &&
        array_key_exists("lngInChargeUserCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "mu.strUserDisplayCode = '" . pg_escape_string($searchValue["lngInChargeUserCode"]) . "'";
    }

    // ����ʬ
    if (array_key_exists("lngSalesClassCode", $searchColumns) &&
        array_key_exists("lngSalesClassCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "rd1.lngSalesClassCode = '" . pg_escape_string($searchValue["lngSalesClassCode"]) . "'";
    }

    // Ǽ��_from
    if (array_key_exists("dtmDeliveryDate", $searchColumns) &&
        array_key_exists("dtmDeliveryDate", $from) && $from["dtmDeliveryDate"] != '') {
        $aryQuery[] = "AND rd1.dtmdeliverydate" .
            " >= '" . $from["dtmDeliveryDate"] . "'";
    }
    // Ǽ��_to
    if (array_key_exists("dtmDeliveryDate", $searchColumns) &&
        array_key_exists("dtmDeliveryDate", $to) && $to["dtmDeliveryDate"] != '') {
        $aryQuery[] = "AND rd1.dtmdeliverydate" .
            " <= " . "'" . $to["dtmDeliveryDate"] . "'";
    }
    $aryQuery[] = "    ) as rd ";
    $aryQuery[] = "WHERE";
    $aryQuery[] = " rd.lngReceiveNo = r.lngReceiveNo ";
    $aryQuery[] = " AND rd.lngRevisionNo = r.lngRevisionNo ";
    // ��Ͽ��_from
    if (array_key_exists("dtmInsertDate", $searchColumns) &&
        array_key_exists("dtmInsertDate", $from) && $from["dtmInsertDate"] != '') {
        $aryQuery[] = "AND r.dtmInsertDate" .
            " >= '" . $from["dtmInsertDate"] . " 00:00:00'";
    }
    // ��Ͽ��_to
    if (array_key_exists("dtmInsertDate", $searchColumns) &&
        array_key_exists("dtmInsertDate", $to) && $to["dtmInsertDate"] != '') {
        $aryQuery[] = "AND r.dtmInsertDate" .
            " <= " . "'" . $to["dtmInsertDate"] . " 23:59:59.99999'";
    }

    // �ܵҼ����ֹ�_from
    if (array_key_exists("strCustomerReceiveCode", $searchColumns) &&
        array_key_exists("strCustomerReceiveCode", $from) && $from["strCustomerReceiveCode"] != '') {
        $aryQuery[] = "AND r.strCustomerReceiveCode" .
            " >= '" . $from["strCustomerReceiveCode"] . "'";
    }

    // �ܵҼ����ֹ�_to
    if (array_key_exists("strCustomerReceiveCode", $searchColumns) &&
        array_key_exists("strCustomerReceiveCode", $to) && $to["strCustomerReceiveCode"] != '') {
        $aryQuery[] = "AND r.strCustomerReceiveCode" .
            " <= " . "'" . $to["strCustomerReceiveCode"] . "'";
    }

    // ����Σ�_from
    if (array_key_exists("strReceiveCode", $searchColumns) &&
        array_key_exists("strReceiveCode", $from) && $from["strReceiveCode"] != '') {
        $fromstrReceiveCode = strpos($from["strReceiveCode"], "-") ? preg_replace(strrchr($from["strReceiveCode"], "-"), "", $from["strReceiveCode"]) : $from["strReceiveCode"];
        $aryQuery[] = "AND r.strReceiveCode" .
            " >= '" . $fromstrReceiveCode . "'";
    }

    // ����Σ�_to
    if (array_key_exists("strReceiveCode", $searchColumns) &&
        array_key_exists("strReceiveCode", $to) && $to["strReceiveCode"] != '') {
        $tostrReceiveCode = strpos($to["strReceiveCode"], "-") ? preg_replace(strrchr($to["strReceiveCode"], "-"), "", $to["strReceiveCode"]) : $to["strReceiveCode"];

        $aryQuery[] = "AND r.strReceiveCode" .
            " <= " . "'" . $tostrReceiveCode . "'";
    }

    // ���ϼ�
    if (array_key_exists("lngInputUserCode", $searchColumns) &&
        array_key_exists("lngInputUserCode", $searchValue)) {
        $aryQuery[] = " AND input_u.strUserDisplayCode = '" . $searchValue["lngInputUserCode"] . "'";
    }

    // �ܵ�
    if (array_key_exists("lngCustomerCompanyCode", $searchColumns) &&
        array_key_exists("lngCustomerCompanyCode", $searchValue)) {
        $aryQuery[] = " AND cust_c.strCompanyDisplayCode = '" . $searchValue["lngCustomerCompanyCode"] . "'";
    }

    // ����
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

    // �������ʿ�פ�ʸ������Ѵ�
    $strQuery = implode("\n", $aryQuery);
    
    return $strQuery;
}

function fncGetReceivesByStrReceiveCodeSQL($subStrQuery)
{
    // ���ٸ�������
    $detailConditionCount = 0;
    // ���������Ω��
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

    // �������ʿ�פ�ʸ������Ѵ�
    $strQuery = implode("\n", $aryQuery);

    return $strQuery;
}
