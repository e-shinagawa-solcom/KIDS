<?php
// ----------------------------------------------------------------------------
/**
 *       請求管理  請求集計画面
 *
 *
 *       @package    K.I.D.S.
 *       @license    http://www.kuwagata.co.jp/
 *       @copyright  KUWAGATA CO., LTD.
 *       @author     K.I.D.S. Groups <info@kids-groups.com>
 *       @access     public
 *       @version    2.00
 *
 *
 *       処理概要
 *         ・指定された月の請求書をエクセルにて出力する
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 設定読み込み
include_once 'conf.inc';

// ライブラリ読み込み
require LIB_FILE;
require LIB_DEBUGFILE;
require SRC_ROOT . "m/cmn/lib_m.php";
require SRC_ROOT . "inv/cmn/lib_regist.php";
require SRC_ROOT . "search/cmn/lib_search.php";
require SRC_ROOT . "inv/cmn/column.php";

// PhpSpreadsheeを使うため
require_once VENDOR_AUTOLOAD_FILE;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

// オブジェクト生成
$objDB = new clsDB();
$objAuth = new clsAuth();

// DBオープン
$objDB->open("", "", "", "");

// パラメータ取得
if ($_POST) {
    $aryData = $_POST;
} elseif ($_GET) {
    $aryData = $_GET;
}

// cookieにSET
if (!empty($aryData["strSessionID"])) {
    setcookie("strSessionID", $aryData["strSessionID"], 0, "/");
}

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 2200 請求管理
if (!fncCheckAuthority(DEF_FUNCTION_INV0, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 2202 請求書検索
if (!fncCheckAuthority(DEF_FUNCTION_INV2, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 2203 請求集計
if (!fncCheckAuthority(DEF_FUNCTION_INV4, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// ヘルプ対応
$aryData["lngFunctionCode"] = DEF_FUNCTION_INV0;

if (isset($aryData["strMode"]) && $aryData["strMode"] == 'export') {

    //詳細画面の表示
    $invoiceMonth = $aryData["invoiceMonth"] . '-01';

    // 指定月の請求書マスタ取得
    $strQuery = fncGetInvoiceAggregateSQL($aryData["invoiceMonth"]);

    // 詳細データの取得
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if (!$lngResultNum) {
        $objDB->freeResult($lngResultID);
        $objDB->close();
        // HTML出力
        $aryData['noDataMsg'] = str_replace('-', '年', $aryData["invoiceMonth"]) . '月の請求書は見つかりませんでした。';
        echo fncGetReplacedHtmlWithBase("inv/base_aggregate.html", "inv/aggregate/index.tmpl", $aryData, $objAuth);
        return;
    }

    // エクセル出力用にデータを加工
    // array[lngmonetaryunitcode][strcustomercode][]
    $aggregateDataForCustomer = [];
    $aggregateDataForCustomerCompany = [];
    if ($lngResultNum) {
        $pre_strcustomername = "";
        $pre_strcustomercompanyname = "";
        $j = 0;
        $k = 0;
        for ($i = 0; $i < $lngResultNum; $i++) {
            $exportAry = [];
            $aryResult = $objDB->fetchArray($lngResultID, $i);
            // lngmonetaryunitcode別の配列に格納
            $monetaryunitcode = (int) $aryResult['lngmonetaryunitcode'];
            // lngmonetaryunitcode別の配列に格納
            $strcustomercode = $aryResult['strcustomercode'];

            // 顧客コード・顧客名・顧客社名
            list($printCustomerName, $printCompanyName, $customerCode, $strcompanydisplayname) = fncGetCompanyPrintName($aryResult['strcustomercode'], $objDB);

            $exportAry['lnginvoiceno'] = $aryResult['lnginvoiceno'];
            $exportAry['lngrevisionno'] = $aryResult['lngrevisionno'];
            // 顧客コード(表示用)
            $strcustomercode = $aryResult['strcustomercode'];
            $exportAry['strcustomercode'] = $strcustomercode;
            // 表示用顧客名・会社名を入れる
            $exportAry['strcustomername'] = $printCustomerName;
            $exportAry['strcustomercompanyname'] = $printCompanyName;
            $exportAry['strinvoicecode'] = $aryResult['strinvoicecode'];

            $exportAry['curlastmonthbalance'] = $aryResult['curlastmonthbalance'];
            $exportAry['curthismonthamount'] = $aryResult['curthismonthamount'];
            $exportAry['lngmonetaryunitcode'] = $aryResult['lngmonetaryunitcode'];
            $exportAry['lngtaxclasscode'] = $aryResult['lngtaxclasscode'];
            $exportAry['strtaxclassname'] = $aryResult['strtaxclassname'];
            // 税抜金額1
            $cursubtotal1 = $aryResult['cursubtotal1'];
            $exportAry['cursubtotal1'] = $cursubtotal1;
            // 消費税率1
            $curtax1 = $aryResult['curtax1'];
            $exportAry['curtax1'] = $curtax1;
            // 消費税額1
            $curtaxprice1 = $aryResult['curtaxprice1'];
            $exportAry['curtaxprice1'] = $curtaxprice1;

            if ($pre_strcustomername != $printCustomerName || $pre_strcustomercompanyname != $printCompanyName) {
                if ($pre_strcustomername == "" && $pre_strcustomercompanyname == "") {
                    $j = 0;
                } else {
                    $j += 1;
                }
            }

            // 顧客ごとのデータをまとめる
            $aggregateDataForCustomer[$monetaryunitcode][$j]['invoiceobj'][] = $exportAry;
            // 顧客毎の合計値
            $aggregateDataForCustomer[$monetaryunitcode][$j]['cursubtotal'] += $cursubtotal1;
            $aggregateDataForCustomer[$monetaryunitcode][$j]['curtaxprice'] += $curtaxprice1;
            $aggregateDataForCustomer[$monetaryunitcode][$j]['strcustomername'] = $printCustomerName;
            $aggregateDataForCustomer[$monetaryunitcode][$j]['strcustomercompanyname'] = $printCompanyName;

            if ($pre_strcustomercompanyname != $printCompanyName) {
                if ($pre_strcustomercompanyname == "") {
                    $k = 0;
                } else {
                    $k += 1;
                }
            }

            $aggregateDataForCustomerCompany[$monetaryunitcode][$k]['invoiceobj'][] = $exportAry;
            $aggregateDataForCustomerCompany[$monetaryunitcode][$k]['strcustomercompanyname'] = $printCompanyName;
            $aggregateDataForCustomerCompany[$monetaryunitcode][$k]['cursubtotal'] += $cursubtotal1;
            $aggregateDataForCustomerCompany[$monetaryunitcode][$k]['curtaxprice'] += $curtaxprice1;
            $pre_strcustomername = $printCustomerName;
            $pre_strcustomercompanyname = $printCompanyName;
        }
    }
    $objDB->freeResult($lngResultID);
    ini_set('default_charset', 'UTF-8');
    // 1.日本円 顧客毎の集計
    $row = [];
    // 書き込み行数
    $writeRow = 0;
    if (isset($aggregateDataForCustomer[1])) {
        foreach ((array) $aggregateDataForCustomer[1] as $code => $val) {
            for ($i = 0; $i < COUNT($val['invoiceobj']); $i++) {
                // 詳細データを取得する
                $lnginvoiceno = $val['invoiceobj'][$i]['lnginvoiceno'];
                $lngrevisionno = $val['invoiceobj'][$i]['lngrevisionno'];
                $strcustomercompanyname = $val['invoiceobj'][$i]['strcustomercompanyname'];
                $strcustomername = $val['invoiceobj'][$i]['strcustomername'];
                $strinvoicecode = $val['invoiceobj'][$i]['strinvoicecode'];
                $detailData = fncGetDetailData('inv', $lnginvoiceno, $lngrevisionno, $objDB);
                $len = 0;
                foreach ($detailData as $data) {
                    if ($len == 0) {
                        $row[] = [
                            $strcustomercompanyname,
                            $strcustomername,
                            '',
                            $strinvoicecode,
                            $data['strslipcode'],
                            $data['strcustomerno'],
                            $data['cursubtotalprice'],
                            $data['curtaxprice'],
                        ];
                    } else {
                        $row[] = [
                            $strcustomercompanyname,
                            $strcustomername,
                            '',
                            '',
                            $data['strslipcode'],
                            $data['strcustomerno'],
                            $data['cursubtotalprice'],
                            $data['curtaxprice'],
                        ];
                    }
                    $len += 1;
                    // 行数カウント
                    $writeRow++;
                }
            }
            // 合計値
            $row[] = [
                '',
                '',
                '',
                '小計',
                '',
                '',
                $val['cursubtotal'],
                $val['curtaxprice'],
            ];
            // 行数カウント
            $writeRow++;
            // 行間
            $row[] = [];
            // 行数カウント
            $writeRow++;
        }
    }
    // 書き込みデータ
    $writeData1_1 = $row;
    // 書き込み開始行 (D7)
    $writeCell1_1 = 'D' . '7';
    // 挿入行(157行)
    $addCell1_1 = 0;
    if ($writeRow > 157) {
        $addCell1_1 = $writeRow - 157;
    }
    // 挿入行Total
    $addCellTotal += $addCell1_1;

    // 2.日本円 顧客名がNull以外の集計
    // 3.日本円 6102の集計
    // 4.日本円 4410の集計
    $row6102 = [];
    $row4410 = [];
    $row = [];
    // 書き込み行数
    $writeRow = 0;
    if (isset($aggregateDataForCustomerCompany[1])) {
        foreach ((array) $aggregateDataForCustomerCompany[1] as $code => $val) {
            if ($val['invoiceobj'][0]['strcustomercode'] == '6102') {
                $row6102[] = [
                    $val['strcustomercompanyname'],
                    '',
                    '',
                    '',
                    '',
                    $val['cursubtotal'],
                    $val['curtaxprice'],
                ];
            } else if ($val['invoiceobj'][0]['strcustomercode'] == '4410') {
                $row4410[] = [
                    $val['strcustomercompanyname'],
                    '',
                    '',
                    '',
                    '',
                    $val['cursubtotal'],
                    $val['curtaxprice'],
                ];
            } else {
                $row[] = [
                    $val['strcustomercompanyname'],
                    '',
                    '',
                    '',
                    '',
                    $val['cursubtotal'],
                    $val['curtaxprice'],
                ];
                // 行数カウント
                $writeRow++;
            }
        }
    }
    // 書き込みデータ
    $writeData1_2 = $row;
    // 書き込み開始行 (E209)
    $writeCell1_2 = 'E' . (209 + $addCellTotal);
    // 挿入行(6行)
    $addCell1_2 = 0;
    if ($writeRow > 6) {
        $addCell1_2 = $writeRow - 6;
    }
    // 挿入行Total
    $addCellTotal += $addCell1_2;

    // 書き込みデータ
    $writeData1_3 = $row6102;
    // 書き込み開始行 (E216)
    $writeCell1_3 = 'E' . (216 + $addCellTotal);
    // 書き込みデータ
    $writeData1_4 = $row4410;
    // 書き込み開始行 (E217)
    $writeCell1_4 = 'E' . (217 + $addCellTotal);

    // 1.ドル 顧客毎の集計
    $row = [];
    // 書き込み行数
    $writeRow = 0;
    if (isset($aggregateDataForCustomer[2])) {
        foreach ((array) $aggregateDataForCustomer[2] as $code => $val) {
            for ($i = 0; $i < COUNT($val['invoiceobj']); $i++) {
                // 詳細データを取得する
                $lnginvoiceno = $val['invoiceobj'][$i]['lnginvoiceno'];
                $lngrevisionno = $val['invoiceobj'][$i]['lngrevisionno'];
                $strcustomercompanyname = $val['invoiceobj'][$i]['strcustomercompanyname'];
                $strcustomername = $val['invoiceobj'][$i]['strcustomername'];
                $strinvoicecode = $val['invoiceobj'][$i]['strinvoicecode'];
                $detailData = fncGetDetailData('inv', $lnginvoiceno, $lngrevisionno, $objDB);
                $len = 0;
                foreach ($detailData as $data) {
                    if ($len == 0) {
                        $row[] = [
                            $strcustomercompanyname,
                            $strcustomername,
                            '',
                            $strinvoicecode,
                            $data['strslipcode'],
                            $data['strcustomerno'],
                            $data['cursubtotalprice'],
                            $data['curtaxprice'],
                        ];
                    } else {
                        $row[] = [
                            $strcustomercompanyname,
                            $strcustomername,
                            '',
                            '',
                            $data['strslipcode'],
                            $data['strcustomerno'],
                            $data['cursubtotalprice'],
                            $data['curtaxprice'],
                        ];
                    }
                    $len += 1;
                    // 行数カウント
                    $writeRow++;
                }
            }
            // 合計値
            $row[] = [
                '',
                '',
                '',
                '小計',
                '',
                '',
                $val['cursubtotal'],
                $val['curtaxprice'],
            ];
            // 行数カウント
            $writeRow++;
            // 行間
            $row[] = [];
            // 行数カウント
            $writeRow++;
        }
    }
    // 書き込みデータ
    $writeData2_1 = $row;
    // 書き込み開始行 (D242)
    $writeCell2_1 = 'D' . (242 + $addCellTotal);
    // 挿入行(31行)
    $addCell2_1 = 0;
    if ($writeRow > 31) {
        $addCell2_1 = $writeRow - 31;
    }
    // 挿入行Total
    $addCellTotal += $addCell2_1;

    // 2.ドル 顧客名がNull以外の集計
    // 3.ドル 6102の集計
    // 4.ドル 4410の集計
    $row = [];
    $row6102 = [];
    $row4410 = [];
    // 書き込み行数
    $writeRow = 0;
    if (isset($aggregateDataForCustomerCompany[2])) {
        foreach ((array) $aggregateDataForCustomerCompany[2] as $code => $val) {
            if ($val['invoiceobj'][0]['strcustomercode'] == '6102') {
                $row6102[] = [
                    $val['strcustomercompanyname'],
                    '',
                    '',
                    '',
                    '',
                    $val['cursubtotal'],
                    $val['curtaxprice'],
                ];
            } else if ($val['invoiceobj'][0]['strcustomercode'] == '4410') {
                $row4410[] = [
                    $val['strcustomercompanyname'],
                    '',
                    '',
                    '',
                    '',
                    $val['cursubtotal'],
                    $val['curtaxprice'],
                ];
            } else {
                $row[] = [
                    $val['strcustomercompanyname'],
                    '',
                    '',
                    '',
                    '',
                    $val['cursubtotal'],
                    $val['curtaxprice'],
                ];
                // 行数カウント
                $writeRow++;
            }

        }
    }
    // 書き込みデータ
    $writeData2_2 = $row;
    // 書き込み開始行 (E298)
    $writeCell2_2 = 'E' . (298 + $addCellTotal);
    // 挿入行(6行)
    $addCell2_2 = 0;
    if ($writeRow > 6) {
        $addCell2_2 = $writeRow - 6;
    }
    // 挿入行Total
    $addCellTotal += $addCell2_2;

    // 書き込みデータ
    $writeData2_3 = $row6102;
    // 書き込み開始行 (E305)
    $writeCell2_3 = 'E' . (305 + $addCellTotal);
    // 書き込みデータ
    $writeData2_4 = $row4410;
    // 書き込み開始行 (E306)
    $writeCell2_4 = 'E' . (306 + $addCellTotal);

    // 1.HKドル 顧客毎の集計
    $row = [];
    // 書き込み行数
    $writeRow = 0;
    if (isset($aggregateDataForCustomer[3])) {
        foreach ((array) $aggregateDataForCustomer[3] as $code => $val) {
            for ($i = 0; $i < COUNT($val['invoiceobj']); $i++) {
                // 詳細データを取得する
                $lnginvoiceno = $val['invoiceobj'][$i]['lnginvoiceno'];
                $lngrevisionno = $val['invoiceobj'][$i]['lngrevisionno'];
                $strcustomercompanyname = $val['invoiceobj'][$i]['strcustomercompanyname'];
                $strcustomername = $val['invoiceobj'][$i]['strcustomername'];
                $strinvoicecode = $val['invoiceobj'][$i]['strinvoicecode'];
                $detailData = fncGetDetailData('inv', $lnginvoiceno, $lngrevisionno, $objDB);
                $len = 0;
                foreach ($detailData as $data) {
                    if ($len == 0) {
                        $row[] = [
                            $strcustomercompanyname,
                            $strcustomername,
                            '',
                            $strinvoicecode,
                            $data['strslipcode'],
                            $data['strcustomerno'],
                            $data['cursubtotalprice'],
                            $data['curtaxprice'],
                        ];
                    } else {
                        $row[] = [
                            $strcustomercompanyname,
                            $strcustomername,
                            '',
                            '',
                            $data['strslipcode'],
                            $data['strcustomerno'],
                            $data['cursubtotalprice'],
                            $data['curtaxprice'],
                        ];
                    }
                    $len += 1;
                    // 行数カウント
                    $writeRow++;
                }
            }
            // 合計値
            $row[] = [
                '',
                '',
                '',
                '小計',
                '',
                '',
                $val['cursubtotal'],
                $val['curtaxprice'],
            ];
            // 行数カウント
            $writeRow++;
            // 行間
            $row[] = [];
            // 行数カウント
            $writeRow++;
        }
    }
    // 書き込みデータ
    $writeData3_1 = $row;
    // 書き込み開始行 (D316)
    $writeCell3_1 = 'D' . (316 + $addCellTotal);
    // 挿入行(31行)
    $addCell3_1 = 0;
    if ($writeRow > 31) {
        $addCell3_1 = $writeRow - 31;
    }
    // 挿入行Total
    $addCellTotal += $addCell3_1;

    // 2.ドル 顧客名がNull以外の集計
    // 3.ドル 6102の集計
    // 4.ドル 4410の集計
    $row = [];
    $row6102 = [];
    $row4410 = [];
    // 書き込み行数
    $writeRow = 0;
    if (isset($aggregateDataForCustomerCompany[3])) {
        foreach ((array) $aggregateDataForCustomerCompany[3] as $code => $val) {
            if ($val['invoiceobj'][0]['strcustomercode'] == '6102') {
                $row6102[] = [
                    $val['strcustomercompanyname'],
                    '',
                    '',
                    '',
                    '',
                    $val['cursubtotal'],
                    $val['curtaxprice'],
                ];
            } else if ($val['invoiceobj'][0]['strcustomercode'] == '4410') {
                $row4410[] = [
                    $val['strcustomercompanyname'],
                    '',
                    '',
                    '',
                    '',
                    $val['cursubtotal'],
                    $val['curtaxprice'],
                ];
            } else {
                $row[] = [
                    $val['strcustomercompanyname'],
                    '',
                    '',
                    '',
                    '',
                    $val['cursubtotal'],
                    $val['curtaxprice'],
                ];
                // 行数カウント
                $writeRow++;
            }
        }

    }
    // 書き込みデータ
    $writeData3_2 = $row;
    // 書き込み開始行 (E372)
    $writeCell3_2 = 'E' . (372 + $addCellTotal);
    // 挿入行(6行)
    $addCell3_2 = 0;
    if ($writeRow > 6) {
        $addCell3_2 = $writeRow - 6;
    }
    // 挿入行Total
    $addCellTotal += $addCell3_2;

    // 書き込みデータ
    $writeData3_3 = $row6102;
    // 書き込み開始行 (E379)
    $writeCell3_3 = 'E' . (379 + $addCellTotal);
    // 書き込みデータ
    $writeData3_4 = $row4410;
    // 書き込み開始行 (E380)
    $writeCell3_4 = 'E' . (380 + $addCellTotal);

    // 読み込み設定
    $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();

    // テンプレートファイル名
    $baseFile = TMP_ROOT . 'inv/aggregate.xlsx';
    $reader->setReadDataOnly(false);
    $spreadsheet = $reader->load($baseFile);

    //テンプレートの複写
    // $clonedSheet = clone $baseSheet;
    $sheet = $spreadsheet->getActiveSheet();
    // $sheet = $spreadsheet->getSheetByName($sheetname);
    // 書き込み先頭行
    $topRow = 8;

    // ブックに値を設定する
    // 日付(D1)
    $time = new DateTime($invoiceMonth);
    $time->modify('last day of this month');
    $title = $time->format('Y年m月');
    $fileName = '請求集計_' . $time->format('Ym') . '_通貨名.xlsx';
    $sheet->GetCell('D1')->SetValue($title . "請求明細　（通貨＝￥）");
    $sheet->GetCell('D237')->SetValue($title . "請求明細　（通貨＝ＵＳ＄）");
    $sheet->GetCell('D311')->SetValue($title . "請求明細　（通貨＝ＨＫ＄）");

    $time2 = $time->modify('last day of this month')->modify('+15 days')->format('Y年m月');
    $sheet->GetCell('D2')->SetValue($time2 . "回収予定");
    $sheet->GetCell('D238')->SetValue($time2 . "回収予定");
    $sheet->GetCell('D312')->SetValue($time2 . "回収予定");

    // シートの行数調整
    if ($addCell1_1 > 0) {
        $sheet->insertNewRowBefore(8, $addCell1_1); // 8行目の上に$addCell1_1行分挿入
    }
    if ($addCell1_2 > 0) {
        $addRow = 209 + $addCell1_1;
        $sheet->insertNewRowBefore($addRow, $addCell1_2); // 209+α行目の上に$addCell1_2行分挿入
    }

    // データ転写
    $sheet->fromArray($writeData1_1, null, $writeCell1_1);
    $sheet->fromArray($writeData1_2, null, $writeCell1_2);
    $sheet->fromArray($writeData1_3, null, $writeCell1_3);
    $sheet->fromArray($writeData1_4, null, $writeCell1_4);

    setCurrencyFormatCode($sheet, "7", "小計", 7, 173, "円");
    setCurrencyFormatCode($sheet, "5", "請求額合計", 174, 174, "円");

    $sheet->fromArray($writeData2_1, null, $writeCell2_1);
    $sheet->fromArray($writeData2_2, null, $writeCell2_2);
    $sheet->fromArray($writeData2_3, null, $writeCell2_3);
    $sheet->fromArray($writeData2_4, null, $writeCell2_4);

    setCurrencyFormatCode($sheet, "7", "小計", 242, 272, "USドル");
    setCurrencyFormatCode($sheet, "5", "請求額合計", 273, 273, "USドル");

    $sheet->fromArray($writeData3_1, null, $writeCell3_1);
    $sheet->fromArray($writeData3_2, null, $writeCell3_2);
    $sheet->fromArray($writeData3_3, null, $writeCell3_3);
    $sheet->fromArray($writeData3_4, null, $writeCell3_4);

    setCurrencyFormatCode($sheet, "7", "小計", 316, 346, "HKドル");
    setCurrencyFormatCode($sheet, "5", "請求額合計", 347, 347, "HKドル");
    $outFile = FILE_UPLOAD_TMPDIR . $fileName;

    //データを書き込む
    $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    // ダウンロード開始
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Transfer-Encoding: binary');
    $writer->save('php://output');

    ini_set('default_charset', 'UTF-8');

    return;

} else {
    // HTML出力
    $aryData['noDataMsg'] = '';
    echo fncGetReplacedHtmlWithBase("inv/base_aggregate.html", "inv/aggregate/index.tmpl", $aryData, $objAuth);
}
return true;

/**
 * 数字フォーマットの取得
 *
 * @param [string] $currencyClass
 * @return string
 */
