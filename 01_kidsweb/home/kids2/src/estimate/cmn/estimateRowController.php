<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

// Composerのオートロードファイル読み込み
require_once ( LIB_COMPOSER_FILE );

use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
*	行ごとのデータチェッククラス
*	
*	以下のグローバル変数を定義すること
*   @param object $objDB        データベース接続オブジェクト(clsDBまたは継承クラス)
*   @param object $sheet        phpSpreadSheetのシートオブジェクト
*   
*/

abstract class estimateRowController {

    abstract protected function setDivisionSubjectCodeMaster();

    abstract protected function setNameList(); // 対象エリアごとのセル名称の指定

    protected $objDB;

    protected $errorMessage; // エラーメッセージ

    // 取り込み値
    public $columnNumberList; // 列の番号リスト
    protected $columnDisplayNameList; // 列の表示名リスト

    // セルの取得値
    public $delivery;
    public $quantity;
    public $price;
    protected $divisionSubject;
    public $classItem;
    protected $subtotal;
    public $conversionRate;
    protected $monetaryDisplay;
    public $monetary;
    public $customerCompany;
    protected $note;

    protected $row;

    // 登録用にセットする値
    public $divisionSubjectCode;
    public $classItemCode;
    public $customerCompanyCode;

    public $acquiredRate; // マスターから取得した通貨レート
    public $calculatedSubtotal; // 小計の再計算結果
    public $percentInputFlag; // パーセント入力フラグ

    public $invalidFlag;
    public $messageCode;

    protected $salesOrder;

    // static変数：処理の中で不変のデータ（マスターデータ、定数等）をセット
    // マスターデータ配列
    protected static $customerCompanyCodeMaster; // 顧客先、仕入先マスター
    protected static $divisionSubjectCodeMaster; // 売上分類、仕入科目マスター(売上区分、仕入部品結合済み）
    protected static $conversionRateMaster; // 通貨レートマスター

    // セル名称リスト
    protected static $headerNameList; // 対象エリアのヘッダー（タイトル）のセル名称
    protected static $resultNameList; // 対象エリアの計算結果のセル名称(明細最終行の次の行)


    protected function __construct($objDB) {
        // マスターデータ読み込み
        $this->objDB = $objDB;
        $this->setSalesOrder();
        $this->setCustomerCompanyCodeMaster();
        $this->setDivisionSubjectCodeMaster();
        $this->setConversionRateMaster();
    }

    // 顧客先、仕入先のマスターのデータを取得する
    protected function setCustomerCompanyCodeMaster() {
        if (!static::$customerCompanyCodeMaster) {
            $areaCode = $this->areaCode;
            $masterData = $this->objDB->getCustomerCompanyCodeList($areaCode);
            static::$customerCompanyCodeMaster = $masterData;
        }        
    }
    
    // 通貨レートマスターのデータを取得する
    protected function setConversionRateMaster() {
        if (!self::$conversionRateMaster) {
            $masterData = $this->objDB->getTemporaryRateList();
            self::$conversionRateMaster = $masterData;
        }
    }

    // 項目とチェックするマスターの対応配列の生成（名称取得用)
    // 表示用にマスター名が必要な場合、適宜定数の追加と要素の追加を行うこと
    protected function setMasterForDetailInputItems() {
        $areaCode = $this->areaCode;
        if ($this->salesOrder === DEF_ATTRIBUTE_CLIENT) {
            // 受注の場合
            $divisionSubjectTable = 'm_salesdivision'; // 売上分類マスタ
            $classItemTable = 'm_salesclass'; // 売上区分マスタ
        } else if ($this->salesOrder === DEF_ATTRIBUTE_SUPPLIER) {
            // 発注の場合
            $divisionSubjectTable = 'm_stocksubject'; // 仕入科目マスタ
            $classItemTable = 'm_stockitem'; // 仕入部品マスタ
        }
        $useMaster = array(
            'divisionSubject' => $divisionSubjectTable,
            'classItem' => $classItemTable,          
            'customerCompany' => 'm_company' // 会社マスタ
        );
        return $useMaster;
    }

