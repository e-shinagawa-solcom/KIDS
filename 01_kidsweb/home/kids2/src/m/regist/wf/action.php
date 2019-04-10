<?
/** 
*	�ޥ������� ����ե�����ޥ��� ��λ����
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// �¹�
// confirm.php -> strSessionID              -> action.php
// confirm.php -> lngActionCode             -> action.php
// confirm.php -> lngWorkflowOrderCode      -> action.php
// confirm.php -> strWorkflowOrderName      -> action.php
// confirm.php -> lngWorkflowOrderGroupCode -> action.php
// confirm.php -> strOrderData              -> action.php


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

if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
{
	$aryCheck["strWorkflowOrderName"]      = "null:length(0,100)";
	$aryCheck["lngWorkflowOrderGroupCode"] = "null:number(0,2147483647)";
	$aryCheck["strOrderData"]              = "null:ascii(1,100)";
}
elseif ( $aryData["lngActionCode"] == DEF_ACTION_DELETE )
{
	$aryCheck["lngWorkflowOrderCode"]      = "null:number(0,2147483647)";
}


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );



//////////////////////////////////////////////////////////////////////////
// ������ͭ����������å�
//////////////////////////////////////////////////////////////////////////
// ��Ͽ ���� ���顼���ʤ� ��硢
// ������Ͽ�����������å��¹�
if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT && !join ( $aryCheckResult ) )
{
	// ���롼�ץǡ�������
	$strQuery = "SELECT * " .
                "FROM m_GroupRelation gr, m_AuthorityGroup ag, m_User u " .
                "WHERE gr.lngGroupCode = " . $aryData["lngWorkflowOrderGroupCode"] .
                " AND u.lngUserCode = gr.lngUserCode" .
                " AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// ��̷�����ʤ���硢���顼
	if ( $lngResultNum < 1 )
	{
		fncOutputError ( 9056, DEF_WARNING, "�桼���������ޤ���", TRUE, "", $objDB );
	}

	// �桼���������ɤ򥭡��Ȥ���Ϣ������˥桼�������¥�٥�򥻥å�
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );
		$aryAuthorityLevel[$objResult->lngusercode] = $objResult->lngauthoritylevel;
		$aryUserName[$objResult->lngusercode] = $objResult->struserdisplayname;
	}

	$objDB->freeResult( $lngResultID );

	// �桼�����ν��֤�ͭ�����ɤ����Υ����å�
	// A.���롼�׽�°�����å�
	// B.��ʣ�����å�
	// C.�¤ӽ��ͭ����(���¥����å�)

	// ������Ͽ�ǡ��� �� '&' ��ʬ��
	$aryOrderData = explode ( "&", $aryData["strOrderData"] );
	$lngOrderDataLength = count ( $aryOrderData ) - 1;

	// '=' ��ʬ�򤷡��桼���������ɡ���������������˥��å�
	for ( $i = 0; $i < $lngOrderDataLength; $i++ )
	{
		$aryOrderSubData = explode ( "=", $aryOrderData[$i] );

		// A.���롼�׽�°�����å�
		// ���Ϥ��줿�桼���������ɤ����롼�פ�°���Ƥ��ʤ��ä���票�顼
		if ( $aryAuthorityLevel[$aryOrderSubData[0]] == "" )
		{
			fncOutputError ( 9056, DEF_WARNING, "����桼�����ϻ��ꥰ�롼�פ˴ޤޤ�Ƥ��ޤ���", TRUE, "", $objDB );
		}

		// B.��ʣ�����å�
		// �������������ͤ�Ʊ���桼���������ɤ�¸�ߤ�������ʣ���顼
		$count = count ( $aryUserCode );
		for ( $j = 0; $j < $count; $j++ )
		{
			if ( $aryUserCode[$j] == $aryOrderSubData[0] )
			{
				fncOutputError ( 9056, DEF_WARNING, "�桼��������ʣ���Ƥ��ޤ���", TRUE, "", $objDB );
			}
		}

		$aryUserCode[$i]  = $aryOrderSubData[0];
		$aryLimitDays[$i] = $aryOrderSubData[1];
	}

	// C.�¤ӽ��ͭ����(���¥����å�)
	$count = count ( $aryUserCode ) - 1;
	for ( $i = 0; $i < $count; $i++ )
	{
		if ( $aryAuthorityLevel[$aryUserCode[$i]] < $aryAuthorityLevel[$aryUserCode[$i + 1]] )
		{
			fncOutputError ( 9056, DEF_WARNING, "���¤��㤤�桼�������⤤�桼�����������˾�ǧ�ԤȤ�����Ͽ����Ƥ��ޤ���", TRUE, "", $objDB );
		}
	}

	// ��Ͽ����(INSERT)

	$lngWorkflowOrderCode = fncGetSequence( "m_WorkflowOrder.lngWorkflowOrderCode", $objDB );

	// ���֥����� �ǥե����1 ������
	$lngWorkflowStatusCode = 1;

	// ��Ͽ�ο�����INSERT����������
	for ( $i = 0; $i < $lngOrderDataLength; $i++ )
	{
		// �桼���������ɡ����¤�ʬ��
		list ( $lngUserCode, $lngLimitDays ) = explode ( "=", $aryOrderData[$i] );

		// �ǽ���ǧ�Ԥξ�硢���֥����ɤ�2������
		if ( $i == ( $lngOrderDataLength - 1 ) )
		{
			$lngWorkflowStatusCode = 2;
		}

		$aryQuery[] = "INSERT INTO m_WorkflowOrder VALUES ( $lngWorkflowOrderCode, " . ( $i + 1 ) . ", '" . $aryData["strWorkflowOrderName"] . "', $lngWorkflowStatusCode, $lngUserCode, $lngLimitDays, " . $aryData["lngWorkflowOrderGroupCode"] . ", TRUE )";
	}
}

// ��� ���� ���顼���ʤ� ��硢
// ��������å��¹�
elseif ( $aryData["lngActionCode"] == DEF_ACTION_DELETE && !join ( $aryCheckResult ) )
{
	$strQuery = "SELECT * " .
                "FROM t_Workflow t, m_Workflow w " .
                "WHERE w.lngWorkflowOrderCode = " . $aryData["lngWorkflowOrderCode"] .
                " AND t.lngWorkflowSubCode =" .
                "(" .
                "  SELECT MAX(t2.lngWorkflowSubCode)" .
                "  FROM t_Workflow t2, m_Workflow w2" .
                "  WHERE t.lngWorkflowCode = t2.lngWorkflowCode" .
                "   AND t2.lngWorkflowCode = w2.lngWorkflowCode" .
                ")" .
                " AND t.lngWorkflowStatusCode = 1" .
                " AND t.lngWorkflowCode = w.lngWorkflowCode";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// ��̤�1��Ǥ⤢�ä���硢����Բ�ǽ�Ȥ������顼����
	if ( $lngResultNum > 0 )
	{
		$objDB->freeResult( $lngResultID );
		fncOutputError ( 1201, DEF_WARNING, "�ޥ�����������", TRUE, "", $objDB );
	}

	// �������(bytWorkflowDisplayFlag �� FALSE)
	$aryQuery[] = "UPDATE m_WorkflowOrder SET bytWorkflowOrderDisplayFlag = FALSE WHERE lngWorkflowOrderCode = " . $aryData["lngWorkflowOrderCode"];
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


$objDB->close();



//////////////////////////////////////////////////////////////////////////
// ����
//////////////////////////////////////////////////////////////////////////
echo getArrayTable( $aryData, "HIDDEN" );
echo "<script language=javascript>window.returnValue=true;window.close();</script>";



return TRUE;
?>


