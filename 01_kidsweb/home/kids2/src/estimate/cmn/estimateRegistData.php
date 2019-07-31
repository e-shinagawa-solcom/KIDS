<?php

require_once ('conf.inc');
require_once ( LIB_ROOT . "lib.php");

require_once ( SRC_ROOT . "estimate/cmn/const/workSheetConst.php");

// 登録用データ作成クラス
class estimateRegistData {
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


    protected $objDB;

    protected $companyCodeList;

    public function __construct() {

    }
    

    // 必要なパラメータをクラスにセットする
    public function setParam($regist, $inputUserCode, $objDB) {
        if (is_array($regist)) {
            // 登録用のパラメータをセット
            $this->headerData = $regist['headerData'];
            $this->rowDataList = $regist['rowDataList'];
            $this->calculatedData = $regist['calculatedData'];

            // 入力者のユーザーコードをセット
            $this->inputUserCode = $inputUserCode;

            // DBクラスのセット
            $this->objDB = $objDB;

            // グループコードの取得
            $groupRecord = $this->objDB->getGroupRecordForDisplay($this->headerData[workSheetConst::INCHARGE_GROUP_CODE]);
            $this->groupCode = $groupRecord->lnggroupcode;

            // ユーザーコードの取得
            $inchargeUserRecord = $this->objDB->getUserRecordForDisplay($this->headerData[workSheetConst::INCHARGE_USER_CODE]);
            $this->inchargeUserCode = $inchargeUserRecord->lngusercode;

            // 開発担当者コードの取得
            $developUserRecord = $this->objDB->getUserRecordForDisplay($this->headerData[workSheetConst::DEVELOP_USER_CODE]);
            $this->developUserCode = $developUserRecord->lngusercode;

            // 表示会社コードをキーにもつ会社コードの配列取得
            $this->companyCodeList = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", "Array", "", $this->objDB );
            
        } else {
            return false;
        }
        return true;
    }

