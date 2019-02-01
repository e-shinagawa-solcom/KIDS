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
$aryCheck["lngmonetaryratecode"] = "null:number(1,2147483647)";
$aryCheck["lngmonetaryunitcode"] = "null:number(1,2147483647)";
$aryCheck["curconversionrate"]   = "null:number(0.000001,9999999999.999999)";
$aryCheck["dtmapplystartdate"]   = "null:date(/)";
$aryCheck["dtmapplyenddate"]     = "null:date(/)";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );


// �ޥ��������֥�����������
$objMaster = new clsMaster();
$objMaster->setMasterTable( "m_MonetaryRate", "lngmonetaryratecode", $aryData["lngmonetaryratecode"], Array ( "lngmonetaryunitcode" => $aryData["lngmonetaryunitcode"], "dtmapplystartdate" => $aryData["dtmapplystartdate"] ), $objDB );
$objMaster->setAryMasterInfo( $aryData["lngmonetaryratecode"], $aryData["lngmonetaryunitcode"] );



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
	$objMaster->aryCheckQuery["INSERT"] .= " AND NOT ( " . $objMaster->aryColumnName[4] . " < '" . $aryData[$objMaster->aryColumnName[3]] . "' OR " . $objMaster->aryColumnName[3] . " > '" . $aryData[$objMaster->aryColumnName[4]] . "' )";

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
		$count = count ( $objMaster->aryColumnName );

		// INSERT VALUES ����
		for ( $i = 0; $i < $count; $i++ )
		{
			// TEXT �����ä���硢���������ղ�
			if ( $objMaster->aryType[$i] == "text" || $objMaster->aryType[$i] == "date" )
			{
				$aryValue[$i] = "'" . $aryData[$objMaster->aryColumnName[$i]] . "'";
			}
			else
			{
				$aryValue[$i] = $aryData[$objMaster->aryColumnName[$i]];
			}
		}

		$aryQuery[] = "INSERT INTO " . $objMaster->strTableName . " VALUES ( " . join ( ", ", $aryValue ) . ")";
	}

	// ��������(UPDATE)
	elseif ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE )
	{
		$count = count ( $objMaster->aryColumnName );

		// UPDATE VALUES ����
		for ( $i = 2; $i < $count; $i++ )
		{
			// TEXT �����ä���硢���������ղ�
			if ( $objMaster->aryType[$i] == "text" || $objMaster->aryType[$i] == "date" )
			{
				$aryValue[$i] = $objMaster->aryColumnName[$i] . " = '" . $aryData[$objMaster->aryColumnName[$i]] . "'";
			}
			else
			{
				$aryValue[$i] = $objMaster->aryColumnName[$i] . " = " . $aryData[$objMaster->aryColumnName[$i]];
			}
		}

		// ��å�
		$aryQuery[] = "SELECT * FROM " . $objMaster->strTableName . " WHERE " . $objMaster->aryColumnName[0] . " = " . $aryData[$objMaster->aryColumnName[0]] . " AND " . $objMaster->aryColumnName[1] . " = " . $aryData[$objMaster->aryColumnName[1]] . " AND " . $objMaster->aryColumnName[3] . " = '" . $aryData[$objMaster->aryColumnName[3]] . "' FOR UPDATE";

		// UPDATE ������
		$aryQuery[] = "UPDATE " . $objMaster->strTableName . " SET " . join ( ", ", $aryValue ) . " WHERE " . $objMaster->aryColumnName[0] . " = " . $aryData[$objMaster->aryColumnName[0]] . " AND " . $objMaster->aryColumnName[1] . " = " . $aryData[$objMaster->aryColumnName[1]] . " AND " . $objMaster->aryColumnName[3] . " = '" . $aryData[$objMaster->aryColumnName[3]] . "'";
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
	echo "<script language=javascript>window.returnValue=true;window.close();</script>";
}


$objDB->close();


return TRUE;
?>


