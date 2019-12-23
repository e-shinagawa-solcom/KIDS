<?
// ----------------------------------------------------------------------------
/**
 *       ȯ�����  ������Ϣ�ؿ���
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
 *         ��������̴�Ϣ�δؿ�
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------

/**
 * �������ܤ�����פ���ǿ���ȯ��ǡ������������SQLʸ�κ����ؿ�
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
function fncGetSearchPurchaseSQL($displayColumns, $searchColumns, $from, $to, $searchValue, $optionColumns)
{
    $detailConditionCount = 0;
    // ���������Ω��
    $aryQuery = array();
    $aryQuery[] = "SELECT";
    $aryQuery[] = "  distinct ";
    $aryQuery[] = "  o.lngOrderNo as lngOrderNo";
    $aryQuery[] = "  , o.lngOrderNo as lngpkno";
    $aryQuery[] = "  , o.lngRevisionNo as lngRevisionNo";
    $aryQuery[] = "  , od.lngOrderDetailNo";
    $aryQuery[] = "  , od.lngOrderDetailNo as lngdetailno";
    $aryQuery[] = "  , o.strOrderCode as strOrderCode";
    $aryQuery[] = "  , o.strOrderCode || '_' || to_char(o.lngRevisionNo, 'FM00') as strOrderCode_desc";
    $aryQuery[] = "  , to_char(o.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS') as dtmInsertDate";
    $aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
    $aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
    $aryQuery[] = "  , cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
    $aryQuery[] = "  , cust_c.strCompanyDisplayName as strCustomerDisplayName";
    $aryQuery[] = "  , o.lngOrderStatusCode as lngOrderStatusCode";
    $aryQuery[] = "  , os.strOrderStatusName as strOrderStatusName";
    $aryQuery[] = "  , mm.lngMonetaryUnitCode as lngMonetaryUnitCode";
    $aryQuery[] = "  , mm.strMonetaryUnitSign as strMonetaryUnitSign";
    $aryQuery[] = "  , od.strProductCode";
    $aryQuery[] = "  , od.strProductName";
    $aryQuery[] = "  , od.strProductEnglishName";
    $aryQuery[] = "  , od.lngInChargeGroupCode as strgroupdisplaycode";
    $aryQuery[] = "  , od.strInChargeGroupName as strgroupdisplayname";
    $aryQuery[] = "  , od.lngInChargeUserCode as struserdisplaycode";
    $aryQuery[] = "  , od.strInChargeUserName as struserdisplayname";
    $aryQuery[] = "  , od.lngStockSubjectCode";
    $aryQuery[] = "  , od.strStockSubjectName";
    $aryQuery[] = "  , od.lngStockItemCode";
    $aryQuery[] = "  , od.strstockitemname";
    $aryQuery[] = "  , od.dtmDeliveryDate";
    $aryQuery[] = "  , od.curProductPrice";
    $aryQuery[] = "  , od.lngProductQuantity";
    $aryQuery[] = "  , od.curSubTotalPrice";
    $aryQuery[] = "  , od.strDetailNote ";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  m_Order o ";
    $aryQuery[] = "  INNER JOIN ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      lngorderno";
    $aryQuery[] = "      , MAX(lngrevisionno) as lngrevisionno ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_order ";
    $aryQuery[] = "    group by";
    $aryQuery[] = "      lngorderno";
    $aryQuery[] = "  ) rev ";
    $aryQuery[] = "    on rev.lngorderno = o.lngorderno ";
    $aryQuery[] = "    and rev.lngrevisionno = o.lngrevisionno ";
    $aryQuery[] = "  LEFT JOIN m_User input_u ";
    $aryQuery[] = "    ON o.lngInputUserCode = input_u.lngUserCode ";
    $aryQuery[] = "  LEFT JOIN m_Company cust_c ";
    $aryQuery[] = "    ON o.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
    $aryQuery[] = "  LEFT JOIN m_OrderStatus os ";
    $aryQuery[] = "    USING (lngOrderStatusCode) ";
    $aryQuery[] = "  LEFT JOIN m_MonetaryUnit mm ";
    $aryQuery[] = "    ON o.lngMonetaryUnitCode = mm.lngMonetaryUnitCode";
    $aryQuery[] = "  , ( ";
    $aryQuery[] = "      select";
    $aryQuery[] = "        od1.lngorderno";
    $aryQuery[] = "        , od1.lngorderDetailNo";
    $aryQuery[] = "        , od1.lngRevisionNo";
    $aryQuery[] = "        , od1.strProductCode || '_' || od1.strReviseCode as strProductCode";
    $aryQuery[] = "        , mp.strProductName as strProductName";
    $aryQuery[] = "        , mp.strProductEnglishName as strProductEnglishName";
    $aryQuery[] = "        , mg.strgroupdisplaycode as lngInChargeGroupCode";
    $aryQuery[] = "        , mg.strgroupdisplayname as strInChargeGroupName";
    $aryQuery[] = "        , mu.struserdisplaycode as lngInChargeUserCode";
    $aryQuery[] = "        , mu.struserdisplayname as strInChargeUserName";
    $aryQuery[] = "        , od1.lngStockSubjectCode as lngStockSubjectCode";
    $aryQuery[] = "        , ss.strStockSubjectName as strStockSubjectName";
    $aryQuery[] = "        , od1.lngStockItemCode as lngStockItemCode";
    $aryQuery[] = "        , si.strstockitemname as strstockitemname";
    $aryQuery[] = "        , to_char(od1.dtmDeliveryDate, 'YYYY/MM/DD') as dtmDeliveryDate";
    $aryQuery[] = "        , to_char(od1.curProductPrice, '9,999,999,990.9999') as curProductPrice";
    $aryQuery[] = "        , to_char(od1.lngProductQuantity, '9,999,999,990') as lngProductQuantity";
    $aryQuery[] = "        , to_char(od1.curSubTotalPrice, '9,999,999,990.99') as curSubTotalPrice";
    $aryQuery[] = "        , od1.strNote as strDetailNote ";
    $aryQuery[] = "      from";
    $aryQuery[] = "        t_orderdetail od1 ";
    $aryQuery[] = "        LEFT JOIN m_product mp ";
    $aryQuery[] = "          on mp.strproductcode = od1.strproductcode ";
    $aryQuery[] = "          and mp.strrevisecode = od1.strrevisecode ";
    $aryQuery[] = "          and mp.lngrevisionno = od1.lngrevisionno ";
    $aryQuery[] = "        LEFT JOIN m_group mg ";
    $aryQuery[] = "          on mg.lnggroupcode = mp.lnginchargegroupcode ";
    $aryQuery[] = "        LEFT JOIN m_user mu ";
    $aryQuery[] = "          on mu.lngusercode = mp.lnginchargeusercode ";
    $aryQuery[] = "        LEFT JOIN m_StockSubject ss ";
    $aryQuery[] = "          ON od1.lngstocksubjectcode = ss.lngstocksubjectcode ";
    $aryQuery[] = "        LEFT JOIN m_stockitem si ";
    $aryQuery[] = "          ON od1.lngstockitemcode = si.lngstockitemcode ";
    $aryQuery[] = "          AND od1.lngstocksubjectcode = si.lngstocksubjectcode";

    //���ʥ�����
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
                $aryQuery[] = "(od1.strProductCode" .
                " between '" . explode("-", $strProductCode)[0] . "'" .
                " AND " . "'" . explode("-", $strProductCode)[1] . "')";
            } else {
                if (strpos($strProductCode, '_') !== false) {
                    $aryQuery[] = "od1.strProductCode = '" . explode("_", $strProductCode)[0] . "'";
                    $aryQuery[] = " AND od1.strrevisecode = '" . explode("_", $strProductCode)[1] . "'";
                } else {
                    $aryQuery[] = "od1.strProductCode = '" . $strProductCode . "'";
                }
            }
        }
        $aryQuery[] = ")";
    }
    // ����̾�Ρ����ܸ��
    if (array_key_exists("strProductName", $searchColumns) &&
        array_key_exists("strProductName", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "UPPER( mp.strProductName ) LIKE UPPER( '%" . $searchValue["strProductName"] . "%' ) ";
    }
    // ����̾�ΡʱѸ��
    if (array_key_exists("strProductEnglishName", $searchColumns) &&
        array_key_exists("strProductEnglishName", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "UPPER( mp.strProductEnglishName ) LIKE UPPER( '%" . $searchValue["strProductEnglishName"] . "%' ) ";
    }

    // ����
    if (array_key_exists("lngInChargeGroupCode", $searchColumns) &&
        array_key_exists("lngInChargeGroupCode", $searchValue)) {
            $detailConditionCount += 1;
            $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = " mg.strGroupDisplayCode = '" . $searchValue["lngInChargeGroupCode"] . "'";

    }
    // ô����
    if (array_key_exists("lngInChargeUserCode", $searchColumns) &&
        array_key_exists("lngInChargeUserCode", $searchValue)) {
            $detailConditionCount += 1;
            $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = " mu.strUserDisplayCode = '" . $searchValue["lngInChargeUserCode"] . "'";

    }

    // ��������
    if (array_key_exists("lngStockSubjectCode", $searchColumns) &&
        array_key_exists("lngStockSubjectCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "od1.lngStockSubjectCode = " . $searchValue["lngStockSubjectCode"] . " ";
    }
    // ��������
    if (array_key_exists("lngStockItemCode", $searchColumns) &&
        array_key_exists("lngStockItemCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        
        $aryQuery[] = "od1.lngStockSubjectCode = " . explode("-", $searchValue["lngStockItemCode"])[0] . " ";
        $aryQuery[] = " AND od1.lngStockItemCode = " . explode("-", $searchValue["lngStockItemCode"])[1] . " ";
    }

    // Ǽ��
    if (array_key_exists("dtmDeliveryDate", $searchColumns) &&
        array_key_exists("dtmDeliveryDate", $from) && $from["dtmDeliveryDate"] != '') {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "od1.dtmDeliveryDate >= '" . $from["dtmDeliveryDate"] . "' ";
    }
    if (array_key_exists("dtmDeliveryDate", $searchColumns) &&
        array_key_exists("dtmDeliveryDate", $to) && $to["dtmDeliveryDate"] != '') {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "od1.dtmDeliveryDate <= '" . $to["dtmDeliveryDate"] . "' ";
    }
    $aryQuery[] = "    ) od ";
    $aryQuery[] = "WHERE";
    $aryQuery[] = "  od.lngorderno = o.lngorderno ";
    $aryQuery[] = "  AND od.lngRevisionNo = o.lngRevisionNo ";

    // ////ȯ��ޥ�����θ������////
    // ��Ͽ��
    if (array_key_exists("dtmInsertDate", $searchColumns) &&
        array_key_exists("dtmInsertDate", $from) && $from["dtmInsertDate"] != '') {
        $dtmSearchDate = $from["dtmInsertDate"] . " 00:00:00";
        $aryQuery[] = " AND o.dtmInsertDate >= '" . $dtmSearchDate . "'";
    }
    if (array_key_exists("dtmInsertDate", $searchColumns) &&
        array_key_exists("dtmInsertDate", $to) && $to["dtmInsertDate"] != '') {
        $dtmSearchDate = $to["dtmInsertDate"] . " 23:59:59.99999";
        $aryQuery[] = " AND o.dtmInsertDate <= '" . $dtmSearchDate . "'";
    }

    // �׾���
    if (array_key_exists("dtmAppropriation", $searchColumns) &&
        array_key_exists("dtmAppropriation", $from) && $from["dtmAppropriation"] != '') {
        $dtmSearchDate = $from["dtmOrderAppDateFrom"] . " 00:00:00";
        $aryQuery[] = " AND o.dtmAppropriationDate >= '" . $dtmSearchDate . "'";
    }
    if (array_key_exists("dtmAppropriation", $searchColumns) &&
        array_key_exists("dtmAppropriation", $to) && $to["dtmAppropriation"] != '') {
        $dtmSearchDate = $to["dtmAppropriation"] . " 23:59:59.99999";
        $aryQuery[] = " AND o.dtmAppropriationDate <= '" . $dtmSearchDate . "'";
    }
    // ȯ��Σ�
    if (array_key_exists("strOrderCode", $searchColumns) &&
        array_key_exists("strOrderCode", $searchValue)) {
        $strOrdereCodeArray = explode(",", $searchValue["strOrderCode"]);
        $aryQuery[] = " AND (";
        $count = 0;
        foreach ($strOrdereCodeArray as $strOrderCode) {
            $count += 1;
            if ($count != 1) {
                $aryQuery[] = " OR ";
            }
            if (strpos($strOrderCode, '-') !== false) {
                $aryQuery[] = "(o.strOrderCode" .
                " between '" . explode("-", $strOrderCode)[0] . "'" .
                " AND " . "'" . explode("-", $strOrderCode)[1] . "')";
            } else {
                if (strpos($strOrderCode, '_') !== false) {                    
                    if (explode("_", $strOrderCode)[1] == '00') {
                        $aryQuery[] = " o.strOrderCode = '" . explode("_", $strOrderCode)[0] . "'";
                        $aryQuery[] = " AND o.lngrevisionno = 0 ";
                    } else {                        
                        $aryQuery[] = " o.strOrderCode = '" . explode("_", $strOrderCode)[0] . "'";
                        $aryQuery[] = " AND o.lngrevisionno = " . ltrim(explode("_", $strOrderCode)[1], '0') . "";
                    }
                } else {
                    $aryQuery[] = "o.strOrderCode = '" . $strOrderCode . "'";
                }
            }
        }
        $aryQuery[] = ")";
    }

    // if (array_key_exists("strOrderCode", $searchColumns) &&
    //     array_key_exists("strOrderCode", $from) && $from["strOrderCode"] != '') {
    //     if (strpos($from["strOrderCode"], "-")) {
    //         // ��Х����������դ�ȯ��Σ�Υ�Х��������ɤϸ�����̤ǤϺǿ��Ǥ�ɽ�����뤿�ᡢ̵�뤹��
    //         $strNewOrderCode_from = preg_replace(strstr($from["strOrderCode"], "_"), "", $from["strOrderCode"]);
    //     } else {
    //         $strNewOrderCode_from = $from["strOrderCode"];
    //     }
    // }
    // if (array_key_exists("strOrderCode", $searchColumns) &&
    //     array_key_exists("strOrderCode", $to) && $to["strOrderCode"] != '') {
    //     if (strpos($to["strOrderCode"], "-")) {
    //         // ��Х����������դ�ȯ��Σ�Υ�Х��������ɤϸ�����̤ǤϺǿ��Ǥ�ɽ�����뤿�ᡢ̵�뤹��
    //         $strNewOrderCode_to = preg_replace(strstr($to["strOrderCode"], "_"), "", $to["strOrderCode"]);
    //     } else {
    //         $strNewOrderCode_to = $to["strOrderCode"];
    //     }
    // }
    // if (($strNewOrderCode_from && $strNewOrderCode_to) && ($strNewOrderCode_from == $strNewOrderCode_to)) {
    //     // from��to��Ʊ���ͤξ��ϡ��ϰϻ���ǤϤʤ�"="�ǻ���"
    //     $aryQuery[] = " AND o.strOrderCode = '" . $strNewOrderCode_to . "'";
    // } else {
    //     if ($strNewOrderCode_from) {
    //         $aryQuery[] = " AND o.strOrderCode >= '" . $strNewOrderCode_from . "'";
    //     }
    //     if ($strNewOrderCode_to) {
    //         $aryQuery[] = " AND o.strOrderCode <= '" . $strNewOrderCode_to . "'";
    //     }
    // }
    // ���ϼ�
    if (array_key_exists("lngInputUserCode", $searchColumns) &&
        array_key_exists("lngInputUserCode", $searchValue)) {
        $aryQuery[] = " AND input_u.strUserDisplayCode ~* '" . $searchValue["lngInputUserCode"] . "'";
    }
    // ������
    if (array_key_exists("lngCustomerCode", $searchColumns) &&
        array_key_exists("lngCustomerCode", $searchValue)) {
        $aryQuery[] = " AND cust_c.strCompanyDisplayCode ~* '" . $searchValue["lngCustomerCode"] . "'";
    }
    // ����
    if (array_key_exists("lngOrderStatusCode", $searchColumns) &&
        array_key_exists("lngOrderStatusCode", $searchValue)) {
        // ȯ����֤� ","���ڤ��ʸ����Ȥ����Ϥ����
        //$arySearchStatus = explode( ",", $arySearchDataColumn["lngOrderStatusCode"] );
        // �����å��ܥå������ˤ�ꡢ����򤽤Τޤ�����
        $arySearchStatus = $searchValue["lngOrderStatusCode"];

        if (is_array($arySearchStatus)) {
            $aryQuery[] = " AND ( ";
            // ȯ����֤�ʣ�����ꤵ��Ƥ����ǽ��������Τǡ�����Ŀ�ʬ�롼��
            for ($j = 0; $j < count($arySearchStatus); $j++) {
                // ������
                if ($j != 0) {
                    $aryQuery[] = " OR ";
                }
                $aryQuery[] = "o.lngOrderStatusCode = " . $arySearchStatus[$j] . "";
            }
            $aryQuery[] = " ) ";
        }
    }
    // ��ʧ���
    if (array_key_exists("lngPayConditionCode", $searchColumns) &&
        array_key_exists("lngPayConditionCode", $searchValue)) {
        $aryQuery[] = " AND o.lngPayConditionCode = " . $searchValue["lngPayConditionCode"] . "";
    }
    if (!array_key_exists("admin", $optionColumns)) {
        $aryQuery[] = "  AND not exists ( ";
        $aryQuery[] = "    select";
        $aryQuery[] = "      lngorderno ";
        $aryQuery[] = "    from";
        $aryQuery[] = "      m_Order ";
        $aryQuery[] = "    where";
        $aryQuery[] = "      strOrderCode = o.strOrderCode ";
        $aryQuery[] = "      and lngrevisionno < 0";
        $aryQuery[] = "  ) ";
    } else {
        $aryQuery[] = " AND o.bytInvalidFlag = FALSE ";
        $aryQuery[] = " AND o.lngRevisionNo >= 0";
    }
    $aryQuery[] = "ORDER BY";
    $aryQuery[] = "  o.lngOrderNo DESC";

    return implode("\n", $aryQuery);
}

/**
 * �������ܤ�����פ���ǿ���ȯ���ǡ������������SQLʸ�κ����ؿ�
 *
 *    �������ܤ��� SQLʸ���������
 *
 *    @param  Array     $aryViewColumn             ɽ���оݥ����̾������
 *    @param  Array     $arySearchColumn         �����оݥ����̾������
 *    @param  Array     $arySearchDataColumn     �������Ƥ�����
 *    @param  Object    $objDB                   DB���֥�������
 *    @param    String    $strOrderCode            ȯ������    ��������:������̽���    ȯ�����ɻ����:�����ѡ�Ʊ��ȯ�����ɤΰ�������
 *    @param    Integer    $lngOrderNo                ȯ��Σ�    0:������̽���    ȯ��Σ�����:�����ѡ�Ʊ��ȯ�����ɤȤ�������оݳ�ȯ��NO
 *    @param    Boolean    $bytAdminMode            ͭ���ʺ���ǡ����μ����ѥե饰    FALSE:������̽���    TRUE:�����ѡ�����ǡ�������
 *    @return Array     $strSQL ������SQLʸ OR Boolean FALSE
 *    @access public
 */
