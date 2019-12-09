<?php
// ----------------------------------------------------------------------------
/**
 *       ����Ǽ�ʽ����Ͽ�ؿ���
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
 *       ��������
 *         ������Ǽ�ʽ����Ͽ��Ϣ�δؿ�
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------

// �����оݥǡ������Ф��ƥ�å����Ƥ���ͤ��ǧ����
// ��å����Ƥ���ͤ����ʤ��ʤ��ʸ������֤�
function fncGetExclusiveLockUser($lngFunctionCode, $strSlipCode, $objAuth, $objDB)
{
    $lockUserName = "";
    $v_lngusercode = $objAuth->UserCode; //1:�桼��������
    $v_stripaddress = $objAuth->AccessIP; //2:ü��IP���ɥ쥹

    $aryQuery = array();
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "  tec.lngfunctioncode, ";
    $aryQuery[] = "  tec.lngusercode, ";
    $aryQuery[] = "  tec.stripaddress, ";
    $aryQuery[] = "  mu.struserdisplayname ";
    $aryQuery[] = " FROM t_exclusivecontrol tec ";
    $aryQuery[] = "  INNER JOIN m_user mu ON tec.lngusercode = mu.lngusercode ";
    $aryQuery[] = " WHERE tec.lngfunctioncode = " . $lngFunctionCode;
    $aryQuery[] = "   AND tec.strexclusivekey1 = " . withQuote($strSlipCode);

    $strQuery = "";
    $strQuery .= implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if (0 < $lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
        if ($v_lngusercode != $aryResult[0]["lngusercode"] || $v_stripaddress != $aryResult[0]["stripaddress"]) {
            // ��å����Ƥ���桼������ɽ���ѥ桼����̾������
            $lockUserName = $aryResult[0]["struserdisplayname"];
            $isLock = 1; // �̥桼������å���
        } else {
            $lockUserName = "";
            $isLock = 2; // Ʊ�桼������å���
        }
    } else {
        // ������å����Ƥ��ʤ�
        $lockUserName = "";
        $isLock = 0; // ̤��å�
    }
    $objDB->freeResult($lngResultID);

    $aryResult["lockUserName"] = $lockUserName;
    $aryResult["isLock"] = $isLock;
    return $aryResult;
}

// �оݤε�ǽID���Ф�����¾��å�����
function fncTakeExclusiveLock($lngFunctionCode, $strSlipCode, $objAuth, $objDB)
{
    $locked = false;

    // ��¾����1�Τ��б���¾�ϥǥե�����͡�
    $v_lngfunctioncode = $lngFunctionCode; //1:��ǽ������
    $v_strexclusivekey1 = withQuote($strSlipCode); //2:��¾����1
    $v_lngusercode = $objAuth->UserCode; //5:�桼��������
    $v_stripaddress = withQuote($objAuth->AccessIP); //6:ü��IP���ɥ쥹
    $v_dtminsertdate = "now()"; //7:��Ͽ��

    $aryInsert[] = "INSERT  ";
    $aryInsert[] = " INTO t_exclusivecontrol(  ";
    $aryInsert[] = "  lngfunctioncode "; //1:��ǽ������
    $aryInsert[] = "  , strexclusivekey1 "; //2:��¾����1
    $aryInsert[] = "  , lngusercode "; //5:�桼��������
    $aryInsert[] = "  , stripaddress "; //6:ü��IP���ɥ쥹
    $aryInsert[] = "  , dtminsertdate "; //7:��Ͽ��
    $aryInsert[] = ")  ";
    $aryInsert[] = " VALUES (  ";
    $aryInsert[] = "  " . $v_lngfunctioncode; //1:��ǽ������
    $aryInsert[] = " ," . $v_strexclusivekey1; //2:��¾����1
    $aryInsert[] = " ," . $v_lngusercode; //5:�桼��������
    $aryInsert[] = " ," . $v_stripaddress; //6:ü��IP���ɥ쥹
    $aryInsert[] = " ," . $v_dtminsertdate; //7:��Ͽ��
    $aryInsert[] = ") ";
    $strQuery = "";
    $strQuery .= implode("\n", $aryInsert);

    // �ȥ�󥶥�����󳫻�
    $objDB->transactionBegin();

    // ��Ͽ�¹�
    if (!$lngResultID = $objDB->execute($strQuery)) {
        // ����
        $locked = false;
    } else {
        $objDB->freeResult($lngResultID);
        // ���ߥå�
        $objDB->transactionCommit();
        // ����
        $locked = true;
    }

    return $locked;
}

// �оݤε�ǽID���Ф�����¾��å���������
function fncReleaseExclusiveLock($lngFunctionCode, $strSlipCode, $objDB)
{
    $unlocked = false;

    $aryDelete[] = " ";
    $aryDelete[] = "DELETE  ";
    $aryDelete[] = " FROM ";
    $aryDelete[] = "  t_exclusivecontrol  ";
    $aryDelete[] = " WHERE ";
    $aryDelete[] = "  lngfunctioncode = " . $lngFunctionCode;
    $aryDelete[] = "  and strexclusivekey1 = " . withQuote($strSlipCode);
    $strQuery = "";
    $strQuery .= implode("\n", $aryDelete);

    // �ȥ�󥶥�����󳫻�
    $objDB->transactionBegin();

    // ��Ͽ�¹�
    if (!$lngResultID = $objDB->execute($strQuery)) {
        // ����
        $unlocked = false;
    } else {
        $objDB->freeResult($lngResultID);

        // ���ߥå�
        $objDB->transactionCommit();

        // ����
        $unlocked = true;
    }

    return $unlocked;
}

// ������Ψ�ץ�������������ܺ���
function fncGetTaxRatePullDown($dtmDeliveryDate, $curDefaultTax, $objDB)
{
    // DB����ǡ�������
    $strQuery = "SELECT lngtaxcode, curtax * 100 as curtax "
        . " FROM m_tax "
        . " WHERE dtmapplystartdate <= '$dtmDeliveryDate' "
        . "   AND dtmapplyenddate >= '$dtmDeliveryDate' "
        . " ORDER BY lngpriority ";
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "�����Ǿ���μ����˼���", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    // ������ܺ���
    $strHtml = "";
    for ($i = 0; $i < count($aryResult); $i++) {
        $optionValue = $aryResult[$i]["lngtaxcode"];
        $displayText = $aryResult[$i]["curtax"] * 1; // ������������0�򥫥å�

        // �ǥե�����ͤ����ꤵ��Ƥ����硢�����ͤ�����
        if ($curDefaultTax == $displayText) {
            $strHtml .= "<OPTION VALUE=\"$optionValue\" SELECTED>$displayText</OPTION>\n";
        } else {
            $strHtml .= "<OPTION VALUE=\"$optionValue\">$displayText</OPTION>\n";
        }
    }

    return $strHtml;
}

// Ǽ����ɼ�ֹ��ɳ�Ť��إå����ܼ���
function fncGetHeaderBySlipNo($lngSlipNo, $objDB)
{

    $aryQuery = array();
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "  s.lngslipno, ";
    $aryQuery[] = "  s.lngrevisionno, ";
    $aryQuery[] = "  u_ins.lngusercode as lngdrafterusercode,  "; //��ɼ�ԡʥ桼���������ɡ�
    $aryQuery[] = "  u_ins.struserdisplaycode as strdrafteruserdisplaycode,  "; //��ɼ�ԡ�ɽ���ѥ桼���������ɡ�
    $aryQuery[] = "  u_ins.struserdisplayname as strdrafteruserdisplayname, "; //��ɼ�ԡ�ɽ���ѥ桼����̾��
    $aryQuery[] = "  c_cust.strcompanydisplaycode as strcompanydisplaycode, "; //�ܵҡ�ɽ���Ѳ�ҥ����ɡ�
    $aryQuery[] = "  c_cust.strcompanydisplayname as strcompanydisplayname, "; //�ܵҡ�ɽ���Ѳ��̾��
    $aryQuery[] = "  s.strcustomerusername, "; //�ܵ�ô����
    $aryQuery[] = "  TO_CHAR(s.dtmdeliverydate, 'YYYY/MM/DD') as dtmdeliverydate, "; //Ǽ����
    $aryQuery[] = "  TO_CHAR(s.dtmpaymentlimit, 'YYYY/MM/DD') as dtmpaymentlimit, "; //��ʧ����
    $aryQuery[] = "  s.lngpaymentmethodcode, "; //��ʧ��ˡ
    $aryQuery[] = "  c_deli.strcompanydisplaycode as strdeliveryplacecompanydisplaycode, "; //Ǽ�����ɽ���Ѳ�ҥ����ɡ�
    $aryQuery[] = "  s.strdeliveryplacename, "; //Ǽ�����ɽ���Ѳ��̾��
    $aryQuery[] = "  s.strdeliveryplaceusername, "; //Ǽ����ô����
    $aryQuery[] = "  s.strnote, "; //����
    $aryQuery[] = "  s.lngtaxclasscode, "; //�����Ƕ�ʬ�ʥ������͡�
    $aryQuery[] = "  s.strtaxclassname, "; //�����Ƕ�ʬ��̾�Ρ�
    $aryQuery[] = "  s.curtax, "; //������Ψ�ʿ��͡�
    $aryQuery[] = "  Null as lngtaxcode, "; //������Ψ�ʥ������͡�
    $aryQuery[] = "  Null as strtaxamount, "; //�����ǳ�
    $aryQuery[] = "  s.curtotalprice "; //��׶��
    $aryQuery[] = " FROM m_slip s ";
    $aryQuery[] = "   LEFT JOIN m_user u_ins ON s.lnginsertusercode = u_ins.lngusercode ";
    $aryQuery[] = "   LEFT JOIN m_company c_cust ON s.lngcustomercode = c_cust.lngcompanycode ";
    $aryQuery[] = "   LEFT JOIN m_company c_deli ON s.lngdeliveryplacecode = c_deli.lngcompanycode ";
    $aryQuery[] = " WHERE ";
    $aryQuery[] = "  s.lngslipno = " . $lngSlipNo;
    $aryQuery[] = " and s.lngrevisionno = (SELECT MAX(lngrevisionno) FROM m_slip WHERE lngslipno = " . $lngSlipNo.")";
    
    // $aryQuery[] = "  AND s.lngrevisionNo = " . $lngRevisionNo;

    $strQuery = "";
    $strQuery .= implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "Ǽ�ʽ�ǡ����μ����˼���", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0];
}

// Ǽ����ɼ�ֹ��ɳ�Ť��������پ�����������
function fncGetDetailBySlipNo($lngSlipNo, $lngRevisionNo, $objDB)
{
    // �������Υ������������
    $aryDetailKey = fncGetDetailKeyBySlipNo($lngSlipNo, $lngRevisionNo, $objDB);

    // �������Υ�����ɳ�Ť��������پ�����������
    $aryDetail = array();
    for ($i = 0; $i < count($aryDetailKey); $i++) {

        $aryCondition = array();
        $aryCondition["lngReceiveNo"] = $aryDetailKey[$i]["lngreceiveno"];
        $aryCondition["lngReceiveDetailNo"] = $aryDetailKey[$i]["lngreceivedetailno"];
        $aryCondition["lngReceiveRevisionNo"] = $aryDetailKey[$i]["lngreceiverevisionno"];

        // ������ɳ�Ť����٤�1�鷺�ļ����������Τ�����˥ޡ���
        $arySubDetail = fncGetReceiveDetail($aryCondition, $objDB);
        $aryDetail = array_merge($aryDetail, $arySubDetail);
    }

    return $aryDetail;
}

// Ǽ����ɼ�ֹ��ɳ�Ť����٤Υ������ܤ����
function fncGetDetailKeyBySlipNo($lngSlipNo, $lngRevisionNo, $objDB)
{
    $aryQuery = array();
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "  sd.lngslipno, ";
    $aryQuery[] = "  sd.lngreceiveno, ";
    $aryQuery[] = "  sd.lngreceivedetailno, ";
    $aryQuery[] = "  sd.lngreceiverevisionno ";
    $aryQuery[] = " FROM t_slipdetail sd";
    $aryQuery[] = " WHERE ";
    $aryQuery[] = "  sd.lngslipno = " . $lngSlipNo;
    $aryQuery[] = "  AND sd.lngRevisionNo = " . $lngRevisionNo;

    $strQuery = "";
    $strQuery .= implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "���٤Υ������ܤμ����˼���", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult;
}

// ���ٸ���
function fncGetReceiveDetail($aryCondition, $objDB)
{
    // -------------------
    //  �������
    // -------------------
    $arySelect[] = " SELECT";
    $arySelect[] = "  rd.lngsortkey,"; //No.
    $arySelect[] = "  r.strcustomerreceivecode,"; //�ܵҼ����ֹ�
    $arySelect[] = "  r.strreceivecode,"; //�����ֹ�
    $arySelect[] = "  p.strgoodscode,"; //�ܵ�����
    $arySelect[] = "  rd.strproductcode,"; //���ʥ�����
    $arySelect[] = "  rd.strrevisecode,"; //��Х��������ɡʺ��Υ����ɡ�
    $arySelect[] = "  p.strproductname,"; //����̾
    $arySelect[] = "  p.strproductenglishname,"; //����̾�ʱѸ��
    $arySelect[] = "  g.strgroupdisplayname as strsalesdeptname,"; //�Ķ������̾�Ρ�
    $arySelect[] = "  rd.lngsalesclasscode,"; //����ʬ������
    $arySelect[] = "  sc.strsalesclassname,"; //����ʬ��̾�Ρ�
    $arySelect[] = "  TO_CHAR(rd.dtmdeliverydate, 'YYYY/MM/DD') as dtmdeliverydate,"; //Ǽ��
    $arySelect[] = "  rd.lngunitquantity,"; //����
    $arySelect[] = "  rd.curproductprice,"; //ñ��
    $arySelect[] = "  rd.lngproductunitcode,"; //ñ�̥�����
    $arySelect[] = "  pu.strproductunitname,"; //ñ�̡�̾�Ρ�
    $arySelect[] = "  rd.lngproductquantity,"; //����
    $arySelect[] = "  rd.cursubtotalprice,"; //��ȴ���
    $arySelect[] = "  rd.lngreceiveno,"; //�����ֹ��������Ͽ�ѡ�
    $arySelect[] = "  rd.lngreceivedetailno,"; //���������ֹ��������Ͽ�ѡ�
    $arySelect[] = "  rd.lngrevisionno as lngreceiverevisionno,"; //��ӥ�����ֹ��������Ͽ�ѡ�
    $arySelect[] = "  rd.strnote,"; //���͡�������Ͽ�ѡ�
    $arySelect[] = "  r.lngmonetaryunitcode,"; //�̲�ñ�̥����ɡ�������Ͽ�ѡ�
    $arySelect[] = "  r.lngmonetaryratecode,"; //�̲ߥ졼�ȥ����ɡ�������Ͽ�ѡ�
    $arySelect[] = "  mu.strmonetaryunitsign,"; //�̲�ñ�̵����������Ͽ�ѡ�
    $arySelect[] = "  sc.bytdetailunifiedflg"; //��������ե饰��������Ͽ�ѡ�
    $arySelect[] = " FROM";
    $arySelect[] = "  t_receivedetail rd ";
    $arySelect[] = "  INNER JOIN ( ";
    $arySelect[] = "    select";
    $arySelect[] = "      r1.* ";
    $arySelect[] = "    from";
    $arySelect[] = "      m_receive r1 ";
    $arySelect[] = "      inner join ( ";
    $arySelect[] = "        select";
    $arySelect[] = "          max(lngrevisionno) lngrevisionno";
    $arySelect[] = "          , strreceivecode ";
    $arySelect[] = "        from";
    $arySelect[] = "          m_receive r2 ";
    $arySelect[] = "        where";
    $arySelect[] = "          bytinvalidflag = false ";
    $arySelect[] = "          and not exists ( ";
    $arySelect[] = "            select";
    $arySelect[] = "              strreceivecode ";
    $arySelect[] = "            from";
    $arySelect[] = "              m_receive r3 ";
    $arySelect[] = "            where";
    $arySelect[] = "              lngrevisionno < 0 ";
    $arySelect[] = "              and r3.strreceivecode = r2.strreceivecode";
    $arySelect[] = "          ) ";
    $arySelect[] = "        group by";
    $arySelect[] = "          strreceivecode";
    $arySelect[] = "      ) r2 ";
    $arySelect[] = "        ON r1.lngrevisionno = r2.lngrevisionno ";
    $arySelect[] = "        and r1.strreceivecode = r2.strreceivecode ";
    $arySelect[] = "  ) r ";
    $arySelect[] = "    on rd.lngreceiveno = r.lngreceiveno ";
    $arySelect[] = "    AND rd.lngrevisionno = r.lngrevisionno ";
    $arySelect[] = "  LEFT JOIN m_company c ";
    $arySelect[] = "    ON r.lngcustomercompanycode = c.lngcompanycode ";
    $arySelect[] = "  LEFT JOIN ( ";
    $arySelect[] = "    select";
    $arySelect[] = "      p1.* ";
    $arySelect[] = "    from";
    $arySelect[] = "      m_product p1 ";
    $arySelect[] = "      inner join ( ";
    $arySelect[] = "        select";
    $arySelect[] = "          max(lngrevisionno) lngrevisionno";
    $arySelect[] = "          , strproductcode, strrevisecode ";
    $arySelect[] = "        from";
    $arySelect[] = "          m_product p2 ";
    $arySelect[] = "        where";
    $arySelect[] = "          lngrevisionno >= 0 ";
    $arySelect[] = "          and bytinvalidflag = false ";
    $arySelect[] = "          and not exists ( ";
    $arySelect[] = "            select";
    $arySelect[] = "              strproductcode ";
    $arySelect[] = "            from";
    $arySelect[] = "              m_product p3 ";
    $arySelect[] = "            where";
    $arySelect[] = "              lngrevisionno < 0 ";
    $arySelect[] = "              and p3.strproductcode = p2.strproductcode";
    $arySelect[] = "          ) ";
    $arySelect[] = "        group by";
    $arySelect[] = "          strproductcode, strrevisecode";
    $arySelect[] = "      ) p2 ";
    $arySelect[] = "        on p1.strproductcode = p2.strproductcode ";
    $arySelect[] = "        and p1.lngrevisionno = p2.lngrevisionno";
    $arySelect[] = "        and p1.strrevisecode = p2.strrevisecode";
    $arySelect[] = "  ) p ";
    $arySelect[] = "    ON rd.strproductcode = p.strproductcode ";
    $arySelect[] = "    and rd.strrevisecode = p.strrevisecode ";
    $arySelect[] = "  LEFT JOIN m_salesclass sc ";
    $arySelect[] = "    ON rd.lngsalesclasscode = sc.lngsalesclasscode ";
    $arySelect[] = "  LEFT JOIN m_productunit pu ";
    $arySelect[] = "    ON rd.lngproductunitcode = pu.lngproductunitcode ";
    $arySelect[] = "  LEFT JOIN m_group g ";
    $arySelect[] = "    ON p.lnginchargegroupcode = g.lnggroupcode ";
    $arySelect[] = "  LEFT JOIN m_monetaryunit mu ";
    $arySelect[] = "    ON r.lngmonetaryunitcode = mu.lngmonetaryunitcode ";

    // -------------------
    //  �����������
    // -------------------
    $aryWhere[] = " WHERE 1=1"; // ���ߡ��������ʾ�˿�����³�θ������˺ǽ餫��AND����Ϳ���뤿���¸�ߡ�

    // ������֥�����
    if ($aryCondition["lngreceivestatuscode"]) {
        $aryWhere[] = " AND r.lngreceivestatuscode = '" . $aryCondition["lngreceivestatuscode"] . "'";
    }

    // �ܵҡʥ����ɤǸ�����
    if ($aryCondition["strCompanyDisplayCode"]) {
        $aryWhere[] = " AND c.strcompanydisplaycode = '" . $aryCondition["strCompanyDisplayCode"] . "'";
    }

    // �ܵҼ����ֹ�
    if ($aryCondition["strCustomerReceiveCode"]) {
        $aryWhere[] = " AND r.strcustomerreceivecode = '" . $aryCondition["strCustomerReceiveCode"] . "'";
    }

    // �����ֹ�
    if ($aryCondition["lngReceiveNo"]) {
        $aryWhere[] = " AND r.lngreceiveno = " . $aryCondition["lngReceiveNo"];
    }

    // ���������ֹ�
    if ($aryCondition["lngReceiveDetailNo"]) {
        $aryWhere[] = " AND rd.lngreceivedetailno = " . $aryCondition["lngReceiveDetailNo"];
    }

    // ��ӥ�����ֹ�
    if ($aryCondition["lngReceiveRevisionNo"]) {
        $aryWhere[] = " AND rd.lngrevisionno = " . $aryCondition["lngReceiveRevisionNo"];
    }

    // ���ʥ�����
    if ($aryCondition["strReceiveDetailProductCode"]) {
        $aryWhere[] = " AND rd.strproductcode = '" . $aryCondition["strReceiveDetailProductCode"] . "'";
    }

    // �Ķ�����ʥ����ɤǸ�����
    if ($aryCondition["lngInChargeGroupCode"]) {
        $aryWhere[] = " AND g.strgroupdisplaycode = '" . $aryCondition["lngInChargeGroupCode"] . "'";
    }

    // ����ʬ�ʥ����ɤǸ�����
    if ($aryCondition["lngSalesClassCode"]) {
        $aryWhere[] = " AND rd.lngsalesclasscode = " . $aryCondition["lngSalesClassCode"];
    }

    // �ܵ�����
    if ($aryCondition["strGoodsCode"]) {
        $aryWhere[] = " AND p2.strgoodscode = " . $aryCondition["strGoodsCode"];
    }

    // Ǽ����(FROM)
    if ($aryCondition["From_dtmDeliveryDate"]) {
        $dtmSearchDate = $aryCondition["From_dtmDeliveryDate"] . " 00:00:00";
        $aryWhere[] = " AND rd.dtmdeliverydate >= '" . $dtmSearchDate . "'";
    }

    // Ǽ����(TO)
    if ($aryCondition["To_dtmDeliveryDate"]) {
        $dtmSearchDate = $aryCondition["To_dtmDeliveryDate"] . " 23:59:59";
        $aryWhere[] = " AND rd.dtmdeliverydate <= '" . $dtmSearchDate . "'";
    }

    // ��������
    if ($aryCondition["strNote"]) {
        $aryWhere[] = " AND rd.strNote LIKE '%" . $aryCondition["strNote"] . "%'";
    }

    // ���Τ�ޤ��off�ξ�硢t_receivedetail.strrevisecode='00'�Τߤ��оݡ�
    if ($aryCondition["IsIncludingResale"] == "Off") {
        $aryWhere[] = " AND rd.strrevisecode = '00'";
    }

    // -------------------
    //  �¤ӽ����
    // -------------------
    $aryOrder[] = " ORDER BY";
    $aryOrder[] = "  rd.lngsortkey";

    // -------------------
    // ���������
    // -------------------
    $strQuery = "";
    $strQuery .= implode("\n", $arySelect);
    $strQuery .= "\n";
    $strQuery .= implode("\n", $aryWhere);
    $strQuery .= "\n";
    $strQuery .= implode("\n", $aryOrder);
    // -------------------
    // ������¹�
    // -------------------
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    // ��̤�����˳�Ǽ
    $aryResult = []; //��������ǽ����
    if (0 < $lngResultNum) {
        for ($j = 0; $j < $lngResultNum; $j++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $j);
        }
    }
    $objDB->freeResult($lngResultID);

    return $aryResult;
}

function fncGetReceiveDetailHtml($aryDetail, $isCreateNew)
{
    $strHtml = "";
    for ($i = 0; $i < count($aryDetail); $i++) {
        $strDisplayValue = "";
        //�����򥹥���ץ�������
        $strHtml .= "<tr onmousedown='RowClick(this,false);'>";

        // �������򥨥ꥢ�ϥ����å��ܥå������ꡢ�������ٰ������ꥢ�ϥ����å��ܥå����ʤ��Τ��ᤳ�Τ褦�ʥ����å����Ѱ�
        if ($isCreateNew) {
            // �ǡ�����Ͽ�����������򥨥ꥢ�ˤ���������å��ܥå�����ɬ�סʥǡ������������������ٰ������ꥢ�˥����å��ܥå��������ס�
            $strHtml .= "<td class='detailCheckbox'><input type='checkbox' name='edit' onmousedown='return false;' onclick='return false;'></td>";
        }

        //NO.
        // �ǡ������������������ٰ������ꥢ��No.�����٤�����Υ���ǥå���+1�Ȥ���ʹ��ֹ��
        $rownumber = $i + 1;
        $strHtml .= "<td name='rownum'>" . $rownumber . "</td>";
        //�ܵ�ȯ���ֹ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strcustomerreceivecode"]);
        $strHtml .= "<td class='detailCustomerReceiveCode'>" . $strDisplayValue . "</td>";
        //�����ֹ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strreceivecode"]);
        $strHtml .= "<td class='detailReceiveCode'>" . $strDisplayValue . "</td>";
        //�ܵ�����
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strgoodscode"]);
        $strHtml .= "<td class='detailGoodsCode'>" . $strDisplayValue . "</td>";
        //���ʥ�����
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strproductcode"]);
        $strDisplayValue .= "_";
        $strDisplayValue .= htmlspecialchars($aryDetail[$i]["strrevisecode"]);
        $strHtml .= "<td class='detailProductCode'>" . $strDisplayValue . "</td>";
        //����̾
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strproductname"]);
        $strHtml .= "<td class='detailProductName'>" . $strDisplayValue . "</td>";
        //����̾�ʱѸ��
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strproductenglishname"]);
        $strHtml .= "<td class='detailProductEnglishName'>" . $strDisplayValue . "</td>";
        //�Ķ�����
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strsalesdeptname"]);
        $strHtml .= "<td class='detailSalesDeptName'>" . $strDisplayValue . "</td>";
        //����ʬ
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strsalesclassname"]);
        $strHtml .= "<td class='detailSalesClassName'>" . $strDisplayValue . "</td>";
        //Ǽ��
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["dtmdeliverydate"]);
        $strHtml .= "<td class='detailDeliveryDate'>" . $strDisplayValue . "</td>";
        //����
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngunitquantity"]);
        $strHtml .= "<td class='detailUnitQuantity'>" . $strDisplayValue . "</td>";
        //ñ��
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["curproductprice"]);
        $strHtml .= "<td class='detailProductPrice' style='text-align:right;'>" . number_format($strDisplayValue, 4) . "</td>";
        //ñ��
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strproductunitname"]);
        $strHtml .= "<td class='detailProductUnitName'>" . $strDisplayValue . "</td>";
        //����
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngproductquantity"]);
        $strHtml .= "<td class='detailProductQuantity' style='text-align:right;'>" . number_format($strDisplayValue) . "</td>";
        //��ȴ���
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["cursubtotalprice"]);
        $strHtml .= "<td class='detailSubTotalPrice' style='text-align:right;'>" . number_format($strDisplayValue) . "</td>";
        //�����ֹ��������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngreceiveno"]);
        $strHtml .= "<td class='forEdit detailReceiveNo'>" . $strDisplayValue . "</td>";
        //���������ֹ��������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngreceivedetailno"]);
        $strHtml .= "<td class='forEdit detailReceiveDetailNo'>" . $strDisplayValue . "</td>";
        //��ӥ�����ֹ��������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngreceiverevisionno"]);
        $strHtml .= "<td class='forEdit detailReceiveRevisionNo'>" . $strDisplayValue . "</td>";
        //���Υ����ɡ�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strrevisecode"]);
        $strHtml .= "<td class='forEdit detailReviseCode'>" . $strDisplayValue . "</td>";
        //����ʬ�����ɡ�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngsalesclasscode"]);
        $strHtml .= "<td class='forEdit detailSalesClassCode'>" . $strDisplayValue . "</td>";
        //����ñ�̥����ɡ�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngproductunitcode"]);
        $strHtml .= "<td class='forEdit detailProductUnitCode'>" . $strDisplayValue . "</td>";
        //���͡�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strnote"]);
        $strHtml .= "<td class='forEdit detailNote'>" . $strDisplayValue . "</td>";
        //�̲�ñ�̥����ɡ�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngmonetaryunitcode"]);
        $strHtml .= "<td class='forEdit detailMonetaryUnitCode'>" . $strDisplayValue . "</td>";
        //�̲ߥ졼�ȥ����ɡ�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngmonetaryratecode"]);
        $strHtml .= "<td class='forEdit detailMonetaryRateCode'>" . $strDisplayValue . "</td>";
        //�̲�ñ�̵����������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strmonetaryunitsign"]);
        $strHtml .= "<td class='forEdit detailMonetaryUnitSign'>" . $strDisplayValue . "</td>";
        //��������ե饰��������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["bytdetailunifiedflg"]);
        $strHtml .= "<td class='forEdit detailUnifiedFlg'>" . $strDisplayValue . "</td>";

        $strHtml .= "</tr>";
    }
    return $strHtml;
}

// Ǽ����ɼ�ޥ����������������
function fncGetInsertDateBySlipCode($strSlipCode, $objDB)
{
    $strQuery = ""
        . "SELECT"
        . "  TO_CHAR(dtminsertdate, 'yyyy/mm/dd hh24:mm:ss') as dtminsertdate"
        . " FROM"
        . "  m_slip"
        . " WHERE"
        . "  strslipcode = '" . $strSlipCode . "'"
    ;

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "Ǽ����ɼ�κ������μ����˼���", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0]["dtminsertdate"];
}

// Ǽ����ɼ�ޥ����������ֹ�����
function fncGetSalesNoBySlipCode($strSlipCode, $objDB)
{
    $strQuery = ""
        . "SELECT"
        . "  lngsalesno"
        . " FROM"
        . "  m_slip"
        . " WHERE"
        . "  strslipcode = '" . $strSlipCode . "'"
    ;

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "Ǽ����ɼ������ֹ�μ����˼���", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0]["lngsalesno"];
}

// ���������1���䤹
function fncIncrementPrintCountBySlipCode($strSlipCode, $objDB)
{

    $aryUpdate = array();
    $aryUpdate[] = "UPDATE m_slip ";
    $aryUpdate[] = " SET lngprintcount = (lngprintcount+1) ";
    $aryUpdate[] = " WHERE ";
    $aryUpdate[] = "  strslipcode = '" . $strSlipCode . "'";
    $strQuery = "";
    $strQuery .= implode("\n", $aryUpdate);

    // �ȥ�󥶥�����󳫻�
    $objDB->transactionBegin();

    // ��Ͽ�¹�
    if (!$lngResultID = $objDB->execute($strQuery)) {
        // ����
        fncOutputError(9501, DEF_FATAL, "Ǽ����ɼ�ΰ�������ι����˼���", true, "", $objDB);
    } else {
        // ����
        $objDB->freeResult($lngResultID);
        $objDB->transactionCommit();
    }

}

// ������֥����ɤˤ��Х�ǡ������
function fncNotReceivedDetailExists($aryDetail, $objDB)
{
    for ($i = 0; $i < count($aryDetail); $i++) {
        $d = $aryDetail[$i];

        $lngReceiveNo = $d["lngreceiveno"];
        $lngRevisionNo = $d["lngreceiverevisionno"];

        $strQuery = ""
            . "SELECT"
            . "  lngreceivestatuscode"
            . " FROM"
            . "  m_receive"
            . " WHERE"
            . "  lngreceivestatuscode not in (2, 4)"
            . "  AND lngreceiveno = " . $lngReceiveNo
            . "  AND lngrevisionno = " . $lngRevisionNo
        ;
        list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
        if ($lngResultID) {
            // ������֥����ɤ�2�ʳ������٤�¸�ߤ���ʤ�true���֤��Ƹ����Ǥ��ڤ�
            if ($lngResultNum > 0) {return true;}
        } else {
            fncOutputError(9501, DEF_FATAL, "������֥����ɤμ����˼���", true, "", $objDB);
        }
        $objDB->freeResult($lngResultID);
    }

    // ������֥����ɤ�2�ʳ������٤�¸�ߤ��ʤ�
    return false;

}

// ���٤�ɳ�Ť�����ޥ����μ�����֥����ɤ򹹿�
function fncUpdateReceiveMaster($aryDetail, $objDB)
{
    for ($i = 0; $i < count($aryDetail); $i++) {
        $d = $aryDetail[$i];

        $lngReceiveNo = $d["lngreceiveno"];
        $lngRevisionNo = $d["lngreceiverevisionno"];

        $strQuery = ""
            . "UPDATE"
            . "  m_receive"
            . " SET"
            . "  lngreceivestatuscode = 4"
            . " WHERE"
            . "  lngreceiveno = " . $lngReceiveNo
            . "  AND lngrevisionno = " . $lngRevisionNo
        ;

        // �����¹�
        if (!$lngResultID = $objDB->execute($strQuery)) {
            fncOutputError(9051, DEF_ERROR, "����ޥ����������ԡ�", true, "", $objDB);
            // ����
            return false;
        }
        $objDB->freeResult($lngResultID);
    }

    // ����
    return true;
}

// ɽ���Ѳ�ҥ����ɤ����ҥ����ɤ��������
function fncGetNumericCompanyCode($strCompanyDisplayCode, $objDB)
{
    $lngCompanyCode = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", $strCompanyDisplayCode . ":str", '', $objDB);
    return $lngCompanyCode;
}

// ɽ���Ѳ�ҥ����ɤ���񥳡��ɤ��������
function fncGetCountryCode($strCompanyDisplayCode, $objDB)
{
    $lngCountryCode = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcountrycode", "$strCompanyDisplayCode:str", '', $objDB);
    return $lngCountryCode;
}

// ɽ���Ѳ�ҥ����ɤ������������������
function fncGetClosedDay($strCompanyDisplayCode, $objDB)
{
    $strQuery = ""
    . "SELECT"
    . "  cd.lngclosedday"
    . " FROM"
    . "  m_company c "
    . "    INNER JOIN m_closedday cd "
    . "    on c.lngcloseddaycode = cd.lngcloseddaycode"
    . " WHERE"
    . "  c.strcompanydisplaycode = " . withQuote($strCompanyDisplayCode)
    ;

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "�������μ����˼���", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0]["lngclosedday"];
}

// ɽ���ѥ桼���������ɤ���桼���������ɤ��������
function fncGetNumericUserCode($strUserDisplayCode, $objDB)
{
    $lngUserCode = fncGetMasterValue("m_user", "struserdisplaycode", "lngusercode", $strUserDisplayCode . ":str", '', $objDB);
    return $lngUserCode;
}

// ��ҥ����ɤ�ɳ�Ť�Ģɼ��ɼ���̤����
function fncGetSlipKindByCompanyCode($lngCompanyCode, $objDB)
{
    $strQuery = ""
        . "SELECT"
        . "  c.lngcompanycode,"
        . "  c.strcompanydisplaycode,"
        . "  c.strcompanydisplayname,"
        . "  sk.lngslipkindcode,"
        . "  sk.strslipkindname,"
        . "  sk.lngmaxline"
        . " FROM m_slipkindrelation skr"
        . "   LEFT JOIN m_slipkind sk ON skr.lngslipkindcode = sk.lngslipkindcode"
        . "   LEFT JOIN m_company c ON skr.lngcompanycode = c.lngcompanycode"
        . " WHERE c.lngcompanycode = " . $lngCompanyCode
    ;

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "Ģɼ��ɼ���̤μ����˼���", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0];
}

// ��ҥ����ɤ�ɳ�Ť���Ҿ�������
function fncGetCompanyInfoByCompanyCode($lngCompanyCode, $objDB)
{
    $strQuery = ""
        . "SELECT "
        . "  c.lngcompanycode,"
        . "  c.strcompanydisplaycode, "
        . "  c.strcompanydisplayname,"
        . "  c.straddress1,"
        . "  c.straddress2,"
        . "  c.straddress3,"
        . "  c.straddress4,"
        . "  c.strtel1,"
        . "  c.strtel2,"
        . "  c.strfax1,"
        . "  c.strfax2,"
        . "  sc.strstockcompanycode,"
        . "  cp.strprintcompanyname,"
        . "  c.strcompanyname,"
        . "  c.bytorganizationfront,"
        . "  o.lngorganizationcode,"
        . "  CASE o.lngorganizationcode WHEN 0 THEN '' ELSE o.strorganizationname END AS strorganizationname"
        . " FROM m_company c"
        . "  LEFT JOIN m_stockcompanycode sc ON c.lngcompanycode = sc.lngcompanyno"
        . "  LEFT JOIN m_companyprintname cp ON c.lngcompanycode = cp.lngcompanycode"
        . "  LEFT JOIN m_organization o ON c.lngorganizationcode = o.lngorganizationcode"
        . " WHERE c.lngcompanycode = " . $lngCompanyCode
    ;
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "��Ҿ���μ����˼���", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0];
}

// �ܵҼ�̾�����
function fncGetCustomerCompanyName($lngCountryCode, $aryCompanyInfo)
{
    if (strlen($aryCompanyInfo["strprintcompanyname"]) != 0) {
        return $aryCompanyInfo["strprintcompanyname"];
    }

    // Ģɼ�Ѳ��̾����
    if ($lngCountryCode != 81) {
        return $aryCompanyInfo["strcompanyname"];
    } else if ($aryCompanyInfo["bytorganizationfront"] == true) {
        return $aryCompanyInfo["strorganizationname"] . $aryCompanyInfo["strcompanyname"];
    } else {
        return $aryCompanyInfo["strcompanyname"] . $aryCompanyInfo["strorganizationname"];
    }
}

// �ܵ�̾�����
function fncGetCustomerName($aryCompanyInfo)
{
    return $aryCompanyInfo["strcompanyname"];
}

// �桼���������ɤ�ɳ�Ť��桼������������
function fncGetUserInfoByUserCode($lngUserCode, $objDB)
{
    $strQuery = ""
        . "SELECT"
        . "  u.lngusercode,"
        . "  u.struserdisplaycode,"
        . "  u.struserdisplayname,"
        . "  gr.lnggroupcode"
        . " FROM m_user u"
        . "  LEFT JOIN (select * from m_grouprelation WHERE bytdefaultflag=TRUE) gr ON u.lngusercode = gr.lngusercode "
        . " WHERE u.lngusercode=" . $lngUserCode
    ;

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "�桼��������μ����˼���", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0];
}

// ����ǡ�����ɳ�Ť������졼�Ȥ����
function fncGetConversionRateByReceiveData($lngReceiveNo, $lngReceiveRevisionNo, $dtmAppropriationDate, $objDB)
{
    $strQuery = ""
        . "SELECT"
        . "  r.lngreceiveno,"
        . "  r.lngmonetaryunitcode,"
        . "  r.lngmonetaryratecode,"
        . "  mr.curconversionrate,"
        . "  mr.dtmapplystartdate,"
        . "  mr.dtmapplyenddate"
        . " FROM m_receive r"
        . "  LEFT JOIN (select distinct * from m_monetaryrate "
        . "             where dtmapplystartdate<='" . $dtmAppropriationDate . "' and '" . $dtmAppropriationDate . "'<=dtmapplyenddate) mr "
        . "   ON r.lngmonetaryunitcode = mr.lngmonetaryunitcode AND r.lngmonetaryratecode = mr.lngmonetaryratecode"
        . " WHERE r.lngreceiveno=" . $lngReceiveNo . " AND r.lngrevisionno = " . $lngReceiveRevisionNo
    ;

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "�����졼�Ȥμ����˼���", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0];
}

// Ǽ�ʽ�NO��ȯ��
function fncPublishSlipCode($dtmPublishDate, $objDB)
{
    $strYYYYMM = substr($dtmPublishDate, 0, 4) . substr($dtmPublishDate, 5, 2);

    $strQuery = ""
        . "SELECT"
        . "  MAX(strslipcode) as yyyymmnn,"
        . "  SUBSTR(MAX(strslipcode),7,8) as nn"
        . " FROM"
        . "  m_slip"
        . " WHERE"
        . "  strslipcode IS NOT NULL "
        . "  AND LENGTH(strslipcode) = 8"
        . "  AND strslipcode LIKE '" . $strYYYYMM . "__'"
    ;

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "Ǽ�ʽ�NO��ȯ�Ԥ˼���", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    // Ǽ�ʽ�NO������
    if ($lngResultNum != 0) {
        $lngNumber = intval($aryResult[0]["nn"]);
        $lngNumber += 1;
    } else {
        // ����1���ܤ� nn='01' �ǳ���
        $lngNumber = 1;
    }

    $strNN = sprintf("%02d", $lngNumber);
    $strPublishdSlipCode = $strYYYYMM . $strNN;

    return $strPublishdSlipCode;
}

// �����ǳۤη׻�
function fncCalcTaxPrice($curPrice, $lngTaxClassCode, $curTax)
{
    $curTaxPrice = 0;

    if ($lngTaxClassCode == "1") {
        // 1:�����
        $curTaxPrice = 0;
    } else if ($lngTaxClassCode == "2") {
        // 2:����
        $curTaxPrice = floor($curPrice * $curTax);
    } else if ($lngTaxClassCode == "3") {
        // 3:����
        $curTaxPrice = floor(($curPrice / (1 + $curTax)) * $curTax);
    }

    return $curTaxPrice;
}

// Ǽ����ɼ�ޥ����Υ�ӥ�����ֹ�κ����ͤ��������
function fncGetSlipMaxRevisionNo($lngSlipNo, $objDB)
{
    $aryQuery = array();
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "  lngslipno ";
    $aryQuery[] = "  , MAX(lngrevisionno) as lngrevisionno";
    $aryQuery[] = " FROM ";
    $aryQuery[] = "  m_slip  ";
    $aryQuery[] = " GROUP BY ";
    $aryQuery[] = "  lngslipno  ";
    $aryQuery[] = " HAVING ";
    $aryQuery[] = "  lngslipno = " . $lngSlipNo;

    $strQuery = "";
    $strQuery .= implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "Ǽ����ɼ�ޥ����Υ�ӥ�����ֹ�����˼���", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0]["lngrevisionno"];
}

// --------------------------------
//
// ����Ǽ�ʽ����Ͽ�ᥤ��ؿ�
//
// --------------------------------
function fncRegisterSalesAndSlip(
    $lngRenewTargetSlipNo, $strRenewTargetSlipCode, $lngRenewTargetSalesNo, $strRenewTargetSalesCode,
    $aryHeader, $aryDetail, $objDB, $objAuth) {
    // ����ͤν����
    $aryRegisterResult = array();
    $aryRegisterResult["result"] = false;
    $aryRegisterResult["aryPerPage"] = array();

    // ��Ͽ���������ʽ����оݤȤʤ�Ǽ����ɼ�ֹ椬���ʤ���Ͽ�����Ǥʤ��ʤ齤����
    $isCreateNew = strlen($lngRenewTargetSlipNo) == 0;

    // ��������
    $dtmNowDate = date('Y/m/d', time());
    // �׾���
    $dtmAppropriationDate = $dtmNowDate;
    // �ܵҤβ�ҥ����ɤ����
    $lngCustomerCompanyCode = fncGetNumericCompanyCode($aryHeader["strcompanydisplaycode"], $objDB);
    // �ܵҤβ�ҥ����ɤ�ɳ�Ť���Ҿ�������
    $aryCustomerCompany = fncGetCompanyInfoByCompanyCode($lngCustomerCompanyCode, $objDB);
    // �����졼�Ȥμ���
    $aryConversionRate = fncGetConversionRateByReceiveData($aryDetail[0]["lngreceiveno"], $aryDetail[0]["lngreceiverevisionno"], $dtmAppropriationDate, $objDB);

    // ��ɼ�Ԥ�ɳ�Ť��桼������������
    if ($aryHeader["strdrafteruserdisplaycode"]) {
        // ��ɼ�Ԥ����Ϥ���Ƥ�����
        $lngDrafterUserCode = fncGetNumericUserCode($aryHeader["strdrafteruserdisplaycode"], $objDB);
        $aryDrafter = fncGetUserInfoByUserCode($lngDrafterUserCode, $objDB);
    } else {
        // ��ɼ�Ԥ�̤���Ϥξ��
        $aryDrafter = array();
        $aryDrafter["lngusercode"] = null;
        $aryDrafter["struserdisplaycode"] = null;
        $aryDrafter["struserdisplayname"] = null;
        $aryDrafter["lnggroupcode"] = null;
    }

    // �ܵҤι񥳡��ɤ����
    $lngCustomerCountryCode = fncGetCountryCode($aryHeader["strcompanydisplaycode"], $objDB);
    // �ܵҼ�̾�μ���
    $strCustomerCompanyName = fncGetCustomerCompanyName($lngCustomerCountryCode, $aryCustomerCompany);
    // �ܵ�̾�μ���
    $strCustomerName = fncGetCustomerName($aryCustomerCompany);
    // Ǽ����β�ҥ����ɤμ���
    if ($aryHeader["strdeliveryplacecompanydisplaycode"]) {
        // Ǽ���褬���Ϥ���Ƥ�����
        $lngDeliveryPlaceCode = fncGetNumericCompanyCode($aryHeader["strdeliveryplacecompanydisplaycode"], $objDB);
    } else {
        // Ǽ���褬̤���Ϥξ��
        $lngDeliveryPlaceCode = null;
    }

    // �ܵҤβ�ҥ����ɤ�ɳ�Ť�Ǽ����ɼ���̤����
    $aryReport = fncGetSlipKindByCompanyCode($lngCustomerCompanyCode, $objDB);
    // �ܵҤ�ɳ�Ť�Ģɼ1�ڡ���������κ������ٿ����������
    $maxItemPerPage = intval($aryReport["lngmaxline"]);
    // ��Ͽ���������٤ο�
    $totalItemCount = count($aryDetail);
    // ����ڡ������η׻�
    $maxPageCount = ceil($totalItemCount / $maxItemPerPage);

    // �ڡ���ñ�̤ǤΥǡ�����Ͽ
    for ($page = 1; $page <= $maxPageCount; $page++) {

        // ���ߤΥڡ�������1�ڡ�������������ٿ����顢
        // ��Ͽ�������٤Υ���ǥå����κǾ��ͤȺ����ͤ����
        $itemMinIndex = ($page - 1) * $maxItemPerPage;
        $itemMaxIndex = $page * $maxItemPerPage - 1;
        if ($itemMaxIndex > $totalItemCount - 1) {
            $itemMaxIndex = $totalItemCount - 1;
        }

        // ��ӥ�����ֹ�
        if ($isCreateNew) {
            // ��Ͽ��0 ����
            $lngRevisionNo = 0;
        } else {
            // ������Ʊ��Ǽ����ɼ�ֹ���Ǥκ����͡ܣ�
            $lngRevisionNo = fncGetSlipMaxRevisionNo($lngRenewTargetSlipNo, $objDB) + 1;
        }

        // ����ֹ�
        if ($isCreateNew) {
            // ��Ͽ���������󥹤��ȯ��
            $lngSalesNo = fncGetSequence('m_sales.lngSalesNo', $objDB);
        } else {
            // �����������оݤ�ɳ�Ť���
            $lngSalesNo = $lngRenewTargetSalesNo;
        }

        // ��女����
        if ($isCreateNew) {
            // ��Ͽ�������ɳ�Ť���女���ɤ�ȯ��
            $strSalesCode = fncGetDateSequence(date('Y', strtotime($dtmNowDate)),
                date('m', strtotime($dtmNowDate)), "m_sales.lngSalesNo", $objDB);
        } else {
            // �����������оݤ�ɳ�Ť���
            $strSalesCode = $strRenewTargetSalesCode;
        }

        // Ǽ����ɼ�ֹ�
        if ($isCreateNew) {
            // ��Ͽ���������󥹤��ȯ��
            $lngSlipNo = fncGetSequence('m_Slip.lngSlipNo', $objDB);
        } else {
            // �����������оݤ�ɳ�Ť���
            $lngSlipNo = $lngRenewTargetSlipNo;
        }

        // Ǽ����ɼ������
        if ($isCreateNew) {
            // ��Ͽ��������ɳ�Ť�Ǽ����ɼ�����ɤ�ȯ��
            $strSlipCode = fncPublishSlipCode($dtmNowDate, $objDB);
        } else {
            // �����������оݤ�ɳ�Ť���
            $strSlipCode = $strRenewTargetSlipCode;
        }

        // �ڡ���ñ�̤ξ������¸�ʸ����Ͽ��̲��̤ǻȤ���
        $aryPageInfo = array();
        $aryPageInfo["lngSlipNo"] = $lngSlipNo;
        $aryPageInfo["strSlipCode"] = $strSlipCode;
        $aryPageInfo["lngRevisionNo"] = $lngRevisionNo;
        $aryRegisterResult["aryPerPage"][] = $aryPageInfo;

        // --------------------------------
        //   �ǡ����١����ѹ�
        // --------------------------------
        // ���ޥ�����Ͽ
        if (!fncRegisterSalesMaster($lngSalesNo, $lngRevisionNo, $strSlipCode, $strSalesCode, $dtmAppropriationDate, $aryConversionRate, $aryCustomerCompany, $aryDrafter,
            $aryHeader, $aryDetail, $objDB, $objAuth)) {
            // ����
            $aryRegisterResult["result"] = false;
            return $aryRegisterResult;
        }

        // ���������Ͽ
        if (!fncRegisterSalesDetail($itemMinIndex, $itemMaxIndex, $lngSalesNo, $lngRevisionNo,
            $aryHeader, $aryDetail, $objDB, $objAuth)) {
            // ����
            $aryRegisterResult["result"] = false;
            return $aryRegisterResult;
        }

        // Ǽ����ɼ�ޥ�����Ͽ
        if (!fncRegisterSlipMaster($lngSlipNo, $lngRevisionNo, $lngSalesNo, $strSlipCode, $strCustomerCompanyName, $strCustomerName, $aryCustomerCompany, $lngDeliveryPlaceCode,
            $aryHeader, $aryDetail, $objDB, $objAuth)) {
            // ����
            $aryRegisterResult["result"] = false;
            return $aryRegisterResult;
        }

        // Ǽ����ɼ������Ͽ
        if (!fncRegisterSlipDetail($itemMinIndex, $itemMaxIndex, $lngSlipNo, $lngRevisionNo,
            $aryHeader, $aryDetail, $objDB, $objAuth)) {
            // ����
            $aryRegisterResult["result"] = false;
            return $aryRegisterResult;
        }

    }

    // ����
    $aryRegisterResult["result"] = true;
    return $aryRegisterResult;
}

// --------------------------------
// �ѥ�᡼���Х�����ѥإ�Ѵؿ�
// --------------------------------
// ���󥰥륯�����ȤǰϤ�
function withQuote($source)
{
    return "'" . $source . "'";
}

// Null���ä���"Null"��Null�ʳ����ä����ͤ򤽤Τޤ��֤�
function nullIfEmpty($source)
{
    if (is_null($source)) {
        return "Null";
    }

    if (strlen($source) == 0) {
        return "Null";
    }

    return $source;
}

// Null���ä���"Null"��Null�ʳ����ä����ͤ򥷥󥰥륯�����ȤǰϤä��֤�
function nullIfEmptyWithQuote($source)
{
    if (is_null($source)) {
        return "Null";
    }

    if (strlen($source) == 0) {
        return "Null";
    }

    return withQuote($source);
}

// ���ޥ�����Ͽ
function fncRegisterSalesMaster($lngSalesNo, $lngRevisionNo, $strSlipCode, $strSalesCode, $dtmAppropriationDate, $aryConversionRate, $aryCustomerCompany, $aryDrafter,
    $aryHeader, $aryDetail, $objDB, $objAuth) {
    // �����졼�Ȥ�����
    if (strlen($aryConversionRate["curconversionrate"]) == 0) {
        $curConversionRate = "Null";
    } else {
        $curConversionRate = $aryConversionRate["curconversionrate"];
    }

    // ��Ͽ�ǡ����Υ��å�
    $v_lngsalesno = $lngSalesNo; //1:����ֹ�
    $v_lngrevisionno = $lngRevisionNo; //2:��ӥ�����ֹ�
    $v_strsalescode = withQuote($strSalesCode); //3:��女����
    $v_dtmappropriationdate = withQuote($dtmAppropriationDate); //4:�׾���
    $v_lngcustomercompanycode = $aryCustomerCompany["lngcompanycode"]; //5:�ܵҥ�����
    $v_lnggroupcode = nullIfEmpty($aryDrafter["lnggroupcode"]); //6:���롼�ץ�����
    $v_lngusercode = nullIfEmpty($aryDrafter["lngusercode"]); //7:�桼��������
    $v_lngsalesstatuscode = "4"; //8:�����֥�����
    $v_lngmonetaryunitcode = $aryDetail[0]["lngmonetaryunitcode"]; //9:�̲�ñ�̥�����
    $v_lngmonetaryratecode = $aryDetail[0]["lngmonetaryratecode"]; //10:�̲ߥ졼�ȥ�����
    $v_curconversionrate = $curConversionRate; //11:�����졼��
    $v_strslipcode = withQuote($strSlipCode); //12:Ǽ�ʽ�NO
    $v_lnginvoiceno = "Null"; //13:������ֹ�
    $v_curtotalprice = $aryHeader["curtotalprice"]; //14:��׶��
    $v_strnote = withQuote($aryHeader["strnote"]); //15:����
    $v_lnginputusercode = $objAuth->UserCode; //16:���ϼԥ�����
    $v_bytinvalidflag = "FALSE"; //17:̵���ե饰
    $v_dtminsertdate = "now()"; //18:��Ͽ��

    // ��Ͽ���������
    $aryInsert = [];
    $aryInsert[] = "INSERT  ";
    $aryInsert[] = "INTO m_sales(  ";
    $aryInsert[] = "  lngsalesno "; //1:����ֹ�
    $aryInsert[] = "  , lngrevisionno "; //2:��ӥ�����ֹ�
    $aryInsert[] = "  , strsalescode "; //3:��女����
    $aryInsert[] = "  , dtmappropriationdate "; //4:�׾���
    $aryInsert[] = "  , lngcustomercompanycode "; //5:�ܵҥ�����
    $aryInsert[] = "  , lnggroupcode "; //6:���롼�ץ�����
    $aryInsert[] = "  , lngusercode "; //7:�桼��������
    $aryInsert[] = "  , lngsalesstatuscode "; //8:�����֥�����
    $aryInsert[] = "  , lngmonetaryunitcode "; //9:�̲�ñ�̥�����
    $aryInsert[] = "  , lngmonetaryratecode "; //10:�̲ߥ졼�ȥ�����
    $aryInsert[] = "  , curconversionrate "; //11:�����졼��
    $aryInsert[] = "  , strslipcode "; //12:Ǽ�ʽ�NO
    $aryInsert[] = "  , lnginvoiceno "; //13:������ֹ�
    $aryInsert[] = "  , curtotalprice "; //14:��׶��
    $aryInsert[] = "  , strnote "; //15:����
    $aryInsert[] = "  , lnginputusercode "; //16:���ϼԥ�����
    $aryInsert[] = "  , bytinvalidflag "; //17:̵���ե饰
    $aryInsert[] = "  , dtminsertdate "; //18:��Ͽ��
    $aryInsert[] = ")  ";
    $aryInsert[] = "VALUES (  ";
    $aryInsert[] = "  " . $v_lngsalesno; //1:����ֹ�
    $aryInsert[] = " ," . $v_lngrevisionno; //2:��ӥ�����ֹ�
    $aryInsert[] = " ," . $v_strsalescode; //3:��女����
    $aryInsert[] = " ," . $v_dtmappropriationdate; //4:�׾���
    $aryInsert[] = " ," . $v_lngcustomercompanycode; //5:�ܵҥ�����
    $aryInsert[] = " ," . $v_lnggroupcode; //6:���롼�ץ�����
    $aryInsert[] = " ," . $v_lngusercode; //7:�桼��������
    $aryInsert[] = " ," . $v_lngsalesstatuscode; //8:�����֥�����
    $aryInsert[] = " ," . $v_lngmonetaryunitcode; //9:�̲�ñ�̥�����
    $aryInsert[] = " ," . $v_lngmonetaryratecode; //10:�̲ߥ졼�ȥ�����
    $aryInsert[] = " ," . $v_curconversionrate; //11:�����졼��
    $aryInsert[] = " ," . $v_strslipcode; //12:Ǽ�ʽ�NO
    $aryInsert[] = " ," . $v_lnginvoiceno; //13:������ֹ�
    $aryInsert[] = " ," . $v_curtotalprice; //14:��׶��
    $aryInsert[] = " ," . $v_strnote; //15:����
    $aryInsert[] = " ," . $v_lnginputusercode; //16:���ϼԥ�����
    $aryInsert[] = " ," . $v_bytinvalidflag; //17:̵���ե饰
    $aryInsert[] = " ," . $v_dtminsertdate; //18:��Ͽ��
    $aryInsert[] = ") ";
    $strQuery = "";
    $strQuery .= implode("\n", $aryInsert);

    // ��Ͽ�¹�
    if (!$lngResultID = $objDB->execute($strQuery)) {
        fncOutputError(9051, DEF_ERROR, "���ޥ�����Ͽ���ԡ�", true, "", $objDB);
        // ����
        return false;
    }
    $objDB->freeResult($lngResultID);

    // ����
    return true;
}

// ���������Ͽ
function fncRegisterSalesDetail($itemMinIndex, $itemMaxIndex, $lngSalesNo, $lngRevisionNo,
    $aryHeader, $aryDetail, $objDB, $objAuth) {
    // ������Ψ
    $curTax = floatval($aryHeader["curtax"]);
    // �����Ƕ�ʬ
    $lngTaxClassCode = $aryHeader["lngtaxclasscode"];

    for ($i = $itemMinIndex; $i <= $itemMaxIndex; $i++) {
        $d = $aryDetail[$i];

        // ����ñ�̤Ǥξ����Ƕ�ۤη׻�
        $curTaxPrice = fncCalcTaxPrice($d["cursubtotalprice"], $lngTaxClassCode, $curTax);

        // ��Ͽ�ǡ����Υ��å�
        $v_lngsalesno = $lngSalesNo; //1:����ֹ�
        $v_lngsalesdetailno = $d["rownumber"]; //2:��������ֹ�
        $v_lngrevisionno = $lngRevisionNo; //3:��ӥ�����ֹ�
        $v_strproductcode = withQuote(mb_substr($d["strproductcode"], 0, 5)); //4:���ʥ�����
        $v_strrevisecode = withQuote($d["strrevisecode"]); //5:���Υ�����
        $v_lngsalesclasscode = $d["lngsalesclasscode"]; //6:����ʬ������
        $v_lngconversionclasscode = "Null"; //7:������ʬ������
        $v_lngquantity = $d["lngunitquantity"]; //8:����
        $v_curproductprice = $d["curproductprice"]; //9:���ʲ���
        $v_lngproductquantity = $d["lngproductquantity"]; //10:���ʿ���
        $v_lngproductunitcode = $d["lngproductunitcode"]; //11:����ñ�̥�����
        $v_lngtaxclasscode = $aryHeader["lngtaxclasscode"]; //12:�����Ƕ�ʬ������
        $v_lngtaxcode = nullIfEmpty($aryHeader["lngtaxcode"]); //13:������Ψ������
        $v_curtaxprice = $curTaxPrice; //14:�����Ƕ��
        $v_cursubtotalprice = $d["cursubtotalprice"]; //15:���׶��
        $v_strnote = withQuote($d["strnote"]); //16:����
        $v_lngsortkey = $d["rownumber"]; //17:ɽ���ѥ����ȥ���
        $v_lngreceiveno = $d["lngreceiveno"]; //18:�����ֹ�
        $v_lngreceivedetailno = $d["lngreceivedetailno"]; //19:���������ֹ�
        $v_lngreceiverevisionno = $d["lngreceiverevisionno"]; //20:�����ӥ�����ֹ�

        // ��Ͽ���������
        $aryInsert = [];
        $aryInsert[] = "INSERT  ";
        $aryInsert[] = "INTO t_salesdetail(  ";
        $aryInsert[] = "  lngsalesno "; //1:����ֹ�
        $aryInsert[] = "  , lngsalesdetailno "; //2:��������ֹ�
        $aryInsert[] = "  , lngrevisionno "; //3:��ӥ�����ֹ�
        $aryInsert[] = "  , strproductcode "; //4:���ʥ�����
        $aryInsert[] = "  , strrevisecode "; //5:���Υ�����
        $aryInsert[] = "  , lngsalesclasscode "; //6:����ʬ������
        $aryInsert[] = "  , lngconversionclasscode "; //7:������ʬ������
        $aryInsert[] = "  , lngquantity "; //8:����
        $aryInsert[] = "  , curproductprice "; //9:���ʲ���
        $aryInsert[] = "  , lngproductquantity "; //10:���ʿ���
        $aryInsert[] = "  , lngproductunitcode "; //11:����ñ�̥�����
        $aryInsert[] = "  , lngtaxclasscode "; //12:�����Ƕ�ʬ������
        $aryInsert[] = "  , lngtaxcode "; //13:������Ψ������
        $aryInsert[] = "  , curtaxprice "; //14:�����Ƕ��
        $aryInsert[] = "  , cursubtotalprice "; //15:���׶��
        $aryInsert[] = "  , strnote "; //16:����
        $aryInsert[] = "  , lngsortkey "; //17:ɽ���ѥ����ȥ���
        $aryInsert[] = "  , lngreceiveno "; //18:�����ֹ�
        $aryInsert[] = "  , lngreceivedetailno "; //19:���������ֹ�
        $aryInsert[] = "  , lngreceiverevisionno "; //20:�����ӥ�����ֹ�
        $aryInsert[] = ")  ";
        $aryInsert[] = "VALUES (  ";
        $aryInsert[] = "  " . $v_lngsalesno; //1:����ֹ�
        $aryInsert[] = " ," . $v_lngsalesdetailno; //2:��������ֹ�
        $aryInsert[] = " ," . $v_lngrevisionno; //3:��ӥ�����ֹ�
        $aryInsert[] = " ," . $v_strproductcode; //4:���ʥ�����
        $aryInsert[] = " ," . $v_strrevisecode; //5:���Υ�����
        $aryInsert[] = " ," . $v_lngsalesclasscode; //6:����ʬ������
        $aryInsert[] = " ," . $v_lngconversionclasscode; //7:������ʬ������
        $aryInsert[] = " ," . $v_lngquantity; //8:����
        $aryInsert[] = " ," . $v_curproductprice; //9:���ʲ���
        $aryInsert[] = " ," . $v_lngproductquantity; //10:���ʿ���
        $aryInsert[] = " ," . $v_lngproductunitcode; //11:����ñ�̥�����
        $aryInsert[] = " ," . $v_lngtaxclasscode; //12:�����Ƕ�ʬ������
        $aryInsert[] = " ," . $v_lngtaxcode; //13:������Ψ������
        $aryInsert[] = " ," . $v_curtaxprice; //14:�����Ƕ��
        $aryInsert[] = " ," . $v_cursubtotalprice; //15:���׶��
        $aryInsert[] = " ," . $v_strnote; //16:����
        $aryInsert[] = " ," . $v_lngsortkey; //17:ɽ���ѥ����ȥ���
        $aryInsert[] = " ," . $v_lngreceiveno; //18:�����ֹ�
        $aryInsert[] = " ," . $v_lngreceivedetailno; //19:���������ֹ�
        $aryInsert[] = " ," . $v_lngreceiverevisionno; //20:�����ӥ�����ֹ�
        $aryInsert[] = ") ";
        $strQuery = "";
        $strQuery .= implode("\n", $aryInsert);

        // ��Ͽ�¹�
        if (!$lngResultID = $objDB->execute($strQuery)) {
            fncOutputError(9051, DEF_ERROR, "���������Ͽ���ԡ�", true, "", $objDB);
            // ����
            return false;
        }
        $objDB->freeResult($lngResultID);
    }

    // ����
    return true;
}

// Ǽ����ɼ�ޥ�����Ͽ
function fncRegisterSlipMaster($lngSlipNo, $lngRevisionNo, $lngSalesNo, $strSlipCode, $strCustomerCompanyName, $strCustomerName, $aryCustomerCompany, $lngDeliveryPlaceCode,
    $aryHeader, $aryDetail, $objDB, $objAuth) {
    // �����襳���ɤμ����ʶ��ξ�������Ū��Null�򥻥åȡ�
    if (strlen($aryCustomerCompany["strstockcompanycode"]) != 0) {
        $strShipperCode = withQuote($aryCustomerCompany["strstockcompanycode"]);
    } else {
        $strShipperCode = "Null";
    }

    if (strlen($aryHeader["dtmpaymentlimit"]) != 0) {
        $dtmPaymentLimit = withQuote($aryHeader["dtmpaymentlimit"]);
    } else {
        $dtmPaymentLimit = "Null";
    }

    // ��Ͽ�ǡ����Υ��å�
    $v_lngslipno = $lngSlipNo; //1:Ǽ����ɼ�ֹ�
    $v_lngrevisionno = $lngRevisionNo; //2:��ӥ�����ֹ�
    $v_strslipcode = withQuote($strSlipCode); //3:Ǽ����ɼ������
    $v_lngsalesno = $lngSalesNo; //4:����ֹ�
    $v_lngcustomercode = nullIfEmpty($aryCustomerCompany["lngcompanycode"]); //5:�ܵҥ�����
    $v_strcustomercompanyname = withQuote($strCustomerCompanyName); //6:�ܵҼ�̾
    $v_strcustomername = withQuote($strCustomerName); //7:�ܵ�̾
    $v_strcustomeraddress1 = nullIfEmptyWithQuote($aryCustomerCompany["straddress1"]); //8:�ܵҽ���1
    $v_strcustomeraddress2 = nullIfEmptyWithQuote($aryCustomerCompany["straddress2"]); //9:�ܵҽ���2
    $v_strcustomeraddress3 = nullIfEmptyWithQuote($aryCustomerCompany["straddress3"]); //10:�ܵҽ���3
    $v_strcustomeraddress4 = nullIfEmptyWithQuote($aryCustomerCompany["straddress4"]); //11:�ܵҽ���4
    $v_strcustomerphoneno = nullIfEmptyWithQuote($aryCustomerCompany["strtel1"]); //12:�ܵ������ֹ�
    $v_strcustomerfaxno = nullIfEmptyWithQuote($aryCustomerCompany["strfax1"]); //13:�ܵ�FAX�ֹ�
    $v_strcustomerusername = withQuote($aryHeader["strcustomerusername"]); //14:�ܵ�ô����̾
    $v_strshippercode = $strShipperCode; //15:�����襳���ɡʽвټԡ�
    $v_dtmdeliverydate = withQuote($aryHeader["dtmdeliverydate"]); //16:Ǽ����
    $v_lngdeliveryplacecode = nullIfEmpty($lngDeliveryPlaceCode); //17:Ǽ�ʾ�ꥳ����
    $v_strdeliveryplacename = withQuote($aryHeader["strdeliveryplacename"]); //18:Ǽ�ʾ��̾
    $v_strdeliveryplaceusername = withQuote($aryHeader["strdeliveryplaceusername"]); //19:Ǽ�ʾ��ô����̾
    $v_lngpaymentmethodcode = $aryHeader["lngpaymentmethodcode"]; //20:��ʧ��ˡ������
    $v_dtmpaymentlimit = $dtmPaymentLimit; //21:��ʧ����
    $v_lngtaxclasscode = $aryHeader["lngtaxclasscode"]; //22:���Ƕ�ʬ������
    $v_strtaxclassname = withQuote($aryHeader["strtaxclassname"]); //23:���Ƕ�ʬ
    $v_curtax = $aryHeader["curtax"]; //24:������Ψ
    $v_lngusercode = nullIfEmpty($aryHeader["lngdrafterusercode"]); //25:ô���ԥ�����
    $v_strusername = withQuote($aryHeader["strdrafteruserdisplayname"]); //26:ô����̾
    $v_curtotalprice = $aryHeader["curtotalprice"]; //27:��׶��
    $v_lngmonetaryunitcode = $aryDetail[0]["lngmonetaryunitcode"]; //28:�̲�ñ�̥�����
    $v_strmonetaryunitsign = withQuote($aryDetail[0]["strmonetaryunitsign"]); //29:�̲�ñ��
    $v_dtminsertdate = "now()"; //30:������
    $v_lnginsertusercode = nullIfEmpty($objAuth->UserCode); //31:���ϼԥ�����
    $v_strinsertusername = withQuote($objAuth->UserDisplayName); //32:���ϼ�̾
    $v_strnote = withQuote($aryHeader["strnote"]); //33:����
    $v_lngprintcount = 0; //34:�������
    $v_bytinvalidflag = "FALSE"; //35:̵���ե饰

    // ��Ͽ���������
    $aryInsert = [];
    $aryInsert[] = "INSERT  ";
    $aryInsert[] = "INTO m_slip(  ";
    $aryInsert[] = "  lngslipno "; //1:Ǽ����ɼ�ֹ�
    $aryInsert[] = "  , lngrevisionno "; //2:��ӥ�����ֹ�
    $aryInsert[] = "  , strslipcode "; //3:Ǽ����ɼ������
    $aryInsert[] = "  , lngsalesno "; //4:����ֹ�
    $aryInsert[] = "  , lngcustomercode "; //5:�ܵҥ�����
    $aryInsert[] = "  , strcustomercompanyname "; //6:�ܵҼ�̾
    $aryInsert[] = "  , strcustomername "; //7:�ܵ�̾
    $aryInsert[] = "  , strcustomeraddress1 "; //8:�ܵҽ���1
    $aryInsert[] = "  , strcustomeraddress2 "; //9:�ܵҽ���2
    $aryInsert[] = "  , strcustomeraddress3 "; //10:�ܵҽ���3
    $aryInsert[] = "  , strcustomeraddress4 "; //11:�ܵҽ���4
    $aryInsert[] = "  , strcustomerphoneno "; //12:�ܵ������ֹ�
    $aryInsert[] = "  , strcustomerfaxno "; //13:�ܵ�FAX�ֹ�
    $aryInsert[] = "  , strcustomerusername "; //14:�ܵ�ô����̾
    $aryInsert[] = "  , strshippercode "; //15:�����襳���ɡʽвټԡ�
    $aryInsert[] = "  , dtmdeliverydate "; //16:Ǽ����
    $aryInsert[] = "  , lngdeliveryplacecode "; //17:Ǽ�ʾ�ꥳ����
    $aryInsert[] = "  , strdeliveryplacename "; //18:Ǽ�ʾ��̾
    $aryInsert[] = "  , strdeliveryplaceusername "; //19:Ǽ�ʾ��ô����̾
    $aryInsert[] = "  , lngpaymentmethodcode "; //20:��ʧ��ˡ������
    $aryInsert[] = "  , dtmpaymentlimit "; //21:��ʧ����
    $aryInsert[] = "  , lngtaxclasscode "; //22:���Ƕ�ʬ������
    $aryInsert[] = "  , strtaxclassname "; //23:���Ƕ�ʬ
    $aryInsert[] = "  , curtax "; //24:������Ψ
    $aryInsert[] = "  , lngusercode "; //25:ô���ԥ�����
    $aryInsert[] = "  , strusername "; //26:ô����̾
    $aryInsert[] = "  , curtotalprice "; //27:��׶��
    $aryInsert[] = "  , lngmonetaryunitcode "; //28:�̲�ñ�̥�����
    $aryInsert[] = "  , strmonetaryunitsign "; //29:�̲�ñ��
    $aryInsert[] = "  , dtminsertdate "; //30:������
    $aryInsert[] = "  , lnginsertusercode "; //31:���ϼԥ�����
    $aryInsert[] = "  , strinsertusername "; //32:���ϼ�̾
    $aryInsert[] = "  , strnote "; //33:����
    $aryInsert[] = "  , lngprintcount "; //34:�������
    $aryInsert[] = "  , bytinvalidflag "; //35:̵���ե饰
    $aryInsert[] = ")  ";
    $aryInsert[] = "VALUES (  ";
    $aryInsert[] = "  " . $v_lngslipno; //1:Ǽ����ɼ�ֹ�
    $aryInsert[] = " ," . $v_lngrevisionno; //2:��ӥ�����ֹ�
    $aryInsert[] = " ," . $v_strslipcode; //3:Ǽ����ɼ������
    $aryInsert[] = " ," . $v_lngsalesno; //4:����ֹ�
    $aryInsert[] = " ," . $v_lngcustomercode; //5:�ܵҥ�����
    $aryInsert[] = " ," . $v_strcustomercompanyname; //6:�ܵҼ�̾
    $aryInsert[] = " ," . $v_strcustomername; //7:�ܵ�̾
    $aryInsert[] = " ," . $v_strcustomeraddress1; //8:�ܵҽ���1
    $aryInsert[] = " ," . $v_strcustomeraddress2; //9:�ܵҽ���2
    $aryInsert[] = " ," . $v_strcustomeraddress3; //10:�ܵҽ���3
    $aryInsert[] = " ," . $v_strcustomeraddress4; //11:�ܵҽ���4
    $aryInsert[] = " ," . $v_strcustomerphoneno; //12:�ܵ������ֹ�
    $aryInsert[] = " ," . $v_strcustomerfaxno; //13:�ܵ�FAX�ֹ�
    $aryInsert[] = " ," . $v_strcustomerusername; //14:�ܵ�ô����̾
    $aryInsert[] = " ," . $v_strshippercode; //15:�����襳���ɡʽвټԡ�
    $aryInsert[] = " ," . $v_dtmdeliverydate; //16:Ǽ����
    $aryInsert[] = " ," . $v_lngdeliveryplacecode; //17:Ǽ�ʾ�ꥳ����
    $aryInsert[] = " ," . $v_strdeliveryplacename; //18:Ǽ�ʾ��̾
    $aryInsert[] = " ," . $v_strdeliveryplaceusername; //19:Ǽ�ʾ��ô����̾
    $aryInsert[] = " ," . $v_lngpaymentmethodcode; //20:��ʧ��ˡ������
    $aryInsert[] = " ," . $v_dtmpaymentlimit; //21:��ʧ����
    $aryInsert[] = " ," . $v_lngtaxclasscode; //22:���Ƕ�ʬ������
    $aryInsert[] = " ," . $v_strtaxclassname; //23:���Ƕ�ʬ
    $aryInsert[] = " ," . $v_curtax; //24:������Ψ
    $aryInsert[] = " ," . $v_lngusercode; //25:ô���ԥ�����
    $aryInsert[] = " ," . $v_strusername; //26:ô����̾
    $aryInsert[] = " ," . $v_curtotalprice; //27:��׶��
    $aryInsert[] = " ," . $v_lngmonetaryunitcode; //28:�̲�ñ�̥�����
    $aryInsert[] = " ," . $v_strmonetaryunitsign; //29:�̲�ñ��
    $aryInsert[] = " ," . $v_dtminsertdate; //30:������
    $aryInsert[] = " ," . $v_lnginsertusercode; //31:���ϼԥ�����
    $aryInsert[] = " ," . $v_strinsertusername; //32:���ϼ�̾
    $aryInsert[] = " ," . $v_strnote; //33:����
    $aryInsert[] = " ," . $v_lngprintcount; //34:�������
    $aryInsert[] = " ," . $v_bytinvalidflag; //35:̵���ե饰
    $aryInsert[] = ") ";
    $strQuery = "";
    $strQuery .= implode("\n", $aryInsert);

    // ��Ͽ�¹�
    if (!$lngResultID = $objDB->execute($strQuery)) {
        fncOutputError(9051, DEF_ERROR, "Ǽ����ɼ�ޥ�����Ͽ���ԡ�", true, "", $objDB);
        // ����
        return false;
    }
    $objDB->freeResult($lngResultID);

    // ����
    return true;
}

// Ǽ����ɼ������Ͽ
function fncRegisterSlipDetail($itemMinIndex, $itemMaxIndex, $lngSlipNo, $lngRevisionNo,
    $aryHeader, $aryDetail, $objDB, $objAuth) {
    for ($i = $itemMinIndex; $i <= $itemMaxIndex; $i++) {
        $d = $aryDetail[$i];

        // ��Ͽ�ǡ����Υ��å�
        $v_lngslipno = $lngSlipNo; //1:Ǽ����ɼ�ֹ�
        $v_lngslipdetailno = $d["rownumber"]; //2:Ǽ����ɼ�����ֹ�
        $v_lngrevisionno = $lngRevisionNo; //3:��ӥ�����ֹ�
        $v_strcustomersalescode = withQuote($d["strcustomerreceivecode"]); //4:�ܵҼ����ֹ�
        $v_lngsalesclasscode = $d["lngsalesclasscode"]; //5:����ʬ������
        $v_strsalesclassname = withQuote($d["strsalesclassname"]); //6:����ʬ̾
        $v_strgoodscode = withQuote($d["strgoodscode"]); //7:�ܵ�����
        $v_strproductcode = withQuote(mb_substr($d["strproductcode"], 0, 5)); //8:���ʥ�����
        $v_strrevisecode = withQuote($d["strrevisecode"]); //9:���Υ�����
        $v_strproductname = withQuote($d["strproductname"]); //10:����̾
        $v_strproductenglishname = withQuote($d["strproductenglishname"]); //11:����̾�ʱѸ��
        $v_curproductprice = $d["curproductprice"]; //12:ñ��
        $v_lngquantity = $d["lngunitquantity"]; //13:����
        $v_lngproductquantity = $d["lngproductquantity"]; //14:����
        $v_lngproductunitcode = $d["lngproductunitcode"]; //15:����ñ�̥�����
        $v_strproductunitname = withQuote($d["strproductunitname"]); //16:����ñ��̾
        $v_cursubtotalprice = $d["cursubtotalprice"]; //17:����
        $v_strnote = withQuote($d["strnote"]); //18:��������
        $v_lngreceiveno = $d["lngreceiveno"]; //19:�����ֹ�
        $v_lngreceivedetailno = $d["lngreceivedetailno"]; //20:���������ֹ�
        $v_lngreceiverevisionno = $d["lngreceiverevisionno"]; //21:�����ӥ�����ֹ�
        $v_lngsortkey = $d["rownumber"]; //22:ɽ���ѥ����ȥ���

        // ��Ͽ���������
        $aryInsert = [];
        $aryInsert[] = "INSERT  ";
        $aryInsert[] = "INTO t_slipdetail(  ";
        $aryInsert[] = "  lngslipno "; //1:Ǽ����ɼ�ֹ�
        $aryInsert[] = "  , lngslipdetailno "; //2:Ǽ����ɼ�����ֹ�
        $aryInsert[] = "  , lngrevisionno "; //3:��ӥ�����ֹ�
        $aryInsert[] = "  , strcustomersalescode "; //4:�ܵҼ����ֹ�
        $aryInsert[] = "  , lngsalesclasscode "; //5:����ʬ������
        $aryInsert[] = "  , strsalesclassname "; //6:����ʬ̾
        $aryInsert[] = "  , strgoodscode "; //7:�ܵ�����
        $aryInsert[] = "  , strproductcode "; //8:���ʥ�����
        $aryInsert[] = "  , strrevisecode "; //9:���Υ�����
        $aryInsert[] = "  , strproductname "; //10:����̾
        $aryInsert[] = "  , strproductenglishname "; //11:����̾�ʱѸ��
        $aryInsert[] = "  , curproductprice "; //12:ñ��
        $aryInsert[] = "  , lngquantity "; //13:����
        $aryInsert[] = "  , lngproductquantity "; //14:����
        $aryInsert[] = "  , lngproductunitcode "; //15:����ñ�̥�����
        $aryInsert[] = "  , strproductunitname "; //16:����ñ��̾
        $aryInsert[] = "  , cursubtotalprice "; //17:����
        $aryInsert[] = "  , strnote "; //18:��������
        $aryInsert[] = "  , lngreceiveno "; //19:�����ֹ�
        $aryInsert[] = "  , lngreceivedetailno "; //20:���������ֹ�
        $aryInsert[] = "  , lngreceiverevisionno "; //21:�����ӥ�����ֹ�
        $aryInsert[] = "  , lngsortkey "; //22:ɽ���ѥ����ȥ���
        $aryInsert[] = ")  ";
        $aryInsert[] = "VALUES (  ";
        $aryInsert[] = "  " . $v_lngslipno; //1:Ǽ����ɼ�ֹ�
        $aryInsert[] = " ," . $v_lngslipdetailno; //2:Ǽ����ɼ�����ֹ�
        $aryInsert[] = " ," . $v_lngrevisionno; //3:��ӥ�����ֹ�
        $aryInsert[] = " ," . $v_strcustomersalescode; //4:�ܵҼ����ֹ�
        $aryInsert[] = " ," . $v_lngsalesclasscode; //5:����ʬ������
        $aryInsert[] = " ," . $v_strsalesclassname; //6:����ʬ̾
        $aryInsert[] = " ," . $v_strgoodscode; //7:�ܵ�����
        $aryInsert[] = " ," . $v_strproductcode; //8:���ʥ�����
        $aryInsert[] = " ," . $v_strrevisecode; //9:���Υ�����
        $aryInsert[] = " ," . $v_strproductname; //10:����̾
        $aryInsert[] = " ," . $v_strproductenglishname; //11:����̾�ʱѸ��
        $aryInsert[] = " ," . $v_curproductprice; //12:ñ��
        $aryInsert[] = " ," . $v_lngquantity; //13:����
        $aryInsert[] = " ," . $v_lngproductquantity; //14:����
        $aryInsert[] = " ," . $v_lngproductunitcode; //15:����ñ�̥�����
        $aryInsert[] = " ," . $v_strproductunitname; //16:����ñ��̾
        $aryInsert[] = " ," . $v_cursubtotalprice; //17:����
        $aryInsert[] = " ," . $v_strnote; //18:��������
        $aryInsert[] = " ," . $v_lngreceiveno; //19:�����ֹ�
        $aryInsert[] = " ," . $v_lngreceivedetailno; //20:���������ֹ�
        $aryInsert[] = " ," . $v_lngreceiverevisionno; //21:�����ӥ�����ֹ�
        $aryInsert[] = " ," . $v_lngsortkey; //22:ɽ���ѥ����ȥ���
        $aryInsert[] = ") ";
        $strQuery = "";
        $strQuery .= implode("\n", $aryInsert);

        // ��Ͽ�¹�
        if (!$lngResultID = $objDB->execute($strQuery)) {
            fncOutputError(9051, DEF_ERROR, "Ǽ����ɼ������Ͽ��Ͽ���ԡ�", true, "", $objDB);
            // ����
            return false;
        }
        $objDB->freeResult($lngResultID);
    }

    // ����
    return true;
}

// ʸ�������� EUC-JP -> UTF-8 �Ѵ��ѥإ�Ѵؿ�
function fncToUtf8($eucjpText)
{
    return mb_convert_encoding($eucjpText, 'UTF-8', 'EUC-JP');
}

// ʸ�������� UTF-8 -> EUC-JP �Ѵ��ѥإ�Ѵؿ�
function fncToEucjp($utf8Text)
{
    return mb_convert_encoding($utf8Text, 'EUC-JP', 'UTF-8');
}

// ���ꥢ�ɥ쥹�Υ�����ͤ򥻥åȤ���إ�Ѵؿ�
function setCellValue($xlWorkSheet, $address, $value)
{
    $value = fncToUtf8($value);
    $xlWorkSheet->GetCell($address)->SetValue($value);
}

// �Ԥ��Ѥ��ʤ��饻����ͤ򥻥åȤ���إ�Ѵؿ�
function setCellDetailValue($xlWorkSheet, $columnAddress, $rowNumber, $value)
{
    $address = $columnAddress . $rowNumber;
    $value = fncToUtf8($value);
    $xlWorkSheet->GetCell($address)->SetValue($value);
}

function fncConvertArrayHeaderToEucjp($aryHeader)
{
    $aryHeader["strdrafteruserdisplaycode"] = fncToEucjp($aryHeader["strdrafteruserdisplaycode"]);
    $aryHeader["strdrafteruserdisplayname"] = fncToEucjp($aryHeader["strdrafteruserdisplayname"]);
    $aryHeader["strcompanydisplaycode"] = fncToEucjp($aryHeader["strcompanydisplaycode"]);
    $aryHeader["strcompanydisplayname"] = fncToEucjp($aryHeader["strcompanydisplayname"]);
    $aryHeader["strcustomerusername"] = fncToEucjp($aryHeader["strcustomerusername"]);
    $aryHeader["dtmdeliverydate"] = fncToEucjp($aryHeader["dtmdeliverydate"]);
    $aryHeader["strdeliveryplacecompanydisplaycode"] = fncToEucjp($aryHeader["strdeliveryplacecompanydisplaycode"]);
    $aryHeader["strdeliveryplacename"] = fncToEucjp($aryHeader["strdeliveryplacename"]);
    $aryHeader["strdeliveryplaceusername"] = fncToEucjp($aryHeader["strdeliveryplaceusername"]);
    $aryHeader["strnote"] = fncToEucjp($aryHeader["strnote"]);
    $aryHeader["lngtaxclasscode"] = fncToEucjp($aryHeader["lngtaxclasscode"]);
    $aryHeader["strtaxclassname"] = fncToEucjp($aryHeader["strtaxclassname"]);
    $aryHeader["lngtaxcode"] = fncToEucjp($aryHeader["lngtaxcode"]);
    $aryHeader["curtax"] = fncToEucjp($aryHeader["curtax"]);
    $aryHeader["strtaxamount"] = fncToEucjp($aryHeader["strtaxamount"]);
    $aryHeader["dtmpaymentlimit"] = fncToEucjp($aryHeader["dtmpaymentlimit"]);
    $aryHeader["lngpaymentmethodcode"] = fncToEucjp($aryHeader["lngpaymentmethodcode"]);
    $aryHeader["curtotalprice"] = fncToEucjp($aryHeader["curtotalprice"]);

    return $aryHeader;
}

function fncConvertArrayDetailToEucjp($aryDetail)
{
    for ($i = 0; $i < count($aryDetail); $i++) {
        $d = &$aryDetail[$i];

        $d["rownumber"] = fncToEucjp($d["rownumber"]);
        $d["strcustomerreceivecode"] = fncToEucjp($d["strcustomerreceivecode"]);
        $d["strreceivecode"] = fncToEucjp($d["strreceivecode"]);
        $d["strgoodscode"] = fncToEucjp($d["strgoodscode"]);
        $d["strproductcode"] = fncToEucjp($d["strproductcode"]);
        $d["strproductname"] = fncToEucjp($d["strproductname"]);
        $d["strproductenglishname"] = fncToEucjp($d["strproductenglishname"]);
        $d["strsalesdeptname"] = fncToEucjp($d["strsalesdeptname"]);
        $d["strsalesclassname"] = fncToEucjp($d["strsalesclassname"]);
        $d["dtmdeliverydate"] = fncToEucjp($d["dtmdeliverydate"]);
        $d["lngunitquantity"] = fncToEucjp($d["lngunitquantity"]);
        $d["curproductprice"] = fncToEucjp($d["curproductprice"]);
        $d["strproductunitname"] = fncToEucjp($d["strproductunitname"]);
        $d["lngproductquantity"] = fncToEucjp($d["lngproductquantity"]);
        $d["cursubtotalprice"] = fncToEucjp($d["cursubtotalprice"]);
        $d["lngreceiveno"] = fncToEucjp($d["lngreceiveno"]);
        $d["lngreceivedetailno"] = fncToEucjp($d["lngreceivedetailno"]);
        $d["lngreceiverevisionno"] = fncToEucjp($d["lngreceiverevisionno"]);
        $d["strrevisecode"] = fncToEucjp($d["strrevisecode"]);
        $d["lngsalesclasscode"] = fncToEucjp($d["lngsalesclasscode"]);
        $d["lngproductunitcode"] = fncToEucjp($d["lngproductunitcode"]);
        $d["strnote"] = fncToEucjp($d["strnote"]);
        $d["lngmonetaryunitcode"] = fncToEucjp($d["lngmonetaryunitcode"]);
        $d["lngmonetaryratecode"] = fncToEucjp($d["lngmonetaryratecode"]);
        $d["strmonetaryunitsign"] = fncToEucjp($d["strmonetaryunitsign"]);
    }

    return $aryDetail;
}

/**
 * �ƥ�ץ졼�Ȥ���Ģɼ���᡼������������
 * @param  string  $strMode     ư��⡼�ɡ�"html"->�ץ�ӥ塼��HTML������"download"->�����������Writer����
 * @param  array   $aryHeader   �إå����ǡ���
 * @param  array   $aryDetail   �������ǡ���
 * @param  object  $objDB       �ǡ����١������饹
 * @return array   $$aryGenerateResult  ������̡�ư��⡼�ɤˤ���Ǽ������ͤ��ۤʤ�
 */
