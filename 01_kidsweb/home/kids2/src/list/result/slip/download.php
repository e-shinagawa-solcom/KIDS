<?php

// 読み込み
include 'conf.inc';
require LIB_FILE;
require SRC_ROOT . "list/cmn/lib_lo.php";
require SRC_ROOT . "m/cmn/lib_m.php";
require PATH_HOME . "/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;

$aryData = $_GET;

$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

// データ取得クエリ
$strQuery = fncGetSlipForDownloadQuery($aryData["strReportKeyCode"]);
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
$strQuery = fncGetSlipDetailForDownloadQuery($aryData["strReportKeyCode"], $aryParts["lngrevisionno"]);
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
if ($lngResultNum < 1) {
    fncOutputError(9051, DEF_FATAL, "帳票詳細データが存在しませんでした。", true, "", $objDB);
} else {
    // フィールド名取得
    for ($i = 0; $i < pg_num_fields($lngResultID); $i++) {
        $aryKeys[] = pg_field_name($lngResultID, $i);
    }

    // 行数だけデータ取得、配列に代入
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult = $objDB->fetchArray($lngResultID, $i);
        for ($j = 0; $j < count($aryKeys); $j++) {
            $aryDetail[$i][$aryKeys[$j]] = $aryResult[$j];
        }
    }
}

$objDB->close();

// テンプレートパス設定
if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_EXCLUSIVE) {
    $strTemplatePath = REPORT_TMPDIR . REPORT_SLIP_EXCLUSIVE;
    // $downloadFileName = REPORT_SLIP_EXCLUSIVE;
} else if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_COMM) {
    $strTemplatePath = REPORT_TMPDIR . REPORT_SLIP_COMM;
    // $downloadFileName = REPORT_SLIP_COMM;
} else if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_DEBIT) {
    $strTemplatePath = REPORT_TMPDIR . REPORT_SLIP_DEBIT;
    // $downloadFileName = REPORT_SLIP_DEBIT;
}

// 帳票テンプレートファイルの読込
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($strTemplatePath);
// データ設定用シートにデータの設定
$worksheet = $spreadsheet->getSheetByName("データ設定用");
$worksheet->fromArray($aryParts, null, 'B3');
$worksheet->fromArray($aryDetail, null, 'B6');

if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_DEBIT) {
    //ロードしたシートの中から"売上明細"シートを$sheetとする
    $sheet = $spreadsheet->getSheetByName("DEBIT NOTE");
    //画像の貼り付け
    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing->setPath("rogo_slip.gif");
    $drawing->setCoordinates('A2'); //貼り付け場所
    $drawing->setResizeProportional(false); // リサイズ時に縦横比率を固定する (false = 固定しない)
    $drawing->setWidth(143); // 画像の幅 (px)
    $drawing->setHeight(99); // 画像の高さ (px)
    $drawing->setWorksheet($sheet); //対象シート（インスタンスを指定）

    $drawing1 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing1->setPath("title1.gif");
    $drawing1->setHeight(25); //高さpx
    $drawing1->setOffsetY(5); // 位置をずらす
    $drawing1->setCoordinates('D1'); //貼り付け場所
    $drawing1->setWorksheet($sheet); //対象シート（インスタンスを指定）

    $drawing2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing2->setPath("title2.gif");
    $drawing2->setHeight(60); //高さpx
    $drawing2->setWidth(400); // 画像の幅 (px)
    $drawing2->setCoordinates('D3'); //貼り付け場所
    $drawing2->setWorksheet($sheet); //対象シート（インスタンスを指定）

    $drawing3 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing3->setPath("brackets_left.gif");
    $drawing3->setHeight(100); //高さpx
    $drawing3->setOffsetX(20); // 位置をずらす
    $drawing3->setCoordinates('A9'); //貼り付け場所
    $drawing3->setWorksheet($sheet); //対象シート（インスタンスを指定）

    $drawing4 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing4->setPath("brackets_right.gif");
    $drawing4->setHeight(100); //高さpx
    $drawing4->setCoordinates('F9'); //貼り付け場所
    $drawing4->setWorksheet($sheet); //対象シート（インスタンスを指定）
} else {
    // 再印刷フラグの設定
    $worksheet->getCell('AK3')->setValue($aryData["reprintFlag"]);
}
// 成功
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="01simple.xls"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