function fncGetSearchPurcheseOrderSQL($aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strOrderCode, $lngOrderNo, $bytAdminMode)
{
    // ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
    for ($i = 0; $i < count($aryViewColumn); $i++) {
        $strViewColumnName = $aryViewColumn[$i];

        // ��Ͽ��
        if ($strViewColumnName == "dtmInsertDate") {
            $arySelectQuery[] = "  ,to_char(mp.dtminsertdate, 'YYYY/MM/DD') as dtmInsertDate";
        }

        // ���ϼ�
        if ($strViewColumnName == "lngInputUserCode") {
            $arySelectQuery[] = "  ,input_user.struserdisplaycode AS strinputuserdisplaycode";
            $arySelectQuery[] = "  ,mp.strinsertusername AS strinputuserdisplayname";
        }

        // ȯ��ͭ��������
        if ($strViewColumnName == "dtmExpirationDate") {
            $arySelectQuery[] = "  ,to_char(mp.dtmexpirationdate, 'YYYY/MM/DD') as dtmExpirationDate";
        }

        // ȯ��NO.
        if ($strViewColumnName == "strOrderCode") {
            $arySelectQuery[] = "  ,mp.strordercode as strOrderCode";
        }

        // ����
        if ($strViewColumnName == "strProductCode") {
            $arySelectQuery[] = "  ,mp.strproductcode as strProductCode";
            $arySelectQuery[] = "  ,mp.strproductname as strProductName";
            $arySelectQuery[] = "  ,mp.strproductenglishname as strProductEnglishName";
        }

        // �Ķ�����
        if ($strViewColumnName == "lngInChargeGroupCode") {
            $arySelectQuery[] = "  ,mg.strgroupdisplaycode AS strgroupdisplaycode";
            $arySelectQuery[] = "  ,mp.strgroupname as strgroupdisplayname";
        }

        // ��ȯô����
        if ($strViewColumnName == "lngInChargeUserCode") {
            $arySelectQuery[] = "  ,mu.struserdisplaycode as struserdisplaycode";
            $arySelectQuery[] = "  ,mp.strusername as struserdisplayname";
        }

        // ������
        if ($strViewColumnName == "lngCustomerCode") {
            $arySelectQuery[] = "  ,mc_stock.strcompanydisplaycode as strcustomerdisplaycode";
            $arySelectQuery[] = "  ,mp.strcustomername as strcustomerdisplayname";
        }

        // Ǽ�ʾ��
        if ($strViewColumnName == "lngDeliveryPlaceCode") {
            $arySelectQuery[] = "  ,mp.lngdeliveryplacecode as strdeliveryplacecode";
            $arySelectQuery[] = "  ,mp.strdeliveryplacename as strDeliveryPlaceName";
        }

        // �̲�
        if ($strViewColumnName == "lngMonetaryunitCode" or $strViewColumnName == "curTotalPrice") {
            $arySelectQuery[] = "  ,mp.lngmonetaryunitcode as lngMonetaryUnitCode";
            $arySelectQuery[] = "  ,mp.strmonetaryunitname as strmonetaryunitname";
            $arySelectQuery[] = "  ,mp.strmonetaryunitsign as strMonetaryUnitSign";
        }

        // �̲ߥ졼��
        if ($strViewColumnName == "lngMonetaryRateCode") {
            $arySelectQuery[] = "  ,mp.lngmonetaryratecode as lngMonetaryRateCode";
            $arySelectQuery[] = "  ,mp.strmonetaryratename as strMonetaryRateName";
        }

        // ��ʧ���
        if ($strViewColumnName == "lngPayConditionCode") {
            $arySelectQuery[] = "  ,mp.lngpayconditioncode as lngPayConditionCode";
            $arySelectQuery[] = "  ,mp.strpayconditionname as strPayConditionName";
        }

        // ��׶��
        if ($strViewColumnName == "curTotalPrice") {
            $arySelectQuery[] = "  ,To_char(mp.curtotalprice, '9,999,999,990.99') as curtotalprice";
        }

        // ����
        if ($strViewColumnName == "strNote") {
            $arySelectQuery[] = "  ,mp.strnote as strNote";
        }

        // �������
        if ($strViewColumnName == "lngPrintCount") {
            $arySelectQuery[] = "  ,mp.lngprintcount as lngPrintCount";
        }
        // ���
        if ($strViewColumnName == "btnDelete") {
            $arySelectQuery[] = "  ,1 as btnDelete";
        }
    }

    $aryQuery[] = "WHERE mp.lngpurchaseorderno >= 0";
    if(!$bytAdminMode)
    {
        //�����ԥ⡼�ɰʳ��ξ��Ϻ�����줿�ǡ������оݤȤ��ʤ�
        $aryQuery[] = " AND mp.lngpurchaseorderno NOT IN (SELECT lngpurchaseorderno from m_purchaseorder where lngrevisionno < 0)";
    }
    // �����ѥ��������ꤵ��Ƥ������Ƥ򸡺�����ʸ��������
    for ($i = 0; $i < count($arySearchColumn); $i++) {
        $strSearchColumnName = $arySearchColumn[$i];

        // ȯ���ޥ����θ������
        // ȯ����
        if ($strSearchColumnName == "dtmInsertDate") {
            if ($arySearchDataColumn["dtmInsertDateFrom"]) {
                $dtmSearchDate = $arySearchDataColumn["dtmInsertDateFrom"] . " 00:00:00";
                $aryQuery[] = "AND   mp.dtminsertdate >= '" . $dtmSearchDate . "'";
            }
            if ($arySearchDataColumn["dtmInsertDateTo"]) {
                $dtmSearchDate = $arySearchDataColumn["dtmInsertDateTo"] . " 23:59:59.99999";
                $aryQuery[] = "AND   mp.dtminsertdate <= '" . $dtmSearchDate . "'";
            }
        }

        // ���ϼ�
        if ($strSearchColumnName == "lngInputUserCode") {
            if ($arySearchDataColumn["lngInputUserCode"]) {
//                $aryQuery[] = "AND   mp.lnginsertusercode ~* '" . $arySearchDataColumn["lngInputUserCode"] . "'";
                $aryQuery[] = "AND   input_user.struserdisplaycode = '" . $arySearchDataColumn["lngInputUserCode"] . "'";
            }
//            if($arySearchDataColumn["strInputUserName"]){
            //                $aryQuery[] = "AND   UPPER(mp.strinsertusername) LIKE UPPER('%" . $arySearchDataColumn["strInputUserName"] . "%')";
            //            }
        }

        // ȯ��ͭ������
        if ($strSearchColumnName == "dtmExpirationDate") {
            if ($arySearchDataColumn["dtmExpirationDateFrom"]) {
                $dtmSearchDate = $arySearchDataColumn["dtmExpirationDateFrom"] . " 00:00:00";
                $aryQuery[] = "AND   mp.dtmexpirationdate >= '" . $dtmSearchDate . "'";
            }
            if ($arySearchDataColumn["dtmExpirationDateTo"]) {
                $dtmSearchDate = $arySearchDataColumn["dtmExpirationDateTo"] . " 23:59:59.99999";
                $aryQuery[] = "AND   mp.dtmexpirationdate <= '" . $dtmSearchDate . "'";
            }
        }

        // ȯ��NO.
        if ($strSearchColumnName == "strOrderCode") {
            $strOrderCodeArray = explode(",", $arySearchDataColumn["strOrderCode"]);
            $aryQuery[] = " AND (";
            $count = 0;
            foreach ($strOrderCodeArray as $strOrderCode) {
                $count += 1;
                if ($count != 1) {
                    $aryQuery[] = " OR ";
                }
                if (strpos($strOrderCode, '-') !== false) {
                    $aryQuery[] = "(mp.strordercode" .
                    " between '" . explode("-", $strOrderCode)[0] . "'" .
                    " AND " . "'" . explode("-", $strOrderCode)[1] . "')";
                } else {
                    if (strpos($strOrderCode, '_') !== false) {
                        $aryQuery[] = "mp.strordercode = '" . explode("_", $strOrderCode)[0] . "'";
                        if (explode("_", $strOrderCode)[1] == '00') {
                            $aryQuery[] = " AND mp.lngrevisionno = 0 ";
                        } else {
                            $aryQuery[] = " AND mp.lngrevisionno = " . ltrim(explode("_", $strOrderCode)[1], '0') . "";
                        }
                    } else {
                        $aryQuery[] = "mp.strordercode = '" . $strOrderCode . "'";
                    }
                }
            }
            $aryQuery[] = ")";
        }
        // ����
        if ($strSearchColumnName == "strProductCode") {            
            $strProductCodeArray = explode(",", $arySearchDataColumn["strProductCode"]);
            $aryQuery[] = " AND (";
            $count = 0;
            foreach ($strProductCodeArray as $strProductCode) {
                $count += 1;
                if ($count != 1) {
                    $aryQuery[] = " OR ";
                }
                if (strpos($strProductCode, '-') !== false) {
                    $aryQuery[] = "(mp.strProductCode" .
                    " between '" . explode("-", $strProductCode)[0] . "'" .
                    " AND " . "'" . explode("-", $strProductCode)[1] . "')";
                } else {
                    if (strpos($strProductCode, '_') !== false) {
                        $aryQuery[] = "mp.strProductCode = '" . explode("_", $strProductCode)[0] . "'";
                        $aryQuery[] = " AND mp.strrevisecode = '" . explode("_", $strProductCode)[1] . "'";
                    } else {
                        $aryQuery[] = "mp.strProductCode = '" . $strProductCode . "'";
                    }
                }
            }
            $aryQuery[] = ")";
            // if ($arySearchDataColumn["strProductCode"]) {
            //     $aryQuery[] = "AND   mp.strProductCode = '" . $arySearchDataColumn["strProductCode"] . "'";
            // }
//            if($arySearchDataColumn["strProductName"]){
            //                $aryQuery[] = "AND   UPPER(mp.strproductname) LIKE UPPER('%" . $arySearchDataColumn["strProductName"] . "%')";
            //            }
        }

        // �Ķ�����
        if ($strSearchColumnName == "lngInChargeGroupCode") {
            if ($arySearchDataColumn["lngInChargeGroupCode"]) {
//                $aryQuery[] = "AND   mp.lnggroupcode = '" . $arySearchDataColumn["lngInChargeGroupCode"] . "'";
                $aryQuery[] = "AND   mg.strgroupdisplaycode = '" . $arySearchDataColumn["lngInChargeGroupCode"] . "'";
            }
//            if($arySearchDataColumn["strInChargeGroupName"]){
            //                $aryQuery[] = "AND   UPPER(mp.strgroupname) LIKE UPPER('%" . $arySearchDataColumn["strInChargeGroupName"] . "%')";
            //            }
        }

        // ��ȯô����
        if ($strSearchColumnName == "lngInChargeUserCode") {
            if ($arySearchDataColumn["lngInChargeUserCode"]) {
//                $aryQuery[] = "AND   mp.lngusercode = '" . $arySearchDataColumn["lngInChargeUserCode"] . "'";
                $aryQuery[] = "AND   mu.struserdisplaycode = '" . $arySearchDataColumn["lngInChargeUserCode"] . "'";
            }
//            if($arySearchDataColumn["strInChargeUserName"]){
            //                $aryQuery[] = "AND   UPPER(mp.strusername) LIKE UPPER('%" . $arySearchDataColumn["strInChargeUserName"] . "%')";
            //            }
        }

        // ������
        if ($strSearchColumnName == "lngCustomerCode") {
            if ($arySearchDataColumn["lngCustomerCode"]) {
//                $aryQuery[] = "AND   mp.lngcustomercode = '" . $arySearchDataColumn["lngCustomerCode"] . "'";
                $aryQuery[] = "AND   mc_stock.strcompanydisplaycode = '" . $arySearchDataColumn["lngCustomerCode"] . "'";
            }
//            if($arySearchDataColumn["strCustomerName"]){
            //                $aryQuery[] = "AND   UPPER(mp.strcustomername) LIKE UPPER('%" . $arySearchDataColumn["strCustomerName"] . "%')";
            //            }
        }

        // Ǽ�ʾ��
        if ($strSearchColumnName == "lngDeliveryPlaceCode") {
            if ($arySearchDataColumn["lngDeliveryPlaceCode"]) {
//                $aryQuery[] = "AND   mp.lngdeliveryplacecode = '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'";
                $aryQuery[] = "AND   mc_delivary.strcompanydisplaycode = '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'";
            }
//            if($arySearchDataColumn["strDeliveryPlaceName"]){
            //                $aryQuery[] = "AND   UPPER(mp.strdeliveryplacename) LIKE UPPER('%" . $arySearchDataColumn["strDeliveryPlaceName"] . "%')";
            //            }
        }

        // �̲�
        if ($strSearchColumnName == "lngMonetaryunitCode") {
            $aryQuery[] = "AND   mp.lngmonetaryunitcode = " . $arySearchDataColumn["lngMonetaryunitCode"];
        }

        // �̲ߥ졼��
        if ($strSearchColumnName == "lngMonetaryRateCode") {
            $aryQuery[] = "AND   mp.lngmonetaryratecode = " . $arySearchDataColumn["lngMonetaryRateCode"];
        }

        // ��ʧ���
        if ($strSearchColumnName == "lngPayConditionCode") {
            $aryQuery[] = "AND   mp.lngpayconditioncode = " . $arySearchDataColumn["lngPayConditionCode"];
        }
    }

    // SQL����
    $aryOutQuery[] = "SELECT";
    $aryOutQuery[] = "   mp.lngpurchaseorderno as lngPurchaseOrderNo";
    $aryOutQuery[] = "  ,mp.lngpurchaseorderno as lngpkno";
    $aryOutQuery[] = "  ,mp.lngrevisionno as lngRevisionNo";
    $aryOutQuery[] = "  ,mp.strrevisecode as strReviseCode";
    $aryOutQuery[] = "  ,max_status.lngorderstatuscode as lngorderstatuscode";
    $aryOutQuery[] = implode("\n", $arySelectQuery);
    $aryOutQuery[] = "FROM m_purchaseorder mp";
    $aryOutQuery[] = "INNER JOIN (";
    $aryOutQuery[] = "    SELECT lngpurchaseorderno, MAX(lngrevisionno) as maxRevisionNo FROM m_purchaseorder GROUP BY lngpurchaseorderno";
    $aryOutQuery[] = ") max_rev ON max_rev.lngpurchaseorderno = mp.lngpurchaseorderno and max_rev.maxRevisionNo = mp.lngrevisionno";
    $aryOutQuery[] = "INNER JOIN (";
    $aryOutQuery[] = "    select ";
    $aryOutQuery[] = "        tpod.lngpurchaseorderno";
    $aryOutQuery[] = "       ,tpod.lngrevisionno";
    $aryOutQuery[] = "       ,max(mo.lngorderstatuscode) as lngorderstatuscode";
    $aryOutQuery[] = "    from t_purchaseorderdetail tpod";
    $aryOutQuery[] = "    inner join m_order mo";
    $aryOutQuery[] = "        on mo.lngorderno = tpod.lngorderno";
    $aryOutQuery[] = "        and mo.lngrevisionno = tpod.lngorderrevisionno";
    $aryOutQuery[] = "    group by";
    $aryOutQuery[] = "        tpod.lngpurchaseorderno";
    $aryOutQuery[] = "       ,tpod.lngrevisionno";
    $aryOutQuery[] = ") max_status ON max_status.lngpurchaseorderno = mp.lngpurchaseorderno and max_status.lngrevisionno = mp.lngrevisionno";
    $aryOutQuery[] = "left join m_user input_user on input_user.lngusercode = mp.lnginsertusercode";
    $aryOutQuery[] = "left join m_group mg on mg.lnggroupcode = mp.lnggroupcode";
    $aryOutQuery[] = "left join m_user mu on mu.lngusercode = mp.lngusercode";
    $aryOutQuery[] = "left join m_company mc_stock on mc_stock.lngcompanycode = mp.lngcustomercode";
    $aryOutQuery[] = "left join m_company mc_delivary on mc_delivary.lngcompanycode = mp.lngdeliveryplacecode";
    $aryOutQuery[] = implode("\n", $aryQuery);
    $aryOutQuery[] = "ORDER BY";
    $aryOutQuery[] = "   mp.lngpurchaseorderno";
    $aryOutQuery[] = "  ,mp.lngrevisionno DESC";
    $aryOutQuery[] = "";

    switch ($arySearchDataColumn["strSort"]) {

    }

    return implode("\n", $aryOutQuery);
}

