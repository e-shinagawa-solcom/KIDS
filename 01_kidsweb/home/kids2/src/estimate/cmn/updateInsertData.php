<?php

require_once 'conf.inc';
require_once LIB_DEBUGFILE;

require_once LIB_ROOT . "lib.php";

require_once SRC_ROOT . "estimate/cmn/const/workSheetConst.php";
require_once SRC_ROOT . "estimate/cmn/estimateInsertData.php";

// 登録用データ作成クラス
class updateInsertData extends estimateInsertData
{

    public function __construct()
    {
        parent::__construct();
    }

    // 必要なパラメータをクラスにセットする
    public function setUpdateParam($update, $inputUserCode, $productCode, $reviseCode, $revisionNo, $objDB)
    {
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
            $this->companyCodeList = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", "Array", "", $this->objDB);

        } else {
            return false;
        }

        return true;
    }

    /**
     * DB登録用関数
     *
     *    見積原価登録を行う
     *
     *    @return true
     */
    public function update()
    {

        // 製品マスタの登録処理
        $this->updateMasterProduct();

        // 見積原価マスタの登録処理
        $this->updateMasterEstimate();

        // 商品化企画テーブルの登録処理
        $this->updateTableGoodsPlan();

        $rowDataList = $this->rowDataList;

        // 行の情報から以前の見積原価明細番号を取得する
        $detailNoList = [];
        $newDetailNo = $this->getNewDetailNo();
        for ($i = 0; $i <= count($rowDataList); $i++) {
            if (is_null($rowDataList[$i])) {
                continue;
            }
            $previousDetailNo = $rowDataList[$i]['previousDetailNo'];

            if ($rowDataList[$i]['previousDetailNo'] != 0) {
                $detailNoList[] = $rowDataList[$i]['previousDetailNo'];
                $rowDataList[$i]['detailRevisionNo'] = (int) $this->getCurrentDetailRevision($rowDataList[$i]['previousDetailNo']);
                // 受注マスタ、発注マスタいずれかに登録される明細行について、以前の見積原価明細行番号を取得する
                if (
                    ($rowDataList[$i]['areaCode'] !== DEF_AREA_OTHER_COST_ORDER) ||
                    (
                        ($rowDataList[$i]['areaCode'] == DEF_AREA_OTHER_COST_ORDER) &&
                        (
                            ($rowDataList[$i]['divisionSubject'] == DEF_STOCK_SUBJECT_CODE_MATERIAL_PARTS_COST && $rowDataList[$i]['classItem'] == DEF_STOCK_ITEM_CODE_CERTIFICATE) ||
                            ($rowDataList[$i]['divisionSubject'] == DEF_STOCK_SUBJECT_CODE_EXPENSE && $rowDataList[$i]['classItem'] == DEF_STOCK_ITEM_CODE_TARIFF)
                        )
                    )
                ) {

                    if ($rowDataList[$i]['salesOrder'] === DEF_ATTRIBUTE_CLIENT) {
                        $rowDataList[$i]['receiveStatusCode'] = (int) $this->getReceiveStatus($rowDataList[$i]);
                        if ($rowDataList[$i]['receiveStatusCode'] != DEF_RECEIVE_APPLICATE) {
                            // 仮受注ではないデータはリビジョンアップしないため、現リビジョンで履歴に登録
                            $rowDataList[$i]['detailRevisionNo'] -= 1;
                        }
                    } else {
                        $rowDataList[$i]['orderStatusCode'] = (int) $this->getOrderStatus($rowDataList[$i]);
                        if ($rowDataList[$i]['orderStatusCode'] != DEF_ORDER_APPLICATE) {
                            // 仮発注ではないデータはリビジョンアップしないため、現リビジョンで履歴に登録
                            $rowDataList[$i]['detailRevisionNo'] -= 1;
                        }
                    }
                }
            } else {
                // 見積原価明細番号を新規採番する。
                $rowDataList[$i]['previousDetailNo'] = $newDetailNo;
                $newDetailNo++;
                $rowDataList[$i]['detailRevisionNo'] = -1;
                if ($rowDataList[$i]['salesOrder'] === DEF_ATTRIBUTE_CLIENT) {
                    $rowDataList[$i]['receiveStatusCode'] = DEF_RECEIVE_APPLICATE;
                } else {
                    $rowDataList[$i]['orderStatusCode'] = DEF_ORDER_APPLICATE;
                }
            }
        }

        $receiveTable = 't_receivedetail';
        $orderTable = 't_orderdetail';

        // 受注明細テーブル、発注明細テーブルの明細番号の最大値を取得し、セットする
        $search = "WHERE lngestimateno = " . $this->estimateNo; // 検索条件クエリ

        $this->maxReceiveDetailNo = $this->getFirstRecordValue($receiveTable, 'lngreceivedetailno', $search, 'max'); // 受注番号の最大値
        $this->maxOrderDetailNo = $this->getFirstRecordValue($orderTable, 'lngorderdetailno', $search, 'max'); // 発注番号の最大値

        // 見積原価番号及び見積原価リビジョン番号に紐付く受注明細テーブル、発注明細テーブルの最大のリビジョン番号を取得する
        //        $searchRevision = "WHERE lngestimateno = ". $this->estimateNo; // 検索条件クエリ
        //        $searchRevision .= " AND lngestimaterevisionno = ". $rowData['detailRevisionNo'];

//        $this->preReceiveRevisionNo = $this->getFirstRecordValue($receiveTable, 'lngrevisionno', $searchRevision, 'max');
        //        $this->preOrderRevisionNo = $this->getFirstRecordValue($orderTable, 'lngrevisionno', $searchRevision, 'max');

        // 見積原価番号に紐付く受注コード、発注コードを取得する。取得できなかった場合はシーケンス処理により採番する
        // 受注
        if ($getReviseCode = $this->getReceiveCode()) {
            $this->receiveCode = $getReviseCode;
        } else {
            $this->receiveCode = 'd' . fncGetDateSequence(date('Y'), date('m'), 'm_receive.strreceivecode', $this->objDB);
        }
        // 発注
        if ($getOrderCode = $this->getOrderCode()) {
            $this->orderCode = $getOrderCode;
        } else {
            $this->orderCode = fncGetDateSequence(date('Y'), date('m'), 'm_order.strordercode', $this->objDB);
        }

        // 明細行の登録
        $recvSortKey = 1;
        $orderSortKey = 1;
        foreach ($rowDataList as $rowData) {
            if (is_null($rowData)) {
                continue;
            }
            // 見積原価明細番号のインクリメントs
            ++$estimateDetailNo;

            $detailRevisionNo = $rowData['detailRevisionNo'];
            if ($rowData['areaCode'] == DEF_AREA_OTHER_COST_ORDER &&
                (($rowData['divisionSubject'] == DEF_STOCK_SUBJECT_CODE_MATERIAL_PARTS_COST && $rowData['classItem'] == DEF_STOCK_ITEM_CODE_CERTIFICATE) ||
                ($rowData['divisionSubject'] == DEF_STOCK_SUBJECT_CODE_EXPENSE && $rowData['classItem'] == DEF_STOCK_ITEM_CODE_TARIFF))
                && $rowData['orderStatusCode'] == 0) {
                $detailRevisionNo += 1;
            }
            // 見積原価履歴テーブルの登録処理
            $this->registTableEstimateHistory($estimateDetailNo, $rowData['previousDetailNo'], $detailRevisionNo);

            $salesOrder = $rowData['salesOrder'];
            // 受注の場合
            if ($salesOrder === DEF_ATTRIBUTE_CLIENT) {
                // 仮受注の明細のみ、見積原価明細と受注明細、受注マスタを更新
                if ($rowData['receiveStatusCode'] == DEF_RECEIVE_APPLICATE) {
                    // 見積原価明細テーブルの登録処理
                    $this->updateTableEstimateDetail($rowData, $estimateDetailNo);

                    // 受注明細テーブル登録処理
                    $this->updateTableReceiveDetail($rowData, $recvSortKey);
                    $recvSortKey++;

                    // 受注マスタ登録処理
                    $this->updateMasterReceive($rowData);

                    /*
                if ($rowData['receiveStatusCode'] == DEF_RECEIVE_END
                || $rowData['receiveStatusCode'] == DEF_RECEIVE_CLOSED) {
                // 納品済、または締め済の場合
                $this->updateTableSalesDetail($rowData);
                $this->updateTableSlipDetail($rowData);

                }
                 */
                }
                // 発注の場合
            } else if ($salesOrder === DEF_ATTRIBUTE_SUPPLIER) {
                // 発注で経費以外の場合
                if (
                    ($rowData['areaCode'] !== DEF_AREA_OTHER_COST_ORDER) ||
                    (
                        ($rowData['areaCode'] == DEF_AREA_OTHER_COST_ORDER) &&
                        (
                            ($rowData['divisionSubject'] == DEF_STOCK_SUBJECT_CODE_MATERIAL_PARTS_COST && $rowData['classItem'] == DEF_STOCK_ITEM_CODE_CERTIFICATE) ||
                            ($rowData['divisionSubject'] == DEF_STOCK_SUBJECT_CODE_EXPENSE && $rowData['classItem'] == DEF_STOCK_ITEM_CODE_TARIFF)
                        )
                    )
                ) {

//fncDebug("view.log", "divisionSubject:" . $rowData['divisionSubject'], __FILE__, __LINE__, "a");
                    //fncDebug("view.log", "classItem:" . $rowData['classItem'], __FILE__, __LINE__, "a");
                    // 仮発注の明細のみ、見積原価明細と発注明細、発注マスタを更新
                    if ($rowData['orderStatusCode'] == DEF_ORDER_APPLICATE) {
                        // 見積原価明細テーブルの登録処理
                        $this->updateTableEstimateDetail($rowData, $estimateDetailNo);

                        // 発注明細テーブル登録処理
                        $this->updateTableOrderDetail($rowData, $orderSortKey);
                        $orderSortKey++;

                        // 発注マスタ登録処理
                        $this->updateMasterOrder($rowData);
                    }

                    if ($rowData['areaCode'] == DEF_AREA_OTHER_COST_ORDER && (
                        ($rowData['divisionSubject'] == DEF_STOCK_SUBJECT_CODE_MATERIAL_PARTS_COST && $rowData['classItem'] == DEF_STOCK_ITEM_CODE_CERTIFICATE) ||
                        ($rowData['divisionSubject'] == DEF_STOCK_SUBJECT_CODE_EXPENSE && $rowData['classItem'] == DEF_STOCK_ITEM_CODE_TARIFF)
                    ) && $rowData['orderStatusCode'] == 0
                    ) {

                        $rowData['detailRevisionNo'] = (int) $this->getCurrentDetailRevision($rowData['previousDetailNo']);
                        // 見積原価明細テーブルの登録処理
                        $this->updateTableEstimateDetail($rowData, $estimateDetailNo);

                        if ($rowData['divisionSubject'] == DEF_STOCK_SUBJECT_CODE_MATERIAL_PARTS_COST &&
                            $rowData['classItem'] == DEF_STOCK_ITEM_CODE_CERTIFICATE) {
                            // 発注明細テーブル登録処理
                            $this->updateTableOrderDetail($rowData, $orderSortKey);
                            $orderSortKey++;

                            // 発注マスタ登録処理
                            $this->updateMasterOrder($rowData);
                        }
                    }
                } else {
//fncDebug("view.log", "divisionSubject:" . $rowData['divisionSubject'], __FILE__, __LINE__, "a");
                    //fncDebug("view.log", "classItem:" . $rowData['classItem'], __FILE__, __LINE__, "a");
                    // 見積原価明細テーブルの登録処理
                    $this->updateTableEstimateDetail($rowData, $estimateDetailNo);
                }
                /*
            if ($rowData['orderStatusCode'] == DEF_ORDER_ORDER) {
            // 発注の場合
            $this->updateTablePurchaseOrderDetail($rowData);

            } else if ($rowData['orderStatusCode'] == DEF_ORDER_END
            || $rowData['orderStatusCode'] == DEF_ORDER_CLOSED) {

            // 納品済、または締め済の場合
            $this->updateTablePurchaseOrderDetail($rowData);
            $this->updateTableStockDetail($rowData);
            }
             */

            }
        }

        // 受注マスタ、発注マスタからデータの削除を行う（削除された行がある場合）
        $this->insertDeleteRecord();

        return true;
    }

    /**
     * DB登録用関数
     *
     *    製品マスタへ編集したデータの編集登録を行う
     *
     *    @return true
     */
    protected function updateMasterProduct()
    {

        $table = 'm_product';

        // 登録データ
        $data = array(
            'lngproductno' => $table . ".lngproductno",
            'strproductcode' => "'" . $this->productCode . "'",
            'strproductname' => "'" . $this->headerData[workSheetConst::PRODUCT_NAME] . "'",
            'strproductenglishname' => "'" . $this->headerData[workSheetConst::PRODUCT_ENGLISH_NAME] . "'",
            'strgoodscode' => $table . ".strgoodscode",
            'strgoodsname' => $table . ".strgoodsname",
            'lnginchargegroupcode' => $this->groupCode,
            'lnginchargeusercode' => $this->inchargeUserCode,
            'lngdevelopusercode' => $this->developUserCode,
            'lnginputusercode' => $this->inputUserCode,
            'lngcustomercompanycode' => $table . ".lngcustomercompanycode",
            'lngcustomergroupcode' => $table . ".lngcustomergroupcode",
            'lngcustomerusercode' => $table . ".lngcustomerusercode",
            'strcustomerusername' => $table . ".strcustomerusername",
            'lngpackingunitcode' => $table . ".lngpackingunitcode",
            'lngproductunitcode' => $table . ".lngproductunitcode",
            'lngboxquantity' => $table . ".lngboxquantity",
            'lngcartonquantity' => $this->headerData[workSheetConst::CARTON_QUANTITY],
            'lngproductionquantity' => $this->headerData[workSheetConst::PRODUCTION_QUANTITY],
            'lngproductionunitcode' => $table . ".lngproductionunitcode",
            'lngfirstdeliveryquantity' => $table . ".lngfirstdeliveryquantity",
            'lngfirstdeliveryunitcode' => $table . ".lngfirstdeliveryunitcode",
            'lngfactorycode' => $table . ".lngfactorycode",
            'lngassemblyfactorycode' => $table . ".lngassemblyfactorycode",
            'lngdeliveryplacecode' => $table . ".lngdeliveryplacecode",
            'dtmdeliverylimitdate' => $table . ".dtmdeliverylimitdate",
            'curproductprice' => $table . ".curproductprice",
            'curretailprice' => $this->headerData[workSheetConst::RETAIL_PRICE],
            'lngtargetagecode' => $table . ".lngtargetagecode",
            'lngroyalty' => $table . ".lngroyalty",
            'lngcertificateclasscode' => $table . ".lngcertificateclasscode",
            'lngcopyrightcode' => $table . ".lngcopyrightcode",
            'strcopyrightdisplaystamp' => $table . ".strcopyrightdisplaystamp",
            'strcopyrightdisplayprint' => $table . ".strcopyrightdisplayprint",
            'lngproductformcode' => $table . ".lngproductformcode",
            'strproductcomposition' => $table . ".strproductcomposition",
            'strassemblycontents' => $table . ".strassemblycontents",
            'strspecificationdetails' => $table . ".strspecificationdetails",
            'strnote' => $table . ".strnote",
            'bytinvalidflag' => $table . ".bytinvalidflag",
            'dtminsertdate' => $table . ".dtminsertdate",
            'dtmupdatedate' => "'" . fncGetDateTimeString() . "'",
            'strcopyrightnote' => $table . ".strcopyrightnote",
            'lngcategorycode' => $table . ".lngcategorycode",
            'lngrevisionno' => $table . ".lngrevisionno + 1",
            'strrevisecode' => "'" . $this->reviseCode . "'",
        );

        // 検索、結合条件
        $join = "INNER JOIN (";
        $join .= "SELECT";
        $join .= " " . $table . ".lngproductno,";
        $join .= " " . $table . ".strrevisecode,";
        $join .= " MAX(" . $table . ".lngrevisionno) AS lngrevisionno";
        $join .= " FROM " . $table;
        $join .= " WHERE";
        $join .= " " . $table . ".strproductcode = '" . $this->productCode . "'";
        $join .= " AND " . $table . ".strrevisecode = '" . $this->reviseCode . "'";
        $join .= " GROUP BY";
        $join .= " " . $table . ".lngproductno,";
        $join .= " " . $table . ".strrevisecode";
        $join .= ") A";
        $join .= " ON A.lngproductno =" . $table . ".lngproductno";
        $join .= " AND A.strrevisecode =" . $table . ".strrevisecode";
        $join .= " AND A.lngrevisionno =" . $table . ".lngrevisionno";

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
     *    見積原価マスタへの編集登録を行う
     *
     *    @return true
     */
    protected function updateMasterEstimate()
    {
        $table = 'm_estimate';

        // 上代
        $retailPrice = $this->headerData[workSheetConst::RETAIL_PRICE];
        // 償却数
        $productionQuantity = $this->headerData[workSheetConst::PRODUCTION_QUANTITY];

        // 合計金額の計算
        $totalPrice = $retailPrice * $productionQuantity;

        $data = array(
            'lngestimateno' => $table . '.lngestimateno',
            'lngrevisionno' => $table . '.lngrevisionno + 1',
            'strproductcode' => "'" . $this->productCode . "'",
            'strrevisecode' => "'" . $this->reviseCode . "'",
            'bytdecisionflag' => $table . '.bytdecisionflag',
            'lngestimatestatuscode' => $table . '.lngestimatestatuscode',
            'curfixedcost' => $this->calculatedData[workSheetConst::DEPRECIATION_COST],
            'curmembercost' => $this->calculatedData[workSheetConst::MEMBER_COST],
            'curtotalprice' => $this->calculatedData[workSheetConst::PROFIT],
            'curmanufacturingcost' => $this->calculatedData[workSheetConst::MANUFACTURING_COST],
            'cursalesamount' => $this->calculatedData[workSheetConst::PRODUCT_TOTAL_PRICE],
            'curprofit' => $this->calculatedData[workSheetConst::OPERATING_PROFIT],
            'lnginputusercode' => $this->inputUserCode,
            'bytinvalidflag' => $table . '.bytinvalidflag',
            'dtminsertdate' => "'" . fncGetDateTimeString() . "'",
            'lngproductionquantity' => $productionQuantity,
            'lngtempno' => $table . '.lngtempno',
            'strnote' => $table . '.strnote',
            'lngproductrevisionno' => $this->productRevisionNo,
        );

        // 検索、結合条件
        $join = "INNER JOIN";
        $join .= " (";
        $join .= " SELECT";
        $join .= " " . $table . ".lngestimateno,";
        $join .= " MAX(" . $table . ".lngrevisionno) AS lngrevisionno";
        $join .= " FROM " . $table . "";
        $join .= " WHERE " . $table . ".strproductcode = '" . $this->productCode . "'";
        $join .= " AND " . $table . ".strrevisecode = '" . $this->reviseCode . "'";
        $join .= " GROUP BY " . $table . ".lngestimateno";
        $join .= " ) A";
        $join .= " ON A.lngestimateno = " . $table . ".lngestimateno";
        $join .= " AND A.lngrevisionno = " . $table . ".lngrevisionno";

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
     *    見積原価履歴テーブルへの編集登録を行う
     *
     *   @param array $rowData 行のデータ
     *   @param integer $rowNo 通しの行番号
     *   @param integer $estimateDetailNo 見積原価明細番号
     *   @param integer $revisionno 見積原価明細のリビジョン番号（新規は-1を指定する）
     *
     *    @return true
     */
    protected function registTableEstimateHistory($rowNo, $estimateDetailNo, $revisionno)
    {
        // テーブル名の設定
        $table = 'm_estimatehistory';

        // 受注の場合

        // 登録データの作成
        $data = array(
            'lngestimateno' => $this->estimateNo,
            'lngrevisionno' => $this->revisionNo,
            'lngestimaterowno' => $rowNo,
            'lngestimatedetailno' => $estimateDetailNo,
            'lngestimatedetailrevisionno' => $revisionno + 1,
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
     *    見積原価明細テーブルへの編集登録を行う
     *
     *   @param array $rowData 行のデータ
     *   @param integer $estimateDetailNo 見積原価明細番号
     *
     *    @return true
     */
    protected function updateTableEstimateDetail(&$rowData, $estimateDetailNo)
    {
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
//            'lngestimatedetailno' => $estimateDetailNo,
            'lngestimatedetailno' => $rowData['previousDetailNo'],
//            'lngrevisionno' => $this->revisionNo,
            'lngrevisionno' => $rowData['detailRevisionNo'] + 1,
            'lngstocksubjectcode' => $stockSubjectCode,
            'lngstockitemcode' => $stockItemCode,
            'lngcustomercompanycode' => $this->companyCodeList[$rowData['customerCompany']],
            'dtmdelivery' => $rowData['delivery'] ? "TO_TIMESTAMP('" . $rowData['delivery'] . "', 'YYYY/MM/DD')" : 'null',
            'bytpayofftargetflag' => $rowData['payoff'] == '○' ? 'true' : 'false',
            'bytpercentinputflag' => $rowData['percentInputFlag'],
            'lngmonetaryunitcode' => $rowData['monetary'],
            'lngmonetaryratecode' => $rowData['monetary'] == 1 ? DEF_MONETARY_RATE_CODE_NONE : DEF_MONETARY_RATE_CODE_TTM,
            'curconversionrate' => $rowData['conversionRate'],
            'lngproductquantity' => $rowData['quantity'],
            'curproductprice' => $rowData['price'],
            'curproductrate' => $rowData['percentInputFlag'] === true ? $rowData['percent'] / 100 : 'null',
            'cursubtotalprice' => $rowData['subtotal'],
            'strnote' => "'" . $rowData['note'] . "'",
            'lngsortkey' => $estimateDetailNo,
            'lngsalesdivisioncode' => $salesDivisionCode,
            'lngsalesclasscode' => $salesClassCode,
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
     *    受注明細テーブルへの登録を行う
     *
     *   @param array $rowData 行のデータ
     *   @param array $receiveDetailNoList 受注明細番号リスト
     *   @param integer $defaultSortKey 新しく追加する明細用のソートキー
     *
     *    @return true
     */
    protected function updateTableReceiveDetail(&$rowData, $sortKey)
    {
        // テーブルの設定
        $table = 't_receivedetail';

//        $previousRevisionNo = $this->revisionNo - 1;      // 1つ前の見積原価のリビジョン番号

        $revisionNo = $this->preReceiveRevisionNo + 1; // 登録に使用する受注のリビジョン番号

        $previousDetailNo = $rowData['previousDetailNo']; // 以前の見積原価明細番号（検索用）
        //        $estimateDetailNo = $rowData['currentDetailNo'];  // 今回の見積原価明細番号（登録用)
        $estimateDetailNo = $rowData['previousDetailNo']; // 今回の見積原価明細番号（登録用)←不変。見積原価明細番号は見積原価履歴マスタの見積原価行番号が担う
        
        $curproductprice = $rowData['monetary'] == 1 ? floor_plus($rowData['price'], 2): floor_plus($rowData['price'], 4);
        $cursubtotalprice = $curproductprice * $rowData['quantity'];
        $cursubtotalprice = $rowData['monetary'] == 1 ? floor_plus($cursubtotalprice, 0): floor_plus($cursubtotalprice, 2);
        if ($rowData["detailRevisionNo"] >= 0) {
            $data = array(
                'lngreceiveno' => 'lngreceiveno',
                'lngreceivedetailno' => 'lngreceivedetailno',
                'lngrevisionno' => $rowData['detailRevisionNo'] + 1,
                'strproductcode' => "'" . $this->productCode . "'",
                'strrevisecode' => "'" . $this->reviseCode . "'",
                'lngsalesclasscode' => $rowData['classItem'],
                'dtmdeliverydate' => "TO_TIMESTAMP('" . $rowData['delivery'] . "', 'YYYY/MM/DD')",
                'lngconversionclasscode' => 'lngconversionclasscode',
                'curproductprice' => $curproductprice,
                'lngproductquantity' => $rowData['quantity'],
                'lngproductunitcode' => 'lngproductunitcode',
                'lngunitquantity' => 'lngunitquantity',
                'cursubtotalprice' => $cursubtotalprice,
                'strnote' => "'" . $rowData['note'] . "'",
                'lngsortkey' => $sortKey,
                'lngestimateno' => $this->estimateNo,
                'lngestimatedetailno' => $estimateDetailNo,
                'lngestimaterevisionno' => $rowData['detailRevisionNo'] + 1,

            );

            $condition = "WHERE lngestimateno =" . $this->estimateNo;
            $condition .= " AND lngestimatedetailno = " . $previousDetailNo;
//            $condition .= " AND lngestimaterevisionno = ". $previousRevisionNo;
            $condition .= " AND lngestimaterevisionno = " . $rowData['detailRevisionNo'];

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
                'lngrevisionno' => $rowData['detailRevisionNo'] + 1,
                'strproductcode' => "'" . $this->productCode . "'",
                'strrevisecode' => "'" . $this->reviseCode . "'",
                'lngsalesclasscode' => $rowData['classItem'],
                'dtmdeliverydate' => "TO_TIMESTAMP('" . $rowData['delivery'] . "', 'YYYY/MM/DD')",
                'lngconversionclasscode' => 'NULL',
                'curproductprice' => $curproductprice,
                'lngproductquantity' => $rowData['quantity'],
                'lngproductunitcode' => 1,
                'lngunitquantity' => 1,
                'cursubtotalprice' => $cursubtotalprice,
                'strnote' => "'" . $rowData['note'] . "'",
                'lngsortkey' => $sortKey,
                'lngestimateno' => $this->estimateNo,
                'lngestimatedetailno' => $estimateDetailNo,
                'lngestimaterevisionno' => $rowData['detailRevisionNo'] + 1,
            );

            // クエリの生成
            $strQuery = $this->makeInsertSelectQuery($table, $data);

            // クエリの実行
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

            // 行情報に受注番号をセットし返却
            $rowData['receiveNo'] = $receiveNo;

            $this->objDB->freeResult($resultID);

            return;
        }
    }

    /**
     * DB登録用関数
     *
     *    受注マスタへの登録を行う
     *
     *   @param array $rowData 行のデータ
     *   @param string $receiveCode 受注コード
     *
     *    @return true
     */
    protected function updateMasterReceive(&$rowData)
    {
        // テーブルの設定
        $table = 'm_receive';

        $receiveNo = $rowData['receiveNo']; // 受注番号
        $revisionNo = $this->preReceiveRevisionNo + 1; // 登録に使用する受注のリビジョン番号

        $previousDetailNo = $rowData['previousDetailNo']; // 以前の見積原価明細番号

        // 登録データの作成
        if ($rowData["detailRevisionNo"] >= 0) { // 以前のリビジョンで見積原価明細行番号が存在する場合

            $data = array(
                'lngreceiveno' => 'lngreceiveno',
                'lngrevisionno' => $rowData['detailRevisionNo'] + 1,
                'strreceivecode' => "strreceivecode",
                'strrevisecode' => "strrevisecode",
                'dtmappropriationdate' => 'dtmappropriationdate',
                'lngcustomercompanycode' => $this->companyCodeList[$rowData['customerCompany']],
                'lnggroupcode' => $this->groupCode,
                'lngusercode' => $this->inchargeUserCode,
                'lngreceivestatuscode' => 'lngreceivestatuscode',
                'lngmonetaryunitcode' => $rowData['monetary'],
                'lngmonetaryratecode' => 'lngmonetaryratecode',
                'curconversionrate' => 'curconversionrate',
                'lnginputusercode' => $this->inputUserCode,
                'bytinvalidflag' => 'bytinvalidflag',
                'dtminsertdate' => "'" . fncGetDateTimeString() . "'",
                'strcustomerreceivecode' => 'strcustomerreceivecode',
            );

            $condition = "WHERE lngreceiveno =" . $receiveNo;
            $condition .= " AND lngrevisionno = " . $rowData['detailRevisionNo'];

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
                'lngrevisionno' => $rowData['detailRevisionNo'] + 1,
                'strreceivecode' => "'" . $this->receiveCode . "'",
                'strrevisecode' => "'" . $this->reviseCode . "'",
                'dtmappropriationdate' => "'" . fncGetDateTimeString() . "'",
                'lngcustomercompanycode' => $this->companyCodeList[$rowData['customerCompany']],
                'lnggroupcode' => $this->groupCode,
                'lngusercode' => $this->inchargeUserCode,
                'lngreceivestatuscode' => DEF_RECEIVE_APPLICATE,
                'lngmonetaryunitcode' => $rowData['monetary'],
                'lngmonetaryratecode' => 'NULL',
                'curconversionrate' => 'NULL',
                'lnginputusercode' => $this->inputUserCode,
                'bytinvalidflag' => 'false',
                'dtminsertdate' => "'" . fncGetDateTimeString() . "'",
                'strcustomerreceivecode' => 'NULL',
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
     *    発注明細テーブルへの登録を行う
     *
     *   @param array $rowData 行のデータ
     *   @param array $receiveDetailNoList 受注明細番号リスト
     *   @param integer $defaultSortKey 新しく追加する明細用のソートキー
     *
     *    @return true
     */
    protected function updateTableOrderDetail(&$rowData, $sortKey)
    {
        // テーブルの設定
        $table = 't_orderdetail';

        $previousRevisionNo = $this->revisionNo - 1; // 1つ前の見積原価のリビジョン番号

        $revisionNo = $this->preOrderRevisionNo + 1; // 登録に使用する発注のリビジョン番号

        $previousDetailNo = $rowData['previousDetailNo']; // 以前の見積原価明細番号（検索用）
        //        $estimateDetailNo = $rowData['currentDetailNo'];  // 今回の見積原価明細番号（登録用)
        $estimateDetailNo = $rowData['previousDetailNo']; // 今回の見積原価明細番号（登録用)←不変。見積原価明細番号は見積原価履歴マスタの見積原価行番号が担う

        $detailRevisionNo = $rowData['detailRevisionNo'] + 1;
        $updateFlag = true;
        if ($rowData['areaCode'] == DEF_AREA_OTHER_COST_ORDER &&
            $rowData['divisionSubject'] == DEF_STOCK_SUBJECT_CODE_MATERIAL_PARTS_COST &&
            $rowData['classItem'] == DEF_STOCK_ITEM_CODE_CERTIFICATE
        ) {
            if ($rowData['orderStatusCode'] == 0) {
                $updateFlag = false;
                $detailRevisionNo = 0;
            }

        }
        
        $curproductprice = $rowData['monetary'] == 1 ? floor_plus($rowData['price'], 2): floor_plus($rowData['price'], 4);
        $cursubtotalprice = $curproductprice * $rowData['quantity'];
        $cursubtotalprice = $rowData['monetary'] == 1 ? floor_plus($cursubtotalprice, 0): floor_plus($cursubtotalprice, 2);
        if ($rowData["detailRevisionNo"] >= 0 && $updateFlag) {

            if (($rowData['divisionSubject'] == 433 and $rowData['classItem'] == 1) || 
            ($rowData['divisionSubject'] == 431 and $rowData['classItem'] == 8)) {

                $data = array(
                    'lngorderno' => 'lngorderno',
                    'lngorderdetailno' => 'lngorderdetailno',
                    'lngrevisionno' => $rowData['detailRevisionNo'] + 1,
                    'strproductcode' => "'" . $this->productCode . "'",
                    'strrevisecode' => "'" . $this->reviseCode . "'",
                    'lngstocksubjectcode' => $rowData['divisionSubject'],
                    'lngstockitemcode' => $rowData['classItem'],
                    'dtmdeliverydate' => "TO_TIMESTAMP('" . $rowData['delivery'] . "', 'YYYY/MM/DD')",
                    'lngdeliverymethodcode' => 'lngdeliverymethodcode',
                    'lngconversionclasscode' => 'lngconversionclasscode',
                    'curproductprice' => $curproductprice,
                    'lngproductquantity' => $rowData['quantity'],
                    'lngproductunitcode' => 'lngproductunitcode',
                    'cursubtotalprice' => $cursubtotalprice,
                    'strnote' => "'" . $rowData['note'] . "'",
                    'strmoldno' => 'strmoldno',
                    'lngsortkey' => (int) $sortKey,
                    'lngestimateno' => $this->estimateNo,
                    'lngestimatedetailno' => $estimateDetailNo,
                    'lngestimaterevisionno' => $rowData['detailRevisionNo'] + 1,
                );
            } else {
                $data = array(
                    'lngorderno' => 'lngorderno',
                    'lngorderdetailno' => 'lngorderdetailno',
                    'lngrevisionno' => $rowData['detailRevisionNo'] + 1,
                    'strproductcode' => "'" . $this->productCode . "'",
                    'strrevisecode' => "'" . $this->reviseCode . "'",
                    'lngstocksubjectcode' => $rowData['divisionSubject'],
                    'lngstockitemcode' => $rowData['classItem'],
                    'dtmdeliverydate' => "TO_TIMESTAMP('" . $rowData['delivery'] . "', 'YYYY/MM/DD')",
                    'lngdeliverymethodcode' => 'lngdeliverymethodcode',
                    'lngconversionclasscode' => 'lngconversionclasscode',
                    'curproductprice' => $curproductprice,
                    'lngproductquantity' => $rowData['quantity'],
                    'lngproductunitcode' => 'lngproductunitcode',
                    'cursubtotalprice' => $cursubtotalprice,
                    'strnote' => "'" . $rowData['note'] . "'",
                    'strmoldno' => 'NULL',
                    'lngsortkey' => (int) $sortKey,
                    'lngestimateno' => $this->estimateNo,
                    'lngestimatedetailno' => $estimateDetailNo,
                    'lngestimaterevisionno' => $rowData['detailRevisionNo'] + 1,
                );
            }

            $condition = "WHERE lngestimateno =" . $this->estimateNo;
            $condition .= " AND lngestimatedetailno = " . $previousDetailNo;
            $condition .= " AND lngestimaterevisionno = " . $rowData['detailRevisionNo'];

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
                'lngrevisionno' => $detailRevisionNo,
                'strproductcode' => "'" . $this->productCode . "'",
                'strrevisecode' => "'" . $this->reviseCode . "'",
                'lngstocksubjectcode' => $rowData['divisionSubject'],
                'lngstockitemcode' => $rowData['classItem'],
                'dtmdeliverydate' => "TO_TIMESTAMP('" . $rowData['delivery'] . "', 'YYYY/MM/DD')",
                'lngdeliverymethodcode' => 'NULL',
                'lngconversionclasscode' => DEF_CONVERSION_SEIHIN,
                'curproductprice' => $curproductprice,
                'lngproductquantity' => $rowData['quantity'],
                'lngproductunitcode' => DEF_PRODUCTUNIT_PCS,
                'cursubtotalprice' => $cursubtotalprice,
                'strnote' => "'" . $rowData['note'] . "'",
                'strmoldno' => 'NULL',
                'lngsortkey' => (int) $sortKey,
                'lngestimateno' => $this->estimateNo,
                'lngestimatedetailno' => $estimateDetailNo,
                'lngestimaterevisionno' => $rowData['detailRevisionNo'] + 1,

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
     *    発注マスタへの登録を行う
     *
     *   @param array $rowData 行のデータ
     *   @param string $orderCode 発注コード
     *
     *    @return true
     */
    protected function updateMasterOrder(&$rowData)
    {
        // テーブルの設定
        $table = 'm_order';

        $orderNo = $rowData['orderNo']; // 発注番号
        $revisionNo = $this->preOrderRevisionNo + 1; // 登録に使用する発注のリビジョン番号

        $previousDetailNo = $rowData['previousDetailNo']; // 以前の見積原価明細番号
        $detailRevisionNo = $rowData['detailRevisionNo'] + 1;
        if ($rowData['areaCode'] == DEF_AREA_OTHER_COST_ORDER &&
            $rowData['divisionSubject'] == DEF_STOCK_SUBJECT_CODE_MATERIAL_PARTS_COST &&
            $rowData['classItem'] == DEF_STOCK_ITEM_CODE_CERTIFICATE
        ) {
            if ($rowData['orderStatusCode'] == 0) {
                $updateFlag = false;
                $detailRevisionNo = 0;
            }

        }
        if ($rowData["detailRevisionNo"] >= 0 && $updateFlag) {

            $data = array(
                'lngorderno' => $orderNo,
                'lngrevisionno' => $rowData['detailRevisionNo'] + 1,
                'strordercode' => 'strordercode',
                'dtmappropriationdate' => 'dtmappropriationdate',
                'lngcustomercompanycode' => $this->companyCodeList[$rowData['customerCompany']],
                'lnggroupcode' => $this->groupCode,
                'lngusercode' => $this->inchargeUserCode,
                'lngorderstatuscode' => 'lngorderstatuscode',
                'lngmonetaryunitcode' => $rowData['monetary'],
                'lngmonetaryratecode' => 'lngmonetaryratecode',
                'curconversionrate' => 'curconversionrate',
                'lngpayconditioncode' => 'lngpayconditioncode',
                'lngdeliveryplacecode' => 'lngdeliveryplacecode',
                'lnginputusercode' => $this->inputUserCode,
                'bytinvalidflag' => 'bytinvalidflag',
                'dtminsertdate' => "'" . fncGetDateTimeString() . "'",
            );

            $condition = "WHERE lngorderno =" . $orderNo;
            $condition .= " AND lngrevisionno = " . $rowData['detailRevisionNo'];

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
            $timeString = "'" . fncGetDateTimeString() . "'";
            $data = array(
                'lngorderno' => $orderNo,
                'lngrevisionno' => $detailRevisionNo,
                'strordercode' => "'" . $this->orderCode . "'",
                'dtmappropriationdate' => $timeString,
                'lngcustomercompanycode' => $this->companyCodeList[$rowData['customerCompany']],
                'lnggroupcode' => $this->groupCode,
                'lngusercode' => $this->inchargeUserCode,
                'lngorderstatuscode' => DEF_ORDER_APPLICATE,
                'lngmonetaryunitcode' => $rowData['monetary'],
                'lngmonetaryratecode' => 'NULL',
                'curconversionrate' => 'NULL',
                'lngpayconditioncode' => 'NULL',
                'lngdeliveryplacecode' => 'NULL',
                'lnginputusercode' => $this->inputUserCode,
                'bytinvalidflag' => 'false',
                'dtminsertdate' => $timeString,
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
     *    商品化企画テーブルへの登録を行う
     *
     *    @return true
     */
    protected function updateTableGoodsPlan()
    {

        $table = 't_goodsplan';

        $previousRevisionNo = $this->productRevisionNo - 1;

        $data = array(
            'lnggoodsplancode' => 'lnggoodsplancode',
            'lngrevisionno' => $this->productRevisionNo,
            'lngproductno' => 'lngproductno',
            'strrevisecode' => 'strrevisecode',
            'dtmcreationdate' => "'" . fncGetDateTimeString() . "'",
            'dtmrevisiondate' => 'dtmrevisiondate',
            'lnggoodsplanprogresscode' => 'lnggoodsplanprogresscode',
            'lnginputusercode' => $this->inputUserCode,
        );

        $condition = "WHERE lngproductno =" . $this->productNo;
        $condition .= " AND lngrevisionno = " . $previousRevisionNo;
        $condition .= " AND strrevisecode = '" . $this->reviseCode . "'";

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
     *    売上明細テーブルの修正を行う
     *
     *    @return true
     */
    protected function updateTableSalesDetail($rowData)
    {

        $receiveNo = $rowData['receiveNo'];
        $preReceiveRevisionNo = $this->preReceiveRevisionNo;

        $strQuery = "UPDATE";
        $strQuery .= " t_salesdetail";
        $strQuery .= " SET";
        $strQuery .= " lngreceiverevisionno = " . $preReceiveRevisionNo . " + 1";
        $strQuery .= " WHERE lngreceiveno = " . $receiveNo;
        $strQuery .= " AND lngreceiverevisionno = " . $preReceiveRevisionNo;
        $strQuery .= " AND lngsalesno not in (select lngsalesno from m_sales where lngrevisionno < 0)";

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
     * DB登録用関数
     *
     *    納品伝票明細テーブルの修正を行う
     *
     *    @return true
     */
    protected function updateTableSlipDetail($rowData)
    {

        $receiveNo = $rowData['receiveNo'];
        $preReceiveRevisionNo = $this->preReceiveRevisionNo;

        $strQuery = "UPDATE";
        $strQuery .= " t_slipdetail";
        $strQuery .= " SET";
        $strQuery .= " lngreceiverevisionno = " . $preReceiveRevisionNo . " + 1";
        $strQuery .= " WHERE lngreceiveno = " . $receiveNo;
        $strQuery .= " AND lngreceiverevisionno = " . $preReceiveRevisionNo;
        $strQuery .= " AND lngslipno not in (select lngslipno from m_slip where lngrevisionno < 0)";

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
     * DB登録用関数
     *
     *    発注書詳細テーブルの修正を行う
     *
     *    @return true
     */
    protected function updateTablePurchaseOrderDetail($rowData)
    {

        $orderNo = $rowData['orderNo'];
        $preOrderRevisionNo = $this->preOrderRevisionNo;

        $strQuery = "UPDATE";
        $strQuery .= " t_purchaseorderdetail";
        $strQuery .= " SET";
        $strQuery .= " lngorderrevisionno =" . $preOrderRevisionNo . " + 1";
        $strQuery .= " WHERE lngorderno = " . $orderNo;
        $strQuery .= " AND lngorderrevisionno = " . $preOrderRevisionNo;
        $strQuery .= " AND lngpurchaseorderno not in (select lngpurchaseorderno from m_purchaseorder where lngrevisionno < 0)";
        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
     * DB登録用関数
     *
     *    仕入明細テーブルの修正を行う
     *
     *    @return true
     */
    protected function updateTableStockDetail($rowData)
    {

        $orderNo = $rowData['orderNo'];
        $preOrderRevisionNo = $this->preOrderRevisionNo;

        $strQuery = "UPDATE";
        $strQuery .= " t_stockdetail";
        $strQuery .= " SET";
        $strQuery .= " lngorderrevisionno = " . $preOrderRevisionNo . " + 1";
        $strQuery .= " WHERE lngorderno = " . $orderNo;
        $strQuery .= " AND lngorderrevisionno = " . $preOrderRevisionNo;
        $strQuery .= " AND lngstockno not in (select lngstockno from m_stock where lngrevisionno < 0)";

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
     * DB検索用関数
     *
     *    条件を満たすレコードが存在するか確認する
     *
     *   @param string $table
     *   @param array $data   カラム名をキーに持つ、検索条件の配列 ($data['カラム名']] = 検索条件);
     *
     *    @return boolean
     */
    protected function confirmExistenceRecord($table, $data)
    {
        $sqlQuery = "SELECT * FROM " . $table;
        $sqlQuery .= " WHERE";

        foreach ($data as $col => $condition) {

            if ($search) {
                $search = ", AND " . $col . " = " . $condition;
            } else {
                $search = " " . $col . " = " . $condition;
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

    /**
     *    検索結果の最初のレコードから指定したカラムの値を取得する
     *
     *   @param string $table            検索するテーブル名
     *   @param string $needle           値を取得するカラム
     *   @param string $search           検索条件
     *   @param string $sort             'max' or 'min'
     *
     */
    protected function getFirstRecordValue($table, $needle, $search, $sort = null)
    {
        $strQuery = "SELECT " . $needle . " FROM " . $table . " " . $search;

        // $sort指定がある場合は最大値又は最小値を返す
        if (isset($sort)) {
            $sortString = mb_strtolower($sort);
            if ($sortString === 'min') {
                $orderQuery = " ORDER BY " . $needle . " ASC";
            } else {
                $orderQuery = " ORDER BY " . $needle . " DESC";
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
    protected function getReceiveCode()
    {
        $strQuery = "SELECT";
        $strQuery .= " strreceivecode";
        $strQuery .= " FROM";
        $strQuery .= " m_receive a";
        $strQuery .= " INNER JOIN";
        $strQuery .= " t_receivedetail b";
        $strQuery .= " ON a.lngreceiveno = b.lngreceiveno";
        $strQuery .= " AND a.lngrevisionno = b.lngrevisionno";
        $strQuery .= " WHERE b.lngestimateno = " . $this->estimateNo;

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
    protected function getOrderCode()
    {
        $strQuery = "SELECT";
        $strQuery .= " strordercode";
        $strQuery .= " FROM";
        $strQuery .= " m_order a";
        $strQuery .= " INNER JOIN";
        $strQuery .= " t_orderdetail b";
        $strQuery .= " ON a.lngorderno = b.lngorderno";
        $strQuery .= " AND a.lngrevisionno = b.lngrevisionno";
        $strQuery .= " WHERE b.lngestimateno = " . $this->estimateNo;

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

    // 見積原価番号から新規明細番号を取得する
    protected function getNewDetailNo()
    {
        $strQuery = "SELECT";
        $strQuery .= " MAX(lngestimatedetailno) + 1 as lngestimatedetailno";
        $strQuery .= " FROM";
        $strQuery .= " t_estimatedetail";
        $strQuery .= " WHERE lngestimateno = " . $this->estimateNo;

        list($resultID, $resuluNumber) = fncQuery($strQuery, $this->objDB);

        // 最初のレコードの値を返却する
        if ($resuluNumber > 0) {
            $result = pg_fetch_object($resultID, 0);
            $ret = $result->lngestimatedetailno;
        } else {
            $ret = false;
        }

        return $ret;
    }

    // 見積原価明細の最新リビジョンを取得する。
    protected function getCurrentDetailRevision($detailNo)
    {
        $strQuery = "SELECT";
        $strQuery .= " MAX(lngrevisionno) as lngrevisionno";
        $strQuery .= " FROM";
        $strQuery .= " t_estimatedetail";
        $strQuery .= " WHERE lngestimateno = " . $this->estimateNo;
        $strQuery .= " AND lngestimatedetailno = " . $detailNo;

        list($resultID, $resuluNumber) = fncQuery($strQuery, $this->objDB);

        // 最初のレコードの値を返却する
        if ($resuluNumber > 0) {
            $result = pg_fetch_object($resultID, 0);
            $ret = $result->lngrevisionno;
        } else {
            $ret = false;
        }

        return $ret;
    }

    // 受注ステータス取得
    protected function getReceiveStatus($rowData)
    {
        $strQuery = "SELECT";
        $strQuery .= " mr.lngreceivestatuscode";
        $strQuery .= " FROM";
        $strQuery .= " m_receive mr";
        $strQuery .= " INNER JOIN t_receivedetail tr";
        $strQuery .= " ON tr.lngreceiveno = mr.lngreceiveno";
        $strQuery .= " AND tr.lngrevisionno = mr.lngrevisionno";
        $strQuery .= " WHERE tr.lngestimateno = " . $this->estimateNo;
        $strQuery .= " AND tr.lngestimatedetailno = " . $rowData["previousDetailNo"];
        $strQuery .= " AND tr.lngestimaterevisionno = " . $rowData["detailRevisionNo"];
        list($resultID, $resuluNumber) = fncQuery($strQuery, $this->objDB);

        // 最初のレコードの値を返却する
        if ($resuluNumber > 0) {
            $result = pg_fetch_object($resultID, 0);
            $ret = $result->lngreceivestatuscode;
        } else {
            $ret = false;
        }
        return $ret;
    }

    // 発注ステータス取得
    protected function getOrderStatus($rowData)
    {
        $strQuery = "SELECT";
        $strQuery .= " mo.lngorderstatuscode";
        $strQuery .= " FROM";
        $strQuery .= " m_order mo";
        $strQuery .= " INNER JOIN t_orderdetail tod";
        $strQuery .= " ON tod.lngorderno = mo.lngorderno";
        $strQuery .= " AND tod.lngrevisionno = mo.lngrevisionno";
        $strQuery .= " WHERE tod.lngestimateno = " . $this->estimateNo;
        $strQuery .= " AND tod.lngestimatedetailno = " . $rowData["previousDetailNo"];
        $strQuery .= " AND tod.lngestimaterevisionno = " . $rowData["detailRevisionNo"];

        list($resultID, $resuluNumber) = fncQuery($strQuery, $this->objDB);

        // 最初のレコードの値を返却する
        if ($resuluNumber > 0) {
            $result = pg_fetch_object($resultID, 0);
            $ret = $result->lngorderstatuscode;
        } else {
            $ret = false;
        }
        return $ret;
    }

    // 削除対象明細の修正および論理削除用のレコードの追加を行う
    protected function insertDeleteRecord()
    {
//        $this->updateDeleteReceive(); //受注コード更新は見積原価削除時に。
        $this->insertDeleteReceive();
        $this->insertDeleteOrder();
        return;
    }

    // 受注マスタの削除対象明細の受注コードを修正する
    protected function updateDeleteReceive()
    {

        $previousRevisionNo = $this->revisionNo - 1;

        $strQuery = "UPDATE m_receive mr";
        $strQuery .= " SET strreceivecode = CASE WHEN strreceivecode ~ '^\*.+\*$' THEN strreceivecode ELSE '*' || strreceivecode || '*' END";
        // 以下、登録後のリビジョン番号が存在しない受注番号を取得する
        $strQuery .= " FROM";
        $strQuery .= " (";
        $strQuery .= "SELECT mr2.lngreceiveno";
        $strQuery .= " FROM t_receivedetail trd";
        $strQuery .= " INNER JOIN m_receive mr2";
        $strQuery .= " ON mr2.lngreceiveno = trd.lngreceiveno";
        $strQuery .= " AND mr2.lngrevisionno = trd.lngrevisionno";
        $strQuery .= " WHERE trd.lngestimateno = " . $this->estimateNo;
        $strQuery .= " AND (trd.lngestimatedetailno, trd.lngestimaterevisionno) IN(";
        $strQuery .= " select mh1.lngestimatedetailno, mh1.lngestimatedetailrevisionno";
        $strQuery .= " from m_estimatehistory mh1";
        $strQuery .= " left outer join m_estimatehistory mh2";
        $strQuery .= "     on mh2.lngestimateno = mh1.lngestimateno";
        $strQuery .= " and mh2.lngrevisionno = mh1.lngrevisionno +1";
        $strQuery .= " and mh2.lngestimatedetailno = mh1.lngestimatedetailno";
        $strQuery .= " and mh2.lngestimatedetailrevisionno = mh1.lngestimatedetailrevisionno +1";
        $strQuery .= " where mh1.lngestimateno = " . $this->estimateNo;
        $strQuery .= " and mh1.lngrevisionno=" . $previousRevisionNo;
        $strQuery .= " and mh2.lngestimateno is null";
        $strQuery .= " )";
        $strQuery .= ") sub";
        $strQuery .= " WHERE sub.lngreceiveno = mr.lngreceiveno"; // 受注番号を結合
        //fncDebug("kids2.log", $strQuery, __FILE__, __LINE__, "a");

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    // 受注マスタに論理削除用レコードを追加する
    protected function insertDeleteReceive()
    {

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
        $strQuery .= " mr.strreceivecode,";
        $strQuery .= " " . $this->inputUserCode . ",";
        $strQuery .= " false,";
        $strQuery .= " '" . fncGetDateTimeString() . "',";
        $strQuery .= " mr.strrevisecode";

        $strQuery .= " FROM t_receivedetail trd";

        // 受注明細テーブルに受注マスタを結合
        $strQuery .= " INNER JOIN m_receive mr";
        $strQuery .= " ON mr.lngreceiveno = trd.lngreceiveno";
        $strQuery .= " AND mr.lngrevisionno = trd.lngrevisionno";

        // 修正後のリビジョンが存在しないものだけを対象とするWHERE句
        $strQuery .= " WHERE trd.lngestimateno = " . $this->estimateNo;
        $strQuery .= " AND (trd.lngestimatedetailno, trd.lngestimaterevisionno) IN(";
        $strQuery .= " select mh1.lngestimatedetailno, mh1.lngestimatedetailrevisionno";
        $strQuery .= " from m_estimatehistory mh1";
        $strQuery .= " left outer join m_estimatehistory mh2";
        $strQuery .= "     on mh2.lngestimateno = mh1.lngestimateno";
        $strQuery .= " and mh2.lngrevisionno = mh1.lngrevisionno +1";
        $strQuery .= " and mh2.lngestimatedetailno = mh1.lngestimatedetailno";
//        $strQuery .= " and mh2.lngestimatedetailrevisionno = mh1.lngestimatedetailrevisionno +1";
        $strQuery .= " where mh1.lngestimateno = " . $this->estimateNo;
        $strQuery .= " and mh1.lngrevisionno=" . $previousRevisionNo;
        $strQuery .= " and mh2.lngestimateno is null";
        $strQuery .= " )";
//fncDebug("kids2.log", $strQuery, __FILE__, __LINE__, "a");

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    // 発注マスタに論理削除用レコードを追加する
    protected function insertDeleteOrder()
    {

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
        $strQuery .= " " . $this->inputUserCode . ",";
        $strQuery .= " false,";
        $strQuery .= " '" . fncGetDateTimeString() . "'";

        $strQuery .= " FROM t_orderdetail tod";
        $strQuery .= " INNER JOIN m_order mo";
        $strQuery .= " ON mo.lngorderno = tod.lngorderno";
        $strQuery .= " AND mo.lngrevisionno = tod.lngrevisionno";
        $strQuery .= " WHERE tod.lngestimateno = " . $this->estimateNo;
        $strQuery .= " AND (tod.lngestimatedetailno, tod.lngestimaterevisionno) IN(";
        $strQuery .= " select mh1.lngestimatedetailno, mh1.lngestimatedetailrevisionno";
        $strQuery .= " from m_estimatehistory mh1";
        $strQuery .= " left outer join m_estimatehistory mh2";
        $strQuery .= "     on mh2.lngestimateno = mh1.lngestimateno";
        $strQuery .= " and mh2.lngrevisionno = mh1.lngrevisionno +1";
        $strQuery .= " and mh2.lngestimatedetailno = mh1.lngestimatedetailno";
//        $strQuery .= " and mh2.lngestimatedetailrevisionno = mh1.lngestimatedetailrevisionno +1";
        $strQuery .= " where mh1.lngestimateno = " . $this->estimateNo;
        $strQuery .= " and mh1.lngrevisionno=" . $previousRevisionNo;
        $strQuery .= " and mh2.lngestimateno is null";
        $strQuery .= " )";
//fncDebug("kids2.log", $strQuery, __FILE__, __LINE__, "a");

        // クエリの実行
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }
}
