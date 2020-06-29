<?php

// 読み込まれていなければ必要ファイルを読み込む
require_once ('conf.inc');
require_once ( LIB_FILE );
require_once ( LIB_DEBUGFILE );


// Composerのオートロードファイル読み込み
require_once ( VENDOR_AUTOLOAD_FILE );

// 定数ファイルの読み込み
require_once ( SRC_ROOT . "/estimate/cmn/const/workSheetConst.php");

// phpSpreadSheetのクラス読み込み
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\Drawing as XlsxDrawing;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;


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

    protected $monetaryUnitFormula;

    protected $targetAreaRows;

    protected $excelErrorList;

    protected $uneditableFlag = false;

    public $errorMessage;
    public $difference;

    // 仕入科目 1224:チャージ計算用変数
    protected $overSeasMoldDepreciation;       // 仕入科目 403:海外金型償却の合計
    protected $importPartsCosts;               // 仕入科目 402:輸入パーツ仕入高の合計
    protected $tariff;
    
    protected $notFoundCellList;

    public function __construct() {
        $this->setExcelErrorList();
    }

    public function dataInitialize($sheetInfo, $objDB) {
        $this->setObjectDatabase($objDB);
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


    // データベースオブジェクトをセットする
    protected function setObjectDatabase($objDB) {
        $this->objDB = $objDB;
        return true;
    }

    // 編集不可能フラグをセットする
    public function setUneditableFlag($flag) {
        $this->uneditableFlag = $flag;
        return true;
    }

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
        if(!empty($file['exc_type']) || strtolower($file['exc_type']) == workSheetConst::APP_EXCEL_TYPE ) {
            // 拡張子がxlsxならファイルを開いて確認する
            $excel_file	= FILE_UPLOAD_TMPDIR . $file["exc_tmp_name"];
            $finfo  = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $excel_file);
            finfo_close($finfo);

            // MIME_TYPEによる判定
            if (!empty($mime_type) && $mime_type == workSheetConst::APP_EXCEL_TYPE) {
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

        // 対象エリア名を取得
        $targetAreaNameList = workSheetConst::TARGET_AREA_NAME;

        foreach ($targetAreaNameList as $areaCode => $areaDisplayName) {
            if ($targetAreaRows[$areaCode]['firstRow'] <= $row && $row <= $targetAreaRows[$areaCode]['lastRow']) {
                $rowAttribute = $areaCode;
//fncDebug("view.log", sprintf("row:%d, area:%d, top:%d last:%d", $row, $areaCode, $targetAreaRows[$areaCode]['firstRow'], $targetAreaRows[$areaCode]['lastRow']), __FILE__, __LINE__, "a");
                break;
            }
        }
        return $rowAttribute;
    }


    
    // ワークシートとセル名称リストの情報をインスタンスにセットする
    public function setSheetInfo($sheetInfo) {
        $sheet = $sheetInfo['sheet'];
        $displayInvalid = $sheetInfo['displayInvalid'];
        $cellAddressList = $sheetInfo['cellAddress'];
        $startRow = $sheetInfo['startRow'];
        $endRow = $sheetInfo['endRow'];
        $startColumn = $sheetInfo['startColumn'];
        $endColumn = $sheetInfo['endColumn'];
        $nameList = $sheetInfo['nameList'];

        $this->sheet = $sheet;
        $this->displayInvalid = $displayInvalid;
        $this->cellAddressList = $cellAddressList;
        $this->startRow = $startRow;
        $this->endRow = $endRow;
        $this->startColumn = $startColumn;
        $this->endColumn = $endColumn;
        $this->nameList = $nameList;

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

        $sheetInfo = [];
        
        for ($count = 0; $count < $sheetCount; ++$count) {
            $displayInvalid = false; // 表示無効フラグ

            $sheet = $spreadSheet->getSheet($count);
            $sheetName = $sheet->getTitle();
            if ($sheet->getSheetState() === 'visible') {
                $notFoundCellList = "";
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
                        if(strlen($notFoundCellList) > 0){
                            $notFoundCellList .= ", ";
                        }
                        $notFoundCellList .= $val;
//                        break;
                    }
                }
                if(strlen($notFoundCellList) > 0){
//fncDebug("kids2.log", $sheetName . ":cell " . $notFoundCellList . " not found", __FILE__, __LINE__, "a");
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
                'endColumn' => $endColumn,           // 終了列
                'nameList' => $nameList              // 検索に使用したセル名称のリスト
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
     * シートのデータを生成する
     *
     * @return array $viewData 出力データ配列
     */
    public function makeDataOfSheet() {
        $sheet = $this->sheet;

        $areaNameList = workSheetConst::TARGET_AREA_DISPLAY_NAME_LIST;

        // エリアごとに列の入力項目を取得する（売上分類はどの列にあるか等）
        foreach($areaNameList as $areaCode => $name) {
            $columnNumberList[$areaCode] = $this->getColumnNumberList($areaCode);
        }

        // 標準のフォントを取得
        $defaultFont = $sheet->getParent()->getDefaultStyle()->getFont();

        // シート名を取得
        $sheetName = $sheet->getTitle();

        // ワークシートの開始行列、終了行列をセットする(列は数値に変換する)
        $startRow = $this->startRow;
        $endRow = $this->endRow;
        $startColumn = $this->startColumn;
        $endColumn = $this->endColumn;

        // ワークシートの行高さと列幅の情報を取得する
        // 行高さ
        $rowHeight = $this->getRowHeight();
        // 列幅
        $columnWidth = $this->getColumnWidth();

        // Handsontable用に開始行、開始列(デフォルトは(0,0))を設定する
        // 表示用のデータを作るときのみ、補正した開始行、開始列を使用する
        //（Excelからデータを取得する場合に使用すると参照セルがずれます)
        $tableStartRow = 0;
        $tableStartColumn = 0;

        // ワークシートとHandsontableの開始行、開始列を比較してシフト量を算出する（補正用）
        $rowShiftValue = $startRow - $tableStartRow; // Rowのシフト量
        $tableEndRow = $endRow - $rowShiftValue;     // シフト分を補正し、Handsontableの終了行を算出
        $columnShiftValue = $startColumn - $tableStartColumn; // Columnのシフト量
        $tableEndColumn = $endColumn - $columnShiftValue;  // シフト分を補正し、Handsontableの終了列を算出

        // マージされたセルのリストを取得する
        $mergedCellsList = $this->getMergedCellsList();
        // マージセルリストをHandsontable用に補正する
        $shiftResult = $this->shiftMergedCellsList($mergedCellsList, $rowShiftValue, $columnShiftValue); 

        // ワークシート上の非表示行の取得
        $hiddenRowList = $this->hiddenRowList;

        // セル位置からセル名称を取得する配列を生成
        $nameListForCellAddress = array_flip($this->cellAddressList);

        // DBからの入力データの取得
        if ($this->inputData) $inputData = $this->inputData;

        // パラメータの設定
        for ($tableRow = $tableStartRow; $tableRow <= $tableEndRow; ++$tableRow) {

            $workSheetRow = $tableRow + $rowShiftValue; // ワークシートの行番号

            // エリアコードを取得
            $areaCode = $this->checkAttributeRow($workSheetRow);

            // 列番号リストの取得
            if ($areaCode) {
                $columnNumberList = $this->getColumnNumberList($areaCode);
            }

            // 情報表示押下時のセル高さリスト作成
            if ($hiddenRowList[$workSheetRow] !== true) {
                // セルの背景色を取得する
                $hiddenRowHeight[$tableRow] = $rowHeight[$tableRow];
            } else {
                $hiddenRowHeight[$tableRow] = 0.1;
            }

            // readOnly行のセット
            if ($inputData) { // DBから見積原価明細のデータを取得している場合
                // 受発注の分類を取得(受注 or 発注)
                $receiveAreaCodeList = workSheetConst::RECEIVE_AREA_CODE;
                $orderAreaCodeList = workSheetConst::ORDER_AREA_CODE;
                if ($areaCode) {
                    $data = $inputData[$workSheetRow]['data']; // 行データの取得
                    $status = $data['statusCode']; // ステータスコードの取得
                    $estimateDetailNo = is_numeric($data['estimateDetailNo']) ? (int)$data['estimateDetailNo'] : $data['estimateDetailNo']; // 見積原価明細番号

                    $detailNoList[] = array(
                        'row' => $tableRow,
                        'estimateDetailNo' => $estimateDetailNo
                    );

                    if ($receiveAreaCodeList[$areaCode] === true) { // 受注の場合

                        if ($status == DEF_RECEIVE_ORDER || $status == DEF_RECEIVE_END) { // 受注確定ないし納品済の場合
                            $readOnlyDetailRow[] = $tableRow;
                        }
    
                    } else if ($orderAreaCodeList[$areaCode] === true) { // 発注の場合

                        if ($status == DEF_ORDER_ORDER || $status == DEF_ORDER_END) { // 発注確定ないし納品済の場合
                            $readOnlyDetailRow[] = $tableRow;
                        }

                    }
                }

            }
            for ($tableColumn = $tableStartColumn; $tableColumn <= $tableEndColumn; ++$tableColumn) {
                // 初期化
                $cellAddress = null;
                $style = null;
                $workSheetColumn = $tableColumn + $startColumn; // ワークシートの列番号
                // セル位置を復元する
                $cellAddress = self::combineRowAndColumnIndex($workSheetRow, self::getAlphabetForColumnIndex($workSheetColumn));

                // 列番号を数字からアルファベットに変換
                $colAlphabet = $this->getAlphabetForColumnIndex($workSheetColumn);


                $setFormatFlag = true; // フォーマットセットフラグ

                
                // 編集モード時の書式の設定
                if ($this->mode === workSheetConst::MODE_ESTIMATE_EDIT) {
                    if ($areaCode) {
                        // 各入力項目のヘッダに対する列番号リストの取得
//                        $columnNoList = $this->getColumnNumberList($areaCode);
                        if ($columnNumberList['quantity'] == $colAlphabet
                        || $columnNumberList['price'] == $colAlphabet
                        || $columnNumberList['conversionRate'] == $colAlphabet) { // 数量又は単価の場合
                            $setFormatFlag = false;
                        }
                        
                        if ($areaCode == DEF_AREA_OTHER_COST_ORDER) {
                            if ($columnNumberList['payoff'] == $colAlphabet) { // その他費用の償却（パーセント入力)
                                $setFormatFlag = 'percent';
                            }
                        }
                    } else {
                        $targetCells= workSheetConst::QUANTITY_OR_PRICE_CELLS;
                        if ($targetCells[$nameListForCellAddress[$cellAddress]] === true) { // 明細以外の数量又は単価の場合
                            $setFormatFlag = false;
                        }
                    }
                }
                if ($areaCode == DEF_AREA_OTHER_COST_ORDER) {
                    if ($columnNumberList['payoff'] == $colAlphabet) { // その他費用の償却（パーセント入力)
                        $setFormatFlag = 'percent';
                    }
                }

                // セルの値を取得する
                $getValue = $sheet->getCell($cellAddress)->getCalculatedValue();
                if (isset($getValue)) {
                    if ($setFormatFlag === true) {
                        $cellValue = trim($sheet->getCell($cellAddress)->getFormattedValue());
                    } else { 
                        // フォーマットセットフラグがfalseの時は計算値を直接与える
                        //（入力可能セルの一部について入力後にもセルの書式を反映させるため、書式をHandsontableで設定するための対応）
                        $cellValue =  $getValue;
                        if ($setFormatFlag === 'percent' && $cellValue) {
                            $cellValue = $cellValue * 100;
                        }
                    }                    
                } else {
                    $cellValue = '';
                }

                // セルのフォント情報を取得する
                $fontInfo = $this->getFontInfo($cellAddress);
      
                // セルの背景色を取得する
                if ($hiddenRowList[$workSheetRow] !== true) {
                    $backgroundColor = $this->sheet->getStyle($cellAddress)->getFill()->getStartColor()->getRGB();
                } else {
                    $backgroundColor = 'CCCCCC';
                }

                // セルの罫線情報を取得する
                $border = $this->getBorderInfo($cellAddress);

                // セルの配置情報を取得する
                $verticalPosition = $this->getVerticalPosition($cellAddress);
                $horizontalPosition = $this->getHorizontalPosition($cellAddress);

                // セル情報の配列に取得したパラメータを格納する
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

                // Html描画時に線が重複して表示されないよう罫線情報を補正する
                if ($tableRow > $tableStartRow) {
                    $beforeRow = $tableRow -1;
                    $this->setAvailableBorderInfo($cellData[$tableRow][$tableColumn]['border']['top'], $cellData[$beforeRow][$tableColumn]['border']['bottom']);
                }
                if ($tableColumn > $tableStartColumn) {
                    $beforeColumn = $tableColumn -1;
                    $this->setAvailableBorderInfo($cellData[$tableRow][$tableColumn]['border']['left'], $cellData[$tableRow][$beforeColumn]['border']['right']);
                }

                // 情報表示押下時のデータを作成する
                if ($hiddenRowList[$workSheetRow] !== true) {
                    $hiddenCellValue[$tableRow][$tableColumn] = $cellData[$tableRow][$tableColumn]['value'];
                } else {
                    $hiddenCellValue[$tableRow][$tableColumn] = null;
                }

                // クラス名の初期化
                $className = null;

                // クラス名のセット
                if ($areaCode) {
                    $className = workSheetConst::DETAIL_CLASS_STRING. ' '. workSheetConst::AREA_CLASS_STRING. $areaCode;
                    // 各入力項目のヘッダに対する列番号リストの取得
                    $columnNumberList = $this->getColumnNumberList($areaCode);

                    foreach($columnNumberList as $key => $columnNo) {
                        if ($columnNo === $colAlphabet) {
                            $className .= ' ';
                            $className .= $key;
                            break;
                        }
                    }
                } else if ($nameListForCellAddress[$cellAddress]) {
                    // セルにセル名称が存在する場合はセル名称をクラスにセットする
                    $className = $nameListForCellAddress[$cellAddress];
                }

                if ($className) {
                    // クラス情報にクラス名を追加する
                    $cellClass[] = $this->setCellClass($tableRow, $tableColumn, $className);
                }

                // readOnlyセルの設定
                if ($this->mode === workSheetConst::MODE_ESTIMATE_EDIT) {                    
                    if ($areaCode) {
                        // 列番号を数字からアルファベットに変換
                        // $colAlphabet = $this->getAlphabetForColumnIndex($workSheetColumn);
                        // foreach($columnNumberList as $key => $columnNo) {
                        //     if ($columnNo === $colAlphabet) {
                        //         break;
                        //     }
                        // }

                        // 対象エリアに応じた編集可能なセルのリストを取得 
                        $editableKeys = workSheetConst::getEditableKeys($areaCode);

                    } else if ($nameListForCellAddress[$cellAddress]) {
                        $key = $nameListForCellAddress[$cellAddress];
                        $editableKeys = workSheetConst::EDITABLE_KEY_EXPECT_FOR_TARGET_AREA;
                    }
                    // readOnlyをセットする
                    if ($editableKeys[$key] === true) {
                        $cellData[$tableRow][$tableColumn]['readOnly'] = false;
                    } else {
                        $cellData[$tableRow][$tableColumn]['readOnly'] = true;
                    }
                }
            }
        }

        // 結合セルがある場合、結合元のセルに罫線情報を付与する
        //（Handsontable対応、結合セルの最も左上の罫線情報を取得して描画する為）
        $resultSetBorder = $this->setBorderInfoForMergedCell($mergedCellsList, $cellData);
        
        // 罫線情報をCSSのborder-styleに対応させる
        for ($tableRow = $tableStartRow; $tableRow <= $tableEndRow; $tableRow++) {
            $workSheetRow = $tableRow + $startRow;
            for ($tableColumn = $tableStartColumn; $tableColumn <= $tableEndColumn; ++$tableColumn) {
                foreach($cellData[$tableRow][$tableColumn]['border'] as $position => $borderData) {
                    $cellData[$tableRow][$tableColumn]['border'][$position] += $this->setBorderForCss($borderData['excelStyle']);
                }
            }
        }

        // Rowのシフト量を非表示行に反映させる
        if ($hiddenRowList) {
            foreach ($hiddenRowList as $key => $val) {
                $viewKey = $key - $rowShiftValue;
                $viewHiddenRowList[$viewKey] = $val;
            }
        }

        // previewの場合
        if ($this->mode === workSheetConst::MODE_ESTIMATE_PREVIEW) {

            // 発注状態マスタの取得
            $receiveStatusMaster = $this->objDB->getMasterToArray('m_receivestatus', 'lngreceivestatuscode', 'strreceivestatusname');
            // 受注状態マスタの取得
            $orderStatusMaster = $this->objDB->getMasterToArray('m_orderstatus', 'lngorderstatuscode', 'strorderstatusname');

            // 追加列のデフォルト値をセットする
            $defaultCellData = workSheetConst::WORK_SHEET_CELL_DEFAULT;

            $targetAreaRows = $this->targetAreaRows;

            foreach ($targetAreaRows as $areaCode => $workSheetRows) {
                if ($areaCode === DEF_AREA_OTHER_COST_ORDER) {
                    continue;
                }
                // タイトル行の取得(対象エリア開始行の1つ前の行)
                $titleRow = $workSheetRows['firstRow'] - 1;
                // タイトル行がどの対象エリアに属するかを判定する配列の生成
                $titleRowAttribute[$titleRow] = $areaCode;
            }
            

            // 列の挿入処理
            $shiftResult = $this->shiftMergedCellsList($mergedCellsList, 0, -4); // セル位置補正
            array_unshift($columnWidth, 30, 25, 40, 32);
            $tableEndColumn += 4;

            // クラス情報の更新
            foreach ($cellClass as &$classData) {
                $classData['col'] += 4;
            }

            // 受注確定、発注確定、発注取消のチェックボックス、ボタンのセット
            $receiveConfirm = '確定';
            $orderConfirm = '確';
            $orderCancel = '消';
            $rowno = 0;
            $lastArea = 0;
            $displayButton = array();
            $titleRowList = array();
            foreach ($cellData as $tableRow => $rowData) {
                $workSheetRow = $tableRow + $rowShiftValue; // ワークシートの行番号

                $ColumnData1 = $defaultCellData;
                $ColumnData2 = $defaultCellData;
                $ColumnData3 = $defaultCellData;
                $ColumnData4 = $defaultCellData;

                $areaCode = $this->checkAttributeRow($workSheetRow);
                                
                // 編集可能な場合のみボタンを生成する
                if ($this->uneditableFlag === false) {
                    if($lastArea != $areaCode){
                        if($areaCode != DEF_AREA_OTHER_COST_ORDER){
                            $rowno = 0;
                        }
                        $lastArea = $areaCode;
                    }
                    $rowno++;


                    // ボタン生成処理
                    if ($receiveAreaCodeList[$areaCode]) {
                        
                        $htmlValue4 = "<div class=\"div_number\">";
                        $htmlValue4 .= "<span class=\"number_display\">" . $rowno . "<span>";
                        $htmlValue4 .= "</div>";
                        $ColumnData4['value'] = $htmlValue4;
                        // 受注の場合
                        $data = $inputData[$workSheetRow]['data']; // 行データの取得
                        $statusCode = $data['statusCode'];
                        $receiveNo = $data['receiveNo'];
                       
                        preg_match('/\A(\d{4})\:/', $data['customerCompany'], $customerCompany);
                        
                        if (isset($customerCompany)) {
                            $companyCode = $customerCompany[1]; // 会社コードの取得
                        } else {
                            $companyCode = '';
                        }

                        switch($statusCode) {
                            case DEF_RECEIVE_APPLICATE:
                                $name = "confirm". $areaCode;

                                if (isset($companyCode) && $companyCode !== DEF_DISPLAY_COMPANY_CODE_OTHERS) {
                                    $htmlValue1 = "<div class=\"applicate\">";
                                    $htmlValue1 .= "<input type=\"checkbox\" class=\"checkbox_applicate\" name=\"". $name. "\" value=\"".$receiveNo . "\">";
                                    $htmlValue1 .= "</div>";
    
                                    $ColumnData1['value'] = $htmlValue1;
                                    $displayButton[$areaCode] = true;
                                }


                                $mergedCellsList[] = array(
                                    'row' => $tableRow,
                                    'col' => 0,
                                    'rowspan' => 1,
                                    'colspan' => 2,
                                );
                                
                                $value3 = $receiveStatusMaster[$statusCode]['strreceivestatusname'];
                                $ColumnData3['value'] = "<div class=\"status_applicate\"><span class=\"status_display\">". $value3. "</span></div>";
                                
                                break;
                            case DEF_RECEIVE_ORDER:
                                $value = $receiveStatusMaster[$statusCode]['strreceivestatusname'];
                                $ColumnData3['value'] = "<div class=\"status_order\"><span class=\"status_display\">". $value. "</span></div>";
                                
                                break;
                            case DEF_RECEIVE_END:
                            case DEF_RECEIVE_CLOSED:
                                $value = $receiveStatusMaster[$statusCode]['strreceivestatusname'];
                                $ColumnData1['value'] = "<div class=\"receive_status_end\"><span class=\"status_display\">". $value. "</span></div>";

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
                        $htmlValue4 = "<div class=\"div_number\">";
                        $htmlValue4 .= "<span class=\"number_display\">" . $rowno . "<span>";
                        $htmlValue4 .= "</div>";
                        $ColumnData4['value'] = $htmlValue4;
                        // 発注の場合
                        $data = $inputData[$workSheetRow]['data'];
                        $statusCode = $data['statusCode'];
                        $orderNo = $data['orderNo'];

                        preg_match('/\A(\d{4})\:/', $data['customerCompany'], $customerCompany);
                        
                        if (isset($customerCompany)) {
                            $companyCode = $customerCompany[1]; // 会社コードの取得
                        } else {
                            $companyCode = '';
                        }

                        switch($statusCode) {
                            case DEF_ORDER_APPLICATE:

                                if (isset($companyCode) && $companyCode !== DEF_DISPLAY_COMPANY_CODE_OTHERS) {
                                    if( $areaCode == DEF_AREA_OTHER_COST_ORDER ){
                                        $name = "confirm". DEF_AREA_PARTS_COST_ORDER;
                                        $displayButton[DEF_AREA_PARTS_COST_ORDER] = true;
                                    }
                                    else{
                                        $name = "confirm". $areaCode;
                                        $displayButton[$areaCode] = true;
                                    }
                                    $htmlValue1 = "<div class=\"applicate\">";
                                    $htmlValue1 .= "<input type=\"checkbox\" class=\"checkbox_applicate\" name=\"". $name. "\" value=\"".$orderNo . "\">";
                                    $htmlValue1 .= "</div>";
                                    $ColumnData1['value'] = $htmlValue1;
                                }

                                $value3 = $orderStatusMaster[$statusCode]['strorderstatusname'];
                                $ColumnData3['value'] = "<div class=\"status_applicate\"><span class=\"status_display\">". $value3. "</span></div>";

                                break;
                            case DEF_ORDER_ORDER:
                                if( $areaCode == DEF_AREA_OTHER_COST_ORDER ){
                                    $name = "cancel". DEF_AREA_PARTS_COST_ORDER;
                                    $displayButton[DEF_AREA_PARTS_COST_ORDER] = true;
                                }
                                else{
                                    $name = "cancel". $areaCode;
                                    $displayButton[$areaCode] = true;
                                }
                                $htmlValue2 = "<div class=\"order\">";
                                $htmlValue2 .= "<input type=\"checkbox\" class=\"checkbox_cancel\" name=\"". $name. "\" value=\"".$orderNo . "\">";
                                $htmlValue2 .= "</div>";
                                $ColumnData2['value'] = $htmlValue2;
                                
                                $value3 = $orderStatusMaster[$statusCode]['strorderstatusname'];
                                $ColumnData3['value'] = "<div class=\"status_order\"><span class=\"status_display\">". $value3. "</span></div>";

                                break;
                            case DEF_ORDER_END:
                            case DEF_ORDER_CLOSED:
                                $value = $receiveStatusMaster[$statusCode]['strreceivestatusname'];
                                $ColumnData1['value'] = "<div class=\"order_status_end\"><span class=\"status_display\">". $value. "</span></div>";

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
        
                    } else if ($titleRowAttribute[$workSheetRow]) {
                        // 確定、取消ボタンの生成
                        $areaCode = $titleRowAttribute[$workSheetRow];
                        $titleRowList[$areaCode] = $tableRow;
                        if ($receiveAreaCodeList[$areaCode]) {
                            
                            // 受注エリアの場合
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
                            // 発注エリアの場合
                            $htmlValue1 = "<div>";
                            $htmlValue1 .= "<span><button type=\"button\" class=\"btn_confirm_order\" value=\"". $confirmValue. "\">". $orderConfirm. "</button><span>";
                            $htmlValue1 .= "</div>";
                            $htmlValue2 = "<div>";
                            $htmlValue2 .= "<button type=\"button\" class=\"btn_cancel_order\" value=\"". $cancelValue. "\">". $orderCancel. "</button>";
                            $htmlValue2 .= "</div>";
                            $ColumnData1['value'] = $htmlValue1;
                            $ColumnData2['value'] = $htmlValue2;
                        }
                    }
                }
                array_unshift($cellData[$tableRow], $ColumnData1, $ColumnData2, $ColumnData3, $ColumnData4);
            }
            // 確定・取消できる行がない場合のボタン削除
            foreach(workSheetConst::TARGET_AREA_CODE_LIST as $area){
                if(!isset($displayButton[$area]) || $displayButton[$area] == false)
                {
                    $cellData[$titleRowList[$area]][0]['value'] = '';
                    $cellData[$titleRowList[$area]][1]['value'] = '';
                }
            }
        }

        // データを配列にセットする
        $viewData = array(
            'sheetName' => $sheetName,              // シート名
            'mergedCellsList' => $mergedCellsList,  // マージされたセルのリスト
            'startRow' => $tableStartRow,           // 開始行
            'endRow' => $tableEndRow,               // 終了行
            'startColumn' => $tableStartColumn,     // 開始列
            'endColumn' => $tableEndColumn,         // 終了列
            'rowHeight' => $rowHeight,              // 行高さ
            'columnWidth' => $columnWidth,          // 列幅
            'cellData' => $cellData,                // セルのデータ(値、文字フォント、罫線、背景色)
            'cellClass' => $cellClass,
            'hiddenList' => $viewHiddenRowList,
            'hiddenRowHeight' => $hiddenRowHeight,  // 情報表示押下時用行高さ
            'hiddenCellValue' => $hiddenCellValue     // 情報表示押下時用セル入力値
        );

        // readOnlyの明細行情報が存在する場合はセットする
        if ($readOnlyDetailRow) {
            $viewData['readOnlyDetailRow'] = $readOnlyDetailRow;
        }

        // 見積原価明細番号リストが存在する場合はセットする
        if ($detailNoList) {
            $viewData['detailNoList'] = $detailNoList;
        }

        return $viewData;
    }

    // 無効行を削除する
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

        if(!is_array($hiddenList)){
            $hiddenList[] = $hiddenList;
        }

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
     * @return array $mergedCellsRowAndColumnInfo マージされたセルリスト
     */
    protected function getMergedCellsList() {
        $sheet = $this->sheet;
        // ワークシートの開始行列、終了行列（有効範囲）
        $areaStartRow = $this->startRow;
        $areaEndRow = $this->endRow;
        $areaStartColumn = $this->startColumn;
        $areaEndColumn = $this->endColumn;

        $mergedCellsList =  $sheet->getMergeCells();
        $address = array();
        foreach($mergedCellsList as $value) {
            $address = $this->getRowAndColumnStartToEnd($value);
            // マージセルの開始行列と終了行列
            $startRow = intval($address['start']['row']);
            $endRow = intval($address['end']['row']);
            $startColumn = self::getIndexForColumnAlphabet($address['start']['column']);
            $endColumn = self::getIndexForColumnAlphabet($address['end']['column']);
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
     * @return array $rowHeightPixel 各行の高さ
     */
    protected function getRowHeight() {
        $sheet = $this->sheet;
        $startRow = $this->startRow;
        $endRow = $this->endRow;
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
     * @return array $columnWidthPixel 各列の幅
     */
    protected function getColumnWidth() {
        $sheet = $this->sheet;
        $startColumn = $this->startColumn;
        $endColumn = $this->endColumn;
        // 標準のフォントを取得
        $defaultFont = $sheet->getParent()->getDefaultStyle()->getFont();
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
     * 指定したセル範囲の最初及び最後の行と列を取得する
     *
     * @param string $printArea "開始セル位置:終了セル位置"形式のstring
     *
     * @return array $cellAddress 開始セル及び終了セルの行番号と列番号
     */
    protected function getRowAndColumnStartToEnd($area) {
        $cell = array();
        $cell['start'] = strstr($area, ':', true);
        $cell['end'] = str_replace(':', '', strstr($area, ':'));
        $cellAddress = self::separateRowAndColumn($cell);
        return $cellAddress;
    }

    /**
     * セル位置から行番号と列番号を抽出する
     *
     * @param $cell 行番号と列番号を抽出するセル位置
     *
     * @return array $cellAddress セル位置に対応する行番号と列番号
     */
    public static function separateRowAndColumn($cell) {
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
     * 指定した行数と列数を移動したセル位置を返却する
     *
     * @param $cell 移動前のセル位置
     * @param $rowMove 移動する行数（下方向が＋）
     * @param $colMove 移動する列数（右方向が＋）
     *
     * @return $movedCell 移動後のセル位置
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
     * @param $cellAddress 行番号と列番号を抽出するセル位置
     *
     * @return array $borders セルの罫線情報
     */
    protected function getBorderInfo($cellAddress) {
        $borderInfoData = $this->sheet->getStyle($cellAddress)->getBorders();
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


    // テキストの水平方向の配置を取得する
    protected function getHorizontalPosition ($cellAddress) {
        $horizontalPosition = $this->sheet->getStyle($cellAddress)->getAlignment()->getHorizontal();
        return $horizontalPosition;
    }

    // テキストの垂直方向の配置を取得する
    protected function getVerticalPosition ($cellAddress) {
        $verticalPosition = $this->sheet->getStyle($cellAddress)->getAlignment()->getVertical();
        //centerの場合はcss用にmiddleに置換する
        if ($verticalPosition == 'center') {
            $verticalPosition = 'middle';
        }
        return $verticalPosition;
    }

    // セルのフォント情報を取得する
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

    // セルの太字、斜体情報を取得する
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
     * 数値化された列番号をアルファベットに変換する
     * 
     * @param $columnIndex
     * 
     * @return $column
     */
    public static function getAlphabetForColumnIndex($columnIndex) {
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
    public static function getIndexForColumnAlphabet($column) {
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
     * クラス情報をセットする
     * 
     * @param integer $row クラスをセットする行
     * @param integer $col クラスをセットする列
     * @param string  $className クラス名
     * 
     * @return クラス情報の配列
     */
    protected function setCellClass($row, $col, $className) {
        $cellClass = array(
            'row' => $row,
            'col' => $col,
            'className' => $className
        );
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

     /**
     * 結合セルの情報をシフトさせる
     * 
     * @param array   $mergedCellsList 結合セルのリスト
     * @param string  $rowShift 行のシフト量
     * @param string  $columnShift  列のシフト量
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
                $cellData[$value['row']][$value['col']]['border']['right'] = $cellData[$value['row']][$endMergedColumn]['border']['right'];
                // 結合セルの一番上の罫線情報に、結合セルの一番下の罫線情報を入れる
                $cellData[$value['row']][$value['col']]['border']['bottom'] = $cellData[$endMergedRow][$value['col']]['border']['bottom'];
            } 
        }
        return true;
    }

    /**
    * エクセルで出力されるエラーリストの生成
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
    * 対象エリアの開始行と終了行をセットする
    * 
    */
    protected function setRowRangeOfTargetArea() {
        $targetAreaList = workSheetConst::TARGET_AREA_NAME;
        foreach($targetAreaList as $areaCode => $areaName) {
            if ($areaCode !== DEF_AREA_OTHER_COST_ORDER) {
                $rows[$areaCode] = $this->getRowRangeOfTargetArea($areaCode);
//fncDebug("view.log", sprintf("area:%d, first:%d, last:%d", $areaCode, $rows[$areaCode]['firstRow'], $rows[$areaCode]['lastRow'] ), __FILE__, __LINE__, "a");
            }           
        }

        // 部材費エリアの開始行と終了行をセットする
        $firstRow = $rows[DEF_AREA_PARTS_COST_ORDER]['firstRow'];
        $lastRow = $rows[DEF_AREA_PARTS_COST_ORDER]['lastRow'];

        // 部材費エリアの仕入科目の列番号を取得する
        $subjectCellName = workSheetConst::ORDER_ELEMENTS_COST_STOCK_SUBJECT_CODE;
        $cellAddress = $this->cellAddressList[$subjectCellName];
        if (!$cellAddress) {
            return false;
        }
        $ret = self::separateRowAndColumn($cellAddress);
        $column = $ret['column'];
        
        // その他費用エリアの開始行と終了行をセットする（部材費の終了行も再セットする）
        for ($row = $firstRow; $row <= $lastRow; ++$row) {
            $cell = $column. $row;
            $backgroundColor = $this->sheet->getStyle($cell)->getFill()->getStartColor()->getRGB();
            if (isset($color)) {
                if ($backgroundColor !== $color) {
                    // 色の変化があった行をその他費用エリアの開始行とする
                    $rows[DEF_AREA_OTHER_COST_ORDER] = array(
                        'firstRow' => $row,
                        'lastRow' => $lastRow
                    );
                    // 部材費エリアの終了行を再セットする
                    $rows[DEF_AREA_PARTS_COST_ORDER]['lastRow'] = intval($row) - 1;
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
    * 対象エリアの行範囲を取得する(開始行と終了行)
    * @param integer   $areaCode  エリア区分番号
    * 
    */
    protected function getRowRangeOfTargetArea($areaCode) {
        // 対象エリアのセル名称取得
        $cellNameList = workSheetConst::getCellNameOfTargetArea($areaCode);
        $cellAddressList = $this->cellAddressList;
        // ヘッダーおよびフッター（計算結果）のセル名称を1つセットする
        $upperCellName = self::getFirstElement($cellNameList['headerList']);
        $belowCellName = self::getFirstElement($cellNameList['resultList']);
//fncDebug("view.log", sprintf("upper:%s, below:%s", $upperCellName, $belowCellName), __FILE__, __LINE__, "a");
        // セル名称から行番号を取得する
        $firstRow = $this->getRowNumberFromCellName($upperCellName) + 1;
        $lastRow = $this->getRowNumberFromCellName($belowCellName) - 1;
//fncDebug("view.log", sprintf("first:%d, last:%d", $firstRow, $lastRow), __FILE__, __LINE__, "a");
        $rows = array(
            'firstRow' => $firstRow,
            'lastRow' => $lastRow
        );
        return $rows;
    }

    /**
    * 配列の最初の要素を取得する
    * @param array   $array  要素を取得したい配列
    * 
    * @return array  配列の最初の要素
    */
    public static function getFirstElement($array){
        return current($array);
    }

    /**
    * セル名称から行番号、列番号を取得する
    * @param string   $cellName  セル名称
    *
    * @return array  行番号と列番号の情報
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
    * セル名称から行番号を取得する
    * @param string   $cellName  セル名称
    *
    * @return string  行番号
    */
    protected function getRowNumberFromCellName($cellName) {
        $rowAndColumn = $this->getRowAndColumnFromCellName($cellName);
        return $rowAndColumn['row'];
    }

    /**
    * セル名称から列番号を取得する
    * @param string   $cellName  セル名称
    *
    * @return string  列番号（アルファベット)
    */
    protected function getColumnNumberFromCellName($cellName) {
        $rowAndColumn = $this->getRowAndColumnFromCellName($cellName);
        return $rowAndColumn['column'];
    }

    /**
    * シートに見積原価情報をセットする
    * @param string   $productData  製品情報
    * @param string   $estimateData  見積原価情報
    *
    */
    public function setDBEstimateData($productData, $estimateData, $mode = null) {
        if ($mode) {
            $this->mode = $mode;
        }

        $this->templateAdjust($estimateData); // テンプレート整形
        $this->inputHeaderData($productData); // 製品情報のセット（ヘッダ部）
        $this->inputStandardRate(); // 標準割合のセット
        $this->inputEstimateDetailData($estimateData); // 見積原価明細のセット

        if ($mode === workSheetConst::MODE_ESTIMATE_DOWNLOAD) {
            $this->inputDropdownList();
            // 空のエリアの入力規則の設定
            $this->correctDataValidationForNullAreas();
        }
    }

    // ヘッダ部に値をセットする
    public function inputHeaderData($productData) {
        $cellAddressList = $this->cellAddressList;
        foreach ($productData as $key => $param) {
            if (isset($param)) {
                $cell = $cellAddressList[$key];
                $this->sheet->getCell($cell)->setValue($param);
            }
        }
    }

    // ワークシートに標準割合をセットする
    public function inputStandardRate() {
        // DBから標準割合を取得
	    $standardRate = $this->objDB->getEstimateStandardRate();
        $cellAddressList = $this->cellAddressList;
        $cell = $cellAddressList[workSheetConst::STANDARD_RATE];
        $this->sheet->getCell($cell)->setValue($standardRate);
        return;
    }

    // 明細部に値をセットする
    public function inputEstimateDetailData($estimateData) {
        $this->initMonetaryList();
        $targetAreaRows = $this->targetAreaRows;
        foreach ($estimateData as $areaCode => $data) {
            $this->inputEstimateDetailToTargetArea($areaCode, $data);
        }
        return true;
    }

    protected function initMonetaryList()
    {
        $cellAddress = $this->getRowAndColumnFromCellName(workSheetConst::JPYEN_DISPLAY);
        $moneraryUnitCellRow = $cellAddress["row"];
        $moneraryUnitCellColumn = $cellAddress["column"];
        $rowCount = 1;
        $cellAddress=$moneraryUnitCellColumn.$moneraryUnitCellRow;
        $cellAddressFrom = $cellAddress;
//fncDebug("kids2.log", $cellAddress , __FILE__, __LINE__, "a");
//fncDebug("kids2.log", is_null($this->sheet->getCell($cellAddress)) , __FILE__, __LINE__, "a");
        while(!is_null($this->sheet->getCell($cellAddress)->getValue())){
            $rowCount++;
            $cellAddress=$moneraryUnitCellColumn.($moneraryUnitCellRow+$rowCount);
//fncDebug("kids2.log", $cellAddress , __FILE__, __LINE__, "a");
        }
        
        $this->monetaryUnitFormula = "$" . $moneraryUnitCellColumn ."$" . ($moneraryUnitCellRow-1) . ":$" .$moneraryUnitCellColumn . "$" .($moneraryUnitCellRow+$rowCount-1);
//fncDebug("kids2.log", $this->monetaryUnitFormula, __FILE__, __LINE__, "a");
    }
    
    protected function correctDataValidationForNullAreas() {
        $sheet = $this->sheet;
        $targetAreaRows = $this->targetAreaRows;
        $colNum = workSheetConst::WORK_SHEET_COPY_COLUMN_NUMBER;
        foreach ($targetAreaRows as $targetArea) {
            $targetAreaRows = $this->targetAreaRows;
            $firstRow = $targetArea['firstRow'];
            $lastRow = $targetArea['lastRow'];

            $cellAddressList = $this->cellAddressList;
            // 明細行の先頭（=売上区分の1行目明細）のアドレス
            $topRowCellAddress = $cellAddressList[workSheetConst::RECEIVE_PRODUCT_SALES_DIVISION_CODE];
            $rowAndColumn = self::separateRowAndColumn($topRowCellAddress);
            $replaceFormulaRow = $rowAndColumn['row'] + 1;
            $formulaArray = array();
            for ($row = $firstRow; $row <= $lastRow; $row++) {   // 移動済みのコピー元の行まで
                if (is_null($sheet->getCell('A'.$firstRow)->getValue())) {
                    for ($col = 1; $col <= $colNum; ++$col) {
                        // 入力規則の式の複製はコピー元、コピー先にも設定しなおしが必要
                        $alphaCol = Coordinate::stringFromColumnIndex($col);
                        $copyAddress = $alphaCol.$firstRow;
                        $newAddress = $alphaCol.$row;
                        if(($sheet->getCell($copyAddress)->hasDataValidation() == true) && $sheet->getCell($copyAddress)->getDataValidation()->getType() != ""){
                            if(($sheet->getCell($copyAddress)->isMergeRangeValueCell() == true) || ($sheet->getCell($copyAddress)->isInMergeRange() == false)){
                                $orgvalidation = $sheet->getCell($copyAddress)->getDataValidation();
                                if ($formulaArray[$col] == "") {
                                    $formulaArray[$col] = $orgvalidation->getFormula1();
                                }
                                $cellPattern = '/(\$?[A-Z]+)'. $replaceFormulaRow. '(\D?)/';
                                $replace = '${1}'.$row. '${2}';
                                $newFormula = preg_replace($cellPattern, $replace, $formulaArray[$col]);
                                // 入力規則を初期化
                                $sheet->getCell($newAddress)->setDataValidation();
                                // 式を作り直した入力規則を再設定
                                $validation = new DataValidation();
                                $validation->setType($orgvalidation->getType());
                                $validation->setAllowBlank($orgvalidation->getAllowBlank());
                                $validation->setShowDropDown($orgvalidation->getShowDropDown());
                                $validation->setFormula1($newFormula);
                                $sheet->getCell($newAddress)->setDataValidation($validation);
                
                                $validation = $sheet->getCell($newAddress)->getDataValidation();
                            }
                        }
                    }                
                }
            }
        }

    }

    // 対象エリアに見積原価明細情報をセットする
    protected function inputEstimateDetailToTargetArea($areaCode, $datas) {
        $targetAreaRows = $this->targetAreaRows;
        $columnNumber = $this->getColumnNumberList($areaCode);
        $firstRow = $targetAreaRows[$areaCode]['firstRow'];
        $lastRow = $targetAreaRows[$areaCode]['lastRow'];

        $linage = (int)$lastRow - (int)$firstRow + 1;

        $inputRowCount = count($datas);

        $row = $firstRow;
        
        foreach($datas as $sorKey => $data) {
            foreach($data as $name => $value) {
                $column = $columnNumber[$name];
                if (isset($row) && isset($column)) {
                    $cell = $column. $row;
                    $this->sheet->getCell($cell)->setValue($value);
                    // パーセント入力の場合、書式を変更する
                    if ($name == 'payoff' && is_numeric($value)) {
                        $style = $this->sheet->getStyle($cell);
                        $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    }
                    // データ書き込み情報の作成
                    $inputData[$row] = array(
                        'areaCode' => $areaCode,
                        'sortKey' => $sorKey,
                        'data' => $data
                    );
                }
            }
            ++$row;
        }

        // ワークシートへのデータ書き込み情報を保持
        if ($this->inputData) {
            $this->inputData += $inputData;
        } else {
            $this->inputData = $inputData;
        }
        
        return true;
    }


    // ドロップダウンリストをワークシートオブジェクト内のセルに入力する
    protected function inputDropdownList() {
        // ドロップダウンリストの元データをDBより取得する
        $this->setDropdownList();
        // ドロップダウンリストの生成

        $this->inputInchargeDropdownList(); // 営業部署、担当

        $this->inputDevelopDropdownList(); // 開発担当者
        
        $this->inputDropdownItems();  //売上分類 or 仕入科目、売上区分 or 仕入部品、顧客先 or 仕入先

    }

    // ドロップダウンリストをオブジェクトにセットする
    protected function setDropdownList() {
        if (!$this->dropdownDSCI) {
            // 売上分類（仕入科目）、売上区分（仕入部品）
            $this->setDropdownForDivSubAndClsItm();
        }
        if (!$this->dropdownCompany) {
            // 会社
            $this->setDropdownForCompany();
        }
        if (!$this->dropdownGU) {
            // 営業部門と営業担当者
            $this->setDropdownForGroupAndUser();
        }
        if (!$this->dropdownDevUser) {
            // 開発担当者
            $this->setDropdownForDevelopUser();
        }
        /*
        if (!$this->monetaryiunit) {
            // 通貨
            $this->setDropdownForMonetaryUnit();
        }
        */
        
        
        return;
    }

    protected function setDropdownForDivSubAndClsItm() {
        $dropdown = $this->objDB->getDropdownForDivSubAndClsItm();

        // 売上分類(or仕入科目)、売上区分(or仕入部品)についてリレーションを維持して切り分ける

        foreach ($dropdown as $list) {
            $areaCode = $list->areacode;
        
            // ドロップダウンリストをエリア区分、売上分類ごとに整理
            $newList[$areaCode][$list->divisionsubject][] = $list->classitem;
        }

        $this->dropdownDSCI = $newList;
        return;
    }

    protected function setDropdownForCompany() {
        $dropdown = $this->objDB->getDropdownForCompany();
        $newCompanyList = array();
        // 会社をエリア、売上分類（仕入科目）で分類する
        foreach ($dropdown as $list) {
            $areaCode = $list->areano;
            $level1Item = $list->name;
            if( !isset($newCompanyList[$areaCode][$level1Item])){
                $newCompanyList[$areaCode][$level1Item] = array();
            }
//            $companyAttribute = $list->lngattributecode;
            $newCompanyList[$areaCode][$level1Item][] = $list->customercompany;
        }

        $this->dropdownCompany = $newCompanyList;
        return;
    }

    protected function setDropdownForGroupAndUser() {
        $this->dropdownGU = $this->objDB->getDropdownForGroupAndUser();
        return;
    }

    protected function setDropdownForDevelopUser() {
        $this->dropdownDevUser = $this->objDB->getDropdownForDevelopUser();
        return;
    }

    protected function setDropdownForMonetaryUnit() {
        $this->monetaryiunit = $this->objDB->getDropdownForMonetaryUnit();
        return;
    }

    // 営業部署及び担当のドロップダウンリストをブックにセットする
    protected function inputInchargeDropdownList() {

        $dropdownGU = $this->dropdownGU;

        // ユーザーを部署ごとに切り分ける

        $cellAddressList = $this->cellAddressList;
        $baseCellAddress = $cellAddressList[workSheetConst::PULLDOWN_MRKT_DEV];
        $lastgroup = "";
        
        $columnOffset = -1;
        foreach ($dropdownGU as $list) {
            $groupCode = $list->groupcode;
            if($lastgroup != $groupCode){
                $columnOffset++;
                $cellAddress = self::getMoveCell($baseCellAddress, 0, $columnOffset);
                $this->sheet->getCell($cellAddress)->setValue($groupCode);
                $rowOffset = 2;
                $lastgroup = $groupCode;
            }
            $usercode = $list->usercode;
            $cellAddress = self::getMoveCell($baseCellAddress, $rowOffset, $columnOffset);
            $this->sheet->getCell($cellAddress)->setValue($usercode);
            $rowOffset++;
        }
        return;
    }

    // 開発担当者のドロップダウンリストをブックにセットする
    protected function inputDevelopDropdownList() {
        $dropdownDevUser = $this->dropdownDevUser;

        $cellAddressList = $this->cellAddressList;
        $baseCellAddress = $cellAddressList[workSheetConst::PULLDOWN_DEPT_MEMBER]; // ドロップダウンリストをセットするためのセル名称(開発担当)
        $rowOffset = 1;
        foreach ($dropdownDevUser as $data) {
            $cellAddress = self::getMoveCell($baseCellAddress, $rowOffset, 0);
            $this->sheet->getCell($cellAddress)->setValue($data->usercode);
            $rowOffset++;
        }
        

        return;
    }

    // ドロップダウンアイテムをセルに設定
    protected function inputDropdownItems() {
        $cellAddressList = $this->cellAddressList;
        // 売上区分・仕入部品最大収容件数取得
        $cellAddress = $cellAddressList[workSheetConst::LEVEL2_MAX_COUNT];
        $level2Max = $this->sheet->getCell($cellAddress)->getCalculatedValue();

//fncDebug("dl.log", sprintf("address:%s, level2Max:%s", $cellAddress, $level2Max ), __FILE__, __LINE__, "a");

        
        // 会社最大収容件数取得
        $cellAddress = $cellAddressList[workSheetConst::COMPANY_MAX_COUNT];
        $companyMax = $this->sheet->getCell($cellAddress)->getCalculatedValue();
//fncDebug("dl.log", sprintf("address:%s, companyMax:%s", $cellAddress, $companyMax ), __FILE__, __LINE__, "a");
        
        // エリアセット開始セル取得
        $areaBaseAddress = $cellAddressList[workSheetConst::PULLDOWN_KEY_AREA];
        
        // 会社セット開始セル取得
        $companyBaseAddress = $cellAddressList[workSheetConst::PULLDOWN_COMPANY];

        $columnOffset = 0;
        $dropdownDSCI = $this->dropdownDSCI;
        $dropdownCompany = $this->dropdownCompany;

        // 売上分類、仕入科目
        $areaList = workSheetConst::TARGET_AREA_CODE_LIST;
        foreach($areaList as $areaCode){
            //エリアのアイテム
            $rowOffset = 0;
            $cellAddress = self::getMoveCell($areaBaseAddress, $rowOffset, $columnOffset);
//fncDebug("dl.log", sprintf("address:%s, area:%s", $cellAddress, $areaCode ), __FILE__, __LINE__, "a");
            $this->sheet->getCell($cellAddress)->setValue($areaCode);
            // エリア内1件目はエリアコードのみ設定
            $columnOffset++;
            $level1Item = $dropdownDSCI[$areaCode];
            $level1Key = array_keys($level1Item);
            foreach($level1Key as $key1 ){
                // エリアコード設定
                $cellAddress = self::getMoveCell($areaBaseAddress, $rowOffset, $columnOffset);
//fncDebug("dl.log", sprintf("address:%s, area:%s", $cellAddress, $areaCode ), __FILE__, __LINE__, "a");
                $this->sheet->getCell($cellAddress)->setValue($areaCode);
                // 次の行は売上分類・仕入科目
                $rowOffset++;
                $cellAddress = self::getMoveCell($areaBaseAddress, $rowOffset, $columnOffset);
//fncDebug("dl.log", sprintf("address:%s, level1:%s", $cellAddress, $key1 ), __FILE__, __LINE__, "a");
                $this->sheet->getCell($cellAddress)->setValue($key1);
                // 次の行以降は、売上区分・仕入部品（ただし、1件目は空白）
                $rowOffset += 2;
                foreach($level1Item[$key1] as $level2Item){
                    $cellAddress = self::getMoveCell($areaBaseAddress, $rowOffset, $columnOffset);
//fncDebug("dl.log", sprintf("address:%s, level2:%s", $cellAddress, $level2Item ), __FILE__, __LINE__, "a");
                    $this->sheet->getCell($cellAddress)->setValue($level2Item);
                    $rowOffset++;
                    if($rowOffset >= $level2Max)
                    {
                        break;
                    }
                }
//fncDebug("dl.log","set compamy", __FILE__, __LINE__, "a");
                // 会社プルダウン設定
                $rowOffset = 1;
                if(is_array($dropdownCompany[$areaCode][$key1])){
//echo sprintf("%s-%s:count=%d", $areaCode, $key1, count($dropdownCompany[$areaCode][$key1])) . "<br>";
//echo "<br>";
                    foreach($dropdownCompany[$areaCode][$key1] as $company){
                        $cellAddress = self::getMoveCell($companyBaseAddress,$rowOffset, $columnOffset);
//fncDebug("dl.log", sprintf("address:%s, company:%s", $cellAddress, $company ), __FILE__, __LINE__, "a");
                        $this->sheet->getCell($cellAddress)->setValue($company);
                        $rowOffset++;
                        if($rowOffset >= $companyMax)
                        {
                            break;
                        }
                    }
                }
                $columnOffset++;
                $rowOffset = 0;
            }
            
        }
    }

    // データの入力規則を式で指定し、セルにドロップダウンを設定する
    protected function setDataValidationForCell($cell, $fomula) {
        // 対象セルの入力規則を取得
        $validation = $this->sheet->getCell($cell)->getDataValidation();

        // パラメータのセット
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setAllowBlank(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1($fomula);

        return;
    }



    // 各項目の列番号を取得する
    protected function getColumnNumberList($areaCode) {
        
        $cellNameList = workSheetConst::getCellNameOfTargetArea($areaCode);
        $headerNameList = $cellNameList['headerList'];
        $cellAddressList = $this->cellAddressList;
        foreach ($headerNameList as $key => $name) {
            if (preg_match("/\A[A-Z]+[1-9][0-9]*\z/", $cellAddressList[$name])) {
                // セル位置の数値部分を除去する
                $columnNumber[$key] = preg_replace("/[1-9][0-9]*/", '', $cellAddressList[$name]);
            } else {
                return false;
            }
        }
        return $columnNumber;
    }

    // セルを右寄せにする
    public function setHorizontalRight($cellAddress) {
        $this->sheet->getStyle($cellAddress)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    // ワークシートヘッダ部の表示名を置換する
    public function cellValueReplace($replace) {
        $cellAddressList = $this->cellAddressList;
        
        foreach ($replace as $key => $name) {
            $cellAddress = $cellAddressList[$key];
            $this->sheet->getCell($cellAddress)->setValue($name);
        }

        return;
    }

    /**
    * 選択した行の直前に指定した数の行をコピーして挿入する
    * 
    * @param string  $selectedRow 選択行
    * @param string  $rowNum  挿入する行数
    * @param string  $colNum  コピーする列数
    * 
    * @return boolean
    */
    protected function insertCopyRowBefore($selectedRow, $rowNum, $colNum = workSheetConst::WORK_SHEET_COPY_COLUMN_NUMBER) {
//fncDebug("view.log", sprintf("selectedRow=%d, rowNum=%d", $selectedRow, $rowNum), __FILE__, __LINE__, "a");
        
        $sheet = $this->sheet;
        $sheet->insertNewRowBefore($selectedRow, $rowNum);
    
        // コピー元の行番号は挿入された行の数だけ増える
        $copyRow = $selectedRow + $rowNum;
        for ($row = 0; $row <= $rowNum; $row++) {   // 移動済みのコピー元の行まで

            $newRow = $selectedRow + $row; // 挿入された行の番号

            // セルの書式と値の複製
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
                if( $col <= workSheetConst::WORK_SHEET_COLUMN_NUMBER ){
                    // コピー元のbottomは太いため、細い線をセット
                    $sheet->getStyle($newAddress)->getBorders()->getBottom()->setBorderStyle("thin");
                }
                
                
            }
            // 行の高さ複製
            $height = $sheet->getRowDimension($copyRow)->getRowHeight();
            $sheet->getRowDimension($newRow)->setRowHeight($height);
        }
    
        // セル結合の複製
        foreach ($sheet->getMergeCells() as $mergeCell) {
            list($startCell, $endCell) = explode(":", $mergeCell);
            $colStart = preg_replace("/[0-9]*/", "", $startCell);
            $colEnd = preg_replace("/[0-9]*/", "", $endCell);
            $rowStart = ((int)preg_replace("/[A-Z]*/", "", $startCell));
            $rowEnd = ((int)preg_replace("/[A-Z]*/", "", $endCell));
    
            // 開始行と終了行が一致（行内のセル結合)かつ行番号がコピー元の行と一致する場合は結合情報を追加する
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
    * テンプレートの整形を行う（行数不足対策）
    * 
    * @param object  $spreadSheet 選択行
    * @param string  $rowNum  挿入する行数
    * @param string  $colNum  コピーする列数
    * 
    * @return boolean
    */
    protected function templateAdjust($estimateData) {

        $this->insertDeficiencyRow($estimateData); // 不足行の挿入

        $this->resettingCellAddressList(); // セル名称リストの再設定

        return;
    }

    /**
    * 入力規則の再設定
    * 
    * @param string  $from  設定開始行
    * @param string  $to  設定終了行
    * 
    * @return boolean
    */
    protected function correctDataValidation($from, $to){
        $sheet = $this->sheet;
        $colNum = workSheetConst::WORK_SHEET_COPY_COLUMN_NUMBER;
        // phpspreadsheetのバグで、テンプレートに設定された入力形式の式が
        // 常にエリア1の明細1行目の式と認識されてしまうため、
        // エリア1の明細1行目の行番号を置換対象として保持する必要がある

        $cellAddressList = $this->cellAddressList;
        // 明細行の先頭（=売上区分の1行目明細）のアドレス
        $topRowCellAddress = $cellAddressList[workSheetConst::RECEIVE_PRODUCT_SALES_DIVISION_CODE];
        $rowAndColumn = self::separateRowAndColumn($topRowCellAddress);
        $replaceFormulaRow = $rowAndColumn['row'] + 1;
        $formulaArray = array();

        for ($row = $from; $row <= $to; $row++) {   // 移動済みのコピー元の行まで
            for ($col = 1; $col <= $colNum; ++$col) {
                // 入力規則の式の複製はコピー元、コピー先にも設定しなおしが必要

                $alphaCol = Coordinate::stringFromColumnIndex($col);

                $copyAddress = $alphaCol.$from;
                $newAddress = $alphaCol.$row;
                if(($sheet->getCell($copyAddress)->hasDataValidation() == true) && $sheet->getCell($copyAddress)->getDataValidation()->getType() != ""){
                    if(($sheet->getCell($copyAddress)->isMergeRangeValueCell() == true) || ($sheet->getCell($copyAddress)->isInMergeRange() == false)){
                        $orgvalidation = $sheet->getCell($copyAddress)->getDataValidation();
                        if ($formulaArray[$col] == "") {
                            $formulaArray[$col] = $orgvalidation->getFormula1();
                        }
                        $cellPattern = '/(\$?[A-Z]+)'. $replaceFormulaRow. '(\D?)/';
                        $replace = '${1}'.$row. '${2}';
                        $newFormula = preg_replace($cellPattern, $replace, $formulaArray[$col]);
                        // 入力規則を初期化
                        $sheet->getCell($newAddress)->setDataValidation();
                        // 式を作り直した入力規則を再設定
                        $validation = new DataValidation();
                        $validation->setType($orgvalidation->getType());
                        $validation->setAllowBlank($orgvalidation->getAllowBlank());
                        $validation->setShowDropDown($orgvalidation->getShowDropDown());
                        $validation->setFormula1($newFormula);
                        $sheet->getCell($newAddress)->setDataValidation($validation);
                    }
                    else{
                        // 入力規則を初期化
                        $sheet->getCell($newAddress)->setDataValidation();
                    }
                }
                else{
                }
            }
        }
    }

    /**
    * 見積原価明細行のデータ代入時に不足する行を挿入する
    * 
    * @param string  $selectedRow 選択行
    * @param string  $rowNum  挿入する行数
    * @param string  $colNum  コピーする列数
    * 
    * @return boolean
    */
    protected function insertDeficiencyRow($estimateData) {
        $targetAreaRows = $this->targetAreaRows;

        ksort($estimateData, SORT_NUMERIC);

        $difTotal = 0;

        if ($this->mode === workSheetConst::MODE_ESTIMATE_EDIT || $this->mode === workSheetConst::MODE_ESTIMATE_DOWNLOAD) {
            $marginCell = 1; // 挿入する空行数
        } else {
            $marginCell = 0; // 挿入する空行数
        }

        $i = 0;
        foreach ($estimateData as $areaCode => $data) {
            $firstRow = $targetAreaRows[$areaCode]['firstRow'];
            $lastRow = $targetAreaRows[$areaCode]['lastRow'];
//fncDebug("view.log", sprintf("area:%d, first:%d, last:%d", $areaCode, $firstRow, $lastRow ), __FILE__, __LINE__, "a");
            if( $this->mode != workSheetConst::MODE_ESTIMATE_DOWNLOAD){
                $linage = (int)$lastRow - (int)$firstRow + 1;
            }
            else{
                $linage = 1;
            }
            $inputRowCount = count($data);    
    
            if ($linage < $inputRowCount + $marginCell) {
    
                $difference = $inputRowCount + $marginCell - $linage;
    
                $selectedRow = $firstRow + 1; // 2行目の1行前に(指定した行数を)挿入する
//fncDebug("view.log", sprintf("area:%d, selected:%d last:%d", $areaCode, $selectedRow, $lastRow), __FILE__, __LINE__, "a");
    
                $this->insertCopyRowBefore($selectedRow, $difference); // 行挿入実行

                // 各エリアの先頭行、最終行の値を追加された行数分補正
                foreach ($targetAreaRows as $key => $code) {
                    if ($key > $areaCode) {
                        $targetAreaRows[$key]['firstRow'] += $difference;
                        $targetAreaRows[$key]['lastRow'] += $difference;
                    } else if ($key === $areaCode) {
                        $targetAreaRows[$key]['lastRow'] += $difference;
                    }
//fncDebug("view.log", sprintf("area:%d, top:%d last:%d", $areaCode, $targetAreaRows[$key]['firstRow'], $targetAreaRows[$key]['lastRow']), __FILE__, __LINE__, "a");
//fncDebug("view.log", sprintf("area:%d, top:%d last:%d", $areaCode, $this->targetAreaRows[$key]['firstRow'], $this->targetAreaRows[$key]['lastRow']), __FILE__, __LINE__, "a");
                }
//fncDebug("view.log", sprintf("area:%d, selected:%d last:%d", $areaCode, $selectedRow, $lastRow), __FILE__, __LINE__, "a");

                $difTotal += $difference;
            }
            else if($linage > $inputRowCount + $marginCell){
                $difference = $linage - ($inputRowCount + $marginCell) ;
    
                $selectedRow = (int)$firstRow; // 先頭行を起点に(指定した行数を)削除する
//fncDebug("view.log", sprintf("area:%d, selected:%d last:%d", $areaCode, $selectedRow, $lastRow), __FILE__, __LINE__, "a");
    
                $this->sheet->removeRow($selectedRow, $difference); // 行挿入実行

                // 各エリアの先頭行、最終行の値を削除された行数分補正
                foreach ($targetAreaRows as $key => $code) {
                    if ($key > $areaCode) {
                        $targetAreaRows[$key]['firstRow'] -= $difference;
                        $targetAreaRows[$key]['lastRow'] -= $difference;
                    } else if ($key === $areaCode) {
                        $targetAreaRows[$key]['lastRow'] -= $difference;
                    }
//fncDebug("view.log", sprintf("area:%d, top:%d last:%d", $areaCode, $targetAreaRows[$key]['firstRow'], $targetAreaRows[$key]['lastRow']), __FILE__, __LINE__, "a");
//fncDebug("view.log", sprintf("area:%d, top:%d last:%d", $areaCode, $this->targetAreaRows[$key]['firstRow'], $this->targetAreaRows[$key]['lastRow']), __FILE__, __LINE__, "a");
                }

//fncDebug("view.log", sprintf("area:%d, selected:%d last:%d", $areaCode, $selectedRow, $lastRow), __FILE__, __LINE__, "a");
                $difTotal -= $difference;
            }
            // 入力規則の式補正
            $this->correctDataValidation($targetAreaRows[$areaCode]['firstRow'], $targetAreaRows[$areaCode]['lastRow']);
            // 条件付き書式設定
            $this->setConditionsForClassItemCell($areaCode, $targetAreaRows[$areaCode]['firstRow'], $targetAreaRows[$areaCode]['lastRow']);
            
            $i++;
            $lastDetailRow = $targetAreaRows[$areaCode]['lastRow'];
        }
        $this->endRow += $difTotal;

        $this->targetAreaRows = $targetAreaRows;

        return true;
    }

    // セル名称リストの再設定（行挿入後等、セル名称の位置を再取得する必要がある場合に使用）
    protected function resettingCellAddressList() {

        $spreadSheet = $this->sheet->getParent();
        $nameList = $this->nameList;

        foreach ($nameList as $cellName) {
            $cellAddress = $spreadSheet->getNamedRange($cellName, $this->sheet)->getRange();
            $cellAddressList[$cellName] = $cellAddress;
        }

        $this->cellAddressList = $cellAddressList;
        $this->setRowRangeOfTargetArea();
    }

    // 売上区分 or 仕入部品に条件付き書式を設定する
    //（売上分類 or 仕入科目 ⇔ 売上区分 or 仕入部品の関係チェック用）
    //（通貨とレート関係チェック用）
    protected function setConditionsForClassItemCell($areaCode, $firstRow, $lastRow) {

//fncDebug("view.log", sprintf("area:%s", $areaCode), __FILE__, __LINE__, "a");
        // 売上分類 or 仕入科目 ⇔ 売上区分 or 仕入部品の関係チェック
        $columnNumber = $this->getColumnNumberList($areaCode);

        if (!$columnNumber) {
            return false;
        }

        $targetAreaRows = $this->targetAreaRows;

//fncDebug("view.log", sprintf("area:%d, top:%d last:%d", $areaCode, $firstRow, $lastRow), __FILE__, __LINE__, "a");
        
        // 条件付き書式の設定は、workSheetConstにて定義
        // テンプレートからは条件付き書式を削除しておくこと。
        foreach(workSheetConst::CONDITION_RULE_LIST as $ruleList){
            $conditionItems = explode("|", $ruleList);
            $areaList = explode(",", strtoupper($conditionItems[0]));
            if( in_array(strtoupper($areaCode), $areaList) == true ){
                $conditional = new Conditional();
                $col = strtoupper($conditionItems[1]);        // 列番号
                $firstClsItmCell = $col. $firstRow;
                $lastClsItmCell = $col. $lastRow;
                $cellRange = $firstClsItmCell. ':'. $lastClsItmCell;
//fncDebug("view.log", sprintf("range:%s", $cellRange), __FILE__, __LINE__, "a");
//fncDebug("view.log", sprintf("Condition:%s", $conditionItems[2]), __FILE__, __LINE__, "a");
                $conditional->setConditionType($conditionItems[2]);
//fncDebug("view.log", sprintf("Operator:%s", $conditionItems[3]), __FILE__, __LINE__, "a");
                $conditional->setOperatorType($conditionItems[3]);
                $expression = strtoupper(preg_replace("/__/", $firstRow, $conditionItems[4]));    // チェックの式
//fncDebug("view.log", sprintf("expression:%s", $expression), __FILE__, __LINE__, "a");
                $conditional->addCondition($expression);

//fncDebug("view.log", sprintf("color:%s", $conditionItems[5]), __FILE__, __LINE__, "a");
                // 書式の設定
                $conditional->getStyle()->getFont()->getColor()->setARGB($conditionItems[5]); // 文字色
                $conditional->getStyle()->getFont()->setBold($conditionItems[6]); // 太字
                $conditional->getStyle()->getFont()->setItalic($conditionItems[7]); // 斜体
                $conditional->getStyle()->getFont()->setUnderline($conditionItems[8]); // 下線
//fncDebug("view.log", sprintf("fill color:%s", $conditionItems[9]), __FILE__, __LINE__, "a");
                $conditional->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getEndColor()->setARGB($conditionItems[9]); // 背景色

                $conditionalStyles = $this->sheet->getStyle($cellRange)->getConditionalStyles();
                $conditionalStyles[] = $conditional;

                // 条件付き書式をセルにセットする
                $this->sheet->getStyle($cellRange)->setConditionalStyles($conditionalStyles);
            }
        }
        return;
    }

}

?>
