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
// confirm.php -> strSessionID       -> action.php
// confirm.php -> lngActionCode      -> action.php
// confirm.php -> strMasterTableName -> action.php
// confirm.php -> strKeyName         -> action.php
// confirm.php -> *(�����̾)        -> action.php


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


$aryCheck["strSessionID"]       = "null:numenglish(32,32)";
$aryCheck["strMasterTableName"] = "null:ascii(1,32)";
$aryCheck["strKeyName"]         = "ascii(1,32)";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );



// �ޥ��������֥�����������
$objMaster = new clsMaster();
$objMaster->setMasterTable( $aryData["strMasterTableName"], $aryData["strKeyName"], $aryData[$aryData["strKeyName"]], Array ( "lngstocksubjectcode" => $aryData["lngstocksubjectcode"] ), $objDB );
$objMaster->setAryMasterInfo( $aryData[$aryData["strKeyName"]], $aryData["lngstocksubjectcode"] );

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
		fncOutputError ( 9052, DEF_WARNING, "�ޥ�����������", TRUE, "", $objDB );
	}

	// ��Ͽ����(INSERT)
	elseif ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
	{
		$count = count ( $objMaster->aryColumnName );

		// �������ܥޥ������������ʥޥ�������ޥ����ʳ���
		// ���ꥢ��ˤƿ���������ȯ��
		//if ( $objMaster->strTableName != "m_StockSubject" && $objMaster->strTableName != "m_StockItem" && $objMaster->strTableName != "m_Country" )
		//{
			//$aryValue[0] = fncGetSequence ( $objMaster->strTableName . "." . $objMaster->aryColumnName[0], $objDB );
		//	$aryValue[0] = $objMaster->lngRecordRow + 1;
		//}
		//else
		//{
			$aryValue[0] = $aryData[$objMaster->aryColumnName[0]];
		//}
		// INSERT VALUES ����
		for ( $i = 1; $i < $count; $i++ )
		{
			// TEXT �����ä���硢���������ղ�
			if ( $objMaster->aryType[$i] == "text" || $objMaster->aryType[$i] == "bool" )
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
		for ( $i = 1; $i < $count; $i++ )
		{
			// TEXT �����ä���硢���������ղ�
			if ( $objMaster->aryType[$i] == "text" || $objMaster->aryType[$i] == "bool" )
			{
				$aryValue[$i] = $objMaster->aryColumnName[$i] . " = '" . $aryData[$objMaster->aryColumnName[$i]] . "'";
			}
			else
			{
				$aryValue[$i] = $objMaster->aryColumnName[$i] . " = " . $aryData[$objMaster->aryColumnName[$i]];
			}
		}

		// �������ʥޥ����ξ�硢������2�Ĥ��뤿��ξ����ɲä���
		if ( $objMaster->strTableName == "m_StockItem" )
		{
			$where = " AND lngStockSubjectCode = " . $aryData["lngstocksubjectcode"];
		}

		// �оݥޥ�����å�
		$aryQuery[] = "SELECT * FROM " . $objMaster->strTableName . " WHERE " . $aryData["strKeyName"] . " = " . $aryData[$aryData["strKeyName"]] . $where . " FOR UPDATE";

		// �оݥޥ���UPDATE������
		$aryQuery[] = "UPDATE " . $objMaster->strTableName . " SET " . join ( ", ", $aryValue ) . " WHERE " . $aryData["strKeyName"] . " = " . $aryData[$aryData["strKeyName"]] . $where;
	}
}

// ����ξ�硢��������å��¹�
elseif ( $aryData["lngActionCode"] == DEF_ACTION_DELETE )
{
	$count = count ( $objMaster->aryCheckQuery["DELETE"] );
	for ( $i = 0; $i < $count; $i++ )
	{
		list ( $lngResultID, $lngResultNum ) = fncQuery ( $objMaster->aryCheckQuery["DELETE"][$i], $objDB );
		if ( $lngResultNum > 0 )
		{
			fncOutputError ( 1201, DEF_WARNING, "�ޥ�����������", TRUE, "", $objDB );
		}
	}

	// �������ʥޥ����ξ�硢������2�Ĥ��뤿��ξ����ɲä���
	if ( $objMaster->strTableName == "m_StockItem" )
	{
		$where = " AND lngStockSubjectCode = " . $aryData["lngstocksubjectcode"];
	}

	$aryQuery[] = "DELETE FROM " . $objMaster->strTableName . " WHERE " . $aryData["strKeyName"] . " = " . $aryData[$aryData["strKeyName"]] . $where;
}


////////////////////////////////////////////////////////////////////////////
// ������¹�
////////////////////////////////////////////////////////////////////////////
$objDB->transactionBegin();

$count = count ( $aryQuery );
for ( $i = 0; $i < $count; $i++ )
{
	list ( $lngResultID, $lngResultNum ) = fncQuery ( $aryQuery[$i], $objDB );
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
	//echo "<form name=form1><input type=hidden name=strSessionID value=" . $aryData["strSessionID"] . "></form>";
	echo "<script language=javascript>window.returnValue=true;window.open('about:blank','_parent').close();</script>";
}


$objDB->close();


return TRUE;
?>


