<?php

// 読み込み
include 'conf.inc';
//クラスファイルの読み込み
require_once '../lcModel/db_common.php';
//共通ファイル読み込み
require_once '../lcModel/lcModelCommon.php';
//DB接続ファイルの読み込み
require_once '../lcModel/kidscore_common.php';
require_once '../lcModel/report_common.php';
require_once '../lcModel/reportoutput.php';
require LIB_FILE;
//PHP標準のJSON変換メソッドはエラーになるので外部のライブラリ(恐らくエンコードの問題)
require_once 'JSON.php';
// phpspreadsheetパッケージをインポートする
require PATH_HOME . "/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PhpOffice\PhpSpreadsheet\Writer\Xls as XlsWriter;

$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//経理サブシステムDB接続
$lcModel = new lcModel();

$data = $_POST;

$data["bankname"] = $_REQUEST["bankname"];

foreach ($data as $key => $value) {
    $data[$key] = trim($value);
}


// セッション確認
$objAuth = fncIsSession($data["strSessionID"], $objAuth, $objDB);

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

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filepath); //template.xlsx 読込

// 出力
if ($data["impletterChk"] == "1") {
    if ($currencyClassLst && count($currencyClassLst) > 0) {
        $pageNo_6 = 1;
        foreach ($currencyClassLst as $currencyClassObj) {
            $currencyClass = $currencyClassObj["currencyclass"];
            // 輸入信用状発行情報の出力
            $pageNo_6 = reportSixOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $data, $pageNo_6);
        }
    }
}

if ($data["setChk"] == "1") {
    if ($currencyClassLst && count($currencyClassLst) > 0) {
        $pageNo_1_openym = 1;
        $pageNo_1_shipym = 1;
        $pageNo_2 = 1;
        $pageNo_3 = 1;
        $pageNo_4_openym = 1;
        $pageNo_4_shipym = 1;

        // $clonedWorksheet = clone $spreadsheet->getSheetByName("1");
        // $clonedWorksheet->setTitle("1_船積月");
        // $spreadsheet->addSheet($clonedWorksheet);

        $oldIndex = $spreadsheet->getIndex($spreadsheet->getSheetByName("5"));
        $clonedWorksheet = clone $spreadsheet->getSheetByName("4");
        $clonedWorksheet->setTitle("4_船積月");
        $spreadsheet->addSheet($clonedWorksheet, $oldIndex);
        foreach ($currencyClassLst as $currencyClassObj) {
            $currencyClass = $currencyClassObj["currencyclass"];

            // LCOpen情報(Beneficiary・BK別合計)ーオープン月の出力
            $pageNo_1_openym = reportOneOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $objectYm, 1, $pageNo_1_openym);

            // LCOpen情報(Beneficiary・BK別合計)ー船積月の出力
            // $pageNo_1_shipym = reportOneOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $objectYm, 2, $pageNo_1_shipym);

            // L/C Open情報(LC別合計）の出力
            $pageNo_2 = reportTwoOutput($objDB, $spreadsheet, $currencyClass, $objectYm, $pageNo_2);

            // L/C Open情報(LC別明細）の出力
            $pageNo_3 = reportThreeOutput($objDB, $spreadsheet, $currencyClass, $objectYm, $pageNo_3);

            // L/C Open情報（Open月・Beneficiary別L/C発行予定集計表）の出力
            $pageNo_4_openym = reportFourOutput($objDB, $spreadsheet, $currencyClass, $objectYm, 3, $pageNo_4_openym);

            // L/C Open情報（船積月・Beneficiary別L/C発行予定集計表）の出力
            $pageNo_4_shipym = reportFourOutput($objDB, $spreadsheet, $currencyClass, $objectYm, 4, $pageNo_4_shipym);
        }
    }

}

if ($data["unsetChk"] == "1") {
    if ($currencyClassAllLst && count($currencyClassAllLst) > 0) {
        $pageNo_5 = 1;
        foreach ($currencyClassAllLst as $currencyClassObj) {
            $currencyClass = $currencyClassObj["currencyclass"];
            // L/C 未決済リストの出力
            $pageNo_5 = reportFiveOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $data, $pageNo_5);
        }
    }
}

$objDB->close();
// return;

