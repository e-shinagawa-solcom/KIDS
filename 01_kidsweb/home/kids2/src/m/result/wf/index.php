<?
/** 
*	�ޥ������� ����ե�����ޥ��� �ޥ������ơ��֥��̰�������
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID              -> index.php
// index.php -> lngWorkflowOrderCode      -> index.php
// index.php -> lngWorkflowOrderGroupCode -> index.php
// index.php -> lngInChargeCode           -> index.php
//
// ��Ͽ����
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
//
// ��������
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
// index.php -> lngmonetaryratecode   -> edit.php
// index.php -> lngmonetaryunitcode   -> edit.php

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");



$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData = $_POST;

// ʸ��������å�
$aryCheck["strSessionID"]              = "null:numenglish(32,32)";
$aryCheck["lngWorkflowOrderCode"]      = "number(0,2147483647)";
$aryCheck["lngWorkflowOrderGroupCode"] = "number(0,2147483647)";
$aryCheck["lngInChargeCode"]           = "number(0,2147483647)";

$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// �ޥ��������֥�����������
$objMaster = new clsMaster();
$objMaster->strTableName = "m_WorkflowOrder";

// ��������������
$strQuery = "SELECT * FROM m_WorkflowOrder o, m_User u, m_Group g";

// ����ե����֥����ɾ������
if ( $aryData["lngWorkflowOrderCode"] )
{
	$aryWhereString[] = " o.lngWorkflowOrderCode = " . $aryData["lngWorkflowOrderCode"];
}

// ����ե�������롼�ץ����ɾ������
if ( $aryData["lngWorkflowOrderGroupCode"] )
{
	$aryWhereString[] = " o.lngWorkflowOrderGroupCode = " . $aryData["lngWorkflowOrderGroupCode"];
}

// ô���ԥ����ɾ������
if ( $aryData["lngInChargeCode"] )
{
	$aryWhereString[] = " o.lngWorkflowOrderCode = ( SELECT o2.lngWorkflowOrderCode FROM m_WorkflowOrder o2 WHERE o2.lngInChargeCode = " . $aryData["lngInChargeCode"] . " AND o.lngWorkflowOrderCode = o2.lngWorkflowOrderCode )";
}

// ɽ���ե饰��TRUE�Τ�Τ���ɽ��
$aryWhereString[] = " o.bytWorkflowOrderDisplayFlag = TRUE";

// °���ơ��֥�Ȥη��
$aryWhereString[] = " o.lngInChargeCode = u.lngUserCode";
$aryWhereString[] = " o.lngWorkflowOrderGroupCode = g.lngGroupCode";

// ���ʬ����������������ɲ�
$strWhereString = join ( " AND", $aryWhereString );
$strQuery .= " WHERE " . $strWhereString . " ORDER BY o.lngWorkflowOrderCode, o.lngWorkflowOrderNo";


// �ǡ����μ����ȥ��֥������ȤؤΥ��å�
$lngResultNum = $objMaster->setMasterTableData( $strQuery, $objDB );

if ( $lngResultNum )
{
	///////////////////////////////////////////////////////////////////
	// �ơ��֥�����
	///////////////////////////////////////////////////////////////////
	// ��̹�ɽ��
	$count = 0;

	// bytGroupDisplayFlag ����
	$aryGroupDisplayFlag = Array ( "t" => "ɽ��", "f" => "��ɽ��" );


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

			 $aryParts["strResultHtml"] .= "	<tr id=\"TD" . $codeCount . "_" . $noCount . "\" class=\"Segs\" onclick=\"fncSelectSomeTrColor( this , 'TD" . $codeCount . "_' , _%count" . $record["lngworkflowordercode"] . "%_ );\" style=\"background:" . $record["strgroupdisplaycolor"] . ";\">\n";

			// ���������
			$aryParts["strResultHtml"] .= "<th nowrap rowspan=_%count" . $record["lngworkflowordercode"] . "%_>" . ( $codeCount + 1 ) . "</th>\n";
			$aryParts["strResultHtml"] .= "		<td nowrap rowspan=_%count" . $record["lngworkflowordercode"] . "%_>" . fncHTMLSpecialChars( $record["strworkflowordername"] ) . "</td>\n";
			$aryParts["strResultHtml"] .= "		<td nowrap rowspan=_%count" . $record["lngworkflowordercode"] . "%_>" . fncHTMLSpecialChars( $record["strgroupdisplayname"] ) . "</td>\n";
		}

		// ����ʳ��ξ�硢<tr>�Τߤ�ɽ��
		else
		{
			 $aryParts["strResultHtml"] .= "	<tr id=\"TD" . $codeCount . "_" . $noCount . "\" class=\"Segs\" onclick=\"fncSelectSomeTrColor( this , 'TD" . $codeCount . "_' , _%count" . $record["lngworkflowordercode"] . "%_ );\" style=\"background:" . $record["strgroupdisplaycolor"] . ";\">\n";
		}

		// ������ɽ��
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["lngworkfloworderno"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["struserdisplayname"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["lnglimitdays"] . "����</td>\n";

		// �ǽ�Υ���ե�����ֹ�ξ�硢����ܥ����ɽ��
		if ( $record["lngworkfloworderno"] == 1 )
		{
			// GET���Ϥ�ʸ��������
			$getUrl = "strSessionID=$aryData[strSessionID]&lngWorkflowOrderCode=" . $record["lngworkflowordercode"] . "&lngActionCode=" . DEF_ACTION_DELETE;

			// ����ܥ�������
			$aryParts["strResultHtml"] .= "		<th bgcolor=\"#ffffff\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\" nowrap rowspan=_%count" . $record["lngworkflowordercode"] . "%_><a href=\"javascript:fncShowDialogCommonMaster('/m/regist/wf/confirm.php?$getUrl' , window.form1 , 'ResultIframeCommonMaster' , 'NO' , $_COOKIE[lngLanguageCode] , 'delete' );\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DELETE\"></a></th>\n";
		}

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
$aryParts["strColumnHtml"] = $objMaster->getColumnHtmlTable( 5 );


$objDB->close();



$aryParts["HIDDEN"]           = "<input type=hidden name=strSessionID value=$aryData[strSessionID]>\n";
$aryParts["HIDDEN"]          .= "<input type=hidden name=lngWorkflowOrderCode value=$aryData[lngWorkflowOrderCode]>\n";
$aryParts["HIDDEN"]          .= "<input type=hidden name=lngWorkflowOrderGroupCode value=$aryData[lngWorkflowOrderGroupCode]>\n";
$aryParts["HIDDEN"]          .= "<input type=hidden name=lngInChargeCode value=$aryData[lngInChargeCode]>\n";
$aryParts["lngLanguageCode"] =& $_COOKIE["lngLanguageCode"];
$aryParts["strTableName"]    = $objMaster->strTableName;
$aryParts["lngColumnNum"]    = 5;
$aryParts["strEditURL"]      = "/m/regist/wf/edit.php?lngActionCode=" . DEF_ACTION_INSERT . "&strSessionID=" . $aryData["strSessionID"];

// HTML����
$objTemplate = new clsTemplate();
//echo getArrayTable( $aryParts, "TABLE" );exit;
$objTemplate->getTemplate( "m/result/parts.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;



return TRUE;
?>
