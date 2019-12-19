<?
/**
 *    Ģɼ���� ���Ѹ��������ѥ饤�֥��
 *
 *    @package   KIDS
 *    @copyright Copyright &copy; 2004, AntsBizShare
 *    @author    Kenji Chiba
 *    @access    public
 *    @version   1.00
 */

// ����졼�����
define("DEF_MONETARYCLASS_SHANAI", 2); // ����

// ���������
define("DEF_SALESCLASS_HONNI", 1); // �ܲ�
define("DEF_SALESCLASS_TEST", 2); // �ƥ��ȥ�

/**
 * ɸ��������ؿ�
 *
 *    ɸ����ǡ���������ؿ�
 *
 *    @param  String  $strProductCode     ���ʥ�����
 *    @param  Object  $objDB             DB���֥�������
 *    @return Integer $curStandardRate ɸ����
 *    @access public
 */
function fncGetEstimateDefault($objDB)
{
    list($lngResultID, $lngResultNum) = fncQuery("SELECT To_char( curstandardrate, '990.9999' ) as curstandardrate FROM m_EstimateStandardRate WHERE dtmApplyStartDate < NOW() AND dtmApplyEndDate > NOW()", $objDB);

    if ($lngResultNum < 1) {
// 2004.10.01 suzukaze update start
        // �⤷�����ɸ���礬���ȤǤ��ʤ����ǿ������դ�ɸ����򻲾�
        list($lngResultMaxID, $lngResultMaxNum) = fncQuery("select To_char( curstandardrate, '990.9999' ) as curstandardrate from m_estimatestandardrate where dtmapplyenddate = (select max(dtmapplyenddate) from m_estimatestandardrate);", $objDB);

        if ($lngResultMaxNum < 1) {
            fncOutputError(1502, DEF_WARNING, "", true, "", $objDB);
        } else {
            $lngResultNum = $lngResultMaxNum;
            $lngResultID = $lngResultMaxID;
        }
// 2004.10.01 suzukaze update end
    }

    $objResult = $objDB->fetchObject($lngResultID, 0);
    $objDB->freeResult($lngResultID);

    $curStandardRate = $objResult->curstandardrate;

// ɸ������ͤˤĤ��Ƥ� ��ɽ���ˤư���
    $curStandardRate = $curStandardRate * 100;

    unset($objResult);

    return $curStandardRate;
}

/**
 * ���Ѹ����������μ����̲߼����ؿ�
 *
 *    �����̲ߥǡ���������ؿ�
 *
 *    @param  String  $dtmInsertDate     ���Ѹ�����Ͽ��
 *    @param  Object  $objDB             DB���֥�������
 *    @return Integer $curStandardRate ɸ����
 *    @access public
 */
function fncGetUSConversionRate($dtmInsertDate, $objDB)
{
    if ($dtmInsertDate == "") {
        return 0;
    }

    $aryQuery[] = "SELECT mmr.lngMonetaryRateCode, mmr.curConversionRate, MAX(mmr.dtmapplystartdate) ";
    $aryQuery[] = "FROM m_MonetaryRate mmr ";
    $aryQuery[] = "JOIN m_monetaryunit mmu on mmr.lngmonetaryunitcode = mmu.lngmonetaryunitcode ";
    $aryQuery[] = "WHERE mmr.lngmonetaryratecode = '" . DEF_MONETARYCLASS_SHANAI . "' ";
    $aryQuery[] = "	AND mmu.lngmonetaryunitcode = '" . DEF_MONETARY_USD . "' ";
    $aryQuery[] = "	AND mmr.dtmapplystartdate = (SELECT MAX(mmr2.dtmapplystartdate) FROM m_MonetaryRate mmr2 WHERE mmr2.lngMonetaryRateCode = mmr.lngMonetaryRateCode AND mmr2.lngMonetaryUnitCode = mmr.lngMonetaryUnitCode) ";
    $aryQuery[] = "GROUP BY mmr.lngMonetaryRateCode, mmr.curConversionRate ";
    $aryQuery[] = "UNION ";
    $aryQuery[] = "SELECT mmr.lngMonetaryRateCode, mmr.curConversionRate, MAX(mmr.dtmapplystartdate) ";
    $aryQuery[] = "FROM m_MonetaryRate mmr ";
    $aryQuery[] = "JOIN m_monetaryunit mmu on mmr.lngmonetaryunitcode = mmu.lngmonetaryunitcode ";
    $aryQuery[] = "WHERE mmr.dtmapplystartdate <= '" . $dtmInsertDate . "' ";
    $aryQuery[] = "	AND mmr.dtmapplyenddate >= '" . $dtmInsertDate . "' ";
    $aryQuery[] = "	AND mmr.lngmonetaryratecode = '" . DEF_MONETARYCLASS_SHANAI . "' ";
    $aryQuery[] = "	AND mmu.lngmonetaryunitcode = '" . DEF_MONETARY_USD . "' ";
    $aryQuery[] = "GROUP BY mmr.lngMonetaryRateCode, mmr.curConversionRate ";
    $aryQuery[] = "ORDER BY 3 ";

    list($lngResultID, $lngResultNum) = fncQuery(join(" ", $aryQuery), $objDB);
    unset($aryQuery);

    if ($lngResultNum < 1) {
        fncOutputError(9061, DEF_WARNING, "", true, "", $objDB);
    }

    $objResult = $objDB->fetchObject($lngResultID, 0);
    $objDB->freeResult($lngResultID);

    $curConversionRate = $objResult->curconversionrate;

    unset($objResult);

    return $curConversionRate;
}

/**
 * ���Ѹ����׻������ؿ�
 *
 *    lngEstimateNo ���鸫�Ѹ����׻��Ƽ�ɽ���˻��Ѥ���ǡ������������ؿ�
 *
 *    @param  String $lngEstimateNo    ���Ѹ����ʥ�С�
 *    @param  Object $objDB            DB���֥�������
 *    @return Array  $aryData            ���Ѹ����ǡ���
 *    @access public
 */
