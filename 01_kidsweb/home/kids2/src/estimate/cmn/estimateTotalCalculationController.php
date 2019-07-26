<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

/**
*	��������ȤκƷ׻����ǡ��������å����饹
*	
*	�ʲ��Υ����Х��ѿ���������뤳��
*   @param object $objDB        �ǡ����١�����³���֥�������(clsDB�ޤ��ϷѾ����饹)
*   @param object $sheet        phpSpreadSheet�Υ����ȥ��֥�������
*   
*/

class estimateTotalCalculationController {

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

    // �ޥ������ǡ�������

    // ����̾�Υꥹ��
    protected static $nameList; // �إå��������Ϲ��ܤΥ���̾��
    protected static $titleNameList; // �إå����������ȥ���ܤΥ���̾��

    protected $headerTitleNameList; // ���Ϲ��ܤΥ����ȥ�ꥹ��

    protected $cellAddressList; // ����̾�Τ��б�����������֤Υꥹ��

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

    // ����̾�Τ��б���������Υꥹ�Ȥȹ��ֹ�ˤ�����ǡ�������
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

    // �ƹ��ܤΥǡ������������
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

    // �ƹ��ܤΥ����ȥ�̾���������
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
                $subtotal = $objRow->calculatedSubtotal ? $objRow->calculatedSubtotal : $objRow->subtotal;
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
        $productProfitRate = $productProfit / $manufacturingCost;
        // ����������
        $fixedCostTotalPrice = $receiveFixedCostTotalPrice;
        // ����������
        $fixedCostProfit = $fixedCostTotalPrice - $orderFixedCostNotDepreciation;
        // ����������Ψ
        $fixedCostProfitRate = $fixedCostProfit / $fixedCostTotalPrice;
        // ������
        $salesAmount = $productTotalPrice + $fixedCostTotalPrice;
        // ���������
        $profit = $productProfit + $fixedCostProfit;
        // ����Ψ
        $profitRate = $profit / $salesAmount;
        // ɸ����
        $standardRate = $standardRateMaster;
        // ������¤����
        $indirectCost = floor($salesAmount * $standardRate);
        // �Ķ�����
        $operatingProfit = $profit - $indirectCost;
        // �Ķ�����Ψ
        $operatingProfitRate = $operatingProfit / $salesAmount;
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

    // ���ʥ����ɤ������ͤ�Х�ǡ�����󤹤�
    protected function validateProductCode() {
        $productCode = $this->productCode;
        // �Х�ǡ��������
        if (isset($productCode) && $productCode !=='') {
            if(!preg_match("/\A[0-9]+\z/", $productCode)) {
                // ���顼����
                $this->messageCode['productCode'] = 9201;
            }
        } else {
            $this->messageCode['productCode'] = 9001; // ɬ�ܥ����å�
        }
        return true;
    }

    // ����̾
    protected function validateProductName() {
        $productName = $this->productName;
        // �Х�ǡ��������
        if (!isset($productName) || $productName ==='') {
            // ���顼��å�����or���顼�����ɽ��ϡ�ɬ�ܥ��顼��
            $this->messageCode['productName'] = 9001; // ɬ��
        }
        return true;
    }

    // ����̾�ʱѸ��
    protected function validateProductEnglishName() {
        $productEnglishName = $this->productEnglishName;
        // ʸ��������å�(Ⱦ�ѱѿ������� ASCII��0x20���0x7e)
        if (isset($productEnglishName) && $productEnglishName !=='') {
            if(!preg_match("/\A[ -~]+\z/", $productEnglishName)) {
                // ���顼����
                $this->messageCode['productEnglishName'] = 9201;
            }
        } else {
            $this->messageCode['productEnglishName'] = 9001; // ɬ�ܥ����å�
        }
        return true;
    }

