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
    include_once('conf.inc');

    // ライブラリ読み込み
    require (LIB_FILE);
    require (SRC_ROOT . "m/cmn/lib_m.php");
    require (SRC_ROOT . "inv/cmn/lib_regist.php");
    require (SRC_ROOT . "inv/cmn/column.php");

    // PhpSpreadsheeを使うため
    require_once (VENDOR_AUTOLOAD_FILE);

    // オブジェクト生成
    $objDB   = new clsDB();
    $objAuth = new clsAuth();

    // DBオープン
    $objDB->open("", "", "", "");

    // パラメータ取得
    if ( $_POST )
    {
        $aryData = $_POST;
    }
    elseif ( $_GET )
    {
        $aryData = $_GET;
    }

    // cookieにSET
    if( !empty($aryData["strSessionID"]) )
        setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

    // 文字列チェック
    $aryCheck["strSessionID"] = "null:numenglish(32,32)";

    // セッション確認
    $objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

    // 2200 請求管理
    if ( !fncCheckAuthority( DEF_FUNCTION_INV0, $objAuth ) )
    {
        fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
    }

    // 2202 請求書検索
    if ( !fncCheckAuthority( DEF_FUNCTION_INV2, $objAuth ) )
    {
        fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
    }

    // 2203 請求集計
    if ( !fncCheckAuthority( DEF_FUNCTION_INV4, $objAuth ) )
    {
        fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
    }

    // ヘルプ対応
    $aryData["lngFunctionCode"] = DEF_FUNCTION_INV0;

    if(isset($aryData["strMode"]) && $aryData["strMode"] == 'export')
    {

        //詳細画面の表示
        $invoiceMonth = $aryData["invoiceMonth"].'-01';

        // 指定月の請求書マスタ取得
        $strQuery = fncGetInvoiceAggregateSQL ( $invoiceMonth );

        // 詳細データの取得
        list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

        if ( !$lngResultNum )
        {
            $objDB->freeResult( $lngResultID );
            $objDB->close();
            // HTML出力
            $aryData['noDataMsg'] = str_replace('-', '年' ,$aryData["invoiceMonth"]) .'月の請求書は見つかりませんでした。';
            echo fncGetReplacedHtmlWithBase("inv/base_aggregate.html", "inv/aggregate/index.tmpl", $aryData ,$objAuth );
            return;
        }

        // エクセル出力用にデータを加工
        // array[lngmonetaryunitcode][strcustomercode][]
        $aggregateData = [];
        if ( $lngResultNum )
        {
            for ( $i = 0; $i < $lngResultNum; $i++ )
            {
                $exportAry = [];
                $aryDetailResult = $objDB->fetchArray( $lngResultID, $i );
                // lngmonetaryunitcode別の配列に格納
                $monetaryunitcode = (int)$aryDetailResult['lngmonetaryunitcode'];
                // lngmonetaryunitcode別の配列に格納
                $strcustomercode  = $aryDetailResult['strcustomercode'];

                // 顧客コード・顧客名・顧客社名
                list ($printCustomerName, $printCompanyName, $customerCode, $strcompanydisplayname ) = fncGetCompanyPrintName($aryDetailResult['strcustomercode'], $objDB);

                $exportAry['lnginvoiceno']              = $aryDetailResult['lnginvoiceno'];
                $exportAry['lngrevisionno']             = $aryDetailResult['lngrevisionno'];
                // 顧客コード(表示用)
                $strcustomercode                        = $aryDetailResult['strcustomercode'];
                $exportAry['strcustomercode']           = $strcustomercode;
                // 表示用顧客名・会社名を入れる
                $exportAry['strcustomername']           = $printCustomerName;
                $exportAry['strcustomercompanyname']    = $printCompanyName;
                $exportAry['strinvoicecode']            = $aryDetailResult['strinvoicecode'];

                $exportAry['curlastmonthbalance']       = $aryDetailResult['curlastmonthbalance'];
                $exportAry['curthismonthamount']        = $aryDetailResult['curthismonthamount'];
                $exportAry['lngmonetaryunitcode']       = $aryDetailResult['lngmonetaryunitcode'];
                $exportAry['lngtaxclasscode']           = $aryDetailResult['lngtaxclasscode'];
                $exportAry['strtaxclassname']           = $aryDetailResult['strtaxclassname'];
                // 税抜金額1
                $cursubtotal1                           = (int)$aryDetailResult['cursubtotal1'];
                $exportAry['cursubtotal1']              = $cursubtotal1;
                // 消費税率1
                $curtax1                                = (int)$aryDetailResult['curtax1'];
                $exportAry['curtax1']                   = $curtax1;
                // 消費税額1
                $curtaxprice1                           = (int)$aryDetailResult['curtaxprice1'];
                $exportAry['curtaxprice1']              = $curtaxprice1;

                // 顧客ごとのデータをまとめる
                $aggregateData[$monetaryunitcode][$strcustomercode][] = $exportAry;
                // 顧客毎の合計値
                $aggregateData[$monetaryunitcode][$strcustomercode]['cursubtotal'] += $cursubtotal1;
                $aggregateData[$monetaryunitcode][$strcustomercode]['curtaxprice'] += $curtaxprice1;
            }
        }

        $objDB->freeResult( $lngResultID );

        ini_set('default_charset','UTF-8');

        // 1.日本円 顧客毎の集計
        $row = [];
        // 書き込み行数
        $writeRow = 0;
        foreach((array)$aggregateData[1] as $code => $val) {
            for($i=0; $i+2 < COUNT($val); $i++){
                $row[] = [
                    $val[$i]['strcustomercompanyname'] ,
                    $val[$i]['strcustomername'] ,
                    '',
                    $val[$i]['strinvoicecode'] ,
                    $val[$i]['cursubtotal1'] ,
                    $val[$i]['curtaxprice1']
                    ];
                // 行数カウント
                $writeRow++;
            }
            // 合計値
            $row[] = [
                '' ,
                '' ,
                '' ,
                '小計' ,
                $val['cursubtotal'] ,
                $val['curtaxprice'],
            ];
            // 行数カウント
            $writeRow++;
            // 行間
            $row[] = [];
            // 行数カウント
            $writeRow++;
        }
        // 書き込みデータのmb_convert_encoding
        $writeData1_1 = mb_convert_encoding($row, 'UTF-8','EUC-JP' );
        // 書き込み開始行 (D7)
        $writeCell1_1 = 'D'.'7';
        // 挿入行(157行)
        $addCell1_1 = 0;
        if($writeRow > 157) {
            $addCell1_1 = $writeRow-157;
        }
        // 挿入行Total
        $addCellTotal += $addCell1_1;

        // 2.日本円 顧客名がNull以外の集計
        // 3.日本円 6102の集計
        // 4.日本円 4410の集計
        $row = [];
        $row6102 = [];
        $row4410 = [];
        // 書き込み行数
        $writeRow = 0;
        foreach((array)$aggregateData[1] as $code => $val) {
            for($i=0; $i+2 < COUNT($val); $i++){
                if($val[$i]['strcustomername']) {
                    $row[] = [
                        $val[$i]['strcustomercompanyname'] ,
                        '' ,
                        '',
                        $val[$i]['cursubtotal1'] ,
                        $val[$i]['curtaxprice1']
                    ];
                    // 行数カウント
                    $writeRow++;
                }
                if($val[$i]['strcustomercode'] == '6102') {
                    $row6102[] = [
                        $val[$i]['strcustomercompanyname'] ,
                        '' ,
                        '',
                        $val[$i]['cursubtotal1'] ,
                        $val[$i]['curtaxprice1']
                    ];
                }
                if($val[$i]['strcustomercode'] == '4410') {
                    $row4410[] = [
                        $val[$i]['strcustomercompanyname'] ,
                        '' ,
                        '',
                        $val[$i]['cursubtotal1'] ,
                        $val[$i]['curtaxprice1']
                    ];
                }
            }
        }
        // 書き込みデータのmb_convert_encoding
        $writeData1_2 = mb_convert_encoding($row, 'UTF-8','EUC-JP' );
        // 書き込み開始行 (E209)
        $writeCell1_2 = 'E' .(209+$addCellTotal);
        // 挿入行(6行)
        $addCell1_2 = 0;
        if($writeRow > 6) {
            $addCell1_2 = $writeRow-6;
        }
        // 挿入行Total
        $addCellTotal += $addCell1_2;

        // 書き込みデータのmb_convert_encoding
        $writeData1_3 = mb_convert_encoding($row6102, 'UTF-8','EUC-JP' );
        // 書き込み開始行 (E216)
        $writeCell1_3 = 'E' .(216+$addCellTotal);
        // 書き込みデータのmb_convert_encoding
        $writeData1_4 = mb_convert_encoding($row4410, 'UTF-8','EUC-JP' );
        // 書き込み開始行 (E217)
        $writeCell1_4 = 'E' .(217+$addCellTotal);


        // 1.ドル 顧客毎の集計
        $row = [];
        // 書き込み行数
        $writeRow = 0;
        foreach((array)$aggregateData[2] as $code => $val) {
            for($i=0; $i+2 < COUNT($val); $i++){
                $row[] = [
                    $val[$i]['strcustomercompanyname'] ,
                    $val[$i]['strcustomername'] ,
                    '',
                    $val[$i]['strinvoicecode'] ,
                    $val[$i]['cursubtotal1'] ,
                    $val[$i]['curtaxprice1']
                ];
                // 行数カウント
                $writeRow++;
            }
            // 合計値
            $row[] = [
                '' ,
                '' ,
                '' ,
                '小計' ,
                $val['cursubtotal'] ,
                $val['curtaxprice'],
            ];
            // 行数カウント
            $writeRow++;
            // 行間
            $row[] = [];
            // 行数カウント
            $writeRow++;
        }
        // 書き込みデータのmb_convert_encoding
        $row = [];
        $writeData2_1 = mb_convert_encoding($row, 'UTF-8','EUC-JP' );
        // 書き込み開始行 (D242)
        $writeCell2_1 = 'D'.(242+$addCellTotal);
        // 挿入行(31行)
        $addCell2_1 = 0;
        if($writeRow > 31) {
            $addCell2_1 = $writeRow-31;
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
        foreach((array)$aggregateData[2] as $code => $val) {
            for($i=0; $i+2 < COUNT($val); $i++){
                if($val[$i]['strcustomername']) {
                    $row[] = [
                        $val[$i]['strcustomercompanyname'] ,
                        '' ,
                        '',
                        $val[$i]['cursubtotal1'] ,
                        $val[$i]['curtaxprice1']
                    ];
                    // 行数カウント
                    $writeRow++;
                }
                if($val[$i]['strcustomercode'] == '6102') {
                    $row6102[] = [
                        $val[$i]['strcustomercompanyname'] ,
                        '' ,
                        '',
                        $val[$i]['cursubtotal1'] ,
                        $val[$i]['curtaxprice1']
                    ];
                }
                if($val[$i]['strcustomercode'] == '4410') {
                    $row4410[] = [
                        $val[$i]['strcustomercompanyname'] ,
                        '' ,
                        '',
                        $val[$i]['cursubtotal1'] ,
                        $val[$i]['curtaxprice1']
                    ];
                }
            }
        }
        // 書き込みデータのmb_convert_encoding
        $row = [];
        $writeData2_2 = mb_convert_encoding($row, 'UTF-8','EUC-JP' );
        // 書き込み開始行 (E298)
        $writeCell2_2 = 'E' .(298+$addCellTotal);
        // 挿入行(6行)
        $addCell2_2 = 0;
        if($writeRow > 6) {
            $addCell2_2 = $writeRow-6;
        }
        // 挿入行Total
        $addCellTotal += $addCell2_2;

        // 書き込みデータのmb_convert_encoding
        $writeData2_3 = mb_convert_encoding($row6102, 'UTF-8','EUC-JP' );
        // 書き込み開始行 (E305)
        $writeCell2_3 = 'E' .(305+$addCellTotal);
        // 書き込みデータのmb_convert_encoding
        $writeData2_4 = mb_convert_encoding($row4410, 'UTF-8','EUC-JP' );
        // 書き込み開始行 (E306)
        $writeCell2_4 = 'E' .(306+$addCellTotal);


        // 1.HKドル 顧客毎の集計
        $row = [];
        // 書き込み行数
        $writeRow = 0;
        foreach((array)$aggregateData[3] as $code => $val) {
            for($i=0; $i+2 < COUNT($val); $i++){
                $row[] = [
                    $val[$i]['strcustomercompanyname'] ,
                    $val[$i]['strcustomername'] ,
                    '',
                    $val[$i]['strinvoicecode'] ,
                    $val[$i]['cursubtotal1'] ,
                    $val[$i]['curtaxprice1']
                ];
                // 行数カウント
                $writeRow++;
            }
            // 合計値
            $row[] = [
                '' ,
                '' ,
                '' ,
                '小計' ,
                $val['cursubtotal'] ,
                $val['curtaxprice'],
            ];
            // 行数カウント
            $writeRow++;
            // 行間
            $row[] = [];
            // 行数カウント
            $writeRow++;
        }
        // 書き込みデータのmb_convert_encoding
        $row = ['3-1ドル開始'];
        $writeData3_1 = mb_convert_encoding($row, 'UTF-8','EUC-JP' );
        // 書き込み開始行 (D316)
        $writeCell3_1 = 'D'.(316+$addCellTotal);
        // 挿入行(31行)
        $addCell3_1 = 0;
        if($writeRow > 31) {
            $addCell3_1 = $writeRow-31;
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
        foreach((array)$aggregateData[3] as $code => $val) {
            for($i=0; $i+2 < COUNT($val); $i++){
                if($val[$i]['strcustomername']) {
                    $row[] = [
                        $val[$i]['strcustomercompanyname'] ,
                        '' ,
                        '',
                        $val[$i]['cursubtotal1'] ,
                        $val[$i]['curtaxprice1']
                    ];
                    // 行数カウント
                    $writeRow++;
                }
                if($val[$i]['strcustomercode'] == '6102') {
                    $row6102[] = [
                        $val[$i]['strcustomercompanyname'] ,
                        '' ,
                        '',
                        $val[$i]['cursubtotal1'] ,
                        $val[$i]['curtaxprice1']
                    ];
                }
                if($val[$i]['strcustomercode'] == '4410') {
                    $row4410[] = [
                        $val[$i]['strcustomercompanyname'] ,
                        '' ,
                        '',
                        $val[$i]['cursubtotal1'] ,
                        $val[$i]['curtaxprice1']
                    ];
                }
            }
        }
        // 書き込みデータのmb_convert_encoding
        $row = [];
        $writeData3_2 = mb_convert_encoding($row, 'UTF-8','EUC-JP' );
        // 書き込み開始行 (E372)
        $writeCell3_2 = 'E' .(372+$addCellTotal);
        // 挿入行(6行)
        $addCell3_2 = 0;
        if($writeRow > 6) {
            $addCell3_2 = $writeRow-6;
        }
        // 挿入行Total
        $addCellTotal += $addCell3_2;

        // 書き込みデータのmb_convert_encoding
        $writeData3_3 = mb_convert_encoding($row6102, 'UTF-8','EUC-JP' );
        // 書き込み開始行 (E379)
        $writeCell3_3 = 'E' .(379+$addCellTotal);
        // 書き込みデータのmb_convert_encoding
        $writeData3_4 = mb_convert_encoding($row4410, 'UTF-8','EUC-JP' );
        // 書き込み開始行 (E380)
        $writeCell3_4 = 'E' .(380+$addCellTotal);

        // 読み込み設定
        $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();

        // テンプレートファイル名
        $baseFile = TMP_ROOT.'inv/aggregate.xlsx';
        $reader->setReadDataOnly(false);
        $baseSheet = $reader->load($baseFile);

        //テンプレートの複写
        $clonedSheet = clone $baseSheet;
        $sheet = $clonedSheet->getActiveSheet();
        // 書き込み先頭行
        $topRow = 8;

        // ブックに値を設定する
        // 日付(D1)
        $time = new DateTime($invoiceMonth);
//         $timeStamp = $time->getTimestamp();
//         $excelDateValue = new PhpOffice\PhpSpreadsheet\Date::PHPToExcel( $timeStamp );
        $title = mb_convert_encoding($time->format('Y/m/d'), 'UTF-8','EUC-JP' );
        $sheet->GetCell('D1')->SetValue($title);
//         $sheet->getStyle('D1')->getNumberFormat()->setFormatCode('ggge年m月請求明細　（通貨＝￥）');

        // シートの行数調整
        if($addCell1_1 > 0) {
            $sheet->insertNewRowBefore(8, $addCell1_1);         // 8行目の上に$addCell1_1行分挿入
        }
        if($addCell1_2 > 0) {
            $addRow = 209+$addCell1_1;
            $sheet->insertNewRowBefore($addRow, $addCell1_2);   // 209+α行目の上に$addCell1_2行分挿入
        }

        // データ転写
        $sheet->fromArray($writeData1_1,NULL,$writeCell1_1);
        $sheet->fromArray($writeData1_2,NULL,$writeCell1_2);
        $sheet->fromArray($writeData1_3,NULL,$writeCell1_3);
        $sheet->fromArray($writeData1_4,NULL,$writeCell1_4);

        $sheet->fromArray($writeData2_1,NULL,$writeCell2_1);
        $sheet->fromArray($writeData2_2,NULL,$writeCell2_2);
        $sheet->fromArray($writeData2_3,NULL,$writeCell2_3);
        $sheet->fromArray($writeData2_4,NULL,$writeCell2_4);

        $sheet->fromArray($writeData3_1,NULL,$writeCell3_1);
        $sheet->fromArray($writeData3_2,NULL,$writeCell3_2);
        $sheet->fromArray($writeData3_3,NULL,$writeCell3_3);
        $sheet->fromArray($writeData3_4,NULL,$writeCell3_4);

        $fileName = '請求集計_' .$time->format('Ym') .'_通貨名.xlsx';
        $outFile  = mb_convert_encoding(FILE_UPLOAD_TMPDIR.$fileName, 'UTF-8','EUC-JP' );

        //データを書き込む
        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($clonedSheet);

//         $writer->save($outFile);

        // ダウンロード開始
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="'.mb_convert_encoding($fileName, 'UTF-8','EUC-JP').'"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Transfer-Encoding: binary');
        $writer->save('php://output');

        ini_set('default_charset','EUC-JP');

        return;

    }
    else
   {
       // HTML出力
        $aryData['noDataMsg'] = '';
        echo fncGetReplacedHtmlWithBase("inv/base_aggregate.html", "inv/aggregate/index.tmpl", $aryData ,$objAuth );
   }
   return true;

    ?>

