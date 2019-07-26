<?php

require_once ( 'conf.inc' );		// 設定読み込み
require_once ( LIB_FILE );			// ライブラリ読み込み

require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php"); // ワークシート処理用定数ファイル


// 見積原価DBデータ処理クラス
class estimateDB extends clsDB {
    public function __construct() {
        parent::__construct();
    }

    // 適用レートの取得
    public function getTemporaryRateList() {        
        if (!$this->isOpen()) {
			return false;
		} else {
            $today = date('Y/m/d');
            $thisMonthfirstDay = date('Y/m/1');
            $endDay = date('Y/m/t', strtotime($thisMonthfirstDay . "+6 month"));
            $monetaryRateCode = DEF_MONETARY_RATE_CODE_COMPANY_LOCAL; // 社内レート
    
            $strQuery = "SELECT lngmonetaryunitcode, curconversionrate, dtmapplystartdate, dtmapplyenddate FROM m_monetaryrate";
            $strQuery .= " WHERE TO_DATE('$today', 'YYYY/MM/DD') <= dtmapplyenddate";
            $strQuery .= " AND dtmapplystartdate <= TO_DATE('$endDay', 'YYYY/MM/DD')";
            $strQuery .= " AND lngmonetaryratecode = ". $monetaryRateCode;
    
            $queryResult = fncQuery($strQuery, $this); // [0]:結果ID [1]:取得行数
    
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

            $strQuery = "SELECT a.lngattributecode, b.strcompanydisplaycode FROM m_attributerelation a";
            $strQuery .= " INNER JOIN m_company b";
            $strQuery .= " ON a.lngcompanycode = b.lngcompanycode";
            $strQuery .= " WHERE a.lngattributecode = ". $orderAttribute;

            $queryResult = fncQuery($strQuery, $this); // [0]:結果ID [1]:取得行数

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
    *	検索結果の中で指定したカラムの最大値を含むレコードを取得
    *   (テーブルによっては複数行の結果が返ってくることもあるため、使用時には留意すること)
    *	
    *   @param string $table            検索するテーブル名
    *   @param string $needle           最大値を取得するカラム
    *   @param string $searchColumn     検索を行う列
    *   @param scalar $searchValue      検索条件
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
    *	検索結果の中で指定したカラムの最大値を取得
    *	
    *   @param string $table            検索するテーブル名
    *   @param string $needle           最大値を取得するカラム
    *   @param string $searchColumn     検索を行う列
    *   @param scalar $searchValue      検索条件
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
    public function getDivisionCodeList($areaCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT c.lngsalesdivisioncode, b.lngsalesclasscode FROM m_salesclass a"; 
            $strQuery .= " INNER JOIN m_salesclassdivisonlink b ON a.lngsalesclasscode = b.lngsalesclasscode";
            $strQuery .= " INNER JOIN m_salesdivision c ON b.lngsalesdivisioncode = c.lngsalesdivisioncode";
            $strQuery .= " INNER JOIN m_pulldown d ON c.lngsalesdivisioncode = d.lngitemcode";
            $strQuery .= " WHERE d.lngtargetarea = ". $areaCode;

            $queryResult = fncQuery($strQuery, $this); // [0]:結果ID [1]:取得行数

            for ($i = 0; $i < $queryResult[1]; ++$i) {
                $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                $divisionCode = $result['lngsalesdivisioncode']; // 仕入科目コード
                $classCode = $result['lngsalesclasscode']; // 仕入部品コード
                // 売上分類コードの下に売上区分コードの配列を生成する
                // 配列イメージ　$divisionCodeList = array(divisionCodeA => array(classCode1, classCode2, ...), divisionCodeB => array[(classCode1, classCode2, ...), ...)
                $divisionCodeList[$divisionCode][] = $classCode;          
            }
            $this->freeResult($queryResult[0]);
            return $divisionCodeList;
        }        
    }

    // 仕入科目マスターデータの取得
    public function getSubjectCodeList($areaCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT b.lngstocksubjectcode, b.lngstockclasscode, a.lngstockitemcode FROM m_stockitem a";
            $strQuery .= " INNER JOIN m_stocksubject b ON a.lngstocksubjectcode = b.lngstocksubjectcode";
            $strQuery .= " INNER JOIN m_stockclass c ON b.lngstockclasscode = c.lngstockclasscode";
            $strQuery .= " INNER JOIN m_pulldown d ON b.lngstocksubjectcode = d.lngitemcode";
            $strQuery .= " WHERE d.lngtargetarea = " .$areaCode;

            $queryResult = fncQuery($strQuery, $this); // [0]:結果ID [1]:取得行数

            for ($i = 0; $i < $queryResult[1]; ++$i) {
                $result = pg_fetch_array($queryResult[0], $i, PGSQL_ASSOC);
                $classCode = $result['lngstockclasscode']; // 仕入区分コード
                $subjectCode = $result['lngstocksubjectcode']; // 仕入科目コード
                $itemCode = $result['lngstockitemcode']; // 仕入部品コード
                // 仕入科目コードの下に仕入部品コードの配列を生成する
                    $subjectCodeList[$classCode][$subjectCode][$i] = $itemCode;            
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

    // ログインユーザーが該当営業部署に所属しているかチェックする
    public function loginUserAffiliateCheck($loginUserCode, $groupDisplayCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery  = "SELECT a.strgroupdisplaycode FROM m_group a";
            $strQuery .= " INNER JOIN m_grouprelation b ON b.lnggroupcode = a.lnggroupcode";
            $strQuery .= " INNER JOIN m_user c ON c.lngusercode = b.lngusercode";
            $strQuery .= " WHERE a.strgroupdisplaycode = '". $groupDisplayCode. "' and b.lngusercode = ". $loginUserCode;
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

    // 要修正
    public function getProductMasterData($productCode, $reviseCode) {
        if (!$this->isOpen()) {
            return false;
        } else {
            $strQuery = "SELECT * FROM m_product";
            $strQuery .= " WHERE strproductcode = '". $productCode. "'";
            $strQuery .= " AND strrevisecode = $reviseCode";
            $strQuery .= " AND bytinvalidflag = false";
        }
        $queryResult = fncQuery($strQuery, $objDB); // [0]:結果ID [1]:取得行数
        $result = pg_fetch_array($queryResult[0], 0, PGSQL_ASSOC);
        if (!$result) {
            $revise = false;
        } else {
            $revise = $result['max'];
        }
        $this->freeResult($queryResult[0]);   
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
            list($resultID, $resultNumber) = fncQuery("SELECT To_char( curstandardrate, '990.9999' ) AS curstandardrate FROM m_estimatestandardrate WHERE dtmApplyStartDate < NOW() AND dtmApplyEndDate > NOW()", $this);

            if ($resultNumber < 1) {
                // もし当月の標準割合が参照できない場合最新の日付の標準割合を参照
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
    *	製品コード、リバイスコードに紐付く最新の見積原価明細のデータを取得する
    *
    *	@param string $productCode 製品コード
    *	@param string $reviseCode  リバイスコード
    *
    *	@return array $estimateDetail 見積原価明細
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
    *	製品コード、リバイスコードに紐付く最新の見積原価明細のデータを取得する
    *
    *	@param string $productCode 製品コード
    *	@param string $reviseCode  リバイスコード
    *
    *	@return array $estimateDetail 見積原価明細
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