    // セル名称に対応したセルのリストと行番号による初期データ作成
    public function initialize($cellAddressList, $row) {
        $this->cellAddressList = $cellAddressList;
        $this->row = $row;
        $this->setNameList();
        $this->setColumnNumberList();
        $this->setColumnDisplayNameList();
        $params = $this->getRowParams();
        $this->setRowParams($params);
        $this->setRowDataType();
        return true;
    }

    public function editInitialize ($params, $row) {
        $this->row = $row;
        $this->setNameList();
        $this->setRowParams($params);
    }

    // 各項目の列番号を取得する
    protected function setColumnNumberList() {
        $headerNameList = static::$headerNameList;
        $cellAddressList = $this->cellAddressList;
        foreach ($headerNameList as $key => $name) {
            if (preg_match("/\A[A-Z]+[1-9][0-9]*\z/", $cellAddressList[$name])) {
                // セル位置の数値部分を除去する
                $columnNumber[$key] = preg_replace("/[1-9][0-9]*/", '', $cellAddressList[$name]);
            } else {
                return false;
            }
        }
        $this->columnNumberList = $columnNumber; 
        return true;
    }

    // 各項目の名称を取得する
    protected function setColumnDisplayNameList() {
        global $sheet;
        $headerNameList = static::$headerNameList;
        $cellAddressList = $this->cellAddressList;
        foreach ($headerNameList as $key => $name) {
            $cellAddress = $cellAddressList[$name];
            $displayName[$key] = $sheet->getCell($cellAddress)->getCalculatedValue();
        }
        $this->columnDisplayNameList = $displayName;
        return true;
    }

    // 各項目のデータを取得する
    protected function getRowParams() {
        $columnNumberList = $this->columnNumberList;
        global $sheet;
        if ($columnNumberList) {
            $row = $this->row;
            foreach ($columnNumberList as $key => $column) {
                $cellAdress = $column.$row;
                if ($key == 'delivery') {
                    $param[$key] = $sheet->getCell($cellAdress)->getFormattedValue();
                } else {
                    $param[$key] = $sheet->getCell($cellAdress)->getCalculatedValue();
                }
            }
        } else {
            return false;
        }
        return $param;
    }

    // 各項目のデータ型を取得する
    protected function getRowDataType() {
        $columnNumberList = $this->columnNumberList;
        global $sheet;
        if ($columnNumberList) {
            $row = $this->row;
            foreach ($columnNumberList as $key => $column) {
                $cellAdress = $column.$row;
                $dataType[$key] = $sheet->getCell($cellAdress)->getDataType();
            }
        } else {
            return false;
        }
        return $dataType;
    }

    // 配列内のデータを各定数にセットする
    protected function setRowParams($data) {
        $this->delivery = $data['delivery'] ? $data['delivery'] : '';
        $this->quantity = $data['quantity'] ? $data['quantity'] : '';
        $this->price = $data['price'] ? $data['price'] : '';
        $this->divisionSubject = $data['divisionSubject'] ? $data['divisionSubject'] : '';
        $this->classItem = $data['classItem'] ? $data['classItem'] : '';
        $this->subtotal = $data['subtotal'] ? $data['subtotal'] : '';
        $this->conversionRate = $data['conversionRate'] ? number_format($data['conversionRate'], 6, '.', '') : '';
        $this->monetaryDisplay = $data['monetaryDisplay'] ? $data['monetaryDisplay'] : '';
        $this->monetary = $data['monetary'] ? (int)$data['monetary'] : ''; // 比較するのでint型で取得
        $this->customerCompany = $data['customerCompany'] ? $data['customerCompany'] : '';
        $this->payoff = $data['payoff'] ? $data['payoff'] : '';
        $this->note = $data['note'] ? $data['note'] : '';
        return true;
    }

    // セルのデータ形式を設定する
    public function setRowDataType() {
        $dataType = $this->getRowDataType();
        $this->dataType = $dataType;
        return true;
    }

    // 無効フラグを設定する
    public function setInvalidFlag() {
        $invalidFlag = $this->getInvalidFlag();
        $this->invalidFlag = $invalidFlag;
        return true;
    }

