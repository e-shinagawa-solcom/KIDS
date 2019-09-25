<?
/** 
*	�ޥ������� ���롼�ץޥ��� ��ǧ����
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// ��Ͽ������
// edit.php -> strSessionID         -> confirm.php
// edit.php -> lngActionCode        -> confirm.php
// edit.php -> lnggroupcode         -> confirm.php
// edit.php -> lngcompanycode       -> confirm.php
// edit.php -> strgroupname         -> confirm.php
// edit.php -> bytgroupdisplayflag  -> confirm.php
// edit.php -> strgroupdisplaycode  -> confirm.php
// edit.php -> strgroupdisplayname  -> confirm.php
// edit.php -> strgroupdisplaycolor -> confirm.php
//
// ���
// index.php -> strSessionID         -> confirm.php
// index.php -> lngActionCode        -> confirm.php
// index.php -> lnggroupcode         -> confirm.php
//
// ��Ͽ�������¹Ԥ�
// confirm.php -> strSessionID         -> action.php
// confirm.php -> lngActionCode        -> action.php
// confirm.php -> lnggroupcode         -> action.php
// confirm.php -> lngcompanycode       -> action.php
// confirm.php -> strgroupname         -> action.php
// confirm.php -> bytgroupdisplayflag  -> action.php
// confirm.php -> strgroupdisplaycode  -> action.php
// confirm.php -> strgroupdisplayname  -> action.php
// confirm.php -> strgroupdisplaycolor -> action.php
//
// ����¹Ԥ�
// confirm.php -> strSessionID        -> action.php
// confirm.php -> lngActionCode       -> action.php
// confirm.php -> lnggroupcode        -> action.php


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

if ( $aryData["lngActionCode"] != DEF_ACTION_DELETE )
{
	// �����꤬�ʤ��ä���硢�ǥե���Ȥ��������
	if ( $aryData["strgroupdisplaycolor"] == "" )
	{
		$aryData["strgroupdisplaycolor"] = "#FFFFFF";
	}

	$aryCheck["lnggroupcode"]         = "null:number(0,2147483647)";
	$aryCheck["lngcompanycode"]       = "null:number(0,2147483647)";
	$aryCheck["strgroupname"]         = "null:length(1,100)";
	$aryCheck["bytgroupdisplayflag"]  = "english(1,1)";
	$aryCheck["strgroupdisplaycode"]  = "null:numenglish(1,3)";
	$aryCheck["strgroupdisplayname"]  = "null:length(1,100)";
	$aryCheck["strgroupdisplaycolor"] = "null:color";
}

// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
//fncPutStringCheckError( $aryCheckResult, $objDB );



//////////////////////////////////////////////////////////////////////////
// ������ͭ����������å�
//////////////////////////////////////////////////////////////////////////
// ( ��Ͽ �ޤ��� ���� ) ���顼���ʤ� ��硢
// ������Ͽ�����������å��¹�
if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT || $aryData["lngActionCode"] == DEF_ACTION_UPDATE ) && !join ( $aryCheckResult ) )
{
	// ���롼�ץ����ɽ�ʣ�����å�
	$strQuery = "SELECT * FROM m_Group " .
                "WHERE lngGroupCode = " . $aryData["lnggroupcode"];

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// ������Ͽ ���� ��̷����0�ʾ�
	// �ޤ���
	// ���� ���� ��̷����1�ʳ� �ξ�硢���顼
	if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT && $lngResultNum > 0 ) || ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE && $lngResultNum != 1 ) )
	{
		$aryCheckResult["lnggroupcode_Error"] = 1;
		$objDB->freeResult( $lngResultID );
	}

	// Ʊ�������ˤ�����ɽ�����롼�ץ����ɽ�ʣ�����å�
	$strQuery = "SELECT * FROM m_Group " .
                "WHERE lngCompanyCode = " . $aryData["lngcompanycode"] .
                " AND strGroupDisplayCode = '" . $aryData["strgroupdisplaycode"] . "'";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// ��̷����0�ʾ�ξ�硢���顼Ƚ�������
	if ( $lngResultNum > 0 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );

		// ( ���� ���� ���롼�ץ����ɤ�Ʊ�� ) �ʳ� �ξ�硢���顼
		if ( !( $aryData["lngActionCode"] == DEF_ACTION_UPDATE && $objResult->lnggroupcode == $aryData["lnggroupcode"] ) )
		{
			$aryCheckResult["strgroupdisplaycode_Error"] = 1;
		}

		$objDB->freeResult( $lngResultID );
	}

	// ��������ɽ���ե饰��OFF�ξ�硢�桼������°�����å��¹�
	if ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE && !$aryData["bytgroupdisplayflag"] )
	{
		$strQuery = "SELECT * FROM m_GroupRelation " .
	                "WHERE lngGroupCode = " . $aryData["lnggroupcode"];

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		// ��̷����1�ʾ�ξ�硢���顼
		if ( $lngResultNum > 0 )
		{
			$aryCheckResult["lnggroupcode_Error"] = 1;
			$objDB->freeResult( $lngResultID );
		}
	}
}

// ��� ���� ���顼���ʤ� ��硢
// ��������å��¹�
elseif ( $aryData["lngActionCode"] == DEF_ACTION_DELETE && !join ( $aryCheckResult ) )
{
	// �����å��оݥơ��֥�̾��������
	$aryTableName = Array ( "m_GroupRelation", "m_Order", "m_Receive", "m_Sales", "m_Stock" );

	// �����å�����������
	for ( $i = 0; $i < count ( $aryTableName ); $i++ )
	{
		$aryQuery[] = "SELECT lngGroupCode FROM " . $aryTableName[$i] . " WHERE lngGroupCode = " . $aryData["lnggroupcode"];
	}
	$aryQuery[] = "SELECT lngInChargeGroupCode FROM m_Product WHERE lngInChargeGroupCode = " . $aryData["lnggroupcode"] ." OR lngCustomerGroupCode = " . $aryData["lnggroupcode"];

	$strQuery = join ( " UNION ", $aryQuery );

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// ��̤�1��Ǥ⤢�ä���硢����Բ�ǽ�Ȥ������顼����
	// if ( $lngResultNum > 0 )
	// {
	// 	$objDB->freeResult( $lngResultID );
	// 	fncOutputError ( 1201, DEF_WARNING, "�ޥ�����������", TRUE, "", $objDB );
	// }

	// ����о�ɽ���Τ���Υǡ��������
	$strQuery = "SELECT * FROM m_Group WHERE lngGroupCode = " . $aryData["lnggroupcode"];

	$objMaster = new clsMaster();
	$objMaster->setMasterTableData( $strQuery, $objDB );

	$aryKeys = array_keys ( $objMaster->aryData[0] );

	foreach ( $aryKeys as $strKey )
	{
		$aryData[$strKey] = $objMaster->aryData[0][$strKey];
	}

	$objMaster = new clsMaster();
	$aryKeys = Array ();
}

// ���顼����ɽ������
list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );


// ���롼��ɽ���ե饰����
if ( $aryData["bytgroupdisplayflag"] == "t" )
{
	$aryData["bytgroupdisplayflag"] = "TRUE";
}
else
{
	$aryData["bytgroupdisplayflag"] = "FALSE";
}

//////////////////////////////////////////////////////////////////////////
// ����
//////////////////////////////////////////////////////////////////////////
$aryParts["lngLanguageCode"] =1;
$aryParts["lngActionCode"]   =& $aryData["lngActionCode"];
$aryParts["strTableName"]    =  "m_Group";
$aryParts["strKeyName"]      =  "lnggroupcode";
$aryParts["lngKeyCode"]      =& $aryData["lnggroupcode"];
$aryParts["strSessionID"]    =& $aryData["strSessionID"];


// lngCompanyCode ��(CODE+NAME)����
$aryCompanyCode = fncGetMasterValue( "m_Company", "lngCompanyCode", "strCompanyName", "Array", "", $objDB );
// bytGroupDisplayFlag ��(CODE+NAME)����
$aryGroupDisplayFlag = Array ( "TRUE" => "ɽ��", "FALSE" => "��ɽ��" );

if ( $aryData["lngActionCode"] != DEF_ACTION_INSERT )
{
	$lngOutputGroupCode =& $aryData["lnggroupcode"];
}
$aryParts["MASTER"] .= "				<tr><td id=\"Column0\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . $lngOutputGroupCode . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"lnggroupcode\" value=\"" . $aryData["lnggroupcode"] . "\">\n";

$aryParts["MASTER"] .= "				<tr><td id=\"Column1\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">" . $aryCompanyCode[$aryData["lngcompanycode"]] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"lngcompanycode\" value=\"" . $aryData["lngcompanycode"] . "\">\n";

$aryParts["MASTER"] .= "				<tr><td id=\"Column2\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">" . fncHTMLSpecialChars( $aryData["strgroupname"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strgroupname\" value=\"" . fncHTMLSpecialChars( $aryData["strgroupname"] ) . "\">\n";

$aryParts["MASTER"] .= "				<tr><td id=\"Column3\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">" . $aryGroupDisplayFlag[$aryData["bytgroupdisplayflag"]] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"bytgroupdisplayflag\" value=\"" . $aryData["bytgroupdisplayflag"] . "\">\n";

$aryParts["MASTER"] .= "				<tr><td id=\"Column4\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">" . $aryData["strgroupdisplaycode"] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strgroupdisplaycode\" value=\"" . $aryData["strgroupdisplaycode"] . "\">\n";

$aryParts["MASTER"] .= "				<tr><td id=\"Column5\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">" . fncHTMLSpecialChars( $aryData["strgroupdisplayname"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strgroupdisplayname\" value=\"" . fncHTMLSpecialChars( $aryData["strgroupdisplayname"] ) . "\">\n";

$aryParts["MASTER"] .= "				<tr><td id=\"Column6\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">" . $aryData["strgroupdisplaycolor"] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strgroupdisplaycolor\" value=\"" . $aryData["strgroupdisplaycolor"] . "\">\n";


if ( $bytErrorFlag )
{
	//echo fncGetReplacedHtml( "m/regist/edit.tmpl", $aryData, $objAuth );
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=euc-jp\">";
	echo "<form action=/m/regist/g/edit.php method=GET>";
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


