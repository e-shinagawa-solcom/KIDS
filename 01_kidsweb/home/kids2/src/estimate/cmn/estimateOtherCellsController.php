<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

/**
*	フッタ部及びその他セルの再計算、データチェッククラス
*	
*	以下のグローバル変数を定義すること
*   @param object $objDB        データベース接続オブジェクト(clsDBまたは継承クラス)
*   @param object $sheet        phpSpreadSheetのシートオブジェクト
*   
*/

class estimateOtherCellsController {

    protected $errorMessage; // エラーメッセージ
    protected $messageCode;

    // 出力値
    public $productionQuantity;                 // 生産数 pcs
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

    public function __construct() {

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

}