function fncGetEstimate($lngEstimateNo, $objDB)
{
    //////////////////////////////////////////////////////////
    // ���Ѹ����ǡ�������
    //////////////////////////////////////////////////////////
    $aryQuery[] = "SELECT p.strProductCode, p.strProductName,";
    $aryQuery[] = " to_char(p.dtmDeliveryLimitDate,'YYYY/MM') AS dtmDeliveryLimitDate,";
    $aryQuery[] = " g.strGroupDisplayCode AS strInChargeGroupDisplayCode,";
    $aryQuery[] = " u.strUserDisplayCode AS strInChargeUserDisplayCode,";
    $aryQuery[] = " u.strUserDisplayName AS strInChargeUserDisplayName,";
    $aryQuery[] = " p.curRetailPrice, p.lngCartonQuantity,";

    // ����ñ�̤�ctn�ʤ�С�����ͽ�����pcs���Ѵ�����
    $aryQuery[] = " CASE WHEN p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_CTN;
    $aryQuery[] = "  THEN p.lngProductionQuantity * p.lngCartonQuantity ";
    $aryQuery[] = "  ELSE p.lngProductionQuantity ";
    $aryQuery[] = " END AS lngProductionQuantity, ";

    // ����ñ�̤�ctn�ʤ�С��ײ�C/t�Ϥ��Τޤ�����ͽ���
    $aryQuery[] = " CASE WHEN ( p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_PCS . " OR p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_SET . " ) AND p.lngCartonQuantity <> 0 ";
    $aryQuery[] = "  THEN p.lngProductionQuantity / p.lngCartonQuantity ";
    $aryQuery[] = "  ELSE p.lngProductionQuantity ";
    $aryQuery[] = " END AS lngPlanCartonProduction,";

    $aryQuery[] = " p.lngProductionUnitCode,";
    $aryQuery[] = " p.curProductPrice, ";
    $aryQuery[] = " e.lngRevisionNo, ";
    $aryQuery[] = " e.lngEstimateStatusCode, ";
    $aryQuery[] = " e.curFixedCost, ";
    $aryQuery[] = " e.curMemberCost, ";
    $aryQuery[] = " e.curManufacturingCost, ";
    $aryQuery[] = " to_char(e.dtmInsertDate,'YYYY/MM/DD') AS dtmInsertDate ";
    $aryQuery[] = " ,e.strNote ";
    $aryQuery[] = " ,e.lngprintcount ";

    $aryQuery[] = "FROM m_Estimate e ";
    $aryQuery[] = " INNER JOIN m_Product p ON ( e.strProductCode = p.strProductCode AND p.bytInvalidFlag = FALSE ) ";
    $aryQuery[] = " LEFT OUTER JOIN m_Group g ON ( p.lngInChargeGroupCode = g.lngGroupCode ) ";
    $aryQuery[] = " LEFT OUTER JOIN m_User u ON ( p.lngInChargeUserCode = u.lngUserCode ) ";
    $aryQuery[] = "WHERE e.lngEstimateNo = " . $lngEstimateNo;
    $aryQuery[] = " AND e.lngRevisionNo = (SELECT MAX(e2.lngRevisionNo) FROM m_Estimate e2 WHERE e.lngEstimateNo = e2.lngEstimateNo)";

    echo join(" ", $aryQuery);
    list($lngResultID, $lngResultNum) = fncQuery(join(" ", $aryQuery), $objDB);
    unset($aryQuery);

    if ($lngResultNum < 1) {
        fncOutputError(1502, DEF_WARNING, "", true, "", $objDB);
    }

    $objResult = $objDB->fetchObject($lngResultID, 0);
    $objDB->freeResult($lngResultID);
    unset($lngResultID);
    unset($lngResultNum);

    $aryData["strProductCode"] = $objResult->strproductcode;
    $aryData["strProductName"] = $objResult->strproductname;
    $aryData["dtmDeliveryLimitDate"] = $objResult->dtmdeliverylimitdate;
    $aryData["strInChargeGroupDisplayCode"] = $objResult->strinchargegroupdisplaycode;
    $aryData["strInChargeUserDisplayCode"] = $objResult->strinchargeuserdisplaycode;
    $aryData["strInChargeUserDisplayName"] = $objResult->strinchargeuserdisplayname;
    $aryData["curRetailPrice"] = $objResult->curretailprice;
    $aryData["lngCartonQuantity"] = $objResult->lngcartonquantity;
    $aryData["lngPlanCartonProduction"] = $objResult->lngplancartonproduction;
    $aryData["lngProductionQuantity"] = $objResult->lngproductionquantity;
    $aryData["lngProductionUnitCode"] = $objResult->lngproductionunitcode;

    $aryData["curProductPrice"] = $objResult->curproductprice;

    $aryData["lngRevisionNo"] = $objResult->lngrevisionno;
    $aryData["lngEstimateStatusCode"] = $objResult->lngestimatestatuscode;
    $aryData["curFixedCost"] = $objResult->curfixedcost;

    $aryData["curMemberCost"] = $objResult->curmembercost;

    $aryData["curManufacturingCost"] = $objResult->curmanufacturingcost;
    $aryData["dtmInsertDate"] = $objResult->dtminsertdate;

    $aryData["strInChargeUserName"] = $objResult->strinchargeusername;

    $aryData["strRemark"] = $objResult->strnote; // ������

    unset($objResult);

    //////////////////////////////////////////////////////////
    // ����ǡ�������
    //////////////////////////////////////////////////////////
    $aryQuery[] = "SELECT distinct r.strSalesCode, rd.lngSalesDetailNo, rd.lngProductQuantity AS lngProductQuantity, rd.lngProductUnitCode AS lngProductUnitCode, ";
    $aryQuery[] = "rd.curProductPrice AS curProductPrice, p.lngCartonQuantity AS lngCartonQuantity ";
    $aryQuery[] = "FROM m_Sales  r ";
    $aryQuery[] = "LEFT JOIN t_SalesDetail rd ON ( r.lngSalesNo = rd.lngSalesNo ) ";
    $aryQuery[] = "LEFT JOIN m_Product p ON ( rd.strProductCode = p.strProductCode AND p.bytInvalidFlag = FALSE ), ";
    $aryQuery[] = "m_Estimate e ";
    $aryQuery[] = "WHERE e.lngEstimateNo = " . $lngEstimateNo . " AND e.strProductCode = rd.strProductCode ";
    $aryQuery[] = " AND ( rd.lngSalesClassCode = " . DEF_SALESCLASS_HONNI . " OR rd.lngSalesClassCode = " . DEF_SALESCLASS_TEST . " )";
    $aryQuery[] = " AND r.lngRevisionNo = (SELECT MAX(r2.lngRevisionNo) FROM m_Sales r2 WHERE r.strSalesCode = r2.strSalesCode )";
    $aryQuery[] = " AND 0 <= ( SELECT MIN( r3.lngRevisionNo ) FROM m_Sales r3 WHERE r3.bytInvalidFlag = false AND r3.strSalesCode = r.strSalesCode )";

    list($lngResultID, $lngResultNum) = fncQuery(join(" ", $aryQuery), $objDB);
    unset($aryQuery);

    if ($lngResultNum < 1) {
        $aryData["lngReceiveCartonProduction"] = 0;
        // ����Ŀ��������פ��ͤ򥻥å�
        $aryData["lngReceiveProductQuantity"] = 0;
        $aryData["curReceiveSubTotalPrice"] = 0;
    } else {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryReceiveResult[] = $objDB->fetchArray($lngResultID, $i);
        }
        $objDB->freeResult($lngResultID);
        unset($lngResultID);
        unset($lngResultNum);

        $lngReceiveProductQuantity = 0;
        $curReceiveSubTotalPrice = 0;

        for ($i = 0; $i < count($aryReceiveResult); $i++) {
            // ������֤������ȥ�ξ��
            if ($aryReceiveResult[$i]["lngproductunitcode"] == DEF_PRODUCTUNIT_CTN) {
                // ����� �� ���� �� �����ȥ�����
                $lngReceiveProductQuantity += $aryReceiveResult[$i]["lngproductquantity"] * $aryReceiveResult[$i]["lngcartonquantity"];
                // ������� �� ���� �� �����ȥ������ʼ�����ʤϺǽ����͡�
                if ($aryReceiveResult[$i]["lngcartonquantity"] != 0) {
                    $curReceiveProductPrice = $aryReceiveResult[$i]["curproductprice"] / $aryReceiveResult[$i]["lngcartonquantity"];
                } else {
                    $curReceiveProductPrice = 0;
                }
            }
            // ������֤��ԡ����ξ��
            else {
                // ����� �� ����
                $lngReceiveProductQuantity += $aryReceiveResult[$i]["lngproductquantity"];
                // ������� �� ���ʡʼ�����ʤϺǽ����͡�
                $curReceiveProductPrice = $aryReceiveResult[$i]["curproductprice"];
            }
            // ������ �� ���� �� ����
            $curReceiveSubTotalPrice += $aryReceiveResult[$i]["lngproductquantity"] * $aryReceiveResult[$i]["curproductprice"];
        }
        // ����C/t
        if ($aryData["lngCartonQuantity"] != 0 and $aryData["lngCartonQuantity"] != "") {
            $aryData["lngReceiveCartonProduction"] = $lngReceiveProductQuantity / $aryData["lngCartonQuantity"];
        }

        // ����Ŀ��������פ��ͤ򥻥å�
        $aryData["lngReceiveProductQuantity"] = $lngReceiveProductQuantity;
        $aryData["curReceiveProductPrice"] = $curReceiveProductPrice;
        $aryData["curReceiveSubTotalPrice"] = $curReceiveSubTotalPrice;

        unset($objResult);
    }

    return $aryData;
}

