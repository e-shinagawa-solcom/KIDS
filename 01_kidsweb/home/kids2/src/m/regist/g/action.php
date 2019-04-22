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
// ��Ͽ�������¹�
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
// ����¹�
// confirm.php -> strSessionID  -> action.php
// confirm.php -> lngActionCode -> action.php
// confirm.php -> lnggroupcode  -> action.php


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
	$aryCheck["bytgroupdisplayflag"]  = "null:english(4,5)";
	$aryCheck["strgroupdisplaycode"]  = "null:numenglish(1,3)";
	$aryCheck["strgroupdisplayname"]  = "null:length(1,100)";
	$aryCheck["strgroupdisplaycolor"] = "null:color";
}


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );



//////////////////////////////////////////////////////////////////////////
// ������ͭ����������å�
//////////////////////////////////////////////////////////////////////////
// ( ��Ͽ �ޤ��� ���� ) ���� �����˥��顼���ʤ� ��硢
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
		fncOutputError ( 9052, DEF_WARNING, "�ޥ�����������", TRUE, "", $objDB );
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
			fncOutputError ( 9052, DEF_WARNING, "�ޥ�����������", TRUE, "", $objDB );
		}

		$objDB->freeResult( $lngResultID );
	}

	// ��������ɽ���ե饰��"FALSE"�ξ�硢�桼������°�����å��¹�
	if ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE && $aryData["bytgroupdisplayflag"] == "FALSE" )
	{
		$strQuery = "SELECT * FROM m_GroupRelation " .
	                "WHERE lngGroupCode = " . $aryData["lnggroupcode"];

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		// ��̷����1�ʾ�ξ�硢���顼
		if ( $lngResultNum > 0 )
		{
			$objDB->freeResult( $lngResultID );
			fncOutputError ( 9052, DEF_WARNING, "�ޥ�����������", TRUE, "", $objDB );
		}
	}

	// ��Ͽ����(INSERT)
	if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
	{

		//$aryData["lnggroupcode"] = fncGetSequence( "m_Group.lngGroupCode", $objDB );
		$aryQuery[] = "INSERT INTO m_Group VALUES ( " .
                      $aryData["lnggroupcode"] . ", " .
                      $aryData["lngcompanycode"] . ", '" .
                      $aryData["strgroupname"] . "', " .
                      $aryData["bytgroupdisplayflag"] . ", '" .
                      $aryData["strgroupdisplaycode"] . "', '" .
                      $aryData["strgroupdisplayname"] . "', '" .
                      $aryData["strgroupdisplaycolor"] . "'" .
                      " )";
	}

	// ��������(UPDATE)
	elseif ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE )
	{
		// ��å�
		$aryQuery[] = "SELECT * FROM m_Group WHERE lngGroupCode = " . $aryData["lnggroupcode"] . " FOR UPDATE";

		// UPDATE ������
		$aryQuery[] = "UPDATE m_Group SET" .
                      " lngCompanyCode = " . $aryData["lngcompanycode"] . "," .
                      " strGroupName = '" . $aryData["strgroupname"] . "'," .
                      " bytGroupDisplayFlag = " . $aryData["bytgroupdisplayflag"] . "," .
                      " strGroupDisplayCode = '" . $aryData["strgroupdisplaycode"] . "'," .
                      " strGroupDisplayName = '" . $aryData["strgroupdisplayname"] . "'," .
                      " strGroupDisplayColor = '" . $aryData["strgroupdisplaycolor"] . "'" .
                      "WHERE lngGroupCode = " . $aryData["lnggroupcode"];
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
	$aryQuery = Array();

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// ��̤�1��Ǥ⤢�ä���硢����Բ�ǽ�Ȥ������顼����
	if ( $lngResultNum > 0 )
	{
		fncOutputError ( 1201, DEF_WARNING, "�ޥ�����������", TRUE, "", $objDB );
	}

	// �������(DELETE)
	$aryQuery[] = "DELETE FROM m_Group WHERE lngGroupCode = " . $aryData["lnggroupcode"];
}



////////////////////////////////////////////////////////////////////////////
// ������¹�
// ////////////////////////////////////////////////////////////////////////////
$objDB->transactionBegin();

for ( $i = 0; $i < count ( $aryQuery ); $i++ )
{
	echo "<p>$aryQuery[$i]</p>\n";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $aryQuery[$i], $objDB );
}

$objDB->transactionCommit();


// $objDB->close();



//////////////////////////////////////////////////////////////////////////
// ����
//////////////////////////////////////////////////////////////////////////
echo "<script language=javascript>window.returnValue=true;window.open('about:blank','_parent').close();</script>";



return TRUE;
?>


