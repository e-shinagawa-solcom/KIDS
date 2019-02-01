<?php

// ----------------------------------------------------------------------------
/**
*       ����  ��ǧ�롼��
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       ��������
*         ������Ͽ����ӽ������̾�ξ�ǧ�롼�Ȱ���������
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");



$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

if ( $_POST )
{
	$aryData = $_POST;
}
else
{
	$aryData = $_GET;
}

// ʸ��������å�
$aryCheck["strSessionID"]              = "null:numenglish(32,32)";

$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// �ޥ��������֥�����������
$objMaster = new clsMaster();
$objMaster->strTableName = "m_WorkflowOrder";

$lngUserCode = $objAuth->UserCode;

// �桼������°���륰�롼�פ�ޤ����ե����
// ����(EXCEPT)
// �桼������°�������ե����� �ޤ��� �桼�����ʾ�θ��¤��ĥ桼������°�������ե����
// �ʾ�ξ��Υ���ե������ɤ�������륯��������
$aryQuery[] = "SELECT DISTINCT ON ( w.lngWorkflowOrderCode ) w.lngWorkflowOrderCode ";
$aryQuery[] = "FROM m_WorkflowOrder w, m_GroupRelation gr ";
$aryQuery[] = "WHERE gr.lngUserCode = $lngUserCode ";
$aryQuery[] = " AND w.lngWorkflowOrderGroupCode = gr.lngGroupCode ";
$aryQuery[] = "EXCEPT ";
$aryQuery[] = "SELECT DISTINCT ON ( w.lngWorkflowOrderCode ) w.lngWorkflowOrderCode ";
$aryQuery[] = "FROM m_WorkflowOrder w, m_User u, m_AuthorityGroup ag ";
$aryQuery[] = "WHERE w.lngInChargeCode = $lngUserCode ";
$aryQuery[] = " OR ag.lngAuthorityLevel > ";
$aryQuery[] = "(";
$aryQuery[] = "  SELECT ag2.lngAuthorityLevel";
$aryQuery[] = "  FROM m_User u2, m_AuthorityGroup ag2";
$aryQuery[] = "  WHERE u2.lngUserCode = $lngUserCode";
$aryQuery[] = "   AND u2.lngAuthorityGroupCode = ag2.lngAuthorityGroupCode";
$aryQuery[] = ")";
$aryQuery[] = " AND w.lngInChargeCode = u.lngUserCode";
$aryQuery[] = " AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode ";
$aryQuery[] = "GROUP BY w.lngworkflowordercode ";

list ( $lngResultID, $lngResultNum ) = fncQuery( join ( "", $aryQuery ), $objDB );

if ( $lngResultNum > 0 )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );
		$aryWhereQuery[] = "lngWorkflowOrderCode = " . $objResult->lngworkflowordercode;
	}

	unset ( $aryQuery );

	$aryQuery[] = "SELECT wo.lngWorkflowOrderCode, wo.strWorkflowOrderName, g.strGroupDisplayName, wo.lngWorkflowOrderNo, u3.strUserDisplayName, wo.lngLimitDays ";
	$aryQuery[] = "FROM m_WorkflowOrder wo, m_Group g, m_User u3 ";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "(";
	$aryQuery[] = join ( " OR ", $aryWhereQuery );
	$aryQuery[] = ")";
	$aryQuery[] = " AND wo.bytWorkflowOrderDisplayFlag = TRUE";
	$aryQuery[] = " AND wo.lngWorkflowOrderGroupCode = g.lngGroupCode";
	$aryQuery[] = " AND wo.lngInChargeCode = u3.lngUserCode ";
	$aryQuery[] = "ORDER BY wo.lngWorkflowOrderCode, wo.lngWorkflowOrderNo";

	unset ( $aryWhereQuery );

	$strQuery = join ( "", $aryQuery );

	// �ǡ����μ����ȥ��֥������ȤؤΥ��å�
	$lngResultNum = $objMaster->setMasterTableData( $strQuery, $objDB );
}

unset ( $aryQuery );

if ( $lngResultNum > 0 )
{
	///////////////////////////////////////////////////////////////////
	// �ơ��֥�����
	///////////////////////////////////////////////////////////////////
	// ��̹�ɽ��
	$count = 0;

	// lngWorkflowOrderCode ���������
	$codeCount = -1;

	// �쥳����ɽ������
	foreach ( $objMaster->aryData as $record )
	{
		// lngWorkflowOrderNo ��(�ޤȤ��Կ�)�����󥯥����
		$noCount++;

		// �ǽ�Υ���ե�����ֹ�ξ�硢����ե�̾�ȥ��롼��̾��ɽ��
		if ( $record["lngworkfloworderno"] == 1 )
		{
			// lngWorkflowOrderCode �������󥯥����
			$codeCount++;

			// lngWorkflowOrderNo ��(�ޤȤ��Կ�)�������
			$noCount = 0;

			 $aryParts["strResultHtml"] .= "	<tr id=\"TD" . $codeCount . "_" . $noCount . "\" class=\"Segs\">\n";
			 //$aryParts["strResultHtml"] .= "	<tr id=\"TD" . $codeCount . "_" . $noCount . "\" class=\"Segs\" onclick=\"fncSelectSomeTrColor( this , 'TD" . $codeCount . "_' , _%count" . $record["lngworkflowordercode"] . "%_ );\" style=\"background:" . $record["strgroupdisplaycolor"] . ";\">\n";

			// ���������
			$aryParts["strResultHtml"] .= "		<td nowrap rowspan=_%count" . $record["lngworkflowordercode"] . "%_>" . fncHTMLSpecialChars( $record["strworkflowordername"] ) . "</td>\n";
			$aryParts["strResultHtml"] .= "		<td nowrap rowspan=_%count" . $record["lngworkflowordercode"] . "%_>" . fncHTMLSpecialChars( $record["strgroupdisplayname"] ) . "</td>\n";
		}

		// ����ʳ��ξ�硢<tr>�Τߤ�ɽ��
		else
		{
			 $aryParts["strResultHtml"] .= "	<tr id=\"TD" . $codeCount . "_" . $noCount . "\" class=\"Segs\">\n";
			 //$aryParts["strResultHtml"] .= "	<tr id=\"TD" . $codeCount . "_" . $noCount . "\" class=\"Segs\" onclick=\"fncSelectSomeTrColor( this , 'TD" . $codeCount . "_' , _%count" . $record["lngworkflowordercode"] . "%_ );\" style=\"background:" . $record["strgroupdisplaycolor"] . ";\">\n";
		}

		// ������ɽ��
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["lngworkfloworderno"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["struserdisplayname"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["lnglimitdays"] . "����</td>\n";

		$aryParts["strResultHtml"] .= "	</tr>\n";
		$aryCount[$record["lngworkflowordercode"]] = $record["lngworkfloworderno"];
	}


	$aryKeys = array_keys ( $aryCount );

	// ROWSPAN ������
	foreach ( $aryKeys as $lngworkflowordercode )
	{
		$aryParts["strResultHtml"] = preg_replace ( "/_%count$lngworkflowordercode%_/", $aryCount[$lngworkflowordercode], $aryParts["strResultHtml"] );
	}

}
else
{
	$aryParts["strResultHtml"] = "<tr bgcolor=#ffffff><th colspan=" . ( count ( $objMaster->aryColumnName ) + 1 ) . ">���̵����</th></tr>";
}


// ������HTML����
for ( $i = 0; $i < 5; $i++ )
{
	$aryParts["strColumnHtml"] .= "		<td id=\"Column$i\" nowrap>Column$i</td>\n";
}



$objDB->close();



$aryParts["lngLanguageCode"] =& $_COOKIE["lngLanguageCode"];
$aryParts["strTableName"]    = $objMaster->strTableName;
$aryParts["lngColumnNum"]    = 5;

// HTML����
$objTemplate = new clsTemplate();
//echo getArrayTable( $aryParts, "TABLE" );exit;
$objTemplate->getTemplate( "/po/wf/parts.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;



return TRUE;
?>
