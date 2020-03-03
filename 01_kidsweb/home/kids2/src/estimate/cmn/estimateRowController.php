<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

// Composerのオートロードファイル読み込み
require_once ( VENDOR_AUTOLOAD_FILE );

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
    public $divisionSubject;
    public $classItem;
    public $subtotal;
    public $conversionRate;
    public $monetaryDisplay;
    public $monetary;
    public $customerCompany;
    public $note;

    protected $row;

    // 登録用にセットする値
    public $divisionSubjectCode;
    public $classItemCode;
    public $customerCompanyCode;

    public $acquiredRate; // マスターから取得した通貨レート
    public $calculatedSubtotal; // 小計の再計算結果
    public $percentInputFlag; // パーセント入力フラグ

    public $invalidFlag; // 無効フラグ（エラーとはしないが、登録対象外）
    public $errorFlag;   // エラーフラグ（無視できないエラー）
    public $message;

    protected $salesOrder;

    // static変数：処理の中で不変のデータ（マスターデータ、定数等）をセット
    // マスターデータ配列
    protected static $customerCompanyCodeMaster; // 顧客先、仕入先マスター
    protected static $divisionSubjectCodeMaster; // 売上分類、仕入科目マスター(売上区分、仕入部品結合済み）
    protected static $conversionRateMaster; // 通貨レートマスター
    protected static $salesGroupAndUserMaster; // 営業グループ及びユーザマスタ
    protected static $developGroupAndUserMaster; // 開発グループおよびユーザマスタ
    protected static $groupDisplayMaster; // 表示用のグループマスタ
    protected static $userDispayMaster; // 表示用のユーザマスタ

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
        $this->setGroupAndUserMaster();
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

    // 
    protected function setGroupAndUserMaster() {
        if (!isset(self::$salesGroupAndUserMaster)
            || !isset(self::$developGroupAndUserMaster)
            || !isset(self::$groupDisplayMaster)
            || !isset(self::$userDispayMaster)) {

            $master = $this->objDB->getSalesGroupAndDevelopGroup();

            foreach($master as $record) {
                $attributeCode = $record->lngattributecode;           
                $groupDisplayCode = $record->strgroupdisplaycode;
                $groupDisplayName = $record->strgroupdisplayname;
                $userDisplayCode = $record->struserdisplaycode;
                $userDisplayName = $record->struserdisplayname;
                
                if ($attributeCode === DEF_GROUP_ATTRIBUTE_CODE_SALES_GROUP) {
                    $salesGroupAndUserMaster[$groupDisplayCode][$userDisplayCode] = true;
                } else if ($attributeCode === DEF_GROUP_ATTRIBUTE_CODE_DEVELOP_GROUP) {
                    $developGroupAndUserMaster[$groupDisplayCode][$userDisplayCode] = true;
                }
    
                if (!$groupDisplayMaster[$groupDisplayCode]) {
                    $groupDisplayMaster[$groupDisplayCode] = $groupDisplayName;
                }
    
                if ($userDispayMaster[$userDisplayCode]) {
                    $userDispayMaster[$userDisplayCode] = $userDisplayName;
                }
                
            }
    
            self::$salesGroupAndUserMaster = $salesGroupAndUserMaster;
            self::$developGroupAndUserMaster = $developGroupAndUserMaster;
    
            self::$groupDisplayMaster = $groupDisplayMaster;
            self::$userDispayMaster = $userDispayMaster;
        }

        return true;
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
        $this->delivery = isset($data['delivery']) ? $data['delivery'] : '';
        $this->quantity = isset($data['quantity']) ? $data['quantity'] : '';
        $this->price = isset($data['price']) ? $data['price'] : 0; // 単価は未入力を0とする
        $this->divisionSubject = isset($data['divisionSubject']) ? $data['divisionSubject'] : '';
        $this->classItem = isset($data['classItem']) ? $data['classItem'] : '';
        $this->subtotal = isset($data['subtotal']) ? $data['subtotal'] : ''; /// 小計は未入力を''とする
        $this->conversionRate = isset($data['conversionRate']) ? $data['conversionRate'] : '';
        $this->monetaryDisplay = isset($data['monetaryDisplay']) ? $data['monetaryDisplay'] : '';
        $this->monetary = isset($data['monetary']) ? (int)$data['monetary'] : ''; // 比較するのでint型で取得
        $this->customerCompany = isset($data['customerCompany']) ? $data['customerCompany'] : '';
        $this->payoff = isset($data['payoff']) ? $data['payoff'] : '';
        $this->note = isset($data['note']) ? $data['note'] : '';
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

    // 無効フラグ・エラーフラグを取得する
    public function getInvalidFlag() {
        // 全てのエラーメッセージを取得したい場合は$retを使用すること
        // $ret = false;

        // 金額がセットされていない場合、登録対象外として無視する。（以降のチェックはしない）
        if($this->subtotal == '')
        {
           return true;
        }

        // （プレビュー画面用）金額がセットされていない場合、登録対象外として無視する。（以降のチェックはしない）
        if($this->quantity == '' && $this->price == 0 )
        {
           return true;
        }

        // 金額がセットされている場合、無効なエラー行とする。

        // 売上分類 or 仕入科目のチェック
        $this->validateDivisionSubject();

        if ($this->message['divisionSubject']) {
            // 売上分類 or 仕入科目の入力が正確でない場合は表示しない
            $this->errorFlag = true;
            // return true;
            $ret = true;
        }

        // 売上区分、仕入部品のチェック
        $this->validateClassItem();

        if ($this->message['classItem']) {
            // 売上区分 or 仕入部品の入力が正確でない場合は表示しない
            $this->errorFlag = true;
            //return true;
            $ret = true;
        }

        // 数量のチェック
        $this->validateQuantity();

        if ($this->message['quantity']) {
            // 数量の入力が正確でない場合
            $this->errorFlag = true;
            // return true;
            $ret = true;
        }

        // 単価のバリデーション
        $this->validatePrice();

        if ($this->message['price']) {
            // 単価の入力が正確でない場合
            $this->errorFlag = true;
            //return true;
            $ret = true;
        }

        // 輸入費用、関税フラグを設定する
        $this->setDistinctionFlag();

        // 納期のチェック
        $this->validateDelivery();

        if ($this->message['delivery']) {
            // 納期の入力が正確でない場合は非表示にする
            $this->errorFlag = true;
            //return true;
            $ret = true;
        }


        if ($this->importCostFlag || $this->tariffFlag) { // 輸入費用、関税の場合、パーセント入力フラグの判定を行う
            // 入力書式を取得する
            $dataType = $this->dataType;
            if ($dataType) {
                if ($dataType['price'] === DataType::TYPE_FORMULA) {
                    if (is_numeric($this->customerCompany)) {
                        $this->percentInputFlag = true;
                    } else {
                        // 単価が数式の場合、顧客先に数値が入っていない場合は無効にする
                        //return true;
                        $ret = true;
                    }
                }
            } else {
                if (is_numeric($this->customerCompany)) {
                    $this->percentInputFlag = true;
                } else if (!is_numeric($this->price)) {
                    // 単価が数式の場合、顧客先に数値が入っていない場合は無効にする
                    //return true;
                    $ret = true;
                }
            }

        } else { // 輸入費用、関税以外の場合
            // 顧客先のチェックを行う
            $this->validateCustomerCompany();
            if ($this->message['customerCompany']) {
                // 顧客先の入力が正確でない場合非表示
                $this->errorFlag = true;
                //return true;
                $ret = true;
            }
        }

        // 通貨レートのチェックを行う
        $this->validateConversionRate();

        if ($this->message['conversionRate']) {
            // 通貨レートが正常でない場合
            $this->errorFlag = true;
            //return true;
            $ret = true;
        }

        // 輸入費用と関税以外の場合は小計を再計算し、チェックする
        if (!$this->importCostFlag && !$this->tariffFlag) {
            // 単価の小数点以下の桁数を整え、小計を再計算する
            $this->resettingPriceAndSubtotal();

            if ($this->message['subtotal']) {
                // 小計の値が正常でない場合
                $this->errorFlag = true;
                //return true;
                $ret = true;
            }
        }
 
        //return false;
        return $ret;
    }



    // 単価の桁数調整、小計の再計算を行う
    protected function resettingPriceAndSubtotal() {
        $price = $this->price;
        $monetary = $this->monetary;

        // 通貨ごとの単価の小数点以下の桁数を取得
        $decimalDigit = workSheetConst::PRICE_DECIMAL_DIGIT;

        // 文字列型に変換
        $strPrice = (string)$price;

        list($strInt, $strDecimal) = explode('.', $strPrice);

        if (strlen($strDecimal) > $decimalDigit) {
            $strDecimal = substr($strDecimal, 0, $decimalDigit);
        }

        $price = $strInt.'.'. $strDecimal;

        // 単価の小数点以下の処理
        $price = number_format($price, 4, '.', '');

        // 再計算結果をセットする
        $this->price = $price;
        
        // 小計の計算を行う
        $this->calculateSubtotal();

        // 小計のバリデーション
        $this->validateSubtotal();

        return true;
    }

    // 小計の再計算を行う
    public function calculateSubtotal() {
        $quantity = $this->quantity;
        $price = $this->price;
        $conversionRate = $this->conversionRate;

        if(is_numeric($quantity) && is_numeric($price) && is_numeric($conversionRate)) {
            $calculatedSubtotal = $quantity * $price;
            $calculatedSubtotalJP = $quantity * $price * $conversionRate;
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
            'areaCode' => $this->areaCode,
            'salesOrder' => $this->salesOrder, // 受注または発注
            'delivery' => $this->delivery,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'divisionSubject' => $this->divisionSubjectCode,
            'classItem' => $this->classItemCode,
            'subtotal' => $this->calculatedSubtotal, // 再計算結果を出力
            'conversionRate' => $this->conversionRate, // DBから取得した通貨コードを出力
            'monetary' => $this->monetary,
            'customerCompany' => $this->customerCompanyCode,
            'payoff' => $this->payoff,
            'percentInputFlag' => $this->percentInputFlag,
            'percent' => $this->percent,
            'note' => $this->note
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
            'payoff' => $this->payoff,
            'note' => $this->note
        );
        return $errorValue;
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
        $this->setInvalidFlag();
        if ($this->invalidFlag != true) {
            if (!$this->importCostFlag && !$this->tariffFlag) {
                // 単価の桁数再設定と小計の再計算
                $this->resettingPriceAndSubtotal();
            }
        }
        return;
    }
    
    // 仕入科目、仕入部品による区別フラグの設定（チャージ、経費）
    protected function setDistinctionFlag() {
        $messageList = $this->message;
        $divisionSubjectCode = $this->divisionSubjectCode;
        $classItemCode = $this->classItemCode;
        
        if (!$messageList['divisionSubject'] && !$messageList['classItem']) {
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
        } else {
            $price = $this->price;
        }

        // 通貨ごとの単価の小数点以下の桁数を取得
        $decimalDigit = workSheetConst::PRICE_DECIMAL_DIGIT;

        // 小数点第4桁以下切り捨て
        $price = floor($price * pow(10, $decimalDigit[$monetary])) / pow(10, $decimalDigit[$monetary]);
        $price = number_format($price, 4, '.', '');

        // 再計算結果で置換
        $this->price = $price;
        
        $conversionRate = $this->conversionRate; // マスター上の通貨レート

        // 小計の再計算
        $calculatedSubtotal = $price * $conversionRate * $quantity;
        $this->calculatedSubtotal = $calculatedSubtotal;
        $this->calculatedSubtotalJP = $calculatedSubtotal;

        return true;
    }

    //----------------------------------------------------------------------------------------------------
    
    // バリデーション関数

    //----------------------------------------------------------------------------------------------------

    // 納期のバリデーション
    protected function validateDelivery() {
        $key = 'delivery';
        $delivery = $this->delivery;
        if (isset($delivery) && $delivery !=='') {
            if (preg_match("/\A(\d{4})\/(0?[1-9]|1[0-2])\/(0?[1-9]|[12][0-9]|3[01])\z/", $delivery)) {
                list ($year, $month, $day) = explode('/', $delivery);
                if (!checkdate($month, $day, $year)) {
                    // 存在しない日付エラー
                    $message = DEF_MESSAGE_CODE_FORMAT_ERROR;
                }
            } else {
                // 入力形式不正
                $message = DEF_MESSAGE_CODE_FORMAT_ERROR;
            }
        } else {
            // 必須エラー
            $message = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR;
        }
        if( $message ){
            $str = array("明細部",$this->columnDisplayNameList[$key]);
            $str = array("明細部",strlen($this->columnDisplayNameList[$key]) > 0 ? $this->columnDisplayNameList[$key] : "納期");
            $this->message[$key]  = fncOutputError($message, DEF_WARNING, $str, FALSE, '', $this->objDB);
        }
        return true;
    }

    // 数量の入力値をバリデーションする
    protected function validateQuantity() {
        $key = 'quantity';
        $quantity = $this->quantity;
        // バリデーション条件
        if (isset($quantity) && $quantity !=='') {
            if(!preg_match("/\A\d+\z/", $quantity) || $quantity <= 0) {
                // 自然数でない場合はエラー処理
                $message = DEF_MESSAGE_CODE_FORMAT_ERROR;
            } else if ((int)$quantity > DEF_DB_INTEGER_MAX_LIMIT) {
                // オーバーフローする場合はエラー処理
                $message = DEF_MESSAGE_CODE_FORMAT_ERROR;
            }
        } else {
            $message = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR; // 必須チェック
        }
        if( $message ){
            $str = array("明細部",strlen($this->columnDisplayNameList[$key]) > 0 ? $this->columnDisplayNameList[$key] : "数量");
            $this->message[$key]  = fncOutputError($message, DEF_WARNING, $str, FALSE, '', $this->objDB);
        }
        return true;
    }

    // 単価の入力値をバリデーションする
    protected function validatePrice() {
        $price = $this->price;
        // バリデーション条件
        if (isset($price) && $price !=='') {
            if (!preg_match("/\A-?\d{0,14}(\.[0-9]+)?\z/", $price)) { // 整数部が14桁まで許容
                // エラー処理
                $str = array("明細部",strlen($this->columnDisplayNameList['price']) > 0 ? $this->columnDisplayNameList['price'] : "単価");
                $this->message['price']  = fncOutputError(DEF_MESSAGE_CODE_FORMAT_ERROR, DEF_WARNING, $str, FALSE, '', $this->objDB);
            }
        } else {
            // 未入力の場合は0をセットする
            $this->price = 0;
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
        $key = 'divisionSubject';
        $divisionSubject = $this->divisionSubject;
        if (isset($divisionSubject) && $divisionSubject !=='') {
            // 文字列チェック
            if (preg_match("/\A[0-9]+:.*\z/", $divisionSubject)) {
                list ($divisionSubjectCode, $divisionSubjectName) = explode(':', $divisionSubject);
                $masterData = static::$divisionSubjectCodeMaster;

                $this->divisionSubjectCode = (int)$divisionSubjectCode;

                // マスターチェック
                if (!isset($masterData[(int)$divisionSubjectCode])) {
                    // マスターチェックエラー
                    $message = DEF_MESSAGE_CODE_MASTER_CHECK_ERROR;
                }

            } else {
                // 書式エラー
                $message = DEF_MESSAGE_CODE_FORMAT_ERROR;
            }
        } else {
            // 必須エラー
            $message = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR;
        }
        if( $message ){
            $str = array("明細部",strlen($this->columnDisplayNameList[$key]) > 0 ? $this->columnDisplayNameList[$key] : "売上分類または仕入科目");
            $this->message[$key]  = fncOutputError($message, DEF_WARNING, $str, FALSE, '', $this->objDB);
        }
    }

    // 売上区分、仕入部品
    protected function validateClassItem() {
        $key = 'classItem';
        $classItem = $this->classItem;
        // バリデーション条件
        if (isset($classItem) && $classItem !=='') {
            if (preg_match("/\A[0-9]+:.*\z/", $classItem)) {
                list ($classItemCode, $classItemName) = explode(':', $classItem);
                $masterData = static::$divisionSubjectCodeMaster;
                $divisionSubjectCode = $this->divisionSubjectCode;
                $this->classItemCode = (int)$classItemCode;
                // マスターチェック
                if (!isset($masterData[$divisionSubjectCode][(int)$classItemCode])) {
                    $message = DEF_MESSAGE_CODE_MASTER_CHECK_ERROR;
                }
            } else {
                // 入力形式不正
                $message = DEF_MESSAGE_CODE_FORMAT_ERROR;
            }
        } else {
            // 必須エラー
            $message = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR;
        }
        if( $message ){
            $str = array("明細部",strlen($this->columnDisplayNameList[$key]) > 0 ? $this->columnDisplayNameList[$key] : "売上区分または仕入部品");
            $this->message[$key]  = fncOutputError($message, DEF_WARNING, $str, FALSE, '', $this->objDB);
        }
        return true;
    }


    // 通貨レートのチェックを行う
    protected function validateConversionRate() {
        $conversionRate = $this->conversionRate;
        if (!$conversionRate) {
            $str = array("明細部",strlen($this->columnDisplayNameList['conversionRate']) > 0 ? $this->columnDisplayNameList['conversionRate'] : "通貨レート");
            $this->message['conversionRate']  = fncOutputError(DEF_MESSAGE_CODE_NOT_ENTRY_ERROR, DEF_WARNING, $str, FALSE, '', $this->objDB);
            return true;
        }
        if (preg_match("/\A\d{0,15}(\.[0-9]+)?\z/", $conversionRate) && $conversionRate > 0) { // 整数部分が15ケタ以内か確認
            return true;
        } else {
            $str = array("明細部",strlen($this->columnDisplayNameList['conversionRate']) > 0 ? $this->columnDisplayNameList['conversionRate'] : "通貨レート");
            $this->message['conversionRate']  = fncOutputError(DEF_MESSAGE_CODE_FORMAT_ERROR, DEF_WARNING, $str, FALSE, '', $this->objDB);
        }

        return true;
    }

    // 顧客先
    protected function validateCustomerCompany() {
        $customerCompany = $this->customerCompany;
        if (isset($customerCompany) && $customerCompany !=='') {
            if (preg_match("/\A[0-9]+:.*\z/", $customerCompany)) {
                list ($customerCompanyCode, $customerCompanyName) = explode(':', $customerCompany);
                $masterData = static::$customerCompanyCodeMaster;
                $this->customerCompanyCode = (string)$customerCompanyCode;
                // マスターチェック
                if (!isset($masterData[$customerCompanyCode])) {
                $str = array("明細部",strlen($this->columnDisplayNameList['customerCompany']) > 0 ? $this->columnDisplayNameList['customerCompany'] : "顧客または仕入先");
                    $this->message['customerCompany']  = fncOutputError(DEF_MESSAGE_CODE_MASTER_CHECK_ERROR, DEF_WARNING, $str, FALSE, '', $this->objDB);
                }
                $display = $masterData[$customerCompanyCode]['shortName'];
                $this->customerCompany = $customerCompanyCode. ':'. $display;
            } else {
                // 入力形式不正
                $str = array("明細部",strlen($this->columnDisplayNameList['customerCompany']) > 0 ? $this->columnDisplayNameList['customerCompany'] : "顧客または仕入先");
                $this->message['customerCompany']  = fncOutputError(DEF_MESSAGE_CODE_FORMAT_ERROR, DEF_WARNING, $str, FALSE, '', $this->objDB);
            }
        } else {
            // 空欄の場合は'0000'をセット
            $customerCompanyCode = (string)DEF_DISPLAY_COMPANY_CODE_OTHERS;
            $this->customerCompanyCode = $customerCompanyCode;
            $masterData = static::$customerCompanyCodeMaster;
            $display = $masterData[$customerCompanyCode]['shortName'];
            $this->customerCompany = $customerCompanyCode. ':'. $display;
        }
        return true;
    }

    // 小計のバリデーションを行う
    protected function validateSubtotal() {
        // 現地通貨の再計算結果を取得（DBに登録する値）
        $subtotal = $this->calculatedSubtotal;

        // バリデーション条件
        if (!preg_match("/\A-?\d{0,14}(\.[0-9]+)?\z/", $subtotal)) {
            // エラー処理
            $str = array("明細部",strlen($this->columnDisplayNameList['subtotal']) > 0 ? $this->columnDisplayNameList['subtotal'] : "金額");
            $this->message['subtotal']  = fncOutputError(DEF_MESSAGE_CODE_FORMAT_ERROR, DEF_WARNING, $str, FALSE, '', $this->objDB);
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
        $sheetConversionRate = $this->conversionRate;
        $acquiredRate = $this->acquiredRate;
        $this->difference = array(
            'delivery' => $delivery,
            'monetary' => $monetary,
            'sheetRate' => strlen($sheetConversionRate) ? number_format($sheetConversionRate, 6, '.', '') : '',
            'temporaryRate' => strlen($acquiredRate) ? number_format($acquiredRate, 6, '.', '') : ''
        );
    }
}