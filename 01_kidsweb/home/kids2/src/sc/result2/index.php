<?php

// ----------------------------------------------------------------------------
/**
*       Ǽ�ʽ����
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
*         ��Ǽ�ʽ�ǡ���������̲���ɽ������
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
	require (SRC_ROOT . "sc/cmn/column_scd.php");
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
	$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
	$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
	$to = UtilSearchForm::extractArrayByTo($_REQUEST);
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
	// 602 ����������帡����
	if ( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	// 602 ����������帡����
	if ( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	// 603 ����������帡���������⡼�ɡ�
	if ( fncCheckAuthority( DEF_FUNCTION_SC3, $objAuth ) and isset( $aryData["Admin"]) )
	{
		$aryUserAuthority["Admin"] = 1;		// 603 �����⡼�ɤǤθ���
	}
	// 604 �������ʾܺ�ɽ����
	if ( fncCheckAuthority( DEF_FUNCTION_SC4, $objAuth ) )
	{
		$aryUserAuthority["Detail"] = 1;	// 604 �ܺ�ɽ��
	}
	// 607 �������ʽ�����
	if ( fncCheckAuthority( DEF_FUNCTION_SC5, $objAuth ) )
	{
		$aryUserAuthority["Fix"] = 1;		// 605 ����
	}
	// 606 �������ʺ����
	if ( fncCheckAuthority( DEF_FUNCTION_SC6, $objAuth ) )
	{
		$aryUserAuthority["Delete"] = 1;	// 606 ���
	}
	// ̵�����ܥ���ϻ��ͤ�̵�������ö�����ȥ����ȡ�2019/8/22 T.Miyata��
	/*
	// 607 ��������̵������
	if ( fncCheckAuthority( DEF_FUNCTION_SC7, $objAuth ) and isset( $aryData["Admin"]) )
	{
		$aryUserAuthority["Invalid"] = 1;	// 607 ̵����
	}
	*/

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
	$strQuery = fncGetSearchSlipSQL( $arySearchColumn, $aryData, $objDB, "", 0, $aryData["strSessionID"]);
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		// ���������������ʾ�ξ�票�顼��å�������ɽ������
		if ( $lngResultNum > DEF_SEARCH_MAX )
		{
			$strMessage = fncOutputError( 9057, DEF_WARNING, DEF_SEARCH_MAX ,FALSE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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
		$strMessage = fncOutputError( 603, DEF_WARNING, "" ,FALSE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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
	$aryHtml["strHtml"] = fncSetSlipTableBody ( $aryResult, $arySearchColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache);

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
	$objTemplate->getTemplate( "/sc/result2/sc_search_result.html" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objCache->Release();

	return true;

?>
