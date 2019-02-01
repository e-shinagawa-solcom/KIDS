<?
/** 
*	�ޥ������� ���롼�ץޥ��� �ǡ������ϲ���
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
// index.php -> lnggroupcode        -> edit.php
//
// ��ǧ���̤�
// edit.php -> strSessionID         -> confirm.php
// edit.php -> lngActionCode        -> confirm.php
// edit.php -> lnggroupcode         -> confirm.php
// edit.php -> lngcompanycode       -> confirm.php
// edit.php -> strgroupname         -> confirm.php
// edit.php -> bytgroupdisplayflag  -> confirm.php
// edit.php -> strgroupdisplaycode  -> confirm.php
// edit.php -> strgroupdisplayname  -> confirm.php
// edit.php -> strgroupdisplaycolor -> confirm.php


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
$objMaster->setMasterTable( "m_Group", "lnggroupcode", $aryData["lnggroupcode"], "", $objDB );
$objMaster->setAryMasterInfo( $aryData["lnggroupcode"], "" );



// ����������
$lngColumnNum = count ( $objMaster->aryColumnName );

//////////////////////////////////////////////////////////////////////////
// ������ɽ������
//////////////////////////////////////////////////////////////////////////
// �����ξ�硢�������Ϲ�������
if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
{
	// �ǡ��������
	$objMaster->aryData[0] = Array ();

	if ( !$aryData[$objMaster->aryColumnName[0]] )
	{
		$aryData[$objMaster->aryColumnName[0]] = $objMaster->lngRecordRow;
	}
	//$seq = fncIsSequence( $objMaster->strTableName . "." . $objMaster->aryColumnName[0], $objDB );

	// ���󥯥���ȸ�Υ������󥹤�9999���ä���礵���1­�������������
	//if ( ( $seq + 1 ) == 9999 )
	//{
	//	$seq = fncGetSequence( $objMaster->strTableName . "." . $objMaster->aryColumnName[0], $objDB );
	//}

	// ���롼�ץ����ɤΥ��å�
	$objMaster->aryData[0][$objMaster->aryColumnName[0]] =& $aryData[$objMaster->aryColumnName[0]];

	// ��ҥ����ɤΥ��å�
	$objMaster->aryData[0][$objMaster->aryColumnName[1]] =& $aryData[$objMaster->aryColumnName[1]];

	// ���롼��̾
	$objMaster->aryData[0][$objMaster->aryColumnName[2]] =& $aryData[$objMaster->aryColumnName[2]];

	// ɽ�����롼�ץե饰
	$objMaster->aryData[0][$objMaster->aryColumnName[3]] =& $aryData[$objMaster->aryColumnName[3]];

	// ɽ�����롼�ץ�����
	$objMaster->aryData[0][$objMaster->aryColumnName[4]] =& $aryData[$objMaster->aryColumnName[4]];

	// ɽ�����롼��̾��
	$objMaster->aryData[0][$objMaster->aryColumnName[5]] =& $aryData[$objMaster->aryColumnName[5]];

	// ���롼��ɽ����
	$objMaster->aryData[0][$objMaster->aryColumnName[6]] =& $aryData[$objMaster->aryColumnName[6]];
}

// ���롼��
if ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE )
{
	$lngOutputGroupCode =& $objMaster->aryData[0][$objMaster->aryColumnName[0]];
}
$aryParts["MASTER"][0]  = "<span class=\"InputSegs\"><input type=\"text\" id=\"Input0\" value=\"" . $lngOutputGroupCode . "\" disabled></span>\n";

$aryData["HIDDEN"]  = "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[0] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[0]] . "\">\n";

// �����ξ�硢���⡼���򤫤���
if ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE )
{
	$strQuery = "SELECT * FROM m_GroupRelation WHERE lngGroupCode = " . $aryData["lnggroupcode"];

	// ���̾(������)
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	if ( $lngResultNum > 0 )
	{
		$objDB->freeResult( $lngResultID );

		// ���̾(���Ǥ�°�Ԥ������硢���⡼����ݤ���)
		$aryParts["MASTER"][1]  = "<span class=\"InputSegs\"><select id=\"Input1\" disabled>\n";
		$aryParts["MASTER"][1] .= fncGetPulldown( "m_Company", "lngCompanyCode", "strCompanyName",  $objMaster->aryData[0][$objMaster->aryColumnName[1]], "WHERE bytCompanyDisplayFlag = TRUE", $objDB );
		$aryParts["MASTER"][1] .= "</select></span>\n";

		$aryData["HIDDEN"] .= "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[1] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[1]] . "\">\n";

		$flag = 1;
	}
}

// ���̾�������ɽ�����Ƥ��ʤ���硢ɽ��
if ( !$flag )
{
	// ���̾
	$aryParts["MASTER"][1]  = "<span class=\"InputSegs\"><select id=\"Input1\" name=\"" . $objMaster->aryColumnName[1] . "\">\n";
	$aryParts["MASTER"][1] .= fncGetPulldown( "m_Company", "lngCompanyCode", "strCompanyName",  $objMaster->aryData[0][$objMaster->aryColumnName[1]], "WHERE bytCompanyDisplayFlag = TRUE", $objDB );
	$aryParts["MASTER"][1] .= "</select></span>\n";
}
$flag = "";

// ���롼��̾
$aryParts["MASTER"][2] = "<span class=\"InputSegs\"><input id=\"Input2\" type=\"text\" name=\"" . $objMaster->aryColumnName[2] . "\" value=\"" . fncHTMLSpecialChars( $objMaster->aryData[0][$objMaster->aryColumnName[2]] ) . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// ɽ�����롼�ץե饰
if ( $objMaster->aryData[0][$objMaster->aryColumnName[3]] == "t" || $aryData["lngActionCode"] == DEF_ACTION_INSERT )
{
	$flag = " checked";
}
$aryParts["MASTER"][3] = "<span class=\"InputSegs\"><input id=\"Input3\" type=\"checkbox\" name=\"" . $objMaster->aryColumnName[3] . "\" value=\"t\"$flag></span>\n";
// ɽ�����롼�ץ�����
$aryParts["MASTER"][4] = "<span class=\"InputSegs\"><input id=\"Input4\" type=\"text\" name=\"" . $objMaster->aryColumnName[4] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[4]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"3\"></span>\n";

// ɽ�����롼��̾��
$aryParts["MASTER"][5] = "<span class=\"InputSegs\"><input id=\"Input5\" type=\"text\" name=\"" . $objMaster->aryColumnName[5] . "\" value=\"" . fncHTMLSpecialChars( $objMaster->aryData[0][$objMaster->aryColumnName[5]] ) . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// ���롼��ɽ����
$aryParts["MASTER"][6] = "<span class=\"InputSegs\"><input id=\"Input6\" type=\"text\" name=\"" . $objMaster->aryColumnName[6] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[6]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"7\"></span>\n";




//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////
//echo fncGetReplacedHtml( "m/regist/parts.tmpl", $aryData, $objAuth );
$count = count ( $aryParts["MASTER"] );
for ( $i = 0; $i < $count; $i++ )
{
	$aryData["MASTER"] .= $aryParts["MASTER"][$i];

	// �����ɽ��
	$aryData["COLUMN"] .= "<span id=\"Column$i\" class=\"ColumnSegs\"></span>\n";
}

$objDB->close();


$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
$aryData["strTableName"]    = $objMaster->strTableName;


// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/regist/c/edit.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;


return TRUE;
?>
