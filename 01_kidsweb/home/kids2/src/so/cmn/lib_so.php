<?
/**
 *    �����ܺ١������̵�����ؿ���
 *
 *    @package   kuwagata
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp>
 *    @access    public
 *    @version   1.01
 *
 *    ��������
 *    ������̴�Ϣ�δؿ�
 *
 *    ��������
 *
 *    2004.03.17    �ܺ�ɽ������ñ����ʬ��ɽ�������򾮿����ʲ�������ѹ�
 *    2004.03.29    ������̰���ɽ���������ٹ��ֹ���ʬ��ɽ���ѥ����ȥ�����ɽ������褦���ѹ�
 *
 */

/**
 * ���ꤵ�줿�����ֹ椫�����إå�������������ӣѣ�ʸ�����
 *
 *    ��������ֹ�Υإå�����μ����ѣӣѣ�ʸ�����ؿ�
 *
 *    @param  Integer     $lngReceiveNo             ������������ֹ�
 *    @return strQuery     $strQuery ������SQLʸ
 *    @access public
 */
function fncGetReceiveHeadNoToInfoSQL($lngReceiveNo, $lngRevisionNo, $lngreceivestatuscode)
{
    // SQLʸ�κ���
    $aryQuery[] = "SELECT distinct on (r.lngReceiveNo) r.lngReceiveNo as lngReceiveNo, r.lngRevisionNo as lngRevisionNo";

    // ��Ͽ��
    $aryQuery[] = ", to_char( r.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS' ) as dtmInsertDate";
    // �׾���
    $aryQuery[] = ", to_char( r.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmReceiveAppDate";
    // �ܵҼ����ֹ�
    $aryQuery[] = ", r.strCustomerReceiveCode as strCustomerReceiveCode";
    // ��������
    $aryQuery[] = ", r.strReceiveCode as strReceiveCode";
    // ���ʥ�����
    $aryQuery[] = "  , p.strproductcode";
    $aryQuery[] = "  , p.strproductname";
    $aryQuery[] = "  , p.strrevisecode";
    // ���ϼ�
    $aryQuery[] = ", r.lngInputUserCode as lngInputUserCode";
    $aryQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode";
    $aryQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName";
    // �ܵ�
    $aryQuery[] = ", r.lngCustomerCompanyCode as lngCustomerCode";
    $aryQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
    $aryQuery[] = ", cust_c.strCompanyDisplayName as strCustomerDisplayName";
    // ����
    $aryQuery[] = ", r.lngGroupCode as lngInChargeGroupCode";
    $aryQuery[] = ", inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode";
    $aryQuery[] = ", inchg_g.strGroupDisplayName as strInChargeGroupDisplayName";
    // ô����
    $aryQuery[] = ", r.lngUserCode as lngInChargeUserCode";
    $aryQuery[] = ", inchg_u.strUserDisplayCode as strInChargeUserDisplayCode";
    $aryQuery[] = ", inchg_u.strUserDisplayName as strInChargeUserDisplayName";
    // �̲�
    $aryQuery[] = ", r.lngMonetaryUnitCode as lngMonetaryUnitCode";
    $aryQuery[] = ", mu.strMonetaryUnitName as strMonetaryUnitName";
    $aryQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
    // �졼�ȥ�����
    $aryQuery[] = ", r.lngMonetaryRateCode as lngMonetaryRateCode";
    $aryQuery[] = ", mr.strMonetaryRateName as strMonetaryRateName";
    // �����졼��
    $aryQuery[] = ", r.curConversionRate as curConversionRate";
    // ����
    $aryQuery[] = ", r.lngReceiveStatusCode as lngReceiveStatusCode";
    $aryQuery[] = ", rs.strReceiveStatusName as strReceiveStatusName";
    $aryQuery[] = " FROM m_Receive r ";
    if ($lngRevisionNo == "") {
        $aryQuery[] = "  inner join ( ";
        $aryQuery[] = "    select";
        $aryQuery[] = "      max(lngRevisionNo) lngRevisionNo";
        $aryQuery[] = "      , strReceiveCode ";
        $aryQuery[] = "    from";
        $aryQuery[] = "      m_Receive";
        $aryQuery[] = "    group by";
        $aryQuery[] = "      strReceiveCode";
        $aryQuery[] = "  ) r1";
        $aryQuery[] = "    on r.lngrevisionno = r1.lngRevisionNo ";
        $aryQuery[] = "    and r.strreceivecode = r1.strReceiveCode ";
    }
    $aryQuery[] = "  LEFT JOIN t_ReceiveDetail rd ";
    $aryQuery[] = "    USING (lngReceiveNo) ";
    $aryQuery[] = "  LEFT JOIN ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      p1.strproductcode";
    $aryQuery[] = "      , p1.strproductname";
    $aryQuery[] = "	  , p1.strrevisecode";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_product p1 ";
    $aryQuery[] = "      inner join ( ";
    $aryQuery[] = "        select";
    $aryQuery[] = "          max(p2.lngRevisionNo) lngRevisionNo";
    $aryQuery[] = "          , p2.strproductcode";
    $aryQuery[] = "          , p2.strrevisecode ";
    $aryQuery[] = "        from";
    $aryQuery[] = "          m_Product p2 ";
    $aryQuery[] = "        where";
    $aryQuery[] = "          p2.bytinvalidflag = false ";
    $aryQuery[] = "          and not exists ( ";
    $aryQuery[] = "            select";
    $aryQuery[] = "              strproductcode ";
    $aryQuery[] = "            from";
    $aryQuery[] = "              m_product p3 ";
    $aryQuery[] = "            where";
    $aryQuery[] = "              p3.lngRevisionNo < 0 ";
    $aryQuery[] = "              and p3.strproductcode = p2.strproductcode ";
    $aryQuery[] = "              and p3.strrevisecode = p2.strrevisecode";
    $aryQuery[] = "          ) ";
    $aryQuery[] = "        group by";
    $aryQuery[] = "          p2.strProductCode";
    $aryQuery[] = "          , p2.strrevisecode";
    $aryQuery[] = "      ) p4 ";
    $aryQuery[] = "        on p1.lngRevisionNo = p4.lngRevisionNo ";
    $aryQuery[] = "        and p1.strproductcode = p4.strproductcode ";
    $aryQuery[] = "        and p1.strrevisecode = p4.strrevisecode";
    $aryQuery[] = "  ) p ";
    $aryQuery[] = "    ON rd.strProductCode = p.strProductCode ";
    $aryQuery[] = "    and rd.strrevisecode = p.strrevisecode ";
    $aryQuery[] = " LEFT JOIN m_User input_u ON r.lngInputUserCode = input_u.lngUserCode";
    $aryQuery[] = " LEFT JOIN m_Company cust_c ON r.lngCustomerCompanyCode = cust_c.lngCompanyCode";
    $aryQuery[] = " LEFT JOIN m_Group inchg_g ON r.lngGroupCode = inchg_g.lngGroupCode";
    $aryQuery[] = " LEFT JOIN m_User inchg_u ON r.lngUserCode = inchg_u.lngUserCode";
    $aryQuery[] = " LEFT JOIN m_ReceiveStatus rs USING (lngReceiveStatusCode)";
    $aryQuery[] = " LEFT JOIN m_MonetaryUnit mu ON r.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
    $aryQuery[] = " LEFT JOIN m_MonetaryRateClass mr ON r.lngMonetaryRateCode = mr.lngMonetaryRateCode";
    $aryQuery[] = " WHERE r.lngReceiveNo in (" . $lngReceiveNo . ")";
    if ($lngRevisionNo != "") {
        $aryQuery[] = " AND r.lngRevisionNo = " . $lngRevisionNo . "";
    }
    if ($lngreceivestatuscode != null) {
        $aryQuery[] = " and r.lngreceivestatuscode = " . $lngreceivestatuscode . " ";
    }

    $strQuery = implode("\n", $aryQuery);
    
    return $strQuery;
}

