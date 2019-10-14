<?php

require_once ('conf.inc');
require_once ( LIB_ROOT . "lib.php");

require_once ( SRC_ROOT . "estimate/cmn/const/workSheetConst.php");
require_once ( SRC_ROOT . "estimate/cmn/estimateInsertData.php");

// ��Ͽ�ѥǡ����������饹
class updateInsertData extends estimateInsertData {

    public function __construct() {
      parent::__construct();
    }
    

    // ɬ�פʥѥ�᡼���򥯥饹�˥��åȤ���
    public function setUpdateParam($update, $inputUserCode, $productCode, $reviseCode, $revisionNo, $objDB) {
        if (is_array($update)) {

            // �����͡��桼���������ɡ�DB���饹�Υ��å�
            $this->setParam($update, $inputUserCode, $objDB);

            // ���ʥ����ɤΥ��å�
            $this->productCode = $productCode;

            // ���Υ����ɤΥ��å�
            $this->reviseCode = $reviseCode;

            // ��ӥ�����ֹ�򥻥åȤ���
            $this->revisionNo = $revisionNo;

            // ���롼�ץ����ɤμ���
            $groupRecord = $this->objDB->getGroupRecordForDisplay($this->headerData[workSheetConst::INCHARGE_GROUP_CODE]);
            $this->groupCode = $groupRecord->lnggroupcode;

            // �桼���������ɤμ���
            $inchargeUserRecord = $this->objDB->getUserRecordForDisplay($this->headerData[workSheetConst::INCHARGE_USER_CODE]);
            $this->inchargeUserCode = $inchargeUserRecord->lngusercode;

            // ��ȯô���ԥ����ɤμ���
            $developUserRecord = $this->objDB->getUserRecordForDisplay($this->headerData[workSheetConst::DEVELOP_USER_CODE]);
            $this->developUserCode = $developUserRecord->lngusercode;

            // ɽ����ҥ����ɤ򥭡��ˤ�Ĳ�ҥ����ɤ��������
            $this->companyCodeList = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", "Array", "", $this->objDB );
            
        } else {
            return false;
        }

        return true;
    }

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	���Ѹ�����Ͽ��Ԥ�
    *   
    *	@return true
    */
    public function update() {

        // ���ʥޥ�������Ͽ����
        $this->updateMasterProduct();

        // ���Ѹ����ޥ�������Ͽ����
        $this->updateMasterEstimate();

        // ���ʲ����ơ��֥����Ͽ����
        $this->updateTableGoodsPlan();

        $rowDataList = $this->rowDataList;


        // �Ԥξ��󤫤�����θ��Ѹ����ֹ���������
        $detailNoList = [];
        foreach ($rowDataList as $rowData) {
            $previousDetailNo = $rowData['previousDetailNo'];

            if ($previousDetailNo) {
                // ����ޥ�����ȯ��ޥ��������줫����Ͽ��������ٹԤˤĤ��ơ������θ��Ѹ������ٹ��ֹ���������
                if ($rowData['areaCode'] !== DEF_AREA_OTHER_COST_ORDER) {

                    $detailNoList[] = $previousDetailNo;
                }
            }
        }

        // �������٥ơ��֥롢ȯ�����٥ơ��֥�κƺ��֤��������ȥ��������
        $receiveTable = 't_receivedetail';
        $orderTable = 't_orderdetail';

        $receiveSortKeyList = $this->getRenumberSortKeyList($receiveTable, $detailNoList, $receiveNewSortKey); // �������٥ơ��֥���
        $orderSortKeyList = $this->getRenumberSortKeyList($orderTable, $detailNoList, $orderNewSortKey);       // ȯ�����٥ơ��֥���


        // �������٥ơ��֥롢ȯ�����٥ơ��֥�������ֹ�κ����ͤ�����������åȤ���
        $search = "WHERE lngestimateno = ". $this->estimateNo; // ������說����

        $this->maxReceiveDetailNo = $this->getFirstRecordValue($receiveTable, 'lngreceivedetailno', $search, 'max'); // �����ֹ�κ�����
        $this->maxOrderDetailNo = $this->getFirstRecordValue($orderTable, 'lngorderdetailno', $search, 'max');       // ȯ���ֹ�κ�����


        // ���Ѹ����ֹ�ڤӸ��Ѹ�����ӥ�����ֹ��ɳ�դ��������٥ơ��֥롢ȯ�����٥ơ��֥�κ���Υ�ӥ�����ֹ���������
        $searchRevision = "WHERE lngestimateno = ". $this->estimateNo; // ������說����
        $searchRevision .= " AND lngestimaterevisionno = ". ((int)$this->revisionNo - 1);

        $this->preReceiveRevisionNo = $this->getFirstRecordValue($receiveTable, 'lngrevisionno', $searchRevision, 'max');
        $this->preOrderRevisionNo = $this->getFirstRecordValue($orderTable, 'lngrevisionno', $searchRevision, 'max');


        // ���Ѹ����ֹ��ɳ�դ��������ɡ�ȯ�����ɤ��������
        $this->receiveCode = $this->getReceiveCode();
        $this->orderCode = $this->getOrderCode();

        
        // ���ٹԤ���Ͽ
        foreach ($rowDataList as $rowData) {
            // ���Ѹ��������ֹ�Υ��󥯥����
            ++$estimateDetailNo;

            // ���Ѹ������٥ơ��֥����Ͽ����
            $this->updateTableEstimateDetail($rowData, $estimateDetailNo);

            $salesOrder = $rowData['salesOrder'];

            // ����ξ��
            if ($salesOrder === DEF_ATTRIBUTE_CLIENT) {

                // �������٥ơ��֥���Ͽ����
                $this->updateTableReceiveDetail($rowData, $receiveSortKeyList, $receiveNewSortKey);

                // ����ޥ�����Ͽ����
                $this->updateMasterReceive($rowData);

                if ($rowData['receiveStatusCode'] === DEF_RECEIVE_END
                    || $rowData['receiveStatusCode'] === DEF_RECEIVE_CLOSED) {
                    // Ǽ�ʺѡ��ޤ�������Ѥξ��
                    $this->updateTableSalesDetail($rowData);
                    $this->updateTableSlipDetail($rowData);

                }

            // ȯ��ξ��
            } else if ($salesOrder === DEF_ATTRIBUTE_SUPPLIER) {
                // ȯ��Ƿ���ʳ��ξ��
                if ($rowData['areaCode'] !== DEF_AREA_OTHER_COST_ORDER) {

                    // ȯ�����٥ơ��֥���Ͽ����
                    $this->updateTableOrderDetail($rowData, $orderSortKeyList, $orderNewSortKey);

                    // ȯ��ޥ�����Ͽ����
                    $this->updateMasterOrder($rowData);
                }

                if ($rowData['orderStatusCode'] === DEF_ORDER_ORDER) {
                    // ȯ��ξ��
                    $this->updateTablePurchaseOrderDetail($rowData);

                } else if ($rowData['orderStatusCode'] === DEF_ORDER_END
                    || $rowData['orderStatusCode'] === DEF_ORDER_CLOSED) {

                    // Ǽ�ʺѡ��ޤ�������Ѥξ��
                    $this->updateTablePurchaseOrderDetail($rowData);
                    $this->updateTableStockDetail($rowData);
                }
 
            }
        }

        // ����ޥ�����ȯ��ޥ�������ǡ����κ����Ԥ�
        $this->insertDeleteRecord();
        
        return true;
    }


    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	���ʥޥ������Խ������ǡ������Խ���Ͽ��Ԥ�
    *
    *	@return true
    */
    protected function updateMasterProduct() {

        $table = 'm_product';

        // ��Ͽ�ǡ���
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

        // �����������
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

        // �����������
        $strQuery = $this->makeInsertSelectQuery($table, $data, $join, $returning);
        
        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $result = pg_fetch_object($resultID, 0);

        // 
        $this->productNo = $result->lngproductno;

        // ���ʥ�ӥ�����ֹ�򥻥åȤ���
        $this->productRevisionNo = $result->lngrevisionno;

        $this->objDB->freeResult($resultID);

        return true;
    }
    
// ��������

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	���Ѹ����ޥ����ؤ��Խ���Ͽ��Ԥ�
    *   
    *	@return true
    */
    protected function updateMasterEstimate() {
        $table = 'm_estimate';

        // ����
        $retailPrice = $this->headerData[workSheetConst::RETAIL_PRICE];
        // ���ѿ�
        $productionQuantity = $this->headerData[workSheetConst::PRODUCTION_QUANTITY];

        // ��׶�ۤη׻�
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

        // �����������
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

        // �����������
        $strQuery = $this->makeInsertSelectQuery($table, $data, $join, $returning);
        
        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $result = pg_fetch_object($resultID, 0);

        // ���Ѹ����ֹ�򥻥åȤ���
        $this->estimateNo = $result->lngestimateno;

        // ��ӥ�����ֹ�򥻥åȤ���
        $this->revisionNo = $result->lngrevisionno;

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	���Ѹ������٥ơ��֥�ؤ��Խ���Ͽ��Ԥ�
    *
    *   @param array $rowData �ԤΥǡ���
    *   @param integer $estimateDetailNo ���Ѹ��������ֹ�
    *
    *	@return true
    */
    protected function updateTableEstimateDetail(&$rowData, $estimateDetailNo) {
        // �ơ��֥�̾������
        $table = 't_estimatedetail';

        // ����ξ��
        if ($rowData['salesOrder'] === DEF_ATTRIBUTE_CLIENT) {
            $stockSubjectCode = 0;
            $stockItemCode = 0;
            $salesDivisionCode = $rowData['divisionSubject'];
            $salesClassCode = $rowData['classItem'];
        // ȯ��ξ��
        } else if ($rowData['salesOrder'] === DEF_ATTRIBUTE_SUPPLIER) {
            $stockSubjectCode = $rowData['divisionSubject'];
            $stockItemCode = $rowData['classItem'];
            $salesDivisionCode = 0;
            $salesClassCode = 0;
        } else {
            return false;
        }

        $previousRevisionNo = $this->revisionNo - 1;

        // ��Ͽ�ǡ����κ���
        $data = array(
            'lngestimateno' => $this->estimateNo,
            'lngestimatedetailno' => $estimateDetailNo,
            'lngrevisionno' => $this->revisionNo,
            'lngstocksubjectcode'=> $stockSubjectCode,
            'lngstockitemcode' => $stockItemCode,
            'lngcustomercompanycode' => $this->companyCodeList[$rowData['customerCompany']],
            'dtmdelivery' => $rowData['delivery'] ? "TO_TIMESTAMP('". $rowData['delivery']. "', 'YYYY/MM/DD')" : 'null',
            'bytpayofftargetflag' => $rowData['payoff'] == '��' ? 'true' : 'false',
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
        
        // �����������
        $strQuery = $this->makeInsertSelectQuery($table, $data);
        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        // ��Ͽ�˻��Ѥ������ٹ��ֹ��Ԥ˥��åȤ���
        $rowData['currentDetailNo'] = $estimateDetailNo;

        return true;
    }

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	�������٥ơ��֥�ؤ���Ͽ��Ԥ�
    *
    *   @param array $rowData �ԤΥǡ���
    *   @param array $receiveDetailNoList ���������ֹ�ꥹ��
    *   @param integer $defaultSortKey �������ɲä��������ѤΥ����ȥ���
    *   
    *	@return true
    */
    protected function updateTableReceiveDetail(&$rowData, $receiveSortKeyList, &$defaultSortKey) {
        // �ơ��֥������ 
        $table = 't_receivedetail';

        $previousRevisionNo = $this->revisionNo - 1;      // 1�����θ��Ѹ����Υ�ӥ�����ֹ�

        $revisionNo = $this->preReceiveRevisionNo + 1;    // ��Ͽ�˻��Ѥ������Υ�ӥ�����ֹ�

        $previousDetailNo = $rowData['previousDetailNo']; // �����θ��Ѹ��������ֹ�ʸ����ѡ�
        $estimateDetailNo = $rowData['currentDetailNo'];  // ����θ��Ѹ��������ֹ����Ͽ��)

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

            // �����������
            $strQuery = $this->makeInsertSelectQuery($table, $data, $condition, $returning);

            // ������μ¹�
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

            // �����ֹ桢��ӥ�����ֹ�����
            $result = pg_fetch_object($resultID, 0);

            // �Ծ���˼��������ͤ򥻥åȤ��ֵ�
            $rowData['receiveNo'] = $result->lngreceiveno;

            $this->objDB->freeResult($resultID);

            return;            

        } else {
            // �����ֹ�����
            $receiveNo = fncGetSequence("m_receive.lngReceiveNo", $this->objDB);

            // ���������ֹ�κ����ͤΥ��󥯥����
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
    
            // �����������
            $strQuery = $this->makeInsertSelectQuery($table, $data);
    
            // ������μ¹�
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

            // �Ծ���˼����ֹ�򥻥åȤ��ֵ�
            $rowData['receiveNo'] = $receiveNo;
    
            $this->objDB->freeResult($resultID);

            // ���󥯥����
            ++$defaultSortKey;

            return;
        }
    }

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	����ޥ����ؤ���Ͽ��Ԥ�
    *
    *   @param array $rowData �ԤΥǡ���
    *   @param string $receiveCode ��������
    *   
    *	@return true
    */
    protected function updateMasterReceive(&$rowData) {
        // �ơ��֥������ 
        $table = 'm_receive';

        $receiveNo = $rowData['receiveNo'];             // �����ֹ�
        $revisionNo = $this->preReceiveRevisionNo + 1;  // ��Ͽ�˻��Ѥ������Υ�ӥ�����ֹ�

        $previousDetailNo = $rowData['previousDetailNo']; // �����θ��Ѹ��������ֹ�

        // ��Ͽ�ǡ����κ���
        if ($previousDetailNo) { // �����Υ�ӥ����Ǹ��Ѹ������ٹ��ֹ椬¸�ߤ�����

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

            // �����������
            $strQuery = $this->makeInsertSelectQuery($table, $data, $condition, $returning);
            // ������μ¹�
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

            // ������֥����ɤ����
            $result = pg_fetch_object($resultID, 0);

            // �Ծ���˼��������ͤ򥻥åȤ��ֵ�
            $rowData['receiveStatusCode'] = $result->lngreceivestatuscode;

            $this->objDB->freeResult($resultID);

            return true;
        } else {
            // ��Ͽ�ǡ����κ���
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

            // �����������
            $strQuery = $this->makeInsertSelectQuery($table, $data);
            // ������μ¹�
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

            $this->objDB->freeResult($resultID);

            return true;
        }
    }

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	ȯ�����٥ơ��֥�ؤ���Ͽ��Ԥ�
    *
    *   @param array $rowData �ԤΥǡ���
    *   @param array $receiveDetailNoList ���������ֹ�ꥹ��
    *   @param integer $defaultSortKey �������ɲä��������ѤΥ����ȥ���  
    *   
    *	@return true
    */
    protected function updateTableOrderDetail(&$rowData, $orderSortKeyList, &$defaultSortKey) {
        // �ơ��֥������
        $table = 't_orderdetail';

        $previousRevisionNo = $this->revisionNo - 1; // 1�����θ��Ѹ����Υ�ӥ�����ֹ�

        $revisionNo = $this->preOrderRevisionNo + 1;     // ��Ͽ�˻��Ѥ���ȯ��Υ�ӥ�����ֹ�

        $previousDetailNo = $rowData['previousDetailNo']; // �����θ��Ѹ��������ֹ�ʸ����ѡ�
        $estimateDetailNo = $rowData['currentDetailNo'];  // ����θ��Ѹ��������ֹ����Ͽ��)

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

            // �����������
            $strQuery = $this->makeInsertSelectQuery($table, $data, $condition, $returning);
    
            // ������μ¹�
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

            // �����ֹ桢��ӥ�����ֹ�����
            $result = pg_fetch_object($resultID, 0);

            // �Ծ���˼��������ͤ򥻥åȤ��ֵ�
            $rowData['orderNo'] = $result->lngorderno;
    
            $this->objDB->freeResult($resultID);

            return;

        } else {
            // ȯ���ֹ�����
            $orderNo = fncGetSequence("m_Order.lngOrderNo", $this->objDB);

            // ȯ�������ֹ�κ����ͤΥ��󥯥����
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
            // �����������
            $strQuery = $this->makeInsertSelectQuery($table, $data);
    
            // ������μ¹�
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

            // �Ծ���������ȯ���ֹ�򥻥åȤ����ֵ�
            $rowData['orderNo'] = $orderNo;
    
            $this->objDB->freeResult($resultID);
        }
    }


    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	ȯ��ޥ����ؤ���Ͽ��Ԥ�
    *
    *   @param array $rowData �ԤΥǡ���
    *   @param string $orderCode ȯ������
    *   
    *	@return true
    */
    protected function updateMasterOrder(&$rowData) {
        // �ơ��֥������
        $table = 'm_order';

        $orderNo = $rowData['orderNo'];               // ȯ���ֹ�
        $revisionNo = $this->preOrderRevisionNo + 1;  // ��Ͽ�˻��Ѥ���ȯ��Υ�ӥ�����ֹ�

        $previousDetailNo = $rowData['previousDetailNo']; // �����θ��Ѹ��������ֹ�

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
    
            // �����������
            $strQuery = $this->makeInsertSelectQuery($table, $data, $condition, $returning);

            // ������μ¹�
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

            // ������֥����ɤ����
            $result = pg_fetch_object($resultID, 0);

            // �Ծ���˼��������ͤ򥻥åȤ��ֵ�
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
    
            // �����������
            $strQuery = $this->makeInsertSelectQuery($table, $data);
            // ������μ¹�
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);
    
