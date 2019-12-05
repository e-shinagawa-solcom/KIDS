<?php

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
    $result = array();
    $header = array();
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

        // テンプレートへの出力情報を設定する
        if ($type == 1) {
            $header['header'] = convertEncoding('L/C Open情報（Beneficiary・Bk別合計）Open月');
        } else {
            $header['header'] = convertEncoding('L/C Open情報（Beneficiary・Bk別合計）船積月');
        }
        $header['A4'] = convertEncoding(sprintf('%d年%d月', substr($objectYm, 0, 4), substr($objectYm, 5, 2)));
        $header['F5'] = convertEncoding(sprintf('通貨区分:%s', $currencyClass));
        $header['B7'] = convertEncoding($bankLst[0]["bankomitname"]);
        $header['C7'] = convertEncoding($bankLst[1]["bankomitname"]);
        $header['D7'] = convertEncoding($bankLst[2]["bankomitname"]);
        $header['E7'] = convertEncoding($bankLst[3]["bankomitname"]);
        $sumofBenebkTotal = fncGetSumofBeneBkPrice($objDB);
        $header['B27'] = $sumofBenebkTotal->sum_1;
        $header['C27'] = $sumofBenebkTotal->sum_2;
        $header['D27'] = $sumofBenebkTotal->sum_3;
        $header['E27'] = $sumofBenebkTotal->sum_4;
        $header['F27'] = $sumofBenebkTotal->sum_5;
        $header['currencyclass'] = $currencyClass;


        mb_convert_variables('UTF-8' , 'EUC-JP' , $header);
        $result['report_header'] = $header;
        $benebkTotalLst = fncGetReportByBenebktotal($objDB);
        
        for ($i = 0; $i < count($benebkTotalLst); $i++) {
            mb_convert_variables('UTF-8' , 'EUC-JP' , $benebkTotalLst[$i] );
        }
        $result['report_main'] = $benebkTotalLst;
    }

    return $result;
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
        $header['A1'] = convertEncoding(sprintf('(%d年%d月)', substr($objectYm, 0, 4), substr($objectYm, 5, 2)));
        $header['H2'] = convertEncoding(sprintf('通貨区分:%s', $currencyClass));
        $header['C2'] = $totalPrice;
        $header['currencyclass'] = $currencyClass;

        mb_convert_variables('UTF-8' , 'EUC-JP' , $header);
        $result['report_header'] = $header;

        $lcTotalLst = fncGetReportByLcTotal($objDB);

        if ($lcTotalLst && count($lcTotalLst) > 0) {
            
            for ($i = 0; $i < count($lcTotalLst); $i++) {
                mb_convert_variables('UTF-8' , 'EUC-JP' , $lcTotalLst[$i] );
            }
            $result['report_main'] = $lcTotalLst;
        }

    }
    return $result;
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
    $result = array();
    $header = array();
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
        $header['C1'] = convertEncoding(sprintf('%d年%d月', substr($objectYm, 0, 4), substr($objectYm, 5, 2)));
        $header['Q1'] = convertEncoding(sprintf('通貨区分:%s', $currencyClass));
        $header['I1'] = $totalPrice;
        $header['currencyclass'] = $currencyClass;
        
        mb_convert_variables('UTF-8' , 'EUC-JP' , $header);
        $result['report_header'] = $header;
        $lcDetailLst = fncGetReportByLcDetail($objDB);
        if ($lcDetailLst && count($lcDetailLst) > 0) {
            
            for ($i = 0; $i < count($lcDetailLst); $i++) {
                mb_convert_variables('UTF-8' , 'EUC-JP' , $lcDetailLst[$i] );
            }
            $result['report_main'] = $lcDetailLst;
        }

    }
    return $result;

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
    $result = array();
    $header = array();
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

    if ($totalPriceByPayfLst && count($totalPriceByPayfLst) > 0 
        && $totalPriceByPayfDateLst && count($totalPriceByPayfDateLst) > 0) {
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

        // テンプレートの出力
        if ($type == 3) {
            $header['header'] = convertEncoding('Open月・Beneficiary別L/C発行予定集計表');
        } else if ($type == 4) {
            $header['header'] = convertEncoding('船積月・Beneficiary別L/C発行予定集計表');
        }

        $header['M1'] = convertEncoding(sprintf('通貨区分:%s', $currencyClass));
        $objectYm = str_replace("/", "-", $objectYm) . "-01";
        $header['B3'] = convertEncoding(date("Y年m月", strtotime($objectYm . "-6 month")));
        $header['C3'] = convertEncoding(date("Y年m月", strtotime($objectYm . "-5 month")));
        $header['D3'] = convertEncoding(date("Y年m月", strtotime($objectYm . "-4 month")));
        $header['E3'] = convertEncoding(date("Y年m月", strtotime($objectYm . "-3 month")));
        $header['F3'] = convertEncoding(date("Y年m月", strtotime($objectYm . "-2 month")));
        $header['G3'] = convertEncoding(date("Y年m月", strtotime($objectYm . "-1 month")));
        $header['H3'] = convertEncoding(date("Y年m月", strtotime($objectYm)));
        $header['I3'] = convertEncoding(date("Y年m月", strtotime($objectYm . "+1 month")));
        $header['J3'] = convertEncoding(date("Y年m月", strtotime($objectYm . "+2 month")));
        $header['K3'] = convertEncoding(date("Y年m月", strtotime($objectYm . "+3 month")));
        $header['L3'] = convertEncoding(date("Y年m月", strtotime($objectYm . "+4 month")));

        $sumofBeneMonthCal = fncGetSumofBeneMonCal($objDB);
        $header['B23'] = $sumofBeneMonthCal->sum_1;
        $header['C23'] = $sumofBeneMonthCal->sum_2;
        $header['D23'] = $sumofBeneMonthCal->sum_3;
        $header['E23'] = $sumofBeneMonthCal->sum_4;
        $header['F23'] = $sumofBeneMonthCal->sum_5;
        $header['G23'] = $sumofBeneMonthCal->sum_6;
        $header['H23'] = $sumofBeneMonthCal->sum_7;
        $header['I23'] = $sumofBeneMonthCal->sum_8;
        $header['J23'] = $sumofBeneMonthCal->sum_9;
        $header['K23'] = $sumofBeneMonthCal->sum_10;
        $header['L23'] = $sumofBeneMonthCal->sum_11;
        $header['M23'] = $sumofBeneMonthCal->sum_12;
        $header['currencyclass'] = $currencyClass;
        
        mb_convert_variables('UTF-8' , 'EUC-JP' , $header);
        $result['report_header'] = $header;

        $beneMonthCalLst = fncGetReportByBeneMonthCal($objDB);
        if ($beneMonthCalLst && count($beneMonthCalLst) > 0) {
            
            for ($i = 0; $i < count($beneMonthCalLst); $i++) {
                mb_convert_variables('UTF-8' , 'EUC-JP' , $beneMonthCalLst[$i] );
            }
            $result['report_main'] = $beneMonthCalLst;
        }
    }
    return $result;

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
    $result = array();
    $header = array();
    // （臨時テーブル）帳票未決済額テーブルデータを全件削除する
    fncDeleteReportUnSettedPrice($objDB);
    // L/C情報取得
    $lcinfoLst = fncGetLcInfoForReportFive($objDB, $data["startDate"], $data["endDate"], $currencyClass, 1);

    // （臨時テーブル）帳票未決済額テーブルにデータを登録する
    if ($lcinfoLst && count($lcinfoLst) > 0) {
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
    $lcinfoUnapprovalLst = fncGetLcInfoForReportFive($objDB, $data["startDate"], $data["endDate"], $currencyClass, 2);

    // （臨時テーブル）帳票未決済額未承認テーブルにデータを登録する
    if ($lcinfoUnapprovalLst && count($lcinfoUnapprovalLst) > 0) {
        foreach ($lcinfoUnapprovalLst as $lcinfoUnapproval) {
            $insertData["payeeformalname"] = $lcinfoUnapproval["payfnameformal"];
            $insertData["unsettledprice"] = $lcinfoUnapproval["moneyprice"] -
                ($lcinfo["bldetail1money"] + $lcinfoUnapproval["bldetail2money"] + $lcinfoUnapproval["bldetail3money"]);

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
        } else if ($currencyClass == "ＵＳドル") {
            $monetaryUnitCode = DEF_MONETARY_USD;

            // パラメータの通貨区分（未承認含む）が”HKドル”の場合、通貨区分 = 3
        } else if ($currencyClass == "ＨＫドル") {
            $monetaryUnitCode = DEF_MONETARY_HKD;
        }
        // 通貨区分（未承認含む）が”円”の場合、
        if ($currencyClass == "円") {
            $rate = 0;
        } else {
            $rate = fncGetMonetaryRate($objDB, DEF_MONETARYCLASS_SHANAI, $monetaryUnitCode);
        }
    }

    if ($lcinfoLst && count($lcinfoLst) > 0) {

        // テンプレートの出力
        $header['M3'] = convertEncoding("通貨区分：" . $currencyClass);
        $header['B2'] = $data["startDate"];
        $header['B3'] = $data["endDate"];

        $unSettedLst = fncGetUnSettedLst($objDB);
        if ($unSettedLst && count($unSettedLst) > 0) {            
            for ($i = 0; $i < count($unSettedLst); $i++) {
                mb_convert_variables('UTF-8' , 'EUC-JP' , $unSettedLst[$i] );
            }            
        }
        $header['A5'] = convertEncoding($unSettedLst);

        $unSettedTotal = fncGetUnSettedTotal($objDB);
        if ($unSettedTotal) {            
            $header['B23'] = $unSettedTotal->bank1total;
            $header['C23'] = $unSettedTotal->bank2total;
            $header['D23'] = $unSettedTotal->bank3total;
            $header['E23'] = $unSettedTotal->bank4total;
            $header['F23'] = $unSettedTotal->unapprovaltotaltotal;
            $header['G23'] = $unSettedTotal->benetotaltotal;
        }

        $header['A26'] = sprintf("%01.2f", $rate);

        $header['B4'] = convertEncoding($bankLst[0]["bankomitname"]);
        $header['C4'] = convertEncoding($bankLst[1]["bankomitname"]);
        $header['D4'] = convertEncoding($bankLst[2]["bankomitname"]);
        $header['E4'] = convertEncoding($bankLst[3]["bankomitname"]);

        $header['B26'] = $unSettedTotal->bank1total * $raterate;
        $header['C26'] = $unSettedTotal->bank2total * $raterate;
        $header['D26'] = $unSettedTotal->bank3total * $raterate;
        $header['E26'] = $unSettedTotal->bank4total * $raterate;
        $header['F26'] = $unSettedTotal->unapprovaltotaltotal * $raterate;
        $header['G26'] = $unSettedTotal->benetotaltotal * $raterate;
        
        $header['currencyclass'] = $currencyClass;
        mb_convert_variables('UTF-8' , 'EUC-JP' , $header);
        $result['report_header'] = $header;
        $unSettedPriceLst = fncGetReportUnSettedPrice($objDB);

        if ($unSettedPriceLst && count($unSettedPriceLst) > 0) {
            for ($i = 0; $i < count($unSettedPriceLst); $i++) {
                mb_convert_variables('UTF-8' , 'EUC-JP' , $unSettedPriceLst[$i] );
            }
            $result['report_main'] = $unSettedPriceLst;
        }
    }
    return $result;
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
    $result = array();
    $header = array();
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
        if (strcmp($currencyClass, '円') == 0) {
            $header["H10"] = convertEncoding("金額（￥）");
        }
        $header["P9"] = convertEncoding("通貨区分：" . $currencyClass);
        $header["B7"] = convertEncoding(sprintf('%d年%d月', substr($data["openYm"], 0, 4), substr($data["openYm"], 5, 2)));
        $header["M8"] = convertEncoding(sprintf('%d年%d月', substr($data["shipYm"], 0, 4), substr($data["shipYm"], 5, 2)));
        $header["D8"] = convertEncoding($data["payfName"]);
        $header["H8"] = convertEncoding($data["bankname"]);
        if (strcmp($data["bankname"], 'ALL') == 0) {
            $header["P10"] = convertEncoding("予定銀行");
        }
        $header["L8"] = convertEncoding($data["lcopen"]);
        $header["O8"] = convertEncoding($data["portplace"]);

        $sendLst = fncGetSendInfo($objDB);

        if ($sendLst && count($sendLst) > 0) {
            $header["B3"] = convertEncoding($sendLst[0]["sendcarenote1"]);
            $header["B4"] = convertEncoding($sendLst[0]["sendcarenote2"]);
            $header["G2"] = convertEncoding($sendLst[0]["sendfromname"]);
            $header["H3"] = convertEncoding($sendLst[0]["sendfromfax"]);
        }

        $payfInfo = fncGetPayfInfoByPayfcd($objDB, $data["payfCode"]);
        if ($payfInfo != null) {
            $header["B2"] = convertEncoding($payfInfo->payfsendname);
            $header["H42"] = convertEncoding($payfInfo->payfsendfax);
        }

        $header["H27"] = fncGetSumofImpLcOrderPrice($objDB);

        $header['currencyclass'] = $currencyClass;
        
        mb_convert_variables('UTF-8' , 'EUC-JP' , $header);

        $result['report_header'] = $header;

        $ilopLst = fncGetReportImpLcOrderInfo($objDB);

        if ($ilopLst && count($ilopLst) > 0) {
            for ($i = 0; $i < count($ilopLst); $i++) {
                mb_convert_variables('UTF-8' , 'EUC-JP' , $ilopLst[$i] );
            }
            $result['report_main'] = $ilopLst;
        }

    }
    return $result;
}

function convertEncoding($str)
{
    // return mb_convert_encoding($str, 'UTF-8', 'EUC-JP');
    return $str;
}

/**
 * 金額フォーマット変換
 *
 * @param [numeric] $money
 * @param [string] $currencyClass
 * @return number
 */
function moneyFormat($money, $currencyClass)
{
    if ($currencyClass == "円") {
        return number_format($money);
    } else {
        return number_format($money, 2, '.', ',');
    }
}