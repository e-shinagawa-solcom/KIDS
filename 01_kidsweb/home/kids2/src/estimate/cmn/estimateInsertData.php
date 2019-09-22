<?php

require_once ('conf.inc');
require_once ( LIB_FILE );

require_once ( SRC_ROOT . "estimate/cmn/const/workSheetConst.php");

// ��Ͽ�ѥǡ����������饹
class estimateInsertData {
    protected $headerData;
    protected $rowDataList;
    protected $calculatedData;

    protected $inputUserCode;
    protected $inchargeUserCode;
    protected $developUserCode;
    protected $groupCode;
    protected $productNo;
    protected $productCode;
    protected $reviseCode;
    protected $estimateNo;
    protected $goodsPlanCode;
    protected $revisionNo;
    protected $productRevisionNo;


    protected $objDB;

    protected $companyCodeList;

    public function __construct() {

    }

    // ���Ѹ����׻�������Ϥ��줿�ǡ����򥻥åȤ���
    protected function setParam($input, $inputUserCode, $objDB) {
        // ��Ͽ�ѤΥѥ�᡼���򥻥å�
        $this->headerData = $input['headerData'];
        $this->rowDataList = $input['rowDataList'];
        $this->calculatedData = $input['calculatedData'];

        // ���ϼԤΥ桼���������ɤ򥻥å�
        $this->inputUserCode = $inputUserCode;

        // DB���饹�Υ��å�
        $this->objDB = $objDB;

        return;
    }

    // ���ʥ����ɤ���Ϥ���
    public function getProductCode() {
        return $this->productCode;
    }

    // ��Х��������ɤ���Ϥ���
    public function getReviseCode() {
        return $this->reviseCode;
    }

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	��Ͽ�Ѥ�INSERTʸ����������
    *   
    *   @param array $array ��Ͽ�ǡ����ʥ����˥����̾����ĥǡ��������
    *	@return boolean
    *	@access protected
    */
    protected function makeInsertQuery($table, $array) {
        foreach ($array as $key => $value) {
            // �����Υ��å�
            if (!isset($columns)) {
                $columns = $key;
            } else {
                $columns = $columns. ", ". $key;
            }
            
            $type = gettype($value);
            if ($type == 'boolean' || $type == 'NULL') {
                $value = var_export($value, true);
            }

            if (!isset($values)) {
                $values = $value; 
            } else {
                $values = $values. ", ". $value;
            }
        }
        $sqlQuery = "INSERT INTO ". $table. " (". $columns. ") VALUES (". $values. ")";
        return $sqlQuery;
    }
}
