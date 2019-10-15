<?php

require_once ('conf.inc');
require_once ( LIB_FILE );

require_once ( SRC_ROOT . "estimate/cmn/const/workSheetConst.php");
require_once ( SRC_ROOT . "estimate/cmn/estimateInsertData.php");

// ��Ͽ�ѥǡ����������饹
class deleteInsertData extends estimateInsertData {
    
    public function __construct() {
        parent::__construct();
    }
    

    // ɬ�פʥѥ�᡼���򥯥饹�˥��åȤ���
    public function setDeleteParam($estimateNo, $revisionNo, $inputUserCode, $objDB) {

        $this->inputUserCode = $inputUserCode;

        $this->objDB = $objDB;

        $this->estimateNo = $estimateNo;

        $this->revisionNo = $revisionNo;

        return true;
    }


    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	���Ѹ����κ����Ԥ�
    *
    *	@return true
    */
    public function delete() {

        if (strlen($this->estimateNo) && is_numeric($this->estimateNo)
            && strlen($this->revisionNo) && is_numeric($this->revisionNo)) {

            $estimateNo = $this->estimateNo;
            $revisionNo = $this->revisionNo;

            $estimateDetail = $this->objDB->getEstimateDetail($estimateNo);
            
            $firstRecord = $estimateDetail[0];

            // ��ӥ��������å�
            if ($firstRecord->lngrevisionno !== $revisionNo) {
                return fncOutputError ( DEF_MESSAGE_CODE_CURRENT_REVISION_ERROR, DEF_WARNING, "", FALSE, "", $this->objDB );
            }

            // ����Ѥߥ����å�
            if ((int)$firstRecord->minrevisionno === -1) {
                return fncOutputError ( DEF_MESSAGE_CODE_DELETED_ERROR, DEF_WARNING, "", FALSE, "", $this->objDB );
            }

            // ���ơ����������å�
            foreach ($estimateDetail as $record) {
                if (isset($record->lngreceivestatuscode)) {
                    $status = (int)$record->lngreceivestatuscode;
                    if ($status !== DEF_RECEIVE_APPLICATE) {
                        return fncOutputError ( DEF_MESSAGE_CODE_DELETE_CHECK_ERROR, DEF_WARNING, "", FALSE, "", $this->objDB );
                    }
                } else if (isset($record->lngorderstatuscode)) {
                    $status = (int)$record->lngorderstatuscode;
                    if ($status !== DEF_ORDER_APPLICATE) {
                        return fncOutputError ( DEF_MESSAGE_CODE_DELETE_CHECK_ERROR, DEF_WARNING, "", FALSE, "", $this->objDB );
                    }
                }
            }

            // ���Ѹ��������ε�ǽ�����ɼ���
            $editFunctionCode = DEF_FUNCTION_E3;

            $productCode = $firstRecord->strproductcode;
            $reviseCode = $firstRecord->strrevisecode;

            // ��¾�ơ��֥�����å��ʸ��Ѹ���������Ǥʤ����Ȥ��ǧ)
            $check = $this->objDB->checkExclusiveStatus($editFunctionCode, $productCode, $reviseCode);
            if ($check === true) { // ��¾���椬ͭ���ʾ��
                return fncOutputError ( DEF_MESSAGE_CODE_EXCLUSIVE_CHECK_ERROR, DEF_WARNING, "$check->struserdisplayname", FALSE, "", $this->objDB );
            }

            // ���Ѹ����ޥ������
            $this->deteleMasterEstimate();

            // ����ޥ�����ȯ��ޥ����κ��
            foreach ($estimateDetail as $record) {
                if (isset($record->lngreceiveno)) {

                    $this->deleteMasterReceive($record);

                } else if (isset($record->lngorderno)) {

                    $this->deleteMasterOrder($record);
                    
                }                                            
            }
            
            $this->productNo = $firstRecord->lngproductno;
            $this->reviseCode = $reviseCode;
            $this->productRevisionNo = $firstRecord->lngproductrevisionno;

            // ���ʥޥ����κ��
            $this->deleteMasterProduct();
        }

        return;
    }

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	���ʥޥ�����κ��������Ԥ�
    *
    *	@return
    */
    protected function deleteMasterProduct() {
        $this->updateDeleteRecordForProduct();
        $this->insertDeleteRecordForProduct();
        return;
    }

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	���ʥޥ����κ���оݤ����ʤξ���򹹿�����
    *
    *	@return true
    */
    protected function updateDeleteRecordForProduct() {

        $strQuery = "UPDATE m_product SET bytinvalidflag = true, strproductcode = strproductcode || '_del'";
        $strQuery .= " WHERE lngproductno = ". $this->productNo;
        $strQuery .= " AND strrevisecode = '". $this->reviseCode. "'";

        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }


    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	���ʥޥ����κ���쥳���ɤ���Ͽ��Ԥ�
    *
    *	@return true
    */
    protected function insertDeleteRecordForProduct() {

        $table = 'm_product';

        $data = array(
            'lngproductno' => $this->productNo,
            'strproductcode' => "CASE WHEN strproductcode LIKE '%_del' THEN strproductcode ELSE strproductcode || '_del' END",
            'lnginputusercode' => $this->inputUserCode,
            'bytinvalidflag' => 'true',
            'dtminsertdate' => 'NOW()',
            'lngrevisionno' => -1,
            'strrevisecode' => 'strrevisecode'
        );

        $condition = "WHERE lngproductno = ". $this->productNo;
        $condition .= " AND lngrevisionno = ". $this->productRevisionNo;
        $condition .= " AND strrevisecode = '" . $this->reviseCode."'";

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
    *	���Ѹ����ޥ�����κ��������Ԥ�
    *   
    *	@return true
    */
    protected function deteleMasterEstimate() {
        $this->updateDeleteRecordForEstimate();
        $this->insertDeleteRecordForEstimate();
    }

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	���Ѹ����ޥ����κ���оݤθ��Ѹ����ξ���򹹿�����
    *
    *	@return true
    */
    protected function updateDeleteRecordForEstimate() {

        $strQuery = "UPDATE m_estimate SET strproductcode = strproductcode || '_del'";
        $strQuery .= " WHERE lngestimateno = ". $this->estimateNo;

        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }
    
    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	���Ѹ����ޥ����ؤκ���쥳���ɤ���Ͽ��Ԥ�
    *   
    *	@return true
    */
    protected function insertDeleteRecordForEstimate() {
        $table = 'm_estimate';

        $data = array(
            'lngestimateno' => $this->estimateNo,
            'lngrevisionno'=> -1,
            'strproductcode'=> "CASE WHEN strproductcode LIKE '%_del' THEN strproductcode ELSE strproductcode || '_del' END",
            'strrevisecode' => 'strrevisecode',
            'lnginputusercode' => $this->inputUserCode,
            'bytinvalidflag' => 'false',
            'dtminsertdate' => 'NOW()',
        );

        $condition = "WHERE lngestimateno = ". $this->estimateNo;
        $condition .= " AND lngrevisionno = ". $this->revisionNo;

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
    *	����ޥ�����κ��������Ԥ�
    *
    *   @param object $record ���ٹԤθ������
    *   
    *	@return
    */
    protected function deleteMasterReceive($record) {
        $receiveNo = $record->lngreceiveno;
        $revisionNo = $record->lngreceiverevisionno;

        $this->updateDeleteRecordForReceive($receiveNo);
        $this->insertDeleteRecordForReceive($receiveNo, $revisionNo);

        return;
    }


    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	ȯ��ޥ�������κ��������Ԥ�
    *
    *   @param object $record ���ٹԤθ������
    *   
    *	@return
    */
    protected function deleteMasterOrder($record) {
        $orderNo = $record->lngorderno;
        $revisionNo = $record->lngorderrevisionno;

        $this->insertDeleteRecordForOrder($orderNo, $revisionNo);

        return;
    }

}