function fncGenerateReportImage($strMode, $aryHeader, $aryDetail,
    $lngSlipNo, $lngRevisionNo, $strSlipCode, $lngSalesNo, $dtmInsertDate,
    $objDB) {
    // ������̡�����͡�
    $aryGenerateResult = array();

    // DBG:��������ȥ������о�
    // --------------------------------------------
    //  �ǡ�������
    // --------------------------------------------
    // �ܵҤβ�ҥ����ɤ����
    $lngCustomerCompanyCode = fncGetNumericCompanyCode($aryHeader["strcompanydisplaycode"], $objDB);
    // �ܵҤβ�ҥ����ɤ�ɳ�Ť�Ǽ����ɼ���̤����
    $aryReport = fncGetSlipKindByCompanyCode($lngCustomerCompanyCode, $objDB);
    // �ܵҤβ�ҥ����ɤ�ɳ�Ť���Ҿ�������
    $aryCustomerCompany = fncGetCompanyInfoByCompanyCode($lngCustomerCompanyCode, $objDB);
    // �ܵҤι񥳡��ɤ����
    $lngCustomerCountryCode = fncGetCountryCode($aryHeader["strcompanydisplaycode"], $objDB);
    // �ܵҼ�̾�μ���
    $strCustomerCompanyName = fncGetCustomerCompanyName($lngCustomerCountryCode, $aryCustomerCompany);
    // �ܵ�̾�μ���
    $strCustomerName = fncGetCustomerName($aryCustomerCompany);
    // Ǽ����β�ҥ����ɤμ���
    $lngDeliveryPlaceCode = fncGetNumericCompanyCode($aryHeader["strdeliveryplacecompanydisplaycode"], $objDB);

    // Ģɼ���̤μ���
    $lngSlipKindCode = $aryReport["lngslipkindcode"];
    // �ܵҤ�ɳ�Ť�Ģɼ1�ڡ���������κ������ٿ����������
    $maxItemPerPage = intval($aryReport["lngmaxline"]);
    // ��Ͽ���������٤ο�
    $totalItemCount = count($aryDetail);
    // ����ڡ������η׻�
    $maxPageCount = ceil($totalItemCount / $maxItemPerPage);
    // Ģɼ���̤��Ȥ˰ۤʤ����μ���
    if ($lngSlipKindCode == 1) {
        //1:���ꡦ����
        $templatFileName = "Ǽ�ʽ�temple_B��_Ϣ�����.xlsx";
        $activeSheetName = fncToUtf8("Ǽ�ʽ�");
    } else if ($lngSlipKindCode == 2) {
        //2:����
        $templatFileName = "Ǽ�ʽ�temple_����_Ϣ�����.xlsx";
        $activeSheetName = fncToUtf8("Ǽ�ʽ�");
    } else if ($lngSlipKindCode == 3) {
        //3:DEBIT NOTE
        $templatFileName = "DEBIT NOTE.xlsx";
        $activeSheetName = fncToUtf8("DEBIT NOTE");
    } else {
        throw new Exception("Ģɼ�ƥ�ץ졼�Ȥ�����Ǥ��ޤ���lngSlipKindCode=" . $lngSlipKindCode);
    }

    // --------------------------------------------
    //  ���ץ�åɥ����Ƚ����
    // --------------------------------------------
    // ���ܸ��б�
    ini_set('default_charset', 'UTF-8');
    // Ģɼ�ƥ�ץ졼�ȤΥե�ѥ�
    $spreadSheetFilePath = fncToUtf8(REPORT_TMPDIR . $templatFileName);
    // �ǡ��������ꤹ�륷����̾
    $dataSheetName = fncToUtf8("�ǡ���������");

    // --------------------------------------------
    //  ư��⡼�ɤˤ�����ʬ��
    // --------------------------------------------
    if ($strMode == "download") {
        // --------------------------------------------
        //  �����������Writer����
        // --------------------------------------------
        //���ץ�åɥ���������
        $xlSpreadSheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($spreadSheetFilePath);
        //�������������
        $xlWorkSheet = $xlSpreadSheet->GetSheetByName($dataSheetName);
        //XlsxWriter����
        $xlWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($xlSpreadSheet);

        //1�ڡ���ʬ�����٤Τ�¸�ߤ���Ȥ�������
        $itemMinIndex = 0;
        $itemMaxIndex = count($aryDetail) - 1;

        //��������Ȥ�Ǽ�ʽ�ǡ���������
        fncSetSlipDataToWorkSheet(
            $xlWorkSheet,
            $itemMinIndex, $itemMaxIndex, $strCustomerCompanyName,
            $strCustomerName, $aryCustomerCompany, $lngDeliveryPlaceCode,
            $aryHeader, $aryDetail,
            $lngSlipNo, $lngRevisionNo, $strSlipCode, $lngSalesNo, $dtmInsertDate);

        //�����ƥ��֥������ѹ�
        $xlSpreadSheet->setActiveSheetIndexByName($activeSheetName);

        //Writer�����ᤷ�Ƥߤ�
        $aryGenerateResult["XlsxWriter"] = $xlWriter;
    } else if ($strMode == "html") {
        if ($lngSlipKindCode == 1 || $lngSlipKindCode == 2) {
            // --------------------------------------------
            //  �ץ�ӥ塼HTML����
            // --------------------------------------------
            // �ץ�ӥ塼��CSS
            $previewStyle = "";
            // �ץ�ӥ塼HTML
            $previewData = "";

            // �ڡ���ñ�̤Ǥ�HTML����
            for ($page = 1; $page <= $maxPageCount; $page++) {

                // �μ¤˽�������뤿��1�ڡ�����˥��ץ�åɥ����Ȥ��ɤ߹��ߤʤ���
                $xlSpreadSheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($spreadSheetFilePath);
                $xlWorkSheet = $xlSpreadSheet->GetSheetByName($dataSheetName);
                $xlWriter = new \PhpOffice\PhpSpreadsheet\Writer\Html($xlSpreadSheet);

                if (strlen($previewStyle) == 0) {
                    // CSS�����Τ�1�Ĥ���Ф褤
                    $previewStyle = $xlWriter->generateStyles(true);
                }

                // ���ߤΥڡ�������1�ڡ�������������ٿ�����
                // ���Ϥ������٤Υ���ǥå����κǾ��ͤȺ����ͤ����
                $itemMinIndex = ($page - 1) * $maxItemPerPage;
                $itemMaxIndex = $page * $maxItemPerPage - 1;
                if ($itemMaxIndex > $totalItemCount - 1) {
                    $itemMaxIndex = $totalItemCount - 1;
                }

                // 1�ڡ���ʬ�Υץ�ӥ塼HTML����
                fncSetSlipDataToWorkSheet(
                    $xlWorkSheet,
                    $itemMinIndex, $itemMaxIndex, $strCustomerCompanyName,
                    $strCustomerName, $aryCustomerCompany, $lngDeliveryPlaceCode,
                    $aryHeader, $aryDetail,
                    $lngSlipNo, $lngRevisionNo, $strSlipCode, $lngSalesNo, $dtmInsertDate);

                // ���Τ��ɲ�
                $pageHtml = $xlWriter->generateSheetData();
                $previewData .= $pageHtml;

                // ���ܸ��б�
                ini_set('default_charset', 'EUC-JP');
                // �Ǹ��UTF-8����EUC-JP���Ѵ�������̤򥻥å�
                $aryGenerateResult["PreviewStyle"] = fncToEucjp($previewStyle);
                $aryGenerateResult["PreviewData"] = fncToEucjp($previewData);

            }
        } else if ($lngSlipKindCode == 3) {
                // // ���ܸ��б�
                ini_set('default_charset', 'EUC-JP');
            $strTemplateHeaderPath = "list/result/slip_debit_header.html";
            $strTemplatePath = "list/result/slip_debit.html";
            // $strTemplateFooterPath = "list/result/slip_debit_footer.html";

            $aryParts["strslipcode"] = $strSlipCode; //Ǽ�ʽ�NO.
            $aryParts["strcustomername"] = $strCustomerName; //7:�ܵ�̾
            $aryParts["strcustomeraddress1"] = $aryCustomerCompany["straddress1"]; //8:�ܵҽ���1
            $aryParts["strcustomeraddress2"] = $aryCustomerCompany["straddress2"]; //9:�ܵҽ���2
            $aryParts["strcustomeraddress3"] = $aryCustomerCompany["straddress3"]; //10:�ܵҽ���3
            $aryParts["strcustomeraddress4"] = $aryCustomerCompany["straddress4"]; //11:�ܵҽ���4
            $aryParts["dtmdeliverydate"] = $aryHeader["dtmdeliverydate"];            
            $lngmonetaryunitcode = $aryDetail[0]["lngmonetaryunitcode"];
            $strmonetaryunitsign = $aryDetail[0]["strmonetaryunitsign"];
            // �ܵ������ֹ�
            $aryParts["strcustomertel"] = "Tel:" . $aryCustomerCompany["strtel1"] . " " . $aryCustomerCompany["strtel2"];

            // �ܵ�FAX�ֹ�
            $aryParts["strcustomerfax"] = "Fax.:" . $aryCustomerCompany["strfax1"] . " " . $aryCustomerCompany["strfax2"];

            // ��׶��
            $curTotalPrice = ($lngmonetaryunitcode == 1 ? "&yen; " : $strmonetaryunitsign) . " " . number_format($aryHeader["curtotalprice"], 2, '.', ',');

            $aryParts["curtotalprice"] = $curTotalPrice;            
            $aryParts["strpaymentmethodname"] = $aryHeader["strpaymentmethodname"];
            $aryParts["nameofbank"] = $aryHeader["lngpaymentmethodcode"] == 1 ? "MUFG BANK, LTD." : "";
            $aryParts["nameofbranch"] = $aryHeader["lngpaymentmethodcode"] == 1 ? "ASAKUSA BRANCH" : "";
            $aryParts["addressofbank1"] = $aryHeader["lngpaymentmethodcode"] == 1 ? "4-2, ASAKUSA 1-CHOME, " : "";
            $aryParts["addressofbank2"] = $aryHeader["lngpaymentmethodcode"] == 1 ? " TAITO-KU, TOKYO 111-0032, JAPAN" : "";
            $aryParts["swiftcode"] = $aryHeader["lngpaymentmethodcode"] == 1 ? "BOTKJPJT" : "";
            $aryParts["accountname"] = $aryHeader["lngpaymentmethodcode"] == 1 ? "KUWAGATA CO.,LTD." : "";
            $aryParts["accountno"] = $aryHeader["lngpaymentmethodcode"] == 1 ? "1063143" : "";

            // HTML����
            $objTemplateHeader = new clsTemplate();
            $objTemplateHeader->getTemplate($strTemplateHeaderPath);
            $strTemplateHeader = $objTemplateHeader->strTemplate;

            $objTemplate = new clsTemplate();
            $objTemplate->getTemplate($strTemplatePath);
            $strTemplate = $objTemplate->strTemplate;

            $objTemplate->strTemplate = $strTemplate;

            for ($i = 0; $i < count($aryDetail); $i++) {
                $aryParts["strcustomersalescode". ($i)] = $aryDetail[$i]["strcustomerreceivecode"];
                $aryParts["strproductenglishname". ($i)] = $aryDetail[$i]["strproductenglishname"];
                $aryParts["lngproductquantity". ($i)] = number_format($aryDetail[$i]["lngproductquantity"]);
                $aryParts["curproductprice". ($i)] = number_format($aryDetail[$i]["curproductprice"], 2, '.', ',');
                $aryParts["cursubtotalprice". ($i)] = number_format($aryDetail[$i]["cursubtotalprice"], 2, '.', ',');
                $aryParts["strsalesclassname". ($i)] = $aryDetail[$i]["strsalesclassname"];
                $aryParts["strnote". ($i)] = $aryDetail[$i]["strnote"];

                // �ܵҼ����ֹ�
                if ($aryParts["strcustomersalescode". ($i)] != "") {
                    $aryParts["strcustomersalescode". ($i)] = "(PO No:" . $aryParts["strcustomersalescode". ($i)] . ")";
                }
            }

            // �֤�����
            $objTemplate->replace($aryParts);
            $objTemplate->complete();
            $strBodyHtml = $objTemplate->strTemplate;
            $aryGenerateResult["PreviewStyle"] = fncToEucjp($strTemplateHeader);
            $aryGenerateResult["PreviewData"] = fncToEucjp($strBodyHtml);
        }
        // $aryGenerateResult["PreviewData"] = $previewData;
    } else {
        // �����ʥ⡼��
        throw new Exception("�����ʥ⡼�ɤ����ꤵ��ޤ�����strMode=" . $strMode);
    }

    // --------------------------------------------
    //  ������̤��֤�
    // --------------------------------------------
    return $aryGenerateResult;
}

