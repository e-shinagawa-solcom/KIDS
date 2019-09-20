<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/estimateOtherCellsController.php");
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

/**
*	¥Õ¥Ã¥¿ÉôµÚ¤Ó¤½¤ÎÂ¾¥»¥ë¤ÎºÆ·×»»¡¢¥Ç¡¼¥¿¥Á¥§¥Ã¥¯¥¯¥é¥¹
*	
*	°Ê²¼¤Î¥°¥í¡¼¥Ð¥ëÊÑ¿ô¤òÄêµÁ¤¹¤ë¤³¤È
*   @param object $objDB        ¥Ç¡¼¥¿¥Ù¡¼¥¹ÀÜÂ³¥ª¥Ö¥¸¥§¥¯¥È(clsDB¤Þ¤¿¤Ï·Ñ¾µ¥¯¥é¥¹)
*   @param object $sheet        phpSpreadSheet¤Î¥·¡¼¥È¥ª¥Ö¥¸¥§¥¯¥È
*   
*/

class registOtherCellsController extends estimateOtherCellsController {
    
    public function __construct() {
        parent::__construct();
    }

    public function calculateParam($objRowList, $objHeader, $cellAddressList, $standardRateMaster) {
        global $sheet;
        // ½þµÑ¤Î¸¡º÷¾ò·ï¼èÆÀ
        $cellPayoffCircle = $cellAddressList[workSheetConst::HIDDEN_PAYOFF_CIRCLE];
        $payoffCircle = $sheet->getCell($cellPayoffCircle)->getCalculatedValue();

        // ËÜ²Ù¤Î¸¡º÷¾ò·ï¼èÆÀ
        $cellMainProduct = $cellAddressList[workSheetConst::HIDDEN_MAIN_PRODUCT];
        $mainProduct = $sheet->getCell($cellMainProduct)->getCalculatedValue();

        foreach ($objRowList as $objRow) {
            if ($objRow->invalidFlag === false) {
                $areaCode = $objRow->areaCode;
                // ºÆ·×»»·ë²Ì¤¬¤Ê¤¤¾ì¹ç¤Ï¼èÆÀÃÍ¤ò»²¾È¤¹¤ë
                $subtotal = $objRow->calculatedSubtotalJP ? $objRow->calculatedSubtotalJP : $objRow->subtotal;
                switch ($areaCode) {
                    case DEF_AREA_PRODUCT_SALES:
                        $quantity = $objRow->quantity;
                        $classItem = $objRow->classItem;
                        // À½ÉÊÇä¾å¹ç·×
                        $receiveProductTotalPrice += $subtotal;
                        // À½ÉÊ¿ôÎÌ¹ç·×
                        $receiveProductTotalQuantity += $quantity;
                        // ½þµÑ¿ô
                        if ($classItem === $mainProduct) {
                            $productionQuantity += $quantity;
                        }
                        break;
                    case DEF_AREA_FIXED_COST_SALES:
                        $quantity = $objRow->quantity;
                        // ¸ÇÄêÈñÇä¾å¹ç·×
                        $receiveFixedCostTotalPrice += $subtotal;
                        // ¸ÇÄêÈñ¿ôÎÌ¹ç·×
                        $receiveFixedCostTotalQuantity += $quantity;
                        break;
                    case DEF_AREA_FIXED_COST_ORDER:
                        $payoff = $objRow->payoff;
                        // ¸ÇÄêÈñ¾®·×
                        $orderFixedCostTotalPrice += $subtotal;
                        
                        if ($payoff === $payoffCircle) {
                            // ½þµÑÈñ
                            $depreciationCost += $subtotal;
                        } else {
                            // ½þµÑÂÐ¾Ý³°¹ç·×
                            $orderFixedCostNotDepreciation += $subtotal;
                        }
                        break;
                    case DEF_AREA_PARTS_COST_ORDER:
                        $payoff = $objRow->payoff;
                        
                        if ($payoff === $payoffCircle) {
                            // ½þµÑÈñ
                            $depreciationCost += $subtotal;
                        } else {
                            // ÉôºàÈñ
                            $memberCost += $subtotal;
                        }
                        break;
                    case DEF_AREA_OTHER_COST_ORDER:
                        $payoff = $objRow->payoff;
                        
                        if ($payoff === $payoffCircle) {
                            // ½þµÑÈñ
                            $depreciationCost += $subtotal;
                        } else {
                            // ÉôºàÈñ
                            $memberCost += $subtotal;
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        // ¤½¤ÎÂ¾·×»»
        // À½ÉÊÇä¾å¹â
        $productTotalPrice = $receiveProductTotalPrice;
        // À½Â¤ÈñÍÑ
        $manufacturingCost = $depreciationCost + $memberCost;
        // À½ÉÊÍø±×
        $productProfit = $productTotalPrice - $manufacturingCost;
        // À½ÉÊÍø±×Î¨
        $productProfitRate = $manufacturingCost ? ($productProfit / $manufacturingCost) : '';
        // ¸ÇÄêÈñÇä¾å¹â
        $fixedCostTotalPrice = $receiveFixedCostTotalPrice;
        // ¸ÇÄêÈñÍø±×
        $fixedCostProfit = $fixedCostTotalPrice - $orderFixedCostNotDepreciation;
        // ¸ÇÄêÈñÍø±×Î¨
        $fixedCostProfitRate = $fixedCostTotalPrice ? ($fixedCostProfit / $fixedCostTotalPrice) : '';
        // ÁíÇä¾å¹â
        $salesAmount = $productTotalPrice + $fixedCostTotalPrice;
        // Çä¾åÁíÍø±×
        $profit = $productProfit + $fixedCostProfit;
        // Íø±×Î¨
        $profitRate = $salesAmount ? ($profit / $salesAmount) : '';
        // É¸½à³ä¹ç
        $standardRate = $standardRateMaster;
        // ´ÖÀÜÀ½Â¤·ÐÈñ
        $indirectCost = floor($salesAmount * $standardRate);
        // ±Ä¶ÈÍø±×
        $operatingProfit = $profit - $indirectCost;
        // ±Ä¶ÈÍø±×Î¨
        $operatingProfitRate = $salesAmount ? ($operatingProfit / $salesAmount) : '';
        // ÉôºàÈñ¸Ä¿ô
        $memberQuantity = $productionQuantity;
        // ½þµÑÈñ¸Ä¿ô
        $depreciationQuantity = $productionQuantity;
        // À½Â¤ÈñÍÑ¸Ä¿ô
        $manufacturingQuantity = $productionQuantity;
        // ÉôºàÈñÃ±²Á
        $memberUnitCost = ($memberQuantity > 0) ? $memberCost / $memberQuantity : 0;
        // ½þµÑÈñÃ±²Á
        $depreciationUnitCost = ($depreciationQuantity > 0) ? $depreciationCost / $depreciationQuantity : 0;
        // À½Â¤ÈñÍÑ
        $manufacturingUnitCost = ($manufacturingQuantity > 0) ? $manufacturingCost / $manufacturingQuantity : 0;
        // ½þµÑÂÐ¾Ý³°¸ÇÄêÈñ
        $costNotDepreciation = $orderFixedCostNotDepreciation;

        // ¥¯¥é¥¹¤Î¥×¥í¥Ñ¥Æ¥£¤Ë¥»¥Ã¥È
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