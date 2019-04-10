<?
/** 
*	�ޥ������� ���̥ޥ��� ��ǧ����
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// edit.php -> strSessionID          -> confirm.php
// edit.php -> lngActionCode         -> confirm.php
// edit.php -> strMasterTableName    -> confirm.php
// edit.php -> strKeyName            -> confirm.php
// edit.php -> lngKeyCode            -> confirm.php
// edit.php -> (lngStockSubjectCode) -> confirm.php

// �¹Ԥ�
// confirm.php -> strSessionID          -> action.php
// confirm.php -> lngActionCode         -> action.php
// confirm.php -> strMasterTableName    -> action.php
// confirm.php -> strKeyName            -> action.php
// confirm.php -> lngKeyCode            -> action.php
// confirm.php -> (lngStockSubjectCode) -> action.php


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


// �������ʤξ��˻��Ѥ���lngStockSubjectCode������
list ( $aryData["lngstocksubjectcode"], $i ) = mb_split ( ":", $aryData["lngstocksubjectcode"] );


// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]       = "null:numenglish(32,32)";
$aryCheck["strMasterTableName"] = "null:ascii(1,32)";
$aryCheck["strKeyName"]         = "ascii(1,32)";
$aryCheck[$aryData["strKeyName"]] = "null:number(,2147483647)";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );



// �ޥ��������֥�����������
$objMaster = new clsMaster();
$objMaster->setMasterTable( $aryData["strMasterTableName"], $aryData["strKeyName"], $aryData[$aryData["strKeyName"]], Array ( "lngstocksubjectcode" => $aryData["lngstocksubjectcode"] ), $objDB );
$objMaster->setAryMasterInfo( $aryData[$aryData["strKeyName"]], $aryData["lngstocksubjectcode"] );




// ���ϥǡ�����ʸ��������å�
if ( $aryData["lngActionCode"] != DEF_ACTION_DELETE )
{
	// ��ʸ��������å�
	$aryCheckResult = fncAllCheck( $aryData, $objMaster->aryCheck );
}
elseif ( $aryData["lngActionCode"] == DEF_ACTION_DELETE )
{
	// ����ξ�硢���������ɤΤߥ����å�
	$aryCheck[$objMaster->strColumnName[0]] = $objMaster->aryCheck[$objMaster->strColumnName[0]];
	//$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
}


//////////////////////////////////////////////////////////////////////////
// ������ͭ����������å�
//////////////////////////////////////////////////////////////////////////
// ( ��Ͽ �ޤ��� ���� ) ���� �����˥��顼���ʤ� ��硢
// ������Ͽ�����������å��¹�
if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT || $aryData["lngActionCode"] == DEF_ACTION_UPDATE ) && !$aryCheckResult[$aryData["strKeyName"] . "_Error"] )
{
	list ( $lngResultID, $lngResultNum ) = fncQuery ( $objMaster->aryCheckQuery["INSERT"], $objDB );

	// ������Ͽ ���� ��̷����0�ʾ�
	// �ޤ���
	// ���� ���� ��̷����1�ʳ� �ξ�硢���顼
	if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT && $lngResultNum > 0 ) || ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE && $lngResultNum != 1 ) )
	{
		$aryCheckResult[$aryData["strKeyName"] . "_Error"] = 1;
	}
}

// ����ξ�硢��������å��¹�
elseif ( $aryData["lngActionCode"] == DEF_ACTION_DELETE )
{
	$count = count ( $objMaster->aryCheckQuery["DELETE"] );
	for ( $i = 0; $i < $count; $i++ )
	{
		$strQuery = $objMaster->aryCheckQuery["DELETE"][$i];

		list ( $lngResultID, $lngResultNum ) = fncQuery ( $strQuery, $objDB );
		if ( $lngResultNum > 0 )
		{
			fncOutputError ( 1201, DEF_WARNING, "�ޥ�����������", TRUE, "", $objDB );
		}
	}
}


// ���顼����ɽ������
list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );


//////////////////////////////////////////////////////////////////////////
// ����
//////////////////////////////////////////////////////////////////////////
$count = count ( $objMaster->aryColumnName );

if ( $aryData["lngActionCode"] == DEF_ACTION_DELETE )
{
	for ( $i = 0; $i < $count; $i++ )
	{
		$aryData[$objMaster->aryColumnName[$i]] = $objMaster->aryData[0][$objMaster->aryColumnName[$i]];
	}
}

$aryParts["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
$aryParts["lngActionCode"]   = $aryData["lngActionCode"];
$aryParts["strTableName"]    = $objMaster->strTableName;
$aryParts["strKeyName"]      = $aryData["strKeyName"];
$aryParts["lngKeyCode"]      = $aryData["lngKeyCode"];
$aryParts["strSessionID"]    = $aryData["strSessionID"];


$aryData = fncToHTMLString( $aryData );

// �����ʬ�����ơ��֥������
for ( $i = 0; $i < $count; $i++ )
{
	// �ǽ�Υ���� ���� ������Ͽ ���� �������ܥޥ����ǤϤʤ� ����
	// �������ʥޥ����ǤϤʤ� ���� ��ޥ����ǤϤʤ� ���� �ȿ��ޥ����ǤϤʤ�
	// ��硢ɽ�����ʤ�
	if ( $i == 0 && $aryData["lngActionCode"] == DEF_ACTION_INSERT && $aryData["strMasterTableName"] != "m_StockSubject" && $aryData["strMasterTableName"] != "m_StockItem" && $aryData["strMasterTableName"] != "m_Country" && $aryData["strMasterTableName"] != "m_Organization" )
	{
		$aryMaster[] = "<tr><td id=\"Column$i\" class=\"SegColumn\" width=\"25%\">Column$i</td><td class=\"Segs\"></td></tr>\n";
	}
	else
	{
		$aryMaster[] = "<tr><td id=\"Column$i\" class=\"SegColumn\" width=\"25%\">Column$i</td><td class=\"Segs\">" . $aryData[$objMaster->aryColumnName[$i]] . "</td></tr>\n";
	}
	$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[$i] . "\" value=\"" . $aryData[$objMaster->aryColumnName[$i]] . "\">\n";
}

// �������ܤξ�硢�֥����ɡ�̾�Ρ��Ѵ�
if ( $objMaster->strTableName == "m_StockSubject" )
{
	$strName = fncGetMasterValue( "m_StockClass", "lngStockClassCode", "strStockClassName", $aryData[$objMaster->aryColumnName[1]], "", $objDB );
	$aryMaster[1] = "<tr><td id=\"Column1\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">$strName</td></tr>\n";
}

// �������ʤξ�硢�֥����ɡ�̾�Ρ��Ѵ�
if ( $objMaster->strTableName == "m_StockItem" )
{
	$strName = fncGetMasterValue( "m_StockSubject", "lngStockSubjectCode", "strStockSubjectName", $aryData[$objMaster->aryColumnName[1]], "", $objDB );
	$aryMaster[1] = "<tr><td id=\"Column1\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">$strName</td></tr>\n";
}


// ɽ���ޥ���������η��
$aryParts["MASTER"] = join ( "", $aryMaster );


if ( $bytErrorFlag )
{
	//echo fncGetReplacedHtml( "m/regist/edit.tmpl", $aryData, $objAuth );
	echo "<form action=/m/regist/c/edit.php method=GET>";
	//echo getArrayTable( $aryData, "TABLE" );exit;
	echo getArrayTable( $aryData, "HIDDEN" );
	echo "</form>";
	echo "<script language=javascript>document.forms[0].submit();</script>";
}
else
{
	//echo getArrayTable( $aryData, "TABLE" );exit;
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "m/regist/c/confirm.tmpl" );
	$objTemplate->replace( $aryParts );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;
}


$objDB->close();


return TRUE;
?>


