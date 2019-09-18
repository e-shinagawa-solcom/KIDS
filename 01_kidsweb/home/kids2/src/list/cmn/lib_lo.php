<?
/**
 *    Ģɼ�����ѥ饤�֥��
 *
 *    Ģɼ�����ѡ�������ؿ��饤�֥��
 *
 *    @package   kuwagata
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
 *
 */

// Ģɼ���������
$aryListOutputMenu = array
    (
    DEF_REPORT_PRODUCT => array
    (
        "name" => "���ʴ��� ���ʴ���",
        "file" => "p",
    ),

    DEF_REPORT_ORDER => array
    (
        "name" => "ȯ����� ȯ���",
        "file" => "po",
    ),

    DEF_REPORT_ESTIMATE => array
    (
        "name" => "���Ѹ�������",
        "file" => "estimate",
    ),

    DEF_REPORT_SLIP => array
    (
        "name" => "������� Ǽ�ʽ�",
        "file" => "slip",
    ),
);

// -----------------------------------------------------------------
/**
 *    ȯ��ǡ�����ǧ���֥����å��ؿ�
 *
 *    ���ꤷ��ȯ��ǡ����ξ�ǧ���֤�����å�����ؿ�
 *
 *    @param  Integer $lngOrderNo ȯ��ʥ�С�
 *    @param  Object  $objDB      DB���֥�������
 *    @return Boolean $bytApproval  ��ǧ����(TRUE:��ǧ�� FALSE:̤��ǧ)
 *    @access public
 */
