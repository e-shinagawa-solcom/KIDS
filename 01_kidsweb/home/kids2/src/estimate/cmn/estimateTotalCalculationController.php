<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

/**
*	ワークシートの再計算、データチェッククラス
*	
*	以下のグローバル変数を定義すること
*   @param object $objDB        データベース接続オブジェクト(clsDBまたは継承クラス)
*   @param object $sheet        phpSpreadSheetのシートオブジェクト
*   
*/

class estimateTotalCalculationController {

    protected $errorMessage; // エラーメッセージ
    protected $messageCode;

    // 出力値
    public $productionQuantity;                 // 償却数 pcs
    public $receiveProductTotalPrice;           // 製品売上合計
    public $receiveProductTotalQuantity;        // 製品数量合計
    public $receiveFixedCostTotalPrice;         // 固定費売上合計
    public $receiveFixedCostTotalQuantity;      // 固定費数量合計
    public $orderFixedCostTotalPrice;           // 固定費小計
    public $orderFixedCostNotDepreciation;      // 償却対象外合計
    public $productTotalPrice;                  // 製品売上高
    public $productProfit;                      // 製品利益
    public $productProfitRate;                  // (製品利益率)
    public $fixedCostTotalPrice;                // 固定費売上高
    public $fixedCostProfit;                    // 固定費利益
    public $fixedCostProfitRate;                // (固定費利益率)
    public $salseAmount;                        // 総売上高
    public $profit;                             // 売上総利益
    public $profitRate;                         // (利益率)
    public $indirectCost;                       // 間接製造経費
    public $standardRate;                       // 標準割合
    public $operatingProfit;                    // 営業利益
    public $operatingProfitRate;                // (営業利益率)
    public $memberQuantity;                     // 部材費（数量）
    public $memberUnitCost;                     // 部材費（単価）
    public $memberCost;                         // 部材費
    public $depreciationQuantity;               // 償却費（数量）
    public $depreciationUnitCost;               // 償却費（単価）
    public $depreciationCost;                   // 償却費
    public $manufacturingQuantity;              // 製造費用（数量）
    public $manufacturingUnitCost;              // 製造費用（単価）
    public $manufacturingCost;                  // 製造費用
    public $costNotDepreciation;                // 償却対象外固定費

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

