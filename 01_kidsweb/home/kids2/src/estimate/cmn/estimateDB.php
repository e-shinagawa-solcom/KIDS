<?php

require_once ( 'conf.inc' );		// �����ɤ߹���
require_once ( LIB_FILE );			// �饤�֥���ɤ߹���

require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php"); // ��������Ƚ���������ե�����


// ���Ѹ���DB�ǡ����������饹
class estimateDB extends clsDB {
    public function __construct() {
        parent::__construct();
    }

    // Ŭ�ѥ졼�Ȥμ���
    public function getTemporaryRateList() {        
        if (!$this->isOpen()) {
			return false;
		} else {
            $today = date('Y/m/d');
            $thisMonthfirstDay = date('Y/m/1');
            $endDay = date('Y/m/t', strtotime($thisMonthfirstDay . "+6 month"));
            $monetaryRateCode = DEF_MONETARY_RATE_CODE_COMPANY_LOCAL; // ����졼��
    
            $strQuery = "SELECT lngmonetaryunitcode, curconversionrate, dtmapplystartdate, dtmapplyenddate FROM m_monetaryrate";
            $strQuery .= " WHERE TO_DATE('$today', 'YYYY/MM/DD') <= dtmapplyenddate";
            $strQuery .= " AND dtmapplystartdate <= TO_DATE('$endDay', 'YYYY/MM/DD')";
            $strQuery .= " AND lngmonetaryratecode = ". $monetaryRateCode;
    
            $queryResult = fncQuery($strQuery, $this); // [0]:���ID [1]:�����Կ�
    
            for ($i = 0; $i < $queryResult[1]; ++$i) {
                $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                $monetaryUnitCode = $result['lngmonetaryunitcode'];
                $conversionRate[$monetaryUnitCode][] = array(
                    'conversionRate' => $result['curconversionrate'],
                    'startDate' => $result['dtmapplystartdate'],
                    'endDate' => $result['dtmapplyenddate']
                );
            }
            $this->freeResult($queryResult[0]);
            return $conversionRate;
        }
    }

    // �̲�̾�μ���
    public function getMonetaryUnitList() {
        if (!$this->isOpen()) {
			return false;
		} else {
            $strQuery = "SELECT lngmonetaryunitcode, strmonetaryunitname FROM m_monetaryunit";
        }
        $queryResult = fncQuery($strQuery, $this); // [0]:���ID [1]:�����Կ�
    
        for ($i = 0; $i < $queryResult[1]; ++$i) {
            $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
            $monetaryUnitCode = $result['lngmonetaryunitcode'];
            $monetaryUnitList[$monetaryUnitCode] = $result['strmonetaryunitname'];
        }
        $this->freeResult($queryResult[0]);
        return $monetaryUnitList;
    }
    
