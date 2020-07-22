<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/estimateOtherCellsController.php");
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

/**
*	フッタ部及びその他セルの再計算、データチェッククラス
*	
*	以下のグローバル変数を定義すること
*   @param object $objDB        データベース接続オブジェクト(clsDBまたは継承クラス)
*   @param object $sheet        phpSpreadSheetのシートオブジェクト
*   
*/

class registOtherCellsController extends estimateOtherCellsController {
    
    public function __construct() {
        parent::__construct();
    }

    public function calculateParam($objRowList, $objHeader, $cellAddressList, $standardRateMaster) {
        global $sheet;
        // 償却の検索条件取得
        $cellPayoffCircle = $cellAddressList[workSheetConst::HIDDEN_PAYOFF_CIRCLE];
        $payoffCircle = $sheet->getCell($cellPayoffCircle)->getCalculatedValue();


        // 集計用変数のセット
        $receiveProductTotalPrice = 0;
        $receiveProductTotalQuantity = 0;
//        $productionQuantity = 0;
        $productionQuantity = $sheet->getCell($cellAddressList[workSheetConst::PRODUCTION_QUANTITY])->getCalculatedValue();;  // "productionquantity"の値
        $receiveFixedCostTotalPrice = 0;
        $receiveFixedCostTotalQuantity = 0;
        $orderFixedCostTotalPrice = 0;
        $depreciationCost = 0;
        $orderFixedCostNotDepreciation = 0;
        $memberCost = 0;

        foreach ($objRowList as $objRow) {
            if ($objRow->invalidFlag != true) {
                $areaCode = $objRow->areaCode;
                // 再計算結果がない場合は0を代入する
                $subtotal = floor_plus(isset($objRow->calculatedSubtotalJP) ? $objRow->calculatedSubtotalJP : 0, 0);
                switch ($areaCode) {
                    case DEF_AREA_PRODUCT_SALES:
                        $quantity = $objRow->quantity;
                        $classItem = $objRow->classItem;
                        // 製品売上合計
                        $receiveProductTotalPrice += $subtotal;
                        // 製品数量合計
                        $receiveProductTotalQuantity += $quantity;
                        // 生産数
//                        if ($classItem === $mainProduct) {
//                            $productionQuantity += $quantity;
//                        }
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
                    case DEF_AREA_OTHER_COST_ORDER:
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
        $productProfitRate = $productTotalPrice ? ($productProfit / $productTotalPrice) : 0;
        // 固定費売上高
        $fixedCostTotalPrice = $receiveFixedCostTotalPrice;
        // 固定費利益
        $fixedCostProfit = $fixedCostTotalPrice - $orderFixedCostNotDepreciation;
        // 固定費利益率
        $fixedCostProfitRate = $fixedCostTotalPrice ? ($fixedCostProfit / $fixedCostTotalPrice) : 0;
        // 総売上高
        $salesAmount = $productTotalPrice + $fixedCostTotalPrice;
        // 売上総利益
        $profit = $productProfit + $fixedCostProfit;
        // 利益率
        $profitRate = $salesAmount ? ($profit / $salesAmount) : 0;
        // 標準割合
        $standardRate = $standardRateMaster;
        // 間接製造経費
        $indirectCost = floor_plus($salesAmount * $standardRate, 0);
        // 営業利益
        $operatingProfit = $profit - $indirectCost;
        // 営業利益率
        $operatingProfitRate = $salesAmount ? ($operatingProfit / $salesAmount) : 0;
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
}