/**
 * �б�����ȯ��NO�Υǡ������Ф������ٹԤ��������SQLʸ�κ����ؿ�
 *
 *    ȯ��NO�������٤�������� SQLʸ���������
 *
 *    @param  Array     $aryDetailViewColumn     ɽ���о����٥����̾������
 *    @param  String     $lngOrderNo             �о�ȯ��NO
 *    @param  Array     $aryData                 POST�ǡ���������
 *    @param  Object    $objDB                   DB���֥�������
 *    @return Array     $strSQL ������SQLʸ OR Boolean FALSE
 *    @access public
 */
function fncGetOrderToProductSQL($aryDetailViewColumn, $lngOrderNo, $lngRevisionNo, $aryData, $objDB)
{
    reset($aryDetailViewColumn);

    // ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
    for ($i = 0; $i < count($aryDetailViewColumn); $i++) {
        $strViewColumnName = $aryDetailViewColumn[$i];

        // ɽ������
        // ���ʥ�����
        if ($strViewColumnName == "strProductCode") {
            $arySelectQuery[] = ", od.strProductCode || '_' || od.strReviseCode  as strProductCode";
        }

        // ����
        if ($strViewColumnName == "lngInChargeGroupCode") {
            $arySelectQuery[] = ", '['||mg.strgroupdisplaycode||'] '|| mg.strgroupdisplayname as lngInChargeGroupCode";
        }

        // ô����
        if ($strViewColumnName == "lngInChargeUserCode") {
            $arySelectQuery[] = ", '['||mu.struserdisplaycode ||'] '|| mu.struserdisplayname  as lngInChargeUserCode";
        }

        // ����̾�Ρ����ܸ��
        if ($strViewColumnName == "strProductName") {
            $arySelectQuery[] = ", p.strProductName as strProductName";
            $flgProductCode = true;
        }

        // ����̾�ΡʱѸ��
        if ($strViewColumnName == "strProductEnglishName") {
            $arySelectQuery[] = ", p.strProductEnglishName as strProductEnglishName";
            $flgProductCode = true;
        }

        // ��������
        if ($strViewColumnName == "lngStockSubjectCode") {
            $arySelectQuery[] = ", od.lngStockSubjectCode as lngStockSubjectCode";
            $arySelectQuery[] = ", ss.strStockSubjectName as strStockSubjectName";
            $flgStockSubject = true;
        }

        // ��������
        if ($strViewColumnName == "lngStockItemCode") {
            $arySelectQuery[] = ", od.lngStockItemCode as lngStockItemCode";
            $flgStockItem = true;
        }

        // �ⷿ�ֹ�
        if ($strViewColumnName == "strMoldNo") {
            $arySelectQuery[] = ", od.strMoldNo as strMoldNo";
        }

        // �ܵ�����
        if ($strViewColumnName == "strGoodsCode") {
            $arySelectQuery[] = ", p.strGoodsCode as strGoodsCode";
            $flgProductCode = true;
        }

        // ������ˡ
        if ($strViewColumnName == "lngDeliveryMethodCode") {
            $arySelectQuery[] = ", od.lngDeliveryMethodCode as lngDeliveryMethodCode";
            $arySelectQuery[] = ", dm.strDeliveryMethodName as strDeliveryMethodName";
            $flgDeliveryMethod = true;
        }

        // Ǽ��
        if ($strViewColumnName == "dtmDeliveryDate") {
            $arySelectQuery[] = ", to_char( od.dtmDeliveryDate, 'YYYY/MM/DD' ) as dtmDeliveryDate";
        }

        // ñ��
        if ($strViewColumnName == "curProductPrice") {
            $arySelectQuery[] = ", To_char( od.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
        }

        // ñ��
        if ($strViewColumnName == "lngProductUnitCode") {
            $arySelectQuery[] = ", od.lngProductUnitCode as lngProductUnitCode";
            $arySelectQuery[] = ", pu.strProductUnitName as strProductUnitName";
            $flgProductUnit = true;
        }

        // ����
        if ($strViewColumnName == "lngProductQuantity") {
            $arySelectQuery[] = ", To_char( od.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
        }

        // ��ȴ���
        if ($strViewColumnName == "curSubTotalPrice") {
            $arySelectQuery[] = ", To_char( od.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
        }
        // ��������
        if ($strViewColumnName == "strDetailNote") {
            $arySelectQuery[] = ", od.strNote as strDetailNote";
        }
    }

    // �������ʤΤ�ɽ���оݤ��ä����ϻ������ܤˤĤ��Ƥ�ǡ������������
    if ($flgStockItem == true and $flgStockSubject == false) {
        $arySelectQuery[] = ", od.lngStockSubjectCode as lngStockSubjectCode";
        $arySelectQuery[] = ", ss.strStockSubjectName as strStockSubjectName";
        $flgStockSubject = true;
    }

    // ���о�� �о�ȯ��NO�λ���
    $aryQuery[] = " WHERE od.lngOrderNo = " . $lngOrderNo . " AND od.lngrevisionno = " . $lngRevisionNo;

    // �����ɲ�

    // ////ȯ��ޥ�����θ������////
    // SQLʸ�κ���
    $aryOutQuery = array();
    $aryOutQuery[] = "SELECT od.lngSortKey as lngRecordNo";
    $aryOutQuery[] = "	,od.lngOrderNo as lngOrderNo";
    $aryOutQuery[] = "	,od.lngRevisionNo as lngRevisionNo";

    // select�� �����꡼Ϣ��
    if (!empty($arySelectQuery)) {
        $aryOutQuery[] = implode("\n", $arySelectQuery);
    }

    // From�� ������
    $aryFromQuery = array();
    $aryFromQuery[] = " FROM t_OrderDetail od";

    // �ɲ�ɽ���Ѥλ��ȥޥ����б�
    $aryFromQuery[] = "   LEFT JOIN m_Product p on p.strProductCode = od.strProductCode and p.strReviseCode = od.strReviseCode and p.lngrevisionno = od.lngrevisionno";
    $aryFromQuery[] = " left join m_group mg on mg.lnggroupcode = p.lnginchargegroupcode";
    $aryFromQuery[] = " left join m_user  mu on mu.lngusercode = p.lnginchargeusercode";

    if ($flgStockSubject) {
        $aryFromQuery[] = " LEFT JOIN m_StockSubject ss USING (lngStockSubjectCode)";
    }
    if ($flgStockItem) {
        //        $aryOutQuery[] = " LEFT JOIN m_StockItem si USING (lngStockItemCode)\n";
    }
    if ($flgDeliveryMethod) {
        $aryFromQuery[] = " LEFT JOIN m_DeliveryMethod dm USING (lngDeliveryMethodCode)";
    }
    if ($flgProductUnit) {
        $aryFromQuery[] = " LEFT JOIN m_ProductUnit pu ON od.lngProductUnitCode = pu.lngProductUnitCode";
    }

    // From�� �����꡼Ϣ��
    $aryOutQuery[] = implode("\n", $aryFromQuery);
    // Where�� �����꡼Ϣ��
    $aryOutQuery[] = implode("\n", $aryQuery);

    // �����Ⱦ�����
    if ($aryData["strSortOrder"] == "ASC") {
        $strAsDs = " DESC"; // �إå����ܤȤϵս�ˤ���
    } else {
        $strAsDs = " ASC"; //�߽�
    }

    switch ($aryData["strSort"]) {
        case "strDetailNote":
            $aryOutQuery[] = " ORDER BY od.strNote" . $strAsDs . ", od.lngSortKey ASC";
            break;
        case "lngOrderDetailNo":
            $aryOutQuery[] = " ORDER BY od.lngSortKey" . $strAsDs;
            break;
        case "strProductName":
        case "strProductEnglishName":
        case "strGoodsCode":
            $aryOutQuery[] = " ORDER BY " . $aryData["strSort"] . " " . $strAsDs . ", od.lngSortKey ASC";
            break;
        default:
            $aryOutQuery[] = " ORDER BY od.lngSortKey ASC";
    }

    return implode("\n", $aryOutQuery);
}

