<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/estimateOtherCellsController.php");
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

/**
*	�եå����ڤӤ���¾����κƷ׻����ǡ��������å����饹
*	
*	�ʲ��Υ����Х��ѿ���������뤳��
*   @param object $objDB        �ǡ����١�����³���֥�������(clsDB�ޤ��ϷѾ����饹)
*   @param object $sheet        phpSpreadSheet�Υ����ȥ��֥�������
*   
*/

class registOtherCellsController extends estimateOtherCellsController {
    
    public function __construct() {
        parent::__construct();
    }

    public function calculateParam($objRowList, $objHeader, $cellAddressList, $standardRateMaster) {
        global $sheet;
        // ���Ѥθ���������
        $cellPayoffCircle = $cellAddressList[workSheetConst::HIDDEN_PAYOFF_CIRCLE];
        $payoffCircle = $sheet->getCell($cellPayoffCircle)->getCalculatedValue();

        // �ܲ٤θ���������
        $cellMainProduct = $cellAddressList[workSheetConst::HIDDEN_MAIN_PRODUCT];
        $mainProduct = $sheet->getCell($cellMainProduct)->getCalculatedValue();

        foreach ($objRowList as $objRow) {
            if ($objRow->invalidFlag === false) {
                $areaCode = $objRow->areaCode;
                // �Ʒ׻���̤��ʤ����ϼ����ͤ򻲾Ȥ���
                $subtotal = $objRow->calculatedSubtotalJP ? $objRow->calculatedSubtotalJP : $objRow->subtotal;
                switch ($areaCode) {
                    case DEF_AREA_PRODUCT_SALES:
                        $quantity = $objRow->quantity;
                        $classItem = $objRow->classItem;
                        // ���������
                        $receiveProductTotalPrice += $subtotal;
                        // ���ʿ��̹��
                        $receiveProductTotalQuantity += $quantity;
                        // ���ѿ�
                        if ($classItem === $mainProduct) {
                            $productionQuantity += $quantity;
                        }
                        break;
                    case DEF_AREA_FIXED_COST_SALES:
                        $quantity = $objRow->quantity;
                        // �����������
                        $receiveFixedCostTotalPrice += $subtotal;
                        // ��������̹��
                        $receiveFixedCostTotalQuantity += $quantity;
                        break;
                    case DEF_AREA_FIXED_COST_ORDER:
                        $payoff = $objRow->payoff;
                        // �����񾮷�
                        $orderFixedCostTotalPrice += $subtotal;
                        
                        if ($payoff === $payoffCircle) {
                            // ������
                            $depreciationCost += $subtotal;
                        } else {
                            // �����оݳ����
                            $orderFixedCostNotDepreciation += $subtotal;
                        }
                        break;
                    case DEF_AREA_PARTS_COST_ORDER:
                        $payoff = $objRow->payoff;
                        
                        if ($payoff === $payoffCircle) {
                            // ������
                            $depreciationCost += $subtotal;
                        } else {
                            // ������
                            $memberCost += $subtotal;
                        }
                        break;
                    case DEF_AREA_OTHER_COST_ORDER:
                        $payoff = $objRow->payoff;
                        
                        if ($payoff === $payoffCircle) {
                            // ������
                            $depreciationCost += $subtotal;
                        } else {
                            // ������
                            $memberCost += $subtotal;
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        // ����¾�׻�
        // ��������
        $productTotalPrice = $receiveProductTotalPrice;
        // ��¤����
        $manufacturingCost = $depreciationCost + $memberCost;
        // ��������
        $productProfit = $productTotalPrice - $manufacturingCost;
        // ��������Ψ
        $productProfitRate = $manufacturingCost ? ($productProfit / $manufacturingCost) : '';
        // ����������
        $fixedCostTotalPrice = $receiveFixedCostTotalPrice;
        // ����������
        $fixedCostProfit = $fixedCostTotalPrice - $orderFixedCostNotDepreciation;
        // ����������Ψ
        $fixedCostProfitRate = $fixedCostTotalPrice ? ($fixedCostProfit / $fixedCostTotalPrice) : '';
        // ������
        $salesAmount = $productTotalPrice + $fixedCostTotalPrice;
        // ���������
        $profit = $productProfit + $fixedCostProfit;
        // ����Ψ
        $profitRate = $salesAmount ? ($profit / $salesAmount) : '';
        // ɸ����
        $standardRate = $standardRateMaster;
        // ������¤����
        $indirectCost = floor($salesAmount * $standardRate);
        // �Ķ�����
        $operatingProfit = $profit - $indirectCost;
        // �Ķ�����Ψ
        $operatingProfitRate = $salesAmount ? ($operatingProfit / $salesAmount) : '';
        // ������Ŀ�
        $memberQuantity = $productionQuantity;
        // ������Ŀ�
        $depreciationQuantity = $productionQuantity;
        // ��¤���ѸĿ�
        $manufacturingQuantity = $productionQuantity;
        // ������ñ��
        $memberUnitCost = ($memberQuantity > 0) ? $memberCost / $memberQuantity : 0;
        // ������ñ��
        $depreciationUnitCost = ($depreciationQuantity > 0) ? $depreciationCost / $depreciationQuantity : 0;
        // ��¤����
        $manufacturingUnitCost = ($manufacturingQuantity > 0) ? $manufacturingCost / $manufacturingQuantity : 0;
        // �����оݳ�������
        $costNotDepreciation = $orderFixedCostNotDepreciation;

        // ���饹�Υץ�ѥƥ��˥��å�
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