// Ǽ�ʽ�ǡ�����Ģɼ�ƥ�ץ졼�ȤΥ�������Ȥ�����
function fncSetSlipDataToWorkSheet(
    $xlWorkSheet,
    $itemMinIndex, $itemMaxIndex, $strCustomerCompanyName,
    $strCustomerName, $aryCustomerCompany, $lngDeliveryPlaceCode,
    $aryHeader, $aryDetail,
    $lngSlipNo, $lngRevisionNo, $strSlipCode, $lngSalesNo, $dtmInsertDate) {
    // ����­��
    // lngSlipNo,lngRevisionNo,strSlipCode,lngSalesNo,dtmInsertDate�ϥǡ�����Ͽ����ޤǳ��ꤷ�ʤ�����
    // �ץ�ӥ塼ɽ�����϶��ˤ���������ʤ�����������ɻ��ϥǡ�����Ͽ�Ѥߤʤ�����ϲ�ǽ��

    // ------------------------------------------
    //   �ޥ����ǡ����Υ��å�
    // ------------------------------------------
    // �ͤ�����
    $v_lngslipno = is_null($lngSlipNo) ? "" : $lngSlipNo; //1:Ǽ����ɼ�ֹ�
    $v_lngrevisionno = is_null($lngRevisionNo) ? "" : $lngRevisionNo; //2:��ӥ�����ֹ�
    $v_strslipcode = is_null($strSlipCode) ? "" : $strSlipCode; //3:Ǽ����ɼ������
    $v_lngsalesno = is_null($lngSalesNo) ? "" : $lngSalesNo; //4:����ֹ�
    $v_strcustomercode = $aryCustomerCompany["lngcompanycode"]; //5:�ܵҥ�����
    $v_strcustomercompanyname = $strCustomerCompanyName; //6:�ܵҼ�̾
    $v_strcustomername = $strCustomerName; //7:�ܵ�̾
    $v_strcustomeraddress1 = $aryCustomerCompany["straddress1"]; //8:�ܵҽ���1
    $v_strcustomeraddress2 = $aryCustomerCompany["straddress2"]; //9:�ܵҽ���2
    $v_strcustomeraddress3 = $aryCustomerCompany["straddress3"]; //10:�ܵҽ���3
    $v_strcustomeraddress4 = $aryCustomerCompany["straddress4"]; //11:�ܵҽ���4
    $v_strcustomerphoneno = $aryCustomerCompany["strtel1"]; //12:�ܵ������ֹ�
    $v_strcustomerfaxno = $aryCustomerCompany["strfax1"]; //13:�ܵ�FAX�ֹ�
    $v_strcustomerusername = $aryHeader["strcustomerusername"]; //14:�ܵ�ô����̾
    $v_dtmdeliverydate = $aryHeader["dtmdeliverydate"]; //15:Ǽ����
    $v_lngdeliveryplacecode = $lngDeliveryPlaceCode; //16:Ǽ�ʾ�ꥳ����
    $v_strdeliveryplacename = $aryHeader["strdeliveryplacename"]; //17:Ǽ�ʾ��̾
    $v_strdeliveryplaceusername = $aryHeader["strdeliveryplaceusername"]; //18:Ǽ�ʾ��ô����̾
    $v_strusercode = $aryHeader["strdrafteruserdisplaycode"]; //19:ô���ԥ�����
    $v_strusername = $aryHeader["strdrafteruserdisplayname"]; //20:ô����̾
    $v_curtotalprice = $aryHeader["curtotalprice"]; //21:��׶��
    $v_lngmonetaryunitcode = $aryDetail[0]["lngmonetaryunitcode"]; //22:�̲�ñ�̥�����
    $v_strmonetaryunitsign = $aryDetail[0]["strmonetaryunitsign"]; //23:�̲�ñ��
    $v_lngtaxclasscode = $aryHeader["lngtaxclasscode"]; //24:���Ƕ�ʬ������
    $v_strtaxclassname = $aryHeader["strtaxclassname"]; //25:���Ƕ�ʬ
    $v_curtax = $aryHeader["curtax"]; //26:������Ψ
    $v_lngpaymentmethodcode = $aryHeader["lngpaymentmethodcode"]; //27:��ʧ��ˡ������
    $v_dtmpaymentlimit = $aryHeader["dtmpaymentlimit"]; //28:��ʧ����
    $v_dtminsertdate = is_null($dtmInsertDate) ? "" : $dtmInsertDate; //29:������
    $v_strnote = $aryHeader["strnote"]; //30:����
    $v_strshippercode = $aryCustomerCompany["strstockcompanycode"]; //31:�����襳���ɡʽвټԡ�

    // ������ͤ򥻥å�
    setCellValue($xlWorkSheet, "B3", mb_convert_encoding($v_lngslipno, 'euc-jp', 'UTF8')); //1:Ǽ����ɼ�ֹ�
    setCellValue($xlWorkSheet, "C3", mb_convert_encoding($v_lngrevisionno, 'euc-jp', 'UTF8')); //2:��ӥ�����ֹ�
    setCellValue($xlWorkSheet, "D3", mb_convert_encoding($v_strslipcode, 'euc-jp', 'UTF8')); //3:Ǽ����ɼ������
    setCellValue($xlWorkSheet, "E3", mb_convert_encoding($v_lngsalesno, 'euc-jp', 'UTF8')); //4:����ֹ�
    setCellValue($xlWorkSheet, "F3", mb_convert_encoding($v_strcustomercode, 'euc-jp', 'UTF8')); //5:�ܵҥ�����
    setCellValue($xlWorkSheet, "G3", $v_strcustomercompanyname); //6:�ܵҼ�̾
    setCellValue($xlWorkSheet, "H3", $v_strcustomername); //7:�ܵ�̾
    setCellValue($xlWorkSheet, "I3", mb_convert_encoding($v_strcustomeraddress1, 'euc-jp', 'UTF8')); //8:�ܵҽ���1
    setCellValue($xlWorkSheet, "J3", mb_convert_encoding($v_strcustomeraddress2, 'euc-jp', 'UTF8')); //9:�ܵҽ���2
    setCellValue($xlWorkSheet, "K3", mb_convert_encoding($v_strcustomeraddress3, 'euc-jp', 'UTF8')); //10:�ܵҽ���3
    setCellValue($xlWorkSheet, "L3", mb_convert_encoding($v_strcustomeraddress4, 'euc-jp', 'UTF8')); //11:�ܵҽ���4
    setCellValue($xlWorkSheet, "M3", mb_convert_encoding($v_strcustomerphoneno, 'euc-jp', 'UTF8')); //12:�ܵ������ֹ�
    setCellValue($xlWorkSheet, "N3", mb_convert_encoding($v_strcustomerfaxno, 'euc-jp', 'UTF8')); //13:�ܵ�FAX�ֹ�
    setCellValue($xlWorkSheet, "O3", mb_convert_encoding($v_strcustomerusername, 'euc-jp', 'UTF8')); //14:�ܵ�ô����̾
    setCellValue($xlWorkSheet, "P3", mb_convert_encoding($v_dtmdeliverydate, 'euc-jp', 'UTF8')); //15:Ǽ����
    setCellValue($xlWorkSheet, "Q3", mb_convert_encoding($v_lngdeliveryplacecode, 'euc-jp', 'UTF8')); //16:Ǽ�ʾ�ꥳ����
    setCellValue($xlWorkSheet, "R3", mb_convert_encoding($v_strdeliveryplacename, 'euc-jp', 'UTF8')); //17:Ǽ�ʾ��̾
    setCellValue($xlWorkSheet, "S3", mb_convert_encoding($v_strdeliveryplaceusername, 'euc-jp', 'UTF8')); //18:Ǽ�ʾ��ô����̾
    setCellValue($xlWorkSheet, "T3", mb_convert_encoding($v_strusercode, 'euc-jp', 'UTF8')); //19:ô���ԥ�����
    setCellValue($xlWorkSheet, "U3", mb_convert_encoding($v_strusername, 'euc-jp', 'UTF8')); //20:ô����̾
    setCellValue($xlWorkSheet, "V3", $v_curtotalprice); //21:��׶��
    setCellValue($xlWorkSheet, "W3", mb_convert_encoding($v_lngmonetaryunitcode, 'euc-jp', 'UTF8')); //22:�̲�ñ�̥�����
    setCellValue($xlWorkSheet, "X3", mb_convert_encoding($v_strmonetaryunitsign, 'euc-jp', 'UTF8')); //23:�̲�ñ��
    setCellValue($xlWorkSheet, "Y3", mb_convert_encoding($v_lngtaxclasscode, 'euc-jp', 'UTF8')); //24:���Ƕ�ʬ������
    setCellValue($xlWorkSheet, "Z3", mb_convert_encoding($v_strtaxclassname, 'euc-jp', 'UTF8')); //25:���Ƕ�ʬ
    setCellValue($xlWorkSheet, "AA3", mb_convert_encoding($v_curtax, 'euc-jp', 'UTF8')); //26:������Ψ
    setCellValue($xlWorkSheet, "AB3", mb_convert_encoding($v_lngpaymentmethodcode, 'euc-jp', 'UTF8')); //27:��ʧ��ˡ������
    setCellValue($xlWorkSheet, "AC3", mb_convert_encoding($v_dtmpaymentlimit, 'euc-jp', 'UTF8')); //28:��ʧ����
    setCellValue($xlWorkSheet, "AD3", mb_convert_encoding($v_dtminsertdate, 'euc-jp', 'UTF8')); //29:������
    setCellValue($xlWorkSheet, "AE3", mb_convert_encoding($v_strnote, 'euc-jp', 'UTF8')); //30:����
    setCellValue($xlWorkSheet, "AF3", mb_convert_encoding($v_strshippercode, 'euc-jp', 'UTF8')); //31:�����襳���ɡʽвټԡ�

    // ------------------------------------------
    //   ���٥ǡ����Υ��å�
    // ------------------------------------------
    // ���٥ǡ����򥻥åȤ��볫�Ϲ�
    $startRowIndex = 6;
    for ($i = $itemMinIndex; $i <= $itemMaxIndex; $i++) {
        $d = $aryDetail[$i];

        // �ͤ�����
        $v_lngslipno = is_null($lngSlipNo) ? "" : $lngSlipNo; //1:Ǽ����ɼ�ֹ�
        $v_lngslipdetailno = $d["rownumber"]; //2:Ǽ����ɼ�����ֹ�
        $v_lngrevisionno = is_null($lngRevisionNo) ? "" : $lngRevisionNo; //3:��ӥ�����ֹ�
        $v_strcustomersalescode = $d["strcustomerreceivecode"]; //4:�ܵҼ����ֹ�
        $v_lngsalesclasscode = $d["lngsalesclasscode"]; //5:����ʬ������
        $v_strsalesclassname = $d["strsalesclassname"]; //6:����ʬ̾
        $v_strgoodscode = $d["strgoodscode"]; //7:�ܵ�����
        $v_strproductcode = $d["strproductcode"]; //8:���ʥ�����
        $v_strrevisecode = $d["strrevisecode"]; //9:���Υ�����
        $v_strproductname = $d["strproductname"]; //10:����̾
        $v_strproductenglishname = $d["strproductenglishname"]; //11:����̾�ʱѸ��
        $v_curproductprice = $d["curproductprice"]; //12:ñ��
        $v_lngquantity = $d["lngunitquantity"]; //13:����
        $v_lngproductquantity = $d["lngproductquantity"]; //14:����
        $v_lngproductunitcode = $d["lngproductunitcode"]; //15:����ñ�̥�����
        $v_strproductunitname = $d["strproductunitname"]; //16:����ñ��̾
        $v_cursubtotalprice = $d["cursubtotalprice"]; //17:����
        $v_strnote = $d["strnote"]; //18:��������

        // ������ͤ򥻥å�
        $r = $startRowIndex + ($i - $itemMinIndex);
        setCellDetailValue($xlWorkSheet, "B", $r, mb_convert_encoding($v_lngslipno, 'euc-jp', 'UTF8')); //1:Ǽ����ɼ�ֹ�
        setCellDetailValue($xlWorkSheet, "C", $r, mb_convert_encoding($v_lngslipdetailno, 'euc-jp', 'UTF8')); //2:Ǽ����ɼ�����ֹ�
        setCellDetailValue($xlWorkSheet, "D", $r, mb_convert_encoding($v_lngrevisionno, 'euc-jp', 'UTF8')); //3:��ӥ�����ֹ�
        setCellDetailValue($xlWorkSheet, "E", $r, mb_convert_encoding($v_strcustomersalescode, 'euc-jp', 'UTF8')); //4:�ܵҼ����ֹ�
        setCellDetailValue($xlWorkSheet, "F", $r, mb_convert_encoding($v_lngsalesclasscode, 'euc-jp', 'UTF8')); //5:����ʬ������
        setCellDetailValue($xlWorkSheet, "G", $r, mb_convert_encoding($v_strsalesclassname, 'euc-jp', 'UTF8')); //6:����ʬ̾
        setCellDetailValue($xlWorkSheet, "H", $r, mb_convert_encoding($v_strgoodscode, 'euc-jp', 'UTF8')); //7:�ܵ�����
        setCellDetailValue($xlWorkSheet, "I", $r, mb_convert_encoding($v_strproductcode, 'euc-jp', 'UTF8')); //8:���ʥ�����
        setCellDetailValue($xlWorkSheet, "J", $r, mb_convert_encoding($v_strrevisecode, 'euc-jp', 'UTF8')); //9:���Υ�����
        setCellDetailValue($xlWorkSheet, "K", $r, mb_convert_encoding($v_strproductname, 'euc-jp', 'UTF8')); //10:����̾
        setCellDetailValue($xlWorkSheet, "L", $r, mb_convert_encoding($v_strproductenglishname, 'euc-jp', 'UTF8')); //11:����̾�ʱѸ��
        setCellDetailValue($xlWorkSheet, "M", $r, $v_curproductprice); //12:ñ��
        setCellDetailValue($xlWorkSheet, "N", $r, $v_lngquantity); //13:����
        setCellDetailValue($xlWorkSheet, "O", $r, $v_lngproductquantity); //14:����
        setCellDetailValue($xlWorkSheet, "P", $r, $v_lngproductunitcode); //15:����ñ�̥�����
        setCellDetailValue($xlWorkSheet, "Q", $r, mb_convert_encoding($v_strproductunitname, 'euc-jp', 'UTF8')); //16:����ñ��̾
        setCellDetailValue($xlWorkSheet, "R", $r, $v_cursubtotalprice); //17:����
        setCellDetailValue($xlWorkSheet, "S", $r, mb_convert_encoding($v_strnote, 'euc-jp', 'UTF8')); //18:��������

    }

}

