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

require_once(LIB_DEBUGFILE);

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

$lgusrname = trim($objAuth->UserDisplayName);

//結果配列
$result = array();

//処理振り分け
switch ($data['method']) {
    // LC設定変更初期表示
    case 'getLcSetting':
        // L/c設定情報取得
        $result = getLcSetting($objDB, $lcModel, $data);
        break;
    // L/C設定変更の反映イベント
    case 'updateLcSetting':
        // L/C設定更新の反映
        $result = updateLcSetting($objDB, $lcModel, $data, $lgusrname);
        break;
}

$objDB->close();
$lcModel->close();

//結果出力
echo $s->encodeUnsafe($result);

/**
 * LC設定情報取得
 *
 * @param [object] $lcModel
 * @param [array] $data
 * @return array
 */
function getLcSetting($objDB, $lcModel, $data)
{
    //基準日の取得
    $base_open_date = $lcModel->getBaseDate();
    //銀行情報の取得
    $bank_info = fncGetBankInfo($objDB);
    //支払先情報の取得
    $payf_info = fncGetPayfInfo($objDB);

    $result["base_open_date"] = $base_open_date;
    $result["bank_info"] = $bank_info;
    $result["payf_info"] = $payf_info;

    return $result;
}

// LC設定情報更新
function updateLcSetting($objDB, $lcModel, $data, $lgusrname)
{
    //送信データ取得
    //取引先銀行情報
    $bankInfoChk = $data["send_data"]["bankInfoChk"];
    $bankInfos = $data["send_data"]["bank_info"];
    //仕入先情報
    $payfInfoChk = $data["send_data"]["payfInfoChk"];
    $payfInfos = $data["send_data"]["payf_info"];
    //基準日
    $baseOpenDateChk = $data["send_data"]["baseOpenDateChk"];
    $baseOpenDate = $data["send_data"]["baseOpenDate"];

    // DB処理開始
    $objDB->transactionBegin();
    // DB処理開始
    $lcModel->transactionBegin();
    if ($bankInfoChk == "true") {
        // kidscore2への銀行情報の更新
        // kidscore2の銀行情報の削除
        fncDeleteBank($objDB);
        // kidscore2の銀行情報の登録
        foreach ($bankInfos as $bankInfo) {
            fncInsertBank($objDB, $bankInfo);
        }
        // ackidsへの銀行情報の更新
        $lcModel->updateBankInfo(fncGetBankInfo($objDB), $lgusrname);
    }

    if ($payfInfoChk == "true") {
        // kidscore2への支払先情報の更新
        // kidscore2の支払先情報の削除
        fncDeletePayfinfo($objDB);
        // kidscore2の支払先情報の登録
        foreach ($payfInfos as $payfInfo) {
            fncInsertPayf($objDB, $payfInfo);
        }
        //支払先情報の更新
        $lcModel->updatePayfInfo($payfInfos, $lgusrname);
    }

    if ($baseOpenDateChk == "true") {
        //基準日の更新
        $lcModel->updateBaseOpenDate($baseOpenDate, $lgusrname);
    }

    // DB処理終了
    $lcModel->transactionCommit();
    // DB処理終了
    $objDB->transactionCommit();

    return $result;
}

