<?php

require_once ( 'conf.inc' );		// 設定読み込み
require_once ( LIB_FILE );			// ライブラリ読み込み

require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php"); // ワークシート処理用定数ファイル


// 見積原価DBデータ処理クラス
class estimateDB extends clsDB {
    public function __construct() {
        parent::__construct();
    }

    // 適用レートの取得(社内レート)
    public function getTemporaryRateList() {        
        if (!$this->isOpen()) {
			return false;
		} else {
            $today = date('Y/m/d');
            $thisMonthfirstDay = date('Y/m/1');
            $endDay = date('Y/m/t', strtotime($thisMonthfirstDay . "+6 month"));
            $monetaryRateCode = DEF_MONETARY_RATE_CODE_COMPANY_LOCAL; // 社内レート
    
            $strQuery = "SELECT";
            $strQuery .= " lngmonetaryunitcode,";
            $strQuery .= " curconversionrate,";
            $strQuery .= " dtmapplystartdate,"; 
            $strQuery .= " dtmapplyenddate";
            $strQuery .= " FROM m_monetaryrate";
    
            $queryResult = fncQuery($strQuery, $this); // [0]:結果ID [1]:取得行数
    
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

    // 通貨名称取得
    public function getMonetaryUnitList() {
        if (!$this->isOpen()) {
			return false;
		} else {
            $strQuery = "SELECT lngmonetaryunitcode, strmonetaryunitname FROM m_monetaryunit";
        }
        $queryResult = fncQuery($strQuery, $this); // [0]:結果ID [1]:取得行数
    
        for ($i = 0; $i < $queryResult[1]; ++$i) {
            $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
            $monetaryUnitCode = $result['lngmonetaryunitcode'];
            $monetaryUnitList[$monetaryUnitCode] = $result['strmonetaryunitname'];
        }
        $this->freeResult($queryResult[0]);
        return $monetaryUnitList;
    }
    
    // 顧客先、仕入先マスターデータ取得
    public function getCustomerCompanyCodeList($areaCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $orderAttributeArray = workSheetConst::ORDER_ATTRIBUTE_FOR_TARGET_AREA; // 売上、仕入
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

            $queryResult = fncQuery($strQuery, $this); // [0]:結果ID [1]:取得行数

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
    *	使用可能な発注コードを発番する
    *	
    *   @return string $orderCode      発注コード
    *   
    */
    public function getOrderCode() {
        if (!$this->isOpen()) {
            return false;
        } else {
            $thisMonth = date('Ym');
            $strQuery = "SELECT * FROM m_order WHERE strordercode LIKE '". $thisMonth. "%' ORDER BY strordercode DESC"; 
            list($resultID, $resultNumber) = fncQuery($strQuery, $this);
            // 取得できた場合
            if (0 < $resultNumber) {
                $result = pg_fetch_object($resultID, 0);
                $maxOrderCode = $result->strordercode;
                // 最大の発注コードに1を足す
                $orderCode = (string)((int)$maxOrderCode + 1);
            // 当月に発行された発注番号を取得できなかった場合は001から採番する
            } else {
                $orderCode = $thisMonth.'001';
            }
        }
        $this->freeResult($queryResult[0]);
        return $orderCode;
    }

    /**
    *	使用可能な受注コードを発番する
    *	
    *   @return string $reviceCode      受注コード
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
                // 最大の受注コードに1を足す
                $reviceCode = (int)(str_replace('d', '', $maxReceiveCode)) + 1;
                // 接頭辞のdをつける
                $reviceCode = 'd'. $reviceCode;
            // 当月に発行された受注番号を取得できなかった場合は001から採番する
            } else {
                $reviceCode = 'd'. $thisMonth. '001';
            }
        }
        $this->freeResult($queryResult[0]);
        return $reviceCode;
    }

    /**
    *	製品コードで検索した結果の中で、リバイスコードが最大の中からリビジョンが最大のレコードを取得する
    *	
    *   @param string $productCode      製品コード
    *   @return array $ret              製品コードに対する最新のレコード
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
    *	DBでの検索結果を取得する
    *	
    *   @param string $table            検索するテーブル名
    *   @param string $searchColumn     検索を行う列
    *   @param scalar $searchValue      検索条件
    *   @return array $record           取得したレコード
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

    // 売上分類マスターデータの取得
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
            
            $queryResult = fncQuery($strQuery, $this); // [0]:結果ID [1]:取得行数

            for ($i = 0; $i < $queryResult[1]; ++$i) {
                $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                $divisionCode = $result['lngsalesdivisioncode']; // 仕入科目コード
                $classCode = $result['lngsalesclasscode']; // 仕入部品コード
                if (!isset($areaCode)) {
                    // 対象エリアの指定がない場合はエリアコード別の配列を生成する
                    $targetArea = $result['lngtargetarea'];
                    $divisionCodeList[$targetArea][$divisionCode][$classCode] = true;
                } else {
                    // 売上分類コードの下に売上区分コードの配列を生成する
                    // 配列イメージ　$divisionCodeList = array(divisionCodeA => array(classCode1, classCode2, ...), divisionCodeB => array[(classCode1, classCode2, ...), ...)
                    $divisionCodeList[$divisionCode][$classCode] = true;
                }
            }
            $this->freeResult($queryResult[0]);
            return $divisionCodeList;
        }        
    }

    // 仕入科目マスターデータの取得
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

            $queryResult = fncQuery($strQuery, $this); // [0]:結果ID [1]:取得行数

            for ($i = 0; $i < $queryResult[1]; ++$i) {
                $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                $classCode = $result['lngstockclasscode']; // 仕入区分コード
                $subjectCode = $result['lngstocksubjectcode']; // 仕入科目コード
                $itemCode = $result['lngstockitemcode']; // 仕入部品コード
                if (!isset($areaCode)) {
                    // 対象エリアの指定がない場合はエリアコード別の配列を生成する
                    $targetArea = $result['lngtargetarea'];
                    $subjectCodeList[$targetArea][$classCode][$subjectCode][$itemCode] = true; 
                } else {
                    // 仕入科目コードの下に仕入部品コードの配列を生成する
                    $subjectCodeList[$classCode][$subjectCode][$itemCode] = true; 
                }
            }
            $this->freeResult($queryResult[0]);
            return $subjectCodeList;
        }
    }

    // グループコードによる検索
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

    // ユーザーコードによる検索
    public function getUserRecordForDisplay ($userDisplayCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT * FROM m_user WHERE struserid = '". $userDisplayCode. "'";
        }
        list($resultID, $resultNumber) = fncQuery($strQuery, $this); // [0]:結果ID [1]:取得行数
        if (0 < $resultNumber) {
            $result = pg_fetch_object($resultID, 0);
        } else {
            $result = false;
        }
        $this->freeResult($resultID);
        return $result;
    }

    // ユーザーコードが該当営業部署に所属しているかチェックする
    public function userCodeAffiliateCheck($userCode, $groupDisplayCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery  = "SELECT a.strgroupdisplaycode FROM m_group a";
            $strQuery .= " INNER JOIN m_grouprelation b ON b.lnggroupcode = a.lnggroupcode";
            $strQuery .= " INNER JOIN m_user c ON c.lngusercode = b.lngusercode";
            $strQuery .= " WHERE a.strgroupdisplaycode = '". $groupDisplayCode. "' and b.lngusercode = ". $userCode;
            $strQuery .= " AND c.bytinvalidflag = false";
            
            $queryResult = fncQuery($strQuery, $this); // [0]:結果ID [1]:取得行数
            // 検索結果があればtrueを返す
            if (0 < $queryResult[1]) {
                $result =  true;
            } else {
                $result = false;
            }
            $this->freeResult($queryResult[0]);
            return $result;
        }        
    }

    // ユーザー表示コードが該当営業部署に所属しているかチェックする
    public function userDisplayCodeAffiliateCheck($userDisplayCode, $groupDisplayCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery  = "SELECT a.strgroupdisplaycode FROM m_group a";
            $strQuery .= " INNER JOIN m_grouprelation b ON b.lnggroupcode = a.lnggroupcode";
            $strQuery .= " INNER JOIN m_user c ON c.lngusercode = b.lngusercode";
            $strQuery .= " WHERE a.strgroupdisplaycode = '". $groupDisplayCode. "' and c.struserdisplaycode = '". $userDisplayCode."'";
            $strQuery .= " AND c.bytinvalidflag = false";
            
            $queryResult = fncQuery($strQuery, $this); // [0]:結果ID [1]:取得行数
            // 検索結果があればtrueを返す
            if (0 < $queryResult[1]) {
                $result =  true;
            } else {
                $result = false;
            }
            $this->freeResult($queryResult[0]);
            return $result;
        }        
    }


    // 製品マスタの部署とワークシートの営業部署が一致するか確認する
    public function checkConsistencyGroup($productCode, $groupDisplayCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery  = "SELECT * FROM m_group";
            $strQuery .= " WHERE lnginchargegroupcode = ". $productCode;
            $strQuery .= " AND bytinvalidflag = false";
            
            $queryResult = fncQuery($strQuery, $this); // [0]:結果ID [1]:取得行数
            // 検索結果があればtrueを返す
            if (0 < $queryResult[1]) {
                $result =  true;
            } else {
                $result = false;
            }
            $this->freeResult($queryResult[0]);
            return $result;
        }        
    }

    // 製品コードの存在を確認し、リバイスコードを返す
    public function getReviseCode($productCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT max(strrevisecode) FROM m_product WHERE strproductcode = '". $productCode. "'";
            $queryResult = fncQuery($strQuery, $this); // [0]:結果ID [1]:取得行数
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

    // グループ（部署）とユーザーの所属関係をチェックする
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

            $queryResult = fncQuery($strQuery, $this); // [0]:結果ID [1]:取得行数

            if ($queryResult[1] == 2) {
                return true;
            } else if ($queryResult[1] == 1) {
                    $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                    $userId = $result['struserid']; // ユーザーID
                    $groupCode = $result['strgroupdisplaycode']; // 表示用グループコード         
                return false;
            } else {
                return false;
            }
        }
    }

    /**
    * 標準割合取得関数
    *
    *	標準割合データクエリ関数
    *
    *	@return Integer $curStandardRate 標準割合
    *	@access public
    */
    public function getEstimateStandardRate() {
        if (!$this->isOpen()) {
            return false;
        } else {
            list($resultID, $resultNumber) = fncQuery("SELECT curstandardrate FROM m_estimatestandardrate WHERE dtmApplyStartDate <= NOW() AND dtmApplyEndDate >= NOW()", $this);

            if ($resultNumber < 1) {
                // もし当月の標準割合が参照できない場合最新の日付の標準割合を参照
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
    *	製品コード、リバイスコード、リビジョン番号に紐付く最新の受注明細を取得する
    *
    *	@param string $productCode 製品コード
    *	@param string $reviseCode  リバイスコード
    *
    *	@return array $estimateDetail 見積原価明細
    *	@access public
    */
    public function getReceiveDetail($productCode, $reviseCode, $revisionNo = null) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT";
            // 見積原価明細の情報取得
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
            // 受注状態の情報取得
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
                // リビジョン番号が指定されている場合
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
    *	製品コード、リバイスコード、リビジョン番号に紐付く最新の発注明細を取得する
    *
    *	@param string $productCode 製品コード
    *	@param string $reviseCode  リバイスコード
    *
    *	@return array $estimateDetail 見積原価明細
    *	@access public
    */
    public function getOrderDetail($productCode, $reviseCode, $revisionNo = null) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT";
            // 見積原価明細の情報取得
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
            // 発注状態の情報取得
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
                // リビジョン番号が指定されている場合
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
    *	製品マスタから製品コード、リバイスコード、リビジョン番号に紐付く最新の製品のデータを取得する
    *
    *	@param string $productCode 製品コード
    *	@param string $reviseCode  リバイスコード
    *
    *	@return array $estimateDetail 見積原価明細
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
                // リビジョン番号が指定されている場合
                $strQuery .= " = ". $revisionNo;
            } else {
                // リビジョン番号が指定されていない場合は最新のレコードを取得する
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
    *	製品コード、リバイスコード、リビジョン番号に紐付く見積原価明細のデータを取得する
    *
    *	@param string $productCode 製品コード
    *	@param string $reviseCode  リバイスコード
    *
    *	@return array $estimateDetail 見積原価明細
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

            // 発注情報の結合(t_orderdetail, m_order)
            $strQuery .= " LEFT OUTER JOIN t_orderdetail d";
            $strQuery .= " ON a.lngestimateno = d.lngestimateno";
            $strQuery .= " AND a.lngestimatedetailno = d.lngestimatedetailno";
            $strQuery .= " AND a.lngrevisionno = d.lngrevisionno";
            $strQuery .= " LEFT OUTER JOIN m_order e";
            $strQuery .= " ON d.lngorderno = e.lngorderno";
            $strQuery .= " AND d.lngrevisionno = e.lngrevisionno";

            // 受注情報の結合(t_receivedetail, m_receive)
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

    // ユーザーコードからユーザーの表示情報を取得する
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
    *	マスターから取得した値を、指定したカラムの値をキーとする配列で返却する
    *
    *	@param string $table テーブル名
    *	@param $key 配列のキーとして使用するテーブルのカラム
    *	@param $columns 取得するカラム（複数ある場合は配列で指定）
    *
    *	@return array $ret $keys(複数ある場合はkeyの数だけ階層を持つ）をキーとする検索結果の配列
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
    *	売上分類（仕入科目）、売上区分（仕入部品）のプルダウンデータを作成する
    *
    *	@param string $table テーブル名
    *	@param $key キーとして使用するテーブルのセル
    *	@param $columns 取得するカラム（複数ある場合は配列で指定）
    *
    *	@return array $ret 検索結果のオブジェクトを持つ配列
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
    *	排他制御テーブルにレコードが存在するか確認する
    *
    *	@param integer $functionCode 機能コード
    *	@param string $key1 排他キー1
    *	@param string $key2 排他キー2
    *	@param string $key3 排他キー3
    *
    *	@return boolean 存在する場合: false, 存在しない場合: true
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