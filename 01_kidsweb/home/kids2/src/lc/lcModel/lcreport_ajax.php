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
        // 帳票出力の印刷イベント
        // case 'exportLcReport':
        //     //処理呼び出し
        //     $result = exportLcReport($objDB, $data);
        //     $objDB->close();
        //     break;
}

//結果出力
mb_convert_variables('UTF-8', 'EUC-JP', $result);
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

/**
 * LC帳票出力画面-印刷処理
 *
 * @param [object] $objDB
 * @param [string] $data
 * @return void
 */
function exportLcReport($objDB, $data)
{

    // パラメータの取得
    // 対象年月
    $objectYm = $data["objectYm"];

    //通貨区分リストの取得
    $currencyClassLst = fncGetCurrencyClassList($objDB);
    //通貨区分(未承認含む)リストの取得
    $currencyClassAllLst = fncGetCurrencyClassListAll($objDB);
    // 銀行マスタ情報の取得
    $bankLst = fncGetValidBankInfo($objDB);

    //テンプレートのコピー

    $reader = new XlsReader();
    $filepath = REPORT_TMPDIR . REPORT_LC_TMPFILE;
    $spreadsheet = $reader->load($filepath); //template.xlsx 読込

    // 出力
    if ($data["impletterChk"] == "true") {
        if ($currencyClassLst && count($currencyClassLst) > 0) {
            foreach ($currencyClassLst as $currencyClassObj) {
                $currencyClass = $currencyClassObj["currencyclass"];
                // 輸入信用状発行情報の出力
                reportSixOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $data);
            }
        }
    }

    if ($data["setChk"] == "true") {
        if ($currencyClassLst && count($currencyClassLst) > 0) {
            foreach ($currencyClassLst as $currencyClassObj) {
                $currencyClass = $currencyClassObj["currencyclass"];
                // LCOpen情報(Beneficiary・BK別合計)ーオープン月の出力
                reportOneOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $objectYm, 1);

                // LCOpen情報(Beneficiary・BK別合計)ー船積月の出力
                reportOneOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $objectYm, 2);

                // L/C Open情報(LC別合計）の出力
                reportTwoOutput($objDB, $spreadsheet, $currencyClass, $objectYm);

                // L/C Open情報(LC別明細）の出力
                reportThreeOutput($objDB, $spreadsheet, $currencyClass, $objectYm);

                // L/C Open情報（Open月・Beneficiary別L/C発行予定集計表）の出力
                reportFourOutput($objDB, $spreadsheet, $currencyClass, $objectYm, 3);

                // L/C Open情報（船積月・Beneficiary別L/C発行予定集計表）の出力
                reportFourOutput($objDB, $spreadsheet, $currencyClass, $objectYm, 4);
            }
        }

    }

    if ($data["unsetChk"] == "true") {
        if ($currencyClassAllLst && count($currencyClassAllLst) > 0) {
            foreach ($currencyClassAllLst as $currencyClassObj) {
                $currencyClass = $currencyClassObj["currencyclass"];
                // L/C 未決済リストの出力
                reportFiveOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $data);
            }
        }
    }

    $writer = new XlsWriter($spreadsheet);
    $writer->save(REPORT_TMPDIR . REPORT_LC_OUTPUTFILE);

}

/**
 * 帳票（オープン月）_１の出力
 *
 * @param [type] $objDB
 * @param [type] $currencyClass
 * @param [type] $bankLst
 * @param [type] $objectYm
 * @return void
 */
function reportOneOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $objectYm, $type)
{
    $insertData = array();
    $params = array();
    $params["opendate"] = str_replace("/", "", $objectYm);
    $params["currencyclass"] = $currencyClass;

    // 支払先銀行別の合計金額を取得する
    $totalPriceByPayfBankLst = fncGetSumOfMoneypriceByPayfAndBank($objDB, $params, $type);

    // 支払先別の合計金額を取得する
    $totalPriceByPayfLst = fncGetSumOfMoneypriceByPayf($objDB, $params, $type);

    // 帳票BeneBk別合計テーブルのデータを全件削除する
    fncDeleteReportByBenebktotal($objDB);

    // （臨時テーブル）帳票BeneBｋ別合計テーブルにデータを登録する
    if ($totalPriceByPayfLst && count($totalPriceByPayfLst) > 0) {
        foreach ($totalPriceByPayfLst as $totalPriceByPayf) {
            $payfcd = $totalPriceByPayf["payfcd"];
            $insertData["beneficiary"] = $totalPriceByPayf["payfnameformal"];
            $insertData["total"] = $totalPriceByPayf["totalmoneyprice"];
            if ($totalPriceByPayfBankLst && count($totalPriceByPayfBankLst) > 0) {
                foreach ($totalPriceByPayfBankLst as $totalPriceByPayfBank) {
                    if ($totalPriceByPayfBank["payfcd"] == $payfcd) {
                        $bankcd = $totalPriceByPayfBank["bankcd"];
                        $totalmoneyprice = $totalPriceByPayfBank["totalmoneyprice"];
                        if ($bankcd == $bankLst[0]["bankcd"]) {
                            $insertData["bank1"] = $totalmoneyprice;
                        } else if ($bankcd == $bankLst[1]["bankcd"]) {
                            $insertData["bank2"] = $totalmoneyprice;
                        } else if ($bankcd == $bankLst[2]["bankcd"]) {
                            $insertData["bank3"] = $totalmoneyprice;
                        } else if ($bankcd == $bankLst[3]["bankcd"]) {
                            $insertData["bank4"] = $totalmoneyprice;
                        }
                    } else {
                        continue;
                    }
                }
            }

            // 帳票BeneBｋ別合計テーブルの登録設定
            fncInsertReportByBenebktotal($objDB, $insertData);

            unset($insertData);

        }
        // テンプレートの出力
        fncSetReportOne($objDB, $spreadsheet, "1", $currencyClass, $bankLst, $objectYm, $type);

    }
}

/**
 * 帳票_2の出力
 *
 * @param [object] $objDB
 * @param [sheet] $spreadsheet
 * @param [string] $currencyClass
 * @param [stringg] $objectYm
 * @return void
 */
function reportTwoOutput($objDB, $spreadsheet, $currencyClass, $objectYm)
{
    $insertData = array();
    $priceTotal = 0;
    // 帳票出力用のL/C別合計
    $lcinfoLst = fncGetLcInfoForReportTwo($objDB, str_replace("/", "", $objectYm), $currencyClass);

    // （臨時テーブル）帳票LC別合計テーブルのデータを全件削除する
    fncDeleteReportByLcTotal($objDB);

    if ($lcinfoLst && count($lcinfoLst) > 0) {
        foreach ($lcinfoLst as $lcinfo) {
            $insertData["lcno"] = $lcinfo["lcno"];
            $insertData["factoryname"] = $lcinfo["payfnameformal"];
            $insertData["price"] = $lcinfo["moneyprice"];
            $insertData["shipterm"] = $lcinfo["shipterm"];
            $insertData["validterm"] = $lcinfo["validterm"];
            $insertData["bankname"] = $lcinfo["bankname"];
            $insertData["bankreqdate"] = $lcinfo["bankreqdate"];
            $insertData["lcamopen"] = $lcinfo["lcamopen"];
            // 合計金額の設定
            $priceTotal += $lcinfo["moneyprice"];

            // （臨時テーブル）帳票LC別合計テーブルにデータを登録する
            fncInsertReportByLcTotal($objDB, $insertData);

            unset($insertData);
        }

        // テンプレートの出力
        fncSetReportTwo($objDB, $spreadsheet, "2", $currencyClass, $objectYm, $priceTotal);

    }
}

/**
 * 帳票_3の出力
 *
 * @param [object] $objDB
 * @param [sheet] $spreadsheet
 * @param [string] $currencyClass
 * @param [string] $objectYm
 * @return void
 */
function reportThreeOutput($objDB, $spreadsheet, $currencyClass, $objectYm)
{
    $insertData = array();
    $priceTotal = 0;
    // 帳票出力用のL/C別合計
    $lcinfoLst = fncGetLcInfoForReportTwo($objDB, str_replace("/", "", $objectYm), $currencyClass);

    // （臨時テーブル）帳票LC別明細テーブルのデータを全件削除する
    fncDeleteReportByLcDetail($objDB);

    if ($lcinfoLst && count($lcinfoLst) > 0) {
        foreach ($lcinfoLst as $lcinfo) {
            $insertData["lcno"] = $lcinfo["lcno"];
            $insertData["pono"] = $lcinfo["pono"] . sprintf("%02s", $lcinfo["polineno"]);
            $insertData["factoryname"] = $lcinfo["payfnameformal"];
            $insertData["productcd"] = $lcinfo["productcd"];
            $insertData["productrevisecd"] = $lcinfo["productrevisecd"];
            $insertData["productname"] = $lcinfo["productname"];
            $insertData["productnumber"] = $lcinfo["productnumber"];
            $insertData["unitname"] = $lcinfo["unitname"];
            $insertData["unitprice"] = $lcinfo["unitprice"];
            $insertData["moneyprice"] = $lcinfo["moneyprice"];
            $insertData["shipstartdate"] = $lcinfo["shipstartdate"];
            $insertData["shipenddate"] = $lcinfo["shipenddate"];
            $insertData["portplace"] = $lcinfo["portplace"];
            $insertData["shipterm"] = $lcinfo["shipterm"];
            $insertData["validterm"] = $lcinfo["validterm"];
            $insertData["bankname"] = $lcinfo["bankname"];
            $insertData["bankreqdate"] = $lcinfo["bankreqdate"];
            $insertData["lcamopen"] = $lcinfo["lcamopen"];
            // 合計金額の設定
            $priceTotal += $lcinfo["moneyprice"];

            // （臨時テーブル）帳票LC別合計テーブルにデータを登録する
            fncInsertReportByLcDetail($objDB, $insertData);
        }
        // テンプレートの出力
        fncSetReportThree($objDB, $spreadsheet, "3", $currencyClass, $objectYm, $priceTotal);

    }

}

