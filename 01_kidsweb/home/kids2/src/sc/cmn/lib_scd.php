<?
// ----------------------------------------------------------------------------
/**
*       ������  Ǽ�ʽ񸡺���Ϣ�ؿ���
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       ��������
*         ��Ǽ�ʽ񸡺���̴�Ϣ�δؿ�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



/**
* Ǽ�ʽ񸡺��θ������ܤ�����פ���ǿ������ǡ������������SQLʸ�κ����ؿ�
*
*	Ǽ�ʽ񸡺��θ������ܤ��� SQLʸ���������
*
*	@param  Array 	$arySearchColumn 		�����оݥ����̾������
*	@param  Array 	$arySearchDataColumn 	�������Ƥ�����
*	@param  Object	$objDB       			DB���֥�������
*	@param	String	$strSlipCode			Ǽ����ɼ������	��������:������̽���	Ǽ����ɼ�����ɻ����:�����ѡ�Ʊ��Ǽ�ʽ�ΣϤΰ�������
*	@param	Integer	$lngSlipNo				Ǽ����ɼ�ֹ�	0:������̽���	Ǽ����ɼ�ֹ�����:�����ѡ�Ʊ��Ǽ����ɼ�����ɤȤ�������оݳ�Ǽ����ɼ�ֹ�
*	@return Array 	$strSQL ������SQLʸ OR Boolean FALSE
*	@access public
*/
function fncGetSearchSlipSQL ( $arySearchColumn, $arySearchDataColumn, $objDB, $strSlipCode, $lngSlipNo, $strSessionID)
{
	// -----------------------------
	//  ��������ưŪ����
	// -----------------------------
	// ���پ���ɲúѤߥե饰
	$detailFlag = FALSE;

	// Ʊ��Ǽ����ɼ�����ɤΥǡ��������������
	if ( $strSlipCode )
	{
		// Ʊ��Ǽ����ɼ�����ɤ��Ф��ƻ����Ǽ����ɼ�ֹ�Υǡ����Ͻ�������
		if ( $lngSlipNo )
		{
			$aryQuery[] = " WHERE s.bytInvalidFlag = FALSE AND s.strSlipCode = '" . $strSlipCode . "'";
		}
		else
		{
			fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../sc/search2/index.php?strSessionID=".$strSessionID, $objDB );
		}
	}
	// �����⡼�ɤǤ�Ʊ��Ǽ����ɼ�����ɤ��Ф��븡���⡼�ɰʳ��ξ��ϸ��������ɲä���
	else
	{
		// ���о�� ̵���ե饰�����ꤵ��Ƥ��餺���ǿ����Τ�
		$aryQuery[] = " WHERE s.bytInvalidFlag = FALSE AND s.lngRevisionNo >= 0";

		// ���������å��ܥå�����ON�ι��ܤΤ߸��������ɲ�
		for ( $i = 0; $i < count($arySearchColumn); $i++ )
		{
			$strSearchColumnName = $arySearchColumn[$i];
			
			// ----------------------------------------------
			//   Ǽ�ʽ�ޥ����ʥإå����ˤθ������
			// ----------------------------------------------
			// �ܵҡ�������
			if ( $strSearchColumnName == "lngCustomerCompanyCode" )
			{
				if ( $arySearchDataColumn["lngCustomerCompanyCode"] )
				{
					$aryQuery[] = " AND cust_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngCustomerCompanyCode"] . "'";
				}
			}

			// ���Ƕ�ʬ�ʾ����Ƕ�ʬ��
			if ( $strSearchColumnName == "lngTaxClassCode" )
			{
				if ( $arySearchDataColumn["lngTaxClassCode"] )
				{
					$aryQuery[] = " AND s.lngTaxClassCode = '" . $arySearchDataColumn["lngTaxClassCode"] . "'";
				}
			}

			// Ǽ����ɼ�����ɡ�Ǽ�ʽ�NO��
			if ( $strSearchColumnName == "strSlipCode" )
			{
				if ( $arySearchDataColumn["strSlipCode"] )
				{
					// ����޶��ڤ�������ͤ�OR����Ÿ��
					$arySCValue = explode(",",$arySearchDataColumn["strSlipCode"]);
					foreach($arySCValue as $strSCValue){
						$arySCOr[] = "UPPER(s.strSlipCode) LIKE UPPER('%" . $strSCValue . "%')";
					}
					$aryQuery[] = " AND (";
					$aryQuery[] = implode(" OR ", $arySCOr);
					$aryQuery[] = ") ";
				}
			}

			// Ǽ����
			if ( $strSearchColumnName == "dtmDeliveryDate" )
			{
				if ( $arySearchDataColumn["dtmDeliveryDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmDeliveryDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND s.dtmDeliveryDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmDeliveryDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmDeliveryDateTo"] . " 23:59:59";
					$aryQuery[] = " AND s.dtmDeliveryDate <= '" . $dtmSearchDate . "'";
				}
			}

			// Ǽ����
			if ( $strSearchColumnName == "lngDeliveryPlaceCode" )
			{
				if ( $arySearchDataColumn["lngDeliveryPlaceCode"] )
				{
					//��ҥޥ�����ɳ�Ť����ͤ����
					$aryQuery[] = " AND delv_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'";
				}
//				if ( $arySearchDataColumn["strDeliveryPlaceName"] )
//				{
//					$aryQuery[] = " AND UPPER(s.strDeliveryPlaceName) LIKE UPPER('%" . $arySearchDataColumn["strDeliveryPlaceName"] . "%')";
//				}
			}

			// ��ɼ��
			if ( $strSearchColumnName == "lngInsertUserCode" )
			{
				if ( $arySearchDataColumn["lngInsertUserCode"] )
				{
					$aryQuery[] = " AND insert_u.struserdisplaycode ~* '" . $arySearchDataColumn["lngInsertUserCode"] . "'";
				}
//				if ( $arySearchDataColumn["strInsertUserName"] )
//				{
//					$aryQuery[] = " AND UPPER(s.strInsertUserName) LIKE UPPER('%" . $arySearchDataColumn["strInsertUserName"] . "%')";
//				}
			}

			// ----------------------------------------------
			//   Ǽ����ɼ���٥ơ��֥���������ˤθ������
			// ----------------------------------------------
			// ��ʸ��NO.
			if ( $strSearchColumnName == "strCustomerSalesCode" )
			{
				if ( $arySearchDataColumn["strCustomerSalesCode"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						unset( $aryDetailTargetQuery );
						$aryDetailTargetQuery[] = " where";
						
						$aryDetailWhereQuery[] = "AND ";
					}

					// ����޶��ڤ�������ͤ�OR����Ÿ��
					$aryCSCValue = explode(",",$arySearchDataColumn["strCustomerSalesCode"]);
					foreach($aryCSCValue as $strCSCValue){
						$aryCSCOr[] = "UPPER(sd1.strCustomerSalesCode) LIKE UPPER('%" . $strCSCValue . "%')";
					}
					$aryDetailWhereQuery[] = " (";
					$aryDetailWhereQuery[] = implode(" OR ", $aryCSCOr);
					$aryDetailWhereQuery[] = ") ";

					$detailFlag = TRUE;
				}
			}
		
			// �ܵ�����
			if ( $strSearchColumnName == "strGoodsCode" )
			{
				if ( $arySearchDataColumn["strGoodsCode"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						unset( $aryDetailTargetQuery );
						$aryDetailTargetQuery[] = " where";
						
						$aryDetailWhereQuery[] = "AND ";
					}

					// ����޶��ڤ�������ͤ�OR����Ÿ��
					$aryGCValue = explode(",",$arySearchDataColumn["strGoodsCode"]);
					foreach($aryGCValue as $strGCValue){
						$aryGCOr[] = "UPPER(sd1.strGoodsCode) LIKE UPPER('%" . $strGCValue . "%')";
					}
					$aryDetailWhereQuery[] = " (";
					$aryDetailWhereQuery[] = implode(" OR ", $aryGCOr);
					$aryDetailWhereQuery[] = ") ";

					$detailFlag = TRUE;
				}
			}

			// ����ʬ
			if ( $strSearchColumnName == "lngSalesClassCode" )
			{
				if ( $arySearchDataColumn["lngSalesClassCode"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
					$aryDetailWhereQuery[] = "sd1.lngSalesClassCode = " . $arySearchDataColumn["lngSalesClassCode"] . " ";
					$detailFlag = TRUE;
				}
			}
		}
	}



	// ---------------------------------
	//   SQLʸ�κ���
	// ---------------------------------
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT distinct s.lngSlipNo as lngSlipNo";	//Ǽ����ɼ�ֹ�
	$aryOutQuery[] = "	,s.lngSalesNo as lngSalesNo";			    //����ֹ�
	$aryOutQuery[] = "	,s.lngRevisionNo as lngRevisionNo";			//��ӥ�����ֹ�
	$aryOutQuery[] = "	,s.dtmInsertDate as dtmInsertDate";			//������
	// �ܵ�
	$arySelectQuery[] = ", cust_c.strcompanydisplaycode as strCustomerDisplayCode";
	$arySelectQuery[] = ", cust_c.strcompanydisplayname as strCustomerDisplayName";
	// �ܵҤι�
	$arySelectQuery[] = ", cust_c.lngCountryCode as lngcountrycode";
	// ������ֹ�
	$arySelectQuery[] = ", sa.lngInvoiceNo as lnginvoiceno";
	// ���Ƕ�ʬ
	$arySelectQuery[] = ", s.strTaxClassName as strTaxClassName";
	// Ǽ����ɼ�����ɡ�Ǽ�ʽ�NO��
	$arySelectQuery[] = ", s.strSlipCode as strSlipCode";
	// Ǽ����
	$arySelectQuery[] = ", to_char( s.dtmDeliveryDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmDeliveryDate";
	// Ǽ����
	$arySelectQuery[] = " , delv_c.strcompanydisplaycode as strdeliveryplacecode";
	$arySelectQuery[] = " , s.strDeliveryPlaceName as strDeliveryPlaceName";
	// ��ɼ��
	$arySelectQuery[] = ", insert_u.struserdisplaycode as strInsertUserCode";
	$arySelectQuery[] = ", s.strInsertUserName as strInsertUserName";
	// ����
	$arySelectQuery[] = ", s.strNote as strNote";
	// ��׶��
	$arySelectQuery[] = ", To_char( s.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
	//// ���Σ�
	$arySelectQuery[] = ", sa.strSalesCode as strSalesCode";
	// �����֥�����
	$arySelectQuery[] = ", sa.lngSalesStatusCode as lngSalesStatusCode";
	$arySelectQuery[] = ", ss.strSalesStatusName as strSalesStatusName";
	// �̲�ñ��
	$arySelectQuery[] = ", s.lngMonetaryUnitCode";
	$arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";

	// select�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $arySelectQuery);

	// From�� ������
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM m_Slip s";
//	if ( !$strSlipCode )
//	{
		 $aryFromQuery[] = "INNER JOIN (SELECT lngSlipNo, MAX(lngRevisionNo) AS lngRevisionNo from m_slip group by lngSlipNo) max_rev "
		 . "on max_rev.lngSlipNo = s.lngslipno and max_rev.lngRevisionNo = s.lngrevisionno";

//    }
	$aryFromQuery[] = " INNER JOIN m_Sales sa ON s.lngSalesNo = sa.lngSalesNo AND s.lngRevisionNo = sa.lngRevisionNo";
	$aryFromQuery[] = " LEFT JOIN m_SalesStatus ss ON sa.lngSalesStatusCode = ss.lngSalesStatusCode";
	$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON s.lngCustomerCode = cust_c.lngCompanyCode";
	$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	$aryFromQuery[] = " LEFT JOIN m_User insert_u ON s.lngInsertUserCode = insert_u.lngusercode";
	$aryFromQuery[] = " LEFT JOIN m_Company delv_c ON s.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
	// From�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryFromQuery);

	// ���ٸ����ѥơ��֥�����
	$aryDetailFrom = array();
	$aryDetailFrom[] = ", (SELECT distinct on ( sd1.lngSlipNo ) sd1.lngSlipNo ";
	$aryDetailFrom[] = "	,sd1.lngSlipDetailNo";				// Ǽ����ɼ�����ֹ�
	$aryDetailFrom[] = "	,sd1.lngSortKey as lngRecordNo";	// ���ٹ�NO
	$aryDetailFrom[] = "	,sd1.strCustomerSalesCode";			// ��ʸ��NO
	$aryDetailFrom[] = "	,sd1.strGoodsCode";					// �ܵ�����
	$aryDetailFrom[] = "	,sd1.strProductName";				// ��̾
	$aryDetailFrom[] = "	,sd1.strSalesClassName";	// ����ʬ
	$aryDetailFrom[] = "	,sd1.curProductPrice";		// ñ��
	$aryDetailFrom[] = "	,sd1.lngQuantity";	        // ����
	$aryDetailFrom[] = "	,sd1.lngProductQuantity";	// ����
	$aryDetailFrom[] = "	,sd1.strProductUnitName";	// ñ��
	$aryDetailFrom[] = "	,sd1.curSubTotalPrice";		// ��ȴ���
	$aryDetailFrom[] = "	,sd1.strNote";				// ��������
	$aryDetailFrom[] = "	FROM t_SlipDetail sd1 ";
	// where������ٹԡ� �����꡼Ϣ��
	$strDetailQuery = implode("\n", $aryDetailFrom) . "\n";
	// ���ٹԤξ�郎¸�ߤ�����
	if ( $detailFlag )
	{
		$strDetailQuery .= implode("\n", $aryDetailTargetQuery) . "\n";
	}
	$aryDetailWhereQuery[] = ") as sd";
	$strDetailQuery .= implode("\n", $aryDetailWhereQuery) . "\n";
	
	// Where�� �����꡼Ϣ��
	$aryOutQuery[] = $strDetailQuery;
	$aryOutQuery[] = implode("\n", $aryQuery);

	// ���ٹ��Ѥξ��Ϣ��
	$aryOutQuery[] = " AND sd.lngSlipNo = s.lngSlipNo";


	/////////////////////////////////////////////////////////////
	//// �ǿ����ʥ�ӥ�����ֹ椬���硢��Х����ֹ椬���硢     ////
	//// ���ĥ�ӥ�����ֹ�����ͤ�̵���ե饰��FALSE��           ////
	//// Ʊ��Ǽ����ɼ�����ɤ���ĥǡ�����̵�����ǡ���          ////
	/////////////////////////////////////////////////////////////
	// Ǽ����ɼ�����ɤ����ꤵ��Ƥ��ʤ����ϸ����������ꤹ��
	if ( !$strSlipCode )
	{
		// �����⡼�ɤξ��Ϻ���ǡ����⸡���оݤȤ��뤿��ʲ��ξ����оݳ�
		if ( !$arySearchDataColumn["Admin"] )
		{
//			$aryOutQuery[] = " AND 0 <= ( "
//				. "SELECT MIN( s2.lngRevisionNo ) FROM m_Slip s2 WHERE s2.bytInvalidFlag = false AND s2.strSlipCode = s.strSlipCode )";
			$aryOutQuery[] = " AND not exists (SELECT lngslipno from m_slip s1 where s1.lngslipno=s.lngslipno and s1.lngRevisionNo < 0 and s1.bytInvalidFlag = false)";
		}
	}

	// Ʊ��Ǽ����ɼ�����ɤΥǡ��������������
	if ($strSlipCode)
	{
		$aryOutQuery[] = " ORDER BY dtmInsertDate DESC";
	}
	else
	{
		// �����Ⱦ������
		$aryOutQuery[] = " ORDER BY lngSlipNo DESC";		
	}
	return implode("\n", $aryOutQuery);
}



