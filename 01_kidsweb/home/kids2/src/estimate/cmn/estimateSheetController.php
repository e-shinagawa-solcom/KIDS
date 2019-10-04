<?php

// �ɤ߹��ޤ�Ƥ��ʤ����ɬ�ץե�������ɤ߹���
require_once ('conf.inc');

// Composer�Υ����ȥ��ɥե������ɤ߹���
require_once ( VENDOR_AUTOLOAD_FILE );

// ����ե�������ɤ߹���
require_once ( SRC_ROOT . "/estimate/cmn/const/workSheetConst.php");

// phpSpreadSheet�Υ��饹�ɤ߹���
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\Drawing as XlsxDrawing;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


/**
 * excel�ǡ��������ߡ��Ѵ����饹
 * 
 * PHPSpreadSheet�饤�֥�����Ѥ���excel�ե����뤫��ɬ�פʥǡ�������Ϥ���
 *
 */
class estimateSheetController {
    
    protected $reader;
    protected $drawing;

    public $sheet;
    protected $displayInvalid;
    protected $cellAddressList;
    protected $startRow;
    protected $endRow;
    protected $startColumn;
    protected $endColumn;

    protected $targetAreaRows;

    protected $excelErrorList;

    public $errorMessage;
    public $difference;
    

    // �������� 1224:���㡼���׻����ѿ�
    protected $overSeasMoldDepreciation;       // �������� 403:�����ⷿ���Ѥι��
    protected $importPartsCosts;               // �������� 402:͢���ѡ��Ļ�����ι��
    protected $tariff;

    public function __construct() {
        $this->setExcelErrorList();
    }

    public function dataInitialize($sheetInfo, $objDB) {
        $this->setObjectDatabase($objDB);
        $this->setSheetInfo($sheetInfo);
        $this->setRowRangeOfTargetArea();
    }
    
    // �ǡ�������------------------------------------------------------------------------

    // �����ȥ��֥�������
    public function outputSheet() {
        return $this->sheet;
    }

    // �оݥ��ꥢ�γ��ϹԤȽ�λ��
    public function outputTargetAreaRows() {
        return $this->targetAreaRows;
    }

    // ����̾�Τ��б�����������֥ꥹ��
    public function outputCellAddressList() {
        return $cellAddressList;
    }    

    //-----------------------------------------------------------------------------------


    // �ǡ����١������֥������Ȥ򥻥åȤ���
    protected function setObjectDatabase($objDB) {
        $this->objDB = $objDB;
        return true;
    }

