<?php

// 読み込み
include 'conf.inc';
//クラスファイルの読み込み
require_once 'db_common.php';
//共通ファイル読み込み
require_once './lcModelCommon.php';
//DB接続ファイルの読み込み
require_once './db_common.php';
require_once './kidscore_common.php';
require LIB_FILE;
//PHP標準のJSON変換メソッドはエラーになるので外部のライブラリ(恐らくエンコードの問題)
require_once 'JSON.php';
// phpspreadsheetパッケージをインポートする
require PATH_HOME . "/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Reader\Csv;

$data = $_POST;

$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//経理サブシステムDB接続
$lcModel = new lcModel();

//JSONクラスインスタンス化
$s = new Services_JSON();

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

    // // ファイル情報の取得
    // $data["txt_name"] = $_FILES['txtfile']['name'];
    // $data["txt_type"] = $_FILES['txtfile']['type'];
    // $data["txt_tmp_name"] = FILE_UPLOAD_TMPDIR . $strTmpFileName;
    // $data["txt_error"] = $_FILES['txtfile']['error'];
    // $data["txt_size"] = $_FILES['txtfile']['size'];
}

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
$reader->setInputEncoding('sjis');
$spreadsheet = $reader->load(FILE_UPLOAD_TMPDIR . $strTmpFileName);
$sheetData = $spreadsheet->getActiveSheet()->toArray();

// t_lcinfテーブルよりデータを削除する
fncDeleteLcInfo($objDB);

// ファイルより読み込んだデータをt_lcinfoに登録する
$lccount = count($sheetData);
if ($lccount > 1) {
    for ($i = 1; $i < lccount; $i++) {
        $data["payfnameomit"] = $sheetData[$i][0];
        $data["opendate"] = $sheetData[$i][1];
        $data["portplace"] = $sheetData[$i][2];
        $data["pono"] = $sheetData[$i][3];
        $data["polineno"] = $sheetData[$i][4];
        $data["poreviseno"] = $sheetData[$i][5];
        $data["postate"] = $sheetData[$i][6];
        $data["payfcd"] = $sheetData[$i][7];
        $data["productcd"] = $sheetData[$i][8];
        $data["productrevisecd"] = $sheetData[$i][9];
        $data["productname"] = $sheetData[$i][10];
        $data["productnumber"] = $sheetData[$i][11];
        $data["unitname"] = $sheetData[$i][12];
        $data["unitprice"] = $sheetData[$i][13];
        $data["moneyprice"] = $sheetData[$i][14];
        $data["shipstartdate"] = $sheetData[$i][15];
        $data["shipenddate"] = $sheetData[$i][16];
        $data["sumdate"] = $sheetData[$i][17];
        $data["poupdatedate"] = $sheetData[$i][18];
        $data["deliveryplace"] = $sheetData[$i][19];
        $data["currencyclass"] = $sheetData[$i][20];
        $data["lcnote"] = $sheetData[$i][21];
        $data["shipterm"] = $sheetData[$i][22];
        $data["validterm"] = $sheetData[$i][23];
        $data["bankname"] = $sheetData[$i][24];
        $data["bankreqdate"] = ($sheetData[$i][25] == "") ? null : $sheetData[$i][25];
        $data["lcno"] = $sheetData[$i][26];
        $data["lcamopen"] = ($sheetData[$i][27] == "") ? null : $sheetData[$i][27];
        $data["validmonth"] = ($sheetData[$i][28] == "") ? null : $sheetData[$i][28];
        $data["usancesettlement"] = ($sheetData[$i][29] == "") ? null : $sheetData[$i][29];
        $data["bldetail1date"] = ($sheetData[$i][30] == "") ? null : $sheetData[$i][30];
        $data["bldetail1money"] = ($sheetData[$i][31] == "") ? null : $sheetData[$i][31];
        $data["bldetail2date"] = ($sheetData[$i][32] == "") ? null : $sheetData[$i][32];
        $data["bldetail2money"] = ($sheetData[$i][33] == "") ? null : $sheetData[$i][33];
        $data["bldetail3date"] = ($sheetData[$i][34] == "") ? null : $sheetData[$i][34];
        $data["bldetail3money"] = ($sheetData[$i][35] == "") ? null : $sheetData[$i][35];
        $data["payfnameformal"] = $sheetData[$i][36];
        $data["productnamee"] = $sheetData[$i][37];
        $data["lcstate"] = $sheetData[$i][38];
        $data["bankcd"] = $sheetData[$i][39];
        $data["shipym"] = $sheetData[$i][40];
        fncInsertLcInfo($objDB, $data);
    }
    $lcModel->updateLcImpDate($lgno, date("YmdHi"));
}

$result = true;

$objDB->close();
$lcModel->close();
return true;


