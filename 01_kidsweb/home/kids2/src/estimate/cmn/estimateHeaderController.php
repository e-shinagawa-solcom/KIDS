<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

/**
*	ワークシートヘッダーのデータチェッククラス
*	
*	以下のグローバル変数を定義すること
*   @param object $objDB        データベース接続オブジェクト(clsDBまたは継承クラス)
*   @param object $sheet        phpSpreadSheetのシートオブジェクト
*   
*/

class estimateHeaderController {

    protected $errorMessage; // エラーメッセージ
    protected $messageCode;
    
    public $loginUserCode;

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

    // マスターデータ配列

    // セル名称リスト
    protected static $nameList; // ヘッダー部入力項目のセル名称
    protected static $titleNameList; // ヘッダー部タイトル項目のセル名称

    protected $headerTitleNameList; // 入力項目のタイトルリスト

    protected $cellAddressList; // セル名称に対応したセル位置のリスト

    public function __construct() {

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

    // セル名称に対応したセルのリストと行番号による初期データ作成
    public function initialize($cellAddressList, $loginUserCode) {
        $this->cellAddressList = $cellAddressList;
        $this->setNameList();
        $this->setTitleNameList();
        $params = $this->getCellParams();
        $this->params = $params;
        $this->setCellParams($params);
        $this->setCellTitleParams();
        $this->loginUserCode = $loginUserCode;
        return true;
    }

    // 各項目のデータを取得する
    protected function getCellParams() {
        $nameList = self::$nameList;
        $cellAddressList = $this->cellAddressList;
        global $sheet;
        if ($nameList) {
            foreach ($nameList as $key => $cellName) {
                $cellAdress = $cellAddressList[$cellName];
                $param[$key] = $sheet->getCell($cellAdress)->getCalculatedValue();
            }
        } else {
            return false;
        }
        return $param;
    }

    // 各項目のタイトル名を取得する
    protected function setCellTitleParams() {
        $nameList = self::$titleNameList;
        $cellAddressList = $this->cellAddressList;
        global $sheet;
        if ($nameList) {
            foreach ($nameList as $key => $cellName) {
                $cellAdress = $cellAddressList[$cellName];
                $param[$key] = $sheet->getCell($cellAdress)->getCalculatedValue();
            }
        } else {
            return false;
        }
        $this->headerTitleNameList = $param;
        return true;
    }

    // 配列内のデータを各定数にセットする
    protected function setCellParams($data) {
        $this->productCode = $data['productCode'] ? $data['productCode'] : '';
        $this->productName = $data['productName'] ? $data['productName'] : '';
        $this->productEnglishName = $data['productEnglishName'] ? $data['productEnglishName'] : '';
        $this->retailPrice = $data['retailPrice'] ? $data['retailPrice'] : '';
        $this->inchargeGroupCode = $data['inchargeGroupCode'] ? $data['inchargeGroupCode'] : '';
        $this->inchargeUserCode = $data['inchargeUserCode'] ? $data['inchargeUserCode'] : '';
        $this->developUserCode = $data['developUserCode'] ? $data['developUserCode'] : '';
        $this->cartonQuantity = $data['cartonQuantity'] ? $data['cartonQuantity'] : '';
        $this->productionQuantity = $data['productionQuantity'] ? $data['productionQuantity'] : '';
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
            workSheetConst::PRODUCTION_QUANTITY => $this->productionQuantity,
        );
        return $registData;
    }

