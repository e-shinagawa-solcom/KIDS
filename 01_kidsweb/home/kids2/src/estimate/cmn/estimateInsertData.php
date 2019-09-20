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
}
