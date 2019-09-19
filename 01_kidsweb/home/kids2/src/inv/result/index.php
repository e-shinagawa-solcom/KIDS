<?php

// ----------------------------------------------------------------------------
/**
*       �������  ����񸡺�����
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
*         �������ǡ���������̲���ɽ������
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
	require (SRC_ROOT . "sc/cmn/lib_scd.php");
	require (SRC_ROOT . "inv/cmn/lib_regist.php");
	require (SRC_ROOT . "inv/cmn/column.php");
	require (LIB_DEBUGFILE);

	// DB��³
	$objDB    = new clsDB();
	$objAuth  = new clsAuth();
	$objCache = new clsCache();
	$objDB->open( "", "", "", "" );

	//////////////////////////////////////////////////////////////////////////
	// POST(����GET)�ǡ�������
	//////////////////////////////////////////////////////////////////////////
	// �ե�����ǡ�������ƥ��ƥ���ο���ʬ����Ԥ�
	$options     = UtilSearchForm::extractArrayByOption($_REQUEST);
	$isSearch    = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
	$from        = UtilSearchForm::extractArrayByFrom($_REQUEST);
	$to          = UtilSearchForm::extractArrayByTo($_REQUEST);
	$searchValue = $_REQUEST;

	$isSearch=array_keys($isSearch);
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
	// 2200 �������
	if ( !fncCheckAuthority( DEF_FUNCTION_INV0, $objAuth ) )
	{
	    fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	// 2202 ����񸡺�
	if ( !fncCheckAuthority( DEF_FUNCTION_INV2, $objAuth ) )
	{
	    fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	//////////////////////////////////////////////////////////////////////////
	// ʸ��������å�
	//////////////////////////////////////////////////////////////////////////
	//���å����ID
	$aryCheck["strSessionID"]			= "null:numenglish(32,32)";

	// TODO:�׻��ͳ�ǧ
	/*
	//�ܵ�
	$aryCheck["lngCustomerCode"]		= "numenglish(0,4)";
	$aryCheck["strCustomerName"]		= "length(0,50)";
	//���Ƕ�ʬ

	//Ǽ�ʽ�NO.

	//Ǽ����
	$aryCheck["dtmDeliveryDateFrom"] 	= "date(/)";
	$aryCheck["dtmDeliveryDateTo"]		= "date(/)";
	//Ǽ����

	//��̾
	$aryCheck["strProductCode"]			= "numenglish(0,5)";
	$aryCheck["strProductName"]			= "length(0,100)";
	//��ɼ��
	$aryCheck["lngInsertUserCode"]		= "numenglish(0,3)";
	$aryCheck["strInsertUserName"]		= "length(0,50)";
	//��ʸ��NO

	//�ܵ�����

	//����ʬ
    $aryCheck["lngSalesClassCode"]		= "number(0,100)";
	*/

	// ʸ��������å�
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );

	// ��������  $arySearchColumn�˳�Ǽ
	$arySearchColumn=$isSearch;

	// ��������ܸ�Ǹ���
	$aryData["lngLanguageCode"] = 1;

	if ( !$bytSearchFlag )
	{
		reset($arySearchColumn);
	}
	reset($aryData);

	// ����SQL��¹Ԥ������ʥҥåȡ˷�����������
	$strQuery = fncGetSearchInvoiceSQL( $arySearchColumn, $aryData, $objDB, "", 0, $aryData["strSessionID"]);

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		// ���������������ʾ�ξ�票�顼��å�������ɽ������
		if ( $lngResultNum > DEF_SEARCH_MAX )
		{
			$strMessage = fncOutputError( 9057, DEF_WARNING, DEF_SEARCH_MAX ,FALSE, "../inv/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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
		$strMessage = fncOutputError( 603, DEF_WARNING, "" ,FALSE, "../inv/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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
	$aryTytle = $arySearchTableTytle;

	// �ơ��֥빽���Ǹ�����̤�������ȣԣ̷ͣ����ǽ��Ϥ���
	$aryHtml["strHtml"] = fncSetInvoiceTableBody ( $aryResult, $arySearchColumn, $aryData, $aryTytle, $objDB, $objCache);


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
	$objTemplate->getTemplate( "/inv/result/search_result.html" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objCache->Release();

	return true;

?>