/**
 * 帳票_4の出力
 *
 * @param [object] $objDB
 * @param [sheet] $spreadsheet
 * @param [string] $currencyClass
 * @param [string] $objectYm
 * @param [string] $type
 * @return void
 */
function reportFourOutput($objDB, $spreadsheet, $currencyClass, $objectYm, $type)
{

    $insertData = array();
    $params = array();
    $dates = array();
    $objectYm = str_replace("/", "-", $objectYm) . "-01";
    $dates["date1"] = date("Ym", strtotime($objectYm . "-6 month"));
    $dates["date2"] = date("Ym", strtotime($objectYm . "-5 month"));
    $dates["date3"] = date("Ym", strtotime($objectYm . "-4 month"));
    $dates["date4"] = date("Ym", strtotime($objectYm . "-3 month"));
    $dates["date5"] = date("Ym", strtotime($objectYm . "-2 month"));
    $dates["date6"] = date("Ym", strtotime($objectYm . "-1 month"));
    $dates["date7"] = date("Ym", strtotime($objectYm . "0 month"));
    $dates["date8"] = date("Ym", strtotime($objectYm . "+1 month"));
    $dates["date9"] = date("Ym", strtotime($objectYm . "+2 month"));
    $dates["date10"] = date("Ym", strtotime($objectYm . "+3 month"));
    $dates["date11"] = date("Ym", strtotime($objectYm . "+4 month"));
    $params["opendatefrom"] = $dates["date1"];
    $params["opendateto"] = $dates["date11"];
    $params["currencyclass"] = $currencyClass;

    // 支払先月別の合計金額を取得する
    $totalPriceByPayfDateLst = fncGetSumOfMoneypriceByPayfAndOpenDate($objDB, $params, $type);

    // 支払先別の合計金額を取得する
    $totalPriceByPayfLst = fncGetSumOfMoneypriceByPayf($objDB, $params, $type);

    // 帳票Bene月別合計テーブルのデータを全件削除する
    fncDeleteReportByBeneMonthCal($objDB);

    if (count($totalPriceByPayfLst) > 0 && count($totalPriceByPayfDateLst) > 0) {
        foreach ($totalPriceByPayfLst as $totalPriceByPayf) {

            $payfcd = $totalPriceByPayf["payfcd"];

            $insertData["beneficiary"] = $totalPriceByPayf["payfnameformal"];
            $insertData["total"] = $totalPriceByPayf["totalmoneyprice"];
            $total = 0;

            foreach ($totalPriceByPayfDateLst as $totalPriceByPayfDate) {

                if ($totalPriceByPayfDate["payfcd"] == $payfcd) {
                    $opendate = $totalPriceByPayfDate["opendate"];
                    $moneyprice = $totalPriceByPayfDate["totalmoneyprice"];

                    if ($opendate == $dates["date1"]) {
                        $insertData["date1"] = $moneyprice;
                    } else if ($opendate == $dates["date2"]) {
                        $insertData["date2"] = $moneyprice;
                    } else if ($opendate == $dates["date3"]) {
                        $insertData["date3"] = $moneyprice;
                    } else if ($opendate == $dates["date4"]) {
                        $insertData["date4"] = $moneyprice;
                    } else if ($opendate == $dates["date5"]) {
                        $insertData["date5"] = $moneyprice;
                    } else if ($opendate == $dates["date6"]) {
                        $insertData["date6"] = $moneyprice;
                    } else if ($opendate == $dates["date7"]) {
                        $insertData["date7"] = $moneyprice;
                    } else if ($opendate == $dates["date8"]) {
                        $insertData["date8"] = $moneyprice;
                    } else if ($opendate == $dates["date9"]) {
                        $insertData["date9"] = $moneyprice;
                    } else if ($opendate == $dates["date10"]) {
                        $insertData["date10"] = $moneyprice;
                    } else if ($opendate == $dates["date11"]) {
                        $insertData["date11"] = $moneyprice;
                    }
                    $total += $moneyprice;
                } else {
                    continue;
                }
            }

            $insertData["total"] = $total;
            // （臨時テーブル）帳票Bene月別合計テーブルにデータを登録する
            fncInsertReportByBeneMonthCal($objDB, $insertData);

            unset($insertData);

        }
        // テンプレートの出力
        fncSetReportFour($objDB, $spreadsheet, "4", $currencyClass, $objectYm, $type);

    }

}