    // バリデーション処理を行う
    public function validate() {
        // エラーコードをセットする関数
        $this->validateProductCode(); // 製品コード
        $this->validateProductName(); // 製品名
        $this->validateProductEnglishName(); // 製品名(英語)
        $this->validateRetailPrice(); // 上代
        $this->validateInchargeGroupCode(); // 営業部署
        $this->validateInchargeUserCode(); // 担当
        $this->validateDevelopUserCode(); // 開発担当者
        $this->validateCartonQuantity(); // カートン入り数
        $this->validateProductionQuantity(); // 償却数
        
        // グローバルのDBオブジェクトを取得する
        global $objDB;

        $loginUserCode = $this->loginUserCode;
        $inchargeGroupCodeNumber = $this->inchargeGroupCodeNumber;
        $inchargeUserCodeNumber = $this->inchargeUserCodeNumber;

        // ログインユーザーが営業部署に所属するかチェックする
        if (!$this->messageCode['inchargeGroupCode']) {
            $result = $objDB->userCodeAffiliateCheck($loginUserCode, $inchargeGroupCodeNumber);
            if (!$result) {
                $this->messageCode['loginUser'] = 9202;
            }
        }

        // 担当者が営業部署に所属するかチェックする
        if (!$this->messageCode['inchargeGroupCode'] && !$this->messageCode['inchargeUserCode']) {
            $result = $objDB->userDisplayCodeAffiliateCheck($inchargeUserCodeNumber, $inchargeGroupCodeNumber);
            if (!$result) {
                $this->messageCode['loginUser'] = 9202;
            }
        }

        if (!$this->messageCode['productCode'] && $this->productCode) {
            // 製品マスタとシートの営業部署が一致するか確認する
            $currentRecord = $objDB->getCurrentRecordForProductCode($this->productCode);
            if($currentRecord !== false) {
                $groupDisplayCode = $currentRecord->strgroupdisplaycode;
                if ($groupDisplayCode != $inchargeGroupCodeNumber) {
                    $this->messageCode['inchargeGroupCode'] = 9202;
                }                
            }
        }
        
        $messageCodeList = $this->messageCode;
        $headerTitleNameList = $this->headerTitleNameList;

        if ($messageCodeList) {
            // メッセージに出力する項目をセットする
            foreach ($messageCodeList as $key => $messageCode) {
                $message = '';
                switch ($messageCode) {
                    case 9001:
                        $str = "ヘッダ部：". mb_convert_encoding($headerTitleNameList[$key], 'EUC-JP', 'UTF-8');
                        break;
                    case 9201:
                        $str = array(
                            "ヘッダ部",
                            mb_convert_encoding($headerTitleNameList[$key], 'EUC-JP', 'UTF-8')
                        );
                        break;
                    case 9202:
                        $str = array(
                            "ヘッダ部",
                            mb_convert_encoding($headerTitleNameList[$key], 'EUC-JP', 'UTF-8'),
                            $this->params[$key],
                        );
                        break;
                    default:
                        break;    
                }

                $message = fncOutputError($messageCode, DEF_WARNING, $str, FALSE, '', $objDB);

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
                $this->messageCode['productCode'] = 9201;
            } else {
                global $objDB;
                $record = $objDB->getRecordValue('m_product', 'strproductcode', $productCode);
                if ($record == false) {
                    // マスターチェックエラー
                    $this->messageCode['productCode'] = 9202;
                }
            }
        }
        return true;
    }