    // 無効フラグを取得する
    public function getInvalidFlag() {

        // 売上分類 or 仕入科目のチェック
        $this->validateDivisionSubject();

        if ($this->messageCode['divisionSubject']) {
            // 売上分類 or 仕入科目の入力が正確でない場合は表示しない
            return true;
        }

        // 売上区分、仕入部品のチェック
        $this->validateClassItem();

        if ($this->messageCode['classItem']) {
            // 売上区分 or 仕入部品の入力が正確でない場合は表示しない
            return true;
        }

        // 輸入費用、関税フラグを設定する
        $this->setDistinctionFlag();

        // 納期のチェック
        $this->validateDelivery();

        if ($this->messageCode['delivery']) {
            // 納期の入力が正確でない場合は非表示にする
            return true;
        }

        $quantity = $this->quantity; // 数量
        $price = $this->price; // 単価
        $conversionRate = $this->conversionRate; // 適用レート

        // 数量、単価、適用レートが数値型なら小計（計画原価）を計算する
        if (is_numeric($quantity) && is_numeric($price) && is_numeric($conversionRate)) {
            $calculatedSubtotal = $quantity * $price * $conversionRate;
            if ($calculatedSubtotal === 0) {
                return true;
            }
        } else {
            return true;
        }
        

        if ($this->importCostFlag || $this->tariffFlag) {
            // 入力書式を取得する
            $dataType = $this->dataType;
            if (!$dataType || $dataType['price'] === DataType::TYPE_FORMULA) {
                if (is_numeric($this->customerCompany)) {
                    $this->percentInputFlag = true;
                } else {
                    // 単価が数式の場合、顧客先に数値が入っていない場合は無効にする
                    return true;
                }
            }
        } else {
            // 顧客先のチェックを行う
            $this->validateCustomerCompany();
            if ($this->messageCode['customerCompany']) {
                // 顧客先の入力が正確でない場合非表示
                return true;
            }
        }

        // 通貨レートのチェックを行う
        $this->validateConversionRate();

        if ($this->messageCode['conversionRate'] === 9203) {
            // 通貨レートが取得できなかった場合
            return true;
        }
 
        return false;
    }



    // 単価の再計算を行う
    protected function calcuratePrice() {
        $quantity = $this->quantity;
        $subtotal = $this->subtotal;
        $acqiredRate = $this->acqiredRate;
        if($quantity || $subtotal || $acqiredRate) {
            $recalculatedPrice = $subtotal / $quantity / $acqiredRate;
        }
        return $recalculatedPrice;
    }

    // 単価の桁数調整、小計の再計算を行う
    protected function resettingPriceAndSubtotal() {
        $price = $this->price;
        $monetary = $this->monetary;

        // 通貨ごとの単価の小数点以下の桁数を取得
        $decimalDigit = workSheetConst::PRICE_DECIMAL_DIGIT;

        // 単価の小数点以下の処理
        $price = floor($price * pow(10, $decimalDigit[$monetary])) / pow(10, $decimalDigit[$monetary]);
        $price = number_format($price, 4, '.', '');

        // 再計算結果をセットする
        $this->price = $price;
        
        // 小計の計算を行う
        $this->calculateSubtotal();

        return true;
    }

    public function calculateSubtotal() {
        $quantity = $this->quantity;
        $price = $this->price;
        $acquiredRate = $this->acquiredRate;

        if(is_numeric($quantity) || is_numeric($price) || is_numeric($acquiredRate)) {
            $calculatedSubtotal = $quantity * $price;
            $calculatedSubtotalJP = $quantity * $price * $acquiredRate;
        }
        // 計算値を代入
        $this->calculatedSubtotal = $calculatedSubtotal;
        $this->calculatedSubtotalJP = $calculatedSubtotalJP;
        
        return true;
    }

