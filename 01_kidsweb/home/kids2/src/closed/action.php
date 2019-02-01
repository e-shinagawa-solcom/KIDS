<?php
// ----------------------------------------------------------------------------
/**
*       ����������¹Խ���
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
*         ��������������������������ᤷ�����μ¹�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------


// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "closed/cmn/lib_closed.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// GET�ǡ�������
//////////////////////////////////////////////////////////////////////////
if ( $_GET )
{
	$aryData = $_GET;
}
else if ( $_POST )
{
	$aryData = $_POST;
}

// ʸ��������å�
if ( $aryData["lngActionCode"] == "" or $aryData["lngActionCode"] < 2 or $aryData["lngActionCode"] > 3 )
{
	echo "����μ����˼��Ԥ��ޤ�����<BR>";
	return true;
}
if ( fncCheckString( $aryData["dtmUpdateFrom"], "null:date" ) != FALSE )
{
	echo "���Ϸ׾��������ꤵ��Ƥ��ޤ���<BR>";
	return true;
}
if ( fncCheckString( $aryData["dtmUpdateTo"], "null:date" ) != FALSE )
{
	echo "��λ�׾��������ꤵ��Ƥ��ޤ���<BR>";
	return true;
}

if(  !is_array($aryData["lngTargetData"]) )
{
	echo "�оݤ����ꤵ��Ƥ��ޤ���<BR>";
	return true;
}

$aryTargetFlag = array();
while( list($strKey, $strValue) = each($aryData["lngTargetData"]) )
{
	switch($strValue)
	{
		case DEF_FUNCTION_SO0:
			$aryTargetFlag[DEF_FUNCTION_SO0] = true;
		break;
		case DEF_FUNCTION_PO0:
			$aryTargetFlag[DEF_FUNCTION_PO0] = true;
		break;
		case DEF_FUNCTION_SC0:
			$aryTargetFlag[DEF_FUNCTION_SC0] = true;
		break;
		case DEF_FUNCTION_PC0:
			$aryTargetFlag[DEF_FUNCTION_PC0] = true;
		break;
	}
}

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
// 1400 �������
if ( !fncCheckAuthority( DEF_FUNCTION_CLD0, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// ���դγ�ǧ
if ( $aryData["dtmUpdateFrom"] > $aryData["dtmUpdateTo"] )
{
	$aryData["dtmUpdateTo"] = $aryData["dtmUpdateFrom"];
}
else
{
	$dtmUpdateFrom = $aryData["dtmUpdateFrom"];
	$dtmUpdateTo   = $aryData["dtmUpdateTo"];
}

$aryData["strMessageDetail"] = "";

////////////////////////////////////////////////////////////////////
/////////////////////////////�������///////////////////////////////
////////////////////////////////////////////////////////////////////
if ( $aryData["lngActionCode"] == DEF_CLOSED_RUN )
{
	$lngReceiveCount = 0;
	$lngOrderCount   = 0;
	$lngSalesCount   = 0;
	$lngStockCount   = 0;
	
	////////////////////////////////////
	//////////////�������//////////////
	////////////////////////////////////
	if( $aryTargetFlag[DEF_FUNCTION_SO0] )
	{
		// �ȥ�󥶥�����󳫻�
		$objDB->transactionBegin();

		// �����Ԥιԥ�٥��å�
		$strQuery = "SELECT lngReceiveNo, strReceiveCode, strReviseCode FROM m_Receive WHERE lngReceiveStatusCode = " . DEF_RECEIVE_END
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "����ǡ����Υ�å������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		if ( $lngResultNum )
		{
			$lngReceiveCount = $lngResultNum;
			for ( $i = 0; $i < $lngReceiveCount; $i++ )
			{
				$aryReceiveResult[] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		$objDB->freeResult( $lngResultID );

		// �����Ԥ�UPDATE
		$strQuery = "UPDATE m_Receive SET lngReceiveStatusCode = " . DEF_RECEIVE_CLOSED . " WHERE lngReceiveStatusCode = " . DEF_RECEIVE_END
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "����ǡ���������������֤ؤι��������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		// ���ߥåȽ���
		$objDB->transactionCommit();

		// �ִ���ʸ���������
		for( $i = 0; $i < $lngReceiveCount; $i++ )
		{
			$aryDetailData["strFuncType"] = "�������";
			$aryDetailData["strCode"] = $aryReceiveResult[$i]["strreceivecode"] . "-" . $aryReceiveResult[$i]["strrevisecode"];

			// �ƥ�ץ졼���ɤ߹���
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "closed/parts_detail.tmpl" );

			// �ƥ�ץ졼������
			$objTemplate->replace( $aryDetailData );
			$objTemplate->complete();
			
			// HTML����
			$aryDetail[] = $objTemplate->strTemplate;
		}

		if ( count($aryDetail) )
		{
			$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
		}
	}
	
	////////////////////////////////////
	//////////////ȯ�����//////////////
	////////////////////////////////////
	if( $aryTargetFlag[DEF_FUNCTION_PO0] )
	{
		// �ȥ�󥶥�����󳫻�
		$objDB->transactionBegin();

		// �����Ԥιԥ�٥��å�
		$strQuery = "SELECT lngOrderNo, strOrderCode, strReviseCode FROM m_Order WHERE lngOrderStatusCode = " . DEF_ORDER_END
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "ȯ��ǡ����Υ�å������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		if ( $lngResultNum )
		{
			$lngOrderCount = $lngResultNum;
			for ( $i = 0; $i < $lngOrderCount; $i++ )
			{
				$aryOrderResult[] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		$objDB->freeResult( $lngResultID );

		// �����Ԥ�UPDATE
		$strQuery = "UPDATE m_Order SET lngOrderStatusCode = " . DEF_ORDER_CLOSED . " WHERE lngOrderStatusCode = " . DEF_ORDER_END
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "ȯ��ǡ���������������֤ؤι��������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		// ���ߥåȽ���
		$objDB->transactionCommit();

		unset ( $aryDetail );
		// �ִ���ʸ���������
		for( $i = 0; $i < $lngOrderCount; $i++ )
		{
			$aryDetailData["strFuncType"] = "ȯ�����";
			$aryDetailData["strCode"] = $aryOrderResult[$i]["strordercode"] . "-" . $aryOrderResult[$i]["strrevisecode"];

			// �ƥ�ץ졼���ɤ߹���
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "closed/parts_detail.tmpl" );

			// �ƥ�ץ졼������
			$objTemplate->replace( $aryDetailData );
			$objTemplate->complete();
			
			// HTML����
			$aryDetail[] = $objTemplate->strTemplate;
		}

		if ( count($aryDetail) )
		{
			$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
		}
	}
	
	////////////////////////////////////
	//////////////������//////////////
	////////////////////////////////////
	if( $aryTargetFlag[DEF_FUNCTION_SC0] )
	{
		// �ȥ�󥶥�����󳫻�
		$objDB->transactionBegin();

		// �����Ԥιԥ�٥��å�
		$strQuery = "SELECT lngSalesNo, strSalesCode FROM m_Sales WHERE lngSalesStatusCode = " . DEF_SALES_END
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "���ǡ����Υ�å������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		if ( $lngResultNum )
		{
			$lngSalesCount = $lngResultNum;
			for ( $i = 0; $i < $lngSalesCount; $i++ )
			{
				$arySalesResult[] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		$objDB->freeResult( $lngResultID );

		// �����Ԥ�UPDATE
		$strQuery = "UPDATE m_Sales SET lngSalesStatusCode = " . DEF_SALES_CLOSED . " WHERE lngSalesStatusCode = " . DEF_SALES_END
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "���ǡ���������������֤ؤι��������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		// ���ߥåȽ���
		$objDB->transactionCommit();

		unset ( $aryDetail );
		// �ִ���ʸ���������
		for( $i = 0; $i < $lngSalesCount; $i++ )
		{
			$aryDetailData["strFuncType"] = "������";
			$aryDetailData["strCode"] = $arySalesResult[$i]["strsalescode"];

			// �ƥ�ץ졼���ɤ߹���
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "closed/parts_detail.tmpl" );

			// �ƥ�ץ졼������
			$objTemplate->replace( $aryDetailData );
			$objTemplate->complete();
			
			// HTML����
			$aryDetail[] = $objTemplate->strTemplate;
		}

		if ( count($aryDetail) )
		{
			$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
		}
	}
	
	////////////////////////////////////
	//////////////��������//////////////
	////////////////////////////////////
	if( $aryTargetFlag[DEF_FUNCTION_PC0] )
	{
		// �ȥ�󥶥�����󳫻�
		$objDB->transactionBegin();

		// �����Ԥιԥ�٥��å�
		$strQuery = "SELECT lngStockNo, strStockCode FROM m_Stock WHERE lngStockStatusCode = " . DEF_STOCK_END
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "�����ǡ����Υ�å������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		if ( $lngResultNum )
		{
			$lngStockCount = $lngResultNum;
			for ( $i = 0; $i < $lngStockCount; $i++ )
			{
				$aryStockResult[] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		$objDB->freeResult( $lngResultID );

		// �����Ԥ�UPDATE
		$strQuery = "UPDATE m_Stock SET lngStockStatusCode = " . DEF_STOCK_CLOSED . " WHERE lngStockStatusCode = " . DEF_STOCK_END
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "�����ǡ���������������֤ؤι��������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		// ���ߥåȽ���
		$objDB->transactionCommit();

		unset ( $aryDetail );
		// �ִ���ʸ���������
		for( $i = 0; $i < $lngStockCount; $i++ )
		{
			$aryDetailData["strFuncType"] = "��������";
			$aryDetailData["strCode"] = $aryStockResult[$i]["strstockcode"];

			// �ƥ�ץ졼���ɤ߹���
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "closed/parts_detail.tmpl" );

			// �ƥ�ץ졼������
			$objTemplate->replace( $aryDetailData );
			$objTemplate->complete();
			
			// HTML����
			$aryDetail[] = $objTemplate->strTemplate;
		}

		if ( count($aryDetail) )
		{
			$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
		}
	}
	
	////////////////////////////////////
	//////////////��̽���//////////////
	////////////////////////////////////

	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
	$aryData["strProcessMessage"] = "�����Ρ�Ǽ�ʺѡפΥǡ������Ф������������Ԥ��ޤ�����";
	$aryData["strAction"] = "/closed/closed.php?strSessionID=";

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "closed/finish.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}
////////////////////////////////////////////////////////////////////
///////////////////////////�����ᤷ����/////////////////////////////
////////////////////////////////////////////////////////////////////
else if ( $aryData["lngActionCode"] == DEF_CLOSED_RETURN )
{
	$lngReceiveCount = 0;
	$lngOrderCount   = 0;
	$lngSalesCount   = 0;
	$lngStockCount   = 0;
	
	////////////////////////////////////
	//////////////�������//////////////
	////////////////////////////////////
	if( $aryTargetFlag[DEF_FUNCTION_SO0] )
	{
		// �ȥ�󥶥�����󳫻�
		$objDB->transactionBegin();

		// �����Ԥιԥ�٥��å�
		$strQuery = "SELECT lngReceiveNo, strReceiveCode, strReviseCode FROM m_Receive WHERE lngReceiveStatusCode = " . DEF_RECEIVE_CLOSED
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "����ǡ����Υ�å������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		if ( $lngResultNum )
		{
			$lngReceiveCount = $lngResultNum;
			for ( $i = 0; $i < $lngReceiveCount; $i++ )
			{
				$aryReceiveResult[] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		$objDB->freeResult( $lngResultID );

		// �����Ԥ�UPDATE
		$strQuery = "UPDATE m_Receive SET lngReceiveStatusCode = " . DEF_RECEIVE_END . " WHERE lngReceiveStatusCode = " . DEF_RECEIVE_CLOSED
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "����ǡ�������������ᤷ���֤ؤι��������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		// ���ߥåȽ���
		$objDB->transactionCommit();

		// �ִ���ʸ���������
		for( $i = 0; $i < $lngReceiveCount; $i++ )
		{
			$aryDetailData["strFuncType"] = "�������";
			$aryDetailData["strCode"] = $aryReceiveResult[$i]["strreceivecode"] . "-" . $aryReceiveResult[$i]["strrevisecode"];

			// �ƥ�ץ졼���ɤ߹���
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "closed/parts_detail.tmpl" );

			// �ƥ�ץ졼������
			$objTemplate->replace( $aryDetailData );
			$objTemplate->complete();
			
			// HTML����
			$aryDetail[] = $objTemplate->strTemplate;
		}

		if ( count($aryDetail) )
		{
			$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
		}
	}
	
	////////////////////////////////////
	//////////////ȯ�����//////////////
	////////////////////////////////////
	if( $aryTargetFlag[DEF_FUNCTION_PO0] )
	{
		// �ȥ�󥶥�����󳫻�
		$objDB->transactionBegin();

		// �����Ԥιԥ�٥��å�
		$strQuery = "SELECT lngOrderNo, strOrderCode, strReviseCode FROM m_Order WHERE lngOrderStatusCode = " . DEF_ORDER_CLOSED
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "ȯ��ǡ����Υ�å������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		if ( $lngResultNum )
		{
			$lngOrderCount = $lngResultNum;
			for ( $i = 0; $i < $lngOrderCount; $i++ )
			{
				$aryOrderResult[] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		$objDB->freeResult( $lngResultID );

		// �����Ԥ�UPDATE
		$strQuery = "UPDATE m_Order SET lngOrderStatusCode = " . DEF_ORDER_END . " WHERE lngOrderStatusCode = " . DEF_ORDER_CLOSED
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "ȯ��ǡ�������������ᤷ���֤ؤι��������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		// ���ߥåȽ���
		$objDB->transactionCommit();

		unset ( $aryDetail );
		// �ִ���ʸ���������
		for( $i = 0; $i < $lngOrderCount; $i++ )
		{
			$aryDetailData["strFuncType"] = "ȯ�����";
			$aryDetailData["strCode"] = $aryOrderResult[$i]["strordercode"] . "-" . $aryOrderResult[$i]["strrevisecode"];

			// �ƥ�ץ졼���ɤ߹���
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "closed/parts_detail.tmpl" );

			// �ƥ�ץ졼������
			$objTemplate->replace( $aryDetailData );
			$objTemplate->complete();
			
			// HTML����
			$aryDetail[] = $objTemplate->strTemplate;
		}

		if ( count($aryDetail) )
		{
			$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
		}
	}
	
	////////////////////////////////////
	//////////////������//////////////
	////////////////////////////////////
	if( $aryTargetFlag[DEF_FUNCTION_SC0] )
	{
		// �ȥ�󥶥�����󳫻�
		$objDB->transactionBegin();

		// �����Ԥιԥ�٥��å�
		$strQuery = "SELECT lngSalesNo, strSalesCode FROM m_Sales WHERE lngSalesStatusCode = " . DEF_SALES_CLOSED
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "���ǡ����Υ�å������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		if ( $lngResultNum )
		{
			$lngSalesCount = $lngResultNum;
			for ( $i = 0; $i < $lngSalesCount; $i++ )
			{
				$arySalesResult[] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		$objDB->freeResult( $lngResultID );

		// �����Ԥ�UPDATE
		$strQuery = "UPDATE m_Sales SET lngSalesStatusCode = " . DEF_SALES_END . " WHERE lngSalesStatusCode = " . DEF_SALES_CLOSED
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "���ǡ�������������ᤷ���֤ؤι��������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		// ���ߥåȽ���
		$objDB->transactionCommit();

		unset ( $aryDetail );
		// �ִ���ʸ���������
		for( $i = 0; $i < $lngSalesCount; $i++ )
		{
			$aryDetailData["strFuncType"] = "������";
			$aryDetailData["strCode"] = $arySalesResult[$i]["strsalescode"];

			// �ƥ�ץ졼���ɤ߹���
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "closed/parts_detail.tmpl" );

			// �ƥ�ץ졼������
			$objTemplate->replace( $aryDetailData );
			$objTemplate->complete();
			
			// HTML����
			$aryDetail[] = $objTemplate->strTemplate;
		}

		if ( count($aryDetail) )
		{
			$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
		}
	}
	
	////////////////////////////////////
	//////////////��������//////////////
	////////////////////////////////////
	if( $aryTargetFlag[DEF_FUNCTION_PC0] )
	{
		// �ȥ�󥶥�����󳫻�
		$objDB->transactionBegin();

		// �����Ԥιԥ�٥��å�
		$strQuery = "SELECT lngStockNo, strStockCode FROM m_Stock WHERE lngStockStatusCode = " . DEF_STOCK_CLOSED
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "�����ǡ����Υ�å������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		if ( $lngResultNum )
		{
			$lngStockCount = $lngResultNum;
			for ( $i = 0; $i < $lngStockCount; $i++ )
			{
				$aryStockResult[] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		$objDB->freeResult( $lngResultID );

		// �����Ԥ�UPDATE
		$strQuery = "UPDATE m_Stock SET lngStockStatusCode = " . DEF_STOCK_END . " WHERE lngStockStatusCode = " . DEF_STOCK_CLOSED
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "�����ǡ�������������ᤷ���֤ؤι��������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		// ���ߥåȽ���
		$objDB->transactionCommit();

		unset ( $aryDetail );
		// �ִ���ʸ���������
		for( $i = 0; $i < $lngStockCount; $i++ )
		{
			$aryDetailData["strFuncType"] = "��������";
			$aryDetailData["strCode"] = $aryStockResult[$i]["strstockcode"];

			// �ƥ�ץ졼���ɤ߹���
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "closed/parts_detail.tmpl" );

			// �ƥ�ץ졼������
			$objTemplate->replace( $aryDetailData );
			$objTemplate->complete();
			
			// HTML����
			$aryDetail[] = $objTemplate->strTemplate;
		}

		if ( count($aryDetail) )
		{
			$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
		}
	}
	
	////////////////////////////////////
	//////////////��̽���//////////////
	////////////////////////////////////

	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
	$aryData["strProcessMessage"] = "�����Ρ�����ѡפΥǡ������Ф�������������ᤷ������Ԥ��ޤ�����";
	$aryData["strAction"] = "/closed/closed.php?strSessionID=";

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "closed/finish.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}
?>