/**
 * ���ꤵ�줿�����ֹ椫��������پ�����������ӣѣ�ʸ�����
 *
 *    ��������ֹ�����پ���μ����ѣӣѣ�ʸ�����ؿ�
 *
 *    @param  Integer     $lngReceiveNo             ������������ֹ�
 *    @return strQuery     $strQuery ������SQLʸ
 *    @access public
 */
function fncGetReceiveDetailNoToInfoSQL($lngReceiveNo, $lngRevisionNo)
{
    // SQLʸ�κ���
    $aryQuery[] = "SELECT rd.lngSortKey as lngRecordNo, ";
    $aryQuery[] = "rd.lngReceiveNo as lngReceiveNo, rd.lngRevisionNo as lngRevisionNo";
    $aryQuery[] = ", rd.lngreceivedetailno";
    // ���ʥ����ɡ�̾��
    $aryQuery[] = ", rd.strProductCode as strProductCode";
    $aryQuery[] = ", p.strProductName as strProductName";
    $aryQuery[] = ", p.strproductenglishname as strproductenglishname";
    // ����ʬ
    $aryQuery[] = ", rd.lngSalesClassCode as lngSalesClassCode";
    $aryQuery[] = ", ss.strSalesClassName as strSalesClassName";
    // ���ʬ��
    $aryQuery[] = ", sd.lngsalesdivisioncode";
    $aryQuery[] = ", sd.strsalesdivisionname";
    // �ܵ�����
    $aryQuery[] = ", p.strGoodsCode as strGoodsCode";
    // Ǽ��
    $aryQuery[] = ", rd.dtmDeliveryDate as dtmDeliveryDate";
    // ñ��
    $aryQuery[] = ", To_char( rd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
    // ñ��
    $aryQuery[] = ", rd.lngProductUnitCode as lngProductUnitCode";
    $aryQuery[] = ", pu.strProductUnitName as strProductUnitName";
    // ����
    $aryQuery[] = ", To_char( rd.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
    // ��ȴ���
    $aryQuery[] = ", To_char( rd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
    // ��������
    $aryQuery[] = ", rd.strNote as strDetailNote";

    // ���ٹԤ�ɽ��������
    $aryQuery[] = " FROM t_ReceiveDetail rd ";
    if ($lngRevisionNo == "") {
        $aryQuery[] = "  inner join ( ";
        $aryQuery[] = "    select";
        $aryQuery[] = "      max(lngRevisionNo) lngRevisionNo";
        $aryQuery[] = "      , lngreceiveno ";
        $aryQuery[] = "    from";
        $aryQuery[] = "      t_ReceiveDetail ";
        $aryQuery[] = "    group by";
        $aryQuery[] = "      lngreceiveno";
        $aryQuery[] = "  ) rd1 ";
        $aryQuery[] = "    on rd.lngrevisionno = rd1.lngRevisionNo ";
        $aryQuery[] = "    and rd.lngreceiveno = rd1.lngreceiveno ";
    }
    $aryQuery[] = " LEFT JOIN (";
    $aryQuery[] = "   select p1.*  from m_product p1 ";
    $aryQuery[] = "   inner join (select max(lngrevisionno) lngrevisionno, strproductcode, strrevisecode from m_Product group by strProductCode, strrevisecode) p2";
    $aryQuery[] = "   on p1.lngrevisionno = p2.lngrevisionno and p1.strproductcode = p2.strproductcode and p1.strrevisecode = p2.strrevisecode";
    $aryQuery[] = ") p ";
    $aryQuery[] = " ON rd.strProductCode = p.strProductCode and rd.strrevisecode = p.strrevisecode ";
    $aryQuery[] = " LEFT JOIN m_SalesClass ss on rd.lngSalesClassCode = ss.lngSalesClassCode";
    $aryQuery[] = " LEFT JOIN m_salesclassdivisonlink ssdl on ssdl.lngSalesClassCode = ss.lngSalesClassCode";
    $aryQuery[] = " LEFT JOIN m_salesdivision sd on sd.lngsalesdivisioncode = ssdl.lngsalesdivisioncode";
    $aryQuery[] = " LEFT JOIN m_ProductUnit pu ON rd.lngProductUnitCode = pu.lngProductUnitCode";
    $aryQuery[] = " WHERE rd.lngReceiveNo in (" . $lngReceiveNo . ") ";
    if ($lngRevisionNo != "") {
        $aryQuery[] = " AND rd.lngRevisionNo = " . $lngRevisionNo . "";
    }
    $aryQuery[] = " ORDER BY rd.lngSortKey ASC ";

    $strQuery = implode("\n", $aryQuery);

    return $strQuery;
}