function getNumberFormat($currencyClass)
{
    if (strcmp($currencyClass, '円') == 0) {
        $numberFormat = '"¥"#,##0;[Red]"¥"-#,##0;';
    } else if (strcmp($currencyClass, 'USドル') == 0) {
        $numberFormat = '"US$"#,##0.00;[Red]-"US$"#,##0.00';
    } else if (strcmp($currencyClass, 'HKドル') == 0) {
        $numberFormat = '[$HK$-zh-HK]#,##0.00;[Red]-[$HK$-zh-HK]#,##0.00';
    }
    return $numberFormat;
}

/**
 * 指定した行のセルの通貨フォーマットの設定
 *　例：G8の設定値が小計の場合、H8:J8の書式を'"¥"#,##0;[Red]"¥"-#,##0;'に設定
 * @param [type] $sheet
 * @param [type] $chkCol
 * @param [type] $chkStr
 * @param [type] $startRow
 * @param [type] $endRow
 * @param [type] $currencyClass
 * @return void
 */
function setCurrencyFormatCode($sheet, $chkCol, $chkStr, $startRow, $endRow, $currencyClass)
{
    $numberFormat = getNumberFormat($currencyClass);
    for ($row = $startRow; $row <= $endRow; $row++) {
        $alphaChkCol = Coordinate::stringFromColumnIndex($chkCol);
        $chkAddress = $alphaChkCol . $row;
        $value = $sheet->getCell($chkAddress)->getValue();
        if ($value == $chkStr) {
            $newAddress = "J" . $row . ":L" . $row;
            $sheet->getStyle($newAddress)->getNumberFormat()->setFormatCode($numberFormat);
            $sheet->getStyle($newAddress)->getFont()->setBold(true);
            $sheet->getStyle($chkAddress)->getFont()->setBold(true);
        }

    }
}
