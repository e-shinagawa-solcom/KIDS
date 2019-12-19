<?
/**
 *    帳票出力 見積原価管理用ライブラリ
 *
 *    @package   KIDS
 *    @copyright Copyright &copy; 2004, AntsBizShare
 *    @author    Kenji Chiba
 *    @access    public
 *    @version   1.00
 */

// 社内レート定義
define("DEF_MONETARYCLASS_SHANAI", 2); // 社内

// 売上科目定義
define("DEF_SALESCLASS_HONNI", 1); // 本荷
define("DEF_SALESCLASS_TEST", 2); // テストロケ

/**
 * 標準割合取得関数
 *
 *    標準割合データクエリ関数
 *
 *    @param  String  $strProductCode     製品コード
 *    @param  Object  $objDB             DBオブジェクト
 *    @return Integer $curStandardRate 標準割合
 *    @access public
 */
function fncGetEstimateDefault($objDB)
{
    list($lngResultID, $lngResultNum) = fncQuery("SELECT To_char( curstandardrate, '990.9999' ) as curstandardrate FROM m_EstimateStandardRate WHERE dtmApplyStartDate < NOW() AND dtmApplyEndDate > NOW()", $objDB);

    if ($lngResultNum < 1) {
// 2004.10.01 suzukaze update start
        // もし当月の標準割合が参照できない場合最新の日付の標準割合を参照
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

// 標準割合の値については ％表記にて扱う
    $curStandardRate = $curStandardRate * 100;

    unset($objResult);

    return $curStandardRate;
}

/**
 * 見積原価作成時の社内通貨取得関数
 *
 *    社内通貨データクエリ関数
 *
 *    @param  String  $dtmInsertDate     見積原価登録日
 *    @param  Object  $objDB             DBオブジェクト
 *    @return Integer $curStandardRate 標準割合
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
 * 見積原価計算取得関数
 *
 *    lngEstimateNo から見積原価計算各種表示に使用するデータを取得する関数
 *
 *    @param  String $lngEstimateNo    見積原価ナンバー
 *    @param  Object $objDB            DBオブジェクト
 *    @return Array  $aryData            見積原価データ
 *    @access public
 */
function fncGetEstimate($lngEstimateNo, $objDB)
{
    //////////////////////////////////////////////////////////
    // 見積原価データ取得
    //////////////////////////////////////////////////////////
    $aryQuery[] = "SELECT p.strProductCode, p.strProductName,";
    $aryQuery[] = " to_char(p.dtmDeliveryLimitDate,'YYYY/MM') AS dtmDeliveryLimitDate,";
    $aryQuery[] = " g.strGroupDisplayCode AS strInChargeGroupDisplayCode,";
    $aryQuery[] = " u.strUserDisplayCode AS strInChargeUserDisplayCode,";
    $aryQuery[] = " u.strUserDisplayName AS strInChargeUserDisplayName,";
    $aryQuery[] = " p.curRetailPrice, p.lngCartonQuantity,";

    // 製品単位がctnならば、生産予定数はpcsに変換する
    $aryQuery[] = " CASE WHEN p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_CTN;
    $aryQuery[] = "  THEN p.lngProductionQuantity * p.lngCartonQuantity ";
    $aryQuery[] = "  ELSE p.lngProductionQuantity ";
    $aryQuery[] = " END AS lngProductionQuantity, ";

    // 製品単位がctnならば、計画C/tはそのまま生産予定数
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

    $aryData["strRemark"] = $objResult->strnote; // コメント

    unset($objResult);

    //////////////////////////////////////////////////////////
    // 受注データ取得
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
        // 受注個数、受注小計に値をセット
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
            // 受注形態がカートンの場合
            if ($aryReceiveResult[$i]["lngproductunitcode"] == DEF_PRODUCTUNIT_CTN) {
                // 受注数 ＝ 数量 ＊ カートン入数
                $lngReceiveProductQuantity += $aryReceiveResult[$i]["lngproductquantity"] * $aryReceiveResult[$i]["lngcartonquantity"];
                // 受注価格 ＝ 価格 ／ カートン入数（受注価格は最終の値）
                if ($aryReceiveResult[$i]["lngcartonquantity"] != 0) {
                    $curReceiveProductPrice = $aryReceiveResult[$i]["curproductprice"] / $aryReceiveResult[$i]["lngcartonquantity"];
                } else {
                    $curReceiveProductPrice = 0;
                }
            }
            // 受注形態がピースの場合
            else {
                // 受注数 ＝ 数量
                $lngReceiveProductQuantity += $aryReceiveResult[$i]["lngproductquantity"];
                // 受注価格 ＝ 価格（受注価格は最終の値）
                $curReceiveProductPrice = $aryReceiveResult[$i]["curproductprice"];
            }
            // 受注金額 ＝ 数量 ＊ 価格
            $curReceiveSubTotalPrice += $aryReceiveResult[$i]["lngproductquantity"] * $aryReceiveResult[$i]["curproductprice"];
        }
        // 受注C/t
        if ($aryData["lngCartonQuantity"] != 0 and $aryData["lngCartonQuantity"] != "") {
            $aryData["lngReceiveCartonProduction"] = $lngReceiveProductQuantity / $aryData["lngCartonQuantity"];
        }

        // 受注個数、受注小計に値をセット
        $aryData["lngReceiveProductQuantity"] = $lngReceiveProductQuantity;
        $aryData["curReceiveProductPrice"] = $curReceiveProductPrice;
        $aryData["curReceiveSubTotalPrice"] = $curReceiveSubTotalPrice;

        unset($objResult);
    }

    return $aryData;
}

