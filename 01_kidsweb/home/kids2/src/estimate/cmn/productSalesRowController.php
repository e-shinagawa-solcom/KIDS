<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/estimateRowController.php");
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

class productSalesRowController extends estimateRowController {

    protected static $customerCompanyCodeMaster; // �ܵ��衢������ޥ�����
    protected static $divisionSubjectCodeMaster; // ���ʬ�ࡢ�������ܥޥ�����(����ʬ���������ʷ��Ѥߡ�

    protected static $headerNameList; // �оݥ��ꥢ�Υإå����Υ���̾��
    protected static $resultNameList; // �оݥ��ꥢ�η׻���̤Υ���̾��(���ٺǽ��Ԥμ��ι�)

    protected static $productionQuantity = 0; // ���ѿ��׻����

    protected static $PQOutputFlag;

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

    // ���ѿ����ܲ٤ο��̤�û�����
    protected function addProductionQuantity() {
        if ($this->invalidFlag !== true) {
            if ($this->divisionSubjectCode === DEF_SALES_DIVISION_CODE_PRODUCT_SALES
                && $this->classItemCode === DEF_SALES_CLASS_CODE_MAIN_PRODUCT) {

                self::$productionQuantity += $this->quantity;

            }            
        }

        return;
    }

    // ��Ͽ���Υѥ�᡼�������å���Ԥ�
    public function workSheetRegistCheck() {
        if (self::$PQOutputFlag === true) {
            // ���ѿ������٤Ǥ���Ϥ���Ƥ���������������
            return false;
        }
        parent::workSheetRegistCheck();
        $this->addProductionQuantity();
        return;
    }

    // ���ѿ��η׻���̤���Ϥ���
    public static function outputProductionQuantity() {
        static $PQOutputFlag = true;
        return self::$productionQuantity;
    }
}