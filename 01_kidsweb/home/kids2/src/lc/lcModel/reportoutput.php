<?php

// phpspreadsheetパッケージをインポートする
require PATH_HOME . "/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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
function fncSetReportOne($objDB, $spreadsheet, $sheetname, $currencyClass, $bankLst, $objectYm, $type, $startRow, $pageNo)
{

    $sheet = $spreadsheet->getSheetByName($sheetname);
    // 帳票行数
    $reportRowsNum = 27;
    // 帳票列数
    $reportColsNum = 6;

    if ($startRow >= ($reportRowsNum + 1)) {
        
        copyRows($sheet, 1, $startRow, $reportRowsNum, $reportColsNum);
        // セルに値のクリア
        contentsClear($sheet, ($startRow -1 + 8), ($startRow -1 + 26), 1, $reportColsNum);
        // セルに値のクリア
        contentsClear($sheet, ($startRow -1  + 27), ($startRow -1 + 27), 2, $reportColsNum);

        $sheet->setBreak('A'. ($startRow-1), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
    }
    
    $startRow -= 1;

    if ($type == 1) {
        $sheet->getHeaderFooter()->setOddHeader('L/C Open情報（Beneficiary・Bk別合計）Open月');
    } else {
        $sheet->getHeaderFooter()->setOddHeader('L/C Open情報（Beneficiary・Bk別合計）船積月');
    }

    $sheet->setCellValue('A'. ($startRow + 4), sprintf('%d年%d月', substr($objectYm, 0, 4), substr($objectYm, 5, 2)));
    $sheet->setCellValue('F'. ($startRow + 5), sprintf('通貨区分:%s', $currencyClass));
    $sheet->setCellValue('B'. ($startRow + 7), $bankLst[0]["bankomitname"]);
    $sheet->setCellValue('C'. ($startRow + 7), $bankLst[1]["bankomitname"]);
    $sheet->setCellValue('D'. ($startRow + 7), $bankLst[2]["bankomitname"]);
    $sheet->setCellValue('E'. ($startRow + 7), $bankLst[3]["bankomitname"]);

    $offset = $pageNo * REPORT_LC_ONE_PAGE_REPORT_NUM_1;
    $limit = REPORT_LC_ONE_PAGE_REPORT_NUM_1;

    $sumofBenebkTotal = fncGetSumofBeneBkPrice($objDB, $offset, $limit); 
    $sheet->setCellValue('B'. ($startRow + 27), $sumofBenebkTotal->sum_1);
    $sheet->setCellValue('C'. ($startRow + 27), $sumofBenebkTotal->sum_2);
    $sheet->setCellValue('D'. ($startRow + 27), $sumofBenebkTotal->sum_3);
    $sheet->setCellValue('E'. ($startRow + 27), $sumofBenebkTotal->sum_4);
    $sheet->setCellValue('F'. ($startRow + 27), $sumofBenebkTotal->sum_5);

    $numberFormat = getNumberFormat($currencyClass);
    $sheet->getStyle('B'. ($startRow + 27). ':E'. ($startRow + 27))->getNumberFormat()->setFormatCode($numberFormat);

    $benebkTotalLst = fncGetReportByBenebktotal($objDB, $offset, $limit);
    if ($benebkTotalLst && count($benebkTotalLst) > 0) {
        $sheet->fromArray($benebkTotalLst, null, 'A'. ($startRow + 8));
    }

    $sheet->setSelectedCell('A1');
    
    $sheet->getHeaderFooter()->setOddFooter( '&P / &N' );
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
function fncSetReportTwo($objDB, $spreadsheet, $sheetname, $currencyClass, $objectYm, $totalPrice, $startRow, $pageNo)
{
    $sheet = $spreadsheet->getSheetByName($sheetname);
    // 帳票行数
    $reportRowsNum = 40;
    // 帳票列数
    $reportColsNum = 8;

    if ($startRow >= ($reportRowsNum + 1)) {
        
        copyRows($sheet, 1, $startRow, $reportRowsNum, $reportColsNum);
        // セルに値のクリア
        contentsClear($sheet, ($startRow  -1 + 5), ($startRow -1 + $reportRowsNum), 1, $reportColsNum);

        $sheet->setBreak('A'. ($startRow-1), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
    }
    
    $startRow -= 1;

    $sheet->setCellValue('A'. ($startRow + 1), sprintf('%d年%d月', substr($objectYm, 0, 4), substr($objectYm, 5, 2)));
    $sheet->setCellValue('H'. ($startRow + 2), sprintf('通貨区分:%s', $currencyClass));

    $numberFormat = getNumberFormat($currencyClass);
    $sheet->setCellValue('C'. ($startRow + 2), $totalPrice);
    $sheet->getCell('C'. ($startRow + 2))->getStyle()->getNumberFormat()->setFormatCode($numberFormat);

    $offset = $pageNo * REPORT_LC_ONE_PAGE_REPORT_NUM_2;
    $limit = REPORT_LC_ONE_PAGE_REPORT_NUM_2;

    $lcTotalLst = fncGetReportByLcTotal($objDB, $offset, $limit);

    if ($lcTotalLst && count($lcTotalLst) > 0) {
        $sheet->fromArray($lcTotalLst, null, 'A'. ($startRow + 5));
    }
    $sheet->getStyle('C'. ($startRow + 5) .':C'. ($startRow + 40))->getNumberFormat()->setFormatCode($numberFormat);

    $sheet->setSelectedCell('A1');
    
    $sheet->getHeaderFooter()->setOddFooter( '&P / &N' );
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
function fncSetReportThree($objDB, $spreadsheet, $sheetname, $currencyClass, $objectYm, $totalPrice, $startRow, $pageNo)
{
    $sheet = $spreadsheet->getSheetByName($sheetname);
    // 帳票行数
    $reportRowsNum = 47;
    // 帳票列数
    $reportColsNum = 17;

    if ($startRow >= ($reportRowsNum + 1)) {
        
        copyRows($sheet, 1, $startRow, $reportRowsNum, $reportColsNum);
        // セルに値のクリア
        contentsClear($sheet, ($startRow -1 + 3), ($startRow + $reportRowsNum - 1), 1, $reportColsNum);

        $sheet->setBreak('A'. ($startRow-1), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);

        $sheet->getRowDimension($startRow)->setVisible(false);
        
        $sheet->getRowDimension($startRow + 1)->setVisible(false);
    }
    
    $startRow -= 1;

    $sheet->setCellValue('C'. ($startRow + 1), sprintf('%d年%d月', substr($objectYm, 0, 4), substr($objectYm, 5, 2)));
    $sheet->setCellValue('Q'. ($startRow + 1), sprintf('通貨区分:%s', $currencyClass));

    $numberFormat = getNumberFormat($currencyClass);
    $sheet->setCellValue('I'. ($startRow + 1), $totalPrice);
    $sheet->getCell('I'. ($startRow + 1))->getStyle()->getNumberFormat()->setFormatCode($numberFormat);

    $offset = $pageNo * REPORT_LC_ONE_PAGE_REPORT_NUM_3;
    $limit = REPORT_LC_ONE_PAGE_REPORT_NUM_3;

    $lcDetailLst = fncGetReportByLcDetail($objDB, $offset, $limit);

    if ($lcDetailLst && count($lcDetailLst) > 0) {
        $sheet->fromArray($lcDetailLst, null, 'A'. ($startRow + 3));
    }

    $sheet->getStyle('I'. ($startRow + 3) .':I'. ($startRow + 47))->getNumberFormat()->setFormatCode($numberFormat);
    
    $sheet->setSelectedCell('A1');
    
    $sheet->getHeaderFooter()->setOddFooter( '&P / &N' );
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
function fncSetReportFour($objDB, $spreadsheet, $sheetname, $currencyClass, $objectYm, $type, $startRow, $pageNo)
{
    $sheet = $spreadsheet->getSheetByName($sheetname);
    // 帳票行数
    $reportRowsNum = 23;
    // 帳票列数
    $reportColsNum = 15;

    if ($startRow >= ($reportRowsNum + 1)) {
        copyRows($sheet, 1, $startRow, $reportRowsNum, $reportColsNum);
        // セルに値のクリア
        contentsClear($sheet, ($startRow -1 + 4), ($startRow -1 + 22), 1, $reportColsNum);
        // セルに値のクリア
        contentsClear($sheet, ($startRow -1 + 23), ($startRow -1 + 23), 2, $reportColsNum);

        $sheet->setBreak('A'. ($startRow-1), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
    }
    
    $startRow -= 1;

    if ($type == 3) {
        $sheet->getHeaderFooter()->setOddHeader('Open月・Beneficiary別L/C発行予定集計表');
        // $copysheetname = $sheetname . "_" . $currencyClass . "Open月";
    } else if ($type == 4) {
        $sheet->getHeaderFooter()->setOddHeader('船積月・Beneficiary別L/C発行予定集計表');
        // $copysheetname = $sheetname . "_" . $currencyClass . "船積月";
    }

    // $sheet->setTitle($copysheetname);
    // $spreadsheet->addSheet($sheet);
    $sheet->setCellValue('M'. ($startRow + 1), sprintf('通貨区分:%s', $currencyClass));
    $objectYm = str_replace("/", "-", $objectYm) . "-01";
    $sheet->setCellValue('B'. ($startRow + 3), date("Y年m月", strtotime($objectYm . "-6 month")));
    $sheet->setCellValue('C'. ($startRow + 3), date("Y年m月", strtotime($objectYm . "-5 month")));
    $sheet->setCellValue('D'. ($startRow + 3), date("Y年m月", strtotime($objectYm . "-4 month")));
    $sheet->setCellValue('E'. ($startRow + 3), date("Y年m月", strtotime($objectYm . "-3 month")));
    $sheet->setCellValue('F'. ($startRow + 3), date("Y年m月", strtotime($objectYm . "-2 month")));
    $sheet->setCellValue('G'. ($startRow + 3), date("Y年m月", strtotime($objectYm . "-1 month")));
    $sheet->setCellValue('H'. ($startRow + 3), date("Y年m月", strtotime($objectYm . "0 month")));
    $sheet->setCellValue('I'. ($startRow + 3), date("Y年m月", strtotime($objectYm . "+1 month")));
    $sheet->setCellValue('J'. ($startRow + 3), date("Y年m月", strtotime($objectYm . "+2 month")));
    $sheet->setCellValue('K'. ($startRow + 3), date("Y年m月", strtotime($objectYm . "+3 month")));
    $sheet->setCellValue('L'. ($startRow + 3), date("Y年m月", strtotime($objectYm . "+4 month")));


    $offset = $pageNo * REPORT_LC_ONE_PAGE_REPORT_NUM_4;
    $limit = REPORT_LC_ONE_PAGE_REPORT_NUM_4;

    $sumofBeneMonthCal = fncGetSumofBeneMonCal($objDB, $offset, $limit);
// var_dump($sumofBeneMonthCal);
    $sheet->setCellValue('B'. ($startRow + 23), $sumofBeneMonthCal->sum_1);
    $sheet->setCellValue('C'. ($startRow + 23), $sumofBeneMonthCal->sum_2);
    $sheet->setCellValue('D'. ($startRow + 23), $sumofBeneMonthCal->sum_3);
    $sheet->setCellValue('E'. ($startRow + 23), $sumofBeneMonthCal->sum_4);
    $sheet->setCellValue('F'. ($startRow + 23), $sumofBeneMonthCal->sum_5);
    $sheet->setCellValue('G'. ($startRow + 23), $sumofBeneMonthCal->sum_6);
    $sheet->setCellValue('H'. ($startRow + 23), $sumofBeneMonthCal->sum_7);
    $sheet->setCellValue('I'. ($startRow + 23), $sumofBeneMonthCal->sum_8);
    $sheet->setCellValue('J'. ($startRow + 23), $sumofBeneMonthCal->sum_9);
    $sheet->setCellValue('K'. ($startRow + 23), $sumofBeneMonthCal->sum_10);
    $sheet->setCellValue('L'. ($startRow + 23), $sumofBeneMonthCal->sum_11);
    $sheet->setCellValue('M'. ($startRow + 23), $sumofBeneMonthCal->sum_12);

    $beneMonthCalLst = fncGetReportByBeneMonthCal($objDB, $offset, $limit);

    if ($beneMonthCalLst && count($beneMonthCalLst) > 0) {
        $sheet->fromArray($beneMonthCalLst, null, 'A'. ($startRow + 4));
    }

    $numberFormat = getNumberFormat($currencyClass);
    $sheet->getStyle('B'. ($startRow + 4). ':M'. ($startRow + 23))->getNumberFormat()->setFormatCode($numberFormat);
    
    $sheet->setSelectedCell('A1');
    
    $sheet->getHeaderFooter()->setOddFooter( '&P / &N' );
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
function fncSetReportFive($objDB, $spreadsheet, $sheetname, $currencyClass, $bankLst, $startYmd, $endYmd, $rate, $startRow, $pageNo)
{
    $sheet = $spreadsheet->getSheetByName($sheetname);
    // 帳票行数
    $reportRowsNum = 49;
    // 帳票列数
    $reportColsNum = 14;

    if ($startRow >= ($reportRowsNum + 1)) {
        
        copyRows($sheet, 1, $startRow, $reportRowsNum, $reportColsNum);
        // セルに値のクリア
        contentsClear($sheet, ($startRow - 1 + 5), ($startRow + $reportRowsNum - 1), 8, $reportColsNum);
        // セルに値のクリア
        contentsClear($sheet, ($startRow - 1 + 5), ($startRow - 1 + 22), 1, 7);
        // セルに値のクリア
        contentsClear($sheet, ($startRow - 1 + 23), ($startRow - 1 + 23), 2, 7);

        $sheet->setBreak('A'. ($startRow-1), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
    }
    
    $startRow -= 1;

    // $sheet = clone $spreadsheet->getSheetByName($sheetname);
    // $sheet->setTitle($sheetname . "_" . $currencyClass);
    // $spreadsheet->addSheet($sheet);

    $sheet->setCellValue('M'. ($startRow + 3), "通貨区分：" . $currencyClass);
    $sheet->setCellValue('B'. ($startRow + 2), $startYmd);
    $sheet->setCellValue('B'. ($startRow + 3), $endYmd);
    $sheet->setCellValue('B'. ($startRow + 3), $endYmd);

    $numberFormat = getNumberFormat($currencyClass);

    $offset = $pageNo * REPORT_LC_ONE_PAGE_REPORT_NUM_5_1;

    $limit = REPORT_LC_ONE_PAGE_REPORT_NUM_5_1;

    $unSettedLst = fncGetUnSettedLst($objDB, $offset, $limit);
    if ($unSettedLst && count($unSettedLst) > 0) {
        $sheet->fromArray($unSettedLst, null, 'A'. ($startRow + 5));
    }
    $sheet->getStyle('F'. ($startRow + 5). ':G'. ($startRow + 22))->getNumberFormat()->setFormatCode($numberFormat);

    $unSettedTotal = fncGetUnSettedTotal($objDB, $offset, $limit); 
    if ($unSettedTotal) {            
        $sheet->setCellValue('B'. ($startRow + 23), $unSettedTotal->bank1total);
        $sheet->setCellValue('C'. ($startRow + 23), $unSettedTotal->bank2total);
        $sheet->setCellValue('D'. ($startRow + 23), $unSettedTotal->bank3total);
        $sheet->setCellValue('E'. ($startRow + 23), $unSettedTotal->bank4total);
        $sheet->setCellValue('F'. ($startRow + 23), $unSettedTotal->unapprovaltotaltotal);
        $sheet->setCellValue('G'. ($startRow + 23), $unSettedTotal->benetotaltotal);
    }    
    $sheet->getStyle('B'. ($startRow + 23). ':G'. ($startRow + 23))->getNumberFormat()->setFormatCode($numberFormat);

    $sheet->setCellValue('A'. ($startRow + 26), sprintf("%01.2f", $rate));
    $sheet->setCellValue('B'. ($startRow + 4), $bankLst[0]["bankomitname"]);
    $sheet->setCellValue('C'. ($startRow + 4), $bankLst[1]["bankomitname"]);
    $sheet->setCellValue('D'. ($startRow + 4), $bankLst[2]["bankomitname"]);
    $sheet->setCellValue('E'. ($startRow + 4), $bankLst[3]["bankomitname"]);

    $sheet->setCellValue('B'. ($startRow + 26), $unSettedTotal->bank1total * $rate);
    $sheet->setCellValue('C'. ($startRow + 26), $unSettedTotal->bank2total * $rate);
    $sheet->setCellValue('D'. ($startRow + 26), $unSettedTotal->bank3total * $rate);
    $sheet->setCellValue('E'. ($startRow + 26), $unSettedTotal->bank4total * $rate);
    $sheet->setCellValue('F'. ($startRow + 26), $unSettedTotal->unapprovaltotaltotal * $rate);
    $sheet->setCellValue('G'. ($startRow + 26), $unSettedTotal->benetotaltotal * $rate);

    $sheet->getStyle('B'. ($startRow + 26). ':G'. ($startRow + 26))->getNumberFormat()->setFormatCode($numberFormat);

    $offset = $pageNo * REPORT_LC_ONE_PAGE_REPORT_NUM_5_2;

    $limit = REPORT_LC_ONE_PAGE_REPORT_NUM_5_2;

    $unSettedPriceLst = fncGetReportUnSettedPrice($objDB, $offset, $limit);

    if ($unSettedPriceLst && count($unSettedPriceLst) > 0) {

        $sheet->fromArray($unSettedPriceLst, null, 'H'. ($startRow + 5));
    }
    $sheet->getStyle('N'. ($startRow + 5). ':N'. ($startRow + 49))->getNumberFormat()->setFormatCode($numberFormat);

    $sheet->setSelectedCell('A1');
    
    $sheet->getHeaderFooter()->setOddFooter( '&P / &N' );

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
function fncSetReportSix($objDB, $spreadsheet, $sheetname, $currencyClass, $bankLst, $data, $startRow, $pageNo)
{
    $sheet = $spreadsheet->getSheetByName($sheetname);
    // 帳票行数
    $reportRowsNum = 44;
    // 帳票列数
    $reportColsNum = 16;

    if ($startRow >= ($reportRowsNum + 1)) {

        $sheet->setCellValue('D27' , 'SUB TOTAL AMOUNT');
        
        copyRows($sheet, 1, $startRow, $reportRowsNum, $reportColsNum);
        // セルに値のクリア
        contentsClear($sheet, ($startRow - 1 + 12), ($startRow -1 + 26), 1, $reportColsNum);

        $sheet->setBreak('A'. ($startRow-1), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
    }
    
    $startRow -= 1;
    
    if (strcmp($currencyClass, '円') == 0) {
        $sheet->setCellValue('H10', "金額（￥）");
    }
    $sheet->setCellValue('P'. ($startRow + 9), "通貨区分：" . $currencyClass);
    $sheet->setCellValue('B'. ($startRow + 7), sprintf('%d年%d月', substr($data["openYm"], 0, 4), substr($data["openYm"], 5, 2)));
    $sheet->setCellValue('M'. ($startRow + 8), sprintf('%d年%d月', substr($data["shipYm"], 0, 4), substr($data["shipYm"], 5, 2)));

    $sheet->setCellValue('D'. ($startRow + 8), $data["payfName"]);
    $sheet->setCellValue('H'. ($startRow + 8), $data["bankname"]);

    if (strcmp($currencyClass, '円') == 0) {
        $pricesign = '金額（＄）';
    } else {
        $pricesign = '金額（￥）';
    }
    $sheet->setCellValue('H'. ($startRow + 10), $pricesign);

    if (strcmp(trim($data["bankname"]), 'ALL') == 0) {
        $sheet->setCellValue('P'. ($startRow + 10), "予定銀行");
    }
    $sheet->setCellValue('L'. ($startRow + 8), trim($data["lcopen"]));
    $sheet->setCellValue('O'. ($startRow + 8), trim($data["portplace"]));

    $sendLst = fncGetSendInfo($objDB);

    if ($sendLst && count($sendLst) > 0) {
        $sheet->setCellValue('B'. ($startRow + 3), $sendLst[0]["sendcarenote1"]);
        $sheet->setCellValue('B'. ($startRow + 4), $sendLst[0]["sendcarenote2"]);
        $sheet->setCellValue('G'. ($startRow + 2), $sendLst[0]["sendfromname"]);
        $sheet->setCellValue('H'. ($startRow + 3), $sendLst[0]["sendfromfax"]);
    }

    $payfInfo = fncGetPayfInfoByPayfcd($objDB, $data["payfCode"]);

    if ($payfInfo != null) {
        $sheet->setCellValue('B'. ($startRow + 2), $payfInfo->payfsendname);
        $sheet->setCellValue('H'. ($startRow + 42), $payfInfo->payfsendfax);
    }
    
    $offset = $pageNo * REPORT_LC_ONE_PAGE_REPORT_NUM_6;
    $limit = REPORT_LC_ONE_PAGE_REPORT_NUM_6;

    $sumofILOP = fncGetSumofImpLcOrderPrice($objDB, $offset, $limit);

    $sheet->setCellValue('H'. ($startRow + 27), $sumofILOP);
    $numberFormat = getNumberFormat($currencyClass);
    $sheet->getStyle('H'. ($startRow + 27))->getNumberFormat()->setFormatCode($numberFormat);

    $ilopLst = fncGetReportImpLcOrderInfo($objDB, $offset, $limit);

    if ($ilopLst && count($ilopLst) > 0) {
        $sheet->fromArray($ilopLst, null, 'A'. ($startRow + 12));
    }
    $sheet->getStyle('H'. ($startRow + 12). ':' .'H'. ($startRow + 26))->getNumberFormat()->setFormatCode($numberFormat);

    $sheet->setSelectedCell('A1');
    
    $sheet->getHeaderFooter()->setOddFooter( '&P / &N' );
}

/**
 * 行のコピー
 *
 * @param [type] $sheet シート
 * @param [type] $srcRow 複製元行番号
 * @param [type] $dstRow 複製先行番号
 * @param [type] $height 複製行数
 * @param [type] $width 複製カラム数
 * @return void
 */
function copyRows($sheet, $srcRow, $dstRow, $height, $width) {
    for ($row = 0; $row < $height; $row++) {
        $copyRow = $srcRow + $row;
        $newRow = $dstRow + $row; // 挿入された行の番号
        // セルの書式と値の複製
        for ($col = 1; $col <= $width; $col++) {
            $alphaCol = Coordinate::stringFromColumnIndex($col);
            $copyAddress = $alphaCol . $copyRow;
            $newAddress = $alphaCol . $newRow;
            $copyValue = $sheet->getCell($copyAddress)->getValue();
            $copyStyle = $sheet->getStyle($copyAddress);
            $cellPattern = '/(\$?[A-Z]+)' . $copyRow . '(\D?)/';
            $replace = '${1}' . $newRow . '${2}';

            $insertValue = preg_replace($cellPattern, $replace, $copyValue);

            $sheet->setCellValue($newAddress, $insertValue);
            $sheet->duplicateStyle($copyStyle, $newAddress);
        }

        // 行の高さ複製。
        $h = $sheet->getRowDimension($srcRow + $row)->getRowHeight();
        $sheet->getRowDimension($dstRow + $row)->setRowHeight($h);
    }

    // セル結合の複製
    // - $mergeCell="AB12:AC15" 複製範囲の物だけ行を加算して復元。
    // - $merge="AB16:AC19"
    foreach ($sheet->getMergeCells() as $mergeCell) {
        $mc = explode(":", $mergeCell);
        $col_s = preg_replace("/[0-9]*/", "", $mc[0]);
        $col_e = preg_replace("/[0-9]*/", "", $mc[1]);
        $row_s = ((int) preg_replace("/[A-Z]*/", "", $mc[0])) - $srcRow;
        $row_e = ((int) preg_replace("/[A-Z]*/", "", $mc[1])) - $srcRow;

        // 複製先の行範囲なら。
        if (0 <= $row_s && $row_s < $height) {
            $merge = $col_s . (string) ($dstRow + $row_s) . ":" . $col_e . (string) ($dstRow + $row_e);
            $sheet->mergeCells($merge);
        }
    }
}

function contentsClear($sheet, $startRow, $endRow, $startCol, $endCol)
{
    for ($row = $startRow; $row <= $endRow; $row++) {
        for ($col = $startCol; $col <= $endCol; $col++) {
            $alphaCol = Coordinate::stringFromColumnIndex($col);
            $newAddress = $alphaCol . $row;
            $sheet->setCellValue($newAddress, '');
        }
    }
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
