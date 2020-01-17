<?php
// ----------------------------------------------------------------------------
/**
 *       ������� ������̤γ���ܥ���
 *
 *       ��������
 *         �������о����ٹ������������򤷤��Ԥ���ꤹ�����
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------

// �ɤ߹���
include 'conf.inc';
require LIB_FILE;

//PHPɸ���JSON�Ѵ��᥽�åɤϥ��顼�ˤʤ�Τǳ����Υ饤�֥��(���餯���󥳡��ɤ�����)
include 'JSON.php';

//�ͤμ���
$postdata = file_get_contents("php://input");
$data = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//JSON���饹���󥹥��󥹲�
$s = new Services_JSON();

//�ͤ�¸�ߤ��ʤ������̾�� POST �Ǽ�����
if ($data == null) {
    $data = $_POST;
}
// ���å�����ǧ
$objAuth = fncIsSession($data["strSessionID"], $objAuth, $objDB);

//�桼����ID����(Ⱦ�ѥ��ڡ��������뤿��)
$usrId = trim($objAuth->UserID);

$strId = $data["strId"];
$aryId = explode(",", $strId);
$aryQuery = array();
$aryQuery[] = "SELECT ";
$aryQuery[] = "rd.lngReceiveNo as lngReceiveNo, rd.lngRevisionNo as lngRevisionNo";
$aryQuery[] = ", r.strReceiveCode strReceiveCode";
$aryQuery[] = ", r.strcustomerreceivecode";
$aryQuery[] = ", r.strMonetaryUnitSign";
$aryQuery[] = ", r.lngMonetaryUnitCode";
$aryQuery[] = ", rd.lngreceivedetailno as lngreceivedetailno";
$aryQuery[] = ", rd.strProductCode as strProductCode";// ���ʥ����ɡ�̾��
$aryQuery[] = ", p.strProductName as strProductName";
$aryQuery[] = ", p.lngProductNo as lngProductNo";
$aryQuery[] = ", p.lngRevisionNo as lngProductRevisionNo";
$aryQuery[] = ", p.strrevisecode as strrevisecode";
$aryQuery[] = ", r.strCompanyDisplayCode as strCompanyDisplayCode";// �ܵҥ����ɡ�̾��
$aryQuery[] = ", r.strCompanyDisplayName as strCompanyDisplayName";
$aryQuery[] = ", p.lngproductno as lngproductno";
// ���ʬ��
$aryQuery[] = ", sd.lngsalesdivisioncode";
$aryQuery[] = ", sd.strsalesdivisionname";

$aryQuery[] = ", rd.lngSalesClassCode as lngSalesClassCode";// ����ʬ
$aryQuery[] = ", ss.strSalesClassName as strSalesClassName";
$aryQuery[] = ", p.strGoodsCode as strGoodsCode";// �ܵ�����
$aryQuery[] = ", rd.dtmDeliveryDate as dtmDeliveryDate";// Ǽ��
$aryQuery[] = ", To_char( rd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";// ñ��
$aryQuery[] = ", rd.lngProductUnitCode as lngProductUnitCode";// ñ��
$aryQuery[] = ", pu.strProductUnitName as strProductUnitName";
$aryQuery[] = ", p.lngcartonquantity";// �����ȥ�����
$aryQuery[] = ", To_char( rd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";// ��ȴ���
$aryQuery[] = ", rd.strNote as strDetailNote";// ��������
$aryQuery[] = ", ed.lngproductquantity as lngproductquantity";// ���ʿ���
$aryQuery[] = " FROM t_ReceiveDetail rd";
$aryQuery[] = " LEFT JOIN (";
$aryQuery[] = " SELECT r1.*";
$aryQuery[] = ", m_MonetaryUnit.strMonetaryUnitSign";
$aryQuery[] = ", cust_c.strCompanyDisplayCode";
$aryQuery[] = ", cust_c.strCompanyDisplayName";
$aryQuery[] = " from m_Receive r1";
$aryQuery[] = " LEFT JOIN m_MonetaryUnit USING (lngMonetaryUnitCode) ";
$aryQuery[] = " LEFT JOIN m_Company cust_c ON r1.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
$aryQuery[] = " ) r USING (lngReceiveNo, lngRevisionNo)";
$aryQuery[] = " LEFT JOIN (";
$aryQuery[] = "   select p1.*  from m_product p1 ";
$aryQuery[] = "     inner join (select max(lngRevisionNo) lngRevisionNo, strproductcode, strrevisecode from m_Product group by strProductCode, strrevisecode) p2";
$aryQuery[] = "     on p1.lngRevisionNo = p2.lngRevisionNo and p1.strproductcode = p2.strproductcode and p1.strrevisecode = p2.strrevisecode";
$aryQuery[] = " ) p on p.strproductcode = rd.strproductcode AND p.strrevisecode = rd.strrevisecode";
$aryQuery[] = "  LEFT JOIN t_estimatedetail me ";
$aryQuery[] = "    on rd.lngestimateno = me.lngestimateno ";
$aryQuery[] = "    and rd.lngestimatedetailno = me.lngestimatedetailno ";
$aryQuery[] = "    and rd.lngestimaterevisionno = me.lngrevisionno ";
$aryQuery[] = "  LEFT JOIN m_SalesClass ss ";
$aryQuery[] = "    on rd.lngSalesClassCode = ss.lngSalesClassCode ";
$aryQuery[] = "  LEFT JOIN m_salesclassdivisonlink ssdl ";
$aryQuery[] = "    on ssdl.lngSalesClassCode = ss.lngSalesClassCode ";
$aryQuery[] = "    and ssdl.lngsalesdivisioncode = me.lngsalesdivisioncode ";
$aryQuery[] = " LEFT JOIN m_salesdivision sd on sd.lngsalesdivisioncode = ssdl.lngsalesdivisioncode";
$aryQuery[] = " LEFT JOIN m_ProductUnit pu ON rd.lngProductUnitCode = pu.lngProductUnitCode";
$aryQuery[] = " LEFT JOIN t_estimatedetail ed on rd.lngestimateno = ed.lngestimateno";
$aryQuery[] = " and rd.lngestimatedetailno = ed.lngestimatedetailno ";
$aryQuery[] = " and rd.lngestimaterevisionno = ed.lngrevisionno";
$aryQuery[] = " WHERE ";
for ($i = 0; $i < count($aryId); $i++) {
    $id = explode("_", $aryId[$i]);
    $lngReceiveNo = $id[0];
    $lngReceiveDetailNo = $id[1];
    $lngRevisionNo = $id[2];
    if ($i != 0) {
        $aryQuery[] = " OR ";        
    }
    $aryQuery[] = " (rd.lngReceiveNo = " .$lngReceiveNo . " AND rd.lngRevisionNo = " .$lngRevisionNo . " AND rd.lngReceiveDetailNo = " .$lngReceiveDetailNo . ")";
}
$aryQuery[] = " ORDER BY strReceiveCode ASC, lngreceivedetailno DESC";
$strQuery = implode( "\n", $aryQuery );
//�������
$result = array();
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
// �������������ξ��
if ($lngResultNum) {
    // ���������Ǥ�����̾����
    for ($i = 0; $i < $lngResultNum; $i++) {
        $result['receiveDetail'] = pg_fetch_all($lngResultID);
    }
}

$objDB->freeResult($lngResultID);

$strQuery = "SELECT lngproductunitcode, strproductunitname FROM m_productunit";
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
// �������������ξ��
if ($lngResultNum) {
    // ���������Ǥ�����̾����
    for ($i = 0; $i < $lngResultNum; $i++) {
        $result['productUnit'] = pg_fetch_all($lngResultID);
    }
}

$objDB->freeResult($lngResultID);
$objDB->close();

//��̽���
mb_convert_variables('UTF-8', 'EUC-JP', $result);
echo $s->encodeUnsafe($result);

