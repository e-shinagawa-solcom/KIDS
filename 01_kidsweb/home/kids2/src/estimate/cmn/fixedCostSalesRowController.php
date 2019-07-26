<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/estimateRowController.php");
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

class fixedCostSalesRowController extends estimateRowController {

    protected static $customerCompanyCodeMaster; // �ܵ��衢������ޥ�����
    protected static $divisionSubjectCodeMaster; // ���ʬ�ࡢ�������ܥޥ�����(����ʬ���������ʷ��Ѥߡ�

    protected static $headerNameList; // �оݥ��ꥢ�Υإå����Υ���̾��
    protected static $resultNameList; // �оݥ��ꥢ�η׻���̤Υ���̾��(���ٺǽ��Ԥμ��ι�)

    // ��������
    public $columnNumberList; // ����ֹ�ꥹ��
    protected $columnDisplayNameList; // ���ɽ��̾�ꥹ��

    public function __construct() {
        $this->areaCode = DEF_AREA_FIXED_COST_SALES; // ���ꥢ�����ɤΥ��å�
        parent::__construct();        
    }

    protected function setNameList() {
        if (!static::$headerNameList) {
            static::$headerNameList = workSheetConst::RECEIVE_FIXED_COST_HEADER_CELL;
        }
        if (!static::$resultNameList) {
            static::$resultNameList = workSheetConst::RECEIVE_FIXED_COST_RESULT_CELL;
        }
    }

    // ���ʬ��Υޥ������Υǡ������������
    protected function setDivisionSubjectCodeMaster() {
        if (!static::$divisionSubjectCodeMaster) {
            global $objDB;
            $areaCode = $this->areaCode;
            $masterData = $objDB->getDivisionCodeList($areaCode);
            static::$divisionSubjectCodeMaster = $masterData;
        }
    }
}