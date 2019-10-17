<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

/**
*	ワークシートヘッダーのデータチェッククラス
*	
*   
*/

class estimateHeaderController {

    protected $errorMessage; // エラーメッセージ
    protected $messageCode;
    
    protected $loginUserCode;

    protected $objDB;

    // 取り込み値
    protected $productCode;
    protected $productName;
    protected $productEnglishName;
    protected $retailPrice;
    protected $inchargeGroupCode;
    protected $inchargeUserCode;
    protected $developUserCode;
    protected $cartonQuantity;
    protected $productionQuantity;
    protected $calculatedProductionQuantity;

    // マスターデータ配列

    protected static $groupDislayNameMaster;
    protected static $userDisplayNameMaster;
    protected static $salesGroupMaster;
    protected static $developUserMaster;

    // セル名称リスト
    protected static $nameList; // ヘッダー部入力項目のセル名称
    protected static $titleNameList; // ヘッダー部タイトル項目のセル名称

    protected $headerTitleNameList; // 入力項目のタイトルリスト

    protected $cellAddressList; // セル名称に対応したセル位置のリスト

    protected $reviseFlag; // 再販フラグ

    protected function __construct($objDB) {
        $this->objDB = $objDB;
        $this->setNameList();
        $this->setTitleNameList();
        $this->setGroupAndUserMaster();
    }

    protected function setNameList() {
        if (!self::$nameList) {
            self::$nameList = workSheetConst::WORK_SHEET_HEADER_DATA_CELL;
        }
    }

    protected function setTitleNameList() {
        if (!self::$titleNameList) {
            self::$titleNameList = workSheetConst::WORK_SHEET_HEADER_TITLE_CELL;
        }
    }

    public function outputReviseFlag() {
        return $this->reviseFlag;
    }

    // マスターセット関数
    protected function setGroupAndUserMaster() {
        if (!self::$groupDislayNameMaster
            || !self::$userDisplayNameMaster
            || !self::$salesGroupMaster
            || !self::$developUserMaster) {

           
            $datas = $this->objDB->getSalesGroupAndDevelopGroup();

            foreach ($datas as $data) {

                $attributeCode = (int)$data->lngattributecode;
                $groupDisplayCode = $data->strgroupdisplaycode;
                $groupDisplayName = $data->strgroupdisplayname;
                $userDisplayCode = $data->struserdisplaycode;
                $userDisplayName = $data->struserdisplayname;

                if (!$groupDislayNameMaster[$groupDisplayCode]) {
                    $groupDislayNameMaster[$groupDisplayCode] = $groupDisplayName;
                }

                if (!$userDisplayNameMaster[$userDisplayCode]) {
                    $userDisplayNameMaster[$userDisplayCode] = $userDisplayName;
                }
                
                if ($attributeCode === DEF_GROUP_ATTRIBUTE_CODE_SALES_GROUP) { // 営業部門の場合

                    $salesGroupMaster[$groupDisplayCode][$userDisplayCode] = true;
                    
                } else if ($attributeCode === DEF_GROUP_ATTRIBUTE_CODE_DEVELOP_GROUP) { // 開発部門の場合

                    $developUserMaster[$userDisplayCode] = true;

                }                
            }

            if (!self::$groupDislayNameMaster) {
                self::$groupDislayNameMaster = $groupDislayNameMaster;
            }

            if (!self::$userDisplayNameMaster) {
                self::$userDisplayNameMaster = $userDisplayNameMaster;
            }

            if (!self::$salesGroupMaster) {
                self::$salesGroupMaster = $salesGroupMaster;
            }

            if (!self::$developUserMaster) {
                self::$developUserMaster = $developUserMaster;
            }
        }

        return;
    }

    // 配列内のデータを各定数にセットする
    protected function setCellParams($data) {
        $this->productCode = isset($data['productCode']) ? $data['productCode'] : '';
        $this->productName = isset($data['productName']) ? $data['productName'] : '';
        $this->productEnglishName = isset($data['productEnglishName']) ? $data['productEnglishName'] : '';
        $this->retailPrice = isset($data['retailPrice']) ? $data['retailPrice'] : '';
        $this->inchargeGroupCode = isset($data['inchargeGroupCode']) ? $data['inchargeGroupCode'] : '';
        $this->inchargeUserCode = isset($data['inchargeUserCode']) ? $data['inchargeUserCode'] : '';
        $this->developUserCode = isset($data['developUserCode']) ? $data['developUserCode'] : '';
        $this->cartonQuantity = isset($data['cartonQuantity']) ? $data['cartonQuantity'] : '';
        $this->productionQuantity = isset($data['productionQuantity']) ? $data['productionQuantity'] : '';
        return true;
    }