/**
 * デフォルト見積原価対応値取得関数
 *
 *    デフォルト見積原価の対応する値の取得データクエリ関数
 *
 *    @param  Integer $lngEstimateNo            見積原価番号
 *    @param  Integer $lngReceiveQuantity        受注数
 *    @param  Integer $lngProductionQuantity    生産予定数
 *    @param  Array      $curProductPrice        予定納価
 *    @param  Array      $aryRate                 通貨レートコードをキーとする通貨レート
 *    @param  Object  $objDB                     DBオブジェクト
 *    @return Array     $aryDefaultValue         デフォルト値
 *    @return Array     $curReceiveProductPrice         実績納価
 *
 *    2005/06/10     ABE Yuuki
 *    引数に実績納価を追加し受注価額を実績納価を元に算出するように修正
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

    // 仕入科目を配列の数値キーに対応させるための配列生成
    $aryStockKey = array("431" => 0, "433" => 1, "403" => 2, "402" => 3, "401" => 4, "420" => 5, "1224" => 6, "1230" => 7);

    // 仕入科目毎のカウンター配列を生成
    $aryCount = array("431" => 0, "433" => 0, "403" => 0, "402" => 0, "401" => 0, "420" => 0, "1224" => 0, "1230" => 0);

    // Booleanに対応させるための配列生成
    $aryBooleanString = array("t" => "true", "f" => "false", "true" => "true", "false" => "false", "" => "false");

    $aryMonetaryUnit = array(DEF_MONETARY_YEN => "\\", DEF_MONETARY_USD => "$", DEF_MONETARY_HKD => "HKD");

    // 見積原価テーブルデータ取得
    // 明細の数だけループ
    for ($i = 0; $i < $lngResultNum; $i++) {
        $objResult = $objDB->fetchObject($lngResultID, $i);

        // $aryDetail[科目毎配列番号][科目毎カウンター][明細カラム名]
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

        // もし、パーセント入力フラグが設定されていれば以下の値を引数より設定する
        if ($aryBooleanString[$objResult->bytpercentinputflag] == "true") {
            $aryDefaultValue[$i]["lngProductQuantity"] = $lngReceiveQuantity;

            $aryDefaultValue[$i]["curProductRate"] = $objResult->curproductrate;

            //2005/06/10 ABE Yuuki 数量×実績納価×単価＝仕入価額
            $aryDefaultValue[$i]["curSubTotalPrice"] = $lngReceiveQuantity * $curReceiveProductPrice * $objResult->curproductrate;
            //$aryDefaultValue[$i]["curSubTotalPrice"]   = $lngReceiveQuantity * $curProductPrice * $objResult->curproductrate;
        } else {
            // 個数入力設定されていてその個数が生産予定数の場合は、受注数を設定する
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

        // デフォルト値にはフラグを設定する
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
 * 見積原価計算明細取得関数
 *
 *    lngEstimateNo から見積原価計算各種表示に使用する明細データを取得する関数
 *
 *    @param  String $lngEstimateNo    見積原価ナンバー
 *    @param  String $strProductCode    見積対象の製品コード
 *    @param  Array  $aryRate            通貨レートコードをキーとする通貨レート
 *    @param    Array  $aryDefaultValue 見積原価のデフォルト値に対する入力されたデータ
 *    @param  Object $objDB            DBオブジェクト
 *    @return Array  $aryDetail        見積原価明細データ
 *            Array  $aryOrderDetail    発注明細データ
 *    @access public
 */
