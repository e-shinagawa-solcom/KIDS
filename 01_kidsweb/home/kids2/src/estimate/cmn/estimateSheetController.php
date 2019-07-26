<?php

// 読み込まれていなければ必要ファイルを読み込む
require_once ('conf.inc');
require_once ( LIB_ROOT . "/mapping/conf_mapping_common.inc");

// PHPSpreadSheetライブラリのオートロードファイル読み込み
require_once ( LIB_ROOT . "/phpspreadsheet/autoload.php" );

// 定数ファイルの読み込み
require_once ( SRC_ROOT . "/estimate/cmn/const/workSheetConst.php");

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\Drawing as XlsxDrawing;



/**
 * excelデータ取り込み・変換クラス
 * 
 * PHPSpreadSheetライブラリを使用してexcelファイルから必要なデータを出力する
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

    // 仕入科目 1224:チャージ計算用変数
    protected $overSeasMoldDepreciation;       // 仕入科目 403:海外金型償却の合計
    protected $importPartsCosts;               // 仕入科目 402:輸入パーツ仕入高の合計
    protected $tariff;

    // 登録用データ


    public function __construct() {
        $this->setExcelErrorList();
    }

    public function dataInitialize($sheetInfo) {
        $this->setSheetInfo($sheetInfo);
        $this->setRowRangeOfTargetArea();
    }
    
    // データ出力------------------------------------------------------------------------

    // シートオブジェクト
    public function outputSheet() {
        return $this->sheet;
    }

    // 対象エリアの開始行と終了行
    public function outputTargetAreaRows() {
        return $this->targetAreaRows;
    }

    // セル名称に対応したセル位置リスト
    public function outputCellAddressList() {
        return $cellAddressList;
    }    

    //-----------------------------------------------------------------------------------

    /**
    *	概要	: Excelファイルフォーマットチェック
    *
    *
    *	解説	: アップロードされたExcelファイルのフォーマットが正常かチェックする
    *
    *	対象	: 結果用テンプレート
    *
    *	@param	[$exc]		: [Object]	. ExcelParser Object（参照渡し）
    *	@param	[$aryData]	: [Array]	. $_REQUEST より取得した値
    *
    *	@return	[$aryErr]	: [Array]	. エラーチェック配列
    */
    public static function checkFileFormat( &$file )
    {
        $error = true;
        $checkResult　= '';	// チェック結果

        // 拡張子による判定
        if(!empty($file['exc_type']) || strtolower($file['exc_type']) == APP_EXCEL_TYPE ) {
            // 拡張子がxlsxならファイルを開いて確認する
            $excel_file	= FILE_UPLOAD_TMPDIR . $file["exc_tmp_name"];
            $finfo  = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $excel_file);
            finfo_close($finfo);

            // MIME_TYPEによる判定
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


    // 指定した行がどの対象エリアに属するかを判定する
    public function checkAttributeRow($row) {
        $rowAttribute = null;

        // 対象エリアの開始行と終了行を取得
        $targetAreaRows = $this->targetAreaRows;

        // 対象エリアの名称リストを取得
        $targetAreaNameList = workSheetConst::TARGET_AREA_DISPLAY_NAME_LIST;

        foreach ($targetAreaNameList as $areaCode => $areaDisplayName) {
            if ($targetAreaRows[$areaCode]['firstRow'] <= $row && $row <= $targetAreaRows[$areaCode]['lastRow']) {
                $rowAttribute = $areaCode;
                break;
            }
        }
        return $rowAttribute;
    }


    
    // ワークシートとセル名称リストの情報を登録する
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
     * ブックからシートの情報を取得し、セル名称に対応するセル位置を取得する。
     *
     * @param array $spreadSheet ブックのphpSpreadSheetオブジェクト
     *
     * @return array $sheetInfo シート情報
     */
    public static function getSheetInfo($spreadSheet, $nameList, $rowCheckNameList) {
        $cellAddressList = array();
        $separate = array();
        // シート数取得
        $sheetCount = $spreadSheet->getSheetCount();
        
        for ($count = 0; $count < $sheetCount; ++$count) {
            $displayInvalid = false; // 表示無効フラグ

            $sheet = $spreadSheet->getSheet($count);
            $sheetName = $sheet->getTitle();
            if ($sheet->getSheetState() === 'visible') {
                foreach($nameList as $val) {
                    $namedRange = null;
                    // シート内にセル名称が設定されているか検索する
                    $namedRange = $spreadSheet->getNamedRange($val, $sheet);
                    if ($namedRange && $namedRange->getWorkSheet()->getTitle() === $sheetName) {
                        // 取得した結果内のシート名と検索時に指定したシート名が一致した場合のみセル名称に対するセル位置の配列を生成する
                        // 対象シートで定義できなかった場合、ブックに設定されたデータを取得しないようするための対策
                        $cellAddress = $namedRange->getRange();
                        $cellAddressList[$val] = $cellAddress;
                        $separate[$val] = self::separateRowAndColumn($cellAddress);
                    } else {
                        // シート内で見つからないセル名称があった場合は処理を中断し、表示無効フラグを立てる
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
                // 列数が指定数でない場合は削除フラグを立てる
                    $displayInvalid = true;
                } else {
                    // 各入力フォームのヘッダーが同じ行に無いときは、データ削除フラグを立てる
                    foreach($rowCheckNameList as $array) {
                        if (!self::rowCheck($separate, $array)) {
                            $displayInvalid = true;
                            break;
                        }
                    }
                }
            }
            $sheetInfo[$sheetName] = array(
                'sheet' => $sheet,                   // phpSpreadSheetで生成したシートオブジェクト
                'displayInvalid' => $displayInvalid, // 表示無効フラグ
                'cellAddress' => $cellAddressList,   // セル名称と対応するセル位置のリスト
                'startRow' => $startRow,             // 開始行
                'endRow' => $endRow,                 // 終了行
                'startColumn' => $startColumn,       // 開始列
                'endColumn' => $endColumn            // 終了列
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
     * View用のデータを生成する
     *
     * @param array $spreadSheet Loadしたブック内のデータ
     *
     * @return string $sheet シート内のデータ
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
            // セル名称リストにある名称のセルを取得する
            $data = $this->getCellAddressForCellName($spreadSheet, $sheet, $cellNameList, $rowCheckNameList);
            if (empty($data)) {
                $errorMessage = '選択可能なワークシートが存在しません';
                return $errorMessage;
            }

            $sheetData[0] = $this->makeDataOfSheet($data[0], $action);
            $difference[0] = $this->difference;
            $this->initializeDifference();

        } else {
            // シート情報を取得
            $allSheets = $this->getSheetInfo($spreadSheet);
            // セル名称リストにある名称のセルを取得する
            $datas = $this->getCellAddressForCellName($spreadSheet, $allSheets, $cellNameList, $rowCheckNameList);

            if (empty($datas)) {
                $errorMessage = '選択可能なワークシートが存在しません';
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
     * シートのデータを生成する
     *
     * @param array $sheetData シートデータのphpSpreadSheetオブジェクト
     *
     * @return array $aryData 出力データ配列
     */
    function makeDataOfSheet() {
        $sheet = $this->sheet;

        $areaNameList = workSheetConst::TARGET_AREA_DISPLAY_NAME_LIST;

        // 標準のフォントを取得
        $defaultFont = $sheet->getParent()->getDefaultStyle()->getFont();

        // シート名を取得
        $sheetName = $sheet->getTitle();

        // 開始行列、終了行列をセットする(列は数値に変換する)
        $startRow = $this->startRow;
        $endRow = $this->endRow;
        $startColumn = $this->startColumn;
        $endColumn = $this->endColumn;

        // 行高さと列幅の情報を取得する
        // 行高さ
        $rowHeight = $this->getRowHeight($sheet, $startRow, $endRow);
        // 列幅
        $columnWidth = $this->getColumnWidth($sheet, $startColumn, $endColumn, $defaultFont);

        // 開始行、開始列をHandsontable用に(0,0)に設定する
        // 表示用のデータを作るときのみ、補正した開始行、開始列を使用する
        //（Excelからデータを取得する場合に使用すると参照セルがずれます)
        $adjustedStartRow = 0;
        $adjustedStartColumn = 0;

        $rowShiftValue = $startRow - $adjustedStartRow; // Rowのシフト量
        $adjustedEndRow = $endRow - $rowShiftValue;     // シフト分を終了行に補正
        $columnShiftValue = $startColumn - $adjustedStartColumn; // Columnのシフト量
        $adjustedEndColumn = $endColumn - $columnShiftValue;  // シフト分を終了列に補正

        // マージされたセルのリストを取得する
        $mergedCellsList = $this->getMergedCellsList($sheet, $startRow, $endRow, $startColumn, $endColumn);
        $adjMergedResult = $this->adjustMergedCellsList($mergedCellsList, $rowShiftValue, $columnShiftValue); // セル位置補正

        // ワークシート上の非表示行の取得
        $hiddenRowList = $this->hiddenRowList;

        // パラメータの設定
        for ($i = $adjustedStartRow; $i <= $adjustedEndRow; ++$i) {

            $adjustedRow = $i + $startRow; // セル位置復元用の行番号($adjustedRowはエクセルの行番号、$iはhandsontable用の行番号)

            // 情報表示押下時のセル高さリスト作成
            if ($hiddenRowList[$adjustedRow] !== true) {
                // セルの背景色を取得する
                $hiddenRowHeight[$i] = $rowHeight[$i];
            } else {
                $hiddenRowHeight[$i] = 0.1;
            }
            
            for ($j = $adjustedStartColumn; $j <= $adjustedEndColumn; ++$j) {
                // 初期化
                $cellAddress = null;
                $cellInfo = null;
                $style = null;
                $adjustedColumn = $j + $startColumn; // セル位置復元用の列番号
                // セル位置を復元する
                $cellAddress = $this->combineRowAndColumnIndex($adjustedRow, $this->getAlphabetForColumnIndex($adjustedColumn));    
                // セルの値を取得する
                $cellInfo = $this->getCellInfo($sheet, $cellAddress);
                $cellValue = $this->getCellValue($cellInfo);
                $cellData[$i][$j]['value'] = (array_search($cellValue, $this->excelErrorList)) ? '#ERROR!' : $cellValue;
                
                // セルのスタイルを取得する
                $style = $sheet->getStyle($cellAddress);
                // セルのフォント情報を取得する
                $font = $style->getFont();
                // セルの書体を取得する
                $cellData[$i][$j]['fontFamily'] = $font->getName();
                // セルの文字サイズを取得する
                $cellData[$i][$j]['fontSize'] = $font->getSize();

                if ($hiddenRowList[$adjustedRow] !== true) {
                    // セルの背景色を取得する
                    $cellData[$i][$j]['backgroundColor'] = $style->getFill()->getStartColor()->getRGB();
                } else {
                    $cellData[$i][$j]['backgroundColor'] = 'CCCCCC';
                }
                // セルの文字色を取得する
                $cellData[$i][$j]['fontColor'] = $font->getColor()->getRGB();

                // セルの罫線情報を取得する
                $cellData[$i][$j]['border'] = $this->getBorderInfo($style);
                if ($i > $adjustedStartRow) {
                    $beforeRow = $i -1;
                    $this->setAvailableBorderInfo($cellData[$i][$j]['border']['top'], $cellData[$beforeRow][$j]['border']['bottom']);
                }
                if ($j > $adjustedStartColumn) {
                    $beforeColumn = $j -1;
                    $this->setAvailableBorderInfo($cellData[$i][$j]['border']['left'], $cellData[$i][$beforeColumn]['border']['right']);
                }
                // セルの配置情報を取得する
                $alignmentInfo = $style->getAlignment();
                $cellData[$i][$j]['verticalPosition'] = $this->getVerticalPosition($alignmentInfo);
                $cellData[$i][$j]['horizontalPosition'] = $alignmentInfo->getHorizontal();
                // セルの書式情報(太字、斜体）を取得する
                $cellData[$i][$j]['emphasis'] = $this->getEmphasizedStyle($font);

                // 情報表示押下時のデータを作成する
                if ($hiddenRowList[$adjustedRow] !== true) {
                    $hiddenCellValue[$i][$j] = $cellData[$i][$j]['value'];
                } else {
                    $hiddenCellValue[$i][$j] = null;
                }
                
            }
        }

        // 結合セルがある場合、結合元のセルに罫線情報を付与する
        //（Handsontable対応、結合セルの最も左上の罫線情報を取得して描画する為）
        $resultSetBorder = $this->setBorderInfoForMergedCell($mergedCellsList, $cellData);
        
        // 罫線情報をCSSのborder-styleに対応させる
        for ($i = $adjustedStartRow; $i <= $adjustedEndRow; $i++) {
            $adjustedRow = $i + $startRow;
            for ($j = $adjustedStartColumn; $j <= $adjustedEndColumn; $j++) {
                foreach($cellData[$i][$j]['border'] as $position => $borderData) {
                    $cellData[$i][$j]['border'][$position] += $this->setBorderForCss($borderData['excelStyle']);
                }
            }
        }

        // Rowのシフト量を非表示行に反映させる
        foreach ($hiddenRowList as $key => $val) {
            $viewKey = $key - $rowShiftValue;
            $viewHiddenRowList[$viewKey] = $val;
        }

        // データを配列に格納する
        $viewData = array(
            'sheetName' => $sheetName,              // シート名
            'mergedCellsList' => $mergedCellsList,  // マージされたセルのリスト
            'startRow' => $adjustedStartRow,        // 開始行
            'endRow' => $adjustedEndRow,            // 終了行
            'startColumn' => $adjustedStartColumn,  // 開始列
            'endColumn' => $adjustedEndColumn,      // 終了列
            'rowHeight' => $rowHeight,              // 行高さ
            'columnWidth' => $columnWidth,          // 列幅
            'cellData' => $cellData,                // セルのデータ(値、文字フォント、罫線、背景色)
            'hiddenList' => $viewHiddenRowList,
            'hiddenRowHeight' => $hiddenRowHeight,  // 情報表示押下時用行高さ
            'hiddenCellValue' => $hiddenCellValue     // 情報表示押下時用セル入力値
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

        // マージセルの情報を開始行をキーに持つ配列に格納する
        foreach ($mergedCellsList as $mergeInfo) {
            $key = $mergeInfo['row'];
            $newMergeCellsList[$key][] = $mergeInfo;
        }

        // マージセルのない行を空配列で埋める
        for ($i = $startRow; $i <= $endRow; ++$i) {
            if (!$newMergeCellsList[$i]) {
                $newMergeCellsList[$i] = array();
            }            
        }

        // キー（行番号）の昇順ソート
        ksort($newMergeCellsList);

        // 無効行を削除する
        foreach ($hiddenList as $row => $bool) {
            if ($bool === true) {
                unset($cellData[$row]);
                unset($rowHeight[$row]);
                unset($newMergeCellsList[$row]);
                ++$deleteCount;
            }
        }

        // 配列を前に詰める（キー：行番号に相当　を連番にする)
        $cellData = array_merge($cellData);
        $rowHeight = array_merge($rowHeight);
        $newMergeCellsList = array_merge($newMergeCellsList);

        unset($mergedCellsList);

        // マージセル情報の配列を復元する
        foreach ($newMergeCellsList as $newRow => $mergeInfoList) {
            if ($mergeInfoList) {
                foreach ($mergeInfoList as $mergeInfo) {
                    // 補正後の行を入れる（マージセル情報の開始行は処理前のままなので置換処理を行う）
                    $mergeInfo['row'] = $newRow;

                    $mergedCellsList[] = $mergeInfo;
                }
            }
        }

        $endRow -= $deleteCount; // 行を削除した数だけ終了行を繰り上げる
        
        $viewData['cellData'] = $cellData;
        $viewData['rowHeight'] = $rowHeight;
        $viewData['endRow'] = $endRow;
        $viewData['mergedCellsList'] = $mergedCellsList;               

        return $viewData;
    }

    /**
     * Excelの罫線の優先度を設定する
     * （優先度の高い順に設定）
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
     * セル名称に対応するセル位置を取得する
     *
     * @param array $spreadSheet ブックのphpSpreadSheetオブジェクト
     * @param array $sheet シート情報(シートのphpSpreadSheetオブジェクトを子要素にもつ配列)
     *
     * @return array $data シート情報（無効なシートを削除）
     */
    function getCellAddressForCellName($spreadSheet, $sheet, $nameList, $rowCheckNameList) {
        $cellAddressList = array();
        $cellAddressListRowAndColumn = array();
        if (!is_array($sheet)) {
            // 引数がシートオブジェクトの場合は配列にセットする
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
                // シート内にセル名称が設定されているか検索する
                $namedRange = $spreadSheet->getNamedRange($val, $value);
                if ($namedRange && $namedRange->getWorkSheet()->getTitle() === $value->getTitle()) {
                    // 取得した結果内のシート名と検索時に指定したシート名が一致した場合のみセル名称に対するセル位置の配列を生成する
                    // 対象シートで定義できなかった場合、ブックに設定されたデータを取得しないようするための対策
                    $draftCellAddress = $namedRange->getRange();
                    $draftCellAddressList[$val] = $draftCellAddress;
                    $draftCellAddressListRowAndColumn[$val] = $this->separateRowAndColumn($draftCellAddress); //行と列に分割したセル位置リスト
                } else {
                    // シート内で見つからないセル名称があった場合は処理を中断し、データ削除フラグを立てる
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
                // 列数が指定数でない場合は削除フラグを立てる
                    $showDataDeleteFlug = 1;
                } else {
                    // 各入力フォームのヘッダーが同じ行に無いときは、データ削除フラグを立てる
                    foreach($rowCheckNameList as $array) {
                        if (!$this->rowCheck($draftCellAddressListRowAndColumn, $array)) {
                            $showDataDeleteFlug = 1;
                            break;
                        }
                    }
                }
            }
            if ($showDataDeleteFlug !== 1) {
                // データ削除フラグが1でない時、シート及びセル名称に対応したセルリストを配列にセットする
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
     * 指定のセル名称が同じ行に存在するかチェックする
     *
     * @param array $cellAddressList セル名称と紐付くセル位置のリスト
     * @param array $array 同じ行に存在する必要のあるセル名称リスト
     *
     * @return boolean $result チェック結果
     */
    protected static function rowCheck($cellAddressList, $array) {
        $param = null;
        $result = false;
        foreach($array as $value) {
            // 1回目のループでは列番号を$paramにセットする
            if (!$param) {
                $param = $cellAddressList[$value]['row'];
            } else {
                if ($param == $cellAddressList[$value]['row']) {
                    $result = true;
                } else {
                    // 1つでも同じ行にセルが存在しなかった場合はチェックNGとして処理を中断する
                    $result = false;
                    break;
                }
            }        
        }
        return $result;
    }

    /**
     * マージされたセルのリストを取得する
     *
     * @param array $sheet シート情報
     * @param string $startRow 開始行
     * @param string $endRow 終了行
     *
     * @return array $mergedCellsRowAndColumnInfo マージされたセルリスト
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
            // マージされたセルが指定の範囲外にあるときはリストに追加しない
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

    // セル情報を取得する
    function getCellInfo($sheet, $address) {
        $cellInfo = $sheet->getCell($address);
        return $cellInfo;
    }

    // セル情報からセルの値を取得する
    function getCellValue($cellInfo) {
        $value = $cellInfo->getFormattedValue();
        return $value;
    }

    /**
     * シートの行高さを取得する
     *
     * @param array $sheet シート情報
     * @param string $startRow 開始行
     * @param string $endRow 終了行
     * @param array $defaultFont シートのフォント情報
     *
     * @return array $rowHeightPixel 各行の高さ
     */
    function getRowHeight($sheet, $startRow, $endRow) {
        if (is_integer($startRow) && is_integer($endRow)) {
            if ($startRow <= $endRow) {
                for ($i = $startRow; $i <= $endRow; $i++) {
                    $rowHeight = $sheet->getRowDimension($i)->getRowHeight();
                    // ピクセルに変換
                    $rowHeightPixel[] = XlsxDrawing::pointsToPixels($rowHeight);
                }
                return $rowHeightPixel;
            }
        }
        return false;
    }


    /**
     * シートの列幅を取得する
     *
     * @param array $sheet シート情報
     * @param string $startColumn 開始列
     * @param string $endColumn 終了列
     * @param array $defaultFont シートのフォント情報
     *
     * @return array $columnWidthPixel 各列の幅
     */
    function getColumnWidth($sheet, $startColumn, $endColumn, $defaultFont) {
        if ($startColumn <= $endColumn) {
            for ($i = $startColumn; $i <= $endColumn; $i++) {
                $column = self::getAlphabetForColumnIndex($i);
                $columnWidth = $sheet->getColumnDimension($column)->getWidth();
                // ピクセルに変換
                $columnWidthPixel[$i] = XlsxDrawing::cellDimensionToPixels($columnWidth, $defaultFont);
            }
            return $columnWidthPixel;
        }
        return false;
    }
    /**
     * 印刷範囲（ページとして有効な範囲）を取得する
     *
     * @param array $spreadSheet シート情報
     *
     * @return string $printArea 開始セル位置及び終了セル位置
     * （開始セル位置：終了セル位置　の形で取得）
     */
    function selectPickUpArea($sheet) {
        $printArea = $sheet->getPageSetup()->getPrintArea();
        return $printArea;
    }

    /**
     * 指定したセル範囲の最初及び最後の行と列を取得する
     *
     * @param string $printArea "開始セル位置:終了セル位置"形式のstring
     *
     * @return array $cellAddress 開始セル及び終了セルの行番号と列番号
     */
    function getRowAndColumnStartToEnd($area) {
        $cell = array();
        $cell['start'] = strstr($area, ':', true);
        $cell['end'] = str_replace(':', '', strstr($area, ':'));
        $cellAddress = $this->separateRowAndColumn($cell);
        return $cellAddress;
    }

    /**
     * セル位置から行番号と列番号を抽出する
     *
     * @param $cell 行番号と列番号を抽出するセル位置
     *
     * @return array $cellAddress セル位置に対応する行番号と列番号
     */
    protected static function separateRowAndColumn($cell) {
        $cellAddress = array();
        if (is_array($cell)) {
            foreach ($cell as $key => $value) {
                $cellAddress[$key]['row'] = preg_replace('/[^0-9]/', '', $value);    // 行番号の取得
                $cellAddress[$key]['column'] = preg_replace('/[^A-Z]/', '', $value); // 列番号の取得
            }
        } else {
            $cellAddress['row'] = preg_replace('/[^0-9]/', '', $cell);      // 行番号の取得
            $cellAddress['column'] = preg_replace('/[^A-Z]/', '', $cell);   // 列番号の取得
        }
        return $cellAddress;
    }

    /**
     * 行番号と列番号を結合する
     *
     * @param $row 行番号
     * @param $column 列番号
     *
     * @return array $cell セル位置
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
     * スタイル情報を取得する
     *
     * @param $sheet 対象のワークシート
     *
     * @return array $styles セルのスタイル情報
     */
    function getStyleInfo($sheet, $address) {
        $styleInfoData = $sheet->getStyle($address);
        return $styleInfoData;
    }


    /**
     * 罫線情報を取得する
     *
     * @param $cell 行番号と列番号を抽出するセル位置
     *
     * @return array $borders セルの罫線情報
     */
    function getBorderInfo($style) {
        $borderInfoData = $style->getBorders();
        // 罫線情報の中から部位別の情報を取得する
        $border = array();
        $border['left'] = $borderInfoData->getLeft();
        $border['right'] = $borderInfoData->getRight();
        $border['top'] = $borderInfoData->getTop();
        $border['bottom'] = $borderInfoData->getBottom();

        // 罫線情報から線の情報を取得し結合
        $borders = array();
        foreach ($border as $key => $value) {
            $borders[$key]['color'] = $value->getColor()->getRGB(); // 罫線の色
            $borders[$key]['excelStyle'] = $value->getBorderStyle();
        }
        return $borders;
    }

    // 配置情報を取得する
    function getAlignmentInfo($style) {
        $alignmentInfo = $style->getAlignment();
        return $alignmentInfo;
    }

    // テキストの折り返し状態を取得する
    function getWrapTextInfo($alignment) {
        $textWrapInfo = $alignment->getWrapText();
        return $textWrapInfo;
    }

    // テキストの水平方向の配置を取得する
    function getHorizontalPosition ($alignment) {
        $horizontalPosition = $alignment->getHorizontal();
        return $horizontalPosition;
    }

    // テキストの垂直方向の配置を取得する
    function getVerticalPosition ($alignment) {
        $verticalPosition = $alignment->getVertical();
        //centerの場合はcss用にmiddleに置換する
        if ($verticalPosition == 'center') {
            $verticalPosition = 'middle';
        }
        return $verticalPosition;
    }

    // セルのフォント情報を取得する
    function getFontInfo($style) {
        $font = $style->getFont();
        return $font;
    }

    // セルの書体を取得する
    function getFontFamilyInfo($font) {
        $fontFamily = $font->getName();
        return $fontFamily;
    }

    // セルのフォントサイズを取得する
    function getFontSizeInfo($font) {
        $fontSize = $font->getSize();
        return $fontSize;
    }

    // セルの文字色を取得する
    function getFontColorInfo($font) {
        $fontColor = $font->getColor()->getRGB();
        return $fontColor;
    }

    // セルの太字、斜体情報を取得する
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
     * 数値化された列番号をアルファベットに変換する
     * 
     * @param $columnIndex
     * 
     * @return $column
     */
    protected static function getAlphabetForColumnIndex($columnIndex) {
        if (!is_integer($columnIndex)) {
            return false;
        }
        // キーの数値にアルファベットを対応させた配列を生成する
        for ($i = 0; $i < 26; $i++) {
            $alphabet[] = chr(ord('A') + $i);
        }
        // 一の位の処理
        $number = fmod($columnIndex, 26);
        $column = $alphabet[$number];
        $carry = ($columnIndex - $number) / 26;
        // それ以上の位がある場合は以下処理
        while ($carry > 0) {
            $carry = $carry -1;
            $number = fmod($carry, 26);
            $column = $alphabet[$number] .$column;
            $carry = ($carry - $number) / 26;
        }
        return $column;
    }


    /**
     * Excelの列名を数値に変換する
     * 
     * @param $column
     * 
     * @return $columnIndex
     */
    protected static function getIndexForColumnAlphabet($column) {
        $error = !preg_match('/[A-Z]+/', $column);
        if (!$error) {
            $columnIndex = 0;
            //文字列を反転する
            $column = strrev($column);
            //1文字ずつ切ったものを配列に格納
            $columnDigit = str_split($column);
            for($i = 0; $i < count($columnDigit); $i++) {
            //ord関数を使いアルファベットを数値(ASCII値）にし、-64することでA = 1 から始まる番号になる
            //アルファベットが増えるごとに26の累乗桁を増やす
            $columnIndex += (ord($columnDigit[$i]) -64) * pow(26, $i);
            }
            $columnIndex = $columnIndex -1; // A = 0から始まるように1を引く
            return $columnIndex;
        }
    }

    /**
     * 範囲の開始行又は開始列を移動する場合のセル位置を補正する
     * 
     * @param string $startRowOrColumn 開始行または開始列
     * @param integer  $number 開始行または開始列の設定先（デフォルトが0に出力するか1に出力するかで変更する）
     * 
     * @return $corrected 補正後の行または列番号
     */
    function correctPosition($cellRowOrColumn, $startRowOrColumn, $number) {
        $corrected = $cellRowOrColumn - $startRowOrColumn + $number;
        return $corrected;
    }


    // 行番号と列番号を持つクラスを設定する
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

    // 罫線情報をCSS用に変換する
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
     * 隣接するセル間の有効な罫線情報を取得する
     * (Excelでは隣接部位に非表示の罫線情報を持っている場合があり、そのまま描画するとhtmlでは2重に表示されるため)
     * 
     * @param array   $high 列番号又は行番号が大きい方のセル
     * @param array   $low  列番号又は行番号が小さい方のセル
     * 
     */
    protected function setAvailableBorderInfo(&$high, &$low ) {
        // 小さい側の罫線がある場合のみ有効な罫線情報の変換を行う
        if ($high['excelStyle'] !== "none") {
            if ($high['excelStyle'] === $low['excelStyle']) {
                // 罫線の書式が同じで色が異なる場合は黒にする(色による優先順位の判別は行わない)
                if ($high['color'] !== $low['color']) {
                    $low['color'] = "000000";
                }
            }
            // 罫線の種類が異なる場合は優先順位別に指定する
            else {
                // 優先順位配列を取得
                $linePriority = $this->makeLinePriority();
                $lowPriorityKey = array_keys($linePriority, $low['excelStyle']);
                $highPriorityKey = array_keys($linePriority, $high['excelStyle']);
                if ($lowPriorityKey > $highPriorityKey) {
                    // $highの罫線の優先順位が高い場合は$lowに代入する
                    $low = $high;
                }
            }
            // 大きい側の罫線情報を削除する
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
     * 結合セルがある場合、結合元のセルに罫線情報を付与する
     *（Handsontable対応、結合セルの最も左上の罫線情報を取得して描画する為）
    * 
    * @param array   $mergedCellsList マージされたセルの情報(row：開始行、column：開始列、rowspan：結合行数、colspan：結合列数の配列)
    * @param array   $cellData  セルの情報（border：罫線の情報を含む）
    * 
    * @return boolean
    */
    protected function setBorderInfoForMergedCell($mergedCellsList, &$cellData) {
        if ($mergedCellsList) {
            foreach($mergedCellsList as $value) {
                $endMergedRow = $value['row'] + $value['rowspan'] -1;
                $endMergedColumn = $value['col'] + $value['colspan'] -1;
                // 結合セルの一番左の罫線情報に、結合セルの一番右の罫線情報を入れる
                $cellData[$value['row']][$value['col']]['border']['right'] =  $cellData[$value['row']][$endMergedColumn]['border']['right'];
                // 結合セルの一番上の罫線情報に、結合セルの一番下の罫線情報を入れる
                $cellData[$value['row']][$value['col']]['border']['bottom'] =  $cellData[$endMergedRow][$value['col']]['border']['bottom'];
            } 
        }
        return true;
    }

    /**
    * エクセルで出力されるエラーリストの生成
    * 
    * @return $errorList エクセルのエラーリスト
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

    // 対象エリアの開始行と終了行をセットする
    protected function setRowRangeOfTargetArea() {
        $targetAreaList = workSheetConst::TARGET_AREA_DISPLAY_NAME_LIST;
        foreach($targetAreaList as $areaCode => $areaName) {
            $rows[$areaCode] = $this->getRowRangeOfTargetArea($areaCode);
        }
        $this->targetAreaRows = $rows;
        return true;
    }

    // 対象エリアの行範囲を取得する(開始行と終了行)
    protected function getRowRangeOfTargetArea($areaCode) {
        // 対象エリアのセル名称取得
        $cellNameList = workSheetConst::getCellNameOfTargetArea($areaCode);
        $cellAddressList = $this->cellAddressList;
        // ヘッダーおよびフッター（計算結果）のセル名称を1つセットする
        $upperCellName = self::getFirstElement($cellNameList['headerList']);
        $belowCellName = self::getFirstElement($cellNameList['resultList']);

        // セル名称から行番号を取得する
        $firstRow = $this->getRowNumberFromCellName($upperCellName) + 1;
        $lastRow = $this->getRowNumberFromCellName($belowCellName) - 1;
        $rows = array(
            'firstRow' => $firstRow,
            'lastRow' => $lastRow
        );
        return $rows;
    }

    // 配列の最初の要素を取得する
    public static function getFirstElement($array){
        return current($array);
    }

    // セル名称から行番号、列番号を取得する
    protected function getRowAndColumnFromCellName($cellName) {
        $cellAddressList = $this->cellAddressList;
        $cellAddress = $cellAddressList[$cellName];
        $rowAndColumn = self::separateRowAndColumn($cellAddress);
        return $rowAndColumn;
    }

    // セル名称から行番号を取得する
    protected function getRowNumberFromCellName($cellName) {
        $rowAndColumn = $this->getRowAndColumnFromCellName($cellName);
        return $rowAndColumn['row'];
    }

    // セル名称から列番号を取得する
    protected function getColumnNumberFromCellName($cellName) {
        $rowAndColumn = $this->getRowAndColumnFromCellName($cellName);
        return $rowAndColumn['column'];
    }
}

?>
