<?php

/**
 * Ģɼ�ʥ����ץ���_���ν���
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

    // ��ʧ�����̤ι�׶�ۤ��������
    $totalPriceByPayfBankLst = fncGetSumOfMoneypriceByPayfAndBank($objDB, $params, $type);

    // ��ʧ���̤ι�׶�ۤ��������
    $totalPriceByPayfLst = fncGetSumOfMoneypriceByPayf($objDB, $params, $type);

    // ĢɼBeneBk�̹�ץơ��֥�Υǡ���������������
    fncDeleteReportByBenebktotal($objDB);

    // ���׻��ơ��֥��ĢɼBeneB���̹�ץơ��֥�˥ǡ�������Ͽ����
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

            // ĢɼBeneB���̹�ץơ��֥����Ͽ����
            fncInsertReportByBenebktotal($objDB, $insertData);

            unset($insertData);

        }

        // �ƥ�ץ졼�Ȥؤν��Ͼ�������ꤹ��
        if ($type == 1) {
            $header['header'] = convertEncoding('L/C Open�����Beneficiary��Bk�̹�ס�Open��');
        } else {
            $header['header'] = convertEncoding('L/C Open�����Beneficiary��Bk�̹�ס����ѷ�');
        }
        $header['A4'] = convertEncoding(sprintf('%dǯ%d��', substr($objectYm, 0, 4), substr($objectYm, 5, 2)));
        $header['F5'] = convertEncoding(sprintf('�̲߶�ʬ:%s', $currencyClass));
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
 * Ģɼ_2�ν���
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
    // Ģɼ�����Ѥ�L/C�̹��
    $lcinfoLst = fncGetLcInfoForReportTwo($objDB, str_replace("/", "", $objectYm), $currencyClass);

    // ���׻��ơ��֥��ĢɼLC�̹�ץơ��֥�Υǡ���������������
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
            // ��׶�ۤ�����
            $priceTotal += $lcinfo["moneyprice"];

            // ���׻��ơ��֥��ĢɼLC�̹�ץơ��֥�˥ǡ�������Ͽ����
            fncInsertReportByLcTotal($objDB, $insertData);

            unset($insertData);
        }

        // �ƥ�ץ졼�Ȥν���
        $header['A1'] = convertEncoding(sprintf('(%dǯ%d��)', substr($objectYm, 0, 4), substr($objectYm, 5, 2)));
        $header['H2'] = convertEncoding(sprintf('�̲߶�ʬ:%s', $currencyClass));
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
 * Ģɼ_3�ν���
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
    // Ģɼ�����Ѥ�L/C�̹��
    $lcinfoLst = fncGetLcInfoForReportTwo($objDB, str_replace("/", "", $objectYm), $currencyClass);

    // ���׻��ơ��֥��ĢɼLC�����٥ơ��֥�Υǡ���������������
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
            // ��׶�ۤ�����
            $priceTotal += $lcinfo["moneyprice"];

            // ���׻��ơ��֥��ĢɼLC�̹�ץơ��֥�˥ǡ�������Ͽ����
            fncInsertReportByLcDetail($objDB, $insertData);
        }

        // �ƥ�ץ졼�Ȥν���
        $header['C1'] = convertEncoding(sprintf('%dǯ%d��', substr($objectYm, 0, 4), substr($objectYm, 5, 2)));
        $header['Q1'] = convertEncoding(sprintf('�̲߶�ʬ:%s', $currencyClass));
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
 * Ģɼ_4�ν���
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

    // ��ʧ����̤ι�׶�ۤ��������
    if ($type == 3) {
        $totalPriceByPayfDateLst = fncGetSumOfMoneypriceByPayfAndOpenDate($objDB, $params, $type);
    } else {
        $totalPriceByPayfDateLst = fncGetSumOfMoneypriceByPayfAndShipDate($objDB, $params, $type);
    }
    
    // ��ʧ���̤ι�׶�ۤ��������
    $totalPriceByPayfLst = fncGetSumOfMoneypriceByPayf($objDB, $params, $type);

    // ĢɼBene���̹�ץơ��֥�Υǡ���������������
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
            // ���׻��ơ��֥��ĢɼBene���̹�ץơ��֥�˥ǡ�������Ͽ����
            fncInsertReportByBeneMonthCal($objDB, $insertData);

            unset($insertData);

        }

        // �ƥ�ץ졼�Ȥν���
        if ($type == 3) {
            $header['header'] = convertEncoding('Open�Beneficiary��L/Cȯ��ͽ�꽸��ɽ');
        } else if ($type == 4) {
            $header['header'] = convertEncoding('���ѷBeneficiary��L/Cȯ��ͽ�꽸��ɽ');
        }

        $header['M1'] = convertEncoding(sprintf('�̲߶�ʬ:%s', $currencyClass));
        $objectYm = str_replace("/", "-", $objectYm) . "-01";
        $header['B3'] = convertEncoding(date("Yǯm��", strtotime($objectYm . "-6 month")));
        $header['C3'] = convertEncoding(date("Yǯm��", strtotime($objectYm . "-5 month")));
        $header['D3'] = convertEncoding(date("Yǯm��", strtotime($objectYm . "-4 month")));
        $header['E3'] = convertEncoding(date("Yǯm��", strtotime($objectYm . "-3 month")));
        $header['F3'] = convertEncoding(date("Yǯm��", strtotime($objectYm . "-2 month")));
        $header['G3'] = convertEncoding(date("Yǯm��", strtotime($objectYm . "-1 month")));
        $header['H3'] = convertEncoding(date("Yǯm��", strtotime($objectYm)));
        $header['I3'] = convertEncoding(date("Yǯm��", strtotime($objectYm . "+1 month")));
        $header['J3'] = convertEncoding(date("Yǯm��", strtotime($objectYm . "+2 month")));
        $header['K3'] = convertEncoding(date("Yǯm��", strtotime($objectYm . "+3 month")));
        $header['L3'] = convertEncoding(date("Yǯm��", strtotime($objectYm . "+4 month")));

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
 * Ģɼ_5�ν���
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
    // ���׻��ơ��֥��Ģɼ̤��ѳۥơ��֥�ǡ���������������
    fncDeleteReportUnSettedPrice($objDB);
    // L/C�������
    $lcinfoLst = fncGetLcInfoForReportFive($objDB, $data["startDate"], $data["endDate"], $currencyClass, 1);

    // ���׻��ơ��֥��Ģɼ̤��ѳۥơ��֥�˥ǡ�������Ͽ����
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

    // ���׻��ơ��֥��Ģɼ̤��ѳ�̤��ǧ�ơ��֥�ǡ���������������
    fncDeleteReportUnSettedPriceUnapproval($objDB);

    // L/C�������
    $lcinfoUnapprovalLst = fncGetLcInfoForReportFive($objDB, $data["startDate"], $data["endDate"], $currencyClass, 2);

    // ���׻��ơ��֥��Ģɼ̤��ѳ�̤��ǧ�ơ��֥�˥ǡ�������Ͽ����
    if ($lcinfoUnapprovalLst && count($lcinfoUnapprovalLst) > 0) {
        foreach ($lcinfoUnapprovalLst as $lcinfoUnapproval) {
            $insertData["payeeformalname"] = $lcinfoUnapproval["payfnameformal"];
            $insertData["unsettledprice"] = $lcinfoUnapproval["moneyprice"] -
                ($lcinfo["bldetail1money"] + $lcinfoUnapproval["bldetail2money"] + $lcinfoUnapproval["bldetail3money"]);

            fncInsertReportUnSettedPriceUnapproval($objDB, $insertData);
            unset($insertData);
        }
    }

    // ĢɼBeneBK�̹�ץơ��֥�ǡ���������������
    fncDeleteReportByBenebktotal($objDB);

    // ��ʧ�����̤ι�׶�ۤ��������
    $totalUnSettedPriceByPayfBankLst = fncGetSumofUnSettedPriceByPayfAndBank($objDB);

    // ��ʧ�����̤ι�׶�ۤ��������
    $totalUnSettedPriceByPayfLst = fncGetSumofUnSettedPriceByPayf($objDB);

    // ���׻��ơ��֥��ĢɼBeneBk�̹�ץơ��֥�˥ǡ�������Ͽ����
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

            // ĢɼBeneBk�̹�ץơ��֥����Ͽ����
            fncInsertReportByBenebktotal($objDB, $insertData);

            unset($insertData);
        }
    }

    // �졼�Ȥ����ꤹ��
    $rate = 0;
    if ($data["rate"] != "" && $data["rate"] > 0) {
        $rate = $data["rate"];
    } else {
        // �ѥ�᡼�����̲߶�ʬ��̤��ǧ�ޤ�ˤ��ɱߡɤξ�硢�̲߶�ʬ = 1
        if ($currencyClass == "��") {
            $monetaryUnitCode = DEF_MONETARY_YEN;

            // �ѥ�᡼�����̲߶�ʬ��̤��ǧ�ޤ�ˤ���US�ɥ�ɤξ�硢�̲߶�ʬ = 2
        } else if ($currencyClass == "�գӥɥ�") {
            $monetaryUnitCode = DEF_MONETARY_USD;

            // �ѥ�᡼�����̲߶�ʬ��̤��ǧ�ޤ�ˤ���HK�ɥ�ɤξ�硢�̲߶�ʬ = 3
        } else if ($currencyClass == "�ȣ˥ɥ�") {
            $monetaryUnitCode = DEF_MONETARY_HKD;
        }
        // �̲߶�ʬ��̤��ǧ�ޤ�ˤ��ɱߡɤξ�硢
        if ($currencyClass == "��") {
            $rate = 0;
        } else {
            $rate = fncGetMonetaryRate($objDB, DEF_MONETARYCLASS_SHANAI, $monetaryUnitCode);
        }
    }

    if ($lcinfoLst && count($lcinfoLst) > 0) {

        // �ƥ�ץ졼�Ȥν���
        $header['M3'] = convertEncoding("�̲߶�ʬ��" . $currencyClass);
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
 * Ģɼ_6�ν���
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
    // L/C������������
    $lcinfoLst = fncGetLcInfoForReportSix($objDB, $currencyClass, $data);

    // ���׻��ơ��֥��Ģɼ͢�����Ѿ�ȯ�Ծ���ơ��֥���ǡ���������������
    fncDeleteReportImpLcOrderInfo($objDB);

    // �嵭��������LC�����Ģɼ͢�����Ѿ�ȯ�Ծ���ơ��֥����Ͽ����
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

        // �ƥ�ץ졼�Ȥν���
        if (strcmp($currencyClass, '��') == 0) {
            $header["H10"] = convertEncoding("��ۡʡ��");
        }
        $header["P9"] = convertEncoding("�̲߶�ʬ��" . $currencyClass);
        $header["B7"] = convertEncoding(sprintf('%dǯ%d��', substr($data["openYm"], 0, 4), substr($data["openYm"], 5, 2)));
        $header["M8"] = convertEncoding(sprintf('%dǯ%d��', substr($data["shipYm"], 0, 4), substr($data["shipYm"], 5, 2)));
        $header["D8"] = convertEncoding($data["payfName"]);
        $header["H8"] = convertEncoding($data["bankname"]);
        if (strcmp($data["bankname"], 'ALL') == 0) {
            $header["P10"] = convertEncoding("ͽ����");
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
 * ��ۥե����ޥå��Ѵ�
 *
 * @param [numeric] $money
 * @param [string] $currencyClass
 * @return number
 */
function moneyFormat($money, $currencyClass)
{
    if ($currencyClass == "��") {
        return number_format($money);
    } else {
        return number_format($money, 2, '.', ',');
    }
}