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
        $aryQuery[] = "FROM m_Order o, t_Report r ";

        // �о�Ģɼ(���ʴ�� or ȯ��)����
        $aryQuery[] = "WHERE r.lngReportClassCode = " . $lngReportClassCode;

        $aryQuery[] = $strReportCodeConditions;

        // Ģɼ�����ɻ���
        $aryQuery[] = " AND r.strReportKeyCode = '" . $strReportKeyCode . "'";

        // A:��ȯ��׾��֤���礭�����֤�ȯ��ǡ���
        // B:��ȯ��׾��֤Υǡ���
        // A OR B
        $aryQuery[] = " AND (";
        // A:��ȯ��׾��֤���礭�����֤�ȯ��ǡ���
        $aryQuery[] = " o.lngOrderStatusCode > " . DEF_ORDER_ORDER;
        $aryQuery[] = " OR";
        // B:��ȯ��׾��֤Υǡ���
        $aryQuery[] = " o.lngOrderStatusCode = " . DEF_ORDER_ORDER;
        $aryQuery[] = ")";
        // ��ӥ����˥ޥ��ʥ���̵��
        $aryQuery[] = " AND 0 <= ";
        $aryQuery[] = "( ";
        $aryQuery[] = "  SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode ";
        $aryQuery[] = ")";
        $aryQuery[] = " AND  r.strReportKeyCode = trim(to_char(o.lngOrderNo, '9999999'))";
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
    // ��Ͽ��            dtmInsertDate
    // �׾���            dtmOrderAppDate
    // ȯ��No            strOrderCode
    // ���ϼ�            strInputUserDisplayName
    // ������            strCustomerDisplayName
    // ������(����)        strAddress*
    // ������(TEL)        strTel1
    // ������(FAX)        strFax1
    // ������(�ȿ�)        lngOrganizationCode
    // ����                strInChargeGroupDisplayName
    // ô����            strInChargeUserDisplayName
    // Ǽ�ʾ��            strDeliveryDisplayName
    // �̲�                strMonetaryUnitSign
    // �졼�ȥ�����        strMonetaryRateName
    // �����졼��        curConversionRate
    // ����                strOrderStatusName
    // ��ʧ���            strPayConditionName
    // ȯ��ͭ��������    dtmExpirationDate
    // ����                o.strNote
    // ��׶��            curTotalPrice
    // �ǽ���ǧ��̾        strInChargeUserName
    elseif ($lngClassCode == DEF_REPORT_ORDER) {
        $aryQuery[] = "SELECT distinct on (o.lngOrderNo) o.lngOrderNo, o.lngRevisionNo";

        // ��Ͽ��
        $aryQuery[] = ", To_Char( o.dtmInsertDate, 'YYYY/MM/DD' ) as dtmInsertDate";
        // �׾���
        $aryQuery[] = ", To_Char( o.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmOrderAppDate";
        // ȯ��No
        $aryQuery[] = ", o.strOrderCode as strOrderCode";
        // ������
        $aryQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
        $aryQuery[] = ", CASE";
        $aryQuery[] = "    WHEN cust_c.bytOrganizationFront = TRUE AND cust_c.lngOrganizationCode < 10";
        $aryQuery[] = "      THEN org.strOrganizationName || cust_c.strCompanyName";
        $aryQuery[] = "    WHEN cust_c.bytOrganizationFront = FALSE AND cust_c.lngOrganizationCode < 10";
        $aryQuery[] = "      THEN cust_c.strCompanyName || org.strOrganizationName";
        $aryQuery[] = "    ELSE cust_c.strCompanyName";
        $aryQuery[] = "  END AS strCustomerDisplayName";
        $aryQuery[] = ", cust_c.strAddress1 as strCustomerAddress1";
        $aryQuery[] = ", cust_c.strAddress2 as strCustomerAddress2";
        $aryQuery[] = ", cust_c.strAddress3 as strCustomerAddress3";
        $aryQuery[] = ", cust_c.strAddress4 as strCustomerAddress4";
        $aryQuery[] = ", cust_c.strTel1";
        $aryQuery[] = ", cust_c.strFax1";
        $aryQuery[] = ", cust_c.lngOrganizationCode";
        // ����
        $aryQuery[] = ", inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode";
        $aryQuery[] = ", inchg_g.strGroupDisplayName as strInChargeGroupDisplayName";
        // ô����
        $aryQuery[] = ", inchg_u.strUserDisplayCode as strInChargeUserDisplayCode";
        // ô���Ԣ�Group̾�λ���
        $aryQuery[] = ", inchg_g.strGroupDisplayName as strInChargeUserDisplayName";
        // Ǽ�ʾ��
        $aryQuery[] = ", delv_c.strCompanyDisplayCode as strDeliveryDisplayCode";
        $aryQuery[] = ", delv_c.strCompanyDisplayName as strDeliveryDisplayName";
        // �̲�
        $aryQuery[] = ", mu.strMonetaryUnitName";
        $aryQuery[] = ", mu.strMonetaryUnitSign";
        // �졼�ȥ�����
        $aryQuery[] = ", mr.strMonetaryRateName";
        // �����졼��
        $aryQuery[] = ", o.curConversionRate";
        // ����
        $aryQuery[] = ", os.strOrderStatusName";
        // ��ʧ���
        $aryQuery[] = ", pc.strPayConditionName";
        // ȯ��ͭ��������
        $aryQuery[] = ", To_Char( o.dtmExpirationDate, 'YYYY/MM/DD' ) as dtmExpirationDate";
        $aryQuery[] = " FROM m_Order o";
        // ȯ��-���
        $aryQuery[] = " LEFT JOIN m_Company cust_c ON o.lngCustomerCompanyCode = cust_c.lngCompanyCode";
        // ȯ��-���
        $aryQuery[] = " LEFT JOIN m_Company delv_c ON o.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
        $aryQuery[] = " LEFT JOIN m_OrderStatus os USING (lngOrderStatusCode)";
        // ȯ��-��ʧ���
        $aryQuery[] = " LEFT JOIN m_PayCondition pc ON o.lngPayConditionCode = pc.lngPayConditionCode";
        // ȯ��-�̲ߥ졼��
        $aryQuery[] = " LEFT JOIN m_MonetaryUnit mu ON o.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
        // ȯ��-�̲ߥ졼�ȼ���
        $aryQuery[] = " LEFT JOIN m_MonetaryRateClass mr ON o.lngMonetaryRateCode = mr.lngMonetaryRateCode";
        // ȯ��-ȯ��ܺ�
        $aryQuery[] = " LEFT JOIN t_OrderDetail od ON o.lngOrderNo = od.lngOrderNo";
        //ȯ��-����
        $aryQuery[] = " LEFT JOIN ( ";
        $aryQuery[] = " select p1.*  from m_product p1 ";
        $aryQuery[] = " inner join (select max(lngproductno) lngproductno, strproductcode from m_Product group by strProductCode) p2";
        $aryQuery[] = " on p1.lngproductno = p2.lngproductno";
        $aryQuery[] = " ) p ON od.strProductcode = p.strProductcode";
        // ���롼��-ȯ��
        $aryQuery[] = " LEFT JOIN m_Group inchg_g ON p.lnginchargegroupcode = inchg_g.lngGroupCode";
        // �桼����-ȯ��
        $aryQuery[] = " LEFT JOIN m_User inchg_u ON p.lnginchargeusercode = inchg_u.lngUserCode";
        // �ȿ�
        $aryQuery[] = " LEFT JOIN m_Organization org ON cust_c.lngOrganizationCode = org.lngOrganizationCode";

        $aryQuery[] = " WHERE o.lngOrderNo = " . $lngKeyCode;
        // A:��ȯ��׾��֤���礭�����֤�ȯ��ǡ���
        $aryQuery[] = " AND o.lngOrderStatusCode >= " . DEF_ORDER_ORDER;
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

return true;
