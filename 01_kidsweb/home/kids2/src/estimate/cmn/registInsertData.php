<?php

require_once ('conf.inc');
require_once ( LIB_FILE );

require_once ( SRC_ROOT . "estimate/cmn/const/workSheetConst.php");
require_once ( SRC_ROOT . "estimate/cmn/estimateInsertData.php");

// ��Ͽ�ѥǡ����������饹
class registInsertData extends estimateInsertData {
    
    public function __construct() {
        parent::__construct();
    }
    

    // ɬ�פʥѥ�᡼���򥯥饹�˥��åȤ���
    public function setRegistParam($regist, $inputUserCode, $objDB) {
        if (is_array($regist)) {

            // �����͡��桼���������ɡ�DB���饹�Υ��å�
            $this->setParam($regist, $inputUserCode, $objDB);

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

    // ���Ѹ�����Ͽ����ɬ�פʥǡ���������������åȤ���
    protected function setEstimateRegistParam() {
        // ��������Ⱦ�����ʥ����ɤμ���
        $productCode = $this->headerData[workSheetConst::PRODUCT_CODE];

		// ���ʥ����ɤ�¸�ߤ�����Ϻ���
		if ($productCode) {

			// �������ʤΥ����ɤ����ʥޥ�����¸�ߤ��뤫��ǧ������Х��������ɤκ����ͤ��������
            $currentRecord = $this->objDB->getCurrentRecordForProductCode($productCode);

			if ($currentRecord !== false) {
                // �����ֹ�����
                $productNo = $currentRecord->lngproductno;
                
                // ����Υ�Х��������ɤ����
                $maxReviceCode = (int)$currentRecord->strrevisecode;
                
				// ��Х��������ɤ�����
				$reviseCode =  $maxReviceCode + 1;
				$reviseCode =  str_pad($reviseCode, 2, 0, STR_PAD_LEFT);
	
			} else {
				// ���顼����
			}
        // ���ʥ����ɤ�¸�ߤ��ʤ��ʤ鿷��
		} else {
            // �����ֹ�����
            $productNo = fncGetSequence("m_product.lngproductno", $this->objDB);

            // ���ʥ����ɤ�ȯ��
            $productCode = str_pad($productNo, 5, 0, STR_PAD_LEFT);
			
			// ��Х��������ɤ�����
			$reviseCode = '00';
        }
        
        // �ѥ�᡼���Υ��å�
        $this->productNo = $productNo;
        $this->productCode = $productCode;
        $this->reviseCode = $reviseCode;

		// ���Ѹ����ֹ�����
		$estimateNo = fncGetSequence("m_Estimate.lngEstimateNo", $this->objDB);
        $this->estimateNo = $estimateNo;

		// ���ʲ���襳���ɤ����
		$goodsPlanCode = fncGetSequence("t_goodsplan.lnggoodsplancode", $this->objDB);
		$this->goodsPlanCode = $goodsPlanCode;

		// ��ӥ����No������
        $this->revisionNo = 0;
        $this->productRevisionNo = 0;
        
        return true;        
    }

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	���Ѹ�����Ͽ��Ԥ�
    *   
    *	@return true
    */
    public function regist() {
        // ��Ͽ����ɬ�פʥǡ���������������åȤ���
        $this->setEstimateRegistParam();

        // ���ʥޥ�������Ͽ����
        $this->registMasterProduct();

        // ���Ѹ����ޥ�������Ͽ����
        $this->registMasterEstimate();

        // ���ʲ����ơ��֥����Ͽ����
        $this->registTableGoodsPlan();

        $estimateDetailNo = 0; // ���Ѹ��������ֹ�
        $receiveDetailNo = 0;  // ���������ֹ�
        $orderDetailNo = 0;    // ȯ�������ֹ�

        $rowDataList = $this->rowDataList;

        $year = date('Y');
        $month = date('m');
        
        // ���ٹԤ���Ͽ
        foreach ($rowDataList as $rowData) {
            // ���Ѹ��������ֹ�Υ��󥯥����
            ++$estimateDetailNo;

            // ���Ѹ������٥ơ��֥����Ͽ����
            $this->registTableEstimateDetail($rowData, $estimateDetailNo);

            $salesOrder = $rowData['salesOrder'];

            // ����ξ��
            if ($salesOrder === DEF_ATTRIBUTE_CLIENT) {
                // ���������ֹ�Υ��󥯥����
                ++$receiveDetailNo;

                // �����ֹ�����
                $receiveNo = fncGetSequence("m_receive.lngReceiveNo", $this->objDB);

                // �������ɤμ���
                if (!isset($receiveCode)) {
                    $receiveCode = 'd'. fncGetDateSequence($year, $month, 'm_receive.strreceivecode', $this->objDB);
                }

                // ����ޥ�����Ͽ����
                $this->registMasterReceive($rowData, $receiveNo, $receiveCode);

                // �������٥ơ��֥���Ͽ����
                $this->registTableReceiveDetail($rowData, $receiveNo, $receiveDetailNo, $estimateDetailNo);

            // ȯ��ξ��
            } else if ($salesOrder === DEF_ATTRIBUTE_SUPPLIER) {
                if ($rowData['areaCode'] !== DEF_AREA_OTHER_COST_ORDER) {
                    // ȯ�������ֹ�Υ��󥯥����
                    ++$orderDetailNo;

                    // ȯ���ֹ�����
                    $orderNo = fncGetSequence("m_Order.lngOrderNo", $this->objDB);

                    // ȯ�����ɤμ���
                    if (!isset($orderCode)) {
                        $orderCode = fncGetDateSequence($year, $month, 'm_order.strordercode', $this->objDB);
                    }

                    // ȯ��ޥ�����Ͽ����
                    $this->registMasterOrder($rowData, $orderNo, $orderCode);

                    // ȯ�����٥ơ��֥���Ͽ����
                    $this->registTableOrderDetail($rowData, $orderNo, $orderDetailNo, $estimateDetailNo);
                }
            }
        }
        
        return true;
    }


    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	���ʥޥ����ؤ���Ͽ��Ԥ�
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
            'lngrevisionno' => $this->productRevisionNo,
            'strrevisecode' => "'". $this->reviseCode. "'"
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
    *	���Ѹ����ޥ����ؤ���Ͽ��Ԥ�
    *   
    *	@return true
    */
    protected function registMasterEstimate() {
        $table = 'm_estimate';

        // ����
        $retailPrice = $this->headerData[workSheetConst::RETAIL_PRICE];
        // ���ѿ�
        $productionQuantity = $this->headerData[workSheetConst::PRODUCTION_QUANTITY];

        $data = array(
            'lngestimateno' => $this->estimateNo,
            'lngrevisionno'=> $this->revisionNo,
            'strproductcode'=> "'". $this->productCode. "'",
            'strrevisecode' => "'". $this->reviseCode. "'",
            'bytdecisionflag' => 'true',
            'lngestimatestatuscode'=> DEF_ESTIMATE_APPROVE,
            'curfixedcost' => $this->calculatedData[workSheetConst::DEPRECIATION_COST],
            'curmembercost' => $this->calculatedData[workSheetConst::MEMBER_COST],
            'curtotalprice' => $this->calculatedData[workSheetConst::PROFIT],
            'curmanufacturingcost' => $this->calculatedData[workSheetConst::MANUFACTURING_COST],
            'cursalesamount' => $this->calculatedData[workSheetConst::PRODUCT_TOTAL_PRICE],
            'curprofit' => $this->calculatedData[workSheetConst::OPERATING_PROFIT],
            'lnginputusercode'=> $this->inputUserCode,
            'bytinvalidflag' => 'false',
            'dtminsertdate' => 'NOW()',
            'lngproductionquantity' => $productionQuantity,
            'lngtempno' => 'NULL',
            'strnote' => 'NULL',
            'lngproductrevisionno' => $this->productRevisionNo,
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
    *	���Ѹ������٥ơ��֥�ؤ���Ͽ��Ԥ�
    *
    *   @param array $rowData �ԤΥǡ���
    *   @param integer $estimateDetailNo ���Ѹ��������ֹ�
    *
    *	@return true
    */
    protected function registTableEstimateDetail($rowData, $estimateDetailNo) {
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
            'curproductrate' => $rowData['percentInputFlag'] === true ? $rowData['percent'] : 'null',
            'lngmonetaryunitcode' => $rowData['monetary'],
            'lngmonetaryratecode' => $rowData['monetary'] == 1 ? DEF_MONETARY_RATE_CODE_NONE : DEF_MONETARY_RATE_CODE_COMPANY_LOCAL,
            'curconversionrate' => $rowData['conversionRate'],
            'lngproductquantity' => $rowData['quantity'],
            'curproductprice' => $rowData['percentInputFlag'] === false ? $rowData['price'] : 'null',
            'cursubtotalprice' => $rowData['subtotal'],
            'strnote' => "'". $rowData['note']. "'",
            'lngsortkey' => $estimateDetailNo,
            'lngsalesdivisioncode' => $salesDivisionCode,
            'lngsalesclasscode' => $salesClassCode
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
    *	����ޥ����ؤ���Ͽ��Ԥ�
    *
    *   @param array $rowData �ԤΥǡ���
    *   @param string $receiveCode ��������
    *   
    *	@return true
    */
    protected function registMasterReceive($rowData, $receiveNo, $receiveCode) {
        // �ơ��֥������ 
        $table = 'm_receive';

        // ��Ͽ�ǡ����κ���
        $data = array(
            'lngreceiveno' => $receiveNo,
            'lngrevisionno' => $this->revisionNo,
            'strreceivecode' => "'". $receiveCode. "'",
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
        $strQuery = $this->makeInsertQuery($table, $data);
        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }


    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	�������٥ơ��֥�ؤ���Ͽ��Ԥ�
    *
    *   @param array $rowData �ԤΥǡ���
    *   @param integer $receiveDetailNo ���������ֹ�
    *   @param integer $estimateDetailNo ���Ѹ��������ֹ�    
    *   
    *	@return true
    */
    protected function registTableReceiveDetail($rowData, $receiveNo, $receiveDetailNo, $estimateDetailNo) {
        // �ơ��֥������ 
        $table = 't_receivedetail';

        $data = array(
            'lngreceiveno' => $receiveNo,
            'lngreceivedetailno' => $receiveDetailNo,
            'lngrevisionno' => $this->revisionNo,
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
            'lngsortkey' => $receiveDetailNo,
            'lngestimateno' => $this->estimateNo,
            'lngestimatedetailno' => $estimateDetailNo,
            'lngestimaterevisionno' => $this->revisionNo
        );

        // �����������
        $strQuery = $this->makeInsertQuery($table, $data);

        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);
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
    protected function registMasterOrder($rowData, $orderNo, $orderCode) {
        // �ơ��֥������
        $table = 'm_order';

        $data = array(
            'lngorderno' => $orderNo,
            'lngrevisionno' => $this->revisionNo,
            'strordercode' => "'". $orderCode. "'",
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
        $strQuery = $this->makeInsertQuery($table, $data);
        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	ȯ�����٥ơ��֥�ؤ���Ͽ��Ԥ�
    *
    *   @param array $rowData �ԤΥǡ���
    *   @param integer $orderDetailNo ȯ�������ֹ�
    *   @param integer $estimateDetailNo ���Ѹ��������ֹ�    
    *   
    *	@return true
    */
    protected function registTableOrderDetail($rowData, $orderNo, $orderDetailNo, $estimateDetailNo) {
        // �ơ��֥������
        $table = 't_orderdetail';

        $data = array(
            'lngorderno' => $orderNo,
            'lngorderdetailno' => $orderDetailNo,
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
            'cursubtotalprice' => $rowData['subtotal'],
            'strnote' => "'". $rowData['note']. "'",
            'strmoldno' => 'NULL',
            'lngsortkey' => $orderDetailNo,
            'lngestimateno' => $this->estimateNo,
            'lngestimatedetailno' => $estimateDetailNo,
            'lngestimaterevisionno' => $this->revisionNo
        );
        // �����������
        $strQuery = $this->makeInsertQuery($table, $data);

        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

    }

    /**
    * DB��Ͽ�Ѵؿ�
    *
    *	���ʲ����ơ��֥�ؤ���Ͽ��Ԥ� 
    *   
    *	@return true
    */
    protected function registTableGoodsPlan() {

        $table = 't_goodsplan';

        $data = array(
            'lnggoodsplancode' => $this->goodsPlanCode,
            'lngrevisionno' => $this->revisionNo,
            'lngproductno' => $this->productNo,
            'strrevisecode' => $this->reviseCode,
            'dtmcreationdate' => 'NOW()',
            'dtmrevisiondate' => 'NULL',
            'lnggoodsplanprogresscode' => DEF_GOODSPLAN_AFOOT,
            'lnginputusercode' => $this->inputUserCode
        );

        // �����������
        $strQuery = $this->makeInsertQuery($table, $data);

        // ������μ¹�
        list($resultID, $resultNumber) = fncQuery($strQuery, $this->objDB);

        $this->objDB->freeResult($resultID);

        return true;
    }
}