/**
 * �ǥե���ȸ��Ѹ����б��ͼ����ؿ�
 *
 *    �ǥե���ȸ��Ѹ������б������ͤμ����ǡ���������ؿ�
 *
 *    @param  Integer $lngEstimateNo            ���Ѹ����ֹ�
 *    @param  Integer $lngReceiveQuantity        �����
 *    @param  Integer $lngProductionQuantity    ����ͽ���
 *    @param  Array      $curProductPrice        ͽ��Ǽ��
 *    @param  Array      $aryRate                 �̲ߥ졼�ȥ����ɤ򥭡��Ȥ����̲ߥ졼��
 *    @param  Object  $objDB                     DB���֥�������
 *    @return Array     $aryDefaultValue         �ǥե������
 *    @return Array     $curReceiveProductPrice         ����Ǽ��
 *
 *    2005/06/10     ABE Yuuki
 *    �����˼���Ǽ�����ɲä�������ۤ����Ǽ���򸵤˻��Ф���褦�˽���
 *
 *    @access public
 */
function fncGetEstimateDefaultValue($lngEstimateNo, $lngReceiveQuantity, $lngProductionQuantity, $curProductPrice, $aryRate, $objDB, $curReceiveProductPrice)
{
    $aryQuery[] = "SELECT distinct e.lngStockSubjectCode AS lngStockSubjectCode,";
    $aryQuery[] = " e.lngStockItemCode AS lngStockItemCode, ";
    $aryQuery[] = " e.bytPayOffTargetFlag AS bytPayOffTargetFlag, ";
    $aryQuery[] = " e.bytPercentInputFlag AS bytPercentInputFlag, ";
    $aryQuery[] = " e.lngMonetaryUnitCode AS lngMonetaryUnitCode, ";
    $aryQuery[] = " e.lngMonetaryRateCode AS lngMonetaryRateCode, ";
    $aryQuery[] = " e.curConversionRate AS curConversionRate, ";
    $aryQuery[] = " e.lngProductQuantity AS lngProductQuantity, ";
    $aryQuery[] = " e.curProductPrice AS curProductPrice, ";
    $aryQuery[] = " e.curProductRate AS curProductRate, ";
    $aryQuery[] = " e.curSubTotalPrice AS curSubTotalPrice, ";
    $aryQuery[] = " e.strNote AS strNote ";

    $aryQuery[] = "FROM t_EstimateDetail e";
    $aryQuery[] = " LEFT JOIN m_Company c ON ( e.lngCustomerCompanyCode = c.lngCompanyCode )";
    $aryQuery[] = " INNER JOIN m_StockSubject ss ON ( e.lngStockSubjectCode = ss.lngStockSubjectCode )";
    $aryQuery[] = " INNER JOIN m_StockItem si ON ( e.lngStockItemCode = si.lngStockItemCode AND e.lngStockSubjectCode = si.lngStockSubjectCode)";
    $aryQuery[] = " LEFT JOIN m_Estimate es ON ( e.lngEstimateNo = es.lngEstimateNo )";
    $aryQuery[] = " LEFT JOIN m_EstimateDefault ed ON ( e.lngStockSubjectCode = ed.lngStockSubjectCode AND e.lngStockItemCode = ed.lngStockItemCode )";
    $aryQuery[] = "WHERE e.lngEstimateNo = " . $lngEstimateNo;
    $aryQuery[] = " AND e.lngRevisionNo = (SELECT MAX(e2.lngRevisionNo) FROM t_EstimateDetail e2 WHERE e.lngEstimateNo = e2.lngEstimateNo AND e.lngEstimateDetailNo = e2.lngEstimateDetailNo)";
    $aryQuery[] = " AND ed.dtmApplyStartDate < es.dtmInsertDate AND ed.dtmApplyEndDate > es.dtmInsertDate ";

    list($lngResultID, $lngResultNum) = fncQuery(join(" ", $aryQuery), $objDB);
    unset($aryQuery);

    if ($lngResultNum < 1) {
        fncOutputError(1502, DEF_WARNING, "", true, "", $objDB);
    }

    // �������ܤ�����ο��ͥ������б������뤿�����������
    $aryStockKey = array("431" => 0, "433" => 1, "403" => 2, "402" => 3, "401" => 4, "420" => 5, "1224" => 6, "1230" => 7);

    // ����������Υ����󥿡����������
    $aryCount = array("431" => 0, "433" => 0, "403" => 0, "402" => 0, "401" => 0, "420" => 0, "1224" => 0, "1230" => 0);

    // Boolean���б������뤿�����������
    $aryBooleanString = array("t" => "true", "f" => "false", "true" => "true", "false" => "false", "" => "false");

    $aryMonetaryUnit = array(DEF_MONETARY_YEN => "\\", DEF_MONETARY_USD => "$", DEF_MONETARY_HKD => "HKD");

    // ���Ѹ����ơ��֥�ǡ�������
    // ���٤ο������롼��
    for ($i = 0; $i < $lngResultNum; $i++) {
        $objResult = $objDB->fetchObject($lngResultID, $i);

        // $aryDetail[�����������ֹ�][�����襫���󥿡�][���٥����̾]
        $aryDefaultValue[$i]["lngStockSubjectCode"]
        = $objResult->lngstocksubjectcode;

        $aryDefaultValue[$i]["lngStockItemCode"]
        = $objResult->lngstockitemcode;

        $aryDefaultValue[$i]["bytPayOffTargetFlag"]
        = $aryBooleanString[$objResult->bytpayofftargetflag];

        $aryDefaultValue[$i]["lngCustomerCompanyCode"]
        = $objResult->strcompanydisplaycode;

        $aryDefaultValue[$i]["bytPercentInputFlag"]
        = $aryBooleanString[$objResult->bytpercentinputflag];

        // �⤷���ѡ���������ϥե饰�����ꤵ��Ƥ���аʲ����ͤ����������ꤹ��
        if ($aryBooleanString[$objResult->bytpercentinputflag] == "true") {
            $aryDefaultValue[$i]["lngProductQuantity"] = $lngReceiveQuantity;

            $aryDefaultValue[$i]["curProductRate"] = $objResult->curproductrate;

            //2005/06/10 ABE Yuuki ���̡߼���Ǽ����ñ�����������
            $aryDefaultValue[$i]["curSubTotalPrice"] = $lngReceiveQuantity * $curReceiveProductPrice * $objResult->curproductrate;
            //$aryDefaultValue[$i]["curSubTotalPrice"]   = $lngReceiveQuantity * $curProductPrice * $objResult->curproductrate;
        } else {
            // �Ŀ��������ꤵ��Ƥ��Ƥ��θĿ�������ͽ����ξ��ϡ�����������ꤹ��
            if ($objResult->lngproductquantity == $lngProductionQuantity) {
                $aryDefaultValue[$i]["lngProductQuantity"] = $lngReceiveQuantity;
            } else {
                $aryDefaultValue[$i]["lngProductQuantity"] = $objResult->lngproductquantity;
            }

            $aryDefaultValue[$i]["curProductRate"] = $objResult->curproductrate;

            $aryDefaultValue[$i]["curSubTotalPrice"] = $objResult->cursubtotalprice;
        }

        $aryDefaultValue[$i]["curProductPrice"]
        = $objResult->curproductprice;

        $aryDefaultValue[$i]["strNote"]
        = $objResult->strnote;

        $aryDefaultValue[$i]["lngMonetaryUnitCode"]
        = $aryMonetaryUnit[$objResult->lngmonetaryunitcode];

        if (is_array($aryRate)) {
            $aryDefaultValue[$i]["curSubTotalPriceJP"]
            = $aryDefaultValue[$i]["curSubTotalPrice"]
                 * $aryRate[$objResult->lngmonetaryunitcode];
        } else {
            $aryDefaultValue[$i]["curSubTotalPriceJP"]
            = $aryDefaultValue[$i]["curSubTotalPrice"]
             * $objResult->curconversionrate;
        }

        $aryDefaultValue[$i]["curConversionRate"]
        = $objResult->curconversionrate;

        $aryDefaultValue[$i]["strCompanyDisplayName"]
        = $objResult->strcompanydisplayname;

        // �ǥե�����ͤˤϥե饰�����ꤹ��
        $aryDefaultValue[$i]["bytDefaultFlag"] = "true";

        $aryCount[$objResult->lngstocksubjectcode]++;
        unset($objResult);
    }

    unset($lngResultID);
    unset($lngResultNum);
    unset($aryCount);

    return $aryDefaultValue;
}

