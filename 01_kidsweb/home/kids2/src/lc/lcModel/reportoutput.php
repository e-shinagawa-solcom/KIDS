<?php

// phpspreadsheet�ѥå������򥤥�ݡ��Ȥ���
require PATH_HOME . "/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Writer\Xls as XlsWriter;


/**
 * Ģɼ_1_L/C Open����(Beneficiary��BK�̹��)�ƥ�ץ졼�����Ƥ�����
 *
 * @param [object] $objDB
 * @param [sheet] $spreadsheet
 * @param [string] $sheetname
 * @param [string] $currencyClass
 * @param [array] $bankLst
 * @param [string] $objectYm
 * @param [string] $type
 * @return void
 */
function fncSetReportOne($objDB, $spreadsheet, $sheetname, $currencyClass, $bankLst, $objectYm, $type)
{
    $clonedWorksheet = clone $spreadsheet->getSheetByName($sheetname);

    if ($type == 1) {
        $clonedWorksheet->getHeaderFooter()->setOddHeader(convertEncoding('L/C Open�����Beneficiary��Bk�̹�ס�Open��'));
        $copysheetname = $sheetname . "_" . $currencyClass . "Open��";
    } else {
        $clonedWorksheet->getHeaderFooter()->setOddHeader(convertEncoding('L/C Open�����Beneficiary��Bk�̹�ס����ѷ�'));
        $copysheetname = $sheetname . "_" . $currencyClass . "���ѷ�";
    }
    
    $clonedWorksheet->setTitle(convertEncoding($copysheetname));
    $spreadsheet->addSheet($clonedWorksheet);
    //    $clonedWorksheet->getHeaderFooter()->setOddFooter('��P��&N');
    //    $clonedWorksheet->getPageSetup()
    //        ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
    //    $clonedWorksheet->getPageSetup()
    //        ->setPaperSize(PageSetup::PAPERSIZE_A4);

    $clonedWorksheet->setCellValue('A4', convertEncoding(sprintf('%dǯ%d��', substr($objectYm, 0, 4), substr($objectYm, 4, 2))));
    $clonedWorksheet->setCellValue('F5', convertEncoding(sprintf('�̲߶�ʬ:%s', $currencyClass)));
    $clonedWorksheet->setCellValue('B7', convertEncoding($bankLst[0]["bankomitname"]));
    $clonedWorksheet->setCellValue('C7', convertEncoding($bankLst[1]["bankomitname"]));
    $clonedWorksheet->setCellValue('D7', convertEncoding($bankLst[2]["bankomitname"]));
    $clonedWorksheet->setCellValue('E7', convertEncoding($bankLst[3]["bankomitname"]));

    $sumofBenebkTotal = fncGetSumofBeneBkPrice($objDB);
    $clonedWorksheet->fromArray($sumofBenebkTotal, null, 'B27');

    $numberFormat = getNumberFormat($currencyClass);
    $clonedWorksheet->getStyle('B27:E27')->getNumberFormat()->setFormatCode($numberFormat);
    // $sheet->getCell('C27')->getStyle()->getNumberFormat()->setFormatCode($numberFormat);
    // $sheet->getCell('D27')->getStyle()->getNumberFormat()->setFormatCode($numberFormat);
    // $sheet->getCell('E27')->getStyle()->getNumberFormat()->setFormatCode($numberFormat);

    $benebkTotalLst = fncGetReportByBenebktotal($objDB);
    if ($benebkTotalLst && count($benebkTotalLst) > 0) {
        mb_convert_variables('UTF-8', 'EUC-JP', $benebkTotalLst);
        $clonedWorksheet->fromArray($benebkTotalLst, null, 'A8');
    }


}

/**
 * Ģɼ_2_L/C Open����(L/C�̹��)�ƥ�ץ졼�����Ƥ�����
 *
 * @param [object] $objDB
 * @param [sheet] $spreadsheet
 * @param [string] $sheetname
 * @param [string] $currencyClass
 * @param [string] $objectYm
 * @param [numeric] $totalPrice
 * @return void
 */
