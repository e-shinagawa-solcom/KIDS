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
		
		// ���᤿����ɳ�Ť�����ޥ����ιԥ�٥��å�
		$strQuery = "select m_receive.lngreceiveno, m_receive.lngrevisionno "
			. "from m_sales "
//			. "inner join ( "
//			. "select  "
//			. "    strsalescode "
//			. "   ,MAX(lngrevisionno) as lngrevisionno "
//			. "from m_sales "
//			. "group by m_sales.strsalescode "
//			. ") A "
//			. "on A.strsalescode = m_sales.strsalescode "
//			. "and A.lngrevisionno = m_sales.lngrevisionno "
			. "inner join t_salesdetail "
			. "on t_salesdetail.lngsalesno = m_sales.lngsalesno "
			. "and t_salesdetail.lngrevisionno = m_sales.lngrevisionno "
			. "inner join t_receivedetail "
			. "on t_receivedetail.lngreceiveno = t_salesdetail.lngreceiveno "
			. "and t_receivedetail.lngreceivedetailno = t_salesdetail.lngreceivedetailno "
			. "and t_receivedetail.lngrevisionno = t_salesdetail.lngreceiverevisionno "
			. "inner join m_receive "
			. "on m_receive.lngreceiveno = t_salesdetail.lngreceiveno "
			. "and m_receive.lngrevisionno = t_salesdetail.lngreceiverevisionno "
			. "where m_sales.lngSalesStatusCode = " . DEF_SALES_END
			. " AND to_char( date_trunc( 'month', m_sales.dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom ."' "
			. " AND to_char( date_trunc( 'month', m_sales.dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "���ʬ�μ���ǡ����Υ�å������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
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
		$strQuery = "UPDATE m_Sales SET lngSalesStatusCode = " . DEF_SALES_CLOSED . " WHERE lngSalesStatusCode = " . DEF_SALES_END
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "���ǡ���������������֤ؤι��������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );
		// ��å���������ޥ����Υ��ơ������򹹿�
		for ( $i = 0; $i < $lngReceiveCount; $i++ )
		{
			$strQuery = "UPDATE m_receive SET lngreceivestatuscode = " . DEF_RECEIVE_CLOSED 
			. "WHERE lngreceiveno = " . $aryReceiveResult[$i]["lngreceiveno"] . " AND lngrevisionno = " . $aryReceiveResult[$i]["lngrevisionno"];
			if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
			{
				fncOutputError ( 9061, DEF_ERROR, "���ʬ�μ���ǡ���������������֤ؤι��������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
			}
			$objDB->freeResult( $lngResultID );
		}

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

		// ���᤿����ɳ�Ť�ȯ��ޥ����ιԥ�٥��å�
		$strQuery = "select "
			. "m_order.lngorderno, m_order.lngrevisionno "
			. "from m_stock "
//			. "inner join ( "
//			. "select  "
//			. "    strstockcode "
//			. "   ,MAX(lngrevisionno) as lngrevisionno "
//			. "from m_stock "
//			. "group by m_stock.strstockcode "
//			. ") A "
//			. "on A.strstockcode = m_stock.strstockcode "
//			. "and A.lngrevisionno = m_stock.lngrevisionno "
			. "inner join t_stockdetail "
			. "on t_stockdetail.lngstockno = m_stock.lngstockno "
			. "and t_stockdetail.lngrevisionno = m_stock.lngrevisionno "
			. "inner join t_orderdetail "
			. "on t_orderdetail.lngorderno = t_stockdetail.lngorderno "
			. "and t_orderdetail.lngorderdetailno = t_stockdetail.lngorderdetailno "
			. "and t_orderdetail.lngrevisionno = t_stockdetail.lngorderrevisionno "
			. "inner join m_order "
			. "on m_order.lngorderno = t_stockdetail.lngorderno "
			. "and m_order.lngrevisionno = t_stockdetail.lngorderrevisionno "
			. "where m_stock.lngStockStatusCode = " . DEF_STOCK_END
			. " AND to_char( date_trunc( 'month', m_stock.dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom . "'"
			. " AND to_char( date_trunc( 'month', m_stock.dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "����ʬ��ȯ��ǡ����Υ�å������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
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
		$strQuery = "UPDATE m_Stock SET lngStockStatusCode = " . DEF_STOCK_CLOSED . " WHERE lngStockStatusCode = " . DEF_STOCK_END
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "�����ǡ���������������֤ؤι��������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		// ��å�����ȯ��ޥ����Υ��ơ������򹹿�
		for ( $i = 0; $i < $lngOrderCount; $i++ )
		{
			$strQuery = "UPDATE m_order SET lngorderstatuscode = " . DEF_ORDER_CLOSED 
			. "WHERE lngorderno = " . $aryOrderResult[$i]["lngorderno"] . " AND lngrevisionno = " . $aryOrderResult[$i]["lngrevisionno"];
			if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
			{
				fncOutputError ( 9061, DEF_ERROR, "����ʬ��ȯ��ǡ���������������֤ؤι��������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
			}
			$objDB->freeResult( $lngResultID );
		}

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

		// ���᤿����ɳ�Ť�����ޥ����ιԥ�٥��å�
		$strQuery = "select m_receive.lngreceiveno, m_receive.lngrevisionno "
			. "from m_sales "
//			. "inner join ( "
//			. "select  "
//			. "    strsalescode "
//			. "   ,MAX(lngrevisionno) as lngrevisionno "
//			. "from m_sales "
//			. "group by m_sales.strsalescode "
//			. ") A "
//			. "on A.strsalescode = m_sales.strsalescode "
//			. "and A.lngrevisionno = m_sales.lngrevisionno "
			. "inner join t_salesdetail "
			. "on t_salesdetail.lngsalesno = m_sales.lngsalesno "
			. "and t_salesdetail.lngrevisionno = m_sales.lngrevisionno "
			. "inner join t_receivedetail "
			. "on t_receivedetail.lngreceiveno = t_salesdetail.lngreceiveno "
			. "and t_receivedetail.lngreceivedetailno = t_salesdetail.lngreceivedetailno "
			. "and t_receivedetail.lngrevisionno = t_salesdetail.lngreceiverevisionno "
			. "inner join m_receive "
			. "on m_receive.lngreceiveno = t_salesdetail.lngreceiveno "
			. "and m_receive.lngrevisionno = t_salesdetail.lngreceiverevisionno "
			. "where m_sales.lngSalesStatusCode = " . DEF_SALES_CLOSED
			. " AND to_char( date_trunc( 'month', m_sales.dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom ."' "
			. " AND to_char( date_trunc( 'month', m_sales.dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "���ʬ�μ���ǡ����Υ�å������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
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
		$strQuery = "UPDATE m_Sales SET lngSalesStatusCode = " . DEF_SALES_END . " WHERE lngSalesStatusCode = " . DEF_SALES_CLOSED
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "���ǡ�������������ᤷ���֤ؤι��������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		// ��å���������ޥ����Υ��ơ������򹹿�
		for ( $i = 0; $i < $lngReceiveCount; $i++ )
		{
			$strQuery = "UPDATE m_receive SET lngreceivestatuscode = " . DEF_RECEIVE_END 
			. "WHERE lngreceiveno = " . $aryReceiveResult[$i]["lngreceiveno"] . " AND lngrevisionno = " . $aryReceiveResult[$i]["lngrevisionno"];
			if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
			{
				fncOutputError ( 9061, DEF_ERROR, "���ʬ�μ���ǡ�������������ᤷ���֤ؤι��������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
			}
			$objDB->freeResult( $lngResultID );
		}

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

		// ���᤿����ɳ�Ť�ȯ��ޥ����ιԥ�٥��å�
		$strQuery = "select "
			. "m_order.lngorderno, m_order.lngrevisionno "
			. "from m_stock "
//			. "inner join ( "
//			. "select  "
//			. "    strstockcode "
//			. "   ,MAX(lngrevisionno) as lngrevisionno "
//			. "from m_stock "
//			. "group by m_stock.strstockcode "
//			. ") A "
//			. "on A.strstockcode = m_stock.strstockcode "
//			. "and A.lngrevisionno = m_stock.lngrevisionno "
			. "inner join t_stockdetail "
			. "on t_stockdetail.lngstockno = m_stock.lngstockno "
			. "and t_stockdetail.lngrevisionno = m_stock.lngrevisionno "
			. "inner join t_orderdetail "
			. "on t_orderdetail.lngorderno = t_stockdetail.lngorderno "
			. "and t_orderdetail.lngorderdetailno = t_stockdetail.lngorderdetailno "
			. "and t_orderdetail.lngrevisionno = t_stockdetail.lngorderrevisionno "
			. "inner join m_order "
			. "on m_order.lngorderno = t_stockdetail.lngorderno "
			. "and m_order.lngrevisionno = t_stockdetail.lngorderrevisionno "
			. "where m_stock.lngStockStatusCode = " . DEF_STOCK_CLOSED
			. " AND to_char( date_trunc( 'month', m_stock.dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom . "'"
			. " AND to_char( date_trunc( 'month', m_stock.dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "����ʬ��ȯ��ǡ����Υ�å������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
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
		$strQuery = "UPDATE m_Stock SET lngStockStatusCode = " . DEF_STOCK_END . " WHERE lngStockStatusCode = " . DEF_STOCK_CLOSED
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "�����ǡ�������������ᤷ���֤ؤι��������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		// ��å�����ȯ��ޥ����Υ��ơ������򹹿�
		for ( $i = 0; $i < $lngOrderCount; $i++ )
		{
			$strQuery = "UPDATE m_order SET lngorderstatuscode = " . DEF_ORDER_END 
			. "WHERE lngorderno = " . $aryOrderResult[$i]["lngorderno"] . " AND lngrevisionno = " . $aryOrderResult[$i]["lngrevisionno"];
			if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
			{
				fncOutputError ( 9061, DEF_ERROR, "����ʬ��ȯ��ǡ�������������ᤷ���֤ؤι��������˼��Ԥ��ޤ�����", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
			}
			$objDB->freeResult( $lngResultID );
		}

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