    /**
    *	����	: Excel�ե�����ե����ޥåȥ����å�
    *
    *
    *	����	: ���åץ��ɤ��줿Excel�ե�����Υե����ޥåȤ����狼�����å�����
    *
    *	�о�	: ����ѥƥ�ץ졼��
    *
    *	@param	[$exc]		: [Object]	. ExcelParser Object�ʻ����Ϥ���
    *	@param	[$aryData]	: [Array]	. $_REQUEST ������������
    *
    *	@return	[$aryErr]	: [Array]	. ���顼�����å�����
    */
    public static function checkFileFormat( &$file )
    {
        $error = true;
        $checkResult��= '';	// �����å����

        // ��ĥ�Ҥˤ��Ƚ��
        if(!empty($file['exc_type']) || strtolower($file['exc_type']) == APP_EXCEL_TYPE ) {
            // ��ĥ�Ҥ�xlsx�ʤ�ե�����򳫤��Ƴ�ǧ����
            $excel_file	= FILE_UPLOAD_TMPDIR . $file["exc_tmp_name"];
            $finfo  = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $excel_file);
            finfo_close($finfo);

            // MIME_TYPE�ˤ��Ƚ��
            if (!empty($mime_type) && $mime_type == APP_EXCEL_TYPE) {
                if (!empty($file['exc_name'])) {
                    $error = false;
                }
            }
        }
        if ($error === false) {
            return $excel_file;
        } else {
            $file['error'] = true;
            return false;
        }
    }


    // ���ꤷ���Ԥ��ɤ��оݥ��ꥢ��°���뤫��Ƚ�ꤹ��
    public function checkAttributeRow($row) {
        $rowAttribute = null;

        // �оݥ��ꥢ�γ��ϹԤȽ�λ�Ԥ����
        $targetAreaRows = $this->targetAreaRows;

        // �оݥ��ꥢ̾�����
        $targetAreaNameList = workSheetConst::TARGET_AREA_NAME;

        foreach ($targetAreaNameList as $areaCode => $areaDisplayName) {
            if ($targetAreaRows[$areaCode]['firstRow'] <= $row && $row <= $targetAreaRows[$areaCode]['lastRow']) {
                $rowAttribute = $areaCode;
                break;
            }
        }
        return $rowAttribute;
    }


    
    // ��������Ȥȥ���̾�Υꥹ�Ȥξ���򥤥󥹥��󥹤˥��åȤ���
    public function setSheetInfo($sheetInfo) {
        $sheet = $sheetInfo['sheet'];
        $displayInvalid = $sheetInfo['displayInvalid'];
        $cellAddressList = $sheetInfo['cellAddress'];
        $startRow = $sheetInfo['startRow'];
        $endRow = $sheetInfo['endRow'];
        $startColumn = $sheetInfo['startColumn'];
        $endColumn = $sheetInfo['endColumn'];

        $this->sheet = $sheet;
        $this->displayInvalid = $displayInvalid;
        $this->cellAddressList = $cellAddressList;
        $this->startRow = $startRow;
        $this->endRow = $endRow;
        $this->startColumn = $startColumn;
        $this->endColumn = $endColumn;

        return true;
    }

    /**
     * �֥å����饷���Ȥξ���������������̾�Τ��б����륻����֤�������롣
     *
     * @param array $spreadSheet �֥å���phpSpreadSheet���֥�������
     *
     * @return array $sheetInfo �����Ⱦ���
     */
    public static function getSheetInfo($spreadSheet, $nameList, $rowCheckNameList) {
        $cellAddressList = array();
        $separate = array();
        // �����ȿ�����
        $sheetCount = $spreadSheet->getSheetCount();

        $sheetInfo = [];
        
        for ($count = 0; $count < $sheetCount; ++$count) {
            $displayInvalid = false; // ɽ��̵���ե饰

            $sheet = $spreadSheet->getSheet($count);
            $sheetName = $sheet->getTitle();
            if ($sheet->getSheetState() === 'visible') {
                foreach($nameList as $val) {
                    $namedRange = null;
                    // ��������˥���̾�Τ����ꤵ��Ƥ��뤫��������
                    $namedRange = $spreadSheet->getNamedRange($val, $sheet);
                    if ($namedRange && $namedRange->getWorkSheet()->getTitle() === $sheetName) {
                        // �������������Υ�����̾�ȸ������˻��ꤷ��������̾�����פ������Τߥ���̾�Τ��Ф��륻����֤��������������
                        // �оݥ����Ȥ�����Ǥ��ʤ��ä���硢�֥å������ꤵ�줿�ǡ�����������ʤ��褦���뤿����к�
                        $cellAddress = $namedRange->getRange();
                        $cellAddressList[$val] = $cellAddress;
                        $separate[$val] = self::separateRowAndColumn($cellAddress);
                    } else {
                        // ��������Ǹ��Ĥ���ʤ�����̾�Τ����ä����Ͻ��������Ǥ���ɽ��̵���ե饰��Ω�Ƥ�
                        $cellAddressList = array();
                        $displayInvalid = true;
                        break;
                    }
                }
            } else {
                $displayInvalid = true;
            }

            if($separate) {
            $startRow = (int)$separate['top_left']['row'];
            $endRow = (int)$separate['bottom_left']['row'];
            $startColumn = self::getIndexForColumnAlphabet($separate['top_left']['column']);
            $endColumn = self::getIndexForColumnAlphabet($separate['top_right']['column']);
            $columnValue = $endColumn - $startColumn + 1;
                if ($columnValue !== workSheetConst::WORK_SHEET_COLUMN_NUMBER) {
                // �����������Ǥʤ����Ϻ���ե饰��Ω�Ƥ�
                    $displayInvalid = true;
                } else {
                    // �����ϥե�����Υإå�����Ʊ���Ԥ�̵���Ȥ��ϡ��ǡ�������ե饰��Ω�Ƥ�
                    foreach($rowCheckNameList as $array) {
                        if (!self::rowCheck($separate, $array)) {
                            $displayInvalid = true;
                            break;
                        }
                    }
                }
            }
            $sheetInfo[$sheetName] = array(
                'sheet' => $sheet,                   // phpSpreadSheet���������������ȥ��֥�������
                'displayInvalid' => $displayInvalid, // ɽ��̵���ե饰
                'cellAddress' => $cellAddressList,   // ����̾�Τ��б����륻����֤Υꥹ��
                'startRow' => $startRow,             // ���Ϲ�
                'endRow' => $endRow,                 // ��λ��
                'startColumn' => $startColumn,       // ������
                'endColumn' => $endColumn            // ��λ��
            );
        }        
        return $sheetInfo;
    }

    public function setHiddenRowList($list) {
        if ($list) {
            $this->hiddenRowList = $list;
        }
        return true;
    }

    /**
     * �����ȤΥǡ�������������
     *
     * @return string $mode �����⡼��
     *
     * @return array $viewData ���ϥǡ�������
     */
    public function makeDataOfSheet($mode = null) {
        $sheet = $this->sheet;

        $areaNameList = workSheetConst::TARGET_AREA_DISPLAY_NAME_LIST;

        // ���ꥢ���Ȥ�������Ϲ��ܤ������������ʬ��Ϥɤ���ˤ��뤫����
        foreach($areaNameList as $areaCode => $name) {
            $columnNumberList[$areaCode] = $this->getColumnNumberList($areaCode);
        }

        // ɸ��Υե���Ȥ����
        $defaultFont = $sheet->getParent()->getDefaultStyle()->getFont();

        // ������̾�����
        $sheetName = $sheet->getTitle();

        // ��������Ȥγ��Ϲ��󡢽�λ����򥻥åȤ���(��Ͽ��ͤ��Ѵ�����)
        $startRow = $this->startRow;
        $endRow = $this->endRow;
        $startColumn = $this->startColumn;
        $endColumn = $this->endColumn;

        // ��������ȤιԹ⤵�������ξ�����������
        // �Թ⤵
        $rowHeight = $this->getRowHeight();
        // ����
        $columnWidth = $this->getColumnWidth();

        // Handsontable�Ѥ˳��Ϲԡ�������(�ǥե���Ȥ�(0,0))�����ꤹ��
        // ɽ���ѤΥǡ�������Ȥ��Τߡ������������Ϲԡ����������Ѥ���
        //��Excel����ǡ��������������˻��Ѥ���Ȼ��ȥ��뤬����ޤ�)
        $tableStartRow = 0;
        $tableStartColumn = 0;

        // ��������Ȥ�Handsontable�γ��Ϲԡ����������Ӥ��ƥ��ե��̤򻻽Ф���������ѡ�
        $rowShiftValue = $startRow - $tableStartRow; // Row�Υ��ե���
        $tableEndRow = $endRow - $rowShiftValue;     // ���ե�ʬ����������Handsontable�ν�λ�Ԥ򻻽�
        $columnShiftValue = $startColumn - $tableStartColumn; // Column�Υ��ե���
        $tableEndColumn = $endColumn - $columnShiftValue;  // ���ե�ʬ����������Handsontable�ν�λ��򻻽�

        // �ޡ������줿����Υꥹ�Ȥ��������
        $mergedCellsList = $this->getMergedCellsList();
        // �ޡ�������ꥹ�Ȥ�Handsontable�Ѥ���������
        $shiftResult = $this->shiftMergedCellsList($mergedCellsList, $rowShiftValue, $columnShiftValue); 

        // ��������Ⱦ����ɽ���Ԥμ���
        $hiddenRowList = $this->hiddenRowList;

        // ������֤��饻��̾�Τ�����������������
        $nameListForCellAddress = array_flip($this->cellAddressList);

        // DB��������ϥǡ����μ���
        if ($this->inputData) $inputData = $this->inputData;

        // �ѥ�᡼��������
        for ($tableRow = $tableStartRow; $tableRow <= $tableEndRow; ++$tableRow) {

            $workSheetRow = $tableRow + $rowShiftValue; // ��������Ȥι��ֹ�

            // ���ꥢ�����ɤ����
            $areaCode = $this->checkAttributeRow($workSheetRow);

            // ���ֹ�ꥹ�Ȥμ���
            if ($areaCode) {
                $columnNumberList = $this->getColumnNumberList($areaCode);
            }

            // ����ɽ���������Υ���⤵�ꥹ�Ⱥ���
            if ($hiddenRowList[$workSheetRow] !== true) {
                // ������طʿ����������
                $hiddenRowHeight[$tableRow] = $rowHeight[$tableRow];
            } else {
                $hiddenRowHeight[$tableRow] = 0.1;
            }

            // readOnly�ԤΥ��å�
            if ($inputData) { // DB���鸫�Ѹ������٤Υǡ�����������Ƥ�����
                // ��ȯ���ʬ������(���� or ȯ��)
                $receiveAreaCodeList = workSheetConst::RECEIVE_AREA_CODE;
                $orderAreaCodeList = workSheetConst::ORDER_AREA_CODE;
                if ($areaCode) {
                    $data = $inputData[$workSheetRow]['data']; // �ԥǡ����μ���
                    $status = $data['statusCode']; // ���ơ����������ɤμ���
                    $estimateDetailNo = $data['estimateDetailNo']; // ���Ѹ��������ֹ�

                    $detailNoList[$tableRow] = $estimateDetailNo;

                    if ($receiveAreaCodeList[$areaCode] === true) { // ����ξ��

                        if ($status == DEF_RECEIVE_ORDER || $status == DEF_RECEIVE_END) { // �������ʤ���Ǽ�ʺѤξ��
                            $readOnlyDetailRow[] = $tableRow;
                        }
    
                    } else if ($orderAreaCodeList[$areaCode] === true) { // ȯ��ξ��

                        if ($status == DEF_ORDER_ORDER || $status == DEF_ORDER_END) { // ȯ�����ʤ���Ǽ�ʺѤξ��
                            $readOnlyDetailRow[] = $tableRow;
                        }

                    }
                }

            }
           
            for ($tableColumn = $tableStartColumn; $tableColumn <= $tableEndColumn; ++$tableColumn) {
                // �����
                $cellAddress = null;
                $style = null;
                $workSheetColumn = $tableColumn + $startColumn; // ��������Ȥ����ֹ�
                // ������֤���������
                $cellAddress = self::combineRowAndColumnIndex($workSheetRow, self::getAlphabetForColumnIndex($workSheetColumn));

                // ���ֹ��������饢��ե��٥åȤ��Ѵ�
                $colAlphabet = $this->getAlphabetForColumnIndex($workSheetColumn);


                $setFormatFlag = true; // �ե����ޥåȥ��åȥե饰

                // �Խ��⡼�ɻ��ν񼰤�����
                if ($mode === 'edit') {
                    if ($areaCode) {
                        // �����Ϲ��ܤΥإå����Ф������ֹ�ꥹ�Ȥμ���
                        $columnNoList = $this->getColumnNumberList($areaCode);
                        if ($columnNoList['quantity'] == $colAlphabet
                        || $columnNoList['price'] == $colAlphabet) { // ��������ñ���ξ��
                            $setFormatFlag = false;
                        }

                        if ($areaCode == DEF_AREA_OTHER_COST_ORDER) {
                            if ($columnNoList['customerCompany'] == $colAlphabet) { // ����¾���Ѥθܵ���ʥѡ����������)
                                $setFormatFlag = 'percent';
                            }
                        }
                    } else {
                        $targetCells= workSheetConst::QUANTITY_OR_PRICE_CELLS;
                        if ($targetCells[$nameListForCellAddress[$cellAddress]] === true) { // ���ٰʳ��ο�������ñ���ξ��
                            $setFormatFlag = false;
                        }
                    }
                }

                // ������ͤ��������
                $getValue = $sheet->getCell($cellAddress)->getCalculatedValue();
                if (isset($getValue)) {
                    if ($setFormatFlag === true) {
                        $cellValue = trim($sheet->getCell($cellAddress)->getFormattedValue());
                    } else { // �ե����ޥåȥ��åȥե饰��false�λ��Ϸ׻��ͤ�ľ��Ϳ��������ϲ�ǽ����ΰ����ˤĤ������ϸ�ˤ⥻��ν񼰤�ȿ�Ǥ����뤿�ᡢ�񼰤�Handsontable�����ꤹ�뤿����б���
                        $cellValue = $sheet->getCell($cellAddress)->getCalculatedValue();
                        if ($setFormatFlag === 'percent' && $cellValue) {
                            $cellValue = $cellValue * 100;
                        }
                    }                    
                } else {
                    $cellValue = '';
                }

                // ����Υե���Ⱦ�����������
                $fontInfo = $this->getFontInfo($cellAddress);
      
                // ������طʿ����������
                if ($hiddenRowList[$workSheetRow] !== true) {
                    $backgroundColor = $this->sheet->getStyle($cellAddress)->getFill()->getStartColor()->getRGB();
                } else {
                    $backgroundColor = 'CCCCCC';
                }

                // ����η���������������
                $border = $this->getBorderInfo($cellAddress);

                // ��������־�����������
                $verticalPosition = $this->getVerticalPosition($cellAddress);
                $horizontalPosition = $this->getHorizontalPosition($cellAddress);

                // ������������˼��������ѥ�᡼�����Ǽ����
                $cellData[$tableRow][$tableColumn] = array(
                    'value' => (array_search($cellValue, $this->excelErrorList)) ? '#ERROR!' : $cellValue,
                    'fontFamily' => $fontInfo['fontFamily'],
                    'fontSize' => $fontInfo['fontSize'],
                    'backgroundColor' => $backgroundColor,
                    'fontColor' => $fontInfo['fontColor'],
                    'border' => $border,
                    'verticalPosition' => $verticalPosition,
                    'horizontalPosition' => $horizontalPosition,
                    'emphasis' => $fontInfo['emphasis']
                );

                // Html�������������ʣ����ɽ������ʤ��褦�����������������
                if ($tableRow > $tableStartRow) {
                    $beforeRow = $tableRow -1;
                    $this->setAvailableBorderInfo($cellData[$tableRow][$tableColumn]['border']['top'], $cellData[$beforeRow][$tableColumn]['border']['bottom']);
                }
                if ($tableColumn > $tableStartColumn) {
                    $beforeColumn = $tableColumn -1;
                    $this->setAvailableBorderInfo($cellData[$tableRow][$tableColumn]['border']['left'], $cellData[$tableRow][$beforeColumn]['border']['right']);
                }

                // ����ɽ���������Υǡ������������
                if ($hiddenRowList[$workSheetRow] !== true) {
                    $hiddenCellValue[$tableRow][$tableColumn] = $cellData[$tableRow][$tableColumn]['value'];
                } else {
                    $hiddenCellValue[$tableRow][$tableColumn] = null;
                }

                // ���饹̾�ν����
                $className = null;

                // ���饹̾�Υ��å�
                if ($areaCode) {
                    $className = workSheetConst::DETAIL_CLASS_STRING. ' '. workSheetConst::AREA_CLASS_STRING. $areaCode;
                    // �����Ϲ��ܤΥإå����Ф������ֹ�ꥹ�Ȥμ���
                    $columnNoList = $this->getColumnNumberList($areaCode);

                    foreach($columnNoList as $key => $columnNo) {
                        if ($columnNo === $colAlphabet) {
                            $className .= ' ';
                            $className .= $key;
                            break;
                        }
                    }
                } else if ($nameListForCellAddress[$cellAddress]) {
                    // ����˥���̾�Τ�¸�ߤ�����ϥ���̾�Τ򥯥饹�˥��åȤ���
                    $className = $nameListForCellAddress[$cellAddress];
                }

                if ($className) {
                    // ���饹����˥��饹̾���ɲä���
                    $cellClass[] = $this->setCellClass($tableRow, $tableColumn, $className);
                }

                // readOnly���������
                if ($mode === 'edit') {                    
                    if ($areaCode) {
                        // ���ֹ��������饢��ե��٥åȤ��Ѵ�
                        // $colAlphabet = $this->getAlphabetForColumnIndex($workSheetColumn);
                        // foreach($columnNoList as $key => $columnNo) {
                        //     if ($columnNo === $colAlphabet) {
                        //         break;
                        //     }
                        // }

                        // �оݥ��ꥢ�˱������Խ���ǽ�ʥ���Υꥹ�Ȥ���� 
                        $editableKeys = workSheetConst::getEditableKeys($areaCode);

                    } else if ($nameListForCellAddress[$cellAddress]) {
                        $key = $nameListForCellAddress[$cellAddress];
                        $editableKeys = workSheetConst::EDITABLE_KEY_EXPECT_FOR_TARGET_AREA;
                    }
                    // readOnly�򥻥åȤ���
                    if ($editableKeys[$key] === true) {
                        $cellData[$tableRow][$tableColumn]['readOnly'] = false;
                    } else {
                        $cellData[$tableRow][$tableColumn]['readOnly'] = true;
                    }
                }
            }
        }

        // ��祻�뤬�����硢��縵�Υ���˷����������Ϳ����
        //��Handsontable�б�����祻��κǤ⺸��η������������������褹��١�
        $resultSetBorder = $this->setBorderInfoForMergedCell($mergedCellsList, $cellData);
        
        // ���������CSS��border-style���б�������
        for ($tableRow = $tableStartRow; $tableRow <= $tableEndRow; $tableRow++) {
            $workSheetRow = $tableRow + $startRow;
            for ($tableColumn = $tableStartColumn; $tableColumn <= $tableEndColumn; ++$tableColumn) {
                foreach($cellData[$tableRow][$tableColumn]['border'] as $position => $borderData) {
                    $cellData[$tableRow][$tableColumn]['border'][$position] += $this->setBorderForCss($borderData['excelStyle']);
                }
            }
        }

        // Row�Υ��ե��̤���ɽ���Ԥ�ȿ�Ǥ�����
        if ($hiddenRowList) {
            foreach ($hiddenRowList as $key => $val) {
                $viewKey = $key - $rowShiftValue;
                $viewHiddenRowList[$viewKey] = $val;
            }
        }

        // preview�ξ��
        if ($mode === 'preview') {

            // ȯ����֥ޥ����μ���
            $receiveStatusMaster = $this->objDB->getMasterToArray('m_receivestatus', 'lngreceivestatuscode', 'strreceivestatusname');
            // ������֥ޥ����μ���
            $orderStatusMaster = $this->objDB->getMasterToArray('m_orderstatus', 'lngorderstatuscode', 'strorderstatusname');

            // �ɲ���Υǥե�����ͤ򥻥åȤ���
            $defaultCellData = workSheetConst::WORK_SHEET_CELL_DEFAULT;
            mb_convert_variables('UTF-8', 'EUC-JP', $defaultCellData); // UTF-8���Ѵ�

            $targetAreaRows = $this->targetAreaRows;

            foreach ($targetAreaRows as $areaCode => $workSheetRows) {
                if ($areaCode === DEF_AREA_OTHER_COST_ORDER) {
                    continue;
                }
                // �����ȥ�Ԥμ���(�оݥ��ꥢ���ϹԤ�1�����ι�)
                $titleRow = $workSheetRows['firstRow'] - 1;
                // �����ȥ�Ԥ��ɤ��оݥ��ꥢ��°���뤫��Ƚ�ꤹ�����������
                $titleRowAttribute[$titleRow] = $areaCode;
            }
            

            // �����������
            $shiftResult = $this->shiftMergedCellsList($mergedCellsList, 0, -3); // �����������
            array_unshift($columnWidth, 30, 25, 40);
            $tableEndColumn += 3;

            // ���饹����ι���
            foreach ($cellClass as &$classData) {
                $classData['col'] += 3;
            }

            foreach ($cellData as $tableRow => $rowData) {
                $workSheetRow = $tableRow + $rowShiftValue; // ��������Ȥι��ֹ�

                $ColumnData1 = $defaultCellData;
                $ColumnData2 = $defaultCellData;
                $ColumnData3 = $defaultCellData;

                $areaCode = $this->checkAttributeRow($workSheetRow);

                $receiveConfirm = mb_convert_encoding('����', 'UTF-8', 'EUC-JP');
                $orderConfirm = mb_convert_encoding('��', 'UTF-8', 'EUC-JP');
                $orderCancel = mb_convert_encoding('��', 'UTF-8', 'EUC-JP');

                // �ܥ�����������
                if ($receiveAreaCodeList[$areaCode]) {
                    // ����ξ��
                    $data = $inputData[$workSheetRow]['data'];
                    $statusCode = $data['statusCode'];
                    $receiveNo = $data['receiveNo'];
                    switch($statusCode) {
                        case DEF_RECEIVE_APPLICATE:
                            $name = "confirm". $areaCode;
                            $htmlValue1 = "<div class=\"applicate\">";
                            $htmlValue1 .= "<input type=\"checkbox\" class=\"checkbox_applicate\" name=\"". $name. "\" value=\"".$receiveNo . "\">";
                            $htmlValue1 .= "</div>";
                            $mergedCellsList[] = array(
                                'row' => $tableRow,
                                'col' => 0,
                                'rowspan' => 1,
                                'colspan' => 2,
                            );
                            
                            $value3 = $receiveStatusMaster[$statusCode]['strreceivestatusname'];
                            $htmlValue3 = "<div class=\"status_applicate\">". $value3. "</div>";

                            $ColumnData1['value'] = $htmlValue1;
                            $ColumnData3['value'] = $htmlValue3;
                            break;
                        case DEF_RECEIVE_ORDER:
                            $value = $receiveStatusMaster[$statusCode]['strreceivestatusname'];
                            $htmlValue = "<div class=\"status_order\">". $value. "</div>";
                            $ColumnData3['value'] = $htmlValue;
                            break;
                        case DEF_RECEIVE_END:
                            $ColumnData1['value'] = $receiveStatusMaster[$statusCode]['strreceivestatusname'];
                            $mergedCellsList[] = array(
                                'row' => $tableRow,
                                'col' => 0,
                                'rowspan' => 1,
                                'colspan' => 3,
                            );
                            break;
                        default:
                            break;
                    }                
                } else if ($orderAreaCodeList[$areaCode]) {
                    // ȯ��ξ��
                    $data = $inputData[$workSheetRow]['data'];
                    $statusCode = $data['statusCode'];
                    $orderNo = $data['orderNo'];
                    switch($statusCode) {
                        case DEF_ORDER_APPLICATE:
                            $name = "confirm". $areaCode;
                            $htmlValue1 = "<div class=\"applicate\">";
                            $htmlValue1 .= "<input type=\"checkbox\" class=\"checkbox_applicate\" name=\"". $name. "\" value=\"".$orderNo . "\">";
                            $htmlValue1 .= "</div>";
                            $value3 = $orderStatusMaster[$statusCode]['strorderstatusname'];
                            $htmlValue3 = "<div class=\"status_applicate\">". $value3. "</div>";
                            $ColumnData1['value'] = $htmlValue1;
                            $ColumnData3['value'] = $htmlValue3;
                            break;
                        case DEF_ORDER_ORDER:
                            $name = "cancel". $areaCode;
                            $htmlValue2 = "<div class=\"order\">";
                            $htmlValue2 .= "<input type=\"checkbox\" class=\"checkbox_cancel\" name=\"". $name. "\" value=\"".$orderNo . "\">";
                            $htmlValue2 .= "</div>";
                            $value3 = $orderStatusMaster[$statusCode]['strorderstatusname'];
                            $htmlValue3 = "<div class=\"status_order\">". $value3. "</div>";
                            $ColumnData2['value'] = $htmlValue2;
                            $ColumnData3['value'] = $htmlValue3;
                            break;
                        case DEF_ORDER_END:
                            break;
                        default:
                            break;
                    }
    
                } else if ($titleRowAttribute[$workSheetRow]) {
                    // ���ꡢ��åܥ��������
                    $areaCode = $titleRowAttribute[$workSheetRow];
                    if ($receiveAreaCodeList[$areaCode]) {
                        // �����ꥢ�ξ��
                        $confirmValue = "confirm". $areaCode;
                        $htmlValue1 = "<div>";
                        $htmlValue1 .= "<button type=\"button\" class=\"btn_confirm_receive\" value=\"". $confirmValue. "\">". $receiveConfirm. "</button>";
                        $htmlValue1 .= "</div>";
                        $mergedCellsList[] = array(
                            'row' => $tableRow,
                            'col' => 0,
                            'rowspan' => 1,
                            'colspan' => 2,
                        );
                        $ColumnData1['value'] = $htmlValue1;
                    } else if ($orderAreaCodeList[$areaCode]) {
                        $confirmValue = "confirm". $areaCode;
                        $cancelValue = "cancel". $areaCode;
                        // ȯ���ꥢ�ξ��
                        $htmlValue1 = "<div>";
                        $htmlValue1 .= "<button type=\"button\" class=\"btn_confirm_order\" value=\"". $confirmValue. "\">". $orderConfirm. "</button>";
                        $htmlValue1 .= "</div>";
                        $htmlValue2 = "<div>";
                        $htmlValue2 .= "<button type=\"button\" class=\"btn_cancel_order\" value=\"". $cancelValue. "\">". $orderCancel. "</button>";
                        $htmlValue2 .= "</div>";
                        $ColumnData1['value'] = $htmlValue1;
                        $ColumnData2['value'] = $htmlValue2;
                    }
                }
                array_unshift($cellData[$tableRow], $ColumnData1, $ColumnData2, $ColumnData3);
            }
        }

        // �ǡ���������˥��åȤ���
        $viewData = array(
            'sheetName' => $sheetName,              // ������̾
            'mergedCellsList' => $mergedCellsList,  // �ޡ������줿����Υꥹ��
            'startRow' => $tableStartRow,           // ���Ϲ�
            'endRow' => $tableEndRow,               // ��λ��
            'startColumn' => $tableStartColumn,     // ������
            'endColumn' => $tableEndColumn,         // ��λ��
            'rowHeight' => $rowHeight,              // �Թ⤵
            'columnWidth' => $columnWidth,          // ����
            'cellData' => $cellData,                // ����Υǡ���(�͡�ʸ���ե���ȡ��������طʿ�)
            'cellClass' => $cellClass,
            'hiddenList' => $viewHiddenRowList,
            'hiddenRowHeight' => $hiddenRowHeight,  // ����ɽ���������ѹԹ⤵
            'hiddenCellValue' => $hiddenCellValue     // ����ɽ���������ѥ���������
        );

        // readOnly�����ٹԾ���¸�ߤ�����ϥ��åȤ���
        if ($readOnlyDetailRow) {
            $viewData['readOnlyDetailRow'] = $readOnlyDetailRow;
        }

        // ���Ѹ��������ֹ�ꥹ�Ȥ�¸�ߤ�����ϥ��åȤ���
        if ($detailNoList) {
            $viewData['detailNoList'] = $detailNoList;
        }
        
        return $viewData;
    }

    // ̵���Ԥ�������
    public function deleteInvalidRow($viewData) {
        $hiddenList = $viewData['hiddenList'];
        $cellData = $viewData['cellData'];
        $rowHeight = $viewData['rowHeight'];
        $startRow = $viewData['startRow'];
        $endRow = $viewData['endRow'];
        $mergedCellsList = $viewData['mergedCellsList'];
        $deleteCount = 0;

        $newMergeCellsList = array();

        // �ޡ�������ξ���򳫻ϹԤ򥭡��˻�������˳�Ǽ����
        foreach ($mergedCellsList as $mergeInfo) {
            $key = $mergeInfo['row'];
            $newMergeCellsList[$key][] = $mergeInfo;
        }

        // �ޡ�������Τʤ��Ԥ�����������
        for ($i = $startRow; $i <= $endRow; ++$i) {
            if (!$newMergeCellsList[$i]) {
                $newMergeCellsList[$i] = array();
            }            
        }

        // �����ʹ��ֹ�ˤξ��祽����
        ksort($newMergeCellsList);

        // ̵���Ԥ�������
        foreach ($hiddenList as $row => $bool) {
            if ($bool === true) {
                unset($cellData[$row]);
                unset($rowHeight[$row]);
                unset($newMergeCellsList[$row]);
                ++$deleteCount;
            }
        }

        // ��������˵ͤ��ʥ��������ֹ����������Ϣ�֤ˤ���)
        $cellData = array_merge($cellData);
        $rowHeight = array_merge($rowHeight);
        $newMergeCellsList = array_merge($newMergeCellsList);

        unset($mergedCellsList);

        // �ޡ������������������������
        foreach ($newMergeCellsList as $newRow => $mergeInfoList) {
            if ($mergeInfoList) {
                foreach ($mergeInfoList as $mergeInfo) {
                    // ������ιԤ������ʥޡ����������γ��ϹԤϽ������ΤޤޤʤΤ��ִ�������Ԥ���
                    $mergeInfo['row'] = $newRow;

                    $mergedCellsList[] = $mergeInfo;
                }
            }
        }

        $endRow -= $deleteCount; // �Ԥ���������������λ�Ԥ򷫤�夲��
        
        $viewData['cellData'] = $cellData;
        $viewData['rowHeight'] = $rowHeight;
        $viewData['endRow'] = $endRow;
        $viewData['mergedCellsList'] = $mergedCellsList;               

        return $viewData;
    }

    /**
     * Excel�η�����ͥ���٤����ꤹ��
     * ��ͥ���٤ι⤤��������
     *
     * @return array $linePriority
     */
    function makeLinePriority() {
        $linePriority = array(
            'double',
            'thick',
            'medium',
            'mediumDashed',
            'mediumDashDot',
            'slantDashDot',
            'mediumDashDotDot',
            'thin',
            'dashed',
            'dotted',
            'dashDot',
            'dashDotDot',
            'hair',
            'none'
        );
        return $linePriority;
    }

    /**
     * ����̾�Τ��б����륻����֤��������
     *
     * @param array $spreadSheet �֥å���phpSpreadSheet���֥�������
     * @param array $sheet �����Ⱦ���(�����Ȥ�phpSpreadSheet���֥������Ȥ�����Ǥˤ������)
     *
     * @return array $data �����Ⱦ����̵���ʥ����Ȥ�����
     */
    function getCellAddressForCellName($spreadSheet, $sheet, $nameList, $rowCheckNameList) {
        $cellAddressList = array();
        $cellAddressListRowAndColumn = array();
        if (!is_array($sheet)) {
            // �����������ȥ��֥������Ȥξ�������˥��åȤ���
            $sheetList[0] = $sheet;
        } else {
            $sheetList = $sheet;
        }
        foreach($sheetList as $key => $value) {
            $showDataDeleteFlug = null;
            $draftCellAddressList = array();
            $draftCellAddressListRowAndColumn = array();
            foreach($nameList as $val) {
                $namedRange = null;
                // ��������˥���̾�Τ����ꤵ��Ƥ��뤫��������
                $namedRange = $spreadSheet->getNamedRange($val, $value);
                if ($namedRange && $namedRange->getWorkSheet()->getTitle() === $value->getTitle()) {
                    // �������������Υ�����̾�ȸ������˻��ꤷ��������̾�����פ������Τߥ���̾�Τ��Ф��륻����֤��������������
                    // �оݥ����Ȥ�����Ǥ��ʤ��ä���硢�֥å������ꤵ�줿�ǡ�����������ʤ��褦���뤿����к�
                    $draftCellAddress = $namedRange->getRange();
                    $draftCellAddressList[$val] = $draftCellAddress;
                    $draftCellAddressListRowAndColumn[$val] = $this->separateRowAndColumn($draftCellAddress); //�Ԥ����ʬ�䤷��������֥ꥹ��
                } else {
                    // ��������Ǹ��Ĥ���ʤ�����̾�Τ����ä����Ͻ��������Ǥ����ǡ�������ե饰��Ω�Ƥ�
                    $showDataDeleteFlug = 1;
                    break;
                }
            }
            if ($draftCellAddressListRowAndColumn) {
                $startRow = intval($draftCellAddressListRowAndColumn['top_left']['row']);
                $endRow = intval($draftCellAddressListRowAndColumn['bottom_left']['row']);
                $startColumn = $this->getIndexForColumnAlphabet($draftCellAddressListRowAndColumn['top_left']['column']);
                $endColumn = $this->getIndexForColumnAlphabet($draftCellAddressListRowAndColumn['top_right']['column']);
                $rowValue = $endRow - $startRow + 1;
                if ($endRow - $startRow === 16) {
                // �����������Ǥʤ����Ϻ���ե饰��Ω�Ƥ�
                    $showDataDeleteFlug = 1;
                } else {
                    // �����ϥե�����Υإå�����Ʊ���Ԥ�̵���Ȥ��ϡ��ǡ�������ե饰��Ω�Ƥ�
                    foreach($rowCheckNameList as $array) {
                        if (!$this->rowCheck($draftCellAddressListRowAndColumn, $array)) {
                            $showDataDeleteFlug = 1;
                            break;
                        }
                    }
                }
            }
            if ($showDataDeleteFlug !== 1) {
                // �ǡ�������ե饰��1�Ǥʤ����������ȵڤӥ���̾�Τ��б���������ꥹ�Ȥ�����˥��åȤ���
                $data[$key] = array(
                    'sheet' => $sheetList[$key],
                    'cellAddress' => $draftCellAddressList,
                    'area' => array(
                        'startRow' => $startRow,
                        'endRow' => $endRow,
                        'startColumn' => $startColumn,
                        'endColumn' => $endColumn,
                    )                
                );
            }
        }
        return $data;
    }

    /**
     * ����Υ���̾�Τ�Ʊ���Ԥ�¸�ߤ��뤫�����å�����
     *
     * @param array $cellAddressList ����̾�Τ�ɳ�դ�������֤Υꥹ��
     * @param array $array Ʊ���Ԥ�¸�ߤ���ɬ�פΤ��륻��̾�Υꥹ��
     *
     * @return boolean $result �����å����
     */
    protected static function rowCheck($cellAddressList, $array) {
        $param = null;
        $result = false;
        foreach($array as $value) {
            // 1���ܤΥ롼�פǤ����ֹ��$param�˥��åȤ���
            if (!$param) {
                $param = $cellAddressList[$value]['row'];
            } else {
                if ($param == $cellAddressList[$value]['row']) {
                    $result = true;
                } else {
                    // 1�ĤǤ�Ʊ���Ԥ˥��뤬¸�ߤ��ʤ��ä����ϥ����å�NG�Ȥ��ƽ��������Ǥ���
                    $result = false;
                    break;
                }
            }        
        }
        return $result;
    }

    /**
     * �ޡ������줿����Υꥹ�Ȥ��������
     *
     * @return array $mergedCellsRowAndColumnInfo �ޡ������줿����ꥹ��
     */
    protected function getMergedCellsList() {
        $sheet = $this->sheet;
        // ��������Ȥγ��Ϲ��󡢽�λ�����ͭ���ϰϡ�
        $areaStartRow = $this->startRow;
        $areaEndRow = $this->endRow;
        $areaStartColumn = $this->startColumn;
        $areaEndColumn = $this->endColumn;

        $mergedCellsList =  $sheet->getMergeCells();
        $address = array();
        foreach($mergedCellsList as $value) {
            $address = $this->getRowAndColumnStartToEnd($value);
            // �ޡ�������γ��Ϲ���Ƚ�λ����
            $startRow = intval($address['start']['row']);
            $endRow = intval($address['end']['row']);
            $startColumn = $this->getIndexForColumnAlphabet($address['start']['column']);
            $endColumn = $this->getIndexForColumnAlphabet($address['end']['column']);
            // �ޡ������줿���뤬������ϰϳ��ˤ���Ȥ��ϥꥹ�Ȥ��ɲä��ʤ�
            if ($startRow < $areaStartRow
                || $endRow > $areaEndRow
                || $startColumn < $areaStartColumn
                || $endColumn > $areaEndColumn) {
                continue;
            }

            $mergedCellsRowAndColumnInfo[] = array(
                'row' => $startRow,
                'col' => $startColumn,
                'rowspan' => $endRow - $startRow + 1,
                'colspan' => $endColumn - $startColumn + 1,
            );
        }
        return $mergedCellsRowAndColumnInfo;
    }

    // ���������������
    function getCellInfo($sheet, $address) {
        $cellInfo = $sheet->getCell($address);
        return $cellInfo;
    }

    // ������󤫤饻����ͤ��������
    function getCellValue($cellInfo) {
        $value = $cellInfo->getFormattedValue();
        return $value;
    }

    /**
     * �����ȤιԹ⤵���������
     *
     * @return array $rowHeightPixel �ƹԤι⤵
     */
    protected function getRowHeight() {
        $sheet = $this->sheet;
        $startRow = $this->startRow;
        $endRow = $this->endRow;
        if (is_integer($startRow) && is_integer($endRow)) {
            if ($startRow <= $endRow) {
                for ($i = $startRow; $i <= $endRow; $i++) {
                    $rowHeight = $sheet->getRowDimension($i)->getRowHeight();
                    // �ԥ�������Ѵ�
                    $rowHeightPixel[] = XlsxDrawing::pointsToPixels($rowHeight);
                }
                return $rowHeightPixel;
            }
        }
        return false;
    }


    /**
     * �����Ȥ��������������
     *
     * @return array $columnWidthPixel �������
     */
    protected function getColumnWidth() {
        $sheet = $this->sheet;
        $startColumn = $this->startColumn;
        $endColumn = $this->endColumn;
        // ɸ��Υե���Ȥ����
        $defaultFont = $sheet->getParent()->getDefaultStyle()->getFont();
        if ($startColumn <= $endColumn) {
            for ($i = $startColumn; $i <= $endColumn; $i++) {
                $column = self::getAlphabetForColumnIndex($i);
                $columnWidth = $sheet->getColumnDimension($column)->getWidth();
                // �ԥ�������Ѵ�
                $columnWidthPixel[$i] = XlsxDrawing::cellDimensionToPixels($columnWidth, $defaultFont);
            }
            return $columnWidthPixel;
        }
        return false;
    }

    /**
     * ���ꤷ�������ϰϤκǽ�ڤӺǸ�ιԤ�����������
     *
     * @param string $printArea "���ϥ������:��λ�������"������string
     *
     * @return array $cellAddress ���ϥ���ڤӽ�λ����ι��ֹ�����ֹ�
     */
    protected function getRowAndColumnStartToEnd($area) {
        $cell = array();
        $cell['start'] = strstr($area, ':', true);
        $cell['end'] = str_replace(':', '', strstr($area, ':'));
        $cellAddress = self::separateRowAndColumn($cell);
        return $cellAddress;
    }

    /**
     * ������֤�����ֹ�����ֹ����Ф���
     *
     * @param $cell ���ֹ�����ֹ����Ф��륻�����
     *
     * @return array $cellAddress ������֤��б�������ֹ�����ֹ�
     */
    public static function separateRowAndColumn($cell) {
        $cellAddress = array();
        if (is_array($cell)) {
            foreach ($cell as $key => $value) {
                $cellAddress[$key]['row'] = preg_replace('/[^0-9]/', '', $value);    // ���ֹ�μ���
                $cellAddress[$key]['column'] = preg_replace('/[^A-Z]/', '', $value); // ���ֹ�μ���
            }
        } else {
            $cellAddress['row'] = preg_replace('/[^0-9]/', '', $cell);      // ���ֹ�μ���
            $cellAddress['column'] = preg_replace('/[^A-Z]/', '', $cell);   // ���ֹ�μ���
        }
        return $cellAddress;
    }
    
    
    /**
     * ���ֹ�����ֹ���礹��
     *
     * @param $row ���ֹ�
     * @param $column ���ֹ�
     *
     * @return array $cell �������
     */
    protected static function combineRowAndColumnIndex($row, $column) {
        if (!preg_match('/[A-Z]+/', $column)) {
            $column = self::getAlphabetForColumnIndex($column);
        }
        if (is_integer($row) && preg_match('/[A-Z]+/', $column)) {
            $cell = $column . $row;
            return $cell;
        } else {
            return false;
        }
    }

    /**
     * ���ꤷ���Կ���������ư����������֤��ֵѤ���
     *
     * @param $cell ��ư���Υ������
     * @param $rowMove ��ư����Կ��ʲ��������ܡ�
     * @param $colMove ��ư��������ʱ��������ܡ�
     *
     * @return $movedCell ��ư��Υ������
     */
    public static function getMoveCell($cell, $rowMove, $colMove) {
        $separate = self::separateRowAndColumn($cell);
        $row = $separate['row'];
        $col = $separate['column'];
        $colNumber = self::getIndexForColumnAlphabet($col);
        $movedRow = $row + $rowMove;
        $movedCol = $colNumber + $colMove;
        $movedCell = self::combineRowAndColumnIndex($movedRow, $movedCol);
        return $movedCell;
    }

    /**
     * �������������������
     *
     * @param $sheet �оݤΥ��������
     *
     * @return array $styles ����Υ����������
     */
    function getStyleInfo($sheet, $address) {
        $styleInfoData = $sheet->getStyle($address);
        return $styleInfoData;
    }


    /**
     * ����������������
     *
     * @param $cellAddress ���ֹ�����ֹ����Ф��륻�����
     *
     * @return array $borders ����η�������
     */
    protected function getBorderInfo($cellAddress) {
        $borderInfoData = $this->sheet->getStyle($cellAddress)->getBorders();
        // ����������椫�������̤ξ�����������
        $border = array();
        $border['left'] = $borderInfoData->getLeft();
        $border['right'] = $borderInfoData->getRight();
        $border['top'] = $borderInfoData->getTop();
        $border['bottom'] = $borderInfoData->getBottom();

        // �������󤫤����ξ������������
        $borders = array();
        foreach ($border as $key => $value) {
            $borders[$key]['color'] = $value->getColor()->getRGB(); // �����ο�
            $borders[$key]['excelStyle'] = $value->getBorderStyle();
        }
        return $borders;
    }


    // �ƥ����Ȥο�ʿ���������֤��������
    protected function getHorizontalPosition ($cellAddress) {
        $horizontalPosition = $this->sheet->getStyle($cellAddress)->getAlignment()->getHorizontal();
        return $horizontalPosition;
    }

    // �ƥ����Ȥο�ľ���������֤��������
    protected function getVerticalPosition ($cellAddress) {
        $verticalPosition = $this->sheet->getStyle($cellAddress)->getAlignment()->getVertical();
        //center�ξ���css�Ѥ�middle���ִ�����
        if ($verticalPosition == 'center') {
            $verticalPosition = 'middle';
        }
        return $verticalPosition;
    }

    // ����Υե���Ⱦ�����������
    protected function getFontInfo($cellAddress) {
        $font = $this->sheet->getStyle($cellAddress)->getFont();
        $fontFamily = $font->getName();
        $fontSize = $font->getSize();
        $fontColor = $font->getColor()->getRGB();
        $emphasis = $this->getEmphasizedStyle($font);

        $fontInfo = array(
            'fontFamily' => $fontFamily,
            'fontSize' => $fontSize,
            'fontColor' => $fontColor,
            'emphasis' => $emphasis
        );
        
        return $fontInfo;
    }

    // ��������������ξ�����������
    protected function getEmphasizedStyle($font) {
        $bold = $font->getBold();
        $italic = $font->getItalic();
        $emphasis = array(
            'bold' => $bold,
            'italic' => $italic,
        );
        return $emphasis;
    }

    /**
     * ���Ͳ����줿���ֹ�򥢥�ե��٥åȤ��Ѵ�����
     * 
     * @param $columnIndex
     * 
     * @return $column
     */
    public static function getAlphabetForColumnIndex($columnIndex) {
        if (!is_integer($columnIndex)) {
            return false;
        }
        // �����ο��ͤ˥���ե��٥åȤ��б��������������������
        for ($i = 0; $i < 26; $i++) {
            $alphabet[] = chr(ord('A') + $i);
        }
        // ��ΰ̤ν���
        $number = fmod($columnIndex, 26);
        $column = $alphabet[$number];
        $carry = ($columnIndex - $number) / 26;
        // ����ʾ�ΰ̤�������ϰʲ�����
        while ($carry > 0) {
            $carry = $carry -1;
            $number = fmod($carry, 26);
            $column = $alphabet[$number] .$column;
            $carry = ($carry - $number) / 26;
        }
        return $column;
    }


    /**
     * Excel����̾����ͤ��Ѵ�����
     * 
     * @param $column
     * 
     * @return $columnIndex
     */
    public static function getIndexForColumnAlphabet($column) {
        $error = !preg_match('/[A-Z]+/', $column);
        if (!$error) {
            $columnIndex = 0;
            //ʸ�����ȿž����
            $column = strrev($column);
            //1ʸ�������ڤä���Τ�����˳�Ǽ
            $columnDigit = str_split($column);
            for($i = 0; $i < count($columnDigit); $i++) {
            //ord�ؿ���Ȥ�����ե��٥åȤ����(ASCII�͡ˤˤ���-64���뤳�Ȥ�A = 1 ����Ϥޤ��ֹ�ˤʤ�
            //����ե��٥åȤ������뤴�Ȥ�26���߾������䤹
            $columnIndex += (ord($columnDigit[$i]) -64) * pow(26, $i);
            }
            $columnIndex = $columnIndex -1; // A = 0����Ϥޤ�褦��1�����
            return $columnIndex;
        }
    }

    /**
     * ���饹����򥻥åȤ���
     * 
     * @param integer $row ���饹�򥻥åȤ����
     * @param integer $col ���饹�򥻥åȤ�����
     * @param string  $className ���饹̾
     * 
     * @return ���饹���������
     */
    protected function setCellClass($row, $col, $className) {
        $cellClass = array(
            'row' => $row,
            'col' => $col,
            'className' => $className
        );
        return $cellClass;
    }

    // ���������CSS�Ѥ��Ѵ�����
    protected function setBorderForCss($borderStyle) {
        switch($borderStyle) {
            case 'none':
                $style = 'none';
                $width = '0';
                break;
            case 'thin':
            case 'hair':
                $style = 'solid';
                $width = 'thin';
                break;
            case 'medium':
                $style = 'solid';
                $width = 'medium';
                break;
            case 'thick':
                $style = 'solid';
                $width = 'thick';
                break;
            case 'double':
                $style = 'double';
                $width = 'medium';
                break;
            case 'mediumDashed':
            case 'mediumDashDot':
            case 'slantDashDot':
            case 'mediumDashDotDot':
                $style = 'dashed';
                $width = 'medium';
                break;
            case 'dashed':
            case 'dashDot':
            case 'dashDotDot':
                $style = 'dashed';
                $width = 'thin';
                break;
            case 'dotted':
                $style = 'dotted';
                $width = 'thin';
                break;   
            default:
                $style = 'none';
                $width = '0';
                break;
        }
        $convertedStyle = array(
            'style' => $style,
            'width' => $width,
        );
        return $convertedStyle;
    }

    /**
     * ���ܤ��륻��֤�ͭ���ʷ���������������
     * (Excel�Ǥ��������̤���ɽ���η����������äƤ����礬���ꡢ���Τޤ����褹���html�Ǥ�2�Ť�ɽ������뤿��)
     * 
     * @param array   $high ���ֹ����Ϲ��ֹ椬�礭�����Υ���
     * @param array   $low  ���ֹ����Ϲ��ֹ椬���������Υ���
     * 
     */
    protected function setAvailableBorderInfo(&$high, &$low ) {
        // ������¦�η�����������Τ�ͭ���ʷ���������Ѵ���Ԥ�
        if ($high['excelStyle'] !== "none") {
            if ($high['excelStyle'] === $low['excelStyle']) {
                // �����ν񼰤�Ʊ���ǿ����ۤʤ���Ϲ��ˤ���(���ˤ��ͥ���̤�Ƚ�̤ϹԤ�ʤ�)
                if ($high['color'] !== $low['color']) {
                    $low['color'] = "000000";
                }
            }
            // �����μ��ब�ۤʤ����ͥ�����̤˻��ꤹ��
            else {
                // ͥ������������
                $linePriority = $this->makeLinePriority();
                $lowPriorityKey = array_keys($linePriority, $low['excelStyle']);
                $highPriorityKey = array_keys($linePriority, $high['excelStyle']);
                if ($lowPriorityKey > $highPriorityKey) {
                    // $high�η�����ͥ���̤��⤤����$low����������
                    $low = $high;
                }
            }
            // �礭��¦�η��������������
            $high['excelStyle'] = "none";
        }
    }

     /**
     * ��祻��ξ���򥷥եȤ�����
     * 
     * @param array   $mergedCellsList ��祻��Υꥹ��
     * @param string  $rowShift �ԤΥ��ե���
     * @param string  $columnShift  ��Υ��ե���
     * 
     */
    protected function shiftMergedCellsList(&$mergedCellsList, $rowShift, $columnShift) {
        foreach ($mergedCellsList as &$value) {
            $value['row'] = $value['row'] - $rowShift;
            $value['col'] = $value['col'] - $columnShift;
        }
        return true;
    }


    /**
    * ��祻�뤬�����硢��縵�Υ���˷����������Ϳ����
    *��Handsontable�б�����祻��κǤ⺸��η������������������褹��١�
    * 
    * @param array   $mergedCellsList �ޡ������줿����ξ���(row�����Ϲԡ�column��������rowspan�����Կ���colspan��������������)
    * @param array   $cellData  ����ξ����border�������ξ����ޤ��
    * 
    * @return boolean
    */
    protected function setBorderInfoForMergedCell($mergedCellsList, &$cellData) {
        if ($mergedCellsList) {
            foreach($mergedCellsList as $value) {
                $endMergedRow = $value['row'] + $value['rowspan'] -1;
                $endMergedColumn = $value['col'] + $value['colspan'] -1;
                // ��祻��ΰ��ֺ��η�������ˡ���祻��ΰ��ֱ��η�������������
                $cellData[$value['row']][$value['col']]['border']['right'] = $cellData[$value['row']][$endMergedColumn]['border']['right'];
                // ��祻��ΰ��־�η�������ˡ���祻��ΰ��ֲ��η�������������
                $cellData[$value['row']][$value['col']]['border']['bottom'] = $cellData[$endMergedRow][$value['col']]['border']['bottom'];
            } 
        }
        return true;
    }

    /**
    * ��������ǽ��Ϥ���륨�顼�ꥹ�Ȥ�����
    * 
    */
    public function setExcelErrorList() {
        $errorList = array(
            '#DIV/0!',
            '#N/A',
            '#NAME?',
            '#NULL!',
            '#NUM!',
            '#REF!',
            '#VALUE!',
            '######'
        );
        $this->excelErrorList = $errorList;
        return true;
    }

    /**
    * �оݥ��ꥢ�γ��ϹԤȽ�λ�Ԥ򥻥åȤ���
    * 
    */
    protected function setRowRangeOfTargetArea() {
        $targetAreaList = workSheetConst::TARGET_AREA_NAME;
        foreach($targetAreaList as $areaCode => $areaName) {
            if ($areaCode !== DEF_AREA_OTHER_COST_ORDER) {
                $rows[$areaCode] = $this->getRowRangeOfTargetArea($areaCode);
            }           
        }

        // �����񥨥ꥢ�γ��ϹԤȽ�λ�Ԥ򥻥åȤ���
        $firstRow = $rows[DEF_AREA_PARTS_COST_ORDER]['firstRow'];
        $lastRow = $rows[DEF_AREA_PARTS_COST_ORDER]['lastRow'];

        // �����񥨥ꥢ�λ������ܤ����ֹ���������
        $subjectCellName = workSheetConst::ORDER_ELEMENTS_COST_STOCK_SUBJECT_CODE;
        $cellAddress = $this->cellAddressList[$subjectCellName];
        if (!$cellAddress) {
            return false;
        }
        $ret = self::separateRowAndColumn($cellAddress);
        $column = $ret['column'];
        
        // ����¾���ѥ��ꥢ�γ��ϹԤȽ�λ�Ԥ򥻥åȤ����������ν�λ�Ԥ�ƥ��åȤ����
        for ($row = $firstRow; $row <= $lastRow; ++$row) {
            $cell = $column. $row;
            $backgroundColor = $this->sheet->getStyle($cell)->getFill()->getStartColor()->getRGB();
            if (isset($color)) {
                if ($backgroundColor !== $color) {
                    // �����Ѳ������ä��Ԥ򤽤�¾���ѥ��ꥢ�γ��ϹԤȤ���
                    $rows[DEF_AREA_OTHER_COST_ORDER] = array(
                        'firstRow' => $row,
                        'lastRow' => $lastRow
                    );
                    // �����񥨥ꥢ�ν�λ�Ԥ�ƥ��åȤ���
                    $rows[DEF_AREA_PARTS_COST_ORDER]['lastRow'] = $row - 1;
                    break;
                }
            } else {
                $color = $backgroundColor;
            }
        }

        $this->targetAreaRows = $rows;
        return true;
    }


    /**
    * �оݥ��ꥢ�ι��ϰϤ��������(���ϹԤȽ�λ��)
    * @param integer   $areaCode  ���ꥢ��ʬ�ֹ�
    * 
    */
    protected function getRowRangeOfTargetArea($areaCode) {
        // �оݥ��ꥢ�Υ���̾�μ���
        $cellNameList = workSheetConst::getCellNameOfTargetArea($areaCode);
        $cellAddressList = $this->cellAddressList;
        // �إå�������ӥեå����ʷ׻���̡ˤΥ���̾�Τ�1�ĥ��åȤ���
        $upperCellName = self::getFirstElement($cellNameList['headerList']);
        $belowCellName = self::getFirstElement($cellNameList['resultList']);

        // ����̾�Τ�����ֹ���������
        $firstRow = $this->getRowNumberFromCellName($upperCellName) + 1;
        $lastRow = $this->getRowNumberFromCellName($belowCellName) - 1;
        $rows = array(
            'firstRow' => $firstRow,
            'lastRow' => $lastRow
        );
        return $rows;
    }

    /**
    * ����κǽ�����Ǥ��������
    * @param array   $array  ���Ǥ��������������
    * 
    * @return array  ����κǽ������
    */
    public static function getFirstElement($array){
        return current($array);
    }

    /**
    * ����̾�Τ�����ֹ桢���ֹ���������
    * @param string   $cellName  ����̾��
    *
    * @return array  ���ֹ�����ֹ�ξ���
    */
    protected function getRowAndColumnFromCellName($cellName) {
        $cellAddressList = $this->cellAddressList;
        if (!$cellAddressList) {
            return false;
        }
        $cellAddress = $cellAddressList[$cellName];
        $rowAndColumn = self::separateRowAndColumn($cellAddress);
        return $rowAndColumn;
    }

    /**
    * ����̾�Τ�����ֹ���������
    * @param string   $cellName  ����̾��
    *
    * @return string  ���ֹ�
    */
    protected function getRowNumberFromCellName($cellName) {
        $rowAndColumn = $this->getRowAndColumnFromCellName($cellName);
        return $rowAndColumn['row'];
    }

    /**
    * ����̾�Τ������ֹ���������
    * @param string   $cellName  ����̾��
    *
    * @return string  ���ֹ�ʥ���ե��٥å�)
    */
    protected function getColumnNumberFromCellName($cellName) {
        $rowAndColumn = $this->getRowAndColumnFromCellName($cellName);
        return $rowAndColumn['column'];
    }

    /**
    * ����̾�Τ������ֹ���������
    * @param string   $cellName  ����̾��
    *
    * @return string  ���ֹ�ʥ���ե��٥å�)
    */
    public function setDBEstimateData($productData, $estimateData) {
        // $this->setMonetaryRate(); // �̲ߥ졼�ȤΥ��å�
        $this->inputHeaderData($productData); // ���ʾ���Υ��åȡʥإå�����
        $this->inputStandardRate(); // ɸ����Υ��å�
        $this->insertDeficiencyRow($estimateData); // ���Ѹ������åȻ�����­����Ԥ���������
        $this->inputEstimateDetailData($estimateData); // ���Ѹ������٤Υ��å�
        // $this->inputDropdownList();
    }

    // �إå������ͤ򥻥åȤ���
    public function inputHeaderData($productData) {
        $cellAddressList = $this->cellAddressList;
        foreach ($productData as $key => $param) {
            if (isset($param)) {
                $cell = $cellAddressList[$key];
                $this->sheet->getCell($cell)->setValue($param);
            }
        }
    }

    // ��������Ȥ�ɸ����򥻥åȤ���
    public function inputStandardRate() {
        // DB����ɸ��������
	    $standardRate = $this->objDB->getEstimateStandardRate();
        $cellAddressList = $this->cellAddressList;
        $cell = $cellAddressList[workSheetConst::STANDARD_RATE];
        $this->sheet->getCell($cell)->setValue($standardRate);
        return;
    }

    // ���������ͤ򥻥åȤ���
    public function inputEstimateDetailData($estimateData) {
        $targetAreaRows = $this->targetAreaRows;
        foreach ($estimateData as $areaCode => $data) {
            $this->inputEstimateDetailToTargetArea($areaCode, $data);
        }
        return true;
    }


    // �оݥ��ꥢ�˸��Ѹ������پ���򥻥åȤ���
    protected function inputEstimateDetailToTargetArea($areaCode, $datas) {
        $targetAreaRows = $this->targetAreaRows;
        $columnNumber = $this->getColumnNumberList($areaCode);

        $firstRow = $targetAreaRows[$areaCode]['firstRow'];
        $lastRow = $targetAreaRows[$areaCode]['lastRow'];

        $linage = (int)$lastRow - (int)$firstRow + 1;

        $inputRowCount = count($datas);


        if ($linage < $inputRowCount + 1) {

            $difference = $inputRowCount + 1 - $linage;

            $selectedRow = $lastRow - 1;

            $this->insertCopyRowBefore($selectedRow, $difference);
        }

        $row = $firstRow;
        
        foreach($datas as $sorKey => $data) {
            // ͢������Ƚ��(����¾���Ѥξ��Τ߹Ԥ�)
            if ($areaCode == DEF_AREA_OTHER_COST_ORDER) {
                list($divisionSubjectCode, $divisionSubjectName) = explode(':', $data['divisionSubject']);
                if ($divisionSubjectCode == DEF_STOCK_SUBJECT_CODE_CHARGE) {
                    list($classItemCode, $classItemName) = explode(':', $data['classItem']);
                    if ($classItemCode == DEF_STOCK_ITEM_CODE_IMPORT_COST) {
                        $importCostList[$sorKey] = $data;
                        break;
                    } else if ($classItemCode == DEF_STOCK_ITEM_CODE_TARIFF) {
                        // ���Ǥι��ֹ����
                        $tariffRowList[] = $row;
                    }
                }
            }

            foreach($data as $name => $value) {
                $column = $columnNumber[$name];
                if (isset($row) && isset($column)) {
                    $cell = $column. $row;
                    $this->sheet->getCell($cell)->setValue($value);
                    // �ѡ���������Ϥξ�硢�񼰤��ѹ�����
                    if ($name == 'customerCompany' && is_numeric($value)) {
                        $style = $this->sheet->getStyle($cell);
                        $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    }
                    // �ǡ����񤭹��߾���κ���
                    $inputData[$row] = array(
                        'areaCode' => $areaCode,
                        'sortKey' => $sorKey,
                        'data' => $data
                    );
                }
            }
            ++$row;
        }

        // ���Ǥ�¸�ߤ�����
        if ($tariffRowList) {
            foreach($tariffRowList as $tariffRow) {
                // �����˷׻����Ƥ��ʤ��ȥ���η׻���̤��ä����к�(PhpSpreadSheet���Զ��Ȼפ���)
                $column = $columnNumber['subtotal'];
                $cell = $column. $row;
                $this->sheet->getCell($cell)->getCalculatedValue();
            }
        }

        // ͢�����Ѥ�¸�ߤ�����
        if ($importCostList) {
            $importCount = count($importCostList);
            // �����ϰϤκǽ��Ԥ����
            $lastInputAreaRow = $targetAreaRows[$areaCode]['lastRow'];
            // ͢�����Ѥ����Ϥ��볫�ϹԤ����
            $firstImportCostRow = $lastInputAreaRow - $importCount + 1;

            $row = $firstImportCostRow;

            // ͢�����Ѥ��ͤ�����
            foreach($importCostList as $sorKey => $data) {
                foreach($data as $name => $value) {
                    $column = $columnNumber[$name];
                    if (isset($row) && isset($column)) {
                        $cell = $column. $row;
                        $this->sheet->getCell($cell)->setValue($value);
                        if ($name == 'customerCompany' && is_numeric($value)) {
                            $style = $this->sheet->getStyle($cell);
                            $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                            $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        }
                        // �ǡ����񤭹��߾���κ���
                        $inputData[$row] = array(
                            'areaCode' => $areaCode,
                            'sortKey' => $sorKey,
                            'data' => $data
                        );
                    }
                }
                ++$row;
            }
            
            // ���Ǹ����γ��ϥ���Ƚ�λ��������
            $tariffSearchColumn = $columnNumber['classItem'];          // ������
            $tariffTotalColumn = $columnNumber['subtotal'];            // �����
            $tariffStartRow = $targetAreaRows[$areaCode]['firstRow'];  // ���Ϲ�
            $tariffEndRow = $firstImportCostRow - 1;                   // ��λ��

            // ����������ϰϤ����ꤹ��ʸ��������
            $tariffSearchStartCell = $tariffSearchColumn. $tariffStartRow;  // �������ϥ���
            $tariffSearchEndCell = $tariffSearchColumn. $tariffEndRow;      // ������λ����
            $tariffTotalStartCell = $tariffTotalColumn. $tariffStartRow;    // ��׳��ϥ���
            $tariffTotalEndCell = $tariffTotalColumn. $tariffEndRow;        // ��׽�λ����

            $tariffSearchArea = $tariffSearchStartCell. ':'. $tariffSearchEndCell;  // �����ϰ�
            $tariffTotalArea = $tariffTotalStartCell. ':'. $tariffTotalEndCell;     // ����ϰ�

            // ���Ǥι���ͤ����Ϥ��륻������
            $cellAddressList = $this->cellAddressList;
            $tariffTotalCell = $cellAddressList[workSheetConst::CALCULATION_TARIFF];  // ���ǹ�ץ���
            
            // ���Ϥ��줿�������
            $inputParam = $this->sheet->getCell($tariffTotalCell)->getValue();
            // sumif()�Υ��å�������
            preg_match('/(?<=sumif\()[^\)]+(?=\))/i', $inputParam, $match);
            // ,(����ޡˤǶ��ڤ�줿�����������
            list($searchArea, $searchCondition, $totalArea) = explode(',', $match[0]);

            // sumif�ξ��������
            $sumifParam = $tariffSearchArea. ','. $searchCondition. ','. $tariffTotalArea;

            // ���μ����ִ�����
            $newFomula = str_replace($match, $sumifParam, $inputParam);
            
            // ���������
            $this->sheet->getCell($tariffTotalCell)->setValue($newFomula);
       }


        // ��������ȤؤΥǡ����񤭹��߾�����ݻ�
        if ($this->inputData) {
            $this->inputData += $inputData;
        } else {
            $this->inputData = $inputData;
        }
        
        return true;
    }


    // �ɥ�åץ�����ꥹ�Ȥ��������ȥ��֥���������Υ�������Ϥ���
    protected function inputDropdownList() {
        // �ɥ�åץ�����ꥹ�Ȥμ���
        $this->setDropdownList();

        // �ɥ�åץ�����ꥹ�Ȥ��ڤ�ʬ����Ԥ�
        $dropdownDSCI = $this->dropdownDSCI;
        $dropdownCompany = $this->dropdownCompany;
        $dropdownGU = $this->dropdownGU;

        $cellAdrresList = $this->cellAddressList;

        $divSubDropdownCellList = workSheetConst::DIVISION_SUBJECT_DROPDOWN_CELL_NAME;
        $clsItmDropdownCellList = workSheetConst::CLASS_ITEM_DROPDOWN_CELL_NAME;
        
        foreach ($dropdownDSCI as $dropdownList) {
            $areaCode = $dropdownList->areacode;
        
            // �ɥ�åץ�����ꥹ�Ȥ򥨥ꥢ��ʬ�����ʬ�ऴ�Ȥ�����
            $newList[$areaCode][$dropdownList->divisionsubject][] = $dropdownList->classitem;
        }

        $areaNameList = workSheetConst::TARGET_AREA_NAME;

        foreach($areaNameList as $areaCode => $areaName) {
            $divSubCellName = $divSubDropdownCellList[$areaCode];
            $divSubCellAddress = $cellAdrresList[$divSubCellName];
            $divSubSeparate = $this->separateRowAndColumn($divSubCellAddress);
            $divSubHeaderRow = $divSubSeparate['row'];
            $divSubCol = $divSubSeparate['col'];

            $clsItmCellName = $clsItmDropdownCellList[$areaCode];
            $clsItmCellAddress = $cellAdrresList[$clsItmCellName];
            $clsItmseparate = $this->separateRowAndColumn($clsItmCellAddress);
            $clsItmHeaderRow = $divSubSeparate['row'];
            $clsItmCol = $divSubSeparate['col'];

            $divSubRow = $divSubHeaderRow;
            $clsItmRow = $clsItmHeaderRow;

            $beforeDivSub = '';

            foreach ($newList[$areaCode] as $divSub => $clsItm) {
                if ($beforeDivSub === '') { // �ǽ����������
                    $beforeDivSub = $divSub;

                    ++$divSubRow;
                    $inputDivSubCell = $divSubCol.$divSubRow;
                    $this->sheet->getCell($inputDivSubCell)->setValue($divSub);

                    ++$clsItmRow;
                    $inputClsItmCell = $clsItmCol.$clsItmRow;
                    $this->sheet->getCell($inputClsItmCell)->setValue($clsItm);

                } else if ($beforeDivSub === $divSub) { // 2���ܰʹߤǰ��������ʬ��(���ϻ�������)�Ȱ��פ�����
                    ++$clsItmRow;
                    $inputClsItmCell = $clsItmCol.$clsItmRow;
                    $this->sheet->getCell($inputClsItmCell)->setValue($clsItm);

                } else { // 2���ܰʹߤǰ��������ʬ��(���ϻ�������)�Ȱ��פ��ʤ����
                    $beforeDivSub = $divSub;
                    ++$divSubRow;
                    $inputDivSubCell = $divSubCol.$divSubRow;
                    $this->sheet->getCell($inputDivSubCell)->setValue($divSub);

                    ++$clsItmRow;
                    ++$clsItmRow; // 1�ԥ��ڡ����������
                    $inputClsItmCell = $clsItmCol.$clsItmRow;
                    $this->sheet->getCell($inputClsItmCell)->setValue($clsItm);
                }
                
            }
        }






        // foreach ($newList as $divSub => $clsItmList) {
        //     ++$divSubRow;
        //     $inputDivSubCell = $divSubCol.$divSubRow;
        //     $this->sheet->getCell($cell)->setValue($divSub);
        //     foreach ($clsItmList as $classItem) {
        //         ++$clsItmRow;
        //         $inputClsItmCell = $clsItmCol.$clsItmRow;

        //         // ����������ѡ���α                    
        //     }
        // }

        var_dump($newList);
    }

    // �ɥ�åץ�����ꥹ�Ȥ򥪥֥������Ȥ˥��åȤ���
    protected function setDropdownList() {
        if (!$this->dropdownDSCI) {
            $this->setDropdownForDivSubAndClsItm();
        }
        if (!$this->dropdownCompany) {
            $this->setDropdownForDivSubAndClsItm();
        }
        if (!$this->dropdownGU) {
            $this->setDropdownForGroupAndUser();
        }
        return;
    }

    protected function setDropdownForDivSubAndClsItm() {
        $this->dropdownDSCI = $this->objDB->getDropdownForDivSubAndClsItm();
        return;
    }

    protected function setDropdownForCompany() {
        $this->dropdownCompany = $this->objDB->getDropdownForCompany();
        return;
    }

    protected function setDropdownForGroupAndUser() {
        $this->dropdownGU = $this->objDB->getDropdownForGroupAndUser();
        return;
    }



    // �ƹ��ܤ����ֹ���������
    protected function getColumnNumberList($areaCode) {
        $cellNameList = workSheetConst::getCellNameOfTargetArea($areaCode);
        $headerNameList = $cellNameList['headerList'];
        $cellAddressList = $this->cellAddressList;
        foreach ($headerNameList as $key => $name) {
            if (preg_match("/\A[A-Z]+[1-9][0-9]*\z/", $cellAddressList[$name])) {
                // ������֤ο�����ʬ������
                $columnNumber[$key] = preg_replace("/[1-9][0-9]*/", '', $cellAddressList[$name]);
            } else {
                return false;
            }
        }
        return $columnNumber;
    }

    // ���������̲ߥ졼�Ȥ��������Ȥ˥��åȤ���
    public function setMonetaryRate() {
        if (!$this->objDB) {
            return false;
        }

        $sheet = $this->sheet;

        $cellAddressList = $this->cellAddressList;
        $monetaryHeaderCell = $cellAddressList[WorkSheetConst::MONETARY_RATE_LIST]; 
     
        $monetaryRateMaster = $this->objDB->getTemporaryRateList();

        $firstRowMove = 2; // �ǽ��2�Բ�������
        // �̲ߥ졼�Ȥ��������Ȥ˥��åȤ���
        foreach ($monetaryRateMaster as $monetaryUnitCode => $monetaryData) {
            foreach ($monetaryData as $item => $value) {
                if (!$rowMove) {
                    $rowMove = $firstRowMove;
                } else {
                    ++$rowMove;
                }
                $monetaryRateCode = $value['monetaryRateCode'];
                $rate = $value['conversionRate'];
                $startDate = $value['startDate'];
                $endDate = $value['endDate'];

                // �����������������
                $setArray = array(
                    $monetaryRateCode,
                    $monetaryUnitCode,
                    $rate,
                    $startDate,
                    $endDate
                );

                foreach ($setArray as $index => $setValue) {
                    $cell = self::getMoveCell($monetaryHeaderCell, $rowMove, $index);
                    // ��Ĵ���ʾ��ʬ��ɬ�ܤ����
                    // if ($index === 3 || $index === 4 ) {
                    //     $date = new DateTime($setValue);
                    //     $setValue = Date::dateTimeToExcel($date);
                    // }
                    $sheet->getCell($cell)->setValue($setValue);
                }
            }
        }

        $colCount = count($setArray);

        // ���л����ִ�������
        $patterns = '/([A-z]+|[0-9]+)/';  // ��������ɽ��
        $replace = '$$1';                 // �ִ�����ɽ��

        // �̲ߥ졼�ȼ����ϰϤΥ��å�
        for ($colIndex = 0; $colIndex < $colCount; ++$colIndex) {
            $rangeStart = self::getMoveCell($monetaryHeaderCell, $firstRowMove -1, $colIndex);
            $rangeEnd = self::getMoveCell($monetaryHeaderCell, $rowMove, $colIndex);
            $rangeStart = preg_replace($patterns, $replace, $rangeStart);
            $rangeEnd = preg_replace($patterns, $replace, $rangeEnd);
            $range[$colIndex] = $rangeStart. ':'. $rangeEnd;
        }

        $targetAreaRows = $this->targetAreaRows;
        foreach($targetAreaRows as $areaCode => $rows) {
            // �оݥ��ꥢ�γ��ϹԤȽ�λ�Լ���
            $firstRow = $rows['firstRow'];
            $lastRow = $rows['lastRow'];

            // �оݥ��ꥢ�Υ���̾�Υꥹ�Ȥ����
            $nameList = workSheetConst::getCellNameOfTargetArea($areaCode);

            // Ŭ�ѥ졼�Ȥ�������
            $rateHeaderName = $nameList['headerList']['conversionRate'];
            $rateHeaderCell = $cellAddressList[$rateHeaderName];
            $separate = self::separateRowAndColumn($rateHeaderCell);
            $rateCol = $separate['column']; // Ŭ�ѥ졼�Ȥ����ֹ�(����ե��٥å�)

            for ($row = $firstRow; $row <= $lastRow; ++$row) {
                $cell = $rateCol. $row;
                $inputParam = $sheet->getCell($cell)->getValue();
                // sumifs()�Υ��å�������
                preg_match('/(?<=sumifs\()[^\)]+(?=\))/i', $inputParam, $match);
                // ,(����ޡˤǶ��ڤ�줿�����������
                $conditions = explode(',', $match[0]);

                // $conditions����(�ܺ٤ϥ�������μ����ȤΤ���)
                // $totalRange = $conditions[0];        // ����ϰ�
                // $rateCodeRange = $conditions[1];     // �̲ߥ졼�ȥ����ɸ����ϰ�
                // $rateCodeValue = $conditions[2];     // �̲ߥ졼�ȥ����ɸ������
                // $unitCodeRange = $conditions[3];     // �̲�ñ�̥����ɸ����ϰ�
                // $unitCodeValue = $conditions[4];     // �̲�ñ�̥����ɸ������
                // $startDateRange = $conditions[5];    // Ŭ�ѳ����������ϰ�
                // $startDateValue = $conditions[6];    // Ŭ�ѳ������������
                // $endDateRange = $conditions[7];      // Ŭ�ѽ�λ�������ϰ�
                // $endDateValue = $conditions[8];      // Ŭ�ѽ�λ���������

                $sumifsParam = $range[2];
                $sumifsParam .= ','.$range[0];
                $sumifsParam .= ','.$conditions[2];
                $sumifsParam .= ','.$range[1];
                $sumifsParam .= ','.$conditions[4];
                $sumifsParam .= ','.$range[3];
                $sumifsParam .= ','.$conditions[6];
                $sumifsParam .= ','.$range[4];
                $sumifsParam .= ','.$conditions[8];

                $newFomula = str_replace($match, $sumifsParam, $inputParam);
                $sheet->getCell($cell)->setValue($newFomula);
            }
        }
        
        return true;
    }

    // ����򱦴󤻤ˤ���
    public function setHorizontalRight($cellAddress) {
        $this->sheet->getStyle($cellAddress)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    // ��������ȥإå�����ɽ��̾���ִ�����
    public function cellValueReplace($replace) {
        $cellAddressList = $this->cellAddressList;
        
        foreach ($replace as $key => $name) {
            $cellAddress = $cellAddressList[$key];
            $this->sheet->getCell($cellAddress)->setValue($name);
        }

        return;
    }

    /**
    * ���򤷤��Ԥ�ľ���˻��ꤷ�����ιԤ򥳥ԡ�������������
    * 
    * @param string  $selectedRow �����
    * @param string  $rowNum  ��������Կ�
    * @param string  $colNum  ���ԡ��������
    * 
    * @return boolean
    */
    protected function insertCopyRowBefore($selectedRow, $rowNum, $colNum = workSheetConst::WORK_SHEET_COPY_COLUMN_NUMBER) {
        
        $sheet = $this->sheet;

        $sheet->insertNewRowBefore($selectedRow, $rowNum);
    
        // ���ԡ����ι��ֹ���������줿�Ԥο�����������
        $copyRow = $selectedRow + $rowNum;
    
        for ($row = 0; $row < $rowNum; ++$row) {

            $newRow = $selectedRow + $row; // �������줿�Ԥ��ֹ�

            // ����ν񼰤��ͤ�ʣ��
            for ($col = 1; $col <= $colNum; ++$col) {
                
                $alphaCol = Coordinate::stringFromColumnIndex($col);

                $copyAddress = $alphaCol.$copyRow;
                $newAddress = $alphaCol.$newRow;              

                $copyValue = $sheet->getCell($copyAddress)->getValue();
                $copyStyle = $sheet->getStyle($copyAddress);
    
                $cellPattern = '/(\$?[A-Z]+)'. $copyRow. '(\D?)/';
                $replace = '${1}'.$newRow. '${2}';
                
                $insertValue = preg_replace($cellPattern, $replace, $copyValue);
    
                $sheet->setCellValue($newAddress, $insertValue);
                $sheet->duplicateStyle($copyStyle, $newAddress);
            }
    
            // �Ԥι⤵ʣ��
            $height = $sheet->getRowDimension($copyRow)->getRowHeight();
            $sheet->getRowDimension($newRow)->setRowHeight($height);
        }
    
        // �������ʣ��
        foreach ($sheet->getMergeCells() as $mergeCell) {
            list($startCell, $endCell) = explode(":", $mergeCell);
            $colStart = preg_replace("/[0-9]*/", "", $startCell);
            $colEnd = preg_replace("/[0-9]*/", "", $endCell);
            $rowStart = ((int)preg_replace("/[A-Z]*/", "", $startCell));
            $rowEnd = ((int)preg_replace("/[A-Z]*/", "", $endCell));
    
            // ���ϹԤȽ�λ�Ԥ����סʹ���Υ�����)���Ĺ��ֹ椬���ԡ����ιԤȰ��פ�����Ϸ�������ɲä���
            if ($rowStart === $rowEnd) {
                if ($rowStart === $copyRow) {
                    for ($row = 0; $row < $rowNum; $row++) {
                        $newRow = $selectedRow + $row;
                        $merge = $colStart. (string)$newRow. ":". $colEnd. (string)$newRow;
                        $sheet->mergeCells($merge);
                    }
                }
            }
        }

        return;
    }

    /**
    * �ƥ�ץ졼�Ȥ�������Ԥ��ʹԿ���­�к���
    * 
    * @param object  $spreadSheet �����
    * @param string  $rowNum  ��������Կ�
    * @param string  $colNum  ���ԡ��������
    * 
    * @return boolean
    */
    public function templateAdjust($estimateData) {

        $this->insertDeficiencyRow($estimateData); // ��­�Ԥ�����

        $this->resettingCellAddressList(); // ����̾�Υꥹ�Ȥκ�����

        return;
    }

    /**
    * ���Ѹ������ٹԤΥǡ�������������­����Ԥ���������
    * 
    * @param string  $selectedRow �����
    * @param string  $rowNum  ��������Կ�
    * @param string  $colNum  ���ԡ��������
    * 
    * @return boolean
    */
    protected function insertDeficiencyRow($estimateData) {
        $targetAreaRows = $this->targetAreaRows;

        ksort($estimateData, SORT_NUMERIC);

        $difTotal = 0;

        foreach ($estimateData as $areaCode => $data) {
    
            $firstRow = $targetAreaRows[$areaCode]['firstRow'];
            $lastRow = $targetAreaRows[$areaCode]['lastRow'];
    
            $linage = (int)$lastRow - (int)$firstRow + 1;
    
            $inputRowCount = count($data);    
    
            if ($linage < $inputRowCount + 1) {
    
                $difference = $inputRowCount + 1 - $linage;
    
                $selectedRow = $lastRow - 1;
    
                $this->insertCopyRowBefore($selectedRow, $difference);

                foreach ($targetAreaRows as $code) {
                    if ($code > $areaCode) {
                        $targetAreaRows[$areaCode]['firstRow'] += $difference;
                        $targetAreaRows[$areaCode]['lastRow'] += $difference;
                    } else if ($code =  $areaCode) {
                        $targetAreaRows[$areaCode]['lastRow'] += $difference;
                    }
                }

                $difTotal += $difference;
            }
        }
        $this->endRow += $difTotal;

        return true;
    }

    // ����̾�Υꥹ�Ȥκ�����ʹ���������������̾�Τΰ��֤�Ƽ�������ɬ�פ�������˻��ѡ�
    protected function resettingCellAddressList() {

        $spreadSheet = $this->sheet->getParent();
        $nameList = workSheetConst::getAllNameList();

        foreach ($nameList as $cellName) {
            $cellAddress = $spreadSheet->getNamedRange($cellName, $this->sheet)->getRange();
            $cellAddressList[$cellName] = $cellAddress;
        }

        $this->cellAddressList = $cellAddressList;
        $this->setRowRangeOfTargetArea();
    }
}

?>
