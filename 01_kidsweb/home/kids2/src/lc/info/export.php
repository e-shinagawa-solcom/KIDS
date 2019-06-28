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

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Write\Csv;

$data = $_GET;

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

$curdate = date("YmdHi");

// パラメータの有効フラグ = 1の場合
if ($data["getDataModeFlg"] == 1) {
    $txtFileName = "ACLC_VAL_" .$curdate .".txt";
} else {
    $txtFileName = "ACLC_ALL_" .$curdate .".txt";
}

// t_lcinfoよりL/C情報を取得する
$lcInfoArry = fncGetLcInfoData($objDB, $data);

$lcInfoArry = mb_convert_encoding($lcInfoArry, 'UTF-8', 'EUC-JP');

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// A1から$dataを貼り付け
$header = [['支払先名称', 'オープン年月', '荷揚地', 'PO番号',
    'PO行番号', 'POリバイズ番号', 'POデータ状態', '支払先コード',
    '商品コード', '商品名', '数量', '単位', '単価', '金額',
    '船積開始予定日付', '船積終了予定日付', '計上日', '更新日',
    '納品場所', '通貨区分', '備考', '船積期限', '有効期限',
    '発行銀行名', '銀行依頼日', 'LC番号', 'LCAMオープン',
    '有効日', 'ユーザンス決済', 'BL引受明細１日付', 'BL引受明細１金額',
    'BL引受明細２日付', 'BL引受明細２金額', 'BL引受明細３日付',
    'BL引受明細３金額', '支払先正式名称', '商品名英名', '状態',
    '銀行コード', '船積年月']];
// A1にヘッダの貼り付ける
$sheet->fromArray($header, null, 'A1');
// L/C情報取得件数 > 0の場合
if (count($lcInfoArry) > 0) {
    // A2にL/C情報を貼り付ける
    $sheet->fromArray($lcInfoArry, null, 'A2');
    // ログイン状況テーブルのエクスポート時刻を更新する
    $lcModel->updateLcExpDate($lgno, $curdate);
}

$objDB->close();
header("Content-Type: application/octet-stream");
header('Content-Disposition: attachment;filename="' .$txtFileName .'"');
header('Cache-Control: max-age=0');
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
// 区切り文字
$writer->setDelimiter("\t");
// 囲み文字
$writer->setEnclosure('"');
// 改行コード
$writer->setLineEnding("\r\n");
// // CSVを出力するシート
// $writer->setSheetIndex(0);
$writer->save('php://output');
?>

