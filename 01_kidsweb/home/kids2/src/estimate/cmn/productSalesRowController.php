<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/estimateRowController.php");
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

class productSalesRowController extends estimateRowController {

    protected static $customerCompanyCodeMaster; // �ܵ��衢������ޥ�����
    protected static $divisionSubjectCodeMaster; // ���ʬ�ࡢ�������ܥޥ�����(����ʬ���������ʷ��Ѥߡ�

    protected static $headerNameList; // �оݥ��ꥢ�Υإå����Υ���̾��
    protected static $resultNameList; // �оݥ��ꥢ�η׻���̤Υ���̾��(���ٺǽ��Ԥμ��ι�)

    // ��������
    public $columnNumberList; // ����ֹ�ꥹ��
    protected $columnDisplayNameList; // ���ɽ��̾�ꥹ��

    public function __construct($objDB) {
        $this->areaCode = DEF_AREA_PRODUCT_SALES; // ���ꥢ�����ɤΥ��å�
        parent::__construct($objDB);        
    }

    protected function setNameList() {
        if (!static::$headerNameList) {
            static::$headerNameList = workSheetConst::PRODUCT_SALES_HEADER_NAME_LIST;
        }
        if (!static::$resultNameList) {
            static::$resultNameList = workSheetConst::PRODUCT_SALES_RESULT_NAME_LIST;
        }
    }

    // ���ʬ��Υޥ������Υǡ������������
    protected function setDivisionSubjectCodeMaster() {
        if (!static::$divisionSubjectCodeMaster) {
            $areaCode = $this->areaCode;
            $masterData = $this->objDB->getDivisionCodeList($areaCode);
            static::$divisionSubjectCodeMaster = $masterData;
        }
    }
}