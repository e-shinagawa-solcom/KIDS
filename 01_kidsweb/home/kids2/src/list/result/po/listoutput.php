<?
/**
 *    帳票出力 発注書 印刷プレビュー画面
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
 *
 *    更新履歴
 *    2004.03.05    海外の会社の宛先に付ける TO の横に : を追加するように修正する
 *    2004.03.30    明細行のソート順を表示用ソートキーの順序で表示するように変更（明細行番号の項目も表示用ソートキーに変更）
 *
 */
// 帳票出力 印刷プレビュー画面
// frameset.php -> strSessionID     -> listoutput.php
// frameset.php -> lngReportCode    -> listoutput.php
// frameset.php -> lngReportKeyCode -> listoutput.php

// 設定読み込み
include_once 'conf.inc';

// ライブラリ読み込み
require LIB_FILE;
require SRC_ROOT . "list/cmn/lib_lo.php";
require SRC_ROOT . "m/cmn/lib_m.php";

$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

//////////////////////////////////////////////////////////////////////////
// POST(一部GET)データ取得
//////////////////////////////////////////////////////////////////////////
if ($_POST) {
    $aryData = $_POST;
} elseif ($_GET) {
    $aryData = $_GET;
}

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngReportCode"] = "ascii(1,7)";
$aryCheck["strReportKeyCode"] = "null:number(0,9999999)";