// -----------------------------------------------------------------
function fncCheckApprovalProductOrder($lngOrderNo, $objDB)
{
    // ȯ�����ե��ξ�ǧ���֥����å�����������
    $aryQuery[] = "SELECT o.lngOrderNo ";
    $aryQuery[] = "FROM m_Order o ";

    // ����ȯ��No
    $aryQuery[] = "WHERE o.lngOrderNo = " . $lngOrderNo;

    // A:��ȯ��׾��֤���礭�����֤�ȯ��ǡ���
    // B:��ȯ��׾��֤Υǡ���
    // C:����ե���¸�ߤ��ʤ�(¨ǧ�ڰƷ�)
    // D:�־�ǧ�׾��֤ˤ���Ʒ�
    // A OR ( B AND ( C OR D ) )
    $aryQuery[] = " AND (";

    // A:��ȯ��׾��֤���礭�����֤�ȯ��ǡ���
    $aryQuery[] = "  o.lngOrderStatusCode > " . DEF_ORDER_ORDER;

    $aryQuery[] = "  OR";
    $aryQuery[] = "  (";

    // B:��ȯ��׾��֤Υǡ���
    $aryQuery[] = "    o.lngOrderStatusCode = " . DEF_ORDER_ORDER;
    $aryQuery[] = "     AND";
    $aryQuery[] = "    (";

    // C:����ե���¸�ߤ��ʤ�(¨ǧ�ڰƷ�)
    $aryQuery[] = "      0 = ";
    $aryQuery[] = "      (";
    $aryQuery[] = "        SELECT COUNT ( mw.lngWorkflowCode ) ";
    $aryQuery[] = "        FROM m_Workflow mw ";
// ��ץꥱ������󥵡��С��ǡ�Index����������ʤ��㳲�б� - to_number ��ä���
    // CREATE INDEX m_workflow_strworkflowkeycode_index ON m_workflow USING btree (to_number(strworkflowkeycode, '9999999'::text));
    //    $aryQuery[] = "        WHERE to_number ( mw.strWorkflowKeyCode, '9999999') = o.lngOrderNo";
    $aryQuery[] = "        WHERE mw.strWorkflowKeyCode = trim(to_char(o.lngOrderNo, '9999999'))";
    $aryQuery[] = "         AND mw.lngFunctionCode = " . DEF_FUNCTION_PO1;
    $aryQuery[] = "      )";

    // D:�־�ǧ�׾��֤ˤ���Ʒ�
    $aryQuery[] = "      OR " . DEF_STATUS_APPROVE . " = ";
    $aryQuery[] = "      (";
    $aryQuery[] = "        SELECT tw.lngWorkflowStatusCode";
    $aryQuery[] = "        FROM m_Workflow mw2, t_Workflow tw";
// ��ץꥱ������󥵡��С��ǡ�Index����������ʤ��㳲�б� - to_number ��ä���
    //    $aryQuery[] = "        WHERE to_number ( mw2.strWorkflowKeyCode, '9999999') = o.lngOrderNo";
    $aryQuery[] = "        WHERE mw2.strWorkflowKeyCode = trim(to_char(o.lngOrderNo, '9999999'))";
    $aryQuery[] = "         AND mw2.lngFunctionCode = " . DEF_FUNCTION_PO1;
    $aryQuery[] = "         AND tw.lngWorkflowSubCode =";
    $aryQuery[] = "        (";
    $aryQuery[] = "          SELECT MAX ( tw2.lngWorkflowSubCode ) FROM t_Workflow tw2 WHERE tw.lngWorkflowCode = tw2.lngWorkflowCode";
    $aryQuery[] = "        )";
    $aryQuery[] = "         AND mw2.lngWorkflowCode = tw.lngWorkflowCode";
    $aryQuery[] = "      )";
    $aryQuery[] = "    )";
    $aryQuery[] = "  )";
    $aryQuery[] = ")";

    // ����Х���ȯ������ɽ������
    // ����lngOrderNo��Ʊ��ȯ��������ˤ����ơ�
    // ��ӥ����NO������Τ�Τ��ɤ����Υ����å�
    $aryQuery[] = " AND o.lngRevisionNo = ";
    $aryQuery[] = "(";
    $aryQuery[] = "  SELECT MAX ( o2.lngRevisionNo )";
    $aryQuery[] = "  FROM m_Order o2";
    $aryQuery[] = "  WHERE o.strOrderCode = o2.strOrderCode AND o2.bytInvalidFlag = false";
    $aryQuery[] = ")";

    // ���ȯ������ɽ������
    // ����lngOrderNo��Ʊ��ȯ��������ˤ����ơ�
    // ��ӥ����NO���Ǿ��Τ�Τ�0�ʾ�(ȯ���ðƷ�ʳ�)���ɤ����Υ����å�
    $aryQuery[] = " AND 0 <= ";
    $aryQuery[] = "(";
    $aryQuery[] = "  SELECT MIN ( o3.lngRevisionNo )";
    $aryQuery[] = "  FROM m_Order o3";
    $aryQuery[] = "  WHERE o.strOrderCode = o3.strOrderCode AND o3.bytInvalidFlag = false";
    $aryQuery[] = ")";

    $strQuery = join("", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    // �ǥե���� FALSE ������
    $bytApproval = false;

    // ��̥쥳���ɤ�1���ä����(ʣ��¸�ߤϥ��꡼����Ǥ����ǽ��ͭ��)��TRUE
    if ($lngResultNum == 1) {
        $bytApproval = true;
        $objDB->freeResult($lngResultID);
    }

    return $bytApproval;
}

// -----------------------------------------------------------------
/**
 *    Ģɼ���ϥ��ԡ��ե�����ѥ����������������ؿ�
 *
 *    ���ꤷ��Ģɼ���ԡ��ե�����ѥ���������륯�������������ؿ�
 *
 *    @param  Integer $lngReportClassCode Ģɼ��ʬ������
 *    @param  String  $strReportKeyCode   Ģɼ����������
 *    @param  Integer $lngReportCode      Ģɼ������
 *    @return String                      Ģɼ���ϥ��ԡ��ե�����ѥ�����������
 *    @access public
 */
// -----------------------------------------------------------------
function fncGetCopyFilePathQuery($lngReportClassCode, $strReportKeyCode, $lngReportCode)
{

    // Ģɼ�����ɤ����ξ�硢���ξ�說����ʬ����
    if ($lngReportCode) {
        $strReportCodeConditions = " AND r.lngReportCode = " . $lngReportCode;
    }

    // ���ʴ�����ϥ��ԡ��ե������������������
    if ($lngReportClassCode == DEF_REPORT_PRODUCT) {
        $aryQuery[] = "SELECT r.strReportPathName ";
        $aryQuery[] = "FROM t_GoodsPlan gp, t_Report r ";
        $aryQuery[] = "WHERE r.lngReportClassCode = " . $lngReportClassCode;
        $aryQuery[] = $strReportCodeConditions;

        // �������ʥ�����
        $aryQuery[] = " AND gp.lngProductNo = " . $strReportKeyCode;
        // �ǿ���ӥ����
        $aryQuery[] = " AND lngRevisionNo = ( SELECT MAX ( gp2.lngRevisionNo ) FROM t_GoodsPlan gp2 WHERE gp.lngProductNo = gp2.lngProductNo )";

        // Ģɼ���������ɤ����ʴ�襳���ɷ��
        $aryQuery[] = " AND to_number ( r.strReportKeyCode, '9999999') = gp.lngGoodsPlanCode";
    }

    // ȯ��Ģɼ���ϥ��ԡ��ե������������������
    elseif ($lngReportClassCode == DEF_REPORT_ORDER) {
        $aryQuery[] = "SELECT r.strReportPathName ";
        $aryQuery[] = "FROM m_purchaseorder po, t_Report r ";

        // �о�Ģɼ(���ʴ�� or ȯ��)����
        $aryQuery[] = "WHERE r.lngReportClassCode = " . $lngReportClassCode;

        $aryQuery[] = $strReportCodeConditions;

        // Ģɼ�����ɻ���
        $aryQuery[] = " AND r.strReportKeyCode = '" . $strReportKeyCode . "'";
        // ��ӥ����˥ޥ��ʥ���̵��
        $aryQuery[] = " AND 0 <= ";
        $aryQuery[] = "( ";
        $aryQuery[] = "  SELECT MIN( po1.lngRevisionNo ) FROM m_purchaseorder po1 WHERE po1.strOrderCode = po.strOrderCode ";
        $aryQuery[] = ")";
        $aryQuery[] = " AND  r.strReportKeyCode = trim(to_char(po.lngpurchaseorderno, '9999999'))";
    }
    // Ǽ�ʽ�Ģɼ���ϥ��ԡ��ե������������������
    elseif ($lngReportClassCode == DEF_REPORT_SLIP) {
        $aryQuery[] = "SELECT r.strReportPathName ";
        $aryQuery[] = "FROM m_slip s, t_Report r ";

        // �о�Ģɼ(���ʴ�� or ȯ��)����
        $aryQuery[] = "WHERE r.lngReportClassCode = " . $lngReportClassCode;

        $aryQuery[] = $strReportCodeConditions;

        // Ģɼ�����ɻ���
        $aryQuery[] = " AND r.strReportKeyCode = '" . $strReportKeyCode . "'";
        // ��ӥ����˥ޥ��ʥ���̵��
        $aryQuery[] = " AND 0 <= ";
        $aryQuery[] = "( ";
        $aryQuery[] = "  SELECT MIN( s1.lngRevisionNo ) FROM m_slip s1 WHERE s1.bytInvalidFlag = false AND s1.strslipcode = s.strslipcode ";
        $aryQuery[] = ")";
        $aryQuery[] = " AND  r.strReportKeyCode = trim(to_char(s.lngslipno, '9999999'))";
    }

    // ���Ѹ���Ģɼ���ϥ��ԡ��ե������������������
    elseif ($lngReportClassCode == DEF_REPORT_ESTIMATE) {
        $aryQuery[] = "SELECT r.strReportPathName ";
        $aryQuery[] = "FROM t_Report r ";
        $aryQuery[] = "LEFT OUTER JOIN m_Estimate e ON ( to_number ( r.strReportKeyCode, '9999999') = e.lngEstimateNo ) ";
        $aryQuery[] = "WHERE r.lngReportClassCode = " . $lngReportClassCode;
        $aryQuery[] = $strReportCodeConditions;

        // �������ʥ�����
        $aryQuery[] = " AND e.lngEstimateNo = " . $strReportKeyCode;
    }
    return join("", $aryQuery);
}

// -----------------------------------------------------------------
/**
 *    Ģɼ�����������ؿ�
 *
 *    �����оݤ�Ģɼ���������������ؿ�
 *
 *    @param  Integer $lngClassCode Ģɼ��ʬ������
 *    @param  Integer $lngKeyCode   Ģɼ����������
 *    @param  Object  $objDB        DB���֥�������
 *    @return String                ������
 *    @access public
 */
// -----------------------------------------------------------------
function fncGetListOutputQuery($lngClassCode, $lngKeyCode, $objDB)
{
    /////////////////////////////////////////////////////////////////////////
    // ���ʲ�����
    /////////////////////////////////////////////////////////////////////////
    // �ܵ�����
    // ������            dtmUpdateDate
    // ����̾            strProductEnglishName
    // ���ʥ�����        strProductCode - strGoodsCode
    // ����̾(���ܸ�)    strProductName
    // ����̾(�Ѹ�)        strProductEnglishName
    // ����                strGroupDisplayCode strGroupDisplayName
    // ô����            strUserDisplayCode strUserDisplayName
    // �ܵ�                strCompanyDisplayCode strCompanyDisplayName
    // �ܵ�ô����        strUserDisplayCode strUserDisplayName
    // ���ʷ���            strProductFormName
    // ��Ȣ(��)����        lngBoxQuantity strProductUnitName
    // �����ȥ�����        lngCartonQuantity strProductUnitName
    // ����ͽ���        lngProductionQuantity
    // ���Ǽ�ʿ�        lngFirstDeliveryQuantity
    // Ǽ��                dtmDeliveryLimitDate
    // ��������            strCompanyDisplayCode strCompanyDisplayName
    // �������ݎ̎ގع���        strCompanyDisplayCode strCompanyDisplayName
    // Ǽ�ʾ��            strCompanyDisplayCode strCompanyDisplayName
    // Ǽ��                curProductPrice
    // ����                curRetailPrice
    // �о�ǯ��            strTargetAgeName
    // �ێ��Ԏ؎Î�(%)        lngRoyalty
    // �ڻ�                strCertificateClassName
    // �Ǹ���            strCopyrightName
    // �Ǹ�ɽ��(���)    strCopyrightDisplayStamp
    // �Ǹ�ɽ��(����ʪ)    strCopyrightDisplayPrint
    // ���ʹ���            strProductComposition
    // �������ݎ̎ގ�����        strAssemblyContents
    // ���;ܺ�            strSpecificationDetails
    if ($lngClassCode == DEF_REPORT_PRODUCT) {
        $aryQuery[] = "SELECT DISTINCT ON (p.lngProductNo)";
        $aryQuery[] = "   p.lngProductNo";
        $aryQuery[] = " , p.lngInChargeGroupCode as lngGroupCode";
        //  ������
        $aryQuery[] = " , To_Char( p.dtminsertdate, 'YYYY/MM/DD' ) as dtminsertdate";
        //  ������
        $aryQuery[] = " , To_Char( p.dtmUpdateDate, 'YYYY/MM/DD' ) as dtmUpdateDate";
        //  ���ʹԾ���
        $aryQuery[] = " , t_gp.lngGoodsPlanProgressCode";
        //  �����ֹ�
        $aryQuery[] = " , t_gp.lngRevisionNo";
        //  ��������
        $aryQuery[] = " , To_Char( p.dtmUpdateDate, 'YYYY/MM/DD' ) as dtmRevisionDate";
        //  ���ʥ�����
        $aryQuery[] = " , p.strProductCode";
        //  ����̾��
        $aryQuery[] = " , p.strProductName";
        //  ����̾�ΡʱѸ��
        $aryQuery[] = " , p.strProductEnglishName";
        //  ���ϼ�
        $aryQuery[] = " , input_u.strUserDisplayCode as strInputUserDisplayCode";
        $aryQuery[] = " , input_u.strUserDisplayName as strInputUserDisplayName";
        $aryQuery[] = " , p.lnginputusercode";
        //  ����
        $aryQuery[] = " , inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode";
        $aryQuery[] = " , inchg_g.strGroupDisplayName as strInChargeGroupDisplayName";
        //  ô����
        $aryQuery[] = " , inchg_u.strUserDisplayCode as strInChargeUserDisplayCode";
        $aryQuery[] = " , inchg_u.strUserDisplayName as strInChargeUserDisplayName";
        //  ��ȯô����
        $aryQuery[] = " , devp_u.strUserDisplayCode as strDevelopUserDisplayCode";
        $aryQuery[] = " , devp_u.strUserDisplayName as strDevelopUserDisplayName";
        $aryQuery[] = " , category.strCategoryName as strCategoryName";
        //  �ܵ�����
        $aryQuery[] = " , p.strGoodsCode";
        $aryQuery[] = " , cust_c.strDistinctCode";
        //  ����̾��
        $aryQuery[] = " , p.strGoodsName";
        //  �ܵ�
        $aryQuery[] = " , cust_c.strCompanyDisplayCode as strCustomerCompanyDisplayCode";
        $aryQuery[] = " , cust_c.strCompanyDisplayName as strCustomerCompanyDisplayName";
        //  �ܵ�ô����
        $aryQuery[] = " , cust_u.strUserDisplayCode as strCustomerUserDisplayCode";
        $aryQuery[] = " , cust_u.strUserDisplayName as strCustomerUserDisplayName";
        $aryQuery[] = " , p.lngCustomerUserCode";
        $aryQuery[] = " , p.strCustomerUserName";
        //  �ٻ�ñ��
        $aryQuery[] = " , packingunit.strProductUnitName as strPackingUnitName";
        //  ����ñ��
        $aryQuery[] = " , productunit.strProductUnitName as strProductUnitName";
        //  ���ʷ���
        $aryQuery[] = " , productform.strProductFormName";
        //  ��Ȣ���ޡ�����
        $aryQuery[] = " , To_char( p.lngBoxQuantity, '9,999,999,990' ) as lngBoxQuantity";
        //  �����ȥ�����
        $aryQuery[] = " , To_char( p.lngCartonQuantity, '9,999,999,990' ) as lngCartonQuantity";
        //  ����ͽ���
        $aryQuery[] = " , To_char( p.lngProductionQuantity, '9,999,999,990' ) as lngProductionQuantity";
        $aryQuery[] = " , productionunit.strProductUnitName AS strProductionUnitName";
        //  ���Ǽ�ʿ�
        $aryQuery[] = " , To_char( p.lngFirstDeliveryQuantity, '9,999,999,990' ) as lngFirstDeliveryQuantity";
        $aryQuery[] = " , firstdeliveryunit.strProductUnitName AS strFirstDeliveryUnitName";
        //  ��������
        $aryQuery[] = " , fact_c.strCompanyDisplayCode as strFactoryDisplayCode";
        $aryQuery[] = " , fact_c.strCompanyDisplayName as strFactoryDisplayName";
        //  ���å���֥깩��
        $aryQuery[] = " , assemfact_c.strCompanyDisplayCode as strAssemblyFactoryDisplayCode";
        $aryQuery[] = " , assemfact_c.strCompanyDisplayName as strAssemblyFactoryDisplayName";
        //  Ǽ�ʾ��
        $aryQuery[] = " , delv_c.strCompanyDisplayCode as strDeliveryPlaceDisplayCode";
        $aryQuery[] = " , delv_c.strCompanyDisplayName as strDeliveryPlaceDisplayName";
        //  Ǽ��
        $aryQuery[] = " , To_Char( p.dtmDeliveryLimitDate, 'YYYY/MM' ) as dtmDeliveryLimitDate";
        //  Ǽ��
        $aryQuery[] = " , To_char( p.curProductPrice, '9,999,999,990.99' )  as curProductPrice";
        //  ����
        $aryQuery[] = " , To_char( p.curRetailPrice, '9,999,999,990.99' )  as curRetailPrice";
        //  �о�ǯ��
        $aryQuery[] = " , targetage.strTargetAgeName";
        //  �����ƥ�
        $aryQuery[] = " , To_char( p.lngRoyalty, '9,999,999,990.99' )  as lngRoyalty";
        //  �ڻ�
        $aryQuery[] = " , certificate.strCertificateClassName";
        //  �Ǹ���
        $aryQuery[] = " , copyright.strCopyrightName";
        //  �Ǹ�������
        $aryQuery[] = " , p.strCopyrightNote";
        //  �Ǹ�ɽ���ʹ����
        $aryQuery[] = " , p.strCopyrightDisplayStamp";
        //  �Ǹ�ɽ���ʰ���ʪ��
        $aryQuery[] = " , p.strCopyrightDisplayPrint";
        //  ���ʹ���
        $aryQuery[] = " , p.strProductComposition";
        //  ���å���֥�����
        $aryQuery[] = " , p.strAssemblyContents";
        //  ���;ܺ�
        $aryQuery[] = " , p.strSpecificationDetails ";

        $aryQuery[] = " , t_gp.lngGoodsPlanCode ";

        $aryQuery[] = "FROM m_Product p";

        //  �ɲ�ɽ���Ѥλ��ȥޥ����б�
        $aryQuery[] = " LEFT JOIN m_User input_u ON p.lngInputUserCode = input_u.lngUserCode";
        $aryQuery[] = " LEFT JOIN m_Group inchg_g ON p.lngInChargeGroupCode = inchg_g.lngGroupCode";
        $aryQuery[] = " LEFT JOIN m_User inchg_u ON p.lngInChargeUserCode = inchg_u.lngUserCode";
        $aryQuery[] = " LEFT JOIN m_User devp_u ON p.lngdevelopusercode = devp_u.lngUserCode";
        $aryQuery[] = " LEFT JOIN m_Company cust_c ON p.lngCustomerCompanyCode = cust_c.lngCompanyCode";
        $aryQuery[] = " LEFT JOIN m_User cust_u ON p.lngCustomerUserCode = cust_u.lngUserCode";
        $aryQuery[] = " LEFT JOIN m_ProductUnit packingunit ON p.lngPackingUnitCode = packingunit.lngProductUnitCode";
        $aryQuery[] = " LEFT JOIN m_ProductUnit productunit ON p.lngProductUnitCode = productunit.lngProductUnitCode";
        $aryQuery[] = " LEFT JOIN m_ProductUnit productionunit ON p.lngProductionUnitCode = productionunit.lngProductUnitCode";
        $aryQuery[] = " LEFT JOIN m_ProductUnit firstdeliveryunit ON p.lngFirstDeliveryUnitCode = firstdeliveryunit.lngProductUnitCode";
        $aryQuery[] = " LEFT JOIN m_ProductForm productform ON p.lngProductFormCode = productform.lngProductFormCode";
        $aryQuery[] = " LEFT JOIN m_Company fact_c ON p.lngFactoryCode = fact_c.lngCompanyCode";
        $aryQuery[] = " LEFT JOIN m_Company assemfact_c ON p.lngAssemblyFactoryCode = assemfact_c.lngCompanyCode";
        $aryQuery[] = " LEFT JOIN m_Company delv_c ON p.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
        $aryQuery[] = " LEFT JOIN m_TargetAge targetage ON p.lngTargetAgeCode = targetage.lngTargetAgeCode";
        $aryQuery[] = " LEFT JOIN m_CertificateClass certificate ON p.lngCertificateClassCode = certificate.lngCertificateClassCode";
        $aryQuery[] = " LEFT JOIN m_Copyright copyright ON p.lngCopyrightCode = copyright.lngCopyrightCode";
        $aryQuery[] = " LEFT JOIN m_Category category ON p.lngCategoryCode = category.lngCategoryCode";
        $aryQuery[] = ", t_GoodsPlan t_gp ";

        $aryQuery[] = "WHERE p.lngProductNo = " . $lngKeyCode;

        $aryQuery[] = " AND t_gp.lngProductNo = p.lngProductNo";
        $aryQuery[] = " AND t_gp.lngRevisionNo = ( SELECT MAX( t_gp1.lngRevisionNo ) FROM t_GoodsPlan t_gp1 WHERE t_gp1.lngProductNo = p.lngProductNo )";
    }

    /////////////////////////////////////////////////////////////////////////
    // ȯ���
    /////////////////////////////////////////////////////////////////////////
    elseif ($lngClassCode == DEF_REPORT_ORDER) {
        $aryQuery[] = "select";
        $aryQuery[] = "  po.lngpurchaseorderno";
        $aryQuery[] = "  , po.lngrevisionno";
        $aryQuery[] = "  , po.strordercode";
        $aryQuery[] = "  , po.lngcustomercode";
        $aryQuery[] = "  , po.strcustomername";
        $aryQuery[] = "  , po.strcustomercompanyaddreess";
        $aryQuery[] = "  , po.strcustomercompanytel";
        $aryQuery[] = "  , po.strcustomercompanyfax";
        $aryQuery[] = "  , po.strproductcode";
        $aryQuery[] = "  , po.strrevisecode";
        $aryQuery[] = "  , po.strproductname";
        $aryQuery[] = "  , po.strproductenglishname";
        $aryQuery[] = "  , po.dtmexpirationdate";
        $aryQuery[] = "  , po.lngmonetaryunitcode";
        $aryQuery[] = "  , po.strmonetaryunitname";
        $aryQuery[] = "  , po.strmonetaryunitsign";
        $aryQuery[] = "  , po.lngmonetaryratecode";
        $aryQuery[] = "  , po.strmonetaryratename";
        $aryQuery[] = "  , po.lngpayconditioncode";
        $aryQuery[] = "  , po.strpayconditionname";
        $aryQuery[] = "  , po.lnggroupcode";
        $aryQuery[] = "  , po.strgroupname";
        $aryQuery[] = "  , po.txtsignaturefilename";
        $aryQuery[] = "  , po.lngusercode";
        $aryQuery[] = "  , po.strusername";
        $aryQuery[] = "  , po.lngdeliveryplacecode";
        $aryQuery[] = "  , po.strdeliveryplacename";
        $aryQuery[] = "  , po.curtotalprice";
        $aryQuery[] = "  , po.dtminsertdate";
        $aryQuery[] = "  , po.lnginsertusercode";
        $aryQuery[] = "  , po.strinsertusername";
        $aryQuery[] = "  , po.strnote";
        $aryQuery[] = "  , po.lngprintcount ";
        $aryQuery[] = "from";
        $aryQuery[] = "  m_purchaseorder po ";
        $aryQuery[] = "WHERE po.lngpurchaseorderno = " . $lngKeyCode;
    } else if ($lngClassCode == DEF_REPORT_SLIP) {
        $aryQuery[] = "select";
        $aryQuery[] = "  lngslipno";
        $aryQuery[] = "  , lngrevisionno";
        $aryQuery[] = "  , strslipcode";
        $aryQuery[] = "  , lngsalesno";
        $aryQuery[] = "  , strcustomercode";
        $aryQuery[] = "  , strcustomercompanyname";
        $aryQuery[] = "  , strcustomername";
        $aryQuery[] = "  , strcustomeraddress1";
        $aryQuery[] = "  , strcustomeraddress2";
        $aryQuery[] = "  , strcustomeraddress3";
        $aryQuery[] = "  , strcustomeraddress4";
        $aryQuery[] = "  , strcustomerphoneno";
        $aryQuery[] = "  , strcustomerfaxno";
        $aryQuery[] = "  , strcustomerusername";
        $aryQuery[] = "  , to_char(dtmdeliverydate, 'yyyy/mm/dd') as dtmdeliverydate";
        $aryQuery[] = "  , lngdeliveryplacecode";
        $aryQuery[] = "  , strdeliveryplacename";
        $aryQuery[] = "  , strdeliveryplaceusername";
        $aryQuery[] = "  , strusercode";
        $aryQuery[] = "  , strusername";
        $aryQuery[] = "  , to_char(curtotalprice, '9,999,999,990') AS curtotalprice";
        $aryQuery[] = "  , trunc(curtotalprice) AS curtotalprice_comm";
        $aryQuery[] = "  , lngmonetaryunitcode";
        $aryQuery[] = "  , strmonetaryunitsign";
        $aryQuery[] = "  , lngtaxclasscode";
        $aryQuery[] = "  , strtaxclassname";
        $aryQuery[] = "  , curtax";
        $aryQuery[] = "  , lngpaymentmethodcode";
        $aryQuery[] = "  , to_char(dtmpaymentlimit, 'dd/mm/yyyy') as dtmpaymentlimit";
        $aryQuery[] = "  , dtminsertdate";
        $aryQuery[] = "  , strnote";
        $aryQuery[] = "  , strshippercode ";
        $aryQuery[] = "from";
        $aryQuery[] = "  m_slip ";
        $aryQuery[] = "WHERE lngslipno = " . $lngKeyCode;
    }

    return join("", $aryQuery);
}

// -----------------------------------------------------------------
/**
 *    ��̾���᡼���ե�����¸�ߥ����å��ؿ�
 *
 *    �����桼�����ν�̾���᡼���ե����뤬¸�ߤ��뤫�ɤ��������å�����ؿ�
 *
 *    GIF / JEPG ���б�
 *
 *    @param  String  $strPath  �ե������Ǽ�ǥ��쥯�ȥ�ѥ�
 *    @param  Number  $lnguc    �����桼����������
 *    @access public
 *
 *    @return bool
 */
// -----------------------------------------------------------------
function fncSignatureCheckFile($strPath, $lnguc)
{
    $bl = false;
    $dh = opendir($strPath);

    while ($file = readdir($dh)) {
        $file = strtolower($file);

        if ($file == $lnguc . ".gif" || $file == $lnguc . ".jpg" || $file == $lnguc . ".jpeg") {
            $bl = true;
            break;
        } else {
            $bl = false;
        }
    }

    closedir($dh);

    return $bl;
}

/**
 * �����襳���ɤˤ��Ǽ����ɼ���̼��������������
 *
 * @param [type] $strShipperCode
 * @return void
 */
function fncGetSlipKindQuery($strShipperCode)
{
    $aryQuery[] = "select";
    $aryQuery[] = "  sk.lngslipkindcode";
    $aryQuery[] = "  , sk.strslipkindname";
    $aryQuery[] = "  , sk.lngmaxline ";
    $aryQuery[] = "from";
    $aryQuery[] = "  m_stockcompanycode sc ";
    $aryQuery[] = "  inner join m_slipkindrelation skr ";
    $aryQuery[] = "    on sc.lngcompanyno = skr.lngcompanycode ";
    $aryQuery[] = "  inner join m_slipkind sk ";
    $aryQuery[] = "    on sk.lngslipkindcode = skr.lngslipkindcode ";
    $aryQuery[] = "where";
    $aryQuery[] = "  sc.strstockcompanycode = '" . $strShipperCode . "'";
    return join("", $aryQuery);
}

/**
 * Ǽ�ʾܺټ��������������
 *
 * @param [type] $strReportKeyCode
 * @return void
 */
function fncGetSlipDetailQuery($strReportKeyCode)
{
    $aryQuery[] = "select";
    $aryQuery[] = "  lngslipno";
    $aryQuery[] = "  , lngslipdetailno";
    $aryQuery[] = "  , lngrevisionno";
    $aryQuery[] = "  , strcustomersalescode";
    $aryQuery[] = "  , lngsalesclasscode";
    $aryQuery[] = "  , strsalesclassname";
    $aryQuery[] = "  , strgoodscode";
    $aryQuery[] = "  , strproductcode";
    $aryQuery[] = "  , strrevisecode";
    $aryQuery[] = "  , strproductname";
    $aryQuery[] = "  , strproductenglishname";
    $aryQuery[] = "  , to_char(curproductprice, '9,999,999,990') AS curproductprice";
    $aryQuery[] = "  , lngquantity";
    $aryQuery[] = "  , to_char(lngproductquantity, '9,999,999,990') AS lngproductquantity";
    $aryQuery[] = "  , lngproductunitcode";
    $aryQuery[] = "  , strproductunitname";
    $aryQuery[] = "  , to_char(cursubtotalprice, '9,999,999,990') AS cursubtotalprice";
    $aryQuery[] = "  , trunc(cursubtotalprice) AS cursubtotalprice_comm";
    $aryQuery[] = "  , strnote ";
    $aryQuery[] = "from";
    $aryQuery[] = "  t_slipdetail ";
    $aryQuery[] = "where";
    $aryQuery[] = "  lngslipno = " . $strReportKeyCode;
    $aryQuery[] = " ORDER BY";
    $aryQuery[] = "  lngSortKey";

    return join("", $aryQuery);
}

/**
 * Ǽ�ʾܺټ��������������
 *
 * @param [type] $strReportKeyCode
 * @return void
 */
function fncGetSlipDetailForDownloadQuery($strReportKeyCode)
{
    $aryQuery[] = "select";
    $aryQuery[] = "  lngslipno";
    $aryQuery[] = "  , lngslipdetailno";
    $aryQuery[] = "  , lngrevisionno";
    $aryQuery[] = "  , strcustomersalescode";
    $aryQuery[] = "  , lngsalesclasscode";
    $aryQuery[] = "  , strsalesclassname";
    $aryQuery[] = "  , strgoodscode";
    $aryQuery[] = "  , strproductcode";
    $aryQuery[] = "  , strrevisecode";
    $aryQuery[] = "  , strproductname";
    $aryQuery[] = "  , strproductenglishname";
    $aryQuery[] = "  , curproductprice";
    $aryQuery[] = "  , lngquantity";
    $aryQuery[] = "  , lngproductquantity";
    $aryQuery[] = "  , lngproductunitcode";
    $aryQuery[] = "  , strproductunitname";
    $aryQuery[] = "  , cursubtotalprice";
    $aryQuery[] = "  , strnote ";
    $aryQuery[] = "from";
    $aryQuery[] = "  t_slipdetail ";
    $aryQuery[] = "where";
    $aryQuery[] = "  lngslipno = " . $strReportKeyCode;
    $aryQuery[] = " ORDER BY";
    $aryQuery[] = "  lngSortKey";

    return join("", $aryQuery);
}

/**
 * Ǽ�ʾܺټ��������������
 *
 * @param [type] $strReportKeyCode
 * @return void
 */
function fncGetSlipForDownloadQuery($strReportKeyCode)
{
    $aryQuery[] = "select";
    $aryQuery[] = "  lngslipno";
    $aryQuery[] = "  , lngrevisionno";
    $aryQuery[] = "  , strslipcode";
    $aryQuery[] = "  , lngsalesno";
    $aryQuery[] = "  , strcustomercode";
    $aryQuery[] = "  , strcustomercompanyname";
    $aryQuery[] = "  , strcustomername";
    $aryQuery[] = "  , strcustomeraddress1";
    $aryQuery[] = "  , strcustomeraddress2";
    $aryQuery[] = "  , strcustomeraddress3";
    $aryQuery[] = "  , strcustomeraddress4";
    $aryQuery[] = "  , strcustomerphoneno";
    $aryQuery[] = "  , strcustomerfaxno";
    $aryQuery[] = "  , strcustomerusername";
    $aryQuery[] = "  , dtmdeliverydate";
    $aryQuery[] = "  , lngdeliveryplacecode";
    $aryQuery[] = "  , strdeliveryplacename";
    $aryQuery[] = "  , strdeliveryplaceusername";
    $aryQuery[] = "  , strusercode";
    $aryQuery[] = "  , strusername";
    $aryQuery[] = "  , curtotalprice";
    $aryQuery[] = "  , lngmonetaryunitcode";
    $aryQuery[] = "  , strmonetaryunitsign";
    $aryQuery[] = "  , lngtaxclasscode";
    $aryQuery[] = "  , strtaxclassname";
    $aryQuery[] = "  , curtax";
    $aryQuery[] = "  , lngpaymentmethodcode";
    $aryQuery[] = "  , to_char(dtmpaymentlimit, 'dd/mm/yyyy') as dtmpaymentlimit";
    $aryQuery[] = "  , dtminsertdate";
    $aryQuery[] = "  , strnote";
    $aryQuery[] = "  , strshippercode ";
    $aryQuery[] = "from";
    $aryQuery[] = "  m_slip ";
    $aryQuery[] = "WHERE lngslipno = " . $strReportKeyCode;
    return join("", $aryQuery);
}
return true;
