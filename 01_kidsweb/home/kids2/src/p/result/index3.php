<?php

// ----------------------------------------------------------------------------
/**
*       ���ʴ���  ���
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
*         �����꾦���ֹ�ǡ����κ������
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
require (SRC_ROOT . "p/cmn/lib_ps.php");
require (SRC_ROOT . "p/cmn/lib_ps1.php");
require (SRC_ROOT . "p/cmn/column.php");

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
if ( !isset($aryData["lngProductNo"]) )
{
	fncOutputError ( 9061, DEF_ERROR, "�ǡ����۾�Ǥ���", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// ʸ��������å�
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngProductNo"]	  = "null:number(0,10)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ������桼���������ɤμ���
$lngInputUserCode = $objAuth->UserCode;



// ���³�ǧ
// 302 ���ʴ����ʾ��ʸ�����
if ( !fncCheckAuthority( DEF_FUNCTION_P2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
// 307 ���ʴ����ʾ��ʺ����
if ( !fncCheckAuthority( DEF_FUNCTION_P7, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}



//-------------------------------------------------------------------------
// �������ʡפ˥�����桼������°���Ƥ��뤫�����å�
//-------------------------------------------------------------------------
$strFncFlag = "P";
$blnCheck = fncCheckInChargeProduct( $aryData["lngProductNo"], $lngInputUserCode, $strFncFlag, $objDB );

// �桼�������о����ʤ�°���Ƥ��ʤ����
if( !$blnCheck )
{
	fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
}




// ����оݤ����ʥ����ɤξ��ʾ������
$strQuery = fncGetProductNoToInfoSQL ( $aryData["lngProductNo"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum == 1 )
{
	$aryProductResult = $objDB->fetchArray( $lngResultID, 0 );
}
else
{
	fncOutputError( 9061, DEF_ERROR, "", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

// var_dump( $aryData );
// exit;

////////////////////////////////////////////////////////
////////////////////// �����ǧ���� ////////////////////
////////////////////////////////////////////////////////
$strProductCode = $aryProductResult["strproductcode"];
$aryDeta["strMessageDetail"] = "";
// ����оݤ���Ѥ��Ƥ���ǡ����γ�ǧ
// ����
$aryCode = fncGetDeleteCodeToMaster ( $strProductCode, 1, $objDB );
if ( $aryCode )
{
	$lngReceiveCount = count($aryCode);
	// �ִ���ʸ���������
	for( $i = 0; $i < $lngReceiveCount; $i++ )
	{
		$aryDetailData["strFuncType"] = "�������";
		$aryDetailData["strCode"] = $aryCode[$i]["lngsearchno"];

		// �ƥ�ץ졼���ɤ߹���
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "error/use/parts_detail.tmpl" );

		// �ƥ�ץ졼������
		$objTemplate->replace( $aryDetailData );
		$objTemplate->complete();
		
		// HTML����
		$aryDetail[] = $objTemplate->strTemplate;
	}

	$aryData["strMessageDetail"] = implode ("\n", $aryDetail );
}

// ȯ��
unset ( $aryCode );
unset ( $aryDetail );
$aryCode = fncGetDeleteCodeToMaster ( $strProductCode, 2, $objDB );
if ( $aryCode )
{
	$lngOrderCount = count($aryCode);
	// �ִ���ʸ���������
	for( $i = 0; $i < $lngOrderCount; $i++ )
	{
		$aryDetailData["strFuncType"] = "ȯ�����";
		$aryDetailData["strCode"] = $aryCode[$i]["lngsearchno"];

		// �ƥ�ץ졼���ɤ߹���
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "error/use/parts_detail.tmpl" );

		// �ƥ�ץ졼������
		$objTemplate->replace( $aryDetailData );
		$objTemplate->complete();
		
		// HTML����
		$aryDetail[] = $objTemplate->strTemplate;
	}

	$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
}

// ���
unset ( $aryCode );
unset ( $aryDetail );
$aryCode = fncGetDeleteCodeToMaster ( $strProductCode, 3, $objDB );
if ( $aryCode )
{
	$lngSalesCount = count($aryCode);
	// �ִ���ʸ���������
	for( $i = 0; $i < $lngSalesCount; $i++ )
	{
		$aryDetailData["strFuncType"] = "������";
		$aryDetailData["strCode"] = $aryCode[$i]["lngsearchno"];

		// �ƥ�ץ졼���ɤ߹���
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "error/use/parts_detail.tmpl" );

		// �ƥ�ץ졼������
		$objTemplate->replace( $aryDetailData );
		$objTemplate->complete();
		
		// HTML����
		$aryDetail[] = $objTemplate->strTemplate;
	}

	$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
}

// ����
unset ( $aryCode );
unset ( $aryDetail );
$aryCode = fncGetDeleteCodeToMaster ( $strProductCode, 4, $objDB );
if ( $aryCode )
{
	$lngStockCount = count($aryCode);
	// �ִ���ʸ���������
	for( $i = 0; $i < $lngStockCount; $i++ )
	{
		$aryDetailData["strFuncType"] = "��������";
		$aryDetailData["strCode"] = $aryCode[$i]["lngsearchno"];

		// �ƥ�ץ졼���ɤ߹���
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "error/use/parts_detail.tmpl" );

		// �ƥ�ץ졼������
		$objTemplate->replace( $aryDetailData );
		$objTemplate->complete();
		
		// HTML����
		$aryDetail[] = $objTemplate->strTemplate;
	}

	$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
}

////////////////////////////////////////////////////////
////////////////////// ��������¹� ////////////////////
////////////////////////////////////////////////////////
// �����ǧ�ϣˤʤ��
if ( $aryData["strSubmit"] == "submit" and $aryData["strMessageDetail"] == "" )
{
	// �ȥ�󥶥�����󳫻�
	$objDB->transactionBegin();

	// �����Ԥιԥ�٥��å�
	$strQuery = "SELECT lngProductNo FROM m_Product WHERE lngProductNo = " . $aryData["lngProductNo"] . " FOR UPDATE";

	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		fncOutputError ( 9061, DEF_ERROR, "��å������˼��Ԥ��ޤ�����", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// �����Ԥ�UPDATE
	$strQuery = "UPDATE m_Product SET bytInvalidFlag = true WHERE lngProductNo = " . $aryData["lngProductNo"];

	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		fncOutputError ( 9061, DEF_ERROR, "��������˼��Ԥ��ޤ�����", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// ���ߥåȽ���
	$objDB->transactionCommit();

	// �����ǧ���̤�ɽ��
	$aryDeleteData = $aryProductResult;
	$aryDeleteData["strAction"] = "/p/search/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "p/finish/remove_parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryDeleteData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}

////////////////////////////////////////////////////////
////////////////////// ����Ǥ��ʤ� ////////////////////
////////////////////////////////////////////////////////
if ( $aryData["strMessageDetail"] != "" )
{
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "error/use/parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;

}

////////////////////////////////////////////////////////
//////////////////// �����ǧ����ɽ�� //////////////////
////////////////////////////////////////////////////////
$objCache = new clsCache();


// ���꾦���ֹ�ξ��ʥǡ���������SQLʸ�κ���
$strQuery = fncGetProductNoToInfoSQL ( $aryData["lngProductNo"] );

// �ܺ٥ǡ����μ���
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum )
{
	if ( $lngResultNum == 1 )
	{
		$aryResult = $objDB->fetchArray( $lngResultID, 0 );
	}
	else
	{
		fncOutputError( 303, DEF_ERROR, "�����ǡ����μ����˼��Ԥ��ޤ���", TRUE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}
}
else
{
	fncOutputError( 303, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

$objDB->freeResult( $lngResultID );

// �����ǡ�����Ĵ��
$aryNewResult = fncSetProductTableData ( $aryResult, $objDB, $objCache );




// �������ʤξ��֤��ֿ�����פξ��֤Ǥ����
if ( $aryNewResult["lngproductstatuscode"] == DEF_PRODUCT_APPLICATE )
{
	fncOutputError( 308, DEF_WARNING, "", TRUE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}





// ���������
if ( isset($aryData["lngLanguageCode"]) and  $aryData["lngLanguageCode"] == 0 )
{
	$aryTytle = $aryTableTytleEng;
}
else
{
	$aryTytle = $aryTableTytle;
}

// �����̾������
$aryColumnNames = fncSetProductTabelName ( $aryTableView, $aryTytle );

$aryNewResult["strAction"] = "index3.php";
$aryNewResult["strSessionID"] = $aryData["strSessionID"];
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "delete";

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "p/result/parts2.tmpl" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryNewResult );
$objTemplate->replace( $aryHeadColumnNames );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;


$objDB->close();

$objCache->Release();

return true;

?>