/**
 * �ܺ�ɽ���ؿ��ʥإå��ѡ�
 *
 *    �ơ��֥빽���Ǽ���ǡ����ܺ٤���Ϥ���ؿ�
 *    �إå��Ԥ�ɽ������
 *
 *    @param  Array     $aryResult                 �إå��Ԥθ�����̤���Ǽ���줿����
 *    @access public
 */
function fncSetReceiveHeadTabelData($aryResult)
{
    $aryColumnNames = array_keys($aryResult);

    // ɽ���оݥ������������̤ν���
    for ($i = 0; $i < count($aryColumnNames); $i++) {
        $strColumnName = $aryColumnNames[$i];

        // ��Ͽ��
        if ($strColumnName == "dtminsertdate") {
            $aryNewResult[$strColumnName] = str_replace("-", "/", substr($aryResult["dtminsertdate"], 0, 19));
        }

        // �׾���
        else if ($strColumnName == "dtmreceiveappdate") {
            $aryNewResult[$strColumnName] = str_replace("-", "/", $aryResult["dtmreceiveappdate"]);
        }

        // ���ϼ�
        else if ($strColumnName == "lnginputusercode") {
            if ($aryResult["strinputuserdisplaycode"]) {
                $aryNewResult[$strColumnName] = "[" . $aryResult["strinputuserdisplaycode"] . "]";
            } else {
                $aryNewResult[$strColumnName] = "     ";
            }
            $aryNewResult[$strColumnName] .= " " . $aryResult["strinputuserdisplayname"];
        }

        // �ܵ�
        else if ($strColumnName == "lngcustomercode") {
            if ($aryResult["strcustomerdisplaycode"]) {
                $aryNewResult[$strColumnName] = "[" . $aryResult["strcustomerdisplaycode"] . "]";
            } else {
                $aryNewResult[$strColumnName] = "      ";
            }
            $aryNewResult[$strColumnName] .= " " . $aryResult["strcustomerdisplayname"];
        }

        // ����
        else if ($strColumnName == "lnginchargegroupcode") {
            if ($aryResult["strinchargegroupdisplaycode"]) {
                $aryNewResult[$strColumnName] = "[" . $aryResult["strinchargegroupdisplaycode"] . "]";
            } else {
                $aryNewResult[$strColumnName] = "    ";
            }
            $aryNewResult[$strColumnName] .= " " . $aryResult["strinchargegroupdisplayname"];
        }

        // ô����
        else if ($strColumnName == "lnginchargeusercode") {
            if ($aryResult["strinchargeuserdisplaycode"]) {
                $aryNewResult[$strColumnName] = "[" . $aryResult["strinchargeuserdisplaycode"] . "]";
            } else {
                $aryNewResult[$strColumnName] = "     ";
            }
            $aryNewResult[$strColumnName] .= " " . $aryResult["strinchargeuserdisplayname"];
        }

        // ��׶��
        else if ($strColumnName == "curtotalprice") {
            $aryNewResult[$strColumnName] = $aryResult["strmonetaryunitsign"] . " ";
            if (!$aryResult["curtotalprice"]) {
                $aryNewResult[$strColumnName] .= "0.00";
            } else {
                $aryNewResult[$strColumnName] .= $aryResult["curtotalprice"];
            }
        }

        // ����
        else if ($strColumnName == "lngreceivestatuscode") {
            $aryNewResult[$strColumnName] = $aryResult["strreceivestatusname"];
        }

        // �̲�
        else if ($strColumnName == "lngmonetaryunitcode") {
            $aryNewResult[$strColumnName] = $aryResult["strmonetaryunitname"];
        }

        // �졼�ȥ�����
        else if ($strColumnName == "lngmonetaryratecode") {
            if ($aryResult["lngmonetaryratecode"] and $aryResult["lngmonetaryunitcode"] != DEF_MONETARY_YEN) {
                $aryNewResult[$strColumnName] = $aryResult["strmonetaryratename"];
            } else {
                $aryNewResult[$strColumnName] = "";
            }
        }

        // ����
        else if ($strColumnName == "strnote") {
            $aryNewResult[$strColumnName] = nl2br($aryResult["strnote"]);
        }

        // ����¾�ι��ܤϤ��Τޤ޽���
        else {
            $aryNewResult[$strColumnName] = $aryResult[$strColumnName];
        }
    }

    return $aryNewResult;
}

