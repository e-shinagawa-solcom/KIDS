<?php

// ----------------------------------------------------------------------------
/**
*       ȯ�����  ����
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
	require (SRC_ROOT . "po/cmn/lib_pos.php");
	require (SRC_ROOT . "po/cmn/column.php");
	require (LIB_DEBUGFILE);

	// DB��³
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objCache = new clsCache();
	$objDB->open( "", "", "", "" );

	//////////////////////////////////////////////////////////////////////////
	// POST(����GET)�ǡ�������
	//////////////////////////////////////////////////////////////////////////
	$aryData = $_REQUEST;


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
		$strMessage = fncOutputError( 9058, DEF_WARNING, "" ,FALSE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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
	//	fncOutputError( 502, DEF_WARNING, "�����оݹ��ܤ������å�����Ƥ��ޤ���",TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		$bytSearchFlag = TRUE;
	}

	//fncDebug("po_index.txt", $arySearchColumn, __FILE__ , __LINE__ );

	//////////////////////////////////////////////////////////////////////////
	// ���å���󡢸��³�ǧ
	//////////////////////////////////////////////////////////////////////////
	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ���³�ǧ
	// 502 ȯ�������ȯ������
	if ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	//////////////////////////////////////////////////////////////////////////
	// ʸ��������å�
	//////////////////////////////////////////////////////////////////////////
	$aryCheck["strSessionID"]			= "null:numenglish(32,32)";
	$aryCheck["dtmInsertDateFrom"] 		= "date(/)";
	$aryCheck["dtmInsertDateTo"]		= "date(/)";
	$aryCheck["dtmOrderAppDateFrom"] 	= "date(/)";
	$aryCheck["dtmOrderAppDateTo"]		= "date(/)";
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
	//$aryCheck["lngOrderStatusCode"]		= "length(0,50)";
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

	// 502 ȯ�������ȯ������
	if ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// 503 ȯ�������ȯ�����������⡼�ɡ�
	if ( fncCheckAuthority( DEF_FUNCTION_PO3, $objAuth ) and isset( $aryData["Admin"]) )
	{
		$aryUserAuthority["Admin"] = 1;		// 503 �����⡼�ɤǤθ���
	}
	// 504 ȯ������ʾܺ�ɽ����
	if ( fncCheckAuthority( DEF_FUNCTION_PO4, $objAuth ) )
	{
		$aryUserAuthority["Detail"] = 1;	// 504 �ܺ�ɽ��
	}
	// 505 ȯ������ʽ�����
	if ( fncCheckAuthority( DEF_FUNCTION_PO5, $objAuth ) )
	{
		$aryUserAuthority["Fix"] = 1;		// 505 ����
	}
	// 506 ȯ������ʺ����
	if ( fncCheckAuthority( DEF_FUNCTION_PO6, $objAuth ) )
	{
		$aryUserAuthority["Delete"] = 1;	// 506 ���
	}
	// 507 ȯ�������̵������
	if ( fncCheckAuthority( DEF_FUNCTION_PO7, $objAuth ) and isset( $aryData["Admin"]) )
	{
		$aryUserAuthority["Invalid"] = 1;	// 507 ̵����
	}

	// ���å�������
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	reset($aryViewColumn);
	if ( !$bytSearchFlag )
	{
		reset($arySearchColumn);
	}
	reset($aryData);

	// �������˰��פ���ȯ�����ɤ��������SQLʸ�κ���
	$strQuery = fncGetSearchPurchaseSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, "", 0, FALSE );
	//fncDebug("po_index.txt", $aryData, __FILE__ , __LINE__ );


	// �ͤ�Ȥ� =====================================
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		// ���������������ʾ�ξ�票�顼��å�������ɽ������
		if ( $lngResultNum > DEF_SEARCH_MAX )
		{
			$strMessage = fncOutputError( 9057, DEF_WARNING, DEF_SEARCH_MAX ,FALSE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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
		$strMessage = fncOutputError( 503, DEF_WARNING, "" ,FALSE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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
	$aryHtml["strHtml"] = fncSetPurchaseTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableViewName );

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
		elseif( $strValues == "strSort" || $strValues == "strSortOrder" )
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

	$aryHidden[] = "<input type='hidden' name='strSort'>";
	$aryHidden[] = "<input type='hidden' name='strSortOrder'>";
	$strHidden = implode ("\n", $aryHidden );

	$aryHtml["strHidden"] = $strHidden;

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "/po/result/parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objCache->Release();

	return true;

?>