    public function calculateParam($objRowList, $objHeader, $cellAddressList, $standardRateMaster) {
        global $sheet;
        // 償却の検索条件取得
        $cellPayoffCircle = $cellAddressList[workSheetConst::HIDDEN_PAYOFF_CIRCLE];
        $payoffCircle = $sheet->getCell($cellPayoffCircle)->getCalculatedValue();

        // 本荷の検索条件取得
        $cellMainProduct = $cellAddressList[workSheetConst::HIDDEN_MAIN_PRODUCT];
        $mainProduct = $sheet->getCell($cellMainProduct)->getCalculatedValue();

        foreach ($objRowList as $objRow) {
            if ($objRow->invalidFlag === false) {
                $areaCode = $objRow->areaCode;
                // 再計算結果がない場合は取得値を参照する
                $subtotal = $objRow->calculatedSubtotal ? $objRow->calculatedSubtotal : $objRow->subtotal;
                switch ($areaCode) {
                    case DEF_AREA_PRODUCT_SALES:
                        $quantity = $objRow->quantity;
                        $classItem = $objRow->classItem;
                        // 製品売上合計
                        $receiveProductTotalPrice += $subtotal;
                        // 製品数量合計
                        $receiveProductTotalQuantity += $quantity;
                        // 償却数
                        if ($classItem === $mainProduct) {
                            $productionQuantity += $quantity;
                        }
                        break;
                    case DEF_AREA_FIXED_COST_SALES:
                        $quantity = $objRow->quantity;
                        // 固定費売上合計
                        $receiveFixedCostTotalPrice += $subtotal;
                        // 固定費数量合計
                        $receiveFixedCostTotalQuantity += $quantity;
                        break;
                    case DEF_AREA_FIXED_COST_ORDER:
                        $payoff = $objRow->payoff;
                        // 固定費小計
                        $orderFixedCostTotalPrice += $subtotal;
                        
                        if ($payoff === $payoffCircle) {
                            // 償却費
                            $depreciationCost += $subtotal;
                        } else {
                            // 償却対象外合計
                            $orderFixedCostNotDepreciation += $subtotal;
                        }
                        break;
                    case DEF_AREA_PARTS_COST_ORDER:
                        $payoff = $objRow->payoff;
                        
                        if ($payoff === $payoffCircle) {
                            // 償却費
                            $depreciationCost += $subtotal;
                        } else {
                            // 部材費
                            $memberCost += $subtotal;
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        // その他計算
        // 製品売上高
        $productTotalPrice = $receiveProductTotalPrice;
        // 製造費用
        $manufacturingCost = $depreciationCost + $memberCost;
        // 製品利益
        $productProfit = $productTotalPrice - $manufacturingCost;
        // 製品利益率
        $productProfitRate = $productProfit / $manufacturingCost;
        // 固定費売上高
        $fixedCostTotalPrice = $receiveFixedCostTotalPrice;
        // 固定費利益
        $fixedCostProfit = $fixedCostTotalPrice - $orderFixedCostNotDepreciation;
        // 固定費利益率
        $fixedCostProfitRate = $fixedCostProfit / $fixedCostTotalPrice;
        // 総売上高
        $salesAmount = $productTotalPrice + $fixedCostTotalPrice;
        // 売上総利益
        $profit = $productProfit + $fixedCostProfit;
        // 利益率
        $profitRate = $profit / $salesAmount;
        // 標準割合
        $standardRate = $standardRateMaster;
        // 間接製造経費
        $indirectCost = floor($salesAmount * $standardRate);
        // 営業利益
        $operatingProfit = $profit - $indirectCost;
        // 営業利益率
        $operatingProfitRate = $operatingProfit / $salesAmount;
        // 部材費個数
        $memberQuantity = $productionQuantity;
        // 償却費個数
        $depreciationQuantity = $productionQuantity;
        // 製造費用個数
        $manufacturingQuantity = $productionQuantity;
        // 部材費単価
        $memberUnitCost = ($memberQuantity > 0) ? $memberCost / $memberQuantity : 0;
        // 償却費単価
        $depreciationUnitCost = ($depreciationQuantity > 0) ? $depreciationCost / $depreciationQuantity : 0;
        // 製造費用
        $manufacturingUnitCost = ($manufacturingQuantity > 0) ? $manufacturingCost / $manufacturingQuantity : 0;
        // 償却対象外固定費
        $costNotDepreciation = $orderFixedCostNotDepreciation;

        // クラスのプロパティにセット
        $this->receiveProductTotalPrice = $receiveProductTotalPrice;
        $this->receiveProductTotalQuantity = $receiveProductTotalQuantity;
        $this->productionQuantity = $productionQuantity;
        $this->receiveFixedCostTotalPrice = $receiveFixedCostTotalPrice;
        $this->receiveFixedCostTotalQuantity = $receiveFixedCostTotalQuantity;
        $this->orderFixedCostTotalPrice = $orderFixedCostTotalPrice;
        $this->depreciationCost = $depreciationCost;
        $this->orderFixedCostNotDepreciation = $orderFixedCostNotDepreciation;
        $this->memberCost = $memberCost;
        $this->productTotalPrice = $productTotalPrice;
        $this->manufacturingCost = $manufacturingCost;
        $this->productProfit = $productProfit;
        $this->productProfitRate = $productProfitRate;
        $this->fixedCostTotalPrice = $fixedCostTotalPrice;
        $this->fixedCostProfit = $fixedCostProfit;
        $this->fixedCostProfitRate = $fixedCostProfitRate;
        $this->salesAmount = $salesAmount;
        $this->profit = $profit;
        $this->profitRate = $profitRate;
        $this->standardRate = $standardRate;
        $this->indirectCost = $indirectCost;
        $this->operatingProfit = $operatingProfit;
        $this->operatingProfitRate = $operatingProfitRate;
        $this->memberQuantity = $memberQuantity;
        $this->depreciationQuantity = $depreciationQuantity;
        $this->manufacturingQuantity = $manufacturingQuantity;
        $this->memberUnitCost = $memberUnitCost;
        $this->depreciationUnitCost = $depreciationUnitCost;
        $this->manufacturingUnitCost = $manufacturingUnitCost;
        $this->costNotDepreciation = $costNotDepreciation;

        return true;
    }

    // ワークシート内のセル名称に対応したデータ（計算結果）を出力する
    public function outputParam() {
        $data = array(
            workSheetConst::RECEIVE_PRODUCT_TOTAL_PRICE => $this->receiveProductTotalPrice,
            workSheetConst::RECEIVE_PRODUCT_TOTAL_QUANTITY => $this->receiveProductTotalQuantity,
            workSheetConst::PRODUCTION_QUANTITY => $this->productionQuantity,
            workSheetConst::RECEIVE_FIXED_COST_TOTAL_PRICE => $this->receiveFixedCostTotalPrice,
            workSheetConst::RECEIVE_FIXED_COST_TOTAL_QUANTITY => $this->receiveFixedCostTotalQuantity,
            workSheetConst::ORDER_FIXED_COST_FIXED_COST => $this->orderFixedCostTotalPrice,
            workSheetConst::DEPRECIATION_COST => $this->depreciationCost,
            workSheetConst::ORDER_FIXED_COST_COST_NOT_DEPRECIATION => $this->orderFixedCostNotDepreciation,
            workSheetConst::MEMBER_COST => $this->memberCost,
            workSheetConst::PRODUCT_TOTAL_PRICE => $this->productTotalPrice,
            workSheetConst::MANUFACTURING_COST => $this->manufacturingCost,
            workSheetConst::PRODUCT_PROFIT => $this->productProfit,
            workSheetConst::PRODUCT_PROFIT_RATE => $this->productProfitRate,
            workSheetConst::FIXED_COST_TOTAL_PRICE => $this->fixedCostTotalPrice,
            workSheetConst::FIXED_COST_PROFIT => $this->fixedCostProfit,
            workSheetConst::FIXED_COST_PROFIT_RATE => $this->fixedCostProfitRate,
            workSheetConst::SALES_AMOUNT => $this->salesAmount,
            workSheetConst::PROFIT => $this->profit,
            workSheetConst::PROFIT_RATE => $this->profitRate,
            workSheetConst::STANDARD_RATE => $this->standardRate,
            workSheetConst::INDIRECT_COST => $this->indirectCost,
            workSheetConst::OPERATING_PROFIT => $this->operatingProfit,
            workSheetConst::OPERATING_PROFIT_RATE => $this->operatingProfitRate,
            workSheetConst::MEMBER_QUANTITY => $this->memberQuantity,
            workSheetConst::DEPRECIATION_QUANTITY => $this->depreciationQuantity,
            workSheetConst::MANUFACTURING_QUANTITY => $this->manufacturingQuantity,
            workSheetConst::MEMBER_UNIT_COST => $this->memberUnitCost,
            workSheetConst::DEPRECIATION_UNIT_COST => $this->depreciationUnitCost,
            workSheetConst::MANUFACTURING_UNIT_COST => $this->manufacturingUnitCost,
            workSheetConst::COST_NOT_DEPRECIATION => $this->costNotDepreciation
        );
        return $data;
    }

    // 製品コードの入力値をバリデーションする
    protected function validateProductCode() {
        $productCode = $this->productCode;
        // バリデーション条件
        if (isset($productCode) && $productCode !=='') {
            if(!preg_match("/\A[0-9]+\z/", $productCode)) {
                // エラー処理
                $this->messageCode['productCode'] = 9201;
            }
        } else {
            $this->messageCode['productCode'] = 9001; // 必須チェック
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
                $result = $objDB->checkGroupExist($inchargeGroupCodeNumber);
                // マスターチェック
                if (!$result) {
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

    // 開発担当者のチェックを行う
    protected function validateUserCode() {
        $userCode = $this->userCode;
        // バリデーション条件
        if (isset($userCode) && $userCode !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $userCode)) {
                list ($userCodeNumber, $userCodeName) = explode(':', $userCode);
                global $objDB; // グローバルのデータベースオブジェクト取得
                $result = $objDB->checkUserExist($userCodeNumber);
                // マスターチェック
                if (!$result) {
                    $this->messageCode['userCode'] = 9202;
                } else {
                    $this->userCodeNumber = $userCodeNumber; // グループコードをセットする
                }
            } else {
                // 入力形式不正
                $this->messageCode['userCode'] = 9201;
            }
        } else {
            // 必須エラー
            $this->messageCode['userCode'] = 9001;
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

    // 	if( !fncCheckGroup( $objDB, $aryProductHeader, $lngUserDispCode ) )
// 	{
// 		$strErrMsg	= "（ログイン者とExcelデータ上の部門が異なります。）";
// 		$strErrMsg	= mb_convert_encoding( $strErrMsg, "EUC-JP", "EUC-JP,UTF-8,SJIS,ASCII,JIS" );

// 		// グループが違う場合、処理終了
// 		fncOutputError( 1603, DEF_WARNING, $strErrMsg, TRUE, "", $objDB );
// 	}

}