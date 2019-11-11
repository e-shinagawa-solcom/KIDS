<?php
// ----------------------------------------------------------------------------
/**
 *       �������  ���ὸ�ײ���
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
 *       ��������
 *         �����ꤵ�줿��������򥨥�����ˤƽ��Ϥ���
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------


    // �����ɤ߹���
    include_once('conf.inc');

    // �饤�֥���ɤ߹���
    require (LIB_FILE);
    require (SRC_ROOT . "m/cmn/lib_m.php");
    require (SRC_ROOT . "inv/cmn/lib_regist.php");
    require (SRC_ROOT . "inv/cmn/column.php");

    // PhpSpreadshee��Ȥ�����
    require_once (VENDOR_AUTOLOAD_FILE);

    // ���֥�����������
    $objDB   = new clsDB();
    $objAuth = new clsAuth();

    // DB�����ץ�
    $objDB->open("", "", "", "");

    // �ѥ�᡼������
    if ( $_POST )
    {
        $aryData = $_POST;
    }
    elseif ( $_GET )
    {
        $aryData = $_GET;
    }

    // cookie��SET
    if( !empty($aryData["strSessionID"]) )
        setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

    // ʸ��������å�
    $aryCheck["strSessionID"] = "null:numenglish(32,32)";

    // ���å�����ǧ
    $objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

    // 2200 �������
    if ( !fncCheckAuthority( DEF_FUNCTION_INV0, $objAuth ) )
    {
        fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
    }

    // 2202 ����񸡺�
    if ( !fncCheckAuthority( DEF_FUNCTION_INV2, $objAuth ) )
    {
        fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
    }

    // 2203 ���ὸ��
    if ( !fncCheckAuthority( DEF_FUNCTION_INV4, $objAuth ) )
    {
        fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
    }

    // �إ���б�
    $aryData["lngFunctionCode"] = DEF_FUNCTION_INV0;

    if(isset($aryData["strMode"]) && $aryData["strMode"] == 'export')
    {

        //�ܺٲ��̤�ɽ��
        $invoiceMonth = $aryData["invoiceMonth"].'-01';

        // �����������ޥ�������
        $strQuery = fncGetInvoiceAggregateSQL ( $invoiceMonth );

        // �ܺ٥ǡ����μ���
        list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

        if ( !$lngResultNum )
        {
            $objDB->freeResult( $lngResultID );
            $objDB->close();
            // HTML����
            $aryData['noDataMsg'] = str_replace('-', 'ǯ' ,$aryData["invoiceMonth"]) .'��������ϸ��Ĥ���ޤ���Ǥ�����';
            echo fncGetReplacedHtmlWithBase("inv/base_aggregate.html", "inv/aggregate/index.tmpl", $aryData ,$objAuth );
            return;
        }

        // ������������Ѥ˥ǡ�����ù�
        // array[lngmonetaryunitcode][strcustomercode][]
        $aggregateData = [];
        if ( $lngResultNum )
        {
            for ( $i = 0; $i < $lngResultNum; $i++ )
            {
                $exportAry = [];
                $aryDetailResult = $objDB->fetchArray( $lngResultID, $i );
                // lngmonetaryunitcode�̤�����˳�Ǽ
                $monetaryunitcode = (int)$aryDetailResult['lngmonetaryunitcode'];
                // lngmonetaryunitcode�̤�����˳�Ǽ
                $strcustomercode  = $aryDetailResult['strcustomercode'];

                // �ܵҥ����ɡ��ܵ�̾���ܵҼ�̾
                list ($printCustomerName, $printCompanyName, $customerCode, $strcompanydisplayname ) = fncGetCompanyPrintName($aryDetailResult['strcustomercode'], $objDB);

                $exportAry['lnginvoiceno']              = $aryDetailResult['lnginvoiceno'];
                $exportAry['lngrevisionno']             = $aryDetailResult['lngrevisionno'];
                // �ܵҥ�����(ɽ����)
                $strcustomercode                        = $aryDetailResult['strcustomercode'];
                $exportAry['strcustomercode']           = $strcustomercode;
                // ɽ���Ѹܵ�̾�����̾�������
                $exportAry['strcustomername']           = $printCustomerName;
                $exportAry['strcustomercompanyname']    = $printCompanyName;
                $exportAry['strinvoicecode']            = $aryDetailResult['strinvoicecode'];

                $exportAry['curlastmonthbalance']       = $aryDetailResult['curlastmonthbalance'];
                $exportAry['curthismonthamount']        = $aryDetailResult['curthismonthamount'];
                $exportAry['lngmonetaryunitcode']       = $aryDetailResult['lngmonetaryunitcode'];
                $exportAry['lngtaxclasscode']           = $aryDetailResult['lngtaxclasscode'];
                $exportAry['strtaxclassname']           = $aryDetailResult['strtaxclassname'];
                // ��ȴ���1
                $cursubtotal1                           = (int)$aryDetailResult['cursubtotal1'];
                $exportAry['cursubtotal1']              = $cursubtotal1;
                // ������Ψ1
                $curtax1                                = (int)$aryDetailResult['curtax1'];
                $exportAry['curtax1']                   = $curtax1;
                // �����ǳ�1
                $curtaxprice1                           = (int)$aryDetailResult['curtaxprice1'];
                $exportAry['curtaxprice1']              = $curtaxprice1;

                // �ܵҤ��ȤΥǡ�����ޤȤ��
                $aggregateData[$monetaryunitcode][$strcustomercode][] = $exportAry;
                // �ܵ���ι����
                $aggregateData[$monetaryunitcode][$strcustomercode]['cursubtotal'] += $cursubtotal1;
                $aggregateData[$monetaryunitcode][$strcustomercode]['curtaxprice'] += $curtaxprice1;
            }
        }

        $objDB->freeResult( $lngResultID );

        ini_set('default_charset','UTF-8');

        // 1.���ܱ� �ܵ���ν���
        $row = [];
        // �񤭹��߹Կ�
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
                // �Կ��������
                $writeRow++;
            }
            // �����
            $row[] = [
                '' ,
                '' ,
                '' ,
                '����' ,
                $val['cursubtotal'] ,
                $val['curtaxprice'],
            ];
            // �Կ��������
            $writeRow++;
            // �Դ�
            $row[] = [];
            // �Կ��������
            $writeRow++;
        }
        // �񤭹��ߥǡ�����mb_convert_encoding
        $writeData1_1 = mb_convert_encoding($row, 'UTF-8','EUC-JP' );
        // �񤭹��߳��Ϲ� (D7)
        $writeCell1_1 = 'D'.'7';
        // ������(157��)
        $addCell1_1 = 0;
        if($writeRow > 157) {
            $addCell1_1 = $writeRow-157;
        }
        // ������Total
        $addCellTotal += $addCell1_1;

        // 2.���ܱ� �ܵ�̾��Null�ʳ��ν���
        // 3.���ܱ� 6102�ν���
        // 4.���ܱ� 4410�ν���
        $row = [];
        $row6102 = [];
        $row4410 = [];
        // �񤭹��߹Կ�
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
                    // �Կ��������
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
        // �񤭹��ߥǡ�����mb_convert_encoding
        $writeData1_2 = mb_convert_encoding($row, 'UTF-8','EUC-JP' );
        // �񤭹��߳��Ϲ� (E209)
        $writeCell1_2 = 'E' .(209+$addCellTotal);
        // ������(6��)
        $addCell1_2 = 0;
        if($writeRow > 6) {
            $addCell1_2 = $writeRow-6;
        }
        // ������Total
        $addCellTotal += $addCell1_2;

        // �񤭹��ߥǡ�����mb_convert_encoding
        $writeData1_3 = mb_convert_encoding($row6102, 'UTF-8','EUC-JP' );
        // �񤭹��߳��Ϲ� (E216)
        $writeCell1_3 = 'E' .(216+$addCellTotal);
        // �񤭹��ߥǡ�����mb_convert_encoding
        $writeData1_4 = mb_convert_encoding($row4410, 'UTF-8','EUC-JP' );
        // �񤭹��߳��Ϲ� (E217)
        $writeCell1_4 = 'E' .(217+$addCellTotal);


        // 1.�ɥ� �ܵ���ν���
        $row = [];
        // �񤭹��߹Կ�
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
                // �Կ��������
                $writeRow++;
            }
            // �����
            $row[] = [
                '' ,
                '' ,
                '' ,
                '����' ,
                $val['cursubtotal'] ,
                $val['curtaxprice'],
            ];
            // �Կ��������
            $writeRow++;
            // �Դ�
            $row[] = [];
            // �Կ��������
            $writeRow++;
        }
        // �񤭹��ߥǡ�����mb_convert_encoding
        $row = [];
        $writeData2_1 = mb_convert_encoding($row, 'UTF-8','EUC-JP' );
        // �񤭹��߳��Ϲ� (D242)
        $writeCell2_1 = 'D'.(242+$addCellTotal);
        // ������(31��)
        $addCell2_1 = 0;
        if($writeRow > 31) {
            $addCell2_1 = $writeRow-31;
        }
        // ������Total
        $addCellTotal += $addCell2_1;

        // 2.�ɥ� �ܵ�̾��Null�ʳ��ν���
        // 3.�ɥ� 6102�ν���
        // 4.�ɥ� 4410�ν���
        $row = [];
        $row6102 = [];
        $row4410 = [];
        // �񤭹��߹Կ�
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
                    // �Կ��������
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
        // �񤭹��ߥǡ�����mb_convert_encoding
        $row = [];
        $writeData2_2 = mb_convert_encoding($row, 'UTF-8','EUC-JP' );
        // �񤭹��߳��Ϲ� (E298)
        $writeCell2_2 = 'E' .(298+$addCellTotal);
        // ������(6��)
        $addCell2_2 = 0;
        if($writeRow > 6) {
            $addCell2_2 = $writeRow-6;
        }
        // ������Total
        $addCellTotal += $addCell2_2;

        // �񤭹��ߥǡ�����mb_convert_encoding
        $writeData2_3 = mb_convert_encoding($row6102, 'UTF-8','EUC-JP' );
        // �񤭹��߳��Ϲ� (E305)
        $writeCell2_3 = 'E' .(305+$addCellTotal);
        // �񤭹��ߥǡ�����mb_convert_encoding
        $writeData2_4 = mb_convert_encoding($row4410, 'UTF-8','EUC-JP' );
        // �񤭹��߳��Ϲ� (E306)
        $writeCell2_4 = 'E' .(306+$addCellTotal);


        // 1.HK�ɥ� �ܵ���ν���
        $row = [];
        // �񤭹��߹Կ�
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
                // �Կ��������
                $writeRow++;
            }
            // �����
            $row[] = [
                '' ,
                '' ,
                '' ,
                '����' ,
                $val['cursubtotal'] ,
                $val['curtaxprice'],
            ];
            // �Կ��������
            $writeRow++;
            // �Դ�
            $row[] = [];
            // �Կ��������
            $writeRow++;
        }
        // �񤭹��ߥǡ�����mb_convert_encoding
        $row = ['3-1�ɥ볫��'];
        $writeData3_1 = mb_convert_encoding($row, 'UTF-8','EUC-JP' );
        // �񤭹��߳��Ϲ� (D316)
        $writeCell3_1 = 'D'.(316+$addCellTotal);
        // ������(31��)
        $addCell3_1 = 0;
        if($writeRow > 31) {
            $addCell3_1 = $writeRow-31;
        }
        // ������Total
        $addCellTotal += $addCell3_1;

        // 2.�ɥ� �ܵ�̾��Null�ʳ��ν���
        // 3.�ɥ� 6102�ν���
        // 4.�ɥ� 4410�ν���
        $row = [];
        $row6102 = [];
        $row4410 = [];
        // �񤭹��߹Կ�
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
                    // �Կ��������
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
        // �񤭹��ߥǡ�����mb_convert_encoding
        $row = [];
        $writeData3_2 = mb_convert_encoding($row, 'UTF-8','EUC-JP' );
        // �񤭹��߳��Ϲ� (E372)
        $writeCell3_2 = 'E' .(372+$addCellTotal);
        // ������(6��)
        $addCell3_2 = 0;
        if($writeRow > 6) {
            $addCell3_2 = $writeRow-6;
        }
        // ������Total
        $addCellTotal += $addCell3_2;

        // �񤭹��ߥǡ�����mb_convert_encoding
        $writeData3_3 = mb_convert_encoding($row6102, 'UTF-8','EUC-JP' );
        // �񤭹��߳��Ϲ� (E379)
        $writeCell3_3 = 'E' .(379+$addCellTotal);
        // �񤭹��ߥǡ�����mb_convert_encoding
        $writeData3_4 = mb_convert_encoding($row4410, 'UTF-8','EUC-JP' );
        // �񤭹��߳��Ϲ� (E380)
        $writeCell3_4 = 'E' .(380+$addCellTotal);

        // �ɤ߹�������
        $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();

        // �ƥ�ץ졼�ȥե�����̾
        $baseFile = TMP_ROOT.'inv/aggregate.xlsx';
        $reader->setReadDataOnly(false);
        $baseSheet = $reader->load($baseFile);

        //�ƥ�ץ졼�Ȥ�ʣ��
        $clonedSheet = clone $baseSheet;
        $sheet = $clonedSheet->getActiveSheet();
        // �񤭹�����Ƭ��
        $topRow = 8;

        // �֥å����ͤ����ꤹ��
        // ����(D1)
        $time = new DateTime($invoiceMonth);
//         $timeStamp = $time->getTimestamp();
//         $excelDateValue = new PhpOffice\PhpSpreadsheet\Date::PHPToExcel( $timeStamp );
        $title = mb_convert_encoding($time->format('Y/m/d'), 'UTF-8','EUC-JP' );
        $sheet->GetCell('D1')->SetValue($title);
//         $sheet->getStyle('D1')->getNumberFormat()->setFormatCode('gggeǯm���������١����̲ߡ���');

        // �����ȤιԿ�Ĵ��
        if($addCell1_1 > 0) {
            $sheet->insertNewRowBefore(8, $addCell1_1);         // 8���ܤξ��$addCell1_1��ʬ����
        }
        if($addCell1_2 > 0) {
            $addRow = 209+$addCell1_1;
            $sheet->insertNewRowBefore($addRow, $addCell1_2);   // 209+�����ܤξ��$addCell1_2��ʬ����
        }

        // �ǡ���ž��
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

        $fileName = '���ὸ��_' .$time->format('Ym') .'_�̲�̾.xlsx';
        $outFile  = mb_convert_encoding(FILE_UPLOAD_TMPDIR.$fileName, 'UTF-8','EUC-JP' );

        //�ǡ�����񤭹���
        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($clonedSheet);

//         $writer->save($outFile);

        // ��������ɳ���
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
       // HTML����
        $aryData['noDataMsg'] = '';
        echo fncGetReplacedHtmlWithBase("inv/base_aggregate.html", "inv/aggregate/index.tmpl", $aryData ,$objAuth );
   }
   return true;

    ?>