$filename = 'lc_report_' . date("YmdHis") . '.xls';
// 成功
$writer = new XlsWriter($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');


/**
 * 帳票（オープン月）_１の出力
 *
 * @param [type] $objDB
 * @param [type] $currencyClass
 * @param [type] $bankLst
 * @param [type] $objectYm
 * @return void
 */
function reportOneOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $objectYm, $type, $pageNo)
{
    if ($type == 1) {
        $sheetName = "1";
    } else {
        $sheetName = "1_船積月";
    }
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
                        if ($bankcd == trim($bankLst[0]["bankcd"])) {
                            $insertData["bank1"] = $totalmoneyprice;
                        } else if ($bankcd == trim($bankLst[1]["bankcd"])) {
                            $insertData["bank2"] = $totalmoneyprice;
                        } else if ($bankcd == trim($bankLst[2]["bankcd"])) {
                            $insertData["bank3"] = $totalmoneyprice;
                        } else if ($bankcd == trim($bankLst[3]["bankcd"])) {
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

        // ページ数の取得
        $pageNum = ceil(count($totalPriceByPayfLst) / REPORT_LC_ONE_PAGE_REPORT_NUM_1);

        // 帳票出力
        for ($i = 0; $i < $pageNum; $i++) {
            $startRow = 1 + ($pageNo + $i - 1) * 27;
            fncSetReportOne($objDB, $spreadsheet, $sheetName, $currencyClass, $bankLst, $objectYm, $type, $startRow, $i);
        }
        $pageNo = $pageNo + $pageNum;
    }

    return $pageNo;
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
function reportTwoOutput($objDB, $spreadsheet, $currencyClass, $objectYm, $pageNo)
{
    $insertData = array();
    $priceTotal = 0;
    $itemTotal = 0;
    $opendate = str_replace("/", "", $objectYm);
    // 帳票出力用のL/C別合計
    $lcinfoSumLst = fncGetSumMoneyPriceForReportTwo($objDB, $opendate, $currencyClass);

    // （臨時テーブル）帳票LC別合計テーブルのデータを全件削除する
    fncDeleteReportByLcTotal($objDB);

    if ($lcinfoSumLst && count($lcinfoSumLst) > 0) {
        $count = count($lcinfoSumLst);
        foreach ($lcinfoSumLst as $lcinfosum) {
            $data["lcno"] = $lcinfosum["lcno"];
            $data["bankcd"] = $lcinfosum["bankcd"];
            $data["payfnameformal"] = $lcinfosum["payfnameformal"];
            $data["opendate"] = $opendate;
            $data["currencyclass"] = $currencyClass;

            $lcinfoLst = fncGetLcInfoForReportTwo($objDB, $data);

            // 帳票LC別合計データの設定
            $insertData["lcno"] = $lcinfoLst[0]["lcno"];
            $insertData["factoryname"] = $lcinfoLst[0]["payfnameformal"];
            $insertData["price"] = $lcinfosum["itemprice"];
            $insertData["shipterm"] = $lcinfoLst[0]["shipterm"];
            $insertData["validterm"] = $lcinfoLst[0]["validterm"];
            $insertData["bankname"] = $lcinfoLst[0]["bankname"];
            $insertData["bankreqdate"] = $lcinfoLst[0]["bankreqdate"];
            $insertData["lcamopen"] = $lcinfoLst[0]["lcamopen"];

            // 合計金額の設定
            $priceTotal += $lcinfosum["itemprice"];

            // （臨時テーブル）帳票LC別合計テーブルにデータを登録する
            fncInsertReportByLcTotal($objDB, $insertData);

            unset($insertData);
        }

        // ページ数の取得
        $pageNum = ceil($count / REPORT_LC_ONE_PAGE_REPORT_NUM_2);

        // 帳票出力
        for ($i = 0; $i < $pageNum; $i++) {
            $startRow = 1 + ($pageNo + $i - 1) * 40;
            fncSetReportTwo($objDB, $spreadsheet, "2", $currencyClass, $objectYm, $priceTotal, $startRow, $i);
        }
        $pageNo = $pageNo + $pageNum;
    }

    return $pageNo;
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
function reportThreeOutput($objDB, $spreadsheet, $currencyClass, $objectYm, $pageNo)
{
    $insertData = array();
    $priceTotal = 0;
    // 帳票出力用のL/C別明細
    $lcinfoLst = fncGetLcInfoForReportThree($objDB, str_replace("/", "", $objectYm), $currencyClass);

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

        // ページ数の取得
        $pageNum = ceil(count($lcinfoLst) / REPORT_LC_ONE_PAGE_REPORT_NUM_3);
        // 帳票出力
        for ($i = 0; $i < $pageNum; $i++) {
            $startRow = 1 + ($pageNo + $i - 1) * 47;
            fncSetReportThree($objDB, $spreadsheet, "3", $currencyClass, $objectYm, $priceTotal, $startRow, $i);
        }
        $pageNo = $pageNo + $pageNum;
    }

    return $pageNo;

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
function reportFourOutput($objDB, $spreadsheet, $currencyClass, $objectYm, $type, $pageNo)
{
    if ($type == 3) {
        $sheetName = "4";
    } else {
        $sheetName = "4_船積月";
    }
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
    $dates["date7"] = date("Ym", strtotime($objectYm));
    $dates["date8"] = date("Ym", strtotime($objectYm . "+1 month"));
    $dates["date9"] = date("Ym", strtotime($objectYm . "+2 month"));
    $dates["date10"] = date("Ym", strtotime($objectYm . "+3 month"));
    $dates["date11"] = date("Ym", strtotime($objectYm . "+4 month"));
    $params["opendatefrom"] = $dates["date1"];
    $params["opendateto"] = $dates["date11"];
    $params["currencyclass"] = $currencyClass;

    // 支払先月別の合計金額を取得する
    if ($type == 3) {
        $totalPriceByPayfDateLst = fncGetSumOfMoneypriceByPayfAndOpenDate($objDB, $params, $type);
    } else {
        $totalPriceByPayfDateLst = fncGetSumOfMoneypriceByPayfAndShipDate($objDB, $params, $type);
    }
    // 支払先別の合計金額を取得する
    $totalPriceByPayfLst = fncGetSumOfMoneypriceByPayf($objDB, $params, $type);

    // 帳票Bene月別合計テーブルのデータを全件削除する
    fncDeleteReportByBeneMonthCal($objDB);

    if ($totalPriceByPayfDateLst && $totalPriceByPayfLst && count($totalPriceByPayfLst) > 0 && count($totalPriceByPayfDateLst) > 0) {
        foreach ($totalPriceByPayfLst as $totalPriceByPayf) {

            $payfcd = $totalPriceByPayf["payfcd"];

            $insertData["beneficiary"] = $totalPriceByPayf["payfnameformal"];
            $insertData["total"] = $totalPriceByPayf["totalmoneyprice"];
            $total = 0;

            foreach ($totalPriceByPayfDateLst as $totalPriceByPayfDate) {

                if ($totalPriceByPayfDate["payfcd"] == $payfcd) {
                    if ($type == 3) {
                        $opendate = $totalPriceByPayfDate["opendate"];
                    } else {
                        $opendate = $totalPriceByPayfDate["shipym"];
                    }
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

        // ページ数の取得
        $pageNum = ceil(count($totalPriceByPayfLst) / REPORT_LC_ONE_PAGE_REPORT_NUM_4);

        for ($i = 0; $i < $pageNum; $i++) {
            $startRow = 1 + ($pageNo + $i - 1) * 23;
            fncSetReportFour($objDB, $spreadsheet, $sheetName, $currencyClass, $objectYm, $type, $startRow, $i);
        }

        $pageNo = $pageNo + $pageNum;
    }

    return $pageNo;

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
function reportFiveOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $data, $pageNo)
{
    $insertData = array();
    $pageNum_1 = 0;
    $pageNum_2 = 0;
    // （臨時テーブル）帳票未決済額テーブルデータを全件削除する
    fncDeleteReportUnSettedPrice($objDB);
    // L/C情報取得
    $lcinfoLst = fncGetLcInfoForReportFive($objDB, $data["startDate"], $data["endDate"], $currencyClass, 1);

    // （臨時テーブル）帳票未決済額テーブルにデータを登録する
    if ($lcinfoLst && count($lcinfoLst) > 0) {
        $insertData = array();
        $manageno = 1;
        foreach ($lcinfoLst as $lcinfo) {
            $insertData["managementno"] = $manageno;
            $insertData["bankname"] = $lcinfo["bankname"];
            $insertData["payeeformalname"] = $lcinfo["payfnameformal"];
            $insertData["shipstartdate"] = $lcinfo["shipstartdate"];
            $insertData["lcno"] = $lcinfo["lcno"];
            $insertData["productcode"] = $lcinfo["productcd"] . "_" . $lcinfo["productrevisecd"];
            $insertData["usancesettlement"] = $lcinfo["moneyprice"] -
                ($lcinfo["bldetail1money"] + $lcinfo["bldetail2money"] + $lcinfo["bldetail3money"]);

            fncInsertReportUnSettedPrice($objDB, $insertData);
            $manageno += 1;
            unset($insertData);
        }

        // ページ数の取得
        $pageNum_2 = ceil(count($lcinfoLst) / REPORT_LC_ONE_PAGE_REPORT_NUM_5_2);
    }

    // （臨時テーブル）帳票未決済額未承認テーブルデータを全件削除する
    fncDeleteReportUnSettedPriceUnapproval($objDB);

    // L/C情報取得
    $lcinfoUnapprovalLst = fncGetLcInfoForReportFive($objDB, $data["startDate"], $data["endDate"], $currencyClass, 2);

    // （臨時テーブル）帳票未決済額未承認テーブルにデータを登録する
    if ($lcinfoUnapprovalLst && count($lcinfoUnapprovalLst) > 0) {
        unset($insertData);
        foreach ($lcinfoUnapprovalLst as $lcinfoUnapproval) {
            $insertData["payeeformalname"] = $lcinfoUnapproval["payfnameformal"];
            $insertData["unsettledprice"] = $lcinfoUnapproval["moneyprice"] -
                ($lcinfoUnapproval["bldetail1money"] + $lcinfoUnapproval["bldetail2money"] + $lcinfoUnapproval["bldetail3money"]);

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
    if ($totalUnSettedPriceByPayfLst && count($totalUnSettedPriceByPayfLst) > 0) {
        foreach ($totalUnSettedPriceByPayfLst as $totalUnSettedPriceByPayf) {
            $payeeformalname = $totalUnSettedPriceByPayf["payeeformalname"];
            $insertData["beneficiary"] = $payeeformalname;
            $insertData["total"] = $totalUnSettedPriceByPayf["totalmoneyprice"];
            foreach ($totalUnSettedPriceByPayfBankLst as $totalUnSettedPriceByPayfBank) {
                if ($totalUnSettedPriceByPayfBank["payeeformalname"] == $payeeformalname) {
                    $bankname = $totalUnSettedPriceByPayfBank["bankname"];
                    $totalmoneyprice = $totalUnSettedPriceByPayfBank["totalmoneyprice"];
                    if ($bankname == trim($bankLst[0]["bankomitname"])) {
                        $insertData["bank1"] = $totalmoneyprice;
                    } else if ($bankname == trim($bankLst[1]["bankomitname"])) {
                        $insertData["bank2"] = $totalmoneyprice;
                    } else if ($bankname == trim($bankLst[2]["bankomitname"])) {
                        $insertData["bank3"] = $totalmoneyprice;
                    } else if ($bankname == trim($bankLst[3]["bankomitname"])) {
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
        // ページ数の取得
        $pageNum_1 = ceil(count($totalUnSettedPriceByPayfLst) / REPORT_LC_ONE_PAGE_REPORT_NUM_5_1);
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
    }

    if ($pageNum_1 > $pageNum_2) {
        $pageNum = $pageNum_1;
    } else {
        $pageNum = $pageNum_2;
    }

    for ($i = 0; $i < $pageNum; $i++) {
        $startRow = 1 + ($pageNo + $i - 1) * 49;

        fncSetReportFive($objDB, $spreadsheet, "5", $currencyClass, $bankLst, $data["startDate"], $data["endDate"], $rate, $startRow, $i);

    }

    $pageNo = $pageNo + $pageNum;

    return $pageNo;
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
function reportSixOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $data, $pageNo)
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

        // ページ数の取得
        $pageNum = ceil(count($lcinfoLst) / REPORT_LC_ONE_PAGE_REPORT_NUM_6);

        for ($i = 0; $i < $pageNum; $i++) {
            $startRow = 1 + ($pageNo + $i - 1) * 44;

            fncSetReportSix($objDB, $spreadsheet, "6", $currencyClass, $bankLst, $data, $startRow, $i);

        }

        $pageNo = $pageNo + $pageNum;

    }

    return $pageNo;
}
