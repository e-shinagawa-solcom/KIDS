<?php

require_once ('conf.inc');
require_once (LIB_DEBUGFILE);

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


        // �Ԥξ��󤫤�����θ��Ѹ��������ֹ���������
        $detailNoList = [];
        $newDetailNo = $this->getNewDetailNo();
        for($i = 0; $i <= count($rowDataList); $i++) {
            if( is_null($rowDataList[$i]) )
            {
                continue;
            }
            $previousDetailNo = $rowDataList[$i]['previousDetailNo'];

            if ($rowDataList[$i]['previousDetailNo'] != 0) {
                $detailNoList[] = $rowDataList[$i]['previousDetailNo'];
                $rowDataList[$i]['detailRevisionNo'] = (int)$this->getCurrentDetailRevision($rowDataList[$i]['previousDetailNo']);
                // ����ޥ�����ȯ��ޥ��������줫����Ͽ��������ٹԤˤĤ��ơ������θ��Ѹ������ٹ��ֹ���������
                if ($rowDataList[$i]['areaCode'] !== DEF_AREA_OTHER_COST_ORDER) {

                    if($rowDataList[$i]['salesOrder'] === DEF_ATTRIBUTE_CLIENT)
                    {
                        $rowDataList[$i]['receiveStatusCode'] = (int)$this->getReceiveStatus($rowDataList[$i]);
                        if( $rowDataList[$i]['receiveStatusCode'] != DEF_RECEIVE_APPLICATE)
                        {
                            // ������ǤϤʤ��ǡ����ϥ�ӥ���󥢥åפ��ʤ����ᡢ����ӥ������������Ͽ
                            $rowDataList[$i]['detailRevisionNo'] -= 1;
                        }
                    }
                    else
                    {
                        $rowDataList[$i]['orderStatusCode'] = (int)$this->getOrderStatus($rowDataList[$i]);
                        if( $rowDataList[$i]['orderStatusCode'] != DEF_ORDER_APPLICATE)
                        {
                            // ��ȯ��ǤϤʤ��ǡ����ϥ�ӥ���󥢥åפ��ʤ����ᡢ����ӥ������������Ͽ
                            $rowDataList[$i]['detailRevisionNo'] -= 1;
                        }
                    }
                }
            }
            else{
                // ���Ѹ��������ֹ�򿷵����֤��롣
                $rowDataList[$i]['previousDetailNo'] = $newDetailNo;
                $newDetailNo++;
                $rowDataList[$i]['detailRevisionNo'] = -1;
                if($rowDataList[$i]['salesOrder'] === DEF_ATTRIBUTE_CLIENT)
                {
                    $rowDataList[$i]['receiveStatusCode'] = DEF_RECEIVE_APPLICATE;
                }
                else
                {
                    $rowDataList[$i]['orderStatusCode'] = DEF_ORDER_APPLICATE;
                }
            }
        }

        $receiveTable = 't_receivedetail';
        $orderTable = 't_orderdetail';

        // �������٥ơ��֥롢ȯ�����٥ơ��֥�������ֹ�κ����ͤ�����������åȤ���
        $search = "WHERE lngestimateno = ". $this->estimateNo; // ������說����

        $this->maxReceiveDetailNo = $this->getFirstRecordValue($receiveTable, 'lngreceivedetailno', $search, 'max'); // �����ֹ�κ�����
        $this->maxOrderDetailNo = $this->getFirstRecordValue($orderTable, 'lngorderdetailno', $search, 'max');       // ȯ���ֹ�κ�����


        // ���Ѹ����ֹ�ڤӸ��Ѹ�����ӥ�����ֹ��ɳ�դ��������٥ơ��֥롢ȯ�����٥ơ��֥�κ���Υ�ӥ�����ֹ���������
//        $searchRevision = "WHERE lngestimateno = ". $this->estimateNo; // ������說����
//        $searchRevision .= " AND lngestimaterevisionno = ". $rowData['detailRevisionNo'];