/**
* ���ꤷ��Ǽ����ɼ�ֹ�Υǡ������б���������ٹԡפ��������SQLʸ�κ����ؿ�
*
*	Ǽ����ɼ�ֹ椫�����٤��������SQLʸ���������
*
*	@param  String 	$lngSlipNo 			    �о�Ǽ����ɼ�ֹ�
*	@param  Array 	$aryData 				POST�ǡ���������
*	@param  Object	$objDB       			DB���֥�������
*	@return Array 	$strSQL ������SQLʸ OR Boolean FALSE
*	@access public
*/
function fncGetSlipToProductSQL ( $lngSlipNo, $lngRevisionNo, $aryData, $objDB )
{
	// ----------------------
	//   SQLʸ�κ���
	// ----------------------
	$aryOutQuery = array();
	//���ٹ�NO
	$aryOutQuery[] = "SELECT sd.lngSortKey as lngRecordNo";
	//Ǽ����ɼ�ֹ�
	$aryOutQuery[] = "	,sd.lngSlipNo as lngSlipNo";
	//��ӥ�����ֹ�	
	$aryOutQuery[] = "	,sd.lngRevisionNo as lngRevisionNo";
	// ��ʸ��NO.
	$aryOutQuery[] = ", sd.strCustomerSalesCode as strCustomerSalesCode";
	// �ܵ�����
	$aryOutQuery[] = ", sd.strGoodsCode as strGoodsCode";
	// ��̾
	$aryOutQuery[] = ", sd.strProductName as strProductName";
	// ����ʬ
	$aryOutQuery[] = ", sd.lngSalesClassCode as lngSalesClassCode";
	$aryOutQuery[] = ", sd.strSalesClassName as strSalesClassName";
	// ñ��
	$aryOutQuery[] = ", To_char( sd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
	// ����
	$aryOutQuery[] = ", To_char( sd.lngQuantity, '9,999,999,990' )  as lngQuantity";
	// ����
	$aryOutQuery[] = ", To_char( sd.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
	// ñ��
	$aryOutQuery[] = ", sd.strProductUnitName as strProductUnitName";
	// ��ȴ���
	$aryOutQuery[] = ", To_char( sd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
	// ��������
	$aryOutQuery[] = ", sd.strNote as strDetailNote";
	// �����ơ�����������
	$aryOutQuery[] = ", re.lngReceiveStatusCode as lngReceiveStatusCode";

	// From��
	$aryOutQuery[] = " FROM t_SlipDetail sd";
	$aryOutQuery[] = "  LEFT JOIN ( ";
	$aryOutQuery[] = "    select";
	$aryOutQuery[] = "      r1.* ";
	$aryOutQuery[] = "    from";
	$aryOutQuery[] = "      m_Receive r1 ";
	$aryOutQuery[] = "      inner join ( ";
	$aryOutQuery[] = "        select";
	$aryOutQuery[] = "          max(lngRevisionNo) lngRevisionNo";
	$aryOutQuery[] = "          , strreceivecode ";
	$aryOutQuery[] = "        from";
	$aryOutQuery[] = "          m_Receive ";
	$aryOutQuery[] = "        group by";
	$aryOutQuery[] = "          strreceivecode";
	$aryOutQuery[] = "      ) r2 ";
	$aryOutQuery[] = "        on r1.lngrevisionno = r2.lngRevisionNo ";
	$aryOutQuery[] = "        and r1.strreceivecode = r2.strreceivecode";
	$aryOutQuery[] = "  ) re ";
	$aryOutQuery[] = "    ON sd.lngReceiveNo = re.lngReceiveNo ";

	// Where��
	$aryOutQuery[] = " WHERE sd.lngSlipNo = " . $lngSlipNo . " AND sd.lngRevisionNo = " . $lngRevisionNo . "";	// �о�Ǽ����ɼ�ֹ�λ���

	// OrderBy��
	$aryOutQuery[] = " ORDER BY sd.lngSortKey ASC";

	return implode("\n", $aryOutQuery);
}


