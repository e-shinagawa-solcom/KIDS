<?
/** 
*	�ޥ������� ����ե�����ޥ��� ��ǧ����
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// ������Ͽ
// edit.php -> strSessionID              -> confirm.php
// edit.php -> lngActionCode             -> confirm.php
// edit.php -> strWorkflowOrderName      -> confirm.php
// edit.php -> lngWorkflowOrderGroupCode -> confirm.php
// edit.php -> strOrderData              -> confirm.php
//
// ���
// index.php -> strSessionID         -> confirm.php
// index.php -> lngActionCode        -> confirm.php
// index.php -> lngWorkflowOrderCode -> confirm.php
//
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
//fncPutStringCheckError( $aryCheckResult, $objDB );



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

	// ����о�ɽ���Τ���Υǡ��������
	$strQuery = "SELECT * " .
                "FROM m_WorkflowOrder wo, m_User u " .
                "WHERE wo.lngWorkflowOrderCode = " . $aryData["lngWorkflowOrderCode"] .
                " AND wo.lngInChargeCode = u.lngUserCode " .
                "ORDER BY wo.lngWorkflowOrderNo";

	$objMaster = new clsMaster();
	$objMaster->setMasterTableData( $strQuery, $objDB );

	// ����ե�����̾����
	$aryData["strWorkflowOrderName"] = $objMaster->aryData[0]["strworkflowordername"];

	// ����ե����롼�ץ����ɼ���
	$aryData["lngWorkflowOrderGroupCode"] = $objMaster->aryData[0]["lngworkflowordergroupcode"];

	$count = count ( $objMaster->aryData );

	for ( $i = 0; $i < $count; $i++ )
	{
		$aryData["strOrderData"] .= $objMaster->aryData[$i]["lngusercode"] . "=" . $objMaster->aryData[$i]["lnglimitdays"] . "&";
		$aryUserName[$objMaster->aryData[$i]["lngusercode"]] = $objMaster->aryData[$i]["struserdisplayname"];
	}

	$objMaster = new clsMaster();

	// ������Ͽ�ǡ��� �� '&' ��ʬ��
	$aryOrderData = explode ( "&", $aryData["strOrderData"] );
	$lngOrderDataLength = count ( $aryOrderData ) - 1;
}

// ���顼����ɽ������
list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );


//////////////////////////////////////////////////////////////////////////
// ����
//////////////////////////////////////////////////////////////////////////
$aryParts["lngLanguageCode"] =& $_COOKIE["lngLanguageCode"];
$aryParts["lngActionCode"]   =& $aryData["lngActionCode"];
$aryParts["strTableName"]    =  "m_WorkflowOrder";
$aryParts["strKeyName"]      =  "lngWorkflowOrderCode";
$aryParts["lngKeyCode"]      =& $aryData["lngWorkflowOrderCode"];
$aryParts["strSessionID"]    =& $aryData["strSessionID"];


// lngWorkflowOrderGroupCode ��(CODE+NAME)����
$aryGroupCode = fncGetMasterValue( "m_Group", "lngGroupCode", "strGroupDisplayCode || ':' || strGroupDisplayName", "Array", "", $objDB );

list ( $lngUserCode, $lngLimitDays ) = explode ( "=", $aryOrderData[0] );

$aryParts["MASTER"]  = "				<tr><td id=\"Column0\" class=\"SegColumn\"></td><td class=\"Segs\" align=\"left\">" . fncHTMLSpecialChars( $aryData["strWorkflowOrderName"] ) . "</td></tr>";
$aryParts["MASTER"] .= "				<tr><td id=\"Column1\" class=\"SegColumn\"></td><td class=\"Segs\" align=\"left\">" . fncHTMLSpecialChars( $aryGroupCode[$aryData["lngWorkflowOrderGroupCode"]] ) . "</td></tr>\n";

$aryParts["HIDDEN"]  = "<input type=\"hidden\" name=\"strWorkflowOrderName\" value=\"" . fncHTMLSpecialChars( $aryData["strWorkflowOrderName"] ) . "\">\n";

$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"lngWorkflowOrderGroupCode\" value=\"" . $aryData["lngWorkflowOrderGroupCode"] . "\">\n";

for ( $i = 0; $i < $lngOrderDataLength; $i++ )
{
	list ( $lngUserCode, $lngLimitDays ) = explode ( "=", $aryOrderData[$i] );

	$aryParts["MASTER"] .= "				<tr><td class=\"SegColumn\">" . ( $i + 1 ) . "</td><td class=\"Segs\" align=\"left\">$aryUserName[$lngUserCode] : $lngLimitDays ����</td></tr>\n";
}

$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strOrderData\" value=\"" . $aryData["strOrderData"] . "\">\n";

$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"lngWorkflowOrderCode\" value=\"" . $aryData["lngWorkflowOrderCode"] . "\">\n";




if ( $bytErrorFlag )
{
	//echo fncGetReplacedHtml( "m/regist/edit.tmpl", $aryData, $objAuth );
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=euc-jp\">";
	echo "<form action=/m/regist/wf/edit.php method=GET>";
	echo getArrayTable( $aryData, "HIDDEN" );
	echo "</form>";
	echo "<script language=javascript>document.forms[0].submit();</script>";
}
else
{
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "m/regist/wf/confirm.tmpl" );
	$objTemplate->replace( $aryParts );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;
/*
	echo "<form><table border>";
	echo "<input type=hidden name=strSessionID value=" . $aryData["strSessionID"] . ">";
	echo "<input type=hidden name=lngActionCode value=" . $aryData["lngActionCode"] . ">";
	echo $aryParts["HIDDEN"];
	echo $aryParts["MASTER"];
	echo "</table><input type=button value=BACK onClick=\"document.forms[0].action='edit.php';document.forms[0].submit();\"><input type=button value=SUBMIT onClick=\"document.forms[0].action='action.php';document.forms[0].submit();\"></form>";
*/
}


$objDB->close();


return TRUE;
?>