/**
 * ���Ѹ����׻����ټ����ؿ�
 *
 *    lngEstimateNo ���鸫�Ѹ����׻��Ƽ�ɽ���˻��Ѥ������٥ǡ������������ؿ�
 *
 *    @param  String $lngEstimateNo    ���Ѹ����ʥ�С�
 *    @param  String $strProductCode    �����оݤ����ʥ�����
 *    @param  Array  $aryRate            �̲ߥ졼�ȥ����ɤ򥭡��Ȥ����̲ߥ졼��
 *    @param    Array  $aryDefaultValue ���Ѹ����Υǥե�����ͤ��Ф������Ϥ��줿�ǡ���
 *    @param  Object $objDB            DB���֥�������
 *    @return Array  $aryDetail        ���Ѹ������٥ǡ���
 *            Array  $aryOrderDetail    ȯ�����٥ǡ���
 *    @access public
 */
function fncGetEstimateDetail($lngEstimateNo, $strProductCode, $aryRate, $aryDefaultValue, $objDB)
{
    $aryDetail = array();
    //////////////////////////////////////////////////////////
    // ���Ѿܺ٥ǡ�������
    //////////////////////////////////////////////////////////
    $aryQuery[] = "SELECT *";
    $aryQuery[] = "FROM t_EstimateDetail e";
    $aryQuery[] = " LEFT JOIN m_Company c ON ( e.lngCustomerCompanyCode = c.lngCompanyCode )";
    $aryQuery[] = " INNER JOIN m_StockSubject ss ON ( e.lngStockSubjectCode = ss.lngStockSubjectCode )";
    $aryQuery[] = " INNER JOIN m_StockItem si ON ( e.lngStockItemCode = si.lngStockItemCode AND e.lngStockSubjectCode = si.lngStockSubjectCode)";
    $aryQuery[] = "WHERE e.lngEstimateNo = " . $lngEstimateNo;
    $aryQuery[] = " AND e.lngRevisionNo = (SELECT MAX(e2.lngRevisionNo) FROM t_EstimateDetail e2 WHERE e.lngEstimateNo = e2.lngEstimateNo)";
    $aryQuery[] = " ORDER BY e.lngStockSubjectCode, e.lngEstimateDetailNo ";

    list($lngResultID, $lngResultNum) = fncQuery(join(" ", $aryQuery), $objDB);
    unset($aryQuery);

    if ($lngResultNum < 1) {
        fncOutputError(1502, DEF_WARNING, "", true, "", $objDB);
    }

    // �������ܤ�����ο��ͥ������б������뤿�����������
    $aryStockKey = array("431" => 0, "433" => 1, "403" => 2, "402" => 3, "401" => 4, "420" => 5, "1224" => 6, "1230" => 7);

    // ����������Υ����󥿡����������
    $aryCount = array("431" => 0, "433" => 0, "403" => 0, "402" => 0, "401" => 0, "420" => 0, "1224" => 0, "1230" => 0);

    // Boolean���б������뤿�����������
    $aryBooleanString = array("t" => "true", "f" => "false", "true" => "true", "false" => "false", "" => "false");

    $aryMonetaryUnit = array(DEF_MONETARY_YEN => "\\", DEF_MONETARY_USD => "$", DEF_MONETARY_HKD => "HKD");

    // ���Ѹ����ơ��֥�ǡ�������
    // ���٤ο������롼��
    for ($i = 0; $i < $lngResultNum; $i++) {
        $objResult = $objDB->fetchObject($lngResultID, $i);

        // $aryDetail[�����������ֹ�][�����襫���󥿡�][���٥����̾]
        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngStockSubjectCode"]
        = $objResult->lngstocksubjectcode;

        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strStockSubjectName"]
        = $objResult->strstocksubjectname;

        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngStockItemCode"]
        = $objResult->lngstockitemcode;

        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strStockItemName"]
        = $objResult->strstockitemname;

        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["bytPayOffTargetFlag"]
        = $aryBooleanString[$objResult->bytpayofftargetflag];

        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngCustomerCompanyCode"]
        = $objResult->strcompanydisplaycode;

        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strCompanyDisplayCode"]
        = $objResult->strcompanydisplaycode;

        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strCompanyDisplayName"]
        = $objResult->strcompanydisplayname;

        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["bytPercentInputFlag"]
        = $aryBooleanString[$objResult->bytpercentinputflag];

        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngProductQuantity"]
        = $objResult->lngproductquantity;

        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curProductRate"]
        = $objResult->curproductrate;

        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curProductPrice"]
        = $objResult->curproductprice;

        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPrice"]
        = $objResult->cursubtotalprice;

        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strNote"]
        = $objResult->strnote;

        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngMonetaryUnitCode"]
        = $aryMonetaryUnit[$objResult->lngmonetaryunitcode];

        if (is_array($aryRate)) {
            $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPriceJP"]
            = $objResult->cursubtotalprice * $aryRate[$objResult->lngmonetaryunitcode];
        } else {
            $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPriceJP"]
            = $objResult->cursubtotalprice * $objResult->curconversionrate;
        }

        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curConversionRate"]
        = $objResult->curconversionrate;

        $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strCompanyDisplayName"]
        = $objResult->strcompanydisplayname;

        $aryCount[$objResult->lngstocksubjectcode]++;
        unset($objResult);
    }

    unset($lngResultID);
    unset($lngResultNum);
    unset($aryCount);
    //ȯ����������ѹ�
    //////////////////////////////////////////////////////////
    // ȯ�����٥ǡ�������
    //////////////////////////////////////////////////////////
    $aryQuery[] = "SELECT od.lngStockSubjectCode, ";

    // ����ñ�̤�ctn�ʤ�С����̤�pcs���Ѵ�����
    $aryQuery[] = " CASE WHEN od.lngProductUnitCode = " . DEF_PRODUCTUNIT_CTN;
    $aryQuery[] = "  THEN od.lngProductQuantity * p.lngCartonQuantity ";
    $aryQuery[] = "  ELSE od.lngProductQuantity ";
    $aryQuery[] = " END AS lngOrderProductQuantity, ";

    // �̲ߤΰ㤤�ˤ����ʤ��ִԤ���
    $aryQuery[] = " od.curSubTotalPrice * o.curConversionRate AS curOrderSubTotalPrice ";
    $aryQuery[] = "FROM t_StockDetail od";
    $aryQuery[] = " LEFT JOIN m_Stock o ON ( od.lngStockNo = o.lngStockNo ) ";
    $aryQuery[] = " LEFT JOIN m_Product p ON ( od.strProductCode = p.strProductCode )";
    $aryQuery[] = "WHERE od.strProductCode = '" . $strProductCode . "'";

    // ��ӥ����ʥ�С������� ���� ��Х��������ɤ����� ���� ��ӥ����ʥ�С��Ǿ��ͤ�0�ʾ�
    $aryQuery[] = " AND o.lngRevisionNo = ( ";
    $aryQuery[] = "SELECT MAX( o1.lngRevisionNo ) FROM m_Stock o1 WHERE o1.strStockCode = o.strStockCode )";
    $aryQuery[] = " AND 0 <= ( ";
    $aryQuery[] = "SELECT MIN( o3.lngRevisionNo ) FROM m_Stock o3 WHERE o3.bytInvalidFlag = false AND o3.strStockCode = o.strStockCode )";

    list($lngResultID, $lngResultNum) = fncQuery(join(" ", $aryQuery), $objDB);
    unset($aryQuery);

    // �������ܥ����ɤ򥭡��Ȥ������̾������������
    $aryStockSubjectCode = fncGetMasterValue("m_StockSubject", "lngStockSubjectCode", "strStockSubjectName", "Array", "", $objDB);

    // ȯ�����٥ơ��֥�ǡ�������
    // ���٤ο������롼��
    for ($i = 0; $i < $lngResultNum; $i++) {
        $objResult = $objDB->fetchObject($lngResultID, $i);

        // $aryDetail[�����������ֹ�][���٥����̾]
        $aryOrderDetail[$aryStockKey[$objResult->lngstocksubjectcode]]["strStockSubjectName"] = $aryStockSubjectCode[$objResult->lngstocksubjectcode];

        $aryOrderDetail[$aryStockKey[$objResult->lngstocksubjectcode]]["lngOrderQuantity"] += $objResult->lngorderproductquantity;

        $aryOrderDetail[$aryStockKey[$objResult->lngstocksubjectcode]]["curOrderSubTotalPrice"] += $objResult->curordersubtotalprice;

        unset($objResult);
    }

    if ($lngResultNum > 0) {
        $objDB->freeResult($lngResultID);
    }

    // ���Ѹ����Υǥե�����ͤ��Ф������ϥǡ������ȯ��ǡ����ؤ��������
    // ���٤ο������롼��
    for ($i = 0; $i < count($aryDefaultValue); $i++) {
        // $aryDetail[�����������ֹ�][���٥����̾]
        $aryOrderDetail[$aryStockKey[$aryDefaultValue[$i]["lngStockSubjectCode"]]]["lngOrderQuantity"] += $aryDefaultValue[$i]["lngProductQuantity"];

        $aryOrderDetail[$aryStockKey[$aryDefaultValue[$i]["lngStockSubjectCode"]]]["curOrderSubTotalPrice"] += $aryDefaultValue[$i]["curSubTotalPriceJP"];
    }

    unset($lngResultID);
    unset($lngResultNum);
    unset($aryCount);

    return array($aryDetail, $aryOrderDetail);
}

