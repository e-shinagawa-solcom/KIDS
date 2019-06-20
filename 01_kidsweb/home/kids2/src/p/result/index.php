<?php

// ----------------------------------------------------------------------------
/**
*       ���ʴ���  ����
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
	require (SRC_ROOT . "p/cmn/lib_ps.php");
	require (SRC_ROOT . "p/cmn/column.php");
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

	// ������桼���������ɤμ���
	$lngInputUserCode = $objAuth->UserCode;



	// ���³�ǧ
	// 302 ���ʴ����ʾ��ʸ�����
	if ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	//////////////////////////////////////////////////////////////////////////
	// ʸ��������å�
	//////////////////////////////////////////////////////////////////////////
	$aryCheck["strSessionID"]					= "null:numenglish(32,32)";
	$aryCheck["dtmInsertDateFrom"] 				= "date(/)";
	$aryCheck["dtmInsertDateTo"]				= "date(/)";
	$aryCheck["lngGoodsPlanProgressCode"]		= "number(0,2)";
	$aryCheck["dtmRevisionDateFrom"] 			= "date(/)";
	$aryCheck["dtmRevisionDateTo"]				= "date(/)";
	$aryCheck["strProductCode"]					= "ascii(0,10)";

	////////  ʸ��������å��»ܤ���褦�ˡ�����̤�»ܡ����� /////////
	// ʸ��������å�
	// $aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	// fncPutStringCheckError( $aryCheckResult, $objDB );

	// 302 ���ʴ����ʾ��ʸ�����
	if ( !fncCheckAuthority( DEF_FUNCTION_P2, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	// 303 ���ʴ����ʾ��ʸ���������ǡ�����ɽ����
	if ( fncCheckAuthority( DEF_FUNCTION_P3, $objAuth ) )
	{
		$aryUserAuthority["SearchDelete"] = 1;
	}
	// 304 ���ʴ����ʾܺ�ɽ����
	if ( fncCheckAuthority( DEF_FUNCTION_P4, $objAuth ) )
	{
		$aryUserAuthority["Detail"] = 1;
	}
	// 305 ���ʴ����ʾܺ�ɽ��������ǡ�����ɽ����
	if ( fncCheckAuthority( DEF_FUNCTION_P5, $objAuth ) )
	{
		$aryUserAuthority["DetailDelete"] = 1;
	}
	// 306 ���ʴ����ʽ�����
	if ( fncCheckAuthority( DEF_FUNCTION_P6, $objAuth ) )
	{
		$aryUserAuthority["Fix"] = 1;
	}
	// 307 ���ʴ����ʺ����
	if ( fncCheckAuthority( DEF_FUNCTION_P7, $objAuth ) )
	{
		$aryUserAuthority["Delete"] = 1;
	}
	
	// ɽ������  $aryViewColumn�˳�Ǽ
	$aryViewColumn=$isDisplay;
	
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

	// �������˰��פ��뾦�ʥ����ɤ��������SQLʸ�κ���
	$strQuery = fncGetSearchProductSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, $aryUserAuthority );
	// �ͤ�Ȥ� =====================================
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		// ���������������ʾ�ξ�票�顼��å�������ɽ������
		if ( $lngResultNum > DEF_SEARCH_MAX )
		{
			$strMessage = fncOutputError( 9057, DEF_WARNING, DEF_SEARCH_MAX ,FALSE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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
		$strMessage = fncOutputError( 303, DEF_WARNING, "" ,FALSE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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
		$aryTytle = $aryTableTytle;
	}
	else
	{
		$aryTytle = $aryTableTytleEng;
	}

	// �ơ��֥빽���Ǹ�����̤�������ȣԣ̷ͣ����ǽ��Ϥ���
	$aryHtml["strHtml"] = fncSetProductTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableViewName );



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
			$aryHidden[] = "<input type='hidden' name='". $strValues."' value='".$aryData[$strValues]."'>";
		}
	}

	$aryHidden[] = "<input type='hidden' name='strSort'>";
	$aryHidden[] = "<input type='hidden' name='strSortOrder'>";
	$strHidden = implode ("\n", $aryHidden );

	$aryHtml["strHidden"] = $strHidden;

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "/p/result/p_search_result.html" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objCache->Release();

	return true;

?>