    // 登録用のデータを出力する
    public function outputRegistData() {
        if (!isset($this->percentInputFlag)) {
            $this->percentInputFlag = false;
        }
        $registData = array(
            'salesOrder' => $this->salesOrder, // 受注または発注
            'delivery' => $this->delivery,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'divisionSubject' => $this->divisionSubjectCode,
            'classItem' => $this->classItemCode,
            'subtotal' => $this->calculatedSubtotal, // 再計算結果を出力
            'conversionRate' => $this->acquiredRate, // DBから取得した通貨コードを出力
            'monetary' => $this->monetary,
            'customerCompany' => $this->customerCompanyCode,
            'payoff' => $this->payoff,
            'percentInputFlag' => $this->percentInputFlag,
            'percent' => $this->percent
        );
        return $registData;
    }

    // 行番号を出力する
    public function outputRow() {
        return $this->row;
    }
    


    // エラー用に出力するパラメータをセットする
    protected function outputErrorValueList() {
        $errorValue = array(
            'delivery' => $this->delivery,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'divisionSubject' => $this->divisionSubjectCode,
            'classItem' => $this->classItemCode,
            'subtotal' => $this->subtotal,
            'conversionRate' => $this->conversionRate,
            'monetaryDisplay' => $this->monetaryDisplay,
            'monetary' => $this->monetary,
            'customerCompany' => $this->customerCompanyCode,
            'payoff' => $this->payoff
        );
        return $errorValue;
    }


    // 表示行判定に使用する項目を設定する
    protected function makeInputDataCheckListForDisplay() {
        $checkList = array(
            'price',
            'monetary',
            'delivery',
            'quantity'
        );
        return $checkList;
    }

    // エラーメッセージ出力項目から除外する項目を設定する
    protected function makeExclusionList() {
        $checkList = array(
            'price',
            'monetary',
            'delivery',
            'quantity',
            'conversionRate',
            'subtotal'
        );
        return $checkList;
    }

    // ワークシート選択時のワークシートチェックを行う
    public function workSheetSelectCheck() {
        // 無効フラグを設定する
        $this->setInvalidFlag();
        return true;
    }

    // 受注、発注を判定しセットする
    protected function setSalesOrder() {
        $areaCode = $this->areaCode;
        $salesOrderList = workSheetConst::ORDER_ATTRIBUTE_FOR_TARGET_AREA;
        if (isset($salesOrderList[DEF_ATTRIBUTE_CLIENT][$areaCode])) {
            $salesOrder = DEF_ATTRIBUTE_CLIENT;
        } else if (isset($salesOrderList[DEF_ATTRIBUTE_SUPPLIER][$areaCode])) {
            $salesOrder = DEF_ATTRIBUTE_SUPPLIER;
        }
        $this->salesOrder = $salesOrder;
        return true;
    }

    // 登録画面移行時のバリデーション処理
    public function workSheetRegistCheck() {
        $errorMessage = '';
        $this->setInvalidFlag();
        if ($this->invalidFlag === false) {
            if (!$this->importCostFlag && !$this->tariffFlag) {
                // 単価の桁数再設定と小計の再計算
                $this->resettingPriceAndSubtotal();
            }
        }
        return;
    }
    
    // 仕入科目、仕入部品による区別フラグの設定（チャージ、経費）
    protected function setDistinctionFlag() {
        $messageCodeList = $this->messageCodeList;
        $divisionSubjectCode = $this->divisionSubjectCode;
        $classItemCode = $this->classItemCode;
        
        if (!$messageCodeList['divisionSubject'] && !$messageCodeList['classItem']) {
            switch ($divisionSubjectCode) {
                case DEF_STOCK_SUBJECT_CODE_CHARGE:
                    switch ($classItemCode) {
                        case DEF_STOCK_ITEM_CODE_IMPORT_COST:
                            $this->importCostFlag = true; // 輸入費用フラグ
                            break;
                        case DEF_STOCK_ITEM_CODE_TARIFF:
                            $this->tariffFlag = true; // 関税フラグ
                            break;
                        default:
                            break;
                    }
                    $this->chargeFlag = true; // 
                    break;

                case DEF_STOCK_SUBJECT_CODE_EXPENSE:
                    $this->expenseFlag = true; // 
                    break;

                default:
                    break;
            }
        }
        return true;
    }