//        $this->preReceiveRevisionNo = $this->getFirstRecordValue($receiveTable, 'lngrevisionno', $searchRevision, 'max');
//        $this->preOrderRevisionNo = $this->getFirstRecordValue($orderTable, 'lngrevisionno', $searchRevision, 'max');


        // ���Ѹ����ֹ��ɳ�դ��������ɡ�ȯ�����ɤ�������롣�����Ǥ��ʤ��ä����ϥ������󥹽����ˤ����֤���
        // ����
        if ($getReviseCode = $this->getReceiveCode()) {
            $this->receiveCode = $getReviseCode;
        } else {
            $this->receiveCode = 'd'. fncGetDateSequence(date('Y'), date('m'), 'm_receive.strreceivecode', $this->objDB);
        }
        // ȯ��
        if ($getOrderCode = $this->getOrderCode()) {
            $this->orderCode = $getOrderCode;
        } else {
            $this->orderCode = fncGetDateSequence(date('Y'), date('m'), 'm_order.strordercode', $this->objDB);
        }
                
        // ���ٹԤ���Ͽ
        $recvSortKey = 1;
        $orderSortKey = 1;
        foreach ($rowDataList as $rowData) {
            if( is_null($rowData) )
            {
                continue;
            }
            // ���Ѹ��������ֹ�Υ��󥯥����
            ++$estimateDetailNo;
            // ���Ѹ�������ơ��֥����Ͽ����
            $this->registTableEstimateHistory($estimateDetailNo, $rowData['previousDetailNo'], $rowData['detailRevisionNo']);

            $salesOrder = $rowData['salesOrder'];
            // ����ξ��
            if ($salesOrder === DEF_ATTRIBUTE_CLIENT) {
                // ����������٤Τߡ����Ѹ������٤ȼ������١�����ޥ����򹹿�
                if( $rowData['receiveStatusCode'] == DEF_RECEIVE_APPLICATE)
                {
                    // ���Ѹ������٥ơ��֥����Ͽ����
                    $this->updateTableEstimateDetail($rowData, $estimateDetailNo);
                    
                    // �������٥ơ��֥���Ͽ����
                    $this->updateTableReceiveDetail($rowData, $recvSortKey);
                    $recvSortKey++;

                    // ����ޥ�����Ͽ����
                    $this->updateMasterReceive($rowData);

                    /*
                    if ($rowData['receiveStatusCode'] == DEF_RECEIVE_END
                        || $rowData['receiveStatusCode'] == DEF_RECEIVE_CLOSED) {
                        // Ǽ�ʺѡ��ޤ�������Ѥξ��
                        $this->updateTableSalesDetail($rowData);
                        $this->updateTableSlipDetail($rowData);

                    }
                    */
                }
            // ȯ��ξ��
            } else if ($salesOrder === DEF_ATTRIBUTE_SUPPLIER) {
                // ȯ��Ƿ���ʳ��ξ��
                if ($rowData['areaCode'] !== DEF_AREA_OTHER_COST_ORDER) {

                    // ��ȯ������٤Τߡ����Ѹ������٤�ȯ�����١�ȯ��ޥ����򹹿�
                    if ($rowData['orderStatusCode'] == DEF_ORDER_APPLICATE){
                        // ���Ѹ������٥ơ��֥����Ͽ����
                        $this->updateTableEstimateDetail($rowData, $estimateDetailNo);

                        // ȯ�����٥ơ��֥���Ͽ����
                        $this->updateTableOrderDetail($rowData, $orderSortKey);
                        $orderSortKey++;

                        // ȯ��ޥ�����Ͽ����
                        $this->updateMasterOrder($rowData);
                    }
                }
                else
                {
                    // ���Ѹ������٥ơ��֥����Ͽ����
                    $this->updateTableEstimateDetail($rowData, $estimateDetailNo);
                }
                /*
                if ($rowData['orderStatusCode'] == DEF_ORDER_ORDER) {
                    // ȯ��ξ��
                    $this->updateTablePurchaseOrderDetail($rowData);

                } else if ($rowData['orderStatusCode'] == DEF_ORDER_END
                    || $rowData['orderStatusCode'] == DEF_ORDER_CLOSED) {

                    // Ǽ�ʺѡ��ޤ�������Ѥξ��
                    $this->updateTablePurchaseOrderDetail($rowData);
                    $this->updateTableStockDetail($rowData);
                }
                */
 
            }
        }

        // ����ޥ�����ȯ��ޥ�������ǡ����κ����Ԥ��ʺ�����줿�Ԥ��������
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
    *	���Ѹ�������ơ��֥�ؤ��Խ���Ͽ��Ԥ�
    *
    *   @param array $rowData �ԤΥǡ���
    *   @param integer $rowNo �̤��ι��ֹ�
    *   @param integer $estimateDetailNo ���Ѹ��������ֹ�
    *   @param integer $revisionno ���Ѹ������٤Υ�ӥ�����ֹ�ʿ�����-1����ꤹ���
    *
    *	@return true
    */
    protected function registTableEstimateHistory($rowNo, $estimateDetailNo, $revisionno) {
        // �ơ��֥�̾������
        $table = 'm_estimatehistory';

        // ����ξ��

        // ��Ͽ�ǡ����κ���
        $data = array(
            'lngestimateno' => $this->estimateNo,
            'lngrevisionno' => $this->revisionNo,
            'lngestimaterowno' => $rowNo,
            'lngestimatedetailno' => $estimateDetailNo,
            'lngestimatedetailrevisionno' => $revisionno + 1
        );

        // �����������
        $strQuery = $this->makeInsertQuery($table, $data);
        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

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
//            'lngestimatedetailno' => $estimateDetailNo,
            'lngestimatedetailno' => $rowData['previousDetailNo'],
//            'lngrevisionno' => $this->revisionNo,
            'lngrevisionno' => $rowData['detailRevisionNo'] + 1,
            'lngstocksubjectcode'=> $stockSubjectCode,
            'lngstockitemcode' => $stockItemCode,
            'lngcustomercompanycode' => $this->companyCodeList[$rowData['customerCompany']],
            'dtmdelivery' => $rowData['delivery'] ? "TO_TIMESTAMP('". $rowData['delivery']. "', 'YYYY/MM/DD')" : 'null',
            'bytpayofftargetflag' => $rowData['payoff'] == '��' ? 'true' : 'false',
            'bytpercentinputflag'=> $rowData['percentInputFlag'],
            'lngmonetaryunitcode' => $rowData['monetary'],
            'lngmonetaryratecode' => $rowData['monetary'] == 1 ? DEF_MONETARY_RATE_CODE_NONE : DEF_MONETARY_RATE_CODE_COMPANY_LOCAL,
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
    protected function updateTableReceiveDetail(&$rowData, $sortKey) {
        // �ơ��֥������ 
        $table = 't_receivedetail';

//        $previousRevisionNo = $this->revisionNo - 1;      // 1�����θ��Ѹ����Υ�ӥ�����ֹ�

        $revisionNo = $this->preReceiveRevisionNo + 1;    // ��Ͽ�˻��Ѥ������Υ�ӥ�����ֹ�

        $previousDetailNo = $rowData['previousDetailNo']; // �����θ��Ѹ��������ֹ�ʸ����ѡ�
//        $estimateDetailNo = $rowData['currentDetailNo'];  // ����θ��Ѹ��������ֹ����Ͽ��)
        $estimateDetailNo = $rowData['previousDetailNo'];  // ����θ��Ѹ��������ֹ����Ͽ��)�����ѡ����Ѹ��������ֹ�ϸ��Ѹ�������ޥ����θ��Ѹ������ֹ椬ô��

        if ($rowData["detailRevisionNo"] >= 0) {
            $data = array(
                'lngreceiveno' => 'lngreceiveno',
                'lngreceivedetailno' => 'lngreceivedetailno',
                'lngrevisionno' => $rowData['detailRevisionNo'] + 1,
                'strproductcode' => "'". $this->productCode. "'",
                'strrevisecode' => "'". $this->reviseCode. "'",
                'lngsalesclasscode' => $rowData['classItem'],
                'dtmdeliverydate' => "TO_TIMESTAMP('". $rowData['delivery']. "', 'YYYY/MM/DD')",
                'lngconversionclasscode' => 'lngconversionclasscode',
                'curproductprice' => $rowData['price'],
                'lngproductquantity' => $rowData['quantity'],
                'lngproductunitcode' => 'lngproductunitcode',
                'lngunitquantity' => 'lngunitquantity',
                'cursubtotalprice' => $rowData['subtotal'],
                'strnote' => "'". $rowData['note']. "'",
                'lngsortkey' => $sortKey,
                'lngestimateno' => $this->estimateNo,
                'lngestimatedetailno' => $estimateDetailNo,
                'lngestimaterevisionno' => $rowData['detailRevisionNo'] + 1

            );

            $condition = "WHERE lngestimateno =". $this->estimateNo;
            $condition .= " AND lngestimatedetailno = ". $previousDetailNo;
//            $condition .= " AND lngestimaterevisionno = ". $previousRevisionNo;
            $condition .= " AND lngestimaterevisionno = ". $rowData['detailRevisionNo'];

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
                'lngrevisionno' => $rowData['detailRevisionNo'] + 1,
                'strproductcode' => "'". $this->productCode. "'",
                'strrevisecode' => "'". $this->reviseCode. "'",
                'lngsalesclasscode' => $rowData['classItem'],
                'dtmdeliverydate' => "TO_TIMESTAMP('". $rowData['delivery']. "', 'YYYY/MM/DD')",
                'lngconversionclasscode' => 'NULL',
                'curproductprice' => $rowData['price'],
                'lngproductquantity' => $rowData['quantity'],
                'lngproductunitcode' => 1,
                'lngunitquantity' => 1,
                'cursubtotalprice' => $rowData['subtotal'],
                'strnote' => "'". $rowData['note']. "'",
                'lngsortkey' => $sortKey,
                'lngestimateno' => $this->estimateNo,
                'lngestimatedetailno' => $estimateDetailNo,
                'lngestimaterevisionno' => $rowData['detailRevisionNo'] + 1
            );        
    
            // �����������
            $strQuery = $this->makeInsertSelectQuery($table, $data);
    
            // ������μ¹�
            list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

            // �Ծ���˼����ֹ�򥻥åȤ��ֵ�
            $rowData['receiveNo'] = $receiveNo;
    
            $this->objDB->freeResult($resultID);

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
        if ($rowData["detailRevisionNo"] >= 0) {  // �����Υ�ӥ����Ǹ��Ѹ������ٹ��ֹ椬¸�ߤ�����

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
                'lngmonetaryratecode' => $rowData['monetary'] == 1 ? DEF_MONETARY_RATE_CODE_NONE : DEF_MONETARY_RATE_CODE_COMPANY_LOCAL,
                'curconversionrate' => $rowData['conversionRate'],
                'lnginputusercode' => $this->inputUserCode,
                'bytinvalidflag' => 'bytinvalidflag',
                'dtminsertdate' => 'NOW()',
                'strcustomerreceivecode' => 'strcustomerreceivecode'
            );

            $condition = "WHERE lngreceiveno =". $receiveNo;
            $condition .= " AND lngrevisionno = ". $rowData['detailRevisionNo'];
;

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
                'lngrevisionno' => $rowData['detailRevisionNo'] + 1,
                'strreceivecode' => "'". $this->receiveCode. "'",
                'strrevisecode' => "'". $this->reviseCode. "'",
                'dtmappropriationdate' => 'NOW()',
                'lngcustomercompanycode' => $this->companyCodeList[$rowData['customerCompany']],
                'lnggroupcode' => $this->groupCode,
                'lngusercode' => $this->inchargeUserCode,
                'lngreceivestatuscode' => DEF_RECEIVE_APPLICATE,
                'lngmonetaryunitcode' => $rowData['monetary'],
                'lngmonetaryratecode' => $rowData['monetary'] == 1 ? DEF_MONETARY_RATE_CODE_NONE : DEF_MONETARY_RATE_CODE_COMPANY_LOCAL,
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
    protected function updateTableOrderDetail(&$rowData, $sortKey) {
        // �ơ��֥������
        $table = 't_orderdetail';

        $previousRevisionNo = $this->revisionNo - 1; // 1�����θ��Ѹ����Υ�ӥ�����ֹ�

        $revisionNo = $this->preOrderRevisionNo + 1;     // ��Ͽ�˻��Ѥ���ȯ��Υ�ӥ�����ֹ�

        $previousDetailNo = $rowData['previousDetailNo']; // �����θ��Ѹ��������ֹ�ʸ����ѡ�
//        $estimateDetailNo = $rowData['currentDetailNo'];  // ����θ��Ѹ��������ֹ����Ͽ��)
        $estimateDetailNo = $rowData['previousDetailNo'];  // ����θ��Ѹ��������ֹ����Ͽ��)�����ѡ����Ѹ��������ֹ�ϸ��Ѹ�������ޥ����θ��Ѹ������ֹ椬ô��

//        $sortKey = $orderSortKeyList[$previousDetailNo];

        if ($rowData["detailRevisionNo"] >= 0) {

            $data = array(
                'lngorderno' => 'lngorderno',
                'lngorderdetailno' => 'lngorderdetailno',
                'lngrevisionno' => $rowData['detailRevisionNo'] + 1,
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
                'cursubtotalprice' => $rowData['subtotal'],
                'strnote' => "'". $rowData['note']. "'",
                'strmoldno' => 'strmoldno',
                'lngsortkey' => (int)$sortKey,
                'lngestimateno' => $this->estimateNo,
                'lngestimatedetailno' => $estimateDetailNo,
                'lngestimaterevisionno' => $rowData['detailRevisionNo'] + 1
            );



            $condition = "WHERE lngestimateno =". $this->estimateNo;
            $condition .= " AND lngestimatedetailno = ". $previousDetailNo;
            $condition .= " AND lngestimaterevisionno = ". $rowData['detailRevisionNo'];

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
                'lngrevisionno' => $rowData['detailRevisionNo'] + 1,
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
                'cursubtotalprice' => $rowData['subtotal'],
                'strnote' => "'". $rowData['note']. "'",
                'strmoldno' => 'NULL',
                'lngsortkey' => (int)$sortKey,
                'lngestimateno' => $this->estimateNo,
                'lngestimatedetailno' => $estimateDetailNo,
                'lngestimaterevisionno' => $rowData['detailRevisionNo'] + 1

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

        if ($rowData["detailRevisionNo"] >= 0) {
            
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
                'curconversionrate' => $rowData['conversionRate'],
                'lngpayconditioncode' => 'lngpayconditioncode',
                'lngdeliveryplacecode' => 'lngdeliveryplacecode',
                'lnginputusercode' => $this->inputUserCode,
                'bytinvalidflag' => 'bytinvalidflag',
                'dtminsertdate' => 'NOW()'
            );

            $condition = "WHERE lngorderno =". $orderNo;
            $condition .= " AND lngrevisionno = ". $rowData['detailRevisionNo'];
;

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
                'lngrevisionno' => $rowData['detailRevisionNo'] + 1,
                'strordercode' => "'". $this->orderCode. "'",
                'dtmappropriationdate' => 'NOW()',
                'lngcustomercompanycode' => $this->companyCodeList[$rowData['customerCompany']],
                'lnggroupcode' => $this->groupCode,
                'lngusercode' => $this->inchargeUserCode,
                'lngorderstatuscode' => DEF_ORDER_APPLICATE,
                'lngmonetaryunitcode' => $rowData['monetary'],
                'lngmonetaryratecode' => $rowData['monetary'] == 1 ? DEF_MONETARY_RATE_CODE_NONE : DEF_MONETARY_RATE_CODE_COMPANY_LOCAL,
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
        $preReceiveRevisionNo = $this->preReceiveRevisionNo;
        
        $strQuery = "UPDATE";
        $strQuery .= " t_salesdetail";
        $strQuery .= " SET";
        $strQuery .= " lngreceiverevisionno = " . $preReceiveRevisionNo . " + 1";
        $strQuery .= " WHERE lngreceiveno = ". $receiveNo;
        $strQuery .= " AND lngreceiverevisionno = ". $preReceiveRevisionNo;
        $strQuery .= " AND lngsalesno not in (select lngsalesno from m_sales where lngrevisionno < 0)";

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
        $preReceiveRevisionNo = $this->preReceiveRevisionNo;
        
        $strQuery = "UPDATE";
        $strQuery .= " t_slipdetail";
        $strQuery .= " SET";
        $strQuery .= " lngreceiverevisionno = " . $preReceiveRevisionNo . " + 1";
        $strQuery .= " WHERE lngreceiveno = ". $receiveNo;
        $strQuery .= " AND lngreceiverevisionno = ". $preReceiveRevisionNo;
        $strQuery .= " AND lngslipno not in (select lngslipno from m_slip where lngrevisionno < 0)";

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
        $preOrderRevisionNo = $this->preOrderRevisionNo;



        
        $strQuery = "UPDATE";
        $strQuery .= " t_purchaseorderdetail";
        $strQuery .= " SET";
        $strQuery .= " lngorderrevisionno =" . $preOrderRevisionNo . " + 1";
        $strQuery .= " WHERE lngorderno = ". $orderNo;
        $strQuery .= " AND lngorderrevisionno = ". $preOrderRevisionNo;
        $strQuery .= " AND lngpurchaseorderno not in (select lngpurchaseorderno from m_purchaseorder where lngrevisionno < 0)";
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
        $preOrderRevisionNo = $this->preOrderRevisionNo;
        
        $strQuery = "UPDATE";
        $strQuery .= " t_stockdetail";
        $strQuery .= " SET";
        $strQuery .= " lngorderrevisionno = " . $preOrderRevisionNo . " + 1";
        $strQuery .= " WHERE lngorderno = ". $orderNo;
        $strQuery .= " AND lngorderrevisionno = ". $preOrderRevisionNo;
        $strQuery .= " AND lngstockno not in (select lngstockno from m_stock where lngrevisionno < 0)";

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

    // ���Ѹ����ֹ椫�鿷�������ֹ���������
    protected function getNewDetailNo() {
        $strQuery = "SELECT";
        $strQuery .= " MAX(lngestimatedetailno) + 1 as lngestimatedetailno";
        $strQuery .= " FROM";
        $strQuery .= " t_estimatedetail";
        $strQuery .= " WHERE lngestimateno = ". $this->estimateNo;

        list($resultID, $resuluNumber) = fncQuery($strQuery, $this->objDB);

        // �ǽ�Υ쥳���ɤ��ͤ��ֵѤ���
        if ($resuluNumber > 0) {
            $result = pg_fetch_object($resultID, 0);
            $ret = $result->lngestimatedetailno;
        } else {
            $ret = false;
        }

        return $ret;
    }

    // ���Ѹ������٤κǿ���ӥ�����������롣
    protected function getCurrentDetailRevision($detailNo)
    {
        $strQuery = "SELECT";
        $strQuery .= " MAX(lngrevisionno) as lngrevisionno";
        $strQuery .= " FROM";
        $strQuery .= " t_estimatedetail";
        $strQuery .= " WHERE lngestimateno = ". $this->estimateNo;
        $strQuery .= " AND lngestimatedetailno = ". $detailNo;

        list($resultID, $resuluNumber) = fncQuery($strQuery, $this->objDB);

        // �ǽ�Υ쥳���ɤ��ͤ��ֵѤ���
        if ($resuluNumber > 0) {
            $result = pg_fetch_object($resultID, 0);
            $ret = $result->lngrevisionno;
        } else {
            $ret = false;
        }
        
        return $ret;
    }
    

    // �����ơ���������
    protected function getReceiveStatus($rowData){
        $strQuery =  "SELECT";
        $strQuery .= " mr.lngreceivestatuscode";
        $strQuery .= " FROM";
        $strQuery .= " m_receive mr";
        $strQuery .= " INNER JOIN t_receivedetail tr";
        $strQuery .= " ON tr.lngreceiveno = mr.lngreceiveno";
        $strQuery .= " AND tr.lngrevisionno = mr.lngrevisionno";
        $strQuery .= " WHERE tr.lngestimateno = ". $this->estimateNo;
        $strQuery .= " AND tr.lngestimatedetailno = ". $rowData["previousDetailNo"];
        $strQuery .= " AND tr.lngestimaterevisionno = ". $rowData["detailRevisionNo"];
        list($resultID, $resuluNumber) = fncQuery($strQuery, $this->objDB);

        // �ǽ�Υ쥳���ɤ��ͤ��ֵѤ���
        if ($resuluNumber > 0) {
            $result = pg_fetch_object($resultID, 0);
            $ret = $result->lngreceivestatuscode;
        } else {
            $ret = false;
        }
        return $ret;
    }
    
    // ȯ���ơ���������
    protected function getOrderStatus($rowData){
        $strQuery = "SELECT";
        $strQuery .= " mo.lngorderstatuscode";
        $strQuery .= " FROM";
        $strQuery .= " m_order mo";
        $strQuery .= " INNER JOIN t_orderdetail tod";
        $strQuery .= " ON tod.lngorderno = mo.lngorderno";
        $strQuery .= " AND tod.lngrevisionno = mo.lngrevisionno";
        $strQuery .= " WHERE tod.lngestimateno = ". $this->estimateNo;
        $strQuery .= " AND tod.lngestimatedetailno = ". $rowData["previousDetailNo"];
        $strQuery .= " AND tod.lngestimaterevisionno = ". $rowData["detailRevisionNo"];

        list($resultID, $resuluNumber) = fncQuery($strQuery, $this->objDB);

        // �ǽ�Υ쥳���ɤ��ͤ��ֵѤ���
        if ($resuluNumber > 0) {
            $result = pg_fetch_object($resultID, 0);
            $ret = $result->lngorderstatuscode;
        } else {
            $ret = false;
        }
        return $ret;
    }


    // ����о����٤ν����������������ѤΥ쥳���ɤ��ɲä�Ԥ�
    protected function insertDeleteRecord() {
//        $this->updateDeleteReceive(); //�������ɹ����ϸ��Ѹ���������ˡ�
        $this->insertDeleteReceive();
        $this->insertDeleteOrder();
        return;
    }

    // ����ޥ����κ���о����٤μ������ɤ�������
    protected function updateDeleteReceive() {

        $previousRevisionNo = $this->revisionNo - 1;

        $strQuery = "UPDATE m_receive mr";
        $strQuery .= " SET strreceivecode = CASE WHEN strreceivecode ~ '^\*.+\*$' THEN strreceivecode ELSE '*' || strreceivecode || '*' END";
        // �ʲ�����Ͽ��Υ�ӥ�����ֹ椬¸�ߤ��ʤ������ֹ���������
        $strQuery .= " FROM";
        $strQuery .= " (";
        $strQuery .= "SELECT mr2.lngreceiveno";
        $strQuery .= " FROM t_receivedetail trd";
        $strQuery .= " INNER JOIN m_receive mr2";
        $strQuery .= " ON mr2.lngreceiveno = trd.lngreceiveno";
        $strQuery .= " AND mr2.lngrevisionno = trd.lngrevisionno";
        $strQuery .= " WHERE trd.lngestimateno = ". $this->estimateNo;
        $strQuery .= " AND (trd.lngestimatedetailno, trd.lngestimaterevisionno) IN(";
        $strQuery .= " select mh1.lngestimatedetailno, mh1.lngestimatedetailrevisionno";
        $strQuery .= " from m_estimatehistory mh1";
        $strQuery .= " left outer join m_estimatehistory mh2";
        $strQuery .= "     on mh2.lngestimateno = mh1.lngestimateno";
        $strQuery .= " and mh2.lngrevisionno = mh1.lngrevisionno +1";
        $strQuery .= " and mh2.lngestimatedetailno = mh1.lngestimatedetailno";
        $strQuery .= " and mh2.lngestimatedetailrevisionno = mh1.lngestimatedetailrevisionno +1";
        $strQuery .= " where mh1.lngestimateno = ". $this->estimateNo;
        $strQuery .= " and mh1.lngrevisionno=". $previousRevisionNo;
        $strQuery .= " and mh2.lngestimateno is null";
        $strQuery .= " )";
        $strQuery .= ") sub";
        $strQuery .= " WHERE sub.lngreceiveno = mr.lngreceiveno"; // �����ֹ����
//fncDebug("kids2.log", $strQuery, __FILE__, __LINE__, "a");

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
        $strQuery .= " mr.strreceivecode,";
        $strQuery .= " ". $this->inputUserCode. ",";
        $strQuery .= " false,";
        $strQuery .= " now(),";
        $strQuery .= " mr.strrevisecode";

        $strQuery .= " FROM t_receivedetail trd";

        // �������٥ơ��֥�˼���ޥ�������
        $strQuery .= " INNER JOIN m_receive mr";
        $strQuery .= " ON mr.lngreceiveno = trd.lngreceiveno";
        $strQuery .= " AND mr.lngrevisionno = trd.lngrevisionno";

        // ������Υ�ӥ����¸�ߤ��ʤ���Τ������оݤȤ���WHERE��
        $strQuery .= " WHERE trd.lngestimateno = ". $this->estimateNo;
        $strQuery .= " AND (trd.lngestimatedetailno, trd.lngestimaterevisionno) IN(";
        $strQuery .= " select mh1.lngestimatedetailno, mh1.lngestimatedetailrevisionno";
        $strQuery .= " from m_estimatehistory mh1";
        $strQuery .= " left outer join m_estimatehistory mh2";
        $strQuery .= "     on mh2.lngestimateno = mh1.lngestimateno";
        $strQuery .= " and mh2.lngrevisionno = mh1.lngrevisionno +1";
        $strQuery .= " and mh2.lngestimatedetailno = mh1.lngestimatedetailno";
//        $strQuery .= " and mh2.lngestimatedetailrevisionno = mh1.lngestimatedetailrevisionno +1";
        $strQuery .= " where mh1.lngestimateno = ". $this->estimateNo;
        $strQuery .= " and mh1.lngrevisionno=". $previousRevisionNo;
        $strQuery .= " and mh2.lngestimateno is null";
        $strQuery .= " )";
//fncDebug("kids2.log", $strQuery, __FILE__, __LINE__, "a");

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
        $strQuery .= " WHERE tod.lngestimateno = ". $this->estimateNo;
        $strQuery .= " AND (tod.lngestimatedetailno, tod.lngestimaterevisionno) IN(";
        $strQuery .= " select mh1.lngestimatedetailno, mh1.lngestimatedetailrevisionno";
        $strQuery .= " from m_estimatehistory mh1";
        $strQuery .= " left outer join m_estimatehistory mh2";
        $strQuery .= "     on mh2.lngestimateno = mh1.lngestimateno";
        $strQuery .= " and mh2.lngrevisionno = mh1.lngrevisionno +1";
        $strQuery .= " and mh2.lngestimatedetailno = mh1.lngestimatedetailno";
//        $strQuery .= " and mh2.lngestimatedetailrevisionno = mh1.lngestimatedetailrevisionno +1";
        $strQuery .= " where mh1.lngestimateno = ". $this->estimateNo;
        $strQuery .= " and mh1.lngrevisionno=". $previousRevisionNo;
        $strQuery .= " and mh2.lngestimateno is null";
        $strQuery .= " )";
//fncDebug("kids2.log", $strQuery, __FILE__, __LINE__, "a");

        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }
}