// /**
//  * �������ɽ���ؿ��ʥإå��ѡ�
//  *
//  *    ������̤���ơ��֥빽���Ƿ�̤���Ϥ���ؿ�
//  *    �إå��Ԥ�ɽ������
//  *
//  *    @param  Integer $lngColumnCount         �Կ�
//  *    @param  Array     $aryHeadResult             �إå��Ԥθ�����̤���Ǽ���줿����
//  *    @param  Array     $aryDetailResult         ���ٹԤθ�����̤���Ǽ���줿����
//  *    @param  Array     $aryDetailViewColumn     ����ɽ���оݥ����̾������
//  *    @param  Array     $aryHeadViewColumn         �إå�ɽ���оݥ����̾������
//  *    @param  Array     $aryData                 �Уϣӣԥǡ�����
//  *    @param    Array    $aryUserAuthority        �桼�����������Ф��븢�¤����ä�����
//  *    @param  Object     $objDB                     DB���֥�������
//  *    @param  Object     $objCache                 ����å��奪�֥�������
//  *    @param    Integer    $lngReviseTotalCount    ɽ���оݤ�ȯ��β���Х����ι�׿�
//  *    @param    Integer    $lngReviseCount            ɽ���оݤ�ȯ���ɽ����ʺǿ�ȯ��ʤ飰��
//  *    @param    Array    $aryNewResult            ɽ���оݤ�ȯ��κǿ���ȯ��ǡ���
//  *    @access public
//  */
// function fncSetPurchaseHeadTable($lngColumnCount, $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn,
//     $aryData, $aryUserAuthority, $objDB, $objCache, $lngReviseTotalCount, $lngReviseCount, $aryNewResult) {
//     include_once 'conf.inc';
//     require_once LIB_DEBUGFILE;
//     for ($i = 0; $i < count($aryDetailResult); $i++) {
//         $aryHtml[] = "<tr>";
//         $aryHtml[] = "\t<td class=\"rownum\">" . ($lngColumnCount + $i) . "</td>";
//         // ɽ���оݥ������������̤ν���
//         for ($j = 0; $j < count($aryHeadViewColumn); $j++) {
//             $strColumnName = $aryHeadViewColumn[$j];
//             $TdData = "";

//             // ɽ���оݤ��ܥ���ξ��
//             if ($strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" or $strColumnName == "btnInvalid" or $strColumnName == "Record" or $strColumnName == "btnAdmin") {
//                 // �ܥ����ˤ���ѹ�

//                 // �ܺ�ɽ��
//                 if ($strColumnName == "btnDetail" and $aryUserAuthority["Detail"]) {
//                     // ȯ��ǡ���������оݤξ�硢�ܺ�ɽ���ܥ���������Բ�
//                     if ($aryHeadResult["lngrevisionno"] >= 0) {
//                         $aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" lngrevisionno=\"" . $aryDetailResult[$i]["lngrevisionno"] . "\" class=\"detail button\"></td>\n";
//                     } else {
//                         $aryHtml[] = "\t<td></td>\n";
//                     }
//                 }

//                 // ����
//                 if ($strColumnName == "btnFix" and $aryUserAuthority["Fix"]) {
//                     // ȯ��ǡ����ξ��֤ˤ��ʬ��  //// ���֤��ֲ�ȯ��פξ�����ܥ���������Բ�
//                     if ($aryHeadResult["lngorderstatuscode"] == 1) {
//                         $aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" class=\"fix button\"></td>\n";
//                     } else {
//                         $aryHtml[] = "\t<td></td>\n";
//                     }
//                 }

//                 // ����
//                 if ($strColumnName == "Record") {
//                     if ($aryHeadResult["lngrevisionno"] > 0) {
//                         $aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" strordercode=\"" . $aryHeadResult["strordercode"] . "\" class=\"record button\"></td>\n";
//                     } else {
//                         $aryHtml[] = "\t<td></td>\n";
//                     }
//                 }

//                 // ������
//                 if ($strColumnName == "btnDelete" and $aryUserAuthority["Delete"]) {
//                     // ȯ��ǡ����ξ��֤ˤ��ʬ��  //// ���֤��ֿ�����ס�Ǽ����ס�Ǽ�ʺѡס�����ѡפξ�����ܥ���������Բ�
//                     // �ǿ�ȯ������ǡ����ξ��������Բ�
//                     if ($aryHeadResult["lngorderstatuscode"] != DEF_ORDER_APPLICATE and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_CLOSED) {
//                         $aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" class=\"remove button\"></td>\n";
//                     } else {
//                         $aryHtml[] = "\t<td></td>\n";
//                     }
// /*
// //��Х�����¸�ߤ��ʤ����
// if ( $lngReviseTotalCount == 1 )
// {
// // ȯ��ǡ����ξ��֤ˤ��ʬ��  //// ���֤��ֲ�ȯ��ס�����ѡפξ�����ܥ���������Բ�
// // �ǿ�ȯ������ǡ����ξ��������Բ�
// if ( $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_APPLICATE and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_CLOSED)
// {
// $aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" class=\"remove button\"></td>\n";
// }
// else
// {
// $aryHtml[] = "\t<td></td>\n";
// }
// }
// //ʣ����Х�����¸�ߤ�����
// else
// {
// // �ǿ�ȯ��ξ��
// if ( $lngReviseCount == 0 )
// {
// // ȯ��ǡ����ξ��֤ˤ��ʬ��  //// ���֤��ֿ�����ס�Ǽ����ס�Ǽ�ʺѡס�����ѡפξ�����ܥ���������Բ�
// // �ǿ�ȯ������ǡ����ξ��������Բ�
// if ( $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_APPLICATE and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_CLOSED)
// {
// $aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" class=\"remove button\"></td>\n";
// }
// else
// {
// $aryHtml[] = "\t<td></td>\n";
// }
// }
// else
// {
// $aryHtml[] = "\t<td></td>\n";
// }
// }
//  */
//                 }

//                 // �����
//                 if ($strColumnName == "btnAdmin" and $aryUserAuthority["Admin"]) {
//                     if ($aryHeadResult["lngRevisionno"] == -1) {
//                         $aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" class=\"admin button\"></td>\n";
//                     } else {
//                         $aryHtml[] = "\t<td></td>\n";
//                     }
//                 }

