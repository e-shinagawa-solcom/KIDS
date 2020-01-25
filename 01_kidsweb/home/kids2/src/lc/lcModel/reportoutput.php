<?php

// phpspreadsheetパッケージをインポートする
require PATH_HOME . "/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Writer\Xls as XlsWriter;


/**
 * 帳票_1_L/C Open情報(Beneficiary・BK別合計)テンプレート内容の設定
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
        $clonedWorksheet->getHeaderFooter()->setOddHeader('L/C Open情報（Beneficiary・Bk別合計）Open月');
        $copysheetname = $sheetname . "_" . $currencyClass . "Open月";
    } else {
        $clonedWorksheet->getHeaderFooter()->setOddHeader('L/C Open情報（Beneficiary・Bk別合計）船積月');
        $copysheetname = $sheetname . "_" . $currencyClass . "船積月";
    }
    
    $clonedWorksheet->setTitle($copysheetname);
    $spreadsheet->addSheet($clonedWorksheet);
    //    $clonedWorksheet->getHeaderFooter()->setOddFooter('＆P／&N');
    //    $clonedWorksheet->getPageSetup()
    //        ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
    //    $clonedWorksheet->getPageSetup()
    //        ->setPaperSize(PageSetup::PAPERSIZE_A4);

    $clonedWorksheet->setCellValue('A4', sprintf('%d年%d月', substr($objectYm, 0, 4), substr($objectYm, 4, 2)));
    $clonedWorksheet->setCellValue('F5', sprintf('通貨区分:%s', $currencyClass));
    $clonedWorksheet->setCellValue('B7', $bankLst[0]["bankomitname"]);
    $clonedWorksheet->setCellValue('C7', $bankLst[1]["bankomitname"]);
    $clonedWorksheet->setCellValue('D7', $bankLst[2]["bankomitname"]);
    $clonedWorksheet->setCellValue('E7', $bankLst[3]["bankomitname"]);

    $sumofBenebkTotal = fncGetSumofBeneBkPrice($objDB);
    $clonedWorksheet->fromArray($sumofBenebkTotal, null, 'B27');

    $numberFormat = getNumberFormat($currencyClass);
    $clonedWorksheet->getStyle('B27:E27')->getNumberFormat()->setFormatCode($numberFormat);
    // $sheet->getCell('C27')->getStyle()->getNumberFormat()->setFormatCode($numberFormat);
    // $sheet->getCell('D27')->getStyle()->getNumberFormat()->setFormatCode($numberFormat);
    // $sheet->getCell('E27')->getStyle()->getNumberFormat()->setFormatCode($numberFormat);

    $benebkTotalLst = fncGetReportByBenebktotal($objDB);
    if ($benebkTotalLst && count($benebkTotalLst) > 0) {
        $clonedWorksheet->fromArray($benebkTotalLst, null, 'A8');
    }


}

/**
 * 帳票_2_L/C Open情報(L/C別合計)テンプレート内容の設定
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
    $clonedWorksheet->setTitle($sheetname . "_" . $currencyClass);
    $spreadsheet->addSheet($clonedWorksheet);
    $clonedWorksheet->setCellValue('A1', sprintf('%d年%d月', substr($objectYm, 0, 4), substr($objectYm, 5, 2)));
    $clonedWorksheet->setCellValue('H2', sprintf('通貨区分:%s', $currencyClass));

    
    $numberFormat = getNumberFormat($currencyClass);
    $clonedWorksheet->setCellValue('C2', $totalPrice);    
    $clonedWorksheet->getCell('C2')->getStyle()->getNumberFormat()->setFormatCode($numberFormat);

    $lcTotalLst = fncGetReportByLcTotal($objDB);
    
    if ($lcTotalLst && count($lcTotalLst) > 0) {
        $clonedWorksheet->fromArray($lcTotalLst, null, 'A5');
    }
    $clonedWorksheet->getStyle('C5:C40')->getNumberFormat()->setFormatCode($numberFormat);
    
}

/**
 * 帳票_3_L/C Open情報(L/C別明細)テンプレート内容の設定
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
    $clonedWorksheet->setTitle($sheetname . "_" . $currencyClass);
    $spreadsheet->addSheet($clonedWorksheet);
    
    $clonedWorksheet->setCellValue('C1', sprintf('%d年%d月', substr($objectYm, 0, 4), substr($objectYm, 5, 2)));
    $clonedWorksheet->setCellValue('Q1', sprintf('通貨区分:%s', $currencyClass));
    
    $numberFormat = getNumberFormat($currencyClass);
    $clonedWorksheet->setCellValue('I1', $totalPrice);    
    $clonedWorksheet->getCell('I1')->getStyle()->getNumberFormat()->setFormatCode($numberFormat);

    $lcDetailLst = fncGetReportByLcDetail($objDB);
    
    if ($lcDetailLst && count($lcDetailLst) > 0) {
        $clonedWorksheet->fromArray($lcDetailLst, null, 'A3');
    }
    
    $clonedWorksheet->getStyle('I3:I47')->getNumberFormat()->setFormatCode($numberFormat);
}

/**
 * 帳票_4_L/C Open情報（Beneficiary別L/C発行予定集計表）テンプレート内容の設定
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
        $clonedWorksheet->getHeaderFooter()->setOddHeader('Open月・Beneficiary別L/C発行予定集計表');
        $copysheetname = $sheetname . "_" . $currencyClass . "Open月";
    } else if ($type == 4) {
        $clonedWorksheet->getHeaderFooter()->setOddHeader('船積月・Beneficiary別L/C発行予定集計表');
        $copysheetname = $sheetname . "_" . $currencyClass . "船積月";
    }

    $clonedWorksheet->setTitle($copysheetname);
    $spreadsheet->addSheet($clonedWorksheet);
    $clonedWorksheet->setCellValue('M1', sprintf('通貨区分:%s', $currencyClass));    
    $objectYm = str_replace("/", "-", $objectYm) . "-01";
    $clonedWorksheet->setCellValue('B3', date("Y年m月", strtotime($objectYm . "-6 month")));
    $clonedWorksheet->setCellValue('C3', date("Y年m月", strtotime($objectYm . "-5 month")));
    $clonedWorksheet->setCellValue('D3', date("Y年m月", strtotime($objectYm . "-4 month")));
    $clonedWorksheet->setCellValue('E3', date("Y年m月", strtotime($objectYm . "-3 month")));
    $clonedWorksheet->setCellValue('F3', date("Y年m月", strtotime($objectYm . "-2 month")));
    $clonedWorksheet->setCellValue('G3', date("Y年m月", strtotime($objectYm . "-1 month")));
    $clonedWorksheet->setCellValue('H3', date("Y年m月", strtotime($objectYm . "0 month")));
    $clonedWorksheet->setCellValue('I3', date("Y年m月", strtotime($objectYm . "+1 month")));
    $clonedWorksheet->setCellValue('J3', date("Y年m月", strtotime($objectYm . "+2 month")));
    $clonedWorksheet->setCellValue('K3', date("Y年m月", strtotime($objectYm . "+3 month")));
    $clonedWorksheet->setCellValue('L3', date("Y年m月", strtotime($objectYm . "+4 month")));

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
        $clonedWorksheet->fromArray($beneMonthCalLst, null, 'A4');
    }
    
    $numberFormat = getNumberFormat($currencyClass);
    $clonedWorksheet->getStyle('B4:M23')->getNumberFormat()->setFormatCode($numberFormat);
}


/**
 * 帳票_5_未決済リストテンプレート内容の設定
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
    // $spreadsheet = $reader->load($filepath); //template.xlsx 読込
    $clonedWorksheet = clone $spreadsheet->getSheetByName($sheetname);
    $clonedWorksheet->setTitle($sheetname . "_" . $currencyClass);
    $spreadsheet->addSheet($clonedWorksheet);

    $clonedWorksheet->setCellValue('M3', "通貨区分：" . $currencyClass);
    $clonedWorksheet->setCellValue('B2', $startYmd);
    $clonedWorksheet->setCellValue('B3', $endYmd);
    $clonedWorksheet->setCellValue('B3', $endYmd);

    $unSettedLst = fncGetUnSettedLst($objDB);
    if ($unSettedLst && count($unSettedLst) > 0) {
        $clonedWorksheet->fromArray($unSettedLst, null, 'A5');
    }
    $unSettedTotal = fncGetUnSettedTotal($objDB);
    if ($unSettedTotal && count($unSettedTotal) > 0) {
        $clonedWorksheet->fromArray($unSettedTotal, null, 'B23');
    }

    $clonedWorksheet->setCellValue('A26', sprintf("%01.2f", $rate));

    $clonedWorksheet->setCellValue('B4', $bankLst[0]["bankomitname"]);
    $clonedWorksheet->setCellValue('C4', $bankLst[1]["bankomitname"]);
    $clonedWorksheet->setCellValue('D4', $bankLst[2]["bankomitname"]);
    $clonedWorksheet->setCellValue('E4', $bankLst[3]["bankomitname"]);

    $clonedWorksheet->setCellValue('B26', moneyFormat($unSettedTotal[0]["bank1total"] * $raterate, $currencyClass));
    $clonedWorksheet->setCellValue('C26', moneyFormat($unSettedTotal[0]["bank2total"] * $raterate, $currencyClass));
    $clonedWorksheet->setCellValue('D26', moneyFormat($unSettedTotal[0]["bank3total"] * $raterate, $currencyClass));
    $clonedWorksheet->setCellValue('E26', moneyFormat($unSettedTotal[0]["bank4total"] * $raterate, $currencyClass));
    $clonedWorksheet->setCellValue('F26', moneyFormat($unSettedTotal[0]["unapprovaltotaltotal"] * $raterate, $currencyClass));
    $clonedWorksheet->setCellValue('G26', moneyFormat($unSettedTotal[0]["benetotaltotal"] * $raterate, $currencyClass));

    $unSettedPriceLst = fncGetReportUnSettedPrice($objDB);

    if ($unSettedPriceLst && count($unSettedPriceLst) > 0) {

        $clonedWorksheet->fromArray($unSettedPriceLst, null, 'H5');
    }
    // $writer = new XlsWriter($spreadsheet);
    // $writer->save(REPORT_TMPDIR . REPORT_LC_OUTPUTFILE);

    //ダウンロード用
    // // Redirect output to a client’s web browser (Xlsx)
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
 * 帳票_6_輸入信用状情報テンプレート内容の設定
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
    $clonedWorksheet->setTitle($sheetname . "_" . $currencyClass);
    $spreadsheet->addSheet($clonedWorksheet);

    if (strcmp($currencyClass, '円') == 0)
    {
        $clonedWorksheet->setCellValue('H10', "金額（￥）");
    }
    $clonedWorksheet->setCellValue('P9', "通貨区分：" . $currencyClass);
    $clonedWorksheet->setCellValue('B7', sprintf('%d年%d月', substr($data["openYm"], 0, 4), substr($data["openYm"], 4, 2)));
    $clonedWorksheet->setCellValue('M8', sprintf('%d年%d月', substr($data["shipYm"], 0, 4), substr($data["shipYm"], 4, 2)));

    $clonedWorksheet->setCellValue('D8', $data["payfName"]);
    $clonedWorksheet->setCellValue('H8', $data["bankname"]);

    if (strcmp($data["bankname"], 'ALL') == 0)
    {  
        $clonedWorksheet->setCellValue('P10', "予定銀行");
    }
    $clonedWorksheet->setCellValue('L8', $data["lcopen"]);
    $clonedWorksheet->setCellValue('O8', $data["portplace"]);

    $sendLst = fncGetSendInfo($objDB); 

    if ($sendLst && count($sendLst) > 0) {
        $clonedWorksheet->setCellValue('B3', $sendLst[0]["lccautionstatement1"]);
        $clonedWorksheet->setCellValue('B4', $sendLst[0]["lccautionstatement2"]);
        $clonedWorksheet->setCellValue('G2', $sendLst[0]["lcsender"]);
        $clonedWorksheet->setCellValue('H3', $sendLst[0]["senderfax"]);
    }

    $payfInfo = fncGetPayfInfoByPayfcd($objDB, $data["payfCode"]);
    
    if ($payfInfo != null) {
        $clonedWorksheet->setCellValue('B2', $payfInfo->payfsendname);
        $clonedWorksheet->setCellValue('H42', $payfInfo->payfsendfax);
    }

    $sumofILOP = fncGetSumofImpLcOrderPrice($objDB);
    
    $clonedWorksheet->setCellValue('H27', $sumofILOP);
    $numberFormat = getNumberFormat($currencyClass);
    $clonedWorksheet->getStyle('H27')->getNumberFormat()->setFormatCode($numberFormat);

    $ilopLst = fncGetReportImpLcOrderInfo($objDB);

    if ($ilopLst && count($ilopLst) > 0) {
        $clonedWorksheet->fromArray($ilopLst, null, 'A12');
    }
    $clonedWorksheet->getStyle('H12:H26')->getNumberFormat()->setFormatCode($numberFormat);

}

/**
 * 数字フォーマットの取得
 *
 * @param [string] $currencyClass
 * @return string
 */
function getNumberFormat($currencyClass)
{
    if (strcmp($currencyClass, '円') == 0) {
        $numberFormat = '#,##0';
    } else {
        $numberFormat = '#,##0.00';
    }
    return $numberFormat;
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
