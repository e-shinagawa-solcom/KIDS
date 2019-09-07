<?
/** 
*	ȯ�����̴ؿ���
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.01
*
*	��������
*	��Ͽ��Ϣ���̽����ؿ���
*
*	��������
*	2004.04.02	���ʲ��ʥޥ��������ʥơ��֥�ؤ���Ͽ�ؿ����ɲ�
*
*
*/

/**
* �����ȯ��ǡ����˴ؤ��ơ�����ȯ�����٥ǡ��������ꤵ��Ƥ������ʥ����ɤΰ㤤��Ĵ������ؿ�
*
*	ȯ�����پ��������ʥ����ɤΰ㤦�Ԥ�¸�ߤ��ʤ����ɤ����Υ����å��ؿ�
*
*	@param	Array		$aryOrderDetail	ȯ����Ͽ�ˤ����ꤵ�줿���پ���
*	@param  Object		$objDB			DB���֥�������
*	@return Integer 	0				�¹����������٤Υǡ����Ϥ��٤�Ʊ�����ʥ����ɤΥǡ����Ǥ���
*						99				�¹Լ��ԡ����٤˰㤦���ʥ����ɤΥǡ�����¸�ߤ���
*	@access public
*/
function fncCheckOrderDetailProductCode ( $aryOrderDetail, $objDB )
{
	$bytSearchFlag = 0;

	// �����٤ˤĤ���Ĵ��
	for ( $i = 0; $i < count($aryOrderDetail); $i++ )
	{
		$strProductCode1 = $aryOrderDetail[$i]["strProductCode"];
		// ���ꤵ�줿�����̾�˥ǡ������ʤ����
		if ( $strProductCode1 == "" )
		{
			$strProductCode1 = $aryOrderDetail[$i]["strproductcode"];
		}

		// �����٤ˤĤ���Ĵ��
		for ( $j = 0; $j < count($aryOrderDetail); $j++ )
		{
			$strProductCode2 = $aryOrderDetail[$j]["strProductCode"];
			// ���ꤵ�줿�����̾�˥ǡ������ʤ����
			if ( $strProductCode2 == "" )
			{
				$strProductCode2 = $aryOrderDetail[$j]["strproductcode"];
			}
			
			if ( $strProductCode1 != $strProductCode2 )
			{
				$bytSearchFlag = 1;
				break;
			}
		}
		if ( $bySeachFlag == 1 )
		{
			break;
		}
	}

	if ( $bytSearchFlag == 1 )
	{
		return 99;
	}

	return 0;
}