function fncGetEstimateDetail($lngEstimateNo, $strProductCode, $aryRate, $aryDefaultValue, $objDB)
{
    $aryDetail = array();
    //////////////////////////////////////////////////////////
    // 見積詳細データ取得
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

    // 仕入科目を配列の数値キーに対応させるための配列生成
    $aryStockKey = array("431" => 0, "433" => 1, "403" => 2, "402" => 3, "401" => 4, "420" => 5, "1224" => 6, "1230" => 7);

    // 仕入科目毎のカウンター配列を生成
    $aryCount = array("431" => 0, "433" => 0, "403" => 0, "402" => 0, "401" => 0, "420" => 0, "1224" => 0, "1230" => 0);

    // Booleanに対応させるための配列生成
    $aryBooleanString = array("t" => "true", "f" => "false", "true" => "true", "false" => "false", "" => "false");

    $aryMonetaryUnit = array(DEF_MONETARY_YEN => "\\", DEF_MONETARY_USD => "$", DEF_MONETARY_HKD => "HKD");

    // 見積原価テーブルデータ取得
    // 明細の数だけループ
    for ($i = 0; $i < $lngResultNum; $i++) {
        $objResult = $objDB->fetchObject($lngResultID, $i);

        // $aryDetail[科目毎配列番号][科目毎カウンター][明細カラム名]
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
    //発注から仕入に変更
    //////////////////////////////////////////////////////////
    // 発注明細データ取得
    //////////////////////////////////////////////////////////
    $aryQuery[] = "SELECT od.lngStockSubjectCode, ";

    // 製品単位がctnならば、数量はpcsに変換する
    $aryQuery[] = " CASE WHEN od.lngProductUnitCode = " . DEF_PRODUCTUNIT_CTN;
    $aryQuery[] = "  THEN od.lngProductQuantity * p.lngCartonQuantity ";
    $aryQuery[] = "  ELSE od.lngProductQuantity ";
    $aryQuery[] = " END AS lngOrderProductQuantity, ";

    // 通貨の違いによる価格を返還する
    $aryQuery[] = " od.curSubTotalPrice * o.curConversionRate AS curOrderSubTotalPrice ";
    $aryQuery[] = "FROM t_StockDetail od";
    $aryQuery[] = " LEFT JOIN m_Stock o ON ( od.lngStockNo = o.lngStockNo ) ";
    $aryQuery[] = " LEFT JOIN m_Product p ON ( od.strProductCode = p.strProductCode )";
    $aryQuery[] = "WHERE od.strProductCode = '" . $strProductCode . "'";

    // リビジョンナンバーが最大 かつ リバイズコードが最大 かつ リビジョンナンバー最小値が0以上
    $aryQuery[] = " AND o.lngRevisionNo = ( ";
    $aryQuery[] = "SELECT MAX( o1.lngRevisionNo ) FROM m_Stock o1 WHERE o1.strStockCode = o.strStockCode )";
    $aryQuery[] = " AND 0 <= ( ";
    $aryQuery[] = "SELECT MIN( o3.lngRevisionNo ) FROM m_Stock o3 WHERE o3.bytInvalidFlag = false AND o3.strStockCode = o.strStockCode )";

    list($lngResultID, $lngResultNum) = fncQuery(join(" ", $aryQuery), $objDB);
    unset($aryQuery);

    // 仕入科目コードをキーとする仕入名称想配列を取得
    $aryStockSubjectCode = fncGetMasterValue("m_StockSubject", "lngStockSubjectCode", "strStockSubjectName", "Array", "", $objDB);

    // 発注明細テーブルデータ取得
    // 明細の数だけループ
    for ($i = 0; $i < $lngResultNum; $i++) {
        $objResult = $objDB->fetchObject($lngResultID, $i);

        // $aryDetail[科目毎配列番号][明細カラム名]
        $aryOrderDetail[$aryStockKey[$objResult->lngstocksubjectcode]]["strStockSubjectName"] = $aryStockSubjectCode[$objResult->lngstocksubjectcode];

        $aryOrderDetail[$aryStockKey[$objResult->lngstocksubjectcode]]["lngOrderQuantity"] += $objResult->lngorderproductquantity;

        $aryOrderDetail[$aryStockKey[$objResult->lngstocksubjectcode]]["curOrderSubTotalPrice"] += $objResult->curordersubtotalprice;

        unset($objResult);
    }

    if ($lngResultNum > 0) {
        $objDB->freeResult($lngResultID);
    }

    // 見積原価のデフォルト値に対する入力データより発注データへの設定処理
    // 明細の数だけループ
    for ($i = 0; $i < count($aryDefaultValue); $i++) {
        // $aryDetail[科目毎配列番号][明細カラム名]
        $aryOrderDetail[$aryStockKey[$aryDefaultValue[$i]["lngStockSubjectCode"]]]["lngOrderQuantity"] += $aryDefaultValue[$i]["lngProductQuantity"];

        $aryOrderDetail[$aryStockKey[$aryDefaultValue[$i]["lngStockSubjectCode"]]]["curOrderSubTotalPrice"] += $aryDefaultValue[$i]["curSubTotalPriceJP"];
    }

    unset($lngResultID);
    unset($lngResultNum);
    unset($aryCount);

    return array($aryDetail, $aryOrderDetail);
}

/**
 * 見積原価計算明細HTML出力文字列取得関数
 *
 *    見積原価計算明細データを明細テンプレートにはめ込んだ文字列を取得する関数
 *
 *    @param  String $strProductCode    製品コード
 *    @param  String $aryDetail        見積原価計算明細データ
 *    @param  String $aryOrderDetail    発注明細データ
 *    @param  String $aryOrderDefault    見積原価デフォルトデータ
 *    @param  String $strDetailTemplatePath        見積原価明細テンプレート
 *    @param  String $strOrderDetailTemplatePath    発注明細テンプレート
 *    @return Array  $aryDetail        見積原価明細データ
 *    @access public
 */
function fncGetEstimateDetailHtml($aryDetail, $aryOrderDetail, $aryOrderDefault, $strDetailTemplatePath, $strOrderDetailTemplatePath, $objDB)
{
    // 見積原価明細テンプレート取得
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate($strDetailTemplatePath);
    $strTemplate = $objTemplate->strTemplate;

    // 見積原価明細仕入科目計行テンプレート取得
    $objOrderTemplate = new clsTemplate();
    $objOrderTemplate->getTemplate($strOrderDetailTemplatePath);
    $strOrderTemplate = $objOrderTemplate->strTemplate;

    // 仕入科目コードをキーとする仕入名称想配列を取得
    $aryStockSubjectCode = fncGetMasterValue("m_StockSubject", "lngStockSubjectCode", "strStockSubjectName", "Array", "", $objDB);

    // 仕入科目を配列の数値キーに対応させるための配列生成
    $aryStockKey = array(0 => "431", 1 => "433", 2 => "403", 3 => "402", 4 => "401", 5 => "420", 6 => "1224", 7 => "1230");

    // 会社表示コードをキーとする会社名連想配列を取得
    $aryCompanyName = fncGetMasterValue("m_Company", "strCompanyDisplayCode", "strCompanyDisplayName", "Array", "", $objDB);

    // 減価償却対象に対応させるための配列生成
    $aryPayOffFlag = array("t" => "○", "f" => "", "true" => "○", "false" => "", "" => "");

    // 固定費、部材費合計値
    $curFixedCost = 0;
    $curMemberCost = 0;

    // 固定費小計 added by k.saito
    $curFixedCostSubtotal = 0;

    // 検査費用
    $curCheckCost = 0;

    //////////////////////////////////////////////////////////////////
    // 明細データ
    //////////////////////////////////////////////////////////////////
    // $aryDitail[仕入科目][明細行][項目]
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

            // コスト加算
            if (!is_numeric($aryDetail[$i][$j]["curSubTotalPriceJP"])) {
                $aryDetail[$i][$j]["curSubTotalPriceJP"] = str_replace("\\", "", $aryDetail[$i][$j]["curSubTotalPriceJP"]);
                $aryDetail[$i][$j]["curSubTotalPriceJP"] = str_replace(" ", "", $aryDetail[$i][$j]["curSubTotalPriceJP"]);
                $aryDetail[$i][$j]["curSubTotalPriceJP"] = str_replace(",", "", $aryDetail[$i][$j]["curSubTotalPriceJP"]);
            }
            if ($aryDetail[$i][$j]["curSubTotalPriceJP"] != "") {
                $aryCost[$i] += $aryDetail[$i][$j]["curSubTotalPriceJP"];
                // 対象が固定費ならば
                if ($i < 3) {
// start modified by k.saito 2005.01.27
                    // 償却対象の場合、固定費合計に加算する
                    if ($aryDetail[$i][$j]["bytPayOffTargetFlag"] == "○") {
                        $curFixedCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];
                    }

                    // 償却対象外合計取得
                    else {
                        $curNonFixedCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];
                    }

                    // 償却対象関係なく、固定費小計に加算する
                    $curFixedCostSubtotal += $aryDetail[$i][$j]["curSubTotalPriceJP"];
                    // 科目毎の小計に加算する
                    $curSubjectTotalCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];

//                    else
                    //                    {
                    //                        $curMemberCost       += $aryDetail[$i][$j]["curSubTotalPriceJP"];
                    //                    }
                }
                // 対象が部材費ならば
                else {
                    if ($aryDetail[$i][$j]["bytPayOffTargetFlag"] == "○") {
                        $curFixedCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];
                    } else {
                        $curMemberCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];
                        $curSubjectTotalCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];
                    }
                }
            }

            // 検査費用対応
            if ($lngStockSubjectCode == "403" and $lngStockItemCode == "6") {
                $curCheckCost = $aryDetail[$i][$j]["curSubTotalPriceJP"];
            }

            // 計画個数加算
            if (!is_numeric($aryDetail[$i][$j]["lngProductQuantity"])) {
                $aryDetail[$i][$j]["lngProductQuantity"] = str_replace("\\", "", $aryDetail[$i][$j]["lngProductQuantity"]);
                $aryDetail[$i][$j]["lngProductQuantity"] = str_replace(" ", "", $aryDetail[$i][$j]["lngProductQuantity"]);
                $aryDetail[$i][$j]["lngProductQuantity"] = str_replace(",", "", $aryDetail[$i][$j]["lngProductQuantity"]);
            }
            if ($aryDetail[$i][$j]["curSubTotalPriceJP"] != "") {
                $aryProductQuantity[$i] += $aryDetail[$i][$j]["lngProductQuantity"];
            }

            // 発注欄へのデフォルト値設定
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

            // カンマ処理
            $aryDetail[$i][$j] = fncGetCommaNumber($aryDetail[$i][$j]);

            // 置き換え
            $objTemplate->replace($aryDetail[$i][$j]);
            $objTemplate->complete();

            if ($aryDetail[$i][$j]["bytPercentInputFlag"] == "true") {
                $aryDetail[$i][$j]["curProductPrice"] = $aryDetail[$i][$j]["curConversionRate"];
            }

            // 仕入科目毎にテンプレート保持
            $aryDetailTemplate[$i] .= $objTemplate->strTemplate;

            $objTemplate->strTemplate = $strTemplate;
        }

        // 検査費用対応
        // 仕入科目「1230」で、検査費用合計が「0円」ではなく、仕入科目「1230」合計が「0円」はない場合
        // 固定費減算処理
        if ($lngStockSubjectCode == "1230" and $curCheckCost != 0 and $aryCost[$i] != 0) {
            $lngCount = count($aryDetail[$i]);
            $aryDetail[$i][$lngCount]["lngStockSubjectCode"] = $lngStockSubjectCode;
            $aryDetail[$i][$lngCount]["strStockSubjectName"] = $strStockSubjectName;
            $aryDetail[$i][$lngCount]["strStockItemName"] = "固定費減算";
            $aryDetail[$i][$lngCount]["curSubTotalPriceJP"] = 0 - $curCheckCost;
            $aryDetail[$i][$lngCount]["curOrderSubTotalPriceJP"] = 0 - $curCheckCost;

            $aryCost[$i] += $aryDetail[$i][$lngCount]["curSubTotalPriceJP"];
            $curMemberCost += $aryDetail[$i][$lngCount]["curSubTotalPriceJP"];
            $curSubjectTotalCost += $aryDetail[$i][$lngCount]["curSubTotalPriceJP"];

            // カンマ処理
            $aryDetail[$i][$lngCount] = fncGetCommaNumber($aryDetail[$i][$lngCount]);

            // 置き換え
            $objTemplate->replace($aryDetail[$i][$lngCount]);
            $objTemplate->complete();

            // 仕入科目毎にテンプレート保持
            $aryDetailTemplate[$i] .= $objTemplate->strTemplate;

            $objTemplate->strTemplate = $strTemplate;
        }

        ////////////////////////////////////////////////////////////
        // 仕入科目計行追加処理
        ////////////////////////////////////////////////////////////
        // 仕入科目計の算出
        $arySubDetail[$i]["curSubjectTotalCost"] = number_format($curSubjectTotalCost, 2, '.', ',');

        // コスト加算
        if ($aryOrderDetail[$i]["curOrderSubTotalPrice"] == "") {
            $aryOrderDetail[$i]["curOrderSubTotalPrice"] = 0.00;
        }
        // 検査費用対応
        if ($lngStockSubjectCode == "1230" and $curCheckCost != 0) {
            $aryOrderDetail[$i]["curOrderSubTotalPrice"] -= $curCheckCost;
        }
        $aryOrderCost[$i] = $aryOrderDetail[$i]["curOrderSubTotalPrice"];

        // 計画個数加算
        $aryOrderProductQuantity[$i] += $aryOrderDetail[$i]["lngOrderQuantity"];

        if ($aryOrderDetail[$i]["strStockSubjectName"] == "") {
            $aryOrderDetail[$i]["strStockSubjectName"] = $aryStockSubjectCode[$aryStockKey[$i]];
        }
        // カンマ処理
        $aryOrderDetail[$i] = fncGetCommaNumber($aryOrderDetail[$i]);

        // 仕入科目計置き換え
        // テンプレート生成
        $objOrderTemplate->replace($aryOrderDetail[$i]);
        $objOrderTemplate->replace($arySubDetail[$i]);
        $objOrderTemplate->complete();

        // 仕入科目毎にテンプレート保持
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

    // 固定明細部分
    for ($i = 0; $i < 3; $i++) {
        // 固定明細部分HTML
        $aryEstimateDetail["strFixCostTemplate"] .= $aryDetailTemplate[$i];

        // 見積原価
        $aryEstimate["lngFixedQuantity"] += $aryProductQuantity[$i];

        // 発注
        $aryEstimate["curOrderFixedCost"] += $aryOrderCost[$i];
        $aryEstimate["lngOrderFixedQuantity"] += $aryOrderProductQuantity[$i];
    }
    // 固定費小計 added by k.saito
    $aryEstimate["curFixedCostSubtotal"] = $curFixedCostSubtotal;
    // 固定費合計は計算値
    $aryEstimate["curFixedCost"] = $curFixedCost;