    // 登録用のデータを出力する
    public function outputRegistData() {
        $registData = array(
            workSheetConst::PRODUCT_CODE => $this->productCode,
            workSheetConst::PRODUCT_NAME => $this->productName,
            workSheetConst::PRODUCT_ENGLISH_NAME => $this->productEnglishName,
            workSheetConst::RETAIL_PRICE => $this->retailPrice,
            workSheetConst::INCHARGE_GROUP_CODE => $this->inchargeGroupCodeNumber,
            workSheetConst::INCHARGE_USER_CODE => $this->inchargeUserCodeNumber,
            workSheetConst::DEVELOP_USER_CODE => $this->developUserCodeNumber,
            workSheetConst::CARTON_QUANTITY => $this->cartonQuantity,
            workSheetConst::PRODUCTION_QUANTITY => $this->calculatedProductionQuantity ? $this->calculatedProductionQuantity : $this->productionQuantity,
        );
        return $registData;
    }

    // 表示名と表示コードを結合したデータのリストを出力する
    public function outputDisplayData() {
        $displayData = array(
            workSheetConst::INCHARGE_GROUP_CODE => $this->inchargeGroupCode,
            workSheetConst::INCHARGE_USER_CODE => $this->inchargeUserCode,
            workSheetConst::DEVELOP_USER_CODE => $this->developUserCode,
        );

        return $displayData;
    }

    // バリデーション処理を行う
    public function validate() {
        // エラーコードをセットする関数
        $this->validateProductCode(); // 製品コード
        $this->validateProductName(); // 製品名
        $this->validateProductEnglishName(); // 製品名(英語)
        $this->validateRetailPrice(); // 上代
        $this->validateIncharge(); // 営業部署、担当
        $this->validateDevelopUserCode(); // 開発担当者
        $this->validateCartonQuantity(); // カートン入り数
        $this->validateProductionQuantity(); // 償却数
        
        $loginUserCode = $this->loginUserCode;
        $inchargeGroupCodeNumber = $this->inchargeGroupCodeNumber;
        $salesGroupMaster = self::$salesGroupMaster; // 営業部署のマスターを取得

        // ログインユーザーが営業部署に所属するかチェックする
        if (!$this->messageCode['inchargeGroupCode'] && $inchargeGroupCodeNumber) {
            $result = $this->objDB->userCodeAffiliateCheck($loginUserCode, $inchargeGroupCodeNumber);
            if (!$result) {
                $this->messageCode['loginUser'] = DEF_MESSAGE_CODE_MASTER_CHECK_ERROR;
            }
        }
        
        $messageCodeList = $this->messageCode;
        $headerTitleNameList = $this->headerTitleNameList;

        if ($messageCodeList) {
            $str = '';
            // メッセージに出力する項目をセットする
            foreach ($messageCodeList as $key => $messageCode) {
                $message = '';
                switch ($messageCode) {
                    case DEF_MESSAGE_CODE_NOT_ENTRY_ERROR:
                        $str = array(
                            "ヘッダ部",
                            mb_convert_encoding($headerTitleNameList[$key], 'EUC-JP', 'UTF-8')
                        );
                        break;
                    case DEF_MESSAGE_CODE_FORMAT_ERROR:
                        $str = array(
                            "ヘッダ部",
                            mb_convert_encoding($headerTitleNameList[$key], 'EUC-JP', 'UTF-8')
                        );
                        break;
                    case DEF_MESSAGE_CODE_MASTER_CHECK_ERROR:
                        $str = array(
                            "ヘッダ部",
                            mb_convert_encoding($headerTitleNameList[$key], 'EUC-JP', 'UTF-8'),
                        );
                        break;
                    default:
                        break;    
                }

                $message = fncOutputError($messageCode, DEF_WARNING, $str, FALSE, '', $this->objDB);

                if ($message) {
                    $errorMessage[] = $message;
                }
            }
        }
        return $errorMessage;
    }

