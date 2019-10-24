<?
/**
 *    帳票出力 請求書 印刷完了画面
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
$strQuery = fncGetCopyFilePathQuery(DEF_REPORT_INV, $aryData["strReportKeyCode"], $aryData["lngReportCode"]);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum === 1) {
    $objResult = $objDB->fetchObject($lngResultID, 0);
    $strListOutputPath = $objResult->strreportpathname;
    unset($objResult);
    $objDB->freeResult($lngResultID);
}

// 帳票が存在しない場合、コピー帳票ファイルを生成、保存
elseif ($lngResultNum === 0) {
    // データ取得クエリ
    $strQuery = fncGetListOutputQuery(DEF_REPORT_INV, $aryData["strReportKeyCode"], $objDB);

    $objMaster = new clsMaster();
    $objMaster->setMasterTableData($strQuery, $objDB);

    $aryParts = &$objMaster->aryData[0];

    unset($aryQuery);

    // 詳細取得
    $strQuery = fncGetInvDetailQuery($aryData["strReportKeyCode"], $aryParts["lngrevisionno"]);
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

    $objDB->close();

    // HTML出力
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("list/result/inv.html");
    $aryParts["totalprice_unitsign"] = ($aryParts["lngmonetaryunitcode"] == 1 ? "&yen; " : $aryParts["strmonetaryunitsign"]) . " " . $aryParts["totalprice"];
    $aryParts["dtminvoicedate"] = convert_jpdt($aryParts["dtminvoicedate"], '年m月');
    $aryParts["dtminsertdate"] = convert_jpdt($aryParts["dtminsertdate"],'.m.d',false);
    // 置き換え
    $objTemplate->replace($aryParts);
    $objTemplate->replace($aryDetail);
    $objTemplate->complete();   

	$strHtml = $objTemplate->strTemplate;

    $objDB->transactionBegin();

    // シーケンス発行
    $lngSequence = fncGetSequence("t_Report.lngReportCode", $objDB);

    // 帳票テーブルにINSERT
    $strQuery = "INSERT INTO t_Report VALUES ( $lngSequence, " . DEF_REPORT_SLIP . ", " . $aryParts["lngslipno"] . ", '', '$lngSequence' )";

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    
    $objDB->freeResult($lngResultID);

    // 印刷回数の設定
    $aryParts["lngprintcount"] += 1;

    // 印刷回数の更新    
    $strQuery = "update m_invoice set lngprintcount = ".$aryParts["lngprintcount"] ." where lnginvoiceno = " .$aryParts["lnginvoiceno"] . " and lngrevisionno = " .$aryParts["lngrevisionno"];
    
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    
    $objDB->freeResult($lngResultID);

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
