<?
/** 
*	�ޥ������� ����졼�ȥޥ��� ��ǧ����
*
*	@package   KIDS
*	@license   http://www.Solcom.co.jp/ 
*	@copyright Copyright &copy; 2019, Solcom 
*	@author    Solcom rin 
*	@access    public
*	@version   1.00
*
*/
// edit.php -> strSessionID        -> confirm.php
// edit.php -> lngActionCode       -> confirm.php
// edit.php -> lngmonetaryunitcode -> confirm.php
// edit.php -> curconversionrate   -> confirm.php
// edit.php -> dtmapplystartdate   -> confirm.php
// edit.php -> dtmapplyenddate     -> confirm.php

// �¹Ԥ�
// confirm.php -> strSessionID        -> action.php
// confirm.php -> lngActionCode       -> action.php
// confirm.php -> lngmonetaryunitcode -> action.php
// confirm.php -> curconversionrate   -> action.php
// confirm.php -> dtmapplystartdate   -> action.php
// confirm.php -> dtmapplyenddate     -> action.php


// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// GET�ǡ�������
$aryData = $_GET;



// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]        = "null:numenglish(32,32)";
$aryCheck["lngActionCode"]       = "null:number(" . DEF_ACTION_INSERT . "," . DEF_ACTION_DELETE . ")";
$aryCheck["lngmonetaryunitcode"] = "null:number(1,2147483647)";
$aryCheck["curconversionrate"]   = "null:number(0.000001,9999999999.999999)";
$aryCheck["dtmapplystartdate"]   = "null:date(/)";
$aryCheck["dtmapplyenddate"]     = "null:date(/)";

// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
if ( $aryData["dtmapplystartdate"] > $aryData["dtmapplyenddate"] )
{
	$aryCheckResult["dtmapplystartdate_Error"] = 1;
	$aryCheckResult["dtmapplyenddate_Error"]   = 1;
}

// ���顼���ʤ���硢�ޥ��������֥�������������ʸ��������å��¹�
if ( !$aryCheckResult["strSessionID"] && !join ( $aryCheckResult ) )
{
	// �ޥ��������֥�����������
	$objMaster = new clsMaster();
	$objMaster->setMasterTable( "m_TemporaryRate", "lngmonetaryunitcode", $aryData["lngmonetaryunitcode"], Array ( "dtmapplystartdate" => $aryData["dtmapplystartdate"] ), $objDB );
	$objMaster->setAryMasterInfo( $aryData["lngmonetaryunitcode"], "" );
}
//////////////////////////////////////////////////////////////////////////
// ������ͭ����������å�
//////////////////////////////////////////////////////////////////////////
// ( ��Ͽ �ޤ��� ���� ) ���顼���ʤ� ��硢
// ������Ͽ�����������å��¹�
if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT || $aryData["lngActionCode"] == DEF_ACTION_UPDATE ) && !join ( $aryCheckResult ) )
{
	// �����å�����������
	// ���ϡ���λ2�ĤȤ�������Ťʤ�ʤ��ʳ��ξ����ղ�
	// AND NOT ( ��λǯ���� < ���ϳ���ǯ���� OR ����ǯ���� > ���Ͻ�λǯ���� )
	// ����ɲ�
	$objMaster->aryCheckQuery["INSERT"] .= " AND NOT (dtmapplyenddate < '" . $aryData["dtmapplystartdate"] . "' OR dtmapplystartdate > '" . $aryData["dtmapplyenddate"] . "' )";
	list ( $lngResultID, $lngResultNum ) = fncQuery ( $objMaster->aryCheckQuery["INSERT"], $objDB );

	// ������Ͽ ���� ��̷����0�ʾ�
	// �ޤ���
	// ���� ���� ��̷����1�ʳ� �ξ�硢���顼
	if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT && $lngResultNum > 0 ) || ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE && $lngResultNum != 1 ) )
	{
		$aryCheckResult[$objMaster->aryColumnName[0] . "_Error"] = 1;
		$aryCheckResult[$objMaster->aryColumnName[1] . "_Error"] = 1;
	}
}

// ���顼����ɽ������
list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );


//////////////////////////////////////////////////////////////////////////
// ����
//////////////////////////////////////////////////////////////////////////
$aryParts["lngActionCode"]   = $aryData["lngActionCode"];
$aryParts["strSessionID"]    = $aryData["strSessionID"];
// lngmonetaryunitcode ��(CODE+NAME)����
$aryMonetaryUnitCode = fncGetMasterValue( "m_MonetaryUnit", "lngmonetaryunitcode", "strMonetaryUnitName || ':' || strMonetaryUnitSign", "Array", "", $objDB );
$aryParts["lngMonetaryUnitName"] = $aryMonetaryUnitCode[$aryData["lngmonetaryunitcode"]];
$aryParts["lngmonetaryunitcode"] = $aryData["lngmonetaryunitcode"];
$aryParts["curconversionrate"] = $aryData["curconversionrate"];
$aryParts["dtmapplystartdate"] = $aryData["dtmapplystartdate"];
$aryParts["dtmapplyenddate"] = $aryData["dtmapplyenddate"];

if ( $bytErrorFlag )
{
	//echo fncGetReplacedHtml( "m/regist/edit.tmpl", $aryData, $objAuth );
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=euc-jp\">";
	echo "<form action=/m/regist/tr/edit.php method=GET>";
	echo getArrayTable( $aryData, "HIDDEN" );
	echo "</form>";
	echo "<script language=javascript>document.forms[0].submit();</script>";
}
else
{
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "m/regist/tr/confirm.tmpl" );
	$objTemplate->replace( $aryParts );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;
}


$objDB->close();


return TRUE;
?>