//             } else if ($strColumnName != "") {
//                 // $TdData = "\t<td>";
//                 $TdData = "";
//                 $TdDataUse = true;
//                 $strText = "";
//                 // ��Ͽ��
//                 if ($strColumnName == "dtmInsertDate") {
//                     $TdData .= "\t<td class=\"td-dtminsertdate\">";
//                     $TdData .= str_replace("-", "/", substr($aryHeadResult["dtminsertdate"], 0, 19));
//                 }
//                 // �׾���
//                 else if ($strColumnName == "dtmOrderAppDate") {
//                     $TdData .= "\t<td class=\"td-dtmorderappdate\">";
//                     $TdData .= str_replace("-", "/", $aryHeadResult["dtmorderappdate"]);
//                 }
//                 // ȯ��NO
//                 else if ($strColumnName == "strOrderCode") {
//                     $baseOrderCode = explode("_", $aryHeadResult["strordercode"])[0];
//                     $TdData .= "\t<td class=\"td-strordercode\" baseordercode=\"" . $baseOrderCode . "\">";
//                     $TdData .= $aryHeadResult["strordercode"];
//                     // // �����⡼�ɤξ�硡��ӥ�����ֹ��ɽ������
//                     // if ( $aryData["Admin"] )
//                     // {
//                     //     $TdData .= "</td>\n\t<td>" . $aryHeadResult["lngrevisionno"];
//                     // }
//                 }
//                 // ���ϼ�
//                 else if ($strColumnName == "lngInputUserCode") {
//                     if ($aryHeadResult["strinputuserdisplaycode"]) {
//                         $strText .= "[" . $aryHeadResult["strinputuserdisplaycode"] . "]";
//                     } else {
//                         $strText .= "     ";
//                     }
//                     $strText .= " " . $aryHeadResult["strinputuserdisplayname"];
//                     $TdData .= "\t<td class=\"td-strinputuserdisplaycode\">";
//                     $TdData .= $strText;
//                 }
//                 // ������
//                 else if ($strColumnName == "lngCustomerCode") {
//                     if ($aryHeadResult["strcustomerdisplaycode"]) {
//                         $strText .= "[" . $aryHeadResult["strcustomerdisplaycode"] . "]";
//                     } else {
//                         $strText .= "      ";
//                     }
//                     $strText .= " " . $aryHeadResult["strcustomerdisplayname"];
//                     $TdData .= "\t<td class=\"td-strcustomerdisplaycode\">";
//                     $TdData .= $strText;
//                 }
//                 // ��׶��
//                 else if ($strColumnName == "curTotalPrice") {
//                     $strText .= $aryHeadResult["strmonetaryunitsign"] . " ";
//                     if (!$aryHeadResult["curtotalprice"]) {
//                         $strText .= "0.00";
//                     } else {
//                         $strText .= $aryHeadResult["curtotalprice"];
//                     }
//                     $TdData .= "\t<td class=\"td-curtotalprice\">";
//                     $TdData .= $strText;
//                 }
//                 // ����
//                 else if ($strColumnName == "lngOrderStatusCode") {
//                     $TdData .= "\t<td class=\"td-strorderstatusname\">";
//                     $TdData .= $aryHeadResult["strorderstatusname"];
//                 }
//                 // ��ʧ���
//                 else if ($strColumnName == "lngPayConditionCode") {
//                     $TdData .= "\t<td class=\"td-strpayconditionname\">";
//                     $TdData .= $aryHeadResult["strpayconditionname"];
//                 }
//                 // ȯ��ͭ��������
//                 else if ($strColumnName == "dtmExpirationDate") {
//                     $TdData .= "\t<td class=\"td-dtmexpirationdate\">";
//                     $TdData .= str_replace("-", "/", $aryHeadResult["dtmexpirationdate"]);
//                 }
//                 // ���ٹ��ֹ�
//                 else if ($strColumnName == "lngRecordNo") {
//                     $TdData .= "\t<td class=\"td-lngrecordno\">";
//                     $TdData .= $aryDetailResult[$i]["lngrecordno"];
//                 }
//                 // 2004.03.31 suzukaze update start
//                 // ���ʥ�����
//                 else if ($strColumnName == "strProductCode") {
//                     if ($aryDetailResult[$i]["strproductcode"]) {
//                         $strText .= "[" . $aryDetailResult[$i]["strproductcode"] . "]";
//                     } else {
//                         $strText .= "      ";
//                     }
//                     $TdData .= "\t<td class=\"td-strproductcode\">";
//                     $TdData .= $strText;
//                 }
//                 // 2004.03.31 suzukaze update start
//                 // ��������
//                 else if ($strColumnName == "lngStockSubjectCode") {
//                     if ($aryDetailResult[$i]["lngstocksubjectcode"]) {
//                         $strText .= "[" . $aryDetailResult[$i]["lngstocksubjectcode"] . "]";
//                     } else {
//                         $strText .= "      ";
//                     }
//                     $strText .= " " . $aryDetailResult[$i]["strstocksubjectname"];
// //fncDebug("kids2.log", $strText , __FILE__, __LINE__, "a" );
//                     $TdData .= "\t<td class=\"td-lngstocksubjectcode\">";
//                     $TdData .= $strText;
//                 }
//                 // ��������
//                 else if ($strColumnName == "lngStockItemCode") {
//                     if ($aryDetailResult[$i]["lngstockitemcode"]) {
//                         $strText .= "[" . $aryDetailResult[$i]["lngstockitemcode"] . "]";
//                         // �������ܥ����ɤ�¸�ߤ���ʤ��
//                         if ($aryDetailResult[$i]["lngstocksubjectcode"]) {
//                             $strSubjectItem = $aryDetailResult[$i]["lngstocksubjectcode"] . ":" . $aryDetailResult[$i]["lngstockitemcode"];
//                             $aryStockItem = $objCache->GetValue("lngstocksubjectcode:lngstockitemcode", $strSubjectItem);
//                             if (!is_array($aryStockItem)) {
//                                 // ����̾�Τμ���
//                                 $strStockItemName = fncGetMasterValue("m_stockitem", "lngstockitemcode", "strstockitemname",
//                                     $aryDetailResult[$i]["lngstockitemcode"], "lngstocksubjectcode = " . $aryDetailResult[$i]["lngstocksubjectcode"], $objDB);
//                                 // ����̾�Τ�����
//                                 $aryStockItem = $strStockItemName;
//                                 $objCache->SetValue("lngstocksubjectcode:lngstockitemcode", $strSubjectItem, $aryStockItem);
//                             }
//                             $strText .= " " . $aryStockItem;
//                         }
//                     } else {
//                         $strText .= "      ";
//                         $strText .= " " . $aryDetailResult[$i]["strstockitemname"];
//                     }
//                     $TdData .= "\t<td class=\"td-lngstockitemcode\">";
//                     $TdData .= $strText;
//                 }
//                 // ������ˡ
//                 else if ($strColumnName == "lngDeliveryMethodCode") {
//                     if ($aryDetailResult[$i]["strdeliverymethodname"] == "") {
//                         $aryDetailResult[$i]["strdeliverymethodname"] = "̤��";
//                     }
//                     $strText .= $aryDetailResult[$i]["strdeliverymethodname"];
//                     $TdData .= "\t<td class=\"td-strdeliverymethodname\">";
//                     $TdData .= $strText;
//                 }
//                 // 2004.04.21 suzukaze update start
//                 // Ǽ��
//                 else if ($strColumnName == "dtmDeliveryDate") {
//                     $TdData .= "\t<td class=\"td-dtmdeliverydate\">";
//                     $TdData .= str_replace("-", "/", $aryDetailResult[$i]["dtmdeliverydate"]);
//                 }
//                 // 2004.04.21 suzukaze update end
//                 // ñ��
//                 else if ($strColumnName == "curProductPrice") {
//                     $TdDataUse = false;
//                     $strText = "\t<td align=\"right\">";
//                     $strText .= $aryHeadResult["strmonetaryunitsign"] . " ";
//                     if (!$aryDetailResult[$i]["curproductprice"]) {
//                         $strText .= "0.00";
//                     } else {
//                         $strText .= $aryDetailResult[$i]["curproductprice"];
//                     }
//                     $aryHtml[] = $strText . "</td>\n";
//                 }
//                 // ñ��
//                 else if ($strColumnName == "lngProductUnitCode") {
//                     $TdData .= "\t<td class=\"td-strproductunitname\">";
//                     $TdData .= $aryDetailResult[$i]["strproductunitname"];
//                 }
//                 // ����
//                 else if ($strColumnName == "lngProductQuantity") {
//                     $TdDataUse = false;
//                     $aryHtml[] = "\t<td align=\"right\">" . $aryDetailResult[$i]["lngproductquantity"] . "</td>\n";
//                 }
//                 // ��ȴ���
//                 else if ($strColumnName == "curSubTotalPrice") {
//                     $TdDataUse = false;
//                     $strText = "\t<td align=\"right\">";
//                     $strText .= $aryHeadResult["strmonetaryunitsign"] . " ";
//                     if (!$aryDetailResult[$i]["cursubtotalprice"]) {
//                         $strText .= "0.00";
//                     } else {
//                         $strText .= $aryDetailResult[$i]["cursubtotalprice"];
//                     }
//                     $aryHtml[] = $strText . "</td>\n";
//                 }
//                 // ����¾�ι��ܤϤ��Τޤ޽���
//                 else {
//                     $strLowColumnName = strtolower($strColumnName);
//                     if ($strLowColumnName == "strnote") {
//                         $strText .= nl2br($aryHeadResult[$strLowColumnName]);
//                     } else if (array_key_exists($strLowColumnName, $aryDetailResult[$i])) {
//                         $strText .= $aryDetailResult[$i][$strLowColumnName];
//                     } else {
//                         $strText .= $aryHeadResult[$strLowColumnName];
//                     }
//                     $TdData .= "\t<td>";
//                     $TdData .= $strText;
//                 }
//                 $TdData .= "</td>\n";
//                 if ($TdDataUse) {
//                     $aryHtml[] = $TdData;
//                 }
//             }
//         }
//         $aryHtml[] = "</tr>";
//     }
//     return $aryHtml;
// }

/**
 * ȯ���ǡ���HTML�Ѵ�
 *
 * @param    Array    $aryViewColumn        ��������
 * @param    Array    $aryResult            ȯ���ǡ���
 * @param    Array    $aryUserAuthority ��  ����
 * @param    boolean  $isMaxObj   ���������� �ǿ��о�
 * @param    boolean  $rownum   ���������� �����ֹ�
 * @access    public
 *
 */
function fncSetPurchaseOrderHtml($aryViewColumn, $aryResult, $aryUserAuthority, $isMaxObj, $rownum)
{
    for ($i = 0; $i < count($aryResult); $i++) {
        if ($isMaxObj) {
            $aryHtml[] = "<tr id=" . $aryResult[$i]["strordercode"] . ">";
            $aryHtml[] = "  <td class=\"rownum\">" . ($i + 1) . "</td>";
        } else {
            $aryHtml[] = "<tr id=" . $aryResult[$i]["strordercode"] . "_" . $aryResult[$i]["lngrevisionno"] . ">";
            $aryHtml[] = "  <td class=\"rownum\">" . $rownum . "." . ($i + 1) . "</td>";
        }
        for ($j = 0; $j < count($aryViewColumn); $j++) {
            $strColumn = $aryViewColumn[$j];
            // ɽ���оݤ��ܥ���ξ��
            if ($strColumn == "btnEdit" or $strColumn == "btnRecord" or $strColumn == "btnDelete") {
                // �����ܥ���
                if ($strColumn == "btnEdit" and $aryUserAuthority["Edit"]) {

                    $aryHtml[] = "  <td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngpurchaseorderno=\"" . $aryResult[$i]["lngpurchaseorderno"] . "\" lngrevisionno=\"" . $aryResult[$i]["lngrevisionno"] . "\" class=\"edit button\"></td>";
                    // ����ܥ���
                } else if ($strColumn == "btnRecord" and $aryResult[$i]["lngrevisionno"] != 0) {
                    // ���ӥ����ξ�硢����ܥ������ɽ��
                    $strOrderCode = sprintf("%s_%02d", $aryResult[$i]["strordercode"], $aryResult[$i]["lngrevisionno"]);
                    $aryHtml[] = "  <td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" rownum=\"" . ($i + 1) . "\" id=\"" . $aryResult[$i]["strordercode"] . "\" lngrevisionno=\"" . $aryResult[$i]["lngrevisionno"] . "\" class=\"history button\"></td>";
                } else {
                    $aryHtml[] = "  <td></td>";
                }
            } else {
                // ȯ��NO.
                if ($strColumn == "strOrderCode") {
                    $aryHtml[] = "  <td class=\"td-strordercode\" baseordercode=\"" . $aryResult[$i]["strordercode"] . "\">" . sprintf("%s_%02d", $aryResult[$i]["strordercode"], $aryResult[$i]["lngrevisionno"]) . "</td>";
                }
                // ȯ��ͭ��������
                if ($strColumn == "dtmExpirationDate") {
                    $aryHtml[] = "  <td class=\"td-dtmexpirationdate\">" . $aryResult[$i]["dtmexpirationdate"] . "</td>";
                }
                // ���ʥ�����
                if ($strColumn == "strProductCode") {
                    $aryHtml[] = "  <td class=\"td-strproductcode\">" . sprintf("[%s]", $aryResult[$i]["strproductcode"]) . "</td>";
                }
                // ��Ͽ��
                if ($strColumn == "dtmInsertDate") {
                    $aryHtml[] = "  <td class=\"td-dtminsertdate\">" . $aryResult[$i]["dtminsertdate"] . "</td>";
                }
                // ���ϼ�
                if ($strColumn == "lngInputUserCode") {
                    $aryHtml[] = "  <td class=\"td-lnginsertusercode\">" . sprintf("[%s] %s", $aryResult[$i]["lnginsertusercode"], $aryResult[$i]["strinsertusername"]) . "</td>";
                }
                // ����̾
                if ($strColumn == "strProductName") {
                    $aryHtml[] = "  <td class=\"td-strproductname\">" . $aryResult[$i]["strproductname"] . "</td>";
                }
                // ����̾(�Ѹ�)
                if ($strColumn == "strProductEnglishName") {
                    $aryHtml[] = "  <td class=\"td-strproductenglishname\">" . $aryResult[$i]["strproductenglishname"] . "</td>";
                }
                // �Ķ�����
                if ($strColumn == "lngInChargeGroupCode") {
                    $aryHtml[] = "  <td class=\"td-lnggroupcode\">" . sprintf("[%s] %s", $aryResult[$i]["lnggroupcode"], $aryResult[$i]["strgroupname"]) . "</td>";
                }
                // ��ȯô����
                if ($strColumn == "lngInChargeUserCode") {
                    $aryHtml[] = "  <td class=\"td-lngusercode\">" . sprintf("[%s] %s", $aryResult[$i]["lngusercode"], $aryResult[$i]["strusername"]) . "</td>";
                }
                // ������
                if ($strColumn == "lngCustomerCode") {
                    $aryHtml[] = "  <td class=\"td-lngcustomercode\">" . sprintf("[%s] %s", $aryResult[$i]["lngcustomercode"], $aryResult[$i]["strcustomername"]) . "</td>";
                }
                // ��ʧ���
                if ($strColumn == "lngPayConditionCode") {
                    $aryHtml[] = "  <td class=\"td-strpaycnoditionname\">" . $aryResult[$i]["strpayconditionname"] . "</td>";
                }
                // ��ȴ���
                if ($strColumn == "curTotalPrice") {
                    $aryHtml[] = "  <td class=\"td-curtotalprice\">" . sprintf("%s %.2f", $aryResult[$i]["strmonetaryunitsign"], $aryResult[$i]["curtotalprice"]) . "</td>";
                }
                // Ǽ�ʾ��
                if ($strColumn == "lngDeliveryPlaceCode") {
                    $aryHtml[] = "  <td class=\"td-strdeliveryplacename\">" . $aryResult[$i]["strdeliveryplacename"] . "</td>";
                }
                // ��������
                if ($strColumn == "strNote") {
                    $aryHtml[] = "  <td class=\"td-strnote\">" . $aryResult[$i]["strnote"] . "</td>";
                }
            }
        }
        $aryHtml[] = "</tr>";
    }

    return implode("\n", $aryHtml);
}

