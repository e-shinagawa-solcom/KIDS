<?php

// �ɤ߹��ޤ�Ƥ��ʤ����ɬ�ץե�������ɤ߹���
require_once ('conf.inc');
require_once ( LIB_ROOT . "/mapping/conf_mapping_common.inc");

// PHPSpreadSheet�饤�֥��Υ����ȥ��ɥե������ɤ߹���
require_once ( LIB_ROOT . "/phpspreadsheet/autoload.php" );

// ����ե�������ɤ߹���
require_once ( SRC_ROOT . "/estimate/cmn/const/workSheetConst.php");

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\Drawing as XlsxDrawing;



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

    // ��Ͽ�ѥǡ���


    public function __construct() {
        $this->setExcelErrorList();
    }

    public function dataInitialize($sheetInfo) {
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

        // �оݥ��ꥢ��̾�Υꥹ�Ȥ����
        $targetAreaNameList = workSheetConst::TARGET_AREA_DISPLAY_NAME_LIST;

        foreach ($targetAreaNameList as $areaCode => $areaDisplayName) {
            if ($targetAreaRows[$areaCode]['firstRow'] <= $row && $row <= $targetAreaRows[$areaCode]['lastRow']) {
                $rowAttribute = $areaCode;
                break;
            }
        }
        return $rowAttribute;
    }


    
    // ��������Ȥȥ���̾�Υꥹ�Ȥξ������Ͽ����
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
     * View�ѤΥǡ�������������
     *
     * @param array $spreadSheet Load�����֥å���Υǡ���
     *
     * @return string $sheet ��������Υǡ���
     */
    function makeViewData($spreadSheet, $action, $sheetName = false) {
        $cellAddressList = null;
        $cellAddressListRowAndColumn = null;

        $nameList = $this->nameList;
        foreach ($nameList as $areaCellNameList) {
            if ($cellNameList) {
                $cellNameList = array_merge($areaCellNameList);
            } else {
                $cellNameList = $areaCellNameList;
            }            
        }

        $mergedCellsList = array();
        $startRow = null;
        $startColumn = null;
        $endRow = null;
        $endColumn = null;
        $aryData = array();

        if ($sheetName) {
            $sheet[0] = $spreadSheet->getSheetByName($sheetName);
            // ����̾�Υꥹ�Ȥˤ���̾�ΤΥ�����������
            $data = $this->getCellAddressForCellName($spreadSheet, $sheet, $cellNameList, $rowCheckNameList);
            if (empty($data)) {
                $errorMessage = '�����ǽ�ʥ�������Ȥ�¸�ߤ��ޤ���';
                return $errorMessage;
            }

            $sheetData[0] = $this->makeDataOfSheet($data[0], $action);
            $difference[0] = $this->difference;
            $this->initializeDifference();

        } else {
            // �����Ⱦ�������
            $allSheets = $this->getSheetInfo($spreadSheet);
            // ����̾�Υꥹ�Ȥˤ���̾�ΤΥ�����������
            $datas = $this->getCellAddressForCellName($spreadSheet, $allSheets, $cellNameList, $rowCheckNameList);

            if (empty($datas)) {
                $errorMessage = '�����ǽ�ʥ�������Ȥ�¸�ߤ��ޤ���';
                return $errorMessage;
            }

            foreach ($datas as $key => $data) {
                $sheetData[$key] = $this->makeDataOfSheet($data, $action);
                $difference[$key] = $this->difference;
                $this->initializeDifference();
                $warning[$key] = $this->warning;
            } 

            $sheetData = array_merge($sheetData);
            $difference = array_merge($difference);
        }

        $retData = array(
            'sheetData' => $sheetData,
            'difference' => $difference,
            'warning' => $warning
        );

        return $retData;
    }


    /**
     * �����ȤΥǡ�������������
     *
     * @param array $sheetData �����ȥǡ�����phpSpreadSheet���֥�������
     *
     * @return array $aryData ���ϥǡ�������
     */
    function makeDataOfSheet() {
        $sheet = $this->sheet;

        $areaNameList = workSheetConst::TARGET_AREA_DISPLAY_NAME_LIST;

        // ɸ��Υե���Ȥ����
        $defaultFont = $sheet->getParent()->getDefaultStyle()->getFont();

        // ������̾�����
        $sheetName = $sheet->getTitle();

        // ���Ϲ��󡢽�λ����򥻥åȤ���(��Ͽ��ͤ��Ѵ�����)
        $startRow = $this->startRow;
        $endRow = $this->endRow;
        $startColumn = $this->startColumn;
        $endColumn = $this->endColumn;

        // �Թ⤵�������ξ�����������
        // �Թ⤵
        $rowHeight = $this->getRowHeight($sheet, $startRow, $endRow);
        // ����
        $columnWidth = $this->getColumnWidth($sheet, $startColumn, $endColumn, $defaultFont);

        // ���Ϲԡ��������Handsontable�Ѥ�(0,0)�����ꤹ��
        // ɽ���ѤΥǡ�������Ȥ��Τߡ������������Ϲԡ����������Ѥ���
        //��Excel����ǡ��������������˻��Ѥ���Ȼ��ȥ��뤬����ޤ�)
        $adjustedStartRow = 0;
        $adjustedStartColumn = 0;

        $rowShiftValue = $startRow - $adjustedStartRow; // Row�Υ��ե���
        $adjustedEndRow = $endRow - $rowShiftValue;     // ���ե�ʬ��λ�Ԥ�����
        $columnShiftValue = $startColumn - $adjustedStartColumn; // Column�Υ��ե���
        $adjustedEndColumn = $endColumn - $columnShiftValue;  // ���ե�ʬ��λ�������

        // �ޡ������줿����Υꥹ�Ȥ��������
        $mergedCellsList = $this->getMergedCellsList($sheet, $startRow, $endRow, $startColumn, $endColumn);
        $adjMergedResult = $this->adjustMergedCellsList($mergedCellsList, $rowShiftValue, $columnShiftValue); // �����������

        // ��������Ⱦ����ɽ���Ԥμ���
        $hiddenRowList = $this->hiddenRowList;

        // �ѥ�᡼��������
        for ($i = $adjustedStartRow; $i <= $adjustedEndRow; ++$i) {

            $adjustedRow = $i + $startRow; // ������������Ѥι��ֹ�($adjustedRow�ϥ�������ι��ֹ桢$i��handsontable�Ѥι��ֹ�)

            // ����ɽ���������Υ���⤵�ꥹ�Ⱥ���
            if ($hiddenRowList[$adjustedRow] !== true) {
                // ������طʿ����������
                $hiddenRowHeight[$i] = $rowHeight[$i];
            } else {
                $hiddenRowHeight[$i] = 0.1;
            }
            
            for ($j = $adjustedStartColumn; $j <= $adjustedEndColumn; ++$j) {
                // �����
                $cellAddress = null;
                $cellInfo = null;
                $style = null;
                $adjustedColumn = $j + $startColumn; // ������������Ѥ����ֹ�
                // ������֤���������
                $cellAddress = $this->combineRowAndColumnIndex($adjustedRow, $this->getAlphabetForColumnIndex($adjustedColumn));    
                // ������ͤ��������
                $cellInfo = $this->getCellInfo($sheet, $cellAddress);
                $cellValue = $this->getCellValue($cellInfo);
                $cellData[$i][$j]['value'] = (array_search($cellValue, $this->excelErrorList)) ? '#ERROR!' : $cellValue;
                
                // ����Υ���������������
                $style = $sheet->getStyle($cellAddress);
                // ����Υե���Ⱦ�����������
                $font = $style->getFont();
                // ����ν��Τ��������
                $cellData[$i][$j]['fontFamily'] = $font->getName();
                // �����ʸ�����������������
                $cellData[$i][$j]['fontSize'] = $font->getSize();

                if ($hiddenRowList[$adjustedRow] !== true) {
                    // ������طʿ����������
                    $cellData[$i][$j]['backgroundColor'] = $style->getFill()->getStartColor()->getRGB();
                } else {
                    $cellData[$i][$j]['backgroundColor'] = 'CCCCCC';
                }
                // �����ʸ�������������
                $cellData[$i][$j]['fontColor'] = $font->getColor()->getRGB();

                // ����η���������������
                $cellData[$i][$j]['border'] = $this->getBorderInfo($style);
                if ($i > $adjustedStartRow) {
                    $beforeRow = $i -1;
                    $this->setAvailableBorderInfo($cellData[$i][$j]['border']['top'], $cellData[$beforeRow][$j]['border']['bottom']);
                }
                if ($j > $adjustedStartColumn) {
                    $beforeColumn = $j -1;
                    $this->setAvailableBorderInfo($cellData[$i][$j]['border']['left'], $cellData[$i][$beforeColumn]['border']['right']);
                }
                // ��������־�����������
                $alignmentInfo = $style->getAlignment();
                $cellData[$i][$j]['verticalPosition'] = $this->getVerticalPosition($alignmentInfo);
                $cellData[$i][$j]['horizontalPosition'] = $alignmentInfo->getHorizontal();
                // ����ν񼰾���(���������Ρˤ��������
                $cellData[$i][$j]['emphasis'] = $this->getEmphasizedStyle($font);

                // ����ɽ���������Υǡ������������
                if ($hiddenRowList[$adjustedRow] !== true) {
                    $hiddenCellValue[$i][$j] = $cellData[$i][$j]['value'];
                } else {
                    $hiddenCellValue[$i][$j] = null;
                }
                
            }
        }

        // ��祻�뤬�����硢��縵�Υ���˷����������Ϳ����
        //��Handsontable�б�����祻��κǤ⺸��η������������������褹��١�
        $resultSetBorder = $this->setBorderInfoForMergedCell($mergedCellsList, $cellData);
        
        // ���������CSS��border-style���б�������
        for ($i = $adjustedStartRow; $i <= $adjustedEndRow; $i++) {
            $adjustedRow = $i + $startRow;
            for ($j = $adjustedStartColumn; $j <= $adjustedEndColumn; $j++) {
                foreach($cellData[$i][$j]['border'] as $position => $borderData) {
                    $cellData[$i][$j]['border'][$position] += $this->setBorderForCss($borderData['excelStyle']);
                }
            }
        }

        // Row�Υ��ե��̤���ɽ���Ԥ�ȿ�Ǥ�����
        foreach ($hiddenRowList as $key => $val) {
            $viewKey = $key - $rowShiftValue;
            $viewHiddenRowList[$viewKey] = $val;
        }

        // �ǡ���������˳�Ǽ����
        $viewData = array(
            'sheetName' => $sheetName,              // ������̾
            'mergedCellsList' => $mergedCellsList,  // �ޡ������줿����Υꥹ��
            'startRow' => $adjustedStartRow,        // ���Ϲ�
            'endRow' => $adjustedEndRow,            // ��λ��
            'startColumn' => $adjustedStartColumn,  // ������
            'endColumn' => $adjustedEndColumn,      // ��λ��
            'rowHeight' => $rowHeight,              // �Թ⤵
            'columnWidth' => $columnWidth,          // ����
            'cellData' => $cellData,                // ����Υǡ���(�͡�ʸ���ե���ȡ��������طʿ�)
            'hiddenList' => $viewHiddenRowList,
            'hiddenRowHeight' => $hiddenRowHeight,  // ����ɽ���������ѹԹ⤵
            'hiddenCellValue' => $hiddenCellValue     // ����ɽ���������ѥ���������
        );
        return $viewData;
    }

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
            if($draftCellAddressListRowAndColumn) {
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
     * @param array $sheet �����Ⱦ���
     * @param string $startRow ���Ϲ�
     * @param string $endRow ��λ��
     *
     * @return array $mergedCellsRowAndColumnInfo �ޡ������줿����ꥹ��
     */
    function getMergedCellsList($sheet, $areaStartRow, $areaEndRow, $areaStartColumn, $areaEndColumn) {
        $mergedCellsList =  $sheet->getMergeCells();
        $address = array();
        foreach($mergedCellsList as $value) {
            $address = $this->getRowAndColumnStartToEnd($value);
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
     * @param array $sheet �����Ⱦ���
     * @param string $startRow ���Ϲ�
     * @param string $endRow ��λ��
     * @param array $defaultFont �����ȤΥե���Ⱦ���
     *
     * @return array $rowHeightPixel �ƹԤι⤵
     */
    function getRowHeight($sheet, $startRow, $endRow) {
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
     * @param array $sheet �����Ⱦ���
     * @param string $startColumn ������
     * @param string $endColumn ��λ��
     * @param array $defaultFont �����ȤΥե���Ⱦ���
     *
     * @return array $columnWidthPixel �������
     */
    function getColumnWidth($sheet, $startColumn, $endColumn, $defaultFont) {
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
     * �����ϰϡʥڡ����Ȥ���ͭ�����ϰϡˤ��������
     *
     * @param array $spreadSheet �����Ⱦ���
     *
     * @return string $printArea ���ϥ�����ֵڤӽ�λ�������
     * �ʳ��ϥ�����֡���λ������֡��η��Ǽ�����
     */
    function selectPickUpArea($sheet) {
        $printArea = $sheet->getPageSetup()->getPrintArea();
        return $printArea;
    }

    /**
     * ���ꤷ�������ϰϤκǽ�ڤӺǸ�ιԤ�����������
     *
     * @param string $printArea "���ϥ������:��λ�������"������string
     *
     * @return array $cellAddress ���ϥ���ڤӽ�λ����ι��ֹ�����ֹ�
     */
    function getRowAndColumnStartToEnd($area) {
        $cell = array();
        $cell['start'] = strstr($area, ':', true);
        $cell['end'] = str_replace(':', '', strstr($area, ':'));
        $cellAddress = $this->separateRowAndColumn($cell);
        return $cellAddress;
    }

    /**
     * ������֤�����ֹ�����ֹ����Ф���
     *
     * @param $cell ���ֹ�����ֹ����Ф��륻�����
     *
     * @return array $cellAddress ������֤��б�������ֹ�����ֹ�
     */
    protected static function separateRowAndColumn($cell) {
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
    function combineRowAndColumnIndex($row, $column) {
        if (!preg_match('/[A-Z]+/', $column)) {
            $column = $this->getAlphabetForColumnIndex($column);
        }
        if (is_integer($row) && preg_match('/[A-Z]+/', $column)) {
            $cell = $column . $row;
            return $cell;
        } else {
            return false;
        }
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
     * @param $cell ���ֹ�����ֹ����Ф��륻�����
     *
     * @return array $borders ����η�������
     */
    function getBorderInfo($style) {
        $borderInfoData = $style->getBorders();
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

    // ���־�����������
    function getAlignmentInfo($style) {
        $alignmentInfo = $style->getAlignment();
        return $alignmentInfo;
    }

    // �ƥ����Ȥ��ޤ��֤����֤��������
    function getWrapTextInfo($alignment) {
        $textWrapInfo = $alignment->getWrapText();
        return $textWrapInfo;
    }

    // �ƥ����Ȥο�ʿ���������֤��������
    function getHorizontalPosition ($alignment) {
        $horizontalPosition = $alignment->getHorizontal();
        return $horizontalPosition;
    }

    // �ƥ����Ȥο�ľ���������֤��������
    function getVerticalPosition ($alignment) {
        $verticalPosition = $alignment->getVertical();
        //center�ξ���css�Ѥ�middle���ִ�����
        if ($verticalPosition == 'center') {
            $verticalPosition = 'middle';
        }
        return $verticalPosition;
    }

    // ����Υե���Ⱦ�����������
    function getFontInfo($style) {
        $font = $style->getFont();
        return $font;
    }

    // ����ν��Τ��������
    function getFontFamilyInfo($font) {
        $fontFamily = $font->getName();
        return $fontFamily;
    }

    // ����Υե���ȥ��������������
    function getFontSizeInfo($font) {
        $fontSize = $font->getSize();
        return $fontSize;
    }

    // �����ʸ�������������
    function getFontColorInfo($font) {
        $fontColor = $font->getColor()->getRGB();
        return $fontColor;
    }

    // ��������������ξ�����������
    function getEmphasizedStyle($font) {
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
    protected static function getAlphabetForColumnIndex($columnIndex) {
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
    protected static function getIndexForColumnAlphabet($column) {
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
     * �ϰϤγ��Ϲ����ϳ�������ư������Υ�����֤���������
     * 
     * @param string $startRowOrColumn ���ϹԤޤ��ϳ�����
     * @param integer  $number ���ϹԤޤ��ϳ������������ʥǥե���Ȥ�0�˽��Ϥ��뤫1�˽��Ϥ��뤫���ѹ������
     * 
     * @return $corrected ������ιԤޤ������ֹ�
     */
    function correctPosition($cellRowOrColumn, $startRowOrColumn, $number) {
        $corrected = $cellRowOrColumn - $startRowOrColumn + $number;
        return $corrected;
    }


    // ���ֹ�����ֹ����ĥ��饹�����ꤹ��
    function setCellClass($startRow, $endRow, $startColumn, $endColumn) {
        for($i = $startRow; $i <= $endRow; $i++) {
            for($j = $startColumn; $j <= $endColumn; $j++) {
                $cellClass[] = array(
                    'row' => $i,
                    'col' => $j,
                    'className' => 'row'.$i.' col'.$j
                );
            }
        }
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

    function adjustMergedCellsList(&$mergedCellsList, $rowShift, $columnShift) {
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
                $cellData[$value['row']][$value['col']]['border']['right'] =  $cellData[$value['row']][$endMergedColumn]['border']['right'];
                // ��祻��ΰ��־�η�������ˡ���祻��ΰ��ֲ��η�������������
                $cellData[$value['row']][$value['col']]['border']['bottom'] =  $cellData[$endMergedRow][$value['col']]['border']['bottom'];
            } 
        }
        return true;
    }

    /**
    * ��������ǽ��Ϥ���륨�顼�ꥹ�Ȥ�����
    * 
    * @return $errorList ��������Υ��顼�ꥹ��
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

    // �оݥ��ꥢ�γ��ϹԤȽ�λ�Ԥ򥻥åȤ���
    protected function setRowRangeOfTargetArea() {
        $targetAreaList = workSheetConst::TARGET_AREA_DISPLAY_NAME_LIST;
        foreach($targetAreaList as $areaCode => $areaName) {
            $rows[$areaCode] = $this->getRowRangeOfTargetArea($areaCode);
        }
        $this->targetAreaRows = $rows;
        return true;
    }

    // �оݥ��ꥢ�ι��ϰϤ��������(���ϹԤȽ�λ��)
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

    // ����κǽ�����Ǥ��������
    public static function getFirstElement($array){
        return current($array);
    }

    // ����̾�Τ�����ֹ桢���ֹ���������
    protected function getRowAndColumnFromCellName($cellName) {
        $cellAddressList = $this->cellAddressList;
        $cellAddress = $cellAddressList[$cellName];
        $rowAndColumn = self::separateRowAndColumn($cellAddress);
        return $rowAndColumn;
    }

    // ����̾�Τ�����ֹ���������
    protected function getRowNumberFromCellName($cellName) {
        $rowAndColumn = $this->getRowAndColumnFromCellName($cellName);
        return $rowAndColumn['row'];
    }

    // ����̾�Τ������ֹ���������
    protected function getColumnNumberFromCellName($cellName) {
        $rowAndColumn = $this->getRowAndColumnFromCellName($cellName);
        return $rowAndColumn['column'];
    }
}

?>