$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 権限確認
if (!fncCheckAuthority(DEF_FUNCTION_LO0, $objAuth) || !fncCheckAuthority(DEF_FUNCTION_PO0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 帳票出力コピーファイルパス取得クエリ生成
//===================================================================

$strQuery = fncGetCopyFilePathQuery(DEF_REPORT_ORDER, $aryData["strReportKeyCode"], $aryData["lngReportCode"]);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum > 0) {
    $objResult = $objDB->fetchObject($lngResultID, 0);
    $strReportPathName = $objResult->strreportpathname;
    unset($objResult);
}

$copyDisabled = "visible";

// コピーファイルパスが存在しない または
// 帳票コードが無い または コピーフラグが偽(コピー選択ではない) かつ
// コピー解除権限がある場合、
// コピーマークの非表示
if (!$strReportPathName || (!($aryData["lngReportCode"] || $aryData["bytCopyFlag"]) && fncCheckAuthority(DEF_FUNCTION_LO4, $objAuth))) {
    $copyDisabled = "hidden";
}

///////////////////////////////////////////////////////////////////////////
// 帳票コードが真の場合、ファイルデータを取得
///////////////////////////////////////////////////////////////////////////
if ($aryData["lngReportCode"]) {
    if (!$lngResultNum) {
        fncOutputError(9056, DEF_FATAL, "帳票コピーがありません。", true, "", $objDB);
    }

    if (!$aryHtml[] = file_get_contents(SRC_ROOT . "list/result/cash/" . $strReportPathName . ".tmpl")) {
        fncOutputError(9059, DEF_FATAL, "帳票データファイルが開けませんでした。", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);
}

///////////////////////////////////////////////////////////////////////////
// テンプレートと置き換えデータ取得
///////////////////////////////////////////////////////////////////////////
else {

    // データ取得クエリ
    $strQuery = fncGetListOutputQuery(DEF_REPORT_ORDER, $aryData["strReportKeyCode"], $objDB);

    $objMaster = new clsMaster();
    $objMaster->setMasterTableData($strQuery, $objDB);

    $aryParts = &$objMaster->aryData[0];
    $aryParts["copyDisabled"] = $copyDisabled;

    /////////////////////////////////////////////////////////////////
    // 特殊処理
    /////////////////////////////////////////////////////////////////
    // 海外・国内用宛名処理
    if ($aryParts["lngorganizationcode"] == DEF_ORGANIZATION_FOREIGN) {
        // 宛名
        $aryParts["strcustomerdisplayname"] = "TO: " . $aryParts["strcustomerdisplayname"];

        // 仕入先住所
        $aryParts["strAddress"] = $objMaster->aryData[0]["strcustomeraddress4"] . $objMaster->aryData[0]["strcustomeraddress3"] . "<br>" . $objMaster->aryData[0]["strcustomeraddress2"] . $objMaster->aryData[0]["strcustomeraddress1"];
    } else {
        // 宛名
        $aryParts["strcustomerdisplayname"] .= " 様";

        // 仕入先住所
        $aryParts["strAddress"] = $objMaster->aryData[0]["strcustomeraddress1"] . $objMaster->aryData[0]["strcustomeraddress2"] . "<br>" . $objMaster->aryData[0]["strcustomeraddress3"] . $objMaster->aryData[0]["strcustomeraddress4"];
    }

    // 詳細取得
    $aryQuery[] = "SELECT p.strProductName, p.strProductEnglishName,";
    $aryQuery[] = " p.strProductCode, p.lngCartonQuantity,";
    $aryQuery[] = " od.lngSortKey AS lngOrderDetailNo, od.strNote,";
    $aryQuery[] = " od.lngConversionClassCode,";
    $aryQuery[] = " TO_CHAR( od.lngProductQuantity, '9,999,999,990' ) AS lngProductQuantity,";
    $aryQuery[] = " TO_CHAR( od.dtmDeliveryDate, 'YYYY/MM/DD' ) AS dtmDeliveryDate,";
    $aryQuery[] = " TO_CHAR( od.curProductPrice, '9,999,999,990.9999' ) AS curProductPrice,";
    $aryQuery[] = " TO_CHAR( od.curSubTotalPrice, '9,999,999,990.99' ) AS curSubTotalPrice,";
    $aryQuery[] = " si.strStockItemName, dm.strDeliveryMethodName,";
    $aryQuery[] = " pu.strProductUnitName, od.strMoldNo ";
    $aryQuery[] = "FROM t_OrderDetail od, m_Product p,";
    $aryQuery[] = " m_StockItem si, m_DeliveryMethod dm, m_ProductUnit pu ";
    $aryQuery[] = "WHERE od.lngOrderNo = " . $aryData["strReportKeyCode"];
    $aryQuery[] = " AND od.lngRevisionNo = ";
    $aryQuery[] = "(";
    $aryQuery[] = "  SELECT MAX ( od2.lngRevisionNo )";
    $aryQuery[] = "  FROM t_OrderDetail od2";
    $aryQuery[] = "  WHERE od.lngOrderNo = od2.lngOrderNo";
    $aryQuery[] = "   AND od.lngOrderDetailNo = od2.lngOrderDetailNo";
    $aryQuery[] = ")";
    $aryQuery[] = " AND od.strProductCode = p.strProductCode";
    $aryQuery[] = " AND od.lngStockItemCode = si.lngStockItemCode";
    $aryQuery[] = " AND od.lngStockSubjectCode = si.lngStockSubjectCode";
    $aryQuery[] = " AND od.lngDeliveryMethodCode = dm.lngDeliveryMethodCode";
    $aryQuery[] = " AND od.lngProductUnitCode = pu.lngProductUnitCode ";
    $aryQuery[] = "ORDER BY od.lngSortKey";

    $strQuery = join("", $aryQuery);
    unset($aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum < 1) {
        fncOutputError(9051, DEF_FATAL, "帳票詳細データが存在しませんでした。", true, "", $objDB);
    }

    // フィールド名取得
    for ($i = 0; $i < pg_num_fields($lngResultID); $i++) {
        $aryKeys[] = pg_field_name($lngResultID, $i);
    }

    // 製品コード、製品名、英語製品名取得
    $aryResult = $objDB->fetchArray($lngResultID, 0);
    $aryParts[$aryKeys[2]] = $aryResult[2];
    $aryParts[$aryKeys[0]] = $aryResult[0];
    $aryParts[$aryKeys[1]] = $aryResult[1];

    // 行数だけデータ取得、配列に代入
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult = $objDB->fetchArray($lngResultID, $i);
        for ($j = 3; $j < count($aryKeys); $j++) {
            $aryDetail[$i][$aryKeys[$j] . (($i + 5) % 5)] = $aryResult[$j];
        }
    }
    $objDB->freeResult($lngResultID);

    // 合計金額処理(最後のページだけに表示)別変数に保存
    $curTotalPrice = $aryParts["strmonetaryunitsign"] . " " . $aryParts["curtotalprice"];
    unset($aryParts["curtotalprice"]);

    // ページ処理
    $aryParts["lngNowPage"] = 1;
    $aryParts["lngAllPage"] = ceil($lngResultNum / 5);
    $objDB->close();

    // HTML出力
    $objTemplateHeader = new clsTemplate();
    $objTemplateHeader->getTemplate("list/result/po_header.tmpl");
    $strTemplateHeader = $objTemplateHeader->strTemplate;

    $objTemplateFooter = new clsTemplate();
    $objTemplateFooter->getTemplate("list/result/po_footer.tmpl");
    $strTemplateFooter = $objTemplateFooter->strTemplate;

    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("list/result/po.tmpl");
    $strTemplate = $objTemplate->strTemplate;

    // ページ数分テンプレートを繰り返し読み込み
    for (; $aryParts["lngNowPage"] < ($aryParts["lngAllPage"] + 1); $aryParts["lngNowPage"]++) {
        $objTemplate->strTemplate = $strTemplate;

        // 表示しようとしているページが最後のページの場合、
        // 合計金額を代入(発注書出力特別処理)
        if ($aryParts["lngNowPage"] == $aryParts["lngAllPage"]) {
            $aryParts["curTotalPrice"] = $curTotalPrice;
            $aryParts["strTotalAmount"] = "Total Amount";
        }

        // 置き換え
        $objTemplate->replace($aryParts);

        // 詳細行を５行表示(発注書出力特別処理)
        $lngRecordCount = 0;
        for ($j = ($aryParts["lngNowPage"] - 1) * 5; $j < ($aryParts["lngNowPage"] * 5); $j++) {
            $aryDetail[$j]["record" . $lngRecordCount] = $j + 1;

            // 単価が存在すれば、それに通貨単位をつける
            if ($aryDetail[$j]["curproductprice" . (($j + 5) % 5)] > 0) {
                $aryDetail[$j]["curproductprice" . (($j + 5) % 5)] = $aryParts["strmonetaryunitsign"] . " " . $aryDetail[$j]["curproductprice" . (($j + 5) % 5)];
            }

            // 小計が存在すれば、それに通貨単位をつける
            if ($aryDetail[$j]["cursubtotalprice" . (($j + 5) % 5)] > 0) {
                $aryDetail[$j]["cursubtotalprice" . (($j + 5) % 5)] = $aryParts["strmonetaryunitsign"] . " " . $aryDetail[$j]["cursubtotalprice" . (($j + 5) % 5)];
            }

            // 製品数量が存在すれば、それに製品単位をつける
            if ($aryDetail[$j]["lngproductquantity" . (($j + 5) % 5)] > 0) {
                $aryDetail[$j]["lngproductquantity" . (($j + 5) % 5)] .= "(" . $aryDetail[$j]["strproductunitname" . (($j + 5) % 5)] . ")";
            }

            // カートン入数が存在すれば、それに製品単位をつける
            if ($aryDetail[$j]["lngconversionclasscode" . (($j + 5) % 5)] == 2) {
                $aryDetail[$j]["lngcartonquantity" . (($j + 5) % 5)] = "1(c/t) = " . $aryDetail[$j]["lngcartonquantity" . (($j + 5) % 5)] . "(pcs)";
            } else {
                unset($aryDetail[$j]["lngcartonquantity" . (($j + 5) % 5)]);
            }

            // 金型番号が存在すれば、それに()をつける
            if ($aryDetail[$j]["strmoldno" . (($j + 5) % 5)] != "") {
                $aryDetail[$j]["strmoldno" . (($j + 5) % 5)] = "(" . $aryDetail[$j]["strmoldno" . (($j + 5) % 5)] . ")";
            } else {
                unset($aryDetail[$j]["strmoldno" . (($j + 5) % 5)]);
            }

            $objTemplate->replace($aryDetail[$j]);
            $lngRecordCount++;
        }

        $objTemplate->complete();
        $aryHtml[] = $objTemplate->strTemplate;
    }
}
$strBodyHtml = join("<br style=\"page-break-after:always;\">\n", $aryHtml);

echo $strTemplateHeader . $strBodyHtml . $strTemplateFooter;