function fncSetReportTwo($objDB, $spreadsheet, $sheetname, $currencyClass, $objectYm, $totalPrice)
{
    $clonedWorksheet = clone $spreadsheet->getSheetByName($sheetname);
    $clonedWorksheet->setTitle(convertEncoding($sheetname . "_" . $currencyClass));
    $spreadsheet->addSheet($clonedWorksheet);
    $clonedWorksheet->setCellValue('A1', convertEncoding(sprintf('%dǯ%d��', substr($objectYm, 0, 4), substr($objectYm, 5, 2))));
    $clonedWorksheet->setCellValue('H2', convertEncoding(sprintf('�̲߶�ʬ:%s', $currencyClass)));

    
    $numberFormat = getNumberFormat($currencyClass);
    $clonedWorksheet->setCellValue('C2', $totalPrice);    
    $clonedWorksheet->getCell('C2')->getStyle()->getNumberFormat()->setFormatCode($numberFormat);

    $lcTotalLst = fncGetReportByLcTotal($objDB);
    
    if ($lcTotalLst && count($lcTotalLst) > 0) {
        mb_convert_variables('UTF-8', 'EUC-JP', $lcTotalLst);
        $clonedWorksheet->fromArray($lcTotalLst, null, 'A5');
    }
    $clonedWorksheet->getStyle('C5:C40')->getNumberFormat()->setFormatCode($numberFormat);
    
}

/**
 * Ģɼ_3_L/C Open����(L/C������)�ƥ�ץ졼�����Ƥ�����
 *
 * @param [object] $objDB
 * @param [sheet] $spreadsheet
 * @param [string] $sheetname
 * @param [string] $currencyClass
 * @param [string] $objectYm
 * @param [numeric] $totalPrice
 * @return void
 */
function fncSetReportThree($objDB, $spreadsheet, $sheetname, $currencyClass, $objectYm, $totalPrice)
{
    $clonedWorksheet = clone $spreadsheet->getSheetByName($sheetname);
    $clonedWorksheet->setTitle(convertEncoding($sheetname . "_" . $currencyClass));
    $spreadsheet->addSheet($clonedWorksheet);
    
    $clonedWorksheet->setCellValue('C1', convertEncoding(sprintf('%dǯ%d��', substr($objectYm, 0, 4), substr($objectYm, 5, 2))));
    $clonedWorksheet->setCellValue('Q1', convertEncoding(sprintf('�̲߶�ʬ:%s', $currencyClass)));
    
    $numberFormat = getNumberFormat($currencyClass);
    $clonedWorksheet->setCellValue('I1', $totalPrice);    
    $clonedWorksheet->getCell('I1')->getStyle()->getNumberFormat()->setFormatCode($numberFormat);

    $lcDetailLst = fncGetReportByLcDetail($objDB);
    
    if ($lcDetailLst && count($lcDetailLst) > 0) {
        mb_convert_variables('UTF-8', 'EUC-JP', $lcDetailLst);
        $clonedWorksheet->fromArray($lcDetailLst, null, 'A3');
    }
    
    $clonedWorksheet->getStyle('I3:I47')->getNumberFormat()->setFormatCode($numberFormat);
}

/**
 * Ģɼ_4_L/C Open�����Beneficiary��L/Cȯ��ͽ�꽸��ɽ�˥ƥ�ץ졼�����Ƥ�����
 *
 * @param [object] $objDB
 * @param [sheet] $spreadsheet
 * @param [string] $sheetname
 * @param [string] $currencyClass
 * @param [string] $objectYm
 * @param [string] $type
 * @return void
 */
