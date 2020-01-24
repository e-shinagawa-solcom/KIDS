<?
/**
 *    帳票出力 納品書 印刷完了画面
 *
 */
// 印刷プレビュー画面( * は指定帳票のファイル名 )
// listoutput.php -> strSessionID       -> action.php
// listoutput.php -> strReportKeyCode   -> action.php
// listoutput.php -> lngReportCode      -> action.php

// 設定読み込み
include_once 'conf.inc';

// ライブラリ読み込み
require LIB_FILE;
require SRC_ROOT . "/list/cmn/lib_lo.php";
require SRC_ROOT . "m/cmn/lib_m.php";
require LIB_DEBUGFILE;

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
$aryCheck["strReportKeyCode"] = "null:number(0,99999999)";
$strTemplateFile = "p";

$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 権限確認
if (!fncCheckAuthority(DEF_FUNCTION_LO0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 指定キーコードの帳票データを取得
$strQuery = fncGetCopyFilePathQuery(DEF_REPORT_SLIP, $aryData["strReportKeyCode"], $aryData["lngReportCode"]);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
var_dump($lngResultNum);
if ($lngResultNum === 1) {
    $objResult = $objDB->fetchObject($lngResultID, 0);
    $strListOutputPath = $objResult->strreportpathname;
    unset($objResult);
    $objDB->freeResult($lngResultID);
}
// データ取得クエリ
$strQuery = fncGetListOutputQuery(DEF_REPORT_SLIP, $aryData["strReportKeyCode"], $objDB);

$objMaster = new clsMaster();
$objMaster->setMasterTableData($strQuery, $objDB);

$aryParts = &$objMaster->aryData[0];
if ($lngResultNum === 1) {
    // 印刷回数を更新する
    fncUpdatePrintCount(DEF_REPORT_SLIP, $aryParts, $objDB);
}
// 帳票が存在しない場合、コピー帳票ファイルを生成、保存
elseif ($lngResultNum === 0) {

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

    // 行数だけデータ取得、配列に代入
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult = $objDB->fetchArray($lngResultID, $i);
        for ($j = 0; $j < count($aryKeys); $j++) {
            $aryDetail[$i][$aryKeys[$j] . $i] = $aryResult[$j];
        }
    }
    $objDB->freeResult($lngResultID);

    $rowNum = $slipKidObj["lngmaxline"];

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

        // 合計金額
        $curTotalPrice = ($aryParts["lngmonetaryunitcode"] == 1 ? "&yen; " : $aryParts["strmonetaryunitsign"]) . " " . $aryParts["curtotalprice"];

        $aryParts["curtotalprice"] = $curTotalPrice;
        $aryParts["nameofbank"] = $aryParts["lngpaymentmethodcode"] == 1 ? "MUFG BANK, LTD." : "";
        $aryParts["nameofbranch"] = $aryParts["lngpaymentmethodcode"] == 1 ? "ASAKUSA BRANCH" : "";
        $aryParts["addressofbank1"] = $aryParts["lngpaymentmethodcode"] == 1 ? "4-2, ASAKUSA 1-CHOME, " : "";
        $aryParts["addressofbank2"] = $aryParts["lngpaymentmethodcode"] == 1 ? " TAITO-KU, TOKYO 111-0032, JAPAN" : "";
        $aryParts["swiftcode"] = $aryParts["lngpaymentmethodcode"] == 1 ? "BOTKJPJT" : "";
        $aryParts["accountname"] = $aryParts["lngpaymentmethodcode"] == 1 ? "KUWAGATA CO.,LTD." : "";
        $aryParts["accountno"] = $aryParts["lngpaymentmethodcode"] == 1 ? "1063143" : "";
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
        } else if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_DEBIT) {
            // 顧客受注番号
            if ($aryDetail[$i]["strcustomersalescode" . ($i)] != "") {
                $aryDetail[$i]["strcustomersalescode" . ($i)] = "(PO No:" . $aryDetail[$i]["strcustomersalescode" . ($i)] . ")";
            }
        }

        // 置き換え
        $objTemplate->replace($aryDetail[$i]);
    }

    $objTemplate->complete();
    $aryHtml[] = $objTemplate->strTemplate;

    $strBodyHtml = join("<br style=\"page-break-after:always;\">\n", $aryHtml);

    $strHtml = $strTemplateHeader . $strBodyHtml . $strTemplateFooter;

    $objDB->transactionBegin();

    // シーケンス発行
    $lngSequence = fncGetSequence("t_Report.lngReportCode", $objDB);

    // 帳票テーブルにINSERT
    $strQuery = "INSERT INTO t_Report VALUES ( $lngSequence, " . DEF_REPORT_SLIP . ", " . $aryParts["lngslipno"] . ", '', '$lngSequence' )";

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    $objDB->freeResult($lngResultID);

    // 印刷回数の設定
    fncUpdatePrintCount(DEF_REPORT_SLIP, $aryParts, $objDB);

    // 帳票ファイルオープン
    if (!$fp = fopen(SRC_ROOT . "list/result/cash/" . $lngSequence . ".tmpl", "w")) {
        list($lngResultID, $lngResultNum) = fncQuery("ROLLBACK", $objDB);
        fncOutputError(9059, DEF_FATAL, "帳票ファイルのオープンに失敗しました。", true, "", $objDB);
    }

    // 帳票ファイルへの書き込み
    if (!fwrite($fp, $strHtml)) {
        list($lngResultID, $lngResultNum) = fncQuery("ROLLBACK", $objDB);
        fncOutputError(9059, DEF_FATAL, "帳票ファイルの書き込みに失敗しました。", true, "", $objDB);
    }

    $objDB->transactionCommit();
}

$objDB->close();

header("location: /list/result/slip/download.php?strSessionID=" . $aryData["strSessionID"]
    . "&strReportKeyCode=" . $aryData["strReportKeyCode"]
    . "&lngReportCode=" . $aryData["lngReportCode"]
    . "&reprintFlag=" . $aryData["reprintFlag"]);

return true;
