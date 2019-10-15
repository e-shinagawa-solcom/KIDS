<?php

require_once ('conf.inc');
require_once ( LIB_FILE );

require_once ( SRC_ROOT . "estimate/cmn/const/workSheetConst.php");

// 登録用データ作成クラス
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

    // 見積原価計算書に入力されたデータをセットする
    protected function setParam($input, $inputUserCode, $objDB) {
        // 登録用のパラメータをセット
        $this->headerData = $input['headerData'];
        $this->rowDataList = $input['rowDataList'];
        $this->calculatedData = $input['calculatedData'];

        // 入力者のユーザーコードをセット
        $this->inputUserCode = $inputUserCode;

        // DBクラスのセット
        $this->objDB = $objDB;

        return;
    }

    // 製品コードを出力する
    public function getProductCode() {
        return $this->productCode;
    }

    // リバイスコードを出力する
    public function getReviseCode() {
        return $this->reviseCode;
    }

    // 見積原価番号を出力する
    public function getEstimateNo() {
        return $this->estimateNo;
    }

    /**
    * DB登録用関数
    *
    *	登録用のINSERT文を生成する
    *   
    *   @param array $array 登録データ（キーにカラム名を持つデータ配列）
    *	@return boolean
    *	@access protected
    */
    protected function makeInsertQuery($table, $array) {
        foreach ($array as $key => $value) {
            // 列情報のセット
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
    * DB登録用関数
    *
    *	登録用のINSERT文を生成する
    *   
    *   @param array $array 登録データ（キーにカラム名を持つデータ配列）
    *   @param string $condition 検索条件
    *   @param $returning 返却するカラム
    *
    *	@return boolean
    *	@access protected
    */
    protected function makeInsertSelectQuery($table, $array, $condition = null, $returning = null) {

        if ($condition) { // 検索条件が指定されているとき
            foreach ($array as $key => $value) {
                // 列情報のセット
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
    * DB登録用関数
    *
    *	受注マスタへの削除対象の受注情報を更新する
    *
    *   @param string $receiveNo 受注番号
    *   @param string $revisionNo 受注リビジョン番号
    *   
    *	@return true
    */
    protected function updateDeleteRecordForReceive($receiveNo) {

        $strQuery = "UPDATE m_receive SET strreceivecode = '*' || strreceivecode || '*'";
        $strQuery .= " WHERE lngreceiveno = ". $receiveNo;

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }



    /**
    * DB登録用関数
    *
    *	受注マスタへの削除レコードの登録を行う
    *
    *   @param string $receiveNo 受注番号
    *   @param string $revisionNo コピー元の受注リビジョン番号(基本的には最新リビジョン)
    *   
    *	@return true
    */
    protected function insertDeleteRecordForReceive($receiveNo, $revisionNo) {
        // テーブルの設定 
        $table = 'm_receive';

        if (!strlen($receiveNo) || !strlen($revisionNo)) {
            // 受注番号とリビジョン番号がセットされていないまたは空文字の場合は処理しない
            return false;
        }

        // 登録データの作成
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
    *	発注マスタへの削除レコードの登録を行う
    *
    *   @param array $rowData 行のデータ
    *   @param string $orderCode 発注コード
    *   
    *	@return true
    */
    protected function insertDeleteRecordForOrder($orderNo, $revisionNo) {
        // テーブルの設定
        $table = 'm_order';

        if (!strlen($orderNo) || !strlen($revisionNo)) {
            // 受注番号とリビジョン番号がセットされていないまたは空文字の場合は処理しない
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

        // クエリの生成
        $strQuery = $this->makeInsertSelectQuery($table, $data, $condition);

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }
}