// 償却対象外合計
    $aryEstimate["curNonFixedCost"] = (is_null($curNonFixedCost) || empty($curNonFixedCost)) ? 0.00 : $curNonFixedCost;

    // 部材明細部分
    for ($i = 3; $i < 8; $i++) {
        // 部材明細部分HTML
        $aryEstimateDetail["strMemberCostTemplate"] .= $aryDetailTemplate[$i];

        // 見積原価
        $aryEstimate["lngMemberQuantity"] += $aryProductQuantity[$i];

        // 発注
        $aryEstimate["curOrderMemberCost"] += $aryOrderCost[$i];
//        $aryEstimate["lngOrderMemberQuantity"]        += $aryOrderProductQuantity[$i];
    }
    // 部材費合計は計算値
    $aryEstimate["curMemberCost"] = $curMemberCost;

    unset($aryDetailTemplate);
    unset($aryCost);

    return array($aryEstimateDetail, $aryEstimate, $aryHiddenString);
}

/**
 * 見積原価計算計算結果取得関数
 *
 *    総製造費用~売上総利益の見積原価計算計算結果データを取得する関数
 *
 *    @param  Array  $aryEstimateData    見積原価計算データ
 *    @param  Object $objDB            DBオブジェクト
 *    @return Array  $aryEstimateData    見積原価計算データ
 *    @access public
 */
