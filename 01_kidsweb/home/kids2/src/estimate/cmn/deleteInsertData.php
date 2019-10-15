<?php

require_once ('conf.inc');
require_once ( LIB_FILE );

require_once ( SRC_ROOT . "estimate/cmn/const/workSheetConst.php");
require_once ( SRC_ROOT . "estimate/cmn/estimateInsertData.php");

// 登録用データ作成クラス
class deleteInsertData extends estimateInsertData {
    
    public function __construct() {
        parent::__construct();
    }
    

    // 必要なパラメータをクラスにセットする
    public function setDeleteParam($estimateNo, $revisionNo, $inputUserCode, $objDB) {

        $this->inputUserCode = $inputUserCode;

        $this->objDB = $objDB;

        $this->estimateNo = $estimateNo;

        $this->revisionNo = $revisionNo;

        return true;
    }


    /**
    * DB登録用関数
    *
    *	見積原価の削除を行う
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

            // リビジョンチェック
            if ($firstRecord->lngrevisionno !== $revisionNo) {
                return fncOutputError ( DEF_MESSAGE_CODE_CURRENT_REVISION_ERROR, DEF_WARNING, "", FALSE, "", $this->objDB );
            }

            // 削除済みチェック
            if ((int)$firstRecord->minrevisionno === -1) {
                return fncOutputError ( DEF_MESSAGE_CODE_DELETED_ERROR, DEF_WARNING, "", FALSE, "", $this->objDB );
            }

            // ステータスチェック
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

            // 見積原価修正の機能コード取得
            $editFunctionCode = DEF_FUNCTION_E3;

            $productCode = $firstRecord->strproductcode;
            $reviseCode = $firstRecord->strrevisecode;

            // 排他テーブルチェック（見積原価修正中でないことを確認)
            $check = $this->objDB->checkExclusiveStatus($editFunctionCode, $productCode, $reviseCode);
            if ($check === true) { // 排他制御が有効な場合
                return fncOutputError ( DEF_MESSAGE_CODE_EXCLUSIVE_CHECK_ERROR, DEF_WARNING, "$check->struserdisplayname", FALSE, "", $this->objDB );
            }

            // 見積原価マスタ削除
            $this->deteleMasterEstimate();

            // 受注マスタ、発注マスタの削除
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

            // 製品マスタの削除
            $this->deleteMasterProduct();
        }

        return;
    }

    /**
    * DB登録用関数
    *
    *	製品マスタ内の削除処理を行う
    *
    *	@return
    */
    protected function deleteMasterProduct() {
        $this->updateDeleteRecordForProduct();
        $this->insertDeleteRecordForProduct();
        return;
    }

    /**
    * DB登録用関数
    *
    *	製品マスタの削除対象の製品の情報を更新する
    *
    *	@return true
    */
    protected function updateDeleteRecordForProduct() {

        $strQuery = "UPDATE m_product SET bytinvalidflag = true, strproductcode = strproductcode || '_del'";
        $strQuery .= " WHERE lngproductno = ". $this->productNo;
        $strQuery .= " AND strrevisecode = '". $this->reviseCode. "'";

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }


    /**
    * DB登録用関数
    *
    *	製品マスタの削除レコードの登録を行う
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

        // クエリの生成
        $strQuery = $this->makeInsertSelectQuery($table, $data, $condition);

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
    * DB登録用関数
    *
    *	見積原価マスタ内の削除処理を行う
    *   
    *	@return true
    */
    protected function deteleMasterEstimate() {
        $this->updateDeleteRecordForEstimate();
        $this->insertDeleteRecordForEstimate();
    }

    /**
    * DB登録用関数
    *
    *	見積原価マスタの削除対象の見積原価の情報を更新する
    *
    *	@return true
    */
    protected function updateDeleteRecordForEstimate() {

        $strQuery = "UPDATE m_estimate SET strproductcode = strproductcode || '_del'";
        $strQuery .= " WHERE lngestimateno = ". $this->estimateNo;

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }
    
    /**
    * DB登録用関数
    *
    *	見積原価マスタへの削除レコードの登録を行う
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

        // クエリの生成
        $strQuery = $this->makeInsertSelectQuery($table, $data, $condition);

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
    * DB登録用関数
    *
    *	受注マスタ内の削除処理を行う
    *
    *   @param object $record 明細行の検索結果
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
    * DB登録用関数
    *
    *	発注マスタへ内の削除処理を行う
    *
    *   @param object $record 明細行の検索結果
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