function fncSetReportFour($objDB, $spreadsheet, $sheetname, $currencyClass, $objectYm, $type)
{
    $clonedWorksheet = clone $spreadsheet->getSheetByName($sheetname);

    if ($type == 3) {
        $clonedWorksheet->getHeaderFooter()->setOddHeader(convertEncoding('Open�Beneficiary��L/Cȯ��ͽ�꽸��ɽ'));
        $copysheetname = $sheetname . "_" . $currencyClass . "Open��";
    } else if ($type == 4) {
        $clonedWorksheet->getHeaderFooter()->setOddHeader(convertEncoding('���ѷBeneficiary��L/Cȯ��ͽ�꽸��ɽ'));
        $copysheetname = $sheetname . "_" . $currencyClass . "���ѷ�";
    }

    $clonedWorksheet->setTitle(convertEncoding($copysheetname));
    $spreadsheet->addSheet($clonedWorksheet);
    $clonedWorksheet->setCellValue('M1', convertEncoding(sprintf('�̲߶�ʬ:%s', $currencyClass)));    
    $objectYm = str_replace("/", "-", $objectYm) . "-01";
    $clonedWorksheet->setCellValue('B3', convertEncoding(date("Yǯm��", strtotime($objectYm . "-6 month"))));
    $clonedWorksheet->setCellValue('C3', convertEncoding(date("Yǯm��", strtotime($objectYm . "-5 month"))));
    $clonedWorksheet->setCellValue('D3', convertEncoding(date("Yǯm��", strtotime($objectYm . "-4 month"))));
    $clonedWorksheet->setCellValue('E3', convertEncoding(date("Yǯm��", strtotime($objectYm . "-3 month"))));
    $clonedWorksheet->setCellValue('F3', convertEncoding(date("Yǯm��", strtotime($objectYm . "-2 month"))));
    $clonedWorksheet->setCellValue('G3', convertEncoding(date("Yǯm��", strtotime($objectYm . "-1 month"))));
    $clonedWorksheet->setCellValue('H3', convertEncoding(date("Yǯm��", strtotime($objectYm . "0 month"))));
    $clonedWorksheet->setCellValue('I3', convertEncoding(date("Yǯm��", strtotime($objectYm . "+1 month"))));
    $clonedWorksheet->setCellValue('J3', convertEncoding(date("Yǯm��", strtotime($objectYm . "+2 month"))));
    $clonedWorksheet->setCellValue('K3', convertEncoding(date("Yǯm��", strtotime($objectYm . "+3 month"))));
    $clonedWorksheet->setCellValue('L3', convertEncoding(date("Yǯm��", strtotime($objectYm . "+4 month"))));

    $sumofBeneMonthCal = fncGetSumofBeneMonCal($objDB);
    $clonedWorksheet->setCellValue('B23', $sumofBeneMonthCal->sum_1);
    $clonedWorksheet->setCellValue('C23', $sumofBeneMonthCal->sum_2);
    $clonedWorksheet->setCellValue('D23', $sumofBeneMonthCal->sum_3);
    $clonedWorksheet->setCellValue('E23', $sumofBeneMonthCal->sum_4);
    $clonedWorksheet->setCellValue('F23', $sumofBeneMonthCal->sum_5);
    $clonedWorksheet->setCellValue('G23', $sumofBeneMonthCal->sum_6);
    $clonedWorksheet->setCellValue('H23', $sumofBeneMonthCal->sum_7);
    $clonedWorksheet->setCellValue('I23', $sumofBeneMonthCal->sum_8);
    $clonedWorksheet->setCellValue('J23', $sumofBeneMonthCal->sum_9);
    $clonedWorksheet->setCellValue('K23', $sumofBeneMonthCal->sum_10);
    $clonedWorksheet->setCellValue('L23', $sumofBeneMonthCal->sum_11);
    $clonedWorksheet->setCellValue('M23', $sumofBeneMonthCal->sum_12);

    
    $beneMonthCalLst = fncGetReportByBeneMonthCal($objDB);
    
    if ($beneMonthCalLst && count($beneMonthCalLst) > 0) {
        mb_convert_variables('UTF-8', 'EUC-JP', $beneMonthCalLst);
        $clonedWorksheet->fromArray($beneMonthCalLst, null, 'A4');
    }
    
    $numberFormat = getNumberFormat($currencyClass);
    $clonedWorksheet->getStyle('B4:M23')->getNumberFormat()->setFormatCode($numberFormat);
}


