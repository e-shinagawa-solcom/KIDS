<?
/**
 *    帳票出力 納品書 印刷完了画面
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
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

if ($lngResultNum === 1) {
    $objResult = $objDB->fetchObject($lngResultID, 0);
    $strListOutputPath = $objResult->strreportpathname;
    unset($objResult);
    $objDB->freeResult($lngResultID);
}

// 帳票が存在しない場合、コピー帳票ファイルを生成、保存
elseif ($lngResultNum === 0) {
    // データ取得クエリ
    $strQuery = fncGetListOutputQuery(DEF_REPORT_SLIP, $aryData["strReportKeyCode"], $objDB);

    $objMaster = new clsMaster();
    $objMaster->setMasterTableData($strQuery, $objDB);

    $aryParts = &$objMaster->aryData[0];

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
    $curTotalPrice = $aryParts["strmonetaryunitsign"] . " " . $aryParts["curtotalprice"];
    unset($aryParts["curtotalprice"]);

    // ページ処理
    $aryParts["lngNowPage"] = 1;
    $aryParts["lngAllPage"] = ceil($lngResultNum / $rowNum);
    $objDB->close();

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
            $aryParts["strTotalAmount"] = "Total Amount";
        }

        // 置き換え
        $objTemplate->replace($aryParts);

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

    $strBodyHtml = join("<br style=\"page-break-after:always;\">\n", $aryHtml);

    $strHtml = $strTemplateHeader . $strBodyHtml . $strTemplateFooter;

    $objDB->transactionBegin();

    // シーケンス発行
    $lngSequence = fncGetSequence("t_Report.lngReportCode", $objDB);

    // 帳票テーブルにINSERT
    $strQuery = "INSERT INTO t_Report VALUES ( $lngSequence, " . DEF_REPORT_SLIP . ", " . $aryParts["lngorderno"] . ", '', '$lngSequence' )";

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

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
echo "<script language=javascript>parent.window.close();</script>";

$objDB->close();

return true;