/**
 * Ǽ�ʽ񥳡��ɤˤ��ǡ����ξ��֤��ǧ����
 *
 * @param [type] $strstrslipcode
 * @param [type] $objDB
 * @return void [0:����ѥǡ�����1�������оݥǡ���]
 */
function fncCheckData($strslipcode, $objDB)
{
    $result = 0;
    unset($aryQuery);
    $aryQuery[] = "SELECT";
    $aryQuery[] = " min(lngrevisionno) lngrevisionno, bytInvalidFlag, strslipcode ";
    $aryQuery[] = "FROM m_slip ";
    $aryQuery[] = "WHERE strslipcode='" . $strslipcode . "'";
    $aryQuery[] = "group by strslipcode, bytInvalidFlag";
    // �������ʿ�פ�ʸ������Ѵ�
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum) {
        $resultObj = $objDB->fetchArray($lngResultID, 0);
    }

    $objDB->freeResult($lngResultID);

    if ($resultObj["lngrevisionno"] < 0) {
        $result = 1;
    }
    return $result;
}

/**
 * ���٥ǡ����μ���
 *
 * @param [type] $lngSlipNo
 * @param [type] $lngRevisionNo
 * @param [type] $objDB
 * @return void
 */
function fncGetDetailData($lngSlipNo, $lngRevisionNo, $objDB)
{
    $detailData = array();
    unset($aryQuery);
	$aryQuery[] = "select";
	$aryQuery[] = "  sd.lngSlipDetailNo";
	$aryQuery[] = "  , sd.strCustomerSalesCode";
	$aryQuery[] = "  , sd.strGoodsCode";
	$aryQuery[] = "  , sd.strProductCode";
	$aryQuery[] = "  , sd.strProductName";
	$aryQuery[] = "  , sd.strSalesClassName";
	$aryQuery[] = "  , to_char(sd.curProductPrice, '9,999,999,990.99') as curProductPrice";
	$aryQuery[] = "  , to_char(sd.lngQuantity, '9,999,999,990.99') as lngQuantity";
	$aryQuery[] = "  , to_char(sd.lngProductQuantity, '9,999,999,990.99') as lngProductQuantity";
	$aryQuery[] = "  , sd.strProductUnitName";
	$aryQuery[] = "  , to_char(sd.curSubTotalPrice, '9,999,999,990.99') as curSubTotalPrice";
	$aryQuery[] = "  , sd.strNote ";
	$aryQuery[] = "from";
	$aryQuery[] = "  t_slipdetail sd ";
	$aryQuery[] = "where";
	$aryQuery[] = "  sd.lngslipno = " . $lngSlipNo;
    $aryQuery[] = "  AND sd.lngrevisionno = " . $lngRevisionNo;
    $aryQuery[] = "ORDER BY";
    $aryQuery[] = "  sd.lngSlipDetailNo ASC";
    // �������ʿ�פ�ʸ������Ѵ�
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    // �������������ξ��
    if ($lngResultNum > 0) {
        // ���������Ǥ�����̾����
        for ($i = 0; $i < $lngResultNum; $i++) {
            $detailData = pg_fetch_all($lngResultID);
        }
    }
    $objDB->freeResult($lngResultID);

    return $detailData;
}