/**
 * �ܺ�ɽ���ؿ��������ѡ�
 *
 *    �ơ��֥빽���Ǽ���ǡ����ܺ٤���Ϥ���ؿ�
 *    ���ٹԤ�ɽ������
 *
 *    @param  Array     $aryDetailResult     ���ٹԤθ�����̤���Ǽ���줿����ʣ��ǡ���ʬ��
 *    @param  Array     $aryHeadResult         �إå��Ԥθ�����̤���Ǽ���줿����ʻ����ѡ�
 *    @access public
 */
function fncSetReceiveDetailTabelData($aryDetailResult, $aryHeadResult)
{
    $aryColumnNames = array_keys($aryDetailResult);

    // ɽ���оݥ������������̤ν���
    for ($i = 0; $i < count($aryColumnNames); $i++) {
        $strColumnName = $aryColumnNames[$i];

        // ���ʥ�����̾��
        if ($strColumnName == "strproductcode") {
            if ($aryDetailResult["strproductcode"]) {
                $aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["strproductcode"] . "]";
            } else {
                $aryNewDetailResult[$strColumnName] = "      ";
            }
            $aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strproductname"];
        }

        // ����ʬ
        else if ($strColumnName == "lngsalesclasscode") {
            if ($aryDetailResult["lngsalesclasscode"]) {
                $aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["lngsalesclasscode"] . "]";
            } else {
                $aryNewDetailResult[$strColumnName] = "      ";
            }
            $aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strsalesclassname"];
        }

        // �ܵ�����
        else if ($strColumnName == "strgoodscode") {
            $aryNewDetailResult[$strColumnName] = $aryDetailResult[$strColumnName];
        }

        // Ǽ��
        else if ($strColumnName == "dtmdeliverydate") {
            $aryNewDetailResult[$strColumnName] = str_replace("-", "/", $aryDetailResult["dtmdeliverydate"]);
        }

        // ñ��
        else if ($strColumnName == "curproductprice") {
            $aryNewDetailResult[$strColumnName] = $aryHeadResult["strmonetaryunitsign"] . " ";
            if (!$aryDetailResult["curproductprice"]) {
                $aryNewDetailResult[$strColumnName] .= "0.00";
            } else {
                $aryNewDetailResult[$strColumnName] .= $aryDetailResult["curproductprice"];
            }
        }

        // ñ��
        else if ($strColumnName == "lngproductunitcode") {
            $aryNewDetailResult[$strColumnName] = $aryDetailResult["strproductunitname"];
        }

        // ��ȴ���
        else if ($strColumnName == "cursubtotalprice") {
            $aryNewDetailResult[$strColumnName] = $aryHeadResult["strmonetaryunitsign"] . " ";
            if (!$aryDetailResult["cursubtotalprice"]) {
                $aryNewDetailResult[$strColumnName] .= "0.00";
            } else {
                $aryNewDetailResult[$strColumnName] .= $aryDetailResult["cursubtotalprice"];
            }
        }

        // ��������
        else if ($strColumnName == "strdetailnote") {
            $aryNewDetailResult[$strColumnName] = nl2br($aryDetailResult[$strColumnName]);
        }

        // ����¾�ι��ܤϤ��Τޤ޽���
        else {
            $aryNewDetailResult[$strColumnName] = $aryDetailResult[$strColumnName];
        }
    }

    return $aryNewDetailResult;
}

