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
    //ログアウト処理
    case 'logoutState':
        //処理呼び出し
        $result = logoutState($lcModel, $data);
        break;
    //L/C情報の抽出・全データ・有効データあ・PONOソートイベント
    case 'getLcInfo':
        $result = getLcInfo($objDB, $data);
        //対象データが多いとJSON変換時にメモリオーバーになるため、2000件で小分けに変換する
        ini_set('memory_limit', '512M');
        break;
    // シミュレートイベント
    case 'getSimulateLcInfo':
        // シミュレート処理
        $result = getSimulateLcInfo($objDB, $data);
        //対象データが多いとJSON変換時にメモリオーバーになるため、2000件で小分けに変換する
        ini_set('memory_limit', '512M');
        break;
    // 反映イベント
    case 'reflectLcInfo':
        $result = reflectLcInfo($objDB, $lcModel, $usrId);
        break;
    // 帳票出力初期表示イベント
    case 'getSelLcReport':
        //処理呼び出し
        $result = getSelLcReport();
        break;
    // 帳票出力の印刷イベント
    case 'exportLcReport':
        //処理呼び出し
        $result = exportLcReport($data);
        break;
}

$objDB->close();
$lcModel->close();

//結果出力
mb_convert_variables('UTF-8', 'EUC-JP', $result);
echo $s->encodeUnsafe($result);

// ログアウト処理
function logoutState($lcModel, $data)
{
    $result = $lcModel->loginStateLogout($data);
    return $result;
}

/**
 * LC情報取得
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void
 */
function getLcInfo($objDB, $data)
{
    $result = fncGetLcInfoData($objDB, $data);
    return $result;
}

/**
 * シミュレート処理
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void
 */
function getSimulateLcInfo($objDB, $data)
{
    // 銀行マスタ情報の取得
    $bankArry = fncGetValidBankInfo($objDB);

    // 通貨区分配列の取得
    $currencyClassArry = fncGetCurrencyClassList($objDB);

    // 各変数の初期化
    $shipym = $data["to"];
    $sumOfMoneypriceByPonoArry = array();
    $mCurTtlMoney = 0;
    $sumOfMoneypriceByBanknameArry = array();

    if (count($currencyClassArry) > 0) {
        foreach ($currencyClassArry as $currencyClass) {
            // 通貨別PO番号別合計金額配列
            $sumOfMoneypriceByPonoArry = fncGetSumOfMoneypriceByPono($objDB, $shipym, $currencyClass);
            //
            if (count($sumOfMoneypriceByPonoArry) > 0) {
                // 通貨別合計金額配列
                $mCurTtlMoney = fncGetSumOfMoneyprice($objDB, $shipym, $currencyClass);
                // 通貨別銀行別の合計金額取得
                $sumOfMoneypriceByBanknameArry = fncGetSumOfMoneypriceByBankname($objDB, $shipym, $currencyClass);

                $bankdivMoney = array();
                // 銀行割振処理
                for ($i = 0; $i <= count($bankArry) - 1; $i++) {
                    // 銀行別割振り金額 = 通貨別合計金額 * 銀行別割振率配列分.割振り率
                    $bankdivMoney[$i] = $mCurTtlMoney * $bankArry[$i]->bankdivrate;
                    // 通貨別PO番号別合計金額配列分
                    foreach ($sumOfMoneypriceByBanknameArry as $sumOfMoneypriceByBankname) {
                        // 銀行別割振率配列.銀行名省略名称 = 通貨別銀行別合計金額配列.発行銀行名の場合
                        if ($bankArry[$i]->bankomitname == $sumOfMoneypriceByBankname->bankname) {
                            // 銀行別割振り金額 = 銀行別割振り金額　- 通貨別銀行別合計金額配列.合計金額
                            $bankdivMoney[$i] = $bankdivMoney[$i] - $sumOfMoneypriceByBankname->totalmoneyprice;
                        }
                    }
                }

                // 通貨別PO番号別合計金額配列分
                foreach ($sumOfMoneypriceByPonoArry as $sumOfMoneypriceByPono) {
                    $curMoney = $sumOfMoneypriceByPono->totalmoneyprice;
                    $blnBkSetFlg = false;
                    for ($i = 0; $i <= count($bankArry) - 1; $i++) {
                        // 銀行別割振り金額 - 通貨別PO番号別合計金額配列.合計金額　>= 0の場合
                        if (($bankdivMoney[$i] - $curMoney) >= 0) {
                            // 銀行別割振り金額 = 銀行別割振り金額 - 通貨別PO番号別合計金額配列.合計金額
                            $bankdivMoney[$i] = $bankdivMoney[$i] - $sumOfMoneypriceByPono->totalmoneypric;
                            // t_lcinfoを更新
                            fncUpdateBankname($objDB, $bankArry[$i]->bankcd, $bankArry[$i]->bankomitname, $currencyClass, $sumOfMoneypriceByPono->pono);

                            $blnBkSetFlg = true;
                        }
                    }

                    if (!$blnBkSetFlg) {
                        $intBkNum = 0;
                        for ($i = 0; $i <= count($bankArry) - 1; $i++) {
                            if ($i == 0) {
                                $curCkMnyTmp = $bankdivMoney[$i] - $curMoney;
                                $intBkNum = $i;
                            } else {
                                if ($curCkMnyTmp > ($bankdivMoney[$i] - $curMoney)) {
                                    $curCkMnyTmp = $bankdivMoney[$i] - $curMoney;
                                    $intBkNum = $i;
                                }
                            }
                        }
                        $bankdivMoney[$intBkNum] = $bankdivMoney[$intBkNum] - $curMoney;

                        // t_lcinfoを更新
                        fncUpdateBankname($objDB, $bankArry[$intBkNum]->bankcd, $bankArry[$intBkNum]->bankomitname, $currencyClass, $sumOfMoneypriceByPono->pono);

                    }
                }
            }

        }

    }

    // L/C情報データの抽出
    $result = fncGetLcInfoData($objDB, $data);
    return $result;

}