    // 製品名
    protected function validateProductName() {
        $productName = $this->productName;
        // バリデーション条件
        if (!isset($productName) || $productName ==='') {
            // エラーメッセージorエラーコード出力（必須エラー）
            $this->messageCode['productName'] = 9001; // 必須
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
                $this->messageCode['productEnglishName'] = 9201;
            }
        } else {
            $this->messageCode['productEnglishName'] = 9001; // 必須チェック
        }
        return true;
    }

    // 上代
    protected function validateRetailPrice() {
        $retailPrice = $this->retailPrice;
        if (isset($retailPrice) && $retailPrice !=='') {
            if(!preg_match("/\A[0-9]+\z/", $retailPrice)) {
                // エラー処理
                $this->messageCode['retailPrice'] = 9201;
            }
        } else {
            $this->messageCode['retailPrice'] = 9001; // 必須チェック
        }
        return true;
    }

    // 営業部署チェック
    protected function validateInchargeGroupCode() {
        $inchargeGroupCode = $this->inchargeGroupCode;
        // バリデーション条件
        if (isset($inchargeGroupCode) && $inchargeGroupCode !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $inchargeGroupCode)) {
                list ($inchargeGroupCodeNumber, $inchargeGroupCodeName) = explode(':', $inchargeGroupCode);
                global $objDB; // グローバルのデータベースオブジェクト取得
                $result = $objDB->getGroupRecordForDisplay($inchargeGroupCodeNumber);
                // マスターチェック
                if (!$result) {
                    // レコードが取得できなかった場合
                    $this->messageCode['inchargeGroupCode'] = 9202;
                } else {
                    $this->inchargeGroupCodeNumber = $inchargeGroupCodeNumber; // グループコードをセットする
                }
            } else {
                // 入力形式不正
                $this->messageCode['inchargeGroupCode'] = 9201;
            }
        } else {
            // 必須エラー
            $this->messageCode['inchargeGroupCode'] = 9001;
        }
        return true;
    }

    // 担当者のチェックを行う
    protected function validateInchargeUserCode() {
        $inchargeUserCode = $this->inchargeUserCode;
        // バリデーション条件
        if (isset($inchargeUserCode) && $inchargeUserCode !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $inchargeUserCode)) {
                list ($inchargeUserCodeNumber, $inchargeUserCodeName) = explode(':', $inchargeUserCode);
                global $objDB; // グローバルのデータベースオブジェクト取得
                $result = $objDB->getUserRecordForDisplay($inchargeUserCodeNumber);
                // マスターチェック
                if (!$result) {
                    // レコードが取得できなかった場合
                    $this->messageCode['inchargeUserCode'] = 9202;
                } else {
                    $this->inchargeUserCodeNumber = $inchargeUserCodeNumber; // 表示上のユーザーコードをセットする
                }
            } else {
                // 入力形式不正
                $this->messageCode['inchargeUserCode'] = 9201;
            }
        } else {
            // 必須エラー
            $this->messageCode['inchargeUserCode'] = 9001;
        }
        return true;
    }

    // 開発担当者のチェックを行う
    protected function validateDevelopUserCode() {
        $developUserCode = $this->developUserCode;
        // バリデーション条件
        if (isset($developUserCode) && $developUserCode !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $developUserCode)) {
                list ($developUserCodeNumber, $developUserCodeName) = explode(':', $developUserCode);
                global $objDB; // グローバルのデータベースオブジェクト取得
                $result = $objDB->getUserRecordForDisplay($developUserCodeNumber);
                // マスターチェック
                if (!$result) {
                    // レコードが取得できなかった場合
                    $this->messageCode['developUserCode'] = 9202;
                } else {
                    $this->developUserCodeNumber = $developUserCodeNumber; // 表示上のユーザーコードをセットする
                }
            } else {
                // 入力形式不正
                $this->messageCode['developUserCode'] = 9201;
            }
        } else {
            // 必須エラー
            $this->messageCode['developUserCode'] = 9001;
        }
        return true;
    }

    // カートン入り数
    protected function validateCartonQuantity() {
        $cartonQuantity = $this->cartonQuantity;
        if (isset($cartonQuantity) && $cartonQuantity !=='') {
            if (!preg_match("/\A[1-9][0-9]*\z/", $cartonQuantity)) {
                // 入力形式不正
                $this->messageCode['cartonQuantity'] = 9201;
            }
        } else {
            // 必須エラー
            $this->messageCode['cartonQuantity'] = 9001;
        }
        return true;
    }

    // 償却数
    protected function validateProductionQuantity() {
        $productionQuantity = $this->productionQuantity;
        if (isset($productionQuantity) && $productionQuantity !=='') {
            if (!preg_match("/\A[1-9][0-9]*\z/", $productionQuantity)) {
                // 入力形式不正
                $this->messageCode['productionQuantity'] = 9201;
            }
        } else {
            // 必須エラー
            $this->messageCode['productionQuantity'] = 9001;
        }
        return true;
    }
}