/**
 * �������ɽ���ؿ�
 *
 *    ������̤���ơ��֥빽���Ƿ�̤���Ϥ���ؿ�
 *
 *    @param  Array     $aryResult             ������̤���Ǽ���줿����
 *    @param  Array     $aryViewColumn         ɽ���оݥ����̾������
 *    @param  Array     $aryData             �Уϣӣԥǡ�����
 *    @param    Array    $aryUserAuthority    �桼�����������Ф��븢�¤����ä�����
 *    @param    Array    $aryTytle            ����̾����Ǽ���줿����ʸƤӽФ��������ܸ��ѡ��Ѹ��Ѥ��ڤ��ؤ���
 *    @param  Object    $objDB               DB���֥�������
 *    @param  Object    $objCache           ����å��奪�֥�������
 *    @param    Array    $aryTableName        ɽ�������̾�ȥޥ����⥫���̾�ѹ���
 *    @access public
 */
function fncSetPurchaseTable($aryResult, $arySearchColumn, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableName)
{
    // ����
    include_once 'conf.inc';
    require_once LIB_DEBUGFILE;

    // ɽ�������Υإå�������������ʬΥ����
    for ($i = 0; $i < count($aryViewColumn); $i++) {
        $strColumnName = $aryViewColumn[$i];

        // �ܥ���ξ�礳����ɽ������ɽ���ڤ��ؤ�
        if ($strColumnName == "btnDetail") {
            if ($aryUserAuthority["Detail"]) {
                $aryHeadViewColumn[] = $strColumnName;
            }
        } else if ($strColumnName == "btnFix") {
            if ($aryUserAuthority["Fix"]) {
                $aryHeadViewColumn[] = $strColumnName;
            }
        } else if ($strColumnName == "btnDelete") {
            if ($aryUserAuthority["Delete"]) {
                $aryHeadViewColumn[] = $strColumnName;
            }
        } else if ($strColumnName == "btnInvalid") {
            if ($aryUserAuthority["Invalid"]) {
                $aryHeadViewColumn[] = $strColumnName;
            }
        } else if ($strColumnName == "btnAdmin") {
            if ($aryUserAuthority["Admin"]) {
                $aryHeadViewColumn[] = $strColumnName;
            }
        }
        // 2004.03.31 suzukaze update start
        // �ܺ���
        else if ($strColumnName == "strProductCode"
            or $strColumnName == "lngInChargeGroupCode" or $strColumnName == "lngInChargeUserCode" // <-- added by siato
             or $strColumnName == "lngRecordNo" or $strColumnName == "lngStockSubjectCode" or $strColumnName == "lngStockItemCode"
            or $strColumnName == "strGoodsCode" or $strColumnName == "lngDeliveryMethodCode" or $strColumnName == "curProductPrice"
            or $strColumnName == "lngProductUnitCode" or $strColumnName == "lngProductQuantity" or $strColumnName == "curSubTotalPrice"
            or $strColumnName == "strDetailNote" or $strColumnName == "dtmDeliveryDate"
            or $strColumnName == "strProductName" or $strColumnName == "strProductEnglishName" or $strColumnName == "strMoldNo")
        // 2004.03.31 suzukaze update end
        {
            $aryDetailViewColumn[] = $strColumnName;
            $aryHeadViewColumn[] = $strColumnName;
        }
        // �إå���
        else {
            $aryHeadViewColumn[] = $strColumnName;
        }
    }

    // �ơ��֥�η���
    $lngResultCount = count($aryResult);

    $lngColumnCount = 1;

    // ����̾������� start=========================================
    $aryHtml[] = "<thead>";
    $aryHtml[] = "<tr>";
    $aryHtml[] = "\t<th class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/copy_off_bt.gif\" class=\"copy button\"></th>";

    // ɽ���оݥ������������������
    for ($j = 0; $j < count($aryViewColumn); $j++) {
        $Addth = "\t<th>";
        $strColumnName = $aryViewColumn[$j];

        // �����ȹ��ܰʳ��ξ��
        if ($strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete") {
            // �����ȹ��ܰʳ��ξ��
            if (($strColumnName == "btnDetail" and $aryUserAuthority["Detail"])
                or ($strColumnName == "btnFix" and $aryUserAuthority["Fix"])
                or ($strColumnName == "btnDelete" and $aryUserAuthority["Delete"])
                or ($strColumnName == "btnAdmin" and $aryUserAuthority["Admin"])) {
                $Addth .= $aryTytle[$strColumnName];
            }
        }
        // �����ȹ��ܤξ��
        else {
            $Addth .= $aryTytle[$strColumnName];
        }

        $Addth .= "</th>";
        $aryHtml[] = $Addth;
    }
    $aryHtml[] = "</tr>";
    $aryHtml[] = "</thead>";

    // ����̾������� end=========================================

    $aryHtml[] = "<tbody>";

    for ($i = 0; $i < $lngResultCount; $i++) {
        // �����⡼���Ѳ���Х���������ǡ�������start==================================
        // �����⡼�ɤξ�硡Ʊ��ȯ�����ɤΰ����������ɽ������

        // ��Х���������̵����ȯ�����ɤ��������
        if (strlen($aryResult[$i]["strordercode"]) >= 9) {
            $strOrderCodeBase = preg_replace("/" . strstr($aryResult[$i]["strordercode"] . "/", "_"), "", $aryResult[$i]["strordercode"]);
        } else {
            $strOrderCodeBase = $aryResult[$i]["strordercode"];
        }

//        $strSameOrderCodeQuery = fncGetSearchPurchaseSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, $strOrderCodeBase, $aryResult[$i]["lngorderno"], FALSE ,$aryResult[$i]["lngrevisionno"]);
        //        // �ͤ�Ȥ� =====================================
        //        list ( $lngResultID, $lngResultNum ) = fncQuery( $strSameOrderCodeQuery, $objDB );

        // ����Υ��ꥢ
        //        unset( $arySameOrderCodeResult );

//        if ( $lngResultNum )
        //        {
        //            for ( $j = 0; $j < $lngResultNum; $j++ )
        //            {
        //                $arySameOrderCodeResult[] = $objDB->fetchArray( $lngResultID, $j );
        //            }
        //            $lngSameOrderCount = $lngResultNum;
        //        }
        //        $objDB->freeResult( $lngResultID );

        // Ʊ��ȯ�����ɤǤβ���Х����ǡ�����¸�ߤ����
        //        if ( $lngResultNum )
        //        {
        //            for ( $j = 0; $j < $lngSameOrderCount; $j++ )
        //            {
        // ���������ʬ������

//                reset( $arySameOrderCodeResult[$j] );

        // ���ٽ����Ѥ�Ĵ��
        $lngDetailViewCount = count($aryDetailViewColumn);

        if ($lngDetailViewCount) {
            // ���ٹԿ���Ĵ��
            $strDetailQuery = fncGetOrderToProductSQL($aryDetailViewColumn, $aryResult[$i]["lngorderno"], $aryResult[$i]["lngrevisionno"], $aryData, $objDB);
//("kids2.log", $strDetailQuery , __FILE__, __LINE__, "a" );
            echo $strDetailQuery;
            // �����꡼�¹�
            if (!$lngDetailResultID = $objDB->execute($strDetailQuery)) {
                $strMessage = fncOutputError(3, "DEF_FATAL", "�����꡼�¹ԥ��顼", true, "../po/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
            }

            $lngDetailCount = pg_num_rows($lngDetailResultID);

            // ����Υ��ꥢ
            unset($aryDetailResult);

            // ��̤μ���
            if ($lngDetailCount) {
                for ($k = 0; $k < $lngDetailCount; $k++) {
                    $aryDetailResult[] = pg_fetch_array($lngDetailResultID, $k, PGSQL_ASSOC);
                }
            }

            $objDB->freeResult($lngDetailResultID);
        }

        // ���쥳����ʬ�ν���
        $aryHtml_add = fncSetPurchaseHeadTable($lngColumnCount, $aryResult[$i], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameOrderCount, $aryResult[$i]["lngrevisionno"], $aryResult[$i]);
//                $aryHtml_add = fncSetPurchaseHeadTable ( $lngColumnCount, $arySameOrderCodeResult[$j], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameOrderCount, $j, $arySameOrderCodeResult[0] );
        $lngColumnCount = $lngColumnCount + count($aryDetailResult);

        $strColBuff = '';
        for ($k = 0; $k < count($aryHtml_add); $k++) {
            $strColBuff .= $aryHtml_add[$k];
        }
        $aryHtml[] = $strColBuff;
//            }
        //        }

        // �����⡼���Ѳ���Х����ǡ�������end==================================

    }

    $aryHtml[] = "</tbody>";

    $strhtml = implode("\n", $aryHtml);

    return $strhtml;
}

/**
 * ȯ��ǡ���HTML�Ѵ�
 *
 * @param    Array    $aryResult            ȯ��ǡ���
 * @param    Array    $aryViewColumn        ɽ����
 * @param    Array    $aryUserAuthority    ����
 * @param    Array    $aryTitle            ��̾
 * @param    Object   $objDB                DB���֥�������
 * @param    Object   $objCache            ����å��奪�֥�������
 * @param    Array    $aryTableName        �ơ��֥�̾
 * @param    boolean  $isMaxObj           ���ǿ��о�
 */
function fncSetPurchaseOrderTable($aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTitle, $objDB, $objCache, $aryTableName, $isMaxObj)
{
    // ɽ�������Υإå�������������ʬΥ����
    for ($i = 0; $i < count($aryViewColumn); $i++) {
        $strColumnName = $aryViewColumn[$i];

        // �ܥ���ξ�礳����ɽ������ɽ���ڤ��ؤ�
        if ($strColumnName == "btnEdit") {
            if ($aryUserAuthority["Edit"]) {
                $aryHeadViewColumn[] = $strColumnName;
            }
        } else if ($strColumnName == "btnRecord") {
            $aryHeadViewColumn[] = $strColumnName;
        } else if ($strColumnName == "dtmInsertDate"
            or $strColumnName == "lngInputUserCode"
            or $strColumnName == "dtmExpirationDate"
            or $strColumnName == "strOrderCode"
            or $strColumnName == "strProductCode"
            or $strColumnName == "strProductName"
            or $strColumnName == "strProductEnglishName"
            or $strColumnName == "lngInChargeGroupCode"
            or $strColumnName == "lngInChargeUserCode"
            or $strColumnName == "lngCustomerCode"
            or $strColumnName == "strDeliveryPlaceName"
            or $strColumnName == "lngMonetaryunitCode"
            or $strColumnName == "lngMonetaryRateCode"
            or $strColumnName == "lngPayConditionCode") {
            $aryDetailViewColumn[] = $strColumnName;
            $aryHeadViewColumn[] = $strColumnName;
        } else {
            $aryHeadViewColumn[] = $strColumnName;
        }
    }

    // �ơ��֥�η���
    $lngColumnCount = 1;

    // ����̾�������
    $aryHtml[] = "<thead>";
    $aryHtml[] = "<tr>";
    $aryHtml[] = "\t<th class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/copy_off_bt.gif\" class=\"copy button\"></th>";

    // ɽ���оݥ������������������
    for ($i = 0; $i < count($aryViewColumn); $i++) {
        $addTh = "\t<th>";
        $strColumnName = $aryViewColumn[$i];

        if ($strColumnName == "btnPreview" or $strColumnName == "btnEdit" or $strColumnName == "btnRecord") {
            // �����ȹ��ܰʳ��ξ��
            if (($strColumnName == "btnPreview" and $aryUserAuthority["Preview"])
                or ($strColumnName == "btnEdit" and $aryUserAuthority["Edit"])
                or ($strColumnName == "btnRecord")
            ) {
                $addTh .= $aryTitle[$strColumnName];
            } else {
                // ɽ���оݳ�
                continue;
            }
        } else {
            // �����ȹ��ܤξ��
            $addTh .= $aryTitle[$strColumnName];
        }

        $addTh .= "</th>";
        $aryHtml[] = $addTh;
    }
    $aryHtml[] = "</tr>";
    $aryHtml[] = "</thead>";

    // �ǡ�����
    $aryHtml[] = "<tbody>";
    $lngResultCount = count($aryResult);

    $aryHtml[] = fncSetPurchaseOrderHtml($aryViewColumn, $aryResult, $aryUserAuthority, $isMaxObj, null);
    $aryHtml[] = "</tbody>";
    $strHtml = implode("\n", $aryHtml);

    return $strHtml;
}

// /**
//  * ȯ���ǡ���HTML�Ѵ�
//  *
//  * @param    Array    $aryResult            ȯ���ǡ���
//  * @param    Array    $aryViewColumn        ɽ����
//  * @param    Array    $aryUserAuthority    ����
//  * @param    Array    $aryTitle            ��̾
//  * @param    Object    $objDB                DB���֥�������
//  * @param    Object    $objCache            ����å��奪�֥�������
//  * @param    Array    $aryTableName        �ơ��֥�̾
//  */

// function fncSetPurchaseOrderTable2($aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTitle, $objDB, $objCache, $aryTableName)
// {
//     for ($i = 0; $i < count($aryDetailResult); $i++) {
//         $aryHtml[] = "<tr>";
//         $aryHtml[] = "\t<td>" . ($lngColumnCount + 1) . "</td>";

//         // ɽ���оݥ������������̤ν���
//         for ($j = 0; $j < count($aryHeadViewColumn); $j++) {
//             $strColumnName = $aryHeadViewColumn[$j];
//             $tdData = "";

//             // ɽ���оݤ��ܥ���ξ��
//             if ($strColumnName == "btnEdit" or $strColumnName == "btnRecord" or $strColumnName == "btnDelete") {
//                 // �����ܥ���
//                 if ($strColumnName == "btnEdit" and $aryUserAuthority["Edit"]) {
//                     $aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngPurchaseOrderNo=\"" . $aryDetailResult[$i]["lngPurchaseOrderNo"] . "\" class=\"detail button\"></td>\n";
//                 } else {
//                     $aryHtml[] = "\t<td></td>\n";
//                 }

//                 // ����ܥ���
//                 if ($strColumnName == "btnRecord") {
//                     if ($aryHeadResult["lngRevisionNo"] > 0 and $aryUserAuthority["Admin"]) {
//                         $aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngPurchaseOrderNo=\"" . $aryDetailResult[$i]["lngPurchaseOrderNo"] . "\" strOrderCode =\"" . $aryResult["strordercode"] . "\" class=\"fix button\"></td>\n";
//                     } else {
//                         $aryHtml[] = "\t<td></td>\n";
//                     }
//                 }

//                 // ����ѥܥ���
//                 if ($strColumnName == "btnDelete" and $aryUserAuthority["Admin"]) {
//                     if ($aryHeadResult["lngRevisionNo"] == -1) {
//                         $aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngPurchaseOrderNo=\"" . $aryDetailResult[$i]["lngPurchaseOrderNo"] . "\" class=\"fix button\"></td>\n";
//                     } else {
//                         $aryHtml[] = "\t<td></td>\n";
//                     }
//                 }
//             }
//             $tdData .= "</td>\n";
//             //if($tdDataUse){
//             $aryHtml[] = $tdData;
//             //}
//         }
//         $aryHtml[] = "</tr>";
//     }
//     return $aryHtml;
// }

function fncResortSearchColumn($aryViewColumn)
{
    $aryResult = array();

    $aryResult[] = "btnDetail";
    $aryResult[] = "btnFix";
    $aryResult[] = "Record";
    if (in_array("btnAdmin", $aryViewColumn)) {$aryResult[] = "btnAdmin";}
    if (in_array("strOrderCode", $aryViewColumn)) {$aryResult[] = "strOrderCode";}
    if (in_array("dtmExpirationDate", $aryViewColumn)) {$aryResult[] = "dtmExpirationDate";}
    if (in_array("strProductCode", $aryViewColumn)) {$aryResult[] = "strProductCode";}
    if (in_array("dtmInsertDate", $aryViewColumn)) {$aryResult[] = "dtmInsertDate";}
    if (in_array("lngInputUserCode", $aryViewColumn)) {$aryResult[] = "lngInputUserCode";}
    if (in_array("strProductName", $aryViewColumn)) {
        $aryResult[] = "strProductName";
        $aryResult[] = "strProductEnglishName";
    }
    if (in_array("lngInChargeGroupCode", $aryViewColumn)) {$aryResult[] = "lngInChargeGroupCode";}
    if (in_array("lngInChargeUserCode", $aryViewColumn)) {$aryResult[] = "lngInChargeUserCode";}
    if (in_array("lngCustomerCode", $aryViewColumn)) {$aryResult[] = "lngCustomerCode";}
    if (in_array("lngStockSubjectCode", $aryViewColumn)) {$aryResult[] = "lngStockSubjectCode";}
    if (in_array("lngStockItemCode", $aryViewColumn)) {$aryResult[] = "lngStockItemCode";}
    if (in_array("dtmDeliveryDate", $aryViewColumn)) {$aryResult[] = "dtmDeliveryDate";}
    if (in_array("lngOrderStatusCode", $aryViewColumn)) {$aryResult[] = "lngOrderStatusCode";}
    if (in_array("lngRecordNo", $aryViewColumn)) {$aryResult[] = "lngRecordNo";}
    if (in_array("curProductPrice", $aryViewColumn)) {$aryResult[] = "curProductPrice";}
    if (in_array("lngProductQuantity", $aryViewColumn)) {$aryResult[] = "lngProductQuantity";}
    if (in_array("curSubTotalPrice", $aryViewColumn)) {$aryResult[] = "curSubTotalPrice";}
    if (in_array("strNote", $aryViewColumn)) {$aryResult[] = "strNote";}
    if (in_array("strDetailNote", $aryViewColumn)) {$aryResult[] = "strDetailNote";}
    if (in_array("btnDelete", $aryViewColumn)) {$aryResult[] = "btnDelete";}

    return $aryResult;
}

function fncResortSearchColumn2($aryViewColumn)
{
    $aryResult = array();

    if (in_array("btnPreview", $aryViewColumn)) {$aryResult[] = "btnPreview";}
    $aryResult[] = "btnEdit";
    $aryResult[] = "btnRecord";
//    $aryResult[] = "btnDelete";
    if (in_array("strOrderCode", $aryViewColumn)) {$aryResult[] = "strOrderCode";}
    if (in_array("dtmExpirationDate", $aryViewColumn)) {$aryResult[] = "dtmExpirationDate";}
    if (in_array("strProductCode", $aryViewColumn)) {$aryResult[] = "strProductCode";}
    if (in_array("dtmInsertDate", $aryViewColumn)) {$aryResult[] = "dtmInsertDate";}
    if (in_array("lngInputUserCode", $aryViewColumn)) {$aryResult[] = "lngInputUserCode";}
    if (in_array("strProductCode", $aryViewColumn)) {
        $aryResult[] = "strProductName";
        $aryResult[] = "strProductEnglishName";
    }
    if (in_array("lngInChargeGroupCode", $aryViewColumn)) {$aryResult[] = "lngInChargeGroupCode";}
    if (in_array("lngInChargeUserCode", $aryViewColumn)) {$aryResult[] = "lngInChargeUserCode";}
    if (in_array("lngCustomerCode", $aryViewColumn)) {$aryResult[] = "lngCustomerCode";}
    if (in_array("lngPayConditionCode", $aryViewColumn)) {$aryResult[] = "lngPayConditionCode";}
    if (in_array("curTotalPrice", $aryViewColumn)) {$aryResult[] = "curTotalPrice";}
    if (in_array("lngDeliveryPlaceCode", $aryViewColumn)) {$aryResult[] = "lngDeliveryPlaceCode";}
    if (in_array("strNote", $aryViewColumn)) {$aryResult[] = "strNote";}
    if (in_array("btnDelete", $aryViewColumn)) {$aryResult[] = "btnDelete";}

    return $aryResult;
}

function fncGetPurchseOrderByOrderCodeSQL($strOrderCode, $lngRevisionNo)
{
    $aryQuery[] = "SELECT";
    $aryQuery[] = "  mp.lngpurchaseorderno as lngPurchaseOrderNo";
    $aryQuery[] = "  , mp.lngrevisionno as lngRevisionNo";
    $aryQuery[] = "  , mp.strrevisecode as strReviseCode";
    $aryQuery[] = "  , mp.strordercode as strOrderCode";
    $aryQuery[] = "  , to_char(mp.dtmexpirationdate, 'YYYY/MM/DD') as dtmExpirationDate";
    $aryQuery[] = "  , mp.strproductcode as strProductCode";
    $aryQuery[] = "  , mp.strproductname as strProductName";
    $aryQuery[] = "  , mp.strproductenglishname as strProductEnglishName";
    $aryQuery[] = "  , to_char(mp.dtminsertdate, 'YYYY/MM/DD') as dtmInsertDate";
    $aryQuery[] = "  , input_user.struserdisplaycode AS lngInsertUserCode";
    $aryQuery[] = "  , mp.strinsertusername AS strInsertUserName";
    $aryQuery[] = "  , mg.strgroupdisplaycode AS lngGroupCode";
    $aryQuery[] = "  , mp.strgroupname as strGroupName";
    $aryQuery[] = "  , mu.struserdisplaycode as lngUserCode";
    $aryQuery[] = "  , mp.strusername as strUserName";
    $aryQuery[] = "  , mc_stock.strcompanydisplaycode as lngCustomerCode";
    $aryQuery[] = "  , mp.strcustomername as strCustomerName";
    $aryQuery[] = "  , mp.lngpayconditioncode as lngPayConditionCode";
    $aryQuery[] = "  , mp.strpayconditionname as strPayConditionName";
    $aryQuery[] = "  , mp.lngmonetaryunitcode as lngMonetaryUnitCode";
    $aryQuery[] = "  , mp.strmonetaryunitsign as strMonetaryUnitSign";
    $aryQuery[] = "  , mp.curtotalprice as curTotalPrice";
    $aryQuery[] = "  , mp.strdeliveryplacename as strDeliveryPlaceName";
    $aryQuery[] = "  , mp.strnote as strNote ";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  m_purchaseorder mp ";
    $aryQuery[] = "  left join m_user input_user ";
    $aryQuery[] = "    on input_user.lngusercode = mp.lnginsertusercode ";
    $aryQuery[] = "  left join m_group mg ";
    $aryQuery[] = "    on mg.lnggroupcode = mp.lnggroupcode ";
    $aryQuery[] = "  left join m_user mu ";
    $aryQuery[] = "    on mu.lngusercode = mp.lngusercode ";
    $aryQuery[] = "  left join m_company mc_stock ";
    $aryQuery[] = "    on mc_stock.lngcompanycode = mp.lngcustomercode ";
    $aryQuery[] = "  left join m_company mc_delivary ";
    $aryQuery[] = "    on mc_delivary.lngcompanycode = mp.lngdeliveryplacecode ";
    $aryQuery[] = "WHERE";
    $aryQuery[] = "  mp.strordercode = '" . $strOrderCode . "' ";
    $aryQuery[] = "  AND mp.lngrevisionno <> " . $lngRevisionNo . " ";
    $aryQuery[] = "ORDER BY";
    $aryQuery[] = "  mp.lngpurchaseorderno";
    $aryQuery[] = "  , mp.lngrevisionno DESC";

    return implode("\n", $aryQuery);
}

// /**
//  * �������ɤˤ��ǡ����ξ��֤��ǧ����
//  *
//  * @param [type] $strordercode
//  * @param [type] $objDB
//  * @return void [0:�����оݳ��ǡ�����1�������оݥǡ���]
//  */
// function fncCheckData($strordercode, $objDB)
// {
//     $result = 1;
//     unset($aryQuery);
//     $aryQuery[] = "SELECT";
//     $aryQuery[] = " min(lngrevisionno) lngrevisionno, bytInvalidFlag, strordercode ";
//     $aryQuery[] = "FROM m_order ";
//     $aryQuery[] = "WHERE strordercode='" . $strordercode . "' ";
//     $aryQuery[] = "group by strordercode, bytInvalidFlag";

//     // �������ʿ�פ�ʸ������Ѵ�
//     $strQuery = implode("\n", $aryQuery);

//     list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

//     if ($lngResultNum) {
//         $resultObj = $objDB->fetchArray($lngResultID, 0);
//     }

//     $objDB->freeResult($lngResultID);

//     if ($resultObj["lngrevisionno"] < 0 || $resultObj["bytInvalidFlag"]) {
//         $result = 0;
//     }
//     return $result;
// }

// /**
//  * �إå������ǡ���������
//  *
//  * @param [type] $doc
//  * @param [type] $trBody
//  * @param [type] $bgcolor
//  * @param [type] $aryTableHeaderName
//  * @param [type] $record
//  * @param [type] $toUTF8Flag
//  * @return void
//  */
// function fncSetHeaderDataToTr($doc, $trBody, $bgcolor, $aryTableHeaderName, $displayColumns, $record, $toUTF8Flag)
// {
//     // ���ꤵ�줿�ơ��֥���ܤΥ�����������
//     foreach ($aryTableHeaderName as $key => $value) {
//         // ɽ���оݤΥ����ξ��
//         if (array_key_exists($key, $displayColumns)) {
//             // �����̤�ɽ���ƥ����Ȥ�����
//             switch ($key) {
//                 // ��Ͽ��
//                 case "dtminsertdate":
//                     $td = $doc->createElement("td", $record["dtminsertdate"]);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // [���ϼ�ɽ��������] ���ϼ�ɽ��̾
//                 case "lnginputusercode":
//                     $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // ȯ��Σ�.
//                 case "strordercode":
//                     $textContent = $record["strordercode_desc"];
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // ��ӥ�����ֹ�
//                 case "lngrevisionno":
//                     $td = $doc->createElement("td", $record["lngrevisionno"]);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // ���ʥ�����
//                 case "strproductcode":
//                     $td = $doc->createElement("td", $record["strproductcode"]);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // ���ʥޥ���.���ʥ�����(���ܸ�)
//                 case "strproductname":
//                     $textContent = $record["strproductname"];
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // ���ʥޥ���.����̾��(�Ѹ�)
//                 case "strproductenglishname":
//                     $textContent = $record["strproductenglishname"];
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // [�Ķ�����ɽ��������] �Ķ�����ɽ��̾
//                 case "lnginchargegroupcode":
//                     if ($record["lnginchargegroupcode"] != '') {
//                         $textContent = "[" . $record["lnginchargegroupcode"] . "]" . " " . $record["strinchargegroupname"];
//                     } else {
//                         $textContent = "    ";
//                     }
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // [��ȯô����ɽ��������] ��ȯô����ɽ��̾
//                 case "lnginchargeusercode":
//                     if ($record["lnginchargeusercode"] != '') {
//                         $textContent = "[" . $record["lnginchargeusercode"] . "]" . " " . $record["strinchargeusername"];
//                     } else {
//                         $textContent = "    ";
//                     }
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // [������ɽ��������] ������ɽ��̾
//                 case "lngcustomercode":
//                     if ($record["strcustomerdisplaycode"] != '') {
//                         $textContent = "[" . $record["strcustomerdisplaycode"] . "]" . " " . $record["strcustomerdisplayname"];
//                     } else {
//                         $textContent = "    ";
//                     }
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // ��������
//                 case "lngstocksubjectcode":
//                     if ($record["lngstocksubjectcode"] != '') {
//                         $textContent = "[" . $record["lngstocksubjectcode"] . "]" . " " . $record["strstocksubjectname"];
//                     } else {
//                         $textContent = "    ";
//                     }
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // ��������
//                 case "lngstockitemcode":
//                     if ($record["lngstockitemcode"] != '') {
//                         $textContent = "[" . $record["lngstockitemcode"] . "]" . " " . $record["strstockitemname"];
//                     } else {
//                         $textContent = "    ";
//                     }
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // Ǽ��
//                 case "dtmdeliverydate":
//                     $textContent = $record["dtmdeliverydate"];
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // ����
//                 case "lngorderstatuscode":
//                     $textContent = $record["strorderstatusname"];
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // ���ٹ��ֹ�
//                 case "lngrecordno":
//                     $td = $doc->createElement("td", $record["lngorderdetailno"]);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // ñ��
//                 case "curproductprice":
//                     $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curproductprice"]));
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // ����
//                 case "lngproductquantity":
//                     $textContent = $record["lngproductquantity"];
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // ��ȴ���
//                 case "cursubtotalprice":
//                     $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["cursubtotalprice"]));
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // ��������
//                 case "strdetailnote":
//                     $textContent = $record["strdetailnote"];
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//             }
//         }
//     }
// }


// function fncGetOrdersByStrOrderCodeSQL($strOrderCode, $lngOrderDetailNo, $lngRevisionNo)
// {
//     // ���ٸ�������
//     $detailConditionCount = 0;
//     // ���������Ω��
//     $aryQuery = array();
//     $aryQuery[] = "SELECT";
//     $aryQuery[] = "  o.lngOrderNo as lngOrderNo";
//     $aryQuery[] = "  , o.lngRevisionNo as lngRevisionNo";
//     $aryQuery[] = "  , od.lngOrderDetailNo";
//     $aryQuery[] = "  , o.strOrderCode as strOrderCode";
//     $aryQuery[] = "  , o.strOrderCode || '_' || to_char(o.lngRevisionNo, 'FM00') as strOrderCode_desc";
//     $aryQuery[] = "  , to_char(o.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS') as dtmInsertDate";
//     $aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
//     $aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
//     $aryQuery[] = "  , cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
//     $aryQuery[] = "  , cust_c.strCompanyDisplayName as strCustomerDisplayName";
//     $aryQuery[] = "  , o.lngOrderStatusCode as lngOrderStatusCode";
//     $aryQuery[] = "  , os.strOrderStatusName as strOrderStatusName";
//     $aryQuery[] = "  , mm.strMonetaryUnitSign as strMonetaryUnitSign";
//     $aryQuery[] = "  , od.strProductCode";
//     $aryQuery[] = "  , od.strProductName";
//     $aryQuery[] = "  , od.strProductEnglishName";
//     $aryQuery[] = "  , od.lngInChargeGroupCode";
//     $aryQuery[] = "  , od.strInChargeGroupName";
//     $aryQuery[] = "  , od.lngInChargeUserCode";
//     $aryQuery[] = "  , od.strInChargeUserName";
//     $aryQuery[] = "  , od.lngStockSubjectCode";
//     $aryQuery[] = "  , od.strStockSubjectName";
//     $aryQuery[] = "  , od.lngStockItemCode";
//     $aryQuery[] = "  , od.strstockitemname";
//     $aryQuery[] = "  , od.dtmDeliveryDate";
//     $aryQuery[] = "  , od.curProductPrice";
//     $aryQuery[] = "  , od.lngProductQuantity";
//     $aryQuery[] = "  , od.curSubTotalPrice";
//     $aryQuery[] = "  , od.strDetailNote ";
//     $aryQuery[] = "FROM";
//     $aryQuery[] = "  m_Order o ";
//     $aryQuery[] = "  LEFT JOIN m_User input_u ";
//     $aryQuery[] = "    ON o.lngInputUserCode = input_u.lngUserCode ";
//     $aryQuery[] = "  LEFT JOIN m_Company cust_c ";
//     $aryQuery[] = "    ON o.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
//     $aryQuery[] = "  LEFT JOIN m_OrderStatus os ";
//     $aryQuery[] = "    USING (lngOrderStatusCode) ";
//     $aryQuery[] = "  LEFT JOIN m_MonetaryUnit mm ";
//     $aryQuery[] = "    ON o.lngMonetaryUnitCode = mm.lngMonetaryUnitCode";
//     $aryQuery[] = "  , ( ";
//     $aryQuery[] = "      select";
//     $aryQuery[] = "        od1.lngorderno";
//     $aryQuery[] = "        , od1.lngorderDetailNo";
//     $aryQuery[] = "        , od1.lngRevisionNo";
//     $aryQuery[] = "        , od1.strProductCode || '_' || od1.strReviseCode as strProductCode";
//     $aryQuery[] = "        , mp.strProductName as strProductName";
//     $aryQuery[] = "        , mp.strProductEnglishName as strProductEnglishName";
//     $aryQuery[] = "        , mg.strgroupdisplaycode as lngInChargeGroupCode";
//     $aryQuery[] = "        , mg.strgroupdisplayname as strInChargeGroupName";
//     $aryQuery[] = "        , mu.struserdisplaycode as lngInChargeUserCode";
//     $aryQuery[] = "        , mu.struserdisplayname as strInChargeUserName";
//     $aryQuery[] = "        , od1.lngStockSubjectCode as lngStockSubjectCode";
//     $aryQuery[] = "        , ss.strStockSubjectName as strStockSubjectName";
//     $aryQuery[] = "        , od1.lngStockItemCode as lngStockItemCode";
//     $aryQuery[] = "        , si.strstockitemname as strstockitemname";
//     $aryQuery[] = "        , to_char(od1.dtmDeliveryDate, 'YYYY/MM/DD') as dtmDeliveryDate";
//     $aryQuery[] = "        , to_char(od1.curProductPrice, '9,999,999,990.9999') as curProductPrice";
//     $aryQuery[] = "        , to_char(od1.lngProductQuantity, '9,999,999,990') as lngProductQuantity";
//     $aryQuery[] = "        , to_char(od1.curSubTotalPrice, '9,999,999,990.99') as curSubTotalPrice";
//     $aryQuery[] = "        , od1.strNote as strDetailNote ";
//     $aryQuery[] = "      from";
//     $aryQuery[] = "        t_orderdetail od1 ";
//     $aryQuery[] = "        LEFT JOIN m_product mp ";
//     $aryQuery[] = "          on mp.strproductcode = od1.strproductcode ";
//     $aryQuery[] = "          and mp.strrevisecode = od1.strrevisecode ";
//     $aryQuery[] = "          and mp.lngrevisionno = od1.lngrevisionno ";
//     $aryQuery[] = "        LEFT JOIN m_group mg ";
//     $aryQuery[] = "          on mg.lnggroupcode = mp.lnginchargegroupcode ";
//     $aryQuery[] = "        LEFT JOIN m_user mu ";
//     $aryQuery[] = "          on mu.lngusercode = mp.lnginchargeusercode ";
//     $aryQuery[] = "        LEFT JOIN m_StockSubject ss ";
//     $aryQuery[] = "          ON od1.lngstocksubjectcode = ss.lngstocksubjectcode ";
//     $aryQuery[] = "        LEFT JOIN m_stockitem si ";
//     $aryQuery[] = "          ON od1.lngstockitemcode = si.lngstockitemcode ";
//     $aryQuery[] = "          AND od1.lngstocksubjectcode = si.lngstocksubjectcode ";
//     $aryQuery[] = "    ) od ";
//     $aryQuery[] = "WHERE";
//     $aryQuery[] = "  od.lngorderno = o.lngorderno ";
//     $aryQuery[] = "  AND od.lngRevisionNo = o.lngRevisionNo ";
//     $aryQuery[] = "  AND o.lngRevisionNo <>  " . $lngRevisionNo . "";
//     $aryQuery[] = "  AND o.strordercode = '" . $strOrderCode . "'";
//     $aryQuery[] = "  AND od.lngOrderDetailNo = '" . $lngOrderDetailNo . "'";
//     $aryQuery[] = "ORDER BY";
//     $aryQuery[] = "  o.strordercode, od.lngOrderDetailNo, o.lngRevisionNo DESC";

//     // �������ʿ�פ�ʸ������Ѵ�
//     $strQuery = implode("\n", $aryQuery);

//     return $strQuery;
// }