/**
 * Ģɼ_5_̤��ѥꥹ�ȥƥ�ץ졼�����Ƥ�����
 *
 * @param [type] $objDB
 * @param [type] $spreadsheet
 * @param [type] $sheetname
 * @param [type] $currencyClass
 * @param [type] $bankLst
 * @param [type] $startYmd
 * @param [type] $endYmd
 * @param [type] $rate
 * @return void
 */
function fncSetReportFive($objDB, $spreadsheet, $sheetname, $currencyClass, $bankLst, $startYmd, $endYmd, $rate)
{
    // $reader = new XlsReader();
    // $filepath = REPORT_TMPDIR . REPORT_LC_TMPFILE;
    // $spreadsheet = $reader->load($filepath); //template.xlsx �ɹ�
    $clonedWorksheet = clone $spreadsheet->getSheetByName($sheetname);
    $clonedWorksheet->setTitle(convertEncoding($sheetname . "_" . $currencyClass));
    $spreadsheet->addSheet($clonedWorksheet);

    $clonedWorksheet->setCellValue('M3', convertEncoding("�̲߶�ʬ��" . $currencyClass));
    $clonedWorksheet->setCellValue('B2', $startYmd);
    $clonedWorksheet->setCellValue('B3', $endYmd);
    $clonedWorksheet->setCellValue('B3', $endYmd);

    $unSettedLst = fncGetUnSettedLst($objDB);
    if ($unSettedLst && count($unSettedLst) > 0) {
        $clonedWorksheet->fromArray(convertEncoding($unSettedLst), null, 'A5');
    }
    $unSettedTotal = fncGetUnSettedTotal($objDB);
    if ($unSettedTotal && count($unSettedTotal) > 0) {
        // mb_convert_variables('UTF-8', 'EUC-JP', $unSettedTotal);
        $clonedWorksheet->fromArray($unSettedTotal, null, 'B23');
    }

    $clonedWorksheet->setCellValue('A26', sprintf("%01.2f", $rate));

    $clonedWorksheet->setCellValue('B4', convertEncoding($bankLst[0]["bankomitname"]));
    $clonedWorksheet->setCellValue('C4', convertEncoding($bankLst[1]["bankomitname"]));
    $clonedWorksheet->setCellValue('D4', convertEncoding($bankLst[2]["bankomitname"]));
    $clonedWorksheet->setCellValue('E4', convertEncoding($bankLst[3]["bankomitname"]));

    $clonedWorksheet->setCellValue('B26', moneyFormat($unSettedTotal[0]["bank1total"] * $raterate, $currencyClass));
    $clonedWorksheet->setCellValue('C26', moneyFormat($unSettedTotal[0]["bank2total"] * $raterate, $currencyClass));
    $clonedWorksheet->setCellValue('D26', moneyFormat($unSettedTotal[0]["bank3total"] * $raterate, $currencyClass));
    $clonedWorksheet->setCellValue('E26', moneyFormat($unSettedTotal[0]["bank4total"] * $raterate, $currencyClass));
    $clonedWorksheet->setCellValue('F26', moneyFormat($unSettedTotal[0]["unapprovaltotaltotal"] * $raterate, $currencyClass));
    $clonedWorksheet->setCellValue('G26', moneyFormat($unSettedTotal[0]["benetotaltotal"] * $raterate, $currencyClass));

    $unSettedPriceLst = fncGetReportUnSettedPrice($objDB);

    if ($unSettedPriceLst && count($unSettedPriceLst) > 0) {

        mb_convert_variables('UTF-8', 'EUC-JP', $unSettedPriceLst);
        $clonedWorksheet->fromArray($unSettedPriceLst, null, 'H5');
    }
    // $writer = new XlsWriter($spreadsheet);
    // $writer->save(REPORT_TMPDIR . REPORT_LC_OUTPUTFILE);

    //�����������
    // // Redirect output to a client��s web browser (Xlsx)
    // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    // header('Content-Disposition: attachment;filename="01simple.xls"');
    // header('Expires: 0'); // Date in the past
    // header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    // header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    // header('Pragma: public'); // HTTP/1.0

    // $writer = new XlsWriter($spreadsheet);

    // $writer->save('php://output');

}

