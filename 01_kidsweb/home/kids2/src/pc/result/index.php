<?php

// ----------------------------------------------------------------------------
/**
*       ��������  ����
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
*         ��������̲���ɽ������
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



	// �����ɤ߹���
	include_once('conf.inc');

	// �饤�֥���ɤ߹���
	require (LIB_FILE);
	require (LIB_ROOT . "clscache.php" );
	require (SRC_ROOT . "pc/cmn/lib_pcs.php");
	require (SRC_ROOT . "pc/cmn/column.php");
	require (LIB_DEBUGFILE);

	// DB��³
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objCache = new clsCache();
	$objDB->open( "", "", "", "" );

	//////////////////////////////////////////////////////////////////////////
	// POST(����GET)�ǡ�������
	//////////////////////////////////////////////////////////////////////////
	if ( $_POST )
	{
		$aryData = $_POST;
	}
	elseif ( $_GET )
	{
		$aryData = $_GET;
	}

	// ����ɽ�����ܼ���
	// ɽ������  $aryViewColumn�˳�Ǽ
	if( is_array( $_POST["ViewColumn"] ) )
	{
		while ( list( $strKeys, $strValues ) = each( $_POST["ViewColumn"] ) )
		{
			$strValues =  preg_replace("/(.+?)(Visible|Conditions)$/", "\\1", $strValues );
			$aryViewColumn[$strKeys] = $strValues;
		}
	}
	else
	{
		$strMessage = fncOutputError( 9058, DEF_WARNING, "" ,FALSE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

		// [lngLanguageCode]�񤭽Ф�
		$aryHtml["lngLanguageCode"] = $aryData["lngLanguageCode"];

		// [strErrorMessage]�񤭽Ф�
		$aryHtml["strErrorMessage"] = $strMessage;

		// �ƥ�ץ졼���ɤ߹���
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "/result/error/parts.tmpl" );
		
		// �ƥ�ץ졼������
		$objTemplate->replace( $aryHtml );
		$objTemplate->complete();

		// HTML����
		echo $objTemplate->strTemplate;

		exit;
	}

	// ���������ܼ���
	// ������� $arySearchColumn�˳�Ǽ
	if( is_array ( $aryData["SearchColumn"] ) )
	{
		while ( list ($strKeys, $strValues ) = each ( $aryData["SearchColumn"] ))
		{
			$strValues =  preg_replace("/(.+?)(Visible|Conditions)$/", "\\1", $strValues );
			$arySearchColumn[$strKeys] = $strValues;
		}
	}
	else
	{
	//	fncOutputError( 702, DEF_WARNING, "�����оݹ��ܤ������å�����Ƥ��ޤ���",TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		$bytSearchFlag = TRUE;
	}

	//////////////////////////////////////////////////////////////////////////
	// ���å���󡢸��³�ǧ
	//////////////////////////////////////////////////////////////////////////
	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ���³�ǧ
	// 702 ���������ʻ���������
	if ( !fncCheckAuthority( DEF_FUNCTION_PC2, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	//////////////////////////////////////////////////////////////////////////
	// ʸ��������å�
	//////////////////////////////////////////////////////////////////////////
	$aryCheck["strSessionID"]			= "null:numenglish(32,32)";
	$aryCheck["dtmInsertDateFrom"] 		= "date(/)";
	$aryCheck["dtmInsertDateTo"]		= "date(/)";
	$aryCheck["dtmStockAppDateFrom"] 	= "date(/)";
	$aryCheck["dtmStockAppDateTo"]		= "date(/)";
	$aryCheck["strStockCodeFrom"]		= "ascii(0,10)";
	$aryCheck["strStockCodeTo"]			= "ascii(0,10)";
	$aryCheck["strSlipCode"]			= "ascii(0,10)";
	$aryCheck["strOrderCodeFrom"]		= "ascii(0,10)";
	$aryCheck["strOrderCodeTo"]			= "ascii(0,10)";
	$aryCheck["lngInputUserCode"]		= "numenglish(0,3)";
	$aryCheck["strInputUserName"]		= "length(0,50)";
	$aryCheck["lngCustomerCode"]		= "numenglish(0,4)";
	$aryCheck["strCustomerName"]		= "length(0,50)";
	$aryCheck["lngInChargeGroupCode"]	= "numenglish(0,2)";
	$aryCheck["strInChargeGroupName"]	= "length(0,50)";
	$aryCheck["lngInChargeUserCode"]	= "numenglish(0,3)";
	$aryCheck["strInChargeUserName"]	= "length(0,50)";
	// 2004.04.14 suzukaze update start
	//$aryCheck["lngStockStatusCode"]		= "length(0,50)";
	// 2004.04.14 suzukaze update end
	$aryCheck["lngPayConditionCode"]	= "numenglish(0,3)";
	$aryCheck["dtmExpirationDateFrom"] 	= "date(/)";
	$aryCheck["dtmExpirationDateTo"]	= "date(/)";
	$aryCheck["strProductCode"]			= "numenglish(0,5)";
	$aryCheck["strProductName"]			= "length(0,100)";
	$aryCheck["lngStockSubjectCode"]	= "ascii(0,7)";
	$aryCheck["lngStockItemCode"]		= "ascii(0,7)";

	// ʸ��������å�
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );

	// 702 ���������ʻ���������
	if ( !fncCheckAuthority( DEF_FUNCTION_PC2, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// 703 ���������ʻ��������������⡼�ɡ�
	if ( fncCheckAuthority( DEF_FUNCTION_PC3, $objAuth ) and isset( $aryData["Admin"]) )
	{
		$aryUserAuthority["Admin"] = 1;		// 703 �����⡼�ɤǤθ���
	}
	// 704 ���������ʾܺ�ɽ����
	if ( fncCheckAuthority( DEF_FUNCTION_PC4, $objAuth ) )
	{
		$aryUserAuthority["Detail"] = 1;	// 704 �ܺ�ɽ��
	}
	// 707 ���������ʽ�����
	if ( fncCheckAuthority( DEF_FUNCTION_PC5, $objAuth ) )
	{
		$aryUserAuthority["Fix"] = 1;		// 705 ����
	}
	// 706 ���������ʺ����
	if ( fncCheckAuthority( DEF_FUNCTION_PC6, $objAuth ) )
	{
		$aryUserAuthority["Delete"] = 1;	// 706 ���
	}
	// 707 ����������̵������
	if ( fncCheckAuthority( DEF_FUNCTION_PC7, $objAuth ) and isset( $aryData["Admin"]) )
	{
		$aryUserAuthority["Invalid"] = 1;	// 707 ̵����
	}

	// ���å�������
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	reset($aryViewColumn);
	if ( !$bytSearchFlag )
	{
		reset($arySearchColumn);
	}
	reset($aryData);

	// �������˰��פ�����������ɤ��������SQLʸ�κ���
	$strQuery = fncGetSearchStockSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, "", 0, FALSE );


	//fncDebug("pcsearch.txt", $strQuery, __FILE__ , __LINE__ );

	// echo "strQuery: <BR>";
	// var_dump( $strQuery );
	// echo "<BR>aryViewColumn: <BR>";
	// var_dump( $aryViewColumn );
	// echo "<BR>arySearchColumn: <BR>";
	// var_dump( $arySearchColumn );
	// echo "<BR>aryData: <BR>";
	// var_dump( $aryData );
	// exit;

	// �ͤ�Ȥ� =====================================
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		// ���������������ʾ�ξ�票�顼��å�������ɽ������
		if ( $lngResultNum > DEF_SEARCH_MAX )
		{
			$strMessage = fncOutputError( 9057, DEF_WARNING, DEF_SEARCH_MAX ,FALSE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

			// [lngLanguageCode]�񤭽Ф�
			$aryHtml["lngLanguageCode"] = $aryData["lngLanguageCode"];

			// [strErrorMessage]�񤭽Ф�
			$aryHtml["strErrorMessage"] = $strMessage;

			// �ƥ�ץ졼���ɤ߹���
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "/result/error/parts.tmpl" );
			
			// �ƥ�ץ졼������
			$objTemplate->replace( $aryHtml );
			$objTemplate->complete();

			// HTML����
			echo $objTemplate->strTemplate;

			exit;
		}

		// ���������Ǥ�����̾����
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		$strMessage = fncOutputError( 703, DEF_WARNING, "" ,FALSE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

		// [lngLanguageCode]�񤭽Ф�
		$aryHtml["lngLanguageCode"] = $aryData["lngLanguageCode"];

		// [strErrorMessage]�񤭽Ф�
		$aryHtml["strErrorMessage"] = $strMessage;

		// �ƥ�ץ졼���ɤ߹���
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "/result/error/parts.tmpl" );
		
		// �ƥ�ץ졼������
		$objTemplate->replace( $aryHtml );
		$objTemplate->complete();

		// HTML����
		echo $objTemplate->strTemplate;

		exit;
	}

	$objDB->freeResult( $lngResultID );

	// ���������
	if ( $aryData["lngLanguageCode"] == 1 )
	{
		$aryTytle = $arySearchTableTytle;
	}
	else
	{
		$aryTytle = $arySearchTableTytleEng;
	}

	// �ơ��֥빽���Ǹ�����̤�������ȣԣ̷ͣ����ǽ��Ϥ���
	$aryHtml["strHtml"] = fncSetStockTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableViewName );

	// POST���줿�ǡ�����Hidden�ˤ����ꤹ��
	unset($ary_keys);
	$ary_Keys = array_keys( $aryData );
	while ( list ($strKeys, $strValues ) = each ( $ary_Keys ) )
	{
		if( $strValues == "ViewColumn")
		{
			reset( $aryData["ViewColumn"] );
			for ( $i = 0; $i < count( $aryData["ViewColumn"] ); $i++ )
			{
				$aryHidden[] = "<input type='hidden' name='ViewColumn[]' value='" .$aryData["ViewColumn"][$i]. "'>";
			}
		}
		elseif( $strValues == "SearchColumn")
		{
			reset( $aryData["SearchColumn"] );
			for ( $j = 0; $j < count( $aryData["SearchColumn"] ); $j++ )
			{
				$aryHidden[] = "<input type='hidden' name='SearchColumn[]' value='". $aryData["SearchColumn"][$j] ."'>";
			}
		}
		elseif( $strValues == "strSort" || $strValues == "strSortOrder")
		{
			//���⤷�ʤ�
		} 
		else
		{
			// ������ͤξ��ʾ��֡�����ե����֡�
			if( is_array($aryData[$strValues]) )
			{
				for($k = 0; $k < count($aryData[$strValues]); $k++ )
				{
					$aryHidden[] = '<input type="hidden" name="'.$strValues.'['.$k.']" value="'. $aryData[$strValues][$k] .'">';
				}
			}
			else
			{
				$aryHidden[] = '<input type="hidden" name="'. $strValues.'" value="'.$aryData[$strValues].'">';
			}
		}
	}

	$aryHidden[] = "<input type='hidden' name='strSort' value=''>";
	$aryHidden[] = "<input type='hidden' name='strSortOrder' value=''>";
	$strHidden = implode ("\n", $aryHidden );

	$aryHtml["strHidden"] = $strHidden;

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "/pc/result/parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objCache->Release();

	return true;

?>