    // メッセージの先頭に表示する文字列を生成する：売上分類（仕入科目）＋売上区分（仕入部品）
    protected function setPrefixOfMessage() {
        $divisionSubjectPrefix = '';
        $classItemPrefix = '';
        $columnDisplayNameList = $this->$columnDisplayNameList;
        if (strlen($this->divisionSubject)) {
            $divisionSubject = $this->divisionSubject;
            $divisionSubjectDisplayName = $columnDisplayNameList['divisionSubject'];
            $divisionSubjectPrefix = $divisionSubjectDisplayName. ' ' .$divisionSubject;
        }
        if (strlen($this->classItem)) {
            $classItem = $this->classItem;
            $classItemDisplayName = $columnDisplayNameList['classItem'];
            $classItemPrefix = $classItemDisplayName. ' ' .$classItem;
        }
        $prefix = $divisionSubjectPrefix.', '. $classItemPrefix;
        return $prefix;
    }

    // 輸入費用、関税の処理
    public function chargeCalculate($conditionalTotal) {
        $monetary = $this->monetary;
        if ($monetary !== DEF_MONETARY_YEN) {
            $this->invalidFlag = true;
            return false;
        }

        $quantity = $this->quantity; // 数量

        if ($this->percentInputFlag) {
            // パーセント値の取得
            $percent = $this->customerCompany;
            // 単価の再計算
            $price = $percent * $conditionalTotal / $quantity;

            // 仕入先の出力値を空白にする
            $this->customerCompany = null;
            // パーセント値をセットする
            $this->percent = $percent;
        }

        // 通貨ごとの単価の小数点以下の桁数を取得
        $decimalDigit = workSheetConst::PRICE_DECIMAL_DIGIT;

        // 小数点第4桁以下切り捨て
        $price = floor($price * pow(10, $decimalDigit[$monetary])) / pow(10, $decimalDigit[$monetary]);
        $price = number_format($price, 4, '.', '');

        // 再計算結果で置換
        $this->price = $price;
        
        $conversionRate = $this->acquiredRate; // マスター上の通貨レート

        // 小計の再計算
        $calculatedSubtotal = $price * $conversionRate * $quantity;
        $this->calculatedSubtotal = $calculatedSubtotal;
        $this->calculatedSubtotalJP = $calculatedSubtotal;

        return true;
    }

    // 顧客先が空欄の場合、


    //----------------------------------------------------------------------------------------------------
    
    // バリデーション関数

    //----------------------------------------------------------------------------------------------------

    // 納期のバリデーション
    protected function validateDelivery() {
        $delivery = $this->delivery;
        if (isset($delivery) && $delivery !=='') {
            if (preg_match("/\A(\d{4})\/(0?[1-9]|1[0-2])\/(0?[1-9]|[12][0-9]|3[01])\z/", $delivery)) {
                list ($year, $month, $day) = explode('/', $delivery);
                if (!checkdate($month, $day, $year)) {
                    // 存在しない日付エラー
                    $this->messageCode['delivery'] = 9201;
                }
            } else {
                // 入力形式不正
                $this->messageCode['delivery'] = 9201;
            }
        } else {
            // 必須エラー
            $this->messageCode['delivery'] = 9001;
        }
        return true;
    }

    // 数量の入力値をバリデーションする
    protected function validateQuantity() {
        $quantity = $this->quantity;
        // バリデーション条件
        if (isset($quantity) && $quantity !=='') {
            if(!preg_match("/\A[1-9][0-9]*\z/", $quantity)) {
                // エラー処理
                $this->messageCode['quantity'] = 9201;
            } else if ((int)$quantity > 2147483647) {
                $this->messageCode['quantity'] = 9201;
            }
        } else {
            $this->messageCode['quantity'] = 9001; // 必須チェック
        }
        return true;
    }

    // 単価
    protected function validatePrice() {
        $price = $this->price;
        // バリデーション条件
        if (isset($price) && $price !=='') {
            if(!preg_match("/\A-?[0-9]*\.?[0-9]\z/", $price)) { // 小数点以下の桁数によって判断
                // エラー処理
                $this->messageCode['price'] = 9201;
            }
        } else {
            // エラーメッセージorエラーコード出力（必須エラー）
            $this->messageCode['price'] = 9001; // 必須
        }
        return true;
    }

