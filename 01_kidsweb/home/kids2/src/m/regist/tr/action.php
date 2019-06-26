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
$aryCheck["lngmonetaryunitcode"] = "null:number(1,2147483647)";
$aryCheck["curconversionrate"]   = "null:number(0.000001,9999999999.999999)";
$aryCheck["dtmapplystartdate"]   = "null:date(/)";
$aryCheck["dtmapplyenddate"]     = "null:date(/)";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );


// �ޥ��������֥�����������
$objMaster = new clsMaster();
$objMaster->setMasterTable( "m_TemporaryRate", "lngmonetaryunitcode", $aryData["lngmonetaryunitcode"], Array ( "dtmapplystartdate" => $aryData["dtmapplystartdate"] ), $objDB );
$objMaster->setAryMasterInfo( $aryData["lngmonetaryunitcode"], "" );



//////////////////////////////////////////////////////////////////////////
// ������ͭ����������å�
//////////////////////////////////////////////////////////////////////////
// ( ��Ͽ �ޤ��� ���� ) ���� �����˥��顼���ʤ� ��硢
// ������Ͽ�����������å��¹�
if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT || $aryData["lngActionCode"] == DEF_ACTION_UPDATE ) && !join ( $aryCheckResult ) )
{
	// �����å�����������
	// AND NOT ( ��λǯ���� < ���ϳ���ǯ���� OR ����ǯ���� > ���Ͻ�λǯ���� )
	// ����ɲ�
	$objMaster->aryCheckQuery["INSERT"] .= " AND NOT ( dtmapplystartdate < '" . $aryData["dtmapplystartdate"] . "' OR dtmapplyenddate > '" . $aryData["dtmapplyenddate"] . "' )";

	 list ( $lngResultID, $lngResultNum ) = fncQuery ( $objMaster->aryCheckQuery["INSERT"], $objDB );

	// ������Ͽ ���� ��̷����0�ʾ�
	// �ޤ���
	// ���� ���� ��̷����1�ʳ� �ξ�硢���顼
	if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT && $lngResultNum > 0 ) || ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE && $lngResultNum != 1 ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�ޥ�����������", TRUE, "", $objDB );
	}

	// ��Ͽ����(INSERT)
	elseif ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
	{
		$aryQuery[] = "INSERT INTO m_TemporaryRate VALUES ( " . $aryData["lngmonetaryunitcode"] . ", " . $aryData["curconversionrate"] . ", '". $aryData["dtmapplystartdate"] . "', '". $aryData["dtmapplyenddate"] . "')";
	}

	// ��������(UPDATE)
	elseif ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE )
	{
		// ��å�
		$aryQuery[] = "SELECT * FROM m_TemporaryRate WHERE lngmonetaryunitcode = '" . $aryData["lngmonetaryunitcode"] . "' AND dtmapplystartdate = '" . $aryData["dtmapplystartdate"]  . "' FOR UPDATE";

		// UPDATE ������
		$aryQuery[] = "UPDATE m_TemporaryRate SET dtmapplyenddate = '" . $aryData["dtmapplyenddate"] . "', curconversionrate = " . $aryData["curconversionrate"] . " WHERE lngmonetaryunitcode = '" . $aryData["lngmonetaryunitcode"] . "' AND dtmapplystartdate = '" . $aryData["dtmapplystartdate"] . "'";
	}
}



////////////////////////////////////////////////////////////////////////////
// ������¹�
////////////////////////////////////////////////////////////////////////////
$objDB->transactionBegin();

for ( $i = 0; $i < count ( $aryQuery ); $i++ )
{
	echo "<p>$aryQuery[$i]</p>\n";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $aryQuery[$i], $objDB );
}

$objDB->transactionCommit();

// ���顼����ɽ������
list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );


//////////////////////////////////////////////////////////////////////////
// ����
//////////////////////////////////////////////////////////////////////////
if ( $bytErrorFlag )
{
	fncOutputError ( 9052, DEF_WARNING, "�ޥ�����������", TRUE, "", $objDB );
}
else
{
	echo "<script language=javascript>window.returnValue=true;window.open('about:blank','_parent').close();</script>";
}


$objDB->close();


return TRUE;
?>