/**
 * 帳票_5の出力
 *
 * @param [object] $objDB
 * @param [sheet] $spreadsheet
 * @param [string] $currencyClass
 * @param [array] $bankLst
 * @param [array] $data
 * @return void
 */
function reportFiveOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $data)
{
    $insertData = array();
    // （臨時テーブル）帳票未決済額テーブルデータを全件削除する
    fncDeleteReportUnSettedPrice($objDB);
    // L/C情報取得
    $lcinfoLst = fncGetLcInfoForReportFive($objDB, $data["startDate"], $data["endDate"], $currencyClass, 1);

    // （臨時テーブル）帳票未決済額テーブルにデータを登録する
    if ($lcinfoLst && count($lcinfoLst) > 0) {
        $data = array();
        $manageno = 1;
        foreach ($lcinfoLst as $lcinfo) {
            $insertData["managementno"] = $manageno;
            $insertData["bankname"] = $lcinfo["bankname"];
            $insertData["payeeformalname"] = $lcinfo["payfnameformal"];
            $insertData["shipstartdate"] = $lcinfo["shipstartdate"];
            $insertData["lcno"] = $lcinfo["lcno"];
            $insertData["usancesettlement"] = $lcinfo["moneyprice"] -
                ($lcinfo["bldetail1money"] + $lcinfo["bldetail2money"] + $lcinfo["bldetail3money"]);

            fncInsertReportUnSettedPrice($objDB, $insertData);
            $manageno += 1;
            unset($insertData);
        }
    }

    // （臨時テーブル）帳票未決済額未承認テーブルデータを全件削除する
    fncDeleteReportUnSettedPriceUnapproval($objDB);

    // L/C情報取得
    $lcinfoLst = fncGetLcInfoForReportFive($objDB, $startYmd, $endYmd, $currencyClass, 2);

    // （臨時テーブル）帳票未決済額未承認テーブルにデータを登録する
    if ($lcinfoLst && count($lcinfoLst) > 0) {
        foreach ($lcinfoLst as $lcinfo) {
            $insertData["payeeformalname"] = $lcinfo["payfnameformal"];
            $insertData["unsettledprice"] = $lcinfo["moneyprice"] -
                ($lcinfo["bldetail1money"] + $lcinfo["bldetail2money"] + $lcinfo["bldetail3money"]);

            fncInsertReportUnSettedPriceUnapproval($objDB, $insertData);
            unset($insertData);
        }
    }

    // 帳票BeneBK別合計テーブルデータを全件削除する
    fncDeleteReportByBenebktotal($objDB);

    // 支払先銀行別の合計金額を取得する
    $totalUnSettedPriceByPayfBankLst = fncGetSumofUnSettedPriceByPayfAndBank($objDB);

    // 支払先銀行別の合計金額を取得する
    $totalUnSettedPriceByPayfLst = fncGetSumofUnSettedPriceByPayf($objDB);

    // （臨時テーブル）帳票BeneBk別合計テーブルにデータを登録する
    if ($totalUnSettedPriceByPayf && count($totalUnSettedPriceByPayf) > 0) {
        foreach ($totalUnSettedPriceByPayfLst as $totalUnSettedPriceByPayf) {
            $payfcd = $totalUnSettedPriceByPayf["payfcd"];
            $insertData["beneficiary"] = $totalUnSettedPriceByPayf["payfnameformal"];
            $insertData["total"] = $totalUnSettedPriceByPayf["totalmoneyprice"];
            foreach ($totalUnSettedPriceByPayfBankLst as $totalUnSettedPriceByPayfBank) {
                if ($totalUnSettedPriceByPayfBank["payfcd"] == $payfcd) {
                    $bankcd = $totalUnSettedPriceByPayfBank["bankcd"];
                    $totalmoneyprice = $totalUnSettedPriceByPayfBank["totalmoneyprice"];
                    if ($bankcd == $bankLst[0]["bankcd"]) {
                        $insertData["bank1"] = $totalmoneyprice;
                    } else if ($bankcd == $bankLst[1]["bankcd"]) {
                        $insertData["bank2"] = $totalmoneyprice;
                    } else if ($bankcd == $bankLst[2]["bankcd"]) {
                        $insertData["bank3"] = $totalmoneyprice;
                    } else if ($bankcd == $bankLst[3]["bankcd"]) {
                        $insertData["bank4"] = $totalmoneyprice;
                    }
                } else {
                    continue;
                }
            }

            // 帳票BeneBk別合計テーブルの登録設定
            fncInsertReportByBenebktotal($objDB, $insertData);

            unset($insertData);
        }
    }

    // レートを設定する
    $rate = 0;
    if ($data["rate"] != "" && $data["rate"] > 0) {
        $rate = $data["rate"];
    } else {
        // パラメータの通貨区分（未承認含む）が”円”の場合、通貨区分 = 1
        if ($currencyClass == "円") {
            $monetaryUnitCode = DEF_MONETARY_YEN;

            // パラメータの通貨区分（未承認含む）が”USドル”の場合、通貨区分 = 2
        } else if ($currencyClass == "USドル") {
            $monetaryUnitCode = DEF_MONETARY_USD;

            // パラメータの通貨区分（未承認含む）が”HKドル”の場合、通貨区分 = 3
        } else if ($currencyClass == "HKドル") {
            $monetaryUnitCode = DEF_MONETARY_HKD;
        }
        // 通貨区分（未承認含む）が”円”の場合、
        if ($currencyClass == "円") {
            $rate = 0;
        } else {
            $rate = fncGetMonetaryRate($objDB, DEF_MONETARYCLASS_SHANAI, $monetaryUnitCode);
        }

        // テンプレートの出力
        fncSetReportFive($objDB, $spreadsheet, "5", $currencyClass, $bankLst, $data["startDate"], $data["endDate"], $rate);

    }
}