    // 見積原価登録時に必要なデータを作成し、セットする
    protected function setEstimateRegistParam() {
        // ワークシート上の製品コードの取得
        $productCode = $this->headerData[workSheetConst::PRODUCT_CODE];

		// 製品コードが存在する場合は再販
		if ($productCode) {

			// 指定製品のコードが製品マスタに存在するか確認し、リバイスコードの最大値を取得する
            $currentRecord = $this->objDB->getCurrentRecordForProductCode($productCode);

			if ($currentRecord !== false) {
                // 製品番号を取得
                $productNo = $currentRecord->lngproductno;
                
                // 最大のリバイスコードを取得
                $maxReviceCode = (int)$currentRecord->strrevisecode;
                
				// リバイスコードの設定
				$reviseCode =  $maxReviceCode + 1;
				$reviseCode =  str_pad($reviseCode, 2, 0, STR_PAD_LEFT);
	
			} else {
				// エラー処理
			}
        // 製品コードが存在しないなら新規
		} else {
            // 製品番号を取得
            $productNo = fncGetSequence("m_product.lngproductno", $this->objDB);

            // 製品コードを発行
            $productCode = str_pad($productNo, 5, 0, STR_PAD_LEFT);
			
			// リバイスコードの設定
			$reviseCode = '00';
        }
        
        // パラメータのセット
        $this->productNo = $productNo;
        $this->productCode = $productCode;
        $this->reviseCode = $reviseCode;

		// 見積原価番号を取得
		$estimateNo = fncGetSequence("m_Estimate.lngEstimateNo", $this->objDB);
        $this->estimateNo = $estimateNo;

		// 商品化企画コードを取得
		$goodsPlanCode = fncGetSequence("t_goodsplan.lnggoodsplancode", $this->objDB);
		$this->goodsPlanCode = $goodsPlanCode;

		// リビジョンNoの設定
        $this->revisionNo = 0;
        
        return true;        
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

    /**
    * DB登録用関数
    *
    *	見積原価登録を行う
    *   
    *	@return true
    */
    public function regist() {
        // 登録時に必要なデータを作成し、セットする
        $this->setEstimateRegistParam();

        // 製品マスタの登録処理
        $this->registMasterProduct();

        // 見積原価マスタの登録処理
        $this->registMasterEstimate();

        // 商品化企画テーブルの登録処理
        $this->registTableGoodsPlan();

        $estimateDetailNo = 0; // 見積原価明細番号
        $receiveDetailNo = 0;  // 受注明細番号
        $orderDetailNo = 0;    // 発注明細番号

        $rowDataList = $this->rowDataList;
        
        // 明細行の登録
        foreach ($rowDataList as $rowData) {
            // 見積原価明細番号のインクリメント
            ++$estimateDetailNo;

            // 見積原価明細テーブルの登録処理
            $this->registTableEstimateDetail($rowData, $estimateDetailNo);

            $salesOrder = $rowData['salesOrder'];

            // 受注の場合
            if ($salesOrder === DEF_ATTRIBUTE_CLIENT) {
                // 受注明細番号のインクリメント
                ++$receiveDetailNo;

                // 受注番号を取得
                $receiveNo = fncGetSequence("m_receive.lngReceiveNo", $this->objDB);

                // 受注コードの取得
                if (!isset($receiveCode)) {
                    $receiveCode = $this->objDB->getReceiveCode();
                }

                // 受注マスタ登録処理
                $this->registMasterReceive($rowData, $receiveNo, $receiveCode);

                // 受注明細テーブル登録処理
                $this->registTableReceiveDetail($rowData, $receiveNo, $receiveDetailNo, $estimateDetailNo);

            // 発注の場合
            } else if ($salesOrder === DEF_ATTRIBUTE_SUPPLIER) {
                if ($rowData['divisionSubject'] != DEF_STOCK_SUBJECT_CODE_CHARGE
                    && $rowData['divisionSubject'] != DEF_STOCK_SUBJECT_CODE_EXPENSE) {
                    // 発注明細番号のインクリメント
                    ++$orderDetailNo;

                    // 発注番号を取得
                    $orderNo = fncGetSequence("m_Order.lngOrderNo", $this->objDB);

                    // 発注コードの取得
                    if (!isset($orderCode)) {
                        $orderCode = $this->objDB->getOrderCode();
                    }

                    // 発注マスタ登録処理
                    $this->registMasterOrder($rowData, $orderNo, $orderCode);

                    // 発注明細テーブル登録処理
                    $this->registTableOrderDetail($rowData, $orderNo, $orderDetailNo, $estimateDetailNo);
                }
            }
        }
        
        return true;
    }


    /**
    * DB登録用関数
    *
    *	製品マスタへの登録を行う
    *
    *	@return true
    */
    protected function registMasterProduct() {

        $table = 'm_product';

        $data = array(
            'lngproductno' => $this->productNo,
            'strproductcode' => "'". $this->productCode. "'",
            'strproductname' => "'". $this->headerData[workSheetConst::PRODUCT_NAME]. "'",
            'strproductenglishname' => "'". $this->headerData[workSheetConst::PRODUCT_ENGLISH_NAME]. "'",
            'lnginchargegroupcode' => $this->groupCode,
            'lnginchargeusercode' => $this->inchargeUserCode,
            'lngdevelopusercode' => $this->developUserCode,
            'lnginputusercode' => $this->inputUserCode,
            'lngcartonquantity' => $this->headerData[workSheetConst::CARTON_QUANTITY],
            'lngproductionquantity' => $this->headerData[workSheetConst::PRODUCTION_QUANTITY],
            // 'curproductprice' => '',
            'curretailprice' => $this->headerData[workSheetConst::RETAIL_PRICE],
            'bytinvalidflag' => 'false',
            'dtminsertdate' => 'NOW()',
            'dtmupdatedate' => 'NOW()',
            'lngrevisionno' => $this->revisionNo,
            'strrevisecode' => "'". $this->reviseCode. "'"
        );
        // クエリの生成
        $strQuery = $this->makeInsertQuery($table, $data);
        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }
    
    /**
    * DB登録用関数
    *
    *	見積原価マスタへの登録を行う
    *   
    *	@return true
    */
    protected function registMasterEstimate() {
        $table = 'm_estimate';

        // 上代
        $retailPrice = $this->headerData[workSheetConst::RETAIL_PRICE];
        // 償却数
        $productionQuantity = $this->headerData[workSheetConst::PRODUCTION_QUANTITY];

        // 合計金額の計算
        $totalPrice = $retailPrice * $productionQuantity;

        $data = array(
            'lngestimateno' => $this->estimateNo,
            'lngrevisionno'=> $this->revisionNo,
            'strproductcode'=> "'". $this->productCode. "'",
            'strrevisecode' => "'". $this->reviseCode. "'",
            'bytdecisionflag' => 'true',
            'lngestimatestatuscode'=> DEF_ESTIMATE_APPROVE,
            'curfixedcost' => $this->calculatedData[workSheetConst::ORDER_FIXED_COST_FIXED_COST],
            'curmembercost' => $this->calculatedData[workSheetConst::MEMBER_COST],
            'curtotalprice' => $totalPrice,
            'curmanufacturingcost' => $this->calculatedData[workSheetConst::MANUFACTURING_COST],
            'cursalesamount' => $this->calculatedData[workSheetConst::SALES_AMOUNT],
            'curprofit' => $this->calculatedData[workSheetConst::PROFIT],
            'lnginputusercode'=> $this->inputUserCode,
            'bytinvalidflag' => 'false',
            'dtminsertdate' => 'NOW()',
            'lngproductionquantity' => $productionQuantity,
            'lngtempno' => 'NULL',
            'strnote' => 'NULL',
        );

        // クエリの生成
        $strQuery = $this->makeInsertQuery($table, $data);

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
    * DB登録用関数
    *
    *	見積原価明細テーブルへの登録を行う
    *
    *   @param array $rowData 行のデータ
    *   @param integer $estimateDetailNo 見積原価明細番号
    *
    *	@return true
    */
    protected function registTableEstimateDetail($rowData, $estimateDetailNo) {
        // テーブル名の設定
        $table = 't_estimatedetail';

        // 受注の場合
        if ($rowData['salesOrder'] === DEF_ATTRIBUTE_CLIENT) {
            $stockSubjectCode = 0;
            $stockItemCode = 0;
            $salesDivisionCode = $rowData['divisionSubject'];
            $salesClassCode = $rowData['classItem'];
        // 発注の場合
        } else if ($rowData['salesOrder'] === DEF_ATTRIBUTE_SUPPLIER) {
            $stockSubjectCode = $rowData['divisionSubject'];
            $stockItemCode = $rowData['classItem'];
            $salesDivisionCode = 0;
            $salesClassCode = 0;
        } else {
            return false;
        }

        // 登録データの作成
        $data = array(
            'lngestimateno' => $this->estimateNo,
            'lngestimatedetailno' => $estimateDetailNo,
            'lngrevisionno' => $this->revisionNo,
            'lngstocksubjectcode'=> $stockSubjectCode,
            'lngstockitemcode' => $stockItemCode,
            'lngcustomercompanycode' => $rowData['customerCompanyCode'],
            'bytpayofftargetflag' => $rowData['payoff'] == '○' ? 'true' : 'false',
            'bytpercentinputflag'=> $rowData['percentInputFlag'],
            'dblpercent' => $rowData['percentInputFlag'] === true ? $rowData['percent'] : 'null',
            'lngmonetaryunitcode' => $rowData['monetary'],
            'lngmonetaryratecode' => DEF_MONETARY_RATE_CODE_COMPANY_LOCAL,
            'curconversionrate' => $rowData['acqiredRate'],
            'lngproductquantity' => $rowData['quantity'],
            'curproductprice' => $rowData['percentInputFlag'] === 'false' ? $rowData['price'] : 'null',
            'curproductrate' => $rowData['percentInputFlag'] === 'true' ? $rowData['price'] : 'null',
            'cursubtotalprice' => $rowData['calculatedSubtotal'],
            'strnote' => "'". $rowData['note']. "'",
            'lngsortkey' => $estimateDetailNo,
            'lngsalesdivisioncode' => $salesDivisionCode,
            'lngsalesclasscode' => $salesClassCode
        );
        
        // クエリの生成
        $strQuery = $this->makeInsertQuery($table, $data);
        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
    * DB登録用関数
    *
    *	受注マスタへの登録を行う
    *
    *   @param array $rowData 行のデータ
    *   @param string $receiveCode 受注コード
    *   
    *	@return true
    */
    protected function registMasterReceive($rowData, $receiveNo, $receiveCode) {
        // テーブルの設定 
        $table = 'm_receive';

        // 登録データの作成
        $data = array(
            'lngreceiveno' => $receiveNo,
            'lngrevisionno' => $this->revisionNo,
            'strreceivecode' => "'". $receiveCode. "'",
            'strrevisecode' => "'". $this->reviseCode. "'",
            'dtmappropriationdate' => 'NOW()',
            'lngcustomercompanycode' => $this->companyCodeList[$rowData['customerCompany']],
            'lnggroupcode' => $this->groupCode,
            'lngusercode' => $this->inchargeUserCode,
            'lngreceivestatuscode' => DEF_RECEIVE_PREORDER,
            'lngmonetaryunitcode' => $rowData['monetary'],
            'lngmonetaryratecode' => DEF_MONETARY_RATE_CODE_COMPANY_LOCAL,
            'curconversionrate' => $rowData['acqiredRate'],
            'lnginputusercode' => $this->inputUserCode,
            'bytinvalidflag' => 'false',
            'dtminsertdate' => 'NOW()',
            'strcustomerreceivecode' => 'NULL'
        );

        // クエリの生成
        $strQuery = $this->makeInsertQuery($table, $data);
        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }


    /**
    * DB登録用関数
    *
    *	受注明細テーブルへの登録を行う
    *
    *   @param array $rowData 行のデータ
    *   @param integer $receiveDetailNo 受注明細番号
    *   @param integer $estimateDetailNo 見積原価明細番号    
    *   
    *	@return true
    */
    protected function registTableReceiveDetail($rowData, $receiveNo, $receiveDetailNo, $estimateDetailNo) {
        // テーブルの設定 
        $table = 't_receivedetail';

        $data = array(
            'lngreceiveno' => $receiveNo,
            'lngreceivedetailno' => $receiveDetailNo,
            'lngrevisionno' => $this->revisionNo,
            'strproductcode' => "'". $this->productCode. "'",
            'strrevisecode' => "'". $this->revisecode. "'",
            'lngsalesclasscode' => $rowData['classItemCode'],
            'dtmdeliverydate' => "TO_TIMESTAMP('". $rowData['delivery']. "', 'YYYY/MM/DD')",
            'lngconversionclasscode' => 'NULL',
            'curproductprice' => $rowData['price'],
            'lngproductquantity' => $rowData['quantity'],
            'lngproductunitcode' => 'NULL',
            'lngtaxclasscode' => 'NULL',
            'lngtaxcode' => 'NULL',
            'curtaxprice' => 'NULL',
            'cursubtotalprice' => $rowData['calculatedSubtotal'],
            'strnote' => "'". $rowData['note']. "'",
            'lngsortkey' => $receiveDetailNo,
            'lngestimateno' => $this->estimateNo,
            'lngestimatedetailno' => $estimateDetailNo
        );

        // クエリの生成
        $strQuery = $this->makeInsertQuery($table, $data);

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);
    }

    /**
    * DB登録用関数
    *
    *	発注マスタへの登録を行う
    *
    *   @param array $rowData 行のデータ
    *   @param string $orderCode 発注コード
    *   
    *	@return true
    */
    protected function registMasterOrder($rowData, $orderNo, $orderCode) {
        // テーブルの設定
        $table = 'm_order';

        $data = array(
            'lngorderno' => $orderNo,
            'lngrevisionno' => $this->revisionNo,
            'strordercode' => "'". $orderCode. "'",
            'strrevisecode' => "'". $this->reviseCode. "'",
            'dtmappropriationdate' => 'NOW()',
            'lngcustomercompanycode' => $this->companyCodeList[$rowData['customerCompany']],
            'lnggroupcode' => $this->groupCode,
            'lngusercode' => $this->inchargeUserCode,
            'lngorderstatuscode' => DEF_ORDER_TEMPORARY,
            'lngmonetaryunitcode' => $rowData['monetary'],
            'lngmonetaryratecode' => DEF_MONETARY_RATE_CODE_COMPANY_LOCAL,
            'curconversionrate' => $rowData['acqiredRate'],
            'lngpayconditioncode' => 'NULL',
            'lngdeliveryplacecode' => 'NULL',
            'dtmexpirationdate' => 'NULL',
            'lnginputusercode' => $this->inputUserCode,
            'bytinvalidflag' => 'false',
            'dtminsertdate' => 'NOW()'
        );

        // クエリの生成
        $strQuery = $this->makeInsertQuery($table, $data);
        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
    * DB登録用関数
    *
    *	発注明細テーブルへの登録を行う
    *
    *   @param array $rowData 行のデータ
    *   @param integer $orderDetailNo 発注明細番号
    *   @param integer $estimateDetailNo 見積原価明細番号    
    *   
    *	@return true
    */
    protected function registTableOrderDetail($rowData, $orderNo, $orderDetailNo, $estimateDetailNo) {
        // テーブルの設定
        $table = 't_orderdetail';

        $data = array(
            'lngorderno' => $orderNo,
            'lngorderdetailno' => $orderDetailNo,
            'lngrevisionno' => $this->revisionNo,
            'strproductcode' => "'". $this->productCode. "'",
            'strrevisecode' => "'". $this->reviseCode. "'",
            'lngstocksubjectcode' => $rowData['divisionSubjectCode'],
            'lngstockitemcode' => $rowData['classItem'],
            'dtmdeliverydate' => "TO_TIMESTAMP('". $rowData['delivery']. "', 'YYYY/MM/DD')",
            'lngdeliverymethodcode' => 'NULL',
            'lngconversionclasscode' => DEF_CONVERSION_SEIHIN,
            'curproductprice' => $rowData['price'],
            'lngproductquantity' => $rowData['quantity'],
            'lngproductunitcode' => DEF_PRODUCTUNIT_PCS,
            'lngtaxclasscode' => 'NULL',
            'lngtaxcode' => 'NULL',
            'curtaxprice' => 'NULL',
            'cursubtotalprice' => $rowData['calculatedSubtotal'],
            'strnote' => "'". $rowData['note']. "'",
            'strmoldno' => 'NULL',
            'lngsortkey' => $orderDetailNo,
            'lngestimateno' => $this->estimateNo,
            'lngestimatedetailno' => $estimateDetailNo
        );
        // クエリの生成
        $strQuery = $this->makeInsertQuery($table, $data);

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

    }

    /**
    * DB登録用関数
    *
    *	商品化企画テーブルへの登録を行う 
    *   
    *	@return true
    */
    protected function registTableGoodsPlan() {

        $table = 't_goodsplan';

        $data = array(
            'lnggoodsplancode' => $this->goodsPlanCode,
            'lngrevisionno' => $this->revisionNo,
            'lngproductno' => $this->productNo,
            'dtmcreationdate' => 'NOW()',
            'dtmrevisiondate' => 'NOW()',
            'lnggoodsplanprogresscode' => DEF_GOODSPLAN_AFOOT,
            'lnginputusercode' => $this->inputUserCode
        );

        // クエリの生成
        $strQuery = $this->makeInsertQuery($table, $data);

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }
}