/**
 * L/C情報をackidsに反映
 *
 * @param [object] $objDB
 * @param [object] $lcModel
 * @param [array] $data
 * @return boolean
 */
function reflectLcInfo($objDB, $lcModel, $usrId)
{
    // t_lcinfoよりL/C情報を取得する
    $lcInfoArry = fncGetLcInfoData($objDB, $data);
    if (count($lcInfoArry) > 0) {
        foreach ($lcInfoArry as $lcInfo) {
            $lcInfo["updateuser"] = $usrId;
            $lcInfo["updatedate"] = date("Ymd");
            $lcInfo["updatetime"] = date("H:i:s");
            // t_aclcinfoにデータを反映する
            $lcModel->updateAcLcInfo($lcInfo);
        }
    }
    return true;

}

// LC帳票出力画面-セレクトボックス情報取得
function getSelLcReport($objDB, $lcModel)
{
    $result["unloading_areas"] = fncGetPortplace($objDB);
    $result["bank_info"] = $lcModel->getBankList();
    return $result;
}

// LC帳票出力画面-印刷処理
function exportLcReport($objDB, $lcModel, $data)
{
    //通貨区分リストの取得
    $currency_class_list = fncGetCurrencyClassList($objDB);
    //通貨区分(未承認含む)リストの取得
    $currency_class_list_all = fncGetCurrencyClassListAll($objDB);
    //銀行リストの取得
    $bank_info = $lcModel->getBankList();
    //入力フォームの情報
    //$data ←この中にあります。
    /*
    以下帳票出力の処理は未実装です。
    現在はAjax通信で当処理が実行されますが、
    ファイルを出力するので同期通信でなければいけないかもしれません。
    その場合は画面内の入力フォームを<form>タグで括り、
    JS側の入力チェック後にSUBMITを実行する流れにする必要があります。
     */
    return true;
}
