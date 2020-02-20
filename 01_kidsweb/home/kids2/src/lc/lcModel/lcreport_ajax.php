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
require_once './report_common.php';
require_once './reportoutput.php';
require LIB_FILE;
//PHP標準のJSON変換メソッドはエラーになるので外部のライブラリ(恐らくエンコードの問題)
require_once 'JSON.php';
// phpspreadsheetパッケージをインポートする
require PATH_HOME . "/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PhpOffice\PhpSpreadsheet\Writer\Xls as XlsWriter;

//値の取得
$postdata = file_get_contents("php://input");
$data = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//経理サブシステムDB接続
$lcModel = new lcModel();

//JSONクラスインスタンス化
$s = new Services_JSON();

//値が存在しない場合は通常の POST で受ける
if ($data == null) {
    $data = $_POST;
}

// セッション確認
$objAuth = fncIsSession($data["sessionid"], $objAuth, $objDB);

//ユーザーID取得(半角スペースがあるため)
$usrId = trim($objAuth->UserID);

//結果配列
$result = array();

//処理振り分け
switch ($data['method']) {
    // 帳票出力初期表示イベント
    case 'getSelLcReport':
        //処理呼び出し
        $result = getSelLcReport($objDB, $lcModel);
        $objDB->close();
        $lcModel->close();
        break;
}

//結果出力
echo $s->encodeUnsafe($result);

/**
 * LC帳票出力画面-セレクトボックス情報取得
 *
 * @param [type] $objDB
 * @param [type] $lcModel
 * @return void
 */
function getSelLcReport($objDB, $lcModel)
{
    // ackidsのデータをkidscore2に登録
    // ackidsの銀行情報の取得
    $bankArry = $lcModel->getBankInfo();
    // kidscore2の銀行情報の削除
    fncDeleteBank($objDB);
    // kidscore2の銀行情報の登録
    if (count($bankArry) > 0) {
        foreach ($bankArry as $bank) {
            fncInsertBank($objDB, $bank);
        }
    }

    // ackidsの支払先情報の取得
    $payfArry = $lcModel->getPayfInfo();
    // kidscore2の支払先情報の削除
    fncDeletePayfinfo($objDB);
    // kidscore2の支払先情報の登録
    if (count($payfArry) > 0) {
        foreach ($payfArry as $payf) {
            fncInsertPayf($objDB, $payf);
        }
    }

    // ackidsの送付先マスタ情報の取得
    $sendArry = $lcModel->getSendInfo();
    // kidscore2の送付先マスタ情報の削除
    fncDeleteSendinfo($objDB);
    // kidscore2の送付先マスタ情報の登録
    if (count($sendArry) > 0) {
        foreach ($sendArry as $send) {
            fncInsertSendInfo($objDB, $send);
        }
    }

    // 荷揚地リストの取得
    $result["portplace"] = fncGetPortplaceAndAll($objDB);
    // 銀行リストの取得
    $result["bankinfo"] = fncGetBankAndAll($objDB);

    return $result;
}