    // ����
    protected function validateRetailPrice() {
        $retailPrice = $this->retailPrice;
        if (isset($retailPrice) && $retailPrice !=='') {
            if(!preg_match("/\A[0-9]+\z/", $retailPrice)) {
                // ���顼����
                $this->messageCode['retailPrice'] = 9201;
            }
        } else {
            $this->messageCode['retailPrice'] = 9001; // ɬ�ܥ����å�
        }
        return true;
    }

    // �Ķ���������å�
    protected function validateInchargeGroupCode() {
        $inchargeGroupCode = $this->inchargeGroupCode;
        // �Х�ǡ��������
        if (isset($inchargeGroupCode) && $inchargeGroupCode !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $inchargeGroupCode)) {
                list ($inchargeGroupCodeNumber, $inchargeGroupCodeName) = explode(':', $inchargeGroupCode);
                global $objDB; // �����Х�Υǡ����١������֥������ȼ���
                $result = $objDB->checkGroupExist($inchargeGroupCodeNumber);
                // �ޥ����������å�
                if (!$result) {
                    $this->messageCode['inchargeGroupCode'] = 9202;
                } else {
                    $this->inchargeGroupCodeNumber = $inchargeGroupCodeNumber; // ���롼�ץ����ɤ򥻥åȤ���
                }
            } else {
                // ���Ϸ�������
                $this->messageCode['inchargeGroupCode'] = 9201;
            }
        } else {
            // ɬ�ܥ��顼
            $this->messageCode['inchargeGroupCode'] = 9001;
        }
        return true;
    }

    // ��ȯô���ԤΥ����å���Ԥ�
    protected function validateUserCode() {
        $userCode = $this->userCode;
        // �Х�ǡ��������
        if (isset($userCode) && $userCode !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $userCode)) {
                list ($userCodeNumber, $userCodeName) = explode(':', $userCode);
                global $objDB; // �����Х�Υǡ����١������֥������ȼ���
                $result = $objDB->checkUserExist($userCodeNumber);
                // �ޥ����������å�
                if (!$result) {
                    $this->messageCode['userCode'] = 9202;
                } else {
                    $this->userCodeNumber = $userCodeNumber; // ���롼�ץ����ɤ򥻥åȤ���
                }
            } else {
                // ���Ϸ�������
                $this->messageCode['userCode'] = 9201;
            }
        } else {
            // ɬ�ܥ��顼
            $this->messageCode['userCode'] = 9001;
        }
        return true;
    }

    // �����ȥ������
    protected function validateCartonQuantity() {
        $cartonQuantity = $this->cartonQuantity;
        if (isset($cartonQuantity) && $cartonQuantity !=='') {
            if (!preg_match("/\A[1-9][0-9]*\z/", $cartonQuantity)) {
                // ���Ϸ�������
                $this->messageCode['cartonQuantity'] = 9201;
            }
        } else {
            // ɬ�ܥ��顼
            $this->messageCode['cartonQuantity'] = 9001;
        }
        return true;
    }

    // ���ѿ�
    protected function validateProductionQuantity() {
        $productionQuantity = $this->productionQuantity;
        if (isset($productionQuantity) && $productionQuantity !=='') {
            if (!preg_match("/\A[1-9][0-9]*\z/", $productionQuantity)) {
                // ���Ϸ�������
                $this->messageCode['productionQuantity'] = 9201;
            }
        } else {
            // ɬ�ܥ��顼
            $this->messageCode['productionQuantity'] = 9001;
        }
        return true;
    }

    // 	if( !fncCheckGroup( $objDB, $aryProductHeader, $lngUserDispCode ) )
// 	{
// 		$strErrMsg	= "�ʥ�����Ԥ�Excel�ǡ���������礬�ۤʤ�ޤ�����";
// 		$strErrMsg	= mb_convert_encoding( $strErrMsg, "EUC-JP", "EUC-JP,UTF-8,SJIS,ASCII,JIS" );

// 		// ���롼�פ��㤦��硢������λ
// 		fncOutputError( 1603, DEF_WARNING, $strErrMsg, TRUE, "", $objDB );
// 	}

}