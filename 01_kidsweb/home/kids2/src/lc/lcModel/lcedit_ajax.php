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
    // L/C編集の初期表示イベント
    case 'getLcEdit':
        // L/C情報取得
        $result = getLcEdit($objDB, $lcModel, $data);
        break;
    // L/C編集の更新イベント
    case 'updateLcEdit':
        //L/C情報の更新
        $result = updateLcEdit($objDB, $lcModel, $data);
        break;
    // L/C編集の解除イベント
    case 'releaseLcEdit':
        //処理呼び出し
        $result = releaseLcEdit($objDB, $data);
        break;
}

$objDB->close();
$lcModel->close();

//結果出力
echo $s->encodeUnsafe($result);


/**
 * LC情報取変更画面-情報取得
 *
 * @param [object] $objDB
 * @param [object] $lcModel
 * @param [array] $data
 * @return void
 */
function getLcEdit($objDB, $lcModel, $data)
{
    //単体のL/C情報取得
    $result["lc_data"] = fncGetLcInfoSingle($objDB, $data);
    //荷揚地取得
    $result["portplace_list"] = fncGetPortplace($objDB);
    //銀行リスト取得
    $result["bank_list"] = $lcModel->getBankList();
    // パラメータの状態 = 7の場合
    if ($result["lc_data"]->lcstate == 7 && $result["lc_data"]->bankreqdate == "") {
        if (intval($data["poreviseno"]) > 0) {
            $poreviseno = intval($data["poreviseno"]);
            do {
                $param = $data;
                $param["poreviseno"] = sprintf("%02d", $poreviseno - 1);
                // 同一POの直近リバイズデータを取得する
                $lcinfo = fncGetLcInfoSingle($objDB, $param);
                // 取得したデータの銀行依頼日が空の場合、
                if ($lcinfo->bankreqdate != "") {
                    $result["lc_data"]->bankreqdate = $lcinfo->bankreqdate;
                    $result["lc_data"]->lcamopen = $lcinfo->lcamopen;
                    $result["lc_data"]->validmonth = $lcinfo->validmonth;
                    $result["lc_data"]->usancesettlement = $lcinfo->usancesettlement;
                    $result["lc_data"]->bldetail1date = $lcinfo->bldetail1date;
                    $result["lc_data"]->bldetail1money = $lcinfo->bldetail1money;
                    $result["lc_data"]->bldetail2date = $lcinfo->bldetail2date;
                    $result["lc_data"]->bldetail2money = $lcinfo->bldetail2money;
                    $result["lc_data"]->bldetail3date = $lcinfo->bldetail3date;
                    $result["lc_data"]->bldetail3money = $lcinfo->bldetail3money;
                    break;
                }
                $poreviseno = $poreviseno - 1;
            } while ($poreviseno != 0);
        }
    }

    return $result;
}

/**
 * LC情報取変更画面-更新処理
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return 更新件数
 */
function updateLcEdit($objDB, $lcModel, $data)
{
    // $bankreqchk = $data["bankreqchk"];
    // if ($bankreqdate == "") {
    //     if (intval($data["poreviseno"]) > 0) {
    //         $poreviseno = intval($data["poreviseno"]);
    //         do {
    //             $param = $data;
    //             $param["poreviseno"] = sprintf("%02d", $poreviseno - 1);
    //             // 同一POの直近リバイズデータを取得する
    //             $lcinfo = fncGetLcInfoSingle($objDB, $param);
    //             // 取得したデータの銀行依頼日が空の場合、
    //             if ($lcinfo != null) {
    //                 if ($lcinfo->bankreqdate != "") {
    //                     $data["bankreqdate"] = $lcinfo->bankreqdate;
    //                     $data["lcamopen"] = $lcinfo->lcamopen == "" ? $data["lcamopen"] :$lcinfo->lcamopen;
    //                     $data["validmonth"] = $lcinfo->validmonth == "" ? $data["validmonth"] :$lcinfo->validmonth;
    //                     break;
    //                 }
    //             }
    //             $poreviseno = $poreviseno - 1;
    //         } while ($poreviseno != 0);
    //     }
    // }
    // パラメータの状態 <> 7の場合
    if ($data["lcstate"] != 7) {
        if ($data["lcstate"] == 9) {
            $data["lcstate"] = 10;
        }
        if ($data["bankcd"] != "") {
            $bankinfo = $lcModel->getAcBankInfo($data["bankcd"]);
            $data["bankname"] = $bankinfo->bankomitname;
        }
        // L/C情報の更新
        $result = fncUpdateLcinfo($objDB, $data);
    } else {
        // L/C情報の更新
        $result = fncUpdateLcinfoToAmandCancel($objDB, $data);
    }

    // 決済金額の更新
    $result = fncUpdateSettleInfo($objDB, $data);

    return $result;
}

/**
 * LC情報変更画面-解除処理
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return 解除件数
 */
function releaseLcEdit($objDB, $data)
{

    $data = $data["lc_data"];

    // パラメータの状態 = 3の場合
    if ($data["lcstate"] == 3) {
        if (intval($data["poreviseno"]) > 0) {
            $poreviseno = intval($data["poreviseno"]);
            do {
                $param = $data;
                $param["poreviseno"] = sprintf("%02d", $poreviseno - 1);
                // 同一POの直近リバイズデータを取得する
                $lcinfo = fncGetLcInfoSingle($objDB, $param);
                // 取得したデータの銀行依頼日が空の場合、
                if ($lcinfo->bankreqdate != "") {
                    $data["bankreqdate"] = $lcinfo->bankreqdate;
                    $data["lcamopen"] = $lcinfo->lcamopen;
                    $data["validmonth"] = $lcinfo->validmonth;
                    break;
                }
                $poreviseno = $poreviseno - 1;
            } while ($poreviseno != 0);
        }

        if ($data["bankreqdate"] != "" && $data["poreviseno"] != "00") {
            $data["lcstate"] = 7;
        } else {
            $data["lcstate"] = 4;
        }
    } else if ($data["lcstate"] == 7) {
        $data["lcstate"] = 8;
    } else {
        $data["lcstate"] = 10;
    }
    // L/C情報の状態更新
    $result = fncUpdateLcState($objDB, $data);

    return $result;
}