/**
 * �إå������ǡ���������
 *
 * @param [type] $doc
 * @param [type] $trBody
 * @param [type] $bgcolor
 * @param [type] $aryTableHeaderName
 * @param [type] $record
 * @param [type] $toUTF8Flag
 * @return void
 */
function fncSetHeaderDataToTr($doc, $trBody, $bgcolor, $rowspan, $aryTableHeaderName, $record, $toUTF8Flag)
{
	// TODO �ץ�ե��������
    // ���ꤵ�줿�ơ��֥���ܤΥ�����������
    foreach ($aryTableHeaderName as $key => $value) {
        // �����̤�ɽ���ƥ����Ȥ�����
        switch ($key) {
            // �ܵ�
            case "lngCustomerCode":
                if ($record["strcustomerdisplaycode"] != '') {
                    $textContent = "[" . $record["strcustomerdisplaycode"] . "]" . " " . $record["strcustomerdisplayname"];
                } else {
                    $textContent .= "     ";
				}
				if ($toUTF8Flag) {
					$textContent = toUTF8($textContent);
				}
                $td = $doc->createElement("td", $textContent);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // ���Ƕ�ʬ
			case "lngTaxClassCode":
				$textContent = $record["strtaxclassname"];
				if ($toUTF8Flag) {
					$textContent = toUTF8($textContent);
				}
                $td = $doc->createElement("td", $textContent);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // Ǽ�ʽ�NO.
            case "strSlipCode":
                $td = $doc->createElement("td", $record["strslipcode"]);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // Ǽ����
            case "dtmDeliveryDate":
                $td = $doc->createElement("td", str_replace("-", "/", substr($record["dtmdeliverydate"], 0, 19)));
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // Ǽ����
            case "lngDeliveryPlaceCode":
                if ($record["strdeliveryplacecode"] != '') {
                    $textContent = "[" . $record["strdeliveryplacecode"] . "]" . " " . $record["strdeliveryplacename"];
                } else {
                    $textContent = "     ";
				}
				if ($toUTF8Flag) {
					$textContent = toUTF8($textContent);
				}
                $td = $doc->createElement("td", $textContent);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // ��ɼ��
            case "lngInsertUserCode":
                if ($record["strinsertusercode"] != '') {
                    $textContent = "[" . $record["strinsertusercode"] . "]" . " " . $record["strinsertusername"];
                } else {
                    $textContent .= "     ";
				}				
				if ($toUTF8Flag) {
					$textContent = toUTF8($textContent);
				}
                $td = $doc->createElement("td", $textContent);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // ����
			case "strNote":
				$textContent = $record["strnote"];
				if ($toUTF8Flag) {
					$textContent = toUTF8($textContent);
				}
                $td = $doc->createElement("td", $textContent);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // ��׶��
            case "curTotalPrice":
                $textContent = toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curtotalprice"]);
                $td = $doc->createElement("td", $textContent);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
        }
	}
	
    return $trBody;
}