/**
 * ���Ѹ����׻�����HTML����ʸ��������ؿ�
 *
 *    ���Ѹ����׻����٥ǡ��������٥ƥ�ץ졼�ȤˤϤ�����ʸ������������ؿ�
 *
 *    @param  String $strProductCode    ���ʥ�����
 *    @param  String $aryDetail        ���Ѹ����׻����٥ǡ���
 *    @param  String $aryOrderDetail    ȯ�����٥ǡ���
 *    @param  String $aryOrderDefault    ���Ѹ����ǥե���ȥǡ���
 *    @param  String $strDetailTemplatePath        ���Ѹ������٥ƥ�ץ졼��
 *    @param  String $strOrderDetailTemplatePath    ȯ�����٥ƥ�ץ졼��
 *    @return Array  $aryDetail        ���Ѹ������٥ǡ���
 *    @access public
 */
function fncGetEstimateDetailHtml($aryDetail, $aryOrderDetail, $aryOrderDefault, $strDetailTemplatePath, $strOrderDetailTemplatePath, $objDB)
{
    // ���Ѹ������٥ƥ�ץ졼�ȼ���
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate($strDetailTemplatePath);
    $strTemplate = $objTemplate->strTemplate;

    // ���Ѹ������ٻ������ܷ׹ԥƥ�ץ졼�ȼ���
    $objOrderTemplate = new clsTemplate();
    $objOrderTemplate->getTemplate($strOrderDetailTemplatePath);
    $strOrderTemplate = $objOrderTemplate->strTemplate;

    // �������ܥ����ɤ򥭡��Ȥ������̾������������
    $aryStockSubjectCode = fncGetMasterValue("m_StockSubject", "lngStockSubjectCode", "strStockSubjectName", "Array", "", $objDB);

    // �������ܤ�����ο��ͥ������б������뤿�����������
    $aryStockKey = array(0 => "431", 1 => "433", 2 => "403", 3 => "402", 4 => "401", 5 => "420", 6 => "1224", 7 => "1230");

    // ���ɽ�������ɤ򥭡��Ȥ�����̾Ϣ����������
    $aryCompanyName = fncGetMasterValue("m_Company", "strCompanyDisplayCode", "strCompanyDisplayName", "Array", "", $objDB);

    // ���������оݤ��б������뤿�����������
    $aryPayOffFlag = array("t" => "��", "f" => "", "true" => "��", "false" => "", "" => "");

    // ����������������
    $curFixedCost = 0;
    $curMemberCost = 0;

    // �����񾮷� added by k.saito
    $curFixedCostSubtotal = 0;

    // ��������
    $curCheckCost = 0;

    //////////////////////////////////////////////////////////////////
    // ���٥ǡ���
    //////////////////////////////////////////////////////////////////
    // $aryDitail[��������][���ٹ�][����]
    for ($i = 0; $i < 8; $i++) {
        $lngStockSubjectCode = 0;
        $strStockSubjectName = 0;
        $curSubjectTotalCost = 0;

        $aryOrderCost[$i] = 0;
        $aryOrderProductQuantity[$i] = 0;

        if (!is_array($aryDetail[$i])) {
            break;
        }
        for ($j = 0; $j < count($aryDetail[$i]); $j++) {
            $lngStockItemCode = 0;
            // HIDDEN
            $aryKeys = array_keys($aryDetail[$i][$j]);
            foreach ($aryKeys as $strKey) {
                if ($strKey == "lngStockSubjectCode") {
                    $aryDetail[$i][$j]["strStockSubjectName"] = $aryStockSubjectCode[$aryDetail[$i][$j][$strKey]];
                    $lngStockSubjectCode = $aryDetail[$i][$j][$strKey];
                    $strStockSubjectName = $aryDetail[$i][$j]["strStockSubjectName"];
                    $arySubDetail[$i]["strStockSubjectName"] = $aryStockSubjectCode[$aryDetail[$i][$j][$strKey]];
                }
                if ($strKey == "lngStockItemCode") {
                    $strStockItemName = fncGetMasterValue("m_stockitem", "lngstockitemcode", "strstockitemname", $aryDetail[$i][$j][$strKey], "lngstocksubjectcode = " . $lngStockSubjectCode, $objDB);
                    $lngStockItemCode = $aryDetail[$i][$j][$strKey];
                    $aryDetail[$i][$j]["strStockItemName"] = $strStockItemName;
                }
                if ($strKey == "lngCustomerCompanyCode") {
                    $aryDetail[$i][$j]["strCompanyDisplayCode"] = $aryDetail[$i][$j][$strKey];
                    $aryDetail[$i][$j]["strCompanyDisplayName"] = $aryCompanyName[$aryDetail[$i][$j][$strKey]];
                }
            }
            $aryDetail[$i][$j]["bytPayOffTargetFlag"] = $aryPayOffFlag[$aryDetail[$i][$j]["bytPayOffTargetFlag"]];

            if ($aryDetail[$i][$j]["bytPercentInputFlag"] == "t" or $aryDetail[$i][$j]["bytPercentInputFlag"] == "true") {
                $aryDetail[$i][$j]["strPlanPrice"] = number_format($aryDetail[$i][$j]["curProductRate"] * 100, 4, ".", ",") . " %";
            } else {
                $aryDetail[$i][$j]["strPlanPrice"] = $aryDetail[$i][$j]["lngMonetaryUnitCode"] . " " . number_format($aryDetail[$i][$j]["curProductPrice"], 4, ".", ",");
            }

            // �����Ȳû�
            if (!is_numeric($aryDetail[$i][$j]["curSubTotalPriceJP"])) {
                $aryDetail[$i][$j]["curSubTotalPriceJP"] = str_replace("\\", "", $aryDetail[$i][$j]["curSubTotalPriceJP"]);
                $aryDetail[$i][$j]["curSubTotalPriceJP"] = str_replace(" ", "", $aryDetail[$i][$j]["curSubTotalPriceJP"]);
                $aryDetail[$i][$j]["curSubTotalPriceJP"] = str_replace(",", "", $aryDetail[$i][$j]["curSubTotalPriceJP"]);
            }
            if ($aryDetail[$i][$j]["curSubTotalPriceJP"] != "") {
                $aryCost[$i] += $aryDetail[$i][$j]["curSubTotalPriceJP"];
                // �оݤ�������ʤ��
                if ($i < 3) {
// start modified by k.saito 2005.01.27
                    // �����оݤξ�硢�������פ˲û�����
                    if ($aryDetail[$i][$j]["bytPayOffTargetFlag"] == "��") {
                        $curFixedCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];
                    }

                    // �����оݳ���׼���
                    else {
                        $curNonFixedCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];
                    }

                    // �����оݴط��ʤ��������񾮷פ˲û�����
                    $curFixedCostSubtotal += $aryDetail[$i][$j]["curSubTotalPriceJP"];
                    // ������ξ��פ˲û�����
                    $curSubjectTotalCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];

//                    else
                    //                    {
                    //                        $curMemberCost       += $aryDetail[$i][$j]["curSubTotalPriceJP"];
                    //                    }
                }
                // �оݤ�������ʤ��
                else {
                    if ($aryDetail[$i][$j]["bytPayOffTargetFlag"] == "��") {
                        $curFixedCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];
                    } else {
                        $curMemberCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];
                        $curSubjectTotalCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];
                    }
                }
            }

            // ���������б�
            if ($lngStockSubjectCode == "403" and $lngStockItemCode == "6") {
                $curCheckCost = $aryDetail[$i][$j]["curSubTotalPriceJP"];
            }

            // �ײ�Ŀ��û�
            if (!is_numeric($aryDetail[$i][$j]["lngProductQuantity"])) {
                $aryDetail[$i][$j]["lngProductQuantity"] = str_replace("\\", "", $aryDetail[$i][$j]["lngProductQuantity"]);
                $aryDetail[$i][$j]["lngProductQuantity"] = str_replace(" ", "", $aryDetail[$i][$j]["lngProductQuantity"]);
                $aryDetail[$i][$j]["lngProductQuantity"] = str_replace(",", "", $aryDetail[$i][$j]["lngProductQuantity"]);
            }
            if ($aryDetail[$i][$j]["curSubTotalPriceJP"] != "") {
                $aryProductQuantity[$i] += $aryDetail[$i][$j]["lngProductQuantity"];
            }

            // ȯ����ؤΥǥե����������
            for ($k = 0; $k < count($aryOrderDefault); $k++) {
                if ($aryOrderDefault[$k]["lngStockSubjectCode"] == $lngStockSubjectCode
                    && $aryOrderDefault[$k]["lngStockItemCode"] == $lngStockItemCode
                    && $aryOrderDefault[$k]["bytDefaultFlag"] == "true") {
                    $aryDetail[$i][$j]["lngOrderProductQuantity"] = $aryOrderDefault[$k]["lngProductQuantity"];
                    $aryDetail[$i][$j]["curOrderSubTotalPriceJP"] = $aryOrderDefault[$k]["curSubTotalPriceJP"];
                    $bytDefaultFlag = 1;
                    break 1;
                }
            }

            // ����޽���
            $aryDetail[$i][$j] = fncGetCommaNumber($aryDetail[$i][$j]);

            // �֤�����
            $objTemplate->replace($aryDetail[$i][$j]);
            $objTemplate->complete();

            if ($aryDetail[$i][$j]["bytPercentInputFlag"] == "true") {
                $aryDetail[$i][$j]["curProductPrice"] = $aryDetail[$i][$j]["curConversionRate"];
            }

            // ����������˥ƥ�ץ졼���ݻ�
            $aryDetailTemplate[$i] .= $objTemplate->strTemplate;

            $objTemplate->strTemplate = $strTemplate;
        }

        // ���������б�
        // �������ܡ�1230�פǡ��������ѹ�פ���0�ߡפǤϤʤ����������ܡ�1230�׹�פ���0�ߡפϤʤ����
        // �����񸺻�����
        if ($lngStockSubjectCode == "1230" and $curCheckCost != 0 and $aryCost[$i] != 0) {
            $lngCount = count($aryDetail[$i]);
            $aryDetail[$i][$lngCount]["lngStockSubjectCode"] = $lngStockSubjectCode;
            $aryDetail[$i][$lngCount]["strStockSubjectName"] = $strStockSubjectName;
            $aryDetail[$i][$lngCount]["strStockItemName"] = "�����񸺻�";
            $aryDetail[$i][$lngCount]["curSubTotalPriceJP"] = 0 - $curCheckCost;
            $aryDetail[$i][$lngCount]["curOrderSubTotalPriceJP"] = 0 - $curCheckCost;

            $aryCost[$i] += $aryDetail[$i][$lngCount]["curSubTotalPriceJP"];
            $curMemberCost += $aryDetail[$i][$lngCount]["curSubTotalPriceJP"];
            $curSubjectTotalCost += $aryDetail[$i][$lngCount]["curSubTotalPriceJP"];

            // ����޽���
            $aryDetail[$i][$lngCount] = fncGetCommaNumber($aryDetail[$i][$lngCount]);

            // �֤�����
            $objTemplate->replace($aryDetail[$i][$lngCount]);
            $objTemplate->complete();

            // ����������˥ƥ�ץ졼���ݻ�
            $aryDetailTemplate[$i] .= $objTemplate->strTemplate;

            $objTemplate->strTemplate = $strTemplate;
        }

        ////////////////////////////////////////////////////////////
        // �������ܷ׹��ɲý���
        ////////////////////////////////////////////////////////////
        // �������ܷפλ���
        $arySubDetail[$i]["curSubjectTotalCost"] = number_format($curSubjectTotalCost, 2, '.', ',');

        // �����Ȳû�
        if ($aryOrderDetail[$i]["curOrderSubTotalPrice"] == "") {
            $aryOrderDetail[$i]["curOrderSubTotalPrice"] = 0.00;
        }
        // ���������б�
        if ($lngStockSubjectCode == "1230" and $curCheckCost != 0) {
            $aryOrderDetail[$i]["curOrderSubTotalPrice"] -= $curCheckCost;
        }
        $aryOrderCost[$i] = $aryOrderDetail[$i]["curOrderSubTotalPrice"];

        // �ײ�Ŀ��û�
        $aryOrderProductQuantity[$i] += $aryOrderDetail[$i]["lngOrderQuantity"];

        if ($aryOrderDetail[$i]["strStockSubjectName"] == "") {
            $aryOrderDetail[$i]["strStockSubjectName"] = $aryStockSubjectCode[$aryStockKey[$i]];
        }
        // ����޽���
        $aryOrderDetail[$i] = fncGetCommaNumber($aryOrderDetail[$i]);

        // �������ܷ��֤�����
        // �ƥ�ץ졼������
        $objOrderTemplate->replace($aryOrderDetail[$i]);
        $objOrderTemplate->replace($arySubDetail[$i]);
        $objOrderTemplate->complete();

        // ����������˥ƥ�ץ졼���ݻ�
        $aryDetailTemplate[$i] .= $objOrderTemplate->strTemplate;

        $objOrderTemplate->strTemplate = $strOrderTemplate;
    }

    unset($objTemplate);
    unset($objOrderTemplate);
    unset($strTemplate);
    unset($strOrderTemplate);
    unset($aryDetail);

    $aryEstimate["curFixedCost"] = 0;
    $aryEstimate["curMemberCost"] = 0;
    $aryEstimate["curOrderFixedCost"] = 0;
    $aryEstimate["curOrderMemberCost"] = 0;

    // ����������ʬ
    for ($i = 0; $i < 3; $i++) {
        // ����������ʬHTML
        $aryEstimateDetail["strFixCostTemplate"] .= $aryDetailTemplate[$i];

        // ���Ѹ���
        $aryEstimate["lngFixedQuantity"] += $aryProductQuantity[$i];

        // ȯ��
        $aryEstimate["curOrderFixedCost"] += $aryOrderCost[$i];
        $aryEstimate["lngOrderFixedQuantity"] += $aryOrderProductQuantity[$i];
    }
    // �����񾮷� added by k.saito
    $aryEstimate["curFixedCostSubtotal"] = $curFixedCostSubtotal;
    // �������פϷ׻���
    $aryEstimate["curFixedCost"] = $curFixedCost;

