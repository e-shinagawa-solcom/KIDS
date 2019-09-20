<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/estimateRowController.php");
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

class otherCostOrderRowController extends estimateRowController {
    protected static $customerCompanyCodeMaster; // �ܵ��衢������ޥ�����
    protected static $divisionSubjectCodeMaster; // ���ʬ�ࡢ�������ܥޥ�����(����ʬ���������ʷ��Ѥߡ�

    protected static $headerNameList; // �оݥ��ꥢ�Υإå����Υ���̾��
    protected static $resultNameList; // �оݥ��ꥢ�η׻���̤Υ���̾��(���ٺǽ��Ԥμ��ι�)

    // ��������
    public $columnNumberList; // ����ֹ�ꥹ��
    protected $columnDisplayNameList; // ���ɽ��̾�ꥹ��

    protected $stockClassCode; // ������ʬ

    public function __construct($objDB) {
        $this->areaCode = DEF_AREA_OTHER_COST_ORDER; // ���ꥢ�����ɤΥ��å�
        $this->stockClassCode = workSheetConst::AREA_ATTRIBUTE_TO_STOCK_CLASS_CODE[$this->areaCode]; // ������ʬ�Υ��å�
        parent::__construct($objDB);        
    }

    protected function setNameList() {
        if (!static::$headerNameList) {
            static::$headerNameList = workSheetConst::ORDER_ELEMENT_COST_HEADER_CELL;
        }
        if (!static::$resultNameList) {
            static::$resultNameList = workSheetConst::ORDER_ELEMENT_COST_RESULT_CELL;
        }
    }

    // ���ʬ��Υޥ������Υǡ������������
    protected function setDivisionSubjectCodeMaster() {
        if (!static::$divisionSubjectCodeMaster) {
            $areaCode = $this->areaCode;
            $stockClassCode = $this->stockClassCode;
            $masterData = $this->objDB->getSubjectCodeList($areaCode);
            foreach ($masterData as $classCode => $data) {
                if ($classCode == $stockClassCode) {
                    $newMasterData = $data;
                    break;
                }
            }
            static::$divisionSubjectCodeMaster = $newMasterData;
        }
    }

    // �̲ߥ졼�ȥޥ���������Ǽ�����б������̲ߥ졼�Ȥ��������
    protected function getConversionRateForDelivery() {
        $monetary = $this->monetary;
        $delivery = $this->delivery;
        
        if (!$monetary) {
            $acquiredRate = null;
        } else if ($monetary == DEF_MONETARY_YEN) {
            $acquiredRate = 1;
        } else {
            if(!$delivery) {
                $acquiredRate = null;
            } else {
                $conversionRateMaster = self::$conversionRateMaster;
                if (!$conversionRateMaster[$monetary]) {
                    $acquiredRate = null;
                } else {
                    foreach ($conversionRateMaster[$monetary] as $data) {
                        // Ǽ�������б������̲ߥ졼�Ȥ���������DB������������ꥹ����θ�����
                        if (strtotime($delivery) <= strtotime($data['endDate']) 
                            && strtotime($data['startDate']) <= strtotime($delivery)) {
                            $acquiredRate = $data['conversionRate'];
                        } else {
                            $acquiredRate = null;
                        }
                    }
                }
            }
        }
        return $acquiredRate;
    }
}