/**
* ȯ����Ͽ�������ʲ��ʥޥ��������ʥơ��֥�ξ����ǧ����Ͽ�ؿ�
*
*	ȯ�����پ��������ʲ��ʥޥ����ˤʤ��ǡ��������ʥơ��֥�ˤʤ��ǡ����򿷵�����Ͽ����
*
*	@param	Array		$aryOrderDetail			ȯ����Ͽ�ˤ����ꤵ�줿���پ���
*	@param	Integer		$lngMonetaryUnitCode	�̲�ñ�̥�����
*	@param  Object		$objDB					DB���֥�������
*	@return Boolean 	TRUE					�¹�����
*						FALSE					�¹Լ���
*	@access public
*/
function fncCheckSetProduct ( $aryOrderDetail, $lngMonetaryUnitCode, $objDB )
{
	// �̲�ñ�̤�����
	if ( $lngMonetaryUnitCode == "" )
	{
		$lngMonetaryUnitCode = DEF_MONETARY_YEN;
	}

	// ���ʲ��ʥޥ����ؤΥǡ�����Ͽ
	for( $i = 0 ; $i < count( $aryOrderDetail ); $i++ )
	{
		// ������ʬ������
		$lngConversionClassCode = ( $aryOrderDetail[$i]["lngConversionClassCode"] == "gs" ) ? 1 : 2;
		// �����ֹ�
		$lngProductNo = intval($aryOrderDetail[$i]["strProductCode"]);
		// �������ܥ�����
		$lngStockSubjectCode = $aryOrderDetail[$i]["strStockSubjectCode"];
		// �������ʥ�����
		$lngStockItemCode = $aryOrderDetail[$i]["strStockItemCode"];
		// ���ʲ���
		$curProductPrice = $aryOrderDetail[$i]["curProductPrice"];
		if ( $curProductPrice == "" )
		{
			$curProductPrice == 0;
		}
		// ��������
		$strDetailNote = $aryOrderDetail[$i]["strDetailNote"];
		if ( $strDetailNote == "null" )
		{
			$strDetailNote = "";
		}

		// ���ٹԥǡ���������å����������ٹԤξ�������ʲ��ʥޥ��������Ƥˤʤ���Τ���Ͽ����
		// �оݤ�����ñ�̷׾�ξ��Τ�
		if ( $lngConversionClassCode == DEF_CONVERSION_SEIHIN )
		{
			$checkFlag = FALSE;
			$strCheckQuery = "SELECT lngProductPriceCode FROM m_ProductPrice WHERE lngProductNo = " . $lngProductNo
				. " AND lngStockSubjectCode = " . $lngStockSubjectCode . " AND lngStockItemCode = " . $lngStockItemCode
				. " AND lngMonetaryUnitCode = " . $lngMonetaryUnitCode . " AND curProductPrice = " . $curProductPrice;
			// �����å������꡼�μ¹�
			list ( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

			if ( $lngCheckResultNum )
			{
				$checkFlag = TRUE;
			}
			$objDB->freeResult( $lngCheckResultID );

			// ��Ͽ����Ƥ������ʲ��ʤ��ʤ����Ͽ��������ʲ��ʥޥ�������Ͽ����
			if ( $checkFlag == FALSE )
			{
				// m_ProductPrice�Υ������󥹤����
				$sequence_m_productprice = fncGetSequence( 'm_ProductPrice.lngProductPriceCode', $objDB );

				unset( $aryQuery );

				$aryQuery[] = "INSERT INTO m_ProductPrice (";
				$aryQuery[] = "lngProductPriceCode, ";												// ���ʲ��ʥ����� 
				$aryQuery[] = "lngProductNo,";														// �����ֹ�
				$aryQuery[] = "lngStockSubjectCode,";												// �������ܥ�����
				$aryQuery[] = "lngStockItemCode,";													// �������ʥ����� 
				$aryQuery[] = "lngMonetaryUnitCode,";												// �̲�ñ�̥�����
				$aryQuery[] = "curProductPrice ";													// ���ʲ��� 
				$aryQuery[] = ") VALUES (";
				$aryQuery[] = $sequence_m_productprice . ", ";										// ���ʲ��ʥ�����
				$aryQuery[] = $lngProductNo . ", ";												// �����ֹ�
				$aryQuery[] = $lngStockSubjectCode . ", ";											// �������ܥ�����
				$aryQuery[] = $lngStockItemCode . ", ";												// �������ʥ�����
				$aryQuery[] = $lngMonetaryUnitCode . ", ";											// �̲�ñ�̥�����
				$aryQuery[] = $curProductPrice;														// ���ʲ���
				$aryQuery[] = ")";

				$strQuery = "";
				$strQuery = implode("\n", $aryQuery );

				if ( !$lngResultID = $objDB->execute( $strQuery ) )
				{
					fncOutputError ( 9051, DEF_ERROR, "���ʲ��ʥޥ����ؤ���Ͽ�����˼��Ԥ��ޤ�����", TRUE, "", $objDB );
					return FALSE;
				}
				$objDB->freeResult( $lngResultID );
			}
		}

		// ���ٹԥǡ���������å����������ٹԤξ�������ʥơ��֥�ˤʤ��ǡ��������ʥơ��֥����Ͽ����
		$checkFlag = FALSE;
		$strCheckQuery = "SELECT lngProductSubNo FROM t_Product WHERE lngProductNo = " . $lngProductNo
			. " AND lngStockSubjectCode = " . $lngStockSubjectCode . " AND lngStockItemCode = " . $lngStockItemCode;
		// �������ʤ� ���� ����¾ �ξ������ٹԤˤĤ��Ƥ���Ӥ���
		if ( $lngStockItemCode == 99 )
		{
			$strCheckQuery .= " AND strNote = '" . $strNote . "'";
		}
		// �����å������꡼�μ¹�
		list ( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

		if ( $lngCheckResultNum )
		{
			$checkFlag = TRUE;
		}
		$objDB->freeResult( $lngCheckResultID );

		// ��Ͽ����Ƥ������ʾ��󤬤ʤ����Ͽ��������ʥơ��֥����Ͽ����
		if ( $checkFlag == FALSE )
		{
			// Ʊ�����ʥ����ɤ��Ф��ư�դˤʤ�褦�˥�å��򤫤���
			$strLockQuery = "SELECT lngProductNo, lngProductSubNo "
				. "FROM t_Product WHERE lngProductNo = " . $lngProductNo
				. " FOR UPDATE";

			// ��å������꡼�μ¹�
			list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );

			$lngMaxProductSubNo = 0;
			if ( $lngLockResultNum )
			{
				for ( $i = 0; $i < $lngLockResultNum; $i++ )
				{
					$objResult = $objDB->fetchObject( $lngLockResultID, $i );
					if ( $lngMaxProductSubNo < $objResult->lngproductsubno )
					{
						$lngMaxProductSubNo = $objResult->lngproductsubno;
					}
				}
			}
			$objDB->freeResult( $lngLockResultID );

			// ���ʥ����ֹ�
			$lngMaxProductSubNo++;

			unset( $aryQuery );
			$aryQuery[] = "INSERT INTO t_Product (";
			$aryQuery[] = "lngProductSubNo, ";							// ���ʥ����ֹ�
			$aryQuery[] = "lngProductNo,";								// �����ֹ�
			$aryQuery[] = "lngStockSubjectCode,";						// �������ܥ�����
			$aryQuery[] = "lngStockItemCode";							// �������ʥ����� 
			if ( $lngStockItemCode == 99 )
			{
				$aryQuery[] = ", strNote";								// ����
			}
			$aryQuery[] = ") VALUES (";
			$aryQuery[] = $lngMaxProductSubNo . ", ";					// ���ʥ����ֹ�
			$aryQuery[] = $lngProductNo . ", ";						// �����ֹ�
			$aryQuery[] = $lngStockSubjectCode . ", ";					// �������ܥ�����
			$aryQuery[] = $lngStockItemCode;							// �������ʥ�����
			if ( $lngStockItemCode == 99 )
			{
				$aryQuery[] = ", '" . $strDetailNote . "'";				// ����
			}
			$aryQuery[] = ")";

			$strQuery = "";
			$strQuery = implode("\n", $aryQuery );

			if ( !$lngResultID = $objDB->execute( $strQuery ) )
			{
				fncOutputError ( 9051, DEF_ERROR, "���ʥơ��֥�ؤ���Ͽ�����˼��Ԥ��ޤ�����", TRUE, "", $objDB );
				return FALSE;
			}
			$objDB->freeResult( $lngResultID );
		}
	}

	return TRUE;
}

//2007.07.23 matsuki update start
function fncPayConditionCodeMatch($aryData , $aryHeadColumnNames , $aryPoDitail , $objDB )
{
	//$objDB          = new clsDB();
	//$objAuth        = new clsAuth();
	$cnt = count( $aryPoDitail);
	$strPayConditionTable = "";//��ǧ���̤ˤ������ʧ�����ʬ��htmlʸ��
	$flgPayConditionsMatch = true;
	$flgForeignTable = false;//�����ơ��֥��¸�ߤ��뤫�ɤ���
		
	
	//�ؿ���Ǥλ�ʧ����Ƚ��
	$arystockitemcode = array("1", "2", "3" ,"7","9","11");
	//$bytcompanyforeignflag	= fncGetMasterValue( "m_company", "strcompanydisplaycode", "bytcompanyforeignflag", $aryData["lngCustomerCode"] , '', $objDB);
	$strCountryCode = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcountrycode", $aryData["lngCustomerCode"] . ":str", '', $objDB);
				
				
	//2008.02.21 matsuki update start
	//$bytcompanyforeignflag�������$strCountryCode��81(����)�Ǥʤ��������褬�������ɤ����δ��Ȥ���
	//�����Υ����ƥ�Ǥ�$bytcompanyforeignflag����Ѥ��Ƥ����Τǡ������Ǥϴ�ñ�Τ���
	//�ʲ��Τ褦�˵��Ҥ���
	$bytcompanyforeignflag = "";
	if( $strCountryCode != 81)//��ȥ�����81�ʳ��ϳ��������˳���
		$bytcompanyforeignflag = "t";
	//2008.02.21 matsuki update end
	
	if( $bytcompanyforeignflag == "t"){//����賤���ե饰true{
		//�ʲ���������Ͽ������
				
		//2007.12.14 matsuki update start
				
		$aryforeigntable = fncSetForeignTabel();
		for( $i = 0; $i < count( $aryforeigntable ) ; $i++ ){
			if( $aryforeigntable[$i] == $aryData["lngCustomerCode"] ){	//�����ξ���������Ͽ������å�
				$flgForeignTable = true;
				break;
			}
		}
		
		if($flgForeignTable){
			for( $i = 0; $i < $cnt; $i++ ){
				$Code[$i]= "2";//�����2(T/T)�˥��å�
				if(  $aryPoDitail[$i]["strStockSubjectCode"] == "402"){
					for( $j = 0; $j < count( $arystockitemcode ); $j++ ){
							
						if( $aryPoDitail[$i]["strStockItemCode"] == $arystockitemcode[$j]){
							$Code[$i]="1";//L/C�˥��å�
							break;
						}
					}
				}
			}
		}
		
		else{//�����ơ��֥��¸�ߤ��ʤ�ȯ����ξ�硢�侩������ʧ����������T/T
			for( $i = 0; $i < $cnt; $i++ )
				$Code[$i]= "2";
		}
	
				
		if ( $aryData["lngMonetaryUnitCode"] == 'US$' ){//US�ɥ�ξ��˸¤�
			if( $Code[0] == "1" && $aryData["curAllTotalPrice"] < 30000 ){//1���ܤ��������ƤΤߤ���侩�����ʧ������ �嵭���������Ƥ��뤬��׶�ۤ���­���Ƥ���
					$Code[0]="2";
			}
		}
		else $Code[0]="2";//US�ɥ�ξ��ʳ���T/T�侩
			
		if( $cnt >1 )//���٤�1��ʾ夢����
			for ( $i = 0; $i < $cnt; $i++ ){
				if ($Code[$i] == $Code[0] ){//���Ƥ����٤�1���ܤȰ��פ��Ƥ��뤫
					$flgPayConditionsMatch = false;
					break;
				}
			}
					
								
		//�桼���������ꤷ����ʧ����ȯ����ˤ�ä�Ƚ�Ǥ��줿��ʧ����郎�ޥå����Ƥ뤫�ɤ���
		$flgPayConditionCodeMatch = ( $aryData["lngPayConditionCode"] == $Code[0] )? true:false;
		if ($flgPayConditionCodeMatch == true && $flgPayConditionsMatch == true ){
		//��ʧ����郎�ޥå����Ƥ���Τǥե���������
			$frmPayConditionTable = $aryData["strPayConditionName"];
		}
		else{
					
			$strhtml= fncPulldownMenu( 2, 0, '', $objDB );//���������selected ���ΤȤ���onload��Ŭ�����ꤷ�Ƥ���
			$frmPayConditionTable = '<span id="VarsA10">
						<select name="lngPayConditionCode" tabindex="3" onchange = "fncPayConditionFrmChanged();">
							'.$strhtml.
						'</select></span>';
			
			if ($flgPayConditionCodeMatch == false && $flgPayConditionsMatch == true ){//���򤷤Ƥ����ʧ����郎�ְ�äƤ��뤬���Ƥ����٤ϰ���
				$strPayMode = "0";
			}
					
			else if ( $flgPayConditionCodeMatch == true && $flgPayConditionsMatch == false ){//���򤷤Ƥ����ʧ�����Ϲ��פ��Ƥ��뤬���Ƥ����٤����פ���
						$strPayMode = "1";
			}
			
			else {
				$strPayMode = "2";//���򤷤Ƥ����ʧ�����Ϲ��פ��Ƥ��餺����������Ƥ����٤����פ���
			}	
			//body��Onload�ؿ��˴ؿ����ɲ�
			$aryData["strOnloadfnc"] = "fncPayConditionConfirm( '".$strPayMode."' , '".$aryData["lngPayConditionCode"]."' , '".$Code[0]."' )";
				
		}
			
	}
	else //���������Ǥʤ����Ͼ嵭�ν�����Ԥ�ʤ�
		$frmPayConditionTable = $aryData["strPayConditionName"];
			
	//2007.12.14 matsuki update end

	$aryData["strPayConditionTable"]='<tr> 
										<td id="PayCondition" class="SegColumn">'.$aryHeadColumnNames["CNlngPayConditionCode"].'</td>
										<td class="Segs">'.$frmPayConditionTable.'<span id="strRecommendPayCondition"></span></td>
									</tr>';
		
	return $aryData;
	
}

//2007.12.14 matsuki update start
function fncSetForeignTabel(){
	
	$aryforeigntable = array(
							1111,
							1112,
							1113,
							1117,
							1119,
							1211,
							1303,
							1304,
							2106,
							2107,
							2207,
							2208,
							2209,
							2215,
							2305,
							2307,
							2308,
							3205,
							3207,
							3209,
							3210,
							3217,
							3220,
							3223,
							3504,
							4202,
							4510,
							4511,
							4512,
							4516,
							5209,
							6106,
							6107,
							6115,
							6117,
							6118,
							6127,
							6318,
							6139,
							6311,
							6320,
							6403,
							6404,
							6505,
							6507,
							8301,
							9101,
							9102,
							9103,
							9104,
							9403,
							9995);
	
	return $aryforeigntable;
}
//��ҥ�����8310��9402�Ϥ��Υ롼���Ŭ�Ѥ��ʤ���
//�ǽ��Խ���2012ǯ3��8����
//8301��롼����ɲá�//2012ǯ6��25��

//2007.12.14 matsuki update end
?>