/**
 * �ܺ�ɽ���ѥ����̾���åȴؿ�
 *
 *    �ܺ�ɽ�����Υ����̾�����ܸ졢�Ѹ�ˤǤ�����ؿ�
 *
 *    @param  Array     $aryResult         ������̤���Ǽ���줿����
 *    @param  Array     $aryTytle         �����̾����Ǽ���줿����
 *    @access public
 */
function fncSetReceiveTabelName($aryResult, $aryTytle)
{
    $aryColumnNames = array_values($aryResult);

    // ɽ���оݥ������������̤ν���
    for ($i = 0; $i < count($aryColumnNames); $i++) {
        $strColumnName = $aryColumnNames[$i];

        if ($aryTytle[$strColumnName]) {
            $strNewColumnName = "CN" . $strColumnName;
            $aryNames[$strNewColumnName] = $aryTytle[$strColumnName];
        }
    }

    return $aryNames;
}

/**
 * ����Υ����ɤΥǡ�����¾�Υޥ����ǻ��Ѥ��Ƥ��륳���ɼ���
 *
 *    ���ꥳ���ɤ��Ф��ơ����ꤵ�줿�ޥ����θ����ؿ�
 *
 *    @param  String         $strCode         �����оݥ�����
 *    @param    Integer        $lngMode        �����⡼��    1:�������ɤ�������ޥ���    �ʽ缡�ɲá�
 *    @param  Object        $objDB            DB���֥�������
 *    @return Array         $aryCode        �����оݥ����ɤ����Ѥ���Ƥ���ޥ�����Υ����ɤ�����
 *    @access public
 */
