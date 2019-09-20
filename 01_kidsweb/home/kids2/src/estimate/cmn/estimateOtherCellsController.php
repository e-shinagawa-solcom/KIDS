<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

/**
*	�եå����ڤӤ���¾����κƷ׻����ǡ��������å����饹
*	
*	�ʲ��Υ����Х��ѿ���������뤳��
*   @param object $objDB        �ǡ����١�����³���֥�������(clsDB�ޤ��ϷѾ����饹)
*   @param object $sheet        phpSpreadSheet�Υ����ȥ��֥�������
*   
*/

class estimateOtherCellsController {

    protected $errorMessage; // ���顼��å�����
    protected $messageCode;

    // ������
    public $productionQuantity;                 // ���ѿ� pcs
    public $receiveProductTotalPrice;           // ���������
    public $receiveProductTotalQuantity;        // ���ʿ��̹��
    public $receiveFixedCostTotalPrice;         // �����������
    public $receiveFixedCostTotalQuantity;      // ��������̹��
    public $orderFixedCostTotalPrice;           // �����񾮷�
    public $orderFixedCostNotDepreciation;      // �����оݳ����
    public $productTotalPrice;                  // ��������
    public $productProfit;                      // ��������
    public $productProfitRate;                  // (��������Ψ)
    public $fixedCostTotalPrice;                // ����������
    public $fixedCostProfit;                    // ����������
    public $fixedCostProfitRate;                // (����������Ψ)
    public $salseAmount;                        // ������
    public $profit;                             // ���������
    public $profitRate;                         // (����Ψ)
    public $indirectCost;                       // ������¤����
    public $standardRate;                       // ɸ����
    public $operatingProfit;                    // �Ķ�����
    public $operatingProfitRate;                // (�Ķ�����Ψ)
    public $memberQuantity;                     // ������ʿ��̡�
    public $memberUnitCost;                     // �������ñ����
    public $memberCost;                         // ������
    public $depreciationQuantity;               // ������ʿ��̡�
    public $depreciationUnitCost;               // �������ñ����
    public $depreciationCost;                   // ������
    public $manufacturingQuantity;              // ��¤���ѡʿ��̡�
    public $manufacturingUnitCost;              // ��¤���ѡ�ñ����
    public $manufacturingCost;                  // ��¤����
    public $costNotDepreciation;                // �����оݳ�������

    public function __construct() {

    }

    // �����������Υ���̾�Τ��б������ǡ����ʷ׻���̡ˤ���Ϥ���
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