<?
/** 
*	�ޥ������� �̲ߥ졼�ȥޥ��� �ǡ������ϲ���
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// ��Ͽ����
// index.php -> strSessionID        -> edit.php
// index.php -> lngActionCode       -> edit.php
//
// ��������
// index.php -> strSessionID        -> edit.php
// index.php -> lngActionCode       -> edit.php
// index.php -> lngmonetaryratecode -> edit.php
// index.php -> lngmonetaryunitcode -> edit.php
// index.php -> dtmapplystartdate   -> edit.php
//
// ��ǧ���̤�
// edit.php -> strSessionID        -> confirm.php
// edit.php -> lngActionCode       -> confirm.php
// edit.php -> lngmonetaryratecode -> confirm.php
// edit.php -> lngmonetaryunitcode -> confirm.php
// edit.php -> curconversionrate   -> confirm.php
// edit.php -> dtmapplystartdate   -> confirm.php
// edit.php -> dtmapplyenddate     -> confirm.php


// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POST�ǡ�������
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

if ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE )
{
	$aryCheck["lngmonetaryratecode"] = "null:number(1,2147483647)";
	$aryCheck["lngmonetaryunitcode"] = "null:number(1,2147483647)";
	$aryCheck["dtmapplystartdate"]   = "null:date(/)";
}

// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );


// ���顼���ʤ���硢�ޥ��������֥�������������ʸ��������å��¹�
if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�ޥ�����������", TRUE, "", $objDB );
}

// �ޥ��������֥�����������
$objMaster = new clsMaster();

//////////////////////////////////////////////////////////////////////////
// ���������ɤ�ɽ������
//////////////////////////////////////////////////////////////////////////
// �����ξ�硢�������Ϲ�������
if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
{
	// �̲ߥ졼�ȥޥ�������ԥǡ����ȥ����̾����
	$objMaster->setMasterTable( "m_MonetaryRate", "lngmonetaryratecode", NULL, Array (), $objDB );

	// ����������
	$lngColumnNum = count ( $objMaster->aryColumnName );

	// �̲ߥ졼��
	$aryParts["MASTER"][0]  = "<span class=\"InputSegs\"><select id=\"Input0\" name=\"" . $objMaster->aryColumnName[0] . "\" onChange=\"subLoadMasterText( 'cnMonetaryRate', this, document.forms[0]." . $objMaster->aryColumnName[3] . ", Array(this.value, document.forms[0]." . $objMaster->aryColumnName[1] . ".value), objDataSourceSetting, 0 );subLoadMasterText( 'cnMonetaryRate2', this, document.forms[0]." . $objMaster->aryColumnName[4] . ", Array(document.forms[0]." . $objMaster->aryColumnName[0] . ".value, document.forms[0]." . $objMaster->aryColumnName[1] . ".value), objDataSourceSetting2, 1 );\">\n";
	$aryParts["MASTER"][0] .= fncGetPulldown( "m_MonetaryRateClass", "lngMonetaryRateCode", "strMonetaryRateName", $aryData["lngmonetaryratecode"], "", $objDB );
	$aryParts["MASTER"][0] .= "</select></span>\n";

	// �̲�ñ��
	$aryParts["MASTER"][1]  = "<span class=\"InputSegs\"><select id=\"Input1\" name=\"" . $objMaster->aryColumnName[1] . "\" onChange=\"subLoadMasterText( 'cnMonetaryRate', this, document.forms[0]." . $objMaster->aryColumnName[3] . ", Array(document.forms[0]." . $objMaster->aryColumnName[0] . ".value, this.value), objDataSourceSetting, 0 );subLoadMasterText( 'cnMonetaryRate2', this, document.forms[0]." . $objMaster->aryColumnName[4] . ", Array(document.forms[0]." . $objMaster->aryColumnName[0] . ".value, document.forms[0]." . $objMaster->aryColumnName[1] . ".value), objDataSourceSetting2, 1 );\">\n";
	$aryParts["MASTER"][1] .= fncGetPulldown( "m_MonetaryUnit", "lngMonetaryUnitCode", "strMonetaryUnitName || ':' ||strMonetaryUnitSign", $aryData["lngmonetaryunitcode"], "WHERE lngMonetaryUnitCode > 1", $objDB );
	$aryParts["MASTER"][1] .= "</select></span>\n";

	// Ŭ�ѳ��Ϸ�
	$aryParts["MASTER"][3] = "<span class=\"InputSegs\"><input id=\"Input3\" type=\"text\" name=\"" . $objMaster->aryColumnName[3] . "\" value=\"" . $aryData[$objMaster->aryColumnName[3]]. "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"10\"></span>\n";

	// �����졼�Ȥ���äƤ����ݤΥǡ������Ǽ
	$objMaster->aryData[0][$objMaster->aryColumnName[2]] = $aryData[$objMaster->aryColumnName[2]];

	// Ŭ�ѽ�λ�����äƤ����ݤΥǡ������Ǽ
	$objMaster->aryData[0][$objMaster->aryColumnName[4]] = $aryData[$objMaster->aryColumnName[4]];
}

// ��Ͽ�ʳ��ξ�硢�������Ϲ��ܤ˥��⡼����ݤ���
elseif ( $aryData["lngActionCode"] != DEF_ACTION_INSERT )
{
	// �̲ߥ졼�ȥޥ����������ԥǡ����ȥ����̾����
	$objMaster->setMasterTable( "m_MonetaryRate", "lngmonetaryratecode", $aryData["lngmonetaryratecode"], Array ( "lngmonetaryunitcode" => $aryData["lngmonetaryunitcode"], "dtmapplystartdate" => $aryData["dtmapplystartdate"] ), $objDB );

	// ����������
	$lngColumnNum = count ( $objMaster->aryColumnName );

	// ����������ɽ��
	$aryData["HIDDEN"]  = "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[0] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[0]] . "\">\n";
	$aryData["HIDDEN"] .= "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[1] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[1]] . "\">\n";
	$aryData["HIDDEN"] .= "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[3] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[3]] . "\">\n";

	// �̲ߥ졼��
	$aryParts["MASTER"][0]  = "<span class=\"InputSegs\"><select id=\"Input0\" disabled>\n";
	$aryParts["MASTER"][0] .= fncGetPulldown( "m_MonetaryRateClass", "lngMonetaryRateCode", "strMonetaryRateName", $objMaster->aryData[0][$objMaster->aryColumnName[0]], "", $objDB );
	$aryParts["MASTER"][0] .= "</select></span>\n";

	// �̲�ñ��
	$aryParts["MASTER"][1]  = "<span class=\"InputSegs\"><select id=\"Input1\" disabled>\n";
	$aryParts["MASTER"][1] .= fncGetPulldown( "m_MonetaryUnit", "lngMonetaryUnitCode", "strMonetaryUnitName || ':' ||strMonetaryUnitSign", $objMaster->aryData[0][$objMaster->aryColumnName[1]], "", $objDB );
	$aryParts["MASTER"][1] .= "</select></span>\n";

	// Ŭ�ѳ��Ϸ�
	$aryParts["MASTER"][3] = "<span class=\"InputSegs\"><input id=\"Input3\" type=\"text\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[3]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" disabled></span>\n";}

// ������Ͽ�������������Ϲ���
// �����졼��
$aryParts["MASTER"][2] = "<span class=\"InputSegs\"><input id=\"Input2\" type=\"text\" name=\"" . $objMaster->aryColumnName[2] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[2]]. "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"10\"></span>\n";

// Ŭ�ѽ�λ��
$aryParts["MASTER"][4] = "<span class=\"InputSegs\"><input id=\"Input4\" type=\"text\" name=\"" . $objMaster->aryColumnName[4] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[4]]. "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"10\"></span>\n";

// �����ɽ��
$aryData["COLUMN"] = "<span id=\"Column0\" class=\"ColumnSegs\"></span>\n<span id=\"Column1\" class=\"ColumnSegs\"></span>\n<span id=\"Column2\" class=\"ColumnSegs\"></span>\n<span id=\"Column3\" class=\"ColumnSegs\"></span>\n<span id=\"Column4\" class=\"ColumnSegs\"></span>\n";


//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////
//echo fncGetReplacedHtml( "m/regist/parts.tmpl", $aryData, $objAuth );
$count = count ( $aryParts["MASTER"] );
for ( $i = 0; $i < $count; $i++ )
{
	$aryData["MASTER"] .= $aryParts["MASTER"][$i];
}

$objDB->close();


$aryData["lngLanguageCode"] = 1;
$aryData["strTableName"]    = $objMaster->strTableName;

// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/regist/c/edit.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;


return TRUE;
?>