/**
 * ���ٹԥǡ���������
 *
 * @param [type] $doc
 * @param [type] $trBody
 * @param [type] $bgcolor
 * @param [type] $aryTableDetailHeaderName
 * @param [type] $displayColumns
 * @param [type] $detailData
 * @return void
 */
function fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $detailData, $headerData, $toUTF8Flag)
{
    // ���ꤵ�줿�ơ��֥���ܤΥ�����������
    foreach ($aryTableDetailHeaderName as $key => $value) {
            // �����̤�ɽ���ƥ����Ȥ�����
            switch ($key) {                
                // ���ٹ��ֹ�
                case "lngRecordNo":
                    $td = $doc->createElement("td", $detailData["lngslipdetailno"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // Ǽ����
                case "dtmDeliveryDate":
                    if ($toUTF8Flag) {
                        $td = $doc->createElement("td", str_replace( "-", "/", toUTF8(substr( $detailData["dtmdeliverydate"], 0, 19 ))));
                    } else {
                        $td = $doc->createElement("td", str_replace( "-", "/", substr( $detailData["dtmdeliverydate"], 0, 19 )));
                       
                    }
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��ʸ��NO
                case "strCustomerSalesCode":
                    $td = $doc->createElement("td", $detailData["strcustomersalescode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
					break;				
                // �ܵ�����
                case "strGoodsCode":
                    $td = $doc->createElement("td", $detailData["strgoodscode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;		
                // ��̾
                case "strProductName":
					$textContent = "[" . $detailData["strproductcode"] . "]" . " " . $detailData["strproductname"];                    
					if ($toUTF8Flag) {
						$textContent = toUTF8($textContent);
					}
					$td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
					break;			
                // ����ʬ
                case "strSalesClassName":
					$textContent = $detailData["strsalesclassname"];                    
					if ($toUTF8Flag) {
						$textContent = toUTF8($textContent);
					}
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ñ��
                case "curProductPrice":
                    $textContent = toMoneyFormat($headerData["lngmonetaryunitcode"], $headerData["strmonetaryunitsign"], $detailData["curproductprice"]);
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����
                case "lngQuantity":
                    $textContent = $detailData["lngquantity"];                    
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����
                case "lngProductQuantity":
                    $textContent = $detailData["lngproductquantity"];                    
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ñ��
                case "strProductUnitName":
                    $textContent = $detailData["strproductunitname"];                    
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��ȴ���
                case "curSubTotalPrice":
                    $textContent = toMoneyFormat($headerData["lngmonetaryunitcode"], $headerData["strmonetaryunitsign"], $detailData["cursubtotalprice"]);
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��������
                case "strDetailNote":
                    $textContent = $detailData["strnote"];
                    if ($toUTF8Flag) {
                        $textContent = toUTF8($textContent);
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
            }

    }
    return $trBody;
}



function fncGetSlipsByStrSlipCodeSQL($strslipcode, $lngrevisionno)
{
	$aryQuery[] = "SELECT distinct";
	$aryQuery[] = "  s.lngSlipNo as lngSlipNo";
	$aryQuery[] = "  , s.lngSalesNo as lngSalesNo";
	$aryQuery[] = "  , s.lngRevisionNo as lngRevisionNo";
	$aryQuery[] = "  , s.dtmInsertDate as dtmInsertDate";
	$aryQuery[] = "  , cust_c.strcompanydisplaycode as strCustomerDisplayCode";
	$aryQuery[] = "  , cust_c.strcompanydisplayname as strCustomerDisplayName";
	$aryQuery[] = "  , cust_c.lngCountryCode as lngcountrycode";
	$aryQuery[] = "  , sa.lngInvoiceNo as lnginvoiceno";
	$aryQuery[] = "  , s.strTaxClassName as strTaxClassName";
	$aryQuery[] = "  , s.strSlipCode as strSlipCode";
	$aryQuery[] = "  , to_char(s.dtmDeliveryDate, 'YYYY/MM/DD HH:MI:SS') as dtmDeliveryDate";
	$aryQuery[] = "  , delv_c.strcompanydisplaycode as strdeliveryplacecode";
	$aryQuery[] = "  , s.strDeliveryPlaceName as strDeliveryPlaceName";
	$aryQuery[] = "  , insert_u.struserdisplaycode as strInsertUserCode";
	$aryQuery[] = "  , s.strInsertUserName as strInsertUserName";
	$aryQuery[] = "  , s.strNote as strNote";
	$aryQuery[] = "  , To_char(s.curTotalPrice, '9,999,999,990.99') as curTotalPrice";
	$aryQuery[] = "  , sa.strSalesCode as strSalesCode";
	$aryQuery[] = "  , sa.lngSalesStatusCode as lngSalesStatusCode";
	$aryQuery[] = "  , ss.strSalesStatusName as strSalesStatusName";
	$aryQuery[] = "  , s.lngMonetaryUnitCode ";
	$aryQuery[] = "  , mu.strMonetaryUnitSign as strMonetaryUnitSign ";
	$aryQuery[] = "FROM";
	$aryQuery[] = "  m_Slip s ";
	$aryQuery[] = "  INNER JOIN m_Sales sa ";
	$aryQuery[] = "    ON s.lngSalesNo = sa.lngSalesNo ";
	$aryQuery[] = "    AND s.lngRevisionNo = sa.lngRevisionNo ";
	$aryQuery[] = "  LEFT JOIN m_SalesStatus ss ";
	$aryQuery[] = "    ON sa.lngSalesStatusCode = ss.lngSalesStatusCode ";
	$aryQuery[] = "  LEFT JOIN m_Company cust_c ";
	$aryQuery[] = "    ON s.lngCustomerCode = cust_c.lngCompanyCode ";
	$aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
	$aryQuery[] = "    ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode ";
	$aryQuery[] = "  LEFT JOIN m_User insert_u ";
	$aryQuery[] = "    ON s.lngInsertUserCode = insert_u.lngusercode ";
	$aryQuery[] = "  LEFT JOIN m_Company delv_c ";
	$aryQuery[] = "    ON s.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
	$aryQuery[] = "WHERE";
	$aryQuery[] = "  s.bytInvalidFlag = FALSE ";
	$aryQuery[] = "  AND s.lngRevisionNo <>" .$lngrevisionno. "";
	$aryQuery[] = "  AND s.strslipcode = '". $strslipcode."'";
	$aryQuery[] = "ORDER BY";
	$aryQuery[] = "  s.lngrevisionno DESC";

    return implode("\n", $aryQuery);
}
?>