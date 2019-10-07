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
            $strQuery .= " mc.lngcompanycode,";
            $strQuery .= " mc.strcompanydisplaycode,";
            $strQuery .= " mc.strcompanydisplayname,";
            $strQuery .= " mc.strshortname";
            $strQuery .= " FROM m_attributerelation mar";
            $strQuery .= " INNER JOIN m_company mc";
            $strQuery .= " ON mar.lngcompanycode = mc.lngcompanycode";
            $strQuery .= " INNER JOIN m_attribute ma";
            $strQuery .= " ON mar.lngattributecode = ma.lngattributecode";
            $strQuery .= " WHERE mar.lngattributecode = ". $orderAttribute;
            $strQuery .= " ORDER BY mc.strcompanydisplaycode";

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
            $strQuery = "SELECT * FROM m_product mp";
            $strQuery .= " INNER JOIN m_group mg ON mp.lnginchargegroupcode = mg.lnggroupcode";
            $strQuery .= " INNER JOIN m_user mu ON mp.lnginputusercode = mu.lngusercode";
            $strQuery .= " WHERE strproductcode = '". $productCode. "'";
            $strQuery .= " ORDER BY mp.strrevisecode DESC, mp.lngrevisionno DESC";

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
            $strQuery = "SELECT msd.lngsalesdivisioncode, mscdl.lngsalesclasscode, mscdl.lngestimateareaclassno FROM m_salesclass msc";
            $strQuery .= " INNER JOIN m_salesclassdivisonlink mscdl ON msc.lngsalesclasscode = mscdl.lngsalesclasscode";
            $strQuery .= " INNER JOIN m_estimateareaclass meac ON mscdl.lngestimateareaclassno = meac.lngestimateareaclassno";
            $strQuery .= " INNER JOIN m_salesdivision msd ON mscdl.lngsalesdivisioncode = msd.lngsalesdivisioncode";

            if (isset($areaCode)) {
                $strQuery .= " WHERE mscdl.lngestimateareaclassno = ". $areaCode;
            }
            
            $queryResult = fncQuery($strQuery, $this); // [0]:���ID [1]:�����Կ�

            for ($i = 0; $i < $queryResult[1]; ++$i) {
                $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                $divisionCode = $result['lngsalesdivisioncode']; // �������ܥ�����
                $classCode = $result['lngsalesclasscode']; // �������ʥ�����
                if (!isset($areaCode)) {
                    // �оݥ��ꥢ�λ��꤬�ʤ����ϥ��ꥢ�������̤��������������
                    $targetArea = $result['lngestimateareaclassno'];
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
            $strQuery = "SELECT mss.lngstocksubjectcode, mss.lngstockclasscode, msi.lngstockitemcode, msi.lngestimateareaclassno FROM m_stockitem msi";
            $strQuery .= " INNER JOIN m_estimateareaclass meac ON msi.lngestimateareaclassno = meac.lngestimateareaclassno";
            $strQuery .= " INNER JOIN m_stocksubject mss ON msi.lngstocksubjectcode = mss.lngstocksubjectcode";
            $strQuery .= " INNER JOIN m_stockclass msc ON mss.lngstockclasscode = msc.lngstockclasscode";
            if (isset($areaCode)) {
                $strQuery .= " WHERE msi.lngestimateareaclassno = " .$areaCode;
            }            

            $queryResult = fncQuery($strQuery, $this); // [0]:���ID [1]:�����Կ�

            for ($i = 0; $i < $queryResult[1]; ++$i) {
                $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                $classCode = $result['lngstockclasscode']; // ������ʬ������
                $subjectCode = $result['lngstocksubjectcode']; // �������ܥ�����
                $itemCode = $result['lngstockitemcode']; // �������ʥ�����
                if (!isset($areaCode)) {
                    // �оݥ��ꥢ�λ��꤬�ʤ����ϥ��ꥢ�������̤��������������
                    $targetArea = $result['lngestimateareaclassno'];
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
            $strQuery  = "SELECT mg.strgroupdisplaycode FROM m_group mg";
            $strQuery .= " INNER JOIN m_grouprelation mgr ON mgr.lnggroupcode = mg.lnggroupcode";
            $strQuery .= " INNER JOIN m_user mu ON mu.lngusercode = mgr.lngusercode";
            $strQuery .= " WHERE mg.strgroupdisplaycode = '". $groupDisplayCode. "' and mgr.lngusercode = ". $userCode;
            $strQuery .= " AND mu.bytinvalidflag = false";
            
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
            $strQuery  = "SELECT mg.strgroupdisplaycode FROM m_group mg";
            $strQuery .= " INNER JOIN m_grouprelation mgr ON mgr.lnggroupcode = mg.lnggroupcode";
            $strQuery .= " INNER JOIN m_user mu ON mu.lngusercode = mgr.lngusercode";
            $strQuery .= " WHERE mg.strgroupdisplaycode = '". $groupDisplayCode. "' and mu.struserdisplaycode = '". $userDisplayCode."'";
            $strQuery .= " AND mu.bytinvalidflag = false";
            
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
            $strQuery = "SELECT mg.strgroupdisplaycode, mu.struserid FROM m_grouprelation mgr";
            $strQuery .= " INNER JOIN m_group mg ON mgr.lnggroupcode = mg.lnggroupcode";
            $strQuery .= " INNER JOIN m_user mu ON mgr.lngusercode = mu.lngusercode";
            if (is_array($userCode)) {
                $key = 0;
                foreach ($userCode as $code) {
                    if($key === 0) {
                        $strQuery .= " WHERE (mg.strgroupdisplaycode = '". $groupCode ."' and mu.struserid ='". $code. "')";
                    } else {
                        $strQuery .= " OR (mg.strgroupdisplaycode = '". $groupCode. "' and mu.struserid ='". $code. "')";
                    }
                    ++$key;
                }                
            } else {
                $strQuery .= " WHERE mg.strgroupdisplaycode = '". $groupCode. "' and mu.struserid ='". $userCode. "'";
            }
            $strQuery .= " AND mu.bytinvalidflag = false";

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
            $strQuery .= " ted.lngestimateno AS lngestimateno,";
            $strQuery .= " ted.lngestimatedetailno AS lngestimatedetailno,";
            $strQuery .= " ted.lngrevisionno AS lngrevisionno,";
            $strQuery .= " ted.lngstocksubjectcode AS lngstocksubjectcode,";
            $strQuery .= " ted.lngstockitemcode AS lngstockitemcode,";
            $strQuery .= " ted.lngcustomercompanycode AS lngcustomercompanycode,";
            $strQuery .= " to_char(ted.dtmdelivery, 'YYYY/MM/DD') AS dtmdelivery,";
            $strQuery .= " ted.bytpayofftargetflag AS bytpayofftargetflag,";
            $strQuery .= " ted.bytpercentinputflag AS bytpercentinputflag,";
            $strQuery .= " ted.dblpercent AS dblpercent,";
            $strQuery .= " ted.lngmonetaryunitcode AS lngmonetaryunitcode,";
            $strQuery .= " ted.lngmonetaryratecode AS lngmonetaryratecode,";
            $strQuery .= " ted.curconversionrate AS curconversionrate,";
            $strQuery .= " ted.lngproductquantity AS lngproductquantity,";
            $strQuery .= " ted.curproductprice AS curproductprice,";
            $strQuery .= " ted.curproductrate AS curproductrate,";
            $strQuery .= " ted.cursubtotalprice AS cursubtotalprice,";
            $strQuery .= " ted.strnote AS strnote,";
            $strQuery .= " ted.lngsortkey AS lngsortkey,";
            $strQuery .= " ted.lngsalesdivisioncode AS lngsalesdivisioncode,";
            $strQuery .= " ted.lngsalesclasscode AS lngsalesclasscode,";
            // ������֤ξ������
            $strQuery .= " mr.lngreceivestatuscode AS lngreceivestatuscode";

            $strQuery .= " FROM m_receive mr";
            $strQuery .= " INNER JOIN t_receivedetail trd";
            $strQuery .= " ON mr.lngreceiveno = trd.lngreceiveno";
            $strQuery .= " AND mr.lngrevisionno = trd.lngrevisionno";
            $strQuery .= " INNER JOIN t_estimatedetail ted";
            $strQuery .= " ON trd.lngestimateno = ted.lngestimateno";
            $strQuery .= " AND trd.lngestimatedetailno = ted.lngestimatedetailno";
            $strQuery .= " INNER JOIN m_estimate me";
            $strQuery .= " ON ted.lngestimateno = me.lngestimateno";
            $strQuery .= " AND ted.lngrevisionno = me.lngrevisionno";
            $strQuery .= " INNER JOIN m_product mp";
            $strQuery .= " ON me.strproductcode = mp.strproductcode";
            $strQuery .= " AND me.strrevisecode = mp.strrevisecode";
            $strQuery .= " WHERE mp.strproductcode = '". $productCode. "'";
            $strQuery .= " AND mp.strrevisecode = '". $reviseCode. "'";
            $strQuery .= " AND me.lngrevisionno";
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
            $strQuery .= " ted.lngestimateno AS lngestimateno,";
            $strQuery .= " ted.lngestimatedetailno AS lngestimatedetailno,";
            $strQuery .= " ted.lngrevisionno AS lngrevisionno,";
            $strQuery .= " ted.lngstocksubjectcode AS lngstocksubjectcode,";
            $strQuery .= " ted.lngstockitemcode AS lngstockitemcode,";
            $strQuery .= " ted.lngcustomercompanycode AS lngcustomercompanycode,";
            $strQuery .= " to_char(ted.dtmdelivery, 'YYYY/MM/DD') AS dtmdelivery,";
            $strQuery .= " ted.bytpayofftargetflag AS bytpayofftargetflag,";
            $strQuery .= " ted.bytpercentinputflag AS bytpercentinputflag,";
            $strQuery .= " ted.dblpercent AS dblpercent,";
            $strQuery .= " ted.lngmonetaryunitcode AS lngmonetaryunitcode,";
            $strQuery .= " ted.lngmonetaryratecode AS lngmonetaryratecode,";
            $strQuery .= " ted.curconversionrate AS curconversionrate,";
            $strQuery .= " ted.lngproductquantity AS lngproductquantity,";
            $strQuery .= " ted.curproductprice AS curproductprice,";
            $strQuery .= " ted.curproductrate AS curproductrate,";
            $strQuery .= " ted.cursubtotalprice AS cursubtotalprice,";
            $strQuery .= " ted.strnote AS strnote,";
            $strQuery .= " ted.lngsortkey AS lngsortkey,";
            $strQuery .= " ted.lngsalesdivisioncode AS lngsalesdivisioncode,";
            $strQuery .= " ted.lngsalesclasscode AS lngsalesclasscode,";
            // ȯ����֤ξ������
            $strQuery .= " mo.lngorderstatuscode AS lngorderstatuscode";

            $strQuery .= " FROM m_order mo";
            $strQuery .= " INNER JOIN t_orderdetail tod";
            $strQuery .= " ON mo.lngorderno = tod.lngorderno";
            $strQuery .= " AND mo.lngrevisionno = tod.lngrevisionno";
            $strQuery .= " INNER JOIN t_estimatedetail ted";
            $strQuery .= " ON tod.lngestimateno = ted.lngestimateno";
            $strQuery .= " AND tod.lngestimatedetailno = ted.lngestimatedetailno";
            $strQuery .= " INNER JOIN m_estimate me";
            $strQuery .= " ON ted.lngestimateno = me.lngestimateno";
            $strQuery .= " AND ted.lngrevisionno = me.lngrevisionno";
            $strQuery .= " INNER JOIN m_product mp";
            $strQuery .= " ON me.strproductcode = mp.strproductcode";
            $strQuery .= " AND me.strrevisecode = mp.strrevisecode";
            $strQuery .= " WHERE mp.strproductcode = '". $productCode. "'";
            $strQuery .= " AND mp.strrevisecode = '". $reviseCode. "'";
            $strQuery .= " AND me.lngrevisionno";
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
    public function getEstimateDetail($estimateNo, $revisionNo = null) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT";
            $strQuery .= " ted.lngestimateno,";
            $strQuery .= " ted.lngestimatedetailno,";
            $strQuery .= " ted.lngrevisionno,";
            $strQuery .= " ted.lngstocksubjectcode,";
            $strQuery .= " ted.lngstockitemcode,";
            $strQuery .= " ted.lngcustomercompanycode,";
            $strQuery .= " to_char(ted.dtmdelivery, 'YYYY/MM/DD') AS dtmdelivery,";
            $strQuery .= " ted.bytpayofftargetflag,";
            $strQuery .= " ted.bytpercentinputflag,";
            $strQuery .= " ted.curproductrate,";
            $strQuery .= " ted.lngmonetaryunitcode,";
            $strQuery .= " ted.lngmonetaryratecode,";
            $strQuery .= " ted.curconversionrate,";
            $strQuery .= " ted.lngproductquantity,";
            $strQuery .= " ted.curproductprice,";
            $strQuery .= " ted.curproductrate,";
            $strQuery .= " ted.cursubtotalprice,";
            $strQuery .= " ted.strnote,";
            $strQuery .= " ted.lngsortkey,";
            $strQuery .= " ted.lngsalesdivisioncode,";
            $strQuery .= " ted.lngsalesclasscode,";
            $strQuery .= " me.lngproductrevisionno,";
            $strQuery .= " mo.lngorderno,";
            $strQuery .= " mo.lngorderstatuscode,";
            $strQuery .= " mr.lngreceiveno,";
            $strQuery .= " mr.lngreceivestatuscode,";
            $strQuery .= " mp.strproductcode,";
            $strQuery .= " mp.strrevisecode";

            $strQuery .= " FROM t_estimatedetail ted";
            $strQuery .= " INNER JOIN m_estimate me";
            $strQuery .= " ON ted.lngestimateno = me.lngestimateno";
            $strQuery .= " AND ted.lngrevisionno = me.lngrevisionno";
            $strQuery .= " INNER JOIN m_product mp";
            $strQuery .= " ON me.strproductcode = mp.strproductcode";
            $strQuery .= " AND me.strrevisecode = mp.strrevisecode";
            $strQuery .= " AND me.lngproductrevisionno = mp.lngrevisionno";

            // ȯ�����η��(t_orderdetail, m_order)
            $strQuery .= " LEFT OUTER JOIN t_orderdetail tod";
            $strQuery .= " ON ted.lngestimateno = tod.lngestimateno";
            $strQuery .= " AND ted.lngestimatedetailno = tod.lngestimatedetailno";
            $strQuery .= " AND ted.lngrevisionno = tod.lngrevisionno";
            $strQuery .= " LEFT OUTER JOIN m_order mo";
            $strQuery .= " ON tod.lngorderno = mo.lngorderno";
            $strQuery .= " AND tod.lngrevisionno = mo.lngrevisionno";

            // �������η��(t_receivedetail, m_receive)
            $strQuery .= " LEFT OUTER JOIN t_receivedetail trd";
            $strQuery .= " ON ted.lngestimateno = trd.lngestimateno";
            $strQuery .= " AND ted.lngestimatedetailno = trd.lngestimatedetailno";
            $strQuery .= " AND ted.lngrevisionno = trd.lngrevisionno";
            $strQuery .= " LEFT OUTER JOIN m_receive mr";
            $strQuery .= " ON trd.lngreceiveno = mr.lngreceiveno";
            $strQuery .= " AND trd.lngrevisionno = mr.lngrevisionno";

            $strQuery .= " WHERE me.lngestimateno = ". $estimateNo;
            $strQuery .= " AND me.lngrevisionno";
            if ($revisionNo) {
                $strQuery .= " = ". $revisionNo;
            } else {
                $strQuery .= " IN (SELECT MAX(lngrevisionno) FROM m_estimate";
                $strQuery .= " WHERE lngestimateno = ". $estimateNo. ")";
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
            $strQuery .= " b.lngestimateareaclassno as areacode,";
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
            $strQuery .= " d.lngestimateareaclassno as areacode,";
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
            $strQuery .= " mc.strcompanydisplaycode ||':'|| coalesce(mc.strshortname, '')";
            $strQuery .= " as customercompany,";
            $strQuery .= " mar.lngattributecode as lngattributecode";
            $strQuery .= " FROM m_attributerelation mar";
            $strQuery .= " INNER JOIN m_company mc";
            $strQuery .= " ON mar.lngcompanycode = mc.lngcompanycode";
            $strQuery .= " INNER JOIN m_attribute ma";
            $strQuery .= " ON mar.lngattributecode = ma.lngattributecode";
            $strQuery .= " WHERE mar.lngattributecode in (". DEF_ATTRIBUTE_CLIENT. ", ". DEF_ATTRIBUTE_SUPPLIER. ")" ;
            $strQuery .= " ORDER BY lngattributecode ASC, mc.strcompanydisplaycode ASC";

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
            $strQuery .= " mg.strgroupdisplaycode ||':'|| mg.strgroupdisplayname as groupcode,";
            $strQuery .= " mu.struserdisplaycode ||':'|| mu.struserdisplayname as usercode";
            $strQuery .= " FROM m_grouprelation mgr";
            $strQuery .= " INNER JOIN m_group mg";
            $strQuery .= " ON mgr.lnggroupcode = mg.lnggroupcode";
            $strQuery .= " INNER JOIN m_user mu";
            $strQuery .= " ON mgr.lngusercode = mu.lngusercode";
            $strQuery .= " INNER JOIN m_company mc";
            $strQuery .= " ON mg.lngcompanycode = mc.lngcompanycode";

            $strQuery .= " INNER JOIN m_groupattributerelation mgar";
            $strQuery .= " ON mg.lnggroupcode = mgar.lnggroupcode";

            $strQuery .= " WHERE mg.bytgroupdisplayflag = TRUE";
            $strQuery .= " AND mu.bytuserdisplayflag = TRUE";
            $strQuery .= " AND mc.lngcompanycode = 1";
            $strQuery .= " AND mu.bytinvalidflag = FALSE";
            $strQuery .= " AND mgar.lngattributecode = ". DEF_GROUP_ATTRIBUTE_CODE_SALES_GROUP;
            $strQuery .= " ORDER BY mg.strgroupdisplaycode ASC, mg.strgroupdisplayname ASC,";
            $strQuery .= " mu.struserdisplaycode ASC";
            
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

    // ��ȯô���ԤΥɥ�åץ�����ꥹ�Ȥ��������
    public function getDropdownForDevelopUser() {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT";
            $strQuery .= " mu.struserdisplaycode ||':'|| mu.struserdisplayname as usercode";
            $strQuery .= " FROM m_grouprelation mgr";
            $strQuery .= " INNER JOIN m_group mg";
            $strQuery .= " ON mgr.lnggroupcode = mg.lnggroupcode";
            $strQuery .= " INNER JOIN m_user mu";
            $strQuery .= " ON mgr.lngusercode = mu.lngusercode";
            $strQuery .= " INNER JOIN m_company mc";
            $strQuery .= " ON mg.lngcompanycode = mc.lngcompanycode";

            $strQuery .= " INNER JOIN m_groupattributerelation mgar";
            $strQuery .= " ON mg.lnggroupcode = mgar.lnggroupcode";

            $strQuery .= " WHERE mg.bytgroupdisplayflag = TRUE";
            $strQuery .= " AND mu.bytuserdisplayflag = TRUE";
            $strQuery .= " AND mc.lngcompanycode = 1";
            $strQuery .= " AND mu.bytinvalidflag = FALSE";
            $strQuery .= " AND mgar.lngattributecode = ". DEF_GROUP_ATTRIBUTE_CODE_DEVELOP_GROUP;
            $strQuery .= " ORDER BY mg.strgroupdisplaycode ASC, mg.strgroupdisplayname ASC,";
            $strQuery .= " mu.struserdisplaycode ASC";
            
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
            $strQuery .= " tec.lngfunctioncode,";
            $strQuery .= " tec.lngusercode,";
            $strQuery .= " tec.stripaddress,";
            $strQuery .= " mu.struserdisplayname";
            $strQuery .= " FROM t_exclusivecontrol tec";
            $strQuery .= " INNER JOIN m_user mu";
            $strQuery .= " ON tec.lngusercode = mu.lngusercode";
            $strQuery .= " WHERE tec.lngfunctioncode = ". $functionCode;
            $strQuery .= " AND tec.strexclusivekey1 = '". $key1. "'";
            if ($key2) $strQuery .= " AND tec.strexclusivekey2 = '". $key2. "'";
            if ($key3) $strQuery .= " AND tec.strexclusivekey3 = '". $key3. "'";

            list ($resultID, $resultNumber) = fncQuery($strQuery, $this);

            if ($resultNumber < 1) {
                return false;
            } else {
                return pg_fetch_object($resultID, 0);
            }
        }
    }

    /**
    *
    *	�Ķ����硢��ȯ�����°���륰�롼�פ���ӥ桼�����������
    *
    *	@access public
    */
    public function getSalesGroupAndDevelopGroup() {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT";
            $strQuery .= " mga.lngattributecode,";
            $strQuery .= " mg.strgroupdisplaycode,";
            $strQuery .= " mg.strgroupdisplayname,";
            $strQuery .= " mu.struserdisplaycode,";
            $strQuery .= " mu.struserdisplayname";
            $strQuery .= " FROM m_grouprelation mgr";
            $strQuery .= " INNER JOIN m_user mu";
            $strQuery .= " ON mu.lngusercode = mgr.lngusercode";
            $strQuery .= " INNER JOIN m_group mg";
            $strQuery .= " ON mg.lnggroupcode = mgr.lnggroupcode";
            $strQuery .= " INNER JOIN m_groupattributerelation mgar";
            $strQuery .= " ON mgar.lnggroupcode = mg.lnggroupcode";
            $strQuery .= " INNER JOIN m_groupattribute mga";
            $strQuery .= " ON mga.lngattributecode = mgar.lngattributecode";
            $strQuery .= " WHERE mga.lngattributecode in (". DEF_GROUP_ATTRIBUTE_CODE_SALES_GROUP. ",". DEF_GROUP_ATTRIBUTE_CODE_DEVELOP_GROUP.")";
            $strQuery .= " ORDER BY mga.lngattributecode ASC,";
            $strQuery .= " mg.strgroupdisplaycode ASC,";
            $strQuery .= " mu.struserdisplaycode ASC";

            list ($resultID, $resultNumber) = fncQuery($strQuery, $this);

            $ret = [];

            if ($resultNumber >= 1) {
                for ($i = 0; $i < $resultNumber; ++$i) {
                    $result = pg_fetch_object($resultID, $i);
                    $ret[] = $result;
                }
            }

            return $ret;
        }
    }
}