// �����оݳ����
    $aryEstimate["curNonFixedCost"] = (is_null($curNonFixedCost) || empty($curNonFixedCost)) ? 0.00 : $curNonFixedCost;

    // ����������ʬ
    for ($i = 3; $i < 8; $i++) {
        // ����������ʬHTML
        $aryEstimateDetail["strMemberCostTemplate"] .= $aryDetailTemplate[$i];

        // ���Ѹ���
        $aryEstimate["lngMemberQuantity"] += $aryProductQuantity[$i];

        // ȯ��
        $aryEstimate["curOrderMemberCost"] += $aryOrderCost[$i];
//        $aryEstimate["lngOrderMemberQuantity"]        += $aryOrderProductQuantity[$i];
    }
    // �������פϷ׻���
    $aryEstimate["curMemberCost"] = $curMemberCost;

    unset($aryDetailTemplate);
    unset($aryCost);

    return array($aryEstimateDetail, $aryEstimate, $aryHiddenString);
}

/**
 * ���Ѹ����׻��׻���̼����ؿ�
 *
 *    ����¤���я����������פθ��Ѹ����׻��׻���̥ǡ������������ؿ�
 *
 *    @param  Array  $aryEstimateData    ���Ѹ����׻��ǡ���
 *    @param  Object $objDB            DB���֥�������
 *    @return Array  $aryEstimateData    ���Ѹ����׻��ǡ���
 *    @access public
 */