    // エラーメッセージに渡す入力値のデータを出力する

    // エラーコードが存在するか確認する
    protected function messageCodeExist() {
        if ($this->messageCode) {
            return true;
        } else {
            return false;
        }
    }

    // 製品コードの入力値をバリデーションする
    protected function validateProductCode() {
        $productCode = $this->productCode;
        // バリデーション条件
        if (isset($productCode) && $productCode !=='') {
            if(!preg_match("/\A[0-9]{5}\z/", $productCode)) {
                // エラー処理
                $this->messageCode['productCode'] = DEF_MESSAGE_CODE_FORMAT_ERROR;
            } else {
                $record = $this->objDB->getRecordValue('m_product', 'strproductcode', $productCode);
                if ($record == false) {
                    // マスターチェックエラー
                    $this->messageCode['productCode'] = DEF_MESSAGE_CODE_PRODUCT_CODE_ERROR;
                }
                $this->reviseFlag = true;
            }
        } else {
            $this->reviseFlag = false;
        }
        return true;
    }

    // 製品名
    protected function validateProductName() {
        $productName = $this->productName;
        // バリデーション条件
        if (!isset($productName) || $productName ==='') {
            // エラーメッセージorエラーコード出力（必須エラー）
            $this->messageCode['productName'] = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR; // 必須
        }
        return true;
    }

    // 製品名（英語）
    protected function validateProductEnglishName() {
        $productEnglishName = $this->productEnglishName;
        // 文字列チェック(半角英数字記号 ASCIIの0x20~0x7e)
        if (isset($productEnglishName) && $productEnglishName !=='') {
            if(!preg_match("/\A[ -~]+\z/", $productEnglishName)) {
                // エラー処理
                $this->messageCode['productEnglishName'] = DEF_MESSAGE_CODE_FORMAT_ERROR;
            }
        } else {
            $this->messageCode['productEnglishName'] = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR; // 必須チェック
        }
        return true;
    }

    // 上代
    protected function validateRetailPrice() {
        $retailPrice = $this->retailPrice;
        if (isset($retailPrice) && $retailPrice !=='') {
            if(!is_numeric($retailPrice)) {
                // エラー処理
                $this->messageCode['retailPrice'] = DEF_MESSAGE_CODE_FORMAT_ERROR;
            } else {
                // 小数点以下第3位を四捨五入
                $formattedValue = number_format(round($retailPrice, 2), 2, '.', '');
                $this->retailPrice = $formattedValue;
            }
        } else {
            $this->messageCode['retailPrice'] = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR; // 必須チェック
        }
        return true;
    }

    // 営業部署関連のバリデーションを行う
    protected function validateIncharge() {
        $this->validateInchargeGroupCode(); // 営業部署
        $this->validateInchargeUserCode();  // 担当
        return;
    }