function fncGetEstimateCalculate($aryEstimateData)
{
    ///////////////////////////////////////////////////////////////////////
    // 予定
    ///////////////////////////////////////////////////////////////////////
    // 固定費合計計画個数
    $aryEstimateData["lngFixedQuantityTotal"] = $aryEstimateData["lngProductionQuantity"];

    // 固定費単価 ⇒ 部材費合計計画原価 / 生産予定数
    if ($aryEstimateData["lngFixedQuantityTotal"] != 0) {
        $aryEstimateData["curFixedProductPrice"] = $aryEstimateData["curFixedCost"] / $aryEstimateData["lngProductionQuantity"];
    } else {
        $aryEstimateData["curFixedProductPrice"] = 0.00;
    }

    // 部材費合計計画個数
    $aryEstimateData["lngMemberQuantityTotal"] = $aryEstimateData["lngProductionQuantity"];

    // 部材費単価 ⇒ 部材費合計計画原価 / 生産予定数
    if ($aryEstimateData["lngMemberQuantityTotal"] != 0) {
        $aryEstimateData["curMemberProductPrice"] = $aryEstimateData["curMemberCost"] / $aryEstimateData["lngMemberQuantityTotal"];
    } else {
        $aryEstimateData["curMemberProductPrice"] = 0.00;
    }

// 総製造費用 ⇒ 固定費 ＋ 部材費 + 償却対象外合計
    $aryEstimateData["curManufacturingCost"] = $aryEstimateData["curFixedCost"] + $aryEstimateData["curMemberCost"] + $aryEstimateData["curNonFixedCost"];

    // 総製造費用計画個数 ⇒ 生産予定数（pcs）
    $aryEstimateData["lngManufacturingQuantity"] = $aryEstimateData["lngProductionQuantity"];

    // 総製造費用単価 ⇒ 総製造費用 / 生産予定数
    if ($aryEstimateData["lngProductionQuantity"] != 0) {
        $aryEstimateData["curManufacturingProductPrice"] = $aryEstimateData["curManufacturingCost"] / $aryEstimateData["lngProductionQuantity"];
    } else {
        $aryEstimateData["curManufacturingProductPrice"] = 0.00;
    }

// 予定売上高 ⇒ 生産予定数 × 納価 + 償却対象外合計
    $aryEstimateData["curAmountOfSales"] = $aryEstimateData["lngProductionQuantity"] * $aryEstimateData["curProductPrice"] + $aryEstimateData["curNonFixedCost"];

    // 企画目標利益 ⇒ 予定売上高 − 総製造費用
    $aryEstimateData["curTargetProfit"] = $aryEstimateData["curAmountOfSales"] - $aryEstimateData["curManufacturingCost"];

    // 目標利益率 ⇒ 企画目標利益 / 予定売上高
    if ($aryEstimateData["curAmountOfSales"] != 0) {
        $aryEstimateData["curAchievementRatio"] = round($aryEstimateData["curTargetProfit"] / $aryEstimateData["curAmountOfSales"] * 100, 2);
    }

    // 間接製造経費 ⇒ 予定売上高 × 標準割合
    $aryEstimateData["curStandardCost"] = $aryEstimateData["curAmountOfSales"] * $aryEstimateData["curStandardRate"] / 100;

    // 売上総利益 ⇒ 企画目標利益 − 間接製造経費
    $aryEstimateData["curProfitOnSales"] = $aryEstimateData["curTargetProfit"] - $aryEstimateData["curStandardCost"];

    ///////////////////////////////////////////////////////////////////////
    // 発注
    ///////////////////////////////////////////////////////////////////////
    // 固定費合計計画個数
    $aryEstimateData["lngOrderFixedQuantityTotal"] = $aryEstimateData["lngReceiveProductQuantity"];

    // 部材費単価 ⇒ 部材費合計計画原価 / 生産予定数
    if ($aryEstimateData["lngReceiveProductQuantity"] != 0) {
        $aryEstimateData["curOrderFixedProductPrice"] = $aryEstimateData["curOrderFixedCost"] / $aryEstimateData["lngReceiveProductQuantity"];
    } else {
        $aryEstimateData["curOrderFixedProductPrice"] = 0.00;
    }

    // 部材費合計計画個数
    $aryEstimateData["lngOrderMemberQuantity"] = $aryEstimateData["lngReceiveProductQuantity"];

    // 部材費単価 ⇒ 部材費合計計画原価 / 生産予定数
    if ($aryEstimateData["lngReceiveProductQuantity"] != 0) {
        $aryEstimateData["curOrderMemberProductPrice"] = $aryEstimateData["curOrderMemberCost"] / $aryEstimateData["lngReceiveProductQuantity"];
    } else {
        $aryEstimateData["curOrderMemberProductPrice"] = 0.00;
    }

    // 総製造費用
    $aryEstimateData["curOrderManufacturingCost"] = $aryEstimateData["curOrderFixedCost"] + $aryEstimateData["curOrderMemberCost"];

    // 総製造費用計画個数
    $aryEstimateData["lngOrderManufacturingQuantity"] = $aryEstimateData["lngReceiveProductQuantity"];

    // 総製造費用単価
    if ($aryEstimateData["lngOrderManufacturingQuantity"] != 0) {
        $aryEstimateData["curOrderManufacturingProductPrice"] = $aryEstimateData["curOrderManufacturingCost"] / $aryEstimateData["lngOrderManufacturingQuantity"];
    } else {
        $aryEstimateData["curOrderManufacturingProductPrice"] = 0.00;
    }

    // 予定売上高（発注） ⇒ 受注数量 × 納価
    $aryEstimateData["curOrderAmountOfSales"] = $aryEstimateData["lngReceiveProductQuantity"] * $aryEstimateData["curReceiveProductPrice"];

    // 企画目標利益（発注） ⇒ 予定売上高（発注） − 総製造費用（発注）
    $aryEstimateData["curOrderTargetProfit"] = $aryEstimateData["curOrderAmountOfSales"] - $aryEstimateData["curOrderManufacturingCost"];

    // 目標利益率（発注） ⇒ 企画目標利益 / 予定売上高
    if ($aryEstimateData["curOrderAmountOfSales"] != 0) {
        $aryEstimateData["curOrderAchievementRatio"] = round($aryEstimateData["curOrderTargetProfit"] / $aryEstimateData["curOrderAmountOfSales"] * 100, 2);
    } else {
        $aryEstimateData["curOrderAchievementRatio"] = 0.00;
    }

    // 間接製造経費（発注） ⇒ 予定売上高 × 標準割合
    $aryEstimateData["curOrderStandardCost"] = $aryEstimateData["curOrderAmountOfSales"] * $aryEstimateData["curStandardRate"] / 100;

    // 売上総利益（発注） ⇒ 企画目標利益 − 間接製造経費
    $aryEstimateData["curOrderProfitOnSales"] = $aryEstimateData["curOrderTargetProfit"] - $aryEstimateData["curOrderStandardCost"];

    return $aryEstimateData;
}

/**
 * カンマ処理数値データ取得関数
 *
 *    カンマ処理を施した数値データを取得する関数
 *
 *    @param  Array $aryNumberData    数値データ
 *    @return Array $aryNumberData    数値データ
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