/**
 * Ģɼ_6_͢�����Ѿ�����ƥ�ץ졼�����Ƥ�����
 *
 * @param [object] $objDB
 * @param [sheet] $spreadsheet
 * @param [string] $sheetname
 * @param [string] $currencyClass
 * @param [array] $bankLst
 * @param [array] $data
 * @return void
 */
function fncSetReportSix($objDB, $spreadsheet, $sheetname, $currencyClass, $bankLst, $data)
{
    $clonedWorksheet = clone $spreadsheet->getSheetByName($sheetname);
    $clonedWorksheet->setTitle(convertEncoding($sheetname . "_" . $currencyClass));
    $spreadsheet->addSheet($clonedWorksheet);

    if (strcmp($currencyClass, '��') == 0)
    {
        $clonedWorksheet->setCellValue('H10', convertEncoding("��ۡʡ��"));
    }
    $clonedWorksheet->setCellValue('P9', convertEncoding("�̲߶�ʬ��" . $currencyClass));
    $clonedWorksheet->setCellValue('B7', convertEncoding(sprintf('%dǯ%d��', substr($data["openYm"], 0, 4), substr($data["openYm"], 4, 2))));
    $clonedWorksheet->setCellValue('M8', convertEncoding(sprintf('%dǯ%d��', substr($data["shipYm"], 0, 4), substr($data["shipYm"], 4, 2))));

    $clonedWorksheet->setCellValue('D8', convertEncoding($data["payfName"]));
    $clonedWorksheet->setCellValue('H8', convertEncoding($data["bankname"]));

    if (strcmp($data["bankname"], 'ALL') == 0)
    {  
        $clonedWorksheet->setCellValue('P10', convertEncoding("ͽ����"));
    }
    $clonedWorksheet->setCellValue('L8', convertEncoding($data["lcopen"]));
    $clonedWorksheet->setCellValue('O8', convertEncoding($data["portplace"]));

    $sendLst = fncGetSendInfo($objDB); 

    if ($sendLst && count($sendLst) > 0) {
        $clonedWorksheet->setCellValue('B3', convertEncoding($sendLst[0]["lccautionstatement1"]));
        $clonedWorksheet->setCellValue('B4', convertEncoding($sendLst[0]["lccautionstatement2"]));
        $clonedWorksheet->setCellValue('G2', convertEncoding($sendLst[0]["lcsender"]));
        $clonedWorksheet->setCellValue('H3', convertEncoding($sendLst[0]["senderfax"]));
    }

    $payfInfo = fncGetPayfInfoByPayfcd($objDB, $data["payfCode"]);
    
    if ($payfInfo != null) {
        $clonedWorksheet->setCellValue('B2', convertEncoding($payfInfo->payfsendname));
        $clonedWorksheet->setCellValue('H42', convertEncoding($payfInfo->payfsendfax));
    }

    $sumofILOP = fncGetSumofImpLcOrderPrice($objDB);
    
    $clonedWorksheet->setCellValue('H27', $sumofILOP);
    $numberFormat = getNumberFormat($currencyClass);
    $clonedWorksheet->getStyle('H27')->getNumberFormat()->setFormatCode($numberFormat);

    $ilopLst = fncGetReportImpLcOrderInfo($objDB);

    if ($ilopLst && count($ilopLst) > 0) {
        mb_convert_variables('UTF-8', 'EUC-JP', $ilopLst);
        $clonedWorksheet->fromArray($ilopLst, null, 'A12');
    }
    $clonedWorksheet->getStyle('H12:H26')->getNumberFormat()->setFormatCode($numberFormat);

}
function convertEncoding($str)
{
    return mb_convert_encoding($str, 'UTF-8', 'EUC-JP');
}

/**
 * �����ե����ޥåȤμ���
 *
 * @param [string] $currencyClass
 * @return string
 */
function getNumberFormat($currencyClass)
{
    if (strcmp($currencyClass, '��') == 0) {
        $numberFormat = '#,##0';
    } else {
        $numberFormat = '#,##0.00';
    }
    return $numberFormat;
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
