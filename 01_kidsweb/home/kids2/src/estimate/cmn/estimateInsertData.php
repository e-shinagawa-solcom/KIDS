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

    // ���Ѹ����ֹ����Ϥ���
    public function getEstimateNo() {
        return $this->estimateNo;
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

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	��Ͽ�Ѥ�INSERTʸ����������
    *   
    *   @param array $array ��Ͽ�ǡ����ʥ����˥����̾����ĥǡ��������
    *   @param string $condition �������
    *   @param $returning �ֵѤ��륫���
    *
    *	@return boolean
    *	@access protected
    */
    protected function makeInsertSelectQuery($table, $array, $condition = null, $returning = null) {

        if ($condition) { // ������郎���ꤵ��Ƥ���Ȥ�
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
            $sqlQuery = "INSERT INTO ". $table;
            $sqlQuery .= " (". $columns. ")";
            $sqlQuery .= " SELECT";
            $sqlQuery .= " ". $values;
            $sqlQuery .= " FROM ". $table;
            $sqlQuery .= " ". $condition;

            if ($returning) {
                $returningQuery = " RETURNING";
                if (is_array($returning)) {
                    $columns = "";
                    foreach($returning as $column) {
                        if (is_string($column)) {
                            if ($columns) {
                                $columns .= ", ". $column;
                            } else {
                                $columns = " ". $column;
                            }
                        } else {
                            $returningQuery = "";
                            break;
                        }
                    }
    
                    $returningQuery .= $columns;
                    
                } else if (is_string($returning)) {
                    $returningQuery .= " ". $returning;
                } else {
                    $returningQuery = "";
                }
    
                $sqlQuery .= $returningQuery;
            }
        } else {
            $sqlQuery = $this->makeInsertQuery($table, $array);
        }

        return $sqlQuery;
    }


    
    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	����ޥ����ؤκ���оݤμ������򹹿�����
    *
    *   @param string $receiveNo �����ֹ�
    *   @param string $revisionNo �����ӥ�����ֹ�
    *   
    *	@return true
    */
    protected function updateDeleteRecordForReceive($receiveNo) {

        $strQuery = "UPDATE m_receive SET strreceivecode = '*' || strreceivecode || '*'";
        $strQuery .= " WHERE lngreceiveno = ". $receiveNo;

        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }



    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	����ޥ����ؤκ���쥳���ɤ���Ͽ��Ԥ�
    *
    *   @param string $receiveNo �����ֹ�
    *   @param string $revisionNo ���ԡ����μ����ӥ�����ֹ�(����Ū�ˤϺǿ���ӥ����)
    *   
    *	@return true
    */
    protected function insertDeleteRecordForReceive($receiveNo, $revisionNo) {
        // �ơ��֥������ 
        $table = 'm_receive';

        if (!strlen($receiveNo) || !strlen($revisionNo)) {
            // �����ֹ�ȥ�ӥ�����ֹ椬���åȤ���Ƥ��ʤ��ޤ��϶�ʸ���ξ��Ͻ������ʤ�
            return false;
        }

        // ��Ͽ�ǡ����κ���
        $data = array(
            'lngreceiveno' => $receiveNo,
            'lngrevisionno' => -1,
            'strreceivecode' => "CASE WHEN strreceivecode ~ '^\*.+\*$' THEN strreceivecode ELSE '*' || strreceivecode || '*' END",
            'strrevisecode' => 'strrevisecode',
            'lnginputusercode' => $this->inputUserCode,
            'bytinvalidflag' => 'false',
            'dtminsertdate' => 'NOW()',
        );

        $condition = "WHERE lngreceiveNo = ". $receiveNo;
        $condition .= " AND lngrevisionno = ". $revisionNo;

        // �����������
        $strQuery = $this->makeInsertSelectQuery($table, $data, $condition);
        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	ȯ��ޥ����ؤκ���쥳���ɤ���Ͽ��Ԥ�
    *
    *   @param array $rowData �ԤΥǡ���
    *   @param string $orderCode ȯ������
    *   
    *	@return true
    */
    protected function insertDeleteRecordForOrder($orderNo, $revisionNo) {
        // �ơ��֥������
        $table = 'm_order';

        if (!strlen($orderNo) || !strlen($revisionNo)) {
            // �����ֹ�ȥ�ӥ�����ֹ椬���åȤ���Ƥ��ʤ��ޤ��϶�ʸ���ξ��Ͻ������ʤ�
            return false;
        }

        $data = array(
            'lngorderno' => $orderNo,
            'lngrevisionno' => -1,
            'strordercode' => 'strordercode',
            'lnginputusercode' => $this->inputUserCode,
            'bytinvalidflag' => 'false',
            'dtminsertdate' => 'NOW()'
        );

        $condition = "WHERE lngorderno = ". $orderNo;
        $condition .= " AND lngrevisionno = ".$revisionNo;

        // �����������
        $strQuery = $this->makeInsertSelectQuery($table, $data, $condition);

        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }
}
