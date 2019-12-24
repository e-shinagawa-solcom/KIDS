<?php
/**
*       ȯ��������ؿ���
*
*       @package   kuwagata
*       @license   http://www.wiseknot.co.jp/
*       @copyright Copyright &copy; 2003, Wiseknot
*       @author    Hiroki Watanabe <h-watanabe@wiseknot.co.jp>
*       @access    public
*       @version   1.00
*
*       ��������
*       
*	��������
*
*	2004.03.02	���ٹԤΥ����å��ؿ�����ñ������ȴ����ۤ� 0�� �׾塢�ޥ��ʥ��ͷ׾��ǧ���褦�˽���
*	2004.03.25	fncDetailHidden�ؿ������ٹ��ֹ�ˤĤ��Ƥ��Ϥ��褦�˽���
*
*/

/*

fncDiscodeToCode��case3�ν����򤷤Ƥ��ʤ�
fncDiscodeToCode�ͤ��ʤ�����null��(�ƥ����ѤʤΤǽ���ä���ä���
fncCheckData�ٻ�ñ���Ȳٻ�ñ�̤Υ����å��򤷤Ƥʤ���option�ͤ���������Ƥ��ʤ�����
fnccheck�δ����졼��disable����post����Ƥʤ��ΤǤ�����

*/
	// �ɤ߹���
	// -----------------------------------------------------------------
	/**		fncDetailError()�ؿ�
	*
	*
	*		@param String	bytErrorFlag2		// �����ֹ�
	*		@return	STring	$strDetailErrorMessage			//
	*/
	// -----------------------------------------------------------------
	
	function fncDetailError( $bytErrorFlag2 )
	{
	
		// ���顼������С�TRUE�פ��֤äƤ���ΤǤ��ιԿ��ֹ��Ͽ
		for( $i = 0; $i < count( $bytErrorFlag2 ); $i++ ) 
		{
			if( $bytErrorFlag2[$i] == TRUE )
			{
				$aryNumber[] = $i+1;
			}
		}
		
		
		if( is_array( $aryNumber ) )
		{
			
			for( $i = 0; $i < count( $aryNumber ); $i++ )
			{
				$aryDetailErrorMessage[] = "���ٹ� ".$aryNumber[$i]."���ܡ����顼 ";
			}
		}
		
		if( is_array( $aryDetailErrorMessage ) )
		{
			$strDetailErrorMessage = implode( " : ", $aryDetailErrorMessage );
			//echo "strDetailErrorMessage : $strDetailErrorMessage<br>";
			
		}
		else
		{
			$strDetailErrorMessage = "";
		}
		
		return $strDetailErrorMessage;
	
	}
	
	
	
	
	
	
	// -----------------------------------------------------------------
	/**		funPulldownMenu()�ؿ�
	*
	*		�ץ�������˥塼������
	*
	*		@param Long		$lngProcessNo		// �����ֹ�
	*		@param Long		$lngValueCode		// value��
	*		@param String	$strWhere			// ���
	*		@param Object	$objDB				// DB��³���֥�������
	*		@return	Array	$strPulldownMenu
	*/
	// -----------------------------------------------------------------
	
	
	function fncPulldownMenu ( $lngProcessNo, $lngValueCode , $strWhere, $objDB )
	{

		switch ( $lngProcessNo )
		{
			case 0:		// �̲�
				$strPulldownMenu = fncGetPulldown3( "m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $lngValueCode, $strWhere, $objDB );
				break;
			case 1:		// �졼�ȥ�����
				$strPulldownMenu = fncGetPulldown( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $lngValueCode, '', $objDB );
				break;
			case 2:		// ��ʧ���
				$strPulldownMenu = fncGetPulldown( "m_paycondition", "lngpayconditioncode", "strpayconditionname", $lngValueCode, $strWhere, $objDB );
				break;
			case 3:		// ��������
				$strPulldownMenu = fncGetPulldown2( "m_stocksubject", "lngstocksubjectcode", "strstocksubjectname", $lngValueCode, 'WHERE bytdisplayflag = TRUE AND bytinvalidflag = FALSE', $objDB );
				break;
			case 4:		// ��������
				$strPulldownMenu = "";
				break;
			case 5:		// ñ���ꥹ��
				$strPulldownMenu = "";
				break;
			case 6:		// ������ˡ
				$strPulldownMenu = fncGetPulldown( "m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", $lngValueCode,'', $objDB );
				break;
			case 7:		// ����ñ��
				$strPulldownMenu = fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $lngValueCode, "WHERE bytproductconversionflag =true", $objDB );
				break;
			case 8:		// ����ñ��
				$strPulldownMenu = fncGetPulldown("m_productunit", "lngProductUnitCode", "strProductUnitName", $lngValueCode, "WHERE bytpackingconversionflag=true", $objDB );
				break;
			case 9:		// ȯ�����
				$strPulldownMenu = fncGetPulldown( "m_OrderStatus", "lngorderstatuscode", "strorderstatusname", $lngValueCode,'', $objDB );
				break;
			case 10:	// ����ʬ(�����
				$strPulldownMenu = fncGetPulldown( "m_salesclass", "lngsalesclasscode", "strsalesclassname", $lngValueCode,'', $objDB );
				break;
		}
		return $strPulldownMenu;
	}
	
	
	
	// -----------------------------------------------------------------
	/**		fncGetPulldown2()�ؿ���lib/lib.php�򥳥ԡ����ƻ��Ѥˡ�����
	*		lib/lib.php���������ˤ������٤����ꤽ���ʤΤǤ��������̤˺��ޤ�����
	*		�ץ�������ɽ���� name�����Ǥʤ�code�����ɽ��
	*		�ץ�������˥塼������
	*
	*/
	// -----------------------------------------------------------------
	function fncGetPulldown2( $strTable, $strValueFieldName, $strDisplayFieldName, $lngDefaultValue, $strQueryWhere, $objDB )
	{
	        // ���ڡ���ID�Υꥹ�Ȥ����
	        $strQuery = "SELECT $strValueFieldName, $strDisplayFieldName FROM $strTable $strQueryWhere ORDER BY $strValueFieldName";
	        
	        
			// �����꡼�¹� =====================================
			if ( !$lngResultID = $objDB->execute( $strQuery ) )
			{
				echo "�����꡼���顼";
				//fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
			}
			
			
			// �ͤ�Ȥ� =====================================
			if( $lngResultNum = pg_num_rows ( $lngResultID ) )
			{
				for( $i = 0; $i < $lngResultNum; $i++ )
				{
					$aryResut[] = pg_fetch_array( $lngResultID, $i, PGSQL_ASSOC );
				}
			}
			

	        if ( !$lngResultNum )
	        {
	                return FALSE;
	        }


	        // <OPTION>����
	        for ( $count = 0; $count < $lngResultNum; $count++ )
	        {
	                $aryResult = $objDB->fetchArray( $lngResultID, $count );

	                // HTML����
	                if ( $lngDefaultValue == $aryResult[0] )
	                {
	                        //$strHtml .= "<OPTION VALUE=\"$aryResult[$strValueFieldName]&nbsp;$aryResult[$strDisplayFieldName]\" SELECTED>".$aryResult[$strValueFieldName]."&nbsp;&nbsp;".$aryResult[$strDisplayFieldName]."</OPTION>\n";
	                        $strHtml .= "<OPTION VALUE=\"$aryResult[$strValueFieldName]\" SELECTED>".$aryResult[$strValueFieldName]."&nbsp;".$aryResult[$strDisplayFieldName]."</OPTION>\n";
	                }
	                else
	                {
	                        //$strHtml .= "<OPTION VALUE=\"$aryResult[$strValueFieldName]&nbsp;$aryResult[$strDisplayFieldName]\">".$aryResult[$strValueFieldName]."&nbsp;&nbsp;".$aryResult[$strDisplayFieldName]."</OPTION>\n";
	                        $strHtml .= "<OPTION VALUE=\"$aryResult[$strValueFieldName]\">".$aryResult[$strValueFieldName]."&nbsp;".$aryResult[$strDisplayFieldName]."</OPTION>\n";
	                }
	        }

	        $objDB->freeResult( $lngResultID );
	        return $strHtml;
	}


	// -----------------------------------------------------------------
	/**		fncGetPulldown3()�ؿ���lib/lib.php�򥳥ԡ����ƻ��Ѥˡ��������̲����ѡ�
	*		lib/lib.php���������ˤ������٤����ꤽ���ʤΤǤ��������̤˺��ޤ�����
	*		�̲ߤΥץ���������value�ͤ�code�ǤϤʤ�<option value="\">���ܱ�</option>
	*		���λ���code�ǥ����Ȥ�������
	*		�ץ�������˥塼������
	*
	*/
	// -----------------------------------------------------------------
	function fncGetPulldown3( $strTable, $strValueFieldName, $strDisplayFieldName, $lngDefaultValue, $strQueryWhere, $objDB )
	{
		// ���ڡ���ID�Υꥹ�Ȥ����
		$strQuery = "SELECT $strValueFieldName, $strDisplayFieldName FROM $strTable $strQueryWhere ORDER BY lngmonetaryunitcode";

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if ( !$lngResultNum )
		{
			return FALSE;
		}

		$lngFieldsCount = $objDB->getFieldsCount( $lngResultID );
		// <OPTION>����
		for ( $count = 0; $count < $lngResultNum; $count++ ) {
			$aryResult = $objDB->fetchArray( $lngResultID, $count );

			$strDisplayValue = "";
			for ( $i = 1; $i < $lngFieldsCount; $i++ )
			{
				$strDisplayValue .= "$aryResult[$i]";
			}

			// HTML����

			if ( $lngDefaultValue == $aryResult[0] )
			{
				$strHtml .= "<OPTION VALUE=\"$aryResult[0]\" SELECTED>$strDisplayValue</OPTION>\n";
			}
			else
			{
				$strHtml .= "<OPTION VALUE=\"$aryResult[0]\">$strDisplayValue</OPTION>\n";
			}
		}

		$objDB->freeResult( $lngResultID );
		return $strHtml;
	}


	// -----------------------------------------------------------------
	/**		fncDiscodeToCode()�ؿ�
	*
	*		displayCode��Code
	*
	*		@param Long		$strColumnName		// �����̾
	*		@param Long		$lngValueCode		// value��
	*		@param Object	$objDB				// DB��³���֥�������
	*		@return	long	$lngCode			//
	*/
	// -----------------------------------------------------------------
	
	function fncDiscodeToCode ( $strColumnName, $strDisplayCode , $objDB )
	{
		switch ( $strColumnName )
		{
			case 'lnginchargegroupcode':	// ���롼�ץ����ɡ������
				$lngCode = fncGetMasterValue("m_group", "strgroupdisplaycode", "lnggroupcode", $strDisplayCode . ":str",'',$objDB);
				$lngCode = ( $lngCode != "") ? $lngCode : "null";
				break;
			case 'lnginchargeusercode':		// �桼�������ɡ�ô���ԡ�
				$lngCode = fncGetMasterValue("m_user", "struserdisplaycode" ,"lngusercode" , $strDisplayCode . ":str",'',$objDB);
				$lngCode = ( $lngCode != "" ) ? $lngCode : "null";
				break;
			case 'lnglocationcode':			// Ǽ�ʾ��
				$lngCode = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", $strDisplayCode . ":str", '',$objDB);
				$lngCode = ( $lngCode != "" ) ? $lngCode : "null";
				break;
			case 'lngcustomercode':			//��ҥ�����(������)
				$lngCode = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", $strDisplayCode . ":str", '',$objDB);
				$lngCode = ( $lngCode != "" ) ? $lngCode : "null";
				break;
		}
		
		return $lngCode;
	
	}



	// -----------------------------------------------------------------
	/**		fncCodeToDisplayCode()�ؿ�
	*
	*		Code��displayCode
	*
	*		@param String	$strValue			// code�����̾
	*		@param Long		$lngCode			// code��
	*		@param Object	$objDB				// DB��³���֥�������
	*		@return	String	$strDisplayCode		//
	*/
	// -----------------------------------------------------------------
	
	function fncCodeToDisplayCode ( $strValue , $lngCode , $objDB )
	{
		if( $strValue == "lnginchargegroupcode" )
		{
			// ���롼�ץ����ɡ������
			$strDisplayCode = fncGetMasterValue("m_group", "lnggroupcode", "strgroupdisplaycode || ',' || strgroupdisplayname", $lngCode,'',$objDB);
		}
		elseif( $strValue == "lnginchargeusercode")
		{
			// �桼�������ɡ�ô���ԡ�
			$strDisplayCode = fncGetMasterValue("m_user", "lngusercode", "struserdisplaycode || ',' || struserdisplayname", $lngCode,'',$objDB);
		}
		elseif( $strValue == "lnglocationcode" )
		{
			// Ǽ�ʾ��
			$strDisplayCode = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplaycode || ',' || strcompanydisplayname", $lngCode, '',$objDB);
		}
		else
		{
			//��ҥ�����(������)
			$strDisplayCode = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplaycode || ',' || strcompanydisplayname", $lngCode, '',$objDB);
		}

		return $strDisplayCode;
	
	}

	// -----------------------------------------------------------------
	/**		fncCodeToDisplayCode()�ؿ�
	*
	*		Code��displayCode
	*
	*		@param String	$strValue			// code�����̾
	*		@param Long		$lngCode			// code��
	*		@param Object	$objDB				// DB��³���֥�������
	*		@return	String	$strDisplayCode		//
	*/
	// -----------------------------------------------------------------
	
	function fncDisCodeToDisplayName ( $strValue , $strCode , $objDB )
	{
		if( $strValue == "lnginchargegroupcode" )
		{
			// ���롼�ץ����ɡ������
			$strDisplayCode = fncGetMasterValue("m_group", "strgroupdisplaycode", "strgroupdisplayname", $strCode. ":str",'',$objDB);
		}
		elseif( $strValue == "lnginchargeusercode")
		{
			// �桼�������ɡ�ô���ԡ�
			$strDisplayCode = fncGetMasterValue("m_user", "struserdisplaycode", "struserdisplayname", $strCode. ":str",'',$objDB);
		}
		elseif( $strValue == "lnglocationcode" )
		{
			// Ǽ�ʾ��
			$strDisplayCode = fncGetMasterValue("m_company", "strcompanydisplaycode", "strcompanydisplayname", $strCode. ":str", '',$objDB);
		}
		else
		{
			//��ҥ�����(������)
			$strDisplayCode = fncGetMasterValue("m_company", "strcompanydisplaycode", "strcompanydisplayname", $strCode. ":str", '',$objDB);
		}

		return $strDisplayCode;
	
	}



	// -----------------------------------------------------------------
	/**		fncChangeData()�ؿ�
	*
	*		$_POST�Υǡ�����displayCode���Ϥ��äƤ�����Code���Ѵ���
	*		�������ľ��
	*
	*		���󥵡����� ���ǡ�����null���������롣
	*		
	*		����$aryDisplayCode���ֹ��fncDiscodeToCode�Υץ���ID��Ʊ��
	*
	*		@param	Array	$aryData			// value��
	*		@param	String	$strNull			// �ͤ��ʤ�����null���Ѵ������뤫
	*		@param	Object	$objDB				// DB��³���֥�������
	*		@return	long	$lngCode			//
	*/
	// -----------------------------------------------------------------
	
	
	function fncChangeData( $aryData, $objDB  )
	{
	
//		$aryDisplayCode[0] = "lngInChargeGroupCode";		// ���롼�ץ����ɡ������
//		$aryDisplayCode[1] = "lngInChargeUserCode";			// �桼�������ɡ�ô���ԡ�
//		$aryDisplayCode[3] = "lngLocationCode";				// Ǽ�ʾ��
//		$aryDisplayCode[4] = "lngCustomerCode";				// ��ҥ�����(������)
		$aryDisplayCode["lnglocationcode"]			= "strLocationName";			// Ǽ�ʾ��
		$aryDisplayCode["lngcustomercode"]			= "strCustomerName";			// ��ҥ�����(������)

		for( $i = 0; $i < count( $aryData ); $i++ )
		{
			list( $strKeys, $strValues ) = each( $aryData );
			
			reset( $aryDisplayCode );
			for ( $j = 0; $j < count( $aryDisplayCode ); $j++ )
			{
				if( $strKeys == $aryDisplayCode[$j] )
				{
					$aryNewData[$strKeys] = fncDiscodeToCode( $strKeys, $strValues , $objDB );
					break;
				}
			}
			
			if( $j == count( $aryDisplayCode ) )
			{
				$strValues = ($strValues == "") ? "null" : $strValues;
				$aryNewData[$strKeys] = $strValues;
			}
		}
		
		return $aryNewData;
	}



	// -----------------------------------------------------------------
	/**		fncChangeData2()�ؿ�
	*
	*		���������ѤΥǡ����Ѵ�
	*		SQL�����ͤ�ȤäƤ����Ȥ��˻Ȥ�
	*		code����dispalaycode��displayName�����
	*		POST�ͤ�����ξ��Ϥ��Τޤ�
	*		
	*		����$aryDisplayCode���ֹ��fncDiscodeToCode�Υץ���ID��Ʊ��
	*
	*		@param	Array	$aryData			// value��
	*		@param	String	$strNull			// �ͤ��ʤ�����null���Ѵ������뤫
	*		@param	Object	$objDB				// DB��³���֥�������
	*		@return	long	$lngCode			//
	*/
	// -----------------------------------------------------------------
	
	
	// ������ displaycode�����ä���dispalayname������
	function fncChangeData2( $aryData, $objDB )
	{
	
//		$aryDisplayCode["lnginchargegroupcode"]		= "strInChargeGroupName";		// ���롼�ץ����ɡ������
//		$aryDisplayCode["lnginchargeusercode"]		= "strInChargeUserName";		// �桼�������ɡ�ô���ԡ�
		$aryDisplayCode["lnglocationcode"]			= "strLocationName";			// Ǽ�ʾ��
		$aryDisplayCode["lngcustomercode"]			= "strCustomerName";			// ��ҥ�����(������)
		
		for( $i = 0; $i < count( $aryData ); $i++ )
		{
			list( $strKeys, $strValues ) = each( $aryData );
			
			reset( $aryDisplayCode );
			for ( $j = 0; $j < count( $aryDisplayCode ); $j++ )
			{
				list ( $strKeys2, $strValues2 ) = each( $aryDisplayCode );
				
				if( strcasecmp($strKeys, $strKeys2) == 0 )
				{
					$strDisplayValue = fncCodeToDisplayCode($strKeys2, $strValues, $objDB );
					
					$aryDisplayValue = array();
					$aryDispalyValue = explode(',', $strDisplayValue);
			
					$aryNewData[$strKeys2] = $aryDispalyValue[0];
					$aryNewData[$strValues2] = $aryDispalyValue[1];
					break;
				}
			}
			
			if( $j == count( $aryDisplayCode ) )
			{
				// ������
				$aryNewData[$strKeys] = $strValues;
			}
		}
		
		return $aryNewData;
	}
		
	// -----------------------------------------------------------------
	/**		fncChangeData3()�ؿ�
	*
	*		���顼����ä���硦�����ץܥ������ä����˻Ȥ�
	*		dispalaycode����displayName�����
	*		POST�ͤ�����ξ��Ϥ��Τޤ�
	*		
	*		����$aryDisplayCode���ֹ��fncDiscodeToCode�Υץ���ID��Ʊ��
	*
	*		@param	Array	$aryData			// value��
	*		@param	String	$strNull			// �ͤ��ʤ�����null���Ѵ������뤫
	*		@param	Object	$objDB				// DB��³���֥�������
	*		@return	long	$lngCode			//
	*/
	// -----------------------------------------------------------------
	
	
	// ������ displaycode�����ä���dispalayname������
	function fncChangeData3( $aryData, $objDB  )
	{
	
//		$aryDisplayCode["lnginchargegroupcode"]		= "strInChargeGroupName";		// ���롼�ץ����ɡ������
//		$aryDisplayCode["lnginchargeusercode"]		= "strInChargeUserName";		// �桼�������ɡ�ô���ԡ�
		$aryDisplayCode["lnglocationcode"]			= "strLocationName";			// Ǽ�ʾ��
		$aryDisplayCode["lngcustomercode"]			= "strCustomerName";			// ��ҥ�����(������)
		
		for( $i = 0; $i < count( $aryData ); $i++ )
		{
			list( $strKeys, $strValues ) = each( $aryData );
			
			reset( $aryDisplayCode );
			for ( $j = 0; $j < count( $aryDisplayCode ); $j++ )
			{
				list ( $strKeys2, $strValues2 ) = each( $aryDisplayCode );
				
				if( strcasecmp($strKeys, $strKeys2) == 0 )
				{
					$strDisplayName = fncDisCodeToDisplayName($strKeys2, $strValues, $objDB );
			
					//2007.08.10 matsuki update start
					/*
					strcasecmp��$aryData���������Ǥ�$aryDisplayCode���������ǤΥޥå��󥰤���ʸ����ʸ��̵��ǹԤ���
					�ޥå����Ƥ����fncDisCodeToDisplayName�ؿ��ǡ������ɤ˳�������ʸ�����DB���黲�Ȥ��Ƥ��롣
					�����ǡ�fncDisCodeToDisplayName��Ǥ�������$strKeys2��caseʸ�����Ѥ���ʬ����ԤäƤ���Τǡ�
					����������ʸ���Τߤ�ʸ����(lngcustomercode�ʤ�)�Ǥʤ��ȴؿ�������Ω���ʤ���
					�����ޤǤ��ɤ��Τ�����
					
					��ʸ���Ǥϰʾ��³����
					
					$aryNewData[$strKeys2] = $strValues;
					$aryNewData[$strValues2] = $strDisplayName;
					
					�ȤʤäƤ��롣
					�Ĥޤ�
					$aryNewData["lnglocationcode"]��$aryNewData["lngcustomercode"]���ͤ���Ǽ����롣
					����$aryData�Ǥ���ʸ����ޤ�ɽ��ˡ�Ǥ��ꡢ
					$aryData["lngCustomerCode"]��¸�ߤ��ʤ���ΤȤʤäƤ��ޤ���
					�ʤΤǰʲ��Τ褦�˽�����
					*/
					$aryNewData[$strKeys] = $strValues;
					$aryNewData[$strValues2] = $strDisplayName;
					//2007.08.10 matsuki update end
					

					break;
				}
			}
			
			if( $j == count( $aryDisplayCode ) )
			{
				
				$aryNewData[$strKeys] = $strValues;
			}
		}
		
		return $aryNewData;
	}

	// -----------------------------------------------------------------
	/**		fncCheckData_po()�ؿ�
	*
	*		submit���줿�ǡ���������å�����
	*
	*		@param Array	$aryData			// submit���줿��
	*		@param Object	$objDB				// DB��³���֥�������
	*		@return	Array	$aryError
	*/
	// -----------------------------------------------------------------

	function fncCheckData_po( $aryData, $strPart, $objDB )
	{
		if($strPart == "header")
		{
		
			$aryCheck["dtmOrderAppDate"]				= "null:date";			// �׾���
			$aryCheck["strOrderCode"]					= "";				// ȯ��No
			$aryCheck["lngCustomerCode"]				= "null";			// ������
			//$aryCheck["lngInChargeGroupCode"]			= "null";			// ���祳����
			//$aryCheck["lngInChargeUserCode"]			= "null";			// ô����
			$aryCheck["lngLocationCode"]				= "null";			// Ǽ�ʾ��
			$aryCheck["dtmExpirationDate"]				= "null:date";		// ȯ��ͭ��������
			$aryCheck["lngOrderStatusCode"]				= "";				// ����(���ץ������)
			$aryCheck["lngMonetaryUnitCode"]			= "null";			// �̲�


			if( $aryData["lngMonetaryUnitCode"] != DEF_MONETARY_CODE_YEN )	//�̲ߤ����ܰʳ�
			{
				//$aryCheck["lngMonetaryRateCode"]		= "number(0,99)";	// �졼�ȥ�����
				//$aryCheck["curConversionRate"]			= "null";			// �����졼��

				$aryCheck["lngPayConditionCode"]	= "number(1,99,The list has not been selected.)";	// ��ʧ���

				if($_COOKIE["lngLanguageCode"])
				{
					$aryCheck["lngPayConditionCode"] = "number(1,99,�ꥹ�Ȥ����򤵤�Ƥ��ޤ���)";
				}
			}
		}
		else
		{
			$aryCheck["strProductCode"]					= "null";				// ����
			$aryCheck["strStockSubjectCode"]			= "number(1,999999999)";				// ��������
			$aryCheck["strStockItemCode"]				= "number(1,999999999)";				// ��������
			$aryCheck["lngConversionClassCode"]			= "null";				// ����ñ�̷׾�
			$aryCheck["lngProductUnitCode"]				= "null";				// �ٻ�ñ��
			$aryCheck["lngGoodsQuantity"]				= "null";				// ���ʿ���
			$aryCheck["curTotalPrice"]					= "null:money(0,99999999999999)";				// ��ȴ���
			$aryCheck["dtmDeliveryDate"]				= "null";

		}
		
		// �����å��ؿ��ƤӽФ�
		$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
		
		// print_r( $aryCheckResult );
		list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );
		return array ( $aryData, $bytErrorFlag );
	
	}
	
	
	
	// -----------------------------------------------------------------
	/**		fncWorkFlow()�ؿ�
	*
	*		��ǧ�롼�Ȥθ���
	*
	*		@param String	$strUserCode		// ������桼��������
	*		@param Long		$lngSelectNumber	// ��ä�����value��(selected)
	*		@param Object	$objDB				// DB��³���֥�������
	*		@return	Array	$aryError
	*/
	// -----------------------------------------------------------------
	
	
	function fncWorkFlow( $lngUserCode, $objDB ,$lngSelectNumber)
	{

		$aryQuery[] = "SELECT DISTINCT ON ( w.lngWorkflowOrderCode ) w.lngWorkflowOrderCode ";
		$aryQuery[] = "FROM m_WorkflowOrder w, m_GroupRelation gr ";
		$aryQuery[] = "WHERE gr.lngUserCode = $lngUserCode ";
		$aryQuery[] = " AND w.lngWorkflowOrderGroupCode = gr.lngGroupCode ";
		$aryQuery[] = " AND w.bytWorkflowOrderDisplayFlag = true ";
		$aryQuery[] = "EXCEPT ";
		$aryQuery[] = "SELECT DISTINCT ON ( w.lngWorkflowOrderCode ) w.lngWorkflowOrderCode ";
		$aryQuery[] = "FROM m_WorkflowOrder w, m_User u, m_AuthorityGroup ag ";
		$aryQuery[] = "WHERE w.lngInChargeCode = $lngUserCode ";
		$aryQuery[] = " OR ag.lngAuthorityLevel > ";
		$aryQuery[] = "(";
		$aryQuery[] = "  SELECT ag2.lngAuthorityLevel";
		$aryQuery[] = "  FROM m_User u2, m_AuthorityGroup ag2";
		$aryQuery[] = "  WHERE u2.lngUserCode = $lngUserCode";
		$aryQuery[] = "   AND u2.lngAuthorityGroupCode = ag2.lngAuthorityGroupCode";
		$aryQuery[] = ")";
		$aryQuery[] = " AND w.lngInChargeCode = u.lngUserCode";
		$aryQuery[] = " AND w.bytWorkflowOrderDisplayFlag = true ";
		$aryQuery[] = " AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode";
		$aryQuery[] = "GROUP BY w.lngworkflowordercode ";
		$aryQuery[] = "ORDER BY lngworkflowordercode ";

		$strQuery = implode("\n", $aryQuery );
		// echo "$strQuery<br>";
			
		// �����꡼�¹� =====================================
		
		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			echo "�����꡼���顼";
			//fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		
		$lngCount = pg_num_rows( $lngResultID );
		// ��ǧ�롼�Ȥ��ʤ����
		if ( $lngCount == 0 )
		{
			$strOptionValue = "<option value=\"0\">��ǧ�ʤ�</option>";
		}
		else
		{
			// echo "��ǧ�롼�Ȥ���<br>";
			// echo "count : $lngCount<br>";
			// lngworkflowordercode���龵ǧ�Ԥ���Ф� =====================================
			for( $i = 0; $i < $lngCount; $i++ )
			{
				$aryResult = pg_fetch_array( $lngResultID, $i, PGSQL_ASSOC );
		
				$strWorkflowOrderName = fncGetMasterValue( "m_workfloworder", "lngworkflowordercode", "strworkflowordername", $aryResult["lngworkflowordercode"] . ":str", 'lngWorkflowOrderNo = 1', $objDB);

				unset( $strSelect );
				// ��ä����ˡ�����
				if ( strcmp($lngSelectNumber ,"" ) != 0 and $aryResult["lngworkflowordercode"] == $lngSelectNumber )
				{
					$strSelect = " selected";
				}
				
				$strOptionValue .= "<option value=\"" . $aryResult["lngworkflowordercode"] . "\"$strSelect>" 
					. $strWorkflowOrderName . "</option>";
			}
		}


		return $strOptionValue;
	}
	
	
	
	// -----------------------------------------------------------------
	/**		fncDe. tailHidden()
	*
	*		������Ͽ�����������ٹԤ�hidden�ͤ��Ѵ�����
	*
	*		@param Array	$aryData			// ���ٹԤΥǡ���
	*		@param String	$strMode			// ��Ͽ�Ƚ�����Ƚ��(��ʸ����ʸ���ΰ㤤��������Ͽ��������ʸ����DB����������Ͼ�ʸ��
	*		@return	Array	$aryJScript			//
	*/
	// -----------------------------------------------------------------
	
	
	
	
	function fncDetailHidden( $aryData, $strMode, $objDB)
	{
		
		if( $strMode == "insert" )
		{
			for ($i = 0; $i < count( $aryData ); $i++ )
			{
				// ���ٹ��ֹ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngPurchaseOrderNo]\" value=\"".$aryData[$i]["lngPurchaseOrderNo"]."\">";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngPurchaseOrderDetailNo]\" value=\"".$aryData[$i]["lngPurchaseOrderDetailNo"]."\">";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngOrderNo]\" value=\"".$aryData[$i]["lngOrderNo"]."\">";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngOrderDetailNo]\" value=\"".$aryData[$i]["lngOrderDetailNo"]."\">";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngSortKey]\" value=\"".$aryData[$i]["lngSortKey"]."\">";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngRevisionNo]\" value=\"".$aryData[$i]["lngRevisionNo"]."\">";
				// ������ˡ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngCarrierCode]\" value=\"".$aryData[$i]["lngDeliveryMethodCode"]."\">";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strDeliveryMethodName]\" value=\"".mb_convert_encoding($aryData[$i]["strDeliveryMethodName"],"EUC-JP","auto")."\">";
				// ñ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductUnitCode]\" value=\"".$aryData[$i]["lngProductUnitCode"]."\">";
				// ��������
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngStockSubjectCode]\" value=\"".$aryData[$i]["lngStockSubjectCode"]."\">";
				// ��������
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strStockItemCode]\" value=\"".$aryData[$i]["strStockItemCode"]."\">";
				// �̲�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngMonetaryUnitCode]\" value=\"".$aryData[$i]["lngMonetaryUnitCode"]."\">";
				// ������
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngCustomerCompanyCode]\" value=\"".$aryData[$i]["lngCustomerCompanyCode"]."\">";
				// ñ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curProductPrice]\" value=\"".$aryData[$i]["curProductPrice"]."\">";
				// ����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductQuantity]\" value=\"".$aryData[$i]["lngProductQuantity"]."\">";
				// ��ȴ���
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curSubtotalPrice]\" value=\"".$aryData[$i]["curSubtotalPrice"]."\">";
				// Ǽ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][dtmDeliveryDate]\" value=\"".$aryData[$i]["dtmDeliveryDate"]."\">";
				// ����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strDetailNote]\" value=\"".fncHTMLSpecialChars(mb_convert_encoding($aryData[$i]["strDetailNote"],"EUC-JP","auto"))."\">";
				
				
				
				// ����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strProductCode]\" value=\"".$aryData[$i]["strProductCode"]."\">";
				//$strStockSubjectName = "";
				//$strStockSubjectName = fncGetMasterValue( "m_stocksubject", "lngstocksubjectcode", "strstocksubjectname", $aryData[$i]["strStockSubjectCode"],'', $objDB );
				//$strStockItemName = "";
				//$strStockItemName = fncGetMasterValue( "m_stockitem", "lngstockitemcode","strstockitemname" , $aryData[$i]["strStockItemCode"], "lngstocksubjectcode = ".$aryData[$i]["strStockSubjectCode"],$objDB );
				// ������ʬ������
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngConversionClassCode]\" value=\"".$aryData[$i]["lngConversionClassCode"]."\">";
				// ForList
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curProductPriceForList]\" value=\"".$aryData[$i]["curProductPriceForList"]."\">";
				// ñ���ꥹ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngGoodsPriceCode]\" value=\"".$aryData[$i]["lngGoodsPriceCode"]."\">";
				// ���ꥢ��NO
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strSerialNo]\" value=\"".$aryData[$i]["strSerialNo"]."\">";
				
				// ñ���ꥹ�Ȥ�ɽ����value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductUnitCodeName]\" value=\"".$aryData[$i]["lngProductUnitCodeName"]."\">";
				// �������ܤ�ɽ����value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strStockSubjectCodeName]\" value=\"".$aryData[$i]["strStockSubjectCodeName"]."\">";
				// �������ʤ�ɽ����value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strStockItemCodeName]\" value=\"".$aryData[$i]["strStockItemCodeName"]."\">";
			
			}
		}
		else
		{
		// DB�����ͤ�ȤäƤ������������̾��hidden��name°�����㤦�Ľ꤬����
			for ($i = 0; $i < count( $aryData ); $i++ )
			{
				$lngConversionClassCode = ( $aryData[$i]["lngconversionclasscode"] == 1 ) ? "gs" : "ps";
				
// 2004.03.25 suzukaze update start
				// ���ٹ��ֹ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngorderdetailno]\" value=\"".$aryData[$i]["lngorderdetailno"]."\">";
// 2004.03.25 suzukaze update end

				// ���ʥ�����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strproductcode]\" value=\"".$aryData[$i]["strproductcode"]."\">";
				// �������ܥ�����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strstocksubjectcode]\" value=\"".$aryData[$i]["lngstocksubjectcode"]."\">";
				// �������ʥ�����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strstockitemcode]\" value=\"".$aryData[$i]["lngstockitemcode"]."\">";
				// ������ʬ������
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngconversionclasscode]\" value=\"$lngConversionClassCode\">";
				// ForList
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curproductpriceforlist]\" value=\"".$aryData[$i]["curproductpriceforlist"]."\">";
				// ñ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curproductprice]\" value=\"".$aryData[$i]["curproductprice"]."\">";
				// ñ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngproductunitcode]\" value=\"".$aryData[$i]["lngproductunitcode"]."\">";
				// ����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lnggoodsquantity]\" value=\"".$aryData[$i]["lngproductquantity"]."\">";
				// ��ȴ���
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curtotalprice]\" value=\"".$aryData[$i]["cursubtotalprice"]."\">";
				// ������ˡ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngcarriercode]\" value=\"".$aryData[$i]["lngdeliverymethodcode"]."\">";
				// ����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strdetailnote]\" value=\"".fncHTMLSpecialChars($aryData[$i]["strnote"])."\">";
				// ñ���ꥹ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lnggoodspricecode]\" value=\"".$aryData[$i]["lnggoodspricecode"]."\">";
				// ���ꥢ��NO
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strserialno]\" value=\"".$aryData[$i]["strserialno"]."\">";
				// Ǽ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][dtmdeliverydate]\" value=\"".$aryData[$i]["dtmdeliverydate"]."\">";
				
				$strStockSubjectName = "";
				$strStockSubjectName = fncGetMasterValue( "m_stocksubject", "lngstocksubjectcode", "strstocksubjectname", $aryData[$i]["lngstocksubjectcode"],'', $objDB );
				$strStockItemName = "";
				$strStockItemName = fncGetMasterValue( "m_stockitem", "lngstockitemcode","strstockitemname" , $aryData[$i]["lngstockitemcode"], "lngstocksubjectcode = ".$aryData[$i]["lngstocksubjectcode"],$objDB );
				
				$strProductUnitCodeName = fncGetMasterValue("m_productunit", "lngproductunitcode", "strProductUnitName", $aryData[$i]["lngproductunitcode"], "", $objDB );


				// ñ���ꥹ�Ȥ�ɽ����value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngproductunitcodename]\" value=\"$strProductUnitCodeName\">";
				// �������ܤ�ɽ����value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strstocksubjectcodename]\" value=\"".$aryData[$i]["lngstocksubjectcode"]." $strStockSubjectName\">";
				// �������ʤ�ɽ����value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strstockitemcodename]\" value=\"".$aryData[$i]["lngstockitemcode"]." $strStockItemName\">";
				
				
			}
		}

		
		$strDetailHidden = implode( "\n", $aryDetailHidden );

		return $strDetailHidden;
	}


?>