<?php

// ----------------------------------------------------------------------------
/**
*       ȯ������  ����
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
	
	require_once(SRC_ROOT.'/mold/lib/UtilSearchForm.class.php');

	// �饤�֥���ɤ߹���
	require (LIB_FILE);
	require (LIB_ROOT . "clscache.php" );
	require (SRC_ROOT . "po/cmn/lib_pos.php");
	require (SRC_ROOT . "po/cmn/column2.php");
	require (LIB_DEBUGFILE);

	// DB��³
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objCache = new clsCache();
	$objDB->open( "", "", "", "" );

	//////////////////////////////////////////////////////////////////////////
	// POST(����GET)�ǡ�������
	//////////////////////////////////////////////////////////////////////////
	// �ե�����ǡ�������ƥ��ƥ���ο���ʬ����Ԥ�
	$options = UtilSearchForm::extractArrayByOption($_REQUEST);
	$isDisplay = UtilSearchForm::extractArrayByIsDisplay($_REQUEST);
	$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
	$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
	$to = UtilSearchForm::extractArrayByTo($_REQUEST);
	$searchValue = $_REQUEST;
	
	$isDisplay=array_keys($isDisplay);
	$isSearch=array_keys($isSearch);
	$aryData['ViewColumn']=$isDisplay;
	$aryData['SearchColumn']=$isSearch;
	foreach($from as $key=> $item){
		$aryData[$key.'From']=$item;
	}
	foreach($to as $key=> $item){
		$aryData[$key.'To']=$item;
	}
	foreach($searchValue as $key=> $item){
		$aryData[$key]=$item;
	}
	
	
	// ����ɽ�����ܼ���
	if(empty($isDisplay))
	{
		$strMessage = fncOutputError( 9058, DEF_WARNING, "" ,FALSE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

		// [lngLanguageCode]�񤭽Ф�
		$aryHtml["lngLanguageCode"] = 1;

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
	if( empty ( $isSearch ) )
	{
	//	fncOutputError( 502, DEF_WARNING, "�����оݹ��ܤ������å�����Ƥ��ޤ���",TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		$bytSearchFlag = TRUE;
	}

	//////////////////////////////////////////////////////////////////////////
	// ���å���󡢸��³�ǧ
	//////////////////////////////////////////////////////////////////////////
	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ���³�ǧ
	// 510 ȯ��������ȯ���񸡺���
	if ( !fncCheckAuthority( DEF_FUNCRION_PO10, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	//////////////////////////////////////////////////////////////////////////
	// ʸ��������å�
	//////////////////////////////////////////////////////////////////////////
	$aryCheck["strSessionID"]			= "null:numenglish(32,32)";
	$aryCheck["dtmInsertDateFrom"] 		= "date(/)";
	$aryCheck["dtmInsertDateTo"]		= "date(/)";
	$aryCheck["lngInputUserCode"]		= "numenglish(0,3)";
	$aryCheck["strInputUserName"]		= "length(0,50)";
	$aryCheck["From_dtmExpirationDate"] = "date(/)";
	$aryCheck["To_dtmExpirationDate"]	= "date(/)";
	$aryCheck["strOrderCode"]			= "ascii(0,10)";
	$aryCheck["strProductCode"]			= "numenglish(0,5)";
	$aryCheck["strProductName"]			= "length(0,100)";
	$aryCheck["lngInChargeGroupCode"]	= "numenglish(0,2)";
	$aryCheck["strInChargeGroupName"]	= "length(0,50)";
	$aryCheck["lngInChargeUserCode"]	= "numenglish(0,3)";
	$aryCheck["strInChargeUserName"]	= "length(0,50)";
	$aryCheck["lngCustomerCode"]		= "numenglish(0,4)";
	$aryCheck["strCustomerName"]		= "length(0,50)";
	$aryCheck["lngDeliveryPlaceCode"]	= "numenglish(0,4)";
	$aryCheck["strDeliveryPlaceName"]	= "length(0,50)";
	$aryCheck["lngMonetaryunitCode"]	= "numenglish(0,3)";
	$aryCheck["lngMonetaryrateCode"]	= "numenglish(0,3)";
	$aryCheck["lngPayConditionCode"]	= "numenglish(0,3)";

	// ʸ��������å�
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );

	// 511 ȯ��������ȯ���񸡺��������⡼�ɡ�
	if ( fncCheckAuthority( DEF_FUNCRION_PO11, $objAuth ) and isset( $aryData["Admin"]) )
	{
		$aryUserAuthority["Admin"] = 1;		// 511 �����⡼�ɤǤθ���
	}
	// 512 ȯ��������ȯ��������
	if ( fncCheckAuthority( DEF_FUNCRION_PO12, $objAuth ) )
	{
		$aryUserAuthority["Edit"] = 1;		// 512 ����
	}
	
	// ɽ������  $aryViewColumn�˳�Ǽ
	// $aryViewColumn=$isDisplay;
	$aryViewColumn=fncResortSearchColumn2($isDisplay);
	// ��������  $arySearchColumn�˳�Ǽ
	$arySearchColumn=$isSearch;

	// ���å�������
	$aryData["lngLanguageCode"] = 1;

	reset($aryViewColumn);
	if ( !$bytSearchFlag )
	{
		reset($arySearchColumn);
	}
	reset($aryData);
	
	// �������˰��פ���ȯ�������ɤ��������SQLʸ�κ���
	$strQuery = fncGetSearchPurcheseOrderSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, "", 0, FALSE );
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
	$aryHtml["strHtml"] = fncSetPurchaseOrderTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableViewName );

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
			// ������ͤξ��ʾ��֡�����ե������֡�
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
	$objTemplate->getTemplate( "/po/result2/po_search_result.html" );
	
	// �ƥ�ץ졼������
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objCache->Release();

	return true;

?>