    // 通貨
    protected function validateMonetary() {
        $monetary = $this->monetary;
        if (!isset($monetary) || $monetary === '') {
            // 取得できなかった場合はJPとして処理をする
            $this->monetary = DEF_MONETARY_YEN;
        }
        return true;
    }

    // 売上分類、仕入科目
    protected function validateDivisionSubject() {
        $divisionSubject = $this->divisionSubject;
        if (isset($divisionSubject) && $divisionSubject !=='') {
            // 文字列チェック
            if (preg_match("/\A[0-9]+:.+\z/", $divisionSubject)) {
                list ($divisionSubjectCode, $divisionSubjectName) = explode(':', $divisionSubject);
                $masterData = static::$divisionSubjectCodeMaster;

                $this->divisionSubjectCode = (int)$divisionSubjectCode;

                // マスターチェック
                if (!isset($masterData[(int)$divisionSubjectCode])) {
                    // マスターチェックエラー
                    $this->messageCode['divisionSubject'] = 9202;
                }

            } else {
                // 書式エラー
                $this->messageCode['divisionSubject'] = 9201;
            }
        } else {
            // 必須エラー
            $this->messageCode['divisionSubject'] = 9001;
        }
        return true;
    }

    // 売上区分、仕入部品
    protected function validateClassItem() {
        $classItem = $this->classItem;
        // バリデーション条件
        if (isset($classItem) && $classItem !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $classItem)) {
                list ($classItemCode, $classItemName) = explode(':', $classItem);
                $masterData = static::$divisionSubjectCodeMaster;
                $divisionSubjectCode = $this->divisionSubjectCode;
                $this->classItemCode = (int)$classItemCode;
                // マスターチェック
                if (!isset($masterData[$divisionSubjectCode][(int)$classItemCode])) {
                    $this->messageCode['classItem'] = 9202;
                }
            } else {
                // 入力形式不正
                $this->messageCode['classItem'] = 9201;
            }
        } else {
            // 必須エラー
            $this->messageCode['classItem'] = 9001;
        }
        return true;
    }


    // 通貨レートのチェックを行い、DBの通貨レートを出力する
    protected function validateConversionRate() {
        $conversionRate = $this->conversionRate;
        $acquiredRate = $this->getConversionRateForDelivery(); // DBから取得した通貨レート
        if ($acquiredRate) {
            $this->acquiredRate = $acquiredRate;
            if ($acquiredRate !== DEF_MONETARY_YEN  && $conversionRate !== $acquiredRate) {
                // DBから取得した通貨レートとシートから取得した通貨レートが異なる場合
                $this->messageCode['conversionRate'] = 9206;
            }
        } else {
            // DBから通貨レートが取得できなかった場合
            $this->messageCode['conversionRate'] = 9203;
        }
        return true;
    }

    // 顧客先
    protected function validateCustomerCompany() {
        $customerCompany = $this->customerCompany;
        if (isset($customerCompany) && $customerCompany !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $customerCompany)) {
                list ($customerCompanyCode, $customerCompanyName) = explode(':', $customerCompany);
                $masterData = static::$customerCompanyCodeMaster;
                $this->customerCompanyCode = (string)$customerCompanyCode;
                // マスターチェック
                if (!isset($masterData[$customerCompanyCode])) {
                    $this->messageCode['customerCompany'] = 9202;
                }
                $display = $masterData[$customerCompanyCode]['shortName'] ? $masterData[$customerCompanyCode]['shortName'] : $masterData[$customerCompanyCode]['displayName'];
                $this->customerCompany = $customerCompanyCode. ':'. $display;
            } else {
                // 入力形式不正
                $this->messageCode['customerCompany'] = 9201;
            }
        } else {
            if ($this->salesOrder === DEF_ATTRIBUTE_CLIENT) {
                // 受注の場合必須エラーを出力
                $this->messageCode['customerCompany'] = 9001;
            } else if ($this->salesOrder === DEF_ATTRIBUTE_SUPPLIER) {
                // 発注の場合は'0000'をセット
                $customerCompanyCode = (string)DEF_DISPLAY_COMPANY_CODE_OTHERS;
                $this->customerCompanyCode = $customerCompanyCode;
                $masterData = static::$customerCompanyCodeMaster;
                $display = $masterData[$customerCompanyCode]['shortName'] ? $masterData[$customerCompanyCode]['shortName'] : $masterData[$customerCompanyCode]['displayName'];
                $this->customerCompany = $customerCompanyCode. ':'. $display;
            }
        }
        return true;
    }

    // 小計のチェックを行う
    protected function validateSubtotal() {
        $subtotal = $this->subtotal;
        $acquiredRate = $this->acquiredRate; // DBから取得した通貨レート
        $quantity = $this->quantity;
        $price = $this->price;

        if ($acquiredRate && $quantity && $price) {
            $calculatedSubtotal = $quantity * $price * $acquiredRate;
        }

        if (!isset($calculatedSubtotal) || !is_numeric($calculatedSubtotal)) {
            // 数量、単価、通貨レートのチェックが行われていれば基本的には通らない処理
            $this->messageCode['subtotal'] = 9204;
            return false;
        } else {
            if ($subtotal != $calculatedSubtotal) {
                // 計算結果とシートから取得した小計（計画原価）が異なる場合
                if ($this->messageCode['conversionRate'] !== 9206) {
                    // 通貨レートの変更がなければメッセージコードをセット
                    $this->messageCode['subtotal'] = 9205;
                }
            }
            $this->calculatedSubtotal = $calculatedSubtotal;
            return true;
        }
    }
    
    // 償却
    protected function validatePayoffFlag() {
        $payoff = $this->payoff;
        // 入力形式不正
        if (!$payoff == '○' && !$payoff == '') {
            $this->messageCode['payoff'] = 9201;
        }
        return true;
    }
    
    // 対象エリアの行範囲を取得する(開始行と終了行)
    protected function getRowRangeOfTargetArea($addressListOfCellName) {
        // 対象エリアのセル名称取得
        $headerNameList = $this->headerNameList;
        $resultNameList = $this->resultNameList;
        // ヘッダーおよびフッター（計算結果）のセル名称を1つセットする
        $upperCellName = $this->getFirstElement($headerNameList);
        $belowCellName = $this->getFirstElement($resultNameList);

        // セル名称から行番号を取得する
        $upperRow = $this->getRowNumberFromCellName($addressListOfCellName, $upperCellName);
        $belowRow = $this->getRowNumberFromCellName($addressListOfCellName, $belowCellName);
        $rows = array(
            'firstRow' => $upperRow +1,
            'lastRow' => $belowRow -1,
        );
        return $rows;
    }

    // 通貨レートマスターから納期に対応する通貨レートを取得する
    protected function getConversionRateForDelivery() {
        $monetary = $this->monetary;
        $delivery = $this->delivery;
        
        if (!$monetary || !$delivery) {
            $acquiredRate = null;
        } else if ($monetary == DEF_MONETARY_YEN) {
            $acquiredRate = 1;
        } else {
            $conversionRateMaster = self::$conversionRateMaster;
            if (!$conversionRateMaster[$monetary]) {
                $acquiredRate = null;
            } else {
                foreach ($conversionRateMaster[$monetary] as $data) {
                    // 納品日に対応する通貨レートを取得する（DBから取得したリスト内の検索）
                    if (strtotime($delivery) <= strtotime($data['endDate']) 
                        && strtotime($data['startDate']) <= strtotime($delivery)) {
                        $acquiredRate = $data['conversionRate'];
                        break;
                    } else {
                        $acquiredRate = null;
                    }
                }
            }
        }
        return $acquiredRate;
    }

    // 通貨レートの差分データを作成する
    protected function makeDifferenceData() {
        $delivery = $this->delivery;
        $monetary = $this->monetary;
        $sheerConversionRate = $this->conversionRate;
        $acquiredRate = $this->acquiredRate;
        $this->difference = array(
            'delivery' => $delivery,
            'monetary' => $monetary,
            'sheetRate' => strlen($sheerConversionRate) ? number_format($sheerConversionRate, 6, '.', '') : '',
            'temporaryRate' => strlen($acquiredRate) ? number_format($acquiredRate, 6, '.', '') : ''
        );
    }
}