function fncGetDeleteCodeToMaster($strCode, $lngMode, $objDB)
{
    // SQLʸ�κ���
    $strQuery = "SELECT distinct on (";
    switch ($lngMode) {
        case 1: // �������ɤ������ޥ����θ�����
            $strQuery .= "s.strSalesCode) s.strSalesCode as lngSearchNo FROM m_Sales s, m_Receive r ";
            $strQuery .= "WHERE s.lngReceiveNo = r.lngReceiveNo AND s.bytInvalidFlag = FALSE AND r.strReceiveCode = '";
            break;
    }
    $strQuery .= $strCode . "'";

    // ���������꡼�μ¹�
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryCode[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        $aryCode = false;
    }
    $objDB->freeResult($lngResultID);

    return $aryCode;
}

/**
 * �����NO�Υǡ�����¾�Υޥ����ǻ��Ѥ��Ƥ��륳���ɼ���
 *
 *    ����NO���Ф��ơ����ꤵ�줿�ޥ����θ����ؿ�
 *
 *    @param  Integer     $lngNo             �����о�No
 *    @param    Integer        $lngMode        �����⡼��    1:�������ɤ�������ޥ���    �ʽ缡�ɲá�
 *    @param  Object        $objDB            DB���֥�������
 *    @return Array         $aryCode        �����оݥ����ɤ����Ѥ���Ƥ���ޥ�����Υ����ɤ�����
 *    @access public
 */
function fncGetDeleteNoToMaster($lngNo, $lngMode, $objDB)
{
    // SQLʸ�κ���
    $strQuery = "SELECT distinct on (";
    switch ($lngMode) {
        case 1: // ����No��������ޥ����θ�����
            $strQuery .= "s.lngReceiveNo) s.lngReceiveNo as lngSearchNo FROM m_Sales s ";
            $strQuery .= "WHERE s.bytInvalidFlag = FALSE AND s.lngReceiveNo = ";
            break;
        case 2: // ����No�������ޥ����θ�����
            $strQuery .= "s.lngReceiveNo) s.lngReceiveNo as lngSearchNo FROM m_Sales s ";
            $strQuery .= "WHERE s.bytInvalidFlag = FALSE AND s.lngReceiveNo = ";
            break;
    }
    $strQuery .= $lngNo;

    // ���������꡼�μ¹�
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryCode[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        $aryCode = false;
    }
    $objDB->freeResult($lngResultID);

    return $aryCode;
}

/**
 * ����μ���ǡ����ˤĤ���̵�������뤳�ȤǤɤ��ʤ뤫�������櫓����
 *
 *    ����μ���ǡ����ξ��֤�Ĵ�������������櫓����ؿ�
 *
 *    @param  Array         $aryReceiveData     ����ǡ���
 *    @param  Object        $objDB            DB���֥�������
 *    @return Integer     $lngCase        ���֤Υ�����
 *                                        1: �оݼ���ǡ�����̵�������Ƥ⡢�ǿ��μ���ǡ������ƶ������ʤ�
 *                                        2: �оݼ���ǡ�����̵�������뤳�Ȥǡ��ǿ��μ���ǡ����������ؤ��
 *                                        3: �оݼ���ǡ���������ǡ����ǡ��������褹��
 *                                        4: �оݼ���ǡ�����̵�������뤳�Ȥǡ��ǿ��μ���ǡ����ˤʤꤦ�����ǡ������ʤ�
 *    @access public
 */
