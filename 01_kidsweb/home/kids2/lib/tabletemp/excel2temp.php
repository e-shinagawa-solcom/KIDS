<?

/**
*
*	@charset	: euc-jp
*/


	/*
		include_once('conf.inc');
		require (LIB_FILE);
		include_once('clstabletemp.php');

		// DB��³
		$objDB   = new clsDB();
		$objAuth = new clsAuth();
		$objDB->open( "", "", "", "" );
	*/

	/*
		$aryA = array();
		$aryA['curproductprice']		= '99.0000';			// ����
		$aryA['curretailprice']			= '300.0000';			// Ǽ��
		$aryA['lngestimateno']			= '386';				// ���Ѹ���No
		$aryA['lnginchargeusercode']	= '243';				// ô���ԥ�����
		$aryA['lngproductionquantity']	= '400000';				// ����ͽ���
		$aryA['strproductcode']			= '2009';				// ���ʥ�����
		$aryA['strproductname']			= 'EF���쥯�����';	// ����̾��
	*/



	// ------------------------------------------------------------------------
	/**
	*   fncExcel2Temp() �ؿ�
	*
	*   ��������
	*     ������ʥϥå���=�͡ˤǻ��äƤ���ǡ�����ƥ�ݥ��ơ��֥����Ͽ����
	*
	*   @param   $objDB			[Object]	�ǡ����١������֥�������
	*   @param   $aryIn			[Array]		$ary["�ϥå���"]=�͡����ݻ�����Ƥ������
	*   @return  $lngTempNo  	[integer]	�ƥ�ݥ��ơ��֥����Ͽ�����ݤ�No��lngTempNo��
	*/
	// ------------------------------------------------------------------------
	function fncArray2Temp($objDB, $aryIn)
	{
		// �ƥ�ݥ��ơ��֥륪�֥�����������
		$objTT = new clsTableTemp;
		$objTT->objDB = $objDB;

		// �ƥ�ݥ��ơ��֥����Ͽ����Ͽ����lngTempNo�����
		$lngTempNo = $objTT->fncInsert($aryIn);

		return $lngTempNo;
	}

	// ------------------------------------------------------------------------
	/**
	*   fncTemp2ProductUpdate() �ؿ�
	*
	*   ��������
	*     ���ƥ�ݥ��ơ��֥�����Ƥ��Ѥ��ƾ��ʥޥ����򹹿�����
	*
	*   @param   $objDB			[Object]	�ǡ����١������֥�������
	*   @param   $lngTempNo		[integer]	�Ѥ���ƥ�ݥ��ơ��֥��No��lngTempNo��
	*   @return  true/false  	[boolean]	����������
	*
	*	���
	*	�ƥ�ݥ��ơ��֥�� strKey �ˤ� strproductcode ��¸�ߤ��ʤ���Фʤ�ʤ���
	*	�ƥ�ݥ��ơ��֥�� strKey �ˡ�m_product ��¸�ߤ��ʤ������̾�������������ǽ��
	*	��lngestimateno �ϰտ�Ū���оݳ��ˤ��Ƥ����
	*/
	// ------------------------------------------------------------------------
	function fncTemp2ProductUpdate($objDB, $lngTempNo)
	{
		require_once ( LIB_DEBUGFILE );


		// �ƥ�ݥ��ơ��֥륪�֥�����������
		$objTT = new clsTableTemp;
		$objTT->objDB = $objDB;


		// �ƥ�ݥ��ơ��֥뤫�����
		$aryTempInfo = $objTT->fncSelect($lngTempNo);

		// �ǡ�������������ʤ����
		if(!isset($aryTempInfo)) return false;

		// ���������ǡ������˾��ʥޥ����򹹿�
		$arySql = array();
		$arySql[] = "update m_product";
		$arySql[] = "set";
		$arySql[] = "strproductcode='" .$aryTempInfo["strproductcode"]. "'";	// ��while��,ʸ�����򵤤ˤ��ʤ������ɬ��

		// $aryTempInfo ����ʬ�ν���
		while( list($strKey, $strValue) = each($aryTempInfo) )
		{
			// strproductcode, lngestimateno , curconversionrate, curstandardrate, lngplancartonproduction �Ͼ����ɲä��ʤ�
			if( $strKey == "strproductcode" || $strKey == "lngestimateno" ||
				$strKey == "curconversionrate" || $strKey == "curstandardrate" ||
				$strKey == "lngplancartonproduction" )
			{
				continue;
			}

//fncDebug( 'temp_sql.txt', $arySql, __FILE__, __LINE__);


			// �ͤ�¸�ߤ��ʤ���硢�����ɲä��ʤ�
			if( $strValue == "" ) continue;

			// dtmdeliverylimitdate �ξ�硢YYYY/mm/dd �������Ѵ�
			if( $strKey == "dtmdeliverylimitdate" ) $strValue	= $strValue . "/01";

			// strGroupDisplayCode -> lnginchargegroupcode
			if( $strKey == "strgroupdisplaycode" )
			{
				$strKey		= "lnginchargegroupcode";
				$strValue	= fncGetMasterValue( "m_group", "strgroupdisplaycode", "lnggroupcode", $strValue.":str", '', $objDB );
			}

			// strUserDiplayCode -> lnginchargeusercode
			if( $strKey == "struserdiplaycode" )
			{
				$strKey		= "lnginchargegroupcode";
				$strValue	= fncGetMasterValue( "m_user", "struserdisplaycode", "lngusercode", $strValue.":str", '', $objDB );
			}

			// ������
			$strType	= substr( $strKey, 0, 3 );

			switch( $strType )
			{
				case "str":
					$arySql[] = "," .$strKey. "='" .$strValue. "'";
					break;
				case "dtm":
					$arySql[] = "," .$strKey. "='" .$strValue. "'";
					break;
				default:
					$arySql[] = "," .$strKey. "=" .$strValue;
					break;
			}
		}



		// ����ͽ���ñ�̤��PCS�פ��ѹ��ʶ�����
		$arySql[]	= ",lngproductionunitcode=1";

		$arySql[]	= "where";
		$arySql[]	= "strproductcode='" .$aryTempInfo["strproductcode"]. "'";
		$strSql	= implode($arySql,"\n");

		// �����¹�
		list ($lngResultID, $lngResultNum) = fncQuery($strSql, $objDB);

		return true;
	}



	// ------------------------------------------------------------------------
	/**
	*   fncGetTempData() �ؿ�
	*
	*   ��������
	*     ���ƥ�ݥ��ơ��֥�����Ƽ�������
	*
	*   @param   $objDB				[Object]	�ǡ����١������֥�������
	*   @param   $lngTempNo			[integer]	�Ѥ���ƥ�ݥ��ơ��֥��No��lngTempNo��
	*   @return  Array/Boolean  	[Object]	����:Array �� ����:Flase
	*
	*/
	// ------------------------------------------------------------------------
	function fncGetTempData($objDB, $lngTempNo)
	{
		// �ƥ�ݥ��ơ��֥륪�֥�����������
		$objTT = new clsTableTemp;
		$objTT->objDB = $objDB;


		// �ƥ�ݥ��ơ��֥뤫�����
		$aryTempInfo = $objTT->fncSelect($lngTempNo);

		// �ǡ�������������ʤ����
		if( !isset($aryTempInfo) ) return false;

		return $aryTempInfo;
	}



	// ------------------------------------------------------------------------
	/**
	*   fncDeleteEstimateTempNo() �ؿ�
	*
	*   ��������
	*     �����Ѹ����ֹ�򥭡��Ȥ��ơ������оݥơ��֥��lngTempNo��ä�
	*
	*   @param   $objDB			[Object]	�ǡ����١������֥�������
	*   @param   $lngKeyNo		[integer]	�����Ȥʤ븫�Ѹ����ֹ��lngEstimateNo��
	*   @return  true/false  	[boolean]	����������
	*
	*/
	// ------------------------------------------------------------------------
	function fncDeleteEstimateTempNo( $objDB, $lngKeyNo )
	{
		require_once ( LIB_DEBUGFILE );


		$arySql	=	array();
		$arySql[]	= "update";
		$arySql[]	= "	m_estimate";
		$arySql[]	= "set";
		$arySql[]	= "	lngtempno = null";
		$arySql[]	= "where";
		$arySql[]	= "	m_estimate.lngrevisionno = (select max(me1.lngrevisionno) from m_estimate me1 where me1.lngestimateno = m_estimate.lngestimateno)";
		$arySql[]	= "and m_estimate.lngestimateno = ".$lngKeyNo;

		$strSql	= implode( "\n", $arySql );

		// �����¹�
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strSql, $objDB );

//fncDebug( 'temp_no.txt', $lngResultNum, __FILE__, __LINE__);

		return true;
	}

?>
