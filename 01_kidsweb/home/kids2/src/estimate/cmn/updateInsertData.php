<?php

require_once ('conf.inc');
require_once ( LIB_ROOT . "lib.php");

require_once ( SRC_ROOT . "estimate/cmn/const/workSheetConst.php");
require_once ( SRC_ROOT . "estimate/cmn/estimateInsertData.php");

// 登録用データ作成クラス
class updateInsertData extends estimateInsertData {

    public function __construct() {
      parent::__construct();
    }
    

    // 必要なパラメータをクラスにセットする
    public function setUpdateParam($update, $inputUserCode, $productCode, $reviseCode, $revisionNo, $objDB) {
        if (is_array($update)) {

            // 入力値、ユーザーコード、DBクラスのセット
            $this->setParam($update, $inputUserCode, $objDB);

            // 製品コードのセット
            $this->productCode = $productCode;

            // 再販コードのセット
            $this->reviseCode = $reviseCode;

            // リビジョン番号をセットする
            $this->revisionNo = $revisionNo;

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

    /**
    * DB登録用関数
    *
    *	見積原価登録を行う
    *   
    *	@return true
    */
    public function update() {

        // 製品マスタの登録処理
        $this->updateMasterProduct();

        // 見積原価マスタの登録処理
        $this->updateMasterEstimate();

        // 商品化企画テーブルの登録処理
        $this->updateTableGoodsPlan();

        $rowDataList = $this->rowDataList;


        // 行の情報から以前の見積原価番号を取得する
        $detailNoList = [];
        foreach ($rowDataList as $rowData) {
            $previousDetailNo = $rowData['previousDetailNo'];

            if ($previousDetailNo) {
                // 受注マスタ、発注マスタいずれかに登録される明細行について、以前の見積原価明細行番号を取得する
                if ($rowData['areaCode'] !== DEF_AREA_OTHER_COST_ORDER) {

                    $detailNoList[] = $previousDetailNo;
                }
            }
        }

        // 受注明細テーブル、発注明細テーブルの再採番したソートキーを取得
        $receiveTable = 't_receivedetail';
        $orderTable = 't_orderdetail';

        $receiveSortKeyList = $this->getRenumberSortKeyList($receiveTable, $detailNoList, $receiveNewSortKey); // 受注明細テーブル用
        $orderSortKeyList = $this->getRenumberSortKeyList($orderTable, $detailNoList, $orderNewSortKey);       // 発注明細テーブル用


        // 受注明細テーブル、発注明細テーブルの明細番号の最大値を取得し、セットする
        $search = "WHERE lngestimateno = ". $this->estimateNo; // 検索条件クエリ

        $this->maxReceiveDetailNo = $this->getFirstRecordValue($receiveTable, 'lngreceivedetailno', $search, 'max'); // 受注番号の最大値
        $this->maxOrderDetailNo = $this->getFirstRecordValue($orderTable, 'lngorderdetailno', $search, 'max');       // 発注番号の最大値


        // 見積原価番号及び見積原価リビジョン番号に紐付く受注明細テーブル、発注明細テーブルの最大のリビジョン番号を取得する
        $searchRevision = "WHERE lngestimateno = ". $this->estimateNo; // 検索条件クエリ
        $searchRevision .= " AND lngestimaterevisionno = ". ((int)$this->revisionNo - 1);

        $this->preReceiveRevisionNo = $this->getFirstRecordValue($receiveTable, 'lngrevisionno', $searchRevision, 'max');
        $this->preOrderRevisionNo = $this->getFirstRecordValue($orderTable, 'lngrevisionno', $searchRevision, 'max');


        // 見積原価番号に紐付く受注コード、発注コードを取得する
        $this->receiveCode = $this->getReceiveCode();
        $this->orderCode = $this->getOrderCode();

        
        // 明細行の登録
        foreach ($rowDataList as $rowData) {
            // 見積原価明細番号のインクリメント
            ++$estimateDetailNo;

            // 見積原価明細テーブルの登録処理
            $this->updateTableEstimateDetail($rowData, $estimateDetailNo);

            $salesOrder = $rowData['salesOrder'];

            // 受注の場合
            if ($salesOrder === DEF_ATTRIBUTE_CLIENT) {

                // 受注明細テーブル登録処理
                $this->updateTableReceiveDetail($rowData, $receiveSortKeyList, $receiveNewSortKey);

                // 受注マスタ登録処理
                $this->updateMasterReceive($rowData);

                if ($rowData['receiveStatusCode'] === DEF_RECEIVE_END
                    || $rowData['receiveStatusCode'] === DEF_RECEIVE_CLOSED) {
                    // 納品済、または締め済の場合
                    $this->updateTableSalesDetail($rowData);
                    $this->updateTableSlipDetail($rowData);

                }

            // 発注の場合
            } else if ($salesOrder === DEF_ATTRIBUTE_SUPPLIER) {
                // 発注で経費以外の場合
                if ($rowData['areaCode'] !== DEF_AREA_OTHER_COST_ORDER) {

                    // 発注明細テーブル登録処理
                    $this->updateTableOrderDetail($rowData, $orderSortKeyList, $orderNewSortKey);

                    // 発注マスタ登録処理
                    $this->updateMasterOrder($rowData);
                }

                if ($rowData['orderStatusCode'] === DEF_ORDER_ORDER) {
                    // 発注の場合
                    $this->updateTablePurchaseOrderDetail($rowData);

                } else if ($rowData['orderStatusCode'] === DEF_ORDER_END
                    || $rowData['orderStatusCode'] === DEF_ORDER_CLOSED) {

                    // 納品済、または締め済の場合
                    $this->updateTablePurchaseOrderDetail($rowData);
                    $this->updateTableStockDetail($rowData);
                }
 
            }
        }

        // 受注マスタ、発注マスタからデータの削除を行う
        $this->insertDeleteRecord();
        
        return true;
    }


    /**
    * DB登録用関数
    *
    *	製品マスタへ編集したデータの編集登録を行う
    *
    *	@return true
    */
    protected function updateMasterProduct() {

        $table = 'm_product';

        // 登録データ
        $data = array(
            'lngproductno' => $table. ".lngproductno",
            'strproductcode' => "'". $this->productCode. "'",
            'strproductname' => "'". $this->headerData[workSheetConst::PRODUCT_NAME]. "'",
            'strproductenglishname' => "'". $this->headerData[workSheetConst::PRODUCT_ENGLISH_NAME]. "'",
            'strgoodscode' => $table. ".strgoodscode",
            'strgoodsname' => $table. ".strgoodsname",
            'lnginchargegroupcode' => $this->groupCode,
            'lnginchargeusercode' => $this->inchargeUserCode,
            'lngdevelopusercode' => $this->developUserCode,
            'lnginputusercode' => $this->inputUserCode,
            'lngcustomercompanycode' => $table. ".lngcustomercompanycode",
            'lngcustomergroupcode' =>  $table. ".lngcustomergroupcode",
            'lngcustomerusercode' =>  $table. ".lngcustomerusercode",
            'strcustomerusername' =>  $table. ".strcustomerusername",
            'lngpackingunitcode' =>  $table. ".lngpackingunitcode",
            'lngproductunitcode' =>  $table. ".lngproductunitcode",
            'lngboxquantity' => $table. ".lngboxquantity",
            'lngcartonquantity' => $this->headerData[workSheetConst::CARTON_QUANTITY],
            'lngproductionquantity' => $this->headerData[workSheetConst::PRODUCTION_QUANTITY],
            'lngproductionunitcode' => $table. ".lngproductionunitcode",
            'lngfirstdeliveryquantity' => $table. ".lngfirstdeliveryquantity",
            'lngfirstdeliveryunitcode' => $table. ".lngfirstdeliveryunitcode",
            'lngfactorycode' => $table. ".lngfactorycode",
            'lngassemblyfactorycode' => $table. ".lngassemblyfactorycode",
            'lngdeliveryplacecode' => $table. ".lngdeliveryplacecode",
            'dtmdeliverylimitdate' => $table. ".dtmdeliverylimitdate",
            'curproductprice' => $table. ".curproductprice",
            'curretailprice' => $this->headerData[workSheetConst::RETAIL_PRICE],
            'lngtargetagecode' => $table. ".lngtargetagecode",
            'lngroyalty' => $table. ".lngroyalty",
            'lngcertificateclasscode' => $table. ".lngcertificateclasscode",
            'lngcopyrightcode' => $table. ".lngcopyrightcode",
            'strcopyrightdisplaystamp' => $table. ".strcopyrightdisplaystamp",
            'strcopyrightdisplayprint' => $table. ".strcopyrightdisplayprint",
            'lngproductformcode' => $table. ".lngproductformcode",
            'strproductcomposition' => $table. ".strproductcomposition",
            'strassemblycontents' => $table. ".strassemblycontents",
            'strspecificationdetails' => $table. ".strspecificationdetails",
            'strnote' => $table. ".strnote",
            'bytinvalidflag' => $table. ".bytinvalidflag",
            'dtminsertdate' => $table. ".dtminsertdate",
            'dtmupdatedate' => "NOW()",
            'strcopyrightnote' => $table. ".strcopyrightnote",
            'lngcategorycode' => $table. ".lngcategorycode",
            'lngrevisionno' => $table. ".lngrevisionno + 1",
            'strrevisecode' => "'". $this->reviseCode. "'"
        );

        // 検索、結合条件
        $join = "INNER JOIN (";
        $join .=     "SELECT";
        $join .=         " ". $table. ".lngproductno,";
        $join .=         " ". $table. ".strrevisecode,";
        $join .=         " MAX(". $table. ".lngrevisionno) AS lngrevisionno";
        $join .=     " FROM ". $table;
        $join .=     " WHERE";
        $join .=         " ". $table. ".strproductcode = '". $this->productCode. "'";
        $join .=         " AND ". $table. ".strrevisecode = '". $this->reviseCode. "'";
        $join .=     " GROUP BY";
        $join .=         " ". $table. ".lngproductno,";
        $join .=         " ". $table. ".strrevisecode";
        $join .= ") A";
        $join .= " ON A.lngproductno =". $table. ".lngproductno";
        $join .= " AND A.strrevisecode =". $table. ".strrevisecode";
        $join .= " AND A.lngrevisionno =". $table. ".lngrevisionno";

        $returning = 'lngproductno, lngrevisionno';

        // クエリの生成
        $strQuery = $this->makeInsertSelectQuery($table, $data, $join, $returning);
        
        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $result = pg_fetch_object($resultID, 0);

        // 
        $this->productNo = $result->lngproductno;

        // 製品リビジョン番号をセットする
        $this->productRevisionNo = $result->lngrevisionno;

        $this->objDB->freeResult($resultID);

        return true;
    }
    
// ここから

    /**
    * DB登録用関数
    *
    *	見積原価マスタへの編集登録を行う
    *   
    *	@return true
    */
    protected function updateMasterEstimate() {
        $table = 'm_estimate';

        // 上代
        $retailPrice = $this->headerData[workSheetConst::RETAIL_PRICE];
        // 償却数
        $productionQuantity = $this->headerData[workSheetConst::PRODUCTION_QUANTITY];

        // 合計金額の計算
        $totalPrice = $retailPrice * $productionQuantity;

        $data = array(
            'lngestimateno' => $table. '.lngestimateno',
            'lngrevisionno'=> $table. '.lngrevisionno + 1',
            'strproductcode'=> "'". $this->productCode. "'",
            'strrevisecode' => "'". $this->reviseCode. "'",
            'bytdecisionflag' => $table. '.bytdecisionflag',
            'lngestimatestatuscode'=> $table. '.lngestimatestatuscode',
            'curfixedcost' => $this->calculatedData[workSheetConst::DEPRECIATION_COST],
            'curmembercost' => $this->calculatedData[workSheetConst::MEMBER_COST],
            'curtotalprice' => $this->calculatedData[workSheetConst::PROFIT],
            'curmanufacturingcost' => $this->calculatedData[workSheetConst::MANUFACTURING_COST],
            'cursalesamount' => $this->calculatedData[workSheetConst::PRODUCT_TOTAL_PRICE],
            'curprofit' => $this->calculatedData[workSheetConst::OPERATING_PROFIT],
            'lnginputusercode'=> $this->inputUserCode,
            'bytinvalidflag' => $table. '.bytinvalidflag',
            'dtminsertdate' => 'NOW()',
            'lngproductionquantity' => $productionQuantity,
            'lngtempno' => $table. '.lngtempno',
            'strnote' => $table. '.strnote',
            'lngproductrevisionno' => $this->productRevisionNo,
        );

        // 検索、結合条件
        $join = "INNER JOIN";
        $join .= " (";
        $join .=     " SELECT";
        $join .=     " ". $table. ".lngestimateno,";
        $join .=     " MAX(". $table. ".lngrevisionno) AS lngrevisionno";
        $join .=     " FROM ". $table. "";
        $join .=     " WHERE ". $table. ".strproductcode = '". $this->productCode. "'";
        $join .=     " AND ". $table. ".strrevisecode = '". $this->reviseCode."'";
        $join .=     " GROUP BY ". $table. ".lngestimateno";
        $join .= " ) A";
        $join .= " ON A.lngestimateno = ". $table. ".lngestimateno";
        $join .= " AND A.lngrevisionno = ". $table. ".lngrevisionno";

        $returning = 'lngestimateno, lngrevisionno';

        // クエリの生成
        $strQuery = $this->makeInsertSelectQuery($table, $data, $join, $returning);
        
        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $result = pg_fetch_object($resultID, 0);

        // 見積原価番号をセットする
        $this->estimateNo = $result->lngestimateno;

        // リビジョン番号をセットする
        $this->revisionNo = $result->lngrevisionno;

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
    * DB登録用関数
    *
    *	見積原価明細テーブルへの編集登録を行う
    *
    *   @param array $rowData 行のデータ
    *   @param integer $estimateDetailNo 見積原価明細番号
    *
    *	@return true
    */
    protected function updateTableEstimateDetail(&$rowData, $estimateDetailNo) {
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

        $previousRevisionNo = $this->revisionNo - 1;

        // 登録データの作成
        $data = array(
            'lngestimateno' => $this->estimateNo,
            'lngestimatedetailno' => $estimateDetailNo,
            'lngrevisionno' => $this->revisionNo,
            'lngstocksubjectcode'=> $stockSubjectCode,
            'lngstockitemcode' => $stockItemCode,
            'lngcustomercompanycode' => $this->companyCodeList[$rowData['customerCompany']],
            'dtmdelivery' => $rowData['delivery'] ? "TO_TIMESTAMP('". $rowData['delivery']. "', 'YYYY/MM/DD')" : 'null',
            'bytpayofftargetflag' => $rowData['payoff'] == '○' ? 'true' : 'false',
            'bytpercentinputflag'=> $rowData['percentInputFlag'],
            'lngmonetaryunitcode' => $rowData['monetary'],
            'lngmonetaryratecode' => DEF_MONETARY_RATE_CODE_COMPANY_LOCAL,
            'curconversionrate' => $rowData['conversionRate'],
            'lngproductquantity' => $rowData['quantity'],
            'curproductprice' => $rowData['percentInputFlag'] === false ? $rowData['price'] : 'null',
            'curproductrate' => $rowData['percentInputFlag'] === true ? $rowData['percent'] : 'null',
            'cursubtotalprice' => $rowData['subtotal'],
            'strnote' => "'". $rowData['note']. "'",
            'lngsortkey' => $estimateDetailNo,
            'lngsalesdivisioncode' => $salesDivisionCode,
            'lngsalesclasscode' => $salesClassCode
        ); 
        
        // クエリの生成
        $strQuery = $this->makeInsertSelectQuery($table, $data);
        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        // 登録に使用した明細行番号を行にセットする
        $rowData['currentDetailNo'] = $estimateDetailNo;

        return true;
    }

    /**
    * DB登録用関数
    *
    *	受注明細テーブルへの登録を行う
    *
    *   @param array $rowData 行のデータ
    *   @param array $receiveDetailNoList 受注明細番号リスト
    *   @param integer $defaultSortKey 新しく追加する明細用のソートキー
    *   
    *	@return true
    */
    protected function updateTableReceiveDetail(&$rowData, $receiveSortKeyList, &$defaultSortKey) {
        // テーブルの設定 
        $table = 't_receivedetail';

        $previousRevisionNo = $this->revisionNo - 1;      // 1つ前の見積原価のリビジョン番号

        $revisionNo = $this->preReceiveRevisionNo + 1;    // 登録に使用する受注のリビジョン番号

        $previousDetailNo = $rowData['previousDetailNo']; // 以前の見積原価明細番号（検索用）
        $estimateDetailNo = $rowData['currentDetailNo'];  // 今回の見積原価明細番号（登録用)

        $sortKey = $receiveSortKeyList[$previousDetailNo];

        if ($previousDetailNo) {
            $data = array(
                'lngreceiveno' => 'lngreceiveno',
                'lngreceivedetailno' => 'lngreceivedetailno',
                'lngrevisionno' => $revisionNo,
                'strproductcode' => "'". $this->productCode. "'",
                'strrevisecode' => "'". $this->reviseCode. "'",
                'lngsalesclasscode' => $rowData['classItem'],
                'dtmdeliverydate' => "TO_TIMESTAMP('". $rowData['delivery']. "', 'YYYY/MM/DD')",
                'lngconversionclasscode' => 'lngconversionclasscode',
                'curproductprice' => $rowData['price'],
                'lngproductquantity' => $rowData['quantity'],
                'lngproductunitcode' => 'lngproductunitcode',
                'cursubtotalprice' => $rowData['subtotal'],
                'strnote' => "'". $rowData['note']. "'",
                'lngsortkey' => $sortKey,
                'lngestimateno' => $this->estimateNo,
                'lngestimatedetailno' => $estimateDetailNo,
                'lngestimaterevisionno' => $this->revisionNo
            );

            $condition = "WHERE lngestimateno =". $this->estimateNo;
            $condition .= " AND lngestimatedetailno = ". $previousDetailNo;
            $condition .= " AND lngestimaterevisionno = ". $previousRevisionNo;

            $returning = "lngreceiveno";

            // クエリの生成
            $strQuery = $this->makeInsertSelectQuery($table, $data, $condition, $returning);

            // クエリの実行
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

            // 受注番号、リビジョン番号を取得
            $result = pg_fetch_object($resultID, 0);

            // 行情報に取得した値をセットし返却
            $rowData['receiveNo'] = $result->lngreceiveno;

            $this->objDB->freeResult($resultID);

            return;            

        } else {
            // 受注番号を取得
            $receiveNo = fncGetSequence("m_receive.lngReceiveNo", $this->objDB);

            // 受注明細番号の最大値のインクリメント
            $this->maxReceiveDetailNo += 1;

            $data = array(
                'lngreceiveno' => $receiveNo,
                'lngreceivedetailno' => $this->maxReceiveDetailNo,
                'lngrevisionno' => $revisionNo,
                'strproductcode' => "'". $this->productCode. "'",
                'strrevisecode' => "'". $this->reviseCode. "'",
                'lngsalesclasscode' => $rowData['classItem'],
                'dtmdeliverydate' => "TO_TIMESTAMP('". $rowData['delivery']. "', 'YYYY/MM/DD')",
                'lngconversionclasscode' => 'NULL',
                'curproductprice' => $rowData['price'],
                'lngproductquantity' => $rowData['quantity'],
                'lngproductunitcode' => 'NULL',
                'cursubtotalprice' => $rowData['subtotal'],
                'strnote' => "'". $rowData['note']. "'",
                'lngsortkey' => $defaultSortKey,
                'lngestimateno' => $this->estimateNo,
                'lngestimatedetailno' => $estimateDetailNo,
                'lngestimaterevisionno' => $this->revisionNo
            );        
    
            // クエリの生成
            $strQuery = $this->makeInsertSelectQuery($table, $data);
    
            // クエリの実行
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

            // 行情報に受注番号をセットし返却
            $rowData['receiveNo'] = $receiveNo;
    
            $this->objDB->freeResult($resultID);

            // インクリメント
            ++$defaultSortKey;

            return;
        }
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
    protected function updateMasterReceive(&$rowData) {
        // テーブルの設定 
        $table = 'm_receive';

        $receiveNo = $rowData['receiveNo'];             // 受注番号
        $revisionNo = $this->preReceiveRevisionNo + 1;  // 登録に使用する受注のリビジョン番号

        $previousDetailNo = $rowData['previousDetailNo']; // 以前の見積原価明細番号

        // 登録データの作成
        if ($previousDetailNo) { // 以前のリビジョンで見積原価明細行番号が存在する場合

            $data = array(
                'lngreceiveno' => 'lngreceiveno',
                'lngrevisionno' => $revisionNo,
                'strreceivecode' => "strreceivecode",
                'strrevisecode' => "strrevisecode",
                'dtmappropriationdate' => 'dtmappropriationdate',
                'lngcustomercompanycode' => $this->companyCodeList[$rowData['customerCompany']],
                'lnggroupcode' => $this->groupCode,
                'lngusercode' => $this->inchargeUserCode,
                'lngreceivestatuscode' => 'lngreceivestatuscode',
                'lngmonetaryunitcode' => $rowData['monetary'],
                'lngmonetaryratecode' => DEF_MONETARY_RATE_CODE_COMPANY_LOCAL,
                'curconversionrate' => $rowData['conversionRate'],
                'lnginputusercode' => $this->inputUserCode,
                'bytinvalidflag' => 'bytinvalidflag',
                'dtminsertdate' => 'NOW()',
                'strcustomerreceivecode' => 'strcustomerreceivecode'
            );

            $condition = "WHERE lngreceiveno =". $receiveNo;
            $condition .= " AND lngrevisionno = ". $this->preReceiveRevisionNo;

            $returning = "lngreceivestatuscode";

            // クエリの生成
            $strQuery = $this->makeInsertSelectQuery($table, $data, $condition, $returning);
            // クエリの実行
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

            // 受注状態コードを取得
            $result = pg_fetch_object($resultID, 0);

            // 行情報に取得した値をセットし返却
            $rowData['receiveStatusCode'] = $result->lngreceivestatuscode;

            $this->objDB->freeResult($resultID);

            return true;
        } else {
            // 登録データの作成
            $data = array(
                'lngreceiveno' => $receiveNo,
                'lngrevisionno' => $revisionNo,
                'strreceivecode' => "'". $this->receiveCode. "'",
                'strrevisecode' => "'". $this->reviseCode. "'",
                'dtmappropriationdate' => 'NOW()',
                'lngcustomercompanycode' => $this->companyCodeList[$rowData['customerCompany']],
                'lnggroupcode' => $this->groupCode,
                'lngusercode' => $this->inchargeUserCode,
                'lngreceivestatuscode' => DEF_RECEIVE_APPLICATE,
                'lngmonetaryunitcode' => $rowData['monetary'],
                'lngmonetaryratecode' => DEF_MONETARY_RATE_CODE_COMPANY_LOCAL,
                'curconversionrate' => $rowData['conversionRate'],
                'lnginputusercode' => $this->inputUserCode,
                'bytinvalidflag' => 'false',
                'dtminsertdate' => 'NOW()',
                'strcustomerreceivecode' => 'NULL'
            );

            // クエリの生成
            $strQuery = $this->makeInsertSelectQuery($table, $data);
            // クエリの実行
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

            $this->objDB->freeResult($resultID);

            return true;
        }
    }

    /**
    * DB登録用関数
    *
    *	発注明細テーブルへの登録を行う
    *
    *   @param array $rowData 行のデータ
    *   @param array $receiveDetailNoList 受注明細番号リスト
    *   @param integer $defaultSortKey 新しく追加する明細用のソートキー  
    *   
    *	@return true
    */
    protected function updateTableOrderDetail(&$rowData, $orderSortKeyList, &$defaultSortKey) {
        // テーブルの設定
        $table = 't_orderdetail';

        $previousRevisionNo = $this->revisionNo - 1; // 1つ前の見積原価のリビジョン番号

        $revisionNo = $this->preOrderRevisionNo + 1;     // 登録に使用する発注のリビジョン番号

        $previousDetailNo = $rowData['previousDetailNo']; // 以前の見積原価明細番号（検索用）
        $estimateDetailNo = $rowData['currentDetailNo'];  // 今回の見積原価明細番号（登録用)

        $sortKey = $orderSortKeyList[$previousDetailNo];

        if ($previousDetailNo) {

            $data = array(
                'lngorderno' => 'lngorderno',
                'lngorderdetailno' => 'lngorderdetailno',
                'lngrevisionno' => $revisionNo,
                'strproductcode' => "'". $this->productCode. "'",
                'strrevisecode' => "'". $this->reviseCode. "'",
                'lngstocksubjectcode' => $rowData['divisionSubject'],
                'lngstockitemcode' => $rowData['classItem'],
                'dtmdeliverydate' => "TO_TIMESTAMP('". $rowData['delivery']. "', 'YYYY/MM/DD')",
                'lngdeliverymethodcode' => 'lngdeliverymethodcode',
                'lngconversionclasscode' => 'lngconversionclasscode',
                'curproductprice' => $rowData['price'],
                'lngproductquantity' => $rowData['quantity'],
                'lngproductunitcode' => 'lngproductunitcode',
                'lngtaxclasscode' => 'lngtaxclasscode',
                'lngtaxcode' => 'lngtaxcode',
                'curtaxprice' => 'curtaxprice',
                'cursubtotalprice' => $rowData['subtotal'],
                'strnote' => "'". $rowData['note']. "'",
                'strmoldno' => 'strmoldno',
                'lngsortkey' => $orderDetailNo,
                'lngestimateno' => $this->estimateNo,
                'lngestimatedetailno' => $estimateDetailNo,
                'lngestimaterevisionno' => $this->revisionNo
            );

            $condition = "WHERE lngestimateno =". $this->estimateNo;
            $condition .= " AND lngestimatedetailno = ". $previousDetailNo;
            $condition .= " AND lngestimaterevisionno = ". $previousRevisionNo;

            $returning = "lngorderno";

            // クエリの生成
            $strQuery = $this->makeInsertSelectQuery($table, $data, $condition, $returning);
    
            // クエリの実行
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

            // 受注番号、リビジョン番号を取得
            $result = pg_fetch_object($resultID, 0);

            // 行情報に取得した値をセットし返却
            $rowData['orderNo'] = $result->lngorderno;
    
            $this->objDB->freeResult($resultID);

            return;

        } else {
            // 発注番号を取得
            $orderNo = fncGetSequence("m_Order.lngOrderNo", $this->objDB);

            // 発注明細番号の最大値のインクリメント
            $this->maxOrderDetailNo += 1;

            $data = array(
                'lngorderno' => $orderNo,
                'lngorderdetailno' => $this->maxOrderDetailNo,
                'lngrevisionno' => $this->revisionNo,
                'strproductcode' => "'". $this->productCode. "'",
                'strrevisecode' => "'". $this->reviseCode. "'",
                'lngstocksubjectcode' => $rowData['divisionSubject'],
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
                'cursubtotalprice' => $rowData['subtotal'],
                'strnote' => "'". $rowData['note']. "'",
                'strmoldno' => 'NULL',
                'lngsortkey' => $orderDetailNo,
                'lngestimateno' => $this->estimateNo,
                'lngestimatedetailno' => $estimateDetailNo,
                'lngestimaterevisionno' => $this->revisionNo
            );
            // クエリの生成
            $strQuery = $this->makeInsertSelectQuery($table, $data);
    
            // クエリの実行
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

            // 行情報の配列に発注番号をセットして返却
            $rowData['orderNo'] = $orderNo;
    
            $this->objDB->freeResult($resultID);
        }
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
    protected function updateMasterOrder(&$rowData) {
        // テーブルの設定
        $table = 'm_order';

        $orderNo = $rowData['orderNo'];               // 発注番号
        $revisionNo = $this->preOrderRevisionNo + 1;  // 登録に使用する発注のリビジョン番号

        $previousDetailNo = $rowData['previousDetailNo']; // 以前の見積原価明細番号

        if ($previousDetailNo) {
            
            $data = array(
                'lngorderno' => $orderNo,
                'lngrevisionno' => $revisionNo,
                'strordercode' => 'strordercode',
                'dtmappropriationdate' => 'dtmappropriationdate',
                'lngcustomercompanycode' => $this->companyCodeList[$rowData['customerCompany']],
                'lnggroupcode' => $this->groupCode,
                'lngusercode' => $this->inchargeUserCode,
                'lngorderstatuscode' => 'lngorderstatuscode',
                'lngmonetaryunitcode' => $rowData['monetary'],
                'lngmonetaryratecode' => 'lngmonetaryratecode',
                'curconversionrate' => $rowData['conversionRate'],
                'lngpayconditioncode' => 'lngpayconditioncode',
                'lngdeliveryplacecode' => 'lngdeliveryplacecode',
                'lnginputusercode' => $this->inputUserCode,
                'bytinvalidflag' => 'bytinvalidflag',
                'dtminsertdate' => 'NOW()'
            );

            $condition = "WHERE lngorderno =". $orderNo;
            $condition .= " AND lngrevisionno = ". $this->preOrderRevisionNo;

            $returning = "lngorderstatuscode";
    
            // クエリの生成
            $strQuery = $this->makeInsertSelectQuery($table, $data, $condition, $returning);

            // クエリの実行
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

            // 受注状態コードを取得
            $result = pg_fetch_object($resultID, 0);

            // 行情報に取得した値をセットし返却
            $rowData['orderStatusCode'] = $result->lngorderstatuscode;
    
            $this->objDB->freeResult($resultID);
    
            return true;
        } else {
            $data = array(
                'lngorderno' => $orderNo,
                'lngrevisionno' => $revisionNo,
                'strordercode' => "'". $this->orderCode. "'",
                'dtmappropriationdate' => 'NOW()',
                'lngcustomercompanycode' => $this->companyCodeList[$rowData['customerCompany']],
                'lnggroupcode' => $this->groupCode,
                'lngusercode' => $this->inchargeUserCode,
                'lngorderstatuscode' => DEF_ORDER_APPLICATE,
                'lngmonetaryunitcode' => $rowData['monetary'],
                'lngmonetaryratecode' => DEF_MONETARY_RATE_CODE_COMPANY_LOCAL,
                'curconversionrate' => $rowData['conversionRate'],
                'lngpayconditioncode' => 'NULL',
                'lngdeliveryplacecode' => 'NULL',
                'lnginputusercode' => $this->inputUserCode,
                'bytinvalidflag' => 'false',
                'dtminsertdate' => 'NOW()'
            );
    
            // クエリの生成
            $strQuery = $this->makeInsertSelectQuery($table, $data);
            // クエリの実行
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);
    
            $this->objDB->freeResult($resultID);

            // 行情報に発注状態コードをセットし返却
            $rowData['orderStatusCode'] = DEF_ORDER_APPLICATE;
    
            return true;
        }


    }



    /**
    * DB登録用関数
    *
    *	商品化企画テーブルへの登録を行う 
    *   
    *	@return true
    */
    protected function updateTableGoodsPlan() {

        $table = 't_goodsplan';

        $previousRevisionNo = $this->productRevisionNo - 1;

        $data = array(
            'lnggoodsplancode' => 'lnggoodsplancode',
            'lngrevisionno' => $this->productRevisionNo,
            'lngproductno' => 'lngproductno',
            'strrevisecode' => 'strrevisecode',
            'dtmcreationdate' => 'NOW()',
            'dtmrevisiondate' => 'dtmrevisiondate',
            'lnggoodsplanprogresscode' => 'lnggoodsplanprogresscode',
            'lnginputusercode' => $this->inputUserCode
        );

        $condition = "WHERE lngproductno =". $this->productNo;
        $condition .= " AND lngrevisionno = ". $previousRevisionNo;
        $condition .= " AND strrevisecode = '". $this->reviseCode. "'";

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
    *	売上明細テーブルの修正を行う 
    *   
    *	@return true
    */
    protected function updateTableSalesDetail($rowData) {

        $receiveNo = $rowData['receiveNo'];
        $receiveDetailNo = $rowData['receiveDetailNo'];
        $preReceiveRevisionNo = $this->preReceiveRevisionNo;
        
        $strQuery = "UPDATE";
        $strQuery .= " t_salesdetail";
        $strQuery .= " SET";
        $strQuery .= " lngreceiverevisionno = lngreceiverevisionno + 1";
        $strQuery .= " WHERE lngreceiveno = ". $receiveNo;
        $strQuery .= " AND lngreceivedetailno = ". $receiveDetailNo;
        $strQuery .= " AND lngreceiverevisionno = ". $preReceiveRevisionNo;

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
    * DB登録用関数
    *
    *	納品伝票明細テーブルの修正を行う 
    *   
    *	@return true
    */
    protected function updateTableSlipDetail($rowData) {

        $receiveNo = $rowData['receiveNo'];
        $receiveDetailNo = $rowData['receiveDetailNo'];
        $preReceiveRevisionNo = $this->preReceiveRevisionNo;
        
        $strQuery = "UPDATE";
        $strQuery .= " t_slipdetail";
        $strQuery .= " SET";
        $strQuery .= " lngreceiverevisionno = lngreceiverevisionno + 1";
        $strQuery .= " WHERE lngreceiveno = ". $receiveNo;
        $strQuery .= " AND lngreceivedetailno = ". $receiveDetailNo;
        $strQuery .= " AND lngreceiverevisionno = ". $preReceiveRevisionNo;

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
    * DB登録用関数
    *
    *	発注書詳細テーブルの修正を行う 
    *   
    *	@return true
    */
    protected function updateTablePurchaseOrderDetail($rowData) {

        $orderNo = $rowData['orderNo'];
        $orderDetailNo = $rowData['orderDetailNo'];
        $preOrderRevisionNo = $this->preOrderRevisionNo;
        
        $strQuery = "UPDATE";
        $strQuery .= " t_purchaseorderdetail";
        $strQuery .= " SET";
        $strQuery .= " lngorderrevisionno = lngorderrevisionno + 1";
        $strQuery .= " WHERE lngorderno = ". $orderNo;
        $strQuery .= " AND lngorderdetailno = ". $orderDetailNo;
        $strQuery .= " AND lngorderrevisionno = ". $preOrderRevisionNo;

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
    * DB登録用関数
    *
    *	仕入明細テーブルの修正を行う 
    *   
    *	@return true
    */
    protected function updateTableStockDetail($rowData) {
 
        $orderNo = $rowData['orderNo'];
        $orderDetailNo = $rowData['orderDetailNo'];
        $preOrderRevisionNo = $this->preOrderRevisionNo;
        
        $strQuery = "UPDATE";
        $strQuery .= " t_stockdetail";
        $strQuery .= " SET";
        $strQuery .= " lngorderrevisionno = lngorderrevisionno + 1";
        $strQuery .= " WHERE lngorderno = ". $orderNo;
        $strQuery .= " AND lngorderdetailno = ". $orderDetailNo;
        $strQuery .= " AND lngorderrevisionno = ". $preOrderRevisionNo;

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }


    /**
    * DB検索用関数
    *
    *	条件を満たすレコードが存在するか確認する
    *   
    *   @param string $table 
    *   @param array $data   カラム名をキーに持つ、検索条件の配列 ($data['カラム名']] = 検索条件);
    *
    *	@return boolean
    */
    protected function confirmExistenceRecord($table, $data) {
        $sqlQuery = "SELECT * FROM ".$table;
        $sqlQuery .= " WHERE";

        foreach ($data as $col => $condition) {

            if ($search) {
                $search = ", AND ". $col. " = ". $condition;
            } else {
                $search = " ". $col. " = ". $condition;
            }
        }

        $slqQuery .= $search;
        
        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);
        
        if ($resultNumber < 0) {
            return false;
        } else {
            return true;
        }
    }


    // 見積原価番号に対応する、指定のテーブルのソートキーのリストを取得する
    protected function getRenumberSortKeyList($table, $detailNoList, &$newSortKey) {
        $detail = implode(', ', $detailNoList);
        $previousRevisionNo = $this->revisionNo - 1;
        $strQuery = "SELECT";
        $strQuery .= " lngsortkey,";
        $strQuery .= " lngestimatedetailno";
        $strQuery .= " FROM ". $table;
        $strQuery .= " WHERE lngestimatedetailno IN (". $detail. ")";
        $strQuery .= " AND lngestimateno = ". $this->estimateNo;
        $strQuery .= " AND lngrevisionno = ". $previousRevisionNo;
        $strQuery .= " ORDER BY lngsortkey";

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $sortKey = 1; // ソートキーは1からの連番

        for ($i = 0; $i < $resultNumber; ++$i) {
            $result = pg_fetch_object($resultID, $i);
            $previousDetailNo = $result->lngestimatedetailno;
            $sortKeyList[$previousDetailNo] = $sortKey;
            ++$sortKey;
        }

        $newSortKey = $sortKey; // 新しいソートキーが必要な時に使用する

        $this->objDB->freeResult($resultID);

        return $sortKeyList;
    }



    /**
    *	検索結果の最初のレコードから指定したカラムの値を取得する
    *	
    *   @param string $table            検索するテーブル名
    *   @param string $needle           値を取得するカラム
    *   @param string $search           検索条件
    *   @param string $sort             'max' or 'min'
    *   
    */
    protected function getFirstRecordValue($table, $needle, $search, $sort = null) {
        $strQuery = "SELECT ". $needle. " FROM ". $table. " " .$search;

        // $sort指定がある場合は最大値又は最小値を返す
        if (isset($sort)) {
            $sortString = mb_strtolower($sort);
            if ($sortString === 'min') {
                $orderQuery = " ORDER BY ". $needle. " ASC";
            } else {
                $orderQuery = " ORDER BY ". $needle. " DESC";
            }
        }

        if (isset($orderQuery)) {
            $strQuery .= $orderQuery;
        }        

        list($resultID, $resuluNumber) = fncQuery($strQuery, $this->objDB);

        // 最初のレコードの値を返却する
        if ($resuluNumber > 0) {
            $result = pg_fetch_object($resultID, 0);
            $ret = $result->$needle;
        } else {
            $ret = false;
        }

        $this->objDB->freeResult($resultID);
        return $ret;
    }

    // 見積原価番号から受注コードを取得する
    protected function getReceiveCode() {
        $strQuery = "SELECT";
        $strQuery .= " strreceivecode";
        $strQuery .= " FROM";
        $strQuery .= " m_receive a";
        $strQuery .= " INNER JOIN";
        $strQuery .= " t_receivedetail b";
        $strQuery .= " ON a.lngreceiveno = b.lngreceiveno";
        $strQuery .= " AND a.lngrevisionno = b.lngrevisionno";
        $strQuery .= " WHERE b.lngestimateno = ". $this->estimateNo;

        list($resultID, $resuluNumber) = fncQuery($strQuery, $this->objDB);

        // 最初のレコードの値を返却する
        if ($resuluNumber > 0) {
            $result = pg_fetch_object($resultID, 0);
            $ret = $result->strreceivecode;
        } else {
            $ret = false;
        }

        return $ret;
    }

    // 見積原価番号から発注コードを取得する
    protected function getOrderCode() {
        $strQuery = "SELECT";
        $strQuery .= " strordercode";
        $strQuery .= " FROM";
        $strQuery .= " m_order a";
        $strQuery .= " INNER JOIN";
        $strQuery .= " t_orderdetail b";
        $strQuery .= " ON a.lngorderno = b.lngorderno";
        $strQuery .= " AND a.lngrevisionno = b.lngrevisionno";
        $strQuery .= " WHERE b.lngestimateno = ". $this->estimateNo;

        list($resultID, $resuluNumber) = fncQuery($strQuery, $this->objDB);

        // 最初のレコードの値を返却する
        if ($resuluNumber > 0) {
            $result = pg_fetch_object($resultID, 0);
            $ret = $result->strordercode;
        } else {
            $ret = false;
        }

        return $ret;
    }

    // 削除対象明細の修正および論理削除用のレコードの追加を行う
    protected function insertDeleteRecord() {
        $this->updateDeleteReceive();
        $this->insertDeleteReceive();
        $this->insertDeleteOrder();
        return;
    }

    // 受注マスタの削除対象明細の受注コードを修正する
    protected function updateDeleteReceive() {

        $previousRevisionNo = $this->revisionNo - 1;

        $strQuery = "UPDATE m_receive mr";
        $strQuery .= " SET strreceivecode = CASE WHEN strreceivecode ~ '^\*.+\*$' THEN strreceivecode ELSE '*' || strreceivecode || '*' END";
        // 以下、登録前のリビジョン番号が存在し、登録後のリビジョン番号が存在しない受注番号を取得する
        $strQuery .= " FROM";
        $strQuery .= " (";
        $strQuery .= "SELECT mr2.lngreceiveno";
        $strQuery .= " FROM t_receivedetail trd";
        $strQuery .= " INNER JOIN m_receive mr2";
        $strQuery .= " ON mr2.lngreceiveno = trd.lngreceiveno";
        $strQuery .= " AND mr2.lngrevisionno = trd.lngrevisionno";
        $strQuery .= " LEFT OUTER JOIN t_receivedetail trd2";
        $strQuery .= " ON mr2.lngreceiveno = trd2.lngreceiveno";
        $strQuery .= " AND trd.lngestimateno = trd2.lngestimateno";
        $strQuery .= " AND trd2.lngestimaterevisionno = ". $this->revisionNo;
        $strQuery .= " WHERE";
        $strQuery .= " trd.lngestimateno = ". $this->estimateNo;
        $strQuery .= " AND trd.lngestimaterevisionno = ". $previousRevisionNo;
        $strQuery .= " AND trd2.lngreceiveno is NULL";
        $strQuery .= ") sub";
        $strQuery .= " WHERE sub.lngreceiveno = mr.lngreceiveno"; // 受注番号を結合

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    // 受注マスタに論理削除用レコードを追加する
    protected function insertDeleteReceive() {

        $previousRevisionNo = $this->revisionNo - 1;

        $strQuery = "INSERT INTO m_receive";
        $strQuery .= " (";

        $strQuery .= "lngreceiveno,";
        $strQuery .= " lngrevisionno,";
        $strQuery .= " strreceivecode,";
        $strQuery .= " lnginputusercode,";
        $strQuery .= " bytinvalidflag,";
        $strQuery .= " dtminsertdate,";
        $strQuery .= " strrevisecode";

        $strQuery .= ")";

        $strQuery .= " SELECT";

        $strQuery .= " mr.lngreceiveno,";
        $strQuery .= " -1,";
        $strQuery .= " CASE WHEN mr.strreceivecode ~ '^\*.+\*$' THEN mr.strreceivecode ELSE '*' || mr.strreceivecode || '*' END,";
        $strQuery .= " ". $this->inputUserCode. ",";
        $strQuery .= " false,";
        $strQuery .= " now(),";
        $strQuery .= " mr.strrevisecode";

        $strQuery .= " FROM t_receivedetail trd";

        // 受注明細テーブルに受注マスタを結合
        $strQuery .= " INNER JOIN m_receive mr";
        $strQuery .= " ON mr.lngreceiveno = trd.lngreceiveno";
        $strQuery .= " AND mr.lngrevisionno = trd.lngrevisionno";

        // 修正後のリビジョンの検索結果を結合
        $strQuery .= " LEFT OUTER JOIN t_receivedetail trd2";
        $strQuery .= " ON mr.lngreceiveno = trd2.lngreceiveno";
        $strQuery .= " AND trd2.lngestimaterevisionno = ". $this->revisionNo;

        // 修正後のリビジョンが存在しないものだけを対象とするWHERE句
        $strQuery .= " WHERE";
        $strQuery .= " trd.lngestimateno = ". $this->estimateNo;
        $strQuery .= " AND trd.lngestimaterevisionno = ". $previousRevisionNo;
        $strQuery .= " AND trd2.lngreceiveno is NULL";

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    // 発注マスタに論理削除用レコードを追加する
    protected function insertDeleteOrder() {

        $previousRevisionNo = $this->revisionNo - 1;

        $strQuery = "INSERT INTO m_order";
        $strQuery .= " (";
        
        $strQuery .= " lngorderno,";
        $strQuery .= " lngrevisionno,";
        $strQuery .= " strordercode,";
        $strQuery .= " lnginputusercode,";
        $strQuery .= " bytinvalidflag,";
        $strQuery .= " dtminsertdate";

        $strQuery .= ")";

        $strQuery .= " SELECT";
        $strQuery .= " mo.lngorderno,";
        $strQuery .= " -1,";
        $strQuery .= " mo.strordercode,";
        $strQuery .= " ". $this->inputUserCode. ",";
        $strQuery .= " false,";
        $strQuery .= " now()";

        $strQuery .= " FROM t_orderdetail tod";
        $strQuery .= " INNER JOIN m_order mo";
        $strQuery .= " ON mo.lngorderno = tod.lngorderno";
        $strQuery .= " AND mo.lngrevisionno = tod.lngrevisionno";
        $strQuery .= " LEFT OUTER JOIN t_orderdetail tod2";
        $strQuery .= " ON mo.lngorderno = tod2.lngorderno";
        $strQuery .= " AND tod.lngestimateno = tod2.lngestimateno";
        $strQuery .= " AND tod2.lngestimaterevisionno = ". $this->revisionNo;
        $strQuery .= " WHERE";
        $strQuery .= " tod.lngestimateno = ". $this->estimateNo;
        $strQuery .= " AND tod.lngestimaterevisionno = ". $previousRevisionNo;
        $strQuery .= " AND tod2.lngorderno is NULL";

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }
}
