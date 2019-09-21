<?php

require_once ( 'conf.inc' );		// �����ɤ߹���
require_once ( LIB_FILE );			// �饤�֥���ɤ߹���

require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php"); // ��������Ƚ���������ե�����


// ���Ѹ���DB�ǡ����������饹
class estimateDB extends clsDB {
    public function __construct() {
        parent::__construct();
    }

    // Ŭ�ѥ졼�Ȥμ���(����졼��)
    public function getTemporaryRateList() {        
        if (!$this->isOpen()) {
			return false;
		} else {
            $today = date('Y/m/d');
            $thisMonthfirstDay = date('Y/m/1');
            $endDay = date('Y/m/t', strtotime($thisMonthfirstDay . "+6 month"));
            $monetaryRateCode = DEF_MONETARY_RATE_CODE_COMPANY_LOCAL; // ����졼��
    
            $strQuery = "SELECT";
            $strQuery .= " lngmonetaryunitcode,";
            $strQuery .= " curconversionrate,";
            $strQuery .= " dtmapplystartdate,"; 
            $strQuery .= " dtmapplyenddate";
            $strQuery .= " FROM m_monetaryrate";
    
            $queryResult = fncQuery($strQuery, $this); // [0]:���ID [1]:�����Կ�
    
            for ($i = 0; $i < $queryResult[1]; ++$i) {
                $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                $monetaryUnitCode = $result['lngmonetaryunitcode'];
                $conversionRate[$monetaryUnitCode][] = array(
                    'monetaryRateCode' => $monetaryRateCode,
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

            $strQuery = "SELECT";
            $strQuery .= " b.lngcompanycode,";
            $strQuery .= " b.strcompanydisplaycode,";
            $strQuery .= " b.strcompanydisplayname,";
            $strQuery .= " b.strshortname";
            $strQuery .= " FROM m_attributerelation a";
            $strQuery .= " INNER JOIN m_company b";
            $strQuery .= " ON a.lngcompanycode = b.lngcompanycode";
            $strQuery .= " INNER JOIN m_attribute c";
            $strQuery .= " ON a.lngattributecode = c.lngattributecode";
            $strQuery .= " WHERE a.lngattributecode = ". $orderAttribute;
            $strQuery .= " ORDER BY b.strcompanydisplaycode";

            $queryResult = fncQuery($strQuery, $this); // [0]:���ID [1]:�����Կ�

            if ($queryResult[1]) {
                for ($i = 0; $i < $queryResult[1]; ++$i) {
                    $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                    $lngcompanycode = $result['lngcompanycode'];
                    $customerCompanycode = $result['strcompanydisplaycode'];
    
                    $customerCompanyCodeList[$customerCompanycode] = array(
                        'displayName' => $result['strcompanydisplayname'],
                        'shortName' => $result['strshortname']
                    );
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
    public function getDivisionCodeList($areaCode = null) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT c.lngsalesdivisioncode, b.lngsalesclasscode, b.lngtargetarea FROM m_salesclass a"; 
            $strQuery .= " INNER JOIN m_salesclassdivisonlink b ON a.lngsalesclasscode = b.lngsalesclasscode";
            $strQuery .= " INNER JOIN m_salesdivision c ON b.lngsalesdivisioncode = c.lngsalesdivisioncode";
            if (isset($areaCode)) {
                $strQuery .= " WHERE b.lngtargetarea = ". $areaCode;
            }
            
            $queryResult = fncQuery($strQuery, $this); // [0]:���ID [1]:�����Կ�

            for ($i = 0; $i < $queryResult[1]; ++$i) {
                $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                $divisionCode = $result['lngsalesdivisioncode']; // �������ܥ�����
                $classCode = $result['lngsalesclasscode']; // �������ʥ�����
                if (!isset($areaCode)) {
                    // �оݥ��ꥢ�λ��꤬�ʤ����ϥ��ꥢ�������̤��������������
                    $targetArea = $result['lngtargetarea'];
                    $divisionCodeList[$targetArea][$divisionCode][$classCode] = true;
                } else {
                    // ���ʬ�ॳ���ɤβ�������ʬ�����ɤ��������������
                    // ���󥤥᡼����$divisionCodeList = array(divisionCodeA => array(classCode1, classCode2, ...), divisionCodeB => array[(classCode1, classCode2, ...), ...)
                    $divisionCodeList[$divisionCode][$classCode] = true;
                }
            }
            $this->freeResult($queryResult[0]);
            return $divisionCodeList;
        }        
    }

    // �������ܥޥ������ǡ����μ���
    public function getSubjectCodeList($areaCode = null) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT b.lngstocksubjectcode, b.lngstockclasscode, a.lngstockitemcode, a.lngtargetarea FROM m_stockitem a";
            $strQuery .= " INNER JOIN m_stocksubject b ON a.lngstocksubjectcode = b.lngstocksubjectcode";
            $strQuery .= " INNER JOIN m_stockclass c ON b.lngstockclasscode = c.lngstockclasscode";
            if (isset($areaCode)) {
                $strQuery .= " WHERE a.lngtargetarea = " .$areaCode;
            }            

            $queryResult = fncQuery($strQuery, $this); // [0]:���ID [1]:�����Կ�

            for ($i = 0; $i < $queryResult[1]; ++$i) {
                $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                $classCode = $result['lngstockclasscode']; // ������ʬ������
                $subjectCode = $result['lngstocksubjectcode']; // �������ܥ�����
                $itemCode = $result['lngstockitemcode']; // �������ʥ�����
                if (!isset($areaCode)) {
                    // �оݥ��ꥢ�λ��꤬�ʤ����ϥ��ꥢ�������̤��������������
                    $targetArea = $result['lngtargetarea'];
                    $subjectCodeList[$targetArea][$classCode][$subjectCode][$itemCode] = true; 
                } else {
                    // �������ܥ����ɤβ��˻������ʥ����ɤ��������������
                    $subjectCodeList[$classCode][$subjectCode][$itemCode] = true; 
                }
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

    // �桼���������ɤ������Ķ�����˽�°���Ƥ��뤫�����å�����
    public function userCodeAffiliateCheck($userCode, $groupDisplayCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery  = "SELECT a.strgroupdisplaycode FROM m_group a";
            $strQuery .= " INNER JOIN m_grouprelation b ON b.lnggroupcode = a.lnggroupcode";
            $strQuery .= " INNER JOIN m_user c ON c.lngusercode = b.lngusercode";
            $strQuery .= " WHERE a.strgroupdisplaycode = '". $groupDisplayCode. "' and b.lngusercode = ". $userCode;
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

    // �桼����ɽ�������ɤ������Ķ�����˽�°���Ƥ��뤫�����å�����
    public function userDisplayCodeAffiliateCheck($userDisplayCode, $groupDisplayCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery  = "SELECT a.strgroupdisplaycode FROM m_group a";
            $strQuery .= " INNER JOIN m_grouprelation b ON b.lnggroupcode = a.lnggroupcode";
            $strQuery .= " INNER JOIN m_user c ON c.lngusercode = b.lngusercode";
            $strQuery .= " WHERE a.strgroupdisplaycode = '". $groupDisplayCode. "' and c.struserdisplaycode = '". $userDisplayCode."'";
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
            list($resultID, $resultNumber) = fncQuery("SELECT curstandardrate FROM m_estimatestandardrate WHERE dtmApplyStartDate <= NOW() AND dtmApplyEndDate >= NOW()", $this);

            if ($resultNumber < 1) {
                // �⤷�����ɸ���礬���ȤǤ��ʤ����ǿ������դ�ɸ����򻲾�
                list($resultMaxID, $resultMaxNumber) = fncQuery("SELECT curstandardrate FROM m_estimatestandardrate WHERE dtmapplyenddate = (SELECT max(dtmapplyenddate) FROM m_estimatestandardrate);", $this);

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

    /**
    *
    *	���ʥ����ɡ���Х��������ɡ���ӥ�����ֹ��ɳ�դ��ǿ��μ������٤��������
    *
    *	@param string $productCode ���ʥ�����
    *	@param string $reviseCode  ��Х���������
    *
    *	@return array $estimateDetail ���Ѹ�������
    *	@access public
    */
    public function getReceiveDetail($productCode, $reviseCode, $revisionNo = null) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT";
            // ���Ѹ������٤ξ������
            $strQuery .= " c.lngestimateno AS lngestimateno,";
            $strQuery .= " c.lngestimatedetailno AS lngestimatedetailno,";
            $strQuery .= " c.lngrevisionno AS lngrevisionno,";
            $strQuery .= " c.lngstocksubjectcode AS lngstocksubjectcode,";
            $strQuery .= " c.lngstockitemcode AS lngstockitemcode,";
            $strQuery .= " c.lngcustomercompanycode AS lngcustomercompanycode,";
            $strQuery .= " to_char(c.dtmdelivery, 'YYYY/MM/DD') AS dtmdelivery,";
            $strQuery .= " c.bytpayofftargetflag AS bytpayofftargetflag,";
            $strQuery .= " c.bytpercentinputflag AS bytpercentinputflag,";
            $strQuery .= " c.dblpercent AS dblpercent,";
            $strQuery .= " c.lngmonetaryunitcode AS lngmonetaryunitcode,";
            $strQuery .= " c.lngmonetaryratecode AS lngmonetaryratecode,";
            $strQuery .= " c.curconversionrate AS curconversionrate,";
            $strQuery .= " c.lngproductquantity AS lngproductquantity,";
            $strQuery .= " c.curproductprice AS curproductprice,";
            $strQuery .= " c.curproductrate AS curproductrate,";
            $strQuery .= " c.cursubtotalprice AS cursubtotalprice,";
            $strQuery .= " c.strnote AS strnote,";
            $strQuery .= " c.lngsortkey AS lngsortkey,";
            $strQuery .= " c.lngsalesdivisioncode AS lngsalesdivisioncode,";
            $strQuery .= " c.lngsalesclasscode AS lngsalesclasscode,";
            // ������֤ξ������
            $strQuery .= " a.lngreceivestatuscode AS lngreceivestatuscode";

            $strQuery .= " FROM m_receive a";
            $strQuery .= " INNER JOIN t_receivedetail b";
            $strQuery .= " ON a.lngreceiveno = b.lngreceiveno";
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
            $strQuery .= " AND d.lngrevisionno";
            if ($revisionNo) {
                // ��ӥ�����ֹ椬���ꤵ��Ƥ�����
                $strQuery .= " = ". $revisionNo;
            } else { 
                $strQuery .= " IN (SELECT MAX(lngrevisionno) FROM m_estimate";
                $strQuery .= " WHERE strproductcode = '". $productCode. "'";
                $strQuery .= " AND strrevisecode = '". $reviseCode. "')";
            }
        }

        list ($resultID, $resultNumber) = fncQuery($strQuery, $this);

        if ($resultNumber > 0) {
            for ($i = 0; $i < $resultNumber; ++$i) {
                $result = pg_fetch_object($resultID, $i);   
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
    *	���ʥ����ɡ���Х��������ɡ���ӥ�����ֹ��ɳ�դ��ǿ���ȯ�����٤��������
    *
    *	@param string $productCode ���ʥ�����
    *	@param string $reviseCode  ��Х���������
    *
    *	@return array $estimateDetail ���Ѹ�������
    *	@access public
    */
    public function getOrderDetail($productCode, $reviseCode, $revisionNo = null) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT";
            // ���Ѹ������٤ξ������
            $strQuery .= " c.lngestimateno AS lngestimateno,";
            $strQuery .= " c.lngestimatedetailno AS lngestimatedetailno,";
            $strQuery .= " c.lngrevisionno AS lngrevisionno,";
            $strQuery .= " c.lngstocksubjectcode AS lngstocksubjectcode,";
            $strQuery .= " c.lngstockitemcode AS lngstockitemcode,";
            $strQuery .= " c.lngcustomercompanycode AS lngcustomercompanycode,";
            $strQuery .= " to_char(c.dtmdelivery, 'YYYY/MM/DD') AS dtmdelivery,";
            $strQuery .= " c.bytpayofftargetflag AS bytpayofftargetflag,";
            $strQuery .= " c.bytpercentinputflag AS bytpercentinputflag,";
            $strQuery .= " c.dblpercent AS dblpercent,";
            $strQuery .= " c.lngmonetaryunitcode AS lngmonetaryunitcode,";
            $strQuery .= " c.lngmonetaryratecode AS lngmonetaryratecode,";
            $strQuery .= " c.curconversionrate AS curconversionrate,";
            $strQuery .= " c.lngproductquantity AS lngproductquantity,";
            $strQuery .= " c.curproductprice AS curproductprice,";
            $strQuery .= " c.curproductrate AS curproductrate,";
            $strQuery .= " c.cursubtotalprice AS cursubtotalprice,";
            $strQuery .= " c.strnote AS strnote,";
            $strQuery .= " c.lngsortkey AS lngsortkey,";
            $strQuery .= " c.lngsalesdivisioncode AS lngsalesdivisioncode,";
            $strQuery .= " c.lngsalesclasscode AS lngsalesclasscode,";
            // ȯ����֤ξ������
            $strQuery .= " a.lngorderstatuscode AS lngorderstatuscode";

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
            $strQuery .= " AND d.lngrevisionno";
            if ($revisionNo) {
                // ��ӥ�����ֹ椬���ꤵ��Ƥ�����
                $strQuery .= " = ". $revisionNo;
            } else { 
                $strQuery .= " IN (SELECT MAX(lngrevisionno) FROM m_estimate";
                $strQuery .= " WHERE strproductcode = '". $productCode. "'";
                $strQuery .= " AND strrevisecode = '". $reviseCode. "')";
            }
        }

        list ($resultID, $resultNumber) = fncQuery($strQuery, $this);

        if ($resultNumber > 0) {
            for ($i = 0; $i < $resultNumber; ++$i) {
                $result = pg_fetch_object($resultID, $i);   
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
    *	���ʥޥ����������ʥ����ɡ���Х��������ɡ���ӥ�����ֹ��ɳ�դ��ǿ������ʤΥǡ������������
    *
    *	@param string $productCode ���ʥ�����
    *	@param string $reviseCode  ��Х���������
    *
    *	@return array $estimateDetail ���Ѹ�������
    *	@access public
    */
    public function getProduct($productCode, $reviseCode, $revisionNo = null) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT";
            $strQuery .= " lngproductno,";
            $strQuery .= " lngrevisionno,";
            $strQuery .= " strproductcode,";
            $strQuery .= " strproductname,";
            $strQuery .= " strproductenglishname,";
            $strQuery .= " lnginchargegroupcode,";
            $strQuery .= " lnginchargeusercode,";
            $strQuery .= " lngdevelopusercode,";
            $strQuery .= " lnginputusercode,";
            $strQuery .= " lngcustomercompanycode,";
            $strQuery .= " lngcustomergroupcode,";
            $strQuery .= " lngcustomerusercode,";
            $strQuery .= " strcustomerusername,";
            $strQuery .= " lngcartonquantity,";
            $strQuery .= " lngproductionquantity,";
            $strQuery .= " curretailprice,";
            $strQuery .= " bytinvalidflag,";
            $strQuery .= " dtminsertdate,";
            $strQuery .= " dtmupdatedate,";
            $strQuery .= " strrevisecode";

            $strQuery .= " FROM m_product";
            $strQuery .= " WHERE strproductcode = '". $productCode. "'";
            $strQuery .= " AND strrevisecode = '". $reviseCode. "'";
            $strQuery .= " AND lngrevisionno";
            if ($revisionNo) {
                // ��ӥ�����ֹ椬���ꤵ��Ƥ�����
                $strQuery .= " = ". $revisionNo;
            } else {
                // ��ӥ�����ֹ椬���ꤵ��Ƥ��ʤ����Ϻǿ��Υ쥳���ɤ��������
                $strQuery .= " IN (SELECT MAX(lngrevisionno) FROM m_product";
                $strQuery .= " WHERE strproductcode = '". $productCode. "'";
                $strQuery .= " AND strrevisecode = '". $reviseCode. "')";
            }
        }

        list ($resultID, $resultNumber) = fncQuery($strQuery, $this);

        if ($resultNumber > 0) {
            $result = pg_fetch_object($resultID, 0);   
        } else {
            $result = false;
        }

        $this->freeResult($resultID);

        return $result;
    }

    /**
    *
    *	���ʥ����ɡ���Х��������ɡ���ӥ�����ֹ��ɳ�դ����Ѹ������٤Υǡ������������
    *
    *	@param string $productCode ���ʥ�����
    *	@param string $reviseCode  ��Х���������
    *
    *	@return array $estimateDetail ���Ѹ�������
    *	@access public
    */
    public function getEstimateDetail($productCode, $reviseCode, $revisionNo = null) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT";
            $strQuery .= " a.lngestimateno AS lngestimateno,";
            $strQuery .= " a.lngestimatedetailno AS lngestimatedetailno,";
            $strQuery .= " a.lngrevisionno AS lngrevisionno,";
            $strQuery .= " a.lngstocksubjectcode AS lngstocksubjectcode,";
            $strQuery .= " a.lngstockitemcode AS lngstockitemcode,";
            $strQuery .= " a.lngcustomercompanycode AS lngcustomercompanycode,";
            $strQuery .= " to_char(a.dtmdelivery, 'YYYY/MM/DD') AS dtmdelivery,";
            $strQuery .= " a.bytpayofftargetflag AS bytpayofftargetflag,";
            $strQuery .= " a.bytpercentinputflag AS bytpercentinputflag,";
            $strQuery .= " a.curproductrate AS curproductrate,";
            $strQuery .= " a.lngmonetaryunitcode AS lngmonetaryunitcode,";
            $strQuery .= " a.lngmonetaryratecode AS lngmonetaryratecode,";
            $strQuery .= " a.curconversionrate AS curconversionrate,";
            $strQuery .= " a.lngproductquantity AS lngproductquantity,";
            $strQuery .= " a.curproductprice AS curproductprice,";
            $strQuery .= " a.curproductrate AS curproductrate,";
            $strQuery .= " a.cursubtotalprice AS cursubtotalprice,";
            $strQuery .= " a.strnote AS strnote,";
            $strQuery .= " a.lngsortkey AS lngsortkey,";
            $strQuery .= " a.lngsalesdivisioncode AS lngsalesdivisioncode,";
            $strQuery .= " a.lngsalesclasscode AS lngsalesclasscode,";
            $strQuery .= " b.lngproductrevisionno AS lngproductrevisionno,";
            $strQuery .= " e.lngorderno AS lngorderno,";
            $strQuery .= " e.lngorderstatuscode AS lngorderstatuscode,";
            $strQuery .= " g.lngreceiveno AS lngreceiveno,";
            $strQuery .= " g.lngreceivestatuscode AS lngreceivestatuscode";

            $strQuery .= " FROM t_estimatedetail a";
            $strQuery .= " INNER JOIN m_estimate b";
            $strQuery .= " ON a.lngestimateno = b.lngestimateno";
            $strQuery .= " AND a.lngrevisionno = b.lngrevisionno";
            $strQuery .= " INNER JOIN m_product c";
            $strQuery .= " ON b.strproductcode = c.strproductcode";
            $strQuery .= " AND b.strrevisecode = c.strrevisecode";
            $strQuery .= " AND b.lngproductrevisionno = c.lngrevisionno";

            // ȯ�����η��(t_orderdetail, m_order)
            $strQuery .= " LEFT OUTER JOIN t_orderdetail d";
            $strQuery .= " ON a.lngestimateno = d.lngestimateno";
            $strQuery .= " AND a.lngestimatedetailno = d.lngestimatedetailno";
            $strQuery .= " AND a.lngrevisionno = d.lngrevisionno";
            $strQuery .= " LEFT OUTER JOIN m_order e";
            $strQuery .= " ON d.lngorderno = e.lngorderno";
            $strQuery .= " AND d.lngrevisionno = e.lngrevisionno";

            // �������η��(t_receivedetail, m_receive)
            $strQuery .= " LEFT OUTER JOIN t_receivedetail f";
            $strQuery .= " ON a.lngestimateno = f.lngestimateno";
            $strQuery .= " AND a.lngestimatedetailno = f.lngestimatedetailno";
            $strQuery .= " AND a.lngrevisionno = f.lngrevisionno";
            $strQuery .= " LEFT OUTER JOIN m_receive g";
            $strQuery .= " ON f.lngreceiveno = g.lngreceiveno";
            $strQuery .= " AND f.lngrevisionno = g.lngrevisionno";

            $strQuery .= " WHERE c.strproductcode = '". $productCode. "'";
            $strQuery .= " AND c.strrevisecode = '". $reviseCode. "'";
            $strQuery .= " AND b.lngrevisionno";
            if ($revisionNo) {
                $strQuery .= " = ". $revisionNo;
            } else {
                $strQuery .= " IN (SELECT MAX(lngrevisionno) FROM m_estimate";
                $strQuery .= " WHERE strproductcode = '". $productCode. "'";
                $strQuery .= " AND strrevisecode = '". $reviseCode. "')";
            }
        }

        list ($resultID, $resultNumber) = fncQuery($strQuery, $this);

        if ($resultNumber > 0) {
            for ($i = 0; $i < $resultNumber; ++$i) {
                $result = pg_fetch_object($resultID, $i);   
                $estimateDetail[] = $result;
            }
        } else {
            $estimateDetail = false;
        }

        $this->freeResult($resultID);

        return $estimateDetail;
    }

    // �桼���������ɤ���桼������ɽ��������������
    public function getUserDisplayInfo($userCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT lngusercode, struserdisplaycode, struserdisplayname";
            $strQuery .= " FROM m_user";
            $strQuery .= " WHERE lngusercode = ". $userCode;

            list ($resultID, $resultNumber) = fncQuery($strQuery, $this);

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
    *
    *	�ޥ�����������������ͤ򡢻��ꤷ���������ͤ򥭡��Ȥ���������ֵѤ���
    *
    *	@param string $table �ơ��֥�̾
    *	@param $key ����Υ����Ȥ��ƻ��Ѥ���ơ��֥�Υ����
    *	@param $columns �������륫����ʣ�������������ǻ����
    *
    *	@return array $ret $keys(ʣ���������key�ο��������ؤ���ġˤ򥭡��Ȥ��븡����̤�����
    *	@access public
    */
    public function getMasterToArray($table, $key, $columns) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT ". $key;

            $strQuery .= $strColumn;
            
            if (is_array($columns)) {
                foreach($columns as $column) {
                    $strQuery .= ", ". $column;
                }
            } else if (is_scalar($columns)){
                $strQuery .= ", ". $columns;
            } else {
                return false;
            }

            $strQuery .= " FROM ". $table;

            list ($resultID, $resultNumber) = fncQuery($strQuery, $this);

            if ($resultNumber > 0) {
                for ($i = 0; $i < $resultNumber; ++$i) {
                    $result = pg_fetch_array($resultID, $i, PGSQL_ASSOC);
                    $ret[$result[$key]] = $result; 
                }
            } else {
                $ret = false;
            }

            $this->freeResult($resultID);

            return $ret;
        }
    }

    public function getStockItemDisplayList() {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT lngstockitemcode, lngstocksubjectcode, strstockitemname";
            $strQuery .= " FROM m_stockitem";

            list ($resultID, $resultNumber) = fncQuery($strQuery, $this);

            if ($resultNumber > 0) {
                for ($i = 0; $i < $resultNumber; ++$i) {
                    $result = pg_fetch_array($resultID, $i, PGSQL_ASSOC);
                    $subjectCode = $result['lngstocksubjectcode'];
                    $itemCode = $result['lngstockitemcode'];
                    $ret[$subjectCode][$itemCode] = $result;
                }
            } else {
                $ret = false;
            }

            $this->freeResult($resultID);

            return $ret;
        }
    }

    /**
    *
    *	���ʬ��ʻ������ܡˡ�����ʬ�ʻ������ʡˤΥץ������ǡ������������
    *
    *	@param string $table �ơ��֥�̾
    *	@param $key �����Ȥ��ƻ��Ѥ���ơ��֥�Υ���
    *	@param $columns �������륫����ʣ�������������ǻ����
    *
    *	@return array $ret ������̤Υ��֥������Ȥ��������
    *	@access public
    */
    public function getDropdownForDivSubAndClsItm() {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT";
            $strQuery .= " b.lngtargetarea as areacode,";
            $strQuery .= " c.lngsalesdivisioncode ||':'|| c.strsalesdivisionname as divisionsubject,";
            $strQuery .= " a.lngsalesclasscode ||':'|| a.strsalesclassname as classitem,";
            $strQuery .= " c.lngsalesdivisioncode as divisionsubjectsort,";
            $strQuery .= " a.lngsalesclasscode as classItemsort";
            
            $strQuery .= " FROM m_salesclass a";
            
            $strQuery .= " INNER JOIN m_salesclassdivisonlink b ON a.lngsalesclasscode = b.lngsalesclasscode";
            $strQuery .= " INNER JOIN m_salesdivision c ON b.lngsalesdivisioncode = c.lngsalesdivisioncode";
            
            $strQuery .= " WHERE (char_length(trim(c.strsalesdivisionname)) > 0) AND (char_length(trim(a.strsalesclassname)) > 0)";
            
            $strQuery .= " UNION";
            
            $strQuery .= " SELECT";
            $strQuery .= " d.lngtargetarea as areacode,";
            $strQuery .= " e.lngstocksubjectcode ||':'|| e.strstocksubjectname as divisionsubject,";
            $strQuery .= " d.lngstockitemcode ||':'|| d.strstockitemname as classitem,";
            $strQuery .= " e.lngstocksubjectcode as divisionsubjectsort,";
            $strQuery .= " d.lngstockitemcode as classitemsort";
            
            $strQuery .= " FROM m_stockitem d";
            
            $strQuery .= " INNER JOIN m_stocksubject e ON d.lngstocksubjectcode = e.lngstocksubjectcode";
            $strQuery .= " INNER JOIN m_stockclass f ON e.lngstockclasscode = f.lngstockclasscode";
            
            $strQuery .= " WHERE (char_length(trim(e.strstocksubjectname)) > 0) AND (char_length(trim(d.strstockitemname)) > 0)";
            
            $strQuery .= " ORDER BY areacode ASC, divisionsubjectsort ASC, classitemsort ASC";
            
            list ($resultID, $resultNumber) = fncQuery($strQuery, $this);

            if ($resultNumber > 0) {
                for ($i = 0; $i < $resultNumber; ++$i) {
                    $result = pg_fetch_object($resultID, $i);

                    $ret[] = $result;
                }
            } else {
                $ret = false;
            }

        }
        return $ret;
    }

    public function getDropdownForCompany() {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT";
            $strQuery .= " b.strcompanydisplaycode ||':'|| coalesce(b.strshortname, '')";
            $strQuery .= " as customercompany,";
            $strQuery .= " a.lngattributecode as lngattributecode";
            $strQuery .= " FROM m_attributerelation a";
            $strQuery .= " INNER JOIN m_company b";
            $strQuery .= " ON a.lngcompanycode = b.lngcompanycode";
            $strQuery .= " INNER JOIN m_attribute c";
            $strQuery .= " ON a.lngattributecode = c.lngattributecode";
            $strQuery .= " WHERE a.lngattributecode in (". DEF_ATTRIBUTE_CLIENT. ", ". DEF_ATTRIBUTE_SUPPLIER. ")" ;
            $strQuery .= " ORDER BY lngattributecode ASC, b.strcompanydisplaycode ASC";

            list ($resultID, $resultNumber) = fncQuery($strQuery, $this);

            if ($resultNumber > 0) {
                for ($i = 0; $i < $resultNumber; ++$i) {
                    $result = pg_fetch_object($resultID, $i);

                    $ret[] = $result;
                }
            } else {
                $ret = false;
            }
        }
        return $ret;
    }

    public function getDropdownForGroupAndUser() {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT";
            $strQuery .= " b.strgroupdisplaycode ||':'|| b.strgroupdisplayname as groupcode,";
            $strQuery .= " c.struserdisplaycode ||':'|| c.struserdisplayname as usercode";
            $strQuery .= " FROM m_grouprelation a";
            $strQuery .= " INNER JOIN m_group b";
            $strQuery .= " ON a.lnggroupcode = b.lnggroupcode";
            $strQuery .= " INNER JOIN m_user c";
            $strQuery .= " ON a.lngusercode = c.lngusercode";
            $strQuery .= " INNER JOIN m_company d";
            $strQuery .= " ON b.lngcompanycode = d.lngcompanycode";
            // $strQuery .= " WHERE a.bytdefaultflag = TRUE";
            $strQuery .= " AND b.bytgroupdisplayflag = TRUE";
            $strQuery .= " AND c.bytuserdisplayflag = TRUE";
            $strQuery .= " AND d.lngcompanycode = 1";
            $strQuery .= " AND c.bytinvalidflag = FALSE";
            $strQuery .= " ORDER BY b.strgroupdisplaycode ASC, b.strgroupdisplayname ASC,";
            $strQuery .= " c.struserdisplaycode ASC";
            list ($resultID, $resultNumber) = fncQuery($strQuery, $this);

            if ($resultNumber > 0) {
                for ($i = 0; $i < $resultNumber; ++$i) {
                    $result = pg_fetch_object($resultID, $i);

                    $ret[] = $result;
                }
            } else {
                $ret = false;
            }
        }
        return $ret;
    }


    /**
    *
    *	��¾����ơ��֥�˥쥳���ɤ�¸�ߤ��뤫��ǧ����
    *
    *	@param integer $functionCode ��ǽ������
    *	@param string $key1 ��¾����1
    *	@param string $key2 ��¾����2
    *	@param string $key3 ��¾����3
    *
    *	@return boolean ¸�ߤ�����: false, ¸�ߤ��ʤ����: true
    *	@access public
    */
    public function checkExclusiveStatus($functionCode, $key1, $key2 = null, $key3 = null) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT";
            $strQuery .= " a.lngfunctioncode,";
            $strQuery .= " a.lngusercode,";
            $strQuery .= " a.stripaddress,";
            $strQuery .= " b.struserdisplayname";
            $strQuery .= " FROM t_exclusivecontrol a";
            $strQuery .= " INNER JOIN m_user b";
            $strQuery .= " ON a.lngusercode = b.lngusercode";
            $strQuery .= " WHERE a.lngfunctioncode = ". $functionCode;
            $strQuery .= " AND a.strexclusivekey1 = '". $key1. "'";
            if ($key2) $strQuery .= " AND a.strexclusivekey2 = '". $key2. "'";
            if ($key3) $strQuery .= " AND a.strexclusivekey3 = '". $key3. "'";

            list ($resultID, $resultNumber) = fncQuery($strQuery, $this);

            if ($resultNumber < 1) {
                return false;
            } else {
                return pg_fetch_object($resultID, 0);
            }
        }
    }
}