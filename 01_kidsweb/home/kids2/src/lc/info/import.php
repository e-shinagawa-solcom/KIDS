<?php

// 読み込み
include 'conf.inc';
//クラスファイルの読み込み
require_once '../lcModel/db_common.php';
//共通ファイル読み込み
require_once '../lcModel/lcModelCommon.php';
require_once '../lcModel/kidscore_common.php';
require LIB_FILE;
// phpspreadsheetパッケージをインポートする
require PATH_HOME . "/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

$data = $_POST;

$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//経理サブシステムDB接続
$lcModel = new lcModel();

// セッション確認
$objAuth = fncIsSession($data["sessionid"], $objAuth, $objDB);

//ユーザーID取得(半角スペースがあるため)
$usrId = trim($objAuth->UserID);

//ログイン状況の最大管理番号の取得
$lgno = $lcModel->getMaxLoginStateNum();

$strTmpFileName = "";

if ($_FILES) {
    // テンポラリファイル作成、ファイル名取得
    $strTmpFileName = getTempFileName($_FILES['txtfile']['tmp_name']);
}

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();

// 区切り文字
$reader->setDelimiter("\r\n");
$spreadsheet = $reader->load(FILE_UPLOAD_TMPDIR . $strTmpFileName);
$sheetData = $spreadsheet->getActiveSheet()->toArray();
$csvData = [];
for ($i = 0; $i <= count($sheetData); $i++) {
    $cells = explode("	", $sheetData[$i][0]);
    for ($j = 0; $j < count($cells); $j++) {
        $csvData[$i][$j+1] = rtrim(ltrim($cells[$j], '"'), '"');
    }
}
// DB処理開始
$objDB->transactionBegin();
// t_lcinfテーブルよりデータを削除する
fncDeleteLcInfo($objDB);
// ファイルより読み込んだデータをt_lcinfoに登録する
$lccount = count($csvData);
if ($lccount >= 2) {
    for ($i = 1; $i < $lccount-1; $i++) {
        $data["payfnameomit"] = $csvData[$i][1];
        $data["opendate"] = $csvData[$i][2];
        $data["portplace"] = $csvData[$i][3];
        $data["pono"] = $csvData[$i][4];
        $data["polineno"] = $csvData[$i][5];
        $data["poreviseno"] = $csvData[$i][6];
        $data["postate"] = $csvData[$i][7];
        $data["payfcd"] = $csvData[$i][8];
        $data["productcd"] = $csvData[$i][9];
        $data["productrevisecd"] = $csvData[$i][10];
        $data["productname"] = $csvData[$i][11];
        $data["productnumber"] = $csvData[$i][12];
        $data["unitname"] = $csvData[$i][13];
        $data["unitprice"] = $csvData[$i][14];
        $data["moneyprice"] = $csvData[$i][15];
        $data["shipstartdate"] = $csvData[$i][16];
        $data["shipenddate"] = $csvData[$i][17];
        $data["sumdate"] = $csvData[$i][18];
        $data["poupdatedate"] = $csvData[$i][19];
        $data["deliveryplace"] = $csvData[$i][20];
        $data["currencyclass"] = $csvData[$i][21];
        $data["lcnote"] = $csvData[$i][22];
        $data["shipterm"] = $csvData[$i][23];
        $data["validterm"] = $csvData[$i][24];
        $data["bankname"] = $csvData[$i][25];
        $data["bankreqdate"] = ($csvData[$i][26] == "") ? null : $csvData[$i][26];
        $data["lcno"] = $csvData[$i][27];
        $data["lcamopen"] = ($csvData[$i][28] == "") ? null : $csvData[$i][28];
        $data["validmonth"] = ($csvData[$i][29] == "") ? null : $csvData[$i][29];
        $data["usancesettlement"] = ($csvData[$i][30] == "") ? null : $csvData[$i][30];
        $data["bldetail1date"] = ($csvData[$i][31] == "") ? null : $csvData[$i][31];
        $data["bldetail1money"] = ($csvData[$i][32] == "") ? null : $csvData[$i][32];
        $data["bldetail2date"] = ($csvData[$i][33] == "") ? null : $csvData[$i][33];
        $data["bldetail2money"] = ($csvData[$i][34] == "") ? null : $csvData[$i][34];
        $data["bldetail3date"] = ($csvData[$i][35] == "") ? null : $csvData[$i][35];
        $data["bldetail3money"] = ($csvData[$i][36] == "") ? null : $csvData[$i][36];
        $data["payfnameformal"] = $csvData[$i][37];
        $data["productnamee"] = $csvData[$i][38];
        $data["lcstate"] = $csvData[$i][39];
        $data["bankcd"] = $csvData[$i][40];
        $data["shipym"] = $csvData[$i][41];
        fncInsertLcInfo($objDB, $data);
    }
    $lcModel->updateLcImpDate($lgno, date("YmdHi"));
}
$objDB->transactionCommit();
$objDB->close();
$lcModel->close();
echo "success";
return;
?>