            $this->objDB->freeResult($resultID);

            // �Ծ����ȯ����֥����ɤ򥻥åȤ��ֵ�
            $rowData['orderStatusCode'] = DEF_ORDER_APPLICATE;
    
            return true;
        }


    }



    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	���ʲ����ơ��֥�ؤ���Ͽ��Ԥ� 
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
    *	������٥ơ��֥�ν�����Ԥ� 
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

        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	Ǽ����ɼ���٥ơ��֥�ν�����Ԥ� 
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

        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	ȯ���ܺ٥ơ��֥�ν�����Ԥ� 
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

        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	�������٥ơ��֥�ν�����Ԥ� 
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

        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }


    /**
    * DB�����Ѵؿ�
    *
    *	�����������쥳���ɤ�¸�ߤ��뤫��ǧ����
    *   
    *   @param string $table 
    *   @param array $data   �����̾�򥭡��˻��ġ������������� ($data['�����̾']] = �������);
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
        
        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);
        
        if ($resultNumber < 0) {
            return false;
        } else {
            return true;
        }
    }


    // ���Ѹ����ֹ���б����롢����Υơ��֥�Υ����ȥ����Υꥹ�Ȥ��������
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

        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $sortKey = 1; // �����ȥ�����1�����Ϣ��

        for ($i = 0; $i < $resultNumber; ++$i) {
            $result = pg_fetch_object($resultID, $i);
            $previousDetailNo = $result->lngestimatedetailno;
            $sortKeyList[$previousDetailNo] = $sortKey;
            ++$sortKey;
        }

        $newSortKey = $sortKey; // �����������ȥ�����ɬ�פʻ��˻��Ѥ���

        $this->objDB->freeResult($resultID);

        return $sortKeyList;
    }



    /**
    *	������̤κǽ�Υ쥳���ɤ�����ꤷ���������ͤ��������
    *	
    *   @param string $table            ��������ơ��֥�̾
    *   @param string $needle           �ͤ�������륫���
    *   @param string $search           �������
    *   @param string $sort             'max' or 'min'
    *   
    */
    protected function getFirstRecordValue($table, $needle, $search, $sort = null) {
        $strQuery = "SELECT ". $needle. " FROM ". $table. " " .$search;

        // $sort���꤬������Ϻ��������ϺǾ��ͤ��֤�
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

        // �ǽ�Υ쥳���ɤ��ͤ��ֵѤ���
        if ($resuluNumber > 0) {
            $result = pg_fetch_object($resultID, 0);
            $ret = $result->$needle;
        } else {
            $ret = false;
        }

        $this->objDB->freeResult($resultID);
        return $ret;
    }

    // ���Ѹ����ֹ椫��������ɤ��������
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

        // �ǽ�Υ쥳���ɤ��ͤ��ֵѤ���
        if ($resuluNumber > 0) {
            $result = pg_fetch_object($resultID, 0);
            $ret = $result->strreceivecode;
        } else {
            $ret = false;
        }

        return $ret;
    }

    // ���Ѹ����ֹ椫��ȯ�����ɤ��������
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

        // �ǽ�Υ쥳���ɤ��ͤ��ֵѤ���
        if ($resuluNumber > 0) {
            $result = pg_fetch_object($resultID, 0);
            $ret = $result->strordercode;
        } else {
            $ret = false;
        }

        return $ret;
    }

    // ����о����٤ν����������������ѤΥ쥳���ɤ��ɲä�Ԥ�
    protected function insertDeleteRecord() {
        $this->updateDeleteReceive();
        $this->insertDeleteReceive();
        $this->insertDeleteOrder();
        return;
    }

    // ����ޥ����κ���о����٤μ������ɤ�������
    protected function updateDeleteReceive() {

        $previousRevisionNo = $this->revisionNo - 1;

        $strQuery = "UPDATE m_receive mr";
        $strQuery .= " SET strreceivecode = CASE WHEN strreceivecode ~ '^\*.+\*$' THEN strreceivecode ELSE '*' || strreceivecode || '*' END";
        // �ʲ�����Ͽ���Υ�ӥ�����ֹ椬¸�ߤ�����Ͽ��Υ�ӥ�����ֹ椬¸�ߤ��ʤ������ֹ���������
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
        $strQuery .= " WHERE sub.lngreceiveno = mr.lngreceiveno"; // �����ֹ����

        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    // ����ޥ�������������ѥ쥳���ɤ��ɲä���
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

        // �������٥ơ��֥�˼���ޥ�������
        $strQuery .= " INNER JOIN m_receive mr";
        $strQuery .= " ON mr.lngreceiveno = trd.lngreceiveno";
        $strQuery .= " AND mr.lngrevisionno = trd.lngrevisionno";

        // ������Υ�ӥ����θ�����̤���
        $strQuery .= " LEFT OUTER JOIN t_receivedetail trd2";
        $strQuery .= " ON mr.lngreceiveno = trd2.lngreceiveno";
        $strQuery .= " AND trd2.lngestimaterevisionno = ". $this->revisionNo;

        // ������Υ�ӥ����¸�ߤ��ʤ���Τ������оݤȤ���WHERE��
        $strQuery .= " WHERE";
        $strQuery .= " trd.lngestimateno = ". $this->estimateNo;
        $strQuery .= " AND trd.lngestimaterevisionno = ". $previousRevisionNo;
        $strQuery .= " AND trd2.lngreceiveno is NULL";

        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    // ȯ��ޥ�������������ѥ쥳���ɤ��ɲä���
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

        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }
}