    // 営業部署チェック
    protected function validateInchargeGroupCode() {
        $inchargeGroupCode = $this->inchargeGroupCode;

        $salesGroupMaster = self::$salesGroupMaster; // マスターのデータを取得

        // バリデーション条件
        if (isset($inchargeGroupCode) && $inchargeGroupCode !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $inchargeGroupCode)) {
                list ($inchargeGroupCodeNumber, $inchargeGroupCodeName) = explode(':', $inchargeGroupCode);

                // $result = $this->objDB->getGroupRecordForDisplay($inchargeGroupCodeNumber);
                
                // マスターチェック
                if (!$salesGroupMaster[$inchargeGroupCodeNumber]) {
                    // 営業部署に存在しない場合
                    $this->messageCode['inchargeGroupCode'] = DEF_MESSAGE_CODE_MASTER_CHECK_ERROR;

                } else {

                    $this->inchargeGroupCodeNumber = $inchargeGroupCodeNumber; // グループコードをセットする

                    // 表示名をDB取得値に置換             
                    $groupDislayNameMaster = self::$groupDislayNameMaster;
                    $displayName = $groupDislayNameMaster[$inchargeGroupCodeNumber];
                    $this->inchargeGroupCode = $inchargeGroupCodeNumber. ':'. $displayName;

                }
            } else {
                // 入力形式不正
                $this->messageCode['inchargeGroupCode'] = DEF_MESSAGE_CODE_FORMAT_ERROR;
            }
        } else {
            // 必須エラー
            $this->messageCode['inchargeGroupCode'] = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR;
        }
        return true;
    }

    // 担当者のチェックを行う
    protected function validateInchargeUserCode() {
        $inchargeGroupCodeNumber = $this->inchargeGroupCodeNumber;

        if (!$inchargeGroupCodeNumber) {
            return false;
        }

        $inchargeUserCode = $this->inchargeUserCode;

        $salesGroupMaster = self::$salesGroupMaster; // マスターのデータを取得

        // バリデーション条件
        if (isset($inchargeUserCode) && $inchargeUserCode !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $inchargeUserCode)) {
                list ($inchargeUserCodeNumber, $inchargeUserCodeName) = explode(':', $inchargeUserCode);

                // マスターチェック
                if ($salesGroupMaster[$inchargeGroupCodeNumber][$inchargeUserCodeNumber] !== true) {
                    // 営業部署に存在しないまたはユーザーコードが存在しない場合
                    $this->messageCode['inchargeUserCode'] = DEF_MESSAGE_CODE_MASTER_CHECK_ERROR;
                } else {
                    $this->inchargeUserCodeNumber = $inchargeUserCodeNumber; // 表示上のユーザーコードをセットする

                    // 表示名をDB取得値に置換             
                    $userDisplayNameMaster = self::$userDisplayNameMaster;
                    $displayName = $userDisplayNameMaster[$inchargeUserCodeNumber];
                    $this->inchargeUserCode = $inchargeUserCodeNumber. ':'. $displayName;
                }
            } else {
                // 入力形式不正
                $this->messageCode['inchargeUserCode'] = DEF_MESSAGE_CODE_FORMAT_ERROR;
            }
        } else {
            // 必須エラー
            $this->messageCode['inchargeUserCode'] = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR;
        }
        return true;
    }

    // 開発担当者のチェックを行う
    protected function validateDevelopUserCode() {

        $developUserCode = $this->developUserCode;
        
        $developUserMaster = self::$developUserMaster;

        // バリデーション条件
        if (isset($developUserCode) && $developUserCode !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $developUserCode)) {
                list ($developUserCodeNumber, $developUserCodeName) = explode(':', $developUserCode);

                // マスターチェック
                if ($developUserMaster[$developUserCodeNumber] !== true) {
                    // 開発書に存在していないまたはユーザーコードが存在しない場合
                    $this->messageCode['developUserCode'] = DEF_MESSAGE_CODE_MASTER_CHECK_ERROR;
                } else {
                    $this->developUserCodeNumber = $developUserCodeNumber; // 表示上のユーザーコードをセットする

                    // 表示名をDB取得値に置換             
                    $userDisplayNameMaster = self::$userDisplayNameMaster;
                    $displayName = $userDisplayNameMaster[$developUserCodeNumber];
                    
                    $this->developUserCode = $developUserCodeNumber. ':'. $displayName;
                }
            } else {
                // 入力形式不正
                $this->messageCode['developUserCode'] = DEF_MESSAGE_CODE_FORMAT_ERROR;
            }
        } else {
            // 必須エラー
            $this->messageCode['developUserCode'] = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR;
        }
        return true;
    }

    // カートン入り数
    protected function validateCartonQuantity() {
        $cartonQuantity = $this->cartonQuantity;
        if (isset($cartonQuantity) && $cartonQuantity !=='') {
            if (!preg_match("/\A[1-9][0-9]*\z/", $cartonQuantity)) {
                // 入力形式不正
                $this->messageCode['cartonQuantity'] = DEF_MESSAGE_CODE_FORMAT_ERROR;
            }
        } else {
            // 必須エラー
            $this->messageCode['cartonQuantity'] = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR;
        }
        return true;
    }

    // 償却数
    protected function validateProductionQuantity() {
        $productionQuantity = $this->productionQuantity;
        if (isset($productionQuantity) && $productionQuantity !=='') {
            if (!preg_match("/\A\d+\z/", $productionQuantity)) {
                // 入力形式不正
                $this->messageCode['productionQuantity'] = DEF_MESSAGE_CODE_FORMAT_ERROR;
            }
        } else {
            // 必須エラー
            $this->messageCode['productionQuantity'] = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR;
        }
        return true;
    }

    public function setProductionQuantity($value) {
        $this->calculatedProductionQuantity = $value;
        return;
    }
}