function fncGetEstimateCalculate($aryEstimateData)
{
    ///////////////////////////////////////////////////////////////////////
    // ͽ��
    ///////////////////////////////////////////////////////////////////////
    // �������׷ײ�Ŀ�
    $aryEstimateData["lngFixedQuantityTotal"] = $aryEstimateData["lngProductionQuantity"];

    // ������ñ�� �� �������׷ײ踶�� / ����ͽ���
    if ($aryEstimateData["lngFixedQuantityTotal"] != 0) {
        $aryEstimateData["curFixedProductPrice"] = $aryEstimateData["curFixedCost"] / $aryEstimateData["lngProductionQuantity"];
    } else {
        $aryEstimateData["curFixedProductPrice"] = 0.00;
    }

    // �������׷ײ�Ŀ�
    $aryEstimateData["lngMemberQuantityTotal"] = $aryEstimateData["lngProductionQuantity"];

    // ������ñ�� �� �������׷ײ踶�� / ����ͽ���
    if ($aryEstimateData["lngMemberQuantityTotal"] != 0) {
        $aryEstimateData["curMemberProductPrice"] = $aryEstimateData["curMemberCost"] / $aryEstimateData["lngMemberQuantityTotal"];
    } else {
        $aryEstimateData["curMemberProductPrice"] = 0.00;
    }

// ����¤���� �� ������ �� ������ + �����оݳ����
    $aryEstimateData["curManufacturingCost"] = $aryEstimateData["curFixedCost"] + $aryEstimateData["curMemberCost"] + $aryEstimateData["curNonFixedCost"];

    // ����¤���ѷײ�Ŀ� �� ����ͽ�����pcs��
    $aryEstimateData["lngManufacturingQuantity"] = $aryEstimateData["lngProductionQuantity"];

    // ����¤����ñ�� �� ����¤���� / ����ͽ���
    if ($aryEstimateData["lngProductionQuantity"] != 0) {
        $aryEstimateData["curManufacturingProductPrice"] = $aryEstimateData["curManufacturingCost"] / $aryEstimateData["lngProductionQuantity"];
    } else {
        $aryEstimateData["curManufacturingProductPrice"] = 0.00;
    }

// ͽ������ �� ����ͽ��� �� Ǽ�� + �����оݳ����
    $aryEstimateData["curAmountOfSales"] = $aryEstimateData["lngProductionQuantity"] * $aryEstimateData["curProductPrice"] + $aryEstimateData["curNonFixedCost"];

    // �����ɸ���� �� ͽ������ �� ����¤����
    $aryEstimateData["curTargetProfit"] = $aryEstimateData["curAmountOfSales"] - $aryEstimateData["curManufacturingCost"];

    // ��ɸ����Ψ �� �����ɸ���� / ͽ������
    if ($aryEstimateData["curAmountOfSales"] != 0) {
        $aryEstimateData["curAchievementRatio"] = round($aryEstimateData["curTargetProfit"] / $aryEstimateData["curAmountOfSales"] * 100, 2);
    }

    // ������¤���� �� ͽ������ �� ɸ����
    $aryEstimateData["curStandardCost"] = $aryEstimateData["curAmountOfSales"] * $aryEstimateData["curStandardRate"] / 100;

    // ��������� �� �����ɸ���� �� ������¤����
    $aryEstimateData["curProfitOnSales"] = $aryEstimateData["curTargetProfit"] - $aryEstimateData["curStandardCost"];

    ///////////////////////////////////////////////////////////////////////
    // ȯ��
    ///////////////////////////////////////////////////////////////////////
    // �������׷ײ�Ŀ�
    $aryEstimateData["lngOrderFixedQuantityTotal"] = $aryEstimateData["lngReceiveProductQuantity"];

    // ������ñ�� �� �������׷ײ踶�� / ����ͽ���
    if ($aryEstimateData["lngReceiveProductQuantity"] != 0) {
        $aryEstimateData["curOrderFixedProductPrice"] = $aryEstimateData["curOrderFixedCost"] / $aryEstimateData["lngReceiveProductQuantity"];
    } else {
        $aryEstimateData["curOrderFixedProductPrice"] = 0.00;
    }

    // �������׷ײ�Ŀ�
    $aryEstimateData["lngOrderMemberQuantity"] = $aryEstimateData["lngReceiveProductQuantity"];

    // ������ñ�� �� �������׷ײ踶�� / ����ͽ���
    if ($aryEstimateData["lngReceiveProductQuantity"] != 0) {
        $aryEstimateData["curOrderMemberProductPrice"] = $aryEstimateData["curOrderMemberCost"] / $aryEstimateData["lngReceiveProductQuantity"];
    } else {
        $aryEstimateData["curOrderMemberProductPrice"] = 0.00;
    }

    // ����¤����
    $aryEstimateData["curOrderManufacturingCost"] = $aryEstimateData["curOrderFixedCost"] + $aryEstimateData["curOrderMemberCost"];

    // ����¤���ѷײ�Ŀ�
    $aryEstimateData["lngOrderManufacturingQuantity"] = $aryEstimateData["lngReceiveProductQuantity"];

    // ����¤����ñ��
    if ($aryEstimateData["lngOrderManufacturingQuantity"] != 0) {
        $aryEstimateData["curOrderManufacturingProductPrice"] = $aryEstimateData["curOrderManufacturingCost"] / $aryEstimateData["lngOrderManufacturingQuantity"];
    } else {
        $aryEstimateData["curOrderManufacturingProductPrice"] = 0.00;
    }

    // ͽ�������ȯ��� �� ������� �� Ǽ��
    $aryEstimateData["curOrderAmountOfSales"] = $aryEstimateData["lngReceiveProductQuantity"] * $aryEstimateData["curReceiveProductPrice"];

    // �����ɸ���ס�ȯ��� �� ͽ�������ȯ��� �� ����¤���ѡ�ȯ���
    $aryEstimateData["curOrderTargetProfit"] = $aryEstimateData["curOrderAmountOfSales"] - $aryEstimateData["curOrderManufacturingCost"];

    // ��ɸ����Ψ��ȯ��� �� �����ɸ���� / ͽ������
    if ($aryEstimateData["curOrderAmountOfSales"] != 0) {
        $aryEstimateData["curOrderAchievementRatio"] = round($aryEstimateData["curOrderTargetProfit"] / $aryEstimateData["curOrderAmountOfSales"] * 100, 2);
    } else {
        $aryEstimateData["curOrderAchievementRatio"] = 0.00;
    }

    // ������¤�����ȯ��� �� ͽ������ �� ɸ����
    $aryEstimateData["curOrderStandardCost"] = $aryEstimateData["curOrderAmountOfSales"] * $aryEstimateData["curStandardRate"] / 100;

    // ��������ס�ȯ��� �� �����ɸ���� �� ������¤����
    $aryEstimateData["curOrderProfitOnSales"] = $aryEstimateData["curOrderTargetProfit"] - $aryEstimateData["curOrderStandardCost"];

    return $aryEstimateData;
}

/**
 * ����޽������ͥǡ��������ؿ�
 *
 *    ����޽�����ܤ������ͥǡ������������ؿ�
 *
 *    @param  Array $aryNumberData    ���ͥǡ���
 *    @return Array $aryNumberData    ���ͥǡ���
 *    @access public
 */
function fncGetCommaNumber($aryNumberData)
{
    $aryKeys = array_keys($aryNumberData);
    foreach ($aryKeys as $strKey) {
        if ($strKey == "curProductPrice" or $strKey == "curRetailPrice") {
            preg_match("/\.(\d+)$/", $aryNumberData[$strKey], $lngFloor);
            $aryNumberData[$strKey] = number_format($aryNumberData[$strKey], 4, '.', ',');
        } elseif (preg_match("/^cur/", $strKey)) {
            preg_match("/\.(\d+)$/", $aryNumberData[$strKey], $lngFloor);
            $aryNumberData[$strKey] = number_format($aryNumberData[$strKey], 2, '.', ',');
        } elseif (preg_match("/Quantity$/", $strKey)) {
            $aryNumberData[$strKey] = number_format($aryNumberData[$strKey]);
        }
    }

    return $aryNumberData;
}

return true;