    // �ܵ��衢������ޥ������ǡ�������
    public function getCustomerCompanyCodeList($areaCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $orderAttributeArray = workSheetConst::ORDER_ATTRIBUTE_FOR_TARGET_AREA; // ��塢����
            foreach ($orderAttributeArray as $key => $val) {
                if($val[$areaCode] === true) {
                    $orderAttribute = $key;
                    break;
                }
            }

            $strQuery = "SELECT a.lngattributecode, b.strcompanydisplaycode FROM m_attributerelation a";
            $strQuery .= " INNER JOIN m_company b";
            $strQuery .= " ON a.lngcompanycode = b.lngcompanycode";
            $strQuery .= " WHERE a.lngattributecode = ". $orderAttribute;

            $queryResult = fncQuery($strQuery, $this); // [0]:���ID [1]:�����Կ�

            if ($queryResult[1]) {
                for ($i = 0; $i < $queryResult[1]; ++$i) {
                    $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                    $attributeCode = $result['lngattributecode'];
                    $customerCompanycode = $result['strcompanydisplaycode'];
    
                    $customerCompanyCodeList[] = $customerCompanycode;
                }
            } else {
                return $customerCompanyCodeList = false;
            }

            $this->freeResult($queryResult[0]);
            return $customerCompanyCodeList;
        }        
    }

    /**
    *	���Ѳ�ǽ��ȯ�����ɤ�ȯ�֤���
    *	
    *   @return string $orderCode      ȯ������
    *   
    */
    public function getOrderCode() {
        if (!$this->isOpen()) {
            return false;
        } else {
            $thisMonth = date('Ym');
            $strQuery = "SELECT * FROM m_order WHERE strordercode LIKE '". $thisMonth. "%' ORDER BY strordercode DESC"; 
            list($resultID, $resultNumber) = fncQuery($strQuery, $this);
            // �����Ǥ������
            if (0 < $resultNumber) {
                $result = pg_fetch_object($resultID, 0);
                $maxOrderCode = $result->strordercode;
                // �����ȯ�����ɤ�1��­��
                $orderCode = (string)((int)$maxOrderCode + 1);
            // �����ȯ�Ԥ��줿ȯ���ֹ������Ǥ��ʤ��ä�����001������֤���
            } else {
                $orderCode = $thisMonth.'001';
            }
        }
        $this->freeResult($queryResult[0]);
        return $orderCode;
    }

    /**
    *	���Ѳ�ǽ�ʼ������ɤ�ȯ�֤���
    *	
    *   @return string $reviceCode      ��������
    *   
    */
    public function getReceiveCode() {
        if (!$this->isOpen()) {
            return false;
        } else {
            $thisMonth = date('Ym');
            $strQuery = "SELECT * FROM m_receive WHERE strreceivecode LIKE 'd". $thisMonth. "%' ORDER BY strreceivecode DESC"; 
            list($resultID, $resultNumber) = fncQuery($strQuery, $this);
            if (0 < $resultNumber) {
                $result = pg_fetch_object($resultID, 0);
                $maxReceiveCode = $result->strreceivecode;
                // ����μ������ɤ�1��­��
                $reviceCode = (int)(str_replace('d', '', $maxReceiveCode)) + 1;
                // ��Ƭ����d��Ĥ���
                $reviceCode = 'd'. $reviceCode;
            // �����ȯ�Ԥ��줿�����ֹ������Ǥ��ʤ��ä�����001������֤���
            } else {
                $reviceCode = 'd'. $thisMonth. '001';
            }
        }
        $this->freeResult($queryResult[0]);
        return $reviceCode;
    }

    /**
    *	������̤���ǻ��ꤷ�������κ����ͤ�ޤ�쥳���ɤ����
    *   (�ơ��֥�ˤ�äƤ�ʣ���Ԥη�̤��֤äƤ��뤳�Ȥ⤢�뤿�ᡢ���ѻ��ˤ�α�դ��뤳��)
    *	
    *   @param string $table            ��������ơ��֥�̾
    *   @param string $needle           �����ͤ�������륫���
    *   @param string $searchColumn     ������Ԥ���
    *   @param scalar $searchValue      �������
    *   
    */
    public function getRecordOfMax($table, $needle, $searchColumn, $searchValue) {
        if (!$this->isOpen()) {
            return false;
        } else {
            if (gettype($searchValue) == 'string') {
                $searchValue = "'". $searchValue. "'";
            }
            $strQuery = "SELECT * FROM ". $table. "WHERE ". $searchColumn. " = ". $searchValue. " AND ". $needle." =";
            $strQuery = " (SELECT MAX(". $needle.") FROM ". $table." WHERE ". $searchColumn. " = ". $searchValue.")";

            list($resultID, $resultNumber) = fncQuery($strQuery, $this);

            if ($resultNumber > 0) {
                $result = pg_fetch_object($resultID, 0);
                $ret = $result['max'];
            } else {
                $ret = false;
            }

            $this->freeResult($queryResult[0]);
            return $ret;
        }
    }

    /**
    *	���ʥ����ɤǸ���������̤���ǡ���Х��������ɤ�������椫���ӥ���󤬺���Υ쥳���ɤ��������
    *	
    *   @param string $productCode      ���ʥ�����
    *   @return array $ret              ���ʥ����ɤ��Ф���ǿ��Υ쥳����
    *   
    */
    public function getCurrentRecordForProductCode($productCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT * FROM m_product a";
            $strQuery .= " INNER JOIN m_group b ON a.lnginchargegroupcode = b.lnggroupcode";
            $strQuery .= " INNER JOIN m_user c ON a.lnginputusercode = c.lngusercode";
            $strQuery .= " WHERE strproductcode = '". $productCode. "'";
            $strQuery .= " ORDER BY a.strrevisecode DESC, a.lngrevisionno DESC";

            list($resultID, $resultNumber) = fncQuery($strQuery, $this);

            if ($resultNumber > 0) {
                $result = pg_fetch_object($resultID, 0);
                $ret = $result;
            } else {
                $ret = false;
            }

            $this->freeResult($resultID);
            return $ret;
        }
    }


    /**
    *	������̤���ǻ��ꤷ�������κ����ͤ����
    *	
    *   @param string $table            ��������ơ��֥�̾
    *   @param string $needle           �����ͤ�������륫���
    *   @param string $searchColumn     ������Ԥ���
    *   @param scalar $searchValue      �������
    *   
    */
    public function getMaxValue($table, $needle, $searchColumn, $searchValue) {
        if (!$this->isOpen()) {
            return false;
        } else {
            if (gettype($searchValue) == 'string') {
                $searchValue = "'". $searchValue. "'";
            }
            $strQuery = "SELECT MAX(CAST(". $needle. " AS INT)) FROM ". $table." WHERE ". $searchColumn. " = ". $searchValue;

            $queryResult = fncQuery($strQuery, $this);

            if ($queryResult[1] > 0) {
                $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                $ret = $result['max'];
            } else {
                $ret = false;
            }

            $this->freeResult($queryResult[0]);
            return $ret;
        }

    }

    /**
    *	DB�Ǥθ�����̤��������
    *	
    *   @param string $table            ��������ơ��֥�̾
    *   @param string $searchColumn     ������Ԥ���
    *   @param scalar $searchValue      �������
    *   @return array $record           ���������쥳����
    *   
    */
    public function getRecordValue($table, $searchColumn, $searchValue) {
        if (!$this->isOpen()) {
            return false;
        } else {
            if (gettype($searchValue) == 'string') {
                $searchValue = "'". $searchValue. "'";
            }
            $strQuery = "SELECT * FROM ". $table." WHERE ". $searchColumn. " = $searchValue";

            $queryResult = fncQuery($strQuery, $this);

            if ($queryResult[1] > 0) {
                for ($i = 0; $i < $queryResult[1]; ++$i) {
                    $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);   
                    $ret[] = $result;
                }
            } else {
                $ret = false;
            }
            $this->freeResult($queryResult[0]);
            return $ret;
        }

    }

    // ���ʬ��ޥ������ǡ����μ���
    public function getDivisionCodeList($areaCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT c.lngsalesdivisioncode, b.lngsalesclasscode FROM m_salesclass a"; 
            $strQuery .= " INNER JOIN m_salesclassdivisonlink b ON a.lngsalesclasscode = b.lngsalesclasscode";
            $strQuery .= " INNER JOIN m_salesdivision c ON b.lngsalesdivisioncode = c.lngsalesdivisioncode";
            $strQuery .= " INNER JOIN m_pulldown d ON c.lngsalesdivisioncode = d.lngitemcode";
            $strQuery .= " WHERE d.lngtargetarea = ". $areaCode;

            $queryResult = fncQuery($strQuery, $this); // [0]:���ID [1]:�����Կ�

            for ($i = 0; $i < $queryResult[1]; ++$i) {
                $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                $divisionCode = $result['lngsalesdivisioncode']; // �������ܥ�����
                $classCode = $result['lngsalesclasscode']; // �������ʥ�����
                // ���ʬ�ॳ���ɤβ�������ʬ�����ɤ��������������
                // ���󥤥᡼����$divisionCodeList = array(divisionCodeA => array(classCode1, classCode2, ...), divisionCodeB => array[(classCode1, classCode2, ...), ...)
                $divisionCodeList[$divisionCode][] = $classCode;          
            }
            $this->freeResult($queryResult[0]);
            return $divisionCodeList;
        }        
    }

    // �������ܥޥ������ǡ����μ���
    public function getSubjectCodeList($areaCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT b.lngstocksubjectcode, b.lngstockclasscode, a.lngstockitemcode FROM m_stockitem a";
            $strQuery .= " INNER JOIN m_stocksubject b ON a.lngstocksubjectcode = b.lngstocksubjectcode";
            $strQuery .= " INNER JOIN m_stockclass c ON b.lngstockclasscode = c.lngstockclasscode";
            $strQuery .= " INNER JOIN m_pulldown d ON b.lngstocksubjectcode = d.lngitemcode";
            $strQuery .= " WHERE d.lngtargetarea = " .$areaCode;

            $queryResult = fncQuery($strQuery, $this); // [0]:���ID [1]:�����Կ�

            for ($i = 0; $i < $queryResult[1]; ++$i) {
                $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                $classCode = $result['lngstockclasscode']; // ������ʬ������
                $subjectCode = $result['lngstocksubjectcode']; // �������ܥ�����
                $itemCode = $result['lngstockitemcode']; // �������ʥ�����
                // �������ܥ����ɤβ��˻������ʥ����ɤ��������������
                    $subjectCodeList[$classCode][$subjectCode][$i] = $itemCode;            
            }
            $this->freeResult($queryResult[0]);
            return $subjectCodeList;
        }
    }

    // ���롼�ץ����ɤˤ�븡��
    public function getGroupRecordForDisplay($groupDisplayCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT * FROM m_group WHERE strgroupdisplaycode = '". $groupDisplayCode. "'";
        }

        list($resultID, $resultNumber) = fncQuery($strQuery, $this);

        if (0 < $resultNumber) {
            $result = pg_fetch_object($resultID, 0);
        } else{
            $result = false;
        }

        $this->freeResult($resultID);
        return $result;
    }

    // �桼���������ɤˤ�븡��
    public function getUserRecordForDisplay ($userDisplayCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT * FROM m_user WHERE struserid = '". $userDisplayCode. "'";
        }
        list($resultID, $resultNumber) = fncQuery($strQuery, $this); // [0]:���ID [1]:�����Կ�
        if (0 < $resultNumber) {
            $result = pg_fetch_object($resultID, 0);
        } else {
            $result = false;
        }
        $this->freeResult($resultID);
        return $result;
    }

    // ������桼�����������Ķ�����˽�°���Ƥ��뤫�����å�����
    public function loginUserAffiliateCheck($loginUserCode, $groupDisplayCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery  = "SELECT a.strgroupdisplaycode FROM m_group a";
            $strQuery .= " INNER JOIN m_grouprelation b ON b.lnggroupcode = a.lnggroupcode";
            $strQuery .= " INNER JOIN m_user c ON c.lngusercode = b.lngusercode";
            $strQuery .= " WHERE a.strgroupdisplaycode = '". $groupDisplayCode. "' and b.lngusercode = ". $loginUserCode;
            $strQuery .= " AND c.bytinvalidflag = false";
            
            $queryResult = fncQuery($strQuery, $this); // [0]:���ID [1]:�����Կ�
            // ������̤������true���֤�
            if (0 < $queryResult[1]) {
                $result =  true;
            } else {
                $result = false;
            }
            $this->freeResult($queryResult[0]);
            return $result;
        }        
    }


    // ���ʥޥ���������ȥ�������ȤαĶ����𤬰��פ��뤫��ǧ����
    public function checkConsistencyGroup($productCode, $groupDisplayCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery  = "SELECT * FROM m_group";
            $strQuery .= " WHERE lnginchargegroupcode = ". $productCode;
            $strQuery .= " AND bytinvalidflag = false";
            
            $queryResult = fncQuery($strQuery, $this); // [0]:���ID [1]:�����Կ�
            // ������̤������true���֤�
            if (0 < $queryResult[1]) {
                $result =  true;
            } else {
                $result = false;
            }
            $this->freeResult($queryResult[0]);
            return $result;
        }        
    }

    // ���ʥ����ɤ�¸�ߤ��ǧ������Х��������ɤ��֤�
    public function getReviseCode($productCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT max(strrevisecode) FROM m_product WHERE strproductcode = '". $productCode. "'";
            $queryResult = fncQuery($strQuery, $this); // [0]:���ID [1]:�����Կ�
            $result = pg_fetch_array($queryResult[0], 0, PGSQL_ASSOC);
            if (!$result) {
                $revise = false;
            } else {
                $revise = $result['max'];
            }
            $this->freeResult($queryResult[0]);
        }
        return $revise;
    }

    // ���롼�ס�����ˤȥ桼�����ν�°�ط�������å�����
    public function checkRelationGroupAndUser($groupCode, $userCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT b.strgroupdisplaycode, c.struserid FROM m_grouprelation a";
            $strQuery .= " INNER JOIN m_group b ON a.lnggroupcode = b.lnggroupcode";
            $strQuery .= " INNER JOIN m_user c ON a.lngusercode = c.lngusercode";
            if (is_array($userCode)) {
                $key = 0;
                foreach ($userCode as $code) {
                    if($key === 0) {
                        $strQuery .= " WHERE (b.strgroupdisplaycode = '". $groupCode ."' and c.struserid ='". $code. "')";
                    } else {
                        $strQuery .= " OR (b.strgroupdisplaycode = '". $groupCode. "' and c.struserid ='". $code. "')";
                    }
                    ++$key;
                }                
            } else {
                $strQuery .= " WHERE b.strgroupdisplaycode = '". $groupCode. "' and c.struserid ='". $userCode. "'";
            }
            $strQuery .= " AND c.bytinvalidflag = false";

            $queryResult = fncQuery($strQuery, $this); // [0]:���ID [1]:�����Կ�

            if ($queryResult[1] == 2) {
                return true;
            } else if ($queryResult[1] == 1) {
                    $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                    $userId = $result['struserid']; // �桼����ID
                    $groupCode = $result['strgroupdisplaycode']; // ɽ���ѥ��롼�ץ�����         
                return false;
            } else {
                return false;
            }
        }
    }

    // �׽���
    public function getProductMasterData($productCode, $reviseCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT * FROM m_product";
            $strQuery .= " WHERE strproductcode = '". $productCode. "'";
            $strQuery .= " AND strrevisecode = $reviseCode";
            $strQuery .= " AND bytinvalidflag = false";
        }
        $queryResult = fncQuery($strQuery, $objDB); // [0]:���ID [1]:�����Կ�
        $result = pg_fetch_array($queryResult[0], 0, PGSQL_ASSOC);
        if (!$result) {
            $revise = false;
        } else {
            $revise = $result['max'];
        }
        $this->freeResult($queryResult[0]);   
    }

    /**
    * ɸ��������ؿ�
    *
    *	ɸ����ǡ���������ؿ�
    *
    *	@return Integer $curStandardRate ɸ����
    *	@access public
    */
    public function getEstimateStandardRate() {
        if (!$this->isOpen()) {
            return false;
        } else {
            list($resultID, $resultNumber) = fncQuery("SELECT To_char( curstandardrate, '990.9999' ) AS curstandardrate FROM m_estimatestandardrate WHERE dtmApplyStartDate < NOW() AND dtmApplyEndDate > NOW()", $this);

            if ($resultNumber < 1) {
                // �⤷�����ɸ���礬���ȤǤ��ʤ����ǿ������դ�ɸ����򻲾�
                list($resultMaxID, $resultMaxNumber) = fncQuery("SELECT To_char( curstandardrate, '990.9999' ) AS curstandardrate FROM m_estimatestandardrate WHERE dtmapplyenddate = (SELECT max(dtmapplyenddate) FROM m_estimatestandardrate);", $this);

                if ($resultMaxNumber < 1) {
                    fncOutputError(1502, DEF_WARNING, "", TRUE, "", $this);
                }
                else {
                    $resultNumber = $resultMaxNumber;
                    $resultID  = $resultMaxID;
                }
            }

            $objResult = $this->fetchObject($resultID, 0);
            $this->freeResult($resultID);

            $standardRate = $objResult->curstandardrate;

            return $standardRate;
        }
    }

    public function getOrder($productCode, $reviseCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT";
            $strQuery = " a.lngorderno AS lngorderno";
            $strQuery = " a.lngrevisionno AS lngrevisionno";
            $strQuery = " a.strordercode AS strordercode";
            $strQuery = " a.strrevisecode AS strrevisecode";
            $strQuery = " a.dtmappropriationdate AS dtmappropriationdate";
            $strQuery = " a.lngcustomercompanycode AS lngcustomercompanycode";
            $strQuery = " a.lnggroupcode AS lnggroupcode";
            $strQuery = " a.lngusercode AS lngusercode";
            $strQuery = " a.lngorderstatuscode AS lngorderstatuscode";
            $strQuery = " a.lngmonetaryunitcode AS lngmonetaryunitcode";
            $strQuery = " a.lngmonetaryratecode AS lngmonetaryratecode";
            $strQuery = " a.curconversionrate AS curconversionrate";
            $strQuery = " a.lngpayconditioncode AS lngpayconditioncode";
            $strQuery = " a.curtotalprice AS curtotalprice";
            $strQuery = " a.lngdeliveryplacecode AS lngdeliveryplacecode";
            $strQuery = " a.dtmexpirationdate AS dtmexpirationdate";
            $strQuery = " a.strnote AS strnote";
            $strQuery = " a.lnginputusercode AS lnginputusercode";
            $strQuery = " a.bytinvalidflag AS bytinvalidflag";
            $strQuery = " a.dtminsertdate AS dtminsertdate";
            $strQuery = " b.lngorderdetailno AS lngorderdetailno";
            $strQuery = " b.strproductcode AS strproductcode";
            $strQuery = " b.lngstocksubjectcode AS lngstocksubjectcode";
            $strQuery = " b.lngstockitemcode AS lngstockitemcode";
            $strQuery = " b.dtmdeliverydate AS dtmdeliverydate";
            $strQuery = " b.lngdeliverymethodcode AS lngdeliverymethodcode";
            $strQuery = " b.lngconversionclasscode AS lngconversionclasscode";
            $strQuery = " b.curproductprice AS curproductprice";
            $strQuery = " b.lngproductquantity AS lngproductquantity";
            $strQuery = " b.lngproductunitcode AS lngproductunitcode";
            $strQuery = " b.lngtaxclasscode AS lngtaxclasscode";
            $strQuery = " b.lngtaxcode AS lngtaxcode";
            $strQuery = " b.curtaxprice AS curtaxprice";
            $strQuery = " b.cursubtotalprice AS cursubtotalprice";
            $strQuery = " b.strnote AS strnote";
            $strQuery = " b.strmoldno AS strmoldno";
            $strQuery = " c.lngestimateno AS lngestimateno";
            $strQuery = " c.lngestimatedetailno AS lngestimatedetailno";
            $strQuery = " c.bytpayofftargetflag AS bytpayofftargetflag";
            $strQuery = " c.bytpercentinputflag AS bytpercentinputflag";
            $strQuery = " c.dblpercent AS dblpercent";
            $strQuery = " c.bytpayofftargetflag AS bytpayofftargetflag";
            $strQuery = " c.bytpercentinputflag AS bytpercentinputflag";
            $strQuery = " c.dblpercent AS dblpercent";
            $strQuery = " c.curproductrate AS curproductrate";
            $strQuery = " c.lngsortkey AS lngsortkey";
            $strQuery = " c.lngsalesdivisioncode AS lngsalesdivisioncode";
            $strQuery = " c.lngsalesclasscode AS lngsalesclasscode";

            $strQuery .= " FROM m_order a";
            $strQuery .= " INNER JOIN t_orderdetail b";
            $strQuery .= " ON a.lngorderno = b.lngorderno";
            $strQuery .= " AND a.lngrevisionno = b.lngrevisionno";
            $strQuery .= " INNER JOIN t_estimatedetail c";
            $strQuery .= " ON b.lngestimateno = c.lngestimateno";
            $strQuery .= " AND b.lngestimatedetailno = c.lngestimatedetailno";
            $strQuery .= " INNER JOIN m_estimate d";
            $strQuery .= " ON c.lngestimateno = d.lngestimateno";
            $strQuery .= " AND c.lngrevisionno = d.lngrevisionno";
            $strQuery .= " INNER JOIN m_product e";
            $strQuery .= " ON d.strproductcode = e.strproductcode";
            $strQuery .= " AND d.strrevisecode = e.strrevisecode";
            $strQuery .= " WHERE e.strproductcode = '". $productCode. "'";
            $strQuery .= " AND e.strrevisecode = '". $reviseCode. "'";
            $strQuery .= " AND e.lngrevisionno IN (SELECT MAX(lngrevisionno) FROM m_product";
            $strQuery .= " WHERE strproductcode = '". $productCode. "'";
            $strQuery .= " AND strrevisecode = '". $reviseCode. "')";
        }

        list ($resultID, $resultNumber) = fncQuery($strQuery, $objDB);

        if ($resultNumber > 0) {
            for ($i = 0; $i < $resultNumber; ++$i) {
                $result = pg_fetch_array($resultID, $i, PGSQL_ASSOC);   
                $order[] = $result;
            }
        } else {
            $order = false;
        }

        $this->freeResult($resultID);
        return $order;
    }

    /**
    *
    *	���ʥ����ɡ���Х��������ɤ�ɳ�դ��ǿ��θ��Ѹ������٤Υǡ������������
    *
    *	@param string $productCode ���ʥ�����
    *	@param string $reviseCode  ��Х���������
    *
    *	@return array $estimateDetail ���Ѹ�������
    *	@access public
    */
    public function getProduct() {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT";
            $strQuery .= " lngproductno";
            $strQuery .= " lngrevisionno";
            $strQuery .= " strproductcode";
            $strQuery .= " strproductname";
            $strQuery .= " strproductenglishname";
            $strQuery .= " lnginchargegroupcode";
            $strQuery .= " lnginchargeusercode";
            $strQuery .= " lnginchargeusercode";
            $strQuery .= " lnginputusercode";
            $strQuery .= " lngcustomercompanycode";
            $strQuery .= " lngcustomergroupcode";
            $strQuery .= " lngcustomerusercode";
            $strQuery .= " strcustomerusername";
            $strQuery .= " lngcartonquantity";
            $strQuery .= " lngproductionquantity";
            $strQuery .= " curretailprice";
            $strQuery .= " bytinvalidflag";
            $strQuery .= " dtminsertdate";
            $strQuery .= " dtmupdatedate";
            $strQuery .= " strrevisecode";

            $strQuery .= " FROM m_product";
            $strQuery .= " WHERE strproductcode = '". $productCode. "'";
            $strQuery .= " AND strrevisecode = '". $reviseCode. "'";
            $strQuery .= " AND lngrevisionno IN (SELECT MAX(lngrevisionno) FROM m_product";
            $strQuery .= " WHERE strproductcode = '". $productCode. "'";
            $strQuery .= " AND strrevisecode = '". $reviseCode. "')";
        }

        list ($resultID, $resultNumber) = fncQuery($strQuery, $objDB);

        if ($resultNumber > 0) {
            for ($i = 0; $i < $resultNumber; ++$i) {
                $result = pg_fetch_array($resultID, $i, PGSQL_ASSOC);   
                $product[] = $result;
            }
        } else {
            $product = false;
        }

        $this->freeResult($resultID);

        return $product;
    }

    /**
    *
    *	���ʥ����ɡ���Х��������ɤ�ɳ�դ��ǿ��θ��Ѹ������٤Υǡ������������
    *
    *	@param string $productCode ���ʥ�����
    *	@param string $reviseCode  ��Х���������
    *
    *	@return array $estimateDetail ���Ѹ�������
    *	@access public
    */
    public function getEstimateDetail($productCode, $reviseCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT";
            $strQuery = " a.lngestimateno AS lngestimateno";
            $strQuery = " a.lngestimatedetailno AS lngestimatedetailno";
            $strQuery = " a.lngrevisionno AS lngrevisionno";
            $strQuery = " a.lngstocksubjectcode AS lngstocksubjectcode";
            $strQuery = " a.lngstockitemcode AS lngstockitemcode";
            $strQuery = " a.lngcustomercompanycode AS lngcustomercompanycode";
            $strQuery = " a.bytpayofftargetflag AS bytpayofftargetflag";
            $strQuery = " a.bytpercentinputflag AS bytpercentinputflag";
            $strQuery = " a.dblpercent AS dblpercent";
            $strQuery = " a.lngmonetaryunitcode AS lngmonetaryunitcode";
            $strQuery = " a.lngmonetaryratecode AS lngmonetaryratecode";
            $strQuery = " a.curconversionrate AS curconversionrate";
            $strQuery = " a.lngproductquantity AS lngproductquantity";
            $strQuery = " a.curproductprice AS curproductprice";
            $strQuery = " a.curproductrate AS curproductrate";
            $strQuery = " a.cursubtotalprice AS cursubtotalprice";
            $strQuery = " a.strnote AS strnote";
            $strQuery = " a.lngsortkey AS lngsortkey";
            $strQuery = " a.lngsalesdivisioncode AS lngsalesdivisioncode";
            $strQuery = " a.lngsalesclasscode AS lngsalesclasscode";

            $strQuery .= " FROM t_estimatedetail a";
            $strQuery .= " INNER JOIN m_estimate b";
            $strQuery .= " ON a.lngestimateno = b.lngestimateno";
            $strQuery .= " AND a.lngrevisionno = b.lngrevisionno";
            $strQuery .= " INNER JOIN m_product c";
            $strQuery .= " ON b.strproductcode = c.strproductcode";
            $strQuery .= " AND b.strrevisecode = c.strrevisecode";
            $strQuery .= " WHERE c.strproductcode = '". $productCode. "'";
            $strQuery .= " AND c.strrevisecode = '". $reviseCode. "'";
            $strQuery .= " AND c.lngrevisionno IN (SELECT MAX(lngrevisionno) FROM m_product";
            $strQuery .= " WHERE strproductcode = '". $productCode. "'";
            $strQuery .= " AND strrevisecode = '". $reviseCode. "')";
        }

        list ($resultID, $resultNumber) = fncQuery($strQuery, $objDB);

        if ($resultNumber > 0) {
            for ($i = 0; $i < $resultNumber; ++$i) {
                $result = pg_fetch_array($resultID, $i, PGSQL_ASSOC);   
                $estimateDetail[] = $result;
            }
        } else {
            $estimateDetail = false;
        }

        $this->freeResult($resultID);

        return $estimateDetail;
    }
}