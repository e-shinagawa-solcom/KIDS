<?
/**
 *    帳票出力 納品書 印刷プレビュー画面
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
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
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
// データ取得クエリ
$strQuery = fncGetListOutputQuery(DEF_REPORT_SLIP, $aryData["strReportKeyCode"], $objDB);

$objMaster = new clsMaster();
$objMaster->setMasterTableData($strQuery, $objDB);
$aryParts = &$objMaster->aryData[0];

// 納品伝票種別取得
$strQuery = fncGetSlipKindQuery($aryParts["lngcustomercode"]);
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
if ($lngResultNum < 1) {
    fncOutputError(9051, DEF_FATAL, "納品伝票種別データが存在しませんでした。", true, "", $objDB);
} else {
    $slipKidObj = $objDB->fetchArray($lngResultID, 0);
}

$objDB->freeResult($lngResultID);

unset($aryQuery);

// 詳細取得
$strQuery = fncGetSlipDetailQuery($aryData["strReportKeyCode"], $aryParts["lngrevisionno"]);
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
if ($lngResultNum < 1) {
    fncOutputError(9051, DEF_FATAL, "帳票詳細データが存在しませんでした。", true, "", $objDB);
}

// フィールド名取得
for ($i = 0; $i < pg_num_fields($lngResultID); $i++) {
    $aryKeys[] = pg_field_name($lngResultID, $i);
}

$maxLine = $slipKidObj["lngmaxline"];
$count = $lngResultNum;
// 行数だけデータ取得、配列に代入
for ($i = 0; $i < $count; $i++) {
    $aryResult = $objDB->fetchArray($lngResultID, $i);
    for ($j = 0; $j < count($aryKeys); $j++) {
        $aryDetail[$i][$aryKeys[$j] . (($i + $maxLine) % $maxLine)] = $aryResult[$j];
    }
}
$objDB->freeResult($lngResultID);

$objDB->close();

// テンプレートパス設定
if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_EXCLUSIVE) {
    $strTemplateHeaderPath = "list/result/slip_exc_header.html";
    $strTemplatePath = "list/result/slip_exc.html";
    $strTemplateFooterPath = "list/result/slip_exc_footer.html";
} else if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_COMM) {
    $strTemplateHeaderPath = "list/result/slip_comm_header.html";
    $strTemplatePath = "list/result/slip_comm.html";
    $strTemplateFooterPath = "list/result/slip_comm_footer.html";
} else if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_DEBIT) {
    $strTemplateHeaderPath = "list/result/slip_debit_header.html";
    $strTemplatePath = "list/result/slip_debit.html";
    $strTemplateFooterPath = "list/result/slip_debit_footer.html";
}

// 市販の場合
if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_COMM) {
    // 消費税設定
    $lngtaxclasscode = $aryParts["lngtaxclasscode"];
    $curtotalprice = $aryParts["curtotalprice_comm"];
    $curtax = $aryParts["curtax"];
    $curtaxprice = $lngtaxclasscode != 1 ? 0 : ($lngtaxclasscode = 1 ? ($curtotalprice * $curtax) : ($curtotalprice / (1 + $curtax) * $curtax));
    $aryParts["curtaxprice"] = round($curtaxprice);

    // 合計金額
    $curtotalprice = str_pad(round($curtotalprice), 8, " ", STR_PAD_LEFT);
    for ($k = 0; $k < 8; $k++) {
        $aryParts["curtotalprice" . $k] = substr($curtotalprice, $k, 1);
    }

    // 税込金額
    $curprice = $curtotalprice + $curtaxprice;
    $curprice = str_pad(round($curprice), 8, " ", STR_PAD_LEFT);
    $len = strlen($curprice);
    for ($k = 0; $k < 8; $k++) {
        $aryParts["curprice" . $k] = substr($curprice, $k, 1);
    }

    // DEBIT　NOTEの場合
} else if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_DEBIT) {

    // 顧客電話番号
    $aryParts["strcustomertel"] = "Tel:" . $aryParts["strcustomerphoneno"];

    // 顧客FAX番号
    $aryParts["strcustomerfax"] = "Fax.:" . $aryParts["strcustomerfaxno"];
    $aryparts["strcustomerusername"] = $aryParts["strcustomerusername"];
    // 合計金額
    $curTotalPrice = ($aryParts["lngmonetaryunitcode"] == 1 ? "&yen; " : $aryParts["strmonetaryunitsign"]) . " " . $aryParts["curtotalprice_us"];

    // $aryParts["curtotalprice"] = $curTotalPrice;
    $aryParts["nameofbank"] = $aryParts["lngpaymentmethodcode"] == 1 ? "MUFG BANK, LTD." : "";
    $aryParts["nameofbranch"] = $aryParts["lngpaymentmethodcode"] == 1 ? "ASAKUSA BRANCH" : "";
    $aryParts["addressofbank1"] = $aryParts["lngpaymentmethodcode"] == 1 ? "4-2, ASAKUSA 1-CHOME, " : "";
    $aryParts["addressofbank2"] = $aryParts["lngpaymentmethodcode"] == 1 ? " TAITO-KU, TOKYO 111-0032, JAPAN" : "";
    $aryParts["swiftcode"] = $aryParts["lngpaymentmethodcode"] == 1 ? "BOTKJPJT" : "";
    $aryParts["accountname"] = $aryParts["lngpaymentmethodcode"] == 1 ? "KUWAGATA CO.,LTD." : "";
    $aryParts["accountno"] = $aryParts["lngpaymentmethodcode"] == 1 ? "1063143" : "";
    $aryParts["dtmpaymentlimit"] = $aryParts["lngpaymentmethodcode"] == 1 ? ("on " . $aryParts["dtmpaymentlimit"]) : "";
} else if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_EXCLUSIVE) {
    $strnote = "";
    $strsalesclassname = $aryDetail[0]["strsalesclassname0"];
    if (strlen($strsalesclassname) > 0) {
        $strnote = $strnote . $strsalesclassname . "分　";
    }
    if ($aryParts["strtaxclassname"] != "非課税") {
        $strnote = $strnote . $aryParts["strtaxclassname"] . "(" . ($aryParts["curtax"] * 100) . "%)" . "\n" . $aryParts["strnote"];
    }

    $aryParts["strnote"] = $strnote;
}

// HTML出力
$objTemplateHeader = new clsTemplate();
$objTemplateHeader->getTemplate($strTemplateHeaderPath);
$objTemplateHeader->replace($aryData);
$objTemplateHeader->complete();
$strTemplateHeader = $objTemplateHeader->strTemplate;

$objTemplateFooter = new clsTemplate();
$objTemplateFooter->getTemplate($strTemplateFooterPath);
$strTemplateFooter = $objTemplateFooter->strTemplate;

$objTemplate = new clsTemplate();
$objTemplate->getTemplate($strTemplatePath);
$strTemplate = $objTemplate->strTemplate;

if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_DEBIT) {
// ページ処理
    $aryParts["lngNowPage"] = 1;
    $aryParts["lngAllPage"] = ceil($lngResultNum / $maxLine);
// ページ数分テンプレートを繰り返し読み込み
    for (; $aryParts["lngNowPage"] < ($aryParts["lngAllPage"] + 1); $aryParts["lngNowPage"]++) {
        $lngRecordCount = 0;
        $aryHtml[] = "<div style=\"page-break-after:always;page-break-inside: avoid;\">\n";

        // 表示しようとしているページが最後のページの場合、
        // 合計金額を代入(発注書出力特別処理)
        if ($aryParts["lngNowPage"] == $aryParts["lngAllPage"]) {
            $aryParts["curtotalprice"] = $curTotalPrice;
            $aryParts["strTotalAmount"] = "Total Amount";
        } else {
            $aryParts["curtotalprice"] = "";
        }

        $objTemplate->strTemplate = $strTemplate;
        // 置き換え
        $objTemplate->replace($aryParts);
        for ($j = ($aryParts["lngNowPage"] - 1) * $maxLine; $j < ($aryParts["lngNowPage"] * $maxLine); $j++) {
            if ($j > ($count - 1)) {
                break;
            }
            $index = ($j + $maxLine) % $maxLine;
            // 顧客受注番号
            if ($aryDetail[$j]["strcustomersalescode" . ($index)] != "") {
                $aryDetail[$j]["strcustomersalescode" . ($index)] = "(PO No:" . $aryDetail[$j]["strcustomersalescode" . ($index)] . ")";
            }

            // 置き換え
            $objTemplate->replace($aryDetail[$j]);
        }

        $objTemplate->complete();
        $aryHtml[] = $objTemplate->strTemplate;
        $aryHtml[] = "</div>";

    }
} else {
    $objTemplate->strTemplate = $strTemplate;
    // 置き換え
    $objTemplate->replace($aryParts);

    for ($i = 0; $i < $lngResultNum; $i++) {
        // 合計金額
        if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_COMM) {
            // 金額設定
            $cursubtotalprice = str_pad($aryDetail[$i]["cursubtotalprice_comm" . ($i)], 8, " ", STR_PAD_LEFT);
            for ($k = 0; $k < 8; $k++) {
                $aryDetail[$i]["cursubtotalprice" . $i . $k] = substr($cursubtotalprice, $k, 1);
            }
            // 入数
            $aryDetail[$i]["lngquantity" . ($i)] = "";
        } else if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_EXCLUSIVE) {
            $aryDetail[$i]["cursubtotalprice" . ($i)] = $aryDetail[$i]["cursubtotalprice_jp" . ($i)];
        }

        $aryDetail[$i]["curproductprice" . ($i)] = $aryDetail[$i]["curproductprice_jp" . ($i)];

        // 置き換え
        $objTemplate->replace($aryDetail[$i]);
    }

    $objTemplate->complete();
    $aryHtml[] = $objTemplate->strTemplate;
}
$strBodyHtml = join("", $aryHtml);

echo $strTemplateHeader . $strBodyHtml . $strTemplateFooter;
