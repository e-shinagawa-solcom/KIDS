<?
/**
 *    帳票出力 納品書 印刷プレビュー画面
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
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
if (!fncCheckAuthority(DEF_FUNCTION_LO0, $objAuth) || !fncCheckAuthority(DEF_FUNCTION_SO0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 帳票出力コピーファイルパス取得クエリ生成
//===================================================================

$strQuery = fncGetCopyFilePathQuery(DEF_REPORT_SLIP, $aryData["strReportKeyCode"], $aryData["lngReportCode"]);

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
if (!$strReportPathName || (!($aryData["lngReportCode"] || $aryData["bytCopyFlag"]) && fncCheckAuthority(DEF_FUNCTION_LO6, $objAuth))) {
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
    $strQuery = fncGetListOutputQuery(DEF_REPORT_SLIP, $aryData["strReportKeyCode"], $objDB);

    $objMaster = new clsMaster();
    $objMaster->setMasterTableData($strQuery, $objDB);
    $aryParts = &$objMaster->aryData[0];

    $aryParts["copyDisabled"] = $copyDisabled;

    // 詳細取得
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
    $aryQuery[] = "  , strnote";
    $aryQuery[] = "  , lngreceiveno";
    $aryQuery[] = "  , lngreceivedetailno";
    $aryQuery[] = "  , lngreceiverevisionno";
    $aryQuery[] = "  , lngsortkey ";
    $aryQuery[] = "from";
    $aryQuery[] = "  t_slipdetail ";
    $aryQuery[] = "where";
    $aryQuery[] = "  lngslipno = " . $aryData["strReportKeyCode"];
    $aryQuery[] = " ORDER BY";
    $aryQuery[] = "  lngSortKey";

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

    $rowNum = $aryParts["lngmaxline"];
    // テンプレートパス設定
    if ($aryParts["lngslipkindcode"] == DEF_SLIP_KIND_EXCLUSIVE) {
        $strTemplateHeaderPath = "list/result/slip_exc_header.html";
        $strTemplatePath = "list/result/slip_exc.html";
        $strTemplateFooterPath = "list/result/slip_exc_footer.html";
    } else if ($aryParts["lngslipkindcode"] == DEF_SLIP_KIND_COMM) {
        $strTemplateHeaderPath = "list/result/slip_comm_header.html";
        $strTemplatePath = "list/result/slip_comm.html";
        $strTemplateFooterPath = "list/result/slip_comm_footer.html";
    } else if ($aryParts["lngslipkindcode"] == DEF_SLIP_KIND_DEBIT) {
        $strTemplateHeaderPath = "list/result/slip_debit_header.html";
        $strTemplatePath = "list/result/slip_debit.html";
        $strTemplateFooterPath = "list/result/slip_debit_footer.html";
    }

    // 行数だけデータ取得、配列に代入
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult = $objDB->fetchArray($lngResultID, $i);
        for ($j = 3; $j < count($aryKeys); $j++) {
            $aryDetail[$i][$aryKeys[$j] . (($i + $rowNum) % $rowNum)] = $aryResult[$j];
        }
    }
    $objDB->freeResult($lngResultID);

    // 合計金額処理(最後のページだけに表示)別変数に保存
    $curTotalPrice = ($aryParts["lngmonetaryunitcode"] == 1 ? "&yen; " : $aryParts["strmonetaryunitsign"] )  . " " . $aryParts["curtotalprice"];
    unset($aryParts["curtotalprice"]);

    // ページ処理
    $aryParts["lngNowPage"] = 1;
    $aryParts["lngAllPage"] = ceil($lngResultNum / $rowNum);
    $objDB->close();

    // 顧客電話番号
    $aryParts["strcustomertel"] = "Tel:" .$aryParts["strcustomertel1"] . " " . $aryParts["strcustomertel2"];

    // 顧客FAX番号
    $aryParts["strcustomerfax"] = "Fax.:" .$aryParts["strcustomerfax1"] . " " . $aryParts["strcustomerfax2"];

    // HTML出力
    $objTemplateHeader = new clsTemplate();
    $objTemplateHeader->getTemplate($strTemplateHeaderPath);
    $strTemplateHeader = $objTemplateHeader->strTemplate;

    $objTemplateFooter = new clsTemplate();
    $objTemplateFooter->getTemplate($strTemplateFooterPath);
    $strTemplateFooter = $objTemplateFooter->strTemplate;

    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate($strTemplatePath);
    $strTemplate = $objTemplate->strTemplate;

    // ページ数分テンプレートを繰り返し読み込み
    for (; $aryParts["lngNowPage"] < ($aryParts["lngAllPage"] + 1); $aryParts["lngNowPage"]++) {
        $objTemplate->strTemplate = $strTemplate;

        // 表示しようとしているページが最後のページの場合、
        // 合計金額を代入(発注書出力特別処理)
        if ($aryParts["lngNowPage"] == $aryParts["lngAllPage"]) {
            $aryParts["curTotalPrice"] = $curTotalPrice;
            $aryParts["strTotalAmount"] = "Total Amount :";
        }

        // 置き換え
        $objTemplate->replace($aryParts);

        // 詳細行を５行表示(発注書出力特別処理)
        $lngRecordCount = 0;
        for ($j = ($aryParts["lngNowPage"] - 1) * $rowNum; $j < ($aryParts["lngNowPage"] * $rowNum); $j++) {
            $aryDetail[$j]["record" . $lngRecordCount] = $j + 1;
            $index = ($j + $rowNum) % $rowNum;

            // 単価が存在すれば、それに通貨単位をつける
            if ($aryDetail[$j]["curproductprice" . ($index)] > 0) {
                $aryDetail[$j]["curproductprice" . ($index)] = $aryDetail[$j]["curproductprice" . ($index)];
            }

            // 小計が存在すれば、それに通貨単位をつける
            if ($aryDetail[$j]["cursubtotalprice" . ($index)] > 0) {
                $aryDetail[$j]["cursubtotalprice" . ($index)] = $aryDetail[$j]["cursubtotalprice" . ($index)];
            }

            // 製品数量が存在すれば、それに製品単位をつける
            if ($aryDetail[$j]["lngproductquantity" . ($index)] > 0) {
                $aryDetail[$j]["lngproductquantity" . ($index)] .= "(" . $aryDetail[$j]["strproductunitname" . ($index)] . ")";
            }

            // 入数
            $aryDetail[$j]["lngquantity" . ($index)] = "";

            $objTemplate->replace($aryDetail[$j]);
            $lngRecordCount++;
        }

        $objTemplate->complete();
        $aryHtml[] = $objTemplate->strTemplate;
    }
}
$strBodyHtml = join("<br style=\"page-break-after:always;\">\n", $aryHtml);

echo $strTemplateHeader . $strBodyHtml . $strTemplateFooter;