function fncGetInvalidCodeToMaster($aryReceiveData, $objDB)
{
    // �������ɤμ���
    $strReceiveCode = $aryReceiveData["strreceivecode2"];

    // ����оݼ����Ʊ���������ɤκǿ��μ���No��Ĵ�٤�
    $strQuery = "SELECT lngReceiveNo FROM m_Receive r WHERE r.strReceiveCode = '" . $strReceiveCode . "' AND r.bytInvalidFlag = FALSE ";
    $strQuery .= " AND r.lngRevisionNo >= 0";
    $strQuery .= " AND r.lngRevisionNo = ( "
        . "SELECT MAX( r1.lngRevisionNo ) FROM m_Receive r1 WHERE r1.strReceiveCode = r.strReceiveCode ";
    $strQuery .= " AND r1.strReviseCode = ( "
        . "SELECT MAX( r2.strReviseCode ) FROM m_Receive r2 WHERE r2.strReceiveCode = r1.strReceiveCode ) )";

    // ���������꡼�μ¹�
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum == 1) {
        $objResult = $objDB->fetchObject($lngResultID, 0);
        $lngNewReceiveNo = $objResult->lngreceiveno;
    } else {
        $lngCase = 4;
    }
    $objDB->freeResult($lngResultID);

    // ����оݤ��ǿ����ɤ����Υ����å�
    if ($lngCase != 4) {
        if ($lngNewReceiveNo == $aryReceiveData["lngreceiveno"]) {
            // �ǿ��ξ��
            // ����оݼ���ʳ��Ǥ�Ʊ���������ɤκǿ��μ���No��Ĵ�٤�
            $strQuery = "SELECT lngReceiveNo FROM m_Receive r WHERE r.strReceiveCode = '" . $strReceiveCode . "' AND r.bytInvalidFlag = FALSE ";
            $strQuery .= " AND r.lngReceiveNo <> " . $aryReceiveData["lngreceiveno"] . " AND r.lngRevisionNo >= 0";
            $strQuery .= " AND r.lngRevisionNo = ( "
                . "SELECT MAX( r1.lngRevisionNo ) FROM m_Receive r1 WHERE r1.strReceiveCode = r.strReceiveCode ";
            $strQuery .= " AND r1.strReviseCode = ( "
                . "SELECT MAX( r2.strReviseCode ) FROM m_Receive r2 WHERE r2.strReceiveCode = r1.strReceiveCode ) )";

            // ���������꡼�μ¹�
            list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

            if ($lngResultNum >= 1) {
                $lngCase = 2;
            } else {
                $lngCase = 4;
            }
            $objDB->freeResult($lngResultID);
        }
        // �оݼ�������ǡ������ɤ����γ�ǧ
        else if ($aryReceiveData["lngrevisionno"] < 0) {
            $lngCase = 3;
        } else {
            $lngCase = 1;
        }
    }

    return $lngCase;
}

/**
 * ��¾��������å�
 *
 * @param [type] $lngFunctionCode
 * @param [type] $strProductCode
 * @param [type] $lngRevisionNo
 * @param [type] $objDB
 * @return void [true����¾����ȯ����false����¾����ȯ�����Ƥ��ʤ�]
 */
function fncCheckExclusiveControl($lngFunctionCode, $strProductCode, $lngRevisionNo, $objDB)
{
    $strQuery = "select";
    $strQuery .= "  lngfunctioncode,strexclusivekey1,strexclusivekey2  ";
    $strQuery .= "from";
    $strQuery .= "  t_exclusivecontrol ";
    $strQuery .= "where";
    $strQuery .= "  lngfunctioncode = " . $lngFunctionCode;
    $strQuery .= "  and strexclusivekey1 = '" . $strProductCode . "' ";
    $strQuery .= "  and strexclusivekey2 = '" . $lngRevisionNo . "' ";

    // ���������꡼�μ¹�
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum >= 1) {
        $result = true;
    } else {
        $result = false;
    }
    $objDB->freeResult($lngResultID);

    return $result;
}
