<?
/** 
*	�ޥ������� �̲ߥ졼�ȥޥ��� ��ǧ����
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// edit.php -> strSessionID        -> confirm.php
// edit.php -> lngActionCode       -> confirm.php
// edit.php -> lngmonetaryratecode -> confirm.php
// edit.php -> lngmonetaryunitcode -> confirm.php
// edit.php -> curconversionrate   -> confirm.php
// edit.php -> dtmapplystartdate   -> confirm.php
// edit.php -> dtmapplyenddate     -> confirm.php

// �¹Ԥ�
// confirm.php -> strSessionID        -> action.php
// confirm.php -> lngActionCode       -> action.php
// confirm.php -> lngmonetaryratecode -> action.php
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
$aryCheck["lngmonetaryratecode"] = "null:number(1,2147483647)";
$aryCheck["lngmonetaryunitcode"] = "null:number(1,2147483647)";
$aryCheck["curconversionrate"]   = "null:number(0.000001,9999999999.999999)";
$aryCheck["dtmapplystartdate"]   = "null:date(/)";
$aryCheck["dtmapplyenddate"]     = "null:date(/)";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
//fncPutStringCheckError( $aryCheckResult, $objDB );

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
	$objMaster->setMasterTable( "m_MonetaryRate", "lngmonetaryratecode", $aryData["lngmonetaryratecode"], Array ( "lngmonetaryunitcode" => $aryData["lngmonetaryunitcode"], "dtmapplystartdate" => $aryData["dtmapplystartdate"] ), $objDB );
	$objMaster->setAryMasterInfo( $aryData["lngmonetaryratecode"], $aryData["lngmonetaryunitcode"] );

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
	$objMaster->aryCheckQuery["INSERT"] .= " AND NOT ( " . $objMaster->aryColumnName[4] . " < '" . $aryData[$objMaster->aryColumnName[3]] . "' OR " . $objMaster->aryColumnName[3] . " > '" . $aryData[$objMaster->aryColumnName[4]] . "' )";

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
$count = count ( $objMaster->aryColumnName );


$aryParts["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
$aryParts["lngActionCode"]   = $aryData["lngActionCode"];
$aryParts["strTableName"]    = $objMaster->strTableName;
$aryParts["strKeyName"]      = $objMaster->aryColumnName[0];
$aryParts["lngKeyCode"]      = $aryData[$objMaster->aryColumnName[0]];
$aryParts["strSessionID"]    = $aryData["strSessionID"];


// lngMonetaryRateCode ��(CODE+NAME)����
$aryMonetaryRateCode = fncGetMasterValue( "m_MonetaryRateClass", "lngMonetaryRateCode", "strMonetaryRateName", "Array", "", $objDB );
// lngMonetaryUnitCode ��(CODE+NAME)����
$aryMonetaryUnitCode = fncGetMasterValue( "m_MonetaryUnit", "lngMonetaryUnitCode", "strMonetaryUnitName || ':' || strMonetaryUnitSign", "Array", "", $objDB );

$aryParts["MASTER"] .= "				<tr><td id=\"Column0\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . $aryMonetaryRateCode[$aryData[$objMaster->aryColumnName[0]]] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[0] . "\" value=\"" . $aryData[$objMaster->aryColumnName[0]] . "\">\n";

$aryParts["MASTER"] .= "				<tr><td id=\"Column1\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">" . $aryMonetaryUnitCode[$aryData[$objMaster->aryColumnName[1]]] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[1] . "\" value=\"" . $aryData[$objMaster->aryColumnName[1]] . "\">\n";

for ( $i = 2; $i < $count; $i++ )
{
	$aryParts["MASTER"] .= "				<tr><td id=\"Column$i\" class=\"SegColumn\" width=\"25%\">Column$i</td><td class=\"Segs\">" . $aryData[$objMaster->aryColumnName[$i]] . "</td></tr>\n";
	$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[$i] . "\" value=\"" . $aryData[$objMaster->aryColumnName[$i]] . "\">\n";
}


if ( $bytErrorFlag )
{
	//echo fncGetReplacedHtml( "m/regist/edit.tmpl", $aryData, $objAuth );
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=euc-jp\">";
	echo "<form action=/m/regist/r/edit.php method=GET>";
	echo getArrayTable( $aryData, "HIDDEN" );
	echo "</form>";
	echo "<script language=javascript>document.forms[0].submit();</script>";
}
else
{
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "m/regist/c/confirm.tmpl" );
	$objTemplate->replace( $aryParts );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;
}


$objDB->close();


return TRUE;
?>