// �ڡ�����ξ��󤫤���Ͽ���HTML������
function fncGetRegisterResultTableBodyHtml($aryPerPage, $objDB)
{
    $strHtml = "";

    for ($i = 0; $i < count($aryPerPage); $i++) {
        $aryPage = $aryPerPage[$i];

        // Ǽ����ɼ�ֹ�
        $lngSlipNo = $aryPage["lngSlipNo"];
        // Ǽ����ɼ������
        $strSlipCode = $aryPage["strSlipCode"];
        // ��ӥ�����ֹ�
        $lngRevisionNo = $aryPage["lngRevisionNo"];

        // �������μ���
        $dtmInsertDate = fncGetInsertDateBySlipCode($strSlipCode, $objDB);

        // HTML��������/sc/finish2/finish2.js �Υե��󥯥����ƤӽФ���ޤ��
        $aryHtml = array();
        $aryHtml[] = "                <tr>";
        $aryHtml[] = "                    <td class='item-value'>" . $strSlipCode . "</td>";
        $aryHtml[] = "                    <td class='item-value'>" . $dtmInsertDate . "</td>";
        $aryHtml[] = "                    <td class='item-value'>";
        $aryHtml[] = "                        <img class='btn-download'";
        $aryHtml[] = "                         onclick='OnClickDownload(this, \"" . $lngSlipNo . "\", \"" . $strSlipCode . "\", \"" . $lngRevisionNo . "\");'";
        $aryHtml[] = "                         onmouseover='OnMouseOverDownload(this);'";
        $aryHtml[] = "                         onmouseout='OnMouseOutDownload(this);'>";
        $aryHtml[] = "                    </td>";
        $aryHtml[] = "                </tr>";

        $strHtml .= implode("\n", $aryHtml);
    }

    return $strHtml;

}