/**
 * 帳票_6の出力
 *
 * @param [object] $objDB
 * @param [sheet] $spreadsheet
 * @param [string] $currencyClass
 * @param [array] $bankLst
 * @param [array] $data
 * @return void
 */
function reportSixOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $data)
{
    $insertData = array();
    // L/C情報を取得する
    $lcinfoLst = fncGetLcInfoForReportSix($objDB, $currencyClass, $data);

    // （臨時テーブル）帳票輸入信用状発行情報テーブルよりデータを全件削除する
    fncDeleteReportImpLcOrderInfo($objDB);

    // 上記取得したLC情報を帳票輸入信用状発行情報テーブルに登録する
    if ($lcinfoLst && count($lcinfoLst) > 0) {
        foreach ($lcinfoLst as $lcinfo) {
            $insertData["bankreqdate"] = $lcinfo["bankreqdate"];
            $insertData["pono"] = $lcinfo["pono"] . sprintf("%02s", $lcinfo["polineno"]);
            $insertData["productcd"] = $lcinfo["productcd"];
            $insertData["productrevisecd"] = $lcinfo["productrevisecd"];
            $insertData["productname"] = $lcinfo["productname"];
            $insertData["productnumber"] = $lcinfo["productnumber"];
            $insertData["unitname"] = $lcinfo["unitname"];
            $insertData["unitprice"] = $lcinfo["unitprice"];
            $insertData["moneyprice"] = $lcinfo["moneyprice"];
            $insertData["shipstartdate"] = $lcinfo["shipstartdate"];
            $insertData["shipenddate"] = $lcinfo["shipenddate"];
            $insertData["shipterm"] = $lcinfo["shipterm"];
            $insertData["validterm"] = $lcinfo["validterm"];
            $insertData["lcno"] = $lcinfo["lcno"];
            $insertData["reckoninginitialdate"] = $lcinfo["reckoninginitialdate"];
            $insertData["portplace"] = $lcinfo["portplace"];
            $insertData["bankname"] = $lcinfo["bankname"];
            $insertData["reserve1"] = $lcinfo["reserve1"];

            fncInsertReportImpLcOrderInfo($objDB, $insertData);

            unset($insertData);
        }
        // テンプレートの出力
        return fncSetReportSix($objDB, $spreadsheet, "6", $currencyClass, $bankLst, $data);

    }
}
